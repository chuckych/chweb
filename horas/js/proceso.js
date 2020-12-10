// $(".Filtros").prop('disabled', true);
function ActualizaTablas(){
    if ($("#Visualizar").is(":checked")) {
        $('#GetFechas').DataTable().ajax.reload();
    } else {
        $('#GetPersonal').DataTable().ajax.reload();
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
        if (map[13]) { /** Enter */
            ActualizaTablas()
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
$("#GetHorasFechaTable").addClass('d-none');
$('#Visualizar').prop('disabled', true)

    $('#GetPersonal').DataTable({
        "initComplete": function( settings, json ) {
        },
        "drawCallback": function( settings ) {
            $("#GetPersonal thead").remove();  
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $('#GetHoras').DataTable().ajax.reload();
            $('#GetHorasTotales').DataTable().ajax.reload();
        },  
        pagingType: "full",
        lengthMenu: [[1], [1]],
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        dom: '<"d-inline-flex d-flex align-items-center"t<"ml-2"p>><"mt-n3 d-flex justify-content-end"i>',
        ajax: {
            url: "/" + $("#_homehost").val() + "/horas/GetPersonalFichas1.php",
            type: "POST",
            "data": function(data){
                data._l      = $("#_l").val();
                data.Per     = $("#Per").val();
                data.Tipo    = $("#Tipo").val();
                data.Emp     = $("#Emp").val();
                data.Plan    = $("#Plan").val();
                data.Sect    = $("#Sect").val();
                data.Sec2    = $("#Sec2").val();
                data.Grup    = $("#Grup").val();
                data.Sucur   = $("#Sucur").val();
                data._dr     = $("#_dr").val();
                data.Thora   = $("#Thora").val();
                data.SHoras  = $("#SHoras").val();
                data.HoraMin = $("#HoraMin").val();
                data.HoraMax = $("#HoraMax").val();
            },
            error: function() {
                $("#GetPersonal_processing").css("display", "none");
            },
        },
        columns: [
        {
            "class": "w80 px-3 border fw4 bg-light radius",
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
    $('#GetHoras').DataTable({
        "initComplete": function( settings, json ) {
            $("#Refresh").prop('disabled', false);
            $('#trash_all').removeClass('invisible');
            fadeInOnly('#pagLega')
            fadeInOnly('#GetHorasTable')
        },
        "drawCallback": function( settings ) {
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $(".custom-select").addClass('text-secondary bg-light');
            $('#pagLega').removeClass('invisible')
            $('#GetHorasTable').removeClass('invisible')
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
            url: "/" + $("#_homehost").val() + "/horas/GetHoras.php",
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
                data.Thora = $("#Thora").val();
                data.SHoras = $("#SHoras").val();
                data.HoraMin = $("#HoraMin").val();
                data.HoraMax = $("#HoraMax").val();
                data.Calculos = $("#Calculos").val();
            },
            error: function() {
                $("#GetHoras_processing").css("display", "none");
            },
        },
        columns: [
            { 
                'class': 'ls1',
                'data': 'FicFech',
            },
            { 
                'class': '',
                'data': 'Dia',
            },
            { 
                'class': 'ls1',
                'data': 'Horario',
            },
            { 
                'class': 'text-center',
                'data': 'Hora',
            },
            { 
                'class': '',
                'data': 'HoraDesc',
            },
            { 
                'class': 'ls1 text-center',
                'data': 'FicHsAu',
            },
            { 
                'class': 'ls1 bg-light fw4 text-center',
                'data': 'FicHsAu2',
            },
            { 
                'class': '',
                'data': 'Observ',
            },
            { 
                'class': '',
                'data': 'DescMotivo',
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
            "url": "../js/DataTableSpanishShort2.json"
        },
    });
    $('#GetHorasTotales').DataTable({
        "initComplete": function( settings, json ) {
            $("#Refresh").prop('disabled', false);
            $('#trash_all').removeClass('invisible');
        },
        "drawCallback": function( settings ) {
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $(".custom-select").addClass('text-secondary bg-light');
            setTimeout(function(){ 
                $(".Filtros").prop('disabled', false);
             }, 1000);
             $('#GetHorasTotalesTable').removeClass('invisible')
        },
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        ajax: {
            url: "/" + $("#_homehost").val() + "/horas/GetHorasTotales.php",
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
                data.Thora = $("#Thora").val();
                data.SHoras = $("#SHoras").val();
                data.HoraMin = $("#HoraMin").val();
                data.HoraMax = $("#HoraMax").val();
                data.Calculos = $("#Calculos").val();
            },
            error: function() {
                $("#GetHorasNew_processing").css("display", "none");
            },
        },
        columns: [
            { 
                'class': 'ls1 text-center',
                'data': 'FicHora',
            },
            { 
                'class': 'w150',
                'data': 'THoDesc',
            },
            { 
                'class': 'ls1 text-center',
                'data': 'FicHsAu',
            },
            { 
                'class': 'ls1 text-center bg-light fw4',
                'data': 'FicHsAu2',
            },
        ],
        // scrollY: '450px',
        scrollX: true,
        scrollCollapse: false,
        paging: false,
        responsive: false,
        info: false,
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
            // $( "#GetHorasFechaTable" ).append( "<div class='loader2'></div>" );
            $('#GetHorasFecha').DataTable().ajax.reload();
            $('#GetHorasFechaTotales').DataTable().ajax.reload();
            // $(".loader2").fadeOut("slow");
        },  
        pagingType: "full",
        lengthMenu: [[1], [1]],
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        dom: '<"d-inline-flex d-flex align-items-center"t<"ml-2"p>><"mt-n3 d-flex justify-content-end"i>',
        ajax: {
            url: "/" + $("#_homehost").val() + "/horas/GetFechasFichas1.php",
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
                data.Thora = $("#Thora").val();
                data.SHoras = $("#SHoras").val();
                data.HoraMin = $("#HoraMin").val();
                data.HoraMax = $("#HoraMax").val();
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
    $('#GetHorasFecha').DataTable({
        "initComplete": function( settings, json ) {
          },
          "drawCallback": function( settings ) {
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $(".custom-select").addClass('text-secondary bg-light');
            $('#Visualizar').prop('disabled', false)
            $('#GetHorasFechaTable').removeClass('invisible')
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
            url: "/" + $("#_homehost").val() + "/horas/GetHorasFecha.php",
            type: "POST",
            "data": function(data){
                data._f       = $("#_f").val();
                data.Per      = $("#Per").val();
                data.Tipo     = $("#Tipo").val();
                data.Emp      = $("#Emp").val();
                data.Plan     = $("#Plan").val();
                data.Sect     = $("#Sect").val();
                data.Sec2     = $("#Sec2").val();
                data.Grup     = $("#Grup").val();
                data.Sucur    = $("#Sucur").val();
                data._dr      = $("#_dr").val();
                data._l       = $("#_l").val();
                data.Thora    = $("#Thora").val();
                data.SHoras   = $("#SHoras").val();
                data.HoraMin  = $("#HoraMin").val();
                data.HoraMax  = $("#HoraMax").val();
                data.Calculos = $("#Calculos").val();
            },
            error: function() {
                $("#GetHorasFecha_processing").css("display", "none");
            },
        },
        columns: [
            { 
                'class': 'ls1',
                'data': 'Legajo',
            },
            { 
                'class': 'ApNo',
                'data': 'Nombre',
            },
            { 
                'class': 'ls1',
                'data': 'Horario',
            },
            { 
                'class': 'text-center',
                'data': 'Hora',
            },
            { 
                'class': '',
                'data': 'HoraDesc',
            },
            { 
                'class': 'ls1 text-center',
                'data': 'FicHsAu',
            },
            { 
                'class': 'ls1 bg-light fw4 text-center',
                'data': 'FicHsAu2',
            },
            { 
                'class': '',
                'data': 'Observ',
            },
            { 
                'class': '',
                'data': 'DescMotivo',
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
            "url": "../js/DataTableSpanishShort2.json"
        },
    });
    $('#GetHorasFechaTotales').DataTable({
        "initComplete": function( settings, json ) {
            $(".CollapseFiltros").prop('disabled', false);
            $("#Refresh").prop('disabled', false);
            $('#trash_all').removeClass('invisible');
        },
        "drawCallback": function( settings ) {
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $(".custom-select").addClass('text-secondary bg-light');
            setTimeout(function(){ 
                $(".Filtros").prop('disabled', false);
             }, 1000);
            
        },
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        ajax: {
            url: "/" + $("#_homehost").val() + "/horas/GetHorasFechaTotales.php",
            type: "POST",
            "data": function(data){
                data._f = $("#_f").val();
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
                data.Thora = $("#Thora").val();
                data.SHoras = $("#SHoras").val();
                data.HoraMin = $("#HoraMin").val();
                data.HoraMax = $("#HoraMax").val();
                data.Calculos = $("#Calculos").val();
            },
            error: function() {
                $("#GetHorasNew_processing").css("display", "none");
            },
        },
        columns: [
            { 
                'class': 'ls1 text-center',
                'data': 'FicHora',
            },
            { 
                'class': 'w150',
                'data': 'THoDesc',
            },
            { 
                'class': 'ls1 text-center',
                'data': 'FicHsAu',
            },
            { 
                'class': 'ls1 text-center bg-light fw4',
                'data': 'FicHsAu2',
            },
        ],
        // scrollY: '450px',
        scrollX: true,
        scrollCollapse: false,
        paging: false,
        responsive: false,
        info: false,
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
        $("#GetHorasTable").addClass('d-none');
        $("#GetHorasFechaTable").removeClass('d-none');
        $("#GetHorasTotalesTable").addClass('d-none');
        $("#pagLega").addClass('d-none');
        $("#pagFech").removeClass('d-none')
        // $('#VerPor').html('Visualizar por Legajo')
    } else {       
        $('#GetPersonal').DataTable().ajax.reload();
        $("#GetHorasTable").removeClass('d-none');
        $("#GetHorasFechaTable").addClass('d-none')
        $("#GetHorasTotalesTable").removeClass('d-none')
        $("#pagLega").removeClass('d-none')
        $("#pagFech").addClass('d-none')
        // $('#VerPor').html('Visualizar por Fecha')
    }
});