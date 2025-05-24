import $ from 'jquery';

export const ConsultaDatosRelacionadosClientes = {
    obtenerDatosClientesRelacionados(cliente_id) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/fn_obtenerDatosClientesRelacionados',
                method: 'GET',
                data: { cliente_id: cliente_id },
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
