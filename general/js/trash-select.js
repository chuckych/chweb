
 function TrashSelect(slectjs) {
    $(slectjs).val(null).trigger("change");
    ActualizaTablas()
}

function LimpiarFiltros() {
    $('.selectjs_plantas').val(null).trigger("change");
    $('#datoNovedad').val(null).trigger("change");
    $('.selectjs_empresa').val(null).trigger("change");
    $('.selectjs_sectores').val(null).trigger("change");
    $('.select_seccion').val(null).trigger("change");
    $(".select_seccion").prop("disabled", true);
    $('.selectjs_grupos').val(null).trigger("change");
    $('.selectjs_sucursal').val(null).trigger("change");
    $('.selectjs_personal').val(null).trigger("change");
    $('.selectjs_tipoper').val(null).trigger("change");
    $('#Per2').val(null)
    $('#FicDiaL').prop('checked', false)
    $('#datoFicFalta').val('0')
    $('#FicFalta').prop('checked', false)
    $('#datoFicNovT').val('0')
    $('#FicNovT').prop('checked', false)
    $('#datoFicNovI').val('0')
    $('#FicNovI').prop('checked', false)
    $('#datoFicNovS').val('0')
    $('#FicNovS').prop('checked', false)
    $('#datoFicNovA').val('0')
    $('#SoloFic').prop('checked', false)
    $('#SoloHCalc').prop('checked', true)
    $('#LegDe').val('').val(null).trigger("change");
    $('#LegHa').val('').val(null).trigger("change");
    _Filtros()
    $('#FicNovA').prop('checked', false)
    $('#FicNovA').prop('disabled', false)
    $('#FicFalta').prop('disabled', false)
    $('#FicNovT').prop('disabled', false)
    $('#FicNovI').prop('disabled', false)
    $('#FicNovS').prop('disabled', false)
}
function LimpiarFiltros2() {
    $('#datoNovedad').val(null).trigger("change");
    $('.selectjs_plantas').val(null).trigger("change");
    $('.selectjs_empresa').val(null).trigger("change");
    $('.selectjs_sectores').val(null).trigger("change");
    $('.select_seccion').val(null).trigger("change");
    $(".select_seccion").prop("disabled", true);
    $('.selectjs_grupos').val(null).trigger("change");
    $('.selectjs_sucursal').val(null).trigger("change");
    $('.selectjs_personal').val(null).trigger("change");
    $('.selectjs_tipoper').val(null).trigger("change");
    $('#FicDiaL').prop('checked', false)
    $('#datoFicFalta').val('0')
    $('#FicFalta').prop('checked', false)
    $('#datoFicNovT').val('0')
    $('#FicNovT').prop('checked', false)
    $('#datoFicNovI').val('0')
    $('#FicNovI').prop('checked', false)
    $('#datoFicNovS').val('0')
    $('#FicNovS').prop('checked', false)
    $('#SoloFic').prop('checked', false)
    $('#SoloHCalc').prop('checked', true)
    $('#datoFicNovA').val('0')
    $('#LegDe').val('').val(null).trigger("change");
    $('#LegHa').val('').val(null).trigger("change");
    _Filtros()
    $('#FicNovA').prop('checked', false)
    $('#FicNovA').prop('disabled', false)
    $('#FicFalta').prop('disabled', false)
    $('#FicNovT').prop('disabled', false)
    $('#FicNovI').prop('disabled', false)
    $('#FicNovS').prop('disabled', false)
}
$("#trash_all").on("click", function () {
    $('#Filtros').modal('show')
    LimpiarFiltros()
    $('#Filtros').modal('hide')
    ActualizaTablas2()
});
$("#trashDeHa").on("click", function () {
    $('#LegDe').val('')
    $('#LegDe').val(null).trigger("change");
    $('#LegHa').val('')
    $('#LegHa').val(null).trigger("change");
    $('.selectjs_personal').val(null).trigger("change");
    ActualizaTablas2()
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
        ActualizaTablas2()
        GetPersonal.on( 'xhr', function () {
            var json = GetPersonal.ajax.json();
            // $("#tableInfo").html(json.recordsTotal)
            if (json.recordsTotal) {
                $('#GetGeneralTable').removeClass('d-none')
            }else{
                $('#GetGeneralTable').addClass('d-none')
            }
        });
        // $('#Per2').val(null)
    });
};  
refreshOnChange("#Per2");