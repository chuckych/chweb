$(function () {
    'use strict'

    let cliente_rol = $('#cliente_rol').val()
    let recid_rol = $('#recid_rol').val()
    let id_rol = $('#id_rol').val()

    let tableHorarios = $('#tableHorarios').dataTable({
        initComplete: function (settings, json) {
            if(json.data.length===0){
                $("#tableHorarios").parents('.table-responsive').hide();
                $('#horarios').html('<div class="my-3 fontq">No se encontraron resultados</div>')
            }
        },
        dom: "<'row'<'col-12 divHorFilter d-flex align-items-center justify-content-between pt-1'f>>" +
            "<'row'<'col-12 divtablehorario 't>>" +
            "<'row'<'col-12 d-flex align-items-center justify-content-end'>" +
            "<'col-12 d-flex align-items-center justify-content-between divHorarioInfo pt-2 pl-0 pr-0'i><'col-12 pt-2 pl-0 divRefreshHorario'>>",
        "ajax": {
            url: "../listas/getListas.php",
            type: "GET",
            dataType: "json",
            "data": function (data) {
                data._c = cliente_rol,
                    data.lista = 3,
                    data.id_rol = id_rol
            },
            error: function () {
                $("#tableHorarios").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex,) {
            $(row).addClass('animate__animated animate__fadeIn pointer checkHorario');
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
                            $('#Horario_' + row['codigo']).parents('tr').addClass('table-active')
                        }, 200);
                    }
                    let classTipo = 'Horclass_' + row.idtipo
                    let cod = (row['codigo']===0) ? 32768 : row['codigo']
                    let datacol = `
                    <div class="custom-control custom-checkbox">
                        <input `+ checked + ` type="checkbox" class="custom-control-input ` + classTipo + `" id="Horario_` + row['codigo'] + `" value="` + cod + `">
                        <label class="custom-control-label" for="Horario_`+ row['codigo'] + `"></label>
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
            "sInfo": "_START_ al _END_ de _TOTAL_ Horarios",
            "sInfoEmpty": "No se encontraron resultados",
            "sInfoFiltered": "<br>(Filtrado de un total de _MAX_ Horarios)",
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
    tableHorarios.on('init.dt', function () {

        $('#tableHorarios_info').css('margin-top', '0px')
        $('#tableHorarios_info').addClass('p-0')

        $('#tableHorarios tbody').on('click', '.checkHorario', function (e) {
            e.preventDefault();
            let data = $(tableHorarios).DataTable().row($(this)).data();
            if ($('#Horario_' + data.codigo).is(":checked")) {
                $('#Horario_' + data.codigo).prop('checked', false)
                $(this).removeClass('table-active')
            } else {
                $('#Horario_' + data.codigo).prop('checked', true)
                $(this).addClass('table-active')
            };
        });

        $("#tableHorarios_filter .form-control").attr('placeholder', 'Buscar Horario')
        $(this).children('thead').remove()
        $('.divtablehorario').css('max-height', '300px')
        $('.divtablehorario').addClass('overflow-auto')

        let buttonsRefresh = `
            <div class="">
                <button class="btn btn-link btn-sm fontq p-0 m-0" id="refreshHorarioList">Actualizar Grilla</button>
            </div>
            `
        let buttonsCheck = `
            <div class="">
                <button class="btn btn-link btn-sm fontq" id="checkAllHorario">Marcar</button>
                <button class="ml-1 btn btn-link btn-sm fontq" id="nocheckAllHorario">Desmarcar</button>
            </div>
            `
        let buttonsSubmit = `
            <div class="">
                <button type="button" class="btn btn-custom btn-sm btn-mobile fontq w100" id="aplicarHorarios">Aplicar</button>
            </div>
            `
        $(this).parents().find('.divHorFilter').prepend(buttonsCheck)
        $(this).parents().find('.divHorarioInfo').append(buttonsSubmit)
        $(this).parents().find('.divRefreshHorario').append(buttonsRefresh)

        $('#refreshHorarioList').on('click', function (e) {
            e.preventDefault();
            $('#tableHorarios').DataTable().ajax.reload()
        })
        $('#checkAllHorario').on('click', function () {
            $("#tableHorarios input:checkbox").prop('checked', true)
            $('#tableHorarios tr').addClass('table-active')
        });
        $('#nocheckAllHorario').on('click', function () {
            $("#tableHorarios input:checkbox").prop('checked', false)
            $('#tableHorarios tr').removeClass('table-active')
        });

        $('#aplicarHorarios').on('click', function () {
            CheckSesion()
            let selected = new Array();
            $("#tableHorarios input:checkbox:checked").each(function () {
                (selected.push(parseInt($(this).val())));
            });
            $.ajax({
                url: "../listas/setLista.php",
                type: "POST",
                data: {
                    lista: 3,
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
                        $('#tableHorarios').DataTable().ajax.reload()
                    } else {
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                        $('#tableHorarios').DataTable().ajax.reload()
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