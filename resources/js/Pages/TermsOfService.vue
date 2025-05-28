<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';

const sections = [
    {
        title: '1. Libertad para Navegar y Comparar Productos y Servicios',
        content: 'La sola visita de este sitio no te impone obligación alguna, a menos que hayas expresado en forma inequívoca y mediante actos positivos tu voluntad de adquirir determinados bienes o servicios, en la forma indicada en estos Términos y Condiciones.'
    },
    {
        title: '2. ¿Cómo Comprar o Contratar Servicios en Este Sitio?',
        content: `
            <ol class="list-decimal ml-8">
                <li>Selecciona los productos o servicios de tu interés y agrégales al carrito de compras.</li>
                <li>Selecciona el tipo de envío y uno de los medios de pago disponibles.</li>
                <li>Una vez colocada la orden, se desplegará en tu pantalla una descripción detallada del producto o servicio, su precio, fecha de entrega, el medio de pago, el valor total de la operación y las demás condiciones.</li>
                <li>Recibirás un correo electrónico con los detalles de tu compra.</li>
                <li>Tu orden pasará a un proceso de confirmación de identidad y disponibilidad del pago.</li>
                <li>Una vez confirmada, se procederá con el cargo y el despacho del producto.</li>
            </ol>
        `
    },
    {
        title: '3. Cambios y Devoluciones',
        content: 'En Poliméricos DIAL De Bolivia, nuestra prioridad es que te sientas contento con las compras que realices. Si necesitas realizar cambios o devoluciones, puedes hacerlo bajo nuestras políticas de devolución.'
    },
    {
        title: '4. Despacho de los Productos',
        content: 'Podrás elegir las condiciones de despacho de los productos adquiridos de entre las opciones disponibles en el sitio. Si los productos están almacenados en diferentes lugares, podrán enviarse por separado.'
    },
    {
        title: '5. Compra Online y Retira en Tienda',
        content: 'Si eliges retirar tu producto en tienda, podrás hacerlo dentro del horario de apertura. Te avisaremos cuando tu pedido esté listo para ser recogido.'
    },
    {
        title: '6. Políticas de Seguridad',
        content: 'Poliméricos DIAL De Bolivia adoptará medidas para proteger tus datos y la seguridad de las transacciones. Si detectamos irregularidades, podremos contactarte para confirmar tu identidad y evitar fraudes.'
    },
    {
        title: '7. Eliminación de Productos Vendidos',
        content: 'Los productos que han sido vendidos y pagados serán eliminados de la tienda después de una semana.'
    },
    {
        title: '8. Política de Privacidad',
        content: 'Respetamos tu privacidad y protegemos tus datos personales. No compartiremos tu información con terceros sin tu consentimiento.'
    },
    {
        title: '9. Cookies y Tecnologías Similares',
        content: 'Usamos cookies para personalizar tu experiencia en el sitio y mostrar publicidad relevante. Puedes deshabilitar las cookies en cualquier momento.'
    },
    {
        title: '10. Contacto',
        content: 'Si tienes alguna duda o necesitas asistencia, contáctanos vía WhatsApp o correo electrónico.'
    }
];
</script>

