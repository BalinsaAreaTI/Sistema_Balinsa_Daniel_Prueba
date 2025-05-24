<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import { ref, onMounted, watch } from 'vue';
import { ConsularDtProductosSalidas } from '@/service/ConsularDtProductosSalidas';
import { DatosUsuariosSalidas } from '@/service/DatosUsuariosSalidas';
import Select from 'primevue/select';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import axios from "axios";
import Textarea from 'primevue/textarea';
import DatePicker from 'primevue/datepicker';
import TextInput from '@/Components/TextInput.vue';
import { show_alerta } from '@/utils/alertasSwal';
import InputNumber from 'primevue/inputnumber';

const props = defineProps({
    productos: {
        type: Object,
        required: true,
    },
    salidas: {
        type: Object,
        required: true,
    },
    tecnico_salidas:{
        type: Object,
        required: true,
    }
});

const productosSalidas = ref([]);
const filteredProductos = ref([]);
const searchTerm = ref('');
const selectedIndex = ref(-1);
const selectUsuarios = ref(parseInt(props.salidas.tecnico));
const usuarios = ref([]);

const form = useForm({
    empresa: props.salidas.empresa,
    unidad_salida: props.salidas.unidad_salida,
    comentario_salida: props.salidas.comentario_salida,
    tecnico: props.salidas.tecnico,
    fecha: props.salidas.fecha,
    hora_salida: props.salidas.hora_salida,
    producto_id: props.salidas.producto_id,
    devolucion: props.salidas.devolucion,
    unidad_devolucion: props.salidas.unidad_devolucion
})

function onInput() {
    ConsularDtProductosSalidas.obtenerDatosConsultarProductosSalidas()
        .then((response) => {
            productosSalidas.value = response;
            filterProductos();
        })
        .catch(error => {
            console.error('Error al obtener los datos de productos:', error);
        });
}

function filterProductos() {
    const term = searchTerm.value.toLowerCase();
    filteredProductos.value = productosSalidas.value.filter(producto =>
        producto.insumo.toLowerCase().includes(term)
    );
}

function onKeydown(event) {
    if (event.key === 'ArrowDown') {
        event.preventDefault();
        selectedIndex.value = (selectedIndex.value + 1) % filteredProductos.value.length;
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        selectedIndex.value = (selectedIndex.value - 1 + filteredProductos.value.length) % filteredProductos.value.length;
    } else if (event.key === 'Enter') {
        event.preventDefault();
        if (selectedIndex.value >= 0) {
            selectProducto(filteredProductos.value[selectedIndex.value]);
        }
    }
}

function selectProducto(producto) {
    searchTerm.value = producto.insumo;
    form.producto_id = producto.id;
    form.name = producto.insumo;
    filteredProductos.value = [];
    selectedIndex.value = -1;
}

function updateSelection(index) {
    selectedIndex.value = index;
}

watch(selectUsuarios, (newValue) => {
    form.tecnico = newValue;
});

const submit = async () => {
    try {
        form.put(route('salidas.update', {salida: props.salidas.id}));
        show_alerta('La salida se ha registrado correctamente', 'success');
        const productoId = form.producto_id;
        const productoNombre = form.name;
        localStorage.setItem('producto_id', productoId);
        localStorage.setItem('producto_nombre', productoNombre);
        if (productoId) {
            router.get(route('salidas.listarSalidasRelacionadas', { producto_id: productoId }));
        } else {
            console.error('No se encontró producto_id en el formulario');
        }
    } catch (error) {
        console.error('Error al registrar la salida:', error);
    }
};

