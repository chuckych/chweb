$(document).on('shown.bs.modal', '#altaNuevoLeg', function () {
  CheckSesion()
  $(this).find('[autofocus]').focus();
});
$(document).ready(function () {
  $(".form-NuevoLeg").bind("submit", function (event) {
    event.preventDefault();
    $.ajax({
      type: $(this).attr("method"),
      contetnType: "application_json; charset=utf-8",
      url: $(this).attr("action"),
      data: $(this).serialize(),
      beforeSend: function (data) {
        $("#alerta_AltaLega").addClass("d-none")
        $.notifyClose();
      },
      success: function (data) {
        // console.log(data.status);
        if (data.status == 'ok') {
          $.notifyClose();
          notify(data.Mensaje, 'success', 5000, 'right')
          $("#alerta_AltaLega").removeClass("d-none").removeClass("d-none").removeClass("text-danger").addClass("text-success")
          $(".respuesta_AltaLega").html("Legajo Creado!")
          $(".mensaje_AltaLega").html('');
          window.location.href = `legajo/?_leg=${data.Legajo}`;
        } else {
          $.notifyClose();
          notify(data.Mensaje, 'danger', 5000, 'right')
          $("#alerta_AltaLega").removeClass("d-none").removeClass("text-success").addClass("text-danger")
          $(".respuesta_AltaLega").html("¡Error!")
          $(".mensaje_AltaLega").html(`${data.Mensaje}`);
          //   window.location.reload(true); 
        }
      }
    });
  });
});