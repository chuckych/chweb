$(function () {
    "use strict"; // Start of use strict
    let tableEstados = $("#tableEstados").dataTable({ //inicializar datatable
        lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]], //mostrar cantidad de registros
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        responsive: true,
        dom:
            "<'row mt-3'<'col-12 col-sm-6 d-flex justify-content-start d-none d-sm-block'l><'col-12 col-sm-6 d-flex justify-content-end'<'divAltaEst'>f>>" +
            "<'row'<'col-12 table-responsive't>" +
            "<'col-12 d-flex justify-content-end'p>"+
            "<'col-12 d-flex justify-content-end'i>>",
        ajax: {
            url: "data/getEstados.php?" + $.now(),
            type: "POST",
            dataType: "json",
            data: function (data) {
                // data._eg = $("input[name=_eg]:checked").val();
            },
            error: function () {
                $("#tableEstados").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass("animate__animated animate__fadeIn");
        },
        columns: [
            {
                className: "p-0 pb-1",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                   
                        let datacol =
                        `
                        <div class="form-selectgroup-item flex-fill">
                            <div class="card-body border border-end-0 p-3 animate__animated animate__fadeIn h80">
                                    <span class="font-weight-bold">${row["EstDesc"]}</span>
                            <div class="divColor shadow-sm mt-2" style="height:15px; width:120px;background-color: `+row["EstColor"]+`"></div>
                        </div>
                        `;
                    return datacol;
                }
            },
            {
                className: "w-100 p-0 pb-1",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let icon = "";
                    let iconText = "";
                    switch (row.EstTipo) {
                        case 'Abierto':
                            icon = `<i class="bi bi-play"></i>`;
                            iconText = 'Abierto'
                            break;
                        case 'Pausado':
                            icon = `<i class="bi bi-pause"></i>`;
                            iconText = 'Pausado'
                            break;
                        case 'Cerrado':
                            icon = `<i class="bi bi-stop"></i>`;
                            iconText = 'Cerrado'
                            break;
                        default:
                            icon = `<i class="bi bi-play"></i>`;
                            iconText = 'Abierto'
                            break;
                    }
                        let datacol =
                        `
                        <div class="form-selectgroup-item flex-fill">
                            <div class="card-body border border-start-0 p-3 animate__animated animate__fadeIn h80">
                                <div class='d-flex justify-content-between'>
                                    <div class="d-flex align-items-center"><span class="me-2">${icon}</span><span class="font08 text-mutted">${iconText}</span></div>
                                    <div><span><button type="button" class="btn p-2 btn-outline-teal bi bi-pencil editEst"></button></span></div>
                                </div>
                                
                        </div>
                        `;
                    return datacol;
                }
            }
        ],
        searchDelay: 350,
        // scrollY: "430px",
        paging: true,
        searching: true,
        info: true,
        ordering: 0,
        responsive: 0,
        language: {
            url: `../js/DataTableSpanishShort2.json?${Date.now()}`
        }
    });
    function bindFormEmpresa(tipo) { //bindear formulario de alta/edicion
        $("#estForm").bind("submit", function (e) {
            e.preventDefault();
            if ($("#EstDesc").val() == "") {
                $.notifyClose() // Se cierra el notify
                $("#EstDesc").focus().addClass("is-invalid");
                let textErr = `<span class="text-danger font-weight-bold">Ingresa una descripción de estado<span>`;
                notify(textErr, "danger", 2000, "right");
                return;
            }
            $.ajax({
                type: $(this).attr("method"),
                url: "op/crud.php",
                data: $(this).serialize() + "&EstSubmit=" + tipo,
                beforeSend: function (data) {
                    $.notifyClose();
                    notify("Aguarde <span class='animated-dots'></span>", "dark", 0, "right");
                    ActiveBTN(!0, "#EstSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="EstSubmit"></i>Crear Estado');
                    $(".is-invalid").removeClass("is-invalid");
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        // $("#modales").addClass('animate__animated animate__fadeOut');
                        $("#estModal").fadeOut('slow');
                        notify(data.Mensaje, "success", 5000, "right", 'top', false)
                        setTimeout((function () {
                            $("#estModal").modal("hide");
                        }), 300);
                        $("#tableEstados").DataTable().ajax.reload(null, false);
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, "danger", 2000, "right");
                    }
                    ActiveBTN(!1, "#EstSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="EstSubmit"></i>Crear Estado');
                },
                error: function (data) {
                    ActiveBTN(!1, "#EstSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="EstSubmit"></i>Crear Estado');
                    $.notifyClose();
                    notify("Error", "danger", 3000, "right");
                }
            });
            e.stopImmediatePropagation();
            document.getElementById('estModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                $("#modales").html('');
            })
        });
    } //fin bindFormEmpresa
    tableEstados.on("init.dt", function (e, settings) { // Cuando se inicializa la tabla
        let idTable = "#" + e.target.id; // Se obtiene el id de la tabla
        $("thead").remove(); // Se remueve el thead
        let lengthMenu = $(idTable + "_length select"); // Se obtiene el select del lengthMenu
        $(lengthMenu).addClass("h50"); // Se agrega la clase h40 height: 50px
        let filterInput = $(idTable + "_filter input"); // Se obtiene el input del filtro
        $(filterInput).attr({ placeholder: "Buscar estado.." }).addClass("p-3");  // Se agrega placeholder al input y se agrega clase p-3.
        $(".divAltaEst").html( // Se agrega el boton de alta de empresa
            `<button type="button" data-titlel="Nuevo Estado" class="btn btn-tabler h50 shadow" id="btnAltaEstado"><i class="bi bi-plus font12"></i></button>`
        );
        $(idTable).removeClass("invisible"); // Se remueve la clase invisible de la tabla
        $(idTable + " tbody").on("click", ".editEst", function (e) { // Se agrega el evento click al boton editar
            e.preventDefault();
            $.notifyClose() // Se cierra el notify
            let dataRow = $(idTable).DataTable().row($(this).parents("tr")).data(); // Se obtiene la fila seleccionada en la tabla
            // console.log(dataRow);
            fetch("op/estModal.html?" + $.now()) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text()) // Se obtiene la respuesta
                .then(data => {  // Se obtiene el html del modal
                    $("#modales").html(data); // Se agrega el html al modal
                    $("#modales .form-control").attr("autocomplete", "off"); // Se agrega el atributo autocomplete
                    let estModal = new bootstrap.Modal(document.getElementById("estModal"), { keyboard: true }); // Se inicializa el modal
                    $("#estModal .modal-title").html("Editar Estado"); // Se agrega el titulo del modal
                    $("#estModal #EstDesc").val(decodeEntities(dataRow.EstDesc)); // Se agrega el valor de la empresa
                    $("#estModal #EstColor").val((dataRow.EstColor)); // Se agrega el valor del telefono
                    ActiveBTN(!1, "#EstSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-pencil me-2"></i>Editar Estado'); // Se desactiva el boton de submit
                    // console.log(dataRow);
                    switch (dataRow.EstTipo) {
                        case 'Abierto':
                            $("#estModal #EstTipoAbierto").prop('checked',true); // Se marca el radio de comportamiento Abierto
                            break;
                        case 'Pausado':
                            $("#estModal #EstTipoPausado").prop('checked',true);  // Se marca el radio de comportamiento Pausado
                            break;
                        case 'Cerrado':
                            $("#estModal #EstTipoCerrado").prop('checked',true);  // Se marca el radio de comportamiento Cerrado
                            break;
                        default:
                            $("#estModal #EstTipoAbierto").prop('checked',true); // Se marca el radio de comportamiento Abierto
                            break;
                    }
                    estModal.show(); // Se muestra el modal
                    setTimeout(() => {
                        $("#EstDesc").focus(); // Se posiciona el cursor en el input de la descripcion de la empresa
                    }, 500);
                    $('#estModal .modal-footer #divSubmit').prepend(`<button type="button" class="btn btn-outline-pinterest h50 me-2" id="EstSubmitDelete"><i class="bi bi-trash"></i></button>`); // Se agrega el boton de eliminar
                    bindFormEmpresa('mod&EstID=' + dataRow.EstID)  // Se bindea el formulario
                    $('#EstSubmitDelete').click(function () { // Se agrega el evento click al boton de eliminar
                        $("#estModal").modal("hide"); // Se oculta el modal
                        fetch("op/estModal.html?" + $.now()) // Se hace la peticion ajax para obtener el modal
                            .then(response => response.text()) // Se obtiene la respuesta
                            .then(data => { // Se obtiene el html del modal
                                $("#modales").html(data); // Se agrega el html al modal
                                $("#estModal .modal-content").html('') // Se remueve el contenido del modal
                                // $("#estModal .modal-dialog").addClass('modal-dialog-centered') // Se agrega la clase modal-dialog-centered
                                let estModal = new bootstrap.Modal(document.getElementById("estModal"), { keyboard: true }); // Se inicializa el modal
                                $("#estModal .modal-content").html(`
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        <div class="modal-status bg-danger"></div>
                                        <div class="modal-body text-center py-4">
                                            <i class="bi bi-exclamation-triangle font20 text-pinterest"></i>
                                            <h2>¿Desea eliminar el estado<br>`+ dataRow.EstDesc + `?</h2>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="w-100">
                                                <div class="row">
                                                    <div class="col">
                                                        <a href="#" class="btn btn-white w-100 h50" data-bs-dismiss="modal">Cancelar</a>
                                                    </div>
                                                    <div class="col">
                                                        <a href="#" class="btn btn-danger w-100 h50" data-bs-dismiss="modal" id="EstConfirmDelete">Eliminar</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `); // Se agrega el html del modal de confirmacion de eliminacion
                                estModal.show(); // Se muestra el modal
                                $('#EstConfirmDelete').click(function (e) { // Se agrega el evento click al boton de confirmar eliminar
                                    e.preventDefault(); // Se evita el evento click
                                    $.ajax({ // Se hace la peticion ajax para eliminar la empresa
                                        type: 'POST', // Se envia como POST
                                        url: "op/crud.php",  // Se envia a la ruta del crud
                                        data: `EstSubmit=baja&EstID=${dataRow.EstID}&EstDesc=${dataRow.EstDesc}`,
                                        beforeSend: function (data) { // Antes de enviar la peticion
                                            $.notifyClose(); // Se cierra el notify
                                            notify("Aguarde <span class='animated-dots'></span>", "dark", 0, "right"); // Se muestra el notify
                                            ActiveBTN(true, "#EstSubmitDelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se desactiva el boton de submit
                                        },
                                        success: function (data) {  // Si la peticion es correcta
                                            if (data.status == "ok") { // Si el status es ok
                                                $.notifyClose(); // Se cierra el notify
                                                $("#estModal").fadeOut('slow'); // Se oculta el modal
                                                notify(data.Mensaje, "success", 2000, "right") // Se muestra el notify
                                                setTimeout((function () {
                                                    $("#estModal").modal("hide"); // Se oculta el modal
                                                }), 300); // Se agrega un setTimeout para que el notify se muestre despues de 0.5 segundos
                                                $("#tableEstados").DataTable().ajax.reload(null, false); // Se recarga la tabla
                                            } else {  // Si el status no es ok
                                                $.notifyClose(); // Se cierra el notify
                                                notify(data.Mensaje, "danger", 2000, "right"); // Se muestra el notify con el mensaje de error
                                            }
                                            ActiveBTN(false, "#EstSubmitDelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se activa el boton de submit
                                        },
                                        error: function (data) {
                                            ActiveBTN(false, "#EstSubmitDelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se activa el boton de submit
                                            $.notifyClose(); // Se cierra el notify
                                            notify("Error", "danger", 3000, "right"); // Se muestra el notify con el mensaje de error
                                        }
                                    }); // Se termina la peticion ajax
                                }); // Se termina el evento click del boton de confirmar eliminar
                                document.getElementById('estModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                                    $("#modales").html('');
                                })
                            }); // Se termina el then de la peticion ajax
                    });
                });
        });
        $("#btnAltaEstado").click(function () { // Se agrega el evento click al boton de alta de empresa
            $.notifyClose() // Se cierra el notify
            fetch("op/estModal.html?" + $.now()) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text())  // Se obtiene la respuesta  
                .then(data => { // Se obtiene el html del modal
                    $("#modales").html(data); // Se agrega el html al modal
                    $("#modales .form-control").attr("autocomplete", "off"); // Se desactiva el autocomplete
                    var estModal = new bootstrap.Modal(document.getElementById("estModal"), { keyboard: true }); // Se inicializa el modal
                    ActiveBTN(false, "#EstSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="EstSubmit"></i>Crear Estado'); // Se desactiva el boton de submit
                    estModal.show(); // Se muestra el modal
                    setTimeout(() => {
                        $("#EstDesc").focus();
                    }, 500); // Se agrega un setTimeout para que el focus se haga despues de 0.5 segundos
                    bindFormEmpresa('alta') // Se llama la funcion bindFormEmpresa para hacer el submit del formulario
                    document.getElementById('estModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                        $("#modales").html('');
                    })
                });
        });
    });
    tableEstados.on("page.dt", function (e, settings) { // Se agrega el evento page.dt para que se ejecute cuando se cambie de pagina
        let idTable = "#" + e.target.id; // Se obtiene el id de la tabla
        $(idTable + " div").addClass("pre-div"); // Se agrega la clase pre-div a la div de la tabla
    });
    tableEstados.on("draw.dt", function (e, settings) { // Se agrega el evento draw.dt para que se ejecute cuando se redibuje la tabla
        let idTable = "#" + e.target.id; // Se obtiene el id de la tabla
        $(idTable + " div").removeClass("pre-div"); // Se remueve la clase pre-div a la div de la tabla
        $("#tableEstados thead").remove(); // Se remueve el thead de la tabla
    });
    $.fn.DataTable.ext.pager.numbers_length = 5; // Se agrega el numero de paginas a mostrar en el paginador
});
