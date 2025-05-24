<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { ConsultaobtenerDatosdeActaClientes } from "@/service/ConsultaobtenerDatosdeActaClientes";
import { ConsultaObtenerListadeTecnicosActa } from "@/service/ConsultaObtenerListadeTecnicosActa";
import Select from "primevue/select";
import { show_alerta } from "@/utils/alertasSwal";
import InputText from "primevue/inputtext";
import DatePicker from "primevue/datepicker";
import jsPDF from "jspdf";
import "jspdf-autotable";
import Textarea from "primevue/textarea";

const { props } = usePage();
const user = props.auth.user.name;

const tecnicoactas = ref([]);
const actasClientes = ref([]);
const filteredDtClientes = ref([]);
const searchTerm = ref("");
const selectedIndex = ref(-1);
const selectedCity = ref(null);
const cities = ref([]);
const fechaHoy = new Date();
const selectedActa = ref(null);
const selectTipoActaConformidad = ref(null);

const form = useForm({
    idActa: "",
    tipo_acta_conformidad: "",
    tipo_conformidad: "",
    ruc_acta: "",
    razon_social_acta: "",
    direccion_acta: "",
    fecha_acta: fechaHoy,
    fecha_final_acta: fechaHoy,
    fecha_conformidad: fechaHoy,
    lugar_atencion: "",
    trabajo_conformidad: "",
    tecnico: "",
});

function onInput() {
    ConsultaobtenerDatosdeActaClientes.obtenerDatosdeActaClientes()
        .then((response) => {
            actasClientes.value = response;
            filterClientesAc();
        })
        .catch((error) => {
            console.error(
                "Error al obtener los datos de salidas únicas:",
                error
            );
        });
}

function filterClientesAc() {
    const term = searchTerm.value.toLowerCase();
    filteredDtClientes.value = actasClientes.value.filter(
        (cliente) =>
            cliente.razonSocial &&
            cliente.razonSocial.toLowerCase().includes(term)
    );
}

function onKeydown(event) {
    if (event.key === "ArrowDown") {
        event.preventDefault();
        selectedIndex.value =
            (selectedIndex.value + 1) % filteredDtClientes.value.length;
    } else if (event.key === "ArrowUp") {
        event.preventDefault();
        selectedIndex.value =
            (selectedIndex.value - 1 + filteredDtClientes.value.length) %
            filteredDtClientes.value.length;
    } else if (event.key === "Enter") {
        event.preventDefault();
        if (selectedIndex.value >= 0) {
            selectClienteActa(filteredDtClientes.value[selectedIndex.value]);
        }
    }
}

function selectClienteActa(cliente) {
    searchTerm.value = cliente.razonSocial;
    form.cliente_id = cliente.id;
    form.name = cliente.numeroDocumento;

    cities.value = [
        { name: cliente.direccion, value: cliente.direccion },
        { name: cliente.cli_direccion2, value: cliente.cli_direccion2 },
    ].filter((direccion) => direccion.name);

    form.lugar_atencion = cliente.prov_nombre;
    form.ruc_acta = cliente.numeroDocumento;

    filteredDtClientes.value = [];
    selectedIndex.value = -1;
}

function updateSelection(index) {
    selectedIndex.value = index;
}

const tipoacta = ref([
    { name: "N° de Cotización" },
    { name: "Orden de Compra" },
]);

const tipoActaConformidad = ref([
    { name: "Acta de Conformidad" },
    { name: "Acta de Venta" },
]);

//

const filteredTecnico = ref([]);
const searchTermTecnico = ref("");
const selectedTecnico = ref(-1);

function onInputTecnico() {
    if (searchTermTecnico.value.length >= 1) {
        ConsultaObtenerListadeTecnicosActa.obtenerListadeTecnicosActa()
            .then((response) => {
                filteredTecnico.value = response.filter(
                    (tecnico) =>
                        tecnico.name
                            .toLowerCase()
                            .includes(searchTermTecnico.value.toLowerCase()) ||
                        tecnico.apellidomat
                            .toLowerCase()
                            .includes(searchTermTecnico.value.toLowerCase()) ||
                        tecnico.apellidopat
                            .toLowerCase()
                            .includes(searchTermTecnico.value.toLowerCase())
                );
            })
            .catch((error) => {
                console.error("Error al obtener los técnicos:", error);
            });
    } else {
        filteredTecnico.value = [];
    }
}

