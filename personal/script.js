$(function () {

    function ActualizaTablas() {
        CheckSesion()
        $('#table-personal').DataTable().ajax.reload();
    };

    $("#_eg").click(function () {
        CheckSesion()
        if ($("#_eg").is(":checked")) {
            $("#_eg").val('on').trigger('change')
            $('#table-personal').DataTable().ajax.reload();
        } else {
            $("#_eg").val('off').trigger('change')
            $('#table-personal').DataTable().ajax.reload();
        }
    });
    $("#_porApNo").click(function () {
        CheckSesion()
        if ($("#_porApNo").is(":checked")) {
            $("#_porApNo").val('on').trigger('change')
            $('#table-personal').DataTable().ajax.reload();
        } else {
            $("#_porApNo").val('off').trigger('change')
            $('#table-personal').DataTable().ajax.reload();
        }
    });
    $('#table-personal').dataTable({
        "initComplete": function (settings) {
            $("#PersonalTable").removeClass('invisible');
            classEfect("#PersonalTable", 'animate__animated animate__fadeIn')
            // $("#table-personal_filter .form-control").attr('placeholder', 'Buscar')
            $('#table-personal_filter input').removeClass('form-control-sm')
            $('#table-personal_filter input').attr("style", "height: 40px !important; width:250px;");
            $('#table-personal_filter input').attr('placeholder', 'Filtrar Legajo / Nombre')
            select2Simple('#table-personal_length select', '', false, false)
        },
        "drawCallback": function (settings) {
            $("td").tooltip({ container: 'table' });
            $('[data-toggle="tooltip"]').tooltip();
            if ($("#_eg").is(":checked")) {
                $('td').addClass('text-danger')
            } else {
                $('td').removeClass('text-danger')
            }

        },
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        // stateSave: true,
        // stateDuration: -1,
        lengthMenu: [[5, 10, 25, 50, 100, 200], [5, 10, 25, 50, 100, 200]],
        dom: "<'row'" +
            "<'col-12 col-sm-6 d-flex align-items-start'l><'col-12 col-sm-6 d-inline-flex align-items-start justify-content-end'f>>" +
            "<'row '<'col-12 table-responsive't>>" +
            "<'row'<'col-12 d-flex bg-transparent align-items-center justify-content-between'<i><p>>>",
        "ajax": {
            url: "getPersonal.php",
            type: "POST",
            dataType: "json",
            "data": function (data) {
                data._eg = $("input[name=_eg]:checked").val();
                data._porApNo = $("input[name=_porApNo]:checked").val();
                data.Per = $("#Per").val();
                data.Emp = $("#Emp").val();
                data.Plan = $("#Plan").val();
                data.Sect = $("#Sect").val();
                data.Sec2 = $("#Sec2").val();
                data.Grup = $("#Grup").val();
                data.Sucur = $("#Sucur").val();
                data.Tipo = $("#Tipo").val();
                data.Tare = $("#Tare").val();
                data.Conv = $("#Conv").val();
                data.Regla = $("#Regla").val();
            },
            error: function () {
                $("#table-personal").css("display", "none");
            }
        },
        columns: [
            {
                "class": "",
                "data": 'editar'
            },
            {
                className: '', targets: '', title: 'LEGAJO',
                "render": function (data, type, row, meta) {
                    return '<span class="text-secondary fontq">' + row.pers_legajo + '</span>' + '<br />' + row.pers_nombre;
                },
            },
            // {
            //     "class": "",
            //     "data": 'pers_legajo'
            // },
            // {
            //     "class": "",
            //     "data": 'pers_nombre'
            // },
            {
                "class": "text-center ",
                "data": 'pers_tipo'
            },
            // {
            //     "class": "",
            //     "data": 'pers_estado'
            // },
            {
                "class": "",
                "data": 'pers_empresa'
            },
            // {
            //     "class": "",
            //     "data": 'pers_planta'
            // },

            {
                "class": "",
                "data": 'pers_sector'
            },
            // {
            //     "class": "",
            //     "data": 'pers_seccion'
            // },
            {
                "class": "",
                "data": 'pers_grupo'
            },
            {
                "class": "w-100",
                "data": 'pers_convenio'
            },
            // {
            //     "class": "",
            //     "data": 'pers_sucur'
            // },
        ],
        // scrollY: '50vh',
        // scrollX: true,
        paging: true,
        searching: true,
        // scrollCollapse: true,
        info: true,
        ordering: 0,
        responsive: 0,
        language: {
            "url": "/" + _homehost + "/js/DataTableSpanishShort2.json?" + vjs()
        }
    });
});
