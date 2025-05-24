<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import InputIcon from 'primevue/inputicon';
import IconField from 'primevue/iconfield';
import Button from 'primevue/button';
import { router, useForm } from '@inertiajs/vue3';
import Dialog from 'primevue/dialog';
import { show_alerta } from '@/utils/alertasSwal';
import { ref, onMounted, watch } from 'vue';
import { ConsultaIndexRoles } from '@/service/ConsultaIndexRoles';
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';

const customers = ref();
const selectedCustomer = ref();
const filters = ref(
    {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        acciones: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
);

const AsignarPermisosUsu = (roleId) => {
    router.get(route('roles.edit', { role: roleId }));
};

const showModal = ref(false);
const nombreRol = ref('');

const form = useForm({
    name: nombreRol.value,
});

watch(nombreRol, (newValue) => {
    form.name = newValue;
});

const submitForm = () => {
    form.post(route('roles.store'), {
        onSuccess: () => {
            show_alerta('El rol se ha registrado correctamente.', 'success');
            showModal.value = false;
            nombreRol.value = '';
            ConsultaIndexRoles.obtenerRolesdeUsuariosIndex().then((response) => {
                customers.value = response; // Actualiza la lista de roles
            }).catch(error => {
                console.error('Error al obtener los datos del cliente:', error);
            });
        },
        onError: (errors) => {
            show_alerta('Ha ocurrido un error al registrar el rol. Por favor, inténtalo de nuevo.', 'error');
            console.error('Error al registrar rol:', errors);
        }
    });
};

onMounted(() => {
    ConsultaIndexRoles.obtenerRolesdeUsuariosIndex().then((response) => {
        customers.value = response; // Asegúrate de que el formato de `response` es correcto
    }).catch(error => {
        console.error('Error al obtener los datos del cliente:', error);
    });
});
</script>
<template>
    <AppLayout title="Roles">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Roles</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="card text-sm -mt-4">
                        <DataTable removableSort v-model:filters="filters" v-model:selection="selectedCustomer" :value="customers"
                                stateStorage="session" stateKey="dt-state-roles" paginator :rows="5" filterDisplay="menu"
                                selectionMode="single" dataKey="id" :globalFilterFields="['name', 'email', 'celular', 'apellidopat', 'apellidomat']" tableStyle="min-width: 50rem"
                                :rowsPerPageOptions="[5, 10, 20, 50, 100]" paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                                currentPageReportTemplate="{first} de {last} de {totalRecords}">
                            <template #header>
                                <div class="flex justify-between md:flex-row flex-col gap-y-3">
                                    <Button @click="showModal = true" label="Crear Roles" icon="bi bi-person-plus" size="medium" severity="info" />
                                    <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText class="md:w-80 w-full" v-model="filters['global'].value" placeholder="Buscar Roles" />
                                    </IconField>
                                </div>
                            </template>
                            <Column class="hidden" field="id" header="#" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.id }}</span>
                                </template>
                            </Column>
                            <Column class="font-extrabold" field="name" header="Nombres" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.name }}</span>
                                </template>
                            </Column>
                            <Column field="acciones" header="Acciones" style="width: 15%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <Button size="small" label="Asignar Permisos" icon="pi pi-key" @click="AsignarPermisosUsu(data.id)" class="p-button-warning" />
                                </template>
                            </Column>
                            <template #empty> No se encontraron roles.</template>
                        </DataTable>
                    </div>
                </div>
            </div>
            <div class="card flex justify-center">
                <Dialog v-model:visible="showModal" modal header="Registrar Rol" :style="{ width: '25rem' }">
                    <span class="text-surface-500 dark:text-surface-400 block mb-8">Ingresa el nombre del Rol.</span>
                    <div class="flex items-center flex-col gap-4 mb-4">
                        <div class="flex flex-row w-full justify-between items-center">
                            <label for="name" class="font-semibold w-24">Nombre :</label>
                            <InputText id="name" class="flex-auto" v-model="nombreRol" autocomplete="off" />
                        </div>
                        <div class="flex justify-center">
                            <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2">
                        <Button type="button" label="Cancel" severity="secondary" @click="showModal = false"></Button>
                        <Button type="button" label="Save" @click="submitForm"></Button>
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
</style>
