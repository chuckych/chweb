// $(document).ready(function () {

const homehost = $("#_homehost").val();
const LS_MODAL_USER = homehost + '_mobile_modal_user';
const LS_MODAL_TRAIN = homehost + '_mobile_modal_train';
if (!ls.get(LS_MODAL_USER)) {
    axios.get('modalUser.php').then((response) => {
        ls.set(LS_MODAL_USER, response.data);
    }).catch(() => {
        ls.remove(LS_MODAL_USER);
    });
}
if (!ls.get(LS_MODAL_TRAIN)) {
    axios.get('modalTrain.php').then((response) => {
        ls.set(LS_MODAL_TRAIN, response.data);
    }).catch(() => {
        ls.remove(LS_MODAL_TRAIN);
    });
}


const loadingTableUser = (selectorTable) => {
    $(selectorTable).addClass('loader-in');

    // $(selectorTable + ' td div').addClass('bg-light text-light border-0')
    // $(selectorTable + ' td div').css('height', '31px')
    // $(selectorTable + ' td img').addClass('invisible')
    // $(selectorTable + ' td i').addClass('invisible')
    // $(selectorTable + ' td span').addClass('invisible')
}
if ($(window).width() < 540) {
    tableUsuarios = $('#tableUsuarios').DataTable({
        "drawCallback": function (settings) {

        },
        dom: "<'row lengthFilterTable'<'col-12 d-flex align-items-end m-0 justify-content-between'lf>>" +
            "<'row '<'col-12 table-responsive't>>" +
            "<'fixed-bottom'<''<'d-flex p-0 justify-content-center'p><'pb-2'i>>>",
        ajax: {
            url: "getUsuariosMobile.php",
            type: "POST",
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass('animate__animated animate__fadeIn align-middle');
        },
        columns: [
            /** Columna Usuario Mobile */
            {
                className: 'align-middle w-100', targets: '', title: '<div class="">Usuarios</div>',
                "render": function (data, type, row, meta) {

                    let activar = `<span data-titlel="Sin Reg ID" class="ml-1 btn btn-outline-custom disabled border"><i class="bi bi-phone"></i></span>`;
                    let mensaje = `<span data-titlel="Sin Reg ID" class="ml-1 btn btn-outline-custom border bi bi-chat-text disabled"></span></span>`;
                    let del = `<span data-titlel="No se puede eliminar" data-iduser="${row.userID}" data-nombre="${row.userName}" class="ml-1 btn btn-outline-custom border bi bi-trash disabled"></span>`;
                    let train = `<span data-titlel="No se puede entrenar" data-iduser="${row.userID}" data-nombre="${row.userName}" class="ml-1 btn btn-outline-custom border bi bi-person-bounding-box disabled"></span>`;

                    if (row.userRegId.length > '100') {
                        activar = `<span data-titlel="Configurar dispositivo. Envía Legajo y Empresa" class="ml-1 btn btn-outline-custom border sendSettings"><i class="bi bi-phone"></i></span>`
                    }
                    if (row.userRegId.length > '100') {
                        mensaje = `<span data-nombre="${row.userName}" data-regid="${row.userRegId}"  data-titlel="Enviar Mensaje" class="ml-1 btn btn-outline-custom border bi bi-chat-text sendMensaje"></span>`
                    }
                    if (row.userChecks < 1) {
                        del = `<span data-titlel="Eliminar" data-iduser="${row.userID}" data-nombre="${row.userName}" class="ml-1 btn btn-outline-custom border bi bi-trash deleteUser"></span>`;
                    }
                    if (row.userChecks > 0) {
                        train = `<span data-titlel="Entrenar rostro" data-iduser="${row.userID}" data-nombre="${row.userName}" class="ml-1 btn btn-outline-custom border bi bi-person-bounding-box trainUser"></span>`;
                    }
                    let datacol = `
                            <div class="font-weight-bold text-secondary text-uppercase">${row.userName}</div>
                            <div class="text-secondary">ID: ${row.userID}</div>
                            <div class="d-flex justify-content-end mt-2">
                            <span data-titlel="Editar" data-iduser="${row.userID}" data-nombre="${row.userName}" class="btn btn-outline-custom border bi bi-pen updateUser"></span>
                            ${train}
                            ${del}
                            </div>
                            `
                    return datacol;
                },
            },
        ],
        lengthMenu: [[3, 10, 25, 50, 100, 200], [3, 10, 25, 50, 100, 200]],
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        searchDelay: 1000,
        paging: true,
        searching: true,
        info: true,
        ordering: false,
        // scrollY: '52vh',
        scrollY: '320px',
        scrollCollapse: true,
        scrollX: true,
        fixedHeader: false,
        language: {
            "url": "../../js/DataTableSpanishShort2.json?v=" + vjs(),
        },

    });
} else {
    tableUsuarios = $('#tableUsuarios').DataTable({
        "drawCallback": function (settings) {

        },
        dom: "<'row lengthFilterTable'<'col-12 d-flex align-items-end m-0 justify-content-between'lf>>" +
            "<'row '<'col-12 table-responsive't>>" +
            "<'row d-none d-sm-block'<'col-12 d-flex align-items-center justify-content-between'ip>>" +
            "<'row d-block d-sm-none'<'col-12 fixed-bottom h70 d-flex align-items-center justify-content-center'p>>" +
            "<'row d-block d-sm-none'<'col-12 d-flex align-items-center justify-content-center'i>>",
        ajax: {
            url: "getUsuariosMobile.php",
            type: "POST",
            "data": function (data) { },
            error: function () { },
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass('align-middle');
        },
        columns: [
            /** Columna ID */
            {
                className: 'align-middle', targets: '', title: '<div class="w80">ID</div>',
                "render": function (data, type, row, meta) {
                    let bloqueado = '';
                    if (row.bloqueado == true) {
                        bloqueado = 'text-danger font-weight-bold';
                    }
                    let datacol = `<div class="w80 ${bloqueado}">${row.userID}</div>`
                    return datacol;
                },
            },
            /** Columna Nombre */
            {
                className: 'align-middle', targets: '', title: `<div class="w200">Nombre</div>`,
                "render": function (data, type, row, meta) {
                    let bloqueado = '';
                    if (row.bloqueado == true) {
                        bloqueado = 'text-danger font-weight-bold';
                    }
                    let datacol = `<div class="text-truncate w200 ${bloqueado}">${row.userName}</div>`
                    return datacol;
                },
            },
            /** Columna cant Fichadas */
            {
                className: 'align-middle text-center', targets: '', title: '<div class="w100">Registros</div>',
                "render": function (data, type, row, meta) {
                    let bloqueado = '';
                    if (row.bloqueado == true) {
                        bloqueado = 'text-danger font-weight-bold';
                    }
                    let datacol = `<div class="w100 ls1 ${bloqueado}">${row.userChecks}</div>`
                    return datacol;
                },
            },
            /** Columna Acciones */
            {
                className: 'align-middle w-100', targets: '', title: '',
                "render": function (data, type, row, meta) {

                    let colorTrained = (row.trained == true) ? 'success' : 'primary'
                    let textTrained = (row.trained == true) ? 'Enrolado' : 'No enrolado'

                    let activar = `<span data-titlel="Sin Reg ID" class=" btn btn-outline-custom disabled border"><i class="bi bi-phone"></i></span>`;
                    let mensaje = `<span data-titlel="Sin Reg ID" class=" btn btn-outline-custom border-0 bi bi-chat-text disabled "></span></span>`;
                    let del = `<span data-titlel="No se puede eliminar" data-iduser="${row.userID}" data-nombre="${row.userName}" class=" btn btn-outline-custom border-0 bi bi-trash disabled"></span>`;
                    let train = `<span data-titlel="No se puede entrenar" data-iduser="${row.userID}" data-nombre="${row.userName}" class=" btn btn-outline-custom border-0 bi bi-person-bounding-box disabled"></span>`;

                    if (row.userRegId.length > '100') {
                        activar = `<span data-titlel="Configurar dispositivo. Envía Legajo y Empresa" class=" btn btn-outline-custom border-0 sendSettings"><i class="bi bi-phone"></i></span>`
                    }
                    if (row.userRegId.length > '100') {
                        mensaje = `<span data-nombre="${row.userName}" data-regid="${row.userRegId}"  data-titlel="Enviar Mensaje" class=" btn btn-outline-custom border-0 bi bi-chat-text sendMensaje"></span>`
                    }
                    if (row.userChecks < 1) {
                        del = `<span data-titlel="Eliminar" data-iduser="${row.userID}" data-nombre="${row.userName}" class=" btn btn-outline-custom border-0 bi bi-trash deleteUser"></span>`;
                    }
                    if (row.userChecks > 0) {
                        train = `<span data-titlel="${textTrained}" data-iduser="${row.userID}" data-nombre="${row.userName}" class=" btn btn-outline-${colorTrained} border-0 bi bi-person-bounding-box trainUser"></span>`;
                    }
                    let datacol = `
                        <div class="float-right border p-1">
                            <span data-titlel="Editar" data-iduser="${row.userID}" data-nombre="${row.userName}" class="btn btn-outline-custom border-0 bi bi-pen updateUser"></span>
                            ${train}
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
        scrollY: '360px',
        scrollCollapse: true,
        scrollX: true,
        fixedHeader: false,
        language: {
            "url": "../../js/DataTableSpanishShort2.json?v=" + vjs(),
        },

    });
}

tableUsuarios.on('draw.dt', function (e, settings) {
    // $('#modalUsuarios').modal('show')
    $('#tableUsuarios_filter .form-control-sm').attr('placeholder', 'Buscar usuarios')
    $('#RowTableUsers').removeClass('invisible')
    $('#tableUsuarios').removeClass('loader-in');
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
    $('#tableUsuarios_filter input').attr("style", "height: 40px !important");
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

    let modalUser = ls.get(LS_MODAL_USER);

    if (!modalUser) {
        return;
    }

    $('#modales').html(modalUser)

    $('#modalUser .modal-title').html('Nuevo Usuario')
    $('#modalUser').modal('show');
    // $('#formUser .requerido').html('(*)')
    $('#formUser .form-control').attr('autocomplete', 'off')
    $('#formUser #tipo').val('add_usuario')
    $('#formUser #formUserID').mask('00000000000', { reverse: false });
    setTimeout(() => {
        $('#formUser #formUserID').focus();
    }, 500);

    $('#_drUser').daterangepicker({
        singleDatePicker: false,
        opens: 'right',
        drops: 'auto',
        autoUpdateInput: true,
        autoApply: true,
        linkedCalendars: false,
        ranges: {
            'Hoy': [moment(), moment()],
            'Esta semana': [moment().day(1), moment().day(7)],
            'Próxima Semana': [moment().add(1, 'week').day(1), moment().add(1, 'week').day(7)],
            'Este mes': [moment().startOf('month'), moment().endOf('month')],
            'Próximo Mes': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
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
            "monthNames": ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            firstDay: 1,
            alwaysShowCalendars: true,
            applyButtonClasses: "text-white bg-custom",
        },
    });
    $('#_drUser').val(''); // Set the start date
    $('#labelInactivo').removeClass('active');
    $('#labelActivo').addClass('active');
    $('#formUserEstadoAct').prop('checked', true);
    $('#formUserAreaAct').prop('checked', true);
    $('#formUserAreaAct').parents('label').addClass('active');
    $('#formUserEstadoBloc').prop('checked', false);
    $('#formUserAreaBloc').prop('checked', false);

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
$(document).on("click", ".updateUser", function (e) {
    e.preventDefault();
    let data = $('#tableUsuarios').DataTable().row($(this).parents('tr')).data();
    let modalUser = ls.get(LS_MODAL_USER);

    if (!data) {
        return;
    }
    if (!modalUser) {
        return;
    }

    $('#modales').html(modalUser)

    let bloqueado = '';
    if (data.tipoBloqueo == 'Fecha') {
        bloqueado = `<span class="text-danger font-weight-bold">| Bloqueado Por Fechas</span>`
    }
    $('#modalUser .modal-title').html(`
            <div>Editar Usuario</div>
            <div class="text-muted fontq">ID: ${data.userID} ${bloqueado}</div> 
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
    $('#formUser #formUserMotivo').val(data.motivo);

    $('#_drUser').daterangepicker({
        singleDatePicker: false,
        opens: 'right',
        drops: 'auto',
        autoUpdateInput: true,
        autoApply: true,
        linkedCalendars: false,
        ranges: {
            'Hoy': [moment(), moment()],
            'Esta semana': [moment().day(1), moment().day(7)],
            'Próxima Semana': [moment().add(1, 'week').day(1), moment().add(1, 'week').day(7)],
            'Este mes': [moment().startOf('month'), moment().endOf('month')],
            'Próximo Mes': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
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
            "monthNames": ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            firstDay: 1,
            alwaysShowCalendars: true,
            applyButtonClasses: "text-white bg-custom",
        },
    });
    if (data.locked == '1') {
        $('#labelInactivo').addClass('active');
        $('#labelActivo').removeClass('active');
        $('#formUserEstadoBloc').prop('checked', true);
        $('#formUserEstadoAct').prop('checked', false);
    } else {
        $('#labelInactivo').removeClass('active');
        $('#labelActivo').addClass('active');
        $('#formUserEstadoAct').prop('checked', true);
        $('#formUserEstadoBloc').prop('checked', false);
    }
    if (data.userArea == '1') {

        $('#labelAreaInactivo').removeClass('active');
        $('#labelAreaActivo').addClass('active');
        $('#formUserAreaAct').prop('checked', true);
        $('#formUserAreaBloc').prop('checked', false);

    } else {
        $('#labelAreaInactivo').addClass('active');
        $('#labelAreaActivo').removeClass('active');
        $('#formUserAreaBloc').prop('checked', true);
        $('#formUserAreaAct').prop('checked', false);
    }

    if (data.expiredEnd) {
        $('#_drUser').data('daterangepicker').setStartDate(data.expiredStart); // Set the start date
        $('#_drUser').data('daterangepicker').setEndDate(data.expiredEnd); // Set the end date
    } else {
        $('#_drUser').val(''); // Set the start date
    }

    $('#formUser #divid_user').hide()
    setTimeout(() => {
        focusEndText('#formUser #formUserName')
    }, 500);

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
                    $('#tableUsuarios').DataTable().ajax.reload(null, false);
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
$(document).on("click", ".cleanDate", function (e) {
    clearInput('#_drUser')
});
$(document).on("click", ".deleteUser", function (e) {
    let data = $('#tableUsuarios').DataTable().row($(this).parents('tr')).data();

    let modalUser = ls.get(LS_MODAL_USER);

    if (!data) {
        return;
    }
    if (!modalUser) {
        return;
    }

    $('#modales').html(modalUser)

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
    $('.hide').hide()

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
$(document).on("click", ".sendMensaje", function (e) {
    let data = $('#tableUsuarios').DataTable().row($(this).parents('tr')).data();
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
                data: $(this).serialize() + '&userID=' + data.userID,
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
$(document).on("click", ".trainUser", function (e) {

    let modalTrain = ls.get(LS_MODAL_TRAIN);

    if (!modalTrain) {
        return;
    }

    $('#modales').html(modalTrain)

    function restarNumeros(n1, n2) {
        if (n1 && n2) {
            let t = 0
            t = (parseInt(n1) - parseInt(n2));
            return t
        }
        return 0
    }

    function maxTotalSelected(length, max = 10) {
        if (length && length > 0) {
            m = restarNumeros(parseInt(max), parseInt(length))
            return m
        }
        return max
    }

    e.preventDefault();
    let data = $('#tableUsuarios').DataTable().row($(this).parents('tr')).data();
    // alert((data.locked));
    // axios({
    // method: 'post',
    // url: 'modalTrain.html?v=' + $.now(),
    // }).then(function (response) {
    // $('#modales').html(response.data)
    // }).then(function () {

    $('#modalTrain .modal-title').html(`
            <div class="text-secondary font-weight-bold font1">(${data.userID}) ${data.userName}</div> 
            <div class="text-muted font1">Seleccione las fotos que desea enrolar.</div>
            <div class="text-muted font1">Total seleccionado: <span class="totalSelected font-weight-bold">0</span></div>
        `)

    $('#modalTrain').modal('show');
    $('#modalTrain .modal-body').append(`<div class="aguarde d-flex justify-content-center p-3 animate__animated animate__fadeIn">Aguarde por favor..</div>`);

    $('#modalTrain #submitTrain').hide();

    // }).then(function () {

    const id_user = data.userID
    let userID = new FormData()
    userID.append('userID', data.userID)
    $('#userPhoto').val(id_user)

    function getFaces() {
        axios({
            method: 'post',
            url: 'getFaces.php',
            data: userID,
        }).then(function (response) {

            $('.aguarde').remove()

            let data = new Array()
            let data2 = new Array()

            data2.length = 0

            data = response.data.data;
            data2 = response.data.data2;

            $('#modalTrain .modal-body').append(`<div class="form-row d-flex justify-content-start align-items-start mb-2" id="colfotos2">`);
            (data2.length > 0) ? $('#typeEnroll').val('update') : $('#typeEnroll').val('enroll')
            if (data2.length > 0) {
                $('#modalTrain #colfotos2').append(`<div class="col-12 pb-2">Fotos Enroladas (${data2.length})</div>`);

                $.each(data2, function (index, element) {
                    url_foto = `${element.imageData.img}`;
                    let path = '';
                    let apiMobile = document.getElementById('apiMobile').value;
                    if (apiMobile == 'http://localhost:8050') {
                        path = ''
                    } else {
                        path = document.getElementById('apiMobile').value + '/chweb/mobile/hrp/'
                    }
                    divFoto = `<div class="col-4 col-sm-3 col-md-3 col-lg-2 pb-3 d-flex justify-content-center">`
                    divFoto += `<div class="btn-group-toggle animate__animated animate__fadeIn" data-toggle="buttons">`
                    divFoto += `<label for="${element.id_api}" class="disabled active shadow-sm btn btn-success border-0 p-1" style="width:80px;">`
                    divFoto += `<input type="checkbox" readonly value="${element.id_api}"><img loading="lazy" id="${element.id_api}" src="${path}${url_foto}" style="width:80px; height:80px" class="radius img-fluid shadow" title="${element.imageData.humanSize}">`
                    divFoto += `</label>`
                    divFoto += `</div>`
                    divFoto += `</div>`;

                    $('#modalTrain #colfotos2').append(divFoto);
                })
                // maxTotalSelected = restarNumeros(parseInt(max), parseInt(data2.length))
            }

            $('#modalTrain .modal-body').append(`</div>`);

            $('#modalTrain .modal-body').append(`<div class="form-row d-flex justify-content-start align-items-start" id="colfotos">`);

            if (data.length > 0) {
                $('#modalTrain #colfotos').append(`<div class="col-12 pb-2 d-inline-flex justify-content-between"><div>Fotos a Enrolar </div><div><button type="button" class="cleanSelection btn btn-sm btn-link border" data-titlel="Borrar selección"><div class="d-inline-flex"><span class="d-none d-sm-block mr-2">Desmarcar</span> <i class="bi bi-eraser-fill"></i></div></button></div></div>`);
                $.each(data, function (index, element) {
                    if (element.imageData.size < 30000) {
                        url_foto = `${element.imageData.img}`;
                        let path = '';
                        let apiMobile = document.getElementById('apiMobile').value;
                        if (apiMobile == 'http://localhost:8050') {
                            path = ''
                        } else {
                            path = document.getElementById('apiMobile').value + '/chweb/mobile/hrp/'
                        }
                        divFoto = `<div class="col-6 col-sm-4 col-md-4 col-lg-3 pb-2 d-flex justify-content-center">`
                        divFoto += `<div class="btn-group-toggle animate__animated animate__fadeIn selected" data-toggle="buttons">`
                        divFoto += `<label for="${element.id_api}" class="shadow-sm btn btn-outline-success border-0 p-2" style="width:140px;">`
                        divFoto += `<input type="checkbox" name="idPunchEvent[]" value="${element.id_api}"><img loading="lazy" id="${element.id_api}" src="${path}${url_foto}" style="width:140px; height:140px" class="radius img-fluid shadow" title="${element.imageData.humanSize}">`
                        divFoto += `</label>`
                        divFoto += `</div>`
                        divFoto += `</div>`;
                        $('#modalTrain #colfotos').append(divFoto);
                    }
                })
            }

            $(document).on("click", ".selected", function (e) {
                let selected = new Array();
                e.preventDefault()
                $("#modalTrain .modal-body input:checkbox:checked").each(function (e) {
                    (selected.push(parseInt($(this).val())));
                    $('#selectedPhoto').val(selected)

                });

                $('#modalTrain .totalSelected').html("(" + selected.length + ")");
                if (selected.length == 0) {
                    $('#modalTrain .totalSelected').html("(0)");
                    $('#modalTrain #submitTrain').hide();
                } else {
                    $('#modalTrain #submitTrain').show();
                }

                if (selected.length >= maxTotalSelected(data2.length)) {
                    $('#modalTrain .totalSelected').html("(" + selected.length + ")" + " Máximo permitido: " + 10 + "");
                    $('#modalTrain .totalSelected').addClass('animate__animated animate__flash')
                    setTimeout(() => {
                        $('#modalTrain .totalSelected').removeClass('animate__animated animate__flash')
                    }, 500);
                    $("#modalTrain .modal-body input:checkbox").prop('disabled', true)
                } else {
                    $('#modalTrain .totalSelected').html("(" + selected.length + ")");
                    $("#modalTrain .modal-body input:checkbox").prop('disabled', false)
                }
            });

            $('#modalTrain .modal-body').append(`</div>`);

            $(document).on("click", ".cleanSelection", function (e) {
                $("#modalTrain .modal-body input:checkbox").prop('checked', false)
                $('#modalTrain #colfotos label').removeClass('active')
                $('#modalTrain .totalSelected').html("");
                $('#modalTrain #submitTrain').hide();
                $('#selectedPhoto').val('')
            });

        }).catch(function (error) {
            console.log(error)
        })
    }
    getFaces()

    $("#formTrain").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'crud.php',
            data: $(this).serialize() + '&tipo=formTrain',
            beforeSend: function (data) {
                CheckSesion()
                $.notifyClose();
                notify('Aguarde..', 'info', 0, 'right')
                ActiveBTN(true, "#submitTrain", 'Aguarde ' + loading, 'Aceptar')
            },
            success: function (data) {
                if (data.status == "ok") {
                    $.notifyClose();
                    notify('Proceso de enrolamiento finalizado', 'success', 5000, 'right')
                    ActiveBTN(false, "#submitTrain", 'Aguarde ' + loading, 'Aceptar')
                    // $('#modalTrain').modal('h');
                    $('#modalTrain .modal-body').html('')
                    $('#modalTrain .modal-body').append(`<div class="aguarde d-flex justify-content-center p-3 animate__animated animate__fadeIn">Aguarde por favor..</div>`);
                    setTimeout(() => {
                        getFaces();
                    }, 500);
                    $('#tableUsuarios').DataTable().ajax.reload(null, false);
                    $('#modalTrain .totalSelected').html("");
                } else {
                    $.notifyClose();
                    notify(data.Mensaje, 'danger', 5000, 'right')
                    ActiveBTN(false, "#submitTrain", 'Aguarde ' + loading, 'Aceptar')
                }
            },
            error: function () { }
        });
        e.stopImmediatePropagation()
    });

    // }).catch(function (error) {
    // console.log(error)
    // }).then(function () {
    $('#modalTrain').on('hidden.bs.modal', function () {
        $('.selected').off('click')
        setTimeout(() => {
            $('#modales').html(' ');
        }, 100);
    });
    // });
});
