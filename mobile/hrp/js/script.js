$('#Encabezado').addClass('pointer')
$('#RowTableUsers').hide();
$('#RowTableDevices').hide();
// windows on load
$(window).on('load', function () {
    $('.loading').hide()
});
const loadingTable = (selectortable) => {
    $(selectortable + ' td div').addClass('bg-light text-light border-0 h50')
    $(selectortable + ' td img').addClass('invisible')
    $(selectortable + ' td i').addClass('invisible')
    $(selectortable + ' td span').addClass('invisible')
}
let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
let host = $('#_host').val()

if ((host == 'https://localhost')) {
    console.log(host)
} else if ((host == 'http://localhost')) {
    console.log(host)
} else {
    actualizar(false);
}

$.fn.DataTable.ext.pager.numbers_length = 5;
// $('#btnFiltrar').removeClass('d-sm-block');
let drmob2 = $('#min').val() + ' al ' + $('#max').val()
$('#_drMob2').val(drmob2)

function doesFileExist(urlToFile) {
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', urlToFile, false);
    xhr.send();
    return (xhr.status == "404") ? false : true;
}
// max-h-500 overflow-auto
tablemobile = $('#table-mobile').DataTable({
    // iDisplayLength: -1,
    dom: "<'row lengthFilterTable'" +
        "<'col-12 col-sm-6 d-flex align-items-start dr'l><'col-12 col-sm-6 d-inline-flex align-items-start justify-content-end'<'SoloFic mt-2'>f>>" +
        "<'row'<'col-12 border shadow-sm table-responsive't>>" +
        "<'row d-none d-sm-block'<'col-12 d-flex bg-white pr-3 align-items-center justify-content-between'ip>>" +
        "<'row d-block d-sm-none'<'col-12 fixed-bottom h70 bg-white pr-3 d-flex align-items-center justify-content-center'p>>" +
        "<'row d-block d-sm-none'<'col-12 d-flex align-items-center justify-content-center'i>>",
    ajax: {
        url: "getRegMobile.php",
        type: "POST",
        // dataSrc: "mobile",
        "data": function (data) {
            data._drMob = $("#_drMob").val();
            data._drMob2 = $("#_drMob2").val();
            data.SoloFic = $("#SoloFic").val();
        },
    },
    createdRow: function (row, data, dataIndex) {
        $(row).addClass('animate__animated animate__fadeIn');
    },
    columns: [
        /** Columna Foto */
        {
            className: 'text-center', targets: 'regPhoto', title: '<div class="w50">Foto</div>',
            "render": function (data, type, row, meta) {
                operation = (row.operation == 0) ? '' : ': ' + row.operation;
                let evento = '';
                switch (row.operationType) {
                    case '-1':
                        evento = 'Fichada';
                        break;
                    case '1':
                        evento = 'Ronda';
                        break;
                    case '3':
                        evento = 'Evento';
                        break;
                    default:
                        evento = 'Desconocido';
                        break;
                }
                if (row.operationType == '0' && row.eventType == '2') {
                    evento = 'Fichada';
                }
                evento = evento + operation;
                console.log('foto: '+row.regPhoto);
                let foto = '';
                if (row.regPhoto) {
                    url_foto = `${row.regPhoto}`;
                    foto = `<img loading="lazy" src="${row.regPhoto}" class="w40 h40 radius img-fluid"></img>`;
                } else {
                    url_foto = ``;
                    foto = `<i class="bi bi-card-image font1 text-secondary"></i>`;
                }

                let datacol = `<div class="pic scale w50 h50 shadow-sm d-flex justify-content-center align-items-center pointer">${foto}</div>`
                return datacol;
            },
        },
        /** Columna Usuario */
        {
            className: '', targets: '', title: `
            <div class="d-none d-sm-block w150">Usuario</div>
            <div class="d-block d-sm-none w100">Usuario</div>
            `,
            "render": function (data, type, row, meta) {
                let nameuser = (row['userName']) ? row['userName'] : '<span class="text-danger font-weight-bold">Usuario inválido</span>';
                let datacol = `
                    <div class="smtdcol">
                        <div class="searchName pointer text-truncate d-none d-sm-block" style="max-width: 150px;">${nameuser}</div>
                        <div class="pointer text-truncate d-block d-sm-none" style="max-width: 100px;">${nameuser}</div>
                        <div class="searchID pointer text-secondary fontp">${row.userID}</div>
                    </div>
                    `
                return datacol;
            },
        },
        /** Columna Fecha DIA */
        {
            className: '', targets: '', title: `
            <span class="d-none d-sm-block w70">Día</span>
            <span class="d-sm-none d-block w70">Fecha</span>
            `,
            "render": function (data, type, row, meta) {
                let datacol = `
                    <div class="w70 d-none d-sm-block">
                        <span class="pointer">${row.regDay}</span>
                    </div>
                    <div class="w70 d-block d-sm-none">
                        <span class="">${row.regDate}</span><br>
                        <span class="text-secondary fontp">${row.regDay}</span>
                    </div>
                    `
                return datacol;
            },
        },
        /** Columna Fecha */
        {
            className: 'd-none d-sm-block', targets: '', title: '<div class="w70">Fecha</div>',
            "render": function (data, type, row, meta) {
                // let datacol = `<div class="ls1">${row.regDate}</div>`
                // return datacol;
                let datacol = `
                <div class="smtd">
                    <span class="">${row.regDate}</span>
                </div>`
                return datacol;
            },
        },
        /** Columna HORA */
        {
            className: '', targets: '', title: '<div class="w40">Hora</div>',
            "render": function (data, type, row, meta) {
                let datacol = `<div class="font-weight-bold ls1">${row.regTime}</div>`
                return datacol;
            },
        },
        /** Columna Mapa */
        {
            className: '', targets: '', title: '<div class="w40">Mapa</div>',
            "render": function (data, type, row, meta) {
                let linkMapa = `https://www.google.com/maps/place/${row.regLat},${row.regLng}`;
                let iconMapa = (row.regLat != '0') ? `<a href="${linkMapa}" target="_blank" rel="noopener noreferrer" data-titlet="Ver Mapa"><i class="bi bi-pin-map-fill btn btn-sm btn-outline-info border-0 linkMapa"></i></a>` : `<i data-titler="Sin datos GPS" class="bi bi-x-lg btn btn-sm btn-outline-danger border-0 linkMapa"></i>`
                let datacol = `<div>${iconMapa}</div>`
                return datacol;
            },
        },
        /** Columna Tipo */
        {
            className: '', targets: '', title: '<div class="w70">Tipo</div>',
            "render": function (data, type, row, meta) {
                // let eventType = (row.eventType == '2') ? 'Fichada' : 'Evento';
                let evento = '';
                switch (row.operationType) {
                    case '-1':
                        evento = 'Fichada';
                        break;
                    case '1':
                        evento = 'Ronda';
                        break;
                    case '3':
                        evento = 'Evento';
                        break;
                    default:
                        evento = 'Desconocido';
                        break;
                }
                if (row.operationType == '0' && row.eventType == '2') {
                    evento = 'Fichada';
                }
                row.operation = (row.operation == '0') ? '' : row.operation;
                let datacol = `<div class="">${evento}<br>${row.operation}</div>`
                return datacol;
            },
        },
        /** Columna Dispositivo */
        {
            className: 'w-100', targets: '', title: '<div class="" style="max-width: 170px; min-width:170px;" >Dispositivo</div>',
            "render": function (data, type, row, meta) {
                let btnAdd = `<button data-titlet="Agregar Dispositivo" class="btn btn-sm btn-outline-success border-0 ml-1 addDevice" data-phoneid='${row.phoneid}'><i class="bi bi-plus-circle"></i></button>`;
                let device = (!row.deviceName) ? `<div class="text-danger"><label class="m-0 p-0 w130 fontq">${row.phoneid}</label>${btnAdd}</div>` : `<div class="">${row.deviceName}</div><div class="text-secondary fontp">${row.phoneid}</div>`;

                let datacol = `<div class="smtdcol">${device}</div>`
                return datacol;
            },
        },
    ],
    lengthMenu: [[5, 10, 25, 50, 100, 200], [5, 10, 25, 50, 100, 200]],
    bProcessing: false,
    serverSide: true,
    deferRender: true,
    searchDelay: 1000,
    paging: true,
    searching: true,
    info: true,
    ordering: false,
    scrollY: '50vh',
    scrollCollapse: true,
    // fixedHeader: true,
    language: {
        "url": "../../js/DataTableSpanishShort2.json?v=" + vjs()
    },
});
// on draw dt
tablemobile.on('init.dt', function () {
    $('.dr').append(`
        <div class="mx-2">
            <input type="text" readonly class="pointer form-control text-center w250 ls1 bg-white" name="_dr" id="_drMob">
        </div>
    `);
    dateRange()
    $('#_drMob').on('apply.daterangepicker', function (ev, picker) {
        $('#_drMob2').val($('#_drMob').val())
        // $('#_drMob').daterangepicker({startDate: $('#min').val(), endDate: $('#max').val()});
        loadingTable('#table-mobile');
        $('#table-mobile').DataTable().ajax.reload();
    });
    $('.SoloFic').html(`<div class="custom-control custom-switch custom-control-inline d-flex justify-content-end">
        <input type="checkbox" class="custom-control-input" id="SoloFic" name="SoloFic" value="0">
        <label class="custom-control-label" for="SoloFic" style="padding-top: 3px;">
            <span class="text-dark d-none d-lg-block">Solo Fichadas</span>
            <span class="text-dark d-block d-lg-none" style="padding-top:1px">Fichadas</span>
        </label>
    </div>`)
    $('#RowTableMobile').removeClass('invisible')
    // $('#table-mobile_filter input').addClass('w250')
    $('#table-mobile_filter input').attr('placeholder', 'Filtrar ID / Nombre')
});
tablemobile.on('draw.dt', function (e, settings) {
    e.preventDefault();
    return true
});
tablemobile.on('page.dt', function () {
    loadingTable('#table-mobile')
});
// tablemobile.on('search.dt', function () {
//     loadingTable('#table-mobile')
// });
tablemobile.on('xhr.dt', function (e, settings, json) {
    tablemobile.off('xhr.dt');
});

