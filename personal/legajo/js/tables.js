// selectJS('selectjs_naciones', `/${HOMEHOST}/data/getNaciones.php`);
const FLAG = Date.now();

const langTable = {
    "sProcessing": "Actualizando . . .",
    "sLengthMenu": "_MENU_",
    "sZeroRecords": "No se encontraron resultados",
    "sEmptyTable": "No se encontraron resultados",
    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
    "sInfoPostFix": "",
    "sSearch": "<span class='LabelSearchDT'>Buscar:</span>",
    "sUrl": "",
    "sInfoThousands": ",",
    "sLoadingRecords": "<div class='spinner-border text-light'></div>",
    "oPaginate": {
        "sFirst": "<<",
        "sLast": ">>",
        "sNext": "»",
        "sPrevious": "«"
    },
    "oAria": {
        "sSortAscending": ":Activar para ordenar la columna de manera ascendente",
        "sSortDescending": ":Activar para ordenar la columna de manera descendente"
    }
};

// clean ls tableGrupoCapt 
ls.remove('#GrupoCapt');
ls.remove('#TablePerRelo');
ls.remove('#Identifica-table');

$('#liquid-tab').on('show.bs.tab', function (e) {
    tablePerInEg();
    tablePerPremio();
    tableOtrosConLeg();
});
$('#horarios-tab').on('show.bs.tab', function (e) {
    tablePerHoAlt();
});

$('#identifica-tab').on('show.bs.tab', function (e) {
    tableIdentifica('#Identifica-table');
});

$('#control-tab').on('show.bs.tab', function (e) {
    selectJS('.selectjs_regla', `/${HOMEHOST}/data/getReglaCo.php`);
});

