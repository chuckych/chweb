// $(".Filtros").prop('disabled', true);
function ActualizaTablas() {
    $('.modal-footer .result').html('');
    if ($("#Visualizar").is(":checked")) {
        $('#GetFechas').DataTable().ajax.reload(null, false);
        $("#GetFechas_paginate .page-link").addClass('border border-0');
    } else {
        $('#GetPersonal').DataTable().ajax.reload(null, false);
        $('#Per2').addClass('d-none')
        $('.pers_legajo').removeClass('d-none')
    };
};
function ActualizaTablas2() {
    $('.modal-footer .result').html('');
    if ($("#Visualizar").is(":checked")) {
        $('#GetFechas').DataTable().ajax.reload();
        $("#GetFechas_paginate .page-link").addClass('border border-0');
    } else {
        $('#GetPersonal').DataTable().ajax.reload();
        $('#Per2').addClass('d-none')
        $('.pers_legajo').removeClass('d-none')
    };
};
function atajosTeclado() {
    let map = { 17: false, 18: false, 32: false, 16: false, 39: false, 37: false, 13: false, 27: false };
    $(document).keydown(function (e) {
        if (e.keyCode in map) {
            map[e.keyCode] = true;
            if (map[32]) { /** Barra espaciadora */
                if (!$('#Exportar').hasClass('show')) {
                    $('#Filtros').modal('show');
                }
            }
        }
        if (e.keyCode in map) {
            map[e.keyCode] = true;
            if (map[27]) { /** Esc */
                $('#Filtros').modal('hide');
            }
        }
        if (e.keyCode in map) {
            map[e.keyCode] = true;
            if (map[39]) { /** Flecha derecha */
                if ($("#Visualizar").is(":checked")) {
                    $('#GetFechas').DataTable().page('next').draw('page');
                } else {
                    $('#GetPersonal').DataTable().page('next').draw('page');
                };
            }
        }
        if (e.keyCode in map) {
            map[e.keyCode] = true;
            if (map[37]) { /** Flecha izquierda */
                if ($("#Visualizar").is(":checked")) {
                    $('#GetFechas').DataTable().page('previous').draw('page');
                } else {
                    $('#GetPersonal').DataTable().page('previous').draw('page');
                };

            }
        }
    }).keyup(function (e) {
        if (e.keyCode in map) {
            map[e.keyCode] = false;
        }
    });
}

$("#pagFech").hide()
$("#GetGeneralFechaTable").hide();

$('#datoFicFalta').val('0');
$("#FicFalta").change(function () {
    if ($("#FicFalta").is(":checked")) {
        $('#datoFicFalta').val('1')
        $('#datoFicNovA').val('0')
        $('#FicNovA').prop('checked', false)
        $('#FicNovA').prop('disabled', true)
        ActualizaTablas2()
    } else {
        $('#datoFicFalta').val('0')
        $('#FicNovA').prop('disabled', false)
        ActualizaTablas2()
    }
});
$('#datoFicNovT').val('0');
$("#FicNovT").change(function () {
    if ($("#FicNovT").is(":checked")) {
        $('#datoFicNovT').val('1')
        $('#datoFicNovA').val('0')
        $('#FicNovA').prop('checked', false)
        $('#FicNovA').prop('disabled', true)
        ActualizaTablas2()
        $('#datoNovedad').val(null).trigger("change");
    } else {
        $('#datoNovedad').val(null).trigger("change");
        $('#datoFicNovT').val('0')
        $('#FicNovA').prop('disabled', false)
        ActualizaTablas2()
    }
});
$('#datoFicNovI').val('0');
$("#FicNovI").change(function () {
    if ($("#FicNovI").is(":checked")) {
        $('#datoNovedad').val(null).trigger("change");
        $('#datoFicNovI').val('1')
        $('#datoFicNovA').val('0')
        $('#FicNovA').prop('checked', false)
        $('#FicNovA').prop('disabled', true)
        ActualizaTablas2()
    } else {
        $('#datoNovedad').val(null).trigger("change");
        $('#datoFicNovI').val('0')
        $('#FicNovA').prop('disabled', false)
        ActualizaTablas2()
    }
});
$('#datoFicNovS').val('0');
$("#FicNovS").change(function () {
    if ($("#FicNovS").is(":checked")) {
        $('#datoNovedad').val(null).trigger("change");
        $('#datoFicNovS').val('1')
        $('#datoFicNovA').val('0')
        $('#FicNovA').prop('checked', false)
        $('#FicNovA').prop('disabled', true)
        ActualizaTablas2()
    } else {
        $('#datoNovedad').val(null).trigger("change");
        $('#datoFicNovS').val('0')
        $('#FicNovA').prop('disabled', false)
        ActualizaTablas2()
    }
});
$('#datoFicNovA').val('0');
$("#FicNovA").change(function () {
    if ($("#FicNovA").is(":checked")) {
        $('#datoNovedad').val(null).trigger("change");
        $('#datoFicNovA').val('1')
        $('#datoFicFalta').val('0')
        $('#datoFicNovT').val('0')
        $('#datoFicNovI').val('0')
        $('#datoFicNovS').val('0')
        $('#FicNovI').prop('checked', false)
        $('#FicFalta').prop('checked', false)
        $('#FicNovT').prop('checked', false)
        $('#FicNovS').prop('checked', false)
        $('#FicFalta').prop('disabled', true)
        $('#FicNovT').prop('disabled', true)
        $('#FicNovI').prop('disabled', true)
        $('#FicNovS').prop('disabled', true)
        ActualizaTablas2()
    } else {
        $('#datoNovedad').val(null).trigger("change");
        $('#datoFicNovA').val('0')
        $('#FicFalta').prop('disabled', false)
        $('#FicNovT').prop('disabled', false)
        $('#FicNovI').prop('disabled', false)
        $('#FicNovS').prop('disabled', false)
        ActualizaTablas2()
    }
});

