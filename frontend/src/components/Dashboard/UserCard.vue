<template>
  <div
    class="flex flex-col items-center bg-indigo-100 border border-gray-200 mt-4 w-full py-6 px-4 rounded-lg"
  >
      <div
        class="flex items-center justify-center h-20 w-20 bg-indigo-200 rounded-full"
      >
        {{ user.name[0].toUpperCase() }}
      </div>
    <div class="text-sm font-semibold mt-2">{{ user.name }}</div>
  </div>
</template>

<script setup>
const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
});
</script>