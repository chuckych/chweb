const homehost = $("#_homehost").val();
const LS_FILTROS = homehost + '_filtro_horas_';
const spinnerLoad = `<div class="spinner-border font07" role="status" style="width: 15px; height:15px" ></div>`;
const loading = () => {
    $.notifyClose();
    let spinner = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`;
    notify('Aguarde <span class = "dotting mr-1"> </span> ' + spinner, 'dark', 60000, 'right')
}

const dateRange = async () => {
    let rs = await axios.get('../app-data/fechas/horas');
    if (!rs.data) return;
    let añoMin = rs.data.min.split('-')[0];
    let añoMax = rs.data.max.split('-')[0];
    let minDate = rs.data.min.split('-').reverse().join('/');
    let maxDate = rs.data.max.split('-').reverse().join('/');

    $('#_dr').daterangepicker({
        singleDatePicker: false,
        showDropdowns: true,
        minYear: añoMin,
        maxYear: añoMax,
        showWeekNumbers: false,
        autoUpdateInput: true,
        opens: "left",
        drops: "down",
        startDate: maxDate,
        endDate: maxDate,
        autoApply: true,
        minDate: minDate,
        maxDate: maxDate,
        alwaysShowCalendars: true,
        linkedCalendars: false,
        buttonClasses: "btn btn-sm fontq",
        applyButtonClasses: "btn-custom fw4 px-3 opa8",
        cancelClass: "btn-link fw4 text-gris",
        ranges: {
            'Hoy': [moment(), moment()],
            'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Esta semana': [moment().day(1), moment().day(7)],
            'Semana Anterior': [moment().subtract(1, 'week').day(1), moment().subtract(1, 'week').day(7)],
            'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
            'Este mes': [moment().startOf('month'), moment().endOf('month')],
            'Mes anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
        },
        locale: {
            format: "DD/MM/YYYY",
            separator: " al ",
            applyLabel: "Aplicar",
            cancelLabel: "Cancelar",
            fromLabel: "Desde",
            toLabel: "Para",
            customRangeLabel: "Personalizado",
            weekLabel: "Sem",
            daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
            monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            firstDay: 1,
            alwaysShowCalendars: true,
            applyButtonClasses: "btn-custom fw5 px-3 opa8",
        },
    });
}
function ActualizaTablas() {
    loaderIn('table', true);
    let verPor = document.querySelector('input[name="VPor"]:checked').value;
    ls.set(LS_FILTROS + 'VPor', parseInt(verPor));
    if (verPor === '1') {
        getFechas();
    } else {
        getPersonal();
    };
};

$('input[name="VPor"]').on('change', function () {
    $('#tablas2').addClass('invisible').removeClass('animate__animated animate__fadeIn');
    loading()
    ActualizaTablas()
    CheckSesion()
});

const toggleTablas = (tipo) => {

    if (tipo === 1) {

        $('#tablas').removeClass('invisible').addClass('animate__animated animate__fadeIn');
        loaderIn('#tablas', false);
        $('#pagLega').hide().removeClass('animate__animated animate__fadeIn');
        $('#GetHorasTable').hide().removeClass('animate__animated animate__fadeIn');
        $('#pagFech').show().addClass('animate__animated animate__fadeIn');
        $('#GetHorasFechaTable').show().addClass('animate__animated animate__fadeIn');

    } else {

        $('#tablas').removeClass('invisible').addClass('animate__animated animate__fadeIn');
        loaderIn('#tablas', false);
        $('#pagFech').hide().removeClass('animate__animated animate__fadeIn');
        $('#GetHorasFechaTable').hide().removeClass('animate__animated animate__fadeIn');
        $('#GetHorasTable').show().addClass('animate__animated animate__fadeIn');
        $('#pagLega').show().addClass('animate__animated animate__fadeIn');
    }
}
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
            if (verPor === '1') {
                $('#GetFechas').DataTable().page('next').draw('page');
            } else {
                $('#GetPersonal').DataTable().page('next').draw('page');
            }
        }
    }
    if (e.keyCode in map) {
        map[e.keyCode] = true;
        if (map[37]) { /** Flecha izquierda */
            if (verPor === '1') {
                $('#GetFechas').DataTable().page('previous').draw('page');
            } else {
                $('#GetPersonal').DataTable().page('previous').draw('page');
            }
        }
    }
}).keyup(function (e) {
    if (e.keyCode in map) {
        map[e.keyCode] = false;
    }
});

const getHoras = () => {
    if ($.fn.DataTable.isDataTable('#GetHoras')) {
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
            "<'row '<'col-12'<'border radius p-2 shadow-sm table-responsive't>>>" +
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
                data: 'FicFech', className: '', targets: '', title: 'Fecha',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'Dia', className: '', targets: '', title: 'Día',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'Horario', className: '', targets: '', title: 'Horario',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'Hora', className: 'text-center', targets: '', title: 'Hora',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'HoraDesc', className: '', targets: '', title: 'Descripción',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'FicHsAu', className: 'text-center', targets: '', title: 'Hechas',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'FicHsAu2', className: 'bg-light fw4 text-center', targets: '', title: 'Autor.',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'Observ', className: '', targets: '', title: 'Observ.',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'DescMotivo', className: '', targets: '', title: 'Motivo',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
        ],
        scrollX: true,
        scrollCollapse: true,
        // scrollY: '25vmax',
        paging: true,
        info: true,
        searching: false,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort2.json" + "?" + vjs(),
        },
    });
    $('#GetHoras').on('init.dt', function (settings, json) {
        $('#trash_all').removeClass('invisible');
    });
    $('#GetHoras').on('draw.dt', function (settings, json) {
        $(".dataTables_info").addClass('text-secondary');
        $(".custom-select").addClass('text-secondary bg-light');
        $(".Filtros").prop('disabled', false);
        $('#tablas2').removeClass('invisible').addClass('animate__animated animate__fadeIn');
        $.notifyClose();
        loaderIn('table', false);
    });
    $('#GetHoras').on('page.dt', function () {
        CheckSesion()
        loaderIn('#GetHoras', true);
    });
}
const getHorasFecha = () => {
    if ($.fn.DataTable.isDataTable('#GetHorasFecha')) {
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
            "<'row '<'col-12'<'border radius p-2 shadow-sm table-responsive't>>>" +
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
                data: 'Legajo', className: '', targets: '', title: 'Legajo',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'Nombre', className: 'ApNo', targets: '', title: 'Nombre',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'Horario', className: '', targets: '', title: 'Horario',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'Hora', className: 'text-center', targets: '', title: 'Hora',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'HoraDesc', className: '', targets: '', title: 'Descripción',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'FicHsAu', className: 'text-center', targets: '', title: 'Hechas',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'FicHsAu2', className: 'bg-light fw4 text-center', targets: '', title: 'Autor.',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'Observ', className: '', targets: '', title: 'Observ.',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'DescMotivo', className: '', targets: '', title: 'Motivo',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
        ],
        scrollX: true,
        scrollCollapse: true,
        // scrollY: '25vmax',
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
        setTimeout(function () {
            $(".Filtros").prop('disabled', false);
        }, 500);
        $('#tablas2').removeClass('invisible').addClass('animate__animated animate__fadeIn');
        $.notifyClose();
        loaderIn('table', false);
    });
    $('#GetHorasFecha').on('page.dt', function () {
        CheckSesion()
        loaderIn('#GetHorasFecha', true);
    });
}
const getPersonal = () => {

    if ($.fn.DataTable.isDataTable('#GetPersonal')) {
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
        dom: `
        <"row"
                <"col-12 d-flex justify-content-end"
                <"d-flex justify-content-end align-items-end">
                <"d-inline-flex align-items-center"<"mt-2 mt-sm-1"t>
                    <"d-none d-sm-block ml-1"p>
                >   
            >
            <"col-12"
                <"d-block d-sm-none mt-n2"p>
                <"d-flex justify-content-end align-items-end mt-n1"i>
            >
        >
            `,
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
                "class": "w80 px-3 border border-right-0 radius-left fw4 bg-light",
                "data": 'pers_legajo'
            },
            {
                data: 'pers_nombre', className: 'text-left border-left-0 radius-right px-3 border fw4 bg-light', targets: '', title: '',
                "render": function (data, type, row, meta) {
                    return `<div class="text-truncate d-sm-none d-block" style="max-width:160px">${data}</div><div style="min-width:300px" class="d-sm-block d-none">${data}</div>`;
                },
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
        $(".dataTables_info").addClass('text-secondary');
    });
    $('#GetPersonal').on('draw.dt', function (settings, json) {
        toggleTablas();
        getHoras(); // create
        tableTotalesLegajo();
        loaderIn('#GetPersonal', false);
    });
    $('#GetPersonal').on('page.dt', function () {
        loaderIn('#GetPersonal', true);
        loaderIn('#GetHoras', true);
        CheckSesion()
    });
}
const getFechas = () => {
    if ($.fn.DataTable.isDataTable('#GetFechas')) {
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
        // dom: '<"d-inline-flex d-flex align-items-center"t<"ml-2"p>><"mt-n3 d-flex justify-content-end"i>',
        dom: `
        <"row"
            <"col-12 d-flex justify-content-end"
                <"d-flex justify-content-end align-items-end">
                <"d-inline-flex align-items-center"<"mt-2 mt-sm-1"t>
                    <"d-none d-sm-block ml-1"p>
                >   
            >
            <"col-12"
                <"d-block d-sm-none mt-n2"p>
                <"d-flex justify-content-end align-items-end mt-n1"i>
            >
        >
            `,
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
                "class": "w80 px-3 border border-right-0 radius-left fw4 bg-light",
                "data": 'FicFech'
            },
            {
                "class": "w200 px-3 border border-left-0 radius-right fw4 bg-light",
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
    $('#GetFechas').on('init.dt', function (settings, json) {
        $("#GetFechas thead").remove();
        $(".dataTables_info").addClass('text-secondary');
        tableTotalesFecha2();
    });
    $('#GetFechas').on('draw.dt', function (e, settings, json, xhr) {
        toggleTablas(1);
        getHorasFecha();
        tableTotalesFecha();
        tableTotalesFecha2();
        loaderIn('#GetFechas', false);
    });
    $('#GetFechas').on('page.dt', function () {
        loaderIn('#GetFechas', true);
        loaderIn('#GetHorasFecha', true);
        CheckSesion()
    });
}
if (ls.get(LS_FILTROS + 'VPor')) {
    $("#VFecha").prop('checked', true);
} else {
    $("#VLegajo").prop('checked', true);
}
dateRange().then(() => {
    ActualizaTablas();
});
const verPor = document.querySelector('input[name="VPor"]:checked').value;

$("#Refresh").on("click", function () {
    CheckSesion()
    ActualizaTablas()
});

$('#_dr').on('apply.daterangepicker', function (ev, picker) {
    CheckSesion()
    loaderIn('#GetFechas', true);
    loaderIn('#GetPersonal', true);
    loaderIn('#GetHoras', true);
    loaderIn('#GetHorasFecha', true);
    ActualizaTablas()
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
            HtmlTable += `<td class=" font09">${rs.EnHoras} <span class="font08">(${rs.Cantidad})</span></td>`;
        });
        HtmlTable += `<td class="w-100"></td>`;
        HtmlTable += '</tr>';
        HtmlTable += '<tr>';
        HtmlTable += `<td class="font09 fw5">Autorizadas</td>`;
        tiposHoras.forEach((item) => {
            let rs = data.find((x) => x.HoraCodi === item.HoraCodi);
            HtmlTable += `<td class="font09 fw5">${rs.EnHoras2} <span class="font08">(${rs.Cantidad})</span></td>`;
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
            document.getElementById('tabla-horas-total').innerHTML = '';
            return;
        }
        let data = rs.totales ?? false;

        if (data === false) {
            document.getElementById('tabla-horas-total').innerHTML = '';
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
            HtmlTable += `<td class=" font09">${rs.EnHoras} <span class="font08">(${rs.Cantidad})</span></td>`;
        });
        HtmlTable += `<td class="w-100"></td>`;
        HtmlTable += '</tr>';
        HtmlTable += '<tr>';
        HtmlTable += `<td class="font09 fw5">Autorizadas</td>`;
        tiposHoras.forEach((item) => {
            let rs = data.find((x) => x.HoraCodi === item.HoraCodi);
            HtmlTable += `<td class="fw5 font09">${rs.EnHoras2} <span class="font08">(${rs.Cantidad})</span></td>`;
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
            document.getElementById('tabla-horas-total2').innerHTML = '';
            return;
        }
        let tiposHoras = rs.tiposHoras ?? false;
        if (tiposHoras === false) {
            document.getElementById('tabla-horas-total2').innerHTML = '';
            return;
        }
        let data = rs.totales ?? false;
        if (data === false) {
            document.getElementById('tabla-horas-total2').innerHTML = '';
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
            HtmlTable += `<td class=" font09">${rs.EnHoras} <span class="font08">(${rs.Cantidad})</span></td>`;
        });
        HtmlTable += `<td class="w-100"></td>`;
        HtmlTable += '</tr>';
        HtmlTable += '<tr>';
        HtmlTable += `<td class="font09 fw5">Autorizadas</td>`;
        tiposHoras.forEach((item) => {
            let rs = data.find((x) => x.HoraCodi === item.HoraCodi);
            HtmlTable += `<td class="font09 fw5">${rs.EnHoras2} <span class="font08">(${rs.Cantidad})</span></td>`;
        });
        HtmlTable += `<td class="w-100"></td>`;
        HtmlTable += '</tr>';
        table.innerHTML += HtmlTable;
    });
}