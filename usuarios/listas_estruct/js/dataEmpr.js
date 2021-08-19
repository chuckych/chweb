function actualizaListas() {
    finishCallBack = 0
    $('#tableEmpresa').DataTable().ajax.reload()
    $('#tablePlantas').DataTable().ajax.reload()
    $('#tableConvenios').DataTable().ajax.reload()
    $('#tableSectores').DataTable().ajax.reload()
    $('#tableSecciones').DataTable().ajax.reload()
    $('#tableGrupos').DataTable().ajax.reload()
    $('#tableSucursales').DataTable().ajax.reload()
    $('#tablePersonal').DataTable().ajax.reload()
}
finishCallBack = 0
relacionesSwith = 1
$('#relacionesSwith').on('change', function (e) {
    finishCallBack = 0
    habilitarRelacionesSwith(finishCallBack)
    if ($("#relacionesSwith").is(":checked")) {
        relacionesSwith = 1
        $('#divRelacion').attr('data-titler', 'Quitar relación de estructura')
    } else {
        relacionesSwith = 0
        $('#divRelacion').attr('data-titler', 'Mantener relación de estructura')
    }
    actualizaListas()
    e.stopImmediatePropagation()
});
function habilitarRelacionesSwith(finishCallBack) {
    if (finishCallBack >= 8) {
        
        if ($("#relacionesSwith").is(":disabled")) {
            $('#relacionesSwith').prop('disabled', false)
        }
        setTimeout(() => {
            $('#spanFinishTable').html('')
        }, 300);
    } else {
        if (!$("#relacionesSwith").is(":disabled")) {
            $('#relacionesSwith').prop('disabled', true)
        }
    }
    // console.log(finishCallBack);
}
$(function () {
    'use strict'
    let cliente = $('#cliente').val()
    let uid = $('#uid').val()
    // let relacionesSwith = $('#relacionesSwith').val()    

    let tableEmpresa = $('#tableEmpresa').dataTable({
        initComplete: function (settings, json) {
            if (json.data.length === 0) {
                $("#tableEmpresa").parents('.table-responsive').hide();
                $('#empresa').html('<div class="my-3 fontq">No se encontraron resultados</div>')
            }
        },
        dom: "<'row'<'col-12 divEmpFilter d-flex align-items-center justify-content-between pt-1'f>>" +
            "<'row'<'col-12 divTableEmp 't>>" +
            "<'row'<'col-12 d-flex align-items-center justify-content-end'>" +
            "<'col-12 d-flex align-items-center justify-content-between divEmpresaInfo pt-2 pl-0 pr-0'i><'col-12 pt-2 pl-0 divRefreshEmpresa'>>",
        "ajax": {
            url: "listas_estruct/getListas.php",
            type: "GET",
            dataType: "json",
            "data": function (data) {
                data._c = cliente,
                    data.lista = 1,
                    data.uid = uid,
                    data.rel = relacionesSwith
            },
            error: function () {
                $("#tableEmpresa").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex,) {
            $(row).addClass('animate__animated animate__fadeIn pointer checkEmpresa');
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
                    let datacol = '<span data-titlel="Total Legajos">' + row['totLeg'] + '</span>'
                    return datacol;
                },
            },
            {
                className: 'align-middle', targets: '', title: '',
                "render": function (data, type, row, meta) {
                    let checked = (row.set === 1) ? 'checked' : ''
                    if (checked) {
                        setTimeout(() => {
                            $('#Empresa_' + row['codigo']).parents('tr').addClass('table-active')
                        }, 200);
                    }
                    let classTipo = 'EmpClass_' + row.idtipo
                    let cod = (row['codigo'] === 0) ? 32768 : row['codigo']
                    let datacol = `
                    <div class="custom-control custom-checkbox">
                        <input `+ checked + ` type="checkbox" class="custom-control-input ` + classTipo + `" id="Empresa_` + row['codigo'] + `" value="` + cod + `">
                        <label class="custom-control-label" for="Empresa_`+ row['codigo'] + `"></label>
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
            "sInfo": "_START_ al _END_ de _TOTAL_ Empresas",
            "sInfoEmpty": "No se encontraron resultados",
            "sInfoFiltered": "<br>(Filtrado de un total de _MAX_ Empresas)",
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
    tableEmpresa.on('init.dt', function () {
        $('#tableEmpresa_info').css('margin-top', '0px')
        $('#tableEmpresa_info').addClass('p-0')

        $('#tableEmpresa tbody').on('click', '.checkEmpresa', function (e) {
            e.preventDefault();
            let data = $(tableEmpresa).DataTable().row($(this)).data();
            if ($('#Empresa_' + data.codigo).is(":checked")) {
                $('#Empresa_' + data.codigo).prop('checked', false)
                $(this).removeClass('table-active')
            } else {
                $('#Empresa_' + data.codigo).prop('checked', true)
                $(this).addClass('table-active')
            };
        });

        $("#tableEmpresa_filter .form-control").attr('placeholder', 'Buscar Empresa')
        $(this).children('thead').remove()
        $('.divTableEmp').css('max-height', '300px')
        $('.divTableEmp').addClass('overflow-auto')

        let buttonsRefresh = `
            <div class="">
                <button class="btn btn-link btn-sm fontq p-0 m-0" id="refreshEmpresaList">Actualizar Grilla</button>
            </div>
            `
        let buttonsCheck = `
            <div class="">
                <button class="btn btn-link btn-sm fontq" id="checkAllEmpresa">Marcar</button>
                <button class="ml-1 btn btn-link btn-sm fontq" id="nocheckAllEmpresa">Desmarcar</button>
            </div>
            `
        let buttonsSubmit = `
            <div class="">
                <button type="button" class="btn btn-custom btn-sm btn-mobile fontq w100" id="aplicarEmpresa">Aplicar</button>
            </div>
            `
        $(this).parents().find('.divEmpFilter').prepend(buttonsCheck)
        $(this).parents().find('.divEmpresaInfo').append(buttonsSubmit)
        $(this).parents().find('.divRefreshEmpresa').append(buttonsRefresh)

        $('#refreshEmpresaList').on('click', function (e) {
            e.preventDefault();
            habilitarRelacionesSwith(8)
            $('#tableEmpresa').DataTable().ajax.reload()
        })
        $('#checkAllEmpresa').on('click', function () {
            $("#tableEmpresa input:checkbox").prop('checked', true)
            $('#tableEmpresa tr').addClass('table-active')
        });
        $('#nocheckAllEmpresa').on('click', function () {
            $("#tableEmpresa input:checkbox").prop('checked', false)
            $('#tableEmpresa tr').removeClass('table-active')
        });

        $('#aplicarEmpresa').on('click', function () {
            CheckSesion()
            let selected = new Array();
            $("#tableEmpresa input:checkbox:checked").each(function () {
                (selected.push(parseInt($(this).val())));
            });
            $.ajax({
                url: "listas_estruct/setLista.php",
                type: "POST",
                data: {
                    lista: 1,
                    check: JSON.stringify(selected),
                    uid: uid,
                    _c: cliente,
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
        $('#empresa').removeClass('invisible')
    })
    tableEmpresa.on('draw.dt', function () {
        $('#spanFinishTable').html('Empresas')
        finishCallBack = finishCallBack + 1
        habilitarRelacionesSwith(finishCallBack)
    })
    $('#initEstruct').on('click', function () {
        CheckSesion()
        $.ajax({
            url: "listas_estruct/setLista.php",
            type: "POST",
            data: {
                listaInit: 1,
                uid: uid,
                _c: cliente,
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
});


function setGeneral(uid, cliente, idTable, lista) {
    let checked = new Array();
    $("#" + idTable + " input:checkbox").each(function () {
        if ($(this).is(':checked')) {
            (checked.push(parseInt($(this).val()))); /** Array de checkbox checked*/
        }
    });
    $.ajax({
        url: "listas_estruct/setLista.php?e=" + idTable,
        type: "POST",
        data: {
            lista: lista,
            check: JSON.stringify(checked),
            uid: uid,
            _c: cliente,
        },
        success: function (data) {
        },
        error: function (data) {
        }
    })
    return true
}
function setSeccion(uid, cliente) {
    let checked3 = new Array();
    $("#tableSecciones input:checkbox:checked").each(function () {
        (checked3.push(parseInt($(this).val())));
    });
    $.ajax({
        url: "listas_estruct/setLista.php?e=" + "tableSecciones",
        type: "POST",
        data: {
            lista2: 5,
            check: JSON.stringify(checked3),
            uid: uid,
            _c: cliente,
        },
        success: function (data) {
            if (data.status == "ok") {
                return true
            } else {
                actualizaListas()
            }
        },
        error: function (data) {
            $.notifyClose();
            notify('Error Secciones', 'danger', 5000, 'right')
            // $('.loader').hide()
        }
    })
    return true
}
function actualizaSet() {
    let cliente = $('#cliente').val()
    let uid = $('#uid').val()
    setTimeout(() => {
        setGeneral(uid, cliente, 'tableEmpresa', 1)
        setGeneral(uid, cliente, 'tablePlantas', 2)
        setGeneral(uid, cliente, 'tableConvenios', 3)
        setGeneral(uid, cliente, 'tableSectores', 4)
        setSeccion(uid, cliente)
        setGeneral(uid, cliente, 'tableGrupos', 6)
        setGeneral(uid, cliente, 'tableSucursales', 7)
        setGeneral(uid, cliente, 'tablePersonal', 8)
    }, 1000);
}
