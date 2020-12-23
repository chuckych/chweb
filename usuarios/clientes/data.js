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
                ActiveBTN(true, '#submitAdd', 'Aguarde..', 'Aceptar')
            },
            success: function (data) {
                if (data.status == "ok") {
                    document.getElementById('FormCuenta').reset();
                    ActiveBTN(false, '#submitAdd', 'Aguarde..', 'Aceptar')
                    $('.respuesta').html('<div class="animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                    $('#GetClientes').DataTable().ajax.reload()
                    setTimeout(() => {
                        setTimeout(() => {
                            classEfect('#modalFormCuenta', 'animate__animated animate__fadeOut')
                            $('#modalFormCuenta').modal('hide')
                            $('.respuesta').html('')
                        }, 500);
                    }, 1500);
                } else {
                    ActiveBTN(false, '#submitAdd', 'Aguarde..', 'Aceptar')
                    $('.respuesta').html('<div class="py-3 fontq text-danger fw5">' + data.Mensaje + '</div>')
                }
            },
            error: function (data) {
                ActiveBTN(false, '#submitAdd', 'Aguarde..', 'Aceptar')
                $('.respuesta').html('<div class="py-3 fontq text-danger fw5">Error</div>')
            }
        });
        e.stopImmediatePropagation();
    });

    $(document).on('click', '.editCuenta', function (e) {
        $('#modalFormCuenta').modal('show')
        $('#submitFormCuenta').val('EditCuenta')
        var dataNombre     = $(this).attr('dataNombre');
        var dataIdent      = $(this).attr('dataIdent');
        var dataRecid      = $(this).attr('dataRecid');
        var dataId         = $(this).attr('dataId');
        var dataHost       = $(this).attr('dataHost');
        var dataDB         = $(this).attr('dataDB');
        var dataUser       = $(this).attr('dataUser');
        var dataPass       = $(this).attr('dataPass');
        var dataAuth       = $(this).attr('dataAuth');
        var dataTkmobile   = $(this).attr('dataTkmobile');
        var dataWebService = $(this).attr('dataWebService');
        $('#nombreCuenta').html('Editar Cuenta: '+dataNombre)
        $('#nombre').val(dataNombre)
        $('#ident').val(dataIdent)
        $('#recid').val(dataRecid)
        $('#host').val(dataHost)
        $('#db').val(dataDB)
        $('#user').val(dataUser)
        $('#pass').val(dataPass)
        if ((dataAuth=='2')) {
            $('#auth').prop('checked',true)
        }else{
            $('#auth').prop('checked',false)
        }
        $('#tkmobile').val(dataTkmobile)
        $('#WebService').val(dataWebService)
    });
    $('#modalFormCuenta').on('hidden.bs.modal', function (e) {
        document.getElementById('FormCuenta').reset();
        $('#nombreCuenta').html('Nueva Cuenta')
        $('#submitFormCuenta').val('AltaCuenta')
    })
});