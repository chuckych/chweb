/** Variables para las notificaciones de pantalla */
var NotifDelay     = 2000;
var NotifOffset    = 0;
var NotifOffsetX   = 0;
var NotifOffsetY   = 0;
var NotifZindex    = 9999;
var NotifMouseOver = 'pause'
var NotifEnter     = 'animate__animated animate__fadeInDown';
var NotifExit      = 'animate__animated animate__fadeOutUp';
var NotifAlign     = 'center';
$(document).ready(function () {
    $("#submit").html("Generar");
    $(".alta_liquidacion").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            // async : false,
            beforeSend: function (data) {
                $("#submit").prop("disabled", true);
                $("#respuestatext").html("Generando");
                $("#respuestatext").addClass("animate__animated animate__fadeIn");
                $("#respuesta").addClass("alert-info");
                $("#respuesta").removeClass("d-none");
                $("#respuesta").removeClass("alert-success");
                $("#respuesta").removeClass("alert-danger");
                $(".archivo").removeClass("animate__animated animate__fadeIn");
            },
            success: function (data) {
                if (data.status == "ok") {
                    // $("#respuestatext").removeClass("animate__animated animate__fadeIn");
                    $("#respuestatext").html("");
                    $("#submit").prop("disabled", false);
                    $("#submit").html("Generar");
                    $("#respuesta").removeClass("alert-success");
                    $("#respuesta").removeClass("alert-danger");
                    $("#respuesta").removeClass("alert-info");
                    $("#respuesta").addClass("d-none");
                    $('input[type="checkbox"]').prop('checked', false)
                    $.notify(`<span class='fonth fw4'><span class="">${data.dato}</span></span>`, {
                        type: 'success',
                        z_index: NotifZindex,
                        delay: NotifDelay,
                        offset: NotifOffset,
                        mouse_over: NotifMouseOver,
                        placement: {
                            align: NotifAlign
                        },
                        animate: {
                            enter: NotifEnter,
                            exit: NotifExit
                        }
                    });                    
                } else {
                    $("#respuestatext").html("");
                    // $("#respuestatext").removeClass("animate__animated animate__fadeIn");
                    $("#submit").prop("disabled", false);
                    $("#submit").html("Generar");
                    $("#respuesta").removeClass("alert-success");
                    $("#respuesta").removeClass("alert-info");
                    $("#respuesta").removeClass("alert-danger");
                    $("#respuesta").addClass("d-none");
                    $.notify(`<span class='fonth fw4'><span class="">${data.dato}</span></span>`, {
                        type: 'danger',
                        z_index: NotifZindex,
                        delay: NotifDelay,
                        offset: NotifOffset,
                        mouse_over: NotifMouseOver,
                        placement: {
                            align: NotifAlign
                        },
                        animate: {
                            enter: NotifEnter,
                            exit: NotifExit
                        }
                    });
                }
            }
        });
        e.stopImmediatePropagation();
    });
});