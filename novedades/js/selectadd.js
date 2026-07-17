/** Select */
$(document).ready(function () {

    HoraMask('.HoraMask');
    const homehost = $("#_homehost").val();
    const LS_LEGAJOS_MARCADOS = LS_PREFIX + 'legajos_marcados';
    const LS_LEGAJOS_DESMARCADOS = LS_PREFIX + 'legajos_desmarcados';
    const LS_LEGAJOS_MARCADOS_ALL = LS_PREFIX + 'legajos_marcados_all';
    const LS_LEGAJOS_TOTAL = LS_PREFIX + 'legajos_total';
    const LS_WAITING_DT = LS_PREFIX + 'waiting_dt';
    const FLAG_LEGAJOS_DATA = new Date().getTime();
    const $TipoIngreso = $('#TipoIngreso');

    /**
     * Incrementa el valor numérico de un input especificado por el selector en 1.
     * Si el valor actual no es un número válido, se considera como 0 antes de la suma.
     *
     * @param {string} selector - Selector jQuery del input cuyo valor se incrementará.
     */
    function SumaCuenta(selector) {
        const cuenta = parseFloat($(selector).val())
        const cuenta1 = 1
        const SumaCuenta = parseFloat(cuenta + cuenta1)
        $(selector).val(0)
        $(selector).val(SumaCuenta)
    }

    /**
     * Decrementa el valor numérico de un input especificado por el selector en 1.
     * Si el valor actual no es un número válido, se considera como 0 antes de la resta.
     *
     * @param {string} selector - Selector jQuery del input cuyo valor se decrementará.
     */
    function RestaCuenta(selector) {
        const cuenta = parseFloat($(selector).val())
        const cuenta1 = 1
        const RestaCuenta = parseFloat(cuenta - cuenta1)
        $(selector).val(0)
        $(selector).val(RestaCuenta)
    }

    /**
     * Maneja el evento de selección en un elemento select2 y actualiza el valor de un input asociado con el texto del elemento seleccionado. También realiza acciones adicionales como actualizar un contador y recargar una tabla de datos.
     *
     * @param {string} slectjs - Selector jQuery del elemento select2.
     * @param {string} idselec - Selector jQuery del input que se actualizará con el texto seleccionado.
     */
    function textoSelected(slectjs, idselec) {
        $(slectjs).on('select2:select', function (e) {
            const selected = slectjs + ' ' + ':selected';
            const texto = $(selected).text();
            $(idselec).val(texto).trigger('change');
            SumaCuenta('#Cuenta');
            reloadDataTable($('#GetPers'), true);
            textCountMarcados();
            limpiarMarcados();
            legajos_data();

            setTimeout(function () {
                const totalRegistros = TOTALREGISTROS();
                textCountMarcados(0, totalRegistros);
            }, 500);
        });
    }

    /**
     * Maneja el evento de deselección en un elemento select2 y realiza acciones como decrementar un contador, limpiar marcados, actualizar el texto del contador y recargar una tabla de datos.
     *
     * @param {string} slectjs - Selector jQuery del elemento select2.
     */
    function UnSelected(slectjs) {
        $(slectjs).on('select2:unselecting', function (e) {
            RestaCuenta('#Cuenta');
            limpiarMarcados();
            textCountMarcados();
            ls.set(LS_WAITING_DT, true);
            reloadDataTable($('#GetPers'), true);
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
        $("#Personal-select-all").prop("checked", false);
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
    const TOTALREGISTROS = () => {
        const t = $('#GetPers').DataTable().page.info().recordsTotal;
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


    $(document).on('click', '#addNov', function (e) {
        CheckSesion();
        $('#Personal-select-all').addClass('check')

        /**
         * Inicializa la tabla GetPers con configuración de DataTables, incluyendo opciones de paginación, búsqueda, y AJAX para obtener datos del servidor. También define eventos para manejar la selección de legajos y actualizar el estado de los checkboxes.
         */
        const GetPers = $('#GetPers').DataTable({
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
                    $("#GetPers_processing").css("display", "none");
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

        /**
         * Inicializa el estado de la tabla GetPers y actualiza el contador de legajos marcados al cargar los datos por primera vez.
         * También elimina el loader y establece el placeholder del input de búsqueda.
         */
        GetPers.on('init.dt', function (settings) {

            $('div.loader').remove();
            $('#GetPers_filter .form-control-sm').attr('placeholder', 'Buscar Legajo');

            const totalRegistros = GetPers.page.info().recordsTotal;
            ls.set(LS_LEGAJOS_TOTAL, totalRegistros);
            textCountMarcados(0, totalRegistros);

        });

        /**
         * Actualiza el estado de los checkboxes y el contador de legajos marcados cada vez que se redibuja la tabla GetPers.
         */
        GetPers.on('draw.dt', function (settings) {

            /**
             * Si existe la clave LS_WAITING_DT en localStorage, se actualiza el contador de legajos marcados a 0 y se elimina la clave. Esto asegura que al cambiar de página o filtrar la tabla, el contador se reinicie correctamente.
             */
            if (ls.get(LS_WAITING_DT)) {
                textCountMarcados(0, TOTALREGISTROS());
                ls.remove(LS_WAITING_DT);
            }

            const pagIni = (settings._iDisplayStart);
            const pagFin = (settings._iDisplayLength);
            $('#pagIni').val(pagIni);
            $('#pagFin').val(pagFin);

            $('#divTablePers').removeClass('d-none');

            /**
             * Deshabilita los checkboxes de la tabla GetPers si el valor del input #TipoIngreso es igual a 1. Esto asegura que los legajos no puedan ser seleccionados cuando se encuentra en un modo específico de ingreso.
             */
            if ($('#TipoIngreso').val() == 1) {
                $('.check').prop('disabled', true);
            }

            /**
             * Marca los checkboxes de los legajos que están almacenados en localStorage (LS_LEGAJOS_MARCADOS) al redibujar la tabla GetPers. Esto asegura que los legajos previamente seleccionados permanezcan marcados incluso después de cambiar de página o filtrar la tabla.
             */
            const legajosMarcados = new Set(ls.get(LS_LEGAJOS_MARCADOS) || []);
            legajosMarcados.forEach(legajo => {
                $(`#${legajo}`).prop('checked', true);
            });

            /**
             * Si la opción "Select All" está activa (LS_LEGAJOS_MARCADOS_ALL), marca todos los checkboxes de la tabla GetPers y luego desmarca aquellos legajos que están en la lista de desmarcados (LS_LEGAJOS_DESMARCADOS). Esto permite mantener la selección global de legajos mientras se respetan las exclusiones específicas.
             */
            if (ls.get(LS_LEGAJOS_MARCADOS_ALL)) {
                const legajosDesMarcados = new Set(ls.get(LS_LEGAJOS_DESMARCADOS) || []);
                const rows = GetPers.rows({ 'search': 'applied' }).nodes();
                $('.check', rows).prop('checked', true);
                legajosDesMarcados.forEach(legajo => {
                    $(`#${legajo}`).prop('checked', false);
                });
            }

            actualizarEstadoSelectAll();

        });

        /**
         * Maneja el evento de clic en el checkbox "Select All". Marca o desmarca todos los checkboxes individuales de la tabla GetPers y actualiza el estado de marcado en localStorage. También actualiza el contador visual de legajos marcados.
         */
        $('#Personal-select-all').on('click', function () {
            CheckSesion();
            // Check/uncheck all checkboxes in the GetPers
            const rows = GetPers.rows({ 'search': 'applied' }).nodes();
            $('.check', rows).prop('checked', this.checked);

            ls.set(LS_LEGAJOS_MARCADOS_ALL, this.checked);
            ls.remove(LS_LEGAJOS_MARCADOS);
            ls.remove(LS_LEGAJOS_DESMARCADOS);

            // obtener el total de registros general de la tabla del paginador y mostrarlo en el label del checkbox
            const totalRegistros = ls.get(LS_LEGAJOS_TOTAL) || 0;
            const totalMarcados = (ls.get(LS_LEGAJOS_MARCADOS_ALL)) ? totalRegistros : 0;

            textCountMarcados(totalMarcados, totalRegistros);
        });

        /**
         * Maneja el evento de cambio en los checkboxes individuales de la tabla GetPers. Actualiza el estado de marcado de los legajos en localStorage y ajusta el estado del checkbox "Select All" según corresponda.
         */
        $('#GetPers tbody').on('change', '.check', function () {
            almacenarLegajosMarcados(this.id, this.checked);
        });

        const AnioMin = parseFloat($('#AnioMin').val());
        const AnioMax = parseFloat($('#AnioMax').val());

        $('#_draddNov').daterangepicker({
            singleDatePicker: false,
            showDropdowns: true,
            minYear: AnioMin,
            maxYear: AnioMax,
            showWeekNumbers: false,
            autoUpdateInput: true,
            opens: "center",
            drops: "down",
            autoApply: false,
            alwaysShowCalendars: true,
            linkedCalendars: false,
            buttonClasses: "btn btn-sm fontq",
            applyButtonClasses: "btn-custom fw4 px-3 opa8",
            cancelClass: "btn-link fw4 text-gris",
            ranges: {
                'Hoy': [moment(), moment()],
                // 'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Esta semana': [moment().day(1), moment().day(7)],
                'Ultima Semana': [moment().subtract(1, 'week').day(1), moment().subtract(1, 'week').day(7)],
                'Próxima Semana': [moment().add(1, 'week').day(1), moment().add(1, 'week').day(7)],
                'Este mes': [moment().startOf('month'), moment().endOf('month')],
                'Próximo Mes': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
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

        $('#_draddNov').on('apply.daterangepicker', function (ev, picker) {
            $("#range").submit();
        });

        $('#Encabezado').html('Ingresar Novedades');

        $('#divTablas').addClass('d-none');
        $('#divaddNov').removeClass('d-none');
        fadeInOnly('#divaddNov');

        const opt2 = { MinLength: "0", SelClose: false, MaxInpLength: "10", delay: "250", allowClear: true };

        SelectSelect2('.select2Tipo', true, "Tipo de Personal", 0, -1, 10, false)

        $(".sel_empresa").select2({
            multiple: false,
            language: "es",
            allowClear: true,
            placeholder: 'Empresa',
            minimumInputLength: '0',
            minimumResultsForSearch: '5',
            maximumInputLength: '10',
            selectOnClose: true,
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
                },
                removeAllItems: function () {
                    return "Eliminar Selección"
                }
            },
            ajax: {
                url: "/" + homehost + "/app-data/custom/peremp",
                dataType: "json",
                type: 'GET',
                delay: '250',
                data: function (params) {
                    return {
                        q: params.term,
                        Tipo: $("#aTipo").val(),
                        Plan: $("#aPlan").val(),
                        Sect: $("#aSect").val(),
                        Sec2: $("#aSec2").val(),
                        Grup: $("#aGrup").val(),
                        Sucur: $("#aSucur").val(),
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        })

        $(".sel_plantas").select2({
            multiple: false,
            language: "es",
            allowClear: true,
            placeholder: 'Planta',
            minimumInputLength: '0',
            minimumResultsForSearch: '5',
            maximumInputLength: '10',
            selectOnClose: true,
            language: {
                noResults: function () {
                    return 'No hay resultados..'
                },
                inputTooLong: function (args) {
                    let message = 'Máximo ' + opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
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
                },
                removeAllItems: function () {
                    return "Eliminar Selección"
                }
            },
            ajax: {
                url: "/" + homehost + "/app-data/custom/perplan",
                dataType: "json",
                type: 'GET',
                delay: '250',
                data: function (params) {
                    return {
                        q: params.term,
                        Tipo: $("#aTipo").val(),
                        Emp: $("#aEmp").val(),
                        Sect: $("#aSect").val(),
                        Sec2: $("#aSec2").val(),
                        Grup: $("#aGrup").val(),
                        Sucur: $("#aSucur").val(),
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        })

        $(".sel_sectores").select2({
            multiple: false,
            language: "es",
            allowClear: true,
            placeholder: 'Sector',
            minimumInputLength: '0',
            minimumResultsForSearch: '5',
            maximumInputLength: '10',
            selectOnClose: true,
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
                },
                removeAllItems: function () {
                    return "Eliminar Selección"
                }
            },
            ajax: {
                url: "/" + homehost + "/app-data/custom/persect",
                dataType: "json",
                type: 'GET',
                delay: '250',
                data: function (params) {
                    return {
                        q: params.term,
                        Tipo: $("#aTipo").val(),
                        Emp: $("#aEmp").val(),
                        Plan: $("#aPlan").val(),
                        Sec2: $("#aSec2").val(),
                        Grup: $("#aGrup").val(),
                        Sucur: $("#aSucur").val(),
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        })

        $(".sel_seccion").select2({
            multiple: false,
            language: "es",
            allowClear: true,
            placeholder: 'Sección',
            minimumInputLength: '0',
            minimumResultsForSearch: '5',
            maximumInputLength: '10',
            selectOnClose: true,
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
                },
                removeAllItems: function () {
                    return "Eliminar Selección"
                }
            },
            ajax: {
                url: "/" + homehost + "/app-data/custom/persecc",
                dataType: "json",
                type: 'GET',
                delay: '250',
                data: function (params) {
                    return {
                        q: params.term,
                        Sect: $("#aSect").val(),
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        })

        $(".sel_grupos").select2({
            multiple: false,
            language: "es",
            allowClear: true,
            placeholder: 'Grupo',
            minimumInputLength: '0',
            minimumResultsForSearch: '5',
            maximumInputLength: '10',
            selectOnClose: true,
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
                },
                removeAllItems: function () {
                    return "Eliminar Selección"
                }
            },
            ajax: {
                url: "/" + homehost + "/app-data/custom/pergrup",
                dataType: "json",
                type: 'GET',
                delay: '250',
                data: function (params) {
                    return {
                        q: params.term,
                        Tipo: $("#aTipo").val(),
                        Emp: $("#aEmp").val(),
                        Plan: $("#aPlan").val(),
                        Sect: $("#aSect").val(),
                        Sec2: $("#aSec2").val(),
                        Sucur: $("#aSucur").val(),
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        })

        $(".sel_sucursal").select2({
            multiple: false,
            language: "es",
            allowClear: true,
            placeholder: 'Sucursal',
            minimumInputLength: '0',
            minimumResultsForSearch: '5',
            maximumInputLength: '10',
            selectOnClose: true,
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
                },
                removeAllItems: function () {
                    return "Eliminar Selección"
                }
            },
            ajax: {
                url: "/" + homehost + "/app-data/custom/persucu",
                dataType: "json",
                type: 'GET',
                delay: '250',
                data: function (params) {
                    return {
                        q: params.term,
                        Tipo: $("#aTipo").val(),
                        Emp: $("#aEmp").val(),
                        Plan: $("#aPlan").val(),
                        Sect: $("#aSect").val(),
                        Sec2: $("#aSec2").val(),
                        Grup: $("#aGrup").val(),
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        })

        $(".sel_causa").select2({
            multiple: false,
            language: "es",
            allowClear: true,
            placeholder: 'Causa',
            minimumInputLength: '0',
            minimumResultsForSearch: '5',
            maximumInputLength: '10',
            selectOnClose: true,
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
                },
                removeAllItems: function () {
                    return "Eliminar Selección"
                }
            },
            ajax: {
                url: "/" + homehost + "/data/getListNoveCausa.php",
                dataType: "json",
                type: 'POST',
                delay: '250',
                data: function (params) {
                    return {
                        q: params.term,
                        NovCNove: $("#aFicNove").val(),
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        })

        SelectSelect2Ajax(".sel_novedad", false, true, 'Novedad', 0, 5, 10, true, "data/getNovNovedades.php", '250', '', 'POST')

        CloseDropdownOnClearSelect2('.sel_causa')
        CloseDropdownOnClearSelect2('.sel_empresa')
        CloseDropdownOnClearSelect2('.sel_plantas')
        CloseDropdownOnClearSelect2('.sel_sectores')
        CloseDropdownOnClearSelect2('.sel_seccion')
        CloseDropdownOnClearSelect2('.sel_grupos')
        CloseDropdownOnClearSelect2('.sel_sucursal')

        $(".sel_seccion").prop("disabled", true);

        $(".sel_causa").prop("disabled", true);

        $('.sel_sectores').on('select2:select', function (e) {
            $(".sel_seccion").prop("disabled", false);
            $("#select_seccion").removeClass("d-none");
            $('.sel_seccion').val(null).trigger('change');
        });

        $('.sel_sectores').on('select2:unselecting', function (e) {
            $('.sel_seccion').val(null).trigger('change');
            $(".sel_seccion").prop("disabled", true);
        });

        $('.sel_novedad').on('select2:select', function (e) {
            $(".sel_causa").prop("disabled", false);
            $("#select_causa").removeClass("d-none");
            fadeInOnly("#select_causa")
            $('.sel_causa').val(null).trigger('change');
            $('.FicHoras').focus()
            $('#SelNovedad').val($('.sel_novedad' + ' ' + ':selected').text()).trigger('change');
        });

        $('.sel_novedad').on('select2:unselecting', function (e) {
            $('.sel_causa').val(null).trigger('change');
            $(".sel_causa").prop("disabled", true);
            $("#select_causa").addClass("d-none");
        });

        $('#Cuenta').val(0);

        textoSelected('.sel_empresa', '#SelEmpresa');
        textoSelected('.sel_plantas', '#SelPlanta');
        textoSelected('.sel_sectores', '#SelSector');
        textoSelected('.sel_seccion', '#SelSeccion');
        textoSelected('.sel_grupos', '#SelGrupo');
        textoSelected('.sel_sucursal', '#SelSucursal');

        UnSelected('.sel_empresa');
        UnSelected('.sel_plantas');
        UnSelected('.sel_sectores');
        UnSelected('.sel_seccion');
        UnSelected('.sel_grupos');
        UnSelected('.sel_sucursal');

        $("#aTipo").change(function () {
            CheckSesion()
            $('.sel_sucursal').val(null).trigger("change");
            $('#SelSucursal').val(null).trigger("change");
            $('.sel_grupos').val(null).trigger("change");
            $('#SelGrupo').val(null).trigger("change");
            $('.sel_seccion').val(null).trigger("change");
            $('#SelSeccion').val(null).trigger("change");
            $('.sel_sectores').val(null).trigger("change");
            $('.sel_seccion').val(null).trigger("change");
            $('#SelSector').val(null).trigger("change");
            $('#SelSeccion').val(null).trigger("change");
            $('.sel_personal').val(null).trigger("change");
            $('.sel_plantas').val(null).trigger("change");
            $('#SelPlanta').val(null).trigger("change");
            $('.sel_empresa').val(null).trigger("change");
            $('#SelEmpresa').val(null).trigger("change");
            reloadDataTable($('#GetPers'), true);
            $('#Cuenta').val(0)
        });
    });

    $('.check').prop('disabled', true)
    $('#TipoIngreso').val(2);
    $("#TipoIngreso1").change(function () {
        CheckSesion()
        if ($("#TipoIngreso1").is(":checked")) {
            $('#divTablePers').addClass('loader-in')
            $('#TipoIngreso').val(1)
            $('.check').prop('disabled', true)
        }
    });
    $("#TipoIngreso2").change(function () {
        CheckSesion()
        if ($("#TipoIngreso2").is(":checked")) {
            $('#divTablePers').removeClass('loader-in')
            $('#TipoIngreso').val(2)
            $('.check').prop('disabled', false)
        }
    });
    $("#TipoIngreso").change(function () {
        alert('cambio')
        if (this.val() == '2') {
            $('#divTablePers').removeClass('loader-in')
        } else {
            $('#divTablePers').addClass('loader-in')
        }
    });
    /** Variables para las notificaciones de pantalla */
    const NotifDelay = 2000;
    const NotifOffset = 0;
    const NotifOffsetX = 0;
    const NotifOffsetY = 0;
    const NotifZindex = 9999;
    const NotifMouseOver = 'pause'
    const NotifEnter = 'fadeInDown';
    const NotifExit = 'fadeOut';
    const NotifAlign = 'center';

    ActiveBTN(false, "#submit", 'Ingresando', 'Ingresar')

    function GetLog(archivo) {
        var LOG_URL = archivo;
        $.ajax({
            dataType: "text",
            url: LOG_URL,
            success: function (data) {
                $("#respuetatext").html(data);
            },
            error: function () {
            }
        });
    }


    const legajos_data = async () => {

        const url = "/" + homehost + "/app-data/custom/arrpersonal";

        try {

            const payload = {
                Tipo: $("#aTipo").val(),
                Emp: $("#aEmp").val(),
                Plan: $("#aPlan").val(),
                Sect: $("#aSect").val(),
                Sec2: $("#aSec2").val(),
                Grup: $("#aGrup").val(),
                Sucur: $("#aSucur").val(),
                _c: $("#_c").val(),
                _r: $("#_r").val(),
                Modulo: "ws_novedades",
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
    const ws_novedades = async (payload) => {

        const url = "/" + homehost + "/app-data/ws_novedades";

        try {
            ActiveBTN(true, "#submit", 'Ingresando', 'Ingresar');

            /**
             * Si el valor del campo $TipoIngreso es igual a 2 (Por Legajos), se realiza una llamada a la función legajos_data() para obtener los datos de legajos. Si la llamada falla, se lanza un error indicando que no se pudieron obtener los datos y se solicita al usuario que intente nuevamente.
             */
            if ($TipoIngreso.val() == 2) {

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
            const dataResult = Array.isArray(response.data?.DATA)
                ? response.data.DATA[0]
                : response.data?.DATA;

            if (dataResult !== true && dataResult !== 'true') {
                throw new Error(response.data?.MESSAGE);
            }

            cleanAll();
            ActiveBTN(false, "#submit", 'Ingresando', 'Ingresar');
            ActualizaTablas();
            notify('Novedades ingresadas correctamente', 'success', 5000, 'right');
            reloadDataTable($('#GetPers'), true);
        } catch (error) {
            notify('Error: ' + error.message, 'danger', 5000, 'right');
            ActiveBTN(false, "#submit", 'Ingresando', 'Ingresar');
            reloadDataTable($('#GetPers'), true);
        }
    }

    $(".alta_novedad").bind("submit", function (e) {
        e.preventDefault();
        CheckSesion();

        const map_items_names = {
            'aFicObse': 'Observacion',
            'aFicNove': 'Novedad',
            'aCaus': 'Causa',
            'aFicHoras': 'Horas',
            'legajo[]': 'Legajos',
            'aLaboral': 'Laboral',
            'aFicCate': 'Justifica',
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

        payload.flag = FLAG_LEGAJOS_DATA

        // Procesar los datos del formulario
        $(this).serializeArray().forEach(item => {
            // Procesamiento especial para el rango de fechas
            if (item.name === '_draddNov') {
                const fechas = item.value.split(' al ');
                if (fechas.length === 2) {
                    // Convertir DD/MM/YYYY a YYYY-MM-DD
                    const convertirFecha = (fecha) => {
                        const partes = fecha.trim().split('/');
                        return `${partes[2]}-${partes[1]}-${partes[0]}`;
                    };
                    payload.FechaDesde = convertirFecha(fechas[0]);
                    payload.FechaHasta = convertirFecha(fechas[1]);
                }
                return;
            }

            // Procesamiento especial para legajos (array)
            if (item.name === 'legajo[]') {
                payload.Legajos.push(item.value);
                return;
            }

            // Conversión de checkboxes a valores binarios
            if (item.name === 'aFicCate' || item.name === 'aLaboral') {
                const keyName = map_items_names[item.name];
                payload[keyName] = item.value === 'on' ? 1 : 0;
                return;
            }

            // Mapeo normal de campos
            const keyName = map_items_names[item.name] || item.name;
            payload[keyName] = item.value;
        });

        // Empresa, Planta, Sector, Sección, Grupo y Sucursal al menos uno es obligatorio cuando TipoIngreso es 1 (Por Filtros)
        const tieneAlMenosUno = ['Empresa', 'Planta', 'Sector', 'Seccion', 'Grupo', 'Sucursal']
            .some(campo => payload[campo] !== "");

        if (!tieneAlMenosUno && payload.TipoIngreso === "1") {
            notify('Debe seleccionar al menos una Entidad', 'warning', 3000, 'right');
            return;
        }

        if (payload.Novedad == "") {
            notify('Debe seleccionar una novedad', 'warning', 3000, 'right')
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

        // Asegurar que checkboxes no marcados tengan valor 0
        if (!payload.Justifica) payload.Justifica = 0;
        if (!payload.Laboral) payload.Laboral = 0;

        if (payload.TipoIngreso == "2" || payload.TipoIngreso == "1") {
            ws_novedades(payload);
            return;
        }
        e.stopImmediatePropagation();
    });

    $("#trash_allFilter").on("click", function () {
        cleanAll()
        $("#GetPers").DataTable().page.len(10).draw();
    });

    function cleanAll() {
        CheckSesion();
        limpiarMarcados();
        textCountMarcados();
        ls.set(LS_WAITING_DT, true);

        $('#Cuenta').val(0)
        $('#SelEmpresa').val(null).trigger("change");
        $('#SelPlanta').val(null).trigger("change");
        $('#SelSector').val(null).trigger("change");
        $('#SelSeccion').val(null).trigger("change");
        $('#SelGrupo').val(null).trigger("change");
        $('#SelSucursal').val(null).trigger("change");
        $('.sel_plantas').val(null).trigger("change");
        $('.sel_empresa').val(null).trigger("change");
        $('.sel_sectores').val(null).trigger("change");
        $('.sel_seccion').val(null).trigger("change");
        $('.sel_grupos').val(null).trigger("change");
        $('.sel_sucursal').val(null).trigger("change");
        $('.sel_personal').val(null).trigger("change");
        $('.sel_novedad').val(null).trigger("change");
        $(".sel_seccion").prop("disabled", true);
        $("#aFicCate").prop("checked", false);
        $("#aLaboral").prop("checked", false);
        $("#aFicJust").prop("checked", false);
        $("#aFicObse").val(null).trigger("change");
        $("#aFicHoras").val(null).trigger("change");
        $("#respuetatext").removeClass("fadeIn");
        $("#respuetatext").html("");
        ActiveBTN(false, "#submit", 'Ingresando', 'Ingresar')
        $("#respuesta").removeClass("alert-success");
        $("#respuesta").removeClass("alert-danger");
        $("#respuesta").removeClass("alert-info");
        $('.sel_causa').val(null).trigger('change');
        $(".sel_causa").prop("disabled", true);
        $("#select_causa").addClass("d-none");
    }


    $("#Cuenta").change(function () {
        // if ($('#Cuenta').val() > 0) {
        GetPers()
        // }
    });
    $(document).on('click', '#CloseaddNov', function (e) {
        // $('#CloseaddNov').click(function (e) {
        limpiarMarcados();
        $('#GetPers').DataTable().destroy();
        $('#SelEmpresa').val(null).trigger("change");
        $('#SelPlanta').val(null).trigger("change");
        $('#SelSector').val(null).trigger("change");
        $('#SelSeccion').val(null).trigger("change");
        $('#SelGrupo').val(null).trigger("change");
        $('#SelSucursal').val(null).trigger("change");
        $('.sel_plantas').val(null).trigger("change");
        $('.sel_empresa').val(null).trigger("change");
        $('.sel_sectores').val(null).trigger("change");
        $('.sel_seccion').val(null).trigger("change");
        $('.sel_grupos').val(null).trigger("change");
        $('.sel_sucursal').val(null).trigger("change");
        $('.sel_personal').val(null).trigger("change");
        $('.sel_novedad').val(null).trigger("change");
        $(".sel_seccion").prop("disabled", true);
        $("#FicCate").prop("checked", false);
        $("#Laboral").prop("checked", false);
        $("#FicJust").prop("checked", false);
        $("#FicObse").val(null).trigger("change");
        $("#FicHoras").val(null).trigger("change");
        $("#respuetatext").removeClass("fadeIn");
        $("#respuetatext").html("");
        ActiveBTN(false, "#submit", 'Ingresando', 'Ingresar')
        $("#respuesta").removeClass("alert-success");
        $("#respuesta").removeClass("alert-danger");
        $("#respuesta").removeClass("alert-info");
        $('.sel_causa').val(null).trigger('change');
        $(".sel_causa").prop("disabled", true);
        $("#select_causa").addClass("d-none");
        // $('#GetPers').DataTable().search(this.value).draw();
        // $('#GetPers').DataTable().page.len(10).draw();
        $('#divTablePers').addClass('d-none')
        $('#divTablas').removeClass('d-none');
        $('#divaddNov').addClass('d-none');
        fadeInOnly('#divTablas');
        $('#Encabezado').html('Novedades')
    });

});

