(function ($) {
    'use strict';

    $(document).ready(function () {
        initTablaNovedades();
    });

    const homehost = $("#_homehost").val();
    const ENDPOINT_NOVEDADES = '/' + homehost + '/app-data/datatables/novedades';
    const ENDPOINT_NOVEDAD_EXISTE = '/' + homehost + '/app-data/novedad/existe';
    const ENDPOINT_NOVECAUSA = '/' + homehost + '/app-data/novecausa';
    const ENDPOINT_NOVEDAD1 = '/' + homehost + '/app-data/novedad1';
    const ENDPOINT_NOVEDAD2 = '/' + homehost + '/app-data/novedad2';
    const ENDPOINT_HORAS = '/' + homehost + '/app-data/horas';
    const ENDPOINT_NOVEDAD_CONFIG = '/' + homehost + '/app-data/novedad-config';
    const LS_REFRESH_TABLE_NOVEDADES = 'refresh_novedades';
    const DIALOG_CAUSA = document.querySelector('#dialogCausa');
    const DIALOG_NOVEDAD1 = document.querySelector('#dialogNovedad1');
    const DIALOG_HORAS = document.querySelector('#dialogHoras');
    const DIALOG_NUEVA_NOVEDAD = document.querySelector('#dialogNuevaNovedad');
    const DIALOG_ELIMINAR_NOVEDAD = document.querySelector('#dialogEliminarNovedad');

    ls.set(LS_REFRESH_TABLE_NOVEDADES, false);
    const REFRESH_TABLE_NOVEDADES = () => ls.get(LS_REFRESH_TABLE_NOVEDADES);

    const notAusenciaPermitted = document.querySelectorAll('.not-ausencia-permitted');
    const MODAL_NOVEDAD = $('#modal-novedad');

    MODAL_NOVEDAD.on('hidden.bs.modal', function () {
        if (!REFRESH_TABLE_NOVEDADES()) return;
        initTablaNovedades();
        ls.set(LS_REFRESH_TABLE_NOVEDADES, false);
    });
    MODAL_NOVEDAD.on('hide.bs.modal', function () {
        // Si el elemento activo está dentro del modal, quítale el foco
        if (document.activeElement && this.contains(document.activeElement)) {
            document.activeElement.blur();
        }
    });

    const $tabCausas = document.querySelector('#tab-causas');
    const $tabCausasCount = document.querySelector('#tab-causas-count');

    const $tabControles = document.querySelector('#tab-controles');

    const $tabNovedadesAdicionales = document.querySelector('#tab-novedades-adicionales');
    const $tabNovedad1Count = document.querySelector('#tab-novedad1-count');
    const $tabHorasCount = document.querySelector('#tab-horas-count');

    const $tabNovedadesAdicionalesHoras = document.querySelector('#tab-novedades-adicionales-horas');
    let currentNovecausaCodi = null;
    let currentNovedad1Codi = null;
    let currentNovedad2Codi = null;
    let currentMainNovedadCodi = null;
    let novedad1NovCoAdCache = [];
    let novedad1NovCoAdLoadedFor = null;
    let horasTiposCache = [];

    MODAL_NOVEDAD.on('hidden.bs.modal', function () {
        currentMainNovedadCodi = null;
        novedad1NovCoAdCache = [];
        novedad1NovCoAdLoadedFor = null;
    });

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

    const initHorColorPicker = () => {
        const $horColor = $('#NovColor');
        if (!$horColor.length || !window.jscolor) return;

        // Preserva el valor actual (cargado desde datos) antes de instalar/configurar jscolor.
        const preservedColor = normalizeColorHex($horColor.val());

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
            if (!$horColor[0].jscolor) {
                window.jscolor.install($horColor[0]);
            }
        }

        const picker = $horColor[0].jscolor;
        const defaultPreset = window.jscolor && window.jscolor.presets ? window.jscolor.presets.default : null;
        if (picker && defaultPreset && typeof picker.option === 'function') {
            Object.keys(defaultPreset).forEach(function (key) {
                picker.option(key, defaultPreset[key]);
            });
        }

        $horColor.val(preservedColor);
        if (picker && typeof picker.fromString === 'function') {
            picker.fromString(preservedColor);
        }
    };
    // Renderiza badge con el color del horario
    const renderColorBadge = (row) => {
        return `<span class="badge radius w10 h10 border" style="background-color:${row.Color}; color:${row.ColorText};">&nbsp;</span>`;
    };

    const buildColumns = () => {

        const colCodi = {
            data: 'NovCodi', className: '', targets: '', title: 'Cód.',
            visible: true,
            render: function (data, type) {
                // if (type !== 'display') return '';
                return data;
            },
        };

        const colDesc = {
            data: 'NovDesc', className: '', targets: '', title: 'Descripción',
            visible: true,
            render: function (data, type, row) {
                // if (type !== 'display') return '';
                const NovTiCoDescString = row.NovTiCo === '0' ? '' : row.NovTiCoDesc;
                return `<div class="hint--top" aria-label="${NovTiCoDescString}" style="min-width: 250px;">
                    <div class="d-flex flex-column"><div>${data}</div><div class="font08 text-muted text-truncate" style="max-width: 250px;">${NovTiCoDescString}</div></div>
                </div>`;
            },
        };

        const novID = {
            data: 'NovID', className: '', targets: '', title: 'ID',
            visible: true,
            render: function (data, type) { if (type !== 'display') return ''; return data; },
        };

        const novTipo = {
            data: 'NovTipoDesc', className: '', targets: '', title: 'Tipo',
            visible: true,
            render: function (data, type) { if (type !== 'display') return ''; return data; },
        };

        const colColor = {
            data: 'Color', className: 'pt-3', targets: '', title: '',
            orderable: false,
            visible: true,
            render: function (data, type, row) { if (type !== 'display') return ''; return renderColorBadge(row); },
        };

        const novCausas = {
            data: 'NovCausas', className: 'text-center', targets: '', title: 'Causas',
            orderable: false,
            visible: true,
            render: function (data, type) { if (type !== 'display') return ''; return data; },
        };

        const novTiCo = {
            data: 'NovTiCo', className: 'text-center', targets: '', title: '',
            orderable: false,
            visible: true,
            render: function (data, type, row) {
                if (type !== 'display') return '';
                const novTiCoString = (data) => data === '0' ? '' : `<span class="pointer hint--top" aria-label="${row.NovTiCoDesc}"><i class="bi bi-info-circle"></i></span>`;
                return novTiCoString(data);
            },
        };

        const renderLiquidacion = (col1, col2) => {
            return `<div class="d-flex flex-row gap8"><input class="border form-control form-control-sm px-2 w60" disabled value="${col1}" title="${col1}" readonly> <input class="border form-control form-control-sm px-2 w60" disabled value="${col2}" title="${col2}" readonly></div>`;
        };

        const liqMens = {
            data: null, className: '', targets: '', title: 'Liq. Mensuales',
            orderable: false,
            visible: true,
            render: function (data, type, row) {
                if (type !== 'display') return '';
                return renderLiquidacion(row.NovCoL1, row.NovCoL2);
            },
        };
        const NovColor = {
            data: 'NovColor',
            visible: false,
            render: function (data) {
                return '';
            },
        };
        const NovCoL1 = {
            data: 'NovCoL1',
            visible: false,
            render: function (data) {
                return '';
            },
        };
        const NovCoL2 = {
            data: 'NovCoL2',
            visible: false,
            render: function (data) {
                return '';
            },
        };
        const NovCoL3 = {
            data: 'NovCoL3',
            visible: false,
            render: function (data) {
                return '';
            },
        };
        const NovCoL4 = {
            data: 'NovCoL4',
            visible: false,
            render: function (data) {
                return '';
            },
        };
        const NovTiCo = {
            data: 'NovTiCo',
            visible: false,
            render: function (data) {
                return '';
            },
        };

        const liqJornal = {
            data: null, className: '', targets: '', title: 'Liq. Jornales',
            orderable: false,
            visible: true,
            render: function (data, type, row) {
                if (type !== 'display') return '';
                return renderLiquidacion(row.NovCoL3, row.NovCoL4);
            },
        };
        const TieneFichas = {
            data: 'TieneFichas', className: '', targets: '', title: 'Tiene Fichas',
            orderable: false,
            visible: false,
            render: function (data, type, row) {
                return data;
            },
        };

        const accions = {
            data: null, className: 'w-100 text-right', targets: '', title: '',
            orderable: false,
            visible: true,
            render: function (data, type, row) {
                if (type !== 'display') return '';
                const tieneFichas = row.TieneFichas === "1" ? "disabled" : "";
                const labelEliminar = tieneFichas ? 'No se puede eliminar' : 'Eliminar';
                return `<div class="float-right border p-1 fit-content rounded d-flex justify-content-center align-items-center gap5">
                    <button class="hint--top btn btn-sm btn-outline-custom border-0 btn-editar-novedad" data-codi="1" title="Editar" aria-label="Editar"><i class="bi bi-pen"></i></button>
                    <button class="hint--left btn btn-sm btn-outline-custom border-0 btn-eliminar-novedad" data-codi="1" title="${labelEliminar}" aria-label="${labelEliminar}" ${tieneFichas}><i class="bi bi-trash"></i></button>
                </div>`;
            },
        };

        return [colCodi, novID, colColor, colDesc, novTipo, novCausas, liqMens, NovTiCo, NovColor, NovCoL1, NovCoL2, NovCoL3, NovCoL4, liqJornal, accions, TieneFichas];
    };

    //---------------- INICIALIZAR TABLA NOVEDADES ------------- //

    const renderDomTableNovedades = () => {
        return `
                <'row mb-2'
                    <'col-sm-12 col-md-6 d-inline-flex align-items-center justify-content-start gap5'lp>
                    <'col-sm-12 col-md-6'f>
                >
                <'row 
                    '<'col-12 table-responsive'<'text-nowrap'rt>>
                >
                <'row'
                    <'col-sm-12 col-md-5'i>
                    <'col-sm-12 col-md-7'p>
                >`;
    };

    const initTablaNovedades = () => {

        const $tblNovedaes = $('#tblNovedades');
        $tblNovedaes.addClass('loader-in');

        if ($.fn.DataTable.isDataTable($tblNovedaes)) {
            const table = $tblNovedaes.DataTable();
            notify('Recargando novedades...')
            table.ajax.reload(null, false);
            return table;
        }

        $tblNovedaes.off('.novedadActions');

        $tblNovedaes.empty();

        const table = $('#tblNovedades').DataTable({
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            bProcessing: false,
            serverSide: true,
            deferRender: true,
            dom: renderDomTableNovedades(),
            ajax: {
                url: ENDPOINT_NOVEDADES,
                type: 'POST',
                data: function (d) {
                    d.AllColumns = 1;
                    return d;
                },
            },
            createdRow: function (row, data, dataIndex) {
                $(row).addClass('fadeIn');
                // $(row).find('td').addClass('align-middle');
            },
            columns: buildColumns(),
            paging: true,
            info: true,
            searching: true,
            ordering: false,
            // pageLength: 25,
            // lengthChange: false,
            stateSave: true,
            language: DT_SPANISH_2,
        });

        $tblNovedaes.on('click.novedadActions', 'tbody .btn-editar-novedad', async function (event) {
            const data = table.row($(this).closest('tr')).data();
            $(this).prop('disabled', true);
            await getNovedadByID(data.NovCodi, $(this));
        });

        $tblNovedaes.on('click.novedadActions', 'tbody .btn-eliminar-novedad', async function (event) {
            const data = table.row($(this).closest('tr')).data();
            $(this).prop('disabled', true);
            await deleteNovedadByID(data.NovCodi, data.NovDesc, $(this));
        });

        $('#tblNovedades').on('click', '.btn-eliminar-novedad', async function () {
        });

        table.on('init.dt', function (e, settings) {

            const $tblNovedadesLength = $('#tblNovedades_length');
            const $tblNovedadesPaginate = $('#tblNovedades_paginate');
            const $inputFilter = $('#tblNovedades_filter input');
            const $lengthChange = $('#tblNovedades_length');

            $inputFilter.attr('placeholder', 'Código / Descripción').removeClass('form-control-sm');

            $tblNovedadesLength.addClass(['mt-1', 'h40']);
            $tblNovedadesPaginate.addClass('h40');
            $inputFilter.addClass(['mt-2 mt-sm-0']);
            select2Simple($lengthChange.find('select'), 'Seleccionar', false, false, '60px');

            $('#tblNovedades').show();

        });

        table.on('draw.dt', function (e, settings) {
            $.notifyClose();
            $('#tblNovedades').removeClass('loader-in');
            // contar la cantidad de filas visibles
            const visibleRows = table.rows({ filter: 'applied' }).nodes().length;
            const currentPage = settings._iDisplayStart / settings._iDisplayLength + 1;
            if (visibleRows === 0 && currentPage !== 1) {
                setTimeout(() => {
                    table.ajax.reload();
                }, 100);
            }
        });

        table.on('page.dt', function (e, settings) {
            $('#tblNovedades').addClass('loader-in');
        });

        return table;
    };

    //---------------- INICIALIZAR TABLA NOVEDADES CAUSAS ---------------- //

    const buildColumnsCausa = () => {
        const NovCCodi = {
            data: 'NovCCodi',
            visible: true,
            title: 'Código',
            render: function (data) {
                return data;
            },
        };
        const NovCDesc = {
            data: 'NovCDesc',
            visible: true,
            title: 'Descripción',
            render: function (data) {
                return data;
            },
        };
        const accions = {
            data: null, className: 'w-100 text-right', targets: '', title: '',
            orderable: false,
            visible: true,
            render: function (data, type, row) {
                if (type !== 'display') return '';
                const tieneFichas = row.TieneFichas === "1" ? "disabled" : "";
                const labelEliminar = 'Eliminar';
                return `<div class="float-right border p-1 fit-content rounded d-flex justify-content-center align-items-center gap5">
                    <button class="hint--top btn btn-sm btn-outline-custom border-0 btn-editar-novedad" data-codi="1" title="Editar" aria-label="Editar"><i class="bi bi-pen"></i></button>
                    <button class="hint--left btn btn-sm btn-outline-custom border-0 btn-eliminar-novedad" data-codi="1" title="${labelEliminar}" aria-label="${labelEliminar}"><i class="bi bi-trash"></i></button>
                </div>`;
            },
        };

        return [NovCCodi, NovCDesc, accions];
    };

    const renderDomTableNoveCausa = () => {
        return `
                <'row mb-2'
                    <'col-sm-12 col-md-6 d-inline-flex align-items-center justify-content-start gap5'>
                    <'col-sm-12 col-md-6'>
                >
                <'row 
                    '<'col-12 table-responsive'<'text-nowrap'rt>>
                >
                <'row'
                    <'col-sm-12 col-md-5'i>
                    <'col-sm-12 col-md-7'p>
                >`;
    };

    const initTablaNovecausa = (NovCodi) => {
        currentNovecausaCodi = NovCodi;
        const $tblCausas = $('#tblCausas');

        if ($.fn.DataTable.isDataTable($tblCausas)) {
            const table = $tblCausas.DataTable();
            table.ajax.url(ENDPOINT_NOVECAUSA + "/" + NovCodi).load(null, false);
            return table;
        }

        $tblCausas.off('.novecausaActions');

        $tblCausas.empty();

        const table = $tblCausas.DataTable({
            dom: renderDomTableNoveCausa(),
            ajax: {
                url: ENDPOINT_NOVECAUSA + "/" + NovCodi,
                type: 'GET',
                data: function (d) {
                    return d;
                },
                dataSrc: 'DATA'
            },
            createdRow: function (row, data, dataIndex) {
                $(row).find('td').addClass('align-middle');
            },

            columns: buildColumnsCausa(),
            paging: false,
            ordering: false,
            info: false,
            language: DT_SPANISH_2,
        });

        table.on('init.dt', function (e, settings) {

            const $inputFilter = $('#tblCausas_filter input');
            $inputFilter.attr('placeholder', 'Buscar').removeClass('form-control-sm');
            $tblCausas.show();

            const $dialogCausa = document.getElementById('dialogCausa');

            const cancelButton = document.getElementById('cancel-dialog-causa');
            cancelButton.onclick = function () {
                $dialogCausa.close();
            };
        });

        table.on('draw.dt', function () {
            $tblCausas.removeClass('loader-in');
            const visibleRows = table.rows({ filter: 'applied' }).nodes().length;
            $tabCausasCount.textContent = visibleRows;

            if (visibleRows === 0) {
                const colspan = $tblCausas.find('thead th').length;
                $tblCausas.find('tbody').html('<tr><td colspan="' + colspan + '" class="text-center pt-3">No se encontraron registros</td></tr>');
            }
        });

        table.on('page.dt', function (e, settings) {
            $tblCausas.addClass('loader-in');
        });

        $(document).off('click.novecausaActions', '#btnAgregarCausa');
        $(document).on('click.novecausaActions', '#btnAgregarCausa', function (event) {
            const rowData = { NovCCodi: '', NovCDesc: '', NovCNove: currentNovecausaCodi };
            openDialogNoveCausa(rowData, 'create');
        });

        $tblCausas.on('click.novecausaActions', 'tbody .btn-editar-novedad', function (event) {
            const rowData = table.row($(this).closest('tr')).data();
            if (!rowData) return;
            openDialogNoveCausa(rowData, 'edit');
        });

        $tblCausas.on('click.novecausaActions', 'tbody .btn-eliminar-novedad', function (event) {
            const rowData = table.row($(this).closest('tr')).data();
            if (!rowData) return;
            openDialogNoveCausa(rowData, 'delete');
        });


        return table;
    };

    const openDialogNoveCausa = (rowData, type = 'edit') => {

        const NovCCodi = rowData.NovCCodi;
        const NovCDesc = rowData.NovCDesc;
        const NovCNove = rowData.NovCNove ?? currentNovecausaCodi;

        const esCreate = type === 'create';
        const esDelete = type === 'delete';

        if (!NovCNove || (!esCreate && (!NovCCodi || !NovCDesc))) {
            console.error('Faltan datos necesarios para editar la causa');
            return;
        }

        const acceptButton = document.getElementById('accept-dialog-causa');

        openDialog('dialogCausa', acceptButton);

        $('#ENovCodi').val(NovCCodi || '');
        $('#ENovDesc').val(NovCDesc || '');
        $('#ENovCodi').prop('disabled', !esCreate);
        $('#ENovDesc').prop('disabled', esDelete);

        const titulo = esCreate ? 'Nueva Causa' : (esDelete ? '¿Eliminar Causa?' : 'Editar Causa');
        $('.dialog-header-title').text(titulo);

        // Limitar el campo NovCodi a solo números y máximo 5 caracteres
        $('#ENovCodi').on('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 5);
        });

        esCreate ? $('#ENovCodi').focus() : $('#ENovDesc').focus();

        acceptButton.textContent = esDelete ? 'Eliminar' : 'Aceptar';
        acceptButton.onclick = function () {
            if (type === 'edit') {
                postCausa({ ...rowData, NovCNove }, 'edit');
            } else if (type === 'delete') {
                if (!validarFormularioCausa()) return;
                deleteCausa(rowData.NovCCodi, NovCNove);
            } else if (type === 'create') {
                postCausa({ ...rowData, NovCNove }, 'create');
            }
        };
    }

    async function verificarCausaExistente(rowData) {
        try {
            const res = await fetch(`${ENDPOINT_NOVECAUSA}/${rowData.NovCNove}`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const payload = await res.json();

            // Validación del envelope del backend
            if (payload?.HTTP_STATUS !== 200) {
                console.warn('Respuesta no OK del backend:', payload?.MESSAGE);
                return false;
            }

            const causas = Array.isArray(payload?.DATA) ? payload.DATA : [];
            const causaActual = String($('#ENovCodi').val() ?? '').trim();

            const existe = causas.some(
                causa => String(causa?.NovCCodi ?? '').trim() === causaActual
            );

            if (existe) {
                notify('La causa ya existe', 'warning');
                return true;
            }
            return false;
        } catch (error) {
            console.error('Error al obtener la novedad:', error);
            return false;
        }
    }

    function validarFormularioCausa() {
        const novCCodi = $('#ENovCodi').val()?.toString().trim();
        const novCDesc = $('#ENovDesc').val()?.toString().trim();

        // Campos requeridos
        if (!novCCodi || !novCDesc) {
            notify('Por favor complete todos los campos', 'warning');
            return false;
        }

        if (!/^\d+$/.test(novCCodi)) {
            notify('El código de la causa debe ser un número entero positivo', 'warning');
            $('#ENovCodi').focus();
            return false;
        }

        // Rango del código
        if (parseInt(novCCodi, 10) > 32767) {
            notify('El código de la causa no puede ser mayor a 32767', 'warning');
            return false;
        }

        // Longitud de la descripción
        if (novCDesc.length > 40) {
            notify('La descripción de la causa no puede tener más de 40 caracteres', 'warning');
            return false;
        }

        return true;
    }

    const postCausa = async (rowData, type = 'edit') => {
        const isEdit = type === 'edit';
        const novCodiTarget = rowData?.NovCNove ?? currentNovecausaCodi;
        if (!novCodiTarget) {
            notify('No se pudo identificar la novedad de la causa', 'warning');
            return;
        }
        $.notifyClose();
        if (type === 'create') {
            const existe = await verificarCausaExistente({ ...rowData, NovCNove: novCodiTarget });
            if (existe) return;
        }

        if (!validarFormularioCausa()) return;

        $.notifyClose();
        notify('Guardando causa...', 'info');
        axios.post(ENDPOINT_NOVECAUSA, {
            NovCCodi: isEdit ? rowData.NovCCodi : $('#ENovCodi').val(),
            NovCDesc: $('#ENovDesc').val(),
            NovCNove: novCodiTarget
        }).then(response => {
            const httpStatus = response?.data?.HTTP_STATUS;
            if (httpStatus === 200) {
                ls.set(LS_REFRESH_TABLE_NOVEDADES, true);
                $.notifyClose();
                initTablaNovecausa(novCodiTarget);
                notify(`Causa ${isEdit ? 'actualizada' : 'creada'} correctamente`, 'success');
                DIALOG_CAUSA.close();
            } else {
                $.notifyClose();
                const message = response?.data?.MESSAGE || 'Error desconocido';
                notify('Error: ' + message, 'danger');
                console.log('Error updating causa:', response?.data);
            }
        }).catch(error => {
            $.notifyClose();
            console.log('Error updating causa:', error);
            notify('Error: ' + (error?.response?.data?.MESSAGE || 'Error desconocido'), 'danger');
        });
    };

    const deleteCausa = (NovCCodi, NovCNove) => {
        const novCodiTarget = NovCNove ?? currentNovecausaCodi;
        if (!novCodiTarget) {
            notify('No se pudo identificar la novedad de la causa', 'warning');
            return;
        }
        $.notifyClose();
        notify('Eliminando causa...', 'info');
        axios.delete(`${ENDPOINT_NOVECAUSA}/${novCodiTarget}/${NovCCodi}`)
            .then(response => {
                if (response?.data?.HTTP_STATUS === 200) {
                    ls.set(LS_REFRESH_TABLE_NOVEDADES, true);
                    $.notifyClose();
                    initTablaNovecausa(novCodiTarget);
                    notify('Causa eliminada correctamente', 'success');
                    DIALOG_CAUSA.close();
                } else {
                    $.notifyClose();
                    const message = response?.data?.MESSAGE || 'Error desconocido';
                    notify('Error: ' + message, 'danger');
                }
            })
            .catch(error => {
                $.notifyClose();
                notify('Error: ' + (error?.response?.data?.MESSAGE || 'Error desconocido'), 'danger');
            });
    };
    //----------------  ---------------- //

    //---------------- ALTA RAPIDA NOVEDAD ---------------- //
    const validarFormularioNuevaNovedad = () => {
        const novCodi = String($('#ANovCodi').val() ?? '').trim();
        const novDesc = String($('#ANovDesc').val() ?? '').trim();
        const novTipo = String($('#ANovTipo').val() ?? '').trim();

        if (!/^\d+$/.test(novCodi) || Number(novCodi) <= 0 || Number(novCodi) > 32767) {
            notify('Codigo invalido. Debe estar entre 1 y 32767', 'warning');
            $('#ANovCodi').focus();
            return false;
        }

        if (!novDesc) {
            notify('La descripcion es requerida', 'warning');
            $('#ANovDesc').focus();
            return false;
        }

        if (novDesc.length > 40) {
            notify('La descripcion no puede superar 40 caracteres', 'warning');
            $('#ANovDesc').focus();
            return false;
        }

        if (novTipo === '' || !/^\d+$/.test(novTipo)) {
            notify('El tipo es requerido', 'warning');
            $('#ANovTipo').focus();
            return false;
        }

        return true;
    };

    const verificarNovedadExistenteRapida = async (novCodi) => {
        try {
            const response = await $.ajax({
                url: ENDPOINT_NOVEDADES,
                data: { NovCodi: novCodi },
                method: 'POST',
            });

            const rows = Array.isArray(response?.data) ? response.data : [];
            return rows.length > 0;
        } catch (error) {
            console.error('Error al obtener la novedad:', error);
            notify('No se pudo verificar si la novedad ya existe', 'warning');
            return true;
        }
    };
    const verificarNovedadDescripcionExistente = async (novDesc) => {
        try {
            const response = await $.ajax({
                url: ENDPOINT_NOVEDAD_EXISTE,
                data: { NovDesc: novDesc },
                method: 'POST',
            });
            const rs = response?.DATA ?? {};
            const exists = rs.exists ?? false;
            return exists;
        } catch (error) {
            console.error('Error al obtener la novedad:', error);
            notify('No se pudo verificar si la descripción de novedad ya existe', 'warning');
            return true;
        }
    };

    const postNuevaNovedadRapida = async () => {
        if (!validarFormularioNuevaNovedad()) return;

        const payload = {
            NovCodi: String($('#ANovCodi').val() ?? '').trim(),
            NovDesc: String($('#ANovDesc').val() ?? '').trim(),
            NovTipo: String($('#ANovTipo').val() ?? '').trim(),
        };

        const yaExiste = await verificarNovedadExistenteRapida(payload.NovCodi);
        const yaExisteDesc = await verificarNovedadDescripcionExistente(payload.NovDesc);

        if (yaExiste) {
            notify('La novedad ya existe', 'warning');
            $('#ANovCodi').focus();
            return;
        }

        if (yaExisteDesc) {
            notify('La descripción de la novedad ya existe', 'warning');
            $('#ANovDesc').focus();
            return;
        }

        const $accept = $('#accept-dialog-nueva-novedad');
        $accept.prop('disabled', true);
        $.notifyClose();
        notify('Creando novedad...', 'info');

        axios.post(ENDPOINT_NOVEDAD_CONFIG, payload)
            .then(async (response) => {
                const httpStatus = response?.data?.HTTP_STATUS;
                if (httpStatus !== 200) {
                    notify('Error: ' + (response?.data?.MESSAGE || 'Error desconocido'), 'danger');
                    return;
                }

                ls.set(LS_REFRESH_TABLE_NOVEDADES, true);
                DIALOG_NUEVA_NOVEDAD && DIALOG_NUEVA_NOVEDAD.close();
                await getNovedadByID(payload.NovCodi);
                notify('Novedad creada correctamente', 'success');
            })
            .catch((error) => {
                notify('Error: ' + (error?.response?.data?.MESSAGE || 'Error desconocido'), 'danger');
            })
            .finally(() => {
                $accept.prop('disabled', false);
            });
    };

    const openDialogNuevaNovedad = () => {
        $('#ANovCodi').val('');
        $('#ANovDesc').val('');
        $('#ANovTipo').val('0');

        select2Simple(`#ANovTipo`, 'Seleccionar', false, false, '100%', $('#dialogNuevaNovedad'));

        const acceptButton = document.getElementById('accept-dialog-nueva-novedad');
        const cancelButton = document.getElementById('cancel-dialog-nueva-novedad');

        if (cancelButton) {
            cancelButton.onclick = function () {
                DIALOG_NUEVA_NOVEDAD && DIALOG_NUEVA_NOVEDAD.close();
            };
        }

        if (acceptButton) {
            acceptButton.onclick = function () {
                postNuevaNovedadRapida();
            };
        }

        $('#ANovCodi').on('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 5);
        });
        
        $('.dialog-header-title').text('Nueva Novedad');

        openDialog('dialogNuevaNovedad', acceptButton);
        $('#ANovCodi').focus();
    };

    $(document).off('click.nuevaNovedad', '#btn-nueva-novedad');
    $(document).on('click.nuevaNovedad', '#btn-nueva-novedad', function () {
        openDialogNuevaNovedad();
    });
    //----------------  ---------------- //

    //---------------- INICIALIZAR TABLA NOVEDAD1 ---------------- //
    const getNextNovItem = () => {
        const $tblNovedad1 = $('#tblNovedad1');
        if (!$.fn.DataTable.isDataTable($tblNovedad1)) return 1;

        const table = $tblNovedad1.DataTable();
        const rowsData = table.rows().data().toArray();
        if (!rowsData.length) return 1;

        const maxItem = rowsData.reduce((max, row) => {
            const item = Number(row?.NovItem ?? 0);
            return Number.isFinite(item) ? Math.max(max, item) : max;
        }, 0);

        return maxItem + 1;
    };

    const validarFormularioNovedad1 = (isDelete = false) => {
        const novItem = String($('#N1NovItem').val() ?? '').trim();
        if (!novItem || !/^\d+$/.test(novItem)) {
            notify('El item debe ser numerico', 'warning');
            $('#N1NovItem').focus();
            return false;
        }

        const novItemInt = parseInt(novItem, 10);
        if (novItemInt <= 0 || novItemInt > 32767) {
            notify('El item debe estar entre 1 y 32767', 'warning');
            return false;
        }

        if (isDelete) return true;

        const novCoAd = String($('#N1NovCoAd').val() ?? '').trim();
        const novFact = String($('#N1NovFact').val() ?? '').trim();
        const novDesde = String($('#N1NovDesde').val() ?? '').trim();
        const novHasta = String($('#N1NovHasta').val() ?? '').trim();

        if (!novCoAd || !/^\d+$/.test(novCoAd)) {
            notify('El codigo de novedad debe ser numerico', 'warning');
            $('#N1NovCoAd').focus();
            return false;
        }

        if (parseInt(novCoAd, 10) <= 0 || parseInt(novCoAd, 10) > 32767) {
            notify('El codigo de novedad debe estar entre 1 y 32767', 'warning');
            return false;
        }

        if (!novFact || !/^\d{1,10}(?:\.\d{1,2})?$/.test(novFact)) {
            notify('El factor debe ser numerico con hasta 2 decimales', 'warning');
            $('#N1NovFact').focus();
            return false;
        }

        if (!novDesde || !novHasta) {
            notify('Desde y Hasta son obligatorios', 'warning');
            return false;
        }

        return true;
    };

    const postNovedad1 = async (rowData, type = 'edit') => {
        const novCodiTarget = rowData?.NovCodi ?? currentNovedad1Codi;
        if (!novCodiTarget) {
            notify('No se pudo identificar la novedad base', 'warning');
            return;
        }

        if (!validarFormularioNovedad1(false)) return;

        const payload = {
            NovCodi: novCodiTarget,
            NovItem: $('#N1NovItem').val(),
            NovDia: $('#N1NovDia').val(),
            NovMeJo: $('#N1NovMeJo').val(),
            NovCoAd: $('#N1NovCoAd').val(),
            NovFact: $('#N1NovFact').val(),
            NovDesde: $('#N1NovDesde').val(),
            NovHasta: $('#N1NovHasta').val(),
        };

        $.notifyClose();
        notify('Guardando novedad adicional...', 'info');

        axios.post(ENDPOINT_NOVEDAD1, payload)
            .then(response => {
                const httpStatus = response?.data?.HTTP_STATUS;
                if (httpStatus === 200) {
                    ls.set(LS_REFRESH_TABLE_NOVEDADES, true);
                    $.notifyClose();
                    initTablaNovedad1(novCodiTarget);
                    notify(`Novedad adicional ${type === 'create' ? 'creada' : 'actualizada'} correctamente`, 'success');
                    DIALOG_NOVEDAD1.close();
                } else {
                    $.notifyClose();
                    const message = response?.data?.MESSAGE || 'Error desconocido';
                    notify('Error: ' + message, 'danger');
                }
            })
            .catch(error => {
                $.notifyClose();
                notify('Error: ' + (error?.response?.data?.MESSAGE || 'Error desconocido'), 'danger');
            });
    };

    const deleteNovedad1 = (novCodi, novItem) => {
        const novCodiTarget = novCodi ?? currentNovedad1Codi;
        if (!novCodiTarget || !novItem) {
            notify('No se pudo identificar el registro a eliminar', 'warning');
            return;
        }

        $.notifyClose();
        notify('Eliminando novedad adicional...', 'info');
        axios.delete(`${ENDPOINT_NOVEDAD1}/${novCodiTarget}/${novItem}`)
            .then(response => {
                const httpStatus = response?.data?.HTTP_STATUS;
                if (httpStatus === 200) {
                    ls.set(LS_REFRESH_TABLE_NOVEDADES, true);
                    $.notifyClose();
                    initTablaNovedad1(novCodiTarget);
                    notify('Novedad adicional eliminada correctamente', 'success');
                    DIALOG_NOVEDAD1.close();
                } else {
                    $.notifyClose();
                    const message = response?.data?.MESSAGE || 'Error desconocido';
                    notify('Error: ' + message, 'danger');
                }
            })
            .catch(error => {
                $.notifyClose();
                notify('Error: ' + (error?.response?.data?.MESSAGE || 'Error desconocido'), 'danger');
            });
    };

    const renderNovedad1CoAdOptions = (options) => {
        const $select = $('#N1NovCoAd');
        if (!$select.length) return;

        const selectedValue = String($select.val() ?? '');
        $select.empty();
        $select.append('<option value="">Seleccionar</option>');

        const groupsMap = new Map();

        options.forEach(item => {
            const novCodi = String(item?.NovCodi ?? '').trim();
            const novDesc = String(item?.NovDesc ?? '').trim();
            const novTipo = Number(item?.NovTipo ?? 0);
            const novTipoDesc = String(item?.NovTipoDesc ?? '').trim() || `Tipo ${novTipo}`;

            if (!novCodi) return;

            if (!groupsMap.has(novTipo)) {
                groupsMap.set(novTipo, {
                    novTipo,
                    label: `${novTipoDesc}`,
                    items: [],
                });
            }

            groupsMap.get(novTipo).items.push({ novCodi, novDesc });
        });

        const orderedGroups = Array.from(groupsMap.values())
            .sort((a, b) => a.novTipo - b.novTipo);

        orderedGroups.forEach(group => {
            const $optgroup = $('<optgroup>').attr('label', group.label);

            group.items
                .sort((a, b) => Number(a.novCodi) - Number(b.novCodi))
                .forEach(item => {
                    $optgroup.append(`<option value="${item.novCodi}">${item.novCodi} - ${item.novDesc}</option>`);
                });

            $select.append($optgroup);
        });

        if (!$.fn.select2 || !$select.hasClass('select2-hidden-accessible')) {
            select2Simple('#N1NovCoAd', 'Seleccionar', false, false, '100%', $('#dialogNovedad1'));
        }

        if (selectedValue) {
            $select.val(selectedValue).trigger('change.select2');
        }
    };

    const setSelectValueSafe = ($select, value, fallback = '') => {
        if (!$select || !$select.length) return;

        const raw = value === null || value === undefined ? '' : String(value).trim();
        const normalized = raw !== '' ? raw : String(fallback);

        const hasOption = $select.find(`option[value="${normalized}"]`).length > 0;
        const finalValue = hasOption ? normalized : String(fallback);

        $select.val(finalValue);

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.trigger('change.select2');
        }
    };

    const loadNovCoAdOptionsForNovedad1 = () => {
        const targetMainCodi = currentMainNovedadCodi ?? currentNovedad1Codi;
        if (!targetMainCodi) return Promise.resolve();

        if (novedad1NovCoAdLoadedFor === targetMainCodi && novedad1NovCoAdCache.length) {
            renderNovedad1CoAdOptions(novedad1NovCoAdCache);
            return Promise.resolve();
        }

        return $.ajax({
            url: ENDPOINT_NOVEDADES,
            data: { AllColumns: 0 },
            method: 'POST',
        }).then(response => {
            const rows = Array.isArray(response?.data) ? response.data : (Array.isArray(response?.DATA) ? response.DATA : []);
            novedad1NovCoAdCache = rows.map(item => ({
                NovCodi: item?.NovCodi,
                NovDesc: item?.NovDesc,
                NovTipo: item?.NovTipo,
                NovTipoDesc: item?.NovTipoDesc,
            })).filter(item => item.NovCodi !== undefined && item.NovCodi !== null);

            novedad1NovCoAdLoadedFor = targetMainCodi;
            renderNovedad1CoAdOptions(novedad1NovCoAdCache);
        }).catch(() => {
            notify('No se pudieron cargar las novedades para Codigo', 'warning');
        });
    };

    const openDialogNovedad1 = async (rowData, type = 'edit') => {
        const novCodi = rowData?.NovCodi ?? currentNovedad1Codi;
        const isCreate = type === 'create';
        const isDelete = type === 'delete';

        if (!novCodi) {
            notify('No se pudo identificar la novedad base', 'warning');
            return;
        }

        // no permitir letras en novFact al escribir
        $('#N1NovFact').on('input', function () {
            this.value = this.value.replace(/[^0-9.]/g, '');
        });

        if (!$.fn.select2 || !$('#N1NovDia').hasClass('select2-hidden-accessible')) {
            select2Simple(`#N1NovDia`, 'Seleccionar', false, false, '100%', $('#dialogNovedad1'));
        }

        if (!$.fn.select2 || !$('#N1NovMeJo').hasClass('select2-hidden-accessible')) {
            select2Simple(`#N1NovMeJo`, 'Seleccionar', false, false, '100%', $('#dialogNovedad1'));
        }

        await loadNovCoAdOptionsForNovedad1();

        const novItem = isCreate ? getNextNovItem() : rowData.NovItem;
        const novDiaValue = rowData?.NovDia ?? '0';
        const novMeJoValue = rowData?.NovMeJo ?? '0';
        const novCoAdValue = rowData?.NovCoAd ?? '';

        $('#N1NovItem').val(novItem || '');
        setSelectValueSafe($('#N1NovDia'), novDiaValue, '0');
        setSelectValueSafe($('#N1NovMeJo'), novMeJoValue, '0');
        setSelectValueSafe($('#N1NovCoAd'), novCoAdValue, '');
        $('#N1NovFact').val(rowData?.NovFact ?? '1');
        $('#N1NovDesde').val(rowData?.NovDesde ?? '00:00');
        $('#N1NovHasta').val(rowData?.NovHasta ?? '00:00');

        $('#N1NovItem').prop('disabled', !isCreate);
        $('#N1NovDia').prop('disabled', isDelete);
        $('#N1NovMeJo').prop('disabled', isDelete);
        $('#N1NovCoAd').prop('disabled', isDelete);
        $('#N1NovFact').prop('disabled', isDelete);
        $('#N1NovDesde').prop('disabled', isDelete);
        $('#N1NovHasta').prop('disabled', isDelete);

        const title = isCreate ? 'Nueva Novedad Adicional' : (isDelete ? 'Eliminar Novedad Adicional' : 'Editar Novedad Adicional');
        $('#dialog-novedad1-title').text(title);

        const acceptButton = document.getElementById('accept-dialog-novedad1');
        const cancelButton = document.getElementById('cancel-dialog-novedad1');

        acceptButton.textContent = isDelete ? 'Eliminar' : 'Aceptar';
        acceptButton.onclick = function () {
            if (type === 'delete') {
                if (!validarFormularioNovedad1(true)) return;
                deleteNovedad1(novCodi, $('#N1NovItem').val());
                return;
            }
            postNovedad1({ ...rowData, NovCodi: novCodi }, type);
        };

        cancelButton.onclick = function () {
            DIALOG_NOVEDAD1.close();
        };

        openDialog('dialogNovedad1', acceptButton);
    };

    const initTablaNovedad1 = (NovCodi) => {
        currentNovedad1Codi = NovCodi;
        const $tblNovedad1 = $('#tblNovedad1');

        if ($.fn.DataTable.isDataTable($tblNovedad1)) {
            const table = $tblNovedad1.DataTable();
            table.ajax.url(`${ENDPOINT_NOVEDAD1}/${NovCodi}`).load(null, false);
            return table;
        }

        $tblNovedad1.off('.novedad1Actions');
        $tblNovedad1.empty();

        const diasString = {
            '0': 'Domingo',
            '1': 'Lunes',
            '2': 'Martes',
            '3': 'Miércoles',
            '4': 'Jueves',
            '5': 'Viernes',
            '6': 'Sábado',
            '7': 'Fer. Nac.',
            '8': 'Fer. Opt.',
            '9': 'Fer. Emp.',
        };

        const MenJorString = {
            '0': 'Todos',
            '1': 'Mensuales',
            '2': 'Jornales',
        };

        const buildColumnsNovedad1 = () => {
            const NovCodi = {
                data: 'NovCodi',
                visible: false,
                title: 'Cód.',
                render: function (data) {
                    return data;
                },
            };

            const NovItem = {
                data: 'NovItem',
                visible: true,
                title: '#',
                render: function (data) {
                    return data;
                },
            };
            const NovDia = {
                data: 'NovDia',
                visible: false,
                title: 'Día',
                render: function (data) {
                    return data;
                },
            };
            const NovDiaStr = {
                data: 'NovDia',
                visible: true,
                title: 'Días',
                render: function (data) {
                    return diasString[data] || data;
                },
            };
            const NovMeJo = {
                data: 'NovMeJo',
                visible: false,
                title: '',
                render: function (data) {
                    return data;
                },
            };
            const NovMeJoString = {
                data: 'NovMeJo',
                visible: true,
                title: 'Para',
                render: function (data) {
                    return MenJorString[data] || data;
                },
            };
            const NovCoAd = {
                data: 'NovCoAd',
                visible: true,
                title: 'Código',
                render: function (data) {
                    return data;
                },
            };
            const NovCoAdDesc = {
                data: 'NovCoAdDesc',
                visible: true,
                title: 'Novedad',
                render: function (data) {
                    return data;
                },
            };
            const NovFact = {
                data: 'NovFact',
                visible: true,
                title: 'Factor',
                render: function (data) {
                    return data;
                },
            };
            const NovDesde = {
                data: 'NovDesde',
                visible: true,
                title: 'Desde',
                render: function (data) {
                    return data;
                },
            };
            const NovHasta = {
                data: 'NovHasta',
                visible: true,
                title: 'Hasta',
                render: function (data) {
                    return data;
                },
            };
            const accions = {
                data: null, className: 'w-100 text-right', targets: '', title: '',
                orderable: false,
                visible: true,
                render: function (data, type, row) {
                    if (type !== 'display') return '';
                    const labelEliminar = 'Eliminar';
                    return `<div class="float-right border p-1 fit-content rounded d-flex justify-content-center align-items-center gap5">
                    <button class="hint--top btn btn-sm btn-outline-custom border-0 btn-editar-novedad1" title="Editar" aria-label="Editar"><i class="bi bi-pen"></i></button>
                    <button class="hint--left btn btn-sm btn-outline-custom border-0 btn-eliminar-novedad1" title="${labelEliminar}" aria-label="${labelEliminar}"><i class="bi bi-trash"></i></button>
                </div>`;
                },
            };

            return [NovCodi, NovDia, NovDiaStr, NovCoAd, NovCoAdDesc, NovFact, NovDesde, NovHasta, NovMeJo, NovMeJoString, accions];
        };

        const renderDomTableNovedad1 = () => {
            return `
                <'row mb-2'
                    <'col-sm-12 col-md-6 d-inline-flex align-items-center justify-content-start gap5'>
                    <'col-sm-12 col-md-6'>
                >
                <'row 
                    '<'col-12 table-responsive'<'text-nowrap'rt>>
                >
                <'row'
                    <'col-sm-12 col-md-5'i>
                    <'col-sm-12 col-md-7'p>
                >`;
        };

        const table = $tblNovedad1.DataTable({
            dom: renderDomTableNovedad1(),
            ajax: {
                url: ENDPOINT_NOVEDAD1 + "/" + NovCodi,
                type: 'GET',
                data: function (d) {
                    return d;
                },
                dataSrc: 'DATA'
            },
            createdRow: function (row, data, dataIndex) {
                $(row).find('td').addClass('align-middle');
            },

            columns: buildColumnsNovedad1(),
            paging: false,
            ordering: false,
            info: false,
            language: DT_SPANISH_2,
        });

        table.on('init.dt', function () {
            const $inputFilter = $('#tblNovedad1_filter input');
            $inputFilter.attr('placeholder', 'Buscar').removeClass('form-control-sm');
            $tblNovedad1.show();
        });

        table.on('draw.dt', function () {
            $tblNovedad1.removeClass('loader-in');

            const visibleRows = table.rows({ filter: 'applied' }).nodes().length;
            $tabNovedad1Count.textContent = visibleRows;

            if (visibleRows === 0) {
                const colspan = $tblNovedad1.find('thead th').length;
                $tblNovedad1.find('tbody').html('<tr><td colspan="' + colspan + '" class="text-center pt-3">No se encontraron registros</td></tr>');
            }
        });

        table.on('page.dt', function () {
            $tblNovedad1.addClass('loader-in');
        });

        $(document).off('click.novedad1Actions', '#btnAgregarNovedad1');
        $(document).on('click.novedad1Actions', '#btnAgregarNovedad1', function () {
            openDialogNovedad1({ NovCodi: currentNovedad1Codi }, 'create');
        });

        $tblNovedad1.on('click.novedad1Actions', 'tbody .btn-editar-novedad1', function () {
            const rowData = table.row($(this).closest('tr')).data();
            if (!rowData) return;
            openDialogNovedad1(rowData, 'edit');
        });

        $tblNovedad1.on('click.novedad1Actions', 'tbody .btn-eliminar-novedad1', function () {
            const rowData = table.row($(this).closest('tr')).data();
            if (!rowData) return;
            openDialogNovedad1(rowData, 'delete');
        });

        return table;
    };

    //---------------- INICIALIZAR TABLA NOVEDAD2 (HORAS) ---------------- //
    const getNovAdicHoSelectedCodi = () => {
        const value = String($('#NovAdicHo').val() ?? '').trim();
        if (!/^\d+$/.test(value)) return 0;
        return parseInt(value, 10);
    };

    const canUseHorasByNovAdicHo = () => {
        const novCodi = getNovAdicHoSelectedCodi();
        return novCodi > 0;
    };

    const toggleHorasAddButton = () => {
        const enabled = canUseHorasByNovAdicHo();
        const $btnAgregarHora = $('#btnAgregarHora');
        $btnAgregarHora.prop('disabled', !enabled);
        $btnAgregarHora.attr('title', enabled ? 'Agregar' : 'Seleccione una novedad en Novedad Adicional');
    };

    const loadTiposHoraOptions = async () => {
        if (Array.isArray(horasTiposCache) && horasTiposCache.length > 0) {
            return horasTiposCache;
        }

        try {
            const response = await $.ajax({
                url: ENDPOINT_HORAS,
                method: 'GET',
            });

            const rows = Array.isArray(response)
                ? response
                : (Array.isArray(response?.DATA) ? response.DATA : []);

            horasTiposCache = rows.map(item => ({
                NovHora: item?.THoCodi ?? item?.Codi ?? item?.NovHora,
                NovAdicHoDesc: item?.THoDesc ?? item?.THoDesc2 ?? item?.Desc ?? item?.NovAdicHoDesc ?? '',
            })).filter(item => item.NovHora !== undefined && item.NovHora !== null);

            return horasTiposCache;
        } catch (error) {
            notify('No se pudieron cargar los tipos de hora', 'warning');
            return [];
        }
    };

    const renderTiposHoraSelect = (selectedValue = '') => {
        const $select = $('#HNovHora');
        if (!$select.length) return;

        $select.empty();
        $select.append('<option value="">Seleccionar</option>');

        horasTiposCache
            .sort((a, b) => Number(a.NovHora) - Number(b.NovHora))
            .forEach(item => {
                $select.append(`<option value="${item.NovHora}">${item.NovHora} - ${item.NovAdicHoDesc}</option>`);
            });

        if (!$.fn.select2 || !$select.hasClass('select2-hidden-accessible')) {
            select2Simple('#HNovHora', 'Seleccionar', false, false, '100%', $('#dialogHoras'));
        }

        if (selectedValue !== '') {
            $select.val(String(selectedValue));
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.trigger('change.select2');
            }
        }
    };

    const validarFormularioHoras = (isDelete = false) => {
        if (!isDelete && !canUseHorasByNovAdicHo()) {
            notify('Seleccione una novedad en Novedad Adicional', 'warning');
            $('#NovAdicHo').focus();
            return false;
        }

        const novHora = String($('#HNovHora').val() ?? '').trim();
        if (!/^\d+$/.test(novHora) || Number(novHora) <= 0 || Number(novHora) > 32767) {
            notify('Tipo de hora invalido', 'warning');
            $('#HNovHora').focus();
            return false;
        }

        if (isDelete) return true;

        const novFact = String($('#HNovFact').val() ?? '').trim();
        if (!novFact || !/^\d{1,6}(?:\.\d{1,4})?$/.test(novFact) || Number(novFact) <= 0) {
            notify('El factor debe ser numerico mayor a 0, con hasta 4 decimales', 'warning');
            $('#HNovFact').focus();
            return false;
        }

        return true;
    };

    const postHora = (type = 'edit') => {
        const novCodiTarget = Number(currentNovedad2Codi) || 0;
        if (!novCodiTarget) {
            notify('No se pudo identificar la novedad principal', 'warning');
            return;
        }

        if (!validarFormularioHoras(false)) return;

        const payload = {
            NovCodi: novCodiTarget,
            NovHora: String($('#HNovHora').val() ?? '').trim(),
            NovFact: String($('#HNovFact').val() ?? '1').trim(),
        };

        $.notifyClose();
        notify('Guardando novedad adicional por hora...', 'info');

        axios.post(ENDPOINT_NOVEDAD2, payload)
            .then(response => {
                const httpStatus = response?.data?.HTTP_STATUS;
                if (httpStatus === 200) {
                    ls.set(LS_REFRESH_TABLE_NOVEDADES, true);
                    $.notifyClose();
                    initTablaHoras(novCodiTarget);
                    notify(`Novedad adicional por hora ${type === 'create' ? 'creada' : 'actualizada'} correctamente`, 'success');
                    DIALOG_HORAS && DIALOG_HORAS.close();
                } else {
                    $.notifyClose();
                    notify('Error: ' + (response?.data?.MESSAGE || 'Error desconocido'), 'danger');
                }
            })
            .catch(error => {
                $.notifyClose();
                notify('Error: ' + (error?.response?.data?.MESSAGE || 'Error desconocido'), 'danger');
            });
    };

    const deleteHora = (novCodi, novHora) => {
        if (!novCodi || !novHora) {
            notify('No se pudo identificar el registro a eliminar', 'warning');
            return;
        }

        $.notifyClose();
        notify('Eliminando novedad por hora...', 'info');

        axios.delete(`${ENDPOINT_NOVEDAD2}/${novCodi}/${novHora}`)
            .then(response => {
                if (response?.data?.HTTP_STATUS === 200) {
                    ls.set(LS_REFRESH_TABLE_NOVEDADES, true);
                    $.notifyClose();
                    initTablaHoras(novCodi);
                    notify('Novedad adicional por hora eliminada correctamente', 'success');
                    DIALOG_HORAS && DIALOG_HORAS.close();
                } else {
                    $.notifyClose();
                    notify('Error: ' + (response?.data?.MESSAGE || 'Error desconocido'), 'danger');
                }
            })
            .catch(error => {
                $.notifyClose();
                notify('Error: ' + (error?.response?.data?.MESSAGE || 'Error desconocido'), 'danger');
            });
    };

    const openDialogHora = async (rowData = {}, type = 'edit') => {
        const isCreate = type === 'create';
        const isDelete = type === 'delete';
        const novCodiTarget = Number(currentNovedad2Codi) || 0;

        if (!novCodiTarget) {
            notify('No se pudo identificar la novedad principal', 'warning');
            return;
        }

        await loadTiposHoraOptions();
        renderTiposHoraSelect(rowData?.NovHora ?? '');

        $('#HNovFact').val(rowData?.NovFact ?? '1');

        $('#HNovHora').prop('disabled', isDelete || !isCreate);
        $('#HNovFact').prop('disabled', isDelete);

        const title = isCreate ? 'Nueva Novedad Adicional' : (isDelete ? 'Eliminar Novedad Adicional' : 'Editar Novedad Adicional');
        $('#dialog-horas-title').text(title);

        const acceptButton = document.getElementById('accept-dialog-horas');
        const cancelButton = document.getElementById('cancel-dialog-horas');

        if (!acceptButton || !cancelButton) return;

        acceptButton.textContent = isDelete ? 'Eliminar' : 'Aceptar';
        acceptButton.onclick = function () {
            if (isDelete) {
                if (!validarFormularioHoras(true)) return;
                deleteHora(novCodiTarget, rowData?.NovHora);
                return;
            }
            postHora(type);
        };

        cancelButton.onclick = function () {
            DIALOG_HORAS && DIALOG_HORAS.close();
        };

        openDialog('dialogHoras', acceptButton);
    };

    const buildColumnsHoras = () => {
        const NovCodi = {
            data: 'NovCodi',
            visible: false,
            title: 'Cód.',
            render: function (data) {
                return data;
            },
        };

        const NovHora = {
            data: 'NovHora',
            visible: true,
            title: 'Hora',
            render: function (data) {
                return data;
            },
        };

        const NovAdicHoDesc = {
            data: 'NovAdicHoDesc',
            visible: true,
            title: 'Descripción',
            render: function (data) {
                return data;
            },
        };

        const NovFact = {
            data: 'NovFact',
            visible: true,
            title: 'Factor',
            render: function (data) {
                return data;
            },
        };

        const actions = {
            data: null, className: 'w-100 text-right', targets: '', title: '',
            orderable: false,
            visible: true,
            render: function (data, type) {
                if (type !== 'display') return '';
                return `<div class="float-right border p-1 fit-content rounded d-flex justify-content-center align-items-center gap5">
                    <button class="hint--top btn btn-sm btn-outline-custom border-0 btn-editar-hora" title="Editar" aria-label="Editar"><i class="bi bi-pen"></i></button>
                    <button class="hint--left btn btn-sm btn-outline-custom border-0 btn-eliminar-hora" title="Eliminar" aria-label="Eliminar"><i class="bi bi-trash"></i></button>
                </div>`;
            },
        };

        return [NovCodi, NovHora, NovAdicHoDesc, NovFact, actions];
    };

    const renderDomTableHoras = () => {
        return `
                <'row mb-2'
                    <'col-sm-12 col-md-6 d-inline-flex align-items-center justify-content-start gap5'>
                    <'col-sm-12 col-md-6'>
                >
                <'row 
                    '<'col-12 table-responsive'<'text-nowrap'rt>>
                >
                <'row'
                    <'col-sm-12 col-md-5'i>
                    <'col-sm-12 col-md-7'p>
                >`;
    };

    const initTablaHoras = (NovCodi) => {
        currentNovedad2Codi = NovCodi;
        toggleHorasAddButton();

        const $tblHoras = $('#tblHoras');
        const novCodiTarget = Number(NovCodi) || 0;

        if (novCodiTarget <= 0) {
            if ($.fn.DataTable.isDataTable($tblHoras)) {
                const table = $tblHoras.DataTable();
                table.clear().draw();
            }
            if ($tabHorasCount) {
                $tabHorasCount.textContent = '0';
            }
            $tblHoras.hide();
            return null;
        }

        $tblHoras.show();

        if ($.fn.DataTable.isDataTable($tblHoras)) {
            const table = $tblHoras.DataTable();
            table.ajax.url(`${ENDPOINT_NOVEDAD2}/${novCodiTarget}`).load(null, false);
            return table;
        }

        $tblHoras.off('.horasActions');
        $tblHoras.empty();

        const table = $tblHoras.DataTable({
            dom: renderDomTableHoras(),
            ajax: {
                url: `${ENDPOINT_NOVEDAD2}/${novCodiTarget}`,
                type: 'GET',
                data: function (d) {
                    return d;
                },
                dataSrc: 'DATA',
            },
            createdRow: function (row) {
                $(row).find('td').addClass('align-middle');
            },
            columns: buildColumnsHoras(),
            paging: false,
            ordering: false,
            info: false,
            language: DT_SPANISH_2,
        });

        table.on('init.dt', function () {
            const $inputFilter = $('#tblHoras_filter input');
            $inputFilter.attr('placeholder', 'Buscar').removeClass('form-control-sm');
            $tblHoras.show();
        });

        table.on('draw.dt', function () {
            $tblHoras.removeClass('loader-in');
            const visibleRows = table.rows({ filter: 'applied' }).nodes().length;
            if ($tabHorasCount) {
                $tabHorasCount.textContent = visibleRows;
            }

            if (visibleRows === 0) {
                const colspan = $tblHoras.find('thead th').length;
                $tblHoras.find('tbody').html('<tr><td colspan="' + colspan + '" class="text-center pt-3">No se encontraron registros</td></tr>');
            }
        });

        $(document).off('click.horasActions', '#btnAgregarHora');
        $(document).on('click.horasActions', '#btnAgregarHora', function () {
            if (!canUseHorasByNovAdicHo()) {
                notify('Seleccione una novedad en Novedad Adicional', 'warning');
                return;
            }
            openDialogHora({}, 'create');
        });

        $tblHoras.on('click.horasActions', 'tbody .btn-editar-hora', function () {
            const rowData = table.row($(this).closest('tr')).data();
            if (!rowData) return;
            openDialogHora(rowData, 'edit');
        });

        $tblHoras.on('click.horasActions', 'tbody .btn-eliminar-hora', function () {
            const rowData = table.row($(this).closest('tr')).data();
            if (!rowData) return;
            openDialogHora(rowData, 'delete');
        });

        return table;
    };

    //---------------- MODAL ---------------- //
    const getNovedadByID = async (NovCodi, btn = $()) => {
        notify('Aguarde...', 'info');
        $.ajax({
            url: ENDPOINT_NOVEDADES,
            data: { NovCodi: NovCodi, AllColumns: 1 },
            method: 'POST',
            success: async function (response) {
                const rs = response.data[0] || {};
                await renderTabs(rs);
                $.notifyClose();
                MODAL_NOVEDAD.modal('show');
                btn && btn.prop('disabled', false);
            },
            error: function (error) {
                console.error('Error al obtener la novedad:', error);
                btn && btn.prop('disabled', false);
            }
        });
    };

    const deleteNovedadByID = async (NovCodi, NovDesc, $btn = $()) => {

        const novCodi = String(NovCodi ?? '').trim();
        const novDesc = String(NovDesc ?? '').trim();

        if (!/^\d+$/.test(novCodi) || Number(novCodi) <= 0) {
            notify('Codigo de novedad invalido', 'warning');
            $btn && $btn.prop('disabled', false);
            return;
        }

        const confirmed = await openDialogEliminarNovedad(novCodi, novDesc);
        if (!confirmed) {
            $btn && $btn.prop('disabled', false);
            return;
        }

        $.notifyClose();
        notify('Eliminando novedad...', 'info');

        try {
            const response = await axios.delete(`${ENDPOINT_NOVEDAD_CONFIG}/${novCodi}`);
            const httpStatus = response?.data?.HTTP_STATUS;

            if (httpStatus === 200) {
                ls.set(LS_REFRESH_TABLE_NOVEDADES, true);

                const $tblNovedaes = $('#tblNovedades');
                if ($.fn.DataTable.isDataTable($tblNovedaes)) {
                    $tblNovedaes.DataTable().ajax.reload(null, false);
                } else {
                    initTablaNovedades();
                }

                $.notifyClose();
                notify('Novedad eliminada correctamente', 'success');
                return;
            }

            $.notifyClose();
            notify('Error: ' + (response?.data?.MESSAGE || 'Error desconocido'), 'danger');
        } catch (error) {
            $.notifyClose();
            notify('Error: ' + (error?.response?.data?.MESSAGE || 'Error desconocido'), 'danger');
        } finally {
            $btn && $btn.prop('disabled', false);
        }
    };

    const openDialogEliminarNovedad = (novCodi, novDesc) => {
        return new Promise((resolve) => {
            if (!DIALOG_ELIMINAR_NOVEDAD) {
                resolve(false);
                return;
            }

            const $label = $('#delete-novedad-codi');
            const acceptButton = document.getElementById('accept-dialog-eliminar-novedad');
            const cancelButton = document.getElementById('cancel-dialog-eliminar-novedad');

            if (!acceptButton || !cancelButton) {
                resolve(false);
                return;
            }

            $label.text(novCodi + ' - ' + novDesc);

            let resolved = false;

            const cleanup = () => {
                acceptButton.onclick = null;
                cancelButton.onclick = null;
                DIALOG_ELIMINAR_NOVEDAD.onclose = null;
            };

            const resolveOnce = (value) => {
                if (resolved) return;
                resolved = true;
                cleanup();
                resolve(value);
            };

            acceptButton.onclick = function () {
                DIALOG_ELIMINAR_NOVEDAD.close();
                resolveOnce(true);
            };

            cancelButton.onclick = function () {
                DIALOG_ELIMINAR_NOVEDAD.close();
                resolveOnce(false);
            };

            DIALOG_ELIMINAR_NOVEDAD.onclose = function () {
                resolveOnce(false);
            };

            openDialog('dialogEliminarNovedad', acceptButton);
        });
    };

    const getNovedadesByIDs = async (NovCodis) => {
        notify('Aguarde...', 'info');
        return $.ajax({
            url: ENDPOINT_NOVEDADES,
            data: { NovCodi: NovCodis },
            method: 'POST',
            success: async function (response) {
                const rs = response.data[0] || {};
            },
            error: function (error) {
                console.error('Error al obtener las novedades:', error);
            }
        });
    };
    const getNovedadByNovTipo = () => {
        return $.ajax({
            url: ENDPOINT_NOVEDADES,
            data: { NovAu: 1 },
            method: 'POST',
            success: function (response) {
                const rs = response.data || {};
                renderOptNovCarCodi(rs);
                renderOptNovAdicHo(rs);
            },
            error: function (error) {
                console.error('Error al obtener la novedad:', error);
            }
        });
    };
    const renderOptNovCarCodi = (data) => {
        const select = document.getElementById('NovCarCodi');
        if (!select) return;
        select.innerHTML = '';
        const option = document.createElement('option');
        option.value = '0';
        option.textContent = 'Ninguno';
        select.appendChild(option);
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.NovCodi;
            option.textContent = item.NovCodi + ' - ' + item.NovDesc;
            select.appendChild(option);
        });
    };
    const renderOptNovAdicHo = (data) => {
        const select = document.getElementById('NovAdicHo');
        if (!select) return;
        select.innerHTML = '';
        const option = document.createElement('option');
        option.value = '0';
        option.textContent = 'Ninguno';
        select.appendChild(option);
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.NovCodi;
            option.textContent = item.NovCodi + ' - ' + item.NovDesc;
            select.appendChild(option);
        });
    };

    const parseRadioDataFieldValue = (field, scopeSelector = '#modal-novedad') => {
        const checked = document.querySelector(`${scopeSelector} input[data-field="${field}"]:checked`);
        if (!checked) return '';

        const explicitValue = String(checked.value ?? '').trim();
        if (explicitValue !== '' && explicitValue.toLowerCase() !== 'on') {
            return explicitValue;
        }

        const checkedId = String(checked.id ?? '').trim();
        if (checkedId.startsWith(field)) {
            return checkedId.slice(field.length);
        }

        return '';
    };

    const collectNovedadPayloadFromDataFields = () => {
        const scopeSelector = '#modal-novedad';
        const elements = document.querySelectorAll(`${scopeSelector} [data-field]`);
        const payload = {};
        const processedRadioFields = new Set();

        elements.forEach(el => {
            const field = String(el.dataset.field ?? '').trim();
            if (!field) return;

            if (el.type === 'radio') {
                if (processedRadioFields.has(field)) return;
                payload[field] = parseRadioDataFieldValue(field, scopeSelector);
                processedRadioFields.add(field);
                return;
            }

            if (el.type === 'checkbox') {
                payload[field] = el.checked ? '1' : '0';
                return;
            }

            payload[field] = String(el.value ?? '').trim();
        });

        if (Object.prototype.hasOwnProperty.call(payload, 'Color')) {
            payload.NovColor = normalizeColorHex(payload.Color);
            delete payload.Color;
        }

        return payload;
    };

    const validateNovedadPayload = (payload) => {
        const novCodi = String(payload?.NovCodi ?? '').trim();
        const novDesc = String(payload?.NovDesc ?? '').trim();
        const novTipo = String(payload?.NovTipo ?? '').trim();

        if (!/^\d+$/.test(novCodi) || Number(novCodi) <= 0 || Number(novCodi) > 32767) {
            notify('Codigo de novedad invalido (1 a 32767)', 'warning');
            $('#NovCodi').focus();
            return false;
        }

        if (!novDesc) {
            notify('La descripcion es requerida', 'warning');
            $('#NovDesc').focus();
            return false;
        }

        if (novDesc.length > 40) {
            notify('La descripcion no puede superar 40 caracteres', 'warning');
            $('#NovDesc').focus();
            return false;
        }

        if (novTipo === '' || !/^\d+$/.test(novTipo)) {
            notify('El tipo de novedad es requerido', 'warning');
            $('#NovTipo').focus();
            return false;
        }

        return true;
    };

    const saveNovedadConfig = () => {
        const $btnSave = $('#btn-save');
        const payload = collectNovedadPayloadFromDataFields();

        if (!validateNovedadPayload(payload)) return;

        $.notifyClose();
        notify('Guardando novedad...', 'info');
        $btnSave.prop('disabled', true);

        axios.post(ENDPOINT_NOVEDAD_CONFIG, payload)
            .then((response) => {
                const httpStatus = response?.data?.HTTP_STATUS;
                if (httpStatus === 200) {
                    ls.set(LS_REFRESH_TABLE_NOVEDADES, true);
                    $.notifyClose();
                    notify('Novedad guardada correctamente', 'success');
                    // MODAL_NOVEDAD.modal('hide');
                    return;
                }
                $.notifyClose();
                notify('Error: ' + (response?.data?.MESSAGE || 'Error desconocido'), 'danger');
            })
            .catch((error) => {
                $.notifyClose();
                notify('Error: ' + (error?.response?.data?.MESSAGE || 'Error desconocido'), 'danger');
            })
            .finally(() => {
                $btnSave.prop('disabled', false);
            });
    };

    $(document).off('click.novedadSave', '#btn-save');
    $(document).on('click.novedadSave', '#btn-save', function () {
        saveNovedadConfig();
    });

    const setFieldValue = (el, data) => {
        const key = el.dataset.field;
        if (!key || !(key in data)) return;

        if (el.type === 'checkbox') {
            el.checked = Number(data[key]) === 1;
            return;
        }

        if (el.type === 'radio') {
            const valor = String(data[key] ?? '').trim();
            const radio = document.querySelector(`input[id="${key}${valor}"]`);
            if (radio) radio.checked = true;
            return;
        }

        el.value = data[key];

        if (el.tagName === 'SELECT') {
            select2Simple(`#${el.id}`, 'Seleccionar', false, false, '100%');
        }
    };

    const customPlaceHolderSelect2 = (target, placeholder) => {

        // Resuelve el target a un elemento del DOM
        const root = typeof target === 'string'
            ? document.querySelector(target)
            : target;

        if (!root) return;

        const container = root.querySelector('span.select2-selection__placeholder');
        if (container) {
            container.textContent = placeholder;
        }
    };

    const disabledSelect2 = (target, disabled) => {
        const root = typeof target === 'string'
            ? document.querySelector(target)
            : target;

        if (!root) return;

        root.disabled = disabled;
    };

    const disabledCheckbox = (target, disabled) => {
        const root = typeof target === 'string'
            ? document.querySelector(target)
            : target;

        if (!root) return;

        root.disabled = disabled;
    };

    const CONTAINERS = ['#caracteristicas-container', '#tabCarac', '#tabControles', '#tabNovedadesAdicionalesHoras'];

    const renderTabs = async (data) => {
        if (!data) return;
        await renderFields(CONTAINERS, data);
    };

    const esTipoAusencia = (novTipo) => novTipo >= 3;

    const renderFields = async (containers, data) => {

        if (currentMainNovedadCodi !== data.NovCodi) {
            currentMainNovedadCodi = data.NovCodi;
            novedad1NovCoAdCache = [];
            novedad1NovCoAdLoadedFor = null;
        }

        await getNovedadByNovTipo();
        initTablaNovecausa(data.NovCodi);
        initTablaNovedad1(data.NovCodi);

        const $NovAdicHo = document.querySelector('#NovAdicHo');
        if ($NovAdicHo) {
            const optionToRemove = $NovAdicHo.querySelector(`option[value="${data.NovCodi}"]`);
            if (optionToRemove) {
                optionToRemove.remove();
            }
        }

        const selector = containers.map(c => `${c} [data-field]`).join(',');
        document.querySelectorAll(selector).forEach(el => setFieldValue(el, data));

        initTablaHoras(data.NovCodi);
        toggleHorasAddButton();

        initHorColorPicker();

        // Deshabilitar el select2 de NovTipo
        const $NovTipo = document.querySelector('#NovTipo');
        disabledSelect2($NovTipo, true);

        // Deshabilitar el checkbox de NovSusT si NovTipo < 3
        const $NovSusT = document.querySelector('#NovSusT');
        const $NovVaca = document.querySelector('#NovVaca');
        const isAusencia = esTipoAusencia(data.NovTipo);

        disabledCheckbox($NovSusT, !isAusencia);
        disabledCheckbox($NovVaca, !isAusencia);

        // ---- Mostrar u ocultar la pestaña de Novedades Adicionales Horas según si es ausencia o no ----- //
        const $tabLink = document.querySelector('[data-target="#tabNovedadesAdicionalesHoras"]');
        const $tabItem = $tabLink?.closest('li.nav-item');

        if ($tabItem) {
            const eraActiva = $tabItem.classList.contains('active') || $tabLink.classList.contains('active');

            $tabItem.classList.toggle('d-none', !isAusencia);

            if (!isAusencia && eraActiva) {
                const $primeraVisible = document.querySelector('.nav-tabs .nav-item:not(.d-none) .nav-link');
                if ($primeraVisible) {
                    $($primeraVisible).tab('show');
                }
            }
        }
        // --------------------- ///

        notAusenciaPermitted.forEach(el => {
            el.title = isAusencia ? 'Permitido solo para ausencias' : 'No permitido si no es ausencia';
        });
    };

    $(document).off('change.horasNovedad2', '#NovAdicHo');
    $(document).on('change.horasNovedad2', '#NovAdicHo', function () {
        toggleHorasAddButton();
    });

    const openDialog = (dialogId, confirmButton) => {
        const dialog = document.getElementById(dialogId);
        if (!dialog) return;

        // Validación: solo registramos Enter si confirmButton es un elemento clickeable
        const esElementoClickeable = (el) =>
            el instanceof HTMLElement && typeof el.click === 'function';

        if (esElementoClickeable(confirmButton)) {
            const handleKeydown = (event) => {
                if (event.key !== 'Enter') return;

                // No interferir con saltos de línea en textareas
                if (event.target.tagName === 'TEXTAREA') return;

                event.preventDefault();
                event.stopPropagation();
                confirmButton.click();
            };

            dialog.addEventListener('keydown', handleKeydown);

            dialog.addEventListener('close', () => {
                dialog.removeEventListener('keydown', handleKeydown);
            }, { once: true });
        }

        dialog.showModal();
    };

    const closeDialog = (dialogId) => {
        const dialog = document.getElementById(dialogId);
        if (!dialog) return;
        dialog.close();
    };

})(jQuery);