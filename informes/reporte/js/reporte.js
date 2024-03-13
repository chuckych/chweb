const homehost = $("#_homehost").val();
const LS_FILTROS = homehost + '_reporte_totales_filtros';
const LS_TOTAL_HORAS = homehost + '_reporte_totales_horas';
const now = new Date().getTime();

const dateRange = async () => {
    let rs = await axios.get('../../app-data/fechas/fichas'); // retorna objeto con la primer fecha y la ultima de FICHAS ej: {data: {min: "2021-01-01", max: "2021-12-31"}}
    if (!rs.data) return; // si no hay respuesta, no hace nada

    let añoMin = new Date(rs.data.min).getFullYear(); // extrae el año de la fecha minima
    let añoMax = new Date(rs.data.max).getFullYear(); // extrae el año de la fecha maxima
    let minDate = new Date(rs.data.min)
    let maxDate = new Date(rs.data.max)

    let maxDate2 = (new Date(rs.data.max) > new Date()) ? new Date() : new Date(rs.data.max); // si la fecha maxima es mayor a la fecha actual, la fecha maxima es la fecha actual
    let minDate2 = new Date(maxDate2); // la fecha minima es la fecha maxima
    minDate2.setDate(minDate2.getDate() - 29); // le resta 29 Dias a la fecha maxima

    $('#_dr').daterangepicker({
        singleDatePicker: false,
        showDropdowns: true,
        minYear: añoMin,
        maxYear: añoMax,
        showWeekNumbers: false,
        autoUpdateInput: true,
        opens: "left",
        drops: "down",
        startDate: minDate2,
        endDate: maxDate2,
        autoApply: true,
        minDate: minDate,
        maxDate: maxDate,
        alwaysShowCalendars: true,
        linkedCalendars: false,
        buttonClasses: "btn btn-sm font08",
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
    })
    loaderIn('#_dr', false);
}
var IconExcel = '.xls <img src="../../img/xls.png" class="w15" alt="Exportar Excel">'
ActiveBTN(false, "#ExportarXLS", 'Exportando', IconExcel)

const VTodo = (value) => {
    if (value == "horas") {
        $('#VHoras').prop('checked', true);
        $("#select_thora").prop('disabled', false);
        $("#select_nove").prop('disabled', true);
    }
    if (value == "novedades") {
        $('#VNovedades').prop('checked', true);
        $("#select_nove").prop('disabled', false);
        $("#select_thora").prop('disabled', true);
    }
    if (value == "todo") {
        $('#VTodo').prop('checked', true);
        $("#select_thora").prop('disabled', false);
        $("#select_nove").prop('disabled', false);
    }
}

if (!ls.get(LS_FILTROS + 'VPor')) {
    $('#VTodo').prop('checked', true);
    $("#select_thora").prop('disabled', false);
    $("#select_nove").prop('disabled', false);
}

VTodo(ls.get(LS_FILTROS + 'VPor'));

$('input[name="VPor"]').on('change', function () {
    let verPor = document.querySelector('input[name="VPor"]:checked').value;
    ls.set(LS_FILTROS + 'VPor', (verPor));
    verTabla();
    VTodo(ls.get(LS_FILTROS + 'VPor'));
    axios.get('../../app-data/horas/payload?flag=' + now + '&VPor=' + $(this).val());
});
$('input[name="VPorFormato"]').on('change', function () {
    if ($(this).val() == 'enDecimal') {
        $('.enHoras').addClass('d-none').removeClass('animate__animated animate__fadeIn');
        $('.enDecimales').removeClass('d-none').addClass('animate__animated animate__fadeIn');
    } else {
        $('.enHoras').removeClass('d-none').addClass('animate__animated animate__fadeIn');
        $('.enDecimales').addClass('d-none').removeClass('animate__animated animate__fadeIn');
    }
    axios.get('../../app-data/horas/payload?flag=' + now + '&VPorFormato=' + $(this).val());
});

const validarFormatoHoras = (selector) => {

    let string = $(selector);
    let valorHora = string.val().split(':');

    if (!string.val()) {
        string.val('00:00').trigger('change');
        return false;
    }

    let horas = valorHora[0];
    let minutos = valorHora[1];

    if (minutos === undefined) {
        string.val(pad(horas, 2) + ':00').trigger('change');
        return false;
    }

    if (minutos.length === 1) {
        string.val(pad(horas, 2) + ':' + pad(minutos, 2)).trigger('change');
        return false;
    }

    if (string.val().length === 5) {
        return true;
    }
    return false;
}

