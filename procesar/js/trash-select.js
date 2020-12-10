$("#trash_emp").on("click", function () {
    $('.selectjs_empresa').val(null).trigger("change");
    $('#SelEmpresa').val(null).trigger("change");
});
$("#trash_plan").on("click", function () {
    $('.selectjs_plantas').val(null).trigger("change");
    $('#SelPlanta').val(null).trigger("change");
});
$("#trash_sect").on("click", function () {
    $('.selectjs_sectores').val(null).trigger("change");
    $('.select_seccion').val(null).trigger("change");
    $("#select_seccion").addClass("d-none");
    $('#SelSector').val(null).trigger("change");
    $('#SelSeccion').val(null).trigger("change");
});
$("#trash_secc").on("click", function () {
    $('.select_seccion').val(null).trigger("change");
    $('#SelSeccion').val(null).trigger("change");
});
$("#trash_grup").on("click", function () {
    $('.selectjs_grupos').val(null).trigger("change");
    $('#SelGrupo').val(null).trigger("change");
});
$("#trash_sucur").on("click", function () {
    $('.selectjs_sucursal').val(null).trigger("change");
    $('#SelSucursal').val(null).trigger("change");
});


