$(document).ready(function () {

    $('#_dr').daterangepicker({
        singleDatePicker: false,
        showDropdowns: false,
        minYear: AnioMin,
        maxYear: AnioMax,
        showWeekNumbers: false,
        autoUpdateInput: true,
        startDate: moment().subtract(30, 'days'),
        endDate: moment(),
        opens: "center",
        drops: "down",
        autoApply: true,
        alwaysShowCalendars: true,
        linkedCalendars: false,
        buttonClasses: "btn btn-sm fontq",
        applyButtonClasses: "btn-custom fw4 px-3 opa8",
        cancelClass: "btn-link fw4 text-gris",
        ranges: {
            'Ultimos 30 días': [moment().subtract(30, 'days')],
            'Este mes': [
                moment().startOf('month'),
                moment().endOf('month')
            ],
            'Mes anterior': [
                moment().subtract(1, 'month').startOf('month'),
                moment().subtract(1, 'month').endOf('month')
            ],
            '1° Trimestre': [
                moment().startOf('year').startOf('quarter'),
                moment().startOf('year').endOf('quarter')
            ],
            '2° Trimestre': [
                moment().startOf('year').add(1, 'quarter').startOf('quarter'),
                moment().startOf('year').add(1, 'quarter').endOf('quarter')
            ],
            '3° Trimestre': [
                moment().startOf('year').add(2, 'quarter').startOf('quarter'),
                moment().startOf('year').add(2, 'quarter').endOf('quarter')
            ],
            '4° Trimestre': [
                moment().startOf('year').add(3, 'quarter').startOf('quarter'),
                moment().startOf('year').add(3, 'quarter').endOf('quarter')
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

    var AnioMin = parseFloat($('#AnioMin').val());
    var AnioMax = parseFloat($('#AnioMax').val());

    function DataCNove2() {

        $.ajax({
            type: 'POST',
            dataType: "json",
            url: "DataCNove2.php",
            'data': {
                _dr: $("#_dr").val(),
            },
            beforeSend: function () {
                $('.ChartsDiv').addClass('bg-light')
                $('#Refresh').prop('disabled', true)
                $('#charNoveT').addClass('d-none')
            },
            success: function (respuesta) {
                $('#charNoveT').removeClass('d-none')
                $('.ChartsDiv').removeClass('bg-light')
                $('#Refresh').prop('disabled', false)
                var TipoArr = respuesta.TipoArr[0]
                var CantArr = respuesta.CantArr[0]
                var colorArr = respuesta.colorArr[0]
                var ctx = document.getElementById("myChart3").getContext("2d");
                var ctx = $('#myChart3');
                var myChart3 = new Chart(ctx, {
                    type: 'bar',
                    // type: 'horizontalBar',
                    data: {
                        labels: TipoArr,
                        datasets: [{
                            label: '',
                            data: CantArr,
                            backgroundColor: colorArr,
                            borderColor: colorArr,
                            borderWidth: 2,
                            // barThickness: 'flex',
                        }]
                    },
                    options: {
                        responsive: true,
                        layout: {
                            padding: {
                                left: 0,
                                right: 0,
                                top: -10,
                                bottom: 0
                            }
                        },
                        animation: {
                            duration: 0,
                            onComplete: function () {
                                var chartInstance = this.chart,
                                    ctx = chartInstance.ctx;
                                ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle,
                                    Chart.defaults.global.defaultFontFamily);
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'bottom';

                                this.data.datasets.forEach(function (dataset, i) {
                                    var meta = chartInstance.controller.getDatasetMeta(i);
                                    meta.data.forEach(function (bar, index) {
                                        if (dataset.data[index] > 0) {
                                            var data = dataset.data[index];
                                            ctx.fillText(data, bar._model.x, bar._model.y);
                                        }
                                    });
                                });
                            }
                        }
                    },
                });
                $("#_dr").change(function () {
                    myChart3.destroy();
                });
                $("#Refresh").on("click", function () {
                    myChart3.destroy();
                });
            },
            error: function () {
            }
        });
    }
    DataCNove2()

    $("#_dr").change(function () {
        fadeInOnly('.ChartsDiv')
        $('.loader').show()
        setTimeout(() => {
            DataCNove2()
            ChartPie("DataCNove.php",'ChartTipoNove')
            ChartPieHoras("DataCHoras.php",'ChartTotalHoras')    
            setTimeout(() => {
                $('.loader').hide()
            }, 300);
        }, 500);
        classEfect('.arrow-repeat', 'animate__animated animate__rotateIn')
        classEfect('.ChartsDiv', 'animate__animated animate__fadeIn')
    });

    $("#Refresh").on("click", function () {
        fadeInOnly('.ChartsDiv')
        $('.loader').show()
        fadeInOnly('.ChartsDiv')
        setTimeout(() => {
            DataCNove2()
            ChartPie("DataCNove.php",'ChartTipoNove')
            ChartPieHoras("DataCHoras.php",'ChartTotalHoras')    
            setTimeout(() => {
                $('.loader').hide()
            }, 300);
        }, 500);
        classEfect('.arrow-repeat', 'animate__animated animate__rotateIn')
        classEfect('.ChartsDiv', 'animate__animated animate__fadeIn')
    });
    function ChartPie(url, renderTo) {
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: url,
            'data': {
                _dr: $("#_dr").val(),
            },
            beforeSend: function () {
            },
            success: function (data) {
    
                let dataArray = data.dataArray
                let reformattedArray = dataArray.map(function (obj) {
                    return obj;
                });
                var chart;
                chart = new Highcharts.Chart(renderTo, {
                    colors: Highcharts.map(Highcharts.getOptions().colors, function (color) {
                        return {
                            radialGradient: {
                                cx: 0.5,
                                cy: 0.3,
                                r: 0.7
                            },
                            stops: [
                                [0, color],
                                [1, Highcharts.color(color).brighten(-0.3).get('rgb')] // darken
                            ]
                        };
                    }),
                    // Build the chart
                        chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    title: {
                        text: ''
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y}</b>'
                    },
                    accessibility: {
                        point: {
                            valueSuffix: ''
                        }
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '{point.name}: {point.y} ',
                                connectorColor: 'silver'
                            }
                        }
                    },
    
                    series: [{
                        name: 'Total',
                        data: reformattedArray
                    }]
                }, 
                function (chart) {
                    // $('#Refresh').click(function () {
                    //     chart.destroy();
                    // });
                });
            },
        });
    }
    function ChartPieHoras(url, renderTo) {
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: url,
            'data': {
                _dr: $("#_dr").val(),
            },
            beforeSend: function () {
            },
            success: function (data) {
    
                let dataArray = data.dataArray
                let reformattedArray = dataArray.map(function (obj) {
                    return obj;
                });
                var chart;
                chart = new Highcharts.Chart(renderTo, {
                    colors: Highcharts.map(Highcharts.getOptions().colors, function (color) {
                        return {
                            radialGradient: {
                                cx: 0.5,
                                cy: 0.3,
                                r: 0.7
                            },
                            stops: [
                                [0, color],
                                [1, Highcharts.color(color).brighten(-0.3).get('rgb')] // darken
                            ]
                        };
                    }),
                    // Build the chart
                        chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    title: {
                        text: ''
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.horas}</b>'
                    },
                    accessibility: {
                        point: {
                            valueSuffix: ''
                        }
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '{point.name}: {point.horas} ',
                                connectorColor: 'silver'
                            }
                        }
                    },
    
                    series: [{
                        name: 'Total',
                        data: reformattedArray
                    }]
                }, 
                function (chart) {
                    // $('#Refresh').click(function () {
                    //     chart.destroy();
                    // });
                });
            },
        });
    }
    setTimeout(() => {
        ChartPieHoras("DataCHoras.php",'ChartTotalHoras')
    }, 200);
    
    setTimeout(() => {
        ChartPie("DataCNove.php",'ChartTipoNove')
    }, 200);
});
