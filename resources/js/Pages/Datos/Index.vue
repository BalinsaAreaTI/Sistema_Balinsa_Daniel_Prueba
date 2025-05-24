<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import { onMounted, ref } from 'vue';
import { show_alerta, show_confirmacion } from '@/utils/alertasSwal';
import { ConsultaDatosRelacionadosClientes } from '@/service/ConsultaDatosRelacionadosClientes';
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const visible = ref(false);

const selectedDatosCli = ref();
const filters = ref(
    {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        idActa: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        razon_social_acta: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        tipo_conformidad: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        ruc_acta: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        direccion_acta: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        ruc_acta: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        trabajo_conformidad: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
);

const initialvalues = {
    nombre: '',
    cargo: '',
    telefono: '',
    correo: '',
    tarjeta: '',
    cliente_id: '',
};

const showEditModal = ref(false);

const form = useForm(initialvalues)

const editForm = useForm({
    id: '',
    nombre: '',
    cargo: '',
    telefono: '',
    correo: '',
});

const fn_editarDatosdeClientes = (data) => {
    editForm.id = data.id;
    editForm.nombre = data.nombre;
    editForm.cargo = data.cargo;
    editForm.telefono = data.telefono;
    editForm.correo = data.correo;
    showEditModal.value = true;
};

const submitEditFormrDatosdeClientes = () => {
    editForm.put(route('datos.update', editForm.id), {
        data: {
            nombre: editForm.nombre,
            cargo: editForm.cargo,
            telefono: editForm.telefono,
            correo: editForm.correo,
        },
        onSuccess: () => {
            show_alerta('Los datos se han actualizado correctamente.', 'success');
            showEditModal.value = false;

            const cliente_id = localStorage.getItem('cliente_id');
            if (cliente_id) {
                ConsultaDatosRelacionadosClientes.obtenerDatosClientesRelacionados(cliente_id)
                    .then((response) => {
                        listaDatosRel.value = response;
                    })
                    .catch(error => {
                        console.error('Error al obtener los datos relacionados del cliente:', error);
                    });
            }
        },
        onError: (errors) => {
            show_alerta('Ha ocurrido un error al actualizar los datos. Por favor, inténtalo de nuevo.', 'error');
            console.error('Error al actualizar los datos:', errors);
        }
    });
};


const closeModal = () => {
    visible.value = false; 
    form.nombre = '';
    form.cargo = '';
    form.telefono = '';
    form.correo = '';
    form.tarjeta = '';
}

const save = () => {
    form.post(route('datos.store'), {
        onSuccess: () => {
            closeModal();
            show_alerta('El cliente se ha registrado correctamente', 'success');
            const cliente_id = localStorage.getItem('cliente_id');
            if (cliente_id) {
                ConsultaDatosRelacionadosClientes.obtenerDatosClientesRelacionados(cliente_id)
                    .then((response) => {
                        listaDatosRel.value = response;
                    })
                    .catch(error => {
                        console.error('Error al obtener los datos de salidas únicas:', error);
                    });
            }
        }
    });
};

const deleteDatos = (id, nombre) => {
    show_confirmacion(`¿Estás seguro de eliminar los datos de: ${nombre}? Esto se eliminará definitivamente de la base de datos.`)
        .then((result) => {
            if (result.isConfirmed) {
                form.delete(route('datos.destroy', id), {
                    onSuccess: () => {
                        show_alerta('Cliente eliminado exitosamente.', 'success');
                        const cliente_id = localStorage.getItem('cliente_id');
                        if (cliente_id) {
                            ConsultaDatosRelacionadosClientes.obtenerDatosClientesRelacionados(cliente_id)
                                .then((response) => {
                                    listaDatosRel.value = response;
                                })
                                .catch(error => {
                                    console.error('Error al obtener los datos de salidas únicas:', error);
                                });
                        }
                    }
                });
            }
        });
};

const razonSocial = ref('');

const listaDatosRel = ref([]);

onMounted(() => {
    const cliente_id = localStorage.getItem('cliente_id');
    if (cliente_id) {
        ConsultaDatosRelacionadosClientes.obtenerDatosClientesRelacionados(cliente_id)
            .then((response) => {
                listaDatosRel.value = response;
            })
            .catch(error => {
                console.error('Error al obtener los datos de salidas únicas:', error);
            });
    }
});

onMounted(() => {
    const cliente_id = localStorage.getItem('cliente_id');
    const storedRazonSocial = localStorage.getItem('razonSocial');
    if (cliente_id) {
        form.cliente_id = cliente_id;
        form.razon = razonSocial;
    }
    if (storedRazonSocial) {
    razonSocial.value = storedRazonSocial;
  }
});

</script>
<template>
    <AppLayout title="Datos Del Cliente">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista De Datos Del Cliente</h1>
        </template>
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 shadow-2xl rounded-lg dark:bg-gray-800">
                    <DataTable class="text-[13px]" removableSort v-model:filters="filters" v-model:selection="selectedDatosCli" :value="listaDatosRel"
                            stateStorage="session" stateKey="dt-state-dt-datos" paginator :rows="5" filterDisplay="menu"
                            selectionMode="single" dataKey="id" :globalFilterFields="['idActa','razon_social_acta','tipo_conformidad','ruc_acta','direccion_acta','ruc_acta','trabajo_conformidad']" tableStyle="min-width: 50rem"
                            :rowsPerPageOptions="[5, 10, 20, 50, 100]" paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                            currentPageReportTemplate="{first} de {last} de {totalRecords}">
                            <template #header>
                            <div class="flex justify-between flex-col md:flex-row gap-y-4">
                                <div class="flex justify-between gap-x-8 flex-col md:flex-row gap-y-2 text-base">
                                    <ButtonResponsive v-if="$page.props.user.permissions.includes('Registrar Datos Clientes')" @click="visible = true" class="font-normal">
                                        <i class="fa-solid fa-address-card mx-2 font-bold"></i>Registrar Datos
                                    </ButtonResponsive>
                                </div>
                                <Link :href="route('clientes.index')" class="text-white text-base flex flex-row justify-center items-center gap-x-2.5 bg-indigo-700 hover:bg-indigo-800 transform hover:translate-y-[-2px] py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                    <i class="fas fa-arrow-left mx-2"></i> Regresar
                                </Link>
                                <!-- <div>
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText class="md:w-80 w-full" v-model="filters['global'].value" placeholder="Buscar datos" />
                                    </IconField>
                                </div> -->
                            </div>
                            <div class="md:py-4 md:mt-0 mt-4 flex justify-center items-center">
                                <div class="font-semibold text-center md:text-base dark:text-white">CLIENTE || {{ razonSocial }} </div>
                            </div>
                        </template>
                        <Column class="hidden" field="id" header="#" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                            <template #body="{ data }">
                                <span>{{ data.id }}</span>
                            </template>
                        </Column>
                        <Column field="idActa" header="Nombre" sortable style="width: 10%" headerStyle="background-color: #16A34A; color:white;">
                            <template #body="{ data }">
                                <span class="whitespace-nowrap uppercase">{{ data.nombre ? data.nombre : 's/n' }}</span>
                            </template>
                        </Column>
                        <Column field="cargo" header="Cargo" sortable style="width: 16%" headerStyle="background-color: #16A34A; color:white;">
                            <template #body="{ data }">
                                <span class=" whitespace-nowrap uppercase">
                                    {{ data.cargo ? data.cargo : 's/n' }}
                                </span>
                            </template>
                        </Column>
                        <Column field="telefono" header="Telefono" sortable style="width: 10%" headerStyle="background-color: #16A34A; color:white;">
                            <template #body="{ data }">
                                <span class="uppercase">
                                    {{ data.telefono ? data.telefono : 's/n' }}
                                </span>
                            </template>
                        </Column>
                        <Column field="correo" header="Correo" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                            <template #body="{ data }">
                                <span :class="{'uppercase': !data.correo}" class="whitespace-break-spaces">
                                    {{ data.correo ? data.correo : 's/n' }}
                                </span>
                            </template>
                        </Column>
                        <Column v-if="$page.props.user.permissions.includes('Acciones Datos Clientes')" field="acciones" header="Acciones" style="width: 5%" headerStyle="background-color: #16A34A; color:white;">
                            <template #body="{ data }">
                                <div class="flex gap-x-4 justify-center">
                                    <button v-if="$page.props.user.permissions.includes('Editar Datos Clientes')" @click="fn_editarDatosdeClientes(data)" 
                                        class="inline-flex items-center justify-center bg-amber-400 hover:bg-amber-500 px-1.5 py-0.5 rounded-md mr-2">
                                        <i class='bx bxs-edit text-base text-white'></i>
                                    </button>
                                    <button v-if="$page.props.user.permissions.includes('Eliminar Datos Clientes')" @click="deleteDatos(data.id, data.nombre)"
                                        class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 px-1.5 py-0.5 rounded-md">
                                        <i class='bx bxs-trash text-base text-white'></i>
                                    </button>
                                </div>
                            </template>
                        </Column>
                        <template #empty> No se encontraron datos del cliente.</template>
                    </DataTable>
                </div>
            </div>
        </div>
    </AppLayout>
    <div class="card flex justify-center h-full">
        <Dialog v-model:visible="visible" modal header="Registrar Datos" :style="{ width: '30rem', height: '25rem' }">
            <div class="flex flex-col h-full gap-y-3">
                <div class="flex flex-col gap-y-8">
                    <div class="flex items-center flex-col">
                        <div class="flex flex-col gap-y-2.5">
                            <div class="flex items-center flex-col gap-y-2.5">
                                <div class="w-full mt-1 hidden">
                                    <label for="nombre" class="font-semibold w-full">Cliente :</label>
                                    <InputText disabled autofocus v-model="form.cliente_id" inputId="integeronly" fluid />
                                </div>
                                <div class="w-full mt-1">
                                    <label for="nombre" class="font-semibold w-full">Cliente :</label>
                                    <InputText disabled autofocus v-model="form.razon" inputId="integeronly" fluid />
                                </div>
                                <div class="flex flex-row w-full justify-between items-start gap-x-4">
                                    <div>
                                        <label for="nombre" class="font-semibold w-full">Nombre :</label>
                                        <InputText autofocus v-model="form.nombre" inputId="integeronly" fluid />
                                    </div>
                                    <div>
                                        <label for="cargo" class="font-semibold w-full">Cargo :</label>
                                        <InputText v-model="form.cargo" inputId="integeronly" fluid />
                                    </div>
                                </div>
                                <div class="flex flex-row w-full justify-between items-start gap-x-4">
                                    <div>
                                        <label for="telefono" class="font-semibold w-full">Telefono :</label>
                                        <InputText v-model="form.telefono" inputId="integeronly" fluid />
                                    </div>
                                    <div>
                                        <label for="correo" class="font-semibold w-full">Correo :</label>
                                        <InputText v-model="form.correo" inputId="integeronly" fluid />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-4">
                        <Button :disabled="form.processing" @click="save" type="button" label="Registrar" severity="info"></Button>
                    </div>
                </div>
            </div>
        </Dialog>
        <div class="card flex justify-center h-full">
            <Dialog v-model:visible="showEditModal" modal header="Editar Datos" :style="{ width: '30rem', height: '25rem' }">
                <div class="flex flex-col h-full gap-y-3">
                    <div class="flex flex-col gap-y-8">
                        <div class="flex items-center flex-col">
                            <div class="flex flex-col gap-y-2.5">
                                <div class="flex items-center flex-col gap-y-2.5">
                                    <div class="w-full mt-1">
                                        <label for="nombre" class="font-semibold w-full">Cliente :</label>
                                        <InputText :value="razonSocial" disabled inputId="integeronly" fluid />
                                    </div>
                                    <div class="flex flex-row w-full justify-between items-start gap-x-4">
                                        <div>
                                            <label for="nombre" class="font-semibold w-full">Nombre :</label>
                                            <InputText v-model="editForm.nombre" inputId="integeronly" fluid />
                                        </div>
                                        <div>
                                            <label for="cargo" class="font-semibold w-full">Cargo :</label>
                                            <InputText v-model="editForm.cargo" inputId="integeronly" fluid />
                                        </div>
                                    </div>
                                    <div class="flex flex-row w-full justify-between items-start gap-x-4">
                                        <div>
                                            <label for="telefono" class="font-semibold w-full">Telefono :</label>
                                            <InputText v-model="editForm.telefono" inputId="integeronly" fluid />
                                        </div>
                                        <div>
                                            <label for="correo" class="font-semibold w-full">Correo :</label>
                                            <InputText v-model="editForm.correo" inputId="integeronly" fluid />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-4">
                            <Button type="button" label="Actualizar" @click="submitEditFormrDatosdeClientes"></Button>
                        </div>
                    </div>
                </div>
            </Dialog>
        </div>
    </div>
</template>

<style scoped>
:deep(.p-datatable-header){
    padding: 16px 0 !important;
}
:deep(.p-datatable-sort-icon){
    color: white !important;
}
:deep(.p-datatable-tbody > tr > td:nth-child(0)) {
    text-align: center !important;
}
:deep(.p-datepicker-panel){
    z-index: 10004 !important;
}
</style>