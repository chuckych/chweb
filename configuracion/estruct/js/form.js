$(function () {
    $('.requerido').html('(*)')
    $('.opcional').html('<span class="fontp">(opcional)</span>')
    $("#Formulario").bind("submit", function (event) {
        event.preventDefault();
        if ($('#desc').val() == '') {
            switch ($('#tipo').val()) {
                case 'c_empresas':
                    notify('Campo <strong>Razón Social</strong> requerido', 'danger', 2000, 'center')
                    break;
                case 'u_empresas':
                    notify('Campo <strong>Razón Social</strong> requerido', 'danger', 2000, 'center')
                    break;
                case 'd_empresas':
                    notify('Campo <strong>Razón Social</strong> requerido', 'danger', 2000, 'center')
                    break;
                default:
                    notify('Campo <strong>Descripción</strong> requerido', 'danger', 2000, 'center')
                    break;
            }            
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
                            case 'u_plantas':
                                $('#tableplantas').DataTable().ajax.reload(null, false);
                                $('#actionForm').html('')
                                break;
                            case 'd_plantas':
                                $('#tableplantas').DataTable().ajax.reload(null, false);
                                $('#actionForm').html('')
                                break;
                            case 'c_plantas':
                                $('#tableplantas').DataTable().ajax.reload(null, false);
                                $('#actionForm').html('')
                                break;
                            case 'u_empresas':
                                $('#tableempresas').DataTable().ajax.reload(null, false);
                                $('#actionForm_e').html('')
                                break;
                            case 'd_empresas':
                                $('#tableempresas').DataTable().ajax.reload(null, false);
                                $('#actionForm_e').html('')
                                $('#personalTable_e').html('')
                                break;
                            case 'c_empresas':
                                $('#tableempresas').DataTable().ajax.reload(null, false);
                                $('#actionForm_e').html('')
                                break;
                            case 'u_sucur':
                                $('#tablesucur').DataTable().ajax.reload(null, false);
                                $('#actionForm_suc').html('')
                                break;
                            case 'd_sucur':
                                $('#tablesucur').DataTable().ajax.reload(null, false);
                                $('#actionForm_suc').html('')
                                break;
                            case 'c_sucur':
                                $('#tablesucur').DataTable().ajax.reload(null, false);
                                $('#actionForm_suc').html('')
                                break;
                            case 'u_grupos':
                                $('#tablegrupos').DataTable().ajax.reload(null, false);
                                $('#actionForm_grupos').html('')
                                break;
                            case 'd_grupos':
                                $('#tablegrupos').DataTable().ajax.reload(null, false);
                                $('#actionForm_grupos').html('')
                                break;
                            case 'c_grupos':
                                $('#tablegrupos').DataTable().ajax.reload(null, false);
                                $('#actionForm_grupos').html('')
                                break;
                            case 'u_sector':
                                $('#tablesector').DataTable().ajax.reload(null, false);
                                $('#actionForm_sector').html('')
                                break;
                            case 'd_sector':
                                $('#tablesector').DataTable().ajax.reload(null, false);
                                $('#actionForm_sector').html('')
                                break;
                            case 'c_sector':
                                $('#tablesector').DataTable().ajax.reload(null, false);
                                $('#actionForm_sector').html('')
                                break;
                            case 'u_tareas':
                                $('#tabletareas').DataTable().ajax.reload(null, false);
                                $('#actionForm_tareas').html('')
                                break;
                            case 'd_tareas':
                                $('#tabletareas').DataTable().ajax.reload(null, false);
                                $('#actionForm_tareas').html('')
                                break;
                            case 'c_tareas':
                                $('#tabletareas').DataTable().ajax.reload(null, false);
                                $('#actionForm_tareas').html('')
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
        case 'u_plantas':
            $('#cod').attr('readonly', true)
            $('#desc').attr('placeholder', 'Plantas')
            focusEndText('#desc')
            $('.opcional').remove()
            $('.requerido').hide()
            break;
        case 'd_plantas':
            $('#cod').attr('readonly', true)
            $('.opcional').remove()
            $('.requerido').remove()
            $('#desc').attr('readonly', true)
            break;
        case 'c_plantas':
            $('#desc').attr('placeholder', 'Planta')
            $('#desc').focus()
            break;
        case 'u_empresas':
            $('#cod').attr('readonly', true)
            $('#desc').attr('placeholder', 'Empresas')
            focusEndText('#desc')
            $('.opcional').remove()
            $('.requerido').hide()
            break;
        case 'd_empresas':
            $('#cod').attr('readonly', true)
            $('.opcional').remove()
            $('.requerido').remove()
            $('#desc').attr('readonly', true)
            break;
        case 'c_empresas':
            $('#desc').attr('placeholder', 'Razón Social')
            $('#desc').focus()
            break;
        case 'u_sucur':
            $('#cod').attr('readonly', true)
            $('#desc').attr('placeholder', 'Sucursales')
            focusEndText('#desc')
            $('.opcional').remove()
            $('.requerido').hide()
            break;
        case 'd_sucur':
            $('#cod').attr('readonly', true)
            $('.opcional').remove()
            $('.requerido').remove()
            $('#desc').attr('readonly', true)
            break;
        case 'c_sucur':
            $('#desc').attr('placeholder', 'Sucursal')
            $('#desc').focus()
            break;
        case 'u_grupos':
            $('#cod').attr('readonly', true)
            $('#desc').attr('placeholder', 'Grupos')
            focusEndText('#desc')
            $('.opcional').remove()
            $('.requerido').hide()
            break;
        case 'd_grupos':
            $('#cod').attr('readonly', true)
            $('.opcional').remove()
            $('.requerido').remove()
            $('#desc').attr('readonly', true)
            break;
        case 'c_grupos':
            $('#desc').attr('placeholder', 'Grupo')
            $('#desc').focus()
            break;
        case 'u_sector':
            $('#cod').attr('readonly', true)
            $('#desc').attr('placeholder', 'Sector')
            focusEndText('#desc')
            $('.opcional').remove()
            $('.requerido').hide()
            break;
        case 'd_sector':
            $('#cod').attr('readonly', true)
            $('.opcional').remove()
            $('.requerido').remove()
            $('#desc').attr('readonly', true)
            break;
        case 'c_sector':
            $('#desc').attr('placeholder', 'Sector')
            $('#desc').focus()
            break;
        case 'u_tareas':
            $('#cod').attr('readonly', true)
            $('#desc').attr('placeholder', 'Sector')
            focusEndText('#desc')
            $('.opcional').remove()
            $('.requerido').hide()
            break;
        case 'd_tareas':
            $('#cod').attr('readonly', true)
            $('.opcional').remove()
            $('.requerido').remove()
            $('#desc').attr('readonly', true)
            break;
        case 'c_tareas':
            $('#desc').attr('placeholder', 'Tarea')
            $('#desc').focus()
            break;
    }
    $("#cancelForm").on("click", function () {
        $('#actionForm_e').html('')
        $('#actionForm_suc').html('')
        $('#actionForm_grupos').html('')
        $('#actionForm_sector').html('')
        $('#actionForm_tareas').html('')
        $('#actionForm').html('')
    });
});