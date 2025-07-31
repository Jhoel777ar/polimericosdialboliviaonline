<script setup>
import { Head } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import Swal from 'sweetalert2';
import { computed, ref, watch, onMounted } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import Lenis from 'lenis';

defineProps({
    categoria: Object,
    productos: Object,
});

const lenis = ref(null);

onMounted(() => {
    lenis.value = new Lenis({
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        smooth: true,
    });

    function raf(time) {
        lenis.value.raf(time);
        requestAnimationFrame(raf);
    }

    requestAnimationFrame(raf);
});

function redirectToPrecompra(producto) {
    Inertia.get(`/precompra/${producto.id}`)
        .then((response) => {
            if (response.props.flash?.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.props.flash.error,
                });
            }
        })
        .catch(() => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un problema al redirigir al producto.',
            });
        });
};

function cambiarPagina(url) {
    if (url) {
        Inertia.get(url);
    }
};

const menuVisible = ref(false);
const toggleMenu = () => {
    menuVisible.value = !menuVisible.value;
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <header
        class="bg-gradient-to-br from-gray-950/95 via-gray-900/95 to-black/90 text-white shadow-2xl py-4 backdrop-blur-xl border-b border-gray-800/50">
        <div class="container mx-auto flex justify-between items-center px-4 lg:px-8">
            <div class="group relative">
                <div
                    class="absolute -inset-2 bg-gradient-to-r from-cyan-500 via-teal-500 to-emerald-500 rounded-xl blur-md opacity-20 group-hover:opacity-40 transition-all duration-500 animate-pulse">
                </div>
                <a href="/" class="relative text-3xl md:text-4xl font-extrabold tracking-tight bg-gradient-to-r from-cyan-400 via-teal-400 to-emerald-400 bg-clip-text text-transparent 
               hover:animate-pulse transition-all duration-300 z-10">
                    Categorias
                </a>
            </div>
            <nav
                class="hidden md:flex items-center gap-6 backdrop-blur-2xl rounded-xl bg-gradient-to-br from-gray-900/80 to-black/70 px-4 py-3 border border-gray-700/40 shadow-lg">
                <a href="/" class="relative overflow-hidden px-6 py-2 rounded-lg text-gray-200 hover:text-white 
               transition-all duration-300 bg-gray-800/60 hover:bg-gray-700/80 backdrop-blur-md 
               border border-gray-600/30 hover:border-cyan-500/50 hover:shadow-xl hover:shadow-cyan-500/20
               before:absolute before:inset-0 before:-translate-x-full before:animate-[shine_2s_infinite] 
               before:bg-gradient-to-r before:from-transparent before:via-cyan-500/20 before:to-transparent">
                    <span
                        class="font-semibold bg-gradient-to-r from-gray-100 to-cyan-300 bg-clip-text text-transparent">Inicio</span>
                </a>
                <template v-if="$page.props.auth.user">
                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="relative overflow-hidden px-6 py-2 rounded-lg text-gray-200 hover:text-white 
                 transition-all duration-300 bg-gray-800/60 hover:bg-gray-700/80 backdrop-blur-md 
                 border border-gray-600/30 hover:border-cyan-500/50 hover:shadow-xl hover:shadow-cyan-500/20
                 before:absolute before:inset-0 before:-translate-x-full before:animate-[shine_2s_infinite] 
                 before:bg-gradient-to-r before:from-transparent before:via-cyan-500/20 before:to-transparent">
                        <span
                            class="font-semibold bg-gradient-to-r from-gray-100 to-cyan-300 bg-clip-text text-transparent">Productos</span>
                    </NavLink>
                    <NavLink :href="route('ventas.ver')" :active="route().current('ventas.ver')" class="relative overflow-hidden px-6 py-2 rounded-lg text-gray-200 hover:text-white 
                 transition-all duration-300 bg-gray-800/60 hover:bg-gray-700/80 backdrop-blur-md 
                 border border-gray-600/30 hover:border-cyan-500/50 hover:shadow-xl hover:shadow-cyan-500/20
                 before:absolute before:inset-0 before:-translate-x-full before:animate-[shine_2s_infinite] 
                 before:bg-gradient-to-r before:from-transparent before:via-cyan-500/20 before:to-transparent">
                        <span
                            class="font-semibold bg-gradient-to-r from-gray-100 to-cyan-300 bg-clip-text text-transparent">Tus
                            Compras</span>
                    </NavLink>
                    <NavLink :href="route('carrito.ver')" :active="route().current('carrito.ver')" class="relative overflow-hidden px-6 py-2 rounded-lg text-gray-200 hover:text-white 
                 transition-all duration-300 bg-gray-800/60 hover:bg-gray-700/80 backdrop-blur-md 
                 border border-gray-600/30 hover:border-cyan-500/50 hover:shadow-xl hover:shadow-cyan-500/20
                 before:absolute before:inset-0 before:-translate-x-full before:animate-[shine_2s_infinite] 
                 before:bg-gradient-to-r before:from-transparent before:via-cyan-500/20 before:to-transparent">
                        <span
                            class="font-semibold bg-gradient-to-r from-gray-100 to-cyan-300 bg-clip-text text-transparent">Tu
                            Carrito</span>
                    </NavLink>
                </template>
                <div class="ml-3 relative" v-if="$page.props.auth.user">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button v-if="$page.props.jetstream.managesProfilePhotos"
                                class="flex text-sm rounded-full border-2 border-gray-700/50 hover:border-cyan-500/50 
                     transition-all duration-300 focus:outline-none focus:border-cyan-400 hover:shadow-md hover:shadow-cyan-500/20">
                                <img class="size-9 rounded-full object-cover"
                                    :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </button>
                            <span v-else class="inline-flex rounded-md">
                                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-700/50 text-sm font-medium rounded-md 
                       text-gray-200 bg-gray-800/60 hover:bg-gray-700/80 hover:text-white hover:border-cyan-500/50 
                       focus:outline-none focus:bg-gray-700 focus:border-cyan-400 transition-all duration-300 
                       backdrop-blur-md shadow-sm hover:shadow-cyan-500/20">
                                    {{ $page.props.auth.user.name }}
                                    <svg class="ml-2 -mr-1 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </span>
                        </template>
                        <template #content>
                            <DropdownLink :href="route('profile.show')"
                                class="text-gray-200 hover:bg-gray-700/80 hover:text-cyan-300">
                                Perfil
                            </DropdownLink>
                            <form @submit.prevent="logout">
                                <DropdownLink as="button"
                                    class="text-gray-200 hover:bg-gray-700/80 hover:text-cyan-300">
                                    Cerrar Sesión
                                </DropdownLink>
                            </form>
                        </template>
                    </Dropdown>
                </div>
            </nav>
            <button @click="toggleMenu"
                class="md:hidden p-3 rounded-xl backdrop-blur-2xl bg-gray-900/70 border border-gray-700/40 
             hover:bg-gray-800/80 hover:border-cyan-500/50 transition-all duration-300 shadow-md hover:shadow-cyan-500/20">
                <i
                    class="fas fa-bars text-xl bg-gradient-to-r from-cyan-400 to-emerald-400 bg-clip-text text-transparent"></i>
            </button>
        </div>
        <div v-if="menuVisible" class="md:hidden mt-4 mx-4 bg-gradient-to-br from-gray-900/90 to-black/80 backdrop-blur-2xl rounded-2xl 
           border border-gray-700/40 shadow-xl shadow-cyan-900/30">
            <div class="p-4 space-y-4">
                <a href="/" class="block px-6 py-3 rounded-xl bg-gray-800/60 hover:bg-gray-700/80 backdrop-blur-md 
                        border border-gray-600/30 hover:border-cyan-500/50 hover:shadow-md hover:shadow-cyan-500/20 
                        text-gray-200 hover:text-white transition-all duration-300 font-medium 
                        bg-gradient-to-r from-gray-100 to-cyan-300 bg-clip-text text-transparent">
                    Inicio
                </a>
                <template v-if="$page.props.auth.user">
                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="block px-6 py-3 rounded-xl bg-gray-800/60 hover:bg-gray-700/80 backdrop-blur-md 
                 border border-gray-600/30 hover:border-cyan-500/50 hover:shadow-md hover:shadow-cyan-500/20 
                 text-gray-200 hover:text-white transition-all duration-300 font-medium 
                 bg-gradient-to-r from-gray-100 to-cyan-300 bg-clip-text text-transparent">
                        Productos
                    </NavLink>
                    <NavLink :href="route('ventas.ver')" :active="route().current('ventas.ver')" class="block px-6 py-3 rounded-xl bg-gray-800/60 hover:bg-gray-700/80 backdrop-blur-md 
                 border border-gray-600/30 hover:border-cyan-500/50 hover:shadow-md hover:shadow-cyan-500/20 
                 text-gray-200 hover:text-white transition-all duration-300 font-medium 
                 bg-gradient-to-r from-gray-100 to-cyan-300 bg-clip-text text-transparent">
                        Tus Compras
                    </NavLink>
                    <NavLink :href="route('carrito.ver')" :active="route().current('carrito.ver')" class="block px-6 py-3 rounded-xl bg-gray-800/60 hover:bg-gray-700/80 backdrop-blur-md 
                 border border-gray-600/30 hover:border-cyan-500/50 hover:shadow-md hover:shadow-cyan-500/20 
                 text-gray-200 hover:text-white transition-all duration-300 font-medium 
                 bg-gradient-to-r from-gray-100 to-cyan-300 bg-clip-text text-transparent">
                        Tu Carrito
                    </NavLink>
                </template>
                <div class="ml-3 relative" v-if="$page.props.auth.user">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button v-if="$page.props.jetstream.managesProfilePhotos"
                                class="flex text-sm rounded-full border-2 border-gray-700/50 hover:border-cyan-500/50 
                     transition-all duration-300 focus:outline-none focus:border-cyan-400 hover:shadow-md hover:shadow-cyan-500/20">
                                <img class="size-9 rounded-full object-cover"
                                    :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </button>
                            <span v-else class="inline-flex rounded-md">
                                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-700/50 text-sm font-medium rounded-md 
                       text-gray-200 bg-gray-800/60 hover:bg-gray-700/80 hover:text-white hover:border-cyan-500/50 
                       focus:outline-none focus:bg-gray-700 focus:border-cyan-400 transition-all duration-300 
                       backdrop-blur-md shadow-sm hover:shadow-cyan-500/20">
                                    {{ $page.props.auth.user.name }}
                                    <svg class="ml-2 -mr-1 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </span>
                        </template>
                        <template #content>
                            <DropdownLink :href="route('profile.show')"
                                class="text-gray-200 hover:bg-gray-700/80 hover:text-cyan-300">
                                Perfil
                            </DropdownLink>
                            <form @submit.prevent="logout">
                                <DropdownLink as="button"
                                    class="text-gray-200 hover:bg-gray-700/80 hover:text-cyan-300">
                                    Cerrar Sesión
                                </DropdownLink>
                            </form>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </div>
    </header>

    <Head :title="`Categoría - ${categoria.nombre}`" />

    <main class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-green-950 pt-24 pb-16 px-4">
        <div class="container mx-auto max-w-7xl">
            <div
                class="relative bg-black/40 backdrop-blur-2xl rounded-3xl border border-blue-900/30 p-8 md:p-12 shadow-2xl shadow-blue-900/20">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-green-500/10 to-blue-500/10 rounded-3xl blur-2Ա2xl opacity-50">
                </div>

                <div v-if="categoria.parent" class="relative mb-8 text-center">
                    <div
                        class="inline-flex items-center px-6 py-3 rounded-full bg-black/50 backdrop-blur-lg border border-blue-900/20">
                        <span class="text-sm font-medium text-gray-300">Subcategoría de:</span>
                        <span
                            class="ml-2 text-lg font-bold bg-gradient-to-r from-blue-400 to-green-400 bg-clip-text text-transparent">
                            {{ categoria.parent.nombre }}
                        </span>
                    </div>
                </div>

                <h1 class="relative text-4xl md:text-5xl font-extrabold tracking-tight text-center mb-6">
                    <span
                        class="bg-gradient-to-r from-blue-400 via-green-400 to-blue-600 bg-clip-text text-transparent">
                        {{ categoria.nombre }}
                    </span>
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-green-500/20 rounded-3xl blur-xl opacity-40">
                    </div>
                </h1>

                <p class="relative text-lg md:text-xl text-gray-300 text-center max-w-3xl mx-auto mb-12
                    bg-gradient-to-r from-gray-200 to-gray-400 bg-clip-text text-transparent">
                    {{ categoria.descripcion }}
                </p>

                <div v-if="productos.data.length > 0"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 relative">
                    <div v-for="producto in productos.data" :key="producto.id" class="group relative bg-black/50 backdrop-blur-xl rounded-2xl border border-blue-900/30 p-6
                        hover:border-blue-500/50 hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-500">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-green-500/10 rounded-2xl opacity-0 group-hover:opacity-30 transition-opacity duration-500">
                        </div>

                        <div class="relative overflow-hidden rounded-xl">
                            <img :src="'/storage/' + producto.imagen_url" alt="Producto"
                                class="w-full h-64 object-cover transform group-hover:scale-105 transition-transform duration-500">
                            <div v-if="producto.stock === 0"
                                class="absolute top-4 right-4 bg-gradient-to-r from-red-500 to-pink-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                Agotado
                            </div>
                        </div>

                        <div class="pt-6 space-y-4">
                            <h3
                                class="text-xl font-bold text-white truncate group-hover:text-blue-400 transition-colors">
                                {{ producto.nombre }}
                            </h3>
                            <p class="text-sm text-gray-400 line-clamp-2 leading-relaxed">
                                {{ producto.descripcion }}
                            </p>
                            <p
                                class="text-2xl font-extrabold bg-gradient-to-r from-blue-400 to-green-400 bg-clip-text text-transparent">
                                Bs. {{ producto.precio }}
                            </p>
                            <button @click="redirectToPrecompra(producto)"
                                class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-green-600 text-white font-semibold rounded-xl
                                shadow-lg hover:shadow-blue-500/40 hover:from-blue-500 hover:to-green-500 transition-all duration-300 transform hover:scale-[1.02]">
                                <i class="fas fa-shopping-cart mr-2"></i> Ver Producto
                            </button>
                        </div>
                    </div>
                </div>

                <div v-else class="relative text-center py-16">
                    <div
                        class="max-w-md mx-auto bg-black/50 backdrop-blur-lg rounded-2xl p-8 border border-blue-900/30">
                        <p class="text-2xl font-light text-gray-400">No hay productos disponibles</p>
                        <div class="mt-4 text-4xl text-gray-600">
                            <i class="fas fa-box-open"></i>
                        </div>
                    </div>
                </div>

                <div v-if="productos.links.length > 1" class="flex justify-center items-center mt-12 space-x-2">
                    <button v-for="link in productos.links.filter(l => !isNaN(l.label))" :key="link.label"
                        @click.prevent="cambiarPagina(link.url)"
                        class="px-5 py-2.5 min-w-[40px] text-sm font-semibold rounded-full transition-all duration-300"
                        :class="[
                            link.active
                                ? 'bg-gradient-to-r from-blue-600 to-green-600 text-white shadow-lg shadow-blue-500/30'
                                : 'bg-black/50 text-gray-300 hover:bg-gradient-to-r hover:from-blue-500/40 hover:to-green-500/40 hover:text-white'
                        ]">
                        {{ link.label }}
                    </button>
                </div>
            </div>
        </div>
    </main>

    <footer class="py-10 text-center bg-gradient-to-br from-black/70 via-blue-950/60 to-green-950/60 backdrop-blur-xl">
        <div class="relative space-y-4">
            <p class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-green-300">
                Poliméricos Dial Bolivia</p>
            <p class="text-base text-gray-200">© {{ new Date().getFullYear() }} Todos los derechos
                reservados</p>
            <div class="absolute top-0 w-full h-px bg-gradient-to-r from-transparent via-blue-400 to-transparent"></div>
        </div>
    </footer>
</template>

<style>
body {
    @apply bg-black;
}
</style>