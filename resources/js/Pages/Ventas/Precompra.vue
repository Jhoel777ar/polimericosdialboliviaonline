<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';
import Swal from 'sweetalert2';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import Lenis from 'lenis';

const props = defineProps({
    producto: Object,
    productosRelacionados: Array
});

const cantidad = ref(1);
const total = ref((props.producto.precio * cantidad.value).toFixed(2));
const maxCantidad = ref(props.producto.stock);
const currentPage = ref(1);
const itemsPerPage = 4;
const selectedImage = ref(null);
const isModalOpen = ref(false);
const zoomLevel = ref(1);
const modalImagePosition = ref({ x: 0, y: 0 });

const calcularTotal = () => {
    if (cantidad.value > maxCantidad.value) {
        Swal.fire({
            icon: "error",
            title: "Cantidad no válida",
            text: `La cantidad no puede exceder el stock disponible (${maxCantidad.value}).`,
        });
        cantidad.value = maxCantidad.value;
    }
    total.value = (props.producto.precio * cantidad.value).toFixed(2);
};

const productosPorPagina = computed(() => {
    if (props.productosRelacionados) {
        const startIndex = (currentPage.value - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        return props.productosRelacionados.slice(startIndex, endIndex);
    }
    return [];
});

const tieneSiguiente = computed(() => {
    if (props.productosRelacionados) {
        return currentPage.value * itemsPerPage < props.productosRelacionados.length;
    }
    return false;
});

const tieneAnterior = computed(() => {
    return currentPage.value > 1;
});

const siguientePagina = () => {
    if (tieneSiguiente.value) {
        currentPage.value++;
    }
};

const anteriorPagina = () => {
    if (tieneAnterior.value) {
        currentPage.value--;
    }
};

const redirectToPrecompra = (producto) => {
    Inertia.get(`/precompra/${producto.id}`);
};

const agregarAlCarrito = async () => {
    try {
        const response = await axios.post(`/carrito/${props.producto.id}`, {
            cantidad: cantidad.value,
        });

        if (response.data.success) {
            await Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: response.data.success,
            });
            window.location.href = response.data.redirect_url;
        }
    } catch (error) {
        const errorMessage = error.response?.data?.error || 'Ha ocurrido un error inesperado.';
        const result = await Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessage,
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Ir al carrito',
            reverseButtons: true,
        });
        if (result.dismiss === Swal.DismissReason.cancel) {
            window.location.href = '/carrito';
        }
    }
};

const openModal = (image) => {
    selectedImage.value = image;
    isModalOpen.value = true;
    zoomLevel.value = 1;
    modalImagePosition.value = { x: 0, y: 0 };
};

const closeModal = () => {
    isModalOpen.value = false;
    selectedImage.value = null;
};

const zoomIn = () => {
    zoomLevel.value = Math.min(zoomLevel.value + 0.5, 3);
};

const zoomOut = () => {
    zoomLevel.value = Math.max(zoomLevel.value - 0.5, 1);
};

const handleMouseMove = (event) => {
    if (zoomLevel.value > 1) {
        const rect = event.target.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;
        modalImagePosition.value = {
            x: (x / rect.width - 0.5) * 100,
            y: (y / rect.height - 0.5) * 100,
        };
    }
};

const menuVisible = ref(false);
const toggleMenu = () => {
    menuVisible.value = !menuVisible.value;
};

const logout = () => {
    router.post(route('logout'));
};

let lenis;

onMounted(() => {
    lenis = new Lenis({
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        smooth: true,
    });

    function raf(time) {
        lenis.raf(time);
        requestAnimationFrame(raf);
    }

    requestAnimationFrame(raf);
});

onBeforeUnmount(() => {
    if (lenis) lenis.destroy();
});

watch(cantidad, calcularTotal);
</script>

