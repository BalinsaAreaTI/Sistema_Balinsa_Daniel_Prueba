<!-- <script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModalResponsive from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import Swal from "sweetalert2";
import { useForm } from "@inertiajs/vue3";
import vueTailwindPaginationUmd from "@ocrv/vue-tailwind-pagination";
import { nextTick, ref, watchEffect } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { router } from "@inertiajs/vue3";
import { computed } from "vue";

const searchQuery = ref("");
// const filteredServicio = ref([]);
const groupedServicios = ref({});

function redirectToCreate(servicio) {
    router.visit(route("requiere.cotiza", servicio.id));
}

const props = defineProps({
    servicios: {
        type: Object,
        required: true,
    },
    users: {
        type: Object,
    },
    datos: {
        type: Object,
    },
});

const servicios = props.servicios;

const form = useForm({
    id: "",
});

watchEffect(() => {
    if (props.servicios.data) {
        const searchLowerCase = searchQuery.value.toLowerCase().trim();
        // Filtramos los servicios basándonos en si la cadena de búsqueda está incluida en el ID o el número de informe
        const serviciosFiltrados = props.servicios.data.filter((servicio) => {
            return (
                servicio.id.toString().includes(searchLowerCase) ||
                servicio.n_informe.includes(searchLowerCase)
            );
        });
        groupedServicios.value = groupByMonthAndYear(serviciosFiltrados);
    }
});

// Función para agrupar por mes y año
function groupByMonthAndYear(servicios) {
    const grouped = {};
    servicios.forEach((servicio) => {
        const date = new Date(servicio.fecha);
        const month = date.toLocaleString("default", { month: "long" });
        const year = date.getFullYear();
        const key = `${month} ${year}`;
        if (!grouped[key]) {
            grouped[key] = [];
        }
        grouped[key].push(servicio);
    });
    return grouped;
}

// Paginacion
// const formPage = useForm({});
// const onPageClick = (event) => {
//     formPage.get(route("servicios.index", { page: event }));
// };
// const previousPage = () => {
//     const prevPage = props.servicios.current_page - 1;
//     formPage.get(route("servicios.index", { page: prevPage }));
// };

// const nextPage = () => {
//     const nextPage = props.servicios.current_page + 1;
//     formPage.get(route("servicios.index", { page: nextPage }));
// };

// const goToPage = (page) => {
//     formPage.get(route("servicios.index", { page }));
// };

const goToPage = (page) => {
    // Inertia.get(route("balanza", { page }));
    Inertia.get(route("balanza"), {
        page,
        search: searchQuery.value, // si existe
    });
};

const previousPage = () => {
    const prevPage = props.servicios.current_page - 1;
    // Inertia.get(route("balanza", { page: prevPage }));
    // Inertia.get(route("balanza"), {
    //     prevPage,
    //     search: searchQuery.value, // si existe
    // });
    // const prev = props.servicios.current_page - 1;
    if (prevPage >= 1) {
        goToPage(prevPage);
    }
};

const nextPage = () => {
    const nextPage = props.servicios.current_page + 1;
    // Inertia.get(route("balanza", { page: nextPage }));
    // Inertia.get(route("balanza"), {
    //     nextPage,
    //     search: searchQuery.value, // si existe
    // });
    if (nextPage <= props.servicios.last_page) {
        goToPage(nextPage);
    }
};

const onPageClick = (event) => {
    Inertia.get(route("balanza", { page: event }));
};

// const total_pages = props.servicios.last_page;
// const current_page = props.servicios.current_page;
const current_page = computed(() => props.servicios.current_page);
const total_pages = computed(() => props.servicios.last_page);
const countPerPage = computed(() => props.servicios.data.length);
const totalCount = computed(() => props.servicios.total);
</script>

