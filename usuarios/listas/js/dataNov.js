$(function () {
    'use strict'

    let cliente_rol = $('#cliente_rol').val()
    let recid_rol = $('#recid_rol').val()
    let id_rol = $('#id_rol').val()

    let tableNovedades = $('#tableNovedades').dataTable({
        initComplete: function (settings, json) {
            if (json.data.length === 0) {
                $("#tableNovedades").parents('.table-responsive').hide();
                $('#novedades').html('<div class="my-3 fontq">No se encontraron resultados</div>')
            }
        },
        dom: "<'row'<'col-12 divFilter d-flex align-items-center justify-content-between pt-1'f>>" +
            "<'row'<'col-12 divtablenov 't>>" +
            "<'row'<'col-12 d-flex align-items-center justify-content-end'>" +
            "<'col-12 d-flex align-items-center justify-content-between divInfo pt-2 pl-0 pr-0'i><'col-12 pt-2 pl-0 divRefresh'>>",
        "ajax": {
            url: "../listas/getListas.php",
            type: "GET",
            dataType: "json",
            "data": function (data) {
                data._c = cliente_rol,
                    data.lista = 1,
                    data.id_rol = id_rol
            },
            error: function () {
                $("#tableNovedades").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex,) {
            $(row).addClass('animate__animated animate__fadeIn pointer checkNove');
        },
        rowGroup: {
            className: '',
            dataSrc: function (row) {
                return `<div class="pointer btn btn-link m-0 p-0 fontq groupTipo" data-tipo="` + row.idtipo + `">` + row.tipo + `</div>`
            }
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
                className: 'align-middle ', targets: 'id', title: '',
                "render": function (data, type, row, meta) {
                    let datacol = '<span class="text-secondary">' + row['id'] + '</span>'
                    return datacol;
                },
            },
            {
                className: 'align-middle', targets: '', title: '',
                "render": function (data, type, row, meta) {
                    let checked = (row.set === 1) ? 'checked' : ''
                    if (checked) {
                        setTimeout(() => {
                            $('#Nove_' + row['codigo']).parents('tr').addClass('table-active')
                        }, 200);
                    }
                    let classTipo = 'class_' + row.idtipo
                    let datacol = `
                    <div class="custom-control custom-checkbox">
                        <input `+ checked + ` type="checkbox" class="custom-control-input ` + classTipo + `" id="Nove_` + row['codigo'] + `" value="` + row['codigo'] + `">
                        <label class="custom-control-label" for="Nove_`+ row['codigo'] + `"></label>
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
            "sInfo": "_START_ al _END_ de _TOTAL_ Novedades",
            "sInfoEmpty": "No se encontraron resultados",
            "sInfoFiltered": "<br>(Filtrado de un total de _MAX_ Novedades)",
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
    tableNovedades.on('init.dt', function () {

        $('#tableNovedades_info').css('margin-top', '0px')
        $('#tableNovedades_info').addClass('p-0')

        $('#tableNovedades tbody').on('click', '.checkNove', function (e) {
            e.preventDefault();
            let data = $(tableNovedades).DataTable().row($(this)).data();
            if ($('#Nove_' + data.codigo).is(":checked")) {
                $('#Nove_' + data.codigo).prop('checked', false)
                $(this).removeClass('table-active')
            } else {
                $('#Nove_' + data.codigo).prop('checked', true)
                $(this).addClass('table-active')
            };
        });
        $('#tableNovedades tbody').on('click', '.groupTipo', function (e) {
            e.preventDefault();
            let tipo = $(this).attr('data-tipo')
            // console.log(tipo);
            // $('.class_' + tipo).prop('checked', true)
            // $('.class_' + tipo).parents('tr').addClass('table-active')
            if ($('.class_' + tipo).is(":checked")) {
                $('.class_' + tipo).prop('checked', false)
                $('.class_' + tipo).parents('tr').removeClass('table-active')
            } else {
                $('.class_' + tipo).prop('checked', true)
                $('.class_' + tipo).parents('tr').addClass('table-active')
            };

        })

        $("#tableNovedades_filter .form-control").attr('placeholder', 'Buscar Novedad')
        $(this).children('thead').remove()
        $('.divtablenov').css('max-height', '300px')
        $('.divtablenov').addClass('overflow-auto')

        let buttonsRefresh = `
            <div class="">
                <button class="btn btn-link btn-sm fontq p-0 m-0" id="refreshList">Actualizar Grilla</button>
            </div>
            `
        let buttonsCheck = `
            <div class="">
                <button class="btn btn-link btn-sm fontq" id="checkAllNov">Marcar</button>
                <button class="ml-1 btn btn-link btn-sm fontq" id="nocheckAllNov">Desmarcar</button>
            </div>
            `
        let buttonsSubmit = `
            <div class="">
                <button type="button" class="btn btn-custom btn-sm btn-mobile fontq w100" id="aplicarNove">Aplicar</button>
            </div>
            `
        $(this).parents().find('.divFilter').prepend(buttonsCheck)
        $(this).parents().find('.divInfo').append(buttonsSubmit)
        $(this).parents().find('.divRefresh').append(buttonsRefresh)

        $('#refreshList').on('click', function (e) {
            e.preventDefault();
            $('#tableNovedades').DataTable().ajax.reload()
        })
        $('#checkAllNov').on('click', function () {
            $("#tableNovedades input:checkbox").prop('checked', true)
            $('#tableNovedades tr').addClass('table-active')
        });
        $('#nocheckAllNov').on('click', function () {
            $("#tableNovedades input:checkbox").prop('checked', false)
            $('#tableNovedades tr').removeClass('table-active')
        });

        $('#aplicarNove').on('click', function () {
            CheckSesion()
            let selected = new Array();
            $("#tableNovedades input:checkbox:checked").each(function () {
                (selected.push(parseInt($(this).val())));
            });
            $.ajax({
                url: "../listas/setLista.php",
                type: "POST",
                data: {
                    lista: 1,
                    check: JSON.stringify(selected),
                    id_rol: id_rol,
                    recid_rol: recid_rol
                },
                beforeSend: function (data) {
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        $.notifyClose();
                        notify(data.Mensaje, 'success', 5000, 'right')
                        $('#tableNovedades').DataTable().ajax.reload()
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                        $('#tableNovedades').DataTable().ajax.reload()
                    }
                },
                error: function (data) {
                    $.notifyClose();
                    notify('Error', 'danger', 5000, 'right')
                }
            })
        });
    })
});