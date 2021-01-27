$(document).ready(function() {

    $("#Refresh").on("click", function() {
        ActualizaTablas()
    });
    
    $("#_dr").change(function() {
        ActualizaTablas()
        // $('tbody').addClass('opa3')
        // $('tbody').addClass('bg-light')
    });
    function ActualizaTablas() {
        ClassTBody()
        $('#Tabla_General').DataTable().ajax.reload();
        // $('#table-Total_General').DataTable().ajax.reload();
        // $('#table-Total_Novedades').DataTable().ajax.reload();
        // $('#table-TotalNovTipo').DataTable().ajax.reload();
        
    };
    function RefreshDataTables() {
        $('#GetFichadas').DataTable().ajax.reload();
        $('#GetNovedades').DataTable().ajax.reload();
        $('#GetHoras').DataTable().ajax.reload();
        $('#Tabla_General').DataTable().ajax.reload();
    };
    function ClassTBody() {
        $('.open-modal').removeClass('btn-outline-custom')
        $('.contentd').addClass('text-light bg-light w30')
    }
    $("#Refresh").on("click", function() {
        ActualizaTablas()
    });
    $("#RefreshModal").on("click", function() {
        RefreshDataTables()
    });
    $('.totales').addClass('invisible')
    $('tbody').addClass('opa3')
    $('#Tabla_General').DataTable({
        "initComplete": function(settings, json) {
            
            $('.nombre').html(json.nombre)
            $('.totales').removeClass('invisible')
            $('.totales').prop('disabled', false)
            fadeInOnly('.nombre')
            fadeInOnly('.totales')
            
        },
        "drawCallback": function(settings) {
            $("#GetPersonal thead").remove();
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $('#pagLega').removeClass('d-none')
            $('#GetGeneral').DataTable().ajax.reload();
            fadeInOnly('#GetGeneral');
            $('.open-modal').addClass('btn-outline-custom')
            $('.contentd').removeClass('text-light bg-light w30')
            // $('tbody').removeClass('opa3')
            // $('tbody').removeClass('bg-light')
        },
        // "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        // },
        "createdRow": function(row, data, index) {
            $(row).addClass("animate__animated animate__fadeIn align-middle");
            $.each(data, function(key, value) {
                var Horario = data.Gen_Horario;
                if (Horario == 'Franco') {
                    $('td', row).css('background-color', '#fafafa');
                    $('.open-modal', row).removeClass('btn-outline-custom');
                    $('.open-modal', row).addClass('btn-outline-secondary');
                }
                if (Horario == 'Feriado') {
                    $('td', row).css('background-color', '#fafafa');
                    $('.open-modal', row).removeClass('btn-outline-custom');
                    $('.open-modal', row).addClass('btn-outline-secondary');
                }
            });
        },
        "columnDefs": [{
            "visible": false,
            "targets": 1,
            "type": "html"
        }, ],
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        iDisplayLength:15,
        // dom: '<"d-inline-flex d-flex align-items-center"<"ml-2"p>><"mt-n3 d-flex justify-content-end"i>',
        "ajax": {
            url: "GetGeneralMisHoras.php",
            type: "POST",
            "data": function(data) {
                data._c = $("#_c").val();
                data._r = $("#_r").val();
                data._dl = $("input[name=_dl]:checked").val();
                data._range = 'on';
                data._lega = $("#_lega").val();
                data._dr = $("#_dr").val();
            },
            error: function() {
                $("#Tabla_General_processing").css("display", "none");
            },
        },
        columns: [{
                "class": "px-2",
                "data": "modal"
            }, {
                "class": "",
                "data": "LegNombre"
            },
            {
                "class": "",
                "data": "FechaDia"
            },
            {
                "class": "ls1 ",
                "data": "Gen_Horario"
            },
            {
                "class": "ls1 text-center",
                "data": "Primera"
            },
            {
                "class": "ls1 text-center",
                "data": "Ultima"
            },
            {
                "class": "",
                "data": "DescHoras"
            }, {
                "class": "text-center fw4 ls1",
                "data": "HsAuto"
            }, {
                "class": "text-center ls1",
                "data": "HsCalc"
            }, {
                "class": "",
                "data": "Novedades"
            }, {
                "class": "text-center fw4 ls1",
                "data": "NovHor"
            },
        ],
        // scrollY: '70vh',
        bLengthChange: false,
        // scrollX: true,
        // scrollCollapse: true,
        paging: true,
        searching: false,
        info: true,
        ordering: false,
        responsive: false,
        language: {
            "url": "../js/DataTableSpanishShort2.json"
        },
    })
    $('#Tabla_General').on('page.dt', function () {
        ClassTBody()
    });
});

