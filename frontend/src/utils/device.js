export function getDeviceUUID() {
    let uuid = localStorage.getItem('device_uuid');
    if (!uuid) {
        uuid = crypto.randomUUID ? crypto.randomUUID() : 'uuid-' + Math.random().toString(36).substring(2) + Date.now();
        localStorage.setItem('device_uuid', uuid);
    }
    return uuid;
}
