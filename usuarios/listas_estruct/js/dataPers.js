$(function () {
    'use strict'

    let cliente = $('#cliente').val()
    let uid = $('#uid').val()

    let tablePersonal = $('#tablePersonal').dataTable({
        initComplete: function (settings, json) {
            if(json.data.length===0){
                $("#tablePersonal").parents('.table-responsive').hide();
                $('#personal').html('<div class="my-3 fontq">No se encontraron resultados</div>')
            }
        },
        dom: "<'row'<'col-12 divPersonalFilter d-flex align-items-center justify-content-between pt-1'f>>" +
            "<'row'<'col-12 divTablePer 't>>" +
            "<'row'<'col-12 d-flex align-items-center justify-content-end'>" +
            "<'col-12 d-flex align-items-center justify-content-between divPersonalInfo pt-2 pl-0 pr-0'i><'col-12 pt-2 pl-0 divRefreshPersonal'>>",
        "ajax": {
            url: "listas_estruct/getListas.php",
            type: "GET",
            dataType: "json",
            "data": function (data) {
                data._c = cliente,
                data.lista   = 8,
                data.uid     = uid,
                data.rel = relacionesSwith
            },
            error: function () {
                $("#tablePersonal").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex,) {
            $(row).addClass('animate__animated animate__fadeIn pointer checkPersonal');
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
            // {
            //     className: 'align-middle ', targets: 'estado', title: '',
            //     "render": function (data, type, row, meta) {
            //         let estado = (row['estado'] === 1) ? 'Activo' : 'De Baja'
            //         let estadoColor = (row['estado'] === 1) ? 'text-success' : 'text-danger'
            //         let datacol = '<span class="'+estadoColor+'">'+estado+'</span>'
            //         return datacol;
            //     },
            // },
            {
                className: 'align-middle', targets: '', title: '',
                "render": function (data, type, row, meta) {
                    let checked = (row.set === 1) ? 'checked' : ''
                    if (checked) {
                        setTimeout(() => {
                            $('#Personal_' + row['codigo']).parents('tr').addClass('table-active')
                        }, 200);
                    }
                    let classTipo = 'PerClass_' + row.idtipo
                    let datacol = `
                    <div class="custom-control custom-checkbox">
                        <input `+ checked + ` type="checkbox" class="custom-control-input ` + classTipo + `" id="Personal_` + row['codigo'] + `" value="` + row['codigo'] + `">
                        <label class="custom-control-label" for="Personal_`+ row['codigo'] + `"></label>
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
            "sInfo": "_START_ al _END_ de _TOTAL_ Legajos",
            "sInfoEmpty": "No se encontraron resultados",
            "sInfoFiltered": "<br>(Filtrado de un total de _MAX_ Legajos)",
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
    tablePersonal.on('init.dt', function () {

        $('#tablePersonal_info').css('margin-top', '0px')
        $('#tablePersonal_info').addClass('p-0')

        $('#tablePersonal tbody').on('click', '.checkPersonal', function (e) {
            e.preventDefault();
            let data = $(tablePersonal).DataTable().row($(this)).data();
            if ($('#Personal_' + data.codigo).is(":checked")) {
                $('#Personal_' + data.codigo).prop('checked', false)
                $(this).removeClass('table-active')
            } else {
                $('#Personal_' + data.codigo).prop('checked', true)
                $(this).addClass('table-active')
            };
        });

        $("#tablePersonal_filter .form-control").attr('placeholder', 'Buscar Personal')
        $(this).children('thead').remove()
        $('.divTablePer').css('max-height', '300px')
        $('.divTablePer').addClass('overflow-auto')

        let buttonsRefresh = `
            <div class="">
                <button class="btn btn-link btn-sm fontq p-0 m-0" id="refreshPerList">Actualizar Grilla</button>
            </div>
            `
        let buttonsCheck = `
            <div class="">
                <button class="btn btn-link btn-sm fontq" id="checkAllPer">Marcar</button>
                <button class="ml-1 btn btn-link btn-sm fontq" id="nocheckAllPer">Desmarcar</button>
            </div>
            `
        let buttonsSubmit = `
            <div class="">
                <button type="button" class="btn btn-custom btn-sm btn-mobile fontq w100" id="aplicarPer">Aplicar</button>
            </div>
            `
        $(this).parents().find('.divPersonalFilter').prepend(buttonsCheck)
        $(this).parents().find('.divPersonalInfo').append(buttonsSubmit)
        $(this).parents().find('.divRefreshPersonal').append(buttonsRefresh)

        $('#refreshPerList').on('click', function (e) {
            e.preventDefault();
            habilitarRelacionesSwith(8)
            $('#tablePersonal').DataTable().ajax.reload()
        })
        $('#checkAllPer').on('click', function () {
            $("#tablePersonal input:checkbox").prop('checked', true)
            $('#tablePersonal tr').addClass('table-active')
        });
        $('#nocheckAllPer').on('click', function () {
            $("#tablePersonal input:checkbox").prop('checked', false)
            $('#tablePersonal tr').removeClass('table-active')
        });

        $('#aplicarPer').on('click', function () {
            CheckSesion()
            let selected = new Array();
            $("#tablePersonal input:checkbox:checked").each(function () {
                (selected.push(parseInt($(this).val())));
            });
            $.ajax({
                url: "listas_estruct/setLista.php",
                type: "POST",
                data: {
                    lista   : 8,
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
                        notify(data.Mensaje, 'danger', 5000, 'right').
                        actualizaListas()
                        actualizaSet()
                    }
                },
                error: function (data) {
                    $.notifyClose();
                    notify('Error', 'danger', 5000, 'right')
                }
            })
        });
    })
    tablePersonal.on('draw.dt', function () {
        $('#spanFinishTable').html('Personal')
        finishCallBack = finishCallBack + 1
        habilitarRelacionesSwith(finishCallBack)
    })
});