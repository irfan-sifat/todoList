import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/components/login.vue'
import Register from '@/components/register.vue'
import Dashboard from '@/components/dashboard.vue'

const routes = [
  { path: '/', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
  { path: '/dashboard', name: 'Dashboard', component: Dashboard, meta: { requiresAuth: true } }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('auth_token')
    if (to.meta.requiresAuth && !token) {
      next('/')
    } else {
      next()
    }
})

export default router
