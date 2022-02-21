
$('.select2').select2({
    minimumResultsForSearch: -1,
    placeholder: "Seleccionar"
});

$(function () {
    var center = new google.maps.LatLng(-38.416097, -63.616671);
    const image = "../../img/marker.png";

    $("#geocomplete").geocomplete({
        map: ".map_canvas",
        details: "form",
        markerOptions: {
            draggable: true,
            icon: image
        },
        mapOptions: {
            scrollwheel: true,
            scaleControl: true,
            zoomControl: true,
            streetViewControl: false,
            fullscreenControl: true,
            disableDefaultUI: true
        },
        types: ['geocode', 'establishment'],
        country: 'ar'
    });

    var map = $("#geocomplete").geocomplete("map")

    map.setCenter(center);
    map.setZoom(4);
    autoselect: true

    $("#geocomplete").bind("geocode:dragged", function (event, latLng) {
        $("input[name=lat]").val(latLng.lat());
        $("input[name=lng]").val(latLng.lng());
        $("#reset").show();
    });

    $("#reset").click(function () {
        $("#geocomplete").geocomplete("resetMarker");
        $("#geocomplete").geocomplete("map");
        $("#reset").hide();
        return false;
    });

    $("#find").click(function () {
        $("#geocomplete").trigger("geocode");
    }).click();

});

function clean() {
    $('#table-zonas').DataTable().search('').draw();
    $("#divtable").removeClass('col-sm-6')
    $("#divmap").removeClass('col-sm-6')
    $(".divmap").addClass('d-none')
    $("#geocomplete").val("");
    $("input[name=lat]").val('');
    $("input[name=lng]").val('');
    $("input[name=nombre]").val('');
    $("input[name=metros]").val('');
    $("input[name=alta_zona]").val('true');
    $("#reset").hide();
}
$("#Refresh").on("click", function () {
    CheckSesion()
    $('#table-zonas').DataTable().ajax.reload();
    $("tbody").addClass("opa2");
    $("#Refresh").prop("disabled", true);
    $("#Refresh").html("Actualizando!.");
    clean()
    marcadores = [];
});

let marcadores = new Array();

let table = $('#table-zonas').DataTable({
    "initComplete": function (settings, json) {

    },
    "drawCallback": function (settings) {
        $("tbody").removeClass("opa2");
        $("#Refresh").prop("disabled", false);
        $("#Refresh").html("Actualizar Grilla");
    },
    bProcessing: true,
    // search:{ search:("HR")},
    ajax: {
        url: "array_zonas.php",
        type: "POST",
        dataSrc: "zonas",
        "data": function (data) { },
    },
    createdRow: function (row, data, dataIndex) {
        $(row).addClass('animate__animated animate__fadeIn align-middle');
    },
    columns: [{
        "class": 'w30',
        "data": "ver",
    },
    {
        "class": 'w200',
        "data": "name",
    },
    {
        "class": 'w40',
        "data": "map_size",
    },
    {
        "class": 'w40',
        "data": "eliminar",
    },
    {
        "class": '',
        "data": "null",
    },

    ],
    deferRender: true,
    lengthMenu: [[10, 50, 100, 300, 500, 700, 900, 1000], [10, 50, 100, 300, 500, 700, 900, 1000]],
    paging: true,
    searching: true,
    scrollY: "50vh",
    scrollX: true,
    scrollCollapse: true,
    info: true,
    ordering: false,
    language: {
        "url": "../../js/DataTableSpanishShort2.json"
    },

});

let getMarcadores = () => {
    marcadores = [];
    firstLat = '';
    firstLng = '';
    $("#table-zonas td .marcador").each(function () {
        obj = JSON.parse($(this).attr("marcador"));
        let divMarker = `<div class='p-3 shadow-sm bg-white'><label class='w40 fontq'>Zona: </label> <span class='font-weight-bold'>${obj.name}</span><br><label class='w40 fontq'>Radio: </label> <span class='font-weight-bold'>${obj.map_size}</span></div>`
        marcadores.push(
            [obj.name, parseFloat(obj.lat), parseFloat(obj.lng), divMarker],
        );
        firstLat = parseFloat(obj.lat);
        firstLng = parseFloat(obj.lng);
    });
    return marcadores;
}

