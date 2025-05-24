<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import InputIcon from 'primevue/inputicon';
import IconField from 'primevue/iconfield';
import Button from 'primevue/button';
import Image from 'primevue/image';
import DatePicker from 'primevue/datepicker';
import { show_alerta, show_confirmacion } from '@/utils/alertasSwal';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import Dialog from 'primevue/dialog';
import { ref, onMounted } from 'vue';
import { ConsultaListarInventario } from '@/service/ConsultaListarInventario';
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';
import { router } from '@inertiajs/vue3';

const fechaHoy = new Date ();

const customers = ref();
const selectedCustomer = ref();
const filters = ref(
    {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        insumo: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        comprador: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        cantidad: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        unidad_medida: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        marca: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        modelo: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        precio: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
);

const formatDate = (dateString) => {
    const options = { month: 'short', day: '2-digit', year: 'numeric' };
    const offset = new Date().getTimezoneOffset();
    const offsetMilliseconds = offset * 60 * 1000;
    const date = new Date(new Date(dateString).getTime() + offsetMilliseconds);
    return date.toLocaleDateString('es-ES', options);
};

const nombreCategoria = ref('No definido');

const form2 = useForm ({
    insumo: '',
    marca: '',
    modelo: '',
    cantidad: '',
    unidad_medida: '',
    fecha: '',
    comprador: '',
    comentario: '',
    stock: '',
    ultima_entrada: '',
    precio: '',
    category_id: '',
});

const deleteProducto = (id, insumo) => {
    show_confirmacion('¿Estás seguro?', `¿Estás seguro de eliminar definitivamente el producto ${insumo} de la base de datos?`)
    .then((result) => {
        if (result.isConfirmed) {
            form2.delete(route('productos.destroy', id), {
                onSuccess: (response) => {
                    show_alerta('El producto se ha eliminado definitivamente.', 'success');
                    const categoryId = localStorage.getItem('category_id');
                        ConsultaListarInventario.obtenerIndexInventario(categoryId)
                            .then((response) => {
                                customers.value = response.data;
                            })
                            .catch(error => {
                                console.error('Error al obtener los datos del cliente:', error);
                            });
                },
                onError: (error) => {
                    show_alerta('Hubo un error al intentar eliminar el producto.', 'error');
                    console.error('Error al eliminar el producto:', error);
                }
            });
        }
    });
};

const form = useForm ({
    fechaHoy: fechaHoy,
    fechaHasta: fechaHoy
})

const showModal = ref(false);

const fn_imprimirSalidas = async () => {
    const salidas = await obtenerDatosSalidas();
    if (salidas.length > 0) {
        await printPDF(salidas);
    } else {
        show_alerta("No hay datos para las fechas seleccionadas", "error")
    }
};

const obtenerDatosSalidas = async () => {
    try {
        const response = await axios.get('/fn_ObtenerDatosSalidas', {
            params: {
                fechaHoy: form.fechaHoy,
                fechaHasta: form.fechaHasta
            }
        });
        return response.data;
    } catch (error) {
        console.error('Error al obtener las salidas:', error);
        return [];
    }
};

const printPDF = async (salidas) => {
    const timer = Swal.fire({
        title: '¡Atención!',
        html: 'El historial de salidas se está generando, espere un momento.',
        timer: 999999999, // Establece un valor grande para que parezca indefinido
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const doc = new jsPDF('landscape');

    const headerText = 'LISTA DE SALIDAS';
    const tableHeaders = ['N°', 'EMPRESA', 'PRODUCTO', 'TÉCNICO', 'UNIDAD SALIDA', 'UNIDAD DEVOLUCIÓN', 'FECHA', 'HORA SALIDA'];

    // Mapear los datos para la tabla
    const tableData = salidas.map((item, i) => [
        i + 1,
        item.empresa || '',
        item.producto_nombre || '',
        item.tecnico || '',
        item.unidad_salida || '',
        item.unidad_devolucion || '',
        formatDate(item.fecha) || '', // Asegúrate de que formatDate esté definida y sea la función correcta
        item.hora_salida || '',
    ]);

    let eje_y = 10;

    // Añadir el contenido al PDF
    doc.setFontSize(14);
    doc.setFont('courier', 'bold');
    doc.text(headerText, 15, eje_y += 7);

    doc.autoTable({
        head: [tableHeaders],
        body: tableData,
        startY: eje_y += 10,
        theme: 'grid',
        styles: {
            fontSize: 8,
            cellPadding: 2,
            valign: 'middle',
            halign: 'center',
            textColor: '#000000',
        },
        headStyles: {
            fillColor: '#40c63a',
            textColor: '#ffffff',
        },
        bodyStyles: {
            fillColor: '#eeefef',
            textColor: '#000000',
        },
        columnStyles: {
            0: { fontStyle: 'bold' },
        },
    });

    Swal.close();

    const blob = doc.output('blob');
    const url = URL.createObjectURL(blob);
    window.open(url);
};

const fn_redirigirCarritoCompras = (producto) => {
    if (producto) {
        const productoData = {
            insumo: producto.insumo,
            precio: producto.precio
        };
        localStorage.setItem('productoData', JSON.stringify(productoData));
        router.get(route('carritos.index'));
    } else {
        console.error("Producto no definido");
    }
};

const fn_redirigirSalidasProducto = (productoId, productoNombre) => {
    if (productoId === undefined) {
        console.error("Producto no definido: productoId es undefined");
        return;
    }
    localStorage.setItem('producto_id', productoId);
    localStorage.setItem('producto_nombre', productoNombre);
    router.get(route('salidas.listarSalidasRelacionadas', { producto_id: productoId }));
};

const fn_redirigirSalidas = () => {
    router.get(route('salidas.index'));
};

const fn_redirigirEntradas = () => {
    router.get(route('entradas.index'));
};

const fn_crearProductos = () => {
    router.get(route('productos.create'));
};

const fn_editarProductos = (data) => {
    router.get(route('productos.edit', { producto: data.producto_id }));
};

onMounted(() => {
    const categoryId = localStorage.getItem('category_id');
    ConsultaListarInventario.obtenerIndexInventario(categoryId)
        .then((response) => {
            customers.value = response.data;
        })
        .catch(error => {
            console.error('Error al obtener los datos del cliente:', error);
        });

    nombreCategoria.value = localStorage.getItem('nombre_categoria') || 'No definido';
});

</script>
<template>
    <AppLayout title="Productos">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Productos</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="card text-sm -mt-4">
                        <DataTable scrollable scrollHeight="500px" @row-dblclick="(event) => fn_redirigirSalidasProducto(event.data.producto_id, event.data.insumo)" removableSort v-model:filters="filters" v-model:selection="selectedCustomer" :value="customers" stateStorage="session" stateKey="dt-state-productos-inventario" paginator :rows="5" filterDisplay="menu" selectionMode="single" dataKey="id" :globalFilterFields="['insumo', 'comprador', 'cantidad', 'unidad_medida', 'marca', 'modelo', 'precio']" tableStyle="min-width: 50rem" :rowsPerPageOptions="[5, 10, 20, 50, 100]" paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"currentPageReportTemplate="{first} de {last} de {totalRecords}">
                            <template #header>
                                <div class="flex justify-between flex-col lg:flex-row gap-y-4">
                                    <div class="flex justify-between gap-x-8 flex-col md:flex-row gap-y-2">
                                        <Link v-if="$page.props.user.permissions.includes('Crear Producto')" @click="fn_crearProductos" class="text-white cursor-pointer flex justify-center items-center text-base gap-x-2.5 bg-indigo-700 hover:bg-indigo-800 transform hover:translate-y-[-2px] py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                            <i class="fa-solid fa-cart-plus"></i> Registrar Producto
                                        </Link>
                                        <Link v-if="$page.props.user.permissions.includes('Listar Salidas')" @click="fn_redirigirSalidas" class="text-white cursor-pointer flex justify-center items-center text-base gap-x-2.5 bg-indigo-700 hover:bg-indigo-800 transform hover:translate-y-[-2px] py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                            <i class="fa-solid fa-list"></i> Listar Salidas
                                        </Link>
                                        <Link v-if="$page.props.user.permissions.includes('Listar Entradas')" @click="fn_redirigirEntradas" class="text-white cursor-pointer flex justify-center items-center text-base gap-x-2.5 bg-indigo-700 hover:bg-indigo-800 transform hover:translate-y-[-2px] py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                            <i class="fa-solid fa-list-check"></i> Listar Entradas
                                        </Link>
                                        <Link v-if="$page.props.user.permissions.includes('Imprimir Salidas')" @click="showModal = true" class="text-white cursor-pointer flex justify-center items-center text-base gap-x-2.5 bg-indigo-700 hover:bg-indigo-800 transform hover:translate-y-[-2px] py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                            <i class="fa-solid fa-print"></i> Imprimir Salidas
                                        </Link>
                                    </div>
                                    <div>
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText class="md:w-80 w-full" v-model="filters['global'].value" placeholder="Buscar productos" />
                                        </IconField>
                                    </div>
                                </div>
                                <div class="md:mt-4 mt-3.5">
                                    <div class="font-extrabold text-center dark:text-white text-base">Categoria || {{ nombreCategoria }}</div>
                                </div>
                            </template>
                            <Column class="hidden" field="id" header="#" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.producto_id }}</span>
                                </template>
                            </Column>
                            <Column field="imagen_producto" header="Foto" sortable style="width: 1%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <Image alt="Image" preview>
                                        <template #previewicon>
                                            <i class="pi pi-search"></i>
                                        </template>
                                        <template #image>
                                            <img :src="'/productos_inventario/' + data.imagen_producto" alt="image" style="width: 70px; height: 70px; cursor: pointer; object-fit: cover;" class="rounded-md py-1 mx-auto"/>
                                        </template>
                                        <template #preview="slotProps">
                                            <img :src="'/productos_inventario/' + data.imagen_producto" alt="preview" :style="[slotProps.style, { width: '300px', height: '300px', objectFit: 'contain' }]" @click="slotProps.onClick" />
                                        </template>
                                    </Image>
                                </template>
                            </Column>
                            <Column field="insumo" header="Producto" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.insumo }}</span>
                                </template>
                            </Column>
                            <Column field="comprador" header="Comprador" sortable style="width: 0%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span class="flex justify-center">{{ data.comprador }}</span>
                                </template>
                            </Column>
                            <Column field="cantidad" header="Cantidad" sortable style="width: 0%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span class="flex justify-center">{{ data.cantidad }} {{ data.unidad_medida }}</span>
                                </template>
                            </Column>
                            <Column field="marca" header="Marca" sortable style="width: 0%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span class="flex justify-center">{{ data.marca }}</span>
                                </template>
                            </Column>
                            <Column field="modelo" header="Modelo" sortable style="width: 0%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span class="flex justify-center">{{ data.modelo }}</span>
                                </template>
                            </Column>
                            <Column field="stock" header="Stock" sortable style="width: 0%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span class="flex justify-center whitespace-nowrap">{{ parseInt(data.stock) + parseInt(data.total_entradas) + parseInt(data.total_devolucion) - parseInt(data.total_salidas) }}    {{ data.unidad_medida }}</span>
                                </template>
                            </Column>
                            <Column field="fecha" header="Fecha" sortable style="width: 0%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span class="flex justify-center whitespace-nowrap">{{ formatDate(data.entrada_maxima == null ? data.fecha : data.entrada_maxima) }}</span>
                                </template>
                            </Column>
                            <Column field="precio" header="Precio" sortable style="width: 0%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span class="flex justify-center whitespace-nowrap">S/. {{ parseFloat(data.precio).toFixed(2) }}</span>
                                </template>
                            </Column>
                            <Column field="acciones" header="Acciones" style="width: 5%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="flex gap-x-4 justify-center">
                                        <Button size="small" icon="pi pi-cart-plus" @click="fn_redirigirCarritoCompras(data)" class="p-button-warn dark:text-gray-50" />
                                        <Button size="small" icon="pi pi-pencil" v-if="$page.props.user.permissions.includes('Acciones Productos')" @click="fn_editarProductos(data)" class="p-button-warning dark:text-gray-50" />
                                        <Button size="small" icon="pi pi-trash" v-if="$page.props.user.permissions.includes('Acciones Productos')" @click="deleteProducto(data.producto_id, data.insumo)" class="p-button-danger dark:text-gray-50" />
                                    </div>
                                </template>
                            </Column>
                            <template #empty> No se encontraron productos.</template>
                        </DataTable>
                    </div>
                </div>
            </div>
            <div class="card flex justify-center">
                <Dialog v-model:visible="showModal" modal header="Imprimir el registro de salidas" :style="{ width: '30rem' }">
                    <span class="text-surface-500 dark:text-surface-400 block mb-6">Debe seleccionar las fechas a imprimir los registros.</span>
                    <div class="flex flex-col gap-y-2.5">
                        <div class="flex flex-col gap-y-2.5">
                            <div class="flex items-center flex-row gap-2.5">
                                <div class="flex flex-col w-full justify-between items-start gap-y-2">
                                    <label for="name" class="font-semibold w-full">Fecha Desde :</label>
                                    <DatePicker v-model="form.fechaHoy" showIcon fluid :showOnFocus="false" dateFormat="dd/mm/yy"/>
                                </div>
                                <div class="flex flex-col w-full justify-between items-start gap-y-2">
                                    <label for="name" class="font-semibold w-full">Fecha Hasta :</label>
                                    <DatePicker v-model="form.fechaHasta" showIcon fluid :showOnFocus="false" dateFormat="dd/mm/yy"/>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-y-2.5">
                            <div class="flex justify-end gap-2">
                                <Button type="button" label="Imprimir Salidas" @click="fn_imprimirSalidas"></Button>
                            </div>
                        </div>
                    </div>
                </Dialog>
            </div>
        </div>
    </AppLayout>
</template>

<script>
export default {
    methods: {
        // Constante para manejar la selección automática del producto
        guardarProductoId(producto_id, name) {
            localStorage.setItem('producto_id', producto_id);
            localStorage.setItem('nombre_categoria', name);
        },
        redirectToSalidas(productoId) {
            // Redirigir a la página de salidas con el mismo enlace que el botón de visualización de salidas
            this.$inertia.visit(route('salidas.listarSalidasRelacionadas', { producto_id: productoId }));
        }
    }
};
</script>

<style scoped>
:deep(.p-datatable-header){
    padding: 16px 0 !important;
}
:deep(.p-datatable-sort-icon){
    color: white !important;
}
:deep(.p-datatable-tbody > tr > td:nth-child(7)) {
    text-align: center !important;
}
:deep(.p-datepicker-panel){
    z-index: 10004 !important;
}

:deep(.p-datatable-header-cell:nth-child(11) .p-datatable-column-header-content) {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}
</style>