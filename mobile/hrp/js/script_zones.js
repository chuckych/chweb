// $(document).ready(function () {
const loadingTableZones = (selectortable) => {
    $(selectortable + ' td div').addClass('bg-light text-light border-0')
    // $(selectortable + ' td div').css('height', '')
    $(selectortable + ' td img').addClass('invisible')
    $(selectortable + ' td i').addClass('invisible')
    $(selectortable + ' td span').addClass('invisible')
}
tableZones = $('#tableZones').DataTable({
    dom: "<'row lengthFilterTable'<'col-12 d-flex align-items-end m-0 justify-content-between'lf>>" +
    "<'row '<'col-12 table-responsive't>>" +
    "<'row d-none d-sm-block'<'col-12 d-flex bg-white align-items-center justify-content-between'ip>>" +
    "<'row d-block d-sm-none'<'col-12 fixed-bottom h70 bg-white d-flex align-items-center justify-content-center'p>>" +
    "<'row d-block d-sm-none'<'col-12 d-flex align-items-center justify-content-center'i>>",
    ajax: {
        url: "getZonesMobile.php",
        type: "POST",
        "data": function (data) { },
        error: function () { },
    },
    createdRow: function (row, data, dataIndex) {
        $(row).addClass('animate__animated animate__fadeIn align-middle');
    },
    columns: [
        /** Columna Nombre */
        {
            className: 'align-middle', targets: '', title: `<div class="w160">Zona</div>`,
            "render": function (data, type, row, meta) {
                let datacol = `<div title="${row.zoneName}" class="text-truncate w160">${row.zoneName}</div>`
                return datacol;
            },
        },
        /** Columna Radio */
        {
            className: 'align-middle', targets: '', title: `<div class="w40">Radio</div>`,
            "render": function (data, type, row, meta) {
                let datacol = `<div class="w40">${row.zoneRadio}</div>`
                return datacol;
            },
        },
        // /** Columna Latitud */
        // {
        //     className: 'align-middle', targets: '', title: `<div class="w90">Lat / Lng</div>`,
        //     "render": function (data, type, row, meta) {
        //         let datacol = `<div data-titlet="" class="text-truncate w90">${row.zoneLat}<br>${row.zoneLng}</div>`
        //         return datacol;
        //     },
        // },
        // /** Columna Longitud */
        // {
        //     className: 'align-middle', targets: '', title: `<div class="w90">Longitud</div>`,
        //     "render": function (data, type, row, meta) {
        //         let datacol = `<div data-titlet="" class="text-truncate ls1 w90">${row.zoneLng}</div>`
        //         return datacol;
        //     },
        // },
        /** Columna cant TotalZones */
        {
            className: 'align-middle', targets: '', title: '<div class="w50">Fichadas</div>',
            "render": function (data, type, row, meta) {
                let datacol = `<div class="ls1 w50">${row.totalZones}</div>`
                return datacol;
            },
        },
        /** Columna Acciones */
        {
            className: 'align-middle w-100', targets: '', title: '',
            "render": function (data, type, row, meta) {
                let del = `<span><button data-titlel="Eliminar" class="btn btn-outline-custom btn-sm border bi bi-trash delZone"></button></span>`
                if (row.totalZones > 1) {
                    del = `<span><button data-titlel="No se puede eliminar" class="btn btn-outline-custom btn-sm border bi bi-trash disabled"></button></span>`
                }
                let datacol = `
                <div class="d-flex justify-content-end">
                    <span><button data-titlel="Editar Zona" class="mr-1 btn btn-outline-custom btn-sm border bi bi-pen updZone"></button></span>
                    ${del}
                </div>
                `
                return datacol;
            },
        },
    ],
    lengthMenu: [[5, 10, 25, 50, 100, 200], [5, 10, 25, 50, 100, 200]],
    bProcessing: false,
    serverSide: true,
    deferRender: true,
    searchDelay: 500,
    paging: true,
    searching: true,
    info: true,
    ordering: false,
    // scrollY: '52vh',
    scrollY: '286px',
    scrollCollapse: true,
    scrollX: true,
    fixedHeader: false,
    language: {
        "url": "../../js/DataTableSpanishShort2.json?v=" + vjs(),
    },
});
tableZones.on('init.dt', function (e, settings) {
    $('#tableZones_filter').prepend('<button data-titlel="Nueva Zona" class="btn btn-sm btn-custom h40 opa8 px-3" id="addZone"><i class="bi bi-plus-lg"></i></button>')
    $('#tableZones_filter input').removeClass('form-control-sm')
    $('#tableZones_filter input').attr("style","height: 40px !important");
    select2Simple('#tableZones_length select', '', false, false)
});
tableZones.on('draw.dt', function (e, settings) {
    // $('#modalUsuarios').modal('show')
    $('#tableZones_filter .form-control-sm').attr('placeholder', 'Buscar Zonas')
    $('#RowTableZones').removeClass('invisible')
});
tableZones.on('page.dt', function (e, settings) {
    loadingTableZones('#tableZones')
});
tableZones.on('xhr', function (e, settings, json) {
    tableZones.off('xhr');
});
$(document).on("click", ".addZone", function (e) {
    let data = $('#table-mobile').DataTable().row($(this).parents('tr')).data();
    axios({
        method: 'post',
        url: 'modalZone.html?v=' + $.now(),
    }).then(function (response) {
        $('#modales').html(response.data)
    }).then(function () {
        $('#modalZone .modal-title').html('Nueva Zona')
        $('#formZonePhoneID').val(data.phoneid)
        $('#modalZone').modal('show');
        $('#formZone .requerido').html('(*)')
        $('#formZone .form-control').attr('autocomplete', 'off')
        $('#formZone #formZoneEvento').mask('0000', { reverse: false });
        $('#formZone #formZoneTipo').val('add_zone')

        setTimeout(() => {
            $('#formZone #formZoneNombre').focus();
        }, 500);

    }).then(function () {

    }).catch(function (error) {
        alert(error)
    }).then(function () {
        $("#formZone").bind("submit", function (e) {
            e.preventDefault();
            let tipoStatus = '';
            switch ($('#formZone #formZoneTipo').val()) {
                case 'del_zone':
                    tipoStatus = 'eliminado';
                    break;
                case 'upd_zone':
                    tipoStatus = 'actualizado';
                    break;
                case 'add_zone':
                    tipoStatus = 'Creado';
                    break;
                default:
                    tipoStatus = '';
                    break;
            }
            $.ajax({
                type: $(this).attr("method"),
                url: 'crud.php',
                data: $(this).serialize() + '&tipo=' + $('#formZone #formZoneTipo').val(),
                // dataType: "json",
                beforeSend: function (data) {
                    CheckSesion()
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                    ActiveBTN(true, "#submitZone", 'Aguarde ' + loading, 'Aceptar')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        let zoneName = data.Mensaje.zoneName
                        notify('Dispositivo ' + zoneName + '<br />' + tipoStatus + ' ' + 'correctamente.', 'success', 5000, 'right')
                        // $('#tableUsuarios').DataTable().ajax.reload();
                        $('#table-mobile').DataTable().ajax.reload(null, false);
                        $('#tableZones').DataTable().ajax.reload();
                        ActiveBTN(false, "#submitZone", 'Aguarde ' + loading, 'Aceptar')
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
            $('#modales').html(' ');
        });
    });

});
let mapZone = (latitud, longitud, zoom = 4) => {
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
let select2Radio = (selector) => {
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
    axios({
        method: 'post',
        url: 'modalZone.html?v=' + $.now(),
    }).then(function (response) {
        $('#modales').html(response.data)
        // $('#RowTableZones').html(response.data)
    }).then(function () {
        $('#modalZone .modal-title').html('Nueva Zona')
        $('#modalZone').modal('show');
        $('#formZone .requerido').html('(*)')
        $('#formZone .form-control').attr('autocomplete', '_chweb_off')
        // $('#formZone #formZoneRadio').mask('000000', { reverse: false });
        $('#formZone #formZoneTipo').val('add_zone')
        select2Radio("#formZone #formZoneRadio");
        setTimeout(() => {
            $('#formZone .form-control').attr('autocomplete', '_chweb_off')
            $('#formZone #geocomplete').focus();
            $('#formZone #geocomplete').removeAttr('readonly')
        }, 500);

    }).then(function () {

        let defaulLat = -38.416097;
        let defaulLng = -63.616671;
        mapZoneNear(defaulLat, defaulLng);

    }).catch(function (error) {
        alert(error)
    }).then(function () {
        $("#formZone").bind("submit", function (e) {
            e.preventDefault();
            let tipoStatus = '';
            switch ($('#formZone #formZoneTipo').val()) {
                case 'del_zone':
                    tipoStatus = 'eliminada';
                    break;
                case 'upd_zone':
                    tipoStatus = 'actualizada';
                    break;
                case 'add_zone':
                    tipoStatus = 'Creada';
                    break;
                default:
                    tipoStatus = '';
                    break;
            }
            $.ajax({
                type: $(this).attr("method"),
                url: 'crud.php',
                data: $(this).serialize() + '&tipo=' + $('#formZone #formZoneTipo').val(),
                // dataType: "json",
                beforeSend: function (data) {
                    CheckSesion()
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                    ActiveBTN(true, "#submitZone", 'Aguarde ' + loading, 'Aceptar')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        let zoneName = data.Mensaje.zoneName
                        notify('Zona <b>' + zoneName + '</b><br />' + tipoStatus + ' ' + 'correctamente.', 'success', 5000, 'right')
                        // $('#tableUsuarios').DataTable().ajax.reload();
                        $('#table-mobile').DataTable().ajax.reload(null, false);
                        $('#tableZones').DataTable().search(zoneName).draw();
                        classEfect('#tableZones_filter input', 'border-custom')
                        $('#tableZones').DataTable().ajax.reload();
                        ActiveBTN(false, "#submitZone", 'Aguarde ' + loading, 'Aceptar')
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
            $('#modales').html(' ');
        });
    });

});
$(document).on("click", ".updZone", function (e) {
    // alert('Aca estamos')
    // get data datatable row
    let data = $('#tableZones').DataTable().row($(this).parents('tr')).data();
    // console.log(data);
    let zoneLat = data.zoneLat;
    let zoneLng = data.zoneLng;
    let zoneRadio = data.zoneRadio;
    let zoneName = data.zoneName;
    let idZone = data.zoneID;
    // return false;
    axios({
        method: 'post',
        url: 'modalZone.html?v=' + $.now(),
    }).then(function (response) {
        $('#modales').html(response.data)
        // $('#RowTableZones').html(response.data)
    }).then(function () {
        $('#modalZone .modal-title').html('<div>Editar Zona</div><div class="fontq text-secondary">' + zoneName + '</div>')
        $('#modalZone').modal('show');
        $('#formZone .requerido').html('(*)')
        // $('#formZone #formZoneRadio').mask('000000', { reverse: false });
        $('#formZone #formZoneTipo').val('upd_zone')
        $('#formZone #formZoneNombre').val(zoneName)
        $('#formZone #formZoneLat').val(zoneLat)
        $('#formZone #formZoneLng').val(zoneLng)
        $('#formZone #formZoneRadio').val(zoneRadio)

        select2Radio("#formZone #formZoneRadio");

        setTimeout(() => {
            $('#formZone .form-control').attr('autocomplete', '_chweb_off')
            focusEndText('#formZone #formZoneNombre')
            $('#formZone #geocomplete').removeAttr('readonly')
        }, 500);

    }).then(function () {

        let defaulLat = zoneLat;
        let defaulLng = zoneLng;
        mapZoneNear(defaulLat, defaulLng, 16);

    }).catch(function (error) {
        alert(error)
    }).then(function () {
        $("#formZone").bind("submit", function (e) {
            e.preventDefault();
            let tipoStatus = '';
            switch ($('#formZone #formZoneTipo').val()) {
                case 'del_zone':
                    tipoStatus = 'eliminada';
                    break;
                case 'upd_zone':
                    tipoStatus = 'actualizada';
                    break;
                case 'add_zone':
                    tipoStatus = 'Creada';
                    break;
                default:
                    tipoStatus = '';
                    break;
            }
            $.ajax({
                type: $(this).attr("method"),
                url: 'crud.php',
                data: $(this).serialize() + '&tipo=' + $('#formZone #formZoneTipo').val() + '&idZone=' + idZone,
                // dataType: "json",
                beforeSend: function (data) {
                    CheckSesion()
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                    ActiveBTN(true, "#submitZone", 'Aguarde ' + loading, 'Aceptar')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        let zoneName = data.Mensaje.zoneName
                        notify('Zona <b>' + zoneName + '</b><br />' + tipoStatus + ' ' + 'correctamente.', 'success', 5000, 'right')
                        // $('#tableUsuarios').DataTable().ajax.reload();
                        $('#table-mobile').DataTable().ajax.reload(null, false);
                        $('#tableZones').DataTable().search(zoneName).draw();
                        classEfect('#tableZones_filter input', 'border-custom')
                        $('#tableZones').DataTable().ajax.reload();
                        ActiveBTN(false, "#submitZone", 'Aguarde ' + loading, 'Aceptar')
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
            $('#modales').html(' ');
        });
    });

});
$(document).on("click", ".delZone", function (e) {
    let data = $('#tableZones').DataTable().row($(this).parents('tr')).data();
    // console.log(data);
    let zoneLat = data.zoneLat;
    let zoneLng = data.zoneLng;
    let zoneRadio = data.zoneRadio;
    let zoneName = data.zoneName;
    let idZone = data.zoneID;
    // return false;
    axios({
        method: 'post',
        url: 'modalZone.html?v=' + $.now(),
    }).then(function (response) {
        $('#modales').html(response.data)
        // $('#RowTableZones').html(response.data)
    }).then(function () {
        $('#modalZone .modal-title').html('<div class="text-danger">¿Eliminar Zona?</div><div class="">' + zoneName + '</div>')
        $('#modalZone').modal('show');
        // $('#formZone #formZoneRadio').mask('000000', { reverse: false });
        $('#formZone #formZoneTipo').val('del_zone').attr('disabled', 'disabled')
        // $('#formZone #geocomplete').hide();
        $('#formZone .modal-dialog').removeClass('modal-lg');
        $('#formZone .modal-body').html('');
        // $('#formZone #divForm').removeClass('col-lg-6');
        // $('#formZone #divMapCanva').remove();
        // $('#formZone #reset').remove();
        // $('#formZone #formZoneNombre').val(zoneName).attr('disabled', 'disabled')
        // $('#formZone #formZoneLat').val(zoneLat).attr('disabled', 'disabled')
        // $('#formZone #formZoneLng').val(zoneLng).attr('disabled', 'disabled')
        // $('#formZone #formZoneRadio').val(zoneRadio).attr('disabled', 'disabled')

        // select2Radio("#formZone #formZoneRadio");

    }).then(function () {
    }).catch(function (error) {
        alert(error)
    }).then(function () {
        $("#formZone").bind("submit", function (e) {
            e.preventDefault();
            let tipoStatus = '';
            switch ($('#formZone #formZoneTipo').val()) {
                case 'del_zone':
                    tipoStatus = 'eliminada';
                    break;
                case 'upd_zone':
                    tipoStatus = 'actualizada';
                    break;
                case 'add_zone':
                    tipoStatus = 'Creada';
                    break;
                default:
                    tipoStatus = '';
                    break;
            }
            $.ajax({
                type: $(this).attr("method"),
                url: 'crud.php',
                data: $(this).serialize() + '&tipo=' + $('#formZone #formZoneTipo').val() + '&idZone=' + idZone,
                // dataType: "json",
                beforeSend: function (data) {
                    CheckSesion()
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                    ActiveBTN(true, "#submitZone", 'Aguarde ' + loading, 'Aceptar')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        let zoneName = data.Mensaje.zoneName
                        notify('Zona <b>' + zoneName + '</b><br />' + tipoStatus + ' ' + 'correctamente.', 'success', 5000, 'right')
                        // $('#tableUsuarios').DataTable().ajax.reload();
                        $('#table-mobile').DataTable().ajax.reload(null, false);
                        $('#tableZones').DataTable().ajax.reload();
                        ActiveBTN(false, "#submitZone", 'Aguarde ' + loading, 'Aceptar')
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
            $('#modales').html(' ');
        });
    });

});
function getNearZonesTable($lat, $lng, createZone = false) {
    $('#formZone #divNearZone').html(`
        <div class="bg-white pb-3 invisible" id="RowTableNearZones" style="min-height:252px">
            <div class="">
            <table class="table text-nowrap w-100 border table-boderless p-2" id="tableNearZones">
                <thead class="fontq"></thead>
            </table>
            </div>
        </div>
        `)
    tableNearZones = $('#tableNearZones').DataTable({
        dom: "<'row '<'col-12 tableResponsive't>>",
        ajax: {
            url: "getNearZones.php?zoneLat=" + $lat + "&zoneLng=" + $lng,
            type: "GET",
            "data": function (data) { },
            error: function () { },
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass('animate__animated animate__fadeIn align-middle');
        },
        columns: [
            /** Columna Nombre */
            {
                className: 'align-middle py-2 fontq w-100', targets: '', title: `Zonas en cercanía`,
                "render": function (data, type, row, meta) {
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
                className: 'align-middle py-2 fontq text-right', targets: '', title: ``,
                "render": function (data, type, row, meta) {
                    let className = '';
                    if (createZone) {
                        className = ((row.distance) <= (row.zoneRadio)) ? 'font-weight-bold' : '';
                    }
                    let distance = (row.distance >= 1000) ? (row.distance / 1000).toFixed(2) + ' Km.' : row.distance + ' Mts.';
                    let datacol = `<div class="float-right ${className}">${distance}</div>`
                    return datacol;
                },
            },
        ],
        lengthMenu: [[5, 10, 25, 50, 100, 200], [5, 10, 25, 50, 100, 200]],
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        searchDelay: 500,
        paging: true,
        searching: true,
        info: true,
        ordering: false,
        // scrollY: '52vh',
        // scrollCollapse: true,
        language: {
            "url": "../../js/DataTableSpanishShort2.json?v=" + vjs(),
        },

    });
    tableNearZones.on('draw.dt', function (e, settings) {
        // console.log(settings);
        if (settings._iRecordsTotal > 0) {
            $('#RowTableNearZones').removeClass('invisible')
            // $('#tableNearZones thead').remove();
        }
    });
    tableNearZones.on('xhr.dt', function (e, settings, json) {
        tableNearZones.off('xhr');
    });
}
let mapZoneNear = (latitud, longitud, zoom = 4, createZoneOut = false) => {
    $("input[name=lat]").val(latitud);
    $("input[name=lng]").val(longitud);
    var center = new google.maps.LatLng(latitud, longitud);
    const image = '../../img/iconMarker.svg'
    if (createZoneOut == false) {
        getNearZonesTable(center.lat(), center.lng());
    }
    $('#RowTableNearZones').addClass('invisible')
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
    // on change result geocomplete
    $("#geocomplete").on("geocode:result", function (event, result) {
        let lat = result.geometry.location.lat();
        let lng = result.geometry.location.lng();
        $('#RowTableNearZones').addClass('invisible')
        getNearZonesTable(lat, lng);
        focusEndText('#formZone #formZoneNombre');
    });

    var map = $("#geocomplete").geocomplete("map")
    map.setZoom(zoom);

    $("#formZone #reset").hide();

    $("#geocomplete").bind("geocode:dragged", function (event, latLng) {
        $("input[name=lat]").val(latLng.lat());
        $("input[name=lng]").val(latLng.lng());
        if ($('#geocomplete').val() != '') {
            $("#formZone #reset").show();
        }
        $('#RowTableNearZones').addClass('invisible')
        getNearZonesTable(latLng.lat(), latLng.lng());
        focusEndText('#formZone #formZoneNombre');
    });

    $("#formZone #reset").on("click", function () {
        $("#geocomplete").geocomplete("resetMarker");
        $("#geocomplete").geocomplete("map");
        $("#formZone #reset").hide();
        return false;
    });

    // $("#find").on("click", function () {
    //     $("input[name=lat]").val(center.lat());
    //     $("input[name=lng]").val(center.lng());
    //     $("#geocomplete").trigger("geocode");
    // })
}

let processRegZone = (lat, lng, reguid) => {
    $.ajax({
        type: 'POST',
        url: 'crud.php',
        data: 'tipo=proccesZone' + '&lat=' + lat + '&lng=' + lng + '&reguid=' + reguid,
        // dataType: "json",
        beforeSend: function (data) {
            CheckSesion()
            $.notifyClose();
            notify('Aguarde..', 'info', 0, 'right')
            ActiveBTN(true, ".syncZone", 'Aguarde ' + loading, 'Vincular a Zona')
        },
        success: function (data) {
            if (data.status == "ok") {
                $.notifyClose();
                let zoneName = data.Mensaje.zoneName
                let result = data.Mensaje.result
                let zoneDistance = (parseFloat(data.Mensaje.zoneDistance) * 1000).toFixed(2)
                if (result) {
                    notify('Registro Procesado correctamente.<br>Zona: <b>'+zoneName+'</b><br>Distancia: <b>'+zoneDistance+' Mts</b>', 'success', 5000, 'right')
                    $('#table-mobile').DataTable().ajax.reload(null, false);
                }else{
                    notify('Zona no encontrada', 'info', 5000, 'right')
                }
                ActiveBTN(false, ".syncZone", 'Aguarde ' + loading, 'Vincular a Zona')
                $('#modalZone').modal('hide');
            } else {
                $.notifyClose();
                if (data.Mensaje == "There are no areas available") {
                    notify('No hay Zonas disponibles', 'danger', 5000, 'right')
                }else{
                    notify(data.Mensaje, 'danger', 5000, 'right')
                }
                ActiveBTN(false, ".syncZone", 'Aguarde ' + loading, 'Vincular a Zona')
            }
        },
        error: function () { }
    });
}

$(document).on("click", ".createZoneOut", function (e) {
    e.preventDefault();
    let data = $('#table-mobile').DataTable().row($(this).parents('tr')).data();
    let zoneLat = data.regLat;
    let zoneLng = data.regLng;
    let zoneRadio = 100;
    let zoneName = '';
    let idZone = '';
    let regUID = data.regUID;

    // return false;
    axios({
        method: 'post',
        url: 'modalZone.html?v=' + $.now(),
    }).then(function (response) {
        $('#modales').html(response.data)
        // $('#RowTableZones').html(response.data)
    }).then(function () {
        $('#modalZone .modal-title').html('<div>Nueva Zona</div><div class="fontq text-secondary">' + zoneName + '</div>')
        $('#modalZone').modal('show');
        $('#formZone .requerido').html('(*)')
        $('#formZone #divGeocomplete').hide();
        // $('#formZone #formZoneRadio').mask('000000', { reverse: false });
        $('#formZone #formZoneTipo').val('create_zone')
        $('#formZone #formZoneNombre').val(zoneName)
        $('#formZone #formZoneLat').val(zoneLat)
        $('#formZone #formZoneLng').val(zoneLng)
        $('#formZone #formZoneRadio').val(zoneRadio)
        select2Radio("#formZone #formZoneRadio");

        setTimeout(() => {
            $('#formZone .form-control').attr('autocomplete', '_chweb_off')
            focusEndText('#formZone #formZoneNombre')
            $('#formZone #geocomplete').removeAttr('readonly')
        }, 500);

    }).then(function () {
        getNearZonesTable(zoneLat, zoneLng, 'createZoneOut');
        let defaulLat = zoneLat;
        let defaulLng = zoneLng;
        mapZoneNear(defaulLat, defaulLng, 16, true);

                
        $('#formZone .modal-body').append(`
        <input type="hidden" name="regLat" value="${zoneLat}" id="vlat">
        <input type="hidden" name="regLng" value="${zoneLng}" id="vlng">
        <input type="hidden" name="regUID" value="${regUID}" id="vregUID">
        `)

    }).catch(function (error) {
        alert(error)
    }).then(function () {
        $("#formZone").bind("submit", function (e) {
            e.preventDefault();
            let tipoStatus = '';
            switch ($('#formZone #formZoneTipo').val()) {
                case 'del_zone':
                    tipoStatus = 'eliminada';
                    break;
                case 'upd_zone':
                    tipoStatus = 'actualizada';
                    break;
                case 'add_zone':
                    tipoStatus = 'Creada';
                    break;
                default:
                    tipoStatus = '';
                    break;
            }
            $.ajax({
                type: $(this).attr("method"),
                url: 'crud.php',
                data: $(this).serialize() + '&tipo=' + $('#formZone #formZoneTipo').val() + '&idZone=' + idZone,
                // dataType: "json",
                beforeSend: function (data) {
                    CheckSesion()
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                    ActiveBTN(true, "#submitZone", 'Aguarde ' + loading, 'Aceptar')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        let zoneName = data.Mensaje.zoneName
                        notify('Zona <b>' + zoneName + '</b>.<br />Creada correctamente.', 'success', 5000, 'right')
                        // $('#tableUsuarios').DataTable().ajax.reload();
                        $('#table-mobile').DataTable().ajax.reload(null, false);
                        // $('#tableZones').DataTable().search(zoneName).draw();
                        // classEfect('#tableZones_filter input', 'border-custom')
                        $('#tableZones').DataTable().ajax.reload();
                        ActiveBTN(false, "#submitZone", 'Aguarde ' + loading, 'Aceptar')
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
            $('#modales').html(' ');
        });
    });

});

$(document).on("click", ".syncZone", function (e) {
    e.preventDefault();
    processRegZone($('#vlat').val(), $('#vlng').val(), $('#vregUID').val(), 'syncZone');
    return false;
});
$(document).on("click", ".proccessZone", function (e) {
    e.preventDefault();
    let data = $('#table-mobile').DataTable().row($(this).parents('tr')).data();
    let zoneLat = data.regLat;
    let zoneLng = data.regLng;
    let regUID = data.regUID;
    processRegZone(zoneLat, zoneLng, regUID, 'proccessZone');
});

