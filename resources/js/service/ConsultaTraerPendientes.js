import $ from 'jquery';

export const ConsultaTraerPendientes = {
    obtenerIndexListadePendientes(productoId) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/fn_obtenerIndexListadePendientes',
                method: 'GET',
                data: { producto_id: productoId },
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

