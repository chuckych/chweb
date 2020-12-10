// $(".Filtros").prop('disabled', true);
function ActualizaTablas(){
        $('#GetPersonal').DataTable().ajax.reload();
        $('#Per2').addClass('d-none')
        $('.pers_legajo').removeClass('d-none')
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

   $('#GetPersonal').DataTable({
        "initComplete": function( settings, json ) {
        },
        "drawCallback": function( settings ) {
            $("#GetPersonal thead").remove();  
            $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $("#Refresh").prop('disabled', false);
            $('#GetHorarios').DataTable().ajax.reload();
            fadeInOnly('#GetHorarios');
        },  
        pagingType: "full",
        lengthMenu: [[1], [1]],
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        dom: '<"d-inline-flex d-flex align-items-center"t<"ml-2"p>><"mt-n3 d-flex justify-content-end"i>',
        ajax: {
            url: "/" + $("#_homehost").val() + "/informes/horasign/GetPersonal.php",
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
            "url": "../../js/DataTableSpanishShort2.json"
        },
    });
    $('#GetHorarios').DataTable({
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
        },
        lengthMenu: [[31, 60, 90, 120], [31, 60, 90, 120]],
        Processing: true,
        // serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        ajax: {
            url: "/" + $("#_homehost").val() + "/informes/horasign/GetHorarios.php",
            type: "POST",
            dataSrc:"horarios",
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
            },
            error: function() {
                // $("#GetHorarios").css("display", "none");
            },
        },
        columns: [
            {
                "class": "align-middle w50",
                "data": "Legajo"
            },
            {
                "class": "align-middle w200",
                "data": "Nombre"
            },
            {
                "class": "align-middle",
                "data": "Fecha"
            },
            {
                "class": "align-middle",
                "data": "Dia"
            },
            {
                "class": "ls1 fw4 align-middle bg-light w100",
                "data": "Horario"
            },
            {
                "class": "text-nowrap ls1 align-middle",
                "data": "CodHorario"
            },
            {
                "class": "align-middle",
                "data": "Descripcion"
            }, 
        ],
        scrollX: true,
        scrollCollapse: true,
        scrollY: '20vmax',
        paging: true,
        info: true,
        searching: false,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanishShort2.json"
        },
    });

$("#Refresh").on("click", function () {
    ActualizaTablas()
});

$("#_dr").change(function () {
    ActualizaTablas()
});