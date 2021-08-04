$(document).ready(function () {

    $('.select2').select2({
        minimumResultsForSearch: -1,
        placeholder: "Seleccionar"
    });
    // var DRVal = $("#_dr").val();
    $('#RangoDr').html($("#_dr").val())
    $("#_dr").change(function () {
        $('#RangoDr').html($("#_dr").val())
    });

    $(document).on("click", "#FiltroReporte", function (e) {
        CheckSesion()
        $(document).off('keydown');
        e.preventDefault();
        $('#Exportar').modal('hide');
        $('#Filtros').modal('show');

    });
    $(document).on("click", "#ReporteFiltro", function (e) {
        $(document).off('keydown');
        e.preventDefault();
        $('#Filtros').modal('hide');
        $('#Exportar').modal('show');
    });
    $('#Exportar').on('shown.bs.modal', function () {
        CheckSesion()
        $('#_titulo').select();
        $(document).off('keydown');
    });
    $('#Exportar').on('hidden.bs.modal', function () {
        // $('#IFrame').addClass('d-none');
    });
    $('#datoSaltoPag').val('0');
    $("#SaltoPag").change(function () {
        if ($("#SaltoPag").is(":checked")) {
            $('#datoSaltoPag').val('1')
        } else {
            $('#datoSaltoPag').val('0')
        }
    });

    $('#datoTotHoras').val('1');
    $('#TotHoras').prop('checked', true);
    $("#TotHoras").change(function () {
        if ($("#TotHoras").is(":checked")) {
            $('#datoTotHoras').val('1')
        } else {
            $('#datoTotHoras').val('0')
        }
    });

    $('#datoTotNove').val('1');
    $('#TotNove').prop('checked', true);
    $("#TotNove").change(function () {
        if ($("#TotNove").is(":checked")) {
            $('#datoTotNove').val('1')
        } else {
            $('#datoTotNove').val('0')
        }
    });

    $('#datoVerHoras').val('1');
    $('#VerHoras').prop('checked', true);
    $("#VerHoras").change(function () {
        if ($("#VerHoras").is(":checked")) {
            $('#datoTotHoras').val('1');
            $('#TotHoras').prop('checked', true);
            $('#datoVerHoras').val('1')
        } else {
            $('#datoTotHoras').val('0');
            $('#TotHoras').prop('checked', false);
            $('#datoVerHoras').val('0')
        }
    });

    $('#datoVerNove').val('1');
    $('#VerNove').prop('checked', true);
    $("#VerNove").change(function () {
        if ($("#VerNove").is(":checked")) {
            $('#datoTotNove').val('1');
            $('#TotNove').prop('checked', true);
            $('#datoVerNove').val('1')
        } else {
            $('#datoTotNove').val('0');
            $('#TotNove').prop('checked', false);
            $('#datoVerNove').val('0')
        }
    });

    $('#datoVerFic').val('1');
    $('#VerFic').prop('checked', true);
    $("#VerFic").change(function () {
        if ($("#VerFic").is(":checked")) {
            $('#datoVerFic').val('1')
        } else {
            $('#datoVerFic').val('0')
        }
    });

    function uncheckNovedades() {

        $('#datoFicNovA').val('0')
        $('#datoFicFalta').val('0')
        $('#datoFicNovT').val('0')
        $('#datoFicNovI').val('0')
        $('#datoFicNovS').val('0')

        $('#FicNovI').prop('checked', false)
        $('#FicFalta').prop('checked', false)
        $('#FicNovT').prop('checked', false)
        $('#FicNovS').prop('checked', false)
        $('#FicNovA').prop('checked', false)

        $('#FicNovA').prop('disabled', false)
        $('#FicFalta').prop('disabled', false)
        $('#FicNovT').prop('disabled', false)
        $('#FicNovI').prop('disabled', false)
        $('#FicNovS').prop('disabled', false)

    }
    function hideNov() {
        $('#datoTotNove').val('0');
        $('#TotNove').prop('checked', false);
        $('#VerNove').prop('checked', false);
        $('#datoVerNove').val('0')
    }
    function showNov() {
        $('#datoTotNove').val('1');
        $('#TotNove').prop('checked', true);
        $('#VerNove').prop('checked', true);
        $('#datoVerNove').val('1')
    }
    function hideHor() {
        $('#datoTotHoras').val('0');
        $('#TotHoras').prop('checked', false);
        $('#VerHoras').prop('checked', false);
        $('#datoVerHoras').val('0')
    }
    function showHor() {
        $('#datoTotHoras').val('1');
        $('#TotHoras').prop('checked', true);
        $('#VerHoras').prop('checked', true);
        $('#datoVerHoras').val('1')
    }
    function hideFic() {
        $('#datoVerFic').val('0');
        $('#VerFic').prop('checked', false);
    }
    function showFic() {
        $('#datoVerFic').val('1');
        $('#VerFic').prop('checked', true);
    }

    $('#_plantilla').on('select2:select', function (e) {
        if ($('#_plantilla').val() == 'p_fic') {
            $('#_titulo').val('Reporte de Fichadas (Entrada y Salida)')
            uncheckNovedades()
            /** Mostramos Fichadas */
            showFic()
            /** Ocultamos Novedades */
            hideNov()
            /** Ocultamos Horas */
            hideHor()
        } else if ($('#_plantilla').val() == 'p_nov') {
            uncheckNovedades()
            $('#_titulo').val('Reporte de Novedades')
            /** Mostramos Novedades */
            showNov()
            /** Ocultamos Fichadas */
            hideFic()
            /** Ocultamos Horas */
            hideHor()
        } else if ($('#_plantilla').val() == 'p_hor') {
            uncheckNovedades()
            $('#_titulo').val('Reporte de Horas')
            /** Mostramos Horas */
            showHor()
            /** Ocultamos Fichadas */
            hideFic()
            /** Ocultamos Novedaes */
            hideNov()
        } else if ($('#_plantilla').val() == 'p_tar') {
            uncheckNovedades()
            $('#_titulo').val('Reporte de Tardes')
            $('#datoFicNovT').val('1')
            $('#FicNovT').prop('checked', true)
            $('#FicNovA').prop('disabled', true)
            /** Mostramos Novedades */
            showNov()
            /** Ocultamos Fichadas */
            hideFic()
            /** Ocultamos Horas */
            hideHor()
        } else if ($('#_plantilla').val() == 'p_aus') {
            uncheckNovedades()
            $('#_titulo').val('Reporte de Ausencias')
            $('#datoFicNovA').val('1')
            $('#FicNovA').prop('checked', true)
            $('#FicFalta').prop('disabled', true)
            $('#FicNovT').prop('disabled', true)
            $('#FicNovI').prop('disabled', true)
            $('#FicNovS').prop('disabled', true)
            /** Mostramos Novedades */
            showNov()
            /** Ocultamos Fichadas */
            hideFic()
            /** Ocultamos Horas */
            hideHor()
        } else if ($('#_plantilla').val() == 'p_sal') {
            uncheckNovedades()
            $('#_titulo').val('Reporte de Salidas Anticipadas')
            $('#datoFicNovS').val('1')
            $('#FicNovS').prop('checked', true)
            $('#FicNovA').prop('disabled', true)
            /** Mostramos Novedades */
            showNov()
            /** Ocultamos Fichadas */
            hideFic()
            /** Ocultamos Horas */
            hideHor()
        } else if ($('#_plantilla').val() == 'p_inc') {
            uncheckNovedades()
            $('#_titulo').val('Reporte de Incumplimientos')
            $('#datoFicNovI').val('1')
            $('#FicNovI').prop('checked', true)
            $('#FicNovA').prop('disabled', true)
            /** Mostramos Novedades */
            showNov()
            /** Ocultamos Fichadas */
            hideFic()
            /** Ocultamos Horas */
            hideHor()
        }
    });
    var btnPDF = 'Generar PDF'
    $("#btnExportar").html(btnPDF);

    $("#FormExportar").bind("submit", function (e) {
        CheckSesion()
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize() +
                "&_l= " + $("#_l").val() +
                "&Per= " + $("#Per").val() +
                "&Tipo= " + $("#Tipo").val() +
                "&Emp= " + $("#Emp").val() +
                "&Plan= " + $("#Plan").val() +
                "&Sect= " + $("#Sect").val() +
                "&Sec2= " + $("#Sec2").val() +
                "&Grup= " + $("#Grup").val() +
                "&Sucur= " + $("#Sucur").val() +
                "&_dr= " + $("#_dr").val() +
                "&FicDiaL= " + $("#datoFicDiaL").val() +
                "&FicFalta= " + $("#datoFicFalta").val() +
                "&FicNovT= " + $("#datoFicNovT").val() +
                "&FicNovI= " + $("#datoFicNovI").val() +
                "&FicNovS= " + $("#datoFicNovS").val() +
                "&FicNovA= " + $("#datoFicNovA").val(),
            dataType: "json",
            beforeSend: function (data) {
                ActiveBTN(true, "#btnExportar", 'Generando.!', btnPDF)
                $('#IFrame').addClass('d-none');
                $('#Permisos').collapse('hide')
                $.notifyClose();
                let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
                notify('Generando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
            },
            success: function (data) {
                if (data.status == "ok") {
                    ActiveBTN(false, "#btnExportar", 'Generando.!', btnPDF)
                    if (data.destino == "V") {
                        var homehost = $("#_homehost").val();
                        var host = $("#_host").val();
                        window.open(host + '/' + homehost + '/general/reporte/archivos/' + data.archivo, '_blank');
                        $.notifyClose();
                        notify('Reporte Generado', 'success', 2000, 'right')
                        ActiveBTN(false, "#btnExportar", 'Generando.!', btnPDF)
                    } else {
                        $('#IFrame').removeClass('d-none');
                        $('#IFrame').html('<div class="col-12 pt-2"><iframe id="IframeID" src="reporte/archivos/' + `${data.archivo}` + '" width="100%" height="600" style="border:none;"></iframe></div>');
                        ActiveBTN(false, "#btnExportar", 'Generando.!', btnPDF)
                        $.notifyClose();
                        notify('Reporte Generado', 'success', 2000, 'right')
                    }
                } else {
                    ActiveBTN(false, "#btnExportar", 'Generando.!', btnPDF)
                    $.notifyClose();
                    notify('Error', 'danger', 5000, 'right')
                }
            },
            error: function () {
                $.notifyClose();
                notify('Error', 'danger', 5000, 'right')
                ActiveBTN(false, "#btnExportar", 'Generando.!', btnPDF)
            }
        });
        // e.stopImmediatePropagation();
    });
});