function _Filtros() {
    let LegDe = parseInt($('#LegDe').val());
    let LegHa = parseInt($('#LegHa').val());
    if ((LegDe && LegHa)) {
        (LegDe > LegHa) ? $('#LegDe').val(LegHa) : $('#LegDe').val(LegDe);
    }
    let SoloFic = $('#SoloFic').is(":checked") ? 1 : 0;
    let SoloHCalc = $('#SoloHCalc').is(":checked") ? 1 : 0;
    let Filtros = { 'LegDe': LegDe, 'LegHa': LegHa, 'SoloFic': SoloFic, 'SoloHCalc': SoloHCalc };
    return JSON.stringify(Filtros)
}

_Filtros()

$("._filtroLegDeHa").change(function (e) {
    e.preventDefault();
    $('#Per').val(null).trigger("change");
});
$("._filtro").change(function (e) {
    e.preventDefault();
    _Filtros()
});
$("#SoloHCalc").change(function (e) {
    e.preventDefault();
    _Filtros()
    ActualizaTablas()
});

let GetPersonal = $('#GetPersonal').DataTable({
    initComplete: function (settings, json) {
    },
    // bStateSave: -1,
    pagingType: "full",
    lengthMenu: [[1], [1]],
    bProcessing: false,
    serverSide: true,
    deferRender: true,
    searchDelay: 1500,
    dom: ',<"d-inline-flex d-flex align-items-center"t<"m,l-2"p>><"mt-n3 d-flex justify-content-end"i>',
    ajax: {
        url: "/" + $("#_homehost").val() + "/general/GetPersonalFichas.php",
        type: "POST",
        "data": function (data) {
            data._l = $("#_l").val();
            data.Per = $("#Per").val();
            data.Per2 = $("#Per2").val();
            data.Tipo = $("#Tipo").val();
            data.Emp = $("#Emp").val();
            data.Plan = $("#Plan").val();
            data.Sect = $("#Sect").val();
            data.Sec2 = $("#Sec2").val();
            data.Grup = $("#Grup").val();
            data.Sucur = $("#Sucur").val();
            data._dr = $("#_dr").val();
            data.FicDiaL = ($("#FicDiaL").is(":checked")) ? 1 : 0;
            data.FicFalta = $("#datoFicFalta").val();
            data.FicNovT = $("#datoFicNovT").val();
            data.FicNovI = $("#datoFicNovI").val();
            data.FicNovS = $("#datoFicNovS").val();
            data.FicNovA = $("#datoFicNovA").val();
            data.Fic3Nov = $("#datoNovedad").val();
            data.FechaFin = $("#FechaFin").val();
            data.Filtros = _Filtros()
        },

        error: function () {
            $("#GetPersonal_processing").css("display", "none");
        },
    },
    // },
    columns: [
        {
            "class": "w80 px-3 border fw4 bg-light radius pers_legajo",
            "data": 'pers_legajo'
        },
        {
            "class": "w300 px-3 border border-left-0 fw4 bg-light radius",
            "data": 'pers_nombre'
        },
    ],
    paging: true,
    responsive: false,
    info: true,
    ordering: false,
    language: {
        "url": "../js/DataTableSpanishShort2.json" + "?" + vjs(),
    },
});

