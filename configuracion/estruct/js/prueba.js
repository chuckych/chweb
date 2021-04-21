$("#FormPerson").bind("submit", function (event) {
    event.preventDefault();
    $.ajax({
        type: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),

        beforeSend: function (data) {
            ActiveBTN(true, '.submit', 'Aguarde..', 'Aceptar')
        },

        success: function (data) {
            if (data.status == "ok") {
                notify(data.Mensaje, 'success', 2000, 'center')
            } else {
                ActiveBTN(false, '.submit', 'Aguarde..', 'Aceptar')
                notify(data.Mensaje, 'danger', 2000, 'center')
            }
        },
        error: function (data) {
            ActiveBTN(false, '.submit', 'Aguarde..', 'Aceptar')
            notify('Error', 'danger', 2000, 'center')
        }
    });
    event.stopImmediatePropagation();
});