<script setup>
    import { ref, onMounted } from 'vue';
    import axios from 'axios';
    import { useRouter } from 'vue-router';
    import DashboardLayout from '../components/layout/DashboardLayout.vue';
    
    const props = defineProps({
      site_id: {
        type: [String, Number],
        required: true,
      },
    });
    
    const router = useRouter();
    const site = ref(null);
    const loading = ref(true);
    const error = ref(null);
    
    const loadSite = async () => {
      loading.value = true;
      error.value = null;
    
      try {
        const { data } = await axios.get(`/api/sites/view/${props.site_id}`);
        site.value = data.data;
      } catch (e) {
        console.error('Failed to load site', e);
        error.value = 'Failed to load site data.';
        site.value = null;
      } finally {
        loading.value = false;
      }
    };
    
    const goBack = () => {
      router.push({ name: 'dashboard-sites' });
    };
    
    onMounted(loadSite);
    </script>
    
    <template>
      <DashboardLayout title="Site details">
        <div v-if="loading" class="text-muted">
          Loading...
        </div>
    
        <div v-else>
          <button
            type="button"
            class="btn btn-link px-0 mb-3"
            @click="goBack"
          >
            ← Back to sites
          </button>
    
          <div v-if="error" class="alert alert-danger">
            {{ error }}
          </div>
    
          <div v-else-if="site">
            <div class="mb-2">
              <span class="fw-semibold">ID:</span>
              <span class="ms-2">{{ site.id }}</span>
            </div>
    
            <div class="mb-2">
              <span class="fw-semibold">Name:</span>
              <span class="ms-2">{{ site.name }}</span>
            </div>
    
            <div class="mb-2">
              <span class="fw-semibold">URL:</span>
              <a
                class="ms-2 text-decoration-none"
                :href="site.url"
                target="_blank"
                rel="noopener noreferrer"
              >
                {{ site.url }}
              </a>
            </div>
    
            <div class="mb-2">
              <span class="fw-semibold">Token:</span>
              <span class="ms-2 font-monospace">{{ site.token }}</span>
            </div>
          </div>
    
          <div v-else class="text-muted">
            Site not found.
          </div>
        </div>
      </DashboardLayout>
    </template>
    