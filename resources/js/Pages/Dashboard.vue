<script setup>
import { ref, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    productos: Array,
});

const searchNombre = ref('');
const searchCategoria = ref('');
const searchColor = ref('');
const sortOrder = ref('');
const currentPages = ref({});
const productosPorPagina = 8;

const productosFiltradosYOrdenados = computed(() => {
    let filtrados = props.productos.filter((producto) => {
        const nombreMatch =
            searchNombre.value === '' ||
            producto.nombre.toLowerCase().includes(searchNombre.value.toLowerCase());
        const categoriaMatch =
            searchCategoria.value === '' ||
            (producto.categoria?.nombre?.toLowerCase().includes(searchCategoria.value.toLowerCase()) || false);
        const colorMatch =
            searchColor.value === '' ||
            (producto.color?.color?.toLowerCase().includes(searchColor.value.toLowerCase()) || false);
        return nombreMatch && categoriaMatch && colorMatch;
    });

    if (sortOrder.value === 'asc') {
        filtrados.sort((a, b) => a.precio - b.precio);
    } else if (sortOrder.value === 'desc') {
        filtrados.sort((a, b) => b.precio - a.precio);
    }
    return filtrados;
});

const productosAgrupadosSinPaginacion = computed(() => {
    let grupos = {};
    productosFiltradosYOrdenados.value.forEach((producto) => {
        const categoria = producto.categoria?.nombre || 'Sin categoría';
        if (!grupos[categoria]) {
            grupos[categoria] = [];

            if (!(categoria in currentPages.value)) {
                currentPages.value[categoria] = 1;
            }
        }
        grupos[categoria].push(producto);
    });
    return grupos;
});

const productosAgrupados = computed(() => {
    let paginados = {};
    Object.entries(productosAgrupadosSinPaginacion.value).forEach(([categoria, productos]) => {
        const paginaActual = currentPages.value[categoria] || 1;
        const inicio = (paginaActual - 1) * productosPorPagina;
        const fin = inicio + productosPorPagina;
        paginados[categoria] = productos.slice(inicio, fin);
    });
    return paginados;
});

