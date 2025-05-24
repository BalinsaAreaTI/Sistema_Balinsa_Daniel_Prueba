<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import ButtonResponsive from '@/Components/ButtonResponsive.vue';
import { ConsultaObtenerDatosparaPendientes } from '@/service/ConsultaObtenerDatosparaPendientes';
import InputError from '@/Components/InputError.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { show_alerta, show_confirmacion} from '@/utils/alertasSwal';
import InputText from 'primevue/inputtext';

const props = defineProps({
    tb_pendientes: {
        type: Object,
        required: true,
    }
});
const form = useForm({
    cliente_id: '',
    cantidad: '',
    descripcion: '',
    fecha: '',
    hora: '',
    lugar: '',
    contacto: '',
    nro_contacto: ''
});

const isEditing = ref(false);
const pendientes = ref(null);

const fechaSeleccionada = ref('');
const diaSemana = ref('');

const obtenerNombreDiaSemana = (fecha) => {
    const dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const fechaObj = new Date(fecha + 'T00:00:00-05:00');
    return dias[fechaObj.getDay()];
};

const filtrarPorFecha = () => {
    router.get(route('cuadropendientes.index'), { fecha: fechaSeleccionada.value });
};

const setCurrentDate = () => {
    const today = new Date().toLocaleDateString('en-CA', { timeZone: 'America/Lima' }).split('/').reverse().join('-');
    form.fecha = today;
};

const setCurrentTime = () => {
    const now = new Date();
    const hour = String(now.getHours()).padStart(2, '0');
    const minute = String(now.getMinutes()).padStart(2, '0');
    form.hora = `${hour}:${minute}`;
}

const formatTime = (timeString) => {
    const time = new Date("2000-01-01T" + timeString);
    return time.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
};

const setFechaSeleccionada = () => {
    const queryParams = new URLSearchParams(window.location.search);
    const fechaEnUrl = queryParams.get('fecha');
    
    if (fechaEnUrl) {
        fechaSeleccionada.value = fechaEnUrl;
    } else {
        const today = new Date().toLocaleDateString('en-CA', { timeZone: 'America/Lima' }).split('/').reverse().join('-');
        fechaSeleccionada.value = today;
    }
    actualizarDiaSemana();
};

const actualizarDiaSemana = () => {
    diaSemana.value = obtenerNombreDiaSemana(fechaSeleccionada.value);
};

onMounted(() => {
    setCurrentDate();
    setFechaSeleccionada();
    setCurrentTime();
    actualizarDiaSemana();
});

const editHojaServicio = (pendientes) => {
    form.id = pendientes.id;
    form.razon_social = pendientes.razon_social;
    form.cantidad = pendientes.cantidad;
    form.descripcion = pendientes.descripcion;
    form.fecha = pendientes.fecha;
    form.hora = pendientes.hora;
    form.lugar = pendientes.lugar;
    form.contacto = pendientes.contacto;
    form.nro_contacto = pendientes.nro_contacto;
    isEditing.value = true;
};

const submitForm = () => {
    if (!form.id) {
        form.post(route('cuadropendientes.store'), {
            onSuccess: () => {
                form.reset();
                setCurrentDate();
                setCurrentTime();
                actualizarDiaSemana(); 
                searchTerm.value = '';
                show_alerta('Pendiente creado exitosamente.', 'success');
                form.id = null;
            },
        });
    } else {
        form.put(route('cuadropendientes.update', form.id), {
            onSuccess: () => {
                form.reset();
                setCurrentDate();
                setCurrentTime();
                actualizarDiaSemana(); 
                searchTerm.value = '';
                show_alerta('Pendiente actualizado exitosamente.', 'success');
                form.id = null;
                isEditing.value = false;
            },
        });
    }
};

const form2 = useForm({
    id: ''
})

const deleteHojaServicio = (id, razon_social) => {
    show_confirmacion(`¿Estás seguro de eliminar el pendiente definitivamente: ${razon_social}? ¡Este pendiente se eliminará definitivamente de la base de datos. Esta acción no se puede deshacer!`)
    .then((result) => {
        if (result.isConfirmed) {
            form2.delete(route('cuadropendientes.destroy', id), {
                onSuccess: () => {
                    show_alerta('Pendiente eliminado exitosamente de la base de datos.', 'success');
                }
            });
        }
    });
}


