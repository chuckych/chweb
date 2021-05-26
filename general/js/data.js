// $(".Filtros").prop('disabled', true);
function ActualizaTablas() {

    if ($("#Visualizar").is(":checked")) {
        $('#GetFechas').DataTable().ajax.reload();
    } else {
        $('#GetPersonal').DataTable().ajax.reload();
        $('#Per2').addClass('d-none')
        $('.pers_legajo').removeClass('d-none')
    };
};
var map = { 17: false, 18: false, 32: false, 16: false, 39: false, 37: false, 13: false, 27: false };
$(document).keydown(function (e) {
    if (e.keyCode in map) {
        map[e.keyCode] = true;
        if (map[32]) { /** Barra espaciadora */
            $('#Filtros').modal('show');
        }
    }
    if (e.keyCode in map) {
        map[e.keyCode] = true;
        if (map[27]) { /** Esc */
            $('#Filtros').modal('hide');
        }
    }
    // if (e.keyCode in map) {
    //     map[e.keyCode] = true;
    //     if (map[13]) { /** Enter */
    //         ActualizaTablas()
    //     }
    // }
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

// $("#pagFech").addClass('d-none');
$("#pagFech").hide()
$("#GetGeneralFechaTable").hide();
// $('#Visualizar').prop('disabled', true)

$('#datoFicDiaL').val('1');
$("#FicDiaL").change(function () {
    if ($("#FicDiaL").is(":checked")) {
        $('#datoFicDiaL').val('1')
        ActualizaTablas()
    } else {
        $('#datoFicDiaL').val('0')
        ActualizaTablas()
    }
});

$('#datoFicFalta').val('0');
$("#FicFalta").change(function () {
    if ($("#FicFalta").is(":checked")) {
        $('#datoFicFalta').val('1')
        $('#datoFicNovA').val('0')
        $('#FicNovA').prop('checked', false)
        $('#FicNovA').prop('disabled', true)
        ActualizaTablas()
    } else {
        $('#datoFicFalta').val('0')
        $('#FicNovA').prop('disabled', false)
        ActualizaTablas()
    }
});
$('#datoFicNovT').val('0');
$("#FicNovT").change(function () {
    if ($("#FicNovT").is(":checked")) {
        $('#datoFicNovT').val('1')
        $('#datoFicNovA').val('0')
        $('#FicNovA').prop('checked', false)
        $('#FicNovA').prop('disabled', true)
        ActualizaTablas()
    } else {
        $('#datoFicNovT').val('0')
        $('#FicNovA').prop('disabled', false)
        ActualizaTablas()
    }
});
$('#datoFicNovI').val('0');
$("#FicNovI").change(function () {
    if ($("#FicNovI").is(":checked")) {
        $('#datoFicNovI').val('1')
        $('#datoFicNovA').val('0')
        $('#FicNovA').prop('checked', false)
        $('#FicNovA').prop('disabled', true)
        ActualizaTablas()
    } else {
        $('#datoFicNovI').val('0')
        $('#FicNovA').prop('disabled', false)
        ActualizaTablas()
    }
});
$('#datoFicNovS').val('0');
$("#FicNovS").change(function () {
    if ($("#FicNovS").is(":checked")) {
        $('#datoFicNovS').val('1')
        $('#datoFicNovA').val('0')
        $('#FicNovA').prop('checked', false)
        $('#FicNovA').prop('disabled', true)
        ActualizaTablas()
    } else {
        $('#datoFicNovS').val('0')
        $('#FicNovA').prop('disabled', false)
        ActualizaTablas()
    }
});
$('#datoFicNovA').val('0');
$("#FicNovA").change(function () {
    if ($("#FicNovA").is(":checked")) {
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
        ActualizaTablas()
    } else {
        $('#datoFicNovA').val('0')
        $('#FicFalta').prop('disabled', false)
        $('#FicNovT').prop('disabled', false)
        $('#FicNovI').prop('disabled', false)
        $('#FicNovS').prop('disabled', false)
        ActualizaTablas()
    }
});

var GetPersonal = $('#GetPersonal').DataTable({
    initComplete: function (settings, json) {
        // console.log(settings.json);
    },
    drawCallback: function (settings) {
        $("#GetPersonal thead").remove();
        $(".page-link").addClass('border border-0');
        $(".dataTables_info").addClass('text-secondary');
        $('#pagLega').removeClass('d-none')
        $('#GetGeneral').DataTable().ajax.reload(null, false);
        fadeInOnly('#GetGeneral');
        // console.log(settings.json.recordsTotal);
        if ((settings.json.recordsTotal > 0)) {
            $('#Visualizar').prop('disabled', false)
        } else {
            $('#Visualizar').prop('disabled', true)
        }
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
            data.FicDiaL = $("#datoFicDiaL").val();
            data.FicFalta = $("#datoFicFalta").val();
            data.FicNovT = $("#datoFicNovT").val();
            data.FicNovI = $("#datoFicNovI").val();
            data.FicNovS = $("#datoFicNovS").val();
            data.FicNovA = $("#datoFicNovA").val();
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
        "url": "../js/DataTableSpanishShort2.json"
    },
});
var buttonCommon = {
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
var GetGeneral = $('#GetGeneral').DataTable({
    "initComplete": function (settings, json) {
        $("#Refresh").prop('disabled', false);
        $('#trash_all').removeClass('invisible');
        fadeInOnly('#GetGeneralTable')
        fadeInOnly('#pagLega')
    },
    "drawCallback": function (settings) {
        $(".page-link").addClass('border border-0');
        $(".dataTables_info").addClass('text-secondary');
        $(".custom-select").addClass('text-secondary bg-light');
        $('#pagLega').removeClass('invisible')
        $('#GetGeneralTable').removeClass('invisible')
        setTimeout(function () {
            $(".Filtros").prop('disabled', false);
        }, 1000);
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
            data.FicDiaL = $("#datoFicDiaL").val();
            data.FicFalta = $("#datoFicFalta").val();
            data.FicNovT = $("#datoFicNovT").val();
            data.FicNovI = $("#datoFicNovI").val();
            data.FicNovS = $("#datoFicNovS").val();
            data.FicNovA = $("#datoFicNovA").val();
        },
        error: function () {
            $("#GetGeneral").css("display", "none");
        },
    },
    columns: [
        {
            "class": "align-middle",
            "data": "modal"
        },
        {
            "class": "align-middle",
            "data": "FechaDia"
        },
        {
            "class": "text-nowrap ls1 align-middle",
            "data": "Gen_Horario"
        },
        {
            "class": "ls1 text-center fw4 align-middle",
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
        "url": "../js/DataTableSpanishShort2.json"
    },
});

setTimeout(function () {
    var GetFechas = $('#GetFechas').DataTable({
        "initComplete": function (settings, json) {
            $("#GetFechas thead").remove();
        },
        "drawCallback": function (settings) {
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            fadeInOnly('#GetGeneralFecha');
            if ((settings.json.recordsTotal > 0)) {
                $('#GetGeneralFecha').DataTable().ajax.reload(null, false);
                $('#GetGeneralFechaTotales').DataTable().ajax.reload(null, false);
            } else {
                $('#GetGeneralFecha').DataTable().clear().draw();
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
                data.FicDiaL = $("#datoFicDiaL").val();
                data.FicFalta = $("#datoFicFalta").val();
                data.FicNovT = $("#datoFicNovT").val();
                data.FicNovI = $("#datoFicNovI").val();
                data.FicNovS = $("#datoFicNovS").val();
                data.FicNovA = $("#datoFicNovA").val();
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
            "url": "../js/DataTableSpanishShort2.json"
        },
    });
    var GetGeneralFecha = $('#GetGeneralFecha').DataTable({
        "initComplete": function (settings, json) {
            setTimeout(function () {
                $('.dt-button').addClass('btn btn-sm btn-outline-custom fontq border');
                $('.dt-button').removeClass('dt-button buttons-csv buttons-html5');
                $('.dt-buttons').removeClass('d-none');
                $('.dt-button').removeAttr('style');
            }, 500);
        },
        "drawCallback": function (settings) {
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $(".custom-select").addClass('text-secondary bg-light');
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
                data.FicDiaL = $("#datoFicDiaL").val();
                data.FicFalta = $("#datoFicFalta").val();
                data.FicNovT = $("#datoFicNovT").val();
                data.FicNovI = $("#datoFicNovI").val();
                data.FicNovS = $("#datoFicNovS").val();
                data.FicNovA = $("#datoFicNovA").val();
            },
            error: function () {
                $("#GetGeneralFecha_processing").css("display", "none");
            },
        },
        columns: [
            {
                "class": "align-middle",
                "data": "modal"
            },
            {
                "class": "align-middle",
                "data": "LegNombre"
            },
            {
                "class": "text-nowrap ls1 align-middle",
                "data": "Gen_Horario"
            },
            {
                "class": "ls1 text-center fw4 align-middle",
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
        searching: true,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort2.json"
        },
    });
}, 1000);



$("#Refresh").on("click", function () {
    ActualizaTablas()
    CheckSesion()
});

$("#_dr").change(function () {
    CheckSesion()
    ActualizaTablas()
});
$("#Visualizar").change(function () {
    CheckSesion()
    $('#Per2').addClass('d-none')
});

$('#VerPor').html('<span class="d-none d-lg-block">Por Fecha</span>')
$('#VerPorM').html('<span class="d-block d-lg-none">Fecha</span>')
$("#Visualizar").change(function () {
    // $("#loader").addClass('loader');
    if ($("#Visualizar").is(":checked")) {
        $('#GetFechas').DataTable().ajax.reload(null, false);
        $("#GetGeneralTable").addClass('d-none');
        $("#GetGeneralFechaTable").show()
        $("#pagLega").hide()
        $("#pagFech").show()
    } else {
        $('#GetPersonal').DataTable().ajax.reload(null, false);
        $("#GetGeneralTable").removeClass('d-none');
        $("#GetGeneralFechaTable").hide()
        $("#pagFech").hide()
        $("#pagLega").show()
    }
});