const dateRange = async () => {
    let rs = await axios.get('../../app-data/fechas/fichas'); // retorna objeto con la primer fecha y la ultima de FICHAS ej: {data: {min: "2021-01-01", max: "2021-12-31"}}
    if (!rs.data) return; // si no hay respuesta, no hace nada

    let añoMin = new Date(rs.data.min).getFullYear(); // extrae el año de la fecha minima
    let añoMax = new Date(rs.data.max).getFullYear(); // extrae el año de la fecha maxima
    let minDate = new Date(rs.data.min)
    let maxDate = new Date(rs.data.max)

    let maxDate2 = (new Date(rs.data.max) > new Date()) ? new Date() : new Date(rs.data.max); // si la fecha maxima es mayor a la fecha actual, la fecha maxima es la fecha actual
    let minDate2 = new Date(maxDate2); // la fecha minima es la fecha maxima
    minDate2.setDate(minDate2.getDate() - 29); // le resta 29 Dias a la fecha maxima

    const now = () => new Date(); // fecha actual

    $('#_dr').daterangepicker({
        singleDatePicker: false,
        showDropdowns: true,
        minYear: añoMin,
        maxYear: añoMax,
        showWeekNumbers: false,
        autoUpdateInput: true,
        opens: "left",
        drops: "down",
        startDate: minDate2,
        endDate: maxDate2,
        autoApply: true,
        minDate: minDate,
        maxDate: maxDate,
        alwaysShowCalendars: true,
        linkedCalendars: false,
        buttonClasses: "btn btn-sm font08",
        applyButtonClasses: "btn-custom fw4 px-3 opa8",
        cancelClass: "btn-link fw4 text-gris",
        ranges: {
            'Hoy': [now(), now()],
            'Ayer': [now(now().setDate(now().getDate() - 1)), now(now().setDate(now().getDate() - 1))],
            'Esta semana': [now(now().setDate(now().getDate() - now().getDay() + 1)), now()],
            'Semana Anterior': [now(now().setDate(now().getDate() - now().getDay() - 6)), now(now().setDate(now().getDate() - now().getDay()))],
            'Últimos 7 días': [now(now().setDate(now().getDate() - 6)), now()],
            'Este mes': [now(now().getFullYear(), now().getMonth(), 1), now(now().getFullYear(), now().getMonth() + 1, 0)],
            'Mes anterior': [now(now().getFullYear(), now().getMonth() - 1, 1), now(now().getFullYear(), now().getMonth(), 0)],
            'Últimos 30 días': [now(now().setDate(now().getDate() - 29)), now()],
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
            monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            firstDay: 1,
            alwaysShowCalendars: true,
            applyButtonClasses: "btn-custom fw5 px-3 opa8",
        },
    });
    loaderIn('#_dr', false);
}
dateRange();