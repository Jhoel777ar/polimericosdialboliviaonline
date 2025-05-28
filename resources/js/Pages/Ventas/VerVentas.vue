<script setup>
import { ref, computed, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Swal from 'sweetalert2';
import axios from 'axios';

const props = defineProps({
    ventas: Array,
    codigosQR: Array,
    error: String
});

onMounted(() => {
    if (props.error) {
        Swal.fire({
            title: 'Error',
            text: props.error,
            icon: 'error',
            confirmButtonText: 'Aceptar'
        });
    }
});

// Estado del modal y archivos
const showModal = ref(false);
const selectedVenta = ref(null);
const selectedFile = ref(null);
const montoReportado = ref(null)
const descripcion = ref('')
const getImageUrl = (path) => `/storage/${path}`;
/**
 * @param {number} totalVenta 
 * @returns {Object|null}
 */
const getCodigoQRSeleccionado = (totalVenta) => {
    try {
        if (isNaN(totalVenta) || totalVenta <= 0) {
            throw new Error('El monto de la venta no es válido');
        }
        const fechaActual = new Date();
        const codigosValidos = props.codigosQR.filter(qr => new Date(qr.fecha_expiracion) >= fechaActual);
        const qrSeleccionado = codigosValidos.find(qr => parseFloat(qr.monto_aceptado) >= totalVenta);
        if (!qrSeleccionado) {
            Swal.fire('Error', 'El monto de la venta excede lo permitido por los códigos QR. Debe pagarlo con un comprobante por WhatsApp.', 'error');
            return null;
        }
        return qrSeleccionado;
    } catch (error) {
        Swal.fire('Error', error.message || 'Ocurrió un error al seleccionar el código QR.', 'error');
        return null;
    }
};

/**
 * @param {Object} venta 
 */
const abrirModal = (venta) => {
    const qrSeleccionado = getCodigoQRSeleccionado(venta.Total);
    if (!qrSeleccionado) return;

    selectedVenta.value = venta;
    showModal.value = true;
};
const cerrarModal = () => {
    showModal.value = false;
    selectedVenta.value = null;
    selectedFile.value = null;
    montoReportado.value = null
    descripcion.value = ''
};
const subirComprobante = async () => {
    if (!selectedVenta.value?.id) {
        Swal.fire('Error', 'No hay una venta seleccionada.', 'error');
        return;
    }
    if (!selectedFile.value) {
        Swal.fire('Error', 'Por favor, seleccione un archivo.', 'error');
        return;
    }
    if (!montoReportado.value) {
        Swal.fire('Error', 'Por favor, ingresa el monto reportado transferido.', 'error');
        return;
    }
    const formData = new FormData();
    formData.append('ID_Venta', selectedVenta.value.id);
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
        Swal.fire('Éxito', 'Comprobante subido correctamente.', 'success').then(() => {
            location.reload();
        });
    } catch (error) {
        Swal.fire('Error', 'Hubo un problema al subir el comprobante.', 'error');
    }
};
const handleFileChange = (e) => {
    const file = e.target.files[0]
    if (file) {
        selectedFile.value = file
    }
}

