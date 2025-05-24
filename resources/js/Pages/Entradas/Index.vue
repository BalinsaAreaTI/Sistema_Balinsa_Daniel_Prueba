<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import InputIcon from 'primevue/inputicon';
import IconField from 'primevue/iconfield';
import InputNumber from 'primevue/inputnumber';
import Button from 'primevue/button';
import DatePicker from 'primevue/datepicker';
import { show_alerta, show_confirmacion } from '@/utils/alertasSwal';
import { useForm } from '@inertiajs/vue3';
import Dialog from 'primevue/dialog';
import { ref, onMounted, watch } from 'vue';
import { ConsultaObtenerEntradas } from '@/service/ConsultaObtenerEntradas';
import { ConsularDtProductos } from '@/service/ConsularDtProductos';
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';
import { formatDate } from '@/utils/funcionesglobales';

const fechaHoy = new Date ();
const salidas = ref([]);
const selectedCustomer = ref();
const filters = ref(
    {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        id: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        producto_id: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        insumo: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        cantidad: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
);

const deleteSalidas = (id, insumo) => {
    show_confirmacion('¿Estás seguro?', `¿Estás seguro de eliminar definitivamente la salida de ${insumo} de la base de datos?`)
        .then((result) => {
            if (result.isConfirmed) {
                axios.delete(route('entradas.destroy', id))
                    .then((response) => {
                        if (response.data.success) {
                            show_alerta(response.data.message, 'success');
                            ConsultaObtenerEntradas.obtenerEntradas()
                                .then((response) => {
                                    salidas.value = response;
                                })
                                .catch(error => {
                                    console.error('Error al obtener los datos de entradas:', error);
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

const form = useForm ({
    producto_id : '',
    cantidad: 0,
    fecha: fechaHoy
})

const showModal = ref(false);

const productoscs = ref([]);
const filteredProductos = ref([]);
const searchTerm = ref('');
const selectedIndex = ref(-1);

function onInput() {
    ConsularDtProductos.obtenerDatosConsultarProductos()
        .then((response) => {
            productoscs.value = response;
            if (searchTerm.value.length > 0) {
                filteredProductos.value = productoscs.value.filter(producto =>
                    producto.insumo.toLowerCase().includes(searchTerm.value.toLowerCase())
                );
                selectedIndex.value = -1;
            } else {
                filteredProductos.value = [];
            }
        })
        .catch(error => {
            console.error('Error al obtener los datos de productos:', error);
        });
}

function onKeydown(event) {
    if (event.key === 'ArrowDown') {
        selectedIndex.value = (selectedIndex.value + 1) % filteredProductos.value.length;
    } else if (event.key === 'ArrowUp') {
        selectedIndex.value = (selectedIndex.value - 1 + filteredProductos.value.length) % filteredProductos.value.length;
    } else if (event.key === 'Enter') {
        if (selectedIndex.value >= 0) {
            if (showModal.value) {
                selectProductoCrear(filteredProductos.value[selectedIndex.value]);
            } else if (showEditModal.value) {
                selectProductoEditar(filteredProductos.value[selectedIndex.value]);
            }
        }
    }
}

function selectProductoCrear(producto) {
    searchTerm.value = producto.insumo;
    form.producto_id = producto.id;
    filteredProductos.value = [];
}

function selectProductoEditar(producto) {
    searchTerm.value = producto.insumo;
    editForm.producto_id = producto.id;
    filteredProductos.value = [];
}

function updateSelection(index) {
    selectedIndex.value = index;
}

const resetForm = () => {
    form.producto_id = '';
    form.cantidad = 0;
    form.fecha = fechaHoy;
    searchTerm.value = '';
    filteredProductos.value = [];
    selectedIndex.value = -1;
};

const submitForm = () => {
    form.post(route('entradas.store'), {
        onSuccess: () => {
            show_alerta('La entrada se ha registrado correctamente.', 'success');
            resetForm();
            showModal.value = false;
            ConsultaObtenerEntradas.obtenerEntradas()
                .then((response) => {
                    salidas.value = response;
                })
                .catch(error => {
                    console.error('Error al obtener los datos de salidas únicas:', error);
                });
        },
        onError: (errors) => {
            show_alerta('Ha ocurrido un error al registrar la entrada. Por favor, inténtalo de nuevo.', 'error');
            console.error('Error al registr la entrada:', errors);
        }
    });
};

const showEditModal = ref(false);

const editForm = useForm({
    producto_id: '',
    cantidad: 0,
    fecha: fechaHoy
});

const fn_editarEntradadeProducto = (data) => {
    editForm.producto_id = data.producto_id;
    editForm.cantidad = Number(data.cantidad);
    editForm.fecha = data.fecha;
    editForm.id = data.id;
    searchTerm.value = data.insumo;
    showEditModal.value = true;
};

const resetEditForm = () => {
    editForm.producto_id = '';
    editForm.cantidad = 0;
    editForm.fecha = fechaHoy;
};

watch(showEditModal, (newValue) => {
    if (!newValue) {
        resetEditForm();
    }
});

const submitEditForm = () => {
    editForm.put(route('entradas.update', editForm.id), {
        data: {
            producto_id: editForm.producto_id,
            cantidad: editForm.cantidad,
            fecha: editForm.fecha
        },
        onSuccess: () => {
            show_alerta('La entrada se ha actualizado correctamente.', 'success');
            resetEditForm();
            showEditModal.value = false;
            ConsultaObtenerEntradas.obtenerEntradas()
                .then((response) => {
                    salidas.value = response;
                })
                .catch(error => {
                    console.error('Error al obtener los datos de salidas únicas:', error);
                });
        },
        onError: (errors) => {
            show_alerta('Ha ocurrido un error al actualizar la entrada. Por favor, inténtalo de nuevo.', 'error');
            console.error('Error al actualizar la entrada:', errors);
        }
    });
};

onMounted(() => {
    ConsultaObtenerEntradas.obtenerEntradas()
        .then((response) => {
            salidas.value = response;
        })
        .catch(error => {
            console.error('Error al obtener los datos de salidas únicas:', error);
        });
});

</script>
<template>
    <AppLayout title="Listar Entradas">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Listar Entradas</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="card text-sm -mt-4">
                        <DataTable removableSort v-model:filters="filters" v-model:selection="selectedCustomer" :value="salidas"
                                stateStorage="session" stateKey="dt-state-demo-session" paginator :rows="5" filterDisplay="menu"
                                selectionMode="single" dataKey="id" :globalFilterFields="['id','producto_id','insumo','cantidad']" tableStyle="min-width: 50rem"
                                :rowsPerPageOptions="[5, 10, 20, 50, 100]" paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                                currentPageReportTemplate="{first} de {last} de {totalRecords}">
                            <template #header>
                                <div class="flex justify-between flex-col lg:flex-row gap-y-4">
                                    <div class="flex justify-between gap-x-8 flex-col md:flex-row gap-y-2">
                                        <Link v-if="$page.props.user.permissions.includes('Ingresar Entrada')" @click="showModal = true" class="text-white cursor-pointer flex justify-center items-center text-base gap-x-2.5 bg-indigo-700 hover:bg-indigo-800 transform hover:translate-y-[-2px] py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                            <i class="fa-solid fa-inbox"></i> Registrar Entrada
                                        </Link>
                                    </div>
                                    <div>
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText class="md:w-80 w-full" v-model="filters['global'].value" placeholder="Buscar entradas" />
                                        </IconField>
                                    </div>
                                </div>
                            </template>
                            <Column class="hidden" field="id" header="#" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.id }}</span>
                                </template>
                            </Column>
                            <Column class="hidden" field="producto_id" header="#" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.producto_id }}</span>
                                </template>
                            </Column>
                            <Column field="insumo" header="Producto" sortable style="width: 100%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span class=" whitespace-break-spaces">{{ data.insumo }}</span>
                                </template>
                            </Column>
                            <Column field="cantidas" header="Cantidad" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.cantidad }}</span>
                                </template>
                            </Column>
                            <Column class="whitespace-nowrap" field="fecha" header="Fecha" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span class="flex justify-center">{{ formatDate(data.fecha) }}</span>
                                </template>
                            </Column>
                            <Column v-if="$page.props.user.permissions.includes('Acciones Entradas')" field="acciones" header="Acciones" style="width: 5%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="flex gap-x-4 justify-center">
                                        <Button size="small" icon="pi pi-pencil" @click="fn_editarEntradadeProducto(data)" class="p-button-warning dark:text-gray-50" />
                                        <Button size="small" icon="pi pi-trash" @click="deleteSalidas(data.id, data.insumo)" class="p-button-danger dark:text-gray-50" />
                                    </div>
                                </template>
                            </Column>
                            <template #empty> No se encontraron datos de entradas de los productos.</template>
                        </DataTable>
                    </div>
                </div>
            </div>
            <div class="card flex justify-center h-full">
                <Dialog v-model:visible="showModal" modal header="Registrar una nueva entrada" :style="{ width: '30rem', height: '20rem' }">
                    <div class="flex flex-col h-full gap-y-3">
                        <div class="flex flex-col gap-y-2.5">
                            <div class="flex items-center flex-col gap-2.5">
                                <div class="flex flex-col w-full justify-between items-start gap-y-2">
                                    <div class="w-full">
                                        <div class="relative">
                                            <InputText 
                                                v-model="searchTerm" 
                                                @input="onInput" 
                                                @keydown="onKeydown" 
                                                placeholder="Escribe el nombre del producto" 
                                                class="w-full mt-1 focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                                autocomplete="off"
                                            />
                                            <div v-show="filteredProductos.length > 0" 
                                                class="w-full z-50 max-h-40 border border-gray-300 rounded-lg absolute overflow-auto text-sm divide-y divide-gray-200 bg-white">
                                                <div v-for="(producto, index) in filteredProductos" 
                                                    :key="producto.id" 
                                                    :class="['p-2 cursor-pointer dark:bg-gray-700', { 'bg-gray-200 dark:bg-gray-800': index === selectedIndex }]" 
                                                    @click="selectProductoCrear(producto)" 
                                                    @mouseover="updateSelection(index)">
                                                    {{ producto.insumo }}
                                                </div>
                                            </div>  
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-start w-full">
                                    <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-y-8">
                            <div class="flex items-center flex-col">
                                <div class="flex flex-col gap-y-2.5">
                                    <div class="flex items-center flex-row">
                                        <div class="flex flex-row w-full justify-between items-start gap-x-4">
                                            <div>
                                                <label for="descripcion" class="font-semibold w-full">Cantidad :</label>
                                                <InputNumber v-model="form.cantidad" inputId="integeronly" fluid />
                                            </div>
                                            <div class="">
                                                <label for="name" class="font-semibold w-full">Fecha :</label>
                                                <DatePicker v-model="form.fecha" showIcon fluid :showOnFocus="false" dateFormat="dd/mm/yy"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end gap-4">
                                <Button type="button" label="Registrar Entrada" @click="submitForm"></Button>
                            </div>
                        </div>
                    </div>
                </Dialog>
            </div>
            <div class="card flex justify-center h-full">
                <Dialog v-model:visible="showEditModal" modal header="Editar Entrada" :style="{ width: '30rem', height: '20rem' }">
                    <div class="flex flex-col h-full gap-y-3">
                        <div class="flex flex-col gap-y-2.5">
                            <div class="flex items-center flex-col gap-2.5">
                                <div class="flex flex-col w-full justify-between items-start gap-y-2">
                                    <div class="w-full">
                                        <div class="relative">
                                            <InputText 
                                                v-model="searchTerm" 
                                                @input="onInput" 
                                                @keydown="onKeydown" 
                                                placeholder="Escribe el nombre del producto" 
                                                class="w-full mt-1 focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                                autocomplete="off"
                                            />
                                            <div v-show="filteredProductos.length > 0" 
                                                class="w-full z-50 max-h-40 border border-gray-300 rounded-lg absolute overflow-auto text-sm divide-y divide-gray-200 bg-white">
                                                <div v-for="(producto, index) in filteredProductos" 
                                                    :key="producto.id" 
                                                    :class="['p-2 cursor-pointer dark:bg-gray-700', { 'bg-gray-200 dark:bg-gray-800': index === selectedIndex }]" 
                                                    @click="selectProductoEditar(producto)" 
                                                    @mouseover="updateSelection(index)">
                                                    {{ producto.insumo }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-start w-full">
                                    <span v-if="editForm.errors.name" class="text-red-500 text-sm">{{ editForm.errors.name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-y-8">
                            <div class="flex items-center flex-col">
                                <div class="flex flex-col gap-y-2.5">
                                    <div class="flex items-center flex-row">
                                        <div class="flex flex-row w-full justify-between items-start gap-x-4">
                                            <div>
                                                <label for="cantidad" class="font-semibold w-full">Cantidad :</label>
                                                <InputNumber v-model="editForm.cantidad" inputId="integeronly" fluid />
                                            </div>
                                            <div>
                                                <label for="fecha" class="font-semibold w-full">Fecha :</label>
                                                <DatePicker v-model="editForm.fecha" showIcon fluid :showOnFocus="false" dateFormat="dd/mm/yy"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end gap-4">
                                <Button type="button" label="Actualizar" @click="submitEditForm"></Button>
                            </div>
                        </div>
                    </div>
                </Dialog>
            </div>
        </div>
    </AppLayout>
</template>

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
</style>