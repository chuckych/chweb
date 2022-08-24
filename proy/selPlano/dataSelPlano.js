$(function () {
    "use strict"; // Start of use strict

    function bindForm(tipo) { //bindear formulario de alta/edicion
        $("#planoForm").bind("submit", function (e) {
            e.preventDefault();
            let proy_pasos = sessionStorage.getItem(
                location.pathname.substring(1) + "proy_pasos"
            );
            proy_pasos = JSON.parse(proy_pasos);
            if ($("#PlanoDesc").val() == "") {
                $.notifyClose();
                $("#PlanoDesc").focus().addClass("is-invalid");
                let textErr = `<span class="text-danger font-weight-bold">Ingrese una descripción.<span>`;
                notify(textErr, "danger", 2000, "right");
                return;
            }
            let dataForm = $(this).serialize();
            let PlanoDesc = {};
            dataForm.split("&").forEach(function (item) {
                var parts = item.split("=");
                PlanoDesc[parts[0]] = decodeURIComponent(parts[1]);
            });
            $.ajax({
                type: $(this).attr("method"),
                url: "op/crud.php",
                data: $(this).serialize() + "&PlanoSubmit=" + tipo + "&PlantPlano=" + proy_pasos.ProyPlantPlano,
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
                        $("#selectPlano").DataTable().search(PlanoDesc['PlanoDesc']).draw();
                        $("#planoModal").modal("hide");
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

    let proy_info = sessionStorage.getItem(
        location.pathname.substring(1) + "proy_info"
    );
    proy_info = JSON.parse(proy_info);

    let proy_pasos = sessionStorage.getItem(
        location.pathname.substring(1) + "proy_pasos"
    );
    proy_pasos = JSON.parse(proy_pasos);

    if (proy_info) {
        $("#inicioNombre").html(`Hola ${proy_info.name}`);
    }

    $("#mainTitleBar").html(('Seleccionar Plano'));
    $(document).prop("title", ('Seleccionar Plano'));
    let tablePlano = $("#selectPlano").dataTable({ //inicializar datatable
        lengthMenu: [[6, 10, 25, 50, 100], [6, 10, 25, 50, 100]],
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        responsive: true,
        dom:
            "<'row '<'col-12 py-2' <'pagPlano'p>>>" +
            "<'row '<'col-12 sticky-top d-inline-flex justify-content-between title'f>>" +
            "<'row '<'col-12 table-responsive mt-2 d-none tablePlano't>>",
        ajax: {
            url: `data/getPlanos.php?${Date.now()}`,
            type: "POST",
            dataType: "json",
            data: function (data) {
                data.PlanoEsta = '0';
                data.ProyID = proy_pasos.ProyID;
            },
            error: function () {
                $("#selectPlano").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass("");
            $('#listSelPlanoRow').append(`
                <div class="col-12 col-sm-6">
                    <div 
                        data-PlanoID="${data.PlanoID}" 
                        data-PlanoDesc="${data.PlanoDesc}" 
                        data-PlanoObs="${data.PlanoObs}"
                        data-PlanoCod="${data.PlanoCod}"
                        class="card p-3 mt-2 mt-sm-3 animate__animated animate__fadeIn pointer checkPlano">
                        <div class="form-check pointer mb-1">
                            <input class="form-check-input" type="checkbox" value="" id="plano_${data.PlanoID}">
                            <label class="form-check-label h3 mb-0" for="plano_${data.PlanoID}">
                            <span class="ls1 text-secondary">(#${data.PlanoID})</span>  ${data.PlanoDesc}
                            </label>
                        </div>
                        <div class="font08 text-secondary overflow-auto" style="max-height:50px">${data.PlanoObs}</div>
                    </div>
                </div>
                `);
        },
        columns: [
            {
                className: "align-middle border-bottom py-1 w-100 text-wrap",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let PlanoCod = (row.PlanoCod) ? `<span class='text-secondary font08'> - Código: ${row.PlanoCod}</span>` : '';
                    let datacol =
                        `
                        <div class="card p-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="${row.PlanoID}">
                                <label class="form-check-label font09 font-weight-bold" for="${row.PlanoID}">
                                <label class="ls1 text-secondary">(#${row.PlanoID})</label>  ${row.PlanoDesc} ${PlanoCod}
                                </label>
                            </div>
                            <div class="font08 text-secondary">${row.PlanoObs}</div>
                        </div>
                        `;
                    return datacol;
                }
            },
        ],
        info: true,
        ordering: 0,
        responsive: 0,
        language: {
            url: `../js/DataTableSpanishShort2.json?${Date.now()}`
        }

    });
    tablePlano.on('init.dt', function () {
        $("#selectPlano thead").remove();
        $(".dataTables_scrollHead").remove();
        $('#selectPlano_filter input').attr('placeholder', 'Buscar Plano').addClass('p-3 w300');
        $('.title').prepend(`
        <div class="w-100 d-none d-sm-block">
            <h3 class="display-6 text-tabler titlePlanos">Seleccionar Plano</h3>
        </div>
    `)

        if (proy_pasos.ProyPlantPlano == '0') {
            $('#btnAltaPlano').remove()
            $(".titlePlanos").html('<div class="mt-4">No hay Planos.</div>')
            $("#omitePlano").html('<div class="">Siguiente</div>')
        }
        $('#listSelPlano').fadeIn();

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
    tablePlano.on('draw.dt', function (e, settings) {
        e.preventDefault();
        $("#selectPlano thead").remove();
        $(".dataTables_scrollHead").remove();
        if (settings._iRecordsTotal > 6) {
            $(".pagPlano").show();
        } else {
            $(".pagPlano").hide();
        }
    });
    tablePlano.on('search.dt', function (e) {
        $('#listSelPlanoRow').html('');
    });
    tablePlano.on('page.dt', function (e) {
        $('#listSelPlanoRow').html('');
    });
    // $(document).on("click", "#selectPlano tbody tr", function (e) {
    $(document).on("click", ".checkPlano", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        $('#selectPlano tbody tr').find("input[type='checkbox']").prop('checked', false);
        let checkbox = $(this).find("input[type='checkbox']");
        checkbox.prop("checked", !checkbox.prop("checked"));

        // let data = $("#selectPlano").DataTable().row($(this)).data(); //obtener datos de la fila seleccionada

        let dataPlanoObs = $(this).attr('data-PlanoObs');
        let dataPlanoID = $(this).attr('data-PlanoID');
        let dataPlanoDesc = $(this).attr('data-PlanoDesc');
        let dataPlanoCod = $(this).attr('data-PlanoCod');

        let p = get_proy_pasos();

        let proy_pasos = JSON.stringify({
            'EmpID': p.EmpID,
            'EmpDesc': p.EmpDesc,
            'ProyID': p.ProyID,
            'ProyNom': p.ProyNom,
            'ProyDesc': p.ProyDesc,
            'ProyPlant': p.ProyPlant,
            'PlantDesc': p.PlantDesc,
            'ProyResp': p.ProyResp,
            'RespDesc': p.RespDesc,
            'ProcID': p.ProcID,
            'ProcDesc': p.ProcDesc,
            'ProcCost': p.ProcCost,
            'PlanoID': dataPlanoID,
            'PlanoDesc': dataPlanoDesc,
            'PlanoCod': dataPlanoCod,
        });
        sessionStorage.setItem(location.pathname.substring(1) + 'proy_pasos', proy_pasos)
        axios.get('routes.php', {
            params: {
                'page': 'finalizar'
            }
        }).then(function (response) {
            sessionStorage.setItem(
                location.pathname.substring(1) + "proy_page",
                'finalizar'
            );
            $("#contenedor").html(response.data);
        }).then(() => {
        }).catch(function (error) {
            console.log(error);
        })

    });
    $(document).on("click", "#omitePlano", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        let dataPlanoObs = '';
        let dataPlanoID = '';
        let dataPlanoDesc = '';
        let dataPlanoCod = '';

        let p = get_proy_pasos();

        let proy_pasos = JSON.stringify({
            'EmpID': p.EmpID,
            'EmpDesc': p.EmpDesc,
            'ProyID': p.ProyID,
            'ProyNom': p.ProyNom,
            'ProyDesc': p.ProyDesc,
            'ProyPlant': p.ProyPlant,
            'PlantDesc': p.PlantDesc,
            'ProyResp': p.ProyResp,
            'RespDesc': p.RespDesc,
            'ProcID': p.ProcID,
            'ProcDesc': p.ProcDesc,
            'ProcCost': p.ProcCost,
            'PlanoID': dataPlanoID,
            'PlanoDesc': dataPlanoDesc,
            'PlanoCod': dataPlanoCod,
        });
        sessionStorage.setItem(location.pathname.substring(1) + 'proy_pasos', proy_pasos)
        axios.get('routes.php', {
            params: {
                'page': 'finalizar'
            }
        }).then(function (response) {
            sessionStorage.setItem(
                location.pathname.substring(1) + "proy_page",
                'finalizar'
            );
            $("#contenedor").html(response.data);
        }).then(() => {
        }).catch(function (error) {
            console.log(error);
        })

    });

});