<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import { show_alerta, show_confirmacion } from '@/utils/alertasSwal';
import { ConsultarClientes } from '@/service/ConsultarClientes';
import { DatosCiudades } from '@/service/DatosCiudades';
import Dialog from 'primevue/dialog';
import { FilterMatchMode } from '@primevue/core/api';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import Tag from 'primevue/tag';
import Select from 'primevue/select';

const clienteRespuesta = ref();
const filtroClientesCiudad = ref([]);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    asesor: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const loading = ref(true);
const visible = ref(false);
const selectedData = ref({});

function openDialog(data) {
    selectedData.value = data;
    visible.value = true;
}

const props = defineProps({
    tbprovincias: {
        type: Object,
        required: true
    }
});

const form = useForm({
    id: ''
})

const deleteCliente = (id, razonSocial) => {
    show_confirmacion(`¿Estás seguro de eliminar al cliente: ${razonSocial}, ¡Este cliente se eliminará definitivamente de la base de datos. Esta acción no se puede deshacer!`)
    .then((result) => {
        if (result.isConfirmed) {
                    form.delete(route('clientes.destroy', id), {
                        onSuccess: () => {
                            show_alerta('Cliente eliminado exitosamente de la base de datos.', 'success')
                            ConsultarClientes.obtenerDatosConsultarClientes().then((respuesta) => {
                                clienteRespuesta.value = getCustomers(respuesta);
                                filtroClientesCiudad.value = clienteRespuesta.value;
                                loading.value = false;
                            }).catch((error) => {
                                console.error('Error al obtener los datos', error);
                            });
                        }
                    });
                }
            });
        }

const openCtgModal = async (cliente) => {
    const { value: ctg } = await Swal.fire({
        title: `CALIFICACIÓN DEL CLIENTE: ${cliente.razonSocial}`,
        input: 'select',
        inputOptions: {
            'Vip': 'Vip',
            'Potencial': 'Potencial',
            'Regular': 'Regular',
            'Sin Informacion': 'Sin Informacion',
        },
        customClass: {
            title: 'text-[17px] font-bold tracking-widest ',
            input: 'text-sm tracking-widest ',
            confirmButton: 'bg-red-500 hover:bg-red-600 tracking-widest ',
        },
        inputPlaceholder: 'Selecciona una opción ⏬',
        showCancelButton: true,
        confirmButtonText: 'Asignar',
        showLoaderOnConfirm: true,
        inputValidator: (value) => {
            if (!value) {
                return 'Debes seleccionar un tipo de calificación';
            }
        },
    });

    if (ctg) {
        try {
            form.post(route('clientes.updateCtg', cliente.id));
            const response = await axios.post(route('clientes.updateCtg'), {
                cliente_id: cliente.id,
                ctg: ctg
            });
            if (response && response.status === 200) {
                show_alerta('Calificación asignada exitosamente.', 'success');
                ConsultarClientes.obtenerDatosConsultarClientes().then((respuesta) => {
                    clienteRespuesta.value = getCustomers(respuesta);
                    filtroClientesCiudad.value = clienteRespuesta.value;
                    loading.value = false;
                }).catch((error) => {
                    console.error('Error al obtener los datos', error);
                });
            } else {
                throw new Error(response ? response.data.message : 'Error desconocido');
            }
        } catch (error) {
            console.error('Error al actualizar la calificación:', error);
            show_alerta('Hubo un problema al actualizar la calificación del cliente.', 'error')
        }
    }
};

const printPDF = async () => {
    const filteredClientes = filtroClientesCiudad.value.filter(cliente => {
        const globalValue = filters.value.global.value?.toLowerCase() || '';
        const asesorValue = filters.value.asesor.value?.toLowerCase() || '';
        const matchesGlobal = globalValue ? 
            cliente.razonSocial.toLowerCase().includes(globalValue) || 
            cliente.numeroDocumento.includes(globalValue) : 
            true;
        const matchesAsesor = asesorValue ? 
            cliente.asesor?.toLowerCase().includes(asesorValue) : 
            true;

        return matchesGlobal && matchesAsesor;
    });

    const doc = new jsPDF({
        orientation: 'l',
        unit: 'mm',
        format: 'a4',
        compress: true,
    });

    const headerText = 'LISTA DE CLIENTES:';
    const tableHeaders = ['N°', 'RUC', 'RAZÓN SOCIAL', 'DIRECCIÓN', 'CIUDAD', 'ASESOR'];
    const tableData = filteredClientes.map((cliente, i) => [
        i + 1,
        cliente.numeroDocumento || '',
        cliente.razonSocial || '',
        cliente.direccion || '',
        cliente.prov_nombre ? cliente.prov_nombre : 'Sin ciudad',
        cliente.asesor || '',
    ]);

    var eje_y = 10;

    doc.setFontSize(14);
    doc.setFont('courier', 'bold');
    doc.text(headerText, 15, eje_y += 7);

    doc.autoTable({
        head: [tableHeaders],
        body: tableData,
        startY: eje_y += 4,
        theme: 'grid',
        styles: {
            fontSize: 8,
            cellPadding: 2,
            valign: 'middle',
            halign: 'center',
            textColor: '#ffffff',
        },
        headStyles: {
            fillColor: '#1423BC',
            textColor: '#ffffff',
        },
        bodyStyles: {
            fillColor: '#d5d7d8',
            textColor: '#000000',
        },
        columnStyles: {
            2: { fontStyle: 'bold' },
        },
    });

    const blob = doc.output('blob');
    const url = URL.createObjectURL(blob);
    window.open(url);
};

