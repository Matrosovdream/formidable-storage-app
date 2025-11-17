<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const user = ref(null);
const loading = ref(true);

const loadUser = async () => {
    try {
        const { data } = await axios.get('/api/user');
        user.value = data;
    } catch (e) {
        user.value = null;
        await router.push({ name: 'login' });
    } finally {
        loading.value = false;
    }
};

const logout = async () => {
    await axios.get('/api/logout');
    await router.push({ name: 'login' });
};

onMounted(loadUser);
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center">
        <div class="max-w-xl w-full bg-white shadow-md rounded px-8 py-6">
            <div v-if="loading">
                Loading...
            </div>
            <div v-else>
                <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
                <p class="mb-4">
                    Logged in as:
                    <strong>{{ user?.name }} ({{ user?.email }})</strong>
                </p>

                <button
                    @click="logout"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                >
                    Logout
                </button>
            </div>
        </div>
    </div>
</template>
