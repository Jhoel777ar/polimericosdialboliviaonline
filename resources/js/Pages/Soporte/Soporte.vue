<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import Swal from 'sweetalert2';
import { Inertia } from '@inertiajs/inertia';

export default {
    props: {
        soportes: Array,
        puedeCrearSoporte: Boolean,
        flash: Object,
    },
    components: {
        AppLayout
    },
    data() {
        return {
            nuevoSoporte: {
                subject: '',
                description: ''
            }
        };
    },
    watch: {
        flash: {
            handler(newFlash) {
                if (newFlash && newFlash.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: newFlash.success
                    });
                }
                if (newFlash && newFlash.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: newFlash.error
                    });
                }
            },
            immediate: true
        }
    },
    methods: {
        statusClass(status) {
            return status === 'resolved' ? 'text-green-500' : 'text-yellow-500';
        },
        async updateSoporte(id) {
            try {
                const soporte = this.soportes.find(s => s.id === id);
                await Inertia.put(`/soporte/${id}`, {
                    subject: soporte.subject,
                    description: soporte.description
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al actualizar el soporte.'
                });
            }
        },
        async createSoporte() {
            try {
                await Inertia.post('/soporte', {
                    subject: this.nuevoSoporte.subject,
                    description: this.nuevoSoporte.description
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al crear el soporte.'
                });
            }
        },
        async deleteSoporte(id) {
            try {
                const result = await Swal.fire({
                    title: '¿Estás seguro?',
                    text: "No podrás revertir esta acción",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                });

                if (result.isConfirmed) {
                    await Inertia.delete(`/soporte/${id}`);
                    Swal.fire('Eliminado', 'El soporte ha sido eliminado.', 'success');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al eliminar el soporte.'
                });
            }
        }
    }
};
</script>
<template>
    <AppLayout>
        <template #default>
            <div
                class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-blue-900 p-4 sm:p-8 relative overflow-hidden">
                <div
                    class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-blue-900/20 via-transparent to-transparent opacity-30 animate-pulse">
                </div>
                <div class="max-w-7xl mx-auto space-y-12 relative z-10">
                    <div class="group relative">
                        <div
                            class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-cyan-500 rounded-xl blur opacity-30 group-hover:opacity-50 transition-all duration-500">
                        </div>
                        <h1
                            class="text-5xl md:text-6xl font-black mb-4 bg-gradient-to-r from-blue-400 via-cyan-300 to-blue-500 bg-clip-text text-transparent animate-gradient-x">
                            Mis Soportes
                        </h1>
                        <p class="text-gray-400/80 text-lg max-w-2xl leading-relaxed backdrop-blur-sm">
                            🚀 Gestiona todas tus solicitudes de soporte técnico. Crea nuevos tickets,
                            revisa respuestas y actualiza consultas con nuestra interfaz de última generación.
                        </p>
                    </div>
                    <div
                        class="border-l-4 border-cyan-400/40 bg-gradient-to-r from-blue-900/30 to-transparent p-4 rounded-r-xl backdrop-blur-lg shadow-2xl shadow-blue-900/20">
                        <p class="text-sm text-cyan-400/90 font-medium flex items-center gap-2">
                            <span class="text-lg">💡</span> Tus mensajes se manejan con seguridad máxima. Actualiza
                            contenido,
                            asuntos o descripciones directamente en tiempo real.
                        </p>
                    </div>
                    <div v-if="puedeCrearSoporte"
                        class="bg-gray-800/20 backdrop-blur-xl border border-gray-600/30 rounded-2xl p-8 shadow-2xl shadow-blue-900/20 hover:shadow-blue-900/30 transition-all">
                        <div class="space-y-6">
                            <div class="mb-2">
                                <h3
                                    class="text-2xl font-bold bg-gradient-to-r from-cyan-400 to-blue-300 bg-clip-text text-transparent">
                                    Nuevo Ticket de Soporte
                                </h3>
                                <p class="text-gray-400/80 text-sm mt-1 backdrop-blur-sm">
                                    Completa los detalles para generar un nuevo ticket en nuestro sistema inteligente
                                </p>
                            </div>
                            <form @submit.prevent="createSoporte" class="space-y-6">
                                <input v-model="nuevoSoporte.subject"
                                    class="w-full bg-gray-900/40 backdrop-blur-sm border-2 border-gray-600/30 focus:border-cyan-500/50 rounded-xl px-5 py-3.5 text-gray-100 placeholder-gray-400/60 focus:ring-0 focus:outline-none transition-all duration-300 hover:border-cyan-500/30"
                                    placeholder="Asunto principal...">
                                <textarea v-model="nuevoSoporte.description"
                                    class="w-full bg-gray-900/40 backdrop-blur-sm border-2 border-gray-600/30 focus:border-cyan-500/50 rounded-xl px-5 py-3.5 text-gray-100 placeholder-gray-400/60 focus:ring-0 focus:outline-none transition-all duration-300 hover:border-cyan-500/30 h-32"
                                    placeholder="Describe tu problema con detalle..."></textarea>
                                <button type="submit"
                                    class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-[1.02] shadow-2xl shadow-cyan-500/30 hover:shadow-cyan-500/40 w-full flex items-center justify-center gap-2">
                                    <span class="text-xl">⚡</span> Crear Nuevo Soporte
                                </button>
                            </form>
                        </div>
                    </div>
                    <div v-else class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                        <div v-for="soporte in soportes" :key="soporte.id"
                            class="bg-gray-800/30 backdrop-blur-lg border border-gray-600/20 hover:border-cyan-500/30 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 group hover:-translate-y-1 relative">
                            <div
                                class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 to-blue-600/10 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>

                            <div class="space-y-5 relative z-10">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-100 truncate max-w-[200px]">
                                            {{ soporte.subject }}
                                        </h2>
                                        <p class="text-xs text-gray-400/80 mt-1 flex items-center gap-1">
                                            <span class="text-cyan-500">📅</span> {{ soporte.consultation_date }}
                                        </p>
                                    </div>
                                    <span
                                        class="text-[11px] font-semibold uppercase px-3 py-1 rounded-full bg-gradient-to-r from-cyan-600 to-blue-600 text-gray-100 shadow-md shadow-cyan-500/20">
                                        {{ soporte.status }}
                                    </span>
                                </div>
                                <div class="space-y-4">
                                    <p class="text-gray-300/90 leading-relaxed text-sm line-clamp-3 backdrop-blur-sm">
                                        {{ soporte.description }}
                                    </p>
                                    <div class="relative group pt-4 mt-4">
                                        <div
                                            class="absolute inset-x-0 -top-px h-px bg-gradient-to-r from-cyan-500/0 via-cyan-500/70 to-cyan-500/0 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                        </div>
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="relative">
                                                <div
                                                    class="absolute -inset-2 bg-cyan-500/20 blur-xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                </div>
                                                <span class="text-2xl animate-float">💬</span>
                                            </div>
                                            <h3
                                                class="text-transparent bg-gradient-to-r from-cyan-400 via-blue-300 to-cyan-500 bg-clip-text font-bold text-sm uppercase tracking-widest">
                                                Respuesta del Soporte
                                            </h3>
                                        </div>
                                        <div
                                            class="relative overflow-hidden rounded-xl bg-gradient-to-br from-gray-800/40 to-gray-900/20 backdrop-blur-2xl border border-cyan-500/20 hover:border-cyan-500/30 transition-all duration-300">
                                            <div
                                                class="absolute left-0 top-0 w-1 h-full bg-gradient-to-b from-cyan-500/50 to-transparent">
                                            </div>
                                            <div class="p-4 space-y-3">
                                                <p v-if="soporte.respuesta"
                                                    class="text-gray-300/90 text-sm leading-relaxed font-medium animate-fade-in"
                                                    style="text-shadow: 0 2px 8px rgba(34, 211, 238, 0.1)">
                                                    {{ soporte.respuesta }}
                                                </p>
                                                <div v-else class="flex items-center gap-3">
                                                    <div class="relative flex items-center justify-center">
                                                        <div
                                                            class="absolute h-4 w-4 bg-cyan-500/20 rounded-full animate-ping">
                                                        </div>
                                                        <div
                                                            class="h-2 w-2 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-full animate-pulse">
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="text-sm font-medium text-cyan-400/90 animate-pulse-slow">
                                                        Procesando tu solicitud...
                                                    </span>
                                                </div>
                                            </div>
                                            <div
                                                class="absolute inset-0 bg-[radial-gradient(400px_at_50%_120%,rgba(34,211,238,0.1),transparent)] opacity-0 group-hover:opacity-30 transition-opacity duration-300">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <form @submit.prevent="updateSoporte(soporte.id)" class="space-y-4 mt-6">
                                    <input v-model="soporte.subject"
                                        class="w-full bg-gray-700/20 backdrop-blur-sm border border-gray-600/30 focus:border-cyan-500/30 rounded-lg px-4 py-2.5 text-gray-100 placeholder-gray-400/50 text-sm focus:ring-0 focus:outline-none transition-all duration-300">
                                    <textarea v-model="soporte.description"
                                        class="w-full bg-gray-700/20 backdrop-blur-sm border border-gray-600/30 focus:border-cyan-500/30 rounded-lg px-4 py-2.5 text-gray-100 placeholder-gray-400/50 text-sm focus:ring-0 focus:outline-none transition-all duration-300 h-24"></textarea>
                                    <div class="flex gap-3 pt-4">
                                        <button type="submit"
                                            class="bg-gradient-to-r from-cyan-600/90 to-blue-700/90 hover:from-cyan-500/90 hover:to-blue-600/90 text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-300 w-full flex items-center justify-center gap-2">
                                            <span class="text-sm">🔄</span> Actualizar
                                        </button>
                                        <button @click="deleteSoporte(soporte.id)"
                                            class="bg-gradient-to-r from-red-600/90 to-pink-700/90 hover:from-red-500/90 hover:to-pink-600/90 text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-300 flex items-center justify-center gap-2">
                                            <span class="text-sm">🗑️</span> Eliminar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="text-center pt-12 pb-6">
                        <p class="text-sm text-gray-400/80 backdrop-blur-sm">
                            ¿Necesitas ayuda inmediata?
                            <a href="#"
                                class="text-cyan-400/90 hover:text-cyan-300 transition-all duration-200 flex items-center justify-center gap-2">
                                <span class="text-xl">💬</span> Contacta a nuestro equipo por WhatsApp
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            <footer class="relative py-8 text-center overflow-hidden mt-12">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-gray-900/95 via-slate-900/95 to-black backdrop-blur-2xl">
                </div>
                <div class="relative z-10 flex flex-col items-center space-y-3">
                    <p
                        class="text-xl sm:text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-400 animate-gradient-x">
                        Poliméricos Dial Bolivia
                    </p>
                    <p
                        class="text-sm sm:text-base font-medium text-gray-300/90 hover:text-gray-100 transition-all duration-300">
                        &copy; {{ new Date().getFullYear() }} Innovación en Soluciones Industriales
                    </p>
                    <div
                        class="absolute top-0 w-full h-px bg-gradient-to-r from-transparent via-cyan-400/50 to-transparent">
                    </div>
                    <div
                        class="absolute -top-20 left-0 w-48 h-48 bg-gradient-to-r from-cyan-600/20 to-blue-600/20 rounded-full blur-3xl opacity-30">
                    </div>
                </div>
            </footer>
        </template>
    </AppLayout>
</template>