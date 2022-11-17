$('#Encabezado').addClass('pointer')
$('#RowTableUsers').hide();
$('#RowTableDevices').hide();
$('#RowTableZones').hide();
sessionStorage.setItem('tab_32', 'visible');

document.addEventListener("visibilitychange", function () {
    state = document.visibilityState;
    sessionStorage.setItem('tab_32', state);
});
$(window).on('load', function () {
    $('.loading').hide()
});
const loadingTable = (selectortable) => {
    $(selectortable + ' td div').addClass('bg-light text-light border-0 h50')
    $(selectortable + ' td img').addClass('invisible')
    $(selectortable + ' td i').addClass('invisible')
    $(selectortable + ' td span').addClass('invisible')
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
    if (table) {
        let data = [];
        data._drMob = $("#_drMob").val() ?? '';
        data._drMob2 = $("#_drMob2").val() ?? '';
        data.SoloFic = $("#SoloFic").val() ?? '';
        return data;
    }
    return f;
}
let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
let host = $('#_host').val()

if ($("#actMobile").val() == '1') {
    // actualizar(false);
    // actualizar2(false);
    // actualizar3(false);
    // actualizar4(false);
    actualizar_aws(false);
    setInterval(() => {
        // actualizar(false);
        // actualizar2(false);
        // actualizar3(false);
        // actualizar4(false);
        actualizar_aws(false);
    }, 15000);
} else {
    if ((host != 'https://localhost') && (host != 'http://localhost')) {
        setInterval(() => {
            if (sessionStorage.getItem('tab_32') == 'visible') { // Si la pestaña del navegador esta activa consultamos si hay datos nuevos
                let apiMobile = sessionStorage.getItem($('#_homehost').val() + '_api_mobile');
                fetchCreatedDate('createdDate.php')
            }
        }, 5000); // cada 5 segundos
    }
}

