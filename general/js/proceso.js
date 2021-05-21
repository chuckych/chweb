var loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
var maskBehavior = function (val) {
    val = val.split(":");
    return parseInt(val[0]) > 19 ? "HZ:M0" : "H0:M0";
}
spOptions = {
    onKeyPress: function (val, e, field, options) {
        field.mask(maskBehavior.apply({}, arguments), options);
    },
    translation: {
        'H': { pattern: /[0-2]/, optional: false },
        'Z': { pattern: /[0-3]/, optional: false },
        'M': { pattern: /[0-5]/, optional: false }
    }
};
$('.HoraMask').mask(maskBehavior, spOptions);

function OcultaNavTab() {
    if ($("#Mxs").val() == '1') {
        // $("#nav-tab").addClass('d-none d-sm-block')
        $("#nav-tab").hide()
        $("#nav-tabContent").addClass('border')
    }
}
function MuestraNavTab() {
    if ($("#Mxs").val() == '1') {
        // $("#nav-tab").removeClass('d-none d-sm-block')
        $("#nav-tab").show()
        $("#nav-tabContent").removeClass('border')
    }
}
/** ALTA FICHADA */
function DestroyDataTablesModal() {
    $('#GetFichadas').DataTable().clear().draw().destroy();
    $('#GetNovedades').DataTable().clear().draw().destroy();
    $('#GetHoras').DataTable().clear().draw().destroy();
    $('#GetOtrasNov').DataTable().clear().draw().destroy();
};
function ClearFormNov() {
    $(".Form_Novedad").addClass('d-none')
    $(".respuesta_Novedades").html('')
    $(".Form_Novedad_Mod").addClass('d-none')
    $(".FicHoras").val('00:00')
    $(".respuesta_novedad").html('');
    $('.selectjs_Novedades').val(null).trigger('change');
    $('.selectjs_NoveCausa').val(null).trigger('change');
    $("#FicObse").val('');
    $("#FicJust").prop('checked', false);
    $("#FicCate").prop('checked', false);
    $("#FicCate").prop('disabled', false);
    $("#xsTNov").html('Novedades')
};
function ClearFormFic() {
    $(".Form_Fichadas").addClass('d-none')
    $(".respuesta_fichada").html('')
    $(".Form_Fichadas_Mod").addClass('d-none')
    $(".RegHora").val('')
    $(".respuesta_fichada").html("");
    $(".respuesta_fichada_mod").html("");
    $(".respuesta_baja_fichada").addClass('d-none')
    $("#xsTFic").html('Fichadas')
};
$("#ProcesarLegajo").attr("disabled", false);
$("#ProcesarLegajo").html("Procesar");
function ClearFormHora() {
    $(".Form_Horas").addClass('d-none')
    $(".respuesta_Horas").html("");
    $("#Fic1HsAu2").val('00:00')
    $('.selectjs_TipoHora').val(null).trigger('change');
    $('.selectjs_MotivoHora').val(null).trigger('change');
    $("#Fic1Observ").val('');
    $("#xsTHor").html('Horas')
};
function ClearFormONov() {
    $(".Form_OtraNovedad").addClass('d-none')
    $(".respuesta_OtrasNov").html('')
    $("#alta_OtrasNov").val("true").trigger('change');
    $("#FicValor").val(null).trigger('change');
    $('.selectjs_OtrasNovedades').val(null).trigger('change');
    $("#FicObsN").val(null).trigger('change');
    $("#xsTOnov").html('Otras Novedades')
};
function ClearFormCitacion() {
    $("#rowCitacion").addClass('d-none')
    $(".respuesta_Citacion").html('')
    $("#alta_Citación").val("true");
    $("#CitEntra").val();
    $("#CitSale").val();
    $("#CitDesc").val('00:00');
    $("#rowCitacion").removeClass('animate__animated animate__fadeIn')
    $(".submit_btn_Citación").prop("disabled", false);
    $('#modalGeneral').modal('hide')
};
/** Toogle Rango de fecha por defecto Activo */
$("#_range").prop('checked', true);
$("#divRangofecha").addClass('d-none');
$("#PagDia").addClass('d-none');
/** Toogle Solo Laboral por defecto Activo */
$("#_dl").prop('checked', true);
/** Div de paginar por dias invisible */
$('#PagDia').addClass('invisible');
/** Botton total dia oculto */
$('#Total_dia').addClass('d-none');
/** Al hacer cick en el toggle Solo Laboral */
$(document).on("click", "#_dl", function () {
    if ($("#_dl").is(":checked")) {
        $("#_dl").val('on').trigger('change')
        $('#GetGeneral').DataTable().ajax.reload(null, false);
    } else {
        $("#_dl").val('off').trigger('change')
        $('#GetGeneral').DataTable().ajax.reload(null, false);
    }
});
$(document).on("click", "#FullScreen", function () {
    $("#container").removeClass("container")
    $("#container").addClass("container-fluid")
    // $(".table-responsive").addClass("d-flex justify-content-center")
    $("#FullScreen").addClass("d-none")
    // $("#GetGeneral").removeClass("w-100")
    $("#GetGeneral").addClass("w-100")
    $("#NormalScreen").removeClass("d-none")
    $("#GetGeneral").removeClass("text-wrap")
    $("#GetGeneral").addClass("text-nowrap")
    $('#GetGeneral').DataTable().ajax.reload(null, false);
});
$(document).on("click", "#NormalScreen", function () {
    $("#container").removeClass("container-fluid")
    $("#container").addClass("container")
    // $(".table-responsive").removeClass("d-flex justify-content-center")
    $("#GetGeneral").removeClass("w-auto")
    $("#GetGeneral").addClass("w-100")
    $("#GetGeneral").addClass("text-wrap")
    $("#GetGeneral").removeClass("text-nowrap")
    $("#FullScreen").removeClass("d-none")
    $("#NormalScreen").addClass("d-none")
    $('#GetGeneral').DataTable().ajax.reload(null, false);

});
/** Al hace click en toggle Rango de Fecha de la pagina*/
$(document).on("click", "#_range", function () {
    if ($("#_range").is(":checked")) {
        $("#_range").val('on')
        $('#Total_dia').addClass('d-none');
        $('#GetGeneral').DataTable().ajax.reload(null, false);
        $('#PagDia').addClass('invisible');
    } else {
        $("#_range").val('off')
        $('#Total_dia').removeClass('d-none');
        $('#GetGeneral').DataTable().ajax.reload(null, false);
        $('#PagDia').removeClass('invisible');
    }
});