function onKeydownTecnico(event) {
    if (event.key === "ArrowDown") {
        event.preventDefault();
        selectedTecnico.value =
            (selectedTecnico.value + 1) % filteredTecnico.value.length;
    } else if (event.key === "ArrowUp") {
        event.preventDefault();
        selectedTecnico.value =
            (selectedTecnico.value - 1 + filteredTecnico.value.length) %
            filteredTecnico.value.length;
    } else if (event.key === "Enter") {
        event.preventDefault();
        if (selectedTecnico.value >= 0) {
            selectTecnico(filteredTecnico.value[selectedTecnico.value]);
        }
    }
}

function selectTecnico(tecnico) {
    searchTermTecnico.value =
        tecnico.name + " " + tecnico.apellidopat + " " + tecnico.apellidomat;
    form.tecnico_id = tecnico.id;
    form.name = tecnico.name;
    filteredTecnico.value = [];
    selectedTecnico.value = -1;
}

function updateSelectionTecnico(index) {
    selectedTecnico.value = index;
}

//

watch(selectedCity, (newValue) => {
    form.direccion_acta = newValue.name;
});

watch(selectedActa, (newValue) => {
    form.tipo_conformidad = newValue.name;
});

watch(selectTipoActaConformidad, (newValue) => {
    form.tipo_acta_conformidad = newValue.name;
});

watch(
    () => searchTerm.value,
    (newValue) => {
        form.razon_social_acta = newValue;
    }
);

watch(
    () => searchTermTecnico.value,
    (newValue) => {
        form.tecnico = newValue;
    }
);

const submitForm = () => {
    form.post(route("actasConformidad.store"), {
        onSuccess: () => {
            show_alerta(
                "La acta de conformidad se ha registrado correctamente.",
                "success"
            );
        },
        onError: (errors) => {
            show_alerta(
                "Ha ocurrido un error al registrar la acta de conformidad. Por favor, inténtalo de nuevo.",
                "error"
            );
            console.error("Error al registr la entrada:", errors);
        },
    });
};

// previsualicion del pdf de la acta de conformidad

