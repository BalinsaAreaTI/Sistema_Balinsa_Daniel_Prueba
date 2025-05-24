import $ from 'jquery';

export const ConsultaAsignarPermisos = {
    obtenerPermisosdeRoles(roleId) {  // Acepta roleId como parámetro
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `/fn_obtenerPermisosdeRoles/${roleId}`,  // Usa el roleId pasado
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
