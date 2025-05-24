import $ from 'jquery';

export const ConsultaObtenerListadeTecnicosActa = {
    obtenerListadeTecnicosActa() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/fn_obtenerListadeTecnicosActa',
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