const verTabla = () => {
    if (ls.get(LS_FILTROS + 'VPor') == "horas") {
        $('#div_tabla').show().addClass('animate__animated animate__fadeIn');
        $('#div_tabla_novedades').hide().removeClass('animate__animated animate__fadeIn');
    }
    if (ls.get(LS_FILTROS + 'VPor') == "novedades") {
        $('#div_tabla').hide().removeClass('animate__animated animate__fadeIn');
        $('#div_tabla_novedades').show().addClass('animate__animated animate__fadeIn');
    }
    if (ls.get(LS_FILTROS + 'VPor') == "todo") {
        $('#div_tabla').show().addClass('animate__animated animate__fadeIn');
        $('#div_tabla_novedades').show().addClass('animate__animated animate__fadeIn');
    }

}
verTabla();
dateRange().then(() => {

    const select2Data = (selector) => {
        try {
            if (!$(selector).hasClass("select2-hidden-accessible")) {
                throw new Error("No es un select2")
            }
            const data = $(selector).select2('data') ?? [];
            return data.length > 0 ? data.map(item => item.id) : [];
        } catch (error) {
            return [];
        }
    }

    const getValuesDate = () => {
        let dateRange = $('#_dr').data('daterangepicker');
        return {
            startDate: dateRange.startDate.format('YYYY-MM-DD'),
            endDate: dateRange.endDate.format('YYYY-MM-DD')
        }
    }

    const select2Results = data => data ? data.map(item => ({
        id: item.Cod,
        text: item.Desc,
        html: `<div class="d-flex align-items-center"><span class="font08 mr-1">(${item.Cod})</span><span>${item.Desc}</span></div>`,
    })) : [];
    const select2ResultsTipoPersonal = data => data ? data.map(item => ({
        id: item.Cod,
        text: item.Desc,
        html: `<div class="d-flex align-items-center"><span>${item.Desc}</span></div>`,
    })) : [];
    const select2ResultsLegajos = data => data ? data.map(item => ({
        id: item.Cod,
        text: item.Desc,
        html: `<div class=""><span>${item.Desc}</span><br><span class="font08">${item.Cod}</span></div>`,
    })) : [];


    let jsonData = {
        "Estruct": "",
        "Desc": "",
        "Sector": select2Data("#select_sector"),
        "Baja": [],
        "Nume": [],
        "ApNo": "",
        "ApNoNume": "",
        "Docu": [],
        "Empr": select2Data("#select_empresa"),
        "Plan": select2Data("#select_planta"),
        "Conv": [],
        "Sect": select2Data("#select_sector"),
        "Sec2": select2Data("#select_seccion"),
        "Grup": select2Data("#select_grupo"),
        "Sucu": select2Data("#select_sucursal"),
        "TareProd": [],
        "RegCH": [],
        "Tipo": [],
        "THora": [],
        "Esta": [],
        "Nove": [],
        "FechaIni": getValuesDate().startDate,
        "FechaFin": getValuesDate().endDate,
        "FechIni": getValuesDate().startDate,
        "FechFin": getValuesDate().endDate,
        "start": 0,
        "length": 1000
    }
    ls.set(LS_FILTROS, jsonData);

    const ajaxSelect2 = (selector, placeholder, estruct) => {
        // si ya es un select2 no hace nada return false
        if ($(selector).hasClass("select2-hidden-accessible")) return false;
        $(selector).select2({
            multiple: true,
            allowClear: true,
            language: "es",
            dropdownParent: $('#Filtros'),
            placeholder: placeholder,
            minimumInputLength: 0,
            minimumResultsForSearch: 5,
            maximumInputLength: 10,
            selectOnClose: false,
            width: "100%",
            templateResult: function (data) {
                let $result = $(data.html);
                return $result;
            },
            ajax: {
                url: "../../app-data/estruct/fichas/",
                dataType: "json",
                type: "POST",
                delay: 250,
                width: "100%",
                data: function (params) {
                    // console.log(jsonData);
                    return requestData = Object.assign({}, jsonData, { "Estruct": estruct, "Desc": params.term, [estruct]: [] });
                },
                processResults: function (data, params, page) {
                    if (estruct == 'Tipo') {
                        return {
                            results:
                                select2ResultsTipoPersonal(data)
                        };
                    }
                    if (estruct == 'Lega') {
                        return {
                            results:
                                select2ResultsLegajos(data)
                        };
                    }
                    return {
                        results:
                            select2Results(data)
                    };
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
            }
        }).on('select2:select', function () {
            if (estruct == 'Sect') {
                jsonData['Sector'] = select2Data("#select_sector");
                $('#select_seccion').prop('disabled', false);
            }
            jsonData[estruct] = select2Data(selector);
            ls.set(LS_FILTROS, jsonData);
            getHoras();
            getNovedades();
        }).on('select2:unselect', function () {
            if (estruct == 'Sect') {
                jsonData['Sector'] = select2Data("#select_sector");
                $('#select_seccion').val(null).trigger('change').prop('disabled', true);
                jsonData['Sec2'] = [];
            }
            jsonData[estruct] = select2Data(selector);
            ls.set(LS_FILTROS, jsonData);
            getHoras();
            getNovedades();
        }).on('select2:close', function () {

        });
        $("#Filtros").removeClass("invisible");
    }

    $('#Filtros').on('shown.bs.collapse', function () {
        VTodo(ls.get(LS_FILTROS + 'VPor'));
        ajaxSelect2("#select_empresa", "Empresas", "Empr");
        ajaxSelect2("#select_planta", "Plantas", "Plan");
        ajaxSelect2("#select_sector", "Sectores", "Sect");
        ajaxSelect2("#select_grupo", "Grupos", "Grup");
        ajaxSelect2("#select_sucursal", "Sucursales", "Sucu");
        ajaxSelect2("#select_personal", "Personal", "Lega");
        ajaxSelect2("#select_seccion", "Secciones", "Sec2");
        ajaxSelect2("#select_tipo", "Tipo de Personal", "Tipo");
        ajaxSelect2("#select_thora", "Tipos de Horas", "THora");
        ajaxSelect2("#select_nove", "Novedades", "Nove");
        // $('#Filtros').off('shown.bs.collapse');
        $('#HoraMin').mask('00:00');
        $('#HoraMax').mask('00:00');
    });

    let trash_allInputs = () => {
        $('#Filtros input').val('');
        $('#Filtros select').val(null).trigger('change');
        $('#Filtros .select2').val(null).trigger('change');
        $('#HoraMin').val('00:01');
        $('#HoraMax').val('23:59');
        $('#SHoras1').prop('checked', true).val('1');
        $('#SHoras0').prop('checked', false).val('0');
        $('#labelSHoras1').addClass('active');
        $('#labelSHoras0').removeClass('active');
        jsonData = {
            "Estruct": "",
            "Desc": "",
            "Sector": [],
            "Baja": [],
            "Nume": [],
            "ApNo": "",
            "ApNoNume": "",
            "Docu": [],
            "Empr": [],
            "Plan": [],
            "Conv": [],
            "Sect": [],
            "Sec2": [],
            "Grup": [],
            "Sucu": [],
            "TareProd": [],
            "RegCH": [],
            "Tipo": [],
            "THora": [],
            "Esta": [],
            "Nove": [],
            "FechaIni": getValuesDate().startDate,
            "FechIni": getValuesDate().startDate,
            "FechaFin": getValuesDate().endDate,
            "FechFin": getValuesDate().endDate,
            "HoraMin": "00:01",
            "HoraMax": "23:59",
            "MinMaxH": "1",
            "start": 0,
            "length": 1000
        }
        ls.set(LS_FILTROS, jsonData);
        getHoras();
        getNovedades();
    }
    let trash_allIn = document.getElementById('trash_allIn');
    trash_allIn.addEventListener('click', trash_allInputs, false);

    $('#_dr').on('apply.daterangepicker', function (ev, picker) {
        jsonData.FechIni = picker.startDate.format('YYYY-MM-DD');
        jsonData.FechaIni = picker.startDate.format('YYYY-MM-DD');
        jsonData.FechFin = picker.endDate.format('YYYY-MM-DD');
        jsonData.FechaFin = picker.endDate.format('YYYY-MM-DD');
        ls.set(LS_FILTROS, jsonData);
        loaderIn('#section_tablas', true);
        getHoras();
        getNovedades();
    });

    $('#HoraMin').change(function () {
        (validarFormatoHoras('#HoraMin')) ? getHoras() : '';
    });
    $('#HoraMax').change(function () {
        (validarFormatoHoras('#HoraMax')) ? getHoras() : '';
    });
    $('input[name="SHoras"]').change(function () {
        getHoras()
    });
    getHoras();
    getNovedades();
    exportarXls();
});


const getHoras = async () => {

    // Función para obtener los datos actualizados
    const getTableData = () => {
        let jsonData = ls.get(LS_FILTROS);
        return jsonData;
    };

    if ($.fn.DataTable.isDataTable('#tabla')) {
        loaderIn('#tabla', true);
        $('#tabla').DataTable().ajax.reload(); // Recargar la tabla con los datos actuales
        return false;
    }

    let tabla = $('#tabla').DataTable({
        lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
        bProcessing: true,
        serverSide: false,
        deferRender: true,
        searchDelay: 1500,
        dom: "<'row'" +
            "<'col-12 d-flex align-items-center'l<'ml-2 font09 title'>><'col-12 col-sm-6 d-inline-flex align-items-start justify-content-end'f>>" +
            "<'row '<'col-12'<'border radius p-1 table-responsive't>>>" +
            "<'row '<'col-12 col-sm-6'<i>>''<'col-12 col-sm-6'<p>>'>",
        ajax: {
            url: "../../app-data/horas/totales",
            type: "POST",
            "data": function (data) {
                data.Empr = getTableData().Empr;
                data.Plan = getTableData().Plan;
                data.Sect = getTableData().Sect;
                data.Sec2 = getTableData().Sec2;
                data.Grup = getTableData().Grup;
                data.Sucu = getTableData().Sucu;
                data.LegTipo = getTableData().Tipo;
                data.Hora = getTableData().THora;
                data.Nove = getTableData().Nove;
                data.FechIni = getTableData().FechIni;
                data.FechFin = getTableData().FechFin;
                data.Lega = getTableData().Lega;
                data.start = 0;
                data.length = 1000000;
                data.DTHoras = true;
                data.HsTrAT = 1;
                data.HoraMin = $('#HoraMin').val();
                data.HoraMax = $('#HoraMax').val();
                data.MinMaxH = $('input[name="SHoras"]:checked').val();
                data.Formato = $('input[name="VPorFormato"]:checked').val();
                data.VPor = $('input[name="VPor"]:checked').val();
                data.flag = now;
            },
            error: function () {
                $("#tabla_processing").css("display", "none");
            },
        },
        columns: [
            {
                data: 'LegApNo', className: '', targets: '', title: 'LEGAJO',
                "render": function (data, type, row, meta) {
                    return `<div class="text-truncate" style="min-width:220px; max-width:220px">${data}<br>${row.Lega}</div>`;
                },
            },
            {
                data: 'HsATyTR', className: 'text-right minmax50', targets: '', title: '<div class="hint--right hint--rounded hint--no-arrow hint--default hint--no-shadow" aria-label="Horas a Trabajar">HS AT</div>',
                "render": function (data, type, row, meta) {
                    let html = '';
                    let HsATEnDecimal = (data.HsATEnDecimal);
                    HsATEnDecimal = (Math.round((HsATEnDecimal + Number.EPSILON) * 100) / 100).toFixed(2);
                    html += `<div class="enDecimales">${(HsATEnDecimal)}</div>`
                    html += `<div class="enHoras">${data.HsATEnHoras}</div>`
                    return html;
                },
            },
            {
                data: 'HsATyTR', className: 'text-right minmax50', targets: '', title: '<div class="hint--right hint--rounded hint--no-arrow hint--default hint--no-shadow" aria-label="Horas Trabajadas">HS TR</div>',
                "render": function (data, type, row, meta) {
                    let html = '';
                    let HsTrEnDecimal = (data.HsTrEnDecimal);
                    HsTrEnDecimal = (Math.round((HsTrEnDecimal + Number.EPSILON) * 100) / 100).toFixed(2);
                    html += `<div class="enDecimales">${(HsTrEnDecimal)}</div>`
                    html += `<div class="enHoras">${data.HsTrEnHoras}</div>`
                    return html;
                },
            },
            {
                data: '', className: 'text-right pr-0', targets: '', title: 'COD',
                "render": function (data, type, row, meta) {
                    let array = row.Totales
                    let html = '';
                    array.forEach(element => {
                        html += `<div>${element.HoraCodi}</div>`
                    });
                    return html;
                },
            },
            {
                data: '', className: '', targets: '', title: 'TIPO DE HORA',
                "render": function (data, type, row, meta) {
                    let array = row.Totales
                    let html = '';
                    array.forEach(element => {
                        html += `<div class="text-truncate" style="min-width:140px; max-width:140px">${element.THoDesc}</div>`
                    });
                    return html;
                },
            },
            {
                data: '', className: 'text-right', targets: '', title: 'CANT',
                "render": function (data, type, row, meta) {
                    let array = row.Totales
                    let html = '';
                    array.forEach(element => {
                        html += `<div>${element.Cantidad}</div>`
                    });
                    return html;
                },
            },
            {
                data: '', className: 'text-right minmax50', targets: '', title: '<div class="hint--right hint--rounded hint--no-arrow hint--default hint--no-shadow" aria-label="Horas Hechas">HECHAS</div>',
                "render": function (data, type, row, meta) {
                    let array = row.Totales
                    let html = '';
                    array.forEach(element => {
                        let EnHorasDecimal = (element.EnHorasDecimal);
                        EnHorasDecimal = (Math.round((EnHorasDecimal + Number.EPSILON) * 100) / 100).toFixed(2);
                        html += `<div class="enDecimales">${(EnHorasDecimal)}</div>`
                        html += `<div class="enHoras">${element.EnHoras}</div>`
                    });
                    return html;
                },
            },
            {
                data: '', className: 'text-right bg-light minmax50', targets: '', title: '<div class="hint--right hint--rounded hint--no-arrow hint--default hint--no-shadow" aria-label="Horas Autorizadas" > AUTOR.</div>',
                "render": function (data, type, row, meta) {
                    let array = row.Totales
                    let html = '';
                    array.forEach(element => {
                        let EnHorasDecimal2 = (element.EnHorasDecimal2);
                        EnHorasDecimal2 = (Math.round((EnHorasDecimal2 + Number.EPSILON) * 100) / 100).toFixed(2);
                        html += `<div class="enDecimales">${(EnHorasDecimal2)}</div>`
                        html += `<div class="enHoras">${element.EnHoras2}</div>`
                    });
                    return html;
                },
            },
            {
                data: '', className: 'w-100', targets: '', title: '',
                "render": function (data, type, row, meta) {
                    return '';
                },
            },
        ],
        paging: true,
        info: true,
        searching: false,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanishShort2.json" + "?" + vjs(),
        },
        // Eventos de la tabla
        initComplete: function (e, settings, json) {
            // $('.title').html('<span>Totales Horas</span>');
            $('.title').html('<div>Detalle de Horas</div>').addClass('w-100 text-right');
            $(".custom-select").select2({
                minimumResultsForSearch: Infinity,
            });
            loaderIn('#tabla', false);
            $('#section_tablas').show();
            $('#div_formato').show();
        },
        preDrawCallback: function () {
            loaderIn('#tabla', true);
        },
        // al cambiar de pagina o cambiar el tamaño de la tabla mostrar en formato decimal o en horas
        drawCallback: function (e, settings, json) {
            setTimeout(() => {
                loaderIn('#tabla', false);
            }, 100);
            if (e.json) {
                getHorasTotales(e.json.totales ?? '', e.json.totalesTryAT ?? '');
            }
            let formato = $('input[name="VPorFormato"]:checked').val();
            if (formato == 'enDecimal') {
                $('.enHoras').addClass('d-none');
                $('.enDecimales').removeClass('d-none');
            } else {
                $('.enHoras').removeClass('d-none');
                $('.enDecimales').addClass('d-none');
            }
            loaderIn('#section_tablas', false);
        }
    });
}
const getNovedades = async () => {

    // Función para obtener los datos actualizados
    const getTableData = () => {
        let jsonData = ls.get(LS_FILTROS);
        return jsonData;
    };

    if ($.fn.DataTable.isDataTable('#tabla_novedades')) {
        loaderIn('#tabla_novedades', true);
        $('#tabla_novedades').DataTable().ajax.reload(); // Recargar la tabla con los datos actuales
        return false;
    }

    let tabla = $('#tabla_novedades').DataTable({
        lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
        bProcessing: true,
        serverSide: false,
        deferRender: true,
        searchDelay: 1500,
        dom: "<'row'" +
            "<'col-12 d-flex align-items-center'l<'font09 title-nove'>><'col-12 col-sm-6 d-inline-flex align-items-start justify-content-end'f>>" +
            "<'row '<'col-12'<'border radius p-1 table-responsive't>>>" +
            "<'row '<'col-12 col-sm-6'<i>>''<'col-12 col-sm-6'<p>>'>",
        ajax: {
            url: "../../app-data/novedades/totales",
            type: "POST",
            "data": function (data) {
                data.Empr = getTableData().Empr;
                data.Plan = getTableData().Plan;
                data.Sect = getTableData().Sect;
                data.Sec2 = getTableData().Sec2;
                data.Grup = getTableData().Grup;
                data.Sucu = getTableData().Sucu;
                data.LegTipo = getTableData().Tipo;
                data.Hora = getTableData().THora;
                data.Nove = getTableData().Nove;
                data.FechIni = getTableData().FechIni;
                data.FechFin = getTableData().FechFin;
                data.Lega = getTableData().Lega;
                data.start = 0;
                data.length = 1000000;
                data.DTNovedades = true;
                data.flag = now;
            },
            error: function () {
                $("#tabla_novedades_processing").css("display", "none");
            },
        },
        columns: [
            {
                data: 'LegApNo', className: '', targets: '', title: 'LEGAJO',
                "render": function (data, type, row, meta) {
                    return `<div class="text-truncate" style="min-width:220px; max-width:220px">${data}<br>${row.Lega}</div>`;
                },
            },
            {
                data: '', className: 'text-center', targets: '', title: 'COD',
                "render": function (data, type, row, meta) {
                    let array = row.Totales
                    let html = '';
                    array.forEach(element => {
                        html += `<div>${element.NovCodi}</div>`
                    });
                    return html;
                },
            },
            {
                data: '', className: '', targets: '', title: 'NOVEDAD',
                "render": function (data, type, row, meta) {
                    let array = row.Totales
                    let html = '';
                    array.forEach(element => {
                        html += `<div class="text-truncate" style="min-width:220px; max-width:220px">${element.NovDesc}</div>`
                    });
                    return html;
                },
            },
            {
                data: '', className: 'text-right', targets: '', title: 'CANT',
                "render": function (data, type, row, meta) {
                    let array = row.Totales
                    let html = '';
                    array.forEach(element => {
                        html += `<div>${element.Cantidad}</div>`
                    });
                    return html;
                },
            },
            {
                data: '', className: 'text-right minmax50 bg-light', targets: '', title: '<div class="hint--right hint--rounded hint--no-arrow hint--default hint--no-shadow" aria-label="Horas de la novedad">HORAS</div>',
                "render": function (data, type, row, meta) {
                    let array = row.Totales
                    let html = '';
                    array.forEach(element => {
                        let EnHorasDecimal = (element.EnHorasDecimal);
                        EnHorasDecimal = (Math.round((EnHorasDecimal + Number.EPSILON) * 100) / 100).toFixed(2);
                        html += `<div class="enDecimales">${(EnHorasDecimal)}</div>`
                        html += `<div class="enHoras">${element.EnHoras}</div>`
                    });
                    return html;
                },
            },
            {
                data: '', className: 'w-100', targets: '', title: '',
                "render": function (data, type, row, meta) {
                    return '';
                },
            },
        ],
        paging: true,
        info: true,
        searching: false,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanishShort2.json" + "?" + vjs(),
        },
        // Eventos de la tabla
        initComplete: function () {
            $('.title-nove').html('<div>Detalle de Novedades</div>').addClass('w-100 text-right');
            $(".custom-select").select2({
                minimumResultsForSearch: Infinity,
            });
            loaderIn('#tabla_novedades', false);
        },
        preDrawCallback: function () {
            loaderIn('#tabla_novedades', true);
        },
        // al cambiar de pagina o cambiar el tamaño de la tabla mostrar en formato decimal o en horas
        drawCallback: function (e, settings, json) {

            setTimeout(() => {
                loaderIn('#tabla_novedades', false);
            }, 100);

            if (e.json) {
                getNovedadesTotales(e.json.totales ?? '');
            }
            let formato = $('input[name="VPorFormato"]:checked').val();
            if (formato == 'enDecimal') {
                $('.enHoras').addClass('d-none');
                $('.enDecimales').removeClass('d-none');
            } else {
                $('.enHoras').removeClass('d-none');
                $('.enDecimales').addClass('d-none');
            }
        }
    });
}

