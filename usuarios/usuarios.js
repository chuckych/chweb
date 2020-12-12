$(document).ready(function() {

    $('.requerido').addClass('fontp ml-1 ls1')
    $('.requerido').html('(*)')

    function ClassTBody() {
        $('.open-modal').removeClass('btn-outline-custom')
        $('.contentd').addClass('text-light bg-light w30')
        $('.botones').hide()
    }
    // $.fn.DataTable.ext.pager.numbers_length = 5;
    var table = $('#GetUsuarios').DataTable({
        initComplete: function(settings, json) {
            $('.form-control-sm').attr('placeholder', 'Buscar nombre') 
            $('#GetUsuarios_filter').prepend('<button tittle="Alta de Usuario" class="px-2 btn btn-outline-custom add fontq border"><svg class="" width="20" height="20" fill="currentColor"><use xlink:href="../img/bootstrap-icons.svg#person-plus-fill"/></svg></button>')
            if ($(window).width() < 769) {
                $('.botones').removeClass('float-right')
            } else {
                $('.botones').addClass('float-right')
            }

        },
        drawCallback: function(settings) {
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $(".custom-select").addClass('text-secondary');
            $('.contentd').removeClass('text-light bg-light w30')
            $('.botones').show()
            $('.table-responsive').removeClass('invisible')
            fadeInOnly('.table-responsive')
            $('.dataTables_length').addClass('d-none d-sm-block')
        },
        lengthMenu: [ 5, 10, 25, 50, 100 ],
        columnDefs: [{
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
                "visible": true,
                "targets": 3
            },
            {
                "visible": false,
                "targets": 6
            },
            {
                "visible": false,
                "targets": 8
            },
            {
                "visible": false,
                "targets": 9
            },
            {
                "visible": true,
                "targets": 10
            },
            {
                "visible": false,
                "targets": 11
            },
            {
                "visible": false,
                "targets": 14
            },
        ],
        rowGroup: {
            dataSrc: ['nombre']
        },
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        ajax: {
            url: "GetUsuarios.php",
            type: "POST",
            "data": function(data) {
                // console.log(data);
                data.recid_c = $("#recid_c").val();
            },
            error: function() {
                $("#GetUsuarios_processing").hide();
            },
        },
        columns: [{
                "class": "",
                /** Col 00 */
                "data": "uid"
            },
            {
                "class": "",
                /** Col 01 */
                "data": "recid"
            },
            {
                "class": "",
                /** Col 02 */
                "data": "nombre"
            },
            {
                "class": "border-0 pb-2",
                /** Col 03 */
                "data": "usuario"
            },
            {
                "class": "border-0",
                /** Col 04 */
                "data": "legajo"
            },
            {
                "class": "border-0",
                /** Col 05 */
                "data": "rol_n"
            },
            {
                "class": "",
                /** Col 06 */
                "data": "estado"
            },
            {
                "class": "border-0",
                /** Col 07 */
                "data": "estado_n"
            },
            {
                "class": "",
                /** Col 08 */
                "data": "id_cliente"
            },
            {
                "class": "",
                /** Col 09 */
                "data": "recid_cliente"
            },
            {
                "class": "border-0",
                /** Col 10 */
                "data": "cliente"
            },
            {
                "class": "",
                /** Col 11 */
                "data": "rol"
            },
            {
                "class": "border-0",
                /** Col 12 */
                "data": "fecha_alta"
            },
            {
                "class": "border-0",
                /** Col 13 */
                "data": "fecha_mod"
            },
            {
                "class": "text-nowrap",
                /** Col 14 */
                "data": "Buttons"
            },
        ],
        // scrollY: '450px',
        // scrollX: true,
        // scrollCollapse: false,
        paging: true,
        responsive: false,
        info: true,
        searching: true,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort.json"
        },
    });

    table.page.len('5').draw();
    table.on('page.dt', function() {
        ClassTBody()
    });
    if ($(window).width() < 769) {
        $('#GetUsuarios').removeClass('text-wrap')
        $('#GetUsuarios').addClass('text-nowrap')
    } else {
        $('#GetUsuarios').removeClass('text-nowrap')
        $('#GetUsuarios').addClass('text-wrap')
    }
    $(document).on("click", ".editar", function(e) {

        e.preventDefault();
        $('#modalEditUser').modal('show');
        // $('#e_nombre').focus()

        var data_uid = $(this).attr('data_uid');
        var data_nombre = $(this).attr('data_nombre');
        var data_usuario = $(this).attr('data_usuario');
        var data_rol_n = $(this).attr('data_rol_n');
        var data_rol = $(this).attr('data_rol');
        var data_legajo = $(this).attr('data_legajo');
        var data_estado_n = $(this).attr('data_estado_n');
        var data_estado = $(this).attr('data_estado');
        var data_fecha_alta = $(this).attr('data_fecha_alta');
        var data_fecha_mod = $(this).attr('data_fecha_mod');
        var data_cliente = $(this).attr('data_cliente');

        $('#data_nombre').html(data_nombre);
        $('#e_nombre').val(data_nombre);
        $('#e_usuario').val(data_usuario);
        $('#e_legajo').val(data_legajo);
        $('#e_uid').val(data_uid);

        var opt2 = {
            MinLength: "0",
            SelClose: false,
            MaxInpLength: "10",
            delay: "250",
            allowClear: false
        };

        $(".selectRol").select2({
            multiple: false,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: $('#modalEditUser'),
            placeholder: "Rol",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 5,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function() {
                    return 'No hay resultados..'
                },
                inputTooLong: function(args) {
                    var message = 'Máximo ' + opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function() {
                    return 'Buscando..'
                },
                errorLoading: function() {
                    return 'Sin datos..'
                },
                inputTooShort: function() {
                    return 'Ingresar ' + opt2["MinLength"] + ' o mas caracteres'
                },
                maximumSelected: function() {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "getRol.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function(params) {
                    return {
                        q: params.term,
                        recid_c: $("#recid_c").val(),
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    }
                },
            }
        });

        var newOption = new Option(data_rol_n, data_rol, false, true);
        $('.selectRol').append(newOption).trigger('change');

        ActiveBTN(false, '#submitEdit', 'Guardando', 'Guardar')

        $("#FormEdit").bind("submit", function(e) {
            e.preventDefault();
            $.ajax({
                type: $(this).attr("method"),
                url: $(this).attr("action"),
                data: $(this).serialize(),
                // async : false,
                beforeSend: function(data) {
                    ActiveBTN(true, '#submitEdit', 'Guardando', 'Guardar')
                },
                success: function(data) {
                    if (data.status == "ok") {
                        ActiveBTN(false, '#submitEdit', 'Guardando', 'Guardar')
                        $('#respuestaForm').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                        $('#GetUsuarios').DataTable().ajax.reload()
                        setTimeout(() => {
                            classEfect('#modalEditUser', 'animate__animated animate__fadeOut')
                            setTimeout(() => {
                                $('#modalEditUser').modal('hide')
                            }, 500);
                        }, 1500);
                    } else {
                        ActiveBTN(false, '#submitEdit', 'Guardando', 'Guardar')
                        $('#respuestaForm').html('<div class="py-3 fontq text-danger fw5">' + data.Mensaje + '</div>')
                    }
                }
            });
            e.stopImmediatePropagation();
        });
    });

    $('#modalEditUser').on('hidden.bs.modal', function() {
        ActiveBTN(false, '#submitEdit', 'Guardando', 'Guardar')
        $('#data_nombre').html('');
        $('#e_nombre').val('');
        $('#e_usuario').val('');
        $('#e_legajo').val('');
        $('#e_uid').val('');
        $('#respuestaForm').html('')
    });

    $(document).on("click", ".add", function(e) {

        e.preventDefault();

        $('#modalAddUser').modal('show');
        $('#a_nombre').focus()
        $('#a_recid').val($("#recid_c").val())

        var opt2 = {
            MinLength: "0",
            SelClose: false,
            MaxInpLength: "10",
            delay: "250",
            allowClear: false
        };

        $(".selectRol").select2({
            multiple: false,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: $('#modalAddUser'),
            placeholder: "Rol",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 5,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function() {
                    return 'No hay resultados..'
                },
                inputTooLong: function(args) {
                    var message = 'Máximo ' + opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function() {
                    return 'Buscando..'
                },
                errorLoading: function() {
                    return 'Sin datos..'
                },
                inputTooShort: function() {
                    return 'Ingresar ' + opt2["MinLength"] + ' o mas caracteres'
                },
                maximumSelected: function() {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "getRol.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function(params) {
                    return {
                        q: params.term,
                        recid_c: $("#recid_c").val(),
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    }
                },
            }
        });

        ActiveBTN(false, '#submitAdd', 'Guardando', 'Agregar')

        $("#FormAdd").bind("submit", function(e) {
            e.preventDefault();
            $.ajax({
                type: $(this).attr("method"),
                url: $(this).attr("action"),
                data: $(this).serialize(),
                // async : false,
                beforeSend: function(data) {
                    ActiveBTN(true, '#submitAdd', 'Guardando', 'Agregar')
                },
                success: function(data) {
                    if (data.status == "ok") {
                        ActiveBTN(false, '#submitAdd', 'Guardando', 'Agregar')
                        $('#respuestaFormAdd').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                        $('#GetUsuarios').DataTable().ajax.reload()
                        setTimeout(() => {
                            classEfect('#modalAddUser', 'animate__animated animate__fadeOut')
                            setTimeout(() => {
                                $('#modalAddUser').modal('hide')
                            }, 500);
                        }, 1500);
                    } else {
                        ActiveBTN(false, '#submitAdd', 'Guardando', 'Agregar')
                        $('#respuestaFormAdd').html('<div class="py-3 fontq text-danger fw5">' + data.Mensaje + '</div>')
                    }
                },
                error: function(data) {
                    ActiveBTN(false, '#submitAdd', 'Guardando', 'Agregar')
                    $('#respuestaFormAdd').html('<div class="py-3 fontq text-danger fw5">Error</div>')
                }
            });
            e.stopImmediatePropagation();
        });

    });

    $('#modalAddUser').on('hidden.bs.modal', function() {
        ActiveBTN(false, '#submitAdd', 'Guardando', 'Agregar')
        $('#a_nombre').val('')
        $('#a_usuario').val('')
        $('#a_legajo').val('')
        $(".selectRol").val(null).trigger('change')
        $('#respuestaFormAdd').html('')
    });

    $(document).on('click', '.resetKey', function(e) {

        e.preventDefault();
        var data_uid = $(this).attr('data_uid');
        var data_nombre = $(this).attr('data_nombre');
        var data_usuario = $(this).attr('data_usuario');
        $('.resetKey').unbind('click');

        $.ajax({
            type: "POST",
            url: "crud.php",
            'data': {
                submit: 'key',
                uid: data_uid,
                nombre: data_nombre,
                usuario: data_usuario
            },
            beforeSend: function(data) {
                $('#reset_' + data_uid).prop('disabled', true)
            },
            success: function(data) {
                if (data.status == "ok") {
                    $('#reset_' + data_uid).prop('disabled', false)
                    $('#respuestaResetClave').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                } else {
                    $('#reset_' + data_uid).prop('disabled', false)
                    $('#respuestaResetClave').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                }
            },
            error: function(data) {
                $('#reset_' + data_uid).prop('disabled', false)
                $('#respuestaResetClave').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>Error</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
            }
        });

        e.stopImmediatePropagation();
    });

    $(document).on('click', '.estado', function(e) {

        e.preventDefault();
        var data_uid = $(this).attr('data_uid');
        var data_nombre = $(this).attr('data_nombre');
        var data_estado = $(this).attr('data_estado');
        $('.estado').unbind('click');

        $.ajax({
            type: "POST",
            url: "crud.php",
            'data': {
                submit: 'estado',
                uid: data_uid,
                nombre: data_nombre,
                estado: data_estado
            },
            beforeSend: function(data) {
                $('#estado_' + data_uid).prop('disabled', true)
            },
            success: function(data) {
                if (data.status == "ok") {
                    $('#estado_' + data_uid).prop('disabled', false)
                    $('#respuestaResetClave').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                    $('#GetUsuarios').DataTable().ajax.reload()
                } else {
                    $('#estado_' + data_uid).prop('disabled', false)
                    $('#respuestaResetClave').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                }
            },
            error: function(data) {
                $('#estado_' + data_uid).prop('disabled', false)
                $('#respuestaResetClave').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>Error</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
            }
        });

        e.stopImmediatePropagation();
    });

    $(document).on('click', '.delete', function(e) {

        e.preventDefault();
        var data_uid = $(this).attr('data_uid');
        var data_nombre = $(this).attr('data_nombre');
        $('.delete').unbind('click');

        $.ajax({
            type: "POST",
            url: "crud.php",
            'data': {
                submit: 'delete',
                uid: data_uid,
                nombre: data_nombre,
            },
            beforeSend: function(data) {
                $('#delete_' + data_uid).prop('disabled', true)
            },
            success: function(data) {
                if (data.status == "ok") {
                    $('#delete_' + data_uid).prop('disabled', false)
                    $('#respuestaResetClave').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                    $('#GetUsuarios').DataTable().ajax.reload()
                } else {
                    $('#delete_' + data_uid).prop('disabled', false)
                    $('#respuestaResetClave').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                }
            },
            error: function(data) {
                $('#delete_' + data_uid).prop('disabled', false)
                $('#respuestaResetClave').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>Error</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
            }
        });

        e.stopImmediatePropagation();
    });
});