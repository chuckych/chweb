$('#Encabezado').addClass('pointer')
$('#RowTableUsers').hide();
$('#RowTableDevices').hide();
const loadingTable = (selectortable) => {
    $(selectortable + ' td div').addClass('bg-light text-light border-0 h50')
    $(selectortable + ' td img').addClass('invisible')
    $(selectortable + ' td i').addClass('invisible')
    $(selectortable + ' td span').addClass('invisible')
}
let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
actualizar(false);

function dateRange() {
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

tablemobile = $('#table-mobile').DataTable({
    // iDisplayLength: -1,
    dom: "<'row mt-2'" +
        "<'col-12 col-sm-6 d-flex align-items-start dr'l><'col-12 col-sm-6 d-inline-flex align-items-center justify-content-end'<'SoloFic'>f>>" +
        "<'row'<'col-12 border shadow-sm max-h-500 overflow-auto't>>" +
        "<'row d-none d-sm-block'<'col-12 d-flex align-items-center justify-content-between'ip>>" +
        "<'row d-block d-sm-none'<'col-12 d-flex align-items-center justify-content-center'p>>" +
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
        // $(row).addClass('animate__animated animate__fadeIn align-middle');
    },
    columns: [
        {
            className: 'align-middle text-center', targets: 'regPhoto', title: 'Foto',
            "render": function (data, type, row, meta) {
                let urlToFile = `fotos/${row.userCompany}/${row.regPhoto}`;
                let nameuser = (row.userName) ? ': ' + row.userName : '';
                let nameuser2 = (row.userName) ? '' + row.userName : '';
                let gps = (row.gpsStatus != '0') ? 'Ok' : 'Sin GPS';
                // let evento = row.eventType + row.operationType + row.operation;

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

                let foto = '';
                let url_foto = '';
                if (row.regPhoto) {
                    url_foto = `fotos/${row.userCompany}/${row.regPhoto}`;
                    foto = `<img loading="lazy" src="fotos/${row.userCompany}/${row.regPhoto}" class="scale w40 h40 radius img-fluid"></img>`;
                } else {
                    url_foto = ``;
                    foto = `<i class="bi bi-card-image font1 text-secondary"></i>`;
                }

                let datacol = `<div class="pic w50 h50 border d-flex justify-content-center align-items-center pointer" datafoto="${url_foto}" data-iduser="${row.userID}" dataname="${nameuser2}" datauid="${row.phoneid}" datahora="${row.regTime}" datadia="${row.regDay}" datagps="${gps}" datatype="${evento}" datalat="${row.regLat}" datalng="${row.regLng}">${foto}</div>`
                return datacol;
            },
        },
        {
            className: 'align-middle', targets: '', title: 'ID',
            "render": function (data, type, row, meta) {
                let datacol = `<div class="w90"><span class="searchID pointer">${row.userID}</span></div>`
                return datacol;
            },
        },
        {
            className: 'align-middle', targets: '', title: 'Nombre',
            "render": function (data, type, row, meta) {
                let nameuser = (row['userName']) ? row['userName'] : '<span class="text-danger font-weight-bold">Usuario invalido</span>';
                let datacol = `<div class="Mw150"><span class="searchName pointer">${nameuser}</span></div>`
                return datacol;
            },
        },
        {
            className: 'align-middle', targets: '', title: 'Día',
            "render": function (data, type, row, meta) {
                let datacol = `<div class="w70">${row.regDay}</div>`
                return datacol;
            },
        },
        {
            className: 'align-middle', targets: '', title: 'Fecha',
            "render": function (data, type, row, meta) {
                let datacol = `<div class="ls1">${row.regDate}</div>`
                return datacol;
            },
        },
        {
            className: 'align-middle text-center', targets: '', title: 'Hora',
            "render": function (data, type, row, meta) {
                let datacol = `<div class="font-weight-bold ls1">${row.regTime}</div>`
                return datacol;
            },
        },
        {
            className: 'align-middle', targets: '', title: 'Mapa',
            "render": function (data, type, row, meta) {
                let linkMapa = `https://www.google.com/maps/place/${row.regLat},${row.regLng}`;
                let iconMapa = (row.regLat != '0') ? `<a href="${linkMapa}" target="_blank" rel="noopener noreferrer" data-titlet="Ver Mapa"><i class="bi bi-pin-map-fill btn btn-sm btn-outline-info border-0 linkMapa"></i></a>` : `<i data-titler="Sin datos GPS" class="bi bi-x-lg btn btn-sm btn-outline-danger border-0 linkMapa"></i>`
                let datacol = `<div>${iconMapa}</div>`
                return datacol;
            },
        },
        {
            className: 'align-middle', targets: '', title: 'Tipo',
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
        {
            className: 'align-middle w-100', targets: '', title: 'Dispositivo',
            "render": function (data, type, row, meta) {
                let btnAdd = `<button data-titlet="Agregar Dispositivo" class="btn btn-sm btn-outline-success border-0 ml-1 addDevice" data-phoneid='${row.phoneid}'><i class="bi bi-plus-circle"></i></button>`;
                let device = (!row.deviceName) ? `<div class="text-danger"><label class="m-0 p-0 w130 fontq">${row.phoneid}</label>${btnAdd}</div>` : row.deviceName; 

                let datacol = `<div class="">${device}</div>`
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
    language: {
        "url": "../../js/DataTableSpanishShort2.json?v=" + vjs()
    },
});
// on draw dt
tablemobile.on('init.dt', function () {

    // $('#btnMenu').html(`
    //     <button data-titlet="Gestión de usuarios" type="button" class="h35 mr-1 btn btn-outline-custom border-ddd btn-sm px-3 showUsers fontq">
    //         <span class="d-none d-sm-block w100">Usuarios <i class="ml-2 bi bi-people-fill"></i></span>
    //         <span class="d-block d-sm-none"><i class="bi bi-people-fill"></i></span>
    //     </button>
    //     <button data-titlel="Actualizar registros" class="btn btn-sm btn-custom fontq actualizar h35 px-3 float-right">
    //         <i class="bi bi-cloud-download-fill"></i>
    //     </button>`);
    $('.dr').append(`
        <div class="mx-2">
            <input type="text" readonly class="pointer form-control text-center w250 ls1 bg-white" name="_dr" id="_drMob">
        </div>`);

    dateRange()

    $('#_drMob').on('apply.daterangepicker', function (ev, picker) {
        $('#_drMob2').val($('#_drMob').val())
        loadingTable('#table-mobile');
        $('#table-mobile').DataTable().ajax.reload();
    });

    $('.SoloFic').html(`<div class="custom-control custom-switch custom-control-inline d-flex justify-content-end pb-2">
        <input type="checkbox" class="custom-control-input" id="SoloFic" name="SoloFic" value="0">
        <label class="custom-control-label" for="SoloFic" style="padding-top: 3px;">
            <span class="text-dark d-none d-lg-block">Solo Fichadas</span>
            <span class="text-dark d-block d-lg-none fontp" style="padding-top:2px">Fichadas</span>
        </label>
    </div>`)
    $('#RowTableMobile').removeClass('invisible')
    $('#table-mobile_filter input').addClass('w250')
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
tablemobile.on('xhr', function (e, settings, json) {
    tablemobile.off('xhr');
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

let copyRegig = new ClipboardJS('.copyRegig');

copyRegig.on('success', function (e) {
    $.notifyClose();
    notify('Copiado', 'warning', 1000, 'right')
    setTimeout(function () {
        $.notifyClose();
    }, 1000);
    e.clearSelection();
});

copyRegig.on('error', function (e) {
    $.notifyClose();
    notify('Error al copiar', 'danger', 1000, 'right')
    setTimeout(function () {
        $.notifyClose();
    }, 1000);
    e.clearSelection();
});

function initMap() {

    var lati = parseFloat($('#latitud').val())
    var long = parseFloat($('#longitud').val())
    var zona = ($('#zona').val())
    var zona = (zona) ? zona : 'Fuera de Zona';
    var radio = parseFloat($('#map_size').val())

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
    const contentString = '<div id="content"><span>' + zona + "</span></div>";

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
// $('.select2').select2({
//     minimumResultsForSearch: -1,
//     placeholder: "Seleccionar"
// });

$(document).on("click", ".pic", function (e) {

    $('#pic').modal('show')
    let picfoto = $(this).attr('datafoto');
    let picnombre = $(this).attr('dataname');
    let picuid = $(this).attr('datauid');
    let picIDUser = $(this).attr('data-iduser');
    let piccerteza = $(this).attr('datacerteza');
    let piccerteza2 = $(this).attr('datacerteza2');
    let picinout = $(this).attr('datainout');
    let piczone = $(this).attr('datazone');
    let pichora = $(this).attr('datahora');
    let picgps = $(this).attr('datagps');
    let pictype = $(this).attr('datatype');
    let picdia = $(this).attr('datadia');
    let _lat = $(this).attr('datalat');
    let _lng = $(this).attr('datalng');

    $('#latitud').val(_lat)
    $('#longitud').val(_lng)

    $("input[name=lat]").val(_lat);
    $("input[name=lng]").val(_lng);

    $('#zona').val(piczone)
    if (picfoto) {
        $('.picFoto').html('<img loading="lazy" src= "' + picfoto + '" class="w150 img-fluid rounded"/>');
        $('.divFoto').show()
    } else {
        $('.divFoto').hide()
    }

    $('.picName').html(picnombre);
    $('.picUid').html(picuid);
    $('.picIDUser').html(picIDUser);
    $('.picHora').html('<b>' + pichora + '</b>');
    $('.picModo').html(picinout);
    $('.picTipo').html(pictype);
    $('.picDia').html(picdia);

    var position = (parseFloat(_lat) + parseFloat(_lng))

    // if (piccerteza > 70) {
    //     $('.picCerteza').html('<img src="../img/check.png" class="w15" alt="' + piccerteza + '" title="' + piccerteza + '">&nbsp;<span class="fontp fw4 text-success">(' + piccerteza2 + ')</span>');
    // } else {
    //     $('.picCerteza').html('<img src="../img/uncheck.png" class="w15" alt="' + piccerteza + '" title="' + piccerteza + '">&nbsp;<span class="fontp fw4 text-danger">(' + piccerteza2 + ')</span>');
    // }
    // if (position != '0') {
    //     if (piczone) {
    //         $('#btnCrearZona').addClass('d-none')
    //     } else {
    //         $('#btnCrearZona').removeClass('d-none')
    //     }
    //     let zone = (piczone) ? '<span class="text-success">' + piczone + '</span>' : '<span class="text-danger">Fuera de Zona</span>';
    //     $('.picZona').html(zone);
    // } else {
    //     $('.picZona').html('Sin ubicaci&oacute;n');
    // }
    // console.log(position);
    if (position != '0') {
        $('#mapzone').removeClass('d-none');
        initMap()
    } else {
        $('#mapzone').addClass('d-none');
        // $('#btnCrearZona').addClass('d-none')
    }

    // $(document).on("click", "#btnCrearZona", function (e) {
    //     fadeInOnly('#rowCreaZona')
    //     $("#rowRespuesta").addClass("d-none");

    //     $("#map_size").val('200')
    //     initMap()
    //     fadeInOnly('#mapzone')

    //     $('.select2').on('select2:select', function (e) {
    //         var select_val = $(e.currentTarget).val();
    //         $("#map_size").val(select_val)
    //         initMap()
    //         fadeInOnly('#mapzone')
    //     });

    //     $('#rowCreaZona').removeClass('d-none')

    //     $("#CrearZona").bind("submit", function (e) {
    //         e.preventDefault();
    //         $.ajax({
    //             type: $(this).attr("method"),
    //             url: $(this).attr("action"),
    //             data: $(this).serialize(),
    //             // dataType: "json",
    //             beforeSend: function (data) {
    //                 $("#btnSubmitZone").prop("disabled", true);
    //                 $("#btnSubmitZone").html("Creando Zona.!");
    //             },
    //             success: function (data) {
    //                 if (data.status == "ok") {
    //                     $('#btnCrearZona').addClass('d-none')
    //                     $("#btnSubmitZone").prop("disabled", false);
    //                     $("#btnSubmitZone").html("Aceptar");
    //                     $("#rowRespuesta").removeClass("d-none");
    //                     $("#respuesta").html('<div class="alert alert-success fontq"><b>¡Zona creada correctamente!<br>La misma se ver&aacute; reflejada en futuras marcaciones.</b></div>')
    //                     $("#rowCreaZona").addClass("d-none");
    //                     setTimeout(function () {
    //                         $('#rowRespuesta').addClass('d-none')
    //                     }, 4000);
    //                     $("#map_size").val(data.radio)
    //                     initMap()
    //                 } else {
    //                     $("#btnSubmitZone").prop("disabled", false);
    //                     $("#btnSubmitZone").html("Aceptar");
    //                 }
    //             },
    //             error: function () {
    //                 $("#btnSubmitZone").prop("disabled", false);
    //                 $("#btnSubmitZone").html("Aceptar");
    //                 // $("#rowCreaZona").hide();
    //             }
    //         });
    //     });

    // });
    // $(document).on("click", "#cancelZone", function (e) {
    //     clean()
    //     initMap()
    // });
});

$('#pic').on('hidden.bs.modal', function (e) {
    clean()
})

function clean() {
    $('#mapzone').addClass('d-none');
    $("#map_size").val('5')
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
    loadingTableUser('#tableUsuarios')
    loadingTable('#table-mobile');
    tablemobile.ajax.reload();
    $('#tableUsuarios').DataTable().ajax.reload();
});

const enableBtnMenu = (e) => {
    $('#btnMenu .btn').prop('readonly', false)
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
}

$(document).on("click", ".showUsers", function (e) {
    CheckSesion()
    enableBtnMenu()
    $(this).prop('readonly', true)
    focusBtn(this);
    document.title = "Usuarios Mobile HR"
    $('#Encabezado').html("Usuarios Mobile HR");
    focusRowTables()
    $('#RowTableUsers').show();
});
$(document).on("click", ".showDevices", function (e) {
    CheckSesion()
    enableBtnMenu()
    $(this).prop('readonly', true)
    focusBtn(this);
    document.title = "Dispositivos Mobile HR"
    $('#Encabezado').html("Dispositivos Mobile HR");
    focusRowTables()
    $('#RowTableDevices').show();
});
$(document).on("click", ".showChecks", function (e) {
    CheckSesion()
    enableBtnMenu()
    $(this).addClass('btn-custom');
    $(this).prop('readonly', true)
    focusBtn(this);
    document.title = "Fichadas Mobile HR"
    $('#Encabezado').html("Fichadas Mobile HR")
    focusRowTables()
    $('#RowTableMobile').show();
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

function minmaxDate() {
    axios({
        method: 'post',
        url: 'minmaxdate.php'
    }).then(function (response) {
        let data = response.data
        let t = data
        let min = t.min
        let max = t.max
        let dr = min + ' al ' + max
        $('#min').val(min)
        $('#max').val(max)
        $('#_drMob2').val(dr)
        $('#_drMob').val(dr)
        dateRange()

    }).then(() => {
        tablemobile.ajax.reload();
        $('#tableUsuarios').DataTable().ajax.reload();
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
$("#RefreshToken").bind("submit", function (e) {
    e.preventDefault();
    $.ajax({
        type: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        beforeSend: function (data) {
            CheckSesion();
        },
        success: function (data) {
            if (data.status == "ok") {
                loadingTable('#table-mobile');
                loadingTableUser('#tableUsuarios');
                minmaxDate()
                actualizar(false);
            }
        },
        error: function () { }
    });
});