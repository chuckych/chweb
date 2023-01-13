document.addEventListener('DOMContentLoaded', (e) => {

    function ActualizaTablas() {
        loadingTable('#GetPersonal')
        loadingTable('#tableHorarios')
        $('#GetPersonal').DataTable().ajax.reload();
    };
    let loadingTable = (selectortable) => {
        $(selectortable + ' td div').addClass('bg-light text-light')
        $(selectortable + ' td img').addClass('invisible')
        $(selectortable + ' td i').addClass('invisible')
        $(selectortable + ' td span').addClass('invisible')
    }
    let map = { 17: false, 18: false, 32: false, 16: false, 39: false, 37: false, 13: false, 27: false };

    $(document).keydown(function (e) {
        if (e.keyCode in map) {
            map[e.keyCode] = true;
            if (map[32]) { /** Barra espaciadora */
                $('#Filtros').modal('show');
            }
        }
        if (e.keyCode in map) {
            map[e.keyCode] = true;
            if (map[27]) { /** Esc */
                $('#Filtros').modal('hide');
            }
        }

        // if (e.keyCode in map) {
        //     map[e.keyCode] = true;
        //     if (map[13]) { /** Enter */
        //         ActualizaTablas()
        //     }
        // }
        if (e.keyCode in map) {
            map[e.keyCode] = true;
            if (map[39]) { /** Flecha derecha */
                if ($("#Visualizar").is(":checked")) {
                    $('#GetFechas').DataTable().page('next').draw('page');
                } else {
                    $('#GetPersonal').DataTable().page('next').draw('page');
                };
            }
        }
        if (e.keyCode in map) {
            map[e.keyCode] = true;
            if (map[37]) { /** Flecha izquierda */
                if ($("#Visualizar").is(":checked")) {
                    $('#GetFechas').DataTable().page('previous').draw('page');
                } else {
                    $('#GetPersonal').DataTable().page('previous').draw('page');
                };

            }
        }
    }).keyup(function (e) {
        if (e.keyCode in map) {
            map[e.keyCode] = false;
        }
    });

    $('#_drHorarios').daterangepicker({
        singleDatePicker: false,
        showDropdowns: true,
        showWeekNumbers: false,
        autoUpdateInput: true,
        opens: "center",
        drops: "down",
        startDate: moment().day(1),
        endDate: moment().day(7),
        autoApply: false,
        alwaysShowCalendars: true,
        linkedCalendars: false,
        buttonClasses: "btn btn-sm fontq",
        applyButtonClasses: "btn-custom fw4 px-3 opa8",
        cancelClass: "btn-link fw4 text-gris",
        ranges: {
            // 'Hoy': [moment(), moment()],
            // 'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Esta semana': [moment().day(1), moment().day(7)],
            'Semana anterior': [moment().subtract(1, 'week').day(1), moment().subtract(1, 'week').day(7)],
            'Próxima semana': [moment().add(1, 'week').day(1), moment().add(1, 'week').day(7)],
            'Este mes': [moment().startOf('month'), moment().endOf('month')],
            'Mes anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Próximo mes': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
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
    $('#_drHorarios').on('apply.daterangepicker', function (ev, picker) {
        ActualizaTablas()
    });

    $('#Tipo').css({ "width": "200px" });
    $('.form-control').css({ "width": "100%" });

    function Select2Estruct(selector, multiple, placeholder, estruct, url, parent) {
        let opt2 = { MinLength: "0", SelClose: false, MaxInpLength: "10", delay: "250", allowClear: true };
        $(selector).select2({
            multiple: multiple,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: parent,
            placeholder: placeholder,
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 5,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            templateResult: function (data) {
                let $result = $(data.html);
                return $result;
            },
            language: {
                noResults: function () {
                    return 'No hay resultados . . .'
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
                }
            },
            ajax: {
                url: url,
                dataType: "json",
                type: "POST",
                delay: 500,
                cache: false,
                data: function (params) {
                    return {
                        q: params.term,
                        estruct: estruct,
                        Per: $("#Per").val(),
                        time: $("#time").val(),
                        Tipo: $("#Tipo").val(),
                        Emp: $("#Emp").val(),
                        Plan: $("#Plan").val(),
                        Sect: $("#Sect").val(),
                        Sec2: $("#Sec2").val(),
                        Grup: $("#Grup").val(),
                        Sucur: $("#Sucur").val(),
                        Tare: $("#Tare").val(),
                        Conv: $("#Conv").val(),
                        Regla: $("#Regla").val(),
                    }
                },
                processResults: function (data, page) {
                    return {
                        results:
                            data.map(function (item) {
                                if (item.Estruct == 'Lega') {
                                    return {
                                        id: item.Cod,
                                        text: `${item.Desc}`,
                                        html: `<div class="d-flex justify-content-start">
                                                        <div>${item.Cod}&nbsp;-&nbsp;</div>
                                                        <div>${item.Desc}</div>
                                                    </div>`,
                                    };
                                } else if (item.Estruct == 'Sec2') {
                                    return {
                                        id: item.Sect + item.Cod,
                                        text: `${item.Desc}`,
                                        html: `<div title="Sector: ${item.SectDesc}" class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex">
                                                            <div>${item.Cod}&nbsp;-&nbsp;</div>
                                                            <div> ${item.Desc}</div>
                                                        </div>
                                                        <div class="badge badge-light">${item.Count}</div>
                                                    </div>
                                                    `,
                                    };
                                } else if (item.Estruct == 'Tipo') {
                                    return {
                                        id: item.Cod,
                                        text: `${item.Desc}`,
                                        html: `<div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex">
                                                            <div> ${item.Desc}</div>
                                                        </div>
                                                        <div class="badge badge-light">${item.Count}</div>
                                                    </div>
                                                    `,
                                    };
                                } else {
                                    return {
                                        id: item.Cod,
                                        text: `${item.Desc}`,
                                        html: `<div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex">
                                                            <div>${item.Cod}&nbsp;-&nbsp;</div>
                                                            <div> ${item.Desc}</div>
                                                        </div>
                                                        <div class="badge badge-light">${item.Count}</div>
                                                    </div>
                                                    `,
                                    };
                                }
                            })
                    };
                },
            },
        });
    }

    $('#Filtros').on('shown.bs.modal', function (e) {
        CheckSesion()
        $.notifyClose();
        let url = "/" + $("#_homehost").val() + "/informes/horasign/getEstruct.php";
        let Empr = Select2Estruct(".selectjs_empresa", true, "Empresas", "Empr", url, $('#Filtros'));
        let Plan = Select2Estruct(".selectjs_plantas", true, "Plantas", "Plan", url, $('#Filtros'));
        let Sect = Select2Estruct(".selectjs_sectores", true, "Sectores", "Sect", url, $('#Filtros'));
        let Sec2 = Select2Estruct(".select_seccion", true, "Secciones", "Sec2", url, $('#Filtros'));
        let Grup = Select2Estruct(".selectjs_grupos", true, "Grupos", "Grup", url, $('#Filtros'));
        let Sucu = Select2Estruct(".selectjs_sucursal", true, "Sucursales", "Sucu", url, $('#Filtros'));
        let Lega = Select2Estruct(".selectjs_personal", true, "Legajos", "Lega", url, $('#Filtros'));
        let Tipo = Select2Estruct(".selectjs_tipoper", false, "Tipo de Personal", "Tipo", url, $('#Filtros'));
        let Tare = Select2Estruct(".selectjs_tareprod", true, "Taras de Producción", "Tare", url, $('#Filtros'));
        let Conv = Select2Estruct(".selectjs_conv", true, "Convenio", "Conv", url, $('#Filtros'));
        let Regla = Select2Estruct(".selectjs_regla", true, "Regla de control", "Regla", url, $('#Filtros'));

        $('.selectjs_sectores').on('select2:select', function (e) {
            e.preventDefault()
            $(".select_seccion").prop("disabled", false);
            $('.select_seccion').val(null).trigger('change');
        });
        $('.selectjs_sectores').on('select2:unselecting', function (e) {
            $(".select_seccion").prop("disabled", true);
            $('.select_seccion').val(null).trigger('change');
            $('.selectjs_sectores').val(null).trigger('change');
        });
        $('.selectjs_personal').on('select2:select', function (e) {
        });
    });

    function LimpiarFiltros() {
        $('.selectjs_plantas').val(null).trigger("change");
        $('.selectjs_empresa').val(null).trigger("change");
        $('.selectjs_sectores').val(null).trigger("change");
        $('.select_seccion').val(null).trigger("change");
        $(".select_seccion").prop("disabled", true);
        $('.selectjs_grupos').val(null).trigger("change");
        $('.selectjs_sucursal').val(null).trigger("change");
        $('.selectjs_personal').val(null).trigger("change");
        $('.selectjs_tipoper').val(null).trigger("change");
        $('.selectjs_tareprod').val(null).trigger("change");
        $('.selectjs_conv').val(null).trigger("change");
        $('.selectjs_regla').val(null).trigger("change");
    }

    $("#trash_all").on("click", function () {
        $('#Filtros').modal('show')
        LimpiarFiltros()
        $('#Filtros').modal('hide')
    });

    $("#trash_allIn").on("click", function () {
        LimpiarFiltros()
    });
    let GetPersonal = $('#GetPersonal').DataTable({
        pagingType: "full",
        lengthMenu: [[1], [1]],
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        dom: `
    <"row"
        <"col-12 d-flex justify-content-end p-0"
            <"d-flex justify-content-end align-items-end">
            <"d-inline-flex align-items-center"<"mt-2 mt-sm-1"t>
                <"d-none d-sm-block ml-1"p>
            >   
        >
        <"col-12"
            <"d-block d-sm-none mt-n2"p>
            <"d-flex justify-content-end align-items-end mt-n4 p-0"i>
        >
    >
        `,
        createdRow: function (row, data, dataIndex,) {
            $(row).addClass('animate__animated animate__fadeIn');
        },

        ajax: {
            url: "/" + $("#_homehost").val() + "/informes/horasign/getPersonal.php",
            type: "POST",
            "data": function (data) {
                data.time = getSelectorVal("#time");
                data._drhorarios = getSelectorVal("#_drHorarios");
                data.Per = $("#Per").val();
                data.Tipo = $("#Tipo").val();
                data.Emp = $("#Emp").val();
                data.Plan = $("#Plan").val();
                data.Sect = $("#Sect").val();
                data.Sec2 = $("#Sec2").val();
                data.Grup = $("#Grup").val();
                data.Sucur = $("#Sucur").val();
                data.Conv = $("#Conv").val();
                data.Tare = $("#Tare").val();
                data.Tipo = $("#Tipo").val();
                data.Regla = $("#Regla").val();
            },

            error: function () {
                $("#GetPersonal_processing").css("display", "none");
            },
        },
        columns: [
            /** Columna Legajo */
            {
                className: 'w80 px-3 border fw4 bg-light radius pers_legajo', targets: 'pers_legajo',
                "render": function (data, type, row, meta) {
                    let datacol = '<input type="text" id="Per2" class="d-none border-0 border bg-white form-control-sm mr-2 w100 text-center" style="height: 20px;"><span class="p-0 fontq fw4">' + row.pers_legajo + '</span><input type="hidden" id="_l" value=' + row.pers_legajo + '>'

                    return '<div>' + datacol + '</div>';
                },
            },
            /** Columna Nombre */
            {
                className: 'w300 px-3 border border-left-0 fw4 bg-light radius', targets: 'pers_nombre',
                "render": function (data, type, row, meta) {
                    let datacol = row.pers_nombre
                    return '<div class="d-flex align-items-center justify-content-start" style="margin-top:1px"><span>' + datacol + '</span></div>';
                },
            }
        ],
        paging: true,
        responsive: false,
        info: true,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanishPag.json",
        },
    });

    GetPersonal.on('draw.dt', function (e, settings, json) {
        let newData = settings.json.data2.data
        let Mensaje = settings.json.data2.Mensaje
        $(".dataTables_info").addClass('text-secondary');
        $("#GetPersonal thead").remove();
        $("#GetPersonal").removeClass('invisible');
        if (settings.iDraw == 1) {
            const tableHorarios = $('#tableHorarios').DataTable({
                // pagingType: "full",
                lengthMenu: [[7, 14, 21, 31], [7, 14, 21, 31]],
                sProcessing: true,
                serverSide: false,
                deferRender: true,
                stateSave: true,
                data: newData, // data horarios
                createdRow: function (row, data, dataIndex,) {
                    $(row).addClass('animate__animated animate__fadeIn fonth');
                    // console.log(data);
                    if (data.Laboral == 'No') {
                        $('td', row).addClass('text-secondary');
                    }
                    if (data.Feriado == 'Sí') {
                        $('td', row).addClass('text-secondary');
                    }
                },
                dom: "<'row'" +
                    "<'col-12 col-sm-6 d-flex align-items-start'lf><'col-12 col-sm-6 d-inline-flex align-items-start justify-content-end'>>" +
                    "<'row '<'col-12 table-responsive max-h-400't>>" +
                    "<'row '<'col-12 d-flex align-items-center justify-content-between'<i><p>>>",
                columns: [
                    /** Columna Fecha */
                    {
                        className: '', targets: 'Fecha', title: 'FECHA',
                        "render": function (data, type, row, meta) {
                            let datacol = '<span>' + row.Fecha + '</span>'
                            return '<div>' + datacol + '</div>';
                        },
                    },
                    /** Columna Dia */
                    {
                        className: '', targets: 'Dia', title: '',
                        "render": function (data, type, row, meta) {
                            let datacol = '<span>' + row.Dia + '</span>'
                            return '<div>' + datacol + '</div>';
                        },
                    },
                    /** Columna Desde Hasta */
                    {
                        className: '', targets: '', title: 'HORARIO',
                        "render": function (data, type, row, meta) {
                            let datacol = row.Desde + ' a ' + row.Hasta
                            if (row.Laboral == 'No') {
                                datacol = '<span>Franco</span>'
                            }
                            return '<div>' + datacol + '</div>';
                        },
                    },
                    /** Columna Horario */
                    {
                        className: '', targets: 'Horario', title: 'DESCRIPCION',
                        "render": function (data, type, row, meta) {
                            let datacol = '<span>' + row.Horario + '</span>'
                            if (row.Horario == 'Sin datos') {
                                datacol = '<span>' + row.TipoAsign + '</span>'
                            }
                            return '<div>' + datacol + '</div>';
                        },
                    },
                    /** Columna HorarioID */
                    {
                        className: '', targets: 'HorarioID', title: 'ID',
                        "render": function (data, type, row, meta) {
                            let datacol = '<span>' + row.HorarioID + '</span>'
                            if (row.HorarioID == 'Sin datos') {
                                datacol = '<span>' + row.TipoAsign + '</span>'
                            }
                            return '<div>' + datacol + '</div>';
                        },
                    },
                    /** Columna TipoAsign */
                    {
                        className: '', targets: 'TipoAsign', title: 'ASIGNACION',
                        "render": function (data, type, row, meta) {
                            let datacol = '<span>' + row.TipoAsign + '</span>'
                            return '<div>' + datacol + '</div>';
                        },
                    },
                    /** Columna Turno */
                    {
                        className: '', targets: 'Turno', title: 'TURNO',
                        "render": function (data, type, row, meta) {
                            let datacol = '<span>' + row.Turno + '</span>'
                            return '<div>' + datacol + '</div>';
                        },
                    },
                    /** Columna Feriado */
                    {
                        className: 'w-100', targets: 'Feriado', title: '',
                        "render": function (data, type, row, meta) {
                            let datacol = '<span></span>'
                            if (row.Feriado == 'Sí') {
                                datacol = '<span>Feriado</span>'
                            }
                            return '<div>' + datacol + '</div>';
                        },
                    },
                ],
                paging: true,
                responsive: false,
                info: true,
                ordering: false,
                language: {
                    "url": "../../js/DataTableSpanishShort2.json",
                }
            });
            tableHorarios.on('init.dt', function (e, settings, json) {
                $("#tableHorarios").removeClass('invisible');
                $('#tableHorarios_filter input').attr('placeholder', 'Buscar')
            });
            tableHorarios.on('draw.dt', function (e, settings, json) {
                $(".pagination").addClass('d-print-none');
                $(".dataTables_info").addClass('d-print-none');
                $(".custom-select").addClass('d-print-none');
                $("input[type='search']").addClass('d-print-none');
            });
        }
        if (settings.iDraw > 1) {
            $('#tableHorarios').DataTable().clear().rows.add(newData).draw();
        }
        if (Mensaje != 'OK' && Mensaje != '') {
            $.notifyClose();
            notify(`<b>${Mensaje}</b>`, 'danger', 2000, 'right')
        }
        Mensaje = ''
        $("#btnExcel").prop('disabled', false)
        $(".Filtros").prop('disabled', false)
    });

    GetPersonal.on('page.dt', function (e, settings, json) {
        loadingTable('#GetPersonal')
        loadingTable('#tableHorarios')
    });

    $("#Refresh").on("click", function (e) {
        e.preventDefault();
        CheckSesion()
        ActualizaTablas()
    });

    /** Select */
    // $(function () {
    $('#Tipo').css({ "width": "200px" });
    $('.form-control').css({ "width": "100%" });
    // });
    onOpenSelect2()
    $('#Filtros').on('hidden.bs.modal', function (e) {
        CheckSesion()
        ActualizaTablas()
    });
});