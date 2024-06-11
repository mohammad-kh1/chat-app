import axios from "axios";

export const base_url = "http://127.0.0.1:8000/api";
export const http = axios.create({
    baseURL: base_url,
    headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
    }
});