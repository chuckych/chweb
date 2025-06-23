// Desactiva los alerts de error de DataTables en toda la app
$.fn.dataTable.ext.errMode = 'none';

/** Select */
$(function () {
    const homehost = $("#_homehost").val();
    const flag = new Date().getTime();

    const dateRange = async () => {

        const fechaActual = new Date(new Date().setDate(new Date().getDate() + 1));
        const fechaActual7 = new Date(new Date().setDate(new Date().getDate() + 7));

        const proximaSemana = [moment().add(1, "week").startOf("week").add(1, "days"), moment().add(1, "week").endOf("week").subtract(1, "days")];
        const estaSemana = [moment().startOf("week").add(1, "days"), moment().endOf("week").subtract(1, "days")];
        const sigSieteDias = [moment(), moment().add(7, "days")];
        const sigQuinceDias = [moment(), moment().add(15, "days")];
        const sigTreintaDias = [moment(), moment().add(30, "days")];
        const esteMes = [moment().startOf("month"), moment().endOf("month")];
        const sigMes = [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')];

        $('#_dr').daterangepicker({
            singleDatePicker: false,
            showDropdowns: true,
            minYear: new Date().getFullYear(),
            autoUpdateInput: true,
            opens: "left",
            drops: "down",
            startDate: fechaActual,
            endDate: fechaActual7,
            autoApply: true,
            minDate: fechaActual,
            alwaysShowCalendars: true,
            linkedCalendars: false,
            ranges: {
                'Esta Semana': estaSemana,
                'Proxima Semana': proximaSemana,
                'Proximos 7 días': sigSieteDias,
                'Proximos 15 días': sigQuinceDias,
                'Proximos 30 días': sigTreintaDias,
                'Este mes': esteMes,
                'Proximo Mes': sigMes
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
                alwaysShowCalendars: true,
            },
        })
    }
    dateRange().then(() => {
        $('#_dr').on('apply.daterangepicker', function (ev, picker) {
            getHoras_();
        });
    });

    const mapSinDescripcion = {
        'secciones': 'Sin sección',
        'sectores': 'Sin sector',
        'grupos': 'Sin grupo',
        'sucursales': 'Sin sucursal',
        'personal': 'Sin personal',
        'empresas': 'Sin empresa',
        'plantas': 'Sin planta',
    }

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
            placeholder: placeholder,
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
                                text: item.Descripcion || mapSinDescripcion[estructura] || 'Sin descripción',
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
                getHoras_();
                getPersonal();
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
    const getPersonal = async () => {
        const url = "/" + homehost + "/app-data/personal/filtros";

        if ($.fn.DataTable.isDataTable('#tabla')) {
            loaderIn('#tabla', true);
            $('#tabla').DataTable().ajax.reload(); // Recargar la tabla con los datos actuales
            return false;
        }

        const tabla = $('#tabla').DataTable({
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            bProcessing: false,
            serverSide: false,
            deferRender: true,
            searchDelay: 100,
            dom: `
                <'row'
                    <'col-12'<'d-flex w-100 align-items-center justify-content-between'lp>>
                    <'col-12'<'border radius p-2 table-responsive shadow-sm'<t>>>
                    <'col-12'<'d-inline-flex w-100 justify-content-between'i>>
                >
            `,
            ajax: {
                url: url,
                type: "POST",
                "data": function (data) {
                    const tipo = $('input[name="Tipo"]:checked').val() || 0;
                    data.descripcion = '';
                    data.strict = 0;
                    data.estructura = 8;
                    data.nullCant = 0;
                    data.activo = 1;
                    data.estado = 0;
                    data.tipo = tipo;
                    data.proyectar = 1;
                    data.empresas = $("#selectjs_empresa").val();
                    data.plantas = $("#selectjs_planta").val();
                    data.convenios = $("#selectjs_convenio").val();
                    data.sectores = $("#selectjs_sector").val();
                    data.secciones = $("#selectjs_seccion").val();
                    data.grupos = $("#selectjs_grupos").val();
                    data.sucursales = $("#selectjs_sucursal").val();
                    data.personal = $("#selectjs_personal").val();
                    data.flag = flag;
                    data.datatable = 1; // Indica que es una petición de DataTable
                },
                error: function () {
                    $("#tabla_processing").css("display", "none");
                },
            },
            columns: [
                {
                    data: 'Cod', className: 'py-2 align-middle', targets: '', title: 'LEGAJO',
                    "render": function (data, type, row, meta) {
                        return data;
                    },
                },
                {
                    data: 'Descripcion', className: ' align-middle', targets: '', title: 'APELLIDO Y NOMBRE',
                    "render": function (data, type, row, meta) {
                        return data;
                    },
                },
                {
                    data: '', className: ' align-middle w-100', targets: '', title: '<br><br>',
                    "render": function (data, type, row, meta) {
                        return '';
                    },
                },
            ],
            paging: true,
            info: true,
            searching: true,
            ordering: false,
            language: {
                "bProcessing": "Actualizando . . .",
                "sLengthMenu": "_MENU_",
                "sZeroRecords": "",
                "sEmptyTable": "",
                "sInfo": "Mostrando _START_ al _END_ de _TOTAL_ Legajos",
                "sInfoEmpty": "No se encontraron resultados",
                "sInfoFiltered": "(filtrado de un total de _MAX_ Legajos)",
                "sInfoPostFix": "",
                "sSearch": "",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "<div class='spinner-border text-light'></div>",
                "oPaginate": {
                    "sFirst": "<i class='bi bi-chevron-double-left'></i>",
                    "sLast": "<i class='bi bi-chevron-double-right'></i>",
                    "sNext": "<i class='bi bi-chevron-right'></i>",
                    "sPrevious": "<i class='bi bi-chevron-left'></i>"
                },
                "oAria": {
                    "sSortAscending": ":Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ":Activar para ordenar la columna de manera descendente"
                }
            },
            // Eventos de la tabla
            initComplete: function (e, settings, json) {
            },
            preDrawCallback: function () {
                loaderIn('#tabla', true);
            },
            // al cambiar de pagina o cambiar el tamaño de la tabla mostrar en formato decimal o en horas
            drawCallback: function (e, settings, json) {
                setTimeout(() => {
                    loaderIn('#tabla', false);
                    $("#btnProcesar").prop('disabled', false);
                    $("#btnEliminar").prop('disabled', false);
                }, 0);
            }
        });
    }

    async function getTiposHorasYDatos() {
        const url = "/" + homehost + "/app-data/horas/totales";
        // Realiza una petición AJAX para obtener tiposHoras y los datos
        const tipo = $('input[name="Tipo"]:checked').val() || 0;
        let TIPO = tipo === '1' ? ["1"] : tipo === '2' ? [] : ["0"];

        const response = await $.ajax({
            url: url,
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                start: 0,
                length: 100000,
                FechIni: $('#_dr').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                FechFin: $('#_dr').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                Empr: $("#selectjs_empresa").val() || [],
                Plan: $("#selectjs_planta").val() || [],
                Conv: $("#selectjs_convenio").val() || [],
                Sect: $("#selectjs_sector").val() || [],
                Sec2: $("#selectjs_seccion").val() || [],
                Grup: $("#selectjs_grupos").val() || [],
                Sucu: $("#selectjs_sucursal").val() || [],
                Lega: $("#selectjs_personal").val() || [],
                LegTipo: TIPO,
                tipo: tipo, // Agrega el tipo seleccionado
            }),
            dataType: "json"
        });
        return response; // Debe incluir response.tiposHoras y response.data
    }

    async function getHoras_() {

        if ($.fn.DataTable.isDataTable('#tabla_horas')) {
            $('#tabla_horas').DataTable().clear().destroy(true);
        }

        $('#divTablaHoras').html(`
            <table id="tabla_horas" class="table text-nowrap fadeIn">
            <thead></thead>
            <tfoot>
                <tr></tr>
            </tfoot>
        </table>`)

        // promesa de time out
        await new Promise(resolve => setTimeout(resolve, 100));

        const response = await getTiposHorasYDatos();

        if (!response || !response.totales || !response.data) {
            console.log('Error: La respuesta no contiene los datos esperados.');
            $('#btnExportar').hide();
            $('#btnExportarPDF').hide();
            return; // Detener la ejecución si no hay datos válidos
        }
        $('#btnExportar').show();
        $('#btnExportarPDF').show();

        const tiposHoras = response.totales; // Array de tipos de hora

        const data = response.data; // Datos de la tabla

        // Arma las columnas dinámicamente
        let columns = [
            // { data: 'LegApNo', title: 'LEGAJO' }
            {
                data: 'Lega', className: '', targets: '', title: 'LEGAJO',
                "render": function (data, type, row, meta) {
                    return `
                        <div class="d-flex text-truncate" style="min-width:75px; max-width:75px">
                            ${data}
                        </div>
                    `;
                },
            },
            {
                data: 'LegApNo', className: '', targets: '', title: 'APELLIDO Y NOMBRE',
                "render": function (data, type, row, meta) {
                    return `
                        <div title="${data}" class="d-flex text-truncate" style="min-width:160px; max-width:160px">
                            ${data}
                        </div>
                    `;
                },
            },
        ];
        tiposHoras.forEach((tipo, index) => {
            const espar = index % 2 === 0 ? true : false;

            columns.push({
                data: tipo.HoraCodi,
                title: `<div class="text-uppercase">(${tipo.HoraCodi})<br>${tipo.THoDesc2}</div>`,
                className: `text-center ${espar ? 'bg-light' : ''}`,
                render: function (data, type, row) {
                    // Aquí puedes acceder a row.Totales y buscar el valor correspondiente
                    const found = row.Totales.find(t => t.HoraCodi == tipo.HoraCodi);
                    return found ? `<span class="">${found.EnHoras2}</span>` : '-';
                }
            });
        });
        columns.push(
            {
                data: '',
                title: '',
                className: 'w-100',
                render: function (data, type, row) {
                    return '';
                },
            }
        );

        // Inicializa la tabla
        $('#tabla_horas').DataTable({
            lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            bProcessing: false,
            serverSide: false,
            deferRender: true,
            searchDelay: 100,
            dom: `
                <'row'
                    <'col-12'
                        <'d-flex w-100 align-items-center justify-content-between'l<'d-flex align-items-center'<'divBtnHoras mt-0 p-0 mr-1'>p>>
                    >
                    <'col-12'<'border radius p-2 table-responsive shadow-sm'<t>>>
                    <'col-12'<'d-inline-flex w-100 justify-content-between'i>>
                >
            `,
            data: data,
            columns: columns,
            paging: true,
            info: true,
            searching: true,
            ordering: false,
            language: {
                "bProcessing": "Actualizando . . .",
                "sLengthMenu": "_MENU_",
                "sZeroRecords": "",
                "sEmptyTable": "",
                "sInfo": "Mostrando _START_ al _END_ de _TOTAL_ Legajos",
                "sInfoEmpty": "No se encontraron resultados",
                "sInfoFiltered": "(filtrado de un total de _MAX_ Legajos)",
                "sInfoPostFix": "",
                "sSearch": "",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "<div class='spinner-border text-light'></div>",
                "oPaginate": {
                    "sFirst": "<i class='bi bi-chevron-double-left'></i>",
                    "sLast": "<i class='bi bi-chevron-double-right'></i>",
                    "sNext": "<i class='bi bi-chevron-right'></i>",
                    "sPrevious": "<i class='bi bi-chevron-left'></i>"
                },
                "oAria": {
                    "sSortAscending": ":Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ":Activar para ordenar la columna de manera descendente"
                }
            },
            initComplete: function (e, settings, json) {
                // Agregar el botón de procesar
                // const divBtnHoras = $('.divBtnHoras');
                // divBtnHoras.empty(); // Limpiar el contenido previo
                // divBtnHoras.append(`
                //     <a href="javascript:void(0)" id="btnHoras" class="btn btn-link font08 m-0">
                //         Actualizar Tablas
                //     </a>
                // `);
            },
            preDrawCallback: function () {
                loaderIn('#tabla_horas', true);
            },
            // al cambiar de pagina o cambiar el tamaño de la tabla mostrar en formato decimal o en horas
            drawCallback: function (e, settings, json) {
                setTimeout(() => {
                    loaderIn('#tabla_horas', false);
                    // $("#btnHoras").prop('disabled', false);
                }, 0);

                tfootHoras(tiposHoras); // Llama a la función para agregar el footer con los tipos de horas

            }
        });

    }

    const tfootHoras = (tiposHoras) => {
        const borderTop = 'style="border-top: 2px solid #dee2e6 !important; font-size:.9rem;"';
        // Verifica si ya existe un footer y lo elimina
        if ($('#tabla_horas tfoot').length) {
            $('#tabla_horas tfoot').remove();
        }
        $('#tabla_horas').append('<tfoot><tr></tr></tfoot>');
        let $tfoot = $('#tabla_horas tfoot tr');
        $tfoot.empty(); // Limpia el footer 

        // Primeras columnas fijas (LEGAJO, APELLIDO Y NOMBRE)
        $tfoot.append(`<th ${borderTop}"></th><th ${borderTop}"></th>`);

        // Columnas dinámicas de tiposHoras
        tiposHoras.forEach((tipo, index) => {
            const espar = index % 2 === 0 ? true : false;
            $tfoot.append(`<th ${borderTop} class="text-center ${espar ? 'bg-light' : ''}"><strong>${tipo.EnHoras2}</strong></th>`);
        });

        $tfoot.append(`<th ${borderTop}></th>`);
    }

    $("#btnHoras").on("click", async function (e) {
        e.preventDefault();
        getPersonal();
        getHoras_();
    });

    getPersonal();
    getHoras_();

    select2Estruct("#selectjs_empresa", true, "Empresas", 1);
    select2Estruct("#selectjs_planta", true, "Plantas", 2);
    select2Estruct("#selectjs_convenio", true, "Convenios", 3);
    select2Estruct("#selectjs_sector", true, "Sectores", 4);
    select2Estruct("#selectjs_seccion", true, "Secciones", 5);
    select2Estruct("#selectjs_grupos", true, "Grupos", 6);
    select2Estruct("#selectjs_sucursal", true, "Sucursales", 7);
    select2Estruct("#selectjs_personal", true, "Legajos", 8);
    $("#selectjs_seccion").prop("disabled", true);

    $('#selectjs_sector').on('select2:close', function (e) {
        e.preventDefault()
        $("#selectjs_seccion").prop("disabled", false);
        $('#selectjs_seccion').val(null).trigger('change');
    });
    $('#selectjs_sector').on('select2:unselect', function (e) {
        e.preventDefault()
        $("#selectjs_seccion").prop("disabled", true);
        $('#selectjs_seccion').val(null).trigger('change');
        $('#selectjs_sector').val(null).trigger("change");
    });

    function LimpiarFiltros() {
        $('#selectjs_empresa').val(null).trigger("change");
        $('#selectjs_planta').val(null).trigger("change");
        $('#selectjs_convenio').val(null).trigger("change");
        $('#selectjs_sector').val(null).trigger("change");
        $('#selectjs_seccion').val(null).trigger("change");
        $("#selectjs_seccion").prop("disabled", true);
        $('#selectjs_grupos').val(null).trigger("change");
        $('#selectjs_sucursal').val(null).trigger("change");
        $('#selectjs_personal').val(null).trigger("change");
    }

    $('input[name="Tipo"]').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        // trigger change event on
        $('input[name="Tipo"]').trigger('change');
        LimpiarFiltros();
        getPersonal();
        getHoras_();
    });

    $("#trash_allIn").on("click", async function (e) {
        e.preventDefault();
        e.stopPropagation();

        LimpiarFiltros();

        const todos = document.querySelectorAll('input[name="Tipo"]');
        todos.forEach((item) => {
            item.checked = false;
            item.closest('label').classList.remove('active');
        });
        const TipoTodos = document.querySelector('#TipoTodos');
        TipoTodos.checked = true;
        TipoTodos.closest('label').classList.add('active');
        $('input[name="Tipo"]').trigger('change');

        $('.select2-results__option[aria-selected=true]').each(function () {
            $(this).removeClass('select2-results__option--highlighted');
            $(this).removeAttr('aria-selected');
        });

        getPersonal();
        getHoras_();
    });

    // --- FUNCIÓN REUTILIZABLE PARA GENERAR EL TEXTO DE LA TABLA ENMARCADA ---
    function generarTablaTexto(table, data) {
        const columns = table.settings().init().columns;
        const headers = columns.map(col => col.title.replace(/<[^>]*>?/gm, '').trim());
        const keys = columns.map(col => col.data);
        const rows = data.map(row => {
            return keys.map(key => {
                if (!key) return '';
                if (key !== 'Lega' && key !== 'LegApNo') {
                    const found = (row.Totales || []).find(t => t.HoraCodi == key);
                    return found ? String(found.EnHoras2) : '';
                }
                const cellData = row[key] || '';
                return typeof cellData === 'object' ? JSON.stringify(cellData) : String(cellData);
            });
        });
        headers.pop();
        keys.pop();
        rows.forEach(row => row.pop());
        const colCount = headers.length;
        while (headers.length < colCount) headers.push('');
        while (headers.length > colCount) headers.pop();
        rows.forEach(row => {
            while (row.length < colCount) row.push('');
            while (row.length > colCount) row.pop();
        });
        const padding = 2;
        const colWidths = headers.map((_, colIdx) =>
            Math.max(...rows.map(row => (row && row[colIdx] ? row[colIdx].length : 0)), headers[colIdx] ? headers[colIdx].length : 0) + padding * 2
        );
        const pad = (str, len, alignLeft = false) => {
            const contenido = (str || '').toString();
            if (alignLeft) {
                return ' ' + contenido.padEnd(len - 1, ' ');
            }
            const totalPad = len - contenido.length;
            const left = Math.floor(totalPad / 2);
            const right = totalPad - left;
            return ' '.repeat(left) + contenido + ' '.repeat(right);
        };
        const marco = '+' + colWidths.map(len => '-'.repeat(len)).join('+') + '+';
        const separador = '|' + colWidths.map(len => '-'.repeat(len)).join('+') + '|';
        const formatRow = (row) =>
            '|' + row.map((cell, idx) => pad(cell, colWidths[idx], idx === 0 || idx === 1)).join('|') + '|';
        const formattedHeader = formatRow(headers);
        const formattedDataRows = rows.map(formatRow);
        // --- FOOTER DE TOTALES ---
        const toMinutes = (hhmm) => {
            if (!hhmm || typeof hhmm !== 'string') return 0;
            const [h, m] = hhmm.split(':').map(Number);
            if (isNaN(h) || isNaN(m)) return 0;
            return h * 60 + m;
        };
        const toHHMM = (mins) => {
            const h = Math.floor(mins / 60);
            const m = mins % 60;
            return (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m;
        };
        const totalesFooter = keys.map(key => {
            if (!key) return '';
            if (key !== 'Lega' && key !== 'LegApNo') {
                let totalMins = 0;
                data.forEach(row => {
                    const found = (row.Totales || []).find(t => t.HoraCodi == key);
                    if (found && typeof found.EnHoras2 === 'string' && found.EnHoras2.includes(':')) {
                        totalMins += toMinutes(found.EnHoras2);
                    }
                });
                return totalMins ? toHHMM(totalMins) : '';
            }
            return '';
        });
        while (totalesFooter.length < colCount) totalesFooter.push('');
        while (totalesFooter.length > colCount) totalesFooter.pop();
        const formattedFooter = formatRow(totalesFooter);
        // === TÍTULO Y RANGO DE FECHAS ENMARCADO ===
        const dr = $('#_dr').data('daterangepicker');
        const fechaDesde = dr ? dr.startDate.format('DD/MM/YYYY') : '';
        const fechaHasta = dr ? dr.endDate.format('DD/MM/YYYY') : '';
        const titulo = 'REPORTE DE HORAS CALCULADAS';
        const subtitulo = `DESDE: ${fechaDesde}  HASTA: ${fechaHasta}`;
        const anchoTotal = marco.length;
        const centrar = (texto) => {
            const textoMay = texto.toUpperCase();
            const pad = Math.max(0, anchoTotal - 2 - textoMay.length);
            const left = Math.floor(pad / 2);
            const right = pad - left;
            return '|' + ' '.repeat(left) + textoMay + ' '.repeat(right) + '|';
        };
        const tituloEnmarcado = centrar(titulo);
        const subtituloEnmarcado = centrar(subtitulo);
        return [
            marco,
            tituloEnmarcado,
            subtituloEnmarcado,
            marco,
            formattedHeader,
            separador,
            ...formattedDataRows,
            separador,
            formattedFooter,
            marco
        ].join('\n');
    }

    $("#btnExportar").on("click", async function (e) {
        e.preventDefault();
        e.stopPropagation();
        const table = $('#tabla_horas').DataTable();
        const data = Array.from(table.rows({ search: 'applied' }).data());
        if (data.length === 0) {
            alert('No hay datos para exportar.');
            return;
        }
        const txtContent = generarTablaTexto(table, data);
        const blob = new Blob([txtContent], { type: 'text/plain;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'reporte_horas_calculadas_' + new Date().getTime() + '.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        notify('Datos exportados correctamente', 'success', 5000, 'right');
    });

    $("#btnExportarPDF").on("click", async function (e) {
        e.preventDefault();
        e.stopPropagation();
        const table = $('#tabla_horas').DataTable();
        const data = Array.from(table.rows({ search: 'applied' }).data());
        if (data.length === 0) {
            alert('No hay datos para exportar.');
            return;
        }
        const txtContent = generarTablaTexto(table, data);
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: 'portrait', unit: 'pt', format: 'a4' });
        doc.setFont('courier', 'normal');
        const fontSize = 8;
        doc.setFontSize(fontSize);

        // doc.setLineWidth(0.5);
        // // linea punteadas
        // doc.setDrawColor(0, 0, 0);
        // doc.setLineDash([2, 2], 0);
        // doc.setFillColor(255, 255, 255);
        // doc.setTextColor(0, 0, 0);
        // doc.rect(10, 10, doc.internal.pageSize.getWidth() - 20, doc.internal.pageSize.getHeight() - 20);

        // ajustar al tamaño de la página
        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();
        const margin = 20;
        const lineHeight = fontSize + 2;
        const lines = txtContent.split('\n');
        let y = margin;
        lines.forEach(line => {
            doc.text(line, margin, y, { baseline: 'top' });
            y += lineHeight;
            if (y > doc.internal.pageSize.getHeight() - margin) {
                doc.addPage();
                y = margin;
            }
        });
        doc.save('reporte_horas_calculadas_' + new Date().getTime() + '.pdf');
        notify('PDF exportado correctamente', 'success', 5000, 'right');
    });

    $("#btnProcesar").on("click", async function (e) {
        e.preventDefault();
        e.stopPropagation();

        $.notifyClose();
        notify('Aguarde por favor . . .', 'info', 5000, 'right');

        // deshabilitar el botón para evitar múltiples clics
        $(this).prop('disabled', true).text('Aguarde').addClass('hint--info').attr('aria-label', 'Enviando proyección de Horas');

        const selectedValues = $('#tabla').DataTable().rows({ selected: true }).data().map(row => row.Cod).toArray();
        if (selectedValues.length === 0) {
            alert('Debe seleccionar al menos un legajo para procesar.');
            return;
        }

        // obtener la fecha de inicio y fin del rango seleccionado
        const fechaInicio = $('#_dr').data('daterangepicker').startDate.format('YYYY-MM-DD');
        const fechaFin = $('#_dr').data('daterangepicker').endDate.format('YYYY-MM-DD');

        const url = "/" + homehost + "/app-data/proyectar";

        try {
            const response = await axios.post(url, {
                Legajos: selectedValues,
                FechaDesde: fechaInicio,
                FechaHasta: fechaFin
            });
            $.notifyClose();
            if (response.data?.RESPONSE_CODE === '200 OK') {
                if (response.data?.DATA === true) {
                    notify('Proyección de horas enviado correctamente', 'success', 5000, 'right')
                } else {
                    notify('No se pudo procesar la proyección de horas', 'danger', 5000, 'right');
                }
                getHoras_();
            } else {
                alert('Error al procesar');
            }
        } catch (error) {
            console.error('Error en la solicitud:', error);
            // alert('Ocurrió un error al procesar los datos.');
        } finally {
            $(this).prop('disabled', false).text('Generar').removeClass('hint--info').attr('aria-label', 'Generar proyección de horas');
        }
    });
    $("#btnEliminar").on("click", async function (e) {
        e.preventDefault();
        e.stopPropagation();

        $.notifyClose();
        notify('Aguarde por favor . . .', 'info', 5000, 'right');

        // deshabilitar el botón para evitar múltiples clics
        $(this).prop('disabled', true).text('Aguarde').addClass('hint--info').attr('aria-label', 'Eliminando proyección de Horas');

        const selectedValues = $('#tabla').DataTable().rows({ selected: true }).data().map(row => row.Cod).toArray();
        if (selectedValues.length === 0) {
            alert('Debe seleccionar al menos un legajo para procesar.');
            return;
        }

        // obtener la fecha de inicio y fin del rango seleccionado
        const fechaInicio = $('#_dr').data('daterangepicker').startDate.format('YYYY-MM-DD');
        const fechaFin = $('#_dr').data('daterangepicker').endDate.format('YYYY-MM-DD');

        const url = "/" + homehost + "/app-data/proyectar";

        try {
            const response = await axios.delete(url, {
                data: {
                    Legajos: selectedValues,
                    FechaDesde: fechaInicio,
                    FechaHasta: fechaFin
                }
            });
            $.notifyClose();
            if (response.data?.RESPONSE_CODE === '200 OK') {
                const total = response.data?.TOTAL || 0;
                if (total > 0) {
                    notify('Proyección de horas eliminada correctamente<br>Se eliminaron ' + total + ' registros', 'success', 5000, 'right')
                } else {
                    notify('No hay registros eliminados', 'danger', 5000, 'right');
                }
                getHoras_();
            } else {
                alert('Error al eliminada');
            }
        } catch (error) {
            console.error('Error en la solicitud:', error);
            // alert('Ocurrió un error al procesar los datos.');
        } finally {
            $(this).prop('disabled', false).text('Eliminar').removeClass('hint--info').attr('aria-label', 'Eliminar proyección de horas');
        }
    });
});