const previsualizarActaConformidad = () => {
    const formatDate = (date) => {
        const d = new Date(date);
        const day = String(d.getDate()).padStart(2, "0");
        const month = String(d.getMonth() + 1).padStart(2, "0");
        const year = d.getFullYear();
        return `${day}/${month}/${year}`;
    };

    const idActa = form.idActa;
    const tipo_conformidad = form.tipo_conformidad;
    const tipo_acta_conformidad = form.tipo_acta_conformidad;
    const ruc_acta = $("#ruc_acta").val();
    const razon_social_acta = $("#razon_social_acta").val();
    const fecha_acta = formatDate(form.fecha_acta);
    const fecha_conformidad = formatDate(form.fecha_conformidad);
    const fecha_final_acta = formatDate(form.fecha_final_acta);
    const lugar_atencion = $("#lugar_atencion").val();
    const tecnico = $("#tecnico").val();
    const trabajo_conformidad = $("#trabajo_conformidad").val();
    const direccion_acta = form.direccion_acta;

    const doc = new jsPDF();

    let eje_y = 10;
    let eje_x = 10;
    let margenDerecho = 10;
    let anchoPagina = doc.internal.pageSize.width;

    doc.setTextColor(0, 0, 0);
    doc.setFontSize(9);
    doc.setFont("Helvetica", "normal");

    // Agregar imagen de fondo
    const backgroundImg = "/img/logo_ini.png";
    doc.addImage(backgroundImg, "JPEG", eje_x, eje_y, 60, 20);

    eje_y += 5;

    function fn_dibujarEncabezado(texto) {
        const anchoTexto = doc.getTextWidth(texto);
        const eje_x_left = anchoPagina - anchoTexto - margenDerecho;
        doc.text(eje_x_left, eje_y, texto);
    }

    function fn_dibujarEncabezado2(texto) {
        const anchoTexto = doc.getTextWidth(texto);
        const eje_x_center = anchoPagina / 2 - anchoTexto / 2;
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
    doc.setFont("Helvetica", "bold");
    if (tipo_acta_conformidad === "Acta de Conformidad") {
        fn_dibujarEncabezado2(`ACTA DE CONFORMIDAD DEL SERVICIO`);
    } else if (tipo_acta_conformidad === "Acta de Venta") {
        fn_dibujarEncabezado2(`ACTA DE CONFORMIDAD DE VENTA`);
    }

    // ========== Inicia Función Dibujar Datos Generales==========

    function fn_dibujarDatosGenerales(inicioTabla) {
        doc.autoTable({
            body: [["DATOS GENERALES"]],
            rowPageBreak: "avoid",
            theme: "grid",
            styles: {
                fontSize: 8,
                cellPadding: 2,
                lineWidth: 0.3,
                lineColor: [0, 0, 0],
                textColor: [0, 0, 0],
            },
            margin: { left: 10, right: 10 },
            startY: inicioTabla,
            columnStyles: {
                0: {
                    cellWidth: 190,
                    fontStyle: "bold",
                },
            },
        });

        doc.autoTable({
            body: [
                [
                    tipo_conformidad,
                    idActa,
                    "Fecha",
                    fecha_conformidad,
                    "Lugar de Atencion",
                    lugar_atencion,
                ],
            ],
            rowPageBreak: "avoid",
            theme: "grid",
            styles: {
                fontSize: 8,
                cellPadding: 2,
                lineWidth: 0.3,
                lineColor: [0, 0, 0],
            },
            margin: { left: 10, right: 10 },
            startY: doc.lastAutoTable.finalY,
            columnStyles: {
                0: {
                    cellWidth: 40,
                    fontStyle: "bold",
                    textColor: [0, 0, 0],
                },
                1: {
                    cellWidth: 30,
                },
                2: {
                    cellWidth: 30,
                    fontStyle: "bold",
                    textColor: [0, 0, 0],
                },
                3: {
                    cellWidth: 30,
                },
                4: {
                    cellWidth: 30,
                    fontStyle: "bold",
                    textColor: [0, 0, 0],
                },
            },
        });
        doc.autoTable({
            body: [
                [
                    {
                        content: `\nSEÑORES: ${razon_social_acta}\n\nRUC: ${ruc_acta}\n\nDIRECCION: ${direccion_acta}\n`,
                        styles: { halign: "left", fontStyle: "bold" },
                    },
                ],
            ],
            rowPageBreak: "avoid",
            theme: "grid",
            styles: {
                fontSize: 8,
                cellPadding: 2,
                lineWidth: 0.3,
                lineColor: [0, 0, 0],
            },
            margin: { top: 30, left: 10, right: 10 },
            startY: doc.lastAutoTable.finalY,
        });
    }

    // ========== Termina Función Dibujar Datos Generales==========

    // ========== Inicia Función de Datos de Acta ==========

    function fn_dibujarDatosdeActa() {
        doc.autoTable({
            body: [[tipo_conformidad, `: ${idActa}`]],
            rowPageBreak: "avoid",
            theme: "grid",
            styles: {
                fontSize: 9.5,
                cellPadding: 2,
                lineWidth: 0.3,
                lineColor: [255, 255, 255],
            },
            margin: { left: 10, right: 10 },
            startY: doc.lastAutoTable.finalY + 5,
            columnStyles: {
                0: {
                    cellWidth: 75,
                    fontStyle: "semibold",
                    textColor: [0, 0, 0],
                },
                1: {
                    cellWidth: 110,
                    fontSize: 10,
                    textColor: [20, 20, 20],
                },
            },
        });

        doc.autoTable({
            body: [["Nombre del Proveedor", `: INDUSTRIAS BALINSA E.I.R.L.`]],
            rowPageBreak: "avoid",
            theme: "grid",
            styles: {
                fontSize: 9.5,
                cellPadding: 2,
                lineWidth: 0.3,
                lineColor: [255, 255, 255],
            },
            margin: { left: 10, right: 10 },
            startY: doc.lastAutoTable.finalY + 2,
            columnStyles: {
                0: {
                    cellWidth: 75,
                    fontStyle: "semibold",
                    textColor: [0, 0, 0],
                },
                1: {
                    cellWidth: 110,
                    fontSize: 10,
                    textColor: [20, 20, 20],
                },
            },
        });

        doc.autoTable({
            body: [["Cliente", `: ${razon_social_acta}`]],
            rowPageBreak: "avoid",
            theme: "grid",
            styles: {
                fontSize: 9.5,
                cellPadding: 2,
                lineWidth: 0.3,
                lineColor: [255, 255, 255],
            },
            margin: { left: 10, right: 10 },
            startY: doc.lastAutoTable.finalY + 2,
            columnStyles: {
                0: {
                    cellWidth: 75,
                    fontStyle: "semibold",
                    textColor: [0, 0, 0],
                },
                1: {
                    cellWidth: 110,
                    fontSize: 10,
                    textColor: [20, 20, 20],
                },
            },
        });

        doc.autoTable({
            body: [["Fecha de Inicio del Servicio", `: ${fecha_acta}`]],
            rowPageBreak: "avoid",
            theme: "grid",
            styles: {
                fontSize: 9.5,
                cellPadding: 2,
                lineWidth: 0.3,
                lineColor: [255, 255, 255],
            },
            margin: { left: 10, right: 10 },
            startY: doc.lastAutoTable.finalY + 2,
            columnStyles: {
                0: {
                    cellWidth: 75,
                    fontStyle: "semibold",
                    textColor: [0, 0, 0],
                },
                1: {
                    cellWidth: 110,
                    fontSize: 10,
                    textColor: [20, 20, 20],
                },
            },
        });

        doc.autoTable({
            body: [["Fecha de Fin del Servicio", `: ${fecha_final_acta}`]],
            rowPageBreak: "avoid",
            theme: "grid",
            styles: {
                fontSize: 9.5,
                cellPadding: 2,
                lineWidth: 0.3,
                lineColor: [255, 255, 255],
            },
            margin: { left: 10, right: 10 },
            startY: doc.lastAutoTable.finalY + 2,
            columnStyles: {
                0: {
                    cellWidth: 75,
                    fontStyle: "semibold",
                    textColor: [0, 0, 0],
                },
                1: {
                    cellWidth: 110,
                    fontSize: 10,
                    textColor: [20, 20, 20],
                },
            },
        });

        if (tipo_acta_conformidad === "Acta de Conformidad") {
            doc.autoTable({
                body: [
                    [
                        "Conformidad del Servicio",
                        `: Mediante la presente, dejamos constancia que el servicio ejecutado por la empresa INDUSTRIAS BALINSA E.I.R.L, así como las pruebas se ejecutaron a entera satisfacción del cliente ${razon_social_acta}. En el servicio de ${trabajo_conformidad} en optimo estado`,
                    ],
                ],
                rowPageBreak: "avoid",
                theme: "grid",
                styles: {
                    fontSize: 9.5,
                    cellPadding: 2,
                    lineWidth: 0.3,
                    lineColor: [255, 255, 255],
                },
                margin: { left: 10, right: 10 },
                startY: doc.lastAutoTable.finalY + 2,
                columnStyles: {
                    0: {
                        cellWidth: 75,
                        fontStyle: "semibold",
                        textColor: [0, 0, 0],
                    },
                    1: {
                        cellWidth: 110,
                        fontSize: 10,
                        textColor: [20, 20, 20],
                        halign: "justify",
                    },
                },
            });
        } else if (tipo_acta_conformidad === "Acta de Venta") {
            doc.autoTable({
                body: [
                    [
                        "Conformidad del Servicio",
                        `: Mediante la presente, dejamos constancia de la venta ejecutada por la empresa INDUSTRIAS BALINSA E.I.R.L, así como las pruebas del producto(os) a entera satisfacción del cliente ${razon_social_acta}. En la venta de ${trabajo_conformidad}.`,
                    ],
                ],
                rowPageBreak: "avoid",
                theme: "grid",
                styles: {
                    fontSize: 9.5,
                    cellPadding: 2,
                    lineWidth: 0.3,
                    lineColor: [255, 255, 255],
                },
                margin: { left: 10, right: 10 },
                startY: doc.lastAutoTable.finalY + 2,
                columnStyles: {
                    0: {
                        cellWidth: 75,
                        fontStyle: "semibold",
                        textColor: [0, 0, 0],
                    },
                    1: {
                        cellWidth: 110,
                        fontSize: 10,
                        textColor: [20, 20, 20],
                        halign: "justify",
                    },
                },
            });
        }

        doc.autoTable({
            body: [["Fecha de conformidad", `: ${fecha_conformidad}`]],
            rowPageBreak: "avoid",
            theme: "grid",
            styles: {
                fontSize: 9.5,
                cellPadding: 2,
                lineWidth: 0.3,
                lineColor: [255, 255, 255],
            },
            margin: { left: 10, right: 10 },
            startY: doc.lastAutoTable.finalY + 5,
            columnStyles: {
                0: {
                    cellWidth: 75,
                    fontStyle: "semibold",
                    textColor: [0, 0, 0],
                },
                1: {
                    cellWidth: 110,
                    fontSize: 10,
                    textColor: [20, 20, 20],
                },
            },
        });

        doc.autoTable({
            body: [
                [
                    "En señal de conformidad se expide la presente constancia, firman a continuación las personas que intervinieron.",
                ],
            ],
            rowPageBreak: "avoid",
            theme: "grid",
            styles: {
                fontSize: 9,
                cellPadding: 2,
                lineWidth: 0.3,
                lineColor: [255, 255, 255],
            },
            margin: { left: 10, right: 10 },
            startY: doc.lastAutoTable.finalY + 2,
            columnStyles: {
                0: {
                    cellWidth: 190,
                    fontStyle: "semibold",
                    textColor: [0, 0, 0],
                },
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
                        content: `${razon_social_acta}\nFIRMA`,
                        styles: {
                            fontSize: 11,
                            cellPadding: {
                                top: 44.5,
                                right: 5,
                                bottom: 5,
                                left: 5,
                            },
                            lineWidth: 0.3,
                            lineColor: [255, 255, 255],
                            fontStyle: "bold",
                            halign: "center",
                        },
                    },
                    {
                        content: `${tecnico.toUpperCase()}\nFIRMA`,
                        styles: {
                            fontSize: 11,
                            cellPadding: {
                                top: 44.5,
                                right: 5,
                                bottom: 5,
                                left: 5,
                            },
                            lineWidth: 0.3,
                            lineColor: [255, 255, 255],
                            fontStyle: "bold",
                            halign: "center",
                        },
                    },
                ],
            ],
            rowPageBreak: "avoid",
            theme: "grid",
            styles: {
                fontSize: 9,
                cellPadding: 2,
                lineWidth: 0.3,
                lineColor: [255, 255, 255],
            },
            margin: { left: 10, right: 10 },
            startY: doc.lastAutoTable.finalY + 2,
            columnStyles: {
                0: {
                    cellWidth: 95,
                    fontStyle: "semibold",
                    textColor: [0, 0, 0],
                    halign: "center",
                },
                1: {
                    cellWidth: 95,
                    fontStyle: "semibold",
                    textColor: [0, 0, 0],
                    halign: "center",
                },
            },
            didDrawCell: function (data) {
                if (data.cell.section === "body") {
                    const lineY = data.cell.y + 40;

                    const xStart = data.cell.x + 10;
                    const yStart = lineY;
                    const xEnd = data.cell.x + data.cell.width - 10;
                    const yEnd = lineY;

                    doc.setLineWidth(0.3);
                    doc.setDrawColor(0, 0, 0);
                    doc.line(xStart, yStart, xEnd, yEnd);
                }
            },
        });
    }

    // ========== Termina Función para Dibujar las Firmas ==========

    const inicioTabla = 42;
    fn_dibujarDatosGenerales(inicioTabla);

    doc.setFontSize(8);
    doc.setFont("Helvetica", "bold");
    fn_dibujarDatosdeActa();

    fn_firmasActadeConformidad();

    doc.setFontSize(8);
    doc.setFont("Helvetica", "bold");

    const blob = doc.output("blob");
    const url = URL.createObjectURL(blob);
    window.open(url);
};
</script>