let buttonCommon = {
    exportOptions: {
        format: {
            body: function (data, row, column, node) {
                // Strip $ from salary column to make it numeric
                return column === 5 ?
                    data.replace(/[$,]/g, '') :
                    data;
            }
        }
    }
};

function textResult(params, selector, tipo) {
    if ((params > 0)) {
        let plural = (params > 1) ? 's' : '';
        let text = (params > 1) ? 'Hay ' + params + ' ' + tipo + plural + ' con resultado' + plural : 'Hay ' + params + ' ' + tipo + plural + ' con resultado' + plural;
        $(selector).html(text)
    } else {
        $(selector).html('No se encontraron resultados.')
    }
    classEfect(selector, 'animate__animated animate__fadeIn')
}
$('#GetPersonal').DataTable().on('draw.dt', function (e, settings) {

    if ((settings.json.recordsTotal > 0)) {
        $('#Visualizar').prop('disabled', false)
    } else {
        $('#Visualizar').prop('disabled', true)
    }
    (!$('#Visualizar').is(':checked')) ? textResult(settings.json.recordsTotal, '.modal-footer .result', 'legajo') : '';
    $("#GetPersonal thead").remove();
    $(".page-link").addClass('border border-0');
    $(".dataTables_info").addClass('text-secondary');
    $('#pagLega').removeClass('d-none')

    if (settings.iDraw === 1) {
        atajosTeclado()
        $('#GetGeneral').DataTable({
            "initComplete": function (settings, json) {
                $("#Refresh").prop('disabled', false);
                $('#trash_all').removeClass('invisible');
                fadeInOnly('#GetGeneralTable')
                fadeInOnly('#pagLega')
            },
            // stateSave: -1,
            lengthMenu: [[30, 60, 90, 120], [30, 60, 90, 120]],
            bProcessing: true,
            serverSide: true,
            deferRender: true,
            searchDelay: 1500,
            ajax: {
                url: "/" + $("#_homehost").val() + "/general/GetGeneral.php",
                type: "POST",
                "data": function (data) {
                    data.Per = $("#Per").val();
                    data.Tipo = $("#Tipo").val();
                    data.Emp = $("#Emp").val();
                    data.Plan = $("#Plan").val();
                    data.Sect = $("#Sect").val();
                    data.Sec2 = $("#Sec2").val();
                    data.Grup = $("#Grup").val();
                    data.Sucur = $("#Sucur").val();
                    data._dr = $("#_dr").val();
                    data._l = $("#_l").val();
                    data.FicDiaL = ($("#FicDiaL").is(":checked")) ? 1 : 0;
                    data.FicFalta = $("#datoFicFalta").val();
                    data.FicNovT = $("#datoFicNovT").val();
                    data.FicNovI = $("#datoFicNovI").val();
                    data.FicNovS = $("#datoFicNovS").val();
                    data.FicNovA = $("#datoFicNovA").val();
                    data.Fic3Nov = $("#datoNovedad").val();
                    data.FechaFin = $("#FechaFin").val();
                    data.Filtros = _Filtros();
                },
                error: function () {
                    $("#GetGeneral").css("display", "none");
                },
            },
            columns: [
                {
                    "class": "pr-2 pl-2 align-middle",
                    "data": "modal"
                },
                {
                    "class": "pl-0 align-middle",
                    "data": "FechaDia"
                },
                {
                    "class": "text-nowrap ls1 align-middle",
                    "data": "Gen_Horario"
                },
                {
                    "class": "text-center fw4 align-middle",
                    "data": "Primera"
                },
                {
                    "class": "align-middle",
                    "data": "DescHoras"
                }, {
                    "class": "text-center fw4 ls1 align-middle",
                    "data": "HsAuto"
                }, {
                    "class": "text-center ls1 align-middle",
                    "data": "HsCalc"
                }, {
                    "class": "align-middle",
                    "data": "Novedades"
                },
                {
                    "class": "text-center fw4 ls1 align-middle",
                    "data": "NovHor"
                },
            ],
            scrollX: true,
            scrollCollapse: true,
            scrollY: '50vmax',
            paging: true,
            info: true,
            searching: false,
            ordering: false,
            language: {
                "url": "../js/DataTableSpanishShort2.json" + "?" + vjs(),
            },
        });
        $('#GetGeneral').DataTable().on('draw.dt', function () {
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $(".custom-select").addClass('text-secondary bg-light');
            $('#pagLega').removeClass('invisible')
            $('#GetGeneralTable').removeClass('invisible')
            setTimeout(function () {
                $(".Filtros").prop('disabled', false);
            }, 1000);
            fadeInOnly('#GetGeneral');
        })
    } else {
        $('#GetGeneral').DataTable().ajax.reload();
        fadeInOnly('#GetGeneral');
    }
})