$(document).on('click', '.searchID', function (e) {
    e.preventDefault();
    tablemobile.search($(this).text()).draw();
    classEfect('#table-mobile_filter input', 'border-custom')
});
$(document).on('click', '.searchName', function (e) {
    e.preventDefault();
    tablemobile.search($(this).text()).draw();
    classEfect('#table-mobile_filter input', 'border-custom')
});
$(document).on('change', '#SoloFic', function (e) {
    e.preventDefault()
    loadingTable('#table-mobile')
    if ($(this).is(':not(:checked)')) {
        if ($(this).val() != '') {
            $(this).val('0')
            $('#table-mobile').DataTable().ajax.reload()
        }
    } else {
        if ($(this).val() != '') {
            $(this).val('1')
            $('#table-mobile').DataTable().ajax.reload()
        }
    }
});
{/* <div class="copyRegig" data-clipboard-text="HOLA A TODO">HOLA</div> */ }
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

function initMap() {

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
// $('.select2').select2({
//     minimumResultsForSearch: -1,
//     placeholder: "Seleccionar"
// });

$(document).on("click", ".pic", function (e) {
    let data = tablemobile.row($(this).parents("tr")).data();
    // console.log(data);
    $('#pic').modal('show')
    // let picfoto = (data.regPhoto) ? 'fotos/' + data.userCompany + '/' + data.regPhoto : '';
    let picfoto = (data.regPhoto) ? data.regPhoto : '';
    let picnombre = data.userName;
    let picDevice = data.deviceName
    let picIDUser = data.userID
    let pichora = data.regTime
    let picdia = data.regDay + ' ' + data.regDate
    let _lat = data.regLat
    let _lng = data.regLng

    picDevice = (!picDevice) ? `${data.phoneid}` : picDevice;

    $('#latitud').val(_lat)
    $('#longitud').val(_lng)
    $('#modalFoto').val(picfoto)
    $('#modalNombre').val(picnombre)
    $("input[name=lat]").val(_lat);
    $("input[name=lng]").val(_lng);

    if (picfoto) {
        $('.picFoto').html('<img loading="lazy" src= "' + picfoto + '" class="w150 img-fluid rounded"/>');
        $('.divFoto').show()
    } else {
        $('.divFoto').hide()
    }

    $('.picName').html(picnombre);
    $('.picDevice').html(picDevice);
    $('.picIDUser').html(picIDUser);
    $('.picHora').html('<b>' + pichora + '</b>');

    let evento = '';
    switch (data.operationType) {
        case '-1':
            evento = 'Fichada';
            break;
        case '1':
            evento = 'Ronda';
            break;
        case '3':
            evento = 'Evento';
            break;
        default:
            evento = 'Desconocido';
            break;
    }
    if (data.operationType == '0' && data.eventType == '2') {
        evento = 'Fichada';
    }
    data.operation = (data.operation == '0') ? '' : data.operation;
    let picTipo = `${evento} ${data.operation}`
    $('.picTipo').html(picTipo);
    $('.picDia').html(picdia);

    let position = (parseFloat(_lat) + parseFloat(_lng))
    if (position != '0') {
        $('#mapzone').show()
        $('.modal-body #noGPS').html('')
        initMap()
    } else {
        $('#mapzone').hide();
        $('.modal-body #noGPS').html('<div class="text-center mt-2 m-0 p-0 fontq"><span>Ubicación GPS no disponible</span></div>')
    }
});

$('#pic').on('hidden.bs.modal', function (e) {
    clean()
})

$('#expandContainer').on('click', function (e) {
    e.preventDefault()
    if ($('#container').hasClass('container-fluid')) {
        $(this).html('<i class="bi bi-arrows-angle-expand"></i>')
        $(this).attr('data-titlet', 'Expandir')
        $('#container').removeClass('container-fluid')
        $('#container').addClass('container')
        $('#navBarPrimary').show()
        // cancelFullScreen()

    } else {
        $(this).html('<i class="bi bi-arrows-angle-contract"></i>')
        $(this).attr('data-titlet', 'Contraer')
        $('#container').addClass('container-fluid')
        $('#container').removeClass('container')
        $('#navBarPrimary').hide()
        // tablemobile.columns.adjust().draw();
        tablemobile.ajax.reload(null, false);
        // launchFullScreen(document.documentElement)
    }
});
function clean() {
    $('#mapzone').hide();
    $("#map_size").val('5')
    $('.modal-body #noGPS').html('')
}
function actualizar(noti = true) {

    if (noti) {
        ActiveBTN(true, ".actualizar", loading, '<i class="bi bi-cloud-download-fill"></i>')
        notify('Actualizando registros <span class = "dotting mr-1"> </span> ' + loading, 'dark', 60000, 'right')
    };


    axios({
        method: 'post',
        url: 'actualizar.php'
    }).then(function (response) {
        let data = response.data.Response
        // set session storage
        let date = new Date()
        sessionStorage.setItem($('#_homehost').val() + '_LastTranferMobile: ' + date, JSON.stringify(data));
        if (data.status == "ok") {
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
        alert('ERROR actualizar\n' + error);
    }).then(function () {
        ActiveBTN(false, ".actualizar", 'Actualizando..' + loading, '<i class="bi bi-cloud-download-fill"></i>')
    });
}

$(document).on("click", ".actualizar", function (e) {
    actualizar()
});

$(document).on("click", "#Encabezado", function (e) {
    CheckSesion()
    loadingTable('#table-mobile');
    loadingTableUser('#tableUsuarios');
    loadingTableDevices('#tableDevices');
    minmaxDate()
    // actualizar(false);
});

const enableBtnMenu = (e) => {
    $('#btnMenu .btn').prop('readonly', false)
    $('#btnMenu #positionMap').prop('disabled', false)
    hideMapMarcadores();
}

const focusBtn = (selector) => {
    $('#btnMenu .btn').removeClass('btn-custom');
    $('#btnMenu .btn').addClass('btn-outline-custom');
    $(selector).removeClass('btn-outline-custom').addClass('btn-custom');
}

const focusRowTables = () => {
    $('#RowTableMobile').hide();
    $('#RowTableUsers').hide();
    $('#RowTableDevices').hide();
    // $('.loading').show()
}

$(document).on("click", ".showUsers", function (e) {
    CheckSesion()
    enableBtnMenu()
    $(this).prop('readonly', true)
    focusBtn(this);
    document.title = "Usuarios Mobile"
    $('#Encabezado').html("Usuarios Mobile");
    focusRowTables()
    // $('.loading').hide()
    $('#RowTableUsers').show();
});
$(document).on("click", ".showDevices", function (e) {
    CheckSesion()
    enableBtnMenu()
    $(this).prop('readonly', true)
    focusBtn(this);
    document.title = "Dispositivos Mobile"
    $('#Encabezado').html("Dispositivos Mobile");
    focusRowTables()
    // $('.loading').hide()
    $('#RowTableDevices').show();
});
$(document).on("click", ".showChecks", function (e) {
    CheckSesion()
    enableBtnMenu()
    $(this).addClass('btn-custom');
    $(this).prop('readonly', true)
    focusBtn(this);
    document.title = "Fichadas Mobile"
    $('#Encabezado').html("Fichadas Mobile")
    focusRowTables()
    // $('.loading').hide()
    $('#RowTableMobile').show();
    loadingTable('#table-mobile');
    $('#table-mobile').DataTable().columns.adjust().draw();
    // minmaxDate()
});
$(document).on("click", ".sendCH", function (e) {
    CheckSesion()
    e.preventDefault();
    var legFech = $(this).attr('data-legFech')
    let dataRecid = $(this).attr('data-recid')
    $.ajax({
        type: 'POST',
        url: 'crud.php',
        'data': {
            legFech: legFech,
            tipo: 'transferir'
        },
        beforeSend: function (data) {
            ActiveBTN(true, "#" + dataRecid, '<i class="spinner-border fontp wh15"></i>', '<i class="bi bi-forward fontt"></i>')
        },
        success: function (data) {
            if (data.status == "ok") {
                ActiveBTN(false, "#" + dataRecid, '<i class="b   bi-forward fontt"></i>', '<i class="bi bi-forward fontt"></i>')
                notify(data.Mensaje, 'success', 3000, 'right')
                setTimeout(() => {
                    Procesar(data.Fecha, data.Fecha, data.Legajo, data.Legajo)
                }, 3000);
            } else {
                // $.notifyClose();
                ActiveBTN(false, "#" + dataRecid, '<i class="bi bi-forward fontt"></i>', '<i class="bi bi-forward fontt"></i>')
                notify(data.Mensaje, 'danger', 3000, 'right')
            }
        },
        error: function () {
            // $.notifyClose();
            ActiveBTN(false, "#" + dataRecid, '<i class="bi bi-forward fontt"></i>', '<i class="bi bi-forward fontt"></i>')
            notify(data.Mensaje, 'danger', 3000, 'right')
        }
    });
});
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
        startDate: $('#min').val(),
        maxDate: $('#max').val(),
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
        let dr = minFormat + ' al ' + maxFormat
        $('#min').val(minFormat)
        $('#max').val(maxFormat)
        $('#_drMob2').val(dr)
        $('#_drMob').val(dr)
    }).then(() => {
        tablemobile.ajax.reload();
        $('#tableUsuarios').DataTable().ajax.reload();
        $('#tableDevices').DataTable().ajax.reload();
        // dateRange()
    }).catch(function (error) {
        alert('ERROR minmaxDate\n' + error);
    }).then(function () {

    });
}
$(".selectjs_cuentaToken").select2({
    multiple: false,
    language: "es",
    placeholder: "Cambiar de Cuenta",
    minimumInputLength: "0",
    minimumResultsForSearch: -1,
    maximumInputLength: "10",
    selectOnClose: false,
    language: {
        noResults: function () {
            return "No hay resultados..";
        },
        inputTooLong: function (args) {
            var message =
                "Máximo " +
                "10" +
                " caracteres. Elimine " +
                overChars +
                " caracter";
            if (overChars != 1) {
                message += "es";
            }
            return message;
        },
        searching: function () {
            return "Buscando..";
        },
        errorLoading: function () {
            return "Sin datos..";
        },
        inputTooShort: function () {
            return "Ingresar " + "0" + " o mas caracteres";
        },
        maximumSelected: function () {
            return "Puede seleccionar solo una opción";
        }
    },
    ajax: {
        url: "getCuentasApi.php",
        dataType: "json",
        type: "POST",
        data: function (params) {
            return {};
        },
        processResults: function (data) {
            return {
                results: data
            };
        }
    }
});
$(".selectjs_cuentaToken").on("select2:select", function (e) {
    CheckSesion();
    $("#RefreshToken").submit();
    // $('#map').html('').removeClass('shadow').css('height', '0px');
});
$("#RefreshToken").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
        type: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        beforeSend: function (data) {
        },
        success: function (data) {
            if (data.status == "ok") {
                loadingTable('#table-mobile');
                loadingTableUser('#tableUsuarios');
                loadingTableDevices('#tableDevices');
                minmaxDate()
                actualizar(false);
            }
        },
        error: function () { }
    });
});
// focusRowTables()
// $('#RowTableUsers').show();
