(function ($) {
    'use strict';

    $(document).ready(function () {
        bindDayChecksSync();
        bindCopyDayAction();
        bindDayTimeValidation();
        bindHeaderFieldsValidation();
        bindSaveHorarioAction();
        initTablaHorarios();
    });

    const homehost = $("#_homehost").val();
    const ENDPOINT_HORARIOS = '/' + homehost + '/app-data/datatables/horarios';
    const ENDPOINT_HORARIO = '/' + homehost + '/app-data/horario';
    const ENDPOINT_HORARIO_EXPORT_XLS = '/' + homehost + '/app-data/horario/exportar-xls';
    const ENDPOINT_HORARIO_EXPORT_XLS_EJEMPLO = '/' + homehost + '/app-data/horario/exportar-xls-ejemplo';
    const ENDPOINT_HORARIO_IMPORT_XLS = '/' + homehost + '/app-data/horario/importar-xls';
    const ENDPOINT_HORARIO_UNUSED = '/' + homehost + '/app-data/horario/unused';
    const LS_KEY_ORDERBY = homehost + '.configuracion.horarios.ordenarPor';

    const $modalHorario = $('#modal-horario');
    const $modalImportarHorarios = $('#modal-importar-horarios');
    const $modalUnusedHorarios = $('#modal-unused-horarios');
    let tablaHorarios = null;
    let tableHorariosUnused = null;
    let tablaUnused = null;
    let editingHorCodi = null;
    let saveButtonDefaultText = 'Guardar';

    const MAP_DIAS_MODAL = [
        { key: 'Lunes', dayId: 'HorLune', prefix: 'HorLu', shortId: 'Lu', control: 'checkbox' },
        { key: 'Martes', dayId: 'HorMart', prefix: 'HorMa', shortId: 'Ma', control: 'checkbox' },
        { key: 'Miércoles', dayId: 'HorMier', prefix: 'HorMi', shortId: 'Mi', control: 'checkbox' },
        { key: 'Jueves', dayId: 'HorJuev', prefix: 'HorJu', shortId: 'Ju', control: 'checkbox' },
        { key: 'Viernes', dayId: 'HorVier', prefix: 'HorVi', shortId: 'Vi', control: 'checkbox' },
        { key: 'Sábado', dayId: 'HorSaba', prefix: 'HorSa', shortId: 'Sa', control: 'checkbox' },
        { key: 'Domingo', dayId: 'HorDomi', prefix: 'HorDo', shortId: 'Do', control: 'checkbox' },
        { key: 'Feriado', dayId: 'HorFeri', prefix: 'HorFe', shortId: 'Fe', control: 'select' },
    ];

    const MAP_HTML_EXPORT = [
        { id: 'btn-exportar-xls', text: 'Exportar .xls' },
        { id: 'btn-importar-xls', text: 'Importar .xls' },
        { id: 'btn-eliminar-unused', text: 'Eliminar no asignados' },
    ];

    const DAY_ORDER = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

    // Definición de días de la semana con su clave y abreviatura
    const DIAS = [
        { key: 'Lunes', title: 'Lun' },
        { key: 'Martes', title: 'Mar' },
        { key: 'Miércoles', title: 'Mié' },
        { key: 'Jueves', title: 'Jue' },
        { key: 'Viernes', title: 'Vie' },
        { key: 'Sábado', title: 'Sáb' },
        { key: 'Domingo', title: 'Dom' },
        { key: 'Feriado', title: 'Fer' },
    ];

    const MAP_ORDENAR_POR = {
        'Codi': { column: 'Codigo Horario', dir: 'asc' },
        'Fecha': { column: 'Fecha Editado / Creado', dir: 'desc' },
        'ID': { column: 'ID Horario', dir: 'desc' },
        'Desc': { column: 'Descripción Horario', dir: 'asc' },
    };

    const getDefaultOrderBy = () => 'Fecha';

    const normalizeOrderBy = (value) => {
        const key = String(value ?? '').trim();
        return Object.prototype.hasOwnProperty.call(MAP_ORDENAR_POR, key) ? key : getDefaultOrderBy();
    };

    const getStoredOrderBy = () => {
        try {
            const value = ls.get(LS_KEY_ORDERBY);
            return normalizeOrderBy(value);
        } catch (e) {
            return getDefaultOrderBy();
        }
    };

    const setStoredOrderBy = (value) => {
        const normalized = normalizeOrderBy(value);
        try {
            ls.set(LS_KEY_ORDERBY, normalized);
        } catch (e) {
            // noop: si localStorage falla, se mantiene solo en memoria de la vista.
        }
        return normalized;
    };

    // Renderiza Desde - Hasta de cada día
    const renderDia = (dia, Color, ColorText, title) => {
        if (!dia) return '';
        const laboral = dia.LaboralID >= 1;
        const style = laboral ? `style="border: 1px solid #727171; color: #333; background-color: #f8f9fa;"` : '';
        const classLaboral = laboral ? 'LaboralID' : '';
        const Horas = laboral ? dia.Horas + ' hs' : 'Franco';
        const tittleDay = `<span class="font08 text-secondary">${title}</span>`;
        return `<div class="p-1">
            <div class="p-1">${tittleDay}<br>${dia.Desde}<br>${dia.Hasta}<br><span class="font08"><span class="${classLaboral}" ${style}>${Horas}</span></span></div>
        </div>`;
    };

    // Renderiza badge con el color del horario
    const renderColorBadge = (row) => {
        return `<span class="badge radius w10 h10" style="background-color:${row.Color}; color:${row.ColorText};">&nbsp;</span>`;
    };
    const renderHorasSemanales = (data, row) => {
        const style = `style="border: 1px solid #727171; color: #333; background-color: #f8f9fa;"`;
        return `<div class="p-1"><span class="font08 text-secondary">Horas</span><br><span class="LaboralID" ${style}>${data} hs</span></div>`;
    };

    const buildColumns = () => {
        const colCodi = {
            data: 'Codi', className: 'text-center', targets: '', title: 'Cód',
            visible: false,
            render: function (data, type) { if (type !== 'display') return ''; return data; },
        };
        const colDesc = {
            data: 'Desc', className: '', targets: '', title: 'Descripción',
            visible: false,
            render: function (data, type) { if (type !== 'display') return ''; return data; },
        };
        const colColor = {
            data: '', className: 'text-center', targets: '', title: 'Color',
            orderable: false,
            visible: false,
            render: function (data, type, row) { if (type !== 'display') return ''; return renderColorBadge(row); },
        };
        const colHorasSemanales = {
            data: 'HorasSem', className: 'text-center', targets: '', title: '',
            render: function (data, type, row) {
                if (type !== 'display') return '';
                return renderHorasSemanales(data, type, row);
            },
        };
        const colsDias = DIAS.map(function ({ key, title }) {
            return {
                data: key, className: 'text-center', targets: '', title: '',
                orderable: false,
                render: function (data, type, row) {
                    if (type !== 'display') return '';
                    return renderDia(data, row.Color, row.ColorText, title);
                },
                createdCell: function (td, cellData) {
                    if (cellData && cellData.LaboralID !== 1) {
                        $(td).addClass('text-secondary');
                    }
                },
            };
        });
        return [colCodi, colDesc, colColor, ...colsDias, colHorasSemanales];
    };

    const renderRowGroup = (row) => {
        return `
            <div class="d-flex justify-content-between align-items-center pt-3 px-2 m-0">
                <div class="d-flex justify-content-strat align-items-center gap5">
                    <span class="badge radius w50 mr-1" style="background-color:${row.Color}; color:${row.ColorText};">${row.ID || '&nbsp;'} </span>
                    <strong>(${row.Codi}) ${row.Desc}</strong>
                </div>
                <div class="border p-1 rounded d-flex justify-content-center align-items-center gap5">
                    <button class="hint--top btn btn-sm btn-outline-custom border-0 btn-editar-horario" data-codi="${row.Codi}" title="Editar" aria-label="Editar"><i class="bi bi-pen"></i></button>
                    <button class="hint--top btn btn-sm btn-outline-custom border-0 btn-duplicar-horario" data-codi="${row.Codi}" title="Duplicar" aria-label="Duplicar Horario"><i class="bi bi-copy"></i></button>
                    <button class="hint--top btn btn-sm btn-outline-custom border-0 btn-eliminar-horario" data-codi="${row.Codi}" title="Eliminar" aria-label="Eliminar"><i class="bi bi-trash"></i></button>
                </div>
            </div>`;
    };

    const getDataRowByCodi = (table, codi) => {
        const rows = table.rows().data().toArray();
        const selectedCodi = String(codi ?? '');
        return rows.find(function (row) {
            return String(row.Codi ?? '') === selectedCodi;
        }) || null;
    };

    const setModalValue = (id, value) => {
        const $el = $modalHorario.find(`#${id}`);
        if (!$el.length) return;
        $el.val(value ?? '');
    };

    const setModalDisabled = (id, disabled) => {
        const $el = $modalHorario.find(`#${id}`);
        if (!$el.length) return;
        $el.prop('disabled', !!disabled);
    };

    const setSaveButtonText = (text) => {
        const $btn = $modalHorario.find('#btn-save');
        if ($btn.length) {
            $btn.text(text);
        }
    };

    const resetModalForCreate = () => {
        editingHorCodi = null;
        setModalDisabled('HorCodi', true);

        setModalValue('HorCodi', '');
        setModalValue('HorDesc', '');
        setModalValue('HorID', '');
        setModalValue('HorColor', '#000000');

        const $horColor = $modalHorario.find('#HorColor');
        if ($horColor.length && $horColor[0].jscolor && typeof $horColor[0].jscolor.fromString === 'function') {
            $horColor[0].jscolor.fromString('#000000');
        }

        MAP_DIAS_MODAL.forEach(function (item) {
            setDayLaboralValue(item, 0);
            setModalValue(`${item.prefix}De`, '00:00');
            setModalValue(`${item.prefix}Ha`, '00:00');
            setModalValue(`${item.prefix}Re`, '00:00');
            setModalValue(`${item.prefix}Hs`, '00:00');
            setModalValue(`${item.prefix}Li`, '0');
        });

        renderTimelinesOnModalOpen();
    };

    const applyRowDataToModal = (data, opts = {}) => {
        const options = {
            clearDesc: false,
            keepCurrentCode: false,
            ...opts,
        };

        setModalValue('HorDesc', options.clearDesc ? '' : (data.Desc ?? ''));
        setModalValue('HorID', sanitizeHorarioId(data.ID ?? ''));

        const $horColor = $modalHorario.find('#HorColor');
        if ($horColor.length) {
            const colorHex = normalizeColorHex(data.Color);
            $horColor.val(colorHex);
            if ($horColor[0].jscolor && typeof $horColor[0].jscolor.fromString === 'function') {
                $horColor[0].jscolor.fromString(colorHex);
            }
        }

        if (!options.keepCurrentCode) {
            setModalValue('HorCodi', sanitizeHorCodi(data.Codi ?? ''));
        }

        MAP_DIAS_MODAL.forEach(function (item) {
            const dia = data[item.key] || {};
            const laboral = Number(dia.LaboralID ?? 0);

            setDayLaboralValue(item, laboral);
            setModalValue(`${item.prefix}De`, dia.Desde ?? '00:00');
            setModalValue(`${item.prefix}Ha`, dia.Hasta ?? '00:00');
            setModalValue(`${item.prefix}Re`, dia.Descanso ?? '00:00');
            setModalValue(`${item.prefix}Li`, dia.Limite ?? '0');
            setModalValue(`${item.prefix}Hs`, dia.Horas ?? '00:00');
        });

        renderTimelinesOnModalOpen();
    };

    const fetchNextHorarioCodi = async () => {
        if (!window.axios || typeof window.axios.post !== 'function') {
            throw new Error('No se encontró axios para consultar el ultimo codigo');
        }

        const req = {
            LastCodi: 1,
            start: 0,
            length: 1,
            search: { value: '' },
        };

        const res = await window.axios.post(ENDPOINT_HORARIOS, req, {
            headers: {
                'Content-Type': 'application/json',
            },
        });

        const data = res?.data?.data || 0;

        const lastCodi = Number(data ?? 0);
        const nextCodi = Number.isFinite(lastCodi) && lastCodi >= 0 ? (lastCodi + 1) : 1;
        return sanitizeHorCodi(nextCodi);
    };

    const openCreateHorarioModal = async (sourceRow = null) => {
        initFeriadoSelect();
        initHorColorPicker();

        saveButtonDefaultText = 'Agregar';
        setSaveButtonText(saveButtonDefaultText);

        $modalHorario.find('.modal-title').text(sourceRow ? 'Duplicar Horario' : 'Nuevo Horario');
        resetModalForCreate();

        if (sourceRow) {
            applyRowDataToModal(sourceRow, { clearDesc: true, keepCurrentCode: true });
        }

        $modalHorario.modal('show');

        try {
            const nextCode = await fetchNextHorarioCodi();
            setModalValue('HorCodi', nextCode);
        } catch (err) {
            notify(err?.message || 'No se pudo obtener el ultimo codigo', 'danger', 4000, 'right');
            setModalValue('HorCodi', '');
        } finally {
            // En alta queda editable para que el usuario pueda cambiarlo manualmente.
            setModalDisabled('HorCodi', false);
            // HorDesc hacer autofocus para mejorar usabilidad en creación.
            const $descInput = $modalHorario.find('#HorDesc');
            if ($descInput.length) {
                $descInput.focus();
            }
        }
    };

    const setModalChecked = (id, checked) => {
        const $el = $modalHorario.find(`#${id}`);
        if (!$el.length) return;
        const shouldCheck = !!checked;
        $el.prop('checked', shouldCheck);

        // Sincroniza estado visual del btn-group-toggle sin disparar eventos extra.
        const $btnLabel = $el.closest('label.btn');
        if ($btnLabel.length) {
            $btnLabel.toggleClass('active', shouldCheck);
            $btnLabel.attr('aria-pressed', shouldCheck ? 'true' : 'false');
        }
    };

    const setDayLaboralValue = (item, laboralValue) => {
        const normalized = Math.max(0, Math.min(3, Number(laboralValue) || 0));
        const isActive = normalized > 0;

        if (item.control === 'select') {
            const $select = $modalHorario.find(`#${item.dayId}`);
            if ($select.length) {
                const next = String(normalized);
                if (String($select.val() ?? '') !== next) {
                    $select.val(next).trigger('change.select2');
                }
            }
        } else {
            setModalChecked(item.dayId, isActive);
        }

        setModalChecked(item.shortId, isActive);
    };

    const parseTimeToMinutes = (time) => {
        if (!time || typeof time !== 'string' || !time.includes(':')) return null;
        const [hRaw, mRaw] = time.split(':');
        const h = Number(hRaw);
        const m = Number(mRaw);
        if (!Number.isFinite(h) || !Number.isFinite(m)) return null;
        if (h < 0 || h > 23 || m < 0 || m > 59) return null;
        return (h * 60) + m;
    };

    const formatMinutesToTime = (minutes) => {
        const safe = Math.max(0, Math.min(1439, Number(minutes) || 0));
        const h = Math.floor(safe / 60);
        const m = safe % 60;
        return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
    };

    const isValidTimeString = (value) => {
        if (typeof value !== 'string') return false;
        return /^(?:[01]\d|2[0-3]):[0-5]\d$/.test(value.trim());
    };

    const notifyInvalidField = (label) => {
        notify(`Formato invalido en ${label}. Use HH:mm`, 'danger', 3500, 'right');
    };

    const normalizeEmptyTimeFieldById = (id) => {
        const $el = $modalHorario.find(`#${id}`);
        if (!$el.length) return '';
        const current = String($el.val() ?? '').trim();
        if (current === '') {
            $el.val('00:00');
            return '00:00';
        }
        return current;
    };

    const normalizeTimeFieldById = (id) => {
        const $el = $modalHorario.find(`#${id}`);
        if (!$el.length) return '';

        const partial = normalizePartialTimeValue($el.val());
        if (partial !== $el.val()) {
            $el.val(partial);
        }

        return normalizeEmptyTimeFieldById(id);
    };

    const normalizeLimitFieldById = (id, label) => {
        const $el = $modalHorario.find(`#${id}`);
        if (!$el.length) return false;

        const raw = String($el.val() ?? '').trim();
        if (raw === '') {
            $el.val('0');
            return true;
        }

        const parsed = Number(raw);
        if (!Number.isFinite(parsed)) {
            $el.val('0');
            notify(`Valor invalido en ${label}. Debe estar entre 0 y 99`, 'danger', 3500, 'right');
            return false;
        }

        const intValue = Math.trunc(parsed);
        const clamped = Math.max(0, Math.min(99, intValue));
        if (clamped !== intValue) {
            notify(`Valor fuera de rango en ${label}. Se ajustó entre 0 y 99`, 'danger', 3500, 'right');
        }

        $el.val(String(clamped));
        return clamped === intValue;
    };

    const normalizePartialTimeValue = (value) => {
        const str = String(value ?? '').trim();
        const mapAutoComplete = {
            1: '0:00',
            2: ':00',
            3: '00',
            4: '0',
        };
        if (!str || str.length >= 5) return str;
        return `${str}${mapAutoComplete[str.length] || ''}`;
    };

    const validateFieldTimeById = (id, label) => {
        const value = normalizeTimeFieldById(id);
        if (!isValidTimeString(value)) {
            notifyInvalidField(label);
            return false;
        }
        return true;
    };

    const calculateWorkedMinutes = (desde, hasta) => {
        const deMin = parseTimeToMinutes(desde);
        const haMin = parseTimeToMinutes(hasta);

        if (deMin === null || haMin === null) {
            return { ok: false, error: 'Formato de hora invalido' };
        }

        if (deMin === haMin) {
            return { ok: false, error: 'Desde y Hasta no pueden ser iguales' };
        }

        const crossesDay = deMin > haMin;
        const rawDuration = crossesDay ? ((1440 - deMin) + haMin) : (haMin - deMin);

        if (rawDuration <= 0 || rawDuration > 1439) {
            return { ok: false, error: 'La duracion debe ser menor a 24 horas' };
        }

        const worked = rawDuration;
        return { ok: true, minutes: worked };
    };

    const getNextDayName = (dayName) => {
        const idx = DAY_ORDER.indexOf(dayName);
        if (idx === -1) return dayName;
        return DAY_ORDER[(idx + 1) % DAY_ORDER.length];
    };

    const renderTimelineContainer = (opts) => {
        const STEP_MINUTES = 60; // 1h por marca base
        const buildBaseHours = () => Array.from({ length: 25 }, function (_, i) {
            const label = (i % 3 === 0 || i === 24) ? String(i) : '';
            return `<div class="timeline-hour"><span>${label}</span></div>`;
        }).join('');

        const startMin = parseTimeToMinutes(opts.desde);
        const endMin = parseTimeToMinutes(opts.hasta);

        // Si no hay datos válidos, render base sin barras.
        if (startMin === null || endMin === null) {
            return buildBaseHours();
        }

        // Si ambos son 00:00, se considera "sin tramo" y no se pinta barra.
        if (startMin === 0 && endMin === 0) {
            return buildBaseHours();
        }

        const isOvernight = endMin <= startMin;
        const endAbs = isOvernight ? (1440 + endMin) : endMin;
        const axisEndMinutes = isOvernight
            ? Math.ceil(endAbs / STEP_MINUTES) * STEP_MINUTES
            : 1440;

        // timeline-hour dibuja marcas por "celdas" (incluyendo la ultima marca),
        // por eso el eje visual es una celda mas que el eje logico en minutos.
        const visualAxisMinutes = axisEndMinutes + STEP_MINUTES;

        const toLeft = (minutes) => ((minutes / visualAxisMinutes) * 100);
        const toWidth = (minutes) => ((minutes / visualAxisMinutes) * 100);

        // Barra unica continua sobre un eje extendido (cuando cruza de dia).
        const duration = Math.max(0, endAbs - startMin);
        const barHtml = `<div class="timeline-bar" style="left:${toLeft(startMin)}%; width:${toWidth(duration)}%;"></div>`;

        const nextDayName = getNextDayName(opts.dayName);
        const hours = [];
        for (let minute = 0; minute <= axisEndMinutes; minute += STEP_MINUTES) {
            const hourOfDay = Math.floor((minute % 1440) / 60);
            let label = '';
            if (isOvernight && minute === 1440) {
                label = `0<span class="timeline-next-day">${nextDayName}</span>`;
            } else if (!isOvernight && minute === axisEndMinutes) {
                label = '24';
            } else if (minute % 180 === 0 || minute === axisEndMinutes) {
                label = String(hourOfDay);
            }
            hours.push(`<div class="timeline-hour"><span>${label}</span></div>`);
        }

        return `${barHtml}${hours.join('')}`;
    };

    const renderTimelineForModalDay = (item) => {
        const $fromInput = $modalHorario.find(`#${item.prefix}De`);
        if (!$fromInput.length) return;

        const $card = $fromInput.closest('.config-card');
        if (!$card.length) return;

        const $timeline = $card.find('.timeline-container').first();
        if (!$timeline.length) return;

        const desde = $modalHorario.find(`#${item.prefix}De`).val();
        const hasta = $modalHorario.find(`#${item.prefix}Ha`).val();

        $timeline.html(renderTimelineContainer({
            dayName: item.key,
            desde: String(desde ?? ''),
            hasta: String(hasta ?? ''),
        }));
    };

    const renderTimelinesOnModalOpen = () => {
        MAP_DIAS_MODAL.forEach(function (item) {
            renderTimelineForModalDay(item);
        });
    };

    const bindDayChecksSync = () => {
        // Evita registros duplicados de eventos en reinicializaciones.
        $modalHorario.off('change.syncDayChecks');

        MAP_DIAS_MODAL.forEach(function (item) {
            $modalHorario.on('change.syncDayChecks', `#${item.shortId}`, function () {
                const checked = $(this).is(':checked');
                if (item.control === 'select') {
                    const $select = $modalHorario.find(`#${item.dayId}`);
                    if ($select.length) {
                        const nextValue = checked ? '1' : '0';
                        if (String($select.val() ?? '') !== nextValue) {
                            $select.val(nextValue).trigger('change.select2');
                        }
                    }
                } else {
                    setModalChecked(item.dayId, checked);
                }
            });

            $modalHorario.on('change.syncDayChecks', `#${item.dayId}`, function () {
                if (item.control === 'select') {
                    const selected = Number($(this).val() ?? 0);
                    setModalChecked(item.shortId, selected > 0);
                } else {
                    const checked = $(this).is(':checked');
                    setModalChecked(item.shortId, checked);
                }
            });
        });
    };

    const bindCopyDayAction = () => {
        $modalHorario.off('click.copyDay');

        $modalHorario.on('click.copyDay', '.btn-copy-day', function () {
            const $card = $(this).closest('.config-card');
            if (!$card.length) return;

            const sourceDayId = $card.find('input.custom-control-input[id^="Hor"], select[id^="Hor"]').first().attr('id');
            if (!sourceDayId) return;

            const sourceItem = MAP_DIAS_MODAL.find(function (item) {
                return item.dayId === sourceDayId;
            });
            if (!sourceItem) return;

            const sourceLaboralValue = sourceItem.control === 'select'
                ? Number($modalHorario.find(`#${sourceItem.dayId}`).val() ?? 0)
                : ($modalHorario.find(`#${sourceItem.dayId}`).is(':checked') ? 1 : 0);
            const sourceDesde = $modalHorario.find(`#${sourceItem.prefix}De`).val();
            const sourceHasta = $modalHorario.find(`#${sourceItem.prefix}Ha`).val();
            const sourceDescanso = $modalHorario.find(`#${sourceItem.prefix}Re`).val();
            const sourceHoras = $modalHorario.find(`#${sourceItem.prefix}Hs`).val();
            const sourceLimite = $modalHorario.find(`#${sourceItem.prefix}Li`).val();

            MAP_DIAS_MODAL.forEach(function (item) {
                setDayLaboralValue(item, sourceLaboralValue);
                setModalValue(`${item.prefix}De`, sourceDesde);
                setModalValue(`${item.prefix}Ha`, sourceHasta);
                setModalValue(`${item.prefix}Re`, sourceDescanso);
                setModalValue(`${item.prefix}Hs`, sourceHoras);
                setModalValue(`${item.prefix}Li`, sourceLimite);
            });

            // Al copiar, refresca todos los timelines del modal.
            renderTimelinesOnModalOpen();

            notify('Valores copiados a todos los dias', 'success', 2500, 'right');
        });
    };

    const bindDayTimeValidation = () => {
        // Mascara HH:mm para De/Ha/Re/Hs
        const maskBehavior = function (val) {
            val = String(val || '').split(':');
            return parseInt(val[0], 10) > 19 ? 'HZ:M0' : 'H0:M0';
        };

        const maskOptions = {
            onKeyPress: function (val, e, field, options) {
                field.mask(maskBehavior.apply({}, arguments), options);
            },
            translation: {
                H: { pattern: /[0-2]/, optional: false },
                Z: { pattern: /[0-3]/, optional: false },
                M: { pattern: /[0-5]/, optional: false },
            },
        };

        MAP_DIAS_MODAL.forEach(function (item) {
            const ids = [`#${item.prefix}De`, `#${item.prefix}Ha`, `#${item.prefix}Re`, `#${item.prefix}Hs`];
            ids.forEach(function (selector) {
                if (typeof $(selector).mask === 'function') {
                    if (typeof $(selector).unmask === 'function') {
                        $(selector).unmask();
                    }
                    $(selector).mask(maskBehavior, maskOptions);
                }
            });
        });

        $modalHorario.off('change.timeValidation blur.timeValidation');

        MAP_DIAS_MODAL.forEach(function (item) {
            const deId = `${item.prefix}De`;
            const haId = `${item.prefix}Ha`;
            const reId = `${item.prefix}Re`;
            const hsId = `${item.prefix}Hs`;
            const liId = `${item.prefix}Li`;
            const labelDe = `${item.key} - Desde`;
            const labelHa = `${item.key} - Hasta`;
            const labelRe = `${item.key} - Descanso`;
            const labelHs = `${item.key} - Horas`;
            const labelLi = `${item.key} - Limite (%)`;

            const validateAndRecalc = () => {
                const deOk = validateFieldTimeById(deId, labelDe);
                if (!deOk) {
                    renderTimelineForModalDay(item);
                    return;
                }

                const haOk = validateFieldTimeById(haId, labelHa);
                if (!haOk) {
                    renderTimelineForModalDay(item);
                    return;
                }

                // Timeline solo se redibuja al cambiar De/Ha.
                renderTimelineForModalDay(item);

                const desde = String($modalHorario.find(`#${deId}`).val() ?? '').trim();
                const hasta = String($modalHorario.find(`#${haId}`).val() ?? '').trim();

                const calc = calculateWorkedMinutes(desde, hasta);
                if (!calc.ok) {
                    notify(calc.error, 'danger', 4000, 'right');
                    return;
                }

                setModalValue(hsId, formatMinutesToTime(calc.minutes));
            };

            // Si el usuario tabula o sale del input sin disparar change,
            // recalcula igualmente para no dejar Hs desactualizadas.
            $modalHorario.on('blur.timeValidation', `#${deId}, #${haId}`, validateAndRecalc);

            $modalHorario.on('blur.timeValidation', `#${deId}, #${haId}, #${reId}, #${hsId}`, function () {
                const id = this.id;
                const normalized = normalizePartialTimeValue($(this).val());
                if (normalized !== $(this).val()) {
                    setModalValue(id, normalized);
                }
                normalizeTimeFieldById(id);
            });

            $modalHorario.on('blur.timeValidation', `#${reId}`, function () {
                validateFieldTimeById(reId, labelRe);
            });

            $modalHorario.on('blur.timeValidation', `#${hsId}`, function () {
                validateFieldTimeById(hsId, labelHs);
            });

            $modalHorario.on('blur.timeValidation', `#${liId}`, function () {
                normalizeLimitFieldById(liId, labelLi);
            });
        });
    };

    const sanitizeHorarioId = (value) => {
        return String(value ?? '')
            .replace(/[^a-zA-Z0-9]/g, '')
            .slice(0, 3);
    };

    const sanitizeHorCodi = (value) => {
        const digits = String(value ?? '').replace(/\D/g, '');
        if (digits === '') return '';
        const parsed = Number(digits);
        const safe = Math.max(0, Math.min(32767, parsed));
        return String(safe);
    };

    const bindHeaderFieldsValidation = () => {
        const $horCodi = $modalHorario.find('#HorCodi');
        const $horId = $modalHorario.find('#HorID');
        if ($horCodi.length) {
            $horCodi.off('input.horCodi blur.horCodi');

            $horCodi.on('input.horCodi', function () {
                const sanitized = sanitizeHorCodi(this.value);
                if (sanitized !== this.value) {
                    this.value = sanitized;
                }
            });

            $horCodi.on('blur.horCodi', function () {
                this.value = sanitizeHorCodi(this.value);
            });
        }

        if (!$horId.length) return;

        $horId.off('input.horarioId blur.horarioId');

        $horId.on('input.horarioId', function () {
            const sanitized = sanitizeHorarioId(this.value);
            if (sanitized !== this.value) {
                this.value = sanitized;
            }
        });

        $horId.on('blur.horarioId', function () {
            this.value = sanitizeHorarioId(this.value);
        });
    };

    const initFeriadoSelect = () => {
        const $feriado = $modalHorario.find('#HorFeri');
        if (!$feriado.length) return;
        if (!$feriado.data('select2')) {
            select2Simple('#HorFeri', 'seleccionar', false, false, '200px');
        }
    };

    const normalizeColorHex = (value) => {
        const raw = String(value ?? '').trim();
        if (!raw) return '#000000';
        if (/^#[0-9a-fA-F]{6}$/.test(raw)) return raw;
        if (/^[0-9a-fA-F]{6}$/.test(raw)) return `#${raw}`;

        const rgbMatch = raw.match(/^rgba?\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})(?:\s*,\s*(?:0|1|0?\.\d+))?\s*\)$/i);
        if (rgbMatch) {
            const toHex = (n) => {
                const safe = Math.max(0, Math.min(255, Number(n) || 0));
                return safe.toString(16).padStart(2, '0');
            };
            const r = toHex(rgbMatch[1]);
            const g = toHex(rgbMatch[2]);
            const b = toHex(rgbMatch[3]);
            return `#${r}${g}${b}`;
        }

        return '#000000';
    };

    const getDayLaboralValue = (item) => {
        if (item.control === 'select') {
            const raw = Number($modalHorario.find(`#${item.dayId}`).val() ?? 0);
            return String(Math.max(0, Math.min(3, raw)));
        }
        return $modalHorario.find(`#${item.dayId}`).is(':checked') ? '1' : '0';
    };

    const getPayloadHorario = () => {
        const horCodiValue = editingHorCodi !== null
            ? sanitizeHorCodi(editingHorCodi)
            : sanitizeHorCodi($modalHorario.find('#HorCodi').val());

        const payload = {
            HorCodi: Number(horCodiValue || 0),
            HorDesc: String($modalHorario.find('#HorDesc').val() ?? '').trim(),
            HorID: sanitizeHorarioId($modalHorario.find('#HorID').val()),
            HorColor: normalizeColorHex($modalHorario.find('#HorColor').val()),
        };

        MAP_DIAS_MODAL.forEach(function (item) {
            payload[item.dayId] = getDayLaboralValue(item);
            payload[`${item.prefix}De`] = normalizeTimeFieldById(`${item.prefix}De`);
            payload[`${item.prefix}Ha`] = normalizeTimeFieldById(`${item.prefix}Ha`);
            payload[`${item.prefix}Re`] = normalizeTimeFieldById(`${item.prefix}Re`);
            payload[`${item.prefix}Hs`] = normalizeTimeFieldById(`${item.prefix}Hs`);

            const liId = `${item.prefix}Li`;
            normalizeLimitFieldById(liId, `${item.key} - Limite (%)`);
            payload[liId] = String($modalHorario.find(`#${liId}`).val() ?? '0');
        });

        return payload;

    };

    const validateHorarioBeforeSave = (payload) => {
        if (!Number.isInteger(payload.HorCodi) || payload.HorCodi < 0 || payload.HorCodi > 32767) {
            notify('Codigo invalido. Debe estar entre 0 y 32767', 'danger', 3500, 'right');
            return false;
        }
        if (!payload.HorDesc) {
            notify('La descripcion es requerida', 'danger', 3500, 'right');
            return false;
        }
        if (!payload.HorID) {
            notify('El ID es requerido y debe ser alfanumerico', 'danger', 3500, 'right');
            return false;
        }
        if (!/^#[0-9a-f]{6}$/i.test(payload.HorColor)) {
            notify('El color debe estar en formato #RRGGBB', 'danger', 3500, 'right');
            return false;
        }
        return true;
    };

    const notifyHorarioResponse = (response) => {
        const code = String(response?.RESPONSE_CODE ?? '');
        const data = response?.DATA ?? {};
        const insertados = Array.isArray(data.insertados) ? data.insertados.length : 0;
        const actualizados = Array.isArray(data.actualizados) ? data.actualizados.length : 0;
        const errores = Array.isArray(data.errores) ? data.errores : [];

        if (code === '200 OK') {
            const okCount = insertados + actualizados;
            if (okCount > 0) {
                notify(`Horario guardado (${okCount})`, 'success', 3000, 'right');
            }
            if (errores.length > 0) {
                const firstErr = errores[0]?.error || 'Error de validacion';
                notify(`Se detectaron ${errores.length} error(es): ${firstErr}`, 'warning', 4500, 'right');
            }
            return true;
        }

        if (errores.length > 0) {
            const firstErr = errores[0]?.error || response?.MESSAGE || 'No se pudo guardar el horario';
            notify(firstErr, 'danger', 4500, 'right');
        } else {
            notify(response?.MESSAGE || 'No se pudo guardar el horario', 'danger', 4500, 'right');
        }
        return false;
    };

    const notifyDeleteHorarioResponse = (response) => {
        const code = String(response?.RESPONSE_CODE ?? '');
        const data = response?.DATA ?? {};
        const eliminados = Array.isArray(data.eliminados) ? data.eliminados.length : 0;
        const errores = Array.isArray(data.errores) ? data.errores : [];

        if (code === '200 OK') {
            if (eliminados > 0) {
                notify(`Horario eliminado (${eliminados})`, 'success', 3000, 'right');
            }
            if (errores.length > 0) {
                const firstErr = errores[0]?.error || 'Error de validacion';
                notify(`Se detectaron ${errores.length} error(es): ${firstErr}`, 'warning', 4500, 'right');
            }
            return true;
        }

        if (errores.length > 0) {
            const firstErr = errores[0]?.error || response?.MESSAGE || 'No se pudo eliminar el horario';
            notify(firstErr, 'danger', 4500, 'right');
        } else {
            notify(response?.MESSAGE || 'No se pudo eliminar el horario', 'danger', 4500, 'right');
        }
        return false;
    };
    const notifyDeleteUnused = (response) => {
        console.log(response);

        const code = String(response?.RESPONSE_CODE ?? '');
        const data = response?.DATA ?? {};
        const eliminados = Array.isArray(data.eliminados) ? data.eliminados.length : 0;
        const errores = Array.isArray(data.errores) ? data.errores : [];

        if (code === '200 OK') {
            if (eliminados > 0) {
                notify(`Horarios eliminados (${eliminados})`, 'success', 3000, 'right');
            }
            if (errores.length > 0) {
                const firstErr = errores[0]?.error || 'Error de validacion';
                notify(`Se detectaron ${errores.length} error(es): ${firstErr}`, 'warning', 4500, 'right');
            }
            return true;
        }

        if (errores.length > 0) {
            const firstErr = errores[0]?.error || response?.MESSAGE || 'No se pudo eliminar el horario';
            notify(firstErr, 'danger', 4500, 'right');
        } else {
            notify(response?.MESSAGE || 'No se pudo eliminar el horario', 'danger', 4500, 'right');
        }
        return false;
    };

    const notifyHorarioExportReady = (archivo) => {
        const cleanPath = String(archivo ?? '').replace(/^\/+/, '');
        const bannerDownload = `
            <div class="d-flex flex-column">
                <div class="font-weight-bold">Exportación generada.</div>
                <a href="/${homehost}/${cleanPath}" class="btn btn-custom px-2 btn-sm mt-2 font08 download-horarios-xls" target="_blank" download>
                    <div class="d-flex align-items-center w-100 justify-content-center" style="gap:5px">
                        <span>Descargar</span> <i class="bi bi-file-earmark-arrow-down font1"></i>
                    </div>
                </a>
            </div>
        `;
        notify(bannerDownload, 'warning', 0, 'right');

        const $download = $('.download-horarios-xls').last();
        if ($download.length) {
            $download.off('click.closeNotify').on('click.closeNotify', function () {
                $.notifyClose();
            });
        }
    };

    const exportHorariosXls = async ($btnExportar) => {
        if (!window.axios || typeof window.axios.post !== 'function') {
            notify('No se encontró axios para exportar horarios', 'danger', 4500, 'right');
            return;
        }

        const $btn = $btnExportar && $btnExportar.length ? $btnExportar : null;
        if ($btn) {
            $btn.prop('disabled', true).addClass('disabled');
        }

        $.notifyClose();
        notify('Generando archivo XLS, aguarde por favor...', 'dark', 0, 'right');

        try {
            const resExport = await window.axios.post(ENDPOINT_HORARIO_EXPORT_XLS, {}, {
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            $.notifyClose();
            const archivo = resExport?.data?.archivo ?? '';
            if (!archivo) {
                throw new Error('No se pudo generar el archivo XLS');
            }

            notifyHorarioExportReady(archivo);
        } catch (err) {
            $.notifyClose();
            const text = err?.response?.data?.message || err?.response?.data?.mensaje || err?.message || 'Error al exportar horarios';
            notify(text, 'danger', 4500, 'right');
        } finally {
            if ($btn) {
                $btn.prop('disabled', false).removeClass('disabled');
            }
        }
    };

    const openImportarHorariosModal = () => {
        if (!$modalImportarHorarios.length) {
            notify('No se encontró el modal de importación de horarios', 'danger', 4500, 'right');
            return;
        }

        const $fileInput = $modalImportarHorarios.find('#import-horarios-file');
        const $fileInputSelected = $modalImportarHorarios.find('#selected-file-name');
        if ($fileInput.length) {
            $fileInput.val('');
        }

        $fileInput.off('change.file').on('change.file', function () {
            // console.log(this.files[0].name);
            $fileInputSelected.text(this.files[0] ? this.files[0].name : 'Ningún archivo seleccionado');
        });

        const $alert = $modalImportarHorarios.find('#import-horarios-alert');
        const $list = $modalImportarHorarios.find('#import-horarios-alert-list');
        if ($list.length) {
            $list.empty();
        }
        if ($alert.length) {
            $alert.addClass('d-none');
        }

        $modalImportarHorarios.modal('show');
    };

    const openUnusedHorariosModal = () => {
        if (!$modalUnusedHorarios.length) {
            notify('No se encontró el modal de horarios no utilizados', 'danger', 4500, 'right');
            return;
        }
        initTablaUnused();
        $modalUnusedHorarios.modal('show');
    };

    const escapeHtml = (value) => {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    };

    const clearImportWarnings = () => {
        const $alert = $modalImportarHorarios.find('#import-horarios-alert');
        const $list = $modalImportarHorarios.find('#import-horarios-alert-list');
        if ($list.length) {
            $list.empty();
        }
        if ($alert.length) {
            $alert.addClass('d-none');
        }
    };

    const renderImportWarnings = (advertencias) => {
        const warnings = Array.isArray(advertencias) ? advertencias : [];
        const $alert = $modalImportarHorarios.find('#import-horarios-alert');
        const $list = $modalImportarHorarios.find('#import-horarios-alert-list');
        if (!$alert.length || !$list.length) return;

        $list.empty();
        warnings.forEach(function (item) {
            const fila = Number(item?.fila ?? 0);
            const mensaje = String(item?.mensaje ?? 'Advertencia de validación');
            const textoFila = fila > 0 ? `Fila ${fila}: ` : '';
            $list.append(`<li>${escapeHtml(textoFila + mensaje)}</li>`);
        });

        if (warnings.length > 0) {
            $alert.removeClass('d-none');
        } else {
            $alert.addClass('d-none');
        }
    };

    const getImportFileFromModal = () => {
        const input = $modalImportarHorarios.find('#import-horarios-file').get(0);
        if (!input || !input.files || !input.files.length) {
            return null;
        }
        return input.files[0] || null;
    };

    const validateImportFileBasic = (file) => {
        if (!file) {
            return 'Seleccione un archivo para importar';
        }

        const fileName = String(file.name ?? '').toLowerCase();
        if (!/\.(xls|xlsx)$/.test(fileName)) {
            return 'El archivo debe tener extensión .xls o .xlsx';
        }

        if (Number(file.size ?? 0) <= 0) {
            return 'El archivo seleccionado está vacío';
        }

        return '';
    };

    const notifyHorarioImportResponse = (result) => {
        const response = result?.response ?? result ?? {};
        const code = String(response?.RESPONSE_CODE ?? '');
        const data = response?.DATA ?? {};
        const insertados = Array.isArray(data.insertados) ? data.insertados.length : 0;
        const actualizados = Array.isArray(data.actualizados) ? data.actualizados.length : 0;
        const errores = Array.isArray(data.errores) ? data.errores : [];

        if (code === '200 OK') {
            const okCount = insertados + actualizados;
            notify(`Importación finalizada. Creados: ${insertados}, actualizados: ${actualizados}`, 'success', 5000, 'right');
            if (errores.length > 0) {
                const firstErr = errores[0]?.error || 'Error de validación';
                notify(`La Importación arrojó ${errores.length} error(es): ${firstErr}`, 'warning', 7000, 'right');
            }
            return okCount > 0 || errores.length >= 0;
        }

        if (errores.length > 0) {
            const firstErr = errores[0]?.error || response?.MESSAGE || 'No se pudo importar el archivo';
            notify(firstErr, 'danger', 6000, 'right');
        } else {
            notify(response?.MESSAGE || 'No se pudo importar el archivo', 'danger', 6000, 'right');
        }
        return false;
    };

    const importHorariosXls = async ($btnImportar) => {
        if (!window.axios || typeof window.axios.post !== 'function') {
            notify('No se encontró axios para importar horarios', 'danger', 4500, 'right');
            return;
        }

        clearImportWarnings();

        const file = getImportFileFromModal();
        const invalidMessage = validateImportFileBasic(file);
        if (invalidMessage) {
            notify(invalidMessage, 'danger', 4500, 'right');
            return;
        }

        const $btn = $btnImportar && $btnImportar.length ? $btnImportar : null;
        if ($btn) {
            $btn.prop('disabled', true).addClass('disabled');
        }

        const formData = new FormData();
        formData.append('archivo', file);

        try {
            const res = await window.axios.post(ENDPOINT_HORARIO_IMPORT_XLS, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });

            const ok = notifyHorarioImportResponse(res?.data ?? {});
            if (ok) {
                const $fileInput = $modalImportarHorarios.find('#import-horarios-file');
                if ($fileInput.length) {
                    $fileInput.val('');
                }
                $modalImportarHorarios.modal('hide');
                if (tablaHorarios) {
                    tablaHorarios.ajax.reload(null, false);
                }
            }
        } catch (err) {
            const data = err?.response?.data ?? {};
            const warnings = Array.isArray(data?.advertencias) ? data.advertencias : [];
            if (warnings.length > 0) {
                renderImportWarnings(warnings);
                notify(`Se detectaron ${warnings.length} advertencia(s) antes de importar. Revise el detalle por fila.`, 'warning', 6500, 'right');
                return;
            }

            const text = data?.message || data?.mensaje || err?.message || 'Error al importar archivo XLS';
            notify(text, 'danger', 5000, 'right');
        } finally {
            if ($btn) {
                $btn.prop('disabled', false).removeClass('disabled');
            }
        }
    };

    const exportHorariosEjemploXls = async ($btnExportar) => {
        if (!window.axios || typeof window.axios.post !== 'function') {
            notify('No se encontró axios para exportar el ejemplo', 'danger', 4500, 'right');
            return;
        }

        const $btn = $btnExportar && $btnExportar.length ? $btnExportar : null;
        if ($btn) {
            $btn.prop('disabled', true).addClass('disabled');
        }

        try {
            const resExport = await window.axios.post(ENDPOINT_HORARIO_EXPORT_XLS_EJEMPLO, {}, {
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            const archivo = resExport?.data?.archivo ?? '';
            if (!archivo) {
                throw new Error('No se pudo generar el archivo de ejemplo');
            }

            const cleanPath = String(archivo ?? '').replace(/^\/+/, '');
            window.open(`/${homehost}/${cleanPath}`, '_blank');
        } catch (err) {
            const text = err?.response?.data?.message || err?.response?.data?.mensaje || err?.message || 'Error al generar el ejemplo de importación';
            notify(text, 'danger', 4500, 'right');
        } finally {
            if ($btn) {
                $btn.prop('disabled', false).removeClass('disabled');
            }
        }
    };

    const DELETE_MODAL_ID = 'modal-confirm-delete-horario';

    const confirmDeleteHorarioModal = (label) => {
        const $modal = $(`#${DELETE_MODAL_ID}`);
        if (!$modal.length) {
            notify('No se encontró el modal de confirmación para eliminar', 'danger', 4500, 'right');
            return Promise.resolve(false);
        }
        const $text = $modal.find('#confirm-delete-horario-text');
        const $confirmBtn = $modal.find('#btn-confirm-delete-horario');

        $text.text(`¿Confirma eliminar el horario ${label}?`);

        return new Promise((resolve) => {
            let resolved = false;

            const cleanup = () => {
                $confirmBtn.off('click.confirmDeleteHorario');
                $modal.off('hidden.bs.modal.confirmDeleteHorario');
            };

            $confirmBtn.off('click.confirmDeleteHorario').on('click.confirmDeleteHorario', function () {
                resolved = true;
                cleanup();
                $modal.modal('hide');
                resolve(true);
            });

            $modal.off('hidden.bs.modal.confirmDeleteHorario').on('hidden.bs.modal.confirmDeleteHorario', function () {
                cleanup();
                if (!resolved) {
                    resolve(false);
                }
            });

            $modal.modal('show');
        });
    };

    const deleteHorarioByRow = async (row, $triggerBtn) => {
        const horCodi = sanitizeHorCodi(row?.Codi ?? '');
        if (!horCodi) {
            notify('No se encontró el código del horario a eliminar', 'danger', 4500, 'right');
            return;
        }

        if (!window.axios || typeof window.axios.delete !== 'function') {
            notify('No se encontró axios para eliminar el horario', 'danger', 4500, 'right');
            return;
        }

        const label = `(${row?.Codi ?? ''}) ${row?.Desc ?? ''}`.trim();
        const confirmed = await confirmDeleteHorarioModal(label);
        if (!confirmed) return;

        const $btn = $triggerBtn && $triggerBtn.length ? $triggerBtn : null;
        if ($btn) {
            $btn.prop('disabled', true);
            $btn.addClass('disabled');
        }

        try {
            const res = await window.axios.delete(ENDPOINT_HORARIO, {
                headers: {
                    'Content-Type': 'application/json',
                },
                data: [{ HorCodi: Number(horCodi) }],
            });

            const ok = notifyDeleteHorarioResponse(res?.data ?? {});
            if (ok && tablaHorarios) {
                tablaHorarios.ajax.reload(null, false);
            }
        } catch (err) {
            const text = err?.response?.data?.message || err?.response?.data?.MESSAGE || 'Error al eliminar el horario';
            notify(text, 'danger', 4500, 'right');
        } finally {
            if ($btn) {
                $btn.prop('disabled', false);
                $btn.removeClass('disabled');
            }
        }
    };

    const bindSaveHorarioAction = () => {
        $modalHorario.off('click.saveHorario');

        $modalHorario.on('click.saveHorario', '#btn-save', function () {
            const $btn = $(this);
            if ($btn.prop('disabled')) return;

            const payload = getPayloadHorario();

            if (!validateHorarioBeforeSave(payload)) return;

            if (!window.axios || typeof window.axios.post !== 'function') {
                notify('No se encontró axios para enviar el formulario', 'danger', 4500, 'right');
                return;
            }

            $btn.prop('disabled', true).text('Aguarde');

            window.axios.post(ENDPOINT_HORARIO, [payload], {
                headers: {
                    'Content-Type': 'application/json',
                },
            }).then(function (res) {
                const response = res?.data ?? {};
                const ok = notifyHorarioResponse(response);
                if (!ok) return;

                $modalHorario.modal('hide');
                if (tablaHorarios) {
                    tablaHorarios.ajax.reload(null, false);
                }
            }).catch(function (err) {
                const text = err?.response?.data?.message || err?.response?.data?.MESSAGE || 'Error al guardar el horario';
                notify(text, 'danger', 4500, 'right');
            }).finally(function () {
                $btn.prop('disabled', false).text(saveButtonDefaultText);
            });
        });
    };

    const initHorColorPicker = () => {
        const $horColor = $modalHorario.find('#HorColor');
        if (!$horColor.length) return;

        if (window.jscolor && window.jscolor.presets) {
            window.jscolor.presets.default = {
                palette: [
                    '#000000', '#7d7d7d', '#870014', '#ec1c23', '#ff7e26',
                    '#393939', '#22b14b', '#00a1e7', '#3f47cc', '#a349a4',
                    '#fef100', '#c3c3c3', '#008FCE', '#0D983D', '#ffc80d',
                    '#9D2313', '#0F7789', '#2B6FF0', '#063CFF', '#EC640F',
                ],
                paletteCols: 5,
                hideOnPaletteClick: false,
                format: 'hex',
                previewSize: 60,
                borderColor: '#ccc',
                borderRadius: 0,
                width: 148,
                controlBorderColor: '#ccc',
                sliderSize: 28,
                closeButton: true,
                closeText: 'Cerrar',
                shadow: true,
                shadowBlur: 1,
            };
        }

        if (window.jscolor && typeof window.jscolor.install === 'function') {
            window.jscolor.install($horColor[0]);
        }

        const picker = $horColor[0].jscolor;
        const defaultPreset = window.jscolor && window.jscolor.presets ? window.jscolor.presets.default : null;
        if (picker && defaultPreset && typeof picker.option === 'function') {
            Object.keys(defaultPreset).forEach(function (key) {
                picker.option(key, defaultPreset[key]);
            });
        }
    };

    const editarHorario = (data) => {
        if (!data) return;

        initFeriadoSelect();
        initHorColorPicker();
        saveButtonDefaultText = 'Guardar';
        setSaveButtonText(saveButtonDefaultText);
        $modalHorario.find('.modal-title').text(`Editar Horario - ${data.Desc || ''}`);

        editingHorCodi = sanitizeHorCodi(data.Codi ?? '');
        setModalValue('HorCodi', sanitizeHorCodi(data.Codi ?? ''));
        setModalDisabled('HorCodi', true);
        applyRowDataToModal(data);

        $modalHorario.modal('show');
    };

    const renderOpt = `<div class="opt-tbl mb-3 d-flex justify-content-between align-items-center gap5"></div>`;

    const renderOptExport = () => {
        let html = `<div class="btn-group dropright"><button type="button" class="btn btn-sm h40 btn-outline-custom border-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></button><div class="dropdown-menu w200 shadow border-0 p-0 radius" x-placement="right-start" style="position: absolute; transform: translate3d(30px, 0px, 0px); top: 0px; left: 0px; will-change: transform;"><ul class="list-group" id="export-options">`;
        MAP_HTML_EXPORT.forEach(function (item) {
            const label = item.text || item.label || item.id;
            html += `<button class="btn btn-outline-custom border-0 radius font08 w-100 text-left" id="${item.id}"><div class="ml-1">${label}</div></button>`;
        });
        html += `</ul></div></div>`;
        return html;
    };

    const renderOptNuevoHorario = () => {
        return `<div id="div-nuevo-horario" class="d-inline-flex gap5"><button class="btn h40 px-3 btn-sm btn-custom border-0 ml-1" id="btn-nuevo-horario"><span class="d-none d-sm-block"><i class="bi bi-plus-lg"></i> Nuevo Horario</span><span class="d-block d-sm-none">Nuevo</span></button></div>`;
    };
    const renderSelectOrder = () => {
        let html = `<div class="d-flex align-items-center"><label class="font08 mb-0 mr-2 d-none d-sm-block">Ordenar por:</label><select id="select-order" class="form-control form-control-sm">`;
        Object.keys(MAP_ORDENAR_POR).forEach(function (key) {
            html += `<option value="${key}">${MAP_ORDENAR_POR[key].column}</option>`;
        });
        html += `</select></div>`;
        return html;
    };

    const initTablaHorarios = () => {
        let selectedOrderBy = getStoredOrderBy();

        const table = $('#tblHorarios').DataTable({
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            bProcessing: true,
            serverSide: true,
            deferRender: true,
            ajax: {
                url: ENDPOINT_HORARIOS,
                type: 'POST',
                data: function (d) {
                    d.OrderBy = selectedOrderBy;
                    return d;
                },
            },
            createdRow: function (row, data, dataIndex) {
                $(row).addClass('fadeIn');
            },
            columns: buildColumns(),
            paging: true,
            info: true,
            searching: true,
            ordering: false,
            language: DT_SPANISH_2,
            rowGroup: {
                dataSrc: function (row) {
                    return renderRowGroup(row);
                },
            },
        });

        table.on('init.dt', function (e, settings) {
            $('#tblHorarios thead').remove();

            const $lenght = $('#tblHorarios_length select');
            const $inputFilter = $('#tblHorarios_filter input');
            $('#tblHorarios_filter').prepend('<label class="font08 mb-1">Buscar:</label>');
            const htmlOpt = renderOpt;

            $inputFilter.attr('placeholder', 'Código / Descripcion / ID').removeClass('form-control-sm');
            $('#table-responsive-horarios').prepend(htmlOpt);

            const htmlNuevoHorario = renderOptNuevoHorario();
            const htmlSelectOrder = renderSelectOrder();
            const htmlOptExport = renderOptExport();

            const $optTbl = $('#table-responsive-horarios .opt-tbl');

            $optTbl.append(htmlNuevoHorario);
            const $divNuevohorario = $('#div-nuevo-horario');
            $divNuevohorario.append(htmlOptExport);

            $optTbl.append(htmlSelectOrder);

            select2Simple('#select-order', 'Seleccionar orden', false, false, '220px');

            const $selectOrder = $('#select-order');
            if ($selectOrder.length) {
                selectedOrderBy = normalizeOrderBy(selectedOrderBy);
                $selectOrder.val(selectedOrderBy).trigger('change.select2');
                setStoredOrderBy(selectedOrderBy);

                $selectOrder.off('change.orderBy').on('change.orderBy', function () {
                    const value = normalizeOrderBy($(this).val());
                    selectedOrderBy = setStoredOrderBy(value);
                    table.ajax.reload(null, true);
                });
            }

            $('#tblHorarios tbody').on('click', '.btn-editar-horario', function () {
                const dataCodi = $(this).data('codi');
                const dataRow = getDataRowByCodi(table, dataCodi);                
                editarHorario(dataRow);
            });

            $('.opt-tbl').off('click.newHorario').on('click.newHorario', '#btn-nuevo-horario', function () {
                openCreateHorarioModal();
            });

            $('.opt-tbl').off('click.exportHorario').on('click.exportHorario', '#btn-exportar-xls', function () {
                exportHorariosXls($(this));
            });

            $('.opt-tbl').off('click.importHorario').on('click.importHorario', '#btn-importar-xls', function () {
                openImportarHorariosModal();
            });

            $('.opt-tbl').off('click.unusedHorario').on('click.unusedHorario', '#btn-eliminar-unused', function () {
                openUnusedHorariosModal();
            });

            $modalImportarHorarios.off('click.importarHorarios');
            $modalImportarHorarios.on('click.importarHorarios', '#btn-descargar-ejemplo-importar-xls', function () {
                exportHorariosEjemploXls($(this));
            });
            $modalImportarHorarios.on('click.importarHorarios', '#btn-importar-xls-modal', function () {
                importHorariosXls($(this));
            });

            $('#tblHorarios').on('click', '.btn-duplicar-horario', function () {
                const dataCodi = $(this).data('codi');
                const dataRow = getDataRowByCodi(table, dataCodi);
                openCreateHorarioModal(dataRow);
            });

            $('#tblHorarios').on('click', '.btn-eliminar-horario', function () {
                const dataCodi = $(this).data('codi');
                const dataRow = getDataRowByCodi(table, dataCodi);
                deleteHorarioByRow(dataRow, $(this));
            });
            $('.table-responsive').show();
        });

        table.on('draw.dt', function () {
            $('#tblHorarios').removeClass('loader-in');
        });

        table.on('page.dt', function (e, settings) {
            $('#tblHorarios').addClass('loader-in');
        });

        tablaHorarios = table;
        return table;
    };

    const initTablaUnused = () => {

        if (tablaUnused && $.fn.DataTable.isDataTable('#tblUnused')) {
            tablaUnused.ajax.reload(null, false);
            return tablaUnused;
        }

        const renderColorBadgeUnused = (HorColor) => {
            return `<span class="badge radius w10" style="background-color:${HorColor};">&nbsp;</span>`;
        };
        const $btnDeleteConfirm = $('#btn-confirm-unused-horario');
        const buildColumnsUnused = () => {
            const colCodi = {
                data: 'HorCodi', className: '', targets: '', title: 'Cód.',
                visible: true,
                render: function (data, type) { return data; },
            };
            const colID = {
                data: 'HorID', className: '', targets: '', title: 'ID',
                visible: true,
                render: function (data, type) { return data; },
            };
            const colDesc = {
                data: 'HorDesc', className: 'w-100', targets: '', title: 'Descripción',
                visible: true,
                render: function (data, type) { return data; },
            };
            const colColor = {
                data: 'HorColor', className: '', targets: '', title: '',
                orderable: false,
                visible: true,
                render: function (data, type, row) { if (type !== 'display') return ''; return renderColorBadgeUnused(data); },
            };
            return [colCodi, colDesc, colID, colColor];

        };


        tablaUnused = $('#tblUnused').DataTable({
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            bProcessing: false,
            serverSide: false,
            deferRender: true,
            ajax: {
                url: ENDPOINT_HORARIO_UNUSED,
                type: 'GET',
                dataSrc: function (json) {
                    if (!json || (Array.isArray(json) && json.length === 0) || (json.data && Array.isArray(json.data) && json.data.length === 0) || (json.DATA && Array.isArray(json.DATA) && json.DATA.length === 0)) {
                        $btnDeleteConfirm.prop('disabled', true).addClass('disabled');
                        return [];
                    }

                    $btnDeleteConfirm.prop('disabled', false).removeClass('disabled');

                    if (Array.isArray(json)) return json;
                    if (Array.isArray(json?.data)) return json.data;
                    if (Array.isArray(json?.DATA)) return json.DATA;
                    return [];
                },
            },
            createdRow: function (row, data, dataIndex) {
                $(row).addClass('fadeIn');
            },
            columns: buildColumnsUnused(),
            paging: true,
            info: true,
            searching: true,
            ordering: false,
            language: DT_SPANISH_2,
        });

        tablaUnused.on('init.dt', function (e, settings) {
            // $('#tblUnused thead').remove();

            const $lenght = $('#tblUnused_length select');
            const $inputFilter = $('#tblUnused_filter input');

            if ($lenght.length) {
                $lenght.removeClass('form-control-sm');
            }

            $inputFilter.attr('placeholder', 'Buscar').removeClass('form-control-sm');

            $('#modal-unused-horarios').on('click', '#btn-confirm-unused-horario', function () {
                deleteHorarioUnused($(this));
            });

            $('.table-responsive').show();
        });

        tablaUnused.on('draw.dt', function () {
            $('#tblUnused').removeClass('loader-in');
        });

        tablaUnused.on('page.dt', function (e, settings) {
            $('#tblUnused').addClass('loader-in');
        });

        $modalUnusedHorarios.off('shown.bs.modal.unusedTable').on('shown.bs.modal.unusedTable', function () {
            if (tableHorariosUnused) {
                tableHorariosUnused.columns.adjust().draw(false);
            }
        });

        tableHorariosUnused = tablaUnused;

        return tablaUnused;
    };

    const deleteHorarioUnused = async ($triggerBtn) => {

        if (!window.axios || typeof window.axios.delete !== 'function') {
            notify('No se encontró axios para eliminar el horario', 'danger', 4500, 'right');
            return;
        }

        const $btn = $triggerBtn && $triggerBtn.length ? $triggerBtn : null;

        if ($btn) {
            $btn.prop('disabled', true);
            $btn.addClass('disabled');
        }

        try {
            const res = await window.axios.delete(ENDPOINT_HORARIO_UNUSED, {
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            const ok = notifyDeleteUnused(res?.data ?? {});
            if (ok && tableHorariosUnused) {
                tableHorariosUnused.ajax.reload(null, false);
                $modalUnusedHorarios.modal('hide');
            }
        } catch (err) {
            const text = err?.response?.data?.message || err?.response?.data?.MESSAGE || 'Error al eliminar horarios';
            notify(text, 'danger', 4500, 'right');
        } finally {
            if ($btn) {
                $btn.prop('disabled', false);
                $btn.removeClass('disabled');
            }
        }
    };

})(jQuery);