<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';

defineProps({
    policy: String,
});
</script>

<template>

    <Head title="Política de Privacidad y Términos de Uso" />

    <!-- Contenedor principal con fondo degradado -->
    <div
        class="font-sans antialiased text-gray-100 bg-gradient-to-br from-black via-blue-950 to-emerald-900 min-h-screen flex flex-col">
        <!-- Contenido principal -->
        <main class="flex-grow pt-16">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center">
                    <!-- Logo -->
                    <div class="mb-8 hover:scale-110 transition-transform duration-300">
                        <AuthenticationCardLogo class="w-32 h-32" />
                    </div>

                    <!-- Contenedor de contenido -->
                    <div
                        class="w-full max-w-4xl p-8 bg-gray-900/70 backdrop-blur-xl rounded-3xl shadow-2xl border border-blue-800/30">
                        <h1
                            class="text-4xl md:text-5xl font-extrabold text-center mb-10 bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 via-emerald-400 to-blue-500 animate-gradient-text">
                            Política de Privacidad y Términos de Uso
                        </h1>

                        <!-- Sección 1 -->
                        <section class="relative mb-10 group">
                            <div
                                class="absolute -inset-1 bg-gradient-to-r from-cyan-500/20 to-emerald-500/20 rounded-xl blur opacity-20 group-hover:opacity-40 transition-opacity duration-500">
                            </div>
                            <div
                                class="relative bg-gray-800/60 p-6 rounded-xl border border-cyan-800/40 hover:border-emerald-500/40 transition-all duration-300">
                                <h2 class="text-3xl font-semibold text-cyan-400 mb-4">1. Protección de Datos Personales
                                </h2>
                                <p class="text-lg text-gray-300 leading-relaxed">
                                    En <span class="font-bold text-white">Poliméricos Dial Bolivia</span>, tu privacidad
                                    es nuestra prioridad. Protegemos tus datos personales (nombres, correos, CI,
                                    teléfonos) con los más altos estándares de seguridad, utilizándolos únicamente para
                                    gestionar tu cuenta, procesar pedidos y comunicarnos contigo sobre nuestras
                                    operaciones.
                                </p>
                                <p class="text-lg text-gray-300 mt-2">
                                    No compartimos, vendemos ni divulgamos tu información a terceros sin tu
                                    consentimiento, salvo obligación legal.
                                </p>
                            </div>
                        </section>

                        <!-- Sección 2 -->
                        <section class="relative mb-10 group">
                            <div
                                class="absolute -inset-1 bg-gradient-to-r from-purple-500/20 to-blue-500/20 rounded-xl blur opacity-20 group-hover:opacity-40 transition-opacity duration-500">
                            </div>
                            <div
                                class="relative bg-gray-800/60 p-6 rounded-xl border border-purple-800/40 hover:border-blue-500/40 transition-all duration-300">
                                <h2 class="text-3xl font-semibold text-purple-400 mb-4">2. Seguridad y Autenticación
                                </h2>
                                <p class="text-lg text-gray-300 leading-relaxed">
                                    Garantizamos la seguridad de tu cuenta con:
                                </p>
                                <ul class="list-disc list-inside text-gray-300 mt-2 space-y-2">
                                    <li><span class="font-semibold text-white">Autenticación de Dos Pasos (2FA):</span>
                                        Actívala para mayor protección.</li>
                                    <li><span class="font-semibold text-white">Inicio con Google:</span> Acceso rápido y
                                        seguro.</li>
                                </ul>
                                <p class="text-lg text-gray-300 mt-2">
                                    Monitoreamos todas las transacciones para prevenir fraudes y accesos no autorizados.
                                </p>
                            </div>
                        </section>

                        <!-- Sección 3 -->
                        <section class="relative mb-10 group">
                            <div
                                class="absolute -inset-1 bg-gradient-to-r from-pink-500/20 to-purple-500/20 rounded-xl blur opacity-20 group-hover:opacity-40 transition-opacity duration-500">
                            </div>
                            <div
                                class="relative bg-gray-800/60 p-6 rounded-xl border border-pink-800/40 hover:border-purple-500/40 transition-all duration-300">
                                <h2 class="text-3xl font-semibold text-pink-400 mb-4">3. Uso Inadecuado y Sanciones</h2>
                                <p class="text-lg text-gray-300 leading-relaxed">
                                    Prohibimos cualquier actividad indebida, como hackeos, fraudes o manipulación de
                                    datos. Estas acciones resultarán en:
                                </p>
                                <ul class="list-disc list-inside text-gray-300 mt-2 space-y-2">
                                    <li>Suspensión o eliminación inmediata de la cuenta.</li>
                                    <li>Posibles acciones legales si aplica.</li>
                                </ul>
                                <p class="text-lg text-gray-300 mt-2">
                                    Registramos actividades sospechosas para proteger la plataforma y a nuestros
                                    usuarios.
                                </p>
                            </div>
                        </section>

                        <!-- Sección 4 -->
                        <section class="relative mb-10 group">
                            <div
                                class="absolute -inset-1 bg-gradient-to-r from-teal-500/20 to-cyan-500/20 rounded-xl blur opacity-20 group-hover:opacity-40 transition-opacity duration-500">
                            </div>
                            <div
                                class="relative bg-gray-800/60 p-6 rounded-xl border border-teal-800/40 hover:border-cyan-500/40 transition-all duration-300">
                                <h2 class="text-3xl font-semibold text-teal-400 mb-4">4. Procesos de Venta y Envío</h2>
                                <ul class="list-disc list-inside text-gray-300 space-y-3">
                                    <li><span class="font-semibold text-white">Confirmación:</span> Toda venta requiere
                                        tu aprobación explícita. Recibirás una notificación con los detalles.</li>
                                    <li><span class="font-semibold text-white">Comprobantes:</span> Debes presentar un
                                        comprobante de pago válido. Verificamos que el monto coincida; de lo contrario,
                                        la transacción se anulará.</li>
                                    <li><span class="font-semibold text-white">Envíos:</span> Se procesan tras confirmar
                                        el pago. Sin comprobante en un plazo razonable, la venta puede cancelarse.</li>
                                </ul>
                            </div>
                        </section>

                        <!-- Sección 5 -->
                        <section class="relative mb-10 group">
                            <div
                                class="absolute -inset-1 bg-gradient-to-r from-indigo-500/20 to-blue-500/20 rounded-xl blur opacity-20 group-hover:opacity-40 transition-opacity duration-500">
                            </div>
                            <div
                                class="relative bg-gray-800/60 p-6 rounded-xl border border-indigo-800/40 hover:border-blue-500/40 transition-all duration-300">
                                <h2 class="text-3xl font-semibold text-indigo-400 mb-4">5. Cancelaciones y Reclamaciones
                                </h2>
                                <ul class="list-disc list-inside text-gray-300 space-y-3">
                                    <li><span class="font-semibold text-white">Notas de Venta:</span> Una vez
                                        confirmada, no se aceptan cambios ni reclamaciones, salvo errores de nuestro
                                        sistema.</li>
                                    <li><span class="font-semibold text-white">Plazo:</span> Tienes <span
                                            class="font-bold text-white">7 días</span> desde la compra para reclamar
                                        sobre productos o envíos.</li>
                                    <li><span class="font-semibold text-white">Eliminación:</span> Ventas pendientes sin
                                        comprobante por más de 7 días podrán eliminarse para liberar inventario.</li>
                                </ul>
                            </div>
                        </section>

                        <!-- Sección 6 -->
                        <section class="relative mb-10 group">
                            <div
                                class="absolute -inset-1 bg-gradient-to-r from-emerald-500/20 to-teal-500/20 rounded-xl blur opacity-20 group-hover:opacity-40 transition-opacity duration-500">
                            </div>
                            <div
                                class="relative bg-gray-800/60 p-6 rounded-xl border border-emerald-800/40 hover:border-teal-500/40 transition-all duration-300">
                                <h2 class="text-3xl font-semibold text-emerald-400 mb-4">6. Métodos de Pago</h2>
                                <p class="text-lg text-gray-300 leading-relaxed">
                                    Aceptamos pagos mediante:
                                </p>
                                <ul class="list-disc list-inside text-gray-300 mt-2 space-y-2">
                                    <li><span class="font-semibold text-white">Código QR:</span> Disponible para
                                        transacciones digitales rápidas y seguras.</li>
                                    <li><span class="font-semibold text-white">Efectivo:</span> Exclusivamente en
                                        nuestra tienda física en La Paz, Bolivia.</li>
                                </ul>
                                <p class="text-lg text-gray-300 mt-2">
                                    Todos los pagos son verificados antes de procesar pedidos.
                                </p>
                            </div>
                        </section>

                        <!-- Sección 7 -->
                        <section class="relative group">
                            <div
                                class="absolute -inset-1 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 rounded-xl blur opacity-20 group-hover:opacity-40 transition-opacity duration-500">
                            </div>
                            <div
                                class="relative bg-gray-800/60 p-6 rounded-xl border border-blue-800/40 hover:border-indigo-500/40 transition-all duration-300">
                                <h2 class="text-3xl font-semibold text-blue-400 mb-4">7. Aceptación de Términos</h2>
                                <p class="text-lg text-gray-300 leading-relaxed">
                                    Al usar <span class="font-bold text-white">Poliméricos Dial Bolivia</span>, aceptas
                                    estos términos. Podemos actualizarlos en cualquier momento; revisa periódicamente.
                                    Para dudas, contáctanos por nuestros canales oficiales.
                                </p>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer: full-width y ocupando el final del layout -->
        <footer class="w-full py-10 text-center bg-gray-900/90 backdrop-blur-xl border-t border-blue-800/30">
            <div class="container mx-auto px-4">
                <p
                    class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-emerald-400 to-blue-500 animate-gradient-text">
                    Poliméricos Dial Bolivia
                </p>
                <p class="text-base text-gray-300 mt-2">
                    Parque Industrial MZ031 – Distrito N°8, La Paz, Bolivia
                </p>
                <p class="text-sm text-gray-400 mt-1">
                    © {{ new Date().getFullYear() }} Todos los derechos reservados
                </p>
                <div class="mt-6 flex justify-center space-x-6 text-xl">
                    <a href="https://www.facebook.com " target="_blank"
                        class="text-gray-400 hover:text-cyan-400 transition-colors duration-300">
                        Facebook
                    </a>
                    <a href="https://wa.me/573135805824 " target="_blank"
                        class="text-gray-400 hover:text-emerald-400 transition-colors duration-300">
                        WhatsApp
                    </a>
                </div>
            </div>
        </footer>
    </div>
</template>

<style>
body {
    @apply bg-black;
}

.animate-gradient-text {
    background-size: 200% 200%;
    animation: gradientText 5s ease infinite;
}

@keyframes gradientText {
    0% {
        background-position: 0% 50%;
    }

    50% {
        background-position: 100% 50%;
    }

    100% {
        background-position: 0% 50%;
    }
}
</style>