$("#trash_emp").on("click", function () {
    $('.selectjs_empresa').val(null).trigger("change");
    ActualizaTablas()
});
$("#trash_plan").on("click", function () {
    $('.selectjs_plantas').val(null).trigger("change");
    ActualizaTablas()
});
$("#trash_sect").on("click", function () {
    $('.selectjs_sectores').val(null).trigger("change");
    $('.select_seccion').val(null).trigger("change");
    $(".select_seccion").prop("disabled", true);
    ActualizaTablas()
});
$("#trash_secc").on("click", function () {
    $('.select_seccion').val(null).trigger("change");
    ActualizaTablas()
});
$("#trash_grup").on("click", function () {
    $('.selectjs_grupos').val(null).trigger("change");
    ActualizaTablas()
});
$("#trash_sucur").on("click", function () {
    $('.selectjs_sucursal').val(null).trigger("change");
    ActualizaTablas()
});
$("#trash_per").on("click", function () {
    $('.selectjs_personal').val(null).trigger("change");
    ActualizaTablas()
});
function LimpiarFiltros() {
    $('.selectjs_plantas').val(null).trigger("change");
    $('.selectjs_empresa').val(null).trigger("change");
    $('.selectjs_sectores').val(null).trigger("change");
    $('.select_seccion').val(null).trigger("change");
    $(".select_seccion").prop("disabled", true);
    $('.selectjs_grupos').val(null).trigger("change");
    $('.selectjs_sucursal').val(null).trigger("change");
    $('.selectjs_personal').val(null).trigger("change");
    $('.selectjs_thora').val(null).trigger("change");
    $('.selectjs_tipoper').val(null).trigger("change");
}
$("#trash_all").on("click", function () {
    $('#Filtros').modal('show')
    LimpiarFiltros()
    $('#Filtros').modal('hide')
    ActualizaTablas()
});
$("#trash_allIn").on("click", function () {
    LimpiarFiltros()
    ActualizaTablas()
});