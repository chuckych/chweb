$(function () {
    'use strict'

    let cliente_rol = $('#cliente_rol').val()
    let recid_rol = $('#recid_rol').val()
    let id_rol = $('#id_rol').val()

    let tableONovedades = $('#tableONovedades').dataTable({
        initComplete: function (settings, json) {
            if(json.data.length===0){
                $("#tableONovedades").parents('.table-responsive').hide();
                $('#o_novedades').html('<div class="my-3 fontq">No se encontraron resultados</div>')
            }
        },

        dom: "<'row'<'col-12 divONovFilter d-flex align-items-center justify-content-between pt-1'f>>" +
            "<'row'<'col-12 divtableonov 't>>" +
            "<'row'<'col-12 d-flex align-items-center justify-content-end'>" +
            "<'col-12 d-flex align-items-center justify-content-between divONovInfo pt-2 pl-0 pr-0'i><'col-12 pt-2 pl-0 divRefreshONov'>>",
        "ajax": {
            url: "../listas/getListas.php",
            type: "GET",
            dataType: "json",
            "data": function (data) {
                data._c = cliente_rol,
                    data.lista = 2,
                    data.id_rol = id_rol
            },
            error: function () {
                $("#tableONovedades").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex,) {
            $(row).addClass('animate__animated animate__fadeIn pointer checkONove');
        },
        rowGroup: {
            className: '',
            dataSrc: function (row) {
                return `<div class="pointer btn btn-link m-0 p-0 fontq groupONovTipo" data-tipo="` + row.idtipo + `">` + row.tipo + `</div>`
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
                className: 'align-middle', targets: '', title: '',
                "render": function (data, type, row, meta) {
                    let checked = (row.set === 1) ? 'checked' : ''
                    if (checked) {
                        setTimeout(() => {
                            $('#ONove_' + row['codigo']).parents('tr').addClass('table-active')
                        }, 200);
                    }
                    let classTipo = 'oclass_' + row.idtipo
                    let datacol = `
                    <div class="custom-control custom-checkbox">
                        <input `+ checked + ` type="checkbox" class="custom-control-input ` + classTipo + `" id="ONove_` + row['codigo'] + `" value="` + row['codigo'] + `">
                        <label class="custom-control-label" for="ONove_`+ row['codigo'] + `"></label>
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
    tableONovedades.on('init.dt', function () {

        $('#tableONovedades_info').css('margin-top', '0px')
        $('#tableONovedades_info').addClass('p-0')

        $('#tableONovedades tbody').on('click', '.checkONove', function (e) {
            e.preventDefault();
            let data = $(tableONovedades).DataTable().row($(this)).data();
            if ($('#ONove_' + data.codigo).is(":checked")) {
                $('#ONove_' + data.codigo).prop('checked', false)
                $(this).removeClass('table-active')
            } else {
                $('#ONove_' + data.codigo).prop('checked', true)
                $(this).addClass('table-active')
            };
        });
        $('#tableONovedades tbody').on('click', '.groupONovTipo', function (e) {
            e.preventDefault();
            let tipo = $(this).attr('data-tipo')
            if ($('.oclass_' + tipo).is(":checked")) {
                $('.oclass_' + tipo).prop('checked', false)
                $('.oclass_' + tipo).parents('tr').removeClass('table-active')
            } else {
                $('.oclass_' + tipo).prop('checked', true)
                $('.oclass_' + tipo).parents('tr').addClass('table-active')
            };
        })

        $("#tableONovedades_filter .form-control").attr('placeholder', 'Buscar Novedad')
        $(this).children('thead').remove()
        $('.divtableonov').css('max-height', '300px')
        $('.divtableonov').addClass('overflow-auto')

        let buttonsRefresh = `
            <div class="">
                <button class="btn btn-link btn-sm fontq p-0 m-0" id="refreshONovList">Actualizar Grilla</button>
            </div>
            `
        let buttonsCheck = `
            <div class="">
                <button class="btn btn-link btn-sm fontq" id="checkAllONov">Marcar Todo</button>
                <button class="ml-1 btn btn-link btn-sm fontq" id="nocheckAllONov">Desmarcar Todo</button>
            </div>
            `
        let buttonsSubmit = `
            <div class="">
                <button type="button" class="btn btn-custom btn-sm btn-mobile fontq w100" id="aplicarONove">Aplicar</button>
            </div>
            `
        $(this).parents().find('.divONovFilter').prepend(buttonsCheck)
        $(this).parents().find('.divONovInfo').append(buttonsSubmit)
        $(this).parents().find('.divRefreshONov').append(buttonsRefresh)

        $('#refreshONovList').on('click', function (e) {
            e.preventDefault();
            $('#tableONovedades').DataTable().ajax.reload()
        })
        $('#checkAllONov').on('click', function () {
            $("#tableONovedades input:checkbox").prop('checked', true)
            $('#tableONovedades tr').addClass('table-active')
        });
        $('#nocheckAllONov').on('click', function () {
            $("#tableONovedades input:checkbox").prop('checked', false)
            $('#tableONovedades tr').removeClass('table-active')
        });

        $('#aplicarONove').on('click', function () {
            CheckSesion()
            let selected = new Array();
            $("#tableONovedades input:checkbox:checked").each(function () {
                (selected.push(parseInt($(this).val())));
            });
            $.ajax({
                url: "../listas/setLista.php",
                type: "POST",
                data: {
                    lista: 2,
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
                        $('#tableONovedades').DataTable().ajax.reload()
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                        $('#tableONovedades').DataTable().ajax.reload()
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