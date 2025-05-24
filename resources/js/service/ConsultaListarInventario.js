import $ from 'jquery';

export const ConsultaListarInventario = {
    obtenerIndexInventario(categoryId) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/fn_obtenerIndexInventario',
                method: 'GET',
                data: { category_id: categoryId },
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

