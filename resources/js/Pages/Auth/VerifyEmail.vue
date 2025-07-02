<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>

    <Head title="Verificación de Email" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>
        <div
            class="mb-6 text-sm text-gray-800 dark:text-gray-100 bg-gradient-to-r from-indigo-50/80 via-blue-50/80 to-purple-50/80 dark:from-gray-800/90 dark:via-gray-800/90 dark:to-gray-700/90 p-5 rounded-xl border border-white/20 dark:border-gray-600/30 shadow-[0_4px_15px_-5px_rgba(99,102,241,0.15)] dark:shadow-[0_4px_15px_-5px_rgba(0,0,0,0.3)] backdrop-blur-sm">
            Antes de continuar, ¿podría verificar su dirección de correo electrónico haciendo clic en el enlace que le
            enviamos?
            Si no lo recibió, con gusto le enviaremos otro.
        </div>
        <div v-if="verificationLinkSent"
            class="mb-6 font-medium text-sm text-green-700 dark:text-green-100 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/20 p-4 rounded-xl border border-green-100/70 dark:border-green-800/50 shadow-[inset_0_2px_4px_0_rgba(34,197,94,0.05)] dark:shadow-[inset_0_2px_4px_0_rgba(0,0,0,0.1)]">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500 dark:text-green-300" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Nuevo enlace de verificación enviado a tu correo
            </div>
        </div>

        <form @submit.prevent="submit">
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-5">
                <PrimaryButton :class="{ 'opacity-80': form.processing }" :disabled="form.processing"
                    class="relative w-full sm:w-auto group overflow-hidden bg-gradient-to-br from-emerald-500 via-teal-500 to-green-500 dark:from-emerald-600 dark:via-teal-600 dark:to-green-600 hover:shadow-lg hover:shadow-emerald-500/30 dark:hover:shadow-teal-500/20 transition-all duration-500 ease-out hover:scale-[1.02] focus:scale-[0.98] transform-gpu border-0">
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                        Reenviar Email de Verificación
                    </span>
                    <span
                        class="absolute inset-0 bg-gradient-to-br from-white/20 to-white/0 group-hover:from-white/30 group-hover:to-white/10 transition-all duration-300"></span>
                </PrimaryButton>
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <Link :href="route('profile.show')"
                        class="relative text-center text-sm px-4 py-2 rounded-lg bg-white/80 dark:bg-gray-800/80 hover:bg-white dark:hover:bg-gray-700/90 text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 border border-gray-200/80 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-500/60 shadow-sm hover:shadow-indigo-100/50 dark:hover:shadow-none transition-all duration-300 group">
                    <span class="relative z-10 flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Editar Perfil
                    </span>
                    </Link>

                    <Link :href="route('logout')" method="post" as="button"
                        class="relative text-center text-sm px-4 py-2 rounded-lg bg-white/80 dark:bg-gray-800/80 hover:bg-white dark:hover:bg-gray-700/90 text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 border border-gray-200/80 dark:border-gray-700 hover:border-rose-300 dark:hover:border-rose-500/60 shadow-sm hover:shadow-rose-100/50 dark:hover:shadow-none transition-all duration-300 group">
                    <span class="relative z-10 flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        Cerrar Sesión
                    </span>
                    </Link>
                </div>
            </div>
        </form>
    </AuthenticationCard>
</template>