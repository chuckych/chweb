// $(document).ready(function () {
const loadingTableDevices = (selectortable) => {
    $(selectortable + ' td div').addClass('bg-light text-light border-0')
    // $(selectortable + ' td div').css('height', '')
    $(selectortable + ' td img').addClass('invisible')
    $(selectortable + ' td i').addClass('invisible')
    $(selectortable + ' td span').addClass('invisible')
}
tableDevices = $('#tableDevices').DataTable({
    "initComplete": function (settings, json) {
        // $('#tableDevices_filter').prepend('<button data-titlel="Nuevo Dispositivo" class="btn btn-sm btn-custom h35 px-3 addDevice"><i class="bi bi-plus-lg"></i></button>')
    },
    dom: "<'row'<'col-12 d-flex align-items-end m-0 justify-content-between'lf>>" +
        "<'row px-3'<'col-12 border shadow-sm table-responsive't>>" +
        "<'row d-none d-sm-block'<'col-12 d-flex align-items-center justify-content-between'ip>>"+
        "<'row d-block d-sm-none'<'col-12 d-flex align-items-center justify-content-center'p>>"+
        "<'row d-block d-sm-none'<'col-12 d-flex align-items-center justify-content-center'i>>",
    ajax: {
        url: "getDevicesMobile.php",
        type: "POST",
        "data": function (data) { },
        error: function () { },
    },
    createdRow: function (row, data, dataIndex) {
        $(row).addClass('animate__animated animate__fadeIn align-middle');
    },
    columns: [
        {
            className: 'align-middle', targets: '', title: 'Nombre',
            "render": function (data, type, row, meta) {
                let datacol = `<div class="">${row.deviceName}</div>`
                return datacol;
            },
        },
        {
            className: 'align-middle', targets: '', title: 'Evento',
            "render": function (data, type, row, meta) {
                let deviceEvent = (row.deviceEvent == '0') ? '-' : row.deviceEvent
                let datacol = `<div class="ls1">${deviceEvent}</div>`
                return datacol;
            },
        }, 
        {
            className: 'align-middle', targets: '', title: 'Fichadas',
            "render": function (data, type, row, meta) {
                let datacol = `<div class="ls1">${row.totalChecks}</div>`
                return datacol;
            },
        },
        {
            className: 'align-middle w-100', targets: '', title: '',
            "render": function (data, type, row, meta) {
                let datacol = `
                <div class="d-flex justify-content-end">
                    <button data-titlet="Editar Dispositivo" class="mr-1 btn btn-outline-custom btn-sm border bi bi-pen updDevice"></button>
                    <button data-titlel="Eliminar" class="btn btn-outline-custom btn-sm border bi bi-trash delDevice"></button>
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
tableDevices.on('draw.dt', function (e, settings) {
    // $('#modalUsuarios').modal('show')
    $('#tableDevices_filter .form-control-sm').attr('placeholder', 'Buscar Dispositivos')
    $('#RowTableDevices').removeClass('invisible')
});
tableDevices.on('page.dt', function (e, settings) {
    loadingTableDevices('#tableDevices')
});
tableDevices.on('xhr', function (e, settings, json) {
    tableDevices.off('xhr');
});
$(document).on("click", ".addDevice", function (e) {
    let data = $('#table-mobile').DataTable().row($(this).parents('tr')).data();
    axios({
        method: 'post',
        url: 'modalDevice.html?v=' + $.now(),
    }).then(function (response) {
        $('#modales').html(response.data)
    }).then(function () {
        $('#modalDevice .modal-title').html('Nuevo Dispositivo')
        $('#formDevicePhoneID').val(data.phoneid)
        $('#modalDevice').modal('show');
        $('#formDevice .requerido').html('(*)')
        $('#formDevice .form-control').attr('autocomplete', 'off')
        $('#formDevice #formDeviceEvento').mask('0000', { reverse: false });
        $('#formDevice #formDeviceTipo').val('c_device')

        setTimeout(() => {
            $('#formDevice #formDeviceNombre').focus();
        }, 500);

    }).then(function () {

    }).catch(function (error) {
        alert(error)
    }).then(function () {
        $("#formDevice").bind("submit", function (e) {
            e.preventDefault();
            let tipoStatus ='';
            switch ($('#formDevice #formDeviceTipo').val()) {
                case 'd_device':
                    tipoStatus = 'eliminado';
                    break;
                case 'u_device':
                    tipoStatus = 'actualizado';
                    break;
                case 'c_device':
                    tipoStatus = 'creado';
                    break;            
                default:
                    tipoStatus = '';
                    break;
            }
            $.ajax({
                type: $(this).attr("method"),
                url: 'crud.php',
                data: $(this).serialize() + '&tipo='+ $('#formDevice #formDeviceTipo').val(),
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
                        let deviceName = data.Mensaje.deviceName
                        notify('Dispositivo ' + deviceName + ' '+tipoStatus+' '+ 'correctamente.', 'success', 5000, 'right')
                        // $('#tableUsuarios').DataTable().ajax.reload();
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
            $('#modales').html(' ');
        });
    });

});
$(document).on("click", ".updDevice", function (e) {
    // get data row datatable
    let data = tableDevices.row($(this).parents('tr')).data();
    axios({
        method: 'post',
        url: 'modalDevice.html?v=' + $.now(),
    }).then(function (response) {
        $('#modales').html(response.data)
    }).then(function () {
        $('#modalDevice .modal-title').html('Editar Dispositivo '+data.deviceName)
        $('#formDevicePhoneID').val(data.phoneID)
        $('#modalDevice').modal('show');
        $('#formDevice .requerido').html('(*)')
        $('#formDevice .form-control').attr('autocomplete', 'off')
        $('#formDevice #formDeviceEvento').mask('0000', { reverse: false });
        $('#formDevice #formDeviceTipo').val('u_device')
        $('#formDevice #formDevicePhoneID').val(data.phoneID)
        $('#formDevice #formDeviceNombre').val(data.deviceName)
        $('#formDevice #formDeviceEvento').val(data.deviceEvent)
        setTimeout(() => {
            focusEndText('#formDevice #formDeviceNombre')
        }, 500);

    }).then(function () {

    }).catch(function (error) {
        alert(error)
    }).then(function () {
        $("#formDevice").bind("submit", function (e) {
            e.preventDefault();
            let tipoStatus ='';
            switch ($('#formDevice #formDeviceTipo').val()) {
                case 'd_device':
                    tipoStatus = 'eliminado';
                    break;
                case 'u_device':
                    tipoStatus = 'actualizado';
                    break;
                case 'c_device':
                    tipoStatus = 'creado';
                    break;            
                default:
                    tipoStatus = '';
                    break;
            }
            $.ajax({
                type: $(this).attr("method"),
                url: 'crud.php',
                data: $(this).serialize() + '&tipo='+ $('#formDevice #formDeviceTipo').val(),
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
                        let deviceName = data.Mensaje.deviceName
                        notify('Dispositivo ' + deviceName + ' '+tipoStatus+' '+ 'correctamente.', 'success', 5000, 'right')
                        // $('#tableUsuarios').DataTable().ajax.reload();
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
            $('#modales').html(' ');
        });
    });

});
$(document).on("click", ".delDevice", function (e) {
    let data = tableDevices.row($(this).parents('tr')).data();
    console.log(data);
    axios({
        method: 'post',
        url: 'modalDevice.html?v=' + $.now(),
    }).then(function (response) {
        $('#modales').html(response.data)
    }).then(function () {
        $('#modalDevice .modal-title').html('¿Eliminar Dispositivo '+data.deviceName+'?')
        $('#modalDevice .modal-title').addClass('text-danger font-weight-bold')
        $('#formDevicePhoneID').val(data.phoneID)
        $('#modalDevice').modal('show');
        $('#formDevice #formDeviceTipo').val('d_device');
        $('#formDevice #formDevicePhoneID').val(data.phoneID).attr('hidden', 'hidden')
        $('#formDevice #formDeviceNombre').val(data.deviceName).attr('disabled', 'disabled')
        $('#formDevice #formDeviceEvento').val(data.deviceEvent).attr('disabled', 'disabled')
        // $('#formDevice .modal-boy').html('')
    }).then(function () {

    }).catch(function (error) {
        alert(error)
    }).then(function () {
        $("#formDevice").bind("submit", function (e) {
            e.preventDefault();
            let tipoStatus ='';
            switch ($('#formDevice #formDeviceTipo').val()) {
                case 'd_device':
                    tipoStatus = 'eliminado';
                    break;
                case 'u_device':
                    tipoStatus = 'actualizado';
                    break;
                case 'c_device':
                    tipoStatus = 'creado';
                    break;            
                default:
                    tipoStatus = '';
                    break;
            }
            $.ajax({
                type: $(this).attr("method"),
                url: 'crud.php',
                data: $(this).serialize() + '&tipo='+ $('#formDevice #formDeviceTipo').val(),
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
                        let deviceName = data.Mensaje.deviceName
                        notify('Dispositivo ' + deviceName + ' '+tipoStatus+' '+ 'correctamente.', 'success', 5000, 'right')
                        // $('#tableUsuarios').DataTable().ajax.reload();
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
            $('#modales').html(' ');
        });
    });

});