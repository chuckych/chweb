$("#trash_emp").on("click", function () {
    $('.selectjs_empresas').val(null).trigger("change");
});
$("#trash_plan").on("click", function () {
    $('.selectjs_plantas').val(null).trigger("change");
});
$("#trash_conv").on("click", function () {
    $('.selectjs_convenio').val(null).trigger("change");
});
$("#trash_sect").on("click", function () {
    $('.selectjs_sectores').val(null).trigger("change");
    $('.selectjs_secciones').val(null).trigger("change");
    $("#select_seccion").addClass("d-none");
});
$("#trash_secc").on("click", function () {
    $('.selectjs_secciones').val(null).trigger("change");
});
$("#trash_grup").on("click", function () {
    $('.selectjs_grupos').val(null).trigger("change");
});
$("#trash_sucur").on("click", function () {
    $('.selectjs_sucursal').val(null).trigger("change");
});
$("#trash_tar").on("click", function () {
    $('.selectjs_tarea').val(null).trigger("change");
});
$("#trash_prov").on("click", function () {
    $('.selectjs_provincias').val(null).trigger("change");
});
$("#trash_loca").on("click", function () {
    $('.selectjs_localidad').val(null).trigger("change");
});
$("#trash_provEmp").on("click", function () {
    $('.selectjs_provinciasEmp').val(null).trigger("change");
});
$("#trash_locaEmp").on("click", function () {
    $('.selectjs_localidadEmp').val(null).trigger("change");
});
$("#trash_nacion").on("click", function () {
    $('.selectjs_naciones').val(null).trigger("change");
});
$("#trash_premio").on("click", function () {
    $('.selectjs_premios').val(null).trigger("change");
});
$("#trash_LegRegCH").on("click", function () {
    $('.selectjs_regla').val(null).trigger("change");
});
$("#trash_CierreFech").on("click", function () {
    $('#CierreFech').val(null).trigger("change");
});
$("#trash_LegFeEg").on("click", function () {
    $('#LegFeEg').val(null).trigger("change");
});
$("#trash_LegFeIn").on("click", function () {
    $('#LegFeIn').val(null).trigger("change");
});
$("#trash_LegFeNa").on("click", function () {
    $('#LegFeNa').val(null).trigger("change");
    $('#result').html('')
});
$("#trash_IDVence").on("click", function () {
    $('#IDVence').val('');
});
$("#trash_InEgFeEg").on("click", function () {
    $('#InEgFeEg').val('');
});
$("#trash_InEgFeIn").on("click", function () {
    $('#InEgFeIn').val('');
});
