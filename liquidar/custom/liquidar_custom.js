(function ($) {
    'use strict';

    window.camposLiquidar = window.camposLiquidar || [];

    const TIPOS = {
        LEGAJO: 'legajo',
        FECHA: 'fecha',
        NOVEDADES: 'novedades',
        HORAS: 'horas'
    };

    const FORMATOS = {
        NUMERO: 'numero',
        DECIMAL: 'decimal',
        FECHA_YMD: 'YYYY-MM-DD',
        FECHA_MDY: 'MM-DD-YYYY',
        FECHA_DMY: 'DD-MM-YYYY'
    };

    const cacheCatalogos = {
        novedades: null,
        horas: null
    };

    const labelsTipo = {
        [TIPOS.LEGAJO]: 'Legajo',
        [TIPOS.FECHA]: 'Fecha',
        [TIPOS.NOVEDADES]: 'Novedades',
        [TIPOS.HORAS]: 'Horas'
    };

    const labelsFormato = {
        [FORMATOS.NUMERO]: 'Numero',
        [FORMATOS.DECIMAL]: 'Decimal',
        [FORMATOS.FECHA_YMD]: 'YYYY-MM-DD',
        [FORMATOS.FECHA_MDY]: 'MM-DD-YYYY',
        [FORMATOS.FECHA_DMY]: 'DD-MM-YYYY'
    };

    let idSecuencial = 1;
    const ENDPOINT_CAMPOS = '../../app-data/liquidar/custom/campos';
    const ENDPOINT_EXPORT = '../../app-data/liquidar/custom/export';

    const $posicion = $('#campo-posicion');
    const $tipo = $('#campo-tipo');
    const $subtipo = $('#campo-subtipo');
    const $subtipoWrapper = $('#subtipo-wrapper');
    const $subtipoHelp = $('#subtipo-help');
    const $tamano = $('#campo-tamano');
    const $formato = $('#campo-formato');
    const $feedback = $('#campo-feedback');
    const $tablaBody = $('#tabla-campos-body');
    const $btnAgregar = $('#btn-agregar-campo');
    const $btnExportar = $('#btn-exportar');
    const $datePicker = $('#date-picker');
    const $resultadoExportacion = $('#resultado-exportacion');
    const $resultadoExportacionArchivo = $('#resultado-exportacion-archivo');
    const $resultadoExportacionContenido = $('#resultado-exportacion-contenido');
    const $definirCampos = $('#definirCampos');

    $(document).ready(function () {
        inicializarSelects();
        enlazarEventos();
        inicializarOrdenamientoTabla();
        cargarCamposGuardados();
        inicializarDatePicker();
    });

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
                    notificar('No se pudo guardar la configuracion.', 'danger');
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
        axios.get(ENDPOINT_CAMPOS)
            .then(function (response) {
                window.camposLiquidar = normalizarCamposPersistidos(response.data);
                recalcularIdSecuencial();
                renderizarLista();
                actualizarPosicionSugerida();
            })
            .catch(function () {
                window.camposLiquidar = [];
                renderizarLista();
                actualizarPosicionSugerida();
                notificar('No se pudo cargar la configuracion guardada.', 'danger');
            });
    }

    function guardarCampos() {
        return axios.post(ENDPOINT_CAMPOS, {
            campos: window.camposLiquidar
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
            return 'El rango de fechas no puede superar 31 dias.';
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
            '<button type="button" class="btn btn-dark border btn-sm font08 w150 mostrar-liquidar-custom" data-archivo="' + escapeHtml(archivo) + '">Mostrar Resultados</button>' +
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
                $resultadoExportacionContenido.text(contenido || 'Archivo vacio.');
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
        const rango = obtenerRangoFechas();
        const errorRango = validarRangoExportacion(rango);
        if (errorRango) {
            notificar(errorRango, 'danger');
            return;
        }

        $btnExportar.prop('disabled', true);

        const payload = {
            FechIni: rango.inicio,
            FechFin: rango.fin,
            getNov: 1,
            getHor: 1,
            start: 0,
            length: 1000
        };

        axios.post(ENDPOINT_EXPORT, payload)
            .then(function (response) {
                const data = response.data || {};
                if (data.status !== 'ok' || !data.archivo) {
                    notificar(data.message || 'No se pudo exportar el archivo.', 'danger');
                    return;
                }

                mostrarNotificacionDescarga(data.archivo, data.message || 'Archivo exportado correctamente.');
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

    function normalizarCamposPersistidos(data) {
        const listado = $.isArray(data) ? data : [];

        const normalizados = $.map(listado, function (item) {
            if (!item) {
                return null;
            }

            const posicion = parseInt(item.posicion, 10);
            const tamano = parseInt(item.tamano, 10);

            if (!esEnteroPositivo(posicion) || !esEnteroPositivo(tamano) || !item.tipo || !item.formato) {
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

    function inicializarSelects() {
        $tipo.select2({
            placeholder: 'Seleccionar tipo',
            allowClear: true,
            width: '100%'
        });

        renderizarOpcionesFormato('');

        inicializarSubtipoSelect([]);
        ocultarSubtipo();
    }

    function enlazarEventos() {
        $tipo.on('change', function () {
            manejarCambioTipo();
        });

        $formato.on('change', function () {
            aplicarReglasFormato();
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

        $(document).on('click', '.download-liquidar-custom', function () {
            $.notifyClose();
        });

        $(document).on('click', '.mostrar-liquidar-custom', function () {
            const archivo = String($(this).data('archivo') || '');
            if (!archivo) {
                notificar('No se encontro archivo para mostrar.', 'danger');
                return;
            }

            mostrarResultadosArchivo(archivo);
        });
    }

    function inicializarSubtipoSelect(datos) {
        if ($subtipo.hasClass('select2-hidden-accessible')) {
            $subtipo.select2('destroy');
        }

        $subtipo.empty();
        $subtipo.append('<option value=""></option>');

        $.each(datos, function (_, item) {
            $subtipo.append(
                $('<option></option>').val(item.value).text(item.text)
            );
        });

        $subtipo.select2({
            placeholder: 'Seleccionar valor',
            allowClear: true,
            width: '100%'
        });
    }

    function mostrarSubtipo() {
        $subtipoWrapper.removeClass('d-none');
    }

    function ocultarSubtipo() {
        if ($subtipo.hasClass('select2-hidden-accessible')) {
            $subtipo.select2('destroy');
        }

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

        if (!esTipoConSubtipo(tipo)) {
            ocultarSubtipo();
            return;
        }

        setTimeout(function () {
            mostrarSubtipo();
        }, 500);
        cargarSubtipo(tipo);
    }

    function cargarSubtipo(tipo) {
        setSubtipoCargando(true);

        if (cacheCatalogos[tipo]) {
            inicializarSubtipoSelect(cacheCatalogos[tipo]);
            setSubtipoCargando(false);
            return;
        }

        const endpoint = tipo === TIPOS.NOVEDADES
            ? '../../app-data/novedades'
            : '../../app-data/horas';

        axios.get(endpoint)
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
            return $.map(listado, function (item) {
                const codigo = item.NovCodi || '';
                const descripcion = item.NovDesc || '';
                return {
                    value: codigo,
                    text: codigo + ' - ' + descripcion
                };
            });
        }

        return $.map(listado, function (item) {
            const codigo = item.THoCodi || '';
            const descripcion = item.THoDesc || '';
            return {
                value: codigo,
                text: codigo + ' - ' + descripcion
            };
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

        insertarOActualizarPorPosicion(datos);
        renderizarLista();
        prepararProximoCampo();
        guardarCampos().catch(function () {
            notificar('No se pudo guardar la configuracion.', 'danger');
        });

        notificar('Campo agregado correctamente.', 'success');
    }

    function leerFormulario() {
        const tipo = $tipo.val();

        return {
            uid: idSecuencial++,
            posicion: parseInt($posicion.val(), 10),
            tipo: tipo,
            tipoLabel: labelsTipo[tipo] || '',
            subtipo: $subtipo.val(),
            subtipoLabel: obtenerTextoSeleccionado($subtipo),
            tamano: parseInt($tamano.val(), 10),
            formato: $formato.val(),
            formatoLabel: labelsFormato[$formato.val()] || ''
        };
    }

    function validarFormulario(datos) {
        let cantidadErrores = 0;

        if (!esEnteroPositivo(datos.posicion)) {
            cantidadErrores += 1;
        }

        if (!datos.tipo) {
            cantidadErrores += 1;
        }

        if (esTipoConSubtipo(datos.tipo) && !datos.subtipo) {
            cantidadErrores += 1;
        }

        if (!esEnteroPositivo(datos.tamano)) {
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

    function renderizarLista() {
        ordenarCampos();

        if (!window.camposLiquidar.length) {
            $tablaBody.html(
                '<tr id="tabla-campos-vacio">' +
                '<td colspan="6" class="text-center text-muted">Todavia no hay campos agregados.</td>' +
                '</tr>'
            );
            return;
        }

        const filas = $.map(window.camposLiquidar, function (campo) {
            return '' +
                '<tr data-uid="' + escapeHtml(String(campo.uid)) + '">' +
                '<td>' + escapeHtml(String(campo.posicion)) + '</td>' +
                '<td>' + escapeHtml(campo.tipoLabel) + '</td>' +
                '<td>' + escapeHtml(campo.subtipoLabel || '-') + '</td>' +
                '<td>' + escapeHtml(String(campo.tamano)) + '</td>' +
                '<td>' + escapeHtml(campo.formatoLabel) + '</td>' +
                '<td class="text-right">' +
                '<button aria-label="Arrastrar para ordenar" type="button" class="hint--left btn btn-sm btn-outline-secondary border-0 btn-ordenar-campo" title="Arrastrar para ordenar"><i class="bi bi-list"></i></button>' +
                '<button aria-label="Eliminar campo" type="button" class="hint--left btn btn-sm btn-outline-danger border-0 btn-eliminar-campo" data-uid="' + escapeHtml(String(campo.uid)) + '"><i class="bi bi-trash"></i></button>' +
                '</td>' +
                '</tr>';
        });

        $tablaBody.html(filas.join(''));
    }

    function eliminarCampo(uid) {
        window.camposLiquidar = $.grep(window.camposLiquidar, function (campo) {
            return String(campo.uid) !== String(uid);
        });

        compactarPosiciones();
        renderizarLista();
        guardarCampos().catch(function () {
            notificar('No se pudo guardar la configuracion.', 'danger');
        });

        mostrarFeedback('Campo eliminado.', 'success');
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

    function obtenerSiguientePosicion() {
        return window.camposLiquidar.length + 1;
    }

    function actualizarPosicionSugerida() {
        if (!$posicion.val()) {
            $posicion.val(String(obtenerSiguientePosicion()));
        }
    }

    function esFormatoFecha(formato) {
        return formato === FORMATOS.FECHA_YMD
            || formato === FORMATOS.FECHA_MDY
            || formato === FORMATOS.FECHA_DMY;
    }

    function esTipoConSubtipo(tipo) {
        return tipo === TIPOS.NOVEDADES || tipo === TIPOS.HORAS;
    }

    function obtenerOpcionesFormatoPorTipo(tipo) {
        switch (tipo) {
            case TIPOS.FECHA:
                return [FORMATOS.FECHA_YMD, FORMATOS.FECHA_MDY, FORMATOS.FECHA_DMY];
            case TIPOS.NOVEDADES:
            case TIPOS.HORAS:
                return [FORMATOS.DECIMAL];
            case TIPOS.LEGAJO:
                return [FORMATOS.NUMERO];
            default:
                return [FORMATOS.NUMERO, FORMATOS.DECIMAL];
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
                $tamano.prop('disabled', true).val('10');
                break;
            case TIPOS.LEGAJO:
                $formato.val(FORMATOS.NUMERO).trigger('change');
                $formato.prop('disabled', true);
                $tamano.prop('disabled', false).val('11');
                break;
            case TIPOS.NOVEDADES:
            case TIPOS.HORAS:
                $formato.prop('disabled', false);
                $formato.val(FORMATOS.DECIMAL).trigger('change');
                $tamano.prop('disabled', false).val('8');
                break;
            default:
                $formato.prop('disabled', false);
                $tamano.prop('disabled', false);
        }
    }

    function aplicarReglasFormato() {
        if ($tipo.val() === TIPOS.FECHA) {
            if (!esFormatoFecha($formato.val())) {
                $formato.val(FORMATOS.FECHA_YMD).trigger('change.select2');
            }
            $tamano.val('10');
            $tamano.prop('disabled', true);
            return;
        }

        if ($tipo.val() === TIPOS.LEGAJO) {
            $tamano.prop('disabled', false);
            return;
        }

        $tamano.prop('disabled', false);
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

    function notificar(mensaje, tipo) {
        if (typeof $.notify !== 'function') {
            return;
        }
        $.notifyClose();
        notify(mensaje, tipo, 3000, 'right');
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
                'Ultimos 7 dias': [moment().subtract(6, 'days'), moment()],
                'Este mes': [moment().startOf('month'), moment().endOf('month')],
                'Mes anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Ultimos 30 dias': [moment().subtract(29, 'days'), moment()]
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
