<script setup>
    import { ref, onMounted } from 'vue';
    import axios from 'axios';
    import { useRouter } from 'vue-router';
    import DashboardHeader from './DashboardHeader.vue';
    import DashboardSidebar from './DashboardSidebar.vue';
    
    const props = defineProps({
      title: {
        type: String,
        default: 'Dashboard',
      },
    });
    
    const router = useRouter();
    const user = ref(null);
    const loadingUser = ref(true);
    
    const loadUser = async () => {
      loadingUser.value = true;
      try {
        const { data } = await axios.get('/api/user');
        user.value = data;
      } catch (e) {
        user.value = null;
        router.push({ name: 'login' });
      } finally {
        loadingUser.value = false;
      }
    };
    
    const logout = async () => {
      try {
        await axios.post('/api/logout');
      } catch (e) {
        // ignore
      } finally {
        router.push({ name: 'login' });
      }
    };
    
    onMounted(loadUser);
    </script>
    
    <template>
      <div class="container-fluid min-vh-100 d-flex flex-column p-0">
        <DashboardHeader :title="title" :user="user" @logout="logout" />
    
        <div class="row flex-grow-1 m-0">
          <div class="col-12 col-md-3 col-lg-2 bg-light border-end p-0">
            <DashboardSidebar />
          </div>
    
          <div class="col-12 col-md-9 col-lg-10 p-4">
            <div v-if="loadingUser" class="text-muted">
              Loading...
            </div>
            <div v-else>
              <slot />
            </div>
          </div>
        </div>
      </div>
    </template>
    