const getCustomers = (data) => {
    return [...(data || [])].map((d) => {
        return d;
    });
};

const getSeverity = (status) => {
    switch (status) {
        case 'Sin Informacion':
            return 'danger';

        case 'Vip':
            return 'info';

        case 'Potencial':
            return 'success';

        case 'Regular':
            return 'warn';
    }
}

const selectCiudad = ref(0);
const asesorBuscado = ref('');
const ciudades = ref([]);

watch([selectCiudad, asesorBuscado], ([newCiudadValue, newAsesorValue]) => {
    filtroClientesCiudad.value = clienteRespuesta.value.filter(customer => {
        const matchesCiudad = newCiudadValue === 0 || customer.tbprovincia_id === newCiudadValue;
        const matchesAsesor = newAsesorValue === "" || (customer.asesor && customer.asesor.toLowerCase().includes(newAsesorValue.toLowerCase()));
        if (newAsesorValue === "") {
            return matchesCiudad;
        }
        return matchesCiudad && matchesAsesor;
    });
});

onMounted(() => {
    ConsultarClientes.obtenerDatosConsultarClientes().then((respuesta) => {
        clienteRespuesta.value = getCustomers(respuesta);
        filtroClientesCiudad.value = clienteRespuesta.value;
        loading.value = false;
    }).catch((error) => {
        console.error('Error al obtener los datos', error);
    });

    DatosCiudades.obtenerDatosCiudades().then((respuesta) => {
        ciudades.value = [{ prov_nombre: "TODOS", id: 0 }, ...respuesta];
    }).catch((error) => {
        console.error('Error al obtener los datos', error);
    });
});
</script>

