<template>
  <div class="flex flex-col mt-8">
    <div class="flex flex-row items-center justify-between text-xs">
      <span class="font-bold">Online Users</span
      ><span
        class="flex items-center justify-center bg-gray-300 h-4 w-4 rounded-full"
      >
        {{ online_users.length }}
      </span>
    </div>
    <div class="flex flex-col space-y-1 mt-4 -mx-2 h-auto overflow-y-auto">
      <button
        v-for="user in online_users"
        :id="user.id"
        class="flex flex-row items-center hover:bg-gray-100 rounded-xl p-2"
        :class="{ 'bg-gray-200': user.id === selected }"
        @click="Chat(user.id)"
      >
        <div
          class="flex items-center justify-center h-8 w-8 bg-indigo-200 rounded-full"
        >
          {{ user.name[0].toUpperCase() }}
        </div>
        <div class="ml-2 text-sm font-semibold">{{ user.name }}</div>
      </button>
    </div>
  </div>
  <div class="flex flex-col mt-8">
    <div class="flex flex-row items-center justify-between text-xs">
      <span class="font-bold">All Users</span
      ><span
        class="flex items-center justify-center bg-gray-300 h-4 w-4 rounded-full"
        >{{ users.length }}</span
      >
    </div>
    <div class="flex flex-col space-y-1 mt-4 -mx-2 h-auto overflow-y-auto">
      <button
        v-for="user in users"
        :key="user.id"
        :id="user.id"
        class="flex flex-row items-center hover:bg-gray-100 rounded-xl p-2"
        :class="{ 'bg-gray-200': user.id === selected }"
        @click="Chat(user.id)"
      >
        <div
          class="flex items-center justify-center h-8 w-8 bg-indigo-200 rounded-full"
        >
          {{ user.name[0].toUpperCase() }}
        </div>
        <div class="ml-2 text-sm font-semibold">{{ user.name }}</div>
      </button>
    </div>
  </div>
</template>


<script setup>
import { onMounted, ref } from "vue";
import { http } from "../../helper/base";
import { useChatStore } from "../../stores/chat";

const chatStore = useChatStore();

const emits = defineEmits(["chat"]);

const messages = ref([]);
const button = ref(null);

const selected = ref(null);

const chat = () => {
  emits("chat", messages.value);

  messages.value = [];
};

const props = defineProps({
  users: {
    type: Array,
    default: () => [],
    required: true,
  },
  user: {
    type: Object,
    required: true,
  },
});
const online_users = ref([]);

onMounted(() => {
  window.Echo.join(`online_users`)
    .here((users) => {
      users.forEach((user) => {
        if (user.id != chatStore.senderId) {
          online_users.value .push(user);
        }
      });
    })
    .leaving((user) => {
      const index = online_users.value.findIndex((u) => u.id === user.id);
      if (index !== -1) {
        online_users.value.splice(index, 1);
      }
    })
    .joining((user) => {
      online_users.value.push(user);
    });
});

const Chat = async (user_id) => {
  selected.value = user_id;
  chatStore.$patch({
    receiverId: user_id,
  });
  const res = await http.post(`/chat`, {
    sender_id: props.user.id,
    receiver_id: user_id,
  });

  messages.value = res.data;
  chat();
};
</script>