$('#dispositivo-tab').on('show.bs.tab', function (e) {
    selectJS('.selectjs_grupocapt', `/${HOMEHOST}/data/getGrupoCapt.php`);
    $('#altaPerRelo').on('show.bs.modal', function (e) {
        selectJS('.selectjs_Relojes', `/${HOMEHOST}/data/getRelojes.php`);
    })
    tableGrupoCapt('#GrupoCapt');
    tablePerRelo('#TablePerRelo');
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
            url: "../../data/GetConLeg.php",
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
const tableIdentifica = async (selectorTable) => {

    $(`${selectorTable}`).addClass('loader-in');

    let data = [];

    const iconCheck = (value) => {
        if (value === '1') {
            return '<i class="bi bi-check text-success font1"></i>';
        }
        return '<i class="bi bi-dash text-danger font1"></i>';
    }

    try {
        const cacheData = ls.get(selectorTable);
        if (cacheData && cacheData.timestamp === FLAG) {
            data = cacheData.data;
        } else {
            const res = await axios.get('../../app-data/identifica', {
                params: {
                    legajo: [NUMERO_LEGAJO],
                },
            });
            // console.log('fetching new data');
            data = res.data;
            ls.set(selectorTable, { timestamp: FLAG, data: data });
        }

        if (!data) return;

        if ($.fn.DataTable.isDataTable(selectorTable)) {
            $(selectorTable).DataTable().clear().destroy();
        }

        $(selectorTable).DataTable({
            dom: `
                <'row' <'col-12'<'table-responsive't>>>
                `,
            data: data,
            columns: [
                {
                    data: 'IDCodigo', className: '', targets: '', title: 'ID',
                    "render": function (data, type, row, meta) {
                        return data
                    }, visible: true
                },
                {
                    data: 'IDFichada', className: 'text-center', targets: '', title: 'Fichada',
                    "render": function (data, type, row, meta) {
                        return iconCheck(data);
                    }, visible: true
                },
                {
                    data: 'IDTarjeta', className: 'text-center', targets: '', title: 'Tarjeta',
                    "render": function (data, type, row, meta) {
                        return iconCheck(data);
                    }, visible: true
                },
                {
                    data: 'IDVenceStr', className: 'text-center', targets: '', title: 'Vencimiento',
                    "render": function (data, type, row, meta) {
                        return data === '01-01-1753' ? '<i class="bi bi-dash text-danger font1"></i>' : data;
                    }, visible: true
                },
                {
                    data: 'IDCap04', className: 'text-center', targets: '', title: 'ZKTeco',
                    "render": function (data, type, row, meta) {
                        return iconCheck(data);
                    }, visible: true
                },
                {
                    data: 'IDCap05', className: 'text-center', targets: '', title: 'Suprema',
                    "render": function (data, type, row, meta) {
                        return iconCheck(data);
                    }, visible: true
                },
                {
                    data: 'IDCap06', className: 'text-center', targets: '', title: 'HikVision',
                    "render": function (data, type, row, meta) {
                        return iconCheck(data);
                    }, visible: true
                },
                {
                    data: 'IDCap01', className: 'text-center', targets: '', title: 'Macronet',
                    "render": function (data, type, row, meta) {
                        return iconCheck(data);
                    }, visible: true
                },
                {
                    data: 'IDCap03', className: 'text-center', targets: '', title: 'S. Bayres',
                    "render": function (data, type, row, meta) {
                        return iconCheck(data);
                    }, visible: true
                },
                {
                    data: '', className: 'w-100 text-right', targets: '', title: '',
                    "render": function (data, type, row, meta) {
                        return `<div class="item">
                        <a class="btn btn-light btn-sm delete_identifica" data="${row.IDCodigo}" data2="${row.IDLegajo}" data3="true">
                            <i class="bi bi-trash"></i>
                        </a>
                        </div>`
                    }, visible: true
                }
            ],
            deferRender: true,
            paging: false,
            searching: false,
            info: false,
            ordering: false,
            language: langTable,
        });

        $(`${selectorTable}`).removeClass('loader-in');

    } catch (error) {
        const msg = error.response?.data?.message || 'Error al cargar los datos';        
        notify(msg, 'danger', 2000, 'right');
    }
}
const tableGrupoCapt = async (selectorTable) => {

    $(`${selectorTable}`).addClass('loader-in');

    let data = [];

    try {
        const cacheData = ls.get(selectorTable);
        if (cacheData && cacheData.timestamp === FLAG) {
            data = cacheData.data ?? [];
        } else {
            const LegGrHa = $('#LegGrHa').val();            
            const res = await axios.get('../../app-data/relohabi', {
                params: {
                    relgrup: [LegGrHa],
                },
            });
            // console.log('fetching new data');
            data = res.data;
            ls.set(selectorTable, { timestamp: FLAG, data: data });
        }

        if (!data) return;

        if ($.fn.DataTable.isDataTable(selectorTable)) {
            $(selectorTable).DataTable().clear().destroy();
        }

        $(selectorTable).DataTable({
            dom: `
                <'row' <'col-12'<'table-responsive't>>>
                `,
            data: data,
            columns: [
                {
                    data: 'RelSeri', className: '', targets: '', title: 'Serie',
                    "render": function (data, type, row, meta) {
                        return data
                    }, visible: true
                },
                {
                    data: 'RelDeRe', className: '', targets: '', title: 'Dispositivo',
                    "render": function (data, type, row, meta) {
                        return data
                    }, visible: true
                },
                {
                    data: 'RelReMaStr', className: 'w-100', targets: '', title: 'Marca',
                    "render": function (data, type, row, meta) {
                        return data
                    }, visible: true
                }
            ],
            deferRender: true,
            paging: false,
            searching: false,
            info: false,
            ordering: false,
            language: langTable,
        });

        $(`${selectorTable}`).removeClass('loader-in');

    } catch (error) {
        const msg = error.response?.data?.message || 'Error al cargar los datos';
        notify(msg, 'danger', 2000, 'right');
    }
}
const tablePerRelo = async (selectorTable) => {

    $(`${selectorTable}`).addClass('loader-in');

    let data = [];

    try {
        const cacheData = ls.get(selectorTable);
        if (cacheData && cacheData.timestamp === FLAG) {
            data = cacheData.data;
        } else {
            const res = await axios.get('../../app-data/perrelo', {
                params: {
                    legajo: [NUMERO_LEGAJO],
                },
            });
            // console.log('fetching new data');
            data = res.data;
            ls.set(selectorTable, { timestamp: FLAG, data: data });
        }

        if (!data) return;

        if ($.fn.DataTable.isDataTable(selectorTable)) {
            $(selectorTable).DataTable().clear().destroy();
        }

        $(selectorTable).DataTable({
            dom: `
                <'row' <'col-12'<'table-responsive't>>>
                `,
            data: data,
            columns: [
                {
                    data: 'RelSeri', className: '', targets: '', title: 'Serie',
                    "render": function (data, type, row, meta) {
                        return data
                    }, visible: true
                },
                {
                    data: 'RelDeRe', className: '', targets: '', title: 'Dispositivo',
                    "render": function (data, type, row, meta) {
                        return data
                    }, visible: true
                },
                {
                    data: 'RelReMaStr', className: '', targets: '', title: 'Marca',
                    "render": function (data, type, row, meta) {
                        return data
                    }, visible: true
                },
                {
                    data: 'RelFechStr', className: '', targets: '', title: 'Desde',
                    "render": function (data, type, row, meta) {
                        return data
                    }, visible: true
                },
                {
                    data: 'RelFech2Str', className: 'w-100', targets: '', title: 'Vence',
                    "render": function (data, type, row, meta) {
                        return data
                    }, visible: true
                },
                {
                    data: '', className: '', targets: '', title: '',
                    "render": function (data, type, row, meta) {
                        return `<div class="item">
                        <a class="btn btn-light btn-sm delete_perrelo" data="${row.RelRelo}" data2="${row.RelReMa}" data3="${row.RelLega}" data4="true">
                            <i class="bi bi-trash"></i>
                        </a>
                        </div>`
                    }, visible: true
                }
            ],
            deferRender: true,
            paging: false,
            searching: false,
            info: false,
            ordering: false,
            language: langTable,
        });

        $(`${selectorTable}`).removeClass('loader-in');

    } catch (error) {
        const msg = error.response?.data?.message || 'Error al cargar los datos';        
        notify(msg, 'danger', 2000, 'right');
    }
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