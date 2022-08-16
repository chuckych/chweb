$(function () {
    "use strict"; // Start of use strict
    procPend();
    $("#tarProyNomFiltro").val('');
    $("#tarEmprFiltro").val('');
    $("#tarProcNomFiltro").val('');
    $("#tarPlanoFiltro").val('');
    $("#tarRespFiltro").val('');
    $("#FiltroTarFechas").val('');

    $("#mainTitleBar").html('Mis Tareas'); // Title
    let proy_info = sessionStorage.getItem(
        location.pathname.substring(1) + "proy_info"
    );
    let pTarInfo = JSON.parse(proy_info);
    if(!pTarInfo){
        let pag = 'salir';
        axios.get('routes.php', {
            params: {
                'page': pag
            }
        }).then(function (response) {
            sessionStorage.setItem(
                location.pathname.substring(1) + "proy_page",
                pag
            );
            $("#contenedor").html(response.data);
        }).then(() => {
            $("#mainTitleBar").html(capitalize(pag));
            const p = selector;
            $("#mainTitleBar").addClass(p.replace('.', ''));
            $(document).prop("title", capitalize(pag));
        }).catch(function (error) {
            alert(error);
        })
    }    

    let tableTareas = $("#tableTareas").dataTable({ //inicializar datatable
        lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]], //mostrar cantidad de registros
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        responsive: true,
        dom:
            "<'row rowFilters invisible'<'col-12 col-sm-6 flex-end-start'l<'divFiltrosTar'>><'col-12 col-sm-6 flex-center-end'<'divAltaTar'>f<'.divLimpiarSearch'>>>" +
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
                data.tarRespFiltro = pTarInfo.uuid;
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
        $(lengthMenu).addClass("h40"); // Se agrega la clase h40 height: 50px
        let filterInput = $(`${idTable}_filter input`); // Se obtiene el input del filtro
        $(filterInput).attr({ placeholder: "N° Tarea" }).addClass("p-2 pe-3 text-end w100").mask('0000');
        let opt2 = {
            MinLength: "0",
            SelClose: false,
            MaxInpLength: "10",
            delay: "250",
            allowClear: true,
        };

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
                    $("#tarModal .modal-body").prepend("<div class='mb-3 font09'>Empresa: " + dataRow.EmpDesc + "</div>"); // Se agrega la clase al titulo del modal
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
        completeTar('.compleTarNow'); // Se ejecuta la funcion completeTar
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

        const filters = () => {
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
            d.append("tarRespFiltro", pTarInfo.uuid ?? '');
            d.append("FiltroTarFechas", $("#FiltroTarFechas").val() ?? '');
            d.append("TareNum", $("#tableTareas_filter input").val() ?? '');
            return d;
        }

        $('.divFiltrosTar').on("click", ".procPend", function (e) { // Se agrega el evento click al boton procPend
            $('.procPend').prop('disabled', true);

            axios({
                method: "post",
                url: 'finalizar/process.php',
                data: filters(),
                headers: { "Content-Type": "multipart/form-data" },
            }).then(function (response) {
                $.notifyClose()
                notify(response.data.Mensaje, "success", 2000, "right");
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
        $(".divFiltrosTar").append('<button type="button" data-titlel="Filtros" class="shadow-sm ms-1 btn btn-outline-tabler h40 shadow" id="tarShowFiltros" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFiltrosTar" aria-controls="offcanvasFiltrosTar"><i class="bi bi-filter font12"></i></button>'); // Se agrega el boton de filtros
        $(".divFiltrosTar").append('<button class="shadow-sm ms-1 btn btn-outline-info h40 font08 tarLimpiaFiltro" data-titler="Limpiar Filtros"><i class="bi bi-eraser font1"></i></button>'); // Se agrega el boton de limpiar filtros

        fetch(`op/tarFiltros.php?${Date.now()}`).then(
            response => response.text()
        ).then(
            data => {
                $("#divFiltros").html(data); // Se agrega el html al modal
                select2Value(pTarInfo.uuid, pTarInfo.name, '#tarRespFiltro');
                $('#tarRespFiltro').prop('disabled', true);
                setTimeout(() => {
                    $('.form-group-tarRespFiltro').css('display', 'none');
                    // $('span[aria-labelledby="select2-tarRespFiltro-container"]').css('display', 'none');
                }, 500);
            });
        setTimeout(() => {
            $(".divBtnTotales").remove();
        }, 500);
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
        let idTable = "#" + e.target.id;
        $(idTable + ' thead th').addClass("py-3");
        $(idTable + ' tbody tr').hover(function () {
            $(this).find('.btnDots').toggleClass("invisible");
        });
        setTimeout(() => {
            $(idTable + " tr").removeClass("animate__fadeIn");
        }, 500);
        select2Value(pTarInfo.uuid, pTarInfo.name, '#tarRespFiltro');
    });
    tableTareas.on("xhr.dt", function (e, settings, json) {
        tableTareas.off('xhr.dt');
    });
    $.fn.DataTable.ext.pager.numbers_length = 5; // Se agrega el numero de paginas a mostrar en el paginador
});