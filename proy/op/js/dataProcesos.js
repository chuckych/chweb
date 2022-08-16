$(function () {
    "use strict"; // Start of use strict
    let table = $("#tableProcesos").dataTable({ //inicializar datatable
        lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]], //mostrar cantidad de registros
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        responsive: true,
        dom:
            "<'row mt-3'<'col-12 col-sm-6 d-flex justify-content-start d-none d-sm-block'l><'col-12 col-sm-6 d-flex justify-content-end'<'divAltaProc'>f>>" +
            "<'row'<'col-12 table-responsive't>" +
            "<'col-12 d-flex justify-content-end'p>" +
            "<'col-12 d-flex justify-content-end'i>>",
        ajax: {
            url: "data/getProcesos.php?" + $.now(),
            type: "POST",
            dataType: "json",
            data: function (data) {
                // data._eg = $("input[name=_eg]:checked").val();
            },
            error: function () {
                $("#tableProcesos").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass("animate__animated animate__fadeIn");
        },
        columns: [
            {
                className: "w-100 p-0 pb-1",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                        <div class="form-selectgroup-item flex-fill">
                            <div class="card-body border p-3 animate__animated animate__fadeIn ">
                                <div class='d-flex justify-content-between'>
                                    <div class="viewProc cursor-pointer btn-link"><span>${row["ProcDesc"]}</span></div>
                                    <div><span><button type="button" class="btn p-2 btn-outline-teal bi bi-pencil editProc"></button></span></div>
                                </div>
                            <div><span>Costo: ${row["ProcCost"]}</span></div>
                        </div>
                        `;
                    return datacol;
                }
            }
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
    function bindForm(tipo) { //bindear formulario de alta/edicion
        $("#procForm").bind("submit", function (e) {
            e.preventDefault();
            if ($("#ProcDesc").val() == "") {
                $.notifyClose();
                $("#ProcDesc").focus().addClass("is-invalid");
                let textErr = `<span class="text-danger font-weight-bold">Ingrese un Proceso<span>`;
                notify(textErr, "danger", 2000, "right");
                return;
            }
            $.ajax({
                type: $(this).attr("method"),
                url: "op/crud.php",
                data: $(this).serialize() + "&ProcSubmit=" + tipo,
                beforeSend: function (data) {
                    $.notifyClose();
                    notify("Aguarde <span class='animated-dots'></span>", "dark", 0, "right");
                    ActiveBTN(true, "#ProcSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="ProcSubmit"></i>Crear Proceso');
                    $(".is-invalid").removeClass("is-invalid");
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        notify(data.Mensaje, "success", 2000, "right")
                        $("#procModal").fadeOut('slow');
                        // setTimeout((function () {
                        $("#procModal").modal("hide");
                        // }), 500);
                        $("#tableProcesos").DataTable().ajax.reload(null, false);
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, "danger", 2000, "right");
                    }
                    ActiveBTN(false, "#ProcSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="ProcSubmit"></i>Crear Proceso');
                },
                error: function (data) {
                    ActiveBTN(false, "#ProcSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="ProcSubmit"></i>Crear Proceso');
                    $.notifyClose();
                    notify("Error", "danger", 3000, "right");
                }
            });
            e.stopImmediatePropagation();
            document.getElementById('procModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                $("#modales").html('');
            })
        });
    } //fin bindForm
    table.on("init.dt", function (e, settings) { // Cuando se inicializa la tabla
        let idTable = `#${e.target.id}`; // Se obtiene el id de la tabla
        $("thead").remove(); // Se remueve el thead
        let lengthMenu = $(`${idTable}_length select`); // Se obtiene el select del lengthMenu
        $(lengthMenu).addClass("h50"); // Se agrega la clase h40 height: 50px
        let filterInput = $(`${idTable}_filter input`); // Se obtiene el input del filtro
        $(filterInput).attr({ placeholder: "Buscar proceso.." }).addClass("p-3");  // Se agrega placeholder al input y se agrega clase p-3.
        $(".divAltaProc").html( // Se agrega el boton de alta de proceso
            `<button type="button" data-titlel="Nuevo Proceso" class="btn btn-tabler h50 shadow" id="btnAltaProceso"><i class="bi bi-plus font12"></i></button>`
        );
        $(idTable).removeClass("invisible"); // Se remueve la clase invisible de la tabla
        $(idTable + " tbody").on("click", ".editProc", function (e) { // Se agrega el evento click al boton editar
            e.preventDefault();
            $.notifyClose() // Se cierra el notify
            let dataRow = $(idTable).DataTable().row($(this).parents("tr")).data(); // Se obtiene la fila seleccionada en la tabla
            // console.log(dataRow);
            fetch("op/procModal.html?" + $.now()) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text()) // Se obtiene la respuesta
                .then(data => {  // Se obtiene el html del modal
                    // console.log(HtmlEncode(dataRow.ProcObs));
                    $("#modales").html(data); // Se agrega el html al modal
                    $("#modales .form-control").attr("autocomplete", "off"); // Se agrega el atributo autocomplete
                    let procModal = new bootstrap.Modal(document.getElementById("procModal"), { keyboard: true }); // Se inicializa el modal
                    $("#procModal .modal-title").html("Editar Proceso"); // Se agrega el titulo del modal
                    $("#procModal #ProcDesc").val(decodeEntities(dataRow.ProcDesc)); // Se agrega el valor del proceso
                    $("#procModal #ProcCost").val((dataRow.ProcCost));
                    maskCosto('#ProcCost'); // Se agrega el máscara al input
                    $("#procModal #ProcObs").val(decodeEntities(dataRow.ProcObs)); // Se agrega el valor de la observacion
                    ActiveBTN(false, "#ProcSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-pencil me-2"></i>Editar Proceso'); // Se desactiva el boton de submit
                    procModal.show(); // Se muestra el modal
                    setTimeout(() => {
                        $("#ProcDesc").focus(); // Se posiciona el cursor en el input de la descripcion del proceso
                    }, 500);
                    $('#procModal .modal-footer #divSubmit').prepend(`<button type="button" class="btn btn-outline-pinterest h50 me-2" id="ProcSubmitdelete"><i class="bi bi-trash"></i></button>`); // Se agrega el boton de eliminar
                    bindForm('mod&ProcID=' + dataRow.ProcID)  // Se bindea el formulario
                    $('#ProcSubmitdelete').click(function () { // Se agrega el evento click al boton de eliminar
                        $("#procModal").modal("hide"); // Se oculta el modal
                        fetch("op/procModal.html?" + $.now()) // Se hace la peticion ajax para obtener el modal
                            .then(response => response.text()) // Se obtiene la respuesta
                            .then(data => { // Se obtiene el html del modal
                                $("#modales").html(data); // Se agrega el html al modal
                                $("#procModal .modal-content").html('') // Se remueve el contenido del modal
                                // $("#procModal .modal-dialog").addClass('modal-dialog-centered') // Se agrega la clase modal-dialog-centered
                                let procModal = new bootstrap.Modal(document.getElementById("procModal"), { keyboard: true }); // Se inicializa el modal
                                $("#procModal .modal-content").html(`
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        <div class="modal-status bg-danger"></div>
                                        <div class="modal-body text-center py-4">
                                            <i class="bi bi-exclamation-triangle font20 text-pinterest"></i>
                                            <h2>¿Desea eliminar el proceso<br>${dataRow.ProcDesc}?</h2>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="w-100">
                                                <div class="row">
                                                    <div class="col">
                                                        <a href="#" class="btn btn-white w-100 h50" data-bs-dismiss="modal">Cancelar</a>
                                                    </div>
                                                    <div class="col">
                                                        <a href="#" class="btn btn-danger w-100 h50" data-bs-dismiss="modal" id="procConfirmDelete">Eliminar</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `); // Se agrega el html del modal de confirmacion de eliminacion
                                procModal.show(); // Se muestra el modal
                                $('#procConfirmDelete').click(function (e) { // Se agrega el evento click al boton de confirmar eliminar
                                    e.preventDefault(); // Se evita el evento click
                                    $.ajax({ // Se hace la peticion ajax para eliminar el proceso
                                        type: 'POST', // Se envia como POST
                                        url: "op/crud.php",  // Se envia a la ruta del crud
                                        data: `ProcSubmit=baja&ProcID=${dataRow.ProcID}&ProcDesc=${dataRow.ProcDesc}`,
                                        beforeSend: function (data) { // Antes de enviar la peticion
                                            $.notifyClose(); // Se cierra el notify
                                            notify("Aguarde <span class='animated-dots'></span>", "dark", 0, "right"); // Se muestra el notify
                                            ActiveBTN(true, "#ProcSubmitdelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se desactiva el boton de submit
                                        },
                                        success: function (data) {  // Si la peticion es correcta
                                            if (data.status == "ok") { // Si el status es ok
                                                $.notifyClose(); // Se cierra el notify
                                                notify(data.Mensaje, "success", 2000, "right") // Se muestra el notify
                                                $("#procModal").fadeOut('slow');
                                                setTimeout((function () {
                                                    $("#procModal").modal("hide"); // Se oculta el modal
                                                }), 500); // Se agrega un setTimeout para que el notify se muestre despues de 0.5 segundos
                                                $("#tableProcesos").DataTable().ajax.reload(null, false); // Se recarga la tabla
                                            } else {  // Si el status no es ok
                                                $.notifyClose(); // Se cierra el notify
                                                notify(data.Mensaje, "danger", 2000, "right"); // Se muestra el notify con el mensaje de error
                                            }
                                            ActiveBTN(false, "#ProcSubmitdelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se activa el boton de submit
                                        },
                                        error: function (data) {
                                            ActiveBTN(false, "#ProcSubmitdelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se activa el boton de submit
                                            $.notifyClose(); // Se cierra el notify
                                            notify("Error", "danger", 3000, "right"); // Se muestra el notify con el mensaje de error
                                        }
                                    }); // Se termina la peticion ajax
                                }); // Se termina el evento click del boton de confirmar eliminar
                                document.getElementById('procModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                                    $("#modales").html('');
                                })
                            }); // Se termina el then de la peticion ajax
                    });
                });
        });
        $(idTable + " tbody").on("click", ".viewProc", function (e) { // Se agrega el evento click al hacer click en la descripción del proceso
            e.preventDefault();
            $.notifyClose() // Se cierra el notify
            let dataRow = $(idTable).DataTable().row($(this).parents("tr")).data(); // Se obtiene el dato de la fila
            fetch("op/procModal.html?" + $.now()) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text()) // Se obtiene la respuesta
                .then(data => { // Se obtiene el html del modal
                    $("#modales").html(data); // Se agrega el html al modal
                    let procModal = new bootstrap.Modal(document.getElementById("procModal"), { keyboard: !0 }); // Se inicializa el modal
                    $("#procModal .modal-title").html(dataRow.ProcDesc); // Se agrega el titulo del modal
                    $("#procModal .modal-dialog").addClass('modal-fullscreen-sm-down modal-xl'); // Se agrega la clase modal-fullscreen-sm-down
                    let ProcObs = dataRow.ProcObs; // Se obtiene la observacion del proceso
                    let renderProcObs = ProcObs.replace(/(?:\r\n|\r|\n)/g, "<br>"); // Se reemplaza el salto de linea por una etiqueta de salto de linea html
                    $("#procModal .modal-body").html('<div class="card"><div class="card-body"><p>Costo: ' + dataRow.ProcCost + '</p><p><div class="mb-2">Observaciones:</div>' + renderProcObs + "</p>                            </div>\n                        </div>\n                        ");
                    $("#procModal #ProcSubmit").remove(); // Se remueve el boton de submit
                    ActiveBTN(true, "#ProcSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-pencil me-2"></i>Editar Proceso'); // Se desactiva el boton de submit
                    procModal.show(); // Se muestra el modal
                    document.getElementById('procModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                        $("#modales").html('');
                    })
                });
        });
        $("#btnAltaProceso").click(function () { // Se agrega el evento click al boton de alta de proceso
            $.notifyClose() // Se cierra el notify
            fetch("op/procModal.html?" + $.now()) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text())  // Se obtiene la respuesta  
                .then(data => { // Se obtiene el html del modal
                    $("#modales").html(data); // Se agrega el html al modal
                    $("#modales .form-control").attr("autocomplete", "off"); // Se desactiva el autocomplete
                    maskCosto('#ProcCost'); // Se agrega el máscara al input
                    var procModal = new bootstrap.Modal(document.getElementById("procModal"), { keyboard: true }); // Se inicializa el modal
                    ActiveBTN(false, "#ProcSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="ProcSubmit"></i>Crear Proceso'); // Se desactiva el boton de submit
                    procModal.show(); // Se muestra el modal
                    setTimeout(() => {
                        $("#ProcDesc").focus();
                    }, 500); // Se agrega un setTimeout para que el focus se haga despues de 0.5 segundos
                    bindForm('alta') // Se llama la funcion bindForm para hacer el submit del formulario
                    document.getElementById('procModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                        $("#modales").html('');
                    })
                });
        });
    });
    table.on("page.dt", function (e, settings) { // Se agrega el evento page.dt para que se ejecute cuando se cambie de pagina
        let idTable = "#" + e.target.id; // Se obtiene el id de la tabla
        $(idTable + " div").addClass("pre-div"); // Se agrega la clase pre-div a la div de la tabla
    });
    table.on("draw.dt", function (e, settings) { // Se agrega el evento draw.dt para que se ejecute cuando se redibuje la tabla
        let idTable = "#" + e.target.id; // Se obtiene el id de la tabla
        $(idTable + " div").removeClass("pre-div"); // Se remueve la clase pre-div a la div de la tabla
        $("#tableProcesos thead").remove(); // Se remueve el thead de la tabla
    });
    $.fn.DataTable.ext.pager.numbers_length = 5; // Se agrega el numero de paginas a mostrar en el paginador
});