table.on("draw.dt", function (e, settings) {
    $('#map').html('').removeClass('shadow').css('height', '0px');
    $('#mapTitle').html(' ');
    if (getMarcadores() != '') {
        $('#btnVerMarcadores').html(`<a href="#positionMap" class="btn btn-custom fontq" id="VerMarcadores"><span class="mr-1">Mapa</span><i class="bi bi-pin-map-fill"></i></a>`)
    }
});

$(document).on("click", ".EliminaZona", function (e) {
    CheckSesion()
    var _tk = $(this).attr('data4');
    var _nombre = $(this).attr('data5');

    $("#_nombreZona").html(_nombre)
    $('#d_tk').val(_tk)
    $('#d_nombre').val(_nombre)

});

function initMap() {

    var lati = parseFloat($('#latitud').val())
    var long = parseFloat($('#longitud').val())
    var zona = ($('#zona').val())
    const radio = parseFloat($('#map_size').val())

    const myLatLng = {
        lat: lati,
        lng: long
    };

    const map = new google.maps.Map(document.getElementById("maps"), {
        zoom: 16,
        center: myLatLng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        zoomControl: true,
        mapTypeControl: false,
        scaleControl: true,
        streetViewControl: false,
        rotateControl: false,
        fullscreenControl: true,
        mapTypeId: "terrain",
        // gestureHandling: "cooperative", /** para anular el zoom del scroll */
    });
    const contentString = '<div id="content"><span class="font-weight-bold text-secondary">' + zona + "</span></div>";

    const infowindow = new google.maps.InfoWindow({
        content: contentString,
    });

    const image = "../../img/marker.png";
    const marker = new google.maps.Marker({
        position: myLatLng,
        map,
        // animation: google.maps.Animation.DROP,
        // draggable: true,
        icon: image,
        title: zona
    })

    marker.addListener("click", () => {
        infowindow.open(map, marker);
    });

    var sunCircle = {
        strokeColor: "#0388D1",
        strokeOpacity: 1,
        strokeWeight: 1,
        fillColor: "#0388D1",
        fillOpacity: 0.25,
        map: map,
        center: myLatLng,
        radius: radio // en metros
    };
    cityCircle = new google.maps.Circle(sunCircle)
    cityCircle.bindTo('center', marker, 'position');
}
$(document).on("click", "#Zona", function (e) {
    CheckSesion()
    $('#VerZonas').addClass('d-none')
    $('#rowNuevaZona').removeClass('d-none')
    $('#geocomplete').attr('autocomplete', '_chweb_off');
    fadeInOnly('#rowNuevaZona')
});
$(document).on("click", "#cancelZone", function (e) {
    CheckSesion()
    $('#VerZonas').removeClass('d-none')
    $('#rowNuevaZona').addClass('d-none')
    fadeInOnly('#VerZonas')
    clean()
});

$(document).on("click", ".verZone", function (e) {
    CheckSesion()
    fadeInOnly('#divmap')

    var _lat = $(this).attr('data');
    var _lng = $(this).attr('data1');
    var _name = $(this).attr('data2');
    var _map_size = $(this).attr('data3');

    $('#latitud').val(_lat)
    $('#longitud').val(_lng)
    $('#zona').val(_name)
    $('#map_size').val(_map_size)

    $("#divtable").addClass('col-sm-6')
    $("#divmap").addClass('col-sm-6')
    $(".divmap").removeClass('d-none')
    $("#MarkerName").html('')
    $("#MarkerName").html(_name)
    $("#NombreZona").html('Zona: <span class="font-weight-bold text-secondary">' + _name + '</span> ')
    $("#MarkerMapSize").html('')
    $("#MarkerMapSize").html('Radio: ' + _map_size)
    $("#RadioZona").html(' Radio: <span class="font-weight-bold text-secondary">' + _map_size + ' mts.</span>')

    $('#_lat').val(_lat)
    $('#_lng').val(_lng)
    $('#_name').val(_name)
    $('#_map_size').val(_map_size)

    $('.marker').attr('data-id', _lat)
    $('.marker').attr('data-lat', _lat)
    $('.marker').attr('data-lng', _lng)

    initMap()
});

