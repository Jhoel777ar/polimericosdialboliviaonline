<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 via-indigo-950 to-teal-900 p-4">
        <div class="relative w-full max-w-md p-8 bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-700/50 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 via-purple-500/10 to-pink-500/10 animate-gradient-bg opacity-30"></div>
            <div class="relative z-10 mb-8 flex justify-center">
                <AuthenticationCardLogo class="w-24 h-24 hover:scale-110 transition-transform duration-300" />
            </div>
            <div v-if="status" class="relative z-10 mb-6 text-center text-sm font-medium text-green-400 animate-pulse">
                {{ status }}
            </div>
            <form @submit.prevent="submit" class="relative z-10 space-y-6">
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-200">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full px-4 py-3 rounded-xl bg-gray-900/70 border border-gray-600/50 text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 transition-all duration-300 hover:bg-gray-900/90 backdrop-blur-md"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Ingresa tu correo electrónico"
                    />
                    <p v-if="form.errors.email" class="mt-2 text-sm text-red-400">{{ form.errors.email }}</p>
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-200">Contraseña</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full px-4 py-3 rounded-xl bg-gray-900/70 border border-gray-600/50 text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 transition-all duration-300 hover:bg-gray-900/90 backdrop-blur-md"
                        required
                        autocomplete="current-password"
                        placeholder="Ingresa tu contraseña"
                    />
                    <p v-if="form.errors.password" class="mt-2 text-sm text-red-400">{{ form.errors.password }}</p>
                </div>
                <div class="flex items-center">
                    <input
                        v-model="form.remember"
                        type="checkbox"
                        name="remember"
                        class="w-4 h-4 text-cyan-400 bg-gray-900 border-gray-600 rounded focus:ring-cyan-400 focus:ring-2"
                    />
                    <label class="ml-2 text-sm text-gray-200">Acuérdate de mí</label>
                </div>
                <div class="flex items-center justify-between mt-6">
                    <a
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-teal-400 hover:text-teal-300 underline transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:ring-offset-2 focus:ring-offset-gray-900"
                    >
                        ¿Olvidaste tu contraseña?
                    </a>
                    <button
                        type="submit"
                        class="ml-4 px-6 py-3 bg-gradient-to-r from-cyan-500 to-teal-500 text-white font-semibold rounded-lg shadow-md hover:from-cyan-600 hover:to-teal-600 focus:ring-2 focus:ring-cyan-400 focus:ring-offset-2 focus:ring-offset-gray-900 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión
                    </button>
                </div>
            </form>
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
