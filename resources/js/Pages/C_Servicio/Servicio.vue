<!-- <script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import jsPDF from "jspdf";
import "jspdf-autotable";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import TextPrueba from "@/Components/TextPrueba.vue";
import InputError from "@/Components/InputError.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, defineProps, onMounted } from "vue";
import FileInput from "@/Components/FileInput.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    hServicio: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    n_servicio: props.hServicio.hservicio_n_servicio,
    hmarca_id: props.hServicio.hmarca_id,
    lugar_servicio: "",
    instrumento: props.hServicio.instrumento,
    rango: props.hServicio.rango,
    medida_bastago: props.hServicio.medida_bastago,
    medida_bastago2: props.hServicio.medida_bastago2,
    codigo: props.hServicio.codigo,
    material: props.hServicio.material,
    modelo: props.hServicio.modelo,
    serie: props.hServicio.serie,
    division: props.hServicio.division,
    medida_division: props.hServicio.medida_division,
    capacidad: props.hServicio.capacidad,
    medida_capacidad: props.hServicio.medida_capacidad,
    cliente_id: props.hServicio.razonSocial,
    plataforma: props.hServicio.plataforma,
    medida_plataforma: props.hServicio.medida_plataforma,
    fecha: props.hServicio.hservicio_fecha,
    fecha_final: "",
    tecnico: props.hServicio.tecnico,
    requiere: props.hServicio.requiere,
    diagnostico: props.hServicio.diagnostico,
    trabajos: props.hServicio.trabajos,
    foto: "",
    foto2: "",
});

const imagePreview1 = ref("");
const imagePreview2 = ref("");
const imagePreviews = ref(["", "", ""]);

const onSelectFoto = (e, fieldName) => {
    const files = e.target.files;
    if (files.length) {
        const reader = new FileReader();
        reader.onload = (e) => {
            switch (fieldName) {
                case "foto":
                    imagePreview1.value = e.target.result;
                    imagePreviews.value[0] = e.target.result;
                    break;
                case "foto2":
                    imagePreview2.value = e.target.result;
                    imagePreviews.value[1] = e.target.result;
                    break;
                default:
                    break;
            }
        };
        reader.readAsDataURL(files[0]);

        form[fieldName] = files[0];
    }
};

const setCurrentDate = () => {
    const today = new Date()
        .toLocaleDateString("en-CA", { timeZone: "America/Lima" })
        .split("/")
        .reverse()
        .join("-");
    form.fecha_final = today;
};

onMounted(() => {
    setCurrentDate();
});

// const previewPDF2 = () => {
//     // ========== Inicia Construción de PDF ==========

//     let doc = new jsPDF('portrait');

//     function fn_dibujarEncabezado(texto){
//         const anchoTexto = doc.getTextWidth(texto);
//         const eje_x_left = anchoPagina - anchoTexto - margenDerecho;
//         doc.text(eje_x_left, eje_y, texto);
//     }

//     let eje_y = 10;
//     let eje_x = 10;
//     let margenDerecho = 10;
//     let anchoPagina = doc.internal.pageSize.width;

//     doc.setTextColor(0,0,0);
//     doc.setFontSize(9);
//     doc.setFont('Helvetica', 'normal');

//     const backgroundImg = '/img/plantilla.png';
//     doc.addImage(backgroundImg, 'JPEG',1, 1, 208, 295);

//     eje_y += 5;

//     fn_dibujarEncabezado("Av. Separadora Mz A LT 8 Sector 28 de Julio");
//     eje_y += 5;
//     fn_dibujarEncabezado("Telf: 955571986 - 924808237 - 934094721");
//     eje_y += 5;
//     fn_dibujarEncabezado("Correo: industriasbalinsa@gmail.com");
//     eje_y += 5;
//     fn_dibujarEncabezado("www.balinsa.com");
//     eje_y += 5;
//     fn_dibujarEncabezado("RUC: 20608165585");

//     // eje_y += 10;

//     // doc.setFontSize(12);
//     // doc.setFont('Helvetica', 'bold');
//     // fn_dibujarEncabezado(`COTIZACION : N° ${añoCotizacion} - ${numeroCotizacionFormateado}`);
//     // doc.text(eje_x, eje_y, fechaFormateada);

//     // const inicioTabla = 50;

//     // fn_dibujarDatosClientes(inicioTabla);

//     // doc.setFontSize(8);
//     // doc.setFont('Helvetica', 'bold');
//     // fn_dibujarProductos();

//     // fn_dibujarCondiciones();

//     // doc.setFont('Helvetica', 'normal');
//     // fn_dibujarCuentas();

//     // ========== Finaliza Construción de PDF ==========

//     const blob = doc.output('blob');
//     const url = URL.createObjectURL(blob);
//     window.open(url);
// }

// const previewPDF2 = () => {
//     let doc = new jsPDF('portrait');

//     let eje_y = 10;
//     let eje_x = 10;
//     let margenDerecho = 10;
//     let anchoPagina = doc.internal.pageSize.width;

//     doc.setTextColor(0, 0, 0);
//     doc.setFontSize(9);
//     doc.setFont('Helvetica', 'normal');

//     const backgroundImg = '/img/plantilla.png';
//     doc.addImage(backgroundImg, 'JPEG', 1, 1, 208, 295);

//     function fn_dibujarEncabezado(texto) {
//         const anchoTexto = doc.getTextWidth(texto);
//         const eje_x_left = anchoPagina - anchoTexto - margenDerecho;
//         doc.text(eje_x_left, eje_y, texto);
//     }

//     eje_y += 5;
//     fn_dibujarEncabezado("Av. Separadora Mz A LT 8 Sector 28 de Julio");
//     eje_y += 5;
//     fn_dibujarEncabezado("Telf: 955571986 - 924808237 - 934094721");
//     eje_y += 5;
//     fn_dibujarEncabezado("Correo: industriasbalinsa@gmail.com");
//     eje_y += 5;
//     fn_dibujarEncabezado("www.balinsa.com");
//     eje_y += 5;
//     fn_dibujarEncabezado("RUC: 20608165585");

//     // ========== Nueva Página - Contenido del Reporte ==========
//     doc.addPage();

//     // const backgroundImg = '/img/plantilla.png';
//     // doc.addImage(backgroundImg, 'JPEG', 1, 1, 208, 295);

//     function fn_dibujarEncabezado(texto) {
//         const anchoTexto = doc.getTextWidth(texto);
//         const eje_x_left = anchoPagina - anchoTexto - margenDerecho;
//         doc.text(eje_x_left, eje_y, texto);
//     }

//     eje_y += 5;
//     fn_dibujarEncabezado("Av. Separadora Mz A LT 8 Sector 28 de Julio");
//     eje_y += 5;
//     fn_dibujarEncabezado("Telf: 955571986 - 924808237 - 934094721");
//     eje_y += 5;
//     fn_dibujarEncabezado("Correo: industriasbalinsa@gmail.com");
//     eje_y += 5;
//     fn_dibujarEncabezado("www.balinsa.com");
//     eje_y += 5;
//     fn_dibujarEncabezado("RUC: 20608165585");

//     doc.setFontSize(10);
//     eje_y += 10;
//     doc.text(`CLIENTE: INDUSTRIAL PESQUERA SANTA MONICA S.A.`, eje_x, eje_y);
//     eje_y += 7;
//     doc.text(`MARCA: precix weight`, eje_x, eje_y);
//     doc.text(`MODELO: super 6`, 120, eje_y);
//     eje_y += 7;
//     doc.text(`SERIE: 149645`, eje_x, eje_y);
//     doc.text(`CAPACIDAD: 30 Kg`, 120, eje_y);
//     eje_y += 7;
//     doc.text(`DIVISION: 5 g`, eje_x, eje_y);
//     doc.text(`PLATAFORMA: 23X19CM`, 120, eje_y);
//     eje_y += 7;
//     doc.text(`INSTRUMENTO: Balanzas`, eje_x, eje_y);
//     doc.text(`REQUISITO: CALIBRACION TOTAL WEIGHT`, 120, eje_y);
//     eje_y += 7;
//     doc.text(`FECHA INICIO: 26/12/2024`, eje_x, eje_y);
//     doc.text(`FECHA FINAL: 07/05/2025`, 120, eje_y);
//     eje_y += 7;
//     doc.text(`TÉCNICO ASIGNADO: Deyvis`, eje_x, eje_y);

//     eje_y += 10;
//     doc.setFont(undefined, 'bold');
//     doc.text("DIAGNOSTICO TECNICO:", eje_x, eje_y);
//     doc.setFont(undefined, 'normal');
//     eje_y += 6;
//     let diagnostico = `ENCIENDE EN AC
// ENCIENDE EN DC
// LINEALIDAD OK
// REPETIBILIDAD OK
// EXCENTRICIDAD OK`;  // Puedes obtener esto desde un campo dinámico
//     doc.text(diagnostico, eje_x, eje_y);
//     eje_y += 30;

//     doc.setFont(undefined, 'bold');
//     doc.text("TRABAJOS A REALIZAR:", eje_x, eje_y);
//     doc.setFont(undefined, 'normal');
//     eje_y += 6;
//     let trabajos = `AJUSTE Y CALIBRACION
// LIMPIEZA Y MANTENIMIENTO GENERAL`;  // También debe obtenerse dinámicamente
//     doc.text(trabajos, eje_x, eje_y);

//     // ========== Finaliza PDF ==========
//     const blob = doc.output('blob');
//     const url = URL.createObjectURL(blob);
//     window.open(url);
// }

const previewPDF2 = () => {
    let doc = new jsPDF("portrait");
    const marginLeft = 10;
    let yAxis = 20;

    // Helper function for right-aligned text
    const drawRightText = (text) => {
        const textWidth = doc.getTextWidth(text);
        doc.text(doc.internal.pageSize.width - textWidth - 10, yAxis, text);
    };

    // Add background template
    // doc.addImage("/img/template.png", "JPEG", 1, 1, 208, 295);

    // ======== Header Section ========
    yAxis = 10;
    drawRightText("Av. Separadora Mz A LT 8 Sector 28 de Julio");
    yAxis += 5;
    drawRightText("Telf: 955571986 - 924808237 - 934094721");
    yAxis += 5;
    drawRightText("Correo: industriasbalinsa@gmail.com");
    yAxis += 5;
    drawRightText("RUC: 20608165585");
    yAxis += 15;

    // ======== Main Content ========
    doc.setFontSize(12);
    doc.setFont("Helvetica", "bold");
    doc.text(marginLeft, yAxis, "DATOS GENERALES");
    yAxis += 10;

    // General Data Table
    doc.setFontSize(10);
    doc.setFont("Helvetica", "normal");
    doc.text(marginLeft, yAxis, `Asesor Responsable: Branddock Trelles`);
    doc.text(
        marginLeft + 70,
        yAxis,
        `Fecha de Registro: Lunes 21 de Abril del 2025`
    );
    yAxis += 7;
    doc.text(marginLeft, yAxis, `Cliente: ${form.cliente_id}`);
    yAxis += 15;

    // Equipment Data
    doc.setFont("Helvetica", "bold");
    doc.text(marginLeft, yAxis, `DATOS DEL EQUIPO : ${form.instrumento}`);
    yAxis += 10;

    const equipmentData = [
        { label: "MARCA", value: form.hmarca_id },
        { label: "MODELO", value: form.modelo },
        { label: "SERIE", value: form.serie },
        {
            label: "DIVISION",
            value: `${form.division} ${form.medida_division}`,
        },
        {
            label: "CAPACIDAD",
            value: `${form.capacidad} ${form.medida_capacidad}`,
        },
        {
            label: "PLATAFORMA",
            value: `${form.plataforma} ${form.medida_plataforma}`,
        },
        { label: "REQUIERE", value: form.requiere },
    ];

    equipmentData.forEach((item) => {
        doc.text(marginLeft, yAxis, `${item.label}:`);
        doc.text(marginLeft + 30, yAxis, item.value);
        yAxis += 7;
    });
    yAxis += 10;

    // Technical Diagnosis
    doc.setFont("Helvetica", "bold");
    doc.text(marginLeft, yAxis, "DIAGNOSTICO DEL TECNICO :");
    yAxis += 7;
    doc.setFont("Helvetica", "normal");
    form.diagnostico.split("\n").forEach((line) => {
        doc.text(marginLeft + 5, yAxis, `• ${line}`);
        yAxis += 7;
    });

    // Trabajos a Realizar
    doc.setFont("Helvetica", "bold");
    doc.text(marginLeft, yAxis, "TRABAJOS A REALIZAR :");
    yAxis += 7;
    doc.setFont("Helvetica", "normal");
    form.trabajos.split("\n").forEach((line) => {
        doc.text(marginLeft + 5, yAxis, `• ${line}`);
        yAxis += 7;
    });
    // [
    //     "Residuos de aceite en indicador",
    //     "Empalmes en mal estado",
    //     "Batería OK",
    // ].forEach((line) => {
    //     doc.text(marginLeft + 5, yAxis, `• ${line}`);
    //     yAxis += 7;
    // });
    yAxis += 10;

    // Bank Details Table
    doc.setFont("Helvetica", "bold");
    doc.text(marginLeft, yAxis, "DATOS BANCARIOS:");
    yAxis += 10;

    doc.autoTable({
        startY: yAxis,
        head: [["Banco", "Moneda", "Tipo Cuenta", "Cuenta", "CCI"]],
        body: [
            [
                "BCP",
                "SOLES",
                "AHORROS",
                "4752156367062",
                "00247500215636706225",
            ],
            [
                "BBVA",
                "DOLARES",
                "AHORROS",
                "0011-0267-0201320324",
                "01126700020132032421",
            ],
        ],
        theme: "grid",
        styles: { fontSize: 8 },
    });

    // Total Price
    yAxis = doc.lastAutoTable.finalY + 10;
    doc.setFont("Helvetica", "bold");
    doc.text(marginLeft, yAxis, `PRECIO TOTAL: S/ 985.00+IGV`);

    // ======== Generate PDF ========
    const blob = doc.output("blob");
    window.open(URL.createObjectURL(blob));
};

