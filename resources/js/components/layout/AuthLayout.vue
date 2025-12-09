<script setup>
    const props = defineProps({
        title: {
            type: String,
            default: 'Auth',
        },
    });
    </script>
    
    <template>
        <div class="auth-wrapper d-flex align-items-center justify-content-center min-vh-100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-sm-10 col-md-6 col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-body p-4">
                                <h1 class="h4 text-center mb-4">
                                    {{ title }}
                                </h1>
    
                                <!-- page content -->
                                <slot />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
    
    <style scoped>
    .auth-wrapper {
        /* adjust path to your real image location */
        background-image: url('@/assets/media/auth/bg4.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
    </style>
    