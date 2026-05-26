<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

const dataRoutes = ['dashboard-data', 'dashboard-data-site', 'dashboard-data-entry'];

const isActive = (names) => {
    if (Array.isArray(names)) {
        return names.includes(route.name);
    }
    return route.name === names;
};

const isDataRoute = computed(() => dataRoutes.includes(route.name));

const sites = ref([]);
const sitesLoading = ref(false);
const sitesLoaded = ref(false);

const loadSites = async () => {
    if (sitesLoaded.value || sitesLoading.value) return;
    sitesLoading.value = true;
    try {
        const { data } = await axios.get('/api/sites/list');
        const list = Array.isArray(data) ? data : (data.data || data.sites || []);
        sites.value = list;
        sitesLoaded.value = true;
    } catch (e) {
        sites.value = [];
    } finally {
        sitesLoading.value = false;
    }
};

const dataExpanded = ref(isDataRoute.value);

watch(isDataRoute, (v) => {
    if (v) {
        dataExpanded.value = true;
        loadSites();
    }
});

const currentSiteId = computed(() => {
    if (!isDataRoute.value) return null;
    const raw = route.params.site_id;
    if (raw == null) return null;
    const n = Number(raw);
    return Number.isFinite(n) ? n : raw;
});

const go = (name) => router.push({ name });
const goSite = (siteId) => router.push({ name: 'dashboard-data-site', params: { site_id: siteId } });

const toggleData = () => {
    dataExpanded.value = !dataExpanded.value;
    if (dataExpanded.value) {
        loadSites();
        if (!isDataRoute.value) go('dashboard-data');
    }
};

onMounted(() => {
    if (dataExpanded.value) loadSites();
});
</script>

<template>
    <nav class="nav nav-pills flex-column py-3 px-2">
        <button
            type="button"
            class="nav-link text-start mb-1"
            :class="{ active: isActive('dashboard') }"
            @click="go('dashboard')"
        >
            Dashboard
        </button>

        <button
            type="button"
            class="nav-link text-start mb-1"
            :class="{ active: isActive(['dashboard-sites', 'dashboard-site-view', 'dashboard-site-edit', 'dashboard-site-add']) }"
            @click="go('dashboard-sites')"
        >
            Sites
        </button>

        <div class="mb-1">
            <button
                type="button"
                class="nav-link text-start w-100 d-flex align-items-center justify-content-between"
                :class="{ active: isDataRoute && !currentSiteId }"
                @click="toggleData"
            >
                <span>Data</span>
                <span class="small opacity-50">{{ dataExpanded ? '▾' : '▸' }}</span>
            </button>

            <div v-if="dataExpanded" class="ps-3 mt-1 d-flex flex-column gap-1">
                <div v-if="sitesLoading && sites.length === 0" class="small text-muted px-2 py-1">
                    Loading sites…
                </div>
                <div v-else-if="sites.length === 0" class="small text-muted px-2 py-1">
                    No sites
                </div>
                <button
                    v-for="site in sites"
                    :key="site.id"
                    type="button"
                    class="nav-link text-start small text-truncate"
                    :class="{ active: currentSiteId === site.id }"
                    :title="site.name"
                    @click="goSite(site.id)"
                >
                    {{ site.name }}
                </button>
            </div>
        </div>
    </nav>
</template>
