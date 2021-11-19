$(function () {
    "use strict";
    let testConnect = true;
    fetch("../clientes/testConnect.php?_c=" + $("#_c").val())
        .then(response => response.json())
        .then(data => {
            if (data.status == "Error") {
                testConnect = false;
                $("#f1").hide();
                notify(
                    "No hay conexión con Control Horario",
                    "warning",
                    5000,
                    "right"
                );
            }
        });

    $("#table-personal thead tr").clone(true).appendTo("#table-personal thead");
    $("#table-personal thead tr:eq(1) th").each(function (i) {
        var title = $(this).text();
        $(this).html(
            '<input type="text" class="form-control" placeholder="" />'
        );

        $("input", this).on("keyup change", function () {
            if (table.column(i).search() !== this.value) {
                table.column(i).search(this.value).draw();
            }
        });
    });

    $("#DivLegaPass").hide();

    if (testConnect) {
        let table = $("#table-personal").DataTable({
            createdRow: function (row, data, index) {
                $(row).addClass("animate__animated animate__fadeIn fila");
                $("td", row).addClass("align-middle");
            },
            bProcessing: true,
            orderCellsTop: true,
            fixedHeader: true,
            ajax: {
                url: "?p=array_personal.php",
                type: "GET",
                dataSrc: "personal",
                data: function (data) {
                    data._c = $("#_crecid").val();
                    data.LegaPass = $("#LegaPass").val();
                }
            },
            dom:
                "<'row'<'col-12 col-sm-6 d-inline-flex justify-content-start align-items-center'l<'divCheck'>><'col-12 col-sm-6 d-flex justify-content-end'<'divAlta'>f>>" +
                "<'row'<'col-12 table-responsive mt-sm-0 mt-2't>>" +
                "<'row mt-2'<'col-12 col-sm-5'i><'col-12 col-sm-7 d-flex justify-content-end'p>>",
            deferRender: true,
            columns: [
                {
                    class: "",
                    data: "check"
                },
                {
                    class: "",
                    data: "pers_dni"
                },
                {
                    data: "pers_nombre"
                },
                {
                    data: "pers_empresa"
                },
                {
                    data: "pers_planta"
                },
                {
                    data: "pers_sector"
                },
                {
                    data: "pers_grupo"
                },
                {
                    data: "pers_sucur"
                }
            ],
            lengthMenu: [
                [10, 25, 50, 100, 200, 300],
                [10, 25, 50, 100, 200, 300]
            ],
            scrollY: '30vh',
            scrollX: true,
            scrollCollapse: true,
            paging: true,
            searching: true,
            info: true,
            ordering: 0,
            language: {
                url: "../../js/DataTableSpanishShort2.json?" + vjs()
            }
        });

        cargando_table();
        table.on("init.dt", function (e, settings) {

            let idTable = "#" + e.target.id;
            let lengthMenu = $(idTable + "_length select");
            $(lengthMenu).addClass("h35");
            let filterInput = $(idTable + "_filter input");
            $(filterInput).attr({
                placeholder: "Buscar dato..", //placeholder
                autocomplete: "off" //autocomplete
            });

            $("#DivLegaPass").on("click", function (e) {
                e.preventDefault();
                if ($("#LegaPass").is(":checked")) {
                    $("#LegaPass").val("false");
                    $("#LegaPass").prop("checked", false);
                    $("#table-personal").DataTable().ajax.reload();
                } else {
                    $("#LegaPass").val("true");
                    $("#LegaPass").prop("checked", true);
                    $("#table-personal").DataTable().ajax.reload();
                }
                $(".LegaCheck ").attr('disabled', true);
                cargando_table();
            });

            if (settings.iDraw == 2) {
                $("#DivLegaPass").show();
                $(".divCheck").html(`
                <div class="d-flex align-items-center ml-2">
                    <button type="button" class="p-0 fontq btn btn-link text-secondary mr-2" id="marcar"><i class="bi bi-check2-square mr-2"></i>Marcar</button>
                    <button type="button" class="p-0 fontq btn btn-link text-secondary" id="desmarcar">
                        <i class="bi bi-dash-square mr-2"></i>
                        Desmarcar
                    </button>
                </div>
            `);
                $("#marcar").on("click", function (e) {
                    e.preventDefault();
                    $(".LegaCheck").prop("checked", true);
                });
                $("#desmarcar").on("click", function (e) {
                    e.preventDefault();
                    $(".LegaCheck").prop("checked", false);
                });
            }
        });
        table.on("draw.dt", function (e, settings) {
            if (settings.iDraw > 1) {
                $('._cargando').remove();
                let lengthData = JSON.parse(
                    JSON.stringify(table.rows().data())
                ).length;
                if (lengthData === 0) {
                    $("#submit").attr("disabled", true).hide();
                }
                console.log(lengthData);
            }
        });

    }
    onOpenSelect2();
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

    $(".SelecRol").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Seleccionar Rol",
        templateResult: template,
        templateSelection: template,
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
            url: "getRoles.php",
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    _c: getParameterByName("_c")
                };
            },
            processResults: function (data) {
                return {
                    results: data,
                };
            },
        },
    });
    $('.SelecRol').on("select2:select", function (e) {
        $('.select2-container').removeClass("border border-danger");
        $.notifyClose();
    });

    $("#f1").bind("submit", function (e) {
        e.preventDefault();
        console.log($(".SelecRol").val());
        if (!$(".SelecRol").val()) {
            $.notifyClose();
            $('.select2-container').addClass("border border-danger");
            let textErr = `<span class="">Debe seleccionar un rol<span>`;
            notify(textErr, "danger", 0, "right");
            return;
        }
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize() + '&submit=Importar',
            beforeSend: function (data) {
                $.notifyClose();
                notify("Aguarde. . .", "dark", 0, "right");
                ActiveBTN(!0, "#submit", '<i class="bi-download font1 mr-2"></i>AGUARDE..', '<i class="bi-download font1 mr-2"></i>IMPORTAR');
            },
            success: function (data) {
                if (data.status == "ok") {
                    $.notifyClose();
                    notify(data.Mensaje, "success", 5000, "right");
                    cargando_table();
                    $("#table-personal").DataTable().ajax.reload();
                } else {
                    $.notifyClose();
                    notify(data.Mensaje, "danger", 5000, "right");
                }
                ActiveBTN(!1, "#submit", '<i class="bi-download font1 mr-2"></i>AGUARDE..', '<i class="bi-download font1 mr-2"></i>IMPORTAR');
            },
            error: function (data) {
                ActiveBTN(!1, "#submit", '<i class="bi-download font1 mr-2"></i>AGUARDE..', '<i class="bi-download font1 mr-2"></i>IMPORTAR');
                $.notifyClose();
                notify("Error", "danger", 5000, "right");
            }
        });
        e.stopImmediatePropagation();
    });

});
