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
        $.notifyClose();
        $('#NuevoLeg').prop('disabled', true)
      },
      success: function (data) {
        // console.log(data.status);
        if (data.status == 'ok') {
          $.notifyClose();
          notify(data.Mensaje, 'success', 5000, 'right')
          $('#NuevoLeg').prop('disabled', false)
          window.location.href = `legajo/?_leg=${data.Legajo}`;
        } else {
          $.notifyClose();
          $('#NuevoLeg').prop('disabled', false)
          notify(data.Mensaje, 'danger', 5000, 'right')
          //   window.location.reload(true); 
        }
      }
    });
  });
});