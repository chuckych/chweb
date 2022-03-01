<form action="" method="post" class="border px-3 pb-3 shadow-sm mt-1" id="formMensaje">
    <div class="row">
        <div class="col-12 pb-2 py-2">
            <span class="fw4 fonth" id="titleForm">Enviar Mensaje a <span id="nombre"></span></span>
        </div>
        <div class="col-12 pb-2">
            <input class="" type="hidden" readonly name="tipo" id="tipo" value="c_mensaje">
            <input type="hidden" name="regid" id="regid" value="">
            <textarea class="form-control p-3" name="mensaje" id="mensaje" style="height: 85px !important;" placeholder="Mensaje"></textarea>
        </div>
        <?php
        $_POST['value']  = $_POST['value'] ?? '';
        $_POST['action'] = $_POST['action'] ?? '';
        if ($_POST['value']) :
            
            $explodeValue = explode('@', $_POST['value']);
            $regid = $explodeValue[0];
            $nombre = $explodeValue[1];
        ?>
            <div class="col-12 pt-3">
                <div class="float-right">
                    <button type="button" class="btn btn-light border-0 btn-sm fontq h35 btn-mobile text-secondary" id="CancelarFormMensaje">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-custom fontq h35 btn-mobile">Aceptar</button>
                </div>
            </div>
        <?php
        endif
        ?>
    </div>
</form>

<script>
    $('#regid').attr('readonly', true)
    // $('#regid').attr('hidden', true)
    $('#regid').val('<?= ($regid) ?>')
    $('#nombre').html('<?= ($nombre) ?>')

    $(document).on("click", "#CancelarFormMensaje", function(e) {
        $('#divformUsuario').html('')
    });

    $('#mensaje').focus()

    $("#formMensaje").bind("submit", function(e) {
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
                    // $('#tableMensajes').DataTable().ajax.reload();
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