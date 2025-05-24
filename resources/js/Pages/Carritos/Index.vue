<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import { show_alerta, show_confirmacion } from '@/utils/alertasSwal';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { ref, onMounted, computed, watch } from 'vue';
import { ConsultaCompras } from '@/service/ConsultaCompras';
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';
import InputText from 'primevue/inputtext';
import * as XLSX from 'xlsx';
import InputNumber from 'primevue/inputnumber';

const carritos = ref([]);
const selectedCarritos = ref();
const filters = ref(
    {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        descripcion: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
);

const fn_eliminarPedidos = (id, materiales) => {
    const alerta = Swal.mixin({
        buttonsStyling: true
    });
    show_confirmacion(`¿Estás seguro de eliminar el producto del carrito definitivamente: ${materiales}. Esta acción no se puede deshacer!`)
    .then((result) => {
        if (result.isConfirmed) {
            form.delete(route('carritos.destroy', id), {
                onSuccess: () => {
                    show_alerta('El producto del carrito se a eliminado exitosamente.', 'success')
                    ConsultaCompras.obtenerDatosdeCompras().then((response) => {
                            carritos.value = response;
                        }).catch(error => {
                            console.error('Error al obtener los datos del cliente:', error);
                        });
                }
            });
        }
    });
}

const form = useForm({
    materiales: '',
    cantidad: 0,
    precio_u: 0,
});

const restearCarrito = () => {
    form.cantidad = 0;
    form.precio_t = 0;
};

const submitForm = () => {
    form.post(route('carritos.store'), {
        onSuccess: () => {
            show_alerta('El producto se ha registrado en el carrito correctamente.', 'success')
            restearCarrito();
            ConsultaCompras.obtenerDatosdeCompras().then((response) => {
                carritos.value = response;
            }).catch(error => {
                console.error('Error al obtener los datos del cliente:', error);
            });
        },
        onError: (errors) => {
            if (errors.response && errors.response.status) {
                show_alerta('Ha ocurrido un error al registrar el producto. Por favor, inténtalo de nuevo.', 'error')
                console.error('Error HTTP:', errors.response.status);
            } else {
                show_alerta('Ha ocurrido un error al procesar tu solicitud. Por favor, inténtalo de nuevo más tarde.', 'error')
                console.error('Error desconocido:', errors);
            }
        }
    });
}

const totalcompra = computed(() => {
    return carritos.value.reduce((total, product) => {
        return total + (parseFloat(product.precio_u) * parseInt(product.cantidad));
    }, 0);
});

const precio_t = computed(() => {
    const cantidad = parseFloat(form.cantidad)
    const precio_u = parseFloat(form.precio_u)
    return isNaN(cantidad) || isNaN(precio_u) ? 0 : (cantidad * precio_u).toFixed(2);
})

watch(precio_t, (newVal) => {
    form.precio_t = parseFloat(newVal);
});

const handleClick = async () => {
    await generarArchivoExcel();
    const whatsappLink = "https://api.whatsapp.com/send/?phone=51960269942&text=Hola+estoy+interesado+mas+informacion+a%3A+detalle&type=phone_number&app_absent=0";
    window.open(whatsappLink, '_blank');
};

const generarArchivoExcel = () => {
    return new Promise((resolve) => {
        const encabezado = [['Lista de Productos a Comprar']];
        const datos = encabezado.concat([['Materiales', 'Cantidad', 'Precio Unitario', 'Precio Total']]);

        const tbodyRows = document.querySelectorAll('tbody tr');
        tbodyRows.forEach((row) => {
            const rowData = [];
            const cells = row.querySelectorAll('td:not(.hidden)');
            cells.forEach(cell => {
                rowData.push(cell.textContent.trim());
            });
            datos.push(rowData);
        });

        const worksheet = XLSX.utils.aoa_to_sheet(datos);

        const rango = XLSX.utils.decode_range(worksheet['!ref']);
        for (let R = rango.s.r; R <= rango.e.r; ++R) {
            for (let C = rango.s.c; C <= rango.e.c; ++C) {
                const cell_address = { c: C, r: R };
                const cell_ref = XLSX.utils.encode_cell(cell_address);

                if (!worksheet[cell_ref]) continue;

                worksheet[cell_ref].s = {
                    font: {
                        name: 'Arial',
                        sz: 12,
                        bold: R === 0,
                        color: { rgb: R === 0 ? 'FFFFFF' : '000000' }
                    },
                    fill: {
                        fgColor: { rgb: R === 0 ? '4F81BD' : 'FFFFFF' }
                    },
                    alignment: {
                        vertical: 'center',
                        horizontal: 'center'
                    },
                    border: {
                        top: { style: 'thin', color: { rgb: '000000' } },
                        bottom: { style: 'thin', color: { rgb: '000000' } },
                        left: { style: 'thin', color: { rgb: '000000' } },
                        right: { style: 'thin', color: { rgb: '000000' } }
                    }
                };

                worksheet['!cols'] = worksheet['!cols'] || [];
                if (C === 1 || C === 2 || C === 3) {
                    worksheet['!cols'][C] = { wpx: 95 };
                }
                if (R === 0 && C === 0) {
                    worksheet['!cols'][C] = { wpx: 250 };
                }
            }
        }

        worksheet['!rows'] = worksheet['!rows'] || [];
        for (let R = rango.s.r; R <= rango.e.r; ++R) {
            worksheet['!rows'][R] = { hpx: 20 };
        }

        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Productos');
        XLSX.writeFile(workbook, 'compras.xlsx');
        resolve();
    });
};

onMounted(() => {
    ConsultaCompras.obtenerDatosdeCompras().then((response) => {
        carritos.value = response;
    }).catch(error => {
        console.error('Error al obtener los datos del cliente:', error);
    });

    const productoData = JSON.parse(localStorage.getItem('productoData'));
    if (productoData) {
        form.materiales = productoData.insumo;
        form.precio_u = Number(productoData.precio);
    }
});

</script>
<template>
    <AppLayout title="Lista de Compra de Productos">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Productos a Comprar</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <form @submit.prevent="submitForm" class="my-2 uppercase font-bol">
                        <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4 sm:gap-x-8 mb-3">
                            <div>
                                <label for="materiales" class="font-extrabold text-sm">Producto</label>
                                <InputText v-model="form.materiales" disabled placeholder="Escribe el nombre del producto" class="w-full mt-1.5" autocomplete="off"/>
                                <span v-if="form.errors.materiales" class="text-red-500 text-sm">{{ form.errors.materiales }}</span>
                            </div>
                            <div>
                                <label for="cantidad" class="font-extrabold text-sm">Cantidad</label>
                                <InputNumber class="mt-1.5" v-model.number="form.cantidad" inputId="integeronly" fluid />
                                <span v-if="form.errors.cantidad" class="text-red-500 text-sm">{{ form.errors.cantidad }}</span>
                            </div>
                            <div>
                                <label for="precio_u" class="font-extrabold text-sm">Precio Unitario</label>
                                <InputNumber class="mt-1.5" disabled v-model.number="form.precio_u" inputId="integeronly" fluid />
                                <span v-if="form.errors.precio_u" class="text-red-500 text-sm">{{ form.errors.precio_u }}</span>
                            </div>
                            <div>
                                <label for="precio_t" class="font-extrabold text-sm">Precio Total</label>
                                <InputNumber class="mt-1.5" v-model.number="form.precio_t" inputId="integeronly" fluid />
                                <span v-if="form.errors.precio_t" class="text-red-500 text-sm">{{ form.errors.precio_t }}</span>
                            </div>
                        </div>
                        <div class="flex justify-end md:flex-row flex-col gap-x-6 gap-y-4">
                            <a 
                                href="#" 
                                @click.prevent="handleClick" 
                                class="text-sm font-bold py-2 px-5 bg-green-600 hover:bg-green-700 text-gray-50 rounded-md w-full md:w-auto flex gap-2 items-center justify-center"
                            >
                                <img src="img/whatsapp.png" alt="" class="h-5"> Ir a WhatsApp
                            </a>
                            <Link v-if="$page.props.user.permissions.includes('Ingresar Entrada')" @click="showModal = true" class="text-white normal-case cursor-pointer flex justify-center items-center text-base gap-x-2.5 bg-indigo-700 hover:bg-indigo-800 transform hover:translate-y-[-2px] py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                <svg class="text-base" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a4 4 0 0 0 4-4V7.414a1 1 0 0 0-.293-.707l-3.414-3.414A1 1 0 0 0 16.586 3H7a4 4 0 0 0-4 4v10a4 4 0 0 0 4 4"/><path d="M9 3h6v3a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1zm8 18v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/><path stroke-linecap="round" d="M11 17h2"/></g></svg>
                                Guardar
                            </Link>
                        </div>
                    </form>
                    <div class="card text-sm -mt-2">
                        <DataTable removableSort v-model:filters="filters" v-model:selection="selectedCarritos" :value="carritos"
                                stateStorage="session" stateKey="dt-state-demo-session" paginator :rows="5" filterDisplay="menu"
                                selectionMode="single" dataKey="id" :globalFilterFields="['name', 'email', 'celular', 'apellidopat', 'apellidomat']" tableStyle="min-width: 50rem"
                                :rowsPerPageOptions="[5, 10, 20, 50, 100]" paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                                currentPageReportTemplate="{first} de {last} de {totalRecords}">
                            <template #header>
                            </template>
                            <Column class="hidden" field="id" header="#" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.id }}</span>
                                </template>
                            </Column>
                            <Column field="materiales" header="Materiales" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.materiales }}</span>
                                </template>
                            </Column>
                            <Column field="cantidad" header="Cantidad" sortable style="width: 5%; text-align: center;" headerStyle="background-color: #16A34A; color: white;">
                                <template #body="{ data }">
                                    <span class="text-center w-full block">{{ data.cantidad }}</span>
                                </template>
                            </Column>

                            <Column class=" whitespace-nowrap" field="precio_u" header="Precio Unitario" sortable style="width: 5%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span class="text-center w-full block">{{ data.moneda }} {{ data.precio_u }}</span>
                                </template>
                            </Column>
                            <Column field="precio_u" header="Precio Total" sortable style="width: 5%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span class="text-center w-full block">{{data.moneda}} {{ parseFloat(data.precio_u) * parseInt(data.cantidad)  }}</span>
                                </template>
                                <template #footer>
                                    <span class=" whitespace-nowrap">
                                        Saldo Total: S/.  {{ (totalcompra).toFixed(2) }} 
                                    </span>
                                </template>
                            </Column>
                            <Column field="acciones" header="Acciones" style="width: 15%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="flex gap-x-4 justify-center">
                                        <Button size="small" icon="pi pi-trash" @click="fn_eliminarPedidos(data.id, data.materiales)" class="p-button-danger" />
                                    </div>
                                </template>
                                
                            </Column>
                            <template #empty> No se encontraron lista de compras.</template>
                        </DataTable>
                    </div>
                </div>
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

:deep(.p-datatable-header-cell:nth-child(3) .p-datatable-column-header-content),
:deep(.p-datatable-header-cell:nth-child(4) .p-datatable-column-header-content),
:deep(.p-datatable-header-cell:nth-child(5) .p-datatable-column-header-content),
:deep(.p-datatable-header-cell:nth-child(6) .p-datatable-column-header-content) {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}
</style>