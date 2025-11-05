// $(document).ready(function () {

const homehost = $("#_homehost").val();
const LS_MODALES = homehost + '_mobile_modales';

const loadingTableUser = (selectorTable) => {
    $(selectorTable).addClass('loader-in');
}
const domTableUsers = () => {
    if ($(window).width() < 540) {
        return `<'row lengthFilterTable'<'col-12 d-flex align-items-end m-0 justify-content-between'lf>>
        <'row '<'col-12 table-responsive't>>
        <'fixed-bottom'<''<'d-flex p-0 justify-content-center'p><'pb-2'i>>>`;
    }

    return `<'row lengthFilterTable'
                 <'col-12 d-flex align-items-end m-0 justify-content-between'lf>
             >
             <'row' 
                 <'col-12 table-responsive't>
             >
             <'row d-none d-sm-block'
                 <'col-12 d-flex align-items-center justify-content-between'ip>
             >
             <'row d-block d-sm-none'
                 <'col-12 fixed-bottom h70 d-flex align-items-center justify-content-center'p>
             >
             <'row d-block d-sm-none'
                 <'col-12 d-flex align-items-center justify-content-center'i>
             >`;
}

const columnUser540 = (row) => {

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
    const datacol = `
        <div class="font-weight-bold text-secondary text-uppercase">${row.userName}</div>
        <div class="text-secondary">ID: ${row.userID}</div>
        <div class="d-flex justify-content-end mt-2">
        <span data-titlel="Editar" data-iduser="${row.userID}" data-nombre="${row.userName}" class="btn btn-outline-custom border bi bi-pen updateUser"></span>
        ${train}
        ${del}
        </div>
    `;
    return datacol;
}

