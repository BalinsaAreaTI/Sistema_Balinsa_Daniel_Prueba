<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { show_alerta, show_confirmacion } from '@/utils/alertasSwal';
import { ref, onMounted } from 'vue';
// import { ConsultaCajaChica } from '@/service/ConsultaCajaChica';
import DatePicker from 'primevue/datepicker';
import { initFlowbite } from 'flowbite';
import Swal from "sweetalert2";

// const listarCajaChica = ref([]);
const fechaHoy = new Date().toISOString().split('T')[0]; 
const fechaHoyTabla = new Date().toISOString().split('T')[0].split('-').reverse().join('-');

const fechaDesdeCajaChica = ref(fechaHoyTabla);
const fechaHastaCajaChica = ref(fechaHoyTabla);

function tablaEditable(){
    let tbodyReporteDeCajaChica = $('#bodyReporteDeCajaChica');
    tbodyReporteDeCajaChica.empty();
    agregarFilaEntrada(tbodyReporteDeCajaChica);
    hacerCeldasEditables(tbodyReporteDeCajaChica);
}

function agregarFilaEntrada(tbody) {
    const ahoraEnNY2 = new Date();
    const hours = ahoraEnNY2.getHours().toString().padStart(2, '0');
    const minutes = ahoraEnNY2.getMinutes().toString().padStart(2, '0');
    const seconds = ahoraEnNY2.getSeconds().toString().padStart(2, '0');
    const currentTime = hours + ":" + minutes + ":" + seconds;
    let nuevaFila = $('<tr class="bg-white pagosAgregarCajaChica border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 text-gray-900 dark:text-white dark:hover:bg-gray-600 cursor-pointer">');
    nuevaFila.append($('<td class="outline-none border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap text-gray-900 dark:text-white validarFormatoFechaTablas" contenteditable="true">').text(`${fechaHoyTabla}`)); //fecha
    nuevaFila.append($('<td class="outline-none border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap text-gray-900 dark:text-white validarFormatoHoraTablas" contenteditable="true">').text(currentTime)); //hora
    nuevaFila.append($('<td class="outline-none border-r dark:border-gray-700 p-2 font-medium text-gray-900 whitespace-nowrap dark:text-white nombreClienteTablaExcel" contenteditable="true">').text("")); //acreedor
    nuevaFila.append($('<td class="outline-none border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap" contenteditable="true">').text("")); //servicio
    nuevaFila.append($('<td class="outline-none border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap" contenteditable="true">').text("")); //descripcion
    nuevaFila.append($('<td class="outline-none border-r dark:border-gray-700 p-2 text-center cursor-pointer" contenteditable="true">').text("")); //razonSocial
    nuevaFila.append($('<td class="outline-none border-r dark:border-gray-700 p-2 text-center cursor-pointer" contenteditable="true">').text("")); //sustentodePago
    nuevaFila.append($('<td class="outline-none border-r dark:border-gray-700 p-2 text-center cursor-pointer" contenteditable="true">').text("")); //N°deTransaccion
    nuevaFila.append($('<td class="outline-none border-r dark:border-gray-700 p-2 text-center cursor-pointer">')
        .append($('<select class="w-28 h-10 uppercase outline-none bg-gray-50 border border-gray-300 text-gray-900 text-xs focus:ring-primary-600 focus:border-primary-600 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 rounded-lg">')
            .append($('<option>').attr('value', 'FACTURA').text('FACTURA'))
            .append($('<option>').attr('value', 'BOLETA').text('BOLETA'))
            .append($('<option>').attr('value', 'OTROS').text('OTROS'))
            .append($('<option>').attr('value', 'PENDIENTE').text('PENDIENTE'))
        )
    );
    nuevaFila.append($('<td class="outline-none border-r dark:border-gray-700 p-2 text-center cursor-pointer" contenteditable="true">').text("")); //ingreso
    nuevaFila.append($('<td class="outline-none border-r dark:border-gray-700 p-2 text-center cursor-pointer" contenteditable="true">').text("")); //salida
    nuevaFila.append($('<td class="outline-none border-r dark:border-gray-700 p-2 text-center cursor-pointer" contenteditable="true">').text("")); //link
    tbody.append(nuevaFila);

    nuevaFila.on('input', function() {
        let vacio = true;
        nuevaFila.find('td').each(function() {
            if ($(this).text().trim() !== "") {
                vacio = false;
            }
        });
        if (!vacio) {
            agregarFilaEntrada(tbody);
            copiarDatosPenultimaFila();
            nuevaFila.off('input');
        }
    });
}

