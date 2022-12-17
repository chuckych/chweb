$(function () {
    "use strict"; // Start of use strict
    let tableProyectos = $("#tableProyectos").dataTable({ //inicializar datatable
        lengthMenu: [[3, 10, 25, 50, 100], [3, 10, 25, 50, 100]], //mostrar cantidad de registros
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        stateSave: true,
        responsive: true,
        dom:
            "<'row mt-3'<'col-12 col-sm-6 d-flex justify-content-start'l<'divFiltrosProy'>><'col-12 col-sm-6 d-flex justify-content-end'<'divAltaProy'>f>>" +
            "<'row' <'col-12 mt-2 divEstados mh40'>>" +
            "<'row '<'col-12 table-responsive't>>" +
            "<'row '<'col-12 col-sm-5'i><'col-12 col-sm-7 d-flex justify-content-end'p>>",
        ajax: {
            url: `data/getProyectos.php?${Date.now()}`,
            type: "POST",
            dataType: "json",
            data: function (data) {
                data.FiltroEstTipo = $("input[name=FiltroEstTipo]:checked").val();
                data.ProyNomFiltro = $("#ProyNomFiltro").val();
                data.ProyEmprFiltro = $("#ProyEmprFiltro").val();
                data.ProyRespFiltro = $("#ProyRespFiltro").val();
                data.ProyPlantFiltro = $("#ProyPlantFiltro").val();
                data.ProyEstaFiltro = $("#ProyEstaFiltro").val();
                data.ProyFiltroFechas = $("#FiltroFechas").val();
            },
            error: function () {
                $("#tableProyectos").css("display", "none");
            }
        },
        rowGroup: {
            dataSrc: function (row) {
                return `<span class="font-weight-bold"><span class="tracking-wide d-none">(#${row.ProyData.ID})</span> ${row.ProyData.Nombre}</span><p class="text-mutted font08 pt-2 p-0 m-0">${row.ProyData.Desc}</p>`;
            },
            endRender: null,
            startRender: function (rows, group) {
                return `<div class="d-flex align-items-center pt-1"><span class="font09">${group}</span></div>
                `;
            },
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass("animate__animated animate__fadeIn");
        },
        columns: [
            /** Estado */
            {
                className: "align-middle border-bottom pe-3",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                        <div class="pb-1 font08 text-mutted w100" style="border-bottom:10px solid ${row.ProyEsta.Color};">${row.ProyEsta.Nombre}</div>
                        `;
                    return datacol;
                }
            },
            /** Empresa */
            {
                className: "align-middle border-bottom px-3",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                        <span class="font08 text-secondary"><i class="bi bi-building me-1"></i> Empresa: </span><br><span class="font09 font-weight-bold">${row.ProyEmpr.Nombre}</span>
                        `;
                    return datacol;
                }
            },
            /** Responsable */
            {
                className: "align-middle border-bottom px-3",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                        <span class="font08 text-secondary">Responsable: </span><br><span class="font09">${row.ProyResp.Nombre}</span>
                        `;
                    return datacol;
                }
            },
            /** Fechas */
            {
                className: "align-middle border-bottom px-3",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let FechaIni = row.ProyFech.Inicio.split("-"); //fecha inicio
                    let FechaFin = row.ProyFech.Fin.split("-"); //fecha fin 
                    FechaIni = `${FechaIni[2]}/${FechaIni[1]}/${FechaIni[0]}`;  // formato fecha inicio
                    FechaFin = `${FechaFin[2]}/${FechaFin[1]}/${FechaFin[0]}`; // formato fecha fin
                    let datacol =
                        `
                        <span class="tracking-wide font08">${FechaIni}<br>${FechaFin}</span>
                        `;
                    return datacol;
                }
            },
            /** Plantilla */
            {
                className: "align-middle border-bottom px-3",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                        <span class="font08 text-secondary">Procesos: </span><br><span class="font09">${row.ProyPlant.Nombre}</span>
                        `;
                    return datacol;
                }
            },
            /** Plantilla Planos*/
            {
                className: "align-middle border-bottom px-3",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let nombre = (row.ProyPlantPlano.Nombre) ? row.ProyPlantPlano.Nombre : '-'
                    let datacol =
                        `
                        <span class="font08 text-secondary">Planos: </span><br><span class="font09">${nombre}</span>
                        `;
                    return datacol;
                }
            },
            /** Horas */
            {
                className: "align-middle border-bottom px-3",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                        <span class="font08 text-secondary">Horas: </span><br><span class="font09">${row.ProyCalc.Horas}</span>
                        `;
                    return datacol;
                }
            },
            /** Editar */
            {
                className: "align-middle border-bottom px-3 w-100",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                        <button type="button" class="btn p-2 btn-outline-teal bi bi-pencil editProy float-end"></button>
                        `;
                    return datacol;
                }
            },
        ],
        searchDelay: 350,
        paging: true,
        searching: true,
        info: true,
        ordering: 0,
        responsive: 0,
        language: {
            url: `../js/DataTableSpanishShort2.json?${Date.now()}`
        }
    });
    let idPlano = new Array();
    let planos = new Array()
    function addLiPlanos(selector, id, text, cod) {
        let idPlano = new Array();

        $(selector).prepend(`
            <div class="list-group-item animate__animated animate__fadeIn" id="list_${id}">
                <div class="row align-items-center">
                    <div class="col text-truncate">
                        <input type="hidden" hidden class"idPlano" value="${id}">
                        <div class="flex-center-between">
                            <div>${text}</div>
                            <button type="button" class="btn bg-red-lt deleteAsignPlano w30 h30 p-0"><i class="bi bi-dash"></i></button>
                        </div>
                        <small class="d-block text-muted text-truncate mt-n1">${cod}</small>
                    </div>
                </div>
            </div>
        `);
        // $("#list_" + id).addClass('text-teal')
        // setTimeout(() => {
        //     $("#list_" + id).removeClass('text-teal')
        // }, 1000);
        /** Array de checkbox checked*/
        $(document).on('click', '.deleteAsignPlano', function (e) {
            $(this).parents('.list-group-item').remove()
            idPlano = new Array();

            $(selector + " input").each(function (index, element) {
                (idPlano.push(parseInt(element.value)));
            });
            $('#TotalPlanosAsignados').html(idPlano.length)
        });

        $(selector + " input").each(function (index, element) {
            (idPlano.push(parseInt(element.value)));
        });
        $('#TotalPlanosAsignados').html(idPlano.length)

    }
    function getLiPlanos(selector) {
        if (selector) {
            let i = new Array();
            $(selector + " input").each(function (index, element) {
                (i.push(parseInt(element.value)));
            });
            return i;
        }
        return false;
    }
    function setCardProyPlanos(plantilla) {
        if (!plantilla) {
            $('#cardProyPlanos').html('')
            $('#TotalPlanosAsignados').html('0')
            return
        }

        $('#cardProyPlanos').html('')
        $('#TotalPlanosAsignados').html('0')

        let datos = new FormData()
        datos.append('Plantilla', plantilla)
        datos.append('start', 0)
        datos.append('length', 1000)
        datos.append('draw', 0)
        datos.append('planosPlant', 1)

        axios({
            method: "POST",
            url: 'data/getPlantillaPlanos.php',
            data: datos,
            headers: { "Content-Type": "multipart/form-data" },
        }).then(function (response) {
            $.notifyClose();
            let data = response.data;
            if (data.data) {
                $.each(data.data, function (index, value) {
                    addLiPlanos('#cardProyPlanos', value.PlanoID, value.PlanoDesc, value.PlanoCod)
                });
            }

        }).then(() => {

        }).catch(function (error) {
            alert(error);
        })
    }
    function bindSubmitPlanoProy() {
        $(document).on('click', '.submitPlanoProy', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if ($("#PlanoDesc").val() == "") {
                $.notifyClose();
                $("#PlanoDesc").focus().addClass("is-invalid");
                let textErr = `<span class="text-danger font-weight-bold">Ingrese una descripción del Plano<span>`;
                notify(textErr, "danger", 2000, "right");
                return;
            }
            let PlanoDesc = $("#PlanoDesc").val();
            let PlanoCod = $("#PlanoCod").val();
            let PlanoObs = $("#PlanoObs").val();
            let datos = new FormData();

            ActiveBTN(true, this, '<i class="bi bi-plus font15"></i>', '<i class="bi bi-plus font15"></i>');
            datos.append('PlanoDesc', PlanoDesc);
            datos.append('PlanoCod', PlanoCod);
            datos.append('PlanoObs', PlanoObs);
            datos.append('PlanoEsta', 'on');
            datos.append('PlanoSubmit', 'alta');           

            axios({
                method: "post",
                url: 'op/crud.php',
                data: datos,
                headers: { "Content-Type": "multipart/form-data" },
            }).then(function (response) {
                $.notifyClose();
                let data = response.data;
                if (data.status == 'ok') {
                    notify(data.Mensaje, 'success', 1000, 'right')
                    $("#PlanoDesc").val('');
                    $("#PlanoCod").val('');
                    $("#PlanoObs").val('');
                    addLiPlanos('#cardProyPlanos', data.dataPlano.id_plano, data.dataPlano.nombre_plano, data.dataPlano.PlanoCod)
                } else {
                    notify(data.Mensaje, 'danger', 1000, 'right')
                }
            }).then(() => {
                ActiveBTN(false, this, '<i class="bi bi-plus font15"></i>', '<i class="bi bi-plus font15"></i>');
            }).catch(function (error) {
                alert(error);
            })
        });
    }
    function bindForm(tipo) { //bindear formulario de alta/edicion
        $("#proyForm").bind("submit", function (e) {
            e.preventDefault();
            if ($("#ProyDesc").val() == ""
                || $("#ProyNom").val() == ""
                || $("#ProyIniFin").val() == ''
                || $("#ProyEmpr").val() == null
                || $("#ProyResp").val() == null
                || $("#ProyPlant").val() == null
                || $("#ProyEsta").val() == null
            ) {
                $.notifyClose();
                checkEmpty("#ProyNom");
                checkEmpty("#ProyDesc");
                checkEmpty("#ProyIniFin");
                checkEmpty("#ProyEmpr", "#select2-ProyEmpr-container");
                checkEmpty("#ProyResp", "#select2-ProyResp-container");
                checkEmpty("#ProyPlant", "#select2-ProyPlant-container");
                // checkEmpty("#ProyPlantPlanos", "#select2-ProyPlantPlanos-container");
                checkEmpty("#ProyEsta", "#select2-ProyEsta-container");

                let textErr = `<span class="text-danger">Campos requeridos<span>`;
                notify(textErr, "danger", 2000, "right");
                return;
            }
            let textSubmitProy = (tipo.split("&")[0] == 'mod') ? '<i class="bi bi-pencil me-2"></i>Editar Proyecto' : '<i class="bi bi-plus-lg me-2"></i>Crear Proyecto'
            $.ajax({
                type: $(this).attr("method"),
                url: "op/crud.php",
                data: $(this).serialize() + "&ProySubmit=" + tipo + "&ProyLiPlanos=" + getLiPlanos('#cardProyPlanos'),
                beforeSend: function (data) {
                    $.notifyClose();
                    notify("Aguarde <span class='animated-dots'></span>", "dark", 0, "right");
                    ActiveBTN(true, "#ProySubmit", "Aguarde <span class='animated-dots'></span>", textSubmitProy);
                    $(".is-invalid").removeClass("is-invalid");
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        notify(data.Mensaje, "success", 2000, "right")
                        $("#proyModal").fadeOut('slow');
                        // setTimeout((function () {
                        $("#proyModal").modal("hide");
                        // }), 500);
                        $("#tableProyectos").DataTable().ajax.reload(null, false);
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, "danger", 2000, "right");
                    }
                    ActiveBTN(false, "#ProySubmit", "Aguarde <span class='animated-dots'></span>", textSubmitProy);
                },
                error: function (data) {
                    ActiveBTN(false, "#ProySubmit", "Aguarde <span class='animated-dots'></span>", textSubmitProy);
                    $.notifyClose();
                    notify("Error", "danger", 3000, "right");
                }
            });
            e.stopImmediatePropagation();
            document.getElementById('proyModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                $("#modales").html('');
            })
        });
    } //fin bindForm
    tableProyectos.on("init.dt", function (e, settings) { // Cuando se inicializa la tabla
        let idTable = `#${e.target.id}`; // Se obtiene el id de la tabla
        if ($("#tableProyectos_filter input").val()){ // si el input searchbox de la tabla tiene contenido. Lo lomipiamos y digujamos la tabla nuevamente
            $(idTable).DataTable().search('').draw()
        }
        $("thead").remove(); // Se remueve el thead
        let lengthMenu = $(`${idTable}_length select`); // Se obtiene el select del lengthMenu
        $(lengthMenu).addClass("h50"); // Se agrega la clase h40 height: 50px
        let filterInput = $(`${idTable}_filter input`); // Se obtiene el input del filtro
        $(filterInput).attr({ placeholder: "Buscar proyecto.." }).addClass("p-3");  // Se agrega placeholder al input y se agrega clase p-3.
        $(".divAltaProy").html( // Se agrega el boton de alta de proyecto
            `<button type="button" data-titlel="Actualizar Grilla" class="btn btn-link font08" id="btnActualizarGrillaProy"><i class="bi bi-arrow-clockwise font12"></i></button>
            <button type="button" data-titlel="Nuevo Proyecto" class="btn btn-tabler h50 shadow" id="btnAltaProyecto"><i class="bi bi-plus font12"></i></button>`
        );
        $(idTable).removeClass("invisible"); // Se remueve la clase invisible de la tabla
        $(idTable + " tbody").on("click", ".editProy", function (e) { // Se agrega el evento click al boton editar
            e.preventDefault();
            $.notifyClose() // Se cierra el notify
            let dataRow = $(idTable).DataTable().row($(this).parents("tr")).data(); // Se obtiene la fila seleccionada en la tabla
            // console.log(dataRow);
            fetch(`op/proyModal.php?${Date.now()}`) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text()) // Se obtiene la respuesta
                .then(data => {  // Se obtiene el html del modal
                    // console.log(HtmlEncode(dataRow.ProyObs));
                    $("#modales").html(data); // Se agrega el html al modal
                    $("#modales .form-control").attr("autocomplete", "off"); // Se agrega el atributo autocomplete
                    let proyModal = new bootstrap.Modal(document.getElementById("proyModal"), { keyboard: true }); // Se inicializa el modal
                    $("#proyModal .modal-title").html("EDITAR PROYECTO"); // Se agrega el titulo del modal
                    $("#proyModal #ProyDesc").val(decodeEntities(dataRow.ProyData.Desc)); // Se agrega el desc del proyecto
                    $("#proyModal #ProyNom").val(decodeEntities(dataRow.ProyData.Nombre)); // Se agrega el nombre del proyecto
                    $("#proyModal #ProyObs").val(decodeEntities(dataRow.ProyData.Obs)); // Se agrega el obs de la observacion
                    select2Value(dataRow.ProyEmpr.ID, decodeEntities(dataRow.ProyEmpr.Nombre), '#ProyEmpr');
                    select2Value(dataRow.ProyResp.ID, decodeEntities(dataRow.ProyResp.Nombre), '#ProyResp');
                    select2Value(dataRow.ProyPlant.ID, decodeEntities(dataRow.ProyPlant.Nombre), '#ProyPlant');
                    if (dataRow.ProyPlantPlano.ID) {
                        select2Value(dataRow.ProyPlantPlano.ID, decodeEntities(dataRow.ProyPlantPlano.Nombre), '#ProyPlantPlanos');
                        setCardProyPlanos(dataRow.ProyPlantPlano.ID)
                    }
                    select2Value(dataRow.ProyEsta.ID, decodeEntities(dataRow.ProyEsta.Nombre), '#ProyEsta');
                    $("#proyModal #ProyIniFin").prop('disabled', true);
                    $("#proyModal #ProyEmpr").prop('disabled', true);
                    $("#proyModal #form-group-Empr .select2-selection__arrow").hide();


                    let FechaIni = dataRow.ProyFech.Inicio.split("-"); //fecha inicio
                    let FechaFin = dataRow.ProyFech.Fin.split("-"); //fecha fin 
                    FechaIni = `${FechaIni[2]}/${FechaIni[1]}/${FechaIni[0]}`;  // formato fecha inicio
                    FechaFin = `${FechaFin[2]}/${FechaFin[1]}/${FechaFin[0]}`; // formato fecha fin

                    ActiveBTN(false, "#ProySubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-pencil me-2"></i>Editar Proyecto'); // Se desactiva el boton de submit
                    proyModal.show(); // Se muestra el modal
                    document.getElementById('proyModal').addEventListener('shown.bs.modal', function (event) { // Se agrega el evento shown.bs.modal al Modal
                        $('#ProyIniFin').daterangepicker({
                            singleDatePicker: false,
                            showDropdowns: false,
                            showWeekNumbers: false,
                            autoUpdateInput: true,
                            opens: "left",
                            autoApply: true,
                            startDate: FechaIni,
                            endDate: FechaFin,
                            linkedCalendars: false,
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

                        $("#ProyNom").focus();
                        $("#proyModal #ProyIniFin").prop('disabled', false);
                        $("#proyModal #select2-ProyEmpr-container").attr('title', 'No se puede modificar la empresa');
                        $("#proyModal #form-group-Empr .select2-selection__arrow").fadeOut().hide();
                        $('#proyModal .modal-footer #divSubmit').prepend(`<button type="button" class="btn btn-outline-pinterest h50 me-2" id="ProySubmitdelete"><i class="bi bi-trash"></i></button>`); // Se agrega el boton de eliminar

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

                        $("#ProyEmpr").select2({
                            language: "es",
                            multiple: false,
                            allowClear: opt2["allowClear"],
                            language: "es",
                            placeholder: "Empresa",
                            dropdownParent: $('#proyModal'),
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
                            dropdownParent: $('#proyModal'),
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
                        $("#ProyPlantPlanos").select2({
                            language: "es",
                            multiple: false,
                            allowClear: opt2["allowClear"],
                            language: "es",
                            placeholder: "Plantilla planos",
                            dropdownParent: $('#proyModal'),
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
                                url: "../proy/data/select/selPlantillaPlano.php",
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
                            dropdownParent: $('#proyModal'),
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
                        $("#ProyResp").select2({
                            language: "es",
                            multiple: false,
                            allowClear: opt2["allowClear"],
                            language: "es",
                            placeholder: "Responsable del proyecto",
                            dropdownParent: $('#proyModal'),
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
                        $("#ProyAsignPlanos").select2({
                            language: "es",
                            multiple: false,
                            allowClear: true,
                            language: "es",
                            placeholder: "Seleccionar plano",
                            dropdownParent: $('#proyModal'),
                            templateResult: template,
                            // templateSelection: template,
                            minimumInputLength: 0,
                            minimumResultsForSearch: 0,
                            maximumInputLength: 10,
                            selectOnClose: false,
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
                                url: "../proy/data/select/selPlanos.php",
                                dataType: "json",
                                type: "POST",
                                delay: opt2["delay"],
                                data: function (params) {
                                    planos = new Array()
                                    $("#cardProyPlanos input").each(function (index, element) {
                                        (planos.push(parseInt(element.value)));
                                    });
                                    return {
                                        q: params.term,
                                        notPlano: planos
                                    };
                                },
                                processResults: function (data) {
                                    return {
                                        results: data,
                                    };
                                },
                            },
                        });

                        bindForm(`mod&ProyID=${dataRow.ProyData.ID}&ProyEmpr=${dataRow.ProyEmpr.Nombre}`)  // Se bindea el formulario
                        $('#ProySubmitdelete').click(function () { // Se agrega el evento click al boton de eliminar
                            $("#proyModal").modal("hide"); // Se oculta el modal
                            fetch(`op/proyModal.php?${Date.now()}`) // Se hace la peticion ajax para obtener el modal
                                .then(response => response.text()) // Se obtiene la respuesta
                                .then(data => { // Se obtiene el html del modal
                                    $("#modales").html(data); // Se agrega el html al modal
                                    $("#proyModal .modal-content").html('') // Se remueve el contenido del modal
                                    $("#proyModal .modal-dialog").removeClass('modal-xl') // Se remueve el contenido del modal
                                    // $("#proyModal .modal-dialog").addClass('modal-dialog-centered') // Se agrega la clase modal-dialog-centered
                                    let proyModal = new bootstrap.Modal(document.getElementById("proyModal"), { keyboard: true }); // Se inicializa el modal
                                    $("#proyModal .modal-content").html(`
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        <div class="modal-status bg-danger"></div>
                                        <div class="modal-body text-center py-4">
                                            <i class="bi bi-exclamation-triangle font20 text-pinterest"></i>
                                            <h2>¿Desea eliminar el proyecto<br>(#${dataRow.ProyData.ID}) ${dataRow.ProyData.Desc}?</h2>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="w-100">
                                                <div class="row">
                                                    <div class="col">
                                                        <a href="#" class="btn btn-white w-100 h50" data-bs-dismiss="modal">Cancelar</a>
                                                    </div>
                                                    <div class="col">
                                                        <a href="#" class="btn btn-danger w-100 h50" data-bs-dismiss="modal" id="proyConfirmDelete">Eliminar</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `); // Se agrega el html del modal de confirmacion de eliminacion
                                    proyModal.show(); // Se muestra el modal
                                    $('#proyConfirmDelete').click(function (e) { // Se agrega el evento click al boton de confirmar eliminar
                                        e.preventDefault(); // Se evita el evento click
                                        $.ajax({ // Se hace la peticion ajax para eliminar el proyecto
                                            type: 'POST', // Se envia como POST
                                            url: "op/crud.php",  // Se envia a la ruta del crud
                                            data: `ProySubmit=baja&ProyID=${dataRow.ProyData.ID}&ProyDesc=${dataRow.ProyData.Desc}`,
                                            beforeSend: function (data) { // Antes de enviar la peticion
                                                $.notifyClose(); // Se cierra el notify
                                                notify("Aguarde <span class='animated-dots'></span>", "dark", 0, "right"); // Se muestra el notify
                                                ActiveBTN(true, "#ProySubmitdelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se desactiva el boton de submit
                                            },
                                            success: function (data) {  // Si la peticion es correcta
                                                if (data.status == "ok") { // Si el status es ok
                                                    $.notifyClose(); // Se cierra el notify
                                                    notify(data.Mensaje, "success", 2000, "right") // Se muestra el notify
                                                    $("#proyModal").fadeOut('slow');
                                                    setTimeout((function () {
                                                        $("#proyModal").modal("hide"); // Se oculta el modal
                                                    }), 500); // Se agrega un setTimeout para que el notify se muestre despues de 0.5 segundos
                                                    $("#tableProyectos").DataTable().ajax.reload(null, false); // Se recarga la tabla
                                                } else {  // Si el status no es ok
                                                    $.notifyClose(); // Se cierra el notify
                                                    notify(data.Mensaje, "danger", 2000, "right"); // Se muestra el notify con el mensaje de error
                                                }
                                                ActiveBTN(false, "#ProySubmitdelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se activa el boton de submit
                                            },
                                            error: function (data) {
                                                ActiveBTN(false, "#ProySubmitdelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se activa el boton de submit
                                                $.notifyClose(); // Se cierra el notify
                                                notify("Error", "danger", 3000, "right"); // Se muestra el notify con el mensaje de error
                                            }
                                        }); // Se termina la peticion ajax
                                    }); // Se termina el evento click del boton de confirmar eliminar
                                    document.getElementById('proyModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                                        $("#modales").html('');
                                    })
                                }); // Se termina el then de la peticion ajax
                        });

                        bindSubmitPlanoProy();

                        $('#ProyAsignPlanos').on('select2:select', function (e) {
                            let data = e.params.data
                            addLiPlanos('#cardProyPlanos', data.id, data.text, data.cod)
                            $('#ProyAsignPlanos').val('').trigger('change')
                        });
                        $("#ProyPlantPlanos").on('select2:select', function (e) {
                            let dataPlantilla = e.params.data
                            setCardProyPlanos(dataPlantilla.id)
                        });
                        $("#ProyPlantPlanos").on('select2:clear', function (e) {
                            setCardProyPlanos('')
                        });

                        autosize($('textarea'));

                    });

                });
        });
        $(idTable + " tbody").on("click", ".viewProy", function (e) { // Se agrega el evento click al hacer click en la descripción del proyecto
            e.preventDefault();
            $.notifyClose() // Se cierra el notify
            let dataRow = $(idTable).DataTable().row($(this).parents("tr")).data(); // Se obtiene el dato de la fila
            fetch(`op/proyModal.php?${Date.now()}`) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text()) // Se obtiene la respuesta
                .then(data => { // Se obtiene el html del modal
                    $("#modales").html(data); // Se agrega el html al modal
                    let proyModal = new bootstrap.Modal(document.getElementById("proyModal"), { keyboard: !0 }); // Se inicializa el modal
                    $("#proyModal .modal-title").html(dataRow.ProyDesc); // Se agrega el titulo del modal
                    $("#proyModal .modal-dialog").addClass('modal-fullscreen-sm-down modal-xl'); // Se agrega la clase modal-fullscreen-sm-down
                    let ProcObs = dataRow.ProcObs; // Se obtiene la observacion del proyecto
                    let renderProcObs = ProcObs.replace(/(?:\r\n|\r|\n)/g, "<br>"); // Se reemplaza el salto de linea por una etiqueta de salto de linea html
                    $("#proyModal .modal-body").html('<div class="card"><div class="card-body"><p>Costo: ' + dataRow.ProcCost + '</p><p><div class="mb-2">Observaciones:</div>' + renderProcObs + "</p>                            </div>\n                        </div>\n                        ");
                    $("#proyModal #ProySubmit").remove(); // Se remueve el boton de submit
                    ActiveBTN(true, "#ProySubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-pencil me-2"></i>Editar Proyecto'); // Se desactiva el boton de submit
                    proyModal.show(); // Se muestra el modal
                    document.getElementById('proyModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                        $("#modales").html('');
                    })
                });
        });
        $("#btnActualizarGrillaProy").click(function () { // Se agrega el evento click al boton de actualizar grilla
            $("#tableProyectos").DataTable().ajax.reload(); // Se recarga la tabla
        });
        $("#btnAltaProyecto").click(function () { // Se agrega el evento click al boton de alta de proyecto
            $.notifyClose() // Se cierra el notify
            fetch(`op/proyModal.php?${Date.now()}`) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text())  // Se obtiene la respuesta  
                .then(data => { // Se obtiene el html del modal
                    $("#modales").html(data); // Se agrega el html al modal
                    $("#modales .form-control").attr("autocomplete", "off"); // Se desactiva el autocomplete
                    maskCosto('#ProcCost'); // Se agrega el máscara al input
                    var proyModal = new bootstrap.Modal(document.getElementById("proyModal"), { keyboard: true }); // Se inicializa el modal
                    ActiveBTN(false, "#ProySubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2"></i>Crear Proyecto'); // Se desactiva el boton de submit
                    proyModal.show(); // Se muestra el modal
                    setTimeout(() => {
                        $("#ProyNom").focus();
                    }, 500); // Se agrega un setTimeout para que el focus se haga despues de 0.5 segundos
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

                    $("#ProyEmpr").select2({
                        language: "es",
                        multiple: false,
                        allowClear: opt2["allowClear"],
                        language: "es",
                        placeholder: "Empresa",
                        dropdownParent: $('#proyModal'),
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
                        dropdownParent: $('#proyModal'),
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
                    $("#ProyPlantPlanos").select2({
                        language: "es",
                        multiple: false,
                        allowClear: opt2["allowClear"],
                        language: "es",
                        placeholder: "Plantilla planos",
                        dropdownParent: $('#proyModal'),
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
                            url: "../proy/data/select/selPlantillaPlano.php",
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
                        dropdownParent: $('#proyModal'),
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
                    $("#ProyResp").select2({
                        language: "es",
                        multiple: false,
                        allowClear: opt2["allowClear"],
                        language: "es",
                        placeholder: "Responsable del proyecto",
                        dropdownParent: $('#proyModal'),
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
                    $("#ProyAsignPlanos").select2({
                        language: "es",
                        multiple: false,
                        allowClear: true,
                        language: "es",
                        placeholder: "Seleccionar plano",
                        dropdownParent: $('#proyModal'),
                        templateResult: template,
                        // templateSelection: template,
                        minimumInputLength: 0,
                        minimumResultsForSearch: 0,
                        maximumInputLength: 10,
                        selectOnClose: false,
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
                            url: "../proy/data/select/selPlanos.php",
                            dataType: "json",
                            type: "POST",
                            delay: opt2["delay"],
                            data: function (params) {
                                planos = new Array()
                                $("#cardProyPlanos input").each(function (index, element) {
                                    (planos.push(parseInt(element.value)));
                                });
                                return {
                                    q: params.term,
                                    notPlano: planos
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: data,
                                };
                            },
                        },
                    });

                    $('#ProyEmpr').on("select2:select", function (e) {
                    });

                    $('#ProyIniFin').on('show.daterangepicker', function (ev, picker) {
                        $.notifyClose();
                        notify("Seleccione una Fecha de Inicio y Fin", "info", 0, "right");
                    });
                    $('#ProyIniFin').on('hide.daterangepicker', function (ev, picker) {
                        $.notifyClose();
                    });

                    $('#ProyIniFin').daterangepicker({
                        singleDatePicker: false,
                        showDropdowns: false,
                        showWeekNumbers: false,
                        autoUpdateInput: true,
                        opens: "left",
                        autoApply: true,
                        linkedCalendars: false,
                        ranges: {
                            // 'Hoy': [moment(), moment()],
                            'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                            'Esta semana': [moment().day(1), moment().day(7)],
                            'Semana Anterior': [moment().subtract(1, 'week').day(1), moment().subtract(1, 'week').day(7)],
                            'Próxima Semana': [moment().add(1, 'week').day(1), moment().add(1, 'week').day(7)],
                            'Próximo Mes': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
                            'Este mes': [moment().startOf('month'), moment().endOf('month')],
                            // 'Mes anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                            // 'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
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
                    bindForm('alta') // Se llama la funcion bindForm para hacer el submit del formulario
                    document.getElementById('proyModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                        $("#modales").html('');
                    })
                    // $("#ProyAsignPlanos").append($('<option>', { 'value': "9999", 'text': "New Option Text" }));
                    let arrPlanAsign = new Array();
                    let TotalPlanosAsignados = 0
                    $('#TotalPlanosAsignados').html(TotalPlanosAsignados)

                    bindSubmitPlanoProy();

                    $('#ProyAsignPlanos').on('select2:select', function (e) {
                        // console.log(e.params.data);
                        let data = e.params.data
                        // arrPlanAsign.push(data);
                        addLiPlanos('#cardProyPlanos', data.id, data.text, data.cod)
                        $('#ProyAsignPlanos').val('').trigger('change')
                    });
                    $("#ProyPlantPlanos").on('select2:select', function (e) {
                        let dataPlantilla = e.params.data
                        setCardProyPlanos(dataPlantilla.id)
                    });
                    $("#ProyPlantPlanos").on('select2:clear', function (e) {
                        setCardProyPlanos('')
                    });

                    autosize($('textarea'));

                })
        });

        fetch(`op/proyDivEstados.html?${Date.now()}`) // Se hace la peticion ajax para obtener el modal
            .then(response => response.text()) // Se obtiene la respuesta
            .then(data => {
                $(".divEstados").html(data); // Se agrega el html al modal
                $('.divEstados').on("click", ".form-selectgroup-input", function (e) {
                    let valor = $(this).val();
                    $("#tableProyectos").DataTable().ajax.reload();
                });
            });
        $(".divFiltrosProy").html(`
            <button type="button" data-titlel="Filtros" class="shadow-sm ms-1 btn btn-outline-tabler h50 shadow" id="ProyShowFiltros" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFiltros" aria-controls="offcanvasFiltros"><i class="bi bi-filter font12"></i></button>
            <button class="shadow-sm btn btn-outline-info h50 font08 ProyLimpiaFiltro" data-titler="Limpiar Filtros"><i class="bi bi-eraser font1"></i></button>
        `)
        fetch(`op/proyFiltros.php?${Date.now()}`) // Se hace la peticion ajax para obtener el modal
            .then(response => response.text()) // Se obtiene la respuesta
            .then(data => {
                $("#divFiltros").html(data); // Se agrega el html al modal
                // $('.divFiltrosProy').on("click", "#ProyShowFiltros", function (e) {
                //     var proyFiltrosModal = new bootstrap.Modal(document.getElementById("proyFiltrosModal"), { keyboard: true }); // Se inicializa el modal
                //     proyFiltrosModal.show(); // Se muestra el modal
                // });
                // document.getElementById('proyFiltrosModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                // })
            });

    });
    tableProyectos.on("page.dt", function (e, settings) { // Se agrega el evento page.dt para que se ejecute cuando se cambie de pagina
        let idTable = "#" + e.target.id; // Se obtiene el id de la tabla
        $(idTable + " div").addClass("pre-div"); // Se agrega la clase pre-div a la div de la tabla
    });
    tableProyectos.on("draw.dt", function (e, settings) { // Se agrega el evento draw.dt para que se ejecute cuando se redibuje la tabla
        let idTable = "#" + e.target.id; // Se obtiene el id de la tabla
        $(idTable + " div").removeClass("pre-div"); // Se remueve la clase pre-div a la div de la tabla
        $("#tableProyectos thead").remove(); // Se remueve el thead de la tabla
    });
    tableProyectos.on("xhr.dt", function (e, settings) {
        tableProyectos.off('xhr.dt');
    });
    $.fn.DataTable.ext.pager.numbers_length = 5; // Se agrega el numero de paginas a mostrar en el paginador
});
