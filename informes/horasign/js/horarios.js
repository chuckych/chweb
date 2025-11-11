// Desactiva los alerts de error de DataTables en toda la app
// $.fn.dataTable.ext.errMode = 'none';

/** Select */
$(function () {
    const homehost = $("#_homehost").val();
    const flag = new Date().getTime();
    const DIR_APP_DATA = '../../app-data';

    const dateRange = async () => {

        const fechaActual = new Date(new Date().setDate(new Date().getDate() + 1));
        const fechaActual7 = new Date(new Date().setDate(new Date().getDate() + 7));

        const proximaSemana = [
            moment().add(1, "week").startOf("week").add(1, "days"),
            moment().add(1, "week").startOf("week").add(7, "days"),
        ];
        const estaSemana = [
            moment().startOf("week").add(1, "days"),
            moment().startOf("week").add(7, "days"),
        ];
        const sigSieteDias = [moment().add(1, "days"), moment().add(7, "days")];
        const sigQuinceDias = [moment().add(1, "days"), moment().add(15, "days")];
        const sigTreintaDias = [moment().add(1, "days"), moment().add(30, "days")];
        const esteMes = [moment().startOf("month"), moment().endOf("month")];
        const sigMes = [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')];

        $('#_dr').daterangepicker({
            singleDatePicker: false,
            showDropdowns: true,
            // minYear: new Date().getFullYear(),
            autoUpdateInput: true,
            opens: "left",
            drops: "down",
            startDate: fechaActual,
            endDate: fechaActual7,
            autoApply: true,
            // minDate: fechaActual,
            alwaysShowCalendars: true,
            linkedCalendars: true,
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
                "firstDay": 1
            },
        })
    }
    dateRange().then(() => {
        $('#_dr').on('apply.daterangepicker', function (ev, picker) {
            getPersonal();
        });
    });

    const asignacionStr = (prioridad, date) => {
        const dateFormat = dateFormatted(date);
        const splitDate = (date) => {
            const [d1, d2] = date.split(' - ');
            return [dateFormatted(d1), dateFormatted(d2)];
        };
        const map = {
            "1": `Citación fecha ${dateFormat}`,
            "2": `Horario desde ${splitDate(date)[0]} hasta ${splitDate(date)[1]}`,
            "3": (date.split(' - ').length > 1) ? `Rotación desde ${splitDate(date)[0]} hasta ${splitDate(date)[1]}` : `Rotación desde ${dateFormat}`,
            "4": `Horario desde ${dateFormat}`,
        };
        return map[prioridad] || '';
    };

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
                        proyectar: 2,
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

    let recargaPorFiltro = false;
    const getPersonal = async () => {
        const url = "/" + homehost + "/app-data/personal/filtros";

        if ($.fn.DataTable.isDataTable('#tablaPersonal')) {
            loaderIn('#tablaPersonal', true);
            recargaPorFiltro = true;
            $('#tablaPersonal').DataTable().ajax.reload(); // Recargar la tablaPersonal con los datos actuales
            return false;
        }

        const renderMobile = `<'row'
                    <'col-12'
                        <'table-responsive't>
                        <'d-flex flex-column align-items-center justify-content-center'p>
                        <'d-flex flex-column align-items-center justify-content-center gap5'i>
                    >
                >`
        const renderDesktop = `<'row'
                    <'col-12'
                        <'table-responsive d-flex flex-row align-items-center gap5'tp>
                        <i>
                    >
                >`
        let renderTable = '';


        if (window.innerWidth < 768) {
            renderTable = renderMobile;
        } else {
            renderTable = renderDesktop;
        }

        const tablaPersonal = $('#tablaPersonal').DataTable({
            pagingType: "full",
            lengthMenu: [[1], [1]],
            bProcessing: false,
            serverSide: false,
            deferRender: true,
            searchDelay: 1500,
            dom: renderTable,
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
                    data.proyectar = 2;
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
                    $("#tablaPersonal_processing").css("display", "none");
                },
            },
            columns: [
                /** Columna Legajo */
                {
                    data: 'Cod', className: 'w80 px-3 border border-right-0 rounded-left bg-light', targets: 'Cod',
                    render: function (data, type, row, meta) {
                        return '<div>' + data + '</div>';
                    }, visible: true
                },
                /** Columna Nombre */
                {
                    data: 'Descripcion', className: 'text-center w200 border rounded-right bg-light px-3', targets: 'Descripcion',
                    render: function (data, type, row, meta) {
                        return `
                        <span title="${data}">
                            <span class="text-truncate" style="width:200px">
                                ${data}
                            </span>
                        </span>`;
                    }, visible: true
                }
            ],
            paging: true,
            responsive: false,
            info: true,
            ordering: false,
            language: DT_SPANISH_LEGAJOS
            // preDrawCallback: function () {
            // loaderIn('#tablaPersonal', true);
            // },
        });
        tablaPersonal.on('page', function (e, settings, json) {
            getHorarios();
        });
        tablaPersonal.on('init', function (e, settings, json) {
            $("#tablaPersonal thead").remove();
            loaderIn('#tablaPersonal', false);
            $('#tablaPersonal').show();
            getHorarios();
            // getSemanal();
        });
        tablaPersonal.on('draw', function (e, settings, json) {
            // console.log(recargaPorFiltro);

            if (recargaPorFiltro) {
                getHorarios();
                recargaPorFiltro = false;
            }
            loaderIn('#tablaPersonal', false);
        });
    }
    const getHorarios = async () => {
        const url = "/" + homehost + "/app-data/asignados";

        if ($.fn.DataTable.isDataTable('#tabla')) {
            loaderIn('.tableHorarios', true);
            $('#tabla').DataTable().ajax.reload(); // Recargar la tabla con los datos actuales
            return false;
        }

        const tabla = $('#tabla').DataTable({
            lengthMenu: [[7, 14, 21, 28, 31], [7, 14, 21, 28, 31]],
            bProcessing: false,
            serverSide: false,
            deferRender: true,
            searchDelay: 100,
            dom: `
            <'row'
                <'col-12'
                    <'infoLega'>
                    <'table-responsive tableHorarios't>
                >
                <'col-12'
                    <'d-flex w-100 align-items-center justify-content-between mt-2'lp>
                >
            >
            <'text-right w-100 infoTabla'i>
            `,
            ajax: {
                url: url,
                type: "POST",
                dataSrc: function (json) {
                    // Convierte el objeto DATA en un array plano
                    return Object.values(json.DATA).flat();
                },
                "data": function (data) {
                    const fechaInicio = $('#_dr').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    const fechaFin = $('#_dr').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    const tipo = $('input[name="Tipo"]:checked').val() || 0;
                    const legajo = $('#tablaPersonal').DataTable().rows({ page: 'current' }).data().toArray()[0]?.Cod || '';

                    data.FechaDesde = fechaInicio;
                    data.FechaHasta = fechaFin;
                    data.Empresas = $("#selectjs_empresa").val();
                    data.Plantas = $("#selectjs_planta").val();
                    data.Convenios = $("#selectjs_convenio").val();
                    data.Sectores = $("#selectjs_sector").val();
                    data.Secciones = $("#selectjs_seccion").val();
                    data.Grupos = $("#selectjs_grupos").val();
                    data.Sucursales = $("#selectjs_sucursal").val();
                    // data.Legajos = $("#selectjs_personal").val();
                    data.Legajos = [legajo];
                    data.SinHorarios = 1;
                    data.Egreso = 1;
                    data.AgruparPor = 'Legajo';
                },
                error: function () {
                    $("#tabla_processing").css("display", "none");
                },
            },
            columns: [
                {
                    data: 'Legajo', className: '', targets: '', title: 'LEGAJO',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        return `<div class="w80">${data}</div>`;
                    },
                },
                {
                    data: 'Fecha', className: '', targets: '', title: 'FECHA',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        return `<div class="d-flex flex-column">
                            <div>${dateFormatted(data)}</div>
                            <div class="text-secondary font08">${row.Dia}</div>
                            </div>`;
                    },
                },
                {
                    data: 'Horario', className: '', targets: '', title: 'HORARIO',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        const Feriado = row?.Feriado;
                        const Prioridad = row?.Prioridad;
                        let dataCol = data || 'Sin horario asignado';

                        const franco = (valor) => {
                            return valor.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
                        }
                        const horarioStr = (valor, row, feriado) => {
                            const feriadoStr = feriado == "1" ? `<span class="hint hint--top text-secondary font08" aria-label="${row.FeriadoStr}">(Fer)</span>` : '';
                            const entrada = row.Entrada === '00:00' || !row.Entrada ? false : true;
                            const salida = row.Salida === '00:00' || !row.Salida ? false : true;
                            const desdeHasta = entrada && salida ? `${row.Entrada} a ${row.Salida}` : '';
                            const map = {
                                "1": `<div class="d-flex flex-column"><span>${desdeHasta} ${feriadoStr}</span><span class="text-secondary font08">Citación</span></div>`,
                                "FRANCO": `<span class="text-capitalize">${franco(valor)} ${feriadoStr} </span><br><span class="text-secondary font08">${desdeHasta}</span>`,
                                // "Feriado": `<span class="hint--top" aria-label="${row.FeriadoStr}">Feriado ${feriadoStr}</span>`,
                                "Horario": `<span>${desdeHasta} ${feriadoStr}</span>`,
                            };
                            return map[valor] || '';
                        };

                        if (Prioridad == "1") {
                            return horarioStr('1', row, Feriado);
                        }
                        // if (Feriado == "1") {
                        //     return horarioStr('Feriado', row, Feriado);
                        // }
                        if (data == "FRANCO") {
                            return horarioStr("FRANCO", row, Feriado);
                        }
                        return horarioStr("Horario", row, Feriado);
                    },
                },
                {
                    data: 'Descanso', className: 'text-center', targets: '', title: 'DESCANSO',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        return data != 0 ? data : '00:00';
                    },
                },
                {
                    data: 'HsATrab', className: ' text-center', targets: '', title: '<span title="Horas a trabajar">HS a TR</span>',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        return data ? data : '00:00';
                    },
                },
                {
                    data: 'HsDelDia', className: ' text-center', targets: '', title: '<span title="Horas del día">HS DÍA</span>',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        return data ? data : '00:00';
                    },
                },
                {
                    data: 'CodigoHorario', className: 'text-center pr-0', targets: '', title: 'COD',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        const cod = `(${data ? data : '0'})`
                        return cod;
                    },
                },
                {
                    data: 'DescripcionHorario', className: '', targets: '', title: 'DESCRIPCIÓN / ASIGNACIÓN',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        const prioridad = row?.Prioridad;
                        const referencia = row?.Referencia || ''; // Fecha o rango de fechas
                        const referenciaStr = asignacionStr(prioridad, referencia);
                        return `<div class="d-flex flex-column"><div>${data ? data : 'Sin horario asignado'}</div><div class="text-secondary font08">${referenciaStr}</div></div>`;
                    },
                },
                {
                    data: 'HorID', className: 'w-100 text-right pr-0', targets: '', title: '<div class="text-center float-right" style="width:40px">ID</div>',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        const HorID = `${data ? data : ''}`
                        const colorInt = row?.HorColor;
                        const textColor = getTextColor(colorInt);
                        const bgColor = `rgb(${intToRgb(colorInt).join(', ')})`;
                        return `<div class="text-center float-right" style="height:20px;color:${textColor}; background-color:${bgColor}; border-radius: 5px; padding: 2px 5px; font-size: 12px; width:40px">${HorID}</div>`;
                    },
                },
                {
                    data: '', className: ' w-100', targets: '', title: '',
                    render: function (data, type, row, meta) {
                        if (type !== 'display') return '';
                        return '';
                    },
                },
            ],
            paging: true,
            info: true,
            searching: true,
            ordering: false,
            language: DT_SPANISH_DIAS,
            // Eventos de la tabla
            initComplete: function (e, settings, json) {
                $('#tabla').show();
            },
            preDrawCallback: function () {
                loaderIn('.tableHorarios', true);
            },
        });
        tabla.on('draw', function (e, settings, json) {
            setTimeout(() => {
                loaderIn('.tableHorarios', false);
            }, 0);
            // const info = tabla.rows({ page: 'current' }).data().toArray()[0];
            // const legajo = info ? info?.Legajo : '';
            // const nombre = info ? info?.Nombre : '';
            // const infoLega = `<div class="p-1 px-2 font09 bg-light border radius">Legajo: ${legajo} - ${nombre}</div>`;
            // $('.infoLega').html(infoLega);
        });
    }
    getPersonal();

    // on show collapse ·Filtros
    $('#Filtros').on('shown.bs.collapse', function () {

        // #mostrarFiltros aria-label cambiar a "Ocultar Filtros"
        $('#mostrarFiltros').attr('aria-label', 'Ocultar Filtros');

        // remover atributo hidden de #Filtros
        $('#Filtros').removeAttr('hidden');

        select2Estruct("#selectjs_empresa", true, "Empresas", 1);
        select2Estruct("#selectjs_planta", true, "Plantas", 2);
        select2Estruct("#selectjs_convenio", true, "Convenios", 3);
        select2Estruct("#selectjs_sector", true, "Sectores", 4);
        select2Estruct("#selectjs_seccion", true, "Secciones", 5);
        select2Estruct("#selectjs_grupos", true, "Grupos", 6);
        select2Estruct("#selectjs_sucursal", true, "Sucursales", 7);
        $("#selectjs_seccion").prop("disabled", true);

    });

    select2Estruct("#selectjs_personal", true, "Legajos", 8);
    // on hide collapse ·Filtros
    $('#Filtros').on('hidden.bs.collapse', function () {
        $('#mostrarFiltros').attr('aria-label', 'Mostrar Filtros');
    });

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
        // getSemanal();
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
    });

    const ExportarBtn = document.getElementById('ExportarBtn');
    ExportarBtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $.notifyClose();
        notify('Aguarde por favor...', 'dark', 0, 'right');
        exportarHorarios();
    });

    const exportarHorarios = () => {
        const valueTipo = $('input[name="Tipo"]:checked').val() || '0';
        const url = "/" + homehost + "/app-data/asignados/exportar";
        const FechaDesde = $('#_dr').data('daterangepicker').startDate.format('YYYY-MM-DD');
        const FechaHasta = $('#_dr').data('daterangepicker').endDate.format('YYYY-MM-DD');
        const Tipo = valueTipo == '2' ? [0, 1] : [valueTipo];
        const Legajos = $("#selectjs_personal").val() || [];
        const extension = $('input[name="extension"]:checked').val() || 'xls';
        const Egreso = 1;
        const AgruparPor = 'Legajo';
        const SinHorarios = 2;
        const Empresas = $("#selectjs_empresa").val();
        const Plantas = $("#selectjs_planta").val();
        const Convenios = $("#selectjs_convenio").val();
        const Sectores = $("#selectjs_sector").val();
        const Secciones = $("#selectjs_seccion").val();
        const Grupos = $("#selectjs_grupos").val();
        const Sucursales = $("#selectjs_sucursal").val();
        const PlanillaSemanal = document.getElementById('planilla-semanal').checked ? 1 : 0;

        const payload = {
            FechaDesde,
            FechaHasta,
            Tipo,
            Legajos,
            Egreso,
            AgruparPor,
            SinHorarios,
            Empresas,
            Plantas,
            Convenios,
            Sectores,
            Secciones,
            Grupos,
            Sucursales,
            extension,
            PlanillaSemanal
        };

        $.ajax({
            url,
            method: "POST",
            data: JSON.stringify(payload),
            contentType: "application/json",
            success: function (response) {

                if (response.status === 'error') {
                    $.notifyClose();
                    const mensajeError = response.mensaje || 'Ocurrió un error al generar el reporte.';
                    notify(mensajeError, 'danger', 5000, 'right');
                    return;
                }

                $.notifyClose();
                const archivo = response.archivo ?? '';

                if (!archivo) {
                    throw new Error('No se pudo generar el archivo.');
                }

                const bannerDownload = `
                    <div class="d-flex flex-column">
                        <div class="font-weight-bold">Reporte generado.</div>
                        <a href="${DIR_APP_DATA}/${archivo}" class="btn btn-custom px-2 btn-sm mt-2 font08 download" target="_blank" download>
                        <div class="d-flex align-items-center w-100 justify-content-center" style="gap:5px">
                            <span>Descargar</span> <i class="bi bi-file-earmark-arrow-down font1"></i>
                        </div>
                        </a>
                    </div>
                `;
                notify(bannerDownload, 'warning', 0, 'right');

                const download = document.querySelector('.download');

                if (download) {
                    download.addEventListener('click', (e) => {
                        $.notifyClose();
                    });
                }

            },
            error: function (error) {
                const mensajeError = error.responseJSON?.message || 'Ocurrió un error al generar el reporte.';
                $.notifyClose();
                notify(mensajeError, 'danger', 5000, 'right');

            }
        });
    }

    // detectar el cambio del type radio input[name="extension"] y si el value es pdf  quitar el hidden de .div-planilla
    $('input[name="extension"]').on('change', function (e) {
        const value = $(this).val();
        const divPlanilla = document.querySelector('.div-planilla');
        divPlanilla.style.display = (value === 'pdf') ? 'block' : 'none';
    });
});

