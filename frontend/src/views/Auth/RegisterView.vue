<template>
  <div class="mt-5 mx-10">
    <Error v-if="login_status === 0" :message="message" :durtaion="5000" />
    <Success v-if="login_status === 1" :message="message" :durtaion="5000" />
  </div>

  <div class="border mx-auto shadow rounded p-10 w-6/12 pt-16 my-20 bg-gray-50">
    <h1 class="text-3xl text-center">Chat App</h1>

    <form class="max-w-sm mx-auto" @submit.prevent="register">
      <div class="mb-5">
        <label
          for="email"
          class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
          >Fullname</label
        >
        <input
          type="text"
          id="name"
          name="name"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          required
          v-model="name"
        />
      </div>
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
          :class="{
            'bg-red-50 border border-red-500 text-red-900 placeholder-red-700 text-sm rounded-lg focus:ring-red-500 dark:bg-gray-700 focus:border-red-500 block w-full p-2.5 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500':
              canRegister === false,
          }"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          required
          v-model="email"
        />
        <span
          v-if="canRegister == false"
          class="mt-2 text-sm text-red-600 dark:text-red-500"
          >Email Already Exists</span
        >
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
          @input="checkPasswordLength"
        />
      </div>
      <div class="mb-5">
        <label
          for="password"
          class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
          >Password Confrim</label
        >
        <input
          type="password"
          id="password_confrimation"
          name="password_confrimation"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          required
          v-model="password_confrimation"
          @input="checkPasswordConfrim"
        />
                <span
          v-if="password_confirm_length == false"
          class="mt-2 text-sm text-red-600 dark:text-red-500"
          >Password must bigger than 8 character</span
        >
        <span
          v-if="password_confirm == false"
          class="mt-2 text-sm text-red-600 dark:text-red-500"
          >password and password confirm are not same</span
        >
      </div>
      <button
        @submit.prevent="register"
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
import { base_url } from "../../helper/base";
import Error from "../../components/Error.vue";
import Success from "../../components/Success.vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/auth";

const name = ref("");
const email = ref("");
const password = ref("");
const password_confrimation = ref("");
const login_status = ref(false);
const message = ref("");
const canRegister = ref(null);
const router = useRouter();
const password_confirm = ref(null);
const password_confirm_length = ref(null);

const auth = useAuthStore();

const checkPasswordConfrim = () => {
  if (password.value === password_confrimation.value) {
    password_confirm.value = true;
    return;
  } else {
    password_confirm.value = false;
    return;
  }
};
const checkPasswordLength = () => {
  if (password.value.length < 8) {
    password_confirm_length.value = false;
    return;
  } else {
    password_confirm_length.value = true;
    canRegister.value = true;
  }
};

const register = async () => {
  // Check email is exists
  axios
    .post(`${base_url}/check-email`, {
      email: email.value,
    })
    .then((res) => {
      canRegister.value = true;
    })
    .catch((err) => {
      canRegister.value = false;
    });
  if (canRegister.value) {
    await axios
      .post(`${base_url}/register`, {
        name: name.value,
        email: email.value,
        password: password.value,
        password_confirmation: password_confrimation.value,
      })
      .then((res) => {
        // if Register Success
        login_status.value = 1;
        message.value = "Successfully Registerd Pelase Verify Your Email";
        localStorage.setItem("access_token", res.data);
        auth.$patch({
          email: email.value,
        });
        router.push({
          name: "Verification",
        });
      })
      .catch((err) => {
        // If Has Error
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
  }
};
</script>