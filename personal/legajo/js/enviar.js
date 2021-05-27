$(document).ready(function () {
    ActiveBTN(false, "#btnGuardar", 'Aguarde..', 'Aceptar')
    $(".Update_Leg").bind("submit", function (e) {
        e.preventDefault();
        let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $.notifyClose();
                notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                $("#alerta_UpdateLega").addClass("d-none")
                ActiveBTN(true, "#btnGuardar", 'Aguarde..', 'Aceptar')

            },
            success: function (data) {
                // console.log(data.status);
                if (data.status == 'ok') {
                    $.notifyClose();
                    ActiveBTN(false, "#btnGuardar", 'Aguarde..', 'Aceptar')
                    var dt = new Date();
                    var Minutos = ("0" + dt.getMinutes()).substr(-2);
                    var Segundos = ("0" + dt.getSeconds()).substr(-2);
                    var Horas = ("0" + dt.getHours()).substr(-2);
                    var HoraActual = Horas + ":" + Minutos + ":" + Segundos + "Hs.";
                    $("#alerta_UpdateLega").removeClass("d-none").removeClass("d-none").removeClass("text-danger").addClass("text-success")
                    // $(".respuesta_UpdateLega").html(`Datos Guardados.! ${HoraActual}`)
                    $(".mensaje_UpdateLega").html('');
                    $("#Encabezado").html(`Legajo: ${data.Lega} &#8250 ${data.Nombre}`);
                    $("#LegDocu").val(`${data.docu}`)
                    
                    notify(`Datos Guardados.<br /><span class="fw5">Legajo: ${data.Lega} <br>Nombre:  ${data.Nombre}</span>`, 'success', 5000, 'right')

                } else {
                    $.notifyClose();
                    ActiveBTN(false, "#btnGuardar", 'Aguarde..', 'Aceptar')
                    $("#alerta_UpdateLega").removeClass("d-none").removeClass("text-success").addClass("text-danger")
                    $(".respuesta_UpdateLega").html("¡Error!")
                    $(".mensaje_UpdateLega").html(`Mensaje: ${data.dato}`);
                    notify(`Error: ${data.dato}`, 'success', 3000, 'right')
                }
            }
        });
    });
});

$(document).on('shown.bs.modal', '#altaEmpresa', function () {
    $(this).find('[autofocus]').focus();
});
$(document).on('shown.bs.modal', '#altaPlanta', function () {
    $(this).find('[autofocus]').focus();
});
$(document).on('shown.bs.modal', '#altaconvenio', function () {
    $(this).find('[autofocus]').focus();
});
$(document).on('shown.bs.modal', '#altaSector', function () {
    $(this).find('[autofocus]').focus();
});
$(document).on('shown.bs.modal', '#altaseccion', function () {
    $(this).find('[autofocus]').focus();
});
$(document).on('shown.bs.modal', '#altaGrupo', function () {
    $(this).find('[autofocus]').focus();
});
$(document).on('shown.bs.modal', '#altasucur', function () {
    $(this).find('[autofocus]').focus();
});
$(document).on('shown.bs.modal', '#altatarea', function () {
    $(this).find('[autofocus]').focus();
});
$(document).on('shown.bs.modal', '#altaProvincia', function () {
    $(this).find('[autofocus]').focus();
});
$(document).on('shown.bs.modal', '#altaLocalidad', function () {
    $(this).find('[autofocus]').focus();
});
$(document).on('shown.bs.modal', '#altahistorial', function () {
    $(this).find('[autofocus]').focus();
});
$(document).on('shown.bs.modal', '#altaidentifica', function () {
    $(this).find('[autofocus]').focus();
});
$(document).on('shown.bs.modal', '#altaidentifica', function () {
    $(this).find('[autofocus]').focus();
});
$(document).on('shown.bs.modal', '#altaNacion', function () {
    $(this).find('[autofocus]').focus();
});

var LegNume = $('#LegNume').val();

$(document).ready(function () {
    $("#Update_Leg").bind("submit", function (e) {
        e.preventDefault();
    });
});

