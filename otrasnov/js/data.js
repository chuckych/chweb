// $(".Filtros").prop('disabled', true);
function ActualizaTablas(){
    if ($("#Visualizar").is(":checked")) {
        $('#GetFechas').DataTable().ajax.reload();
    } else {
        $('#GetPersonal').DataTable().ajax.reload();
        $('#Per2').addClass('d-none')
        $('.pers_legajo').removeClass('d-none')
    };
};
var map = {17: false, 18: false, 32: false, 16: false, 39: false, 37: false, 13:false, 27:false};
$(document).keydown(function (e) {
    if (e.keyCode in map) {
        map[e.keyCode] = true;
        if (map[32]) { /** Barra espaciadora */
            $('#Filtros').modal('show');
        }
    }
    if (e.keyCode in map) {
        map[e.keyCode] = true;
        if (map[27]) { /** Esc */
            $('#Filtros').modal('hide');
        }
    }

    if (e.keyCode in map) {
        map[e.keyCode] = true;
        if (map[39]) { /** Flecha derecha */
            if ($("#Visualizar").is(":checked")) {
                $('#GetFechas').DataTable().page('next').draw('page');
            } else {
                $('#GetPersonal').DataTable().page('next').draw('page');
            };
        }
    }
    if (e.keyCode in map) {
        map[e.keyCode] = true;
        if (map[37]) { /** Flecha izquierda */
            if ($("#Visualizar").is(":checked")) {
                $('#GetFechas').DataTable().page('previous').draw('page');
            } else {
                $('#GetPersonal').DataTable().page('previous').draw('page');
            };

        }
    }
}).keyup(function (e) {
    if (e.keyCode in map) {
        map[e.keyCode] = false;
    }
});

