<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import InputIcon from 'primevue/inputicon';
import IconField from 'primevue/iconfield';
import { show_alerta, show_confirmacion } from '@/utils/alertasSwal';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { ConsultaListarActas } from '@/service/ConsultaListarActas';
import { ConsultaGenerarActadeConformidad } from '@/service/ConsultaGenerarActadeConformidad';
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';
import { formatDate } from '@/utils/funcionesglobales';
import Swal from 'sweetalert2';
import jsPDF from 'jspdf';
import 'jspdf-autotable';

const listaractas = ref([]);
const selectedActas = ref();
const filters = ref(
    {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        idActa: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        razon_social_acta: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        tipo_acta_conformidad: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        ruc_acta: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        direccion_acta: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        ruc_acta: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        trabajo_conformidad: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    }
);

const deleteActas = (id, razon_social_acta) => {
    show_confirmacion('¿Estás seguro?', `¿Estás seguro de eliminar definitivamente la acta de conformidad del cliente ${razon_social_acta}. Esto se eliminara definitivamente de la base de datos?`)
        .then((result) => {
            if (result.isConfirmed) {
                axios.delete(route('actasConformidad.destroy', id))
                    .then((response) => {
                        if (response.data.success) {
                            show_alerta(response.data.message, 'success');
                            ConsultaListarActas.obtenerListadeActaClientes()
                                .then((response) => {
                                    listaractas.value = response;
                                })
                                .catch(error => {
                                    console.error('Error al obtener los datos de salidas únicas:', error);
                                });
                        } else {
                            show_alerta(response.data.message, 'error');
                        }
                    })
                    .catch((error) => {
                        show_alerta('Hubo un error al intentar eliminar la salida.', 'error');
                        console.error('Error al eliminar la salida:', error);
                    });
            }
        });
};

const formatDatepdf = (dateString) => {
    const [year, month, day] = dateString.split('-');
    const d = new Date(year, month - 1, day);
    const options = { timeZone: 'America/Lima', day: '2-digit', month: '2-digit', year: 'numeric' };
    return new Intl.DateTimeFormat('es-PE', options).format(d);
};

let timerInterval;

