import $ from 'jquery';

export const ConsultaGenerarActadeConformidad = {
    obtenerListadeActaClientesPdf(id) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/fn_obtenerListadeActaClientesPdf',
                method: 'GET',
                data: { id: id },
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