$("#pagFech").addClass('d-none');
$("#GetNovedadesFechaTable").addClass('d-none');
$('#Visualizar').prop('disabled', true)

    var GetPersonal = $('#GetPersonal').DataTable({
        "initComplete": function( settings, json ) {
        },
        "drawCallback": function( settings ) {
            $("#GetPersonal thead").remove();  
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            if (settings.json.recordsTotal>0) {
                $('#GetNovedades').DataTable().ajax.reload();
                $('#GetNovedadesTable').removeClass('d-none');
                $('#resultNov').html('');
            }else{
                $('#resultNov').html('No hay resultados');
                $('#GetNovedadesTable').addClass('d-none');
            }
        },  
        pagingType: "full",
        lengthMenu: [[1], [1]],
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        dom: '<"d-inline-flex d-flex align-items-center"t<"ml-2"p>><"mt-n3 d-flex justify-content-end"i>',
        ajax: {
            url: "/" + $("#_homehost").val() + "/otrasnov/GetPersonal.php",
            type: "POST",
            "data": function(data){
                data._l      = $("#_l").val();
                data.Per     = $("#Per").val();
                data.Per2    = $("#Per2").val();
                data.Tipo    = $("#Tipo").val();
                data.Emp     = $("#Emp").val();
                data.Plan    = $("#Plan").val();
                data.Sect    = $("#Sect").val();
                data.Sec2    = $("#Sec2").val();
                data.Grup    = $("#Grup").val();
                data.Sucur   = $("#Sucur").val();
                data._dr     = $("#_dr").val();
                data.FicONove = $("#FicONove").val();
            },

            error: function() {
                $("#GetPersonal_processing").css("display", "none");
            },
        },
        columns: [
        {
            "class": "w80 px-3 border fw4 bg-light radius pers_legajo",
            "data": 'pers_legajo'
        },
        {
            "class": "w300 px-3 border border-left-0 fw4 bg-light radius",
            "data": 'pers_nombre'
        },
        ],
        paging: true,
        responsive: false,
        info: true,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort2.json"
        },
    });
    
    $('#GetNovedades').DataTable({
        "initComplete": function(settings, json) {
            $("#Refresh").prop('disabled', false);
            $('#trash_all').removeClass('invisible');
        },
        "drawCallback": function(settings) {
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $(".custom-select").addClass('text-secondary bg-light');
            setTimeout(function(){ 
                $(".Filtros").prop('disabled', false);
             }, 1000);
        },
        lengthMenu: [[30, 60, 90, 120], [30, 60, 90, 120]],
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        ajax: {
            url: "/" + $("#_homehost").val() + "/otrasnov/GetNovedades.php",
            type: "POST",
            "data": function(data){
                data.Per= $("#Per").val();
                data.Tipo= $("#Tipo").val();
                data.Emp= $("#Emp").val();
                data.Plan= $("#Plan").val();
                data.Sect= $("#Sect").val();
                data.Sec2= $("#Sec2").val();
                data.Grup= $("#Grup").val();
                data.Sucur= $("#Sucur").val();
                data._dr  = $("#_dr").val();
                data._l  = $("#_l").val();
                data.FicONove  = $("#FicONove").val();
            },
            error: function() {
                $("#GetNovedades").css("display", "none");
            },
        },
        columns: [
        //     {
        //     "class": "align-middle ls1 ",
        //     "data": "nov_LegNume"
        // }, {
        //     "class": "align-middle",
        //     "data": "nov_leg_nombre"
        // }, 
        {
            "class": "align-middle ls1 ",
            "data": "Fecha"
        }, {
            "class": "align-middle",
            "data": "nov_nom_dia"
        }, 
        {
            "class": "align-middle ls1 ",
            "data": "nov_horario"
        }, {
            "class": "align-middle ls1 text-center",
            "data": "NoveCod"
        }, {
            "class": "align-middle",
            "data": "Novedades"
        }, {
            "class": "align-middle ls1 ",
            "data": "NovValor"
        }, 
        {
            "class": "align-middle",
            "data": "NoveObserv"
        }, 
        {
            "class": "align-middle",
            "data": "NoveTipo"
        }, 
    ],
        scrollX: true,
        scrollCollapse: true,
        scrollY: '25vmax',
        paging: true,
        info: true,
        searching: false,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort.json"
        },
    });

    setTimeout(function(){
    $('#GetFechas').DataTable({
        "initComplete": function( settings, json ) {
            $("#GetFechas thead").remove(); 
          },
          "drawCallback": function( settings ) {        
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $('#GetNovedadesFecha').DataTable().ajax.reload();
            $('#resultNov').html('');
        },  
        pagingType: "full",
        lengthMenu: [[1], [1]],
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        dom: '<"d-inline-flex d-flex align-items-center"t<"ml-2"p>><"mt-n3 d-flex justify-content-end"i>',
        ajax: {
            url: "/" + $("#_homehost").val() + "/otrasnov/GetFechas.php",
            type: "POST",
            "data": function(data){
                data.Per     = $("#Per").val();
                data.Tipo    = $("#Tipo").val();
                data.Emp     = $("#Emp").val();
                data.Plan    = $("#Plan").val();
                data.Sect    = $("#Sect").val();
                data.Sec2    = $("#Sec2").val();
                data.Grup    = $("#Grup").val();
                data.Sucur   = $("#Sucur").val();
                data._dr     = $("#_dr").val();
                data._l      = $("#_l").val();
                data.FicONove = $("#FicONove").val();
            },
            error: function() {
                $("#GetFecha_processing").css("display", "none");
            },
        },
        columns: [
        {
            "class": "w80 px-3 border fw4 bg-light radius ls1",
            "data": 'FicFech'
        },
        {
            "class": "w300 px-3 border fw4 bg-light radius",
            "data": 'Dia'
        },
        ],
        paging: true,
        responsive: false,
        info: true,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort2.json"
        },
    });
    $('#GetNovedadesFecha').DataTable({
        "initComplete": function( settings, json ) {
          },
          "drawCallback": function( settings ) {
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $(".custom-select").addClass('text-secondary bg-light');
            $('#Visualizar').prop('disabled', false)
            setTimeout(function(){ 
                $(".Filtros").prop('disabled', false);
             }, 1000);
        },  
        lengthMenu: [[10,25,50,100], [10,25,50,100]],
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        ajax: {
            url: "/" + $("#_homehost").val() + "/otrasnov/GetNovedadesFecha.php",
            type: "POST",
            "data": function(data){
                data._f      = $("#_f").val();
                data.Per     = $("#Per").val();
                data.Tipo    = $("#Tipo").val();
                data.Emp     = $("#Emp").val();
                data.Plan    = $("#Plan").val();
                data.Sect    = $("#Sect").val();
                data.Sec2    = $("#Sec2").val();
                data.Grup    = $("#Grup").val();
                data.Sucur   = $("#Sucur").val();
                data._dr     = $("#_dr").val();
                data._l      = $("#_l").val();
                data.FicONove = $("#FicONove").val();
            },
            error: function() {
                $("#GetNovedadesFecha_processing").css("display", "none");
            },
        },
        columns: [
            {
                "class": "align-middle ls1 ",
                "data": "nov_LegNume"
            }, {
                "class": "align-middle",
                "data": "nov_leg_nombre"
            },
            // {
            //     "class": "align-middle ls1 ",
            //     "data": "Fecha"
            // }, 
            // {
            //     "class": "align-middle",
            //     "data": "nov_nom_dia"
            // },
            {
                "class": "align-middle ls1 ",
                "data": "nov_horario"
            }, {
                "class": "align-middle ls1 text-center",
                "data": "NoveCod"
            }, {
                "class": "align-middle",
                "data": "Novedades"
            }, {
                "class": "align-middle ls1 ",
                "data": "NovValor"
            },
            {
                "class": "align-middle",
                "data": "NoveObserv"
            },
            {
                "class": "align-middle",
                "data": "NoveTipo"
            },
        ],
        scrollX: true,
        scrollCollapse: true,
        scrollY: '25vmax',
        paging: true,
        info: true,
        searching: false,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort.json"
        },
    });
    }, 1000);

$("#Refresh").on("click", function () {
    ActualizaTablas()
});

$("#_dr").change(function () {
    ActualizaTablas()
});
$('#VerPor').html('Visualizar por Fecha')
$("#Visualizar").change(function () {
    // $("#loader").addClass('loader');
    if ($("#Visualizar").is(":checked")) {
        $('#GetFechas').DataTable().ajax.reload();
        $("#GetNovedadesTable").addClass('d-none');
        $("#GetNovedadesFechaTable").removeClass('d-none');
        $("#pagLega").addClass('d-none');
        $("#pagFech").removeClass('d-none')
        // $('#VerPor').html('Visualizar por Legajo')
    } else {       
        $('#GetPersonal').DataTable().ajax.reload();
        $("#GetNovedadesTable").removeClass('d-none');
        $("#GetNovedadesFechaTable").addClass('d-none')
        $("#pagLega").removeClass('d-none')
        $("#pagFech").addClass('d-none')
        // $('#VerPor').html('Visualizar por Fecha')
    }
});