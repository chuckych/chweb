/** Select */
$(function () {
    const homehost = $("#_homehost").val();
    const flag = new Date().getTime();

    const dateRange = async () => {

        const fechaActual = new Date(new Date().setDate(new Date().getDate() + 1));
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
            endDate: fechaActual,
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

        // Función auxiliar para crear el checkbox
        const createCheckbox = () => {
            return $(`
            <div class="d-inline-flex justify-content-between align-items-center w-100 ${disabled}">
                <div class="custom-control custom-checkbox w-100">
                    <input type="checkbox" id="checkbox_${item.id}" 
                           class="custom-control-input select2-results__option-checkbox" 
                           ${checked} ${disabled} />
                    <label class="custom-control-label w-100" for="checkbox_${item.id}">
                        ${createLabelContent()}
                    </label>
                </div>
                <div class="font08">(${cantidad})</div>
            </div>
        `);
        };

        // Contenido del label según tipo de estructura
        const createLabelContent = () => {
            const sectorText = getSectorText();

            if (estructura === 'personal') {
                return `
                <div>
                    <span class="font08">${item.id}</span><br>
                    <span class="font09">${item.text}</span>
                </div>
            `;
            }

            return `
            <div class="d-flex flex-row align-items-center" style="margin-top:3px;gap:5px;">
                <div class="font08">(${item.id2})</div>
                <div class="font09">${item.text}</div>
            </div>
            ${sectorText}
        `;
        };

        // Texto del sector si aplica
        const getSectorText = () => {
            if (estructura === 'secciones' && item.Sector) {
                return `<span class="font07">Sector: (${item.CodSect}) ${item.Sector}</span>`;
            }
            return '';
        };

        return createCheckbox();
    };

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
            const isChecked = $(this).find(`.select2-results__option[aria-selected="true"]`).length > 0;
            // Desmarcar todos los checkboxes
            // $(this).find('.select2-results__option-checkbox').prop('checked', false);
            // Marcar el checkbox del elemento seleccionado
            // $(this).find(`.select2-results__option-checkbox`).filter(`[aria-selected="true"]`).prop('checked', true);
            // getPersonal();
        }).on('select2:unselect', function (e) {
            const id = e.params.data.id;
            // Desmarcar el checkbox del elemento no seleccionado
            // $(this).find(`.select2-results__option-checkbox`).filter(`[aria-selected="false"]`).prop('checked', false);
        }).on('select2:close', function () {
            getPersonal();
            // const selectedValues = $(this).val() || [];
            // // Actualizar los checkboxes al cerrar el select2
            // $(this).find('.select2-results__option-checkbox').each(function () {
            //     const checkbox = $(this);
            //     const option = checkbox.closest('.select2-results__option');
            //     if (selectedValues.includes(option.data('data').id)) {
            //         checkbox.prop('checked', true);
            //     } else {
            //         checkbox.prop('checked', false);
            //     }
            // });
        }).on('select2:opening', function () {
            // Desmarcar todos los checkboxes al abrir el select2
            // $(this).find('.select2-results__option-checkbox').prop('checked', false);
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
                    <'col-12'<'d-inline-flex w-100 justify-content-between'<l><f>>>
                    <'col-12'<'border radius p-2 table-responsive'<t>>>
                    <'col-12'<'d-inline-flex w-100 justify-content-between'<i><p>>>
                >
            `,
            ajax: {
                url: url,
                type: "POST",
                "data": function (data) {
                    const tipo = $('input[name="Tipo"]:checked').val() || 0;
                    data.descripcion = '';
                    data.strict = 1;
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
                },
                error: function () {
                    $("#tabla_processing").css("display", "none");
                },
            },
            columns: [
                {
                    data: 'Cod', className: 'py-2 align-middle', targets: '', title: 'Legajo',
                    "render": function (data, type, row, meta) {
                        return data;
                    },
                },
                {
                    data: 'Descripcion', className: ' align-middle', targets: '', title: 'Nombre',
                    "render": function (data, type, row, meta) {
                        return data;
                    },
                },
                {
                    data: '', className: ' align-middle w-100', targets: '', title: '',
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
                }, 0);
            }
        });
    }

    getPersonal();

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

    // axios post /procesar 
    $("#btnProcesar").on("click", async function (e) {
        e.preventDefault();
        e.stopPropagation();

        // deshabilitar el botón para evitar múltiples clics
        $(this).prop('disabled', true);

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
            } else {
                alert('Error al procesar');
            }
        } catch (error) {
            console.error('Error en la solicitud:', error);
            // alert('Ocurrió un error al procesar los datos.');
        } finally {
            // volver a habilitar el botón
            $(this).prop('disabled', false);
        }
    });
});

