
const loadingTableZones = (selectorTable) => {
    $(selectorTable).addClass('loader-in');
}

const domTableZones = () => {
    if ($(window).width() < 540) {
        return `<'row lengthFilterTable'<'col-12 d-flex align-items-end m-0 justify-content-between'lf>><'row '<'col-12 table-responsive't>><'fixed-bottom'<''<'d-flex p-0 justify-content-center'p><'pb-2'i>>>`
    }
    return `<'row lengthFilterTable'<'col-12 d-flex align-items-end m-0 justify-content-between'lf>><'row '<'col-12 table-responsive't>><'row d-none d-sm-block'<'col-12 d-flex align-items-center justify-content-between'ip>>`;
}
const columnZones540 = (row) => {
    let del = `<span data-titlel="Eliminar" class="btn btn-outline-custom border bi bi-trash delZone"></span>`
    if (row.totalZones > 1) {
        del = `<span data-titlel="No se puede eliminar" class="btn btn-outline-custom border bi bi-trash disabled"></span>`
    }
    return `
    <p class="text-uppercase font-weight-bold text-secondary my-0 py-0">${row.zoneName}</p>
    <span class="text-secondary">Radio: ${row.zoneRadio} Mts.</span>
    <div class="d-flex justify-content-end w-100">
    <span data-titlel="Editar Zona" class="mr-1 btn btn-outline-custom border bi bi-pen updZone"></span>
    ${del}
    </div>
    `
}