/** NACIONES */
$(document).ready(function () {
    $(".Form_Nacion").bind("submit", function () {
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (respuesta) {
                $("#alerta").addClass("d-none");
                // $(".ocultar").addClass("d-none");
                $(".respuesta").html('');
                $(".mensaje").html("");
                $("#btnNacion").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">
                <span class="sr-only"></span>`);
                $("#btnNacion").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    // var dato = data.dato;
                    $("#alerta").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta").html("¡Bien hecho!");
                    $(".mensaje").html(`<br />Se guardó correctamente.<br/ >Cod: ${data.cod} <br />Desc: ${data.desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $(".Form_Nacion")[0].reset();
                    $('.selectjs_naciones').val(null).trigger('change');
                    var newOption = new Option(data.desc, data.cod, true, true);
                    $('.selectjs_naciones').append(newOption).trigger('change');
                    // $('#altaNacion').modal('hide')
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaNacion').on('hidden.bs.modal', function (e) {
                        $("#alerta").addClass("d-none");
                    })
                    $("#btnNacion").html('Aceptar');
                    $("#btnNacion").prop('disabled', false);

                } else if (data.status == "duplicado") {
                    $("#alerta").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-warning");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta").html("¡Atención!");
                    $(".mensaje").html(`El dato ${data.desc} ya existe. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaNacion').on('hidden.bs.modal', function (e) {
                        $("#alerta").addClass("d-none");
                    })
                    $("#btnNacion").html('Aceptar');
                    $("#btnNacion").prop('disabled', false);

                } else if (data.status == "requerido") {
                    $("#alerta").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-danger");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta").html("¡Error!");
                    $(".mensaje").html(`Campo requerido.`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaNacion').on('hidden.bs.modal', function (e) {
                        $("#alerta").addClass("d-none");
                    })
                    $("#btnNacion").html('Aceptar');
                    $("#btnNacion").prop('disabled', false);

                } else {
                    $("#alerta").removeClass("d-none").removeClass("alert-danger").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta").html("Error!");
                    $(".mensaje").html("Error al enviar..");
                    $("#btnNacion").prop('disabled', false);
                }
            }
        });
        // return false;
    });
});
/** PROVINCIAS */
$(document).ready(function () {
    $(".form-provincias").bind("submit", function () {
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (respuesta) {
                $("#alerta_prov").addClass("d-none");
                $(".respuesta_prov").html('');
                $(".mensaje_prov").html("");
                $("#btnProv").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">
                <span class="sr-only"></span>`);
                $("#btnProv").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    $("#alerta_prov").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_prov").html("¡Bien hecho!");
                    $(".mensaje_prov").html(`<br />Se guardó correctamente.<br/ >Cod: ${data.cod} <br />Desc: ${data.desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $(".form-provincias")[0].reset();
                    $('.selectjs_provincias').val(null).trigger('change');
                    var newOption = new Option(data.desc, data.cod, true, true);
                    $('.selectjs_provincias').append(newOption).trigger('change');
                    // $('#altaProvincia').modal('hide')
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaProvincia').on('hidden.bs.modal', function (e) {
                        $("#alerta_prov").addClass("d-none");
                        $(".form-provincias")[0].reset();
                    })
                    $("#btnProv").html('Aceptar');
                    $("#btnProv").prop('disabled', false);

                } else if (data.status == "duplicado") {
                    $("#alerta_prov").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-warning");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_prov").html("¡Atención!");
                    $(".mensaje_prov").html(`El dato ${data.desc} ya existe. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaProvincia').on('hidden.bs.modal', function (e) {
                        $("#alerta_prov").addClass("d-none");
                        $(".form-provincias")[0].reset();
                    })
                    $("#btnProv").html('Aceptar');
                    $("#btnProv").prop('disabled', false);

                } else if (data.status == "requerido") {
                    $("#alerta_prov").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-danger");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_prov").html("¡Error!");
                    $(".mensaje_prov").html(`Campo requerido.`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaProvincia').on('hidden.bs.modal', function (e) {
                        $("#alerta_prov").addClass("d-none");
                    })
                    $("#btnProv").html('Aceptar');
                    $("#btnProv").prop('disabled', false);

                } else {
                    $("#alerta_prov").removeClass("d-none").removeClass("alert-danger").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_prov").html("Error!");
                    $(".mensaje_prov").html("Error al enviar..");
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaProvincia').on('hidden.bs.modal', function (e) {
                        $("#alerta_prov").addClass("d-none");
                    })
                    $("#btnProv").html('Aceptar');
                    $("#btnProv").prop('disabled', false);
                }
            }
        });
        // return false;
    });
});
/** LOCALIDADES */
$(document).ready(function () {

    $(".form-localidad").bind("submit", function () {
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_local").addClass("d-none");
                $(".respuesta_local").html('');
                $(".mensaje_local").html("");
                $("#btnLoca").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">
                <span class="sr-only"></span>`);
                $("#btnLoca").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    var dato = data.dato;
                    $("#alerta_local").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_local").html("¡Bien hecho!");
                    $(".mensaje_local").html(`<br />Se guardó correctamente.<br/ >Cod: ${data.cod} <br />Desc: ${data.desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $(".form-localidad")[0].reset();
                    $('.selectjs_localidad').val(null).trigger('change');
                    var newOption = new Option(data.desc, data.cod, true, true);
                    $('.selectjs_localidad').append(newOption).trigger('change');
                    // $('#altaLocalidad').modal('hide')
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaLocalidad').on('hidden.bs.modal', function (e) {
                        $("#alerta_local").addClass("d-none");
                        $(".form-localidad")[0].reset();
                    })
                    $("#btnLoca").html('Aceptar');
                    $("#btnLoca").prop('disabled', false);

                } else if (data.status == "duplicado") {
                    $("#alerta_local").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-warning");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_local").html("¡Atención!");
                    $(".mensaje_local").html(`El dato ${data.desc} ya existe. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaLocalidad').on('hidden.bs.modal', function (e) {
                        $("#alerta_local").addClass("d-none");
                        $(".form-localidad")[0].reset();
                    })
                    $("#btnLoca").html('Aceptar');
                    $("#btnLoca").prop('disabled', false);

                } else if (data.status == "requerido") {
                    $("#alerta_local").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-danger");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_local").html("¡Error!");
                    $(".mensaje_local").html(`Campo requerido.`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaLocalidad').on('hidden.bs.modal', function (e) {
                        $("#alerta_local").addClass("d-none");
                    })
                    $("#btnLoca").html('Aceptar');
                    $("#btnLoca").prop('disabled', false);

                } else {
                    $("#alerta_local").removeClass("d-none").removeClass("alert-danger").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_local").html("Error!");
                    $(".mensaje_local").html("Error al enviar..");
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaLocalidad').on('hidden.bs.modal', function (e) {
                        $("#alerta_local").addClass("d-none");
                    })
                    $("#btnLoca").html('Aceptar');
                    $("#btnLoca").prop('disabled', false);
                }
            }
        });
        // return false;
    });
});
/** EMPRESAS */
$(document).ready(function () {
    $(".form-empresas").bind("submit", function () {

        $('#altaEmpresa').on('hidden.bs.modal', function (e) {
            $("#alerta_empresa").addClass("d-none");
            $(".form-empresas")[0].reset();
        }),

            event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (respuesta) {
                $("#alerta_empresa").addClass("d-none");
                $(".respuesta_empresa").html('');
                $(".mensaje_empresa").html("");
                $("#btnEmpresa").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">
                <span class="sr-only"></span>`);
                $("#btnEmpresa").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    var dato = data.dato;
                    $("#alerta_empresa").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_empresa").html("¡Bien hecho!");
                    $(".mensaje_empresa").html(`<br />Se guardó correctamente.<br/ >Cod: ${data.cod} <br />Desc: ${data.desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $(".form-empresas")[0].reset();
                    $('.selectjs_empresas').val(null).trigger('change');
                    var newOption = new Option(data.desc, data.cod, true, true);
                    $('.selectjs_empresas').append(newOption).trigger('change');
                    $("#btnEmpresa").html(`Enviar`);
                    $("#btnEmpresa").prop('disabled', false);

                } else if (data.status == "duplicado") {
                    $("#alerta_empresa").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-warning");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_empresa").html("¡Atención!");
                    $(".mensaje_empresa").html(`El dato ${data.desc} ya existe. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnEmpresa").html(`Enviar`);
                    $("#btnEmpresa").prop('disabled', false);

                } else if (data.status == "requerido") {
                    $("#alerta_empresa").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").removeClass('alert-warning').addClass("alert-danger");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_empresa").html("¡Error!");
                    $(".mensaje_empresa").html(`Campo <strong>Razón Social</strong> requerido.`);
                    $("#btnEmpresa").html(`Enviar`);
                    $("#btnEmpresa").prop('disabled', false);

                } else {
                    $("#alerta_empresa").removeClass("d-none").removeClass("alert-danger").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_empresa").html("Error!");
                    $(".mensaje_empresa").html("Error al enviar..");
                    $("#btnEmpresa").prop('disabled', false);
                    $("#btnEmpresa").html(`Enviar`);
                }
            }
        });
        //return false;
    });
});
/** PLANTA */
$(document).ready(function () {
    $(".form-plantas").bind("submit", function () {
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_planta").addClass("d-none");
                $(".respuesta_planta").html('');
                $(".mensaje_planta").html("");
                $("#btnPlanta").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">
                <span class="sr-only"></span>`);
                $("#btnPlanta").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    var dato = data.dato;
                    $("#alerta_planta").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_planta").html("¡Bien hecho!");
                    $(".mensaje_planta").html(`<br />Se guardó correctamente.<br/ >Cod: ${data.cod} <br />Desc: ${data.desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $(".form-plantas")[0].reset();
                    $('.selectjs_plantas').val(null).trigger('change');
                    var newOption = new Option(data.desc, data.cod, true, true);
                    $('.selectjs_plantas').append(newOption).trigger('change');
                    // $('#altaplanta').modal('hide')
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaPlanta').on('hidden.bs.modal', function (e) {
                        $("#alerta_planta").addClass("d-none");
                        $(".form-plantas")[0].reset();
                    })
                    $("#btnPlanta").html('Aceptar');
                    $("#btnPlanta").prop('disabled', false);

                } else if (data.status == "duplicado") {
                    $("#alerta_planta").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-warning");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_planta").html("¡Atención!");
                    $(".mensaje_planta").html(`El dato ${data.desc} ya existe. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaPlanta').on('hidden.bs.modal', function (e) {
                        $("#alerta_planta").addClass("d-none");
                        $(".form-plantas")[0].reset();
                    })
                    $("#btnPlanta").html('Aceptar');
                    $("#btnPlanta").prop('disabled', false);

                } else if (data.status == "requerido") {
                    $("#alerta_planta").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-danger");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_planta").html("¡Error!");
                    $(".mensaje_planta").html(`Campo requerido.`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaPlanta').on('hidden.bs.modal', function (e) {
                        $("#alerta_planta").addClass("d-none");
                    })
                    $("#btnPlanta").html('Aceptar');
                    $("#btnPlanta").prop('disabled', false);

                } else {
                    $("#alerta_planta").removeClass("d-none").removeClass("alert-danger").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_planta").html("Error!");
                    $(".mensaje_planta").html("Error al enviar..");
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaPlanta').on('hidden.bs.modal', function (e) {
                        $("#alerta_planta").addClass("d-none");
                    })
                    $("#btnPlanta").html('Aceptar');
                    $("#btnPlanta").prop('disabled', false);
                }
            }
        });
        // return false;
    });
});
/** SECTORES */
$(document).ready(function () {
    $(".form-sector").bind("submit", function () {
        $('#altaSector').on('hidden.bs.modal', function (e) {
            $("#alerta_sector").addClass("d-none");
            $(".form-sector")[0].reset();
        })
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_sector").addClass("d-none");
                $(".respuesta_sector").html('');
                $(".mensaje_sector").html("");
                $("#btnSect").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">
                <span class="sr-only"></span>`);
                $("#btnSect").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    // var dato = data.dato;
                    $("#alerta_sector").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $(".respuesta_sector").html("¡Bien hecho!");
                    $(".mensaje_sector").html(`<br />Se guardó correctamente.<br/ >Cod: ${data.cod} <br />Desc: ${data.desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $(".form-sector")[0].reset();
                    $('.selectjs_sectores').val(null).trigger('change');
                    var newOption = new Option(data.desc, data.cod, true, true);
                    $('.selectjs_sectores').append(newOption).trigger('change');
                    $('.selectjs_secciones').val(null).trigger('change');

                    $("#btnSect").html('Aceptar');
                    $("#btnSect").prop('disabled', false);
                    $('#SecCodi').val(`${data.cod}`);
                    $("#select_seccion").removeClass("d-none");
                    var nombresector = data.desc;
                    $("#SectorHelpBlock").html('Sector: ' + nombresector);

                } else if (data.status == "duplicado") {
                    $("#alerta_sector").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-warning");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_sector").html("¡Atención!");
                    $(".mensaje_sector").html(`El dato ${data.desc} ya existe. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnSect").html('Aceptar');
                    $("#btnSect").prop('disabled', false);

                } else if (data.status == "requerido") {
                    $("#alerta_sector").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-danger");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_sector").html("¡Error!");
                    $(".mensaje_sector").html(`Campo requerido.`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaSector').on('hidden.bs.modal', function (e) {
                        $("#alerta_sector").addClass("d-none");
                    })
                    $("#btnSect").html('Aceptar');
                    $("#btnSect").prop('disabled', false);

                } else {
                    $("#alerta_sector").removeClass("d-none").removeClass("alert-danger").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_sector").html("Error!");
                    $(".mensaje_sector").html("Error al enviar..");
                    $("#btnSect").html('Aceptar');
                    $("#btnSect").prop('disabled', false);
                }
            }
        });
        // return false;
    });
});
/** SECCIONES */
$(document).ready(function () {
    /** Funcion para ocultar el alert al cerra el modal */
    $('#altaseccion').on('hidden.bs.modal', function (e) {
        $("#alerta_seccion").addClass("d-none");
        $("#Se2Desc").val(null);
        $("#btnSec2").html('Aceptar');
        $("#btnSec2").prop('disabled', false);
    })
    $(".form-seccion").bind("submit", function () {
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_seccion").addClass("d-none");
                $(".respuesta_seccion").html('');
                $(".mensaje_seccion").html("");
                $("#btnSec2").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">
                <span class="sr-only"></span>`);
                $("#btnSec2").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    var dato = data.dato;
                    $("#alerta_seccion").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_seccion").html("¡Bien hecho!");
                    $(".mensaje_seccion").html(`<br />Se guardó correctamente.<br/ >Cod: ${data.cod} <br />Desc: ${data.desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#Se2Desc").val(null);
                    $('.selectjs_secciones').val(null).trigger('change');
                    var newOption = new Option(data.desc, data.cod, true, true);
                    $('.selectjs_secciones').append(newOption).trigger('change');

                    $("#btnSec2").html('Aceptar');
                    $("#btnSec2").prop('disabled', false);

                } else if (data.status == "duplicado") {
                    $("#alerta_seccion").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-warning");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_seccion").html("¡Atención!");
                    $(".mensaje_seccion").html(`El dato ${data.desc} ya existe. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);

                    $("#btnSec2").html('Aceptar');
                    $("#btnSec2").prop('disabled', false);

                } else if (data.status == "requerido") {
                    $("#alerta_seccion").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-danger");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_seccion").html("¡Error!");
                    $(".mensaje_seccion").html(`Campo requerido.`);
                    $("#btnSec2").html('Aceptar');
                    $("#btnSec2").prop('disabled', false);

                } else {
                    $("#alerta_seccion").removeClass("d-none").removeClass("alert-danger").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_seccion").html("Error!");
                    $(".mensaje_seccion").html(`Error al enviar.. ${data.dato}`);
                    $("#btnSec2").html('Aceptar');
                    $("#btnSec2").prop('disabled', false);
                }
            }
        });
        // return false;
    });
});
/** GRUPOS */
$(document).ready(function () {
    $(".form-grupo").bind("submit", function () {
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_grupo").addClass("d-none");
                $(".respuesta_grupo").html('');
                $(".mensaje_grupo").html("");
                $("#btnGrup").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">
                <span class="sr-only"></span>`);
                $("#btnGrup").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    var dato = data.dato;
                    $("#alerta_grupo").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_grupo").html("¡Bien hecho!");
                    $(".mensaje_grupo").html(`<br />Se guardó correctamente.<br/ >Cod: ${data.cod} <br />Desc: ${data.desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $(".form-grupo")[0].reset();
                    $('.selectjs_grupos').val(null).trigger('change');
                    var newOption = new Option(data.desc, data.cod, true, true);
                    $('.selectjs_grupos').append(newOption).trigger('change');
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaGrupo').on('hidden.bs.modal', function (e) {
                        $("#alerta_grupo").addClass("d-none");
                        $(".form-grupo")[0].reset();
                    })
                    $("#btnGrup").html('Aceptar');
                    $("#btnGrup").prop('disabled', false);

                } else if (data.status == "duplicado") {
                    $("#alerta_grupo").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-warning");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_grupo").html("¡Atención!");
                    $(".mensaje_grupo").html(`El dato ${data.desc} ya existe. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaGrupo').on('hidden.bs.modal', function (e) {
                        $("#alerta_grupo").addClass("d-none");
                        $(".form-grupo")[0].reset();
                    })
                    $("#btnGrup").html('Aceptar');
                    $("#btnGrup").prop('disabled', false);

                } else if (data.status == "requerido") {
                    $("#alerta_grupo").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-danger");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_grupo").html("¡Error!");
                    $(".mensaje_grupo").html(`Campo requerido.`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaGrupo').on('hidden.bs.modal', function (e) {
                        $("#alerta_grupo").addClass("d-none");
                    })
                    $("#btnGrup").html('Aceptar');
                    $("#btnGrup").prop('disabled', false);

                } else {
                    $("#alerta_grupo").removeClass("d-none").removeClass("alert-danger").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_grupo").html("Error!");
                    $(".mensaje_grupo").html("Error al enviar..");
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altagrupo').on('hidden.bs.modal', function (e) {
                        $("#alerta_grupo").addClass("d-none");
                    })
                    $("#btnGrup").html('Aceptar');
                    $("#btnGrup").prop('disabled', false);
                }
            }
        });
        // return false;
    });
});
/** SUCURSALES */
$(document).ready(function () {
    $(".form-sucur").bind("submit", function () {
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_sucur").addClass("d-none");
                $(".respuesta_sucur").html('');
                $(".mensaje_sucur").html("");
                $("#btnSucur").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status"><span class="sr-only"></span>`);
                $("#btnSucur").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    var dato = data.dato;
                    $("#alerta_sucur").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_sucur").html("¡Bien hecho!");
                    $(".mensaje_sucur").html(`<br />Se guardó correctamente.<br/ >Cod: ${data.cod} <br />Desc: ${data.desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $(".form-sucur")[0].reset();
                    $('.selectjs_sucursal').val(null).trigger('change');
                    var newOption = new Option(data.desc, data.cod, true, true);
                    $('.selectjs_sucursal').append(newOption).trigger('change');
                    // $('#altasucur').modal('hide')
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altasucur').on('hidden.bs.modal', function (e) {
                        $("#alerta_sucur").addClass("d-none");
                        $(".form-sucur")[0].reset();
                    })
                    $("#btnSucur").html(`Aceptar`);
                    $("#btnSucur").prop('disabled', false);

                } else if (data.status == "duplicado") {
                    $("#alerta_sucur").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-warning");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_sucur").html("¡Atención!");
                    $(".mensaje_sucur").html(`El dato ${data.desc} ya existe. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altasucur').on('hidden.bs.modal', function (e) {
                        $("#alerta_sucur").addClass("d-none");
                        $(".form-sucur")[0].reset();
                    })
                    $("#btnSucur").html(`Aceptar`);
                    $("#btnSucur").prop('disabled', false);

                } else if (data.status == "requerido") {
                    $("#alerta_sucur").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-danger");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_sucur").html("¡Error!");
                    $(".mensaje_sucur").html(`Campo requerido.`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altasucur').on('hidden.bs.modal', function (e) {
                        $("#alerta_sucur").addClass("d-none");
                    })
                    $("#btnSucur").html(`Aceptar`);
                    $("#btnSucur").prop('disabled', false);

                } else {
                    $("#alerta_sucur").removeClass("d-none").removeClass("alert-danger").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_sucur").html("Error!");
                    $(".mensaje_sucur").html("Error al enviar..");
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altasucur').on('hidden.bs.modal', function (e) {
                        $("#alerta_sucur").addClass("d-none");
                    })
                    $("#btnSucur").html(`Aceptar`);
                    $("#btnSucur").prop('disabled', false);
                }
            }
        });
        // return false;
    });
});
/** TAREAS DE PRODUCCION */
$(document).ready(function () {
    $(".form-tarea").bind("submit", function () {
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_tarea").addClass("d-none");
                $(".respuesta_tarea").html('');
                $(".mensaje_tarea").html("");
                $("#btnTarea").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status"><span class="sr-only"></span>`);
                $("#btnTarea").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    var dato = data.dato;
                    $("#alerta_tarea").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_tarea").html("¡Bien hecho!");
                    $(".mensaje_tarea").html(`<br />Se guardó correctamente.<br/ >Cod: ${data.cod} <br />Desc: ${data.desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $(".form-tarea")[0].reset();
                    $('.selectjs_tarea').val(null).trigger('change');
                    var newOption = new Option(data.desc, data.cod, true, true);
                    $('.selectjs_tarea').append(newOption).trigger('change');
                    // $('#altatarea').modal('hide')
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altatarea').on('hidden.bs.modal', function (e) {
                        $("#alerta_tarea").addClass("d-none");
                        $(".form-tarea")[0].reset();
                    })
                    $("#btnTarea").html(`Aceptar`);
                    $("#btnTarea").prop('disabled', false);

                } else if (data.status == "duplicado") {
                    $("#alerta_tarea").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-warning");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_tarea").html("¡Atención!");
                    $(".mensaje_tarea").html(`El dato ${data.desc} ya existe. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altatarea').on('hidden.bs.modal', function (e) {
                        $("#alerta_tarea").addClass("d-none");
                        $(".form-tarea")[0].reset();
                    })
                    $("#btnTarea").html(`Aceptar`);
                    $("#btnTarea").prop('disabled', false);
                    // $('#altaLocalidad').modal('hide')

                } else if (data.status == "requerido") {
                    $("#alerta_tarea").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-danger");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_tarea").html("¡Error!");
                    $(".mensaje_tarea").html(`Campo requerido.`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altatarea').on('hidden.bs.modal', function (e) {
                        $("#alerta_tarea").addClass("d-none");
                    })
                    $("#btnTarea").html(`Aceptar`);
                    $("#btnTarea").prop('disabled', false);

                } else {
                    $("#alerta_tarea").removeClass("d-none").removeClass("alert-danger").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_tarea").html("Error!");
                    $(".mensaje_tarea").html("Error al enviar..");
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altatarea').on('hidden.bs.modal', function (e) {
                        $("#alerta_tarea").addClass("d-none");
                    })
                    $("#btnTarea").html(`Aceptar`);
                    $("#btnTarea").prop('disabled', false);
                }
            }
        });
        // return false;
    });
});
/** CONVENIOS */
$('#altaconvenio').on('hidden.bs.modal', function (e) {
    $("#alerta_convenio").addClass("d-none");
    $(".form-convenio")[0].reset();
    $(".form-diasvac")[0].reset();
    $('input[name = dato_conv]').val("alta_convenio");
    $('input[name = codConv]').val("");
    $('#ConvVaca').DataTable().clear().draw().destroy();
    $('#ConvFeri').DataTable().clear().draw().destroy();
    $("#rowConvVac").addClass("d-none"); /** Agregar la clase d-none */
    $("#ConvVacaTabla").addClass("d-none");
    $("#ConvFeriTabla").addClass("d-none");
})
$(document).ready(function () {
    $(".form-convenio").bind("submit", function () {
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_convenio").addClass("d-none");
                $(".mensaje_convenio").html("");
                $("#btnConv").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">
                    <span class="sr-only"></span>`);
                $("#btnConv").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {

                    var cod = data.cod;
                    var desc = data.desc;
                    var ConDias = data.ConDias;
                    var ConTDias = data.ConTDias;

                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $("#rowConvVac").removeClass("d-none"); /** remover la clase d-none */
                    $('#altaconvenio').modal('handleUpdate') /** Reajuste manualmente la posición del modal si la altura de un modal cambia mientras está abierto (es decir, en caso de que aparezca una barra de desplazamiento). */

                    $('input[name=codConv]').val(`${cod}`);
                    $('input[name=cod-diasvac]').val(`${cod}`).trigger('change');
                    $('input[name=CFConv]').val(`${cod}`).trigger('change');
                    $('input[name=dato_conv]').val("mod_convenio");


                    $('#ConDias').val(`${ConDias}`);
                    $('#ConTDias').val(`${ConTDias}`);

                    $(".respuesta_convenio").html("¡Bien hecho!");
                    $(".mensaje_convenio").html(`<br />Se creó correctamente.<br/ >Cod: ${cod} <br />Desc: ${desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);

                    $('.selectjs_convenio').val(null).trigger('change');
                    var newOption = new Option(desc, cod, true, true);
                    $('.selectjs_convenio').append(newOption).trigger('change');

                    $('#ConvVaca').DataTable({
                        deferRender: true,
                        "ajax": {
                            url: "../../data/getConvVaca.php",
                            type: "GET",
                            'data': {
                                q: cod,
                            },
                        },

                        columns: [{ "class": "align-middle text-center", "data": "CVAnios" }, { "class": "align-middle text-center", "data": "CVMeses" }, { "class": "align-middle text-center", "data": "CVDias" }, { "class": "align-middle text-center", "data": "eliminar" }, { "class": "w-100 align-middle", "data": "null" }],
                        paging: false,
                        scrollY: '40vh',
                        scrollX: true,
                        scrollCollapse: true,
                        searching: false,
                        info: true,
                        ordering: false,
                        language: {
                            "url": "../../js/DataTableSpanish.json"
                        },
                    });
                    $('#ConvFeri').DataTable({
                        deferRender: true,
                        "ajax": {
                            url: "../../data/getConvFeri.php",
                            type: "GET",
                            'data': {
                                q: cod,
                            },
                        },

                        columns: [
                            { "class": "align-middle ls1", "data": "CFFech" },
                            { "class": "align-middle", "data": "CFDesc" },
                            { "class": "align-middle text-center", "data": "CFInFeTR" },
                            { "class": "align-middle", "data": "CFCodM" },
                            { "class": "align-middle", "data": "CFCodJ" },
                            { "class": "align-middle text-center", "data": "CFInfM" },
                            { "class": "align-middle text-center", "data": "CFInfJ" },
                            { "class": "align-middle text-center", "data": "eliminar" },
                            { "class": "align-middle w-100", "data": "null" }
                        ],
                        paging: false,
                        // scrollY: '40vh',
                        // scrollX: false,
                        // scrollCollapse: true,
                        searching: false,
                        info: true,
                        ordering: false,
                        language: {
                            "url": "../../js/DataTableSpanish.json"
                        },
                    });
                    $("#btnConv").html('Aceptar');
                    $("#btnConv").prop('disabled', false);

                } else if (data.status == "okm") {
                    var cod = data.cod;
                    var desc = data.desc;
                    var ConDias = data.ConDias;
                    var ConTDias = data.ConTDias;
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $("#diasvac").removeClass("d-none");
                    $(".respuesta_convenio").html("¡Bien hecho!");
                    $(".mensaje_convenio").html(`<br />Se modificó correctamente.<br/ >Cod: ${cod} <br />Desc: ${desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnConv").html('Aceptar');
                    $("#btnConv").prop('disabled', false);

                } else if (data.status == "nomod") {
                    $("#alerta_convenio").addClass("d-none");
                    $("#btnConv").html('Aceptar');
                    $("#btnConv").prop('disabled', false);

                } else if (data.status == "duplicado") {
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-warning");
                    $(".respuesta_convenio").html("¡Atención!");
                    $(".mensaje_convenio").html(`El dato ${data.desc} ya existe. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnConv").html('Aceptar');
                    $("#btnConv").prop('disabled', false);

                } else if (data.status == "requerido") {
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_convenio").html("¡Error!");
                    $(".mensaje_convenio").html(`Campo requerido.`);
                    $("#btnConv").html('Aceptar');
                    $("#btnConv").prop('disabled', false);

                } else {
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_convenio").html("Error!");
                    $(".mensaje_convenio").html("Error al enviar..");
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaconvenio').on('hidden.bs.modal', function (e) {
                        $("#alerta_convenio").addClass("d-none");
                    })
                    $("#btnConv").html('Aceptar');
                    $("#btnConv").prop('disabled', false);
                }
            }
        });
        // return false;
    });
});
/** ALTA CONVENIO DIAS VACACIONES*/
$(document).ready(function () {
    $(".form-diasvac").bind("submit", function () {
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_convenio").addClass("d-none");
            },

            success: function (data) {
                if (data.status == "ok") {
                    $('#ConvVaca').DataTable().ajax.reload();
                    $("#ConvVacaTabla").removeClass("d-none");
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $(".respuesta_convenio").html("Se agregó correctamente.!");
                    $(".mensaje_convenio").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                } else if (data.status == "existe") {
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-warning");
                    $(".respuesta_convenio").html("¡Ya existe!");
                    $(".mensaje_convenio").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $('#ConvVaca').DataTable().ajax.reload();
                } else {
                    $('#ConvVaca').DataTable().ajax.reload();
                }
            }
        });
    });
});
/** BORARR DIAS VAC CONVENIO*/
$(document).on('click', '.delete_convVaca', function (e) {
    e.preventDefault();
    // var parent = $(this).parent().parent().attr('id');
    var del_cod = $(this).attr('data');
    var del_anios = $(this).attr('data2');
    var del_meses = $(this).attr('data3');
    var del_dias = $(this).attr('data4');
    var del_ConvVac = $(this).attr('data5');

    $.ajax({
        type: "POST",
        url: "alta_opciones.php",
        'data': {
            del_cod,
            del_anios,
            del_meses,
            del_dias,
            del_ConvVac
        },

        success: function (data) {
            if (data.status == "ok_delete") {
                $('#ConvVaca').DataTable().ajax.reload();
                $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-info");
                $(".respuesta_convenio").html("Se eliminó correctamente.!");
                $(".mensaje_convenio").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
            } else {
                $('#ConvVaca').DataTable().ajax.reload();
            }
        }
    });
});
/** ALTA CONVENIO FERIADOS*/
$(document).ready(function () {
    $(".form-fericonv").bind("submit", function () {
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_convenio").addClass("d-none");
            },
            success: function (data) {
                if (data.status == "ok") {
                    // $(".form-fericonv")[0].reset();
                    $("#CFFech").val('');
                    $("#CFDesc").val('');
                    // $("#CFInFeTR").checked="";
                    // $("#CFInFeTR").checked=false;
                    $("#CFCodM").val('');
                    $("#CFCodM3").val('');
                    $("#CFCodM2").val('');
                    // $("#CFInfM").val('');
                    // $("#CFInfJ").val('');
                    $("#CFCodJ").val('');
                    $("#CFCodJ3").val('');
                    $("#CFCodJ2").val('');

                    $("#ConvFeriTabla").removeClass("d-none");
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $(".respuesta_convenio").html("Se agregó correctamente.!");
                    $(".mensaje_convenio").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);

                    var cod = data.cod;

                    $('#ConvFeri').DataTable().ajax.reload();

                } else if (data.status == "existe") {
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-warning");
                    $(".respuesta_convenio").html("¡Ya existe!");
                    $(".mensaje_convenio").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $('#ConvFeri').DataTable().ajax.reload();
                } else if (data.status == "requeridos") {
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_convenio").html("Campos requeridos!");
                    $(".mensaje_convenio").html(`<br />Fecha - Descripción. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $('#ConvFeri').DataTable().ajax.reload();
                } else {
                    $('#ConvFeri').DataTable().ajax.reload();
                }
            }
        });
    });
});
/** BORARR FERIADOS CONVENIO*/
$(document).on('click', '.delete_convFeri', function (e) {
    e.preventDefault();
    // var parent = $(this).parent().parent().attr('id');
    var CFConv = $(this).attr('data');
    var CFDesc = $(this).attr('data2');
    var CFFech = $(this).attr('data3');
    var del_ConvFeri = $(this).attr('data4');

    $.ajax({
        type: "POST",
        url: "alta_opciones.php",
        'data': {
            CFConv,
            CFDesc,
            CFFech,
            del_ConvFeri,
        },

        success: function (data) {
            if (data.status == "ok_delete") {
                $('#ConvFeri').DataTable().ajax.reload();
                $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-info");
                $(".respuesta_convenio").html("Se eliminó correctamente.!");
                $(".mensaje_convenio").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
            } else {
                $('#ConvFeri').DataTable().ajax.reload();
            }
        }
    });
});

/** ALTA HISTORIAl INGRESOS LEGAJOS*/
$('#altahistorial').on('hidden.bs.modal', function (e) {
    $("#alerta_historial").addClass("d-none");
    $(".form-perineg")[0].reset();
    $("#btnHisto").html('Aceptar');
    $("#btnHisto").prop('disabled', false);
    $('#Perineg').DataTable().ajax.reload();
})
$(document).ready(function () {
    $(".form-perineg").bind("submit", function () {
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_historial").addClass("d-none");
                $("#btnHisto").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">
                    <span class="sr-only"></span>`);
                $("#btnHisto").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    // $("#CFFech").val('');
                    // $("#ConvFeriTabla").removeClass("d-none");
                    $("#alerta_historial").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $(".respuesta_historial").html("Se agregó correctamente.!");
                    $(".mensaje_historial").html(`<br />${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $(".form-perineg")[0].reset();
                    $("#btnHisto").html('Aceptar');
                    $("#btnHisto").prop('disabled', false);
                    $('#altahistorial').modal('hide');
                    $('#Perineg').DataTable().ajax.reload();

                } else if (data.status == "existe") {
                    $("#alerta_historial").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-warning");
                    $(".respuesta_historial").html("¡Ya existe!");
                    $(".mensaje_historial").html(`<br />${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnHisto").html('Aceptar');
                    $("#btnHisto").prop('disabled', false);
                    $('#Perineg').DataTable().ajax.reload();

                } else if (data.status == "requeridos") {
                    $("#alerta_historial").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_historial").html("¡Campos requeridos!");
                    $(".mensaje_historial").html(`<br />Ingreso - Egreso. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnHisto").html('Aceptar');
                    $("#btnHisto").prop('disabled', false);
                    $('#Perineg').DataTable().ajax.reload();

                } else {
                    $("#alerta_historial").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_historial").html("¡Error!");
                    $(".mensaje_historial").html(`<br />${data.dato}`);
                    $("#btnHisto").html('Aceptar');
                    $("#btnHisto").prop('disabled', false);
                    $('#Perineg').DataTable().ajax.reload();
                }
            }
        });
    });
});
/** BORARR HISTORIAl INGRESOS LEGAJOS*/
$(document).on('click', '.delete_perineg', function (e) {
    e.preventDefault();
    // var parent = $(this).parent().parent().attr('id');
    var DelInEgFeIn = $(this).attr('data');
    var DelInEgLega = $(this).attr('data2');
    var DelPerineg = $(this).attr('data3');

    $.ajax({
        type: "POST",
        url: "alta_opciones.php",
        'data': {
            DelInEgFeIn,
            DelInEgLega,
            DelPerineg
        },

        success: function (data) {
            if (data.status == "ok_delete") {
                $('#Perineg').DataTable().ajax.reload();
            } else {
                $('#Perineg').DataTable().ajax.reload();
            }
        }
    });
});
/** ALTA PERSONAL PREMIOS */
$(document).ready(function () {
    $('#altapremios').on('hidden.bs.modal', function (e) {
        $("#alerta_premios").addClass("d-none");
        $("#btnPremios").html('Aceptar');
        $("#btnPremios").prop('disabled', false);
        $('.selectjs_premios').val(null).trigger("change");
    })

    $(".form-premios").bind("submit", function () {
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_premios").addClass("d-none");
                $("#btnPremios").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">`);
                $("#btnPremios").prop('disabled', true);
            },

            success: function (data) {
                if (data.status == "ok") {
                    $('#Perpremio').DataTable().ajax.reload();
                    $("#alerta_premios").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $(".respuesta_premios").html("Se agregó correctamente.!");
                    $(".mensaje_premios").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);

                    $("#btnPremios").html('Aceptar');
                    $("#btnPremios").prop('disabled', false);
                    $('#altapremios').modal('hide');

                } else if (data.status == "existe") {
                    $('#Perpremio').DataTable().ajax.reload();
                    $("#alerta_premios").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-warning");
                    $(".respuesta_premios").html("¡Ya existe!");
                    $(".mensaje_premios").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnPremios").html('Aceptar');
                    $("#btnPremios").prop('disabled', false);
                    // $('#ConvVaca').DataTable().ajax.reload();
                } else {
                    $('#Perpremio').DataTable().ajax.reload();
                    $("#alerta_premios").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_premios").html("¡Error!");
                    $(".mensaje_premios").html(`<br />${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnPremios").html('Aceptar');
                    $("#btnPremios").prop('disabled', false);
                    // $('#ConvVaca').DataTable().ajax.reload();
                }
            }
        });
    });
});
/** BORARR PERSONAL PREMIOS*/
$(document).on('click', '.delete_perpremi', function (e) {
    e.preventDefault();
    // var parent = $(this).parent().parent().attr('id');
    var DelLPreLega = $(this).attr('data');
    var DelLPreCodi = $(this).attr('data2');
    var DelPerPremi = $(this).attr('data3');

    $.ajax({
        type: "POST",
        url: "alta_opciones.php",
        'data': {
            DelLPreLega,
            DelLPreCodi,
            DelPerPremi
        },

        success: function (data) {
            if (data.status == "ok_delete") {
                $('#Perpremio').DataTable().ajax.reload();
            } else {
                $('#Perpremio').DataTable().ajax.reload();
            }
        }
    });
});

/** ALTA PERSONAL OTROS CONCEPTOS */
$(document).ready(function () {
    $('#altaconceptos').on('hidden.bs.modal', function (e) {
        $("#alerta_conceptos").addClass("d-none");
        $("#btnconceptos").html('Aceptar');
        $("#btnconceptos").prop('disabled', false);
        $('.selectjs_conceptos').val(null).trigger("change");
        $('#OTROConValor').val(null);
    })

    $(".form-otrosconleg").bind("submit", function () {
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_conceptos").addClass("d-none");
                $("#btnconceptos").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">`);
                $("#btnconceptos").prop('disabled', true);
            },

            success: function (data) {
                if (data.status == "ok") {
                    $('#OtrosConLeg').DataTable().ajax.reload();
                    $("#alerta_conceptos").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $(".respuesta_conceptos").html("Se agregó correctamente.!");
                    $(".mensaje_conceptos").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);

                    $("#btnconceptos").html('Aceptar');
                    $("#btnconceptos").prop('disabled', false);
                    $('#altaconceptos').modal('hide');

                } else if (data.status == "existe") {
                    $('#OtrosConLeg').DataTable().ajax.reload();
                    $("#alerta_conceptos").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-warning");
                    $(".respuesta_conceptos").html("¡Ya existe!");
                    $(".mensaje_conceptos").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnconceptos").html('Aceptar');
                    $("#btnconceptos").prop('disabled', false);
                    // $('#ConvVaca').DataTable().ajax.reload();
                } else {
                    $('#OtrosConLeg').DataTable().ajax.reload();
                    $("#alerta_conceptos").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_conceptos").html("¡Error!");
                    $(".mensaje_conceptos").html(`<br />${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnconceptos").html('Aceptar');
                    $("#btnconceptos").prop('disabled', false);
                }
            }
        });
    });
});

/** BORARR PERSONAL OTROS CONCEPTOS*/
$(document).on('click', '.delete_otrosconleg', function (e) {
    e.preventDefault();
    // var parent = $(this).parent().parent().attr('id');
    var OTROConLega = $(this).attr('data');
    var OTROConCodi = $(this).attr('data2');
    var DelOtroConLeg = $(this).attr('data3');

    $.ajax({
        type: "POST",
        url: "alta_opciones.php",
        'data': {
            OTROConLega,
            OTROConCodi,
            DelOtroConLeg
        },

        success: function (data) {
            if (data.status == "ok_delete") {
                $('#OtrosConLeg').DataTable().ajax.reload();
            } else {
                $('#OtrosConLeg').DataTable().ajax.reload();
            }
        }
    });
});

/** ALTA PERSONAL HORARIO ALTERNATIVO */
$(document).ready(function () {
    $('#altahorarioal').on('hidden.bs.modal', function (e) {
        $("#alerta_horarioal").addClass("d-none");
        $("#btnhorarioal").html('Aceptar');
        $("#btnhorarioal").prop('disabled', false);
        $('.selectjs_horarioal').val(null).trigger("change");
        $('#OTROConValor').val(null);
    })

    $(".form-PerHoAl").bind("submit", function () {
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_horarioal").addClass("d-none");
                $("#btnhorarioal").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">`);
                $("#btnhorarioal").prop('disabled', true);
            },

            success: function (data) {
                if (data.status == "ok") {
                    $('#PerHoAlt').DataTable().ajax.reload();
                    $("#alerta_horarioal").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $(".respuesta_horarioal").html("Se agregó correctamente.!");
                    $(".mensaje_horarioal").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);

                    $("#btnhorarioal").html('Aceptar');
                    $("#btnhorarioal").prop('disabled', false);
                    $('#altahorarioal').modal('hide');

                } else if (data.status == "existe") {
                    $('#PerHoAlt').DataTable().ajax.reload();
                    $("#alerta_horarioal").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-warning");
                    $(".respuesta_horarioal").html("¡Ya existe!");
                    $(".mensaje_horarioal").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnhorarioal").html('Aceptar');
                    $("#btnhorarioal").prop('disabled', false);
                    // $('#ConvVaca').DataTable().ajax.reload();
                } else {
                    $('#PerHoAlt').DataTable().ajax.reload();
                    $("#alerta_horarioal").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_horarioal").html("¡Error!");
                    $(".mensaje_horarioal").html(`<br />${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnhorarioal").html('Aceptar');
                    $("#btnhorarioal").prop('disabled', false);
                }
            }
        });
    });
});

/** BORARR PERSONAL HORARIO ALTERNATIVO*/
$(document).on('click', '.delete_perhoalt', function (e) {
    e.preventDefault();
    // var parent = $(this).parent().parent().attr('id');
    var LeHALega = $(this).attr('data');
    var LeHAHora = $(this).attr('data2');
    var DelPerHoAl = $(this).attr('data3');

    $.ajax({
        type: "POST",
        url: "alta_opciones.php",
        'data': {
            LeHALega,
            LeHAHora,
            DelPerHoAl
        },

        success: function (data) {
            if (data.status == "ok_delete") {
                $('#PerHoAlt').DataTable().ajax.reload();
            } else {
                $('#PerHoAlt').DataTable().ajax.reload();
            }
        }
    });
});

/** ALTA IDENTIFICA */
$(document).ready(function () {
    $('#altaidentifica').on('hidden.bs.modal', function (e) {
        $("#alerta_identifica").addClass("d-none");
        $("#btnidentifica").html('Aceptar');
        $("#btnidentifica").prop('disabled', false);
        $('.form-Identifica')[0].reset();
    })

    $(".form-Identifica").bind("submit", function () {
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_identifica").addClass("d-none");
                $("#btnidentifica").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">`);
                $("#btnidentifica").prop('disabled', false);
            },

            success: function (data) {
                if (data.status == "ok") {
                    $('#Identifica-table').DataTable().ajax.reload();
                    $("#alerta_identifica").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $(".respuesta_identifica").html("Se agregó correctamente.!");
                    $(".mensaje_identifica").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);

                    $("#btnidentifica").html('Aceptar');
                    $("#btnidentifica").prop('disabled', false);
                    $('#altaidentifica').modal('hide');

                } else if (data.status == "existe") {
                    $('#Identifica-table').DataTable().ajax.reload();
                    $("#alerta_identifica").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-warning");
                    $(".respuesta_identifica").html("¡Ya existe!");
                    $(".mensaje_identifica").html(`<br />${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnidentifica").html('Aceptar');
                    $("#btnidentifica").prop('disabled', false);
                    // $('#ConvVaca').DataTable().ajax.reload();
                } else {
                    $('#Identifica-table').DataTable().ajax.reload();
                    $("#alerta_identifica").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_identifica").html("¡Error!");
                    $(".mensaje_identifica").html(`<br />${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnidentifica").html('Aceptar');
                    $("#btnidentifica").prop('disabled', false);
                }
            }
        });
    });
});

