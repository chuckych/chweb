const loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
const host = $('#_host').val()

const LS_TOKEN_MOBILE = $("#_homehost").val() + '_tokens_mobile';
ls.remove(LS_TOKEN_MOBILE);

const redMarker = L.icon({
    iconUrl: 'css/marker-icon-2x-red.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    tooltipAnchor: [16, -28],
    // shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    shadowSize: [41, 41],
    shadowAnchor: [12, 41],
    iconColor: '#FF0000'
});
const greenMarker = L.icon({
    iconUrl: 'css/marker-icon-2x-green.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    tooltipAnchor: [16, -28],
    // shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    shadowSize: [41, 41],
    shadowAnchor: [12, 41],
    iconColor: '#FF0000'
});

const loadingTable = (selectorTable) => {
    $(selectorTable).addClass('loader-in');
    // $(selectorTable + ' td div').addClass('bg-light text-light border-0 radius h50 Mw40')
    // $(selectorTable + ' td img').addClass('invisible')
    // $(selectorTable + ' td i').addClass('invisible')
    // $(selectorTable + ' td span').addClass('invisible')
}
const actualizarRegistros = (selector, reload = false, loading = true) => {

    if (!$.fn.DataTable.isDataTable(selector)) {
        return false
    }

    if (loading) {
        loadingTable(selector)
    }
    try {
        (reload) ? $(selector).DataTable().ajax.reload(null, false) : $(selector).DataTable().ajax.reload()
    } catch (error) {
        console.log(error);
    }
}
const ClearFilterMobile = () => {
    $('.FilterUser').val(null).trigger('change'),
        $('.FilterZones').val(null).trigger('change'),
        $('.FilterDevice').val(null).trigger('change'),
        $('input[name=FilterIdentified]').prop('checked', false).parents('label').removeClass('active')
    $('#FilterIdentified3').prop('checked', true).parents('label').addClass('active')
}
function refreshSelected(selector) {
    $(selector).on('select2:select', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        actualizarRegistros('#table-mobile')
    });
}
function refreshUnselected(selector) {
    $(selector).on('select2:unselecting', function (e) {
        if (!e.params.args.originalEvent) {
            console.log(e.params.args.originalEvent);
            return
        } else {
            if ($(this).val().length > 0) {
                actualizarRegistros('#table-mobile')
            }
        }
    }).on('select2:unselect', function (e) {
        if ($(this).val().length === 0) {
            actualizarRegistros('#table-mobile')
        }
    });
    // actualizarTablas();
    return
}
function select2Val(id, text, selector) {
    var newOption = new Option(text, id, false, false);
    if (text != '') {
        $(selector).append(newOption).trigger('change');
    }
}
/**
 * @param {table} boolean 1: table, 0: nada
 * @param {typeDownload} string tipo de descarga
 */