$.fn.DataTable.ext.pager.numbers_length = 5;
// $('#btnFiltrar').removeClass('d-sm-block');
let drmob2 = $('#max').val() + ' al ' + $('#max').val()
$('#_drMob2').val(drmob2)
if ($(window).width() < 540) {
    tablemobile = $('#table-mobile').DataTable({
        dom: "<'row lengthFilterTable'" +
            "<'col-12 col-sm-6 d-flex align-items-start dr'l><'col-12 col-sm-6 d-inline-flex align-items-start justify-content-end'<'SoloFic mt-2'>f>>" +
            "<'row '<'col-12 table-responsive't>>" +
            "<'fixed-bottom'<'bg-white'<'d-flex p-0 justify-content-center'p><'pb-2'i>>>",
        ajax: {
            url: "getRegMobile.php",
            type: "POST",
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
                className: 'text-center', targets: 'imageData', title: '<div class="w70">Fichadas</div>',
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

                    let foto = '';
                    if (row.imageData.img) {
                        // url_foto = `fotos/${row.userCompany}/${row.imageData.img}`;
                        url_foto = `${row.imageData.img}`;
                        foto = `<img loading="lazy" src="${row.imageData.img}" class="w60 h60 radius img-fluid"></img>`;
                    } else {
                        url_foto = ``;
                        foto = `<i class="bi bi-card-image font1 text-secondary"></i>`;
                        // foto = `<img loading="lazy" src="${row.imageData.img}" class="w40 h40 radius img-fluid"></img>`;
                    }

                    if (row.attPhoto == 1) {
                        foto = `<i class="bi bi-card-image font1 text-secondary"></i>`;
                    }
                    if (row.basePhoto) {
                        foto = `<img src="data:image/jpeg;base64,${row.basePhoto}" alt="${row.userName}" class="w40 h40 radius img-fluid" />`
                    }

                    let datacol = `<div class="pic w70 h70 shadow-sm d-flex justify-content-center align-items-center pointer">${foto}</div>`
                    return datacol;
                },
            },
            /** Columna Usuario */
            {
                className: 'text-left w-100', targets: '', title: `
                <div class="w-100"></div>
                `,
                "render": function (data, type, row, meta) {

                    let btnAdd = ''
                    let nameZone = (row.zoneName == null) ? 'Fuera de Zona' : row.zoneName;
                    nameZone = (row.regLat == 0) ? '' : nameZone;
                    let zoneName = (row.zoneID > 0) ? '<span class="text-success">' + nameZone + '</span>' : '<div class="text-danger pt-1">' + nameZone + '</div>'
                    let zoneName2 = (row.zoneID > 0) ? row.zoneName : 'Fuera de Zona'
                    let Distance = (row.zoneID > 0) ? '. Distancia: ' + row.zoneDistance + ' mts' : ''
                    let Distance2 = (row.zoneID > 0) ? '' + row.zoneDistance + ' mts' : ''

                    btnAdd = `<span class="ml-2">
                        <span title="Crear Zona" class="text-secondary fontp btn p-0 m-0 btn-link createZoneOut mt-1"><i class="bi bi-plus px-2 p-1 border"></i></span>
                        <span title="Procesar Zona" class="text-secondary fontp btn p-0 m-0 btn-link proccessZone mt-1"><i class="bi bi-arrow-left-right ml-1 px-2 p-1 border"></i></span>
                    </span>`;
                    if (row.regLat == 0) {
                        btnAdd = `<span class="text-danger p-0 m-0">Sin datos GPS</span>`;
                    }
                    let device = (row.zoneID == 0) ? `<div class="text-danger"><label class="m-0 p-0 fontq">${zoneName}</label>${btnAdd}</div>` : `<span class="">${zoneName}</span><span class="text-secondary fontp ml-2">${Distance2}</span>`;


                    let nameuser = (row['userName']) ? row['userName'] : '<span class="text-danger font-weight-bold">Usuario inválido</span>';
                    let datacol = `
                        <div class="smtdcol">
                            <div class="searchName pointer">${nameuser}</div>
                            <div class="searchID pointer text-secondary d-none">${row.userID}</div>
                            <span class="">${row.regDay} ${row.regDate} <span class="font-weight-bold ls1">${row.regTime}</span></span>
                            <span title="${zoneName2}" class="">${device}</span>
                        </div>
                        `
                    return datacol;
                },
            },
        ],
        lengthMenu: [[3, 10, 25, 50, 100, 200], [3, 10, 25, 50, 100, 200]],
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        searchDelay: 1000,
        paging: true,
        searching: true,
        info: true,
        ordering: false,
        // scrollY: '43vh',
        scrollY: '415px',
        scrollCollapse: true,
        scrollX: true,
        fixedHeader: false,
        language: {
            "url": "../../js/DataTableSpanishShort2.json?v=" + vjs()
        },
    });
} else {
    tablemobile = $('#table-mobile').DataTable({
        // iDisplayLength: 5,
        dom: "<'row lengthFilterTable'" +
            "<'col-12 col-sm-6 d-flex align-items-start dr'l><'col-12 col-sm-6 d-inline-flex align-items-start justify-content-end'<'SoloFic mt-2'>f>>" +
            "<'row '<'col-12 table-responsive't>>" +
            "<'row d-none d-sm-block'<'col-12 d-flex bg-transparent align-items-center justify-content-between'<i><p>>>" +
            "<'row d-block d-sm-none'<'col-12 fixed-bottom h70 bg-white d-flex align-items-center justify-content-center'p>>" +
            "<'row d-block d-sm-none'<'col-12 d-flex align-items-center justify-content-center'i>>",
        ajax: {
            url: "getRegMobile.php",
            type: "POST",
            // data: filterData(1),
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
                className: 'text-center', targets: 'imageData', title: '<div class="w50">Foto</div>',
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

                    let foto = '';
                    if (row.imageData.img) {
                        // url_foto = `fotos/${row.userCompany}/${row.imageData.img}`;
                        url_foto = `${row.imageData.img}`;
                        let path = document.getElementById('apiMobile').value + '/chweb/mobile/hrp/'
                        foto = `<img loading="lazy" src="${path}${url_foto}" class="w40 h40 radius img-fluid">`;
                    } else {
                        url_foto = ``;
                        foto = `<i class="bi bi-card-image font1 text-secondary"></i>`;
                    }
                    if (row.attPhoto == 1) {
                        foto = `<i class="bi bi-card-image font1 text-secondary"></i>`;
                    }
                    if (row.basePhoto) {
                        foto = `<img src="data:image/jpeg;base64,${row.basePhoto}" alt="${row.userName}" class="w40 h40 radius img-fluid" />`
                    }
                    let datacol = `<div class="pic scale w50 h50 shadow-sm d-flex justify-content-center align-items-center pointer">${foto}</div>`
                    return datacol;
                },
            },
            /** Columna Usuario */
            {
                className: 'text-left', targets: '', title: `
                <div class="w150">Usuario</div>
                `,
                "render": function (data, type, row, meta) {
                    let nameuser = (row['userName']) ? row['userName'] : '<span class="text-danger font-weight-bold">Usuario inválido</span>';
                    let datacol = `
                        <div class="smtdcol">
                            <div class="searchName pointer text-truncate" style="width: 150px;">${nameuser}</div>
                            <div class="searchID pointer text-secondary fontp">${row.userID}</div>
                        </div>
                        `
                    return datacol;
                },
            },
            /** Columna Fecha DIA */
            {
                className: '', targets: '', title: `
                <div class="w70">Fecha</div>
                `,
                "render": function (data, type, row, meta) {
                    let datacol = `
                        <div class="w70">
                            <span class="">${row.regDate}</span><br>
                            <span class="text-secondary fontp">${row.regDay}</span>
                        </div>
                        `
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
            /** Columna FACE */
            {
                className: 'text-center', targets: '', title: '<div class="w40">Rostro</div>',
                "render": function (data, type, row, meta) {
                    let confidenceFaceStr = '';
                    let datacol = '';
                    let processRegFace = 'processRegFace pointer';
                    // console.log(row.confidenceFaceStr);
                    if (row.confidenceFaceStr == 'Identificado') {
                        confidenceFaceStr = `<span data-titler="${row.confidenceFaceStr}" class="font1 text-success bi bi-person-bounding-box"></span>`
                    } else if (row.confidenceFaceStr == 'No Identificado') {
                        confidenceFaceStr = `<span data-titler="${row.confidenceFaceStr}" class="font1 text-danger bi bi-person-bounding-box"></span>`
                    } else if (row.confidenceFaceStr == 'No Enrolado') {
                        confidenceFaceStr = `<span data-titler="${row.confidenceFaceStr}" class="font1 text-warning bi bi-person-bounding-box"></span>`
                    } else if (row.confidenceFaceStr == 'Foto Inválida') {
                        confidenceFaceStr = `<span data-titler="${row.confidenceFaceStr}" class="font1 text-info bi bi-person-bounding-box"></span>`
                    } else if (row.confidenceFaceStr == 'No Disponible') {
                        confidenceFaceStr = `<span data-titler="${row.confidenceFaceStr}" class="font1 text-primary bi bi-person-bounding-box"></span>`
                        datacol = `<div class="w40">${confidenceFaceStr}</div>`
                        return datacol;
                    } else if (row.confidenceFaceStr == 'Entrenamiento Inválido') {
                        confidenceFaceStr = `<span data-titler="${row.confidenceFaceStr}" class="font1 text-warning bi bi-person-bounding-box"></span>`
                        datacol = `<div class="w40">${confidenceFaceStr}</div>`
                        return datacol;
                    }
                    datacol = `<div class="w40 ${processRegFace}" title="${row.confidenceFaceVal}">${confidenceFaceStr}</div>`
                    return datacol;
                },
            },
            /** Columna Zona */
            {
                className: '', targets: '', title: '<div class="w120">Zona</div>',
                "render": function (data, type, row, meta) {
                    // let btnAdd = `<button data-titlet="Agregar Dispositivo" class="btn btn-sm btn-outline-success border-0 ml-1 addDevice" data-phoneid='${row.phoneid}'><i class="bi bi-plus-circle"></i></button>`;
                    let btnAdd = ''
                    let zoneName = (row.zoneID > 0) ? '<div class="text-success">' + row.zoneName + '</div>' : '<div class="text-danger">Fuera de Zona</div>'
                    let zoneName2 = (row.zoneID > 0) ? row.zoneName : 'Fuera de Zona'
                    let Distance = (row.zoneID > 0) ? '. Distancia: ' + row.zoneDistance + ' mts' : ''
                    let Distance2 = (row.zoneID > 0) ? '' + row.zoneDistance + ' mts' : ''

                    btnAdd = `<div>
                        <span title="Crear Zona" class="text-secondary fontp btn p-0 m-0 btn-link createZoneOut mt-1"><i class="bi bi-plus px-2 p-1 border"></i></span>
                        <span title="Procesar Zona" class="text-secondary fontp btn p-0 m-0 btn-link proccessZone mt-1"><i class="bi bi-arrow-left-right ml-1 px-2 p-1 border"></i></span>
                    </div>`;
                    if (row.regLat == 0) {
                        btnAdd = `<div class="text-secondary fontp p-0 m-0">Sin datos GPS</div>`;
                    }
                    let device = (row.zoneID == 0) ? `<div class="text-danger"><label class="m-0 p-0 fontq">${zoneName}</label>${btnAdd}</div>` : `<div class="">${zoneName}</div><div class="text-secondary fontp">${Distance2}</div>`;

                    let datacol = `<div title="${zoneName2}" class="w120 text-truncate py-2">${device}</div>`
                    return datacol;
                },
            },
            /** Columna Mapa */
            {
                className: '', targets: '', title: '<div class="w40">Mapa</div>',
                "render": function (data, type, row, meta) {
                    let linkMapa = `https://www.google.com/maps/place/${row.regLat},${row.regLng}`;
                    let iconMapa = (row.regLat != '0') ? `<a href="${linkMapa}" target="_blank" rel="noopener noreferrer" data-titlet="Ver Mapa"><i class="bi bi-pin-map-fill btn btn-sm btn-outline-info border-0 linkMapa"></i></a>` : `<i data-titler="Sin datos GPS" class="bi bi-x-lg btn btn-sm btn-outline-danger border-0 linkMapa"></i>`
                    let datacol = `<div class="w40">${iconMapa}</div>`
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
                    let datacol = `<div class="w70 text-truncate">${evento}<br>${row.operation}</div>`
                    return datacol;
                },
            },
            /** Columna Dispositivo */
            {
                className: '', targets: '', title: '<div class="w140" >Dispositivo</div>',
                "render": function (data, type, row, meta) {
                    // let btnAdd = `<button data-titlet="Agregar Dispositivo" class="btn btn-sm btn-outline-success border-0 ml-1 addDevice" data-phoneid='${row.phoneid}'><i class="bi bi-plus-circle"></i></button>`;
                    let btnAdd = `<span data-titlet="Agregar Dispositivo" class="text-secondary fontp btn p-0 m-0 btn-link addDevice">Agregar Dispositivo <i class="bi bi-plus ml-1 px-1 border-0 bg-ddd"></i></span>`;

                    let device = (!row.deviceName) ? `<div class="text-danger"><label class="m-0 p-0 w140 fontq">${row.phoneid}</label><br>${btnAdd}</div>` : `<div class="">${row.deviceName}</div><div class="text-secondary fontp">${row.phoneid}</div>`;

                    let datacol = `<div class="smtdcol text-truncate" title="Versión App: ${row.appVersion}">${device}</div>`
                    return datacol;
                },
            },
            /** Columna id_api */
            {
                className: '', targets: '', title: '',
                "render": function (data, type, row, meta) {
                    let datacol = `<div class="w40">${row.id_api}</div>`
                    if ((host == 'https://localhost')) {
                        return datacol;
                    } else if ((host == 'http://localhost')) {
                        return datacol;
                    } else {
                        return '';
                    }
                }

            },
            /** Columna Flag */
            {
                className: 'w-100 text-right', targets: '', title: '',
                "render": function (data, type, row, meta) {
                    let locked = '';
                    switch (row.locked) {
                        case '1':
                            locked = '<span data-titlel="' + row.error + '" class="font1 pointer bi bi-clipboard-x-fill text-danger"></span>';
                            break;
                        default:
                            locked = '<span class="font1 bi bi-clipboard-check-fill text-success"></span>';
                            break;
                    }
                    let datacol = `<div class="">${locked}</div>`
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
        // scrollY: '43vh',
        scrollY: '425px',
        scrollCollapse: true,
        scrollX: true,
        fixedHeader: false,
        language: {
            "url": "../../js/DataTableSpanishShort2.json?v=" + vjs()
        },
    });
}

function doesFileExist(urlToFile) {
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', urlToFile, false);
    xhr.send();
    return (xhr.status == "404") ? false : true;
}

let processRegFace = (id_api) => {
    $.ajax({
        type: 'POST',
        url: 'crud.php',
        data: 'tipo=proccesRegFace' + '&id_api=' + id_api,
        // dataType: "json",
        beforeSend: function (data) {
            CheckSesion()
            // $.notifyClose();
            // notify('Aguarde..', 'info', 0, 'right')
        },
        success: function (data) {
            if (data.status == "ok") {
                // $.notifyClose();
                // loadingTable('#table-mobile')
                notify(data.Mensaje.textAud, 'success', 3000, 'right')
                $('#table-mobile').DataTable().ajax.reload(null, false);
            } else {
                // $.notifyClose();
                // loadingTable('#table-mobile')
                $('#table-mobile').DataTable().ajax.reload(null, false);
                notify(data.Mensaje, 'danger', 3000, 'right')
            }
        },
        error: function () {
            $.notifyClose();
            $('#table-mobile').DataTable().ajax.reload(null, false);
        }
    });
}
// max-h-500 overflow-auto

tablemobile.on('init.dt', function (e, settings, json) {
    $('.dr').append(`
        <div class="mx-2">
            <input type="text" readonly class="pointer h40 form-control text-center w250 ls1 bg-white" name="_dr" id="_drMob">
        </div>
        <div class="btn-group dropright">
            <button type="button" class="btn btn-sm h40 btn-outline-secondary border-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <div class="dropdown-menu shadow border-0 p-0 radius">
                <ul class="list-group">
                    <button class="btn btn-outline-custom border-0 radius fontq" id="downloadTxt" ><div class="ml-1"><span>Exportar</span> .txt</div></button>
                    <button class="btn btn-outline-custom border-0 radius fontq" id="downloadXls" ><div class="ml-1">Exportar .xls</div></button>
                </ul>
            </div>
        </div>
    `);
    dateRange()
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
    $('#table-mobile_filter input').removeClass('form-control-sm')
    $('#table-mobile_filter input').attr("style", "height: 40px !important");
    select2Simple('#table-mobile_length select', '', false, false)
    $('.SoloFic').hide()
    // click event
});

$(document).on('click', '#downloadTxt', function (e) {
    e.preventDefault();
    ActiveBTN(true, this, 'Descargando ' + loading, 'Exportar .txt')
    $.notifyClose();
    notify('Exportando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
    axios({
        method: 'post',
        url: 'getRegMobile.php',
        data: filterData('', 'downloadTxt'),
    }).then(function (response) {
        let file = response.data
        // window.location = file.data
        $.notifyClose();
        if (!file.data) {
            notify('No hay datos a exportar', 'danger', 3000, 'right');
        } else {
            notify('<b>Archivo exportado correctamente</b>.<br>Tiempo: ' + file.timeScript + ' segundos.<br/><div class="shadow-sm w100"><a href="' + file.data + '" class="btn btn-custom px-3 btn-sm mt-2 fontq downloadTxt" target="_blank" download><div class="d-flex align-items-center"><span>Descargar</span><i class="bi bi-file-earmark-arrow-down ml-1 font1"></i></div></a></div>', 'warning', 0, 'right')
        }
        $(".downloadTxt").click(function () {
            $.notifyClose();
        });
    }).catch(function (error) {
        console.log('ERROR al descargar\n' + error);
    }).then(function () {
        ActiveBTN(false, '#downloadTxt', 'Descargando ' + loading, 'Exportar .txt')
    });
}).on('click', '#downloadXls', function (e) {
    e.preventDefault();
    ActiveBTN(true, this, 'Descargando ' + loading, 'Exportar .xls')
    $.notifyClose();
    notify('Exportando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
    axios({
        method: 'post',
        url: 'getRegMobile.php',
        data: filterData('', 'downloadXls'),
    }).then(function (response) {
        let file = response.data
        // window.location = file.data
        $.notifyClose();
        if (!file.data) {
            notify('No hay datos a exportar', 'danger', 3000, 'right');
        } else {
            notify('<b>Archivo exportado correctamente</b>.<br>Tiempo: ' + file.timeScript + ' segundos.<br/><div class="shadow-sm w100"><a href="' + file.data + '" class="btn btn-custom px-3 btn-sm mt-2 fontq downloadXls" target="_blank" download><div class="d-flex align-items-center"><span>Descargar</span><i class="bi bi-file-earmark-arrow-down ml-1 font1"></i></div></a></div>', 'warning', 0, 'right')
        }
        $(".downloadXls").click(function () {
            $.notifyClose();
        });
    }).catch(function (error) {
        console.log('ERROR al descargar\n' + error);
    }).then(function () {
        ActiveBTN(false, '#downloadXls', 'Descargando ' + loading, 'Exportar .xls')
    });
});
tablemobile.on('draw.dt', function (e, settings, json) {
    tablemobile.off('draw.dt');
    return true
});
tablemobile.on('page.dt', function () {
    loadingTable('#table-mobile')
});
// tablemobile.on('search.dt', function () {
//     loadingTable('#table-mobile')
// });
tablemobile.on('xhr.dt', function (e, settings, json) {
    let apiMobile = sessionStorage.getItem($('#_homehost').val() + '_api_mobile');
    fetchCreatedDate('createdDate.php')
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

    // var sunCircle = {
    //     strokeColor: "#0388D1",
    //     strokeOpacity: 1,
    //     strokeWeight: 1,
    //     fillColor: "#0388D1",
    //     fillOpacity: 0.25,
    //     map: map,
    //     center: myLatLng,
    //     radius: 200 // en metros
    // };
    // cityCircle = new google.maps.Circle(sunCircle)
    // cityCircle.bindTo('center', marker, 'position');
}

$(document).on("click", ".pic", function (e) {
    let data = tablemobile.row($(this).parents("tr")).data();
    $('#pic').modal('show')
    url_foto = `${data.imageData.img}`;
    let path = document.getElementById('apiMobile').value + '/chweb/mobile/hrp/'
    let picfoto = data.imageData.img ? path + url_foto : '';
    let picnombre = data.userName;
    let picDevice = data.deviceName
    let picIDUser = data.userID
    let pichora = data.regTime
    let picdia = data.regDay + ' ' + data.regDate + ' ' + data.regTime
    let _lat = data.regLat
    let _lng = data.regLng
    let locked = data.locked
    let id_api = data.id_api
    let error = data.error
    let confidenceFaceStr = data.confidenceFaceStr;
    let basePhoto = data.basePhoto;
    let zoneLat = data.zoneLat
    let zoneLng = data.zoneLng
    let zoneRadio = data.zoneRadio
    let zoneDistance = data.zoneDistance
    let zoneName = (data.zoneID > 0) ? '<span class="text-success">' + data.zoneName + '</span>' : '<span class="text-danger">Fuera de Zona</span>'
    let zoneName2 = (data.zoneID > 0) ? data.zoneName : 'Fuera de Zona'
    let Distance = (data.zoneID > 0) ? '. Distancia: ' + data.zoneDistance + ' metros' : ''

    picDevice = (!picDevice) ? `${data.phoneid}` : picDevice;

    // if (data.basePhoto) {
    //     picfoto = `<img src="data:image/jpg;base64,${data.basePhoto}" alt="${data.userName}" class="w40 h40 radius img-fluid" />`
    // }

    $('#latitud').val(_lat)
    $('#longitud').val(_lng)
    $('#modalFoto').val(picfoto)
    $('#modalNombre').val(picnombre)
    $("input[name=lat]").val(_lat);
    $("input[name=lng]").val(_lng);

    if (picfoto) {
        if (data.basePhoto) {
            $('.picFoto').html(`<img src="data:image/jpg;base64,${data.basePhoto}" alt="${data.userName}" class="w150 img-fluid rounded" />`)
        } else {
            $('.picFoto').html('<img loading="lazy" src= "' + picfoto + '" class="w150 img-fluid rounded"/>');
        }
        $('.divFoto').show()
    } else {
        $('.divFoto').hide()
    }

    if (data.attPhoto == 1) {
        $('.divFoto').hide()
    }

    if (locked == '1') {
        $('#divError').show()
        $('#divError').html(`
            <div class="col-12 text-danger mt-3 mb-0 fontq shadow-sm p-2" role="alert">
                <label class="w70 fontp text-secondary">Error: </label>
                <div class="font-weight-bold">${error}</div>
            </div>
        `)
    } else {
        $('#divError').hide();
        $('#divError').html('');
    }

    if (confidenceFaceStr == 'Identificado') {
        confidenceFaceStr = `<span class="text-success">${confidenceFaceStr}</span>`
    } else if (confidenceFaceStr == 'No identificado') {
        confidenceFaceStr = `<span class="text-danger">${confidenceFaceStr}</span>`
    } else if (confidenceFaceStr == 'No Enrolado') {
        confidenceFaceStr = `<span class="text-primary">${confidenceFaceStr}</span>`
    }

    $('.picFace').html(confidenceFaceStr);
    $('.picName').html(picnombre);
    $('.picDevice').html(picDevice);
    $('.picIDUser').html(picIDUser);
    $('.picHora').html('<b>' + pichora + '</b>');
    $('.picZona').html(zoneName);

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

        $('.modal-body #noGPS').html('')
        // initMap()
        let lati = parseFloat($('#latitud').val())
        let long = parseFloat($('#longitud').val())
        let zone = ($('#zona').val())
        zone = (zone) ? zone : 'Fuera de Zona';
        let user = ($('#modalNombre').val()) ? $('#modalNombre').val() : 'Inválido';
        // getMap(lati, long, 17, zone, 100, lati, long, '20 mts', user, pichora)
        $('#pic').on('shown.bs.modal', function (e) {
            setTimeout(() => {
                let map = L.map('mapzone').setView([lati, long], 16);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '<b>HRProcess Mobile</b>'
                }).addTo(map);
                let marker = L.marker([lati, long]).addTo(map);
                let distancia = ''
                if ((data.zoneID > 0)) {
                    let circle = L.circle([zoneLat, zoneLng], {
                        color: '#333',
                        fillColor: '#efefef',
                        fillOpacity: 0.6,
                        radius: zoneRadio
                    }).addTo(map);
                    // circle.bindPopup("<b>Urbana</b>");
                    distancia = '<br>' + zoneDistance + ' mts.'
                }
                marker.bindTooltip("<b>" + user + "</b><br>" + picdia + "<br>" + zoneName + distancia)
            }, 200);
        })
    } else {
        $('#mapzone').hide();
        $('.modal-body #noGPS').html('<div class="text-center mt-2 m-0 p-0 fontq"><span>Ubicación GPS no disponible</span></div>')
    }
});
$(document).on("click", ".processRegFace", function (e) {
    // ActiveBTN(true, ".processRegFace", loading, '')
    $(this).prop('disabled', true);
    $(this).html(loading);
    let data = tablemobile.row($(this).parents("tr")).data();
    processRegFace(data.id_api)
});
$('#pic').on('hidden.bs.modal', function (e) {
    clean()
    initializingMap()
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
    $('#mapzone').html('');
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
$(document).on("click", ".actualizar", function (e) {
    $(this).attr("data-titlel", "Descargando...")
    actualizar()
    actualizar2()
    actualizar3()
});

$(document).on("click", "#Encabezado", function (e) {
    CheckSesion()
    loadingTable('#table-mobile');
    loadingTableUser('#tableUsuarios');
    loadingTableDevices('#tableDevices');
    loadingTableZones('#tableZones');
    minmaxDate()
    // actualizar(false);
});

let enableBtnMenu = (e) => {
    $('#btnMenu .btn').prop('readonly', false)
    $('#btnMenu #positionMap').prop('disabled', false)
    hideMapMarcadores();
}

let focusBtn = (selector) => {
    $('#btnMenu .btn').removeClass('btn-custom');
    $('#btnMenu .btn').parents('li').removeClass('shadow');
    $('#btnMenu .btn').addClass('btn-outline-custom');
    $(selector).removeClass('btn-outline-custom').addClass('btn-custom');
    $(selector).parents('li').addClass('shadow');
}

let focusRowTables = () => {
    $('#RowTableMobile').hide();
    $('#RowTableUsers').hide();
    $('#RowTableDevices').hide();
    $('#RowTableZones').hide();
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
    $('#RowTableUsers').addClass('invisible')
    $('#tableUsuarios').DataTable().ajax.reload(null, false)
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
    $('#RowTableDevices').addClass('invisible')
    $('#tableDevices').DataTable().ajax.reload(null, false)
    $('#RowTableDevices').show();
});
$(document).on("click", ".showZones", function (e) {
    CheckSesion()
    enableBtnMenu()
    $(this).prop('readonly', true)
    focusBtn(this);
    document.title = "Zonas Mobile"
    $('#Encabezado').html("Zonas Mobile");
    focusRowTables()
    $('#RowTableZones').show();
    $('#RowTableZones').addClass('invisible')
    $('#tableZones').DataTable().ajax.reload(null, false)
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
    $('#RowTableMobile').show();
    loadingTable('#table-mobile');
    $('#table-mobile').DataTable().columns.adjust().draw();
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
let dateRange = () => {
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
        loadingTable('#table-mobile');
        $('#table-mobile').DataTable().ajax.reload();
    });
}
let minmaxDate = () => {
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
        tablemobile.ajax.reload(null, false);
        $('#tableUsuarios').DataTable().ajax.reload(null, false);
        $('#tableDevices').DataTable().ajax.reload(null, false);
        $('#tableZones').DataTable().ajax.reload(null, false);
        dateRange()
    }).catch(function (error) {
        alert('ERROR minmaxDate\n' + error);
    }).then(function () {

    });
}
let setStorageDate = (date) => {
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
function getMap(lat, lng, zoom, zona, radio, latzona, lngzona, mtszona, user, datetime) {
    let map = L.map('mapzone').setView([lat, lng], zoom);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '<b>HRProcess Mobile</b>'
    }).addTo(map);

    let marker = L.marker([lat, lng]).addTo(map);
    let circle = L.circle([latzona, latzona], {
        color: '#333',
        fillColor: '#efefef',
        fillOpacity: 0.6,
        radius: radio
    }).addTo(map);
    // circle.bindPopup("<b>Urbana</b>");
    marker.bindTooltip("<img src='http://chweb.ar/chweb/mobile/hrp/fotos/1/2022/11/16/1668635417120_267615861929486.jpg' style='width:60px; height:60px'><br><b>" + user + "</b><br>" + datetime + "<br>En zona: " + zona + " a " + mtszona).openTooltip()
}
function initializingMap() // call this method before you initialize your map.
{
    var container = L.DomUtil.get('mapzone');
    if (container != null) {
        container._leaflet_id = null;
    }
}