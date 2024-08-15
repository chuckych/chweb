// selectJS('selectjs_naciones', `/${HOMEHOST}/data/getNaciones.php`);

$('#liquid-tab').on('show.bs.tab', function (e) {
    tablePerInEg();
    tablePerPremio();
    tableOtrosConLeg();
});
$('#horarios-tab').on('show.bs.tab', function (e) {
    tablePerHoAlt();
});

$('#identifica-tab').on('show.bs.tab', function (e) {
    tableIdentifica();
});

$('#control-tab').on('show.bs.tab', function (e) {
    selectJS('.selectjs_regla', `/${HOMEHOST}/data/getReglaCo.php`);
});

$('#dispositivo-tab').on('show.bs.tab', function (e) {
    selectJS('.selectjs_grupocapt', `/${HOMEHOST}/data/getGrupoCapt.php`);
    $('#altaPerRelo').on('show.bs.modal', function (e) {
        selectJS('.selectjs_Relojes', `/${HOMEHOST}/data/getRelojes.php`);
    })
    tableGrupoCapt();
    tablePerRelo();
});

const tablePerInEg = () => {
    if ($.fn.DataTable.isDataTable('#Perineg')) {
        $('#Perineg').DataTable().ajax.reload();
        return;
    }
    $('#Perineg').DataTable({
        dom: "<'row '<'col-12't>>",
        "ajax": {
            url: "../../data/getPerineg.php",
            type: "GET",
            'data': {
                q2: NUMERO_LEGAJO
            },
        },
        columns: [
            /** Columna Ingreso */
            {
                className: 'align-middle',
                targets: '',
                title: 'Ingreso',
                "render": function (data, type, row, meta) {
                    return `<span class="ls1">${row.InEgFeIn}</span>`;
                },
            },
            /** Columna Egreso */
            {
                className: 'align-middle',
                targets: '',
                title: 'Egreso',
                "render": function (data, type, row, meta) {
                    return `<span class="ls1">${row.InEgFeEg}</span>`;
                },
            },
            /** Columna Diff */
            {
                className: 'align-middle',
                targets: '',
                title: '',
                "render": function (data, type, row, meta) {
                    return row.Diff;
                },
            },
            /** Columna Causa */
            {
                className: 'align-middle text-wrap w-100',
                targets: '',
                title: 'Causa',
                "render": function (data, type, row, meta) {
                    return row.InEgCaus;
                },
            },
            /** Columna Eliminar */
            {
                className: 'align-middle',
                targets: '',
                title: '',
                "render": function (data, type, row, meta) {
                    let div = `<div class="d-flex justify-content-end"><span>${row.editar}</span><span class="ml-2">${row.eliminar}</span></div>`
                    return div;
                },
            }
        ],
        paging: false,
        deferRender: true,
        scrollX: false,
        scrollCollapse: false,
        searching: false,
        info: false,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanish.json"
        },
    });
}
const tablePerPremio = () => {
    if ($.fn.DataTable.isDataTable('#Perpremio')) {
        $('#Perpremio').DataTable().ajax.reload();
        return;
    }
    selectJS('.selectjs_premios', `/${HOMEHOST}/data/getPremios.php`);
    $('#Perpremio').DataTable({
        deferRender: true,
        "ajax": {
            url: "../../data/getPerPremi.php",
            type: "GET",
            'data': {
                q2: NUMERO_LEGAJO
            },
        },
        columns: [{
            "class": "align-middle ls1 text-center",
            "data": "LPreCodi"
        }, {
            "class": "align-middle ls1",
            "data": "PreDesc"
        }, {
            "class": "align-middle text-center",
            "data": "eliminar"
        }, {
            "class": "align-middle w-100",
            "data": "null"
        }],
        paging: false,
        scrollX: false,
        scrollCollapse: false,
        searching: false,
        info: false,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanish.json"
        },
    });
}
const tableOtrosConLeg = () => {
    if ($.fn.DataTable.isDataTable('#OtrosConLeg')) {
        $('#OtrosConLeg').DataTable().ajax.reload();
        return;
    }
    selectJS('.selectjs_conceptos', `/${HOMEHOST}/data/getOtroConLeg.php`);
    $('#OtrosConLeg').DataTable({
        deferRender: true,
        "ajax": {
            url: "../../data/getConLeg.php",
            type: "GET",
            'data': {
                q2: NUMERO_LEGAJO
            },
        },
        columns: [{
            "class": "align-middle ls1 text-center",
            "data": "OTROConCodi"
        }, {
            "class": "align-middle ls1",
            "data": "OTROConDesc"
        }, {
            "class": "align-middle ls1 text-center",
            "data": "OTROConValor"
        }, {
            "class": "align-middle text-center",
            "data": "eliminar"
        }, {
            "class": "align-middle w-100",
            "data": "null"
        }],
        paging: false,
        scrollX: false,
        scrollCollapse: false,
        searching: false,
        info: false,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanish.json"
        },
    });
}
const tablePerHoAlt = () => {
    if ($.fn.DataTable.isDataTable('#PerHoAlt')) {
        $('#PerHoAlt').DataTable().ajax.reload();
        return;
    }
    selectJS('.selectjs_horarioal', `/${HOMEHOST}/data/getHorarios.php`);
    $('#PerHoAlt').DataTable({
        deferRender: true,
        "ajax": {
            url: "../../data/GetPerHoAl.php",
            type: "GET",
            'data': {
                q2: NUMERO_LEGAJO
            },
        },
        columns: [{
            "class": "align-middle ls1 text-center",
            "data": "LeHAHora"
        }, {
            "class": "align-middle",
            "data": "HorDesc"
        }, {
            "class": "align-middle text-center",
            "data": "eliminar"
        }, {
            "class": "align-middle w-100",
            "data": "null"
        }],
        paging: false,
        scrollX: false,
        scrollCollapse: false,
        searching: false,
        info: false,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanish.json"
        },
    });
}
const tableIdentifica = () => {
    if ($.fn.DataTable.isDataTable('#Identifica-table')) {
        $('#Identifica-table').DataTable().ajax.reload();
        return;
    }
    $('#Identifica-table').DataTable({
        "ajax": {
            url: "../../data/getidentifica.php",
            type: "GET",
            'data': {
                q2: NUMERO_LEGAJO
            },
        },
        columns: [{
            "class": "align-middle ls1",
            "data": "IDCodigo"
        },
        {
            "class": "align-middle text-center",
            "data": "IDFichada"
        },
        {
            "class": "align-middle ls1",
            "data": "IDTarjeta"
        },
        {
            "class": "align-middle",
            "data": "IDVence"
        },
        {
            "class": "align-middle text-center",
            "data": "IDCap04"
        },
        {
            "class": "align-middle text-center",
            "data": "IDCap05"
        },
        {
            "class": "align-middle text-center",
            "data": "IDCap06"
        },
        {
            "class": "align-middle text-center",
            "data": "IDCap01"
        },
        {
            "class": "align-middle text-center",
            "data": "IDCap03"
        },
        {
            "class": "align-middle text-center",
            "data": "eliminar"
        },
        {
            "class": "align-middle w-100",
            "data": "null"
        }
        ],
        deferRender: true,
        paging: false,
        scrollX: false,
        scrollCollapse: false,
        searching: false,
        info: false,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanish.json"
        },
    });
}
const tableGrupoCapt = () => {

    const LegGrHa = $('#LegGrHa').val();

    if ($.fn.DataTable.isDataTable('#GrupoCapt')) {
        $('#GrupoCapt').DataTable().ajax.reload();
        return;
    }

    $('#GrupoCapt').DataTable({
        "ajax": {
            url: "../../data/GetReloHabi.php",
            type: "GET",
            'data': {
                q2: LegGrHa,
            },
        },
        columns: [{
            "class": "align-middle ls1",
            "data": "Serie"
        }, {
            "class": "align-middle",
            "data": "Descrip"
        }, {
            "class": "align-middle",
            "data": "Marca"
        }, {
            "class": "align-middle w-100",
            "data": "null"
        }],
        paging: false,
        scrollX: false,
        scrollCollapse: false,
        searching: false,
        info: false,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanish.json"
        },
    });
}
const tablePerRelo = () => {
    if ($.fn.DataTable.isDataTable('#TablePerRelo')) {
        $('#TablePerRelo').DataTable().ajax.reload();
        return;
    }
    $('#TablePerRelo').DataTable({
        "ajax": {
            url: "../../data/GetPerRelo.php",
            type: "GET",
            'data': {
                q2: NUMERO_LEGAJO,
            },
        },
        columns: [{
            "class": "align-middle ls1",
            "data": "Serie"
        }, {
            "class": "align-middle ls1",
            "data": "Descrip"
        }, {
            "class": "align-middle",
            "data": "Marca"
        }, {
            "class": "align-middle ls1",
            "data": "Desde"
        }, {
            "class": "align-middle ls1 fw4",
            "data": "Vence"
        }, {
            "class": "align-middle text-center",
            "data": "eliminar"
        }, {
            "class": "align-middle w-100",
            "data": "null"
        }],
        paging: false,
        scrollX: false,
        scrollCollapse: false,
        searching: false,
        info: false,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanish.json"
        },
    });
}
const selectJS = (selector, urlData) => {
    // si select2 esta inicializado, return
    if ($(selector).hasClass('select2-hidden-accessible')) {
        return;
    }

    $(selector).select2({
        multiple: false,
        language: "es",
        placeholder: "Seleccionar",
        minimumInputLength: 0,
        minimumResultsForSearch: 10,
        maximumInputLength: 10,
        selectOnClose: 0,
        language: {
            noResults: function () {
                return 'No hay resultados..'
            },
            inputTooLong: function (args) {
                var message = 'Máximo 10 caracteres. Elimine ' + overChars + ' caracter';
                if (overChars != 1) {
                    message += 'es'
                }
                return message
            },
            searching: function () {
                return 'Buscando..'
            },
            errorLoading: function () {
                return 'Sin datos..'
            },
            inputTooShort: function () {
                return 'Ingresar 0 o mas caracteres'
            },
            maximumSelected: function () {
                return 'Puede seleccionar solo una opción'
            }
        },
        ajax: {
            url: urlData,
            dataType: "json",
            type: "GET",
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    _c: $("#_c").val(),
                    _r: $("#_r").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    })
}