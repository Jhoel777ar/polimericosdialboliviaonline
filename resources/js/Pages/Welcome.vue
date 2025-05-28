<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount, watch, computed } from 'vue';
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';
import Swal from 'sweetalert2';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
// efecto carga inicio
const isLoading = ref(true);
const loadingText = ref('Cargando...');
// carusel de imagens y categorias
const currentSlide = ref(0);
const images = ref([]);
const categorias = ref([]);
const errorMessage = ref('');
const categoriasError = ref('');
const currentStartIndex = ref(0);
const categoriesPerPage = 4;
const totalCategorias = ref(0);
let slideInterval;
async function fetchImages() {
    try {
        const response = await axios.get('/inicio-imagenes');
        images.value = response.data;
        errorMessage.value = '';
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Hubo un error al cargar las imágenes.';
    }
}
async function fetchCategorias() {
    try {
        const response = await axios.get('/categorias', {
            params: { start: currentStartIndex.value, limit: categoriesPerPage },
        });
        categorias.value = response.data.categories.map(categoria => ({
            ...categoria,
            expanded: false,
        }));
        totalCategorias.value = response.data.total;
        categoriasError.value = response.data.message || '';
    } catch (error) {
        categoriasError.value = error.response?.data?.message || 'Hubo un error al cargar las categorías.';
    }
}
function nextSlide() {
    currentSlide.value = (currentSlide.value + 1) % images.value.length;
}
function prevSlide() {
    currentSlide.value = (currentSlide.value - 1 + images.value.length) % images.value.length;
}
function nextCategories() {
    if (currentStartIndex.value + categoriesPerPage < totalCategorias.value) {
        currentStartIndex.value += categoriesPerPage;
        fetchCategorias();
    }
}
function prevCategories() {
    if (currentStartIndex.value - categoriesPerPage >= 0) {
        currentStartIndex.value -= categoriesPerPage;
        fetchCategorias();
    }
}
onMounted(() => {
    fetchImages();
    fetchCategorias();
    slideInterval = setInterval(nextSlide, 5000);
    //efecto de carga inicio
    setTimeout(() => {
        loadingText.value = '¡Bienvenidos a Polimericos Dial Bolivia!';
        setTimeout(() => {
            isLoading.value = false;
        }, 1000);
    }, 4000);
});
onBeforeUnmount(() => {
    clearInterval(slideInterval);
});

// productos

function debounce(func, wait) {
    let timeout;
    return function (...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}
const productos = ref([]);
const totalProductos = ref(0);
const offset = ref(0);
const limit = 8;
const loading = ref(false);
const searchQuery = ref('');
const sortOption = ref('');
const cache = new Map();
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            observer.unobserve(img);
        }
    });
});
async function fetchProductos() {
    const cacheKey = `${searchQuery.value}_${sortOption.value}_${offset.value}`;
    if (cache.has(cacheKey)) {
        const cachedData = cache.get(cacheKey);
        productos.value = offset.value === 0 ? cachedData.productos : productos.value.concat(cachedData.productos);
        totalProductos.value = cachedData.total;
        offset.value += limit;
        return;
    }
    if (loading.value) return;
    loading.value = true;
    try {
        const response = await axios.get('/productos', {
            params: { limit, offset: offset.value, search: searchQuery.value, sort: sortOption.value }
        });
        const fetchedData = {
            productos: response.data.productos,
            total: response.data.total
        };
        cache.set(cacheKey, fetchedData);
        if (offset.value === 0) {
            productos.value = fetchedData.productos;
        } else {
            productos.value.push(...fetchedData.productos);
        }
        totalProductos.value = fetchedData.total;
        offset.value += limit;
        lazyLoadImages();
    } catch (error) {
        console.error('Error fetching productos:', error);
    } finally {
        loading.value = false;
    }
}
function lazyLoadImages() {
    document.querySelectorAll('img[data-src]').forEach(img => {
        observer.observe(img);
    });
}
const debouncedFetchProductos = debounce(() => {
    offset.value = 0;
    cache.clear();
    productos.value = [];
    fetchProductos();
}, 300);
onMounted(() => {
    fetchProductos();
});
watch([searchQuery, sortOption], ([newSearchQuery]) => {
    if (newSearchQuery === '') {
        offset.value = 0;
        productos.value = [];
        fetchProductos();
    } else {
        debouncedFetchProductos();
    }
});