<template>
    <AppLayout title="Clientes">
        <template #header>
            <div class="flex justify-between w-full pr sm:pr-14 ">
                <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">
                    Lista de Clientes
                </h1>
            </div>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 shadow-2xl rounded-lg dark:bg-gray-800">
                    <div class="flex flex-wrap gap-2 justify-between">
                        <Link v-if="$page.props.user.permissions.includes('Registrar Cliente')" :href="route('clientes.create')" class="text-white bg-indigo-700 hover:bg-indigo-800 transform hover:translate-y-[-2px] py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                            <i class="bi bi-person-plus mx-1"></i> Registrar Cliente
                        </Link>
                    </div>
                    <DataTable id="tablaClientes" v-model:filters="filters" stateStorage="session" stateKey="dt-state-clientes" :value="filtroClientesCiudad" paginator paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                        currentPageReportTemplate="{first} a {last} de {totalRecords}" :rows="10" :rowsPerPageOptions="[10, 20, 50]" :loading="loading" removableSort class="text-sm">
                            <template #header>
                                <div class="flex justify-between items-start gap-4 flex-wrap">
                                    <IconField class="w-full md:w-56">
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText v-model="filters['global'].value" placeholder="Buscar" class="text-sm w-full lg:w-56"/>
                                    </IconField>
                                    <div class="flex flex-wrap gap-2 md:w-auto w-full md:mt-0 -mt-1.5">
                                        <Select v-model="selectCiudad" :options="ciudades" optionValue="id" optionLabel="prov_nombre" placeholder="Selecciona ciudad" checkmark :highlightOnSelect="false" class="w-full md:w-56 text-black" />
                                        <IconField class="w-full md:w-56">
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText v-model="asesorBuscado" placeholder="Buscar Asesor" class="text-sm w-full md:w-56"/>
                                        </IconField>
                                        <button v-if="$page.props.user.permissions.includes('Acciones Clientes')" @click="printPDF"
                                            class="text-white md:w-56 bg-indigo-700 hover:bg-indigo-800 mt-0.5 py-2 px-4 rounded lg:w-min w-full whitespace-nowrap text-center">
                                            <i class="fas fa-print mx-2"></i> Imprimir
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <template #empty> No se encontraron clientes.</template>
                            <template #loading>
                                <!-- Simulación de Loading -->
                                <div class="absolute top-0 left-0 w-full h-full text-gray-800 bg-white z-50 flex justify-center items-center">
                                    <div class="flex flex-col justify-center items-center">
                                        <p class="text-sm font-medium">Cargando datos de clientes... Por favor espere.</p>
                                        <div class="flex">
                                            <div class="loader"></div>
                                            <div class="loader"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- ==================== -->
                            </template>
                            <!-- <Column header="Codigo" field="codigo" sortable headerStyle="background-color: #0060ff; color: white;">
                                <template #body="{ data }">
                                    <div @dblclick="fn_extraerDatosEditarPersonal(data)" class="cursor-pointer">
                                        {{ data.codigo }}
                                    </div>
                                </template>
                            </Column> -->
                            <Column header="Id" field="id" sortable>
                                <template #body="{ data }">
                                    <div class="contenidoTD" @dblclick="redirectToClient(data.id); guardarClienteId(data.id, data.razonSocial)">
                                        {{ data.id }}
                                    </div>
                                </template>
                            </Column>
                            <Column header="RUC" field="numeroDocumento" sortable>
                                <template #body="{ data }">
                                    <div class="contenidoTD" @dblclick="redirectToClient(data.id); guardarClienteId(data.id, data.razonSocial)">
                                        {{ data.numeroDocumento }}
                                    </div>
                                </template>
                            </Column>
                            <Column header="RAZON SOCIAL" field="razonSocial" sortable>
                                <template #body="{ data }">
                                    <div class="contenidoTD" @dblclick="redirectToClient(data.id); guardarClienteId(data.id, data.razonSocial)">
                                        {{ data.razonSocial }}
                                    </div>
                                </template>
                            </Column>
                            <Column header="DIRECCION" field="direccion" sortable>
                                <template #body="{ data }">
                                    <div @dblclick="redirectToClient(data.id); guardarClienteId(data.id, data.razonSocial)" class="contenidoTD" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" :title="data.direccion">
                                        <span>{{ data.direccion }}</span>
                                    </div>
                                </template>
                            </Column>
                            <Column header="CIUDAD" field="prov_nombre" sortable>
                                <template #body="{ data }">
                                    <div class="contenidoTD uppercase" @dblclick="redirectToClient(data.id); guardarClienteId(data.id, data.razonSocial)">
                                        {{ data.prov_nombre }}
                                    </div>
                                </template>
                            </Column>
                            <Column header="ASESOR" field="asesor" sortable>
                                <template #body="{ data }">
                                    <div class="contenidoTD uppercase" @dblclick="redirectToClient(data.id); guardarClienteId(data.id, data.razonSocial)">
                                        {{ data.asesor }}
                                    </div>
                                </template>
                            </Column>
                            <Column header="CTG" field="ctg" sortable>
                                <template #body="{ data }">
                                    <div class="contenidoTD" @dblclick="redirectToClient(data.id); guardarClienteId(data.id, data.razonSocial)" @contextmenu.prevent="openCtgModal(data)">
                                        <Tag :value="data.ctg" :severity="getSeverity(data.ctg)" />
                                    </div>
                                </template>
                            </Column>
                            <Column header="FACTURACION">
                                <template #body="{ data }">
                                    <div class="contenidoTD" @dblclick="redirectToClient(data.id); guardarClienteId(data.id, data.razonSocial)">
                                        {{ data.fechafactura ? data.fechafactura : 'Esperando fecha' }} || <br> {{ data.codigofactura ? data.codigofactura : 'Esperando Codigo' }}
                                    </div>
                                </template>
                            </Column>
                            <Column header="ACCIONES">
                                <template #body="{ data }">
                                    <div class="flex gap-2 justify-center items-center px-4">
                                        <button @click="openDialog(data)" class="transform hover:translate-y-[-2px] inline-flex items-center justify-center bg-green-600 hover:bg-green-700 px-1 py-0.5 rounded-md">
                                            <svg class="text-white text-2xl" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0-4 0" />
                                                    <path d="M21 12q-3.6 6-9 6t-9-6q3.6-6 9-6t9 6" />
                                                </g>
                                            </svg>
                                        </button>
                                        <Link v-if="$page.props.user.permissions.includes('Editar Cliente')" :href="route('clientes.edit', data.id)" class="transform hover:translate-y-[-2px] inline-flex items-center justify-center bg-amber-400 hover:bg-amber-500 px-1.5 py-0.5 rounded-md  ">
                                            <i class='bx bxs-edit text-base text-white'></i>
                                        </Link>
                                        <button v-if="$page.props.user.permissions.includes('Eliminar Cliente')" @click="$event => deleteCliente(data.id, data.razonSocial)" class="transform hover:translate-y-[-2px] inline-flex items-center justify-center bg-red-600 hover:bg-red-700 px-1.5 py-0.5 rounded-md">
                                            <i class='bx bxs-trash text-base text-white'></i>
                                        </button>
                                    </div>
                                </template>
                            </Column>
                        </DataTable>
                        <div class="card flex justify-center">
                            <Dialog class="dialogClientes" v-model:visible="visible" modal header="Detalles del Cliente" :style="{ width: '40rem'}" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
                                <div class="flex flex-col gap-y-2.5 p-2 pt-4">
                                    <div>
                                        <strong>RUC: </strong> {{ selectedData.numeroDocumento }} <br>
                                    </div>
                                    <div>
                                        <strong>Razón Social: </strong> {{ selectedData.razonSocial }} <br>
                                    </div>
                                    <div>
                                        <strong>Dirección Legal: </strong> {{ selectedData.direccion }} <br>
                                    </div>
                                    <div>
                                        <strong>Dirección Anexa: </strong> {{ selectedData.cli_direccion2 }} <br>
                                    </div>
                                    <div>
                                        <strong>Comentario: </strong> {{ selectedData.cli_observacion }} <br>
                                    </div>
                                    <div>
                                        <strong>Asesor: </strong> {{ selectedData.asesor }} <br>
                                    </div>
                                </div>
                            </Dialog>
                        </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
