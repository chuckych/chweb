$(document).ready(function () {
    function ClassTBody() {
        $('.open-modal').removeClass('btn-outline-custom')
        $('.contentd').addClass('text-light bg-light')
        $('.botones').hide()
    }
    var table = $('#GetClientes').DataTable({
        initComplete: function (settings, json) {
            $('.form-control-sm').attr('placeholder', 'Buscar Cuenta')
            $("thead").remove()
            // $('#GetClientes_filter').prepend('<button tittle="Agregar Cuenta" class="btn btn-outline-custom add fontq border"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/></svg></button>')
            $('#GetClientes_filter').prepend('<button title="Nueva Cuenta" class="px-2 btn btn-outline-custom addCuenta fontq border" id="addCuenta"><span><svg class="" width="20" height="20" fill="currentColor"><use xlink:href="../../img/bootstrap-icons.svg#plus-circle-fill"/></svg></span></button>')
            $('.table-responsive').show()
            fadeInOnly('#GetRoles')
            $(".addCuenta").hover(
                function () {
                    $(this).find("span").html('<span class="animate__animated animate__fadeIn"><svg class="mr-2" width="20" height="20" fill="currentColor"><use xlink:href="../../img/bootstrap-icons.svg#plus-circle-fill"/></svg>Nueva Cuenta</span>');
                },
                function () {
                    $(this).find("span").last().html('<span class="animate__animated animate__fadeIn"><svg class="" width="20" height="20" fill="currentColor"><use xlink:href="../../img/bootstrap-icons.svg#plus-circle-fill"/></svg></span>');
                }
            );
        },
        drawCallback: function (settings) {
            $(".dataTables_info").addClass('text-secondary');
            $(".custom-select").addClass('text-secondary');
            $('.contentd').removeClass('text-light bg-light')
            fadeInOnly('.table-responsive')
            $('.table-responsive').removeClass('invisible')
            $('.dataTables_length').addClass('d-none d-sm-block')
        },
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
        lengthMenu: [3, 5, 10, 25, 50, 100],
        rowGroup: {
            dataSrc: ['nombre']
        },
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
            {
                "class": "border-top-0",
                "data": "ident"
            },
            {
                "class": "border-top-0",
                "data": "host"
            },
            {
                "class": "border-top-0",
                "data": "db"
            },
            {
                "class": "border-top-0",
                "data": "user"
            },
            {
                "class": "border-top-0",
                "data": "pass"
            },
            {
                "class": "border-top-0",
                "data": "auth_windows"
            },
            {
                "class": "border-top-0",
                "data": "tkmobile"
            },
            {
                "class": "border-top-0",
                "data": "WebService"
            },
            {
                "class": "border-top-0",
                "data": "fecha_alta"
            },
            // {
            //     "class": "",
            //     "data": ""
            // }, 
            // {
            //     "class": "",
            //     "data": "null"
            // }, 
        ],
        scrollX: true,
        scrollCollapse: true,
        scrollY: '50vmax',
        paging: true,
        info: true,
        searching: true,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanishShort2.json"
        },
    });
    table.on('page.dt', function () {
        ClassTBody()
    });

    $(document).on('click', '.addCuenta', function (e) {
        $('#submitFormCuenta').val('AltaCuenta')
        $('#modalFormCuenta').modal('show')
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
        $('#submitFormCuenta').val('EditCuenta')
        let dataNombre = $(this).attr('dataNombre');
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
        $('#nombreCuenta').html('Editar Cuenta: ' + dataNombre)
        $('#nombre').val(dataNombre)
        $('#ident').val(dataIdent)
        $('#recid').val(dataRecid)
        $('#host').val(dataHost)
        $('#db').val(dataDB)
        $('#user').val(dataUser)
        $('#pass').val(dataPass)
        if ((dataAuth == '2')) {
            $('#auth').prop('checked', true)
        } else {
            $('#auth').prop('checked', false)
        }
        $('#tkmobile').val(dataTkmobile)
        $('#WebService').val(dataWebService)
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