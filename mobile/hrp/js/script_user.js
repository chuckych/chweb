// $(document).ready(function () {

tableUsuarios = $('#tableUsuarios').DataTable({
    "initComplete": function (settings, json) {
        $('#tableUsuarios_filter').prepend('<button data-titlel="Nuevo usuario" class="btn btn-sm btn-custom h35 px-3" id="addUser"><i class="bi bi-person-plus-fill"></i></button>')
    },
    "drawCallback": function (settings) {
        classEfect("#tableUsuarios tbody", 'animate__animated animate__fadeIn')
        setTimeout(function () {
            loadingTableRemove('#modalUsuarios')
        }, 100);
        $('#tableUsuarios_filter .form-control-sm').attr('placeholder', 'Buscar usuarios')
    },
    dom: "<'row'<'col-12 d-flex align-items-end m-0 justify-content-between'lf>>" +
        "<'row'<'col-12'tr>>" +
        "<'row'<'col-12 d-flex align-items-center justify-content-between'ip>>",
    ajax: {
        url: "getUsuariosMobile.php?v=" + vjs(),
        type: "POST",
        "data": function (data) { },
        error: function () { },
    },
    createdRow: function (row, data, dataIndex) {
        $(row).addClass('animate__animated animate__fadeIn align-middle');
    },
    columnDefs: [
        { title: 'Legajo', className: '', targets: 0 },
        { title: 'Nombre', className: 'w-100', targets: 1 },
        { title: 'Fichadas', className: 'text-center', targets: 2 },
        { title: '', className: 'text-center', targets: 3 },
        // { title: '', className: 'text-center', targets: 4 },
    ],
    bProcessing: true,
    serverSide: true,
    deferRender: true,
    searchDelay: 1500,
    paging: true,
    searching: true,
    info: true,
    ordering: false,
    language: {
        "url": "../../js/DataTableSpanishShort2.json"
    },

});
tableUsuarios.on('processing.dt', function (e, settings, processing) {
    e.preventDefault()
    loadingTable('#tableUsuarios')
    CheckSesion()
    e.stopImmediatePropagation();
});

function printFormUsuario(selectorAction, data, action) {
    CheckSesion()
    $.ajax({
        type: 'post',
        dataType: "html",
        url: "formUsuario.php?v=" + vjs(),
        data: {
            value: data,
            action: action
        },
        beforeSend: function (xhr) {
            $(selectorAction).html('<div class="p-3 text-secondary animate__animated animate__fadeIn">Cargando..</div>')
        }
    }).done(function (data) {
        $(selectorAction).html(data)
    });
}
function printFormMensaje(selectorAction, data, action) {
    CheckSesion()
    $.ajax({
        type: 'post',
        dataType: "html",
        url: "formMensaje.php?v=" + vjs(),
        data: {
            value: data,
            action: action
        },
        beforeSend: function (xhr) {
            $(selectorAction).html('<div class="p-3 text-secondary animate__animated animate__fadeIn">Cargando..</div>')
        }
    }).done(function (data) {
        $(selectorAction).html(data)
    });
}

$(document).on("click", "#addUser", function (e) {
    printFormUsuario('#divformUsuario', '')
});
$(document).on("click", ".updateUser", function (e) {
    let dataiduser = $(this).attr('data-iduser')
    printFormUsuario('#divformUsuario', dataiduser, 'update')
});
$(document).on("click", ".deleteUser", function (e) {
    let dataiduser = $(this).attr('data-iduser')
    printFormUsuario('#divformUsuario', dataiduser, 'delete')
});
$(document).on("click", ".sendMensaje", function (e) {
    let data = $(this).attr('data-regid') + '@' + $(this).attr('data-nombre')
    printFormMensaje('#divformUsuario', data, 'mensaje')
});
$(document).on("click", ".sendSettings", function (e) {
    e.preventDefault();
    let regid = $(this).attr('data-regid')
    CheckSesion()
    $.ajax({
        type: 'post',
        url: 'crud.php',
        data: {
            tipo: 'c_settings',
            regid: regid,
        },
        beforeSend: function (data) {
            $.notifyClose();
            notify('Aguarde..', 'info', 0, 'right')
        },
        success: function (data) {
            if (data.status == "ok") {
                $.notifyClose();
                notify(data.Mensaje, 'success', 5000, 'right')
                // $('#divformUsuario').html('')
            } else {
                $.notifyClose();
                notify(data.Mensaje, 'danger', 5000, 'right')
            }
        },
        error: function () { }
    });
    e.stopImmediatePropagation();
});

// });