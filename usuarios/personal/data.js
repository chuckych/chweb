$(document).ready(function () {
    let testConnect = true
    fetch('../clientes/testConnect.php?_c=' + $('#_c').val())
        .then(response => response.json())
        .then(data => {
            if (data.status == "Error") {
                testConnect = false
                $('#f1').hide()
                notify('No hay conexión con Control Horario', 'warning', 5000, 'right')
            }
        });

    $("#marcar").on("click", function () {
        $('.LegaCheck').prop('checked', true)
    });
    $("#desmarcar").on("click", function () {
        $('.LegaCheck').prop('checked', false)
    });

    $('#table-personal thead tr').clone(true).appendTo('#table-personal thead');
    $('#table-personal thead tr:eq(1) th').each(function (i) {
        var title = $(this).text();
        $(this).html('<input type="text" class="form-control" placeholder="" />');

        $('input', this).on('keyup change', function () {
            if (table.column(i).search() !== this.value) {

                table
                    .column(i)
                    .search(this.value)
                    .draw();

            }
        });
    });

    $("#DivLegaPass").on("click", function () {

        if ($("#LegaPass").is(":checked")) {
            $("#LegaPass").val('true');
            $('#table-personal').DataTable().ajax.reload();
        } else {
            $("#LegaPass").val('false');
            $('#table-personal').DataTable().ajax.reload();
        };
    });

    let table =''
    if (testConnect) {
        table = $('#table-personal').DataTable({
            "createdRow": function (row, data, index) {
                $(row).addClass("animate__animated animate__fadeIn");
                $('td', row).addClass("align-middle");
            },
            bProcessing: true,
            orderCellsTop: true,
            fixedHeader: true,
            ajax: {
                url: "?p=array_personal.php",
                type: "GET",
                dataSrc: "personal",
                "data": function (data) {
                    data._c = $("#_crecid").val()
                    data.LegaPass = $("#LegaPass").val()
                },
            },

            dom: 'lrtip',
            deferRender: true,
            columns: [{
                "class": "",
                "data": "check"
            },
            {
                "class": "",
                "data": "pers_dni"
            },
            {
                "data": "pers_nombre"
            },
            {
                "data": "pers_empresa"
            }, {
                "data": "pers_planta"
            }, {
                "data": "pers_sector"
            }, {
                "data": "pers_grupo"
            }, {
                "data": "pers_sucur"
            }
            ],
            scrollY: '50vh',
            scrollX: true,
            scrollCollapse: true,
            paging: true,
            searching: true,
            info: true,
            ordering: 0,
            language: {
                "url": "../../js/DataTableSpanishShort2.json",
            }
        });
    }

});