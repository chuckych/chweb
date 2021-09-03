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
                    checked(respuesta.aTur, "#aTur");
                    checked(respuesta.bTur, "#bTur");
                    checked(respuesta.mTur, "#mTur");
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
            CheckSesion()
            $.ajax({
                type: $(this).attr("method"),
                url: $(this).attr("action"),
                data: $(this).serialize(),
                // async : false,
                beforeSend: function (data) {
                    $('#submitABM').prop('disabled', true);
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $('#submitABM').prop('disabled', false);
                        $.notifyClose();
                        notify(data.Mensaje, 'success', 5000, 'right')
                    } else {
                        $('#submitABM').prop('disabled', false);
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                    }
                },
                error: function () {
                    $('#submitABM').prop('disabled', false);
                    $.notifyClose();
                    notify('Error', 'danger', 5000, 'right')
                }
            });
            e.stopImmediatePropagation();
        });
    });
});