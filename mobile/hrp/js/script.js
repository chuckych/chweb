$(function () {
    'use strict';

    const homehost = $("#_homehost").val();
    const LS_MODALES = homehost + '_mobile_modales';
    const LS_DIR_NAME = homehost + '_position_dir_name_';

    ls.remove(LS_MODALES);

    if (!ls.get(LS_MODALES)) {
        axios.get('modales.php').then((response) => {
            ls.set(LS_MODALES, response.data);
        }).catch(() => {
            ls.remove(LS_MODALES);
        });
    }

    const hintClass = (color) => `hint--right hint--rounded hint--no-arrow hint--${color} hint--no-shadow`;
    const MAP_EVENTO = {
        '-1': 'Fichada',
        '1': 'Ronda',
        '3': 'Evento'
    };
    const MAP_FOTO_COLOR = {
        'Identificado': 'success',
        'No Identificado': 'danger',
        'No Enrolado': 'warning',
        'Foto Inválida': 'info',
        'No Disponible': 'primary',
        'Entrenamiento Inválido': 'warning'
    };
    const iconFaceStr = (faceStr) => {
        const map = {
            'Identificado': `<span class="${hintClass('success')}" aria-label="${faceStr}" ><span class="font1 text-success bi bi-person-bounding-box"></span></span>`,
            'No Identificado': `<span class="${hintClass('error')}" aria-label="${faceStr}" ><span class="font1 text-danger bi bi-person-bounding-box"></span></span>`,
            'No Enrolado': `<span class="${hintClass('warning')}" aria-label="${faceStr}" ><span class="font1 text-warning bi bi-person-bounding-box"></span></span>`,
            'Foto Inválida': `<span class="${hintClass('info')}" aria-label="${faceStr}" ><span class="font1 text-info bi bi-person-bounding-box"></span></span>`,
            'No Disponible': `<span class="${hintClass('primary')}" aria-label="${faceStr}" ><span class="font1 text-primary bi bi-person-bounding-box"></span></span>`,
            'Entrenamiento Inválido': `<span class="${hintClass('warning')}" aria-label="${faceStr}" ><span class="font1 text-warning bi bi-person-bounding-box"></span></span>`
        };
        return map[faceStr] || '';
    }

    $('#Encabezado').addClass('pointer')
    $('#RowTableUsers').hide();
    $('#RowTableDevices').hide();
    $('#RowTableZones').hide();

    sessionStorage.setItem('tab_32', 'visible');

    document.addEventListener("visibilitychange", function () {
        const state = document.visibilityState;
        sessionStorage.setItem('tab_32', state);
        if (state == 'visible') {
            fetchCreatedDate('api/createdDate.php?c=' + $('.selectjs_cuentaToken').val());
        }
    });

    $(window).on('load', function () {
        $('.loading').hide()
    });

    $.fn.DataTable.ext.pager.numbers_length = 5;
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
                $(row).addClass('fadeIn');
            },
            columns: [
                /** Columna Foto */
                {
                    className: 'text-center', targets: 'imageData', title: '<div class="w70">Fichadas</div>',
                    render: function (data, type, row, meta) {

                        const color = MAP_FOTO_COLOR[row.confidenceFaceStr] || '';
                        let foto = '';
                        if (row.r2FileName && row.attPhoto != 1) {
                            /** si tenemos imagen en R2 */
                            foto = `<img loading="lazy" src="${row.r2FileName}" class="w60 h60 img-fluid"></img>`;

                            return `<div class="pic w70 h70 border border-${color} d-flex justify-content-center align-items-center pointer">${foto}</div>`;

                        }

                        if (row.imageData.img) {
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
                            foto = ``;
                        }

                        if (row.attPhoto == 1) {
                            foto = ``;

                        }
                        if (row.basePhoto) {
                            foto = `<img src="data:image/jpeg;base64,${row.basePhoto}" alt="${row.userName}" class="w40 h40 img-fluid" />`
                        }

                        return `<div class="pic w70 h70 border border-${color} d-flex justify-content-center align-items-center pointer">${foto}</div>`;
                    },
                },
                /** Columna Usuario */
                {
                    className: 'text-left w-100', targets: '', title: `
                <div class="w-100"></div>
                `,
                    render: function (data, type, row, meta) {

                        let btnAdd = ''
                        let nameZone = (row.zoneName == null) ? 'Fuera de Zona' : row.zoneName;
                        nameZone = (row.regLat == 0) ? '' : nameZone;
                        let zoneName = (row.zoneID > 0) ? '<span class="text-success">' + nameZone + '</span>' : '<div class="text-danger pt-1">' + nameZone + '</div>'
                        let zoneName2 = (row.zoneID > 0) ? row.zoneName : 'Fuera de Zona'
                        let Distance = (row.zoneID > 0) ? '. Distancia: ' + row.zoneDistance + ' mts' : ''
                        let Distance2 = (row.zoneID > 0) ? '' + row.zoneDistance + ' mts' : ''

                        btnAdd = `<span class="ml-2">
                        <span title="Crear Zona" class="text-secondary font07 btn p-0 m-0 btn-link createZoneOut mt-1"><i class="bi bi-plus px-2 p-1 border"></i></span>
                        <span title="Procesar Zona" aria-label="Procesar Zona" class="hint hint--right text-secondary font07 btn p-0 m-0 btn-link proccessZone mt-1"><i class="bi bi-arrow-left-right ml-1 px-2 p-1 border"></i></span>
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
            lengthMenu: lengthMenuUsers(),
            bProcessing: false,
            serverSide: true,
            deferRender: true,
            searchDelay: 1000,
            paging: true,
            searching: true,
            info: true,
            ordering: false,
            scrollY: '415px',
            scrollCollapse: true,
            scrollX: true,
            fixedHeader: false,
            language: DT_SPANISH_SHORT2,
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
                $(row).addClass('fadeIn');
            },
            columns: [
                /** Columna Foto */
                {
                    className: 'text-center', targets: 'imageData', title: '<div class="w50">Foto</div>',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        const color = MAP_FOTO_COLOR[row.confidenceFaceStr] || '';

                        let foto = '';

                        if (row.r2FileName && row.attPhoto != 1) {
                            /** si tenemos imagen en R2 */
                            foto = `<img loading="lazy" src="${row.r2FileName}" class="w45 h45 img-fluid flipInX"></img>`;

                            return `<div class="pic scale w50 h50 border border-${color} d-flex justify-content-center align-items-center pointer">${foto}</div>`;

                        }

                        if (row.imageData.img) {
                            let path = '';
                            let url_foto = `${row.imageData.img}`;
                            let apiMobile = document.getElementById('apiMobile').value;
                            if (apiMobile == 'http://localhost:8050') {
                                path = ''
                            } else {
                                path = document.getElementById('apiMobile').value + '/chweb/mobile/hrp/'
                            }
                            foto = `<img loading="lazy" src="${path}${url_foto}" class="w45 h45 img-fluid flipInX">`;
                        } else {
                            url_foto = ``;
                            foto = ``;

                        }
                        if (row.attPhoto == 1) {
                            foto = ``;
                        }
                        if (row.basePhoto) {
                            foto = `<img src="data:image/jpeg;base64,${row.basePhoto}" alt="${row.userName}" class="w45 h45 img-fluid flipInX" />`
                        }
                        return `<div class="pic scale w50 h50 border border-${color} d-flex justify-content-center align-items-center pointer">${foto}</div>`;
                    },
                },
                /** Columna Usuario */
                {
                    className: 'text-left', targets: '', title: `<div class="w150">Usuario</div>`,
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
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
                    className: '', targets: '', title: `<div class="w70">Fecha</div>`,
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        return `
                        <div class="w70">
                            <span class="">${row.regDate}</span><br>
                            <span class="text-secondary font07">${row.regDay}</span>
                        </div>
                        `
                    },
                },
                /** Columna HORA */
                {
                    className: '', targets: '', title: '<div class="w40">Hora</div>',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        return `<div class="font-weight-bold ls1">${row.regTime}</div>`;
                    },
                },
                /** Columna FACE */
                {
                    className: 'text-center', targets: '', title: '<div class="w40">Rostro</div>',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        const faceStr = row.confidenceFaceStr || 'No Disponible';
                        const processRegFace = (faceStr == 'No Disponible' || faceStr == 'Entrenamiento Inválido') ? '' : 'processRegFace pointer';
                        return `<div class="w40 ${processRegFace}">${iconFaceStr(faceStr) || ''}</div>`;
                    },
                },
                /** Columna Zona */
                {
                    className: '', targets: '', title: '<div class="w120">Zona</div>',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        let btnAdd = ''
                        let zoneName = (row.zoneID > 0) ? '<div class="text-success">' + row.zoneName + '</div>' : '<div class="text-danger">Fuera de Zona</div>'
                        let zoneName2 = (row.zoneID > 0) ? row.zoneName : 'Fuera de Zona'
                        let Distance = (row.zoneID > 0) ? '. Distancia: ' + row.zoneDistance + ' mts' : ''
                        let Distance2 = (row.zoneID > 0) ? '' + row.zoneDistance + ' mts' : ''

                        btnAdd = `<div class="d-flex py-1" style="gap:5px">
                                    <div title="Crear Zona" class="text-secondary font07 btn p-0 m-0 btn-link createZoneOut">
                                        <i class="bi bi-plus-lg px-2 p-1 border"></i>
                                    </div>
                                    <div title="Procesar Zona">
                                        <span class="text-secondary font07 btn p-0 m-0 btn-link proccessZone">
                                            <i class="bi bi-arrow-left-right px-2 p-1 border"></i>
                                        </span>
                                    </div>
                                </div>`;

                        if (row.regLat == 0) {
                            btnAdd = `<div class="text-secondary font07 p-0 m-0">Sin datos GPS</div>`;
                        }

                        const device = (row.zoneID == 0) ? `<div class="text-danger"><label class="m-0 p-0 font08">${zoneName}</label>${btnAdd}</div>` : `<div class="">${zoneName}</div><div class="text-secondary font07">${Distance2}</div>`;

                        return `<div title="${zoneName2}" class="w120 text-truncate">${device}</div>`;
                    },
                },
                /** Columna Mapa */
                {
                    className: '', targets: '', title: '<div class="w40">Mapa</div>',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        const linkMapa = `https://www.google.com/maps/place/${row.regLat},${row.regLng}`;
                        const iconMapa = (row.regLat != '0') ? `<a href="${linkMapa}" target="_blank" rel="noopener noreferrer" class="hint hint--left" aria-label="Ver Mapa"><i class="bi bi-pin-map-fill btn btn-sm btn-outline-info border-0 linkMapa"></i></a>` : `<i data-titler="Sin datos GPS" class="bi bi-x-lg btn btn-sm btn-outline-danger border-0 linkMapa"></i>`
                        return `<div class="w40">${iconMapa}</div>`
                    },
                },
                /** Columna Tipo */
                {
                    className: '', targets: '', title: '<div class="w70">Tipo</div>',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        let evento = '';
                        evento = MAP_EVENTO[row.operationType] || 'Desconocido';
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
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';

                        const deviceName = row.deviceName || '';
                        const phoneID = row.phoneID || '';

                        let btnAdd = `<span data-titlet="Agregar Dispositivo" class="text-secondary font07 btn p-0 m-0 btn-link addDevice">
                            Agregar Dispositivo <i class="bi bi-plus ml-1 px-1 border-0 bg-ddd"></i>
                        </span>`;

                        const colorDevice = (deviceName == phoneID) ? 'text-danger' : '';
                        const iconEditDevice = (deviceName == phoneID) ? '<i class="bi bi-pencil-fill font07 ml-2 text-primary"></i>' : '';

                        const device = (!deviceName) ? `<div class="text-danger"><label class="m-0 p-0 w140 font08">${phoneID}</label><br>${btnAdd}</div>` : `<div class="d-flex align-items-center updDeviceTable pointer ${colorDevice} hint--rounded hint--no-arrow hint--secondary hint--no-shadow"
                        aria-label="Editar Dispositivo" >${deviceName} ${iconEditDevice}</div><div class="text-secondary font07">${phoneID}</div>`;

                        return `<div class="smtdcol text-truncate" style="max-width:180px">${device}</div>`
                    },
                },
                /** Columna version APP */
                {
                    className: '', targets: '', title: '',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        return `<div class="font07 text-secondary">${row.appVersion}</div>`;
                    },
                },
                /** Columna Flag */
                {
                    className: 'w-100 text-right', targets: '', title: '',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        const locked1 = '<span data-titlel="' + row.error + '" class="font1 pointer bi bi-clipboard-x-fill text-danger"></span>';
                        const locked2 = '<span class="font1 bi bi-clipboard-check-fill text-success"></span>';
                        return `<div class="">${row.locked == '1' ? locked1 : locked2}</div>`;
                    },
                },
            ],
            lengthMenu: lengthMenuUsers(),
            bProcessing: false,
            serverSide: true,
            deferRender: true,
            searchDelay: 1000,
            paging: true,
            searching: true,
            info: true,
            ordering: false,
            scrollY: '425px',
            scrollCollapse: true,
            scrollX: true,
            fixedHeader: false,
            language: DT_SPANISH_SHORT2,
        });
    }

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
        dateRange();
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
        $('.Filter').html(`<button class="btn btn-light bg-white border radius h40 hint--rounded hint--no-arrow hint--secondary hint--no-shadow hint--bottom"
        aria-label="Filtros avanzados" type="button" data-toggle="collapse" data-target="#collapseFilterChecks" aria-expanded="false" aria-controls="collapseFilterChecks">
        <i class="bi bi-funnel-fill text-secondary"></i>
        </button>`)
        $('.Filter').prepend(`<span class="mr-1 hint--rounded hint--no-arrow hint--secondary hint--no-shadow hint--top" aria-label="Actualizar registros" >
            <button id="refreshReg" class="h40 btn btnRefresh"
            type="button">
            <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-arrows-up-down"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 3l0 18" /><path d="M10 6l-3 -3l-3 3" /><path d="M20 18l-3 3l-3 -3" /><path d="M17 21l0 -18" /></svg>
            </button>
        </span>`);
        $('#table-mobile_filter input').removeClass('form-control-sm');
        $('#table-mobile_filter input').attr("style", "height: 40px !important");
        select2Simple('#table-mobile_length select', '', false, false);
        $('.SoloFic').hide();
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
        $('.navbar').removeClass('loader-in');
        const refreshReg = document.getElementById('refreshReg');
        refreshReg.addEventListener('click', function (e) {
            e.preventDefault();
            refreshReg.disabled = true;
            actualizarRegistros('#table-mobile')
        });
    });
    $('#table-mobile').DataTable().on('draw.dt', function (e, settings, json) {
        $('#table-mobile').removeClass('loader-in');
        const refreshReg = document.getElementById('refreshReg');
        if (refreshReg) {
            refreshReg.disabled = false;
        }
        loadMap(settings.json.data, 'map_id_' + settings.json.draw);
        return true
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

    $('#table-mobile').DataTable().on('page.dt', function (e, settings, json) {
        loadingTable('#table-mobile')
    });

    $('#table-mobile').DataTable().on('xhr.dt', function (e, settings, json) {
        fetchCreatedDate('api/createdDate.php?c=' + $('.selectjs_cuentaToken').val());
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

        let getModal = ls.get(LS_MODALES); // obtener modal de localStorage

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
        if (data.r2FileName) {
            picFoto = `${data.r2FileName}`;
        }
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
            $('.modal-body #noGPS').html('')
            let lati = parseFloat($('#latitud').val())
            let long = parseFloat($('#longitud').val())
            let zone = ($('#zona').val())
            zone = (zone) ? zone : 'Fuera de Zona';
            let user = ($('#modalNombre').val()) ? $('#modalNombre').val() : 'Inválido';
            getMap(lati, long, 15, zoneName, zoneRadio, zoneLat, zoneLng, zoneDistance, user, picdia, data.zoneID).then(() => {
                let positionData = document.getElementById('positionData');
                if (positionData) {
                    positionData.innerHTML = '';
                }
                obtenerPost(data.regLat, data.regLng).then((response) => {
                    $('.modal-body').append(`<span class="fadeIn" id="positionData">
                        <div class="font07 pb-1"><i class="bi bi-signpost mr-1"></i>Dirección aproximada:</div>
                        <div class="font08 alert alert-success"><span>${response}</span></div>
                    </span>`);
                });
            });
        } else {
            $('#mapzone').html('');
            $('.modal-body #noGPS').html('<div class="text-center mt-2 m-0 mt-2 font08 alert alert-info mt-2"><span>Ubicación GPS no disponible</span></div>')
        }

        // });
    });
    $(document).on("hidden.bs.modal", "#pic", function (e) {
        modalRegistro.innerHTML = '';
    })
    $(document).on("click", ".processRegFace", function (e) {
        // ActiveBTN(true, ".processRegFace", loading, '')
        $(this).prop('disabled', true);
        $(this).html(loading);
        let data = $('#table-mobile').DataTable().row($(this).parents("tr")).data();
        processRegFace(data.id_api)
    });
    $(document).on("click", "#Encabezado", function (e) {
        const mapDocumentTitle = {
            'Dispositivos Mobile': dtDevices,
            'Usuarios Mobile': dtUsers,
            'Zonas Mobile': dtZones,
            'Mobile HRP': minmaxDate,
        }
        mapDocumentTitle[document.title]?.();
    });
    $(document).on("click", ".showUsers", function (e) {
        // CheckSesion();
        enableBtnMenu();
        $(this).prop('readonly', true);
        focusBtn(this);
        document.title = "Usuarios Mobile";
        $('#Encabezado').html("Usuarios Mobile");
        focusRowTables();
        $('#RowTableUsers').addClass('invisible');
        dtUsers();
        $('#RowTableUsers').show();
    });
    $(document).on("click", ".showDevices", function (e) {
        // CheckSesion()
        enableBtnMenu();
        $(this).prop('readonly', true)
        focusBtn(this);
        document.title = "Dispositivos Mobile"
        $('#Encabezado').html("Dispositivos Mobile");
        focusRowTables();
        $('#RowTableDevices').addClass('invisible');
        dtDevices();
        $('#RowTableDevices').show();
    });
    $(document).on("click", ".showZones", function (e) {
        // CheckSesion()
        enableBtnMenu();
        $(this).prop('readonly', true);
        focusBtn(this);
        document.title = "Zonas Mobile";
        $('#Encabezado').html("Zonas Mobile");
        focusRowTables();
        $('#RowTableZones').show();
        $('#RowTableZones').addClass('invisible');
        dtZones();
    });
    $(document).on("click", ".showChecks", function (e) {
        // CheckSesion()
        enableBtnMenu();
        $(this).addClass('btn-custom');
        $(this).prop('readonly', true);
        focusBtn(this);
        document.title = "Mobile HRP";
        $('#Encabezado').html("Mobile HRP");
        focusRowTables();
        $('#RowTableMobile').show();
        loadingTable('#table-mobile');
        $('#table-mobile').DataTable().columns.adjust().draw();
    });
    $(document).on("click", ".sendCH", function (e) {
        // CheckSesion()
        e.preventDefault();
        var legFech = $(this).attr('data-legFech');
        let dataRecid = $(this).attr('data-recid');
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

    /**
      * Recupera el nombre de una ubicación en función de su latitud y longitud.
      * Si el nombre ya está almacenado en el almacenamiento local, devuelve el nombre almacenado.
      * De lo contrario, realiza una solicitud a la API de OpenStreetMap para obtener el nombre y lo almacena en el almacenamiento local.
     * @param {number} lat - La latitud de la ubicación.
     * @param {number} lng - La longitud de la ubicación.
     * @returns {Promise<string>} El nombre de la ubicación.
     */
    const obtenerPost = async (lat, lng) => {

        let lsDirNames = ls.get(LS_DIR_NAME);
        if (lsDirNames) {
            let pos = lat + ',' + lng;
            let dirName = lsDirNames.find(x => x.pos === pos);
            if (dirName) {
                return dirName.name;
            }
        }

        let url = `data/position_data/${lat}/${lng}`;
        let response = await axios.get(url);

        let array_dirNames = [];
        if (LS_DIR_NAME) {
            array_dirNames = ls.get(LS_DIR_NAME);
        }
        // Comprueba si array_dirNames es null
        if (array_dirNames === null) {
            array_dirNames = [];
        }
        array_dirNames.push({
            pos: lat + ',' + lng,
            name: response.data.data
        });

        ls.set(LS_DIR_NAME, array_dirNames);

        return response.data.data ?? '';
    }
});