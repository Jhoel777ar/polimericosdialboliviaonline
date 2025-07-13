<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { computed } from "vue";
const logoPath = computed(() => "/storage/2333.jpg"); 
</script>

<template>
    <div class="bg-gradient-to-br from-black via-blue-950 to-emerald-900 text-white min-h-screen">
        <div class="container mx-auto px-6 py-16">
            <h1
                class="text-5xl md:text-6xl font-extrabold text-center mb-8 bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 via-emerald-400 to-blue-500">
                Sobre Nosotros
            </h1>
            <p class="text-xl md:text-2xl text-center mb-12 text-gray-200 max-w-3xl mx-auto">
                Somos un grupo de profesionales apasionados por la <strong>educación digital</strong> y el
                <strong>diseño de interiores</strong>, dedicados a capacitar a las personas en técnicas avanzadas como
                el <em>porcelanato líquido 3D</em> y el uso de polímeros innovadores.
            </p>
            <div class="grid md:grid-cols-2 gap-10">
                <div
                    class="bg-gray-900/70 backdrop-blur-md p-8 rounded-xl shadow-2xl hover:shadow-cyan-500/20 transition-all duration-300 border border-blue-800/30">
                    <h2 class="text-3xl font-semibold text-cyan-400 mb-4">Misión</h2>
                    <p class="text-lg text-gray-300 leading-relaxed">
                        Brindar formación de vanguardia en diseño de interiores y porcelanato líquido 3D, combinando
                        teoría y práctica para empoderar a nuestros estudiantes y prepararlos para destacar en el
                        mercado laboral.
                    </p>
                </div>
                <div
                    class="bg-gray-900/70 backdrop-blur-md p-8 rounded-xl shadow-2xl hover:shadow-emerald-500/20 transition-all duration-300 border border-emerald-800/30">
                    <h2 class="text-3xl font-semibold text-emerald-400 mb-4">Visión</h2>
                    <p class="text-lg text-gray-300 leading-relaxed">
                        Convertirnos en referentes globales en la enseñanza de técnicas de diseño innovadoras,
                        expandiendo nuestro alcance desde La Paz, Bolivia, hacia el mundo, formando profesionales
                        creativos y competentes.
                    </p>
                </div>
            </div>

            <!-- Nueva sección de servicios -->
            <div class="relative mt-24">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-black via-blue-900 to-emerald-900 rounded-3xl blur-xl opacity-50">
                </div>
                <div
                    class="relative bg-gray-900/70 backdrop-blur-lg border border-blue-800/30 rounded-3xl p-8 md:p-12 shadow-2xl">
                    <h2
                        class="text-4xl font-extrabold text-center mb-6 bg-clip-text text-transparent bg-gradient-to-r from-blue-400 via-emerald-400 to-cyan-500">
                        Nuestros Servicios Destacados
                    </h2>
                    <p class="text-gray-300 text-center max-w-2xl mx-auto mb-10">
                        Ofrecemos soluciones innovadoras en formación técnica y desarrollo digital. Explora lo que
                        hacemos posible.
                    </p>
                    <div class="grid md:grid-cols-2 gap-8">
                        <!-- Servicio 1 -->
                        <div
                            class="group bg-gradient-to-br from-gray-800/80 via-blue-900/30 to-gray-900/80 p-6 rounded-2xl hover:shadow-lg hover:shadow-cyan-500/20 transition-all duration-300 border border-gray-700/50">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="w-12 h-12 flex-shrink-0 rounded-xl bg-blue-500/20 group-hover:bg-blue-500/30 flex items-center justify-center transition-colors">
                                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-semibold text-white">Capacitación Técnica</h3>
                                    <p class="text-sm text-gray-400">Enseñamos técnicas avanzadas como porcelanato
                                        líquido 3D y uso de polímeros.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Servicio 2 -->
                        <div
                            class="group bg-gradient-to-br from-gray-800/80 via-emerald-900/30 to-gray-900/80 p-6 rounded-2xl hover:shadow-lg hover:shadow-emerald-500/20 transition-all duration-300 border border-gray-700/50">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="w-12 h-12 flex-shrink-0 rounded-xl bg-emerald-500/20 group-hover:bg-emerald-500/30 flex items-center justify-center transition-colors">
                                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.13-2.052-.38-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-semibold text-white">Desarrollo Digital</h3>
                                    <p class="text-sm text-gray-400">Creamos plataformas educativas digitales intuitivas
                                        y modernas.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-10 text-center">
                        <a href="#contacto"
                            class="inline-flex items-center px-6 py-3 text-base font-medium rounded-full bg-gradient-to-r from-cyan-500 to-emerald-500 hover:from-emerald-500 hover:to-cyan-500 text-white transition-all duration-300 transform hover:scale-105">
                            Más Información
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Fin de la nueva sección -->

            <div class="mt-16 text-center">
                <p class="text-lg text-gray-300 mb-4">¡Contáctanos hoy mismo!</p>
                <a href="https://wa.me/573135805824 " target="_blank"
                    class="inline-block bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold py-3 px-6 rounded-full hover:scale-105 transition-transform duration-300">
                    Escríbenos por WhatsApp
                </a>
                <p class="mt-6">
                    <a href="https://www.facebook.com/PolimericosdiandeBolivia/?locale=es_LA" target="_blank"
                        class="text-blue-400 hover:text-emerald-400 text-xl transition-colors duration-300">
                        Visita nuestro Facebook
                    </a>
                </p>
            </div>
            <div class="mt-16">
                <h3 class="text-2xl font-semibold text-center text-white mb-6">Nuestra Ubicación</h3>
                <div class="flex justify-center">
                    <iframe class="rounded-xl shadow-lg w-full max-w-3xl h-64 md:h-96"
                        src=" https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3825.535927298889!2d-68.11929368518247!3d-16.50043818861447!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTbCsDMwJzAxLjYiUyA2OMKwMDcnMDkuNSJX!5e0!3m2!1ses!2sbo!4v1616623440000"
                        allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>
            <div class="mt-16 text-center">
                <h3 class="text-xl font-semibold text-white mb-6">Desarrollado por:</h3>
                <div class="flex justify-center items-center gap-8 text-sm text-blue-400">
                    <div class="flex flex-col items-center">
                        <img :src="logoPath" alt="Cyber Soul" class="w-20 h-20 object-contain mb-2">
                        <p>Cyber Soul</p>
                    </div>
                    <span class="text-white text-2xl">|</span>
                    <div class="flex flex-col items-center">
                        <a href=" https://arkdev.pages.dev " target="_blank"
                            class="hover:scale-110 transition-transform">
                            <img src="https://arkdev.pages.dev/src2/we001136218removebgpreview.png " alt="Ark Dev"
                                class="w-14 h-14 object-contain mb-2 cursor-pointer">
                        </a>
                        <p>Ark Dev</p>
                    </div>
                </div>
            </div>
        </div>
        <footer class="relative py-10 text-center overflow-hidden bg-gray-900/90">
            <div
                class="absolute inset-0 bg-gradient-to-br from-blue-900/20 via-emerald-900/20 to-black/90 backdrop-blur-xl">
            </div>
            <div class="relative z-10 space-y-4">
                <p
                    class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-emerald-400 to-blue-500 animate-gradient-x">
                    Poliméricos Dial Bolivia
                </p>
                <p class="text-base text-gray-300 hover:text-gray-100 transition-all duration-300">
                    La Paz, Bolivia | © {{ new Date().getFullYear() }} Todos los derechos reservados
                </p>
                <div
                    class="absolute top-0 w-full h-px bg-gradient-to-r from-transparent via-cyan-400/50 to-transparent">
                </div>
                <div
                    class="absolute -top-20 left-0 w-64 h-64 bg-gradient-to-r from-cyan-600/20 to-emerald-600/20 rounded-full blur-3xl opacity-40">
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.animate-gradient-x {
    background-size: 200% 200%;
    animation: gradient-x 5s ease infinite;
}

@keyframes gradient-x {
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