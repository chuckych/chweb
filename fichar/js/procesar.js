$(function () {
    "use strict";
    onOpenSelect2()
    ActiveBTN(false, "#submit", '', 'Ingresar Fichadas')
    $(".FicharHorario").bind("submit", function (e) {
        e.preventDefault();
        CheckSesion()
        let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $.notifyClose();
                notify('Ingresando Fichadas', 'info', 0, 'right')
                ActiveBTN(true, "#submit", 'Aguarde <span class = "dotting mr-1"> </span> ' + loading, 'Ingresar Fichadas')
            },
            success: function (data) {
                if (data.status == "ok") {
                    $.notifyClose();
                    notify(data.Mensaje, 'success', 2000, 'right')
                    ActiveBTN(false, "#submit", 'Aguarde <span class = "dotting mr-1"> </span> ' + loading, 'Ingresar Fichadas')
                } else {
                    $.notifyClose();
                    notify(data.Mensaje, 'danger', 2000, 'right')
                    ActiveBTN(false, "#submit", 'Aguarde <span class = "dotting mr-1"> </span> ' + loading, 'Ingresar Fichadas')
                }
            }
        });
    });
});