import { reactive } from 'vue';
import { useAppStore } from '@/stores';

// IndexedDB Manager
class IndexedDBManager {
    constructor() {
        this.dbName = 'ApplicationFormDB';
        this.dbVersion = 1;
        this.db = null;
        this.storeName = 'draftsApplications';
        this.initDB().then(() => console.log('IndexedDB initialized'));
    }

    async initDB() {
        return new Promise((resolve, reject) => {
            if (this.db && this.db.name === this.dbName) {
                resolve(this.db);
                return;
            }

            const request = indexedDB.open(this.dbName, this.dbVersion);

            request.onerror = () => {
                console.error('IndexedDB error:', request.error);
                reject(request.error);
            };

            request.onsuccess = () => {
                this.db = request.result;
                resolve(this.db);
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;

                // Create object store for form drafts if it doesn't exist
                if (!db.objectStoreNames.contains(this.storeName)) {
                    const draftStore = db.createObjectStore(this.storeName, {
                        keyPath: 'id'  // This expects an 'id' property at the root
                    });

                    // Create indexes for faster queries
                    draftStore.createIndex('createdAt', 'createdAt', { unique: false });
                    draftStore.createIndex('updatedAt', 'updatedAt', { unique: false });
                    draftStore.createIndex('step', 'step', { unique: false });

                    console.log('IndexedDB store created');
                }
            };
        });
    }

    async saveDraft(draftData) {
        await this.initDB();

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([this.storeName], 'readwrite');
            const store = transaction.objectStore(this.storeName);

            // Ensure the data has an 'id' property at the root level
            if (!draftData.id) {
                reject(new Error('Draft data must have an id property'));
                return;
            }

            const request = store.put(draftData);

            request.onsuccess = () => {
                console.log('Draft saved to IndexedDB:', draftData.id);
                resolve(draftData.id);
            };

            request.onerror = () => {
                console.error('Error saving draft to IndexedDB:', request.error);
                reject(request.error);
            };
        });
    }

    async loadDraft(draftId) {
        await this.initDB();

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([this.storeName], 'readonly');
            const store = transaction.objectStore(this.storeName);
            const request = store.get(draftId);

            request.onsuccess = () => {
                resolve(request.result || null);
            };

            request.onerror = () => {
                console.error('Error loading draft from IndexedDB:', request.error);
                reject(request.error);
            };
        });
    }

    async deleteDraft(draftId) {
        await this.initDB();

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([this.storeName], 'readwrite');
            const store = transaction.objectStore(this.storeName);
            const request = store.delete(draftId);

            request.onsuccess = () => {
                console.log('Draft deleted from IndexedDB:', draftId);
                resolve();
            };

            request.onerror = () => {
                console.error('Error deleting draft from IndexedDB:', request.error);
                reject(request.error);
            };
        });
    }

    async getAllDrafts() {
        await this.initDB();

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([this.storeName], 'readonly');
            const store = transaction.objectStore(this.storeName);
            const request = store.getAll();

            request.onsuccess = () => {
                const drafts = request.result;
                // Return metadata only
                const draftsMetadata = drafts.map(draft => ({
                    id: draft.id,
                    createdAt: draft.createdAt,
                    updatedAt: draft.updatedAt,
                    isComplete: draft.isComplete,
                    step: draft.step,

                    missalNo: draft.application.missal_no,
                    applicationType: draft.application.application_type_id,
                    certificateType: draft.application.certificate_type,
                    applicantName: draft.applicant?.full_name || 'Unnamed',
                    identityNumber: draft.applicant?.identity_number,
                    identityType: draft.applicant?.identity_type,

                }));
                resolve(draftsMetadata);
            };

            request.onerror = () => {
                console.error('Error getting all drafts:', request.error);
                reject(request.error);
            };
        });
    }

    async clearAllDrafts() {
        await this.initDB();

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([this.storeName], 'readwrite');
            const store = transaction.objectStore(this.storeName);
            const request = store.clear();

            request.onsuccess = () => {
                console.log('All drafts cleared from IndexedDB');
                resolve();
            };

            request.onerror = () => {
                console.error('Error clearing drafts:', request.error);
                reject(request.error);
            };
        });
    }

    async getDraftsCount() {
        await this.initDB();

        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction([this.storeName], 'readonly');
            const store = transaction.objectStore(this.storeName);
            const request = store.count();

            request.onsuccess = () => {
                resolve(request.result);
            };

            request.onerror = () => {
                reject(request.error);
            };
        });
    }
}

