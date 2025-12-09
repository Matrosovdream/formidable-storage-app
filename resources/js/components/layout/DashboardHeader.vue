<script setup>
    const props = defineProps({
      title: {
        type: String,
        default: 'Dashboard',
      },
      user: {
        type: Object,
        default: null,
      },
    });
    
    const emit = defineEmits(['logout']);
    
    const onLogoutClick = () => {
      emit('logout');
    };
    </script>
    
    <template>
      <header class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-3">
        <div class="container-fluid">
          <span class="navbar-brand fw-bold">{{ title }}</span>
    
          <div class="ms-auto d-flex align-items-center gap-3">
            <span v-if="user" class="text-muted small me-3">
              Logged in as
              <strong>{{ user.name }}</strong>
              <span v-if="user.email"> ({{ user.email }})</span>
            </span>
    
            <button
              type="button"
              class="btn btn-outline-danger btn-sm"
              @click="onLogoutClick"
            >
              Logout
            </button>
          </div>
        </div>
      </header>
    </template>
    