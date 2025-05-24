<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import InputIcon from 'primevue/inputicon';
import IconField from 'primevue/iconfield';
import Button from 'primevue/button';
import { show_alerta, show_confirmacion } from '@/utils/alertasSwal';
import { router } from '@inertiajs/vue3';

import { ref, onMounted } from 'vue';
import { ConsultaDatosUsuarios } from '@/service/ConsultaDatosUsuarios';
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';

const customers = ref();
const selectedCustomer = ref();
const filters = ref(
    {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        apellidopat: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        apellidomat: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        email: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        sexo: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        celular: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        roles: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
);
 
const CrearUsuario = () => {
    router.get(route('users.create'));
};

const CrearRoles = () => {
    router.get(route('roles.index'));
};

const CrearPermisos = () => {
    router.get(route('permisos.index'));
};

const fn_eliminarUser = (id, name) => {
    show_confirmacion('¿Estás seguro?', `Estás seguro de eliminar definitivamente al usuario ${name} de la base de datos`)
    .then((result) => {
        if (result.isConfirmed) {
            router.delete(route('users.destroy', id), {
                onSuccess: () => {
                    show_alerta('El usuario se ha eliminado correctamente.', 'success');
                    ConsultaDatosUsuarios.obtenerDatosdeUsuarios().then((response) => {
                        customers.value = response; // Asegúrate de que el formato de `response` es correcto
                    }).catch(error => {
                        console.error('Error al obtener los datos del cliente:', error);
                    });
                },
                onError: (errors) => {
                    show_alerta('Hubo un error al intentar eliminar el usuario.', 'error');
                    console.error('Error al eliminar el usuario:', errors);
                }
            });
        }
    });
};

const fn_editarUser = (id) => {
    router.get(route('users.edit', id));
}

onMounted(() => {
    ConsultaDatosUsuarios.obtenerDatosdeUsuarios().then((response) => {
        customers.value = response; // Asegúrate de que el formato de `response` es correcto
    }).catch(error => {
        console.error('Error al obtener los datos del cliente:', error);
    });
});

</script>
<template>
    <AppLayout title="Usuarios">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Usuarios</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="card text-sm -mt-4">
                        <DataTable removableSort v-model:filters="filters" v-model:selection="selectedCustomer" :value="customers"
                                stateStorage="session" stateKey="dt-state-users" paginator :rows="5" filterDisplay="menu"
                                selectionMode="single" dataKey="id" :globalFilterFields="['name', 'email', 'celular', 'apellidopat', 'apellidomat']" tableStyle="min-width: 50rem"
                                :rowsPerPageOptions="[5, 10, 20, 50, 100]" paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                                currentPageReportTemplate="{first} de {last} de {totalRecords}">
                            <template #header>
                                <div class="flex justify-between md:flex-row flex-col gap-y-3">
                                    <Button @click="CrearUsuario" v-if="$page.props.user.permissions.includes('Acciones Usuarios')" label="Registrar Usuario" icon="bi bi-person-plus" size="medium" severity="info" />
                                    <Button @click="CrearRoles" v-if="$page.props.user.permissions.includes('Acciones Usuarios')" label="Crear Roles" icon="bi bi-person-plus" size="medium" severity="info" />
                                    <Button @click="CrearPermisos" v-if="$page.props.user.permissions.includes('Acciones Usuarios')" label="Crear Permisos" icon="bi bi-person-plus" size="medium" severity="info" />
                                    <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText class="md:w-80 w-full" v-model="filters['global'].value" placeholder="Buscar usuarios" />
                                    </IconField>
                                </div>
                            </template>
                            <Column class="hidden" field="id" header="#" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.id }}</span>
                                </template>
                            </Column>
                            <Column field="name" header="Nombres" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div @dblclick="fn_editarUser(data.id)" class="cursor-pointer whitespace-nowrap">
                                        {{ data.name }} {{ data.apellidopat }} {{ data.apellidomat }}
                                    </div>
                                </template>
                            </Column>
                            <Column field="email" header="Email" sortable style="width: 15%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.email }}</span>
                                </template>
                            </Column>
                            <Column field="sexo" header="Sexo" sortable style="width: 10%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.sexo }}</span>
                                </template>
                            </Column>
                            <Column field="celular" header="Telefono" sortable style="width: 10%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.celular }}</span>
                                </template>
                            </Column>
                            <Column header="Roles" style="width: 10%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span v-if="data.roles && data.roles.length" class=" whitespace-nowrap">
                                        <span v-for="role in data.roles" :key="role.id">
                                            {{ role.name }}<span v-if="data.roles.length > 1">, </span>
                                        </span>
                                    </span>
                                    <span v-else>No roles</span>
                                </template>
                            </Column>
                            <Column v-if="$page.props.user.permissions.includes('Acciones Usuarios')" field="acciones" header="Acciones" style="width: 15%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="flex gap-x-4 justify-center">
                                        <Button size="small" icon="pi pi-pencil" @click="fn_editarUser(data.id)" class="p-button-warning" />
                                        <Button size="small" icon="pi pi-trash" @click="fn_eliminarUser(data.id, data.name)" class="p-button-danger" />
                                    </div>
                                </template>
                            </Column>
                            <template #empty> No se encontraron usuarios.</template>
                        </DataTable>
                    </div>
                </div>
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

:deep(.p-datatable-header-cell:nth-child(7) .p-datatable-column-header-content) {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

:deep(.p-datatable-tbody > tr > td:nth-child(7)) {
    text-align: center !important;
}
</style>