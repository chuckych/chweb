$(function () {
    'use strict'
    let cliente = $('#cliente').val()
    let uid = $('#uid').val()

    let tablePlantas = $('#tablePlantas').dataTable({
        initComplete: function (settings, json) {
            if (json.data.length === 0) {
                $("#tablePlantas").parents('.table-responsive').hide();
                $('#planta').html('<div class="my-3 fontq">No se encontraron resultados</div>')
            }
        },
        dom: "<'row'<'col-12 divPlanFilter d-flex align-items-center justify-content-between pt-1'f>>" +
            "<'row'<'col-12 divTablePlan 't>>" +
            "<'row'<'col-12 d-flex align-items-center justify-content-end'>" +
            "<'col-12 d-flex align-items-center justify-content-between divPlantaInfo pt-2 pl-0 pr-0'i><'col-12 pt-2 pl-0 divRefreshPlanta'>>",
        "ajax": {
            url: "listas_estruct/getListas.php",
            type: "GET",
            dataType: "json",
            "data": function (data) {
                data._c = cliente,
                    data.lista = 2,
                    data.uid = uid,
                    data.rel = relacionesSwith
            },
            error: function () {
                $("#tablePlantas").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex,) {
            $(row).addClass('animate__animated animate__fadeIn pointer checkPlanta');
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
                            $('#Planta_' + row['codigo']).parents('tr').addClass('table-active')
                        }, 200);
                    }
                    let classTipo = 'PlanClass_' + row.idtipo
                    let cod = (row['codigo'] === 0) ? 32768 : row['codigo']
                    let datacol = `
                    <div class="custom-control custom-checkbox">
                        <input `+ checked + ` type="checkbox" class="custom-control-input ` + classTipo + `" id="Planta_` + row['codigo'] + `" value="` + cod + `">
                        <label class="custom-control-label" for="Planta_`+ row['codigo'] + `"></label>
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
            "sInfo": "_START_ al _END_ de _TOTAL_ Plantas",
            "sInfoEmpty": "No se encontraron resultados",
            "sInfoFiltered": "<br>(Filtrado de un total de _MAX_ Plantas)",
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
    tablePlantas.on('init.dt', function () {

        $('#tablePlantas_info').css('margin-top', '0px')
        $('#tablePlantas_info').addClass('p-0')

        $('#tablePlantas tbody').on('click', '.checkPlanta', function (e) {
            e.preventDefault();
            let data = $(tablePlantas).DataTable().row($(this)).data();
            if ($('#Planta_' + data.codigo).is(":checked")) {
                $('#Planta_' + data.codigo).prop('checked', false)
                $(this).removeClass('table-active')
            } else {
                $('#Planta_' + data.codigo).prop('checked', true)
                $(this).addClass('table-active')
            };
        });

        $("#tablePlantas_filter .form-control").attr('placeholder', 'Buscar Planta')
        $(this).children('thead').remove()
        $('.divTablePlan').css('max-height', '300px')
        $('.divTablePlan').addClass('overflow-auto')

        let buttonsRefresh = `
            <div class="">
                <button class="btn btn-link btn-sm fontq p-0 m-0" id="refreshPlantaList">Actualizar Grilla</button>
            </div>
            `
        let buttonsCheck = `
            <div class="">
                <button class="btn btn-link btn-sm fontq" id="checkAllPlanta">Marcar</button>
                <button class="ml-1 btn btn-link btn-sm fontq" id="nocheckAllPlanta">Desmarcar</button>
            </div>
            `
        let buttonsSubmit = `
            <div class="">
                <button type="button" class="btn btn-custom btn-sm btn-mobile fontq w100" id="aplicarPlanta">Aplicar</button>
            </div>
            `
        $(this).parents().find('.divPlanFilter').prepend(buttonsCheck)
        $(this).parents().find('.divPlantaInfo').append(buttonsSubmit)
        $(this).parents().find('.divRefreshPlanta').append(buttonsRefresh)

        $('#refreshPlantaList').on('click', function (e) {
            e.preventDefault();
            habilitarRelacionesSwith(8)
            $('#tablePlantas').DataTable().ajax.reload()
        })
        $('#checkAllPlanta').on('click', function () {
            $("#tablePlantas input:checkbox").prop('checked', true)
            $('#tablePlantas tr').addClass('table-active')
        });
        $('#nocheckAllPlanta').on('click', function () {
            $("#tablePlantas input:checkbox").prop('checked', false)
            $('#tablePlantas tr').removeClass('table-active')
        });

        $('#aplicarPlanta').on('click', function () {
            CheckSesion()
            let selected = new Array();
            $("#tablePlantas input:checkbox:checked").each(function () {
                (selected.push(parseInt($(this).val())));
            });
            $.ajax({
                url: "listas_estruct/setLista.php",
                type: "POST",
                data: {
                    lista: 2,
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
        $('#planta').removeClass('invisible')
    })
    tablePlantas.on('draw.dt', function () {
        $('#spanFinishTable').html('Plantas')
        finishCallBack = finishCallBack + 1
        habilitarRelacionesSwith(finishCallBack)
    })
});