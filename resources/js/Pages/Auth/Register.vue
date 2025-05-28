<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
    recaptcha_token: '',
});
//Captcha
const submit = async () => {
    form.processing = true;
    if (!window.grecaptcha) {
        console.error('reCAPTCHA no está disponible.');
        form.processing = false;
        return;
    }
    try {
        await grecaptcha.ready(async () => {
            const token = await grecaptcha.execute(import.meta.env.VITE_RECAPTCHA_SITE_KEY, { action: 'register' });
            form.recaptcha_token = token;
            form.post(route('register'), {
                onFinish: () => form.reset('password', 'password_confirmation'),
            });
        });
    } catch (error) {
        console.error('Error obteniendo el token de reCAPTCHA:', error);
        form.processing = false;
    }
};
</script>

<template>
    <Head title="Register" />

    <!-- Contenedor Principal -->
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 via-purple-950 to-teal-900 p-4">
        <div class="relative w-full max-w-lg p-8 bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-700/50 overflow-hidden">
            <!-- Fondo Dinámico -->
            <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 via-purple-500/10 to-pink-500/10 animate-gradient-bg opacity-30"></div>

            <!-- Logo -->
            <div class="relative z-10 mb-8 flex justify-center">
                <AuthenticationCardLogo class="w-24 h-24 hover:scale-110 transition-transform duration-300" />
            </div>

            <!-- Formulario -->
            <form @submit.prevent="submit" class="relative z-10 space-y-6">
                <!-- Campo Nombre -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-200">Nombre</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full px-4 py-3 rounded-xl bg-gray-900/70 border border-gray-600/50 text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 transition-all duration-300 hover:bg-gray-900/90 backdrop-blur-md"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Ingresa tu Nombre y Apellido"
                    />
                    <p v-if="form.errors.name" class="mt-2 text-sm text-red-400">{{ form.errors.name }}</p>
                </div>

                <!-- Campo Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-200">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full px-4 py-3 rounded-xl bg-gray-900/70 border border-gray-600/50 text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 transition-all duration-300 hover:bg-gray-900/90 backdrop-blur-md"
                        required
                        autocomplete="username"
                        placeholder="Ingresa tu correo electrónico"
                    />
                    <p v-if="form.errors.email" class="mt-2 text-sm text-red-400">{{ form.errors.email }}</p>
                </div>

                <!-- Campo Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-200">Contraseña</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full px-4 py-3 rounded-xl bg-gray-900/70 border border-gray-600/50 text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 transition-all duration-300 hover:bg-gray-900/90 backdrop-blur-md"
                        required
                        autocomplete="new-password"
                        placeholder="Ingresa tu nueva contraseña"
                    />
                    <p v-if="form.errors.password" class="mt-2 text-sm text-red-400">{{ form.errors.password }}</p>
                </div>

                <!-- Campo Confirmar Contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-200">Confirmar Contraseña</label>
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-1 block w-full px-4 py-3 rounded-xl bg-gray-900/70 border border-gray-600/50 text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 transition-all duration-300 hover:bg-gray-900/90 backdrop-blur-md"
                        required
                        autocomplete="new-password"
                        placeholder="Confirma tu nueva contraseña"
                    />
                    <p v-if="form.errors.password_confirmation" class="mt-2 text-sm text-red-400">{{ form.errors.password_confirmation }}</p>
                </div>

                <!-- Checkbox de Términos (si aplica) -->
                <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature">
                    <div class="flex items-center">
                        <input
                            id="terms"
                            v-model="form.terms"
                            type="checkbox"
                            name="terms"
                            class="w-4 h-4 text-purple-400 bg-gray-900 border-gray-600 rounded focus:ring-purple-400 focus:ring-2"
                            required
                        />
                        <label for="terms" class="ml-2 text-sm text-gray-200">
                            Estoy de acuerdo con los
                            <a
                                target="_blank"
                                :href="route('terms.show')"
                                class="text-purple-400 hover:text-purple-300 underline transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 focus:ring-offset-gray-900"
                            >
                                Términos de servicio
                            </a>
                            y
                            <a
                                target="_blank"
                                :href="route('policy.show')"
                                class="text-purple-400 hover:text-purple-300 underline transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 focus:ring-offset-gray-900"
                            >
                                Política de Privacidad
                            </a>
                        </label>
                    </div>
                    <p v-if="form.errors.terms" class="mt-2 text-sm text-red-400">{{ form.errors.terms }}</p>
                </div>

                <!-- Botones y Enlace -->
                <div class="flex items-center justify-end mt-6">
                    <Link
                        :href="route('login')"
                        class="text-sm text-teal-400 hover:text-teal-300 underline transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:ring-offset-2 focus:ring-offset-gray-900"
                    >
                        ¿Ya estás registrado?
                    </Link>
                    <button
                        type="submit"
                        class="ml-4 px-6 py-3 bg-gradient-to-r from-purple-500 to-teal-500 text-white font-semibold rounded-lg shadow-md hover:from-purple-600 hover:to-teal-600 focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 focus:ring-offset-gray-900 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                    >
                        <i class="fa fa-user-plus mr-2"></i> Registrarse
                    </button>
                </div>
            </form>

            <!-- Botón Google -->
            <div class="relative z-10 mt-6 flex justify-center">
                <a
                    :href="route('google.login')"
                    class="flex items-center px-6 py-3 bg-white/90 text-gray-800 font-semibold rounded-lg shadow-md hover:bg-white focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-gray-900 transition-all duration-300 transform hover:scale-105"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        preserveAspectRatio="xMidYMid"
                        viewBox="0 0 256 262"
                        class="w-6 h-6 mr-2"
                    >
                        <path fill="#4285F4" d="M255.878 133.451c0-10.734-.871-18.567-2.756-26.69H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622 38.755 30.023 2.685.268c24.659-22.774 38.875-56.282 38.875-96.027" />
                        <path fill="#34A853" d="M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055-34.523 0-63.824-22.773-74.269-54.25l-1.531.13-40.298 31.187-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1" />
                        <path fill="#FBBC05" d="M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82 0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602l42.356-32.782" />
                        <path fill="#EB4335" d="M130.55 50.479c24.514 0 41.05 10.589 50.479 19.438l36.844-35.974C195.245 12.91 165.798 0 130.55 0 79.49 0 35.393 29.301 13.925 71.947l42.211 32.783c10.59-31.477 39.891-54.251 74.414-54.251" />
                    </svg>
                    <span>Continuar con Google</span>
                </a>
            </div>
        </div>
    </div>
</template>
<style>
.animate-gradient-bg {
    background-size: 200% 200%;
    animation: gradientBG 10s ease infinite;
}
@keyframes gradientBG {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
</style>
