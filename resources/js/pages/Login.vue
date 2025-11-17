<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const form = ref({
    email: '',
    password: '',
    remember: false,
});

const errors = ref(null);
const loading = ref(false);

const submit = async () => {
    loading.value = true;
    errors.value = null;

    try {
        // Sanctum CSRF cookie
        await axios.get('/sanctum/csrf-cookie');

        await axios.post('/api/login', form.value);

        await router.push({ name: 'dashboard' });
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors || { general: [error.response.data.message] };
        } else {
            errors.value = { general: ['Unexpected error.'] };
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="w-full max-w-md bg-white shadow-md rounded px-8 py-6">
            <h1 class="text-2xl font-bold mb-4 text-center">Login</h1>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Email
                    </label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                        required
                    />
                    <div v-if="errors?.email" class="text-red-500 text-xs mt-1">
                        {{ errors.email[0] }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Password
                    </label>
                    <input
                        v-model="form.password"
                        type="password"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                        required
                    />
                    <div v-if="errors?.password" class="text-red-500 text-xs mt-1">
                        {{ errors.password[0] }}
                    </div>
                </div>

                <div class="mb-4 flex items-center">
                    <input
                        id="remember"
                        type="checkbox"
                        v-model="form.remember"
                        class="mr-2"
                    />
                    <label for="remember" class="text-sm text-gray-700">Remember me</label>
                </div>

                <div v-if="errors?.general" class="mb-4 text-red-500 text-sm">
                    {{ errors.general[0] }}
                </div>

                <div class="flex items-center justify-between">
                    <button
                        type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                        :disabled="loading"
                    >
                        <span v-if="!loading">Login</span>
                        <span v-else>Loading...</span>
                    </button>

                    <router-link
                        :to="{ name: 'register' }"
                        class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800"
                    >
                        Register
                    </router-link>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
/* Optional basic styling, or plug in Tailwind properly */
</style>
