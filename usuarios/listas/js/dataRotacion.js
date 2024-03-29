$(function () {
    'use strict'

    let cliente_rol = $('#cliente_rol').val()
    let recid_rol = $('#recid_rol').val()
    let id_rol = $('#id_rol').val()

    let tableRotaciones = $('#tableRotaciones').dataTable({
        initComplete: function (settings, json) {
            if(json.data.length===0){
                $("#tableRotaciones").parents('.table-responsive').hide();
                $('#rotaciones').html('<div class="my-3 fontq">No se encontraron resultados</div>')
            }
        },
        dom: "<'row'<'col-12 divRotFilter d-flex align-items-center justify-content-between pt-1'f>>" +
            "<'row'<'col-12 divtablerot 't>>" +
            "<'row'<'col-12 d-flex align-items-center justify-content-end'>" +
            "<'col-12 d-flex align-items-center justify-content-between divRotaInfo pt-2 pl-0 pr-0'i><'col-12 pt-2 pl-0 divRefreshRotacion'>>",
        "ajax": {
            url: "../listas/getListas.php",
            type: "GET",
            dataType: "json",
            "data": function (data) {
                data._c = cliente_rol,
                    data.lista = 4,
                    data.id_rol = id_rol
            },
            error: function () {
                $("#tableRotaciones").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex,) {
            $(row).addClass('animate__animated animate__fadeIn pointer checkRotacion');
        },
        // rowGroup: {
        //     className: '',
        //     dataSrc: function (row) {
        //         return `<div class="pointer btn btn-link m-0 p-0 fontq groupHorarioTipo" data-tipo="` + row.idtipo + `">` + row.tipo + `</div>`
        //     }
        // },
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
                            $('#Rotacion_' + row['codigo']).parents('tr').addClass('table-active')
                        }, 200);
                    }
                    let classTipo = 'RotacionClass_' + row.idtipo
                    let datacol = `
                    <div class="custom-control custom-checkbox">
                        <input `+ checked + ` type="checkbox" class="custom-control-input ` + classTipo + `" id="Rotacion_` + row['codigo'] + `" value="` + row['codigo'] + `">
                        <label class="custom-control-label" for="Rotacion_`+ row['codigo'] + `"></label>
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
            "sInfo": "_START_ al _END_ de _TOTAL_ Rotaciones",
            "sInfoEmpty": "No se encontraron resultados",
            "sInfoFiltered": "<br>(Filtrado de un total de _MAX_ Rotaciones)",
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
    tableRotaciones.on('init.dt', function () {

        $('#tableRotaciones_info').css('margin-top', '0px')
        $('#tableRotaciones_info').addClass('p-0')

        $('#tableRotaciones tbody').on('click', '.checkRotacion', function (e) {
            e.preventDefault();
            let data = $(tableRotaciones).DataTable().row($(this)).data();
            if ($('#Rotacion_' + data.codigo).is(":checked")) {
                $('#Rotacion_' + data.codigo).prop('checked', false)
                $(this).removeClass('table-active')
            } else {
                $('#Rotacion_' + data.codigo).prop('checked', true)
                $(this).addClass('table-active')
            };
        });
        // $('#tableRotaciones tbody').on('click', '.groupHorarioTipo', function (e) {
        //     e.preventDefault();
        //     let tipo = $(this).attr('data-tipo')
        //     if ($('.RotacionClass_' + tipo).is(":checked")) {
        //         $('.RotacionClass_' + tipo).prop('checked', false)
        //         $('.RotacionClass_' + tipo).parents('tr').removeClass('table-active')
        //     } else {
        //         $('.RotacionClass_' + tipo).prop('checked', true)
        //         $('.RotacionClass_' + tipo).parents('tr').addClass('table-active')
        //     };
        // })

        $("#tableRotaciones_filter .form-control").attr('placeholder', 'Buscar Rotación')
        $(this).children('thead').remove()
        $('.divtablerot').css('max-height', '300px')
        $('.divtablerot').addClass('overflow-auto')

        let buttonsRefresh = `
            <div class="">
                <button class="btn btn-link btn-sm fontq p-0 m-0" id="refreshRotacionList">Actualizar Grilla</button>
            </div>
            `
        let buttonsCheck = `
            <div class="">
                <button class="btn btn-link btn-sm fontq" id="checkAllRotacion">Marcar</button>
                <button class="ml-1 btn btn-link btn-sm fontq" id="nocheckAllRotacion">Desmarcar</button>
            </div>
            `
        let buttonsSubmit = `
            <div class="">
                <button type="button" class="btn btn-custom btn-sm btn-mobile fontq w100" id="aplicarRotacion">Aplicar</button>
            </div>
            `
        $(this).parents().find('.divRotFilter').prepend(buttonsCheck)
        $(this).parents().find('.divRotaInfo').append(buttonsSubmit)
        $(this).parents().find('.divRefreshRotacion').append(buttonsRefresh)

        $('#refreshRotacionList').on('click', function (e) {
            e.preventDefault();
            $('#tableRotaciones').DataTable().ajax.reload()
        })
        $('#checkAllRotacion').on('click', function () {
            $("#tableRotaciones input:checkbox").prop('checked', true)
            $('#tableRotaciones tr').addClass('table-active')
        });
        $('#nocheckAllRotacion').on('click', function () {
            $("#tableRotaciones input:checkbox").prop('checked', false)
            $('#tableRotaciones tr').removeClass('table-active')
        });

        $('#aplicarRotacion').on('click', function () {
            $('#tableRotaciones').DataTable().search('').draw();
            CheckSesion()
            let selected = new Array();
            $("#tableRotaciones input:checkbox:checked").each(function () {
                (selected.push(parseInt($(this).val())));
            });
            $.ajax({
                url: "../listas/setLista.php",
                type: "POST",
                data: {
                    lista: 4,
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
                        $('#tableRotaciones').DataTable().ajax.reload()
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                        $('#tableRotaciones').DataTable().ajax.reload()
                    }
                },
                error: function (data) {
                    $.notifyClose();
                    notify('Error', 'danger', 5000, 'right')
                }
            })
        });
        $('#rotaciones').removeClass('invisible')
    })
});