export default {
    methods: {
        redirectToClient(cliente_id) {
            // Redirigir a la página de detalles usando el mismo enlace que el botón de visualización
            // window.location.href = this.route('datos.index', { cliente_id: cliente_id });
            this.$inertia.visit(route('datos.index', { cliente_id: cliente_id }));
        },
        guardarClienteId(cliente_id, razonSocial) {
            // Guardar el cliente_id y razonSocial en localStorage
            localStorage.setItem('cliente_id', cliente_id);
            localStorage.setItem('razonSocial', razonSocial);
        },
    }
};
</script>

<style>
#tablaClientes .p-datatable-table{
    background-color: #16a34a !important;
}
#tablaClientes .p-datatable-header{
    padding: 16px 0 !important;
}
#tablaClientes .p-datatable-column-title{
    white-space: nowrap !important;
}
#tablaClientes .p-datatable-sort-icon{
    color: white !important;
}

#tablaClientes .p-datatable-thead > tr > th:nth-child(9){
    display: flex !important;
    justify-content: center;
    border-bottom: 1px solid #16a34a;
}

#tablaClientes .p-datatable-thead > tr > th{
    font-size: 12px !important;
    background-color: #16a34a !important;
    color: white !important;
}

.dark #tablaClientes .p-datatable-tbody > tr{
    background-color: #1f2937 !important;
    color: white !important;
    border-left: 2px solid #4b5563 !important;
    border-right: 2px solid #4b5563 !important;
}

#tablaClientes .p-datatable-tbody > tr{
    background-color: #fff !important;
    color: 1f2937 !important;
}

#tablaClientes .p-datatable-tbody > tr > td{
    font-size: 12px !important;
    white-space: nowrap !important;
    padding: 0px !important;
}

#tablaClientes .p-datatable-tbody > tr > td > .contenidoTD{
    padding: 12px !important;
    cursor: pointer !important;
}

/* Truncar el contenido de las columnas */

#tablaClientes .p-datatable-tbody > tr > td:nth-child(3) .contenidoTD{
    max-width: 300px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

#tablaClientes .p-datatable-tbody > tr > td:nth-child(4) .contenidoTD{
    max-width: 300px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.dark .dialogClientes .p-dialog-content {
    background-color: #1e293b !important;
    color: #ffffff !important;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
}

.dark .dialogClientes .p-dialog-header {
    background-color: #1e293b !important;
    color: #ffffff !important;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    border-bottom: 1px solid #ffffff;
}

.dialogClientes .p-dialog-header {
    height: 55px;
    border-bottom: 1px solid #919090;
}

</style>