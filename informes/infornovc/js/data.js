$(function () {
    // var AnioMin = parseFloat($('#AnioMin').val());
    // var AnioMax = parseFloat($('#AnioMax').val());

    function _rutauser() {
        var _rutauser = $('#_homehost').val() + '_' + $('#_lega').val()
        return _rutauser
    }
    var _rutauser = _rutauser()


    if (sessionStorage.getItem(_rutauser + '_range')) {
        var dataRange = sessionStorage.getItem(_rutauser + '_range').split(' al ')
        var desde = (dataRange[0])
        var hasta = (dataRange[1])
    } else {
        var desde = moment().subtract(6, "month").format('DD/MM/YYYY')
        var hasta = moment().subtract(1, "days").format('DD/MM/YYYY')
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
        // $("select[name='GetPresentismo_length']").val('10');
        table.page.len(10).draw();
        $('#GetConceptosAusentes').DataTable().page.len(10).draw();
        // console.log($("select[name='GetPresentismo_length']").val());
        $('#datoPorLegajo').val('1');
        CheckedInput('#PorLegajo')
        $("#ordenar").val('0')
        sessionStorage.setItem(_rutauser + '_ordenar', $("#ordenar").val());
        table.order([0, 'asc']).draw();
        $('#rowFiltros').collapse('hide')
        ActualizaTablas()
    }
    // $("#trash_allIn").on("click", function () {
    $(document).on('click', '#trash_allIn', function () {
        LimpiarFiltros()
        sessionStorage.clear();
        var desde = moment().subtract(6, "month").format('DD/MM/YYYY')
        var hasta = moment().subtract(1, "days").format('DD/MM/YYYY')
        $('#_drnovc').val(desde + ' al ' + hasta)
        dateRange(desde, hasta)
    });

    function dateRange(desde, hasta) {
        $('#_drnovc').daterangepicker({
            singleDatePicker: false,
            showDropdowns: false,
            // minYear: AnioMin,
            // maxYear: AnioMax,
            startDate: desde,
            endDate: hasta,
            showWeekNumbers: false,
            autoUpdateInput: true,
            opens: "left",
            drops: "down",
            autoApply: false,
            alwaysShowCalendars: true,
            linkedCalendars: false,
            buttonClasses: "btn btn-sm fontq",
            applyButtonClasses: "btn-custom fw4 px-3 opa8",
            cancelClass: "btn-link fw4 text-gris",
            ranges: {
                "1° Semestre": [
                    moment().startOf("year"),
                    moment().startOf("year").add(5, "month").endOf("month")
                ],
                '2° Semestre': [
                    moment().startOf('year').add(6, 'month'),
                    moment().startOf('year').endOf('year')
                ],
                "Ultimos 6 meses": [
                    moment().subtract(6, "month"),
                    moment().subtract(1, "days")
                ],
                "Este Año": [
                    moment().startOf("year"),
                    moment().endOf("year")
                ],
                "Ultimo Año": [
                    moment().subtract(1, "year"),
                    moment().subtract(1, "days")
                ],
                "Año Anterior": [
                    moment().subtract(1, "year").startOf("year"),
                    moment().subtract(1, "year").endOf("year")
                ],
                "1° Semestre Ant.": [
                    moment().subtract(1, "year").startOf("year"),
                    moment().subtract(1, "year").startOf("year").add(5, "month").endOf("month")
                ],
                "2° Semestre Ant.": [
                    moment().subtract(1, "year").startOf("year").add(6, 'month'),
                    moment().subtract(1, "year").startOf("year").endOf('year')
                ],
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
    }

    dateRange(desde, hasta)

    function ActualizaTablas() {
        table.ajax.reload();
    };

    var sessionOrder = sessionStorage.getItem(_rutauser + '_ordenar')

    $("#_drnovc").change(function () {
        sessionStorage.setItem(_rutauser + '_range', $("#_drnovc").val())
        ActualizaTablas()
    });

    if (sessionStorage.getItem(_rutauser + '_range')) {
        $('#_drnovc').val(sessionStorage.getItem(_rutauser + '_range'))
    }
    if (sessionOrder) {
        if (sessionOrder == '0') {
            $('#PorLegajo').prop('checked', true)
        } else {
            $('#PorNombre').prop('checked', true)
        }
    } else {
        $("#ordenar").val('0')
        $('#PorLegajo').prop('checked', true)
    }
    sessionStorage.setItem(_rutauser + '_ordenar', $("#ordenar").val());

    $("#PorLegajo").change(function (e) {
        e.preventDefault();
        var PorLegajo = document.getElementById("PorLegajo");
        if (PorLegajo.checked == true) {
            $("#ordenar").val('0')
            sessionStorage.setItem(_rutauser + '_ordenar', $("#ordenar").val());
        }
        table.order([0, 'asc']).draw();
        setTimeout(function () {
            table.columns.adjust().draw();
        }, 100);
    });
    $("#PorNombre").change(function (e) {
        e.preventDefault();
        var PorNombre = document.getElementById("PorNombre");
        if (PorNombre.checked == true) {
            $("#ordenar").val('1')
            sessionStorage.setItem(_rutauser + '_ordenar', $("#ordenar").val());
        }
        table.order([1, 'asc']).draw();
        setTimeout(function () {
            table.columns.adjust().draw();
        }, 100);
    });

    var IconExcel = '.xls <img src="../../img/xls.png" class="w15" alt="Exportar Excel">'
    ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)
    var IconCsv = '.csv <img src="../../img/csv.png" class="w15" alt="Exportar a Csv">'
    ActiveBTN(false, "#btnCsv", 'Exportando', IconCsv)

    function GetFicCsv(data) {
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: "FicCsv.php",
            'data': {
                datos: data
            },
            beforeSend: function () {
                ActiveBTN(true, "#btnCsv", 'Exportando', IconCsv)
            },
            success: function (data) {
                if (data.status == "ok") {
                    ActiveBTN(false, "#btnCsv", 'Exportando', IconCsv)
                    window.location = data.archivo
                }

            },
            error: function () {
                ActiveBTN(false, "#btnCsv", 'Exportando', IconCsv)
            }
        });
    }
    function GetFicExcel(data) {
        $.ajax({
            type: 'POST',
            dataType: "json",
            // url: "FicCsv.php",
            url: "FicExcel.php",
            'data': {
                datos: data
            },
            beforeSend: function () {
                ActiveBTN(true, "#btnExcel", 'Exportando', IconExcel)
            },
            success: function (data) {
                if (data.status == "ok") {
                    ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)
                    window.location = data.archivo
                }

            },
            error: function () {
                ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)
            }
        });
    }

    var table = $('#GetPresentismo').DataTable({
        "initComplete": function (settings, json) {
            $("#GetPresentismo_filter").prepend('<button title="Exportar Excel" type="button" class="mr-1 btn btn-light text-success fw5 border btn-sm fontq" id="btnExcel">.xls <img src="../../img/xls.png" class="w15" alt="Exportar Excel"></button>')
            $("#GetPresentismo_filter").prepend('<button title="Exportar Csv" type="button" class="mr-1 btn btn-light text-success fw5 border btn-sm fontq" id="btnCsv">.csv <img src="../../img/csv.png" class="w15" alt="Exportar a Csv"></button>')
        },
        "fnDrawCallback": function (settings) {

            $(document).on("click", "#btnExcel", function (e) {
                e.preventDefault();
                $('#rowFiltros').collapse('hide')
                var datoexcel = JSON.stringify(settings.json);
                GetFicExcel(datoexcel)
                e.stopImmediatePropagation()
            });

            $(document).on("click", "#btnCsv", function (e) {
                e.preventDefault();
                $('#rowFiltros').collapse('hide')
                var datocsv = JSON.stringify(settings.json);
                GetFicCsv(datocsv)
                e.stopImmediatePropagation()
            });
        },
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todo"]],
        columnDefs: [
            { orderable: false, targets: 0 },
            { orderable: false, targets: 1 },
            { orderable: false, targets: 2 },
            { orderable: false, targets: 3 },
            { orderable: false, targets: 4 },
            { orderable: false, targets: 5 },
            { orderable: false, targets: 6 },
            { orderable: false, targets: 7 },
            { orderable: false, targets: 8 },
            { orderable: false, targets: 9 },
        ],
        bProcessing: true,
        stateSave: true,
        stateDuration: -1,
        "ajax": {
            url: 'getPresentismo.php?v=' + $.now(),
            type: "POST",
            "data": function (data) {
                data.fecha = $("#_drnovc").val();
                data.ordenar = $("#ordenar").val();
                data.Emp = $("#Emp").val();
                data.Plan = $("#Plan").val();
                data.Sect = $("#Sect").val();
                data.Sec2 = $("#Sec2").val();
                data.Grup = $("#Grup").val();
                data.Sucur = $("#Sucur").val();
                data.Tipo = $("#Tipo").val();
                data.Per = $("#Per").val();
            },
            error: function () { },
        },
        columns: [
            {
                "class": "",
                "data": 'legajo'
            },
            {
                "class": "text-wrap",
                "data": 'nombre'
            },
            {
                "class": "text-center",
                "data": 'desde'
            },
            {
                "class": "text-center",
                "data": 'hasta'
            },
            {
                "class": "text-center",
                "data": '_presentes'
            },
            {
                "class": "text-center",
                "data": '_ausentes'
            },
            {
                "class": "text-center",
                "data": '_totaldias'
            },
            {
                "class": "text-center",
                "data": '_convpres'
            },
            {
                "class": "text-center",
                "data": '_convaus'
            },
            {
                "class": "text-center",
                "data": '_totalmesesconv'
            },
            // {
            //     "class": "text-center",
            //     "data": '_totalmesesfecha'
            // },
        ],
        scrollY: '50vh',
        bScrollCollapse: true,
        deferRender: true,
        bLengthChange: true,
        paging: true,
        searching: true,
        info: true,
        ordering: true,
        responsive: false,
        "autoWidth": true,

        language: {
            "url": "../../js/DataTableSpanishShort2.json?v=" + vjs()
        },
    })
    setTimeout(function () {
        table.columns.adjust().draw();
    }, 100);
});