function hacerCeldasEditables(tbody) {
    tbody.on('keydown', 'td[contenteditable="true"]', function(e) {
        let currentTd = $(this);
        let currentRow = currentTd.parent();
        let currentTdIndex = currentTd.index();

        if (e.key === "ArrowRight") {
            e.preventDefault();
            let nextTd = currentTd.nextAll('td[contenteditable="true"]').first();
            if (nextTd.length) {
                nextTd.focus();
            } else {
                let nextRow = currentRow.next();
                if (nextRow.length) {
                    nextRow.children('td[contenteditable="true"]').first().focus();
                }
            }
        } else if (e.key === "ArrowLeft") {
            e.preventDefault();
            let prevTd = currentTd.prevAll('td[contenteditable="true"]').first();
            if (prevTd.length) {
                prevTd.focus();
            } else {
                let prevRow = currentRow.prev();
                if (prevRow.length) {
                    prevRow.children('td[contenteditable="true"]').last().focus();
                }
            }
        } else if (e.key === "ArrowDown") {
            e.preventDefault();
            let nextRow = currentRow.next();
            if (nextRow.length) {
                nextRow.children().eq(currentTdIndex).focus();
            }
        } else if (e.key === "ArrowUp") {
            e.preventDefault();
            let prevRow = currentRow.prev();
            if (prevRow.length) {
                prevRow.children().eq(currentTdIndex).focus();
            }
        }
    });
}

function copiarDatosPenultimaFila() {
    let filas = $('.pagosAgregarCajaChica');
    if (filas.length > 1) {
        let penultimaFila = filas.eq(filas.length - 2);
        let ultimaFila = filas.eq(filas.length - 1);
        let datosColumna0 = penultimaFila.find('td').eq(0).text();
        let datosColumna6 = penultimaFila.find('td').eq(10).text();
        
        ultimaFila.find('td').eq(0).text(datosColumna0);
        ultimaFila.find('td').eq(10).text(datosColumna6);
    }
}

