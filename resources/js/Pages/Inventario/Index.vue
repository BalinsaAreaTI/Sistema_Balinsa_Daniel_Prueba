<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
</script>
<template>
    <AppLayout title="Inventario">
        <template #header>
            <h1
                class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white"
            >
                Inventario
            </h1>
        </template>

        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800"
                >
                    <!-- Filters Section -->
                    <div
                        class="filters bg-white dark:bg-gray-700 p-4 rounded-lg shadow mb-6"
                    >
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Material Search Filter -->
                            <div>
                                <label
                                    for="search"
                                    class="block text-sm font-medium mb-1 text-black dark:text-white"
                                    >Buscar Material</label
                                >
                                <input
                                    type="text"
                                    id="search"
                                    v-model="searchQuery"
                                    placeholder="Buscar....."
                                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-700"
                                />
                            </div>

                            <!-- Warehouse Filter -->
                            <div>
                                <label
                                    for="warehouse"
                                    class="block text-sm font-medium mb-1"
                                    >Seleccione Almacen</label
                                >
                                <select
                                    id="warehouse"
                                    v-model="selectedWarehouse"
                                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-700"
                                >
                                    <option value="all">
                                        Todos los Almacenes
                                    </option>
                                    <option
                                        v-for="warehouse in warehouses"
                                        :key="warehouse.id"
                                        :value="warehouse.id"
                                    >
                                        {{ warehouse.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Materials List -->
                    <div
                        class="materials-list bg-white rounded-lg shadow overflow-hidden"
                    >
                        <div
                            class="overflow-x-auto scroll-dataTableLEO shadow-md md:rounded-lg rounded-md shadow-gray-200 dark:shadow-gray-500"
                        >
                            <table
                                class="min-w-full divide-y divide-gray-200 text-sm text-left rtl:text-right text-gray-500 dark:text-white"
                            >
                                <thead
                                    class="text-xs text-white uppercase bg-green-600 dark:bg-green-600"
                                >
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                        >
                                            Foto
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                        >
                                            Nombre
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                        >
                                            Descripcion
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                        >
                                            Cantidad
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                        >
                                            Marca/Modelo
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                        >
                                            Stock
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                        >
                                            Fecha de Registro
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"
                                        >
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <!-- <tbody
                                    class="bg-white divide-y divide-gray-200"
                                > -->
                                <tbody class="text-center text-xs">
                                    <tr
                                        v-for="material in paginatedMaterials"
                                        :key="material.id"
                                        class="bg-white text-black border-b border-gray-300 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-900 hover:bg-gray-500 cursor-pointer"
                                    >
                                        <!-- <td class="px-6 py-4 whitespace-nowrap"> -->
                                        <td
                                            class="px-6 py-3 whitespace-nowrap dark:border-white border-b"
                                        >
                                            <img
                                                :src="material.image"
                                                :alt="material.name"
                                                class="h-12 w-12 rounded object-cover"
                                            />
                                        </td>
                                        <td
                                            class="px-6 py-3 text-center dark:border-white border-b"
                                        >
                                            <div class="text-sm font-medium">
                                                {{ material.name }}
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-3 text-center dark:border-white border-b"
                                        >
                                            <div
                                                class="text-sm truncate max-w-xs"
                                            >
                                                {{ material.description }}
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-3 text-center dark:border-white border-b"
                                        >
                                            <div class="text-sm">
                                                {{ material.quantity }}
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-3 text-center dark:border-white border-b"
                                        >
                                            <div class="text-sm">
                                                {{ material.brand }} /
                                                {{ material.model }}
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-3 text-center dark:border-white border-b"
                                        >
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="{
                                                    'bg-green-100 text-green-800':
                                                        material.stock > 10,
                                                    'bg-yellow-100 text-yellow-800':
                                                        material.stock > 0 &&
                                                        material.stock <= 10,
                                                    'bg-red-100 text-red-800':
                                                        material.stock === 0,
                                                }"
                                            >
                                                {{ material.stock }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-3 text-center dark:border-white border-b"
                                        >
                                            <div class="text-sm">
                                                {{
                                                    formatDate(
                                                        material.registrationDate
                                                    )
                                                }}
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-3 text-center dark:border-white border-b"
                                        >
                                            <button
                                                @click="viewMaterial(material)"
                                                class="text-green-600 hover:text-green-900 mr-3"
                                                aria-label="View material details"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="20"
                                                    height="20"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="inline-block"
                                                >
                                                    <path
                                                        d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"
                                                    ></path>
                                                    <circle
                                                        cx="12"
                                                        cy="12"
                                                        r="3"
                                                    ></circle>
                                                </svg>
                                            </button>
                                            <button
                                                @click="editMaterial(material)"
                                                class="text-blue-600 hover:text-blue-900"
                                                aria-label="Edit material"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="20"
                                                    height="20"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="inline-block"
                                                >
                                                    <path
                                                        d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"
                                                    ></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="filteredMaterials.length === 0">
                                        <td
                                            colspan="8"
                                            class="px-6 py-4 text-center text-sm text-gray-500"
                                        >
                                            No materials found matching your
                                            criteria.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div
                            class="pagination bg-gray-50 dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
                        >
                            <div
                                class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
                            >
                                <!-- <div>
                                    <p class="text-sm text-gray-700">
                                        Showing
                                        <span class="font-medium">{{
                                            paginationStart + 1
                                        }}</span>
                                        to
                                        <span class="font-medium">{{
                                            Math.min(
                                                paginationEnd,
                                                filteredMaterials.length
                                            )
                                        }}</span>
                                        of
                                        <span class="font-medium">{{
                                            filteredMaterials.length
                                        }}</span>
                                        results
                                    </p>
                                </div> -->
                                <div>
                                    <nav
                                        class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                        aria-label="Pagination"
                                    >
                                        <button
                                            @click="currentPage = 1"
                                            :disabled="currentPage === 1"
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 hover:bg-blue-500 hover:text-white"
                                            :class="{
                                                'opacity-50 cursor-not-allowed':
                                                    currentPage === 1,
                                            }"
                                        >
                                            <span class="sr-only">First</span>
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="16"
                                                height="16"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="inline-block"
                                            >
                                                <path d="m11 17-5-5 5-5"></path>
                                                <path d="m18 17-5-5 5-5"></path>
                                            </svg>
                                        </button>
                                        <button
                                            @click="prevPage"
                                            :disabled="currentPage === 1"
                                            class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 hover:bg-blue-500 hover:text-white"
                                            :class="{
                                                'opacity-50 cursor-not-allowed':
                                                    currentPage === 1,
                                            }"
                                        >
                                            <span class="sr-only">Antes</span>
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="16"
                                                height="16"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="inline-block"
                                            >
                                                <path d="m15 18-6-6 6-6"></path>
                                            </svg>
                                        </button>

                                        <!-- Page Numbers -->
                                        <template
                                            v-for="page in displayedPageNumbers"
                                            :key="page"
                                        >
                                            <button
                                                v-if="page !== '...'"
                                                @click="goToPage(page)"
                                                :class="[
                                                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                                    currentPage === page
                                                        ? 'z-10 bg-gray-800 border-gray-800 text-white dark:bg-gray-50 dark:text-black'
                                                        : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-white',
                                                ]"
                                            >
                                                {{ page }}
                                            </button>
                                            <span
                                                v-else
                                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                                            >
                                                {{ page }}
                                            </span>
                                        </template>

                                        <button
                                            @click="nextPage"
                                            :disabled="
                                                currentPage === totalPages
                                            "
                                            class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 hover:bg-blue-500 hover:text-white"
                                            :class="{
                                                'opacity-50 cursor-not-allowed':
                                                    currentPage === totalPages,
                                            }"
                                        >
                                            <span class="sr-only"
                                                >Siguiente</span
                                            >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="16"
                                                height="16"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="inline-block"
                                            >
                                                <path d="m9 18 6-6-6-6"></path>
                                            </svg>
                                        </button>
                                        <button
                                            @click="currentPage = totalPages"
                                            :disabled="
                                                currentPage === totalPages
                                            "
                                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 hover:bg-blue-500 hover:text-white"
                                            :class="{
                                                'opacity-50 cursor-not-allowed':
                                                    currentPage === totalPages,
                                            }"
                                        >
                                            <span class="sr-only">Last</span>
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="16"
                                                height="16"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="inline-block"
                                            >
                                                <path d="m13 17 5-5-5-5"></path>
                                                <path d="m6 17 5-5-5-5"></path>
                                            </svg>
                                        </button>
                                    </nav>
                                </div>
                            </div>

                            <!-- Mobile Pagination -->
                            <div
                                class="flex items-center justify-between w-full sm:hidden"
                            >
                                <button
                                    @click="prevPage"
                                    :disabled="currentPage === 1"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                    :class="{
                                        'opacity-50 cursor-not-allowed':
                                            currentPage === 1,
                                    }"
                                >
                                    Antes
                                </button>
                                <span class="text-sm text-gray-700">
                                    Pagina {{ currentPage }}
                                </span>
                                <button
                                    @click="nextPage"
                                    :disabled="currentPage === totalPages"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                    :class="{
                                        'opacity-50 cursor-not-allowed':
                                            currentPage === totalPages,
                                    }"
                                >
                                    Siguiente
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- View Material Modal -->
                    <teleport to="body">
                        <div
                            v-if="viewModalOpen"
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[999999]"
                        >
                            <div
                                class="bg-white rounded-lg shadow-lg max-w-md w-full max-h-[90vh] overflow-y-auto"
                                @click.stop
                            >
                                <div class="p-6">
                                    <div
                                        class="flex justify-between items-start mb-4"
                                    >
                                        <h2 class="text-xl font-bold">
                                            {{ selectedMaterial.name }}
                                        </h2>
                                        <button
                                            @click="viewModalOpen = false"
                                            class="text-gray-500 hover:text-gray-700"
                                            aria-label="Close modal"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="24"
                                                height="24"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            >
                                                <path d="M18 6 6 18"></path>
                                                <path d="m6 6 12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 gap-4">
                                        <img
                                            :src="selectedMaterial.image"
                                            :alt="selectedMaterial.name"
                                            class="w-full h-48 object-cover rounded mb-4"
                                        />
                                        <div>
                                            <h3
                                                class="font-semibold text-gray-700"
                                            >
                                                Descripcion
                                            </h3>
                                            <p class="text-gray-600">
                                                {{
                                                    selectedMaterial.description
                                                }}
                                            </p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <h3
                                                    class="font-semibold text-gray-700"
                                                >
                                                    Marca
                                                </h3>
                                                <p class="text-gray-600">
                                                    {{ selectedMaterial.brand }}
                                                </p>
                                            </div>
                                            <div>
                                                <h3
                                                    class="font-semibold text-gray-700"
                                                >
                                                    Modelo
                                                </h3>
                                                <p class="text-gray-600">
                                                    {{ selectedMaterial.model }}
                                                </p>
                                            </div>
                                            <div>
                                                <h3
                                                    class="font-semibold text-gray-700"
                                                >
                                                    Cantidad
                                                </h3>
                                                <p class="text-gray-600">
                                                    {{
                                                        selectedMaterial.quantity
                                                    }}
                                                </p>
                                            </div>
                                            <div>
                                                <h3
                                                    class="font-semibold text-gray-700"
                                                >
                                                    Stock
                                                </h3>
                                                <p class="text-gray-600">
                                                    {{ selectedMaterial.stock }}
                                                </p>
                                            </div>
                                            <div>
                                                <h3
                                                    class="font-semibold text-gray-700"
                                                >
                                                    Almacen
                                                </h3>
                                                <p class="text-gray-600">
                                                    {{
                                                        getWarehouseName(
                                                            selectedMaterial.warehouseId
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                            <div>
                                                <h3
                                                    class="font-semibold text-gray-700"
                                                >
                                                    Fecha de Registro
                                                </h3>
                                                <p class="text-gray-600">
                                                    {{
                                                        formatDate(
                                                            selectedMaterial.registrationDate
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </teleport>

                    <!-- Edit Material Modal -->
                    <teleport to="body">
                        <div
                            v-if="editModalOpen"
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[999999]"
                        >
                            <div
                                class="bg-white rounded-lg shadow-lg max-w-lg w-full max-h-[90vh] overflow-y-auto"
                                @click.stop
                            >
                                <div class="p-6">
                                    <div
                                        class="flex justify-between items-start mb-4"
                                    >
                                        <h2 class="text-xl font-bold">
                                            Editar
                                        </h2>
                                        <button
                                            @click="editModalOpen = false"
                                            class="text-gray-500 hover:text-gray-700"
                                            aria-label="Close modal"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="24"
                                                height="24"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            >
                                                <path d="M18 6 6 18"></path>
                                                <path d="m6 6 12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <form @submit.prevent="saveMaterial">
                                        <div
                                            class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4"
                                        >
                                            <div class="md:col-span-2">
                                                <label
                                                    for="edit-name"
                                                    class="block text-sm font-medium mb-1"
                                                    >Nombre</label
                                                >
                                                <input
                                                    type="text"
                                                    id="edit-name"
                                                    v-model="editForm.name"
                                                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-500"
                                                    required
                                                />
                                            </div>
                                            <div class="md:col-span-2">
                                                <label
                                                    for="edit-description"
                                                    class="block text-sm font-medium mb-1"
                                                    >Descripcion</label
                                                >
                                                <textarea
                                                    id="edit-description"
                                                    v-model="
                                                        editForm.description
                                                    "
                                                    rows="3"
                                                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-500"
                                                ></textarea>
                                            </div>
                                            <div>
                                                <label
                                                    for="edit-brand"
                                                    class="block text-sm font-medium mb-1"
                                                    >Marca</label
                                                >
                                                <input
                                                    type="text"
                                                    id="edit-brand"
                                                    v-model="editForm.brand"
                                                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-500"
                                                    required
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    for="edit-model"
                                                    class="block text-sm font-medium mb-1"
                                                    >Modelo</label
                                                >
                                                <input
                                                    type="text"
                                                    id="edit-model"
                                                    v-model="editForm.model"
                                                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-500"
                                                    required
                                                />
                                            </div>
                                            <!-- <div>
                                <label
                                    for="edit-quantity"
                                    class="block text-sm font-medium mb-1"
                                    >Quantity</label
                                >
                                <input
                                    type="number"
                                    id="edit-quantity"
                                    v-model="editForm.quantity"
                                    min="0"
                                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-500"
                                    required
                                />
                            </div> -->
                                            <!-- <div>
                                <label
                                    for="edit-stock"
                                    class="block text-sm font-medium mb-1"
                                    >Stock</label
                                >
                                <input
                                    type="number"
                                    id="edit-stock"
                                    v-model="editForm.stock"
                                    min="0"
                                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-500"
                                    required
                                />
                            </div> -->
                                            <div>
                                                <label
                                                    for="edit-warehouse"
                                                    class="block text-sm font-medium mb-1"
                                                    >Almacen</label
                                                >
                                                <select
                                                    id="edit-warehouse"
                                                    v-model="
                                                        editForm.warehouseId
                                                    "
                                                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-500"
                                                    required
                                                >
                                                    <option
                                                        v-for="warehouse in warehouses"
                                                        :key="warehouse.id"
                                                        :value="warehouse.id"
                                                    >
                                                        {{ warehouse.name }}
                                                    </option>
                                                </select>
                                            </div>
                                            <!-- <div>
                                <label
                                    for="edit-date"
                                    class="block text-sm font-medium mb-1"
                                    >Registration Date</label
                                >
                                <input
                                    type="date"
                                    id="edit-date"
                                    v-model="editForm.registrationDate"
                                    class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-500"
                                    required
                                />
                            </div> -->
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button
                                                type="button"
                                                @click="editModalOpen = false"
                                                class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50"
                                            >
                                                Cancelar
                                            </button>
                                            <button
                                                type="submit"
                                                class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700"
                                            >
                                                Guardar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </teleport>

                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
export default {
    data() {
        return {
            searchQuery: "",
            selectedWarehouse: "all",
            viewModalOpen: false,
            editModalOpen: false,
            selectedMaterial: {},
            editForm: {
                id: null,
                name: "",
                description: "",
                quantity: 0,
                brand: "",
                model: "",
                stock: 0,
                warehouseId: null,
                registrationDate: "",
                image: "",
            },
            currentPage: 1,
            itemsPerPage: 10,
            warehouses: [
                { id: 1, name: "Almacen 1" },
                { id: 2, name: "Almacen 2" },
                { id: 3, name: "Almacen 3" },
                { id: 4, name: "Almacen 4" },
            ],
            materials: [
                {
                    id: 1,
                    name: "Aluminum Sheet",
                    description:
                        "High-quality aluminum sheet for industrial applications. Resistant to corrosion and lightweight.",
                    quantity: 50,
                    brand: "MetalPro",
                    model: "AL-1500",
                    stock: 45,
                    warehouseId: 1,
                    registrationDate: "2023-01-15",
                    image: "https://via.placeholder.com/150?text=Aluminum",
                },
                {
                    id: 2,
                    name: "Steel Pipe",
                    description:
                        "Heavy-duty steel pipe for construction and plumbing applications.",
                    quantity: 100,
                    brand: "SteelWorks",
                    model: "SP-200",
                    stock: 87,
                    warehouseId: 1,
                    registrationDate: "2023-02-20",
                    image: "https://via.placeholder.com/150?text=Steel+Pipe",
                },
                {
                    id: 3,
                    name: "Copper Wire",
                    description:
                        "High conductivity copper wire for electrical installations.",
                    quantity: 200,
                    brand: "ElectroPro",
                    model: "CW-100",
                    stock: 150,
                    warehouseId: 2,
                    registrationDate: "2023-03-10",
                    image: "https://via.placeholder.com/150?text=Copper+Wire",
                },
                {
                    id: 4,
                    name: "Concrete Mix",
                    description:
                        "Ready-to-use concrete mix for construction projects.",
                    quantity: 75,
                    brand: "BuildRight",
                    model: "CM-500",
                    stock: 60,
                    warehouseId: 3,
                    registrationDate: "2023-04-05",
                    image: "https://via.placeholder.com/150?text=Concrete",
                },
                {
                    id: 5,
                    name: "Glass Panel",
                    description: "Tempered glass panel for windows and doors.",
                    quantity: 30,
                    brand: "ClearView",
                    model: "GP-2000",
                    stock: 25,
                    warehouseId: 4,
                    registrationDate: "2023-05-12",
                    image: "https://via.placeholder.com/150?text=Glass",
                },
                {
                    id: 6,
                    name: "Wooden Beam",
                    description:
                        "Solid oak wooden beam for construction and furniture making.",
                    quantity: 40,
                    brand: "TimberCraft",
                    model: "WB-300",
                    stock: 35,
                    warehouseId: 2,
                    registrationDate: "2023-06-18",
                    image: "https://via.placeholder.com/150?text=Wood",
                },
                {
                    id: 7,
                    name: "PVC Pipe",
                    description:
                        "Durable PVC pipe for plumbing and drainage systems.",
                    quantity: 120,
                    brand: "PlastiFlow",
                    model: "PVC-400",
                    stock: 100,
                    warehouseId: 3,
                    registrationDate: "2023-07-22",
                    image: "https://via.placeholder.com/150?text=PVC",
                },
                {
                    id: 8,
                    name: "Insulation Roll",
                    description:
                        "Thermal insulation roll for walls and ceilings.",
                    quantity: 60,
                    brand: "ThermoGuard",
                    model: "IR-500",
                    stock: 0,
                    warehouseId: 1,
                    registrationDate: "2023-08-30",
                    image: "https://via.placeholder.com/150?text=Insulation",
                },
                {
                    id: 9,
                    name: "Ceramic Tiles",
                    description:
                        "Premium ceramic tiles for flooring and wall applications.",
                    quantity: 500,
                    brand: "TileMaster",
                    model: "CT-200",
                    stock: 450,
                    warehouseId: 4,
                    registrationDate: "2023-09-05",
                    image: "https://via.placeholder.com/150?text=Ceramic",
                },
                {
                    id: 10,
                    name: "Roofing Shingles",
                    description:
                        "Asphalt roofing shingles with weather resistance.",
                    quantity: 300,
                    brand: "RoofPro",
                    model: "RS-100",
                    stock: 275,
                    warehouseId: 2,
                    registrationDate: "2023-09-15",
                    image: "https://via.placeholder.com/150?text=Shingles",
                },
                {
                    id: 11,
                    name: "Paint Bucket",
                    description: "Interior latex paint, premium quality.",
                    quantity: 80,
                    brand: "ColorMaster",
                    model: "LP-500",
                    stock: 65,
                    warehouseId: 3,
                    registrationDate: "2023-09-25",
                    image: "https://via.placeholder.com/150?text=Paint",
                },
                {
                    id: 12,
                    name: "Drywall Sheet",
                    description: "Standard drywall sheets for interior walls.",
                    quantity: 150,
                    brand: "WallPro",
                    model: "DW-800",
                    stock: 120,
                    warehouseId: 1,
                    registrationDate: "2023-10-05",
                    image: "https://via.placeholder.com/150?text=Drywall",
                },
                {
                    id: 13,
                    name: "Cement Bag",
                    description:
                        "Portland cement for general construction use.",
                    quantity: 200,
                    brand: "CementCo",
                    model: "PC-50",
                    stock: 180,
                    warehouseId: 3,
                    registrationDate: "2023-10-15",
                    image: "https://via.placeholder.com/150?text=Cement",
                },
                {
                    id: 14,
                    name: "Electrical Panel",
                    description:
                        "Residential electrical panel with circuit breakers.",
                    quantity: 25,
                    brand: "PowerSafe",
                    model: "EP-200",
                    stock: 18,
                    warehouseId: 2,
                    registrationDate: "2023-10-25",
                    image: "https://via.placeholder.com/150?text=Electrical",
                },
                {
                    id: 15,
                    name: "Plywood Sheet",
                    description:
                        "Construction grade plywood for various applications.",
                    quantity: 100,
                    brand: "WoodSupply",
                    model: "PW-750",
                    stock: 85,
                    warehouseId: 4,
                    registrationDate: "2023-11-05",
                    image: "https://via.placeholder.com/150?text=Plywood",
                },
                {
                    id: 16,
                    name: "Brick Pallet",
                    description: "Standard red clay bricks for construction.",
                    quantity: 1000,
                    brand: "BrickWorks",
                    model: "RB-100",
                    stock: 850,
                    warehouseId: 1,
                    registrationDate: "2023-11-15",
                    image: "https://via.placeholder.com/150?text=Bricks",
                },
                {
                    id: 17,
                    name: "Gravel Bag",
                    description:
                        "Mixed size gravel for landscaping and construction.",
                    quantity: 150,
                    brand: "StoneSupply",
                    model: "MG-50",
                    stock: 130,
                    warehouseId: 3,
                    registrationDate: "2023-11-25",
                    image: "https://via.placeholder.com/150?text=Gravel",
                },
                {
                    id: 18,
                    name: "Window Frame",
                    description: "Aluminum window frame with double glazing.",
                    quantity: 40,
                    brand: "WindowPro",
                    model: "WF-300",
                    stock: 32,
                    warehouseId: 2,
                    registrationDate: "2023-12-05",
                    image: "https://via.placeholder.com/150?text=Window",
                },
                {
                    id: 19,
                    name: "Door Handle Set",
                    description: "Stainless steel door handle and lock set.",
                    quantity: 75,
                    brand: "LockMaster",
                    model: "DH-100",
                    stock: 60,
                    warehouseId: 4,
                    registrationDate: "2023-12-15",
                    image: "https://via.placeholder.com/150?text=Handle",
                },
                {
                    id: 20,
                    name: "Carpet Roll",
                    description: "Commercial grade carpet for office spaces.",
                    quantity: 30,
                    brand: "FloorCover",
                    model: "CR-200",
                    stock: 25,
                    warehouseId: 1,
                    registrationDate: "2023-12-25",
                    image: "https://via.placeholder.com/150?text=Carpet",
                },
            ],
        };
    },
    computed: {
        filteredMaterials() {
            return this.materials.filter((material) => {
                // Filter by warehouse
                if (
                    this.selectedWarehouse !== "all" &&
                    material.warehouseId !== parseInt(this.selectedWarehouse)
                ) {
                    return false;
                }

                // Filter by search query
                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    return (
                        material.name.toLowerCase().includes(query) ||
                        material.description.toLowerCase().includes(query) ||
                        material.brand.toLowerCase().includes(query) ||
                        material.model.toLowerCase().includes(query)
                    );
                }

                return true;
            });
        },
        totalPages() {
            return Math.ceil(this.filteredMaterials.length / this.itemsPerPage);
        },
        paginatedMaterials() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredMaterials.slice(start, end);
        },
        paginationStart() {
            return (this.currentPage - 1) * this.itemsPerPage;
        },
        paginationEnd() {
            return this.paginationStart + this.itemsPerPage;
        },
        displayedPageNumbers() {
            const totalPages = this.totalPages;
            const currentPage = this.currentPage;
            const pages = [];

            if (totalPages <= 7) {
                // If we have 7 or fewer pages, show all pages
                for (let i = 1; i <= totalPages; i++) {
                    pages.push(i);
                }
            } else {
                // Always show first page
                pages.push(1);

                // Show dots if current page is more than 3
                if (currentPage > 3) {
                    pages.push("...");
                }

                // Show pages around current page
                const startPage = Math.max(2, currentPage - 1);
                const endPage = Math.min(totalPages - 1, currentPage + 1);

                for (let i = startPage; i <= endPage; i++) {
                    pages.push(i);
                }

                // Show dots if current page is less than totalPages - 2
                if (currentPage < totalPages - 2) {
                    pages.push("...");
                }

                // Always show last page
                pages.push(totalPages);
            }

            return pages;
        },
    },
    watch: {
        searchQuery() {
            this.currentPage = 1; // Reset to first page when search changes
        },
        selectedWarehouse() {
            this.currentPage = 1; // Reset to first page when warehouse filter changes
        },
    },
    methods: {
        formatDate(dateString) {
            const options = { year: "numeric", month: "short", day: "numeric" };
            return new Date(dateString).toLocaleDateString(undefined, options);
        },
        getWarehouseName(warehouseId) {
            const warehouse = this.warehouses.find((w) => w.id === warehouseId);
            return warehouse ? warehouse.name : "Unknown";
        },
        viewMaterial(material) {
            this.selectedMaterial = { ...material };
            this.viewModalOpen = true;
        },
        editMaterial(material) {
            this.editForm = { ...material };
            this.editModalOpen = true;
        },
        saveMaterial() {
            // Find the index of the material to update
            const index = this.materials.findIndex(
                (m) => m.id === this.editForm.id
            );

            if (index !== -1) {
                // Update the material in the array
                this.materials[index] = { ...this.editForm };

                // Show success message (in a real app, you might use a toast notification)
                alert("Material updated successfully!");

                // Close the modal
                this.editModalOpen = false;
            }
        },
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
        goToPage(page) {
            this.currentPage = page;
        },
    },
};
</script>
