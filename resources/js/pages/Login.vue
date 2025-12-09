<script setup>
    import { ref } from 'vue';
    import axios from 'axios';
    import { useRouter } from 'vue-router';
    import AuthLayout from '../components/layout/AuthLayout.vue';
    
    const router = useRouter();
    
    const form = ref({
        email: '',
        password: '',
        remember: false,
    });
    
    const errors = ref(null);
    const loading = ref(false);
    
    const submit = async () => {
        loading.value = true;
        errors.value = null;
    
        try {
            await axios.get('/sanctum/csrf-cookie');
            await axios.post('/api/login', form.value);
    
            await router.push({ name: 'dashboard' });
        } catch (error) {
            if (error.response?.status === 422) {
                errors.value = error.response.data.errors || {
                    general: [error.response.data.message],
                };
            } else {
                errors.value = { general: ['Unexpected error.'] };
            }
        } finally {
            loading.value = false;
        }
    };
    </script>
    
    <template>
        <AuthLayout title="Login">
            <form @submit.prevent="submit">
                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="form-control"
                        required
                    />
    
                    <!-- RED ERROR -->
                    <div v-if="errors?.email" class="text-danger fw-bold mt-1">
                        {{ errors.email[0] }}
                    </div>
                </div>
    
                <!-- Password -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Password</label>
                    <input
                        v-model="form.password"
                        type="password"
                        class="form-control"
                        required
                    />
    
                    <!-- RED ERROR -->
                    <div v-if="errors?.password" class="text-danger fw-bold mt-1">
                        {{ errors.password[0] }}
                    </div>
                </div>
    
                <!-- Remember -->
                <div class="mb-3 form-check">
                    <input
                        id="remember"
                        type="checkbox"
                        v-model="form.remember"
                        class="form-check-input"
                    />
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>
    
                <!-- GENERAL ERROR BLOCK -->
                <div v-if="errors?.general" class="alert alert-danger mt-3">
                    {{ errors.general[0] }}
                </div>
    
                <!-- Submit -->
                <button
                    type="submit"
                    class="btn btn-primary w-100 mt-2"
                    :disabled="loading"
                >
                    <span v-if="!loading">Login</span>
                    <span v-else>Loading...</span>
                </button>
            </form>
        </AuthLayout>
    </template>
    