const searchTerm = ref('');
const selectedIndex = ref(-1);
const datosclipendientes = ref([]);

function onInput() {
    ConsultaObtenerDatosparaPendientes.obtenerDatosdeClientesparaPendientes()
        .then((response) => {
            datosclipendientes.value = response;
        })
        .catch(error => {
            console.error('Error al obtener los datos de clientes para pendientes:', error);
        });
}

const filtered = computed(() => {
    const term = searchTerm.value.toLowerCase();
    return datosclipendientes.value.filter(cliente =>
        cliente.razonSocial && cliente.razonSocial.toLowerCase().includes(term)
    );
});

function onKeydown(event) {
    if (event.key === 'ArrowDown') {
        event.preventDefault();
        selectedIndex.value = (selectedIndex.value + 1) % filtered.value.length;
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        selectedIndex.value = (selectedIndex.value - 1 + filtered.value.length) % filtered.value.length;
    } else if (event.key === 'Enter') {
        event.preventDefault();
        if (selectedIndex.value >= 0) {
            selectCliente(filtered.value[selectedIndex.value]);
        }
    }
}

function selectCliente(cliente) {
    searchTerm.value = cliente.razonSocial;
    form.cliente_id = cliente.id;

    datosclipendientes.value = [];
    selectedIndex.value = -1;

    form.lugar = cliente.prov_nombre;
}

function updateSelection(index) {
    selectedIndex.value = index;
}

