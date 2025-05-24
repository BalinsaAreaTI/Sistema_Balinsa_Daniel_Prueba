import $ from 'jquery';

export const ConsultaObtenerDatosparaPendientes = {
    obtenerDatosdeClientesparaPendientes() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/fn_obtenerDatosdeClientesparaPendientes',
                method: 'GET',
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