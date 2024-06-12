import axios from "axios";
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';


export const base_url = "http://127.0.0.1:8000/api";
export const http = axios.create({
    baseURL: base_url,
    headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
    }
});

export const ECHO = new Echo({
    broadcaster: 'reverb',
    key: "kg5wppgjso1uk6cv1dwb",
    wsHost: '127.0.0.1',
    wsPort: '8080',
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});