<template>
    <AppLayout title="Actas de Conformidad">
        <template #header>
            <h1
                class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white"
            >
                Registrar Acta de Conformidad
            </h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-xs shadow-lg bg-white dark:bg-gray-800 rounded-lg"
                >
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                        <form @submit.prevent="submitForm">
                            <div
                                class="grid grid-cols-1 gap-y-3 md:grid-cols-2 lg:grid-cols-3 sm:gap-x-8 mb-3 sm:py-0 py-2"
                            >
                                <div
                                    class="flex flex-col gap-y-1.5 md:gap-y-1 w-full"
                                >
                                    <label
                                        for="tipo_acta_conformidad"
                                        class="font-extrabold text-md"
                                        >Acta de Conformidad</label
                                    >
                                    <Select
                                        :panelStyle="{
                                            width: '250px',
                                            fontSize: '14px',
                                        }"
                                        v-model="selectTipoActaConformidad"
                                        :options="tipoActaConformidad"
                                        id="tipo_acta_conformidad"
                                        optionLabel="name"
                                        placeholder="Seleccionar acta de conformidad"
                                        class="w-full h-[37.6px] flex items-center"
                                    />
                                    <span
                                        v-if="form.errors.tipo_acta_conformidad"
                                        class="text-red-500 text-sm"
                                        >{{
                                            form.errors.tipo_acta_conformidad
                                        }}</span
                                    >
                                </div>
                                <div
                                    class="flex flex-col gap-y-1.5 md:gap-y-1 w-full"
                                >
                                    <label
                                        for="tipo_conformidad"
                                        class="font-extrabold text-md"
                                        >Tipo de Acta</label
                                    >
                                    <Select
                                        :panelStyle="{
                                            width: '250px',
                                            fontSize: '14px',
                                        }"
                                        v-model="selectedActa"
                                        :options="tipoacta"
                                        id="tipo_conformidad"
                                        optionLabel="name"
                                        placeholder="Seleccionar tipo de acta"
                                        class="w-full h-[37.6px] flex items-center"
                                    />
                                    <span
                                        v-if="form.errors.tipo_conformidad"
                                        class="text-red-500 text-sm"
                                        >{{
                                            form.errors.tipo_conformidad
                                        }}</span
                                    >
                                </div>
                                <div
                                    class="flex flex-col gap-y-1.5 md:gap-y-1 w-full"
                                >
                                    <label
                                        for="lugar_atencion"
                                        class="font-extrabold text-md"
                                        >N° de Acta</label
                                    >
                                    <InputText
                                        type="number"
                                        v-model="form.idActa"
                                        id="idActa"
                                        size="small"
                                        placeholder="0000"
                                        min="0"
                                        class="no-spinners"
                                    />
                                    <span
                                        v-if="form.errors.idActa"
                                        class="text-red-500 text-sm"
                                        >{{ form.errors.idActa }}</span
                                    >
                                </div>
                                <div class="w-full hidden">
                                    <InputText
                                        type="text"
                                        v-model="form.ruc_acta"
                                        id="ruc_acta"
                                        size="small"
                                    />
                                </div>
                                <div class="w-full">
                                    <div class="relative">
                                        <label
                                            for="cliente"
                                            class="font-extrabold text-md"
                                            >Cliente</label
                                        >
                                        <InputText
                                            v-model="searchTerm"
                                            @input="onInput"
                                            id="razon_social_acta"
                                            size="small"
                                            @keydown="onKeydown"
                                            placeholder="Escribe el nombre del cliente"
                                            autocomplete="off"
                                            class="mt-1 focus:ring-indigo-500 dark:text-white focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg h-[41.6px]"
                                        />
                                        <span
                                            v-if="form.errors.razon_social_acta"
                                            class="text-red-500 text-sm"
                                            >{{
                                                form.errors.razon_social_acta
                                            }}</span
                                        >
                                        <div
                                            v-show="
                                                filteredDtClientes.length > 0
                                            "
                                            class="md:w-full w-auto z-50 max-h-40 border border-gray-300 rounded-lg absolute overflow-auto text-sm divide-y divide-gray-200 bg-white"
                                        >
                                            <div
                                                v-for="(
                                                    cliente, index
                                                ) in filteredDtClientes"
                                                :key="cliente.id"
                                                :class="[
                                                    'p-2 cursor-pointer dark:bg-gray-700',
                                                    {
                                                        'bg-gray-200 dark:bg-gray-800':
                                                            index ===
                                                            selectedIndex,
                                                    },
                                                ]"
                                                @click="
                                                    selectClienteActa(cliente)
                                                "
                                                @mouseover="
                                                    updateSelection(index)
                                                "
                                            >
                                                {{ cliente.razonSocial }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="flex flex-col gap-y-1.5 md:gap-y-1.5 w-full"
                                >
                                    <label
                                        for="cliente"
                                        class="font-extrabold text-md"
                                        >Dirección del Cliente</label
                                    >
                                    <Select
                                        v-model="selectedCity"
                                        :options="cities"
                                        id="direccion_acta"
                                        optionLabel="name"
                                        placeholder="Seleccionar dirección del cliente"
                                        class="w-full h-[37.6px] flex items-center text-start"
                                        itemStyle="font-size: 12px; padding: 4px;"
                                        :panelStyle="{
                                            width: '250px',
                                            fontSize: '13px',
                                        }"
                                    />
                                    <span
                                        v-if="form.errors.direccion_acta"
                                        class="text-red-500 text-sm"
                                        >{{ form.errors.direccion_acta }}</span
                                    >
                                </div>
                                <div
                                    class="flex flex-col gap-y-1 md:gap-y-0.5 w-full"
                                >
                                    <label
                                        for="lugar_atencion"
                                        class="font-extrabold text-md"
                                        >Lugar de Atención</label
                                    >
                                    <InputText
                                        v-model="form.lugar_atencion"
                                        id="lugar_atencion"
                                        disabled
                                        size="small"
                                        placeholder="Ciudad del cliente"
                                        class="w-full h-[37.6px] mt-1 focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md"
                                        autocomplete="off"
                                    />
                                    <span
                                        v-if="form.errors.lugar_atencion"
                                        class="text-red-500 text-sm"
                                        >{{ form.errors.lugar_atencion }}</span
                                    >
                                </div>
                                <div class="flex flex-col w-full">
                                    <label
                                        for="fecha"
                                        class="font-bold block mb-2"
                                        >Fecha Inicio del Servicio</label
                                    >
                                    <DatePicker
                                        v-model="form.fecha_acta"
                                        size="small"
                                        id="fecha_acta"
                                        class="h-[37.6px] -mt-0.5"
                                        showIcon
                                        fluid
                                        :showOnFocus="false"
                                        dateFormat="dd/mm/yy"
                                    />
                                </div>
                                <div class="flex-col w-full hidden">
                                    <label
                                        for="fecha_conformidad"
                                        class="font-bold block mb-2"
                                        >Fecha Conformidad</label
                                    >
                                    <DatePicker
                                        v-model="form.fecha_conformidad"
                                        size="small"
                                        id="fecha_conformidad"
                                        class="h-[37.6px] -mt-0.5"
                                        showIcon
                                        fluid
                                        :showOnFocus="false"
                                        dateFormat="dd/mm/yy"
                                    />
                                </div>
                                <div class="flex flex-col w-full">
                                    <label
                                        for="fecha"
                                        class="font-bold block mb-2"
                                        >Fecha Final del Servicio</label
                                    >
                                    <DatePicker
                                        v-model="form.fecha_final_acta"
                                        size="small"
                                        id="fecha_final_acta"
                                        class="h-[37.6px] -mt-0.5"
                                        showIcon
                                        fluid
                                        :showOnFocus="false"
                                        dateFormat="dd/mm/yy"
                                    />
                                </div>
                                <div class="w-full">
                                    <div class="relative">
                                        <label
                                            for="tecnico"
                                            class="font-extrabold text-md"
                                            >Técnico Encargado</label
                                        >
                                        <InputText
                                            v-model="searchTermTecnico"
                                            @input="onInputTecnico"
                                            id="tecnico"
                                            size="small"
                                            @keydown="onKeydownTecnico"
                                            placeholder="Escribe el nombre del técnico"
                                            class="w-full mt-1 h-[37.6px] focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md"
                                            autocomplete="off"
                                        />
                                        <span
                                            v-if="form.errors.razon_social_acta"
                                            class="text-red-500 text-sm"
                                            >{{
                                                form.errors.razon_social_acta
                                            }}</span
                                        >
                                        <div
                                            v-show="filteredTecnico.length > 0"
                                            class="w-full z-50 max-h-40 border border-gray-300 rounded-lg absolute overflow-auto text-sm divide-y divide-gray-200 bg-white"
                                        >
                                            <div
                                                v-for="(
                                                    tecnico, index
                                                ) in filteredTecnico"
                                                :key="tecnico.id"
                                                :class="[
                                                    'p-2 cursor-pointer dark:bg-gray-700',
                                                    {
                                                        'bg-gray-200 dark:bg-gray-800':
                                                            index ===
                                                            selectedTecnico,
                                                    },
                                                ]"
                                                @click="selectTecnico(tecnico)"
                                                @mouseover="
                                                    updateSelectionTecnico(
                                                        index
                                                    )
                                                "
                                            >
                                                {{ tecnico.name }}
                                                {{ tecnico.apellidopat }}
                                                {{ tecnico.apellidomat }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex flex-col gap-y-1.5 md:gap-y-1 w-full"
                                >
                                    <label
                                        for="cliente"
                                        class="font-extrabold text-md"
                                        >Descripcion de Acta</label
                                    >
                                    <Textarea
                                        id="trabajo_conformidad"
                                        v-model="form.trabajo_conformidad"
                                        autoResize
                                        rows="5"
                                        cols="80"
                                    />
                                    <span
                                        v-if="form.errors.trabajo_conformidad"
                                        class="text-red-500 text-sm"
                                        >{{
                                            form.errors.trabajo_conformidad
                                        }}</span
                                    >
                                </div>

                                <!-- <div
                                    class="flex flex-col gap-y-1.5 md:gap-y-1 w-full"
                                >
                                    <label
                                        for="cliente"
                                        class="font-extrabold text-md"
                                        >Vista Previa (Descripcion)</label
                                    >
                                    <Textarea
                                        v-if="form.trabajo_conformidad"
                                        id="preview_trabajo_conformidad"
                                        autoResize
                                        rows="5"
                                        cols="80"
                                    >
                                        {{ form.trabajo_conformidad }}</Textarea
                                    >
                                    <Textarea
                                        v-else
                                        id="preview_trabajo_conformidad"
                                        autoResize
                                        rows="5"
                                        cols="80"
                                        >{{
                                            form.trabajo_conformidad
                                        }}</Textarea
                                    >
                                    <span
                                        v-if="form.errors.trabajo_conformidad"
                                        class="text-red-500 text-sm"
                                        >{{
                                            form.errors.trabajo_conformidad
                                        }}</span
                                    >
                                </div> -->

                            </div>
                            <div
                                class="md:mt-28 flex flex-col md:flex-row w-full justify-end items-end gap-x-4 gap-y-2.5"
                            >
                                <button
                                    type="button"
                                    @click="previsualizarActaConformidad"
                                    class="px-4 py-2 text-white font-medium tracking-wider text-xs uppercase rounded-md bg-orange-600 hover:bg-orange-700 focus:outline-none focus:shadow-outline w-full md:w-auto"
                                >
                                    Previsualizar pdf de Acta
                                </button>
                                <button
                                    type="submit"
                                    class="px-4 py-2 text-white font-medium tracking-wider text-xs uppercase rounded-md bg-blue-700 hover:bg-blue-800 focus:outline-none focus:shadow-outline w-full md:w-auto"
                                >
                                    Registrar Acta
                                </button>
                                <Link
                                    type="button"
                                    :href="route('actasConformidad.index')"
                                    class="px-4 py-2 flex justify-center items-center text-white font-medium tracking-wider text-xs uppercase rounded-md bg-red-600 hover:bg-red-700 focus:outline-none focus:shadow-outline w-full md:w-auto"
                                >
                                    Cancelar
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.no-spinners::-webkit-inner-spin-button,
.no-spinners::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.p-select-list-container {
    width: 10px;
}
</style>
