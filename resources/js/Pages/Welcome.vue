<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';
import Swal from 'sweetalert2';
import Lenis from 'lenis';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
    laravelVersion: { type: String, required: true },
    phpVersion: { type: String, required: true },
});

const isLoading = ref(true);
const loadingText = ref('Cargando...');
const currentSlide = ref(0);
const images = ref([]);
const categorias = ref([]);
const productos = ref([]);
const errorMessage = ref('');
const categoriasError = ref('');
const productosError = ref('');
const currentStartIndex = ref(0);
const categoriesPerPage = 4;
const totalCategorias = ref(0);
const totalProductos = ref(0);
const offset = ref(0);
const limit = 8;
const loading = ref(false);
const searchQuery = ref('');
const sortOption = ref('');
const cache = new Map();
let slideInterval;

const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            observer.unobserve(img);
        }
    });
});

async function fetchInitialData() {
    try {
        const [imagesResponse, categoriasResponse, productosResponse] = await Promise.all([
            axios.get('/inicio-imagenes').catch(err => ({ error: err })),
            axios.get('/categorias', { params: { start: currentStartIndex.value, limit: categoriesPerPage } }).catch(err => ({ error: err })),
            axios.get('/productos', { params: { limit, offset: offset.value, search: searchQuery.value, sort: sortOption.value } }).catch(err => ({ error: err }))
        ]);

        if (imagesResponse.error) {
            console.error('Error fetching images:', imagesResponse.error);
            errorMessage.value = imagesResponse.error.response?.data?.message || 'Error al cargar imágenes.';
        } else {
            images.value = imagesResponse.data;
            errorMessage.value = '';
        }

        if (categoriasResponse.error) {
            console.error('Error fetching categorias:', categoriasResponse.error);
            categoriasError.value = categoriasResponse.error.response?.data?.message || 'Error al cargar categorías.';
        } else {
            categorias.value = categoriasResponse.data.categories.map(categoria => ({
                ...categoria,
                expanded: false,
            }));
            totalCategorias.value = categoriasResponse.data.total;
            categoriasError.value = categoriasResponse.data.message || '';
        }

        if (productosResponse.error) {
            console.error('Error fetching productos:', productosResponse.error);
            productosError.value = productosResponse.error.response?.data?.message || 'Error al cargar productos.';
        } else {
            const cacheKey = `${searchQuery.value}_${sortOption.value}_${offset.value}`;
            const fetchedData = {
                productos: productosResponse.data.productos,
                total: productosResponse.data.total
            };
            cache.set(cacheKey, fetchedData);
            productos.value = fetchedData.productos;
            totalProductos.value = fetchedData.total;
            offset.value += limit;
            lazyLoadImages();
        }

        if (imagesResponse.error && categoriasResponse.error && productosResponse.error) {
            throw new Error('Failed to load initial data');
        }
    } catch (error) {
        console.error('Critical error in fetchInitialData:', error);
        throw new Error('Error crítico al cargar datos iniciales');
    } finally {
        loading.value = false;
    }
}

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
        productos.value = offset.value === 0 ? fetchedData.productos : productos.value.concat(fetchedData.productos);
        totalProductos.value = fetchedData.total;
        offset.value += limit;
        lazyLoadImages();
    } catch (error) {
        console.error('Error fetching productos:', error);
        productosError.value = error.response?.data?.message || 'Error al cargar productos.';
    } finally {
        loading.value = false;
    }
}

function lazyLoadImages() {
    document.querySelectorAll('img[data-src]').forEach(img => observer.observe(img));
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
        console.error('Error fetching categorias:', error);
        categoriasError.value = error.response?.data?.message || 'Error al cargar categorías.';
    }
}

const debouncedFetchProductos = debounce(() => {
    offset.value = 0;
    cache.clear();
    productos.value = [];
    fetchProductos();
}, 300);

function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function redirectToPrecompra(producto) {
    Inertia.visit(`/precompra/${producto.id}`, {
        onError: (errors) => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errors?.error || 'Problema al redirigir al producto.',
            });
        },
        onSuccess: (page) => {
            if (page.props.flash?.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: page.props.flash.error,
                });
            }
        },
    });
}

