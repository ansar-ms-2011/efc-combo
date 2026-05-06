import localforage from 'localforage';
import { toRaw } from 'vue';
import { toast } from 'vue3-toastify';
import api from '@/services/axios';

const QUEUE_KEY = 'offline_data';

// Helper: Convert File / Blob to Base64
const fileToBase64 = (file: File | Blob): Promise<string> => {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result as string);
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
};

// Recursively sanitize object for IndexedDB
const sanitizeForStorage = async (obj: any): Promise<any> => {
    if (obj instanceof File || obj instanceof Blob) {
        return await fileToBase64(obj);
    } else if (Array.isArray(obj)) {
        return Promise.all(obj.map(sanitizeForStorage));
    } else if (obj !== null && typeof obj === 'object') {
        const res: any = {};
        for (const key in obj) {
            res[key] = await sanitizeForStorage(obj[key]);
        }
        return res;
    } else {
        return obj;
    }
};

// Add item to queue
export const addToQueue = async ({ syncRoute, payload, resourceLabel, resourceKey }: {
    syncRoute: string,
    payload: any,
    resourceLabel: string,
    resourceKey: string,
}) => {
    const sanitized = await sanitizeForStorage(toRaw(payload));
    const queue = (await localforage.getItem<any[]>(QUEUE_KEY)) || [];
    queue.push({
        id: Date.now(),
        resource_label: resourceLabel || '',
        resource_key: resourceKey || '',
        route: syncRoute,
        payload: sanitized,
        created_at: new Date().toISOString()
    });
    await localforage.setItem(QUEUE_KEY, queue);
};

// Get full queue
export const getQueue = async () => {
    return (await localforage.getItem<any[]>(QUEUE_KEY)) || [];
};

// Remove item from queue
export const removeFromQueue = async (id: number) => {
    const queue = await getQueue();
    const updated = queue.filter(item => item.id !== id);
    await localforage.setItem(QUEUE_KEY, updated);
};

// Sync queue to server
export const syncQueue = async () => {
    const queue = await getQueue();

    for (const item of queue) {
        try {
            await api.post(item.route, item.payload);
            await removeFromQueue(item.id);
            console.log(`Synced offline record ${item.id}`);
            toast.success(`Item ID: ${item.id} synced with server`);
        } catch (error: any) {
            // Stop syncing if offline or session expired
            if (!error.response) {
                console.warn('Still offline. Sync will retry later.');
                break;
            }

            if (error.response.status === 401) {
                console.warn('Session expired. Stop syncing.');
                break;
            }

            console.error('Failed to sync item:', error);
        }
    }
};

// Automatically sync when back online
window.addEventListener('online', () => {
    console.log('Back online. Syncing offline queue...');
    syncQueue();
});

// import localforage from 'localforage'
// import api from '@/services/axios'
//
// const QUEUE_KEY = 'offline_applications'
//
// // Save application to queue
// export const addToQueue = async (payload: any) => {
//     const queue = (await localforage.getItem<any[]>(QUEUE_KEY)) || []
//     queue.push({
//         id: Date.now(),
//         payload,
//         created_at: new Date().toISOString(),
//     })
//     await localforage.setItem(QUEUE_KEY, queue)
// }
//
// // Get queue
// export const getQueue = async () => {
//     return (await localforage.getItem<any[]>(QUEUE_KEY)) || []
// }
//
// // Remove item from queue
// export const removeFromQueue = async (id: number) => {
//     const queue = await getQueue()
//     const updated = queue.filter(item => item.id !== id)
//     await localforage.setItem(QUEUE_KEY, updated)
// }
//
// // Sync function
// export const syncQueue = async () => {
//     const queue = await getQueue()
//
//     for (const item of queue) {
//         try {
//             await api.post('/api/applications', item.payload)
//             await removeFromQueue(item.id)
//         } catch (error) {
//             console.log('Still offline or failed sync')
//             break // stop syncing if one fails
//         }
//     }
// }
