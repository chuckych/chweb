
 function TrashSelect(slectjs) {
    $(slectjs).val(null).trigger("change");
    ActualizaTablas()
}

function LimpiarFiltros() {
    $('.selectjs_plantas').val(null).trigger("change");
    $('.selectjs_empresa').val(null).trigger("change");
    $('.selectjs_sectores').val(null).trigger("change");
    $('.select_seccion').val(null).trigger("change");
    $(".select_seccion").prop("disabled", true);
    $('.selectjs_grupos').val(null).trigger("change");
    $('.selectjs_sucursal').val(null).trigger("change");
    $('.selectjs_personal').val(null).trigger("change");
    $('.selectjs_tipoper').val(null).trigger("change");
    $('.selectjs_FicONove').val(null).trigger("change");
    $('#Per2').val(null)
}
function LimpiarFiltros2() {
    $('.selectjs_plantas').val(null).trigger("change");
    $('.selectjs_empresa').val(null).trigger("change");
    $('.selectjs_sectores').val(null).trigger("change");
    $('.select_seccion').val(null).trigger("change");
    $(".select_seccion").prop("disabled", true);
    $('.selectjs_grupos').val(null).trigger("change");
    $('.selectjs_sucursal').val(null).trigger("change");
    $('.selectjs_personal').val(null).trigger("change");
    $('.selectjs_tipoper').val(null).trigger("change");
    $('.selectjs_FicONove').val(null).trigger("change");
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

$(document).on("click", ".numlega", function (e) {
    $('#Per2').val(null)
    $('.pers_legajo').addClass('d-none')
    $('#Per2').removeClass('d-none')
    $('#Per2').focus();
});

function refreshOnChange(selector) {
    $(selector).change(function () {
        $('#Filtros').modal('show')
        LimpiarFiltros2()
        $('#Filtros').modal('hide')
        ActualizaTablas()
        GetPersonal.on( 'xhr', function () {
            var json = GetPersonal.ajax.json();
            // $("#tableInfo").html(json.recordsTotal)
            if (json.recordsTotal) {
                $('#GetNovedadesTable').removeClass('d-none')
            }else{
                $('#GetNovedadesTable').addClass('d-none')
            }
        });
        // $('#Per2').val(null)
    });
};  
refreshOnChange("#Per2");