$(document).ready(function () {
    // var value = "1352"
    // var Hora = value.slice(0, -2);
    // var Min = value.slice(2);
    // console.log(Hora + ":" + Min);

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

    function DataCNove() {

        $.ajax({
            type: 'POST',
            dataType: "json",
            url: "DataCNove.php",
            'data': {
                _dr: $("#_dr").val(),
            },
            beforeSend: function () {
                $('.ChartsDiv').addClass('bg-light')
                $('#Refresh').prop('disabled', true)
                $('#chartNove').addClass('d-none')
            },
            success: function (respuesta) {
                $('#chartNove').removeClass('d-none')
                $('.ChartsDiv').removeClass('bg-light')
                $('#Refresh').prop('disabled', false)
                var TipoArr  = respuesta.TipoArr[0]
                var CantArr  = respuesta.CantArr[0]
                var colorArr = respuesta.colorArr[0]
                var ctx = document.getElementById("myChart").getContext("2d");
                var ctx = $('#myChart');
                var myChart = new Chart(ctx, {
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
                    myChart.destroy();
                });
                $("#Refresh").on("click", function () {
                    myChart.destroy();
                });
            },
            error: function () {
            }
        });
    }
    DataCNove()
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
                var TipoArr  = respuesta.TipoArr[0]
                var CantArr  = respuesta.CantArr[0]
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

    function DataCHoras() {

        $.ajax({
            type: 'POST',
            dataType: "json",
            url: "DataCHoras.php",
            'data': {
                _dr: $("#_dr").val(),
            },
            beforeSend: function () {
                $('.ChartsDiv').addClass('bg-light')
                $('#Refresh').prop('disabled', true)
                $('#charNoveH').addClass('d-none')
            },
            success: function (respuesta) {
                $('#charNoveH').removeClass('d-none')
                $('.ChartsDiv').removeClass('bg-light')
                $('#Refresh').prop('disabled', false)
                var HoraDesc2Arr = respuesta.HoraDesc2Arr[0]
                var CantArr      = respuesta.CantArr[0]
                var colorArr     = respuesta.colorArr[0]
                var ctx = document.getElementById("myChart2").getContext("2d");
                var ctx = $('#myChart2');
                var myChart2 = new Chart(ctx, {
                    type: 'bar',
                    // type: 'horizontalBar',
                    data: {
                        labels: HoraDesc2Arr,
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
                        scales: {
                            x: {
                                type: 'timeseries',
                            }
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    // Include a dollar sign in the ticks
                                    callback: function (value, index, values) {
                                        var valor = pad(value, 4, 0)
                                        var Hora = valor.slice(0, -2);
                                        var Min  = valor.slice(2);
                                        var valor = (Hora + ":" + Min)
                                        return valor
                                    }
                                }
                            }]
                        },
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
                        },
                    },
                });
                $("#_dr").change(function () {
                    myChart2.destroy();
                });
                $("#Refresh").on("click", function () {
                    myChart2.destroy();
                });
            
            },
            error: function () {
            }
        });
    }
    DataCHoras()

    $("#_dr").change(function () {
        DataCNove()
        DataCNove2()
        DataCHoras()
        // fadeInOnly('.ChartsDiv')
        classEfect('.arrow-repeat','animate__animated animate__rotateIn')
        classEfect('.ChartsDiv','animate__animated animate__fadeIn')
    });

    $("#Refresh").on("click", function () {
        DataCNove()
        DataCNove2()
        DataCHoras()
        // fadeInOnly('.ChartsDiv')
        classEfect('.arrow-repeat','animate__animated animate__rotateIn')
        classEfect('.ChartsDiv','animate__animated animate__fadeIn')
    });
});


Highcharts.chart('container', {
    chart: {
      type: 'column'
    },
    title: {
      text: 'Monthly Average Rainfall'
    },
    subtitle: {
      text: 'Source: WorldClimate.com'
    },
    xAxis: {
      categories: [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec'
      ],
      crosshair: true
    },
    yAxis: {
      min: 0,
      title: {
        text: 'Rainfall (mm)'
      }
    },
    tooltip: {
      headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
      pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
        '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
      footerFormat: '</table>',
      shared: true,
      useHTML: true
    },
    plotOptions: {
      column: {
        pointPadding: 0.2,
        borderWidth: 0
      }
    },
    series: [{
      name: 'Tokyo',
      data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]
  
    }, {
      name: 'New York',
      data: [83.6, 78.8, 98.5, 93.4, 106.0, 84.5, 105.0, 104.3, 91.2, 83.5, 106.6, 92.3]
  
    }, {
      name: 'London',
      data: [48.9, 38.8, 39.3, 41.4, 47.0, 48.3, 59.0, 59.6, 52.4, 65.2, 59.3, 51.2]
  
    }, {
      name: 'Berlin',
      data: [42.4, 33.2, 34.5, 39.7, 52.6, 75.5, 57.4, 60.4, 47.6, 39.1, 46.8, 51.1]
  
    }]
  });