/** BORARR IDENTIFICA*/
$(document).on('click', '.delete_identifica', function (e) {
    e.preventDefault();
    var IDCodigo = $(this).attr('data');
    var IDLegajo = $(this).attr('data2');
    var DelIdentifica = $(this).attr('data3');

    $.ajax({
        type: "POST",
        url: "alta_opciones.php",
        'data': {
            IDCodigo,
            IDLegajo,
            DelIdentifica
        },

        success: function (data) {
            if (data.status == "ok_delete") {
                $('#Identifica-table').DataTable().ajax.reload();
            } else {
                $('#Identifica-table').DataTable().ajax.reload();
            }
        }
    });
});

/** UPDATE GRUPO CAPTURADORES */
$(document).ready(function () {
    // $(document).on('click', '#grupocapt', function (e) {
    $('.selectjs_grupocapt').on('select2:select', function (e) {
        e.preventDefault();

        var LegajoGrHa = $('#LegajoGrHa').val()
        var GrupoHabi = $('#GrupoHabi').val()
        var LegGrHa2 = $('#LegGrHa2').val()

        $.ajax({
            type: "POST",
            url: "alta_opciones.php",
            'data': {
                LegajoGrHa,
                GrupoHabi,
                LegGrHa2
            },
            beforeSend: function (data) {
                $("#alerta_grupocapt").addClass("d-none");
                $("#btngrupocapt").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">`);
                $("#btngrupocapt").prop('disabled', false);
            },
            success: function (data) {
                if (data.status == "ok") {
                    $('#LegGrHa').prop('value', data.LegGrHa);
                    $('#GrupoCapt').DataTable().clear().draw().destroy();
                    $('#GrupoCapt').DataTable({
                        // deferRender: true,
                        "ajax": {
                            url: "../../data/GetReloHabi.php",
                            type: "GET",
                            'data': {
                                q2: data.LegGrHa,
                            },
                        },
                        columns: [
                            // { "class": "align-middle ls1", "data": "Grupo" }, 
                            // { "class": "align-middle ls1", "data": "Reloj" }, 
                            { "class": "align-middle ls1", "data": "Serie" },
                            { "class": "align-middle", "data": "Descrip" },
                            { "class": "align-middle", "data": "Marca" },
                            { "class": "align-middle w-100", "data": "null" }
                        ],
                        paging: false,
                        // scrollY: '40vh',
                        scrollX: false,
                        scrollCollapse: false,
                        searching: false,
                        info: false,
                        ordering: false,
                        language: {
                            "url": "../../js/DataTableSpanish.json"
                        },
                    });
                    $("#btngrupocapt").html('Aceptar');
                    $("#btngrupocapt").prop('disabled', false);

                } else {
                    $('#GrupoCapt').DataTable().ajax.reload();
                    $("#btngrupocapt").html('Aceptar');
                    $("#btngrupocapt").prop('disabled', false);
                }
            }
        });
    });
});

