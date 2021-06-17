$('.trash').hide()
ActiveBTN(false, "#submit", '', 'Generar')
$(".alta_liquidacion").bind("submit", function (e) {
    let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
    e.preventDefault();
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
                $.notifyClose();
                notify(data.Mensaje, 'success', 2000, 'right')
                let tipo = ($('#Tipo').val()=='1') ? 'jornales':'mensuales'
                GetArch($(".ArchNomb").text() +'?v='+ $.now(), $(".ArchNomb").text(), tipo)
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

$('input[name="_drLiq"]').daterangepicker({
    singleDatePicker: false,
    showDropdowns: false,
    showWeekNumbers: false,
    autoUpdateInput: true,
    opens: "left",
    // startDate: '<?= fechformat($FechaIni) ?>',
    // endDate: '<?= fechformat($FechaFin) ?>',
    autoApply: true,
    // minDate: '<?= fechformat($FirstDate) ?>',
    // maxDate: '<?= fechformat($maxDate) ?>',
    linkedCalendars: false,
    ranges: {
        // 'Hoy': [moment(), moment()],
        // 'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        // 'Lunes'     : [moment().day(1), moment().day(1)],
        // 'Martes'    : [moment().day(2), moment().day(2)],
        // 'Miércoles' : [moment().day(3), moment().day(3)],
        // 'Jueves'    : [moment().day(4), moment().day(4)],
        // 'Viernes'   : [moment().day(5), moment().day(5)],
        // 'Sabado'    : [moment().day(6), moment().day(6)],
        // 'Domingo'   : [moment().day(7), moment().day(7)],
        // 'Esta semana': [moment().day(1), moment().day(7)],
        // 'Semana Anterior': [moment().subtract(1, 'week').day(1), moment().subtract(1, 'week').day(7)],
        // 'Semana Anterior': [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')],
        // 'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
        // 'Este mes': [moment().startOf('month'), moment().endOf('month')],
        // 'Mes anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        // 'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
    },
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
$('input[name="_drLiq"]').on('apply.daterangepicker', function(ev, picker) {
    $("#range").submit();
});
if ($(window).width() < 769) {
    $('input[name="_drLiq"]').prop('readonly', true)
}
