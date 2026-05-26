<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';
import { useRoute, useRouter } from 'vue-router';
import DashboardLayout from '../components/layout/DashboardLayout.vue';

const props = defineProps({
    site_id: { type: [String, Number], required: true },
});

const route = useRoute();
const router = useRouter();

const VALID_TABS = ['entries', 'emails', 'updates'];
const PER_PAGE = 25;

const site = ref(null);
const siteLoading = ref(false);
const siteError = ref('');

const activeTab = ref(VALID_TABS.includes(route.query.tab) ? route.query.tab : 'entries');

const setTab = (t) => {
    if (!VALID_TABS.includes(t) || activeTab.value === t) return;
    activeTab.value = t;
    router.replace({ query: { ...route.query, tab: t } });
};

const tabState = ref({
    entries: {
        loaded: false, loading: false, error: '',
        items: [], pagination: { current_page: 1, last_page: 1, per_page: PER_PAGE, total: 0 },
        page: 1, sortBy: 'entry_id', sortDir: 'desc',
        filters: { entry_id: '' },
    },
    emails: {
        loaded: false, loading: false, error: '',
        items: [], pagination: { current_page: 1, last_page: 1, per_page: PER_PAGE, total: 0 },
        page: 1, sortBy: 'date_sent', sortDir: 'desc',
        filters: { entry_id: '', subject: '', status: '', email_from: '', email_to: '' },
    },
    updates: {
        loaded: false, loading: false, error: '',
        items: [], pagination: { current_page: 1, last_page: 1, per_page: PER_PAGE, total: 0 },
        page: 1, sortBy: 'change_date', sortDir: 'desc',
        filters: { entry_id: '', field_id: '' },
    },
});

function pickFilters(obj) {
    const out = {};
    for (const [k, v] of Object.entries(obj)) {
        if (v === '' || v == null) continue;
        out[k] = v;
    }
    return out;
}

const loadSite = async () => {
    siteLoading.value = true;
    siteError.value = '';
    try {
        const { data } = await axios.get(`/api/sites/view/${props.site_id}`);
        site.value = data?.data || data;
    } catch {
        site.value = null;
        siteError.value = 'Failed to load site.';
    } finally {
        siteLoading.value = false;
    }
};

const loadEntries = async () => {
    const s = tabState.value.entries;
    s.loading = true;
    s.error = '';
    try {
        const { data } = await axios.get(`/api/data/entries/${props.site_id}`, {
            params: {
                ...pickFilters(s.filters),
                sort_by: s.sortBy, sort_dir: s.sortDir,
                page: s.page, per_page: PER_PAGE,
            },
        });
        const payload = data?.data || {};
        s.items = payload.items || [];
        s.pagination = payload.pagination || s.pagination;
        s.loaded = true;
    } catch {
        s.items = [];
        s.error = 'Failed to load entries.';
    } finally {
        s.loading = false;
    }
};

const loadEmails = async () => {
    const s = tabState.value.emails;
    s.loading = true;
    s.error = '';
    try {
        const { data } = await axios.get(`/api/data/emails/${props.site_id}`, {
            params: {
                ...pickFilters(s.filters),
                sort_by: s.sortBy, sort_dir: s.sortDir,
                page: s.page, per_page: PER_PAGE,
            },
        });
        const payload = data?.data || {};
        s.items = payload.items || [];
        s.pagination = payload.pagination || s.pagination;
        s.loaded = true;
    } catch {
        s.items = [];
        s.error = 'Failed to load emails.';
    } finally {
        s.loading = false;
    }
};

const loadUpdates = async () => {
    const s = tabState.value.updates;
    s.loading = true;
    s.error = '';
    try {
        const { data } = await axios.get(`/api/data/entry-updates/${props.site_id}`, {
            params: {
                ...pickFilters(s.filters),
                sort_by: s.sortBy, sort_dir: s.sortDir,
                page: s.page, per_page: PER_PAGE,
            },
        });
        const payload = data?.data || {};
        s.items = payload.items || [];
        s.pagination = payload.pagination || s.pagination;
        s.loaded = true;
    } catch {
        s.items = [];
        s.error = 'Failed to load updates.';
    } finally {
        s.loading = false;
    }
};

const loaders = { entries: loadEntries, emails: loadEmails, updates: loadUpdates };

