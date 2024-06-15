<template>
  <div class="flex h-screen antialiased text-gray-800">
    <div class="flex flex-row h-full w-full overflow-x-hidden">
      <div class="flex flex-col py-8 pl-6 pr-2 w-64 bg-white flex-shrink-0">
        <Header />

        <UserCard :user="user" />

        <UserSidebar
          :users="users"
          :user="user"
          @chat="fetchMessages"
        />
      </div>
      <div v-if="messages !== null" class="flex flex-col flex-auto h-full p-6">
        <div
          class="flex flex-col flex-auto flex-shrink-0 rounded-2xl bg-gray-100 h-full p-4"
        >
          <div
            class="flex flex-col h-full overflow-x-auto mb-4"
            ref="messageContainer"
          >
            <div class="flex flex-col h-full">
              <div class="grid grid-cols-12 gap-y-2">
                <div
                  v-if="messages.length > 0"
                  v-for="message in messages"
                  :key="message.id"
                  :class="{
                    'col-start-1': user.id !== message.sender_id,
                    'col-start-6': user.id == message.sender_id,
                    'col-end-8': user.id !== message.sender_id,
                    'col-end-13': user.id === message.sender_id,
                  }"
                  class="p-3 rounded-lg"
                >
                  <div
                    class="flex flex-row items-center"
                    :class="{
                      'justify-start': user.id == message.sender_id,
                      'flex-row-reverse': user.id == message.sender_id,
                    }"
                  >
                    <div
                      class="flex items-center justify-center h-10 w-10 rounded-full bg-indigo-500 flex-shrink-0"
                    >
                      {{ message.sender_id == user.id ? user.name[0] : "b" }}
                    </div>
                    <div
                      class="relative ml-3 mr-2 text-smpy-2 px-4 shadow rounded-xl p-6 text-center"
                      :class="{
                        'bg-blue-600 text-white': user.id == message.sender_id,
                      }"
                    >
                      <div>{{ message.message }}</div>
                    </div>
                  </div>
                </div>
                <div v-else>
                  <h1 class="text-1xl text-center mt-40 mx-auto w-60">
                    No Message...
                  </h1>
                </div>
              </div>
            </div>
          </div>

          <div
            class="flex flex-row items-center h-16 rounded-xl bg-white w-full px-4"
          >
            <div class="flex-grow ml-4">
              <div class="relative w-full">
                <input
                  type="text"
                  class="flex w-full border rounded-xl focus:outline-none focus:border-indigo-300 pl-4 h-10"
                  v-model="message"
                />
                <button
                  class="absolute flex items-center justify-center h-full w-12 right-0 top-0 text-gray-400 hover:text-gray-600"
                  @click="showEmojiPicker = !showEmojiPicker"
                >
                  <svg
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                  </svg>
                </button>
              </div>
            </div>
            <EmojiPicker
              :native="true"
              v-if="showEmojiPicker == true"
              @select="onSelectEmoji"
            />

            <div class="ml-4">
              <button
                class="flex items-center justify-center bg-indigo-500 hover:bg-indigo-600 rounded-xl text-white px-4 py-1 flex-shrink-0"
                @click="sendMessage"
              >
                <span>Send</span>
                <span class="ml-2"> </span>
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="flex flex-col flex-auto h-full p-6" v-else>
        <div
          class="flex flex-col flex-auto flex-shrink-0 rounded-2xl bg-gray-100 h-full p-4"
        >
          <div class="h-screen flex items-center justify-center">
            <h1 class="text-5xl">Welcome {{ user.name }}</h1>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeMount, onMounted, ref, watch } from "vue";
import Header from "../components/Dashboard/Header.vue";
import UserCard from "../components/Dashboard/UserCard.vue";
import UserSidebar from "../components/Dashboard/UserSidebar.vue";
import { useChatStore } from "../stores/chat";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import { http } from "../helper/base";
import axios from "axios";
import EmojiPicker from "vue3-emoji-picker";
import "vue3-emoji-picker/css";

window.Echo = new Echo({
  authEndpoint: "http://127.0.0.1:8000/broadcasting/auth",
  broadcaster: "reverb",
  key: "kg5wppgjso1uk6cv1dwb",
  wsHost: "127.0.0.1",
  wsPort: 8080,
  wssPort: 8080,
  forceTLS: ("http" ?? "https") === "https",
  enabledTransports: ["ws", "wss"],
  auth: {
    headers: {
      Authorization: "Bearer " + localStorage.getItem("token"),
      Accept: "application/json",
    },
  },
});

const chatStore = useChatStore();

const users = ref([]);
const user = ref({});

const messages = ref([]);

const message = ref("");

const messageContainer = ref(null);
const showEmojiPicker = ref(false);


const onSelectEmoji = (emoji) => {
  message.value += emoji.i;
};

watch(messages, () => {
  messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
});

const sendMessage = () => {
  http
    .post(`/send_message`, {
      sender_id: chatStore.senderId,
      receiver_id: chatStore.receiverId,
      message: message.value,
    })
    .then((res) => {
      message.value = "";
      messages.value.push(res.data);
      messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
      showEmojiPicker.value = false;
    });
};

const fetchMessages = (message) => {
  messages.value = [];
  messages.value = message;
};

const fetchUser = async () => {
  const res = await http.get("/dashboard");
  user.value = res.data;
  chatStore.$patch({
    senderId: user.value.id,
  });
};

const fethcUsers = async () => {
  const res = await http.get("/users");
  users.value = res.data;
};

onMounted(() => {
  window.Echo.private(`chat.${chatStore.senderId}`).listen(
    "MessageSent",
    (res) => {
      messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
      messages.value.push(res.message);
    }
  );
});

onBeforeMount(async () => {
  await fetchUser();
  await fethcUsers();
});
</script>