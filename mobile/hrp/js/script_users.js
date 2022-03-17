// $(document).ready(function () {
const loadingTableUser = (selectortable) => {
    $(selectortable + ' td div').addClass('bg-light text-light border-0')
    $(selectortable + ' td div').css('height', '31px')
    $(selectortable + ' td img').addClass('invisible')
    $(selectortable + ' td i').addClass('invisible')
    $(selectortable + ' td span').addClass('invisible')
}
tableUsuarios = $('#tableUsuarios').DataTable({
    "drawCallback": function (settings) {

    },
    dom: "<'row lengthFilterTable'<'col-12 d-flex align-items-end m-0 justify-content-between'lf>>" +
        "<'row '<'col-12 table-responsive't>>" +
        "<'row d-none d-sm-block'<'col-12 d-flex bg-white align-items-center justify-content-between'ip>>" +
        "<'row d-block d-sm-none'<'col-12 fixed-bottom h70 bg-white d-flex align-items-center justify-content-center'p>>" +
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
        /** Columna ID */
        {
            className: 'align-middle', targets: '', title: '<div class="w80">ID</div>',
            "render": function (data, type, row, meta) {
                let datacol = `<div class="w80">${row.userID}</div>`
                return datacol;
            },
        },
        /** Columna Nombre */
        {
            className: 'align-middle', targets: '', title: `<div class="w120">Nombre</div>`,
            "render": function (data, type, row, meta) {
                let datacol = `<div class="text-truncate w120">${row.userName}</div>`
                return datacol;
            },
        },
        /** Columna cant Fichadas */
        {
            className: 'align-middle', targets: '', title: '<div class="w50">Fichadas</div>',
            "render": function (data, type, row, meta) {
                let datacol = `<div class="ls1">${row.userChecks}</div>`
                return datacol;
            },
        },
        /** Columna Acciones */
        {
            className: 'align-middle w-100', targets: '', title: '',
            "render": function (data, type, row, meta) {
                let activar = `<span data-titlel="Sin Reg ID" class="ml-1 btn btn-sm btn-outline-custom disabled border"><i class="bi bi-phone"></i></span>`;
                let mensaje = `<span data-titlel="Sin Reg ID" class="ml-1 btn btn-sm btn-outline-custom border bi bi-chat-text disabled"></span></span>`;
                let del = `<span data-titlel="No se puede eliminar" data-iduser="${row.userID}" data-nombre="${row.userName}" class="ml-1 btn btn-outline-custom btn-sm border bi bi-trash disabled"></span>`;

                if (row.userRegId.length > '100') {
                    activar = `<span data-titlel="Configurar dispositivo. Envía Legajo y Empresa" class="ml-1 btn btn-sm btn-outline-custom border sendSettings"><i class="bi bi-phone"></i></span>`
                }
                if (row.userRegId.length > '100') {
                    mensaje = `<span data-nombre="${row.userName}" data-regid="${row.userRegId}"  data-titlel="Enviar Mensaje" class="ml-1 btn btn-sm btn-outline-custom border bi bi-chat-text sendMensaje"></span>`
                }
                if (row.userChecks < 1) {
                    del = `<span data-titlel="Eliminar" data-iduser="${row.userID}" data-nombre="${row.userName}" class="ml-1 btn btn-outline-custom btn-sm border bi bi-trash deleteUser"></span>`;
                }
                let datacol = `
                <div class="d-flex justify-content-end">
                    <span data-titlel="Editar" data-iduser="${row.userID}" data-nombre="${row.userName}" class="btn btn-outline-custom btn-sm border bi bi-pen updateUser"></span>
                    ${mensaje}
                    ${activar}
                    ${del}
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
    // scrollY: '52vh',
    scrollY: '281px',
    scrollCollapse: true,
    scrollX: true,
    fixedHeader: false,
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
tableUsuarios.on('xhr.dt', function (e, settings, json) {
    tableUsuarios.off('xhr.dt');
});
tableUsuarios.on('init.dt', function (e, settings, json) {
    $('#tableUsuarios_filter').prepend('<button data-titlel="Nuevo usuario" class="btn btn-sm btn-custom h40 opa8 px-3" id="addUser"><i class="bi bi-plus-lg"></i></button>')
    $('#tableUsuarios_filter input').removeClass('form-control-sm')
    $('#tableUsuarios_filter input').attr("style","height: 40px !important");
    select2Simple('#tableUsuarios_length select', '', false, false)
});
$(document).on("click", ".sendSettings", function (e) {
    e.preventDefault();
    // data datatable
    let data = tableUsuarios.row($(this).parents('tr')).data();
    CheckSesion()
    $.ajax({
        type: 'post',
        url: 'crud.php',
        data: {
            tipo: 'send_UserSet',
            regid: data.userRegId,
            userid: data.userID,
        },
        beforeSend: function (data) {
            $.notifyClose();
            notify('Aguarde..', 'info', 0, 'right')
        },
        success: function (data) {
            if (data.status == "ok") {
                $.notifyClose();
                notify('Dispositivo configurado correctamente.', 'success', 5000, 'right')
            } else {
                $.notifyClose();
                notify('No se puedo configurar el dispositivo', 'danger', 5000, 'right')
            }
        },
        error: function () { }
    });
    e.stopImmediatePropagation();
});
$(document).on("click", "#addUser", function (e) {
    let data = $('#tableUsuarios').DataTable().row($(this).parents('tr')).data();
    axios({
        method: 'post',
        url: 'modalUser.html?v=' + $.now(),
    }).then(function (response) {
        $('#modales').html(response.data)
    }).then(function () {
        $('#modalUser .modal-title').html('Nuevo Usuario')
        $('#modalUser').modal('show');
        // $('#formUser .requerido').html('(*)')
        $('#formUser .form-control').attr('autocomplete', 'off')
        $('#formUser #tipo').val('add_usuario')
        $('#formUser #formUserID').mask('00000000000', { reverse: false });
        setTimeout(() => {
            $('#formUser #formUserID').focus();
        }, 500);

    }).then(function () {

    }).catch(function (error) {
        alert(error)
    }).then(function () {
        $("#formUser").bind("submit", function (e) {
            e.preventDefault();
            let tipoStatus = '';
            switch ($('#formUser #tipo').val()) {
                case 'del_usuario':
                    tipoStatus = 'eliminado';
                    break;
                case 'upd_usuario':
                    tipoStatus = 'actualizado';
                    break;
                case 'add_usuario':
                    tipoStatus = 'creado';
                    break;
                default:
                    tipoStatus = '';
                    break;
            }
            $.ajax({
                type: $(this).attr("method"),
                url: 'crud.php',
                data: $(this).serialize(),
                // dataType: "json",
                beforeSend: function (data) {
                    CheckSesion()
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                    ActiveBTN(true, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        let userName = data.Mensaje.userName
                        let userID = data.Mensaje.userID
                        notify('ID ' + userID + ' ' + tipoStatus + ' correctamente<br>Nombre: ' + userName + '.', 'success', 5000, 'right')
                        // $('#tableUsuarios').DataTable().ajax.reload();
                        $('#table-mobile').DataTable().ajax.reload(null, false);
                        $('#tableUsuarios').DataTable().ajax.reload();
                        ActiveBTN(false, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
                        $('#modalUser').modal('hide');
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                        ActiveBTN(false, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
                    }
                },
                error: function () { }
            });
        });
        $('#modalUser').on('hidden.bs.modal', function () {
            $('#modales').html(' ');
        });
    });
});
$(document).on("click", ".updateUser", function (e) {
    let data = $('#tableUsuarios').DataTable().row($(this).parents('tr')).data();
    console.log(data);
    axios({
        method: 'post',
        url: 'modalUser.html?v=' + $.now(),
    }).then(function (response) {
        $('#modales').html(response.data)
    }).then(function () {
        $('#modalUser .modal-title').html(`
            <div>Editar Usuario</div>
            <div class="text-muted fontq">ID: ${data.userID}</div> 
        `)
        $('#modalUser').modal('show');
        // $('#formUser .requerido').html('(*)')
        $('#formUser .form-control').attr('autocomplete', 'off')
        $('#formUser #tipo').val('upd_usuario')
        $('#formUser #formUserID').mask('00000000000', { reverse: false });
        $('#formUser #formUserID').attr('hidden', 'hidden')
        $('#formUser #formUserID').attr('type', 'hidden')
        $('#formUser #formUserID').val(data.userID);
        $('#formUser #formUserID').attr('readonly', 'readonly')
        $('#formUser #formUserName').val(data.userName);
        $('#formUser #formUserRegid').val(data.userRegId);

        $('#formUser #divid_user').hide()
        setTimeout(() => {
            focusEndText('#formUser #formUserName')
        }, 500);

    }).then(function () {

    }).catch(function (error) {
        alert(error)
    }).then(function () {
        $("#formUser").bind("submit", function (e) {
            e.preventDefault();
            let tipoStatus = '';
            switch ($('#formUser #tipo').val()) {
                case 'del_usuario':
                    tipoStatus = 'eliminado';
                    break;
                case 'upd_usuario':
                    tipoStatus = 'actualizado';
                    break;
                case 'add_usuario':
                    tipoStatus = 'creado';
                    break;
                default:
                    tipoStatus = '';
                    break;
            }
            $.ajax({
                type: $(this).attr("method"),
                url: 'crud.php',
                data: $(this).serialize(),
                // dataType: "json",
                beforeSend: function (data) {
                    CheckSesion()
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                    ActiveBTN(true, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        let userName = data.Mensaje.userName
                        let userID = data.Mensaje.userID
                        notify('ID ' + userID + ' ' + tipoStatus + ' correctamente<br>Nombre: ' + userName + '.', 'success', 5000, 'right')
                        // $('#tableUsuarios').DataTable().ajax.reload();
                        $('#table-mobile').DataTable().ajax.reload(null, false);
                        $('#tableUsuarios').DataTable().ajax.reload();
                        ActiveBTN(false, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
                        $('#modalUser').modal('hide');
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                        ActiveBTN(false, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
                    }
                },
                error: function () { }
            });
        });
        $('#modalUser').on('hidden.bs.modal', function () {
            $('#modales').html(' ');
        });
    });
});
$(document).on("click", ".deleteUser", function (e) {
    let data = $('#tableUsuarios').DataTable().row($(this).parents('tr')).data();
    console.log(data);
    axios({
        method: 'post',
        url: 'modalUser.html?v=' + $.now(),
    }).then(function (response) {
        $('#modales').html(response.data)
    }).then(function () {
        $('#modalUser .modal-title').html(`
            <div class="text-danger">¿Eliminar Usuario?</div>
            <div class="text-muted fontq">ID: ${data.userID}</div> 
        `)
        $('#modalUser').modal('show');
        // $('#formUser .requerido').html('(*)')
        $('#formUser .form-control').attr('autocomplete', 'off')
        $('#formUser #tipo').val('del_usuario')
        $('#formUser #formUserID').mask('00000000000', { reverse: false });
        $('#formUser #formUserID').attr('hidden', 'hidden')
        $('#formUser #formUserID').attr('type', 'hidden')
        $('#formUser #formUserID').val(data.userID);
        $('#formUser #formUserID').attr('readonly', 'readonly')
        $('#formUser #formUserName').attr('readonly', 'readonly')
        $('#formUser #formUserRegid').attr('readonly', 'readonly')
        $('#formUser #formUserName').val(data.userName);
        $('#formUser #formUserRegid').val(data.userRegId);
        $('#formUser #divid_user').hide()

    }).then(function () {

    }).catch(function (error) {
        alert(error)
    }).then(function () {
        $("#formUser").bind("submit", function (e) {
            e.preventDefault();
            let tipoStatus = '';
            switch ($('#formUser #tipo').val()) {
                case 'del_usuario':
                    tipoStatus = 'eliminado';
                    break;
                case 'upd_usuario':
                    tipoStatus = 'actualizado';
                    break;
                case 'add_usuario':
                    tipoStatus = 'creado';
                    break;
                default:
                    tipoStatus = '';
                    break;
            }
            $.ajax({
                type: $(this).attr("method"),
                url: 'crud.php',
                data: $(this).serialize(),
                // dataType: "json",
                beforeSend: function (data) {
                    CheckSesion()
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                    ActiveBTN(true, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        let userName = data.Mensaje.userName
                        let userID = data.Mensaje.userID
                        notify('ID ' + userID + ' ' + tipoStatus + ' correctamente<br>Nombre: ' + userName + '.', 'success', 5000, 'right')
                        // $('#tableUsuarios').DataTable().ajax.reload();
                        $('#table-mobile').DataTable().ajax.reload(null, false);
                        $('#tableUsuarios').DataTable().ajax.reload();
                        ActiveBTN(false, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
                        $('#modalUser').modal('hide');
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                        ActiveBTN(false, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
                    }
                },
                error: function () { }
            });
        });
        $('#modalUser').on('hidden.bs.modal', function () {
            $('#modales').html(' ');
        });
    });
});
$(document).on("click", ".sendMensaje", function (e) {
    let data = $('#tableUsuarios').DataTable().row($(this).parents('tr')).data();
    console.log(data);
    // return false;
    axios({
        method: 'post',
        url: 'modalMsg.html?v=' + $.now(),
    }).then(function (response) {
        $('#modales').html(response.data)
    }).then(function () {

        $('#modalMsg .modal-title').html(`
            <div>Enviar Mensaje</div>
            <div class="text-secondary fontq">${data.userName}</div>
        `)
        $('#modalMsg').modal('show');
        // $('#formMsg .requerido').html('(*)')
        $('#formMsg .form-control').attr('autocomplete', 'off')
        $('#formMsg #tipo').val('send_mensaje')
        $('#formMsg #modalMsgRegID').val(data.userRegId)
        setTimeout(() => {
            $('#formMsg #modalMsgMensaje').focus();
        }, 500);

    }).then(function () {

    }).catch(function (error) {
        alert(error)
    }).then(function () {
        $("#formMsg").bind("submit", function (e) {
            e.preventDefault();
            let tipoStatus = '';
            $.ajax({
                type: $(this).attr("method"),
                url: 'crud.php',
                data: $(this).serialize(),
                // dataType: "json",
                beforeSend: function (data) {
                    CheckSesion()
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                    ActiveBTN(true, "#submitMsg", 'Aguarde ' + loading, 'Aceptar')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        notify('Mensaje enviado correctamente', 'success', 5000, 'right')
                        ActiveBTN(false, "#submitMsg", 'Aguarde ' + loading, 'Aceptar')
                        $('#modalMsg').modal('hide');
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                        ActiveBTN(false, "#submitMsg", 'Aguarde ' + loading, 'Aceptar')
                    }
                },
                error: function () { }
            });
        });
        $('#modalMsg').on('hidden.bs.modal', function () {
            $('#modales').html(' ');
        });
    });
});