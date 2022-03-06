// $(document).ready(function () {
const loadingTableUser = (selectortable) => {
    $(selectortable + ' td div').addClass('bg-light text-light border-0')
    $(selectortable + ' td div').css('height', '31px')
    $(selectortable + ' td img').addClass('invisible')
    $(selectortable + ' td i').addClass('invisible')
    $(selectortable + ' td span').addClass('invisible')
}
tableUsuarios = $('#tableUsuarios').DataTable({
    "initComplete": function (settings, json) {
        $('#tableUsuarios_filter').prepend('<button data-titlel="Nuevo usuario" class="btn btn-sm btn-custom h35 px-3" id="addUser"><i class="bi bi-person-plus-fill"></i></button>')
    },
    "drawCallback": function (settings) {

    },
    dom: "<'row'<'col-12 d-flex align-items-end m-0 justify-content-between'lf>>" +
        "<'row px-3'<'col-12 border shadow-sm table-responsive't>>" +
        "<'row d-none d-sm-block'<'col-12 d-flex align-items-center justify-content-between'ip>>"+
        "<'row d-block d-sm-none'<'col-12 d-flex align-items-center justify-content-center'p>>"+
        "<'row d-block d-sm-none'<'col-12 d-flex align-items-center justify-content-center'i>>",
    ajax: {
        url: "getUsuariosMobile.php",
        type: "POST",
        "data": function (data) { },
        error: function () { },
    },
    createdRow: function (row, data, dataIndex) {
        $(row).addClass('animate__animated animate__fadeIn align-middle');
    },
    columns: [
        {
            className: 'align-middle', targets: '', title: 'ID',
            "render": function (data, type, row, meta) {
                let datacol = `<div class="w90">${row.userID}</div>`
                return datacol;
            },
        },
        {
            className: 'align-middle', targets: '', title: 'Nombre',
            "render": function (data, type, row, meta) {
                let datacol = `<div class="">${row.userName}</div>`
                return datacol;
            },
        },
        {
            className: 'align-middle text-center', targets: '', title: 'Fichadas',
            "render": function (data, type, row, meta) {
                let datacol = `<div class="ls1">${row.userChecks}</div>`
                return datacol;
            },
        },
        {
            className: 'align-middle w-100', targets: '', title: '',
            "render": function (data, type, row, meta) {
                let activar = `<span data-titlel="Sin Reg ID" class="ml-1 btn btn-sm btn-outline-custom disabled border"><i class="bi bi-phone"></i></span>`;
                let mensaje = `<span data-titlel="Sin Reg ID" class="ml-1 btn btn-sm btn-outline-custom border bi bi-chat-text disabled"></span></span>`;

                if (row.userRegId.length > '100') {
                    activar = `<span data-regid="${row.userRegId}" data-userid="${row.userID}" data-titlel="Configurar dispositivo. Envía Legajo y Empresa" class="ml-1 btn btn-sm btn-outline-custom border sendSettings"><i class="bi bi-phone"></i></span>`
                }
                if (row.userRegId.length > '100') {
                    mensaje = `<span data-nombre="${row.userName}" data-regid="${row.userRegId}"  data-titlel="Enviar Mensaje" class="ml-1 btn btn-sm btn-outline-custom border bi bi-chat-text sendMensaje"></span>`
                }
                let datacol = `
                <div class="d-flex justify-content-end">
                    <span data-titlel="Editar" data-iduser="${row.userID}" data-nombre="${row.userName}" class="btn btn-outline-custom btn-sm border bi bi-pen updateUser"></span>
                    ${mensaje}
                    ${activar}
                    <span data-titlel="Eliminar" data-iduser="${row.userID}" data-nombre="${row.userName}" class="ml-1 btn btn-outline-custom btn-sm border bi bi-trash deleteUser"></span>
                </div>
                `
                return datacol;
            },
        },

    ],
    lengthMenu: [[5, 10, 25, 50, 100, 200], [5, 10, 25, 50, 100, 200]],
    bProcessing: false,
    serverSide: true,
    deferRender: true,
    searchDelay: 1000,
    paging: true,
    searching: true,
    info: true,
    ordering: false,
    language: {
        "url": "../../js/DataTableSpanishShort2.json?v=" + vjs(),
    },

});
tableUsuarios.on('draw.dt', function (e, settings) {
    // $('#modalUsuarios').modal('show')
    $('#tableUsuarios_filter .form-control-sm').attr('placeholder', 'Buscar usuarios')
    $('#RowTableUsers').removeClass('invisible')
});
tableUsuarios.on('page.dt', function (e, settings) {
    loadingTableUser('#tableUsuarios')
});
tableUsuarios.on('xhr', function (e, settings, json) {
    tableUsuarios.off('xhr');
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
    let userid = $(this).attr('data-userid')
    CheckSesion()
    $.ajax({
        type: 'post',
        url: 'crud.php',
        data: {
            tipo: 'c_setUserEmp',
            regid: regid,
            userid: userid,
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