$('#Total_General').on('shown.bs.modal', function(e) {    
    $('#table-Total_General').DataTable({
        "drawCallback": function(settings) {
            $('.Fechas').html($('#_dr').val())
        },

        "createdRow": function(row, data, index) {
            $(row).addClass("animate__animated animate__fadeIn align-middle");
        },
        "columnDefs": [{
            "visible": false,
            "targets": 0,
            "type": "html"
        }, {
            "visible": false,
            "targets": 4,
            "type": "html"
        },
    ],
        bProcessing: true,
        ajax: {
            url: "Totales.php",
            dataSrc: "Horas",
            type: "POST",
            "data": function(data) {
                data._lega = $("#_lega").val();
                data._dr = $("#_dr").val();
                data.Tipo = 'Horas';
            },
        },
        columns: [{
            "class": "",
            "data": "Cod"
        }, {
            "class": "",
            "data": "Descripcion"
        }, {
            "class": "text-center bg-light fw4 ls1",
            "data": "HsAuto"
        }, {
            "class": "text-center ls1",
            "data": "HsCalc"
        }, {
            "class": "text-center ls1",
            "data": "HsHechas"
        }, {
            "class": "text-center ls1 w-100 text-white",
            "data": "Cod"
        }],
        paging: 0,
        searching: 0,
        scrollCollapse: 0,
        info: 0,
        ordering: 0,
        language: {
            "url": "../js/DataTableSpanishShort2.json"
        },
    });
    $('#table-Total_Novedades').DataTable({
        "createdRow": function(row, data, index) {
            $(row).addClass("animate__animated animate__fadeIn align-middle");
        },
        "columnDefs": [{
            "visible": false,
            "targets": 0,
            "type": "html"
        }, ],
        ajax: {
            url: "Totales.php",
            dataSrc: "Novedades",
            type: "POST",
            "data": function(data) {
                data._lega = $("#_lega").val();
                data._dr = $("#_dr").val();
                data.Tipo = 'Novedades'
            },
        },
        columns: [{
            "class": "",
            "data": "Cod"
        }, {
            "class": "",
            "data": "Descripcion"
        }, {
            "class": "text-center bg-light fw4 ls1",
            "data": "Horas"
        }, {
            "class": "text-center ls1",
            "data": "Dias"
        }, {
            "class": "",
            "data": "Tipo"
        }, {
            "class": "ls1 w-100 text-white",
            "data": "Dias"
        }],
        paging: 0,
        searching: 0,
        scrollCollapse: 0,
        info: 0,
        ordering: 0,
        language: {
            "url": "../js/DataTableSpanishShort2.json"
        },
    });
    $('#table-TotalNovTipo').DataTable({
        "createdRow": function(row, data, index) {
            $(row).addClass("animate__animated animate__fadeIn align-middle");
        },
        ajax: {
            url: "Totales.php",
            dataSrc: "NovTipo",
            type: "POST",
            "data": function(data) {
                data._lega = $("#_lega").val();
                data._dr = $("#_dr").val();
                data.Tipo = 'NovTipo'
            },
        },
        columns: [{
            "class": "",
            "data": "Descripcion"
        }, {
            "class": "text-center bg-light fw4 ls1",
            "data": "Horas"
        }, {
            "class": "text-center ls1",
            "data": "Dias"
        }, {
            "class": "text-center ls1 w-100 text-white",
            "data": "Dias"
        }],
        paging: 0,
        searching: 0,
        scrollCollapse: 0,
        info: 0,
        ordering: 0,
        language: {
            "url": "../js/DataTableSpanishShort2.json"
        },
    });

});

$('#Total_General').on('hide.bs.modal', function(e) {
    $('#table-Total_General').DataTable().clear().draw().destroy();
    $('#table-Total_Novedades').DataTable().clear().draw().destroy();
    $('#table-TotalNovTipo').DataTable().clear().draw().destroy();
})


