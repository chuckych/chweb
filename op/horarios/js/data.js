/* use sessionStorage */
ls.config.storage = sessionStorage;

const iconHoraLe1 = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" /><path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M15 3v4" /><path d="M7 3v4" /><path d="M3 11h16" /><path d="M18 16.496v1.504l1 1" /></svg>`;
const iconRota = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h12" /><path d="M20 14l2 2h-3" /><path d="M20 18l2 -2" /><path d="M19 16a3 3 0 1 0 2 5.236" /></svg>`;
const iconCita = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v5" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M19 19m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /></svg>`;

$(function () {

    const homehost = $("#_homehost").val();
    const LS_REQUEST_PERSONAL = `${homehost}_request_personal`;
    const LS_HORARIOS = `${homehost}_horarios`;
    const LS_HORARIOS_ASIGN = `${homehost}_horarios_asign`;
    const LS_HORARIOS_ASIGN_CACHE = `${homehost}_horarios_asign_cache`;
    const LS_LEGAJO = `${homehost}_horarios_legajo`;
    const LS_ACTION = `${homehost}_action`;
    const LS_ACTION_SET = `${homehost}_action_set`;
    const LS_FORM_DATA = `${homehost}_form_data`;
    const LS_PERIODO = `${homehost}_periodo`;
    const timestamp = new Date().getTime();
    const LS_MODAL_ASIGN = `${homehost}_modal_asign`;
    const DT_TABLE_HORARIOS = `#tableHorarios`;
    const DT_TABLE_PERSONAL = `#tablePersonal`;
    const ID_MODAL = `#modalAsign`;
    const LS_VALUE_FECHA = `${homehost}_value_fecha`;
    const LS_MARCADOS = `${homehost}_marcados`;
    const LS_MARCADOS_PAGE = `${homehost}_marcados_page`;
    const LS_PAGE_PERSONAL = `${homehost}_page_personal`;

    ls.set(LS_LEGAJO, '');
    ls.set(LS_PAGE_PERSONAL, 1);
    ls.set(LS_MARCADOS_PAGE, 0);
    ls.set(LS_MARCADOS, {});

    ls.set(LS_HORARIOS_ASIGN_CACHE, {});

    axios.get('modal-asign.html?v=' + timestamp).then(response => {
        ls.set(LS_MODAL_ASIGN, response.data);
    });
    const getHorariosColumn = (HorCodi) => {
        return ls.get(LS_HORARIOS)?.horariosColumn[HorCodi] ?? [];
    };
    const getRotacionColumn = (RotCodi) => {
        return ls.get(LS_HORARIOS)?.rotacionColumn[RotCodi] ?? [];
    };
    /** agregando los iconos de acciones */
    const m_horale1 = document.querySelector('.m_horale1');
    const m_rota = document.querySelector('.m_rota');
    const m_cita = document.querySelector('.m_cita');
    const l_horale1 = document.querySelector('.l_horale1');
    const l_rota = document.querySelector('.l_rota');
    const l_cita = document.querySelector('.l_cita');

    const clasesHintInfo = ['hint--top', 'hint--info', 'hint--no-shadow', 'hint--no-animate', 'hint--rounded'];
    const clasesHintInfoR = ['hint--right', 'hint--info', 'hint--no-shadow', 'hint--no-animate', 'hint--rounded'];

    m_horale1.innerHTML = `<span class="d-flex flex-column align-items-center">${iconHoraLe1} <span class="font07">Horario</span></span>`;
    m_rota.innerHTML = `<span class="d-flex flex-column align-items-center">${iconRota} <span class="font07">Rotación</span></span>`;
    m_cita.innerHTML = `<span class="d-flex flex-column align-items-center">${iconCita} <span class="font07">Citación</span></span>`;

    l_horale1.innerHTML = `<span class="d-flex flex-column align-items-center">${iconHoraLe1} <span class="font07"></span></span>`;
    l_rota.innerHTML = `<span class="d-flex flex-column align-items-center">${iconRota} <span class="font07"></span></span>`;
    l_cita.innerHTML = `<span class="d-flex flex-column align-items-center">${iconCita} <span class="font07"></span></span>`;

    l_horale1.classList.add(...clasesHintInfo);
    l_rota.classList.add(...clasesHintInfo);
    l_cita.classList.add(...clasesHintInfo);

    const verHorarios = qs('.verHorarios');
    const verRotaciones = qs('.verRotaciones');
    const verCitaciones = qs('.verCitaciones');

    verHorarios.classList.add(...clasesHintInfo);
    verRotaciones.classList.add(...clasesHintInfo);
    verCitaciones.classList.add(...clasesHintInfo);

    const inputVerHorarios = verHorarios.querySelector('input');
    const inputVerRotaciones = verRotaciones.querySelector('input');
    const inputVerCitaciones = verCitaciones.querySelector('input');

    const divHorarioDesde = qs('#divHorariosDesde');
    const divHorarioDesdeHasta = qs('#divHorariosDesdeHasta');
    const divRotaciones = qs('#divRotaciones');
    const divCitaciones = qs('#divCitaciones');

    inputVerHorarios.addEventListener('click', (e) => {
        const label = e.target.closest('label');
        const hint = !inputVerHorarios.checked ? 'Mostrar Horarios' : 'Ocultar Horarios';
        label.setAttribute('aria-label', hint);
        divHorarioDesde.hidden = !inputVerHorarios.checked;
        divHorarioDesdeHasta.hidden = !inputVerHorarios.checked;
    });

    inputVerRotaciones.addEventListener('click', (e) => {
        const label = e.target.closest('label');
        const hint = !inputVerRotaciones.checked ? 'Mostrar Rotaciones' : 'Ocultar Rotaciones';
        label.setAttribute('aria-label', hint);
        divRotaciones.hidden = !inputVerRotaciones.checked;
    });

    inputVerCitaciones.addEventListener('click', (e) => {
        const label = e.target.closest('label');
        const hint = !inputVerCitaciones.checked ? 'Mostrar Citaciones' : 'Ocultar Citaciones';
        label.setAttribute('aria-label', hint);
        divCitaciones.hidden = !inputVerCitaciones.checked;
    });

    l_horale1.addEventListener('click', async () => {
        await ls.set(LS_ACTION, 'l_horale1');
        getModal();
    });
    l_rota.addEventListener('click', async () => {
        await ls.set(LS_ACTION, 'l_rota');
        getModal();
    });
    l_cita.addEventListener('click', async () => {
        await ls.set(LS_ACTION, 'l_cita');
        getModal();
    });

    // $('#collapseMasivos').collapse('show');

    m_horale1.addEventListener('click', async () => {
        await ls.set(LS_ACTION, 'm_horale1');
        getModal();
    });
    m_rota.addEventListener('click', async () => {
        await ls.set(LS_ACTION, 'm_rota');
        getModal();
    });
    m_cita.addEventListener('click', async () => {
        await ls.set(LS_ACTION, 'm_cita');
        getModal();
    });

    var maskBehavior = function (val) {
        val = val.split(":");
        return parseInt(val[0]) > 19 ? "HZ:M0" : "H0:M0";
    }
    spOptions = {
        onKeyPress: function (val, e, field, options) {
            field.mask(maskBehavior.apply({}, arguments), options);
        },
        translation: {
            'H': { pattern: /[0-2]/, optional: false },
            'Z': { pattern: /[0-3]/, optional: false },
            'M': { pattern: /[0-5]/, optional: false }
        }
    };

    $("#_porApNo").click(function () {
        CheckSesion()
        $(".divTablas").hide()
        $("#divSelectLegajo").show()
        if ($("#_porApNo").is(":checked")) {
            $("#_porApNo").val('on').trigger('change')
            dt_tablePersonal()
        } else {
            $("#_porApNo").val('off').trigger('change')
            dt_tablePersonal()
        }
        $('#divData').html('')
    });
    const countMarcados = (bool = false, count = 0) => {
        const textCount = `<span class="font09 text-secondary text-monospace ml-1">${count}</span>`
        const icon = !bool ? `<i class="bi bi-check-square"></i>${textCount}` : `<i class="bi bi-check-square-fill"></i>${textCount}`
        qs('.countMarcados').innerHTML = `<span class="pointer">${icon}</span>`
    }
    const actualizarMarcados = (input, legajo, marcados) => {
        if (input.checked) {
            marcados[legajo] = true;
        } else {
            delete marcados[legajo];
        }
    }

    const dt_tablePersonal = async (nullFalse = false) => {

        if ($.fn.DataTable.isDataTable(DT_TABLE_PERSONAL)) {
            $(DT_TABLE_PERSONAL).addClass('loader-in');
            return new Promise((resolve, reject) => {
                if (nullFalse) {
                    $(DT_TABLE_PERSONAL).DataTable().ajax.reload(null, false);
                } else {
                    $(DT_TABLE_PERSONAL).DataTable().ajax.reload();
                }
                ls.set(LS_HORARIOS_ASIGN_CACHE, {});
                resolve();
            });
        }
        const tablePersonal = $(DT_TABLE_PERSONAL).DataTable({
            bProcessing: true,
            serverSide: true,
            deferRender: true,
            dom: `
                <'row'
                    <'d-flex justify-content-between align-items-center col-12 px-0'
                        <'d-inline-flex align-items-center'l<'ml-2 countMarcados'>>
                        <f>
                    >
                >
                <'row '
                    <'col-12 table-responsive border radius p-2't>
                >
                <'row'
                    <'col-12 d-flex table-responsive justify-content-between align-items-center'ip>
                >`,
            "ajax": {
                url: "../../app-data/get_personal_horarios",
                type: "POST",
                dataType: "json",
                "data": function (data) {
                    data._eg = $("input[name=_eg]:checked").val();
                    data._porApNo = $("input[name=_porApNo]:checked").val();
                    data.Per = $("#Per").val();
                    data.Emp = $("#Emp").val();
                    data.Plan = $("#Plan").val();
                    data.Sect = $("#Sect").val();
                    data.Sec2 = $("#Sec2").val();
                    data.Grup = $("#Grup").val();
                    data.Sucur = $("#Sucur").val();
                    data.Tipo = $("#Tipo").val();
                    data.Tare = $("#Tare").val();
                    data.Conv = $("#Conv").val();
                    data.Regla = $("#Regla").val();
                },
                error: function () {
                    $(DT_TABLE_PERSONAL).css("display", "none");
                }
            },
            createdRow: function (row, data, dataIndex) {
                $(row).addClass('pointer');
                $(row).attr('data-legajo', data.pers_legajo);

                const legajoSelected = ls.get(LS_LEGAJO)?.pers_legajo ?? '';
                const marcados = ls.get(LS_MARCADOS) ?? {};
                if (marcados[data.pers_legajo]) {
                    const checkbox = $(row).find('.checkLega');
                    if (checkbox) {
                        checkbox.prop('checked', true);
                    }
                }
                if (data.pers_legajo === legajoSelected) {
                    $(row).addClass('table-active');
                } else {
                    $(row).removeClass('table-active');
                }

            },
            columns: [
                {
                    data: 'pers_legajo', className: 'pr-0', render: function (data, type, row, meta) {
                        return `
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input checkLega" id="check_${data}">
                                <label class="custom-control-label" for="check_${data}"></label>
                            </div>
                        `;
                    },
                },
                {
                    className: 'w-100', targets: '', title: '',
                    "render": function (data, type, row, meta) {
                        const pers_nombre = row['pers_nombre'];
                        const pers_legajo = row['pers_legajo'];
                        const pers_horario = row['pers_horario'] ?? [];
                        const horario = pers_horario?.TipoAsignStr ?? '';
                        return `
                            <div class="d-flex flex-column">
                                <div>
                                    <span class="font08">(${pers_legajo})</span> 
                                    <span class="">${pers_nombre}</span>
                                </div> 
                                <div class="font08 text-truncate opa9" title="${horario}" style="max-width:350px">${horario}</div>
                            </div>`;
                    },
                },
            ],
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            paging: true,
            searching: true,
            info: true,
            ordering: 0,
            responsive: 0,
            language: lenguaje_dt('Leg.')
        });
        tablePersonal.on('draw.dt', function (e, settings) {
            e.preventDefault();
            const request = settings?.json?.request ?? '';
            if (request) {
                delete request.columns;
                ls.set(LS_REQUEST_PERSONAL, request);
            }
            $(DT_TABLE_PERSONAL).removeClass('loader-in');
        });
        tablePersonal.on('init.dt', function (e, settings) {
            e.preventDefault();
            $("#PersonalTable").removeClass('invisible');
            $("#tablePersonal_filter .form-control").attr('placeholder', 'Buscar ...')
            $("#tablePersonal_filter .form-control").css('width', '200px')
            $("#tablePersonal_wrapper thead").remove();
            qs('#detalleHorario').hidden = false;
            countMarcados(false);
        });
        tablePersonal.on('page.dt', function (e, settings, json) {
            $(DT_TABLE_PERSONAL).addClass('loader-in');
            const start = settings._iDisplayStart || 0;
            const length = settings._iDisplayLength || 5;
            const page = start / length + 1
            ls.set(LS_PAGE_PERSONAL, page);
        });
        $.fn.DataTable.ext.pager.numbers_length = 5;

        qs('.countMarcados').addEventListener('click', (e) => {

            const table = $(DT_TABLE_PERSONAL).DataTable();
            const page = ls.get(LS_PAGE_PERSONAL) || 1;
            const marcadosPage = ls.get(LS_MARCADOS_PAGE) || 1;
            const marcados = ls.get(LS_MARCADOS) || {};

            if (e.target.className === 'bi bi-check-square-fill') { // Desmarcar

                if (marcados && marcadosPage != page) {  // Si hay marcados y la página actual es diferente a la página de los marcados
                    table.$('tr').each((i, el) => {
                        table.$('input[type="checkbox"]').prop('checked', true); // Marca todos los checkbox
                        const legajo = el.getAttribute('data-legajo'); // Obtiene el legajo
                        marcados[legajo] = true; // Marca el legajo
                    });
                    const cuenta = Object.keys(marcados).length; // Cuenta los marcados
                    countMarcados(cuenta, cuenta); // Actualiza el contador
                    ls.set(LS_MARCADOS, marcados); // Guarda los marcados
                    ls.set(LS_MARCADOS_PAGE, page); // Guarda la página actual de los marcados
                    return; // Sale de la función
                }

                countMarcados(false); // Actualiza el contador
                table.$('input[type="checkbox"]').prop('checked', false); // Desmarca todos los checkbox
                ls.set(LS_MARCADOS, {}); // Limpia los marcados
                return;
            } else if (e.target.className === 'bi bi-check-square') { // Marcar
                table.$('input[type="checkbox"]').prop('checked', true); // Marca todos los checkbox
                const marcados = {}; // Inicializa los marcados
                table.$('tr').each((i, el) => { // Recorre las filas
                    const legajo = el.getAttribute('data-legajo'); // Obtiene el legajo
                    marcados[legajo] = true; // Marca el legajo
                });
                const cuenta = Object.keys(marcados).length; // Cuenta los marcados
                countMarcados(cuenta, cuenta); // Actualiza el contador
                ls.set(LS_MARCADOS, marcados); // Guarda los marcados
            }
        });
        tablePersonal.on('click', 'tbody tr', async (e) => {

            e.preventDefault();
            e.stopImmediatePropagation();
            qs('#detalleHorario').hidden = false;


            const tr = e.target.closest('tr');
            const data = $(DT_TABLE_PERSONAL).DataTable().row($(tr)).data();
            this.querySelectorAll('tr').forEach(tr => tr.classList.remove('table-active'));
            // Verifica si el elemento es un checkbox
            if (e.target.tagName === 'LABEL') {
                const input = e.target.previousElementSibling; // Obtiene el input checkbox
                input.checked = !input.checked;
                const legajo = data.pers_legajo;

                let marcados = ls.get(LS_MARCADOS) || {};
                actualizarMarcados(input, legajo, marcados);
                const cuenta = Object.keys(marcados).length;
                countMarcados(cuenta, cuenta);
                ls.set(LS_MARCADOS, marcados);
                return;
            }
            ls.set(LS_LEGAJO, data);
            tr.classList.add('table-active');
            $(".divTablas").addClass('loader-in');
            $(DT_TABLE_PERSONAL).addClass('loader-in');
            await get_horarios_asign(data.pers_legajo);

            $(".divTablas").removeClass('loader-in');
            $(DT_TABLE_PERSONAL).removeClass('loader-in');
            $(".divTablas").show();

            $("#divSelectLegajo").hide();

            const dataLegajo = document.querySelector('.dataLegajo');
            dataLegajo.innerHTML = `
            <span class="font09">(${data.pers_legajo}) ${data.pers_nombre}</span>
            `
            const asign = ls.get(LS_HORARIOS_ASIGN) ?? [];
            let tableData = [];
            let tableData2 = [];
            let tableData3 = [];
            let tableData4 = [];
            dt_getHorale1('#Horale1', asign['desde'] ?? [])
            dt_getHorale2('#Horale2', asign['desde-hasta'] ?? [])
            dt_getRotacion('#table-rota', asign['rotacion'] ?? [])
            dt_getCitacion('#table-citacion', asign['citacion'] ?? [])
        });
        return await tablePersonal;
    }
    dt_tablePersonal();

    const dt_getHorale1 = async (selector, data) => {
        tableData = data; // Store the data in the outer scope
        if ($.fn.DataTable.isDataTable(selector)) {
            await $(selector).DataTable().clear().destroy();
        }

        const table = $(selector).DataTable({
            "data": tableData,
            dom: dom_table(),
            createdRow: function (row, data, dataIndex) {
                $(row).attr('data-index', dataIndex);
            },
            columns: [
                {
                    data: 'Ho1Hora', className: 'align-middle', targets: 'Ho1Hora', title: '',
                    "render": function (data, type, row, meta) {
                        const dataHorario = getHorariosColumn(data);
                        if (type === 'display') {
                            return grillaHorarios2(dataHorario, `${row.Ho1FechStr}`);
                        }
                        return data + row.Ho1HoraStr + dataHorario.ID + row.Ho1FechStr;
                    },
                },
            ],
            deferRender: true,
            paging: false,
            searching: true,
            info: false,
            ordering: false,
            select: true,
            language: lenguaje_dt('Horarios Desde')
        });

        $(table.table().body()).off('click', 'tr');

        table.on('click', 'tbody tr', (event) => {
            event.preventDefault();
            event.stopImmediatePropagation();
            const elementName = event.target.getAttribute('name') || event.target.tagName.toLowerCase();
            const mapElementName = {
                'edit': 'edit_horale1',
                'delete': 'delete_horale1',
            }
            if (!mapElementName[elementName]) return;
            const tr = event.target.closest('tr');
            const index = tr.getAttribute('data-index');
            const dataRow = tableData[index];
            if (!dataRow) return;
            ls.set(LS_ACTION, mapElementName[elementName]);
            getModal(dataRow);
        });

        $(selector + " thead").remove()
        const total = data.length ?? 0;
        let titleTabla = '';
        if (total > 0) {
            titleTabla = '<div>Horarios Desde: <span class="ls1">(' + (total) + ')</span></div>';
        } else {
            titleTabla = '<div class="fw4">Sin Horario Desde asignado.</div>';
        }
        qs('#titleDesde').innerHTML = titleTabla
        dt_custom_search('.searchDesde', table);
    }
    const dt_getHorale2 = async (selector, data) => { // Tabla de Horarios desde hasta
        tableData2 = data;
        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().clear().destroy();
        }

        const table = $(selector).DataTable({
            "data": tableData2,
            dom: dom_table(),
            createdRow: function (row, data, dataIndex) {
                $(row).attr('data-index', dataIndex);
            },
            columns: [
                {
                    data: 'Ho2Hora', className: 'align-middle', targets: 'Ho2Hora', title: '',
                    "render": function (data, type, row, meta) {
                        const dataHorario = getHorariosColumn(data);
                        if (type === 'display') {
                            return grillaHorarios2(dataHorario, `${row.Ho2Fec1Str} al ${row.Ho2Fec2Str}`);
                        }
                        return data + row.Ho2HoraStr + dataHorario.ID + row.Ho2FechStr;
                    },
                },
            ],
            deferRender: true,
            paging: false,
            searching: true,
            info: false,
            ordering: false,
            responsive: false,
            language: lenguaje_dt('Horarios Desde - Hasta')
        });

        $(table.table().body()).off('click', 'tr');

        table.on('click', 'tbody tr', (event) => {
            event.preventDefault();
            event.stopImmediatePropagation();
            const elementName = event.target.getAttribute('name') || event.target.tagName.toLowerCase();
            const mapElementName = {
                'edit': 'edit_horale2',
                'delete': 'delete_horale2',
            }
            if (!mapElementName[elementName]) return;
            const tr = event.target.closest('tr');
            const index = tr.getAttribute('data-index');
            const dataRow = tableData2[index];
            if (!dataRow) return;
            ls.set(LS_ACTION, mapElementName[elementName]);
            getModal(dataRow);
        });

        $(selector + " thead").remove()
        const total = data.length ?? 0;
        let titleTabla = '';
        if (total > 0) {
            titleTabla = '<div>Horarios Desde Hasta: <span class="ls1">(' + (total) + ')</span></div>';
        } else {
            titleTabla = '<div class="fw4">Sin Horario Desde Hasta asignado.</div>';
        }
        $('#titleDesdeHasta').html(titleTabla)
        dt_custom_search('.searchDesdeHasta', table)
    }
    const dt_getCitacion = async (selector, data) => { // Tabla de Citaciones
        const PERMISOS = getAcciones();
        tableData4 = data; // almacena los datos en el ámbito exterior
        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().clear().destroy();
        }
        const table = $(selector).DataTable({
            "data": tableData4,
            dom: dom_table(),
            createdRow: function (row, data, dataIndex) {
                $(row).attr('data-index', dataIndex);
            },
            columns: [
                {
                    data: 'CitFechStr', className: '', targets: 'CitFechStr', title: 'Fecha',
                    "render": function (data, type, row, meta) {
                        return '<span title="Fecha Citación">' + data + '</span>'
                    },
                },
                {
                    className: 'text-nowrap', targets: 'CitEntra', title: 'Citación',
                    "render": function (data, type, row, meta) {
                        let datacol = '<span title="Horario de Citación">' + row['CitEntra'] + ' a ' + row['CitSale'] + '</span>'
                        return datacol;
                    },
                },
                {
                    className: '', targets: 'CitDesc', title: 'Descanso',
                    "render": function (data, type, row, meta) {
                        const descanso = row['CitDesc'] ?? '';
                        const hintClass = clasesHintInfoR.join(' ');
                        const strDescanso = (descanso != '00:00') ? `<span class="${hintClass}" aria-label="Descanso de Citación">(${descanso})</span>` : '';
                        return strDescanso;
                    },
                },
                {
                    data: 'CitHoras', className: '', targets: 'CitEntra', title: 'Horas',
                    "render": function (data, type, row, meta) {
                        return data;
                    },
                },
                {
                    data: '', className: 'w-100 text-right', targets: '', title: '',
                    "render": function (data, type, row, meta) {
                        return `
                            ${accionesBtnHorarios(PERMISOS['mCit'], PERMISOS['bCit'])}
                        `;
                    },
                },
            ],
            deferRender: true,
            bProcessing: false,
            serverSide: false,
            paging: false,
            searching: true,
            info: false,
            ordering: false,
            responsive: false,
            language: lenguaje_dt('Citaciones')
        });

        $(table.table().body()).off('click', 'tr');

        table.on('click', 'tbody tr', (event) => {
            event.preventDefault();
            event.stopImmediatePropagation();
            const elementName = event.target.getAttribute('name') || event.target.tagName.toLowerCase();
            const mapElementName = {
                'edit': 'edit_citacion',
                'delete': 'delete_citacion',
            }
            if (!mapElementName[elementName]) return;
            const tr = event.target.closest('tr');
            const index = tr.getAttribute('data-index');
            const dataRow = tableData4[index];

            if (!dataRow) return;
            ls.set(LS_ACTION, mapElementName[elementName]);
            getModal(dataRow);
        });

        // $(selector + " thead").remove()
        const total = data.length ?? 0;
        let titleTabla = '';
        if (total > 0) {
            titleTabla = '<div>Citaciones: <span class="ls1">(' + (total) + ')</span></div>';
        } else {
            $(selector + " thead").remove()
            titleTabla = '<div class="fw4">Sin Citaciones.</div>';
        }
        $('#titleCitaciones').html(titleTabla)
        dt_custom_search('.searchCitaciones', table)
    }
    const dt_getRotacion = async (selector, data) => {
        tableData3 = data; // almacena los datos en el ámbito exterior
        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().clear().destroy();
        }
        const table = $(selector).DataTable({
            initComplete: function (settings) {
                $(selector + " thead").remove()
            },
            data: tableData3,
            dom: dom_table(),
            createdRow: function (row, data, dataIndex) {
                $(row).attr('data-index', dataIndex);
            },
            columns: [
                {
                    data: 'RoLRota', className: 'align-middle', targets: 'Ho2Hora', title: '',
                    "render": function (data, type, row, meta) {
                        const dataRotacion = getRotacionColumn(data);
                        const vence = (row.RolVencStr != '31/12/2099') ? ` al ${row.RolVencStr}` : ''
                        const desde = (row.RolVencStr != '31/12/2099') ? `Desde: ` : 'Del: '
                        if (type === 'display') {
                            return grillaRotaciones2(dataRotacion, `${desde}${row.RolFechStr}${vence}`);
                        }
                        return data + row.RoLRota + dataRotacion.ID + row.RolFechStr + row.RolRotaStr;
                    },
                },

            ],
            deferRender: true,
            paging: false,
            searching: true,
            info: false,
            ordering: false,
            responsive: false,
            language: lenguaje_dt('Rotaciones')
        });

        $(table.table().body()).off('click', 'tr');

        table.on('click', 'tbody tr', (event) => {
            event.preventDefault();
            event.stopImmediatePropagation();
            const elementName = event.target.getAttribute('name') || event.target.tagName.toLowerCase();
            const mapElementName = {
                'edit': 'edit_rotacion',
                'delete': 'delete_rotacion',
            }
            if (!mapElementName[elementName]) return;
            const tr = event.target.closest('tr');
            const index = tr.getAttribute('data-index');
            const dataRow = tableData3[index];
            console.log('dataRow', dataRow);

            if (!dataRow) return;
            ls.set(LS_ACTION, mapElementName[elementName]);
            getModal(dataRow);
        });

        $(selector + " thead").remove()
        const total = data.length ?? 0;
        let titleTabla = '';
        if (total > 0) {
            titleTabla = '<div>Rotaciones: <span class="ls1">(' + (total) + ')</span></div>';
        } else {
            titleTabla = '<div class="fw4">Sin Rotación asignada.</div>';
        }
        $('#titleRotaciones').html(titleTabla)
        dt_custom_search('.searchRotaciones', table)
    }
    const dom_table = () => {
        return `<'table-responsive table-hover pointer fadeIn't>`
    }
    const get_horarios = async () => {
        axios.get('../../app-data/horarios').then(async (response) => {
            await ls.set(LS_HORARIOS, response.data);
            accionesMasivas(response.data.acciones['aTur'], response.data.acciones['aCit']);
        })

    }
    const get_horarios_asign = async (Legajo) => {
        if (!Legajo) return;
        const dataCache = ls.get(LS_HORARIOS_ASIGN_CACHE) ?? {};

        if (dataCache[Legajo]) {
            ls.set(LS_HORARIOS_ASIGN, dataCache[Legajo]);
            // return dataCache[Legajo];
            return;
        }

        await axios.get('../../app-data/horarios/asign/' + Legajo).then((response) => {
            ls.set(LS_HORARIOS_ASIGN, response.data);
            // Obtener el array existente o inicializar uno nuevo
            let array = ls.get(LS_HORARIOS_ASIGN_CACHE) ?? {};
            // Almacenar los datos del legajo actual
            array[Legajo] = response.data;
            // Guardar el array actualizado en el almacenamiento local
            ls.set(LS_HORARIOS_ASIGN_CACHE, array);
            return;
        });
    }
    const dt_horarios = (selector, action) => {

        let dataHorarios = ls.get(LS_HORARIOS).horarios ?? [];
        const submit = qs('#submit');

        const mapRotacion = {
            'm_rota': true,
            'l_rota': true,
            'edit_rotacion': true,
        }
        if (mapRotacion[action]) {
            dataHorarios = ls.get(LS_HORARIOS).rotacion ?? [];
        }

        const table = $(selector).dataTable({
            createdRow: function (row, data, dataIndex) {
                $(row).addClass('pointer');
            },
            "data": dataHorarios,
            columns: [
                {
                    className: 'align-middle w-100 select', targets: 'Desc', title: '',
                    "render": function (data, type, row, meta) {
                        if (type === 'display') {
                            if (mapRotacion[action]) {
                                return grillaRotaciones(row);
                            }
                            return grillaHorarios(row);
                        }
                        // Para ordenamiento y filtrado, devolver un valor simple
                        return mapRotacion[action] ? row.RotDesc + row.RotCodi : row.Desc + row.TotalHorasCalc + row.Codi;
                    },
                }
            ],
            dom: `
            <'row'
                <'col-12 d-inline-flex justify-content-between align-items-center'pf>
            >
            <'row' <'col-12't>>
            <'row'
                <'col-12 d-inline-flex justify-content-between align-items-center mt-2'li>
                <'col-12 pagination-bottom'p>
            >
            `,
            lengthMenu: [[5, 10, 25, 50, 100, 200], [5, 10, 25, 50, 100, 200]], //mostrar cantidad de registros
            deferRender: true,
            bProcessing: false,
            paging: true,
            searching: true,
            info: true,
            ordering: false,
            language: lenguaje_dt('Horarios'),
        });

        table.on('init.dt', function (e, settings) {
            e.preventDefault();
            $(`${selector}_wrapper table thead`).remove()
        });

        const inputSearch = document.querySelector(`${selector}_filter input`);
        inputSearch.attributes.placeholder.value = 'Buscar ...';
        inputSearch.focus();

        on_apply_daterangepicker();

        const tableBody = document.querySelector(`${selector} tbody`);
        tableBody.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopImmediatePropagation();
            const target = e.target;
            const row = target.closest('tr');
            if (!row) return; // Salir si no es una fila

            on_apply_daterangepicker();

            const data = $(DT_TABLE_HORARIOS).DataTable().row(row).data(); // Obtener datos de la fila

            if (!data) return; // Salir si no hay datos

            $(row).addClass('selected').siblings().removeClass('selected');
            const inputH1Codhor = document.querySelector('#inputH1Codhor');
            const inputH1horario = document.querySelector('#inputH1horario');
            const inputH1FDias = document.querySelector('#inputH1FDias');
            const inputH1FDesde = document.querySelector('#inputH1FDesde');

            inputH1Codhor.value = data.Codi;
            inputH1horario.value = data.Desc;

            if (mapRotacion[action]) {
                inputH1Codhor.value = data.RotCodi
                inputH1horario.value = data.RotDesc
                // modificar el atributo max del inputH1FDias
                inputH1FDias.attributes.max.value = data.RotDias;
            }
            modalTitleHor(data, action)

            submit.disabled = false

            submit.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopImmediatePropagation();

                const valueHorario = document.querySelector('#inputH1Codhor').value;
                let valueFecha = document.querySelector('#inputH1FDesde').value;
                // let valueVence = document.querySelector('#inputH1FVence').value;
                let valueDias = document.querySelector('#inputH1FDias').value;
                let valueFechaDesde = '';
                let valueFechaHasta = '';

                if (ls.get(LS_PERIODO)) {
                    valueFechaDesde = valueFecha.split(' ')[0].split('/').reverse().join('-');
                    valueFechaHasta = valueFecha.split(' ')[2].split('/').reverse().join('-');

                    valueFecha = valueFechaDesde;
                } else {
                    valueFecha = valueFecha.split('/').reverse().join('-');
                }

                const Procesar = qs('#Procesar');
                const formData = {
                    'Codhor': valueHorario,
                    'Fecha': valueFecha,
                    'FechaD': valueFechaDesde ?? '',
                    'FechaH': valueFechaHasta ?? '',
                    'Procesar': Procesar.checked,
                    'Vence': valueFechaHasta ?? '',
                    'Dias': valueDias ?? '',
                }
                ls.set(LS_FORM_DATA, formData)
                setAsignación(formData)
            });
        });
    }
    const setAsignación = (data) => {
        const tipo = ls.get(LS_ACTION_SET);
        const marcados = ls.get(LS_MARCADOS) ?? {};
        // convertir objeto en array
        const keysMarcado = Object.keys(marcados);
        try {
            if (!data || !tipo) {
                throw new Error('Error en los datos');
            }
            CheckSesion()
            notifyWait(); // Notificación de espera
            const LegNume = ls.get(LS_LEGAJO)?.pers_legajo ?? '';

            axios.post('../../app-data/horarios/' + tipo, {
                'Codi': data.Codhor,
                'Fecha': data.Fecha ?? '',
                'FechaD': data.FechaD ?? '',
                'FechaH': data.FechaH ?? '',
                'Vence': data.Vence ?? '',
                'Dias': data.Dias ?? '',
                'Filtros': ls.get(LS_REQUEST_PERSONAL),
                'LegNume': LegNume,
                'Procesar': data.Procesar,
                'Desc': data.Desc ?? '00:00',
                'Entr': data.Entr ?? '00:00',
                'Sale': data.Sale ?? '00:00',
                'Marcados': keysMarcado,
            }).then(async (response) => {
                $.notifyClose();
                if (response.data.status == 'error') {
                    throw new Error(response.data.message);
                }
                notify(response.data.message, 'success', 5000, 'right');

                $(`${ID_MODAL}`).modal('hide');

                await dt_tablePersonal(true);
                await get_horarios_asign(LegNume);

                const asign = ls.get(LS_HORARIOS_ASIGN) ?? [];

                const mapTipo = {
                    'legajo-desde': 'desde',
                    'desde': 'desde',
                    'delete-legajo-desde': 'desde',
                    'delete-legajo-desde-hasta': 'desde-hasta',
                    'legajo-desde-hasta': 'desde-hasta',
                    'desde-hasta': 'desde-hasta',
                    'legajo-citacion': 'citacion',
                    'delete-legajo-citacion': 'citacion',
                    'edit-legajo-citacion': 'citacion',
                    'citacion': 'citacion',
                    'edit-legajo-rotacion': 'rotacion',
                    'delete-legajo-rotacion': 'rotacion',
                    'rotacion': 'rotacion',
                }

                const dato = mapTipo[tipo]; // Tipo de asignación

                if (mapTipo[tipo] == 'desde') {
                    await dt_getHorale1('#Horale1', asign[dato] ?? [])
                    return;
                }
                if (mapTipo[tipo] == 'desde-hasta') {
                    await dt_getHorale2('#Horale2', asign[dato] ?? [])
                    return;
                }
                if (mapTipo[tipo] == 'citacion') {
                    await dt_getCitacion('#table-citacion', asign[dato] ?? [])
                    return;
                }
                if (mapTipo[tipo] == 'rotacion') {
                    await dt_getRotacion('#table-rota', asign[dato] ?? [])
                    return;
                }

                ls.set(LS_LEGAJO, '');

            }).catch((error) => {
                $.notifyClose();
                notify(error, 'danger', 5000, 'right');
            });

        } catch (error) {
            notify(error, 'danger', 5000, 'right');
        }
    }
    const grillaHorarios = (row) => {

        const Desc = row.Desc
        const Cod = row.Codi
        const ID = row.ID
        const colorRgb = row.Color;
        const colorText = row.ColorText;
        const checkIcon = `<i class="bi bi-check-circle-fill text-success"></i>`;
        const dashIcon = `<i class="bi bi-dash-circle text-secondary"></i>`;
        const styleColor = `style="background-color: ${colorRgb}; color: ${colorText}"`
        const TotalHorasCalc = row.TotalHorasCalc ?? '00:00';

        const iconCheck = (day) => {
            const spanIcon = row[day].LaboralID ? checkIcon : dashIcon
            const spanHoras = row[day].Horas ? `<span class="font06">${row[day].Horas}</span>` : ''
            return `
            <div class="d-flex flex-column justify-content-center align-items-center">
                <span>${spanIcon}</span>
                <span>${spanHoras}</span>
            </div>
            `
        }
        const hintDay = (day) => {
            const Descanso = day.Descanso == '00:00' ? '' : `(${day.Descanso})`
            return `aria-label="${day.Desde} a ${day.Hasta} ${Descanso}"`;
        }
        const classDay = (day, f = row) => {
            return (parseInt(f[day].LaboralID)) ? 'bg-ddd radius hint--top hint--info hint--no-shadow' : 'bg-white'
        }
        const grillaSemanaHtml = `
        <div class="radius p-1 bg-white">
            <div class="select d-flex justify-content-between p-1 font08">
                <div class="fw5">(${Cod}) ${Desc}</div>
                <div class="d-inline-flex align-items-center">
                    <div class="font08">(${ID})</div>
                    <div ${styleColor} class="h10 px-3 radius ml-2"></div>
                </div>
            </div>
            <div class="d-flex justify-content-between flex-row font07 align-items-center mt-1 radius p-1 bg-white">
                <div class="dayGrilla ${classDay('Lunes')}" ${hintDay(row.Lunes)}>
                    <span>Lun</span>
                    <span>${iconCheck('Lunes')}</span>
                </div>
                <div class="dayGrilla ${classDay('Martes')}" ${hintDay(row.Martes)}>
                    <span>Mar</span>
                    <span>${iconCheck('Martes')}</span>
                </div>
                <div class="dayGrilla ${classDay('Miércoles')}" ${hintDay(row.Miércoles)}>
                    <span>Mie</span>
                    <span>${iconCheck('Miércoles')}</span>
                </div>
                <div class="dayGrilla ${classDay('Jueves')}" ${hintDay(row.Jueves)}>
                    <span>Jue</span>
                    <span>${iconCheck('Jueves')}</span>
                </div>
                <div class="dayGrilla ${classDay('Viernes')}" ${hintDay(row.Viernes)}>
                    <span>Vie</span>
                    <span>${iconCheck('Viernes')}</span>
                </div>
                <div class="dayGrilla ${classDay('Sábado')}" ${hintDay(row.Sábado)}>
                    <span>Sab</span>
                    <span>${iconCheck('Sábado')}</span>
                </div>
                <div class="dayGrilla ${classDay('Domingo')}" ${hintDay(row.Domingo)}>
                    <span>Dom</span>
                    <span>${iconCheck('Domingo')}</span>
                </div>
                <div class="dayGrilla ${classDay('Feriado')}" ${hintDay(row.Feriado)}>
                    <span>Fer</span>
                    <span>${iconCheck('Feriado')}</span>
                </div>
            </div>
            <div class="font08 text-right w-100">
                Horas: ${TotalHorasCalc}
            </div>
        </div>
        `
        return grillaSemanaHtml
    }
    const getAcciones = () => {
        const horarios = ls.get(LS_HORARIOS) ?? [];
        const acciones = horarios.acciones ?? [];
        return acciones;
    }
    const grillaHorarios2 = (row, fecha) => {
        const PERMISOS = getAcciones();

        const Desc = row.Desc
        const Cod = row.Codi
        const ID = row.ID
        const colorRgb = row.Color;
        const colorText = row.ColorText;
        const checkIcon = `<i class="bi bi-check-circle-fill text-success"></i>`;
        const dashIcon = `<i class="bi bi-dash-circle text-secondary"></i>`;
        const styleColor = `style="width: 53px; background-color: ${colorRgb}; color: ${colorText}; font-size: 10px;"`

        const iconCheck = (day) => {
            const spanIcon = row[day].LaboralID ? checkIcon : dashIcon
            const descanso = row[day].Descanso == '00:00' ? '' : ` <span class="font06">(D)</span>`
            const spanHoras = row[day].Horas ? `<span>${row[day].Horas}</span>` : ''
            return `
            <div class="d-flex flex-column justify-content-center align-items-center">
                <span>${spanHoras}</span>
            </div>
            `
        }
        const hintDay = (day) => {
            const Descanso = day.Descanso == '00:00' ? '' : `(${day.Descanso})`
            return `aria-label="${day.Desde} a ${day.Hasta} ${Descanso}"`;
        }
        const classDay = (day, f = row) => {
            return (parseInt(f[day].LaboralID)) ? 'bg-ddd radius hint--top hint--info hint--no-shadow' : 'bg-white'
        }
        const grillaSemanaHtml = `
        <div class="">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-dark">${fecha}<br>(${Cod}) ${Desc}</div>
                <div class="d-inline-flex align-items-center" style="gap: 5px;">
                    ${accionesBtnHorarios(PERMISOS['mTur'], PERMISOS['bTur'])}
                    <div ${styleColor} class=" p-1 radius ml-2 text-center">${ID ?? '-'}</div>
                </div>
            </div>
            <div class="d-flex justify-content-between flex-row font07 align-items-center mt-1 radius p-1 bg-white">
                <div class="dayGrilla ${classDay('Lunes')}" ${hintDay(row.Lunes)}>
                    <span>Lun</span>
                    <span>${iconCheck('Lunes')}</span>
                </div>
                <div class="dayGrilla ${classDay('Martes')}" ${hintDay(row.Martes)}>
                    <span>Mar</span>
                    <span>${iconCheck('Martes')}</span>
                </div>
                <div class="dayGrilla ${classDay('Miércoles')}" ${hintDay(row.Miércoles)}>
                    <span>Mie</span>
                    <span>${iconCheck('Miércoles')}</span>
                </div>
                <div class="dayGrilla ${classDay('Jueves')}" ${hintDay(row.Jueves)}>
                    <span>Jue</span>
                    <span>${iconCheck('Jueves')}</span>
                </div>
                <div class="dayGrilla ${classDay('Viernes')}" ${hintDay(row.Viernes)}>
                    <span>Vie</span>
                    <span>${iconCheck('Viernes')}</span>
                </div>
                <div class="dayGrilla ${classDay('Sábado')}" ${hintDay(row.Sábado)}>
                    <span>Sab</span>
                    <span>${iconCheck('Sábado')}</span>
                </div>
                <div class="dayGrilla ${classDay('Domingo')}" ${hintDay(row.Domingo)}>
                    <span>Dom</span>
                    <span>${iconCheck('Domingo')}</span>
                </div>
                <div class="dayGrilla ${classDay('Feriado')}" ${hintDay(row.Feriado)}>
                    <span>Fer</span>
                    <span>${iconCheck('Feriado')}</span>
                </div>
            </div>
        </div>
        `
        return grillaSemanaHtml
    }
    const grillaRotaciones = (row) => {

        const RotDesc = row.RotDesc
        const RotCodi = row.RotCodi
        const RotData = row.RotData
        const RotDias = row.RotDias

        let html = '';
        // let horario = [];

        RotData.forEach(item => {

            let infoHorario = getHorariosColumn(item.RotHora);
            // horario.push({ 'RotCodi': RotCodi, 'infoHorario': infoHorario })

            const ID = infoHorario.ID
            const colorRgb = infoHorario.Color;
            const colorText = infoHorario.ColorText;
            const styleColor = `style="background-color: ${colorRgb}; color: ${colorText}; height: 5px; width:20px"`
            const textoHorario = ID == 0 ? 'Franco' : `Hor.: ${item.RotHora} (${ID})`

            html += `
            <div class="radius p-1 bg-white d-inline-flex hint--top hint--info hint--no-shadow" aria-label="${item.RotHoraStr}">
                <div class="select d-flex flex-column p-2 font07 text-center border radius bg-light w100">
                    <div class="d-inline-flex justify-content-center align-items-center" style="gap: 7px;">
                        <div>${item.RotDias} Días</div>
                        <div ${styleColor} class="radius"></div>
                    </div>
                    <div>${textoHorario}</div>
                </div>
            </div>
            `
        });
        // agrupar por RotCodi
        // const horariosAgrupados = horario.reduce((acc, curr) => {
        //     acc[curr.RotCodi] = acc[curr.RotCodi] || [];
        //     acc[curr.RotCodi].push(curr.infoHorario);
        //     return acc;
        // }, {});
        // console.log(horariosAgrupados);
        const grillaSemanaHtml = `
        <div class="radius p-1 bg-white">
            <div class="select d-flex justify-content-between p-1 font08">
                <div class="fw5">(${RotCodi}) ${RotDesc}</div>
                <div class="font08">${RotDias} Días</div>
            </div>
            ${html}
        </div>
        `
        return grillaSemanaHtml
    }
    const grillaRotaciones2 = (row, fecha) => {
        const PERMISOS = getAcciones();
        const RotDesc = row.RotDesc
        const RotCodi = row.RotCodi
        const RotData = row.RotData
        const RotDias = row.RotDias

        let html = '';
        RotData.forEach(item => {

            let infoHorario = getHorariosColumn(item.RotHora);
            const ID = infoHorario.ID
            const colorRgb = infoHorario.Color;
            const colorText = infoHorario.ColorText;
            const styleColor = `style="border-bottom: 2px solid ${colorRgb};font-size: 10px;"`
            const textoHorario = ID == 0 ? 'Franco' : `${ID} (${item.RotHora})`

            html += `
            <div class="radius p-1 bg-white d-inline-flex hint--top hint--info hint--no-shadow" aria-label="${item.RotDias} Días - ${item.RotHoraStr}">
                <div class="select d-flex flex-column p-2 font07 text-center border radius bg-light">
                    <div class="d-inline-flex justify-content-center align-items-center" style="gap: 5px;">
                        <div>${item.RotDias}D</div>
                        <div ${styleColor}>${textoHorario}</div>
                    </div>
                </div>
            </div>
            `
        });
        const grillaSemanaHtml = `
        <div class="radius p-1">
            <div class="select d-flex justify-content-between p-1 font08">
                <div>${fecha}<br>(${RotCodi}) ${RotDesc}</div>
                 <div class="d-inline-flex align-items-center">
                    ${accionesBtnHorarios(PERMISOS['mTur'], PERMISOS['bTur'])}
                    <div class="font08 ml-2">${RotDias} Días</div>
                 </div>
            </div>
            ${html}
        </div>
        `
        return grillaSemanaHtml
    }
    const modalTitleHor = (data, action) => {
        const elemento = document.querySelector(`${ID_MODAL} .modal-title-horario`);
        const clases = ['p-2', 'my-2'];

        if (!data || !action) {
            elemento.innerHTML = '';
            elemento.classList.remove(...clases);
            return;
        }

        let codigo = data.Codi;
        let descripcion = data.Desc;

        if (action == 'm_rota' || action == 'l_rota' || action == 'edit_rotacion') {
            codigo = data.RotCodi;
            descripcion = data.RotDesc;
        }

        const color = data.Color ?? '#cecece';
        const colorText = data.ColorText ?? '';
        // const valueFecha = document.querySelector('#inputH1FDesde').value;
        const valueFechaLS = ls.get(LS_VALUE_FECHA);
        elemento.innerHTML = `<span class="fadeIn">(${codigo}) ${descripcion} - Desde <span class="title-desde">${valueFechaLS}</span></span>`;
        if (action == 'edit_rotacion') {
            elemento.innerHTML = `<span class="fadeIn">(${codigo}) ${descripcion}</span>`;
        }
        elemento.style.backgroundColor = color;
        elemento.style.color = colorText;
        elemento.style.opacity = '0.8';
        elemento.classList.add(...clases);

        return elemento;
    }
    const accionesBtnHorarios = (edit, baja) => {
        let btnEdit = `<i name="edit" class="btn btn-sm action btn-outline-secondary border-0 radius bi bi-pencil-square"></i>`;
        let btnDelete = `<i name="delete" class="btn btn-sm action btn-outline-danger border-0 radius bi bi-trash3"></i>`;

        if (!edit) {
            btnEdit = '';
        }
        if (!baja) {
            btnDelete = '';
        }

        let div = `
        <div class="d-inline-flex bg-white p-1 border-0 radius" style="gap: 5px;">
            ${btnEdit}
            ${btnDelete}
        </div>
        `
        if (!edit && !baja) {
            div = '';
        }
        return div;
    }
    const accionesMasivas = (horarios, citacion) => {
        if (!horarios) {
            qs('.l_horale1').hidden = true;
            qs('.m_horale1').hidden = true;
            qs('.m_rota').hidden = true;
            qs('.l_rota').hidden = true;
        }
        if (!citacion) {
            qs('.l_cita').hidden = true;
            qs('.m_cita').hidden = true;
        }
        if (!horarios && !citacion) {
            qs('#collapseMasivos').hidden = true;
            qs('[aria-controls="collapseMasivos"]').hidden = true;
        }
    }
    const remove_tr_selected = (selector) => {
        if (!selector) return;
        const tableBody = document.querySelector(selector);
        tableBody.querySelectorAll('tr').forEach(tr => {
            tr.classList.remove('selected');
        });
    }
    const on_apply_daterangepicker = () => {
        const inputH1FDesde = qs('#inputH1FDesde');
        ls.set(LS_VALUE_FECHA, inputH1FDesde.value);
        $('#inputH1FDesde').on('apply.daterangepicker', function (ev, picker) {
            ls.set(LS_VALUE_FECHA, inputH1FDesde.value);
            $('.title-desde').html(inputH1FDesde.value);
        });
    }
    const accionesPeriodo = (action) => {

        const modalTitle = qs(`${ID_MODAL} .modal-title`);
        const inputH1Codhor = qs('#inputH1Codhor');
        const inputH1horario = qs('#inputH1horario');
        const submit = qs('#submit');
        let dataLegajo = ls.get(LS_LEGAJO) || {};
        const nombre = dataLegajo?.pers_nombre ?? '';
        const legajo = dataLegajo?.pers_legajo ?? '';
        const textMarcados = textMarcadosModal();

        const periodo = qs('#Periodo');
        periodo.addEventListener('change', (e) => {

            e.preventDefault();
            e.stopImmediatePropagation();

            if (e.target.checked) {
                dobleDatePicker('#inputH1FDesde', 'right', 'down')
                $('#inputH1FDesde').mask('00/00/0000 al 00/00/0000');

                ls.set(LS_PERIODO, e.target.checked)
                if (action == 'm_rota') {
                    ls.set(LS_ACTION_SET, 'rotacion')
                    modalTitle.innerHTML = 'Ingreso masivo de rotación por periodo ' + textMarcados
                }
                if (action == 'l_rota') {
                    ls.set(LS_ACTION_SET, 'rotacion')
                    modalTitle.innerHTML = `Ingreso de rotación por periodo<br><span class="font08 fw5">${nombre} (${legajo})</span>`
                }
                if (action == 'm_horale1') {
                    ls.set(LS_ACTION_SET, 'desde-hasta')
                    modalTitle.innerHTML = 'Ingreso masivo de horario por periodo' + textMarcados
                }
                if (action == 'l_horale1' || action == 'e_horale1') {
                    ls.set(LS_ACTION_SET, 'legajo-desde-hasta')
                    modalTitle.innerHTML = `Ingreso horario por periodo<br><span class="font08 fw5">${nombre} (${legajo})</span>`
                }
            } else {
                ls.set(LS_PERIODO, false)
                singleDatePicker('#inputH1FDesde', 'right', 'down')
                if (action == 'm_rota') {
                    ls.set(LS_ACTION_SET, 'rotacion')
                    modalTitle.innerHTML = 'Ingreso masivo de rotación desde una fecha' + textMarcados
                }
                if (action == 'l_rota') {
                    ls.set(LS_ACTION_SET, 'rotacion')
                    modalTitle.innerHTML = `Ingreso de rotación desde una fecha<br><span class="font08 fw5">${nombre} (${legajo})</span>`
                }
                if (action == 'm_horale1') {
                    ls.set(LS_ACTION_SET, 'desde')
                    modalTitle.innerHTML = 'Ingreso masivo de horario desde una fecha' + textMarcados
                }
                if (action == 'l_horale1' || action == 'e_horale1') {
                    ls.set(LS_ACTION_SET, 'desde')
                    modalTitle.innerHTML = `Ingreso horario desde una fecha<br><span class="font08 fw5">${nombre} (${legajo})</span>`
                }
            }
            remove_tr_selected(`${DT_TABLE_HORARIOS} tbody`);
            inputH1Codhor.value = '';
            inputH1horario.value = '';
            inputH1FDias.value = '1';
            submit.disabled = true;
            modalTitleHor();
        });
    }
    const textMarcadosModal = () => {
        const Marcados = ls.get(LS_MARCADOS) ?? {};
        const cantidadMarcados = Object.keys(Marcados).length;
        const textNoMarcados = `<br> <span class="font08 text-danger fw5">No hay Legajos Marcados. Se ingresará a todos según filtro aplicado.</span>`
        const textMarcados = cantidadMarcados > 0 ? `<br> <span class="font08 text-primary fw5">Legajos Marcados : (${cantidadMarcados})</span>` : textNoMarcados;
        return textMarcados;
    }
    const getModal = async (data) => {
        CheckSesion();
        const PERMISOS = getAcciones();

        const textMarcados = textMarcadosModal();

        if ($(`${ID_MODAL}`).hasClass('show')) {
            return false;
        }
        ls.remove(LS_ACTION_SET);
        const action = await ls.get(LS_ACTION);
        let dataLegajo = ls.get(LS_LEGAJO) || {};

        const mapAction = {
            'l_horale1': 'legajo-desde',
            'e_horale1': 'legajo-desde',
            'e_horale2': 'legajo-desde-hasta',
            'm_horale1': 'desde',
            'm_rota': 'rotacion',
            'm_cita': 'citacion',
            'l_cita': 'legajo-citacion',
            'l_rota': 'rotacion',
            'edit_horale1': 'legajo-desde',
            'edit_horale2': 'legajo-desde-hasta',
            'delete_horale1': 'delete-legajo-desde',
            'delete_horale2': 'delete-legajo-desde-hasta',
            'delete_citacion': 'delete-legajo-citacion',
            'edit_citacion': 'edit-legajo-citacion',
            'edit_rotacion': 'edit-legajo-rotacion',
            'delete_rotacion': 'delete-legajo-rotacion'
        }
        const nombre = dataLegajo?.pers_nombre ?? '';
        const legajo = dataLegajo?.pers_legajo ?? '';
        const mapTitle = {
            'l_horale1': `Ingreso horario desde una fecha<br><span class="font08 fw5">${nombre} (${legajo})</span>`,
            'e_horale1': `Editar horario asignado<br><span class="font08 fw5">${nombre} (${legajo})</span>`,
            'e_horale2': `Editar horario asignado<br><span class="font08 fw5">${nombre} (${legajo})</span>`,
            'm_horale1': `Ingreso masivo de horario desde una fecha. ${textMarcados}`,
            'm_rota': `Ingreso masivo de rotación desde una fecha. ${textMarcados}`,
            'm_cita': `Ingreso masivo de citaciones. ${textMarcados}`,
            'l_rota': `Ingreso de rotación desde una fecha<br><span class="font08 fw5">${nombre} (${legajo})</span>`,
            'l_cita': `Ingreso de citación<br><span class="font08 fw5">${nombre} (${legajo})</span>`,
            'edit_horale1': `Editar horario desde una fecha<br><span class="font08 fw5">${nombre} (${legajo})</span>`,
            'edit_horale2': `Editar horario por periodo<br><span class="font08 fw5">${nombre} (${legajo})</span>`,
            'delete_horale1': `¿Confirma eliminar asignación de horario?<br><span class="font08 fw5">${nombre} (${legajo})</span>`,
            'delete_horale2': `¿Confirma eliminar asignación de horario?<br><span class="font08 fw5">${nombre} (${legajo})</span>`,
            'delete_citacion': `¿Confirma eliminar citación?<br><span class="font08 fw5">${nombre} (${legajo})</span>`,
            'edit_citacion': `Editar citación<br><span class="font08 fw5">${nombre} (${legajo})</span>`,
            'edit_rotacion': `Editar rotación<br><span class="font08 fw5">${nombre} (${legajo})</span>`,
            'delete_rotacion': `¿Confirma eliminar rotación?<br><span class="font08 fw5">${nombre} (${legajo})</span>`,
        }

        const textTitle = mapTitle[action]; // setear el titulo del modal
        ls.set(LS_ACTION_SET, mapAction[action]); // setear el action set
        ls.set(LS_PERIODO, false); // resetear periodo en false
        // ls.set(LS_MODAL_ASIGN, ''); // resetear modal asign en ''
        const modal_horale1 = qs('#modal_horale1');
        modal_horale1.innerHTML = ls.get(LS_MODAL_ASIGN);
        const submit = qs('#submit');
        submit.classList.add('btn-custom');
        const modalAsignBody = qs('#modalAsignBody');
        modalAsignBody.hidden = false;

        const divPeriodo = qs('#divPeriodo');
        const inputCitacion = qs('#inputCitacion');

        divPeriodo.hidden = false;
        inputCitacion.style.display = 'none';

        const mapCita = {
            'm_cita': true,
            'l_cita': true,
        }

        if (mapCita[action]) {

            const arrayInputs = [
                '#inputH1FCitaEntra', // input de entrada
                '#inputH1FCitaSale', // input de salida
                '#inputH1FCitaDesc', // input de descanso
            ];

            selectInputsOnClick(arrayInputs)

            autoCompletarInputsHora(arrayInputs, '#inputH1FCitaHoras'); // auto completar los inputs y calcular horas

            divPeriodo.hidden = true; // ocultar el div periodo
            inputCitacion.style.display = 'block'; // mostrar el input de citacion

            submit.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopImmediatePropagation();

                let valueFecha = qs('#inputH1FDesde').value;
                valueFecha = valueFecha.split('/').reverse().join('-')

                const Procesar = qs('#Procesar');
                const formData = {
                    'Fecha': valueFecha,
                    'Procesar': Procesar.checked,
                    'Entr': qs('#inputH1FCitaEntra').value ?? '00:00',
                    'Sale': qs('#inputH1FCitaSale').value ?? '00:00',
                    'Desc': qs('#inputH1FCitaDesc').value ?? '00:00',
                }
                ls.set(LS_FORM_DATA, formData)
                setAsignación(formData)
            });
        }

        singleDatePicker('#inputH1FDesde', 'right', 'down') // setear el datepicker

        $(`${ID_MODAL}`).modal('show'); // mostrar el modal

        if (!PERMISOS['Proc']) { // si no tiene permisos para procesar ocultar el botón
            qs('.btn-procesar').hidden = true;
        }

        const divtableHorarios = qs('#divtableHorarios');
        const table = `
            <table id="tableHorarios" class="table table-sm text-nowrap mt-2 w-100 border radius table-hover bg-white"></table>
        `
        divtableHorarios.innerHTML = table;

        const modalTitle = qs(`${ID_MODAL} .modal-title`);
        const inputH1FDias = qs('#inputH1FDias');
        const HorarioEdit = qs('.HorarioEdit');

        $('#inputH1FDesde').mask('00/00/0000'); // setear el mask para el input
        modalTitle.innerHTML = textTitle; // setear el titulo del modal

        if (data) { // si se envío data
            let Fecha = '';
            let CodHor = data.Ho1Hora;
            let Horario = data.Ho1HoraStr;
            let FechaD = '';
            let FechaH = '';

            $('#inputH1FDesde').prop('disabled', true);
            qs('.divFecha').style.display = 'none';
            qs('.label-fecha').style.display = 'block';

            if (action == 'edit_horale1' || action == 'delete_horale1') {
                CodHor = data.Ho1Hora ?? '';
                Horario = data.Ho1HoraStr ?? '';
                Fecha = data.Ho1FechStr;
                $('#inputH1FDesde').data('daterangepicker').setStartDate(Fecha)
            }
            if (action == 'edit_horale2' || action == 'delete_horale2') {
                CodHor = data.Ho2Hora ?? '';
                Horario = data.Ho2HoraStr ?? '';
                FechaD = data.Ho2Fec1Str;
                FechaH = data.Ho2Fec2Str;
                Fecha = `${FechaD} al ${FechaH}`;
                ls.set(LS_PERIODO, true)
                dobleDatePicker('#inputH1FDesde', 'right', 'down')
                $('#inputH1FDesde').data('daterangepicker').setStartDate(Fecha)
            }
            if (action == 'delete_horale1') {
                divtableHorarios.hidden = true;
                submit.innerHTML = 'Eliminar';
                submit.classList.remove('btn-custom');
                submit.classList.add('btn-danger');
                submit.disabled = false;
                let valueFecha = document.querySelector('#inputH1FDesde').value;
                valueFecha = valueFecha.split('/').reverse().join('-');

                const Procesar = qs('#Procesar');
                submit.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    const formData = {
                        'Codhor': CodHor,
                        'Fecha': valueFecha,
                        'FechaD': '',
                        'FechaH': '',
                        'Procesar': Procesar.checked,
                    }
                    ls.set(LS_FORM_DATA, formData)
                    setAsignación(formData)
                })
            }
            if (action == 'delete_horale2') {
                divtableHorarios.hidden = true;
                submit.innerHTML = 'Eliminar';
                submit.classList.remove('btn-custom');
                submit.classList.add('btn-danger');
                submit.disabled = false;
                let valueFecha = document.querySelector('#inputH1FDesde').value;
                let valueFechaDesde = '';
                let valueFechaHasta = '';

                if (ls.get(LS_PERIODO)) {
                    valueFechaDesde = valueFecha.split(' ')[0].split('/').reverse().join('-');
                    valueFechaHasta = valueFecha.split(' ')[2].split('/').reverse().join('-');
                    valueFecha = valueFechaDesde;
                } else {
                    valueFecha = valueFecha.split('/').reverse().join('-');
                }

                const Procesar = qs('#Procesar');

                submit.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    const formData = {
                        'Codhor': CodHor,
                        'Fecha': valueFecha,
                        'FechaD': valueFechaDesde,
                        'FechaH': valueFechaHasta,
                        'Procesar': Procesar.checked,
                    }
                    ls.set(LS_FORM_DATA, formData)
                    setAsignación(formData)
                })
            }
            if (action == 'delete_citacion') {
                divtableHorarios.hidden = true;
                submit.innerHTML = 'Eliminar';
                submit.classList.remove('btn-custom');
                submit.classList.add('btn-danger');
                submit.disabled = false;
                Fecha = data.CitFechStr;
                let valueFecha = data.CitFechStr.split('/').reverse().join('-');

                const Procesar = qs('#Procesar');

                submit.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    const formData = {
                        'Codhor': '0',
                        'Fecha': valueFecha,
                        'Procesar': Procesar.checked,
                    }
                    ls.set(LS_FORM_DATA, formData)
                    setAsignación(formData)
                })
            }
            if (action == 'delete_rotacion') {
                divtableHorarios.hidden = true;
                submit.innerHTML = 'Eliminar';
                submit.classList.remove('btn-custom');
                submit.classList.add('btn-danger');
                submit.disabled = false;
                const Fecha = data.RolFechStr;
                const $Codi = data.RoLRota;
                let valueFecha = data.RolFechStr.split('/').reverse().join('-');

                const Procesar = qs('#Procesar');

                submit.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    const formData = {
                        'Codhor': $Codi,
                        'Fecha': valueFecha,
                        'Procesar': Procesar.checked,
                    }
                    ls.set(LS_FORM_DATA, formData)
                    setAsignación(formData)
                })
            }
            if (action == 'edit_citacion') {

                const arrayInputs = [
                    '#inputH1FCitaEntra', // input de entrada
                    '#inputH1FCitaSale', // input de salida
                    '#inputH1FCitaDesc', // input de descanso
                ];

                selectInputsOnClick(arrayInputs)

                autoCompletarInputsHora(arrayInputs, '#inputH1FCitaHoras'); // auto completar los inputs y calcular horas

                divPeriodo.hidden = true; // ocultar el div periodo
                inputCitacion.style.display = 'block'; // mostrar el input de citacion

                divtableHorarios.hidden = true;
                submit.innerHTML = 'Aceptar';
                submit.disabled = false;
                Fecha = data.CitFechStr;

                let valueFecha = data.CitFechStr.split('/').reverse().join('-');

                qs('#inputH1FCitaEntra').value = data.CitEntra;
                qs('#inputH1FCitaSale').value = data.CitSale;
                qs('#inputH1FCitaDesc').value = data.CitDesc;
                qs('#inputH1FCitaHoras').value = data.CitHoras;
                qs('#inputH1FCitaEntra').select();

                submit.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    const Procesar = qs('#Procesar');
                    const formData = {
                        'Codhor': '0',
                        'Desc': qs('#inputH1FCitaDesc').value,
                        'Entr': qs('#inputH1FCitaEntra').value,
                        'Sale': qs('#inputH1FCitaSale').value,
                        'Fecha': valueFecha,
                        'Procesar': Procesar.checked,
                    }
                    ls.set(LS_FORM_DATA, formData)
                    setAsignación(formData)
                })
            }
            if (action == 'edit_rotacion') {

                qs('#divDias').style.display = 'block';
                inputH1FDias.value = data.RoLDias;

                $('#inputH1FDesde').prop('disabled', true);
                qs('.divFecha').style.display = 'none';
                qs('.label-fecha').style.display = 'block';

                $('#inputH1FDias').mask('00');
                const FechaD = data.RolFechStr;
                const FechaH = data.RolVencStr;
                ls.set(LS_PERIODO, data.RolPeriodo);
                const FechaInput = (!data.RolPeriodo) ? FechaD : FechaD + ' al ' + FechaH;
                qs('#inputH1FDesde').value = FechaInput;
            }

            const titleEdit = `
                Desde: <span class="fw5">${Fecha}</span><br>
                Horario: <span class="fw5">(${CodHor}) ${Horario}</span>
            `
            HorarioEdit.innerHTML = titleEdit

            if (action == 'delete_citacion' || action == 'edit_citacion') {
                const cita = data.CitEntra + ' a ' + data.CitSale;
                const citaStr = data.CitDesc != '00:00' ? cita + ' (' + data.CitDesc + ')' : cita;
                const titleEditCitación = `
                    Fecha: <span class="fw5">${Fecha}</span><br>
                    Citación: <span class="fw5">${citaStr}</span>
                `
                HorarioEdit.innerHTML = titleEditCitación
            }

            if (action == 'edit_rotacion' || action == 'delete_rotacion') {
                const titleRota = `
                    Fecha: <span class="fw5">${data.RolFechStr} ${data.RolVencStr != '31/12/2099' ? ' al ' + data.RolVencStr : ''}</span><br>
                    Rotación: <span class="fw5">(${data.RoLRota}) ${data.RolRotaStr}</span>
                `
                HorarioEdit.innerHTML = titleRota
            }
        }

        if (action == 'm_rota' || action == 'l_rota') {
            const divDias = document.querySelector('#divDias');
            divDias.style.display = 'block';
            inputH1FDias.value = '1';
            $('#inputH1FDias').mask('00');
        }

        const mapMostrarHorarios = {
            'm_horale1': true,
            'l_horale1': true,
            'e_horale1': true,
            'edit_horale1': true,
            'm_rota': true,
            'l_rota': true,
            'edit_horale2': true,
            'edit_rotacion': true,
        }

        if (mapMostrarHorarios[action]) {
            accionesPeriodo(action); // acciones para cuando se marca el checkbox periodo
            dt_horarios(DT_TABLE_HORARIOS, action);
        }

    }
    get_horarios();
    const autoCompletarInputsHora = (elements, ElementResult) => {

        if (!elements || !ElementResult) return;

        const inputDeHorasCalculadas = document.querySelector(ElementResult);
        if (!inputDeHorasCalculadas) return;

        const mapAutoComplete = {
            1: '0:00',
            2: ':00',
            3: '00',
            4: '0'
        };

        const entrada = document.querySelector(elements[0]); // input de entrada
        const salida = document.querySelector(elements[1]); // input de salida
        const descanso = document.querySelector(elements[2]); // input de descanso
        if (!entrada || !salida || !descanso) return;

        entrada.value = ''; // resetear los inputs
        salida.value = ''; // resetear los inputs
        descanso.value = ''; // resetear los inputs

        setTimeout(() => {
            entrada.focus();
        }, 0);

        elements.forEach(element => {

            const input = document.querySelector(element);
            if (!input) return;

            $(element).mask(maskBehavior, spOptions); // setear la mascara en el input.

            input.addEventListener('blur', function (e) {
                const value = input.value;
                const length = value.length;

                if (length && length < 5) {
                    input.value += mapAutoComplete[length] || '';
                    e.preventDefault();
                }

                if (length === 5) {
                    input.value = input.value.slice(0, 5); // limitar a 5 caracteres para que no se desborde el input
                }

                submit.disabled = !(entrada.value.length === 5 && salida.value.length === 5); // deshabilitar el submit si los inputs no tienen 5 caracteres

                const inputEntrada = entrada.value ? entrada.value : '00:00'; // setear el valor del input
                const inputSalida = salida.value ? salida.value : '00:00'; // setear el valor del input
                const inputDescanso = descanso.value ? descanso.value : '00:00'; // setear el valor del input

                const horasCalculadas = calcularHorasTrabajadas(inputEntrada, inputSalida, inputDescanso); // calcular las horas trabajadas
                inputDeHorasCalculadas.value = horasCalculadas; // setear el valor del input horas
            });
        });
    }
    const select_filtros = () => {

        $('#Tipo').css({ "width": "200px" });
        $('.form-control').css({ "width": "100%" });

        function Select2Estruct(selector, multiple, placeholder, estruct, url, parent) {
            let opt2 = { MinLength: "0", SelClose: false, MaxInpLength: "10", delay: "250", allowClear: true };
            $(selector).select2({
                multiple: multiple,
                allowClear: opt2["allowClear"],
                language: "es",
                dropdownParent: parent,
                placeholder: placeholder,
                minimumInputLength: opt2["MinLength"],
                minimumResultsForSearch: 5,
                maximumInputLength: opt2["MaxInpLength"],
                selectOnClose: opt2["SelClose"],
                language: {
                    noResults: function () {
                        return 'No hay resultados..'
                    },
                    inputTooLong: function (args) {
                        var message = 'Máximo ' + opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
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
                        return 'Ingresar ' + opt2["MinLength"] + ' o mas caracteres'
                    },
                    maximumSelected: function () {
                        return 'Puede seleccionar solo una opción'
                    }
                },
                ajax: {
                    url: url,
                    dataType: "json",
                    type: "POST",
                    delay: opt2["delay"],
                    cache: false,
                    data: function (params) {
                        return {
                            q: params.term,
                            estruct: estruct,
                            Per: $("#Per").val(),
                            Tipo: $("#Tipo").val(),
                            Emp: $("#Emp").val(),
                            Plan: $("#Plan").val(),
                            Sect: $("#Sect").val(),
                            Sec2: $("#Sec2").val(),
                            Grup: $("#Grup").val(),
                            Sucur: $("#Sucur").val(),
                            Tare: $("#Tare").val(),
                            Conv: $("#Conv").val(),
                            Regla: $("#Regla").val(),
                        }
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        }
                    },
                },
            });
        }

        const url = "/" + $("#_homehost").val() + "/personal/getSelect/getEstruct.php";
        $('#Filtros').on('shown.bs.modal', function () {
            Select2Estruct(".selectjs_empresa", true, "Empresas", "Empr", url, $('#Filtros'));
            Select2Estruct(".selectjs_plantas", true, "Plantas", "Plan", url, $('#Filtros'));
            Select2Estruct(".selectjs_sectores", true, "Sectores", "Sect", url, $('#Filtros'));
            Select2Estruct(".select_seccion", true, "Secciones", "Sec2", url, $('#Filtros'));
            Select2Estruct(".selectjs_grupos", true, "Grupos", "Grup", url, $('#Filtros'));
            Select2Estruct(".selectjs_sucursal", true, "Sucursales", "Sucu", url, $('#Filtros'));
            Select2Estruct(".selectjs_personal", true, "Legajos", "Lega", url, $('#Filtros'));
            Select2Estruct(".selectjs_tipoper", false, "Tipo de Personal", "Tipo", url, $('#Filtros'));
            Select2Estruct(".selectjs_tareProd", true, "Taras de Producción", "Tare", url, $('#Filtros'));
            Select2Estruct(".selectjs_conv", true, "Convenio", "Conv", url, $('#Filtros'));
            Select2Estruct(".selectjs_regla", true, "Regla de control", "Regla", url, $('#Filtros'));

            $('.selectjs_sectores').on('select2:select', function (e) {
                $(".select_seccion").prop("disabled", false);
                $('.select_seccion').val(null).trigger('change');
                var nombresector = $('.selectjs_sectores :selected').text();
                $("#DatosFiltro").html('Sector: ' + nombresector);
            });
            $('.selectjs_sectores').on('select2:unselecting', function (e) {
                $(".select_seccion").prop("disabled", true);
                $('.select_seccion').val(null).trigger('change');
            });
            $('.selectjs_personal').on('select2:select', function (e) {
            });
        });

        function LimpiarFiltros() {
            $('.selectjs_plantas').val(null).trigger("change");
            $('.selectjs_empresa').val(null).trigger("change");
            $('.selectjs_sectores').val(null).trigger("change");
            $('.select_seccion').val(null).trigger("change");
            $(".select_seccion").prop("disabled", true);
            $('.selectjs_grupos').val(null).trigger("change");
            $('.selectjs_sucursal').val(null).trigger("change");
            $('.selectjs_personal').val(null).trigger("change");
            $('.selectjs_tipoper').val(null).trigger("change");
            $('.selectjs_tareProd').val(null).trigger("change");
            $('.selectjs_conv').val(null).trigger("change");
            $('.selectjs_regla').val(null).trigger("change");
        }
        function LimpiarFiltros2() {
            $('.selectjs_plantas').val(null).trigger("change");
            $('.selectjs_empresa').val(null).trigger("change");
            $('.selectjs_sectores').val(null).trigger("change");
            $('.select_seccion').val(null).trigger("change");
            $(".select_seccion").prop("disabled", true);
            $('.selectjs_grupos').val(null).trigger("change");
            $('.selectjs_sucursal').val(null).trigger("change");
            $('.selectjs_personal').val(null).trigger("change");
            $('.selectjs_tipoper').val(null).trigger("change");
            $('.selectjs_tareProd').val(null).trigger("change");
            $('.selectjs_conv').val(null).trigger("change");
            $('.selectjs_regla').val(null).trigger("change");
        }
        $("#trash_all").on("click", function () {
            $('#Filtros').modal('show')
            LimpiarFiltros()
            $('#Filtros').modal('hide')
        });

        $("#trash_allIn").on("click", function () {
            LimpiarFiltros()
        });
        $('#Filtros').on('hidden.bs.modal', function (e) {
            // $('#tablePersonal').DataTable().ajax.reload();
            dt_tablePersonal();
        });
    }
    axios.get('modal_Filtros.html' + '?t=' + new Date().getTime()).then(function (response) {
        const divModal = document.getElementById('divModal');
        divModal.innerHTML = response.data;
        select_filtros();
    }).catch(function (error) {
        console.error(error);
    });
});