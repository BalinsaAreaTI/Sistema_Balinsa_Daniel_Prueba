import $ from 'jquery';

export const ConsultaCajaChica = {
    obtenerDatosCajaChica(fechaDesde, fechaHasta) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/fn_obtenerDatosCajaChica',
                method: 'GET',
                data: { fechaDesde, fechaHasta }, // Enviamos las fechas
                dataType: 'json',
                success: function (data) {
                    resolve(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('Error en la consulta:', textStatus, errorThrown);
                    reject(errorThrown);
                }
            });
        });
    }
};