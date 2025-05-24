<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import { ref, onMounted, watch } from 'vue';
import { ConsultarCategoriasPro } from '@/service/ConsultarCategoriasPro';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import DatePicker from 'primevue/datepicker';
import { show_alerta } from '@/utils/alertasSwal';
import InputNumber from 'primevue/inputnumber';
import FileInput from '@/Components/FileInput.vue';

const selectCategorias = ref(null);
const categorias = ref([]);

const form = useForm ({
    insumo: '',
    marca: '',
    modelo: '',
    cantidad: 0,
    unidad_medida: '',
    fecha: null,
    comprador: '',
    comentario: '',
    stock: 0,
    ultima_entrada: 0,
    precio: 0,
    category_id: '',
    imagen_producto: '',
});

watch(() => form.cantidad, (newValue) => {
        form.stock = newValue;
        form.ultima_entrada = newValue;
    }
);

const imagePreview1 = ref('');
const onSelectFoto = (e, fieldName) => {
    const files = e.target.files;
    if (files.length) {
        const reader = new FileReader();
        reader.onload = (e) => {
            switch (fieldName) {
                case 'foto':
                    imagePreview1.value = e.target.result;
                    break;
            }
        };
        reader.readAsDataURL(files[0]);
        switch (fieldName) {
            case 'foto':
                form.imagen_producto = files[0];
                break;
        }
    }
}

onMounted(() => {
    ConsultarCategoriasPro.obtenerCategoriasProductos()
        .then((respuesta) => {
            categorias.value = respuesta.map(category => ({
                idCategoria: category.id,
                nombreCategoria: category.name
            }));
        })
        .catch((error) => {
            console.error('Error al obtener los datos', error);
        });

    const categoriaPro = parseInt(localStorage.getItem('category_id'), 10);
    selectCategorias.value = categoriaPro;
    form.category_id = categoriaPro;

    const today = new Date().toLocaleDateString('en-CA', { timeZone: 'America/Lima' }).split('/').reverse().join('-');
    form.fecha = today;
});

const submitForm = (event) => {
    form.post(route('productos.store'), {
        onSuccess: () => {
            show_alerta('El producto se ha registrado correctamente', 'success');
        },
        onError: (errors) => {
            if (errors.response && errors.response.status) {
                show_alerta('Ha ocurrido un error al registrar el producto. Por favor, inténtalo de nuevo.', 'error');
                console.error('Error HTTP:', errors.response.status);
            } else {
                show_alerta('Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.', 'error');
                console.error('Error desconocido:', errors);
            }
        }
    });
};

