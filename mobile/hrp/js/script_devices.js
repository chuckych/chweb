
const loadingTableDevices = (selectorTable) => {
    $(selectorTable).addClass('loader-in');
}
const domTableDevices = () => {
    if ($(window).width() < 540) {
        return `<'row lengthFilterTable'<'col-12 d-flex align-items-end m-0 justify-content-between'lf>><'row '<'col-12 table-responsive't>><'fixed-bottom'<''<'d-flex p-0 justify-content-center'p><'pb-2'i>>>`;
    }
    return `<'row lengthFilterTable'<'col-12 d-flex align-items-end m-0 justify-content-between'lf>><'row '<'col-12 table-responsive't>><'row d-none d-sm-block'<'col-12 d-flex align-items-center justify-content-between'ip>><'row d-block d-sm-none'<'col-12 fixed-bottom h70 d-flex align-items-center justify-content-center'p>><'row d-block d-sm-none'<'col-12 d-flex align-items-center justify-content-center'i>>`;
}
const columnDevive540 = (row) => {
    let del = `<span class="ml-1 btn btn-outline-custom border bi bi-trash delDevice"></span>`
    if (row.totalChecks > 1) {
        del = `<span class="ml-1 btn btn-outline-custom border bi bi-trash disabled"></span>`
    }
    let deviceEvent = (row.deviceEvent == '0') ? 'Sin Evento' : `Evento: <span class="ls1">${row.deviceEvent}</span>`

    let text = row.appVersion;
    let myArray = text.split(" - ");
    let appVersion = myArray[0] ?? '';
    let appVersion2 = myArray[1] ?? '';
    let setDevice = ''
    let setDeviceTitle = ''

    if (row.regid) {
        setDevice = "btn-outline-secondary setDevice"
        setDeviceTitle = 'data-titlel="Configurar dispositivo"'
    } else {
        setDevice = "btn-outline-secondary disabled"
        setDeviceTitle = 'data-titlel="Falta Regid"'
    }

    return `
        <div class="d-flex justify-content-between">
            <div class="text-uppercase font-weight-bold text-secondary">${row.deviceName}</div>
            <div class="text-secondary"><small>${row.totalChecks}</small></div>
        </div>
        <div class="text-secondary"><small>${appVersion}</small></div>
        <div class="d-flex justify-content-end w-100">
            <div class="mr-1 btn btn-outline-custom border updDevice"><span data-titlel="Editar Dispositivo" class="bi bi-pen"></span></div>
            <div class="btn border ${setDevice}"><span ${setDeviceTitle} class="bi bi-gear-fill"></span></div>
            ${del}
        </div>
        `;
}
const dtDevices = () => {

    if ($.fn.DataTable.isDataTable('#tableDevices')) {
        $('#tableDevices').DataTable().ajax.reload(null, false);
        return false;
    }

    const tableDevices = $('#tableDevices').DataTable({
        dom: domTableDevices(),
        ajax: {
            url: "data/devices",
            type: "GET",
            "data": function (data) { },
            error: function () { },
        },
        // data: (ls.get(LS_MODAL_DEVICE_DATA)),
        createdRow: function (row, data, dataIndex) {
            $(row).addClass('align-middle fadeIn');
        },
        columns: [
            /** Columna Nombre */
            {
                className: '', targets: '', title: `<div class="w180">Dispositivo</div>`,
                render: function (data, type, row, meta) {
                    if (type !== 'display') {
                        return '';
                    }
                    if ($(window).width() < 540) {
                        return columnDevive540(row);
                    }

                    return `<div class="d-flex flex-column">
                        <div class="text-truncate w180">${row.deviceName}</div><span class="text-secondary fontp">Evento: ${row.deviceEvent}</span>
                    </div>`;
                },
            },
            /** Columna cant TotalChecks */
            {
                className: 'text-center', targets: '', title: '<div class="w60">Registros</div>',
                render: function (data, type, row, meta) {
                    if (type !== 'display') {
                        return '';
                    }
                    if ($(window).width() < 540) return false;
                    return `<div class="ls1 w60">${row.totalChecks}</div>`
                }, visible: visible540(),
            },
            /** Columna appVersion */
            {
                className: 'text-center', targets: '', title: '<div class="w100">Versión App</div>',
                render: function (data, type, row, meta) {
                    if (type !== 'display') {
                        return '';
                    }
                    if ($(window).width() < 540) return false;
                    let text = row.appVersion;
                    let myArray = text.split(" - ");
                    let appVersion = myArray[0] ?? '';
                    let appVersion2 = myArray[1] ?? '';
                    let datacol = `<div data-titler="${row.lastUpdate}">-</div>`
                    if (myArray[0]) {
                        datacol = `<div class="ls1 w100" data-titler="${row.lastUpdate}">${appVersion}</div>`
                    }
                    return datacol;
                }, visible: visible540(),
            },
            /** Columna Acciones */
            {
                className: 'w-100', targets: '', title: '',
                render: function (data, type, row, meta) {
                    if (type !== 'display') {
                        return '';
                    }
                    if ($(window).width() < 540) return false;
                    let setDevice = ''
                    let setDeviceTitle = ''
                    let del = `<span data-titlel="Eliminar" class="ml-1 btn btn-outline-custom border-0 bi bi-trash delDevice"></span>`
                    if (row.totalChecks > 1) {
                        del = `<span data-titlel="No se puede eliminar" class="ml-1 btn btn-outline-custom border-0 bi bi-trash disabled"></span>`
                    }
                    if (row.regid) {
                        setDevice = "btn-outline-secondary setDevice"
                        setDeviceTitle = 'data-titlel="Configurar dispositivo"'
                    } else {
                        setDevice = "btn-outline-secondary disabled"
                        setDeviceTitle = 'data-titlel="Falta Regid"'
                    }
                    let datacol = `
                    <div class="float-right border p-1 bg-white">
                        <div class="mr-1 btn btn-outline-custom border-0 updDevice"><span data-titlel="Editar Dispositivo" class="bi bi-pen"></span></div>
                        <div class="mr-1 btn border-0 ${setDevice}"><span ${setDeviceTitle} class="bi bi-gear-fill"></span></div>
                        ${del}
                    </div>
                    `
                    return datacol;
                }, visible: visible540(),
            },
        ],
        lengthMenu: lengthMenuUsers(),
        bProcessing: false,
        serverSide: false,
        deferRender: true,
        searchDelay: 250,
        paging: true,
        searching: true,
        info: true,
        ordering: true,
        order: [[1, 'desc']],
        scrollY: visible540() ? '100%' : '350px',
        scrollCollapse: true,
        scrollX: true,
        language: DT_SPANISH_SHORT2
    });
    tableDevices.on('init.dt', function (e, settings, json) {
        $('#tableDevices_filter').prepend('<button data-titlel="Nuevo Dispositivo" class="btn btn-sm btn-custom h40 opa8 px-3" id="addDevice"><i class="bi bi-plus-lg"></i></button>');
        $('#tableDevices_filter input').removeClass('form-control-sm');
        $('#tableDevices_filter input').attr("style", "height: 40px !important");
        select2Simple('#tableDevices_length select', '', false, false);
    });
    tableDevices.on('draw.dt', function (e, settings) {
        // $('#modalUsuarios').modal('show')
        $('#tableDevices_filter .form-control-sm').attr('placeholder', 'Buscar Dispositivos')
        $('#RowTableDevices').removeClass('invisible')
        $('#tableDevices').removeClass('loader-in');
    });
    tableDevices.on('xhr.dt', function (e, settings, json) {
        tableDevices.off('xhr.dt');
    });

    $(document).on("click", ".updDevice", function (e) {
        const data = tableDevices.row($(this).parents('tr')).data();
        updDevice(data);
    });
    $(document).on("click", ".setDevice", function (e) {
        const data = tableDevices.row($(this).parents('tr')).data();
        setDevice(data);
    });
    $(document).on("click", ".updDeviceTable", function (e) {
        const data = $('#table-mobile').DataTable().row($(this).parents('tr')).data();
        updDevice(data);
    });

    $(document).on("click", ".delDevice", function (e) {
        const data = tableDevices.row($(this).parents('tr')).data();
        const modalDevice = ls.get(LS_MODALES);

        if (!modalDevice) {
            return;
        }

        $('#modales').html(modalDevice);

        $('#modalDevice .modal-title').html('¿Eliminar Dispositivo ' + data.deviceName + '?');
        $('#modalDevice .modal-title').addClass('text-danger');
        $('#formDevicePhoneID').val(data.phoneID);
        $('#modalDevice').modal('show');
        $('#formDevice #formDeviceTipo').val('del_device');
        $('#formDevice #formDevicePhoneID').val(data.phoneID).attr('hidden', 'hidden');
        $('#formDevice #formDeviceNombre').val(data.deviceName).attr('disabled', 'disabled');
        $('#formDevice #formDeviceEvento').val(data.deviceEvent).attr('disabled', 'disabled');

        $("#formDevice").bind("submit", function (e) {
            e.preventDefault();
            let tipoStatus = '';
            switch ($('#formDevice #formDeviceTipo').val()) {
                case 'del_device':
                    tipoStatus = 'Eliminado';
                    break;
                case 'upd_device':
                    tipoStatus = 'Actualizado';
                    break;
                case 'add_device':
                    tipoStatus = 'Creado';
                    break;
                default:
                    tipoStatus = '';
                    break;
            }
            $.ajax({
                type: $(this).attr("method"),
                url: 'crud.php',
                data: $(this).serialize() + '&tipo=' + $('#formDevice #formDeviceTipo').val(),
                // dataType: "json",
                beforeSend: function (data) {
                    // CheckSesion();
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                    ActiveBTN(true, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        let deviceName = data.Mensaje.deviceName
                        notify('Dispositivo ' + deviceName + '<br />' + tipoStatus + ' ' + 'correctamente.', 'success', 5000, 'right')
                        $('#table-mobile').DataTable().ajax.reload(null, false);
                        $('#tableDevices').DataTable().ajax.reload();
                        ActiveBTN(false, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
                        $('#modalDevice').modal('hide');
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                        ActiveBTN(false, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
                    }
                },
                error: function () { }
            });
        });
        $('#modalDevice').on('hidden.bs.modal', function () {
            $('#modales').html('');
        });
    });
    $(document).on("click", "#addDevice", function (e) {
        const modalDevice = ls.get(LS_MODALES);

        if (!modalDevice) {
            return;
        }

        $('#modales').html(modalDevice);
        $('#modalDevice .modal-title').html('Nuevo Dispositivo');
        $('#modalDevice').modal('show');
        $('#formDevice .requerido').html('(*)')
        $('#formDevice .form-control').attr('autocomplete', 'off')
        $('#formDevice #formDeviceEvento').mask('0000', { reverse: false });
        $('#formDevice #formDevicePhoneID').mask('00000000000000000000', { reverse: false });
        $('#formDevice #formDeviceTipo').val('add_device')

        setTimeout(() => {
            $('#formDevice #formDevicePhoneID').focus();
            $('#formDevice #formDevicePhoneID').removeAttr('readonly')
        }, 500);

        $("#formDevice").bind("submit", function (e) {
            e.preventDefault();
            const mapTipoStatus = {
                'del_device': 'eliminado',
                'upd_device': 'actualizado',
                'add_device': 'Creado'
            }
            const valueTipo = $('#formDevice #formDeviceTipo').val() || '';

            $.ajax({
                type: $(this).attr("method"),
                url: 'crud.php',
                data: $(this).serialize() + '&tipo=' + valueTipo,
                // dataType: "json",
                beforeSend: function (data) {
                    // CheckSesion();
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                    ActiveBTN(true, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        const deviceName = data.Mensaje.deviceName
                        notify('Dispositivo ' + deviceName + '<br />' + (mapTipoStatus[valueTipo] ?? '') + ' ' + 'correctamente.', 'success', 5000, 'right');
                        dtDevices();
                        $('#table-mobile').DataTable().ajax.reload(null, false);
                        ActiveBTN(false, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
                        $('#modalDevice').modal('hide');
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                        ActiveBTN(false, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
                    }
                },
                error: function () { }
            });
        });
        $('#modalDevice').on('hidden.bs.modal', function () {
            $('#modales').html('');
        });
    });

};

const setDevice = (data) => {

    const modalSettings = ls.get(LS_MODALES);

    if (!modalSettings) {
        return;
    }

    $('#modales').html(modalSettings);

    $('#modalSetting .modal-title').html('Configurar Dispositivo <br><span class="fontq">' + data.deviceName + '</span>')
    $('#modalSetting').modal('show');

    $("#deviceSetting").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: 'crud.php',
            data: {
                tipo: 'send_DeviceSet',
                deviceID: data.deviceID,
                deviceName: data.deviceName,
                devicePhoneID: data.phoneID,
                deviceIDCompany: data.idCompany,
                deviceRegid: data.regid,
                deviceAppVersion: data.appVersion,
                deviceUser: $('#deviceSettingUsuario').val(),
                deviceTMEF: $('#deviceSettingTMEF').val(),
                deviceRememberUser: $('input[name=deviceSettingRememberUser]:checked').val(),
                deviceInitialize: $('input[name=deviceInitialize]').is(':checked') ? 1 : 0
            },
            beforeSend: function (data) {
                // CheckSesion();
                $.notifyClose();
                notify('Aguarde..', 'info', 0, 'right');
                ActiveBTN(true, "#submitSetting", 'Aguarde ' + loading, 'Aceptar');
            },
            success: function (data) {
                if (data.status == "ok") {
                    $.notifyClose();
                    notify('Dispositivo configurado correctamente.', 'success', 5000, 'right');
                    ActiveBTN(false, "#submitSetting", 'Aguarde ' + loading, 'Aceptar');
                    $('#modalSetting').modal('hide');
                } else {
                    $.notifyClose();
                    notify(data.Mensaje, 'danger', 5000, 'right');
                    ActiveBTN(false, "#submitSetting", 'Aguarde ' + loading, 'Aceptar');
                }
            },
            error: function () { }
        });
        e.stopImmediatePropagation();
    });
    $('#modalSetting').on('hidden.bs.modal', function () {
        $('#modales').html(' ');
    });

}
const updDevice = (data) => {

    const modalDevice = ls.get(LS_MODALES);

    if (!modalDevice) {
        return;
    }

    $('#modales').html(modalDevice);

    $('#modalDevice .modal-title').html('Editar Dispositivo <br><span class="fontq">' + data.deviceName + '</span>');
    $('#formDevicePhoneID').val(data.phoneID);
    $('#modalDevice').modal('show');
    $('#formDevice .requerido').html('(*)');
    $('#formDevice .form-control').attr('autocomplete', 'off');
    $('#formDevice #formDeviceEvento').mask('0000', { reverse: false });
    $('#formDevice #formDeviceTipo').val('upd_device');
    $('#formDevice #formDevicePhoneID').val(data.phoneID);
    $('#formDevice #formDeviceNombre').val(data.deviceName).select();
    $('#formDevice #formDeviceEvento').val(data.deviceEvent ?? '0');

    $("#formDevice").bind("submit", function (e) {
        e.preventDefault();

        const mapTipoStatus = {
            'del_device': 'eliminado',
            'upd_device': 'actualizado',
            'add_device': 'Creado'
        }

        const valueTipo = $('#formDevice #formDeviceTipo').val() || '';

        $.ajax({
            type: $(this).attr("method"),
            url: 'crud.php',
            data: $(this).serialize() + '&tipo=' + valueTipo,
            // dataType: "json",
            beforeSend: function (data) {
                // CheckSesion();
                $.notifyClose();
                notify('Aguarde..', 'info', 0, 'right')
                ActiveBTN(true, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
            },
            success: function (data) {
                if (data.status == "ok") {
                    $.notifyClose();
                    const deviceName = data.Mensaje.deviceName
                    notify('Dispositivo ' + deviceName + '<br />' + (mapTipoStatus[valueTipo] ?? '') + ' ' + 'correctamente.', 'success', 5000, 'right')
                    dtDevices();
                    $('#table-mobile').DataTable().ajax.reload(null, false);
                    ActiveBTN(false, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
                    $('#modalDevice').modal('hide');
                } else {
                    $.notifyClose();
                    notify(data.Mensaje, 'danger', 5000, 'right')
                    ActiveBTN(false, "#submitDevice", 'Aguarde ' + loading, 'Aceptar')
                }
            },
            error: function () { }
        });
    });
    $('#modalDevice').on('hidden.bs.modal', function () {
        $('#modales').html('');
    });
}