<template>
  <div class="flex h-screen antialiased text-gray-800">
    <div class="flex flex-row h-full w-full overflow-x-hidden">
      <div class="flex flex-col py-8 pl-6 pr-2 w-64 bg-white flex-shrink-0">
        <Header />

        <UserCard :user="user" />

        <UserSidebar :users="users" :user="user" @Chat="fetchMessages" />
      </div>
      <div v-if="messages !== null" class="flex flex-col flex-auto h-full p-6">
        <div
          class="flex flex-col flex-auto flex-shrink-0 rounded-2xl bg-gray-100 h-full p-4"
        >
          <div class="flex flex-col h-full overflow-x-auto mb-4">
            <div class="flex flex-col h-full">
              <div class="grid grid-cols-12 gap-y-2">
                <div
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
                    class="flex flex-row items-center "
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
              </div>
            </div>
          </div>
          <div
            class="flex flex-row items-center h-16 rounded-xl bg-white w-full px-4"
          >
            <div>
              <button
                class="flex items-center justify-center text-gray-400 hover:text-gray-600"
              >
                <svg
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                  ></path>
                </svg>
              </button>
            </div>
            <div class="flex-grow ml-4">
              <div class="relative w-full">
                <input
                  type="text"
                  class="flex w-full border rounded-xl focus:outline-none focus:border-indigo-300 pl-4 h-10"
                />
                <button
                  class="absolute flex items-center justify-center h-full w-12 right-0 top-0 text-gray-400 hover:text-gray-600"
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
            <div class="ml-4">
              <button
                class="flex items-center justify-center bg-indigo-500 hover:bg-indigo-600 rounded-xl text-white px-4 py-1 flex-shrink-0"
              >
                <span>Send</span>
                <span class="ml-2">
                  <svg
                    class="w-4 h-4 transform rotate-45 -mt-px"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                    ></path>
                  </svg>
                </span>
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
import { onBeforeMount, onMounted, ref } from "vue";
import Header from "../components/Dashboard/Header.vue";
import UserCard from "../components/Dashboard/UserCard.vue";
import UserSidebar from "../components/Dashboard/UserSidebar.vue";

import { http } from "../helper/base";
const users = ref([]);
const user = ref({});

const messages = ref(null);

const fetchMessages = (message) => {
  messages.value = [];
  messages.value = message;
  console.log(messages.value);
};

const fetchUser = async () => {
  const res = await http.get("/dashboard");
  user.value = res.data;
};

const fethcUsers = async () => {
  const res = await http.get("/users");
  users.value = res.data;
};

onBeforeMount(async () => {
  await fetchUser();
  await fethcUsers();
});
</script>