const submitForm = (event) => {
    if (event) {
        event.preventDefault();
    }

    form.errors = {};

    if (!form.producto_id) {
        form.errors.producto_id = 'Este campo es obligatorio.';
    }
    if (!form.empresa) {
        form.errors.empresa = 'Este campo es obligatorio.';
    }
    if (!form.unidad_salida) {
        form.errors.unidad_salida = 'Este campo es obligatorio.';
    } else if (form.unidad_salida == 0) {
        form.errors.unidad_salida = 'La cantidad no puede ser 0.';
    }

    if (!selectUsuarios.value) {
        form.errors.selectUsuarios = 'Debes seleccionar un usuario.';
    }
    if (!form.fecha) {
        form.errors.fecha = 'La fecha es obligatoria.';
    }
    if (!form.hora_salida) {
        form.errors.hora_salida = 'La hora es obligatoria.';
    }
    if (!form.comentario_salida) {
        form.errors.comentario_salida = 'El comentario es obligatorio.';
    }

    if (Object.keys(form.errors).length > 0) {
        show_alerta('Por favor completa todos los campos obligatorios.', 'error');
        return;
    }

    
    const { producto_id, unidad_salida, unidad_devolucion } = form;
    let id = selectUsuarios.value;
    
    if (unidad_devolucion > unidad_salida ){
        show_alerta('La unidad de devolucion no puede ser mayor a la unidad de salida', 'error');
        return;
    }

    axios.post('/validarstock', { producto_id: producto_id })
        .then(response => {
            if (!response.data || !Array.isArray(response.data) || response.data.length === 0) {
                show_alerta('Debe seleccionar un producto', 'error');
                return;
            }

            const stockData = response.data[0];
            if (typeof stockData.stock === 'undefined' || typeof stockData.total_entradas === 'undefined' ||
                typeof stockData.total_devolucion === 'undefined' || typeof stockData.total_salidas === 'undefined') {
                show_alerta('Error en los datos de stock', 'error');
                return;
            }

            let stock = parseInt(stockData.stock) + parseInt(stockData.total_entradas) + 
                        parseInt(stockData.total_devolucion) - parseInt(stockData.total_salidas);

            if (stock < parseInt(unidad_salida)) {
                show_alerta('El stock de este producto no está disponible', 'error');
                return;
            }

            Swal.fire({
                title: "Ingrese su Contraseña para confirmar su salida",
                input: "password",
                inputAttributes: {
                    autocapitalize: "off"
                },
                showCancelButton: true,
                confirmButtonText: "Confirmar",
                showLoaderOnConfirm: true,
                cancelButtonText: "Cancelar",
                allowOutsideClick: () => !Swal.isLoading(),
                preConfirm: (password) => {
                    return axios.post('/comprobarSalida', { id: id, passwordconfirmacion: password })
                        .then(response => {
                            if (response.data.siexisteusuario) {
                                return true;
                            } else {
                                show_alerta('Contraseña Incorrecta', 'error');
                                return false;
                            }
                        })
                        .catch(error => {
                            console.error('Error al enviar la solicitud:', error);
                            show_alerta('Ups! Error al verificar la contraseña', 'error');
                            return false;
                        });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const dataToSubmit = {
                        empresa: form.empresa,
                        unidad_salida: form.unidad_salida,
                        comentario_salida: form.comentario_salida,
                        tecnico: form.tecnico,
                        fecha: form.fecha,
                        hora_salida: form.hora_salida,
                        producto_id: form.producto_id,
                        devolucion: form.devolucion,
                        unidad_devolucion: form.unidad_devolucion,
                    };

                    submit();
                }
            });
        })
        .catch(error => {
            console.error('Error al validar el stock:', error);
            show_alerta('Ups! Ocurrió un error al validar el stock', 'error');
        });
};

onMounted(() => {
    DatosUsuariosSalidas.obtenerDatosUsuariosSalidas()
        .then((respuesta) => {
            usuarios.value = respuesta.map(user => ({
                idUsuario: user.id,
                nombreUsuario: user.name,
                passwordUsuario: user.password
            }));
        })
        .catch((error) => {
            console.error('Error al obtener los datos', error);
        });

    const productoIdSalida = localStorage.getItem('producto_id');
    const productoNombreSalida = localStorage.getItem('producto_nombre');
    
    if (productoIdSalida && productoNombreSalida) {
        form.producto_id = productoIdSalida;
        form.name = productoNombreSalida;
        searchTerm.value = productoNombreSalida;
    } else {
        console.log("No se han montado los datos en el local storage.");
    }
});

