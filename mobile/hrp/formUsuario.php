<form action="" method="post" class="border px-3 pb-3 shadow-sm mt-1" id="formUsuario">
    <div class="row">
        <div class="col-12 py-2">
            <span class="fw4 fonth" id="titleForm">Nuevo usuario</span>
        </div>
        <div class="col-12 py-2 col-sm-6" id="divid_user">
            <label for="id_user">ID</label>
            <input class="form-control h40" type="tel" name="id_user" id="id_user" placeholder="ID de Usuario">
            <input class="" type="hidden" readonly name="tipo" id="tipo" value="c_usuario">
        </div>
        <div class="col-12 py-2 col-sm-6">
            <label for="nombre">Nombre</label>
            <input class="form-control h40" type="text" name="nombre" id="nombre" placeholder="Nombre y Apellido">
        </div>
        <div class="col-12 pb-2">
            <label for="regid">Reg ID</label>
            <textarea class="form-control p-3" name="regid" id="regid" style="height: 100px !important;" placeholder="Regid proporcionado desde la App"></textarea>
            <!-- <input class="form-control h40" type="text" name="regid" id="regid" placeholder="Regid proporcionado desde la App"> -->
        </div>
        <div class="col-12 pt-3">
            <div class="float-right">
                <button type="button" class="btn btn-light border-0 btn-sm fontq h35 btn-mobile text-secondary" id="CancelarFormUsuario">Cancelar</button>
                <button type="submit" class="btn btn-sm btn-custom fontq h35 btn-mobile">Aceptar</button>
            </div>
        </div>
    </div>
</form>
<?php
$_POST['value']  = $_POST['value'] ?? '';
$_POST['action'] = $_POST['action'] ?? '';
if ($_POST['value']) :

    switch ($_POST['action']) {
        case 'update':
            $tipo = 'u_usuario';
            $titleForm = 'Editar Usuario';
            $disableNombre = 'false';
            $disableregid = 'false';
            break;
        case 'delete':
            $tipo = 'd_usuario';
            $titleForm = 'Eliminar Usuario';
            $disableNombre = 'true';
            $disableregid = 'true';
            break;
    }

?>
    <script>
        $('#titleForm').html('<?= $titleForm ?>')
        $('#tipo').val('<?= $tipo ?>')
        $('#id_user').attr('readonly', true)
        $('#id_user').attr('hidden', true)
        $('#nombre').prop('disabled', <?= $disableNombre ?>)
        $('#regid').prop('disabled', <?= $disableregid ?>)
        $('#divid_user').hide()
        // $('#id_user').attr('hidden')
        $.ajax({
            type: 'post',
            url: 'crud.php',
            data: $(this).serialize() + '&tipo=r_usuario&id_user=<?= $_POST['value'] ?>',
            dataType: "json",
            beforeSend: function(data) {},
            success: function(data) {
                $('#titleForm').append(': ' + data.id_user)
                $('#id_user').val(data.id_user),
                    $('#nombre').val(data.nombre),
                    $('#regid').val(data.regid)
            },
            error: function() {}
        });
    </script>
<?php
endif
?>
<script>
    $(document).on("click", "#CancelarFormUsuario", function(e) {
        $('#divformUsuario').html('')
    });
    $('#id_user').mask('0000000000');
    $('#id_user').focus()
    $("#formUsuario").bind("submit", function(e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: 'crud.php',
            data: $(this).serialize(),
            // dataType: "json",
            beforeSend: function(data) {
                CheckSesion()
                $.notifyClose();
                notify('Aguarde..', 'info', 0, 'right')
            },
            success: function(data) {
                if (data.status == "ok") {
                    $.notifyClose();
                    notify(data.Mensaje, 'success', 5000, 'right')
                    $('#tableUsuarios').DataTable().ajax.reload();
                    $('#divformUsuario').html('')
                } else {
                    $.notifyClose();
                    notify(data.Mensaje, 'danger', 5000, 'right')
                }
            },
            error: function() {}
        });
    });
</script>