const ensureTabLoaded = (tab) => {
    const s = tabState.value[tab];
    if (!s || s.loaded || s.loading) return;
    loaders[tab]();
};

watch(activeTab, (t) => ensureTabLoaded(t));

const applyFilter = (tab) => {
    tabState.value[tab].page = 1;
    loaders[tab]();
};

const clearFilter = (tab) => {
    const s = tabState.value[tab];
    for (const k of Object.keys(s.filters)) s.filters[k] = '';
    s.page = 1;
    loaders[tab]();
};

const changeSort = (tab, field) => {
    const s = tabState.value[tab];
    if (s.sortBy === field) {
        s.sortDir = s.sortDir === 'asc' ? 'desc' : 'asc';
    } else {
        s.sortBy = field;
        s.sortDir = 'desc';
    }
    s.page = 1;
    loaders[tab]();
};

const sortIndicator = (tab, field) => {
    const s = tabState.value[tab];
    if (s.sortBy !== field) return '';
    return s.sortDir === 'asc' ? ' ▲' : ' ▼';
};

const goPage = (tab, p) => {
    const s = tabState.value[tab];
    if (p < 1 || p > s.pagination.last_page) return;
    s.page = p;
    loaders[tab]();
};

const pages = (tab, window = 2) => {
    const s = tabState.value[tab];
    const last = s.pagination.last_page || 1;
    const cur = s.pagination.current_page || 1;
    if (last <= 1) return [{ type: 'page', n: 1 }];

    const set = new Set([1, last]);
    for (let i = cur - window; i <= cur + window; i++) {
        if (i >= 1 && i <= last) set.add(i);
    }
    const sorted = [...set].sort((a, b) => a - b);

    const out = [];
    let prev = 0;
    for (const n of sorted) {
        if (n - prev > 1) out.push({ type: 'gap', key: `g${prev}-${n}` });
        out.push({ type: 'page', n });
        prev = n;
    }
    return out;
};

const goEntryDetails = (row) => {
    router.push({ name: 'dashboard-data-entry', params: { site_id: props.site_id, entry_id: row.entry_id } });
};

// ---------------- Generators ----------------
const GENERATORS = {
    emails: {
        key: 'emails',
        label: 'Emails',
        endpoint: 'emails',
        fields: [
            { name: 'amount', label: 'Amount', type: 'number', min: 1, max: 10000, default: 10 },
            { name: 'length', label: 'Length (chars)', type: 'number', min: 1, max: 100000, default: 200 },
        ],
        reloadTab: 'emails',
    },
    entry_updates: {
        key: 'entry_updates',
        label: 'Entry updates',
        endpoint: 'entry-updates',
        fields: [
            { name: 'amount', label: 'Amount', type: 'number', min: 1, max: 10000, default: 10 },
        ],
        reloadTab: 'updates',
    },
};

const showGenerateModal = ref(false);
const generator = ref(null);
const generateParams = ref({});
const generating = ref(false);
const generateError = ref('');
const generateResult = ref(null);

const openGenerate = (kind) => {
    const g = GENERATORS[kind];
    if (!g) return;
    generator.value = g;
    generateParams.value = Object.fromEntries(g.fields.map((f) => [f.name, f.default]));
    generateResult.value = null;
    generateError.value = '';
    showGenerateModal.value = true;
};

const closeGenerate = () => {
    if (generating.value) return;
    showGenerateModal.value = false;
    generator.value = null;
    generateResult.value = null;
    generateError.value = '';
};

const submitGenerate = async () => {
    if (!generator.value) return;
    generating.value = true;
    generateError.value = '';
    generateResult.value = null;
    try {
        const params = {};
        for (const f of generator.value.fields) {
            const raw = generateParams.value[f.name];
            const num = Number(raw);
            if (!Number.isFinite(num)) throw new Error(`${f.label} must be a number`);
            if (num < f.min || num > f.max) throw new Error(`${f.label} must be between ${f.min} and ${f.max}`);
            params[f.name] = num;
        }
        const { data } = await axios.post(
            `/api/data/generate/${props.site_id}/${generator.value.endpoint}`,
            null,
            { params },
        );
        generateResult.value = data?.data || data;

        loadSite();
        if (tabState.value.entries.loaded) loadEntries();
        const reloadTab = generator.value.reloadTab;
        if (reloadTab && tabState.value[reloadTab]?.loaded) loaders[reloadTab]();
    } catch (e) {
        generateError.value = e?.response?.data?.message || e?.message || 'Generation failed';
    } finally {
        generating.value = false;
    }
};

