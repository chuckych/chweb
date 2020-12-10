/** Variables para las notificaciones de pantalla */
var NotifDelay     = 2000;
var NotifOffset    = 0;
var NotifOffsetX   = 0;
var NotifOffsetY   = 0;
var NotifZindex    = 9999;
var NotifMouseOver = 'pause'
var NotifEnter     = 'animate__animated animate__fadeInDown';
var NotifExit      = 'animate__animated animate__fadeOutUp';
var NotifAlign     = 'center';
$(document).ready(function () {
    $("#submit").html("Ingresar Cierres");
    $(".alta_cierre").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            // async : false,
            beforeSend: function (data) {
                $("#submit").prop("disabled", true);
                $("#respuetatext").html("Generando Cierres");
                $("#respuetatext").addClass("animate__animated animate__fadeIn");
                $("#respuesta").addClass("alert-info");
                $("#respuesta").removeClass("d-none");
                $("#respuesta").removeClass("alert-success");
                $("#respuesta").removeClass("alert-danger");
            },
            success: function (data) {
                if (data.status == "ok") {
                    $("#cierre").prop('disabled', false);
                    // $("#respuetatext").removeClass("animate__animated animate__fadeIn");
                    $("#respuetatext").html("");
                    $("#submit").prop("disabled", false);
                    $("#submit").html("Ingresar Cierres");
                    $("#respuesta").removeClass("alert-success");
                    $("#respuesta").removeClass("alert-danger");
                    $("#respuesta").removeClass("alert-info");
                    $('input[type="checkbox"]').prop('checked', false)
                    $.notify(`<span class='fonth fw4'><span class="">${data.dato}</span></span>`, {
                        type: 'success',
                        z_index: NotifZindex,
                        delay: NotifDelay,
                        offset: NotifOffset,
                        mouse_over: NotifMouseOver,
                        placement: {
                            align: NotifAlign
                        },
                        animate: {
                            enter: NotifEnter,
                            exit: NotifExit
                        }
                    });
                    $('#GetPersonal').DataTable().ajax.reload();
                } else {
                    $("#respuetatext").html("");
                    // $("#respuetatext").removeClass("animate__animated animate__fadeIn");
                    $("#submit").prop("disabled", false);
                    $("#submit").html("Ingresar Cierres");
                    $("#respuesta").removeClass("alert-success");
                    $("#respuesta").removeClass("alert-info");
                    $("#respuesta").removeClass("alert-danger");
                    $.notify(`<span class='fonth fw4'><span class="">${data.dato}</span></span>`, {
                        type: 'danger',
                        z_index: NotifZindex,
                        delay: NotifDelay,
                        offset: NotifOffset,
                        mouse_over: NotifMouseOver,
                        placement: {
                            align: NotifAlign
                        },
                        animate: {
                            enter: NotifEnter,
                            exit: NotifExit
                        }
                    });
                    $('#GetPersonal').DataTable().ajax.reload();
                }
            }
        });
        e.stopImmediatePropagation();
    });
});

table = $('#GetPersonal').DataTable({
    "initComplete": function( settings, json ) {
        $('div.loader').remove();
        $('input[type="checkbox"]').prop('checked', true)
        $("#EliminaCierre").prop('checked', false)
      },
    lengthMenu: [[10, 25, 50, 100, 200, 300], [10, 25, 50, 100, 200, 300]],
    bProcessing: true,
    serverSide: true,
    deferRender: true,
    searchDelay: 1500,
    ajax: {
        url: "../personal/?p=array_personal.php",
        type: "GET",
        "data": function(data){
            // data._sec = function() { return $("#Sect").val()},
            data.Per= $("#Per").val();
            data.Tipo= $("#Tipo").val();
            data.Emp= $("#Emp").val();
            data.Plan= $("#Plan").val();
            data.Sect= $("#Sect").val();
            data.Sec2= $("#Sec2").val();
            data.Grup= $("#Grup").val();
            data.Sucur= $("#Sucur").val();
            data._c  = $("#_c").val();
            data._r  = $("#_r").val();
            data.Modulo  = "Cierres"
        },
        error: function() {
            $("#GetPersonal_processing").css("display", "none");
        },
    },
    columns: [
    {
        "class": "align-middle animate__animated animate__fadeIn",
        "data": 'check'
    },
    {
        "class": "align-middle animate__animated animate__fadeIn",
        "data": 'pers_legajo2'
    },
    {
        "class": "align-middle animate__animated animate__fadeIn",
        "data": 'pers_nombre2'
    },
    {
        "class": "align-middle animate__animated animate__fadeIn ls1",
        "data": 'FechaCierre'
    },

    ],
    scrollY: '335px',
    scrollX: true,
    // scrollCollapse: false,
    paging: true,
    responsive: false,
    searching: false,
    info: true,
    ordering: false,
    language: {
        "url": "../js/DataTableSpanishShort.json"
    },
});

table.on( 'page.dt',   function () { 
    setTimeout(function(){ 
        $('input[type="checkbox"]').prop('checked', true)
        $("#EliminaCierre").prop('checked', false)
     }, 1000);
 });

table.on('page', function () {
    $('input[type="checkbox"]').prop('checked', false);
} );
   // Handle click on "Select all" control
   $('#Personal-select-all').on('click', function(){
    // Check/uncheck all checkboxes in the table
    var rows = table.rows({ 'search': 'applied' }).nodes();
    $('input[type="checkbox"]', rows).prop('checked', this.checked);
 });

 // Handle click on checkbox to set state of "Select all" control
 $('#GetPersonal tbody').on('change', 'input[type="checkbox"]', function(){
    // If checkbox is not checked
    if(!this.checked){
       var el = $('#Personal-select-all').get(0);
       // If "Select all" control is checked and has 'indeterminate' property
       if(el && el.checked && ('indeterminate' in el)){
          // Set visual state of "Select all" control 
          // as 'indeterminate'
          el.indeterminate = true;
       }
    }
 });