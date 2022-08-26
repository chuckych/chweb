$(function () {
    "use strict"; // Start of use strict
    procPend();
    $("#tarProyNomFiltro").val('');
    $("#tarEmprFiltro").val('');
    $("#tarProcNomFiltro").val('');
    $("#tarPlanoFiltro").val('');
    $("#tarRespFiltro").val('');
    $("#FiltroTarFechas").val('');

    let tableTareas = $("#tableTareas").dataTable({ //inicializar datatable
        lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]], //mostrar cantidad de registros
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        responsive: true,
        dom:
            "<'row rowFilters invisible'<'col-12 col-sm-6 flex-start-start'l<'divFiltrosTar flex-start-end'>><'col-12 col-sm-6 flex-center-end'<'divAltaTar'>f<'.divLimpiarSearch'>>>" +
            "<'row' <'col-12 flex-end-start'<'tarParametros'>>>" +
            "<'row' <'col-12 mt-2 divEstadoTar mh40'>>" +
            "<'row '<'col-12 table-responsive mt-2't>>" +
            "<'row '<'col-12 col-sm-5'i><'col-12 col-sm-7 flex-center-end'p>>",
        ajax: {
            url: `data/getTareas.php`,
            type: "POST",
            dataType: "json",
            data: function (data) {
                data.TareEstado = $("input[name=FiltroTarEstTipo]:checked").val();
                data.tarProyEsta = $("input[name=tarProyEsta]:checked").val();
                data.tarProyNomFiltro = $("#tarProyNomFiltro").val();
                data.tarEmprFiltro = $("#tarEmprFiltro").val();
                data.tarProcNomFiltro = $("#tarProcNomFiltro").val();
                data.tarPlanoFiltro = $("#tarPlanoFiltro").val();
                data.tarRespFiltro = $("#tarRespFiltro").val();
                data.FiltroTarFechas = $("#FiltroTarFechas").val();
            },
            error: function () {
                $("#tableTareas").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass("animate__animated animate__fadeIn");
        },
        columns: [
            /** TareID */
            {
                className: "text-center",
                targets: "",
                title: "#",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                            <span class=""><i class="bi bi-hash font07 me-1"></i>${row.TareID}</span>
                        `;
                    return '<div class="datacol viewTar pointer">' + datacol + '</div>';
                }
            },
            /** Proyecto */
            {
                className: "",
                targets: "",
                title: "Proyecto",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                            <span data-titler="(#${row.proyecto.ID}) ${row.proyecto.nombre}">
                                <div class="text-truncate filterProy" data-id="${row.proyecto.ID}" data-text="${row.proyecto.nombre}" style="max-width:200px">(#${row.proyecto.ID}) ${row.proyecto.nombre}</div>
                            </span>
                        `;
                    return '<div class="datacol">' + datacol + '</div>';
                }
            },
            /** Proceso */
            {
                className: "",
                targets: "",
                title: "Proceso",
                render: function (data, type, row, meta) {
                    let cost = '';
                    if (row.estado != 'Pendiente') {
                        cost = `<span class="font07 text-mutted">Costo: ${row.totales.costFormat}</span>`;
                    } else {
                        cost = ((row.proceso.costo / 60) * row.fechas.diff).toFixed(2);
                        cost = `<span class="font07 text-mutted">Costo: Pendiente</span>`;
                    }
                    let datacol =
                        `
                            <span data-titler="${row.proceso.nombre}">
                                <div class="text-truncate" style="max-width:200px">${row.proceso.nombre}</div>
                                <div>${cost}</div>
                            </span>
                        `;
                    return '<div class="datacol">' + datacol + '</div>';
                }
            },
            /** Empresa */
            {
                className: "",
                targets: "",
                title: "Empresa",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                            <span data-titler="${row.empresa.nombre}">
                            <div class="text-truncate" style="max-width:150px">${row.empresa.nombre}</div>
                            </span>
                        `;
                    return '<div class="datacol">' + datacol + '</div>';
                }
            },
            /** Usuario */
            {
                className: "",
                targets: "",
                title: "Responsable",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                        <div class="text-truncate" style="max-width:100px">${row.responsable.nombre}</div>
                                    `;
                    return '<div class="datacol">' + datacol + '</div>';
                }
            },
            /** Inicio Tarea */
            {
                className: "",
                targets: "",
                title: "Inicio Tarea",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                            <div class="d-flex flex-column">
                                <div data-titler="${row.fechas.inicioDia} ${row.fechas.inicio} ${row.fechas.inicioHora}">
                                <div class="">${row.fechas.inicio}</div>
                                </div>
                                <div data-titler="${row.fechas.inicioHora}">
                                <div class="font-weight-bold">${row.fechas.inicioHora} hs.</div>
                                </div>
                            </div>
                        `;
                    return '<div class="datacol">' + datacol + '</div>';
                }
            },
            /** Fin Tarea */
            {
                className: "",
                targets: "",
                title: "Fin Tarea",
                render: function (data, type, row, meta) {
                    let colorFin = '';
                    switch (row.fechas.finTipo) {
                        case 'normal':
                            colorFin = 'text-dark';
                            break;
                        case 'manual':
                            colorFin = 'text-blue';
                            break;
                        case 'modificada':
                            colorFin = 'text-red';
                            break;
                        case 'turno':
                            colorFin = 'text-orange';
                            break;
                        case 'fichada':
                            colorFin = 'text-green';
                            break;
                        default:
                            colorFin = 'text-dark';
                            break;
                    }
                    let fechaFin = (row.fechas.fin == row.fechas.inicio) ? '<br>' : row.fechas.fin;
                    let datacol =
                        `
                            <div class="d-flex flex-column">
                                <div data-titler="${row.fechas.finDia} ${row.fechas.fin} ${row.fechas.finHora}">
                                <div class="">${fechaFin}</div>
                                </div>
                                <div data-titler="Tipo: ${capitalize(row.fechas.finTipo)}">
                                <div class="font-weight-bold ${colorFin}">${row.fechas.finHora} hs.</div>
                                </div>
                            </div>
                        `;
                    (row.estado == 'Pendiente') ? datacol = `-` : datacol = datacol;
                    return '<div class="datacol">' + datacol + '</div>';
                }
            },
            /** duracion / tiempo*/
            {
                className: "",
                targets: "",
                title: "Duración",
                render: function (data, type, row, meta) {
                    let tiempo = '';
                    let text = ''
                    if (row.fechas.duracion) {
                        tiempo = (row.fechas.duracionMin == 0) ? row.fechas.duracionMin + '<span class="font07 text-capitalize"> min</span>' : row.fechas.duracionHoras
                        tiempo = `<span data-titlel="` + row.fechas.duracionHuman + `">
                            <div class="font08 px-2 badge w60 bg-azure font-weight-normal flex-center-center"><span>`+ tiempo + `</span></div>
                        </span>`
                        text = "Duración"
                    } else {
                        tiempo = (row.fechas.diff >= 60) ? row.fechas.diffHoras : row.fechas.diff + ' <span class="font07 text-capitalize"> min</span>'
                        tiempo = `<span data-titlel="` + row.fechas.diffHuman + `">
                            <div class="font08 px-2 badge w60 bg-red font-weight-normal flex-center-center"><span>`+ tiempo + `</span></div>
                        </span>`
                        text = "Tiempo"
                    }
                    let datacol =
                        `
                            ${tiempo}
                        `;
                    return '<div class="datacol">' + datacol + '</div>';
                }
            },
            /** Estado */
            {
                className: "",
                targets: "",
                title: "Estado",
                render: function (data, type, row, meta) {
                    let estado = ''
                    if (row.fechas.duracion) {
                        estado = `<div class="font08 font-weight-normal text-capitalize">` + row.estado + `</div>`
                    } else {
                        estado = `<div class="font08 font-weight-normal text-capitalize">` + row.estado + `</div>`
                    }

                    let datacol =
                        `
                            ${estado}
                            
                        `;
                    return '<div class="datacol">' + datacol + '</div>';
                }
            },
            /** Acciones */
            {
                className: "actions",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let tiempo = '';
                    if (row.fechas.duracion) {
                        tiempo = (row.fechas.duracionMin == 0) ? row.fechas.duracionMin + '<span class="font07 text-capitalize"> min</span>' : row.fechas.duracionHoras
                        tiempo = `<span><div class=""><span>` + tiempo + `</span></div></span>`
                    } else {
                        tiempo = (row.fechas.diff >= 60) ? row.fechas.diffHoras : row.fechas.diff + ' <span class="font07 text-capitalize"> min</span>'
                        tiempo = `<span><div class=""><span>` + tiempo + `</span></div></span>`
                    }
                    let compleTar = (row.estado == 'Pendiente') ?
                        `<li>
                        <span data-tareID="${row.TareID}" class="dropdown-item pointer compleTar"><span class="bi font09 bi-check2 me-2"></span>Completar</span>
                    </li>
                    <li>
                        <span data-tareID="${row.TareID}" class="dropdown-item pointer compleTarNow d-none"><span class="bi font09 bi-check2-all me-2"></span>Completar ahora<span class="badge bg-red-lt ms-auto">${tiempo}</span></span>
                    </li>`
                        : '';
                    let openTar = (row.estado == 'Completada') ? `
                    <li>
                        <span data-tareID="${row.TareID}" class="dropdown-item pointer openTar"><span class="bi font08 bi-folder2-open me-2"></span>Reabrir</span>
                    </li>
                    <li>
                        <span data-tareID="${row.TareID}" class="dropdown-item pointer anulaTar"><span class="bi font08 bi-x-lg me-2"></span>Anular</span>
                    </li>
                    `: '';

                    let datacol =
                        `<div class="btn-group dropstart">
                            <button type="button" class="btn px-2 bg-azure-lt border-0 btnDots invisible" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="bi bi-three-dots"></span>
                            </button>
                            <ul class="dropdown-menu py-0 border-0 w-100">
                                <span class="dropdown-header py-2 flex-center-between text-muted bg-light">Tarea 
                                    <div class="badge bg-blue-lt ms-auto ls1">#${row.TareID}</div>
                                </span>
                                <li>
                                    <span class="dropdown-item pointer viewTar" id="tar_${row.TareID}">
                                        <span class="bi font08 bi-view-list me-2"></span>Ver
                                    </span>
                                </li>
                                <li>
                                    <span class="dropdown-item pointer ediTar" id="tar_${row.TareID}">
                                        <span class="bi font08 bi-pencil me-2"></span>Editar
                                    </span>
                                </li>
                                ${openTar}
                                ${compleTar}
                            </ul>
                        </div>`;
                    return '<div class="datacol">' + datacol + '</div>';
                }
            },
        ],
        searchDelay: 500,
        paging: true,
        searching: true,
        info: true,
        ordering: 0,
        responsive: 0,
        language: {
            url: `../js/DataTableSpanishShort2.json?${Date.now()}`
        }
    });
    tableTareas.on("init.dt", function (e, settings) { // Cuando se inicializa la tabla
        let idTable = `#${e.target.id}`; // Se obtiene el id de la tabla
        let lengthMenu = $(`${idTable}_length select`); // Se obtiene el select del lengthMenu
        $(lengthMenu).css("height", "48px"); // Se agrega la clase h40 height: 50px
        $(lengthMenu).css("margin-top", "4px"); // Se agrega la clase h40 height: 50px
        let filterInput = $(`${idTable}_filter input`); // Se obtiene el input del filtro
        $(filterInput).attr({ placeholder: "N° Tarea" }).addClass("p-2 pe-3 text-end w100").mask('0000');
        //$(`${idTable}_filter`).append("<div class=''><select class='selectTar form-control w300'></select></div>").addClass('w200'); // Se agrega la clase flex-center-center
        // Se agrega placeholder al input y se agrega clase p-3.
        let opt2 = {
            MinLength: "0",
            SelClose: false,
            MaxInpLength: "10",
            delay: "250",
            allowClear: true,
        };

        $(".selectTar").select2({
            language: "es",
            multiple: false,
            allowClear: opt2["allowClear"],
            language: "es",
            placeholder: "Tarea",
            dropdownParent: $('#tableTareas'),
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 10,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            width: "100%",
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
                url: `../proy/data/select/selTarFiltros.php?${Date.now()}`,
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q: params.term,
                        NomFiltro: "tarProyNomFiltro",
                        TareEstado: $("input[name=FiltroTarEstTipo]:checked").val(),
                        tarEmprFiltro: $('#tarEmprFiltro').val(),
                        tarProcNomFiltro: $('#tarProcNomFiltro').val(),
                        tarPlanoFiltro: $('#tarPlanoFiltro').val(),
                        tarRespFiltro: $('#tarRespFiltro').val(),
                        FiltroTarFechas: $("#FiltroTarFechas").val()
                    };
                },
                processResults: function (data) {
                    return {
                        results: data,
                    };
                },
            },
        });

        $(".divAltaTar").append('<button type="button" data-titlel="Actualizar Grilla" class="btn btn-link border-0 h40 d-none" id="btnActualizarGrillaTar"><i class="bi bi-arrow-clockwise font12"></i></button>');
        $(".divAltaTar").append('<button type="button" data-titlel="Nueva Tareas" class="btn btn-tabler h40 shadow d-none" id="btnAltaTar"><i class="bi bi-plus font12"></i></button><div class="textDate"></div>');  // Se agrega el boton de alta de tarea
        $(".divLimpiarSearch").append(`<button type="button" class="btn-close p-2 ms-2" aria-label="Close" id="limpiarSearch" style="display: none;"></button>`);  // Se agrega el boton de limpiar busqueda

        $(idTable).removeClass("invisible"); // Se remueve la clase invisible de la tabla
        $('.rowFilters').removeClass("invisible"); // Se remueve la clase invisible de la fila de filtros
        $(idTable + " tbody").on("click", ".viewTar", function (e) { // Se agrega el evento click al boton editar
            e.preventDefault();
            $.notifyClose() // Se cierra el notify
            let dataRow = $(idTable).DataTable().row($(this).parents("tr")).data(); // Se obtiene la fila seleccionada en la tabla
            fetch(`op/tarModal.php?${Date.now()}`) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text()) // Se obtiene la respuesta
                .then(data => {  // Se obtiene el html del modal
                    $("#modales").html(data); // Se agrega el html al modal
                    $("#modales .form-control").attr("autocomplete", "off").prop('disabled', true).addClass('bg-white'); // Se agrega el atributo autocomplete
                    let tarModal = new bootstrap.Modal(document.getElementById("tarModal"), { keyboard: true }); // Se inicializa el modal
                    tarModal.show(); // Se muestra el modal
                    let estadoColor = (dataRow.estado == 'Pendiente') ? 'bg-red-lt' : 'bg-azure-lt'; // Se obtiene el color del estado
                    let tiempo = (dataRow.estado == 'Pendiente') ?
                        '<span class="ms-2 badge bg-red-lt text-capitalize font08 p-2">' + dataRow.fechas.diffHoras + ' hs.</span>' :
                        '<span class="ms-2 badge bg-blue-lt text-capitalize font08 p-2">' + dataRow.fechas.duracionHoras + ' hs.</span>';

                    (dataRow.estado == 'Pendiente') ? $("#tarModal .divFin").remove() : '';
                    $("#tarModal .modal-title").html("<div>TAREA #" + dataRow.TareID + "</div>"); // Se agrega el titulo del modal
                    $("#tarModal .modal-header").addClass('py-3'); // Se agrega la clase al titulo del modal
                    $("#tarModal .modal-footer").addClass('py-3'); // Se agrega la clase al titulo del modal
                    $("#tarModal .modal-body").prepend("<div class='mb-3 font09 badge bg-cyan-lt radius-0 p-3'>" + dataRow.empresa.nombre + "</div>"); // Se agrega la clase al titulo del modal
                    $("#tarModal #TareProy").attr("data-plant", dataRow.proyecto.plantilla); // Se agrega el atributo data-plant
                    select2Val(dataRow.proyecto.ID, dataRow.proyecto.nombre, "#tarModal #TareProy");
                    select2Val(dataRow.responsable.ID, dataRow.responsable.nombre, "#tarModal #TareResp");
                    select2Val(dataRow.proceso.ID, dataRow.proceso.nombre, "#tarModal #TareProc");
                    select2Val(dataRow.plano.ID, dataRow.plano.nombre, "#tarModal #TarePlano");
                    $("#tarModal #TareFechaIni").val(dataRow.fechas.inicio);
                    $("#tarModal #TareHoraIni").val(dataRow.fechas.inicioHora);
                    let fechaFin = (dataRow.fechas.fin == "") ? dataRow.fechas.inicio : dataRow.fechas.fin;
                    $("#tarModal #TareFechaFin").val(fechaFin);
                    let finHora = (dataRow.fechas.finHora == "00:00") ? moment().format('HH:mm') : dataRow.fechas.finHora;
                    $("#tarModal #TareHoraFin").val(finHora);
                    $("[type='submit']").remove() // Se remueve el boton submit
                    $('.tarSubmit').html("<div class='p-3 badge h50 border font08 " + estadoColor + "'>" + dataRow.estado + " " + tiempo + "</div>");
                    setTimeout(() => {
                        $('#tarModal .select2-selection__arrow').remove()
                    }, 100);
                    // 
                });
            e.stopImmediatePropagation();
            setTimeout(() => {
                document.getElementById('tarModal').addEventListener('hidden.bs.modal', function (event) {
                    $("#modales").html('');
                    // $('.daterangepicker').remove()
                });
            }, 100);
        });

        $(idTable + " tbody").on("click", ".ediTar", function (e) { // Se agrega el evento click al boton editar
            // $("#tableTareas").DataTable().ajax.reload(null, false); // Se recarga la tabla
            e.preventDefault();
            $.notifyClose() // Se cierra el notify
            let dataRow = $(idTable).DataTable().row($(this).parents("tr")).data(); // Se obtiene la fila seleccionada en la tabla
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
                    // document.getElementById('tarModal').addEventListener('shown.bs.modal', function (event) { // Se agrega el evento shown.bs.modal al Modal
                    let estadoColor = (dataRow.estado == 'Pendiente') ? 'bg-red-lt' : 'bg-azure-lt'; // Se obtiene el color del estado
                    let pendTar = (dataRow.estado == 'Pendiente') ? true : '';
                    let tiempo = (dataRow.estado == 'Pendiente') ?
                        '<span class="ms-2 badge bg-red-lt text-capitalize">' + dataRow.fechas.diffHoras + ' hs.</span>' :
                        '<span class="ms-2 badge bg-azure-lt text-capitalize">' + dataRow.fechas.duracionHoras + ' hs.</span>';

                    (dataRow.estado == 'Pendiente') ? $("#tarModal .divFin").remove() : '';
                    $("#tarModal .modal-title").html("<div>EDITAR TAREA #" + dataRow.TareID + "</div><div class='mt-2 p-3 badge " + estadoColor + "'>" + dataRow.estado + " " + tiempo + "</div>"); // Se agrega el titulo del modal
                    $("#tarModal .modal-header").addClass('py-3'); // Se agrega la clase al titulo del modal
                    $("#tarModal #TareProy").attr("data-plant", dataRow.proyecto.plantilla); // Se agrega el atributo data-plant
                    select2Val(dataRow.proyecto.ID, dataRow.proyecto.nombre, "#tarModal #TareProy");
                    select2Val(dataRow.responsable.ID, dataRow.responsable.nombre, "#tarModal #TareResp");
                    select2Val(dataRow.proceso.ID, dataRow.proceso.nombre, "#tarModal #TareProc");
                    select2Val(dataRow.plano.ID, dataRow.plano.nombre, "#tarModal #TarePlano");
                    $("#tarModal #TareFechaIni").val(dataRow.fechas.inicio);
                    $("#tarModal #TareHoraIni").val(dataRow.fechas.inicioHora);
                    let fechaFin = (dataRow.fechas.fin == "") ? dataRow.fechas.inicio : dataRow.fechas.fin;

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
                    let finHora = (dataRow.fechas.finHora == "00:00") ? moment().format('HH:mm') : dataRow.fechas.finHora;
                    $("#tarModal #TareHoraFin").val(finHora);
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

                    $("#TareProy").select2({
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
                    $("#TareProy").on('select2:select', function (e) {
                        $("#TareProc").val('').trigger("change");
                        $("#TarePlano").val('').trigger("change");
                        $('#TareProc').select2('open');
                    });
                    $("#TareProy").on('select2:unselecting', function (e) {
                        $("#TareProc").val('').trigger("change");
                        $("#TarePlano").val('').trigger("change");
                    });

                    $("#TareResp").select2({
                        language: "es",
                        multiple: false,
                        allowClear: opt2["allowClear"],
                        language: "es",
                        placeholder: "Responsable",
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
                            url: "../proy/data/select/selResponsable.php",
                            dataType: "json",
                            type: "POST",
                            delay: opt2["delay"],
                            data: function (params) {
                                return {
                                    q: params.term
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: data,
                                };
                            },
                        },
                    });

                    if ($("#TareProy").val()) {
                        $("#TareProc").select2({
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
                        $("#TarePlano").select2({
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
                    }

                    select2EmptyRemove("#TareProy");
                    select2EmptyRemove("#TareProc");
                    select2EmptyRemove("#TareResp");
                    checkLengthInput('#TareHoraFin', 5)
                    checkLengthInput('#TareHoraIni', 5)
                    checkLengthInput('#TareFechaFin', 10)
                    checkLengthInput('#TareFechaIni', 10)
                    // $('#TareProy').on('select2:select', function (e) {
                    //     $("#select2-TareProy-container").removeClass("border border-danger border-wide");
                    // });

                    $("#ProyEmpr").select2({
                        language: "es",
                        multiple: false,
                        allowClear: opt2["allowClear"],
                        language: "es",
                        placeholder: "Empresa",
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
                            url: `../proy/data/select/selEmpresas.php?${Date.now()}`,
                            dataType: "json",
                            type: "POST",
                            delay: opt2["delay"],
                            data: function (params) {
                                return {
                                    q: params.term
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: data,
                                };
                            },
                        },
                    });
                    $("#ProyPlant").select2({
                        language: "es",
                        multiple: false,
                        allowClear: opt2["allowClear"],
                        language: "es",
                        placeholder: "Plantilla procesos",
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
                            url: "../proy/data/select/selPlantilla.php",
                            dataType: "json",
                            type: "POST",
                            delay: opt2["delay"],
                            data: function (params) {
                                return {
                                    q: params.term
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: data,
                                };
                            },
                        },
                    });
                    $("#ProyEsta").select2({
                        language: "es",
                        multiple: false,
                        allowClear: opt2["allowClear"],
                        language: "es",
                        placeholder: "Estado",
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
                            url: "../proy/data/select/selEstado.php",
                            dataType: "json",
                            type: "POST",
                            delay: opt2["delay"],
                            data: function (params) {
                                return {
                                    q: params.term
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: data,
                                };
                            },
                        },
                    });

                    $('.date').on('show.daterangepicker', function (ev, picker) {
                        // $.notifyClose();
                        // notify("Seleccione una Fecha de Inicio y Fin", "info", 0, "right");
                    });
                    $('.date').on('hide.daterangepicker', function (ev, picker) {
                        // $.notifyClose();
                    });

                    $('.HoraMask').mask(maskBehavior, spOptions);

                    $('.date').daterangepicker({
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
                    $('#TareFechaFin').on('change', function (e) {
                        e.preventDefault();
                        if ($(this).val().length == 10) {
                            $(this).removeClass("border border-danger border-wide");
                        } else {
                            $(this).addClass("border border-danger border-wide");
                        }
                    });
                    $('#TareFechaIni').on('change', function (e) {
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
                            data: $(this).serialize() + "&tareID=" + dataRow.TareID + "&ediTar=ediTar&fromTareas=true&pendTar=" + pendTar,
                            beforeSend: function (data) {
                                $.notifyClose();
                                if ($("#ProyDesc").val() == ""
                                    || $("#tarModal #TareHoraFin").val() == ""
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
                                    $("#tarModal").fadeOut('slow');
                                    $("#tarModal").modal("hide");
                                } else {
                                    $.notifyClose();
                                    notify(data.Mensaje, "danger", 2000, "right");
                                    $("#tableTareas").DataTable().ajax.reload(null, false); // Se recarga la tabla
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
                            }
                        });
                    });
                });
            e.stopImmediatePropagation();
            setTimeout(() => {
                document.getElementById('tarModal').addEventListener('hidden.bs.modal', function (event) {
                    $("#modales").html('');
                    // $('.daterangepicker').remove()
                });
            }, 100);
        });

        completeTar('.compleTarNow'); // Se ejecuta la funcion completeTar
        openTar('.openTar'); // Se ejecuta la funcion openTar
        anulaTar('.anulaTar'); // Se ejecuta la funcion anulaTar

        $(idTable + " tbody").on("click", ".compleTar", function (e) { // Se agrega el evento click al boton editar
            e.preventDefault();
            $.notifyClose() // Se cierra el notify
            let dataRow = $(idTable).DataTable().row($(this).parents("tr")).data(); // Se obtiene la fila seleccionada en la tabla
            fetch(`op/tarModal.php?${Date.now()}`) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text()) // Se obtiene la respuesta
                .then(data => {  // Se obtiene el html del modal
                    // console.log(HtmlEncode(dataRow.ProyObs));
                    $("#modales").html(data); // Se agrega el html al modal
                    $("#modales .form-control").attr("autocomplete", "off"); // Se agrega el atributo autocomplete
                    let tarModal = new bootstrap.Modal(document.getElementById("tarModal"), { keyboard: true }); // Se inicializa el modal
                    tarModal.show(); // Se muestra el modal
                    // document.getElementById('tarModal').addEventListener('shown.bs.modal', function (event) { // Se agrega el evento shown.bs.modal al Modal
                    $("#tarModal .modal-dialog").removeClass('modal-lg')
                    let estadoColor = (dataRow.estado == 'Pendiente') ? 'bg-red-lt' : 'bg-azure-lt'; // Se obtiene el color del estado
                    let tiempo = (dataRow.estado == 'Pendiente') ?
                        '<span class="ms-2 badge bg-red-lt text-capitalize">' + dataRow.fechas.diffHoras + ' hs.</span>' :
                        '<span class="ms-2 badge bg-azure-lt text-capitalize">' + dataRow.fechas.duracionHoras + ' hs.</span>';

                    $("#tarModal .divIni").remove();
                    $("#tarModal .tarSubmit button").attr("type", "submit").attr('data-tareID', dataRow.TareID);

                    $("#tarModal .divFin").removeClass('col-sm-6')
                    $("#tarModal .divFin").prepend('<div class="mb-4 badge bg-blue-lt py-3 font08 w-100">Inicio Tarea:  ' + dataRow.fechas.inicioDia + ' ' + dataRow.fechas.inicio + ' ' + dataRow.fechas.inicioHora + '</div>')
                    $("#tarModal .modal-title").html("<div>COMPLETAR TAREA #" + dataRow.TareID + "</div><div class='mt-2 p-3 badge " + estadoColor + "'>" + dataRow.estado + " " + tiempo + "</div>"); // Se agrega el titulo del modal

                    $("#tarModal .modal-header").addClass('py-3'); // Se agrega la clase al titulo del modal
                    $("#tarModal .divEditar").remove();
                    let fechaFin = (dataRow.fechas.fin == "") ? dataRow.fechas.inicio : dataRow.fechas.fin;
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
                    let finHora = (dataRow.fechas.finHora == "00:00") ? moment().format('HH:mm') : dataRow.fechas.finHora;
                    $("#tarModal #TareHoraFin").val(finHora);
                    ActiveBTN(false, "#ediTarSubmit", "Aguarde <span class='animated-dots'></span>", 'Aceptar');
                    // Se desactiva el boton de submit
                    $('.HoraMask').mask(maskBehavior, spOptions);
                    $("#tarForm").bind("submit", function (e) {
                        e.preventDefault();
                        $.ajax({
                            type: 'POST',
                            url: 'finalizar/process.php',
                            data: $(this).serialize() + "&tareID=" + dataRow.TareID + "&tarComplete=tarComplete&fromTareas=true",
                            beforeSend: function (data) {
                                $.notifyClose();
                                if ($("#ProyDesc").val() == ""
                                    || $("#tarModal #TareHoraFin").val() == ""
                                    || $("#tarModal #TareFechaFin").val() == ''
                                ) {
                                    $.notifyClose();
                                    checkEmpty("#tarModal #TareHoraFin");
                                    checkEmpty("#tarModal #TareFechaFin");
                                    let textErr = `<span class="">Campos requeridos<span>`;
                                    notify(textErr, "danger", 2000, "right");
                                    return false;
                                }
                                ActiveBTN(true, "#ediTarSubmit", "Aguarde <span class='animated-dots'></span>", 'Aceptar');
                            },
                            success: function (data) {
                                if (data.status == "ok") {
                                    $.notifyClose();
                                    notify(data.Mensaje, "success", 2000, "right")
                                    $("#tableTareas").DataTable().ajax.reload(null, false); // Se recarga la tabla
                                    $("#tarModal").fadeOut('slow');
                                    $("#tarModal").modal("hide");
                                } else {
                                    $.notifyClose();
                                    notify(data.Mensaje, "danger", 2000, "right");
                                    // $("#tableTareas").DataTable().ajax.reload(null, false); // Se recarga la tabla
                                }
                                setTimeout(() => {
                                    ActiveBTN(false, "#ediTarSubmit", "Aguarde <span class='animated-dots'></span>", 'Aceptar');
                                }, 1000);
                            },
                            error: function (data) {
                                ActiveBTN(false, "#ediTarSubmit", "Aguarde <span class='animated-dots'></span>", 'Aceptar');
                                $.notifyClose();
                                notify("Error", "danger", 3000, "right");
                                // $("#tableTareas").DataTable().ajax.reload(null, false); // Se recarga la tabla
                            }
                        });
                    });
                });

            e.stopImmediatePropagation();
            setTimeout(() => {
                document.getElementById('tarModal').addEventListener('hidden.bs.modal', function (event) {
                    $("#modales").html('');
                    // $('.daterangepicker').remove()
                });
            }, 100);
        });

        $('.divFiltrosTar').on("click", ".calCosto", function (e) { // Se agrega el evento click al boton editar
            e.preventDefault();
            $.notifyClose() // Se cierra el notify
            e.stopImmediatePropagation();
            $('.calCosto').prop('disabled', true);
            let datos = new FormData();
            datos.append('calCosto', '1');
            datos.append("TareEstado", $("input[name=FiltroTarEstTipo]:checked").val() ?? '');
            datos.append("tarProyEsta", $("input[name=tarProyEsta]:checked").val() ?? '');
            datos.append("tarProyNomFiltro", $("#tarProyNomFiltro").val() ?? '');
            datos.append("tarEmprFiltro", $("#tarEmprFiltro").val() ?? '');
            datos.append("tarProcNomFiltro", $("#tarProcNomFiltro").val() ?? '');
            datos.append("tarPlanoFiltro", $("#tarPlanoFiltro").val() ?? '');
            datos.append("tarRespFiltro", $("#tarRespFiltro").val() ?? '');
            datos.append("FiltroTarFechas", $("#FiltroTarFechas").val() ?? '');
            datos.append("search[value]", $("#tableTareas_filter input").val() ?? '');
            axios({
                method: "post",
                url: 'finalizar/process.php?calCosto=true',
                data: datos,
                headers: { "Content-Type": "multipart/form-data" },
            }).then(function (response) {
                notify(response.data.Mensaje, "success", 2000, "right");
            }).catch(function (error) {
                alert(error);
            }).then(function () {
                $('.calCosto').prop('disabled', false);
                $("#tableTareas").DataTable().ajax.reload(null, false); // Se recarga la tabla
            });

        });
        const filters = (custom = '') => {
            let proy_info = sessionStorage.getItem(
                location.pathname.substring(1) + "proy_info"
            );
            let p = JSON.parse(proy_info);
            let d = new FormData();
            d.append('procPendientes', 1);
            d.append('ProcPendTar', 1);
            d.append('_c', p.reci);
            d.append("TareEstado", $("input[name=FiltroTarEstTipo]:checked").val() ?? '');
            d.append("tarProyEsta", $("input[name=tarProyEsta]:checked").val() ?? '');
            d.append("tarProyNomFiltro", $("#tarProyNomFiltro").val() ?? '');
            d.append("tarEmprFiltro", $("#tarEmprFiltro").val() ?? '');
            d.append("tarProcNomFiltro", $("#tarProcNomFiltro").val() ?? '');
            d.append("tarPlanoFiltro", $("#tarPlanoFiltro").val() ?? '');
            d.append("tarRespFiltro", $("#tarRespFiltro").val() ?? '');
            d.append("FiltroTarFechas", $("#FiltroTarFechas").val() ?? '');
            d.append("TareNum", $("#tableTareas_filter input").val() ?? '');
            if (custom) {
                d.append(custom[0], custom[1]);
                d.append('procPendientes', '');
                d.append('ProcPendTar', '');
                d.append('start', 0);
                d.append('length', 9999);
                d.append('draw', 1);
            }
            return d;
        }

        $('.divFiltrosTar').on("click", ".procPend", function (e) { // Se agrega el evento click al boton procPend
            $('.procPend').prop('disabled', true);
            $.notifyClose()
            notify('Procesando tareas...', "info", 0, "right");
            axios({
                method: "post",
                url: 'finalizar/process.php',
                data: filters(),
                headers: { "Content-Type": "multipart/form-data" },
            }).then(function (response) {
                $.notifyClose()
                if (response.data.Info) {
                    notify('<p>' + response.data.Mensaje + '<p>', "success", 0, "right");
                    setTimeout(() => {
                        $('[data-notify = "message"]').append('<div class="notifInfo p-2 card border"></div>')
                        $('.notifInfo').addClass('maxh450 overflow-auto')
                        $.each(response.data.Info, function (index, reg) {
                            // console.log(reg);
                            $('.notifInfo').append('<p class="font08 p-0 mt-1"><span class="lh1">' + reg + '</span></p>')
                        })
                    }, 500);
                } else {
                    notify(response.data.Mensaje, "success", 2000, "right");
                }

            }).catch(function (error) {
                alert(error);
            }).then(function () {
                $('.procPend').prop('disabled', false);
                $("#tableTareas").DataTable().ajax.reload(null, false); // Se recarga la tabla
            });
        });
        $("#btnActualizarGrillaTar").click(function (e) {
            e.preventDefault();
            classEfect("#btnActualizarGrillaTar .bi", "animate__animated animate__rotateIn")
            loadingTable("#tableTareas")
            $("#tableTareas").DataTable().ajax.reload(); // Se recarga la tabla
        });
        fetch(`op/tarDivEstados.html?${Date.now()}`) // Se hace la peticion ajax para obtener el div de estados
            .then(response => response.text()) // Se obtiene la respuesta
            .then(data => {
                $(".divEstadoTar").html(data); // Se agrega el html al modal
                $('.divEstadoTar').on("click", ".form-selectgroup-input", function (e) {
                    loadingTable(idTable)
                    $("#tableTareas").DataTable().ajax.reload();
                });
            });
        //navbar-expand-lg
        $(".divFiltrosTar").append(`
        <nav class="navbar navbar-expand-lg">
            <button class="navbar-toggler ms-3 mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedFiltrosTar" aria-controls="navbarSupportedFiltrosTar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon text-secondary"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedFiltrosTar">
                <ul class="navbar-nav divFiltrosTarNav"></ul>
            </div>
        </nav>
        `)
        $(".divFiltrosTarNav").append(`
            <button class="shadow-sm ms-1 mt-3 mt-sm-0 btn btn-outline-tabler shadow" type="button" data-titlel="Filtros"  id="tarShowFiltros" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFiltrosTar" aria-controls="offcanvasFiltrosTar"><i class="bi bi-filter font1 d-none d-sm-block"></i><span class="d-block d-sm-none font08">Filtros</span></button>
            <button class="shadow-sm ms-1 mt-1 mt-sm-0 btn btn-outline-info font08 tarLimpiaFiltro" data-titler="Limpiar Filtros"><i class="bi bi-eraser font1 d-none d-sm-block"></i><span class="d-block d-sm-none">Borrar Filtros</span></button>
            <button class="shadow-sm ms-1 mt-1 mt-sm-0 btn btn-outline-info font08 calCosto" data-titler="Recalcular costos segun filtro"><i class="bi bi-calculator font1 d-none d-sm-block"></i><span class="d-block d-sm-none">Recalcular Costos</span></button>
            <button class="shadow-sm ms-1 mt-1 mt-sm-0 btn btn-outline-info font08 procPend" data-titler="Procesar Tareas Pendientes"><i class="bi bi-arrow-down-up font1 d-none d-sm-block"></i><span class="d-block d-sm-none">Procesar Tareas</span></button>
            <button class="shadow-sm ms-1 mt-1 mt-sm-0 btn btn-outline-info font08" data-titler="Parametros" data-bs-toggle="collapse" data-bs-target="#tarParametros" aria-expanded="false" aria-controls="tarParametros"><i class="bi bi-three-dots font1 d-none d-sm-block"></i><span class="d-block d-sm-none">Parametros</span></button>
            <button class="shadow-sm ms-1 mt-1 mt-sm-0 btn btn-outline-teal font08 toExcel" data-titlel="Exportar a Excel"><i class="bi bi-filetype-xls font1 d-none d-sm-block"></i><span class="d-block d-sm-none">Exportar Excel</span></button>
        `)
        $(".divFiltrosTarNav").addClass('ms-2')

        fetch(`op/tarParametros.html?${Date.now()}`) // Se hace la peticion ajax para obtener el div de estados
            .then(response => response.text()) // Se obtiene la respuesta
            .then(data => {
                $(".tarParametros").html(data); // Se agrega el html al modal
            }).then(() => {
                let dataConf = new FormData();
                function getConfTar() {
                    dataConf.append('conf', '1');
                    dataConf.append('getConf', '1');
                    axios({
                        method: "post",
                        url: 'op/crud.php',
                        data: dataConf,
                        headers: { "Content-Type": "multipart/form-data" },
                    }).then(function (response) {
                        if (response.data) {
                            let d = response.data.confTar;
                            $(".HoraCierre").val(d.HoraCierre);
                            $(".LimitTar").val(d.LimitTar);
                            let checked = (d.ProcPendTar == '1') ? true : false;
                            $(".ProcPendTar").prop('checked', checked);
                        }
                    }).catch(function (error) {
                        alert(error);
                    })
                }
                function setConfTar() {
                    let d = new FormData();
                    d.append('conf', '1');
                    d.append('setConf', '1');
                    d.append('HoraCierre', $(".HoraCierre").val());
                    d.append('LimitTar', $(".LimitTar").val());
                    d.append('ProcPendTar', $(".ProcPendTar").prop('checked') ? '1' : '0');
                    navigator.sendBeacon('op/crud.php', d);
                }
                getConfTar();
                $('.HoraCierre').mask(maskBehavior, spOptions);
                $(".ProcPendTar").on("change", function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    setConfTar()
                });
                $(".HoraCierre").on("keyup", function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    let str = $(this).val();
                    if (str.length == 5) {
                        $(this).removeClass("is-invalid");
                        $(this).addClass("is-valid");
                        setConfTar()
                    } else {
                        $(this).removeClass("is-valid");
                        $(this).addClass("is-invalid");
                    }
                });
                $('.LimitTar').mask('0000', { reverse: false });
                $(".LimitTar").on("keyup", function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    let str = ($(this).val());
                    if (str.length >= 2 && str.length <= 4 && str.match(/^[0-9]+$/)) {
                        $(this).removeClass("is-invalid");
                        $(this).addClass("is-valid");
                        setConfTar()
                    } else {
                        $(this).removeClass("is-valid");
                        $(this).addClass("is-invalid");
                    }
                });
            });

        fetch(`op/tarFiltros.php?${Date.now()}`).then(
            response => response.text()
        ).then(
            data => {
                $("#divFiltros").html(data); // Se agrega el html al modal
            });

        axios.post(`op/tarTotales.php`, {
        }).then(function (response) {
            $("#canvaTarTotales").html(response.data);
        }).catch(function (error) {
            alert(error);
        })
        setTimeout(() => {
            $(".divBtnTotales").fadeIn('slow');
        }, 500);

        $(".toExcel").on("click", function (e) {
            let t = ['toExcel', true];
            $.notifyClose();
            ActiveBTN(true, ".toExcel", "<span class='animated-dots p-1'></span>", '<i class="bi bi-filetype-xls font1"></i>');
            notify('Exportando <span class = "dotting mr-1"> </span> <span class="animated-dots"></span>', 'info', 0, 'right')
            axios({
                method: "post",
                url: 'op/crud.php',
                data: filters(t),
                headers: { "Content-Type": "multipart/form-data" },
            }).then(function (response) {
                let file = response.data
                // window.location = file.data
                $.notifyClose();
                if (file.status == 'error') {
                    notify('No hay datos a exportar', 'danger', 3000, 'right');
                } else {
                    notify('Archivo exportado correctamente.<br><div class="shadow-sm w100"><a href="op/' + file.Mensaje + '" class="btn btn-blue px-4 btn-sm mt-3 font08 h40 downloadXls" target="_blank" download><div class="d-flex align-items-center"><span>Descargar</span><i class="bi bi-file-earmark-arrow-down ms-1 font1"></i></div></a></div>', 'success', 0, 'right')
                }
                $(".downloadXls").click(function () {
                    $.notifyClose();
                });
            }).catch(function (error) {
                alert(error);
            }).then(function () {
                ActiveBTN(false, ".toExcel", "<span class='animated-dots'></span>", '<i class="bi bi-filetype-xls font1"></i>');
            });
        });

    });
    tableTareas.on("page.dt", function (e, settings) {
        let idTable = "#" + e.target.id; // Se obtiene el id de la tabla
        loadingTable(idTable) // Se agrega el loading a la tabla
        // $(idTable + " div").addClass("pre-div"); // Se agrega la clase pre-div a la div de la tabla
    });
    tableTareas.on("draw.dt", function (e, settings) {
        if (settings._iRecordsTotal == 0) {
            $('.notFound').remove();
            $("#tableTareas").append(`<div style="min-height:300px" class="notFound shadow-sm w-100 bg-muted-lt flex-center-center"><h1>No se encontraron resultados.</h1></div>`);
            $("#tableTareas tbody").hide();
            $("#tableTareas thead").hide();
            $(".dataTables_info").hide();
            $(".dataTables_paginate").hide();
        } else {
            $(".notFound").remove()
            $("#tableTareas tbody").show();
            $("#tableTareas thead").show();
            $(".dataTables_info").show();
            $(".dataTables_paginate").show();
        }
        setTimeout(() => {
            getTotalesTar();
        }, 500);
        let idTable = "#" + e.target.id;
        $(idTable + ' thead th').addClass("py-3");
        $(idTable + ' tbody tr').hover(function () {
            $(this).find('.btnDots').toggleClass("invisible");
        });
        setTimeout(() => {
            $(idTable + " tr").removeClass("animate__fadeIn");
        }, 500);

        // $('.animate__slow').mouseover(function (e) {
        //     e.preventDefault();
        //     $(this).find('[data-bs-toggle="dropdown"]').addClass('show').attr('aria-expanded', 'true');
        //     $(this).find('.dropdown-menu').addClass('show').attr('data-popper-placement', 'left-start').css({ "position": "absolute", "inset": "auto 0px 0px auto", "margin": "0px", "transform": "translate3d(-25px, 0.25px, 0px)" });
        // }).mouseout(function () {
        //     // if($('.navbar-toggler').is(':hidden')) {
        //     $(this).find('[data-bs-toggle="dropdown]"').removeClass('show').attr('aria-expanded', 'true');
        //     $(this).find('.dropdown-menu').removeClass('show').attr('data-popper-placement', '').css({ "position": "", "inset": "", "margin": "", "transform": "" });
        //     // }
        // });

    });
    tableTareas.on("xhr.dt", function (e, settings, json) {
        tableTareas.off('xhr.dt');
    });
    $.fn.DataTable.ext.pager.numbers_length = 5; // Se agrega el numero de paginas a mostrar en el paginador

    function getTotalesTar() {

        let datos = new FormData();
        datos.append("TareEstado", $("input[name=FiltroTarEstTipo]:checked").val() ?? '');
        datos.append("tarProyEsta", $("input[name=tarProyEsta]:checked").val() ?? '');
        datos.append("tarProyNomFiltro", $("#tarProyNomFiltro").val() ?? '');
        datos.append("tarEmprFiltro", $("#tarEmprFiltro").val() ?? '');
        datos.append("tarProcNomFiltro", $("#tarProcNomFiltro").val() ?? '');
        datos.append("tarPlanoFiltro", $("#tarPlanoFiltro").val() ?? '');
        datos.append("tarRespFiltro", $("#tarRespFiltro").val() ?? '');
        datos.append("FiltroTarFechas", $("#FiltroTarFechas").val() ?? '');
        datos.append("tarTotales", '1');
        datos.append("search[value]", $("#tableTareas_filter input").val() ?? '');

        axios({
            method: "post",
            url: 'data/getTareas.php?totales',
            data: datos,
            headers: { "Content-Type": "multipart/form-data" },
        }).then(function (response) {
            // let tRecord = response.data.recordsTotal

            (response.data.recordsTotal > 0) ? $("#canvaTarTotales .offcanvas-body .list-group").html('') : $("#canvaTarTotales .offcanvas-body .list-group").html('No se encontraron resultados.');

            $("#canvaTarTotales .offcanvas-title").html('Resumen por proyecto');
            // $("#canvaTarTotales .offcanvas-body .list-group").html('');

            $.each(response.data.data, function (index, r) {
                $("#canvaTarTotales .offcanvas-body .list-group").append(`
                    <div class="border-0 p-0 mb-3">
                        <div class="d-block border-0 shadow-sm border-bottom-0 p-2 bg-blue-lt font08 font-weight-medium py-3">
                            <span class="ms-2"># ${r.proyecto.ID} - ${r.proyecto.nombre}</span>
                        </div>
                        <div class="flex-center-between">
                            <div class="card w-100">
                                <div class="card-body text-center">
                                    <div class="text-muted font-weight-normal font07">Tareas</div>
                                    <div class="h3 my-1">${r.totales.totalTar}</div>
                                </div>
                            </div>
                            <div class="card w-100">
                                <div class="card-body text-center">
                                    <div class="text-muted font-weight-normal font07">Horas</div>
                                    <div class="h3 my-1">${r.totales.totalHoras} Hs.</div>
                                </div>
                            </div>
                            <div class="card w-100">
                                <div class="card-body text-center">
                                    <div class="text-muted font-weight-normal font07">Costo</div>
                                    <div class="h3 my-1">$${r.totales.totalCosto}</div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="w-100 card-body pt-0">
                                <span class="text-muted font-weight-normal font07">Empresa: </span>
                                <div class="mt-1 font08">${r.empresa.nombre}</div>
                            </div>
                        </div>
                    </div>
                `);
            });

        }).then(function () {
            setTimeout(() => {

                let canvaTarTotales = document.getElementById('canvaTarTotales')
                canvaTarTotales.addEventListener('show.bs.offcanvas', event => {
                    $('.tarTotales').removeClass('btn-ghost-azure').addClass('btn-azure');
                })
                canvaTarTotales.addEventListener('hidden.bs.offcanvas', event => {
                    $('.tarTotales').removeClass('btn-azure').addClass('btn-ghost-azure');
                })
            }, 700);
        }).catch(function (error) {
            alert(error);
        })
    }

    // $(document).on('click', '.filterProy', function (e) {
    //     e.preventDefault();
    //     let id = $(this).attr('data-id');
    //     let text = $(this).attr('data-text');
    //     select2Value(id, text, '#tarProyNomFiltro');
    //     $("#tableTareas").DataTable().ajax.reload(); // Se recarga la tabla
    //     e.stopPropagation();
    //     // $('.filterProy').off('click');
    // });
});