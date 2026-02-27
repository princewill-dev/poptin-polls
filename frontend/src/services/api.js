import axios from 'axios';

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL + '/api',
    withCredentials: true, // Required for Sanctum CSRF cookies
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    }
});

export const csrf = () => axios.get(import.meta.env.VITE_API_URL + '/sanctum/csrf-cookie', {
    withCredentials: true
});

api.interceptors.request.use(config => {
    // 1. Core Token: Pull from localStorage for API Auth
    const token = localStorage.getItem('auth_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    // 2. Cookie Support: Axios strips X-XSRF-TOKEN on cross-origin requests.
    const match = document.cookie.match(new RegExp('(^|;\\s*)XSRF-TOKEN=([^;]*)'));
    if (match) {
        config.headers['X-XSRF-TOKEN'] = decodeURIComponent(match[2]);
    }
    return config;
});

api.interceptors.response.use(
    response => response,
    error => {
        return Promise.reject(error);
    }
);

export default api;
