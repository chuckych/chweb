$(function () {
    'use strict'

    let cliente = $('#cliente').val()
    let uid = $('#uid').val()

    let tableSucursales = $('#tableSucursales').dataTable({
        initComplete: function (settings, json) {
            if(json.data.length===0){
                $("#tableSucursales").parents('.table-responsive').hide();
                $('#sucursal').html('<div class="my-3 fontq">No se encontraron resultados</div>')
            }
        },
        dom: "<'row'<'col-12 divSucursalFilter d-flex align-items-center justify-content-between pt-1'f>>" +
            "<'row'<'col-12 divTableSuc 't>>" +
            "<'row'<'col-12 d-flex align-items-center justify-content-end'>" +
            "<'col-12 d-flex align-items-center justify-content-between divSucursalInfo pt-2 pl-0 pr-0'i><'col-12 pt-2 pl-0 divRefreshSucursal'>>",
        "ajax": {
            url: "listas_estruct/getListas.php",
            type: "GET",
            dataType: "json",
            "data": function (data) {
                data._c = cliente,
                data.lista   = 7,
                data.uid     = uid,
                data.rel = relacionesSwith
            },
            error: function () {
                $("#tableSucursales").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex,) {
            $(row).addClass('animate__animated animate__fadeIn pointer checkSucursal');
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
                            $('#Sucursal_' + row['codigo']).parents('tr').addClass('table-active')
                        }, 200);
                    }
                    let classTipo = 'SucClass_' + row.idtipo
                    let cod = (row['codigo']===0) ? 32768 : row['codigo']
                    let datacol = `
                    <div class="custom-control custom-checkbox">
                        <input `+ checked + ` type="checkbox" class="custom-control-input ` + classTipo + `" id="Sucursal_` + row['codigo'] + `" value="` + cod + `">
                        <label class="custom-control-label" for="Sucursal_`+ row['codigo'] + `"></label>
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
            "sInfo": "_START_ al _END_ de _TOTAL_ Sucursales",
            "sInfoEmpty": "No se encontraron resultados",
            "sInfoFiltered": "<br>(Filtrado de un total de _MAX_ Sucursales)",
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
    tableSucursales.on('init.dt', function () {

        $('#tableSucursales_info').css('margin-top', '0px')
        $('#tableSucursales_info').addClass('p-0')

        $('#tableSucursales tbody').on('click', '.checkSucursal', function (e) {
            e.preventDefault();
            let data = $(tableSucursales).DataTable().row($(this)).data();
            if ($('#Sucursal_' + data.codigo).is(":checked")) {
                $('#Sucursal_' + data.codigo).prop('checked', false)
                $(this).removeClass('table-active')
            } else {
                $('#Sucursal_' + data.codigo).prop('checked', true)
                $(this).addClass('table-active')
            };
        });

        $("#tableSucursales_filter .form-control").attr('placeholder', 'Buscar Sucursal')
        $(this).children('thead').remove()
        $('.divTableSuc').css('max-height', '300px')
        $('.divTableSuc').addClass('overflow-auto')

        let buttonsRefresh = `
            <div class="">
                <button class="btn btn-link btn-sm fontq p-0 m-0" id="refreshSucList">Actualizar Grilla</button>
            </div>
            `
        let buttonsCheck = `
            <div class="">
                <button class="btn btn-link btn-sm fontq" id="checkAllSuc">Marcar</button>
                <button class="ml-1 btn btn-link btn-sm fontq" id="nocheckAllSuc">Desmarcar</button>
            </div>
            `
        let buttonsSubmit = `
            <div class="">
                <button type="button" class="btn btn-custom btn-sm btn-mobile fontq w100" id="aplicarSuc">Aplicar</button>
            </div>
            `
        $(this).parents().find('.divSucursalFilter').prepend(buttonsCheck)
        $(this).parents().find('.divSucursalInfo').append(buttonsSubmit)
        $(this).parents().find('.divRefreshSucursal').append(buttonsRefresh)

        $('#refreshSucList').on('click', function (e) {
            e.preventDefault();
            habilitarRelacionesSwith(8)
            $('#tableSucursales').DataTable().ajax.reload()
        })
        $('#checkAllSuc').on('click', function () {
            $("#tableSucursales input:checkbox").prop('checked', true)
            $('#tableSucursales tr').addClass('table-active')
        });
        $('#nocheckAllSuc').on('click', function () {
            $("#tableSucursales input:checkbox").prop('checked', false)
            $('#tableSucursales tr').removeClass('table-active')
        });

        $('#aplicarSuc').on('click', function () {
            $('#tableSucursales').DataTable().search('').draw();
            CheckSesion()
            let selected = new Array();
            $("#tableSucursales input:checkbox:checked").each(function () {
                (selected.push(parseInt($(this).val())));
            });
            $.ajax({
                url: "listas_estruct/setLista.php",
                type: "POST",
                data: {
                    lista   : 7,
                    check   : JSON.stringify(selected),
                    uid     : uid,
                    _c    : cliente
                },
                beforeSend: function (data) {
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        notify(data.Mensaje, 'success', 5000, 'right')
                        actualizaListas()
                        actualizaSet()
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                        actualizaListas()
                    }
                },
                error: function (data) {
                    $.notifyClose();
                    notify('Error', 'danger', 5000, 'right')
                }
            })
        });
        $('#sucursal').removeClass('invisible')
    })
    tableSucursales.on('draw.dt', function () {
        $('#spanFinishTable').html('Sucursales')
        finishCallBack = finishCallBack + 1
        habilitarRelacionesSwith(finishCallBack)
    })
});