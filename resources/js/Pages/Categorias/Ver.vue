<script setup>
import { Head } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import Swal from 'sweetalert2';
import { computed, ref, watch } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';

defineProps({
    categoria: Object,
    productos: Object,
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
// Función para mostrar/ocultar el menú en móviles
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
    <header class="bg-gradient-to-r from-gray-900 via-gray-700 to-gray-800 text-white shadow-lg py-4">
        <div class="container mx-auto flex justify-between items-center px-4 lg:px-8">
            <div class="group relative">
                <div
                    class="absolute -inset-1 bg-gradient-to-r from-cyan-500 to-emerald-500 rounded-xl blur opacity-30 group-hover:opacity-50 transition-all duration-300 animate-tilt">
                </div>
                <a href="/"
                    class="text-3xl font-black tracking-tighter bg-gradient-to-r from-cyan-400 to-emerald-400 bg-clip-text text-transparent hover:animate-pulse transition-all duration-300">
                    Detalles de Categoria
                </a>
            </div>
            <nav
                class="hidden md:flex items-center gap-6 backdrop-blur-lg rounded-xl bg-gradient-to-br from-gray-800 to-gray-900/50 px-4 py-2 border border-white/10">
                <a href="/"
                    class="relative overflow-hidden px-6 py-2.5 rounded-lg text-gray-300 hover:text-white transition-all duration-300
                       bg-gray-800/50 hover:bg-gray-700/60 backdrop-blur-lg border border-white/10
                       hover:border-cyan-400/30 hover:shadow-lg hover:shadow-cyan-500/20
                       before:absolute before:inset-0 before:-translate-x-full before:animate-[shine_3s_infinite] before:bg-gradient-to-r before:from-transparent before:via-white/10 before:to-transparent">
                    <span
                        class="font-semibold bg-gradient-to-r from-gray-200 to-gray-400 bg-clip-text text-transparent">Inicio</span>
                </a>
                <template v-if="$page.props.auth.user">
                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')"
                        class="relative overflow-hidden px-6 py-2.5 rounded-lg text-gray-300 hover:text-white transition-all duration-300
                       bg-gray-800/50 hover:bg-gray-700/60 backdrop-blur-lg border border-white/10
                       hover:border-cyan-400/30 hover:shadow-lg hover:shadow-cyan-500/20
                       before:absolute before:inset-0 before:-translate-x-full before:animate-[shine_3s_infinite] before:bg-gradient-to-r before:from-transparent before:via-white/10 before:to-transparent">
                        <span
                            class="font-semibold bg-gradient-to-r from-gray-200 to-gray-400 bg-clip-text text-transparent">Productos</span>
                    </NavLink>
                </template>
                <template v-if="$page.props.auth.user">
                    <NavLink :href="route('ventas.ver')" :active="route().current('ventas.ver')"
                        class="relative overflow-hidden px-6 py-2.5 rounded-lg text-gray-300 hover:text-white transition-all duration-300
                       bg-gray-800/50 hover:bg-gray-700/60 backdrop-blur-lg border border-white/10
                       hover:border-cyan-400/30 hover:shadow-lg hover:shadow-cyan-500/20
                       before:absolute before:inset-0 before:-translate-x-full before:animate-[shine_3s_infinite] before:bg-gradient-to-r before:from-transparent before:via-white/10 before:to-transparent">
                        <span
                            class="font-semibold bg-gradient-to-r from-gray-200 to-gray-400 bg-clip-text text-transparent">Tus
                            Compras</span>
                    </NavLink>
                </template>
                <template v-if="$page.props.auth.user">
                    <NavLink :href="route('carrito.ver')" :active="route().current('carrito.ver')"
                        class="relative overflow-hidden px-6 py-2.5 rounded-lg text-gray-300 hover:text-white transition-all duration-300
                       bg-gray-800/50 hover:bg-gray-700/60 backdrop-blur-lg border border-white/10
                       hover:border-cyan-400/30 hover:shadow-lg hover:shadow-cyan-500/20
                       before:absolute before:inset-0 before:-translate-x-full before:animate-[shine_3s_infinite] before:bg-gradient-to-r before:from-transparent before:via-white/10 before:to-transparent">
                        <span
                            class="font-semibold bg-gradient-to-r from-gray-200 to-gray-400 bg-clip-text text-transparent">Tu
                            Carrito</span>
                    </NavLink>
                </template>
                <div class="ms-3 relative" v-if="$page.props.auth.user">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button v-if="$page.props.jetstream.managesProfilePhotos"
                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                <img class="size-8 rounded-full object-cover"
                                    :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </button>
                            <span v-else class="inline-flex rounded-md">
                                <button type="button"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                    {{ $page.props.auth.user.name }}

                                    <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </span>
                        </template>
                        <template #content>
                            <DropdownLink :href="route('profile.show')">
                                Perfil
                            </DropdownLink>
                            <form @submit.prevent="logout">
                                <DropdownLink as="button">
                                    Cerrar Sesión
                                </DropdownLink>
                            </form>
                        </template>
                    </Dropdown>
                </div>
            </nav>
            <button @click="toggleMenu"
                class="md:hidden p-3 rounded-xl backdrop-blur-lg border border-white/10 bg-gray-800/50 hover:bg-gray-700/60 transition-all duration-300">
                <i
                    class="fas fa-bars text-xl bg-gradient-to-r from-cyan-400 to-emerald-400 bg-clip-text text-transparent"></i>
            </button>
        </div>
        <div v-if="menuVisible"
            class="md:hidden mt-4 mx-4 bg-gradient-to-br from-gray-800 to-gray-900/80 backdrop-blur-2xl rounded-2xl border border-white/10 shadow-xl shadow-blue-900/30">
            <div class="p-4 space-y-3">
                <a href="/" class="block px-6 py-3 rounded-xl bg-gray-700/30 hover:bg-gray-600/50 backdrop-blur-lg border border-white/10
                        text-gray-300 hover:text-white transition-all duration-300
                        font-medium bg-gradient-to-r from-gray-200 to-gray-400 bg-clip-text text-transparent">
                    Inicio
                </a>
                <template v-if="$page.props.auth.user">
                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="block px-6 py-3 rounded-xl bg-gray-700/30 hover:bg-gray-600/50 backdrop-blur-lg border border-white/10
                        text-gray-300 hover:text-white transition-all duration-300
                        font-medium bg-gradient-to-r from-gray-200 to-gray-400 bg-clip-text text-transparent">
                        <span
                            class="font-semibold bg-gradient-to-r from-gray-200 to-gray-400 bg-clip-text text-transparent">Productos</span>
                    </NavLink>
                </template>
                <template v-if="$page.props.auth.user">
                    <NavLink :href="route('ventas.ver')" :active="route().current('ventas.ver')"
                        class="relative overflow-hidden px-6 py-2.5 rounded-lg text-gray-300 hover:text-white transition-all duration-300
                       bg-gray-800/50 hover:bg-gray-700/60 backdrop-blur-lg border border-white/10
                       hover:border-cyan-400/30 hover:shadow-lg hover:shadow-cyan-500/20
                       before:absolute before:inset-0 before:-translate-x-full before:animate-[shine_3s_infinite] before:bg-gradient-to-r before:from-transparent before:via-white/10 before:to-transparent">
                        <span
                            class="font-semibold bg-gradient-to-r from-gray-200 to-gray-400 bg-clip-text text-transparent">Tus
                            Compras</span>
                    </NavLink>
                </template>
                <template v-if="$page.props.auth.user">
                    <NavLink :href="route('carrito.ver')" :active="route().current('carrito.ver')"
                        class="relative overflow-hidden px-6 py-2.5 rounded-lg text-gray-300 hover:text-white transition-all duration-300
                       bg-gray-800/50 hover:bg-gray-700/60 backdrop-blur-lg border border-white/10
                       hover:border-cyan-400/30 hover:shadow-lg hover:shadow-cyan-500/20
                       before:absolute before:inset-0 before:-translate-x-full before:animate-[shine_3s_infinite] before:bg-gradient-to-r before:from-transparent before:via-white/10 before:to-transparent">
                        <span
                            class="font-semibold bg-gradient-to-r from-gray-200 to-gray-400 bg-clip-text text-transparent">Tu
                            Carrito</span>
                    </NavLink>
                </template>
                <div class="ms-3 relative" v-if="$page.props.auth.user">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button v-if="$page.props.jetstream.managesProfilePhotos"
                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                <img class="size-8 rounded-full object-cover"
                                    :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </button>
                            <span v-else class="inline-flex rounded-md">
                                <button type="button"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                    {{ $page.props.auth.user.name }}

                                    <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </span>
                        </template>
                        <template #content>
                            <DropdownLink :href="route('profile.show')">
                                Perfil
                            </DropdownLink>
                            <form @submit.prevent="logout">
                                <DropdownLink as="button">
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
    <div
        class="min-h-screen bg-gradient-to-r from-gray-800 via-gray-600 to-gray-900 text-white flex items-center justify-center px-4 py-12">
        <div
            class="w-full max-w-7xl bg-gray-800/60 backdrop-blur-lg rounded-3xl shadow-xl border border-gray-700/40 overflow-visible relative inset-0">
            <div
                class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-cyan-500/10 via-transparent to-transparent animate-pulse">
            </div>
            <div class="relative z-10 text-center p-12">
                <div v-if="categoria.parent" class="mb-6">
                    <nav class="flex justify-center p-3 space-x-2 transition-all duration-300 hover:scale-105">
                        <div
                            class="relative flex items-center px-6 py-3 space-x-2 bg-gray-900/30 backdrop-blur-xl rounded-2xl shadow-2xl shadow-blue-900/30 border-[0.5px] border-white/10 hover:border-blue-400/30 transition-all duration-300 group">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-cyan-500/20 to-blue-600/20 rounded-2xl blur-[2px] opacity-30 group-hover:opacity-50 transition-opacity">
                            </div>
                            <span
                                class="text-sm font-medium text-transparent bg-gradient-to-r from-gray-300 to-gray-400 bg-clip-text">
                                Subcategoría de:
                            </span>
                            <span
                                class="text-lg font-bold bg-gradient-to-r from-cyan-400 via-blue-400 to-indigo-400 bg-clip-text text-transparent animate-text">
                                {{ categoria.parent.nombre }}
                            </span>
                            <div
                                class="absolute inset-0 bg-radial-gradient from-cyan-500/10 via-transparent to-transparent opacity-20 group-hover:opacity-30 transition-opacity">
                            </div>
                        </div>
                    </nav>
                </div>
                <h1
                    class="text-[clamp(2rem,8vw,4rem)] font-black tracking-tight text-center max-w-screen-md mx-auto break-words">
                    <div class="relative inline-block">
                        <div
                            class="absolute inset-0 bg-cyan-900/20 backdrop-blur-3xl rounded-[2rem] -m-4 border border-white/10 shadow-2xl shadow-cyan-900/30">
                        </div>
                        <span
                            class="relative bg-gradient-to-r from-cyan-400 via-blue-300 to-indigo-400 bg-clip-text text-transparent animate-shine">
                            {{ categoria.nombre }}
                        </span>
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-white/30 via-transparent to-cyan-400/20 opacity-40 mix-blend-overlay pointer-events-none">
                        </div>
                    </div>
                </h1>
                <div
                    class="relative text-lg md:text-xl mt-8 max-w-3xl mx-auto p-8 rounded-[1.5rem] border border-white/10 bg-gray-900/40">
                    <div
                        class="absolute inset-0 rounded-[1.5rem] bg-gradient-to-r from-cyan-500/30 to-blue-600/30 opacity-0">
                    </div>
                    <p
                        class="relative bg-gradient-to-br from-gray-200 via-gray-300 to-gray-400 bg-clip-text text-transparent">
                        {{ categoria.descripcion }}
                    </p>
                </div>
            </div>
            <div v-if="productos.data.length > 0"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 p-12 relative z-10">
                <div v-for="producto in productos.data" :key="producto.id"
                    class="group relative rounded-3xl bg-gray-800/60 backdrop-blur-xl border border-gray-700/30 p-6 hover:border-cyan-400/30 transition-all duration-500 hover:shadow-2xl hover:shadow-cyan-500/20">
                    <div
                        class="absolute inset-0 rounded-3xl border-2 border-transparent group-hover:border-cyan-400/20 transition-all duration-300 pointer-events-none">
                    </div>
                    <div class="relative overflow-hidden rounded-2xl bg-gray-900/50">
                        <img :src="'/storage/' + producto.imagen_url" alt="Producto"
                            class="w-full h-64 object-cover transform group-hover:scale-105 transition-transform duration-500">
                        <div v-if="producto.stock === 0"
                            class="absolute top-4 right-4 bg-gradient-to-br from-red-500 to-pink-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                            Agotado
                        </div>
                    </div>
                    <div class="pt-6 space-y-3">
                        <h3 class="text-xl font-bold text-white truncate hover:text-cyan-400 transition-colors">
                            {{ producto.nombre }}
                        </h3>
                        <p class="text-sm text-gray-400 line-clamp-2 leading-relaxed">
                            {{ producto.descripcion }}
                        </p>
                        <p
                            class="text-3xl font-black bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent mt-4">
                            Bs. {{ producto.precio }}
                        </p>
                        <button @click="redirectToPrecompra(producto)"
                            class="w-full mt-4 px-6 py-3 bg-gradient-to-r from-cyan-500/80 to-blue-600/80 text-white font-semibold rounded-xl shadow-lg transition-all duration-300 hover:shadow-cyan-500/40 hover:to-blue-500/90 hover:scale-[1.02]">
                            <i class="fas fa-shopping-cart text-sm"></i>
                            <span> Ver Producto</span>
                        </button>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-16 relative z-10">
                <div class="max-w-md mx-auto bg-gray-900/50 backdrop-blur-md rounded-2xl p-8 shadow-xl">
                    <p class="text-2xl font-light text-gray-400">No hay productos disponibles</p>
                    <div class="mt-4 text-4xl text-gray-600">
                        <i class="fas fa-box-open"></i>
                    </div>
                </div>
            </div>
            <div v-if="productos.links.length > 1"
                class="flex justify-center items-center mt-12 space-x-2 relative z-10">
                <button v-for="link in productos.links.filter(l => !isNaN(l.label))" :key="link.label"
                    @click.prevent="cambiarPagina(link.url)"
                    class="px-5 py-2.5 min-w-[40px] text-sm font-semibold rounded-xl transition-all duration-300"
                    :class="[
                        link.active
                            ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-lg shadow-cyan-500/30'
                            : 'bg-gray-800/50 text-gray-300 hover:bg-gray-700/70 hover:text-white'
                    ]">
                    {{ link.label }}
                </button>
            </div>
        </div>
    </div>
    <!--pie de pagina-->
    <footer class="relative py-8 text-center overflow-hidden">
        <div
            class="absolute inset-0 bg-gradient-to-br from-gray-900 via-slate-900 to-black backdrop-blur-xl opacity-95">
        </div>
        <div class="relative z-10 flex flex-col items-center space-y-3">
            <p
                class="text-xl sm:text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-400 animate-gradient-x">
                Poliméricos Dial Bolivia
            </p>
            <p
                class="text-sm sm:text-base font-medium text-gray-300/90 hover:text-gray-100 transition-all duration-300">
                &copy; {{ new Date().getFullYear() }} Todos los derechos reservados
            </p>
            <div class="absolute top-0 w-full h-px bg-gradient-to-r from-transparent via-blue-400/50 to-transparent">
            </div>
            <div
                class="absolute -top-20 left-0 w-48 h-48 bg-gradient-to-r from-blue-600/20 to-purple-600/20 rounded-full blur-3xl opacity-30">
            </div>
        </div>
    </footer>
</template>