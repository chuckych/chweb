$(function () {
    'use strict'

    let cliente = $('#cliente').val()
    let uid = $('#uid').val()

    let tableSectores = $('#tableSectores').dataTable({
        initComplete: function (settings, json) {
            if(json.data.length===0){
                $("#tableSectores").parents('.table-responsive').hide();
                $('#sector').html('<div class="my-3 fontq">No se encontraron resultados</div>')
            }
        },
        drawCallback: function (settings) {
            // setTimeout(() => {
            //     // SetSector2(uid, cliente)
            //     setGeneral(uid, cliente, 'tableSectores', 4)
            //     setGeneral(uid, cliente, 'tableSecciones', 5)
            //     // SetSeccion2(uid, cliente)
            // }, 500);
        },
        dom: "<'row'<'col-12 divSectorFilter d-flex align-items-center justify-content-between pt-1'f>>" +
            "<'row'<'col-12 divTableSect 't>>" +
            "<'row'<'col-12 d-flex align-items-center justify-content-end'>" +
            "<'col-12 d-flex align-items-center justify-content-between divSectorInfo pt-2 pl-0 pr-0'i><'col-12 pt-2 pl-0 divRefreshSector'>>",
        "ajax": {
            url: "listas_estruct/getListas.php",
            type: "GET",
            dataType: "json",
            "data": function (data) {
                data._c = cliente,
                data.lista   = 4,
                data.uid     = uid,
                data.rel = relacionesSwith
            },
            error: function () {
                $("#tableSectores").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex,) {
            $(row).addClass('animate__animated animate__fadeIn pointer checkSector');
        },
        columns: [
            {
                className: 'align-middle', targets: 'codigo', title: '',
                "render": function (data, type, row, meta) {
                    let datacol = row['codigo']
                    return datacol;
                },
            },
            {
                className: 'align-middle w-100 ', targets: 'descripcion', title: '',
                "render": function (data, type, row, meta) {
                    let datacol = row['descripcion']
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
                    if (checked) {
                        setTimeout(() => {
                            $('#Sector_' + row['codigo']).parents('tr').addClass('table-active')
                        }, 200);
                    }
                    let classTipo = 'SectClass_' + row.idtipo
                    let cod = (row['codigo']===0) ? 32768 : row['codigo']
                    let datacol = `
                    <div class="custom-control custom-checkbox">
                        <input `+ checked + ` type="checkbox" class="checkSect custom-control-input ` + classTipo + `" id="Sector_` + row['codigo'] + `" value="` + cod + `">
                        <label class="custom-control-label" for="Sector_`+ row['codigo'] + `"></label>
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
            "sInfo": "_START_ al _END_ de _TOTAL_ Sectores",
            "sInfoEmpty": "No se encontraron resultados",
            "sInfoFiltered": "<br>(Filtrado de un total de _MAX_ Sectores)",
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
    tableSectores.on('init.dt', function () {

        $('#tableSectores_info').css('margin-top', '0px')
        $('#tableSectores_info').addClass('p-0')

        $('#tableSectores tbody').on('click', '.checkSector', function (e) {
            e.preventDefault();
            let data = $(tableSectores).DataTable().row($(this)).data();
            if ($('#Sector_' + data.codigo).is(":checked")) {
                $('#Sector_' + data.codigo).prop('checked', false)
                $(this).removeClass('table-active')
                $('#tableSecciones .class_Sec_'+ data.codigo).prop('checked', false)
                
            } else {
                $('#Sector_' + data.codigo).prop('checked', true)
                $(this).addClass('table-active')
            };
        });

        $("#tableSectores_filter .form-control").attr('placeholder', 'Buscar Sector')
        $(this).children('thead').remove()
        $('.divTableSect').css('max-height', '300px')
        $('.divTableSect').addClass('overflow-auto')

        let buttonsRefresh = `
            <div class="">
                <button class="btn btn-link btn-sm fontq p-0 m-0" id="refreshSectList">Actualizar Grilla</button>
            </div>
            `
        let buttonsCheck = `
            <div class="">
                <button class="btn btn-link btn-sm fontq" id="checkAllSect">Marcar</button>
                <button class="ml-1 btn btn-link btn-sm fontq" id="nocheckAllSect">Desmarcar</button>
            </div>
            `
        let buttonsSubmit = `
            <div class="">
                <button type="button" class="btn btn-custom btn-sm btn-mobile fontq w100" id="aplicarSect">Aplicar</button>
            </div>
            `
        $(this).parents().find('.divSectorFilter').prepend(buttonsCheck)
        // $(this).parents().find('.divSectorInfo').append(buttonsSubmit)
        $('#sector').append(`<div class="row"><div class="col-12 my-3"><div class="float-right">`+buttonsSubmit+`</div></div></div>`)
        $(this).parents().find('.divRefreshSector').append(buttonsRefresh)

        $('#refreshSectList').on('click', function (e) {
            e.preventDefault();
            habilitarRelacionesSwith(8)
            $('#tableSectores').DataTable().ajax.reload()
            $('#tableSecciones').DataTable().ajax.reload()
        })
        $('#checkAllSect').on('click', function () {
            $("#tableSectores input:checkbox").prop('checked', true)
            $('#tableSectores tr').addClass('table-active')
        });
        $('#nocheckAllSect').on('click', function () {
            $("#tableSectores input:checkbox").prop('checked', false)
            $('#tableSectores tr').removeClass('table-active')
        });

        $('#aplicarSect').on('click', function () {
            CheckSesion()
            let checked = new Array();
            let checked2 = new Array();
            $("#tableSectores input:checkbox").each(function () {
                if($(this).is(':checked')){
                    (checked.push(parseInt($(this).val()))); /** Array de checkbox checked*/
                }else{
                    (checked2.push(parseInt($(this).val()))); /** Array de checkbox not checked*/
                }
            });
            
            $.ajax({
                url: "listas_estruct/setLista.php",
                type: "POST",
                data: {
                    lista  : 4,
                    check  : JSON.stringify(checked),
                    check2 : JSON.stringify(checked2),
                    uid    : uid,
                    _c     : cliente
                },
                beforeSend: function (data) {
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                    // $('.loader').show()
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        if (setSeccion(uid, cliente)===true) {
                            notify(data.Mensaje, 'success', 5000, 'right')
                            actualizaListas()
                            actualizaSet()
                        }
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                    }
                },
                error: function (data) {
                    $.notifyClose();
                    notify('Error', 'danger', 5000, 'right')
                }
            })
        });        
    })
    tableSectores.on('draw.dt', function () {
        $('#spanFinishTable').html('Sectores')
        finishCallBack = finishCallBack + 1
        habilitarRelacionesSwith(finishCallBack)
    })
});