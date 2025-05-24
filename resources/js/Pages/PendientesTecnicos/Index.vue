<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { ref, onMounted, watch } from 'vue';
import { ConsultaTraerPendientes } from '@/service/ConsultaTraerPendientes';
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';
import { usePage } from '@inertiajs/vue3';
import DatePicker from 'primevue/datepicker';

const { props } = usePage();
const user = props.auth.user.name;
const listarpendientes = ref([]);
const selectedPendientesTec = ref();
const fechaActual = new Date();
const fechaFiltro = ref(fechaActual);
const ahoraEnNY = new Date();
const padZero = num => num.toString().padStart(2, '0');

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    id: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    razonSocial: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    descripcion: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
});

const fechaCuadroTec = fechaActual
    .toISOString()
    .split('T')[0]
    .replace(/-/g, '');

const fechaHoy = ahoraEnNY.getFullYear() +
        padZero(ahoraEnNY.getMonth() + 1) +
        padZero(ahoraEnNY.getDate()) + '_' +
        padZero(ahoraEnNY.getHours()) +
        padZero(ahoraEnNY.getMinutes()) +
        padZero(ahoraEnNY.getSeconds());

const onRowReorder = (event) => {
    listarpendientes.value = event.value;
};

const btnColocarFechaLlegada = (rowData) => {
    const now = new Date();
    rowData.hora_llegada = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    rowData.marcadoLlegada = true;
};

const btnColocarFechaTermino = (rowData) => {
    const now = new Date();
    rowData.hora_termino = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    rowData.marcadoTermino = true;
};

const registrarTodosPendientes = async () => {
    try {
        const pendientes = listarpendientes.value.map((data) => ({
            razonSocial: data.razonSocial,
            descripcion: data.descripcion,
            hora: data.hora,
            responsable: data.responsable,
            fecha_registro: data.fecha_registro,
            hora_llegada: data.hora_llegada || null,
            hora_limite: data.hora_limite || null,
            hora_termino: data.hora_termino || null,
            marcadoLlegada: data.marcadoLlegada || false,
            marcadoTermino: data.marcadoTermino || false,
        }));

        const response = await axios.post('/guardar-pendientes', {
            data: pendientes,
            fechaHora: fechaHoy,
        });

        console.log(response.data.message);
    } catch (error) {
        console.error("Error al guardar todos los pendientes:", error.response?.data?.error || error.message);
    }
};

watch([fechaFiltro], ([newFechaFiltro]) => {
    if (newFechaFiltro) {
        // Convertir la fecha a formato YYYYMMDD
        let fecha = newFechaFiltro
            .toISOString()
            .split('T')[0] // Obtenemos el formato YYYY-MM-DD
            .replace(/-/g, ''); // Reemplazamos los guiones para obtener YYYYMMDD

        console.log("Nueva fecha seleccionada:", fecha);

        // Llamar al método de filtrado
        filtrarArchivos(fecha);
    }
});

