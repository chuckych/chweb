(function ($) {
    'use strict';

    $(document).ready(function () {
        bindModalActions();
        bindSaveRotacionAction();
        initTablaRotaciones();
    });

    let tablaRotaciones = null;
    let tablaRotacionesUnused = null;
    let tableRotacionesUnused = null;
    let saveButtonDefaultText = 'Guardar';
    let editingRotCodi = null;
    let horariosCatalog = [];

    const homehost = $("#_homehost").val();
    const ENDPOINT_ROTACIONES = '/' + homehost + '/app-data/datatables/rotaciones';
    const ENDPOINT_ROTACION = '/' + homehost + '/app-data/rotacion';
    const ENDPOINT_ROTACIONES_LOOKUP = '/' + homehost + '/app-data/horarios';
    const ENDPOINT_ROTACION_UNUSED = '/' + homehost + '/app-data/rotacion/unused';
    const ENDPOINT_ROTACION_EXPORT_XLS = '/' + homehost + '/app-data/rotacion/exportar-xls';
    const ENDPOINT_ROTACION_EXPORT_XLS_EJEMPLO = '/' + homehost + '/app-data/rotacion/exportar-xls-ejemplo';
    const ENDPOINT_ROTACION_IMPORT_XLS = '/' + homehost + '/app-data/rotacion/importar-xls';

    const $modalRotacion = $('#modal-rotacion');
    const $modalImportarRotaciones = $('#modal-importar-rotaciones');
    const $modalUnusedRotaciones = $('#modal-unused-rotaciones');

    const MAP_HTML_EXPORT = [
        { id: 'btn-exportar-xls-rotacion', text: 'Exportar .xls' },
        { id: 'btn-importar-xls-rotacion', text: 'Importar .xls' },
        { id: 'btn-eliminar-unused-rotacion', text: 'Eliminar no asignadas' },
    ];

    const DIAS = [
        { key: 'HorLune', title: 'Lu' },
        { key: 'HorMart', title: 'Ma' },
        { key: 'HorMier', title: 'Mi' },
        { key: 'HorJuev', title: 'Ju' },
        { key: 'HorVier', title: 'Vi' },
        { key: 'HorSaba', title: 'Sá' },
        { key: 'HorDomi', title: 'Do' },
        { key: 'HorFeri', title: 'Fe' },
    ];

    const renderRowGroup = (row) => {
        const style = `style="border: 1px solid #ddd; color: #333; background-color: #f8f9fa; min-width: 50px;"`;
        return `
            <div class="d-flex justify-content-between align-items-center pt-3 px-2 m-0">
                <div class="d-flex justify-content-start align-items-center gap5">
                        <strong>(${row.RotCodi})<strong> ${row.RotDesc}</strong>
                </div>
                <div class="border p-1 rounded d-flex justify-content-center align-items-center gap5">
                    <button class="hint--top btn btn-sm btn-outline-custom border-0 btn-editar-rotacion" data-codi="${row.RotCodi}" title="Editar" aria-label="Editar"><i class="bi bi-pen"></i></button>
                    <button class="hint--top btn btn-sm btn-outline-custom border-0 btn-duplicar-rotacion" data-codi="${row.RotCodi}" title="Duplicar" aria-label="Duplicar Rotación"><i class="bi bi-copy"></i></button>
                    <button class="hint--top btn btn-sm btn-outline-custom border-0 btn-eliminar-rotacion" data-codi="${row.RotCodi}" title="Eliminar" aria-label="Eliminar"><i class="bi bi-trash"></i></button>
                </div>
            </div>`;
    };

    const renderColorBadge = (HorColor) => {
        return `<span class="radius w5 mr-2" style="background-color:${HorColor};">&nbsp;</span>`;
    };
    const renderItem = (RotItem) => {
        return `# <div class="w20 text-center">${RotItem}</div>`;
    };
    const renderDiaStr = (RotDias) => {
        return `${RotDias} ${RotDias == 1 ? 'día' : 'días'}`;
    }
    const renderActiveDayHorario = (HorDomi, HorLune, HorMart, HorMier, HorJuev, HorVier, HorSaba, HorFeri) => {
        const keyDay = { HorDomi, HorLune, HorMart, HorMier, HorJuev, HorVier, HorSaba, HorFeri };
        return DIAS.map(dia => {
            const isActive = Number(keyDay[dia.key]) === 1;

            if (dia.key === 'HorFeri') {
                // return '';
            }

            return `<div style="border-radius:6px;width:35px; border:1px solid ${isActive ? '#bab9b9' : '#fff'};" class="badge text-center mx-1 font07 bg-${isActive ? 'light' : 'white'}" title="${dia.title}">${dia.title}</div>`;
        }).join(' ');
    };

    const buildColumns = () => {
        return [
            {
                data: 'Horarios',
                visible: true,
                orderable: false,
                className: '',
                render: function (data, type) {
                    if (type !== 'display') return data ?? '';
                    const Horarios = Array.isArray(data) ? data : [];
                    return Horarios.map(item => {
                        const html = `${renderColorBadge(item.RotHoraColor)} ${renderItem(item.RotItem)} (<span title="Código de Horario">${item.RotHora}</span>)&nbsp;<span title="Descripción de Horario">${item.RotHoraStr}</span>`;
                        return `<div style="margin-bottom: 5px;" class="d-flex h30 align-items-center">${html}</div>`;
                    }).join('');
                },
            },
            {
                data: 'Horarios',
                visible: true,
                orderable: false,
                className: 'text-left',
                render: function (data, type) {
                    if (type !== 'display') return data ?? '';
                    const Horarios = Array.isArray(data) ? data : [];
                    return Horarios.map(item => {
                        const html = `${renderDiaStr(item.RotDias)}`;
                        return `<div style="margin-bottom: 5px;" class="d-flex h30 align-items-center">${html}</div>`;
                    }).join('');
                },
            },
            {
                data: 'Horarios',
                visible: true,
                orderable: false,
                className: 'w-100 text-left align-middle',
                render: function (data, type) {
                    if (type !== 'display') return data ?? '';
                    const Horarios = Array.isArray(data) ? data : [];
                    return Horarios.map(item => {
                        const html = `${renderActiveDayHorario(item.HorDomi, item.HorLune, item.HorMart, item.HorMier, item.HorJuev, item.HorVier, item.HorSaba, item.HorFeri)}`;
                        return `<div style="margin-bottom: 5px;" class="d-flex align-items-center h30">${html}</div>`;
                    }).join('');
                },
            },
        ];
    };
    const renderDomTableRotaciones = () => {
        return `
                <'row mt-2'
                    <'col-sm-12 col-md-6 d-inline-flex align-items-center justify-content-start gap5'l>
                    <'col-sm-12 col-md-6'f>
                >
                <'row 
                    '<'col-12 table-responsive'<'text-nowrap'rt>>
                >
                <'row'
                    <'col-sm-12 col-md-5'i>
                    <'col-sm-12 col-md-7'p>
                >`;
    }

    const renderOpt = `<div class="opt-tbl mb-4 d-flex justify-content-between align-items-center gap5"></div>`;

    const renderOptExport = () => {
        let html = `<div class="btn-group dropright"><button type="button" class="btn btn-sm h40 btn-outline-custom border-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></button><div class="dropdown-menu w200 shadow border-0 p-0 radius" x-placement="right-start" style="position: absolute; transform: translate3d(30px, 0px, 0px); top: 0px; left: 0px; will-change: transform;"><ul class="list-group">`;
        MAP_HTML_EXPORT.forEach(function (item) {
            html += `<button class="btn btn-outline-custom border-0 radius font08 w-100 text-left" id="${item.id}"><div class="ml-1">${item.text}</div></button>`;
        });
        html += `</ul></div></div>`;
        return html;
    };

    const renderOptNuevaRotacion = () => {
        return `<div id="div-nueva-rotacion" class="d-inline-flex gap5"><button class="btn h40 px-3 btn-sm btn-custom border-0 ml-1" id="btn-nueva-rotacion"><span class="d-none d-sm-block"><i class="bi bi-plus-lg"></i> Nueva Rotación</span><span class="d-block d-sm-none">Nueva</span></button></div>`;
    };

    const getDataRowByCodi = (table, codi) => {
        const rows = table.rows().data().toArray();
        const selectedCodi = String(codi ?? '');
        return rows.find(function (row) {
            return String(row.RotCodi ?? '') === selectedCodi;
        }) || null;
    };

    const toInt = (value, fallback = 0) => {
        const parsed = Number(value);
        return Number.isFinite(parsed) ? Math.trunc(parsed) : fallback;
    };

    const defaultHorarioOption = () => `<option value="">Seleccionar horario</option>`;

    const initRotHoraSelect2 = (scope = null) => {
        const $scope = scope ? $(scope) : $modalRotacion;
        const $selects = $scope.find('.rot-hora');

        $selects.each(function () {
            const $el = $(this);

            if ($el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
            }

            select2Simple($el, 'Seleccionar horario', false, false, '100%');
        });
    };

    const normalizeHorarioCatalog = (raw = []) => {
        return (Array.isArray(raw) ? raw : []).map(function (item) {
            const codi = item.Codi ?? item.HorCodi ?? item.RotHora ?? '';
            const desc = item.Desc ?? item.HorDesc ?? item.RotHoraStr ?? '';
            const color = item.Color ?? item.HorColor ?? '#000000';
            const id = item.ID ?? item.HorID ?? '';
            return {
                Codi: String(codi ?? '').trim(),
                Desc: String(desc ?? '').trim(),
                Color: String(color ?? '#000000').trim(),
                ID: String(id ?? '').trim(),
            };
        }).filter(function (item) {
            return item.Codi !== '';
        });
    };

    const fetchHorariosCatalog = async () => {
        if (horariosCatalog.length > 0) {
            return horariosCatalog;
        }
        if (!window.axios || typeof window.axios.get !== 'function') {
            throw new Error('No se encontró axios para obtener el catálogo de horarios');
        }

        const res = await window.axios.get(ENDPOINT_ROTACIONES_LOOKUP);
        const data = res?.data ?? {};
        const horarios = data.horarios ?? data.DATA ?? data.data ?? [];
        horariosCatalog = normalizeHorarioCatalog(horarios);

        if (!horariosCatalog.length) {
            throw new Error('No se encontró catálogo de horarios para cargar');
        }
        return horariosCatalog;
    };

    const renderHorarioOptions = (selected = '') => {
        const selectedValue = String(selected ?? '').trim();
        const selectedNumeric = toInt(selectedValue, 0);
        let hasSelected = false;

        const options = horariosCatalog.map(function (item) {
            const codiStr = String(item.Codi ?? '').trim();
            const codiNumeric = toInt(codiStr, 0);
            const matchByNumeric = selectedNumeric > 0 && codiNumeric === selectedNumeric;
            const matchByText = codiStr === selectedValue;
            const isSelected = (matchByNumeric || matchByText) ? 'selected' : '';
            if (isSelected) {
                hasSelected = true;
            }

            const id = item.ID ? ` [${item.ID}]` : '';
            const text = `(${item.Codi})${id} ${item.Desc}`.trim();
            return `<option value="${item.Codi}" ${isSelected}>${text}</option>`;
        }).join('');

        const fallbackSelected = (!hasSelected && selectedValue !== '')
            ? `<option value="${selectedValue}" selected>(${selectedValue}) Horario no encontrado en catálogo</option>`
            : '';

        return `${defaultHorarioOption()}${fallbackSelected}${options}`;
    };

    const refreshItemIndexes = () => {
        $modalRotacion.find('#tbl-rotacion-items tbody tr').each(function (idx) {
            const $item = $(this).find('.rot-item');
            $item.val(idx + 1);
        });
    };

    const getNextRotItem = () => {
        const values = $modalRotacion.find('#tbl-rotacion-items tbody .rot-item').map(function () {
            return toInt($(this).val(), 0);
        }).get().filter(function (val) {
            return val > 0;
        });

        if (!values.length) {
            return 1;
        }

        return Math.max(...values) + 1;
    };

    const appendItemRow = (data = {}) => {
        const rotItemRaw = toInt(data.RotItem, 0);
        const rotItem = rotItemRaw > 0 ? rotItemRaw : getNextRotItem();
        const rotDias = Math.max(1, toInt(data.RotDias, 1));
        let rotHora = String(data.RotHora ?? '').trim();

        // Defensa: si llega HTML (por datos ya mutados), intentamos recuperar el código numérico.
        if (rotHora.includes('<')) {
            const plain = rotHora.replace(/<[^>]*>/g, ' ');
            const match = plain.match(/\((\d+)\)|\b(\d+)\b/);
            rotHora = match ? String(match[1] || match[2] || '').trim() : '';
        }

        const html = `
            <tr>
                <td class="d-none"><input type="number" class="form-control h40 rot-item text-center" min="1" max="32767" value="${rotItem || ''}"></td>
                <td><select class="form-control h40 rot-hora" data-label="Seleccionar horario">${renderHorarioOptions(rotHora)}</select></td>
                <td><input type="number" class="form-control h40 rot-dias text-center w80" min="1" max="365" value="${rotDias}"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0 btn-remove-item" title="Quitar"><i class="bi bi-trash"></i></button></td>
            </tr>
        `;
        $modalRotacion.find('#tbl-rotacion-items tbody').append(html);
        initRotHoraSelect2($modalRotacion.find('#tbl-rotacion-items tbody tr:last'));
        refreshItemIndexes();
    };

    const resetRotacionModal = () => {
        editingRotCodi = null;
        $modalRotacion.find('.modal-title').text('Nueva rotación');
        $modalRotacion.find('#RotCodi').prop('disabled', false).val('');
        $modalRotacion.find('#RotDesc').val('');
        $modalRotacion.find('#tbl-rotacion-items tbody').html('');
        saveButtonDefaultText = 'Agregar';
        $modalRotacion.find('#btn-save-rotacion').text(saveButtonDefaultText);
        appendItemRow();
    };

    const getNextRotCodiByTable = () => {
        if (!tablaRotaciones) return '';
        const rows = tablaRotaciones.rows().data().toArray();
        const max = rows.reduce(function (acc, row) {
            return Math.max(acc, toInt(row.RotCodi, 0));
        }, 0);
        return max > 0 ? String(max + 1) : '';
    };

    const openCreateRotacionModal = async (sourceRow = null) => {
        try {
            await fetchHorariosCatalog();
            resetRotacionModal();

            const next = getNextRotCodiByTable();
            if (next) {
                $modalRotacion.find('#RotCodi').val(next);
            }

            if (sourceRow) {
                $modalRotacion.find('.modal-title').text('Duplicar rotación');
                $modalRotacion.find('#RotDesc').val(String(sourceRow.RotDesc ?? '').trim());
                $modalRotacion.find('#tbl-rotacion-items tbody').html('');

                const detail = Array.isArray(sourceRow.Horarios) ? sourceRow.Horarios : [];
                if (detail.length) {
                    detail.forEach(function (el) {
                        appendItemRow({
                            RotItem: el.RotItem,
                            RotHora: el.RotHora,
                            RotDias: el.RotDias,
                        });
                    });
                } else {
                    appendItemRow();
                }
                saveButtonDefaultText = 'Agregar';
                $modalRotacion.find('#btn-save-rotacion').text(saveButtonDefaultText);
            }

            $modalRotacion.modal('show');
            $modalRotacion.find('#RotDesc').focus();
        } catch (err) {
            notify(err?.message || 'No se pudo abrir el formulario de rotación', 'danger', 4500, 'right');
        }
    };

    const editarRotacion = async (data) => {
        if (!data) return;
        try {
            await fetchHorariosCatalog();
            resetRotacionModal();
            
            editingRotCodi = String(data.RotCodi ?? '').trim();
            $modalRotacion.find('.modal-title').text(`Editar rotación (${editingRotCodi})`);
            $modalRotacion.find('#RotCodi').val(editingRotCodi).prop('disabled', true);
            $modalRotacion.find('#RotDesc').val(String(data.RotDesc ?? '').trim());
            $modalRotacion.find('#tbl-rotacion-items tbody').html('');

            const detail = Array.isArray(data.Horarios) ? data.Horarios : [];
            
            if (detail.length) {
                detail.forEach(function (el) {
                    appendItemRow({
                        RotItem: el.RotItem,
                        RotHora: el.RotHora,
                        RotDias: el.RotDias,
                    });
                });
            } else {
                appendItemRow();
            }

            saveButtonDefaultText = 'Guardar';
            $modalRotacion.find('#btn-save-rotacion').text(saveButtonDefaultText);
            $modalRotacion.modal('show');
        } catch (err) {
            notify(err?.message || 'No se pudo abrir la edición de rotación', 'danger', 4500, 'right');
        }
    };

    const getRotacionPayload = () => {
        const RotCodi = toInt($modalRotacion.find('#RotCodi').val(), 0);
        const RotDesc = String($modalRotacion.find('#RotDesc').val() ?? '').trim();
        const Horarios = [];

        $modalRotacion.find('#tbl-rotacion-items tbody tr').each(function () {
            const $tr = $(this);
            Horarios.push({
                RotItem: toInt($tr.find('.rot-item').val(), 0),
                RotHora: toInt($tr.find('.rot-hora').val(), 0),
                RotDias: Math.max(1, toInt($tr.find('.rot-dias').val(), 1)),
            });
        });

        return {
            RotCodi,
            RotDesc,
            Horarios,
        };
    };

    const validateRotacionPayload = (payload) => {
        if (!payload.RotCodi || payload.RotCodi < 1 || payload.RotCodi > 32767) {
            notify('Código de rotación inválido. Debe estar entre 1 y 32767', 'danger', 3500, 'right');
            return false;
        }
        if (!payload.RotDesc) {
            notify('La descripción de la rotación es requerida', 'danger', 3500, 'right');
            return false;
        }
        if (!Array.isArray(payload.Horarios) || payload.Horarios.length === 0) {
            notify('Debe agregar al menos un item de horario', 'danger', 3500, 'right');
            return false;
        }

        const usedItems = new Set();
        const usedHoras = new Set();

        for (const item of payload.Horarios) {
            if (!item.RotItem || item.RotItem < 1) {
                notify('Cada item debe tener un número válido', 'danger', 3500, 'right');
                return false;
            }
            if (!item.RotHora || item.RotHora < 1) {
                notify('Cada item debe tener un horario seleccionado', 'danger', 3500, 'right');
                return false;
            }
            if (!item.RotDias || item.RotDias < 1) {
                notify('La cantidad de días debe ser mayor a 0', 'danger', 3500, 'right');
                return false;
            }

            if (usedItems.has(item.RotItem)) {
                notify(`El item ${item.RotItem} está repetido`, 'danger', 3500, 'right');
                return false;
            }
            // if (usedHoras.has(item.RotHora)) {
            //     notify(`El horario ${item.RotHora} está repetido dentro de la rotación`, 'danger', 3500, 'right');
            //     return false;
            // }
            usedItems.add(item.RotItem);
            usedHoras.add(item.RotHora);
        }
        return true;
    };

    const notifySaveResponse = (response) => {
        const ok = (response?.RESPONSE_CODE || '').toUpperCase() === '200 OK';
        const data = response?.DATA ?? {};
        const insertados = Array.isArray(data.insertados) ? data.insertados.length : 0;
        const actualizados = Array.isArray(data.actualizados) ? data.actualizados.length : 0;
        const errores = Array.isArray(data.errores) ? data.errores : [];

        if (ok) {
            notify(`Rotación guardada. Nuevas: ${insertados}, actualizadas: ${actualizados}`, 'success', 3500, 'right');
            if (errores.length) {
                notify(`Se detectaron ${errores.length} error(es): ${errores[0]?.error || ''}`, 'warning', 5000, 'right');
            }
            return true;
        }

        const firstErr = errores[0]?.error || '';
        notify(firstErr || response?.MESSAGE || 'No se pudo guardar la rotación', 'danger', 4500, 'right');
        return false;
    };

    const notifyDeleteResponse = (response) => {
        const ok = (response?.RESPONSE_CODE || '').toUpperCase() === '200 OK';
        const data = response?.DATA ?? {};
        const eliminados = Array.isArray(data.eliminados) ? data.eliminados.length : 0;
        const errores = Array.isArray(data.errores) ? data.errores : [];

        if (ok) {
            notify(`Rotación eliminada (${eliminados})`, 'success', 3000, 'right');
            if (errores.length) {
                notify(`Se detectaron ${errores.length} error(es): ${errores[0]?.error || ''}`, 'warning', 5000, 'right');
            }
            return true;
        }

        const firstErr = errores[0]?.error || '';
        notify(firstErr || response?.MESSAGE || 'No se pudo eliminar la rotación', 'danger', 4500, 'right');
        return false;
    };

    const confirmDeleteRotacionModal = (label) => {
        const $modal = $('#modal-confirm-delete-rotacion');
        const $text = $modal.find('#confirm-delete-rotacion-text');
        const $confirmBtn = $modal.find('#btn-confirm-delete-rotacion');

        $text.text(`¿Confirma eliminar la rotación ${label}?`);

        return new Promise((resolve) => {
            let resolved = false;

            const cleanup = () => {
                $confirmBtn.off('click.confirmDeleteRotacion');
                $modal.off('hidden.bs.modal.confirmDeleteRotacion');
            };

            $confirmBtn.off('click.confirmDeleteRotacion').on('click.confirmDeleteRotacion', function () {
                resolved = true;
                cleanup();
                $modal.modal('hide');
                resolve(true);
            });

            $modal.off('hidden.bs.modal.confirmDeleteRotacion').on('hidden.bs.modal.confirmDeleteRotacion', function () {
                cleanup();
                if (!resolved) {
                    resolve(false);
                }
            });

            $modal.modal('show');
        });
    };

    const deleteRotacionByRow = async (row, $triggerBtn) => {
        if (!row) {
            notify('No se encontró la rotación a eliminar', 'danger', 3500, 'right');
            return;
        }
        if (!window.axios || typeof window.axios.delete !== 'function') {
            notify('No se encontró axios para eliminar la rotación', 'danger', 4500, 'right');
            return;
        }

        const label = `(${row?.RotCodi ?? ''}) ${row?.RotDesc ?? ''}`.trim();
        const confirmed = await confirmDeleteRotacionModal(label);
        if (!confirmed) return;

        const $btn = $triggerBtn && $triggerBtn.length ? $triggerBtn : null;
        if ($btn) {
            $btn.prop('disabled', true).addClass('disabled');
        }

        try {
            const res = await window.axios.delete(ENDPOINT_ROTACION, {
                headers: {
                    'Content-Type': 'application/json',
                },
                data: [{ RotCodi: toInt(row.RotCodi, 0) }],
            });

            const ok = notifyDeleteResponse(res?.data ?? {});
            if (ok && tablaRotaciones) {
                tablaRotaciones.ajax.reload(null, false);
            }
        } catch (err) {
            const text = err?.response?.data?.message || err?.response?.data?.MESSAGE || 'Error al eliminar la rotación';
            notify(text, 'danger', 4500, 'right');
        } finally {
            if ($btn) {
                $btn.prop('disabled', false).removeClass('disabled');
            }
        }
    };

    const openImportarRotacionesModal = () => {
        if (!$modalImportarRotaciones.length) {
            notify('No se encontró el modal de importación de rotaciones', 'danger', 4500, 'right');
            return;
        }
        const $fileInput = $modalImportarRotaciones.find('#import-rotaciones-file');
        const $fileName = $modalImportarRotaciones.find('#selected-rotaciones-file-name');
        const $alert = $modalImportarRotaciones.find('#import-rotaciones-alert');
        const $alertList = $modalImportarRotaciones.find('#import-rotaciones-alert-list');

        if ($fileInput.length) {
            $fileInput.val('');
        }
        $fileName.text('Ningún archivo seleccionado');
        $alert.addClass('d-none');
        $alertList.html('');
        $modalImportarRotaciones.modal('show');
    };

    const renderImportWarnings = (warnings = []) => {
        const $alert = $modalImportarRotaciones.find('#import-rotaciones-alert');
        const $alertList = $modalImportarRotaciones.find('#import-rotaciones-alert-list');

        if (!warnings.length) {
            $alert.addClass('d-none');
            $alertList.html('');
            return;
        }

        const html = warnings.slice(0, 50).map(function (w) {
            const fila = w?.fila ? `Fila ${w.fila}: ` : '';
            return `<li>${fila}${w?.mensaje || 'Advertencia sin detalle'}</li>`;
        }).join('');

        $alertList.html(html);
        $alert.removeClass('d-none');
    };

    const notifyImportResponse = (response) => {
        if (response?.status === 'ok') {
            const respApi = response?.response ?? {};
            const data = respApi?.DATA ?? {};
            const insertados = Array.isArray(data.insertados) ? data.insertados.length : 0;
            const actualizados = Array.isArray(data.actualizados) ? data.actualizados.length : 0;
            const errores = Array.isArray(data.errores) ? data.errores : [];

            notify(`Importación finalizada. Creadas: ${insertados}, actualizadas: ${actualizados}`, 'success', 5000, 'right');
            if (errores.length) {
                notify(`La importación arrojó ${errores.length} error(es): ${errores[0]?.error || ''}`, 'warning', 7000, 'right');
            }
            return true;
        }

        notify(response?.MESSAGE || response?.message || 'No se pudo importar el archivo', 'danger', 6000, 'right');
        return false;
    };

    const importRotacionesXls = async ($btnImportar) => {
        if (!window.axios || typeof window.axios.post !== 'function') {
            notify('No se encontró axios para importar rotaciones', 'danger', 4500, 'right');
            return;
        }

        const $fileInput = $modalImportarRotaciones.find('#import-rotaciones-file');
        const file = $fileInput?.[0]?.files?.[0];

        if (!file) {
            notify('Seleccione un archivo .xls o .xlsx', 'danger', 4500, 'right');
            return;
        }

        const ext = String(file.name || '').toLowerCase();
        if (!(/\.(xls|xlsx)$/i).test(ext)) {
            notify('Formato inválido. Solo se aceptan archivos .xls o .xlsx', 'danger', 4500, 'right');
            return;
        }

        const $btn = $btnImportar && $btnImportar.length ? $btnImportar : null;
        if ($btn) {
            $btn.prop('disabled', true).addClass('disabled');
        }

        const formData = new FormData();
        formData.append('archivo', file);

        try {
            const res = await window.axios.post(ENDPOINT_ROTACION_IMPORT_XLS, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });

            const ok = notifyImportResponse(res?.data ?? {});
            if (ok) {
                $fileInput.val('');
                $modalImportarRotaciones.modal('hide');
                if (tablaRotaciones) {
                    tablaRotaciones.ajax.reload(null, false);
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

    const exportRotacionesXls = async ($btnExportar) => {
        if (!window.axios || typeof window.axios.post !== 'function') {
            notify('No se encontró axios para exportar rotaciones', 'danger', 4500, 'right');
            return;
        }

        const $btn = $btnExportar && $btnExportar.length ? $btnExportar : null;
        if ($btn) {
            $btn.prop('disabled', true).addClass('disabled');
        }

        notify('Generando archivo XLS, aguarde por favor...', 'dark', 0, 'right');
        try {
            const resExport = await window.axios.post(ENDPOINT_ROTACION_EXPORT_XLS, {}, {
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            const archivo = resExport?.data?.archivo ?? '';
            if (!archivo) {
                throw new Error('No se pudo generar el archivo XLS');
            }

            const cleanPath = String(archivo ?? '').replace(/^\/+/, '');
            window.open(`/${homehost}/${cleanPath}`, '_blank');
        } catch (err) {
            const text = err?.response?.data?.message || err?.response?.data?.mensaje || err?.message || 'Error al exportar rotaciones';
            notify(text, 'danger', 4500, 'right');
        } finally {
            if ($btn) {
                $btn.prop('disabled', false).removeClass('disabled');
            }
        }
    };

    const exportRotacionesEjemploXls = async ($btnExportar) => {
        if (!window.axios || typeof window.axios.post !== 'function') {
            notify('No se encontró axios para exportar el ejemplo', 'danger', 4500, 'right');
            return;
        }

        const $btn = $btnExportar && $btnExportar.length ? $btnExportar : null;
        if ($btn) {
            $btn.prop('disabled', true).addClass('disabled');
        }

        try {
            const resExport = await window.axios.post(ENDPOINT_ROTACION_EXPORT_XLS_EJEMPLO, {}, {
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

    const openUnusedRotacionesModal = () => {
        if (!$modalUnusedRotaciones.length) {
            notify('No se encontró el modal de rotaciones no utilizadas', 'danger', 4500, 'right');
            return;
        }

        initTablaRotacionesUnused();
        $modalUnusedRotaciones.modal('show');
    };

    const deleteRotacionUnused = async ($triggerBtn) => {
        if (!window.axios || typeof window.axios.delete !== 'function') {
            notify('No se encontró axios para eliminar rotaciones no utilizadas', 'danger', 4500, 'right');
            return;
        }

        const $btn = $triggerBtn && $triggerBtn.length ? $triggerBtn : null;
        if ($btn) {
            $btn.prop('disabled', true).addClass('disabled');
        }

        try {
            const res = await window.axios.delete(ENDPOINT_ROTACION_UNUSED, {
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            const response = res?.data ?? {};
            const ok = (response?.RESPONSE_CODE || '').toUpperCase() === '200 OK';
            const eliminados = Array.isArray(response?.DATA?.eliminados) ? response.DATA.eliminados.length : 0;

            if (ok) {
                notify(eliminados > 0 ? `Rotaciones eliminadas (${eliminados})` : (response?.MESSAGE || 'No hay rotaciones para eliminar'), 'success', 4000, 'right');
                if (tablaRotacionesUnused) {
                    tablaRotacionesUnused.ajax.reload(null, false);
                }
                if (tablaRotaciones) {
                    tablaRotaciones.ajax.reload(null, false);
                }
            } else {
                notify(response?.MESSAGE || 'No se pudieron eliminar las rotaciones no usadas', 'danger', 4500, 'right');
            }
        } catch (err) {
            const text = err?.response?.data?.message || err?.response?.data?.MESSAGE || 'Error al eliminar rotaciones no usadas';
            notify(text, 'danger', 4500, 'right');
        } finally {
            if ($btn) {
                $btn.prop('disabled', false).removeClass('disabled');
            }
        }
    };

    const initTablaRotacionesUnused = () => {
        if (tablaRotacionesUnused && $.fn.DataTable.isDataTable('#tblUnusedRotaciones')) {
            tablaRotacionesUnused.ajax.reload(null, false);
            return tablaRotacionesUnused;
        }

        const $btnDeleteConfirm = $('#btn-confirm-unused-rotacion');

        tablaRotacionesUnused = $('#tblUnusedRotaciones').DataTable({
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            bProcessing: false,
            serverSide: false,
            deferRender: true,
            ajax: {
                url: ENDPOINT_ROTACION_UNUSED,
                type: 'GET',
                dataSrc: function (json) {
                    const rows = Array.isArray(json) ? json : (Array.isArray(json?.DATA) ? json.DATA : (Array.isArray(json?.data) ? json.data : []));
                    if (!rows.length) {
                        $btnDeleteConfirm.prop('disabled', true).addClass('disabled');
                    } else {
                        $btnDeleteConfirm.prop('disabled', false).removeClass('disabled');
                    }
                    return rows;
                },
            },
            columns: [
                { data: 'RotCodi', title: 'Código' },
                { data: 'RotDesc', title: 'Descripción', className: 'w-100' },
            ],
            paging: true,
            info: true,
            searching: true,
            ordering: false,
            language: DT_SPANISH_2,
        });

        tablaRotacionesUnused.on('init.dt', function () {
            $('#tblUnusedRotaciones_filter input').attr('placeholder', 'Buscar').removeClass('form-control-sm');
            $('#modal-unused-rotaciones').find('.table-responsive').show();
            $('#modal-unused-rotaciones').off('click.deleteUnused').on('click.deleteUnused', '#btn-confirm-unused-rotacion', function () {
                deleteRotacionUnused($(this));
            });
        });
        
        tablaRotacionesUnused.on('draw.dt', function () {
            $('#tblUnusedRotaciones').removeClass('loader-in');
        });

        tablaRotacionesUnused.on('page.dt', function (e, settings) {
            $('#tblUnusedRotaciones').addClass('loader-in');
        });

        $modalUnusedRotaciones.off('shown.bs.modal.unusedTable').on('shown.bs.modal.unusedTable', function () {
            if (tableRotacionesUnused) {
                tableRotacionesUnused.columns.adjust().draw(false);
            }
        });

        tableRotacionesUnused = tablaRotacionesUnused;
        return tablaRotacionesUnused;
    };

    const bindModalActions = () => {
        $modalRotacion.off('shown.bs.modal.select2RotHora').on('shown.bs.modal.select2RotHora', function () {
            initRotHoraSelect2();
        });

        $modalRotacion.off('click.addItem').on('click.addItem', '#btn-add-item-rotacion', function () {
            appendItemRow({ RotItem: getNextRotItem() });
        });

        $modalRotacion.off('click.removeItem').on('click.removeItem', '.btn-remove-item', function () {
            const $rows = $modalRotacion.find('#tbl-rotacion-items tbody tr');
            if ($rows.length <= 1) {
                notify('Debe existir al menos un item en la rotación', 'warning', 2500, 'right');
                return;
            }
            $(this).closest('tr').remove();
            refreshItemIndexes();
        });

        $modalImportarRotaciones.off('change.file').on('change.file', '#import-rotaciones-file', function () {
            const fileName = this.files && this.files[0] ? this.files[0].name : 'Ningún archivo seleccionado';
            $modalImportarRotaciones.find('#selected-rotaciones-file-name').text(fileName);
            renderImportWarnings([]);
        });
    };

    const bindSaveRotacionAction = () => {
        $modalRotacion.off('click.saveRotacion').on('click.saveRotacion', '#btn-save-rotacion', function () {
            const $btn = $(this);
            if ($btn.prop('disabled')) return;

            const payload = getRotacionPayload();
            if (!validateRotacionPayload(payload)) return;

            if (!window.axios || typeof window.axios.post !== 'function') {
                notify('No se encontró axios para enviar el formulario', 'danger', 4500, 'right');
                return;
            }

            $btn.prop('disabled', true).text('Aguarde');

            window.axios.post(ENDPOINT_ROTACION, [payload], {
                headers: {
                    'Content-Type': 'application/json',
                },
            }).then(function (res) {
                const ok = notifySaveResponse(res?.data ?? {});
                if (!ok) return;

                $modalRotacion.modal('hide');
                if (tablaRotaciones) {
                    tablaRotaciones.ajax.reload(null, false);
                }
            }).catch(function (err) {
                const text = err?.response?.data?.message || err?.response?.data?.MESSAGE || 'Error al guardar la rotación';
                notify(text, 'danger', 4500, 'right');
            }).finally(function () {
                $btn.prop('disabled', false).text(saveButtonDefaultText);
            });
        });
    };

    const initTablaRotaciones = () => {
        const table = $('#tblRotaciones').DataTable({
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            bProcessing: true,
            serverSide: true,
            deferRender: true,
            dom: renderDomTableRotaciones(),
            ajax: {
                url: ENDPOINT_ROTACIONES,
                type: 'POST',
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
            $('#tblRotaciones thead').remove();

            const $tblRotacionesLength = $('#tblRotaciones_length');
            const $tblRotacionesPaginate = $('#tblRotaciones_paginate');
            const $inputFilter = $('#tblRotaciones_filter input');
            $('#tblRotaciones_filter').prepend('<label class="font08 mb-1"><span class="d-none d-sm-block">Buscar:</span></label>');
            const htmlOpt = renderOpt;

            $inputFilter.attr('placeholder', 'Código / Descripcion').removeClass('form-control-sm');
            $('#table-opt-rotacion').prepend(htmlOpt);

            const htmlNuevaRotacion = renderOptNuevaRotacion();
            const htmlOptExport = renderOptExport();
            $tblRotacionesLength.addClass(['mt-1', 'h40']);
            $tblRotacionesPaginate.addClass('h40');
            $inputFilter.addClass(['mt-2 mt-sm-0']);

            const $optTbl = $('#table-opt-rotacion .opt-tbl');

            $optTbl.append(htmlNuevaRotacion);
            $('#div-nueva-rotacion').append(htmlOptExport);

            $('#tblRotaciones tbody').on('click', '.btn-editar-rotacion', function () {
                const dataCodi = $(this).data('codi');
                const dataRow = getDataRowByCodi(table, dataCodi);
                editarRotacion(dataRow);
            });

            $('.opt-tbl').off('click.newRotacion').on('click.newRotacion', '#btn-nueva-rotacion', function () {
                openCreateRotacionModal();
            });

            $('.opt-tbl').off('click.exportRotacion').on('click.exportRotacion', '#btn-exportar-xls-rotacion', function () {
                exportRotacionesXls($(this));
            });

            $('.opt-tbl').off('click.importRotacion').on('click.importRotacion', '#btn-importar-xls-rotacion', function () {
                openImportarRotacionesModal();
            });

            $('.opt-tbl').off('click.unusedRotacion').on('click.unusedRotacion', '#btn-eliminar-unused-rotacion', function () {
                openUnusedRotacionesModal();
            });

            $modalImportarRotaciones.off('click.importarRotaciones');
            $modalImportarRotaciones.on('click.importarRotaciones', '#btn-descargar-ejemplo-importar-rotaciones-xls', function () {
                exportRotacionesEjemploXls($(this));
            });
            $modalImportarRotaciones.on('click.importarRotaciones', '#btn-importar-rotaciones-xls-modal', function () {
                importRotacionesXls($(this));
            });

            $('#tblRotaciones').on('click', '.btn-duplicar-rotacion', function () {
                const dataCodi = $(this).data('codi');
                const dataRow = getDataRowByCodi(table, dataCodi);
                openCreateRotacionModal(dataRow);
            });

            $('#tblRotaciones').on('click', '.btn-eliminar-rotacion', function () {
                const dataCodi = $(this).data('codi');
                const dataRow = getDataRowByCodi(table, dataCodi);
                deleteRotacionByRow(dataRow, $(this));
            });

            $('#tblRotaciones').show();
        });

        table.on('draw.dt', function () {
            $('#tblRotaciones').removeClass('loader-in');
        });

        table.on('page.dt', function (e, settings) {
            $('#tblRotaciones').addClass('loader-in');
        });

        tablaRotaciones = table;
        return table;
    };

})(jQuery);
