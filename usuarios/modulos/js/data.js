$(document).ready(function () {
    function getModulos(tipo) {
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: "getTipoModulos.php",
            'data': {
                modulos: true,
                recidRol: getUrlParameter('_r'),
                tipo: tipo
            },
            beforeSend: function () {

            },
            success: function (data) {
                if (data.status == "ok") {
                    $.each(data.datos, function (key, value) {
                        // console.log(value);
                        $(".pills-" + value.tipo).append('<div class="col-sm-6 col-lg-4 col-xl-3 col-12"><div class="custom-control custom-checkbox"><input type="checkbox" value="' + value.id + '" name="amod[]" class="custom-control-input switch_' + value.tipo + '" id="m' + TrimEspacios(value.nombre) + 'm"><label class="custom-control-label text-dark fw4" for="m' + TrimEspacios(value.nombre) + 'm" style="padding-top: 3px;">' + value.nombre + '</label></div></div>')
                    });
                } else {
                }
            },
            error: function () {
            }
        });
    }
    function getModulosActivos() {
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: "getTipoModulos.php",
            'data': {
                activos: true,
                recidRol: getUrlParameter('_r'),
            },
            beforeSend: function () {

            },
            success: function (data) {
                if (data.status == "ok") {
                    $.each(data.datos, function (key, value) {
                        // console.log(value);
                        // if (key == '0') {
                        var array = value.mod_roles
                        // console.log(array);
                        // $.each(array, function (key, value) {
                        // console.log(value);
                        $("#m" + TrimEspacios(value.modulo) + "m").prop('checked', true);
                        // });
                        // }
                    });

                } else {
                }
                $("#v-pills-tabContent").removeClass('d-none')
                fadeInOnly("#v-pills-tabContent")
                setTimeout(() => {
                    $("#RowModulos").removeClass('d-none')
                    fadeInOnly("#RowModulos")
                }, 500);
            },
            error: function () {
            }
        });
    }
    function getTipoModulos() {
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: "getTipoModulos.php",
            'data': {
                tipo: true
            },
            beforeSend: function () {
                $('#v-pills-tab').append('<div id="espera" class="fontq text-secondary fw5">Obteniendo datos..</div>')
            },
            success: function (data) {
                if (data.status == "ok") {
                    $('#espera').remove();
                    $.each(data.datos, function (key, valorTipo) {

                        getModulos(valorTipo.id)
                        var cantMod = parseFloat(valorTipo.CantMod)
                        if (cantMod > 0) {
                            $('#v-pills-tab').append('<a class="nav-link fw4 fontq btn-outline-custom border mt-1" id="' + valorTipo.id + '-tab" data-toggle="pill" dataTipo="' + valorTipo.id + '" href="#v-pills-' + valorTipo.id + '" role="tab" aria-controls="v-pills-' + valorTipo.id + '" aria-selected="false">' + valorTipo.TipoModulo + '</a>')
                            $('#1-tab').addClass('active');
                            $('#v-pills-tabContent').append('<div class="tab-pane fade" id="v-pills-' + valorTipo.id + '" role="tabpanel"  aria-labelledby="v-pills-' + valorTipo.id + '-tab"><button class="fontq fw4 btn btn-sm btn-link border-0 text-gris" id="marcar_' + valorTipo.id + '">Marcar Todo</button><button class="fontq fw4 btn btn-sm btn-link border-0 ml-2 text-gris" id="desmarcar_' + valorTipo.id + '">Desmarcar</button><p></p><form action="crud_mod.php" method="POST" id="Form_' + valorTipo.id + '"><input type="hidden" name="TipoMod" value="' + valorTipo.id + '"><div class="row pills-' + valorTipo.id + '"></div><p></p><button type="submit" class="fontq fw4 btn btn-custom border px-3" id="Guardar_' + valorTipo.id + '">Guardar</button><span class="ml-2 fw5 fontq align-middle respuesta"></span></form></div>');
                            $('#v-pills-1').addClass('show active');
                            submitForms("#Form_" + valorTipo.id, "#Guardar_" + valorTipo.id)
                            $(document).on("click", "#marcar_" + valorTipo.id + "", function (e) {
                                $('.switch_' + valorTipo.id).prop('checked', true)
                                $('.switch_' + valorTipo.id).attr('name', 'amod[]');
                            });
                            $(document).on("click", "#desmarcar_" + valorTipo.id + "", function (e) {
                                $('.switch_' + valorTipo.id).prop('checked', false)
                                $('.switch_' + valorTipo.id).attr('name', 'amod[]');
                            });

                        }

                        if (cantMod == 0) {
                            $("#" + valorTipo.id + "-tab").addClass("disabled bg-light")
                        }
                        $('.nav-link').on("click", function (e) {
                            $(".respuesta").html('')
                        });
                    });
                    getModulosActivos()

                } else {
                }
            },
            error: function () {
            }
        });
    }
    getTipoModulos()
    function submitForms(idform, idbtnsubmit) {
        $(idform).bind("submit", function (e) {
            e.preventDefault();
            CheckSesion()
            $.ajax({
                type: $(this).attr("method"),
                url: $(this).attr("action"),
                data: $(this).serialize() +
                    "&recidRol= " + getUrlParameter('_r') +
                    "&IdRol= " + $("#IdRol").val(),
                dataType: "json",
                beforeSend: function (data) {
                    ActiveBTN(true, idbtnsubmit, 'Aguarde..', 'Guardar')
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        ActiveBTN(false, idbtnsubmit, 'Aguarde..', 'Guardar')
                        $(".respuesta").html(data.Mensaje)
                        setTimeout(() => {
                            $(".respuesta").html('')
                        }, 2000);
                        $.notifyClose();
                        notify(data.Mensaje, 'success', 5000, 'right')
                    } else {
                        ActiveBTN(false, idbtnsubmit, 'Aguarde..', 'Guardar')
                        $(".respuesta").html(data.Mensaje)
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                    }
                },
                error: function (data) {
                    ActiveBTN(false, idbtnsubmit, 'Aguarde..', 'Guardar')
                    $(".respuesta").html(data.Mensaje)
                    $.notifyClose();
                    notify(data.Mensaje, 'danger', 5000, 'right')
                }
            });
        });
    }
});