function redirectToCategoria(categoriaId) {
    axios.get(`/categorias/${categoriaId}`)
        .then(() => Inertia.visit(`/categorias/${categoriaId}`))
        .catch((error) => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.response?.data?.error || 'Problema al cargar categoría.',
            });
        });
}

const logoPath = computed(() => "/storage/logo.png");

onMounted(() => {
    const lenis = new Lenis({
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        smooth: true,
    });

    function raf(time) {
        lenis.raf(time);
        requestAnimationFrame(raf);
    }

    requestAnimationFrame(raf);

    fetchInitialData().then(() => {
        slideInterval = setInterval(nextSlide, 5000);
        setTimeout(() => {
            loadingText.value = '¡Bienvenidos a Polimericos Dial Bolivia!';
            setTimeout(() => {
                isLoading.value = false;
            }, 1000);
        }, 4000);
    }).catch(error => {
        console.error('Failed to load initial data:', error);
    });

    const script1 = document.createElement('script');
    script1.src = "https://cdn.botpress.cloud/webchat/v2.2/inject.js";
    script1.async = true;
    document.body.appendChild(script1);

    const script2 = document.createElement('script');
    script2.src = "https://files.bpcontent.cloud/2025/02/12/21/20250212211027-GHNKJLUA.js";
    script2.async = true;
    document.body.appendChild(script2);
});