</script>
<template>
    <AppLayout title="Mis Compras">
        <div
            class="w-full mx-auto p-4 sm:p-6 md:p-8 lg:p-12 rounded-2xl bg-gradient-to-br from-gray-900 via-black to-gray-900 relative overflow-hidden shadow-2xl shadow-indigo-500/20">
            <div class="absolute inset-0 bg-noise opacity-5 mix-blend-overlay"></div>
            <div
                class="absolute inset-0 bg-gradient-to-br from-indigo-900/40 to-cyan-900/20 backdrop-blur-3xl rounded-2xl border border-white/10">
            </div>
            <div class="relative z-10 space-y-8">
                <h1
                    class="text-3xl sm:text-4xl md:text-5xl font-bold text-center mb-8 bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent drop-shadow-[0_5px_15px_rgba(96,165,250,0.3)]">
                    Mis Compras
                </h1>
                <p class="text-sm text-gray-300 text-center px-4 sm:px-6 md:px-8">
                    Los productos que no tengan el comprobante de pago enviado dentro de una semana serán eliminados y
                    el artículo volverá a estar disponible en stock. Si el pago ha sido recibido, se procederá a
                    verificar que el monto sea correcto. Una vez confirmado, el vendedor actualizará el estado del
                    pedido y se realizará el envío o la entrega correspondiente. En caso de que la entrega sea por
                    envío, el producto será enviado a la dirección proporcionada; si la compra es local, el cliente
                    podrá recoger el producto directamente en el establecimiento.
                </p>
                <div v-if="ventas.length === 0"
                    class="text-center py-8 rounded-xl bg-white/5 backdrop-blur-lg border border-white/10">
                    <p class="text-xl font-light text-gray-400">No tienes ventas registradas.</p>
                </div>
                <div v-for="venta in ventas" :key="venta.id"
                    class="group relative p-6 sm:p-8 rounded-2xl bg-gradient-to-br from-white/5 to-white/[0.01] backdrop-blur-xl border border-white/10 hover:border-cyan-400/30 transition-all duration-300 hover:shadow-2xl hover:shadow-cyan-500/10">
                    <div class="flex flex-wrap items-center gap-4 mb-6 pb-6 border-b border-white/10">
                        <span
                            class="px-4 py-2 rounded-full bg-gradient-to-r from-cyan-500/30 to-blue-500/30 text-cyan-300 font-semibold text-sm backdrop-blur-sm">
                            #{{ venta.id }}
                        </span>
                        <span
                            class="text-xl font-medium bg-gradient-to-r from-white to-cyan-300 bg-clip-text text-transparent">
                            {{ new Date(venta.Fecha_Venta).toLocaleDateString() }}
                        </span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-8">
                        <div
                            class="p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/5 hover:bg-white/10 transition-all">
                            <p class="text-sm font-light text-gray-400">Total</p>
                            <p class="text-2xl font-bold text-cyan-400">Bs. {{ venta.Total }}</p>
                        </div>
                        <div
                            class="p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/5 hover:bg-white/10 transition-all">
                            <p class="text-sm font-light text-gray-400">Con Descuento</p>
                            <p class="text-2xl font-bold text-green-400">Bs. {{ venta.Totalcondescuento }}</p>
                        </div>
                        <div
                            class="p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/5 hover:bg-white/10 transition-all">
                            <p class="text-sm font-light text-gray-400">Estado</p>
                            <p class="text-2xl font-bold text-purple-400">{{ venta.Estado }}</p>
                        </div>
                        <div
                            class="p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/5 hover:bg-white/10 transition-all">
                            <p class="text-sm font-light text-gray-400">Método de Pago</p>
                            <p class="text-lg font-semibold text-blue-300">{{ venta.Método_Pago }}</p>
                        </div>
                        <div
                            class="p-4 rounded-xl bg-white/5 backdrop-blur-sm border border-white/5 hover:bg-white/10 transition-all">
                            <p class="text-sm font-light text-gray-400">Tipo de Entrega</p>
                            <p class="text-lg font-semibold text-cyan-300">{{ venta.tipo_entrega }}</p>
                        </div>
                    </div>
                    <h3
                        class="text-lg font-semibold text-white/80 mb-4 bg-gradient-to-r from-cyan-400/20 to-transparent p-2 rounded-lg">
                        Productos Comprados
                    </h3>
                    <ul class="space-y-3">
                        <li v-for="detalle in venta.detalles" :key="detalle.id"
                            class="flex justify-between items-center p-4 rounded-xl bg-gradient-to-r from-white/5 to-white/0 backdrop-blur-lg border border-white/10 hover:border-cyan-400/50 transition-all hover:translate-x-2">
                            <div>
                                <p class="font-medium text-white/90">{{ detalle.producto.nombre }}</p>
                                <p class="text-sm text-gray-400/80 mt-1">
                                    <span class="bg-cyan-900/30 px-2 py-1 rounded-md">Cantidad: {{ detalle.cantidad
                                        }}</span>
                                    <span class="mx-2">|</span>
                                    <span class="bg-purple-900/30 px-2 py-1 rounded-md">Precio: Bs. {{
                                        detalle.precio_unitario }}</span>
                                    <span class="mx-2">|</span>
                                    <span class="bg-green-900/30 px-2 py-1 rounded-md">Color: {{
                                        detalle.producto.color.color }}</span>
                                    <span class="mx-2">|</span>
                                    <span class="bg-yellow-900/30 px-2 py-1 rounded-md">
                                        Categoría: {{ detalle.producto.categoria?.nombre || 'Sin categoría' }}
                                    </span>
                                </p>
                            </div>
                            <span
                                class="bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent font-bold text-xl">
                                Bs. {{ detalle.subtotal }}
                            </span>
                        </li>
                    </ul>
                    <div v-if="venta.envios"
                        class="mt-8 p-6 rounded-xl bg-gradient-to-br from-cyan-500/20 to-blue-500/20 backdrop-blur-2xl border border-cyan-400/50 shadow-lg shadow-cyan-500/10">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="h-8 w-8 bg-cyan-500/30 rounded-full flex items-center justify-center animate-pulse">
                                📦
                            </div>
                            <h3 class="text-lg font-semibold text-cyan-300">Detalles del Envío</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div class="space-y-1">
                                <p class="text-gray-400">Empresa</p>
                                <p class="text-white/90">{{ venta.envios.Empresa_Envio }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-gray-400">Guía</p>
                                <p class="text-white/90">{{ venta.envios.Numero_Guia }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-gray-400">Dirección</p>
                                <p class="text-white/90">{{ venta.envios.Dirrecion }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-gray-400">Fecha Envio</p>
                                <p class="text-white/90">{{ venta.envios.Fecha_Envio }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-gray-400">Estado</p>
                                <span :class="{
                                    'text-yellow-400 animate-pulse': venta.envios.Estado_Envio === 'pendiente',
                                    'text-orange-400': venta.envios.Estado_Envio === 'en tránsito',
                                    'text-green-400': venta.envios.Estado_Envio === 'entregado'
                                }" class="font-semibold">
                                    {{ venta.envios.Estado_Envio }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Si no hay comprobantes de pago -->
                    <div v-if="!venta.comprobante_pago.length"
                        class="mt-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-center shadow-md shadow-red-500/10">
                        <p class="text-lg font-semibold mb-4">
                            No tiene un comprobante de pago. Esta compra se eliminará en una semana.
                        </p>
                        <button @click="abrirModal(venta)"
                            class="px-6 py-2 rounded-full bg-gradient-to-r from-red-500 to-pink-500 text-white font-bold hover:from-pink-500 hover:to-red-500 transition-all">
                            Subir Comprobante
                        </button>
                    </div>
                    <div v-else>
                        <div v-if="venta.comprobante_pago.length > 1"
                            class="p-4 mt-6 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-400 text-center shadow-md shadow-blue-500/10">
                            <p class="text-lg font-bold">
                                Ha subido más de un comprobante. Su compra será procesada cuando todos los comprobantes
                                estén aprobados.
                            </p>
                        </div>
                        <div v-if="venta.comprobante_pago.every(c => c.estado === 'aprobado' && c.subido === 1)"
                            class="p-4 mt-6 rounded-xl bg-green-500/10 border border-green-500/30 text-green-400 text-center shadow-md shadow-green-500/10">
                            <p class="text-lg font-bold">
                                Todos los comprobantes han sido aprobados. Su compra será procesada y enviada pronto o
                                puede recojerla en local.
                            </p>
                        </div>
                        <div v-for="comprobante in venta.comprobante_pago" :key="comprobante.id"
                            class="mt-6 p-4 rounded-xl border text-center shadow-md" :class="{
                                'bg-green-500/10 border-green-500/30 text-green-400 shadow-green-500/10': comprobante.estado === 'aprobado' && comprobante.subido === 1,
                                'bg-yellow-500/10 border-yellow-500/30 text-yellow-400 shadow-yellow-500/10': comprobante.estado === 'pendiente',
                                'bg-red-500/10 border-red-500/30 text-red-400 shadow-red-500/10': comprobante.estado === 'rechazado' || comprobante.subido === 0
                            }">
                            <div v-if="comprobante.subido === 0" class="mb-4">
                                <p class="text-lg font-semibold">
                                    Su compra ha sido cancelada y se realizó la devolución. Esta venta se eliminará en
                                    una semana.
                                </p>
                            </div>
                            <div v-else>
                                <p v-if="comprobante.estado === 'aprobado'" class="text-lg font-semibold">
                                    Este comprobante ha sido aprobado. Monto reportado: {{ comprobante.monto_reportado
                                    }}.
                                </p>
                                <p v-else-if="comprobante.estado === 'rechazado'" class="text-lg font-semibold">
                                    El comprobante ha sido rechazado. El monto reportado ({{ comprobante.monto_reportado
                                    }}) no coincide con el total o el faltante. Puede volver a subir un comprobante para
                                    completar el pago o del monto que falta.
                                    <button @click="abrirModal(venta)"
                                        class="mt-4 px-6 py-2 rounded-full bg-gradient-to-r from-red-500 to-pink-500 text-white font-bold hover:from-pink-500 hover:to-red-500 transition-all">
                                        Subir Comprobante
                                    </button>
                                </p>
                                <p v-else-if="comprobante.estado === 'pendiente'" class="text-lg font-semibold">
                                    Este comprobante está en revisión. Este proceso puede tardar un día o más. Por
                                    favor, espere.
                                </p>
                            </div>
                        </div>
                    </div>


                </div>

                <!-- Modal -->
                <div v-if="showModal"
                    class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-black/10">
                    <div
                        class="bg-gradient-to-br from-gray-800 via-gray-900 to-gray-900 border border-white/10 p-6 rounded-2xl shadow-2xl shadow-cyan-500/20 w-full max-w-md mx-4 transition-all duration-300 overflow-auto max-h-[90vh]">
                        <h2
                            class="text-2xl font-bold bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent mb-5">
                            Subir Comprobante
                        </h2>
                        <div class="space-y-4">
                            <p class="text-gray-300/90 font-medium">Venta #{{ selectedVenta?.id }}</p>
                            <p class="text-cyan-400 font-bold text-lg drop-shadow-[0_1px_1px_rgba(34,211,238,0.4)]">
                                Monto Ventas: Bs. {{ selectedVenta?.Total }}
                            </p>
                            <div v-if="selectedVenta?.Totalcondescuento && selectedVenta.Totalcondescuento > 0">
                                <p class="text-green-400 font-bold bg-green-900/30 px-3 py-2 rounded-lg">
                                    Total con Descuento: Bs. {{ selectedVenta.Totalcondescuento }}
                                </p>
                            </div>
                            <div v-if="selectedVenta && getCodigoQRSeleccionado(selectedVenta.Total)"
                                class="bg-gray-800/40 p-4 rounded-xl border border-white/5">
                                <img :src="getImageUrl(getCodigoQRSeleccionado(selectedVenta.Total)?.qr_image_url)"
                                    alt="Código QR"
                                    class="w-40 h-40 rounded-lg mx-auto mb-3 border-2 border-white/10 shadow-md" />
                                <p class="text-gray-300 text-center mb-1">
                                    Monto QR Aceptado: Bs. {{
                                        getCodigoQRSeleccionado(selectedVenta.Total)?.monto_aceptado }}
                                </p>
                                <p class="text-gray-400/80 text-sm text-center">
                                    Expira: {{ new
                                        Date(getCodigoQRSeleccionado(selectedVenta.Total)?.fecha_expiracion).toLocaleDateString()
                                    }}
                                </p>
                            </div>
                            <input type="file" accept="image/*" @change="handleFileChange"
                                class="w-full file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gradient-to-r file:from-cyan-500/80 file:to-blue-600/80 file:text-white file:font-semibold hover:file:bg-gradient-to-l bg-gray-800/30 border border-white/10 focus:ring-2 focus:ring-cyan-400 rounded-xl transition-all cursor-pointer" />
                            <div v-if="selectedFile" class="bg-gray-800/40 p-4 rounded-xl border border-white/5">
                                <p class="text-gray-300">Archivo Seleccionado: {{ selectedFile.name }}</p>
                                <p class="text-gray-400 text-sm">Tamaño: {{ (selectedFile.size / 1024).toFixed(2) }} KB
                                </p>
                            </div>
                            <div>
                                <label for="montoReportado" class="text-gray-300">Monto Reportado:</label>
                                <input type="number" id="montoReportado" v-model="montoReportado"
                                    class="w-full p-3 bg-gray-800 text-gray-200 rounded-lg border border-gray-700 focus:ring-2 focus:ring-cyan-400 mt-2"
                                    step="0.01" min="0" max="1000000" placeholder="Bs." />
                            </div>
                            <div>
                                <label for="descripcion" class="text-gray-300">Descripción:</label>
                                <textarea id="descripcion" v-model="descripcion"
                                    class="w-full p-3 bg-gray-800 text-gray-200 rounded-lg border border-gray-700 focus:ring-2 focus:ring-cyan-400 mt-2"
                                    rows="4" placeholder="Escriba una descripción..."></textarea>
                            </div>
                        </div>
                        <div class="flex justify-between gap-3 mt-8">
                            <button @click="cerrarModal"
                                class="px-5 py-2.5 bg-gradient-to-bl from-gray-700 to-gray-800 hover:from-gray-600 hover:to-gray-700 text-gray-300 rounded-xl font-semibold shadow-md hover:shadow-gray-700/20 transition-all duration-300">
                                Cancelar
                            </button>
                            <button @click="subirComprobante"
                                class="px-6 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold rounded-xl shadow-lg hover:shadow-cyan-500/30 hover:scale-[1.02] transition-all duration-300">
                                Enviar
                            </button>
                        </div>
                    </div>
                </div>



            </div>
        </div>

    </AppLayout>
</template>
<style>
.shadow-xl {
    box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.3);
}
</style>
