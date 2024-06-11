<template>
  <div class="mt-5 mx-10">
    <Error :message="message" :duration="3000" v-if="verify_status === 0" />
    <Success :message="message" :duration="3000" v-if="verify_status === 1" />
  </div>
  <v-card class="mx-auto mt-40 shadow-md border rounded p-10" width="400">
    <template v-slot:title>
      <span class="font-weight-black">Please Verifiy Your Email</span>
    </template>
    <formm action="">
      <v-otp-input
        focus-all
        variant
        :loading="loading"
        underlined
        v-model="otp"
      ></v-otp-input>
      <div class="p-5">
        <v-btn
          :disabled="loading"
          class="mb-4 text-white"
          size="x-large"
          block
          @click="verify"
        >
          Verify and continue
        </v-btn>
      </div>
    </formm>
  </v-card>
</template>

<script setup>
import { ref } from "vue";
import { useAuthStore } from "../../stores/auth";
import { base_url } from "../../helper/base";
import axios from "axios";
import Error from "../../components/Error.vue";
import Success from "../../components/Success.vue";
import { useRouter } from "vue-router";

const auth = useAuthStore();
const otp = ref("");
const router = useRouter();

const loading = ref(false);
const message = ref("");
const verify_status = ref(false);

const verify = async () => {
  loading.value = !loading.value;
  await axios
    .post(`${base_url}/verify-otp`, {
      email: auth.email,
      otp: otp.value,
    })
    .then((res) => {
      localStorage.setItem("token", res.data);
      loading.value = !loading.value;
      message.value = "Verification Successful";
      verify_status.value = 1;
      router.push({
        name: "Dashboard",
      });
      
    })
    .catch((err) => {
      loading.value = !loading.value;
      message.value = "Invalid Verification Code";
      verify_status.value = 0;
    });
};
</script>