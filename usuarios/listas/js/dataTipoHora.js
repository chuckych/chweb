$(function () {
    'use strict'

    let cliente_rol = $('#cliente_rol').val()
    let recid_rol = $('#recid_rol').val()
    let id_rol = $('#id_rol').val()

    let tableTipoHoras = $('#tableTipoHoras').dataTable({
        initComplete: function (settings, json) {
            if(json.data.length===0){
                $("#tableTipoHoras").parents('.table-responsive').hide();
                $('#tipoHoras').html('<div class="my-3 fontq">No se encontraron resultados</div>')
            }
        },
        dom: "<'row'<'col-12 divTHorFilter d-flex align-items-center justify-content-between pt-1'f>>" +
            "<'row'<'col-12 divtablethor 't>>" +
            "<'row'<'col-12 d-flex align-items-center justify-content-end'>" +
            "<'col-12 d-flex align-items-center justify-content-between divTHorInfo pt-2 pl-0 pr-0'i><'col-12 pt-2 pl-0 divRefreshTHora'>>",
        "ajax": {
            url: "../listas/getListas.php",
            type: "GET",
            dataType: "json",
            "data": function (data) {
                data._c = cliente_rol,
                    data.lista = 5,
                    data.id_rol = id_rol
            },
            error: function () {
                $("#tableTipoHoras").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex,) {
            $(row).addClass('animate__animated animate__fadeIn pointer checkThora');
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
                className: 'align-middle', targets: 'id', title: '',
                "render": function (data, type, row, meta) {
                    let datacol = row['id']
                    return datacol;
                },
            },
            {
                className: 'align-middle', targets: '', title: '',
                "render": function (data, type, row, meta) {
                    let checked = (row.set === 1) ? 'checked' : ''
                    if (checked) {
                        setTimeout(() => {
                            $('#THora_' + row['codigo']).parents('tr').addClass('table-active')
                        }, 200);
                    }
                    let classTipo = 'THoraClass_' + row.idtipo
                    let datacol = `
                    <div class="custom-control custom-checkbox">
                        <input `+ checked + ` type="checkbox" class="custom-control-input ` + classTipo + `" id="THora_` + row['codigo'] + `" value="` + row['codigo'] + `">
                        <label class="custom-control-label" for="THora_`+ row['codigo'] + `"></label>
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
            "sInfo": "_START_ al _END_ de _TOTAL_ Tipos de Horas",
            "sInfoEmpty": "No se encontraron resultados",
            "sInfoFiltered": "<br>(Filtrado de un total de _MAX_ Tipos de Horas)",
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
    tableTipoHoras.on('init.dt', function () {
        $('#tableTipoHoras_info').css('margin-top', '0px')
        $('#tableTipoHoras_info').addClass('p-0')

        $('#tableTipoHoras tbody').on('click', '.checkThora', function (e) {
            e.preventDefault();
            let data = $(tableTipoHoras).DataTable().row($(this)).data();
            if ($('#THora_' + data.codigo).is(":checked")) {
                $('#THora_' + data.codigo).prop('checked', false)
                $(this).removeClass('table-active')
            } else {
                $('#THora_' + data.codigo).prop('checked', true)
                $(this).addClass('table-active')
            };
        });
        // $('#tableTipoHoras tbody').on('click', '.groupHorarioTipo', function (e) {
        //     e.preventDefault();
        //     let tipo = $(this).attr('data-tipo')
        //     if ($('.THoraClass_' + tipo).is(":checked")) {
        //         $('.THoraClass_' + tipo).prop('checked', false)
        //         $('.THoraClass_' + tipo).parents('tr').removeClass('table-active')
        //     } else {
        //         $('.THoraClass_' + tipo).prop('checked', true)
        //         $('.THoraClass_' + tipo).parents('tr').addClass('table-active')
        //     };
        // })

        $("#tableTipoHoras_filter .form-control").attr('placeholder', 'Buscar Tipo de Horas')
        $(this).children('thead').remove()
        $('.divtablethor').css('max-height', '300px')
        $('.divtablethor').addClass('overflow-auto')

        let buttonsRefresh = `
            <div class="">
                <button class="btn btn-link btn-sm fontq p-0 m-0" id="refreshThoraList">Actualizar Grilla</button>
            </div>
            `
        let buttonsCheck = `
            <div class="">
                <button class="btn btn-link btn-sm fontq" id="checkAllThora">Marcar</button>
                <button class="ml-1 btn btn-link btn-sm fontq" id="nocheckAllThora">Desmarcar</button>
            </div>
            `
        let buttonsSubmit = `
            <div class="">
                <button type="button" class="btn btn-custom btn-sm btn-mobile fontq w100" id="aplicarTHora">Aplicar</button>
            </div>
            `
        $(this).parents().find('.divTHorFilter').prepend(buttonsCheck)
        $(this).parents().find('.divTHorInfo').append(buttonsSubmit)
        $(this).parents().find('.divRefreshTHora').append(buttonsRefresh)

        $('#refreshThoraList').on('click', function (e) {
            e.preventDefault();
            $('#tableTipoHoras').DataTable().ajax.reload()
        })
        $('#checkAllThora').on('click', function () {
            $("#tableTipoHoras input:checkbox").prop('checked', true)
            $('#tableTipoHoras tr').addClass('table-active')
        });
        $('#nocheckAllThora').on('click', function () {
            $("#tableTipoHoras input:checkbox").prop('checked', false)
            $('#tableTipoHoras tr').removeClass('table-active')
        });

        $('#aplicarTHora').on('click', function () {
            $('#tableTipoHoras').DataTable().search('').draw();
            CheckSesion()
            let selected = new Array();
            $("#tableTipoHoras input:checkbox:checked").each(function () {
                (selected.push(parseInt($(this).val())));
            });
            $.ajax({
                url: "../listas/setLista.php",
                type: "POST",
                data: {
                    lista: 5,
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
                        $('#tableTipoHoras').DataTable().ajax.reload()
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                        $('#tableTipoHoras').DataTable().ajax.reload()
                    }
                },
                error: function (data) {
                    $.notifyClose();
                    notify('Error', 'danger', 5000, 'right')
                }
            })
        });
        $('#tipoHoras').removeClass('invisible')
    })
});