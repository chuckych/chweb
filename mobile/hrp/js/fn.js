const loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
const host = $('#_host').val()

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

const loadingTable = (selectortable) => {
    $(selectortable + ' td div').addClass('bg-light text-light border-0 h50')
    $(selectortable + ' td img').addClass('invisible')
    $(selectortable + ' td i').addClass('invisible')
    $(selectortable + ' td span').addClass('invisible')
}
const actualizarRegistros = (selector, reload = false, loading = true) => {
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
    $(selector).parents('li').addClass('shadow');
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
        let data = response.data
        let t = data
        // console.log(t);
        let min = t.min
        let minFormat = t.minFormat
        let max = t.max
        let maxFormat = t.maxFormat
        let dr = maxFormat + ' al ' + maxFormat
        $('#min').val(minFormat)
        $('#max').val(maxFormat)
        $('#_drMob2').val(dr).trigger('change')
        $('#_drMob').val(dr).trigger('change')
    }).then(() => {
        actualizarRegistros('#table-mobile')
        actualizarRegistros('#tableUsuarios')
        actualizarRegistros('#tableDevices')
        actualizarRegistros('#tableZones')
        dateRange()
    }).catch(function (error) {
        alert('ERROR minmaxDate\n' + error);
    }).then(function () {

    });
}
const setStorageDate = (date) => {
    let lastdate = parseInt(sessionStorage.getItem($('#_homehost').val() + '_createdDate: '));
    if (lastdate < parseInt(date)) { // si la fecha del json es mayor a la del localstorage
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
        }).then(response => response.json())
            .then(data => {
                resolve(data);
                setStorageDate(data)
            })
            .catch(err => console.log(err));
    });
}
function getMap(lat, lng, zoom, zona, radio, latzona, lngzona, mtszona, user, datetime, zoneID) {
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
    let distancia = (mtszona > 0) ? '<br>Distancia: ' + mtszona : '';
    marker.bindTooltip("<b>" + user + "</b><br>" + datetime + "<br>" + zona + distancia)

    let circleOptions2 = {
        color: 'green',
        fillColor: 'green',
        fillOpacity: 1,
    };

    if ((zoneID > 0)) {
        let circle = L.circle([latzona, lngzona], {
            color: 'green',
            fillColor: 'green',
            fillOpacity: 0.1,
            radius: radio,
            weight: 1
        }).addTo(map);
        circle2 = L.circle([latzona, lngzona], { ...circleOptions2, radius: 1 }).addTo(map);
        start = L.latLng([latzona, lngzona])
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
function actualizar(noti = true) {

    if (noti) {
        ActiveBTN(true, ".actualizar", loading, '<i class="bi bi-cloud-download-fill"></i>')
        notify('Actualizando registros <span class = "dotting mr-1"> </span> ' + loading, 'dark', 0, 'right')
    };

    axios({
        method: 'post',
        url: 'actualizar.php'
    }).then(function (response) {
        let data = response.data.Response
        let date = new Date()
        if (data.status == "ok") {
            // set session storage
            sessionStorage.setItem($('#_homehost').val() + '_LastTranferMobile_1: ' + date, JSON.stringify(data));
            if (noti) {
                $.notifyClose();
                ActiveBTN(false, ".actualizar", loading, '<i class="bi bi-cloud-download-fill"></i>')
                minmaxDate()
                if (data.totalSession > 0) {
                    notify(`<span class="">Se actualizaron registros<br/>Total: <span class="font-weight-bold">${data.totalSession}</span></span>`, 'success', 20000, 'right')
                } else {
                    notify('No hay registros nuevos', 'info', 2000, 'right')
                }
            } else {
                minmaxDate()
            }
        } else {
            if (noti) {
                $.notifyClose();
                ActiveBTN(false, ".actualizar", loading, '<i class="bi bi-cloud-download-fill"></i>')
                notify(data.Mensaje, 'info', 2000, 'right')
            }
        }

    }).catch(function (error) {
        console.log('ERROR actualizar\n' + error);
    }).then(function () {
        ActiveBTN(false, ".actualizar", 'Actualizando..' + loading, '<i class="bi bi-cloud-download-fill"></i>')
        $(".actualizar").attr("data-titlel", "Descargar registros");
        setTimeout(() => {
            $.notifyClose();
        }, 2000);
    });
}
function actualizar2(noti = true) {

    if (noti) {
        ActiveBTN(true, ".actualizar", loading, '<i class="bi bi-cloud-download-fill"></i>')
        notify('Actualizando registros <span class = "dotting mr-1"> </span> ' + loading, 'dark', 0, 'right')
    };

    axios({
        method: 'post',
        url: 'actualizar-2.php'
    }).then(function (response) {
        let data = response.data.Response
        let date = new Date()
        if (data.status == "ok") {
            // set session storage
            sessionStorage.setItem($('#_homehost').val() + '_LastTranferMobile_2: ' + date, JSON.stringify(data));
            if (noti) {
                $.notifyClose();
                ActiveBTN(false, ".actualizar", loading, '<i class="bi bi-cloud-download-fill"></i>')
                minmaxDate()
                if (data.totalSession > 0) {
                    notify(`<span class="">Se actualizaron registros<br/>Total: <span class="font-weight-bold">${data.totalSession}</span></span>`, 'success', 20000, 'right')
                } else {
                    notify('No hay registros nuevos', 'info', 2000, 'right')
                }
            } else {
                minmaxDate()
            }
        } else {
            if (noti) {
                $.notifyClose();
                ActiveBTN(false, ".actualizar", loading, '<i class="bi bi-cloud-download-fill"></i>')
                notify(data.Mensaje, 'info', 2000, 'right')
            }
        }

    }).catch(function (error) {
        console.log('ERROR actualizar\n' + error);
    }).then(function () {
        ActiveBTN(false, ".actualizar", 'Actualizando..' + loading, '<i class="bi bi-cloud-download-fill"></i>')
        $(".actualizar").attr("data-titlel", "Descargar registros");
        setTimeout(() => {
            $.notifyClose();
        }, 2000);
    });
}
function actualizar3(noti = true) {

    if (noti) {
        ActiveBTN(true, ".actualizar", loading, '<i class="bi bi-cloud-download-fill"></i>')
        notify('Actualizando registros <span class = "dotting mr-1"> </span> ' + loading, 'dark', 0, 'right')
    };

    axios({
        method: 'post',
        url: 'actualizar-3.php'
    }).then(function (response) {
        let data = response.data.Response
        let date = new Date()
        if (data.status == "ok") {
            // set session storage
            sessionStorage.setItem($('#_homehost').val() + '_LastTranferMobile_3: ' + date, JSON.stringify(data));
            if (noti) {
                $.notifyClose();
                ActiveBTN(false, ".actualizar", loading, '<i class="bi bi-cloud-download-fill"></i>')
                minmaxDate()
                if (data.totalSession > 0) {
                    notify(`<span class="">Se actualizaron registros<br/>Total: <span class="font-weight-bold">${data.totalSession}</span></span>`, 'success', 20000, 'right')
                } else {
                    notify('No hay registros nuevos', 'info', 2000, 'right')
                }
            } else {
                minmaxDate()
            }
        } else {
            if (noti) {
                $.notifyClose();
                ActiveBTN(false, ".actualizar", loading, '<i class="bi bi-cloud-download-fill"></i>')
                notify(data.Mensaje, 'info', 2000, 'right')
            }
        }

    }).catch(function (error) {
        console.log('ERROR actualizar\n' + error);
    }).then(function () {
        ActiveBTN(false, ".actualizar", 'Actualizando..' + loading, '<i class="bi bi-cloud-download-fill"></i>')
        $(".actualizar").attr("data-titlel", "Descargar registros");
        setTimeout(() => {
            $.notifyClose();
        }, 2000);
    });
}
function actualizar4(noti = true) {

    if (noti) {
        ActiveBTN(true, ".actualizar", loading, '<i class="bi bi-cloud-download-fill"></i>')
        notify('Actualizando registros <span class = "dotting mr-1"> </span> ' + loading, 'dark', 0, 'right')
    };

    axios({
        method: 'post',
        url: 'actualizar-4.php'
    }).then(function (response) {
        let data = response.data.Response
        let date = new Date()
        if (data.status == "ok") {
            // set session storage
            sessionStorage.setItem($('#_homehost').val() + '_LastTranferMobile_4: ' + date, JSON.stringify(data));
            if (noti) {
                $.notifyClose();
                ActiveBTN(false, ".actualizar", loading, '<i class="bi bi-cloud-download-fill"></i>')
                minmaxDate()
                if (data.totalSession > 0) {
                    notify(`<span class="">Se actualizaron registros<br/>Total: <span class="font-weight-bold">${data.totalSession}</span></span>`, 'success', 20000, 'right')
                } else {
                    notify('No hay registros nuevos', 'info', 2000, 'right')
                }
            } else {
                minmaxDate()
            }
        } else {
            if (noti) {
                $.notifyClose();
                ActiveBTN(false, ".actualizar", loading, '<i class="bi bi-cloud-download-fill"></i>')
                notify(data.Mensaje, 'info', 2000, 'right')
            }
        }

    }).catch(function (error) {
        console.log('ERROR actualizar\n' + error);
    }).then(function () {
        ActiveBTN(false, ".actualizar", 'Actualizando..' + loading, '<i class="bi bi-cloud-download-fill"></i>')
        $(".actualizar").attr("data-titlel", "Descargar registros");
        setTimeout(() => {
            $.notifyClose();
        }, 2000);
    });
}
function actualizar_aws(noti = true) {

    if (noti) {
        ActiveBTN(true, ".actualizar", loading, '<i class="bi bi-cloud-download-fill"></i>')
        notify('Actualizando registros <span class = "dotting mr-1"> </span> ' + loading, 'dark', 0, 'right')
    };

    axios({
        method: 'post',
        url: 'actualizar_aws.php'
    }).then(function (response) {
        let data = response.data.Response
        let date = new Date()
        if (data.status == "ok") {
            // set session storage
            sessionStorage.setItem($('#_homehost').val() + '_LastTranferMobile_AWS: ' + date, JSON.stringify(data));
            if (noti) {
                $.notifyClose();
                ActiveBTN(false, ".actualizar", loading, '<i class="bi bi-cloud-download-fill"></i>')
                minmaxDate()
                if (data.totalSession > 0) {
                    notify(`<span class="">Se actualizaron registros<br/>Total: <span class="font-weight-bold">${data.totalSession}</span></span>`, 'success', 20000, 'right')
                } else {
                    notify('No hay registros nuevos', 'info', 2000, 'right')
                }
            } else {
                minmaxDate()
            }
        } else {
            if (noti) {
                $.notifyClose();
                ActiveBTN(false, ".actualizar", loading, '<i class="bi bi-cloud-download-fill"></i>')
                notify(data.Mensaje, 'info', 2000, 'right')
            }
        }

    }).catch(function (error) {
        console.log('ERROR actualizar\n' + error);
    }).then(function () {
        ActiveBTN(false, ".actualizar", 'Actualizando..' + loading, '<i class="bi bi-cloud-download-fill"></i>')
        $(".actualizar").attr("data-titlel", "Descargar registros");
        setTimeout(() => {
            $.notifyClose();
        }, 2000);
    });
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

// {/* <div class="copyRegig" data-clipboard-text="HOLA A TODO">HOLA</div> */ }
// let copyRegig = new ClipboardJS('.copyRegig');
// copyRegig.on('success', function (e) {
//     $.notifyClose();
//     notify('Copiado', 'warning', 1000, 'right')
//     setTimeout(function () {
//         $.notifyClose();
//     }, 1000);
//     e.clearSelection();
// });

// copyRegig.on('error', function (e) {
//     $.notifyClose();
//     notify('Error al copiar', 'danger', 1000, 'right')
//     setTimeout(function () {
//         $.notifyClose();
//     }, 1000);
//     e.clearSelection();
// });

function loadMap(data, customid) {

    // console.log(data.length);
    $('#mapid').html('');

    if ((data.length == 0)) {
        $('#mapid').hide();
        return false
    }
    // Crea el mapa
    $('#mapid').show();
    $('#mapid').html('<div style="width:100%; height:550px;" id="' + customid + '"></div>');

    let ubicacionesParaMapa = [];
    let uniqueZones = new Map();

    for (const item of data) {
        if (!uniqueZones.has(item.zoneID)) {
            if (item.zoneID > 0) {
                let a = {
                    "zoneID": item.zoneID,
                    "zoneName": item.zoneName,
                    "zoneLat": parseFloat(item.zoneLat),
                    "zoneLng": parseFloat(item.zoneLng),
                    "zoneRadio": parseInt(item.zoneRadio),
                }
                uniqueZones.set(item.zoneID, a);
            }
        }
    }


    let uniqueZonesArray = Array.from(uniqueZones.values())
    let apiMobile = document.getElementById('apiMobile');
    let path = apiMobile.value + '/chweb/mobile/hrp/'
    if (apiMobile.value == 'http://localhost:8050') {
        path = ''
    }
    console.log(path);
    data.forEach((ubicacion) => {
        ubicacionesParaMapa.push(
            {
                lat: ubicacion.regLat,
                lon: ubicacion.regLng,
                zoneID: ubicacion.zoneID,
                user: ubicacion.userName,
                datetime: ubicacion.regDate + ' ' + ubicacion.regHora,
                distancia: (ubicacion.zoneDistance > 0) ? 'Distancia: ' + ubicacion.zoneDistance + ' m.' : '',
                zona: (ubicacion.zoneName != null) ? '<span class="text-success">' + ubicacion.zoneName + '</b>' : '<span class="text-danger"><span>Fuera de zona</b></span>',
                img: path + ubicacion.imageData['img'],
                zoneLat: ubicacion.zoneLat,
                zoneLng: ubicacion.zoneLng,
                regLat: ubicacion.regLat,
                regLng: ubicacion.regLng
            }
        );
    });

    let primerElemento = ubicacionesParaMapa[0]
    let latitud = primerElemento.lat
    let longitud = primerElemento.lon

    let mymap = L.map(customid).setView([latitud, longitud], 16);
    // Agrega el layer de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        // attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        maxZoom: 20
    }).addTo(mymap);


    let circleOptions = {
        color: 'green',
        fillColor: 'green',
        fillOpacity: 0.1,
        weight: 1
    };
    let circleOptions2 = {
        color: 'black',
        fillColor: 'black',
        fillOpacity: 1,
    };
    let circleOptions3 = {
        color: 'white',
        fillColor: 'green',
        fillOpacity: 0.9,
        radius: 7
    };
    let circleOptions4 = {
        color: 'white',
        fillColor: 'red',
        fillOpacity: 0.9,
        radius: 7
    };

    mymap.addControl(new L.Control.Fullscreen({
        title: {
            'false': 'Expandir mapa',
            'true': 'Contraer mapa'
        }
    }));

    // let markersLayer = L.layerGroup().addTo(mymap);
    // markersLayer.clearLayers();

    // let iconZone = L.icon({
    //     iconUrl: '',
    let start = 0
    let end = 0
    let markersLayer = ''
    // });    
    for (let i = 0; i < ubicacionesParaMapa.length; i++) {
        let ubicacion = ubicacionesParaMapa[i];
        if (ubicacion.zoneID > 0) {
            start = L.latLng([ubicacion.regLat, ubicacion.regLng])
            end = L.latLng([ubicacion.zoneLat, ubicacion.zoneLng])
            markersLayer = L.marker([ubicacion.lat, ubicacion.lon], { icon: greenMarker }).addTo(mymap);
            // circle3 = L.circleMarker([ubicacion.lat,ubicacion.lon],{...circleOptions3}).addTo(mymap);
            let line = L.polyline([start, end], { color: 'green', weight: 1 }).addTo(mymap);
        } else {
            markersLayer = L.marker([ubicacion.lat, ubicacion.lon], { icon: redMarker }).addTo(mymap);
            // circle3 = L.circleMarker([ubicacion.lat,ubicacion.lon],{...circleOptions4}).addTo(mymap);
        }

        let infoMarker = `
        <div class='d-inline-flex'>
            <img src='${ubicacion.img}' style='width:40px; height:40px'></img>
            <div class='d-flex flex-column ml-2'>
                <span>${ubicacion.user}</span> </span>${ubicacion.datetime}</span>
            </div>
        </div>
        <div class='d-flex flex-column'>
            <span>Zona: ${ubicacion.zona}</span> </span>${ubicacion.distancia}</span>
        </div>
        `

        markersLayer.bindTooltip(infoMarker)

    }
    for (let i = 0; i < uniqueZonesArray.length; i++) {
        let zone = uniqueZonesArray[i];
        circle = L.circle([zone.zoneLat, zone.zoneLng], {
            ...circleOptions,
            radius: zone.zoneRadio
        }).addTo(mymap);
        circle2 = L.circle([zone.zoneLat, zone.zoneLng], {
            ...circleOptions2,
            radius: 1
        }).addTo(mymap);
        circle.bindTooltip("Zona: <b>" + zone.zoneName + "</b><br>Radio: " + zone.zoneRadio)
    }

    $('#mapid').append(`<div class="pt-2 fontp float-right">Total Zonas: ${uniqueZonesArray.length}.</div>`)

}
const getToken = () => {
    let formData = new FormData();
    formData.append('tipo', 'getTokenMobile');
    axios({
        method: 'post',
        url: 'crud.php',
        data: formData,
    }).then(function (response) {
        let data = response.data

        // {
        //     "data": [
        //         {
        //             "companyId": 1,
        //             "dateDelete": null,
        //             "expirationDate": "2024-04-21",
        //             "id": 1,
        //             "token": "HRC20230321",
        //             "updatedDate": 1680106443640,
        //             "config": {
        //                 "alwaysSavePosition": false,
        //                 "notificationId": 195,
        //                 "apiKey": "7BB3A26C25687BCD56A9BAF353A78",
        //                 "locationIp": "http:\/\/190.7.56.83",
        //                 "serverIp": "http:\/\/207.191.165.3:7575",
        //                 "companyCode": "1",
        //                 "recid": "aNGL89kv",
        //                 "employeId": "",
        //                 "updateInterval": 90,
        //                 "fastestInterval": 60,
        //                 "eventType": 0,
        //                 "cancellationReasons": [],
        //                 "operations": [],
        //                 "tmef": 10,
        //                 "rememberEmployeId": true
        //             }
        //         },
        //     ],
        //     "status": "ok"
        // }
        let d = document.getElementById('dataT')
        // create elemente table
        let tabledata = ''
        let t = ''

        t += `<table class="table table-responsive p-2 border animate__animated animate__fadeIn" id="tableToken">`
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
                tabledata += `<tr>`
                tabledata += `<td class="user-select-all">${element.token}</td>`
                tabledata += `<td>${element.expirationDate}</td>`
                // tabledata += `<td>${(element.dateDelete == null) ? 'No' : 'Sí'}</td>`
                tabledata += `<td class="text-center">${element.tmef} s</td>`
                tabledata += `<td class="text-center">${(element.rememberEmployeId == true) ? 'Sí' : 'No'}</td>`
                tabledata += `<td class="w-100"></td>`
                tabledata += `</tr>`
            }

        });
        tabledata += `<tr>`
        tabledata += `<td colspan="5" class="">`
        tabledata += `<a href="https://play.google.com/store/apps/details?id=com.dysi.hrprocessmobile" class="btn btn-sm btn-custom" target="_blank" ><i class="bi bi-google-play mr-2"></i>HRProcess Mobile</a>`
        tabledata += `</td>`
        tabledata += `</tr>`
        let table = document.querySelector('#tableToken tbody')
        table.innerHTML = tabledata


    }).catch(function (error) {
        console.log(error);
    });
}
getToken()