$('#GetFechas').DataTable({
    "initComplete": function (settings, json) {
        $("#GetFechas thead").remove();
        // $(".page-link").addClass('border border-0');
    },
    "drawCallback": function (settings) {
        $(".page-link").addClass('border border-0');
        if ((settings.json.recordsTotal > 0)) {
            // $('#GetGeneralFecha').DataTable().ajax.reload(null, false);
            // $('#GetGeneralFechaTotales').DataTable().ajax.reload(null, false);
        } else {
            // $('#GetGeneralFecha').DataTable().clear().draw();
        }
    },
    pagingType: "full",
    lengthMenu: [[1], [1]],
    bProcessing: false,
    serverSide: true,
    deferRender: true,
    searchDelay: 1500,
    dom: '<"d-inline-flex d-flex align-items-center"t<"ml-2"p>><"mt-n3 d-flex justify-content-end"i>',
    ajax: {
        url: "/" + $("#_homehost").val() + "/general/GetFechasFichas.php",
        type: "POST",
        "data": function (data) {
            data.Per = $("#Per").val();
            data.Tipo = $("#Tipo").val();
            data.Emp = $("#Emp").val();
            data.Plan = $("#Plan").val();
            data.Sect = $("#Sect").val();
            data.Sec2 = $("#Sec2").val();
            data.Grup = $("#Grup").val();
            data.Sucur = $("#Sucur").val();
            data._dr = $("#_dr").val();
            data._l = $("#_l").val();
            data.FicDiaL = ($("#FicDiaL").is(":checked")) ? 1 : 0;
            data.FicFalta = $("#datoFicFalta").val();
            data.FicNovT = $("#datoFicNovT").val();
            data.FicNovI = $("#datoFicNovI").val();
            data.FicNovS = $("#datoFicNovS").val();
            data.FicNovA = $("#datoFicNovA").val();
            data.Fic3Nov = $("#datoNovedad").val();
            data.FechaFin = $("#FechaFin").val();
            data.Filtros = _Filtros()
        },
        error: function () {
            $("#GetFecha_processing").css("display", "none");
        },
    },
    columns: [
        {
            "class": "w80 px-3 border fw4 bg-light radius ls1",
            "data": 'FicFech'
        },
        {
            "class": "w300 px-3 border fw4 bg-light radius",
            "data": 'Dia'
        },
    ],
    paging: true,
    responsive: false,
    info: true,
    ordering: false,
    language: {
        "url": "../js/DataTableSpanishShort2.json" + "?" + vjs()
    },
});
$('#GetFechas').DataTable().on('draw.dt', function (e, settings) {
    ($('#Visualizar').is(':checked')) ? textResult(settings.json.recordsTotal, '.modal-footer .result', 'día') : '';
    if (settings.iDraw === 1) {
        $('#GetGeneralFecha').DataTable({
            "drawCallback": function (settings) {
                $(".page-link").addClass('border border-0');
                // $('#Visualizar').prop('disabled', false)
                if ((settings.json.recordsTotal > 0)) {
                    // $('#GetGeneralFechaTable').show()
                } else {
                    // $('#GetGeneralFechaTable').hide()
                }
                setTimeout(function () {
                    $(".Filtros").prop('disabled', false);
                }, 1000);
            },
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            bProcessing: true,
            serverSide: true,
            deferRender: true,
            searchDelay: 1500,
            // stateSave: -1,
            dom: 'lBfrtip',
            ajax: {
                url: "/" + $("#_homehost").val() + "/general/GetGeneralFecha.php",
                type: "POST",
                "data": function (data) {
                    // console.log(data);
                    data._f = $("#_f").val();
                    data.Per = $("#Per").val();
                    data.Tipo = $("#Tipo").val();
                    data.Emp = $("#Emp").val();
                    data.Plan = $("#Plan").val();
                    data.Sect = $("#Sect").val();
                    data.Sec2 = $("#Sec2").val();
                    data.Grup = $("#Grup").val();
                    data.Sucur = $("#Sucur").val();
                    data._dr = $("#_dr").val();
                    data._l = $("#_l").val();
                    data.FicDiaL = ($("#FicDiaL").is(":checked")) ? 1 : 0;
                    data.FicFalta = $("#datoFicFalta").val();
                    data.FicNovT = $("#datoFicNovT").val();
                    data.FicNovI = $("#datoFicNovI").val();
                    data.FicNovS = $("#datoFicNovS").val();
                    data.FicNovA = $("#datoFicNovA").val();
                    data.Fic3Nov = $("#datoNovedad").val();
                    data.FechaFin = $("#FechaFin").val();
                    data.Filtros = _Filtros()
                },
                error: function () {
                    $("#GetGeneralFecha_processing").css("display", "none");
                },
            },
            columns: [
                {
                    "class": "pr-2 pl-2 align-middle",
                    "data": "modal"
                },
                {
                    "class": "pl-0 align-middle",
                    "data": "LegNombre"
                },
                {
                    "class": "text-nowrap align-middle",
                    "data": "Gen_Horario"
                },
                {
                    "class": "text-center align-middle",
                    "data": "Primera"
                },
                {
                    "class": "align-middle",
                    "data": "DescHoras"
                }, {
                    "class": "text-center align-middle",
                    "data": "HsAuto"
                }, {
                    "class": "text-center align-middle",
                    "data": "HsCalc"
                }, {
                    "class": "align-middle",
                    "data": "Novedades"
                },
                {
                    "class": "text-center align-middle",
                    "data": "NovHor"
                },
            ],
            scrollX: true,
            scrollCollapse: true,
            scrollY: '50vmax',
            paging: true,
            info: true,
            searching: true,
            ordering: false,
            language: {
                "url": "../js/DataTableSpanishShort2.json" + "?" + vjs()
            },
        });
        $('#GetGeneralFecha').DataTable().on('draw.dt', function (e, settings) {
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $(".custom-select").addClass('text-secondary bg-light');
            fadeInOnly('#GetGeneralFecha');
        })
    } else {
        $('#GetGeneralFecha').DataTable().ajax.reload();
        $('#GetGeneralFecha').DataTable().on('draw.dt', function (e, settings) {
            fadeInOnly('#GetGeneralFecha');
            $('#GetGeneralFechaTable').removeClass('invisible')
        })
    }
})

