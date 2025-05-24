<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import { ref, onMounted } from 'vue';
import { ConsultaAsignarPermisos } from '@/service/ConsultaAsignarPermisos';
import Button from 'primevue/button';
import { show_alerta } from '@/utils/alertasSwal';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    role: Object,
    permisos: Object,
    rolePermisos: Array,
});

const permisos = ref({});

const form = useForm({
    permisos: props.rolePermisos
});

const submitForm = async () => {
    try {
        await form.put(route('roles.update', { id: props.role.id }), {
            permisos: form.permisos,
        });
        show_alerta('Roles asignados exitosamente', 'success');
    } catch (errors) {
        show_alerta('Error al registrar los permisos', 'error');
        console.error('Errores:', errors);
    }
};

const btnCancelar = () => {
    router.get(route('roles.index'));
};

onMounted(() => {
    ConsultaAsignarPermisos.obtenerPermisosdeRoles(props.role.id)
        .then((response) => {
            permisos.value = response.permisos;
        })
        .catch(error => console.error('Error al cargar permisos:', error));
});
</script>

<template>
    <AppLayout title="Asignar Roles">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Asignar Roles</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submitForm">
                    <div class="p-6 py-2 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                        <div class="flex flex-col justify-center mt-4 -gap-y-2.5 md:gap-y-2.5">
                            <h1 class="font-bold">Asignar Permisos al rol de : {{ role.name }}</h1>
                            <div class="w-full text-md p-2 gap-4 grid grid-cols-1 gap-y-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 sm:gap-x-6 mb-3">
                                <div v-for="(permiso, id) in permisos" :key="id" class="flex items-center mt-2 whitespace-nowrap">
                                    <input type="checkbox" :id="'permiso_' + id" :value="id" v-model="form.permisos" class="w-4 h-4 text-blue-600 bg-blue-100 border-blue-900 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label class="ml-4 dark:text-white" :for="'permiso_' + id">{{ permiso }}</label>
                                </div>
                            </div>
                            <InputError :message="form.errors.permisos" class="mt-2"></InputError>
                            <div class="flex justify-end md:flex-row flex-col gap-y-3 gap-x-3 py-4 w-full">
                                <Button type="submit" label="Asignar permisos" icon="pi pi-save" />
                                <Button @click="btnCancelar" label="Cancelar" severity="danger" class="md:w-40 w-full" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>