// $(window).on('load', function () {
//     $("#ConceptosModal").modal("show");
// });
$(function () {

    // $('#ConceptosModal').css( 'display', 'block' );
    // tableConceptos.columns.adjust().draw();
    $("#divGetConceptos").hide()
    $('#ConceptosModal').on('show.bs.modal', function (e) {
        if ($("#tconcepto").val() == '0') {
            var tableConceptos = $('#GetConceptos').DataTable({
                "initComplete": function (settings, json) {
                    $('#GetConceptos_filter .form-control-sm').attr('placeholder', 'Buscar Novedad')
                    $("#tconcepto").val('1')
                },
                "drawCallback": function (settings) {
                    $("#divGetConceptos").show()
                    $("#divGetConceptos").addClass('animate__animated animate__fadeIn')
                },
                // "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                // },
                "createdRow": function (row, data, index) {
                    // $(row).addClass("animated fadeIn align-middle");
                },
                dom: '<"w-100 d-inline-flex"<f><"ml-3 d-flex justify-content-end w-100"i>>t',
                bProcessing: true,
                stateSave: true,
                stateDuration: -1,
                "ajax": {
                    url: 'getConceptos.php?v=' + $.now(),
                    type: "POST",
                    "data": function (data) {
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
                scrollY: '50vh',
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
        }
    })
    $('#ConceptosModal').on('hidden.bs.modal', function (e) {
        $('.respuesta ').html('')
    })
    $("#FormConceptos").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize() + '&submit=params',
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
function CheckTipo(selectorclick, selectorcheck) {
    $(document).on("click", selectorclick, function (e) {
        if ($(selectorcheck).is(':checked')) {
            $(selectorcheck).attr('checked', false)
        } else {
            $(selectorcheck).attr('checked', true)
        }
    });
}
CheckTipo('.Lle', '.Lle_Pre')
CheckTipo('.Inc', '.Inc_Pre')
CheckTipo('.Sal', '.Sal_Pre')
CheckTipo('.Aus', '.Aus_Aus')
CheckTipo('.Lic', '.Lic_Aus')
CheckTipo('.Acc', '.Acc_Aus')
CheckTipo('.Vac', '.Vac_Aus')
CheckTipo('.Sus', '.Sus_Aus')
CheckTipo('.ART', '.ART_Aus')