const exportarXls = async () => {
    let button = document.getElementById('ExportarXLS');
    button.addEventListener('click', async function () {
        button.disabled = true;
        notify('Aguarde ..', 'dark', 60000, 'right')
        let rs = await axios.get('../../app-data/export/totales?flag=' + now);
        if (rs.data.status == 'ok') {
            $.notifyClose();
            notify('<b>Reporte Generado correctamente</b>.<br><div id="downloadXls" class="shadow-sm w100"><a href="../../app-data/' + rs.data.archivo + '" class="btn btn-custom px-3 btn-sm mt-2 fontq" target="_blank" download><div class="d-flex align-items-center"><span>Descargar</span><i class="bi bi-file-earmark-arrow-down ml-1 font1"></i></div></a></div>', 'warning', 0, 'right')
            button.disabled = false;
            let downloadXls = document.getElementById('downloadXls');
            downloadXls.addEventListener('click', function () {
                $.notifyClose();
            }, false);
        } else {
            $.notifyClose();
            notify('Error al generar el reporte', 'danger', 0, 'right')
            button.disabled = false;
        }
    });
}

const getHorasTotales = async (data, dataATyTr) => {

    let cardTotales = document.getElementById('card_totales');
    cardTotales.innerHTML = '';

    if (!data) {
        cardTotales.innerHTML = '';
        return false;
    }

    if (dataATyTr) {
        let html2 = '<div class="form-row animate__animated animate__fadeIn mt-2 mb-0">';
        let HsTrEnDecimal = (dataATyTr.HsTrEnDecimal);
        HsTrEnDecimal = (Math.round((HsTrEnDecimal + Number.EPSILON) * 100) / 100).toFixed(2);
        let HsATEnDecimal = (dataATyTr.HsATEnDecimal);
        HsATEnDecimal = (Math.round((HsATEnDecimal + Number.EPSILON) * 100) / 100).toFixed(2);
        html2 += `
            <div class="col-12">
                <div class="w-100 d-flex" style="border:1px solid #ccc !important">
                <div class="card mb-sm-0 mb-2 w-100">
                    <div class="card-header border-0 pb-0 text-center">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="font08 text-uppercase bg-light text-custom border fw5 p-1 px-3 opa8 d-block d-sm-none">A Trabajar</div>
                            <div class="font08 text-uppercase bg-light text-custom border fw5 p-1 px-3 opa8 d-none d-sm-block">Horas a Trabajar</div>
                        </div>
                    </div>
                    <div class="card-body py-1">
                        <div class="d-flex flex-column justify-content-center align-items-center">
                            <div class="font-weight-bold font11 enHoras">${dataATyTr.HsATEnHoras}</div>
                            <div class="font-weight-bold font11 enDecimales">${HsATEnDecimal}</div>
                        </div>
                    </div>
                </div>
                <div class="card w-100">
                    <div class="card-header border-0 pb-0 text-center">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="font08 text-uppercase bg-light text-custom border fw5 p-1 px-3 opa8 d-block d-sm-none">Trabajadas</div>
                            <div class="font08 text-uppercase bg-light text-custom border fw5 p-1 px-3 opa8 d-none d-sm-block">Horas Trabajadas</div>
                        </div>
                    </div>
                    <div class="card-body py-1">
                        <div class="d-flex flex-column justify-content-center align-items-center">
                            <div class="font-weight-bold font11 enHoras">${dataATyTr.HsTrEnHoras}</div>
                            <div class="font-weight-bold font11 enDecimales">${HsTrEnDecimal}</div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            `;
        html2 += '</div>';
        cardTotales.innerHTML += html2;
    }

    if (data.length > 0) {
        let col = (data.length < 3) ? 6 : 4;
        let html = '<div class="form-row animate__animated animate__fadeIn mb-2 mt-1">';
        data.forEach(element => {
            let colorAuto = '';
            colorAuto = (element.EnHoras2 == '00:00') ? 'text-danger' : '';
            let EnHorasDecimal = (element.EnHorasDecimal);
            EnHorasDecimal = (Math.round((EnHorasDecimal + Number.EPSILON) * 100) / 100).toFixed(2);
            let EnHorasDecimal2 = (element.EnHorasDecimal2);
            EnHorasDecimal2 = (Math.round((EnHorasDecimal2 + Number.EPSILON) * 100) / 100).toFixed(2);
            html += `
                <div class="col-12 col-md-6 col-lg-${col} mt-2">
                    <div class="card" style="border:1px solid #ccc !important">
                        <div class="card-header border-0 pb-0">
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="font08 text-uppercase bg-light text-custom border fw5 p-1 px-3 opa8 d-block d-sm-none">${element.THoDesc2}</div>
                                <div class="font08 text-uppercase bg-light text-custom border fw5 p-1 px-3 opa8 d-none d-sm-block">${element.THoDesc}</div>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-2 d-flex justify-content-center">
                            <div class="d-flex flex-column justify-content-center align-items-center">
                                <div class="font07 text-secondary ">Hechas</div>
                                <div class="font11 enDecimales">${EnHorasDecimal}</div>
                                <div class="font11 enHoras">${element.EnHoras}</div>
                            </div>
                            <div class="ml-3 d-flex flex-column justify-content-center align-items-center">
                                <div class="font07 text-secondary">Autorizadas</div>
                                <div class="font-weight-bold font11 enHoras ${colorAuto}">${element.EnHoras2}</div>
                                <div class="font-weight-bold font11 enDecimales ${colorAuto}">${EnHorasDecimal2}</div>
                            </div>
                        </div>
                    </div>
                </div>
                `;
        });
        html += '</div>';
        cardTotales.innerHTML += html;
    }


    // if (!data.length === 0) {
    //     $('#tabla_totales').DataTable().destroy();
    //     return false;
    // }

    // if ($.fn.DataTable.isDataTable('#tabla_totales')) {
    //     $('#tabla_totales').DataTable().destroy();
    // }

    // $('#tabla_totales').DataTable({
    //     bProcessing: true,
    //     dom: "<'row'" +
    //         "<'col-12 d-flex align-items-center'<'font09 title-totales'>>>" +
    //         "<'row '<'col-12'<'border radius p-1 table-responsive't>>>",
    //     data: data,
    //     columns: [
    //         {
    //             data: 'HoraCodi', className: '', targets: '', title: 'COD',
    //             "render": function (data, type, row, meta) {
    //                 return data;
    //             },
    //         },
    //         {
    //             data: 'THoDesc', className: '', targets: '', title: 'TIPO DE HORA',
    //             "render": function (data, type, row, meta) {
    //                 return data;
    //             },
    //         },
    //         {
    //             data: 'Cantidad', className: 'text-right', targets: '', title: 'CANT',
    //             "render": function (data, type, row, meta) {
    //                 return data;
    //             },
    //         },
    //         {
    //             data: '', className: 'text-right', targets: '', title: '<div class="hint--right hint--rounded hint--no-arrow hint--default hint--no-shadow" aria-label="Horas Hechas">HECHAS</div>',
    //             "render": function (data, type, row, meta) {
    //                 // let array = row.Totales
    //                 let html = '';
    //                 // let EnHorasDecimal = (row.EnHorasDecimal);
    //                 // EnHorasDecimal = (Math.round((EnHorasDecimal + Number.EPSILON) * 100) / 100).toFixed(2);
    //                 // html += `< div class="enDecimales" > ${ (EnHorasDecimal) }</div > `
    //                 html += `<div class="enHoras">${row.EnHoras}</div>`
    //                 return html;
    //             },
    //         },
    //         {
    //             data: '', className: 'text-right bg-light', targets: '', title: '<div class="hint--right hint--rounded hint--no-arrow hint--default hint--no-shadow" aria-label="Horas Autorizadas">AUTOR</div>',
    //             "render": function (data, type, row, meta) {
    //                 // let array = row.Totales
    //                 let html = '';
    //                 // let EnHorasDecimal = (row.EnHorasDecimal);
    //                 // EnHorasDecimal = (Math.round((EnHorasDecimal + Number.EPSILON) * 100) / 100).toFixed(2);
    //                 // html += `< div class="enDecimales" > ${ (EnHorasDecimal) }</div > `
    //                 html += `<div class="enHoras"> ${row.EnHoras2}</div>`
    //                 return html;
    //             },
    //         },
    //         {
    //             data: '', className: 'w-100', targets: '', title: '',
    //             "render": function (data, type, row, meta) {
    //                 return '';
    //             },
    //         },
    //     ],
    //     paging: false,
    //     info: false,
    //     searching: false,
    //     ordering: false,
    //     language: {
    //         "url": "../../js/DataTableSpanishShort2.json" + "?" + vjs(),
    //     },
    //     // Eventos de la tabla
    //     drawCallback: function () {
    //         $('.title-totales').html('<div>Total General de Horas</div>').addClass('w-100 text-right my-3');
    //     },
    // });
}
const getNovedadesTotales = async (data) => {

    let cardTotales = document.getElementById('card_totales_nove');
    cardTotales.innerHTML = '';
    if (!data) {
        cardTotales.innerHTML = '';
        return false;
    }
    let col = (data.length < 3) ? 6 : 4;
    let html = '<div class="form-row animate__animated animate__fadeIn mb-3">';
    data.forEach(element => {
        let colorAuto = '';
        colorAuto = (element.EnHoras2 == '00:00') ? 'text-danger' : '';
        let EnHorasDecimal = (element.EnHorasDecimal);
        EnHorasDecimal = (Math.round((EnHorasDecimal + Number.EPSILON) * 100) / 100).toFixed(2);
        let EnHorasDecimal2 = (element.EnHorasDecimal2);
        EnHorasDecimal2 = (Math.round((EnHorasDecimal2 + Number.EPSILON) * 100) / 100).toFixed(2);
        html += `
        <div class="col-12 col-md-6 col-lg-${col} mt-2">
            <div class="card" style="border:1px solid #ccc !important">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="font08 text-uppercase bg-light text-custom border fw5 p-1 px-3 opa8">${element.NovDesc}</div>
                    </div>
                </div>
                <div class="card-body pt-1 pb-2">
                    <div class="d-flex flex-column justify-content-center align-items-center">
                        <div class="font07 text-secondary d-none">Horas</div>
                        <div class="font-weight-bold font11 enHoras ${colorAuto}">${element.EnHoras} <span class="font07">Hs.</span> </div>
                        <div class="font-weight-bold font11 enDecimales ${colorAuto}">${EnHorasDecimal} <span class="font07">Hs.</span></div>
                    </div>
                    <div class="d-flex flex-column justify-content-center align-items-center">
                        <div class="font07 text-secondary d-none">Cantidad</div>
                        <div class="font09 ${colorAuto}"><span class="font07">Cant:</span> ${element.Cantidad}</div>
                    </div>
                </div>
            </div>
        </div>
        `;
    });
    html += '</div>';
    cardTotales.innerHTML = html;
}