import { createRouter, createWebHistory } from 'vue-router'
import DashboardView from '../views/DashboardView.vue'
import { base_url } from "../helper/base";
import axios from 'axios';

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: DashboardView
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/LoginView.vue')
  },
  {
    path: '/verification',
    name: 'Verification',
    component: () => import('../views/VerifiyEmail.vue')
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('../views/RegisterView.vue')
  }
];


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})


router.beforeEach((to, from) => {
  if (to.name === "Login" || to.name === "Verification" || to.name === "Register") {
    return true;
  }
  if (!localStorage.getItem("token")) {
    return {
      name: "Login"
    }
  }


  checkToken();

});

const checkToken = () => {
  axios.get(`${base_url}/dashboard`, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`
    }
  }).then((res) => {

  }).catch((err) => {
    localStorage.removeItem("token");
    router.push({
      name: 'Login'
    })
  });
}

export default router