const filterData = (table = false, typeDownload = '') => {
    let f = new FormData();
    f.append('typeDownload', typeDownload);
    f.append('_drMob', $("#_drMob").val());
    f.append('_drMob2', $("#_drMob2").val());
    f.append('SoloFic', $("#SoloFic").val());
    f.append('start', 0);
    f.append('length', 10000);
    f.append('search[value]', $('#table-mobile_filter input').val());
    f.append('draw', '');
    f.append('users[]', ($('.FilterUser').val() == null) ? '' : $('.FilterUser').val() ?? '');
    f.append('zones[]', ($('.FilterZones').val() == null) ? '' : $('.FilterZones').val() ?? '');
    f.append('device[]', ($('.FilterDevice').val() == null) ? '' : $('.FilterDevice').val() ?? '');
    f.append('identified', $('input[name=FilterIdentified]:checked').val());
    if (table) {
        let data = [];
        data._drMob = $("#_drMob").val() ?? '';
        data._drMob2 = $("#_drMob2").val() ?? '';
        data.SoloFic = $("#SoloFic").val() ?? '';
        return data;
    }
    return f;
}
const enableBtnMenu = (e) => {
    $('#btnMenu .btn').prop('readonly', false)
    $('#btnMenu #positionMap').prop('disabled', false)
    hideMapMarcadores();
}
const focusBtn = (selector) => {
    $('#btnMenu .btn').removeClass('btn-custom');
    $('#btnMenu .btn').parents('li').removeClass('shadow');
    $('#btnMenu .btn').addClass('btn-outline-custom');
    $(selector).removeClass('btn-outline-custom').addClass('btn-custom');
    // $(selector).parents('li').addClass('shadow');
}
const focusRowTables = () => {
    $('#RowTableMobile').hide();
    $('#RowTableUsers').hide();
    $('#RowTableDevices').hide();
    $('#RowTableZones').hide();
    // $('.loading').show()
}
const minmaxDate = () => {
    axios({
        method: 'post',
        url: 'minmaxdate.php'
    }).then(function (response) {
        const data = response.data
        const t = data
        // console.log(t);
        // let min = t.min
        const minFormat = t.minFormat
        // let max = t.max
        const maxFormat = t.maxFormat
        const dr = maxFormat + ' al ' + maxFormat
        $('#min').val(minFormat)
        $('#max').val(maxFormat)
        $('#_drMob2').val(dr).trigger('change')
        $('#_drMob').val(dr).trigger('change')
    }).then(() => {
        actualizarRegistros('#table-mobile');
        dateRange();
    }).catch(function (error) {
        alert('ERROR minmaxDate\n' + error);
    }).then(function () {

    });
}
const setStorageDate = (date) => {
    const lastDate = parseInt(sessionStorage.getItem($('#_homehost').val() + '_createdDate: '));
    if (lastDate < parseInt(date)) { // si la fecha del json es mayor a la del localstorage
        sessionStorage.setItem($('#_homehost').val() + '_createdDate: ', (date)); // actualizar la fecha del localstorage
        minmaxDate(); // actualizar las tablas
    } else {
        sessionStorage.setItem($('#_homehost').val() + '_createdDate: ', (date)); // actualizar la fecha del localstorage
    }
}
tryGetJson = async (resp) => {
    return new Promise((resolve) => {
        if (resp) {
            resp.json().then(json => resolve(json)).catch(() => resolve(null))
        } else {
            resolve(null)
        }
    })
}
function fetchCreatedDate(url) {
    return new Promise((resolve) => {
        fetch(url, {
            method: 'get',
            mode: 'no-cors'
        }).then(response => response.json()).then(data => {
            resolve(data);
            setStorageDate(data)
        }).catch(err => console.log(err));
    });
}
const getMap = async (lat, lng, zoom, zona, radio, latZona, lngZona, mtsZona, user, dateTime, zoneID) => {

    RemoveExistingMap(map)
    initializingMap()

    map = L.map('mapzone').setView([lat, lng], zoom);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '<small>' + lat + ',' + lng + '</small>'
    }).addTo(map);
    map.addControl(new L.Control.Fullscreen({
        title: {
            'false': 'Expandir mapa',
            'true': 'Contraer mapa'
        }
    }));
    // let myIcon = L.icon({
    //     iconUrl: '../../img/iconMarker.svg',
    // });
    if (zoneID > 0) {
        marker = L.marker([lat, lng], { icon: greenMarker }).addTo(map);
    } else {
        marker = L.marker([lat, lng], { icon: redMarker }).addTo(map);
    }
    let distancia = (mtsZona > 0) ? '<br>Distancia: ' + mtsZona : '';
    marker.bindTooltip("<b>" + user + "</b><br>" + dateTime + "<br>" + zona + distancia)

    let circleOptions2 = {
        color: 'green',
        fillColor: 'green',
        fillOpacity: 1,
    };

    if ((zoneID > 0)) {
        let circle = L.circle([latZona, lngZona], {
            color: 'green',
            fillColor: 'green',
            fillOpacity: 0.1,
            radius: radio,
            weight: 1
        }).addTo(map);
        circle2 = L.circle([latZona, lngZona], { ...circleOptions2, radius: 1 }).addTo(map);
        start = L.latLng([latZona, lngZona])
        end = L.latLng([lat, lng])
        let line = L.polyline([start, end], { color: 'green', weight: 1 }).addTo(map);

        circle.bindTooltip("Zona: <b>" + zona + "</b><br>Radio: " + radio)

    }
}
function initializingMap() // call this method before you initialize your map.
{
    let container = L.DomUtil.get('mapzone');
    if (container != null) {
        container._leaflet_id = null;
    }
}
function RemoveExistingMap(map) {
    if (map != null) {
        map.remove();
        map = null;
    }
}
function doesFileExist(urlToFile) {
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', urlToFile, false);
    xhr.send();
    return (xhr.status == "404") ? false : true;
}
const processRegFace = (id_api) => {
    $.ajax({
        type: 'POST',
        url: 'crud.php',
        data: 'tipo=proccesRegFace' + '&id_api=' + id_api,
        beforeSend: function (data) {
            CheckSesion()
        },
        success: function (data) {
            if (data.status == "ok") {
                notify(data.Mensaje.textAud, 'success', 3000, 'right')
                actualizarRegistros('#table-mobile', true)
            } else {
                actualizarRegistros('#table-mobile', true)
                notify(data.Mensaje, 'danger', 3000, 'right')
            }
        },
        error: function () {
            $.notifyClose();
            actualizarRegistros('#table-mobile', true)
        }
    });
}
function initMap() {

    if (!lati) {
        return false
    }
    var lati = parseFloat($('#latitud').val())
    var long = parseFloat($('#longitud').val())
    var zona = ($('#zona').val())
    var zona = (zona) ? zona : 'Fuera de Zona';
    var nombre = ($('#modalNombre').val()) ? $('#modalNombre').val() : 'Sin Nombre';
    var radio = parseFloat($('#map_size').val())
    let modalFoto = ($('#modalFoto').val()) ? $('#modalFoto').val() : '../../img/iconMarker.svg';

    const styledMapType = new google.maps.StyledMapType(
        [
            { elementType: "geometry", stylers: [{ color: "#ebe3cd" }] },
            { elementType: "labels.text.fill", stylers: [{ color: "#523735" }] },
            { elementType: "labels.text.stroke", stylers: [{ color: "#f5f1e6" }] },
            {
                featureType: "administrative",
                elementType: "geometry.stroke",
                stylers: [{ color: "#c9b2a6" }],
            },
            {
                featureType: "administrative.land_parcel",
                elementType: "geometry.stroke",
                stylers: [{ color: "#dcd2be" }],
            },
            {
                featureType: "poi.business",
                stylers: [{ visibility: "off" }],
            },
            {
                featureType: "transit",
                elementType: "labels.icon",
                stylers: [{ visibility: "off" }],
            },
            {
                featureType: "administrative.land_parcel",
                elementType: "labels.text.fill",
                stylers: [{ color: "#ae9e90" }],
            },
            {
                featureType: "landscape.natural",
                elementType: "geometry",
                stylers: [{ color: "#dfd2ae" }],
            },
            {
                featureType: "poi",
                elementType: "geometry",
                stylers: [{ color: "#dfd2ae" }],
            },
            {
                featureType: "poi",
                elementType: "labels.text.fill",
                stylers: [{ color: "#93817c" }],
            },
            {
                featureType: "poi.park",
                elementType: "geometry.fill",
                stylers: [{ color: "#a5b076" }],
            },
            {
                featureType: "poi.park",
                elementType: "labels.text.fill",
                stylers: [{ color: "#447530" }],
            },
            {
                featureType: "road",
                elementType: "geometry",
                stylers: [{ color: "#f5f1e6" }],
            },
            {
                featureType: "road.arterial",
                elementType: "geometry",
                stylers: [{ color: "#fdfcf8" }],
            },
            {
                featureType: "road.highway",
                elementType: "geometry",
                stylers: [{ color: "#f8c967" }],
            },
            {
                featureType: "road.highway",
                elementType: "geometry.stroke",
                stylers: [{ color: "#e9bc62" }],
            },
            {
                featureType: "road.highway.controlled_access",
                elementType: "geometry",
                stylers: [{ color: "#e98d58" }],
            },
            {
                featureType: "road.highway.controlled_access",
                elementType: "geometry.stroke",
                stylers: [{ color: "#db8555" }],
            },
            {
                featureType: "road.local",
                elementType: "labels.text.fill",
                stylers: [{ color: "#806b63" }],
            },
            {
                featureType: "transit.line",
                elementType: "geometry",
                stylers: [{ color: "#dfd2ae" }],
            },
            {
                featureType: "transit.line",
                elementType: "labels.text.fill",
                stylers: [{ color: "#8f7d77" }],
            },
            {
                featureType: "transit.line",
                elementType: "labels.text.stroke",
                stylers: [{ color: "#ebe3cd" }],
            },
            {
                featureType: "transit.station",
                elementType: "geometry",
                stylers: [{ color: "#dfd2ae" }],
            },
            {
                featureType: "water",
                elementType: "geometry.fill",
                stylers: [{ color: "#b9d3c2" }],
            },
            {
                featureType: "water",
                elementType: "labels.text.fill",
                stylers: [{ color: "#92998d" }],
            },
        ],
        { name: "Styled Map" }
    );

    const myLatLng = {
        lat: lati,
        lng: long
    };

    const map = new google.maps.Map(document.getElementById("mapzone"), {
        zoom: 15,
        center: myLatLng,
        mapTypeId: google.maps.MapTypeId.TERRAIN,
        zoomControl: false,
        mapTypeControl: false,
        scaleControl: false,
        streetViewControl: false,
        rotateControl: false,
        fullscreenControl: true,
        // mapTypeId: "terrain",
        // gestureHandling: "cooperative", /** para anular el zoom del scroll */
    });

    map.mapTypes.set("styled_map", styledMapType);
    map.setMapTypeId("styled_map");
    const contentString = '<div id="content"><span>' + nombre + "</span></div>";

    const infowindow = new google.maps.InfoWindow({
        content: contentString,
    });

    const image = "../../img/iconMarker.svg";
    // const image = modalFoto;

    let icon = {
        url: image, // url
        scaledSize: new google.maps.Size(40, 40), // scaled size
        // origin: new google.maps.Point(0,0), // origin
        // anchor: new google.maps.Point(0, 0) // anchor
    };
    const marker = new google.maps.Marker({
        position: myLatLng,
        map,
        animation: google.maps.Animation.DROP,
        // draggable: true,
        icon: icon,
        title: nombre
    })

    marker.addListener("click", () => {
        infowindow.open(map, marker);
    });
}
function clean() {
    $('#mapzone').hide();
    $("#map_size").val('5')
    $('.modal-body #noGPS').html('')
}
const dateRange = () => {
    $('#_drMob').daterangepicker({
        singleDatePicker: false,
        showDropdowns: true,
        minYear: $('#aniomin').val(),
        maxYear: $('#aniomax').val(),
        showWeekNumbers: false,
        autoUpdateInput: true,
        opens: "right",
        drops: "down",
        minDate: $('#min').val(),
        maxDate: $('#max').val(),
        startDate: $('#max').val(),
        endDate: $('#max').val(),
        autoApply: true,
        alwaysShowCalendars: true,
        linkedCalendars: false,
        buttonClasses: "btn btn-sm fontq",
        applyButtonClasses: "btn-custom fw4 px-3 opa8",
        cancelClass: "btn-link fw4 text-gris",
        ranges: {
            'Hoy': [moment(), moment()],
            'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Esta semana': [moment().day(1), moment().day(7)],
            'Semana Anterior': [moment().subtract(1, 'week').day(1), moment().subtract(1, 'week').day(7)],
            'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
            'Este mes': [moment().startOf('month'), moment().endOf('month')],
            'Mes anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
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
    $('#_drMob').on('apply.daterangepicker', function (ev, picker) {
        $('#_drMob2').val($('#_drMob').val()).trigger('change');
        actualizarRegistros('#table-mobile')
    });
}
const formatDateTime = (date) => {
    var d = new Date(date);
    var day = d.getDate();
    var month = d.getMonth() + 1;
    var year = d.getFullYear();
    var hours = d.getHours();
    var minutes = d.getMinutes();

    if (day < 10) {
        day = '0' + day;
    }

    if (month < 10) {
        month = '0' + month;
    }

    if (hours < 10) {
        hours = '0' + hours;
    }

    if (minutes < 10) {
        minutes = '0' + minutes;
    }

    return day + '/' + month + '/' + year + ' ' + hours + ':' + minutes;
}

function loadMap(data, customId) {
    const $mapContainer = $('#mapid');
    
    // Validación temprana
    if (!data || data.length === 0) {
        $mapContainer.hide();
        return false;
    }
    
    // Agregar clase loader al iniciar
    $mapContainer.addClass('loader-in');
    
    // Una sola manipulación del DOM
    $mapContainer.show().html(`<div style="width:100%; height:550px;" id="${customId}"></div>`);

    // Extraer zonas únicas
    const uniqueZones = new Map();
    data.forEach(item => {
        if (item.zoneID > 0 && !uniqueZones.has(item.zoneID)) {
            uniqueZones.set(item.zoneID, {
                zoneID: item.zoneID,
                zoneName: item.zoneName,
                zoneLat: parseFloat(item.zoneLat),
                zoneLng: parseFloat(item.zoneLng),
                zoneRadio: parseInt(item.zoneRadio)
            });
        }
    });

    const uniqueZonesArray = Array.from(uniqueZones.values());
    
    // Obtener primer elemento para centrar el mapa
    const primerElemento = data[0];
    
    // Determinar path una sola vez con validación
    const apiMobile = document.getElementById('apiMobile');
    const path = apiMobile?.value === 'http://localhost:8050' 
        ? '' 
        : `${apiMobile?.value || ''}/chweb/mobile/hrp/`;

    // Inicializar mapa de forma simple como loadMap_old
    const myMap = L.map(customId).setView([primerElemento.regLat, primerElemento.regLng], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: `Zonas encontradas: ${uniqueZonesArray.length}`,
        maxZoom: 20
    }).addTo(myMap);

    // Opciones de estilos - SIN Canvas renderer para evitar lentitud
    const circleOptions = {
        color: 'green',
        fillColor: 'green',
        fillOpacity: 0.1,
        weight: 1
    };
    const circleOptions2 = {
        color: 'black',
        fillColor: 'black',
        fillOpacity: 1,
        radius: 1
    };

    myMap.addControl(new L.Control.Fullscreen({
        title: {
            'false': 'Expandir mapa',
            'true': 'Contraer mapa'
        }
    }));

    // Crear grupos separados para mejor control
    // NO los agregamos directamente al mapa, el control de capas lo hará
    const markersEnZonaGroup = L.layerGroup();
    const markersFueraZonaGroup = L.layerGroup();
    const linesGroup = L.layerGroup();
    const zonesGroup = L.layerGroup();

    // Contadores
    let countEnZona = 0;
    let countFueraZona = 0;

    // Agregar marcadores y líneas
    data.forEach(pos => {
        const marker = L.marker([pos.regLat, pos.regLng], {
            icon: pos.zoneID > 0 ? greenMarker : redMarker
        });
        
        // Tooltip directo como en loadMap_old - más rápido que lazy loading
        const imgSrc = pos.r2FileName || (pos.imageData?.img ? path + pos.imageData.img : '');
        const zonaClass = pos.zoneName ? 'text-success' : 'text-danger';
        const zonaText = pos.zoneName || 'Fuera de zona';
        const distanciaText = pos.zoneDistance > 0 ? `Distancia: ${pos.zoneDistance} m.` : '';
        
        const infoMarker = `
            <div class='d-inline-flex'>
                ${imgSrc ? `<img src='${imgSrc}' style='width:40px; height:40px' alt='${pos.userName}'>` : ''}
                <div class='d-flex flex-column ml-2'>
                    <span>${pos.userName}</span>
                    <span>${pos.regDate} ${pos.regHora}</span>
                </div>
            </div>
            <div class='d-flex flex-column'>
                <span>Zona: <span class='${zonaClass}'>${zonaText}</span></span>
                ${distanciaText ? `<span>${distanciaText}</span>` : ''}
            </div>
        `;
        
        marker.bindTooltip(infoMarker);
        
        // Separar marcadores según estén en zona o fuera de zona
        if (pos.zoneID > 0) {
            markersEnZonaGroup.addLayer(marker);
            countEnZona++;
            
            // Si está en zona, agregar línea SIN renderer canvas
            if (pos.zoneLat && pos.zoneLng) {
                const line = L.polyline(
                    [[pos.regLat, pos.regLng], [pos.zoneLat, pos.zoneLng]], 
                    { 
                        color: 'green', 
                        weight: 1,
                        interactive: false
                    }
                );
                linesGroup.addLayer(line);
            }
        } else {
            markersFueraZonaGroup.addLayer(marker);
            countFueraZona++;
        }
    });

    // Agregar zonas únicas - SIN Canvas renderer
    uniqueZonesArray.forEach(zone => {
        const circle = L.circle([zone.zoneLat, zone.zoneLng], {
            ...circleOptions,
            radius: zone.zoneRadio
        });
        circle.bindTooltip(`Zona: <b>${zone.zoneName}</b><br>Radio: ${zone.zoneRadio}`);
        
        const centerCircle = L.circle([zone.zoneLat, zone.zoneLng], circleOptions2);
        
        zonesGroup.addLayer(circle);
        zonesGroup.addLayer(centerCircle);
    });

    // Control de capas con checkboxes y contadores
    const overlays = {};
    overlays[`En Zona (${countEnZona})`] = markersEnZonaGroup;
    overlays[`Fuera de Zona (${countFueraZona})`] = markersFueraZonaGroup;
    overlays["Líneas"] = linesGroup;
    overlays[`Zonas (${uniqueZonesArray.length})`] = zonesGroup;
    
    const layerControl = L.control.layers(null, overlays, { collapsed: false }).addTo(myMap);

    // Agregar todos los grupos al mapa por defecto (todos los checkboxes marcados)
    myMap.addLayer(markersEnZonaGroup);
    myMap.addLayer(markersFueraZonaGroup);
    myMap.addLayer(linesGroup);
    myMap.addLayer(zonesGroup);

    // Vincular líneas con marcadores "En Zona"
    myMap.on('overlayadd', function(e) {
        // Si se activa "En Zona", mostrar las líneas automáticamente
        if (e.name === `En Zona (${countEnZona})`) {
            if (!myMap.hasLayer(linesGroup)) {
                myMap.addLayer(linesGroup);
                // Actualizar el checkbox de líneas
                setTimeout(() => {
                    const inputs = document.querySelectorAll('.leaflet-control-layers-overlays input');
                    inputs.forEach(input => {
                        const label = input.parentElement;
                        if (label.textContent.includes('Líneas')) {
                            input.checked = true;
                        }
                    });
                }, 10);
            }
        }
    });

    myMap.on('overlayremove', function(e) {
        // Si se desactiva "En Zona", ocultar automáticamente las líneas
        if (e.name === `En Zona (${countEnZona})`) {
            if (myMap.hasLayer(linesGroup)) {
                // Remover el grupo de líneas del mapa
                myMap.removeLayer(linesGroup);
                
                // Actualizar el checkbox de líneas
                const inputs = document.querySelectorAll('.leaflet-control-layers-overlays input');
                inputs.forEach(input => {
                    const label = input.parentElement;
                    if (label.textContent.includes('Líneas')) {
                        input.checked = false;
                    }
                });
            }
        }
    });

    // Aplicar estilo transparente al control de capas
    setTimeout(() => {
        const layerControlDiv = document.querySelector('.leaflet-control-layers');
        if (layerControlDiv) {
            layerControlDiv.style.backgroundColor = 'rgba(255, 255, 255, 0.5)';
            layerControlDiv.style.backdropFilter = 'blur(5px)';
            layerControlDiv.style.borderRadius = '6px';
            layerControlDiv.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.15)';
            layerControlDiv.style.border = '1px solid rgba(0, 0, 0, 0.1)';
        }
        
        // Remover loader cuando termine de renderizar
        $mapContainer.removeClass('loader-in');
    }, 100);
    
    return myMap; // Retornar el mapa para posible uso externo
}

function loadMapZones(data, customId) {
    const $mapContainer = $('#mapid-zones');
    
    // Validación temprana
    if (!data || data.length === 0) {
        $mapContainer.hide();
        return false;
    }
    
    // Una sola manipulación del DOM
    $mapContainer.show().html(`<div style="width:100%; height:550px;" id="${customId}"></div>`);

    // Centrar en la primera zona del array
    const firstZone = data[0];
    const centerLat = parseFloat(firstZone.zoneLat);
    const centerLng = parseFloat(firstZone.zoneLng);

    // Inicializar mapa
    const myMap = L.map(customId).setView([centerLat, centerLng], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: `Total de zonas: ${data.length}`,
        maxZoom: 20
    }).addTo(myMap);

    // Opciones de estilos - SIN Canvas renderer como en loadMap
    const circleOptions = {
        color: 'blue',
        fillColor: 'blue',
        fillOpacity: 0.1,
        weight: 2
    };
    const centerCircleOptions = {
        color: 'darkblue',
        fillColor: 'darkblue',
        fillOpacity: 1,
        radius: 2
    };

    myMap.addControl(new L.Control.Fullscreen({
        title: {
            'false': 'Expandir mapa',
            'true': 'Contraer mapa'
        }
    }));

    // Crear grupos separados para mejor control
    const markersGroup = L.layerGroup();
    const zonesGroup = L.layerGroup();

    // Marcador azul para zonas
    const blueMarker = L.icon({
        iconUrl: 'css/marker-icon-2x-blue.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        tooltipAnchor: [16, -28],
        shadowSize: [41, 41],
        shadowAnchor: [12, 41]
    });

    // Agregar marcadores y círculos de zonas
    data.forEach(zone => {
        const lat = parseFloat(zone.zoneLat);
        const lng = parseFloat(zone.zoneLng);
        const radio = parseInt(zone.zoneRadio);

        // Crear marcador en el centro de la zona
        const marker = L.marker([lat, lng], { icon: blueMarker });
        
        // Tooltip con información de la zona
        const infoMarker = `
            <div class='d-flex flex-column'>
                <span><b>${zone.zoneName}</b></span>
                <span>Radio: ${zone.zoneRadio} m</span>
                <span>Registros: ${zone.totalZones}</span>
                <span>Evento: ${zone.zoneEvent}</span>
            </div>
        `;
        
        marker.bindTooltip(infoMarker);
        markersGroup.addLayer(marker);

        // Crear círculo de la zona SIN Canvas renderer
        const circle = L.circle([lat, lng], {
            ...circleOptions,
            radius: radio
        });
        
        const tooltipZone = `
            <b>${zone.zoneName}</b><br>
            Radio: ${zone.zoneRadio} m<br>
            Registros: ${zone.totalZones}
        `;
        circle.bindTooltip(tooltipZone);

        // Crear punto central SIN Canvas renderer
        const centerCircle = L.circle([lat, lng], centerCircleOptions);
        
        zonesGroup.addLayer(circle);
        zonesGroup.addLayer(centerCircle);
    });

    // Control de capas con checkboxes (sin contador redundante en Zonas)
    const overlays = {};
    overlays[`Marcadores (${data.length})`] = markersGroup;
    overlays["Zonas"] = zonesGroup;
    
    L.control.layers(null, overlays, { collapsed: false }).addTo(myMap);

    // Agregar todos los grupos al mapa por defecto
    myMap.addLayer(markersGroup);
    myMap.addLayer(zonesGroup);

    // Aplicar estilo transparente al control de capas
    setTimeout(() => {
        const layerControlDiv = document.querySelector('.leaflet-control-layers');
        if (layerControlDiv) {
            layerControlDiv.style.backgroundColor = 'rgba(255, 255, 255, 0.5)';
            layerControlDiv.style.backdropFilter = 'blur(5px)';
            layerControlDiv.style.borderRadius = '6px';
            layerControlDiv.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.15)';
            layerControlDiv.style.border = '1px solid rgba(0, 0, 0, 0.1)';
        }
        
        // Remover loader cuando termine de renderizar
        $mapContainer.removeClass('loader-in');
    }, 100);
    
    return myMap; // Retornar el mapa para posible uso externo
}

