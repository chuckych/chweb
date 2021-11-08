$(function() {
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
    $("#table-personal thead tr:eq(1) th").each(function(i) {
        var title = $(this).text();
        $(this).html(
            '<input type="text" class="form-control" placeholder="" />'
        );

        $("input", this).on("keyup change", function() {
            if (table.column(i).search() !== this.value) {
                table.column(i).search(this.value).draw();
            }
        });
    });
    $("#DivLegaPass").hide();
    $("#DivLegaPass").on("click", function() {
        $("#DivLegaPass").hide();
        if ($("#LegaPass").is(":checked")) {
            $("#LegaPass").val("true");
            $(".fila").addClass("pre-carga");
            $("#table-personal").DataTable().ajax.reload();
        } else {
            $("#LegaPass").val("false");
            $(".fila").addClass("pre-carga");
            $("#table-personal").DataTable().ajax.reload();
        }
    });

    let table = "";
    if (testConnect) {
        table = $("#table-personal").DataTable({
            createdRow: function(row, data, index) {
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
                data: function(data) {
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
                url: "../../js/DataTableSpanishShort2.json?"+vjs()
            }
        });

        table.on("init.dt", function(e, settings) {
            $(".divCheck").html(`
                <div class="d-flex align-items-center ml-2">
                    <button type="button" class="p-0 fontq btn btn-link text-secondary mr-2" id="marcar"><i class="bi bi-check2-square mr-2"></i>Marcar</button>
                    <button type="button" class="p-0 fontq btn btn-link text-secondary" id="desmarcar">
                        <i class="bi bi-dash-square mr-2"></i>
                        Desmarcar
                    </button>
                </div>
            `);
            $("#marcar").on("click", function() {
                $(".LegaCheck").prop("checked", true);
            });
            $("#desmarcar").on("click", function() {
                $(".LegaCheck").prop("checked", false);
            });
        });
        table.on("draw.dt", function(e, settings) {
            $("#DivLegaPass").show();
        });

    }
});
