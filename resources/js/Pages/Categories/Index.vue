<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import InputIcon from 'primevue/inputicon';
import IconField from 'primevue/iconfield';
import Button from 'primevue/button';
import { show_alerta, show_confirmacion } from '@/utils/alertasSwal';
import { useForm } from '@inertiajs/vue3';
import Dialog from 'primevue/dialog';
import { ref, onMounted } from 'vue';
import { ConsultaListarCategorias } from '@/service/ConsultaListarCategorias';
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';
import { router } from '@inertiajs/vue3';
import MultiSelect from 'primevue/multiselect'
import * as XLSX from 'xlsx';
import Swal from 'sweetalert2';

const categorias = ref([])
const categoriaSeleccionada = ref([])

const obtenerCategorias = async () => {
    try {
        const response = await axios.get('/fn_obtenerCategorias')
        categorias.value = response.data
    } catch (error) {
        console.error('Error al obtener categorías:', error)
    }
}

const customers = ref();
const selectedCustomer = ref();
const showModal = ref(false);
const showModalProductos = ref(false);
const showModalEditar = ref(false);
const selectedCategoryId = ref(null);

const form = useForm ({
    name: '',
    descripcion: ''
})

const filters = ref(
    {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        descripcion: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
);

const fn_eliminarCategoria = (id, name) => {
    show_confirmacion('¿Estás seguro?', `Estás seguro de eliminar definitivamente a la categoría ${name} de la base de datos`)
        .then((result) => {
            if (result.isConfirmed) {
                axios.delete(route('categories.destroy', id))
                    .then((response) => {
                        show_alerta('La categoría se ha eliminado correctamente.', 'success');
                        ConsultaListarCategorias.obtenerIndexCategorias()
                            .then((response) => {
                                customers.value = response;
                            })
                            .catch(error => {
                                console.error('Error al obtener la lista de categorías:', error);
                            });
                    })
                    .catch((error) => {
                        show_alerta('Hubo un error al intentar eliminar la categoría.', 'error');
                        console.error('Error al eliminar la categoría:', error);
                    });
            }
        });
};

const submitForm = () => {
    form.post(route('categories.store'), {
        onSuccess: () => {
            show_alerta('El rol se ha registrado correctamente.', 'success');
            form.name = '';
            form.descripcion = '';
            showModal.value = false;
            ConsultaListarCategorias.obtenerIndexCategorias().then((response) => {
                customers.value = response;
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

const fn_editarCategoriaSelect = (categoria) => {
    form.name = categoria.name;
    form.descripcion = categoria.descripcion;
    selectedCategoryId.value = categoria.id;
    showModalEditar.value = true;
};

const editarDatosCategoria = () => {
    if (selectedCategoryId.value) {
        form.put(route('categories.update', { category: selectedCategoryId.value }), {
            onSuccess: () => {
                show_alerta('La categoría se ha actualizado correctamente.', 'success');
                form.name = '';
                form.descripcion = '';
                showModalEditar.value = false;
                ConsultaListarCategorias.obtenerIndexCategorias().then((response) => {
                    customers.value = response;
                }).catch(error => {
                    console.error('Error al obtener los datos del cliente:', error);
                });
            },
            onError: (errors) => {
                show_alerta('Ha ocurrido un error al actualizar la categoría. Por favor, inténtalo de nuevo.', 'error');
                console.error('Error al actualizar categoría:', errors);
            }
        });
    }
};

const fn_redirigirCategoriasProducto = (categoryId, name) => {
    localStorage.setItem('category_id', categoryId);
    localStorage.setItem('nombre_categoria', name);
    router.get(route('productos.index', { category_id: categoryId }));
};

const fn_imprimirProductos = async () => {
    const catProductos = await obtenerDatosSalidas(categoriaSeleccionada.value);
    if (catProductos.length === 0) {
        Swal.fire('Atención', 'No hay productos en las categorías seleccionadas.', 'warning');
        return;
    }
    generarExcel(catProductos);
};

const generarExcel = (catProductos) => {
    const wb = XLSX.utils.book_new(); // Crear un nuevo libro de Excel

    // Definir encabezados
    const encabezados = [
        'ID', 'INSUMO', 'MARCA', 'MODELO', 'CANTIDAD',
        'UNIDAD DE MEDIDA', 'FECHA', 'COMPRADOR',
        'COMENTARIO', 'STOCK CALCULADO', 'ULTIMA ENTRADA', 'PRECIO', 'CATEGORÍA'
    ];

    // Convertir los datos a formato de hoja de cálculo con el stock calculado
    const datos = catProductos.map((p) => [
        p.id, p.insumo, p.marca, p.modelo, p.cantidad,
        p.unidad_medida, p.fecha, p.comprador,
        p.comentario,
        (parseInt(p.stock) + parseInt(p.total_entradas) + parseInt(p.total_devolucion) - parseInt(p.total_salidas)) + ' ' + p.unidad_medida, // Stock calculado
        p.ultima_entrada, p.precio,
        p.nombre_categoria
    ]);

    // Agregar encabezados antes de los datos
    datos.unshift(encabezados);

    // Crear la hoja de cálculo
    const ws = XLSX.utils.aoa_to_sheet(datos);

    // Ajustar el ancho de las columnas
    ws['!cols'] = encabezados.map(() => ({ wch: 20 }));

    // Agregar la hoja al libro de Excel
    XLSX.utils.book_append_sheet(wb, ws, "Productos");

    // Generar y descargar el archivo Excel
    const nombreArchivo = `Productos_Categorias_${new Date().toISOString().slice(0, 10)}.xlsx`;
    XLSX.writeFile(wb, nombreArchivo);
};

const obtenerDatosSalidas = async (categoriasSeleccionadas) => {
    try {
        const response = await axios.get('/fn_ObtenerProductosCategorias', {
            params: { categorias: categoriasSeleccionadas } // Enviamos las categorías seleccionadas
        });
        return response.data;
    } catch (error) {
        console.error('Error al obtener las salidas:', error);
        return [];
    }
};

onMounted(() => {
    ConsultaListarCategorias.obtenerIndexCategorias().then((response) => {
        customers.value = response;
    }).catch(error => {
        console.error('Error al obtener los datos del cliente:', error);
    });
    obtenerCategorias()
});
</script>
<template>
    <AppLayout title="Categorias">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Categorias</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="card text-sm -mt-4">
                        <DataTable @row-dblclick="(event) => fn_redirigirCategoriasProducto(event.data.id, event.data.name)" removableSort v-model:filters="filters" v-model:selection="selectedCustomer" :value="customers"
                                stateStorage="session" stateKey="dt-state-categorias-productos" paginator :rows="5" filterDisplay="menu"
                                selectionMode="single" dataKey="id" :globalFilterFields="['name', 'email', 'celular', 'apellidopat', 'apellidomat']" tableStyle="min-width: 50rem"
                                :rowsPerPageOptions="[5, 10, 20, 50, 100]" paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                                currentPageReportTemplate="{first} de {last} de {totalRecords}">
                            <template #header>
                                <div class="flex justify-between md:flex-row flex-col gap-y-3">
                                    <div class="flex flex-col md:flex-row gap-x-6 gap-y-3.5">
                                        <button v-if="$page.props.user.permissions.includes('Crear Categoria')" @click="showModal = true" class="text-white cursor-pointer flex justify-center items-center text-base gap-x-2.5 bg-indigo-700 hover:bg-indigo-800 transform hover:translate-y-[-2px] py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                            <i class="fa fa-layer-group"></i> Registrar Categoria
                                        </button>
                                        <button @click="showModalProductos = true" class="text-white cursor-pointer flex justify-center items-center text-base gap-x-2.5 bg-orange-600 hover:bg-orange-700 transform hover:translate-y-[-2px] py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                            <i class="fa-solid fa-print"></i> Imprimir Productos
                                        </button>
                                    </div>
                                    <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText class="md:w-80 w-full" v-model="filters['global'].value" placeholder="Buscar categorias" />
                                    </IconField>
                                </div>
                            </template>
                            <Column class="hidden" field="id" header="#" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.id }}</span>
                                </template>
                            </Column>
                            <Column field="name" header="Nombres" sortable style="width: 80%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="cursor-pointer flex flex-wrap gap-x-2">
                                        <div class="font-extrabold">
                                            <span class="text-normal">{{ data.name }}</span> <span class="font-normal">({{ data.descripcion }})</span>
                                        </div>
                                    </div>
                                </template>
                            </Column>
                            <Column field="acciones" header="Acciones" v-if="$page.props.user.permissions.includes('Acciones Categorias')" style="width: 15%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="flex gap-x-4 justify-center">
                                        <Button size="small" icon="pi pi-pencil" @click="fn_editarCategoriaSelect(data)" class="p-button-warning dark:text-gray-50" />
                                        <Button size="small" icon="pi pi-trash" @click="fn_eliminarCategoria(data.id, data.name)" class="p-button-danger dark:text-gray-50" />
                                    </div>
                                </template>
                            </Column>
                            <template #empty> No se encontraron categorias.</template>
                        </DataTable>
                    </div>
                </div>
            </div>
            <div class="card flex justify-center">
                <Dialog v-model:visible="showModal" modal header="Registrar una nueva categoria" :style="{ width: '30rem' }">
                    <div class="flex flex-col gap-y-2.5">
                        <div class="flex flex-col gap-y-2.5">
                            <div class="flex items-center flex-col gap-2.5">
                                <div class="flex flex-col w-full justify-between items-start gap-y-2">
                                    <label for="name" class="font-semibold w-full">Nombre :</label>
                                    <InputText id="name" class="flex-auto w-full" v-model="form.name" autocomplete="off" placeholder="Escribe nombre de la categoria" />
                                </div>
                                <div class="flex justify-start w-full">
                                    <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-y-2.5">
                            <div class="flex items-center flex-col">
                                <div class="flex flex-col w-full justify-between items-start gap-y-2">
                                    <label for="descripcion" class="font-semibold w-full">Descripcion :</label>
                                    <Textarea v-model="form.descripcion" autoResize rows="5" cols="30" class="w-full" placeholder="Escribe descripcion de la categoria" />
                                </div>
                                <div class="flex justify-center">
                                    <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.descripcion }}</span>
                                </div>
                            </div>
                            <div class="flex justify-end gap-2">
                                <Button type="button" label="Registrar Categoria" @click="submitForm"></Button>
                            </div>
                        </div>
                    </div>
                </Dialog>
            </div>
            <div class="card flex justify-center">
                <Dialog v-model:visible="showModalEditar" modal header="Editar categoria seleccionada" :style="{ width: '30rem' }">
                    <div class="flex flex-col gap-y-2.5">
                        <div class="flex flex-col gap-y-2.5">
                            <div class="flex items-center flex-col gap-2.5">
                                <div class="flex flex-col w-full justify-between items-start gap-y-2">
                                    <label for="name" class="font-semibold w-full">Nombre :</label>
                                    <InputText id="name" class="flex-auto w-full" v-model="form.name" autocomplete="off" placeholder="Escribe nombre de la categoria" />
                                </div>
                                <div class="flex justify-start w-full">
                                    <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-y-2.5">
                            <div class="flex items-center flex-col">
                                <div class="flex flex-col w-full justify-between items-start gap-y-2">
                                    <label for="descripcion" class="font-semibold w-full">Descripcion :</label>
                                    <Textarea v-model="form.descripcion" autoResize rows="5" cols="30" class="w-full" placeholder="Escribe descripcion de la categoria" />
                                </div>
                                <div class="flex justify-center">
                                    <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.descripcion }}</span>
                                </div>
                            </div>
                            <div class="flex justify-end gap-2">
                                <Button type="button" label="Save" @click="editarDatosCategoria"></Button>
                            </div>
                        </div>
                    </div>
                </Dialog>
            </div>
            <div class="card flex justify-center">
                <Dialog v-model:visible="showModalProductos" modal header="Imprimir el registro de productos" :style="{ width: '30rem' }">
                    <span class="text-surface-500 dark:text-surface-400 block mb-6">
                        Debe seleccionar el registro de productos a imprimir de acuerdo a las categorías.
                    </span>
                    <div class="flex flex-col gap-y-4">
                        <div class="flex flex-col gap-y-2.5">
                            <label for="categoria" class="font-semibold">Seleccione la categoría:</label>
                            <MultiSelect v-model="categoriaSeleccionada" :options="categorias" optionLabel="name" optionValue="id"
                                placeholder="Seleccione categorías" class="w-full" />
                        </div>
                        <div class="flex justify-end gap-2">
                            <Button type="button" label="Imprimir Registro" @click="fn_imprimirProductos"></Button>
                        </div>
                    </div>
                </Dialog>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
:deep(.p-datatable-header) {
    padding: 16px 0 !important;
}

:deep(.p-datatable-sort-icon) {
    color: white !important;
}

:deep(.p-datatable-tbody > tr > td:nth-child(7)) {
    text-align: center !important;
}

:deep(.p-datatable-header-cell:nth-child(3) .p-datatable-column-header-content) {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}
</style>
