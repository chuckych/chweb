$(function () {
    'use strict'
    onOpenSelect2();
    if ($('#cuentaSKF').val() == 1) { $('#contenido').remove(); }
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
        $('#Per2').val(null)
        $('#datoFicFalta').val('0')
        $('#FicFalta').prop('checked', false)
        $('#datoPorLegajo').val('1');
        CheckedInput('#PorLegajo')
    }

    $("#trash_allIn").on("click", function () {
        LimpiarFiltros()
    });

    $('#datoFicFalta').val('0');
    $('#FicFalta').prop('checked', false)
    $("#FicFalta").change(function () {
        if ($("#FicFalta").is(":checked")) {
            $('#datoFicFalta').val('1')
        } else {
            $('#datoFicFalta').val('0')
        }
    });

    $('#datoSaltoPag').val('0');
    $("#SaltoPag").change(function () {
        if ($("#SaltoPag").is(":checked")) {
            $('#datoSaltoPag').val('1')
        } else {
            $('#datoSaltoPag').val('0')
        }
    });

    $('#datoPorLegajo').val('1');
    CheckedInput('#PorLegajo')

    $('#RangoDr').html($("#_dr").val())
    $("#_dr").change(function () {
        $('#RangoDr').html($("#_dr").val())
    });

    function GetFicExcel(data) {
        $.ajax({
            type: 'POST',
            dataType: "json",
            // url: "FicCsv.php",
            url: "inforExcel.php",
            'data': {
                datos: data
            },
            beforeSend: function () {
                ActiveBTN(true, "#btnExportar", 'Exportando', IconExcel)
            },
            success: function (data) {
                if (data.status == "ok") {
                    ActiveBTN(false, "#btnExportar", 'Exportando', IconExcel)
                    window.location = data.archivo
                }

            },
            error: function () {
                ActiveBTN(false, "#btnExportar", 'Exportando', IconExcel)
            }
        });
    }
    var IconExcel = 'Exportar .xls <img src="../../img/xls.png" class="w15" alt="Exportar Excel">'
    ActiveBTN(false, "#btnExportar", 'Exportando', IconExcel)

    // $(document).on("click", "#btnExportar", function (e) {
    $("#btnExportar").on("click", function (e) {
        CheckSesion()
        e.preventDefault();
        let data = {
            "empresa": $('.selectjs_empresa').val(),
            "planta": $('.selectjs_plantas').val(),
            "sector": $('.selectjs_sectores').val(),
            "seccion": $('.select_seccion').val(),
            "grupo": $('.selectjs_grupos').val(),
            "sucursal": $('.selectjs_sucursal').val(),
            "personal": $('.selectjs_personal').val(),
            "tipoper": $('.selectjs_tipoper').val(),
            "_dr": $('#_dr').val(),
            "agrup": $('input[name=agrup]:checked').val()
        }
        GetFicExcel(JSON.stringify(data))
        e.stopImmediatePropagation()
    });
});
