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
          :onlineUsers="onlineUsers"
        />
      </div>
      <div v-if="messages !== null" class="flex flex-col flex-auto h-full p-6">
        <div
          class="flex flex-col flex-auto flex-shrink-0 rounded-2xl bg-gray-100 h-full p-4"
        >
          <div class="flex flex-col h-full overflow-x-auto mb-4">
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

          <MessageForm />
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
import MessageForm from "../components/Dashboard/MessageForm.vue";

import { http } from "../helper/base";
import { ECHO } from "../helper/base";

const users = ref([]);
const user = ref({});

const messages = ref(null);

const online_users_id = ref([]);
const onlineUsers = ref(new Set());


const fetchMessages = (message) => {
  messages.value = [];
  messages.value = message;
};

const fetchUser = async () => {
  const res = await http.get("/dashboard");
  user.value = res.data;
};

const fethcUsers = async () => {
  const res = await http.get("/users");
  users.value = res.data;
};

onMounted(() => {
  ECHO.channel("online_users").listen("OnlineUsers", async () => {
    const res = await http.get("/online_users");
    if (online_users_id.value.join() != res.data.join()) {
      online_users_id.value = res.data;
    }
  });
});

watch(online_users_id, () => {
  online_users_id.value.forEach((onlineUserId) => {
    users.value.forEach((user) => {
      if (user.id == onlineUserId) {
        try {
          onlineUsers.value.add(user);
        } catch (e) {}
      }
    });
  });
});

onBeforeMount(async () => {
  await fetchUser();
  await fethcUsers();
});
</script>