$("#DZona").bind("submit", function (e) {
    CheckSesion()
    e.preventDefault();
    $.ajax({
        type: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        // dataType: "json",
        beforeSend: function (data) {
            $("#btnsi").prop("disabled", true);
            $("#btnsi").html("Eliminando.!");
            $("#Refresh").prop("disabled", true);
            $("#Refresh").html("Actualizando!.");
        },
        success: function (data) {
            if (data.status == "ok") {
                clean()
                CheckSesion()
                $('#table-zonas').DataTable().ajax.reload();
                $("tbody").addClass("opa2");
                $("#btnsi").prop("disabled", false);
                $("#btnsi").html("S&iacute;");
                $("#Refresh").prop("disabled", true);
                $("#Refresh").html("Actualizando!.");
                $('#EliminaZona').modal('hide');

            } else {
                CheckSesion()
                $('#table-zonas').DataTable().ajax.reload();
                $("tbody").addClass("opa2");
                $("#btnsi").prop("disabled", false);
                $("#btnsi").html("S&iacute;");
                $("#Refresh").prop("disabled", false);
                $("#Refresh").html("Actualizar Grilla");
                $('#EliminaZona').modal('hide');

            }
        },
        error: function () {
            CheckSesion()
            $('#table-zonas').DataTable().ajax.reload();
            $("tbody").addClass("opa2");
            $("#btnsi").prop("disabled", false);
            $("#btnsi").html("S&iacute;");
            $("#Refresh").prop("disabled", false);
            $("#Refresh").html("Actualizar Grilla");
            $('#EliminaZona').modal('hide');

        }
    });
});

$("#CrearZona").bind("submit", function (e) {
    e.preventDefault();
    CheckSesion()
    $.ajax({
        type: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        // dataType: "json",
        beforeSend: function (data) {
            $("#btnSubmitZone").prop("disabled", true);
            $("#btnSubmitZone").html("Creando Zona.!");
        },
        success: function (data) {
            if (data.status == "ok") {
                CheckSesion()
                $('#table-zonas').DataTable().search(data.zona).draw();
                $('#table-zonas').DataTable().ajax.reload();
                $("#divtable").addClass('col-sm-6')
                $("#divmap").addClass('col-sm-6')
                $(".divmap").removeClass('d-none')
                $('#zona').val(data.zona)
                $('#map_size').val(data.radio)
                $('#latitud').val(data.lat)
                $('#longitud').val(data.lng)
                $("#NombreZona").html('Zona: <span class="font-weight-bold text-secondary">' + data.zona + '</span> ')
                $("#RadioZona").html(' Radio: <span class="font-weight-bold text-secondary">' + data.radio + ' mts.</span>')
                initMap()
                $("#Refresh").prop("disabled", true);
                $("#Refresh").html("Actualizando!");
                $("#btnSubmitZone").prop("disabled", false);
                $("#btnSubmitZone").html("Crear Zona");
                $("#geocomplete").val("");
                $("input[name=lat]").val('');
                $("input[name=lng]").val('');
                $("input[name=nombre]").val('');
                $("input[name=metros]").val('');
                $("#reset").hide();
                $('#VerZonas').removeClass('d-none')
                $('#rowNuevaZona').addClass('d-none')
                fadeInOnly('#VerZonas')

            } else {
                CheckSesion()
                $('#table-zonas').DataTable().ajax.reload();
                $("#Refresh").prop("disabled", true);
                $("#Refresh").html("Actualizando!");
                $("#btnSubmitZone").prop("disabled", false);
                $("input[name=lat]").val('');
                $("input[name=lng]").val('');
                $("input[name=nombre]").val('');
                $("input[name=metros]").val('');
                $("#reset").hide();
            }
        },
        error: function () {
            CheckSesion()
            $('#table-zonas').DataTable().ajax.reload();
            $("#Refresh").prop("disabled", true);
            $("#Refresh").html("Actualizando!");
            $("#btnSubmitZone").prop("disabled", false);
            $("input[name=lat]").val('');
            $("input[name=lng]").val('');
            $("input[name=nombre]").val('');
            $("input[name=metros]").val('');
            $("#reset").hide();
        }
    });
});