$('#GetPersonal').on('page.dt', function () {
    CheckSesion()
});
$('#GetGeneral').on('page.dt', function () {
    CheckSesion()
});
$('#GetFechas').on('page.dt', function () {
    CheckSesion()
});
$('#GetGeneralFecha').on('page.dt', function () {
    CheckSesion()
});

$("#Refresh").on("click", function () {
    ActualizaTablas()
    CheckSesion()
});

$('#_dr').on('apply.daterangepicker', function (ev, picker) {
    let endDate = picker.endDate.format('DD/MM/YYYY');
    let startDate = picker.startDate.format('DD/MM/YYYY');
    $('#_drFiltro').data('daterangepicker').setStartDate(startDate);
    $('#_drFiltro').data('daterangepicker').setEndDate(endDate);
    CheckSesion()
    ActualizaTablas()
})
$('#_drFiltro').on('apply.daterangepicker', function (ev, picker) {
    let endDate = picker.endDate.format('DD/MM/YYYY');
    let startDate = picker.startDate.format('DD/MM/YYYY');
    $('#_dr').data('daterangepicker').setStartDate(startDate);
    $('#_dr').data('daterangepicker').setEndDate(endDate);
    CheckSesion()
    ActualizaTablas()
});
$("#FicDiaLFiltro").change(function (e) {
    e.preventDefault();
    if ($("#FicDiaLFiltro").is(":checked")) {
        $("#FicDiaL").prop('checked', true);
    } else {
        $("#FicDiaL").prop('checked', false);
    }
    ActualizaTablas()
});
$("#FicDiaL").change(function (e) {
    e.preventDefault();
    if ($("#FicDiaL").is(":checked")) {
        $("#FicDiaLFiltro").prop('checked', true);
    } else {
        $("#FicDiaLFiltro").prop('checked', false);
    }
    CheckSesion()
    ActualizaTablas()
});

