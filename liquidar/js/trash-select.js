$("#trash_anio").on("click", function () {
    $('.selectjs_anio').val(null).trigger("change");
    $('.selectjs_mes').val(null).trigger("change");
    $(".selectjs_mes").prop("disabled", true);
});
$("#trash_mes").on("click", function () {
    $('.selectjs_mes').val(null).trigger("change");
});
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
    $(".select_seccion").prop("disabled", true);
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
$("#trash_per").on("click", function () {
    $('.selectjs_personal').val(null).trigger("change");
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
    $('#LegaIni').val('1')
    $('#LegaFin').val('999999999')
    ActiveBTN(false, "#submit", '', 'Generar');
});
