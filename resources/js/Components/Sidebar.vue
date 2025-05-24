<script setup>
import { ref, onMounted } from "vue";
import { Link, router } from "@inertiajs/vue3";

const isDarkMode = ref(false); // Estado para controlar el modo oscuro

// Función para cambiar el modo oscuro
const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    document.documentElement.classList.toggle("dark", isDarkMode.value);
    localStorage.setItem("modoOscuro", isDarkMode.value);
};

// Verificar si el modo oscuro está activado en localStorage al cargar el componente
onMounted(() => {
    const modoOscuroLocalStorage =
        localStorage.getItem("modoOscuro") === "true";
    isDarkMode.value = modoOscuroLocalStorage;
    document.documentElement.classList.toggle("dark", modoOscuroLocalStorage);
});

//Operacion para poder salir cerrar la sesion
const logout = () => {
    router.post(route("logout")); // Realiza la llamada para cerrar sesión
};
</script>

<template>
    <div
        class="min-h-[calc(100vh-50px)] bg-gray-800"
        id="side-bar"
        :class="dataOpenSideBar == true ? 'side-bar-visible' : 'side-bar-close'"
    >
        <div
            class="bg-gray-700 text-white dark:text-white h-[60px] flex justify-center items-center"
        >
            <Link rel="stylesheet" :href="route('dashboard')">
                <span
                    v-show="dataOpenSideBar"
                    class="gap-1 self-center text-2xl font-semibold whitespace-nowrap dark:text-white flex justify-center items-center"
                >
                    <div
                        class="border-dashed border-4 w-12 h-12 p-2 flex justify-center items-center rounded-full border-white"
                    >
                        <span
                            class="text-4xl flex justify-center items-center"
                            id="B_logo"
                            >B</span
                        >
                    </div>
                    <span id="nombre_Logo">ALINSA</span>
                </span>
            </Link>
            <div v-if="$page.props.auth.user.profile_photo_url">
                <img
                    v-show="!dataOpenSideBar"
                    class="p-1 w-8 h-8 rounded-full ring-2 ring-gray-100 object-cover"
                    :src="$page.props.auth.user.profile_photo_url"
                    :alt="$page.props.auth.user.name"
                />
            </div>
            <div v-else>
                <img
                    src="/img/hombre.png"
                    v-show="!dataOpenSideBar"
                    class="p-1 w-8 h-8 rounded-full ring-2 ring-gray-100 object-cover"
                    alt=""
                />
            </div>
        </div>
        <div
            class="flex flex-col justify-between h-[cal(100vh-3rem)] bg-gray-800"
        >
            <div class="menu-man text-left px-2 whitespace-nowrap">
                <div
                    :class="{
                        'profile flex justify-center items-center text-center p-4':
                            dataOpenSideBar,
                        'flex justify-center items-center text-center':
                            !dataOpenSideBar,
                    }"
                >
                    <div
                        class="flex flex-col justify-center items-center gap-3 text-white"
                        v-show="dataOpenSideBar"
                    >
                        <!-- Verifica si hay una imagen de perfil subida -->
                        <div
                            v-if="$page.props.auth.user.profile_photo_url"
                            class="shrink-0 me-3"
                        >
                            <img
                                class="p-1 w-24 h-24 rounded-full ring-2 ring-gray-300 object-cover"
                                :src="$page.props.auth.user.profile_photo_url"
                                :alt="$page.props.auth.user.name"
                            />
                        </div>
                        <!-- Si no hay imagen subida, muestra la imagen predeterminada -->
                        <div v-else>
                            <img
                                src="/img/hombre.png"
                                class="p-1 w-24 h-24 rounded-full ring-2 ring-gray-300"
                                alt=""
                            />
                        </div>
                        <div>
                            <Link :href="route('profile.show')"
                                ><h5 class="text-xl font-medium leading-tight">
                                    {{ $page.props.auth.user.name }}
                                </h5></Link
                            >
                            <p class="text-gray-500">
                                {{ $page.props.auth.user.roles[0]?.name }}
                            </p>
                            <!-- <p
                                v-if="
                                    $page.props.auth.user.roles[0]?.name ==
                                    'Administracion'
                                "
                                class="text-gray-500"
                            >
                                Bienvenido Jefe
                            </p>
                            <p v-else class="text-gray-500">Otro Rol</p> -->
                        </div>
                    </div>
                </div>
                <!-- Scrool para sidebar -->

                <!-- SideBar Ventas -->

                <div
                    v-if="
                        $page.props.auth.user.roles[0]?.name ==
                        'Asesor de Ventas'
                    "
                    :class="{
                        'scroll-sidebar h-[calc(100vh-415px)] overflow-x-auto':
                            dataOpenSideBar,
                        'h-[calc(100vh-205px)] overflow-hidden':
                            !dataOpenSideBar,
                    }"
                >
                    <Link
                        :href="route('clientes.index')"
                        :active="route().current('clientes.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="0.94em"
                            height="1em"
                            viewBox="0 0 15 16"
                        >
                            <path
                                fill="currentColor"
                                d="M7.5 7a2.5 2.5 0 0 1 0-5a2.5 2.5 0 0 1 0 5m0-4C6.67 3 6 3.67 6 4.5S6.67 6 7.5 6S9 5.33 9 4.5S8.33 3 7.5 3"
                            />
                            <path
                                fill="currentColor"
                                d="M13.5 11c-.28 0-.5-.22-.5-.5s.22-.5.5-.5s.5-.22.5-.5A2.5 2.5 0 0 0 11.5 7h-1c-.28 0-.5-.22-.5-.5s.22-.5.5-.5c.83 0 1.5-.67 1.5-1.5S11.33 3 10.5 3c-.28 0-.5-.22-.5-.5s.22-.5.5-.5A2.5 2.5 0 0 1 13 4.5c0 .62-.22 1.18-.6 1.62c1.49.4 2.6 1.76 2.6 3.38c0 .83-.67 1.5-1.5 1.5m-12 0C.67 11 0 10.33 0 9.5c0-1.62 1.1-2.98 2.6-3.38c-.37-.44-.6-1-.6-1.62A2.5 2.5 0 0 1 4.5 2c.28 0 .5.22.5.5s-.22.5-.5.5C3.67 3 3 3.67 3 4.5S3.67 6 4.5 6c.28 0 .5.22.5.5s-.22.5-.5.5h-1A2.5 2.5 0 0 0 1 9.5c0 .28.22.5.5.5s.5.22.5.5s-.22.5-.5.5m9 3h-6c-.83 0-1.5-.67-1.5-1.5v-1C3 9.57 4.57 8 6.5 8h2c1.93 0 3.5 1.57 3.5 3.5v1c0 .83-.67 1.5-1.5 1.5m-4-5A2.5 2.5 0 0 0 4 11.5v1c0 .28.22.5.5.5h6c.28 0 .5-.22.5-.5v-1A2.5 2.5 0 0 0 8.5 9z"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Listar Clientes
                        </span>
                    </Link>
                    <Link
                        :href="route('tbproductos.index')"
                        :active="route().current('tbproductos.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-base"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill="currentColor"
                                d="M0 1h4.764l.545 2h18.078l-3.666 11H7.78l-.5 2H22v2H4.72l1.246-4.989L3.236 3H0zm7.764 11h10.515l2.334-7H5.855zM4 21a2 2 0 1 1 4 0a2 2 0 0 1-4 0m14 0a2 2 0 1 1 4 0a2 2 0 0 1-4 0"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Catalago de Productos
                        </span>
                    </Link>
                    <Link
                        :href="route('cuadropendientes.index')"
                        :active="route().current('cuadropendientes.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-base"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 16 16"
                        >
                            <path
                                fill="currentColor"
                                d="M13.5 5.88c-.28 0-.5-.22-.5-.5V1.5c0-.28-.22-.5-.5-.5h-9c-.28 0-.5.22-.5.5v2c0 .28-.22.5-.5.5S2 3.78 2 3.5v-2C2 .67 2.67 0 3.5 0h9c.83 0 1.5.67 1.5 1.5v3.88c0 .28-.22.5-.5.5"
                            />
                            <path
                                fill="currentColor"
                                d="M14.5 16h-13C.67 16 0 15.33 0 14.5v-10C0 3.67.67 3 1.5 3h4.75c.16 0 .31.07.4.2L8 5h6.5c.83 0 1.5.67 1.5 1.5v8c0 .83-.67 1.5-1.5 1.5M1.5 4c-.28 0-.5.22-.5.5v10c0 .28.22.5.5.5h13c.28 0 .5-.22.5-.5v-8c0-.28-.22-.5-.5-.5H7.75a.48.48 0 0 1-.4-.2L6 4z"
                            />
                            <path
                                fill="currentColor"
                                d="M5.5 13h-2c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h2c.28 0 .5.22.5.5s-.22.5-.5.5"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2.5">
                            1.- Cuadro de Pendientes
                        </span>
                    </Link>
                    <!-- <Link
                        v-if="
                            $page.props.user.roles.includes('Administracion') ||
                            $page.props.user.roles.includes('Gerencia') ||
                            $page.props.user.roles.includes('Servicio Tecnico')
                        "
                        :href="route('pendientestecnicos.index')"
                        :active="route().current('pendientestecnicos.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-1.5 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill="currentColor"
                                d="M17 22q-2.075 0-3.537-1.463T12 17t1.463-3.537T17 12t3.538 1.463T22 17t-1.463 3.538T17 22m1.675-2.625l.7-.7L17.5 16.8V14h-1v3.2zM5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h4.175q.275-.875 1.075-1.437T12 1q1 0 1.788.563T14.85 3H19q.825 0 1.413.588T21 5v6.25q-.45-.325-.95-.55T19 10.3V5h-2v3H7V5H5v14h5.3q.175.55.4 1.05t.55.95zm7-16q.425 0 .713-.288T13 4t-.288-.712T12 3t-.712.288T11 4t.288.713T12 5"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2.5">
                            2.- Cuadro de Pendientes
                        </span>
                    </Link> -->
                    <!-- <details
                        v-if="
                            $page.props.user.roles.includes('Administracion') ||
                            $page.props.user.roles.includes('Gerencia') ||
                            $page.props.user.roles.includes('Servicio Tecnico')
                        "
                        class="py-3 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary
                            class="px-2 flex items-center space-x-2 hover:text-white"
                        >
                            <svg
                                class="text-[18px]"
                                xmlns="http://www.w3.org/2000/svg"
                                width="1em"
                                height="1em"
                                viewBox="0 0 2048 2048"
                            >
                                <path
                                    fill="currentColor"
                                    d="M1185 1179q-88-75-195-115t-222-40q-88 0-170 23t-153 64t-129 100t-100 130t-65 153t-23 170H0q0-120 35-231t101-205t156-167t204-115q-113-74-176-186t-64-248q0-106 40-199t109-163T568 40T768 0t199 40t163 109t110 163t40 200q0 66-16 129t-48 119t-75 103t-101 83q65 25 124 61t111 81zM384 512q0 80 30 149t82 122t122 83t150 30q79 0 149-30t122-82t83-122t30-150q0-79-30-149t-82-122t-123-83t-149-30q-80 0-149 30t-122 82t-83 123t-30 149m1344 256q66 0 124 25t101 69t69 102t26 124t-25 124t-69 102t-102 69t-124 25q-23 0-45-3l-587 587q-27 27-62 41t-74 15q-40 0-75-15t-61-41t-41-61t-15-75q0-38 14-73t42-63l587-587q-3-22-3-45q0-66 25-124t68-101t102-69t125-26m0 512q40 0 75-15t61-41t41-61t15-75q0-41-19-82l-146 146h-91v-91l146-146q-41-19-82-19q-40 0-75 15t-61 41t-41 61t-15 75q0 41 19 82l-640 641q-19 19-19 45t19 45t45 19t45-19l641-640q41 19 82 19"
                                />
                            </svg>
                            <span v-show="dataOpenSideBar" class="ml-2">
                                Servicio Tecnico
                            </span>
                        </summary>
                        <Link
                            :href="route('hservicios.index')"
                            :active="route().current('hservicios.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >4.- Informes Tecnico</span
                            ></Link
                        >
                        <Link
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Registrar Informe Tecnico</span
                            ></Link
                        >
                        <a
                            href="https://onedrive.live.com/?authkey=%21AD9JIIeIEy788PM&id=D9E6E0F5EA45EB0A%216187&cid=D9E6E0F5EA45EB0A"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"
                            target="_blank"
                            rel="noopener noreferrer"
                            ><span class="ml-4">Manuales y Calibración</span
                            ><img src="/img/one_drive.png" class="w-4"
                        /></a>
                    </details> -->
                    <!-- <Link
                        v-if="
                            $page.props.user.roles.includes('Administracion') ||
                            $page.props.user.roles.includes('Gerencia')
                        "
                        :href="route('actasConformidad.index')"
                        :active="route().current('actasConformidad.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 24 24"
                        >
                            <g
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path
                                    d="m18.18 8.04l.463-.464a1.966 1.966 0 1 1 2.781 2.78l-.463.464M18.18 8.04s.058.984.927 1.853s1.854.927 1.854.927M18.18 8.04l-4.26 4.26c-.29.288-.434.433-.558.592q-.22.282-.374.606c-.087.182-.151.375-.28.762l-.413 1.24l-.134.401m8.8-5.081l-4.26 4.26c-.29.29-.434.434-.593.558q-.282.22-.606.374c-.182.087-.375.151-.762.28l-1.24.413l-.401.134m0 0l-.401.134a.53.53 0 0 1-.67-.67l.133-.402m.938.938l-.938-.938"
                                />
                                <path
                                    stroke-linecap="round"
                                    d="M8 13h2.5M8 9h6.5M8 17h1.5M3 14v-4c0-3.771 0-5.657 1.172-6.828S7.229 2 11 2h2c3.771 0 5.657 0 6.828 1.172M21 14c0 3.771 0 5.657-1.172 6.828m-15.656 0C5.343 22 7.229 22 11 22h2c3.771 0 5.657 0 6.828-1.172m0 0c.944-.943 1.127-2.348 1.163-4.828"
                                />
                            </g>
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Actas de Conformidad
                        </span>
                    </Link> -->

                    <details
                        class="py-3 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary
                            class="px-2 flex items-center space-x-2 hover:text-white"
                        >
                            <svg
                                class="text-[18px]"
                                xmlns="http://www.w3.org/2000/svg"
                                width="1em"
                                height="1em"
                                viewBox="0 0 2048 2048"
                            >
                                <path
                                    fill="currentColor"
                                    d="M1185 1179q-88-75-195-115t-222-40q-88 0-170 23t-153 64t-129 100t-100 130t-65 153t-23 170H0q0-120 35-231t101-205t156-167t204-115q-113-74-176-186t-64-248q0-106 40-199t109-163T568 40T768 0t199 40t163 109t110 163t40 200q0 66-16 129t-48 119t-75 103t-101 83q65 25 124 61t111 81zM384 512q0 80 30 149t82 122t122 83t150 30q79 0 149-30t122-82t83-122t30-150q0-79-30-149t-82-122t-123-83t-149-30q-80 0-149 30t-122 82t-83 123t-30 149m1344 256q66 0 124 25t101 69t69 102t26 124t-25 124t-69 102t-102 69t-124 25q-23 0-45-3l-587 587q-27 27-62 41t-74 15q-40 0-75-15t-61-41t-41-61t-15-75q0-38 14-73t42-63l587-587q-3-22-3-45q0-66 25-124t68-101t102-69t125-26m0 512q40 0 75-15t61-41t41-61t15-75q0-41-19-82l-146 146h-91v-91l146-146q-41-19-82-19q-40 0-75 15t-61 41t-41 61t-15 75q0 41 19 82l-640 641q-19 19-19 45t19 45t45 19t45-19l641-640q41 19 82 19"
                                />
                            </svg>
                            <span v-show="dataOpenSideBar" class="ml-2">
                                Certificaciones
                            </span>
                        </summary>
                        <!-- <Link
                            v-if="
                                $page.props.user.roles.includes(
                                    'Administracion'
                                ) || $page.props.user.roles.includes('Gerencia')
                            "
                            :href="route('hservicios.index')"
                            :active="route().current('hservicios.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Registrar Solicitud de Certificacion</span
                            ></Link
                        > -->
                        <!-- <Link
                            v-if="
                                $page.props.user.roles.includes(
                                    'Administracion'
                                ) || $page.props.user.roles.includes('Gerencia')
                            "
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Solicitudes Emitidas</span
                            ></Link
                        > -->
                        <Link
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Certificaciones Emitidas</span
                            ></Link
                        >
                    </details>

                    <details
                        class="py-3.5 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary class="px-2 flex space-x-2 hover:text-white">
                            <span class="bi bi-graph-up-arrow"
                                ><span v-show="dataOpenSideBar" class="ml-2"
                                    >Cotizaciones</span
                                ></span
                            >
                        </summary>
                        <Link
                            :href="route('cventas.index')"
                            :active="route().current('cventas.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Listar Cotizacion por Venta</span
                            ></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Listar Cotizacion por Servicio</span
                            ></Link
                        >
                    </details>
                    <!-- <details
                        v-if="
                            $page.props.user.roles.includes(
                                'Area de Almacen'
                            ) ||
                            $page.props.user.roles.includes('Administracion')
                        "
                        class="py-3.5 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary class="px-2 flex space-x-2 hover:text-white">
                            <span class="bi bi-graph-up-arrow"
                                ><span v-show="dataOpenSideBar" class="ml-2"
                                    >Almacen</span
                                ></span
                            >
                        </summary>
                        <Link
                            :href="route('categories.index')"
                            :active="route().current('productos.*')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4">Inventario</span></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Ingreso de Materiales</span
                            ></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Salida de Materiales</span
                            ></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4">Generar Kardex</span></Link
                        >
                    </details> -->
                    <!-- <details class="py-3.5 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-bar-chart-line'><span v-show="dataOpenSideBar" class="ml-2">Prestamos o Alquiler</span></span></summary>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Listar Equipos</span> <img src="../../../public/img/excel.png" class="w-4"></Link>
                        </details> -->
                    <!-- <details class="py-3.5 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-patch-check'><span v-show="dataOpenSideBar" class="ml-2">Certificaciones</span></span></summary>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Registrar Solicitud Certificacion</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Solicitudes Emitidas</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Certificaciones Emitidas</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Certificaciones por Caducar</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Lista Certificados emitidos TW</span> <img src="../../../public/img/excel.png" class="w-4"></Link>
                        </details> -->
                    <!-- <Link
                        v-if="
                            $page.props.user.roles.includes('Administracion') ||
                            $page.props.user.roles.includes('Area de TI') ||
                            $page.props.user.roles.includes('Gerencia')
                        "
                        :href="route('cajaChica.index')"
                        :active="route().current('cajaChica.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="22"
                            height="24"
                            viewBox="0 0 24 24"
                        >
                            <g
                                fill="none"
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                color="currentColor"
                            >
                                <path
                                    d="M20.943 16.835a15.76 15.76 0 0 0-4.476-8.616c-.517-.503-.775-.754-1.346-.986C14.55 7 14.059 7 13.078 7h-2.156c-.981 0-1.472 0-2.043.233c-.57.232-.83.483-1.346.986a15.76 15.76 0 0 0-4.476 8.616C2.57 19.773 5.28 22 8.308 22h7.384c3.029 0 5.74-2.227 5.25-5.165"
                                />
                                <path
                                    d="M7.257 4.443c-.207-.3-.506-.708.112-.8c.635-.096 1.294.338 1.94.33c.583-.009.88-.268 1.2-.638C10.845 2.946 11.365 2 12 2s1.155.946 1.491 1.335c.32.37.617.63 1.2.637c.646.01 1.305-.425 1.94-.33c.618.093.319.5.112.8l-.932 1.359c-.4.58-.599.87-1.017 1.035S13.837 7 12.758 7h-1.516c-1.08 0-1.619 0-2.036-.164S8.589 6.38 8.189 5.8zm6.37 8.476c-.216-.799-1.317-1.519-2.638-.98s-1.53 2.272.467 2.457c.904.083 1.492-.097 2.031.412c.54.508.64 1.923-.739 2.304c-1.377.381-2.742-.214-2.89-1.06m1.984-5.06v.761m0 5.476v.764"
                                />
                            </g>
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Caja Chica
                        </span>
                    </Link> -->
                    <!-- <Link
                        v-if="
                            $page.props.user.roles.includes('Administracion') ||
                            $page.props.user.roles.includes('Area de TI') ||
                            $page.props.user.roles.includes('Gerencia')
                        "
                        :href="route('users.index')"
                        :active="route().current('users.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 18 18"
                        >
                            <g fill="currentColor" fill-rule="evenodd">
                                <path
                                    d="M13.689 11.132c1.155 1.222 1.953 2.879 2.183 4.748a1.007 1.007 0 0 1-1 1.12H3.007a1.005 1.005 0 0 1-1-1.12c.23-1.87 1.028-3.526 2.183-4.748c.247.228.505.442.782.633c-1.038 1.069-1.765 2.55-1.972 4.237L14.872 16c-.204-1.686-.93-3.166-1.966-4.235a7 7 0 0 0 .783-.633M8.939 1c1.9 0 3 2 4.38 2.633a2.48 2.48 0 0 1-1.88.867c-.298 0-.579-.06-.844-.157A3.73 3.73 0 0 1 7.69 5.75c-1.395 0-3.75.25-3.245-1.903C5.94 3 6.952 1 8.94 1"
                                />
                                <path
                                    d="M8.94 2c2.205 0 4 1.794 4 4s-1.795 4-4 4c-2.207 0-4-1.794-4-4s1.793-4 4-4m0 9A5 5 0 1 0 8.937.999A5 5 0 0 0 8.94 11"
                                />
                            </g>
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Usuarios
                        </span>
                    </Link> -->
                    <!-- <Link
                        :href="route('categories.index')"
                        :active="route().current('productos.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <span class="bi bi-inboxes"></span>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Almacen
                        </span>
                    </Link> -->
                    <!-- <details class="py-3.5 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-inboxes'><span v-show="dataOpenSideBar" class="ml-2">Recursos</span></span></summary>
                            <Link :href="route('categories.index')" :active="route().current('productos.*')" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white"><span class="ml-4">Inventario</span></Link>
                            <p v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Reuniones Semanales</span> <img src="../../../public/img/excel.png" class="w-4"></p>
                            <p v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Ocurrencias</span> <img src="../../../public/img/excel.png" class="w-4"></p>
                        </details> -->
                </div>

                <!-- SideBar Administracion -->

                <div
                    v-else-if="
                        $page.props.auth.user.roles[0]?.name == 'Administracion'
                    "
                    :class="{
                        'scroll-sidebar h-[calc(100vh-415px)] overflow-x-auto':
                            dataOpenSideBar,
                        'h-[calc(100vh-205px)] overflow-hidden':
                            !dataOpenSideBar,
                    }"
                >
                    <Link
                        :href="route('clientes.index')"
                        :active="route().current('clientes.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="0.94em"
                            height="1em"
                            viewBox="0 0 15 16"
                        >
                            <path
                                fill="currentColor"
                                d="M7.5 7a2.5 2.5 0 0 1 0-5a2.5 2.5 0 0 1 0 5m0-4C6.67 3 6 3.67 6 4.5S6.67 6 7.5 6S9 5.33 9 4.5S8.33 3 7.5 3"
                            />
                            <path
                                fill="currentColor"
                                d="M13.5 11c-.28 0-.5-.22-.5-.5s.22-.5.5-.5s.5-.22.5-.5A2.5 2.5 0 0 0 11.5 7h-1c-.28 0-.5-.22-.5-.5s.22-.5.5-.5c.83 0 1.5-.67 1.5-1.5S11.33 3 10.5 3c-.28 0-.5-.22-.5-.5s.22-.5.5-.5A2.5 2.5 0 0 1 13 4.5c0 .62-.22 1.18-.6 1.62c1.49.4 2.6 1.76 2.6 3.38c0 .83-.67 1.5-1.5 1.5m-12 0C.67 11 0 10.33 0 9.5c0-1.62 1.1-2.98 2.6-3.38c-.37-.44-.6-1-.6-1.62A2.5 2.5 0 0 1 4.5 2c.28 0 .5.22.5.5s-.22.5-.5.5C3.67 3 3 3.67 3 4.5S3.67 6 4.5 6c.28 0 .5.22.5.5s-.22.5-.5.5h-1A2.5 2.5 0 0 0 1 9.5c0 .28.22.5.5.5s.5.22.5.5s-.22.5-.5.5m9 3h-6c-.83 0-1.5-.67-1.5-1.5v-1C3 9.57 4.57 8 6.5 8h2c1.93 0 3.5 1.57 3.5 3.5v1c0 .83-.67 1.5-1.5 1.5m-4-5A2.5 2.5 0 0 0 4 11.5v1c0 .28.22.5.5.5h6c.28 0 .5-.22.5-.5v-1A2.5 2.5 0 0 0 8.5 9z"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Listar Clientes
                        </span>
                    </Link>
                    <Link
                        :href="route('tbproductos.index')"
                        :active="route().current('tbproductos.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-base"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill="currentColor"
                                d="M0 1h4.764l.545 2h18.078l-3.666 11H7.78l-.5 2H22v2H4.72l1.246-4.989L3.236 3H0zm7.764 11h10.515l2.334-7H5.855zM4 21a2 2 0 1 1 4 0a2 2 0 0 1-4 0m14 0a2 2 0 1 1 4 0a2 2 0 0 1-4 0"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Catalago de Productos
                        </span>
                    </Link>
                    <Link
                        :href="route('cuadropendientes.index')"
                        :active="route().current('cuadropendientes.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-base"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 16 16"
                        >
                            <path
                                fill="currentColor"
                                d="M13.5 5.88c-.28 0-.5-.22-.5-.5V1.5c0-.28-.22-.5-.5-.5h-9c-.28 0-.5.22-.5.5v2c0 .28-.22.5-.5.5S2 3.78 2 3.5v-2C2 .67 2.67 0 3.5 0h9c.83 0 1.5.67 1.5 1.5v3.88c0 .28-.22.5-.5.5"
                            />
                            <path
                                fill="currentColor"
                                d="M14.5 16h-13C.67 16 0 15.33 0 14.5v-10C0 3.67.67 3 1.5 3h4.75c.16 0 .31.07.4.2L8 5h6.5c.83 0 1.5.67 1.5 1.5v8c0 .83-.67 1.5-1.5 1.5M1.5 4c-.28 0-.5.22-.5.5v10c0 .28.22.5.5.5h13c.28 0 .5-.22.5-.5v-8c0-.28-.22-.5-.5-.5H7.75a.48.48 0 0 1-.4-.2L6 4z"
                            />
                            <path
                                fill="currentColor"
                                d="M5.5 13h-2c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h2c.28 0 .5.22.5.5s-.22.5-.5.5"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2.5">
                            1.- Cuadro de Pendientes
                        </span>
                    </Link>
                    <Link
                        :href="route('pendientestecnicos.index')"
                        :active="route().current('pendientestecnicos.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-1.5 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill="currentColor"
                                d="M17 22q-2.075 0-3.537-1.463T12 17t1.463-3.537T17 12t3.538 1.463T22 17t-1.463 3.538T17 22m1.675-2.625l.7-.7L17.5 16.8V14h-1v3.2zM5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h4.175q.275-.875 1.075-1.437T12 1q1 0 1.788.563T14.85 3H19q.825 0 1.413.588T21 5v6.25q-.45-.325-.95-.55T19 10.3V5h-2v3H7V5H5v14h5.3q.175.55.4 1.05t.55.95zm7-16q.425 0 .713-.288T13 4t-.288-.712T12 3t-.712.288T11 4t.288.713T12 5"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2.5">
                            2.- Cuadro de Pendientes
                        </span>
                    </Link>
                    <details
                        class="py-3 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary
                            class="px-2 flex items-center space-x-2 hover:text-white"
                        >
                            <svg
                                class="text-[18px]"
                                xmlns="http://www.w3.org/2000/svg"
                                width="1em"
                                height="1em"
                                viewBox="0 0 2048 2048"
                            >
                                <path
                                    fill="currentColor"
                                    d="M1185 1179q-88-75-195-115t-222-40q-88 0-170 23t-153 64t-129 100t-100 130t-65 153t-23 170H0q0-120 35-231t101-205t156-167t204-115q-113-74-176-186t-64-248q0-106 40-199t109-163T568 40T768 0t199 40t163 109t110 163t40 200q0 66-16 129t-48 119t-75 103t-101 83q65 25 124 61t111 81zM384 512q0 80 30 149t82 122t122 83t150 30q79 0 149-30t122-82t83-122t30-150q0-79-30-149t-82-122t-123-83t-149-30q-80 0-149 30t-122 82t-83 123t-30 149m1344 256q66 0 124 25t101 69t69 102t26 124t-25 124t-69 102t-102 69t-124 25q-23 0-45-3l-587 587q-27 27-62 41t-74 15q-40 0-75-15t-61-41t-41-61t-15-75q0-38 14-73t42-63l587-587q-3-22-3-45q0-66 25-124t68-101t102-69t125-26m0 512q40 0 75-15t61-41t41-61t15-75q0-41-19-82l-146 146h-91v-91l146-146q-41-19-82-19q-40 0-75 15t-61 41t-41 61t-15 75q0 41 19 82l-640 641q-19 19-19 45t19 45t45 19t45-19l641-640q41 19 82 19"
                                />
                            </svg>
                            <span v-show="dataOpenSideBar" class="ml-2">
                                Servicio Tecnico
                            </span>
                        </summary>
                        <Link
                            :href="route('hservicios.index')"
                            :active="route().current('hservicios.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >4.- Informes Tecnico</span
                            ></Link
                        >
                        <Link
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Registrar Informe Tecnico</span
                            ></Link
                        >
                        <a
                            href="https://onedrive.live.com/?authkey=%21AD9JIIeIEy788PM&id=D9E6E0F5EA45EB0A%216187&cid=D9E6E0F5EA45EB0A"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"
                            target="_blank"
                            rel="noopener noreferrer"
                            ><span class="ml-4">Manuales y Calibración</span
                            ><img src="/img/one_drive.png" class="w-4"
                        /></a>
                    </details>
                    <Link
                        :href="route('actasConformidad.index')"
                        :active="route().current('actasConformidad.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 24 24"
                        >
                            <g
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path
                                    d="m18.18 8.04l.463-.464a1.966 1.966 0 1 1 2.781 2.78l-.463.464M18.18 8.04s.058.984.927 1.853s1.854.927 1.854.927M18.18 8.04l-4.26 4.26c-.29.288-.434.433-.558.592q-.22.282-.374.606c-.087.182-.151.375-.28.762l-.413 1.24l-.134.401m8.8-5.081l-4.26 4.26c-.29.29-.434.434-.593.558q-.282.22-.606.374c-.182.087-.375.151-.762.28l-1.24.413l-.401.134m0 0l-.401.134a.53.53 0 0 1-.67-.67l.133-.402m.938.938l-.938-.938"
                                />
                                <path
                                    stroke-linecap="round"
                                    d="M8 13h2.5M8 9h6.5M8 17h1.5M3 14v-4c0-3.771 0-5.657 1.172-6.828S7.229 2 11 2h2c3.771 0 5.657 0 6.828 1.172M21 14c0 3.771 0 5.657-1.172 6.828m-15.656 0C5.343 22 7.229 22 11 22h2c3.771 0 5.657 0 6.828-1.172m0 0c.944-.943 1.127-2.348 1.163-4.828"
                                />
                            </g>
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Actas de Conformidad
                        </span>
                    </Link>

                    <details
                        class="py-3 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary
                            class="px-2 flex items-center space-x-2 hover:text-white"
                        >
                            <svg
                                class="text-[18px]"
                                xmlns="http://www.w3.org/2000/svg"
                                width="1em"
                                height="1em"
                                viewBox="0 0 2048 2048"
                            >
                                <path
                                    fill="currentColor"
                                    d="M1185 1179q-88-75-195-115t-222-40q-88 0-170 23t-153 64t-129 100t-100 130t-65 153t-23 170H0q0-120 35-231t101-205t156-167t204-115q-113-74-176-186t-64-248q0-106 40-199t109-163T568 40T768 0t199 40t163 109t110 163t40 200q0 66-16 129t-48 119t-75 103t-101 83q65 25 124 61t111 81zM384 512q0 80 30 149t82 122t122 83t150 30q79 0 149-30t122-82t83-122t30-150q0-79-30-149t-82-122t-123-83t-149-30q-80 0-149 30t-122 82t-83 123t-30 149m1344 256q66 0 124 25t101 69t69 102t26 124t-25 124t-69 102t-102 69t-124 25q-23 0-45-3l-587 587q-27 27-62 41t-74 15q-40 0-75-15t-61-41t-41-61t-15-75q0-38 14-73t42-63l587-587q-3-22-3-45q0-66 25-124t68-101t102-69t125-26m0 512q40 0 75-15t61-41t41-61t15-75q0-41-19-82l-146 146h-91v-91l146-146q-41-19-82-19q-40 0-75 15t-61 41t-41 61t-15 75q0 41 19 82l-640 641q-19 19-19 45t19 45t45 19t45-19l641-640q41 19 82 19"
                                />
                            </svg>
                            <span v-show="dataOpenSideBar" class="ml-2">
                                Certificaciones
                            </span>
                        </summary>
                        <Link
                            :href="route('hservicios.index')"
                            :active="route().current('hservicios.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Registrar Solicitud de Certificacion</span
                            ></Link
                        >
                        <Link
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Solicitudes Emitidas</span
                            ></Link
                        >
                        <Link
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Certificaciones Emitidas</span
                            ></Link
                        >
                    </details>

                    <details
                        class="py-3.5 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary class="px-2 flex space-x-2 hover:text-white">
                            <span class="bi bi-graph-up-arrow"
                                ><span v-show="dataOpenSideBar" class="ml-2"
                                    >Cotizaciones</span
                                ></span
                            >
                        </summary>
                        <Link
                            :href="route('cventas.index')"
                            :active="route().current('cventas.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Listar Cotizacion por Venta</span
                            ></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Listar Cotizacion por Servicio</span
                            ></Link
                        >
                    </details>
                    <details
                        class="py-3.5 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary class="px-2 flex space-x-2 hover:text-white">
                            <span class="bi bi-graph-up-arrow"
                                ><span v-show="dataOpenSideBar" class="ml-2"
                                    >Almacen</span
                                ></span
                            >
                        </summary>
                        <Link
                            :href="route('categories.index')"
                            :active="route().current('productos.*')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4">Inventario</span></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Ingreso de Materiales</span
                            ></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Salida de Materiales</span
                            ></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4">Generar Kardex</span></Link
                        >
                    </details>
                    <!-- <details class="py-3.5 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-bar-chart-line'><span v-show="dataOpenSideBar" class="ml-2">Prestamos o Alquiler</span></span></summary>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Listar Equipos</span> <img src="../../../public/img/excel.png" class="w-4"></Link>
                        </details> -->
                    <!-- <details class="py-3.5 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-patch-check'><span v-show="dataOpenSideBar" class="ml-2">Certificaciones</span></span></summary>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Registrar Solicitud Certificacion</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Solicitudes Emitidas</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Certificaciones Emitidas</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Certificaciones por Caducar</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Lista Certificados emitidos TW</span> <img src="../../../public/img/excel.png" class="w-4"></Link>
                        </details> -->
                    <Link
                        :href="route('cajaChica.index')"
                        :active="route().current('cajaChica.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="22"
                            height="24"
                            viewBox="0 0 24 24"
                        >
                            <g
                                fill="none"
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                color="currentColor"
                            >
                                <path
                                    d="M20.943 16.835a15.76 15.76 0 0 0-4.476-8.616c-.517-.503-.775-.754-1.346-.986C14.55 7 14.059 7 13.078 7h-2.156c-.981 0-1.472 0-2.043.233c-.57.232-.83.483-1.346.986a15.76 15.76 0 0 0-4.476 8.616C2.57 19.773 5.28 22 8.308 22h7.384c3.029 0 5.74-2.227 5.25-5.165"
                                />
                                <path
                                    d="M7.257 4.443c-.207-.3-.506-.708.112-.8c.635-.096 1.294.338 1.94.33c.583-.009.88-.268 1.2-.638C10.845 2.946 11.365 2 12 2s1.155.946 1.491 1.335c.32.37.617.63 1.2.637c.646.01 1.305-.425 1.94-.33c.618.093.319.5.112.8l-.932 1.359c-.4.58-.599.87-1.017 1.035S13.837 7 12.758 7h-1.516c-1.08 0-1.619 0-2.036-.164S8.589 6.38 8.189 5.8zm6.37 8.476c-.216-.799-1.317-1.519-2.638-.98s-1.53 2.272.467 2.457c.904.083 1.492-.097 2.031.412c.54.508.64 1.923-.739 2.304c-1.377.381-2.742-.214-2.89-1.06m1.984-5.06v.761m0 5.476v.764"
                                />
                            </g>
                        </svg>
                        <!-- <svg :class="'text-4xl'" xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 48 48"><g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"><path d="M28.772 24.667A4 4 0 0 0 25 22v-1h-2v1a4 4 0 1 0 0 8v4c-.87 0-1.611-.555-1.887-1.333a1 1 0 1 0-1.885.666A4 4 0 0 0 23 36v1h2v-1a4 4 0 0 0 0-8v-4a2 2 0 0 1 1.886 1.333a1 1 0 1 0 1.886-.666M23 24a2 2 0 1 0 0 4zm2 10a2 2 0 1 0 0-4z"/><path d="M13.153 8.621C15.607 7.42 19.633 6 24.039 6c4.314 0 8.234 1.361 10.675 2.546l.138.067c.736.364 1.33.708 1.748.987L32.906 15C41.422 23.706 48 41.997 24.039 41.997S6.479 24.038 15.069 15l-3.67-5.4c.283-.185.642-.4 1.07-.628q.318-.171.684-.35m17.379 6.307l2.957-4.323c-2.75.198-6.022.844-9.172 1.756c-2.25.65-4.75.551-7.065.124a25 25 0 0 1-1.737-.386l1.92 2.827c4.115 1.465 8.981 1.465 13.097.002M16.28 16.63c4.815 1.86 10.602 1.86 15.417-.002a29.3 29.3 0 0 1 4.988 7.143c1.352 2.758 2.088 5.515 1.968 7.891c-.116 2.293-1.018 4.252-3.078 5.708c-2.147 1.517-5.758 2.627-11.537 2.627c-5.785 0-9.413-1.091-11.58-2.591c-2.075-1.437-2.986-3.37-3.115-5.632c-.135-2.35.585-5.093 1.932-7.87c1.285-2.648 3.078-5.197 5.005-7.274m-1.15-6.714c.8.238 1.636.445 2.484.602c2.15.396 4.306.454 6.146-.079a54 54 0 0 1 6.53-1.471C28.45 8.414 26.298 8 24.038 8c-3.445 0-6.658.961-8.908 1.916"/></g></svg> -->
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Caja Chica
                        </span>
                    </Link>
                    <Link
                        :href="route('users.index')"
                        :active="route().current('users.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 18 18"
                        >
                            <g fill="currentColor" fill-rule="evenodd">
                                <path
                                    d="M13.689 11.132c1.155 1.222 1.953 2.879 2.183 4.748a1.007 1.007 0 0 1-1 1.12H3.007a1.005 1.005 0 0 1-1-1.12c.23-1.87 1.028-3.526 2.183-4.748c.247.228.505.442.782.633c-1.038 1.069-1.765 2.55-1.972 4.237L14.872 16c-.204-1.686-.93-3.166-1.966-4.235a7 7 0 0 0 .783-.633M8.939 1c1.9 0 3 2 4.38 2.633a2.48 2.48 0 0 1-1.88.867c-.298 0-.579-.06-.844-.157A3.73 3.73 0 0 1 7.69 5.75c-1.395 0-3.75.25-3.245-1.903C5.94 3 6.952 1 8.94 1"
                                />
                                <path
                                    d="M8.94 2c2.205 0 4 1.794 4 4s-1.795 4-4 4c-2.207 0-4-1.794-4-4s1.793-4 4-4m0 9A5 5 0 1 0 8.937.999A5 5 0 0 0 8.94 11"
                                />
                            </g>
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Usuarios
                        </span>
                    </Link>
                    <!-- <Link
                        :href="route('categories.index')"
                        :active="route().current('productos.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <span class="bi bi-inboxes"></span>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Almacen
                        </span>
                    </Link> -->
                    <!-- <details class="py-3.5 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-inboxes'><span v-show="dataOpenSideBar" class="ml-2">Recursos</span></span></summary>
                            <Link :href="route('categories.index')" :active="route().current('productos.*')" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white"><span class="ml-4">Inventario</span></Link>
                            <p v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Reuniones Semanales</span> <img src="../../../public/img/excel.png" class="w-4"></p>
                            <p v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Ocurrencias</span> <img src="../../../public/img/excel.png" class="w-4"></p>
                        </details> -->
                </div>

                <!-- SideBar Gerencia -->

                <div
                    v-else-if="
                        $page.props.auth.user.roles[0]?.name == 'Gerencia'
                    "
                    :class="{
                        'scroll-sidebar h-[calc(100vh-415px)] overflow-x-auto':
                            dataOpenSideBar,
                        'h-[calc(100vh-205px)] overflow-hidden':
                            !dataOpenSideBar,
                    }"
                >
                    <!-- <Link
                        :href="route('clientes.index')"
                        :active="route().current('clientes.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="0.94em"
                            height="1em"
                            viewBox="0 0 15 16"
                        >
                            <path
                                fill="currentColor"
                                d="M7.5 7a2.5 2.5 0 0 1 0-5a2.5 2.5 0 0 1 0 5m0-4C6.67 3 6 3.67 6 4.5S6.67 6 7.5 6S9 5.33 9 4.5S8.33 3 7.5 3"
                            />
                            <path
                                fill="currentColor"
                                d="M13.5 11c-.28 0-.5-.22-.5-.5s.22-.5.5-.5s.5-.22.5-.5A2.5 2.5 0 0 0 11.5 7h-1c-.28 0-.5-.22-.5-.5s.22-.5.5-.5c.83 0 1.5-.67 1.5-1.5S11.33 3 10.5 3c-.28 0-.5-.22-.5-.5s.22-.5.5-.5A2.5 2.5 0 0 1 13 4.5c0 .62-.22 1.18-.6 1.62c1.49.4 2.6 1.76 2.6 3.38c0 .83-.67 1.5-1.5 1.5m-12 0C.67 11 0 10.33 0 9.5c0-1.62 1.1-2.98 2.6-3.38c-.37-.44-.6-1-.6-1.62A2.5 2.5 0 0 1 4.5 2c.28 0 .5.22.5.5s-.22.5-.5.5C3.67 3 3 3.67 3 4.5S3.67 6 4.5 6c.28 0 .5.22.5.5s-.22.5-.5.5h-1A2.5 2.5 0 0 0 1 9.5c0 .28.22.5.5.5s.5.22.5.5s-.22.5-.5.5m9 3h-6c-.83 0-1.5-.67-1.5-1.5v-1C3 9.57 4.57 8 6.5 8h2c1.93 0 3.5 1.57 3.5 3.5v1c0 .83-.67 1.5-1.5 1.5m-4-5A2.5 2.5 0 0 0 4 11.5v1c0 .28.22.5.5.5h6c.28 0 .5-.22.5-.5v-1A2.5 2.5 0 0 0 8.5 9z"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Listar Clientes
                        </span>
                    </Link> -->
                    <!-- <Link
                        :href="route('tbproductos.index')"
                        :active="route().current('tbproductos.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-base"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill="currentColor"
                                d="M0 1h4.764l.545 2h18.078l-3.666 11H7.78l-.5 2H22v2H4.72l1.246-4.989L3.236 3H0zm7.764 11h10.515l2.334-7H5.855zM4 21a2 2 0 1 1 4 0a2 2 0 0 1-4 0m14 0a2 2 0 1 1 4 0a2 2 0 0 1-4 0"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Catalago de Productos
                        </span>
                    </Link> -->
                    <!-- <Link
                        :href="route('cuadropendientes.index')"
                        :active="route().current('cuadropendientes.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-base"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 16 16"
                        >
                            <path
                                fill="currentColor"
                                d="M13.5 5.88c-.28 0-.5-.22-.5-.5V1.5c0-.28-.22-.5-.5-.5h-9c-.28 0-.5.22-.5.5v2c0 .28-.22.5-.5.5S2 3.78 2 3.5v-2C2 .67 2.67 0 3.5 0h9c.83 0 1.5.67 1.5 1.5v3.88c0 .28-.22.5-.5.5"
                            />
                            <path
                                fill="currentColor"
                                d="M14.5 16h-13C.67 16 0 15.33 0 14.5v-10C0 3.67.67 3 1.5 3h4.75c.16 0 .31.07.4.2L8 5h6.5c.83 0 1.5.67 1.5 1.5v8c0 .83-.67 1.5-1.5 1.5M1.5 4c-.28 0-.5.22-.5.5v10c0 .28.22.5.5.5h13c.28 0 .5-.22.5-.5v-8c0-.28-.22-.5-.5-.5H7.75a.48.48 0 0 1-.4-.2L6 4z"
                            />
                            <path
                                fill="currentColor"
                                d="M5.5 13h-2c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h2c.28 0 .5.22.5.5s-.22.5-.5.5"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2.5">
                            1.- Cuadro de Pendientes
                        </span>
                    </Link> -->
                    <!-- <Link
                        :href="route('pendientestecnicos.index')"
                        :active="route().current('pendientestecnicos.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-1.5 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill="currentColor"
                                d="M17 22q-2.075 0-3.537-1.463T12 17t1.463-3.537T17 12t3.538 1.463T22 17t-1.463 3.538T17 22m1.675-2.625l.7-.7L17.5 16.8V14h-1v3.2zM5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h4.175q.275-.875 1.075-1.437T12 1q1 0 1.788.563T14.85 3H19q.825 0 1.413.588T21 5v6.25q-.45-.325-.95-.55T19 10.3V5h-2v3H7V5H5v14h5.3q.175.55.4 1.05t.55.95zm7-16q.425 0 .713-.288T13 4t-.288-.712T12 3t-.712.288T11 4t.288.713T12 5"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2.5">
                            2.- Cuadro de Pendientes
                        </span>
                    </Link> -->
                    <!-- <details
                        class="py-3 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary
                            class="px-2 flex items-center space-x-2 hover:text-white"
                        >
                            <svg
                                class="text-[18px]"
                                xmlns="http://www.w3.org/2000/svg"
                                width="1em"
                                height="1em"
                                viewBox="0 0 2048 2048"
                            >
                                <path
                                    fill="currentColor"
                                    d="M1185 1179q-88-75-195-115t-222-40q-88 0-170 23t-153 64t-129 100t-100 130t-65 153t-23 170H0q0-120 35-231t101-205t156-167t204-115q-113-74-176-186t-64-248q0-106 40-199t109-163T568 40T768 0t199 40t163 109t110 163t40 200q0 66-16 129t-48 119t-75 103t-101 83q65 25 124 61t111 81zM384 512q0 80 30 149t82 122t122 83t150 30q79 0 149-30t122-82t83-122t30-150q0-79-30-149t-82-122t-123-83t-149-30q-80 0-149 30t-122 82t-83 123t-30 149m1344 256q66 0 124 25t101 69t69 102t26 124t-25 124t-69 102t-102 69t-124 25q-23 0-45-3l-587 587q-27 27-62 41t-74 15q-40 0-75-15t-61-41t-41-61t-15-75q0-38 14-73t42-63l587-587q-3-22-3-45q0-66 25-124t68-101t102-69t125-26m0 512q40 0 75-15t61-41t41-61t15-75q0-41-19-82l-146 146h-91v-91l146-146q-41-19-82-19q-40 0-75 15t-61 41t-41 61t-15 75q0 41 19 82l-640 641q-19 19-19 45t19 45t45 19t45-19l641-640q41 19 82 19"
                                />
                            </svg>
                            <span v-show="dataOpenSideBar" class="ml-2">
                                Servicio Tecnico
                            </span>
                        </summary>
                        <Link
                            :href="route('hservicios.index')"
                            :active="route().current('hservicios.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >4.- Informes Tecnico</span
                            ></Link
                        >
                        <Link
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Registrar Informe Tecnico</span
                            ></Link
                        >
                        <a
                            href="https://onedrive.live.com/?authkey=%21AD9JIIeIEy788PM&id=D9E6E0F5EA45EB0A%216187&cid=D9E6E0F5EA45EB0A"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"
                            target="_blank"
                            rel="noopener noreferrer"
                            ><span class="ml-4">Manuales y Calibración</span
                            ><img src="/img/one_drive.png" class="w-4"
                        /></a>
                    </details> -->
                    <!-- <Link
                        :href="route('actasConformidad.index')"
                        :active="route().current('actasConformidad.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 24 24"
                        >
                            <g
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path
                                    d="m18.18 8.04l.463-.464a1.966 1.966 0 1 1 2.781 2.78l-.463.464M18.18 8.04s.058.984.927 1.853s1.854.927 1.854.927M18.18 8.04l-4.26 4.26c-.29.288-.434.433-.558.592q-.22.282-.374.606c-.087.182-.151.375-.28.762l-.413 1.24l-.134.401m8.8-5.081l-4.26 4.26c-.29.29-.434.434-.593.558q-.282.22-.606.374c-.182.087-.375.151-.762.28l-1.24.413l-.401.134m0 0l-.401.134a.53.53 0 0 1-.67-.67l.133-.402m.938.938l-.938-.938"
                                />
                                <path
                                    stroke-linecap="round"
                                    d="M8 13h2.5M8 9h6.5M8 17h1.5M3 14v-4c0-3.771 0-5.657 1.172-6.828S7.229 2 11 2h2c3.771 0 5.657 0 6.828 1.172M21 14c0 3.771 0 5.657-1.172 6.828m-15.656 0C5.343 22 7.229 22 11 22h2c3.771 0 5.657 0 6.828-1.172m0 0c.944-.943 1.127-2.348 1.163-4.828"
                                />
                            </g>
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Actas de Conformidad
                        </span>
                    </Link> -->

                    <!-- <details
                        class="py-3 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary
                            class="px-2 flex items-center space-x-2 hover:text-white"
                        >
                            <svg
                                class="text-[18px]"
                                xmlns="http://www.w3.org/2000/svg"
                                width="1em"
                                height="1em"
                                viewBox="0 0 2048 2048"
                            >
                                <path
                                    fill="currentColor"
                                    d="M1185 1179q-88-75-195-115t-222-40q-88 0-170 23t-153 64t-129 100t-100 130t-65 153t-23 170H0q0-120 35-231t101-205t156-167t204-115q-113-74-176-186t-64-248q0-106 40-199t109-163T568 40T768 0t199 40t163 109t110 163t40 200q0 66-16 129t-48 119t-75 103t-101 83q65 25 124 61t111 81zM384 512q0 80 30 149t82 122t122 83t150 30q79 0 149-30t122-82t83-122t30-150q0-79-30-149t-82-122t-123-83t-149-30q-80 0-149 30t-122 82t-83 123t-30 149m1344 256q66 0 124 25t101 69t69 102t26 124t-25 124t-69 102t-102 69t-124 25q-23 0-45-3l-587 587q-27 27-62 41t-74 15q-40 0-75-15t-61-41t-41-61t-15-75q0-38 14-73t42-63l587-587q-3-22-3-45q0-66 25-124t68-101t102-69t125-26m0 512q40 0 75-15t61-41t41-61t15-75q0-41-19-82l-146 146h-91v-91l146-146q-41-19-82-19q-40 0-75 15t-61 41t-41 61t-15 75q0 41 19 82l-640 641q-19 19-19 45t19 45t45 19t45-19l641-640q41 19 82 19"
                                />
                            </svg>
                            <span v-show="dataOpenSideBar" class="ml-2">
                                Certificaciones
                            </span>
                        </summary>
                        <Link
                            :href="route('hservicios.index')"
                            :active="route().current('hservicios.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Registrar Solicitud de Certificacion</span
                            ></Link
                        >
                        <Link
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Solicitudes Emitidas</span
                            ></Link
                        >
                        <Link
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Certificaciones Emitidas</span
                            ></Link
                        >
                    </details> -->

                    <!-- <details
                        class="py-3.5 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary class="px-2 flex space-x-2 hover:text-white">
                            <span class="bi bi-graph-up-arrow"
                                ><span v-show="dataOpenSideBar" class="ml-2"
                                    >Cotizaciones</span
                                ></span
                            >
                        </summary>
                        <Link
                            :href="route('cventas.index')"
                            :active="route().current('cventas.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Listar Cotizacion por Venta</span
                            ></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Listar Cotizacion por Servicio</span
                            ></Link
                        >
                    </details> -->
                    <!-- <details
                        class="py-3.5 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary class="px-2 flex space-x-2 hover:text-white">
                            <span class="bi bi-graph-up-arrow"
                                ><span v-show="dataOpenSideBar" class="ml-2"
                                    >Almacen</span
                                ></span
                            >
                        </summary>
                        <Link
                            :href="route('categories.index')"
                            :active="route().current('productos.*')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4">Inventario</span></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Ingreso de Materiales</span
                            ></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Salida de Materiales</span
                            ></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4">Generar Kardex</span></Link
                        >
                    </details> -->
                    <!-- <details class="py-3.5 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-bar-chart-line'><span v-show="dataOpenSideBar" class="ml-2">Prestamos o Alquiler</span></span></summary>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Listar Equipos</span> <img src="../../../public/img/excel.png" class="w-4"></Link>
                        </details> -->
                    <!-- <details class="py-3.5 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-patch-check'><span v-show="dataOpenSideBar" class="ml-2">Certificaciones</span></span></summary>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Registrar Solicitud Certificacion</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Solicitudes Emitidas</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Certificaciones Emitidas</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Certificaciones por Caducar</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Lista Certificados emitidos TW</span> <img src="../../../public/img/excel.png" class="w-4"></Link>
                        </details> -->
                    <Link
                        :href="route('cajaChica.index')"
                        :active="route().current('cajaChica.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="22"
                            height="24"
                            viewBox="0 0 24 24"
                        >
                            <g
                                fill="none"
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                color="currentColor"
                            >
                                <path
                                    d="M20.943 16.835a15.76 15.76 0 0 0-4.476-8.616c-.517-.503-.775-.754-1.346-.986C14.55 7 14.059 7 13.078 7h-2.156c-.981 0-1.472 0-2.043.233c-.57.232-.83.483-1.346.986a15.76 15.76 0 0 0-4.476 8.616C2.57 19.773 5.28 22 8.308 22h7.384c3.029 0 5.74-2.227 5.25-5.165"
                                />
                                <path
                                    d="M7.257 4.443c-.207-.3-.506-.708.112-.8c.635-.096 1.294.338 1.94.33c.583-.009.88-.268 1.2-.638C10.845 2.946 11.365 2 12 2s1.155.946 1.491 1.335c.32.37.617.63 1.2.637c.646.01 1.305-.425 1.94-.33c.618.093.319.5.112.8l-.932 1.359c-.4.58-.599.87-1.017 1.035S13.837 7 12.758 7h-1.516c-1.08 0-1.619 0-2.036-.164S8.589 6.38 8.189 5.8zm6.37 8.476c-.216-.799-1.317-1.519-2.638-.98s-1.53 2.272.467 2.457c.904.083 1.492-.097 2.031.412c.54.508.64 1.923-.739 2.304c-1.377.381-2.742-.214-2.89-1.06m1.984-5.06v.761m0 5.476v.764"
                                />
                            </g>
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Caja Chica
                        </span>
                    </Link>
                    <!-- <Link
                        :href="route('users.index')"
                        :active="route().current('users.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 18 18"
                        >
                            <g fill="currentColor" fill-rule="evenodd">
                                <path
                                    d="M13.689 11.132c1.155 1.222 1.953 2.879 2.183 4.748a1.007 1.007 0 0 1-1 1.12H3.007a1.005 1.005 0 0 1-1-1.12c.23-1.87 1.028-3.526 2.183-4.748c.247.228.505.442.782.633c-1.038 1.069-1.765 2.55-1.972 4.237L14.872 16c-.204-1.686-.93-3.166-1.966-4.235a7 7 0 0 0 .783-.633M8.939 1c1.9 0 3 2 4.38 2.633a2.48 2.48 0 0 1-1.88.867c-.298 0-.579-.06-.844-.157A3.73 3.73 0 0 1 7.69 5.75c-1.395 0-3.75.25-3.245-1.903C5.94 3 6.952 1 8.94 1"
                                />
                                <path
                                    d="M8.94 2c2.205 0 4 1.794 4 4s-1.795 4-4 4c-2.207 0-4-1.794-4-4s1.793-4 4-4m0 9A5 5 0 1 0 8.937.999A5 5 0 0 0 8.94 11"
                                />
                            </g>
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Usuarios
                        </span>
                    </Link> -->
                    <!-- <Link
                        :href="route('categories.index')"
                        :active="route().current('productos.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <span class="bi bi-inboxes"></span>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Almacen
                        </span>
                    </Link> -->
                    <!-- <details class="py-3.5 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-inboxes'><span v-show="dataOpenSideBar" class="ml-2">Recursos</span></span></summary>
                            <Link :href="route('categories.index')" :active="route().current('productos.*')" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white"><span class="ml-4">Inventario</span></Link>
                            <p v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Reuniones Semanales</span> <img src="../../../public/img/excel.png" class="w-4"></p>
                            <p v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Ocurrencias</span> <img src="../../../public/img/excel.png" class="w-4"></p>
                        </details> -->
                </div>

                <!-- SideBar Servicio Tecnico -->

                <div
                    v-else-if="
                        $page.props.auth.user.roles[0]?.name ==
                        'Servicio Tecnico'
                    "
                    :class="{
                        'scroll-sidebar h-[calc(100vh-415px)] overflow-x-auto':
                            dataOpenSideBar,
                        'h-[calc(100vh-205px)] overflow-hidden':
                            !dataOpenSideBar,
                    }"
                >
                    <!-- <Link
                        :href="route('clientes.index')"
                        :active="route().current('clientes.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="0.94em"
                            height="1em"
                            viewBox="0 0 15 16"
                        >
                            <path
                                fill="currentColor"
                                d="M7.5 7a2.5 2.5 0 0 1 0-5a2.5 2.5 0 0 1 0 5m0-4C6.67 3 6 3.67 6 4.5S6.67 6 7.5 6S9 5.33 9 4.5S8.33 3 7.5 3"
                            />
                            <path
                                fill="currentColor"
                                d="M13.5 11c-.28 0-.5-.22-.5-.5s.22-.5.5-.5s.5-.22.5-.5A2.5 2.5 0 0 0 11.5 7h-1c-.28 0-.5-.22-.5-.5s.22-.5.5-.5c.83 0 1.5-.67 1.5-1.5S11.33 3 10.5 3c-.28 0-.5-.22-.5-.5s.22-.5.5-.5A2.5 2.5 0 0 1 13 4.5c0 .62-.22 1.18-.6 1.62c1.49.4 2.6 1.76 2.6 3.38c0 .83-.67 1.5-1.5 1.5m-12 0C.67 11 0 10.33 0 9.5c0-1.62 1.1-2.98 2.6-3.38c-.37-.44-.6-1-.6-1.62A2.5 2.5 0 0 1 4.5 2c.28 0 .5.22.5.5s-.22.5-.5.5C3.67 3 3 3.67 3 4.5S3.67 6 4.5 6c.28 0 .5.22.5.5s-.22.5-.5.5h-1A2.5 2.5 0 0 0 1 9.5c0 .28.22.5.5.5s.5.22.5.5s-.22.5-.5.5m9 3h-6c-.83 0-1.5-.67-1.5-1.5v-1C3 9.57 4.57 8 6.5 8h2c1.93 0 3.5 1.57 3.5 3.5v1c0 .83-.67 1.5-1.5 1.5m-4-5A2.5 2.5 0 0 0 4 11.5v1c0 .28.22.5.5.5h6c.28 0 .5-.22.5-.5v-1A2.5 2.5 0 0 0 8.5 9z"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Listar Clientes
                        </span>
                    </Link> -->
                    <!-- <Link
                        :href="route('tbproductos.index')"
                        :active="route().current('tbproductos.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-base"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill="currentColor"
                                d="M0 1h4.764l.545 2h18.078l-3.666 11H7.78l-.5 2H22v2H4.72l1.246-4.989L3.236 3H0zm7.764 11h10.515l2.334-7H5.855zM4 21a2 2 0 1 1 4 0a2 2 0 0 1-4 0m14 0a2 2 0 1 1 4 0a2 2 0 0 1-4 0"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Catalago de Productos
                        </span>
                    </Link> -->
                    <!-- <Link
                        :href="route('cuadropendientes.index')"
                        :active="route().current('cuadropendientes.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-base"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 16 16"
                        >
                            <path
                                fill="currentColor"
                                d="M13.5 5.88c-.28 0-.5-.22-.5-.5V1.5c0-.28-.22-.5-.5-.5h-9c-.28 0-.5.22-.5.5v2c0 .28-.22.5-.5.5S2 3.78 2 3.5v-2C2 .67 2.67 0 3.5 0h9c.83 0 1.5.67 1.5 1.5v3.88c0 .28-.22.5-.5.5"
                            />
                            <path
                                fill="currentColor"
                                d="M14.5 16h-13C.67 16 0 15.33 0 14.5v-10C0 3.67.67 3 1.5 3h4.75c.16 0 .31.07.4.2L8 5h6.5c.83 0 1.5.67 1.5 1.5v8c0 .83-.67 1.5-1.5 1.5M1.5 4c-.28 0-.5.22-.5.5v10c0 .28.22.5.5.5h13c.28 0 .5-.22.5-.5v-8c0-.28-.22-.5-.5-.5H7.75a.48.48 0 0 1-.4-.2L6 4z"
                            />
                            <path
                                fill="currentColor"
                                d="M5.5 13h-2c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h2c.28 0 .5.22.5.5s-.22.5-.5.5"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2.5">
                            1.- Cuadro de Pendientes
                        </span>
                    </Link> -->
                    <Link
                        :href="route('pendientestecnicos.index')"
                        :active="route().current('pendientestecnicos.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-1.5 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill="currentColor"
                                d="M17 22q-2.075 0-3.537-1.463T12 17t1.463-3.537T17 12t3.538 1.463T22 17t-1.463 3.538T17 22m1.675-2.625l.7-.7L17.5 16.8V14h-1v3.2zM5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h4.175q.275-.875 1.075-1.437T12 1q1 0 1.788.563T14.85 3H19q.825 0 1.413.588T21 5v6.25q-.45-.325-.95-.55T19 10.3V5h-2v3H7V5H5v14h5.3q.175.55.4 1.05t.55.95zm7-16q.425 0 .713-.288T13 4t-.288-.712T12 3t-.712.288T11 4t.288.713T12 5"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2.5">
                            2.- Cuadro de Pendientes
                        </span>
                    </Link>
                    <details
                        class="py-3 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary
                            class="px-2 flex items-center space-x-2 hover:text-white"
                        >
                            <svg
                                class="text-[18px]"
                                xmlns="http://www.w3.org/2000/svg"
                                width="1em"
                                height="1em"
                                viewBox="0 0 2048 2048"
                            >
                                <path
                                    fill="currentColor"
                                    d="M1185 1179q-88-75-195-115t-222-40q-88 0-170 23t-153 64t-129 100t-100 130t-65 153t-23 170H0q0-120 35-231t101-205t156-167t204-115q-113-74-176-186t-64-248q0-106 40-199t109-163T568 40T768 0t199 40t163 109t110 163t40 200q0 66-16 129t-48 119t-75 103t-101 83q65 25 124 61t111 81zM384 512q0 80 30 149t82 122t122 83t150 30q79 0 149-30t122-82t83-122t30-150q0-79-30-149t-82-122t-123-83t-149-30q-80 0-149 30t-122 82t-83 123t-30 149m1344 256q66 0 124 25t101 69t69 102t26 124t-25 124t-69 102t-102 69t-124 25q-23 0-45-3l-587 587q-27 27-62 41t-74 15q-40 0-75-15t-61-41t-41-61t-15-75q0-38 14-73t42-63l587-587q-3-22-3-45q0-66 25-124t68-101t102-69t125-26m0 512q40 0 75-15t61-41t41-61t15-75q0-41-19-82l-146 146h-91v-91l146-146q-41-19-82-19q-40 0-75 15t-61 41t-41 61t-15 75q0 41 19 82l-640 641q-19 19-19 45t19 45t45 19t45-19l641-640q41 19 82 19"
                                />
                            </svg>
                            <span v-show="dataOpenSideBar" class="ml-2">
                                Servicio Tecnico
                            </span>
                        </summary>
                        <Link
                            :href="route('hservicios.index')"
                            :active="route().current('hservicios.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >4.- Informes Tecnico</span
                            ></Link
                        >
                        <Link
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Registrar Informe Tecnico</span
                            ></Link
                        >
                        <a
                            href="https://onedrive.live.com/?authkey=%21AD9JIIeIEy788PM&id=D9E6E0F5EA45EB0A%216187&cid=D9E6E0F5EA45EB0A"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"
                            target="_blank"
                            rel="noopener noreferrer"
                            ><span class="ml-4">Manuales y Calibración</span
                            ><img src="/img/one_drive.png" class="w-4"
                        /></a>
                    </details>
                    <!-- <Link
                        :href="route('actasConformidad.index')"
                        :active="route().current('actasConformidad.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 24 24"
                        >
                            <g
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path
                                    d="m18.18 8.04l.463-.464a1.966 1.966 0 1 1 2.781 2.78l-.463.464M18.18 8.04s.058.984.927 1.853s1.854.927 1.854.927M18.18 8.04l-4.26 4.26c-.29.288-.434.433-.558.592q-.22.282-.374.606c-.087.182-.151.375-.28.762l-.413 1.24l-.134.401m8.8-5.081l-4.26 4.26c-.29.29-.434.434-.593.558q-.282.22-.606.374c-.182.087-.375.151-.762.28l-1.24.413l-.401.134m0 0l-.401.134a.53.53 0 0 1-.67-.67l.133-.402m.938.938l-.938-.938"
                                />
                                <path
                                    stroke-linecap="round"
                                    d="M8 13h2.5M8 9h6.5M8 17h1.5M3 14v-4c0-3.771 0-5.657 1.172-6.828S7.229 2 11 2h2c3.771 0 5.657 0 6.828 1.172M21 14c0 3.771 0 5.657-1.172 6.828m-15.656 0C5.343 22 7.229 22 11 22h2c3.771 0 5.657 0 6.828-1.172m0 0c.944-.943 1.127-2.348 1.163-4.828"
                                />
                            </g>
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Actas de Conformidad
                        </span>
                    </Link> -->

                    <!-- <details
                        class="py-3 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary
                            class="px-2 flex items-center space-x-2 hover:text-white"
                        >
                            <svg
                                class="text-[18px]"
                                xmlns="http://www.w3.org/2000/svg"
                                width="1em"
                                height="1em"
                                viewBox="0 0 2048 2048"
                            >
                                <path
                                    fill="currentColor"
                                    d="M1185 1179q-88-75-195-115t-222-40q-88 0-170 23t-153 64t-129 100t-100 130t-65 153t-23 170H0q0-120 35-231t101-205t156-167t204-115q-113-74-176-186t-64-248q0-106 40-199t109-163T568 40T768 0t199 40t163 109t110 163t40 200q0 66-16 129t-48 119t-75 103t-101 83q65 25 124 61t111 81zM384 512q0 80 30 149t82 122t122 83t150 30q79 0 149-30t122-82t83-122t30-150q0-79-30-149t-82-122t-123-83t-149-30q-80 0-149 30t-122 82t-83 123t-30 149m1344 256q66 0 124 25t101 69t69 102t26 124t-25 124t-69 102t-102 69t-124 25q-23 0-45-3l-587 587q-27 27-62 41t-74 15q-40 0-75-15t-61-41t-41-61t-15-75q0-38 14-73t42-63l587-587q-3-22-3-45q0-66 25-124t68-101t102-69t125-26m0 512q40 0 75-15t61-41t41-61t15-75q0-41-19-82l-146 146h-91v-91l146-146q-41-19-82-19q-40 0-75 15t-61 41t-41 61t-15 75q0 41 19 82l-640 641q-19 19-19 45t19 45t45 19t45-19l641-640q41 19 82 19"
                                />
                            </svg>
                            <span v-show="dataOpenSideBar" class="ml-2">
                                Certificaciones
                            </span>
                        </summary>
                        <Link
                            :href="route('hservicios.index')"
                            :active="route().current('hservicios.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Registrar Solicitud de Certificacion</span
                            ></Link
                        >
                        <Link
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Solicitudes Emitidas</span
                            ></Link
                        >
                        <Link
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Certificaciones Emitidas</span
                            ></Link
                        >
                    </details> -->

                    <!-- <details
                        class="py-3.5 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary class="px-2 flex space-x-2 hover:text-white">
                            <span class="bi bi-graph-up-arrow"
                                ><span v-show="dataOpenSideBar" class="ml-2"
                                    >Cotizaciones</span
                                ></span
                            >
                        </summary>
                        <Link
                            :href="route('cventas.index')"
                            :active="route().current('cventas.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Listar Cotizacion por Venta</span
                            ></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Listar Cotizacion por Servicio</span
                            ></Link
                        >
                    </details> -->

                    <!-- <details
                        class="py-3.5 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary class="px-2 flex space-x-2 hover:text-white">
                            <span class="bi bi-graph-up-arrow"
                                ><span v-show="dataOpenSideBar" class="ml-2"
                                    >Almacen</span
                                ></span
                            >
                        </summary>
                        <Link
                            :href="route('categories.index')"
                            :active="route().current('productos.*')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4">Inventario</span></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Ingreso de Materiales</span
                            ></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Salida de Materiales</span
                            ></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4">Generar Kardex</span></Link
                        >
                    </details> -->

                    <!-- <details class="py-3.5 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-bar-chart-line'><span v-show="dataOpenSideBar" class="ml-2">Prestamos o Alquiler</span></span></summary>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Listar Equipos</span> <img src="../../../public/img/excel.png" class="w-4"></Link>
                        </details> -->
                    <!-- <details class="py-3.5 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-patch-check'><span v-show="dataOpenSideBar" class="ml-2">Certificaciones</span></span></summary>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Registrar Solicitud Certificacion</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Solicitudes Emitidas</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Certificaciones Emitidas</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Certificaciones por Caducar</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Lista Certificados emitidos TW</span> <img src="../../../public/img/excel.png" class="w-4"></Link>
                        </details> -->
                    <!-- <Link
                        :href="route('cajaChica.index')"
                        :active="route().current('cajaChica.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="22"
                            height="24"
                            viewBox="0 0 24 24"
                        >
                            <g
                                fill="none"
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                color="currentColor"
                            >
                                <path
                                    d="M20.943 16.835a15.76 15.76 0 0 0-4.476-8.616c-.517-.503-.775-.754-1.346-.986C14.55 7 14.059 7 13.078 7h-2.156c-.981 0-1.472 0-2.043.233c-.57.232-.83.483-1.346.986a15.76 15.76 0 0 0-4.476 8.616C2.57 19.773 5.28 22 8.308 22h7.384c3.029 0 5.74-2.227 5.25-5.165"
                                />
                                <path
                                    d="M7.257 4.443c-.207-.3-.506-.708.112-.8c.635-.096 1.294.338 1.94.33c.583-.009.88-.268 1.2-.638C10.845 2.946 11.365 2 12 2s1.155.946 1.491 1.335c.32.37.617.63 1.2.637c.646.01 1.305-.425 1.94-.33c.618.093.319.5.112.8l-.932 1.359c-.4.58-.599.87-1.017 1.035S13.837 7 12.758 7h-1.516c-1.08 0-1.619 0-2.036-.164S8.589 6.38 8.189 5.8zm6.37 8.476c-.216-.799-1.317-1.519-2.638-.98s-1.53 2.272.467 2.457c.904.083 1.492-.097 2.031.412c.54.508.64 1.923-.739 2.304c-1.377.381-2.742-.214-2.89-1.06m1.984-5.06v.761m0 5.476v.764"
                                />
                            </g>
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Caja Chica
                        </span>
                    </Link> -->
                    <!-- <Link
                        :href="route('users.index')"
                        :active="route().current('users.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 18 18"
                        >
                            <g fill="currentColor" fill-rule="evenodd">
                                <path
                                    d="M13.689 11.132c1.155 1.222 1.953 2.879 2.183 4.748a1.007 1.007 0 0 1-1 1.12H3.007a1.005 1.005 0 0 1-1-1.12c.23-1.87 1.028-3.526 2.183-4.748c.247.228.505.442.782.633c-1.038 1.069-1.765 2.55-1.972 4.237L14.872 16c-.204-1.686-.93-3.166-1.966-4.235a7 7 0 0 0 .783-.633M8.939 1c1.9 0 3 2 4.38 2.633a2.48 2.48 0 0 1-1.88.867c-.298 0-.579-.06-.844-.157A3.73 3.73 0 0 1 7.69 5.75c-1.395 0-3.75.25-3.245-1.903C5.94 3 6.952 1 8.94 1"
                                />
                                <path
                                    d="M8.94 2c2.205 0 4 1.794 4 4s-1.795 4-4 4c-2.207 0-4-1.794-4-4s1.793-4 4-4m0 9A5 5 0 1 0 8.937.999A5 5 0 0 0 8.94 11"
                                />
                            </g>
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Usuarios
                        </span>
                    </Link> -->
                    <!-- <Link
                        :href="route('categories.index')"
                        :active="route().current('productos.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <span class="bi bi-inboxes"></span>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Almacen
                        </span>
                    </Link> -->
                    <!-- <details class="py-3.5 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-inboxes'><span v-show="dataOpenSideBar" class="ml-2">Recursos</span></span></summary>
                            <Link :href="route('categories.index')" :active="route().current('productos.*')" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white"><span class="ml-4">Inventario</span></Link>
                            <p v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Reuniones Semanales</span> <img src="../../../public/img/excel.png" class="w-4"></p>
                            <p v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Ocurrencias</span> <img src="../../../public/img/excel.png" class="w-4"></p>
                        </details> -->
                </div>

                <!-- SideBar Area de Almacen -->

                <div
                    v-else-if="
                        $page.props.auth.user.roles[0]?.name ==
                        'Area de Almacen'
                    "
                    :class="{
                        'scroll-sidebar h-[calc(100vh-415px)] overflow-x-auto':
                            dataOpenSideBar,
                        'h-[calc(100vh-205px)] overflow-hidden':
                            !dataOpenSideBar,
                    }"
                >

                    <details
                        class="py-3 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary
                            class="px-2 flex items-center space-x-2 hover:text-white"
                        >
                            <svg
                                class="text-[18px]"
                                xmlns="http://www.w3.org/2000/svg"
                                width="1em"
                                height="1em"
                                viewBox="0 0 2048 2048"
                            >
                                <path
                                    fill="currentColor"
                                    d="M1185 1179q-88-75-195-115t-222-40q-88 0-170 23t-153 64t-129 100t-100 130t-65 153t-23 170H0q0-120 35-231t101-205t156-167t204-115q-113-74-176-186t-64-248q0-106 40-199t109-163T568 40T768 0t199 40t163 109t110 163t40 200q0 66-16 129t-48 119t-75 103t-101 83q65 25 124 61t111 81zM384 512q0 80 30 149t82 122t122 83t150 30q79 0 149-30t122-82t83-122t30-150q0-79-30-149t-82-122t-123-83t-149-30q-80 0-149 30t-122 82t-83 123t-30 149m1344 256q66 0 124 25t101 69t69 102t26 124t-25 124t-69 102t-102 69t-124 25q-23 0-45-3l-587 587q-27 27-62 41t-74 15q-40 0-75-15t-61-41t-41-61t-15-75q0-38 14-73t42-63l587-587q-3-22-3-45q0-66 25-124t68-101t102-69t125-26m0 512q40 0 75-15t61-41t41-61t15-75q0-41-19-82l-146 146h-91v-91l146-146q-41-19-82-19q-40 0-75 15t-61 41t-41 61t-15 75q0 41 19 82l-640 641q-19 19-19 45t19 45t45 19t45-19l641-640q41 19 82 19"
                                />
                            </svg>
                            <span v-show="dataOpenSideBar" class="ml-2">
                                Servicio Tecnico
                            </span>
                        </summary>
                        <Link
                            :href="route('hservicios.index')"
                            :active="route().current('hservicios.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >4.- Informes Tecnico</span
                            ></Link
                        >
                        <!-- <Link
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Registrar Informe Tecnico</span
                            ></Link
                        > -->
                        <a
                            href="https://onedrive.live.com/?authkey=%21AD9JIIeIEy788PM&id=D9E6E0F5EA45EB0A%216187&cid=D9E6E0F5EA45EB0A"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"
                            target="_blank"
                            rel="noopener noreferrer"
                            ><span class="ml-4">Manuales y Calibración</span
                            ><img src="/img/one_drive.png" class="w-4"
                        /></a>
                    </details>

                    <Link
                        :href="route('actasConformidad.index')"
                        :active="route().current('actasConformidad.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 24 24"
                        >
                            <g
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path
                                    d="m18.18 8.04l.463-.464a1.966 1.966 0 1 1 2.781 2.78l-.463.464M18.18 8.04s.058.984.927 1.853s1.854.927 1.854.927M18.18 8.04l-4.26 4.26c-.29.288-.434.433-.558.592q-.22.282-.374.606c-.087.182-.151.375-.28.762l-.413 1.24l-.134.401m8.8-5.081l-4.26 4.26c-.29.29-.434.434-.593.558q-.282.22-.606.374c-.182.087-.375.151-.762.28l-1.24.413l-.401.134m0 0l-.401.134a.53.53 0 0 1-.67-.67l.133-.402m.938.938l-.938-.938"
                                />
                                <path
                                    stroke-linecap="round"
                                    d="M8 13h2.5M8 9h6.5M8 17h1.5M3 14v-4c0-3.771 0-5.657 1.172-6.828S7.229 2 11 2h2c3.771 0 5.657 0 6.828 1.172M21 14c0 3.771 0 5.657-1.172 6.828m-15.656 0C5.343 22 7.229 22 11 22h2c3.771 0 5.657 0 6.828-1.172m0 0c.944-.943 1.127-2.348 1.163-4.828"
                                />
                            </g>
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Actas de Conformidad
                        </span>
                    </Link>

                    <details
                        class="py-3 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary
                            class="px-2 flex items-center space-x-2 hover:text-white"
                        >
                            <svg
                                class="text-[18px]"
                                xmlns="http://www.w3.org/2000/svg"
                                width="1em"
                                height="1em"
                                viewBox="0 0 2048 2048"
                            >
                                <path
                                    fill="currentColor"
                                    d="M1185 1179q-88-75-195-115t-222-40q-88 0-170 23t-153 64t-129 100t-100 130t-65 153t-23 170H0q0-120 35-231t101-205t156-167t204-115q-113-74-176-186t-64-248q0-106 40-199t109-163T568 40T768 0t199 40t163 109t110 163t40 200q0 66-16 129t-48 119t-75 103t-101 83q65 25 124 61t111 81zM384 512q0 80 30 149t82 122t122 83t150 30q79 0 149-30t122-82t83-122t30-150q0-79-30-149t-82-122t-123-83t-149-30q-80 0-149 30t-122 82t-83 123t-30 149m1344 256q66 0 124 25t101 69t69 102t26 124t-25 124t-69 102t-102 69t-124 25q-23 0-45-3l-587 587q-27 27-62 41t-74 15q-40 0-75-15t-61-41t-41-61t-15-75q0-38 14-73t42-63l587-587q-3-22-3-45q0-66 25-124t68-101t102-69t125-26m0 512q40 0 75-15t61-41t41-61t15-75q0-41-19-82l-146 146h-91v-91l146-146q-41-19-82-19q-40 0-75 15t-61 41t-41 61t-15 75q0 41 19 82l-640 641q-19 19-19 45t19 45t45 19t45-19l641-640q41 19 82 19"
                                />
                            </svg>
                            <span v-show="dataOpenSideBar" class="ml-2">
                                Certificaciones
                            </span>
                        </summary>
                        <Link
                            :href="route('hservicios.index')"
                            :active="route().current('hservicios.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Registrar Solicitud de Certificacion</span
                            ></Link
                        >
                        <Link
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Solicitudes Emitidas</span
                            ></Link
                        >
                        <Link
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Certificaciones Emitidas</span
                            ></Link
                        >
                    </details>

                    <details
                        class="py-3.5 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary class="px-2 flex space-x-2 hover:text-white">
                            <span class="bi bi-graph-up-arrow"
                                ><span v-show="dataOpenSideBar" class="ml-2"
                                    >Almacen</span
                                ></span
                            >
                        </summary>
                        <Link
                            :href="route('inventario.index')"

                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4">Inventario</span></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Ingreso de Materiales</span
                            ></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Salida de Materiales</span
                            ></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4">Generar Kardex</span></Link
                        >
                    </details>

                </div>

                <!-- SideBar Area de TI -->

                <div
                    v-else-if="
                        $page.props.auth.user.roles[0]?.name == 'Area de TI'
                    "
                    :class="{
                        'scroll-sidebar h-[calc(100vh-415px)] overflow-x-auto':
                            dataOpenSideBar,
                        'h-[calc(100vh-205px)] overflow-hidden':
                            !dataOpenSideBar,
                    }"
                >
                    <Link
                        :href="route('clientes.index')"
                        :active="route().current('clientes.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="0.94em"
                            height="1em"
                            viewBox="0 0 15 16"
                        >
                            <path
                                fill="currentColor"
                                d="M7.5 7a2.5 2.5 0 0 1 0-5a2.5 2.5 0 0 1 0 5m0-4C6.67 3 6 3.67 6 4.5S6.67 6 7.5 6S9 5.33 9 4.5S8.33 3 7.5 3"
                            />
                            <path
                                fill="currentColor"
                                d="M13.5 11c-.28 0-.5-.22-.5-.5s.22-.5.5-.5s.5-.22.5-.5A2.5 2.5 0 0 0 11.5 7h-1c-.28 0-.5-.22-.5-.5s.22-.5.5-.5c.83 0 1.5-.67 1.5-1.5S11.33 3 10.5 3c-.28 0-.5-.22-.5-.5s.22-.5.5-.5A2.5 2.5 0 0 1 13 4.5c0 .62-.22 1.18-.6 1.62c1.49.4 2.6 1.76 2.6 3.38c0 .83-.67 1.5-1.5 1.5m-12 0C.67 11 0 10.33 0 9.5c0-1.62 1.1-2.98 2.6-3.38c-.37-.44-.6-1-.6-1.62A2.5 2.5 0 0 1 4.5 2c.28 0 .5.22.5.5s-.22.5-.5.5C3.67 3 3 3.67 3 4.5S3.67 6 4.5 6c.28 0 .5.22.5.5s-.22.5-.5.5h-1A2.5 2.5 0 0 0 1 9.5c0 .28.22.5.5.5s.5.22.5.5s-.22.5-.5.5m9 3h-6c-.83 0-1.5-.67-1.5-1.5v-1C3 9.57 4.57 8 6.5 8h2c1.93 0 3.5 1.57 3.5 3.5v1c0 .83-.67 1.5-1.5 1.5m-4-5A2.5 2.5 0 0 0 4 11.5v1c0 .28.22.5.5.5h6c.28 0 .5-.22.5-.5v-1A2.5 2.5 0 0 0 8.5 9z"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Listar Clientes
                        </span>
                    </Link>
                    <Link
                        :href="route('tbproductos.index')"
                        :active="route().current('tbproductos.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-base"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill="currentColor"
                                d="M0 1h4.764l.545 2h18.078l-3.666 11H7.78l-.5 2H22v2H4.72l1.246-4.989L3.236 3H0zm7.764 11h10.515l2.334-7H5.855zM4 21a2 2 0 1 1 4 0a2 2 0 0 1-4 0m14 0a2 2 0 1 1 4 0a2 2 0 0 1-4 0"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Catalago de Productos
                        </span>
                    </Link>
                    <Link
                        :href="route('cuadropendientes.index')"
                        :active="route().current('cuadropendientes.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-base"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 16 16"
                        >
                            <path
                                fill="currentColor"
                                d="M13.5 5.88c-.28 0-.5-.22-.5-.5V1.5c0-.28-.22-.5-.5-.5h-9c-.28 0-.5.22-.5.5v2c0 .28-.22.5-.5.5S2 3.78 2 3.5v-2C2 .67 2.67 0 3.5 0h9c.83 0 1.5.67 1.5 1.5v3.88c0 .28-.22.5-.5.5"
                            />
                            <path
                                fill="currentColor"
                                d="M14.5 16h-13C.67 16 0 15.33 0 14.5v-10C0 3.67.67 3 1.5 3h4.75c.16 0 .31.07.4.2L8 5h6.5c.83 0 1.5.67 1.5 1.5v8c0 .83-.67 1.5-1.5 1.5M1.5 4c-.28 0-.5.22-.5.5v10c0 .28.22.5.5.5h13c.28 0 .5-.22.5-.5v-8c0-.28-.22-.5-.5-.5H7.75a.48.48 0 0 1-.4-.2L6 4z"
                            />
                            <path
                                fill="currentColor"
                                d="M5.5 13h-2c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h2c.28 0 .5.22.5.5s-.22.5-.5.5"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2.5">
                            1.- Cuadro de Pendientes
                        </span>
                    </Link>
                    <Link
                        :href="route('pendientestecnicos.index')"
                        :active="route().current('pendientestecnicos.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-1.5 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill="currentColor"
                                d="M17 22q-2.075 0-3.537-1.463T12 17t1.463-3.537T17 12t3.538 1.463T22 17t-1.463 3.538T17 22m1.675-2.625l.7-.7L17.5 16.8V14h-1v3.2zM5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h4.175q.275-.875 1.075-1.437T12 1q1 0 1.788.563T14.85 3H19q.825 0 1.413.588T21 5v6.25q-.45-.325-.95-.55T19 10.3V5h-2v3H7V5H5v14h5.3q.175.55.4 1.05t.55.95zm7-16q.425 0 .713-.288T13 4t-.288-.712T12 3t-.712.288T11 4t.288.713T12 5"
                            />
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2.5">
                            2.- Cuadro de Pendientes
                        </span>
                    </Link>
                    <details
                        class="py-3 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary
                            class="px-2 flex items-center space-x-2 hover:text-white"
                        >
                            <svg
                                class="text-[18px]"
                                xmlns="http://www.w3.org/2000/svg"
                                width="1em"
                                height="1em"
                                viewBox="0 0 2048 2048"
                            >
                                <path
                                    fill="currentColor"
                                    d="M1185 1179q-88-75-195-115t-222-40q-88 0-170 23t-153 64t-129 100t-100 130t-65 153t-23 170H0q0-120 35-231t101-205t156-167t204-115q-113-74-176-186t-64-248q0-106 40-199t109-163T568 40T768 0t199 40t163 109t110 163t40 200q0 66-16 129t-48 119t-75 103t-101 83q65 25 124 61t111 81zM384 512q0 80 30 149t82 122t122 83t150 30q79 0 149-30t122-82t83-122t30-150q0-79-30-149t-82-122t-123-83t-149-30q-80 0-149 30t-122 82t-83 123t-30 149m1344 256q66 0 124 25t101 69t69 102t26 124t-25 124t-69 102t-102 69t-124 25q-23 0-45-3l-587 587q-27 27-62 41t-74 15q-40 0-75-15t-61-41t-41-61t-15-75q0-38 14-73t42-63l587-587q-3-22-3-45q0-66 25-124t68-101t102-69t125-26m0 512q40 0 75-15t61-41t41-61t15-75q0-41-19-82l-146 146h-91v-91l146-146q-41-19-82-19q-40 0-75 15t-61 41t-41 61t-15 75q0 41 19 82l-640 641q-19 19-19 45t19 45t45 19t45-19l641-640q41 19 82 19"
                                />
                            </svg>
                            <span v-show="dataOpenSideBar" class="ml-2">
                                Servicio Tecnico
                            </span>
                        </summary>
                        <Link
                            :href="route('hservicios.index')"
                            :active="route().current('hservicios.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >4.- Informes Tecnico</span
                            ></Link
                        >
                        <Link
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Registrar Informe Tecnico</span
                            ></Link
                        >
                        <a
                            href="https://onedrive.live.com/?authkey=%21AD9JIIeIEy788PM&id=D9E6E0F5EA45EB0A%216187&cid=D9E6E0F5EA45EB0A"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"
                            target="_blank"
                            rel="noopener noreferrer"
                            ><span class="ml-4">Manuales y Calibración</span
                            ><img src="/img/one_drive.png" class="w-4"
                        /></a>
                    </details>
                    <Link
                        :href="route('actasConformidad.index')"
                        :active="route().current('actasConformidad.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 24 24"
                        >
                            <g
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path
                                    d="m18.18 8.04l.463-.464a1.966 1.966 0 1 1 2.781 2.78l-.463.464M18.18 8.04s.058.984.927 1.853s1.854.927 1.854.927M18.18 8.04l-4.26 4.26c-.29.288-.434.433-.558.592q-.22.282-.374.606c-.087.182-.151.375-.28.762l-.413 1.24l-.134.401m8.8-5.081l-4.26 4.26c-.29.29-.434.434-.593.558q-.282.22-.606.374c-.182.087-.375.151-.762.28l-1.24.413l-.401.134m0 0l-.401.134a.53.53 0 0 1-.67-.67l.133-.402m.938.938l-.938-.938"
                                />
                                <path
                                    stroke-linecap="round"
                                    d="M8 13h2.5M8 9h6.5M8 17h1.5M3 14v-4c0-3.771 0-5.657 1.172-6.828S7.229 2 11 2h2c3.771 0 5.657 0 6.828 1.172M21 14c0 3.771 0 5.657-1.172 6.828m-15.656 0C5.343 22 7.229 22 11 22h2c3.771 0 5.657 0 6.828-1.172m0 0c.944-.943 1.127-2.348 1.163-4.828"
                                />
                            </g>
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Actas de Conformidad
                        </span>
                    </Link>

                    <details
                        class="py-3 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary
                            class="px-2 flex items-center space-x-2 hover:text-white"
                        >
                            <svg
                                class="text-[18px]"
                                xmlns="http://www.w3.org/2000/svg"
                                width="1em"
                                height="1em"
                                viewBox="0 0 2048 2048"
                            >
                                <path
                                    fill="currentColor"
                                    d="M1185 1179q-88-75-195-115t-222-40q-88 0-170 23t-153 64t-129 100t-100 130t-65 153t-23 170H0q0-120 35-231t101-205t156-167t204-115q-113-74-176-186t-64-248q0-106 40-199t109-163T568 40T768 0t199 40t163 109t110 163t40 200q0 66-16 129t-48 119t-75 103t-101 83q65 25 124 61t111 81zM384 512q0 80 30 149t82 122t122 83t150 30q79 0 149-30t122-82t83-122t30-150q0-79-30-149t-82-122t-123-83t-149-30q-80 0-149 30t-122 82t-83 123t-30 149m1344 256q66 0 124 25t101 69t69 102t26 124t-25 124t-69 102t-102 69t-124 25q-23 0-45-3l-587 587q-27 27-62 41t-74 15q-40 0-75-15t-61-41t-41-61t-15-75q0-38 14-73t42-63l587-587q-3-22-3-45q0-66 25-124t68-101t102-69t125-26m0 512q40 0 75-15t61-41t41-61t15-75q0-41-19-82l-146 146h-91v-91l146-146q-41-19-82-19q-40 0-75 15t-61 41t-41 61t-15 75q0 41 19 82l-640 641q-19 19-19 45t19 45t45 19t45-19l641-640q41 19 82 19"
                                />
                            </svg>
                            <span v-show="dataOpenSideBar" class="ml-2">
                                Certificaciones
                            </span>
                        </summary>
                        <Link
                            :href="route('hservicios.index')"
                            :active="route().current('hservicios.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Registrar Solicitud de Certificacion</span
                            ></Link
                        >
                        <Link
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Solicitudes Emitidas</span
                            ></Link
                        >
                        <Link
                            :href="route('hservicios.create')"
                            :active="route().current('hservicios.create')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Certificaciones Emitidas</span
                            ></Link
                        >
                    </details>

                    <details
                        class="py-3.5 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary class="px-2 flex space-x-2 hover:text-white">
                            <span class="bi bi-graph-up-arrow"
                                ><span v-show="dataOpenSideBar" class="ml-2"
                                    >Cotizaciones</span
                                ></span
                            >
                        </summary>
                        <Link
                            :href="route('cventas.index')"
                            :active="route().current('cventas.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Listar Cotizacion por Venta</span
                            ></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Listar Cotizacion por Servicio</span
                            ></Link
                        >
                    </details>
                    <details
                        class="py-3.5 rounded-sm text-gray-400 cursor-pointer"
                    >
                        <summary class="px-2 flex space-x-2 hover:text-white">
                            <span class="bi bi-graph-up-arrow"
                                ><span v-show="dataOpenSideBar" class="ml-2"
                                    >Almacen</span
                                ></span
                            >
                        </summary>
                        <Link
                            :href="route('categories.index')"
                            :active="route().current('productos.*')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4">Inventario</span></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Ingreso de Materiales</span
                            ></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4"
                                >Salida de Materiales</span
                            ></Link
                        >
                        <Link
                            :href="route('c_servicos.index')"
                            :active="route().current('c_servicos.index')"
                            v-show="dataOpenSideBar"
                            class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"
                            ><span class="ml-4">Generar Kardex</span></Link
                        >
                    </details>
                    <!-- <details class="py-3.5 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-bar-chart-line'><span v-show="dataOpenSideBar" class="ml-2">Prestamos o Alquiler</span></span></summary>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Listar Equipos</span> <img src="../../../public/img/excel.png" class="w-4"></Link>
                        </details> -->
                    <!-- <details class="py-3.5 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-patch-check'><span v-show="dataOpenSideBar" class="ml-2">Certificaciones</span></span></summary>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Registrar Solicitud Certificacion</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Solicitudes Emitidas</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Certificaciones Emitidas</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white whitespace-pre-line"><span class="ml-4">Certificaciones por Caducar</span></Link>
                            <Link href="#" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Lista Certificados emitidos TW</span> <img src="../../../public/img/excel.png" class="w-4"></Link>
                        </details> -->
                    <Link
                        :href="route('cajaChica.index')"
                        :active="route().current('cajaChica.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="22"
                            height="24"
                            viewBox="0 0 24 24"
                        >
                            <g
                                fill="none"
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                color="currentColor"
                            >
                                <path
                                    d="M20.943 16.835a15.76 15.76 0 0 0-4.476-8.616c-.517-.503-.775-.754-1.346-.986C14.55 7 14.059 7 13.078 7h-2.156c-.981 0-1.472 0-2.043.233c-.57.232-.83.483-1.346.986a15.76 15.76 0 0 0-4.476 8.616C2.57 19.773 5.28 22 8.308 22h7.384c3.029 0 5.74-2.227 5.25-5.165"
                                />
                                <path
                                    d="M7.257 4.443c-.207-.3-.506-.708.112-.8c.635-.096 1.294.338 1.94.33c.583-.009.88-.268 1.2-.638C10.845 2.946 11.365 2 12 2s1.155.946 1.491 1.335c.32.37.617.63 1.2.637c.646.01 1.305-.425 1.94-.33c.618.093.319.5.112.8l-.932 1.359c-.4.58-.599.87-1.017 1.035S13.837 7 12.758 7h-1.516c-1.08 0-1.619 0-2.036-.164S8.589 6.38 8.189 5.8zm6.37 8.476c-.216-.799-1.317-1.519-2.638-.98s-1.53 2.272.467 2.457c.904.083 1.492-.097 2.031.412c.54.508.64 1.923-.739 2.304c-1.377.381-2.742-.214-2.89-1.06m1.984-5.06v.761m0 5.476v.764"
                                />
                            </g>
                        </svg>
                        <!-- <svg :class="'text-4xl'" xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 48 48"><g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"><path d="M28.772 24.667A4 4 0 0 0 25 22v-1h-2v1a4 4 0 1 0 0 8v4c-.87 0-1.611-.555-1.887-1.333a1 1 0 1 0-1.885.666A4 4 0 0 0 23 36v1h2v-1a4 4 0 0 0 0-8v-4a2 2 0 0 1 1.886 1.333a1 1 0 1 0 1.886-.666M23 24a2 2 0 1 0 0 4zm2 10a2 2 0 1 0 0-4z"/><path d="M13.153 8.621C15.607 7.42 19.633 6 24.039 6c4.314 0 8.234 1.361 10.675 2.546l.138.067c.736.364 1.33.708 1.748.987L32.906 15C41.422 23.706 48 41.997 24.039 41.997S6.479 24.038 15.069 15l-3.67-5.4c.283-.185.642-.4 1.07-.628q.318-.171.684-.35m17.379 6.307l2.957-4.323c-2.75.198-6.022.844-9.172 1.756c-2.25.65-4.75.551-7.065.124a25 25 0 0 1-1.737-.386l1.92 2.827c4.115 1.465 8.981 1.465 13.097.002M16.28 16.63c4.815 1.86 10.602 1.86 15.417-.002a29.3 29.3 0 0 1 4.988 7.143c1.352 2.758 2.088 5.515 1.968 7.891c-.116 2.293-1.018 4.252-3.078 5.708c-2.147 1.517-5.758 2.627-11.537 2.627c-5.785 0-9.413-1.091-11.58-2.591c-2.075-1.437-2.986-3.37-3.115-5.632c-.135-2.35.585-5.093 1.932-7.87c1.285-2.648 3.078-5.197 5.005-7.274m-1.15-6.714c.8.238 1.636.445 2.484.602c2.15.396 4.306.454 6.146-.079a54 54 0 0 1 6.53-1.471C28.45 8.414 26.298 8 24.038 8c-3.445 0-6.658.961-8.908 1.916"/></g></svg> -->
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Caja Chica
                        </span>
                    </Link>
                    <Link
                        :href="route('users.index')"
                        :active="route().current('users.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <svg
                            class="text-xl"
                            xmlns="http://www.w3.org/2000/svg"
                            width="1em"
                            height="1em"
                            viewBox="0 0 18 18"
                        >
                            <g fill="currentColor" fill-rule="evenodd">
                                <path
                                    d="M13.689 11.132c1.155 1.222 1.953 2.879 2.183 4.748a1.007 1.007 0 0 1-1 1.12H3.007a1.005 1.005 0 0 1-1-1.12c.23-1.87 1.028-3.526 2.183-4.748c.247.228.505.442.782.633c-1.038 1.069-1.765 2.55-1.972 4.237L14.872 16c-.204-1.686-.93-3.166-1.966-4.235a7 7 0 0 0 .783-.633M8.939 1c1.9 0 3 2 4.38 2.633a2.48 2.48 0 0 1-1.88.867c-.298 0-.579-.06-.844-.157A3.73 3.73 0 0 1 7.69 5.75c-1.395 0-3.75.25-3.245-1.903C5.94 3 6.952 1 8.94 1"
                                />
                                <path
                                    d="M8.94 2c2.205 0 4 1.794 4 4s-1.795 4-4 4c-2.207 0-4-1.794-4-4s1.793-4 4-4m0 9A5 5 0 1 0 8.937.999A5 5 0 0 0 8.94 11"
                                />
                            </g>
                        </svg>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Usuarios
                        </span>
                    </Link>
                    <!-- <Link
                        :href="route('categories.index')"
                        :active="route().current('productos.*')"
                        class="py-3.5 rounded-sm text-gray-400 px-2 flex items-center space-x-2 p-2 cursor-pointer hover:text-white whitespace-pre-line"
                    >
                        <span class="bi bi-inboxes"></span>
                        <span v-show="dataOpenSideBar" class="ml-2">
                            Almacen
                        </span>
                    </Link> -->
                    <!-- <details class="py-3.5 rounded-sm text-gray-400 cursor-pointer">
                            <summary class="px-2 flex space-x-2 hover:text-white"><span class='bi bi-inboxes'><span v-show="dataOpenSideBar" class="ml-2">Recursos</span></span></summary>
                            <Link :href="route('categories.index')" :active="route().current('productos.*')" v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white"><span class="ml-4">Inventario</span></Link>
                            <p v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Reuniones Semanales</span> <img src="../../../public/img/excel.png" class="w-4"></p>
                            <p v-show="dataOpenSideBar" class="px-2 flex space-x-2 p-2 ms-4 cursor-pointer hover:text-white py-2 items-center gap-2 whitespace-pre-line"><span class="ml-4">Ocurrencias</span> <img src="../../../public/img/excel.png" class="w-4"></p>
                        </details> -->
                </div>

                <!-- Scrool para sidebar -->
            </div>
        </div>
        <div
            class="menu-man text-left px-2 justify-start whitespace-nowrap border-t border-gray-700 dark:border-gray-700"
        >
            <div
                class="py-3 rounded-md cursor-pointer text-gray-400 hover:text-white"
                @click="logout"
            >
                <span class="px-2 flex items-center gap-4">
                    <svg
                        class="text-xl"
                        xmlns="http://www.w3.org/2000/svg"
                        width="1em"
                        height="1em"
                        viewBox="0 0 512 512"
                    >
                        <path
                            fill="currentColor"
                            d="M217 28.098v455.804l142-42.597V70.697zm159.938 26.88l.062 2.327V87h16V55zM119 55v117.27h18V73h62V55zm258 50v16h16v-16zm0 34v236h16V139zm-240 58.727V233H41v46h96v35.273L195.273 256zM244 232c6.627 0 12 10.745 12 24s-5.373 24-12 24s-12-10.745-12-24s5.373-24 12-24M137 339.73h-18V448h18zM377 393v14h16v-14zm0 32v23h16v-23zM32 471v18h167v-18zm290.652 0l-60 18H480v-18z"
                        />
                    </svg>
                    <span v-show="dataOpenSideBar">Cerrar Sesión</span>
                </span>
            </div>
        </div>
        <div
            class="modo_oscuro w-full flex justify-between px-4 py-2 hover:text-white"
        >
            <div
                class="info h-8 flex items-center w-36 text-gray-300 hover:text-gray-100 dark:text-gray-400 gap-4 overflow-hidden cursor-pointer"
                @click="toggleDarkMode"
            >
                <div>
                    <svg
                        class="text-xl hover:text-white"
                        xmlns="http://www.w3.org/2000/svg"
                        width="1em"
                        height="1em"
                        viewBox="0 0 24 24"
                    >
                        <g fill="currentColor" fill-opacity="0">
                            <path
                                d="M15.22 6.03l2.53-1.94L14.56 4L13.5 1l-1.06 3l-3.19.09l2.53 1.94l-.91 3.06l2.63-1.81l2.63 1.81z"
                            >
                                <animate
                                    id="lineMdMoonLoop0"
                                    fill="freeze"
                                    attributeName="fill-opacity"
                                    begin="0.7s;lineMdMoonLoop0.begin+6s"
                                    dur="0.4s"
                                    values="0;1"
                                />
                                <animate
                                    fill="freeze"
                                    attributeName="fill-opacity"
                                    begin="lineMdMoonLoop0.begin+2.2s"
                                    dur="0.4s"
                                    values="1;0"
                                />
                            </path>
                            <path
                                d="M13.61 5.25L15.25 4l-2.06-.05L12.5 2l-.69 1.95L9.75 4l1.64 1.25l-.59 1.98l1.7-1.17l1.7 1.17z"
                            >
                                <animate
                                    fill="freeze"
                                    attributeName="fill-opacity"
                                    begin="lineMdMoonLoop0.begin+3s"
                                    dur="0.4s"
                                    values="0;1"
                                />
                                <animate
                                    fill="freeze"
                                    attributeName="fill-opacity"
                                    begin="lineMdMoonLoop0.begin+5.2s"
                                    dur="0.4s"
                                    values="1;0"
                                />
                            </path>
                            <path
                                d="M19.61 12.25L21.25 11l-2.06-.05L18.5 9l-.69 1.95l-2.06.05l1.64 1.25l-.59 1.98l1.7-1.17l1.7 1.17z"
                            >
                                <animate
                                    fill="freeze"
                                    attributeName="fill-opacity"
                                    begin="lineMdMoonLoop0.begin+0.4s"
                                    dur="0.4s"
                                    values="0;1"
                                />
                                <animate
                                    fill="freeze"
                                    attributeName="fill-opacity"
                                    begin="lineMdMoonLoop0.begin+2.8s"
                                    dur="0.4s"
                                    values="1;0"
                                />
                            </path>
                            <path
                                d="M20.828 9.731l1.876-1.439l-2.366-.067L19.552 6l-.786 2.225l-2.366.067l1.876 1.439L17.601 12l1.951-1.342L21.503 12z"
                            >
                                <animate
                                    fill="freeze"
                                    attributeName="fill-opacity"
                                    begin="lineMdMoonLoop0.begin+3.4s"
                                    dur="0.4s"
                                    values="0;1"
                                />
                                <animate
                                    fill="freeze"
                                    attributeName="fill-opacity"
                                    begin="lineMdMoonLoop0.begin+5.6s"
                                    dur="0.4s"
                                    values="1;0"
                                />
                            </path>
                        </g>
                        <path
                            fill="none"
                            stroke="currentColor"
                            stroke-dasharray="56"
                            stroke-dashoffset="56"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 6 C7 12.08 11.92 17 18 17 C18.53 17 19.05 16.96 19.56 16.89 C17.95 19.36 15.17 21 12 21 C7.03 21 3 16.97 3 12 C3 8.83 4.64 6.05 7.11 4.44 C7.04 4.95 7 5.47 7 6 Z"
                        >
                            <animate
                                fill="freeze"
                                attributeName="stroke-dashoffset"
                                dur="0.6s"
                                values="56;0"
                            />
                        </path>
                    </svg>
                </div>
                <span class="font-normal text-base text-gray-400"
                    >Modo Oscuro</span
                >
            </div>
            <div
                v-show="dataOpenSideBar"
                @click="toggleDarkMode"
                class="w-14 h-8 flex items-center justify-center cursor-pointer"
            >
                <div
                    class="base_swith w-10 h-5 rounded-full relative flex items-center"
                    :class="{
                        'bg-green-600': isDarkMode,
                        'bg-gray-700': !isDarkMode,
                    }"
                >
                    <div
                        :class="{
                            'bg-white': isDarkMode,
                            'bg-white': !isDarkMode,
                        }"
                        :style="{
                            left: isDarkMode
                                ? 'calc(80% - 0.75rem)'
                                : '0.25rem',
                        }"
                        class="circulo_swith duration-300 w-4 h-4 rounded-full bg-gray-100 absolute"
                    ></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        dataOpenSideBar: Boolean,
    },
    setup() {
        const isDarkMode = ref(false);
        const switchPosition = computed(() =>
            isDarkMode.value ? "calc(80% - 0.75rem)" : "0.25rem"
        );

        const toggleDarkMode = () => {
            isDarkMode.value = !isDarkMode.value;
            document.documentElement.classList.toggle("dark", isDarkMode.value);
            localStorage.setItem("modoOscuro", isDarkMode.value);
        };

        onMounted(() => {
            const modoOscuroLocalStorage =
                localStorage.getItem("modoOscuro") === "true";
            isDarkMode.value = modoOscuroLocalStorage;
            document.documentElement.classList.toggle(
                "dark",
                modoOscuroLocalStorage
            );
        });

        return {
            isDarkMode,
            toggleDarkMode,
            switchPosition,
        };
    },
};
</script>

<style>
.tooltip-text {
    font-size: 10px !important;
}

#side-bar {
    overflow: hidden;
    transition: 300ms;
}

.side-bar-visible {
    width: 250px !important;
}

.side-bar-close {
    width: 50px !important;
}
@media (max-width: 768px) {
    .side-bar-close {
        width: 0px !important;
    }
}
</style>

<style scoped>
#nombre_Logo {
    font-family: "AlphaMaleModern";
    letter-spacing: 3px;
}

#B_logo {
    font-family: "AlphaMaleModern";
    transform: translateX(2px) translateY(1px);
}
</style>
