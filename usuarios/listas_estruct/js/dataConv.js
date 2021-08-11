$(function () {
    'use strict'

    let cliente = $('#cliente').val()
    let uid = $('#uid').val()

    let tableConvenios = $('#tableConvenios').dataTable({
        initComplete: function (settings, json) {
            if(json.data.length===0){
                $("#tableConvenios").parents('.table-responsive').hide();
                $('#convenio').html('<div class="my-3 fontq">No se encontraron resultados</div>')
            }
        },
        dom: "<'row'<'col-12 divConvenioFilter d-flex align-items-center justify-content-between pt-1'f>>" +
            "<'row'<'col-12 divTableConv 't>>" +
            "<'row'<'col-12 d-flex align-items-center justify-content-end'>" +
            "<'col-12 d-flex align-items-center justify-content-between divConvenioInfo pt-2 pl-0 pr-0'i><'col-12 pt-2 pl-0 divRefreshConvenio'>>",
        "ajax": {
            url: "listas_estruct/getListas.php",
            type: "GET",
            dataType: "json",
            "data": function (data) {
                data._c = cliente,
                data.lista   = 3,
                data.uid     = uid,
                data.rel = relacionesSwith
            },
            error: function () {
                $("#tableConvenios").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex,) {
            $(row).addClass('animate__animated animate__fadeIn pointer checkConvenio');
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
                            $('#Convenio_' + row['codigo']).parents('tr').addClass('table-active')
                        }, 200);
                    }
                    let classTipo = 'ConvClass_' + row.idtipo
                    let cod = (row['codigo']===0) ? 32768 : row['codigo']
                    let datacol = `
                    <div class="custom-control custom-checkbox">
                        <input `+ checked + ` type="checkbox" class="custom-control-input ` + classTipo + `" id="Convenio_` + row['codigo'] + `" value="` + cod + `">
                        <label class="custom-control-label" for="Convenio_`+ row['codigo'] + `"></label>
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
            "sInfo": "_START_ al _END_ de _TOTAL_ Convenios",
            "sInfoEmpty": "No se encontraron resultados",
            "sInfoFiltered": "<br>(Filtrado de un total de _MAX_ Convenios)",
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
    tableConvenios.on('init.dt', function () {

        $('#tableConvenios_info').css('margin-top', '0px')
        $('#tableConvenios_info').addClass('p-0')

        $('#tableConvenios tbody').on('click', '.checkConvenio', function (e) {
            e.preventDefault();
            let data = $(tableConvenios).DataTable().row($(this)).data();
            if ($('#Convenio_' + data.codigo).is(":checked")) {
                $('#Convenio_' + data.codigo).prop('checked', false)
                $(this).removeClass('table-active')
            } else {
                $('#Convenio_' + data.codigo).prop('checked', true)
                $(this).addClass('table-active')
            };
        });

        $("#tableConvenios_filter .form-control").attr('placeholder', 'Buscar Convenio')
        $(this).children('thead').remove()
        $('.divTableConv').css('max-height', '300px')
        $('.divTableConv').addClass('overflow-auto')

        let buttonsRefresh = `
            <div class="">
                <button class="btn btn-link btn-sm fontq p-0 m-0" id="refreshConvList">Actualizar Grilla</button>
            </div>
            `
        let buttonsCheck = `
            <div class="">
                <button class="btn btn-link btn-sm fontq" id="checkAllConv">Marcar</button>
                <button class="ml-1 btn btn-link btn-sm fontq" id="nocheckAllConv">Desmarcar</button>
            </div>
            `
        let buttonsSubmit = `
            <div class="">
                <button type="button" class="btn btn-custom btn-sm btn-mobile fontq w100" id="aplicarConv">Aplicar</button>
            </div>
            `
        $(this).parents().find('.divConvenioFilter').prepend(buttonsCheck)
        $(this).parents().find('.divConvenioInfo').append(buttonsSubmit)
        $(this).parents().find('.divRefreshConvenio').append(buttonsRefresh)

        $('#refreshConvList').on('click', function (e) {
            e.preventDefault();
            habilitarRelacionesSwith(8)
            $('#tableConvenios').DataTable().ajax.reload()
        })
        $('#checkAllConv').on('click', function () {
            $("#tableConvenios input:checkbox").prop('checked', true)
            $('#tableConvenios tr').addClass('table-active')
        });
        $('#nocheckAllConv').on('click', function () {
            $("#tableConvenios input:checkbox").prop('checked', false)
            $('#tableConvenios tr').removeClass('table-active')
        });

        $('#aplicarConv').on('click', function () {
            CheckSesion()
            let selected = new Array();
            $("#tableConvenios input:checkbox:checked").each(function () {
                (selected.push(parseInt($(this).val())));
            });
            $.ajax({
                url: "listas_estruct/setLista.php",
                type: "POST",
                data: {
                    lista : 3,
                    check     : JSON.stringify(selected),
                    uid       : uid,
                    _c        : cliente
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
    })
    tableConvenios.on('draw.dt', function () {
        $('#spanFinishTable').html('Convenios')
        finishCallBack = finishCallBack + 1
        habilitarRelacionesSwith(finishCallBack)
    })
});