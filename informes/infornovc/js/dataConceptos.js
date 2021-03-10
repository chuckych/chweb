// $(window).on('load', function () {
//     $("#ConceptosModal").modal("show");
// });
$(function () {
    var tableConceptos = $('#GetConceptos').DataTable({
        "initComplete": function (settings, json) {
            +
            $('#GetConceptos_filter .form-control-sm').attr('placeholder', 'Buscar Novedad')
        },
        "drawCallback": function (settings) {
        },
        // "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        // },
        "createdRow": function (row, data, index) {
            // $(row).addClass("animated fadeIn align-middle");
        },
        // "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Todo"] ],
        // columnDefs: [
        //     { orderable: false, targets: 0 },
        //     { orderable: false, targets: 1 },
        //     { orderable: false, targets: 2 },
        //     { orderable: false, targets: 3 },
        //     { orderable: false, targets: 4 },
        //     { orderable: false, targets: 5 },
        // ],
        // fixedHeader: true,
        dom: '<"w-100 d-inline-flex"<f><"ml-3 d-flex justify-content-end w-100"i>>t',
        bProcessing: true,
        stateSave: true,
        stateDuration: -1,
        "ajax": {
            url: 'getConceptos.php?v=' + $.now(),
            type: "POST",
            "data": function (data) {
                // data.fecha = $("#_drnovc").val();
            },
            error: function () { },
        },
        columns: [
            {
                "class": "text-center",
                "data": 'Presentes'
            },
            {
                "class": "text-center",
                "data": 'Ausentes'
            },
            {
                "class": "text-center",
                "data": 'NovCodi'
            },
            {
                "class": "",
                "data": 'NovDesc'
            },
            {
                "class": "text-center",
                "data": 'NovID'
            },
            {
                "class": "",
                "data": 'NovTipo'
            },
            {
                "class": "w-100 text-white",
                "data": 'n'
            }
        ],
        scrollY:        '50vh',
        scrollCollapse: true,
        deferRender: true,
        bLengthChange: false,
        paging: false,
        searching: true,
        info: true,
        ordering: false,
        language: {
            "sProcessing": "Actualizando . . .",
            "sLengthMenu": "_MENU_",
            "sInfo": "Total Novedades: _TOTAL_",
            "sInfoEmpty": "No se encontraron resultados",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "",
            "sLoadingRecords": "<div class='spinner-border text-light'></div>",
            "sEmptyTable": "",
            "sZeroRecords": "Sin novedades",
        },
    })
    $('#ConceptosModal').on('shown.bs.modal', function (e) {
        $('#GetConceptos_filter .form-control-sm').focus()
    })

    $("#FormConceptos").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize()+'&submit=params',
            // async : false,
            beforeSend: function (data) {
                ActiveBTN(true, '#btnsubmit', 'Aguarde..', 'Guardar')
            },
            success: function (data) {
                if (data.status == "ok") {
                    ActiveBTN(false, '#btnsubmit', 'Aguarde..', 'Guardar')
                    $('.respuesta').html('<div class="fontq text-success fw5">' + data.Mensaje + '</div>')
                    $('#GetPresentismo').DataTable().ajax.reload()
                   
                } else {
                    ActiveBTN(false, '#btnsubmit', 'Aguarde..', 'Guardar')
                    $('.respuesta').html('<div class="fontq text-danger fw5">' + data.Mensaje + '</div>')
                }
            },
            error: function (data) {
                ActiveBTN(false, '#btnsubmit', 'Aguarde..', 'Guardar')
                $('.respuesta').html('<div class="fontq text-danger fw5">Error</div>')
            }
        });
        e.stopImmediatePropagation();
    });

});

