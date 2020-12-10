
$(document).on("click", "#marcar", function (e) {
    $('.abmcheck').prop('checked', true)
});
$(document).on("click", "#desmarcar", function (e) {
    $('.abmcheck').prop('checked', false)
});

    function checked(data, selector) {
        (data == 1) ? $(selector).prop('checked', true) : $(selector).prop('checked', false);
    }
    /** GET ABMROL */
    function abmRol() {
        $(document).ready(function () {
            $.ajax({
                url: "../roles/GetAbmRol.php",
                type: 'POST',
                dataType: "json",
                cache: false,
                'data': {
                    RecidRol: $("#RecidRol").val()
                },
                beforeSend: function () {
                    ActiveBTN(true, '#submitABM', 'Espere..', 'Guardar')
                    $('#act_abm').val('false');
                },
                success: function (respuesta) {
                    // console.log(respuesta);
                    $('#act_abm').val('true');
                    ActiveBTN(false, '#submitABM', 'Espere..', 'Guardar')
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
                    ActiveBTN(true, '#submitABM', 'Espere..', 'Guardar')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        ActiveBTN(false, '#submitABM', 'Espere..', 'Guardar')
                        $(".respuestaabm").html('Datos Guardados')
                        setTimeout(() => {
                            $(".respuestaabm").html('')
                        }, 2000);

                    } else {
                        $(".respuestaabm").html('Error')
                        ActiveBTN(false, '#submitABM', 'Espere..', 'Guardar')
                    }
                },
                error: function() {
                    $(".respuestaabm").html('Error')
                    ActiveBTN(false, '#submitABM', 'Espere..', 'Guardar')
                }
            });
            e.stopImmediatePropagation();
        });
    });