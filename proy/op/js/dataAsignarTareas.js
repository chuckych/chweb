$(function () {
    "use strict"; // Start of use strict
    const tableAsignasTareas = $("#tableAsignasTareas").dataTable({ //inicializar datatable
        lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]], //mostrar cantidad de registros
        bProcessing: true,
        serverSide: false,
        deferRender: true,
        stateSave: true,
        dom:
            "<'row '<'col-12 mt-2 flex-center-between'<''l><'btnTableTareas'>>>" +
            "<'row '<'col-12 table-responsive mt-2't>>" +
            "<'row '<'col-12 col-sm-5'i><'col-12 col-sm-7 flex-center-end'p>>",
        ajax: {
            url: `data/getUsersFic.php`,
            type: "POST",
            dataType: "json",
            data: function (data) {
                data.sinTar = $("input[name=sinTar]:checked").val();
                data.presentes = $("input[name=presentes]:checked").val();
                data.FiltroAsignTarFechas = $("#FiltroAsignTarFechas").val();
            },
            error: function () {
                $("#tableAsignasTareas").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass("fadeIn");
        },
        columns: [
            /** Nombre */
            {
                className: "align-middle w300 text-wrap",
                targets: "",
                title: "<span class='ms-1'>Nombre</span>",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                            <div class="">${row.arrUser.nombre}</div>
                            <div class="text-mutted font08">${row.arrUser.legajo}</div>
                        `;
                    return '<div class="w300">' + datacol + '</div>';
                }
            },
            /** Tareas */
            {
                className: "text-center align-middle",
                targets: "",
                title: "Tareas",
                render: function (data, type, row, meta) {

                    let icon = ''
                    if (row.arrTar.length > 0) {
                        icon = `<span class="badge bg-green-lt viewTareaAssign pointer" title="Ver detalle">${row.arrTar.length}</span>`
                    } else {
                        icon = `<span class="badge bg-red-lt">${row.arrTar.length}</span>`
                    }
                    return '<div class="">' + icon + '</div>';
                }
            },
            /** Presente */
            {
                className: "text-center align-middle",
                targets: "",
                title: "Presente",
                render: function (data, type, row, meta) {
                    let icon = ''
                    let ingreso = ''
                    let title = ''
                    if (row.arrFic) {
                        ingreso = row.arrFic.Fich[0].Hora
                        title = 'data-titler="Ingreso: ' + ingreso + '"'
                    }
                    if (row.arrFic) {
                        icon = '<span class="badge bg-green-lt"><i class="bi bi-check-lg"></i></span>'
                    } else {
                        icon = '<span class="badge bg-red-lt"><i class="bi bi-x-lg"></i></span>'
                    }
                    return '<div ' + title + '>' + icon + '</div>';
                }
            },
            /** Acciones */
            {
                className: "align-middle w-100 text-end",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                            <button data-titlel="Asignar Tarea" type="button" class="shadow-sm btn btn-sm btn-teal h30 w30 assignTar"><i class="bi bi-plus-lg"></i></button>
                        `;
                    return '<div class="datacol viewTar pointer">' + datacol + '</div>';
                }
            },
        ],
        searchDelay: 500,
        paging: true,
        searching: true,
        info: true,
        ordering: 0,
        language: {
            url: `../js/DataTableSpanishShort2.json?${Date.now()}`
        }
    });
    tableAsignasTareas.on("init.dt", function (e, settings) { // Cuando se inicializa la tabla
        let idTable = `#${e.target.id}`; // Se obtiene el id de la tabla
        let lengthMenu = $(`${idTable}_length select`); // Se obtiene el select del lengthMenu
        $(lengthMenu).css("height", "48px"); // Se agrega la clase h40 height: 50px
        $(lengthMenu).css("margin-top", "4px"); // Se agrega la clase h40 height: 50px
        let filterInput = $(`${idTable}_filter input`); // Se obtiene el input del filtro
        $(filterInput).attr({ placeholder: "Buscar" }).addClass("p-2 pe-3 text-end w100").mask('0000');
        //$(`${idTable}_filter`).append("<div class=''><select class='selectTar form-control w300'></select></div>").addClass('w200'); // Se agrega la clase flex-center-center
        // Se agrega placeholder al input y se agrega clase p-3.
        let opt2 = {
            MinLength: "0",
            SelClose: false,
            MaxInpLength: "10",
            delay: "250",
            allowClear: true,
        };
        $(idTable).removeClass("invisible"); // Se remueve la clase invisible de la tabla
        $('.btnTableTareas').html(`
            <div class="flex-center-center">
                <div class="border p-1 d-flex me-1 filterTar">
                    <label class="form-selectgroup-item">
                        <input type="checkbox" name="sinTar" id="sinTar" value="1"
                            class="form-selectgroup-input" checked>
                        <span class="form-selectgroup-label flex-center-center h40 border-0">
                            <span class="font08">Sin Tareas</span>
                        </span>
                    </label>
                    <label class="form-selectgroup-item">
                        <input type="checkbox" name="presentes" id="presentes" value="1"
                            class="form-selectgroup-input" checked>
                        <span class="form-selectgroup-label flex-center-center h40 border-0">
                            <span class="font08">Presentes</span>
                        </span>
                    </label>
                </div>
                <label class="border p-1 me-1 d-sm-block">
                    <button type="button" class="btn btn-blue flex-center-center h40 border-0 backTareas">
                    <i class="bi bi-list-task me-1"></i><span class="font08 me-1">Tareas</span>
                    </button>
                </label>
                <input readonly type="text" class="me-1 form-control text-center px-5 bg-white h50 mt-2 mt-sm-0" id="FiltroAsignTarFechas" name="FiltroAsignTarFechas" placeholder="Filtrar Fecha" style="width:220px">
            </div>
        `)
        setTimeout(() => {
            $('#FiltroAsignTarFechas').daterangepicker({
                singleDatePicker: true,
                showDropdowns: false,
                showWeekNumbers: false,
                // autoUpdateInput: true,
                maxDate: moment(),
                opens: "left",
                drops: "down",
                autoApply: true,
                linkedCalendars: false,
                ranges: {
                    'Hoy': [moment(), moment()],
                    'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')]
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
                    "monthNames": ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                    firstDay: 1,
                    alwaysShowCalendars: true,
                    applyButtonClasses: "btn btn-tabler",
                },
            });
            // $('.ranges').remove()
            $("[data-range-key='Personalizado']").remove()
            $("#tableAsignasTareas").DataTable().ajax.reload();
            $('#FiltroAsignTarFechas').on('apply.daterangepicker', function (ev, picker) {
                $("#tableAsignasTareas").DataTable().ajax.reload(); // Se recarga la tabla
            });
            $('.filterTar input').on('change', function (e) {
                $("#tableAsignasTareas").DataTable().ajax.reload(); // Se recarga la tabla
            });
        }, 1000);
    });
    tableAsignasTareas.on("draw.dt", function (e, settings) {
        let json = ((settings.json));
        if (settings.aoData.length == 0) {
            $('.notFound2').remove();
            $("#tableAsignasTareas").append(`<div style="min-height:250px" class="notFound2 shadow-sm w-100 bg-muted-lt flex-center-center"><h1>No se encontraron resultados.</h1></div>`);
            $("#tableAsignasTareas tbody").hide();
            $("#tableAsignasTareas thead").hide();
            $(".dataTables_info").hide();
            $(".dataTables_paginate").hide();
        } else {
            $(".notFound2").remove()
            $("#tableAsignasTareas tbody").show();
            $("#tableAsignasTareas thead").show();
            $(".dataTables_info").show();
            $(".dataTables_paginate").show();
        }
        let idTable = "#" + e.target.id;
        $(idTable + ' thead th').addClass("py-3");
        $(idTable + ' tbody tr').hover(function () {
            $(this).find('.btnDots').toggleClass("invisible");
        });
        setTimeout(() => {
            $(idTable + " tr").removeClass("fadeIn");
        }, 500);
        $("#tableAsignasTareas").css("display", "block");
    });
    tableAsignasTareas.on("xhr.dt", function (e, settings, json) {
        tableAsignasTareas.off('xhr.dt');
    });
    $.fn.DataTable.ext.pager.numbers_length = 5; // Se agrega el numero de paginas a mostrar en el paginador

    const asignarTareas = () => {
        $(document).on("click", ".asignarTareas", function (e) { // Se agrega el evento click al boton asignarTareas
            e.preventDefault()
            $('#containerTableTareas').hide()
            $('#containerTableAssignTareas').show()
            $("#mainTitleBar").html(('Asignar Tareas')); // Title
        });
        $(document).on("click", ".backTareas", function (e) { // Se agrega el evento click al boton asignarTareas
            e.preventDefault()
            $('#containerTableAssignTareas').hide()
            $('#containerTableTareas').show()
            $("#mainTitleBar").html(('Tareas')); // Title
        });
    }
    asignarTareas()

    $(document).on("click", ".viewTareaAssign", function (e) {
        e.preventDefault();
        $.notifyClose() // Se cierra el notify
        let dataRow = $('#tableAsignasTareas').DataTable().row($(this).parents("tr")).data(); // Se obtiene la fila seleccionada en la tabla
        // console.log(dataRow);
        fetch(`op/tarModal.php`) // Se hace la peticion ajax para obtener el modal
            .then(response => response.text()) // Se obtiene la respuesta
            .then(data => {  // Se obtiene el html del modal
                $("#modales").html(data); // Se agrega el html al modal
                $("#modales .form-control").attr("autocomplete", "off").prop('disabled', true).addClass('bg-white'); // Se agrega el atributo autocomplete
                let tarModal = new bootstrap.Modal(document.getElementById("tarModal"), { keyboard: true }); // Se inicializa el modal
                tarModal.show(); // Se muestra el modal
                $("#tarModal .modal-body").html('')
                $("#tarModal .modal-footer").addClass('bg-white pt-2')
                $("#tarModal .modal-footer button").removeClass('me-auto')
                $("#tarModal .modal-title").html(`
                    <div class="py-3">
                        <div>Tareas del ${formatDate(dataRow.date + ' 00:00')}</div>
                        <div class="lead text-mutted">${dataRow.arrUser.nombre}</div>
                    </div>
                `)
                // $("#tarModal .modal-body").addClass('bg-white')
                $("#tarModal .tarSubmit").remove('')
                $('#tarModal .modal-body').append(`
                <div class="card">
                <div class="table-responsive m-0">
                    <table class="table text-nowrap table-vcenter card-table w-100">
                        <thead>
                            <tr>
                                <th class="w50">#</th>
                                <th>ID Tarea</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Duración</th>
                                <th>Proyecto</th>
                            </tr>
                        </thead>
                        <tbody class="font08"></tbody>
                    </table>
                </div>
                </div>
                `)
                $.each(dataRow.arrTar, function (index, v) {
                    let TareFin = formatHour(v.TareFin) ? formatHour(v.TareFin) : '-'
                    let titleTareFin = (v.TareFin != "0000-00-00 00:00:00") ? `title="${formatDate(v.TareFin)}"` : ''
                    $('#tarModal .modal-body tbody').append(`
                        <tr>
                            <td class="w50">${index + 1}</td>
                            <td>${v.TareID}</td>
                            <td title="${formatDate(v.TareIni)}">${formatHour(v.TareIni)}</td>
                            <td ${titleTareFin}>${TareFin}</td>
                            <td>${formatDuracion(v.TareHorHoras)}</td>
                            <td>${(v.ProyNom)}</td>
                        </tr>
                    `)
                })
            });
        e.stopImmediatePropagation();
        setTimeout(() => {
            document.getElementById('tarModal').addEventListener('hidden.bs.modal', function (event) {
                $("#modales").html('');
            });
        }, 100);
    });

    $("#tableAsignasTareas tbody").on("click", ".assignTar", function (e) { // Se agrega el evento click al boton assignTar
        // $("#tableAsignasTareas").DataTable().ajax.reload(null, false); // Se recarga la tabla
        e.preventDefault();
        $.notifyClose() // Se cierra el notify
        let dataRow = $("#tableAsignasTareas").DataTable().row($(this).parents("tr")).data(); // Se obtiene la fila seleccionada en la tabla
        // console.log(dataRow);
        // console.log(dataRow);
        fetch(`op/tarModal.php?${Date.now()}`) // Se hace la peticion ajax para obtener el modal
            .then(response => response.text()) // Se obtiene la respuesta
            .then(data => {  // Se obtiene el html del modal
                // console.log(HtmlEncode(dataRow.ProyObs));
                $("#modales").html(data); // Se agrega el html al modal
                $("#modales .form-control").attr("autocomplete", "off"); // Se agrega el atributo autocomplete
                let tarModal = new bootstrap.Modal(document.getElementById("tarModal"), { keyboard: true }); // Se inicializa el modal
                tarModal.show(); // Se muestra el modal

                $("#tarModal .modal-title").html("<div>Nueva Tarea</div>"); // Se agrega el titulo del modal
                $("#tarModal .modal-header").addClass('py-3'); // Se agrega la clase al titulo del modal
                select2Val(dataRow.arrUser.id, dataRow.arrUser.nombre, "#tarModal #TareResp");
                $('#tarModal #TareResp').prop('readonly', true).addClass('disabled')
                let fechaFin = formatDate(dataRow.date + ' 00:00')
                let fechaIni = formatDate(dataRow.date + ' 00:00')

                $('#tarModal #TareFechaIni').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: false,
                    showWeekNumbers: false,
                    autoUpdateInput: true,
                    maxDate: moment(),
                    opens: "left",
                    drops: "up",
                    autoApply: true,
                    linkedCalendars: false,
                    ranges: {
                        'Hoy': [moment(), moment()],
                        'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')]
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
                        "monthNames": ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                        firstDay: 1,
                        alwaysShowCalendars: true,
                        applyButtonClasses: "btn btn-tabler",
                    },
                });
                $("#tarModal #TareFechaIni").val(fechaIni);
                $('#tarModal #TareFechaFin').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: false,
                    showWeekNumbers: false,
                    autoUpdateInput: true,
                    maxDate: moment(),
                    opens: "left",
                    drops: "up",
                    autoApply: true,
                    linkedCalendars: false,
                    ranges: {
                        'Hoy': [moment(), moment()],
                        'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')]
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
                        "monthNames": ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                        firstDay: 1,
                        alwaysShowCalendars: true,
                        applyButtonClasses: "btn btn-tabler",
                    },
                });
                $("#tarModal #TareFechaFin").val(fechaFin);
                $("#tarModal #TareHoraIni").val(moment().format('HH:mm'));
                // $("#tarModal #TareHoraFin").val(moment().format('HH:mm'));
                ActiveBTN(false, "#ediTarSubmit", "Aguarde <span class='animated-dots'></span>", 'Aceptar');
                $('.HoraMask').mask(maskBehavior, spOptions);
                let opt2 = {
                    MinLength: "0",
                    SelClose: false,
                    MaxInpLength: "10",
                    delay: "250",
                    allowClear: true,
                };

                function template(data) {
                    if ($(data.html).length === 0) {
                        return data.text;
                    }
                    return $(data.html);
                }

                $("#tarModal #TareProy").select2({
                    language: "es",
                    multiple: false,
                    allowClear: opt2["allowClear"],
                    language: "es",
                    placeholder: "Seleccionar Proyecto",
                    dropdownParent: $('#tarModal'),
                    minimumInputLength: opt2["MinLength"],
                    minimumResultsForSearch: 10,
                    maximumInputLength: opt2["MaxInpLength"],
                    selectOnClose: opt2["SelClose"],
                    language: {
                        noResults: function () {
                            return "No hay resultados..";
                        },
                        inputTooLong: function (args) {
                            var message =
                                "Máximo " +
                                opt2["MaxInpLength"] +
                                " caracteres. Elimine " +
                                overChars +
                                " caracter";
                            if (overChars != 1) {
                                message += "es";
                            }
                            return message;
                        },
                        searching: function () {
                            return "Buscando..";
                        },
                        errorLoading: function () {
                            return "Sin datos..";
                        },
                        removeAllItems: function () {
                            return "Borrar";
                        },
                        inputTooShort: function () {
                            return "Ingresar " + opt2["MinLength"] + " o mas caracteres";
                        },
                        maximumSelected: function () {
                            return "Puede seleccionar solo una opción";
                        },
                        loadingMore: function () {
                            return "Cargando más resultados…";
                        },
                    },
                    ajax: {
                        url: `../proy/data/select/selProyFiltros.php?${Date.now()}`,
                        dataType: "json",
                        type: "POST",
                        delay: opt2["delay"],
                        data: function (params) {
                            return {
                                q: params.term,
                                NomFiltro: "ProyNomFiltro",
                                FiltroEstTipo: "Abierto",
                                // ProyNomFiltro: '',
                                // ProyEmprFiltro: $('#ProyEmprFiltro').val(),
                                // ProyRespFiltro: $('#ProyRespFiltro').val(),
                                // ProyPlantFiltro: $('#ProyPlantFiltro').val(),
                                // ProyEstaFiltro: $('#ProyEstaFiltro').val(),
                                // ProyFiltroTarFechas: $("#FiltroTarFechas").val()
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data,
                            };
                        },
                    },
                });
                $("#tarModal #TareProy").on('select2:select', function (e) {
                    $("#tarModal #TareProc").val('').trigger("change");
                    $("#tarModal #TarePlano").val('').trigger("change");
                    $('#tarModal #TareProc').select2('open');
                });
                $("#tarModal #TareProy").on('select2:unselecting', function (e) {
                    $("#tarModal #TareProc").val('').trigger("change");
                    $("#tarModal #TarePlano").val('').trigger("change");
                });
                $("#tarModal #TareProc").select2({
                    language: "es",
                    multiple: false,
                    allowClear: opt2["allowClear"],
                    language: "es",
                    placeholder: "Proceso",
                    dropdownParent: $('#tarModal'),
                    // templateResult: template,
                    // templateSelection: template,
                    minimumInputLength: opt2["MinLength"],
                    minimumResultsForSearch: 10,
                    maximumInputLength: opt2["MaxInpLength"],
                    selectOnClose: opt2["SelClose"],
                    language: {
                        noResults: function () {
                            return "No hay resultados..";
                        },
                        inputTooLong: function (args) {
                            let message = "Máximo " + opt2.MaxInpLength + " caracteres. Elimine " + overChars + " caracter";
                            if (overChars != 1) {
                                message += "es";
                            }
                            return message;
                        },
                        searching: function () {
                            return "Buscando..";
                        },
                        errorLoading: function () {
                            return "Sin datos..";
                        },
                        removeAllItems: function () {
                            return "Borrar";
                        },
                        inputTooShort: function () {
                            return "Ingresar " + opt2["MinLength"] + " o mas caracteres";
                        },
                        maximumSelected: function () {
                            return "Puede seleccionar solo una opción";
                        },
                        loadingMore: function () {
                            return "Cargando más resultados…";
                        },
                    },
                    ajax: {
                        url: "../proy/data/getProcesos.php",
                        dataType: "json",
                        type: "POST",
                        delay: opt2["delay"],
                        data: function (params) {
                            return {
                                q: params.term,
                                selectProc: $("#TareProy").trigger("change").val(),
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data,
                            };
                        },
                    },
                });
                $("#tarModal #TarePlano").select2({
                    language: "es",
                    multiple: false,
                    allowClear: opt2["allowClear"],
                    language: "es",
                    placeholder: "Planos",
                    dropdownParent: $('#tarModal'),
                    templateResult: template,
                    // templateSelection: template,
                    minimumInputLength: opt2["MinLength"],
                    minimumResultsForSearch: 10,
                    maximumInputLength: opt2["MaxInpLength"],
                    selectOnClose: opt2["SelClose"],
                    language: {
                        noResults: function () {
                            return "No hay resultados..";
                        },
                        inputTooLong: function (args) {
                            let message = "Máximo " + opt2.MaxInpLength + " caracteres. Elimine " + overChars + " caracter";
                            if (overChars != 1) {
                                message += "es";
                            }
                            return message;
                        },
                        searching: function () {
                            return "Buscando..";
                        },
                        errorLoading: function () {
                            return "Sin datos..";
                        },
                        removeAllItems: function () {
                            return "Borrar";
                        },
                        inputTooShort: function () {
                            return "Ingresar " + opt2["MinLength"] + " o mas caracteres";
                        },
                        maximumSelected: function () {
                            return "Puede seleccionar solo una opción";
                        },
                        loadingMore: function () {
                            return "Cargando más resultados…";
                        },
                    },
                    ajax: {
                        url: "../proy/data/getPlanos.php",
                        dataType: "json",
                        type: "POST",
                        delay: opt2["delay"],
                        data: function (params) {
                            return {
                                q: params.term,
                                selectPlano: true,
                                ProyID: $("#TareProy").val(),
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data,
                            };
                        },
                    },
                });
                $('#tarModal #TareProc').prop('disabled', true).addClass('disabled')
                $('#tarModal #TarePlano').prop('disabled', true).addClass('disabled')
                $('#tarModal #TareProy').on('change', function (e) {
                    if ($("#tarModal #TareProy").val()) {
                        $('#tarModal #TareProc').prop('disabled', false).removeClass('disabled')
                        $('#tarModal #TarePlano').prop('disabled', false).removeClass('disabled')
                    } else {
                        $('#tarModal #TareProc').prop('disabled', true).addClass('disabled')
                        $('#tarModal #TarePlano').prop('disabled', true).addClass('disabled')
                    }
                });

                select2EmptyRemove("#tarModal #TareProy");
                select2EmptyRemove("#tarModal #TareProc");
                checkLengthInput('#tarModal #TareHoraIni', 5)
                checkLengthInput('#tarModal #TareFechaFin', 10)
                checkLengthInput('#tarModal #TareFechaIni', 10)

                $('.date').on('show.daterangepicker', function (ev, picker) {
                    // $.notifyClose();
                    // notify("Seleccione una Fecha de Inicio y Fin", "info", 0, "right");
                });
                $('.date').on('hide.daterangepicker', function (ev, picker) {
                    // $.notifyClose();
                });

                $('#tarModal .HoraMask').mask(maskBehavior, spOptions);
                // console.log(moment());
                $('#tarModal .date').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: false,
                    showWeekNumbers: false,
                    autoUpdateInput: true,
                    maxDate: moment(),
                    opens: "left",
                    drops: "up",
                    autoApply: true,
                    linkedCalendars: false,
                    ranges: {
                        'Hoy': [moment(), moment()],
                        'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')]
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
                        "monthNames": ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                        firstDay: 1,
                        alwaysShowCalendars: true,
                        applyButtonClasses: "btn btn-tabler",
                    },
                });
                $('#tarModal #TareFechaFin').on('change', function (e) {
                    e.preventDefault();
                    if ($(this).val().length == 10) {
                        $(this).removeClass("border border-danger border-wide");
                    } else {
                        $(this).addClass("border border-danger border-wide");
                    }
                });
                $('#tarModal #TareFechaIni').on('change', function (e) {
                    e.preventDefault();
                    if ($(this).val().length == 10) {
                        $(this).removeClass("border border-danger border-wide");
                    } else {
                        $(this).addClass("border border-danger border-wide");
                    }
                });

                $("#tarForm").bind("submit", function (e) {
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: 'finalizar/process.php',
                        data: $(this).serialize() + "&assignTar=assignTar&fromTareas=true",
                        beforeSend: function (data) {
                            $.notifyClose();
                            if ($("#ProyDesc").val() == ""
                                // || $("#tarModal #TareHoraFin").val() == ""
                                || $("#tarModal #TareFechaFin").val() == ''
                                || $("#tarModal #TareFechaIni").val() == ''
                                || $("#tarModal #TareHoraIni").val() == ''
                                || $("#tarModal #TareProy").val() == null
                                || $("#tarModal #TareProc").val() == null
                                || $("#tarModal #TareResp").val() == null
                            ) {
                                $.notifyClose();
                                checkEmpty("#tarModal #TareResp", "#select2-TareResp-container");
                                checkEmpty("#tarModal #TareProy", "#select2-TareProy-container");
                                checkEmpty("#tarModal #TareProc", "#select2-TareProc-container");
                                checkEmpty("#tarModal #TareHoraFin");
                                checkEmpty("#tarModal #TareFechaIni");
                                checkEmpty("#tarModal #TareHoraIni");
                                checkEmpty("#tarModal #TareFechaFin");
                                let textErr = `<span class="">Campos requeridos<span>`;
                                notify(textErr, "danger", 2000, "right");
                                return false;
                            }
                            ActiveBTN(true, "#ediTarSubmit", "Aguarde <span class='animated-dots'></span>", 'Aceptar');
                            // return false
                        },
                        success: function (data) {
                            if (data.status == "ok") {
                                $.notifyClose();
                                notify(data.Mensaje, "success", 2000, "right")
                                $("#tableTareas").DataTable().ajax.reload(null, false); // Se recarga la tabla
                                $("#tableAsignasTareas").DataTable().ajax.reload(null, false); // Se recarga la tabla
                                $("#tarModal").fadeOut('slow');
                                $("#tarModal").modal("hide");
                            } else {
                                $.notifyClose();
                                notify(data.Mensaje, "danger", 2000, "right");
                                $("#tableTareas").DataTable().ajax.reload(null, false); // Se recarga la tabla
                                $("#tableAsignasTareas").DataTable().ajax.reload(null, false); // Se recarga la tabla
                            }
                            setTimeout(() => {
                                ActiveBTN(false, "#ediTarSubmit", "Aguarde <span class='animated-dots'></span>", 'Aceptar');
                            }, 1000);
                        },
                        error: function (data) {
                            ActiveBTN(false, "#ediTarSubmit", "Aguarde <span class='animated-dots'></span>", 'Aceptar');
                            $.notifyClose();
                            notify("Error", "danger", 3000, "right");
                            $("#tableTareas").DataTable().ajax.reload(null, false); // Se recarga la tabla
                            $("#tableAsignasTareas").DataTable().ajax.reload(null, false); // Se recarga la tabla
                        }
                    });
                });
            });
        e.stopImmediatePropagation();
        setTimeout(() => {
            document.getElementById('tarModal').addEventListener('hidden.bs.modal', function (event) {
                $("#modales").html('');
                $('.daterangepicker').remove()
            });
        }, 100);
    });
});