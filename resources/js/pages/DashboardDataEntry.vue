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
const updatesLoaded = ref(false);
const emailsLoaded = ref(false);
const error = ref('');

const loadUpdates = async () => {
    loadingUpdates.value = true;
    try {
        const { data } = await axios.get(`/api/data/entries/${props.site_id}/${props.entry_id}/updates`);
        updates.value = data?.data?.items || [];
        updatesLoaded.value = true;
    } catch (e) {
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
        emailsLoaded.value = true;
    } catch (e) {
        error.value = 'Failed to load emails.';
    } finally {
        loadingEmails.value = false;
    }
};

const selectTab = (tab) => {
    activeTab.value = tab;
    if (tab === 'updates' && !updatesLoaded.value) loadUpdates();
    if (tab === 'emails' && !emailsLoaded.value) loadEmails();
};

const goBack = () => {
    router.push({ name: 'dashboard-data-site', params: { site_id: props.site_id } });
};

// Email modal
const showEmailModal = ref(false);
const viewingEmail = ref(null);
const emailView = ref('plain');

const openEmail = (em) => {
    viewingEmail.value = em;
    emailView.value = em?.content_html ? 'html' : 'plain';
    showEmailModal.value = true;
};

const closeEmail = () => {
    showEmailModal.value = false;
    viewingEmail.value = null;
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
                >Entry updates</button>
            </li>
            <li class="nav-item">
                <button
                    type="button"
                    class="nav-link"
                    :class="{ active: activeTab === 'emails' }"
                    @click="selectTab('emails')"
                >Emails</button>
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
                        <th class="text-end">Actions</th>
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
                        <td class="text-end">
                            <button class="btn btn-primary btn-sm" @click="openEmail(em)">View</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Email modal -->
        <div v-if="showEmailModal" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,.4)">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-1">{{ viewingEmail?.subject || '(no subject)' }}</h5>
                            <div class="small text-muted">
                                <div><strong>From:</strong> {{ viewingEmail?.email_from || '—' }}</div>
                                <div><strong>To:</strong> {{ viewingEmail?.email_to || '—' }}</div>
                                <div><strong>Sent:</strong> {{ viewingEmail?.date_sent || '—' }}</div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" @click="closeEmail"></button>
                    </div>
                    <div class="px-3 pt-2">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <button class="nav-link" :class="{ active: emailView === 'html' }" :disabled="!viewingEmail?.content_html" @click="emailView = 'html'">HTML</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" :class="{ active: emailView === 'plain' }" :disabled="!viewingEmail?.content_plain" @click="emailView = 'plain'">Plain text</button>
                            </li>
                        </ul>
                    </div>
                    <div class="modal-body" style="max-height: 60vh; overflow:auto">
                        <template v-if="emailView === 'html'">
                            <iframe
                                v-if="viewingEmail?.content_html"
                                :srcdoc="viewingEmail.content_html"
                                class="w-100 border rounded"
                                style="min-height: 360px"
                                sandbox=""
                            />
                            <div v-else class="text-muted small">No HTML body.</div>
                        </template>
                        <template v-else>
                            <pre v-if="viewingEmail?.content_plain" class="small bg-light border rounded p-2" style="white-space: pre-wrap">{{ viewingEmail.content_plain }}</pre>
                            <div v-else class="text-muted small">No plain-text body.</div>
                        </template>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary btn-sm" @click="closeEmail">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