</script>
<template>
    <AppLayout title="Pendientes Vendedores">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">1.- Cuadro de Pendientes (Vendedor)</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4  sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-100 shadow-lg shadow-gray-600 dark:bg-gray-800  dark:shadow-gray-800 rounded-lg">
                    <form @submit.prevent="submitForm" class="uppercase font-bol">
                        <div class="grid grid-cols-1 md:gap-y-6 gap-y-3 md:grid-cols-3 sm:gap-x-8 mb-3">
                            <div class="w-full">
                                <div class="relative">
                                    <InputLabel for="cliente" value="Cliente" class="block text-md font-medium text-gray-700 " />
                                    <InputText v-model="searchTerm" @input="onInput" id="cliente_id" size="small"
                                        @keydown="onKeydown" placeholder="Escribe el nombre del cliente"
                                        autocomplete="off" required
                                        class="mt-1 h-[37.6px] focus:ring-indigo-500 dark:text-white dark:bg-gray-900 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-blue-600 rounded-l-lg" />
                                    <span v-if="form.errors.cliente_id" class="text-red-500 text-sm">
                                        {{ form.errors.cliente_id }}
                                    </span>
                                    <div v-show="searchTerm && filtered.length > 0"
                                        class="md:w-full w-auto z-50 max-h-40 border border-gray-300 rounded-lg absolute overflow-auto text-sm divide-y divide-gray-200 bg-white">
                                        <div v-for="(cliente, index) in filtered" :key="cliente.id"
                                            :class="['p-2 cursor-pointer dark:bg-gray-700', { 'bg-gray-200 dark:bg-gray-800': index === selectedIndex }]"
                                            @click="selectCliente(cliente)" @mouseover="updateSelection(index)">
                                            {{ cliente.razonSocial }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <InputLabel for="cantidad" value="cantidad"
                                    class="block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.cantidad" type="text" id="cantidad" required
                                    class="dark:bg-gray-900 dark:text-white mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.cantidad" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="contacto" value="contacto"
                                    class="block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.contacto" type="text" id="contacto" required
                                    class="dark:bg-gray-900 dark:text-white mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.contacto" class="mt-2"></InputError>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:gap-y-6 gap-y-3 md:grid-cols-3 md:gap-x-8 gap-x-0 mb-3">
                            <div>
                                <InputLabel for="nro_contacto" value="N° contacto"
                                    class="block text-md font-medium text-gray-700 " />
                                <TextInput v-model="form.nro_contacto" type="text" id="nro_contacto" required
                                    class="dark:bg-gray-900 dark:text-white mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.nro_contacto" class="mt-2"></InputError>
                            </div>
                            <div>
                                <InputLabel for="lugar" value="lugar" class="block text-md font-medium text-gray-700" />
                                <TextInput v-model="form.lugar" type="text" id="lugar" required class="dark:bg-gray-900 dark:text-white mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.lugar" class="mt-2"></InputError>
                            </div>
                            <div class="grid md:grid-cols-2 grid-cols-1 gap-x-4 gap-y-4">
                                <div>
                                    <InputLabel for="fecha" value="fecha"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput v-model="form.fecha" type="date" id="fecha" required
                                        class="dark:bg-gray-900 dark:text-white mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.fecha" class="mt-2"></InputError>
                                </div>
                                <div>
                                    <InputLabel for="hora" value="hora"
                                        class="block text-md font-medium text-gray-700 " />
                                    <TextInput v-model="form.hora" type="time" id="hora" required
                                        class="dark:bg-gray-900 dark:text-white mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    <InputError :message="form.errors.hora" class="mt-2"></InputError>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:gap-y-6 gap-y-3 md:grid-cols-3 mb-3">
                            <div class="col-span-2">
                                <InputLabel for="descripcion" value="descripcion"
                                    class="block text-md font-medium text-gray-700 " />
                                <textarea v-model="form.descripcion" type="text" id="descripcion" required
                                    class="dark:bg-gray-900 dark:text-white mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                <InputError :message="form.errors.descripcion" class="mt-2"></InputError>
                            </div>
                            <div class="flex flex-wrap gap-2 justify-end items-end">
                                <ButtonResponsive v-if="!isEditing" @dblclick="editHojaServicio(pendientes)"
                                    class="uppercase text-xs">Generar Pendiente</ButtonResponsive>
                                <ButtonResponsive v-if="isEditing" class="uppercase text-xs">Actualizar Pendiente
                                </ButtonResponsive>
                            </div>
                        </div>
                    </form>
                    <div class="flex sm:flex-row flex-col justify-between items-center md:py-4 gap-y-2 py-2">
                        <h1 class="md:text-lg font-extrabold text-md dark:text-white">Historial de Cuadro de Pendientes</h1>
                        <div class="flex flex-wrap justify-end items-center gap-x-4">
                            <label class="text-base text-gray-900 dark:text-gray-50 font-extrabold">Filtrar por : {{ diaSemana }}</label>
                            <input type="date" v-model="fechaSeleccionada" @change="filtrarPorFecha" class="outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 px-2.5 py-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" id="fechaHoyHoja">
                        </div>
                    </div>
                    <div class="relative overflow-x-auto scroll-dataTableLEO shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-800">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-900">
                            <thead class="text-xs text-white uppercase bg-green-600 dark:bg-green-600">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N°</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Usuario</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Razon Social</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">cantidad</th>
                                    <th scope="col" class="px-20 py-3 text-center dark:border-white border-b-2">descripcion</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">hora</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">lugar</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">contacto</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">N° Contacto</th>
                                    <th scope="col" class="px-6 py-3 text-center dark:border-white border-b-2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr @dblclick="editHojaServicio(pendientes)" v-for="pendientes in tb_pendientes" :key="pendientes.id" class="bg-white text-black text-xs hover:text-white border-b border-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer">
                                    <td class="px-6 py-4 text-center">{{ pendientes.id }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{pendientes.usuario }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b whitespace-break-spaces">{{pendientes.razon_social }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{pendientes.cantidad}}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b whitespace-break-spaces">{{pendientes.descripcion }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b whitespace-nowrap">{{formatTime(pendientes.hora) }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b uppercase">{{pendientes.lugar }}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{pendientes.contacto}}</td>
                                    <td class="px-6 py-3 text-center dark:border-white border-b">{{pendientes.nro_contacto }}</td>
                                    <td class="px-6 py-3 dark:border-white relative">
                                        <button @click="$event => deleteHojaServicio(pendientes.id, pendientes.razon_social)" class="bg-red-600 hover:bg-red-700 px-1.5 py-0.5 rounded-md" title="Eliminar Cliente">
                                            <i class='bx bxs-trash text-base text-white'></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="tb_pendientes.length === 0" class="text-center py-2 dark:text-white">
                            No se han registrado pendientes.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>