$(document).ready(function () {
    let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
    ActiveBTN(false, "#submit", 'Procesando <span class = "dotting mr-1"> </span> ' + loading, 'Procesar')

    $(".procesando").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $.notifyClose();
                notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                ActiveBTN(true, "#submit", 'Procesando <span class = "dotting mr-1"> </span> ' + loading, 'Procesar')
            },
            success: function (data) {
                if (data.status == "ok") {
                    $.notifyClose();
                    notify(data.Mensaje, 'success', 5000, 'right')
                    ActiveBTN(false, "#submit", 'Procesando <span class = "dotting mr-1"> </span> ' + loading, 'Procesar')
                } else {
                    $.notifyClose();
                    notify(data.Mensaje, 'danger', 2000, 'right')
                    ActiveBTN(false, "#submit", 'Procesando <span class = "dotting mr-1"> </span> ' + loading, 'Procesar')
                }
            },
            error: function () {
                $.notifyClose();
                notify('Error', 'danger', 2000, 'right')
                ActiveBTN(false, "#submit", 'Procesando <span class = "dotting mr-1"> </span> ' + loading, 'Procesar')
            }

        });
        e.stopImmediatePropagation();
    });

    if ($(window).width() < 769) {
        $('input[name="_dr"]').prop('readonly', true)
    }
    $("#Legajos").prop('disabled', true)
    function checkleg() {
        if ($("#Legajos").is(":checked")) {
            $('#Personal-select-all').prop('checked', true)
            $('#Personal-select-all').prop('disabled', true)
            $('.check').prop('checked', true)
            $('.check').prop('disabled', true)
            $('#GetPersonal_filter input').prop('disabled', true)
        } else {
            // $('#Personal-select-all').prop('checked', false)
            $('#Personal-select-all').prop('disabled', false)
            $('.check').prop('checked', true)
            $('.check').prop('disabled', false)
            $('#GetPersonal_filter input').prop('disabled', false)
        };
    }
    table = $('#GetPersonal').DataTable({
        bProcessing: true,
        bServerSide: true,
        deferRender: true,
        ajax: {
            url: "getPersonal.php",
            type: "POST",
            "data": function (data) {
                // data._sec = function() { return $("#Sect").val()},
                data.Per = $("#ProcPer").val();
                data.Tipo = $("#ProcTipo").val();
                data.Emp = $("#ProcEmp").val();
                data.Plan = $("#ProcPlan").val();
                data.Sect = $("#ProcSect").val();
                data.Sec2 = $("#ProcSec2").val();
                data.Grup = $("#ProcGrup").val();
                data.Sucur = $("#ProcSucur").val();
            },
            error: function () {
                $("#GetPersonal_processing").css("display", "none");
            },
        },
        columns: [
            {
                "class": "align-middle animate__animated animate__fadeIn",
                "data": 'check'
            },
            {
                "class": "align-middle animate__animated animate__fadeIn",
                "data": 'pers_legajo2'
            },
            {
                "class": "align-middle animate__animated animate__fadeIn",
                "data": 'pers_nombre2'
            },

        ],
        scrollY: '335px',
        scrollX: true,
        scrollCollapse: false,
        paging: true,
        responsive: false,
        searching: true,
        info: true,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort2.json"
        },
    });

    table.on('init.dt', function () {
        $('div.loader').remove();
        $('#Personal-select-all').prop('checked', true)
        $('#Personal-select-all').prop('disabled', true)
        $('.check').prop('checked', true)
        $('.check').prop('disabled', true)
        $('#GetPersonal_filter input').attr('placeholder', 'Buscar Legajos')
        $('#GetPersonal_filter input').prop('disabled', true)
        $("#Legajos").change(function () {
            checkleg()
        });
        $("#Legajos").prop('disabled', false)
    });
    table.on('draw.dt', function () {
        checkleg()
    });
    // Handle click on "Select all" control
    $('#Personal-select-all').on('click', function () {
        // Check/uncheck all checkboxes in the table
        var rows = table.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
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
});