// $(".Filtros").prop('disabled', true);
const LS_SOLOHCALC = LS_PREFIX + 'soloHCalc';
const LS_SOLOFIC = LS_PREFIX + 'soloFic';
const LS_FICDIAL = LS_PREFIX + 'ficDiaL';
const LS_FICFALTA = LS_PREFIX + 'ficFalta';
const LS_LEGDE = LS_PREFIX + 'legDe';
const LS_LEGHA = LS_PREFIX + 'legHa';
const LS_FICNOVT = LS_PREFIX + 'ficNovT';
const LS_FICNOVI = LS_PREFIX + 'ficNovI';
const LS_FICNOVS = LS_PREFIX + 'ficNovS';
const LS_FICNOVA = LS_PREFIX + 'ficNovA';
const LS_PERSONAL = LS_PREFIX + 'personal';
const LS_EMPRESAS = LS_PREFIX + 'empresas';
const LS_PLANTAS = LS_PREFIX + 'plantas';
const LS_SECCION = LS_PREFIX + 'seccion';
const LS_SECTORES = LS_PREFIX + 'sectores';
const LS_GRUPOS = LS_PREFIX + 'grupos';
const LS_SUCURSAL = LS_PREFIX + 'sucursal';
const LS_TIPOPER = LS_PREFIX + 'tipoper';
const LS_NOVEDAD = LS_PREFIX + 'novedad';
const LS_DATERANGE = LS_PREFIX + 'daterange';

const lsDataSelect = function (LS_KEY, selector) {
    const data = ls.get(LS_KEY);
    if (data != null && data.length > 0) {
        if (selector === '#datoNovedad') {
            return parseInt(data[0].id);
        }
        return data.map(function (value) {
            return parseInt(value.id);
        });
    } else {
        return $(selector).val();
    }
}

