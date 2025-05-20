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
    } 
    else if ((to.name === 'Login' || to.name === 'Register') && token) {
    // Logged in, trying to access login/register
    if (from.name && from.name !== 'Login' && from.name !== 'Register') {
      next(from.fullPath) // go back to previous page if available and safe
    } else {
      next('/dashboard') // fallback to dashboard to prevent infinite redirect
    }
  } 
    else {
      next()
    }
})

export default router
