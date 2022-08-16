$(function () {
    "use strict"; // Start of use strict
    let tableEmpresas = $("#tableEmpresas").dataTable({ //inicializar datatable
        lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]], //mostrar cantidad de registros
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        responsive: true,
        dom:
            "<'row mt-3'<'col-12 col-sm-6 d-flex justify-content-start d-none d-sm-block'l><'col-12 col-sm-6 d-flex justify-content-end'<'divAltaEmp'>f>>" +
            "<'row'<'col-12 table-responsive mt-2't>" +
            "<'col-12 d-flex justify-content-end'p>"+
            "<'col-12 d-flex justify-content-end'i>>",
        ajax: {
            url: "data/getEmpresas.php?" + $.now(),
            type: "POST",
            dataType: "json",
            data: function (data) {
                // data._eg = $("input[name=_eg]:checked").val();
            },
            error: function () {
                $("#tableEmpresas").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex) {
            // $(row).attr({
            //     "data-id": data.id,
            //     "data-idsesion": data.id_sesion,
            //     title: "Ver detalle"
            // });
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
                                    <div class="viewEmp cursor-pointer btn-link"><span>${row["EmpDesc"]}</span></div>
                                    <div><span><button type="button" class="btn p-2 btn-outline-teal bi bi-pencil editEmp"></button></span></div>
                                </div>
                            <div><span>Tel: ${row["EmpTel"]}</span></div>
                        </div>
                        `;
                    return datacol;
                }
            }
        ],
        searchDelay: 350,
        // scrollY: "40px",
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
        $("#empForm").bind("submit", function (e) {
            e.preventDefault();
            if ($("#EmpDesc").val() == "") {
                $.notifyClose();
                $("#EmpDesc").focus().addClass("is-invalid");
                let textErr = `<span class="text-danger font-weight-bold">Ingresa una Empresa<span>`;
                notify(textErr, "danger", 2000, "right");
                return;
            }
            $.ajax({
                type: $(this).attr("method"),
                url: "op/crud.php",
                data: $(this).serialize() + "&EmpSubmit=" + tipo,
                beforeSend: function (data) {
                    $.notifyClose();
                    notify("Aguarde <span class='animated-dots'></span>", "dark", 0, "right");
                    ActiveBTN(!0, "#EmpSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="EmpSubmit"></i>Crear Empresa');
                    $(".is-invalid").removeClass("is-invalid");
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        notify(data.Mensaje, "success", 2e3, "right")
                        $("#empModal").fadeOut('slow');
                        setTimeout((function () {
                            $("#empModal").modal("hide");
                        }), 500);
                        $("#tableEmpresas").DataTable().ajax.reload(null, false);
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, "danger", 2e3, "right");
                    }
                    ActiveBTN(!1, "#EmpSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="EmpSubmit"></i>Crear Empresa');
                },
                error: function (data) {
                    ActiveBTN(!1, "#EmpSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="EmpSubmit"></i>Crear Empresa');
                    $.notifyClose();
                    notify("Error", "danger", 3000, "right");
                }
            });
            e.stopImmediatePropagation();
            document.getElementById('empModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                $("#modales").html('');
            })
        });
    } //fin bindFormEmpresa
    tableEmpresas.on("init.dt", function (e, settings) { // Cuando se inicializa la tabla
        let idTable = "#" + e.target.id; // Se obtiene el id de la tabla
        $("thead").remove(); // Se remueve el thead
        let lengthMenu = $(idTable + "_length select"); // Se obtiene el select del lengthMenu
        $(lengthMenu).addClass("h50"); // Se agrega la clase h40 height: 50px
        let filterInput = $(idTable + "_filter input"); // Se obtiene el input del filtro
        $(filterInput).attr({ placeholder: "Buscar empresa.." }).addClass("p-3");  // Se agrega placeholder al input y se agrega clase p-3.
        $(".divAltaEmp").html( // Se agrega el boton de alta de empresa
            `<button type="button" data-titlel="Nueva Empresa" class="btn btn-tabler h50 shadow" id="btnAltaEmpresa"><i class="bi bi-plus font12"></i></button>`
        );
        $(idTable).removeClass("invisible"); // Se remueve la clase invisible de la tabla
        $(idTable + " tbody").on("click", ".editEmp", function (e) { // Se agrega el evento click al boton editar
            e.preventDefault();
            $.notifyClose() // Se cierra el notify
            let dataRow = ($(idTable).DataTable().row($(this).parents("tr")).data()); // Se obtiene la fila seleccionada en la tabla
            // console.log(dataRow);
            fetch("op/empModal.html?" + $.now()) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text()) // Se obtiene la respuesta
                .then(data => {  // Se obtiene el html del modal
                    $("#modales").html(data); // Se agrega el html al modal
                    $("#modales .form-control").attr("autocomplete", "off"); // Se agrega el atributo autocomplete
                    let empModal = new bootstrap.Modal(document.getElementById("empModal"), { keyboard: true }); // Se inicializa el modal
                    $("#empModal .modal-title").html("Editar Empresa"); // Se agrega el titulo del modal
                    $("#empModal #EmpDesc").val(decodeEntities(dataRow.EmpDesc)); // Se agrega el valor de la empresa
                    $("#empModal #EmpTel").val(decodeEntities(dataRow.EmpTel)); // Se agrega el valor del telefono
                    $("#empModal #EmpObs").val(decodeEntities(dataRow.EmpObs)); // Se agrega el valor de la observacion
                    ActiveBTN(!1, "#EmpSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-pencil me-2"></i>Editar Empresa'); // Se desactiva el boton de submit
                    empModal.show(); // Se muestra el modal
                    setTimeout(() => {
                        $("#EmpDesc").focus(); // Se posiciona el cursor en el input de la descripcion de la empresa
                    }, 500);
                    $('#empModal .modal-footer #divSubmit').prepend(`<button type="button" class="btn btn-outline-pinterest h50 me-2" id="EmpSubmitdelete"><i class="bi bi-trash"></i></button>`); // Se agrega el boton de eliminar
                    bindFormEmpresa('mod&EmpID=' + dataRow.EmpID)  // Se bindea el formulario
                    // document.getElementById('empModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                    //     $("#modales").html('');
                    // })
                    $('#EmpSubmitdelete').click(function () { // Se agrega el evento click al boton de eliminar
                        $("#empModal").modal("hide"); // Se oculta el modal
                        fetch("op/empModal.html?" + $.now()) // Se hace la peticion ajax para obtener el modal
                            .then(response => response.text()) // Se obtiene la respuesta
                            .then(data => { // Se obtiene el html del modal
                                $("#modales").html(data); // Se agrega el html al modal
                                $("#empModal .modal-content").html('') // Se remueve el contenido del modal
                                // $("#empModal .modal-dialog").addClass('modal-dialog-centered') // Se agrega la clase modal-dialog-centered
                                let empModal = new bootstrap.Modal(document.getElementById("empModal"), { keyboard: true }); // Se inicializa el modal
                                $("#empModal .modal-content").html(`
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        <div class="modal-status bg-danger"></div>
                                        <div class="modal-body text-center py-4">
                                            <i class="bi bi-exclamation-triangle font20 text-pinterest"></i>
                                            <h2>¿Desea eliminar la empresa<br>`+ dataRow.EmpDesc + `?</h2>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="w-100">
                                                <div class="row">
                                                    <div class="col">
                                                        <a href="#" class="btn btn-white w-100 h50" data-bs-dismiss="modal">Cancelar</a>
                                                    </div>
                                                    <div class="col">
                                                        <a href="#" class="btn btn-danger w-100 h50" data-bs-dismiss="modal" id="EmpConfirmDelete">Eliminar</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `); // Se agrega el html del modal de confirmacion de eliminacion
                                empModal.show(); // Se muestra el modal
                                $('#EmpConfirmDelete').click(function (e) { // Se agrega el evento click al boton de confirmar eliminar
                                    e.preventDefault(); // Se evita el evento click
                                    $.ajax({ // Se hace la peticion ajax para eliminar la empresa
                                        type: 'POST', // Se envia como POST
                                        url: "op/crud.php",  // Se envia a la ruta del crud
                                        data: `EmpSubmit=baja&EmpID=${dataRow.EmpID}&EmpDesc=${dataRow.EmpDesc}`, // Se envia el id de la empresa
                                        beforeSend: function (data) { // Antes de enviar la peticion
                                            $.notifyClose(); // Se cierra el notify
                                            notify("Aguarde <span class='animated-dots'></span>", "dark", 0, "right"); // Se muestra el notify
                                            ActiveBTN(true, "#EmpSubmitdelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se desactiva el boton de submit
                                        },
                                        success: function (data) {  // Si la peticion es correcta
                                            if (data.status == "ok") { // Si el status es ok
                                                $.notifyClose(); // Se cierra el notify
                                                notify(data.Mensaje, "success", 2000, "right") // Se muestra el notify
                                                $("#empModal").fadeOut('slow');
                                                setTimeout((function () {
                                                    $("#empModal").modal("hide"); // Se oculta el modal
                                                }), 500); // Se agrega un setTimeout para que el notify se muestre despues de 0.5 segundos
                                                $("#tableEmpresas").DataTable().ajax.reload(null, false); // Se recarga la tabla
                                            } else {  // Si el status no es ok
                                                $.notifyClose(); // Se cierra el notify
                                                notify(data.Mensaje, "danger", 2000, "right"); // Se muestra el notify con el mensaje de error
                                            }
                                            ActiveBTN(false, "#EmpSubmitdelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se activa el boton de submit
                                        },
                                        error: function (data) {
                                            ActiveBTN(false, "#EmpSubmitdelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se activa el boton de submit
                                            $.notifyClose(); // Se cierra el notify
                                            notify("Error", "danger", 3000, "right"); // Se muestra el notify con el mensaje de error
                                        }
                                    }); // Se termina la peticion ajax
                                }); // Se termina el evento click del boton de confirmar eliminar
                                document.getElementById('empModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                                    $("#modales").html('');
                                })
                            }); // Se termina el then de la peticion ajax
                    });
                });
        });
        $(idTable + " tbody").on("click", ".viewEmp", function (e) { // Se agrega el evento click al hacer click en la descripción de la empresa
            e.preventDefault();
            $.notifyClose() // Se cierra el notify
            let dataRow = $(idTable).DataTable().row($(this).parents("tr")).data(); // Se obtiene el dato de la fila
            fetch("op/empModal.html?" + $.now()) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text()) // Se obtiene la respuesta
                .then(data => { // Se obtiene el html del modal
                    $("#modales").html(data); // Se agrega el html al modal
                    let empModal = new bootstrap.Modal(document.getElementById("empModal"), { keyboard: !0 }); // Se inicializa el modal
                    $("#empModal .modal-title").html(dataRow.EmpDesc); // Se agrega el titulo del modal
                    $("#empModal .modal-dialog").addClass('modal-fullscreen-sm-down modal-xl'); // Se agrega la clase modal-fullscreen-sm-down
                    let EmpObs = dataRow.EmpObs; // Se obtiene la observacion de la empresa
                    let renderEmpObs = EmpObs.replace(/(?:\r\n|\r|\n)/g, "<br>"); // Se reemplaza el salto de linea por una etiqueta de salto de linea html
                    $("#empModal .modal-body").html(`
                        <div class="card">
                            <div class="card-body">
                                <p>Tel: ` +
                        dataRow.EmpTel +
                        `</p>
                                <p><div class="mb-2">Observaciones:</div> ` +
                        (renderEmpObs) +
                        `</p>
                            </div>
                        </div>
                        `); // Se agrega el html del modal
                    $("#empModal #EmpSubmit").remove(); // Se remueve el boton de submit
                    ActiveBTN(!1, "#EmpSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-pencil me-2"></i>Editar Empresa'); // Se desactiva el boton de submit
                    empModal.show(); // Se muestra el modal
                    document.getElementById('empModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                        $("#modales").html('');
                    })
                });
        });
        $("#btnAltaEmpresa").click(function () { // Se agrega el evento click al boton de alta de empresa
            fetch("op/empModal.html?" + $.now()) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text())  // Se obtiene la respuesta  
                .then(data => { // Se obtiene el html del modal
                    $("#modales").html(data); // Se agrega el html al modal
                    $("#modales .form-control").attr("autocomplete", "off"); // Se desactiva el autocomplete
                    var empModal = new bootstrap.Modal(document.getElementById("empModal"), { keyboard: true }); // Se inicializa el modal
                    ActiveBTN(false, "#EmpSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="EmpSubmit"></i>Crear Empresa'); // Se desactiva el boton de submit
                    empModal.show(); // Se muestra el modal
                    setTimeout(() => {
                        $("#EmpDesc").focus();
                    }, 500); // Se agrega un setTimeout para que el focus se haga despues de 0.5 segundos
                    bindFormEmpresa('alta') // Se llama la funcion bindFormEmpresa para hacer el submit del formulario
                    document.getElementById('empModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                        $("#modales").html('');
                    })
                });
        });
    });
    tableEmpresas.on("page.dt", function (e, settings) { // Se agrega el evento page.dt para que se ejecute cuando se cambie de pagina
        let idTable = "#" + e.target.id; // Se obtiene el id de la tabla
        $(idTable + " div").addClass("pre-div"); // Se agrega la clase pre-div a la div de la tabla
    });
    tableEmpresas.on("draw.dt", function (e, settings) { // Se agrega el evento draw.dt para que se ejecute cuando se redibuje la tabla
        let idTable = "#" + e.target.id; // Se obtiene el id de la tabla
        $(idTable + " div").removeClass("pre-div"); // Se remueve la clase pre-div a la div de la tabla
        $("#tableEmpresas thead").remove(); // Se remueve el thead de la tabla
    });
    $.fn.DataTable.ext.pager.numbers_length = 5; // Se agrega el numero de paginas a mostrar en el paginador
});
