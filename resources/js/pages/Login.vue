<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

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
        // Sanctum CSRF cookie
        await axios.get('/sanctum/csrf-cookie');

        await axios.post('/api/login', form.value);

        await router.push({ name: 'dashboard' });
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors || { general: [error.response.data.message] };
        } else {
            errors.value = { general: ['Unexpected error.'] };
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>

        <!--
        <div class="d-flex flex-column flex-root" id="kt_app_root">

			<div class="d-flex flex-column flex-column-fluid flex-lg-row">

				<div class="d-flex flex-center w-lg-50 pt-15 pt-lg-0 px-10">

					<div class="d-flex flex-center flex-lg-start flex-column">

						<a href="index.html" class="mb-7">
							<img alt="Logo" src="@/media/logos/custom-3.svg" />
						</a>

						<h2 class="text-white fw-normal m-0">Branding tools designed for your business</h2>

					</div>

				</div>

				<div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12 p-lg-20">

					<div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-600px p-20">

						<div class="d-flex flex-center flex-column flex-column-fluid px-lg-10 pb-15 pb-lg-20">

							<form class="form w-100" novalidate="novalidate" id="kt_sign_up_form" data-kt-redirect-url="authentication/layouts/creative/sign-in.html" action="#">

								<div class="text-center mb-11">
									<h1 class="text-gray-900 fw-bolder mb-3">Sign Up</h1>
									<div class="text-gray-500 fw-semibold fs-6">Your Social Campaigns</div>
								</div>

								<div class="fv-row mb-8">
									<input type="text" placeholder="Email" name="email" autocomplete="off" class="form-control bg-transparent" />
								</div>

								<div class="fv-row mb-8" data-kt-password-meter="true">

									<div class="mb-1">

										<div class="position-relative mb-3">
											<input class="form-control bg-transparent" type="password" placeholder="Password" name="password" autocomplete="off" />
											<span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
												<i class="ki-duotone ki-eye-slash fs-2"></i>
												<i class="ki-duotone ki-eye fs-2 d-none"></i>
											</span>
										</div>

										<div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
											<div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
											<div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
											<div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
											<div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
										</div>

									</div>

									<div class="text-muted">Use 8 or more characters with a mix of letters, numbers & symbols.</div>

								</div>

								<div class="fv-row mb-8">
									<input placeholder="Repeat Password" name="confirm-password" type="password" autocomplete="off" class="form-control bg-transparent" />
								</div>

								<div class="fv-row mb-8">
									<label class="form-check form-check-inline">
										<input class="form-check-input" type="checkbox" name="toc" value="1" />
										<span class="form-check-label fw-semibold text-gray-700 fs-base ms-1">I Accept the 
										<a href="#" class="ms-1 link-primary">Terms</a></span>
									</label>
								</div>

								<div class="d-grid mb-10">
									<button type="submit" id="kt_sign_up_submit" class="btn btn-primary">
										<span class="indicator-label">Sign up</span>
										<span class="indicator-progress">Please wait... 
										<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
									</button>
								</div>
								<div class="text-gray-500 text-center fw-semibold fs-6">Already have an Account? 
								<a href="authentication/layouts/creative/sign-in.html" class="link-primary fw-semibold">Sign in</a></div>

							</form>

						</div>

						<div class="d-flex flex-stack px-lg-10">

							<div class="me-0">
							</div>

							<div class="d-flex fw-semibold text-primary fs-base gap-5">
								<a href="pages/team.html" target="_blank">Terms</a>
								<a href="pages/pricing/column.html" target="_blank">Plans</a>
								<a href="pages/contact.html" target="_blank">Contact Us</a>
							</div>

						</div>

					</div>

				</div>

			</div>

		</div>
        -->

    
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="w-full max-w-md bg-white shadow-md rounded px-8 py-6">
            <h1 class="text-2xl font-bold mb-4 text-center">Login</h1>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Email
                    </label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                        required
                    />
                    <div v-if="errors?.email" class="text-red-500 text-xs mt-1">
                        {{ errors.email[0] }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Password
                    </label>
                    <input
                        v-model="form.password"
                        type="password"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"
                        required
                    />
                    <div v-if="errors?.password" class="text-red-500 text-xs mt-1">
                        {{ errors.password[0] }}
                    </div>
                </div>

                <div class="mb-4 flex items-center">
                    <input
                        id="remember"
                        type="checkbox"
                        v-model="form.remember"
                        class="mr-2"
                    />
                    <label for="remember" class="text-sm text-gray-700">Remember me</label>
                </div>

                <div v-if="errors?.general" class="mb-4 text-red-500 text-sm">
                    {{ errors.general[0] }}
                </div>

                <div class="flex items-center justify-between">
                    <button
                        type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                        :disabled="loading"
                    >
                        <span v-if="!loading">Login</span>
                        <span v-else>Loading...</span>
                    </button>

                    <router-link
                        :to="{ name: 'register' }"
                        class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800"
                    >
                        Register
                    </router-link>
                </div>
            </form>
        </div>
    </div>
    

</template>

<style scoped>
    body { background-image: url('@media/auth/bg4.jpg')!important; }
    /*body { background-image: url('@media/auth/bg4-dark.jpg'); }*/
</style>
