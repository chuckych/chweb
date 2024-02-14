$("#trash_emp").on("click", function () {
    CheckSesion()
    $('.selectjs_empresa').val(null).trigger("change");
    ActualizaTablas()
});
$("#trash_plan").on("click", function () {
    CheckSesion()
    $('.selectjs_plantas').val(null).trigger("change");
    ActualizaTablas()
});
$("#trash_sect").on("click", function () {
    CheckSesion()
    $('.selectjs_sectores').val(null).trigger("change");
    $('.select_seccion').val(null).trigger("change");
    $(".select_seccion").prop("disabled", true);
    ActualizaTablas()
});
$("#trash_secc").on("click", function () {
    CheckSesion()
    $('.select_seccion').val(null).trigger("change");
    ActualizaTablas()
});
$("#trash_grup").on("click", function () {
    CheckSesion()
    $('.selectjs_grupos').val(null).trigger("change");
    ActualizaTablas()
});
$("#trash_sucur").on("click", function () {
    CheckSesion()
    $('.selectjs_sucursal').val(null).trigger("change");
    ActualizaTablas()
});
$("#trash_per").on("click", function () {
    CheckSesion()
    $('.selectjs_personal').val(null).trigger("change");
    ActualizaTablas()
});
const cleanLs = () => {
    ls.remove(LS_FILTROS + '.selectjs_plantas')
    ls.remove(LS_FILTROS + '.selectjs_empresa')
    ls.remove(LS_FILTROS + '.selectjs_sectores')
    ls.remove(LS_FILTROS + '.select_seccion')
    ls.remove(LS_FILTROS + '.selectjs_grupos')
    ls.remove(LS_FILTROS + '.selectjs_sucursal')
    ls.remove(LS_FILTROS + '.selectjs_personal')
    ls.remove(LS_FILTROS + '.selectjs_thora')
    ls.remove(LS_FILTROS + '.selectjs_tipoper')
    ls.remove(LS_FILTROS + '#HoraMin')
    ls.remove(LS_FILTROS + '#HoraMax')
    ls.remove(LS_FILTROS + '#SHoras')
}
cleanLs();
function LimpiarFiltros() {
    CheckSesion()
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
    $('#HoraMin').val('00:00');
    $('#HoraMax').val('23:59');
    $('#Autori').removeClass('focus active');
    $('#Hechas').addClass('focus active');
    $('#SHoras').val('1');
    $('#Calculos').prop('checked', false);
    $('#Calculos').val(null);
    cleanLs();
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