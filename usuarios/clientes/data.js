$(document).ready(function () {
    let table = $('#GetClientes').DataTable({
        bProcessing: true,
        deferRender: true,
        // searchDelay: 1500,
        ajax: {
            url: "GetClientes.php",
            type: "POST",
            dataType: "json",
            "data": function (data) {

            },
        },
        columnDefs: [
            {
                "visible": false,
                "targets": 0
            },
            {
                "visible": false,
                "targets": 1
            },
            {
                "visible": false,
                "targets": 2
            },
        ],
        lengthMenu: [5, 10, 25, 50, 100],
        rowGroup: {
            dataSrc: ['nombre']
        },
        dom: `<'row'<'col-sm-3 d-none d-sm-block'l><'col-sm-9 col-12 d-inline-flex w-100 justify-content-end'f>>` +
            `<'row'<'col-12 table-responsive invisible'tr>>` +
            `<'row'<'col-sm-5 d-none d-sm-block'i><'col-sm-7 col-12 d-none d-sm-block'p>>`+
            `<'row d-sm-none d-block'<'d-flex justify-content-center fixed-bottom col-12 bg-white'p>>`,
        columns: [
            {
                "class": "",
                "data": "nombre"
            },
            {
                "class": "border-top-0",
                "data": "cant_usuarios"
            },
            {
                "class": "border-top-0",
                "data": "cant_roles"
            },
            // {
            //     "class": "border-top-0",
            //     "data": "ident"
            // },
            // {
            //     "class": "border-top-0",
            //     "data": "host"
            // },
            // {
            //     "class": "border-top-0",
            //     "data": "user"
            // },
            // {
            //     "class": "border-top-0",
            //     "data": "WebService"
            // },
            // {
            //     "class": "border-top-0",
            //     "data": "MobileHRP"
            // },
            // {
            //     "class": "border-top-0 w-100 text-right",
            //     "data": "fecha_alta"
            // },
        ],
        // scrollX: true,
        // scrollCollapse: true,
        // scrollY: '50vmax',
        paging: true,
        info: true,
        searching: true,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanishShort2.json"
        },
    });
    table.on('init.dt', function (e, settings, json) {
        $('#GetClientes_filter input').attr('placeholder', 'Buscar Cuenta').removeClass('form-control-sm');
        $("thead").remove()
        $('#GetClientes_filter').addClass('d-flex justify-content-end align-items-start');
        $('#GetClientes_filter').prepend('<button data-titlel="Nueva Cuenta" class="px-2 btn btn-custom addCuenta fontq border-ddd" id="addCuenta"><span class="bi bi-plus-lg mr-1"></span>Nueva</button>')
        $('.table-responsive').show()
        fadeInOnly('#GetRoles')
        // console.log(json.dataClientes);
    });
    table.on('draw.dt', function (e, settings) {
        e.preventDefault();
        $(".dataTables_info").addClass('text-secondary');
        $(".custom-select").addClass('text-secondary');
        fadeInOnly('.table-responsive')
        $('.table-responsive').removeClass('invisible')
        $('.dataTables_length').addClass('d-none d-sm-block')
    });
    table.on('xhr.dt', function (e, settings, json) {
        table.off('xhr');
    });

    $(document).on('click', '.addCuenta', function (e) {
        $('#submitFormCuenta').val('AltaCuenta')
        $('#modalFormCuenta').modal('show')
        $('#modalFormCuenta input').attr('autocomplate', 'off')
    });

    $(document).on('click', '#cancelarAlta', function (e) {
        e.preventDefault();
        $('#modalFormCuenta').modal('hide')
        $('.respuesta').html('')
    });

    ActiveBTN(false, '#submitAdd', 'Aguarde..', 'Aceptar')
    $("#FormCuenta").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            // async : false,
            beforeSend: function (data) {
                $.notifyClose()
                ActiveBTN(true, '#submitAdd', 'Aguarde..', 'Aceptar')
            },
            success: function (data) {
                if (data.status == "ok") {
                    document.getElementById('FormCuenta').reset();
                    ActiveBTN(false, '#submitAdd', 'Aguarde..', 'Aceptar')
                    $('#GetClientes').DataTable().ajax.reload();
                    $('#modalFormCuenta').modal('hide')
                    notify(data.Mensaje, 'success', 5000, 'right')
                } else {
                    ActiveBTN(false, '#submitAdd', 'Aguarde..', 'Aceptar')
                    notify(data.Mensaje, 'danger', 5000, 'right')
                }
            },
            error: function (data) {
                ActiveBTN(false, '#submitAdd', 'Aguarde..', 'Aceptar')
                notify('Error', 'danger', 5000, 'right')
            }
        });
        e.stopImmediatePropagation();
    });

    $(document).on('click', '.editCuenta', function (e) {
        $('#modalFormCuenta').modal('show')
        $('#modalFormCuenta input').attr('autocomplate', 'off')
        $('#submitFormCuenta').val('EditCuenta')
        let dataNombre = $(this).attr('dataNombre');
        let datahostchweb = $(this).attr('datahostchweb');
        let dataIdent = $(this).attr('dataIdent');
        let dataRecid = $(this).attr('dataRecid');
        let dataId = $(this).attr('dataId');
        let dataHost = $(this).attr('dataHost');
        let dataDB = $(this).attr('dataDB');
        let dataUser = $(this).attr('dataUser');
        let dataPass = $(this).attr('dataPass');
        let dataAuth = $(this).attr('dataAuth');
        let dataTkmobile = $(this).attr('dataTkmobile');
        let dataWebService = $(this).attr('dataWebService');
        let ApiMobileHRPApp = $(this).attr('dataMobileHRPApp');
        let ApiMobileHRP = $(this).attr('dataapimobilehrp');
        let LocalCH = $(this).attr('datalocalch');
        $('#nombreCuenta').html('Editar Cuenta: ' + dataNombre)
        $('#nombre').val(dataNombre)
        $('#ident').val(dataIdent)
        $('#recid').val(dataRecid)
        $('#host').val(dataHost)
        $('#db').val(dataDB)
        $('#user').val(dataUser)
        $('#pass').val(dataPass)
        $('#hostCHWeb').val(datahostchweb)
        
        if ((dataAuth == '2')) {
            $('#auth').prop('checked', true)
        } else {
            $('#auth').prop('checked', false)
        }
        if(LocalCH == '1'){
            $('#labelInactivo').addClass('active')
            $('#labelActivo').removeClass('active')
            $('#localCHSI').prop('checked', false)
            $('#localCHNO').prop('checked', true)
        }else{
            $('#labelActivo').addClass('active')
            $('#labelInactivo').removeClass('active')
            $('#localCHNO').prop('checked', false)
            $('#localCHSI').prop('checked', true)
        }
        $('#tkmobile').val(dataTkmobile)
        $('#WebService').val(dataWebService)
        $('#ApiMobileHRP').val(ApiMobileHRP)
        $('#ApiMobileHRPApp').val(ApiMobileHRPApp)
    });
    $('#modalFormCuenta').on('hidden.bs.modal', function (e) {
        document.getElementById('FormCuenta').reset();
        $('#nombreCuenta').html('Nueva Cuenta')
        $('#submitFormCuenta').val('AltaCuenta')
    })
    $(document).on('click', '.testConnect', function (e) {
        e.preventDefault();
        let dataRecid = $(this).attr('dataRecid');
        $.ajax({
            type: 'GET',
            url: 'testConnect.php?_c=' + dataRecid,
            beforeSend: function (data) {
                ActiveBTN(true, ".testConnect", 'Aguarde..', 'Test Conexión')
                $.notifyClose();
                notify('Aguarde..', 'info', 0, 'right')
            },
            success: function (data) {
                if (data.status == "ok") {
                    $.notifyClose();
                    ActiveBTN(false, ".testConnect", 'Aguarde..', 'Test Conexión')
                    notify(data.Mensaje, 'success', 2000, 'right')
                } else {
                    $.notifyClose();
                    ActiveBTN(false, ".testConnect", 'Aguarde..', 'Test Conexión')
                    notify(data.Mensaje, 'danger', 5000, 'right')
                }
            },
            error: function () {
                $.notifyClose();
                ActiveBTN(false, ".testConnect", 'Aguarde..', 'Test Conexión')
                notify('Error', 'danger', 2000, 'right')
            }
        });
    });
});