const dtUsers = () => {

    if ($.fn.DataTable.isDataTable('#tableUsuarios')) {
        $('#tableUsuarios').DataTable().ajax.reload(null, false);
        return false;
    }

    const tableUsuarios = $('#tableUsuarios').DataTable({
        dom: domTableUsers(),
        ajax: {
            url: "getUsuariosMobile.php",
            type: "POST",
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass('fadeIn align-middle');
        },
        columns: [
            /** Columna ID */
            {
                className: 'align-middle', targets: '', title: '<div class="w80">ID</div>',
                render: function (data, type, row, meta) {
                    if (type !== 'display') {
                        return '';
                    }
                    if ($(window).width() < 540) {
                        return columnUser540(row);
                    }
                    const bloq = 'text-danger font-weight-bold';
                    return `<div class="w80 ${row.bloqueado == true ? bloq : ''}">${row.userID}</div>`;
                },
            },
            /** Columna Nombre */
            {
                className: 'align-middle', targets: '', title: `<div class="w200">Nombre</div>`,
                render: function (data, type, row, meta) {
                    if (type !== 'display') {
                        return '';
                    }
                    if ($(window).width() < 540) return false;
                    const bloq = 'text-danger font-weight-bold';
                    return `<div class="text-truncate w200 ${row.bloqueado == true ? bloq : ''}">${row.userName}</div>`;
                }, visible: visible540(),
            },
            /** Columna cant Fichadas */
            {
                className: 'align-middle text-center', targets: '', title: '<div class="w100">Registros</div>',
                render: function (data, type, row, meta) {
                    if (type !== 'display') {
                        return '';
                    }
                    if ($(window).width() < 540) return false;
                    const bloq = 'text-danger font-weight-bold';
                    return `<div class="w100 ls1 ${row.bloqueado == true ? bloq : ''}">${row.userChecks}</div>`;
                }, visible: visible540(),
            },
            /** Columna Acciones */
            {
                className: 'align-middle w-100', targets: '', title: '',
                render: function (data, type, row, meta) {
                    if (type !== 'display') {
                        return '';
                    }
                    if ($(window).width() < 540) return false;
                    const colorTrained = (row.trained == true) ? 'success' : 'primary'
                    const textTrained = (row.trained == true) ? 'Enrolado' : 'No enrolado'

                    const activar = `<span data-titlel="Sin Reg ID" class=" btn btn-outline-custom disabled border"><i class="bi bi-phone"></i></span>`;
                    let mensaje = `<span data-titlel="Sin Reg ID" class=" btn btn-outline-custom border-0 bi bi-chat-text disabled "></span></span>`;
                    let del = `<span data-titlel="No se puede eliminar" data-iduser="${row.userID}" data-nombre="${row.userName}" class=" btn btn-outline-custom border-0 bi bi-trash disabled"></span>`;
                    let train = `<span data-titlel="No se puede entrenar" data-iduser="${row.userID}" data-nombre="${row.userName}" class=" btn btn-outline-custom border-0 bi bi-person-bounding-box disabled"></span>`;

                    const userRegId = row.userRegId ?? '';
                    const userRegIdLength = userRegId.length || 0;
                    const userChecks = row.userChecks || 0;
                    const userID = row.userID || 0;
                    const userName = row.userName || '';
                    // console.log(userRegIdLength);
                    // contar los caracteres de userRegId
                    if (userRegIdLength > 100) {
                        activar = `<span data-titlel="Configurar dispositivo. Envía Legajo y Empresa" class=" btn btn-outline-custom border-0 sendSettings"><i class="bi bi-phone"></i></span>`
                    }
                    if (userRegIdLength > 100) {
                        mensaje = `<span data-nombre="${userName}" data-regid="${row.userRegId}"  data-titlel="Enviar Mensaje" class=" btn btn-outline-custom border-0 bi bi-chat-text sendMensaje"></span>`
                    }
                    if (userChecks < 1) {
                        del = `<span data-titlel="Eliminar" data-iduser="${userID}" data-nombre="${userName}" class=" btn btn-outline-custom border-0 bi bi-trash deleteUser"></span>`;
                    }
                    if (userChecks > 0) {
                        train = `<span data-titlel="${textTrained}" data-iduser="${userID}" data-nombre="${userName}" class=" btn btn-outline-${colorTrained} border-0 bi bi-person-bounding-box trainUser"></span>`;
                    }
                    const datacol = `
                        <div class="float-right border p-1">
                            <span data-titlel="Editar" data-iduser="${userID}" data-nombre="${userName}" class="btn btn-outline-custom border-0 bi bi-pen updateUser"></span>
                            ${train}
                            ${del}
                        </div>
                        `
                    return datacol;
                }, visible: visible540(),
            },
        ],
        lengthMenu: lengthMenuUsers(),
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        searchDelay: 1000,
        paging: true,
        searching: true,
        info: true,
        ordering: false,
        scrollY: visible540() ? '100%' : '280px',
        scrollCollapse: true,
        scrollX: true,
        fixedHeader: false,
        language: DT_SPANISH_SHORT2

    });

    tableUsuarios.on('draw.dt', function (e, settings) {
        $('#tableUsuarios_filter .form-control-sm').attr('placeholder', 'Buscar usuarios');
        $('#RowTableUsers').removeClass('invisible');
        $('#tableUsuarios').removeClass('loader-in');
    });
    tableUsuarios.on('page.dt', function (e, settings) {
        loadingTableUser('#tableUsuarios');
    });
    tableUsuarios.on('xhr.dt', function (e, settings, json) {
        tableUsuarios.off('xhr.dt'); // Desactivar el evento para que no se llame múltiples veces
    });
    tableUsuarios.on('init.dt', function (e, settings, json) {
        $('#tableUsuarios_filter').prepend('<button data-titlel="Nuevo usuario" class="btn btn-sm btn-custom h40 opa8 px-3" id="addUser"><i class="bi bi-plus-lg"></i></button>');
        $('#tableUsuarios_filter input').removeClass('form-control-sm');
        $('#tableUsuarios_filter input').attr("style", "height: 40px !important");
        select2Simple('#tableUsuarios_length select', '', false, false);
    });
}

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

    let modalUser = ls.get(LS_MODALES);

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
    let modalUser = ls.get(LS_MODALES);

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

    let modalUser = ls.get(LS_MODALES);

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

    let modalTrain = ls.get(LS_MODALES);

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
    $('#modalTrain .modal-body').append(`<div class="aguarde d-flex justify-content-center p-3 fadeIn">Aguarde por favor..</div>`);

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
                    divFoto += `<div class="btn-group-toggle fadeIn" data-toggle="buttons">`
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
                        divFoto += `<div class="btn-group-toggle fadeIn selected" data-toggle="buttons">`
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
                    $('#modalTrain .totalSelected').addClass('flash')
                    setTimeout(() => {
                        $('#modalTrain .totalSelected').removeClass('flash')
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
const importUserBtn = document.getElementById('importUser');
const modalImport = document.getElementById('modalImport');
importUserBtn.addEventListener('click', async function () {
    importUserBtn.disabled = true;
    importUserBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Aguarde...';
    // const importUserModal = new bootstrap.Modal(document.getElementById('importUserModal'));
    const importHtml = await axios.get('import.php');
    modalImport.innerHTML = await importHtml.data;

    await $('#importUserModal').modal('show');
    // on show
    procesarImportar();
    importUserBtn.disabled = false;
    importUserBtn.innerHTML = 'Importar usuarios';
    // on hide
    $('#importUserModal').on('hidden.bs.modal', function () {
        modalImport.innerHTML = '';
    });

});

const procesarImportar = async () => {
    // Botón para abrir el modal
    const importUserBtn = document.getElementById('importUser');
    const submitImportBtn = document.getElementById('submitImport');
    const userFileInput = document.getElementById('userFile');
    const fileError = document.getElementById('fileError');
    const importUserForm = document.getElementById('importUserForm');

    // Validación al cambiar el archivo
    userFileInput.addEventListener('change', function () {
        validateFile();
    });

    // Validación al hacer clic en importar
    submitImportBtn.addEventListener('click', function () {
        if (validateFile()) {
            // Crear FormData y añadir el archivo
            const formData = new FormData();
            formData.append('userFile', userFileInput.files[0]);

            // Incluir token CSRF
            const csrfToken = document.getElementById('csrf_token').value;
            formData.append('csrf_token', csrfToken);

            // Añadir timestamp para evitar caché
            formData.append('_', new Date().getTime());

            // Mostrar indicador de carga
            submitImportBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
            submitImportBtn.disabled = true;

            // Enviar petición AJAX con Axios
            axios.post('import_script.php', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then(function (response) {
                    const data = response.data;

                    const detalleErrores = (data) => {
                        // Si no hay errores, retornamos una cadena vacía
                        if (!data.errors || data.errors.length === 0) {
                            return '';
                        }

                        // Construimos el contenido del HTML
                        const erroresHTML = data.errors.map(error => `
                        <li class="list-group-item">
                            <ul class="mb-0 pl-2">
                                ${error.errors.map(errorMsg => `<li class="small">(Fila ${error.row}) ${errorMsg}</li>`).join('')}
                            </ul>
                        </li>
                    `).join('');

                        // Retornamos el HTML completo usando una plantilla literal
                        return `
                        <a class="btn btn-link border bg-light font08 mt-2" href="#" data-toggle="collapse" data-target="#collapseErrors" aria-expanded="false" aria-controls="collapseErrors" style="width:200px;">
                            <i class="bi bi-chevron-down"></i> Detalle de errores
                        </a>
                        <div id="collapseErrors" class="collapse py-2">
                            <ul class="list-group list-group-flush border">
                                ${erroresHTML}
                            </ul>
                        </div>
                    `;
                    };
                    const detalleCorrectos = (data) => {
                        let resultHTML = '';
                        if (data.data.valid_rows > 0) {
                            // Construir la tabla completa
                            const classfilaLetras = 'bg-secondary text-white text-monospace font09 sticky-header';
                            resultHTML = `
                            <p class="my-2 font09">Filas procesadas correctamente:</p>
                            <div class="table-responsive overflow-auto">
                                <table class="table table-sm table-bordered border">
                                    <thead class="sticky-header">
                                        <tr class="text-center ">
                                            <td class="${classfilaLetras}">
                                                <div class="icon-index"><i class="bi bi-chevron-right"></i></div>
                                            </td>
                                            <td class="${classfilaLetras}">A</td>
                                            <td class="${classfilaLetras}">B</td>
                                            <td class="${classfilaLetras}">C</td>
                                            <td class="${classfilaLetras}">D</td>
                                            <td class="${classfilaLetras}">E</td>
                                            <td class="${classfilaLetras}">F</td>
                                        </tr>
                                        <tr class="table-primary">
                                            <td class="bg-secondary text-white text-center border-0">1</td>
                                            <th class="sticky-header">ID</th>
                                            <th class="sticky-header">Nombre y Apellido</th>
                                            <th class="sticky-header">Estado</th>
                                            <th class="sticky-header">Visualizar zona</th>
                                            <th class="sticky-header">Bloqueo Fecha inicio</th>
                                            <th class="sticky-header">Bloqueo Fecha Fin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${data.data.processed_rows.map(row => `
                                            <tr>
                                                <td class="text-monospace text-center bg-secondary text-white font09">${row.row}</td>
                                                <td>${row.id}</td>
                                                <td>${row.nombre_apellido}</td>
                                                <td><span class="${row.estado === 'activo' ? 'text-success' : 'text-danger'}">${row.estado}</span></td>
                                                <td><span class="${row.visualizar_zona === 'activo' ? 'text-success' : 'text-danger'}">${row.visualizar_zona}</span></td>
                                                <td>${row.bloqueo_fecha_inicio || '—'}</td>
                                                <td>${row.bloqueo_fecha_fin || '—'}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        `;
                        }
                        return resultHTML;
                    };

                    if (data.success) {
                        // Crear una presentación más compacta y visual de los resultados
                        let resultHTML = `
                        <div class="p-3 border bg-white rounded">
                            <div class="d-flex flex-column">
                                <p class="mb-1 font1">Filas procesadas: <span class="text-monospace">${data.data.total_rows}</span> </p>
                                <div class="d-flex flex-column small-info">
                                    <span> - ${data.data.valid_rows} correctas</span>
                                    <span> - ${data.data.error_rows} con errores</span>
                                </div>
                                ${detalleErrores(data)}
                            </div>`;

                        // Mostrar tabla con las filas procesadas si hay filas válidas
                        resultHTML += detalleCorrectos(data);
                        // Añadir botón para cerrar el modal
                        if (data.data.valid_rows > 0) {
                            resultHTML += `
                        <div class="mt-3 text-center">
                            <button type="button" class="submit-import" id="confirm-import" data-bs-dismiss="modal" data-flag="${data.flag}">
                                <i class="bi bi-check-circle pr-1"></i>Confirmar
                                </button>
                            </div>
                            <div class="mt-3 text-center respuesta-import"></div>
                        </div>`;

                            // Limpiar y mostrar resultados

                            importUserForm.innerHTML = resultHTML;
                            confirmImport();
                        } else {
                            importUserForm.innerHTML = resultHTML;
                        }

                    } else {
                        // Mostrar error
                        fileError.textContent = data.message;
                        fileError.classList.remove('d-none');

                        // Restablecer botón
                        submitImportBtn.innerHTML = 'Importar';
                        submitImportBtn.disabled = false;
                    }
                })
                .catch(function (error) {
                    console.error('Error:', error);

                    // Mensaje de error más detallado si está disponible
                    let errorMsg = 'Error al procesar la solicitud. Inténtelo de nuevo.';

                    if (error.response) {
                        // El servidor respondió con un código de estado diferente de 2xx
                        console.log('Error data:', error.response.data);
                        console.log('Error status:', error.response.status);

                        if (error.response.data && error.response.data.message) {
                            errorMsg = error.response.data.message;
                        } else if (error.response.status === 500) {
                            errorMsg = 'Error interno del servidor. Contacte al administrador.';
                        }
                    } else if (error.request) {
                        // La petición fue hecha pero no se recibió respuesta
                        errorMsg = 'No se recibió respuesta del servidor. Verifique su conexión.';
                    }

                    fileError.textContent = errorMsg;
                    fileError.classList.remove('d-none');

                    // Restablecer botón
                    submitImportBtn.innerHTML = 'Importar';
                    submitImportBtn.disabled = false;
                });
        }
    });

    const confirmImport = async () => {
        const confirmImportBtn = document.getElementById('confirm-import');
        const respuestImport = document.querySelector('.respuesta-import');
        confirmImportBtn.addEventListener('click', function () {
            const flag = this.getAttribute('data-flag');
            confirmImportBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> importando...';
            confirmImportBtn.disabled = true;
            respuestImport.innerHTML = '';
            respuestImport.classList.remove('alert', 'alert-success', 'alert-danger', 'fadeInUp');
            if (!flag) {
                return;
            }

            const formData = new FormData();
            formData.append('tipo', 'add_usuarios');
            formData.append('flag', flag);
            // notify('Aguarde..', 'info', 0, 'right');
            // Enviar petición AJAX con Axios
            axios.post('crud.php', formData)
                .then(function (response) {
                    const data = response.data;
                    const MESSAGE = data.MESSAGE ?? 'Error';
                    const RESPONSE_DATA = data.RESPONSE_DATA ?? null;


                    if (MESSAGE == 'OK') {
                        const html = `
                        <div>Usuarios importados correctamente</div>
                        <div>Creados: ${RESPONSE_DATA.totalInsert ?? 0}</div>
                        <div>Actualizados: ${RESPONSE_DATA.totalUpdate ?? 0}</div>
                    `
                        respuestImport.innerHTML = html;
                        respuestImport.classList.add('alert', 'alert-success', 'fadeInUp');
                        // notify(html, 'success', 5000, 'right')
                        // importUserModal.hide();
                        $('#tableUsuarios').DataTable().ajax.reload();
                    } else {
                        respuestImport.innerHTML = '<div>Error al importar usuarios.</div>';
                        respuestImport.classList.add('alert', 'alert-danger', 'fadeInUp');
                        // notify('Error al importar usuarios.', 'error', 5000, 'right');
                    }
                    confirmImportBtn.innerHTML = '<i class="bi bi-check-circle pr-1"></i>Importar';
                    confirmImportBtn.disabled = false;
                })
                .catch(function (error) {
                    console.error('Error:', error);
                    confirmImportBtn.innerHTML = '<i class="bi bi-check-circle pr-1"></i>Importar';
                    confirmImportBtn.disabled = false;
                });

        });
    };

    function validateFile() {
        fileError.classList.add('d-none');

        // Verificar si se ha seleccionado un archivo
        if (!userFileInput.files || userFileInput.files.length === 0) {
            fileError.textContent = 'Por favor, seleccione un archivo.';
            fileError.classList.remove('d-none');
            return false;
        }

        const file = userFileInput.files[0];
        const fileName = file.name;
        const fileExt = fileName.split('.').pop().toLowerCase();

        // Verificar la extensión del archivo
        if (fileExt !== 'xls' && fileExt !== 'xlsx') {
            fileError.textContent = 'Solo se permiten archivos Excel (.xls o .xlsx).';
            fileError.classList.remove('d-none');
            return false;
        }

        // Verificar el tamaño del archivo (2MB máximo = 2 * 1024 * 1024 bytes)
        const maxSizeBytes = 2 * 1024 * 1024;
        if (file.size > maxSizeBytes) {
            fileError.textContent = `El archivo es demasiado grande. El tamaño máximo permitido es 2MB.`;
            fileError.classList.remove('d-none');
            return false;
        }

        return true;
    }
}

