/** Variables para las notificaciones de pantalla */
var NotifDelay = 2000;
var NotifOffset = 0;
var NotifOffsetX = 0;
var NotifOffsetY = 0;
var NotifZindex = 9999;
var NotifMouseOver = 'pause'
var NotifEnter = 'animate__animated animate__fadeInDown';
var NotifExit = 'animate__animated animate__fadeOutUp';
var NotifAlign = 'center';

$(document).on("click", "#marcar", function (e) {
    $('input[type="checkbox"]').prop('checked', true)
});
$(document).on("click", "#desmarcar", function (e) {
    $('input[type="checkbox"]').prop('checked', false)
});

$(document).on("click", "#open-modal", function (e) {
    // $('#ModalABM').modal('show');
    var NombreRol = $(this).attr('data');
    var RecidRol = $(this).attr('data1');
    var IdRol = $(this).attr('data2');
    var Cliente = $(this).attr('data3');

    $("#NombreRol").val(NombreRol);
    $("#RecidRol").val(RecidRol);
    $("#IdRol").val(IdRol);
    $("#Cliente").val(Cliente);

    $('.modal-title').html('Rol: ' + $("#NombreRol").val() + '.<br /><span class"">Cuenta: ' + $("#Cliente").val() + '</span>')
    function checked(data, selector) {
        (data == 1) ? $(selector).prop('checked', true) : $(selector).prop('checked', false);
    }
    /** GET ABMROL */
    function abmRol() {
        $(document).ready(function () {
            $.ajax({
                url: "GetAbmRol.php",
                type: 'POST',
                dataType: "json",
                cache: false,
                'data': {
                    RecidRol: $("#RecidRol").val()
                },
                beforeSend: function () {
                    $('#submitABM').prop('disabled', true);
                    $('#act_abm').val('false');
                },
                success: function (respuesta) {
                    $('#act_abm').val('true');
                    $('.modal-body').removeClass('d-none');
                    $('#submitABM').prop('disabled', false);
                    checked(respuesta.aFic, "#aFic");
                    checked(respuesta.mFic, "#mFic");
                    checked(respuesta.bFic, "#bFic");
                    checked(respuesta.aNov, "#aNov");
                    checked(respuesta.mNov, "#mNov");
                    checked(respuesta.bNov, "#bNov");
                    checked(respuesta.aHor, "#aHor");
                    checked(respuesta.mHor, "#mHor");
                    checked(respuesta.bHor, "#bHor");
                    checked(respuesta.aONov, "#aONov");
                    checked(respuesta.mONov, "#mONov");
                    checked(respuesta.bONov, "#bONov");
                    checked(respuesta.Proc, "#Proc");
                    checked(respuesta.aCit, "#aCit");
                    checked(respuesta.mCit, "#mCit");
                    checked(respuesta.bCit, "#bCit");
                },
                error: function () {
                    $('.modal-body').addClass('d-none')
                    $('#act_abm').val('false');
                }
            });
        });
    }
    abmRol()
    $(document).ready(function () {
        $(".form_abm_rol").bind("submit", function (e) {
            e.preventDefault();
            $.ajax({
                type: $(this).attr("method"),
                url: $(this).attr("action"),
                data: $(this).serialize(),
                // async : false,
                beforeSend: function (data) {
                    $('#submitABM').prop('disabled', true);
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $('#submitABM').prop('disabled', false);
                        $.notify(`<span class='fonth fw4'><span class="text-dark">${data.dato}</span></span>`, {
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
                        $('#submitABM').prop('disabled', false);
                        $.notify(`<span class='fonth fw4'><span data-icon='&#xe41a;' class='mr-2'></span><span class="text-dark">${data.dato}</span></span>`, {
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
                },
                error: function() {
                    $('#submitABM').prop('disabled', false);
                    $.notify(`<span class='fonth fw4'><span data-icon='&#xe41a;' class='mr-2'> Error</span></span>`, {
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
            });
            e.stopImmediatePropagation();
        });
    });
});