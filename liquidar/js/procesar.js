$('.trash').hide()
ActiveBTN(false, "#submit", '', 'Generar')
$(".alta_liquidacion").bind("submit", function (e) {
    let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
    e.preventDefault();

    $.ajax({
        type: "POST",
        url: "borrarArchivo.php",
        data: {
            ruta: $(".ArchNomb").text()
        }
    });
    
    $.ajax({
        type: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        beforeSend: function (data) {
            ActiveBTN(true, "#submit", loading, 'Generar')
            $(".archivo").removeClass("animate__animated animate__fadeIn");
            $.notifyClose();
            notify('Generando <span class = "dotting mr-1"> </span> ' + loading, 'dark', 60000, 'right')
        },
        success: function (data) {
            if (data.status == "ok") {
                ActiveBTN(false, "#submit", loading, 'Generar')
                $('input[type="checkbox"]').prop('checked', false)

                let tipo = ($('#Tipo').val() == '1') ? 'jornales' : 'mensuales'
           
                setTimeout(() => {
                    GetArch($(".ArchNomb").text() + '?v=' + $.now(), $(".ArchNomb").text(), tipo)

                    setTimeout(() => {
                        classEfect('#tdDescargar', 'animate__animated animate__flash')
                        classEfect('.ArchPath', 'animate__animated animate__flash')
                        $.notifyClose();
                        notify(data.Mensaje, 'success', 3000, 'right')
                    }, 500);

                }, 1500);
            } else {
                $("#respuestatext").html("");
                ActiveBTN(false, "#submit", loading, 'Generar')
                $.notifyClose();
                notify(data.Mensaje, 'danger', 2000, 'right')
            }
        }
    });
    e.stopImmediatePropagation();
});

function DatePicker() {
    $('input[name="_drLiq"]').daterangepicker({
        singleDatePicker: false,
        showDropdowns: false,
        showWeekNumbers: false,
        autoUpdateInput: true,
        opens: "left",
        autoApply: true,
        linkedCalendars: false,
        locale: {
            format: "DD/MM/YYYY",
            separator: " al ",
            applyLabel: "Aplicar",
            cancelLabel: "Cancelar",
            fromLabel: "Desde",
            toLabel: "Para",
            customRangeLabel: "Personalizado",
            weekLabel: "Sem",
            daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
            "monthNames": ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            firstDay: 1,
            alwaysShowCalendars: true,
            applyButtonClasses: "text-white bg-custom",
        },
    });
}
// $('input[name="_drLiq"]').on('apply.daterangepicker', function(ev, picker) {
//     $("#range").submit();
// });
if ($(window).width() < 769) {
    $('input[name="_drLiq"]').prop('readonly', true)
}