<template>

    <Head :title="props.producto?.nombre" />
    <header
        class="bg-gradient-to-br from-gray-950/95 via-gray-900/95 to-black/90 text-white shadow-2xl py-4 backdrop-blur-xl border-b border-gray-800/50">
        <div class="container mx-auto flex justify-between items-center px-4 lg:px-8">
            <div class="group relative">
                <div
                    class="absolute -inset-2 bg-gradient-to-r from-cyan-500 via-teal-500 to-emerald-500 rounded-xl blur-md opacity-20 group-hover:opacity-40 transition-all duration-500 animate-pulse">
                </div>
                <a href="/" class="relative text-3xl md:text-4xl font-extrabold tracking-tight bg-gradient-to-r from-cyan-400 via-teal-400 to-emerald-400 bg-clip-text text-transparent 
               hover:animate-pulse transition-all duration-300 z-10">
                    Detalles del Producto
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

    <div
        class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-black text-white px-4 py-12 relative overflow-hidden">
        <div
            class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 via-teal-500/10 to-emerald-500/10 animate-gradient-x pointer-events-none">
        </div>
        <div
            class="container mx-auto max-w-7xl bg-gradient-to-br from-gray-900/80 to-black/80 backdrop-blur-2xl rounded-3xl shadow-2xl border border-gray-800/50 p-6 md:p-12 relative z-10">
            <h2
                class="text-3xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-teal-400 to-emerald-400 text-center mb-12 drop-shadow-lg">
                {{ props.producto?.nombre }}
            </h2>
            <div class="flex flex-col lg:flex-row gap-12">
                <div class="lg:w-1/2 w-full flex justify-center relative group">
                    <div
                        class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-gray-950/90 to-black/90 p-6 shadow-xl border border-gray-800/50 backdrop-blur-md transition-all duration-500 group-hover:shadow-cyan-500/20">
                        <img :src="'/storage/' + props.producto?.imagen_url" alt="Imagen del producto"
                            class="w-full h-auto object-contain max-h-[500px] rounded-xl shadow-lg transition-transform duration-500 ease-in-out transform group-hover:scale-105 cursor-pointer"
                            @click="openModal('/storage/' + props.producto?.imagen_url)" />
                        <div
                            class="absolute top-4 right-4 bg-gradient-to-r from-cyan-500 to-teal-500 p-2 rounded-full text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <i class="fas fa-search-plus text-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="lg:w-1/2 w-full flex flex-col space-y-8">
                    <div
                        class="bg-gradient-to-br from-gray-900/80 to-black/80 backdrop-blur-xl rounded-2xl p-8 shadow-xl border border-gray-800/50 transition-all duration-300 hover:shadow-cyan-500/20">
                        <h3 class="text-2xl font-semibold text-gray-100 mb-4">Descripción</h3>
                        <p class="text-base text-gray-300 leading-relaxed">{{ props.producto?.descripcion }}</p>
                    </div>
                    <div
                        class="bg-gradient-to-br from-gray-900/80 to-black/80 backdrop-blur-xl rounded-2xl p-8 shadow-xl border border-gray-800/50 transition-all duration-300 hover:shadow-cyan-500/20">
                        <h3 class="text-2xl font-semibold text-gray-100 mb-6 flex items-center gap-3">
                            <i class="fas fa-cogs text-teal-400"></i>
                            Especificaciones Técnicas
                        </h3>
                        <ul class="text-base text-gray-400 space-y-4">
                            <li class="flex items-start gap-3 group">
                                <i
                                    class="fas fa-money-bill-wave text-green-400 mt-1 transition-colors duration-300 group-hover:text-green-300"></i>
                                <div>
                                    <strong class="text-gray-200">Precio:</strong>
                                    <span class="text-gray-300">Bs. {{ props.producto?.precio }}</span>
                                </div>
                            </li>
                            <li class="flex items-start gap-3 group">
                                <i
                                    class="fas fa-folder-open text-purple-400 mt-1 transition-colors duration-300 group-hover:text-purple-300"></i>
                                <div>
                                    <strong class="text-gray-200">Categoría:</strong>
                                    <span class="text-gray-300" v-if="props.producto?.categoria?.parent">
                                        {{ props.producto.categoria.parent.nombre }} > {{
                                            props.producto.categoria.nombre }}
                                    </span>
                                    <span class="text-gray-300" v-else>
                                        {{ props.producto?.categoria?.nombre }}
                                    </span>
                                </div>
                            </li>
                            <li class="flex items-start gap-3 group">
                                <i
                                    class="fas fa-truck text-yellow-400 mt-1 transition-colors duration-300 group-hover:text-yellow-300"></i>
                                <div>
                                    <strong class="text-gray-200">Proveedor:</strong>
                                    <span class="text-gray-300">{{ props.producto?.proveedor?.nombre }}</span>
                                </div>
                            </li>
                            <li v-if="props.producto?.color" class="flex items-start gap-3 group">
                                <i
                                    class="fas fa-palette text-pink-400 mt-1 transition-colors duration-300 group-hover:text-pink-300"></i>
                                <div>
                                    <strong class="text-gray-200">Color:</strong>
                                    <span class="text-gray-300">{{ props.producto.color.color }}</span>
                                </div>
                            </li>
                            <li class="flex items-start gap-3 group">
                                <i
                                    class="fas fa-tag text-teal-400 mt-1 transition-colors duration-300 group-hover:text-teal-300"></i>
                                <div>
                                    <strong class="text-gray-200">ID del Producto:</strong>
                                    <span class="text-gray-300">{{ props.producto?.id }}</span>
                                </div>
                            </li>
                            <li class="flex items-start gap-3 group">
                                <i
                                    class="fas fa-info-circle text-blue-400 mt-1 transition-colors duration-300 group-hover:text-blue-300"></i>
                                <div>
                                    <strong class="text-gray-200">Estado:</strong>
                                    <span
                                        :class="{ 'text-green-400': props.producto?.estado === 'disponible', 'text-red-400': props.producto?.estado === 'descontinuado' }"
                                        class="font-medium">
                                        {{ props.producto?.estado }}
                                    </span>
                                </div>
                            </li>
                            <li v-if="props.producto?.stock > 0" class="flex items-start gap-3 group">
                                <i
                                    class="fas fa-check-circle text-green-400 mt-1 transition-colors duration-300 group-hover:text-green-300"></i>
                                <div>
                                    <strong class="text-gray-200">Stock:</strong>
                                    <span class="text-gray-300">{{ props.producto?.stock }}</span>
                                </div>
                            </li>
                            <li v-else class="flex items-start gap-3 text-red-400 group">
                                <i
                                    class="fas fa-times-circle text-red-400 mt-1 transition-colors duration-300 group-hover:text-red-300"></i>
                                <div>Agotado</div>
                            </li>
                        </ul>
                    </div>
                    <div v-if="props.producto.activo === 0"
                        class="bg-gradient-to-br from-red-900/80 to-red-800/80 backdrop-blur-xl rounded-2xl p-8 shadow-xl border border-red-700/50">
                        <h3 class="text-2xl font-semibold text-red-400 mb-4">Producto no habilitado</h3>
                        <p class="text-base text-gray-300">Este producto no está disponible para la compra en este
                            momento.</p>
                    </div>
                    <div v-if="props.producto?.activo === 1" class="flex flex-col gap-6">
                        <div
                            class="bg-gradient-to-br from-gray-900/80 to-black/80 backdrop-blur-xl rounded-2xl p-8 shadow-xl border border-gray-800/50">
                            <label for="cantidad" class="text-base text-gray-300 font-semibold">Cantidad</label>
                            <select id="cantidad"
                                class="mt-3 p-3 w-full rounded-xl bg-gray-800 text-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all duration-300"
                                v-model="cantidad" :disabled="props.producto?.stock <= 0" @change="calcularTotal">
                                <option v-for="i in maxCantidad" :key="i" :value="i">{{ i }}</option>
                            </select>
                        </div>
                        <div
                            class="bg-gradient-to-br from-gray-900/80 to-black/80 backdrop-blur-xl rounded-2xl p-8 shadow-xl border border-gray-800/50">
                            <h3 class="text-2xl font-semibold text-gray-100 mb-4">Total: Bs. {{ total }}</h3>
                        </div>
                        <button @click="agregarAlCarrito"
                            class="w-full py-4 rounded-2xl text-white font-semibold text-lg shadow-xl transition-all duration-300 bg-gradient-to-r from-green-500 to-cyan-600 hover:from-green-400 hover:to-cyan-500 hover:shadow-cyan-500/20"
                            :class="{ 'bg-gray-600 cursor-not-allowed': props.producto?.stock <= 0 }"
                            :disabled="props.producto?.stock <= 0 || props.producto?.activo === 0">
                            {{ props.producto?.stock > 0 && props.producto?.activo === 1 ? 'Comprar' : 'Agotado' }}
                            <i class="fas fa-shopping-cart ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="mt-16 px-6 py-10 bg-gradient-to-br from-gray-900/90 to-black/90 rounded-2xl shadow-2xl border border-gray-800/50">
                <h3
                    class="text-3xl md:text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-teal-400 to-emerald-400 mb-8 text-center">
                    Productos Relacionados
                </h3>
                <div v-if="!props.productosRelacionados || props.productosRelacionados.length === 0"
                    class="text-center text-gray-400">
                    <p>No hay productos relacionados</p>
                </div>
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 px-4">
                    <div v-for="relacionado in productosPorPagina" :key="relacionado.id"
                        class="relative bg-gradient-to-br from-gray-950/90 to-black/90 backdrop-blur-xl rounded-2xl p-6 shadow-xl border border-gray-800/50 group hover:shadow-2xl hover:border-cyan-500/30 transition-all duration-300 hover:-translate-y-2">
                        <img :src="'/storage/' + relacionado?.imagen_url" alt="Imagen de producto relacionado"
                            class="w-full h-48 object-contain mb-6 rounded-xl shadow-md transition-transform duration-300 group-hover:scale-105 cursor-pointer"
                            @click="openModal('/storage/' + relacionado?.imagen_url)" />
                        <div v-if="relacionado?.stock === 0"
                            class="absolute top-2 left-2 bg-gradient-to-r from-red-600/90 to-red-400/90 text-white text-sm font-semibold px-4 py-1 rounded-full flex items-center gap-2 shadow-md backdrop-blur-sm">
                            <i class="fas fa-ban text-red-200"></i> Agotado
                        </div>
                        <div class="space-y-3">
                            <h4 class="text-xl font-bold text-white flex items-start gap-2 break-words max-w-full">
                                <i class="fas fa-box-open text-cyan-400 mt-1 shrink-0"></i>
                                <span class="flex-1 break-words overflow-hidden">{{ relacionado?.nombre }}</span>
                            </h4>
                            <p class="text-lg text-gray-300 flex items-center gap-2">
                                <i class="fas fa-money-bill-wave text-green-400"></i>
                                Bs. {{ relacionado?.precio }}
                            </p>
                            <p class="text-lg text-gray-300 flex items-center gap-2">
                                <i class="fas fa-tag text-teal-400"></i>
                                ID {{ relacionado?.id }}
                            </p>
                            <p v-if="relacionado?.stock > 0" class="text-green-400 font-medium flex items-center gap-2">
                                <i class="fas fa-check-circle"></i>
                                Stock: {{ relacionado?.stock }}
                            </p>
                            <p v-else class="text-red-400 font-medium flex items-center gap-2">
                                <i class="fas fa-times-circle"></i>
                                Agotado
                            </p>
                            <p class="text-sm text-gray-400 flex items-start gap-2 break-words max-w-full">
                                <i class="fas fa-truck text-yellow-400 mt-1 shrink-0"></i>
                                <span class="shrink-0">Proveedor:</span>
                                <span class="flex-1 break-words overflow-hidden">{{ relacionado?.proveedor?.nombre
                                    }}</span>
                            </p>
                            <p class="text-sm text-gray-400 flex items-start gap-2 break-words max-w-full">
                                <i class="fas fa-folder-open text-purple-400 mt-1 shrink-0"></i>
                                <span class="shrink-0">Categoría:</span>
                                <span class="flex-1 break-words overflow-hidden">{{ relacionado?.categoria?.nombre
                                    }}</span>
                            </p>
                            <p class="text-sm text-gray-400 flex items-start gap-2 break-words max-w-full">
                                <i class="fas fa-palette text-pink-400 mt-1 shrink-0"></i>
                                <span class="shrink-0">Color:</span>
                                <span class="flex-1 break-words overflow-hidden">{{ relacionado?.color.color }}</span>
                            </p>
                        </div>
                        <button @click="redirectToPrecompra(relacionado)"
                            class="mt-6 w-full px-6 py-3 bg-gradient-to-r from-teal-600 to-cyan-600 text-white rounded-xl font-medium shadow-md flex items-center justify-center gap-2 transition-all duration-300 hover:from-teal-500 hover:to-cyan-500 hover:shadow-xl hover:scale-105">
                            <i class="fas fa-shopping-cart text-teal-200 group-hover:animate-pulse"></i>
                            Ver Producto
                        </button>
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-teal-500/10 to-cyan-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-2xl pointer-events-none">
                        </div>
                    </div>
                </div>
                <div class="flex justify-between mt-12">
                    <button
                        class="text-white bg-gradient-to-r from-blue-500 to-cyan-600 py-3 px-8 rounded-xl shadow-md hover:shadow-lg hover:from-blue-400 hover:to-cyan-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-105"
                        :disabled="!tieneAnterior" @click="anteriorPagina">
                        <i class="fas fa-arrow-left mr-2"></i>Anterior
                    </button>
                    <button
                        class="text-white bg-gradient-to-r from-green-500 to-teal-600 py-3 px-8 rounded-xl shadow-md hover:shadow-lg hover:from-green-400 hover:to-teal-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-105"
                        :disabled="!tieneSiguiente" @click="siguientePagina">
                        Siguiente<i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>

        <div v-if="isModalOpen" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50"
            @click="closeModal">
            <div class="relative bg-gradient-to-br from-gray-900/90 to-black/90 rounded-2xl p-6 max-w-4xl w-full mx-4 shadow-2xl border border-gray-800/50"
                @click.stop>
                <button @click="closeModal"
                    class="absolute top-4 right-4 text-white bg-gradient-to-r from-red-500 to-red-600 p-2 rounded-full hover:from-red-400 hover:to-red-500 transition-all duration-300">
                    <i class="fas fa-times text-lg"></i>
                </button>
                <div class="relative overflow-hidden rounded-xl">
                    <img :src="selectedImage" alt="Imagen ampliada"
                        class="w-full h-auto max-h-[70vh] object-contain transition-transform duration-300"
                        :style="{ transform: `scale(${zoomLevel}) translate(${modalImagePosition.x}%, ${modalImagePosition.y}%)` }"
                        @mousemove="handleMouseMove" />
                </div>
                <div class="flex justify-center gap-4 mt-4">
                    <button @click="zoomIn"
                        class="text-white bg-gradient-to-r from-cyan-500 to-teal-600 py-2 px-4 rounded-xl hover:from-cyan-400 hover:to-teal-500 transition-all duration-300">
                        <i class="fas fa-search-plus"></i>
                    </button>
                    <button @click="zoomOut"
                        class="text-white bg-gradient-to-r from-cyan-500 to-teal-600 py-2 px-4 rounded-xl hover:from-cyan-400 hover:to-teal-500 transition-all duration-300">
                        <i class="fas fa-search-minus"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <footer class="relative py-8 text-center overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-950 backdrop-blur-xl opacity-95">
        </div>
        <div class="relative z-10 flex flex-col items-center space-y-3">
            <p
                class="text-xl sm:text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-teal-400 to-emerald-400 animate-gradient-x">
                Poliméricos Dial Bolivia
            </p>
            <p
                class="text-sm sm:text-base font-medium text-gray-300/90 hover:text-gray-100 transition-all duration-300">
                © {{ new Date().getFullYear() }} Todos los derechos reservados
            </p>
            <div class="absolute top-0 w-full h-px bg-gradient-to-r from-transparent via-cyan-400/50 to-transparent">
            </div>
            <div
                class="absolute -top-20 left-0 w-48 h-48 bg-gradient-to-r from-cyan-600/20 to-teal-600/20 rounded-full blur-3xl opacity-30">
            </div>
        </div>
    </footer>
</template>

<style>
body {
    background: linear-gradient(45deg, #0a0a0a, #1a1a2a, #2a3a4a, #0a1a2a);
    background-size: 300% 300%;
    animation: gradientMove 8s ease-in-out infinite;
    margin: 0;
    font-family: 'Inter', sans-serif;
}

@keyframes gradientMove {
    0% {
        background-position: 0% 0%;
    }

    25% {
        background-position: 100% 0%;
    }

    50% {
        background-position: 0% 100%;
    }

    75% {
        background-position: 100% 100%;
    }

    100% {
        background-position: 0% 0%;
    }
}

@keyframes shine {
    0% {
        transform: translateX(-100%);
    }

    100% {
        transform: translateX(100%);
    }
}

.animate-gradient-x {
    background-size: 200% 200%;
    animation: gradientMove 4s ease-in-out infinite;
}
</style>