</script>
<template>
    <AppLayout title="Productos">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Registrar Nuevo Producto</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submitForm">
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
                                            <div>
                                                <label for="tecnico" class="font-bold block mb-1">Seleccionar Categoria</label>
                                                <Select disabled v-model="selectCategorias" :options="categorias" optionValue="idCategoria" optionLabel="nombreCategoria" placeholder="Selecciona Usuario" checkmark :highlightOnSelect="false" class="w-full text-black" />
                                                <span v-if="form.errors.selectCategorias" class="text-red-500 text-sm">{{ form.errors.selectCategorias }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="insumo" class="font-extrabold text-sm">Producto</label>
                                            <InputText v-model="form.insumo" placeholder="Escribe el nombre del producto" class="w-full mt-1 focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md" autocomplete="off"/>
                                            <span v-if="form.errors.insumo" class="text-red-500 text-sm">{{ form.errors.insumo }}</span>
                                        </div>
                                        <div class="card text-sm mt-2 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2">
                                            <div>
                                                <label for="marca" class="font-extrabold">Marca del Producto</label>
                                                <InputText v-model="form.marca" placeholder="Escribe la marca" class="w-full mt-1 focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md" autocomplete="off"/>
                                                <span v-if="form.errors.marca" class="text-red-500 text-sm">{{ form.errors.marca }}</span>
                                            </div>
                                            <div>
                                                <label for="modelo" class="font-extrabold">Modelo del Producto</label>
                                                <InputText v-model="form.modelo" placeholder="Escribe el modelo" class="w-full mt-1 focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md" autocomplete="off"/>
                                                <span v-if="form.errors.modelo" class="text-red-500 text-sm">{{ form.errors.modelo }}</span>
                                            </div>
                                            <div>
                                                <label for="cantidad" class="font-extrabold">Cantidad</label>
                                                <InputNumber v-model="form.cantidad" inputId="integeronly" fluid  />
                                                <span v-if="form.errors.cantidad" class="text-red-500 text-sm">{{ form.errors.cantidad }}</span>
                                            </div>
                                            <div>
                                                <label for="unidad_medida" class="font-bold block">Unidad de Medida</label>
                                                <InputText v-model="form.unidad_medida" placeholder="Escribe el modelo" class="w-full mt-1 focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md" autocomplete="off"/>
                                                <span v-if="form.errors.unidad_medida" class="text-red-500 text-sm">{{ form.errors.unidad_medida }}</span>
                                            </div>
                                            <div class="hidden">
                                                <div>
                                                    <label for="stock" class="font-bold block">Stock</label>
                                                    <InputNumber v-model="form.stock" inputId="integeronly" fluid />
                                                    <span v-if="form.errors.stock" class="text-red-500 text-sm">{{ form.errors.stock }}</span>
                                                </div>
                                                <div>
                                                    <label for="ultima_entrada" class="font-bold block">Cantidad Ultima Entrada</label>
                                                    <InputNumber v-model="form.ultima_entrada" inputId="integeronly" fluid />
                                                    <span v-if="form.errors.ultima_entrada" class="text-red-500 text-sm">{{ form.errors.ultima_entrada }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <label for="fecha" class="font-bold block">Fecha de Registro</label>
                                                <DatePicker v-model="form.fecha" showIcon fluid :showOnFocus="false" dateFormat="dd/mm/yy"/>
                                                <span v-if="form.errors.fecha" class="text-red-500 text-sm">{{ form.errors.fecha }}</span>
                                            </div>
                                            <div>
                                                <label for="comprador" class="font-bold block">Comprador</label>
                                                <InputText v-model="form.comprador" placeholder="Escribe el modelo" class="w-full mt-1 focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md" autocomplete="off"/>
                                                <span v-if="form.errors.comprador" class="text-red-500 text-sm">{{ form.errors.comprador }}</span>
                                            </div>
                                            <div>
                                                <label for="precio" class="font-bold block">Precio Aproximado</label>
                                                <InputNumber v-model="form.precio" inputId="integeronly" fluid />
                                                <span v-if="form.errors.precio" class="text-red-500 text-sm">{{ form.errors.precio }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="comentario" class="font-bold block text-sm mb-2">Comentario</label>
                                            <Textarea class="w-full" v-model="form.comentario" autoResize rows="5" cols="30" placeholder="Ingrese un comentario de la salida del producto"/>
                                            <span v-if="form.errors.comentario" class="text-red-500 text-sm">{{ form.errors.comentario }}</span>
                                        </div>
                                        <div class="flex flex-col justify-center items-center gap-y-1">
                                            <div class="flex justify-center items-center flex-col gap-y-2.5">
                                                <label for="foto" class="font-bold block text-sm mt-2.5 uppercase">Foto del Producto</label>
                                                <FileInput name="foto" @change="(e) => onSelectFoto(e, 'foto')" class="text-sm" />
                                            </div>
                                            <span v-if="form.errors.imagen_producto" class="text-red-500 text-sm">{{ form.errors.imagen_producto }}</span>
                                            <div class="flex flex-wrapjustify-center items-center">
                                                <div class="mt-2 " v-if="form.imagen_producto !== ''">
                                                    <img :src="imagePreview1" alt="Vista previa de la foto"
                                                    class="p-2 w-36 h-36 object-contain text-sm text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-gray-200 dark:bg-gray-600 dark:border-gray-500">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end dark:bg-gray-600 bg-gray-200 py-3 pr-4 rounded-b-lg">
                                    <button type="submit" class="px-4 py-2 text-white font-medium tracking-wider text-xs uppercase rounded-lg bg-blue-700 hover:bg-blue-800 focus:outline-none focus:shadow-outline">
                                        Registrar Producto
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