<template>
    <AppLayout title="Hojas de Servicio no Cotizados">
        <template #header>
            <h1
                class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white"
            >
                Lista de Hojas Servicio no Cotizados
            </h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800"
                >
                    <div
                        class="flex flex-wrap gap-2 uppercase text-sm justify-between"
                    >
                        <Link
                            :href="route('c_servicos.index')"
                            class="text-white bg-indigo-700 font-bold hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center"
                        >
                            <i class="fas fa-arrow-left mx-2"></i>Lista de
                            cotizaciones
                        </Link>
                    </div>
                    <div
                        class="w-full mt-4 p-4 bg-gray-200 border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                    >
                        <div class="mb-1 font-normal text-dark dark:text-white">
                            Lista de las hojas de servicio que aun no se han
                            cotizado.
                            <b
                                >Seleccione y luego Haga doble Click en el
                                informe a Cotizar</b
                            >:
                        </div>
                    </div>

                    <div class="flex flex-col items-start py-2">
                        <InputLabel
                            for="table-search"
                            class="block text-md font-medium text-gray-700"
                            >Buscar
                        </InputLabel>
                        <div class="relative mt-1">
                            <div
                                class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none"
                            >
                                <svg
                                    class="w-4 h-4 text-gray-500 dark:text-black"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                                    />
                                </svg>
                            </div>
                            <input
                                v-model="searchQuery"
                                type="text"
                                id="table-search"
                                class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg md:w-80 w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-white dark:border-gray-600 dark:placeholder-gray-600 dark:text-black dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Buscar el id o n_informe"
                            />
                        </div>
                    </div>
                    <div
                        class="relative overflow-x-auto scroll-dataTableLEO shadow-lg sm:rounded-lg shadow-gray-400 dark:shadow-gray-500 mt-2"
                    >
                        <table
                            class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-white"
                        >
                            <thead
                                class="text-xs text-white uppercase bg-green-600 dark:bg-green-600"
                            >
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-center dark:border-white border-b-2"
                                    >
                                        N° Informe
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-center dark:border-white border-b-2"
                                    >
                                        Cliente
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-center dark:border-white border-b-2"
                                    >
                                        Descripcion
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-center dark:border-white border-b-2"
                                    >
                                        T. Bal
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-center dark:border-white border-b-2"
                                    >
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-center text-xs">
                                <template
                                    v-for="(
                                        filteredServicio, key
                                    ) in groupedServicios"
                                    :key="key"
                                >
                                    <tr
                                        class="bg-gray-300 dark:bg-gray-900"
                                        @dblclick="redirectToCreate(servicio)"
                                    >
                                        <td
                                            colspan="7"
                                            class="px-12 py-2 uppercase text-start font-bold text-gray-900 dark:text-white"
                                        >
                                            <strong>{{ key }}</strong>
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="(
                                            servicio, i
                                        ) in filteredServicio"
                                        :key="servicio.id"
                                        class="bg-white text-black border-b border-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer"
                                        @dblclick="redirectToCreate(servicio)"
                                    >
                                        <td
                                            class="px-6 py-4 text-center whitespace-nowrap"
                                        >
                                            {{ servicio.n_informe }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-center whitespace-nowrap"
                                        >
                                            {{
                                                servicio.cliente
                                                    ? servicio.cliente
                                                          .razonSocial
                                                    : "Sin marca"
                                            }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-center whitespace-nowrap"
                                        >
                                            {{ servicio.descripcion }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-center whitespace-nowrap"
                                        >
                                            <span
                                                class="text-gray-800 dark:text-white bg-yellow-500 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-500"
                                                >{{
                                                    $page.props.totalHservicio[
                                                        servicio.id
                                                    ] ?? "N/R"
                                                }}</span
                                            >
                                        </td>
                                        <td
                                            class="p-3 text-center whitespace-nowrap"
                                        >
                                            <button
                                                @click="
                                                    redirectToCreate(servicio)
                                                "
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded"
                                            >
                                                <i
                                                    class="fas fa-arrow-right fa-beat hover:text-white text-yellow-400 mx-2"
                                                ></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div
                        class="flex flex-wrap md:justify-between sm:justify-between justify-center"
                    >
                        <div class="hidden sm:block">
                            <div
                                class="flex flex-wrap mt-4 md:justify-between sm:justify-between justify-center gap-4 text-star"
                            >
                                <p
                                    class="text-gray-700 dark:text-white font-semibold"
                                >
                                    Registros por página: {{ countPerPage }}
                                </p>
                                <p
                                    class="text-gray-700 dark:text-white font-semibold"
                                >
                                    Total de Servicios no Cotizados:
                                    {{ totalCount }}
                                </p>

                                <p class="text-red-600">
                                    Página actual:
                                    {{ current_page }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 sm:text-end text-center">
                            <nav aria-label="Page navigation example mt-4">
                                <ul class="inline-flex -space-x-px text-sm">
                                    <li>
                                        <button
                                            @click="previousPage"
                                            :disabled="!servicios.prev_page_url"
                                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-700 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                                        >
                                            Prev
                                        </button>
                                    </li>
                                    <li v-for="page in total_pages" :key="page">
                                        <button
                                            @click="goToPage(page)"
                                            :class="{
                                                'text-blue-600 border-blue-300 dark:text-gray-900 bg-blue-50 hover:bg-blue-100 hover:text-blue-700':
                                                    page === current_page,
                                                'text-gray-900 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white':
                                                    page !== current_page,
                                            }"
                                            class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                                        >
                                            {{ page }}
                                        </button>
                                    </li>
                                    <li>
                                        <button
                                            @click="nextPage"
                                            :disabled="!servicios.next_page_url"
                                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-700 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                                        >
                                            Next
                                        </button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template> -->

<!-- v2 -->
<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { router, usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import debounce from "lodash/debounce";

const page = usePage();

function redirectToCreate(servicio) {
    router.visit(route("requiere.cotiza", servicio.id));
}

const props = defineProps({
    servicios: {
        type: Object,
        required: true,
    },
    users: Object,
    datos: Object,
});

// =====================
// State & Form Handling
// =====================
const searchQuery = ref("");
const currentPage = ref(props.servicios.current_page);
const reportsPerPage = 10;
searchQuery.value = page.props.search ?? "";

// const reports = ref(props.servicios.data ?? []);
// const filteredReports = ref([]);
// const selectedReport = ref(null);
// const showPreview = ref(false);
// const showNotification = ref(false);

// console.log("SERVICIOS RECIBIDOS:", props.servicios);
// searchQuery.value = page.props.search ?? '';

function fetchData(pageNumber = 1) {
    router.get(
        route("balanza"),
        {
            search: searchQuery.value,
            page: pageNumber,
        },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        }
    );
}

// Métodos para paginación
const totalPages = computed(() => props.servicios.last_page);
const startIndex = computed(
    () => (props.servicios.current_page - 1) * props.servicios.per_page
);
const endIndex = computed(() => startIndex.value + props.servicios.data.length);

// Lógica para generar los botones de página
const displayedPages = computed(() => {
    const pages = [];
    const maxVisible = 5;
    const current = props.servicios.current_page;
    const total = props.servicios.last_page;

    if (total <= maxVisible) {
        for (let i = 1; i <= total; i++) pages.push(i);
    } else {
        pages.push(1);
        let start = Math.max(2, current - 1);
        let end = Math.min(total - 1, current + 1);
        if (current <= 2) end = 4;
        if (current >= total - 1) start = total - 3;
        if (start > 2) pages.push("...");
        for (let i = start; i <= end; i++) pages.push(i);
        if (end < total - 1) pages.push("...");
        pages.push(total);
    }

    return pages;
});

const debouncedSearch = debounce(() => {
    fetchData(1); // Ir a la primera página
}, 400);

// =====================
// Lifecycle
// =====================
// onMounted(() => {
//     filteredReports.value = [...reports.value];
// });

// =====================
// Computed
// =====================
// const totalPages = computed(() =>
//     Math.ceil(filteredReports.value.length / reportsPerPage)
// );

// const paginatedReports = computed(() => {
//     const start = (currentPage.value - 1) * reportsPerPage;
//     return filteredReports.value.slice(start, start + reportsPerPage);
// });

// const startIndex = computed(() => (currentPage.value - 1) * reportsPerPage);
// const endIndex = computed(() => startIndex.value + reportsPerPage);

// console.log("PAGINATED REPORTS:", paginatedReports.value);
// console.log("reportes", reports.value);

// =====================
// Methods
// =====================
// function filterReports() {
//     if (!searchQuery.value) {
//         filteredReports.value = [...reports.value];
//     } else {
//         const query = searchQuery.value.toLowerCase();
//         filteredReports.value = reports.value.filter((report) =>
//             report.reportNumber?.toLowerCase().includes(query)
//         );
//     }
//     currentPage.value = 1;
// }

function formatDate(dateString) {
    if (!dateString) return "";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
}

// function selectReport(report) {
//     selectedReport.value = report;
//     quoteForm.value.clientName = report.client;
// }

// function backToReports() {
//     selectedReport.value = null;
//     resetQuoteForm();
// }

// function resetQuoteForm() {
//     quoteForm.value = {
//         clientName: '',
//         serviceLocation: '',
//         instrumentType: '',
//         technician: '',
//         make: '',
//         model: '',
//         series: '',
//         capacity: '',
//         division: '',
//         requirements: '',
//         startDate: '',
//         endDate: '',
//         technicalDiagnosis: '',
//         workToBePerformed: '',
//         photo: null,
//         photoPreview: null,
//     };
// }

// function triggerFileInput() {
//     document.getElementById('fileInput')?.click();
// }

// function handleFileUpload(event) {
//     const file = event.target.files[0];
//     if (!file) return;

//     quoteForm.value.photo = file;

//     const reader = new FileReader();
//     reader.onload = (e) => {
//         quoteForm.value.photoPreview = e.target.result;
//     };
//     reader.readAsDataURL(file);
// }

// function previewQuote() {
//     showPreview.value = true;
// }

// function generateQuoteNumber() {
//     const date = new Date();
//     const year = date.getFullYear();
//     const month = String(date.getMonth() + 1).padStart(2, '0');
//     const random = Math.floor(Math.random() * 1000)
//         .toString()
//         .padStart(3, '0');
//     return `Q-${year}${month}-${random}`;
// }

// function generateQuote() {
//     downloadPdf();
//     showNotification.value = true;
//     setTimeout(() => {
//         showNotification.value = false;
//     }, 3000);
//     showPreview.value = false;
//     backToReports();
// }

// function downloadPdf() {
//     alert('Simulación de descarga de PDF de la cotización.');
// }
</script>

<template>
    <AppLayout title="Hojas de Servicio no Cotizados">
        <template #header>
            <h1
                class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white"
            >
                Lista de Informes
            </h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800"
                >
                    <div
                        class="flex flex-wrap gap-2 uppercase text-sm justify-between"
                    >
                        <Link
                            :href="route('c_servicos.index')"
                            class="text-white bg-indigo-700 font-bold hover:bg-indigo-800 py-2 px-4 rounded md:w-min whitespace-nowrap w-full text-center"
                        >
                            <i class="fas fa-arrow-left mx-2"></i>Lista de
                            informes
                        </Link>
                    </div>
                    <div
                        class="w-full mt-4 p-4 bg-gray-200 border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                    >
                        <div class="mb-1 font-normal text-dark dark:text-white">
                            Lista de las hojas de servicio que aun no se han
                            cotizado.
                            <b
                                >Seleccione y luego Haga doble Click en el
                                informe a Cotizar</b
                            >:
                        </div>
                    </div>

                    <!-- Search Filter -->
                    <div class="my-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-grow">
                                <label
                                    for="reportSearch"
                                    class="block text-sm font-medium mb-1 dark:text-white"
                                    >Buscar por Razon Social</label
                                >
                                <input
                                    id="reportSearch"
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Ingresa el nombre"
                                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 text-gray-900"
                                    @input="debouncedSearch"
                                />
                            </div>
                            <!-- <div class="self-end">
                                <button
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-colors"
                                    @click="fetchData(1)"
                                >
                                    Buscar
                                </button>
                            </div> -->
                        </div>
                    </div>

                    <!-- Reports Table -->
                    <div class="overflow-x-auto bg-white rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-green-600 text-white">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                    >
                                        N° Informe
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                    >
                                        Razon Social
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                    >
                                        F. Registro
                                    </th>
                                    <!-- <th
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                    >
                                        Instrumento
                                    </th> -->
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                    >
                                        # Instrumentos
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                    >
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:text-white"
                            >
                                <tr
                                    v-for="report in props.servicios.data"
                                    :key="report.id"
                                    class="hover:bg-gray-500 hover:text-gray-50"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ report.id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{
                                            report.cliente
                                                ? report.cliente.razonSocial
                                                : "Sin marca"
                                        }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ formatDate(report.fecha) }}
                                    </td>
                                    <!-- <td class="px-6 py-4 whitespace-nowrap">
                                        {{ report.instrument }}
                                    </td> -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{
                                            $page.props.totalHservicio[
                                                report.id
                                            ] ?? "N/R"
                                        }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button
                                            class="px-3 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors"
                                            @click="redirectToCreate(report)"
                                        >
                                            <i
                                                class="fas fa-arrow-right fa-beat hover:text-white text-yellow-400 mx-2"
                                            ></i>
                                        </button>
                                    </td>
                                </tr>

                                <tr v-if="displayedPages.length === 0">
                                    <td
                                        colspan="6"
                                        class="px-6 py-4 text-center text-gray-500"
                                    >
                                        No se encontraron informes
                                    </td>
                                </tr>

                                <tr v-else-if="props.servicios.data.length === 0">
                                    <td
                                        colspan="6"
                                        class="px-6 py-4 text-center text-gray-500"
                                    >
                                        No se encontraron resultados para "{{
                                            searchQuery
                                        }}"
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center mt-4">
                        <div class="text-sm text-gray-700">
                        </div>
                        <div class="flex space-x-2 text-gray-800 dark:text-white">
                            <button
                                class="px-3 py-1 border rounded hover:bg-gray-400 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="props.servicios.current_page === 1"
                                @click="
                                    fetchData(props.servicios.current_page - 1)
                                "
                            >
                                Antes
                            </button>
                            <button
                                v-for="page in displayedPages"
                                :key="page"
                                class="px-3 py-1 border rounded hover:bg-gray-400 hover:text-gray-700"
                                :class="{
                                    'bg-emerald-600 text-white hover:bg-emerald-700':
                                        page === props.servicios.current_page,
                                    'cursor-default': page === '...',
                                }"
                                :disabled="page === '...'"
                                @click="
                                    page !== '...' &&
                                        page !== props.servicios.current_page &&
                                        fetchData(page)
                                "
                            >
                                {{ page }}
                            </button>
                            <button
                                class="px-3 py-1 border rounded hover:bg-gray-400 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="
                                    props.servicios.current_page ===
                                    props.servicios.last_page
                                "
                                @click="
                                    fetchData(props.servicios.current_page + 1)
                                "
                            >
                                Siguiente
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>