$(document).on("change", "#instrumento", function () {
    let instrumento = $("#instrumento").val();
    actualizarVisibilidadIns(instrumento);
});

function actualizarVisibilidadIns(instrumento) {
    if (instrumento == "1") {
        $("#divMarca").show();
        $("#divModelo").show();
        $("#divSerie").show();
        $("#divDivision").show();
        $("#divCapacidad").show();
        $("#divPlataforma").show();
        $("#divRango").hide();
        $("#divMedidaBastago").hide();
        $("#divCodigo").hide();
        $("#divMaterial").hide();
    } else if (instrumento == "2") {
        $("#divMarca").show();
        $("#divModelo").show();
        $("#divSerie").show();
        $("#divDivision").show();
        $("#divCapacidad").hide();
        $("#divPlataforma").hide();
        $("#divRango").show();
        $("#divMedidaBastago").show();
        $("#divCodigo").hide();
        $("#divMaterial").hide();
    } else if (instrumento == "3") {
        $("#divMarca").hide();
        $("#divModelo").show();
        $("#divSerie").hide();
        $("#divDivision").hide();
        $("#divCapacidad").show();
        $("#divPlataforma").hide();
        $("#divRango").hide();
        $("#divMedidaBastago").hide();
        $("#divCodigo").show();
        $("#divMaterial").show();
    }
}
</script> -->

<!-- <template>
    <AppLayout title="Requerimientos de Servicio">
        <template #header>

            <h1
                class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white"
            >
                Generar Cotizacion por Servicio
            </h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-sm shadow-lg bg-white dark:bg-gray-800 rounded-lg"
                >
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">

                        <div
                            class="flex flex-wrap gap-2 uppercase text-sm justify-between my-2 mb-8"
                        >
                            <Link
                                :href="route('balanza')"
                                class="text-white bg-indigo-700 font-bold hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center"
                            >
                                <i class="fas fa-arrow-left mx-2"></i>Volver a Informes
                            </Link>
                        </div>

                        <form @submit.prevent="submitForm">
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 md:py-0 py-2 gap-y-2 gap-6"
                            >
                                <div id="divClienteId">
                                    <InputLabel
                                        for="cliente_id"
                                        value="Cliente"
                                        class="text-xs"
                                    />
                                    <TextInput
                                        disabled
                                        v-model="form.cliente_id"
                                        type="text"
                                        id="cliente_id"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    />
                                    <InputError
                                        :message="form.errors.cliente_id"
                                        class="mt-2"
                                    ></InputError>
                                </div>
                                <div>
                                    <InputLabel
                                        for="lugar_servicio"
                                        value="Lugar de Servicio"
                                        class="text-xs"
                                    />
                                    <TextInput
                                        v-model="form.lugar_servicio"
                                        type="text"
                                        id="lugar_servicio"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    />
                                    <InputError
                                        :message="form.errors.lugar_servicio"
                                        class="mt-2"
                                    ></InputError>
                                </div>
                                <div>
                                    <InputLabel
                                        for="instrumento"
                                        class="block text-xs font-medium text-black dark:text-white"
                                        >Instrumento</InputLabel
                                    >
                                    <select
                                        disabled
                                        id="instrumento"
                                        v-model="form.instrumento"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md text-black"
                                    >
                                        <option value="1" selected>
                                            Balanzas
                                        </option>
                                        <option value="2">Termometros</option>
                                        <option value="3">Pesas</option>
                                    </select>
                                </div>
                            </div>
                            <div
                                class="grid grid-cols-1 gap-y-2 sm:grid-cols-2 lg:grid-cols-4 sm:gap-x-6 mb-3 py-2"
                            >
                                <div id="divTecnicoResp">
                                    <InputLabel for="tecnico" value="Tecnico" />
                                    <TextInput
                                        disabled
                                        v-model="form.tecnico"
                                        type="text"
                                        id="tecnico"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    />
                                    <InputError
                                        :message="form.errors.tecnico"
                                        class="mt-2"
                                    ></InputError>
                                </div>
                                <div id="divMarca">
                                    <InputLabel for="hmarca_id" value="marca" />
                                    <TextInput
                                        disabled
                                        v-model="form.hmarca_id"
                                        type="text"
                                        id="hmarca_id"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    />
                                    <InputError
                                        :message="form.errors.hmarca_id"
                                        class="mt-2"
                                    ></InputError>
                                </div>
                                <div id="divModelo">
                                    <InputLabel for="modelo" value="modelo" />
                                    <TextInput
                                        disabled
                                        v-model="form.modelo"
                                        type="text"
                                        id="modelo"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    />
                                    <InputError
                                        :message="form.errors.modelo"
                                        class="mt-2"
                                    ></InputError>
                                </div>
                                <div id="divCodigo" class="hidden">
                                    <InputLabel
                                        for="codigo"
                                        value="codigo"
                                        class="block text-md font-medium text-gray-700"
                                    />
                                    <TextInput
                                        disabled
                                        v-model="form.codigo"
                                        type="text"
                                        id="codigo"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    />
                                    <InputError
                                        :message="form.errors.codigo"
                                        class="mt-2"
                                    ></InputError>
                                </div>
                                <div id="divSerie">
                                    <InputLabel
                                        for="serie"
                                        value="serie"
                                        class="block text-md font-medium text-gray-700"
                                    />
                                    <TextInput
                                        disabled
                                        v-model="form.serie"
                                        type="text"
                                        id="serie"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    />
                                    <InputError
                                        :message="form.errors.serie"
                                        class="mt-2"
                                    ></InputError>
                                </div>
                                <div id="divRango" class="hidden">
                                    <InputLabel
                                        for="rango"
                                        value="rango"
                                        class="block text-md font-medium text-gray-700"
                                    />
                                    <TextInput
                                        disabled
                                        v-model="form.rango"
                                        type="text"
                                        id="rango"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    />
                                    <InputError
                                        :message="form.errors.rango"
                                        class="mt-2"
                                    ></InputError>
                                </div>
                                <div id="divMedidaBastago" class="hidden">
                                    <InputLabel
                                        for="medida_bastago"
                                        value="medida de bastago"
                                        class="block text-md font-medium text-gray-700"
                                    />
                                    <div class="flex">
                                        <TextPrueba
                                            v-model="form.medida_bastago"
                                            type="text"
                                            id="medida_bastago"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg"
                                        />
                                        <InputError
                                            :message="
                                                form.errors.medida_bastago
                                            "
                                            class="mt-2"
                                        ></InputError>
                                        <input
                                            id="medida_bastago2"
                                            disabled
                                            v-model="form.medida_bastago2"
                                            class="mt-1 text-center focus:ring-indigo-500 focus:border-indigo-500 block w-16 cursor-pointer tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-r-lg"
                                        />
                                    </div>
                                </div>
                                <div id="divDivision">
                                    <InputLabel for="div" value="div" />
                                    <div class="flex justify-center">
                                        <TextPrueba
                                            v-model="form.division"
                                            type="text"
                                            id="division"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg text-black"
                                        />
                                        <InputError
                                            :message="form.errors.division"
                                            class="mt-2"
                                        ></InputError>
                                        <select
                                            disabled
                                            id="medida_division"
                                            v-model="form.medida_division"
                                            required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-24 cursor-pointer tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-r-lg text-black"
                                        >
                                            <option selected value="Kg">
                                                Kg
                                            </option>
                                            <option value="g">g</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="divCapacidad">
                                    <InputLabel
                                        for="capacidad"
                                        value="capacidad"
                                    />
                                    <div class="flex justify-center">
                                        <TextPrueba
                                            v-model="form.capacidad"
                                            type="text"
                                            id="capacidad"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg text-black"
                                        />
                                        <InputError
                                            :message="form.errors.capacidad"
                                            class="mt-2"
                                        ></InputError>
                                        <select
                                            disabled
                                            id="medida_capacidad"
                                            v-model="form.medida_capacidad"
                                            required
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-24 cursor-pointer tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-r-lg text-black"
                                        >
                                            <option selected value="Kg">
                                                Kg
                                            </option>
                                            <option value="g">g</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="divMaterial" class="hidden">
                                    <InputLabel
                                        for="material"
                                        value="material"
                                    />
                                    <TextInput
                                        disabled
                                        v-model="form.material"
                                        type="text"
                                        id="material"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    />
                                    <InputError
                                        :message="form.errors.material"
                                        class="mt-2"
                                    ></InputError>
                                </div>
                                <div id="divPlataforma">
                                    <InputLabel
                                        for="plataforma"
                                        value="plataforma"
                                    />
                                    <div
                                        class="flex justify-center items-center"
                                    >
                                        <TextPrueba
                                            v-model="form.plataforma"
                                            type="text"
                                            id="plataforma"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-l-lg text-black"
                                        />
                                        <InputError
                                            :message="form.errors.plataforma"
                                            class="mt-2"
                                        ></InputError>
                                        <input
                                            id="medida_plataforma"
                                            disabled
                                            v-model="form.medida_plataforma"
                                            class="mt-1 text-center focus:ring-indigo-500 focus:border-indigo-500 block w-16 cursor-pointer tracking-widest shadow-sm sm:text-sm border-gray-300 rounded-r-lg text-black"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <InputLabel
                                        for="requiere"
                                        value="requiere"
                                    />
                                    <select
                                        disabled
                                        id="requiere"
                                        v-model="form.requiere"
                                        required
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md text-black"
                                    >
                                        <option value="">
                                            Selecciona una opcion
                                        </option>
                                        <option value="DIAGNOSTICO">
                                            DIAGNOSTICO
                                        </option>
                                        <option
                                            value="MANTENIMIENTO PREVENTIVO"
                                        >
                                            MANTENIMIENTO PREVENTIVO
                                        </option>
                                        <option
                                            value="MANTENIMIENTO CORRECTIVO"
                                        >
                                            MANTENIMIENTO CORRECTIVO
                                        </option>
                                        <option value="CALIBRACION BALINSA">
                                            CALIBRACION BALINSA
                                        </option>
                                        <option
                                            value="CALIBRACION TOTAL WEIGHT"
                                        >
                                            CALIBRACION TOTAL WEIGHT
                                        </option>
                                        <option value="GARANTIA">
                                            GARANTIA
                                        </option>
                                        <option value="INOPERATIVA">
                                            INOPERATIVA
                                        </option>
                                    </select>
                                    <InputError
                                        :message="
                                            $page.props.errors.descripcion
                                        "
                                        class=""
                                    />
                                </div>
                                <div>
                                    <InputLabel
                                        for="fecha"
                                        value="Fecha Inicio"
                                        class="block text-md font-medium text-gray-700"
                                    />
                                    <TextInput
                                        disabled
                                        v-model="form.fecha"
                                        type="date"
                                        id="fecha"
                                        :disabled="
                                            !$page.props.user.permissions.includes(
                                                'Acciones Administrador'
                                            )
                                        "
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md text-black"
                                    />
                                    <InputError
                                        :message="form.errors.fecha"
                                        class="mt-2"
                                    ></InputError>
                                </div>
                                <div>
                                    <InputLabel
                                        for="fecha_final"
                                        value="Fecha Final"
                                        class="block text-md font-medium text-gray-700"
                                    />
                                    <TextInput
                                        disabled
                                        v-model="form.fecha_final"
                                        type="date"
                                        id="fecha_final"
                                        :disabled="
                                            !$page.props.user.permissions.includes(
                                                'Acciones Administrador'
                                            )
                                        "
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md text-black"
                                    />
                                    <InputError
                                        :message="form.errors.fecha_final"
                                        class="mt-2"
                                    ></InputError>
                                </div>
                            </div>
                            <div
                                class="grid grid-cols-1 gap-y-3 sm:grid-cols-2 sm:gap-x-6 mb-3"
                            >
                                <div>
                                    <InputLabel
                                        for="diagnostico"
                                        value="diagnostico tecnico"
                                    />
                                    <textarea
                                        id="diagnostico"
                                        v-model="form.diagnostico"
                                        rows="4"
                                        required
                                        class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Escriba las diagnostico..."
                                    ></textarea>
                                    <InputError
                                        :message="form.errors.diagnostico"
                                        class="mt-2"
                                    ></InputError>
                                </div>
                                <div>
                                    <InputLabel
                                        for="trabajos"
                                        value="trabajos a realizar"
                                    />
                                    <textarea
                                        id="trabajos"
                                        v-model="form.trabajos"
                                        rows="4"
                                        required
                                        class="mt-1 block p-2.5 w-full text-base text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-300 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Escriba las trabajos..."
                                    ></textarea>
                                    <InputError
                                        :message="form.errors.trabajos"
                                        class="mt-2"
                                    ></InputError>
                                </div>
                            </div>
                            <div>
                                <div
                                    class="mt-0 flex justify-center items-center flex-wrap gap-y-0 sm:gap-x-2"
                                >
                                    <div
                                        class="sm:col-span-1 flex-1 whitespace-nowrap"
                                    >
                                        <div
                                            class="flex flex-wrap gap-4 items-center mb-4 mt-4"
                                        >
                                            <InputLabel
                                                for="foto"
                                                value="Imagen Antes"
                                                class="block text-xs font-medium text-gray-700"
                                            />
                                            <FileInput
                                                id="foto"
                                                name="foto"
                                                @change="
                                                    ($event) =>
                                                        onSelectFoto(
                                                            $event,
                                                            'foto'
                                                        )
                                                "
                                            />
                                            <InputError
                                                :message="
                                                    $page.props.errors.foto
                                                "
                                                class="mt-2"
                                            />
                                            <div
                                                class="mt-2 flex items-center justify-center w-full"
                                                v-if="form.foto !== ''"
                                            >
                                                <img
                                                    :src="imagePreview1"
                                                    alt="Vista previa de la foto"
                                                    class="p-2 block w-36 h-36 object-contain items-center text-sm text-gray-900 border border-gray-200 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="sm:col-span-1 flex-1 whitespace-nowrap"
                                    >
                                        <div
                                            class="flex flex-wrap gap-4 items-center mb-4 mt-4"
                                        >
                                            <InputLabel
                                                for="foto2"
                                                value="Imagen Despues"
                                                class="block text-xs font-medium text-gray-700"
                                            />
                                            <FileInput
                                                id="foto2"
                                                name="foto2"
                                                @change="
                                                    ($event) =>
                                                        onSelectFoto(
                                                            $event,
                                                            'foto2'
                                                        )
                                                "
                                            />
                                            <InputError
                                                :message="
                                                    $page.props.errors.foto2
                                                "
                                                class="mt-2"
                                            />
                                            <div
                                                class="mt-2 flex items-center justify-center w-full"
                                                v-if="form.foto2 !== ''"
                                            >
                                                <img
                                                    :src="imagePreview2"
                                                    alt="Vista previa de la foto 2"
                                                    class="p-2 block w-36 h-36 items-center object-contain text-sm text-gray-900 border border-gray-200 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-4">
                                <div
                                    class="flex flex-wrap gap-x-4 gap-y-4 justify-end"
                                >
                                    <button
                                        class="inline-block bg-green-700 text-xs text-white font-bold py-2 px-4 rounded hover:bg-green-800 md:w-min whitespace-nowrap w-full text-center"
                                        @click.prevent="previewPDF2"
                                    >
                                        PREVISUALIZAR PDF
                                    </button>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template> -->

