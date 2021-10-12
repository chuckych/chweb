/** Variables para las notificaciones de pantalla */
$(function () {
    'use strict'
    $(".alta_cierre").bind("submit", function (e) {
        e.preventDefault();
        async function f() {
            $('#GetPersonal').DataTable().search('').draw()
        }
        f().then(
            $.ajax({
                type: $(this).attr("method"),
                url: $(this).attr("action"),
                data: $(this).serialize(),
                // async : false,
                beforeSend: function (data) {
                    $("#submit").prop("disabled", true);
                    $("#respuetatext").html("Generando Cierres");
                    $("#respuetatext").addClass("animate__animated animate__fadeIn");
                    $("#respuesta").addClass("alert-info");
                    $("#respuesta").removeClass("d-none");
                    $("#respuesta").removeClass("alert-success");
                    $("#respuesta").removeClass("alert-danger");
                    notify('Procesando Cierres', 'dark', 0, 'right')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $("#cierre").prop('disabled', false);
                        // $("#respuetatext").removeClass("animate__animated animate__fadeIn");
                        $("#respuetatext").html("");
                        $("#submit").prop("disabled", false);
                        $("#submit").html("Ingresar Cierres");
                        $("#respuesta").removeClass("alert-success");
                        $("#respuesta").removeClass("alert-danger");
                        $("#respuesta").removeClass("alert-info");
                        $('input[type="checkbox"]').prop('checked', false)
                        $.notifyClose();
                        notify(data.dato, 'success', 5000, 'right')
                        $('#GetPersonal').DataTable().ajax.reload(null, false);
                    } else {
                        $("#respuetatext").html("");
                        // $("#respuetatext").removeClass("animate__animated animate__fadeIn");
                        $("#submit").prop("disabled", false);
                        $("#submit").html("Ingresar Cierres");
                        $("#respuesta").removeClass("alert-success");
                        $("#respuesta").removeClass("alert-info");
                        $("#respuesta").removeClass("alert-danger");
                        $.notifyClose();
                        notify(data.dato, 'danger', 5000, 'right')
                        $('#GetPersonal').DataTable().ajax.reload(null, false);
                    }
                }
            })
        ); // 1
        e.stopImmediatePropagation();
    });
    $("#submit").html("Ingresar Cierres");
    let table = $('#GetPersonal').DataTable({
        lengthMenu: [[10, 50, 100, 300, 500, 700, 900], [10, 50, 100, 300, 500, 700, 900]],
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        ajax: {
            url: "../personal/?p=array_personal.php",
            type: "GET",
            "data": function (data) {
                // data._sec = function() { return $("#Sect").val()},
                data.Per = $("#Per").val();
                data.Tipo = $("#Tipo").val();
                data.Emp = $("#Emp").val();
                data.Plan = $("#Plan").val();
                data.Sect = $("#Sect").val();
                data.Sec2 = $("#Sec2").val();
                data.Grup = $("#Grup").val();
                data.Sucur = $("#Sucur").val();
                data._c = $("#_c").val();
                data._r = $("#_r").val();
                data.Modulo = "Cierres"
            },
            error: function () {
                $("#GetPersonal_processing").css("display", "none");
            },

        },
        createdRow: function (row, data, dataIndex,) {
            $(row).addClass('animate__animated animate__fadeIn pointer');
        },
        columns: [
            {
                "class": "align-middle",
                "data": 'check'
            },
            {
                "class": "align-middle",
                "data": 'pers_legajo2'
            },
            {
                "class": "align-middle text-wrap",
                "data": 'pers_nombre2'
            },
            {
                "class": "align-middle ls1",
                "data": 'FechaCierre'
            },

        ],
        scrollY: '335px',
        scrollX: true,
        // scrollCollapse: false,
        paging: true,
        responsive: false,
        searching: true,
        info: true,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort.json"
        },
    });

    table.on('init.dt', function (settings) {
        $('div.loader').remove();
        $('#Personal-select-all').prop('checked', true)
        $('#GetPersonal_filter .form-control-sm').attr('placeholder', 'Buscar Legajo');
        // $("#EliminaCierre").prop('checked', false)
        if ($('#EliminaCierre').is(":checked")) {
            $("#EliminaCierre").prop('checked', true)
        } else {
            $("#EliminaCierre").prop('checked', false)
        }
    });
    table.on('draw.dt', function (settings) {
        // $('#GetPersonal input[type="checkbox"]').prop('checked', true)
        // $('#Personal-select-all').prop('checked', true)
        $('.check').prop('checked', true)
        // if ($('#Personal-select-all').is(":checked")) {
        $("#Personal-select-all").prop('checked', true)
        // }else{
        //     $("#Personal-select-all").prop('checked', false)
        // }
    });
    // Handle click on "Select all" control
    $('#Personal-select-all').on('click', function () {
        // Check/uncheck all checkboxes in the table
        let rows = table.rows({ 'search': 'applied' }).nodes();
        $('.check', rows).prop('checked', this.checked);
        // $('#GetPersonal input[type="checkbox"]', rows).prop('checked', this.checked);
    });

    // Handle click on checkbox to set state of "Select all" control
    $('#GetPersonal tbody').on('change', 'input[type="checkbox"]', function () {
        // If checkbox is not checked
        if (!this.checked) {
            var el = $('#Personal-select-all').get(0);
            // If "Select all" control is checked and has 'indeterminate' property
            if (el && el.checked && ('indeterminate' in el)) {
                // Set visual state of "Select all" control 
                // as 'indeterminate'
                el.indeterminate = true;
            }
        }
    });

    singleDatePicker('#cierre', 'right', 'up')
});