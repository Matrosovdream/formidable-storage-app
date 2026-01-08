<script setup>
    import { ref, onMounted } from 'vue';
    import axios from 'axios';
    import { useRouter } from 'vue-router';
    import DashboardLayout from '../components/layout/DashboardLayout.vue';
    
    const router = useRouter();
    const sites = ref([]);
    const sitesLoading = ref(true);
    
    // Delete modal state
    const showDeleteModal = ref(false);
    const siteToDelete = ref(null);
    const deleting = ref(false);
    const deleteError = ref(null);
    
    const loadSites = async () => {
        sitesLoading.value = true;
        try {
            const { data } = await axios.get('/api/sites/list');
            let list = [];
    
            if (Array.isArray(data)) {
                list = data;
            } else if (Array.isArray(data.sites)) {
                list = data.sites;
            } else if (Array.isArray(data.data)) {
                list = data.data;
            }
    
            sites.value = list;
        } catch (e) {
            console.error('Failed to load sites', e);
            sites.value = [];
        } finally {
            sitesLoading.value = false;
        }
    };
    
    const goToSite = (site) => {
        router.push({
            name: 'dashboard-site-view',
            params: { site_id: site.id },
        });
    };
    
    const goToAddSite = () => {
        router.push({ name: 'dashboard-site-add' });
    };
    
    // Format stats safely
    const formatStats = (site) => {
        const frm = site?.stats?.frm || {};

        return {
            fields: formatNumber(Number(frm.fields_count ?? 0)),
            emails: formatNumber(Number(frm.emails_log_count ?? 0)),
            history: formatNumber(Number(frm.entry_history_count ?? 0)),
        };
    };

    const formatNumber = (value) => {
        return new Intl.NumberFormat('de-DE').format(value);
        // de-DE → 1.200.300
        // en-US → 1,200,300
    };
    
    // Open delete popup
    const askDelete = (site) => {
        siteToDelete.value = site;
        deleteError.value = null;
        showDeleteModal.value = true;
    };
    
    // Close popup
    const cancelDelete = () => {
        showDeleteModal.value = false;
        siteToDelete.value = null;
        deleteError.value = null;
    };
    
    // Confirm delete
    const confirmDelete = async () => {
        if (!siteToDelete.value) {
            return;
        }
    
        deleting.value = true;
        deleteError.value = null;
    
        try {
            await axios.delete(`/api/sites/delete/${siteToDelete.value.id}`);
    
            // Reload list
            await loadSites();
    
            // Ensure we are on the sites page
            router.push({ name: 'dashboard-sites' });
    
            // Close modal
            showDeleteModal.value = false;
            siteToDelete.value = null;
        } catch (e) {
            console.error('Failed to delete site', e);
            deleteError.value = 'Failed to delete site.';
        } finally {
            deleting.value = false;
        }
    };
    
    onMounted(loadSites);
    </script>
    
    <template>
        <DashboardLayout title="Sites">
            <div v-if="sitesLoading" class="text-muted">
                Loading sites...
            </div>
    
            <div v-else>
                <!-- Top row: title + Add button on the right -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Your sites</h2>
    
                    <button
                        type="button"
                        class="btn btn-success btn-sm"
                        @click="goToAddSite"
                    >
                        Add site
                    </button>
                </div>
    
                <div v-if="sites.length === 0" class="text-muted small">
                    No sites yet.
                </div>
    
                <ul v-else class="list-group">
                    <li
                        v-for="site in sites"
                        :key="site.id"
                        class="list-group-item d-flex justify-content-between align-items-center mb-2"
                    >
                        <!-- Column 1: Site -->
                        <div class="me-3">
                            <div class="fw-semibold">{{ site.name }}</div>
                            <div class="text-muted small">{{ site.url }}</div>
                        </div>
    
                        <!-- Column 2: Stats (NEW) -->
                        <div class="text-muted small me-3 stats-col">
                            <div class="d-flex gap-3 flex-wrap">
                                <div>
                                    <div class="fw-bold text-dark">
                                        {{ formatStats(site).fields }}
                                    </div>
                                    <div>Fields</div>
                                </div>
    
                                <div>
                                    <div class="fw-bold text-dark">
                                        {{ formatStats(site).emails }}
                                    </div>
                                    <div>Emails</div>
                                </div>
    
                                <div>
                                    <div class="fw-bold text-dark">
                                        {{ formatStats(site).history }}
                                    </div>
                                    <div>Entry updates</div>
                                </div>
                            </div>
                        </div>
    
                        <!-- Column 3: Actions -->
                        <div class="d-flex gap-2">
                            <button
                                type="button"
                                class="btn btn-primary btn-sm"
                                @click="goToSite(site)"
                            >
                                View
                            </button>
    
                            <!-- Delete button -->
                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                @click="askDelete(site)"
                            >
                                Delete
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
    
            <!-- Delete confirmation popup -->
            <div v-if="showDeleteModal" class="modal-backdrop-custom">
                <div class="modal-dialog-custom card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Are you sure?</h5>
                        <p class="mb-3">
                            You are about to delete site
                            <strong>{{ siteToDelete?.name }}</strong>.
                        </p>
    
                        <div v-if="deleteError" class="alert alert-danger py-2">
                            {{ deleteError }}
                        </div>
    
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button
                                type="button"
                                class="btn btn-secondary btn-sm"
                                @click="cancelDelete"
                                :disabled="deleting"
                            >
                                No
                            </button>
                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                @click="confirmDelete"
                                :disabled="deleting"
                            >
                                {{ deleting ? 'Deleting...' : 'Yes, delete' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    </template>
    
    <style scoped>
    .modal-backdrop-custom {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1050;
    }
    
    .modal-dialog-custom {
        max-width: 400px;
        width: 100%;
    }
    
    /* Optional: keep stats width consistent */
    .stats-col {
        min-width: 220px;
    }
    </style>
    