$(function () {
    $('.requerido').html('(*)')
    $('.opcional').html('<span class="fontp">(opcional)</span>')
    $("#Formulario").bind("submit", function (event) {
        event.preventDefault();
        if ($('#desc').val() == '') {
            notify('Campo Descripción requerido', 'danger', 2000, 'center')
            $('#desc').addClass('border border-danger')
            $('#desc').attr('placeholder', 'Campo requerido')
            $('.requerido').show()
        } else if ($('#desc').val().length < 3) {
            $('#desc').addClass('border border-warning')
            notify('La descripción debe contener como mínimo 3 caracteres', 'warning', 2000, 'center')
        } else {
            $.ajax({
                type: $(this).attr("method"),
                url: $(this).attr("action"),
                data: $(this).serialize(),

                beforeSend: function (data) {
                    ActiveBTN(true, '.submit', 'Aguarde..', 'Aceptar')
                },
                
                success: function (data) {
                    if (data.status == "ok") {

                        switch ($('#tipo').val()) {
                            case 'u_nacion':
                                $('#tableNacion').DataTable().ajax.reload(null, false);
                                $('#actionForm').html('')
                                break;
                            case 'd_nacion':
                                $('#tableNacion').DataTable().ajax.reload(null, false);
                                $('#actionForm').html('')
                                break;
                            case 'c_nacion':
                                $('#tableNacion').DataTable().ajax.reload(null, false);
                                $('#actionForm').html('')
                                break;
                            case 'u_provincia':
                                $('#tableProvincias').DataTable().ajax.reload(null, false);
                                $('#actionForm_p').html('')
                                break;
                            case 'd_provincia':
                                $('#tableProvincias').DataTable().ajax.reload(null, false);
                                $('#actionForm_p').html('')
                                break;
                            case 'c_provincia':
                                $('#tableProvincias').DataTable().ajax.reload(null, false);
                                $('#actionForm_p').html('')
                                break;
                            case 'u_localidad':
                                $('#tableLocalidad').DataTable().ajax.reload(null, false);
                                $('#actionForm_l').html('')
                                break;
                            case 'd_localidad':
                                $('#tableLocalidad').DataTable().ajax.reload(null, false);
                                $('#actionForm_l').html('')
                                break;
                            case 'c_localidad':
                                $('#tableLocalidad').DataTable().ajax.reload(null, false);
                                $('#actionForm_l').html('')
                                break;
                        }

                        notify(data.Mensaje, 'success', 2000, 'center')
                    } else {
                        ActiveBTN(false, '.submit', 'Aguarde..', 'Aceptar')
                        notify(data.Mensaje, 'danger', 2000, 'center')
                    }
                },
                error: function (data) {
                    ActiveBTN(false, '.submit', 'Aguarde..', 'Aceptar')
                    notify('Error', 'danger', 2000, 'center')
                }
            });
        }
        event.stopImmediatePropagation();
    });

    switch ($('#tipo').val()) {
        case 'u_nacion':
            $('#cod').attr('readonly', true)
            $('#desc').attr('placeholder', 'Nacionalidad')
            focusEndText('#desc')
            $('.opcional').remove()
            $('.requerido').hide()
            break;
        case 'd_nacion':
            $('#cod').attr('readonly', true)
            $('.opcional').remove()
            $('.requerido').remove()
            $('#desc').attr('readonly', true)
            break;
        case 'c_nacion':
            $('#desc').attr('placeholder', 'Nacionalidad')
            $('#desc').focus()
            break;
        case 'u_provincia':
            $('#cod').attr('readonly', true)
            $('#desc').attr('placeholder', 'Provincia')
            focusEndText('#desc')
            $('.opcional').remove()
            $('.requerido').hide()
            break;
        case 'd_provincia':
            $('#cod').attr('readonly', true)
            $('.opcional').remove()
            $('.requerido').remove()
            $('#desc').attr('readonly', true)
            break;
        case 'c_provincia':
            $('#desc').attr('placeholder', 'Provincia')
            $('#desc').focus()
            break;
        case 'u_localidad':
            $('#cod').attr('readonly', true)
            $('#desc').attr('placeholder', 'Localidad')
            focusEndText('#desc')
            $('.opcional').remove()
            $('.requerido').hide()
            break;
        case 'd_localidad':
            $('#cod').attr('readonly', true)
            $('.opcional').remove()
            $('.requerido').remove()
            $('#desc').attr('readonly', true)
            break;
        case 'c_localidad':
            $('#desc').attr('placeholder', 'Localidad')
            $('#desc').focus()
            break;
    }
    $("#cancelForm").on("click", function () {
        $('#actionForm_p').html('')
        $('#actionForm_l').html('')
        $('#actionForm').html('')
    });
});