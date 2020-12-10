$(document).ready(function () {
/** Variables para las notificaciones de pantalla */
var NotifDelay     = 2000;
var NotifOffset    = 0;
var NotifOffsetX   = 0;
var NotifOffsetY   = 0;
var NotifZindex    = 9999;
var NotifMouseOver = 'pause'
var NotifEnter     = 'animate__animated animate__fadeInDown';
var NotifExit      = 'animate__animated animate__fadeOutUp';
var NotifAlign     = 'center';
var btnPDF = 'Generar PDF'

$("#btnExportar").html(btnPDF);
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

    $("#FormExportar").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize() +
                // "&_l= " + $("#_l").val() +
                "&Per= " + $("#Per").val() +
                "&Tipo= " + $("#Tipo").val() +
                "&Emp= " + $("#Emp").val() +
                "&Plan= " + $("#Plan").val() +
                "&Sect= " + $("#Sect").val() +
                "&Sec2= " + $("#Sec2").val() +
                "&Grup= " + $("#Grup").val() +
                "&Sucur= " + $("#Sucur").val() +
                // "&FicNove= " + $("#FicNove").val() +
                "&_dr= " + $("#_dr").val() +
                // "&_agrupar= " + $("#_agrupar").val() +
                // "&_resaltar= " + $("#_resaltar").val() +
                "&FicFalta= " + $("#datoFicFalta").val(),
            dataType: "json",
            beforeSend: function (data) {
                $("#btnExportar").html("Generando.!");
                $("#btnExportar").prop("disabled", true);
                $('#IFrame').addClass('d-none');
                $('#Permisos').collapse('hide')
                $('#rowFiltros').collapse('hide')
            },
            success: function (data) {
                if (data.status == "ok") {
                    $("#btnExportar").prop("disabled", false);
                    $("#btnExportar").html(btnPDF);
                    if (data.destino == "V") {
                        var homehost = $("#_homehost").val();
                        var host = $("#_host").val();
                        
                        window.open(host + '/' + homehost + '/informes/inforfic/reporte/archivos/' + data.archivo, '_blank');
                    } else {
                        $('#IFrame').removeClass('d-none');
                        $('#IFrame').html('<div class="col-12 pt-2"><iframe id="IframeID" src="reporte/archivos/' + `${data.archivo}` + '" width="100%" height="600" style="border:none;"></iframe></div>');
                       
                    }
                } else {
                    $("#btnExportar").prop("disabled", false);
                    $("#btnExportar").html(btnPDF);
                    $.notify(`<span class='fonth fw4'><span data-icon='&#xe41a;' class='mr-2'></span><span class="text-dark">Error: ${data.dato}</span></span>`, {
                        type: 'danger',
                        z_index: NotifZindex,
                        delay: NotifDelay,
                        offset: NotifOffset,
                        mouse_over: NotifMouseOver,
                        placement: {
                            align: NotifAlign
                        },
                        animate: {
                            enter: NotifEnter,
                            exit: NotifExit
                        }
                    });
                }
            },
            error: function () {
                $.notify(`<span class='fonth fw4'><span data-icon='&#xe41a;' class='mr-2'></span><span class="text-dark">Error</span></span>`, {
                    type: 'danger',
                    z_index: NotifZindex,
                    delay: NotifDelay,
                    offset: NotifOffset,
                    mouse_over: NotifMouseOver,
                    placement: {
                        align: NotifAlign
                    },
                    animate: {
                        enter: NotifEnter,
                        exit: NotifExit
                    }
                });
                $("#btnExportar").prop("disabled", false);
                $("#btnExportar").html(btnPDF);
            }
        });
        // e.stopImmediatePropagation();
    });
});
