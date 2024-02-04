const homehost = $("#_homehost").val();
const LS_FICHA_FORM = homehost + '_ficha_form';
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
    dom: '<"d-inline-flex d-flex align-items-center"t<"ml-2"p>><"mt-n3 d-flex justify-content-end"i>',
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
        dom: '<"d-inline-flex d-flex align-items-center"t<"ml-2"p>><"mt-n3 d-flex justify-content-end"i>',
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

const modalEditNove = async (data) => {
    loading();
    try {

        if (!data) { // Si no hay datos
            throw new Error('No se encontraron datos para editar');
        }

        let tipo_cod = data.tipo_cod ?? null; // Obtiene el código del tipo de novedad
        let nove_cod = data.cod ?? null; // Obtiene el código de la novedad

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
                return;
            }
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

        let Novedades = Ficha.Nove ?? [];
        Novedades.forEach((nove) => {
            nove.Fech = Ficha.Fech;
            nove.Lega = Ficha.Lega;
            nove.Cierre = Ficha.Cierre;
            nove.NoveDelete = Ficha.NoveDelete;
        });

        let Tur = Ficha.Tur; // array de Turno
        let Fichadas = Ficha.Fich; // array de fichadas
        // crear un string con los elementos del array Fichadas separados por una coma
        let hoReArray = Fichadas.map(function (item) {
            return item.HoRe;
        });

        // Crear un string separado por comas de los valores de "HoRe"
        var StrFichadas = hoReArray.join(', ');

        let countFichadas = (Fichadas.length > 2) ? '<span title="' + StrFichadas + '">...</span>' : '';
        let Horario = getHorario(Tur, Ficha['Labo'], Ficha['Feri']) ?? 'Sin horario';
        let periodo = '';
        let primerFichada = getFichada(Fichadas, 'primera');
        let ultimaFichada = getFichada(Fichadas, 'ultima');
        ultimaFichada = (primerFichada === ultimaFichada) ? '-' : ultimaFichada;

        if (Ficha.Cierre.Estado != 'abierto') {
            periodo = '(Periodo ' + Ficha.Cierre.Estado + ')';
        }
        $('.modal-title').html('Editar Novedades ' + periodo) // Cambia el título del modal

        if ($.fn.DataTable.isDataTable('#tableNovEdit')) {
            $('#tableNovEdit').DataTable().destroy();
        }

        let dt = $('#tableNovEdit').DataTable({
            dom: `
        <'row '
            <'col-12 divFichadas'>
        >
        <'row '
            <'col-12 table-responsive mt-0't>
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
                row.setAttribute('data-titlet', 'Editar: ');
            },
            columns: [
                {
                    data: '', className: 'align-middle text-center', targets: '', title: '',
                    "render": function (data, type, row, meta) {
                        return `<div class="custom-control custom-checkbox">
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
                },
                {
                    data: 'NoveDelete', className: 'align-middle text-center px-0', targets: '', title: '',
                    "render": function (data, type, row, meta) {
                        if (data == '1') {
                            return `<delete data-titlel="Eliminar Novedad" class="btn btn-sm btn-outline-danger border-0 mx-2 bi bi-trash"></delete>`;
                        }
                        return ``;
                    },
                },
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

            let tableInfo = tableInfoFicha(Ficha.Lega, Ficha.ApNo, formatDate3(Ficha.Fech), Horario, primerFichada, ultimaFichada, countFichadas);

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

                if (e.target.closest('tr').tagName) {

                    let data = dt.row(e.target.closest('tr')).data();
                    if (!data) return;

                    if (e.target.tagName == 'DELETE') {
                        deleteNovedad(data);
                        return;
                    }

                    $('#modal #rowForm').addClass('loader-in');

                    let checkbox = e.currentTarget.querySelector('input[type="checkbox"]');
                    let classList = e.currentTarget.classList; // Obtiene la lista de clases del tr

                    if (classList.contains('selected')) { // Si el tr está seleccionado
                        classList.remove('selected'); // Quita la clase selected
                        $('#modal #rowForm').hide(); // Oculta el formulario
                        disabledForm(true);
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
                                $('#modal #btnGuardar').prop('disabled', false);
                                $('#modal #rowForm').removeClass('loader-in');
                                ls.set(LS_FICHA_FORM, Ficha);
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

        if (!data.Codi) { // Si no hay novedad o tipo de novedad
            throw new Error('No se encontraron datos para editar');
        }

        let rsData = await axios('data/novedades/' + data.NoTi + '/' + data.Codi); // Busca las novedades y causas
        let novedades = (rsData.data.novedades ?? []); // Obtiene las novedades
        let causas = (rsData.data.causas ?? []); // Obtiene las causas
        // $('#modal #rowForm').fadeIn('slow'); // Muestra el formulario
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

const addSelectOptions = (array, selector) => {
    $(selector).prop('disabled', true).empty().trigger('change');
    if (!array) return false;
    if (array.length > 0) {
        $(selector).append(`<option value=""></option>`);
        Object.keys(array).forEach(element => {
            let opt = array[element];
            $(selector).append(`<option value="${opt.Codi}">${opt.Desc}</option>`);
        });
        $(selector).prop('disabled', false);
    }
}

const formGuardar = async () => {
    $(document).on('click', '#modal #btnGuardar', async function (e) {
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
        disabledForm(true);
        let rs = await axios.put('data/novedad', formData);
        // console.log(rs.data);
        if (rs.data.error) {
            $.notifyClose();
            disabledForm(false);
            notify(rs.data.error, 'danger', 2000, 'right');
            return;
        }
        if (rs.data.MESSAGE == "OK") {
            $.notifyClose();
            $('#modal #btnGuardar').off('click')
            ls.remove(LS_FICHA_FORM);
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

const deleteNovedad = async (data) => {

    if (!data) {
        $.notifyClose();
        notify('No se encontraron datos para eliminar', 'danger', 2000, 'right');
        return;
    }

    loading();

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
    let rs = await axios.delete('data/novedad', { data: formData });
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
    $.notifyClose();
    notify(rs.data.MESSAGE ?? '' == "OK", 'danger', 2000, 'right');

}

const tableInfoFicha = (Lega, ApNo, Fech, Horario, primerFichada, ultimaFichada, countFichadas) => `
<table id="tableInfoFicha" class="table table-responsive text-nowrap mb-0 mt-n1">
    <thead>
        <tr>
            <th>Legajo</th>
            <th>Nombre</th>
            <th>Fecha</th>
            <th>Horario</th>
            <th class="text-center">Entrada</th>
            <th class="text-center">Salida</th>
            <th class=""></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>${Lega}</td>
            <td><div title="${ApNo}" class="text-truncate" style="max-width:200px">${ApNo}</div></td>
            <td>${Fech}</td>
            <td>${Horario}</td>
            <td class="text-center">${primerFichada}</td>
            <td class="text-center">${ultimaFichada}</td>
            <td class="w-100">${countFichadas}</td>
        </tr>
    </tbody>
</table>
`

$(document).on('select2:select', '#Nove', async function (e) {
    e.preventDefault();
    e.stopPropagation();
    let NovCodi = $(this).val(); // Obtiene el valor seleccionado
    if (!NovCodi) return; // Si no hay valor seleccionado, no hace nada
    let rsCausas = await axios('data/causas/' + NovCodi); // Busca las causas
    let causas = (rsCausas.data.causas ?? []); // Obtiene las causas
    addSelectOptions(causas, '#Causa') // Agrega las causas al select
    $('#Causa').append(`<option value="" selected></option>`); // Agrega un option vacío
});

const getFicha = async (legajo, fecha) => {
    try {
        let rs = await axios.post('data/ficha/' + legajo + '/' + fecha + '/');
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