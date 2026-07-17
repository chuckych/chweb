/** Select */
$(document).ready(function () {

    const homehost = $("#_homehost").val();
    const LS_LEGAJOS_MARCADOS = LS_PREFIX + 'proc_legajos_marcados';
    const LS_LEGAJOS_DESMARCADOS = LS_PREFIX + 'proc_legajos_desmarcados';
    const LS_LEGAJOS_MARCADOS_ALL = LS_PREFIX + 'proc_legajos_marcados_all';
    const LS_LEGAJOS_TOTAL = LS_PREFIX + 'proc_legajos_total';
    const LS_WAITING_DT = LS_PREFIX + 'proc_waiting_dt';
    const LS_TIPO_INGRESO = LS_PREFIX + 'proc_procesar_legajos';
    const FLAG_LEGAJOS_DATA = new Date().getTime();
    const $TipoIngreso = $('#TipoIngreso');
    const $PROCESAR_POR = $('input[name="procesar_por"]');
    const $MAPPING_LABELS = $('#mappingLabels');

    ls.set(LS_TIPO_INGRESO, $PROCESAR_POR.filter(':checked').val() === '2');
    $('#Personal-select-all').addClass('check');

    /**
     * Maneja el evento de selección en un elemento select2 y actualiza el valor de un input asociado con el texto del elemento seleccionado. También realiza acciones adicionales como actualizar un contador y recargar una tabla de datos.
     *
     * @param {string} slectjs - Selector jQuery del elemento select2.
     * @param {string} idselec - Selector jQuery del input que se actualizará con el texto seleccionado.
     */
    function textoSelected(slectjs) {
        $(slectjs).on('select2:select', function (e) {
            checkSession();
            ls.set(LS_WAITING_DT, true);
            const selected = slectjs + ' ' + ':selected';
            const texto = $(selected).text();
            limpiarMarcados();
            reloadDataTable($('#table'), true);
            legajos_data();
        });
    }

    /**
     * Maneja el evento de deselección en un elemento select2 y realiza acciones como decrementar un contador, limpiar marcados, actualizar el texto del contador y recargar una tabla de datos.
     *
     * @param {string} slectjs - Selector jQuery del elemento select2.
     */
    function UnSelected(slectjs) {
        $(slectjs).on('select2:unselecting', function (e) {
            checkSession();
            ls.set(LS_WAITING_DT, true);
            limpiarMarcados();
            reloadDataTable($('#table'), true);
        });
    }

    /**
     * Limpia los legajos marcados y desmarcados del localStorage y desmarca el checkbox "Select All".
     */
    const limpiarMarcados = () => {
        ls.remove(LS_LEGAJOS_MARCADOS);
        ls.remove(LS_LEGAJOS_DESMARCADOS);
        ls.remove(LS_LEGAJOS_MARCADOS_ALL);
        ls.remove(LS_LEGAJOS_TOTAL);
        $('#Personal-select-all').prop("checked", false);
        // si datatable está inicializado, desmarcar todos los checkboxes de la tabla
        if ($.fn.DataTable.isDataTable('#table')) {
            const table = $('#table').DataTable();
            const rows = table.rows({ 'search': 'applied' }).nodes();
            $('.check', rows).prop('checked', false);
        }
        actualizarEstadoSelectAll();
    }

    /**
     * Actualiza el texto del contador de legajos marcados en el DOM.
     * Si falta alguno de los valores, limpia el texto (evita mostrar "undefined de undefined").
     *
     * @param {number} [marcados] - Cantidad de legajos marcados.
     * @param {number} [totalRegistros] - Cantidad total de registros.
     * @returns {void}
     */
    const textCountMarcados = (marcados, totalRegistros) => {
        if (marcados == null || totalRegistros == null) {
            $('#countMarcados').text('');
            return;
        }
        $('#countMarcados').text(`${marcados} de ${totalRegistros}`);
    }

    /**
     * Calcula y devuelve el total de registros de la tabla table, almacenándolo en localStorage bajo la clave LS_LEGAJOS_TOTAL. Esto permite mantener un registro del total de legajos disponibles para su selección.
     */
    const TOTALREGISTROS = () => {
        const t = $('#table').DataTable().page.info().recordsTotal;
        ls.set(LS_LEGAJOS_TOTAL, t);
        return t;
    }

    limpiarMarcados();
    ls.remove(LS_WAITING_DT);

    /**
     * Calcula la cantidad de legajos marcados a nivel global, considerando
     * el modo "seleccionar todos" y sus exclusiones.
     *
     * @returns {{marcados: number, total: number}}
     */
    function calcularMarcadosGlobal() {
        const marcadosAll = ls.get(LS_LEGAJOS_MARCADOS_ALL);
        const legajosMarcados = new Set(ls.get(LS_LEGAJOS_MARCADOS) || []);
        const legajosDesMarcados = new Set(ls.get(LS_LEGAJOS_DESMARCADOS) || []);
        const total = ls.get(LS_LEGAJOS_TOTAL) || 0;

        const marcados = marcadosAll
            ? total - legajosDesMarcados.size
            : legajosMarcados.size;
        return { marcados, total };
    }

    /**
     * Actualiza el estado visual (checked / indeterminate) del checkbox "Select All"
     * según la cantidad de legajos marcados a nivel global (no solo la página visible).
     *
     * @returns {void}
     */
    function actualizarEstadoSelectAll() {
        const el = $('#Personal-select-all').get(0);
        if (!el) return;

        const { marcados, total } = calcularMarcadosGlobal();

        if (total === 0 || marcados === 0) {
            el.checked = false;
            el.indeterminate = false;
        } else if (marcados >= total) {
            el.checked = true;
            el.indeterminate = false;
        } else {
            el.checked = false;
            el.indeterminate = true;
        }
    }

    /**
     * Actualiza el estado de marcado de un legajo en localStorage y refresca el contador visual.
     *
     * Si `marcadosAll` está activo (modo "seleccionar todos"), solo se trackean las
     * exclusiones (legajosDesMarcados); el resto se asume marcado implícitamente.
     * También se actualiza el estado del checkbox "Select All" según corresponda.
     *
     * @param {string|number} legajo - Identificador del legajo a marcar/desmarcar.
     * @param {boolean} checked - true si se marca, false si se desmarca.
     * @returns {void}
     */
    function almacenarLegajosMarcados(legajo, checked) {
        const marcadosAll = ls.get(LS_LEGAJOS_MARCADOS_ALL);
        const legajosMarcados = new Set(ls.get(LS_LEGAJOS_MARCADOS) || []);
        const legajosDesMarcados = new Set(ls.get(LS_LEGAJOS_DESMARCADOS) || []);

        if (checked) {
            if (!marcadosAll) legajosMarcados.add(legajo);
            legajosDesMarcados.delete(legajo);
        } else {
            legajosMarcados.delete(legajo);
            if (marcadosAll) legajosDesMarcados.add(legajo);
        }

        ls.set(LS_LEGAJOS_MARCADOS, [...legajosMarcados]);
        ls.set(LS_LEGAJOS_DESMARCADOS, [...legajosDesMarcados]);

        const { marcados, total } = calcularMarcadosGlobal();
        textCountMarcados(marcados, total);
        actualizarEstadoSelectAll();
    }

    function onSelectAllChange() {
        $('#Personal-select-all').on('click', function () {
            checkSession();
            // Check/uncheck all checkboxes in the table
            const rows = table.rows({ 'search': 'applied' }).nodes();
            $('.check', rows).prop('checked', this.checked);

            ls.set(LS_LEGAJOS_MARCADOS_ALL, this.checked);
            ls.remove(LS_LEGAJOS_MARCADOS);
            ls.remove(LS_LEGAJOS_DESMARCADOS);

            // obtener el total de registros general de la tabla del paginador y mostrarlo en el label del checkbox
            const totalRegistros = ls.get(LS_LEGAJOS_TOTAL) || 0;
            const totalMarcados = (ls.get(LS_LEGAJOS_MARCADOS_ALL)) ? totalRegistros : 0;

            textCountMarcados(totalMarcados, totalRegistros);
        });
    }

    /**
     * Inicializa la tabla table con configuración de DataTables, incluyendo opciones de paginación, búsqueda, y AJAX para obtener datos del servidor. También define eventos para manejar la selección de legajos y actualizar el estado de los checkboxes.
     */
    const table = $('#table').DataTable({
        lengthMenu: [[10, 50, 100, 200, 300, 400], [10, 50, 100, 200, 300, 400]],
        bProcessing: true,
        lengthChange: true,
        serverSide: true,
        deferRender: true,
        searchDelay: 350,
        ajax: {
            url: "/" + homehost + "/app-data/custom/arrpersonal",
            type: "GET",
            "data": function (data) {
                data.Tipo = $("#aTipo").val();
                data.Emp = $("#aEmp").val();
                data.Plan = $("#aPlan").val();
                data.Sect = $("#aSect").val();
                data.Sec2 = $("#aSec2").val();
                data.Grup = $("#aGrup").val();
                data.Sucur = $("#aSucur").val();
                data._c = $("#_c").val();
                data._r = $("#_r").val();
                data.Modulo = "Cierres",
                    data.NoPag = false
            },
            error: function () {
                $("#table_processing").css("display", "none");
            },
        },
        columns: [
            {
                "class": "align-middle fadeIn w10",
                "data": 'check'
            },
            {
                "class": "align-middle fadeIn",
                "data": 'pers_legajo2'
            },
            {
                "class": "align-middle fadeIn",
                "data": 'pers_nombre2'
            },
            {
                "class": "align-middle fadeIn",
                "data": 'FechaCierre'
            },

        ],
        scrollY: '550px',
        scrollX: true,
        scrollCollapse: true,
        paging: true,
        responsive: false,
        searching: true,
        info: true,
        ordering: false,
        language: DT_SPANISH_LEGAJOS
    });

    singleDatePicker('#cierre', 'right', 'up');

    /**
     * Inicializa el estado de la tabla table y actualiza el contador de legajos marcados al cargar los datos por primera vez.
     * También elimina el loader y establece el placeholder del input de búsqueda.
     */
    table.on('init.dt', function (settings) {
        $('#table_filter .form-control-sm').attr('placeholder', 'Buscar Legajo');

        const totalRegistros = table.page.info().recordsTotal;
        ls.set(LS_LEGAJOS_TOTAL, totalRegistros);
        textCountMarcados(0, totalRegistros);

        // al hacer clic en el checkbox "Select All", se ejecuta la función onSelectAllChange para manejar la selección de todos los legajos en la tabla table. 
        onSelectAllChange();
    });

    /**
     * Actualiza el estado de los checkboxes y el contador de legajos marcados cada vez que se redibuja la tabla table.
     */
    table.on('draw.dt', function (settings) {
        /**
         * Si existe la clave LS_WAITING_DT en localStorage, se actualiza el contador de legajos marcados a 0 y se elimina la clave. Esto asegura que al cambiar de página o filtrar la tabla, el contador se reinicie correctamente.
         */
        if (ls.get(LS_WAITING_DT)) {
            textCountMarcados(0, TOTALREGISTROS());
            ls.remove(LS_WAITING_DT);
        }

        textCountMarcados(calcularMarcadosGlobal().marcados, TOTALREGISTROS());

        /**
         * Marca los checkboxes de los legajos que están almacenados en localStorage (LS_LEGAJOS_MARCADOS) al redibujar la tabla table. Esto asegura que los legajos previamente seleccionados permanezcan marcados incluso después de cambiar de página o filtrar la tabla.
         */
        const legajosMarcados = new Set(ls.get(LS_LEGAJOS_MARCADOS) || []);
        legajosMarcados.forEach(legajo => {
            $(`#${legajo}`).prop('checked', true);
        });

        /**
         * Si la opción "Select All" está activa (LS_LEGAJOS_MARCADOS_ALL), marca todos los checkboxes de la tabla table y luego desmarca aquellos legajos que están en la lista de desmarcados (LS_LEGAJOS_DESMARCADOS). Esto permite mantener la selección global de legajos mientras se respetan las exclusiones específicas.
         */
        if (ls.get(LS_LEGAJOS_MARCADOS_ALL)) {
            const legajosDesMarcados = new Set(ls.get(LS_LEGAJOS_DESMARCADOS) || []);
            const rows = table.rows({ 'search': 'applied' }).nodes();
            $('.check', rows).prop('checked', true);
            legajosDesMarcados.forEach(legajo => {
                $(`#${legajo}`).prop('checked', false);
            });
        }
        actualizarEstadoSelectAll();

    });

    table.on('page.dt', function (e, settings) {
        checkSession();
    });

    /**
     * Maneja el evento de cambio en los checkboxes individuales de la tabla table. Actualiza el estado de marcado de los legajos en localStorage y ajusta el estado del checkbox "Select All" según corresponda.
     */
    $('#table tbody').on('change', '.check', function () {
        almacenarLegajosMarcados(this.id, this.checked);
    });

    const opt2 = { MinLength: "0", SelClose: false, MaxInpLength: "10", delay: "250", allowClear: true };

    select2Simple('.select2Tipo', 'Seleccione una opción', 0, -1, "100%");

    const SELECTORES_SELECT2 = ['.sel_empresa', '.sel_plantas', '.sel_sectores', '.sel_seccion', '.sel_grupos', '.sel_sucursal'];

    const LANG_SELECT2 = {
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
    }

    const MAPPING_LABELS = JSON.parse($MAPPING_LABELS.val() || '{}');
    $MAPPING_LABELS.val('');

    const initSelect2Filtros = (selector) => {

        const OPT = {
            '.sel_empresa': {
                'placeholder': MAPPING_LABELS.Empresas || 'Empresas',
                'ajax': 'peremp'
            },
            '.sel_plantas': {
                'placeholder': MAPPING_LABELS.Plantas || 'Plantas',
                'ajax': 'perplan'
            },
            '.sel_sectores': {
                'placeholder': MAPPING_LABELS.Sectores || 'Sectores',
                'ajax': 'persect'
            },
            '.sel_seccion': {
                'placeholder': MAPPING_LABELS.Secciones || 'Secciones',
                'ajax': 'persecc'
            },
            '.sel_grupos': {
                'placeholder': MAPPING_LABELS.Grupos || 'Grupos',
                'ajax': 'pergrup'
            },
            '.sel_sucursal': {
                'placeholder': MAPPING_LABELS.Sucursales || 'Sucursal',
                'ajax': 'persucu'
            }
        };

        const buildDataAjax = (selector, term = '') => {
            const COMMON = {
                q: term,
                Tipo: $("#aTipo").val(),
                Emp: $(".sel_empresa").val(),
                Plan: $(".sel_plantas").val(),
                Sect: $(".sel_sectores").val(),
                Sec2: $(".sel_seccion").val(),
                Grup: $(".sel_grupos").val(),
                Sucur: $(".sel_sucursal").val(),
            };

            const EXCLUDE_KEYS = {
                '.sel_empresa': ['Emp'],
                '.sel_plantas': ['Plan'],
                '.sel_sectores': ['Sect'],
                '.sel_seccion': ['Sec2'],
                '.sel_grupos': ['Grup'],
                '.sel_sucursal': ['Sucur']
            };

            const payload = { ...COMMON };
            (EXCLUDE_KEYS[selector] || []).forEach((key) => delete payload[key]);

            return payload;
        }

        $(selector).select2({
            multiple: false,
            language: "es",
            allowClear: true,
            placeholder: OPT[selector]['placeholder'] ?? 'Seleccione una opción',
            minimumInputLength: '0',
            minimumResultsForSearch: '5',
            maximumInputLength: '10',
            selectOnClose: false,
            width: '100%',
            language: LANG_SELECT2,
            ajax: {
                url: "/" + homehost + "/app-data/custom/" + OPT[selector]['ajax'],
                dataType: "json",
                type: 'GET',
                delay: '250',
                data: function (params) {
                    return buildDataAjax(selector, params.term || '');
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        });

    }

    SELECTORES_SELECT2.forEach(selector => initSelect2Filtros(selector));

    SELECTORES_SELECT2.forEach(selector => {
        CloseDropdownOnClearSelect2(selector);
    });

    $(".sel_seccion").prop("disabled", true);

    $('.sel_sectores').on('select2:select', function (e) {
        $(".sel_seccion").prop("disabled", false);
        $("#select_seccion").removeClass("d-none");
        $('.sel_seccion').val(null).trigger('change');
    });

    $('.sel_sectores').on('select2:unselecting', function (e) {
        $('.sel_seccion').val(null).trigger('change');
        $(".sel_seccion").prop("disabled", true);
    });

    SELECTORES_SELECT2.forEach(selector => {
        textoSelected(selector);
    });

    SELECTORES_SELECT2.forEach(selector => {
        UnSelected(selector);
    });

    $("#aTipo").change(function () {
        checkSession()
        $('.sel_sucursal').val(null).trigger("change");
        $('.sel_grupos').val(null).trigger("change");
        $('.sel_seccion').val(null).trigger("change");
        $('.sel_sectores').val(null).trigger("change");
        $('.sel_seccion').val(null).trigger("change");
        $('.sel_personal').val(null).trigger("change");
        $('.sel_plantas').val(null).trigger("change");
        $('.sel_empresa').val(null).trigger("change");
        limpiarMarcados();
        reloadDataTable($('#table'), true);
    });
    $('input[name="Eliminar"]').val('0');
    const textButtonSubmit = () => {
        const eliminar = $('input[name="Eliminar"]').val() === '1';
        return eliminar ? 'Eliminar Cierres' : 'Ingresar Cierres';
    };
    $('input[name="Eliminar"]').change(function () {
        if ($(this).is(':checked')) {
            $('input[name="Eliminar"]').val('1');
            $('input[name="Fecha"]').addClass('loader-in');
        } else {
            $('input[name="Eliminar"]').val('0');
            $('input[name="Fecha"]').removeClass('loader-in');
        }
        $("#submit").text(textButtonSubmit());
    });

    $PROCESAR_POR.change(function () {
        checkSession();
        const isLegajos = $(this).val() === '2';
        ls.set(LS_TIPO_INGRESO, isLegajos);
        toggleDivTablePers(!isLegajos);
    });

    const toggleDivTablePers = (show) => {
        $('#divTablePers').toggleClass('loader-in', show);
        $('.check').prop('disabled', show);
    }

    ActiveBTN(false, "#submit", 'Ingresando', textButtonSubmit());

    const legajos_data = async () => {
        const url = "/" + homehost + "/app-data/custom/arrpersonal";

        try {

            const payload = {
                Tipo: $("#aTipo").val(),
                Emp: $(".sel_empresa").val(),
                Plan: $(".sel_plantas").val(),
                Sect: $(".sel_sectores").val(),
                Sec2: $(".sel_seccion").val(),
                Grup: $(".sel_grupos").val(),
                Sucur: $(".sel_sucursal").val(),
                _c: $("#_c").val(),
                _r: $("#_r").val(),
                Modulo: "generar_cierres",
                NoPag: false,
                flag: FLAG_LEGAJOS_DATA,
                marcadosAll: ls.get(LS_LEGAJOS_MARCADOS_ALL),
                marcados: ls.get(LS_LEGAJOS_MARCADOS) || [],
                desmarcados: ls.get(LS_LEGAJOS_DESMARCADOS) || []
            };

            const response = await axios.get(url, { params: payload });

            if (response.data?.success !== true) {
                return false;
            }

            $.notifyClose();
            return true;

        } catch (error) {
            notify('Error en la solicitud: ' + error.message, 'danger', 5000, 'right');
        }
    }
    const generar = async (payload) => {

        const url = "/" + homehost + "/app-data/cierres/generar";

        try {
            ActiveBTN(true, "#submit", 'Ingresando', textButtonSubmit());

            /**
             * Si el valor del campo $TipoIngreso es igual a 2 (Por Legajos), se realiza una llamada a la función legajos_data() para obtener los datos de legajos. Si la llamada falla, se lanza un error indicando que no se pudieron obtener los datos y se solicita al usuario que intente nuevamente.
             */
            const $procesar_por = $('input[name="procesar_por"]').filter(':checked');

            if ($procesar_por.val() == '2') {

                const marcadosAll = ls.get(LS_LEGAJOS_MARCADOS_ALL);
                const legajosMarcados = ls.get(LS_LEGAJOS_MARCADOS) || [];
                const legajosDesMarcados = ls.get(LS_LEGAJOS_DESMARCADOS) || [];

                /**
                 * Si no hay legajos seleccionados (ni marcados ni desmarcados) y la opción "Select All" no está activa, se lanza un error indicando que no hay legajos seleccionados. Esto asegura que el usuario seleccione al menos un legajo antes de enviar la solicitud.
                 */
                if (!marcadosAll && (!legajosMarcados || legajosMarcados.length === 0)) {
                    throw new Error('No hay legajos seleccionados.');
                }

                const legajos_data_result = await legajos_data();

                if (!legajos_data_result) {
                    throw new Error('No se pudieron obtener los datos de legajos. Por favor, intente nuevamente.');
                }
            }

            const response = await axios.post(url, payload);
            $.notifyClose();


            if (response.data?.RESPONSE_CODE !== '200 OK') {
                throw new Error(response.data?.MESSAGE);
            }

            limpiarFiltros();
            ActiveBTN(false, "#submit", 'Ingresando', textButtonSubmit());
            notify('Cierres procesados correctamente', 'success', 5000, 'right');
            reloadDataTable($('#table'), true);
        } catch (error) {
            notify('Error: ' + error.message, 'danger', 5000, 'right');
            ActiveBTN(false, "#submit", 'Ingresando', textButtonSubmit());
            reloadDataTable($('#table'), true);
        }
    }

    $("#form-procesar").submit(function (e) {
        e.preventDefault();
        checkSession();

        const map_items_names = {
            'legajo[]': 'Legajos',
            'aEmp': 'Empresa',
            'aPlan': 'Planta',
            'aSect': 'Sector',
            'aSec2': 'Seccion',
            'aGrup': 'Grupo',
            'aSucur': 'Sucursal'
        };


        const marcadosAll = ls.get(LS_LEGAJOS_MARCADOS_ALL);
        const legajosMarcados = ls.get(LS_LEGAJOS_MARCADOS) || [];
        const legajosDesMarcados = ls.get(LS_LEGAJOS_DESMARCADOS) || [];

        /**
         * Construye el objeto payload para enviar al servidor, incluyendo los legajos marcados y desmarcados según el estado de "Select All". Inicializa todas las claves mapeadas con valores vacíos o arrays vacíos según corresponda.
         */
        const payload = Object.values(map_items_names).reduce((obj, mappedName) => {
            obj[mappedName] = mappedName === 'Legajos' ? [] : "";
            return obj;
        }, {});

        payload.flag = FLAG_LEGAJOS_DATA;

        // Procesar los datos del formulario
        $(this).serializeArray().forEach(item => {
            // Procesamiento especial para el rango de fechas
            if (item.name === 'Fecha') {
                const fecha = item.value
                // Convertir DD/MM/YYYY a YYYY-MM-DD
                const convertirFecha = (fecha) => {
                    const partes = fecha.trim().split('/');
                    return `${partes[2]}-${partes[1]}-${partes[0]}`;
                };
                payload.Fecha = convertirFecha(fecha);
                return;
            }

            // Procesamiento especial para legajos (array)
            if (item.name === 'legajo[]') {
                payload.Legajos.push(item.value);
                return;
            }

            // Mapeo normal de campos
            const keyName = map_items_names[item.name] || item.name;
            payload[keyName] = item.value;
            payload.Eliminar = $('input[name="Eliminar"]').val();
        });

        // Empresa, Planta, Sector, Sección, Grupo y Sucursal al menos uno es obligatorio cuando TipoIngreso es 1 (Por Filtros)
        const tieneAlMenosUno = ['Empresa', 'Planta', 'Sector', 'Seccion', 'Grupo', 'Sucursal']
            .some(campo => payload[campo] !== "");

        if (!tieneAlMenosUno && payload.procesar_por === "1") {
            notify('Debe seleccionar al menos una Entidad', 'warning', 3000, 'right');
            return;
        }

        if (payload.FechaDesde == "") {
            notify('Debe seleccionar una fecha de inicio', 'warning', 3000, 'right')
            return;
        }
        if (payload.FechaHasta == "") {
            notify('Debe seleccionar una fecha de fin', 'warning', 3000, 'right')
            return;
        }

        if (payload.procesar_por == "2" || payload.procesar_por == "1") {
            generar(payload);
            return;
        }
        e.stopImmediatePropagation();
    });

    $("#trash_allFilter").on("click", function () {
        limpiarFiltros()
        table.page.len(10).draw();
    });

    function limpiarFiltros() {
        limpiarMarcados();
        textCountMarcados();
        ls.set(LS_WAITING_DT, true);

        $('#aTipo').val(0).trigger("change");

        $(".sel_seccion").prop("disabled", true);
        ActiveBTN(false, "#submit", 'Ingresando', textButtonSubmit());
    }

});

