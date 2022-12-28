// $(function () {
document.addEventListener('DOMContentLoaded', (e) => {
    // $(".Filtros").prop('disabled', true);
    $.get('/' + $('#_homehost').val() + '/status_ws.php', {
        status: 'ws',
    }).done(function (data) {
        $.notifyClose();
        notify(data.Mensaje, 'info', 2000, 'right')
    });

    function ActualizaTablas() {
        loadingTable('#GetPersonal')
        loadingTable('#tableHorarios')
        $('#GetPersonal').DataTable().ajax.reload();
    };
    $('#Filtros').on('shown.bs.modal', function () {
        CheckSesion()
    });
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
            url: "/" + $("#_homehost").val() + "/informes/horasign/GetPersonal.php",
            type: "POST",
            "data": function (data) {
                // data._l = $("#_l").val();
                data.time = $("#time").val();
                data._drhorarios = document.getElementById("_drHorarios").value;
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
                // data._dr = $("#_dr").val();
                // data.FicFalta = $('#FicFalta').val();
                // data.onlyReg = $("#onlyReg:checked").val() ?? '';
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

    // $("#trash_all").on("click", function () {
    //     CheckSesion()
    //     $('#Filtros').modal('show')
    //     LimpiarFiltros()
    //     $('#Filtros').modal('hide')
    //     ActualizaTablas()
    // });

    // $(document).on("click", ".numlega", function (e) {
    //     CheckSesion()
    //     $('#Per2').val(null)
    //     $(this).addClass('d-none')
    //     $('#Per2').removeClass('d-none')
    //     $('#Per2').focus();
    // });
    getHTML('modal_Filtros.php', '#modales')
});