<template>
  <div class="flex flex-col mt-8">
    <div class="flex flex-row items-center justify-between text-xs">
      <span class="font-bold">Users</span
      ><span
        class="flex items-center justify-center bg-gray-300 h-4 w-4 rounded-full"
        >{{ users.length }}</span
      >
    </div>
    <div class="flex flex-col space-y-1 mt-4 -mx-2 h-auto overflow-y-auto">
      <button v-for="user in users" :key="user.id"
        class="flex flex-row items-center hover:bg-gray-100 rounded-xl p-2"
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
const props = defineProps({
  users: {
    type: Array,
    default: () => [],
    required: true,
  },
});
</script>