<script setup>
import { ref } from 'vue'
import api from '@/axios'
import { useRouter } from 'vue-router'

const router = useRouter()

const email = ref('')
const password = ref('')

const emailFail = ref(false)
const passFail = ref(false)
const authFail = ref(false)
const processing = ref(false);

const checkForm = async (e) => {
    e.preventDefault()
    processing.value = true;
    // Reset flags
    emailFail.value = false
    passFail.value = false
    authFail.value = false

    let isValid = true

    // Email validation
    if (!email.value || !/\S+@\S+\.\S+/.test(email.value)) {
        emailFail.value = true
        isValid = false
    }

    // Password validation
    if (!password.value) {
        passFail.value = true
        isValid = false
    }

    if (!isValid) {
        processing.value = false
        return
    }

    try {
        const response = await api.post('/login', {
            email: email.value,
            password: password.value
        });
        processing.value = false
        const token = response.data.access_token
        localStorage.setItem('auth_token', token);
        router.push('dashboard')
        // console.log('Login successful:', token);
    } catch (error) {
        console.error('Login error:', error.response?.data || error.message)
        authFail.value = true;
        processing.value = false
    }
}

</script>

<template>
    <div class="bg-gray-200 font-sans text-gray-700">
        <div class="container mx-auto p-8 flex">
            <div class="max-w-md w-full mx-auto">
                <h1 class="text-4xl text-center mb-12 font-thin">Welcome ToDo App</h1>

                <div class="bg-white rounded-lg overflow-hidden shadow-2xl">
                    <div class="p-8">
                        <form method="POST" class="" action="#" @submit="checkForm">
                            <small class="text-red-600" v-if="authFail">Incorrect email or password.</small>
                            <div class="mb-5">
                                <label for="email" class="block mb-2 text-sm font-medium text-gray-600">Email</label>

                                <input v-model="email" type="email" name="email"
                                    class="block w-full p-3 rounded bg-gray-200 border border-transparent focus:outline-none">
                                <small class="text-red-600" v-if="emailFail">Please insert a correct email
                                    address.</small>
                            </div>

                            <div class="mb-5">
                                <label for="password"
                                    class="block mb-2 text-sm font-medium text-gray-600">Password</label>

                                <input v-model="password" type="password" name="password"
                                    class="block w-full p-3 rounded bg-gray-200 border border-transparent focus:outline-none">
                                <small class="text-red-600" v-if="passFail">Please type your password.</small>
                            </div>

                            <button v-if="!processing"
                                class="w-full p-3 mt-4 bg-indigo-600 text-white rounded shadow cursor-pointer">Login</button>
                            <button v-if="processing" type="button"
                                class="w-full p-3 mt-4 bg-indigo-600 text-white rounded shadow cursor-pointer" disabled>
                                <div class="flex flex-row items-center space-x-5 justify-center">
                                    <div class="loader rounded-full flex space-x-3">
                                        <div class="w-3 h-3 bg-gray-800 rounded-full animate-bounce"></div>
                                        <div class="w-3 h-3 bg-gray-800 rounded-full animate-bounce"></div>
                                        <div class="w-3 h-3 bg-gray-800 rounded-full animate-bounce"></div>
                                    </div>
                                    <p>Processing…</p>
                                </div>
                            </button>
                        </form>
                    </div>

                    <div class="flex justify-between p-8 text-sm border-t border-gray-300 bg-gray-100">
                        <RouterLink to="register">
                            <a href="#" class="font-medium text-indigo-500">Create account</a>
                        </RouterLink>
                        <!-- <a href="#" class="text-gray-600">Forgot password?</a> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>