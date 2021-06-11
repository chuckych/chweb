$(document).ready(function () {
    // $('#modalUsuarios').modal('show')
});
$('#Encabezado').addClass('pointer')
function loadingTable(selectortable) {
    $(selectortable + ' td div').addClass('bg-light text-light')
    $(selectortable + ' td img').addClass('invisible')
    $(selectortable + ' td i').addClass('invisible')
    $(selectortable + ' td span').addClass('invisible')
}
function loadingTableRemove(selectortable) {
    $(selectortable + ' td div').removeClass('bg-light text-light')
    $(selectortable + ' td img').removeClass('invisible')
    $(selectortable + ' td i').removeClass('invisible')
    $(selectortable + ' td span').removeClass('invisible')
}
function dateRange() {
    $('#_drMob').daterangepicker({
        singleDatePicker: false,
        showDropdowns: false,
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
        autoApply: false,
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

// $('#btnFiltrar').removeClass('d-sm-block');
let drmob2 = $('#min').val() + ' al ' + $('#max').val()
$('#_drMob2').val(drmob2)

$('#divTableMobile').prepend(`<div class="pb-3 custom-control custom-switch custom-control-inline d-flex align-items-center justify-content-end m-0"><input checked type="checkbox" class="custom-control-input" id="SoloFic" name="SoloFic" value="1"><label class="custom-control-label" for="SoloFic" style="padding-top: 3px;"><span class="text-dark d-none d-lg-block">Solo Fichadas</span><span class="text-dark d-block d-lg-none">Fichadas</span></label></div>`)

tablemobile = $('#table-mobile').DataTable({
    "initComplete": function (settings, json) {
        $('#table-mobile_filter').append('<button data-titlel="Usuarios"type="button" class="h35 mx-1 btn btn-custom btn-sm px-3 openModal"><i class="bi bi-people-fill"></i></button><button data-titlel="Actualizar registros" class="btn btn-sm btn-custom fontq actualizar h35 px-3"><i class="bi bi-cloud-download"></i></button>')
        $('.dr').append(`<div><input type="text" readonly  class="mx-2 form-control text-center w250 ls1" name="_dr" id="_drMob"></div>`)
        dateRange()
        $('#_drMob').on('apply.daterangepicker', function (ev, picker) {
            $('#_drMob2').val($('#_drMob').val())
            $('#table-mobile').DataTable().ajax.reload();
        });
        $('.form-control-sm').attr('placeholder', 'Buscar')
    },
    "drawCallback": function (settings) {
        // CheckSesion()
        classEfect("#table-mobile tbody", 'animate__animated animate__fadeIn')
        setTimeout(function () {
            loadingTableRemove('#table-mobile')
        }, 100);
    },
    // iDisplayLength: -1,
    dom: "<'row'<'col-12 col-sm-6 d-flex align-items-start dr'l><'col-12 col-sm-6 d-flex align-items-start justify-content-end'f>>" +
        "<'row'<'col-12'tr>>" +
        "<'row'<'col-sm-12 col-md-6 d-flex align-items-start'i><'col-sm-12 col-md-6 d-flex justify-content-end'p>>",
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
        $(row).addClass('animate__animated animate__fadeIn align-middle');
    },
    columns: [
        {
            "class": "text-center",
            "data": "face_url"
        },
        {
            "class": '',
            "data": "id_user"
        },
        {
            "class": '',
            "data": "name"
        },
        {
            "class": '',
            "data": "Fecha"
        },
        {
            "class": "ls1",
            "data": "Fecha2"
        },
        {
            "class": "ls1 fw5 text-center",
            "data": "time"
        },
        {
            "class": "text-center",
            "data": "mapa"
        },
        {
            "class": "text-center ls1",
            "data": "eventType"
        },
        {
            "class": "ls1",
            "data": "phoneid"
        },
        {
            "class": "text-center",
            "data": "sendch"
        },
        {
            "class": "text-center",
            "data": "regid"
        },
    ],
    bProcessing: true,
    serverSide: true,
    deferRender: true,
    searchDelay: 1000,
    paging: true,
    searching: true,
    info: true,
    ordering: false,
    language: {
        "url": "../../js/DataTableSpanishShort2.json"
    },
});

$('#SoloFic').change(function () {
    if ($('#SoloFic').is(':not(:checked)')) {
        if ($('#SoloFic').val() != '') {
            $('#SoloFic').val('0')
            $('#table-mobile').DataTable().ajax.reload()
        }
    } else {
        if ($('#SoloFic').val() != '') {
            $('#SoloFic').val('1')
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
    var picfoto = $(this).attr('datafoto');
    var picnombre = $(this).attr('dataname');
    var picuid = $(this).attr('datauid');
    var picIDUser = $(this).attr('data-iduser');
    var piccerteza = $(this).attr('datacerteza');
    var piccerteza2 = $(this).attr('datacerteza2');
    var picinout = $(this).attr('datainout');
    var piczone = $(this).attr('datazone');
    var pichora = $(this).attr('datahora');
    var picgps = $(this).attr('datagps');
    var pictype = $(this).attr('datatype');
    var picdia = $(this).attr('datadia');
    var _lat = $(this).attr('datalat');
    var _lng = $(this).attr('datalng');

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

    if (piccerteza > 70) {
        $('.picCerteza').html('<img src="../img/check.png" class="w15" alt="' + piccerteza + '" title="' + piccerteza + '">&nbsp;<span class="fontp fw4 text-success">(' + piccerteza2 + ')</span>');
    } else {
        $('.picCerteza').html('<img src="../img/uncheck.png" class="w15" alt="' + piccerteza + '" title="' + piccerteza + '">&nbsp;<span class="fontp fw4 text-danger">(' + piccerteza2 + ')</span>');
    }
    if (position != '0') {
        if (piczone) {
            $('#btnCrearZona').addClass('d-none')
        } else {
            $('#btnCrearZona').removeClass('d-none')
        }
        var zone = (piczone) ? '<span class="text-success">' + piczone + '</span>' : '<span class="text-danger">Fuera de Zona</span>';
        $('.picZona').html(zone);
    } else {
        $('.picZona').html('Sin ubicaci&oacute;n');
    }
    // console.log(position);
    if (position != '0') {
        $('#mapzone').removeClass('d-none');
        initMap()
    } else {
        $('#mapzone').addClass('d-none');
        $('#btnCrearZona').addClass('d-none')
    }

    $(document).on("click", "#btnCrearZona", function (e) {
        fadeInOnly('#rowCreaZona')
        $("#rowRespuesta").addClass("d-none");

        $("#map_size").val('200')
        initMap()
        fadeInOnly('#mapzone')

        $('.select2').on('select2:select', function (e) {
            var select_val = $(e.currentTarget).val();
            $("#map_size").val(select_val)
            initMap()
            fadeInOnly('#mapzone')
        });

        $('#rowCreaZona').removeClass('d-none')

        $("#CrearZona").bind("submit", function (e) {
            e.preventDefault();
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
                        $('#btnCrearZona').addClass('d-none')
                        $("#btnSubmitZone").prop("disabled", false);
                        $("#btnSubmitZone").html("Aceptar");
                        $("#rowRespuesta").removeClass("d-none");
                        $("#respuesta").html('<div class="alert alert-success fontq"><b>¡Zona creada correctamente!<br>La misma se ver&aacute; reflejada en futuras marcaciones.</b></div>')
                        $("#rowCreaZona").addClass("d-none");
                        setTimeout(function () {
                            $('#rowRespuesta').addClass('d-none')
                        }, 4000);
                        $("#map_size").val(data.radio)
                        initMap()
                    } else {
                        $("#btnSubmitZone").prop("disabled", false);
                        $("#btnSubmitZone").html("Aceptar");
                    }
                },
                error: function () {
                    $("#btnSubmitZone").prop("disabled", false);
                    $("#btnSubmitZone").html("Aceptar");
                    // $("#rowCreaZona").hide();
                }
            });
        });

    });
    $(document).on("click", "#cancelZone", function (e) {
        clean()
        initMap()
    });
});

