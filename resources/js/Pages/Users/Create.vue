<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import RadioButton from 'primevue/radiobutton';
import InputText from 'primevue/inputtext';
import { ref, onMounted } from 'vue';
import { ConsultaRolesUsuarios } from '@/service/ConsultaRolesUsuarios';
import InputMask from 'primevue/inputmask';
import Button from 'primevue/button';
import Select from 'primevue/select';
import { show_alerta } from '@/utils/alertasSwal';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    users: {
        type: Object
    }
});

const form = useForm({
    name: '',
    apellidopat: '',
    apellidomat: '',
    email: '',
    password: '',
    sexo: '',
    celular: '',
    password_confirmation: '',
    roles: []
});

const roles = ref([]);

const selectedRole = ref('');

const addRole = (roleId) => {
    if (form.roles.includes(roleId)) {
        form.roles = form.roles.filter(role => role !== roleId);
    } else {
        form.roles.push(roleId);
    }
};

const sexos = ref([
    { name: 'Masculino', value: 'Masculino' },
    { name: 'Femenino', value: 'Femenino' }
]);

const submitForm = () => {
    form.errors.password_confirmation = '';
    if (form.password !== form.password_confirmation) {
        form.errors.password_confirmation = 'Las contraseñas no coinciden.';
        return;
    }
    form.post('/fn_registrarDatosdeUsuario', {
        onError: (errors) => {
            show_alerta('Error al registrar el usuario','error')
            console.error('Errores:', errors);
        },
        onSuccess: () => {
            show_alerta('Usuario creado exitosamente','success')
        }
    });
};

const btnCancelar = () => {
    router.get(route('users.index'));
};

onMounted(() => {
    ConsultaRolesUsuarios.obtenerDatosdeUsuarios()
        .then(data => {
            roles.value = data.map(role => ({
                name: role.name,
                key: role.id
            }));
        })
        .catch(error => console.error('Error al cargar roles:', error));
});

</script>
<template>
    <AppLayout title="Usuarios">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Registrar Nuevo Usuario</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submitForm">
                    <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                        <div class="card text-sm mt-2 grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-2">
                            <div>
                                <label for="nombre" class="font-bold block mb-2">Nombre</label>
                                <InputText class="w-full" v-model="form.name" type="text" placeholder="Ingrese Nombre" />
                                <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
                            </div>
                            <div>
                                <label for="apellidopat" class="font-bold block mb-2">Apellido Paterno</label>
                                <InputText class="w-full" v-model="form.apellidopat" type="text" placeholder="Ingrese Apellido Paterno" />
                                <span v-if="form.errors.apellidopat" class="text-red-500 text-sm">{{ form.errors.apellidopat }}</span>
                            </div>
                            <div>
                                <label for="apellidomat" class="font-bold block mb-2">Apellido Materno</label>
                                <InputText class="w-full" v-model="form.apellidomat" type="text" placeholder="Ingrese Apellido Materno" />
                                <span v-if="form.errors.apellidomat" class="text-red-500 text-sm">{{ form.errors.apellidomat }}</span>
                            </div>
                            <div>
                                <label for="email" class="font-bold block mb-2">Email</label>
                                <InputText class="w-full" v-model="form.email" type="email" placeholder="example@balinsa.net" />
                                <span v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</span>
                            </div>
                            <div>
                                <label for="sexo" class="font-bold block mb-2">Sexo</label>
                                <Select 
                                    v-model="form.sexo" 
                                    :options="sexos" 
                                    optionLabel="name" 
                                    optionValue="value" 
                                    placeholder="Seleccionar Sexo" 
                                    class="w-full h-[42px]" 
                                />
                                <span v-if="form.errors.sexo" class="text-red-500 text-sm">{{ form.errors.sexo }}</span>
                            </div>
                            <div>
                                <label for="celular" class="font-bold block mb-2">Celular</label>
                                <InputMask class="w-full" id="basic" v-model="form.celular" mask="999-999-999" placeholder="999-999-999" />
                                <span v-if="form.errors.celular" class="text-red-500 text-sm">{{ form.errors.celular }}</span>
                            </div>
                            <div>
                                <label for="password" class="font-bold block mb-2">Contraseña</label>
                                <InputText class="w-full" v-model="form.password" type="password" placeholder="Ingrese Contraseña" />
                                <span v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password }}</span>
                            </div>
                            <div>
                                <label for="password_confirmation" class="font-bold block mb-2">Confirmar Contraseña</label>
                                <InputText class="w-full" v-model="form.password_confirmation" type="password" placeholder="Ingrese Nuevamente" />
                                <span v-if="form.errors.password_confirmation" class="text-red-500 text-sm">{{ form.errors.password_confirmation }}</span>
                            </div>

                        </div>
                        <div class="flex flex-col justify-center mt-4 gap-y-2.5">
                            <h1 class="font-bold">Asignar Roles</h1>
                            <div class="flex flex-col w-full gap-y-4 md:flex-row justify-between items-start md:items-end">
                                <div class="flex flex-col gap-1.5 px-4 w-full">
                                    <div v-for="rol in roles" :key="rol.key" class="flex items-center gap-x-4">
                                        <RadioButton 
                                            v-model="selectedRole" 
                                            :inputId="rol.key.toString()" 
                                            :value="rol.key" 
                                            @change="addRole(rol.key)"
                                        />
                                        <label :for="rol.key">{{ rol.name }}</label>
                                    </div>
                                    <span v-if="form.errors.roles" class="text-red-500 text-sm">{{ form.errors.roles }}</span>
                                </div>
                                <div class="flex flex-col md:flex-row w-full justify-end items-end gap-x-4 gap-y-4">
                                    <Button @click="btnCancelar" class="md:w-48 w-full" label="Cancelar" size="medium" severity="danger" />
                                    <Button class="md:w-48 w-full" type="submit" label="Guardar" icon="bi bi-person-plus" size="medium" severity="warn" />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>