<template>
  <div class="mt-5 mx-10">
    <Error v-if="login_status === 0" :message="message" :durtaion="5000" />
    <Success v-if="login_status === 1" :message="message" :durtaion="5000" />
  </div>

  <div class="border mx-auto shadow rounded p-10 w-6/12 pt-16 my-20 bg-gray-50">
    <h1 class="text-3xl text-center">Chat App</h1>

    <form class="max-w-sm mx-auto" @submit.prevent="login">
      <div class="mb-5">
        <label
          for="email"
          class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
          >Your email</label
        >
        <input
          type="email"
          id="email"
          name="email"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          required
          v-model="email"
        />
      </div>
      <div class="mb-5">
        <label
          for="password"
          class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
          >Your password</label
        >
        <input
          type="password"
          id="password"
          name="password"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          required
          v-model="password"
        />
      </div>
      <button
        @submit.prevent="login"
        type="submit"
        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
      >
        Login
      </button>
    </form>
  </div>
</template>


<script setup>
import { ref } from "vue";
import { base_url } from "../helper/base";
import Error from "../components/Error.vue";
import Success from "../components/Success.vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

const email = ref("");
const password = ref("");
const login_status = ref(false);
const message = ref("");

const router = useRouter();

const auth = useAuthStore();

const login = async () => {
  await axios
    .post(`${base_url}/login`, {
      email: email.value,
      password: password.value,
    })
    .then((res) => {
      // if Login Success
      login_status.value = 1;
      message.value = "Successfully Logged In";
      localStorage.setItem("token", res.data);
      router.push({
        name: "Dashboard",
      });
    })
    .catch((err) => {
      // If Wrong Credentials
      if (err.response.data.message == "Invalid Credentials") {
        login_status.value = 0;
        message.value = err.response.data.message;
      }
      if (err.response.data.message == "Please verify your email first") {
        login_status.value = 0;
        message.value = err.response.data.message;
        auth.$patch({
          email: email.value,
        });
        router.push({
          name: "Verification",
        });
      }
    });
};
</script>