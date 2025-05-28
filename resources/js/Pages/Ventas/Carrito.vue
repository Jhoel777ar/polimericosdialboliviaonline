<script setup>
import { computed, ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';
import Swal from 'sweetalert2';
import { Head, Link, router } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';

const props = defineProps({
    carrito: Object,
    usuario: Object,
    codigosQR: Array,
});

//eliminar producto
const eliminarProducto = (id) => {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Este producto será eliminado del carrito.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/carrito/eliminar/${id}`)
                .then(() => {
                    Swal.fire('Eliminado', 'El producto ha sido eliminado del carrito.', 'success');
                    location.reload();
                })
                .catch(error => {
                    Swal.fire('Error', 'No se pudo eliminar el producto. Inténtalo de nuevo.', 'error');
                    console.error('Error al eliminar el producto del carrito:', error);
                });
        }
    });
};
//calcular total
const totalCarrito = computed(() => {
    return Object.values(props.carrito).reduce((total, producto) => {
        if (producto.activo && producto.stock > 0) {
            return total + producto.precio * producto.cantidad;
        }
        return total;
    }, 0).toFixed(2);
});
//Actualizar cantidad
const actualizarCantidad = (id, nuevaCantidad) => {
    if (nuevaCantidad < 1) {
        Swal.fire('Atención', 'La cantidad debe ser mayor o igual a 1.', 'warning');
        return;
    }
    axios.post(`/carrito/actualizar/${id}`, { cantidad: nuevaCantidad })
        .then(() => {
            Swal.fire('Actualizado', 'La cantidad ha sido actualizada.', 'success');
            location.reload();
        })
        .catch(error => {
            if (error.response && error.response.data.error) {
                Swal.fire('Error', error.response.data.error, 'error');
            } else {
                Swal.fire('Error', 'No se pudo actualizar la cantidad. Inténtalo de nuevo.', 'error');
            }
        });
};
//controlar productos
const tieneProductosValidos = computed(() => {
    return Object.values(props.carrito).some(producto => producto.activo && producto.stock > 0);
});
//Precargar datos y mostrar modal
const mostrarModal = ref(false);
const formData = ref({
    email: '',
    nombre: '',
    ci: '',
    telefono: '',
    direccion: ''
});
watch(() => props.usuario, (usuario) => {
    if (usuario) {
        formData.value.email = usuario.email;
        formData.value.nombre = usuario.name;
        formData.value.ci = usuario.ci || '';
        formData.value.telefono = usuario.telefono || '';
    } else {
        formData.value.email = '';
        formData.value.nombre = '';
        formData.value.ci = '';
        formData.value.telefono = '';
    }
}, { immediate: true });
//gestiona ventas y actuliza productos y envios
const validarproductos = () => {
    if (!formData.value.nombre || !formData.value.ci || !formData.value.telefono || !formData.value.tipo_entrega) {
        Swal.fire('Error', 'Todos los campos son obligatorios. Por favor, completa todos los datos.', 'warning');
        return;
    }

    if (formData.value.tipo_entrega === 'envio' && !formData.value.direccion) {
        Swal.fire('Error', 'Debes ingresar una dirección para el envío.', 'warning');
        return;
    }

    if (codigoQRSeleccionado.value) {
        if (!selectedFile.value) {
            Swal.fire('Error', 'Debes subir un comprobante de pago.', 'warning');
            return;
        }

        if (!montoReportado.value) {
            Swal.fire('Error', 'Por favor, ingresa el monto reportado transferido.', 'error');
            return;
        }
    }

    const productosValidacion = Object.values(props.carrito).map(producto => ({
        id: producto.id,
        cantidad: producto.cantidad,
        precio: producto.precio,
    }));
    const usuarioData = props.usuario
        ? {
            nombre: formData.value.nombre,
            ci: formData.value.ci,
            telefono: formData.value.telefono,
        }
        : {
            email: formData.value.email,
            nombre: formData.value.nombre,
            ci: formData.value.ci,
            telefono: formData.value.telefono,
        };
    axios
        .post('/validar-productos-stock', {
            productos: productosValidacion,
            tipo_entrega: formData.value.tipo_entrega,
            direccion: formData.value.direccion,
            totalConDescuento: totalConDescuento.value || 0,
            cuponId: cuponId.value,
            ...usuarioData,
        })
        .then(response => {
            if (response.data.venta_id) {
                ventaId.value = response.data.venta_id;
                Swal.fire('¡Su compra fue exitosa!', `ID de su compra: ${ventaId.value}`, 'success').then(() => {
                    subirComprobante();
                    generarFactura();
                });
            } else {
                Swal.fire('¡No se registro la venta!', 'Intente nuevamnete, la validacion fue exitosa.', 'success');
            }
        })
        .catch(error => {
            const errores = error.response?.data?.message || ['Error desconocido. Inténtalo de nuevo.'];
            Swal.fire({
                title: 'Error',
                html: Array.isArray(errores)
                    ? `<ul>${errores.map(err => `<li>${err}</li>`).join('')}</ul>`
                    : errores,
                icon: 'error',
            });
        })
        .finally(() => {
            mostrarModal.value = false;
        });
};

//cupon
const cupon = ref('');
const descuento = ref(0);
const tipoDescuento = ref('');
const descuentoAplicado = ref(false);
const cuponId = ref(null);
const totalConDescuento = computed(() => {
    const total = parseFloat(totalCarrito.value);
    return (total - descuento.value).toFixed(2);
});
const aplicarCupon = () => {
    if (!cupon.value) {
        Swal.fire('Error', 'Por favor, introduce un código de cupón.', 'warning');
        return;
    }
    if (descuentoAplicado.value) {
        Swal.fire('Error', 'Ya se aplicó un cupón. No puedes aplicar más de uno.', 'error');
        return;
    }
    axios
        .post('/validar-cupon', { codigo: cupon.value })
        .then((response) => {
            if (response.data.success) {
                const { tipo, valor, id_cupon } = response.data.cupon;
                cuponId.value = id_cupon;
                if (tipo === 'sin_descuento' || parseFloat(valor) === 0) {
                    Swal.fire('Aviso', 'Este cupón no tiene descuentos.', 'info');
                    descuentoAplicado.value = false;
                    descuento.value = 0;
                    tipoDescuento.value = '';
                    cuponId.value = null;
                    return;
                }
                let descuentoCalculado = 0;
                if (tipo === 'descuento_fijo') {
                    descuentoCalculado = parseFloat(valor);
                    tipoDescuento.value = `Aplicaste un descuento de ${descuentoCalculado} Bs.`;
                } else if (tipo === 'porcentaje') {
                    const total = parseFloat(totalCarrito.value);
                    descuentoCalculado = (total * parseFloat(valor)) / 100;
                    tipoDescuento.value = `Aplicaste un descuento del ${valor}.`;
                }
                descuento.value = Math.min(descuentoCalculado, parseFloat(totalCarrito.value));
                descuentoAplicado.value = true;
                Swal.fire('Éxito', 'Cupón aplicado correctamente.', 'success');
            } else {
                cuponId.value = null;
                Swal.fire('Error', response.data.message, 'error');
            }
        })
        .catch((error) => {
            Swal.fire(
                'Error',
                error.response?.data?.message || 'Hubo un problema al validar el cupón.',
                'error'
            );
        });
};
// Función para mostrar/ocultar el menú en móviles
const menuVisible = ref(false);
const toggleMenu = () => {
    menuVisible.value = !menuVisible.value;
};
const logout = () => {
    router.post(route('logout'));
};
//factura
const ventaId = ref(null);
const mostrarModalFactura = ref(false);
const facturaPDF = ref('');

const generarFactura = () => {
    if (!ventaId.value) {
        Swal.fire('Error', 'No se encontró el ID de la venta.', 'error');
        return;
    }
    Swal.fire({
        title: 'Generando factura...',
        text: 'Por favor, espera mientras se genera el PDF.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    axios.get(`/factura/${ventaId.value}`)
        .then(response => {
            if (response.data.pdf) {
                facturaPDF.value = `data:application/pdf;base64,${response.data.pdf}`;
                mostrarModalFactura.value = true;
                Swal.close();
                descargarFactura();
            } else {
                Swal.fire('Error', 'No se pudo generar la factura.', 'error');
            }
        })
        .catch(error => {
            console.error('Error al generar la factura:', error.response?.data || error);
            Swal.fire('Error', 'No se pudo generar la factura.', 'error');
        });
};
const descargarFactura = () => {
    const link = document.createElement('a');
    link.href = facturaPDF.value;
    link.download = `factura-${ventaId.value}.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};
const cerrarModal = () => {
    mostrarModalFactura.value = false;
    Inertia.visit('/ventas');
};

//codigos qr 
const codigoQRSeleccionado = computed(() => {
    if (!props.codigosQR || props.codigosQR.length === 0) return null;
    const fechaActual = new Date();
    const codigosValidos = props.codigosQR.filter(qr => new Date(qr.fecha_expiracion) >= fechaActual);
    const qrSeleccionado = codigosValidos
        .filter(qr => parseFloat(qr.monto_aceptado) >= totalCarrito.value)
        .sort((a, b) => parseFloat(a.monto_aceptado) - parseFloat(b.monto_aceptado))
        .shift();
    return qrSeleccionado || null;
});

const getImageUrl = (path) => `/storage/${path}`;
const selectedFile = ref(null);
const montoReportado = ref(null)
const descripcion = ref('')
const handleFileChange = (event) => {
    const file = event.target.files[0];
    selectedFile.value = file;
};
const subirComprobante = async () => {
    if (!ventaId.value) {
        Swal.fire('Error', 'La venta no se ha registrado correctamente. Intente nuevamente.', 'error');
        return;
    }
    const formData = new FormData();
    formData.append('ID_Venta', ventaId.value);
    formData.append('monto_reportado', montoReportado.value);
    if (descripcion.value) {
        formData.append('description', descripcion.value);
    }
    formData.append('image', selectedFile.value);
    try {
        const response = await axios.post('/comprobantes/guardar', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
    } catch (error) {
        Swal.fire('Error', 'Hubo un problema al subir el comprobante. Puede volvera a subir en el sector de Tus Compras.', 'error');
    }
};
</script>

<template>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <header
        class="bg-gradient-to-r from-gray-950 via-gray-900 to-black/95 text-white shadow-2xl py-4 backdrop-blur-xl border-b border-gray-800/50">
        <div class="container mx-auto flex justify-between items-center px-4 lg:px-8">
            <div class="group relative">
                <div
                    class="absolute -inset-2 bg-gradient-to-r from-teal-500 via-cyan-500 to-blue-600 rounded-xl blur-lg opacity-20 group-hover:opacity-40 transition-all duration-500 animate-pulse">
                </div>
                <a href="/" class="relative text-3xl md:text-4xl font-extrabold tracking-tight bg-gradient-to-r from-teal-400 via-cyan-400 to-blue-400 bg-clip-text text-transparent 
               hover:animate-pulse transition-all duration-300 flex items-center gap-2">
                    <i
                        class="fas fa-shopping-cart text-teal-400 group-hover:text-cyan-300 transition-colors duration-300"></i>
                    Mi Carrito
                </a>
            </div>
            <nav
                class="hidden md:flex items-center gap-6 backdrop-blur-2xl rounded-xl bg-gradient-to-br from-gray-900/80 to-black/80 px-6 py-3 border border-gray-700/40 shadow-lg">
                <a href="/" class="relative px-6 py-2 rounded-lg text-gray-300 hover:text-white transition-all duration-300 
               bg-gray-800/50 hover:bg-gray-700/70 backdrop-blur-md border border-gray-600/30 
               hover:border-teal-400/50 hover:shadow-[0_0_15px_rgba(20,184,166,0.3)]
               flex items-center gap-2 group">
                    <i class="fas fa-home text-teal-400 group-hover:text-teal-300 transition-colors duration-300"></i>
                    <span
                        class="font-semibold bg-gradient-to-r from-gray-200 to-teal-400 bg-clip-text text-transparent">Inicio</span>
                </a>
                <template v-if="$page.props.auth.user">
                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="relative px-6 py-2 rounded-lg text-gray-300 hover:text-white transition-all duration-300 
                 bg-gray-800/50 hover:bg-gray-700/70 backdrop-blur-md border border-gray-600/30 
                 hover:border-teal-400/50 hover:shadow-[0_0_15px_rgba(20,184,166,0.3)]
                 flex items-center gap-2 group">
                        <i
                            class="fas fa-box-open text-teal-400 group-hover:text-teal-300 transition-colors duration-300"></i>
                        <span
                            class="font-semibold bg-gradient-to-r from-gray-200 to-teal-400 bg-clip-text text-transparent">Productos</span>
                    </NavLink>
                </template>
                <template v-if="$page.props.auth.user">
                    <NavLink :href="route('ventas.ver')" :active="route().current('ventas.ver')" class="relative px-6 py-2 rounded-lg text-gray-300 hover:text-white transition-all duration-300 
                 bg-gray-800/50 hover:bg-gray-700/70 backdrop-blur-md border border-gray-600/30 
                 hover:border-teal-400/50 hover:shadow-[0_0_15px_rgba(20,184,166,0.3)]
                 flex items-center gap-2 group">
                        <i
                            class="fas fa-shopping-bag text-teal-400 group-hover:text-teal-300 transition-colors duration-300"></i>
                        <span
                            class="font-semibold bg-gradient-to-r from-gray-200 to-teal-400 bg-clip-text text-transparent">Tus
                            Compras</span>
                    </NavLink>
                </template>
                <div class="relative" v-if="$page.props.auth.user">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full border-2 border-gray-700/50 focus:outline-none focus:border-teal-400 transition-all duration-300 
                     hover:border-teal-300 hover:shadow-[0_0_10px_rgba(20,184,166,0.3)]">
                                <img class="size-9 rounded-full object-cover"
                                    :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </button>
                            <span v-else class="inline-flex rounded-md">
                                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-700/50 rounded-md text-sm font-medium text-gray-300 
                       bg-gray-800/50 hover:bg-gray-700/70 hover:text-white hover:border-teal-400 focus:outline-none 
                       focus:bg-gray-700 focus:border-teal-400 transition-all duration-300">
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
                            <div
                                class="block px-4 py-2 text-xs text-gray-300 bg-gradient-to-r from-gray-900 to-teal-900/80 backdrop-blur-md">
                                Administrar cuenta
                            </div>
                            <DropdownLink :href="route('profile.show')"
                                class="hover:bg-teal-900/50 hover:text-white transition-all duration-300">
                                Perfil
                            </DropdownLink>
                            <form @submit.prevent="logout">
                                <DropdownLink as="button"
                                    class="hover:bg-teal-900/50 hover:text-white transition-all duration-300">
                                    Cerrar Sesión
                                </DropdownLink>
                            </form>
                        </template>
                    </Dropdown>
                </div>
            </nav>
            <button @click="toggleMenu" class="md:hidden p-3 rounded-xl backdrop-blur-lg border border-gray-700/50 bg-gray-800/70 hover:bg-gray-700/90 
             transition-all duration-300 hover:shadow-[0_0_10px_rgba(20,184,166,0.3)]">
                <i class="fas fa-bars text-xl bg-gradient-to-r from-teal-400 to-cyan-400 bg-clip-text text-transparent 
             hover:animate-pulse"></i>
            </button>
        </div>
        <div v-if="menuVisible" class="md:hidden mt-4 mx-4 bg-gradient-to-br from-gray-900/90 to-black/90 backdrop-blur-2xl rounded-2xl border border-gray-700/50 
           shadow-xl shadow-teal-900/30 p-4 space-y-4">
            <a href="/"
                class="block px-6 py-3 rounded-xl bg-gray-800/50 hover:bg-gray-700/70 backdrop-blur-md border border-gray-600/30 
           text-gray-300 hover:text-white hover:border-teal-400/50 transition-all duration-300 flex items-center gap-2 group">
                <i class="fas fa-home text-teal-400 group-hover:text-teal-300 transition-colors duration-300"></i>
                <span
                    class="font-medium bg-gradient-to-r from-gray-200 to-teal-400 bg-clip-text text-transparent">Inicio</span>
            </a>
            <template v-if="$page.props.auth.user">
                <NavLink :href="route('dashboard')" :active="route().current('dashboard')"
                    class="block px-6 py-3 rounded-xl bg-gray-800/50 hover:bg-gray-700/70 backdrop-blur-md border border-gray-600/30 
               text-gray-300 hover:text-white hover:border-teal-400/50 transition-all duration-300 flex items-center gap-2 group">
                    <i
                        class="fas fa-box-open text-teal-400 group-hover:text-teal-300 transition-colors duration-300"></i>
                    <span
                        class="font-medium bg-gradient-to-r from-gray-200 to-teal-400 bg-clip-text text-transparent">Productos</span>
                </NavLink>
            </template>
            <template v-if="$page.props.auth.user">
                <NavLink :href="route('ventas.ver')" :active="route().current('ventas.ver')"
                    class="block px-6 py-3 rounded-xl bg-gray-800/50 hover:bg-gray-700/70 backdrop-blur-md border border-gray-600/30 
               text-gray-300 hover:text-white hover:border-teal-400/50 transition-all duration-300 flex items-center gap-2 group">
                    <i
                        class="fas fa-shopping-bag text-teal-400 group-hover:text-teal-300 transition-colors duration-300"></i>
                    <span
                        class="font-medium bg-gradient-to-r from-gray-200 to-teal-400 bg-clip-text text-transparent">Tus
                        Compras</span>
                </NavLink>
            </template>
            <div class="relative" v-if="$page.props.auth.user">
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full border-2 border-gray-700/50 focus:outline-none focus:border-teal-400 transition-all duration-300 
                   hover:border-teal-300 hover:shadow-[0_0_10px_rgba(20,184,166,0.3)]">
                            <img class="size-9 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url"
                                :alt="$page.props.auth.user.name">
                        </button>
                        <span v-else class="inline-flex rounded-md">
                            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-700/50 rounded-md text-sm font-medium text-gray-300 
                     bg-gray-800/50 hover:bg-gray-700/70 hover:text-white hover:border-teal-400 focus:outline-none 
                     focus:bg-gray-700 focus:border-teal-400 transition-all duration-300">
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
                        <div
                            class="block px-4 py-2 text-xs text-gray-300 bg-gradient-to-r from-gray-900 to-teal-900/80 backdrop-blur-md">
                            Administrar cuenta
                        </div>
                        <DropdownLink :href="route('profile.show')"
                            class="hover:bg-teal-900/50 hover:text-white transition-all duration-300">
                            Perfil
                        </DropdownLink>
                        <form @submit.prevent="logout">
                            <DropdownLink as="button"
                                class="hover:bg-teal-900/50 hover:text-white transition-all duration-300">
                                Cerrar Sesión
                            </DropdownLink>
                        </form>
                    </template>
                </Dropdown>
            </div>
        </div>
    </header>
    <!-- Sección del carrito -->
    <div
        class="carrito-container px-6 py-8 bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white min-h-screen flex flex-col lg:flex-row lg:space-x-8 animate-fade-in">
        <div
            class="carrito-detalles flex-1 bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white rounded-2xl p-8 shadow-2xl">
            <div
                class="session-info backdrop-blur-xl bg-gradient-to-br from-gray-900 via-blue-900/80 to-gray-800 p-4 rounded-2xl border border-white/10 shadow-2xl shadow-blue-900/30 mx-auto max-w-md mb-8 transition-all duration-300 hover:shadow-cyan-500/20 hover:border-cyan-400/20">
                <div v-if="!usuario" class="space-y-3">
                    <p
                        class="text-sm font-medium bg-gradient-to-r from-red-400 to-pink-500 bg-clip-text text-transparent animate-pulse">
                        ⚠️ No tienes una cuenta
                    </p>
                    <a href="/login"
                        class="inline-flex items-center gap-2 text-sm bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent hover:from-cyan-300 hover:to-blue-400 transition-all duration-300
                  before:content-[''] before:absolute before:-bottom-1 before:left-0 before:w-full before:h-0.5 before:bg-gradient-to-r before:from-cyan-400 before:to-blue-500 before:opacity-50 before:hover:opacity-100 before:transition-opacity before:duration-300">
                        <i class="fas fa-sign-in-alt text-cyan-400"></i>
                        <span class="relative">Inicia sesión aquí</span>
                    </a>
                </div>
                <div v-else class="flex items-center justify-center gap-3">
                    <div class="h-2 w-2 bg-gradient-to-r from-green-400 to-cyan-500 rounded-full animate-pulse"></div>
                    <p
                        class="text-sm font-semibold bg-gradient-to-r from-green-400 to-cyan-500 bg-clip-text text-transparent">
                        🎉 Usuario conectado: <span class="font-bold">{{ usuario.name }}</span>
                    </p>
                </div>
            </div>
            <h2
                class="text-4xl font-extrabold text-center mb-8 bg-clip-text text-transparent bg-gradient-to-r from-green-400 to-blue-500">
                Mi Carrito
            </h2>
            <div v-if="Object.keys(props.carrito).length === 0" class="text-center text-gray-400 py-10">
                <p>No hay productos en el carrito.</p>
            </div>
            <div v-else class="overflow-x-auto">
                <table class="table-auto w-full bg-gray-800/60 backdrop-blur-sm rounded-lg shadow-md overflow-hidden">
                    <thead>
                        <tr class="text-left text-gray-300 border-b border-gray-700">
                            <th class="px-6 py-3">Producto</th>
                            <th class="px-6 py-3 text-center">Cantidad</th>
                            <th class="px-6 py-3 text-center">Precio Total</th>
                            <th class="px-6 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(producto, id) in props.carrito" :key="id" :class="{
                            'bg-red-600/40 text-white': producto.stock === 0 || producto.activo === 0,
                            'border-b border-gray-700': true
                        }" class="hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4 flex items-center space-x-4">
                                <img :src="'/storage/' + producto.imagen_url" alt="Producto"
                                    class="w-16 h-16 object-cover rounded-lg shadow-md" />
                                <div>
                                    <h3 class="text-lg font-semibold">{{ producto.nombre }}</h3>
                                    <p class="text-sm text-gray-400">{{ producto.descripcion }}</p>
                                    <p class="text-xs text-gray-500">Producto ID: {{ producto.id }}</p>
                                    <p class="text-sm mt-2" :class="{
                                        'text-red-300': producto.stock === 0,
                                        'text-gray-200': producto.stock > 0 && producto.activo,
                                        'text-yellow-400': producto.activo === 0
                                    }">
                                        {{ producto.stock === 0 ? 'Agotado' : (producto.activo ? `Stock:
                                        ${producto.stock}` : 'Inactivo') }}
                                    </p>
                                    <p class="text-sm text-gray-300">Precio: Bs. {{ producto.precio }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <input type="number" :value="producto.cantidad" :min="1" :max="producto.stock"
                                    @change="actualizarCantidad(id, $event.target.value)"
                                    :disabled="producto.stock === 0"
                                    class="bg-gray-700/80 text-white text-center w-20 rounded-md shadow-md focus:ring-2 focus:ring-green-400 focus:outline-none" />
                            </td>
                            <td class="px-6 py-4 text-center text-green-400 font-bold">
                                Bs. {{ (producto.precio * producto.cantidad).toFixed(2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button @click="eliminarProducto(id)"
                                    class="relative w-auto py-2 px-4 rounded-full font-bold text-white shadow-lg transition-all duration-300 transform 
    bg-gradient-to-r from-red-500 via-pink-500 to-red-600 
    hover:from-red-600 hover:via-pink-600 hover:to-red-500 
    hover:scale-105 disabled:scale-100
    after:content-[''] after:absolute after:inset-0 after:rounded-full after:border-2 after:border-transparent hover:after:border-pink-400 hover:after:shadow-pink-500/30 after:transition-all after:duration-300">
                                    <i class="fas fa-trash mr-2"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Resumen de compra -->
        <div
            class="resumen-compra bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white rounded-2xl shadow-2xl p-8 lg:w-1/3">
            <h3
                class="text-3xl font-extrabold mb-6 text-center bg-clip-text text-transparent bg-gradient-to-r from-green-400 to-blue-500">
                Resumen de Compra
            </h3>
            <div class="text-lg flex justify-between mb-6">
                <span>Total:</span>
                <span class="text-green-400 font-bold">Bs. {{ totalCarrito }}</span>
            </div>
            <div v-if="descuento > 0" class="text-lg flex justify-between mb-6">
                <span class="text-yellow-300">Total con Descuento:</span>
                <span class="text-green-400 font-bold">Bs. {{ totalConDescuento }}</span>
            </div>
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 mb-6 shadow-md">
                <p class="text-lg text-white mb-4">¿Tienes un cupón de descuento?</p>
                <input v-model="cupon" type="text" placeholder="Introduce tu cupón"
                    class="w-full p-3 rounded-lg bg-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-400 transition" />
                <button @click="aplicarCupon" :disabled="descuentoAplicado && descuento > 0 || !tieneProductosValidos"
                    :class="{
                        'opacity-50 cursor-not-allowed': descuentoAplicado && descuento > 0 || !tieneProductosValidos
                    }"
                    class="relative mt-4 w-full py-3 px-6 rounded-lg font-bold text-white shadow-lg transition-all duration-300 transform 
    bg-gradient-to-r from-teal-500 via-green-500 to-emerald-500 
    hover:from-emerald-500 hover:via-green-500 hover:to-teal-500 
    hover:scale-105 disabled:scale-100
    after:content-[''] after:absolute after:inset-0 after:rounded-lg after:border-2 after:border-transparent hover:after:border-green-400 hover:after:shadow-emerald-500/30 after:transition-all after:duration-300">
                    <i class="fas fa-ticket-alt mr-2"></i>
                    {{ descuentoAplicado && descuento > 0 ? 'Cupón Aplicado' : 'Aplicar cupón' }}
                </button>
                <div v-if="tipoDescuento"
                    class="mt-6 bg-green-100/20 text-green-400 p-4 rounded-lg text-center shadow-sm">
                    <p class="font-semibold">{{ tipoDescuento }}</p>
                </div>
                <div v-else-if="!descuento && descuentoAplicado"
                    class="mt-6 bg-yellow-100/20 text-yellow-400 p-4 rounded-lg text-center shadow-sm">
                    <p class="font-semibold">Este cupón no tiene descuentos.</p>
                </div>
            </div>
            <!--ir a pagar-->
            <button @click="mostrarModal = true" :disabled="!tieneProductosValidos"
                class="relative w-full py-3 px-6 rounded-full font-bold text-white shadow-lg transition-all duration-300 transform 
    bg-gradient-to-r from-cyan-500 via-blue-500 to-purple-500 
    hover:from-purple-500 hover:via-blue-500 hover:to-cyan-500 
    hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:scale-100
    after:content-[''] after:absolute after:inset-0 after:rounded-full after:border-2 after:border-transparent hover:after:border-cyan-400 hover:after:shadow-cyan-500/30 after:transition-all after:duration-300">
                <i class="fas fa-shopping-cart mr-2"></i> Ir a pagar
            </button>
        </div>
    </div>
    <!-- Modal para los datos de pago -->
    <div v-if="mostrarModal"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex justify-center items-center z-50 p-4">
        <div
            class="bg-gradient-to-b from-gray-800 via-gray-900 to-gray-800 text-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 transform transition-all duration-300">
            <h2 class="text-3xl font-extrabold text-center mb-6 tracking-wide">
                Datos Personales
            </h2>
            <p class="text-sm text-center text-green-400 bg-green-900 bg-opacity-30 p-3 rounded-lg mb-6 font-medium">
                Por favor, ingrese datos reales y correctos. El producto será enviado a la dirección proporcionada, y si
                es un usuario nuevo, su correo y C.I. serán su contraseña inicial en la plataforma.
            </p>
            <div class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-1">
                        Email
                    </label>
                    <input v-model="formData.email" type="email" id="email"
                        class="w-full p-3 border border-gray-700 rounded-lg bg-gray-800 text-gray-300 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Correo electrónico" :disabled="!!usuario" required />
                    <span v-if="usuario" class="text-xs text-gray-400 italic">
                        El correo ya está registrado.
                    </span>
                </div>
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-300 mb-1">
                        Nombre Completo
                    </label>
                    <input v-model="formData.nombre" type="text" id="nombre"
                        class="w-full p-3 border border-gray-700 rounded-lg bg-gray-800 text-gray-300 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Nombre completo" required maxlength="250" />
                </div>
                <div>
                    <label for="ci" class="block text-sm font-medium text-gray-300 mb-1">
                        C.I. (Sin extensión)
                    </label>
                    <input v-model="formData.ci" type="text" id="ci"
                        class="w-full p-3 border border-gray-700 rounded-lg bg-gray-800 text-gray-300 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Carnet de Identidad" required maxlength="10" pattern="[0-9]*" inputmode="numeric"
                        @keypress="($event) => $event.charCode >= 48 && $event.charCode <= 57 || $event.preventDefault()" />
                </div>
                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-300 mb-1">
                        Teléfono (Sin extensión)
                    </label>
                    <input v-model="formData.telefono" type="tel" id="telefono"
                        class="w-full p-3 border border-gray-700 rounded-lg bg-gray-800 text-gray-300 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Número de teléfono" required maxlength="10" pattern="[0-9]*" inputmode="numeric"
                        @keypress="($event) => $event.charCode >= 48 && $event.charCode <= 57 || $event.preventDefault()" />
                </div>
            </div>
            <h2 class="text-3xl font-extrabold text-center mt-8 mb-6 tracking-wide">
                Envío
            </h2>
            <div>
                <label for="tipo_entrega" class="block text-sm font-medium text-gray-300 mb-1">
                    Tipo de Entrega
                </label>
                <select v-model="formData.tipo_entrega" id="tipo_entrega"
                    class="w-full p-3 border border-gray-700 rounded-lg bg-gray-800 text-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    required>
                    <option value="local">Retiro en Local</option>
                    <option value="envio">Envío a Domicilio</option>
                </select>
            </div>
            <!-- Dirección solo si elige "Envío" -->
            <div v-if="formData.tipo_entrega === 'envio'">
                <label for="direccion" class="block text-sm font-medium text-gray-300 mb-1">
                    Dirección de Envío (Ciudad, Zona, Calle y Nro. Puerta)
                </label>
                <input v-model="formData.direccion" type="text" id="direccion"
                    class="w-full p-3 border border-gray-700 rounded-lg bg-gray-800 text-gray-300 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Dirección de envío" required />
            </div>
            <!-- Sección de pagos con QR -->
            <h2 class="text-2xl font-bold mt-6">Métodos de Pago</h2>
            <div class="mt-4">
                <h3 class="text-lg font-semibold text-gray-400">Pagos con QR</h3>
                <div v-if="codigoQRSeleccionado">
                    <p class="text-gray-300 font-medium">Monto Aceptado: Bs. {{ codigoQRSeleccionado.monto_aceptado }}
                    </p>
                    <p class="text-gray-400 text-sm">Expira el: {{ new
                        Date(codigoQRSeleccionado.fecha_expiracion).toLocaleDateString() }}</p>
                    <img :src="getImageUrl(codigoQRSeleccionado.qr_image_url)" alt="Código QR"
                        class="w-40 h-40 mx-auto mt-3 rounded-lg border-2 border-white/10 shadow-md" />
                </div>
                <div v-else class="text-red-400 font-bold text-center mt-4">
                    No hay código QR disponible. Debe realizar el pago por WhatsApp.
                </div>
                <!--subidas-->
                <div class="justify-between gap-3 mt-8">
                    <input type="file" accept="image/*" @change="handleFileChange"
                        class="w-full file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gradient-to-r file:from-cyan-500/80 file:to-blue-600/80 file:text-white file:font-semibold hover:file:bg-gradient-to-l bg-gray-800/30 border border-white/10 focus:ring-2 focus:ring-cyan-400 rounded-xl transition-all cursor-pointer" />
                    <div v-if="selectedFile" class="bg-gray-800/40 p-4 rounded-xl border border-white/5">
                        <p class="text-gray-300">Archivo Seleccionado: {{ selectedFile.name }}</p>
                        <p class="text-gray-400 text-sm">Tamaño: {{ (selectedFile.size / 1024).toFixed(2) }} KB
                        </p>
                    </div>
                    <div>
                        <label for="montoReportado" class="text-gray-300">Monto Reportado (Bs):</label>
                        <input type="text" id="montoReportado" v-model="montoReportado"
                            class="w-full p-3 bg-gray-800 text-gray-200 rounded-lg border border-gray-700 focus:ring-2 focus:ring-cyan-400 mt-2"
                            placeholder="Bs." pattern="^\d{1,5}(\.\d{0,2})?$" inputmode="decimal"
                            @keypress="($event) => ($event.charCode >= 48 && $event.charCode <= 57) || $event.charCode === 46 || $event.preventDefault()"
                            maxlength="8" required />
                    </div>
                    <div>
                        <label for="descripcion" class="text-gray-300">Descripción:</label>
                        <textarea id="descripcion" v-model="descripcion"
                            class="w-full p-3 bg-gray-800 text-gray-200 rounded-lg border border-gray-700 focus:ring-2 focus:ring-cyan-400 mt-2"
                            rows="4" placeholder="Escriba una descripción..." maxlength="250"></textarea>
                    </div>
                </div>
            </div>
            <!-- Total a cancelar -->
            <div class="mt-8 text-center">
                <span class="text-lg font-semibold text-gray-400">Total a Cancelar:</span>
                <span class="text-lg font-extrabold text-green-400 ml-2">Bs. {{ totalCarrito }}</span>
            </div>
            <div v-if="descuento > 0" class="mt-8 text-center">
                <span class="text-lg font-semibold text-yellow-300">Total con Descuento:</span>
                <span class="text-lg font-extrabold text-green-400 ml-2">Bs. {{ totalConDescuento }}</span>
            </div>
            <div class="mt-10 flex justify-between space-x-4">
                <button @click="mostrarModal = false"
                    class="w-full bg-red-500 hover:bg-red-600 text-white py-3 px-4 rounded-lg font-semibold shadow-md transition-all duration-300">
                    Cancelar
                </button>
                <button @click="validarproductos"
                    class="w-full bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded-lg font-semibold shadow-md transition-all duration-300">
                    <i class="fas fa-shopping-cart"></i> Validar Compra
                </button>
            </div>
        </div>
    </div>
    <!--factura-->
    <div>
        <div v-if="mostrarModalFactura"
            class="fixed inset-0 flex items-center justify-center backdrop-blur-lg bg-gradient-to-br from-gray-900/80 via-gray-800/90 to-gray-900/80">
            <div
                class="bg-gradient-to-br from-gray-800 via-gray-900 to-gray-900 border border-white/10 rounded-3xl shadow-2xl shadow-cyan-500/20 w-full max-w-4xl mx-4 overflow-hidden transition-all duration-300">
                <div class="space-y-6 p-6">
                    <iframe :src="facturaPDF"
                        class="w-full h-[500px] rounded-xl border-2 border-white/5 bg-gray-800 shadow-inner shadow-gray-900/50"
                        frameborder="0"></iframe>
                    <div class="flex justify-end gap-3">
                        <button @click="descargarFactura"
                            class="px-6 py-3 bg-gradient-to-r from-green-400 to-emerald-600 hover:from-emerald-500 hover:to-green-500 text-white font-bold rounded-xl shadow-lg hover:shadow-emerald-500/30 hover:scale-[1.02] transition-all duration-300">
                            <span class="drop-shadow-[0_1px_1px_rgba(0,0,0,0.3)]">Descargar PDF</span>
                        </button>
                        <button @click="cerrarModal"
                            class="px-6 py-3 bg-gradient-to-r from-red-400 to-rose-600 hover:from-rose-500 hover:to-red-500 text-white font-bold rounded-xl shadow-lg hover:shadow-rose-500/30 hover:scale-[1.02] transition-all duration-300">
                            <span class="drop-shadow-[0_1px_1px_rgba(0,0,0,0.3)]">Cerrar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
<style scoped>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    width: 80%;
    max-width: 600px;
}
</style>
