<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import DashboardLayout from '../components/layout/DashboardLayout.vue';

const router = useRouter();

const sites = ref([]);
const sitesLoading = ref(true);
const activeSiteId = ref(null);

const entries = ref([]);
const entriesLoading = ref(false);
const pagination = ref({ current_page: 1, last_page: 1, per_page: 25, total_items: 0 });

const entryIdFilter = ref('');
const sortBy = ref('entry_id');
const sortDir = ref('desc');
const page = ref(1);

const loadSites = async () => {
    sitesLoading.value = true;
    try {
        const { data } = await axios.get('/api/sites/list');
        const list = Array.isArray(data) ? data : (data.sites || data.data || []);
        sites.value = list;
        if (list.length && !activeSiteId.value) {
            activeSiteId.value = list[0].id;
        }
    } catch (e) {
        console.error('Failed to load sites', e);
        sites.value = [];
    } finally {
        sitesLoading.value = false;
    }
};

const loadEntries = async () => {
    if (!activeSiteId.value) {
        entries.value = [];
        return;
    }
    entriesLoading.value = true;
    try {
        const { data } = await axios.get(`/api/data/entries/${activeSiteId.value}`, {
            params: {
                entry_id: entryIdFilter.value || undefined,
                sort_by: sortBy.value,
                sort_dir: sortDir.value,
                page: page.value,
                per_page: pagination.value.per_page,
            },
        });
        const payload = data?.data || {};
        entries.value = payload.items || [];
        pagination.value = payload.pagination || pagination.value;
    } catch (e) {
        console.error('Failed to load entries', e);
        entries.value = [];
    } finally {
        entriesLoading.value = false;
    }
};

const selectSite = (siteId) => {
    if (activeSiteId.value === siteId) return;
    activeSiteId.value = siteId;
    page.value = 1;
};

const applyFilter = () => {
    page.value = 1;
    loadEntries();
};

const clearFilter = () => {
    entryIdFilter.value = '';
    page.value = 1;
    loadEntries();
};

const changeSort = (field) => {
    if (sortBy.value === field) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = field;
        sortDir.value = 'desc';
    }
    page.value = 1;
    loadEntries();
};

const sortIndicator = (field) => {
    if (sortBy.value !== field) return '';
    return sortDir.value === 'asc' ? ' ▲' : ' ▼';
};

const goDetails = (entry) => {
    router.push({
        name: 'dashboard-data-entry',
        params: { site_id: activeSiteId.value, entry_id: entry.entry_id },
    });
};

const goPage = (p) => {
    if (p < 1 || p > pagination.value.last_page) return;
    page.value = p;
    loadEntries();
};

const pages = computed(() => {
    const last = pagination.value.last_page || 1;
    return Array.from({ length: last }, (_, i) => i + 1);
});

watch(activeSiteId, () => {
    loadEntries();
});

onMounted(async () => {
    await loadSites();
    await loadEntries();
});
</script>

<template>
    <DashboardLayout title="Data">
        <div v-if="sitesLoading" class="text-muted">Loading sites...</div>

        <div v-else>
            <div v-if="sites.length === 0" class="text-muted small">
                No sites available.
            </div>

            <div v-else>
                <!-- Site tabs -->
                <ul class="nav nav-tabs mb-3">
                    <li v-for="site in sites" :key="site.id" class="nav-item">
                        <button
                            type="button"
                            class="nav-link"
                            :class="{ active: activeSiteId === site.id }"
                            @click="selectSite(site.id)"
                        >
                            {{ site.name }}
                        </button>
                    </li>
                </ul>

                <!-- Filter + sort controls -->
                <div class="d-flex align-items-end gap-2 mb-3 flex-wrap">
                    <div>
                        <label class="form-label small mb-1">Filter by entry ID</label>
                        <input
                            v-model="entryIdFilter"
                            type="number"
                            class="form-control form-control-sm"
                            placeholder="Entry ID"
                            @keyup.enter="applyFilter"
                        />
                    </div>
                    <button class="btn btn-primary btn-sm" @click="applyFilter">Apply</button>
                    <button class="btn btn-outline-secondary btn-sm" @click="clearFilter">Clear</button>

                    <div class="ms-auto small text-muted">
                        Total: {{ pagination.total_items }}
                    </div>
                </div>

                <div v-if="entriesLoading" class="text-muted">Loading entries...</div>

                <div v-else>
                    <div v-if="entries.length === 0" class="text-muted small">
                        No entries found.
                    </div>

                    <table v-else class="table table-sm table-striped align-middle">
                        <thead>
                            <tr>
                                <th role="button" @click="changeSort('entry_id')">
                                    ID<span>{{ sortIndicator('entry_id') }}</span>
                                </th>
                                <th>Emails</th>
                                <th>Entry updates</th>
                                <th role="button" @click="changeSort('last_update')">
                                    Last update<span>{{ sortIndicator('last_update') }}</span>
                                </th>
                                <th class="text-end">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="e in entries" :key="e.entry_id">
                                <td class="fw-semibold">{{ e.entry_id }}</td>
                                <td>{{ e.emails_count }}</td>
                                <td>{{ e.updates_count }}</td>
                                <td class="text-muted small">{{ e.last_update || '—' }}</td>
                                <td class="text-end">
                                    <button class="btn btn-primary btn-sm" @click="goDetails(e)">
                                        Details
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <nav v-if="pagination.last_page > 1">
                        <ul class="pagination pagination-sm">
                            <li class="page-item" :class="{ disabled: pagination.current_page <= 1 }">
                                <button class="page-link" @click="goPage(pagination.current_page - 1)">Prev</button>
                            </li>
                            <li
                                v-for="p in pages"
                                :key="p"
                                class="page-item"
                                :class="{ active: p === pagination.current_page }"
                            >
                                <button class="page-link" @click="goPage(p)">{{ p }}</button>
                            </li>
                            <li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }">
                                <button class="page-link" @click="goPage(pagination.current_page + 1)">Next</button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
