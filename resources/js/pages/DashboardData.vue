<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import DashboardLayout from '../components/layout/DashboardLayout.vue';

const router = useRouter();
const sites = ref([]);
const loading = ref(true);

onMounted(async () => {
    try {
        const { data } = await axios.get('/api/sites/list');
        const list = Array.isArray(data) ? data : (data.data || data.sites || []);
        sites.value = list;
        if (list.length) {
            router.replace({ name: 'dashboard-data-site', params: { site_id: list[0].id } });
        }
    } catch (e) {
        sites.value = [];
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <DashboardLayout title="Data">
        <div v-if="loading" class="text-muted">Loading sites…</div>
        <div v-else-if="sites.length === 0" class="text-muted">
            <p class="mb-2">You don't have any sites yet.</p>
            <router-link
                :to="{ name: 'dashboard-site-add' }"
                class="btn btn-success btn-sm"
            >Add a site</router-link>
        </div>
        <div v-else class="text-muted small">Select a site from the sidebar.</div>
    </DashboardLayout>
</template>
