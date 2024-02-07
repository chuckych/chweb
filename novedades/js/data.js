const homehost = $("#_homehost").val();
const LS_FICHA_FORM = homehost + '_ficha_form';
const LS_NOVEDADES = homehost + '_novedades';
ls.remove(LS_FICHA_FORM);

const loading = () => {
    $.notifyClose();
    let spinner = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`;
    notify('Aguarde <span class = "dotting mr-1"> </span> ' + spinner, 'dark', 60000, 'right')
}
function ActualizaTablas() {
    if ($("#Visualizar").is(":checked")) {
        CheckSesion()
        $('#GetFechas').DataTable().ajax.reload(null, false);
    } else {
        CheckSesion()
        $('#GetPersonal').DataTable().ajax.reload(null, false);
        $('#Per2').addClass('d-none')
        $('.pers_legajo').removeClass('d-none')
    };
};
onOpenSelect2()
var map = { 17: false, 18: false, 32: false, 16: false, 39: false, 37: false, 13: false, 27: false };
$(document).keydown(function (e) {
    // if (e.keyCode in map) {
    //     map[e.keyCode] = true;
    //     if (map[32]) { /** Barra espaciadora */
    //         $('#Filtros').modal('show');
    //     }
    // }
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

$("#pagFech").addClass('d-none');
$("#GetNovedadesFechaTable").addClass('d-none');
$('#Visualizar').prop('disabled', true)

let GetPersonal = $('#GetPersonal').DataTable({
    "initComplete": function (settings, json) {
    },
    "drawCallback": function (settings) {
        $("#GetPersonal thead").remove();
        $(".page-link").addClass('border border-0');
        $(".dataTables_info").addClass('text-secondary');
        $('#GetNovedades').DataTable().ajax.reload();
    },
    pagingType: "full",
    lengthMenu: [[1], [1]],
    bProcessing: false,
    serverSide: true,
    deferRender: true,
    searchDelay: 1500,
    // dom: `<'mt-3't><"mt-n2"p><"mt-n2 pb-2 d-flex justify-content-end"i>`,
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
        url: "/" + $("#_homehost").val() + "/novedades/GetPersonal.php",
        type: "POST",
        "data": function (data) {
            data._l = $("#_l").val();
            data.Per = $("#Per").val();
            data.Per2 = $("#Per2").val();
            data.Tipo = $("#Tipo").val();
            data.Emp = $("#Emp").val();
            data.Plan = $("#Plan").val();
            data.Sect = $("#Sect").val();
            data.Sec2 = $("#Sec2").val();
            data.Grup = $("#Grup").val();
            data.Sucur = $("#Sucur").val();
            data._dr = $("#_dr").val();
            data.FicNoTi = $("#FicNoTi").val();
            data.FicNove = $("#FicNove").val();
            data.FicNovA = $('#FicNovA').val();
            data.FicCausa = $('#FicCausa').val();
        },

        error: function () {
            $("#GetPersonal_processing").css("display", "none");
        },
    },
    columns: [
        {
            data: 'pers_legajo', className: 'w80 px-3 border fw4 bg-light radius pers_legajo', targets: '', title: '',
            "render": function (data, type, row, meta) {
                return `<div class="text-truncate" style="max-width:80px">${data}</div>`;
            },
        },
        {
            data: 'pers_nombre', className: 'px-3 border fw4 bg-light radius pers_legajo', targets: '', title: '',
            "render": function (data, type, row, meta) {
                return `<div class="text-truncate" style="min-width:190px;max-width:250px" title="${row.ApNo}">${data}</div>`;
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

let GetNovedades = $('#GetNovedades').DataTable({
    "drawCallback": function (settings) {
        $(".page-link").addClass('border border-0');
        $(".dataTables_info").addClass('text-secondary');
        $(".custom-select").addClass('text-secondary bg-light');
        $('#pagLega').removeClass('invisible');
        $('#GetNovedadesTable').removeClass('invisible');
        setTimeout(function () {
            $(".Filtros").prop('disabled', false);
        }, 1000);
    },
    lengthMenu: [[30, 60, 90, 120], [30, 60, 90, 120]],
    bProcessing: true,
    serverSide: true,
    deferRender: true,
    searchDelay: 1500,
    dom: `<'mt-3'<'d-none d-sm-block'l><t>><"mt-n2"p><"mt-n2 pb-2 d-flex justify-content-end"i>`,
    ajax: {
        url: "/" + $("#_homehost").val() + "/novedades/GetNovedades.php",
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
            data.FicNoTi = $("#FicNoTi").val();
            data.FicNove = $("#FicNove").val();
            data.FicNovA = $('#FicNovA').val();
            data.FicCausa = $('#FicCausa').val();
        },
        error: function () {
            $("#GetNovedades").css("display", "none");
        },
    },
    columns: [
        {
            data: 'NoveEdit', className: 'align-middle px-0', targets: '', title: '',
            "render": function (data, type, row, meta) {
                if (data == '1') {
                    return `<edita class="btn mx-2 btn-sm btn-outline-custom border-0 bi bi-pen editaNov"></edita>`;
                }
                return ``;
            },
        },
        {
            data: 'Fecha', className: 'align-middle', targets: '', title: 'Fecha',
            "render": function (data, type, row, meta) {
                return `${data}`;
            },
        },
        {
            data: 'nov_nom_dia', className: 'align-middle', targets: '', title: 'Día',
            "render": function (data, type, row, meta) {
                return `${data}`;
            },
        },
        {
            data: 'nov_horario', className: 'align-middle', targets: '', title: 'Horario',
            "render": function (data, type, row, meta) {
                return `${data}`;
            },
        },
        {
            data: 'NoveCod', className: 'align-middle text-center', targets: '', title: 'Cod.',
            "render": function (data, type, row, meta) {
                return `${data}`;
            },
        },
        {
            data: 'Novedades', className: 'align-middle', targets: '', title: 'Novedades',
            "render": function (data, type, row, meta) {
                return `${data}`;
            },
        },
        {
            data: 'NovHor', className: 'align-middle', targets: '', title: '',
            "render": function (data, type, row, meta) {
                return `${data}`;
            },
        },
        {
            data: 'NoveTipo', className: 'align-middle', targets: '', title: 'Tipo',
            "render": function (data, type, row, meta) {
                return `${data}`;
            },
        },
        {
            data: 'NoveCausa', className: 'align-middle', targets: '', title: 'Causa',
            "render": function (data, type, row, meta) {
                return `${data}`;
            },
        },
        {
            data: 'NoveObserv', className: 'align-middle', targets: '', title: 'Observación',
            "render": function (data, type, row, meta) {
                return `${data}`;
            },
        }
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
$("#GetNovedades").on('init.dt', function () {

    $("#Refresh").prop('disabled', false);
    $('#trash_all').removeClass('invisible');
    fadeInOnly('#pagLega');
    fadeInOnly('#GetNovedadesTable');

    let idTableBody = '#GetNovedades tbody';

    $(idTableBody).on('click', (e) => {
        if (e.target.closest('tr').tagName) {
            if (e.target.tagName == 'EDITA') {
                if (e.target.classList.contains('disabled')) {
                    return false;
                }
                e.target.classList.add('disabled');
                let data = GetNovedades.row(e.target.closest('tr')).data();
                if (data.arrayNove) {
                    modalEditNove(data).then(() => {
                        e.target.classList.remove('disabled');
                    });
                }
            }
        }
    })

});

setTimeout(function () {
    $('#GetFechas').DataTable({
        "initComplete": function (settings, json) {
            $("#GetFechas thead").remove();
        },
        "drawCallback": function (settings) {
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $('#GetNovedadesFecha').DataTable().ajax.reload();
            // $(".loader2").fadeOut("slow");
        },
        pagingType: "full",
        lengthMenu: [[1], [1]],
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        // dom: `<'mt-3't><"mt-n2"p><"mt-n2 pb-2 d-flex justify-content-end"i>`,
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
            url: "/" + $("#_homehost").val() + "/novedades/GetFechas.php",
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
                data.FicNoTi = $("#FicNoTi").val();
                data.FicNove = $("#FicNove").val();
                data.FicNovA = $('#FicNovA').val();
                data.FicCausa = $('#FicCausa').val();
            },
            error: function () {
                $("#GetFecha_processing").css("display", "none");
            },
        },
        columns: [
            {
                "class": "w80 px-3 border fw4 bg-light radius",
                "data": 'FicFech'
            },
            {
                data: 'Dia', className: 'text-center px-3 border fw4 bg-light radius', targets: '', title: '',
                "render": function (data, type, row, meta) {
                    return `<div class="text-truncate" style="min-width:190px;max-width:250px" title="${row.ApNo}">${data}</div>`;
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
    let GetNovedadesFecha = $('#GetNovedadesFecha').DataTable({
        "drawCallback": function (settings) {
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $(".custom-select").addClass('text-secondary bg-light');
            $('#Visualizar').prop('disabled', false)
            setTimeout(function () {
                $(".Filtros").prop('disabled', false);
            }, 1000);
        },
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        dom: `<'mt-3'<'d-none d-sm-block'l><t>><"mt-n2"p><"mt-n2 pb-2 d-flex justify-content-end"i>`,
        ajax: {
            url: "/" + $("#_homehost").val() + "/novedades/GetNovedadesFecha.php",
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
                data.FicNoTi = $("#FicNoTi").val();
                data.FicNove = $("#FicNove").val();
                data.FicNovA = $('#FicNovA').val();
                data.FicCausa = $('#FicCausa').val();
            },
            error: function () {
                $("#GetNovedadesFecha_processing").css("display", "none");
            },
        },
        columns: [
            {
                data: 'NoveEdit', className: 'align-middle px-0', targets: '', title: '',
                "render": function (data, type, row, meta) {
                    if (data == '1') {
                        return `<edita class="btn btn-sm mx-2 btn-outline-custom border-0 bi bi-pen editaNov"></edita>`;
                    }
                    return ``;
                },
            },
            {
                data: 'nov_LegNume', className: 'align-middle', targets: '', title: 'Legajo',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'nov_leg_nombre', className: 'align-middle', targets: '', title: 'Nombre',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'nov_horario', className: 'align-middle', targets: '', title: 'Horario',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'NoveCod', className: 'align-middle text-center', targets: '', title: 'Cod',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'Novedades', className: 'align-middle', targets: '', title: 'Novedad',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'NovHor', className: 'align-middle', targets: '', title: 'Hs',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'NoveTipo', className: 'align-middle', targets: '', title: 'Tipo',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'NoveCausa', className: 'align-middle', targets: '', title: 'Causa',
                "render": function (data, type, row, meta) {
                    return data;
                },
            },
            {
                data: 'NoveObserv', className: 'align-middle', targets: '', title: 'Observaciones',
                "render": function (data, type, row, meta) {
                    return data;
                },
            }
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
    $("#GetNovedadesFecha").on('init.dt', function () {

        fadeInOnly('#GetNovedadesTable');

        let idTableBody = '#GetNovedadesFecha tbody';

        $(idTableBody).on('click', (e) => {
            // console.log(e.target.tagName);
            if (e.target.closest('tr').tagName) {
                if (e.target.tagName == 'EDITA') {
                    if (e.target.classList.contains('disabled')) {
                        return false;
                    }
                    e.target.classList.add('disabled');

                    let data = GetNovedadesFecha.row(e.target.closest('tr')).data();
                    if (data.arrayNove) {
                        modalEditNove(data).then(() => {
                            e.target.classList.remove('disabled');
                        });
                    }
                }
            }
        })

    });
}, 1000);

$('#GetPersonal').on('page.dt', function () {
    CheckSesion()
});
$('#GetNovedades').on('page.dt', function () {
    CheckSesion()
});
$('#GetFechas').on('page.dt', function () {
    CheckSesion()
});
$('#GetNovedadesFecha').on('page.dt', function () {
    CheckSesion()
});

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
    // $("#loader").addClass('loader');
    if ($("#Visualizar").is(":checked")) {
        $('#GetFechas').DataTable().ajax.reload();
        $("#GetNovedadesTable").addClass('d-none');
        $("#GetNovedadesFechaTable").removeClass('d-none');
        $("#pagLega").addClass('d-none');
        $("#pagFech").removeClass('d-none')
        // $('#VerPor').html('Visualizar por Legajo')
    } else {
        $('#GetPersonal').DataTable().ajax.reload();
        $("#GetNovedadesTable").removeClass('d-none');
        $("#GetNovedadesFechaTable").addClass('d-none')
        $("#pagLega").removeClass('d-none')
        $("#pagFech").addClass('d-none')
        // $('#VerPor').html('Visualizar por Fecha')
    }
});

axios('../app-data/novedades-all').then((rs) => {
    ls.set(LS_NOVEDADES, rs.data);
});

const modalEditNove = async (data) => {
    loading();
    try {

        if (!data) { // Si no hay datos
            throw new Error('No se encontraron datos para editar');
        }
        let response = await axios('modal_editar.html' + "?" + Date.now()); // Busca el modal
        let html = response.data; // Obtiene el modal

        $('#modales').html(html); // Agrega el modal al DOM

        $('#modal #rowForm').hide(); // Oculta el formulario
        $('#Nove').select2({ // Inicializa el select2 de novedades
            placeholder: 'Seleccionar Novedad',
            width: "100%",
            language: "es",
        });
        $('#Causa').select2({   // Inicializa el select2 de causas
            placeholder: 'Seleccionar causa',
            width: "100%",
            language: "es",
            allowClear: true,
        });
        HoraMask('#Horas')

        disabledForm(true);

        getFicha(data.nov_LegNume, data.FechaStr).then((rs) => {
            let ficha = rs;
            if (ficha.length === 0) {
                $('#modal').modal('hide');
                $.notifyClose();
                notify('No se encontró la ficha', 'danger', 2000, 'right');
                ls.remove(LS_FICHA_FORM);
                return;
            }
            ls.set(LS_FICHA_FORM, ficha[0]);
            tableNoveEdit(ficha[0]).then(() => { // Carga la tabla de novedades
                $('#modal').modal('show')// Muestra el modal
                $.notifyClose();
            });
        });

        $('#modal').on('hidden.bs.modal', function (e) {
            tipo_cod = null;
            $('#modales').html('');
            $.notifyClose();
        });
        return true;
    } catch (error) {
        alert(error.message);
        return false;
    }
}
const tableNoveEdit = async (data) => {
    return new Promise((resolve, reject) => {
        let Ficha = data;

        if (Ficha.length === 0) {
            $.notifyClose();
            notify('No se encontró la ficha', 'danger', 2000, 'right');
            return;
        }

        let Novedades = Ficha.Nove ?? [];

        if (Novedades.length === 0) {
            $.notifyClose();
            notify('La novedad no tiene ficha asociada', 'danger', 2000, 'right');
            return;
        }

        Novedades.forEach((nove) => {
            nove.Fech = Ficha.Fech;
            nove.Lega = Ficha.Lega;
            nove.Cierre = Ficha.Cierre;
            nove.NoveDelete = Ficha.NoveDelete;
        });

        let siFichaCerrada = (Ficha.Cierre.Estado != 'abierto') ? '(Periodo ' + Ficha.Cierre.Estado + ')' : '';
        $('.modal-title').html('Editar Novedades ' + siFichaCerrada ?? '') // Cambia el título del modal

        if ($.fn.DataTable.isDataTable('#tableNovEdit')) {
            $('#tableNovEdit').DataTable().destroy();
        }

        let dt = $('#tableNovEdit').DataTable({
            dom: `
                <'row '
                    <'col-12 divFichadas'>
                >
                <'row '
                    <'col-12 mt-0't>
                >`,
            bProcessing: true,
            loadingRecords: true,
            paging: false,
            searching: false,
            info: false,
            serverSide: false,
            searchDelay: 1500,
            data: (Novedades),
            createdRow: function (row, data, dataIndex) {
                row.classList.add('pointer');
            },
            columns: [
                {
                    data: 'NoveDelete', className: 'align-middle text-center pl-0', targets: '', title: '<span id="addNovedad" data-titler="Nueva Novedad" class="btn btn-sm btn-success bi bi-plus-lg"></span>',
                    "render": function (data, type, row, meta) {
                        if (data == '1') {
                            return `<delete data-titler="Eliminar Novedad" class="btn btn-sm btn-outline-danger border-0 mx-2 bi bi-trash"></delete>`;
                        }
                        return ``;
                    },
                },
                {
                    data: '', className: 'd-none align-middle text-center pl-0', targets: '', title: '<span id="addNovedad" data-titler="Nueva Novedad" class="btn btn-sm btn-success bi bi-plus-lg"></span>',
                    "render": function (data, type, row, meta) {
                        return `<div class="custom-control custom-checkbox pl-4 ml-3">
                    <input type="checkbox" class="custom-control-input">
                    <label class="custom-control-label"></label>
                  </div>`;
                    },
                },
                {
                    data: 'Codi', className: 'align-middle text-center', targets: '', title: 'Cod.',
                    "render": function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: 'Desc', className: 'align-middle', targets: '', title: 'Novedad',
                    "render": function (data, type, row, meta) {
                        return `<div class="text-truncate" style="max-width:150px">${data}</div>`;
                    },
                },
                {
                    data: 'Horas', className: 'align-middle', targets: '', title: 'Hs',
                    "render": function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: 'CDesc', className: 'align-middle', targets: '', title: 'Causa',
                    "render": function (data, type, row, meta) {
                        return `<div class="text-truncate" style="max-width:150px">${data}</div>`;
                    },
                },
                {
                    data: 'Esta', className: 'align-middle', targets: '', title: 'Tipo',
                    "render": function (data, type, row, meta) {
                        switch (data) {
                            case '0': return 'Defecto';
                            case '1': return 'Modificada';
                            case '2': return 'Creada';
                            default: return '';
                        }
                    },
                },
                {
                    data: 'Cate', className: 'align-middle', targets: '', title: 'Categoría',
                    "render": function (data, type, row, meta) {
                        return CateNov(data);
                    },
                },
                {
                    data: 'Obse', className: 'align-middle', targets: '', title: 'Observaciones',
                    "render": function (data, type, row, meta) {
                        return `<div class="text-truncate" style="max-width:130px">${data}</div>`;
                    },
                },
                {
                    data: '', className: 'align-middle w-100', targets: '', title: '',
                    "render": function (data, type, row, meta) {
                        return ``;
                    },
                }
            ],
            paging: false,
            responsive: false,
            info: true,
            ordering: false,
            language: {
                "url": "../js/DataTableSpanishShort2.json" + "?" + vjs(),
            },
        });
        dt.on('init.dt', function (e, settings) {

            e.preventDefault();
            e.stopPropagation();

            fadeInOnly('#tableNovEdit');

            let tableInfo = tableInfoFicha(); // Crea la tabla de información de la ficha

            $("#modal .divFichadas").html('');
            $("#modal .divFichadas").html(tableInfo);

            let idTableBody = '#tableNovEdit tbody tr';

            setTimeout(() => {
                let tr = document.querySelector(idTableBody); // Obtiene el primer tr
                if (!tr.classList.contains('selected')) {
                    $(tr).trigger('click'); // Dispara el evento click del primer tr
                }
            }, 0);
            $(idTableBody).on('click', (e) => {

                e.preventDefault();
                e.stopPropagation();

                $('#modal  #rowForm').addClass('loader-in');

                if (e.target.closest('tr').tagName) {

                    let data = dt.row(e.target.closest('tr')).data();
                    if (!data) return;

                    if (e.target.tagName == 'DELETE') {
                        deleteNovedad(data);
                        return;
                    }

                    let checkbox = e.currentTarget.querySelector('input[type="checkbox"]');
                    let classList = e.currentTarget.classList; // Obtiene la lista de clases del tr

                    if (classList.contains('selected')) { // Si el tr está seleccionado
                        classList.remove('selected'); // Quita la clase selected
                        // $('#modal #rowForm').hide(); // Oculta el formulario
                        disabledForm(true);
                        $('#modal  #rowForm').addClass('loader-in');
                        $('#modal #btnGuardar ').off('click')
                        checkbox.checked = false; // Desmarca el checkbox
                    }
                    else { // Si el tr no está seleccionado
                        dt.rows('.selected').nodes().each((row) => row.classList.remove('selected')); // Quita la clase selected de todos los tr
                        // desmarcar todos los checkbox
                        dt.rows().nodes().each((row) => row.querySelector('input[type="checkbox"]').checked = false);
                        classList.add('selected'); // Agrega la clase selected al tr
                        $('#modal #rowForm').fadeIn('slow'); // Muestra el formulario
                        checkbox.checked = true; // Marca el checkbox
                        formNovedad(data).then((rs) => {
                            if (rs) {

                                $('#modal  #rowForm').removeClass('loader-in');
                                $('#modal #rowForm').fadeIn('slow'); // Muestra el formulario

                                $('#modal #btnGuardar').prop('disabled', false);
                                $('#modal #btnGuardar').removeClass('btnAgregar')
                                $('#modal #btnGuardar').addClass('btnGuardar')

                                $('#modal #btnGuardar').removeClass('btn-success')
                                $('#modal #btnGuardar').addClass('btn-custom')
                                $('#modal #btnGuardar').html('Aplicar')

                                if (Ficha.Cierre.Estado != 'abierto') { // Si la ficha está cerrada
                                    disabledForm(true); // inhabilita el formulario
                                    $('#modal #btnGuardar ').off('click') // Quita el evento click del botón guardar
                                }
                            }
                            $("#modal .modal-body").removeClass('loader-in');
                        });
                    }
                }
            })
        });
        resolve();
    });
}
const formNovedad = async (data) => {

    try {

        if (!data.Codi) { // Si no hay novedad
            throw new Error('No se encontraron datos para editar');
        }

        let rsData = ls.get(LS_NOVEDADES);
        if (!rsData) {
            throw new Error('No se encontraron datos para editar');
        }
        // filtrar las novedades por el tipo de novedad
        let novedades = '';
        if (data.NoTi > 2) {
            novedades = rsData.novedades.filter((nove) => nove.Tipo > 2);
        } else {
            novedades = rsData.novedades.filter((nove) => nove.Tipo == data.NoTi);
        }
        // filtrar las causas por el código de la novedad
        let causas = rsData.causas.filter((causa) => causa.CodiNov == data.Codi);

        addSelectOptions(novedades, '#Nove') // Agrega las novedades al select
        addSelectOptions(causas, '#Causa') // Agrega las causas al select

        $('#Nove').val(data.Codi).trigger('change'); // Selecciona la novedad
        $('#NoveOriginal').val(data.Codi).trigger('change'); // Guarda la novedad original
        $('#NoveSec').prop('checked', false).prop('disabled', false);
        $("#Horas").val(data.Horas).prop('disabled', false).trigger('change'); // Selecciona las horas
        $("#Obs").val(data.Obse).prop('disabled', false).trigger('change'); // Selecciona la observación
        $("#Obs").attr('autocomplete', 'off');
        $("#Horas").attr('autocomplete', 'off');
        $("#Causa").val(data.CCodi).trigger('change'); // Selecciona la causa

        if (data.Cate == '2') {
            $('#NoveSec').prop('checked', true).trigger('change');
        }
        return true; // Retorna true si todo salió bien
    } catch (error) {
        alert(error.message);
        return false;
    }

}
const addSelectOptions = (objeto, selector) => {
    if (!objeto) return false;
    $(selector).prop('disabled', true).empty().trigger('change');
    if (!objeto) return false;
    if (objeto.length > 0) {
        $(selector).append(`<option value=""></option>`);
        Object.keys(objeto).forEach(element => {
            let opt = objeto[element];
            $(selector).append(`<option value="${opt.Codi}">${opt.Desc}</option>`);
        });
        $(selector).prop('disabled', false);
    }
}

function generarOptgroupYOptions(objeto, selector) {
    if (!objeto) return false;
    let select = document.querySelector(selector);
    $(selector).append(`<option value=""></option>`);
    Object.keys(objeto).forEach(function (clave) {
        var optgroup = document.createElement('optgroup');
        optgroup.label = clave;

        objeto[clave].forEach(function (item) {
            var option = document.createElement('option');
            option.value = item.Codi;
            option.text = item.Desc;
            optgroup.appendChild(option);
        });

        select.appendChild(optgroup);
    });
}

const formGuardar = async () => {
    $(document).on('click', '#modal .btnGuardar', async function (e) {
        e.preventDefault();
        e.stopPropagation();

        let data = ls.get(LS_FICHA_FORM);
        if (!data) return;

        if (!data) {
            $.notifyClose();
            notify('No se encontraron datos para editar', 'danger', 2000, 'right');
            return;
        }

        loading();
        $('#modal .modal-body').addClass('loader-in');

        if (data.Cierre.Estado != 'abierto') {
            $.notifyClose();
            notify('No se puede editar una ficha cerrada', 'warning', 2000, 'right');
            return;
        }

        let formData = {
            Lega: data.Lega,
            Fecha: data.Fech,
            Cate: $('#NoveSec').is(':checked') ? '2' : '0',
            Nove: $('#NoveOriginal').val(),
            NoveM: $('#Nove').val(),
            Horas: $('#Horas').val(),
            Obse: $('#Obs').val().substring(0, 40).trim(),
            Causa: $('#Causa').val(),
            Esta: "1"
        }
        let rs = await axios.put('../app-data/novedad', formData);
        // console.log(rs.data);
        if (rs.data.error) {
            $.notifyClose();
            notify(rs.data.error, 'danger', 2000, 'right');
            return;
        }
        if (rs.data.MESSAGE == "OK") {
            $.notifyClose();
            $('#modal #btnGuardar').off('click')
            ActualizaTablas();
            notify('Novedad editada correctamente', 'success', 2000, 'right');
            getFicha(data.Lega, data.Fech).then((rs) => {
                if (!rs) {
                    $('#modal').modal('hide');
                    return;
                }
                tableNoveEdit(rs[0]);
            });
            return;
        }
    });
}
formGuardar();

const addNovedad = () => {
    $(document).on('click', '#modal #addNovedad', async function (e) {

        e.preventDefault();
        e.stopPropagation();

        $('#modal #btnGuardar').addClass('btnAgregar')
        $('#modal #btnGuardar').addClass('btn-success')
        $('#modal #btnGuardar').removeClass('btn-custom')
        $('#modal #btnGuardar').removeClass('btnGuardar')
        $('#modal #btnGuardar').html('Agregar')
        $('#modal #rowForm').addClass('loader-in');

        let idTableNovedades = '#tableNovEdit tbody tr';

        if ($(idTableNovedades).length > 0) {
            $(idTableNovedades).removeClass('selected');
            // uncheck all checkboxes
            $(idTableNovedades).find('input[type="checkbox"]').prop('checked', false);
            // $(idTableNovedades).first().trigger('click');
        }
        disabledForm(false);

        generarOptgroupYOptions(ls.get(LS_NOVEDADES).agrupadas, '#Nove') // Agrega las novedades al select

        let data = ls.get(LS_FICHA_FORM);
        if (!data) return;

        if (data.NoveAdd == '0') {
            $.notifyClose();
            notify('No tiene permisos para agregar novedades', 'danger', 2000, 'right');
            $(this).prop('disabled', true).off('click');
            return;
        }

        $('#modal #rowForm').removeClass('loader-in');
        $('#modal #rowForm').fadeIn('slow'); // Muestra el formulario

    });
}
addNovedad();

const formAgregar = async () => {
    $(document).on('click', '#modal .btnAgregar', async function (e) {

        e.preventDefault();
        e.stopPropagation();

        let data = ls.get(LS_FICHA_FORM);
        if (!data) return;

        if (!data) {
            $.notifyClose();
            notify('No se encontraron datos', 'danger', 2000, 'right');
            return;
        }

        loading();
        $('#modal .modal-body').addClass('loader-in');

        if (data.Cierre.Estado != 'abierto') {
            $.notifyClose();
            notify('La ficha está cerrada', 'warning', 2000, 'right');
            return;
        }

        let formData = {
            Lega: data.Lega,
            Fecha: data.Fech,
            Cate: $('#NoveSec').is(':checked') ? '2' : '0',
            Nove: $('#Nove').val(),
            Horas: $('#Horas').val(),
            Obse: $('#Obs').val().substring(0, 40).trim(),
            Causa: $('#Causa').val(),
            Esta: "2"
        }
        let rs = await axios.post('../app-data/novedad', formData);
        // console.log(rs.data);
        if (rs.data.error) {
            $.notifyClose();
            $('#modal .modal-body').removeClass('loader-in');
            notify(rs.data.error, 'danger', 2000, 'right');
            return;
        }
        if (rs.data.MESSAGE == "OK") {
            $.notifyClose();
            $('#modal #btnGuardar').off('click')
            ActualizaTablas();
            notify('Novedad creada correctamente', 'success', 2000, 'right');
            getFicha(data.Lega, data.Fech).then((rs) => {
                if (!rs) {
                    ls.remove(LS_FICHA_FORM)
                    $('#modal').modal('hide');
                    return;
                }
                tableNoveEdit(rs[0]);
            });
            return;
        }
    });
}

formAgregar();

const deleteNovedad = async (data) => {

    if (!data) {
        $.notifyClose();
        notify('No se encontraron datos para eliminar', 'danger', 2000, 'right');
        return;
    }

    loading();
    $('#modal .modal-body').addClass('loader-in');
    if (data.Cierre.Estado != 'abierto') {
        $.notifyClose();
        notify('No se puede editar una ficha cerrada', 'warning', 2000, 'right');
        return;
    }

    let formData = {
        Lega: data.Lega,
        Fecha: data.Fech,
        Nove: data.Codi,
    }
    let rs = await axios.delete('../app-data/novedad', { data: formData });
    if (rs.data.error) {
        $.notifyClose();
        notify(rs.data.error, 'danger', 2000, 'right');
        return;
    }
    if (rs.data.MESSAGE == "OK") {
        $.notifyClose();
        notify('Novedad eliminada correctamente', 'success', 2000, 'right');
        ActualizaTablas();
        getFicha(data.Lega, data.Fech).then((rs) => {
            if (!rs) {
                $('#modal').modal('hide');
                return;
            }
            tableNoveEdit(rs[0]);
        });
        return;
    }
    notify(rs.data.MESSAGE ?? '' == "OK", 'danger', 2000, 'right');

}

const tableInfoFicha = () => {

    let Ficha = ls.get(LS_FICHA_FORM);

    let Horario = Ficha['TurStr'] ?? 'Sin horario';
    let Fichadas = Ficha.Fich
    let primerFichada = getFichada(Fichadas, 'primera');
    let ultimaFichada = getFichada(Fichadas, 'ultima');

    let linkCollapse = '<a class="p-0 btn-link" data-toggle="collapse" href="#collapseFichadas" role="button" aria-expanded="false" aria-controls="collapseFichadas"><i id="chevronCollapse" class="bi bi-chevron-down"></i></a>';
    let countFichadas = (Fichadas.length > 2) ? '<span>' + linkCollapse + '</span>' : '';

    let colsFichadas = (Fichadas) => {
        let html = '';
        if (Fichadas.length <= 2) return html;

        Fichadas.forEach((element, index) => {
            html += `<td class="text-center ${colorFichada(element)}">${element.HoRe}</td>`;
        });
        html += `<td class="w-100"></td>`;

        return html;
    };

    let colsTH = (Fichadas) => {
        let html = '';
        if (Fichadas.length <= 2) return html;

        Fichadas.forEach((element, indice) => {
            html += `<th class="text-center fontq">${(indice % 2 === 0) ? 'Entra' : 'Sale'}</th>`;
        });
        html += `<th class="w-100"></th>`;

        return html;
    };

    const collapseFichadas = (Fichadas) => {
        if (Fichadas.length <= 2) return '';
        return `
        <div class="card">
            <table id="collapseFichadas" class="collapse w-100 table table-responsive mb-0 mt-n1 border">
                <thead>
                    <tr>${colsTH(Fichadas)}</tr>
                </thead>
                <tbody>
                    <tr>${colsFichadas(Fichadas)}</tr>
                </tbody>
            </table>
        </div>`;
    }

    let html = `<table id="tableInfoFicha" class="table table-responsive text-nowrap mb-0 mt-n1">
        <thead>
            <tr>
                <th>Legajo</th>
                <th>Nombre</th>
                <th class="px-0">Fecha</th>
                <th></th>
                <th>Horario</th>
                <th class="text-center">Entra</th>
                <th class="text-center">Sale</th>
                <th class=""></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>${Ficha.Lega}</td>
                <td><div title="${Ficha.ApNo}" class="text-truncate" style="max-width:180px">${Ficha.ApNo}</div></td>
                <td class="px-0">${Ficha.FechD}</td>
                <td class="pl-0"><span class="ml-2">${Ficha.FechF}</span></td>
                <td>${Horario}</td>
                <td class="text-center">${primerFichada}</td>
                <td class="text-center">${ultimaFichada}</td>
                <td class="w-100">${countFichadas}</td>
            </tr>
        </tbody>
    </table>`;
    html += collapseFichadas(Fichadas);
    return html;
}

$(document).on('select2:select', '#Nove', async function (e) {
    e.preventDefault();
    e.stopPropagation();
    let NovCodi = $(this).val(); // Obtiene el valor seleccionado
    if (!NovCodi) return; // Si no hay valor seleccionado, no hace nada

    let rsData = ls.get(LS_NOVEDADES);
    if (!rsData) return;
    let causas = rsData.causas.filter((causa) => causa.CodiNov == NovCodi) ?? [];

    addSelectOptions(causas, '#Causa') // Agrega las causas al select
    $('#Causa').append(`< option value = "" selected ></option > `); // Agrega un option vacío
});

const getFicha = async (legajo, fecha) => {
    try {
        let rs = await axios.post('../app-data/ficha/' + legajo + '/' + fecha + '/');
        if (rs.data.length === 0) {
            return false;
        }
        return rs.data ?? [];
    } catch (error) {
        console.log(error);
        return null;
    }
}

const disabledForm = (disabled) => {
    $('#modal #btnGuardar').prop('disabled', disabled); // Oculta el botón guardar
    $('#Nove').val('').empty().prop('disabled', disabled).trigger('change'); // Limpia el select de novedades
    $('#Causa').val('').empty().prop('disabled', disabled).trigger('change'); // Limpia el select de causas
    $('#Obs').val('').prop('disabled', disabled).trigger('change'); // Limpia el input de observaciones
    $('#Horas').val('').prop('disabled', disabled).trigger('change'); // Limpia el input de Horas
    $('#NoveSec').prop('checked', false).prop('disabled', disabled); // Desmarca el checkbox
}
const disabledBtnEdita = () => {
    let btn = document.querySelectorAll('.editaNov');
    if (btn) {
        btn.forEach((item) => {
            item.classList.add('disabled');
            setTimeout(() => {
                console.log(item);
            }, 200);
        });
    }
}
function scrollHorizontally(event) {
    event.preventDefault();
    let delta = Math.max(-1, Math.min(1, (event.wheelDelta || -event.detail)));
    document.querySelector('.tabla-container').scrollLeft -= (delta * 40); // Ajusta la velocidad del desplazamiento horizontal según tu preferencia
}