const cambiarPagina = (categoria, nuevaPagina) => {
    currentPages.value[categoria] = nuevaPagina;
};
const redirectToPrecompra = (producto) => {
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
</script>

<template>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <AppLayout title="Lista de productos">
        <div
            class="bg-gradient-to-r from-gray-900 via-blue-900/80 to-indigo-900/60 backdrop-blur-xl border-b border-cyan-400/20 shadow-2xl">
            <h2
                class="font-extrabold text-3xl bg-gradient-to-r from-cyan-400 via-blue-400 to-indigo-400 bg-clip-text text-transparent py-6 px-8 tracking-tighter">
                Lista de Productos
            </h2>
        </div>
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-gradient-to-br from-gray-900/90 via-blue-900/40 to-indigo-900/20 backdrop-blur-3xl rounded-3xl shadow-[0_10px_50px_-15px_rgba(0,0,0,0.7)] border border-gray-700/30 p-8">
                    <p
                        class="text-2xl font-black mb-8 bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent inline-block px-2 py-1 rounded-lg">
                        Puedes buscar por categoría, color y nombre de producto
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-10">
                        <input v-model="searchNombre" placeholder="Buscar por nombre"
                            class="bg-gray-900/40 backdrop-blur-md border-2 border-gray-700/50 rounded-xl px-5 py-3.5 text-cyan-100 placeholder-gray-500 focus:border-cyan-400/60 focus:ring-4 focus:ring-cyan-400/20 focus:bg-gray-800/70 transition-all duration-300 shadow-lg shadow-blue-900/10 hover:shadow-cyan-400/10" />
                        <input v-model="searchCategoria" placeholder="Buscar por categoría"
                            class="bg-gray-900/40 backdrop-blur-md border-2 border-gray-700/50 rounded-xl px-5 py-3.5 text-cyan-100 placeholder-gray-500 focus:border-cyan-400/60 focus:ring-4 focus:ring-cyan-400/20 focus:bg-gray-800/70 transition-all duration-300 shadow-lg shadow-blue-900/10 hover:shadow-cyan-400/10" />
                        <input v-model="searchColor" placeholder="Buscar por color"
                            class="bg-gray-900/40 backdrop-blur-md border-2 border-gray-700/50 rounded-xl px-5 py-3.5 text-cyan-100 placeholder-gray-500 focus:border-cyan-400/60 focus:ring-4 focus:ring-cyan-400/20 focus:bg-gray-800/70 transition-all duration-300 shadow-lg shadow-blue-900/10 hover:shadow-cyan-400/10" />
                        <select v-model="sortOrder"
                            class="bg-gray-900/40 backdrop-blur-md border-2 border-gray-700/50 rounded-xl px-5 py-3.5 text-cyan-100 focus:border-cyan-400/60 focus:ring-4 focus:ring-cyan-400/20 transition-all duration-300 shadow-lg shadow-blue-900/10 hover:shadow-cyan-400/10">
                            <option value="" class="bg-gray-900">Ordenar por precio</option>
                            <option value="asc" class="bg-gray-900">Menor a Mayor</option>
                            <option value="desc" class="bg-gray-900">Mayor a Menor</option>
                        </select>
                    </div>
                    <div v-for="(productos, categoria) in productosAgrupados" :key="categoria" class="mb-12">
                        <h3
                            class="text-2xl font-black mb-8 bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent inline-block px-2 py-1 rounded-lg">
                            {{ categoria }}
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                            <div v-for="producto in productos" :key="producto.id"
                                class="group relative bg-gray-900/60 backdrop-blur-lg border-2 border-gray-700/30 rounded-2xl p-6 transform transition-all duration-500 hover:scale-[1.02] hover:border-cyan-400/40 hover:shadow-[0_10px_30px_-5px_rgba(6,182,212,0.15)]">
                                <div
                                    class="absolute inset-0 bg-gradient-to-br from-cyan-400/10 to-blue-400/5 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <div
                                    class="relative overflow-hidden rounded-xl border-2 border-gray-700/40 aspect-square">
                                    <img v-if="producto.imagen_url" :src="'/storage/' + producto.imagen_url"
                                        alt="Producto"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                        loading="lazy" />
                                    <div v-if="producto.stock === 0"
                                        class="absolute top-4 right-4 bg-gradient-to-br from-red-500 to-pink-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                        Agotado
                                    </div>
                                </div>
                                <h4
                                    class="text-xl font-extrabold mt-5 text-gray-100 tracking-tight break-words whitespace-pre-wrap">
                                    {{ producto.nombre }}
                                </h4>
                                <p
                                    class="text-gray-300/90 text-sm mt-3 leading-snug min-h-[3.5rem] break-words whitespace-pre-wrap">
                                    {{ producto.descripcion || 'Sin descripción' }}
                                </p>
                                <div class="mt-6 space-y-3.5">
                                    <div class="flex items-center justify-between">
                                        <p
                                            class="text-xl font-black bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">
                                            Bs.{{ producto.precio }}
                                        </p>
                                        <div class="flex flex-col gap-2">
                                            <span
                                                class="px-2.5 py-1.5 bg-cyan-400/10 text-cyan-400/90 text-xs font-semibold rounded-lg border border-cyan-400/20">
                                                Stock: {{ producto.stock }}
                                            </span>
                                            <span
                                                class="px-2.5 py-1.5 bg-blue-400/10 text-blue-400/90 text-xs font-semibold rounded-lg border border-blue-400/20">
                                                {{ producto.color?.color || 'N/A' }}
                                            </span>
                                            <span
                                                class="px-2.5 py-1.5 bg-blue-400/10 text-blue-400/90 text-xs font-semibold rounded-lg border border-blue-400/20">
                                                ID: {{ producto.id || 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button @click="redirectToPrecompra(producto)"
                                    class="w-full mt-4 px-6 py-3 bg-gradient-to-r from-cyan-500/80 to-blue-600/80 text-white font-semibold rounded-xl shadow-lg transition-all duration-300 hover:shadow-cyan-500/40 hover:to-blue-500/90 hover:scale-[1.02]"
                                    style="position: relative; z-index: 10;">
                                    <i class="fas fa-shopping-cart text-sm"></i>
                                    <span> Ver Producto</span>
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-center gap-3 mt-6">
                            <button
                                v-for="pagina in Math.ceil(productosAgrupadosSinPaginacion[categoria].length / productosPorPagina)"
                                :key="pagina" @click="cambiarPagina(categoria, pagina)" :class="[
                                    'px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-300',
                                    currentPages[categoria] === pagina
                                        ? 'bg-cyan-400 text-gray-900'
                                        : 'bg-gray-800 text-cyan-300 hover:bg-cyan-600/30 hover:text-white'
                                ]">
                                {{ pagina }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="relative py-8 text-center overflow-hidden mt-12">
            <div
                class="absolute inset-0 bg-gradient-to-br from-gray-900/95 via-slate-900/95 to-black backdrop-blur-2xl">
            </div>
            <div class="relative z-10 flex flex-col items-center space-y-3">
                <p
                    class="text-xl sm:text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-400 animate-gradient-x">
                    Poliméricos Dial Bolivia
                </p>
                <p
                    class="text-sm sm:text-base font-medium text-gray-300/90 hover:text-gray-100 transition-all duration-300">
                    &copy; {{ new Date().getFullYear() }} Innovación en Soluciones Industriales
                </p>
                <div
                    class="absolute top-0 w-full h-px bg-gradient-to-r from-transparent via-cyan-400/50 to-transparent">
                </div>
                <div
                    class="absolute -top-20 left-0 w-48 h-48 bg-gradient-to-r from-cyan-600/20 to-blue-600/20 rounded-full blur-3xl opacity-30">
                </div>
            </div>
        </footer>
    </AppLayout>
</template>
