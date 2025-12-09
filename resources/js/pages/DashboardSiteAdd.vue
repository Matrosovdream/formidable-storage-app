<script setup>
    import { ref } from 'vue';
    import axios from 'axios';
    import { useRouter } from 'vue-router';
    import DashboardLayout from '../components/layout/DashboardLayout.vue';
    
    const router = useRouter();
    
    const name = ref('');
    const url = ref('');
    const loading = ref(false);
    const error = ref(null);
    const success = ref(null);
    const urlError = ref(null); // JS URL validation error
    
    const isValidUrl = (value) => {
        if (!value) return false;
    
        try {
            const u = new URL(value);
            // Require http or https
            return u.protocol === 'http:' || u.protocol === 'https:';
        } catch (e) {
            return false;
        }
    };
    
    const submitForm = async () => {
        loading.value = true;
        error.value = null;
        success.value = null;
        urlError.value = null;
    
        // JS URL validation
        if (!isValidUrl(url.value)) {
            loading.value = false;
            urlError.value = 'Please enter a valid URL starting with http:// or https://';
            return;
        }
    
        try {
            await axios.post('/api/sites/store/', {
                name: name.value,
                url: url.value,
            });
    
            success.value = 'Site created successfully.';
    
            setTimeout(() => {
                router.push({ name: 'dashboard-sites' });
            }, 800);
        } catch (e) {
            console.error(e);
            error.value = 'Failed to create site.';
        } finally {
            loading.value = false;
        }
    };
    
    const goBack = () => {
        router.push({ name: 'dashboard-sites' });
    };
    
    // Clear URL error when user edits field
    const onUrlInput = () => {
        if (urlError.value) {
            urlError.value = null;
        }
    };
    </script>
    
    <template>
        <DashboardLayout title="Add site">
            <button
                type="button"
                class="btn btn-link px-0 mb-3"
                @click="goBack"
            >
                ← Back to sites
            </button>
    
            <div class="site-add-wrapper">
                <h2 class="h5 mb-3 text-center">Add new site</h2>
    
                <div v-if="error" class="alert alert-danger">{{ error }}</div>
                <div v-if="success" class="alert alert-success">{{ success }}</div>
    
                <form @submit.prevent="submitForm" class="mt-3">
                    <div class="mb-3">
                        <label class="form-label">Site name</label>
                        <input
                            v-model="name"
                            type="text"
                            class="form-control"
                            required
                            placeholder="Enter site name"
                        />
                    </div>
    
                    <div class="mb-3">
                        <label class="form-label">Site URL</label>
                        <input
                            v-model="url"
                            type="text"
                            class="form-control"
                            required
                            placeholder="https://example.com"
                            @input="onUrlInput"
                        />
                        <div v-if="urlError" class="text-danger fw-bold mt-1">
                            {{ urlError }}
                        </div>
                    </div>
    
                    <button
                        type="submit"
                        class="btn btn-success w-100"
                        :disabled="loading"
                    >
                        {{ loading ? 'Saving...' : 'Create site' }}
                    </button>
                </form>
            </div>
        </DashboardLayout>
    </template>
    
    <style scoped>
        .site-add-wrapper {
            padding-left: 30%;
            padding-right: 30%;
        }
        
        /* Mobile-friendly */
        @media (max-width: 768px) {
            .site-add-wrapper {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
        
        /* Always-green submit button */
        .btn.btn-success {
            background-color: #198754 !important;
            border-color: #198754 !important;
        }
        
        .btn.btn-success:hover,
        .btn.btn-success:focus,
        .btn.btn-success:active {
            background-color: #198754 !important;
            border-color: #198754 !important;
            box-shadow: none !important;
        }
        </style>
        
    