</script>
<template>
    <AppLayout title="Editar Salida">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Editar Salida</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit="submitForm">
                    <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                        <div class="flex flex-col lg:flex-row justify-center items-start md:gap-x-40 gap-0 gap-y-5">
                            <div>
                                <p class="text-gray-900 dark:text-white font-extrabold">Ingresar Nuevo Producto a Almacen</p>
                                <p class="text-gray-900 dark:text-white font-normal">Crear un nuevo producto</p>
                            </div>
                            <div class="md:flex-1 w-full">
                                <div class="class dark:bg-gray-700 bg-gray-100 rounded-t-lg p-5">
                                    <div class="">
                                        <div class="w-full">
                                            <div class="relative">
                                                <label for="producto" class="font-extrabold text-sm">Producto</label>
                                                <InputText :disabled="!$page.props.user.permissions.includes('Acciones Administrador')" 
                                                v-model="searchTerm" 
                                                    @input="onInput" 
                                                    size="normal"
                                                    @keydown="onKeydown" 
                                                    placeholder="Escribe el nombre del producto" 
                                                    class="w-full mt-1 focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                                    autocomplete="off"
                                                />
                                                <div v-show="filteredProductos.length > 0" 
                                                    class="w-full z-50 max-h-40 border border-gray-300 rounded-lg absolute overflow-auto text-sm divide-y divide-gray-200 bg-white">
                                                    <div v-for="(producto, index) in filteredProductos" 
                                                        :key="producto.id" 
                                                        :class="['p-2 cursor-pointer', { 'bg-gray-200': index === selectedIndex }]" 
                                                        @click="selectProducto(producto)"
                                                        @mouseover="updateSelection(index)">
                                                        {{ producto.insumo }}
                                                    </div>
                                                </div>
                                            </div>
                                            <span v-if="form.errors.producto_id" class="text-red-500 text-sm">{{ form.errors.producto_id }}</span>
                                        </div>
                                        <div>
                                            <label for="empresa" class="font-extrabold text-sm">Empresa</label>
                                            <InputText v-model="form.empresa" placeholder="Escribe el nombre de la empresa" class="w-full mt-1 focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md" autocomplete="off"/>
                                            <span v-if="form.errors.empresa" class="text-red-500 text-sm">{{ form.errors.empresa }}</span>
                                        </div>
                                        <div class="card text-sm mt-2 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2">
                                            <div>
                                                <label for="unidad_salida" class="font-extrabold">Unidad de Salida</label>
                                                <InputNumber :disabled="!$page.props.user.permissions.includes('Acciones Administrador')" v-model="form.unidad_salida" inputId="integeronly" fluid />
                                                <span v-if="form.errors.unidad_salida" class="text-red-500 text-sm">{{ form.errors.unidad_salida }}</span>
                                            </div>
                                            <div>
                                                <label for="tecnico" class="font-bold block mb-1">Seleccionar Personal</label>
                                                <Select v-model="selectUsuarios" :options="usuarios" optionValue="idUsuario" optionLabel="nombreUsuario" placeholder="Selecciona Usuario" checkmark :highlightOnSelect="false" class="w-full text-black" />
                                                <span v-if="form.errors.selectUsuarios" class="text-red-500 text-sm">{{ form.errors.selectUsuarios }}</span>
                                            </div>
                                            <div>
                                                <label for="fecha" class="font-bold block mb-2">Fecha de Registro</label>
                                                <DatePicker v-model="form.fecha" showIcon fluid :showOnFocus="false" dateFormat="dd/mm/yy"/>
                                                <span v-if="form.errors.fecha" class="text-red-500 text-sm">{{ form.errors.fecha }}</span>
                                            </div>
                                            <div>
                                                <label for="hora" class="font-bold block mb-2">Hora de Registro</label>
                                                <TextInput id="hora_salida" v-model="form.hora_salida" type="time" autocomplete="hora_salida" class="mt-1 block w-full"/>
                                                <span v-if="form.errors.hora_salida" class="text-red-500 text-sm">{{ form.errors.hora_salida }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="comentario_salida" class="font-bold block text-sm mb-2">Comentario</label>
                                            <Textarea class="w-full" v-model="form.comentario_salida" autoResize rows="5" cols="30" placeholder="Ingrese un comentario de la salida del producto"/>
                                            <span v-if="form.errors.comentario_salida" class="text-red-500 text-sm">{{ form.errors.comentario_salida }}</span>
                                        </div>
                                        <div class="flex flex-row gap-4 items-center mb-2 mt-5 w-full">
                                            <div class="flex justify-between gap-x-20 items-center text-center w-full">
                                                <div class="flex justify-between items-center gap-x-2.5">
                                                    <label for="default-checkbox" class="ms-2 text-sm font-bold text-black dark:text-white whitespace-nowrap">¿Se Espera Devolucion del Producto?</label>
                                                    <div class="flex items-center">
                                                        <input checked id="inline-checked-radio" v-model="form.devolucion" type="radio" value="0" :disabled="!$page.props.user.permissions.includes('Acciones Administrador')" name="inline-radio-group" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-blue-800 focus:ring-2 dark:bg-gray-700 dark:border-white">
                                                        <label for="inline-checked-radio" class="ms-2 text-sm font-bold text-gray-900 dark:text-white">NO</label>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <input id="inline-2-radio" v-model="form.devolucion" type="radio" value="1" :disabled="!$page.props.user.permissions.includes('Acciones Administrador')" name="inline-radio-group" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-white">
                                                        <label for="inline-2-radio" class="ms-2 text-sm font-bold text-gray-900 dark:text-white">SI</label>
                                                    </div>
                                                </div>
                                                <div class="flex w-full" v-if="form.devolucion == 1">
                                                    <InputLabel for="unidad_devolucion" value="Unidad de Devolucion" class="ml-1 mt-1"/>
                                                    <TextInput id="unidad_devolucion" v-model="form.unidad_devolucion" type="text" autocomplete="unidad_devolucion" class="mt-1 block w-full"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end dark:bg-gray-600 bg-gray-200 py-3 pr-4 rounded-b-lg">
                                    <button type="submit" class="px-4 py-2 text-white font-medium tracking-wider text-xs uppercase rounded-lg bg-blue-700 hover:bg-blue-800 focus:outline-none focus:shadow-outline">
                                        Actualizar Salida
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>