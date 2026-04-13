<script setup>
  import { useRoute, useRouter } from 'vue-router';
  
  const route = useRoute();
  const router = useRouter();
  
  const isActive = (names) => {
      if (Array.isArray(names)) {
          return names.includes(route.name);
      }
      return route.name === names;
  };
  
  const go = (name) => {
      router.push({ name });
  };
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

          <button
              type="button"
              class="nav-link text-start mb-1"
              :class="{ active: isActive(['dashboard-data', 'dashboard-data-entry']) }"
              @click="go('dashboard-data')"
          >
              Data
          </button>
      </nav>
  </template>
  