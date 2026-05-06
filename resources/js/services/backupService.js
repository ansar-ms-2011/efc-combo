import axios from 'axios';

export const getBackups = async (page = 1) => {
    return axios.get('/api/backups', { params: { page } });
};

export const createBackup = async (scope = 'full_site', params = {}) => {
    return axios.post('/api/backups', { scope, ...params });
};

export const getBackupStatus = async (backupId) => {
    return axios.get(`/api/backups/${backupId}`);
};

export const downloadBackup = async (backupId) => {
    const response = await axios.get(`/api/backups/${backupId}/download`, {
        responseType: 'blob'
    });

    // Extract filename from Content-Disposition header
    const disposition = response.headers['content-disposition'];
    let filename = `backup-${backupId}`;

    if (disposition) {
        // Try to get filename from filename*=UTF-8'' first (RFC 5987)
        let matches = /filename\*=UTF-8''([^;]+)/i.exec(disposition);

        if (matches && matches[1]) {
            filename = decodeURIComponent(matches[1]);
            console.log('Extracted filename from UTF-8:', filename);
        } else {
            // Fallback to regular filename
            matches = /filename="?([^"]+)"?/i.exec(disposition);
            if (matches && matches[1]) {
                filename = matches[1];
                console.log('Extracted filename from regular:', filename);
            }
        }
    }

    // Create download link
    const url = window.URL.createObjectURL(response.data);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
};
