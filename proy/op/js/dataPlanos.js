$(function () {
    "use strict"; // Start of use strict
    let tablePlanos = $("#tablePlanos").dataTable({ //inicializar datatable
        lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]], //mostrar cantidad de registros
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        responsive: true,
        dom:
            "<'row mt-3'<'col-12 col-sm-6 d-flex justify-content-start d-none d-sm-block'l><'col-12 col-sm-6 d-flex justify-content-end'<'divAltaPlano'>f>>" +
            "<'row'<'col-12 table-responsive mt-2't>" +
            "<'col-12 d-flex justify-content-end'p>"+
            "<'col-12 d-flex justify-content-end'i>>",
        ajax: {
            url: "data/getPlanos.php?" + $.now(),
            type: "POST",
            dataType: "json",
            data: function (data) {
                // data._eg = $("input[name=_eg]:checked").val();
            },
            error: function () {
                $("#tablePlanos").css("display", "none");
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
                    let PlanoEsta = row.PlanoEsta
                    PlanoEsta = (PlanoEsta == '1') ? '<span class="ms-2 radius-0 badge bg-red-lt">Inactivo</span>' : '';
                    let datacol =
                    `
                    <div class="form-selectgroup-item flex-fill">
                        <div class="card-body border p-3 animate__animated animate__fadeIn ">
                            <div class='d-flex justify-content-between'>
                                <div class="viewPlano cursor-pointer btn-link"><span>${row["PlanoDesc"]}</span>${PlanoEsta}</div>
                                <div><span><button type="button" class="btn p-2 btn-outline-teal bi bi-pencil editPlano"></button></span></div>
                            </div>
                        <div><span>Código: ${row["PlanoCod"]}</span></div>
                    </div>
                    `;
                    return datacol;
                }
            }
        ],
        searchDelay: 350,
        // scrollY: "450px",
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
        $("#planoForm").bind("submit", function (e) {
            e.preventDefault();
            if ($("#PlanoDesc").val() == "") {
                $.notifyClose();
                $("#PlanoDesc").focus().addClass("is-invalid");
                let textErr = `<span class="text-danger font-weight-bold">Ingrese una descripción.<span>`;
                notify(textErr, "danger", 2000, "right");
                return;
            }
            $.ajax({
                type: $(this).attr("method"),
                url: "op/crud.php",
                data: $(this).serialize() + "&PlanoSubmit=" + tipo,
                beforeSend: function (data) {
                    $.notifyClose();
                    notify("Aguarde <span class='animated-dots'></span>", "dark", 0, "right");
                    ActiveBTN(true, "#PlanoSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="PlanoSubmit"></i>Crear Plano');
                    $(".is-invalid").removeClass("is-invalid");
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        notify(data.Mensaje, "success", 2000, "right")
                        $("#planoModal").fadeOut('slow');
                        setTimeout((function () {
                            $("#planoModal").modal("hide");
                        }), 500);
                        $("#tablePlanos").DataTable().ajax.reload(null, false);
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, "danger", 2000, "right");
                    }
                    ActiveBTN(false, "#PlanoSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="PlanoSubmit"></i>Crear Plano');
                },
                error: function (data) {
                    ActiveBTN(false, "#PlanoSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="PlanoSubmit"></i>Crear Plano');
                    $.notifyClose();
                    notify("Error", "danger", 3000, "right");
                }
            });
            e.stopImmediatePropagation();
            document.getElementById('planoModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                $("#modales").html('');
            })
        });
    } //fin bindForm
    tablePlanos.on("init.dt", function (e, settings) { // Cuando se inicializa la tabla
        let idTable = `#${e.target.id}`; // Se obtiene el id de la tabla
        $("thead").remove(); // Se remueve el thead
        let lengthMenu = $(`${idTable}_length select`); // Se obtiene el select del lengthMenu
        $(lengthMenu).addClass("h50"); // Se agrega la clase h40 height: 50px
        let filterInput = $(`${idTable}_filter input`); // Se obtiene el input del filtro
        $(filterInput).attr({ placeholder: "Buscar plano.." }).addClass("p-3");  // Se agrega placeholder al input y se agrega clase p-3.
        $(".divAltaPlano").html( // Se agrega el boton de alta de plano
            `<button type="button" data-titlel="Nuevo Plano" class="btn btn-tabler h50 shadow" id="btnAltaPlano"><i class="bi bi-plus font12"></i></button>`
        );
        $(idTable).removeClass("invisible"); // Se remueve la clase invisible de la tabla
        $(idTable + " tbody").on("click", ".editPlano", function (e) { // Se agrega el evento click al boton editar
            e.preventDefault();
            $.notifyClose() // Se cierra el notify
            let dataRow = $(idTable).DataTable().row($(this).parents("tr")).data(); // Se obtiene la fila seleccionada en la tabla
            // console.log(dataRow);
            fetch("op/planoModal.html?" + $.now()) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text()) // Se obtiene la respuesta
                .then(data => {  // Se obtiene el html del modal
                    // console.log(HtmlEncode(dataRow.PlanoObs));
                    $("#modales").html(data); // Se agrega el html al modal
                    $("#modales .form-control").attr("autocomplete", "off"); // Se agrega el atributo autocomplete
                    let planoModal = new bootstrap.Modal(document.getElementById("planoModal"), { keyboard: true }); // Se inicializa el modal
                    $("#planoModal .modal-title").html("Editar Plano"); // Se agrega el titulo del modal
                    $("#planoModal #PlanoDesc").val(decodeEntities(dataRow.PlanoDesc)); // Se agrega el valor del plano
                    $("#planoModal #PlanoCod").val(decodeEntities(dataRow.PlanoCod));
                    $("#planoModal #PlanoObs").val(decodeEntities(dataRow.PlanoObs)); // Se agrega el valor de la observacion

                    let PlanoEsta = dataRow.PlanoEsta
                    PlanoEsta = (PlanoEsta == '0') ? true : false;
                    $("#planoModal #PlanoEsta").prop('checked', PlanoEsta)

                    ActiveBTN(false, "#PlanoSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-pencil me-2"></i>Editar Plano'); // Se desactiva el boton de submit
                    planoModal.show(); // Se muestra el modal
                    setTimeout(() => {
                        $("#PlanoDesc").focus(); // Se posiciona el cursor en el input de la descripcion del plano
                    }, 500);
                    $('#planoModal .modal-footer #divSubmit').prepend(`<button type="button" class="btn btn-outline-pinterest h50 me-2" id="PlanoSubmitdelete"><i class="bi bi-trash"></i></button>`); // Se agrega el boton de eliminar
                    bindForm('mod&PlanoID=' + dataRow.PlanoID)  // Se bindea el formulario
                    $('#PlanoSubmitdelete').click(function () { // Se agrega el evento click al boton de eliminar
                        $("#planoModal").modal("hide"); // Se oculta el modal
                        fetch("op/planoModal.html?" + $.now()) // Se hace la peticion ajax para obtener el modal
                            .then(response => response.text()) // Se obtiene la respuesta
                            .then(data => { // Se obtiene el html del modal
                                $("#modales").html(data); // Se agrega el html al modal
                                $("#planoModal .modal-content").html('') // Se remueve el contenido del modal
                                // $("#planoModal .modal-dialog").addClass('modal-dialog-centered') // Se agrega la clase modal-dialog-centered
                                let planoModal = new bootstrap.Modal(document.getElementById("planoModal"), { keyboard: true }); // Se inicializa el modal
                                $("#planoModal .modal-content").html(`
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        <div class="modal-status bg-danger"></div>
                                        <div class="modal-body text-center py-4">
                                            <i class="bi bi-exclamation-triangle font20 text-pinterest"></i>
                                            <h2>¿Desea eliminar el plano<br>${dataRow.PlanoDesc}?</h2>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="w-100">
                                                <div class="row">
                                                    <div class="col">
                                                        <a href="#" class="btn btn-white w-100 h50" data-bs-dismiss="modal">Cancelar</a>
                                                    </div>
                                                    <div class="col">
                                                        <a href="#" class="btn btn-danger w-100 h50" data-bs-dismiss="modal" id="PlanoConfirmDelete">Eliminar</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `); // Se agrega el html del modal de confirmacion de eliminacion
                                planoModal.show(); // Se muestra el modal
                                $('#PlanoConfirmDelete').click(function (e) { // Se agrega el evento click al boton de confirmar eliminar
                                    e.preventDefault(); // Se evita el evento click
                                    $.ajax({ // Se hace la peticion ajax para eliminar el plano
                                        type: 'POST', // Se envia como POST
                                        url: "op/crud.php",  // Se envia a la ruta del crud
                                        data: `PlanoSubmit=baja&PlanoID=${dataRow.PlanoID}&PlanoDesc=${dataRow.PlanoDesc}&PlantMod=44`,
                                        beforeSend: function (data) { // Antes de enviar la peticion
                                            $.notifyClose(); // Se cierra el notify
                                            notify("Aguarde <span class='animated-dots'></span>", "dark", 0, "right"); // Se muestra el notify
                                            ActiveBTN(true, "#PlanoSubmitdelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se desactiva el boton de submit
                                        },
                                        success: function (data) {  // Si la peticion es correcta
                                            if (data.status == "ok") { // Si el status es ok
                                                $.notifyClose(); // Se cierra el notify
                                                notify(data.Mensaje, "success", 2000, "right") // Se muestra el notify
                                                $("#planoModal").fadeOut('slow');
                                                setTimeout((function () {
                                                    $("#planoModal").modal("hide"); // Se oculta el modal
                                                }), 500); // Se agrega un setTimeout para que el notify se muestre despues de 0.5 segundos
                                                $("#tablePlanos").DataTable().ajax.reload(null, false); // Se recarga la tabla
                                            } else {  // Si el status no es ok
                                                $.notifyClose(); // Se cierra el notify
                                                notify(data.Mensaje, "danger", 2000, "right"); // Se muestra el notify con el mensaje de error
                                            }
                                            ActiveBTN(false, "#PlanoSubmitdelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se activa el boton de submit
                                        },
                                        error: function (data) {
                                            ActiveBTN(false, "#PlanoSubmitdelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se activa el boton de submit
                                            $.notifyClose(); // Se cierra el notify
                                            notify("Error", "danger", 3000, "right"); // Se muestra el notify con el mensaje de error
                                        }
                                    }); // Se termina la peticion ajax
                                }); // Se termina el evento click del boton de confirmar eliminar
                                document.getElementById('planoModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                                    $("#modales").html('');
                                })
                            }); // Se termina el then de la peticion ajax
                    });
                });
        });
        $(idTable + " tbody").on("click", ".viewPlano", function (e) { // Se agrega el evento click al hacer click en la descripción del plano
            e.preventDefault();
            $.notifyClose() // Se cierra el notify
            let dataRow = $(idTable).DataTable().row($(this).parents("tr")).data(); // Se obtiene el dato de la fila
            fetch("op/planoModal.html?" + $.now()) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text()) // Se obtiene la respuesta
                .then(data => { // Se obtiene el html del modal
                    $("#modales").html(data); // Se agrega el html al modal
                    let planoModal = new bootstrap.Modal(document.getElementById("planoModal"), { keyboard: !0 }); // Se inicializa el modal
                    $("#planoModal .modal-title").html(dataRow.PlanoDesc); // Se agrega el titulo del modal
                    $("#planoModal .modal-dialog").addClass('modal-fullscreen-sm-down modal-xl'); // Se agrega la clase modal-fullscreen-sm-down
                    let PlanoObs = dataRow.PlanoObs; // Se obtiene la observacion del plano
                    let renderPlanoObs = PlanoObs.replace(/(?:\r\n|\r|\n)/g, "<br>"); // Se reemplaza el salto de linea por una etiqueta de salto de linea html
                    $("#planoModal .modal-body").html(`<div class="card"><div class="card-body"><p>Código: ${dataRow.PlanoCod}</p><p><div class="mb-2">Observaciones:</div>${renderPlanoObs}</p></div></div>`);
                    let PlanoEsta = dataRow.PlanoEsta
                    PlanoEsta = (PlanoEsta == '0') ? '<div class="badge radius-0 bg-green-lt mt-3 p-2">Plano Activo</div>' : '<div class="badge radius-0 bg-red-lt mt-3 p-2">Plano Inactivo</div>';
                    $("#planoModal .modal-body").append(PlanoEsta)
                    $("#planoModal #PlanoSubmit").remove(); // Se remueve el boton de submit
                    ActiveBTN(true, "#PlanoSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-pencil me-2"></i>Editar Plano'); // Se desactiva el boton de submit
                    planoModal.show(); // Se muestra el modal
                    document.getElementById('planoModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                        $("#modales").html('');
                    })
                });
        });
        $("#btnAltaPlano").click(function () { // Se agrega el evento click al boton de alta de plano
            $.notifyClose() // Se cierra el notify
            fetch("op/planoModal.html?" + $.now()) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text())  // Se obtiene la respuesta  
                .then(data => { // Se obtiene el html del modal
                    $("#modales").html(data); // Se agrega el html al modal
                    $("#modales .form-control").attr("autocomplete", "off"); // Se desactiva el autocomplete
                    var planoModal = new bootstrap.Modal(document.getElementById("planoModal"), { keyboard: true }); // Se inicializa el modal
                    ActiveBTN(false, "#PlanoSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="PlanoSubmit"></i>Crear Plano'); // Se desactiva el boton de submit
                    planoModal.show(); // Se muestra el modal
                    setTimeout(() => {
                        $("#PlanoDesc").focus();
                    }, 500); // Se agrega un setTimeout para que el focus se haga despues de 0.5 segundos
                    bindForm('alta') // Se llama la funcion bindForm para hacer el submit del formulario
                    document.getElementById('planoModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                        $("#modales").html('');
                    })
                });
        });
    });
    tablePlanos.on("page.dt", function (e, settings) { // Se agrega el evento page.dt para que se ejecute cuando se cambie de pagina
        let idTable = "#" + e.target.id; // Se obtiene el id de la tabla
        $(idTable + " div").addClass("pre-div"); // Se agrega la clase pre-div a la div de la tabla
    });
    tablePlanos.on("draw.dt", function (e, settings) { // Se agrega el evento draw.dt para que se ejecute cuando se redibuje la tabla
        let idTable = "#" + e.target.id; // Se obtiene el id de la tabla
        $(idTable + " div").removeClass("pre-div"); // Se remueve la clase pre-div a la div de la tabla
        $("#tablePlanos thead").remove(); // Se remueve el thead de la tabla
    });
    $.fn.DataTable.ext.pager.numbers_length = 5; // Se agrega el numero de paginas a mostrar en el paginador
});
