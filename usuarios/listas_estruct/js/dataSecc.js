$(function () {
    'use strict'

    let cliente = $('#cliente').val()
    let uid = $('#uid').val()
    
    let tableSecciones = $('#tableSecciones').dataTable({
        drawCallback: function (settings) {
            if(settings.aoData.length===0){
                $("#tableSecciones").parents('.table-responsive').hide();
                $('#seccion').html('<div class="mt-3 alert alert-info fontq fw5">No hay secciones disponibles</div>')
            }else{
                $("#tableSecciones").parents('.table-responsive').show();
                $('#seccion').html('')
            }
        }, 
        dom: "<'row'<'col-12 divFilter d-flex align-items-center justify-content-between pt-1'f>>" +
            "<'row'<'col-12 divtablesecc 't>>" +
            "<'row'<'col-12 d-flex align-items-center justify-content-end'>" +
            "<'col-12 d-flex align-items-center justify-content-between divInfo pt-2 pl-0 pr-0'i><'col-12 pt-2 pl-0 divRefresh'>>",
        "ajax": {
            url: "listas_estruct/getListas.php",
            type: "GET",
            dataType: "json",
            "data": function (data) {
                data._c = cliente,
                data.lista   = 5,
                data.uid     = uid,
                data.rel = relacionesSwith
            },
            error: function () {
                $("#tableSecciones").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex,) {
            $(row).addClass('animate__animated animate__fadeIn pointer checkSeccion');
        },
        rowGroup: {
            className: '',
            dataSrc: function (row) {
                return `<div class="pointer btn btn-link m-0 p-0 fontq groupTipo" data-tipo="Sec_` + row.SecCodi + `">` + row.SecDesc + `</div>`
            }
        },
        columns: [
            {
                className: 'align-middle', targets: 'Se2Codi', title: '',
                "render": function (data, type, row, meta) {
                    let datacol = row['Se2Codi']
                    return datacol;
                },
            },
            {
                className: 'align-middle w-100 ', targets: 'Se2Desc', title: '',
                "render": function (data, type, row, meta) {
                    let datacol = row['Se2Desc']
                    return datacol;
                },
            },
            {
                className: 'align-middle', targets: 'totLeg', title: '',
                "render": function (data, type, row, meta) {
                    let datacol = '<span data-titlel="Total Legajos">'+row['totLeg']+'</span>'
                    return datacol;
                },
            },
            {
                className: 'align-middle', targets: '', title: '',
                "render": function (data, type, row, meta) {
                    let checked = (row.set === 1) ? 'checked' : ''
                    let codSeccion = row['codigo']
                    if (checked) {
                        setTimeout(() => {
                            $('#Secci_' + codSeccion).parents('tr').addClass('table-active')
                        }, 200);
                    }
                    let classTipo = 'class_Sec_' + row['SecCodi']
                    let datacol = `
                    <div class="custom-control custom-checkbox">
                        <input `+ checked + ` type="checkbox" class="custom-control-input ` + classTipo + `" id="Secci_` + codSeccion + `" value="`+codSeccion +`">
                        <label class="custom-control-label" for="Secci_`+ codSeccion + `"></label>
                    </div>
                    `
                    return datacol;
                },
            },
        ],
        bProcessing: true,
        serverSide: false,
        deferRender: true,
        // stateSave: true,
        // stateDuration: -1,
        paging: false,
        searching: true,
        info: true,
        ordering: 0,
        responsive: 0,
        language: {
            "sProcessing": "Actualizando . . .",
            "sLengthMenu": "_MENU_",
            "sZeroRecords": "",
            "sEmptyTable": "",
            "sInfo": "_START_ al _END_ de _TOTAL_ Secciones",
            "sInfoEmpty": "No se encontraron resultados",
            "sInfoFiltered": "<br>(Filtrado de un total de _MAX_ Secciones)",
            "sInfoPostFix": "",
            "sSearch": "",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "<div class='spinner-border text-light'></div>",
            "oPaginate": {
                "sFirst": "<i class='bi bi-chevron-left'></i>",
                "sLast": "<i class='bi bi-chevron-right'></i>",
                "sNext": "<i class='bi bi-chevron-right'></i>",
                "sPrevious": "<i class='bi bi-chevron-left'></i>"
            },
            "oAria": {
                "sSortAscending": ":Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ":Activar para ordenar la columna de manera descendente"
            }
        }
    });
    tableSecciones.on('init.dt', function () {

        $('#tableSecciones_info').css('margin-top', '0px')
        $('#tableSecciones_info').addClass('p-0')

        $('#tableSecciones tbody').on('click', '.checkSeccion', function (e) {
            e.preventDefault();
            let data = $(tableSecciones).DataTable().row($(this)).data();
            if ($('#Secci_' + data.codigo).is(":checked")) {
                $('#Secci_' + data.codigo).prop('checked', false)
                $(this).removeClass('table-active')
            } else {
                $('#Secci_' + data.codigo).prop('checked', true)
                $(this).addClass('table-active')
            };
        });
        $('#tableSecciones tbody').on('click', '.groupTipo', function (e) {
            e.preventDefault();
            let tipo = $(this).attr('data-tipo')
            if ($('.class_' + tipo).is(":checked")) {
                $('.class_' + tipo).prop('checked', false)
                $('.class_' + tipo).parents('tr').removeClass('table-active')
            } else {
                $('.class_' + tipo).prop('checked', true)
                $('.class_' + tipo).parents('tr').addClass('table-active')
            };

        })

        $("#tableSecciones_filter .form-control").attr('placeholder', 'Buscar Sección')
        $(this).children('thead').remove()
        $('.divtablesecc').css('max-height', '300px')
        $('.divtablesecc').addClass('overflow-auto')

        // let buttonsRefresh = `
        //     <div class="">
        //         <button class="btn btn-link btn-sm fontq p-0 m-0" id="refreshList">Actualizar Grilla</button>
        //     </div>
        //     `
        let buttonsCheck = `
            <div class="">
                <button class="btn btn-link btn-sm fontq" id="checkAllSecc">Marcar</button>
                <button class="ml-1 btn btn-link btn-sm fontq" id="nocheckAllSecc">Desmarcar</button>
            </div>
            `
        // let buttonsSubmit = `
        //     <div class="">
        //         <button type="button" class="btn btn-custom btn-sm btn-mobile fontq w100" id="aplicarSecci">Aplicar</button>
        //     </div>
        //     `
        $(this).parents().find('.divFilter').prepend(buttonsCheck)
        // $(this).parents().find('.divRefresh').append(buttonsRefresh)

        $('#refreshList').on('click', function (e) {
            e.preventDefault();
            habilitarRelacionesSwith(8)
            $('#tableSecciones').DataTable().ajax.reload()
        })
        $('#checkAllSecc').on('click', function () {
            $("#tableSecciones input:checkbox").prop('checked', true)
            $('#tableSecciones tr').addClass('table-active')
        });
        $('#nocheckAllSecc').on('click', function () {
            $("#tableSecciones input:checkbox").prop('checked', false)
            $('#tableSecciones tr').removeClass('table-active')
        });
        

        // $('#aplicarSecci').on('click', function () {
        //     CheckSesion()
        //     let selected = new Array();
        //     $("#tableSecciones input:checkbox:checked").each(function () {
        //         (selected.push(parseInt($(this).val())));
        //     });
        //     $.ajax({
        //         url: "listas_estruct/setLista.php",
        //         type: "POST",
        //         data: {
        //             lista2 : 5,
        //             check  : JSON.stringify(selected),
        //             uid    : uid,
        //             _c     : cliente,
        //         },
        //         beforeSend: function (data) {
        //             $.notifyClose();
        //             notify('Aguarde..', 'info', 0, 'right')
        //         },
        //         success: function (data) {
        //             if (data.status == "ok") {
        //                 $.notifyClose();
        //                 notify(data.Mensaje, 'success', 5000, 'right')
        //                 $('#tableSecciones').DataTable().ajax.reload()
        //             } else {
        //                 $.notifyClose();
        //                 notify(data.Mensaje, 'danger', 5000, 'right')
        //                 $('#tableSecciones').DataTable().ajax.reload()
        //             }
        //         },
        //         error: function (data) {
        //             $.notifyClose();
        //             notify('Error', 'danger', 5000, 'right')
        //         }
        //     })
        // });
    })
    tableSecciones.on('draw.dt', function () {
        $('#spanFinishTable').html('Secciones')
        finishCallBack = finishCallBack + 1
        habilitarRelacionesSwith(finishCallBack)
    })
});