export function useApplicationForm() {
    const store = useAppStore();
    const dbManager = new IndexedDBManager();

    const generateDraftId = () => {
        const timestamp = Date.now();
        const randomStr = Math.random().toString(36).substring(2, 10);
        const userId = store.user?.id || 'anonymous';
        return `draft_${userId}_${timestamp}_${randomStr}`;
    };

    const form = reactive({
        draft: {
            id: generateDraftId(),
            createdAt: new Date().toISOString(),
            updatedAt: new Date().toISOString(),
            version: 1,
            step: 1,
            isComplete: false,
            lastSavedAt: new Date().toISOString()
        },
        applicant: {
            id: '',
            uuid: '',
            full_name: '',
            identity_number: '',
            identity_type: '',
            dob: '',
            pob: '',
            identity_symbol: '',
            father_name: '',
            father_identity_number: '',
            email: '',
            phone: '',
            occupation: '',
            wife_husband_name: '',
            guardian_type_id: '',
            state_subject_class: '',
            residence_place: '',
            address: '',
            address2: '',
            address3: '',
            address4: '',
            region_id: '',
            district_id: '',
            tehsil_id: '',
            religion_id: '',
            gender_id: '',
            marital_status_id: '',
            location: '',
            personal_image: '',
            status: 'active',
            created_by: '',
            updated_by: '',
            deleted_by: '',
            children: [],
            refugee_details: {
                id: '',
                applicant_id: '',
                refugee_number: '',
                refugee_from: '',
                refugee_year: ''
            }
        },
        application: {
            id: '',
            uuid: '',
            certificate_type: 'state',
            applicant_id: '',
            current_status: '',
            application_type_id: 1,
            application_for_id: '',
            missal_no: '',
            entry_datetime: new Date().toISOString().split('T')[0],
            remarks: '',
            amount: '',
            personal_image: '',
            personal_image_file: null,
            on_desk: '',
            guardian_type_id: '',
            region_id: '',
            district_id: store.user?.district_id,
            tehsil_id: store.user?.tehsil_id,
            center_id: store.user?.center_id,
            created_by: store.user?.id,
            updated_by: '',
            deleted_by: '',
            duplicate_details: {
                id: '',
                application_id: '',
                reason_type_id: '',
                reason: '',
                guardian_type_id: ''
            },
            appointment: {
                id: '',
                application_id: '',
                qmatic_token: '',
                appointment_date: '',
                appointment_time: '',
                delivery_date: ''
            },
            delivery_details: {
                id: '',
                user_id: '',
                application_id: '',
                delivery_mode: 'self',
                delivery_address: '',
                delivery_phone: ''
            },
            biometrics: {
                thumb: {
                    id: null,
                    applicant_id: null,
                    application_id: null,
                    finger_type: 'thumb',
                    image_file: null,
                    image_path: null,
                    feature_set: null
                },
                index: {
                    id: null,
                    applicant_id: null,
                    application_id: null,
                    finger_type: 'index',
                    image_file: null,
                    image_path: null,
                    feature_set: null
                },
                middle: {
                    id: null,
                    applicant_id: null,
                    application_id: null,
                    finger_type: 'middle',
                    image_file: null,
                    image_path: null,
                    feature_set: null
                },
                ring: {
                    id: null,
                    applicant_id: null,
                    application_id: null,
                    finger_type: 'ring',
                    image_file: null,
                    image_path: null,
                    feature_set: null
                },
                little: {
                    id: null,
                    applicant_id: null,
                    application_id: null,
                    finger_type: 'little',
                    image_file: null,
                    image_path: null,
                    feature_set: null
                }
            },
            documents: []
        },
        headerModel: {
            mode: 'create',
            certificateType: 'state',
            identificationType: 'local',
            identificationNumber: null,
            applicationType: null,
            applicantDetails: null
        }
    });

    const prepareDataForStorage = (data) => {
        // Create a deep copy of the form data
        const copy = JSON.parse(JSON.stringify(data));

        // Add root-level properties required for IndexedDB
        return {
            id: copy.draft.id,
            createdAt: copy.draft.createdAt,
            updatedAt: copy.draft.updatedAt,
            step: copy.draft.step,
            isComplete: copy.draft.isComplete,
            // Keep the original structure
            ...copy
        };
    };

    const restoreDataAfterLoad = (data) => {
        if (!data) return null;

        const { id, createdAt, updatedAt, step, isComplete, ...originalData } = data;
        return originalData;
    };

    const saveToIndexedDB = async (data) => {
        try {
            data.draft.updatedAt = new Date().toISOString();
            data.draft.lastSavedAt = new Date().toISOString();

            const dataToSave = prepareDataForStorage(data);

            await dbManager.saveDraft(dataToSave);

            localStorage.setItem('current_draft_id', data.draft.id);

            console.log('Draft saved to IndexedDB:', data.draft.id);
            return true;
        } catch (error) {
            console.error('Error saving draft to IndexedDB:', error);
            return false;
        }
    };

    const loadFromIndexedDB = async (draftId = null) => {
        try {
            const id = draftId || localStorage.getItem('current_draft_id');
            if (!id) return null;

            const savedData = await dbManager.loadDraft(id);

            if (savedData) {
                // Restore the data (removes root-level properties)
                const restoredData = restoreDataAfterLoad(savedData);

                // Clear existing form properties and assign new values
                Object.keys(form).forEach(key => {
                    delete form[key];
                });

                Object.assign(form, restoredData);

                console.log('Draft loaded from IndexedDB:', id);
                return form;
            }
            return null;
        } catch (error) {
            console.error('Error loading draft from IndexedDB:', error);
            return null;
        }
    };

    const clearDraft = async (draftId) => {
        try {
            await dbManager.deleteDraft(draftId);

            // Clear current draft ID reference if it's this one
            if (localStorage.getItem('current_draft_id') === draftId) {
                localStorage.removeItem('current_draft_id');
            }

            console.log('Draft cleared from IndexedDB:', draftId);
            return true;
        } catch (error) {
            console.error('Error clearing draft:', error);
            return false;
        }
    };

    // Update draft with current step
    const updateDraft = async (step, data = null) => {
        form.draft.step = step;
        if (data) {
            Object.assign(form, data);
        }
        form.draft.updatedAt = new Date().toISOString();
        return await saveToIndexedDB();
    };

    const getAllDrafts = async () => {
        try {
            return await dbManager.getAllDrafts();
        } catch (error) {
            console.error('Error getting all drafts:', error);
            return [];
        }
    };

    const cleanupOldDrafts = async (keepCount = 10) => {
        try {
            const allDrafts = await dbManager.getAllDrafts();
            if (allDrafts.length > keepCount) {
                // Sort by updatedAt descending
                const sortedDrafts = allDrafts.sort((a, b) =>
                    new Date(b.updatedAt) - new Date(a.updatedAt)
                );

                // Delete drafts beyond keepCount
                const draftsToDelete = sortedDrafts.slice(keepCount);
                for (const draft of draftsToDelete) {
                    await dbManager.deleteDraft(draft.id);
                }

                console.log(`Cleaned up ${draftsToDelete.length} old drafts`);
            }
        } catch (error) {
            console.error('Error cleaning up old drafts:', error);
        }
    };

    return {
        form,
        saveToIndexedDB,
        loadFromIndexedDB,
        clearDraft,
        updateDraft,
        getAllDrafts,
        generateDraftId,
        cleanupOldDrafts,
        getDraftsCount: dbManager.getDraftsCount.bind(dbManager)
    };
}