function DestroyDataTablesModal() {
    $('#GetFichadas').DataTable().clear().draw().destroy();
    $('#GetNovedades').DataTable().clear().draw().destroy();
    $('#GetHoras').DataTable().clear().draw().destroy();
};

$(document).on("click", ".open-modal", function (e) {
    e.preventDefault();
    $('#modalGeneral').modal('show');
    $('#Fichadas-tab').tab('show')

    var Datos    = $(this).attr('data');
    var Nombre   = $(this).attr('data2');
    var Fecha    = $(this).attr('data3');
    var Dia      = $(this).attr('data4');
    var Horario  = $(this).attr('data5');
    var FechaStr = $(this).attr('data6');

    $(".nombre").html(Nombre);
    $(".fecha").html(Fecha);
    $(".dia").html(Dia);
    $(".horario").html(Horario);
    $(".datos_fichada").val(Datos);
    $(".datos_novedad").val(Datos);
    $(".datos_hora").val(Datos);
    $(".RegFech").val(FechaStr);

    $('#GetFichadas').DataTable({
        "drawCallback": function (settings) {
            $.each(settings.json, function (key, value) {
                (value.length > 0) ? $("#CantFic").html("(" + value.length + ")") : $("#CantFic").html("");
            });
        },
        bProcessing: true,
        deferRender: true,
        "ajax": {
            url: "GetFichadas.php",
            type: "GET",
            dataSrc: "Fichadas",
            'data': {
                Datos:Datos
            },
        },
        columns: [
            { "class": "align-middle ls1", "data": "Fic" },
            { "class": "align-middle", "data": "Estado" },
            { "class": "align-middle", "data": "Tipo" },
            { "class": "align-middle ls1", "data": "Fecha" },
            { "class": "align-middle ls1", "data": "Original" },
            { "class": "align-middle w-100", "data": "null" }
        ],
        paging: false,        scrollX: false,
        scrollCollapse: false,
        searching: false,
        info: false,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort2.json"
        },
    });
    $('#GetNovedades').DataTable({
        "drawCallback": function (settings) {
            $.each(settings.json, function (key, value) {
                (value.length > 0) ? $("#CantNov").html("(" + value.length + ")") : $("#CantNov").html("");
            });
        },
        bProcessing: true,
        deferRender: true,
        "ajax": {
            url: "GetNovedades.php",
            type: "GET",
            dataSrc: "Novedades",
            'data': {
                Datos: Datos
            },
        },
        columns: [
            { "class": "align-middle ls1", "data": "Cod" },
            { "class": "align-middle", "data": "Descripcion" },
            { "class": "align-middle ls1 fw5", "data": "Horas" },
            { "class": "align-middle", "data": "Obserb" },
            { "class": "align-middle", "data": "Causa" },
            { "class": "align-middle", "data": "Just" },
            { "class": "align-middle", "data": "Tipo" },
            { "class": "align-middle", "data": "Cate" },
            { "class": "align-middle w-100", "data": "null" }
        ],
        paging: false,
        scrollX: false,
        scrollCollapse: false,
        searching: false,
        info: false,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort2.json"
        },
    });
    $('#GetHoras').DataTable({
        "drawCallback": function (settings) {
            // console.log(settings.json);
            $.each(settings.json, function (key, value) {
                // console.log(key);
                if (key == 'Horas') {
                    (value.length > 0) ? $("#CantHor").html("(" + value.length + ")") : $("#CantHor").html("");
                }
                if (key == 'Fichas') {
                    // console.log(value.Horario);
                    // $("#FicHsTr").html(value.FicHsTr + 'Hs.');

                    if (value.FicDiaL == 1) {
                        $("#TextFicHsAT").html('<span class="text-dark d-none d-sm-block">Horas a Trabajar </span>');
                        $("#TextFicHsAT_M").html('<span class="text-dark d-block d-sm-none">A Trabajar </span>');
                        $("#FicHsAT").html(value.FicHsAT + 'Hs.');
                        $("#divHorasTR").removeClass("d-none");
                    } else {
                        $("#FicHsAT").html("");
                        $("#TextFicHsAT").html('');
                        $("#divHorasTR").addClass("d-none");
                    }
                    if (value.FicHsTr != "00:00") {
                        $("#TextFicHsTr").html('<span class="text-dark d-none d-sm-block">Horas Trabajadas </span>');
                        $("#TextFicHsTr_M").html('<span class="text-dark d-block d-sm-none">Trabajadas </span>');
                        $("#FicHsTr").html(value.FicHsTr + 'Hs.');
                    } else {
                        $("#FicHsTr").html("");
                        $("#TextFicHsTr").html('');
                    }
                    $("#FicHorario").html(value.Horario);
                    if (value.HorasNeg == 1) {
                        $("#FicHsTr").addClass('text-danger');
                    } else {
                        $("#FicHsTr").removeClass('text-danger');
                    }

                    var Porcentaje = (value.FicHsTrMin / value.FicHsATMin) * 100
                    var Porcentaje = Porcentaje.toFixed()
                    var DatoFicha = value.DatoFicha

                    var Max = (value.FicHsTrMin > value.FicHsATMin) ? value.FicHsTrMin : value.FicHsATMin
                    console.log(Porcentaje);
                    // console.log(Porcentaje.toFixed());
                    if (value.FicHsTrMin>0 && value.FicHsTrMin <= value.FicHsATMin) {

                        $('#ProgressHoras').html('<div class="pb-2">'
                        +'<div class="progress border-0" style="height: 20px;">'
                        +'<div id="'+DatoFicha+'" class="progress-bar" role="progressbar" style="width: '+Porcentaje+'%;" aria-valuenow="'+value.FicHsTrMin+'" aria-valuemin="0" aria-valuemax="'+Max+'">'+value.FicHsTr+'</div>'
                        +'</div>'
                        +'</div>')

                        if (value.FicHsTrMin < value.FicHsATMin) {
                            $("#"+ DatoFicha).addClass('bg-danger');
                            $("#"+ DatoFicha).removeClass('bg-custom');
                        } else {
                            $("#"+DatoFicha).removeClass('bg-danger');
                            $("#"+DatoFicha).addClass('bg-custom');
                        }
                    }else if(value.FicHsTrMin > value.FicHsATMin){

                        var Porcentaje = (value.FicHsATMin / value.FicHsTrMin ) * 100
                        var Porcentaje = Porcentaje.toFixed()
                        var Porcentaje2 = (100 - Porcentaje) + 5
                        // if (Porcentaje2 < 10) {
                        //     var Max2 = (Max + 200)
                        // }else{
                        //     var Max2 = (Max)
                        // }
                        var Max2 = (Max)
                        $('#ProgressHoras').html('<div class="pb-2">'
                        +'<div class="progress" style="height: 20px;">'
                        +'<div id="'+DatoFicha+'" class="progress-bar" role="progressbar" style="width: '+Porcentaje+'%;" aria-valuenow="'+value.FicHsTrMin+'" aria-valuemin="0" aria-valuemax="'+Max+'">'+value.FicHsAT+'</div>'
                        +'<div class="progress-bar bg-info" role="progressbar" style="width: '+Porcentaje2+'%;" aria-valuenow="'+value.FicHsTrMin+'" aria-valuemin="0" aria-valuemax="'+Max2+'"></div>'
                        +'</div>'
                        +'</div>')

                        if (value.FicHsTrMin < value.FicHsATMin) {
                            $("#"+ DatoFicha).addClass('bg-danger');
                            $("#"+ DatoFicha).removeClass('bg-custom');
                        } else {
                            $("#"+DatoFicha).removeClass('bg-danger');
                            $("#"+DatoFicha).addClass('bg-custom');
                        }

                    }else{
                        $('#ProgressHoras').html('')
                    }

                }
            });
        },
        bProcessing: true,
        deferRender: true,
        "ajax": {
            url: "GetHoras.php",
            type: "GET",
            dataSrc: "Horas",
            'data': {
                Datos: Datos
            },
        },
        columns: [
            { "class": "align-middle ls1", "data": "Cod" },
            { "class": "align-middle", "data": "Descripcion" },
            { "class": "align-middle ls1 fw5", "data": "HsAuto" },
            { "class": "align-middle ls1", "data": "HsCalc" },
            { "class": "align-middle ls1", "data": "HsHechas" },
            { "class": "align-middle w-100", "data": "null" }
        ],
        paging: false,
        scrollX: false,
        scrollCollapse: false,
        searching: false,
        info: false,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort2.json"
        },
    });

});

$('#modalGeneral').on('hidden.bs.modal', function () {
    DestroyDataTablesModal();
});
