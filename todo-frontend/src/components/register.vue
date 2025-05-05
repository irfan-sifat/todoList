<script setup>
import { ref } from 'vue'
import api from '@/axios'
import { useRouter } from 'vue-router';

const router = useRouter()

const fullName = ref('');
const email = ref('')
const password = ref('')

const fullNameFail = ref(false)
const emailFail = ref(false)
const passFail = ref(false)
const regFail = ref(false)
const processing = ref(false);

const checkForm = async (e) => {
    e.preventDefault()
    processing.value = true;

    fullNameFail.value = false
    emailFail.value = false
    passFail.value = false
    regFail.value = false

    let isValid = true

    if (!fullName.value) {
        fullNameFail.value = true
        isValid = false
    }

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
        const response = await api.post('/register', {
            fullName: fullName.value,
            email: email.value,
            password: password.value
        });

        const isSuccess = response.data.success;
        if (isSuccess) {
            // Redirect to login 
            regFail.value = false
            processing.value = false
            router.push('/')
        } else {
            regFail.value = true
        }
    } catch (error) {
        console.error('Register error:', error.response?.data || error.message)
        regFail.value = true
        processing.value = false
    }
}

</script>

<template>
    <div class="bg-gray-200 font-sans text-gray-700">
        <div class="container mx-auto p-8 flex">
            <div class="max-w-md w-full mx-auto">
                <h1 class="text-4xl text-center mb-12 font-thin">Register on ToDo App</h1>

                <div class="bg-white rounded-lg overflow-hidden shadow-2xl">
                    <div class="p-8">
                        <form method="POST" class="" action="#" @submit="checkForm">
                            <small class="text-red-600" v-if="regFail">Something is wrong. Please try again!</small>
                            <div class="mb-5">
                                <label for="fullName" class="block mb-2 text-sm font-medium text-gray-600">Name</label>

                                <input v-model="fullName" type="text" name="fullName"
                                    class="block w-full p-3 rounded bg-gray-200 border border-transparent focus:outline-none">
                                <small class="text-red-600" v-if="fullNameFail">Please insert your name.</small>
                            </div>

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
                                class="w-full p-3 mt-4 bg-indigo-600 text-white rounded shadow cursor-pointer">Register</button>
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
                        <RouterLink to="/">
                            <a href="#" class="font-medium text-indigo-500">login account</a>
                        </RouterLink>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>