const homehost = $("#_homehost").val();
const LS_FILTROS = homehost + '_reporte_totales_filtros';

ls.remove(LS_FILTROS);

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

    const now = () => new Date(); // fecha actual

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
            'Hoy': [now(), now()],
            'Ayer': [now(now().setDate(now().getDate() - 1)), now(now().setDate(now().getDate() - 1))],
            'Esta semana': [now(now().setDate(now().getDate() - now().getDay() + 1)), now()],
            'Semana Anterior': [now(now().setDate(now().getDate() - now().getDay() - 6)), now(now().setDate(now().getDate() - now().getDay()))],
            'Últimos 7 días': [now(now().setDate(now().getDate() - 6)), now()],
            'Este mes': [now(now().getFullYear(), now().getMonth(), 1), now(now().getFullYear(), now().getMonth() + 1, 0)],
            'Mes anterior': [now(now().getFullYear(), now().getMonth() - 1, 1), now(now().getFullYear(), now().getMonth(), 0)],
            'Últimos 30 días': [now(now().setDate(now().getDate() - 29)), now()],
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

if (ls.get(LS_FILTROS + 'VPor') == "horas") {
    $('#VHoras').prop('checked', true);
} else {
    $('#VNovedades').prop('checked', true);
}

$('input[name="VPor"]').on('change', function () {
    let verPor = document.querySelector('input[name="VPor"]:checked').value;
    ls.set(LS_FILTROS + 'VPor', (verPor));
});

dateRange().then(() => {

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
        "start": 0,
        "length": 1000
    }

    const ajaxSelect2 = (selector, placeholder, estruct) => {
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
            loaderIn('#tabla', true);
            $('#tabla').DataTable().ajax.reload();
        }).on('select2:unselect', function () {
            if (estruct == 'Sect') {
                jsonData['Sector'] = select2Data("#select_sector");
                $('#select_seccion').val(null).trigger('change').prop('disabled', true);
                jsonData['Sec2'] = [];
            }
            jsonData[estruct] = select2Data(selector);
            ls.set(LS_FILTROS, jsonData);
            loaderIn('#tabla', true);
            $('#tabla').DataTable().ajax.reload();
        }).on('select2:close', function () {

        });
        $("#Filtros").removeClass("invisible");
    }

    // $('#Filtros').on('shown.bs.modal', function () {
    ajaxSelect2("#select_empresa", "Empresas", "Empr");
    ajaxSelect2("#select_planta", "Plantas", "Plan");
    ajaxSelect2("#select_sector", "Sectores", "Sect");
    ajaxSelect2("#select_grupo", "Grupos", "Grup");
    ajaxSelect2("#select_sucursal", "Sucursales", "Sucu");
    ajaxSelect2("#select_personal", "Personal", "Lega");
    ajaxSelect2("#select_seccion", "Secciones", "Sec2");
    ajaxSelect2("#select_tipo", "Tipo de Personal", "Tipo");
    ajaxSelect2("#select_thora", "Tipos de Horas", "THora");
    ajaxSelect2("#select_nove", "Tipos de Horas", "Nove");
    // });

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
            "FechaFin": getValuesDate().endDate,
            "start": 0,
            "length": 1000
        }
        // $('#tabla').DataTable().ajax.reload();
    }
    let trash_allIn = document.getElementById('trash_allIn');
    trash_allIn.addEventListener('click', trash_allInputs, false);

    // console.log((jsonData));
    $('#_dr').on('apply.daterangepicker', function (ev, picker) {
        // reconstruir jsonData
        jsonData.FechaIni = picker.startDate.format('YYYY-MM-DD');
        jsonData.FechaFin = picker.endDate.format('YYYY-MM-DD');

        getHoras(ls.get(LS_FILTROS));
    });
    getHoras(jsonData);
});


const getHoras = (jsonData) => {


    jsonData.FechIni = jsonData.FechaIni;
    jsonData.FechFin = jsonData.FechaFin;
    jsonData.DTHoras = true;

    delete jsonData.start;
    delete jsonData.length;
    // delete jsonData.FechaIni;
    // delete jsonData.FechaFin;

    console.log(jsonData);

    if ($.fn.DataTable.isDataTable('#tabla')) {
        $('#tabla').DataTable().ajax.reload();
        return false;
    }

    $('#tabla').DataTable({
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
            url: "../../app-data/horas/totales",
            type: "POST",
            "data": function (data) {
                Object.assign(data, jsonData);
            },
            error: function () {
                $("#tabla_processing").css("display", "none");
            },
        },
        columns: [
            {
                data: 'Lega', className: '', targets: '', title: 'Legajo',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'LegApNo', className: '', targets: '', title: 'Nombre',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
        ],
        scrollX: true,
        scrollCollapse: true,
        paging: true,
        info: true,
        searching: false,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanishShort2.json" + "?" + vjs(),
        },
    });
    $('#tabla').on('init.dt', function (settings, json) {
    });
    $('#tabla').on('draw.dt', function (settings, json) {
        $(".dataTables_info").addClass('text-secondary');
        $(".custom-select").addClass('text-secondary bg-light');
        $.notifyClose();
        loaderIn('#tabla', false);
    });
    $('#tabla').on('page.dt', function () {
        CheckSesion()
        loaderIn('#tabla', true);
    });
}