/** ALTA PERRelo */
$(document).ready(function () {
    $('#altaPerRelo').on('hidden.bs.modal', function (e) {
        $("#alerta_PerRelo").addClass("d-none");
        $("#btnPerRelo").html('Aceptar');
        $("#btnPerRelo").prop('disabled', false);
        $('.form-PerRelo')[0].reset();
        $('.selectjs_Relojes').val(null).trigger('change');
    })

    $(".form-PerRelo").bind("submit", function () {
        event.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_PerRelo").addClass("d-none");
                $("#btnPerRelo").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">`);
                $("#btnPerRelo").prop('disabled', false);
            },

            success: function (data) {
                if (data.status == "ok") {
                    $('#TablePerRelo').DataTable().ajax.reload();
                    $("#alerta_PerRelo").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $(".respuesta_PerRelo").html("Se agregó correctamente.!");
                    $(".mensaje_PerRelo").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);

                    $("#btnPerRelo").html('Aceptar');
                    $("#btnPerRelo").prop('disabled', false);
                    $('#altaPerRelo').modal('hide');

                } else if (data.status == "existe") {
                    $('#TablePerRelo').DataTable().ajax.reload();
                    $("#alerta_PerRelo").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-warning");
                    $(".respuesta_PerRelo").html("¡Ya existe!");
                    $(".mensaje_PerRelo").html(`<br />${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnPerRelo").html('Aceptar');
                    $("#btnPerRelo").prop('disabled', false);
                    // $('#ConvVaca').DataTable().ajax.reload();
                } else {
                    $('#TablePerRelo').DataTable().ajax.reload();
                    $("#alerta_PerRelo").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_PerRelo").html("¡Error!");
                    $(".mensaje_PerRelo").html(`<br />${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnPerRelo").html('Aceptar');
                    $("#btnPerRelo").prop('disabled', false);
                }
            }
        });
    });
});

