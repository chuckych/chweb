$(function () {
    'use strict';

    const homehost = $("#_homehost").val();
    const LS_MODAL = homehost + '_mobile_modal';

    if (!ls.get(LS_MODAL)) {

        axios.get('modal.php').then((response) => {
            ls.set(LS_MODAL, response.data);
        }).catch(() => {
            ls.remove(LS_MODAL);
        });

    }


    $('#Encabezado').addClass('pointer')
    $('#RowTableUsers').hide();
    $('#RowTableDevices').hide();
    $('#RowTableZones').hide();
    sessionStorage.setItem('tab_32', 'visible');

    document.addEventListener("visibilitychange", function () {
        let state = document.visibilityState;
        sessionStorage.setItem('tab_32', state);
    });
    $(window).on('load', function () {
        $('.loading').hide()
    });

    // if ((host != 'https://localhost') && (host != 'http://localhost')) {
    setInterval(() => {
        if (sessionStorage.getItem('tab_32') == 'visible') { // Si la pestaña del navegador esta activa consultamos si hay datos nuevos
            let apiMobile = sessionStorage.getItem($('#_homehost').val() + '_api_mobile');
            fetchCreatedDate('api/createdDate.php')
        }
    }, 5000); // cada 5 segundos
    // }

    $.fn.DataTable.ext.pager.numbers_length = 5;
    // $('#btnFiltrar').removeClass('d-sm-block');
    let drmob2 = $('#max').val() + ' al ' + $('#max').val()
    $('#_drMob2').val(drmob2)
    if ($(window).width() < 540) {
        $('#table-mobile').DataTable({
            dom: "<'row lengthFilterTable'" +
                "<'col-12 col-sm-6 d-flex align-items-start dr'l><'col-12 col-sm-6 d-inline-flex align-items-start justify-content-end'<'SoloFic mt-2'><'Filter'>f>>" +
                "<'row '<'col-12 table-responsive't>>" +
                "<'fixed-bottom'<'bg-white'<'d-flex p-0 justify-content-center'p><'pb-2'i>>>",
            ajax: {
                url: "getRegMobile.php",
                type: "POST",
                "data": function (data) {
                    data._drMob = $("#_drMob").val();
                    data._drMob2 = $("#_drMob2").val();
                    data.SoloFic = $("#SoloFic").val();
                    data.users = $('.FilterUser').val()
                    data.zones = $('.FilterZones').val()
                    data.device = $('.FilterDevice').val()
                    data.identified = $('input[name=FilterIdentified]:checked').val();
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

                        let color = '';
                        if (row.confidenceFaceStr == 'Identificado') {
                            color = 'success'
                        } else if (row.confidenceFaceStr == 'No Identificado') {
                            color = 'danger'
                        } else if (row.confidenceFaceStr == 'No Enrolado') {
                            color = 'warning'
                        } else if (row.confidenceFaceStr == 'Foto Inválida') {
                            color = 'info'
                        } else if (row.confidenceFaceStr == 'No Disponible') {
                            color = 'primary'
                        } else if (row.confidenceFaceStr == 'Entrenamiento Inválido') {
                            color = 'warning'
                        }

                        let operation = (row.operation == 0) ? '' : ': ' + row.operation;
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
                            let path = '';
                            let url_foto = `${row.imageData.img}`;
                            let apiMobile = document.getElementById('apiMobile').value;
                            if (apiMobile == 'http://localhost:8050') {
                                path = ''
                            } else {
                                path = document.getElementById('apiMobile').value + '/chweb/mobile/hrp/'
                            }
                            foto = `<img loading="lazy" src="${path}${url_foto}" class="w60 h60 img-fluid"></img>`;
                        } else {
                            url_foto = ``;
                            // foto = `<i class="bi bi-card-image font1 text-secondary"></i>`;
                            foto = ``;
                            // foto = `<img loading="lazy" src="${row.imageData.img}" class="w40 h40 radius img-fluid"></img>`;
                        }

                        if (row.attPhoto == 1) {
                            // foto = `<i class="bi bi-card-image font1 text-secondary"></i>`;
                            foto = ``;

                        }
                        if (row.basePhoto) {
                            foto = `<img src="data:image/jpeg;base64,${row.basePhoto}" alt="${row.userName}" class="w40 h40 img-fluid" />`
                        }

                        let datacol = `<div class="pic w70 h70 border border-${color} d-flex justify-content-center align-items-center pointer">${foto}</div>`
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
                        <span title="Crear Zona" class="text-secondary font07 btn p-0 m-0 btn-link createZoneOut mt-1"><i class="bi bi-plus px-2 p-1 border"></i></span>
                        <span title="Procesar Zona" class="text-secondary font07 btn p-0 m-0 btn-link proccessZone mt-1"><i class="bi bi-arrow-left-right ml-1 px-2 p-1 border"></i></span>
                    </span>`;
                        if (row.regLat == 0) {
                            btnAdd = `<span class="text-danger p-0 m-0">Sin datos GPS</span>`;
                        }
                        let device = (row.zoneID == 0) ? `<div class="text-danger"><label class="m-0 p-0 font08">${zoneName}</label>${btnAdd}</div>` : `<div class="text-truncate" style="max-width:170px"><span class="">${zoneName}</span><span class="text-secondary font07 ml-2">${Distance2}</span></div>`;


                        let nameuser = (row['userName']) ? row['userName'] : '<span class="text-danger font-weight-bold">Usuario inválido</span>';
                        let datacol = `
                        <div class="smtdcol">
                            <div class="searchName pointer text-truncate" style="max-width:170px">${nameuser}</div>
                            <div class="searchID pointer text-secondary d-none">${row.userID}</div>
                            <span class="">${row.regDay} ${row.regDate} <span class="font-weight-bold ls1">${row.regTime}</span></span>
                            <span title="${zoneName2}">${device}</span>
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
        $('#table-mobile').DataTable({
            // iDisplayLength: 5,
            dom: "<'row lengthFilterTable'" +
                "<'col-12 col-sm-6 d-flex align-items-start dr'l><'col-12 col-sm-6 d-inline-flex align-items-start justify-content-end'<'SoloFic mt-2'><'Filter'>f>>" +
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
                    data.users = $('.FilterUser').val();
                    data.zones = $('.FilterZones').val();
                    data.device = $('.FilterDevice').val()
                    data.identified = $('input[name=FilterIdentified]:checked').val();
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
                        let color = '';
                        if (row.confidenceFaceStr == 'Identificado') {
                            color = 'success'
                        } else if (row.confidenceFaceStr == 'No Identificado') {
                            color = 'danger'
                        } else if (row.confidenceFaceStr == 'No Enrolado') {
                            color = 'warning'
                        } else if (row.confidenceFaceStr == 'Foto Inválida') {
                            color = 'info'
                        } else if (row.confidenceFaceStr == 'No Disponible') {
                            color = 'primary'
                        } else if (row.confidenceFaceStr == 'Entrenamiento Inválido') {
                            color = 'warning'
                        }

                        let operation = (row.operation == 0) ? '' : ': ' + row.operation;
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
                            let path = '';
                            let url_foto = `${row.imageData.img}`;
                            let apiMobile = document.getElementById('apiMobile').value;
                            if (apiMobile == 'http://localhost:8050') {
                                path = ''
                            } else {
                                path = document.getElementById('apiMobile').value + '/chweb/mobile/hrp/'
                            }
                            foto = `<img loading="lazy" src="${path}${url_foto}" class="w45 h45 img-fluid">`;
                        } else {
                            url_foto = ``;
                            // foto = `<i class="bi bi-card-image font1 text-secondary"></i>`;
                            foto = ``;

                        }
                        if (row.attPhoto == 1) {
                            // foto = `<i class="bi bi-card-image font1 text-secondary"></i>`;
                            foto = ``;
                        }
                        if (row.basePhoto) {
                            foto = `<img src="data:image/jpeg;base64,${row.basePhoto}" alt="${row.userName}" class="w45 h45 img-fluid" />`
                        }
                        let datacol = `<div class="pic scale w50 h50 border border-${color} d-flex justify-content-center align-items-center pointer">${foto}</div>`
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
                            <div class="searchID pointer text-secondary font07">${row.userID}</div>
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
                            <span class="text-secondary font07">${row.regDay}</span>
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
                        let hintClass = (color) => `hint--right hint--rounded hint--no-arrow hint--${color} hint--no-shadow`;
                        console.log(hintClass);

                        if (row.confidenceFaceStr == 'Identificado') {
                            confidenceFaceStr = `<span class="${hintClass('success')}" aria-label="${row.confidenceFaceStr}" ><span class="font1 text-success bi bi-person-bounding-box"></span></span>`
                        } else if (row.confidenceFaceStr == 'No Identificado') {
                            confidenceFaceStr = `<span class="${hintClass('error')}" aria-label="${row.confidenceFaceStr}" ><span class="font1 text-danger bi bi-person-bounding-box"></span></span>`
                        } else if (row.confidenceFaceStr == 'No Enrolado') {

                            confidenceFaceStr = `<span class="${hintClass('warning')}" aria-label="${row.confidenceFaceStr}" ><span class="font1 text-warning bi bi-person-bounding-box"></span></span>`

                        } else if (row.confidenceFaceStr == 'Foto Inválida') {
                            confidenceFaceStr = `<span class="${hintClass('info')}" aria-label="${row.confidenceFaceStr}" ><span class="font1 text-info bi bi-person-bounding-box"></span></span>`
                        } else if (row.confidenceFaceStr == 'No Disponible') {

                            confidenceFaceStr = `<span class="${hintClass('primary')}" aria-label="${row.confidenceFaceStr}" ><span class="font1 text-primary bi bi-person-bounding-box"></span></span>`

                            datacol = `<div class="w40">${confidenceFaceStr}</div>`
                            return datacol;
                        } else if (row.confidenceFaceStr == 'Entrenamiento Inválido') {
                            confidenceFaceStr = `<span class="${hintClass('warning')}" aria-label="${row.confidenceFaceStr}" ><span class="font1 text-warning bi bi-person-bounding-box"></span></span>`
                            datacol = `<div class="w40">${confidenceFaceStr}</div>`
                            return datacol;
                        }
                        datacol = `<div class="w40 ${processRegFace}">${confidenceFaceStr}</div>`
                        return datacol;
                    },
                },
                /** Columna Zona */
                {
                    className: '', targets: '', title: '<div class="w120">Zona</div>',
                    "render": function (data, type, row, meta) {
                        let btnAdd = ''
                        let zoneName = (row.zoneID > 0) ? '<div class="text-success">' + row.zoneName + '</div>' : '<div class="text-danger">Fuera de Zona</div>'
                        let zoneName2 = (row.zoneID > 0) ? row.zoneName : 'Fuera de Zona'
                        let Distance = (row.zoneID > 0) ? '. Distancia: ' + row.zoneDistance + ' mts' : ''
                        let Distance2 = (row.zoneID > 0) ? '' + row.zoneDistance + ' mts' : ''

                        btnAdd = `<div style="padding-bottom: 3px;">
                                    <span title="Crear Zona" class="text-secondary font07 btn p-0 m-0 btn-link createZoneOut mt-1"><i class="bi bi-plus px-2 p-1 border"></i></span>
                                    <span title="Procesar Zona" class="text-secondary font07 btn p-0 m-0 btn-link proccessZone mt-1"><i class="bi bi-arrow-left-right ml-1 px-2 p-1 border"></i></span>
                                </div>`;
                        if (row.regLat == 0) {
                            btnAdd = `<div class="text-secondary font07 p-0 m-0">Sin datos GPS</div>`;
                        }
                        let device = (row.zoneID == 0) ? `<div class="text-danger"><label class="m-0 p-0 font08">${zoneName}</label>${btnAdd}</div>` : `<div class="">${zoneName}</div><div class="text-secondary font07">${Distance2}</div>`;

                        let datacol = `<div title="${zoneName2}" class="w120 text-truncate">${device}</div>`
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
                        if (evento == 'Fichada') {
                            return `<div class="w70 text-truncate">${evento}</div>`
                        }
                        return `<div class="w70 text-truncate">${evento}<br><span class="text-secondary font07">Cod: ${row.eventType}</span></div>`
                    },
                },
                /** Columna Dispositivo */
                {
                    className: '', targets: '', title: '<div class="w140" >Dispositivo</div>',
                    "render": function (data, type, row, meta) {

                        let btnAdd = `<span data-titlet="Agregar Dispositivo" class="text-secondary font07 btn p-0 m-0 btn-link addDevice">Agregar Dispositivo <i class="bi bi-plus ml-1 px-1 border-0 bg-ddd"></i></span>`;

                        let colorDevice = (row.deviceName == row.phoneID) ? 'text-danger' : '';
                        let iconEditDevice = (row.deviceName == row.phoneID) ? '<i class="bi bi-pencil-fill font07 ml-2 text-primary"></i>' : '';

                        let device = (!row.deviceName) ? `<div class="text-danger"><label class="m-0 p-0 w140 font08">${row.phoneID}</label><br>${btnAdd}</div>` : `<div class="d-flex align-items-center updDeviceTable pointer ${colorDevice} hint--rounded hint--no-arrow hint--secondary hint--no-shadow"
                        aria-label="Editar Dispositivo" >${row.deviceName} ${iconEditDevice}</div><div class="text-secondary font07">${row.phoneID}</div>`;

                        let datacol = `<div class="smtdcol text-truncate" style="max-width:180px">${device}</div>`
                        return datacol;
                    },
                },
                /** Columna version APP */
                {
                    className: '', targets: '', title: '',
                    "render": function (data, type, row, meta) {
                        let datacol = `<div class="font07 text-secondary">${row.appVersion}</div>`
                        return datacol;
                    },
                },
                /** Columna id_api */
                // {
                //     className: '', targets: '', title: '',
                //     "render": function (data, type, row, meta) {
                //         let datacol = `<div class="w40">${row.id_api}</div>`
                //         if ((host == 'https://localhost')) {
                //             return datacol;
                //         } else if ((host == 'http://localhost')) {
                //             return datacol;
                //         } else {
                //             return '';
                //         }
                //     }

                // },
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
    // max-h-500 overflow-auto

    $('#table-mobile').DataTable().on('init.dt', function (e, settings, json) {
        $('.dr').append(`
        <div class="mx-2 hint--rounded hint--no-arrow hint--secondary hint--no-shadow hint--top"
        aria-label="Seleccionar periodo">
            <input type="text" readonly class="pointer h40 form-control text-center w250 ls1 bg-white" name="_dr" id="_drMob">
        </div>
        <div class="btn-group dropright d-none d-sm-block hint--rounded hint--no-arrow hint--secondary hint--no-shadow hint--top"
        aria-label="Exportar txt, xls">
            <button type="button" class="btn btn-sm h40 btn-outline-secondary border-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <div class="dropdown-menu shadow border-0 p-0 radius">
                <ul class="list-group">
                    <button class="btn btn-outline-custom border-0 radius font08" id="downloadTxt" ><div class="ml-1"><span>Exportar</span> .txt</div></button>
                    <button class="btn btn-outline-custom border-0 radius font08" id="downloadXls" ><div class="ml-1">Exportar .xls</div></button>
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
        $('.Filter').html(`<button class="btn btn-light bg-white border radius h40 hint--rounded hint--no-arrow hint--secondary hint--no-shadow hint--left"
        aria-label="Filtros avanzados" type="button" data-toggle="collapse" data-target="#collapseFilterChecks" aria-expanded="false" aria-controls="collapseFilterChecks">
        <i class="bi bi-funnel-fill text-secondary"></i>
        </button>`)
        $('#table-mobile_filter input').removeClass('form-control-sm')
        $('#table-mobile_filter input').attr("style", "height: 40px !important");
        select2Simple('#table-mobile_length select', '', false, false)
        $('.SoloFic').hide()
        // click event

        $('input[name=FilterIdentified]').on('change', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            actualizarRegistros('#table-mobile')
        });

        $('#ClearFilter').on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            ClearFilterMobile()
            $('#table-mobile').DataTable().search('').draw();
            // actualizarRegistros('#table-mobile', true)
        });
        let counterShown = 0;
        $('#collapseFilterChecks').on('shown.bs.collapse', function () {
            counterShown++;

            if (counterShown > 1) {
                return;
            }

            $('.FilterUser').select2({
                multiple: true,
                language: "es",
                allowClear: false,
                templateResult: templateData,
                placeholder: 'Usuarios',
                minimumInputLength: 0,
                minimumResultsForSearch: 10,
                maximumInputLength: 10,
                selectOnClose: false,
                language: {
                    noResults: function () {
                        return 'No hay resultados..'
                    },
                    inputTooLong: function (args) {
                        var message = 'Máximo ' + 10 + ' caracteres. Elimine ' + overChars + ' caracter';
                        if (overChars != 1) {
                            message += 'es'
                        }
                        return message
                    },
                    searching: function () {
                        return 'Buscando..'
                    },
                    errorLoading: function () {
                        return 'Sin datos..'
                    },
                    inputTooShort: function () {
                        return 'Ingresar ' + 0 + ' o mas caracteres'
                    },
                    maximumSelected: function () {
                        return 'Puede seleccionar solo una opción'
                    },
                    removeAllItems: function () {
                        return "Eliminar Selección"
                    }
                },
                ajax: {
                    url: "getRegMobile.php",
                    dataType: "json",
                    type: 'POST',
                    delay: 250,
                    data: function (params) {
                        return {
                            qUser: params.term,
                            type: 'selectUsers',
                            _drMob2: $("#_drMob2").val(),
                            start: 0,
                            length: 50,
                            users: $('.FilterUser').val(),
                            zones: $('.FilterZones').val(),
                            device: $('.FilterDevice').val(),
                            identified: $('input[name=FilterIdentified]:checked').val(),
                        }
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        }
                    },
                }
            })
            // .on("select2:unselecting", function (e) {
            //     $(this).data('state', 'unselected');
            // }).on("select2:open", function (e) {
            //     if ($(this).data('state') === 'unselected') {
            //         $(this).removeData('state');
            //         let self = $(this);
            //         // setTimeout(function () {
            //             self.select2('close');
            //         // }, 1);
            //     }
            // });

            $('.FilterZones').select2({
                multiple: true,
                dropdownAutoHeight: true,
                language: "es",
                allowClear: false,
                templateResult: templateData,
                placeholder: 'Zonas',
                minimumInputLength: 0,
                minimumResultsForSearch: 10,
                maximumInputLength: 10,
                selectOnClose: false,
                language: {
                    noResults: function () {
                        return 'No hay resultados..'
                    },
                    inputTooLong: function (args) {
                        var message = 'Máximo ' + 10 + ' caracteres. Elimine ' + overChars + ' caracter';
                        if (overChars != 1) {
                            message += 'es'
                        }
                        return message
                    },
                    searching: function () {
                        return 'Buscando..'
                    },
                    errorLoading: function () {
                        return 'Sin datos..'
                    },
                    inputTooShort: function () {
                        return 'Ingresar ' + 0 + ' o mas caracteres'
                    },
                    maximumSelected: function () {
                        return 'Puede seleccionar solo una opción'
                    },
                    removeAllItems: function () {
                        return "Eliminar Selección"
                    }
                },
                ajax: {
                    url: "getRegMobile.php",
                    dataType: "json",
                    type: 'POST',
                    delay: 250,
                    data: function (params) {
                        return {
                            qZone: params.term,
                            type: 'selectZone',
                            _drMob2: $("#_drMob2").val(),
                            start: 0,
                            length: 50,
                            users: $('.FilterUser').val(),
                            zones: $('.FilterZones').val(),
                            device: $('.FilterDevice').val(),
                            identified: $('input[name=FilterIdentified]:checked').val(),
                        }
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        }
                    },
                }
            })

            $('.FilterDevice').select2({
                multiple: true,
                language: "es",
                allowClear: false,
                templateResult: templateData,
                placeholder: 'Dispositivos',
                minimumInputLength: 0,
                minimumResultsForSearch: 10,
                maximumInputLength: 10,
                selectOnClose: false,
                language: {
                    noResults: function () {
                        return 'No hay resultados..'
                    },
                    inputTooLong: function (args) {
                        var message = 'Máximo ' + 10 + ' caracteres. Elimine ' + overChars + ' caracter';
                        if (overChars != 1) {
                            message += 'es'
                        }
                        return message
                    },
                    searching: function () {
                        return 'Buscando..'
                    },
                    errorLoading: function () {
                        return 'Sin datos..'
                    },
                    inputTooShort: function () {
                        return 'Ingresar ' + 0 + ' o mas caracteres'
                    },
                    maximumSelected: function () {
                        return 'Puede seleccionar solo una opción'
                    },
                    removeAllItems: function () {
                        return "Eliminar Selección"
                    }
                },
                ajax: {
                    url: "getRegMobile.php",
                    dataType: "json",
                    type: 'POST',
                    delay: 250,
                    data: function (params) {
                        return {
                            qDevice: params.term,
                            type: 'selectDevice',
                            _drMob2: $("#_drMob2").val(),
                            start: 0,
                            length: 50,
                            users: $('.FilterUser').val(),
                            zones: $('.FilterZones').val(),
                            device: $('.FilterDevice').val(),
                            identified: $('input[name=FilterIdentified]:checked').val(),
                        }
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        }
                    },
                }
            })

            refreshSelected('.FilterZones')
            refreshUnselected('.FilterZones')
            refreshSelected('.FilterUser')
            refreshUnselected('.FilterUser')
            refreshSelected('.FilterDevice')
            refreshUnselected('.FilterDevice')

            setTimeout(() => {
                $('.FilterUser').removeClass('invisible')
                $('.FilterDevice').removeClass('invisible')
                $('.FilterZones').removeClass('invisible')
            }, 100);
        })
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
                notify('<b>Archivo exportado correctamente</b>.<br>Tiempo: ' + file.timeScript + ' segundos.<br/><div class="shadow-sm w100"><a href="' + file.data + '" class="btn btn-custom px-3 btn-sm mt-2 font08 downloadTxt" target="_blank" download><div class="d-flex align-items-center"><span>Descargar</span><i class="bi bi-file-earmark-arrow-down ml-1 font1"></i></div></a></div>', 'warning', 0, 'right')
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
                notify('<b>Archivo exportado correctamente</b>.<br>Tiempo: ' + file.timeScript + ' segundos.<br/><div class="shadow-sm w100"><a href="' + file.data + '" class="btn btn-custom px-3 btn-sm mt-2 font08 downloadXls" target="_blank" download><div class="d-flex align-items-center"><span>Descargar</span><i class="bi bi-file-earmark-arrow-down ml-1 font1"></i></div></a></div>', 'warning', 0, 'right')
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
    $('#table-mobile').DataTable().on('draw.dt', function (e, settings, json) {
        loadMap(settings.json.data, 'map_id_' + settings.json.draw);
        return true
    });
    $('#table-mobile').DataTable().on('page.dt', function (e, settings, json) {
        loadingTable('#table-mobile')
    });
    // $('#table-mobile').DataTable().on('search.dt', function () {
    //     loadingTable('#table-mobile')
    // });
    $('#table-mobile').DataTable().on('xhr.dt', function (e, settings, json) {
        let apiMobile = sessionStorage.getItem($('#_homehost').val() + '_api_mobile');
        fetchCreatedDate('api/createdDate.php')
        $('#table-mobile').DataTable().off('xhr.dt');
    });

    $(document).on('click', '.searchID', function (e) {
        e.preventDefault();
        $('#table-mobile').DataTable().search($(this).text()).draw();
        classEfect('#table-mobile_filter input', 'border-custom')
    });
    $(document).on('click', '.searchName', function (e) {
        e.preventDefault();
        $('#table-mobile').DataTable().search($(this).text()).draw();
        classEfect('#table-mobile_filter input', 'border-custom')
    });
    // $(document).on('click', '.searchZone', function (e) {
    //     e.preventDefault();
    //     let data = $('#table-mobile').DataTable().row($(this).parents("tr")).data();
    //     let zoneID = data.zoneID
    //     let zoneName = data.zoneName
    //     $('.FilterZones').val(null).trigger('change')
    //     select2Val(zoneID, zoneName, ".FilterZones")
    //     $("#collapseFilterChecks").collapse('show')

    //     setTimeout(() => {
    //         actualizarRegistros('#table-mobile')
    //     }, 500);

    // });
    $(document).on('change', '#SoloFic', function (e) {
        e.preventDefault()
        loadingTable('#table-mobile')
        if ($(this).is(':not(:checked)')) {
            if ($(this).val() != '') {
                $(this).val('0')
                actualizarRegistros('#table-mobile')
            }
        } else {
            if ($(this).val() != '') {
                $(this).val('1')
                actualizarRegistros('#table-mobile')
            }
        }
    });
    $(document).on("click", ".pic", async function (e) {
        let data = $('#table-mobile').DataTable().row($(this).parents("tr")).data(); // obtener datos de la fila seleccionada

        if (!data) {
            return; // si no hay datos, salir
        }

        let modalRegistro = document.getElementById('modalRegistro'); // div donde se carga el modal

        let getModal = ls.get(LS_MODAL); // obtener modal de localStorage

        if (!getModal) {
            return; // si no hay modal, salir
        }

        modalRegistro.innerHTML = getModal; // cargar modal en div modalRegistro

        $('#pic').modal('show') // mostrar modal

        // $(document).on("show.bs.modal", "#pic", function (e) {

        let url_foto = `${data.imageData.img}`;
        // let path = document.getElementById('apiMobile').value + '/chweb/mobile/hrp/'
        let path = '';
        let apiMobile = document.getElementById('apiMobile').value;
        if (apiMobile == 'http://localhost:8050') {
            path = ''
        } else {
            path = document.getElementById('apiMobile').value + '/chweb/mobile/hrp/'
        }

        let picFoto = data.imageData.img ? path + url_foto : '';
        let picNombre = data.userName;
        let picDevice = data.deviceName
        let picIDUser = data.userID
        let picHora = data.regTime
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
        let createdDate = data.createdDate
        let zoneName = (data.zoneID > 0) ? '<span class="text-success">' + data.zoneName + '</span>' : '<span class="text-danger">Fuera de Zona</span>'
        let mts = (data.zoneID > 0) ? '<span class="text-success font-weight-bold"><small> (' + zoneDistance + ' mts)<small></span>' : ''
        let zoneName2 = (data.zoneID > 0) ? data.zoneName : 'Fuera de Zona'
        let Distance = (data.zoneID > 0) ? '. Distancia: ' + data.zoneDistance + ' metros' : ''

        picDevice = (!picDevice) ? `${data.phoneID}` : picDevice;

        // if (data.basePhoto) {
        //     picFoto = `<img src="data:image/jpg;base64,${data.basePhoto}" alt="${data.userName}" class="w40 h40 radius img-fluid" />`
        // }

        $('#latitud').val(_lat)
        $('#longitud').val(_lng)
        $('#modalFoto').val(picFoto)
        $('#modalNombre').val(picNombre)
        $("input[name=lat]").val(_lat);
        $("input[name=lng]").val(_lng);

        $('#pic label').removeClass('bg-loading')

        if (picFoto) {
            if (data.basePhoto) {
                $('.picFoto').html(`<img src="data:image/jpg;base64,${data.basePhoto}" alt="${data.userName}" class="img-fluid rounded" style="width:150px;"; aspect-ratio: 131/174; objet-fit:cover" />`)
            } else {
                $('.picFoto').html('<img loading="lazy" src= "' + picFoto + '" class="w150 rounded img-fluid shadow" style="width:150px;"/>');
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
            <div class="col-12 text-danger mt-3 mb-0 font08 shadow-sm p-2" role="alert">
                <label class="w70 font07 text-secondary">Error: </label>
                <div class="font-weight-bold">${error}</div>
            </div>
        `)
        } else {
            $('#divError').hide();
            $('#divError').html('');
        }

        if (confidenceFaceStr == 'Identificado') {
            confidenceFaceStr = `<span class="text-success">${confidenceFaceStr}</span>`
        } else if (confidenceFaceStr == 'No Identificado') {
            confidenceFaceStr = `<span class="text-danger">${confidenceFaceStr}</span>`
        } else if (confidenceFaceStr == 'No Enrolado') {
            confidenceFaceStr = `<span class="text-primary">${confidenceFaceStr}</span>`
        } else if (confidenceFaceStr == 'Entrenamiento Inválido') {
            confidenceFaceStr = `<span class="text-info">No enrolado</span>`
        }

        $('.picFace').html(confidenceFaceStr);
        $('.picName').html(picNombre);
        $('.picDevice').html(picDevice);
        $('.picIDUser').html(picIDUser);
        $('.picHora').html('<b>' + picHora + '</b>');
        $('.picZona').html(zoneName + mts);

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
            // $('#mapzone').show()
            $('.modal-body #noGPS').html('')
            // initMap()
            let lati = parseFloat($('#latitud').val())
            let long = parseFloat($('#longitud').val())
            let zone = ($('#zona').val())
            zone = (zone) ? zone : 'Fuera de Zona';
            let user = ($('#modalNombre').val()) ? $('#modalNombre').val() : 'Inválido';
            getMap(lati, long, 15, zoneName, zoneRadio, zoneLat, zoneLng, zoneDistance, user, picdia, data.zoneID)
            // $('#mapzone').removeClass('invisible');
            // $('#mapzone').addClass('visible');
        } else {
            $('#mapzone').html('');
            $('.modal-body #noGPS').html('<div class="text-center mt-2 m-0 mt-2 font08 alert alert-info mt-2"><span>Ubicación GPS no disponible</span></div>')
        }
        // });
    });
    $(document).on("hidden.bs.modal", "#pic", function (e) {
        modalRegistro.innerHTML = '';
        // clean()
    })
    $(document).on("click", ".processRegFace", function (e) {
        // ActiveBTN(true, ".processRegFace", loading, '')
        $(this).prop('disabled', true);
        $(this).html(loading);
        let data = $('#table-mobile').DataTable().row($(this).parents("tr")).data();
        processRegFace(data.id_api)
    });
    // $('#pic').on('hidden.bs.modal', function (e) {
    //     clean()
    // })
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
            // $('#table-mobile').DataTable().columns.adjust().draw();
            actualizarRegistros('#table-mobile', true)
            // launchFullScreen(document.documentElement)
        }
    });
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
    $(document).on("click", ".showUsers", function (e) {
        CheckSesion()
        enableBtnMenu()
        $(this).prop('readonly', true)
        focusBtn(this);
        document.title = "Usuarios Mobile"
        $('#Encabezado').html("Usuarios Mobile");
        focusRowTables()
        $('#RowTableUsers').addClass('invisible')
        actualizarRegistros('#tableUsuarios', true)
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
        actualizarRegistros('#tableDevices', true)
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
        actualizarRegistros('#tableZones', true)
    });
    $(document).on("click", ".showChecks", function (e) {
        CheckSesion()
        enableBtnMenu()
        $(this).addClass('btn-custom');
        $(this).prop('readonly', true)
        focusBtn(this);
        document.title = "Mobile HRP"
        $('#Encabezado').html("Mobile HRP")
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
                ActiveBTN(true, "#" + dataRecid, '<i class="spinner-border font07 wh15"></i>', '<i class="bi bi-forward fontt"></i>')
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
});