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
    user: {
        type: Object,
        required: true
    },
    roles: {
        type: Array,
        required: true
    },
    userRoles: {
        type: Array,
        required: true
    }
});

const form = useForm({
    name: props.user.name,
    apellidopat: props.user.apellidopat,
    apellidomat: props.user.apellidomat,
    email: props.user.email,
    password: '',
    sexo: props.user.sexo,
    celular: props.user.celular,
    password_confirmation: '',
    roles: props.userRoles
});

const roles = ref([]);
const selectedRole = ref(form.roles.length > 0 ? form.roles[0] : '');

const handleRoleChange = (roleId) => {
    const selectedRoleName = roles.value.find(role => role.key === roleId)?.name;
    form.roles = selectedRoleName ? [selectedRoleName] : [];
    selectedRole.value = selectedRoleName;
};

const sexos = ref([
    { name: 'Masculino', value: 'Masculino' },
    { name: 'Femenino', value: 'Femenino' }
]);

const btnCancelar = () => {
    router.get(route('users.index'));
};

const actualizarUsuario = () => {
    form.put(route('users.update', { id: props.user.id }), {
        onSuccess: () => {
            show_alerta('Usuario actualizado exitosamente','success')
            console.log('Actualización exitosa');
        },
        onError: (errors) => {
            show_alerta('Ocurrio un error al actualizar el usuario, intente nuevamente','error')
            console.error('Error al actualizar:', errors);
        },
    });
}

onMounted(() => {
    ConsultaRolesUsuarios.obtenerDatosdeUsuarios()
        .then(data => {
            roles.value = data.map(role => ({
                name: role.name,
                key: role.id
            }));

            if (props.userRoles.length > 0) {
                const roleKeys = roles.value.map(role => role.name);
                form.roles = props.userRoles.filter(role => roleKeys.includes(role));
                selectedRole.value = form.roles[0] || '';
            }
        })
        .catch(error => console.error('Error al cargar roles:', error));
});
</script>
<template>
    <AppLayout title="Usuarios">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Actualizar Usuario</h1>
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
                                            :value="rol.name"
                                            @change="handleRoleChange(rol.key)" 
                                        />
                                        <label :for="rol.key">{{ rol.name }}</label>
                                    </div>
                                    <span v-if="form.errors.roles" class="text-red-500 text-sm">{{ form.errors.roles }}</span>
                                </div>
                                <div class="flex flex-col md:flex-row w-full justify-end items-end gap-x-4 gap-y-4">
                                    <Button @click="btnCancelar" class="md:w-48 w-full" label="Cancelar" size="medium" severity="danger" />
                                    <Button @click="actualizarUsuario" class="md:w-48 w-full" type="submit" label="Actualizar" icon="bi bi-person-plus" size="medium" severity="warn" />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>