$('#pic').on('hidden.bs.modal', function (e) {
    clean()
})

function clean() {
    $('#mapzone').addClass('d-none');
    // $('.select2').val('200').trigger("change");
    $("#map_size").val('5')
}

let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`

function actualizar() {
    $.ajax({
        type: 'POST',
        url: 'actualizar.php',
        beforeSend: function (data) {
            ActiveBTN(true, ".actualizar", loading, '<i class="bi bi-cloud-download"></i>')
            notify('Actualizando registros <span class = "dotting mr-1"> </span> ' + loading, 'dark', 60000, 'right')
        },
        success: function (data) {
            if (data.status == "ok") {
                $.notifyClose();
                ActiveBTN(false, ".actualizar", loading, '<i class="bi bi-cloud-download"></i>')
                notify(data.Mensaje, 'success', 2000, 'right')
                minmaxDate()
            } else {
                $.notifyClose();
                ActiveBTN(false, ".actualizar", loading, '<i class="bi bi-cloud-download"></i>')
                notify(data.Mensaje, 'info', 2000, 'right')
            }
        },
        error: function () {
            $.notifyClose();
            ActiveBTN(false, ".actualizar", 'Actualizando..' + loading, 'Actualizar <i class="bi bi-cloud-download"></i>')
            notify('Error', 'danger', 2000, 'right')
        }
    });
}

$(document).on("click", ".actualizar", function (e) {
    actualizar()
});

$(document).on("click", "#Encabezado", function (e) {
    CheckSesion()
    tablemobile.ajax.reload();
});


tablemobile.on('processing.dt', function (e, settings, processing) {
    e.preventDefault()
    CheckSesion()
    loadingTable('#table-mobile')
    e.stopImmediatePropagation();
});

$(document).on("click", ".openModal", function (e) {
    CheckSesion()
    $('#modalUsuarios').modal('show')
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

    $.ajax({
        type: 'POST',
        url: 'minmaxdate.php',
        success: function (data) {
            let t = data
            let min = t.min
            let max = t.max
            let dr = min + ' al ' + max
            $('#min').val(min)
            $('#max').val(max)
            $('#_drMob2').val(dr)
            $('#_drMob').val(dr)
            dateRange()
            // CheckSesion()
            tablemobile.ajax.reload();
        },
    });
}

// });