const homehost = $("#_homehost").val();
const LS_FILTROS = homehost + '_filtros_reporte';

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
    });
    loaderIn('#_dr', false);
}
dateRange().then(() => {

    const getValuesDate = () => {
        let dateRange = $('#_dr').data('daterangepicker');
        let startDate = dateRange.startDate.format('YYYY-MM-DD');
        let endDate = dateRange.endDate.format('YYYY-MM-DD');
        return { startDate, endDate }
    }
    setTimeout(() => {
        $('#Filtros').modal('show');
    }, 100);

    const select2Results = (data) => {
        if (!data) return [];
        return data.map(item => {
            return {
                id: item.Cod,
                text: item.Desc,
                count: item.Count,
                data: item.data ?? '',
                listaObs: item.ListObs ?? '',
                html: `<div class="selectHover d-flex justify-content-between align-items-center"><div class="d-flex justify-content-start"><div><span class="font08">(${item.Cod})</span> ${item.Desc}</div> </div></div>`,
                // html: `<div class="selectHover d-flex justify-content-between align-items-center"><div class="d-flex justify-content-start"><div><span class="font08">(${item.Cod})</span> ${item.Desc}</div> </div>  <div class="badge badge-light">${(item.Count)}</div> </div>`,
            };
        })
    }
    const select2Data = (selector) => {
        try {
            if (!$(selector).hasClass("select2-hidden-accessible")) {
                throw new Error("No es un select2")
            }
            let a = $(selector).select2('data') ?? ''
            if (a.length > 0) {
                return a.map(item => item.id);
            }
            return [];
        } catch (error) {
            console.log(error.message);
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
                    console.log(jsonData);
                    return requestData = Object.assign({}, jsonData, { "Estruct": estruct, "Desc": params.term, [estruct]: [] });
                },
                processResults: function (data, params, page) {
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
        }).on('select2:unselect', function () {
            if (estruct == 'Sect') {
                jsonData['Sector'] = select2Data("#select_sector");
                $('#select_seccion').val(null).trigger('change').prop('disabled', true);
                jsonData['Sec2'] = [];
            }
            jsonData[estruct] = select2Data(selector);
            ls.set(LS_FILTROS, jsonData);
        }).on('select2:close', function () {
        });
    }

    $('#Filtros').on('shown.bs.modal', function () {
        ajaxSelect2("#select_empresa", "Empresas", "Empr");
        ajaxSelect2("#select_planta", "Plantas", "Plan");
        ajaxSelect2("#select_sector", "Sectores", "Sect");
        ajaxSelect2("#select_grupo", "Grupos", "Grup");
        ajaxSelect2("#select_sucursal", "Sucursales", "Sucu");
        ajaxSelect2("#select_personal", "Personal", "Lega");
        ajaxSelect2("#select_seccion", "Secciones", "Sec2");
        ajaxSelect2("#select_tipo", "Tipo de Personal", "Tipo");
    });

    let trash_allInputs = () => {
        $('#Filtros input').val('');
        $('#Filtros select').val(null).trigger('change');
        $('#Filtros .select2').val(null).trigger('change');
        jsonData = {
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
    }
    let trash_allIn = document.getElementById('trash_allIn');
    trash_allIn.addEventListener('click', trash_allInputs, false);

});
