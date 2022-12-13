$(function () {
    "use strict"; // Start of use strict
    let tablePlantillas = $("#tablePlantillas").dataTable({ //inicializar datatable
        lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]], //mostrar cantidad de registros
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        responsive: true,
        dom:
            "<'row mt-3'<'col-12 col-sm-6 d-flex justify-content-start d-none d-sm-block'l><'col-12 col-sm-6 d-flex justify-content-end'<'divAltaPlant'>f>>" +
            "<'row'<'col-12 table-responsive mt-2't>" +
            "<'col-12 d-flex justify-content-end'p>" +
            "<'col-12 d-flex justify-content-end'i>>",
        ajax: {
            url: `data/getPlantillasPl.php?${Date.now()}`,
            type: "POST",
            dataType: "json",
            data: function (data) {
                // data._eg = $("input[name=_eg]:checked").val();
            },
            error: function () {
                $("#tablePlantillas").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass("animate__animated animate__fadeIn");
        },
        columns: [
            {
                className: "w-100 p-0",
                targets: "",
                title: "",

                render: function (data, type, row, meta) {
                    let datacol =
                        `
                        <div class="form-selectgroup-item flex-fill mb-1">
                            <div class="card-body cardBody${row.PlantID} border p-3 animate__animated animate__fadeIn">
                                <div class='d-flex justify-content-between'>
                                    <div class="">
                                        <span>${row["PlantDesc"]}</span>
                                    </div>
                                    <div>
                                        <button data-titlel="Asignar Planos" type="button" class="ms-1 btn p-2 btn-outline-tabler bi bi-list asignPlano" value="${row.PlantID}"><span class="font08"></span></button>
                                        <span class="ms-1">
                                            <button type="button" class="btn p-2 btn-outline-teal bi bi-pencil editPlant"></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        `;
                    return datacol;
                }
            }
        ],
        searchDelay: 350,
        // scrollY: "340px",
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
        $("#plantForm").bind("submit", function (e) {
            e.preventDefault();
            if ($("#PlantDesc").val() == "") {
                $.notifyClose();
                $("#PlantDesc").focus().addClass("is-invalid");
                let textErr = `<span class="text-danger font-weight-bold">Ingrese una plantilla<span>`;
                notify(textErr, "danger", 2000, "right");
                return;
            }
            $.ajax({
                type: $(this).attr("method"),
                url: "op/crud.php",
                data: $(this).serialize() + "&PlantSubmit=" + tipo + "&PlantMod=44",
                beforeSend: function (data) {
                    $.notifyClose();
                    notify("Aguarde <span class='animated-dots'></span>", "dark", 0, "right");
                    ActiveBTN(true, "#PlantSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="PlantSubmit"></i>Crear Plantilla');
                    $(".is-invalid").removeClass("is-invalid");
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        notify(data.Mensaje, "success", 2000, "right")
                        $("#plantModal").fadeOut('slow');
                        setTimeout((function () {
                            $("#plantModal").modal("hide");
                        }), 500);
                        $("#tablePlantillas").DataTable().ajax.reload(null, false);
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, "danger", 2000, "right");
                    }
                    ActiveBTN(false, "#PlantSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="PlantSubmit"></i>Crear Plantilla');
                },
                error: function (data) {
                    ActiveBTN(false, "#PlantSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="PlantSubmit"></i>Crear Plantilla');
                    $.notifyClose();
                    notify("Error", "danger", 3000, "right");
                }
            });
            e.stopImmediatePropagation();
            document.getElementById('plantModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                $("#modales").html('');
            })
        });
    } //fin bindForm
    tablePlantillas.on("init.dt", function (e, settings) { // Cuando se inicializa la tabla
        let idTable = `#${e.target.id}`; // Se obtiene el id de la tabla
        $("thead").remove(); // Se remueve el thead
        let lengthMenu = $(`${idTable}_length select`); // Se obtiene el select del lengthMenu
        $(lengthMenu).addClass("h50"); // Se agrega la clase h40 height: 50px
        let filterInput = $(`${idTable}_filter input`); // Se obtiene el input del filtro
        $(filterInput).attr({ placeholder: "Buscar Plantilla.." }).addClass("p-3");  // Se agrega placeholder al input y se agrega clase p-3.
        $(".divAltaPlant").html( // Se agrega el boton de alta de plano
            `<button type="button" data-titlel="Nueva Plantilla" class="btn btn-tabler h50 shadow" id="btnAltaPlantilla"><i class="bi bi-plus font12"></i></button>`
        );
        $(idTable).removeClass("invisible"); // Se remueve la clase invisible de la tabla
        $(idTable + " tbody").on("click", ".editPlant", function (e) { // Se agrega el evento click al boton editar
            e.preventDefault();
            $.notifyClose() // Se cierra el notify
            let dataRow = $(idTable).DataTable().row($(this).parents("tr")).data(); // Se obtiene la fila seleccionada en la tabla
            // console.log(dataRow);
            fetch(`op/PlantModal.html?${Date.now()}`) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text()) // Se obtiene la respuesta
                .then(data => {  // Se obtiene el html del modal
                    // console.log(HtmlEncode(dataRow.planoObs));
                    $("#modales").html(data); // Se agrega el html al modal
                    $("#modales .form-control").attr("autocomplete", "off"); // Se agrega el atributo autocomplete
                    let plantModal = new bootstrap.Modal(document.getElementById("plantModal"), { keyboard: true }); // Se inicializa el modal
                    $("#plantModal .modal-title").html("Editar Plantilla"); // Se agrega el titulo del modal
                    $("#plantModal #PlantDesc").val(decodeEntities(dataRow.PlantDesc)); // Se agrega el valor del plano
                    ActiveBTN(false, "#PlantSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-pencil me-2"></i>Editar Plantilla'); // Se desactiva el boton de submit
                    plantModal.show(); // Se muestra el modal
                    setTimeout(() => {
                        $("#PlantDesc").focus(); // Se posiciona el cursor en el input de la descripcion del plano
                    }, 500);
                    $('#plantModal .modal-footer #divSubmit').prepend(`<button type="button" class="btn btn-outline-pinterest h50 me-2" id="PlantSubmitdelete"><i class="bi bi-trash"></i></button>`); // Se agrega el boton de eliminar
                    bindForm('mod&PlantID=' + dataRow.PlantID + '&PlantMod=44')  // Se bindea el formulario
                    $('#PlantSubmitdelete').click(function () { // Se agrega el evento click al boton de eliminar
                        $("#plantModal").modal("hide"); // Se oculta el modal
                        fetch(`op/PlantModal.html?${Date.now()}`) // Se hace la peticion ajax para obtener el modal
                            .then(response => response.text()) // Se obtiene la respuesta
                            .then(data => { // Se obtiene el html del modal
                                $("#modales").html(data); // Se agrega el html al modal
                                $("#plantModal .modal-content").html('') // Se remueve el contenido del modal
                                // $("#plantModal .modal-dialog").addClass('modal-dialog-centered') // Se agrega la clase modal-dialog-centered
                                let plantModal = new bootstrap.Modal(document.getElementById("plantModal"), { keyboard: true }); // Se inicializa el modal
                                $("#plantModal .modal-content").html(`
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        <div class="modal-status bg-danger"></div>
                                        <div class="modal-body text-center py-4">
                                            <i class="bi bi-exclamation-triangle font20 text-pinterest"></i>
                                            <h2>¿Desea eliminar la plantilla<br>${dataRow.PlantDesc}?</h2>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="w-100">
                                                <div class="row">
                                                    <div class="col">
                                                        <a href="#" class="btn btn-white w-100 h50" data-bs-dismiss="modal">Cancelar</a>
                                                    </div>
                                                    <div class="col">
                                                        <a href="#" class="btn btn-danger w-100 h50" data-bs-dismiss="modal" id="plantConfirmDelete">Eliminar</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `); // Se agrega el html del modal de confirmacion de eliminacion
                                plantModal.show(); // Se muestra el modal
                                $('#plantConfirmDelete').click(function (e) { // Se agrega el evento click al boton de confirmar eliminar
                                    e.preventDefault(); // Se evita el evento click
                                    $.ajax({ // Se hace la peticion ajax para eliminar el plano
                                        type: 'POST', // Se envia como POST
                                        url: "op/crud.php",  // Se envia a la ruta del crud
                                        data: `PlantSubmit=baja&PlantID=${dataRow.PlantID}&PlantDesc=${dataRow.PlantDesc}&PlantMod=44`,
                                        beforeSend: function (data) { // Antes de enviar la peticion
                                            $.notifyClose(); // Se cierra el notify
                                            notify("Aguarde <span class='animated-dots'></span>", "dark", 0, "right"); // Se muestra el notify
                                            ActiveBTN(true, "#PlantSubmitdelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se desactiva el boton de submit
                                        },
                                        success: function (data) {  // Si la peticion es correcta
                                            if (data.status == "ok") { // Si el status es ok
                                                $.notifyClose(); // Se cierra el notify
                                                notify(data.Mensaje, "success", 2000, "right") // Se muestra el notify
                                                $("#plantModal").fadeOut('slow');
                                                setTimeout((function () {
                                                    $("#plantModal").modal("hide"); // Se oculta el modal
                                                }), 500); // Se agrega un setTimeout para que el notify se muestre despues de 0.5 segundos
                                                $("#tablePlantillas").DataTable().ajax.reload(null, false); // Se recarga la tabla
                                                $('#selPlantilla').val('');
                                                if ($('#selPlantilla').val() == '') {
                                                    $("#tablePlantPlanos").DataTable().ajax.reload();
                                                }
                                            } else {  // Si el status no es ok
                                                $.notifyClose(); // Se cierra el notify
                                                notify(data.Mensaje, "danger", 2000, "right"); // Se muestra el notify con el mensaje de error
                                            }
                                            ActiveBTN(false, "#PlantSubmitdelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se activa el boton de submit
                                        },
                                        error: function (data) {
                                            ActiveBTN(false, "#PlantSubmitdelete", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-trash me-2"></i> Eliminar'); // Se activa el boton de submit
                                            $.notifyClose(); // Se cierra el notify
                                            notify("Error", "danger", 3000, "right"); // Se muestra el notify con el mensaje de error
                                        }
                                    }); // Se termina la peticion ajax
                                }); // Se termina el evento click del boton de confirmar eliminar
                                document.getElementById('plantModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                                    $("#modales").html('');
                                })
                            }); // Se termina el then de la peticion ajax
                    });
                });
        });
        $("#btnAltaPlantilla").click(function () { // Se agrega el evento click al boton de alta de plano
            $.notifyClose() // Se cierra el notify
            fetch(`op/PlantModal.html?${Date.now()}`) // Se hace la peticion ajax para obtener el modal
                .then(response => response.text())  // Se obtiene la respuesta  
                .then(data => { // Se obtiene el html del modal
                    $("#modales").html(data); // Se agrega el html al modal
                    $("#modales .form-control").attr("autocomplete", "off"); // Se desactiva el autocomplete
                    var plantModal = new bootstrap.Modal(document.getElementById("plantModal"), { keyboard: true }); // Se inicializa el modal
                    ActiveBTN(false, "#PlantSubmit", "Aguarde <span class='animated-dots'></span>", '<i class="bi bi-plus-lg me-2" id="PlantSubmit"></i>Crear Plantilla'); // Se desactiva el boton de submit
                    plantModal.show(); // Se muestra el modal
                    setTimeout(() => {
                        $("#PlantDesc").focus();
                    }, 500); // Se agrega un setTimeout para que el focus se haga despues de 0.5 segundos
                    bindForm('alta') // Se llama la funcion bindForm para hacer el submit del formulario
                    document.getElementById('plantModal').addEventListener('hidden.bs.modal', function (event) { // Se agrega el evento hidden.bs.modal al Modal
                        $("#modales").html('');
                    })
                });
        });

        $(`${idTable} tbody`).on("click", ".asignPlano", function (e) { // Se agrega el evento click al hacer click en la descripción del plano
            e.preventDefault();
            $('.asignPlano').attr("checked", false);
            $(".asignPlano").removeClass('btn-tabler')
            $(".asignPlano").addClass('btn-outline-tabler')
            $(this).toggleClass('btn-outline-tabler btn-tabler')
            $('.card-body').removeClass('bg-blue-lt');
            $(this).parents(".card-body").addClass("bg-blue-lt"); // Se agrega la clase selected al tr que contiene el plano
            let dataRow = $(idTable).DataTable().row($(this).parents("tr")).data(); // Se obtiene el dato de la fila
            $('#selPlantilla').val(dataRow.PlantID);
            $('#selPlantillaNombre').html(`
            <label class="form-selectgroup-item flex-fill">
                <div class="form-selectgroup-label p-3 cardSelPlantilla">
                    <div class="form-selectgroup-label-content">
                        <div>
                            <div class="font-weight-bold text-center">
                            <span id="nombrePlantilla">${dataRow.PlantDesc}</span> <span id="totalActivos"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </label>
            `);
            $("#tablePlantPlanos").DataTable().search('').draw().ajax.reload();
            $('.SeleccionePlantilla').html('');
        });

    });
    tablePlantillas.on("page.dt", function (e, settings) { // Se agrega el evento page.dt para que se ejecute cuando se cambie de pagina
        let idTable = "#" + e.target.id; // Se obtiene el id de la tabla
        // $('#selPlantilla').val('')
        // $('#tablePlantPlanos').DataTable().ajax.reload();
        $(idTable + " div").addClass("pre-div"); // Se agrega la clase pre-div a la div de la tabla
        // $("#selPlantillaNombre").hide();
        if ($("#tablePlantPlanos_filter input").val() != "") {
            $('.SeleccionePlantilla').html('');
        }
    });
    tablePlantillas.on("draw.dt", function (e, settings) { // Se agrega el evento draw.dt para que se ejecute cuando se redibuje la tabla
        let idTable = "#" + e.target.id; // Se obtiene el id de la tabla
        $(idTable + " div").removeClass("pre-div"); // Se remueve la clase pre-div a la div de la tabla
        $("#tablePlantillas thead").remove(); // Se remueve el thead de la tabla
    });
    $.fn.DataTable.ext.pager.numbers_length = 5; // Se agrega el numero de paginas a mostrar en el paginador
});
