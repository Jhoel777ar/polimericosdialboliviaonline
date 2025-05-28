<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { computed } from "vue";
const logoPath = computed(() => "/storage/2333.jpg"); 
</script>
<template>
    <div class="bg-gradient-to-r from-gray-900 via-purple-950 to-black text-white min-h-screen">
        <div class="container mx-auto px-6 py-16">
            <h1
                class="text-5xl md:text-6xl font-extrabold text-center mb-8 bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 to-purple-500">
                Sobre Nosotros
            </h1>
            <p class="text-xl md:text-2xl text-center mb-12 text-gray-200 max-w-3xl mx-auto">
                Somos un grupo de profesionales apasionados por la <strong>educación digital</strong> y el
                <strong>diseño de interiores</strong>, dedicados a capacitar a las personas en técnicas avanzadas como
                el <em>porcelanato líquido 3D</em> y el uso de polímeros innovadores.
            </p>
            <div class="grid md:grid-cols-2 gap-10">
                <div
                    class="bg-gray-800/80 backdrop-blur-md p-8 rounded-xl shadow-2xl hover:shadow-cyan-500/20 transition-all duration-300">
                    <h2 class="text-3xl font-semibold text-cyan-400 mb-4">Misión</h2>
                    <p class="text-lg text-gray-300 leading-relaxed">
                        Brindar formación de vanguardia en diseño de interiores y porcelanato líquido 3D, combinando
                        teoría y práctica para empoderar a nuestros estudiantes y prepararlos para destacar en el
                        mercado laboral.
                    </p>
                </div>
                <div
                    class="bg-gray-800/80 backdrop-blur-md p-8 rounded-xl shadow-2xl hover:shadow-purple-500/20 transition-all duration-300">
                    <h2 class="text-3xl font-semibold text-purple-400 mb-4">Visión</h2>
                    <p class="text-lg text-gray-300 leading-relaxed">
                        Convertirnos en referentes globales en la enseñanza de técnicas de diseño innovadoras,
                        expandiendo nuestro alcance desde La Paz, Bolivia, hacia el mundo, formando profesionales
                        creativos y competentes.
                    </p>
                </div>
            </div>
            <div class="mt-16 text-center">
                <p class="text-lg text-gray-300 mb-4">¡Contáctanos hoy mismo!</p>
                <a href="https://wa.me/573135805824" target="_blank"
                    class="inline-block bg-gradient-to-r from-cyan-500 to-purple-600 text-white font-semibold py-3 px-6 rounded-full hover:scale-105 transition-transform duration-300">
                    Escríbenos por WhatsApp
                </a>
                <p class="mt-6">
                    <a href="https://www.facebook.com/PolimericosdiandeBolivia/?locale=es_LA" target="_blank"
                        class="text-purple-400 hover:text-purple-300 text-xl transition-colors duration-300">
                        Visita nuestro Facebook
                    </a>
                </p>
            </div>
            <div class="mt-16">
                <h3 class="text-2xl font-semibold text-center text-white mb-6">Nuestra Ubicación</h3>
                <div class="flex justify-center">
                    <iframe class="rounded-xl shadow-lg w-full max-w-3xl h-64 md:h-96"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3825.535927298889!2d-68.11929368518247!3d-16.50043818861447!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTbCsDMwJzAxLjYiUyA2OMKwMDcnMDkuNSJX!5e0!3m2!1ses!2sbo!4v1616623440000"
                        allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>
            <div class="mt-16 text-center">
                <h3 class="text-xl font-semibold text-white mb-6">Desarrollado por:</h3>
                <div class="flex justify-center items-center gap-8 text-sm text-purple-400">
                    <div class="flex flex-col items-center">
                        <img :src="logoPath" alt="Cyber Soul" class="w-20 h-20 object-contain mb-2">
                        <p>Cyber Soul</p>
                    </div>
                    <span class="text-white text-2xl">|</span>
                    <div class="flex flex-col items-center">
                        <a href="https://arkdev.pages.dev" target="_blank" class="hover:scale-110 transition-transform">
                            <img src="https://arkdev.pages.dev/src2/we001136218removebgpreview.png" alt="Ark Dev"
                                class="w-14 h-14 object-contain mb-2 cursor-pointer">
                        </a>
                        <p>Ark Dev</p>
                    </div>
                </div>
            </div>
        </div>
        <footer class="relative py-10 text-center overflow-hidden bg-gray-900/90">
            <div
                class="absolute inset-0 bg-gradient-to-br from-cyan-900/20 via-purple-900/20 to-black/90 backdrop-blur-xl">
            </div>
            <div class="relative z-10 space-y-4">
                <p
                    class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-400 animate-gradient-x">
                    Poliméricos Dial Bolivia
                </p>
                <p class="text-base text-gray-300 hover:text-gray-100 transition-all duration-300">
                    La Paz, Bolivia | © {{ new Date().getFullYear() }} Todos los derechos reservados
                </p>
                <div
                    class="absolute top-0 w-full h-px bg-gradient-to-r from-transparent via-cyan-400/50 to-transparent">
                </div>
                <div
                    class="absolute -top-20 left-0 w-64 h-64 bg-gradient-to-r from-cyan-600/20 to-purple-600/20 rounded-full blur-3xl opacity-40">
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