$(".selectjs_cuentaToken").select2({
    multiple: false,
    language: "es",
    placeholder: "Cambiar de Cuenta",
    minimumInputLength: '0',
    minimumResultsForSearch: 5,
    maximumInputLength: '10',
    selectOnClose: false,
    language: {
        noResults: function () {
            return 'No hay resultados..'
        },
        inputTooLong: function (args) {
            var message = 'Máximo ' + '10' + ' caracteres. Elimine ' + overChars + ' caracter';
            if (overChars != 1) {
                message += 'es'
            }
            return message
        },
        searching: function () {
            return 'Buscando..'
        },
        errorLoading: function () {
            return 'Sin datos..'
        },
        inputTooShort: function () {
            return 'Ingresar ' + '0' + ' o mas caracteres'
        },
        maximumSelected: function () {
            return 'Puede seleccionar solo una opción'
        }
    },
    ajax: {
        url: "../GetTokenCuenta.php",
        dataType: "json",
        type: "POST",
        // delay: opt2["delay"],
        data: function (params) {
            return {
            }
        },
        processResults: function (data) {
            return {
                results: data
            }
        },
    }
});
$('.selectjs_cuentaToken').on('select2:select', function (e) {
    CheckSesion()
    $("#RefreshToken").submit();
    $('#map').html('').removeClass('shadow').css('height', '0px');
});
$("#RefreshToken").bind("submit", function (e) {
    e.preventDefault();
    $.ajax({
        type: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        // dataType: "json",
        beforeSend: function (data) {
            CheckSesion()
        },
        success: function (data) {
            if (data.status == "ok") {
                CheckSesion()
                $('#table-zonas').DataTable().ajax.reload();
                $("tbody").addClass("opa2");
                clean()
            }
        },
        error: function () {
            CheckSesion()
        }
    });
});
// moment().locale('es');

$(document).on("click", "#VerMarcadores", function (e) {
    $('#map').css('height', '400px').css('width', '100%').addClass('shadow');
    $('#VerMarcadores').hide(); //ocultar boton
    getMarcadores(); //llamar funcion para obtener marcadores
    $('#mapTitle').html('<div class="py-2 fontq">Total Zonas: ' + marcadores.length + '</div>'); //mostrar titulo del mapa con la cantidad de marcadores

    function initialize() {
        var myLatLng = new google.maps.LatLng(firstLat, firstLng);
        // var myLatLng = new google.maps.LatLng(-34.6036844, -58.3815591);
        var mapOptions = {
            zoom: 10,
            center: myLatLng,
            mapTypeId: google.maps.MapTypeId.TERRAIN,
            zoomControl: true,
            mapTypeControl: false,
            scaleControl: false,
            streetViewControl: false,
            rotateControl: false,
            fullscreenControl: true
        }
        var map = new google.maps.Map(document.getElementById('map'), mapOptions);
        setMarkers(map, marcadores);
    }
    var infowindow;

    function setMarkers(map, marcadores) {

        for (var i = 0; i < marcadores.length; i++) {
            var myLatLng = new google.maps.LatLng(marcadores[i][1], marcadores[i][2]);
            var marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                title: marcadores[i][0],
            });
            (function (i, marker) {
                google.maps.event.addListener(marker, 'click', function () {
                    if (!infowindow) {
                        infowindow = new google.maps.InfoWindow();
                    }
                    infowindow.setContent(marcadores[i][3]);
                    infowindow.open(map, marker);
                });
            })(i, marker);
        }
    };
    initialize();
});