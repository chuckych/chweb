$(function () {
    'use strict'

    let cliente = $('#cliente').val()
    let uid = $('#uid').val()

    let tableGrupos = $('#tableGrupos').dataTable({
        initComplete: function (settings, json) {
            if(json.data.length===0){
                $("#tableGrupos").parents('.table-responsive').hide();
                $('#grupo').html('<div class="my-3 fontq">No se encontraron resultados</div>')
            }
        },
        dom: "<'row'<'col-12 divGrupoFilter d-flex align-items-center justify-content-between pt-1'f>>" +
            "<'row'<'col-12 divTableGrup 't>>" +
            "<'row'<'col-12 d-flex align-items-center justify-content-end'>" +
            "<'col-12 d-flex align-items-center justify-content-between divGrupoInfo pt-2 pl-0 pr-0'i><'col-12 pt-2 pl-0 divRefreshGrupo'>>",
        "ajax": {
            url: "listas_estruct/getListas.php",
            type: "GET",
            dataType: "json",
            "data": function (data) {
                data._c = cliente,
                data.lista   = 6,
                data.uid     = uid
            },
            error: function () {
                $("#tableGrupos").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex,) {
            $(row).addClass('animate__animated animate__fadeIn pointer checkGrupo');
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
                            $('#Grupo_' + row['codigo']).parents('tr').addClass('table-active')
                        }, 200);
                    }
                    let classTipo = 'GrupClass_' + row.idtipo
                    let cod = (row['codigo']===0) ? 32768 : row['codigo']
                    let datacol = `
                    <div class="custom-control custom-checkbox">
                        <input `+ checked + ` type="checkbox" class="custom-control-input ` + classTipo + `" id="Grupo_` + row['codigo'] + `" value="` + cod + `">
                        <label class="custom-control-label" for="Grupo_`+ row['codigo'] + `"></label>
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
            "sInfo": "_START_ al _END_ de _TOTAL_ Grupos",
            "sInfoEmpty": "No se encontraron resultados",
            "sInfoFiltered": "<br>(Filtrado de un total de _MAX_ Grupos)",
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
    tableGrupos.on('init.dt', function () {

        $('#tableGrupos_info').css('margin-top', '0px')
        $('#tableGrupos_info').addClass('p-0')

        $('#tableGrupos tbody').on('click', '.checkGrupo', function (e) {
            e.preventDefault();
            let data = $(tableGrupos).DataTable().row($(this)).data();
            if ($('#Grupo_' + data.codigo).is(":checked")) {
                $('#Grupo_' + data.codigo).prop('checked', false)
                $(this).removeClass('table-active')
            } else {
                $('#Grupo_' + data.codigo).prop('checked', true)
                $(this).addClass('table-active')
            };
        });

        $("#tableGrupos_filter .form-control").attr('placeholder', 'Buscar Grupo')
        $(this).children('thead').remove()
        $('.divTableGrup').css('max-height', '300px')
        $('.divTableGrup').addClass('overflow-auto')

        let buttonsRefresh = `
            <div class="">
                <button class="btn btn-link btn-sm fontq p-0 m-0" id="refreshGrupList">Actualizar Grilla</button>
            </div>
            `
        let buttonsCheck = `
            <div class="">
                <button class="btn btn-link btn-sm fontq" id="checkAllGrup">Marcar</button>
                <button class="ml-1 btn btn-link btn-sm fontq" id="nocheckAllGrup">Desmarcar</button>
            </div>
            `
        let buttonsSubmit = `
            <div class="">
                <button type="button" class="btn btn-custom btn-sm btn-mobile fontq w100" id="aplicarGrup">Aplicar</button>
            </div>
            `
        $(this).parents().find('.divGrupoFilter').prepend(buttonsCheck)
        $(this).parents().find('.divGrupoInfo').append(buttonsSubmit)
        $(this).parents().find('.divRefreshGrupo').append(buttonsRefresh)

        $('#refreshGrupList').on('click', function (e) {
            e.preventDefault();
            $('#tableGrupos').DataTable().ajax.reload()
        })
        $('#checkAllGrup').on('click', function () {
            $("#tableGrupos input:checkbox").prop('checked', true)
            $('#tableGrupos tr').addClass('table-active')
        });
        $('#nocheckAllGrup').on('click', function () {
            $("#tableGrupos input:checkbox").prop('checked', false)
            $('#tableGrupos tr').removeClass('table-active')
        });

        $('#aplicarGrup').on('click', function () {
            CheckSesion()
            let selected = new Array();
            $("#tableGrupos input:checkbox:checked").each(function () {
                (selected.push(parseInt($(this).val())));
            });
            $.ajax({
                url: "listas_estruct/setLista.php",
                type: "POST",
                data: {
                    lista   : 6,
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
    })
});