function filtrarArchivos(fecha) {
    // Llamada para obtener la lista de archivos
    $.ajax({
        url: '/api/archivos-precios', // Endpoint para obtener la lista de archivos
        method: 'GET',
        dataType: 'json',
        success: function(files) {
            console.log(files)
            // Filtrar y encontrar el archivo más reciente
            let latestFile = getLatestFile(files, fecha);

            if (latestFile) {
                // Cargar y mostrar el contenido del archivo JSON más reciente
                $.ajax({
                    url: latestFile, // Asumiendo que el archivo es accesible en esta ruta
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let tbodyPrecios = $('#bodyPreciosXPresentacion');
                        tbodyPrecios.empty();

                        // Iterar sobre los objetos y mostrar sus propiedades
                        data.forEach(function(obj) {
                            // Crear una nueva fila
                            let nuevaFila = $('<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 text-black dark:text-white dark:hover:bg-gray-600 cursor-pointer">');

                            // Agregar las celdas con la información
                            nuevaFila.append($('<td class="mover-handle border-r dark:border-gray-700 p-2 text-center cursor-move"><i class="fas fa-grip-lines"></i></td>'));
                            nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer text-start">').text(obj.razonSocial));
                            nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer text-center" contenteditable="true">').text(obj.descripcion));
                            nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer text-center" contenteditable="true">').text(obj.responsable));
                            nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer text-center" contenteditable="true">').text(obj.hora));
                            nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer text-center" contenteditable="true">').text(obj.hora_llegada));
                            nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer text-center" contenteditable="true">').text(obj.hora_limite));
                            nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer text-center" contenteditable="true">').text(obj.hora_termino));
                            const user2 = props.auth.user.name; // Obtienes el nombre del usuario autenticado

                            console.log('Usuario actual:', user2);
                            console.log('Responsable:', obj.responsable);

                            if (obj.responsable === user2) { // Verifica si el responsable es el usuario autenticado
                                let accionesDiv = $('<div>')
                                    .addClass('flex gap-x-2.5 justify-center contenidoTDPend');

                                if (!obj.marcadoLlegada) {
                                    accionesDiv.append(
                                        $('<button>')
                                            .addClass('transform hover:translate-y-[-2px] inline-flex items-center justify-center bg-green-600 hover:bg-green-700 px-2 py-1 rounded-md')
                                            .click(function () {
                                                btnColocarFechaLlegada(obj);
                                            })
                                            .append(
                                                $('<i>')
                                                    .addClass('pi pi-check-circle text-base text-white')
                                            )
                                    );
                                }

                                if (!obj.marcadoTermino) {
                                    accionesDiv.append(
                                        $('<button>')
                                            .addClass('transform hover:translate-y-[-2px] inline-flex items-center justify-center bg-orange-600 hover:bg-orange-700 px-2 py-1 rounded-md')
                                            .click(function () {
                                                btnColocarFechaTermino(obj);
                                            })
                                            .append(
                                                $('<i>')
                                                    .addClass('pi pi-verified text-base text-white')
                                            )
                                    );
                                }

                                nuevaFila.append(
                                    $('<td>')
                                        .append(accionesDiv)
                                );
                            } else {
                                nuevaFila.append(
                                    $('<td>')
                                        .addClass('text-center text-gray-500 italic')
                                        .text('Acciones no disponibles')
                                );
                            }


                            // Agregar la nueva fila al tbody
                            tbodyPrecios.append(nuevaFila);
                        });

                        new Sortable(document.getElementById('bodyPreciosXPresentacion'), {
                            animation: 300, // Duración de la animación
                            handle: '.mover-handle', // Clase para el elemento que permite mover
                            onEnd: function(event) {
                                console.log(`Fila movida de ${event.oldIndex} a ${event.newIndex}`);
                                // Aquí puedes implementar lógica adicional, como actualizar el backend con el nuevo orden
                            },
                        });
                    }
                });
            } else {
                console.log('No se encontró un archivo con la fecha proporcionada.');
                let tbodyPrecios = $('#bodyPreciosXPresentacion');
                tbodyPrecios.empty();
                tbodyPrecios.html(`<tr class="rounded-lg border-2 dark:border-gray-700"><td colspan="25" class="text-center">No hay datos</td></tr>`);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener la lista de archivos:', error);
        }
    });

    // Función para obtener el archivo más reciente basado en la fecha proporcionada
    function getLatestFile(files, fecha) {
        let latestFile = '';
        let latestTime = '';

        files.forEach(function(file) {
            // Extraer la fecha y hora del nombre del archivo
            let match = file.match(/pendientesTecnicos_(\d{8})_(\d{6})\.json/);
            if (match) {
                let fileDate = match[1];
                let fileTime = match[2];

                // Comparar solo si la fecha coincide
                if (fileDate === fecha) {
                    if (fileTime > latestTime) {
                        latestTime = fileTime;
                        latestFile = file;
                    }
                }
            }
        });

        return latestFile;
    }
}

onMounted(() => {
    // Obtener los datos de la fecha actual
    filtrarArchivos(fechaCuadroTec);

    // Llamar al método para obtener los datos de pendientes tecnicos desde la API
    ConsultaTraerPendientes.obtenerIndexListadePendientes()
        .then((response) => {
            listarpendientes.value = response;
        })
        .catch(error => {
            console.error('Error al obtener los datos de pendientes tecnicos:', error);
        });
});

</script>