<template>

    <Head title="Términos y Condiciones" />

    <!-- Contenedor Principal -->
    <div class="font-sans antialiased bg-gradient-to-br from-gray-900 via-blue-950 to-black min-h-screen text-gray-100">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 pt-16">
            <div class="min-h-screen flex flex-col items-center">
                <!-- Logo -->
                <div class="mb-10 hover:scale-110 transition-transform duration-300">
                    <AuthenticationCardLogo class="w-32 h-32" />
                </div>

                <!-- Contenedor de Términos -->
                <div
                    class="w-full max-w-4xl p-8 bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-700/50">
                    <h1
                        class="text-4xl md:text-5xl font-extrabold text-center mb-10 bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-400 animate-gradient-text">
                        Términos y Condiciones
                    </h1>

                    <!-- Introducción -->
                    <div class="relative group mb-12">
                        <div
                            class="absolute -inset-2 bg-gradient-to-r from-cyan-500/20 to-purple-500/20 rounded-xl blur-xl opacity-30 group-hover:opacity-50 transition-opacity duration-500">
                        </div>
                        <div class="relative bg-gray-900/50 p-6 rounded-xl border border-gray-700/30">
                            <p class="text-lg leading-relaxed">
                                Bienvenidos a <span class="font-bold text-cyan-400">Poliméricos DIAL De Bolivia</span>,
                                un equipo de profesionales apasionados por la <span class="text-blue-300">educación
                                    digital</span> y el <span class="text-blue-300">diseño de interiores</span>. Nos
                                dedicamos a capacitar en técnicas innovadoras como <span
                                    class="font-semibold text-purple-400">porcelanato líquido 3D</span>, <span
                                    class="font-semibold text-purple-400">hidrográfica</span>, <span
                                    class="font-semibold text-purple-400">hidrocromo</span> y <span
                                    class="font-semibold text-purple-400">efectos especiales</span>. Desde nuestro
                                centro en <span class="font-bold text-white">La Paz, Bolivia</span>, ofrecemos atención
                                personalizada vía <a href="https://wa.me/573135805824" target="_blank"
                                    class="text-cyan-400 hover:text-cyan-300 underline">WhatsApp</a>.
                            </p>
                        </div>
                    </div>

                    <!-- Secciones Dinámicas -->
                    <section v-for="section in sections" :key="section.title" class="relative mb-10 group">
                        <div
                            class="absolute -inset-1 bg-gradient-to-r from-blue-600/20 to-purple-600/20 rounded-xl blur opacity-20 group-hover:opacity-40 transition-opacity duration-500">
                        </div>
                        <div
                            class="relative bg-gray-800/60 p-6 rounded-xl border border-gray-700/40 hover:border-blue-500/40 transition-all duration-300">
                            <h2
                                class="text-2xl md:text-3xl font-semibold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-cyan-400">
                                {{ section.title }}
                            </h2>
                            <div class="text-lg text-gray-300 leading-relaxed" v-html="section.content"></div>
                        </div>
                    </section>
                </div>

                <!-- Footer Moderno -->
                <footer
                    class="mt-16 w-full py-10 text-center bg-gray-900/90 backdrop-blur-xl border-t border-gray-700/50">
                    <div class="max-w-4xl mx-auto px-4">
                        <p
                            class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-400 animate-gradient-text">
                            Poliméricos DIAL De Bolivia
                        </p>
                        <p class="text-base text-gray-300 mt-2">
                            Parque Industrial MZ031 – Distrito N°8, La Paz, Bolivia
                        </p>
                        <p class="text-sm text-gray-400 mt-1">
                            © {{ new Date().getFullYear() }} Todos los derechos reservados
                        </p>
                        <div class="mt-6 flex justify-center space-x-6 text-xl">
                            <a href="https://www.facebook.com" target="_blank"
                                class="text-gray-400 hover:text-cyan-400 transition-colors duration-300">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="https://twitter.com" target="_blank"
                                class="text-gray-400 hover:text-blue-400 transition-colors duration-300">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.instagram.com" target="_blank"
                                class="text-gray-400 hover:text-purple-400 transition-colors duration-300">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>
</template>

<style>
.animate-gradient-text {
    background-size: 200% 200%;
    animation: gradientText 3s ease infinite;
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

@keyframes holo-grid {
    0% {
        background-position: 0 0;
    }

    100% {
        background-position: 100px 100px;
    }
}

@keyframes holo-pulse {

    0%,
    100% {
        opacity: 0.8;
        transform: scale(1);
    }

    50% {
        opacity: 0.4;
        transform: scale(1.2);
    }
}

.glass-morph-xl {
    background: rgba(19, 28, 46, 0.45);
    backdrop-filter: blur(40px) saturate(180%);
    -webkit-backdrop-filter: blur(40px) saturate(180%);
    border: 1px solid rgba(110, 231, 255, 0.15);
}

.animate-gradient-spin-slow {
    animation: gradient-spin 20s linear infinite;
}

@keyframes gradient-spin {
    100% {
        transform: rotate(360deg);
    }
}

.animate-holo-sparkle {
    animation: sparkle 2s ease-in-out infinite;
}

@keyframes sparkle {

    0%,
    100% {
        opacity: 0.8;
        transform: scale(1);
    }

    50% {
        opacity: 1;
        transform: scale(1.2);
    }
}
</style>
