$(document).on("click", ".Detalle", function (e) {

    e.preventDefault();
    $('#HSHechas-tab').tab('show');
    $('#Detalle').modal('show');
    $("#GetHoras thead").addClass('d-none'); 
    $("#GetNovedades thead").addClass('d-none'); 
    $("#GetNovedades2 thead").addClass('d-none'); 
    $("#GetNovedades3 thead").addClass('d-none'); 
    $("#GetNovedades4 thead").addClass('d-none'); 
    $('#Detalle').modal('handleUpdate')

    var FicLega  = $(this).attr('data');
    var DRFech   = $(this).attr('data1');
    var Nombre   = $(this).attr('data2');
    var Legajo   = $(this).attr('data3');
    var FechaIni = $(this).attr('data4');
    var FechaFin = $(this).attr('data5');
    var HorasEx  = $(this).attr('data6');
    var JorRed1  = $(this).attr('data7');
    var CtaCte   = $(this).attr('data8');
    var Franco1   = $(this).attr('data9');
    var Franco2   = $(this).attr('data11');
    var JorRed2   = $(this).attr('data10');

    $('#detalle-modal-title').html("<span class='fw4'>Cta. Cte. Horas</span>: <span class='fontq ls1'>Del "+FechaIni+" al "+FechaFin+"</span><br /><span class='fontq fw4'>" + Nombre + ". Legajo: </span><span class='ls1 fontq fw4'>"+Legajo+"</span><br /><span class='fontq fw4'>Resultado Cta Cte: <span class='ls1'>"+CtaCte+"<//span></span>");

    $("#HSHechas-tab").html("<span class='fontq align-middle'>Hs. Hechas: "+ HorasEx+"</span>")
    $("#JorReduc-tab").html("<span class='fontq align-middle'>Jor. Resta: "+ JorRed1+"</span>")
    $("#JorReduc2-tab").html("<span class='fontq align-middle'>Jor. Suma: "+ JorRed2+" </span>")
    $("#Francos-tab").html("<span class='fontq align-middle'>Francos Resta: "+ Franco1+"</span>")
    $("#Francos2-tab").html("<span class='fontq align-middle'>Francos Suma: "+ Franco2+"</span>")

    $('#FicLega').val(FicLega);
    $('#DRFech').val(DRFech);

    $('#GetHoras').DataTable({
        "initComplete": function( settings, json ) {
            $('#GetHoras tr').addClass('animate__animated animate__fadeIn');
          },
          "drawCallback": function( settings ) {
            // $("#GetHoras thead").remove(); 
            // $("#GetHoras thead").addClass('d-none'); 
        },  
        bProcessing: true,
        // deferRender: true,
        "ajax": {
            url: "GetHoras.php",
            type: "POST",
            dataSrc: "Horas",
            'data': {
                FicLega,
                DRFech,
            },
        },
        columns: [
            { "class": "align-middle ls1 py-1", "data": "Dia" },
            { "class": "align-middle ls1", "data": "Fecha" },
            // { "class": "align-middle ls1", "data": "Cod" },
            { "class": "align-middle", "data": "Descripcion" },
            { "class": "align-middle ls1 fw5", "data": "HorasEx" },
            // { "class": "align-middle", "data": "DescMotivo" },
            // { "class": "align-middle", "data": "Observ" },
            { "class": "align-middle w-100", "data": "null" },

        ],
        dom: 'rtip',
        // paging: true,
        scrollY: '15vmax',
        scrollX: true,
        scrollCollapse: true,
        searching: false,
        // info: true,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanishTotal.json"
        },
    });
    $('#GetNovedades').DataTable({
        "initComplete": function( settings, json ) {
            $('#GetNovedades tr').addClass('animate__animated animate__fadeIn');
          },
        bProcessing: true,
        deferRender: true,
        "ajax": {
            url: "GetNovedades.php",
            type: "POST",
            dataSrc: "Novedades",
            'data': {
                FicLega,
                DRFech
            },
        },    
        columns: [
            { "class": "align-middle py-1", "data": "Dia" },
            { "class": "align-middle ls1", "data": "Fecha" },
            { "class": "align-middle", "data": "Descripcion" },
            { "class": "align-middle ls1 fw5", "data": "Horas" },
            { "class": "align-middle w-100", "data": "null" }
        ],
        dom: 'rtip',
        scrollY: '15vmax',
        scrollX: true,
        scrollCollapse: true,
        // searching: false,
        // info: true,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanishTotal.json"
        },
    });
    $('#GetNovedades2').DataTable({
        "initComplete": function( settings, json ) {
            $('#GetNovedades2 tr').addClass('animate__animated animate__fadeIn');
          },
        bProcessing: true,
        deferRender: true,
        "ajax": {
            url: "GetNovedades2.php",
            type: "POST",
            dataSrc: "Novedades",
            'data': {
                FicLega,
                DRFech
            },
        },    
        columns: [
            { "class": "align-middle py-1", "data": "Dia" },
            { "class": "align-middle ls1", "data": "Fecha" },
            { "class": "align-middle", "data": "Descripcion" },
            { "class": "align-middle ls1 fw5", "data": "Horas" },
            { "class": "align-middle w-100", "data": "null" }
        ],
        dom: 'rtip',
        scrollY: '15vmax',
        scrollX: true,
        scrollCollapse: true,
        // searching: false,
        // info: true,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanishTotal.json"
        },
    });
    $('#GetNovedades3').DataTable({
        "initComplete": function( settings, json ) {
            $('#GetNovedades3 tr').addClass('animate__animated animate__fadeIn');
          },
        bProcessing: true,
        deferRender: true,
        "ajax": {
            url: "GetNovedades3.php",
            type: "POST",
            dataSrc: "Novedades",
            'data': {
                FicLega,
                DRFech
            },
        },    
        columns: [
            { "class": "align-middle py-1", "data": "Dia" },
            { "class": "align-middle ls1", "data": "Fecha" },
            { "class": "align-middle", "data": "Descripcion" },
            { "class": "align-middle ls1 fw5", "data": "Horas" },
            { "class": "align-middle w-100", "data": "null" }
        ],
        dom: 'rtip',
        scrollY: '15vmax',
        scrollX: true,
        scrollCollapse: true,
        // searching: false,
        // info: true,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanishTotal.json"
        },
    });
    $('#GetNovedades4').DataTable({
        "initComplete": function( settings, json ) {
            $('#GetNovedades4 tr').addClass('animate__animated animate__fadeIn');
          },
        bProcessing: true,
        deferRender: true,
        "ajax": {
            url: "GetNovedades4.php",
            type: "POST",
            dataSrc: "Novedades",
            'data': {
                FicLega,
                DRFech
            },
        },    
        columns: [
            { "class": "align-middle py-1", "data": "Dia" },
            { "class": "align-middle ls1", "data": "Fecha" },
            { "class": "align-middle", "data": "Descripcion" },
            { "class": "align-middle ls1 fw5", "data": "Horas" },
            { "class": "align-middle w-100", "data": "null" }
        ],
        dom: 'rtip',
        scrollY: '15vmax',
        scrollX: true,
        scrollCollapse: true,
        // searching: false,
        // info: true,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanishTotal.json"
        },
    });
    

});
$('#Detalle').on('hidden.bs.modal', function () {
    $('#GetHoras').DataTable().clear().draw().destroy();
    $('#GetNovedades').DataTable().clear().draw().destroy();
    $('#GetNovedades2').DataTable().clear().draw().destroy();
    $('#GetNovedades3').DataTable().clear().draw().destroy();
    $('#GetNovedades4').DataTable().clear().draw().destroy();
});