const generarPdfdeActas = (data) => {

    Swal.fire({
        title: '¡Atención!',
        html: 'La acta de conformidad se esta generando, espere un momento.',
        timer: 999999999,
        timerProgressBar: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        },
        willClose: () => {
            clearInterval(timerInterval);
        }
    })

    const id = data.id;

    ConsultaGenerarActadeConformidad.obtenerListadeActaClientesPdf(id)
        .then((response) => {
            const actaData = response[0];

            const doc = new jsPDF();

            let eje_y = 10;
            let eje_x = 10;
            let margenDerecho = 10;
            let anchoPagina = doc.internal.pageSize.width;

            doc.setTextColor(0, 0, 0);
            doc.setFontSize(9);
            doc.setFont('Helvetica', 'normal');

            // Agregar imagen de fondo
            const backgroundImg = '/img/logo_ini.png';
            doc.addImage(backgroundImg, 'JPEG', eje_x, eje_y, 60, 20);

            eje_y += 5;

            function fn_dibujarEncabezado(texto) {
                const anchoTexto = doc.getTextWidth(texto);
                const eje_x_left = anchoPagina - anchoTexto - margenDerecho;
                doc.text(eje_x_left, eje_y, texto);
            }

            function fn_dibujarEncabezado2(texto) {
                const anchoTexto = doc.getTextWidth(texto);
                const eje_x_center = (anchoPagina / 2) - (anchoTexto / 2);
                doc.text(eje_x_center, eje_y, texto);
            }

            fn_dibujarEncabezado("Av. Separadora Mz A LT 8 Sector 28 de Julio");
            eje_y += 5;
            fn_dibujarEncabezado("Telf: 955571986 - 924808237 - 934094721");
            eje_y += 5;
            fn_dibujarEncabezado("Correo: industriasbalinsa@gmail.com");
            eje_y += 5;
            fn_dibujarEncabezado("RUC: 20608165585");

            eje_y += 8;

            doc.setFontSize(12);
            doc.setFont('Helvetica', 'bold');
            if (actaData.tipo_acta_conformidad === "Acta de Conformidad") {
                fn_dibujarEncabezado2(`ACTA DE CONFORMIDAD DEL SERVICIO`);
            } else if (actaData.tipo_acta_conformidad === "Acta de Venta") {
                fn_dibujarEncabezado2(`ACTA DE CONFORMIDAD DE VENTA`);
            }

            // ========== Inicia Función Dibujar Datos Generales==========

            function fn_dibujarDatosGenerales(inicioTabla) {
                doc.autoTable({
                    body: [
                        ['DATOS GENERALES']
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [0, 0, 0],
                        textColor: [0, 0, 0]
                    },
                    margin: { left: 10, right: 10 },
                    startY: inicioTabla,
                    columnStyles: {
                        0: {
                            cellWidth: 190,
                            fontStyle: 'bold'
                        }
                    },
                });

                doc.autoTable({
                    body: [
                        [
                            actaData.tipo_conformidad,
                            actaData.idActa,
                            'Fecha',
                            formatDatepdf(actaData.fecha_registro),
                            'Lugar de Atencion',
                            actaData.lugar_atencion
                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [0, 0, 0]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY,
                    columnStyles: {
                        0: {
                            cellWidth: 40,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 30
                        },
                        2: {
                            cellWidth: 30,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        },
                        3: {
                            cellWidth: 30,
                        },
                        4: {
                            cellWidth: 30,
                            fontStyle: 'bold',
                            textColor: [0, 0, 0]
                        }
                    },
                });
                doc.autoTable({
                    body: [
                        [
                            { content: `\nSEÑORES: ${actaData.razon_social_acta}\n\nRUC: ${actaData.ruc_acta}\n\nDIRECCION: ${actaData.direccion_acta}\n`, styles: { halign: 'left', fontStyle: 'bold' } }
                        ],
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [0, 0, 0]
                    },
                    margin: { top: 30, left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY,
                });
            }

            // ========== Termina Función Dibujar Datos Generales==========

            // ========== Inicia Función de Datos de Acta ==========

            function fn_dibujarDatosdeActa() {
                doc.autoTable({
                    body: [
                        [
                            actaData.tipo_conformidad,
                            `: ${actaData.idActa}`,
                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 9.5,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [255, 255, 255]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY + 5,
                    columnStyles: {
                        0: {
                            cellWidth: 75,
                            fontStyle: 'semibold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 110,
                            fontSize: 10,
                            textColor: [20, 20, 20]
                        },
                    },
                });

                doc.autoTable({
                    body: [
                        [
                            'Nombre del Proveedor',
                            `: INDUSTRIAS BALINSA E.I.R.L.`,
                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 9.5,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [255, 255, 255]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY + 2,
                    columnStyles: {
                        0: {
                            cellWidth: 75,
                            fontStyle: 'semibold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 110,
                            fontSize: 10,
                            textColor: [20, 20, 20]
                        },
                    },
                });

                doc.autoTable({
                    body: [
                        [
                            'Cliente',
                            `: ${actaData.razon_social_acta}`,
                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 9.5,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [255, 255, 255]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY + 2,
                    columnStyles: {
                        0: {
                            cellWidth: 75,
                            fontStyle: 'semibold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 110,
                            fontSize: 10,
                            textColor: [20, 20, 20]
                        },
                    },
                });

                doc.autoTable({
                    body: [
                        [
                            'Fecha de Inicio del Servicio',
                            `: ${formatDatepdf(actaData.fecha_acta)}`,
                            console.log(actaData.fecha_acta)
                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 9.5,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [255, 255, 255]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY + 2,
                    columnStyles: {
                        0: {
                            cellWidth: 75,
                            fontStyle: 'semibold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 110,
                            fontSize: 10,
                            textColor: [20, 20, 20]
                        },
                    },
                });

                doc.autoTable({
                    body: [
                        [
                            'Fecha de Fin del Servicio',
                            `: ${formatDatepdf(actaData.fecha_final_acta)}`,
                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 9.5,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [255, 255, 255]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY + 2,
                    columnStyles: {
                        0: {
                            cellWidth: 75,
                            fontStyle: 'semibold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 110,
                            fontSize: 10,
                            textColor: [20, 20, 20]
                        },
                    },
                });

                if (actaData.tipo_acta_conformidad === "Acta de Conformidad") {
                    doc.autoTable({
                        body: [
                            [
                                'Conformidad del Servicio',
                                `: Mediante la presente, dejamos constancia que el servicio ejecutado por la empresa INDUSTRIAS BALINSA E.I.R.L, así como las pruebas se ejecutaron a entera satisfacción del cliente ${actaData.razon_social_acta}. En el servicio de ${actaData.trabajo_conformidad} en optimo estado`,
                            ]
                        ],
                        rowPageBreak: 'avoid',
                        theme: 'grid',
                        styles: {
                            fontSize: 9.5,
                            cellPadding: 2,
                            lineWidth: 0.30,
                            lineColor: [255, 255, 255]
                        },
                        margin: { left: 10, right: 10 },
                        startY: doc.lastAutoTable.finalY + 2,
                        columnStyles: {
                            0: {
                                cellWidth: 75,
                                fontStyle: 'semibold',
                                textColor: [0, 0, 0]
                            },
                            1: {
                                cellWidth: 110,
                                fontSize: 10,
                                textColor: [20, 20, 20],
                                halign: 'justify'
                            },
                        },
                    });
                } else if (actaData.tipo_acta_conformidad === "Acta de Venta") {
                    doc.autoTable({
                        body: [
                            [
                                'Conformidad del Servicio',
                                `: Mediante la presente, dejamos constancia de la venta ejecutada por la empresa INDUSTRIAS BALINSA E.I.R.L, así como las pruebas del producto(os) a entera satisfacción del cliente ${actaData.razon_social_acta}. En la venta de ${actaData.trabajo_conformidad}.`,
                            ]
                        ],
                        rowPageBreak: 'avoid',
                        theme: 'grid',
                        styles: {
                            fontSize: 9.5,
                            cellPadding: 2,
                            lineWidth: 0.30,
                            lineColor: [255, 255, 255]
                        },
                        margin: { left: 10, right: 10 },
                        startY: doc.lastAutoTable.finalY + 2,
                        columnStyles: {
                            0: {
                                cellWidth: 75,
                                fontStyle: 'semibold',
                                textColor: [0, 0, 0]
                            },
                            1: {
                                cellWidth: 110,
                                fontSize: 10,
                                textColor: [20, 20, 20],
                                halign: 'justify'
                            },
                        },
                    });
                }

                doc.autoTable({
                    body: [
                        [
                            'Fecha de conformidad',
                            `: ${formatDatepdf(actaData.fecha_conformidad)}`,
                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 9.5,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [255, 255, 255]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY + 5,
                    columnStyles: {
                        0: {
                            cellWidth: 75,
                            fontStyle: 'semibold',
                            textColor: [0, 0, 0]
                        },
                        1: {
                            cellWidth: 110,
                            fontSize: 10,
                            textColor: [20, 20, 20]
                        },
                    },
                });

                doc.autoTable({
                    body: [
                        [
                            'En señal de conformidad se expide la presente constancia, firman a continuación las personas que intervinieron.',
                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 9,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [255, 255, 255]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY + 2,
                    columnStyles: {
                        0: {
                            cellWidth: 190,
                            fontStyle: 'semibold',
                            textColor: [0, 0, 0]
                        }
                    },
                });
            }

            // ========== Termina Función de Datos de Acta ==========

            // ========== Inicia Función para Dibujar las Firmas ==========

            function fn_firmasActadeConformidad() {
                doc.autoTable({
                    body: [
                        [
                            {
                                content: `${actaData.razon_social_acta}\nFIRMA`,
                                styles: {
                                    fontSize: 11,
                                    cellPadding: { top: 44.5, right: 5, bottom: 5, left: 5 },
                                    lineWidth: 0.30,
                                    lineColor: [255, 255, 255],
                                    fontStyle: 'bold',
                                    halign: 'center'
                                }
                            },
                            {
                                content: `${actaData.tecnico.toUpperCase()}\nFIRMA`,
                                styles: {
                                    fontSize: 11,
                                    cellPadding: { top: 44.5, right: 5, bottom: 5, left: 5 },
                                    lineWidth: 0.30,
                                    lineColor: [255, 255, 255],
                                    fontStyle: 'bold',
                                    halign: 'center'
                                }
                            },
                        ]
                    ],
                    rowPageBreak: 'avoid',
                    theme: 'grid',
                    styles: {
                        fontSize: 9,
                        cellPadding: 2,
                        lineWidth: 0.30,
                        lineColor: [255, 255, 255]
                    },
                    margin: { left: 10, right: 10 },
                    startY: doc.lastAutoTable.finalY + 2,
                    columnStyles: {
                        0: {
                            cellWidth: 95,
                            fontStyle: 'semibold',
                            textColor: [0, 0, 0],
                            halign: 'center'
                        },
                        1: {
                            cellWidth: 95,
                            fontStyle: 'semibold',
                            textColor: [0, 0, 0],
                            halign: 'center'
                        }
                    },
                    didDrawCell: function (data) {
                        if (data.cell.section === 'body') {
                            const lineY = data.cell.y + 40;

                            const xStart = data.cell.x + 10;
                            const yStart = lineY;
                            const xEnd = data.cell.x + data.cell.width - 10;
                            const yEnd = lineY;

                            doc.setLineWidth(0.3);
                            doc.setDrawColor(0, 0, 0);
                            doc.line(xStart, yStart, xEnd, yEnd);
                        }
                    }
                });
            }

            // ========== Termina Función para Dibujar las Firmas ==========

            const inicioTabla = 42;
            fn_dibujarDatosGenerales(inicioTabla);

            doc.setFontSize(8);
            doc.setFont('Helvetica', 'bold');
            fn_dibujarDatosdeActa();

            fn_firmasActadeConformidad();

            doc.setFontSize(8);
            doc.setFont('Helvetica', 'bold');

            clearInterval(timerInterval);
            Swal.close();

            const blob = doc.output('blob');
            const url = URL.createObjectURL(blob);
            window.open(url);
        })
        .catch(error => {
            console.error('Error al obtener los datos de salidas únicas:', error);
        });
};

onMounted(() => {
    ConsultaListarActas.obtenerListadeActaClientes()
        .then((response) => {
            listaractas.value = response;
        })
        .catch(error => {
            console.error('Error al obtener los datos de salidas únicas:', error);
        });
});

</script>
<template>
    <AppLayout title="Actas de Conformidad">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Lista de Actas de Conformidad</h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="card text-sm -mt-4">
                        <DataTable class="text-[13px]" removableSort v-model:filters="filters" v-model:selection="selectedActas" :value="listaractas"
                                stateStorage="session" stateKey="dt-state-dt-conformidad" paginator :rows="5" filterDisplay="menu"
                                selectionMode="single" dataKey="id" :globalFilterFields="['idActa','razon_social_acta','tipo_acta_conformidad','ruc_acta','direccion_acta','ruc_acta','trabajo_conformidad']" tableStyle="min-width: 50rem"
                                :rowsPerPageOptions="[5, 10, 20, 50, 100]" paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                                currentPageReportTemplate="{first} de {last} de {totalRecords}">
                            <template #header>
                                <div class="flex justify-between flex-col md:flex-row gap-y-4">
                                    <div class="flex justify-between gap-x-8 flex-col md:flex-row gap-y-2 text-base">
                                        <Link v-if="$page.props.user.permissions.includes('Registrar Acta Conformidad')" :href="route('actasConformidad.create')" class="text-white flex flex-row justify-center items-center gap-x-2.5 bg-indigo-700 hover:bg-indigo-800 transform hover:translate-y-[-2px] py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 16 16"><path fill="currentColor" fill-rule="evenodd" d="M3.5 1.5v13h5.75a.75.75 0 0 1 0 1.5H3a1 1 0 0 1-1-1V1a1 1 0 0 1 1-1h6.644a1 1 0 0 1 .72.305l3.355 3.476a1 1 0 0 1 .281.695V6.25a.75.75 0 0 1-1.5 0V6H9.75A1.75 1.75 0 0 1 8 4.25V1.5zm6 .07l2.828 2.93H9.75a.25.25 0 0 1-.25-.25zM13 15a.75.75 0 0 1-.75-.75v-1.5h-1.5a.75.75 0 0 1 0-1.5h1.5v-1.5a.75.75 0 0 1 1.5 0v1.5h1.5a.75.75 0 0 1 0 1.5h-1.5v1.5A.75.75 0 0 1 13 15" clip-rule="evenodd"/></svg> Registrar Acta
                                        </Link>
                                    </div>
                                    <div>
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText class="md:w-80 w-full" v-model="filters['global'].value" placeholder="Buscar actas de conformidad" />
                                        </IconField>
                                    </div>
                                </div>
                            </template>
                            <Column class="hidden" field="id" header="#" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.id }}</span>
                                </template>
                            </Column>
                            <Column field="idActa" header="N° Acta" sortable style="width: 10%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.idActa }}</span>
                                </template>
                            </Column>
                            <Column class="hidden" field="ruc_acta" header="RUC" sortable style="width: 10%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <span>{{ data.ruc_acta }}</span>
                                </template>
                            </Column>
                            <Column field="tipo_acta_conformidad" header="Tipo / Acta" sortable style="width: 16%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div @dblclick="() => generarPdfdeActas(data)" class="cursor-pointer">
                                        {{ data.tipo_acta_conformidad }}
                                    </div>
                                </template>
                            </Column>
                            <Column class="whitespace-nowrap" field="razon_social_acta" header="Razon Social" sortable style="width: 30%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div @dblclick="() => generarPdfdeActas(data)" class="cursor-pointer">
                                        <span class=" whitespace-break-spaces">{{ data.razon_social_acta }}</span>
                                    </div>
                                </template>
                            </Column>
                            <Column class="whitespace-nowrap" field="direccion_acta" header="Direccion" sortable style="width: 50%; max-width: 100px;" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div @dblclick="() => generarPdfdeActas(data)" class=" cursor-pointer" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" :title="data.direccion_acta">
                                        <span>{{ data.direccion_acta }}</span>
                                    </div>
                                </template>
                            </Column>
                            <Column class="whitespace-nowrap" field="fecha_acta" header="Fecha Inicio" sortable style="width: 30%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div @dblclick="() => generarPdfdeActas(data)" class="cursor-pointer">
                                        <span class=" whitespace-break-spaces">{{ formatDate(data.fecha_acta) }}</span>
                                    </div>
                                </template>
                            </Column>
                            <Column class="whitespace-nowrap" field="fecha_final_acta" header="Fecha Final" sortable style="width: 25%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div @dblclick="() => generarPdfdeActas(data)" class="cursor-pointer">
                                        <span class="whitespace-break-spaces">{{ formatDate(data.fecha_final_acta) }}</span>
                                    </div>
                                </template>
                            </Column>
                            <Column class="whitespace-nowrap" field="trabajo_conformidad" header="Descripcion" sortable style="width: 50%; max-width: 200px;" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div @dblclick="() => generarPdfdeActas(data)" class=" cursor-pointer" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" :title="data.trabajo_conformidad">
                                        <span>{{ data.trabajo_conformidad }}</span>
                                    </div>
                                </template>
                            </Column>
                            <Column v-if="$page.props.user.permissions.includes('Eliminar Acta Conformidad')" field="acciones" header="Acciones" style="width: 5%" headerStyle="background-color: #16A34A; color:white;">
                                <template #body="{ data }">
                                    <div class="flex gap-x-4 justify-center">
                                        <button @click="deleteActas(data.id, data.razon_social_acta)" class="transform hover:translate-y-[-2px] inline-flex items-center justify-center bg-red-600 hover:bg-red-700 px-1.5 py-0.5 rounded-md">
                                            <i class='bx bxs-trash text-base text-white'></i>
                                        </button>
                                    </div>
                                </template>
                            </Column>
                            <template #empty> No se encontraron registros de actas de conformidad.</template>
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
:deep(.p-datatable-tbody > tr > td:nth-child(2)) {
    text-align: center !important;
}
:deep(.p-datepicker-panel){
    z-index: 10004 !important;
}
</style>