/** Al hacer click en el link Procesar */
$(document).on("click", "#ProcesarTodo", function (e) {
    e.preventDefault();
    $.ajax({
        type: "POST",
        dataType: "text",
        url: "procesar.php?Inicio=" + FechaIni + "&Fin=" + FechaFin,
        beforeSend: function () {
            $.notifyClose();
            notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
            $("#ProcesarTodo").attr("disabled", true);
        },
        success: function (data, textStatus, jqXHR) {
            $("#ProcesarTodo").attr("disabled", false);
            $("#ProcesarTodo").html("Procesar");
            if (data == 'Terminado') {
                var TextResult = "Datos Procesados";
            } else {
                var TextResult = `Error<br> ${data}</span>`;
            }
            $('#GetGeneral').DataTable().ajax.reload(null, false);
            $.notifyClose();
            notify(TextResult, 'success', 5000, 'right')
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $("#ProcesarTodo").attr("disabled", false);
            $("#ProcesarTodo").html("Procesar");
            $.notifyClose();
            notify('Error', 'danger', 5000, 'right')

        },
    });
});
/** MODAL Y TABLAS DE TOTALES GENERALES y POR DÍAS */
$(document).ready(function () {
    /** Abre MODAL de Total general */
    $('#Total_General').on('shown.bs.modal', function (e) {
        /** Tabla de Total general Horas */
        $('#table-Total_General').DataTable({
            bProcessing: true,
            ajax: {
                url: host + "/?p=array_general_totales.php&FechaIni=" + FechaIni + "&FechaFin=" + FechaFin + "&" + QueryString,
                // url: "<?= host() ?>/<?= HOMEHOST ?>/<?= $_datos ?>/?p=array_general_totales.php&FechaIni=<?= $FechaIni ?>&FechaFin=<?= $FechaFin ?>&<?= $_SERVER['QUERY_STRING'] ?>",
                dataSrc: "Horas",
                type: "GET",
                "data": function (data) {
                    data._c = $("#_c").val();
                    data._r = $("#_r").val();
                    data._dl = $("input[name=_dl]:checked").val();
                    data._range = $("input[name=_range]:checked").val();
                },
            },
            columns: [{
                "class": "",
                "data": "Cod"
            }, {
                "class": "",
                "data": "Descripcion"
            }, {
                "class": "text-center bg-light fw4 ls1",
                "data": "HsAuto"
            }, {
                "class": "text-center ls1",
                "data": "HsCalc"
            }, {
                "class": "text-center ls1",
                "data": "HsHechas"
            }, {
                "class": "text-center ls1 w-100 text-white",
                "data": "Cod"
            }],
            paging: false,
            searching: false,
            scrollCollapse: false,
            info: false,
            ordering: false,
            language: {
                "url": "../js/DataTableSpanish.json"
            },
        });
        /** Tabla de Total general Novedades */
        $('#table-Total_Novedades').DataTable({
            ajax: {
                url: host + "/?p=array_general_totales.php&FechaIni=" + FechaIni + "&FechaFin=" + FechaFin + "&" + QueryString,
                //url: "<?= host() ?>/<?= HOMEHOST ?>/<?= $_datos ?>/?p=array_general_totales.php&FechaIni=<?= $FechaIni ?>&FechaFin=<?= $FechaFin ?>&<?= $_SERVER['QUERY_STRING'] ?>",
                dataSrc: "Novedades",
                type: "GET",
                "data": function (data) {
                    data._c = $("#_c").val();
                    data._r = $("#_r").val();
                    data._dl = $("input[name=_dl]:checked").val();
                    data._range = $("input[name=_range]:checked").val();
                },
            },
            columns: [{
                "class": "",
                "data": "Cod"
            }, {
                "class": "",
                "data": "Descripcion"
            }, {
                "class": "text-center bg-light fw4 ls1",
                "data": "Horas"
            }, {
                "class": "text-center ls1",
                "data": "Dias"
            }, {
                "class": "",
                "data": "Tipo"
            }, {
                "class": "ls1 w-100 text-white",
                "data": "Dias"
            }],
            paging: false,
            searching: false,
            scrollCollapse: false,
            info: false,
            ordering: false,
            language: {
                "url": "../js/DataTableSpanish.json"
            },
        });
        /** Tabla de Total general NovTipo */
        $('#table-TotalNovTipo').DataTable({
            ajax: {
                url: host + "/?p=array_general_totales.php&FechaIni=" + FechaIni + "&FechaFin=" + FechaFin + "&" + QueryString,
                //url: "<?= host() ?>/<?= HOMEHOST ?>/<?= $_datos ?>/?p=array_general_totales.php&FechaIni=<?= $FechaIni ?>&FechaFin=<?= $FechaFin ?>&<?= $_SERVER['QUERY_STRING'] ?>",
                dataSrc: "NovTipo",
                type: "GET",
                "data": function (data) {
                    data._c = $("#_c").val();
                    data._r = $("#_r").val();
                    data._dl = $("input[name=_dl]:checked").val();
                    data._range = $("input[name=_range]:checked").val();
                },
            },
            columns: [{
                "class": "",
                "data": "Descripcion"
            }, {
                "class": "text-center bg-light fw4 ls1",
                "data": "Horas"
            }, {
                "class": "text-center ls1",
                "data": "Dias"
            }, {
                "class": "text-center ls1 w-100 text-white",
                "data": "Dias"
            }],
            paging: false,
            searching: false,
            scrollCollapse: false,
            info: false,
            ordering: false,
            language: {
                "url": "../js/DataTableSpanish.json"
            },
        });
    });
    /** Destruir las tablas del MODAL de totales al cerrar el MODAL Total general*/
    $('#Total_General').on('hide.bs.modal', function (e) {
        $('#table-Total_General').DataTable().clear().draw().destroy();
        $('#table-Total_Novedades').DataTable().clear().draw().destroy();
        $('#table-TotalNovTipo').DataTable().clear().draw().destroy();
    })
    /** Abre MODAL de Total por día*/
    $('#Total_pdia').on('shown.bs.modal', function (e) {
        /** Total por día Horas*/
        $('#table-Total_General_Dia').DataTable({
            ajax: {
                url: host + "/?p=array_general_totales.php&FechaIni=" + FechaIni + "&FechaFin=" + FechaFin + "&" + QueryString + "&dia=" + diaString,
                dataSrc: "Horas",
                type: "GET",
                "data": function (data) {
                    data._c = $("#_c").val();
                    data._r = $("#_r").val();
                    data._dl = $("input[name=_dl]:checked").val();
                    data._range = $("input[name=_range]:checked").val();
                },
            },
            columns: [{
                "class": "",
                "data": "Cod"
            }, {
                "class": "",
                "data": "Descripcion"
            }, {
                "class": "text-center bg-light fw4 ls1",
                "data": "HsAuto"
            }, {
                "class": "text-center ls1",
                "data": "HsCalc"
            }, {
                "class": "text-center ls1",
                "data": "HsHechas"
            }, {
                "class": "text-center ls1 w-100 text-white",
                "data": "Cod"
            }],
            paging: false,
            searching: false,
            scrollCollapse: false,
            info: false,
            ordering: false,
            language: {
                "url": "../js/DataTableSpanish.json"
            },
        });
        /** Total por día Novedades*/
        $('#table-Total_Nov_Dia').DataTable({
            ajax: {
                url: host + "/?p=array_general_totales.php&FechaIni=" + FechaIni + "&FechaFin=" + FechaFin + "&" + QueryString + "&dia=" + diaString,
                dataSrc: "Novedades",
                type: "GET",
                "data": function (data) {
                    data._c = $("#_c").val();
                    data._r = $("#_r").val();
                    data._dl = $("input[name=_dl]:checked").val();
                    data._range = $("input[name=_range]:checked").val();
                },
            },
            columns: [{
                "class": "",
                "data": "Cod"
            }, {
                "class": "",
                "data": "Descripcion"
            }, {
                "class": "text-center bg-light fw4 ls1",
                "data": "Horas"
            }, {
                "class": "text-center ls1",
                "data": "Dias"
            }, {
                "class": "",
                "data": "Tipo"
            }, {
                "class": "ls1 w-100 text-white",
                "data": "Dias"
            }],
            paging: false,
            searching: false,
            scrollCollapse: false,
            info: false,
            ordering: false,
            language: {
                "url": "../js/DataTableSpanish.json"
            },
        });
        /** Total por día NovTipo*/
        $('#table-Total_Nov_Tipo_Dia').DataTable({
            ajax: {
                url: host + "/?p=array_general_totales.php&FechaIni=" + FechaIni + "&FechaFin=" + FechaFin + "&" + QueryString + "&dia=" + diaString,
                dataSrc: "NovTipo",
                type: "GET",
                "data": function (data) {
                    data._c = $("#_c").val();
                    data._r = $("#_r").val();
                    data._dl = $("input[name=_dl]:checked").val();
                    data._range = $("input[name=_range]:checked").val();
                },
            },
            columns: [{
                "class": "",
                "data": "Tipo"
            }, {
                "class": "",
                "data": "Descripcion"
            }, {
                "class": "text-center bg-light fw4 ls1",
                "data": "Horas"
            }, {
                "class": "text-center ls1",
                "data": "Dias"
            }, {
                "class": "text-center ls1 w-100 text-white",
                "data": "Dias"
            }],
            paging: false,
            searching: false,
            scrollCollapse: false,
            info: false,
            ordering: false,
            language: {
                "url": "../js/DataTableSpanish.json"
            },
        });
    });
    /** Destruir las tablas del MODAL de totales por dia al cerrar el MODAL Total dia*/
    $('#Total_pdia').on('hide.bs.modal', function (e) {
        $('#table-Total_General_Dia').DataTable().clear().draw().destroy();
        $('#table-Total_Nov_Dia').DataTable().clear().draw().destroy();
        $('#table-Total_Nov_Tipo_Dia').DataTable().clear().draw().destroy();
    })
});
/** Al hace click en boton + agregar fichada del modal general */
$(document).on("click", "#AddFic", function (e) {
    ClearFormFic()
    $(".Form_Fichadas").removeClass('d-none')
    $(".Form_Fichadas").addClass('animate__animated animate__fadeIn')
    $(".submit_btn").prop("disabled", false);
    fadeInOnly('#RegHora');
    $("#RegHora").select();
    OcultaNavTab()
    $("#xsTFic").html('Agregar Fichada')
});
/** Al hace click en boton + agregar novedad del modal general*/
$(document).on("click", "#AddNov", function (e) {
    ClearFormNov()
    $(".Form_Novedad").removeClass('d-none')
    $(".Form_Novedad").addClass('animate__animated animate__fadeIn')
    $("#alta_novedad").val("true")
    $("#FicCate").prop('disabled', false);
    $(".submit_btn_mod").html('Agregar');
    $('#novTipo').val('').trigger('change');
    $(".submit_btn_mod").prop("disabled", false);
    $(".selectjs_Novedades").select2('open');
    OcultaNavTab()
    $("#xsTNov").html('Agregar Novedad')
});
/** Al hace click en boton + agregar hora del modal general*/
$(document).on("click", "#AddHora", function (e) {
    ClearFormHora()
    $("#modHora").val("0").trigger('change');
    $(".Form_Horas").removeClass('d-none')
    $(".Form_Horas").addClass('animate__animated animate__fadeIn')
    $("#alta_horas").val("true").trigger('change');
    $(".submit_btn_HorMod").html('Agregar');
    $("#cancelar_btn_hor").html('Cancelar');
    $(".submit_btn_HorMod").prop("disabled", false);
    $(".selectjs_TipoHora").select2('open');
    OcultaNavTab()
    $("#xsTHor").html('Agregar Horas')
});
/** Al hace click en boton + agregar Otra Novedad del modal general*/
$(document).on("click", "#AddONov", function (e) {
    ClearFormONov()
    $(".Form_OtraNovedad").removeClass('d-none')
    $(".Form_OtraNovedad").addClass('animate__animated animate__fadeIn')
    $(".submit_btn_OtrasNov").html('Agregar');
    $(".submit_btn_OtrasNov").prop("disabled", false);
    $(".selectjs_OtrasNovedades").select2('open');
    OcultaNavTab()
    $("#xsTOnov").html('Agregar Novedad')
});
/** Al hace click en boton cancelar del formulario fichadas del modal general*/
$(document).on("click", ".cancelar_btn_fic", function (e) {
    ClearFormFic();
});
/** Al hace click en boton cancelar del formulario novedade del modal general*/
$(document).on("click", ".cancelar_btn_nov", function (e) {
    ClearFormNov();
    $('#novCate').val('2').trigger('change');
});
/** Al hace click en boton cancelar del formulario horas del modal general*/
$(document).on("click", "#cancelar_btn_hor", function (e) {
    ClearFormHora();
});
/** Al hace click en boton cancelar del formulario Otra Novedad del modal general*/
$(document).on("click", "#cancelar_btn_OtrasNov", function (e) {
    ClearFormONov();
});
$(document).on("click", "#cancelar_btn_Citación", function (e) {
    // $("#rowCitacion").addClass('d-none')
    ClearFormCitacion();
});


