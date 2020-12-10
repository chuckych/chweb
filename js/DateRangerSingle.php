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
            singleDatePicker: true,
            showDropdowns: false,
            minYear: <?= $FirstYear ?>,
            maxYear: <?= $maxYear ?>,
            showWeekNumbers: true,
            autoUpdateInput:true,
            opens: "left",
            startDate: '<?= fechformat($FechaIni) ?>',
            endDate: '<?= fechformat($FechaFin) ?>',
            autoApply: true,
            minDate: "<?= fechformat($FirstDate) ?>",
            maxDate: "<?= fechformat($maxDate) ?>",
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
                applyButtonClasses: "text-white <?= $bgcolor ?>",
            },
        });
        $('input[name="_dr"]').on('apply.daterangepicker', function(ev, picker) {
            $("#range").submit();
        });
    });
    $(function() {
        moment().locale('es');
        $('.drs').daterangepicker({
            singleDatePicker: true,
            showDropdowns: false,
            minYear: <?= $FirstYear ?>,
            maxYear: <?= $maxYear ?>,
            showWeekNumbers: true,
            autoUpdateInput:true,
            opens: "left",
            startDate: '<?= fechformat($FechaIni) ?>',
            endDate: '<?= fechformat($FechaFin) ?>',
            autoApply: true,
            minDate: "<?= fechformat($FirstDate) ?>",
            maxDate: "<?= fechformat($maxDate) ?>",
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
                applyButtonClasses: "text-white <?= $bgcolor ?>",
            },
        });
        // $('.drs').on('apply.daterangepicker', function(ev, picker) {
        //     $("#range").submit();
        // });
    });
</script>