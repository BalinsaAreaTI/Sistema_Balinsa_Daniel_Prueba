<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import InputIcon from 'primevue/inputicon';
import IconField from 'primevue/iconfield';
import Button from 'primevue/button';
import { show_alerta, show_confirmacion } from '@/utils/alertasSwal';
import { ref, onMounted } from 'vue';
import { ConusltaListarIndexSalidas } from '@/service/ConusltaListarIndexSalidas';
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';
import { router } from '@inertiajs/vue3';

const customers = ref();
const selectedCustomer = ref();
const filters = ref(
    {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        id: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        insumo: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        empresa: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        unidad_salida: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        comentario_salida: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
);

const nombreProducto = ref('No definido');

const formatDate = (dateString) => {
    const options = { month: 'short', day: '2-digit', year: 'numeric' };
    const offset = new Date().getTimezoneOffset();
    const offsetMilliseconds = offset * 60 * 1000;
    const date = new Date(new Date(dateString).getTime() + offsetMilliseconds);
    return date.toLocaleDateString('es-ES', options);
};

const formatTime = (timeString) => {
    const time = new Date("2000-01-01T" + timeString);
    return time.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
};

const deleteSalidas = (id, empresa) => {
    const productoId = localStorage.getItem('producto_id');
    show_confirmacion('¿Estás seguro?', `¿Estás seguro de eliminar definitivamente la salida de ${empresa} de la base de datos?`)
        .then((result) => {
            if (result.isConfirmed) {
                axios.delete(route('salidas.destroy', id))
                    .then((response) => {
                        if (response.data.success) {
                            show_alerta(response.data.message, 'success');
                            ConusltaListarIndexSalidas.obtenerIndexListadeSalidas(productoId)
                                .then((response) => {
                                    customers.value = response;
                                })
                                .catch(error => {
                                    console.error('Error al obtener los datos del cliente:', error);
                                });
                        } else {
                            show_alerta(response.data.message, 'error');
                        }
                    })
                    .catch((error) => {
                        show_alerta('Hubo un error al intentar eliminar la salida.', 'error');
                        console.error('Error al eliminar la salida:', error);
                    });
            }
        });
};

const RegistrarSalidasCrear = () => {
    router.get(route('salidas.create'));
};

const fn_editarCategoriaSelect = (id) => {
    router.get(route('salidas.edit', { id }));
};

onMounted(() => {
    const productoId = localStorage.getItem('producto_id');
    ConusltaListarIndexSalidas.obtenerIndexListadeSalidas(productoId)
        .then((response) => {
            customers.value = response;
        })
        .catch(error => {
            console.error('Error al obtener los datos del cliente:', error);
        });

    nombreProducto.value = localStorage.getItem('producto_nombre') || 'No definido';
});

</script>
<template>
    <AppLayout title="Salidas">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Salidas</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="card text-sm -mt-4">
                        <DataTable removableSort v-model:filters="filters" v-model:selection="selectedCustomer" :value="customers"
                                stateStorage="session" stateKey="dt-state-salidas-relacionadas" paginator :rows="5" filterDisplay="menu"
                                selectionMode="single" dataKey="id" :globalFilterFields="['id','insumo','empresa','unidad_salida','comentario_salida','name']" tableStyle="min-width: 50rem"
                                :rowsPerPageOptions="[5, 10, 20, 50, 100]" paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                                currentPageReportTemplate="{first} de {last} de {totalRecords}">
                            <template #header>
                                <div class="flex justify-between flex-col lg:flex-row gap-y-4">
                                    <div class="flex justify-start gap-x-8 flex-col md:flex-row gap-y-2">
                                        <Link v-if="$page.props.user.permissions.includes('Ingresar Salida')" @click="RegistrarSalidasCrear" class="text-white cursor-pointer flex justify-center items-center text-base gap-x-2.5 bg-indigo-700 hover:bg-indigo-800 transform hover:translate-y-[-2px] py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                            <i class="fa-solid fa-inbox"></i> Registrar Salida
                                        </Link>
                                    </div>
                                    <div>
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText class="md:w-80 w-full" v-model="filters['global'].value" placeholder="Buscar salidas" />
                                        </IconField>
                                    </div>
                                </div>
                                <div class="md:mt-4 mt-3.5">
                                    <div class="font-extrabold text-center dark:text-white text-base">Producto || {{ nombreProducto }}</div>
                                </div>
                            </template>
                            <Column class="hidden" field="id" header="#" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.id }}</span>
                                </template>
                            </Column>
                            <Column field="empresa" header="Empresa" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.empresa }}</span>
                                </template>
                            </Column>
                            <Column class="whitespace-nowrap" field="unidad_salida" header="Unidad de Salida" sortable style="width: 0%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span class="flex justify-center">{{ data.unidad_salida }}</span>
                                </template>
                            </Column>
                            <Column field="comentario_salida" header="Comentario" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.comentario_salida }}</span>
                                </template>
                            </Column>
                            <Column field="name" header="Tecnico" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.name }}</span>
                                </template>
                            </Column>
                            <Column field="fecha" header="Fecha" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ formatDate(data.fecha) }} a las {{ formatTime(data.hora_salida) }}</span>
                                </template>
                            </Column>
                            <Column class="whitespace-nowrap" field="cantidad_devolucion" header="Cantidad de Devolucion" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="flex justify-center items-center">
                                        <div>
                                            {{ data.devolucion == 0 ? 'Sin Devolucion' : (data.devolucion === 2 ? data.unidad_devolucion : (data.unidad_devolucion === null ? 'Esperando devolución' : data.unidad_devolucion)) }}
                                        </div>
                                        <img v-if="data.devolucion == 1 && data.unidad_devolucion === null" src="/img/advertencia.png" alt="Advertencia" width="60" class="pulsoAdvertencia ml-5 h-6 drop-shadow-[0_0_5px_rgba(255,255,255,0.80)]" />
                                    </div>
                                </template>
                            </Column>
                            <Column field="acciones" header="Acciones" style="width: 5%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="flex gap-x-4 justify-center">
                                        <Button size="small" icon="pi pi-pencil" @click="fn_editarCategoriaSelect(data.id)" class="p-button-warning dark:text-gray-50" />
                                        <Button size="small" icon="pi pi-trash" @click="deleteSalidas(data.id, data.empresa)" class="p-button-danger dark:text-gray-50" />
                                    </div>
                                </template>
                            </Column>
                            <template #empty> No se encontraron datos de salidas de este producto.</template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
.p-datatable-header{
    padding: 16px 0 !important;
}
.p-datatable-sort-icon{
    color: white !important;
}
.p-datatable-tbody > tr > td:nth-child(7) {
    text-align: center !important;
}
.p-datepicker-panel{
    z-index: 10004 !important;
}
</style>