<!-- <ButtonResponsive v-if="isEditing" id="crearNuevaHojaServicio" class="uppercase text-xs bg-red-600 hover:bg-red-700">Nuevo Informe Tecnico</ButtonResponsive>
                                    <button @click.prevent="previsualizarpdf" class="inline-block bg-green-700 text-white font-bold py-2 px-4 rounded hover:bg-green-800 md:w-min whitespace-nowrap w-full text-center uppercase text-xs">Previsualizar Informe Tecnico</button>
                                    <ButtonResponsive id="btnguardarHojaServicio" v-if="!isEditing" class="uppercase text-xs">Guadar Informe Tecnico</ButtonResponsive>
                                    <button
                                        id="loading-button"
                                        disabled
                                        type="button"
                                        class="text-white whitespace-nowrap md:w-min w-full bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 rounded-lg text-sm px-5 py-2 justify-center text-center font-extrabold dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 hidden">
                                        <svg aria-hidden="true" role="status" class="inline w-4 mt-[3px] h-4 me-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                                        </svg>
                                        Cargando...
                                    </button>
                                    <ButtonResponsive id="btnactualizarHojaServicio" v-if="isEditing" class="uppercase text-xs">Actualizar Informe Tecnico</ButtonResponsive> -->

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, computed, watch, nextTick } from "vue";
import { jsPDF } from "jspdf";
import "jspdf-autotable";
import { Link, usePage } from "@inertiajs/vue3";
import { onMounted, onUnmounted } from "vue";

const page = usePage();

const props = defineProps({
    hServicio: {
        type: Object,
        required: true,
    },
    cliente: {
        type: Object,
        required: true,
    },
    asesor: {
        type: Object,
        required: true,
    },
    plantillas: {
        type: Object,
        required: true,
    },
});

const clienteData = ref(props.cliente || []);
const instruments = ref(props.hServicio || []);
const advisor = ref(props.asesor || []);
const plantillasPDF = ref(props.plantillas || []);
const showPreview = ref(false);
const quoteNumber = ref(Math.floor(10000 + Math.random() * 90000));
const currentInstrumentIndex = ref(0);
const workText = ref("");
const precios = ref([]);
const moneda = ref("S/");
const showImageModal = ref(false);
const modalImageIndex = ref(0);
const currentInstrumentImages = ref([]);
const selectedPlantilla = ref(1);
const validez = ref("7 días");
const forma_pago = ref("Al contado");

const todosDelMismoTipo = computed(() => {
    if (instruments.value.length === 0) return false;

    const tipoReferencia = instruments.value[0].instrumento;
    return instruments.value.every((i) => i.instrumento === tipoReferencia);
});

// const todosDelMismoTipo = () => {
//     if (instruments.value.length === 0) return false;

//     const tipoReferencia = instruments.value[0].instrumento;
//     return instruments.value.every(i => i.instrumento === tipoReferencia);
// };

console.log("todosDelMismoTipo", todosDelMismoTipo.value);

const isCarouselActive = computed(() => instruments.value.length > 0);

const handleKeyNavigation = (e) => {
    if (!isCarouselActive.value) return;

    if (e.key === "ArrowRight") nextInstrument();
    else if (e.key === "ArrowLeft") prevInstrument();
};

onMounted(() => {
    window.addEventListener("keydown", handleKeyNavigation);
});

onUnmounted(() => {
    window.removeEventListener("keydown", handleKeyNavigation);
});

const typeInstrument = ref([
    { id: 1, name: "Balanza" },
    { id: 2, name: "Termometro" },
    { id: 3, name: "Pesa" },
]);

const instrumentFields = [
    { name: "hmarca_id", label: "Marca", type: "text" },
    { name: "modelo", label: "Modelo", type: "text" },
    { name: "serie", label: "Serie", type: "text" },
    { name: "requiere", label: "Require", type: "textarea" },
];

const formattedDate = computed(() => {
    return new Date(Date.now()).toLocaleDateString("es-PE", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
});

function formatDayDate(date) {
    const formatted = new Date(date).toLocaleDateString("es-PE", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });

    return formatted.charAt(0).toUpperCase() + formatted.slice(1);
}

