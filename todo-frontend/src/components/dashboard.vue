<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/axios'

const router = useRouter()
const processing = ref(false)
const taskTitle = ref('')
const taskDescription = ref('')
const taskTitleFail = ref(false)
const taskDescriptionFail = ref(false)
const TaskFormFail = ref(false)
const tasks = ref([])

const checkTaskForm = async (e) => {
    e.preventDefault()
    processing.value = true

    TaskFormFail.value = false
    taskTitleFail.value = false
    taskDescriptionFail.value = false

    let isValid = true

    if (!taskTitle.value.trim()) {
        taskTitleFail.value = true
        isValid = false
    }
    if (!taskDescription.value.trim()) {
        taskDescriptionFail.value = true
        isValid = false
    }
    if (!isValid) {
        processing.value = false
        return
    }

    try {
        const res = await api.post('/tasks', {
            title: taskTitle.value,
            description: taskDescription.value
        }, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('auth_token')}`
            }
        })

        // Add new task to list
        tasks.value.unshift(res.data.task)

        // Clear form
        taskTitle.value = ''
        taskDescription.value = ''
    } catch (err) {
        // console.error('Error adding task:', err)
        TaskFormFail.value = true
    } finally {
        processing.value = false
    }

}

onMounted(async () => {
    const token = localStorage.getItem('auth_token')
    if (!token) {
        router.push('/')
        return
    }

    try {
        const res = await api.get('/tasks', {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })
        tasks.value = res.data.tasks
    } catch (err) {
        // console.error('Error fetching tasks:', err)
        router.push('/')
    }
})
</script>

<style>
input[type=checkbox]:checked+label span:first-of-type {
    background-color: #10B981;
    border-color: #10B981;
    color: #fff;
}

input[type=checkbox]:checked+label span:nth-of-type(2) {
    text-decoration: line-through;
    color: #9CA3AF;
}
</style>
<template>
    <div class="grid grid-cols-2 gap-10">
        <div class="col-span-2 md:col-span-1">
            <div class="h-screen font-medium m-10">
                <div class="flex mb-6">
                    <svg class="h-8 w-8 text-indigo-500 stroke-current" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h4 class="font-semibold ml-3 text-lg">Add New Task</h4>
                </div>
                <form method="post" action="#" @submit="checkTaskForm">
                    <small class="text-red-600" v-if="TaskFormFail">Something is wrong. Please try again!</small>
                    <div class="mb-5">
                        <label for="taskTitle" class="block mb-2 text-sm font-medium text-gray-600">Task Title</label>
                        <input v-model="taskTitle" type="text" name="taskTitle"
                            class="block w-full p-3 rounded bg-gray-200 border border-transparent focus:outline-none">
                        <small class="text-red-600" v-if="taskTitleFail">Please insert a task title.</small>
                    </div>
                    <div class="mb-5">
                        <label for="taskDescription" class="block mb-2 text-sm font-medium text-gray-600">Task
                            Description</label>
                        <input v-model="taskDescription" type="text" name="taskDescription"
                            class="block w-full p-3 h-40 rounded bg-gray-200 border border-transparent focus:outline-none">
                        <small class="text-red-600" v-if="taskDescriptionFail">Please explain your task.</small>
                    </div>
                    <button v-if="!processing"
                        class="w-full p-3 mt-4 bg-indigo-600 text-white rounded shadow cursor-pointer">Add Task</button>
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
        </div>
        <div class="col-span-2 md:col-span-1">
            <div class="flex h-screen font-medium">
                <div class="flex flex-grow  h-full text-gray-600 bg-gray-100 p-8">
                    <!-- Component Start -->
                    <div class="w-full p-8 bg-white rounded-lg shadow-lg w-96">
                        <div class="flex items-center mb-6">
                            <svg class="h-8 w-8 text-indigo-500 stroke-current" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <h4 class="font-semibold ml-3 text-lg">Frodo's Jobs</h4>
                        </div>
                        <div v-for="task in tasks" :key="task.id">
                            <input class="hidden" type="checkbox" :id="`task_${task.id}`">
                            <label class="flex items-center h-10 px-2 rounded cursor-pointer hover:bg-gray-100"
                                :for="`task_${task.id}`">
                                <span
                                    class="flex items-center justify-center w-5 h-5 text-transparent border-2 border-gray-300 rounded-full">
                                    <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span class="ml-4 text-sm">{{ task.title }}</span>
                            </label>
                        </div>

                        <div>
                            <input class="hidden" type="checkbox" id="task_1" checked>
                            <label class="flex items-center h-10 px-2 rounded cursor-pointer hover:bg-gray-100"
                                for="task_1">
                                <span
                                    class="flex items-center justify-center w-5 h-5 text-transparent border-2 border-gray-300 rounded-full">
                                    <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span class="ml-4 text-sm">Weed front garden.</span>
                            </label>
                        </div>
                        <div>
                            <input class="hidden" type="checkbox" id="task_2" checked>
                            <label class="flex items-center h-10 px-2 rounded cursor-pointer hover:bg-gray-100"
                                for="task_2">
                                <span
                                    class="flex items-center justify-center w-5 h-5 text-transparent border-2 border-gray-300 rounded-full">
                                    <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span class="ml-4 text-sm">Chill and smoke some Old Toby.</span>
                            </label>
                        </div>
                        <div>
                            <input class="hidden" type="checkbox" id="task_3">
                            <label class="flex items-center h-10 px-2 rounded cursor-pointer hover:bg-gray-100"
                                for="task_3">
                                <span
                                    class="flex items-center justify-center w-5 h-5 text-transparent border-2 border-gray-300 rounded-full">
                                    <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span class="ml-4 text-sm">Keep ring secret and safe.</span>
                            </label>
                        </div>
                        <div>
                            <input class="hidden" type="checkbox" id="task_4">
                            <label class="flex items-center h-10 px-2 rounded cursor-pointer hover:bg-gray-100"
                                for="task_4">
                                <span
                                    class="flex items-center justify-center w-5 h-5 text-transparent border-2 border-gray-300 rounded-full">
                                    <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span class="ml-4 text-sm">Meet Gandalf at Bree.</span>
                            </label>
                        </div>
                        <div>
                            <input class="hidden" type="checkbox" id="task_5">
                            <label class="flex items-center h-10 px-2 rounded cursor-pointer hover:bg-gray-100"
                                for="task_5">
                                <span
                                    class="flex items-center justify-center w-5 h-5 text-transparent border-2 border-gray-300 rounded-full">
                                    <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span class="ml-4 text-sm">Destroy ring and defeat dark lord.</span>
                            </label>
                        </div>
                    </div>
                    <!-- Component End  -->
                </div>
            </div>
        </div>
    </div>

</template>