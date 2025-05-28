<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';

defineProps({
    policy: String,
});
</script>

<template>
    <Head title="Política de Privacidad y Términos de Uso" />
    <div class="font-sans antialiased text-gray-100 bg-gradient-to-br from-gray-900 via-purple-950 to-black min-h-screen">
        <div class="pt-16 flex flex-col items-center">
            <div class="mb-8">
                <AuthenticationCardLogo />
            </div>
            <div class="w-full max-w-4xl mx-4 p-8 bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-700/50">
                <h1 class="text-4xl md:text-5xl font-extrabold text-center mb-8 bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 via-purple-400 to-pink-400 animate-gradient-text">
                    Política de Privacidad y Términos de Uso
                </h1>
                <section class="mb-10">
                    <h2 class="text-3xl font-semibold text-cyan-400 mb-4">1. Protección de Datos Personales</h2>
                    <p class="text-lg text-gray-300 leading-relaxed">
                        En <span class="font-bold text-white">Poliméricos Dial Bolivia</span>, tu privacidad es nuestra prioridad. Protegemos tus datos personales (nombres, correos, CI, teléfonos) con los más altos estándares de seguridad, utilizándolos únicamente para gestionar tu cuenta, procesar pedidos y comunicarnos contigo sobre nuestras operaciones.
                    </p>
                    <p class="text-lg text-gray-300 mt-2">
                        No compartimos, vendemos ni divulgamos tu información a terceros sin tu consentimiento, salvo obligación legal.
                    </p>
                </section>
                <section class="mb-10">
                    <h2 class="text-3xl font-semibold text-purple-400 mb-4">2. Seguridad y Autenticación</h2>
                    <p class="text-lg text-gray-300 leading-relaxed">
                        Garantizamos la seguridad de tu cuenta con:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mt-2 space-y-2">
                        <li><span class="font-semibold text-white">Autenticación de Dos Pasos (2FA):</span> Actívala para mayor protección.</li>
                        <li><span class="font-semibold text-white">Inicio con Google:</span> Acceso rápido y seguro.</li>
                    </ul>
                    <p class="text-lg text-gray-300 mt-2">
                        Monitoreamos todas las transacciones para prevenir fraudes y accesos no autorizados.
                    </p>
                </section>
                <section class="mb-10">
                    <h2 class="text-3xl font-semibold text-pink-400 mb-4">3. Uso Inadecuado y Sanciones</h2>
                    <p class="text-lg text-gray-300 leading-relaxed">
                        Prohibimos cualquier actividad indebida, como hackeos, fraudes o manipulación de datos. Estas acciones resultarán en:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mt-2 space-y-2">
                        <li>Suspensión o eliminación inmediata de la cuenta.</li>
                        <li>Posibles acciones legales si aplica.</li>
                    </ul>
                    <p class="text-lg text-gray-300 mt-2">
                        Registramos actividades sospechosas para proteger la plataforma y a nuestros usuarios.
                    </p>
                </section>
                <section class="mb-10">
                    <h2 class="text-3xl font-semibold text-teal-400 mb-4">4. Procesos de Venta y Envío</h2>
                    <ul class="list-disc list-inside text-gray-300 space-y-3">
                        <li><span class="font-semibold text-white">Confirmación:</span> Toda venta requiere tu aprobación explícita. Recibirás una notificación con los detalles.</li>
                        <li><span class="font-semibold text-white">Comprobantes:</span> Debes presentar un comprobante de pago válido. Verificamos que el monto coincida; de lo contrario, la transacción se anulará.</li>
                        <li><span class="font-semibold text-white">Envíos:</span> Se procesan tras confirmar el pago. Sin comprobante en un plazo razonable, la venta puede cancelarse.</li>
                    </ul>
                </section>
                <section class="mb-10">
                    <h2 class="text-3xl font-semibold text-indigo-400 mb-4">5. Cancelaciones y Reclamaciones</h2>
                    <ul class="list-disc list-inside text-gray-300 space-y-3">
                        <li><span class="font-semibold text-white">Notas de Venta:</span> Una vez confirmada, no se aceptan cambios ni reclamaciones, salvo errores de nuestro sistema.</li>
                        <li><span class="font-semibold text-white">Plazo:</span> Tienes <span class="font-bold text-white">7 días</span> desde la compra para reclamar sobre productos o envíos.</li>
                        <li><span class="font-semibold text-white">Eliminación:</span> Ventas pendientes sin comprobante por más de 7 días podrán eliminarse para liberar inventario.</li>
                    </ul>
                </section>
                <section class="mb-10">
                    <h2 class="text-3xl font-semibold text-emerald-400 mb-4">6. Métodos de Pago</h2>
                    <p class="text-lg text-gray-300 leading-relaxed">
                        Aceptamos pagos mediante:
                    </p>
                    <ul class="list-disc list-inside text-gray-300 mt-2 space-y-2">
                        <li><span class="font-semibold text-white">Código QR:</span> Disponible para transacciones digitales rápidas y seguras.</li>
                        <li><span class="font-semibold text-white">Efectivo:</span> Exclusivamente en nuestra tienda física en La Paz, Bolivia.</li>
                    </ul>
                    <p class="text-lg text-gray-300 mt-2">
                        Todos los pagos son verificados antes de procesar pedidos.
                    </p>
                </section>
                <section class="mb-10">
                    <h2 class="text-3xl font-semibold text-blue-400 mb-4">7. Aceptación de Términos</h2>
                    <p class="text-lg text-gray-300 leading-relaxed">
                        Al usar <span class="font-bold text-white">Poliméricos Dial Bolivia</span>, aceptas estos términos. Podemos actualizarlos en cualquier momento; revisa periódicamente. Para dudas, contáctanos por nuestros canales oficiales.
                    </p>
                </section>
            </div>
            <footer class="mt-16 w-full py-10 text-center bg-gray-900/90 backdrop-blur-xl border-t border-gray-700/50">
                <div class="max-w-4xl mx-auto px-4">
                    <p class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-purple-400 to-pink-400 animate-gradient-text">
                        Poliméricos Dial Bolivia
                    </p>
                    <p class="text-base text-gray-300 mt-2">
                        Parque Industrial MZ031 – Distrito N°8, La Paz, Bolivia
                    </p>
                    <p class="text-sm text-gray-400 mt-1">
                        © {{ new Date().getFullYear() }} Todos los derechos reservados
                    </p>
                    <div class="mt-6 flex justify-center space-x-6 text-xl">
                        <a href="https://www.facebook.com" target="_blank" class="text-gray-400 hover:text-cyan-400 transition-colors">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://twitter.com" target="_blank" class="text-gray-400 hover:text-purple-400 transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.instagram.com" target="_blank" class="text-gray-400 hover:text-pink-400 transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>
<style scoped>
.animate-gradient-text {
    background-size: 200% 200%;
    animation: gradientText 3s ease infinite;
}
@keyframes gradientText {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
</style>