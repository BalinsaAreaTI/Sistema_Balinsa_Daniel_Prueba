import $ from 'jquery';

export const ConsularDtProductosSalidas = {
    obtenerDatosConsultarProductosSalidas() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/fn_obtenerDatosConsultarProductosSalidas',
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