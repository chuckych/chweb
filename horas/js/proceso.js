const homehost = $("#_homehost").val();
const LS_FILTROS = homehost + '_filtro_horas_';
function ActualizaTablas() {
    if ($("#Visualizar").is(":checked")) {
        getFechas(true);
    } else {
        getPersonal(true);
    };
};
onOpenSelect2()
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

const getHoras = (reload) => {
    if (reload) {
        $('#GetHoras').DataTable().ajax.reload();
        return;
    }
    $('#GetHoras').DataTable({
        lengthMenu: [[30, 60, 90, 120], [30, 60, 90, 120]],
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        dom: "<'row'" +
            "<'col-12 col-sm-6 d-flex align-items-start'l><'col-12 col-sm-6 d-inline-flex align-items-start justify-content-end'f>>" +
            "<'row '<'col-12'<'border p-2 shadow-sm table-responsive't>>>" +
            "<'row '<'col-12 d-flex bg-transparent align-items-center justify-content-between'<i><p>>>",
        ajax: {
            url: "/" + $("#_homehost").val() + "/horas/GetHoras.php",
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
            },
            error: function () {
                $("#GetHoras_processing").css("display", "none");
            },
        },
        columns: [
            {
                'class': 'ls1',
                'data': 'FicFech',
            },
            {
                'class': '',
                'data': 'Dia',
            },
            {
                'class': 'ls1',
                'data': 'Horario',
            },
            {
                'class': 'text-center',
                'data': 'Hora',
            },
            {
                'class': '',
                'data': 'HoraDesc',
            },
            {
                'class': 'ls1 text-center',
                'data': 'FicHsAu',
            },
            {
                'class': 'ls1 bg-light fw4 text-center',
                'data': 'FicHsAu2',
            },
            {
                'class': '',
                'data': 'Observ',
            },
            {
                'class': '',
                'data': 'DescMotivo',
            },
        ],
        scrollX: true,
        scrollCollapse: true,
        scrollY: '25vmax',
        paging: true,
        info: true,
        searching: false,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort2.json" + "?" + vjs(),
        },
    });
    $('#GetHoras').on('init.dt', function () {
        $("#Refresh").prop('disabled', false);
        $('#trash_all').removeClass('invisible');
        fadeInOnly('#pagLega')
        fadeInOnly('#GetHorasTable')
    });
    $('#GetHoras').on('draw.dt', function () {
        // $(".page-link").addClass('border border-0');
        $(".dataTables_info").addClass('text-secondary');
        $(".custom-select").addClass('text-secondary bg-light');
        $('#pagLega').removeClass('invisible')
        $('#GetHorasTable').removeClass('invisible')
        setTimeout(function () {
            $(".Filtros").prop('disabled', false);
        }, 1000);
    });
    $('#GetHoras').on('page.dt', function () {
        CheckSesion()
    });
}
const getHorasFecha = (reload) => {
    if (reload) {
        $('#GetHorasFecha').DataTable().ajax.reload();
        return;
    }
    $('#GetHorasFecha').DataTable({
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        dom: "<'row'" +
            "<'col-12 col-sm-6 d-flex align-items-start'l><'col-12 col-sm-6 d-inline-flex align-items-start justify-content-end'f>>" +
            "<'row '<'col-12'<'border p-2 shadow-sm table-responsive't>>>" +
            "<'row '<'col-12 d-flex bg-transparent align-items-center justify-content-between'<i><p>>>",
        ajax: {
            url: "/" + $("#_homehost").val() + "/horas/GetHorasFecha.php",
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
            },
            error: function () {
                $("#GetHorasFecha_processing").css("display", "none");
            },
        },
        columns: [
            {
                'class': 'ls1',
                'data': 'Legajo',
            },
            {
                'class': 'ApNo',
                'data': 'Nombre',
            },
            {
                'class': 'ls1',
                'data': 'Horario',
            },
            {
                'class': 'text-center',
                'data': 'Hora',
            },
            {
                'class': '',
                'data': 'HoraDesc',
            },
            {
                'class': 'ls1 text-center',
                'data': 'FicHsAu',
            },
            {
                'class': 'ls1 bg-light fw4 text-center',
                'data': 'FicHsAu2',
            },
            {
                'class': '',
                'data': 'Observ',
            },
            {
                'class': '',
                'data': 'DescMotivo',
            },
        ],
        scrollX: true,
        scrollCollapse: true,
        scrollY: '25vmax',
        paging: true,
        info: true,
        searching: false,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort2.json" + "?" + vjs(),
        },
    });
    $('#GetHorasFecha').on('draw.dt', function () {
        $(".dataTables_info").addClass('text-secondary');
        $(".custom-select").addClass('text-secondary bg-light');
        $('#Visualizar').prop('disabled', false)
        $('#GetHorasFechaTable').removeClass('invisible')
        setTimeout(function () {
            $(".Filtros").prop('disabled', false);
        }, 500);
    });
    $('#GetHorasFecha').on('page.dt', function () {
        CheckSesion()
    });
}
const getPersonal = (reload) => {
    if (reload) {
        $('#GetPersonal').DataTable().ajax.reload();
        return;
    }
    $('#GetPersonal').DataTable({
        pagingType: "full",
        lengthMenu: [[1], [1]],
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        dom: '<"d-inline-flex d-flex align-items-center"t<"ml-2"p>><"mt-n3 d-flex justify-content-end"i>',
        ajax: {
            url: "/" + $("#_homehost").val() + "/horas/GetPersonalFichas1.php",
            type: "POST",
            "data": function (data) {
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
            "url": "../js/DataTableSpanishShort2.json" + "?" + vjs(),
        },
    });
    $('#GetPersonal').on('init.dt', function (settings, json) {
        $("#GetPersonal thead").remove();
        // $(".page-link").addClass('border border-0');
        $(".dataTables_info").addClass('text-secondary');
    });
    $('#GetPersonal').on('draw.dt', function (settings, json) {

        if ($.fn.DataTable.isDataTable('#GetHoras')) {
            getHoras(true); // reload
        } else {
            getHoras(); // create
        }
        tableTotalesLegajo();
    });
    $('#GetPersonal').on('page.dt', function () {
        CheckSesion()
    });
}
const getFechas = (reload) => {
    if (reload) {
        $('#GetFechas').DataTable().ajax.reload();
        return;
    }
    $('#GetFechas').DataTable({
        pagingType: "full",
        lengthMenu: [[1], [1]],
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        dom: '<"d-inline-flex d-flex align-items-center"t<"ml-2"p>><"mt-n3 d-flex justify-content-end"i>',
        ajax: {
            url: "/" + $("#_homehost").val() + "/horas/GetFechasFichas1.php",
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
            "url": "../js/DataTableSpanishShort2.json" + "?" + vjs(),
        },
    });
    $('#GetFechas').on('init.dt', function () {
        $("#GetFechas thead").remove();
        $(".dataTables_info").addClass('text-secondary');
        tableTotalesFecha2();
    });
    $('#GetFechas').on('draw.dt', function (e, settings, json, xhr) {
        if ($.fn.DataTable.isDataTable('#GetHorasFecha')) {
            getHorasFecha(true); // reload
        } else {
            getHorasFecha(); // create
        }
        tableTotalesFecha();
        tableTotalesFecha2();
    });
    $('#GetFechas').on('page.dt', function () {
        CheckSesion()
    });
}

$("#pagFech").addClass('d-none');
$("#GetHorasFechaTable").addClass('d-none');

getPersonal();

$("#Refresh").on("click", function () {
    CheckSesion()
    ActualizaTablas()
});

$("#_dr").change(function () {
    CheckSesion()
    ActualizaTablas()
});
$('#VerPor').html('Visualizar por Fecha')
$("#Visualizar").change(function () {
    CheckSesion()
    if ($("#Visualizar").is(":checked")) {

        if ($.fn.DataTable.isDataTable('#GetFechas')) {
            getFechas(true); // reload
        } else {
            getFechas(); // create
        }
        $("#GetHorasTable").addClass('d-none');
        $("#GetHorasFechaTable").removeClass('d-none');
        $("#GetHorasTotalesTable").addClass('d-none');
        $("#pagLega").addClass('d-none');
        $("#pagFech").removeClass('d-none')
        let el = document.getElementById('div-horas-total');
        el.classList.replace('show', 'd-none');
    } else {
        if ($.fn.DataTable.isDataTable('#GetPersonal')) {
            getPersonal(true); // reload
        } else {
            getPersonal(); // create
        }
        $("#GetHorasTable").removeClass('d-none');
        $("#GetHorasFechaTable").addClass('d-none')
        $("#GetHorasTotalesTable").removeClass('d-none')
        $("#pagLega").removeClass('d-none')
        $("#pagFech").addClass('d-none')
    }
});
const getHorasTotales = async (jsonData) => {
    try {
        let rs = await axios.post('../app-data/horas/totales/', jsonData);
        if (rs.data.length === 0) {
            return false;
        }

        let el = document.getElementById('div-horas-total');
        el.classList.replace('d-none', 'show');

        return rs.data ?? [];
    } catch (error) {
        console.log(error);
        return null;
    }
}
const tableTotalesLegajo = () => {

    let table2 = document.getElementById('tabla-horas-total2');
    table2.classList.replace('show', 'd-none');

    let dataRangeInput = document.getElementById('_dr');
    let value = dataRangeInput.value ?? '';

    if (value === '') {
        document.getElementById('tabla-horas-total').innerHTML = '';
        return;
    }

    let FechaIni = value.split(' al ')[0];
    let FechaFin = value.split(' al ')[1];
    let FechaIniFormat = FechaIni.split('/').reverse().join('-');
    let FechaFinFormat = FechaFin.split('/').reverse().join('-');

    let SHoras = document.querySelector('input[name="SHoras"]:checked').value;

    let jsonData = {
        FechIni: FechaIniFormat,
        FechFin: FechaFinFormat,
        Lega: [$("#_l").val()],
        HoraMin: ls.get(LS_FILTROS + '#HoraMin') ?? '',
        HoraMax: ls.get(LS_FILTROS + '#HoraMax') ?? '',
        MinMaxH: ls.get(LS_FILTROS + '#SHoras') ?? '',
        Hora: ls.get(LS_FILTROS + '.selectjs_thora') ?? [],
        length: 100
    };

    getHorasTotales(jsonData).then((rs) => {
        if (rs === false) {
            return;
        }
        let tiposHoras = rs.tiposHoras;
        // let data = rs.data[0].Totales ?? false;
        let data = rs.totales ?? false;
        if (data === false) {
            return;
        }
        if (data === false) {
            return;
        }

        let table = document.getElementById('tabla-horas-total');
        let HtmlTable = '';
        HtmlTable += '<thead>';
        HtmlTable += '<tr>';
        HtmlTable += `<th class="font09">Horas</th>`;
        tiposHoras.forEach((item) => {
            HtmlTable += `<th class="font09 ">${item.THoDesc2}</th>`;
        });
        HtmlTable += '</tr>';
        HtmlTable += '</thead>';
        HtmlTable += '<tbody>';
        HtmlTable += '<tr>';
        HtmlTable += `<td class="font09">Hechas</td>`;
        tiposHoras.forEach((item) => {
            let rs = data.find((x) => x.HoraCodi === item.HoraCodi);
            HtmlTable += `<td class=" font09">${rs.EnHoras} <span class="ls1 font08">(${rs.Cantidad})</span></td>`;
        });
        HtmlTable += `<td class="w-100"></td>`;
        HtmlTable += '</tr>';
        HtmlTable += '<tr>';
        HtmlTable += `<td class="font09 fw5">Autorizadas</td>`;
        tiposHoras.forEach((item) => {
            let rs = data.find((x) => x.HoraCodi === item.HoraCodi);
            HtmlTable += `<td class="font09 fw5">${rs.EnHoras2} <span class="ls1 font08">(${rs.Cantidad})</span></td>`;
        });
        HtmlTable += `<td class="w-100"></td>`;
        HtmlTable += '</tr>';
        table.innerHTML = HtmlTable;

    });
}
const tableTotalesFecha = () => {

    let fecha = $("#_f").val() ?? '';

    if (fecha === '') {
        document.getElementById('tabla-horas-total').innerHTML = '';
        return;
    }
    let formattedDate = fecha.substr(0, 4) + '-' + fecha.substr(4, 2) + '-' + fecha.substr(6, 2);
    let formattedDate2 = fecha.substr(6, 2) + '/' + fecha.substr(4, 2) + '/' + fecha.substr(0, 4);

    let jsonData = {
        FechIni: formattedDate,
        FechFin: formattedDate,
        Lega: ls.get(LS_FILTROS + '.selectjs_personal') ?? [],
        Empr: ls.get(LS_FILTROS + '.selectjs_empresa') ?? [],
        Plan: ls.get(LS_FILTROS + '.selectjs_plantas') ?? [],
        Sect: ls.get(LS_FILTROS + '.selectjs_sectores') ?? [],
        Sec2: ls.get(LS_FILTROS + '.select_seccion') ?? [],
        Grup: ls.get(LS_FILTROS + '.selectjs_grupos') ?? [],
        Sucu: ls.get(LS_FILTROS + '.selectjs_sucursal') ?? [],
        HoraMin: ls.get(LS_FILTROS + '#HoraMin') ?? '',
        HoraMax: ls.get(LS_FILTROS + '#HoraMax') ?? '',
        MinMaxH: ls.get(LS_FILTROS + '#SHoras') ?? '',
        Hora: ls.get(LS_FILTROS + '.selectjs_thora') ?? [],
        length: 10000
    };

    getHorasTotales(jsonData).then((rs) => {
        if (rs === false) {
            return;
        }
        let tiposHoras = rs.tiposHoras ?? false;

        if (tiposHoras === false) {
            return;
        }
        let data = rs.totales ?? false;

        if (data === false) {
            return;
        }

        let table = document.getElementById('tabla-horas-total');
        let title = `<p class="p-2 m-0 font09 border-bottom">${formattedDate2} </p>`
        //append title
        table.innerHTML = title;
        let HtmlTable = '';
        HtmlTable += '<thead>';
        HtmlTable += '<tr>';
        HtmlTable += `<th class="font09">Horas</th>`;
        tiposHoras.forEach((item) => {
            HtmlTable += `<th class="font09 ">${item.THoDesc2}</th>`;
        });
        HtmlTable += '</tr>';
        HtmlTable += '</thead>';
        HtmlTable += '<tbody>';
        HtmlTable += '<tr>';
        HtmlTable += `<td class="font09">Hechas</td>`;
        tiposHoras.forEach((item) => {
            let rs = data.find((x) => x.HoraCodi === item.HoraCodi);
            HtmlTable += `<td class=" font09">${rs.EnHoras} <span class="ls1 font08">(${rs.Cantidad})</span></td>`;
        });
        HtmlTable += `<td class="w-100"></td>`;
        HtmlTable += '</tr>';
        HtmlTable += '<tr>';
        HtmlTable += `<td class="font09 fw5">Autorizadas</td>`;
        tiposHoras.forEach((item) => {
            let rs = data.find((x) => x.HoraCodi === item.HoraCodi);
            HtmlTable += `<td class="fw5 font09">${rs.EnHoras2} <span class="ls1 font08">(${rs.Cantidad})</span></td>`;
        });
        HtmlTable += `<td class="w-100"></td>`;
        HtmlTable += '</tr>';
        table.innerHTML += HtmlTable;

    });
}
const tableTotalesFecha2 = () => {

    let dataRangeInput = document.getElementById('_dr');
    let value = dataRangeInput.value ?? '';

    if (value === '') {
        document.getElementById('tabla-horas-total2').innerHTML = '';
        return;
    }

    let FechaIni = value.split(' al ')[0];
    let FechaFin = value.split(' al ')[1];
    let FechaIniFormat = FechaIni.split('/').reverse().join('-');
    let FechaFinFormat = FechaFin.split('/').reverse().join('-');

    if (FechaFinFormat == FechaIniFormat) {
        document.getElementById('tabla-horas-total2').innerHTML = '';
        return;
    }

    let jsonData = {
        FechIni: FechaIniFormat,
        FechFin: FechaFinFormat,
        Lega: ls.get(LS_FILTROS + '.selectjs_personal') ?? [],
        Empr: ls.get(LS_FILTROS + '.selectjs_empresa') ?? [],
        Plan: ls.get(LS_FILTROS + '.selectjs_plantas') ?? [],
        Sect: ls.get(LS_FILTROS + '.selectjs_sectores') ?? [],
        Sec2: ls.get(LS_FILTROS + '.select_seccion') ?? [],
        Grup: ls.get(LS_FILTROS + '.selectjs_grupos') ?? [],
        Sucu: ls.get(LS_FILTROS + '.selectjs_sucursal') ?? [],
        HoraMin: ls.get(LS_FILTROS + '#HoraMin') ?? '',
        HoraMax: ls.get(LS_FILTROS + '#HoraMax') ?? '',
        MinMaxH: ls.get(LS_FILTROS + '#SHoras') ?? '',
        Hora: ls.get(LS_FILTROS + '.selectjs_thora') ?? [],
        length: 10000
    };

    getHorasTotales(jsonData).then((rs) => {
        if (rs === false) {
            return;
        }
        let tiposHoras = rs.tiposHoras ?? false;
        if (tiposHoras === false) {
            return;
        }
        let data = rs.totales ?? false;
        if (data === false) {
            return;
        }

        let table = document.getElementById('tabla-horas-total2');
        table.classList.replace('d-none', 'show');
        let title = `<p class="p-2 m-0 font09 border-bottom border-top pt-4">${value} </p>`
        //append title
        table.innerHTML = title;
        let HtmlTable = '';
        HtmlTable += '<thead>';
        HtmlTable += '<tr>';
        HtmlTable += `<th class="font09">Horas</th>`;
        tiposHoras.forEach((item) => {
            HtmlTable += `<th class="font09 ">${item.THoDesc2}</th>`;
        });
        HtmlTable += '</tr>';
        HtmlTable += '</thead>';
        HtmlTable += '<tbody>';
        HtmlTable += '<tr>';
        HtmlTable += `<td class="font09">Hechas</td>`;
        tiposHoras.forEach((item) => {
            let rs = data.find((x) => x.HoraCodi === item.HoraCodi);
            HtmlTable += `<td class=" font09">${rs.EnHoras} <span class="ls1 font08">(${rs.Cantidad})</span></td>`;
        });
        HtmlTable += `<td class="w-100"></td>`;
        HtmlTable += '</tr>';
        HtmlTable += '<tr>';
        HtmlTable += `<td class="font09 fw5">Autorizadas</td>`;
        tiposHoras.forEach((item) => {
            let rs = data.find((x) => x.HoraCodi === item.HoraCodi);
            HtmlTable += `<td class="font09 fw5">${rs.EnHoras2} <span class="ls1 font08">(${rs.Cantidad})</span></td>`;
        });
        HtmlTable += `<td class="w-100"></td>`;
        HtmlTable += '</tr>';
        table.innerHTML += HtmlTable;
    });
}