onBeforeUnmount(() => clearInterval(slideInterval));
</script>
<template>

    <Head title="Welcome" />
    <div v-if="isLoading"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-br from-black/90 via-blue-950/80 to-green-950/80 backdrop-blur-2xl">
        <div class="text-center space-y-8">
            <div class="relative w-32 h-32 mx-auto">
                <div
                    class="absolute inset-0 rounded-full animate-spin-slow bg-[conic-gradient(from_180deg_at_50%_50%,#00f7ff_0%,#0a2bff_50%,#00ff7f_100%)] shadow-[0_0_50px_rgba(0,255,127,0.5)]">
                </div>
                <div
                    class="absolute inset-2 bg-gradient-to-br from-black/70 to-blue-950/70 rounded-full backdrop-blur-xl border border-white/10 shadow-[0_0_20px_rgba(0,255,255,0.3)]">
                </div>
            </div>
            <h2
                class="text-4xl md:text-5xl font-light tracking-wide animate-pulse text-transparent bg-clip-text bg-[linear-gradient(270deg,#00f7ff,#0a2bff,#00ff7f,#00f7ff)] bg-[length:400%_400%] animate-gradient-x">
                {{ loadingText }}</h2>
        </div>
    </div>

    <header
        class="fixed top-0 left-0 w-full bg-gradient-to-br from-black/70 via-blue-950/60 to-green-950/60 backdrop-blur-xl z-40 py-3 px-4 sm:px-6 border-b border-blue-500/20 shadow-[0_8px_30px_rgba(0,0,0,0.6)] hover:shadow-[0_12px_40px_rgba(0,127,255,0.3)] transition-all duration-500">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <div
                        class="absolute inset-0 rounded-full animate-pulse bg-[conic-gradient(from_180deg,#00f7ff_0%,#0a2bff_50%,#00ff7f_100%)] opacity-30 blur-xl">
                    </div>
                    <img :src="logoPath" alt="Logo"
                        class="w-12 h-12 rounded-full border-2 border-blue-500/30 p-1 shadow-[0_0_20px_rgba(0,127,255,0.4)] hover:scale-110 transition-all duration-500">
                </div>
                <span
                    class="hidden sm:block text-lg font-light text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-green-300">Poliméricos
                    Dial de Bolivia</span>
            </div>
            <nav
                class="flex items-center space-x-6 bg-black/40 backdrop-blur-xl py-2 px-6 rounded-full border border-blue-500/20 shadow-[0_0_20px_rgba(0,127,255,0.3)] hover:shadow-[0_0_30px_rgba(0,255,127,0.3)] transition-all duration-500">
                <Link v-if="canLogin && !$page.props.auth.user" :href="route('login')"
                    class="text-blue-300 hover:text-blue-100 transition-colors"><i
                    class="fas fa-sign-in-alt mr-2"></i>Inicia sesión</Link>
                <Link v-if="canRegister && !$page.props.auth.user" :href="route('register')"
                    class="text-green-300 hover:text-green-100 transition-colors"><i
                    class="fas fa-user-plus mr-2"></i>Registrarse</Link>
                <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                    class="text-green-300 hover:text-green-100 transition-colors"><i
                    class="fas fa-tachometer-alt mr-2"></i>Ir al Panel</Link>
            </nav>
        </div>
    </header>

    <main>
        <div class="relative w-full h-[40vh] sm:h-[80vh] md:h-[90vh] lg:h-[100vh] overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent z-10"></div>
            <div v-if="errorMessage"
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-30 bg-gradient-to-br from-red-600/80 to-black/80 backdrop-blur-xl p-6 rounded-xl shadow-2xl border border-white/10">
                <p class="text-white text-sm">{{ errorMessage }}</p>
            </div>
            <div v-else class="relative w-full h-full">
                <img v-for="(image, index) in images" :key="index" :src="'/storage/' + image"
                    :class="['absolute inset-0 w-full h-full object-cover transition-all duration-1000 ease-[cubic-bezier(0.4,0,0.2,1)]', { 'opacity-100 scale-100 z-20': index === currentSlide, 'opacity-0 scale-105': index !== currentSlide }]"
                    :alt="'Slide ' + (index + 1)" loading="lazy" />
            </div>
            <button @click="prevSlide"
                class="absolute top-1/2 left-4 -translate-y-1/2 z-30 w-10 h-10 bg-gradient-to-br from-black/60 to-blue-950/60 backdrop-blur-xl rounded-full border border-blue-500/20 shadow-xl hover:shadow-[0_0_15px_rgba(0,127,255,0.5)] transition-all duration-300 hover:scale-110">
                <span class="text-white text-2xl">‹</span>
            </button>
            <button @click="nextSlide"
                class="absolute top-1/2 right-4 -translate-y-1/2 z-30 w-10 h-10 bg-gradient-to-br from-black/60 to-blue-950/60 backdrop-blur-xl rounded-full border border-blue-500/20 shadow-xl hover:shadow-[0_0_15px_rgba(0,127,255,0.5)] transition-all duration-300 hover:scale-110">
                <span class="text-white text-2xl">›</span>
            </button>
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-30 flex space-x-2">
                <div v-for="(_, index) in images" :key="index" @click="currentSlide = index"
                    :class="['w-3 h-3 rounded-full cursor-pointer transition-all duration-500', { 'bg-blue-400/90 backdrop-blur-lg border border-blue-300 shadow-[0_0_10px_rgba(0,127,255,0.5)]': index === currentSlide, 'bg-white/30 hover:bg-white/50': index !== currentSlide }]">
                </div>
            </div>
        </div>

        <div class="mt-12 px-4 text-center">
            <h2
                class="text-3xl md:text-5xl font-bold mb-12 bg-gradient-to-r from-blue-300 via-green-300 to-blue-300 bg-clip-text text-transparent relative">
                Nuestras Categorías
                <span
                    class="absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-blue-500 via-green-500 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></span>
            </h2>
            <div v-if="categoriasError"
                class="bg-gradient-to-br from-red-600/80 to-black/80 backdrop-blur-xl p-4 rounded-lg text-white">{{
                    categoriasError }}</div>
            <div class="relative">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
                    <div v-for="categoria in categorias" :key="categoria.id"
                        class="relative bg-gradient-to-br from-black/40 via-blue-950/30 to-green-950/30 backdrop-blur-xl rounded-xl p-4 border border-blue-500/20 shadow-xl hover:shadow-[0_0_20px_rgba(0,255,127,0.4)] hover:scale-105 transition-all duration-500">
                        <div class="relative aspect-[16/9] rounded-lg overflow-hidden">
                            <img :src="'/storage/' + categoria.imagen_url" alt="Categoría"
                                class="w-full h-full object-cover transition-transform duration-500 hover:scale-110" />
                        </div>
                        <div class="mt-4 text-white">
                            <h3 class="text-xl font-semibold text-center">{{ categoria.nombre }}</h3>
                            <p v-if="categoria.parent" class="text-sm text-green-400 text-center mt-2">Subcategoría de
                                <span class="font-bold">{{ categoria.parent.nombre }}</span>
                            </p>
                        </div>
                        <div class="mt-4 bg-black/30 backdrop-blur-lg rounded-lg p-4">
                            <p class="text-white/80 text-center" :class="{ 'line-clamp-3': !categoria.expanded }">{{
                                categoria.descripcion }}</p>
                            <button @click="categoria.expanded = !categoria.expanded"
                                class="mt-4 w-full bg-gradient-to-r from-blue-600 to-green-600 text-white py-2 rounded-full shadow-md hover:shadow-[0_0_15px_rgba(0,255,127,0.5)] transition-all duration-300">
                                {{ categoria.expanded ? 'Mostrar menos' : 'Leer más' }} <i
                                    class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                        <button @click="redirectToCategoria(categoria.id)"
                            class="mt-4 w-full bg-gradient-to-r from-green-600 to-blue-600 text-white py-2 rounded-full shadow-md hover:shadow-[0_0_15px_rgba(0,127,255,0.5)] transition-all duration-300">
                            <i class="fas fa-th-list"></i> Visitar Categoría
                        </button>
                    </div>
                </div>
                <div
                    class="flex justify-center gap-6 mt-8 md:absolute md:top-1/2 md:left-0 md:right-0 md:-translate-y-1/2 md:px-6">
                    <button @click="prevCategories"
                        class="bg-gradient-to-r from-black/60 to-blue-950/60 text-white px-4 py-2 rounded-full shadow-xl hover:shadow-[0_0_15px_rgba(0,127,255,0.5)] transition-all duration-300"
                        :disabled="currentStartIndex <= 0">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button @click="nextCategories"
                        class="bg-gradient-to-r from-black/60 to-blue-950/60 text-white px-4 py-2 rounded-full shadow-xl hover:shadow-[0_0_15px_rgba(0,127,255,0.5)] transition-all duration-300"
                        :disabled="currentStartIndex + categoriesPerPage >= totalCategorias">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-12 px-4 text-center">
            <div
                class="flex flex-col sm:flex-row justify-between items-center mb-6 p-6 bg-gradient-to-br from-black/40 via-blue-950/30 to-green-950/30 backdrop-blur-xl rounded-xl border border-blue-500/20 shadow-xl hover:shadow-[0_0_20px_rgba(0,255,127,0.4)] transition-all duration-500">
                <h2
                    class="text-3xl md:text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-green-300 mb-4 sm:mb-0">
                    <i class="fas fa-store mr-2"></i>Nuestros Productos <span class="text-green-400">({{ totalProductos
                        }})</span>
                </h2>
                <div
                    class="flex flex-col sm:flex-row items-center w-full sm:w-auto space-y-4 sm:space-y-0 sm:space-x-4">
                    <div class="relative w-full sm:w-64">
                        <input v-model="searchQuery" type="text" placeholder=" Buscar productos"
                            class="w-full px-5 py-2 rounded-full bg-black/50 border border-blue-500/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-300" />
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-blue-400"></i>
                    </div>
                    <div class="relative w-full sm:w-48">
                        <select v-model="sortOption"
                            class="w-full px-5 py-2 rounded-full bg-black/50 border border-blue-500/20 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-300">
                            <option value=""> Ordenar por</option>
                            <option value="precio_asc">Precio: Bajo a Alto</option>
                            <option value="precio_desc">Precio: Alto a Bajo</option>
                        </select>
                        <i class="fas fa-sort absolute left-3 top-1/2 -translate-y-1/2 text-blue-400"></i>
                    </div>
                </div>
            </div>
            <div v-if="productos.length === 0" class="text-white text-lg mt-6 flex items-center justify-center gap-3"><i
                    class="fas fa-exclamation-triangle text-yellow-400"></i>No tenemos ese producto disponible.</div>
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 px-6 mb-8">
                <div v-for="producto in productos" :key="producto.id"
                    class="relative bg-gradient-to-br from-black/40 via-blue-950/30 to-green-950/30 backdrop-blur-xl rounded-xl p-5 border border-blue-500/20 shadow-xl hover:shadow-[0_0_20px_rgba(0,255,127,0.4)] hover:scale-105 transition-all duration-500">
                    <div class="relative aspect-square rounded-lg overflow-hidden mb-4">
                        <img :src="'/storage/' + producto.imagen_url" alt="Producto"
                            class="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
                            loading="lazy" />
                        <div v-if="producto.stock === 0"
                            class="absolute top-2 left-2 bg-red-600/80 backdrop-blur-lg text-white text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1">
                            <i class="fas fa-ban"></i>Agotado
                        </div>
                    </div>
                    <div class="bg-black/30 backdrop-blur-lg rounded-lg p-4 text-white">
                        <div
                            class="bg-gradient-to-r from-black/50 to-blue-950/50 rounded-md p-3 mb-3 flex items-center gap-2">
                            <i class="fas fa-tag text-green-400"></i>
                            <h3 class="text-sm font-semibold">ID: {{ producto.id }}</h3>
                        </div>
                        <h3 class="text-xl font-bold flex items-start gap-2"><i
                                class="fas fa-box-open text-blue-400 mt-1"></i>{{ producto.nombre }}</h3>
                        <div class="mt-3 space-y-2 text-sm">
                            <p class="flex items-center gap-2"><i
                                    class="fas fa-money-bill-wave text-green-400"></i><span
                                    class="font-medium">Precio:</span>Bs. {{ producto.precio }}</p>
                            <p :class="producto.stock > 0 ? 'text-green-400' : 'text-red-400'"
                                class="flex items-center gap-2"><i
                                    :class="producto.stock > 0 ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i><span
                                    class="font-medium">Stock:</span>{{ producto.stock > 0 ? producto.stock : 'Agotado'
                                    }}</p>
                            <p class="flex items-start gap-2"><i class="fas fa-folder-open text-blue-400 mt-1"></i><span
                                    class="font-medium">Categoría:</span>{{ producto.categoria.nombre }}</p>
                            <p class="flex items-start gap-2"><i class="fas fa-truck text-green-400 mt-1"></i><span
                                    class="font-medium">Proveedor:</span>{{ producto.proveedor.nombre }}</p>
                            <p class="flex items-start gap-2"><i class="fas fa-palette text-blue-400 mt-1"></i><span
                                    class="font-medium">Color:</span>{{ producto.color.color }}</p>
                        </div>
                    </div>
                    <button @click="redirectToPrecompra(producto)"
                        class="mt-4 w-full bg-gradient-to-r from-blue-600 to-green-600 text-white py-2 rounded-full shadow-md hover:shadow-[0_0_15px_rgba(0,255,127,0.5)] transition-all duration-300">
                        <i class="fas fa-shopping-cart mr-2"></i>Ver Producto
                    </button>
                </div>
            </div>
            <button @click="fetchProductos"
                class="mt-6 px-6 py-3 bg-gradient-to-r from-blue-600 to-green-600 text-white rounded-full shadow-md hover:shadow-[0_0_15px_rgba(0,127,255,0.5)] transition-all duration-300"
                v-if="offset < totalProductos"><i class="fas fa-arrow-down mr-2"></i>Cargar más</button>
        </div>

        <div
            class="mt-12 px-4 py-12 bg-gradient-to-br from-black/50 via-blue-950/40 to-green-950/40 backdrop-blur-xl rounded-xl shadow-2xl hover:shadow-[0_0_30px_rgba(0,255,127,0.4)] transition-all duration-500">
            <h2
                class="text-4xl md:text-5xl font-bold mb-8 text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-green-300">
                Contáctanos</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    class="bg-black/40 backdrop-blur-xl p-8 rounded-xl border border-blue-500/20 hover:border-blue-500/50 hover:bg-gradient-to-br from-black/50 to-blue-950/50 transition-all duration-500 shadow-lg">
                    <h3
                        class="text-2xl font-bold mb-4 text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-green-300">
                        Información de contacto</h3>
                    <div class="space-y-3 text-gray-200">
                        <p>Teléfono: 800171000</p>
                        <p>WhatsApp: 72164000</p>
                        <p>Email: <a href="mailto:ventasonline@polimericosdial.com.bo"
                                class="underline decoration-blue-400 hover:decoration-green-400">polimericosdial@gmail.com</a>
                        </p>
                        <p>Dirección: Parque Industrial MZ031 – Distrito N°8</p>
                        <p>Horario: L-V 8:00 - 18:00 • Sáb 08:00 - 12:30</p>
                    </div>
                </div>
                <div
                    class="bg-black/40 backdrop-blur-xl p-8 rounded-xl border border-blue-500/20 hover:border-blue-500/50 hover:bg-gradient-to-br from-black/50 to-blue-950/50 transition-all duration-500 shadow-lg">
                    <h3
                        class="text-2xl font-bold mb-4 text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-green-300">
                        Sobre nosotros</h3>
                    <ul class="space-y-3 text-gray-200">
                        <li><a href="/nosotros"
                                class="underline decoration-blue-400 hover:decoration-green-400">Nosotros</a></li>
                        <li><a :href="route('terms.show')"
                                class="underline decoration-blue-400 hover:decoration-green-400">Términos y
                                Condiciones</a></li>
                        <li><a :href="route('policy.show')"
                                class="underline decoration-blue-400 hover:decoration-green-400">Políticas de Envío</a>
                        </li>
                    </ul>
                </div>
                <div
                    class="bg-black/40 backdrop-blur-xl p-8 rounded-xl border border-blue-500/20 hover:border-blue-500/50 hover:bg-gradient-to-br from-black/50 to-blue-950/50 transition-all duration-500 shadow-lg">
                    <h3
                        class="text-2xl font-bold mb-4 text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-green-300">
                        Síguenos</h3>
                    <div class="flex justify-center space-x-6 text-3xl">
                        <a href="https://www.facebook.com" target="_blank"
                            class="hover:text-blue-400 transition-colors"><i class="fab fa-facebook"></i></a>
                        <a href="https://wa.me/573135805824" target="_blank"
                            class="hover:text-green-400 transition-colors"><i class="fab fa-whatsapp"></i></a>
                        <a href="https://www.instagram.com" target="_blank"
                            class="hover:text-blue-400 transition-colors"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div
                class="mt-8 bg-black/40 backdrop-blur-xl p-8 rounded-xl border border-blue-500/20 hover:border-blue-500/50 hover:bg-gradient-to-br from-black/50 to-blue-950/50 transition-all duration-500 shadow-lg">
                <h3
                    class="text-2xl font-bold mb-4 text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-green-300">
                    Métodos de pago</h3>
                <p class="text-gray-200">Aceptamos pagos con QR como el único método disponible en nuestra tienda.</p>
            </div>
        </div>
    </main>

    <footer class="py-10 text-center bg-gradient-to-br from-black/70 via-blue-950/60 to-green-950/60 backdrop-blur-xl">
        <div class="relative space-y-4">
            <p class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-green-300">
                Poliméricos Dial Bolivia</p>
            <p class="text-base text-gray-200">La Paz, Bolivia | © {{ new Date().getFullYear() }} Todos los derechos
                reservados</p>
            <div class="absolute top-0 w-full h-px bg-gradient-to-r from-transparent via-blue-400 to-transparent"></div>
        </div>
    </footer>
</template>

<style>
@tailwind base;
@tailwind components;
@tailwind utilities;

body {
    @apply bg-gradient-to-br from-black via-blue-950 to-green-950 text-white font-sans;
    background-size: 300% 300%;
    animation: gradientFlow 20s ease infinite;
}

@keyframes gradientFlow {
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

.animate-gradient-x {
    animation: gradient-x 4s ease infinite;
    background-size: 400% 400%;
}

.animate-spin-slow {
    animation: spin 10s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>