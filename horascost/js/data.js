// $(".Filtros").prop('disabled', true);
function ActualizaTablas() {
    if ($("#Visualizar").is(":checked")) {
        $('#GetFechas').DataTable().ajax.reload();
    } else {
        $('#GetPersonal').DataTable().ajax.reload();
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
    if (e.keyCode in map) {
        map[e.keyCode] = true;
        if (map[13]) { /** Enter */
            ActualizaTablas()
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
// console.log($("#_l").val());
$("#pagFech").addClass('d-none');
$("#GetHorasFechaTable").addClass('d-none');
$('#Visualizar').prop('disabled', true)
/** On Select Agrupar */
$('.selectAgrupar').on('select2:select', function (e) {
    $('#GetHoras').DataTable().clear().destroy();
    GetHoras()
    $('#GetHorasFecha').DataTable().clear().destroy();
    GetHorasFecha()
    Cookies.set("AgrupaHorasCost", $('#Agrupar').val());
});
SelectSelect2('.selectAgrupar', false, "Agrupar Por", 0, -1, 10, false)
function GetHoras() {
    var GetHoras = $('#GetHoras').DataTable({
        "initComplete": function (settings, json) {
            $("#Refresh").prop('disabled', false);
            $('#trash_all').removeClass('invisible');
            fadeInOnly('#pagLega')
            fadeInOnly('#GetHorasTable')
        },
        "columnDefs": [
            { "visible": false, "targets": 0, "type": "html" },
            { "visible": true, "targets": 1, "type": "html" }
        ],
        "drawCallback": function (settings) {
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $(".custom-select").addClass('text-secondary bg-light');
            $('#pagLega').removeClass('invisible')
            $('#GetHorasTable').removeClass('invisible')
            setTimeout(function () {
                // fadeInOnly('#pagLega')
                // fadeInOnly('#GetHorasTable')
                $(".Filtros").prop('disabled', false);
            }, 1000);
            // console.log(settings.json);
        },
        bProcessing: true,
        serverSide: false,
        searchDelay: 1500,
        orderFixed: [[0, "asc"]],
        iDisplayLength: -1,
        rowGroup: {
            dataSrc: [$('#Agrupar').val()],
            // endRender: null,
            // startRender: function ( rows, group ) {
            //     return group +' ('+rows.count()+')';
            // }
        },
        ajax: {
            url: "GetHoras.php",
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
                data.Thora = $("#Thora").val();
                data.SHoras = $("#SHoras").val();
                data.HoraMin = $("#HoraMin").val();
                data.HoraMax = $("#HoraMax").val();
                data.Calculos = $("#Calculos").val();
                data.Tar = $("#Tar").val();
            },
            error: function () {
                $("#GetHoras_processing").css("display", "none");
            },
        },
        columns: [
            {
                'class': '',
                'data': $('#Agrupar').val(),
            },
            {
                'class': '',
                'data': 'FechaDia',
            },
            {
                'class': '',
                'data': 'Horario',
            },
            {
                'class': '',
                'data': 'HoraDesc',
            },
            {
                'class': 'ls1 text-center bg-light',
                'data': 'CalcHoras',
            },
            {
                'class': '',
                'data': 'Costo',
            },
            {
                'class': 'align-middle',
                'data': 'TareaDesc',
            },
            {
                'class': 'align-middle',
                'data': 'PlantaDesc',
            },
            {
                'class': 'align-middle',
                'data': 'SucurDesc',
            },
            {
                'class': 'align-middle',
                'data': 'GrupDesc',
            },
            {
                'class': 'align-middle text-wrap',
                'data': 'SectDesc',
            },
            {
                'class': 'align-middle',
                'data': 'Sec2Desc',
            },
        ],
        deferRender: true,
        paging: false,
        searching: false,
        scrollY: '50vh',
        scrollX: true,
        scrollCollapse: true,
        info: true,
        // ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort2.json"
        },
    });
    return GetHoras
}
GetHoras()
$('#GetPersonal').DataTable({
    "initComplete": function (settings, json) {
        // GetHoras()
    },
    "drawCallback": function (settings) {
        $("#GetPersonal thead").remove();
        $(".page-link").addClass('border border-0');
        $(".dataTables_info").addClass('text-secondary');
        $('#GetHoras').DataTable().ajax.reload();
        $('#GetHorasTotales').DataTable().ajax.reload();
        // GetHoras()
    },
    pagingType: "full",
    lengthMenu: [[1], [1]],
    bProcessing: false,
    serverSide: true,
    deferRender: true,
    searchDelay: 1500,
    dom: '<"d-inline-flex d-flex align-items-center"t<"ml-2"p>><"mt-n3 d-flex justify-content-end"i>',
    ajax: {
        url: "GetPersonalFichas01.php",
        type: "POST",
        "data": function (data) {
            data._l      = $("#_l").val();
            data.Per     = $("#Per").val();
            data.Tipo    = $("#Tipo").val();
            data.Emp     = $("#Emp").val();
            data.Plan    = $("#Plan").val();
            data.Sect    = $("#Sect").val();
            data.Sec2    = $("#Sec2").val();
            data.Grup    = $("#Grup").val();
            data.Sucur   = $("#Sucur").val();
            data._dr     = $("#_dr").val();
            data.Thora   = $("#Thora").val();
            data.SHoras  = $("#SHoras").val();
            data.HoraMin = $("#HoraMin").val();
            data.HoraMax = $("#HoraMax").val();
            data.Tar     = $("#Tar").val();
        },
        error: function () {
            $("#GetPersonal_processing").css("display", "none");
        },
    },
    columns: [
        {
            "class": "w80 px-3 border fw4 bg-light radius",
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

$('#GetHorasTotales').DataTable({
    "initComplete": function (settings, json) {
        $("#Refresh").prop('disabled', false);
        $('#trash_all').removeClass('invisible');
    },
    "drawCallback": function (settings) {
        $(".page-link").addClass('border border-0');
        $(".dataTables_info").addClass('text-secondary');
        $(".custom-select").addClass('text-secondary bg-light');
        setTimeout(function () {
            $(".Filtros").prop('disabled', false);
        }, 1000);
        $('#GetHorasTotalesTable').removeClass('invisible')
    },
    bProcessing: true,
    serverSide: true,
    deferRender: true,
    searchDelay: 1500,
    ajax: {
        url: "GetHorasTotales.php",
        type: "POST",
        "data": function (data) {
            // console.log(data);
            data._l = $("#_l").val();
            data.Per = $("#Per").val();
            data.Tipo = $("#Tipo").val();
            data.Emp = $("#Emp").val();
            data.Plan = $("#Plan").val();
            data.Sect = $("#Sect").val();
            data.Sec2 = $("#Sec2").val();
            data.Grup = $("#Grup").val();
            data.Sucur = $("#Sucur").val();
            data._dr = $("#_dr").val();
            data.Thora = $("#Thora").val();
            data.SHoras = $("#SHoras").val();
            data.HoraMin = $("#HoraMin").val();
            data.HoraMax = $("#HoraMax").val();
            data.Calculos = $("#Calculos").val();
            data.Tar = $("#Tar").val();
        },
        error: function () {
            $("#GetHorasNew_processing").css("display", "none");
        },
    },
    columns: [
        {
            'class': 'ls1 text-center',
            'data': 'FicHora',
        },
        {
            'class': 'w150',
            'data': 'THoDesc',
        },
        {
            'class': 'ls1 text-center',
            'data': 'CalcHoras',
        },
        // {
        //     'class': 'ls1 text-center bg-light fw4',
        //     'data': 'FicHsAuC',
        // },
    ],
    // scrollY: '450px',
    scrollX: true,
    scrollCollapse: false,
    paging: false,
    responsive: false,
    info: false,
    searching: false,
    ordering: false,
    language: {
        "url": "../js/DataTableSpanishShort.json"
    },
});
function GetHorasFecha() {
    var GetHorasFecha = $('#GetHorasFecha').DataTable({
        "initComplete": function (settings, json) {
        },
        "columnDefs": [
            { "visible": false, "targets": 0, "type": "html" },
            // { "visible": false, "targets": 1, "type": "html" }
        ],
        "drawCallback": function (settings) {
            // console.log(settings.json);
            // $.each(settings.json.data, function (key, value) {
            //     console.table(value.Nombre + '->' + value.HoraDesc2 + '->' +value.MinFicHsAuC);
            // });
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $(".custom-select").addClass('text-secondary bg-light');
            $('#Visualizar').prop('disabled', false)
            $('#GetHorasFechaTable').removeClass('invisible')
            setTimeout(function () {
                $(".Filtros").prop('disabled', false);
            }, 1000);
        },
        bProcessing: true,
        serverSide: false,
        searchDelay: 1500,
        orderFixed: [[6, "asc"], [0, "asc"]],
        iDisplayLength: -1,
        rowGroup: {
            dataSrc: [$('#Agrupar').val()],
            // endRender: null,
            // startRender: function ( rows, group ) {
            //     var TextRegistro = (rows.count()=='1') ? ' Registro':' Registros';
            //     return group +' <span class="fontp text-secondary">( '+rows.count()+ TextRegistro +' )</span>';
            // },
        },
        ajax: {
            url: "GetHorasFecha.php",
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
                data.Thora = $("#Thora").val();
                data.SHoras = $("#SHoras").val();
                data.HoraMin = $("#HoraMin").val();
                data.HoraMax = $("#HoraMax").val();
                data.Calculos = $("#Calculos").val();
                data.Tar = $("#Tar").val();
            },
            error: function () {
                $("#GetHorasFecha_processing").css("display", "none");
            },
        },
        columns: [
            {
                'class': '',
                'data': 'Legajo',
            },
            {
                'class': '',
                'data': 'LegNombre',
            },
            {
                'class': '',
                'data': 'Horario',
            },
            {
                'class': '',
                'data': 'HoraDesc',
            },
            {
                'class': 'ls1 text-center bg-light',
                'data': 'CalcHoras',
            },
            {
                'class': 'd-none',
                'data': 'Tarea',
            },
            {
                'class': 'd-none',
                'data': $('#Agrupar').val(),
            },
            {
                'class': '',
                'data': 'Costo',
            },
            {
                'class': 'align-middle',
                'data': 'TareaDesc',
            },
            {
                'class': 'align-middle',
                'data': 'PlantaDesc',
            },
            {
                'class': 'align-middle',
                'data': 'SucurDesc',
            },
            {
                'class': 'align-middle',
                'data': 'GrupDesc',
            },
            {
                'class': 'align-middle text-wrap',
                'data': 'SectDesc',
            },
            {
                'class': 'align-middle',
                'data': 'Sec2Desc',
            },
        ],
        deferRender: true,
        paging: false,
        searching: false,
        scrollY: '50vh',
        scrollX: true,
        scrollCollapse: true,
        info: true,
        language: {
            "url": "../js/DataTableSpanishShort2.json"
        },
    });
    return GetHorasFecha
}
GetHorasFecha()
setTimeout(function () {
    $('#GetFechas').DataTable({
        "initComplete": function (settings, json) {
            $("#GetFechas thead").remove();
            // GetHorasFecha()
        },
        "drawCallback": function (settings) {
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $('#GetHorasFecha').DataTable().ajax.reload();
            $('#GetHorasFechaTotales').DataTable().ajax.reload();
        },
        pagingType: "full",
        lengthMenu: [[1], [1]],
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        dom: '<"d-inline-flex d-flex align-items-center"t<"ml-2"p>><"mt-n3 d-flex justify-content-end"i>',
        ajax: {
            url: "GetFechasFichas1.php",
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
                data.Thora = $("#Thora").val();
                data.SHoras = $("#SHoras").val();
                data.HoraMin = $("#HoraMin").val();
                data.HoraMax = $("#HoraMax").val();
                data.Tar = $("#Tar").val();
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

    $('#GetHorasFechaTotales').DataTable({
        "initComplete": function (settings, json) {
            $(".CollapseFiltros").prop('disabled', false);
            $("#Refresh").prop('disabled', false);
            $('#trash_all').removeClass('invisible');
        },
        "drawCallback": function (settings) {
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $(".custom-select").addClass('text-secondary bg-light');
            setTimeout(function () {
                $(".Filtros").prop('disabled', false);
            }, 1000);

        },
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        ajax: {
            url: "GetHorasFechaTotales.php",
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
                data.Thora = $("#Thora").val();
                data.SHoras = $("#SHoras").val();
                data.HoraMin = $("#HoraMin").val();
                data.HoraMax = $("#HoraMax").val();
                data.Calculos = $("#Calculos").val();
                data.Tar = $("#Tar").val();
            },
            error: function () {
                $("#GetHorasNew_processing").css("display", "none");
            },
        },
        columns: [
            {
                'class': 'ls1 text-center',
                'data': 'FicHora',
            },
            {
                'class': 'w150',
                'data': 'THoDesc',
            },
            {
                'class': 'ls1 text-center',
                'data': 'CalcHoras',
            },
        ],
        // scrollY: '450px',
        scrollX: true,
        scrollCollapse: false,
        paging: false,
        responsive: false,
        info: false,
        searching: false,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort.json"
        },
    });
}, 1000);

$("#Refresh").on("click", function () {
    ActualizaTablas()
});

$("#_dr").change(function () {
    ActualizaTablas()
});
$('#VerPor').html('Visualizar por Fecha')
$("#Visualizar").change(function () {
    // $("#loader").addClass('loader');
    if ($("#Visualizar").is(":checked")) {
        $('#GetFechas').DataTable().ajax.reload();
        // $('#GetHorasFecha').DataTable().ajax.reload();
        $("#GetHorasTable").addClass('d-none');
        $("#GetHorasFechaTable").removeClass('d-none');
        $("#GetHorasTotalesTable").addClass('d-none');
        $("#pagLega").addClass('d-none');
        $("#pagFech").removeClass('d-none')
        // $('#VerPor').html('Visualizar por Legajo')
    } else {
        $('#GetPersonal').DataTable().ajax.reload();
        // $('#GetHoras').DataTable().ajax.reload();
        $("#GetHorasTable").removeClass('d-none');
        $("#GetHorasFechaTable").addClass('d-none')
        $("#GetHorasTotalesTable").removeClass('d-none')
        $("#pagLega").removeClass('d-none')
        $("#pagFech").addClass('d-none')
        // $('#VerPor').html('Visualizar por Fecha')
    }
});