$('#VerPor').html('<span class="d-none d-lg-block">Por Fecha</span>')
$('#VerPorM').html('<span class="d-block d-lg-none">Fecha</span>')

$("#Visualizar").change(function (e) {
    e.preventDefault();
    CheckSesion()
    $('#Per2').addClass('d-none')
    if ($("#Visualizar").is(":checked")) {
        $("#VisualizarFiltro").prop('checked', true);
        $("#GetGeneralTable").addClass('d-none');
        $("#GetGeneralFechaTable").show()
        $("#pagLega").hide()
        $("#pagFech").show()
    } else {
        $("#VisualizarFiltro").prop('checked', false);
        $("#GetGeneralTable").removeClass('d-none');
        $("#GetGeneralFechaTable").hide()
        $("#pagFech").hide()
        $("#pagLega").show()
    }
    ActualizaTablas()
});
$("#VisualizarFiltro").change(function (e) {
    e.preventDefault();
    CheckSesion()
    $('#Per2').addClass('d-none')
    if ($("#VisualizarFiltro").is(":checked")) {
        $("#Visualizar").prop('checked', true);
        $("#GetGeneralTable").addClass('d-none');
        $("#GetGeneralFechaTable").show()
        $("#pagLega").hide()
        $("#pagFech").show()
    } else {
        $("#Visualizar").prop('checked', false);
        $("#GetGeneralTable").removeClass('d-none');
        $("#GetGeneralFechaTable").hide()
        $("#pagFech").hide()
        $("#pagLega").show()
    }
    ActualizaTablas()
});
$('.MaskLega').mask('000000000000', { selectOnFocus: true });

// let options = {
//     onKeyPress: function (cep, e, field, options) {
//         // console.log(cep.length);
//         var masks = ['0000-0000', '00 0000-0000', '+00 0 00 0000-0000'];
//         //   let mask = (cep.length>=17) ? masks[0] : masks[1];
//         let mask = '0#';

//         switch (cep.length) {
//             case 7 :
//                 mask = '0000-0000#'
//                 break;
//             case 8 :
//                 mask = '0000-0000#'
//                 break;
//             case 9:
//                 mask = '0000-00000#'
//                 break;
//             case 10:
//                 mask = '00 0000-0000#'
//                 break;
//             case 11:
//                 mask = '00 0000-000#'
//                 break;
//             case 12:
//                 mask = '00 0000-0000#'
//                 break;
//             case 13:
//                 mask =  '+00 0 00 0000-0000#'
//                 break;
//             default:
//                 mask = '0#'
//                 break;
//         }
//         console.log(cep.length+' - '+ mask);

//         $('.MaskLega').mask(mask, options);
//     }
// };

// $('.MaskLega').mask('0#', options, {selectOnFocus: true});
// SelectSelect2Ajax(selector, multiple, allowClear, placeholder, minimumInputLength, minimumResultsForSearch, maximumInputLength, selectOnClose, ajax_url, delay, data_array, type)
// SelectSelect2Ajax("#datoNovedad", false, true, 'Novedades', 0, 10, 10, true, "../data/getListNovedades.php", '250', '', 'GET')