/** BORARR PERRelo*/
$(document).on('click', '.delete_perrelo', function (e) {
    e.preventDefault();
    var RelRelo = $(this).attr('data');
    var RelReMa = $(this).attr('data2');
    var RelLega = $(this).attr('data3');
    var DelPerrelo = $(this).attr('data4');

    $.ajax({
        type: "POST",
        url: "alta_opciones.php",
        'data': {
            RelRelo,
            RelReMa,
            RelLega,
            DelPerrelo
        },

        success: function (data) {
            if (data.status == "ok_delete") {
                $('#TablePerRelo').DataTable().clear().draw().destroy();
                $('#TablePerRelo').DataTable({
                    "ajax": {
                        url: "../../data/GetPerRelo.php",
                        type: "GET",
                        'data': {
                            q2: LegNume,
                        },
                    },
                    columns: [
                        { "class": "align-middle ls1", "data": "Serie" },
                        { "class": "align-middle ls1", "data": "Descrip" },
                        { "class": "align-middle", "data": "Marca" },
                        { "class": "align-middle ls1", "data": "Desde" },
                        { "class": "align-middle ls1 fw4", "data": "Vence" },
                        { "class": "align-middle text-center", "data": "eliminar" },
                        { "class": "align-middle w-100", "data": "null" }
                    ],
                    paging: false,
                    // scrollY: '40vh',
                    scrollX: false,
                    scrollCollapse: false,
                    searching: false,
                    info: false,
                    ordering: false,
                    language: {
                        "url": "../../js/DataTableSpanish.json"
                    },
                });
                // $('#altaPerRelo').DataTable().ajax.reload();
            } else {
                $('#altaPerRelo').DataTable().ajax.reload();
            }
        }
    });
});
//   window.onbeforeunload = function(e) {
//     return 'Texto de avisosssss';
//   };