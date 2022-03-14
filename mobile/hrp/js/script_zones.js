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
        "<'row '<'col-12 border shadow-sm tableResponsive p-2't>>" +
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
            className: 'align-middle', targets: '', title: `<div class="w180">Zona</div>`,
            "render": function (data, type, row, meta) {
                let datacol = `<div title="${row.zoneName}" class="text-truncate" style="max-width: 180px;">${row.zoneName}</div>`
                return datacol;
            },
        },
        /** Columna Radio */
        {
            className: 'align-middle text-center', targets: '', title: `<div class="w50">Radio</div>`,
            "render": function (data, type, row, meta) {
                let datacol = `<div data-titlet="" class="text-truncate" style="max-width: 50px;">${row.zoneRadio}</div>`
                return datacol;
            },
        },
        /** Columna Latitud */
        {
            className: 'align-middle', targets: '', title: `<div class="w90">Latitud</div>`,
            "render": function (data, type, row, meta) {
                let datacol = `<div data-titlet="" class="text-truncate ls1" style="max-width: 90px;">${row.zoneLat}</div>`
                return datacol;
            },
        },
        /** Columna Longitud */
        {
            className: 'align-middle', targets: '', title: `<div class="w90">Longitud</div>`,
            "render": function (data, type, row, meta) {
                let datacol = `<div data-titlet="" class="text-truncate ls1" style="max-width: 90px;">${row.zoneLng}</div>`
                return datacol;
            },
        },
        /** Columna cant TotalZones */
        {
            className: 'align-middle', targets: '', title: '<div class="w50">Fichadas</div>',
            "render": function (data, type, row, meta) {
                let datacol = `<div class="ls1">${row.totalZones}</div>`
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
                    <span><button data-titlet="Editar Zona" class="mr-1 btn btn-outline-custom btn-sm border bi bi-pen updZone"></button></span>
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
    // scrollCollapse: true,
    language: {
        "url": "../../js/DataTableSpanishShort2.json?v=" + vjs(),
    },

});
tableZones.on('init.dt', function (e, settings) {
    $('#tableZones_filter').prepend('<button data-titlel="Nueva Zona" class="btn btn-sm btn-custom h35 px-3" id="addZone"><i class="bi bi-plus-lg"></i></button>')
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
function getNearZonesTable($lat, $lng) {
    $('#formZone #divNearZone').html(`
        <div class="bg-white pb-3 invisible" id="RowTableNearZones">
            <div class="">
            <table class="table table-sm text-nowrap w-100 border table-boderless" id="tableNearZones">
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
                className: 'align-middle pl-2 fontq', targets: '', title: `Zonas en cercanía`,
                "render": function (data, type, row, meta) {
                    let datacol = `<div title="${row.zoneName}" class="text-truncate" style="max-width: 180px;">${row.zoneName}</div>`
                    return datacol;
                },
            },
            {
                className: 'align-middle pr-2 fontq w-100 text-right', targets: '', title: `En Km`,
                "render": function (data, type, row, meta) {
                    let datacol = `<div data-titlet="${row.distance} km." class="float-right">${row.distance} km.</div>`
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
let mapZoneNear = (latitud, longitud, zoom = 4) => {
    $("input[name=lat]").val(latitud);
    $("input[name=lng]").val(longitud);
    var center = new google.maps.LatLng(latitud, longitud);
    const image = '../../img/iconMarker.svg'
    getNearZonesTable(center.lat(), center.lng());
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
        getNearZonesTable(latLng.lat(), latLng.lng());
    });

    $("#geocomplete").on("change", function () {
        setTimeout(() => {
            getNearZonesTable($("input[name=lat]").val(), $("input[name=lng]").val());
        }, 500);
    });

    $("#reset").on("click", function () {
        $("#geocomplete").geocomplete("resetMarker");
        $("#geocomplete").geocomplete("map");
        $("#reset").hide();
        return false;
    });

    $("#find").on("click", function () {
        $("input[name=lat]").val(center.lat());
        $("input[name=lng]").val(center.lng());
        $("#geocomplete").trigger("geocode");
    })
}
$(document).on("click", ".createZoneOut", function (e) {
    // alert('Aca estamos')
    // get data datatable row
    let data = $('#table-mobile').DataTable().row($(this).parents('tr')).data();
    // console.log(data);
    // return false;
    let zoneLat = data.regLat;
    let zoneLng = data.regLng;
    let zoneRadio = 100;
    let zoneName = '';
    let idZone = '';
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
        // $('#formZone #formZoneRadio').mask('000000', { reverse: false });
        $('#formZone #formZoneTipo').val('add_zone')
        $('#formZone #formZoneNombre').val(zoneName)
        $('#formZone #formZoneLat').val(zoneLat)
        $('#formZone #formZoneLng').val(zoneLng)
        $('#formZone #formZoneRadio').val(zoneRadio)
        getNearZonesTable(zoneLat, zoneLng);
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