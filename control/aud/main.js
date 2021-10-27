$(function () {
    "use strict";
    function tipoAud(tipo) {
        let a = "";
        switch (tipo) {
            case "A":
                a = "Alta";
                break;
            case "B":
                a = "Baja";
                break;
            case "M":
                a = "Modificación";
                break;
            case "P":
                a = "Proceso";
                break;
            default:
                a = row["tipo"];
                break;
        }
        return a;
    }
    let tableAuditoria = $("#tableAuditoria").dataTable({
        lengthMenu: [
            [5, 10, 25, 50, 100],
            [5, 10, 25, 50, 100],
        ],
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        responsive: true,
        // stateSave: true,
        // stateDuration: -1,
        dom:
            "<'row fila invisible animate__animated animate__fadeInDown'<'col-12 col-sm-6 d-flex justify-content-start'<'filtros'>l><'col-12 col-sm-6 d-flex justify-content-end'<'ml-1 _dr'><'refresh'>>>" +
            "<'row'<'col-12 divFiltros'>>" +
            "<'row fila invisible animate__animated animate__fadeInDown'<'col-12'f>>" +
            "<'row animate__animated animate__fadeIn'<'col-12'tr>>" +
            "<'row animate__animated animate__fadeIn'<'col-12 col-sm-5'i><'col-12 col-sm-7 d-flex justify-content-end'p>>",
        ajax: {
            url: "getAuditoria.php?" + $.now(),
            type: "POST",
            dataType: "json",
            data: function (data) {
                // data._eg = $("input[name=_eg]:checked").val();
                data._dr = $("#_dr").val();
                data.nombreAud = $("#nombreAud").val();
                data.tipoAud = $("#tipoAud").val();
                data.userAud = $("#userAud").val();
                data.idSesionAud = $("#idSesionAud").val();
                data.horaAud = $("#horaAud").val();
                data.horaAud2 = $("#horaAud2").val();
                data.cuentaAud = $("#cuentaAud").val();
            },
            error: function () {
                $("#tablePersonal").css("display", "none");
            },
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass("animate__animated animate__fadeIn");
        },
        columns: [
            {
                className: "",
                targets: "",
                title: "Usuario",
                render: function (data, type, row, meta) {
                    let datacol =
                        `<div class="pointer" data="` +
                        row["id_sesion"] +
                        `" data-titler="Usuario: ` +
                        row["nombre"] +
                        `"><span class="fw5">` +
                        row["nombre"] +
                        `</span><br><span class="">` +
                        row["usuario"] +
                        `</span></div>`;
                    return datacol;
                },
            },
            {
                className: "text-center",
                targets: "",
                title: "ID Sesion",
                render: function (data, type, row, meta) {
                    let datacol = "<div>" + row["id_sesion"] + "</div>";
                    return datacol;
                },
            },
            {
                className: "",
                targets: "",
                title: "Cuenta",
                render: function (data, type, row, meta) {
                    let datacol = "<div>" + row["audcuenta_nombre"] + "</div>";
                    return datacol;
                },
            },
            {
                className: "ls1",
                targets: "",
                title: "Fecha",
                render: function (data, type, row, meta) {
                    let datacol =
                        "<div>" +
                        moment(row["fecha"]).format("DD/MM/YYYY") +
                        "</div>";
                    return datacol;
                },
            },
            {
                className: "ls1",
                targets: "",
                title: "Hora",
                render: function (data, type, row, meta) {
                    let datacol = "<div>" + row["hora"] + "</div>";
                    return datacol;
                },
            },
            {
                className: "text-center",
                targets: "",
                title: "Tipo",
                render: function (data, type, row, meta) {
                    let datacol =
                        `<div data-titlel="` +
                        tipoAud(row["tipo"]) +
                        `">` +
                        row["tipo"] +
                        `</div>`;
                    return datacol;
                },
            },
            {
                className: "w-100",
                targets: "",
                title: "Dato",
                render: function (data, type, row, meta) {
                    let datacol =
                        `<div data-titlel="` +
                        row["dato"] +
                        `">` +
                        row["dato"] +
                        `</div>`;
                    return datacol;
                },
            },
        ],
        paging: true,
        searching: true,
        info: true,
        ordering: 0,
        responsive: 0,
        language: {
            url: "../../js/DataTableSpanishShort2.json" + "?" + vjs(),
        },
    });
    function inputFechas(start_date, end_date, newStart = "") {
        let min_year = moment(start_date, "YYYY");
        let max_year = moment(end_date, "YYYY");

        newStart = newStart ? newStart : end_date;
        $("#_dr").daterangepicker({
            singleDatePicker: false,
            showDropdowns: true,
            minYear: parseInt(min_year),
            maxYear: parseInt(max_year),
            startDate: newStart,
            endDate: end_date,
            minDate: start_date,
            maxDate: end_date,
            showWeekNumbers: false,
            autoUpdateInput: true,
            opens: "center",
            drops: "down",
            autoApply: false,
            alwaysShowCalendars: true,
            linkedCalendars: false,
            buttonClasses: "btn btn-sm fontq",
            applyButtonClasses: "btn-custom fw4 px-3 opa8",
            cancelClass: "btn-link fw4 text-gris",
            ranges: {
                Hoy: [moment(), moment()],
                Ayer: [
                    moment().subtract(1, "days"),
                    moment().subtract(1, "days"),
                ],
                "Ultimos 7 Días": [moment().subtract(6, "days"), moment()],
                "Ultimos 30 Días": [moment().subtract(29, "days"), moment()],
                "Este Mes": [
                    moment().startOf("month"),
                    moment().endOf("month"),
                ],
                "Ultimo Mes": [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month"),
                ],
                "Todo el Periodo": [start_date, end_date],
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
                monthNames: [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre",
                ],
                firstDay: 1,
                alwaysShowCalendars: true,
                applyButtonClasses: "btn-custom fw5 px-3 opa8",
            },
        });
        $("#_dr").on("apply.daterangepicker", function (ev, picker) {
            $("#tableAuditoria").DataTable().ajax.reload();
        });
    }

    tableAuditoria.on("init.dt", function (e, settings) {
        $("#tableAuditoria_length select").addClass("h35");
        $("#tableAuditoria_filter input").attr("placeholder", "Buscar dato..");
        $("#tableAuditoria_filter input").attr("id", "datosAud");
        $("#tableAuditoria_filter input").attr("autoc", "datosAud");
        $("#tableAuditoria_filter input").attr("autocomplete", "off");

        fetch("getFechas.php")
            .then((response) => response.json())
            .then((data) => {
                // console.log(max_year);
                $("._dr").html(
                    `<label><input title="Filtrar Fecha" type="text" id="_dr" class="form-control h35 text-center ls1 w200" autocomplete=off></label>`
                );
                $(".refresh").html(
                    `<label><button data-titlel="Actualizar Grilla"class="btn ml-1 h35 btn-custom fontq" id="refresh"><i class="bi bi-arrow-repeat"></i></button></label>`
                );
                $(".filtros").html(
                    `<div class="d-inline-flex align-items-center"><button data-titler="Filtros" class="btn h35 btn-outline-custom border fontq" id="filtros" type="button" data-toggle="collapse" data-target="#collapseFiltros" aria-expanded="false" aria-controls="collapseFiltros">Filtros</button><span id="trash_all" data-titler="Limpiar Filtros" class="mx-2 bi bi-trash fontq text-secondary pointer"></span></div>`
                );

                fetch("filtros.php")
                    .then((response) => response.text())
                    .then((data) => {
                        $(".divFiltros").html(data);
                        $(".fila").removeClass("invisible");
                    });

                inputFechas(
                    moment(data.start_date).format("DD/MM/YYYY"),
                    moment(data.end_date).format("DD/MM/YYYY")
                );

                let iniEndDate = moment(
                    $("#_dr").data("daterangepicker").endDate._d
                ).format("YYYYMMDD");

                $("#refresh").on("click", function () {
                    fetch("getFechas.php")
                        .then((response) => response.json())
                        .then((data) => {
                            let _dr_start = moment(
                                $("#_dr").data("daterangepicker").startDate._d
                            ).format("DD/MM/YYYY");

                            let end_db = moment(data.end_date).format(
                                "DD/MM/YYYY"
                            );
                            let end_db_str = moment(data.end_date).format(
                                "YYYYMMDD"
                            );

                            let finEndDate =
                                parseInt(iniEndDate) < parseInt(end_db_str)
                                    ? end_db_str
                                    : iniEndDate;

                            if (parseInt(iniEndDate) < parseInt(finEndDate)) {
                                inputFechas(_dr_start, end_db, _dr_start);
                                iniEndDate = finEndDate;
                                $("#tableAuditoria").DataTable().ajax.reload();
                            } else {
                                $("#tableAuditoria")
                                    .DataTable()
                                    .ajax.reload(null, false);
                            }
                        });
                });
            });
    });
    tableAuditoria.on("page.dt", function () {
        $("#tableAuditoria div").addClass("bg-light text-light");
    });
    tableAuditoria.on("draw.dt", function () {
        $("#tableAuditoria div").removeClass("bg-light text-light");
        $("#divTableAud").show();
    });
    onOpenSelect2();
});