const getToken = () => {
    let formData = new FormData();
    formData.append('tipo', 'getTokenMobile');
    axios({
        method: 'post',
        url: 'crud.php',
        data: formData,
    }).then(function (response) {
        const data = response.data
        const d = document.getElementById('dataT')
        // create element table
        let tableData = ''
        let t = ''

        t += `<table class="table table-responsive p-2 border radius bg-white fadeIn" id="tableToken">`
        t += `<thead>`
        t += `<tr>`
        t += `<th data-titler="Token de activación de App Mobile">Token</th>`
        t += `<th data-titler="Fecha de expiración del Token">Expiración</th>`
        // t += `<th>Eliminado</th>`
        t += `<th class="text-center" data-titler="Tiempo mínimo entre registraciones">Tiempo</th>`
        t += `<th class="text-center" data-titler="Recuerda el usuario en la App">Recordar usuario</th>`
        t += `<th class="w-100"></th>`
        t += `</tr>`
        t += `</thead>`
        t += `<tbody>`
        t += `</tbody>`
        t += `</table>`
        d.innerHTML = t

        data.data.forEach(element => {
            if (element.dateDelete == null) {
                tableData += `<tr>`
                tableData += `<td class="user-select-all">${element.token}</td>`
                tableData += `<td>${element.expirationDate}</td>`
                // tableData += `<td>${(element.dateDelete == null) ? 'No' : 'Sí'}</td>`
                tableData += `<td class="text-center">${element.tmef} s</td>`
                tableData += `<td class="text-center">${(element.rememberEmployeId == true) ? 'Sí' : 'No'}</td>`
                tableData += `<td class="w-100"></td>`
                tableData += `</tr>`
            }

        });
        tableData += `<tr>`
        tableData += `<td colspan="5" class="">`
        tableData += `<a href="https://play.google.com/store/apps/details?id=com.dysi.hrprocessmobile" class="btn btn-sm btn-custom" target="_blank" ><i class="bi bi-google-play mr-2"></i>HRProcess Mobile</a>`
        tableData += `</td>`
        tableData += `</tr>`
        const table = document.querySelector('#tableToken tbody')
        table.innerHTML = tableData


    }).catch(function (error) {
        console.log(error);
    });
}
$('#collapseTokenView').on('show.bs.collapse', function () {
    if (!ls.get(LS_TOKEN_MOBILE)) {
        getToken();
        ls.set(LS_TOKEN_MOBILE, true);
    }
})
const visible540 = () => {
    if ($(window).width() < 540) return false;
    return true;
}
const lengthMenuUsers = () => {
    if ($(window).width() < 540) {
        return [[3, 10, 25, 50, 100, 200], [3, 10, 25, 50, 100, 200]];
    }
    return [[5, 10, 25, 50, 100, 200], [5, 10, 25, 50, 100, 200]];
}