<!-- <script>
export default {
    data() {
        return {
            // Search and filtering
            searchQuery: "",
            filteredReports: [],

            // Pagination
            currentPage: 1,
            reportsPerPage: 10,

            // Selected report and quote form
            selectedReport: null,
            quoteForm: {
                clientName: "",
                serviceLocation: "",
                instrumentType: "",
                technician: "",
                make: "",
                model: "",
                series: "",
                capacity: "",
                division: "",
                requirements: "",
                startDate: "",
                endDate: "",
                technicalDiagnosis: "",
                workToBePerformed: "",
                photo: null,
                photoPreview: null,
            },

            // UI states
            showPreview: false,
            showNotification: false,

            // Mock data for reports

            reports: servicios,
        };
    },
    computed: {
        // Calculate total pages for pagination
        totalPages() {
            return Math.ceil(this.filteredReports.length / this.reportsPerPage);
        },

        // Get current page of reports
        paginatedReports() {
            const start = (this.currentPage - 1) * this.reportsPerPage;
            const end = start + this.reportsPerPage;
            return this.filteredReports.slice(start, end);
        },

        // Calculate start and end indices for display
        startIndex() {
            return (this.currentPage - 1) * this.reportsPerPage;
        },

        endIndex() {
            return this.startIndex + this.reportsPerPage;
        },
    },
    mounted() {
        // Initialize filtered reports with all reports
        this.filteredReports = [...this.reports];
    },
    methods: {
        // Filter reports based on search query
        filterReports() {
            if (!this.searchQuery) {
                this.filteredReports = [...this.reports];
            } else {
                this.filteredReports = this.reports.filter((report) =>
                    report.reportNumber
                        .toLowerCase()
                        .includes(this.searchQuery.toLowerCase())
                );
            }
            // Reset to first page when filtering
            this.currentPage = 1;
        },

        // Format date for display
        formatDate(dateString) {
            if (!dateString) return "";
            const date = new Date(dateString);
            return date.toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
        },

        // Select a report to generate quote
        selectReport(report) {
            this.selectedReport = report;
            // Pre-fill client information
            this.quoteForm.clientName = report.client;
        },

        // Go back to reports list
        backToReports() {
            this.selectedReport = null;
            this.resetQuoteForm();
        },

        // Reset quote form
        resetQuoteForm() {
            this.quoteForm = {
                clientName: "",
                serviceLocation: "",
                instrumentType: "",
                technician: "",
                make: "",
                model: "",
                series: "",
                capacity: "",
                division: "",
                requirements: "",
                startDate: "",
                endDate: "",
                technicalDiagnosis: "",
                workToBePerformed: "",
                photo: null,
                photoPreview: null,
            };
        },

        // Trigger file input click
        triggerFileInput() {
            this.$refs.fileInput.click();
        },

        // Handle file upload
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.quoteForm.photo = file;

            // Create preview
            const reader = new FileReader();
            reader.onload = (e) => {
                this.quoteForm.photoPreview = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        // Preview quote
        previewQuote() {
            this.showPreview = true;
        },

        // Generate quote number
        generateQuoteNumber() {
            const date = new Date();
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const random = Math.floor(Math.random() * 1000)
                .toString()
                .padStart(3, "0");
            return `Q-${year}${month}-${random}`;
        },

        // Generate and download quote
        generateQuote() {
            // Here you would typically send the data to your backend
            // For this example, we'll just show a success notification

            this.downloadPdf();

            // Show success notification
            this.showNotification = true;
            setTimeout(() => {
                this.showNotification = false;
            }, 3000);

            // Reset and go back to reports
            this.showPreview = false;
            this.backToReports();
        },

        // Download PDF
        downloadPdf() {
            // In a real application, you would use a library like jsPDF or html2pdf
            // For this example, we'll just simulate a download

            alert(
                "In a real application, this would generate and download a PDF of the quote."
            );

            // Example of how you might implement this with html2pdf (not included in this example)
            // if (typeof html2pdf !== 'undefined') {
            //   const element = this.$refs.pdfContent;
            //   const opt = {
            //     margin: 1,
            //     filename: `Quote-${this.generateQuoteNumber()}.pdf`,
            //     image: { type: 'jpeg', quality: 0.98 },
            //     html2canvas: { scale: 2 },
            //     jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            //   };
            //   html2pdf().set(opt).from(element).save();
            // }
        },
    },
};
</script> -->