const status_ws = function () {
    axios.get("/" + $("#_homehost").val() + '/app-data/custom/status_ws', {
        params: {
            status: 'ws',
        }
    }).then(function (response) {
        $.notifyClose();
        const status_ws = response?.data?.status ?? '';
        const mensaje_ws = response?.data?.Mensaje ?? '';
        if (status_ws === 'Error') {
            notify(mensaje_ws, 'info', 2000, 'right')
        }
    });
};
status_ws();
function ActualizaTablas() {
    actualizarContadorFiltros();
    $('.modal-footer .result').html('');
    if ($("#Visualizar").is(":checked")) {
        reloadDataTable('#GetFechas');
        $("#GetFechas_paginate .page-link").addClass('border border-0');
    } else {
        reloadDataTable('#GetPersonal');
        $('#Per2').addClass('d-none')
        $('.pers_legajo').removeClass('d-none')
    };
};
function ActualizaTablas2() {
    $('.modal-footer .result').html('');
    if ($("#Visualizar").is(":checked")) {
        reloadDataTable('#GetFechas', true);
        $("#GetFechas_paginate .page-link").addClass('border border-0');
    } else {
        reloadDataTable('#GetPersonal', true);
        $('#Per2').addClass('d-none')
        $('.pers_legajo').removeClass('d-none')
    };
    actualizarContadorFiltros();
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
    ls.set(LS_FICFALTA, $('#FicFalta').is(":checked"));
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
    ls.set(LS_FICNOVT, $('#FicNovT').is(":checked"));
    if ($("#FicNovT").is(":checked")) {
        $('#datoFicNovT').val('1')
        $('#datoFicNovA').val('0')
        $('#FicNovA').prop('checked', false)
        $('#FicNovA').prop('disabled', true)
        ActualizaTablas2()
        cleanDatoNovedad();
    } else {
        cleanDatoNovedad();
        $('#datoFicNovT').val('0')
        $('#FicNovA').prop('disabled', false)
        ActualizaTablas2()
    }
});
$('#datoFicNovI').val('0');
$("#FicNovI").change(function () {
    ls.set(LS_FICNOVI, $('#FicNovI').is(":checked"));
    if ($("#FicNovI").is(":checked")) {
        cleanDatoNovedad();
        $('#datoFicNovI').val('1')
        $('#datoFicNovA').val('0')
        $('#FicNovA').prop('checked', false)
        $('#FicNovA').prop('disabled', true)
        ActualizaTablas2()
    } else {
        cleanDatoNovedad();
        $('#datoFicNovI').val('0')
        $('#FicNovA').prop('disabled', false)
        ActualizaTablas2()
    }
});
$('#datoFicNovS').val('0');
$("#FicNovS").change(function () {
    ls.set(LS_FICNOVS, $('#FicNovS').is(":checked"));
    if ($("#FicNovS").is(":checked")) {
        cleanDatoNovedad();
        $('#datoFicNovS').val('1')
        $('#datoFicNovA').val('0')
        $('#FicNovA').prop('checked', false)
        $('#FicNovA').prop('disabled', true)
        ActualizaTablas2()
    } else {
        cleanDatoNovedad();
        $('#datoFicNovS').val('0')
        $('#FicNovA').prop('disabled', false)
        ActualizaTablas2()
    }
});
$('#datoFicNovA').val('0');
$("#FicNovA").change(function () {
    ls.set(LS_FICNOVA, $('#FicNovA').is(":checked"));
    if ($("#FicNovA").is(":checked")) {
        cleanDatoNovedad();
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
        cleanDatoNovedad();
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

const setLSFilter_old = function () {
    const getSoloHCalc = ls.get(LS_SOLOHCALC);
    const getSoloFic = ls.get(LS_SOLOFIC);
    const getFicFalta = ls.get(LS_FICFALTA);
    const getLegDe = ls.get(LS_LEGDE);
    const getLegHa = ls.get(LS_LEGHA);
    const getFicNovT = ls.get(LS_FICNOVT);
    const getFicNovI = ls.get(LS_FICNOVI);
    const getFicNovS = ls.get(LS_FICNOVS);
    const getFicNovA = ls.get(LS_FICNOVA);
    const getFicDiaL = ls.get(LS_FICDIAL);

    if (getSoloHCalc === null) {
        ls.set(LS_SOLOHCALC, $('#SoloHCalc').is(":checked"));
    } else {
        $('#SoloHCalc').prop('checked', getSoloHCalc);
    }

    if (getSoloFic === null) {
        ls.set(LS_SOLOFIC, $('#SoloFic').is(":checked"));
    } else {
        $('#SoloFic').prop('checked', getSoloFic);
    }

    if (getFicFalta === null) {
        ls.set(LS_FICFALTA, $('#FicFalta').is(":checked"));
    } else {
        $('#FicFalta').prop('checked', getFicFalta);
    }

    if (getLegDe === null) {
        ls.set(LS_LEGDE, $('#LegDe').val());
    } else {
        Select2Value(getLegDe, getLegDe, '#LegDe');
    }

    if (getLegHa === null) {
        ls.set(LS_LEGHA, $('#LegHa').val());
    } else {
        Select2Value(getLegHa, getLegHa, '#LegHa');
    }

    if (getFicNovT === null) {
        ls.set(LS_FICNOVT, $('#FicNovT').is(":checked"));
    } else {
        $('#FicNovT').prop('checked', getFicNovT);
        $('#datoFicNovT').val(getFicNovT ? '1' : '0');
    }

    if (getFicNovI === null) {
        ls.set(LS_FICNOVI, $('#FicNovI').is(":checked"));
    } else {
        $('#FicNovI').prop('checked', getFicNovI);
        $('#datoFicNovI').val(getFicNovI ? '1' : '0');
    }

    if (getFicNovS === null) {
        ls.set(LS_FICNOVS, $('#FicNovS').is(":checked"));
    } else {
        $('#FicNovS').prop('checked', getFicNovS);
        $('#datoFicNovS').val(getFicNovS ? '1' : '0');
    }

    if (getFicNovA === null) {
        ls.set(LS_FICNOVA, $('#FicNovA').is(":checked"));
    } else {
        $('#FicNovA').prop('checked', getFicNovA);
        $('#datoFicNovA').val(getFicNovA ? '1' : '0');
    }

    if (getFicDiaL === null) {
        ls.set(LS_FICDIAL, $('#FicDiaL').is(":checked"));
    } else {
        $('#FicDiaL').prop('checked', getFicDiaL);
        $('#FicDiaLFiltro').prop('checked', getFicDiaL);
        $('#datoFicDiaL').val(getFicDiaL ? '1' : '0');
    }

}

const FILTROS_LS_MAP = [
    // Checkboxes simples
    { key: LS_SOLOHCALC, selector: '#SoloHCalc', type: 'check' },
    { key: LS_SOLOFIC, selector: '#SoloFic', type: 'check' },
    { key: LS_FICFALTA, selector: '#FicFalta', type: 'check' },

    // Checkboxes con dato oculto
    { key: LS_FICNOVT, selector: '#FicNovT', dato: '#datoFicNovT', type: 'check-dato' },
    { key: LS_FICNOVI, selector: '#FicNovI', dato: '#datoFicNovI', type: 'check-dato' },
    { key: LS_FICNOVS, selector: '#FicNovS', dato: '#datoFicNovS', type: 'check-dato' },
    { key: LS_FICNOVA, selector: '#FicNovA', dato: '#datoFicNovA', type: 'check-dato' },

    // FicDiaL: check-dato + sincroniza un segundo checkbox
    { key: LS_FICDIAL, selector: '#FicDiaL', dato: '#datoFicDiaL', extra: '#FicDiaLFiltro', type: 'check-dato' },

    // Select2
    { key: LS_LEGDE, selector: '#LegDe', type: 'select2' },
    { key: LS_LEGHA, selector: '#LegHa', type: 'select2' },
];

const setLSFilter = () => {
    FILTROS_LS_MAP.forEach(({ key, selector, dato, extra, type }) => {
        const stored = ls.get(key);
        if (stored === null) {
            const val = type === 'select2'
                ? $(selector).val()
                : $(selector).is(':checked');
            ls.set(key, val);
            return;
        }

        if (type === 'check') {
            $(selector).prop('checked', stored);
        }

        if (type === 'check-dato') {
            $(selector).prop('checked', stored);
            $(dato).val(stored ? '1' : '0');
            if (extra) $(extra).prop('checked', stored);
        }

        if (type === 'select2') {
            Select2Value(stored, stored, selector);
        }
    });
};

setLSFilter();
_Filtros();

$("._filtroLegDeHa").change(function (e) {
    e.preventDefault();
    $('#Per').val(null).trigger("change");
    ls.set(LS_LEGDE, $('#LegDe').val());
    ls.set(LS_LEGHA, $('#LegHa').val());
    // _Filtros();
});
$("._filtro").change(function (e) {
    e.preventDefault();
    _Filtros()
});
$("#SoloHCalc").change(function (e) {
    e.preventDefault();
    ls.set(LS_SOLOHCALC, $('#SoloHCalc').is(":checked"));
    _Filtros()
    ActualizaTablas()
});
$("#SoloFic").change(function (e) {
    e.preventDefault();
    ls.set(LS_SOLOFIC, $('#SoloFic').is(":checked"));
    _Filtros()
    ActualizaTablas()
});

const GetPersonal = $('#GetPersonal').DataTable({
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
        url: "/" + $("#_homehost").val() + "/app-data/custom/gen-per-fichas",
        type: "POST",
        "data": function (data) {
            data._l = $("#_l").val();
            data.Per = lsDataSelect(LS_PERSONAL, '.selectjs_personal');
            data.Per2 = $("#Per2").val();
            data.Tipo = $("#Tipo").val();
            data.Emp = lsDataSelect(LS_EMPRESAS, '#Emp');
            data.Plan = lsDataSelect(LS_PLANTAS, '#Plan');
            data.Sect = lsDataSelect(LS_SECTORES, '#Sect');
            data.Sec2 = lsDataSelect(LS_SECCION, '#Sec2');
            data.Grup = lsDataSelect(LS_GRUPOS, '#Grup');
            data.Sucur = lsDataSelect(LS_SUCURSAL, '#Sucur');
            const getLSDaterange = ls.get(LS_DATERANGE);
            if (getLSDaterange) {
                const [startDate, endDate] = getLSDaterange.split(' - ');
                data._dr = `${startDate} al ${endDate}`;
            } else {
                data._dr = $("#_dr").val();
            }
            data.FicDiaL = ($("#FicDiaL").is(":checked")) ? 1 : 0;
            data.FicFalta = $("#datoFicFalta").val();
            data.FicNovT = $("#datoFicNovT").val();
            data.FicNovI = $("#datoFicNovI").val();
            data.FicNovS = $("#datoFicNovS").val();
            data.FicNovA = $("#datoFicNovA").val();
            data.Fic3Nov = lsDataSelect(LS_NOVEDAD, '#datoNovedad');
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
    language: DT_SPANISH_SHORT2,
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

GetPersonal.on('draw.dt', function (e, settings) {

    if ((settings.json.recordsTotal > 0)) {
        $('#Visualizar').prop('disabled', false)
    } else {
        $('#Visualizar').prop('disabled', true)
    }
    (!$('#Visualizar').is(':checked')) ? textResult(settings.json.recordsTotal, '.modal-footer .result', 'legajo') : '';
    $("#GetPersonal thead").remove();

    $(".dataTables_info").addClass('text-secondary');
    $('#pagLega').removeClass('d-none')

    if (settings.iDraw === 1) {
        atajosTeclado();

        $('#GetGeneral').DataTable({
            "initComplete": function (settings, json) {
                $("#Refresh").prop('disabled', false);
                $('#trash_all').removeClass('invisible');
                fadeInOnly('#GetGeneralTable')
                fadeInOnly('#pagLega')
            },
            createdRow: function (row, data, dataIndex) {
                $(row).addClass('fadeIn');
                if (data.Cierre === true) {
                    $(row).addClass('text-light');
                }
            },
            // stateSave: -1,
            lengthMenu: [[30, 60, 90, 120], [30, 60, 90, 120]],
            bProcessing: true,
            serverSide: true,
            deferRender: true,
            searchDelay: 1500,
            ajax: {
                url: "/" + $("#_homehost").val() + "/app-data/custom/gen-general",
                type: "POST",
                "data": function (data) {
                    data.Per = lsDataSelect(LS_PERSONAL);
                    data.Tipo = $("#Tipo").val();
                    data.Emp = lsDataSelect(LS_EMPRESAS, '#Emp');
                    data.Plan = lsDataSelect(LS_PLANTAS, '#Plan');
                    data.Sect = lsDataSelect(LS_SECTORES, '#Sect');
                    data.Sec2 = lsDataSelect(LS_SECCION, '#Sec2');
                    data.Grup = lsDataSelect(LS_GRUPOS, '#Grup');
                    data.Sucur = lsDataSelect(LS_SUCURSAL, '#Sucur');
                    data._dr = $("#_dr").val();
                    data._l = $("#_l").val();
                    data.FicDiaL = ($("#FicDiaL").is(":checked")) ? 1 : 0;
                    data.FicFalta = $("#datoFicFalta").val();
                    data.FicNovT = $("#datoFicNovT").val();
                    data.FicNovI = $("#datoFicNovI").val();
                    data.FicNovS = $("#datoFicNovS").val();
                    data.FicNovA = $("#datoFicNovA").val();
                    data.Fic3Nov = lsDataSelect(LS_NOVEDAD, '#datoNovedad');
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
            language: DT_SPANISH_SHORT2,
        });
        $('#GetGeneral').DataTable().on('draw.dt', function () {

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
        // 
    },
    "drawCallback": function (settings) {

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
        url: "/" + $("#_homehost").val() + "/app-data/custom/gen-fechas-fichas",
        type: "POST",
        "data": function (data) {
            data.Per = lsDataSelect(LS_PERSONAL);
            data.Tipo = $("#Tipo").val();
            data.Emp = lsDataSelect(LS_EMPRESAS, '#Emp');
            data.Plan = lsDataSelect(LS_PLANTAS, '#Plan');
            data.Sect = lsDataSelect(LS_SECTORES, '#Sect');
            data.Sec2 = lsDataSelect(LS_SECCION, '#Sec2');
            data.Grup = lsDataSelect(LS_GRUPOS, '#Grup');
            data.Sucur = lsDataSelect(LS_SUCURSAL, '#Sucur');
            data._dr = $("#_dr").val();
            data._l = $("#_l").val();
            data.FicDiaL = ($("#FicDiaL").is(":checked")) ? 1 : 0;
            data.FicFalta = $("#datoFicFalta").val();
            data.FicNovT = $("#datoFicNovT").val();
            data.FicNovI = $("#datoFicNovI").val();
            data.FicNovS = $("#datoFicNovS").val();
            data.FicNovA = $("#datoFicNovA").val();
            data.Fic3Nov = lsDataSelect(LS_NOVEDAD, '#datoNovedad');
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
    language: DT_SPANISH_SHORT2,
});
$('#GetFechas').DataTable().on('draw.dt', function (e, settings) {
    ($('#Visualizar').is(':checked')) ? textResult(settings.json.recordsTotal, '.modal-footer .result', 'día') : '';
    if (settings.iDraw === 1) {
        $('#GetGeneralFecha').DataTable({
            "drawCallback": function (settings) {

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
            dom: 'ltip',
            ajax: {
                url: "/" + $("#_homehost").val() + "/app-data/custom/gen-general-fecha",
                type: "POST",
                "data": function (data) {
                    // console.log(data);
                    data._f = $("#_f").val();
                    data.Per = lsDataSelect(LS_PERSONAL);
                    data.Tipo = $("#Tipo").val();
                    data.Emp = lsDataSelect(LS_EMPRESAS, '#Emp');
                    data.Plan = lsDataSelect(LS_PLANTAS, '#Plan');
                    data.Sect = lsDataSelect(LS_SECTORES, '#Sect');
                    data.Sec2 = lsDataSelect(LS_SECCION, '#Sec2');
                    data.Grup = lsDataSelect(LS_GRUPOS, '#Grup');
                    data.Sucur = lsDataSelect(LS_SUCURSAL, '#Sucur');
                    data._dr = $("#_dr").val();
                    data._l = $("#_l").val();
                    data.FicDiaL = ($("#FicDiaL").is(":checked")) ? 1 : 0;
                    data.FicFalta = $("#datoFicFalta").val();
                    data.FicNovT = $("#datoFicNovT").val();
                    data.FicNovI = $("#datoFicNovI").val();
                    data.FicNovS = $("#datoFicNovS").val();
                    data.FicNovA = $("#datoFicNovA").val();
                    data.Fic3Nov = lsDataSelect(LS_NOVEDAD, '#datoNovedad');
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
            language: DT_SPANISH_SHORT2
        });
        $('#GetGeneralFecha').DataTable().on('draw.dt', function (e, settings) {

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
    const endDate = picker.endDate.format('DD/MM/YYYY');
    const startDate = picker.startDate.format('DD/MM/YYYY');
    $('#_drFiltro').data('daterangepicker').setStartDate(startDate);
    $('#_drFiltro').data('daterangepicker').setEndDate(endDate);
    ls.set(LS_DATERANGE, startDate + ' - ' + endDate);
    CheckSesion()
    ActualizaTablas()
})

// // Cuando el input pierde el foco (mouse sale o tab)
// $('#_dr').on('blur', function () {
//     const value = $(this).val();
//     console.log('Blur event triggered. Value:', value);
//     if (!value || value.trim() === '') {
//         ls.remove(LS_DATERANGE);
//         CheckSesion();
//         ActualizaTablas();
//         return;
//     }

//     // Intentar parsear el rango de fechas manual
//     const dates = value.split(' al ');
//     if (dates.length === 2) {
//         const startDate = moment(dates[0], 'DD/MM/YYYY');
//         const endDate = moment(dates[1], 'DD/MM/YYYY');

//         if (startDate.isValid() && endDate.isValid()) {
//             // Actualizar el picker con las fechas manuales
//             const picker = $(this).data('daterangepicker');
//             if (picker) {
//                 picker.setStartDate(startDate);
//                 picker.setEndDate(endDate);
//             }

//             ls.set(LS_DATERANGE, value);

//             // Actualizar el otro picker
//             const pickerFiltro = $('#_drFiltro').data('daterangepicker');
//             if (pickerFiltro) {
//                 pickerFiltro.setStartDate(startDate);
//                 pickerFiltro.setEndDate(endDate);
//                 $('#_drFiltro').val(value);
//             }

//             CheckSesion();
//             ActualizaTablas();
//         }
//     }
// });

function processDateRangeChange(input, source = 'manual') {
    if (manualUpdateInProgress) return;

    const $input = $(input);
    const value = $input.val().trim();

    if (!value || value === '') {
        ls.remove(LS_DATERANGE);

        // Limpiar ambos pickers
        const picker1 = $('#_dr').data('daterangepicker');
        const picker2 = $('#_drFiltro').data('daterangepicker');

        if (picker1) {
            picker1.setStartDate(null);
            picker1.setEndDate(null);
            $('#_dr').val('');
        }

        if (picker2) {
            picker2.setStartDate(null);
            picker2.setEndDate(null);
            $('#_drFiltro').val('');
        }

        CheckSesion();
        ActualizaTablas();
        return;
    }

    // Validar formato
    const parts = value.split(/\s*al\s*/);
    if (parts.length !== 2) return;

    const start = moment(parts[0], ['DD/MM/YYYY', 'D/M/YYYY'], true);
    const end = moment(parts[1], ['DD/MM/YYYY', 'D/M/YYYY'], true);

    if (!start.isValid() || !end.isValid() || end.isBefore(start)) return;

    manualUpdateInProgress = true;

    try {
        const startStr = start.format('DD/MM/YYYY');
        const endStr = end.format('DD/MM/YYYY');
        const rangeText = startStr + ' al ' + endStr;

        // Determinar cuál es el picker que cambió
        const isMainPicker = $input.attr('id') === '_dr';
        const mainPicker = isMainPicker ? $input : $('#_dr');
        const filtroPicker = isMainPicker ? $('#_drFiltro') : $input;

        // Actualizar valor de ambos inputs
        mainPicker.val(rangeText);
        filtroPicker.val(rangeText);

        // Actualizar pickers
        const picker1 = mainPicker.data('daterangepicker');
        const picker2 = filtroPicker.data('daterangepicker');

        if (picker1) {
            picker1.setStartDate(start);
            picker1.setEndDate(end);
        }

        if (picker2) {
            picker2.setStartDate(start);
            picker2.setEndDate(end);
        }

        // Guardar en localStorage
        ls.set(LS_DATERANGE, rangeText);

        CheckSesion();
        ActualizaTablas();
    } finally {
        setTimeout(() => {
            manualUpdateInProgress = false;
        }, 200);
    }
}

// Evento blur (edición manual)
$('#_dr').on('blur', function () {
    processDateRangeChange(this, 'blur');
});
// Evento Enter
$('#_dr').on('keypress', function (e) {
    if (e.which === 13) {
        e.preventDefault();
        $(this).blur();
    }
});

// Evento blur (edición manual)
$('#_drFiltro').on('blur', function () {
    processDateRangeChange(this, 'blur');
});

// Evento Enter
$('#_drFiltro').on('keypress', function (e) {
    if (e.which === 13) {
        e.preventDefault();
        $(this).blur();
    }
});

// Variable para evitar bucles
let manualUpdateInProgress = false;

$('#_drFiltro').on('apply.daterangepicker', function (ev, picker) {
    const endDate = picker.endDate.format('DD/MM/YYYY');
    const startDate = picker.startDate.format('DD/MM/YYYY');
    $('#_dr').data('daterangepicker').setStartDate(startDate);
    $('#_dr').data('daterangepicker').setEndDate(endDate);
    ls.set(LS_DATERANGE, startDate + ' - ' + endDate);
    CheckSesion();
    ActualizaTablas();
});
$("#FicDiaLFiltro").change(function (e) {
    e.preventDefault();
    if ($("#FicDiaLFiltro").is(":checked")) {
        $("#FicDiaL").prop('checked', true);
        ls.set(LS_FICDIAL, true);
    } else {
        $("#FicDiaL").prop('checked', false);
        ls.set(LS_FICDIAL, false);
    }
    ActualizaTablas();
});
$("#FicDiaL").change(function (e) {
    e.preventDefault();
    if ($("#FicDiaL").is(":checked")) {
        $("#FicDiaLFiltro").prop('checked', true);
        ls.set(LS_FICDIAL, true);
    } else {
        $("#FicDiaLFiltro").prop('checked', false);
        ls.set(LS_FICDIAL, false);
    }
    CheckSesion();
    ActualizaTablas();
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
const cleanDatoNovedad = () => {
    $('#datoNovedad').val(null).trigger("change");
    ls.remove(LS_NOVEDAD);
}
const FILTROS_KEYS = {
    check: [LS_FICDIAL, LS_SOLOHCALC, LS_SOLOFIC, LS_FICFALTA, LS_LEGDE, LS_LEGHA, LS_FICNOVT, LS_FICNOVI, LS_FICNOVS, LS_FICNOVA],
    select: [LS_EMPRESAS, LS_PLANTAS, LS_SECTORES, LS_SECCION, LS_GRUPOS, LS_SUCURSAL, LS_PERSONAL, LS_TIPOPER, LS_NOVEDAD]
};

const actualizarContadorFiltros = () => {
    const total =
        FILTROS_KEYS.select.filter(k => ls.get(k)?.length > 0).length +
        FILTROS_KEYS.check.filter(k => ls.get(k)).length;

    $('.Filtros').text(total ? `Filtros (${total})` : 'Filtros');
    return total;
};
actualizarContadorFiltros();