$(document).ready(function () {
    $("#submit").html("Procesar");
    $(".procesando").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            // async : false,
            beforeSend: function (data) {
                $("#submit").prop("disabled", true);
                fadeInOnly('#respuesta')
                $("#respuesta").addClass("alert-info");
                $("#respuesta").removeClass("d-none");
                $("#respuesta").removeClass("alert-success");
                $("#respuesta").removeClass("alert-danger");
                $("#respuetatext").html('<div class="d-flex align-items-center mr-3">Procesando<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>');
            },
            success: function (data) {
                // console.log(data.status);
                if (data.status == "ok") {
                    fadeInOnly('#respuesta')
                    $("#respuetatext").html(`${data.dato}`);
                    $("#submit").prop("disabled", false);
                    $("#submit").html("Procesar");
                    $("#respuesta").addClass("alert-success");
                    $("#respuesta").removeClass("alert-danger");
                    $("#respuesta").removeClass("alert-info");
                } else {
                    $("#respuetatext").html(`${data.dato}`);
                    fadeInOnly('#respuesta')
                    $("#submit").prop("disabled", false);
                    $("#submit").html("Procesar");
                    $("#respuesta").removeClass("alert-success");
                    $("#respuesta").removeClass("alert-info");
                    $("#respuesta").addClass("alert-danger");
                }
            },
            error: function () {
                $("#respuetatext").html('Error');
                fadeInOnly('#respuesta')
                $("#submit").prop("disabled", false);
                $("#submit").html("Procesar");
                $("#respuesta").removeClass("alert-success");
                $("#respuesta").removeClass("alert-info");
                $("#respuesta").addClass("alert-danger");
            }

        });
        e.stopImmediatePropagation();
    });
});