onMounted(() => {
    initFlowbite();

    // Esperar a que el DOM esté listo para asignar el evento con jQuery
    $(document).on("dblclick", "tr.editarPagos", function () {
        let fila = $(this).closest('tr');

        // Extraer los datos de la fila
        let idCaja = fila.find('td:eq(0)').text().trim();
        let fechaCaja = fila.find('td:eq(2)').text().trim();
        let horaCaja = fila.find('td:eq(3)').text().trim();
        let acreedorCaja = fila.find('td:eq(4)').text().trim();
        let servicioCaja = fila.find('td:eq(5)').text().trim();
        let comentarioAgregarPagoEditar = fila.find('td:eq(6)').text().trim();
        let razonSocialCaja = fila.find('td:eq(7)').text().trim();
        let sustentoCaja = fila.find('td:eq(8)').text().trim();
        let nTransaccionCaja = fila.find('td:eq(9)').text().trim();
        let sustentoGastoCaja = fila.find('td:eq(10)').text().trim();
        let ingresoCaja = fila.find('td:eq(11)').text().trim();
        let salidaCaja = fila.find('td:eq(12)').text().trim();
        let linkCaja = fila.find('td:eq(14)').text().trim();

        // Llenar los campos del modal
        $('#idCaja').text(idCaja);
        $('#fechaPrestamodeJabasAuto').val(fechaCaja);
        $('#horaAgregarEditar').val(horaCaja);
        $('#valorAcreedor').val(acreedorCaja);
        $('#comentarioAgregarPagoEditar').val(comentarioAgregarPagoEditar);
        $('#valorCantidadPrestamodeJabasAuto').val(servicioCaja);
        $('#valorRazonSocial').val(razonSocialCaja);
        $('#valorSustentodePago').val(sustentoCaja);
        $('#valorNumerodeTransaccion').val(nTransaccionCaja);
        $('#valorSustentodeGasto').val(sustentoGastoCaja);
        $('#valorCantidadIngreso').val(ingresoCaja);
        $('#valorCantidadSalida').val(salidaCaja);
        $('#valorLinkPagoCajaChica').val(linkCaja);

        // Mostrar el modal
        $('#ModalEditarPagoCajaChica').addClass('flex');
        $('#ModalEditarPagoCajaChica').removeClass('hidden');
    });

    $('#btnRegistrarPagoCajaChica').on('click', function () {
        let idCaja = $('#idCaja').text().trim(); 
        if (!idCaja) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'ID de Caja no encontrado.',
            });
            return;
        }

        let data = {
            idCaja: idCaja, // Se agregó
            fechaCaja: $('#fechaPrestamodeJabasAuto').val(),
            horaCaja: $('#horaAgregarEditar').val(),
            acreedorCaja: $('#valorAcreedor').val(),
            servicioCaja: $('#valorCantidadPrestamodeJabasAuto').val(),
            razonSocialCaja: $('#valorRazonSocial').val(),
            sustentoCaja: $('#valorSustentodePago').val(),
            nTransaccionCaja: $('#valorNumerodeTransaccion').val(),
            sustentoGastoCaja: $('#valorSustentodeGasto').val(),
            ingresoCaja: $('#valorCantidadIngreso').val(),
            salidaCaja: $('#valorCantidadSalida').val(),
            linkCaja: $('#valorLinkPagoCajaChica').val()
        };

        $.ajax({
            url: `fn_consulta_AgregarDetalleEgresoEditar`,
            method: 'GET',
            data: data,
            success: function(response) {
                if (response.success) {
                    show_alerta('Registro actualizado correctamente.', 'success');
                    $('#filtrar_pagos_submit').trigger('click');
                    $('#ModalEditarPagoCajaChica').addClass('hidden');
                    $('#ModalEditarPagoCajaChica').removeClass('flex');
                }
            },
            error: function(error) {
                show_alerta('Ocurrió un error al actualizar.', 'error');
                console.error("ERROR",error);
            }
        });
    });

    $(document).on("click", "#registrar_prestamo_de_jabas", function() {
        $('#ModalEditarPagoCajaChica').addClass('flex');
        $('#ModalEditarPagoCajaChica').removeClass('hidden');
    });
    
    $('.cerrarModalEditarPagoCajaChica, #registrar_prestamo_de_jabas .opacity-75').on('click', function (e) {
        $('#ModalEditarPagoCajaChica').addClass('hidden');
        $('#ModalEditarPagoCajaChica').removeClass('flex');
    });

    tablaEditable();
    fn_TraerPagosFechas2(fechaHoy, fechaHoy);

    $(document).on('input', '.validarFormatoFechaTablas', function () {
        copiarDatosPenultimaFila();
        let inputValue = $(this).text();
        let regex = /^\d{2}-\d{2}-\d{4}$/; // Expresión regular para formato dd-mm-yyyy
        
        // Verificar si el valor cumple con el formato de fecha DD-MM-YYYY
        if (regex.test(inputValue)) {
            // Convertir el formato dd-mm-yyyy a yyyy-mm-dd
            let partesFecha = inputValue.split('-');
            let inputDate = new Date(`${partesFecha[2]}-${partesFecha[1]}-${partesFecha[0]}`);
            let currentDate = new Date();
            
            // Comparar con la fecha actual (solo la fecha, sin la hora)
            if (inputDate <= currentDate.setHours(0,0,0,0)) {
                $(this).css('background-color', 'rgb(22 163 74)');
            } else {
                $(this).css('background-color', 'rgb(185 28 28)');
            }
        } else {
            $(this).css('background-color', 'rgb(185 28 28)');
        }
    });

    $(document).on('input', '.validarFormatoHoraTablas', function () {
        let inputValue = $(this).text();
        let regex = /^(?:2[0-3]|[01][0-9]):(?:[0-5][0-9]):(?:[0-5][0-9])$/;
    
        // Verificar si el valor cumple con el formato de hora HH:MM:SS
        if (regex.test(inputValue)) {
            $(this).css('background-color', 'rgb(22 163 74)');
        } else {
            $(this).css('background-color', 'rgb(185 28 28)');
        }
    });

    $(document).off('click', '#registrar_agregarPagos_CajaChica').on('click', '#registrar_agregarPagos_CajaChica', function () {
        let completedRequests = 0;
        let failedRequests = 0;
        let totalRequests = $('.pagosAgregarCajaChica:not(:last-child)').length;

        if (totalRequests === 0) {
            $("#registrar_agregarPagos_CajaChica").prop('disabled', false);
            return;
        }

        // Deshabilitar el botón para evitar múltiples envíos
        $('#registrar_agregarPagos_CajaChica').prop('disabled', true);

        function checkCompletion() {
            if (completedRequests + failedRequests === totalRequests) {
                if (failedRequests > 0) {
                    show_alerta(`Se registraron ${completedRequests} pagos correctamente y ${failedRequests} fallaron.`, 'warning');
                } else {
                    show_alerta('Los pagos se han registrado correctamente.', 'success');
                }
                let fechaDesde = convertirFechaAFormatoISO(fechaDesdeCajaChica.value);
                let fechaHasta = convertirFechaAFormatoISO(fechaHastaCajaChica.value);
                fn_TraerPagosFechas2(fechaDesde, fechaHasta);

                // Rehabilitar el botón después de completar las solicitudes
                $('#registrar_agregarPagos_CajaChica').prop('disabled', false);
            }
        }

        $('.pagosAgregarCajaChica:not(:last-child)').each(function () {
            let filaActual = $(this);

            let fechaAgregarPagoCaja = filaActual.find('td:eq(0)').text().trim().split('-').reverse().join('-');
            let horaAgregarPagoCaja = filaActual.find('td:eq(1)').text().trim();
            let acreedorCaja = filaActual.find('td:eq(2)').text().trim();
            let servicioCaja = filaActual.find('td:eq(3)').text().trim();
            let descripcionCaja = filaActual.find('td:eq(4)').text().trim();
            let razonSocialCaja = filaActual.find('td:eq(5)').text().trim();
            let sustentoCaja = filaActual.find('td:eq(6)').text().trim();
            let nTransaccionCaja = filaActual.find('td:eq(7)').text().trim();
            let sustentoGastoCaja = filaActual.find('td:eq(8) select').val().trim();
            let ingresoCaja = filaActual.find('td:eq(9)').text().trim();
            let salidaCaja = filaActual.find('td:eq(10)').text().trim();
            let linkCaja = filaActual.find('td:eq(11)').text().trim();

            $.ajax({
                url: '/fn_registrarDatosCajaChica',
                method: 'GET',
                data: {
                    fechaAgregarPagoCaja,
                    horaAgregarPagoCaja,
                    acreedorCaja,
                    servicioCaja,
                    descripcionCaja,
                    razonSocialCaja,
                    sustentoCaja,
                    nTransaccionCaja,
                    sustentoGastoCaja,
                    ingresoCaja,
                    salidaCaja,
                    linkCaja,
                },
                success: function (response) {
                    if (response.success) {
                        show_alerta('Los pagos de la caja chica se han registrado correctamente.', 'success');
                        filaActual.remove();
                        $('#filtrar_pagos_submit').trigger('click');
                    }
                    completedRequests++;
                    checkCompletion();
                },
                error: function (error) {
                    console.error("ERROR AL REGISTRAR LOS PAGOS DE LA CAJA ", error);
                    failedRequests++;
                    checkCompletion();
                }
            });
        });
    });

    function convertirFechaAFormatoISO(fecha) {
        if (!fecha) return '';

        if (fecha instanceof Date) {
            return fecha.toISOString().split('T')[0];
        }

        if (typeof fecha === 'string' && fecha.includes('-')) {
            let partes = fecha.split('-');
            if (partes.length !== 3) return '';
            return `${partes[2]}-${partes[1]}-${partes[0]}`;
        }

        return '';
    }

    $('#filtrar_pagos_submit').on('click', function () {
        let fechaDesde = convertirFechaAFormatoISO(fechaDesdeCajaChica.value);
        let fechaHasta = convertirFechaAFormatoISO(fechaHastaCajaChica.value);
        fn_TraerPagosFechas2(fechaDesde, fechaHasta);
    });


    function fn_TraerPagosFechas2(fechaDesdeCajaChica, fechaHastaCajaChica) {
        $.ajax({
            url: '/fn_obtenerDatosCajaChica',
            method: 'GET',
            data: {
                fechaDesde: fechaDesdeCajaChica,
                fechaHasta: fechaHastaCajaChica,
            },
            success: function(response) {
                // Verificar si la respuesta es un arreglo de objetos
                if (Array.isArray(response)) {
                    // Obtener el tbody
                    let tbodyReporteDePagos = $('#bodyReporteDePagosCajaChicaIngreso');
                    tbodyReporteDePagos.empty();

                    let totalIngreso = 0;
                    let totalEgreso = 0;

                    // Iterar sobre los objetos y mostrar sus propiedades
                    response.forEach(function(obj) {
                        let nuevaFila = $('<tr class="bg-white editarPagos border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer">');
                        totalIngreso += parseFloat(obj.ingresoCaja) || 0;
                        totalEgreso += parseFloat(obj.salidaCaja) || 0;

                        nuevaFila.append($('<td class="hidden">').text(obj.idCaja));
                        nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap dark:text-gray-50 text-gray-700 font-bold hidden">').text(obj.idCaja));
                        nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap dark:text-gray-50 text-gray-700 font-bold">').text(obj.fechaCaja));
                        nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap dark:text-gray-50 text-gray-700 font-bold">').text(obj.horaCaja));
                        nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap dark:text-gray-200 text-gray-700 font-bold">').text(obj.acreedorCaja));
                        nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap dark:text-gray-200 text-gray-700 font-normal">').text(obj.servicioCaja));
                        nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap dark:text-gray-200 text-gray-700 font-normal">').text(obj.descripcionCaja));
                        nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap dark:text-gray-200 text-gray-700 font-normal">').text(obj.razonSocialCaja));
                        nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap dark:text-gray-200 text-gray-700 font-normal">').text(obj.sustentoCaja));
                        nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap dark:text-gray-200 text-gray-700 font-normal">').text(obj.nTransaccionCaja));
                        nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap dark:text-gray-200 text-gray-700 font-normal">').text(obj.sustentoGastoCaja));
                        nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap dark:text-gray-50 text-gray-900 font-extrabold">').text(obj.ingresoCaja == null ? "0" : parseFloat(obj.ingresoCaja).toFixed(2)));
                        nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap dark:text-gray-50 text-gray-900 font-extrabold">').text(obj.salidaCaja == null ? "0" : parseFloat(obj.salidaCaja).toFixed(2)));
                        nuevaFila.append(
                            $('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap">').append(
                                obj.linkCaja
                                    ? $('<a>')
                                        .attr('href', obj.linkCaja)
                                        .attr('target', '_blank')
                                        .addClass('text-blue-600 dark:text-blue-500 hover:underline') // Color azul y subrayado al pasar el mouse
                                        .text('Ver Link')
                                    : $('<span>').addClass('dark:text-gray-400 text-gray-800').text('S/N') // Texto en gris oscuro si es null
                            )
                        );
                        nuevaFila.append($('<td class="border-r dark:border-gray-700 p-2 text-center cursor-pointer whitespace-nowrap dark:text-gray-200 text-gray-700 font-normal hidden">').text(obj.linkCaja));

                        tbodyReporteDePagos.append(nuevaFila);
                    });

                    // Agregar fila de totales
                    let filaTotales = $('<tr class="bg-gray-200 font-bold dark:bg-gray-700">');
                    filaTotales.append($('<td colspan="9" class="border-r dark:border-gray-700 p-2 text-end dark:text-gray-50 text-gray-900 font-extrabold">').text('TOTAL : '));
                    filaTotales.append($('<td class="border-r dark:border-gray-700 font-extrabold p-2 text-center text-green-600 dark:text-green-500">').text(totalIngreso.toFixed(2)));
                    filaTotales.append($('<td class="border-r dark:border-gray-700 font-extrabold p-2 text-center text-red-600 dark:text-red-500">').text(totalEgreso.toFixed(2)));
                    filaTotales.append($('<td class="border-r dark:border-gray-700 font-extrabold p-2 text-center text-red-600 dark:text-red-500">').text(""));
                    filaTotales.append($('<td class="hidden">'));

                    tbodyReporteDePagos.append(filaTotales);

                    let resultadoTotalCaja = totalIngreso - totalEgreso;
                    $('#diferenciaDeCajaChica').text(resultadoTotalCaja.toFixed(2));

                } else {
                    console.log("La respuesta no es un arreglo de objetos.");
                }
            },
            error: function(error) {
                console.error("ERROR", error);
            }
        });
    }

    $(document).on('contextmenu', 'tr.editarPagos', function (e) {
        e.preventDefault();
        let codigoPago = $(this).find("td").eq(0).text().trim();

        show_confirmacion('¿Estás seguro?', '¿Estás seguro de eliminar definitivamente el pago de la caja chica?'
        ).then((result) => {
            if (result.isConfirmed) {
                fn_EliminarPago(codigoPago);
            }
        });
    });

    function fn_EliminarPago(codigoPago){
        $.ajax({
            url: '/fn_consulta_EliminarPago',
            method: 'GET',
            data: {
                codigoPago: codigoPago,
            },
            success: function(response) {
                if (response.success) {
                    show_alerta('Se elimino el pago correctamente.', 'success');
                    $('#filtrar_pagos_submit').trigger('click');
                }
            },
            error: function(error) {
                show_alerta('Error: Ocurrio un error inesperado durante la operacion', 'error');
                console.error("ERROR",error);
            }
        });
    }
    
});