//llevar al carrito
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
        .catch((error) => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un problema al redirigir al producto.',
            });
        });
};
//categoria redirect
function redirectToCategoria(categoriaId) {
    axios
        .get(`/categorias/${categoriaId}`)
        .then((response) => {
            Inertia.get(`/categorias/${categoriaId}`);
        })
        .catch((error) => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.response?.data?.error || 'Hubo un problema al cargar la categoría.',
            });
        });
};

const logoPath = computed(() => "/storage/logo.png");
//chat bot
onMounted(() => {
    const script1 = document.createElement('script');
    script1.src = "https://cdn.botpress.cloud/webchat/v2.2/inject.js";
    script1.async = true;
    document.body.appendChild(script1);
    const script2 = document.createElement('script');
    script2.src = "https://files.bpcontent.cloud/2025/02/12/21/20250212211027-GHNKJLUA.js";
    script2.async = true;
    document.body.appendChild(script2);
});

</script>

<template>

    <Head title="Welcome" />
    <transition name="hologram">
        <div v-if="isLoading"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-br from-gray-900 via-[#1a0933] to-[#0d011c] backdrop-blur-2xl">
            <div class="text-center space-y-8">
                <div class="relative mx-auto w-32 h-32">
                    <div
                        class="absolute inset-0 bg-[conic-gradient(from_180deg_at_50%_50%,hsl(189,100%,50%)_15%,hsl(295,100%,50%)_35%,transparent_60%)] rounded-full animate-spin shadow-[0_0_40px_-10px_rgba(147,51,234,0.5)]">
                    </div>
                    <div
                        class="absolute inset-2 bg-gradient-to-br from-gray-900/80 to-purple-950/80 rounded-full backdrop-blur-xl border border-white/10">
                    </div>
                </div>
                <h2 class="text-4xl md:text-5xl font-bold tracking-wider animate-pulse">
                    <span
                        class="text-transparent bg-clip-text bg-[linear-gradient(90deg,theme(colors.cyan.400)_0%,theme(colors.purple.400)_50%,theme(colors.pink.400)_100%)] bg-[length:200%_auto] animate-text-glow">
                        {{ loadingText }}
                    </span>
                </h2>
            </div>
        </div>
    </transition>
    <header
        class="fixed top-0 left-0 w-full bg-gradient-to-br from-indigo-900/70 via-navy-800/50 to-cyan-900/30 backdrop-blur-3xl z-40 py-3 px-4 sm:px-6 transition-all duration-500 shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] border-b border-indigo-400/20 hover:shadow-[0_12px_40px_0_rgba(16,94,245,0.2)] group/header">
        <div class="flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center space-x-2">
                <div class="flex items-center justify-center relative">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-cyan-500 rounded-full opacity-20 blur-[30px] animate-pulse">
                    </div>
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-indigo-400/40 to-cyan-300/30 rounded-full opacity-15 blur-[15px] animate-spin-slow">
                    </div>
                    <img :src="logoPath" alt="Logo"
                        class="logo w-14 h-14 transform transition-all duration-500 hover:scale-110 hover:rotate-[8deg] border-2 border-indigo-400/30 rounded-full p-1 shadow-glow-blue">
                </div>
                <span
                    class="hidden sm:block text-transparent text-base font-semibold bg-clip-text bg-gradient-to-r from-indigo-200 to-cyan-400 tracking-wider">
                    Polimericos Dial de Bolivia
                </span>
            </div>
            <nav
                class="flex items-center space-x-4 sm:space-x-6 bg-indigo-900/30 backdrop-blur-3xl py-2 px-4 sm:px-6 rounded-full shadow-neon-lg transition-all duration-500 border border-indigo-400/20 hover:border-cyan-300/30 relative before:absolute before:inset-0 before:bg-gradient-to-r before:from-indigo-500/20 before:via-transparent before:to-cyan-400/20 before:animate-shine before:-z-10">
                <Link v-if="canLogin && !$page.props.auth.user" :href="route('login')"
                    class="text-sm text-cyan-100 hover:text-white flex items-center justify-center px-3 py-2 rounded-lg border border-indigo-400/30 hover:border-cyan-300/50 bg-gradient-to-br from-indigo-900/50 to-cyan-900/30 hover:from-indigo-800/70 hover:to-cyan-800/50 transition-all duration-300 hover:scale-[1.03] active:scale-95 shadow-sm hover:shadow-glow-cyan group/btn">
                <i class="fas fa-sign-in-alt mr-2 text-cyan-300 group-hover/btn:text-cyan-100"></i>Inicia sesión
                </Link>
                <Link v-if="canRegister && !$page.props.auth.user" :href="route('register')"
                    class="text-sm text-cyan-100 hover:text-white flex items-center justify-center px-3 py-2 rounded-lg border border-indigo-400/30 hover:border-cyan-300/50 bg-gradient-to-br from-indigo-900/50 to-cyan-900/30 hover:from-indigo-800/70 hover:to-cyan-800/50 transition-all duration-300 hover:scale-[1.03] active:scale-95 shadow-sm hover:shadow-glow-cyan group/btn">
                <i class="fas fa-user-plus mr-2 text-indigo-300 group-hover/btn:text-indigo-100"></i> Registrarse
                </Link>
                <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                    class="text-sm text-cyan-100 hover:text-white flex items-center justify-center px-3 py-2 rounded-lg border border-indigo-400/30 hover:border-cyan-300/50 bg-gradient-to-br from-indigo-900/50 to-cyan-900/30 hover:from-indigo-800/70 hover:to-cyan-800/50 transition-all duration-300 hover:scale-[1.03] active:scale-95 shadow-sm hover:shadow-glow-cyan group/btn">
                <i class="fas fa-tachometer-alt mr-2 text-green-300 group-hover/btn:text-green-100"></i> Ingresa al
                Panel
                </Link>
            </nav>
        </div>
        <div class="absolute inset-0 -z-10 opacity-20">
            <div class="absolute w-2 h-2 bg-cyan-400/50 rounded-full top-1/4 left-1/4 animate-particle"></div>
            <div class="absolute w-1.5 h-1.5 bg-indigo-400/50 rounded-full top-1/3 right-1/4 animate-particle-delay">
            </div>
        </div>
    </header>
    <main>
        <!-- Carrusel de imágenes mejorado -->
        <div class="relative w-full h-[35vh] sm:h-[75vh] md:h-[87vh] lg:h-[99vh] overflow-hidden group/carousel">
            <div
                class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-transparent z-10 pointer-events-none">
            </div>
            <div v-if="errorMessage"
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center z-30">
                <div
                    class="bg-gradient-to-br from-red-600/90 to-rose-800/90 backdrop-blur-xl px-8 py-4 rounded-2xl shadow-2xl border border-white/10 animate-pulse-slow">
                    <p class="text-white/90 font-medium text-sm sm:text-base">{{ errorMessage }}</p>
                </div>
            </div>
            <div v-else
                class="relative w-full h-full transform transition-transform duration-1000 ease-[cubic-bezier(0.25,0.1,0.25,1)]">
                <img v-for="(image, index) in images" :key="index" :src="'/storage/' + image" :class="['absolute inset-0 w-full h-full object-cover transform transition-all duration-1000 ease-[cubic-bezier(0.25,0.1,0.25,1)]',
                    {
                        'opacity-100 scale-100 z-20': index === currentSlide,
                        'opacity-0 scale-105 -translate-y-2': index !== currentSlide
                    }]" :alt="'Slide ' + (index + 1)" loading="lazy" />
            </div>
            <button @click="prevSlide"
                class="absolute top-1/2 left-4 sm:left-6 transform -translate-y-1/2 z-30 w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-gradient-to-br from-black/60 to-black/40 backdrop-blur-3xl rounded-full border border-white/10 shadow-2xl hover:shadow-glow transition-all duration-300 hover:scale-110 hover:border-white/30 group/nav">
                <span
                    class="text-white/90 text-2xl sm:text-3xl font-light group-hover/nav:text-white transition-all">‹</span>
            </button>
            <button @click="nextSlide"
                class="absolute top-1/2 right-4 sm:right-6 transform -translate-y-1/2 z-30 w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-gradient-to-br from-black/60 to-black/40 backdrop-blur-3xl rounded-full border border-white/10 shadow-2xl hover:shadow-glow transition-all duration-300 hover:scale-110 hover:border-white/30 group/nav">
                <span
                    class="text-white/90 text-2xl sm:text-3xl font-light group-hover/nav:text-white transition-all">›</span>
            </button>
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-30 flex space-x-2">
                <div v-for="(_, index) in images" :key="index" @click="setSlide(index)"
                    class="w-3 h-3 rounded-full cursor-pointer transition-all duration-500 transform hover:scale-125"
                    :class="{
                        'bg-white/90 backdrop-blur-lg border border-white/50 shadow-glow-inner': index === currentSlide,
                        'bg-white/30 backdrop-blur-sm hover:bg-white/50': index !== currentSlide
                    }"></div>
            </div>
        </div>
        <!-- Carrusel de Categorías -->
        <div class="mt-12 text-center relative px-4">
            <h2 class="text-2xl md:text-4xl font-extrabold mb-8 relative group">
                <span
                    class="inline-block py-3 px-4 md:px-6 rounded-2xl bg-gradient-to-br from-black/80 via-blue-900/60 to-purple-900/70 backdrop-blur-xl backdrop-saturate-150 border border-white/10 border-b-white/20 shadow-[0_16px_32px_-10px_rgba(0,0,0,0.3)] hover:shadow-[0_24px_48px_-12px_rgb(45,45,245,0.4)] transition-all duration-300 hover:-translate-y-0.5">
                    <span
                        class="bg-gradient-to-r from-blue-400 via-purple-300 to-pink-300 bg-clip-text text-transparent animate-text-shine relative">
                        Nuestras Categorías
                        <span
                            class="absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-blue-400/70 via-purple-400/70 to-pink-400/70 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                    </span>
                </span>
                <span
                    class="absolute inset-0 -z-10 bg-[conic-gradient(at_top_left,_var(--tw-gradient-stops))] from-blue-900/30 via-purple-900/30 to-pink-900/30 blur-2xl opacity-60 group-hover:opacity-80 transition-opacity duration-300"></span>
            </h2>
            <div v-if="categoriasError"
                class="text-center text-white bg-gradient-to-br from-red-500 to-black/50 p-4 rounded-lg shadow-lg">
                <p>{{ categoriasError }}</p>
            </div>
            <div class="relative">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
                    <div v-for="(categoria, index) in categorias" :key="categoria.id"
                        class="bg-gradient-to-br from-black/50 via-gray-900 to-black/30 rounded-2xl shadow-xl backdrop-blur-md p-4 hover:scale-105 transform transition-all duration-300 relative">
                        <div class="relative rounded-lg overflow-hidden aspect-[16/9]">
                            <img :src="'/storage/' + categoria.imagen_url" alt="Imagen de Categoria"
                                class="w-full h-full object-cover rounded-md transition-transform duration-500 hover:scale-110">
                        </div>
                        <div class="mt-4 text-white">
                            <h3 class="text-xl font-semibold text-center truncate">
                                {{ categoria.nombre }}
                            </h3>
                            <p v-if="categoria.parent" class="text-sm text-green-400 text-center mt-2">
                                Subcategoría de <span class="font-bold text-green-500">{{ categoria.parent.nombre
                                    }}</span>
                            </p>
                        </div>
                        <div class="mt-4 bg-black/30 rounded-lg p-4 overflow-hidden">
                            <p class="text-white/80 text-center transition-all duration-300"
                                :class="{ 'line-clamp-3': !categoria.expanded }">
                                {{ categoria.descripcion }}
                            </p>
                            <button @click="categoria.expanded = !categoria.expanded"
                                class="mt-4 w-full text-sm text-white bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 py-2 rounded-full shadow-md hover:shadow-lg transform transition-transform hover:scale-105">
                                {{ categoria.expanded ? 'Mostrar menos' : 'Leer más' }} <i
                                    class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                        <button @click="redirectToCategoria(categoria.id)"
                            class="mt-4 w-full text-sm text-white bg-gradient-to-r from-green-700 via-teal-700 to-blue-700 py-2 rounded-full shadow-md hover:shadow-lg transform transition-transform hover:scale-105">
                            <i class="fas fa-th-list"></i> Visitar Categoría
                        </button>
                    </div>
                </div>
                <div class="absolute top-1/2 left-0 right-0 transform -translate-y-1/2 flex justify-between px-6">
                    <button @click="prevCategories"
                        class="text-white bg-gradient-to-r from-gray-700 to-black/50 px-4 py-2 rounded-full text-xl shadow-lg hover:shadow-2xl transform transition-all duration-300"
                        :disabled="currentStartIndex <= 0">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button @click="nextCategories"
                        class="text-white bg-gradient-to-r from-gray-700 to-black/50 px-4 py-2 rounded-full text-xl shadow-lg hover:shadow-2xl transform transition-all duration-300"
                        :disabled="currentStartIndex + categoriesPerPage >= totalCategorias">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Productos -->
        <div class="mt-12 text-center">
            <div class="mt-12 text-center">
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 p-6 
           bg-gradient-to-br from-gray-950/95 via-gray-900/95 to-black/95 
           backdrop-blur-2xl rounded-xl shadow-2xl border border-gray-800/40 
           transition-all duration-300 hover:shadow-[0_0_25px_rgba(59,130,246,0.2)]">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4 sm:mb-0 flex items-center gap-3">
                        <i class="fas fa-store text-blue-400 animate-pulse"></i>
                        Nuestros Productos
                        <span class="text-teal-400">({{ totalProductos }})</span>
                    </h2>
                    <div class="flex flex-col sm:flex-row items-center w-full sm:w-auto 
                    space-y-4 sm:space-y-0 sm:space-x-4 mt-4 sm:mt-0">
                        <div class="relative w-full sm:w-64 group">
                            <input v-model="searchQuery" type="text" placeholder="Buscar productos" class="w-full px-5 py-2.5 pl-10 rounded-full 
                      bg-gray-800/50 border border-gray-700/50 text-white 
                      placeholder-gray-400 focus:outline-none 
                      focus:ring-2 focus:ring-blue-500 focus:bg-gray-800/70 
                      transition-all duration-300 group-hover:shadow-[0_0_15px_rgba(59,130,246,0.2)]">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 
                  text-gray-400 group-hover:text-blue-400 transition-colors duration-300"></i>
                        </div>
                        <div class="relative w-full sm:w-48 group">
                            <select v-model="sortOption" class="w-full px-5 py-2.5 pl-10 rounded-full 
                       bg-gray-800/50 border border-gray-700/50 text-white 
                       appearance-none focus:outline-none 
                       focus:ring-2 focus:ring-blue-500 focus:bg-gray-800/70 
                       transition-all duration-300 group-hover:shadow-[0_0_15px_rgba(59,130,246,0.2)]">
                                <option value="">Ordenar por</option>
                                <option value="precio_asc">Precio: Bajo a Alto</option>
                                <option value="precio_desc">Precio: Alto a Bajo</option>
                            </select>
                            <i class="fas fa-sort absolute left-3 top-1/2 transform -translate-y-1/2 
                  text-gray-400 group-hover:text-blue-400 transition-colors duration-300"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="productos.length === 0"
                class="text-white text-lg md:text-xl mt-6 flex items-center justify-center gap-3">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                No tenemos ese producto disponible.
            </div>
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 px-6 mb-8">
                <div v-for="producto in productos" :key="producto.id" class="group relative bg-gradient-to-br from-gray-950/95 via-gray-900/95 to-black/95 
           backdrop-blur-2xl rounded-xl shadow-xl p-5 
           border border-gray-800/40 hover:border-gray-700/60
           transform transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                    <div class="relative aspect-square overflow-hidden rounded-lg mb-5">
                        <img :src="'/storage/' + producto.imagen_url" alt="Imagen del producto"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 shadow-md"
                            loading="lazy">
                        <div v-if="producto.stock === 0" class="absolute top-2 left-2 bg-red-600/90 backdrop-blur-md text-white text-xs font-semibold 
                  px-3 py-1 rounded-full flex items-center gap-1">
                            <i class="fas fa-ban text-red-200"></i> Agotado
                        </div>
                    </div>
                    <div class="bg-gray-800/30 backdrop-blur-lg rounded-lg p-4 text-white 
                border border-gray-700/30 transition-colors duration-300 group-hover:bg-gray-800/50 w-full">
                        <div
                            class="bg-gradient-to-r from-gray-900/80 to-gray-700/80 rounded-md p-3 mb-3 flex items-center gap-2">
                            <i class="fas fa-tag text-teal-400"></i>
                            <h3 class="text-sm font-semibold text-teal-300">ID: {{ producto.id }}</h3>
                        </div>
                        <h3
                            class="text-xl font-bold text-white tracking-tight flex items-start gap-2 w-full break-words">
                            <i class="fas fa-box-open text-blue-400 mt-1 shrink-0"></i>
                            <span class="flex-1 max-w-full break-words overflow-hidden">
                                {{ producto.nombre }}
                            </span>
                        </h3>
                        <div class="mt-3 space-y-2 text-sm w-full">
                            <p class="flex items-center gap-2 text-gray-200">
                                <i class="fas fa-money-bill-wave text-green-400"></i>
                                <span class="font-medium text-gray-300">Precio:</span> Bs. {{ producto.precio }}
                            </p>
                            <p :class="producto.stock > 0 ? 'text-green-400' : 'text-red-400'"
                                class="flex items-center gap-2">
                                <i :class="producto.stock > 0 ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                                <span class="font-medium text-gray-300">Stock:</span>
                                {{ producto.stock > 0 ? producto.stock : 'Agotado' }}
                            </p>
                            <p class="flex items-start gap-2 text-gray-200 w-full break-words">
                                <i class="fas fa-folder-open text-purple-400 mt-1 shrink-0"></i>
                                <span class="font-medium text-gray-300 shrink-0">Categoría:</span>
                                <span class="flex-1 max-w-full break-words overflow-hidden">
                                    {{ producto.categoria.nombre }}
                                </span>
                            </p>
                            <p class="flex items-start gap-2 text-gray-200 w-full break-words">
                                <i class="fas fa-truck text-yellow-400 mt-1 shrink-0"></i>
                                <span class="font-medium text-gray-300 shrink-0">Proveedor:</span>
                                <span class="flex-1 max-w-full break-words overflow-hidden">
                                    {{ producto.proveedor.nombre }}
                                </span>
                            </p>
                            <p class="flex items-start gap-2 text-gray-200 w-full break-words">
                                <i class="fas fa-palette text-pink-400 mt-1 shrink-0"></i>
                                <span class="font-medium text-gray-300 shrink-0">Color:</span>
                                <span class="flex-1 max-w-full break-words overflow-hidden">
                                    {{ producto.color.color }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <button @click="redirectToPrecompra(producto)" class="mt-5 w-full px-5 py-2.5 bg-gradient-to-r from-blue-600 to-teal-600 
             text-white rounded-full font-medium shadow-md
             flex items-center justify-center gap-2
             transition-all duration-300 hover:from-blue-500 hover:to-teal-500 
             hover:shadow-lg hover:shadow-blue-500/30 group-hover:scale-102">
                        <i class="fas fa-shopping-cart text-teal-200 group-hover:animate-pulse"></i>
                        Ver Producto
                    </button>
                    <div class="absolute inset-0 bg-gradient-to-t from-teal-500/10 to-blue-500/10 
                opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl pointer-events-none">
                    </div>
                </div>
            </div>
            <div class="flex justify-center">
                <button @click="fetchProductos"
                    class="mt-6 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-full shadow-md transform transition-transform hover:scale-105 hover:shadow-xl"
                    v-if="offset < totalProductos">
                    <i class="fas fa-arrow-down mr-2"></i> Cargar más
                </button>
            </div>
        </div>
        <!--informacion-->
        <div
            class="mt-12 text-center relative bg-gradient-to-br from-gray-900 via-slate-900 to-blue-900 p-12 text-white rounded-2xl backdrop-blur-2xl shadow-2xl shadow-blue-900/30 hover:shadow-indigo-900/40 transition-shadow duration-500">
            <h2
                class="text-3xl sm:text-4xl md:text-5xl font-bold mb-8 animate-text bg-gradient-to-r from-cyan-400 via-blue-500 to-indigo-600 bg-clip-text text-transparent">
                Contáctanos
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    class="group bg-slate-900/50 p-8 rounded-xl backdrop-blur-lg border border-slate-800/50 hover:border-cyan-500/30 hover:bg-gradient-to-br from-slate-900/80 to-blue-900/50 transition-all duration-500 hover:-translate-y-2 shadow-lg shadow-blue-900/10">
                    <h3
                        class="text-2xl font-bold mb-4 bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent">
                        Información de contacto</h3>
                    <div class="space-y-3 text-slate-300 group-hover:text-slate-100 transition-colors">
                        <p>Teléfono: 800171000</p>
                        <p>WhatsApp: 72164000</p>
                        <p>Email: <a href="mailto:ventasonline@polimericosdial.com.bo"
                                class="underline decoration-2 decoration-cyan-400 hover:decoration-blue-500 transition-colors">polimericosdial@gmail.com</a>
                        </p>
                        <p>Dirección: Parque Industrial MZ031 – Distrito N°8</p>
                        <p>Horario: L-V 8:00 - 18:00 • Sáb 08:00 - 12:30</p>
                    </div>
                </div>
                <div
                    class="group bg-slate-900/50 p-8 rounded-xl backdrop-blur-lg border border-slate-800/50 hover:border-indigo-500/30 hover:bg-gradient-to-br from-slate-900/80 to-indigo-900/50 transition-all duration-500 hover:-translate-y-2 shadow-lg shadow-indigo-900/10">
                    <h3
                        class="text-2xl font-bold mb-4 bg-gradient-to-r from-purple-400 to-indigo-500 bg-clip-text text-transparent">
                        Sobre nosotros</h3>
                    <ul class="space-y-3 text-slate-300 group-hover:text-slate-100 transition-colors">
                        <Link href="/nosotros"
                            class="underline decoration-2 decoration-purple-400 hover:decoration-indigo-500 transition-colors">
                        Nosotros
                        </Link>
                        <li> <a :href="route('terms.show')"
                                class="underline decoration-2 decoration-purple-400 hover:decoration-indigo-500 transition-colors">Términos
                                y Condiciones</a></li>
                        <li><a :href="route('policy.show')"
                                class="underline decoration-2 decoration-purple-400 hover:decoration-indigo-500 transition-colors">Políticas
                                de Envío</a></li>
                    </ul>
                </div>
                <div
                    class="group bg-slate-900/50 p-8 rounded-xl backdrop-blur-lg border border-slate-800/50 hover:border-pink-500/30 hover:bg-gradient-to-br from-slate-900/80 to-pink-900/50 transition-all duration-500 hover:-translate-y-2 shadow-lg shadow-pink-900/10">
                    <h3
                        class="text-2xl font-bold mb-4 bg-gradient-to-r from-pink-400 to-rose-500 bg-clip-text text-transparent">
                        Síguenos</h3>
                    <div class="flex justify-center space-x-6 text-3xl">
                        <a href="https://www.facebook.com" target="_blank"
                            class="hover:-translate-y-1.5 transition-transform hover:text-blue-400">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://twitter.com" target="_blank"
                            class="hover:-translate-y-1.5 transition-transform hover:text-sky-400">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.instagram.com" target="_blank"
                            class="hover:-translate-y-1.5 transition-transform hover:text-pink-500">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div
                class="mt-8 bg-slate-900/50 p-8 rounded-xl backdrop-blur-lg border border-slate-800/50 hover:border-emerald-500/30 hover:bg-gradient-to-br from-slate-900/80 to-emerald-900/50 transition-all duration-500 hover:-translate-y-2 shadow-lg shadow-emerald-900/10">
                <h3
                    class="text-2xl font-bold mb-4 bg-gradient-to-r from-emerald-400 to-teal-500 bg-clip-text text-transparent">
                    Métodos de pago</h3>
                <p class="text-slate-300 group-hover:text-slate-100 transition-colors">Aceptamos pagos con QR como el
                    unico método disponible en nuestra tienda.</p>
            </div>
        </div>

    </main>
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
<style>
/*carga*/
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/*animaciones*/
body {
    background: linear-gradient(135deg,
            #0d0d0d 0%,
            #1a1a2e 20%,
            #2b3a4f 40%,
            #3e5c76 60%,
            #1f2a44 80%,
            #0d0d0d 100%);
    background-size: 300% 300%;
    animation: gradientFlow 15s ease infinite;
    margin: 0;
    font-family: 'Inter', 'Arial', sans-serif;
    color: #ffffff;
    overflow-x: hidden;
    position: relative;
}

@keyframes gradientFlow {
    0% {
        background-position: 0% 0%;
    }

    25% {
        background-position: 50% 50%;
    }

    50% {
        background-position: 100% 0%;
    }

    75% {
        background-position: 50% 100%;
    }

    100% {
        background-position: 0% 0%;
    }
}

body::before,
body::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

body::before {
    background: radial-gradient(circle at 20% 30%, rgba(0, 212, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
    background-size: 150% 150%;
    animation: particleDrift 25s linear infinite;
    opacity: 0.4;
}

body::after {
    background: radial-gradient(circle at 50% 50%, rgba(236, 72, 153, 0.05) 0%, transparent 70%);
    background-size: 200% 200%;
    animation: particleDriftReverse 20s linear infinite;
    opacity: 0.3;
}

@keyframes particleDrift {
    0% {
        background-position: 0% 0%;
    }

    50% {
        background-position: 100% 100%;
    }

    100% {
        background-position: 0% 0%;
    }
}

@keyframes particleDriftReverse {
    0% {
        background-position: 100% 100%;
    }

    50% {
        background-position: 0% 0%;
    }

    100% {
        background-position: 100% 100%;
    }
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

@keyframes glowEffect {
    0% {
        box-shadow: 0 0 15px rgba(59, 130, 246, 0.2), 0 0 30px rgba(147, 197, 253, 0.1);
    }

    50% {
        box-shadow: 0 0 25px rgba(59, 130, 246, 0.4), 0 0 50px rgba(147, 197, 253, 0.2);
    }

    100% {
        box-shadow: 0 0 15px rgba(59, 130, 246, 0.2), 0 0 30px rgba(147, 197, 253, 0.1);
    }
}
</style>
<style>
/*barra de navegacion animacion*/
@keyframes shine {
    from {
        background-position: -200% 0;
    }

    to {
        background-position: 200% 0;
    }
}

@keyframes particle {
    0% {
        transform: translateY(0) scale(1);
        opacity: 0.3;
    }

    50% {
        transform: translateY(-20px) scale(1.2);
        opacity: 0.6;
    }

    100% {
        transform: translateY(-40px) scale(0.8);
        opacity: 0;
    }
}

.animate-shine {
    animation: shine 6s infinite linear;
}

.animate-particle {
    animation: particle 3s infinite linear;
}

.animate-particle-delay {
    animation: particle 3s infinite linear 0.5s;
}

.shadow-neon-lg {
    box-shadow: 0 0 25px rgba(34, 138, 230, 0.1),
        0 4px 30px -5px rgba(16, 94, 245, 0.2),
        inset 0 2px 8px -1px rgba(255, 255, 255, 0.1);
}

.hover\:shadow-glow-cyan:hover {
    box-shadow: 0 0 20px rgba(34, 211, 238, 0.3),
        0 0 10px rgba(34, 211, 238, 0.2),
        inset 0 1px 4px rgba(255, 255, 255, 0.2);
}

.shadow-glow-blue {
    box-shadow: 0 0 30px rgba(56, 189, 248, 0.2);
}

.bg-navy-800 {
    background-color: #0a1929;
}

.animate-spin-slow {
    animation: spin 15s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

/*loader inicio*/
@keyframes text-glow {

    0%,
    100% {
        background-position: 0% 50%;
        filter: drop-shadow(0 0 10px rgba(59, 130, 246, 0.3));
    }

    50% {
        background-position: 100% 50%;
        filter: drop-shadow(0 0 20px rgba(147, 51, 234, 0.5));
    }
}

.animate-text-glow {
    animation: text-glow 3s ease-in-out infinite;
}

.hologram-enter-active,
.hologram-leave-active {
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.hologram-enter-from,
.hologram-leave-to {
    opacity: 0;
    filter: blur(20px);
}
</style>