<template>
    <AppLayout title="Pendientes Tecnicos">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">2.- Cuadro de Pendientes (Tecnicos)</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="card text-sm -mt-4">
                        
                        <DataTable id="tablaPendientes" class="text-[13px]" removableSort v-model:filters="filters" v-model:selection="selectedPendientesTec" :value="listarpendientes" stateStorage="session" stateKey="dt-state-dt-pendientes-tecnicos" paginator :rows="5" filterDisplay="menu" selectionMode="single" dataKey="id" :globalFilterFields="['id', 'razonSocial', 'descripcion']" tableStyle="min-width: 50rem" :rowsPerPageOptions="[5, 10, 20, 50, 100]" paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink" currentPageReportTemplate="{first} de {last} de {totalRecords}" :reorderableRows="true" @rowReorder="onRowReorder">
                            <template #header>
                                <div class="w-full flex md:flex-row flex-col justify-between gap-y-4">
                                    <button @click="registrarTodosPendientes" class="text-white uppercase font-semibold text-xs md:w-56 bg-indigo-700 hover:bg-indigo-800 mt-0.5 py-2 px-4 rounded lg:w-min w-full whitespace-nowrap text-center">
                                        Registrar Pendientes
                                    </button>
                                    <div class="w-full md:max-w-max">
                                        <DatePicker v-model="fechaFiltro" showIcon fluid iconDisplay="input" inputId="fechaFiltro" dateFormat="dd/mm/yy"/>
                                    </div>
                                    <!-- <div class="flex justify-between flex-col md:flex-row gap-y-4">
                                        <div>
                                            <InputText v-model="filters['global'].value" placeholder="Buscar pendientes"
                                                class="md:w-80 w-full" />
                                        </div>
                                    </div> -->
                                </div>
                            </template>
                            <Column field="lugar" header="Mover" rowReorder :reorderableColumn="true" headerStyle="background-color: #16A34A; color:white; width: 3rem">
                            </Column>
                            <Column field="razonSocial" header="Cliente" style="width: 20%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="contenidoTDPend">
                                        {{ data.razonSocial }}
                                    </div>
                                </template>
                            </Column>
                            <Column field="descripcion" header="Descripcion" style="width: 40%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="contenidoTDPend">
                                        {{ data.descripcion }}
                                    </div>
                                </template>
                            </Column>
                            <Column field="responsable" header="Responsable" style="width: 10%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="contenidoTDPend">
                                        <input id="responsable" v-model="data.responsable" type="text" class="dark:bg-gray-800 dark:text-gray-50 text-black h-8 text-xs border-gray-50 w-24" />
                                    </div>
                                </template>
                            </Column>
                            <Column class="hidden" field="fecha_registro" header="Fecha Registro" style="width: 10%;" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="contenidoTDPend">
                                        <input id="fecha_registro" v-model="data.fecha_registro" type="date" class="dark:bg-gray-800 dark:text-gray-50 text-black h-8 text-xs border-gray-50 w-24" />
                                    </div>
                                </template>
                            </Column>
                            <Column class="whitespace-nowrap" field="hora_prevista" header="H. Prevista" style="width: 10%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="contenidoTDPend">
                                        <input type="text" v-model="data.hora" class="dark:bg-gray-800 dark:text-gray-50 text-black h-8 text-xs border-gray-50 w-24" />
                                    </div>
                                </template>
                            </Column>
                            <Column class="whitespace-nowrap" field="hora_llegada" header="H. Llegada" style="width: 10%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="contenidoTDPend">
                                        <input type="text" v-model="data.hora_llegada" :disabled="!$page.props.user.permissions.includes('Acciones Administrador')" class="dark:bg-gray-800 dark:text-gray-50 text-black h-8 text-xs border-gray-50 w-24" />
                                    </div>
                                </template>
                            </Column>
                            <Column class="whitespace-nowrap" field="hora_limite" header="H. Limite" style="width: 10%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="contenidoTDPend">
                                        <input id="hora_limite" v-model="data.hora_limite" type="text" class="dark:bg-gray-800 dark:text-gray-50 text-black h-8 text-xs border-gray-50 w-24" />
                                    </div>
                                </template>
                            </Column>
                            <Column class="whitespace-nowrap" field="hora_termino" header="H. Termino" style="width: 10%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="contenidoTDPend">
                                        <input type="text" v-model="data.hora_termino" :disabled="!$page.props.user.permissions.includes('Acciones Administrador')" class="dark:bg-gray-800 dark:text-gray-50 text-black h-8 text-xs border-gray-50 w-24" />
                                    </div>
                                </template>
                            </Column>
                            <Column field="acciones" header="Acciones" style="width: 5%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="flex gap-x-2.5 justify-center contenidoTDPend">
                                        <template v-if="data.responsable === user">
                                            <button v-if="!data.marcadoLlegada" @click="btnColocarFechaLlegada(data)"
                                                class="transform hover:translate-y-[-2px] inline-flex items-center justify-center bg-green-600 hover:bg-green-700 px-2 py-1 rounded-md">
                                                <i class="pi pi-check-circle text-base text-white"></i>
                                            </button>
                                            <button v-if="!data.marcadoTermino" @click="btnColocarFechaTermino(data)"
                                                class="transform hover:translate-y-[-2px] inline-flex items-center justify-center bg-orange-600 hover:bg-orange-700 px-2 py-1 rounded-md">
                                                <i class="pi pi-verified text-base text-white"></i>
                                            </button>
                                        </template>
                                    </div>
                                </template>
                            </Column>
                            <template #empty> No se encontraron registros de pendientes tecnicos.</template>
                        </DataTable>

                        <div class="w-full overflow-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" id="tablaPreciosXPresentacion">
                                <caption class="bg-green-600 text-gray-50 p-2 text-base rounded-t-lg sticky top-0 z-50 border-b-2 font-extrabold" id="tituloPrecios">CUADRO N° 01</caption>
                                <thead class="text-xs text-gray-100 uppercase bg-green-600 sticky top-[40px] z-50" id="headerPreciosXPresentacion">
                                    <tr>
                                        <th class="p-4 text-center whitespace-nowrap">Mover</th>
                                        <th class="p-4 text-center whitespace-nowrap">CLIENTE</th>
                                        <th class="p-4 text-center whitespace-nowrap">DESCRIPCION</th>
                                        <th class="p-4 text-center whitespace-nowrap">RESPONSABLE</th>
                                        <th class="p-4 text-center whitespace-nowrap">H. PREVISTA</th>
                                        <th class="p-4 text-center whitespace-nowrap">H. LLEGADA</th>
                                        <th class="p-4 text-center whitespace-nowrap">H. LIMITE</th>
                                        <th class="p-4 text-center whitespace-nowrap">H. TERMINO</th>
                                        <th class="p-4 text-center whitespace-nowrap">ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyPreciosXPresentacion">
    
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
#tablaPendientes .p-datatable-table{
    background-color: #16a34a !important;
}
#tablaPendientes .p-datatable-header{
    padding: 16px 0 !important;
}
#tablaPendientes .p-datatable-column-title{
    white-space: nowrap !important;
}
#tablaPendientes .p-datatable-sort-icon{
    color: white !important;
}

