$("#trash_emp").on("click", function () {
    $('.selectjs_empresa').val(null).trigger("change");
    $('#SelEmpresa').val(null).trigger("change");
    $('#GetPersonal').DataTable().ajax.reload();
});
$("#trash_plan").on("click", function () {
    $('.selectjs_plantas').val(null).trigger("change");
    $('#SelPlanta').val(null).trigger("change");
    $('#GetPersonal').DataTable().ajax.reload();
});
$("#trash_sect").on("click", function () {
    $('.selectjs_sectores').val(null).trigger("change");
    $('.select_seccion').val(null).trigger("change");
    // $(".select_seccion").addClass("d-none");
    $(".select_seccion").prop("disabled", true);
    $('#SelSector').val(null).trigger("change");
    $('#SelSeccion').val(null).trigger("change");
    $('#GetPersonal').DataTable().ajax.reload();
});
$("#trash_secc").on("click", function () {
    $('.select_seccion').val(null).trigger("change");
    $('#SelSeccion').val(null).trigger("change");
    $('#GetPersonal').DataTable().ajax.reload();
});
$("#trash_grup").on("click", function () {
    $('.selectjs_grupos').val(null).trigger("change");
    $('#SelGrupo').val(null).trigger("change");
    $('#GetPersonal').DataTable().ajax.reload();
});
$("#trash_sucur").on("click", function () {
    $('.selectjs_sucursal').val(null).trigger("change");
    $('#SelSucursal').val(null).trigger("change");
    $('#GetPersonal').DataTable().ajax.reload();
});
$("#trash_per").on("click", function () {
    $('.selectjs_personal').val(null).trigger("change");
    $('#GetPersonal').DataTable().ajax.reload();
});
$("#trash_all").on("click", function () {
    $('#SelEmpresa').val(null).trigger("change");
    $('#SelPlanta').val(null).trigger("change");
    $('#SelSector').val(null).trigger("change");
    $('#SelSeccion').val(null).trigger("change");
    $('#SelGrupo').val(null).trigger("change");
    $('#SelSucursal').val(null).trigger("change");
    $('.selectjs_plantas').val(null).trigger("change");
    $('.selectjs_empresa').val(null).trigger("change");
    $('.selectjs_sectores').val(null).trigger("change");
    $('.select_seccion').val(null).trigger("change");
    $('.selectjs_grupos').val(null).trigger("change");
    $('.selectjs_sucursal').val(null).trigger("change");
    $('.selectjs_personal').val(null).trigger("change");
    $("#respuetatext").removeClass("animate__animated animate__fadeIn");
    $("#respuetatext").html("");
    $("#submit").prop("disabled", false);
    $("#submit").html("Ingresar Cierres");
    $("#respuesta").removeClass("alert-success");
    $("#respuesta").removeClass("alert-danger");
    $("#respuesta").removeClass("alert-info");
    $('input[type="checkbox"]').prop('checked', false);
    $('#GetPersonal').DataTable().ajax.reload();
});