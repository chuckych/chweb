$(function () {
    'use strict'

    let cliente = $('#cliente').val()
    let uid = $('#uid').val()

        let tableCopyListas = $('#tableCopyListas').dataTable({
            drawCallback: function (settings) {
                $(".divtablecopylista  .dataTable thead").remove()
            },
            dom: "<'row'<'col-12 divCopyListaFilter d-flex align-items-center justify-content-between pt-1'f>>" +
                "<'row'<'col-12 divtablecopylista 't>>" +
                "<'row'<'col-12 d-flex align-items-center justify-content-end'>" +
                "<'col-12 d-flex align-items-center justify-content-between divCopyListaInfo pt-2 pl-0 pr-0'i><'col-12 pt-2 pl-0 divRefreshCopyLista'>>",
            "ajax": {
                url: "listas_estruct/getListas.php",
                type: "GET",
                dataType: "json",
                "data": function (data) {
                    data._c = cliente,
                        data.lista = 10,
                        data.uid = uid
                },
                error: function () {
                    $("#tableCopyListas").css("display", "none");
                }
            },
            createdRow: function (row, data, dataIndex,) {
                $(row).addClass('animate__animated animate__fadeIn pointer checkCopyLista');
            },
            columns: [
                {
                    className: 'align-middle w-100 ', targets: 'descripcion', title: '',
                    "render": function (data, type, row, meta) {
                        let datacol = `<div class="d-inline-flex">` + row['descripcion'] + `</div>`
                        return datacol;
                    },
                },
                {
                    className: 'align-middle text-center', targets: '', title: '',
                    "render": function (data, type, row, meta) {
                        let checked = (row.set === 1) ? 'checked' : ''
                        if (checked) {
                            setTimeout(() => {
                                $('#CopyLista_' + row['codigo']).parents('tr').addClass('table-active')
                            }, 200);
                        }
                        let classTipo = 'CopyListaClass_' + row.idtipo
                        let datacol = `
                    <div class="custom-control custom-checkbox">
                        <input `+ checked + ` type="checkbox" class="custom-control-input ` + classTipo + `" id="CopyLista_` + row['codigo'] + `" value="` + row['codigo'] + `">
                        <label class="custom-control-label" for="CopyLista_`+ row['codigo'] + `"></label>
                    </div>
                    `
                        return datacol;
                    },
                },
            ],
            scrollX: true,
            scrollCollapse: true,
            scrollY: '300px',
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
            autoWidth: true,
            language: {
                "sProcessing": "Actualizando . . .",
                "sLengthMenu": "_MENU_",
                "sZeroRecords": "",
                "sEmptyTable": "",
                "sInfo": "_START_ al _END_ de _TOTAL_ Usuarios",
                "sInfoEmpty": "No se encontraron resultados",
                "sInfoFiltered": "<br>(Filtrado de un total de _MAX_ Usuarios)",
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
        tableCopyListas.on('init.dt', function () {
            $('#tableCopyListas_info').css('margin-top', '0px')
            $('#tableCopyListas_info').addClass('p-0')

            $('#tableCopyListas tbody').on('click', '.checkCopyLista', function (e) {
                e.preventDefault();
                let data = $(tableCopyListas).DataTable().row($(this)).data();
                if ($('#CopyLista_' + data.codigo).is(":checked")) {
                    $('#CopyLista_' + data.codigo).prop('checked', false)
                    $(this).removeClass('table-active')
                } else {
                    $('#CopyLista_' + data.codigo).prop('checked', true)
                    $(this).addClass('table-active')
                };
            });
            $("#tableCopyListas_filter .form-control").attr('placeholder', 'Buscar Usuario')
            $("#tableCopyListas thead").remove()
            // $('.divtablecopylista').css('max-height', '300px')
            // $('.divtablecopylista').addClass('overflow-auto')

            let buttonsRefresh = `
            <div class="">
                <button class="btn btn-link btn-sm fontq p-0 m-0" id="refreshCopyLista">Actualizar Grilla</button>
            </div>
            `
            let buttonsCheck = `
            <div class="divChecksMarcar">
                <button class="btn btn-link btn-sm fontq" id="checkAllCopyLista">Marcar Todo</button>
                <button class="ml-1 btn btn-link btn-sm fontq" id="nocheckAllCopyLista">Desmarcar Todo</button>
            </div>
            `
            let buttonsSubmit = `
            <div class="">
                <button type="button" class="btn btn-custom btn-sm btn-mobile fontq w100" id="aplicarCopyLista">Aplicar</button>
            </div>
            `
            $(this).parents().find('.divCopyListaFilter .divChecksMarcar').remove()
            $(this).parents().find('.divCopyListaFilter').prepend(buttonsCheck)
            $(this).parents().find('.divCopyListaInfo #aplicarCopyLista').remove()
            $(this).parents().find('.divCopyListaInfo').append(buttonsSubmit)
            $(this).parents().find('.divRefreshCopyLista').html('')
            $(this).parents().find('.divRefreshCopyLista').append(buttonsRefresh)

            $('#refreshCopyLista').on('click', function (e) {
                e.preventDefault();
                $('#tableCopyListas').DataTable().ajax.reload()
            })
            $('#checkAllCopyLista').on('click', function () {
                $("#tableCopyListas input:checkbox").prop('checked', true)
                $('#tableCopyListas tr').addClass('table-active')
            });
            $('#nocheckAllCopyLista').on('click', function () {
                $("#tableCopyListas input:checkbox").prop('checked', false)
                $('#tableCopyListas tr').removeClass('table-active')
            });

            $('#copyListas .nombreRol').html($('#modalListasLabel .nombreRol').text())

            $('#aplicarCopyLista').on('click', function () {
                CheckSesion()
                let selected = new Array();
                $("#tableCopyListas input:checkbox:checked").each(function () {
                    (selected.push(parseInt($(this).val())));
                });
                $.ajax({
                    url: "listas_estruct/setLista.php",
                    type: "POST",
                    data: {
                        listaEstruct: 10,
                        check: JSON.stringify(selected),
                        uid: uid,
                        _c: cliente
                    },
                    beforeSend: function (data) {
                        $.notifyClose();
                        notify('Aguarde..', 'info', 0, 'right')
                    },
                    success: function (data) {
                        if (data.status == "ok") {
                            $.notifyClose();
                            notify(data.Mensaje, 'success', 10000, 'right')
                            $('#tableCopyListas').DataTable().ajax.reload()
                        } else {
                            $.notifyClose();
                            notify(data.Mensaje, 'danger', 5000, 'right')
                            $('#tableCopyListas').DataTable().ajax.reload()
                        }
                    },
                    error: function (data) {
                        $.notifyClose();
                        notify('Error', 'danger', 5000, 'right')
                    }
                })
            });
            $('#copyListas').removeClass('invisible')
        })
});