#tablaPendientes .p-datatable-thead > tr > th{
    font-size: 12px !important;
    background-color: #16a34a !important;
    color: white !important;
}

.dark #tablaPendientes .p-datatable-tbody > tr{
    background-color: #1f2937 !important;
    color: white !important;
    border-left: 2px solid #4b5563 !important;
    border-right: 2px solid #4b5563 !important;
}

#tablaPendientes .p-datatable-tbody > tr{
    background-color: #fff !important;
    color: 1f2937 !important;
}

#tablaPendientes .p-datatable-tbody > tr > td{
    font-size: 12px !important;
    white-space: nowrap !important;
    padding: 0px !important;
}

#tablaPendientes .p-datatable-tbody > tr > td > .contenidoTDPend{
    padding: 12px !important;
    cursor: pointer !important;
}

/* Truncar el contenido de las columnas */

#tablaPendientes .p-datatable-tbody > tr > td:nth-child(3) .contenidoTDPend{
    max-width: 300px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

#tablaPendientes .p-datatable-tbody > tr > td:nth-child(4) .contenidoTDPend{
    max-width: 300px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

#tablaPendientes .p-datatable-tbody > tr > td:nth-child(1) {
    text-align: center !important;
}

.dark .dialogClientes .p-dialog-content {
    background-color: #1e293b !important;
    color: #ffffff !important;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
}

.dark .dialogClientes .p-dialog-header {
    background-color: #1e293b !important;
    color: #ffffff !important;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    border-bottom: 1px solid #ffffff;
}

.dialogClientes .p-dialog-header {
    height: 55px;
    border-bottom: 1px solid #919090;
}

</style>