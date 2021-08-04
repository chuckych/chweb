$(document).ready(function () {
    onOpenSelect2()
    $("#submit").html("Ingresar Fichadas");
    $(".FicharHorario").bind("submit", function (e) {
        e.preventDefault();
        CheckSesion()
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            // async : false,
            beforeSend: function (data) {
                $("#submit").prop("disabled", true);
                $("#respuetatext").html("Ingresando Fichadas");
                $("#respuetatext").addClass("animate__animated animate__fadeIn");
                $("#respuesta").addClass("alert-info");
                $("#respuesta").removeClass("d-none");
                $("#respuesta").removeClass("alert-success");
                $("#respuesta").removeClass("alert-danger");
            },
            success: function (data) {
                if (data.status == "ok") {
                    $("#respuetatext").removeClass("animate__animated animate__fadeIn");
                    $("#respuetatext").html(`${data.dato}`);
                    $("#submit").prop("disabled", false);
                    $("#submit").html("Ingresar Fichadas");
                    $("#respuesta").addClass("alert-success");
                    $("#respuesta").removeClass("alert-danger");
                    $("#respuesta").removeClass("alert-info");
                } else {
                    $("#respuetatext").html(`${data.dato}`);
                    $("#respuetatext").removeClass("animate__animated animate__fadeIn");
                    $("#submit").prop("disabled", false);
                    $("#submit").html("Ingresar Fichadas");
                    $("#respuesta").removeClass("alert-success");
                    $("#respuesta").removeClass("alert-info");
                    $("#respuesta").addClass("alert-danger");
                }
            }
        });
        e.stopImmediatePropagation();
    });
});