$(document).on("click", ".procReg", function (e) {

    $(this).parents('tr td').addClass('text-info')
    $(this).removeClass('procReg')
    e.preventDefault()

    let Nombre = $(this).attr("data-nombre")
    let NumLega = $(this).attr("data-lega")
    let fechaini = $(this).attr("data-fechaini")
    let fechafin = $(this).attr("data-fechafin")
    let procLega = $(this).attr("data-procLega")

    $.ajax({
        type: "POST",
        dataType: "json",
        url: "../procesar/procesando.php",
        data: {
            procesaLegajo: procLega,
            nombreLegajo: Nombre,
            procesando: true,
            ProcLegaIni: NumLega,
            ProcLegaFin: NumLega,
            ProcFechaIni: fechaini,
            ProcFechaFin: fechafin,
        },
        beforeSend: function () {
            $.notifyClose();
            notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
        },
        success: function (data) {
            if (data.status == "ok") {
                $(this).addClass('procReg')
                ActualizaTablas()
                $.notifyClose();
                notify(data.Mensaje, 'success', 3000, 'right')
            } else {
                $.notifyClose();
                ActualizaTablas()
                $(this).addClass('procReg')
                notify(data.Mensaje, 'danger', 3000, 'right')
            }

        }
    });
});
/** ABRIR MODAL */
$(document).on("click", ".open-modal", function (e) {
    $(document).off('keydown');
    e.preventDefault();

    $('#modalGeneral').modal('show');
    $('#modalGeneral').modal('handleUpdate')
    $('.navbar').addClass('mr-0');
    $('#Fichadas-tab').tab('show');
    $("#AddHora").addClass('d-none');
    $("#AddNov").addClass('d-none');
    $("#AddONov").addClass('d-none');
    $('#Horas').addClass('d-none')
    $('#Novedades').addClass('d-none')
    $('#OtrasNov').addClass('d-none')
    $('#FicValor').mask('##.##00.00', { reverse: true });

    var Datos = $(this).attr('data');
    var Nombre = $(this).attr('data2');
    var Fecha = $(this).attr('data3');
    var Dia = $(this).attr('data4');
    var Horario = $(this).attr('data5');
    var FechaStr = $(this).attr('data6');
    var Cita = $(this).attr('data7');
    var mFic = $(this).attr('data_mFic');
    var mHor = $(this).attr('data_mHor');
    var mNov = $(this).attr('data_mNov');
    var NumLega = Datos.split('-');

    if (mFic == '1') {
        $("#Mxs").val('1')
        $('.nav').addClass('d-none')
        Modal_XL_LG('#TopN')
        $('#Fichadas').removeClass('border-top-0')
    }
    if (mHor == '1') {
        $("#Mxs").val('1')
        $('.nav').addClass('d-none')
        $('#Horas-tab').tab('show')
        Modal_XL_LG('#TopN')
        $('#Horas').removeClass('border-top-0')
    }
    if (mNov == '1') {
        $("#Mxs").val('1')
        $('.nav').addClass('d-none')
        $('#Novedades-tab').tab('show')
        Modal_XL_LG('#TopN')
        $('#Novedades').removeClass('border-top-0')
    }
    $('#Fichadas-tab').on('shown.bs.tab', function (e) {
        $('#Fichadas').removeClass('d-none')
        fadeInOnly('#Fichadas')
        $('#Horas').addClass('d-none')
        $('#Novedades').addClass('d-none')
        $('#OtrasNov').addClass('d-none')
    })
    $('#Novedades-tab').on('shown.bs.tab', function (e) {
        $('#Novedades').removeClass('d-none')
        fadeInOnly('#Novedades')
        $('#Fichadas').addClass('d-none')
        $('#Horas').addClass('d-none')
        $('#OtrasNov').addClass('d-none')
    })
    $('#Horas-tab').on('shown.bs.tab', function (e) {
        $('#Horas').removeClass('d-none')
        fadeInOnly('#Horas')
        $('#Novedades').addClass('d-none')
        $('#Fichadas').addClass('d-none')
        $('#OtrasNov').addClass('d-none')
    })
    $('#OtrasNov-tab').on('shown.bs.tab', function (e) {
        $('#OtrasNov').removeClass('d-none')
        fadeInOnly('#OtrasNov')
        $('#Horas').addClass('d-none')
        $('#Novedades').addClass('d-none')
        $('#Fichadas').addClass('d-none')
    })

    $('#data').val(Datos);

    $(".nombre").html(Nombre + "<br /><span class='text-secondary fonth fw4'>Legajo: <span class='ls1'>" + NumLega[0] + "</span></span>");
    $(".NumLega").html(NumLega[0]);
    $(".fecha").html(Fecha);
    $(".dia").html(Dia);
    // $(".horario").html(Horario);
    $(".datos_fichada").val(Datos);
    $(".datos_novedad").val(Datos);
    $(".datos_hora").val(Datos);
    $(".datos_OtrasNov").val(Datos);
    $(".datos_Citación").val(Datos);
    $(".RegFech").val(FechaStr);

    /** Al hacer click en el link Procesar dentro del Modal */
    $("#ProcesarLegajo").on("click", function () {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "../procesar/procesando.php",
            data: {
                procesaLegajo: true,
                nombreLegajo: Nombre,
                procesando: true,
                ProcLegaIni: NumLega[0],
                ProcLegaFin: NumLega[0],
                ProcFechaIni: FechaStr,
                ProcFechaFin: FechaStr,
            },
            beforeSend: function () {
                $.notifyClose();
                notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                CierraModalGeneral();
            },
            success: function (data) {
                if (data.status == "ok") {
                    DisabledClean()
                    RefreshDataTables();
                    $.notifyClose();
                    notify(data.Mensaje, 'success', 5000, 'right')
                } else {
                    DisabledClean()
                    RefreshDataTables();
                    $.notifyClose();
                    notify(data.Mensaje, 'danger', 5000, 'right')
                }

            }
        });
    });

    /** GET FECHA CIERRE */
    function GetCierre() {
        // event.preventDefault();
        $.ajax({
            type: 'GET',
            dataType: "json",
            url: "../data/GetCierre.php",
            'data': {
                Datos: $('#data').val()
            },
            success: function (respuesta) {
                if (respuesta.status == 'ok') {
                    $("#FechCierre").html('<br /> Fecha Cierre: <span class="ls1 fw5">' + respuesta.dato + '</span>');
                    $("#AddHora").prop('disabled', true)
                    $("#AddONov").prop('disabled', true)
                    $("#AddFic").prop('disabled', true)
                    $("#AddNov").prop('disabled', true)
                    $(".mod_Fic").prop('disabled', true);
                    $("#Citacion").prop("disabled", true);
                    $("#Citacion").addClass('d-none');
                } else {
                    $("#FechCierre").html('');
                    // $("#FechCierre").addClass('d-none');
                    $("#AddHora").prop('disabled', false)
                    $("#AddONov").prop('disabled', false)
                    $("#AddFic").prop('disabled', false)
                    $("#AddNov").prop('disabled', false)
                }

            },
            error: function () {

            }
        });
    }
    GetCierre()
    /** GET FICHAS */
    function refrescaFichas() {
        // event.preventDefault();
        $.ajax({
            type: 'GET',
            dataType: "json",
            url: "GetFichas2.php",
            'data': {
                Datos: $('#data').val()
            },
            success: function (respuesta) {
                // const FicHsTr1 = respuesta.FicHsTr;
                //$("#FicHsTr").html(respuesta.FicHsTr + 'Hs.');
                // if (respuesta.FicDiaL == 1) {
                //     $("#TextFicHsAT").html('<span class="text-dark d-none d-sm-block">Horas a Trabajar </span>');
                //     $("#TextFicHsAT_M").html('<span class="text-dark d-block d-sm-none">A Trabajar </span>');
                //     $("#FicHsAT").html(respuesta.FicHsAT + 'Hs.');
                //     $("#divHorasTR").removeClass("d-none");
                // } else {
                //     $("#FicHsAT").html("");
                //     $("#TextFicHsAT").html('');
                //     $("#divHorasTR").addClass("d-none");
                // }
                // if (respuesta.FicHsTr != "00:00") {
                //     $("#TextFicHsTr").html('<span class="text-dark d-none d-sm-block">Horas Trabajadas </span>');
                //     $("#TextFicHsTr_M").html('<span class="text-dark d-block d-sm-none">Trabajadas </span>');
                //     // $("#FicHsTr").html(respuesta.FicHsTr + 'Hs.');
                // } else {
                //     $("#FicHsTr").html("");
                //     $("#TextFicHsTr").html('');
                // }

                // $("#FicHorario").html(respuesta.FicHorario);
                // if (respuesta.HorasNeg == 1) {
                //     $("#FicHsTr").addClass('text-danger');
                // } else {
                //     $("#FicHsTr").removeClass('text-danger');
                // }
            },
            error: function () {
            }
        });
    }
    // refrescaFichas()
    /** FIN GET FICHAS */

    /** GET CITACION */
    function GetCitacion() {
        // $(document).ready(function (e) {
        // e.preventDefault();
        $.ajax({
            type: 'GET',
            dataType: "json",
            url: "GetCitacion.php",
            'data': {
                Datos: $('#data').val()
            },
            beforeSend: function () {

            },
            success: function (respuesta) {
                (respuesta.CitEntra != 0) ? $("#CitEntra").val(respuesta.CitEntra) : $("#CitEntra").val();
                (respuesta.CitSale != 0) ? $("#CitSale").val(respuesta.CitSale) : $("#CitSale").val();
                (respuesta.CitDesc != 0) ? $("#CitDesc").val(respuesta.CitDesc) : $("#CitDesc").val();
            },
            error: function () {
                $("#CitEntra").val();
                $("#CitSale").val();
                $("#CitDesc").val('00:00');
            }
        });
        // });
    }

    // $(document).on('click', '.Citacion', function (e) {
    // });
    // GetCitacion()
    /** FIN GET CITACION */

    $('#GetFichadas').DataTable({
        "initComplete": function (settings, json) {
        },
        "drawCallback": function (settings) {
            $.each(settings.json, function (key, value) {
                (value.length > 0) ? $("#CantFic").html("(" + value.length + ")") : $("#CantFic").html("");
            });
        },

        bProcessing: true,
        deferRender: true,
        "ajax": {
            url: "GetFichadas.php",
            type: "GET",
            dataSrc: "Fichadas",
            'data': {
                Datos
            },
        },
        columns: [
            { "class": "align-middle ls1", "data": "Fic" },
            { "class": "align-middle", "data": "editar" },
            { "class": "align-middle", "data": "eliminar" },
            { "class": "align-middle", "data": "Estado" },
            { "class": "align-middle", "data": "Tipo" },
            { "class": "align-middle ls1", "data": "Fecha" },
            { "class": "align-middle ls1", "data": "Original" },
            { "class": "align-middle w-100", "data": "null" }
        ],
        paging: false,
        // scrollY: '100px',
        scrollX: false,
        scrollCollapse: false,
        searching: false,
        info: false,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanish.json"
        },
    });
    $('#GetNovedades').DataTable({
        "drawCallback": function (settings) {
            $.each(settings.json, function (key, value) {
                (value.length > 0) ? $("#CantNov").html("(" + value.length + ")") : $("#CantNov").html("");
            });
        },
        bProcessing: true,
        deferRender: true,
        "ajax": {
            url: "GetNovedades.php",
            type: "GET",
            dataSrc: "Novedades",
            'data': {
                Datos
            },
        },
        columns: [
            { "class": "align-middle ls1", "data": "Cod" },
            { "class": "align-middle", "data": "Descripcion" },
            { "class": "align-middle ls1 fw5", "data": "Horas" },
            { "class": "align-middle ", "data": "editar" },
            { "class": "align-middle ", "data": "eliminar" },
            { "class": "align-middle", "data": "Obserb" },
            { "class": "align-middle", "data": "Causa" },
            { "class": "align-middle", "data": "Just" },
            { "class": "align-middle", "data": "Tipo" },
            { "class": "align-middle", "data": "Cate" },
            { "class": "align-middle w-100", "data": "null" }
        ],
        paging: false,
        // scrollY: '100px',
        scrollX: false,
        scrollCollapse: false,
        searching: false,
        info: false,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanish.json"
        },
    });
    $('#GetHoras').DataTable({
        "drawCallback": function (settings) {
            // console.log(settings.json);
            $.each(settings.json, function (key, value) {
                // console.log(key);
                if (key == 'Horas') {
                    (value.length > 0) ? $("#CantHor").html("(" + value.length + ")") : $("#CantHor").html("");
                }
                if (key == 'Fichas') {
                    // console.log(value.Horario);
                    // $("#FicHsTr").html(value.FicHsTr + 'Hs.');

                    if (value.FicDiaL == 1) {
                        $("#TextFicHsAT").html('<span class="text-dark d-none d-sm-block">Horas a Trabajar </span>');
                        $("#TextFicHsAT_M").html('<span class="text-dark d-block d-sm-none">A Trabajar </span>');
                        $("#FicHsAT").html(value.FicHsAT + 'Hs.');
                        $("#divHorasTR").removeClass("d-none");
                    } else {
                        $("#FicHsAT").html("");
                        $("#TextFicHsAT").html('');
                        $("#divHorasTR").addClass("d-none");
                    }
                    if (value.FicHsTr != "00:00") {
                        $("#TextFicHsTr").html('<span class="text-dark d-none d-sm-block">Horas Trabajadas </span>');
                        $("#TextFicHsTr_M").html('<span class="text-dark d-block d-sm-none">Trabajadas </span>');
                        $("#FicHsTr").html(value.FicHsTr + 'Hs.');
                    } else {
                        $("#FicHsTr").html("");
                        $("#TextFicHsTr").html('');
                    }
                    $("#FicHorario").html(value.Horario);
                    if (value.HorasNeg == 1) {
                        $("#FicHsTr").addClass('text-danger');
                    } else {
                        $("#FicHsTr").removeClass('text-danger');
                    }

                    var Porcentaje = (value.FicHsTrMin / value.FicHsATMin) * 100
                    var Porcentaje = Porcentaje.toFixed()
                    var DatoFicha = value.DatoFicha

                    var Max = (value.FicHsTrMin > value.FicHsATMin) ? value.FicHsTrMin : value.FicHsATMin
                    // console.log(Porcentaje.toFixed());
                    if (value.FicHsTrMin > 0 && value.FicHsTrMin <= value.FicHsATMin) {

                        $('#ProgressHoras').html('<div class="pb-2">'
                            + '<div class="progress border-0" style="height: 20px;">'
                            + '<div id="' + DatoFicha + '" class="progress-bar" role="progressbar" style="width: ' + Porcentaje + '%;" aria-valuenow="' + value.FicHsTrMin + '" aria-valuemin="0" aria-valuemax="' + Max + '">' + value.FicHsTr + '</div>'
                            + '</div>'
                            + '</div>')

                        if (value.FicHsTrMin < value.FicHsATMin) {
                            $("#" + DatoFicha).addClass('bg-danger');
                            $("#" + DatoFicha).removeClass('bg-custom');
                        } else {
                            $("#" + DatoFicha).removeClass('bg-danger');
                            $("#" + DatoFicha).addClass('bg-custom');
                        }
                    } else if (value.FicHsTrMin > value.FicHsATMin) {

                        var Porcentaje = (value.FicHsATMin / value.FicHsTrMin) * 100
                        var Porcentaje = Porcentaje.toFixed()
                        var Porcentaje2 = (100 - Porcentaje) + 5
                        // if (Porcentaje2 < 10) {
                        //     var Max2 = (Max + 200)
                        // }else{
                        //     var Max2 = (Max)
                        // }
                        var Max2 = (Max)
                        $('#ProgressHoras').html('<div class="pb-2">'
                            + '<div class="progress" style="height: 20px;">'
                            + '<div id="' + DatoFicha + '" class="progress-bar" role="progressbar" style="width: ' + Porcentaje + '%;" aria-valuenow="' + value.FicHsTrMin + '" aria-valuemin="0" aria-valuemax="' + Max + '">' + value.FicHsAT + '</div>'
                            + '<div class="progress-bar bg-info" role="progressbar" style="width: ' + Porcentaje2 + '%;" aria-valuenow="' + value.FicHsTrMin + '" aria-valuemin="0" aria-valuemax="' + Max2 + '"></div>'
                            + '</div>'
                            + '</div>')

                        if (value.FicHsTrMin < value.FicHsATMin) {
                            $("#" + DatoFicha).addClass('bg-danger');
                            $("#" + DatoFicha).removeClass('bg-custom');
                        } else {
                            $("#" + DatoFicha).removeClass('bg-danger');
                            $("#" + DatoFicha).addClass('bg-custom');
                        }

                    } else {
                        $('#ProgressHoras').html('')
                    }

                }
            });
        },
        bProcessing: true,
        deferRender: true,
        "ajax": {
            url: "GetHoras.php",
            type: "GET",
            dataSrc: "Horas",
            'data': {
                Datos
            },
        },
        columns: [
            { "class": "align-middle ls1", "data": "Cod" },
            { "class": "align-middle", "data": "Descripcion" },
            { "class": "align-middle ls1 fw5", "data": "HsAuto" },
            { "class": "align-middle ls1", "data": "HsHechas" },
            { "class": "align-middle", "data": "editar" },
            { "class": "align-middle", "data": "eliminar" },
            // { "class": "align-middle ls1 text-center", "data": "Motivo" },
            { "class": "align-middle", "data": "DescMotivo" },
            { "class": "align-middle", "data": "Observ" },
            { "class": "align-middle w-100", "data": "null" },

        ],
        paging: false,
        // scrollY: '100px',
        scrollX: false,
        scrollCollapse: false,
        searching: false,
        info: false,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanish.json"
        },
    });
    $('#GetOtrasNov').DataTable({
        "drawCallback": function (settings) {
            $.each(settings.json, function (key, value) {
                (value.length > 0) ? $("#CantONov").html("(" + value.length + ")") : $("#CantONov").html("");
            });
        },
        bProcessing: true,
        deferRender: true,
        "ajax": {
            url: "GetOtrasNovedades.php",
            type: "GET",
            dataSrc: "ONovedades",
            'data': {
                Datos
            },
        },

        columns: [
            { "class": "align-middle ls1 text-center", "data": "Cod" },
            { "class": "align-middle", "data": "Descripcion" },
            { "class": "align-middle ls1 text-right fw5", "data": "FicValor" },
            { "class": "align-middle", "data": "Observ" },
            { "class": "align-middle", "data": "editar" },
            { "class": "align-middle", "data": "eliminar" },
            { "class": "align-middle", "data": "Tipo" },
            { "class": "align-middle w-100", "data": "null" }
        ],
        paging: false,
        scrollX: false,
        scrollCollapse: false,
        searching: false,
        info: false,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanish.json"
        },
    });
    var optSelect2 = {
        MinLength: 0,
        SelClose: 0,
        MaxInpLength: 0,
        delay: 250
    }
    $(".selectjs_Novedades").select2({
        dropdownAutoHeight: true,
        multiple: false,
        language: 'es',
        allowClear: true,
        placeholder: "Seleccionar Novedad",
        minimumInputLength: optSelect2.MinLength,
        minimumResultsForSearch: 10,
        maximumInputLength: optSelect2.MaxInpLength,
        selectOnClose: optSelect2.SelClose,
        language: {
            noResults: function () {
                return 'No hay resultados..'
            },
            inputTooLong: function (args) {
                var message = 'Máximo ' + optSelect2.MaxInpLength + ' caracteres. Elimine ' + overChars + ' caracter';
                if (overChars != 1) {
                    message += 'es'
                }
                return message
            },
            searching: function () {
                return 'Buscando..'
            },
            errorLoading: function () {
                return 'Sin datos..'
            },
            inputTooShort: function () {
                return 'Ingresar ' + optSelect2.MinLength + ' o mas caracteres'
            },
            maximumSelected: function () {
                return 'Puede seleccionar solo una opción'
            },
            removeAllItems: function () {
                return "Borrar"
            }
        },
        ajax: {
            url: "../data/getListNovedades.php",
            dataType: "json",
            type: "POST",
            delay: optSelect2.delay,
            data: function (params) {
                return {
                    q: params.term,
                    // Fic: true,
                    _nt: $("#novTipo").val(),
                    _nc: $("#novCate").val(),
                    Datos,
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    })
    $('.selectjs_Novedades').on('select2:select', function (e) {
        $('#FicNove').val($(this).val())
        // console.log($('#FicNove').val())
        $('.selectjs_NoveCausa').val(null).trigger('change');
        $("#FicHoras").focus();
        if ($("#FicHoras").val() != '') {
            $('#FicHoras').select();
        }
    });
    $(".selectjs_Novedades").on("select2:unselecting", function (e) {
        $('.selectjs_NoveCausa').val(null).trigger('change');
    });
    $(".selectjs_NoveCausa").select2({
        multiple: false,
        language: "es",
        allowClear: true,
        placeholder: "Seleccionar Causa",
        minimumInputLength: optSelect2.MinLength,
        minimumResultsForSearch: 10,
        maximumInputLength: optSelect2.MaxInpLength,
        selectOnClose: optSelect2.SelClose,
        language: {
            noResults: function () {
                return 'No hay resultados..'
            },
            inputTooLong: function (args) {
                var message = 'Máximo ' + optSelect2.MaxInpLength + ' caracteres. Elimine ' + overChars + ' caracter';
                if (overChars != 1) {
                    message += 'es'
                }
                return message
            },
            searching: function () {
                return 'Buscando..'
            },
            errorLoading: function () {
                return 'Sin datos..'
            },
            inputTooShort: function () {
                return 'Ingresar ' + optSelect2.MinLength + ' o mas caracteres'
            },
            maximumSelected: function () {
                return 'Puede seleccionar solo una opción'
            },
            removeAllItems: function () {
                return "Borrar"
            }
        },
        ajax: {
            url: "../data/getListNoveCausa.php",
            dataType: "json",
            type: "POST",
            delay: optSelect2.delay,
            data: function (params, page) {
                return {
                    q: params.term,
                    NovCNove: $("#FicNove").val(),
                    // NovCNove: 'data',
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    })
    $(".selectjs_TipoHora").select2({
        multiple: false,
        language: "es",
        placeholder: "Seleccionar Tipo Hora",
        minimumInputLength: optSelect2.MinLength,
        minimumResultsForSearch: 10,
        maximumInputLength: optSelect2.MaxInpLength,
        selectOnClose: optSelect2.SelClose,
        language: {
            noResults: function () {
                return 'No hay resultados..'
            },
            inputTooLong: function (args) {
                var message = 'Máximo ' + optSelect2.MaxInpLength + ' caracteres. Elimine ' + overChars + ' caracter';
                if (overChars != 1) {
                    message += 'es'
                }
                return message
            },
            searching: function () {
                return 'Buscando..'
            },
            errorLoading: function () {
                return 'Sin datos..'
            },
            inputTooShort: function () {
                return 'Ingresar ' + optSelect2.MinLength + ' o mas caracteres'
            },
            maximumSelected: function () {
                return 'Puede seleccionar solo una opción'
            },
            removeAllItems: function () {
                return "Borrar"
            }
        },
        ajax: {
            url: "../data/getListTipoHora.php",
            dataType: "json",
            type: "POST",
            delay: optSelect2.delay,
            data: function (params, page) {
                return {
                    q: params.term,
                    Datos,
                    modHora: $("#modHora").val(),
                    THoCodi: $(".selectjs_TipoHora").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    })
    $(".selectjs_MotivoHora").select2({
        multiple: false,
        language: "es",
        allowClear: true,
        placeholder: "Seleccionar Motivo",
        minimumInputLength: optSelect2.MinLength,
        minimumResultsForSearch: 10,
        maximumInputLength: optSelect2.MaxInpLength,
        selectOnClose: optSelect2.SelClose,
        language: {
            noResults: function () {
                return 'No hay resultados..'
            },
            inputTooLong: function (args) {
                var message = 'Máximo ' + optSelect2.MaxInpLength + ' caracteres. Elimine ' + overChars + ' caracter';
                if (overChars != 1) {
                    message += 'es'
                }
                return message
            },
            searching: function () {
                return 'Buscando..'
            },
            errorLoading: function () {
                return 'Sin datos..'
            },
            inputTooShort: function () {
                return 'Ingresar ' + optSelect2.MinLength + ' o mas caracteres'
            },
            maximumSelected: function () {
                return 'Puede seleccionar solo una opción'
            },
            removeAllItems: function () {
                return "Borrar"
            }
        },
        ajax: {
            url: "../data/getListMotivoHora.php",
            dataType: "json",
            type: "POST",
            delay: optSelect2.delay,
            data: function (params, page) {
                return {
                    q: params.term,
                    Fic1Hora: $("#Fic1Hora").val(),
                    // NovCNove: 'data',
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    })
    $('.selectjs_TipoHora').on('select2:select', function (e) {
        $('#Fic1Hora').val($(this).val())
        $('.selectjs_MotivoHora').val(null).trigger('change');
        $("#Fic1HsAu2").focus();
        if ($("#Fic1HsAu2").val() != '') {
            $('#Fic1HsAu2').select();
        }
    });
    $(".selectjs_TipoHora").on("select2:unselecting", function (e) {
        $('.selectjs_MotivoHora').val(null).trigger('change');
    });
    $(".selectjs_OtrasNovedades").select2({
        dropdownAutoHeight: true,
        multiple: false,
        language: 'es',
        allowClear: true,
        placeholder: "Seleccionar Novedad",
        minimumInputLength: optSelect2.MinLength,
        minimumResultsForSearch: 10,
        maximumInputLength: optSelect2.MaxInpLength,
        selectOnClose: optSelect2.SelClose,
        language: {
            noResults: function () {
                return 'No hay resultados..'
            },
            inputTooLong: function (args) {
                var message = 'Máximo ' + optSelect2.MaxInpLength + ' caracteres. Elimine ' + overChars + ' caracter';
                if (overChars != 1) {
                    message += 'es'
                }
                return message
            },
            searching: function () {
                return 'Buscando..'
            },
            errorLoading: function () {
                return 'Sin datos..'
            },
            inputTooShort: function () {
                return 'Ingresar ' + optSelect2.MinLength + ' o mas caracteres'
            },
            maximumSelected: function () {
                return 'Puede seleccionar solo una opción'
            },
            removeAllItems: function () {
                return "Borrar"
            }
        },
        ajax: {
            url: "../data/getListOtrasNovedades.php",
            dataType: "json",
            type: "POST",
            delay: optSelect2.delay,
            data: function (params) {
                return {
                    q: params.term,
                    Datos,
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    })
    $('.selectjs_OtrasNovedades').on('select2:select', function (e) {
        $("#FicValor").focus();
        if ($("#FicValor").val() != '') {
            $('#FicValor').select();
        }
    });

    function CierraModalGeneral() {
        $("#CierraModalGeneral").prop("disabled", true)
        $("#CierraModalGeneral").html("Procesando")
        $("#ProcesarLegajo").prop("disabled", true)
        $("#ProcesarLegajo").html("Procesando")
    }
    function DisabledClean() {
        $("#ProcesarLegajo").attr("disabled", false);
        $("#ProcesarLegajo").html("Procesar");
        $("#CierraModalGeneral").prop("disabled", false)
        $("#CierraModalGeneral").html("Cerrar")
        $(".respuesta_fichada").html("");
        $(".submit_btn").prop("disabled", false);
        $(".submit_btn_mod").prop("disabled", false);
        $(".respuesta_novedad").html('');
    }
    function RefreshDataTables() {
        DisabledClean();
        $('#GetFichadas').DataTable().ajax.reload(null, false);
        $('#GetNovedades').DataTable().ajax.reload(null, false);
        $('#GetHoras').DataTable().ajax.reload(null, false);
        $('#GetOtrasNov').DataTable().ajax.reload(null, false);
        ActualizaTablas();
        GetCierre();
        // refrescaFichas();
        // GetCitacion();
    };
    $('#RefreshModal').click(function (e) {
        // e.preventDefault();
        RefreshDataTables();
        // e.stopImmediatePropagation();
    });
    /** ALTA Y MOD FICHADA */
    $(".Form_Fichadas").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            // async : false,
            beforeSend: function (data) {
                CierraModalGeneral()
                $.notifyClose();
                notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                $(".respuesta_fichada").html("Procesando.!");
                $(".submit_btn").prop("disabled", true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    RefreshDataTables();
                    DisabledClean();
                    $(".RegHora").val('')
                    $(".Form_Fichadas").addClass('d-none')
                    ClearFormFic();
                    $.notifyClose();
                    notify('Fichada creada.<br>' + data.dato, 'success', 5000, 'right')
                } else {
                    DisabledClean();
                    RefreshDataTables();
                    ClearFormFic();
                    $.notifyClose();
                    notify(data.dato, 'danger', 5000, 'right')
                }
            }
        });
        e.stopImmediatePropagation();
    });
    $(document).on("click", ".mod_Fic", function (e) {
        OcultaNavTab()
        $(".Form_Fichadas_Mod").removeClass('d-none')
        $(".Form_Fichadas_Mod").addClass('animate__animated animate__fadeIn')
        var Hora = $(this).attr('data2');
        var Fecha = $(this).attr('data3');
        var Datos = $(this).attr('data');
        $("#datos_fichada_mod").val(Datos).trigger('change');
        $("#RegFech_mod").val(Fecha).trigger('change');
        $("#RegHora_mod").val(Hora).trigger('change');
        $(".submit_btn").prop("disabled", false);
        $('#RegHora_mod').select();
        $("#xsTFic").html('Modificar Fichada')
    });
    $(".Form_Fichadas_Mod").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            cache: false,
            beforeSend: function (data) {
                $(".submit_btn").prop('disabled', true);
                CierraModalGeneral()
                $(".submit_btn").prop("disabled", true);
                $.notifyClose();
                notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
            },
            success: function (data) {
                if (data.status == "ok") {
                    $(".submit_btn").prop('disabled', false);
                    RefreshDataTables();
                    DisabledClean();
                    $("#RegHora_mod").val('');
                    $(".Form_Fichadas_Mod").addClass('d-none');
                    ClearFormFic();
                    $.notifyClose();
                    notify('Fichada modificada.<br>' + data.dato, 'success', 5000, 'right')
                } else {
                    DisabledClean();
                    ClearFormFic();
                    $(".submit_btn").prop('disabled', false);
                    $.notifyClose();
                    notify(data.dato, 'danger', 5000, 'right')
                }
            }
        });
        e.stopImmediatePropagation();
    });
    /** BAJA FICHADA */
    $(document).on('click', '.baja_Fic', function (e) {
        e.preventDefault();
        var RegHora = $(this).attr('data2');
        var Datos = $(this).attr('data');

        bootbox.confirm({
            message: '<span class="fonth fw5">Eliminar Fichada</span><br><span class="fontq fw4">¿Confirma eliminar la Fichada: ' + RegHora + 'Hs.?</span>',
            // message: '',
            buttons: {
                confirm: {
                    label: 'Aceptar',
                    className: 'btn-custom text-white btn-sm fontq'
                },
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-light btn-sm fontq text-secondary'
                }
            },
            callback: function (result) {
                $('.baja_Fic').unbind('click');
                if (result) {
                    $.ajax({
                        type: "POST",
                        url: "insert.php",
                        'data': {
                            baja_fichada: true,
                            Datos
                        },
                        beforeSend: function (data) {
                            $(".baja_Fic").addClass('d-none')
                            $.notifyClose();
                            notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                            CierraModalGeneral()
                        },
                        success: function (data) {
                            if (data.status == "ok") {
                                DisabledClean();
                                $(".baja_Fic").removeClass('d-none')
                                RefreshDataTables();
                                $(".Form_Fichadas").addClass('d-none')
                                $(".respuesta_baja_fichada").addClass('d-none')
                                $.notifyClose();
                                notify('Fichada Eliminada<br>' + data.dato, 'success', 5000, 'right')
                            } else {
                                DisabledClean();
                                $(".baja_Fic").removeClass('d-none')
                                $(".respuesta_baja_fichada").removeClass('d-none')
                                $.notifyClose();
                                notify(data.dato, 'danger', 5000, 'right')
                            }
                        }
                    });
                }
            }
        });
        e.stopImmediatePropagation();
    });
    /** ALTA, MOD, BAJA NOVEDADES */
    $(document).ready(function () {
        $(".Form_Novedad").bind("submit", function (e) {
            e.preventDefault();
            $.ajax({
                type: $(this).attr("method"),
                // contetnType: "application_json; charset=utf-8",
                url: $(this).attr("action"),
                data: $(this).serialize(),
                beforeSend: function (data) {
                    $.notifyClose();
                    notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                    CierraModalGeneral()
                    $(".submit_btn_mod").prop("disabled", true);
                },
                success: function (data) {
                    if (data.status == "ok") {
                        DisabledClean();
                        $(".Form_Novedad").addClass('d-none')
                        /** refresh datatable */
                        RefreshDataTables();
                        /** vaciamos el form */
                        ClearFormNov()
                        /** Notificación */
                        if (data.tipo == 'mod') {
                            var Textsuccess = 'Novedad modificada.';
                        } else {
                            var Textsuccess = 'Novedad creada.';
                        }
                        $.notifyClose();
                        notify(Textsuccess + '<br>' + data.dato, 'success', 5000, 'right')
                    } else {
                        DisabledClean();
                        $.notifyClose();
                        notify(data.dato, 'danger', 5000, 'right')
                    }
                }
            });
            e.stopImmediatePropagation();
        });
        $(document).on("click", "#FicCate", function (e) {
            if ($('#FicCate').is(':checked')) {
                $('#novCate').val('1').trigger('change');
            } else {
                $('#novCate').val('2').trigger('change');
                $('.selectjs_Novedades').val(null).trigger("change");
                $('.selectjs_NoveCausa').val(null).trigger("change");
            }
        });
        $(document).on("click", ".mod_Nov", function (e) {
            $("#xsTNov").html('Modificar Novedad')
            // ClearFormNov();
            OcultaNavTab()
            $(".submit_btn_mod").prop("disabled", false);
            $(".Form_Novedad").removeClass('d-none')
            $(".Form_Novedad").addClass('animate__animated animate__fadeIn')
            $(".submit_btn_mod").html('Modificar');
            $('#FicHoras').select();

            var DatosNov = $(this).attr('data'); /** Cod Nov, Tipo y Categoria */
            var nov_novedad = $(this).attr('data2');
            var nov_descripcion = $(this).attr('data3');
            var CodCaus = $(this).attr('data4');
            var DescCausa = $(this).attr('data5');
            var Obserb = $(this).attr('data6');
            var FicJust = $(this).attr('data7');
            var FicCate = $(this).attr('data8');
            var nov_horas = $(this).attr('data9');
            var novTipo = $(this).attr('data10');

            $('#novTipo').val(novTipo).trigger('change');

            if (FicJust == 1) {
                $("#FicJust").prop('checked', true);
            }
            if (FicCate == 2) {
                $("#FicCate").prop('checked', true);
                $("#FicCate").prop('disabled', true);
            }

            var newOption = new Option(nov_descripcion, nov_novedad, true, true);
            $('.selectjs_Novedades').append(newOption).trigger('change');
            if (CodCaus != 0) {
                var newOption = new Option(DescCausa, CodCaus, true, true);
                $('.selectjs_NoveCausa').append(newOption).trigger('change');
            }
            $("#FicObse").val(Obserb).trigger('change');
            $("#alta_novedad").val("Mod")
            $("#CNove").val(DatosNov).trigger('change');
            $("#FicHoras").val(nov_horas).trigger('change');
        });
        /** BAJA NOVEDAD */
        $(document).on('click', '.baja_Nov', function (e) {
            e.preventDefault();
            var NovDes = $(this).attr('data2');
            var Datos = $(this).attr('data'); /** FicNov, FicFech, FicLega */
            bootbox.confirm({
                message: '<span class="fonth fw5">Eliminar Novedad</span><br><span class="fontq fw4">¿Confirma eliminar la Novedad: ' + NovDes + 'Hs.?</span>',
                // message: '',
                buttons: {
                    confirm: {
                        label: 'Aceptar',
                        className: 'btn-custom text-white btn-sm fontq'
                    },
                    cancel: {
                        label: 'Cancelar',
                        className: 'btn-light btn-sm fontq text-secondary'
                    }
                },
                callback: function (result) {
                    $('.baja_Nov').unbind('click');
                    if (result) {
                        $.ajax({
                            type: "POST",
                            url: "insert.php",
                            'data': {
                                baja_novedad: true,
                                Datos,
                                NovDes
                            },
                            beforeSend: function (data) {
                                $(".baja_Nov").addClass('d-none')
                                $.notifyClose();
                                notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                                CierraModalGeneral()
                            },

                            success: function (data) {
                                if (data.status == "ok") {
                                    DisabledClean();
                                    $(".Form_Novedad").addClass('d-none')
                                    /** refresh datatable */
                                    RefreshDataTables();
                                    $.notifyClose();
                                    notify('Novedad eliminada<br>' + data.dato, 'success', 5000, 'right')
                                } else {
                                    DisabledClean();
                                    RefreshDataTables();
                                    $.notifyClose();
                                    notify(data.dato, 'danger', 5000, 'right')
                                }
                            }
                        });
                    }
                }
            });
            e.stopImmediatePropagation()
        });
        // });
    });
    /** ALTA, MOD, BAJA HORA */
    $(".Form_Horas").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            // contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $.notifyClose();
                notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                CierraModalGeneral()
                $(".submit_btn_HorMod").prop("disabled", true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    DisabledClean();
                    /** vaciamos el form */
                    ClearFormHora()
                    /** refresh datatable */
                    RefreshDataTables();
                    /** Notificación */
                    if (data.tipo == 'mod') {
                        var Textsuccess = 'Hora modificada.';
                    } else {
                        var Textsuccess = 'Hora cargada.';
                    }
                    $.notifyClose();
                    notify(Textsuccess + '<br>' + data.dato, 'success', 5000, 'right')
                    /** refresh datatable */
                } else {
                    DisabledClean();
                    $(".submit_btn_HorMod").prop("disabled", false);
                    /** refresh datatable */
                    RefreshDataTables();
                    $(".respuesta_Horas").html("");
                    $.notifyClose();
                    notify(data.dato, 'danger', 5000, 'right')
                }
            }
        });
        e.stopImmediatePropagation();
    });

    $(document).on("click", ".mod_hora", function (e) {
        e.preventDefault();
        $("#xsTHor").html('Modificar Horas')
        $(".Form_Horas").removeClass('d-none')
        $(".Form_Horas").addClass('animate__animated animate__fadeIn')
        $(".submit_btn_HorMod").html('Modificar');
        $("#alta_horas").val("mod").trigger('change');
        $("#modHora").val("1").trigger('change');
        $(".submit_btn_HorMod").prop("disabled", false);

        var FicHora = $(this).attr('data');
        var FicHsAu2 = $(this).attr('data2');
        var HoraDesc = $(this).attr('data3');
        var Motivo = $(this).attr('data4');
        var DescMotivo = $(this).attr('data5');
        var Observ = $(this).attr('data6');

        var newOption = new Option(HoraDesc, FicHora, true, true);
        $('.selectjs_TipoHora').append(newOption).trigger('change');

        if (Motivo != 0) {
            var newOption = new Option(DescMotivo, Motivo, true, true);
            $('.selectjs_MotivoHora').append(newOption).trigger('change');
        }
        $("#Fic1Observ").val(Observ).trigger('change');
        $("#Fic1HsAu2").val(FicHsAu2).trigger('change');

        $("#Fic1HsAu2").focus();
        $('#Fic1HsAu2').select();

    });
    /** BAJA HORA */
    $(document).on('click', '.baja_Hora', function (e) {
        e.preventDefault();
        var HoraDesc = $(this).attr('data2');
        var Datos = $(this).attr('data'); /** FicHora, FicFech, FicLega */
        bootbox.confirm({
            message: '<span class="fonth fw5">Eliminar Hora</span><br><span class="fontq fw4">¿Confirma eliminar la Hora: ' + HoraDesc + 'Hs.?</span>',
            // message: '',
            buttons: {
                confirm: {
                    label: 'Aceptar',
                    className: 'btn-custom text-white btn-sm fontq'
                },
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-light btn-sm fontq text-secondary'
                }
            },
            callback: function (result) {
                $('.baja_Hora').unbind('click');
                if (result) {
                    $.ajax({
                        type: "POST",
                        url: "insert.php",
                        'data': {
                            baja_Hora: true,
                            Datos,
                            HoraDesc
                        },
                        beforeSend: function (data) {
                            $.notifyClose();
                            notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                            $(".baja_Hora").addClass('d-none')
                            CierraModalGeneral()
                        },
                        success: function (data) {
                            if (data.status == "ok") {
                                DisabledClean();
                                $(".Form_Horas").addClass('d-none')
                                $(".baja_Hora").removeClass('d-none')
                                /** refresh datatable */
                                RefreshDataTables();
                                $.notifyClose();
                                notify('Hora eliminada<br>' + data.dato, 'success', 5000, 'right')
                            } else {
                                DisabledClean();
                                RefreshDataTables();;
                                $.notifyClose();
                                notify(data.dato, 'danger', 5000, 'right')
                            }
                        }
                    });
                }
            }
        });
        e.stopImmediatePropagation();
    });
    // });
    /** ALTA, MOD, BAJA OTRAS NOVEDADES */
    $(".Form_OtraNovedad").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            // contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $.notifyClose();
                notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                CierraModalGeneral()
                $(".submit_btn_OtrasNov").prop("disabled", true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    DisabledClean();
                    /** vaciamos el form */
                    ClearFormONov()
                    /** refresh datatable */
                    RefreshDataTables();
                    /** Notificación */
                    if (data.tipo == 'mod') {
                        var Textsuccess = 'Novedad modificada.';
                    } else {
                        var Textsuccess = 'Novedad creada.';
                    }
                    $.notifyClose();
                    notify(Textsuccess + '<br>' + data.dato, 'success', 5000, 'right')
                } else {
                    $(".submit_btn_OtrasNov").prop("disabled", false);
                    $(".respuesta_OtrasNov").html("");
                    DisabledClean();
                    RefreshDataTables();
                    $.notifyClose();
                    notify(data.dato, 'danger', 5000, 'right')
                }
            }
        });
        e.stopImmediatePropagation();
    });
    $(document).on("click", ".mod_ONov", function (e) {
        e.preventDefault();
        ClearFormONov();
        $("#xsTOnov").html('Modificar Novedad')
        $(".submit_btn_OtrasNov").prop("disabled", false);
        $(".Form_OtraNovedad").removeClass('d-none')
        $(".Form_OtraNovedad").addClass('animate__animated animate__fadeIn')
        $(".submit_btn_OtrasNov").html('Modificar');
        $("#alta_OtrasNov").val("mod").trigger('change');
        // var DatosONov = $(this).attr('data'); //** FicOnov, FicFech, FicLega */
        var Descrip = $(this).attr('data1');
        var FicObsN = $(this).attr('data2');
        var FicValor = $(this).attr('data3');
        var FicONov = $(this).attr('data4');
        var newOption = new Option(Descrip, FicONov, true, true);
        $('.selectjs_OtrasNovedades').append(newOption).trigger('change');
        $("#FicValor").val(FicValor).trigger('change');
        $("#FicObsN").val(FicObsN).trigger('change');
        $("#FicValor").focus();
        if ($("#FicValor").val() != '') {
            $('#FicValor').select();
        }
        e.stopImmediatePropagation();
    });
    /** BAJA OTRA NOVEDAD */
    $(document).on('click', '.baja_ONov', function (e) {
        e.preventDefault();
        var Descrip = $(this).attr('data2');
        var Datos = $(this).attr('data'); /** FicNov, FicFech, FicLega */
        bootbox.confirm({
            message: '<span class="fonth fw5">Eliminar Novedad</span><br><span class="fontq fw4">¿Confirma eliminar la Novedad: ' + Descrip + '?</span>',
            // message: '',
            buttons: {
                confirm: {
                    label: 'Aceptar',
                    className: 'btn-custom text-white btn-sm fontq'
                },
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-light btn-sm fontq text-secondary'
                }
            },
            callback: function (result) {
                if (result) {
                    $.ajax({
                        type: "POST",
                        url: "insert.php",
                        'data': {
                            baja_ONov: true,
                            Datos,
                            Descrip
                        },
                        beforeSend: function (data) {
                            CierraModalGeneral()
                            $.notifyClose();
                            notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                        },
                        success: function (data) {
                            if (data.status == "ok") {
                                DisabledClean();
                                $(".respuesta_OtrasNov").html('');
                                $.notifyClose();
                                notify('Novedad eliminada<br>' + data.dato, 'success', 5000, 'right')
                                /** refresh datatable */
                                RefreshDataTables();
                            } else {
                                DisabledClean();
                                RefreshDataTables();
                                $.notifyClose();
                                notify(data.dato, 'danger', 5000, 'right')
                            }
                        }
                    });
                }
            }
        });
        // e.stopImmediatePropagation();
    });
    /** ALTA, CITACION */
    $(".Form_Citacion").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $.notifyClose();
                notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                $(".submit_btn_Citación").prop("disabled", true);
                CierraModalGeneral()
            },
            success: function (data) {
                if (data.status == "ok") {
                    DisabledClean();
                    /** vaciamos el form */
                    ClearFormCitacion()
                    /** refresh datatable */
                    RefreshDataTables();
                    /** Notificación */
                    var Textsuccess = (data.tipo == 'mod') ? 'Citación Modificada.' : 'Citación Creada.';
                    $.notifyClose();
                    notify(Textsuccess + '<br>' + data.dato, 'success', 5000, 'right')
                } else {
                    DisabledClean();
                    $(".submit_btn_Citación").prop("disabled", false);
                    RefreshDataTables();
                    $.notifyClose();
                    notify(data.dato, 'danger', 5000, 'right')
                }
            }
        });

    });
    /** Al hace click en boton + agregar Citación modal general*/
    $(document).ready(function () {
        $("#Citacion").on("click", function (e) {
            GetCitacion()
            e.preventDefault();
            $("#rowCitacion").removeClass('d-none')
            $("#rowCitacion").addClass('animate__animated animate__fadeIn')
            $("#alta_Citación").val("true").trigger('change');
            e.stopImmediatePropagation()
            $('#CitEntra').focus();
            if ($("#CitEntra").val() != '') {
                $('#CitEntra').select();
            }
            // $('#Citacion').off('click');
        });
        if (Cita) {
            GetCitacion()
            $("#rowCitacion").removeClass('d-none')
            $("#rowCitacion").addClass('animate__animated animate__fadeIn')
            $("#alta_Citación").val("true").trigger('change');
            $('#Navs').addClass('d-none')
            Modal_XL_LG('#TopN')
            setTimeout(function () {
                $('#CitEntra').focus();
                if ($("#CitEntra").val() != '') {
                    $('#CitEntra').select();
                }
            }, 500);
        }
    });
    $('#Fichadas-tab').on('hide.bs.tab', function (e) {
        $("#AddFic").addClass('d-none')
        ClearFormFic();
    });
    $('#Fichadas-tab').on('shown.bs.tab', function (e) {
        $("#AddFic").removeClass('d-none')
    });
    $('#Novedades-tab').on('hide.bs.tab', function (e) {
        $("#AddNov").addClass('d-none')
        ClearFormNov();
    });
    $('#Novedades-tab').on('shown.bs.tab', function (e) {
        $("#AddNov").removeClass('d-none')
        $('#novCate').val('2').trigger('change');
    });
    $('#Horas-tab').on('hide.bs.tab', function (e) {
        $("#AddHora").addClass('d-none')
        ClearFormHora();
    });
    $('#Horas-tab').on('shown.bs.tab', function (e) {
        $("#AddHora").removeClass('d-none')
    });
    $('#OtrasNov-tab').on('show.bs.tab', function (e) {
        $("#AddONov").removeClass('d-none');
    });
    $('#OtrasNov-tab').on('hide.bs.tab', function (e) {
        $("#AddONov").addClass('d-none')
        ClearFormONov();
    });
});
// });
/** CIERRA MODAL */
$('#modalGeneral').on('hidden.bs.modal', function () {
    DestroyDataTablesModal();
    ClearFormFic();
    Modal_LG_XL('#TopN')
    $("#FicHsAT").html("");
    $("#TextFicHsAT").html('');
    $("#divHorasTR").addClass("d-none");
    $("#FicHorario").html('');
    $('#ProgressHoras').html('');
    $("#FicHsTr").html("");
    $("#TextFicHsTr").html('');
    $('#Navs').removeClass('d-none')
    $('.nav').removeClass('d-none')
    $('#RefreshModal').off('click');
    $('#AddFic').off('click');
    $('#AddNov').off('click');
    $('#AddHora').off('click');
    $('#AddONov').off('click');
    $('#ProcesarLegajo').off('click');
    $('#ProcesarLegajo').unbind('click');
    $('.cancelar_btn_fic').off('click');
    $('.cancelar_btn_nov').off('click');
    $('#cancelar_btn_hor').off('click');
    $('#cancelar_btn_OtrasNov').off('click');
    $('.mod_Fic').off('click');
    $('.baja_ONov').off('click');
    $('#FicCate').off('click');
    $('#Citacion').off('click');
    $('#Citacion').unbind('click');
    $('.mod_Nov').off('click');
    $('.mod_hora').off('click');
    $('.mod_ONov').off('click');
    $('.Form_Citacion').unbind('submit');
    $('#cancelar_btn_Citación').off('click');
    ClearFormCitacion()
    $(document).off('click', '.mod_Fic');
    $(document).off('click', '.baja_Fic');
    $(document).off('click', '.mod_hora');
    $(document).off('click', '.baja_Hora');
    $(document).off('click', '.mod_Nov');
    $(document).off('click', '.baja_Nov');
    $(document).off('click', '.mod_ONov');
    $(document).off('click', '.baja_ONov');
    //$(document).off('click','#cancelar_btn_Citación');
    // $('#Citacion').stopImmediatePropagation()
    $('#nav-tabContent').removeClass('border')
    $('#Fichadas').addClass('border border-top-0')
    $('#Fichadas').removeClass('d-none')
    $('#Novedades').removeClass('d-none')
    $('#Horas').removeClass('d-none')
    $('#OtrasNov').removeClass('d-none')
    $("#CitEntra").val('');
    $("#CitSale").val('');
    $("#CitDesc").val('00:00');
    $("#Mxs").val('')
    MuestraNavTab();
    $("#xsTFic").html('Fichadas')
    $("#xsTNov").html('Novedades')
    $("#xsTHor").html('Horas')
    $("#xsTOnov").html('Otras Novedades')
    $('.navbar').removeClass('mr-0');
    $("#nav-tab").show()
});