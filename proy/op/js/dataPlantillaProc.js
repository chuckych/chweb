$(function () {
    "use strict"; // Start of use strict
    let tablePlantProcesos = $("#tablePlantProcesos").dataTable({ //inicializar datatable
        lengthMenu: [[10000], [10000]], //mostrar cantidad de registros
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        responsive: true,
        dom:
            "<'row mt-3 divFilaPlanProc animate__animated animate__fadeIn'<'col-12 col-sm-6 d-flex justify-content-start d-none d-sm-block'<'divCheck'>><'col-12 col-sm-6 d-flex justify-content-end'<'divAsign'>f>>" +
            "<'row animate__animated animate__fadeIn'<'col-12 SeleccionePlantilla'>>" +
            "<'row animate__animated animate__fadeIn'<'col-12 table-responsive't>>" +
            "<'row divInfo animate__animated animate__fadeIn'<'col-12'i>>",
        ajax: {
            url: `data/getPlantillaProc.php?${Date.now()}`,
            type: "POST",
            dataType: "json",
            data: function (data) {
                data.Plantilla = $("#selPlantilla").val();
            },
            error: function () {
                $("#tablePlantProcesos").css("display", "none");
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
                    let checked = (row.ProcSet == true) ? "checked" : "";
                    let datacol =
                        `<label class="form-selectgroup-item flex-fill mb-1">
                        <input ${checked} type="checkbox" name="ProcID[]" value="${row["ProcID"]}" class="form-selectgroup-input inputProc">
                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                            <div class="pe-3">
                                <span class="form-selectgroup-check"></span>
                            </div>
                            <div class="form-selectgroup-label-content d-flex align-items-center justify-content-start">
                                <div>
                                    <div class="font-weight-bold">${row["ProcDesc"]}</div>
                                    <div class="text-muted font08 mt-1">Costo: ${row["ProcCost"]}</div>
                                </div>
                            </div>
                        </div>
                    </label>`;
                    return datacol;
                }
            }
        ],
        searchDelay: 350,
        scrollY: "340px",
        paging: true,
        searching: true,
        info: true,
        ordering: false,
        responsive: 0,
        language: {
            url: `../js/DataTableSpanishShort2.json?${Date.now()}`
        }
    });

    function asignProc(checked) { 
        $.ajax({
            url: "op/crud.php",
            type: "POST",
            data: {
                plantillaProc: true,
                checkProc: JSON.stringify(checked),
                PlaProPlan: $('#selPlantilla').val(),
                PlaProDesc: $('#nombrePlantilla').text(),
            },
            beforeSend: function (data) {
                $.notifyClose();
                notify("Aguarde <span class='animated-dots'></span>", "dark", 0, "right");
                ActiveBTN(true, "#btnAplicarPlantilla", "Aguarde <span class='animated-dots'></span>", 'Aplicar');
                $(".is-invalid").removeClass("is-invalid");
            },
            success: function (data) {
                if (data.status == "ok") {
                    $.notifyClose();
                    notify(data.Mensaje, "success", 2000, "right")
                } else {
                    $.notifyClose();
                    notify(data.Mensaje, "danger", 2000, "right");
                }
                ActiveBTN(false, "#btnAplicarPlantilla", "Aguarde <span class='animated-dots'></span>", 'Aplicar');
                $("#tablePlantProcesos").DataTable().ajax.reload();
                $("#tablePlantillas").DataTable().ajax.reload(null, false);
                $("#tablePlantillas").on("draw.dt", function (e, settings) {
                    $(`#tablePlantillas .cardBody${$('#selPlantilla').val()}`).addClass("bg-blue-lt");
                    $(`#tablePlantillas .cardBody${$('#selPlantilla').val()} .asignProc`).removeClass('btn-outline-tabler')
                    $(`#tablePlantillas .cardBody${$('#selPlantilla').val()} .asignProc`).addClass('btn-tabler')
                });
            },
        });
    } //fin bindForm

    tablePlantProcesos.on("init.dt", function (e, settings) { // Cuando se inicializa la tabla
        // notify(JSON.stringify(settings), "dark bg-dark d-flex w-100 text-white", 0, "center");
        let idTable = `#${e.target.id}`; // Se obtiene el id de la tabla
        $("thead").remove(); // Se remueve el thead
        let lengthMenu = $(`${idTable}_length select`); // Se obtiene el select del lengthMenu
        $(lengthMenu).addClass("h50"); // Se agrega la clase h40 height: 50px
        let filterInput = $(`${idTable}_filter input`); // Se obtiene el input del filtro
        $(filterInput).attr({ placeholder: "Buscar Proceso.." }).addClass("p-3");  // Se agrega placeholder al input y se agrega clase p-3.
        $(idTable).removeClass("invisible"); // Se remueve la clase invisible de la tabla
        $(".divCheck").html( // Se agrega el boton de alta de proceso
            `<label class="form-selectgroup-item flex-fill MarcarProc" style="width:50px">
            <input type="checkbox" name="" value="" class="form-selectgroup-input CheckAll">
            <div class="form-selectgroup-label d-flex justify-content-center align-items-center h50">
                <div class="">
                <i class="bi bi-check2-square font12"></i>
                </div>
            </div>
        </label>`
        );
        $(".divAsign").html( // Se agrega el boton de alta de proceso
            `<button type="button" data-titlel="Aplicar Procesos a la plantilla ${$('#selPlantillaNombre').val()}" class="btn btn-teal h50 shadow" id="btnAplicarPlantilla">Aplicar</button>`
        );
        $('.divCheck').on("click", ".MarcarProc", function (e) {
            $(".CheckAll").is(":checked") ?
                $(".inputProc").prop("checked", true) :
                $(".inputProc").prop("checked", false);
        });

        $('.divAsign').on("click", "#btnAplicarPlantilla", function (e) {

            let checked = new Array();
            $(`${idTable} input:checkbox`).each(function () {
                if ($(this).is(':checked')) {
                    (checked.push(parseInt($(this).val()))); /** Array de checkbox checked*/
                }
            });
            $.notifyClose();
            asignProc(checked);
        });

    });
    tablePlantProcesos.on("page.dt", function (e, settings) { // Se agrega el evento page.dt para que se ejecute cuando se cambie de pagina
        let idTable = "#" + e.target.id; // Se obtiene el id de la tabla
        $(idTable + " div").addClass("pre-div"); // Se agrega la clase pre-div a la div de la tabla
    });
    tablePlantProcesos.on("draw.dt", function (e, settings) {  // Se agrega el evento draw.dt para que se 
        let idTable = "#" + e.target.id; // Se obtiene el id de la tabla
        
        // console.log(settings.json.recordsTotal);

        if (settings.json.recordsTotal == 0) {
            $(".divFilaPlanProc").hide();
            $(".divInfo").hide();
            $("#selPlantillaNombre").hide();
            $(idTable).hide();
            $(".SeleccionePlantilla").html(`
            <label class="form-selectgroup-item flex-fill">
                <div class="form-selectgroup-label p-3 cardSelPlantilla">
                    <div class="form-selectgroup-label-content">
                        <div>
                            <div class="font-weight-bold text-center">
                            <div><i class="bi bi-chevron-right font08"></i> Seleccionar plantilla de la lista <i class="bi bi-chevron-left font08"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </label>
        `);
            $(".cardSelPlantilla").hover(
                function () {
                    $(".asignProc").toggleClass('animate__animated animate__flash border-secondary text-secondary')
                }
            );
        }

        if (settings.json.recordsTotal > 0) {
            $(".divFilaPlanProc").show();
            $(".divInfo").show();
            $("#selPlantillaNombre").show();
            $(idTable).show();
            $('.SeleccionePlantilla').html('');
        }
        $(idTable + " div").removeClass("pre-div"); // Se remueve la clase pre-div a la div de la tabla
        $("#tablePlantProcesos thead").remove(); // Se remueve el thead de la tabla

        let procActivos = new Array();
        $(`${idTable} input:checkbox`).each(function () {
            if ($(this).is(':checked')) {
                (procActivos.push(parseInt($(this).val()))); /** Array de checkbox checked*/
            }
        });

        $('#totalActivos').html(`(${procActivos.length})`);

        if (settings.json.objProcLength != procActivos.length) {
            $.ajax({
                url: "op/crud.php",
                type: "POST",
                data: {
                    plantillaProc: true,
                    checkProc: JSON.stringify(procActivos),
                    PlaProPlan: $('#selPlantilla').val(),
                    PlaProDesc: $('#nombrePlantilla').text(),
                    actualizar: 'true',
                },
            });
        }
    });
    $.fn.DataTable.ext.pager.numbers_length = 5; // Se agrega el numero de paginas a mostrar en el paginador
});
