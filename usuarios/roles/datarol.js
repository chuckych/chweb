$('#collapse_rol').on('shown.bs.collapse', function() {
    $('#nombre').focus()
})

$(document).on('click', '.editRol', function(e) {
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
        callback: function(result) {
            // $(document).on('click', '.bootbox-accept', function(e) {
            // $('.bootbox-accept').on("click", function(e) {
            // e.preventDefault();
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
                    beforeSend: function(data) {
                        $('#Editar_' + dataidrol).prop('disabled', true)
                    },
                    success: function(data) {
                        if (data.status == "ok") {
                            $('#Editar_' + dataidrol).prop('disabled', false)
                            $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                            // $('#GetRoles').DataTable().ajax.reload()
                        } else if (data.status == "nocambios") {
                            $('#Editar_' + dataidrol).prop('disabled', false)
                            $('#respuesta').html('')
                        } else {
                            $('#Editar_' + dataidrol).prop('disabled', false)
                            $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                        }
                    },
                    error: function(data) {
                        $('#Editar_' + dataidrol).prop('disabled', false)
                        $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>Error</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                    }
                });
                // });
            }
        }
    });

});

$(document).on('click', '.addRol', function(e) {
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
        callback: function(result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "crudRol.php",
                    'data': {
                        submit: 'addRol',
                        recid_c: datarecid_c,
                        nombre: result,
                    },
                    beforeSend: function(data) {
                        $('#AltaRol').prop('disabled', true)
                    },
                    success: function(data) {
                        if (data.status == "ok") {
                            $('#AltaRol').prop('disabled', false)
                            $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                            // $('#GetRoles').DataTable().ajax.reload()
                        } else if (data.status == "nocambios") {
                            $('#AltaRol').prop('disabled', false)
                            $('#respuesta').html('')
                        } else {
                            $('#AltaRol').prop('disabled', false)
                            $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                        }
                    },
                    error: function(data) {
                        $('#AltaRol').prop('disabled', false)
                        $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>Error</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                    }
                });
                // });
            }
        }
    });

});

$(document).on('click', '.deleteRol', function(e) {
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
        callback: function(result) {
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
                    beforeSend: function(data) {
                        $('#delete_' + dataidrol).prop('disabled', true)
                    },
                    success: function(data) {
                        if (data.status == "ok") {
                            $('#delete_' + dataidrol).prop('disabled', false)
                            $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        } else {
                            $('#delete_' + dataidrol).prop('disabled', false)
                            $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>' + data.Mensaje + '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                        }
                    },
                    error: function(data) {
                        $('#delete_' + dataidrol).prop('disabled', false)
                        $('#respuesta').html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>Error</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                    }
                });
            }
        }
    });
    e.stopImmediatePropagation();
});