(function ($) {
    'use strict';

    window.camposLiquidar = window.camposLiquidar || [];
    const homehost = $("#_homehost").val();

    const TIPOS = {
        LEGAJO: 'legajo',
        APNO: 'apno',
        DNI_LEGAJO: 'dni_legajo',
        CUIL_LEGAJO: 'cuil_legajo',
        COD_EMPRESA: 'cod_empresa',
        CUIT_EMPRESA: 'cuit_empresa',
        COD_PLANTA: 'cod_planta',
        COD_CONVENIO: 'cod_convenio',
        COD_SECTOR: 'cod_sector',
        COD_SECCION: 'cod_seccion',
        COD_GRUPO: 'cod_grupo',
        COD_SUCURSAL: 'cod_sucursal',
        FECHA: 'fecha',
        NOVEDADES: 'novedades',
        HORAS: 'horas',
        HORAS_AGRUPADAS: 'horas_agrupadas',
        ATRA: 'atra',
        TRAB: 'trab',
        PRIMER_FICHADA: 'primer_fichada',
        ULTIMA_FICHADA: 'ultima_fichada',
        TODAS_FICHADAS: 'todas_fichadas',
        TURSTR: 'turstr',
        LABO: 'labo',
        FERI: 'feri'
    };

    const FORMATOS = {
        NUMERO: 'numero',
        DECIMAL: 'decimal',
        HORAS: 'horas',
        TEXTO: 'texto',
        FECHA_YMD: 'YYYY-MM-DD',
        FECHA_YMD_COMPACTA: 'YYYYMMDD',
        FECHA_YMD_SLASH: 'YYYY/MM/DD',
        FECHA_MDY: 'MM-DD-YYYY',
        FECHA_MDY_COMPACTA: 'MMDDYYYY',
        FECHA_MDY_SLASH: 'MM/DD/YYYY',
        FECHA_DMY: 'DD-MM-YYYY',
        FECHA_DMY_COMPACTA: 'DDMMYYYY',
        FECHA_DMY_SLASH: 'DD/MM/YYYY'
    };

    const LANGUAGE_SELECT2 = {
        noResults: function () {
            return 'No hay resultados..'
        },
        inputTooLong: function (args) {
            var message = 'Máximo ' + maximumInputLength + ' caracteres. Elimine ' + overChars + ' caracter';
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
            return 'Ingresar ' + minimumInputLength + ' o mas caracteres'
        },
        maximumSelected: function () {
            return 'Puede seleccionar solo una opción'
        },
        removeAllItems: function () {
            return "Eliminar Selección"
        }
    }

    const cacheCatalogos = {
        novedades: null,
        horas: null,
        etiquetasParagene: null
    };

    const MAP_TIPO_ETIQUETA = {
        cod_empresa: 'EmprSin',
        cuit_empresa: 'EmprSin',
        cod_planta: 'PlanSin',
        cod_sector: 'SectSin',
        cod_sucursal: 'SucuSin',
        cod_grupo: 'GrupSin',
        cod_seccion: 'SeccSin'
    };

    const MAP_TIPO_ETIQUETA_FALLBACK = {
        cod_empresa: 'Empresa',
        cuit_empresa: 'Empresa',
        cod_planta: 'Planta',
        cod_sector: 'Sector',
        cod_sucursal: 'Sucursal',
        cod_grupo: 'Grupo',
        cod_seccion: 'Seccion'
    };

    const labelsTipo = {
        [TIPOS.LEGAJO]: 'Legajo',
        [TIPOS.APNO]: 'Apellido y Nombre',
        [TIPOS.DNI_LEGAJO]: 'DNI Legajo',
        [TIPOS.CUIL_LEGAJO]: 'CUIL Legajo',
        [TIPOS.COD_EMPRESA]: 'Empresa',
        [TIPOS.CUIT_EMPRESA]: 'Cuit Empresa',
        [TIPOS.COD_PLANTA]: 'Planta',
        [TIPOS.COD_CONVENIO]: 'Convenio',
        [TIPOS.COD_SECTOR]: 'Sector',
        [TIPOS.COD_SECCION]: 'Seccion',
        [TIPOS.COD_GRUPO]: 'Grupo',
        [TIPOS.COD_SUCURSAL]: 'Sucursal',
        [TIPOS.FECHA]: 'Fecha',
        [TIPOS.NOVEDADES]: 'Novedades',
        [TIPOS.HORAS]: 'Horas',
        [TIPOS.HORAS_AGRUPADAS]: 'Horas Agrupadas',
        [TIPOS.ATRA]: 'Horas a trabajar',
        [TIPOS.TRAB]: 'Horas trabajadas',
        [TIPOS.PRIMER_FICHADA]: 'Primer Fichada',
        [TIPOS.ULTIMA_FICHADA]: 'Ultima Fichada',
        [TIPOS.TODAS_FICHADAS]: 'Todas las fichadas',
        [TIPOS.TURSTR]: 'Horario',
        [TIPOS.LABO]: 'Laboral',
        [TIPOS.FERI]: 'Feriado'
    };

    const labelsFormato = {
        [FORMATOS.NUMERO]: 'Numero',
        [FORMATOS.DECIMAL]: 'Decimal',
        [FORMATOS.HORAS]: 'Horas (HH:MM)',
        [FORMATOS.TEXTO]: 'Texto',
        [FORMATOS.FECHA_YMD]: 'YYYY-MM-DD',
        [FORMATOS.FECHA_YMD_COMPACTA]: 'YYYYMMDD',
        [FORMATOS.FECHA_YMD_SLASH]: 'YYYY/MM/DD',
        [FORMATOS.FECHA_MDY]: 'MM-DD-YYYY',
        [FORMATOS.FECHA_MDY_COMPACTA]: 'MMDDYYYY',
        [FORMATOS.FECHA_MDY_SLASH]: 'MM/DD/YYYY',
        [FORMATOS.FECHA_DMY]: 'DD-MM-YYYY',
        [FORMATOS.FECHA_DMY_COMPACTA]: 'DDMMYYYY',
        [FORMATOS.FECHA_DMY_SLASH]: 'DD/MM/YYYY'
    };

    const DEFAULT_SEPARADOR = ',';
    const DEFAULT_PLANTILLA = '';
    const STORAGE_PLANTILLA_KEY = 'liquidar_custom_plantilla';

    let idSecuencial = 1;
    let uidEnEdicion = null;
    let timerGuardarSeparador = null;
    let timerGuardarFiltros = null;
    const ENDPOINT_CAMPOS = '../../app-data/liquidar/custom/campos';
    const ENDPOINT_EXPORT = '../../app-data/liquidar/custom/export';
    const ENDPOINT_PLANTILLAS = '../../app-data/liquidar/custom/plantillas';
    const MAPA_FILTROS_SELECT = {
        Lega: '#selectjs_personal',
        Empr: '#selectjs_empresa',
        Plan: '#selectjs_planta',
        Conv: '#selectjs_convenio',
        Sect: '#selectjs_sector',
        Sec2: '#selectjs_seccion',
        Grup: '#selectjs_grupos',
        Sucu: '#selectjs_sucursal'
    };
    const SELECTORES_FILTROS = Object.values(MAPA_FILTROS_SELECT).join(',');

    const $plantilla = $('#campo-plantilla');
    const $posicion = $('#campo-posicion');
    const $tipo = $('#campo-tipo');
    const $subtipo = $('#campo-subtipo');
    const $subtipoWrapper = $('#subtipo-wrapper');
    const $subtipoHelp = $('#subtipo-help');
    const $agrupacionWrapper = $('#agrupacion-wrapper');
    const $agrupacionNombre = $('#campo-agrupacion');
    const $tamano = $('#campo-tamano');
    const $formato = $('#campo-formato');
    const $feedback = $('#campo-feedback');
    const $separador = $('#campo-separador');
    const $resultadoSeparador = $('.resultado_separador');
    const $encabezados = $('#encabezados');
    const $switchEncabezadosText = $('.switch_encabezados_text');
    const $tablaBody = $('#tabla-campos-body');
    const $btnAgregar = $('#btn-agregar-campo');
    const $btnExportar = $('#btn-exportar');
    const $datePicker = $('#date-picker');
    const $resultadoExportacion = $('#resultado-exportacion');
    const $resultadoExportacionArchivo = $('#resultado-exportacion-archivo');
    const $resultadoExportacionContenido = $('#resultado-exportacion-contenido');
    const $definirCampos = $('#definirCampos');
    const $btnCrearPlantilla = $('#btn-crear-plantilla, .btn-crear-plantilla');
    const $btnFiltros = $('#btn-filtros');
    const $btnEliminarPlantilla = $('#btn-eliminar-plantilla, .btn-eliminar-plantilla');
    const $modalCrearPlantilla = $('#modal-crear-plantilla');
    const $modalFiltros = $('#modal-filtros');
    const $inputNombrePlantilla = $('#nombre-plantilla');
    const $errorNombrePlantilla = $('#nombre-plantilla-error');
    const $btnGuardarPlantilla = $('#btn-guardar-plantilla');
    const $footerFilters = $('#footer-filters');
    const $tiposLega = $('#tipos-lega');
    const $trashAllIn = $('#trash_allIn');
    const $asignados = $('.asignados');
    const $totalRegistros = $('.total-registros');
    const $camposAsignados = $('[aria-label="Asignados"]');


    let filtrosInicializados = false;
    let filtrosSeleccionadosPlantilla = crearFiltrosVacios();
    let filtrosDescripcionPlantilla = crearFiltrosDescripcionVacia();
    let aplicandoFiltrosPersistidos = false;

    const TEXTO_BOTON_AGREGAR = 'Agregar campo';
    const TEXTO_BOTON_EDITAR = 'Editar campo';

    $(document).ready(function () {
        inicializarSelects();
        inicializarSelectsFiltros();
        actualizarTextoBotonCampo();
        enlazarEventos();
        inicializarOrdenamientoTabla();
        cargarEtiquetasParagene();
        const plantillaGuardada = obtenerPlantillaGuardada() || DEFAULT_PLANTILLA;
        cargarPlantillas(plantillaGuardada)
            .then(function () {
                return cargarCamposGuardados();
            })
            .catch(function () {
                return cargarCamposGuardados();
            });
        inicializarDatePicker();
    });

    const optionSelect2 = (item) => {
        if (!item.id) return item.text;

        const checked = item.selected ? 'checked' : '';
        const disabled = item.disabled ? 'disabled' : '';
        const estructura = item.estructura || '';
        const cantidad = item.Cantidad ?? 0;

        // Caso especial: personal con id == 0
        if (estructura === 'personal' && item.id == '0') {
            return ''; // Retorna cadena vacía para evitar mostrarlo
        }

        // Texto del sector si aplica
        const getSectorText = () => {
            if (estructura === 'secciones' && item.Sector) {
                return `<span class="font07">Sector: (${item.CodSect}) ${item.Sector}</span>`;
            }
            return '';
        };

        const sectorText = getSectorText();

        // Función auxiliar para crear el checkbox
        const createCheckbox = () => {
            return $(`
            <div class="d-flex justify-content-between align-items-center w-100 ${disabled}">
                ${createLabelContent()}
                <div class="font08 badge badge-light">${estructura !== 'personal' ? cantidad : ''}</div>
            </div>
            ${sectorText}
        `);
        };

        // Contenido del label según tipo de estructura
        const createLabelContent = () => {

            if (estructura === 'personal') {
                return `
                <div>
                    <span class="font08">${item.id}</span><br>
                    <span class="font09">${item.text}</span>
                </div>
            `;
            }

            return `
            <div class="d-inline-flex align-items-center" style="gap:5px;">
                <div class="font08" style="white-space: nowrap;margin-bottom:2px">(${item.id2})</div>
                <div class="font09">${item.text}</div>
            </div>
        `;
        };

        return createCheckbox();
    };

    let preventOpenOnClear = false;
    let closeTimeout = null;

    const select2Estruct = (selector, multiple, placeholder, estruct) => {

        const url = "/" + homehost + "/app-data/personal/filtros";

        $(selector).select2({
            multiple: multiple,
            allowClear: true,
            language: "es",
            placeholder: $(selector).data("label") || placeholder,
            minimumInputLength: 0,
            minimumResultsForSearch: 50,
            maximumInputLength: 10,
            width: "100%",
            selectOnClose: false,
            templateResult: function (item) {
                return optionSelect2(item);
            },
            ajax: {
                url: url,
                dataType: "json",
                type: "POST",
                delay: 250,
                cache: true,
                data: function (params) {
                    const tipo = $('input[name="Tipo"]:checked').val() || 0;
                    return {
                        descripcion: params.term,
                        estructura: estruct,
                        nullCant: 0,
                        activo: 1,
                        strict: 1,
                        estado: 0,
                        proyectar: 1,
                        tipo: tipo,
                        empresas: $("#selectjs_empresa").val(),
                        plantas: $("#selectjs_planta").val(),
                        convenios: $("#selectjs_convenio").val(),
                        sectores: $("#selectjs_sector").val(),
                        secciones: $("#selectjs_seccion").val(),
                        grupos: $("#selectjs_grupos").val(),
                        sucursales: $("#selectjs_sucursal").val(),
                        personal: $("#selectjs_personal").val(),
                    }
                },
                processResults: function (data) {
                    const datos = data.data || [];
                    const estructura = data?.estructura || '';
                    const selectedValues = $(selector).val() || [];

                    if (datos.length === 0) {
                        return {
                            results: [{ id: '', text: 'No hay resultados..' }]
                        }
                    }
                    return {
                        results: datos.map(function (item) {
                            const ID = (estructura === 'secciones') ? item.CodSect + item.Cod : item.Cod;
                            return {
                                id: ID,
                                // id: ID == '00' ? '0' : ID,
                                id2: item.Cod,
                                text: item.Descripcion || 'Sin Definir' || 'Sin descripción',
                                Sector: item.Sector || '',
                                CodSect: item.CodSect || '',
                                Cantidad: item.Cantidad,
                                disabled: item.Cantidad === 0,
                                estructura: estructura,
                                selected: selectedValues.includes(String(ID))
                            };
                        })
                    }
                },
            },
        }).on('select2:select', function (e) {
            const id = e.params.data.id;
        }).on('select2:clear', function (e) {
            preventOpenOnClear = true;
        }).on('select2:close', function () {
            if (closeTimeout) clearTimeout(closeTimeout);
            closeTimeout = setTimeout(function () {
                // getHoras_();
                // getPersonal();
            }, 50);
        }).on('select2:opening', function (e) {
            if (preventOpenOnClear) {
                e.preventDefault();
                preventOpenOnClear = false;
            }
        }).on('select2:open', function (e) {
            // limpiar el select2 para que ajax vuelva a renderizar el contenido
            $(this).data('select2').$container.find('.select2-search__field').val('');
        });

    }

    function obtenerPlantillaActiva() {
        return String($plantilla.val() || '').trim();
    }

    function guardarPlantillaEnStorage(slug) {
        try {
            if (slug) {
                localStorage.setItem(STORAGE_PLANTILLA_KEY, slug);
            } else {
                localStorage.removeItem(STORAGE_PLANTILLA_KEY);
            }
        } catch (e) {
            // Ignorar fallos de localStorage por privacidad o cuota.
        }
    }

    function obtenerPlantillaGuardada() {
        try {
            return String(localStorage.getItem(STORAGE_PLANTILLA_KEY) || '').trim();
        } catch (e) {
            return '';
        }
    }

    function cargarPlantillas(slugSeleccionado, forzarSinSeleccion) {
        return axios.get(ENDPOINT_PLANTILLAS)
            .then(function (response) {
                const listado = $.isArray(response.data) ? response.data : [];
                const opciones = listado.length ? listado : [{ slug: DEFAULT_PLANTILLA, nombre: 'LIQUIDAR_CUSTOM' }];
                const slugDestino = String(slugSeleccionado || obtenerPlantillaActiva() || obtenerPlantillaGuardada() || DEFAULT_PLANTILLA);

                $plantilla.empty();
                $plantilla.append($('<option></option>').val('').text(''));

                $.each(opciones, function (_, item) {
                    const slug = String(item.slug || '');
                    if (!slug) {
                        return;
                    }

                    const nombre = String(item.nombre || slug);
                    $plantilla.append($('<option></option>').val(slug).text(nombre));
                });

                const existeSlugDestino = $plantilla.find('option').filter(function () {
                    return String($(this).val()) === slugDestino;
                }).length > 0;

                if (forzarSinSeleccion === true) {
                    $plantilla.val(null).trigger('change.select2');
                    return;
                }

                if (existeSlugDestino) {
                    $plantilla.val(slugDestino).trigger('change.select2');
                } else {
                    $plantilla.val(null).trigger('change.select2');
                }
            })
            .catch(function () {
                $plantilla.empty();
                $plantilla.append($('<option></option>').val('').text(''));
                $plantilla.append($('<option></option>').val(DEFAULT_PLANTILLA).text('LIQUIDAR_CUSTOM'));
                if (forzarSinSeleccion === true) {
                    $plantilla.val(null).trigger('change.select2');
                } else {
                    $plantilla.val(DEFAULT_PLANTILLA).trigger('change.select2');
                }
                throw new Error('No se pudo cargar la lista de plantillas.');
            });
    }

    function cargarEtiquetasParagene() {
        if (cacheCatalogos.etiquetasParagene) {
            return;
        }

        axios.get('../../app-data/paragene')
            .then(function (response) {
                const etiquetas = response && response.data && response.data.Etiquetas
                    ? response.data.Etiquetas
                    : {};

                cacheCatalogos.etiquetasParagene = etiquetas;
                renderizarLista();
            })
            .catch(function () {
                cacheCatalogos.etiquetasParagene = {};
            });
    }

    function inicializarOrdenamientoTabla() {
        if (typeof Sortable === 'undefined' || !$tablaBody.length) {
            return;
        }

        Sortable.create($tablaBody.get(0), {
            handle: '.btn-ordenar-campo',
            draggable: 'tr[data-uid]',
            animation: 150,
            onEnd: function (evt) {
                if (!evt || evt.oldIndex === evt.newIndex) {
                    return;
                }

                reordenarCamposDesdeTabla();
                renderizarLista();
                guardarCampos().catch(function () {
                    notificar('No se pudo guardar la configuración.', 'danger');
                });
                notificar('Orden actualizado.', 'success');
            }
        });
    }

    function reordenarCamposDesdeTabla() {
        const filas = $tablaBody.find('tr[data-uid]');
        const mapaPorUid = {};

        $.each(window.camposLiquidar, function (_, campo) {
            mapaPorUid[String(campo.uid)] = campo;
        });

        const reordenados = [];
        filas.each(function (index, fila) {
            const uid = String($(fila).data('uid'));
            if (!mapaPorUid[uid]) {
                return;
            }

            mapaPorUid[uid].posicion = index + 1;
            reordenados.push(mapaPorUid[uid]);
        });

        window.camposLiquidar = reordenados;
    }

    function cargarCamposGuardados() {
        const plantilla = obtenerPlantillaActiva();
        if (!plantilla) {
            limpiarConfiguracionVista();
            return Promise.resolve();
        }

        return axios.get(ENDPOINT_CAMPOS, {
            params: {
                plantilla: plantilla
            }
        })
            .then(function (response) {
                const config = normalizarConfiguracionPersistida(response.data);
                window.camposLiquidar = config.campos;
                filtrosSeleccionadosPlantilla = normalizarFiltrosConfiguracion(config.filtros);
                filtrosDescripcionPlantilla = normalizarFiltrosDescripcionConfiguracion(config.filtrosDescripcion);
                $separador.val(config.separador);
                $encabezados.prop('checked', config.encabezados === 1);
                actualizarTextoSwitchEncabezados();
                aplicarReglasSeparador();
                actualizarResultadoSeparador();
                recalcularIdSecuencial();
                renderizarLista();
                aplicarFiltrosConfiguradosEnVista();
                actualizarPosicionSugerida(true);
            })
            .catch(function () {
                window.camposLiquidar = [];
                filtrosSeleccionadosPlantilla = crearFiltrosVacios();
                filtrosDescripcionPlantilla = crearFiltrosDescripcionVacia();
                $separador.val(DEFAULT_SEPARADOR);
                $encabezados.prop('checked', false);
                actualizarTextoSwitchEncabezados();
                actualizarResultadoSeparador();
                renderizarLista();
                aplicarFiltrosConfiguradosEnVista();
                actualizarPosicionSugerida(true);
                notificar('No se pudo cargar la configuración guardada.', 'danger');
            });
    }

    function guardarCampos() {
        const plantilla = obtenerPlantillaActiva();
        if (!plantilla) {
            return Promise.resolve();
        }

        return axios.post(ENDPOINT_CAMPOS, {
            campos: window.camposLiquidar,
            separador: obtenerSeparadorNormalizado(),
            encabezados: $encabezados.is(':checked') ? 1 : 0,
            filtros: obtenerFiltrosActuales(),
            filtrosDescripcion: obtenerFiltrosDescripcionActuales(),
            plantilla: plantilla
        });
    }

    function obtenerRangoFechas() {
        const instancia = $datePicker.data('daterangepicker');
        if (!instancia || !instancia.startDate || !instancia.endDate) {
            return null;
        }

        return {
            inicio: instancia.startDate.format('YYYY-MM-DD'),
            fin: instancia.endDate.format('YYYY-MM-DD')
        };
    }

    function validarRangoExportacion(rango) {
        if (!rango || !rango.inicio || !rango.fin) {
            return 'No se pudo obtener el rango de fechas.';
        }

        const inicio = moment(rango.inicio, 'YYYY-MM-DD', true);
        const fin = moment(rango.fin, 'YYYY-MM-DD', true);

        if (!inicio.isValid() || !fin.isValid()) {
            return 'Formato de fecha invalido.';
        }

        if (fin.isBefore(inicio, 'day')) {
            return 'La fecha fin no puede ser menor que la fecha inicio.';
        }

        const diasRango = fin.diff(inicio, 'days') + 1;
        if (diasRango > 31) {
            return 'El rango de fechas no puede superar 31 días.';
        }

        return '';
    }

    function mostrarNotificacionDescarga(archivo, mensaje) {
        if (typeof $.notify !== 'function') {
            return;
        }

        const href = '../../app-data/' + archivo;
        const contenido = '' +
            '<div class="d-flex flex-column">' +
            '<div class="font-weight-bold">' + escapeHtml(mensaje || 'Archivo generado correctamente.') + '</div>' +
            '<div class="d-flex align-items-center mt-2" style="gap:8px">' +
            '<a href="' + escapeHtml(href) + '" class="btn btn-custom px-2 btn-sm font08 w150 download-liquidar-custom" target="_blank" download>' +
            '<div class="d-flex align-items-center w-100 justify-content-center">' +
            '<span>Descargar</span>' +
            '</div>' +
            '</a>' +
            '<button type="button" class="d-none btn btn-dark border btn-sm font08 w150 mostrar-liquidar-custom" data-archivo="' + escapeHtml(archivo) + '">Mostrar Resultados</button>' +
            '</div>' +
            '</div>';

        $.notifyClose();
        notify(contenido, 'warning', 0, 'right');
    }

    function mostrarResultadosArchivo(archivo) {
        const href = '../../app-data/' + archivo;
        axios.get(href, { responseType: 'text' })
            .then(function (response) {
                const contenido = typeof response.data === 'string' ? response.data : '';
                $resultadoExportacionArchivo.text(archivo);
                $resultadoExportacionContenido.text(contenido || 'Archivo vacío.');
                $resultadoExportacion.removeClass('d-none');
                $('html, body').animate({
                    scrollTop: $resultadoExportacion.offset().top - 20
                }, 300);
                $definirCampos.collapse('hide');
            })
            .catch(function () {
                notificar('No se pudo mostrar el archivo exportado.', 'danger');
            });
    }

    function exportarDatos() {
        const plantilla = obtenerPlantillaActiva();
        if (!plantilla) {
            notificar('Debe seleccionar una plantilla para exportar.', 'danger');
            return;
        }

        const rango = obtenerRangoFechas();
        const errorRango = validarRangoExportacion(rango);
        $definirCampos.collapse('hide');

        if (errorRango) {
            notificar(errorRango, 'danger');
            return;
        }

        $resultadoExportacionArchivo.text('');
        $resultadoExportacionContenido.text('Procesando datos... Aguarde.');
        notificar('Procesando datos... Aguarde', 'info', 0, 'right');

        $btnExportar.prop('disabled', true);

        const payload = {
            FechIni: rango.inicio,
            FechFin: rango.fin,
            plantilla: plantilla
        };

        axios.post(ENDPOINT_EXPORT, payload)
            .then(function (response) {
                const data = response.data || {};
                if (data.status !== 'ok' || !data.archivo) {
                    notificar(data.message || 'No se pudo exportar el archivo.', 'danger');
                    return;
                }

                mostrarNotificacionDescarga(data.archivo, data.message || 'Archivo generado correctamente.');
                mostrarResultadosArchivo(data.archivo);
                $totalRegistros.text(`Resultados generados (${data.registros || 0})`);
            })
            .catch(function (error) {
                const mensajeBackend = error
                    && error.response
                    && error.response.data
                    && error.response.data.message
                    ? error.response.data.message
                    : '';

                notificar(mensajeBackend || 'Error al exportar datos.', 'danger');
            })
            .then(function () {
                $btnExportar.prop('disabled', false);
            });
    }

    function normalizarConfiguracionPersistida(data) {
        let separador = DEFAULT_SEPARADOR;
        let encabezados = 0;
        let listado = [];
        let filtros = crearFiltrosVacios();
        let filtrosDescripcion = crearFiltrosDescripcionVacia();

        if ($.isArray(data)) {
            listado = data;
        } else if (data && $.isArray(data.campos)) {
            listado = data.campos;
            separador = normalizarSeparadorCliente(data.separador);
            encabezados = normalizarEncabezados(data.encabezados);
            filtros = normalizarFiltrosConfiguracion(data.filtros);
            filtrosDescripcion = normalizarFiltrosDescripcionConfiguracion(data.filtrosDescripcion);
        } else if (data && data.data && $.isArray(data.data.campos)) {
            listado = data.data.campos;
            separador = normalizarSeparadorCliente(data.data.separador);
            encabezados = normalizarEncabezados(data.data.encabezados);
            filtros = normalizarFiltrosConfiguracion(data.data.filtros);
            filtrosDescripcion = normalizarFiltrosDescripcionConfiguracion(data.data.filtrosDescripcion);
        } else if (data && $.isArray(data.data)) {
            listado = data.data;
        }

        return {
            separador: separador,
            encabezados: encabezados,
            campos: normalizarCamposPersistidos(listado),
            filtros: filtros,
            filtrosDescripcion: filtrosDescripcion
        };
    }

    function normalizarCamposPersistidos(listado) {
        const listadoSeguro = $.isArray(listado) ? listado : [];

        const normalizados = $.map(listadoSeguro, function (item) {
            if (!item) {
                return null;
            }

            const posicion = parseInt(item.posicion, 10);
            const tamano = parseInt(item.tamano, 10);

            const tamanoValido = (item.formato === FORMATOS.TEXTO || item.formato === FORMATOS.HORAS)
                ? tamano === 0
                : (esFormatoFecha(item.formato) ? tamano >= 0 : esEnteroPositivo(tamano));

            if (!esEnteroPositivo(posicion) || !tamanoValido || !item.tipo || !item.formato) {
                return null;
            }

            return {
                uid: parseInt(item.uid, 10) || 0,
                posicion: posicion,
                tipo: item.tipo,
                tipoLabel: item.tipoLabel || labelsTipo[item.tipo] || '',
                subtipo: item.subtipo || '',
                subtipoLabel: item.subtipoLabel || '',
                tamano: tamano,
                formato: item.formato,
                formatoLabel: item.formatoLabel || labelsFormato[item.formato] || ''
            };
        });

        normalizados.sort(function (a, b) {
            return a.posicion - b.posicion;
        });

        return normalizados;
    }

    function crearFiltrosVacios() {
        return {
            Lega: [],
            Empr: [],
            Plan: [],
            Conv: [],
            Sect: [],
            Sec2: [],
            Grup: [],
            Sucu: []
        };
    }

    function crearFiltrosDescripcionVacia() {
        return {
            Lega: [],
            Empr: [],
            Plan: [],
            Conv: [],
            Sect: [],
            Sec2: [],
            Grup: [],
            Sucu: []
        };
    }

    function normalizarListaFiltros(lista) {
        if (!$.isArray(lista)) {
            return [];
        }

        const salida = [];
        $.each(lista, function (_, item) {
            const codigo = String(item || '').trim();
            if (codigo !== '') {
                salida.push(codigo);
            }
        });

        return Array.from(new Set(salida));
    }

    function actualizarContadorAsignados(filtrosNormalizados) {
        if (!$asignados.length) {
            return;
        }

        let counter = 0;
        $.each(filtrosNormalizados || {}, function (_, lista) {
            if ($.isArray(lista)) {
                counter += lista.length;
            }
        });

        $asignados.text(`(${counter})`);
        $camposAsignados.attr('aria-label', `Asignados (${counter})`);
    }

    function normalizarFiltrosConfiguracion(filtros) {
        const normalizados = crearFiltrosVacios();
        if (!filtros || typeof filtros !== 'object') {
            actualizarContadorAsignados(normalizados);
            return normalizados;
        }

        $.each(normalizados, function (clave) {
            normalizados[clave] = normalizarListaFiltros(filtros[clave]);
        });

        actualizarContadorAsignados(normalizados);
        return normalizados;
    }

    function normalizarFiltrosDescripcionConfiguracion(filtrosDescripcion) {
        const normalizados = crearFiltrosDescripcionVacia();
        if (!filtrosDescripcion || typeof filtrosDescripcion !== 'object') {
            return normalizados;
        }

        $.each(normalizados, function (clave) {
            const items = $.isArray(filtrosDescripcion[clave]) ? filtrosDescripcion[clave] : [];
            const salida = [];

            $.each(items, function (_, item) {
                if (!item || typeof item !== 'object') {
                    return;
                }

                const id = String(item.id || '').trim();
                const text = String(item.text || '').trim();
                if (id !== '') {
                    salida.push({
                        id: id,
                        text: text !== '' ? text : id
                    });
                }
            });

            normalizados[clave] = salida;
        });

        return normalizados;
    }

    function obtenerFiltrosDesdeSelects() {
        const filtros = crearFiltrosVacios();

        $.each(MAPA_FILTROS_SELECT, function (clave, selector) {
            filtros[clave] = normalizarListaFiltros($(selector).val());
        });

        return filtros;
    }

    function obtenerFiltrosDescripcionDesdeSelects() {
        const descripciones = crearFiltrosDescripcionVacia();

        $.each(MAPA_FILTROS_SELECT, function (clave, selector) {
            const $select = $(selector);
            const selectedIds = normalizarListaFiltros($select.val());
            const dataSelect2 = ($select.data('select2') && $.isFunction($select.select2)) ? ($select.select2('data') || []) : [];
            const textoPorId = {};

            $.each(dataSelect2, function (_, item) {
                if (!item) {
                    return;
                }

                const id = String(item.id || '').trim();
                const text = String(item.text || '').trim();
                if (id !== '') {
                    textoPorId[id] = text !== '' ? text : id;
                }
            });

            const salida = [];
            $.each(selectedIds, function (_, id) {
                let text = textoPorId[id] || '';
                if (text === '') {
                    text = String($select.find('option[value="' + id.replace(/"/g, '\\"') + '"]').text() || '').trim();
                }
                salida.push({
                    id: id,
                    text: text !== '' ? text : id
                });
            });

            descripciones[clave] = salida;
        });

        return descripciones;
    }

    function obtenerFiltrosActuales() {
        if (filtrosInicializados) {
            return normalizarFiltrosConfiguracion(obtenerFiltrosDesdeSelects());
        }

        return normalizarFiltrosConfiguracion(filtrosSeleccionadosPlantilla);
    }

    function obtenerFiltrosDescripcionActuales() {
        if (filtrosInicializados) {
            return normalizarFiltrosDescripcionConfiguracion(obtenerFiltrosDescripcionDesdeSelects());
        }

        return normalizarFiltrosDescripcionConfiguracion(filtrosDescripcionPlantilla);
    }

    function setearSeleccionSelectFiltro(selector, valores, descripciones) {
        const $select = $(selector);
        if (!$select.length) {
            return;
        }

        const lista = normalizarListaFiltros(valores);
        const descripcionesLista = $.isArray(descripciones) ? descripciones : [];
        const descripcionPorId = {};

        $.each(descripcionesLista, function (_, item) {
            if (!item || typeof item !== 'object') {
                return;
            }

            const id = String(item.id || '').trim();
            const text = String(item.text || '').trim();
            if (id !== '') {
                descripcionPorId[id] = text !== '' ? text : id;
            }
        });

        $select.find('option[data-filtro-persistido="1"]').remove();

        const opcionesExistentes = {};
        $select.find('option').each(function () {
            opcionesExistentes[String($(this).val())] = true;
        });

        $.each(lista, function (_, codigo) {
            if (!opcionesExistentes[codigo]) {
                const texto = descripcionPorId[codigo] || codigo;
                const option = new Option(texto, codigo, false, false);
                $(option).attr('data-filtro-persistido', '1');
                $select.append(option);
            }
        });

        if (lista.length === 0) {
            $select.val(null).trigger('change');
            return;
        }

        $select.val(lista).trigger('change');
    }

    function aplicarFiltrosConfiguradosEnVista() {
        if (!filtrosInicializados) {
            return;
        }

        aplicandoFiltrosPersistidos = true;

        $.each(MAPA_FILTROS_SELECT, function (clave, selector) {
            setearSeleccionSelectFiltro(
                selector,
                filtrosSeleccionadosPlantilla[clave] || [],
                filtrosDescripcionPlantilla[clave] || []
            );
        });

        $("#selectjs_seccion").prop("disabled", (filtrosSeleccionadosPlantilla.Sect || []).length === 0);

        aplicandoFiltrosPersistidos = false;
    }

    function guardarFiltrosConDebounce() {
        if (timerGuardarFiltros) {
            clearTimeout(timerGuardarFiltros);
        }

        timerGuardarFiltros = setTimeout(function () {
            guardarCampos().catch(function () {
                notificar('No se pudieron guardar los filtros.', 'danger');
            });
        }, 300);
    }

    function recalcularIdSecuencial() {
        let maxUid = 0;

        $.each(window.camposLiquidar, function (_, campo) {
            const uid = parseInt(campo.uid, 10) || 0;
            if (uid > maxUid) {
                maxUid = uid;
            }
        });

        idSecuencial = maxUid + 1;
    }

    function LimpiarFiltros() {
        $.each(MAPA_FILTROS_SELECT, function (_, selector) {
            $(selector).val(null).trigger('change');
        });

        $("#selectjs_seccion").prop("disabled", true);
    }

    function reinicializarSelectsFiltrosPorPlantilla() {
        $.each(MAPA_FILTROS_SELECT, function (_, selector) {
            const $select = $(selector);
            if (!$select.length) {
                return;
            }

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.find('option[data-filtro-persistido="1"]').remove();
            $select.val(null);
        });

        filtrosInicializados = false;
        inicializarSelectsFiltros();
    }

    function inicializarSelectsFiltros() {
        const filtrosNs = '.filtrosModal';

        $btnFiltros.off('click' + filtrosNs).on('click' + filtrosNs, function () {
            if ($modalFiltros.length && typeof $modalFiltros.modal === 'function') {
                $modalFiltros.modal('show');
            }
        });

        $modalFiltros.off('shown.bs.modal' + filtrosNs).on('shown.bs.modal' + filtrosNs, function () {
            if (!filtrosInicializados) {
                select2Estruct("#selectjs_empresa", true, "Empresas", 1);
                select2Estruct("#selectjs_planta", true, "Plantas", 2);
                select2Estruct("#selectjs_convenio", true, "Convenios", 3);
                select2Estruct("#selectjs_sector", true, "Sectores", 4);
                select2Estruct("#selectjs_seccion", true, "Secciones", 5);
                select2Estruct("#selectjs_grupos", true, "Grupos", 6);
                select2Estruct("#selectjs_sucursal", true, "Sucursales", 7);
                select2Estruct("#selectjs_personal", true, "Legajos", 8);
                $("#selectjs_seccion").prop("disabled", true);
                filtrosInicializados = true;
                aplicarFiltrosConfiguradosEnVista();
            }

            $('#selectjs_sector').off('select2:close' + filtrosNs).on('select2:close' + filtrosNs, function (e) {
                e.preventDefault()
                $("#selectjs_seccion").prop("disabled", false);
                $('#selectjs_seccion').val(null).trigger('change');
            });
            
            $('#selectjs_sector').off('select2:unselect' + filtrosNs).on('select2:unselect' + filtrosNs, function (e) {
                e.preventDefault()
                $("#selectjs_seccion").prop("disabled", true);
                $('#selectjs_seccion').val(null).trigger('change');
                $('#selectjs_sector').val(null).trigger("change");
            });
        });

        $(document).off('change' + filtrosNs, SELECTORES_FILTROS).on('change' + filtrosNs, SELECTORES_FILTROS, function () {
            if (aplicandoFiltrosPersistidos) {
                return;
            }

            filtrosSeleccionadosPlantilla = normalizarFiltrosConfiguracion(obtenerFiltrosDesdeSelects());
            filtrosDescripcionPlantilla = normalizarFiltrosDescripcionConfiguracion(obtenerFiltrosDescripcionDesdeSelects());
            guardarFiltrosConDebounce();
        });


        $trashAllIn.off('click' + filtrosNs).on('click' + filtrosNs, async function (e) {
            e.preventDefault();
            e.stopPropagation();

            LimpiarFiltros();

            // const todos = document.querySelectorAll('input[name="Tipo"]');
            // todos.forEach((item) => {
            //     item.checked = false;
            //     item.closest('label').classList.remove('active');
            // });

            // const TipoTodos = document.querySelector('#TipoTodos');
            // TipoTodos.checked = true;
            // TipoTodos.closest('label').classList.add('active');
            // $('input[name="Tipo"]').trigger('change');

            $('.select2-results__option[aria-selected=true]').each(function () {
                $(this).removeClass('select2-results__option--highlighted');
                $(this).removeAttr('aria-selected');
            });
        });
    }
    function inicializarSelects() {

        $footerFilters.remove();
        $tiposLega.remove();

        $plantilla.select2({
            placeholder: 'Seleccionar plantilla',
            allowClear: true,
            width: '100%',
            language: LANGUAGE_SELECT2
        });

        $tipo.select2({
            placeholder: 'Seleccionar tipo',
            allowClear: true,
            width: '100%',
            language: LANGUAGE_SELECT2
        });

        renderizarOpcionesFormato('');

        inicializarSubtipoSelect([]);
        ocultarSubtipo();
    }

    function enlazarEventos() {
        $plantilla.on('change', function () {
            const plantilla = obtenerPlantillaActiva();
            guardarPlantillaEnStorage(plantilla);
            salirModoEdicion();
            reinicializarSelectsFiltrosPorPlantilla();

            if (!plantilla) {
                limpiarConfiguracionVista();
                return;
            }

            cargarCamposGuardados();
        });

        $tipo.on('change', function () {
            manejarCambioTipo();
        });

        $formato.on('change', function () {
            aplicarReglasFormato();
        });

        $agrupacionNombre.on('input', function () {
            const limpio = String($(this).val() || '')
                .replace(/[^\p{L}\p{N} ]/gu, '')
                .slice(0, 20);
            $(this).val(limpio);
        });

        $separador.on('input', function () {
            aplicarReglasSeparador();
            if (($separador.val() || '').length === 1) {
                guardarSeparadorConDebounce();
            }
        });

        $separador.on('blur', function () {
            if (($separador.val() || '') === '') {
                $separador.val(DEFAULT_SEPARADOR);
            }

            aplicarReglasSeparador();
            guardarCampos().catch(function () {
                notificar('No se pudo guardar el separador.', 'danger');
            });
        });

        $encabezados.on('change', function () {
            actualizarTextoSwitchEncabezados();
            guardarCampos().catch(function () {
                notificar('No se pudo guardar la opción de encabezados.', 'danger');
            });
        });

        $btnCrearPlantilla.on('click', function () {
            $inputNombrePlantilla.val('');
            $errorNombrePlantilla.addClass('d-none').text('');

            if ($modalCrearPlantilla.length && typeof $modalCrearPlantilla.modal === 'function') {
                $modalCrearPlantilla.modal('show');
                $inputNombrePlantilla.trigger('focus');
            }
        });

        $btnGuardarPlantilla.on('click', function () {
            crearPlantillaDesdeModal();
        });

        $btnEliminarPlantilla.on('click', function () {
            eliminarPlantillaSeleccionada();
        });

        $btnAgregar.on('click', function () {
            agregarCampo();
        });

        $btnExportar.on('click', function () {
            exportarDatos();
        });

        $datePicker.on('focus', function () {
            $definirCampos.collapse('hide');
        });

        $tablaBody.on('click', '.btn-eliminar-campo', function () {
            const uid = String($(this).data('uid'));
            eliminarCampo(uid);
        });

        $tablaBody.on('click', '.btn-editar-campo', function () {
            const uid = String($(this).data('uid'));
            editarCampo(uid);
        });

        $(document).on('click', '.download-liquidar-custom', function () {
            $.notifyClose();
        });

        $(document).on('click', '.mostrar-liquidar-custom', function () {
            const archivo = String($(this).data('archivo') || '');
            if (!archivo) {
                notificar('No se encontró archivo para mostrar.', 'danger');
                return;
            }

            mostrarResultadosArchivo(archivo);
        });
    }

    function inicializarSubtipoSelect(datos) {
        const esMultiple = $subtipo.prop('multiple') === true;

        const placeholderText = esMultiple ? 'Seleccionar uno o más valores' : 'Seleccionar valor';

        if ($subtipo.hasClass('select2-hidden-accessible')) {
            $subtipo.select2('destroy');
        }

        $subtipo.empty();
        if (!esMultiple) {
            $subtipo.append('<option value=""></option>');
        }

        $.each(datos, function (_, item) {
            const children = $.isArray(item.children) ? item.children : [];

            if (children.length) {
                const $optgroup = $('<optgroup></optgroup>').attr('label', String(item.text || ''));

                $.each(children, function (_, child) {
                    const childValue = String(child.id || child.value || '').trim();
                    if (!childValue) {
                        return;
                    }

                    $optgroup.append(
                        $('<option></option>').val(childValue).text(String(child.text || childValue))
                    );
                });

                if ($optgroup.children().length) {
                    $subtipo.append($optgroup);
                }
                return;
            }

            const value = String(item.value || item.id || '').trim();
            if (!value) {
                return;
            }

            $subtipo.append(
                $('<option></option>').val(value).text(String(item.text || value))
            );
        });

        $subtipo.select2({
            placeholder: placeholderText,
            allowClear: !esMultiple,
            width: '100%'
        });

        if (esMultiple) {
            // Evita que quede seleccionado el valor vacío heredado del modo simple.
            $subtipo.val(null).trigger('change.select2');
        }
    }

    function esTipoSubtipoMultiple(tipo) {
        return tipo === TIPOS.HORAS_AGRUPADAS;
    }

    function configurarModoSubtipo(tipo) {
        const multiple = esTipoSubtipoMultiple(tipo);
        $subtipo.prop('multiple', multiple);

        if (multiple) {
            // Limpia estado previo para que Select2 pueda mostrar placeholder al primer render.
            $subtipo.val(null);
        }
    }

    function mostrarAgrupacion() {
        $agrupacionWrapper.removeClass('d-none');
    }

    function ocultarAgrupacion() {
        $agrupacionWrapper.addClass('d-none');
        $agrupacionNombre.val('');
    }

    function mostrarSubtipo() {
        $subtipoWrapper.removeClass('d-none');
    }

    function ocultarSubtipo() {
        if ($subtipo.hasClass('select2-hidden-accessible')) {
            $subtipo.select2('destroy');
        }

        $subtipo.prop('multiple', false);
        $subtipo.empty().append('<option value=""></option>');
        $subtipoWrapper.addClass('d-none');
        $subtipo.val('');
        $subtipoHelp.addClass('d-none').text('Cargando...');
    }

    function setSubtipoCargando(cargando) {
        if (cargando) {
            $subtipo.prop('disabled', true);
            $subtipoHelp.removeClass('d-none').text('Cargando...');
        } else {
            $subtipo.prop('disabled', false);
            $subtipoHelp.addClass('d-none').text('Cargando...');
        }
    }

    function manejarCambioTipo() {
        limpiarErrores();

        const tipo = $tipo.val();
        aplicarReglasTipo(tipo);
        aplicarReglasFormato();

        if (tipo === TIPOS.HORAS_AGRUPADAS) {
            mostrarAgrupacion();
        } else {
            ocultarAgrupacion();
        }

        if (!esTipoConSubtipo(tipo)) {
            ocultarSubtipo();
            return;
        }

        configurarModoSubtipo(tipo);
        mostrarSubtipo();
        cargarSubtipo(tipo);
    }

    function cargarSubtipo(tipo) {
        setSubtipoCargando(true);

        if (cacheCatalogos[tipo]) {
            inicializarSubtipoSelect(cacheCatalogos[tipo]);
            setSubtipoCargando(false);
            return Promise.resolve();
        }

        const endpoint = tipo === TIPOS.NOVEDADES
            ? '../../app-data/novedades'
            : '../../app-data/horas';

        return axios.get(endpoint)
            .then(function (response) {
                const listado = normalizarListado(response.data);
                const opciones = mapearSubtipos(tipo, listado);
                cacheCatalogos[tipo] = opciones;
                inicializarSubtipoSelect(opciones);
            })
            .catch(function () {
                inicializarSubtipoSelect([]);
                mostrarFeedback('No se pudo cargar el catalogo de ' + labelsTipo[tipo] + '.', 'danger');
            })
            .then(function () {
                setSubtipoCargando(false);
            });
    }

    function normalizarListado(data) {
        if ($.isArray(data)) {
            return data;
        }

        if (data && $.isArray(data.data)) {
            return data.data;
        }

        return [];
    }

    function mapearSubtipos(tipo, listado) {
        if (tipo === TIPOS.NOVEDADES) {
            const grupos = {};
            const ordenGrupos = [];

            $.each(listado, function (_, item) {
                const codigo = String(item.NovCodi || '').trim();
                if (!codigo) {
                    return;
                }

                const descripcion = String(item.NovDesc || '').trim();
                const textoOpcion = descripcion ? (codigo + ' - ' + descripcion) : codigo;
                const tipoGrupo = String(item.NovTipoDesc || item.tipo || 'Sin tipo').trim() || 'Sin tipo';

                if (!grupos[tipoGrupo]) {
                    grupos[tipoGrupo] = {
                        text: tipoGrupo,
                        children: []
                    };
                    ordenGrupos.push(tipoGrupo);
                }

                grupos[tipoGrupo].children.push({
                    id: codigo,
                    text: textoOpcion
                });
            });

            return $.map(ordenGrupos, function (key) {
                return grupos[key];
            });
        }

        const grupos = {};
        const ordenGrupos = [];

        $.each(listado, function (_, item) {
            const codigo = String(item.THoCodi || '').trim();
            if (!codigo) {
                return;
            }

            const descripcion = String(item.THoDesc || '').trim();
            const id = String(item.THoID || item.id || '').trim();
            const textoOpcion = descripcion ? (codigo + ' - ' + descripcion) : codigo;
            const idGrupo = id || 'Sin id';

            if (!grupos[idGrupo]) {
                grupos[idGrupo] = {
                    text: idGrupo,
                    children: []
                };
                ordenGrupos.push(idGrupo);
            }

            grupos[idGrupo].children.push({
                id: codigo,
                text: textoOpcion
            });
        });

        return $.map(ordenGrupos, function (key) {
            return grupos[key];
        });
    }

    function agregarCampo() {
        limpiarErrores();

        const datos = leerFormulario();
        const validacion = validarFormulario(datos);

        if (!validacion.ok) {
            notificar('Campos Obligatorios.', 'danger');
            return;
        }

        const enEdicion = uidEnEdicion !== null;

        if (enEdicion) {
            actualizarCampoEnEdicion(datos);
        } else {
            insertarOActualizarPorPosicion(datos);
        }

        renderizarLista();
        guardarCampos().catch(function () {
            notificar('No se pudo guardar la configuración.', 'danger');
        });

        salirModoEdicion();

        if (enEdicion) {
            resetFormularioCampo();
        } else {
            prepararProximoCampo();
        }

        notificar(enEdicion ? 'Campo editado correctamente.' : 'Campo agregado correctamente.', 'success');
    }

    function leerFormulario() {
        const tipo = $tipo.val();
        const nombreAgrupacion = String($agrupacionNombre.val() || '').trim();
        const subtipoValor = esTipoSubtipoMultiple(tipo)
            ? (($subtipo.val() || []).filter(function (item) { return String(item || '').trim() !== ''; }))
            : $subtipo.val();
        const subtipoLabel = esTipoSubtipoMultiple(tipo)
            ? nombreAgrupacion
            : obtenerTextoSeleccionado($subtipo);

        return {
            uid: idSecuencial++,
            posicion: parseInt($posicion.val(), 10),
            tipo: tipo,
            tipoLabel: labelsTipo[tipo] || '',
            subtipo: subtipoValor,
            subtipoLabel: subtipoLabel,
            tamano: parseInt($tamano.val(), 10),
            formato: $formato.val(),
            formatoLabel: labelsFormato[$formato.val()] || ''
        };
    }

    function esNombreAgrupacionValido(nombre) {
        return /^[\p{L}\p{N} ]{1,20}$/u.test(nombre);
    }

    function validarFormulario(datos) {
        let cantidadErrores = 0;

        if (!esEnteroPositivo(datos.posicion)) {
            cantidadErrores += 1;
        }

        if (!datos.tipo) {
            cantidadErrores += 1;
        }

        if (esTipoConSubtipo(datos.tipo)) {
            if (esTipoSubtipoMultiple(datos.tipo)) {
                if (!$.isArray(datos.subtipo) || !datos.subtipo.length) {
                    cantidadErrores += 1;
                }

                if (!esNombreAgrupacionValido(String(datos.subtipoLabel || '').trim())) {
                    cantidadErrores += 1;
                }
            } else if (!datos.subtipo) {
                cantidadErrores += 1;
            }
        }

        if (datos.tipo === TIPOS.HORAS_AGRUPADAS && (datos.formato !== FORMATOS.DECIMAL && datos.formato !== FORMATOS.HORAS)) {
            cantidadErrores += 1;
        }

        if (datos.formato === FORMATOS.TEXTO || datos.formato === FORMATOS.HORAS) {
            if (datos.tamano !== 0) {
                cantidadErrores += 1;
            }
        } else if (esFormatoFecha(datos.formato)) {
            if (datos.tamano < 0 || Number.isNaN(datos.tamano)) {
                cantidadErrores += 1;
            }
        } else if (!esEnteroPositivo(datos.tamano)) {
            cantidadErrores += 1;
        }

        if (!datos.formato) {
            cantidadErrores += 1;
        }

        return {
            ok: cantidadErrores === 0
        };
    }

    function limpiarErrores() {
        $feedback.addClass('d-none').removeClass('alert-danger alert-success').text('');
    }

    function limpiarCampo($campo) {
        if ($campo.hasClass('select2-hidden-accessible')) {
            $campo.val(null).trigger('change');
        } else {
            $campo.val('');
        }
    }

    function insertarOActualizarPorPosicion(nuevoCampo) {
        let indexExistente = -1;

        $.each(window.camposLiquidar, function (index, campo) {
            if (campo.posicion === nuevoCampo.posicion) {
                indexExistente = index;
                return false;
            }
        });

        if (indexExistente !== -1) {
            nuevoCampo.uid = window.camposLiquidar[indexExistente].uid;
            window.camposLiquidar[indexExistente] = nuevoCampo;
            return;
        }

        window.camposLiquidar.push(nuevoCampo);
        compactarPosiciones();
    }

    function actualizarCampoEnEdicion(nuevoCampo) {
        const uidEditando = String(uidEnEdicion);
        const campoOriginal = $.grep(window.camposLiquidar, function (campo) {
            return String(campo.uid) === uidEditando;
        })[0];

        if (!campoOriginal) {
            insertarOActualizarPorPosicion(nuevoCampo);
            return;
        }

        nuevoCampo.uid = campoOriginal.uid;

        // Mantener todos menos: el campo original en edición y el campo de la posicion destino.
        window.camposLiquidar = $.grep(window.camposLiquidar, function (campo) {
            const esOriginal = String(campo.uid) === uidEditando;
            const esPosicionDestino = Number(campo.posicion) === Number(nuevoCampo.posicion)
                && String(campo.uid) !== uidEditando;

            return !esOriginal && !esPosicionDestino;
        });

        window.camposLiquidar.push(nuevoCampo);

        compactarPosiciones();
    }

    function renderizarLista() {
        ordenarCampos();

        if (!window.camposLiquidar.length) {
            $tablaBody.html(
                '<tr id="tabla-campos-vacio">' +
                '<td colspan="6" class="text-center text-muted">Todavía no hay campos agregados.</td>' +
                '</tr>'
            );
            return;
        }

        const filas = $.map(window.camposLiquidar, function (campo) {
            const tipoColumna = obtenerTextoColumnaTipo(campo);

            return '' +
                '<tr data-uid="' + escapeHtml(String(campo.uid)) + '">' +
                '<td>' + escapeHtml(String(campo.posicion)) + '</td>' +
                '<td>' + escapeHtml(tipoColumna) + '</td>' +
                '<td>' + (campo.subtipoLabel || '-') + '</td>' +
                '<td class="text-center">' + escapeHtml(String(campo.tamano)) + '</td>' +
                '<td>' + escapeHtml(campo.formatoLabel) + '</td>' +
                '<td class="text-center">' +
                '<div class="p-1 shadow-sm radius d-inline-flex gap5">' +
                '<button aria-label="Arrastrar para ordenar" type="button" class="hint--top btn btn-sm btn-outline-secondary border-0 btn-ordenar-campo" title="Arrastrar para ordenar"><i class="bi bi-list"></i></button>' +
                '<button aria-label="Editar campo" type="button" class="hint--top btn btn-sm btn-outline-info border-0 btn-editar-campo" data-uid="' + escapeHtml(String(campo.uid)) + '" title="Editar"><i class="bi bi-pen"></i></button>' +
                '<button aria-label="Eliminar campo" type="button" class="hint--top btn btn-sm btn-outline-danger border-0 btn-eliminar-campo" data-uid="' + escapeHtml(String(campo.uid)) + '"><i class="bi bi-trash"></i></button>' +
                '</div>' +
                '</td>' +
                '</tr>';
        });

        $tablaBody.html(filas.join(''));
    }

    function obtenerTextoColumnaTipo(campo) {
        if (!campo || !campo.tipo) {
            return '-';
        }

        const keyEtiqueta = MAP_TIPO_ETIQUETA[campo.tipo];
        if (!keyEtiqueta) {
            return campo.tipoLabel || '-';
        }

        const etiquetas = cacheCatalogos.etiquetasParagene || {};
        const etiquetaBase = etiquetas[keyEtiqueta] || MAP_TIPO_ETIQUETA_FALLBACK[campo.tipo] || '-';

        if (campo.tipo === TIPOS.CUIT_EMPRESA) {
            return 'Cuit ' + etiquetaBase;
        }

        return etiquetaBase;
    }

    function eliminarCampo(uid) {
        window.camposLiquidar = $.grep(window.camposLiquidar, function (campo) {
            return String(campo.uid) !== String(uid);
        });

        if (String(uidEnEdicion) === String(uid)) {
            salirModoEdicion();
        }

        compactarPosiciones();
        renderizarLista();
        guardarCampos().catch(function () {
            notificar('No se pudo guardar la configuración.', 'danger');
        });

        // mostrarFeedback('Campo eliminado.', 'success');
        notificar('Campo eliminado.', 'success');
    }

    function compactarPosiciones() {
        ordenarCampos();
        $.each(window.camposLiquidar, function (index, campo) {
            campo.posicion = index + 1;
        });
    }

    function ordenarCampos() {
        window.camposLiquidar.sort(function (a, b) {
            return a.posicion - b.posicion;
        });
    }

    function prepararProximoCampo() {
        limpiarErrores();
        limpiarCampo($subtipo);
        $posicion.val(String(obtenerSiguientePosicion()));
        $posicion.trigger('focus');
    }

    function resetFormularioCampo() {
        limpiarErrores();
        ocultarSubtipo();
        ocultarAgrupacion();
        $tipo.val(null).trigger('change.select2');
        renderizarOpcionesFormato('');
        $formato.val(null).trigger('change.select2');
        $tamano.val('').prop('disabled', false);
        $posicion.val(String(obtenerSiguientePosicion()));
        $posicion.trigger('focus');
    }

    function actualizarTextoBotonCampo() {
        $btnAgregar.text(uidEnEdicion === null ? TEXTO_BOTON_AGREGAR : TEXTO_BOTON_EDITAR);
    }

    function salirModoEdicion() {
        uidEnEdicion = null;
        actualizarTextoBotonCampo();
    }

    function setSubtipoSeleccionado(valor, etiqueta) {
        if ($subtipo.prop('multiple') === true) {
            const values = $.isArray(valor)
                ? $.map(valor, function (item) { return String(item || '').trim(); })
                : [];

            if (!values.length) {
                $subtipo.val(null).trigger('change.select2');
                return;
            }

            $.each(values, function (_, valueItem) {
                if (!valueItem) {
                    return;
                }

                if ($subtipo.find('option[value="' + valueItem.replace(/"/g, '\\"') + '"]').length === 0) {
                    $subtipo.append($('<option></option>').val(valueItem).text(valueItem));
                }
            });

            $subtipo.val(values).trigger('change.select2');
            return;
        }

        const value = String(valor || '');

        if (!value) {
            $subtipo.val(null).trigger('change.select2');
            return;
        }

        if ($subtipo.find('option[value="' + value.replace(/"/g, '\\"') + '"]').length === 0) {
            const texto = etiqueta || value;
            $subtipo.append($('<option></option>').val(value).text(texto));
        }

        $subtipo.val(value).trigger('change.select2');
    }

    function editarCampo(uid) {
        const campo = $.grep(window.camposLiquidar, function (item) {
            return String(item.uid) === String(uid);
        })[0];

        if (!campo) {
            notificar('No se encontró el campo a editar.', 'danger');
            return;
        }

        uidEnEdicion = campo.uid;
        actualizarTextoBotonCampo();

        $posicion.val(String(campo.posicion));
        $tipo.val(campo.tipo).trigger('change');

        const aplicarValores = function () {
            if (esTipoConSubtipo(campo.tipo)) {
                setSubtipoSeleccionado(campo.subtipo, campo.subtipoLabel);
            }

            if (campo.tipo === TIPOS.HORAS_AGRUPADAS) {
                $agrupacionNombre.val(String(campo.subtipoLabel || ''));
            }

            $formato.val(campo.formato).trigger('change');
            $tamano.val(String(campo.tamano));
        };

        if (esTipoConSubtipo(campo.tipo)) {
            cargarSubtipo(campo.tipo).then(function () {
                aplicarValores();
            });
        } else {
            aplicarValores();
        }

        $posicion.trigger('focus');
    }

    function obtenerSiguientePosicion() {
        return window.camposLiquidar.length + 1;
    }

    function actualizarPosicionSugerida(forzar) {
        if (forzar === true || !$posicion.val()) {
            $posicion.val(String(obtenerSiguientePosicion()));
        }
    }

    function esFormatoFecha(formato) {
        return formato === FORMATOS.FECHA_YMD
            || formato === FORMATOS.FECHA_YMD_COMPACTA
            || formato === FORMATOS.FECHA_YMD_SLASH
            || formato === FORMATOS.FECHA_MDY
            || formato === FORMATOS.FECHA_MDY_COMPACTA
            || formato === FORMATOS.FECHA_MDY_SLASH
            || formato === FORMATOS.FECHA_DMY
            || formato === FORMATOS.FECHA_DMY_COMPACTA
            || formato === FORMATOS.FECHA_DMY_SLASH;
    }

    function esTipoConSubtipo(tipo) {
        return tipo === TIPOS.NOVEDADES || tipo === TIPOS.HORAS || tipo === TIPOS.HORAS_AGRUPADAS;
    }

    function obtenerOpcionesFormatoPorTipo(tipo) {
        switch (tipo) {
            case TIPOS.FECHA:
                return [
                    FORMATOS.FECHA_YMD,
                    FORMATOS.FECHA_YMD_COMPACTA,
                    FORMATOS.FECHA_YMD_SLASH,
                    FORMATOS.FECHA_MDY,
                    FORMATOS.FECHA_MDY_COMPACTA,
                    FORMATOS.FECHA_MDY_SLASH,
                    FORMATOS.FECHA_DMY,
                    FORMATOS.FECHA_DMY_COMPACTA,
                    FORMATOS.FECHA_DMY_SLASH
                ];
            case TIPOS.NOVEDADES:
            case TIPOS.HORAS:
            case TIPOS.HORAS_AGRUPADAS:
            case TIPOS.ATRA:
            case TIPOS.TRAB:
                return [FORMATOS.DECIMAL, FORMATOS.HORAS];
            case TIPOS.PRIMER_FICHADA:
            case TIPOS.ULTIMA_FICHADA:
            case TIPOS.TODAS_FICHADAS:
                return [FORMATOS.HORAS];
            case TIPOS.LEGAJO:
            case TIPOS.DNI_LEGAJO:
            case TIPOS.COD_EMPRESA:
            case TIPOS.COD_PLANTA:
            case TIPOS.COD_CONVENIO:
            case TIPOS.COD_SECTOR:
            case TIPOS.COD_SECCION:
            case TIPOS.COD_GRUPO:
            case TIPOS.COD_SUCURSAL:
            case TIPOS.LABO:
            case TIPOS.FERI:
                return [FORMATOS.NUMERO];
            case TIPOS.CUIL_LEGAJO:
            case TIPOS.APNO:
            case TIPOS.CUIT_EMPRESA:
            case TIPOS.TURSTR:
                return [FORMATOS.TEXTO];
            default:
                return [FORMATOS.NUMERO, FORMATOS.DECIMAL, FORMATOS.TEXTO];
        }
    }

    function renderizarOpcionesFormato(tipo) {
        const opciones = obtenerOpcionesFormatoPorTipo(tipo);
        const valorActual = $formato.val();

        if ($formato.hasClass('select2-hidden-accessible')) {
            $formato.select2('destroy');
        }

        $formato.empty();
        $formato.append('<option value=""></option>');

        $.each(opciones, function (_, value) {
            $formato.append(
                $('<option></option>').val(value).text(labelsFormato[value])
            );
        });

        $formato.select2({
            placeholder: 'Seleccionar formato',
            allowClear: true,
            width: '100%'
        });

        if ($.inArray(valorActual, opciones) !== -1) {
            $formato.val(valorActual).trigger('change.select2');
        } else {
            $formato.val(null).trigger('change.select2');
        }
    }

    function aplicarReglasTipo(tipo) {
        renderizarOpcionesFormato(tipo);

        switch (tipo) {
            case TIPOS.FECHA:
                $formato.prop('disabled', false);
                $formato.val(FORMATOS.FECHA_YMD).trigger('change');
                $tamano.prop('disabled', true).val('0');
                break;
            case TIPOS.LEGAJO:
                $formato.val(FORMATOS.NUMERO).trigger('change');
                $formato.prop('disabled', true);
                $tamano.prop('disabled', false).val('1');
                break;
            case TIPOS.APNO:
            case TIPOS.CUIT_EMPRESA:
                $formato.val(FORMATOS.TEXTO).trigger('change');
                $formato.prop('disabled', true);
                $tamano.prop('disabled', true).val('0');
                break;
            case TIPOS.DNI_LEGAJO:
                $formato.val(FORMATOS.NUMERO).trigger('change');
                $formato.prop('disabled', true);
                $tamano.prop('disabled', false).val('1');
                break;
            case TIPOS.CUIL_LEGAJO:
                $formato.val(FORMATOS.TEXTO).trigger('change');
                $formato.prop('disabled', true);
                $tamano.prop('disabled', true).val('0');
                break;
            case TIPOS.COD_EMPRESA:
            case TIPOS.COD_PLANTA:
            case TIPOS.COD_CONVENIO:
            case TIPOS.COD_SECTOR:
            case TIPOS.COD_SECCION:
            case TIPOS.COD_GRUPO:
            case TIPOS.COD_SUCURSAL:
                $formato.val(FORMATOS.NUMERO).trigger('change');
                $formato.prop('disabled', true);
                $tamano.prop('disabled', false).val('1');
                break;
            case TIPOS.NOVEDADES:
            case TIPOS.HORAS:
            case TIPOS.HORAS_AGRUPADAS:
            case TIPOS.ATRA:
            case TIPOS.TRAB:
                $formato.prop('disabled', false);
                $formato.val(FORMATOS.DECIMAL).trigger('change');
                $tamano.prop('disabled', false).val('1');
                break;
            case TIPOS.PRIMER_FICHADA:
            case TIPOS.ULTIMA_FICHADA:
            case TIPOS.TODAS_FICHADAS:
                $formato.val(FORMATOS.HORAS).trigger('change');
                $formato.prop('disabled', true);
                $tamano.prop('disabled', true).val('0');
                break;
            case TIPOS.TURSTR:
                $formato.val(FORMATOS.TEXTO).trigger('change');
                $formato.prop('disabled', true);
                $tamano.prop('disabled', true).val('0');
                break;
            case TIPOS.LABO:
            case TIPOS.FERI:
                $formato.val(FORMATOS.NUMERO).trigger('change');
                $formato.prop('disabled', true);
                $tamano.prop('disabled', false).val('1');
                break;
            default:
                $formato.prop('disabled', false);
                $tamano.prop('disabled', false);
        }
    }

    function aplicarReglasFormato() {
        if ($formato.val() === FORMATOS.TEXTO || $formato.val() === FORMATOS.HORAS) {
            $tamano.val('0');
            $tamano.prop('disabled', true);
            return;
        }

        if ($tipo.val() === TIPOS.FECHA) {
            if (!esFormatoFecha($formato.val())) {
                $formato.val(FORMATOS.FECHA_YMD).trigger('change.select2');
            }
            $tamano.val('0');
            $tamano.prop('disabled', true);
            return;
        }

        if ($tipo.val() === TIPOS.LEGAJO) {
            $tamano.prop('disabled', false);
            return;
        }

        if (
            ($tipo.val() === TIPOS.NOVEDADES || $tipo.val() === TIPOS.HORAS || $tipo.val() === TIPOS.ATRA || $tipo.val() === TIPOS.TRAB)
            && $formato.val() === FORMATOS.DECIMAL
            && !esEnteroPositivo(parseInt($tamano.val(), 10))
        ) {
            $tamano.val('8');
        }

        $tamano.prop('disabled', false);
    }

    function aplicarReglasSeparador() {
        const separador = $separador.val() || '';
        if (separador.length > 1) {
            mostrarFeedback('El separador solo puede tener un caracter.', 'danger');
            $separador.val(separador.charAt(0));
        } else {
            limpiarErrores();
        }

        actualizarResultadoSeparador();
    }

    function actualizarResultadoSeparador() {
        if (!$resultadoSeparador.length) {
            return;
        }

        $resultadoSeparador.text(($separador.val() || '') === ' ' ? 'Espacio' : '');
    }

    function guardarSeparadorConDebounce() {
        if (timerGuardarSeparador) {
            clearTimeout(timerGuardarSeparador);
        }

        timerGuardarSeparador = setTimeout(function () {
            guardarCampos().catch(function () {
                notificar('No se pudo guardar el separador.', 'danger');
            });
        }, 250);
    }

    function normalizarSeparadorCliente(valor) {
        if (valor === null || valor === undefined) {
            return DEFAULT_SEPARADOR;
        }

        const separador = String(valor);
        if (separador.length === 0) {
            return DEFAULT_SEPARADOR;
        }

        return separador.charAt(0);
    }

    function normalizarEncabezados(valor) {
        return Number(valor) === 1 ? 1 : 0;
    }

    function validarNombrePlantilla(nombre) {
        return /^(?=.*[\p{L}\p{N}])[\p{L}\p{N} ]{1,50}$/u.test(nombre);
    }

    function slugPlantillaDesdeNombre(nombre) {
        const base = String(nombre || '').trim().toLowerCase();
        const sinAcentos = base.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        const conUnderscore = sinAcentos.replace(/\s+/g, '_');
        const limpio = conUnderscore
            .replace(/[^a-z0-9_]/g, '')
            .replace(/_+/g, '_')
            .replace(/^_+|_+$/g, '');

        if (!limpio) {
            return DEFAULT_PLANTILLA;
        }

        return limpio.slice(0, 50);
    }

    function existePlantillaEnSelect(slug) {
        return $plantilla.find('option').filter(function () {
            return String($(this).val()) === String(slug);
        }).length > 0;
    }

    function limpiarConfiguracionVista() {
        window.camposLiquidar = [];
        filtrosSeleccionadosPlantilla = crearFiltrosVacios();
        filtrosDescripcionPlantilla = crearFiltrosDescripcionVacia();
        $separador.val(DEFAULT_SEPARADOR);
        $encabezados.prop('checked', false);
        actualizarTextoSwitchEncabezados();
        actualizarResultadoSeparador();
        recalcularIdSecuencial();
        renderizarLista();
        aplicarFiltrosConfiguradosEnVista();
        actualizarPosicionSugerida(true);
    }

    function crearPlantillaDesdeModal() {
        const nombre = String($inputNombrePlantilla.val() || '').trim();
        const slug = slugPlantillaDesdeNombre(nombre);

        if (!validarNombrePlantilla(nombre)) {
            $errorNombrePlantilla
                .removeClass('d-none')
                .text('Ingrese un nombre válido (solo letras, números, acentos y espacios, máximo 50).');
            return;
        }

        if (existePlantillaEnSelect(slug)) {
            $errorNombrePlantilla
                .removeClass('d-none')
                .text('Ya existe una plantilla con ese nombre.');
            return;
        }

        $errorNombrePlantilla.addClass('d-none').text('');
        $btnGuardarPlantilla.prop('disabled', true);

        const payload = {
            nombre: nombre,
            campos: window.camposLiquidar,
            separador: obtenerSeparadorNormalizado(),
            encabezados: $encabezados.is(':checked') ? 1 : 0,
            filtros: obtenerFiltrosActuales(),
            filtrosDescripcion: obtenerFiltrosDescripcionActuales()
        };

        axios.post(ENDPOINT_PLANTILLAS, payload)
            .then(function (response) {
                const data = response.data || {};
                if (data.status !== 'ok' || !data.slug) {
                    throw new Error(data.message || 'No se pudo crear la plantilla.');
                }

                return cargarPlantillas(data.slug).then(function () {
                    guardarPlantillaEnStorage(data.slug);
                    return cargarCamposGuardados();
                });
            })
            .then(function () {
                if ($modalCrearPlantilla.length && typeof $modalCrearPlantilla.modal === 'function') {
                    $modalCrearPlantilla.modal('hide');
                }

                notificar('Plantilla creada correctamente.', 'success');
            })
            .catch(function (error) {
                const mensajeBackend = error
                    && error.response
                    && error.response.data
                    && error.response.data.message
                    ? error.response.data.message
                    : (error.message || 'No se pudo crear la plantilla.');

                $errorNombrePlantilla.removeClass('d-none').text(mensajeBackend);
            })
            .then(function () {
                $btnGuardarPlantilla.prop('disabled', false);
            });
    }

    function eliminarPlantillaSeleccionada() {
        const plantilla = obtenerPlantillaActiva();
        if (!plantilla) {
            notificar('Debe seleccionar una plantilla para eliminar.', 'danger');
            return;
        }

        if (!window.confirm('¿Eliminar la plantilla seleccionada?')) {
            return;
        }

        $btnEliminarPlantilla.prop('disabled', true);

        axios.delete(ENDPOINT_PLANTILLAS, {
            params: {
                plantilla: plantilla
            }
        })
            .then(function (response) {
                const data = response.data || {};
                if (data.status !== 'ok') {
                    throw new Error(data.message || 'No se pudo eliminar la plantilla.');
                }

                return cargarPlantillas('', true);
            })
            .then(function () {
                guardarPlantillaEnStorage('');
                limpiarConfiguracionVista();
                notificar('Plantilla eliminada correctamente.', 'success');
            })
            .catch(function (error) {
                const mensajeBackend = error
                    && error.response
                    && error.response.data
                    && error.response.data.message
                    ? error.response.data.message
                    : (error.message || 'No se pudo eliminar la plantilla.');

                notificar(mensajeBackend, 'danger');
            })
            .then(function () {
                $btnEliminarPlantilla.prop('disabled', false);
            });
    }

    function actualizarTextoSwitchEncabezados() {
        if (!$switchEncabezadosText.length) {
            return;
        }

        $switchEncabezadosText.text($encabezados.is(':checked') ? 'Sí' : 'No');
    }

    function obtenerSeparadorNormalizado() {
        return normalizarSeparadorCliente($separador.val());
    }

    function obtenerTextoSeleccionado($select) {
        const texto = $select.find('option:selected').text();
        return texto || '';
    }

    function esEnteroPositivo(valor) {
        return Number.isInteger(valor) && valor > 0;
    }

    function mostrarFeedback(mensaje, tipo) {
        const clase = tipo === 'success' ? 'alert-success' : 'alert-danger';
        $feedback.removeClass('d-none alert-danger alert-success').addClass(clase).text(mensaje);
    }

    function notificar(mensaje, tipo, delay = 3000, placement = 'right') {
        if (typeof $.notify !== 'function') {
            return;
        }
        $.notifyClose();
        notify(mensaje, tipo, delay, placement);
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function inicializarDatePicker() {
        if (!$datePicker.length || typeof $.fn.daterangepicker !== 'function') {
            return;
        }

        $datePicker.daterangepicker({
            singleDatePicker: false,
            showDropdowns: true,
            showWeekNumbers: false,
            startDate: moment().subtract(1, 'month').startOf('month'),
            endDate: moment().subtract(1, 'month').endOf('month'),
            autoUpdateInput: true,
            opens: 'top',
            autoApply: true,
            linkedCalendars: false,
            ranges: {
                'Esta semana': [moment().day(1), moment().day(7)],
                'Semana Anterior': [moment().subtract(1, 'week').day(1), moment().subtract(1, 'week').day(7)],
                'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
                'Este mes': [moment().startOf('month'), moment().endOf('month')],
                'Mes anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Últimos 30 días': [moment().subtract(29, 'days'), moment()]
            },
            locale: {
                format: 'DD/MM/YYYY',
                separator: ' al ',
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
                fromLabel: 'Desde',
                toLabel: 'Para',
                customRangeLabel: 'Personalizado',
                weekLabel: 'Sem',
                daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                firstDay: 1,
                applyButtonClasses: 'text-white bg-custom'
            }
        });
    }

})(jQuery);
