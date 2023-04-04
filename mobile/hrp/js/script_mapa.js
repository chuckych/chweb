let hideMapMarcadores = () => {
    $('#map').html('').removeClass('shadow').css('height', '0px').hide();
    $('#mapTitle').html(' ');
    if (getMarcadores() != '') {
        $('#btnVerMarcadores').html(`
        <li class="nav-item">
        <a href="#positionMap" data-titlet="Ver Mapa"
            class="mr-1 btn btn-sm btn-outline-custom border-0 radisu2 p-2 w90" id="VerMarcadores">
            <span class="">
                <i class="bi bi-pin-map-fill"></i>
                <br>
                <span class="fontp">Ver Mapa</span>
            </span>
        </a>
        </li>`)
    }
}
$("#table-mobile").on('draw.dt', function (e, settings) {
    e.preventDefault();
    hideMapMarcadores();
    return true
});
let marcadores = new Array();
let getMarcadores = (lat = '', lng = '') => {
    marcadores = [];
    firstLat = lat;
    firstLng = lng;
    $("#table-mobile td .marcador").each(function () {
        obj = JSON.parse($(this).attr("marcador"));
        // let divMarker = `<div class='p-3 shadow-sm bg-white'><label class='w40 fontq'>Zona: </label> <span class='font-weight-bold'>${obj.name}</span><br><label class='w40 fontq'>Radio: </label> <span class='font-weight-bold'>${obj.map_size}</span></div>`
        let divMarker = `<div class='p-3 shadow-sm bg-white'>
        <label class='w40 fontp p-0 m-0 text-secondary'>Nombre</label>
        <div class='font-weight-bold'>${obj.name}</div>
        <label class='w40 fontp p-0 m-0 text-secondary'>Fecha</label>
        <div class='font-weight-bold'>${obj.regDate} ${obj.regDay}</div>
        <label class='w40 fontp p-0 m-0 text-secondary'>Hora</label>
        <div class='font-weight-bold'>${obj.regHora}</div>
        </div>`
        marcadores.push(
            [obj.name, parseFloat(obj.lat), parseFloat(obj.lng), divMarker],
        );
        if (!lat) {
            firstLat = parseFloat(obj.lat);
            firstLng = parseFloat(obj.lng);
        }
    });

    if (lat) {
        firstLat = lat;
        firstLng = lng;
    }
    return marcadores;
}
$(document).on("click", "#VerMarcadores", function (e) {
    enableBtnMenu()
    document.title = "Fichadas Mobile"
    $('#Encabezado').html("Fichadas Mobile")
    focusRowTables()
    $('#btnMenu .btn').removeClass('btn-custom');
    $('#btnMenu .btn').parents('li').removeClass('shadow');
    $('#btnMenu .btn').addClass('btn-outline-custom');
    $('.showChecks').removeClass('btn-outline-custom').addClass('btn-custom');
    $('.showChecks').parents('li').addClass('shadow');

    $('#RowTableMobile').show();

    $('#map').css('height', '400px').css('width', '100%').addClass('shadow').show();
    $('#VerMarcadores').addClass('disabled'); //ocultar boton
    getMarcadores(); //llamar funcion para obtener marcadores
    // console.log(marcadores);
    $('#mapTitle').html('<div class="py-2 fontq">Total Fichadas: ' + marcadores.length + '</div>'); //mostrar titulo del mapa con la cantidad de marcadores
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