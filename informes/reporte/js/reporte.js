const homehost = $("#_homehost").val();
const LS_FILTROS = homehost + '_reporte_totales_filtros';

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
});
$('input[name="VPorFormato"]').on('change', function () {
    if ($(this).val() == 'enDecimal') {
        $('.enHoras').addClass('d-none').removeClass('animate__animated animate__fadeIn');
        $('.enDecimales').removeClass('d-none').addClass('animate__animated animate__fadeIn');
    } else {
        $('.enHoras').removeClass('d-none').addClass('animate__animated animate__fadeIn');
        $('.enDecimales').addClass('d-none').removeClass('animate__animated animate__fadeIn');
    }
});

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
    });

    let trash_allInputs = () => {
        $('#Filtros input').val('');
        $('#Filtros select').val(null).trigger('change');
        $('#Filtros .select2').val(null).trigger('change');
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
            "start": 0,
            "length": 1000
        }
        ls.set(LS_FILTROS, jsonData);
        getHoras();
        getNovedades();
    }
    let trash_allIn = document.getElementById('trash_allIn');
    trash_allIn.addEventListener('click', trash_allInputs, false);

    // console.log((jsonData));
    $('#_dr').on('apply.daterangepicker', function (ev, picker) {
        jsonData.FechIni = picker.startDate.format('YYYY-MM-DD');
        jsonData.FechaIni = picker.startDate.format('YYYY-MM-DD');
        jsonData.FechFin = picker.endDate.format('YYYY-MM-DD');
        jsonData.FechaFin = picker.endDate.format('YYYY-MM-DD');
        ls.set(LS_FILTROS, jsonData);
        getHoras();
        getNovedades();
    });

    getHoras();
    getNovedades();
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
            "<'row '<'col-12'<'border radius p-2 shadow-sm table-responsive't>>>" +
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
            },
            error: function () {
                $("#tabla_processing").css("display", "none");
            },
        },
        columns: [
            {
                data: 'Lega', className: '', targets: '', title: 'LEGAJO',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'LegApNo', className: '', targets: '', title: 'NOMBRE',
                "render": function (data, type, row, meta) {
                    return `<div class="text-truncate" style="min-width:250px; max-width:250px">${data}</div>`;
                },
            },
            {
                data: '', className: 'text-center', targets: '', title: 'COD',
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
                        EnHorasDecimal = Math.round((EnHorasDecimal + Number.EPSILON) * 100) / 100;
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
                        EnHorasDecimal2 = Math.round((EnHorasDecimal2 + Number.EPSILON) * 100) / 100;
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
        initComplete: function () {
            // $('.title').html('<span>Totales Horas</span>');
            $('.title').html('<div>Totales Horas</div>').addClass('w-100 text-right');
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
        drawCallback: function () {
            let formato = $('input[name="VPorFormato"]:checked').val();
            if (formato == 'enDecimal') {
                $('.enHoras').addClass('d-none');
                $('.enDecimales').removeClass('d-none');
            } else {
                $('.enHoras').removeClass('d-none');
                $('.enDecimales').addClass('d-none');
            }
            setTimeout(() => {
                loaderIn('#tabla', false);
            }, 100);
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
            "<'row '<'col-12'<'border radius p-2 shadow-sm table-responsive't>>>" +
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
            },
            error: function () {
                $("#tabla_novedades_processing").css("display", "none");
            },
        },
        columns: [
            {
                data: 'Lega', className: '', targets: '', title: 'LEGAJO',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'LegApNo', className: '', targets: '', title: 'NOMBRE',
                "render": function (data, type, row, meta) {
                    return `<div class="text-truncate" style="min-width:250px; max-width:250px">${data}</div>`;
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
                        EnHorasDecimal = Math.round((EnHorasDecimal + Number.EPSILON) * 100) / 100;
                        html += `<div class="enDecimales">${(EnHorasDecimal)}</div>`
                        html += `<div class="enHoras">${element.EnHoras}</div>`
                    });
                    return html;
                },
            },
            // {
            //     data: '', className: 'text-right bg-light minmax50', targets: '', title: '<div class="hint--right hint--rounded hint--no-arrow hint--default hint--no-shadow" aria-label="Horas Autorizadas" > AUTOR.</div>',
            //     "render": function (data, type, row, meta) {
            //         let array = row.Totales
            //         let html = '';
            //         array.forEach(element => {
            //             let EnHorasDecimal2 = (element.EnHorasDecimal2);
            //             EnHorasDecimal2 = Math.round((EnHorasDecimal2 + Number.EPSILON) * 100) / 100;
            //             html += `<div class="enDecimales">${(EnHorasDecimal2)}</div>`
            //             html += `<div class="enHoras">${element.EnHoras2}</div>`
            //         });
            //         return html;
            //     },
            // },
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
            $('.title-nove').html('<div>Totales Novedades</div>').addClass('w-100 text-right');
            $(".custom-select").select2({
                minimumResultsForSearch: Infinity,
            });
            loaderIn('#tabla_novedades', false);
        },
        preDrawCallback: function () {
            loaderIn('#tabla_novedades', true);
        },
        // al cambiar de pagina o cambiar el tamaño de la tabla mostrar en formato decimal o en horas
        drawCallback: function () {
            let formato = $('input[name="VPorFormato"]:checked').val();
            if (formato == 'enDecimal') {
                $('.enHoras').addClass('d-none');
                $('.enDecimales').removeClass('d-none');
            } else {
                $('.enHoras').removeClass('d-none');
                $('.enDecimales').addClass('d-none');
            }
            setTimeout(() => {
                loaderIn('#tabla_novedades', false);
            }, 100);
        }
    });
}
