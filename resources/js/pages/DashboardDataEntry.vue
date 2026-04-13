<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import DashboardLayout from '../components/layout/DashboardLayout.vue';

const props = defineProps({
    site_id: { type: [String, Number], required: true },
    entry_id: { type: [String, Number], required: true },
});

const router = useRouter();

const activeTab = ref('updates');
const updates = ref([]);
const emails = ref([]);
const loadingUpdates = ref(false);
const loadingEmails = ref(false);
const error = ref(null);

const loadUpdates = async () => {
    loadingUpdates.value = true;
    try {
        const { data } = await axios.get(`/api/data/entries/${props.site_id}/${props.entry_id}/updates`);
        updates.value = data?.data?.items || [];
    } catch (e) {
        console.error(e);
        error.value = 'Failed to load entry updates.';
    } finally {
        loadingUpdates.value = false;
    }
};

const loadEmails = async () => {
    loadingEmails.value = true;
    try {
        const { data } = await axios.get(`/api/data/entries/${props.site_id}/${props.entry_id}/emails`);
        emails.value = data?.data?.items || [];
    } catch (e) {
        console.error(e);
        error.value = 'Failed to load emails.';
    } finally {
        loadingEmails.value = false;
    }
};

const selectTab = (tab) => {
    activeTab.value = tab;
    if (tab === 'updates' && updates.value.length === 0) loadUpdates();
    if (tab === 'emails' && emails.value.length === 0) loadEmails();
};

const goBack = () => {
    router.push({ name: 'dashboard-data' });
};

onMounted(loadUpdates);
</script>

<template>
    <DashboardLayout title="Entry details">
        <button type="button" class="btn btn-link px-0 mb-3" @click="goBack">
            ← Back to Data
        </button>

        <h2 class="h5 mb-3">
            Entry #{{ entry_id }} <span class="text-muted small">(site {{ site_id }})</span>
        </h2>

        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <button
                    type="button"
                    class="nav-link"
                    :class="{ active: activeTab === 'updates' }"
                    @click="selectTab('updates')"
                >
                    Entry updates
                </button>
            </li>
            <li class="nav-item">
                <button
                    type="button"
                    class="nav-link"
                    :class="{ active: activeTab === 'emails' }"
                    @click="selectTab('emails')"
                >
                    Emails
                </button>
            </li>
        </ul>

        <div v-if="activeTab === 'updates'">
            <div v-if="loadingUpdates" class="text-muted">Loading updates...</div>
            <div v-else-if="updates.length === 0" class="text-muted small">No updates.</div>
            <table v-else class="table table-sm table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Field</th>
                        <th>Old value</th>
                        <th>New value</th>
                        <th>Change date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="u in updates" :key="u.id">
                        <td>{{ u.id }}</td>
                        <td>{{ u.update_type }}</td>
                        <td>
                            <span v-if="u.field">
                                <span class="fw-semibold">{{ u.field.label || u.field.key }}</span>
                                <span class="text-muted small ms-1">({{ u.field.type }})</span>
                            </span>
                            <span v-else class="text-muted">#{{ u.field_id }}</span>
                        </td>
                        <td class="small"><code>{{ u.old_value }}</code></td>
                        <td class="small"><code>{{ u.new_value }}</code></td>
                        <td class="small text-muted">{{ u.change_date }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else-if="activeTab === 'emails'">
            <div v-if="loadingEmails" class="text-muted">Loading emails...</div>
            <div v-else-if="emails.length === 0" class="text-muted small">No emails.</div>
            <table v-else class="table table-sm table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Status</th>
                        <th>Sent</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="em in emails" :key="em.id">
                        <td>{{ em.id }}</td>
                        <td>{{ em.subject }}</td>
                        <td class="small">{{ em.email_from }}</td>
                        <td class="small">{{ em.email_to }}</td>
                        <td>{{ em.status }}</td>
                        <td class="small text-muted">{{ em.date_sent }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </DashboardLayout>
</template>