const formatMs = (v) => (typeof v === 'number' ? `${v.toFixed(2)} ms` : '—');
const formatNumber = (n) => (n == null ? '—' : Number(n).toLocaleString());

// ---------------- Email modal ----------------
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

const resetAll = () => {
    for (const k of ['entries', 'emails', 'updates']) {
        const s = tabState.value[k];
        s.loaded = false;
        s.loading = false;
        s.items = [];
        s.page = 1;
        s.pagination = { current_page: 1, last_page: 1, per_page: PER_PAGE, total: 0 };
        s.error = '';
        for (const fk of Object.keys(s.filters)) s.filters[fk] = '';
    }
};

watch(() => props.site_id, () => {
    resetAll();
    loadSite();
    ensureTabLoaded(activeTab.value);
});

const stats = computed(() => site.value?.stats?.frm || null);

onMounted(async () => {
    await loadSite();
    ensureTabLoaded(activeTab.value);
});
</script>

<template>
    <DashboardLayout title="Data">
        <div v-if="siteLoading" class="text-muted">Loading site…</div>
        <div v-else-if="siteError" class="text-danger">{{ siteError }}</div>
        <div v-else-if="!site" class="text-muted">Site not found.</div>

        <div v-else>
            <!-- Site info block -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-start gap-4">
                        <div>
                            <div class="text-uppercase small text-muted">Site</div>
                            <div class="h5 mb-0">{{ site.name }}</div>
                        </div>
                        <div>
                            <div class="text-uppercase small text-muted">URL</div>
                            <a :href="site.url" target="_blank" rel="noopener noreferrer" class="small">{{ site.url }}</a>
                        </div>
                        <div>
                            <div class="text-uppercase small text-muted">ID</div>
                            <div class="small font-monospace">{{ site.id }}</div>
                        </div>
                    </div>

                    <div v-if="stats" class="mt-3 row g-2">
                        <div class="col-4">
                            <div class="border rounded px-3 py-2 bg-light">
                                <div class="text-uppercase small text-muted">Fields</div>
                                <div class="h5 mb-0">{{ formatNumber(stats.fields_count) }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded px-3 py-2 bg-light">
                                <div class="text-uppercase small text-muted">Emails</div>
                                <div class="h5 mb-0">{{ formatNumber(stats.emails_log_count) }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded px-3 py-2 bg-light">
                                <div class="text-uppercase small text-muted">Entry updates</div>
                                <div class="h5 mb-0">{{ formatNumber(stats.entry_history_count) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <button type="button" class="nav-link" :class="{ active: activeTab === 'entries' }" @click="setTab('entries')">Entries</button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" :class="{ active: activeTab === 'emails' }" @click="setTab('emails')">Emails</button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" :class="{ active: activeTab === 'updates' }" @click="setTab('updates')">Entry updates</button>
                </li>
            </ul>

            <!-- ENTRIES -->
            <section v-if="activeTab === 'entries'">
                <div class="d-flex flex-wrap align-items-end gap-2 mb-3">
                    <div>
                        <label class="form-label small mb-1">Filter by entry ID</label>
                        <input v-model="tabState.entries.filters.entry_id" type="number" class="form-control form-control-sm" placeholder="Entry ID" @keyup.enter="applyFilter('entries')" />
                    </div>
                    <button class="btn btn-primary btn-sm" @click="applyFilter('entries')">Apply</button>
                    <button class="btn btn-outline-secondary btn-sm" @click="clearFilter('entries')">Clear</button>
                    <div class="ms-auto small text-muted">Total: {{ tabState.entries.pagination.total }}</div>
                </div>

                <div v-if="tabState.entries.loading" class="text-muted">Loading entries…</div>
                <div v-else-if="tabState.entries.error" class="text-danger small">{{ tabState.entries.error }}</div>
                <div v-else-if="tabState.entries.items.length === 0" class="text-muted small">No entries found.</div>
                <table v-else class="table table-sm table-striped align-middle">
                    <thead>
                        <tr>
                            <th role="button" @click="changeSort('entries', 'entry_id')">ID<span>{{ sortIndicator('entries', 'entry_id') }}</span></th>
                            <th role="button" @click="changeSort('entries', 'email_count')">Emails<span>{{ sortIndicator('entries', 'email_count') }}</span></th>
                            <th role="button" @click="changeSort('entries', 'update_count')">Updates<span>{{ sortIndicator('entries', 'update_count') }}</span></th>
                            <th role="button" @click="changeSort('entries', 'last_update')">Last update<span>{{ sortIndicator('entries', 'last_update') }}</span></th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="e in tabState.entries.items" :key="e.entry_id">
                            <td class="fw-semibold">{{ e.entry_id }}</td>
                            <td>{{ e.emails_count }}</td>
                            <td>{{ e.updates_count }}</td>
                            <td class="text-muted small">{{ e.last_update || '—' }}</td>
                            <td class="text-end">
                                <button class="btn btn-primary btn-sm" @click="goEntryDetails(e)">Details</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <nav v-if="tabState.entries.pagination.last_page > 1">
                    <ul class="pagination pagination-sm">
                        <li class="page-item" :class="{ disabled: tabState.entries.pagination.current_page <= 1 }">
                            <button class="page-link" @click="goPage('entries', tabState.entries.pagination.current_page - 1)">Prev</button>
                        </li>
                        <template v-for="item in pages('entries')">
                            <li v-if="item.type === 'gap'" :key="item.key" class="page-item disabled">
                                <span class="page-link">…</span>
                            </li>
                            <li v-else :key="item.n" class="page-item" :class="{ active: item.n === tabState.entries.pagination.current_page }">
                                <button class="page-link" @click="goPage('entries', item.n)">{{ item.n }}</button>
                            </li>
                        </template>
                        <li class="page-item" :class="{ disabled: tabState.entries.pagination.current_page >= tabState.entries.pagination.last_page }">
                            <button class="page-link" @click="goPage('entries', tabState.entries.pagination.current_page + 1)">Next</button>
                        </li>
                    </ul>
                </nav>
            </section>

            <!-- EMAILS -->
            <section v-else-if="activeTab === 'emails'">
                <div class="d-flex flex-wrap align-items-end gap-2 mb-3">
                    <div>
                        <label class="form-label small mb-1">Entry ID</label>
                        <input v-model="tabState.emails.filters.entry_id" type="number" class="form-control form-control-sm" style="width:8rem" @keyup.enter="applyFilter('emails')" />
                    </div>
                    <div>
                        <label class="form-label small mb-1">Subject contains</label>
                        <input v-model="tabState.emails.filters.subject" type="text" class="form-control form-control-sm" @keyup.enter="applyFilter('emails')" />
                    </div>
                    <div>
                        <label class="form-label small mb-1">From</label>
                        <input v-model="tabState.emails.filters.email_from" type="text" class="form-control form-control-sm" @keyup.enter="applyFilter('emails')" />
                    </div>
                    <div>
                        <label class="form-label small mb-1">To</label>
                        <input v-model="tabState.emails.filters.email_to" type="text" class="form-control form-control-sm" @keyup.enter="applyFilter('emails')" />
                    </div>
                    <div>
                        <label class="form-label small mb-1">Status</label>
                        <input v-model="tabState.emails.filters.status" type="number" class="form-control form-control-sm" style="width:7rem" @keyup.enter="applyFilter('emails')" />
                    </div>
                    <button class="btn btn-primary btn-sm" @click="applyFilter('emails')">Apply</button>
                    <button class="btn btn-outline-secondary btn-sm" @click="clearFilter('emails')">Clear</button>
                    <button class="btn btn-success btn-sm" @click="openGenerate('emails')">Generate emails</button>
                    <div class="ms-auto small text-muted">Total: {{ tabState.emails.pagination.total }}</div>
                </div>

                <div v-if="tabState.emails.loading" class="text-muted">Loading emails…</div>
                <div v-else-if="tabState.emails.error" class="text-danger small">{{ tabState.emails.error }}</div>
                <div v-else-if="tabState.emails.items.length === 0" class="text-muted small">No emails found.</div>
                <table v-else class="table table-sm table-striped align-middle">
                    <thead>
                        <tr>
                            <th role="button" @click="changeSort('emails', 'id')">ID<span>{{ sortIndicator('emails', 'id') }}</span></th>
                            <th>Entry</th>
                            <th role="button" @click="changeSort('emails', 'subject')">Subject<span>{{ sortIndicator('emails', 'subject') }}</span></th>
                            <th role="button" @click="changeSort('emails', 'email_from')">From<span>{{ sortIndicator('emails', 'email_from') }}</span></th>
                            <th role="button" @click="changeSort('emails', 'email_to')">To<span>{{ sortIndicator('emails', 'email_to') }}</span></th>
                            <th role="button" @click="changeSort('emails', 'status')">Status<span>{{ sortIndicator('emails', 'status') }}</span></th>
                            <th role="button" @click="changeSort('emails', 'date_sent')">Sent<span>{{ sortIndicator('emails', 'date_sent') }}</span></th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="em in tabState.emails.items" :key="em.id">
                            <td>{{ em.id }}</td>
                            <td class="text-muted small">{{ em.entry_id ?? '—' }}</td>
                            <td>{{ em.subject }}</td>
                            <td class="small">{{ em.email_from }}</td>
                            <td class="small">{{ em.email_to }}</td>
                            <td>{{ em.status }}</td>
                            <td class="text-muted small">{{ em.date_sent }}</td>
                            <td class="text-end">
                                <button class="btn btn-primary btn-sm" @click="openEmail(em)">View content</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <nav v-if="tabState.emails.pagination.last_page > 1">
                    <ul class="pagination pagination-sm">
                        <li class="page-item" :class="{ disabled: tabState.emails.pagination.current_page <= 1 }">
                            <button class="page-link" @click="goPage('emails', tabState.emails.pagination.current_page - 1)">Prev</button>
                        </li>
                        <template v-for="item in pages('emails')">
                            <li v-if="item.type === 'gap'" :key="item.key" class="page-item disabled">
                                <span class="page-link">…</span>
                            </li>
                            <li v-else :key="item.n" class="page-item" :class="{ active: item.n === tabState.emails.pagination.current_page }">
                                <button class="page-link" @click="goPage('emails', item.n)">{{ item.n }}</button>
                            </li>
                        </template>
                        <li class="page-item" :class="{ disabled: tabState.emails.pagination.current_page >= tabState.emails.pagination.last_page }">
                            <button class="page-link" @click="goPage('emails', tabState.emails.pagination.current_page + 1)">Next</button>
                        </li>
                    </ul>
                </nav>
            </section>

            <!-- UPDATES -->
            <section v-else-if="activeTab === 'updates'">
                <div class="d-flex flex-wrap align-items-end gap-2 mb-3">
                    <div>
                        <label class="form-label small mb-1">Entry ID</label>
                        <input v-model="tabState.updates.filters.entry_id" type="number" class="form-control form-control-sm" style="width:8rem" @keyup.enter="applyFilter('updates')" />
                    </div>
                    <div>
                        <label class="form-label small mb-1">Field ID</label>
                        <input v-model="tabState.updates.filters.field_id" type="number" class="form-control form-control-sm" style="width:8rem" @keyup.enter="applyFilter('updates')" />
                    </div>
                    <button class="btn btn-primary btn-sm" @click="applyFilter('updates')">Apply</button>
                    <button class="btn btn-outline-secondary btn-sm" @click="clearFilter('updates')">Clear</button>
                    <button class="btn btn-success btn-sm" @click="openGenerate('entry_updates')">Generate entry updates</button>
                    <div class="ms-auto small text-muted">Total: {{ tabState.updates.pagination.total }}</div>
                </div>

                <div v-if="tabState.updates.loading" class="text-muted">Loading updates…</div>
                <div v-else-if="tabState.updates.error" class="text-danger small">{{ tabState.updates.error }}</div>
                <div v-else-if="tabState.updates.items.length === 0" class="text-muted small">No updates found.</div>
                <table v-else class="table table-sm table-striped align-middle">
                    <thead>
                        <tr>
                            <th role="button" @click="changeSort('updates', 'id')">ID<span>{{ sortIndicator('updates', 'id') }}</span></th>
                            <th role="button" @click="changeSort('updates', 'entry_id')">Entry<span>{{ sortIndicator('updates', 'entry_id') }}</span></th>
                            <th>Type</th>
                            <th role="button" @click="changeSort('updates', 'field_id')">Field<span>{{ sortIndicator('updates', 'field_id') }}</span></th>
                            <th>Old value</th>
                            <th>New value</th>
                            <th role="button" @click="changeSort('updates', 'change_date')">Change date<span>{{ sortIndicator('updates', 'change_date') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="u in tabState.updates.items" :key="u.id">
                            <td>{{ u.id }}</td>
                            <td class="text-muted small">{{ u.entry_id ?? '—' }}</td>
                            <td>{{ u.update_type }}</td>
                            <td>
                                <span v-if="u.field_label || u.field_key" class="fw-semibold">{{ u.field_label || u.field_key }}</span>
                                <span v-else class="text-muted">#{{ u.field_id }}</span>
                            </td>
                            <td class="small"><code>{{ u.old_value }}</code></td>
                            <td class="small"><code>{{ u.new_value }}</code></td>
                            <td class="small text-muted">{{ u.change_date }}</td>
                        </tr>
                    </tbody>
                </table>

                <nav v-if="tabState.updates.pagination.last_page > 1">
                    <ul class="pagination pagination-sm">
                        <li class="page-item" :class="{ disabled: tabState.updates.pagination.current_page <= 1 }">
                            <button class="page-link" @click="goPage('updates', tabState.updates.pagination.current_page - 1)">Prev</button>
                        </li>
                        <template v-for="item in pages('updates')">
                            <li v-if="item.type === 'gap'" :key="item.key" class="page-item disabled">
                                <span class="page-link">…</span>
                            </li>
                            <li v-else :key="item.n" class="page-item" :class="{ active: item.n === tabState.updates.pagination.current_page }">
                                <button class="page-link" @click="goPage('updates', item.n)">{{ item.n }}</button>
                            </li>
                        </template>
                        <li class="page-item" :class="{ disabled: tabState.updates.pagination.current_page >= tabState.updates.pagination.last_page }">
                            <button class="page-link" @click="goPage('updates', tabState.updates.pagination.current_page + 1)">Next</button>
                        </li>
                    </ul>
                </nav>
            </section>
        </div>

        <!-- Generate modal -->
        <div v-if="showGenerateModal" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,.4)">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate {{ generator?.label?.toLowerCase() }}</h5>
                        <button type="button" class="btn-close" :disabled="generating" @click="closeGenerate"></button>
                    </div>
                    <form v-if="!generateResult" @submit.prevent="submitGenerate">
                        <div class="modal-body">
                            <div v-for="f in generator?.fields || []" :key="f.name" class="mb-3">
                                <label class="form-label small">
                                    {{ f.label }} <span class="text-muted">({{ f.min }}–{{ f.max }})</span>
                                </label>
                                <input
                                    v-model.number="generateParams[f.name]"
                                    type="number"
                                    :min="f.min"
                                    :max="f.max"
                                    class="form-control form-control-sm"
                                />
                            </div>
                            <div v-if="generateError" class="alert alert-danger py-2 small">{{ generateError }}</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary btn-sm" :disabled="generating" @click="closeGenerate">Cancel</button>
                            <button type="submit" class="btn btn-success btn-sm" :disabled="generating">
                                {{ generating ? 'Generating…' : 'Generate' }}
                            </button>
                        </div>
                    </form>
                    <div v-else>
                        <div class="modal-body">
                            <div class="alert alert-success py-2 small">
                                Generated <strong>{{ generateResult.count }}</strong> {{ generateResult.kind }} row(s).
                            </div>
                            <div class="text-uppercase small text-muted mb-1">Timings</div>
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="border rounded px-2 py-1 bg-light small">
                                        <div class="text-muted">Generation</div>
                                        <div class="fw-semibold">{{ formatMs(generateResult.timings?.generation_ms) }}</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded px-2 py-1 bg-light small">
                                        <div class="text-muted">Insertion</div>
                                        <div class="fw-semibold">{{ formatMs(generateResult.timings?.insertion_ms) }}</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded px-2 py-1 bg-light small">
                                        <div class="text-muted">Total</div>
                                        <div class="fw-semibold">{{ formatMs(generateResult.timings?.total_ms) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" @click="closeGenerate">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email modal -->
        <div v-if="showEmailModal" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,.4)">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-1">{{ viewingEmail?.subject || '(no subject)' }}</h5>
                            <div class="small text-muted">
                                <div><strong>Entry:</strong> {{ viewingEmail?.entry_id ?? '—' }}</div>
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