</script>
<template>
    <AppLayout title="Caja Chica">
        <template #header>
            <h1 class="font-semibold text-base uppercase text-gray-800 leading-tight dark:text-white">Caja Chica</h1>
        </template>
 
        <div class="py-2 md:py-4 min-h-[calc(100vh-185px)] overflow-auto">
            <div class="h-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-gray-600 rounded-lg dark:bg-gray-800">
                    <div class="card text-sm -mt-4">
                        <div class="flex justify-start md:items-end gap-x-8 gap-y-4 flex-col md:flex-row flex-wrap md:m-5 mt-0 mb-5">
                            <div class="flex gap-x-14 gap-y-4 flex-col md:flex-row">
                                <div>
                                    <label for="fecha" class="font-bold block mb-2">Fecha Desde</label>
                                    <DatePicker v-model="fechaDesdeCajaChica" size="small" showIcon fluid :showOnFocus="false" dateFormat="dd/mm/yy"/>
                                </div>
                                <div>
                                    <label for="fecha" class="font-bold block mb-2">Fecha Hasta</label>
                                    <DatePicker v-model="fechaHastaCajaChica" showIcon fluid :showOnFocus="false" dateFormat="dd/mm/yy"/>
                                </div>
                            </div>
                            <button id="filtrar_pagos_submit" class="text-white md:w-56 h-[41px] bg-blue-600 hover:bg-blue-700 mt-0.5 py-2 px-4 lg:w-min w-full whitespace-nowrap text-center items-center flex justify-center gap-x-2 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg">
                                <i class='bx bx-search-alt text-lg' ></i> BUSCAR
                            </button>
                        </div>
                        <div class="md:m-5 mt-0">
                            <div class="relative overflow-x-auto rounded-lg w-full">
                                <div class="text-white font-extrabold bg-orange-500 flex justify-center gap-4 p-1.5">
                                    <div>Diferencia :</div>
                                    <div class="font-extrabold" id="diferenciaDeCajaChica"></div>
                                </div>
                                
                                <div class="w-full overflow-x-auto">
                                    <table class="w-full min-w-max text-sm text-gray-500 dark:text-gray-400">
                                        <caption class="bg-green-600 p-2 border-b-2 text-sm font-bold text-gray-100">Caja Chica Ingreso</caption>
                                        <thead class="text-xs text-gray-100 uppercase bg-green-600">
                                            <tr>
                                                <th class="hidden">Id</th>
                                                <th class="p-4 border-r-2 border-b-2 text-center whitespace-nowrap">Fecha</th>
                                                <th class="p-4 border-r-2 border-b-2 text-center whitespace-nowrap">Hora</th>
                                                <th class="p-4 border-r-2 border-b-2 border-l-[1px] whitespace-nowrap">Acreedor</th>
                                                <th class="p-4 border-r-2 border-b-2 text-center">Servicio de los Asesores</th>
                                                <th class="p-4 border-r-2 border-b-2 text-center whitespace-nowrap">Descripción</th>
                                                <th class="p-4 border-r-[1px] border-b-2 text-center whitespace-nowrap">Razón Social</th>
                                                <th class="p-4 border-r-[1px] border-b-2 text-center">Sustento de Pago</th>
                                                <th class="p-4 border-r-[1px] border-b-2 text-center">N° de Transacción</th>
                                                <th class="p-4 border-r-[1px] border-b-2 text-center whitespace-nowrap">Sustento del Gasto</th>
                                                <th class="p-4 border-r-[1px] border-b-2 text-center whitespace-nowrap">Ingreso</th>
                                                <th class="p-4 border-r-[1px] border-b-2 text-center whitespace-nowrap">Salida</th>
                                                <th class="p-4 border-r-[1px] border-b-2 text-center whitespace-nowrap">Link</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyReporteDePagosCajaChicaIngreso"></tbody>
                                    </table>
                                </div>

                                <div class="w-full overflow-x-auto mt-4">
                                    <table class="w-full min-w-max text-sm text-gray-500 dark:text-gray-400">
                                        <thead class="w-full text-xs text-gray-100 uppercase bg-green-600">
                                            <tr class="border-2 border-l-[1px] border-r-[1px]">
                                                <th class="p-4 whitespace-nowrap text-center" colspan="11">FILAS A AGREGAR</th>
                                                <th class="p-4 whitespace-nowrap text-center">
                                                    <button class="w-full flex gap-2 justify-center items-center cursor-pointer uppercase bg-green-700 text-white hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5" id="registrar_agregarPagos_CajaChica">
                                                        <i class='bx bx-save text-lg'></i>Guardar Pagos
                                                    </button>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th class="hidden">Id</th>
                                                <th class="p-4 border-r-2 border-b-2 text-center whitespace-nowrap">Fecha</th>
                                                <th class="p-4 border-r-2 border-b-2 text-center whitespace-nowrap">Hora</th>
                                                <th class="p-4 border-l-[1px] border-r-2 border-b-2 whitespace-nowrap">Acreedor</th>
                                                <th class="p-4 border-r-2 border-b-2 text-center">Servicio de los Asesores</th>
                                                <th class="p-4 border-r-2 border-b-2 text-center whitespace-nowrap">Descripción</th>
                                                <th class="p-4 border-r-2 border-b-2 text-center whitespace-nowrap">Razón Social</th>
                                                <th class="p-4 border-r-[1px] border-b-2 text-center">Sustento de Pago</th>
                                                <th class="p-4 border-r-[1px] border-b-2 text-center">N° de Transacción</th>
                                                <th class="p-4 border-r-[1px] border-b-2 text-center">Sustento del Gasto</th>
                                                <th class="p-4 border-r-[1px] border-b-2 text-center">Ingreso</th>
                                                <th class="p-4 border-r-[1px] border-b-2 text-center">Salida</th>
                                                <th class="p-4 border-r-[1px] border-b-2 text-center">Link</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyReporteDeCajaChica"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="fixed inset-0 overflow-hidden z-[100] hidden" id="ModalEditarPagoCajaChica">
                <div class="flex justify-center items-center w-full min-h-full h-screen py-4 px-4 text-center pt-32">
                    <!-- Fondo oscuro overlay -->
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
                    </div>
            
                    <!-- Contenido del modal -->
                    <div class="rounded-lg max-h-[100%] aside_scrollED overflow-y-auto bg-white dark:bg-gray-700 text-left shadow-xl transform transition-all max-w-lg w-full">
                        <div class="p-4">
                            <div class="flex flex-col">
                                <div class="border-b rounded-t dark:border-gray-500 p-2 flex justify-center">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Editar Datos de Caja Chica</h3>
                                </div>
                                <label id="idCaja" class="hidden mb-2 text-base font-medium text-gray-900 dark:text-white"></label>
                                <div class="mt-4 flex justify-center items-center h-10">
                                    <div class="text-sm px-3 flex h-full items-center justify-center text-center border border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-gray-600 rounded-l-lg">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-300 min-w-max">Fecha</h4>
                                    </div>
                                    <input class="h-10 w-full uppercase outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-primary-600 focus:border-primary-600 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 rounded-r-lg" type="date" name="fechaPrestamodeJabasAuto" placeholder="0.00" autocomplete="off" id="fechaPrestamodeJabasAuto" value="">
                                </div>
                                <div class="mt-4 flex justify-center items-center h-10">
                                    <div class="text-sm px-3 flex h-full items-center justify-center text-center border border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-gray-600 rounded-l-lg">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-300 min-w-max">Hora</h4>
                                    </div>
                                    <input type="time" class="outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-r-lg focus:ring-primary-600 focus:border-primary-600 px-2.5 py-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 w-full" id="horaAgregarEditar">
                                </div>
                                <div class="mt-4 flex justify-center items-center h-10">
                                    <div class="text-sm px-3 flex h-full items-center justify-center text-center border border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-gray-600 rounded-l-lg">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-300 min-w-max">Acreedor</h4>
                                    </div>
                                    <input class="h-10 w-full uppercase outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-primary-600 focus:border-primary-600 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 rounded-r-lg" type="text" name="valorAcreedor" placeholder="Ingrese Nombre de Acreedor" autocomplete="off" id="valorAcreedor" value="">
                                </div>
                                <div class="mt-4 flex justify-center items-center h-10">
                                    <div class="text-sm px-3 flex h-full items-center justify-center text-center border border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-gray-600 rounded-l-lg">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-300 min-w-max">Servicio de Asesor</h4>
                                    </div>
                                    <input class="h-10 w-full uppercase outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-primary-600 focus:border-primary-600 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 rounded-r-lg" type="text" name="valorCantidadPrestamodeJabasAuto" placeholder="Ingrese Servicio" autocomplete="off" id="valorCantidadPrestamodeJabasAuto" value="">
                                </div>
                                <div class="mt-4 flex justify-center items-center h-10">
                                    <div class="text-sm px-3 flex h-full items-center justify-center text-center border border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-gray-600 rounded-l-lg">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-300 min-w-max">Razón Social</h4>
                                    </div>
                                    <input class="h-10 w-full uppercase outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-primary-600 focus:border-primary-600 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 rounded-r-lg" type="text" name="valorRazonSocial" placeholder="Ingrese Razón Social" autocomplete="off" id="valorRazonSocial" value="">
                                </div>
                                <div class="mt-4 flex justify-center items-center h-10">
                                    <div class="text-sm px-3 flex h-full items-center justify-center text-center border border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-gray-600 rounded-l-lg">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-300 min-w-max">Sustento de Pago</h4>
                                    </div>
                                    <input class="h-10 w-full uppercase outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-primary-600 focus:border-primary-600 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 rounded-r-lg" type="text" name="valorSustentodePago" placeholder="Ingrese Sustento de pago" autocomplete="off" id="valorSustentodePago" value="">
                                </div>
                                <div class="mt-4 flex justify-center items-center h-10">
                                    <div class="text-sm px-3 flex h-full items-center justify-center text-center border border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-gray-600 rounded-l-lg">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-300 min-w-max">N° de Transaccion</h4>
                                    </div>
                                    <input class="h-10 w-full uppercase outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-primary-600 focus:border-primary-600 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 rounded-r-lg" type="text" name="valorNumerodeTransaccion" placeholder="0" autocomplete="off" id="valorNumerodeTransaccion" value="">
                                </div>
                                <div class="mt-4 flex justify-center items-center h-10">
                                    <div class="text-sm px-3 flex h-full items-center justify-center text-center border border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-gray-600 rounded-l-lg">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-300 min-w-max">Sustento de Gasto</h4>
                                    </div>
                                    <select id="valorSustentodeGasto" class="w-full uppercase outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-r-lg focus:ring-primary-600 focus:border-primary-600 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option value="FACTURA">FACTURA</option>
                                        <option value="BOLETA">BOLETA</option>
                                        <option value="OTROS">OTROS</option>
                                        <option value="PENDIENTE">PENDIENTE</option>
                                    </select>
                                </div>
                                <div class="flex flex-row gap-x-6">
                                    <div class="mt-4 flex justify-center items-center h-10">
                                        <div class="text-sm px-3 flex h-full items-center justify-center text-center border border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-gray-600 rounded-l-lg">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-300 min-w-max">Ingreso</h4>
                                        </div>
                                        <input class="h-10 w-full uppercase outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-primary-600 focus:border-primary-600 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 rounded-r-lg" type="text" name="valorCantidadIngreso" placeholder="0" autocomplete="off" id="valorCantidadIngreso" value="">
                                    </div>
                                    <div class="mt-4 flex justify-center items-center h-10">
                                        <div class="text-sm px-3 flex h-full items-center justify-center text-center border border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-gray-600 rounded-l-lg">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-300 min-w-max">Salida</h4>
                                        </div>
                                        <input class="h-10 w-full uppercase outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-primary-600 focus:border-primary-600 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 rounded-r-lg" type="text" name="valorCantidadSalida" placeholder="0" autocomplete="off" id="valorCantidadSalida" value="">
                                    </div>
                                </div>
                                <div class="mt-4 flex justify-center items-center h-10">
                                    <div class="text-sm px-3 flex h-full items-center justify-center text-center border border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-gray-600 rounded-l-lg">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-300 min-w-max">Link</h4>
                                    </div>
                                    <input class="h-10 w-full uppercase outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm focus:ring-primary-600 focus:border-primary-600 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 rounded-r-lg" type="text" name="valorLinkPagoCajaChica" placeholder="Ingrese el link" autocomplete="off" id="valorLinkPagoCajaChica" value="">
                                </div>
                                <div class="flex flex-col w-full pt-2">
                                    <label for="comentarioAgregarPago" class="mb-2 text-sm font-medium text-gray-900 dark:text-white md:w-24">Comentario :</label>
                                    <textarea class="w-full outline-none bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="text" name="comentarioAgregarPagoEditar" autocomplete="off" id="comentarioAgregarPagoEditar"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 pb-4">
                            <div class="border-t dark:border-gray-500 w-full sm:flex sm:flex-row-reverse pt-4">
                                <button type="button" class="flex w-full justify-center items-center gap-1 rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 sm:ml-3 sm:w-auto" id="btnRegistrarPagoCajaChica">Registrar</button>
                                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-red-500 hover:bg-red-600 px-3 py-2 text-sm font-semibold text-gray-100 sm:mt-0 sm:w-auto cerrarModalEditarPagoCajaChica">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>