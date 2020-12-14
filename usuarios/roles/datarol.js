$(document).ready(function () {

    $('#collapse_rol').on('shown.bs.collapse', function () {
        $('#nombre').focus()
    })

    $(document).on('click', '.editRol', function (e) {
        e.preventDefault();
        var datarol = $(this).attr('datarol');
        var dataidrol = $(this).attr('dataidrol');
        var datarecid_c = $(this).attr('datarecid_c');

        var locale = {
            OK: 'OK',
            CONFIRM: 'Confirmar',
            CANCEL: 'Cancelar'
        };

        setTimeout(() => {
            $('.modal-footer').addClass('bg-light')
            $('.modal-header').addClass('border-bottom-0')
            $('.bootbox-input-text').addClass('h40')
            $('.bootbox-input-text').val(datarol)
            $('.bootbox-input-text').select()
            $('.bootbox-input-text').attr('placeholder', 'Nombre Rol')
            // $('.bootbox-input-text').prop('required',true)
        }, 200);
        bootbox.addLocale('custom', locale);
        bootbox.prompt({
            size: 'small',
            buttons: {
                confirm: {
                    label: 'Guardar',
                    className: 'btn-custom btn-sm fontq btn-mobile'
                },
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-outline-custom border btn-sm fontq btn-mobile'
                }
            },
            title: "<span class='fonth text-secondary'>Editar Rol: " + datarol + "</span>",
            locale: 'custom',
            callback: function (result) {
                if (result) {
                    $.ajax({
                        type: "POST",
                        url: "crudRol.php",
                        'data': {
                            submit: 'editarol',
                            id: dataidrol,
                            nombre: datarol,
                            recid_c: datarecid_c,
                            nombre_nuevo: result,
                        },
                        beforeSend: function (data) {
                            $('#Editar_' + dataidrol).prop('disabled', true)
                        },
                        success: function (data) {
                            if (data.status == "ok") {
                                $('#Editar_' + dataidrol).prop('disabled', false)
                                $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                                $('#GetRoles').DataTable().ajax.reload();
                            } else if (data.status == "nocambios") {
                                $('#Editar_' + dataidrol).prop('disabled', false)
                                $('#respuesta').html('')
                            } else {
                                $('#Editar_' + dataidrol).prop('disabled', false)
                                $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                            }
                        },
                        error: function (data) {
                            $('#Editar_' + dataidrol).prop('disabled', false)
                            $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>Error</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                        }
                    });
                    // });
                }
            }
        });

    });

    $(document).on('click', '.addRol', function (e) {
        e.preventDefault();
        var datarecid_c = $('#recid_cRol').val();

        var locale = {
            OK: 'OK',
            CONFIRM: 'Confirmar',
            CANCEL: 'Cancelar'
        };

        setTimeout(() => {
            $('.modal-footer').addClass('bg-light')
            $('.modal-header').addClass('border-bottom-0')
            $('.bootbox-input-text').addClass('h40')
            $('.bootbox-input-text').select()
            $('.bootbox-input-text').attr('placeholder', 'Nombre del Rol')
            // $('.bootbox-input-text').prop('required',true)
        }, 200);
        bootbox.addLocale('custom', locale);
        bootbox.prompt({
            size: 'small',
            buttons: {
                confirm: {
                    label: 'Aceptar',
                    className: 'btn-custom btn-sm fontq btn-mobile'
                },
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-outline-custom border btn-sm fontq btn-mobile'
                }
            },
            title: "<span class='fonth text-secondary'>Alta Rol</span>",
            locale: 'custom',
            callback: function (result) {
                if (result) {
                    $.ajax({
                        type: "POST",
                        url: "crudRol.php",
                        'data': {
                            submit: 'addRol',
                            recid_c: datarecid_c,
                            nombre: result,
                        },
                        beforeSend: function (data) {
                            $('#AltaRol').prop('disabled', true)
                        },
                        success: function (data) {
                            if (data.status == "ok") {
                                $('#AltaRol').prop('disabled', false)
                                $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                                $('#GetRoles').DataTable().ajax.reload();
                            } else if (data.status == "nocambios") {
                                $('#AltaRol').prop('disabled', false)
                                $('#respuesta').html('')
                            } else {
                                $('#AltaRol').prop('disabled', false)
                                $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                            }
                        },
                        error: function (data) {
                            $('#AltaRol').prop('disabled', false)
                            $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>Error</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                        }
                    });
                    // });
                }
            }
        });

    });

    $(document).on('click', '.deleteRol', function (e) {
        e.preventDefault();
        var datarol = $(this).attr('datarol');
        var dataidrol = $(this).attr('dataidrol');
        var datarecid_c = $(this).attr('datarecid_c');

        bootbox.confirm({
            // title: "<span class='fonth'>Eliminar Usuario</span>",
            message: '<span class="fonth fw4">¿Confirma eliminar el Rol: <span class="fw5">' + datarol + '</span>?</span>',
            buttons: {
                confirm: {
                    label: 'Confirmar',
                    className: 'btn-custom btn-sm fontq btn-mobile'
                },
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-outline-custom border btn-sm fontq btn-mobile'
                }
            },
            callback: function (result) {
                $('.deleteRol').unbind('click');
                if (result) {
                    $.ajax({
                        type: "POST",
                        url: "crudRol.php",
                        'data': {
                            submit: 'deleteRol',
                            id: dataidrol,
                            nombre: datarol,
                        },
                        beforeSend: function (data) {
                            $('#delete_' + dataidrol).prop('disabled', true)
                        },
                        success: function (data) {
                            if (data.status == "ok") {
                                $('#delete_' + dataidrol).prop('disabled', false)
                                $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                                $('#GetRoles').DataTable().ajax.reload();
                            } else {
                                $('#delete_' + dataidrol).prop('disabled', false)
                                $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                            }
                        },
                        error: function (data) {
                            $('#delete_' + dataidrol).prop('disabled', false)
                            $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>Error</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                        }
                    });
                }
            }
        });
        e.stopImmediatePropagation();
    });

    function ClassTBody() {
        $('.open-modal').removeClass('btn-outline-custom')
        $('.contentd').addClass('text-light bg-light border-0')
        $('.botones').hide()
    }


    // $.fn.DataTable.ext.pager.numbers_length = 5;
    var table = $('#GetRoles').DataTable({
        initComplete: function (settings, json) {
            $('.form-control-sm').attr('placeholder', 'Buscar Rol')
            $('.LabelSearchDT').html('')
            $('#GetRoles_filter').prepend('<button title="Nuevo Rol" class="px-2 btn btn-outline-custom addRol fontq border" id="AltaRol"><span><svg class="" width="20" height="20" fill="currentColor"><use xlink:href="../../img/bootstrap-icons.svg#plus-circle-fill"/></svg></span></button>')
            $('.table-responsive').show()
            fadeInOnly('#GetRoles')
            $(".addRol").hover(
                function () {
                    $(this).find("span").html('<span class="animate__animated animate__fadeIn"><svg class="mr-2" width="20" height="20" fill="currentColor"><use xlink:href="../../img/bootstrap-icons.svg#plus-circle-fill"/></svg>Nuevo Rol</span>');
                },
                function () {
                    $(this).find("span").last().html('<span class="animate__animated animate__fadeIn"><svg class="" width="20" height="20" fill="currentColor"><use xlink:href="../../img/bootstrap-icons.svg#plus-circle-fill"/></svg></span>');
                }
            );
        },
        drawCallback: function (settings) {
            $('.contentd').removeClass('text-light bg-light border-0')
        },
        lengthMenu: [5, 10, 25, 50, 100],
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
            {
                "visible": false,
                "targets": 4
            },
            {
                "visible": false,
                "targets": 5
            },
            {
                "visible": false,
                "targets": 15
            },
            {
                "visible": false,
                "targets": 16
            },
        ],
        // rowGroup: {
        //     dataSrc: ['nombre']
        // },
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        ajax: {
            url: "GetRoles.php?_c=" + $("#recid_cRol").val(),
            type: "POST",
            "data": function (data) {
                // console.log(data);
                data.recid_c = $("#recid_cRol").val();
            },
            error: function () {
                $("#GetRoles_processing").hide();
            },
        },
        columns: [
            {
                "class": "",
                /** Col 00 */
                "data": "id"
            },
            {
                "class": "",
                /** Col 01 */
                "data": "recid"
            },
            {
                "class": "",
                /** Col 02 */
                "data": "recid_cliente"
            },
            {
                "class": "",
                /** Col 03 */
                "data": "nombre"
            },
            {
                "class": "",
                /** Col 04 */
                "data": "id_cliente"
            },
            {
                "class": "",
                /** Col 05 */
                "data": "cliente"
            },
            {
                "class": "text-center",
                /** Col 06 */
                "data": "cant_roles"
            },
            {
                "class": "text-center",
                /** Col 07 */
                "data": "cant_modulos"
            },
            {
                "class": "text-center",
                /** Col 08 */
                "data": "abm_rol"
            },
            {
                "class": "text-center",
                /** Col 09 */
                "data": "cant_empresas"
            },
            {
                "class": "text-center",
                /** Col 10 */
                "data": "cant_plantas"
            },
            {
                "class": "text-center",
                /** Col 11 */
                "data": "cant_convenios"
            },
            {
                "class": "text-center",
                /** Col 12 */
                "data": "cant_sectores"
            },
            {
                "class": "text-center",
                /** Col 13 */
                "data": "cant_grupos"
            },
            {
                "class": "text-center",
                /** Col 14 */
                "data": "cant_sucur"
            },
            {
                "class": "",
                /** Col 15 */
                "data": "fecha_alta"
            },
            {
                "class": "",
                /** Col 16 */
                "data": "fecha_mod"
            },
            {
                "class": "",
                /** Col 17 */
                "data": "edit_rol"
            },
        ],
        paging: true,
        responsive: false,
        info: true,
        searching: true,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanishShort.json"
        },
    });

    table.page.len('5').draw();
    table.on('page.dt', function () {
        ClassTBody()
    });
});