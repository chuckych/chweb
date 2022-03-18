<?php
$date        = date('Y-m-d');
$FirstYear   = $FirstYear ?? date('Y');
$maxYear     = $maxYear ?? date('Y');
$FirstDate   = $FirstDate ?? ($date);
$maxDate     = $maxDate ?? ($date);
$FechaIni    = $FechaIni ?? ($date);
$FechaFin    = $FechaFin ?? ($date);
$FechaFin2    = $FechaFin ?? ($date);
$FechaFinEnd = $FechaFinEnd ?? ($date);

$FechaFin2 = (FechaString($FechaFinEnd) > FechaString($date)) ? $FechaFinEnd : $FechaFin;
?>
<!-- moment.min.js -->
<script type="text/javascript" src="/<?= HOMEHOST ?>/js/dateranger/moment.min.js"></script>
<!-- daterangepicker.min.js -->
<script type="text/javascript" src="/<?= HOMEHOST ?>/js/dateranger/daterangepicker.min.js"></script>
<!-- daterangepicker.css -->
<link rel="stylesheet" type="text/css" href="/<?= HOMEHOST ?>/js/dateranger/daterangepicker.css" />
<script>
    $(function() {
        moment().locale('es');
        $('input[name="_dr"]').daterangepicker({
            singleDatePicker: false,
            showDropdowns: true,
            minYear: <?= $FirstYear ?>,
            maxYear: <?= $maxYear ?>,
            showWeekNumbers: false,
            autoUpdateInput: true,
            opens: "left",
            startDate: '<?= fechformat($FechaIni) ?>',
            endDate: '<?= fechformat($FechaFin) ?>',
            autoApply: true,
            minDate: '<?= fechformat($FirstDate) ?>',
            maxDate: '<?= fechformat($FechaFin2) ?>',
            linkedCalendars: false,
            ranges: {
                'Hoy': [moment(), moment()],
                'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                // 'Lunes'     : [moment().day(1), moment().day(1)],
                // 'Martes'    : [moment().day(2), moment().day(2)],
                // 'Miércoles' : [moment().day(3), moment().day(3)],
                // 'Jueves'    : [moment().day(4), moment().day(4)],
                // 'Viernes'   : [moment().day(5), moment().day(5)],
                // 'Sabado'    : [moment().day(6), moment().day(6)],
                // 'Domingo'   : [moment().day(7), moment().day(7)],
                'Esta semana': [moment().day(1), moment().day(7)],
                'Semana Anterior': [moment().subtract(1, 'week').day(1), moment().subtract(1, 'week').day(7)],
                // 'Semana Anterior': [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')],
                'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
                'Este mes': [moment().startOf('month'), moment().endOf('month')],
                'Mes anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
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
        $('#_drFiltro').daterangepicker({
            parentEl: "#Filtros",
            singleDatePicker: false,
            showDropdowns: true,
            minYear: <?= $FirstYear ?>,
            maxYear: <?= $maxYear ?>,
            showWeekNumbers: false,
            autoUpdateInput: true,
            opens: "left",
            startDate: '<?= fechformat($FechaIni) ?>',
            endDate: '<?= fechformat($FechaFin) ?>',
            autoApply: true,
            minDate: '<?= fechformat($FirstDate) ?>',
            maxDate: '<?= fechformat($FechaFin2) ?>',
            linkedCalendars: false,
            ranges: {
                'Hoy': [moment(), moment()],
                'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Esta semana': [moment().day(1), moment().day(7)],
                'Semana Anterior': [moment().subtract(1, 'week').day(1), moment().subtract(1, 'week').day(7)],
                'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
                'Este mes': [moment().startOf('month'), moment().endOf('month')],
                'Mes anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
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
        $('input[name="_dr"]').on('apply.daterangepicker', function(ev, picker) {
            $("#range").submit();
        });
    });
</script>