function formatDate(date) {
    return new Date(date).toLocaleDateString("es-PE", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
}

watch(instruments, (newVal) => {
    if (newVal.length === 0) {
        currentInstrumentIndex.value = 0;
    } else if (currentInstrumentIndex.value >= newVal.length) {
        currentInstrumentIndex.value = newVal.length - 1;
    }
});

watch(
    () => instruments.value.length,
    (newLength) => {
        precios.value = instruments.value.map((_, i) => precios.value[i] ?? 0);
    },
    { immediate: true }
);

function parseTrabajos(trabajos) {
    if (!trabajos) return [];
    return trabajos
        .split(/\r?\n|,|;/) // Puedes ajustar el delimitador según tu necesidad
        .map((t) => t.trim())
        .filter((t) => t.length > 0);
}

function useBulletView(instrument) {
    return computed({
        get() {
            if (!instrument.trabajos) return "";
            return instrument.trabajos
                .split("\n")
                .map((line) => (line.trim() ? `• ${line.trim()}` : ""))
                .join("\n");
        },
        set(val) {
            // Al guardar, quitamos las viñetas si las agregaron manualmente
            instrument.trabajos = val
                .split("\n")
                .map((line) => line.replace(/^•\s*/, "")) // quita la viñeta
                .join("\n");
        },
    });
}

function addBullet(instrument) {
    const textarea = document.activeElement;
    const value = instrument.trabajos || "";
    const selectionStart = textarea.selectionStart;
    const selectionEnd = textarea.selectionEnd;

    // Inserta la viñeta en la posición actual del cursor
    const bullet = "\n• ";
    instrument.trabajos =
        value.slice(0, selectionStart) + bullet + value.slice(selectionEnd);

    // Espera al siguiente ciclo del DOM para mover el cursor
    nextTick(() => {
        textarea.selectionStart = textarea.selectionEnd =
            selectionStart + bullet.length;
    });
}

const totalCotizacion = computed(() =>
    precios.value.reduce((acc, val) => acc + (parseFloat(val) || 0), 0)
);

// Galeria

const visibleImages = ref([true, true, true]);

function hideImage(index) {
    visibleImages.value[index] = false;
}

function getValidImagePaths(instrument) {
    return [
        instrument.foto && `/hservicio_img/${instrument.foto}`,
        instrument.foto2 && `/hservicio_img/${instrument.foto2}`,
        instrument.foto3 && `/hservicio_img/${instrument.foto3}`,
    ].filter(Boolean);
}

// Abrir modal y cargar imágenes disponibles
function openImageModal(images, index = 0) {
    currentInstrumentImages.value = images.filter(Boolean); // eliminar null/undefined
    modalImageIndex.value = index;
    showImageModal.value = true;
}

// Imagen actual mostrada
const modalImageSrc = computed(
    () => currentInstrumentImages.value[modalImageIndex.value] || ""
);

// Navegación
function prevImage() {
    if (modalImageIndex.value > 0) modalImageIndex.value--;
}

function nextImage() {
    if (modalImageIndex.value < currentInstrumentImages.value.length - 1)
        modalImageIndex.value++;
}

function closeImageModal() {
    showImageModal.value = false;
}

const trabajosBullet = instruments.value.map((instrument) =>
    useBulletView(instrument)
);

function removeInstrument(index) {
    instruments.value.splice(index, 1);
}

function prevInstrument() {
    if (currentInstrumentIndex.value > 0) {
        currentInstrumentIndex.value--;
    }
}

function nextInstrument() {
    if (currentInstrumentIndex.value < instruments.value.length - 1) {
        currentInstrumentIndex.value++;
    }
}

function goToInstrument(index) {
    currentInstrumentIndex.value = index;
}

// Seleccionar la primera plantilla por defecto
onMounted(() => {
    if (plantillasPDF.value.length) {
        selectedPlantilla.value = plantillasPDF.value[0].id;
    }
});

// console.log("plantillasPDF", plantillasPDF.value);
// console.log("selectedPlantilla", selectedPlantilla.value);
// console.log("tipo de instrumento", instruments.value)
// console.log("plantillas por tipo de instrumento", plantillasPDF.value.filter(p => p.instrumento === instruments.value[0]?.instrumento))

// const plantillasPorTipo = computed(() => {
//     return plantillasPDF.value.filter(
//         (p) => p.instrumento === instruments.value[0]?.instrumento
//     );
// });

const plantillaSeleccionada = computed(() => {
    return plantillasPDF.value.find((p) => p.id === selectedPlantilla.value);
});

function previewQuote() {
    showPreview.value = true;
}

watch(
    instruments,
    () => {
        workText.value = instruments.value
            .map((inst, i) => `\n${inst.trabajos || ""}`)
            .join("\n\n");
    },
    { immediate: true }
);

function distribuirTrabajos() {
    const bloques = workText.value.split(/\n{2,}/); // Separar por doble salto
    instruments.value.forEach((inst, i) => {
        const texto = bloques[i] || "";
        // Eliminar encabezado tipo "#1" si lo hay
        inst.trabajos = texto.replace(/^#\d+\n?/, "").trim();
    });
}
// --- INICIO DE DATOS ---

// const dataPDF = {
//     companyInfo: {
//         address: "Av. Separadora Mz A LT 8 Sector 28 de Julio",
//         phone: "Telf: 955571986-924808237-934094721",
//         email: "industriasbalinsa@gmail.com",
//         website: "www.balinsa.com",
//         ruc: "RUC: 20608165585",
//         logoUrl: "/img/logopdf.jpeg",
//     },
//     conditions: [
//         "Emitir una orden de compra a nombre de INDUSTRIAS BALINSA E.I.R.L con ruc: 20608165585",
//         "No se realizan cambios ni devoluciones",
//         "Orden de compra irrevocable",
//         "Los precios unitarios NO incluyen IGV",
//         "Validez de la Cotización: 3 dias",
//         "Forma de Pago: Al contado",
//         "Plazo de Entrega: 3",
//         "Asistencia al correo de área de ventas industriasbalinsa@gmail.com",
//         "Tipo de Cambio: 3.80",
//     ],
//     bankAccountsHead: [
//         ["Banco", "Moneda", "Tipo de Cuenta", "Cuenta", "Cuenta CCI"],
//     ],
//     bankAccountsBody: [
//         ["BCP", "SOLES", "AHORROS", "4752156367062", "00247500215636706225"],
//         ["BCP", "DOLARES", "AHORROS", "4752156380104", "00247500215638010428"],
//         [
//             "BBVA",
//             "SOLES",
//             "AHORROS",
//             "0011 0267 0201320316",
//             "011 267 000201320316 27",
//         ],
//         [
//             "BBVA",
//             "DOLARES",
//             "AHORROS",
//             "0011-0267-0201320324",
//             "01126700020132032421",
//         ],
//     ],
// footerNotes: [
//     "Posteriormente enviar ticket o certificado de deposito correspondiente a industriasbalinsa@gmail.com haciendo referencia al Nº de orden",
//     "Sin otro particular quedamos de ustedes a la espera de sus gratas ordenes.",
// ],
//     task: [
//         "Mantenimiento del instrumento",
//         "Limpieza y mantenimiento del sensor de carga",
//         "Engrase de patas",
//         "Ajuste y calibración de parámetros",
//         "Configurar de parámetros de calibración",
//         "Verificación de pulsadores",
//         "Verificación de los pesos estándares autorizados",
//         "Verificación del buen funcionamiento de los parámetros del indicador",
//         "Verificación y configuración de la excentricidad, linealidad y repetitividad del pesaje",
//         "Verificación de los voltajes respectivos de la tarjeta principal según normas del fabricante",
//         "Calibración del pesaje con pesas certificadas por organismo acreditado",
//         "Emisión de certificado emitido por laboratorio acreditado por Inacal",
//     ],
// };

// const datosPDF = JSON.parse(plantillaSeleccionada.value.informacion);

// const datosPDF = JSON.parse(
//     plantillasPDF.value.find((p) => p.id === selectedPlantilla.value)
//         .informacion
// );

const quoteInfo = {
    number: "2025-0000000248", //
    date: formatDayDate(Date.now()), //
};

const clientData = [
    //
    ["Razón Social", clienteData.value.razonSocial],
    ["RUC", clienteData.value.ruc],
    ["Direccion", clienteData.value.direccion],
];

const clientMoney = [
    [
        "Moneda",
        (moneda === "S/" ? "Soles" : "Dolares") || "",
        "Asesor",
        `${advisor.value.name} ${advisor.value.apellidopat} ${advisor.value.apellidomat}`,
    ],
];

const conditions = computed(() => [
    "Emitir una orden de compra a nombre de INDUSTRIAS BALINSA E.I.R.L con ruc: 20608165585",
    "Orden de compra irrevocable",
    "Los precios unitarios NO incluyen IGV",
    `Validez de la Cotización \t  : ${validez.value}`,
    `Forma de Pago \t\t\t: ${forma_pago.value}`,
    "Plazo de Entrega \t\t     : 3",
    "Correo \t\t\t\t     : industriasbalinsa@gmail.com",
    "Tipo de Cambio\t\t\t: 3.80",
]);


const footerNotes = [
    "Posteriormente enviar ticket o certificado de deposito correspondiente a industriasbalinsa@gmail.com haciendo referencia al Nº de orden",
    "Sin otro particular quedamos de ustedes a la espera de sus gratas ordenes.",
];

const companyInfo = {
    address: "Av. Separadora Mz A LT 8 Sector 28 de Julio", //
    phone: "Telf: 955571986-924808237-934094721", //
    email: "industriasbalinsa@gmail.com", //
    website: "www.balinsa.com", //
    ruc: "RUC: 20608165585", //
    logoUrl: "/img/logopdf.jpeg", // Ruta al logo en la carpeta public
};

const bankAccountsHead = [
    ["Banco", "Moneda", "Tipo de Cuenta", "Cuenta", "Cuenta CCI"],
]; //
const bankAccountsBody = [
    //
    ["BCP", "SOLES", "AHORROS", "4752156367062", "00247500215636706225"],
    ["BCP", "DOLARES", "AHORROS", "4752156380104", "00247500215638010428"],
    [
        "BBVA",
        "SOLES",
        "AHORROS",
        "0011 0267 0201320316",
        "011 267 000201320316 27",
    ],
    [
        "BBVA",
        "DOLARES",
        "AHORROS",
        "0011-0267-0201320324",
        "01126700020132032421",
    ],
];

const task = [
    "Mantenimiento del instrumento",
    "Limpieza y mantenimiento del sensor de carga",
    "Engrase de patas",
    "Ajuste y calibración de parámetros",
    "Configurar de parámetros de calibración",
    "Verificación de pulsadores",
    "Verificación de los pesos estándares autorizados",
    "Verificación del buen funcionamiento de los parámetros del indicador",
    "Verificación y configuración de la excentricidad, linealidad y repetitividad del pesaje",
    "Verificación de los voltajes respectivos de la tarjeta principal según normas del fabricante",
    "Calibración del pesaje con pesas certificadas por organismo acreditado",
    "Emisión de certificado emitido por laboratorio acreditado por INACAL",
];

// --- FIN DE DATOS DE EJEMPLO ---

const generatePdf = () => {
    const datosPDF = JSON.parse(plantillaSeleccionada.value.informacion);

    const doc = new jsPDF();
    let finalY = 0; // Para llevar el control de la posición Y
    const pageWidth = doc.internal.pageSize.getWidth();
    const marginRight = 10;
    const marginLeft = 10;

    // Cargar el logo
    const logo = new Image();
    logo.src = datosPDF.companyInfo.logoUrl; // Accede a la URL del logo desde la carpeta public

    logo.onload = () => {
        // ------ CABECERA ------
        // Dimensiones y posición del logo (ajústalas según el tamaño real de tu logo)
        const logoWidth = 80; // Ancho del logo en mm
        const logoHeight = 25; // Alto del logo en mm (o calcula proporcionalmente)
        const logoX = 10;
        const logoY = 10;

        const lineSpacingCompany = 4.5; // Espacio entre líneas para los datos de la empresa
        const companyDataFontSize = 9; // Tamaño de fuente uniforme para datos de la empresa

        // 1. Dibuja el Logo
        doc.addImage(logo, "JPEG", logoX, logoY, logoWidth, logoHeight);

        // 2. Datos de la empresa (a la derecha del logo, alineados a la derecha de la página)
        let currentYForCompanyInfo = logoY; // Alineado verticalmente con el inicio del logo
        doc.setFontSize(companyDataFontSize);
        doc.setFont("helvetica", "normal"); // Fuente normal para todos los datos

        const companyDetails = [
            datosPDF.companyInfo.address,
            datosPDF.companyInfo.phone,
            `Correo: ${datosPDF.companyInfo.email}`,
            datosPDF.companyInfo.website,
            datosPDF.companyInfo.ruc,
        ];

        companyDetails.forEach((detail) => {
            const textWidth = doc.getTextWidth(detail);
            doc.text(
                detail,
                pageWidth - marginRight - textWidth,
                currentYForCompanyInfo
            );
            currentYForCompanyInfo += lineSpacingCompany;
        });

        // 3. Fecha (debajo del logo)
        const dateY = logoY + logoHeight + 11; // Espacio debajo del logo
        doc.setFontSize(12); // El tamaño que especificaste
        doc.setFont("helvetica", "bold");
        doc.text(quoteInfo.date, logoX, dateY);

        // 4. Número de Cotización (a la derecha, alineado con la parte superior o como prefieras)
        //    Ajustamos la Y para que esté por ejemplo, al nivel del nombre de la empresa o del logo
        const quoteNumberY = logoY + logoHeight + 11; // Puedes ajustar esto
        doc.setFontSize(12);
        doc.setFont("helvetica", "bold");
        // Calcula el ancho del texto de la cotización para alinear a la derecha
        const quoteText = `COTIZACION: N° ${quoteInfo.number}`;
        const quoteTextWidth = doc.getTextWidth(quoteText);
        // const pageWidth = doc.internal.pageSize.getWidth();
        // const marginRight = 15;
        doc.text(
            quoteText,
            pageWidth - marginRight - quoteTextWidth,
            quoteNumberY
        );

        // Determinar `finalY` para el siguiente elemento
        // Será el mayor entre la altura del bloque de info de la empresa y la altura de la fecha
        finalY = Math.max(currentYForCompanyInfo, dateY) + 3; // +10 de margen para el siguiente bloque

        // ------ DATOS DEL CLIENTE ------
        // doc.setFontSize(11);
        // doc.setFont("helvetica", "bold");
        // doc.text("CLIENTE:", 15, finalY);

        // doc.autoTable({
        //     startY: finalY + 5,
        //     head: [],
        //     body: clientData,
        //     theme: "plain",
        //     styles: { fontSize: 11, cellPadding: 1 },
        //     columnStyles: {
        //         0: { fontStyle: "bold", cellWidth: 30 },
        //         1: { cellWidth: "auto" },
        //     },
        //     margin: { left: 15 },
        //     tableWidth: "auto",
        //     theme: "grid",
        // });

        doc.autoTable({
            body: clientData,
            rowPageBreak: "avoid",
            theme: "grid",
            styles: {
                fontSize: 10,
                cellPadding: 2,
                lineWidth: 0.3,
                lineColor: [0, 0, 0],
            },
            margin: { top: 30, left: 10, right: 10 },
            startY: finalY + 5,
            columnStyles: {
                0: {
                    cellWidth: 40,
                    fontStyle: "bold",
                },
            },
        });

        doc.autoTable({
            body: clientMoney,
            rowPageBreak: "avoid",
            theme: "grid",
            styles: {
                fontSize: 10,
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
                },
                1: {
                    cellWidth: 55,
                },
                2: {
                    cellWidth: 30,
                    fontStyle: "bold",
                },
            },
        });

        finalY = doc.lastAutoTable.finalY;

        // ... (resto del código para detalles de cotización, totales, condiciones, etc., como antes) ...
        // Asegúrate de que `finalY` se siga actualizando correctamente después de cada `autoTable`

        // ------ DETALLES DE LA COTIZACIÓN (PRODUCTOS) ------
        finalY += 10;
        doc.setFontSize(11);
        doc.setFont("helvetica", "bold");
        doc.text("DETALLE DE COTIZACIÓN:", 10, finalY);

        // doc.autoTable({
        //     startY: finalY + 5,
        //     head: productsHead,
        //     body: productsBody,
        //     theme: "striped",
        //     headStyles: {
        //         fillColor: [220, 220, 220],
        //         textColor: 20,
        //         fontStyle: "bold",
        //         halign: "center",
        //     },
        //     styles: { fontSize: 8, cellPadding: 2, halign: "center" },
        //     columnStyles: {
        //         3: { halign: "left", cellWidth: 60 },
        //     },
        //     margin: { left: 15, right: 15 },
        // });

        // finalY = doc.lastAutoTable.finalY;

        finalY += 10;

        instruments.value.forEach((instrument, index) => {
            if (finalY > 250) {
                doc.addPage();
                finalY = 20;
            }

            doc.setFontSize(10);
            doc.text(
                `${typeInstrument.value[instrument.instrumento - 1].name} #${
                    index + 1
                }`,
                14,
                finalY
            );
            finalY += 5;

            const row = [
                instrument.hmarca_id || "-",
                instrument.modelo || "-",
                `${instrument.capacidad || ""} ${
                    instrument.medida_capacidad || ""
                }`,
                `${instrument.plataforma || ""} ${
                    instrument.medida_plataforma || ""
                }`,
                `${moneda.value} ${
                    precios.value[index].toFixed(2) || "0.00"
                } + I.G.V`,
            ];

            doc.autoTable({
                startY: finalY,
                head: [
                    [
                        "Marca",
                        "Modelo",
                        "Capacidad",
                        "Plataforma",
                        "Precio Unitario",
                    ],
                ],
                body: [row],
                theme: "grid",
                styles: {
                    fontSize: 10,
                    cellPadding: 2,
                    lineWidth: 0.3,
                    lineColor: [0, 0, 0],
                },
                headStyles: {
                    fillColor: [241, 196, 15],
                    textColor: 0,
                    fontStyle: "bold",
                },
            });

            finalY = doc.lastAutoTable.finalY + 8;

            // ----- Mostrar trabajos -----
            if (finalY > 250) {
                doc.addPage();
                finalY = 20;
            }

            // const trabajosList = instrument.trabajos
            //     ? instrument.trabajos
            //           .split("\n")
            //           .filter((t) => t.trim() !== "")
            //           .map((line) => `• ${line.trim()}`)
            //           .join("\n")
            //     : "No especificado";

            let trabajosList;
            if (selectedPlantilla.value !== 1) {
                trabajosList = datosPDF.task
                    ? datosPDF.task
                          .filter((t) => t.trim() !== "")
                          .map((line) => `• ${line.trim()}`)
                          .join("\n")
                    : "No especificado";
            } else {
                trabajosList = instrument.trabajos
                    ? instrument.trabajos
                          .split("\n")
                          .filter((t) => t.trim() !== "")
                          .map((line) => `• ${line.trim()}`)
                          .join("\n")
                    : "No especificado";
            }

            doc.setFontSize(10);
            doc.setFont("helvetica", "bold");
            const worksTitle = "Trabajos a Realizar:";
            doc.text(worksTitle, marginLeft + 5, finalY + 5);

            const worksStartY = finalY + 7; // Inicio Y del contenido de condiciones
            let worksCurrentY = worksStartY + 8; // Y actual para dibujar texto de condiciones
            const worksPadding = 3; // Padding dentro del recuadro

            const trabajosLines = doc.splitTextToSize(trabajosList, 180);
            doc.setFontSize(10);
            doc.text(trabajosLines, worksPadding + 15, worksCurrentY);
            worksCurrentY += trabajosLines.length * 5 + 5;

            // Dibujar el recuadro alrededor de los trabajos
            const rectX = marginLeft;
            const rectY = finalY - 2; // Un poco antes del título para englobarlo
            const rectWidth = pageWidth - marginLeft - marginRight;
            const rectHeight =
                worksCurrentY - worksStartY + worksPadding * 2 + 5; // Calcular altura dinámicamente
            doc.setDrawColor(0); // Color del borde (negro)
            doc.rect(rectX, rectY, rectWidth, rectHeight);

            finalY = rectY + rectHeight + 5; // Actualizar finalY después del recuadro de condiciones
            finalY += 5;
        });

        if (finalY > 250) {
            doc.addPage();
            finalY = 20;
        }

        // doc.setDrawColor(200, 200, 200);
        // doc.line(30, finalY, 196, finalY);

        // ----- Total de Cotizacion -----
        finalY += 10;

        // doc.setFontSize(14);
        // doc.setTextColor(44, 62, 80);
        // doc.text(
        //     `PRECIO TOTAL: ${moneda.value} ${totalCotizacion.value.toFixed(
        //         2
        //     )} + I.G.V`,
        //     55,
        //     finalY
        // );

        // Dibuja un recuadro amarillo con padding de 15px para el total
        // const boxPadding = 15;
        const boxWidth = 120;
        const boxHeight = 22;
        // Centrar horizontalmente el recuadro en la página
        const boxX = (pageWidth - boxWidth) / 2;
        const boxY = finalY;

        // Fondo amarillo
        doc.setFillColor(255, 235, 59); // Amarillo (Material Yellow 500)
        doc.setDrawColor(241, 196, 15); // Borde amarillo oscuro
        doc.rect(boxX, boxY, boxWidth, boxHeight, "FD"); // F = fill, D = draw (borde)

        // Texto centrado dentro del recuadro
        doc.setFontSize(18);
        doc.setFont("helvetica", "bold");
        // doc.setTextColor(44, 62, 80);
        doc.setTextColor(0, 0, 0);
        doc.text(
            `PRECIO TOTAL: ${moneda.value} ${totalCotizacion.value.toFixed(
                2
            )} + I.G.V`,
            boxX + boxWidth / 2,
            boxY + boxHeight / 2 + 2, // Centrado verticalmente
            { align: "center" }
        );
        doc.setTextColor(0, 0, 0);
        finalY += boxHeight + 5;
        doc.setFont("helvetica", "normal");

        finalY += 10;

        if (finalY > 200) {
            doc.addPage();
            finalY = 20;
        }

        // ------ CONDICIONES (CON BORDE) ------
        finalY += 10;
        doc.setFontSize(12);
        doc.setFont("helvetica", "bold");
        const conditionsTitle = "CONDICIONES:";
        doc.text(conditionsTitle, marginLeft + 5, finalY + 5);

        const conditionsStartY = finalY + 7; // Inicio Y del contenido de condiciones
        let conditionsCurrentY = conditionsStartY + 8; // Y actual para dibujar texto de condiciones
        const conditionsPadding = 3; // Padding dentro del recuadro

        doc.setFont("helvetica", "normal");
        doc.setFontSize(10);
        conditions.value.forEach((condition) => {
            doc.text(
                condition,
                marginLeft + conditionsPadding + 5,
                conditionsCurrentY
            );
            conditionsCurrentY += 4;
        });

        // Dibujar el recuadro alrededor de las condiciones
        const rectX = marginLeft;
        const rectY = finalY - 2; // Un poco antes del título para englobarlo
        const rectWidth = pageWidth - marginLeft - marginRight;
        const rectHeight =
            conditionsCurrentY - conditionsStartY + conditionsPadding * 2 + 5; // Calcular altura dinámicamente
        doc.setDrawColor(0); // Color del borde (negro)
        doc.rect(rectX, rectY, rectWidth, rectHeight);

        finalY = rectY + rectHeight + 5; // Actualizar finalY después del recuadro de condiciones

        // ------ CUENTAS BANCARIAS ------
        finalY += 5;
        doc.setFontSize(10);
        doc.setFont("helvetica", "normal");
        doc.text(
            "Hacer deposito bancario a nombre de INDUSTRIAS BALINSA EIRL segun:",
            15,
            finalY
        );
        finalY += 2;

        doc.autoTable({
            startY: finalY + 3,
            head: datosPDF.bankAccountsHead,
            body: datosPDF.bankAccountsBody,
            theme: "grid",
            headStyles: {
                fillColor: [241, 196, 15],
                textColor: 0,
                fontStyle: "bold",
                halign: "center",
                fontSize: 10,
            },
            styles: {
                fontSize: 10,
                cellPadding: 1,
                lineWidth: 0.3,
                lineColor: [0, 0, 0],
                halign: "center",
            },
            margin: { left: 15, right: 15 },
        });

        finalY = doc.lastAutoTable.finalY;

        // ------ PIE DE PÁGINA ------
        finalY += 7;
        doc.setFontSize(8);
        footerNotes.forEach((note, index) => {
            doc.text(note, 15, finalY + index * 5, {
                maxWidth: 180,
                align: "justify",
            });
        });
        finalY += footerNotes.length * 5 + 2;

        doc.save(
            `CTZ-${clienteData.value.razonSocial}-${quoteInfo.number}.pdf`
        );
    };

    logo.onerror = () => {
        console.error("No se pudo cargar el logo para el PDF.");
        alert(
            "Error al cargar el logo. El PDF no se generará o se generará sin logo."
        );
        // Opcionalmente, puedes decidir generar el PDF sin el logo aquí si la carga falla.
        // Para ello, tendrías que duplicar la lógica de generación del PDF
        // pero sin la parte de `doc.addImage()` y sin estar dentro de `logo.onload`.
    };
};

function generateQuote() {
    if (instruments.value.length === 0) {
        alert("Este informe no cuenta con ningún instrumento.");
        return;
    }

    try {
        const logo = new Image();
        logo.src = "/img/plantilla3.jpeg";

        logo.onload = () => {
            const doc = new jsPDF();
            const pageWidth = doc.internal.pageSize.getWidth();

            // Add background template
            doc.addImage(logo, "JPEG", 1, 1, 208, 295);

            // Datos generales
            doc.setFontSize(10);
            doc.text(
                "Av. Separadora Mz A LT 8 Sector 28 de Julio",
                pageWidth - 85,
                10
            );
            doc.text(
                "Telf: 955571986 - 924808237 - 934094721",
                pageWidth - 81,
                15
            );
            doc.text("Correo: industriasbalinsa@gmail.com", pageWidth - 73, 20);
            doc.text("www.balinsa.com", pageWidth - 42, 25);
            doc.text("RUC: 20608165585", pageWidth - 46, 30);

            // Cotización y cliente
            doc.setFontSize(12);
            doc.text(`Fecha: ${formattedDate.value}`, 14, 45);
            doc.text(
                `COTIZACIÓN: N° CTZ-${quoteNumber.value}`,
                pageWidth - 70,
                45
            );

            // Tabla Datos
            doc.autoTable({
                startY: 52,
                body: clientData,
                styles: {
                    fontSize: 10,
                    cellPadding: 3,
                },
                columnStyles: {
                    0: { fontStyle: "bold", cellWidth: 30 },
                    2: { fontStyle: "bold", cellWidth: 25 },
                },
                didParseCell: (data) => {
                    // Merge columns 1 and 3 into one row visually
                    if (data.row.index === 2) {
                        data.cell.styles.halign = "left";
                    }
                },
                theme: "grid",
            });

            // Tabla
            const tableStartY = 100;
            const headers = [
                "Marca",
                "Modelo",
                "Capacidad",
                "Plataforma",
                "Cantidad",
                "P. Unitario",
                "Subtotal",
            ];
            const body = instruments.value.map((item) => [
                item.hmarca_id,
                item.modelo,
                `${item.capacidad} ${item.medida_capacidad}`,
                item.especificaciones || `${item.plataforma || ""}`,
                item.cantidad || 1,
                parseFloat(item.precio_unitario || 0).toFixed(2),
                (item.cantidad * item.precio_unitario).toFixed(2),
            ]);

            doc.autoTable({
                startY: tableStartY,
                head: [headers],
                body: body,
                styles: { fontSize: 9, cellPadding: 3 },
                headStyles: {
                    fillColor: [44, 62, 80],
                    textColor: 255,
                },
            });

            const finalY = doc.lastAutoTable.finalY;

            // Totales
            const subtotal = body.reduce(
                (acc, row) => acc + parseFloat(row[6]),
                0
            );
            const igv = subtotal * 0.18;
            const total = subtotal + igv;

            doc.text(
                `Subtotal: $${subtotal.toFixed(2)}`,
                pageWidth - 60,
                finalY + 5
            );
            doc.text(
                `IGV (18%): $${igv.toFixed(2)}`,
                pageWidth - 60,
                finalY + 10
            );
            doc.text(
                `Total: $${total.toFixed(2)}`,
                pageWidth - 60,
                finalY + 15
            );

            // Condiciones
            const condiciones = [
                "Emitir una orden de compra a nombre de INDUSTRIAS BALINSA E.I.R.L con ruc: 20608165585",
                "No se realizan cambios ni devoluciones",
                "Orden de compra irrevocable",
                "Los precios unitarios NO incluyen IGV",
                "Validez de la Cotización: 3 días",
                "Forma de Pago: Al contado",
                "Plazo de Entrega: 3 días",
                "Enviar comprobante a: industriasbalinsa@gmail.com",
            ];

            doc.setFontSize(10);
            doc.setTextColor(0, 0, 0);
            doc.text("CONDICIONES:", 14, finalY + 40);
            condiciones.forEach((linea, idx) => {
                doc.text(`- ${linea}`, 14, finalY + 46 + idx * 6);
            });

            // doc.setFont(undefined, "bold");
            // doc.text(
            //     `Total Cotización: S/ ${totalCotizacion.value.toFixed(2)}`,
            //     105,
            //     yPos,
            //     { align: "center" }
            // );

            // Guardar
            doc.save(
                `${clientData[0][1].replace(/\s+/g, "_")}_CTZ_${
                    quoteNumber.value
                }.pdf`
            );
            alert("¡Cotización generada correctamente!");
        };
    } catch (err) {
        console.error("Error generando PDF:", err);
        alert("Error generando la cotización.");
    }
}

// --- DATOS DEL PDF (Ejemplo - en una app real vendrían de props, store, o API) ---
const pdfData = {
    serviceTitle: plantillaSeleccionada.value.nombre, // [cite: 1]
    tasks: [
        "Programación y Calibración de la precisión del pesaje con pesas certificadas por INACAL.", // [cite: 3]
        "Trabajos de certificación realizados por laboratorio de calibración acreditado por INACAL.", // [cite: 4]
        "Trabajos de logística y unidades móviles (traslado y recojo de pesas patrón)", // [cite: 5]
        "Certificado emitido por laboratorio de calibración acreditado, autorizado por INACAL", // [cite: 5]
        "Sujeto a disponibilidad técnica. (previa coordinación para las fechas de certificación)", // [cite: 5, 6]
    ],
    priceIncludesItems: [
        "1. La actualización del Sistema Software de la norma produce será totalmente a CERO COSTO.", // [cite: 6]
        "2. El servicio de ajuste de la balanza es a CERO COSTO.",
    ],
    conditionsTable: [
        { label: "Validez de Cotizacion", value: `${validez.value}` },
        { label: "Plazo de Entrega", value: "3 Dias" },
        { label: "Forma de Pago", value: `${forma_pago.value}` },
    ],
    paymentDetails: [
        "Deposito a cuenta de Ahorros.",
        "BCP:  S/ 4752156367062 / CCI: 002 475 002156367062 25",
        "BCP:  $  4752156380104 / CCI: 002 475 002156380104 28",
        "BBVA: S/ 0011 0267 0201320316 / CCI: 011 267 000201320316 27", // [cite: 9]
        "BBVA: $  0011 0267 0201320324 / CCI: 011 267 000201320324 21", // [cite: 9]
        "Orden de compra irrevocable", // [cite: 9]
        "Servicio gratuito dentro del tiempo de garantía", // [cite: 9]
    ],
};

const generatePlantillaPdf = () => {
    const doc = new jsPDF();
    let currentY = 0;
    const symptomesPageWidth = doc.internal.pageSize.getWidth();
    const marginLeft = 15;
    const marginRight = 15;
    const contentWidth = symptomesPageWidth - marginLeft - marginRight;

    const backgroundImg = "/img/plantillacotizacion.png";
    doc.addImage(
        backgroundImg,
        "JPEG",
        0,
        0,
        doc.internal.pageSize.getWidth(),
        doc.internal.pageSize.getHeight()
    );

    // --- CABECERA (INDUSTRAS BALINSA, COTIZACION, N°) ---
    currentY = 40;
    // doc.setFontSize(12);
    // doc.setFont("helvetica", "bold");
    // doc.text(pdfData.companyName, symptomesPageWidth - marginRight, currentY, {
    //     align: "right",
    // });
    currentY += 6;
    doc.setFontSize(12); // "COTIZACION" es un poco más grande
    doc.setFont("helvetica", "bold");
    doc.setTextColor(255, 0, 0);
    doc.text(
        `COTIZACION     N° - ${quoteInfo.number}`,
        symptomesPageWidth - marginRight,
        currentY,
        {
            align: "right",
        }
    );
    doc.setFont("helvetica", "normal");
    doc.setTextColor(0, 0, 0);
    // currentY += 6;
    // doc.setFontSize(10);
    // doc.setFont("helvetica", "normal");
    // doc.text(pdfData.quoteNumber, symptomesPageWidth - marginRight, currentY, {
    //     align: "right",
    // });

    // --- FECHA Y DATOS DEL CLIENTE ---
    currentY += 7; // Espacio después de la cabecera derecha
    doc.setFontSize(12);
    doc.setFont("helvetica", "bold");
    doc.text(quoteInfo.date, symptomesPageWidth - marginRight, currentY, {
        align: "right",
    });

    currentY += 2;
    doc.setFont("helvetica", "normal");
    doc.text("Señores:", marginLeft, currentY);
    currentY += 5;
    doc.setFont("helvetica", "bold");
    doc.text(clientData[0][1], marginLeft, currentY);
    currentY += 12;
    doc.setFont("helvetica", "normal");
    doc.text("Estimados Señores:", marginLeft, currentY);
    currentY += 5;
    doc.text(
        "Tenemos el agrado de presupuestarle lo siguiente:",
        marginLeft,
        currentY
    );

    // --- TÍTULO DEL SERVICIO ---
    currentY += 10;
    doc.setFont("helvetica", "bold");
    doc.text(plantillaSeleccionada.value.nombre, marginLeft, currentY, {
        maxWidth: contentWidth,
        align: "left",
    });
    doc.setFont("helvetica", "normal");

    // --- CARACTERÍSTICAS DEL DISPOSITIVO (Tabla simple) ---

    instruments.value.forEach((instrument, index) => {
        if (currentY > 200) {
            doc.addPage();
            doc.addImage(
                backgroundImg,
                "JPEG",
                0,
                0,
                doc.internal.pageSize.getWidth(),
                doc.internal.pageSize.getHeight()
            );
            currentY = 50;
        }
        currentY += 8;
        const characteristicsBody = [
            [
                {
                    content: "Marca",
                    styles: { fontStyle: "normal", cellWidth: 25 },
                },
                {
                    content: `: ${instrument.hmarca_id}`,
                    styles: { fontStyle: "bold", cellWidth: "auto" },
                },
            ],
            [
                {
                    content: "Modelo",
                    styles: { fontStyle: "normal", cellWidth: 25 },
                },
                {
                    content: `: ${instrument.modelo}`,
                    styles: { fontStyle: "bold", cellWidth: "auto" },
                },
            ],
        ];
        doc.autoTable({
            startY: currentY,
            body: characteristicsBody,
            theme: "plain",
            styles: { fontSize: 10, cellPadding: 0.5 },
            margin: { left: marginLeft + 5 }, // Un poco de indentación
        });
        currentY = doc.lastAutoTable.finalY + 8;

        // --- TRABAJOS A REALIZAR ---
        doc.setFontSize(10);
        doc.setFont("helvetica", "bold");
        doc.text("TRABAJOS A REALIZAR:", marginLeft, currentY);
        currentY += 6;
        doc.setFont("helvetica", "normal");
        pdfData.tasks.forEach((task) => {
            doc.text(`* ${task}`, marginLeft + 5, currentY, {
                maxWidth: contentWidth - 5,
            });
            currentY += task.includes("Sujeto a disponibilidad técnica")
                ? 8
                : 5; // Más espacio para la última línea con subtexto
        });

        // --- PRECIO ---
        currentY += 5;
        doc.setFont("helvetica", "bold");
        doc.text(
            `PRECIO UNITARIO: ${moneda.value}${
                precios.value[index].toFixed(2) || "0.00"
            }`,
            marginLeft,
            currentY
        );
        // currentY += 5;
        // doc.text(pdfData.priceCurrency, marginLeft, currentY); // SOLES
        // currentY += 5;
        doc.setFont("helvetica", "normal");
    });

    currentY += 12;
    if (currentY > 250) {
        doc.addPage();
        doc.addImage(
            backgroundImg,
            "JPEG",
            0,
            0,
            doc.internal.pageSize.getWidth(),
            doc.internal.pageSize.getHeight()
        );
        currentY = 50;
    }

    // currentY += 8;
    // const characteristicsBody = pdfData.deviceCharacteristics.map((item) => [
    //     {
    //         content: `${item.label}`,
    //         styles: { fontStyle: "normal", cellWidth: 25 },
    //     },
    //     {
    //         content: `: ${item.value}`,
    //         styles: { fontStyle: "bold", cellWidth: "auto" },
    //     },
    // ]);

    // doc.autoTable({
    //     startY: currentY,
    //     body: characteristicsBody,
    //     theme: "plain",
    //     styles: { fontSize: 10, cellPadding: 0.5 },
    //     margin: { left: marginLeft + 5 }, // Un poco de indentación
    // });
    // currentY = doc.lastAutoTable.finalY + 8;

    // // --- TRABAJOS A REALIZAR ---
    // doc.setFontSize(10);
    // doc.setFont("helvetica", "bold");
    // doc.text(pdfData.tasksTitle, marginLeft, currentY);
    // currentY += 6;
    // doc.setFont("helvetica", "normal");
    // pdfData.tasks.forEach((task) => {
    //     doc.text(`* ${task}`, marginLeft + 5, currentY, {
    //         maxWidth: contentWidth - 5,
    //     });
    //     currentY += task.includes("Sujeto a disponibilidad técnica") ? 8 : 5; // Más espacio para la última línea con subtexto
    // });

    // // --- PRECIO ---
    // currentY += 5;
    // doc.setFont("helvetica", "bold");
    // doc.text(pdfData.price, marginLeft, currentY);
    // currentY += 5;
    // doc.text(pdfData.priceCurrency, marginLeft, currentY); // SOLES
    // currentY += 5;
    // doc.setFont("helvetica", "normal");

    // --- PRECIO INCLUYE ---
    doc.setFont("helvetica", "bold");
    doc.text("PRECIO INCLUYE:", marginLeft, currentY);
    currentY += 6;
    doc.setFont("helvetica", "normal");
    pdfData.priceIncludesItems.forEach((item) => {
        doc.text(item, marginLeft + 5, currentY, {
            maxWidth: contentWidth - 5,
        });
        currentY += 5;
    });

    // Total de Cotizacion
    currentY += 15;

    // Dibuja un recuadro amarillo con padding de 15px para el total
    // const boxPadding = 15;
    const boxWidth = 120;
    const boxHeight = 22;
    // Centrar horizontalmente el recuadro en la página
    const boxX = (symptomesPageWidth - boxWidth) / 2;
    const boxY = currentY;

    // Fondo amarillo
    doc.setFillColor(255, 235, 59); // Amarillo (Material Yellow 500)
    doc.setDrawColor(241, 196, 15); // Borde amarillo oscuro
    doc.rect(boxX, boxY, boxWidth, boxHeight, "FD"); // F = fill, D = draw (borde)

    // Texto centrado dentro del recuadro
    doc.setFontSize(18);
    doc.setFont("helvetica", "bold");
    // doc.setTextColor(44, 62, 80);
    doc.setTextColor(0, 255, 0);
    doc.text(
        `PRECIO TOTAL: ${moneda.value} ${totalCotizacion.value.toFixed(
            2
        )} + I.G.V`,
        boxX + boxWidth / 2,
        boxY + boxHeight / 2 + 2, // Centrado verticalmente
        { align: "center" }
    );
    doc.setTextColor(0, 0, 0);

    currentY += boxHeight + 5;
    doc.setFont("helvetica", "normal");

    currentY += 10;

    if (currentY > 200) {
        doc.addPage();
        doc.addImage(
            backgroundImg,
            "JPEG",
            0,
            0,
            doc.internal.pageSize.getWidth(),
            doc.internal.pageSize.getHeight()
        );
        currentY = 50;
    }

    // --- CONDICIONES GENERALES ---
    currentY += 5;
    doc.setFont("helvetica", "bold");
    doc.text("CONDICIONES GENERALES:", marginLeft, currentY);
    currentY += 4;

    const conditionsBody = pdfData.conditionsTable.map((item) => [
        {
            content: `${item.label}`,
            styles: { fontStyle: "normal", cellWidth: 35 },
        },
        {
            content: `: ${item.value}`,
            styles: { fontStyle: "bold", cellWidth: "auto" },
        },
    ]);

    doc.autoTable({
        startY: currentY,
        body: conditionsBody,
        theme: "plain",
        styles: { fontSize: 10, cellPadding: 0.5 },
        margin: { left: marginLeft },
    });
    currentY = doc.lastAutoTable.finalY + 3;

    doc.setFontSize(10);
    doc.setFont("helvetica", "normal");
    pdfData.paymentDetails.forEach((detail) => {
        doc.text(detail, marginLeft, currentY, { maxWidth: contentWidth });
        currentY += 5;
    });

    // --- MENSAJE DE CIERRE ---
    currentY += 5;
    doc.text("A la espera de su orden.", marginLeft, currentY);

    // Guardar PDF
    doc.save(`CTZ-${clienteData.value.razonSocial}-${quoteInfo.number}.pdf`);
};
</script>

<template>
    <AppLayout title="Requerimientos de Servicio">
        <template #header>
            <h1
                class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white"
            >
                Generar Cotizacion por Servicio
            </h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto uppercase text-sm shadow-lg bg-white dark:bg-gray-800 rounded-lg"
                >
                    <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                        <div
                            class="flex flex-wrap gap-2 uppercase text-sm justify-between my-2 mb-8"
                        >
                            <Link
                                :href="route('balanza')"
                                class="text-white bg-indigo-700 font-bold hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center"
                            >
                                <i class="fas fa-arrow-left mx-2"></i>Volver a
                                Informes
                            </Link>
                        </div>

                        <!-- Header: Nombre de la Empresa -->
                        <header class="header dark:bg-gray-800 dark:text-white">
                            <h1 class="dark:text-white text-2xl">
                                {{ clientData[0][1] }}
                            </h1>
                        </header>

                        <!-- Carrusel de Instrumentos -->
                        <section
                            v-if="instruments.length"
                            class="instruments-carousel dark:bg-gray-800"
                        >
                            <h2 class="dark:text-white text-base">Detalles</h2>
                            <p class="carousel-info dark:text-white">
                                {{ currentInstrumentIndex + 1 }} de
                                {{ instruments.length }}
                            </p>

                            <div class="carousel-container">
                                <button
                                    @click="prevInstrument"
                                    class="carousel-btn prev-btn"
                                    :disabled="currentInstrumentIndex === 0"
                                >
                                    &#10094;
                                </button>

                                <div class="carousel-track" ref="carouselTrack">
                                    <div
                                        v-for="(
                                            instrument, index
                                        ) in instruments"
                                        :key="index"
                                        class="instrument-card mb-8 p-6 bg-white shadow-sm dark:bg-gray-800"
                                        :class="{
                                            active:
                                                index ===
                                                currentInstrumentIndex,
                                        }"
                                    >
                                        <!-- Tarjeta de Diagnostico -->
                                        <!-- <div
                                            class="p-4 border rounded-lg bg-white shadow-sm dark:bg-gray-800 flex flex-col justify-between gap-4 mb-6"
                                        >
                                            <div class="mt-1 space-y-2">
                                                <div>
                                                    <h4
                                                        class="font-semibold text-gray-700 dark:text-gray-300"
                                                    >
                                                        Diagnostico:
                                                    </h4>
                                                    <p
                                                        class="text-gray-800 dark:text-white whitespace-pre-wrap"
                                                    >
                                                        {{
                                                            instrument.diagnostico
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div> -->

                                        <div
                                            class="grid grid-cols-1 md:grid-cols-2 gap-6"
                                        >
                                            <!-- Tarjeta: Datos del instrumento -->
                                            <div
                                                class="p-6 border rounded-lg bg-white shadow-sm dark:bg-gray-800"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-800 dark:text-white mb-4"
                                                >
                                                    {{
                                                        typeInstrument[
                                                            instrument.instrumento -
                                                                1
                                                        ].name
                                                    }}
                                                    #{{ index + 1 }}
                                                </h3>

                                                <!-- <div
                                                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 text-sm"
                                                >
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Instrumento:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                typeInstrument[
                                                                    instrument.instrumento -
                                                                        1
                                                                ].name
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Marca:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.hmarca_id
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Modelo:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.modelo
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Serie:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.serie
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Capacidad:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.capacidad
                                                            }}
                                                            {{
                                                                instrument.medida_capacidad
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >División:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.division
                                                            }}
                                                            {{
                                                                instrument.medida_division
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Plataforma:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.plataforma
                                                            }}
                                                            {{
                                                                instrument.medida_plataforma
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Técnico:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.tecnico
                                                            }}
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Require:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.requiere
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >F. del
                                                            Informe:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                formatDate(
                                                                    instrument.informetec_fecha
                                                                )
                                                            }}
                                                        </div>
                                                    </div>
                                                </div> -->
                                                <!-- Detalles de los Instrumentos -->
                                                <!-- Balanza -->
                                                <div
                                                    v-if="
                                                        instrument.instrumento ===
                                                        '1'
                                                    "
                                                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 text-sm"
                                                >
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Instrumento:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                typeInstrument[
                                                                    instrument.instrumento -
                                                                        1
                                                                ].name
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Marca:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.hmarca_id
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Modelo:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.modelo
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Serie:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.serie
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Capacidad:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.capacidad
                                                            }}
                                                            {{
                                                                instrument.medida_capacidad
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >División:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.division
                                                            }}
                                                            {{
                                                                instrument.medida_division
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Plataforma:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.plataforma
                                                            }}
                                                            {{
                                                                instrument.medida_plataforma
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Técnico:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.tecnico
                                                            }}
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Require:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.requiere
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >F. del
                                                            Informe:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                formatDate(
                                                                    instrument.informetec_fecha
                                                                )
                                                            }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Termómetro -->
                                                <div
                                                    v-else-if="
                                                        instrument.instrumento ===
                                                        '2'
                                                    "
                                                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 text-sm"
                                                >
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Instrumento:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                typeInstrument[
                                                                    instrument.instrumento -
                                                                        1
                                                                ].name
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Marca:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.hmarca_id
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Modelo:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.modelo
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Serie:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.serie
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Rango:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.rango
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >División:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.division
                                                            }}
                                                            {{
                                                                instrument.medida_division
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Bastago:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.medida_bastago
                                                            }}
                                                            {{
                                                                instrument.medida_bastago2
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Técnico:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.tecnico
                                                            }}
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Require:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.requiere
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >F. del
                                                            Informe:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                formatDate(
                                                                    instrument.informetec_fecha
                                                                )
                                                            }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Otros Instrumentos (Pesa) -->
                                                <div
                                                    v-else
                                                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 text-sm"
                                                >
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Instrumento:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                typeInstrument[
                                                                    instrument.instrumento -
                                                                        1
                                                                ].name
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Marca:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.hmarca_id ||
                                                                "NO INDICA"
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Forma:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.modelo
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Material:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.material
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Capacidad:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.capacidad
                                                            }}
                                                            {{
                                                                instrument.medida_capacidad
                                                            }}
                                                        </div>
                                                    </div>
                                                    <!-- <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >División:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.division
                                                            }}
                                                            {{
                                                                instrument.medida_division
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Plataforma:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.plataforma
                                                            }}
                                                            {{
                                                                instrument.medida_plataforma
                                                            }}
                                                        </div>
                                                    </div> -->
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Técnico:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.tecnico
                                                            }}
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >Require:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                instrument.requiere
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-600 dark:text-gray-300"
                                                            >F. del
                                                            Informe:</span
                                                        >
                                                        <div
                                                            class="text-gray-800 dark:text-white"
                                                        >
                                                            {{
                                                                formatDate(
                                                                    instrument.informetec_fecha
                                                                )
                                                            }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tarjeta: Trabajos + Precio -->
                                            <div
                                                class="p-6 border rounded-lg bg-white shadow-sm dark:bg-gray-800 flex flex-col justify-between gap-4"
                                            >
                                                <div>
                                                    <h3
                                                        class="text-lg font-semibold text-gray-800 dark:text-white mb-2"
                                                    >
                                                        Trabajos a realizar
                                                    </h3>
                                                    <textarea
                                                        v-model="
                                                            instrument.trabajos
                                                        "
                                                        class="w-full h-60 p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 text-sm"
                                                        placeholder="Describe los trabajos a realizar..."
                                                    ></textarea>
                                                    <!-- <textarea
                                                        v-model="
                                                            trabajosBullet[index]
                                                        "
                                                        class="w-full h-60 p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 text-sm"
                                                        placeholder="Describe los trabajos a realizar..."
                                                    ></textarea> -->
                                                </div>

                                                <div>
                                                    <h3
                                                        class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                                                    >
                                                        Precio estimado
                                                    </h3>
                                                    <!-- <input
                                                        type="number"
                                                        v-model.number="
                                                            precios[index]
                                                        "
                                                        placeholder="0.00"
                                                        class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 text-sm"
                                                        min="0"
                                                        step="0.01"
                                                    /> -->
                                                    <div
                                                        class="grid grid-cols-4 gap-4"
                                                    >
                                                        <div>
                                                            <select
                                                                v-model="moneda"
                                                                class="p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 text-sm w-full"
                                                            >
                                                                <option
                                                                    value="S/"
                                                                >
                                                                    S/
                                                                </option>
                                                                <option
                                                                    value="$"
                                                                >
                                                                    $
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-span-3">
                                                            <input
                                                                type="number"
                                                                v-model.number="
                                                                    precios[
                                                                        index
                                                                    ]
                                                                "
                                                                placeholder="0.00"
                                                                class="w-full p-2 border rounded-md focus:ring-2 focus:ring-emerald-500 text-sm"
                                                                min="0"
                                                                step="0.01"
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tarjeta de Diagnostico (Abajo)-->
                                        <div
                                            class="p-4 border rounded-lg bg-white shadow-sm dark:bg-gray-800 flex flex-col justify-between gap-4 mt-6"
                                        >
                                            <div class="mt-1 space-y-2">
                                                <div>
                                                    <h4
                                                        class="font-semibold text-gray-700 dark:text-gray-300"
                                                    >
                                                        Diagnostico:
                                                    </h4>
                                                    <p
                                                        class="text-gray-800 dark:text-white whitespace-pre-wrap"
                                                    >
                                                        {{
                                                            instrument.diagnostico
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Galeria de Fotos -->
                                        <div
                                            class="p-4 border rounded-lg bg-white shadow-sm dark:bg-gray-800 flex flex-col justify-between gap-4 mt-6"
                                        >
                                            <div
                                                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4"
                                            >
                                                <div
                                                    v-for="(src, i) in [
                                                        instrument.foto,
                                                        instrument.foto2,
                                                        instrument.foto3,
                                                    ].filter(Boolean)"
                                                    :key="i"
                                                >
                                                    <!-- <img
                                                        :src="`/hservicio_img/${src}`"
                                                        alt="Foto del instrumento"
                                                        class="w-full h-48 object-cover rounded shadow cursor-pointer"
                                                        @click="
                                                            openImageModal(
                                                                [
                                                                    instrument.foto &&
                                                                        `/hservicio_img/${instrument.foto}`,
                                                                    instrument.foto2 &&
                                                                        `/hservicio_img/${instrument.foto2}`,
                                                                    instrument.foto3 &&
                                                                        `/hservicio_img/${instrument.foto3}`,
                                                                ],
                                                                i
                                                            )
                                                        "
                                                    /> -->

                                                    <img
                                                        v-if="visibleImages[i]"
                                                        :src="`/hservicio_img/${src}`"
                                                        alt="Foto del instrumento"
                                                        class="w-full h-48 object-cover rounded shadow cursor-pointer"
                                                        @error="hideImage(i)"
                                                        @click="
                                                            openImageModal(
                                                                getValidImagePaths(
                                                                    instrument
                                                                ),
                                                                i
                                                            )
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button
                                    @click="nextInstrument"
                                    class="carousel-btn next-btn"
                                    :disabled="
                                        currentInstrumentIndex ===
                                        instruments.length - 1
                                    "
                                >
                                    &#10095;
                                </button>
                            </div>

                            <!-- Carousel Dots -->
                            <div class="carousel-indicators">
                                <button
                                    v-for="(_, index) in instruments"
                                    :key="`indicator-${index}`"
                                    @click="goToInstrument(index)"
                                    class="indicator-dot"
                                    :class="{
                                        active:
                                            index === currentInstrumentIndex,
                                    }"
                                ></button>
                            </div>
                        </section>

                        <!-- Work Description -->

                        <!-- <section
                            v-if="instruments.length"
                            class="work-section dark:bg-gray-800"
                        >
                            <h2
                                class="text-lg font-semibold mb-2 dark:text-white"
                            >
                                Trabajos a Realizar
                            </h2>
                            <textarea
                                v-model="workText"
                                rows="12"
                                placeholder="Escribe aquí los trabajos para todos los instrumentos..."
                                class="w-full p-4 border rounded-md focus:ring focus:ring-emerald-500 dark:bg-gray-700 dark:text-white dark:border-gray-600"
                            ></textarea>

                            <p
                                class="text-sm text-gray-500 mt-2 dark:text-gray-40"
                            >
                                Escribe los trabajos de cada instrumento
                                separados por dos saltos de línea. Por
                                ejemplo:<br />
                                <code>#1 Trabajo 1...</code><br />
                                <code>#2 Trabajo 2...</code>
                            </p>

                            <div class="mt-4 text-right">
                                <button
                                    @click="distribuirTrabajos"
                                    class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700"
                                >
                                    Aplicar a cada instrumento
                                </button>
                            </div>
                        </section> -->

                        <!-- Validez y Forma de Pago -->
                        <section
                            v-if="instruments.length"
                            class="dark:bg-gray-800 mb-6"
                        >
                            <div class="flex flex-col md:flex-row gap-4">
                                <!-- Validez de Cotización -->
                                <div class="flex-1">
                                    <label
                                        for="validez"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-100 mb-1"
                                    >
                                        Validez de Cotización
                                    </label>
                                    <select
                                        id="validez"
                                        v-model="validez"
                                        class="w-full border rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500"
                                    >
                                        <option value="3 días">3 días</option>
                                        <option value="7 días">7 días</option>
                                        <option value="15 días">15 días</option>
                                    </select>
                                </div>
                                <!-- Forma de Pago -->
                                <div class="flex-1">
                                    <label
                                        for="forma_pago"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-100 mb-1"
                                    >
                                        Forma de Pago
                                    </label>
                                    <select
                                        id="forma_pago"
                                        v-model="forma_pago"
                                        class="w-full border rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500"
                                    >
                                        <option value="Anticipado">
                                            Anticipado
                                        </option>
                                        <option value="Al contado">
                                            Al contado
                                        </option>
                                        <option value="Credito">Crédito</option>
                                    </select>
                                </div>
                            </div>
                        </section>

                        <!-- Plantillas y Total -->
                        <div class="grid grid-cols-3 gap-4">
                            <!-- Plantillas -->
                            <section
                                class="total-section dark:bg-gray-800 col-span-2"
                            >
                                <div>
                                    <label
                                        for="plantilla"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-100 mb-1"
                                    >
                                        Seleccione Plantilla
                                    </label>

                                    <!-- <select
                                        id="plantilla"
                                        v-model="selectedPlantilla"
                                        class="w-full border rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500"
                                    >
                                        <option
                                            v-for="plantilla in plantillasPDF"
                                            :key="plantilla.id"
                                            :value="plantilla.id"
                                        >
                                            {{ plantilla.nombre }}
                                        </option>
                                    </select> -->

                                    <select
                                        id="plantilla"
                                        v-model="selectedPlantilla"
                                        class="w-full border rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500"
                                    >
                                        <option
                                            v-for="plantilla in todosDelMismoTipo
                                                ? plantillasPDF.filter(
                                                      (p, idx) =>
                                                          idx === 0 ||
                                                          p.instrumento ===
                                                              instruments[0]
                                                                  ?.instrumento
                                                  )
                                                : [plantillasPDF[0]]"
                                            :key="plantilla.id"
                                            :value="plantilla.id"
                                        >
                                            {{ plantilla.nombre }}
                                        </option>
                                    </select>

                                </div>
                            </section>

                            <!-- Total de Cotizacion -->
                            <section class="total-section dark:bg-gray-800">
                                <div
                                    class="my-2 p-4 border rounded-lg shadow text-right text-lg font-semibold text-gray-800 dark:text-white"
                                >
                                    Total: {{ moneda }}
                                    {{ totalCotizacion.toFixed(2) }}
                                </div>
                            </section>
                        </div>

                        <!-- Action Buttons -->
                        <section
                            v-if="instruments.length"
                            class="action-buttons dark:bg-gray-800"
                        >
                            <button
                                @click="previewQuote"
                                class="btn secondary-btn"
                            >
                                Previsualizar PDF
                            </button>
                            <button
                                @click="generatePdf"
                                class="btn primary-btn"
                            >
                                Generar Cotizacion
                            </button>
                        </section>

                        <!-- PDF Modal Preview -->
                        <div v-if="showPreview" class="modal">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2>Cotizacion por Servicio</h2>
                                    <button
                                        @click="showPreview = false"
                                        class="btn-icon"
                                    >
                                        ×
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <h1>{{ clientData[0][1] }}</h1>
                                    <p class="quote-date">
                                        Fecha: {{ formattedDate }}
                                    </p>
                                    <p class="quote-number">
                                        Cotizacion #: CTZ-{{ quoteNumber }}
                                    </p>

                                    <h2>Resumen de Instrumentos</h2>
                                    <div
                                        v-for="(
                                            instrument, index
                                        ) in instruments"
                                        :key="`detail-${index}`"
                                        class="instrument-detail"
                                    >
                                        <h3>
                                            {{
                                                typeInstrument[
                                                    instrument.instrumento - 1
                                                ].name
                                            }}
                                            #{{ index + 1 }}
                                        </h3>
                                        <table class="quote-table">
                                            <tbody>
                                                <tr
                                                    v-for="field in instrumentFields"
                                                    :key="field.name"
                                                >
                                                    <th>{{ field.label }}</th>
                                                    <td>
                                                        {{
                                                            instrument[
                                                                field.name
                                                            ]
                                                        }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Capacidad</th>
                                                    <td>
                                                        {{
                                                            instrument.capacidad
                                                        }}
                                                        {{
                                                            instrument.medida_capacidad
                                                        }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>División</th>
                                                    <td>
                                                        {{
                                                            instrument.division
                                                        }}
                                                        {{
                                                            instrument.medida_division
                                                        }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Plataforma</th>
                                                    <td>
                                                        {{
                                                            instrument.plataforma
                                                        }}
                                                        {{
                                                            instrument.medida_plataforma
                                                        }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Trabajos a Realizar</th>
                                                    <td>
                                                        <ul
                                                            class="list-disc pl-5"
                                                        >
                                                            <li
                                                                v-for="(
                                                                    trabajo,
                                                                    index
                                                                ) in parseTrabajos(
                                                                    instrument.trabajos
                                                                )"
                                                                :key="index"
                                                            >
                                                                {{ trabajo }}
                                                            </li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Precio</th>
                                                    <td>
                                                        S/
                                                        {{
                                                            precios[index]
                                                                ? precios[
                                                                      index
                                                                  ].toFixed(2)
                                                                : "0.00"
                                                        }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="quote-footer">
                                        <p>
                                            Total de la Cotización: S/
                                            {{ totalCotizacion.toFixed(2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Galeria -->
                        <div
                            v-if="showImageModal"
                            class="fixed inset-0 z-[99999] bg-black bg-opacity-80 flex items-center justify-center p-4"
                            @click.self="closeImageModal"
                        >
                            <div
                                class="relative max-w-5xl w-full flex items-center justify-center"
                            >
                                <!-- Botón anterior -->
                                <button
                                    @click="prevImage"
                                    :disabled="modalImageIndex === 0"
                                    class="absolute left-2 text-white text-4xl px-3 py-2 hover:text-gray-300 disabled:opacity-50"
                                >
                                    ‹
                                </button>

                                <!-- Imagen -->
                                <img
                                    :src="modalImageSrc"
                                    class="max-h-[80vh] max-w-full rounded-lg shadow-lg"
                                />

                                <!-- Botón siguiente -->
                                <button
                                    @click="nextImage"
                                    :disabled="
                                        modalImageIndex ===
                                        currentInstrumentImages.length - 1
                                    "
                                    class="absolute right-2 text-white text-4xl px-3 py-2 hover:text-gray-300 disabled:opacity-50"
                                >
                                    ›
                                </button>

                                <!-- Cerrar -->
                                <button
                                    @click="closeImageModal"
                                    class="absolute top-2 right-4 text-white text-3xl font-bold hover:text-gray-300"
                                >
                                    ×
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
/* Global Styles */
:root {
    --primary-color: #264237;
    --secondary-color: #2c2250;
    --accent-color: #e74c3c;
    --light-color: #ecf0f1;
    --dark-color: #2c3e50;
    --border-color: #bdc3c7;
    --border-radius: 4px;
    --box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: var(--dark-color);
    background-color: var(--light-color);
}

.quote-registration-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Header Styles */
.header {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px;
    background-color: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
}

h1,
h2,
h3,
h4 {
    color: var(--primary-color);
    margin-bottom: 15px;
}

.company-name-input {
    width: 100%;
    max-width: 500px;
    padding: 10px;
    font-size: 18px;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    margin-top: 10px;
}

/* Section Styles */
section {
    margin-bottom: 30px;
    padding: 20px;
    background-color: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
}

.instrument-controls {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: flex-end;
}

.form-group {
    margin-bottom: 15px;
    flex: 1;
    min-width: 200px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--dark-color);
}

input,
select,
textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    font-size: 16px;
}

textarea {
    resize: vertical;
    min-height: 80px;
}

/* Button Styles */
.btn {
    display: inline-block;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    user-select: none;
    border: 1px solid transparent;
    padding: 10px 20px;
    font-size: 16px;
    line-height: 1.5;
    border-radius: var(--border-radius);
    transition: var(--transition);
    cursor: pointer;
}

.primary-btn {
    color: white;
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.primary-btn:hover {
    background-color: #1a252f;
    border-color: #1a252f;
}

.secondary-btn {
    color: white;
    background-color: var(--secondary-color);
    border-color: var(--secondary-color);
}

.secondary-btn:hover {
    background-color: #6a7a7a;
    border-color: #6a7a7a;
}

.btn-icon {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: var(--accent-color);
}

/* Carousel Styles */
.instruments-carousel {
    position: relative;
    text-align: center;
}

.carousel-info {
    margin-bottom: 15px;
    font-style: italic;
    color: var(--secondary-color);
}

.carousel-container {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    margin: 0 auto;
    max-width: 1000px;
}

.carousel-track {
    display: flex;
    overflow: hidden;
    width: 100%;
    position: relative;
}

.carousel-btn {
    background-color: rgba(44, 62, 80, 0.7);
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    z-index: 10;
    transition: var(--transition);
}

.carousel-btn:hover {
    background-color: rgba(44, 62, 80, 1);
}

.carousel-btn:disabled {
    background-color: rgba(127, 140, 141, 0.5);
    cursor: not-allowed;
}

.prev-btn {
    left: -20px;
}

.next-btn {
    right: -20px;
}

.carousel-indicators {
    display: flex;
    justify-content: center;
    margin-top: 20px;
    gap: 10px;
}

.indicator-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: var(--border-color);
    border: none;
    cursor: pointer;
    transition: var(--transition);
}

.indicator-dot.active {
    background-color: var(--primary-color);
    transform: scale(1.2);
}

/* Instrument Card Styles */
.instrument-card {
    min-width: 100%;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    background-color: white;
    box-shadow: var(--box-shadow);
    display: none;
}

.instrument-card.active {
    display: block;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background-color: #f8f9fa;
    border-bottom: 1px solid var(--border-color);
}

.card-body {
    padding: 15px;
}

/* Work Section */
.work-item {
    margin-bottom: 20px;
    padding: 15px;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    background-color: #f8f9fa;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    justify-content: center;
    gap: 20px;
}

/* Modal Styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 999999;
}

.modal-content {
    background-color: white;
    border-radius: var(--border-radius);
    width: 90%;
    max-width: 900px;
    max-height: 90vh;
    overflow: auto;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.modal-header,
.modal-footer {
    padding: 15px 20px;
    display: flex;
    align-items: center;
}

.modal-header {
    justify-content: space-between;
    border-bottom: 1px solid var(--border-color);
}

.modal-footer {
    justify-content: flex-end;
    border-top: 1px solid var(--border-color);
    gap: 10px;
}

.modal-body {
    padding: 20px;
    max-height: calc(90vh - 140px);
    overflow-y: auto;
}

/* PDF Preview Styles */
.pdf-preview {
    padding: 20px;
    background-color: white;
}

.quote-date,
.quote-number {
    margin-bottom: 15px;
    font-style: italic;
}

.quote-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 25px;
}

.quote-table th,
.quote-table td {
    border: 1px solid var(--border-color);
    padding: 10px;
    text-align: left;
}

.quote-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.instrument-detail {
    margin-bottom: 30px;
}

.work-description {
    padding: 10px;
    background-color: #f8f9fa;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    margin-bottom: 15px;
}

.quote-footer {
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
    font-style: italic;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .instrument-controls {
        flex-direction: column;
    }

    .form-group {
        width: 100%;
    }

    .carousel-btn {
        width: 30px;
        height: 30px;
        font-size: 14px;
    }

    .prev-btn {
        left: -10px;
    }

    .next-btn {
        right: -10px;
    }

    .action-buttons {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        margin-bottom: 10px;
    }

    .modal-content {
        width: 95%;
        height: 95vh;
        max-height: none;
    }

    .modal-body {
        max-height: calc(95vh - 140px);
    }
}
</style>

<!--
<script>
import { ref, computed, onMounted, watch } from 'vue'
import { jsPDF } from 'jspdf'
import 'jspdf-autotable'

export default {
  name: 'RegistrationQuoteForm',
  setup() {
    const companyName = ref('Client Company Name')
    const instruments = ref([])
    const showPreview = ref(false)
    const quoteNumber = ref(Math.floor(10000 + Math.random() * 90000))
    const currentInstrumentIndex = ref(0)
    const carouselTrack = ref(null)

    const newInstrument = ref({
      type: 'Analytical',
      quantity: 1,
      brand: '',
      model: '',
      series: '',
      capacity: '',
      division: '',
      platform: '',
      requirements: '',
      diagnostics: '',
      workToBeDone: ''
    })

    const formattedDate = computed(() => {
      const now = new Date()
      return now.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    })

    // Watch for changes in the instruments array to update the carousel
    watch(instruments, (newVal) => {
      if (newVal.length === 0) {
        currentInstrumentIndex.value = 0
      } else if (currentInstrumentIndex.value >= newVal.length) {
        currentInstrumentIndex.value = newVal.length - 1
      }
    })

    function addInstrument() {
      instruments.value.push({...newInstrument.value})
      // Reset some fields but keep the type
      newInstrument.value = {
        type: newInstrument.value.type,
        quantity: 1,
        brand: '',
        model: '',
        series: '',
        capacity: '',
        division: '',
        platform: '',
        requirements: '',
        diagnostics: '',
        workToBeDone: ''
      }
      // Go to the newly added instrument
      currentInstrumentIndex.value = instruments.value.length - 1
    }

    function removeInstrument(index) {
      instruments.value.splice(index, 1)
    }

    function prevInstrument() {
      if (currentInstrumentIndex.value > 0) {
        currentInstrumentIndex.value--
      }
    }

    function nextInstrument() {
      if (currentInstrumentIndex.value < instruments.value.length - 1) {
        currentInstrumentIndex.value++
      }
    }

    function goToInstrument(index) {
      currentInstrumentIndex.value = index
    }

    function previewQuote() {
      showPreview.value = true
    }

    function generateQuote() {
      // Create a new jsPDF instance
      const doc = new jsPDF()

      // Set document properties
      doc.setProperties({
        title: `${companyName.value} - Quote`,
        subject: 'Instrument Quote',
        author: 'Quote Generator',
        keywords: 'quote, instruments',
        creator: 'Quote Generator'
      })

      // Add company name as title
      doc.setFontSize(20)
      doc.setTextColor(44, 62, 80) // Dark blue color
      doc.text(`${companyName.value} - Quote`, 105, 20, { align: 'center' })

      // Add quote information
      doc.setFontSize(12)
      doc.setTextColor(0, 0, 0)
      doc.text(`Date: ${formattedDate.value}`, 14, 30)
      doc.text(`Quote #: QT-${quoteNumber.value}`, 14, 37)

      // Add instruments summary table
      doc.setFontSize(16)
      doc.setTextColor(44, 62, 80)
      doc.text('Instruments Summary', 14, 50)

      // Create summary table
      const summaryTableColumns = [
        { header: 'Type', dataKey: 'type' },
        { header: 'Brand', dataKey: 'brand' },
        { header: 'Model', dataKey: 'model' },
        { header: 'Series', dataKey: 'series' },
        { header: 'Quantity', dataKey: 'quantity' }
      ]

      const summaryTableData = instruments.value.map(instrument => ({
        type: instrument.type,
        brand: instrument.brand,
        model: instrument.model,
        series: instrument.series,
        quantity: instrument.quantity
      }))

      doc.autoTable({
        startY: 55,
        head: [summaryTableColumns.map(col => col.header)],
        body: summaryTableData.map(data =>
          summaryTableColumns.map(col => data[col.dataKey])
        ),
        theme: 'grid',
        headStyles: {
          fillColor: [44, 62, 80],
          textColor: [255, 255, 255],
          fontStyle: 'bold'
        },
        styles: {
          cellPadding: 3,
          fontSize: 10
        }
      })

      // Add detailed specifications for each instrument
      doc.setFontSize(16)
      doc.setTextColor(44, 62, 80)
      let yPos = doc.lastAutoTable.finalY + 15

      // Check if we need a new page
      if (yPos > 250) {
        doc.addPage()
        yPos = 20
      }

      doc.text('Detailed Specifications', 14, yPos)
      yPos += 10

      instruments.value.forEach((instrument, index) => {
        // Check if we need a new page
        if (yPos > 250) {
          doc.addPage()
          yPos = 20
        }

        doc.setFontSize(14)
        doc.setTextColor(44, 62, 80)
        doc.text(`${instrument.type} #${index + 1}`, 14, yPos)
        yPos += 5

        // Create detailed table for this instrument
        const detailsData = [
          ['Brand', instrument.brand],
          ['Model', instrument.model],
          ['Series', instrument.series],
          ['Capacity', instrument.capacity],
          ['Division', instrument.division],
          ['Platform', instrument.platform],
          ['Requirements', instrument.requirements],
          ['Diagnostics', instrument.diagnostics]
        ]

        doc.autoTable({
          startY: yPos,
          head: [['Property', 'Value']],
          body: detailsData,
          theme: 'grid',
          headStyles: {
            fillColor: [44, 62, 80],
            textColor: [255, 255, 255],
            fontStyle: 'bold'
          },
          styles: {
            cellPadding: 3,
            fontSize: 10
          },
          columnStyles: {
            0: { cellWidth: 40 }
          }
        })

        yPos = doc.lastAutoTable.finalY + 10

        // Add work to be done
        doc.setFontSize(12)
        doc.setTextColor(44, 62, 80)
        doc.text('Work to be Done:', 14, yPos)
        yPos += 7

        // Split work text into lines to handle long text
        const workText = instrument.workToBeDone || 'No work specified'
        const textLines = doc.splitTextToSize(workText, 180)

        // Check if we need a new page for the work text
        if (yPos + (textLines.length * 5) > 280) {
          doc.addPage()
          yPos = 20
        }

        doc.setTextColor(0, 0, 0)
        doc.setFontSize(10)
        doc.text(textLines, 14, yPos)

        yPos += (textLines.length * 5) + 15
      })

      // Add footer
      // Check if we need a new page for the footer
      if (yPos > 260) {
        doc.addPage()
        yPos = 20
      }

      doc.setDrawColor(200, 200, 200)
      doc.line(14, yPos, 196, yPos)
      yPos += 10

      doc.setFontSize(10)
      doc.setTextColor(100, 100, 100)
      doc.text('Thank you for your business!', 105, yPos, { align: 'center' })
      yPos += 5
      doc.text('This quote is valid for 30 days from the date of issue.', 105, yPos + 5, { align: 'center' })

      // Save the PDF
      doc.save(`${companyName.value.replace(/\s+/g, '_')}_Quote_${quoteNumber.value}.pdf`)

      // Here you would typically save the quote to a database
      alert('Quote generated and registered successfully!')
    }

    return {
      companyName,
      instruments,
      newInstrument,
      showPreview,
      formattedDate,
      quoteNumber,
      currentInstrumentIndex,
      carouselTrack,
      addInstrument,
      removeInstrument,
      prevInstrument,
      nextInstrument,
      goToInstrument,
      previewQuote,
      generateQuote
    }
  }
}
</script>

 -->