const dtZones = () => {

    if ($.fn.DataTable.isDataTable('#tableZones')) {
        $('#tableZones').DataTable().ajax.reload(null, false);
        return false;
    }

    const tableZones = $('#tableZones').DataTable({
        dom: domTableZones(),
        ajax: {
            url: "getZonesMobile.php",
            type: "POST",
            "data": function (data) { },
            error: function () { },
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass('fadeIn align-middle');
        },
        columns: [
            /** Columna Nombre */
            {
                className: '', targets: '', title: `<div class="w250 ">Zona</div>`,
                render: function (data, type, row, meta) {
                    if (type !== 'display') {
                        return '';
                    }
                    if ($(window).width() < 540) {
                        return columnZones540(row);
                    }
                    return `<div class="d-flex flex-column">
                        <div title="${row.zoneName}" class="text-truncate w250">${row.zoneName}</div><span class="text-secondary fontp">Evento: ${row.zoneEvent}</span>
                    </div>`;
                },
            },
            /** Columna Radio */
            {
                className: 'text-center', targets: '', title: `<div class="w40">Radio</div>`,
                render: function (data, type, row, meta) {
                    if (type !== 'display') {
                        return '';
                    }
                    if ($(window).width() < 540) return false;
                    return `<div class="w40 ls1">${row.zoneRadio}</div>`
                }, visible: visible540(),
            },
            /** Columna cant TotalZones */
            {
                className: 'text-center', targets: '', title: '<div class="w70">Registros</div>',
                render: function (data, type, row, meta) {
                    if (type !== 'display') {
                        return '';
                    }
                    if ($(window).width() < 540) return false;
                    return `<div class="ls1 w70">${row.totalZones}</div>`
                }, visible: visible540(),
            },
            /** Columna Lat / Lng */
            {
                className: 'align-middle', targets: '', title: `<div class="w90">Lat / Lng</div>`,
                render: function (data, type, row, meta) {
                    if (type !== 'display') {
                        return '';
                    }
                    if ($(window).width() < 540) return false;
                    return `<div data-titlet="" class="ls1 w90 fontp text-secondary">${row.zoneLat}<br>${row.zoneLng}</div>`
                }, visible: visible540(),
            },
            /** Columna Acciones */
            {
                className: 'w-100', targets: '', title: '',
                render: function (data, type, row, meta) {
                    if (type !== 'display') {
                        return '';
                    }
                    if ($(window).width() < 540) return false;
                    let del = `<span><button data-titlel="Eliminar" class="btn btn-outline-custom border-0 bi bi-trash delZone"></button></span>`
                    if (row.totalZones > 1) {
                        del = `<span><button data-titlel="No se puede eliminar" class="btn btn-outline-custom border-0 bi bi-trash disabled"></button></span>`
                    }
                    return `
                    <div class="float-right border p-1">
                        <span><button data-titlel="Editar Zona" class="mr-1 btn btn-outline-custom border-0 bi bi-pen updZone"></button></span>
                        ${del}
                    </div>
                    `
                }, visible: visible540(),
            },
        ],
        lengthMenu: lengthMenuUsers(),
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        searchDelay: 500,
        paging: true,
        searching: true,
        info: true,
        ordering: false,
        scrollY: visible540() ? '100%' : '370px',
        scrollCollapse: true,
        scrollX: true,
        fixedHeader: false,
        language: DT_SPANISH_SHORT2
    });
    tableZones.on('init.dt', function (e, settings) {
        $('#tableZones_filter').prepend('<button data-titlel="Nueva Zona" class="btn btn-sm btn-custom h40 opa8 px-3" id="addZone"><i class="bi bi-plus-lg"></i></button>');
        $('#tableZones_filter input').removeClass('form-control-sm');
        $('#tableZones_filter input').attr("style", "height: 40px !important");
        select2Simple('#tableZones_length select', '', false, false);
    });
    tableZones.on('draw.dt', function (e, settings) {
        $('#tableZones_filter .form-control-sm').attr('placeholder', 'Buscar Zonas');
        $('#RowTableZones').removeClass('invisible');
        $('#tableZones').removeClass('loader-in');
    });
    tableZones.on('page.dt', function (e, settings) {
        loadingTableZones('#tableZones');
    });
    tableZones.on('xhr', function (e, settings, json) {
        tableZones.off('xhr');
    });
}
const mapZone = (latitud, longitud, zoom = 4) => {
    $("input[name=lat]").val(latitud);
    $("input[name=lng]").val(longitud);
    var center = new google.maps.LatLng(latitud, longitud);
    const image = '../../img/iconMarker.svg'

    $("#geocomplete").geocomplete({
        map: ".map_canvas",
        details: "form",
        autoselect: true,
        blur: true,
        markerOptions: {
            position: center,
            draggable: true,
            icon: image
        },
        location: center,
        types: ["geocode", 'establishment'],
        country: 'ar',  //restricciones de paises

        mapOptions: {
            scrollwheel: true,
            scaleControl: true,
            zoomControl: true,
            fullscreenControl: true,
            disableDefaultUI: true,
            geocodeAfterResult: true,
            mapTypeId: "roadmap",
            styles: [
                {
                    "featureType": "administrative",
                    "elementType": "labels.text.fill",
                    "stylers": [
                        {
                            "color": "#4a5461"
                        }
                    ]
                }
            ]
        },
    });

    var map = $("#geocomplete").geocomplete("map")

    // map.setCenter(center);
    map.setZoom(zoom);
    // autoselect: true
    $("#reset").hide();
    $("#geocomplete").bind("geocode:dragged", function (event, latLng) {
        $("input[name=lat]").val(latLng.lat());
        $("input[name=lng]").val(latLng.lng());
        if ($('#geocomplete').val() != '') {
            $("#reset").show();
        }
    });

    // $("#reset").click(function () {
    $("#reset").on("click", function () {
        $("#geocomplete").geocomplete("resetMarker");
        $("#geocomplete").geocomplete("map");
        $("#reset").hide();
        return false;
    });

    $("#find").on("click", function () {
        $("input[name=formZoneLat]").val(center.lat());
        $("input[name=formZoneLng]").val(center.lng());
        $("#geocomplete").trigger("geocode");
    })
}
const select2Radio = (selector) => {
    $(selector).select2({
        placeholder: "Radio en Metros",
        allowClear: false,
        maximumInputLength: 6,
        width: '100%',
        language: "es",
        tags: true,
        tokenSeparators: [',', ' '],
        minimumResultsForSearch: 11
    })
}
$(document).on("click", "#addZone", function (e) {

    const modalZone = ls.get(LS_MODALES);

    if (!modalZone) {
        return;
    }
    $('#modales').html(modalZone)

    $('#modalZone .modal-title').html('Nueva Zona');
    $('#modalZone').modal('show');
    $('#formZone .requerido').html('(*)');
    $('#formZone .form-control').attr('autocomplete', '_chweb_off');
    $('#formZone #formZoneTipo').val('add_zone');
    select2Radio("#formZone #formZoneRadio");
    setTimeout(() => {
        $('#formZone .form-control').attr('autocomplete', '_chweb_off');
        $('#formZone #geocomplete').focus();
        $('#formZone #geocomplete').removeAttr('readonly');
    }, 500);

    const defaulLat = -34.6037389;
    const defaulLng = -58.3815704;
    mapZoneNear(defaulLat, defaulLng, 8);

    $("#formZone").bind("submit", function (e) {
        e.preventDefault();
        const mapTipoStatus = {
            'del_device': 'eliminada',
            'upd_device': 'actualizada',
            'add_device': 'Creada'
        }
        const valueTipo = $('#formZone #formZoneTipo').val() || '';
        $.ajax({
            type: $(this).attr("method"),
            url: 'crud.php',
            data: $(this).serialize() + '&tipo=' + valueTipo,
            // dataType: "json",
            beforeSend: function (data) {
                // CheckSesion()
                $.notifyClose();
                notify('Aguarde..', 'info', 0, 'right');
                ActiveBTN(true, "#submitZone", 'Aguarde ' + loading, 'Aceptar');
            },
            success: function (data) {
                if (data.status == "ok") {
                    $.notifyClose();
                    const zoneName = data.Mensaje.zoneName;
                    notify('Zona <b>' + zoneName + '</b><br />' + (mapTipoStatus[valueTipo] ?? '') + ' ' + 'correctamente.', 'success', 5000, 'right');
                    $('#table-mobile').DataTable().ajax.reload(null, false);
                    $('#tableZones').DataTable().search(zoneName).draw();
                    classEfect('#tableZones_filter input', 'border-custom');
                    $('#tableZones').DataTable().ajax.reload();
                    ActiveBTN(false, "#submitZone", 'Aguarde ' + loading, 'Aceptar');
                    $('#modalZone').modal('hide');
                } else {
                    $.notifyClose();
                    notify(data.Mensaje, 'danger', 5000, 'right');
                    ActiveBTN(false, "#submitZone", 'Aguarde ' + loading, 'Aceptar');
                }
            },
            error: function () { }
        });
    });
    $('#modalZone').on('hidden.bs.modal', function () {
        $('#modales').html('');
    });
});
$(document).on("click", ".updZone", function (e) {
    const data = $('#tableZones').DataTable().row($(this).parents('tr')).data();

    const modalZone = ls.get(LS_MODALES);

    if (!data) {
        return;
    }

    if (!modalZone) {
        return;
    }

    $('#modales').html(modalZone);

    const zoneLat = data.zoneLat;
    const zoneLng = data.zoneLng;
    const zoneRadio = data.zoneRadio;
    const zoneName = data.zoneName;
    const idZone = data.zoneID;
    const zoneEvent = data.zoneEvent;

    $('#modalZone .modal-title').html('<div>Editar Zona</div><div class="fontq text-secondary">' + zoneName + '</div>');
    $('#formZone #divGeocomplete').hide();
    $('#modalZone').modal('show');
    $('#formZone .requerido').html('(*)');
    $('#formZone #formZoneTipo').val('upd_zone');
    $('#formZone #formZoneNombre').val(zoneName);
    $('#formZone #formZoneLat').val(zoneLat);
    $('#formZone #formZoneLng').val(zoneLng);
    $('#formZone #formZoneRadio').val(zoneRadio);
    $('#formZone #formZoneEvento').val(zoneEvent);

    select2Radio("#formZone #formZoneRadio");

    setTimeout(() => {
        $('#formZone .form-control').attr('autocomplete', '_chweb_off');
        focusEndText('#formZone #formZoneNombre');
        $('#formZone #geocomplete').removeAttr('readonly');
    }, 500);

    const defaulLat = zoneLat;
    const defaulLng = zoneLng;
    mapZoneNear(defaulLat, defaulLng, 8);

    $("#formZone").bind("submit", function (e) {
        e.preventDefault();
        const mapTipoStatus = {
            'del_device': 'eliminada',
            'upd_device': 'actualizada',
            'add_device': 'Creada'
        }
        const valueTipo = $('#formZone #formZoneTipo').val() || '';

        $.ajax({
            type: $(this).attr("method"),
            url: 'crud.php',
            data: $(this).serialize() + '&tipo=' + valueTipo + '&idZone=' + idZone,
            beforeSend: function (data) {
                CheckSesion()
                $.notifyClose();
                notify('Aguarde..', 'info', 0, 'right')
                ActiveBTN(true, "#submitZone", 'Aguarde ' + loading, 'Aceptar')
            },
            success: function (data) {
                if (data.status == "ok") {
                    $.notifyClose();
                    const zoneName = data.Mensaje.zoneName;
                    notify('Zona <b>' + zoneName + '</b><br />' + (mapTipoStatus[valueTipo] ?? '') + ' ' + 'correctamente.', 'success', 5000, 'right');
                    $('#tableZones').DataTable().search(zoneName).draw();
                    classEfect('#tableZones_filter input', 'border-custom');
                    dtZones();
                    $('#table-mobile').DataTable().ajax.reload(null, false);
                    ActiveBTN(false, "#submitZone", 'Aguarde ' + loading, 'Aceptar');
                    $('#modalZone').modal('hide');
                } else {
                    $.notifyClose();
                    notify(data.Mensaje, 'danger', 5000, 'right')
                    ActiveBTN(false, "#submitZone", 'Aguarde ' + loading, 'Aceptar')
                }
            },
            error: function () { }
        });
    });
    $('#modalZone').on('hidden.bs.modal', function () {
        $('#modales').html('');
    });
});
$(document).on("click", ".delZone", function (e) {
    const data = $('#tableZones').DataTable().row($(this).parents('tr')).data();

    const modalZone = ls.get(LS_MODALES);

    if (!data) {
        return;
    }
    if (!modalZone) {
        return;
    }
    $('#modales').html(modalZone)
    const zoneLat = data.zoneLat;
    const zoneLng = data.zoneLng;
    const zoneRadio = data.zoneRadio;
    const zoneName = data.zoneName;
    const idZone = data.zoneID;

    $('#modalZone .modal-title').html('<div class="text-danger">¿Eliminar Zona?</div><div class="">' + zoneName + '</div>')
    $('#modalZone').modal('show');
    $('#formZone #formZoneTipo').val('del_zone').attr('disabled', 'disabled')
    $('#formZone .modal-dialog').removeClass('modal-lg');
    $('#formZone .modal-body').html('');

    $("#formZone").bind("submit", function (e) {
        e.preventDefault();
        const mapTipoStatus = {
            'del_device': 'eliminada',
            'upd_device': 'actualizada',
            'add_device': 'Creada'
        }
        const valueTipo = $('#formZone #formZoneTipo').val() || '';
        $.ajax({
            type: $(this).attr("method"),
            url: 'crud.php',
            data: $(this).serialize() + '&tipo=' + valueTipo + '&idZone=' + idZone,
            beforeSend: function (data) {
                // CheckSesion()
                $.notifyClose();
                notify('Aguarde..', 'info', 0, 'right');
                ActiveBTN(true, "#submitZone", 'Aguarde ' + loading, 'Aceptar');
            },
            success: function (data) {
                if (data.status == "ok") {
                    $.notifyClose();
                    const zoneName = data.Mensaje.zoneName
                    notify('Zona <b>' + zoneName + '</b><br />' + (mapTipoStatus[valueTipo] ?? '') + ' ' + 'correctamente.', 'success', 5000, 'right');
                    $('#table-mobile').DataTable().ajax.reload(null, false);
                    $('#tableZones').DataTable().ajax.reload();
                    ActiveBTN(false, "#submitZone", 'Aguarde ' + loading, 'Aceptar');
                    $('#modalZone').modal('hide');
                } else {
                    $.notifyClose();
                    notify(data.Mensaje, 'danger', 5000, 'right');
                    ActiveBTN(false, "#submitZone", 'Aguarde ' + loading, 'Aceptar');
                }
            },
            error: function () { }
        });
    });
    $('#modalZone').on('hidden.bs.modal', function () {
        $('#modales').html('');
    });
    // });

});
function getNearZonesTable($lat, $lng, createZone = false) {
    $('#formZone #divNearZone').html(`
        <div class="bg-white pb-3 invisible" id="RowTableNearZones" style="min-height:252px">
            <div class="">
            <table class="table text-nowrap w-100 border table-boderless p-2 shadow-sm" id="tableNearZones">
                <thead class="fontq"></thead>
            </table>
            </div>
        </div>
        `)
    const tableNearZones = $('#tableNearZones').DataTable({
        dom: `
            <'row '<'col-12'l>>
            <'row '<'col-12 tableResponsive't>>
            `,
        ajax: {
            url: "getNearZones.php?zoneLat=" + $lat + "&zoneLng=" + $lng,
            type: "GET",
            "data": function (data) { },
            error: function () { },
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass('fadeIn align-middle');
        },
        columns: [
            /** Columna Nombre */
            {
                className: 'align-middle py-2 fontq w-100', targets: '', title: `Zonas en cercanía`,
                render: function (data, type, row, meta) {
                    let syncZone = '';
                    let className = '';
                    if (createZone) {
                        syncZone = ((row.distance) <= (row.zoneRadio)) ? `<br><button title="Vincular a ${row.zoneName}" type="button" class="syncZone fontp p-0 mt-1 btn btn-sm btn-link"><span class="text-success">Vincular a Zona</span></button>` : '';
                        className = ((row.distance) <= (row.zoneRadio)) ? 'font-weight-bold' : '';
                    }
                    syncZone = '';
                    let datacol = `<div title="${row.zoneName}" class="text-truncate ${className}" style="max-width: 180px;">${row.zoneName} ${syncZone}</div>`
                    return datacol;
                },
            },
            /** Columna Distancia */
            {
                className: 'align-middle py-2 fontq text-right', targets: '', title: 'Distancia',
                render: function (data, type, row, meta) {
                    let className = '';
                    if (createZone) {
                        className = ((row.distance) <= (row.zoneRadio)) ? 'font-weight-bold' : '';
                    }
                    let distance = (row.distance >= 1000) ? (row.distance / 1000).toFixed(2) + ' Km.' : row.distance + ' Mts.';
                    let datacol = `<div class="float-right ${className}">${distance}</div>`
                    return datacol;
                },
            },
            /** Columna Radio */
            {
                className: 'align-middle py-2 fontq text-right', targets: '', title: 'Radio',
                render: function (data, type, row, meta) {
                    let datacol = `<div class="float-right fontq">${row.zoneRadio}</div>`
                    return datacol;
                },
            },
        ],
        lengthMenu: [[5, 10, 25], [5, 10, 25]],
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        searchDelay: 500,
        paging: true,
        searching: true,
        info: true,
        ordering: false,
        language: DT_SPANISH_SHORT2

    });
    tableNearZones.on('draw.dt', function (e, settings) {
        if (settings._iRecordsTotal > 0) {
            $('#RowTableNearZones').removeClass('invisible')
        }
    });
    tableNearZones.on('xhr.dt', function (e, settings, json) {
        tableNearZones.off('xhr');
    });
}
const mapZoneNear = (latitud, longitud, zoom = 10, createZoneOut = false) => {
    // Validación de Google Maps API
    if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
        console.error('Google Maps API no está cargada');
        return;
    }

    $("input[name=lat]").val(latitud);
    $("input[name=lng]").val(longitud);
    
    // No necesita await - LatLng es síncrono
    const center = new google.maps.LatLng(latitud, longitud);
    const image = '../../img/iconMarker.svg';
    
    if (createZoneOut === false) {
        getNearZonesTable(center.lat(), center.lng());
    }
    
    $('#RowTableNearZones').addClass('invisible');
    
    // Remover eventos previos para evitar duplicados
    $("#geocomplete").off("geocode:result geocode:dragged");
    $("#formZone #reset").off("click");
    
    // Inicializar geocomplete
    $("#geocomplete").geocomplete({
        map: ".map_canvas",
        details: "form",
        autoselect: true,
        blur: true,
        markerOptions: {
            position: center,
            draggable: true,
            icon: image
        },
        location: center,
        types: ["geocode", 'establishment'],
        country: 'ar',  //restricciones de paises

        mapOptions: {
            scrollwheel: true,
            scaleControl: true,
            zoomControl: true,
            fullscreenControl: true,
            disableDefaultUI: true,
            geocodeAfterResult: true,
            mapTypeId: "roadmap",
            styles: [
                {
                    "featureType": "administrative",
                    "elementType": "labels.text.fill",
                    "stylers": [
                        {
                            "color": "#4a5461"
                        }
                    ]
                }
            ]
        }
    });

    // Obtener el mapa y configurarlo
    const map = $("#geocomplete").geocomplete("map");
    
    // Calcular timeout dinámico según el zoom (mayor zoom = más tiempo)
    // Zoom 8 o menos: 150ms, Zoom 12: 350ms, Zoom 15+: 500ms
    const dynamicTimeout = zoom <= 8 ? 150 : (zoom <= 12 ? 350 : 500);
    
    setTimeout(() => {
        google.maps.event.trigger(map, 'resize');
        
        // Establecer centro y zoom después del resize
        map.setCenter(center);
        map.setZoom(zoom);
        
        // Segundo resize después de aplicar zoom para asegurar renderizado completo
        setTimeout(() => {
            google.maps.event.trigger(map, 'resize');
            map.setCenter(center);
        }, 100);
    }, dynamicTimeout);

    $("#formZone #reset").hide();

    // Event listener para resultado de geocodificación
    $("#geocomplete").on("geocode:result", function (event, result) {
        const lat = result.geometry.location.lat();
        const lng = result.geometry.location.lng();
        $('#RowTableNearZones').addClass('invisible');
        getNearZonesTable(lat, lng);
        focusEndText('#formZone #formZoneNombre');
    });

    // Event listener para arrastre del marcador
    $("#geocomplete").on("geocode:dragged", function (event, latLng) {
        $("input[name=lat]").val(latLng.lat());
        $("input[name=lng]").val(latLng.lng());
        
        if ($('#geocomplete').val() !== '') {
            $("#formZone #reset").show();
        }
        
        $('#RowTableNearZones').addClass('invisible');
        getNearZonesTable(latLng.lat(), latLng.lng());
        focusEndText('#formZone #formZoneNombre');
    });

    // Event listener para reset
    $("#formZone #reset").on("click", function () {
        $("#geocomplete").geocomplete("resetMarker");
        $("#geocomplete").geocomplete("map");
        $("#formZone #reset").hide();
        return false;
    });
};
const processRegZone = (lat, lng, reguid) => {
    $.ajax({
        type: 'POST',
        url: 'crud.php',
        data: 'tipo=proccesZone' + '&lat=' + lat + '&lng=' + lng + '&reguid=' + reguid,
        beforeSend: function (data) {
            // CheckSesion();
            $.notifyClose();
            notify('Aguarde..', 'info', 0, 'right');
            ActiveBTN(true, ".syncZone", 'Aguarde ' + loading, 'Vincular a Zona');
        },
        success: function (data) {
            if (data.status == "ok") {
                $.notifyClose();
                const zoneName = data.Mensaje.zoneName;
                const result = data.Mensaje.result;
                const zoneDistance = (parseFloat(data.Mensaje.zoneDistance) * 1000).toFixed(2);
                if (result) {
                    notify('Registro Procesado correctamente.<br>Zona: <b>' + zoneName + '</b><br>Distancia: <b>' + zoneDistance + ' Mts</b>', 'success', 5000, 'right');
                    $('#table-mobile').DataTable().ajax.reload(null, false);
                } else {
                    notify('Zona no encontrada', 'info', 5000, 'right');
                }
                ActiveBTN(false, ".syncZone", 'Aguarde ' + loading, 'Vincular a Zona');
                $('#modalZone').modal('hide');
            } else {
                $.notifyClose();
                if (data.Mensaje == "There are no areas available") {
                    notify('No hay Zonas disponibles', 'danger', 5000, 'right');
                } else {
                    notify(data.Mensaje, 'danger', 5000, 'right');
                }
                ActiveBTN(false, ".syncZone", 'Aguarde ' + loading, 'Vincular a Zona');
            }
        },
        error: function () { }
    });
}
$(document).on("click", ".createZoneOut", function (e) {
    e.preventDefault();
    const data = $('#table-mobile').DataTable().row($(this).parents('tr')).data();
    const modalZone = ls.get(LS_MODALES);

    if (!data) {
        return;
    }
    if (!modalZone) {
        return;
    }
    $('#modales').html(modalZone);

    const zoneLat = data.regLat;
    const zoneLng = data.regLng;
    const zoneRadio = 100;
    const zoneName = '';
    const idZone = '';
    const regUID = data.regUID;

    $('#modalZone .modal-title').html('<div>Nueva Zona</div><div class="fontq text-secondary">' + zoneName + '</div>');
    $('#modalZone').modal('show');
    $('#formZone .requerido').html('(*)');
    $('#formZone #divGeocomplete').hide();
    $('#formZone #formZoneTipo').val('create_zone');
    $('#formZone #formZoneNombre').val(zoneName);
    $('#formZone #formZoneLat').val(zoneLat);
    $('#formZone #formZoneLng').val(zoneLng);
    $('#formZone #formZoneRadio').val(zoneRadio);
    select2Radio("#formZone #formZoneRadio");

    setTimeout(() => {
        $('#formZone .form-control').attr('autocomplete', '_chweb_off');
        focusEndText('#formZone #formZoneNombre');
        $('#formZone #geocomplete').removeAttr('readonly');
    }, 500);

    getNearZonesTable(zoneLat, zoneLng, 'createZoneOut');
    const defaulLat = zoneLat;
    const defaulLng = zoneLng;
    mapZoneNear(defaulLat, defaulLng, 8, true);

    $('#formZone .modal-body').append(`
        <input type="hidden" name="regLat" value="${zoneLat}" id="vlat">
        <input type="hidden" name="regLng" value="${zoneLng}" id="vlng">
        <input type="hidden" name="regUID" value="${regUID}" id="vregUID">
    `);

    $("#formZone").bind("submit", function (e) {
        e.preventDefault();
        const valueTipo = $('#formZone #formZoneTipo').val() || '';
        $.ajax({
            type: $(this).attr("method"),
            url: 'crud.php',
            data: $(this).serialize() + '&tipo=' + valueTipo + '&idZone=' + idZone,
            beforeSend: function (data) {
                // CheckSesion()
                $.notifyClose();
                notify('Aguarde..', 'info', 0, 'right');
                ActiveBTN(true, "#submitZone", 'Aguarde ' + loading, 'Aceptar');
            },
            success: function (data) {
                if (data.status == "ok") {
                    $.notifyClose();
                    const zoneName = data.Mensaje.zoneName;
                    notify('Zona <b>' + zoneName + '</b>.<br />Creada correctamente.', 'success', 5000, 'right');
                    dtZones();
                    $('#table-mobile').DataTable().ajax.reload(null, false);
                    ActiveBTN(false, "#submitZone", 'Aguarde ' + loading, 'Aceptar');
                    $('#modalZone').modal('hide');
                } else {
                    $.notifyClose();
                    notify(data.Mensaje, 'danger', 5000, 'right');
                    ActiveBTN(false, "#submitZone", 'Aguarde ' + loading, 'Aceptar');
                }
            },
            error: function () { }
        });
    });
    $('#modalZone').on('hidden.bs.modal', function () {
        $('#modales').html('');
    });
});
$(document).on("click", ".syncZone", function (e) {
    e.preventDefault();
    processRegZone($('#vlat').val(), $('#vlng').val(), $('#vregUID').val(), 'syncZone');
    return false;
});
$(document).on("click", ".proccessZone", function (e) {
    e.preventDefault();
    const data = $('#table-mobile').DataTable().row($(this).parents('tr')).data();
    const zoneLat = data.regLat;
    const zoneLng = data.regLng;
    const regUID = data.regUID;
    processRegZone(zoneLat, zoneLng, regUID, 'proccessZone');
});

