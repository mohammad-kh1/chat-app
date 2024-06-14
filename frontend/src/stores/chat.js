import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useChatStore = defineStore('chat', () => {
    const senderId = ref(null);
    const receiverId = ref(null);
    return { senderId, receiverId }
})
