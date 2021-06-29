$(function () {

    var maskBehavior = function (val) {
        val = val.split(":");
        return parseInt(val[0]) > 19 ? "HZ:M0" : "H0:M0";
    }
    spOptions = {
        onKeyPress: function (val, e, field, options) {
            field.mask(maskBehavior.apply({}, arguments), options);
        },
        translation: {
            'H': { pattern: /[0-2]/, optional: false },
            'Z': { pattern: /[0-3]/, optional: false },
            'M': { pattern: /[0-5]/, optional: false }
        }
    };


    function ActualizaTablas() {
        $("#Horale1").DataTable().ajax.reload(null, false)
        $("#Horale2").DataTable().ajax.reload(null, false)
        $("#Rotacion").DataTable().ajax.reload(null, false)
    };

    $("#_eg").click(function () {
        CheckSesion()
        $("#detalleHorario").hide()
        if ($("#_eg").is(":checked")) {
            $("#_eg").val('on').trigger('change')
            $('#tablePersonal').DataTable().ajax.reload();
        } else {
            $("#_eg").val('off').trigger('change')
            $('#tablePersonal').DataTable().ajax.reload();
        }
    });
    $("#_porApNo").click(function () {
        CheckSesion()
        $("#detalleHorario").hide()
        if ($("#_porApNo").is(":checked")) {
            $("#_porApNo").val('on').trigger('change')
            $('#tablePersonal').DataTable().ajax.reload();
        } else {
            $("#_porApNo").val('off').trigger('change')
            $('#tablePersonal').DataTable().ajax.reload();
        }
    });
    $('#tablePersonal').dataTable({
        "initComplete": function (settings) {
            setTimeout(() => {
                $("#PersonalTable").removeClass('invisible');
            }, 100);
            classEfect("#PersonalTable", 'animate__animated animate__fadeIn')
            $("#tablePersonal_filter .form-control").attr('placeholder', 'Buscar')
        },
        "drawCallback": function (settings) {
            $("td").tooltip({ container: 'table' });
            $('[data-toggle="tooltip"]').tooltip();
            if ($("#_eg").is(":checked")) {
                $('td').addClass('text-danger')
            } else {
                $('td').removeClass('text-danger')
            }
        },
        bProcessing: true,
        serverSide: false,
        deferRender: true,
        stateSave: true,
        stateDuration: -1,
        "ajax": {
            url: "getPersonal.php",
            type: "POST",
            dataType: "json",
            "data": function (data) {
                data._eg = $("input[name=_eg]:checked").val();
                data._porApNo = $("input[name=_porApNo]:checked").val();
                data.Per = $("#Per").val();
                data.Emp = $("#Emp").val();
                data.Plan = $("#Plan").val();
                data.Sect = $("#Sect").val();
                data.Sec2 = $("#Sec2").val();
                data.Grup = $("#Grup").val();
                data.Sucur = $("#Sucur").val();
                data.Tipo = $("#Tipo").val();
                data.Tare = $("#Tare").val();
                data.Conv = $("#Conv").val();
                data.Regla = $("#Regla").val();
            },
            error: function () {
                $("#tablePersonal").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass('animate__animated animate__fadeIn pointer');
        },
        columns: [
            {
                className: 'view', targets: 'pers_legajo', title: 'Legajo',
                "render": function (data, type, row, meta) {
                    let datacol = row['pers_legajo']
                    return datacol;
                },
            },
            {
                className: 'w-100 view', targets: 'pers_nombre', title: 'Apellido y Nombre',
                "render": function (data, type, row, meta) {
                    let datacol = row['pers_nombre']
                    return datacol;
                },
            },
        ],
        paging: true,
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
            "sInfoFiltered": "<br>(Filtrado de un total de _MAX_)",
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
    function getListHorarios(datos, selector) {
        $(selector).dataTable({
            initComplete: function (settings) {
                $("#tableHorarios_wrapper thead").remove()
                $(selector).show()
                $(selector + "_filter .form-control").attr('placeholder', 'Buscar Horario')
            },
            drawCallback: function (settings) {
                $("#tableHorarios_wrapper thead").remove()
                classEfect('#tableHorarios_wrapper', 'animate__animated animate__fadeIn')
            },
            createdRow: function (row, data, dataIndex) {
                $(row).addClass('animate__animated animate__fadeIn pointer');
            },
            "ajax": {
                url: "getHorale.php",
                type: "POST",
                dataType: "json",
                "data": function (data) {
                    data.datos = datos;
                },
                error: function () {
                    $(selector).css("display", "none");
                }
            },

            columns: [
                {
                    className: 'text-center align-middle select', targets: 'HorCodi', title: '',
                    "render": function (data, type, row, meta) {
                        let datacol = '<span id="H' + row['HorCodi'] + 'H" class="select" title="Código de Horario">' + row['HorCodi'] + '</span>'
                        return datacol;
                    },
                },
                {
                    className: 'align-middle w-100 select', targets: 'HorDesc', title: '',
                    "render": function (data, type, row, meta) {
                        let datacol = `<span class="select" title="Horario">` + row[`HorDesc`] + `</span>`
                        return datacol;
                    },
                },
                {
                    className: 'text-center align-middle select', targets: 'HorID', title: '',
                    "render": function (data, type, row, meta) {
                        let datacol = `<span class="select" title="ID">` + row[`HorID`] + `</span>`
                        return datacol;
                    },
                },
            ],
            scrollY: '200px',
            scrollX: true,
            scrollCollapse: true,
            deferRender: true,
            bProcessing: false,
            serverSide: false,
            paging: false,
            searching: true,
            info: true,
            ordering: false,
            responsive: false,
            language:
            {
                "sProcessing": "Actualizando . . .",
                "sLengthMenu": "_MENU_",
                "sZeroRecords": "",
                "sEmptyTable": "",
                "sInfo": "Mostrando _START_ al _END_ de _TOTAL_ Horarios",
                "sInfoEmpty": "No se encontraron resultados",
                "sInfoFiltered": "(filtrado de un total de _MAX_ Horarios)",
                "sInfoPostFix": "",
                "sSearch": "",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "<div class='spinner-border text-light'></div>",
            },
        });
    }
    function getListRotaciones(datos, selector) {
        $(selector).dataTable({
            initComplete: function (settings) {
                $("#tableHorarios_wrapper thead").remove()
                $(selector).show()
                $(selector + "_filter .form-control").attr('placeholder', 'Buscar Rotación')
            },
            drawCallback: function (settings) {
                $("#tableHorarios_wrapper thead").remove()
                classEfect('#tableHorarios_wrapper', 'animate__animated animate__fadeIn')
            },
            createdRow: function (row, data, dataIndex) {
                $(row).addClass('animate__animated animate__fadeIn pointer');
            },
            "ajax": {
                url: "getHorale.php",
                type: "POST",
                dataType: "json",
                "data": function (data) {
                    data.datos = datos;
                },
                error: function () {
                    $(selector).css("display", "none");
                }
            },

            columns: [
                {
                    className: 'text-center align-middle select', targets: 'RotCodi', title: '',
                    "render": function (data, type, row, meta) {
                        let datacol = '<span id="H' + row['RotCodi'] + 'H" class="select" title="Código de Rotación">' + row['RotCodi'] + '</span>'
                        return datacol;
                    },
                },
                {
                    className: 'align-middle w-100 select', targets: 'RotDesc', title: '',
                    "render": function (data, type, row, meta) {
                        let datacol = `<span class="select" title="Rotación">` + row[`RotDesc`] + `</span>`
                        return datacol;
                    },
                },
            ],
            scrollY: '200px',
            scrollX: true,
            scrollCollapse: true,
            deferRender: true,
            bProcessing: false,
            serverSide: false,
            paging: false,
            searching: true,
            info: true,
            ordering: false,
            responsive: false,
            language:
            {
                "sProcessing": "Actualizando . . .",
                "sLengthMenu": "_MENU_",
                "sZeroRecords": "",
                "sEmptyTable": "",
                "sInfo": "Mostrando _START_ al _END_ de _TOTAL_ Horarios",
                "sInfoEmpty": "No se encontraron resultados",
                "sInfoFiltered": "(filtrado de un total de _MAX_ Horarios)",
                "sInfoPostFix": "",
                "sSearch": "",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "<div class='spinner-border text-light'></div>",
            },
        });
    }
    function getHorale1(datos, selector) {
        $(selector).dataTable({
            initComplete: function (settings) {
                $(selector + " thead").remove()
            },
            drawCallback: function (settings) {
                $(selector + " thead").remove()
                let titletabla = '<div>Horarios Desde: <span class="ls1">(' + (settings.aiDisplay.length) + ')</span></div>';
                let btnAdd = `<button title="Nueva asignación" class="btn btn-sm btn-light px-2 pointer border c_horale1"><i class="bi bi-plus fonth"></i></button>`
                if (settings.aiDisplay.length == 0) {
                    let titletabla = '<div class="fw4">Sin Horario Desde asignado.</div>';
                    $('#titleDesde').removeClass('btn-custom')
                    $('#titleDesde').addClass('text-dark shadow-sm')
                    $('#titleDesde').html(titletabla + btnAdd)
                    $('.c_horale1').removeClass('btn-custom')
                    $('.c_horale1').addClass('btn-custom')
                    $(selector).hide()
                } else {
                    $(selector).show()
                    $('#titleDesde').removeClass('text-dark shadow-sm')
                    $('#titleDesde').addClass('btn-custom')
                    $('#titleDesde').html(titletabla + btnAdd)
                    $('.c_horale1').removeClass('btn-custom')
                    $('.c_horale1').addClass('btn-light')
                }
                $(".c_horale1").click(function () {
                    CheckSesion()
                    let data = settings.json.data[0];
                    $('#actModal').modal('show')

                    $('#actModal .modal-title').html('Nueva Asignación')
                    getHTML('bodyHorale1.html', '#actModalbody')

                    setTimeout(() => {
                        $('#H1Horario').prepend(`
                        <label class="fontq">Fecha desde:</label>
                        <div class="d-inline-flex align-items-center w-100 animate__animated animate__fadeInDown mb-2">
                            <input type="text" class="form-control text-center h40 w150" name="FDesde" id="inputH1FDesde">
                        </div>
                        `)
                        singleDatePicker('#inputH1FDesde', 'right', 'down')
                        $('#H1Legajo').html('<label class="w60 fontq">Legajo:</label><span class="fw5">(' + $('.divTablas .NumLega').text() + ') ' + $('.divTablas .ApNo').text() + `</span>`)
                        $('#inputH1Legajo').val($('.divTablas .NumLega').text())
                        $('#inputH1Codhor').mask('0000');
                        $('#inputTipo').val('c_horale1');
                        $('#divtableHorarios').html('')
                        $('#divtableHorarios').html(`
                                <table id="tableHorarios" class="table text-nowrap mt-2 w-100 border border-top-0 table-hover"></table>
                            `)
                        let datos = JSON.stringify({ 'nombre': '', 'legajo': '', 'tabla': 'ListHorarios' });
                        setTimeout(() => {
                            getListHorarios(datos, '#tableHorarios')
                            $("#tableHorarios tbody").on('click', '.select', function (e) {
                                e.preventDefault();
                                let data = $("#tableHorarios").DataTable().row($(this).parents('tr')).data();
                                $('#inputH1Codhor').val(data.HorCodi)
                                $('#inputH1horario').val(data.HorDesc)
                                classEfect('#inputH1Codhor', 'fw5 border-info')
                                classEfect('#inputH1horario', 'fw5 border-info')
                                e.stopImmediatePropagation();
                            });
                        }, 300);

                    }, 300);
                    submitForm('#form', 'crud.php')
                });
                // $(".viewHor").click(function () { 
                //     let data = $(selector).DataTable().row($(this).parents('tr')).data();
                //     let HorCodi = JSON.stringify({ 'HorCodi': data.CodHor, 'nombre': '', 'legajo': '', 'tabla': 'Horario' });
                //     getHorario(HorCodi)
                // });
                $(".actModal").click(function () {
                    CheckSesion()
                    let data = $(selector).DataTable().row($(this).parents('tr')).data();
                    $('#actModal').modal('show')

                    $('#actModal .modal-title').html('Editar Asignación')
                    getHTML('bodyHorale1.html', '#actModalbody')

                    setTimeout(() => {
                        $('#H1Legajo').html('<label class="w60 fontq">Legajo:</label><span class="fw5">(' + data.Legajo + ') ' + data.ApNo + `</span>`)
                        $('#H1Fecha').html(`<label class="w60 fontq">Desde:</label><span class="fw5 ls1">` + data.Fecha + `</span>`)
                        $('#inputH1Codhor').val(data.CodHor)
                        $('#inputH1Codhor2').val(data.CodHor)
                        $('#inputH1Legajo').val(data.Legajo)
                        $('#inputTipo').val('u_horale1');
                        $('#inputH1Fecha').val(data.FechaStr)
                        $('#inputH1Codhor').mask('0000');
                        $('#inputH1horario').val(data.Horario)
                        // $('#tipo').val('u_horale1')

                        $('#divtableHorarios').html('')
                        $('#divtableHorarios').html(`
                                <table id="tableHorarios" class="table text-nowrap mt-2 w-100 border border-top-0 table-hover"></table>
                            `)
                        let datos = JSON.stringify({ 'nombre': '', 'legajo': '', 'tabla': 'ListHorarios' });
                        setTimeout(() => {
                            getListHorarios(datos, '#tableHorarios')

                            $("#tableHorarios tbody").on('click', '.select', function (e) {
                                e.preventDefault();
                                // $('tr').removeClass('table-active')
                                // $(this).parents('tr').addClass('table-active')
                                let data = $("#tableHorarios").DataTable().row($(this).parents('tr')).data();
                                // $("#H"+data.HorCodi+"H").parents('tr').addClass('table-active')
                                // console.log(data);
                                $('#inputH1Codhor').val(data.HorCodi)
                                $('#inputH1horario').val(data.HorDesc)
                                classEfect('#inputH1Codhor', 'fw5 border-info')
                                classEfect('#inputH1horario', 'fw5 border-info')
                                e.stopImmediatePropagation();

                            });

                        }, 300);

                    }, 300);
                    submitForm('#form', 'crud.php')
                });
                $(".horale1Delete").click(function () {
                    let data = $(selector).DataTable().row($(this).parents('tr')).data();
                    bootbox.confirm({
                        message: `<span class="fonth fw5">¿Eliminar asignación Desde?</span><br>
                        <div class="fontq mt-3">
                            <p class="p-0 m-0"><label class="w60 fontq">Legajo:</label><span class="fw5">(` + data.Legajo + `) ` + data.ApNo + `</span></p>
                            <p class="p-0 m-0"><label class="w60 fontq">Fecha:</label><span class="fw5">` + data.Fecha + `</span></p>
                            <p class="p-0 m-0"><label class="w60 fontq">Horario:</label><span class="fw5">(` + data.CodHor + `) ` + data.Horario + `</span></p>
                        </div>
                        `,
                        // message: '',
                        buttons: {
                            confirm: {
                                label: 'Aceptar',
                                className: 'btn-custom text-white btn-sm fontq submit'
                            },
                            cancel: {
                                label: 'Cancelar',
                                className: 'btn-light btn-sm fontq text-secondary'
                            }
                        },
                        callback: function (result) {
                            if (result) {
                                let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
                                $.ajax({
                                    type: "POST",
                                    url: "crud.php",
                                    'data': {
                                        NumLega: data.Legajo,
                                        Fecha: data.FechaStr,
                                        Codhor: data.CodHor,
                                        tipo: 'd_horale1'
                                    },
                                    beforeSend: function (data) {
                                        $.notifyClose();
                                        ActiveBTN(true, '.submit', 'Aguarde..', 'Aceptar')
                                        notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                                    },
                                    success: function (data) {
                                        if (data.status == "ok") {
                                            ActiveBTN(false, '.submit', 'Aguarde..', 'Aceptar')
                                            $.notifyClose();
                                            notify(data.Mensaje, 'success', 5000, 'right')
                                            ActualizaTablas()
                                        } else {
                                            ActiveBTN(false, '.submit', 'Aguarde..', 'Aceptar')
                                            $.notifyClose();
                                            notify(data.Mensaje, 'danger', 5000, 'right')
                                        }
                                    }
                                });
                            }
                        }
                    });
                });

            },
            createdRow: function (row, data, dataIndex) {
                $(row).addClass('animate__animated animate__fadeIn');
            },
            "ajax": {
                url: "getHorale.php",
                type: "POST",
                dataType: "json",
                "data": function (data) {
                    data.datos = datos;
                },
                error: function () {
                    $(selector).css("display", "none");
                }
            },

            columns: [
                {
                    className: 'align-middle', targets: 'Fecha', title: 'Desde',
                    "render": function (data, type, row, meta) {
                        let datacol = '<span title="Fecha Desde">' + row['Fecha'] + '</span>'
                        return datacol;
                    },
                },
                {
                    className: 'align-middle', targets: 'CodHor', title: '',
                    "render": function (data, type, row, meta) {
                        let datacol = '<span title="Código de Horario">' + row['CodHor'] + '</span>'
                        return datacol;
                    },
                },
                {
                    className: 'viewHor align-middle w-100', targets: 'Horario', title: 'Horario',
                    "render": function (data, type, row, meta) {
                        let datacol = `<span class="" title="Horario">` + row[`Horario`] + `</span>`
                        return datacol;
                    },
                },
                {
                    className: 'align-middle', targets: '', title: '',
                    "render": function (data, type, row, meta) {
                        let datos = JSON.stringify({ 'horario': row[`CodHor`], 'legajo': row[`Legajo`], 'fecha': row[`FechaStr`], 'tabla': 'Desde' });
                        // console.log(row);
                        let datacol = `
                        <div class="btn-group dropleft float-right">
                            <button type="button" class="btn btn-sm fontq text-secondary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu p-0 border-0" style="width:60px !important">
                                <button data="`+ datos + `" title="Editar asignación" class="actModal float-right btn btn-outline-custom border mx-1 btn-sm fontq"><i class="bi bi-pencil"></i></button>
                                <button  data="`+ datos + `" title="Eliminar asignación" class="horale1Delete float-right btn btn-outline-danger border btn-sm fontq"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                        `
                        return datacol;
                    },
                },
            ],
            deferRender: true,
            bProcessing: false,
            serverSide: false,
            paging: false,
            searching: false,
            info: false,
            ordering: false,
            responsive: false,
            language: {
                "url": "/" + _homehost + "/js/DataTableSpanishShort2.json"
            }
        });
    }
    function getHorale2(datos, selector) {
        $(selector).dataTable({
            initComplete: function (settings) {
                $(selector + " thead").remove()
            },
            drawCallback: function (settings) {
                $(selector + " thead").remove()
                let titletabla = '<div>Horarios Desde Hasta: <span class="ls1">(' + (settings.aiDisplay.length) + ')</span></div>';
                let btnAdd = `<button title="Nueva asignación" class="btn btn-sm btn-light px-2 pointer border c_horale2"><i class="bi bi-plus fonth"></i></button>`
                if (settings.aiDisplay.length == 0) {
                    let titletabla = '<div class="fw4">Sin Horario Desde Hasta asignado.</div>';
                    $('#titleDesdeHasta').removeClass('btn-custom')
                    $('#titleDesdeHasta').addClass('text-dark shadow-sm')
                    $('#titleDesdeHasta').html(titletabla + btnAdd)
                    $('.c_horale2').removeClass('btn-custom')
                    $('.c_horale2').addClass('btn-custom')
                    $(selector).hide()
                } else {
                    $(selector).show()
                    $('#titleDesdeHasta').removeClass('text-dark shadow-sm')
                    $('#titleDesdeHasta').addClass('btn-custom')
                    $('#titleDesdeHasta').html(titletabla + btnAdd)
                    $('.c_horale2').removeClass('btn-custom')
                    $('.c_horale2').addClass('btn-light')
                }
                $(".c_horale2").click(function () {
                    CheckSesion()
                    let data = settings.json.data[0];
                    $('#actModal').modal('show')

                    $('#actModal .modal-title').html('Nueva Asignación')
                    getHTML('bodyHorale1.html', '#actModalbody')

                    setTimeout(() => {
                        $('#H1Horario').prepend(`
                        <label class="fontq">Fecha desde / hasta:</label>
                        <div class="d-inline-flex align-items-center w-100 animate__animated animate__fadeInDown mb-2">
                            <input type="text" class="form-control text-center h40 w250" name="FDesdeHasta" id="inputH1FDesdeHasta">
                        </div>
                        `)
                        dobleDatePicker('#inputH1FDesdeHasta', 'right', 'down')
                        $('#H1Legajo').html('<label class="w60 fontq">Legajo:</label><span class="fw5">(' + $('.divTablas .NumLega').text() + ') ' + $('.divTablas .ApNo').text() + `</span>`)
                        $('#inputH1Legajo').val($('.divTablas .NumLega').text())
                        $('#inputH1Codhor').mask('0000');
                        $('#inputTipo').val('c_horale2');
                        $('#divtableHorarios').html('')
                        $('#divtableHorarios').html(`
                                <table id="tableHorarios" class="table text-nowrap mt-2 w-100 border border-top-0 table-hover"></table>
                            `)
                        let datos = JSON.stringify({ 'nombre': '', 'legajo': '', 'tabla': 'ListHorarios' });
                        setTimeout(() => {
                            getListHorarios(datos, '#tableHorarios')
                            $("#tableHorarios tbody").on('click', '.select', function (e) {
                                e.preventDefault();
                                let data = $("#tableHorarios").DataTable().row($(this).parents('tr')).data();
                                $('#inputH1Codhor').val(data.HorCodi)
                                $('#inputH1horario').val(data.HorDesc)
                                classEfect('#inputH1Codhor', 'fw5 border-info')
                                classEfect('#inputH1horario', 'fw5 border-info')
                                e.stopImmediatePropagation();
                            });
                        }, 300);

                    }, 300);
                });

                $(".actModal2").click(function () {
                    CheckSesion()
                    let data = $(selector).DataTable().row($(this).parents('tr')).data();
                    $('#actModal').modal('show')

                    $('#actModal .modal-title').html('Editar Asignación')
                    getHTML('bodyHorale1.html', '#actModalbody')

                    setTimeout(() => {
                        $('#H1Horario').prepend(`
                        <label class="fontq">Fecha Hasta:</label>
                        <div class="d-inline-flex align-items-center w-100 animate__animated animate__fadeInDown mb-2">
                            <input type="hidden" class="" name="FDesde" id="inputH1FDesde">
                            <input type="hidden" class="" name="FHasta2" id="inputH1FHasta2">
                            <input type="text" class="form-control text-center h40 w150" name="FHasta" id="inputH1FHasta">
                        </div>
                        `)
                        singleDatePicker('#inputH1FHasta', 'right', 'down')
                        $('#inputH1FHasta').data('daterangepicker').setStartDate(data.Ho2Fec2);
                        $('#inputH1FHasta').data('daterangepicker').setEndDate(data.Ho2Fec2);

                        $('#H1Legajo').html('<label class="w60 fontq">Legajo:</label><span class="fw5">(' + data.Legajo + ') ' + data.ApNo + `</span>`)
                        $('#H1Fecha').html(`<label class="w60 fontq">Desde:</label><span class="fw5 ls1">` + data.Ho2Fec1 + `</span>`)
                        $('#inputH1Codhor').val(data.Ho2Hora)
                        $('#inputH1Codhor2').val(data.Ho2Hora)
                        $('#inputH1Legajo').val(data.Legajo)
                        $('#inputH1FDesde').val(data.Ho2Fec1)
                        $('#inputH1FHasta2').val(data.Ho2Fec2)
                        $('#inputTipo').val('u_horale2');
                        $('#inputH1Fecha').val(data.FechaStr)
                        $('#inputH1Codhor').mask('0000');
                        $('#inputH1horario').val(data.HorDesc)
                        // $('#tipo').val('u_horale1')

                        $('#divtableHorarios').html('')
                        $('#divtableHorarios').html(`
                                <table id="tableHorarios" class="table text-nowrap mt-2 w-100 border border-top-0 table-hover"></table>
                            `)
                        let datos = JSON.stringify({ 'nombre': '', 'legajo': '', 'tabla': 'ListHorarios' });
                        setTimeout(() => {
                            getListHorarios(datos, '#tableHorarios')

                            $("#tableHorarios tbody").on('click', '.select', function (e) {
                                e.preventDefault();
                                // $('tr').removeClass('table-active')
                                // $(this).parents('tr').addClass('table-active')
                                let data = $("#tableHorarios").DataTable().row($(this).parents('tr')).data();
                                // $("#H"+data.HorCodi+"H").parents('tr').addClass('table-active')
                                // console.log(data);
                                $('#inputH1Codhor').val(data.HorCodi)
                                $('#inputH1horario').val(data.HorDesc)
                                classEfect('#inputH1Codhor', 'fw5 border-info')
                                classEfect('#inputH1horario', 'fw5 border-info')
                                e.stopImmediatePropagation();

                            });

                        }, 300);
                    }, 300);
                });
                submitForm('#form', 'crud.php')
                $(".horale2Delete").click(function () {
                    let data = $(selector).DataTable().row($(this).parents('tr')).data();
                    bootbox.confirm({
                        message: `<span class="fonth fw5">¿Eliminar asignación Desde Hasta?</span><br>
                        <div class="fontq mt-3">
                            <p class="p-0 m-0"><label class="w60 fontq">Legajo:</label><span class="fw5">(` + data.Legajo + `) ` + data.ApNo + `</span></p>
                            <p class="p-0 m-0"><label class="w60 fontq">Fecha:</label><span class="fw5">` + data.Ho2Fec1 + ` al ` + data.Ho2Fec2 + `</span></p>
                            <p class="p-0 m-0"><label class="w60 fontq">Horario:</label><span class="fw5">(` + data.Ho2Hora + `) ` + data.HorDesc + `</span></p>
                        </div>
                        `,
                        // message: '',
                        buttons: {
                            confirm: {
                                label: 'Aceptar',
                                className: 'btn-custom text-white btn-sm fontq submit'
                            },
                            cancel: {
                                label: 'Cancelar',
                                className: 'btn-light btn-sm fontq text-secondary'
                            }
                        },
                        callback: function (result) {
                            if (result) {
                                let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
                                $.ajax({
                                    type: "POST",
                                    url: "crud.php",
                                    'data': {
                                        NumLega: data.Legajo,
                                        FechaIni: data.Ho2Fec1,
                                        FechaFin: data.Ho2Fec2,
                                        Codhor: data.Ho2Hora,
                                        tipo: 'd_horale2'
                                    },
                                    beforeSend: function (data) {
                                        $.notifyClose();
                                        ActiveBTN(true, '.submit', 'Aguarde..', 'Aceptar')
                                        notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                                    },
                                    success: function (data) {
                                        if (data.status == "ok") {
                                            ActiveBTN(false, '.submit', 'Aguarde..', 'Aceptar')
                                            $.notifyClose();
                                            notify(data.Mensaje, 'success', 5000, 'right')
                                            ActualizaTablas()
                                        } else {
                                            ActiveBTN(false, '.submit', 'Aguarde..', 'Aceptar')
                                            $.notifyClose();
                                            notify(data.Mensaje, 'danger', 5000, 'right')
                                        }
                                    }
                                });
                            }
                        }
                    });
                });
            },
            createdRow: function (row, data, dataIndex) {
                $(row).addClass('animate__animated animate__fadeIn');
            },
            "ajax": {
                url: "getHorale.php",
                type: "POST",
                dataType: "json",
                "data": function (data) {
                    data.datos = datos;
                },
                error: function () {
                    $(selector).css("display", "none");
                }
            },

            columns: [
                {
                    className: 'align-middle', targets: 'Ho2Fec1', title: 'Desde',
                    "render": function (data, type, row, meta) {
                        let datacol = '<span title="Fecha Inicio">' + row['Ho2Fec1'] + '</span>'
                        return datacol;
                    },
                },
                {
                    className: 'align-middle', targets: 'Ho2Fec2', title: 'Hasta',
                    "render": function (data, type, row, meta) {
                        let datacol = '<span title="Fecha Fin">' + row['Ho2Fec2'] + '</span>'
                        return datacol;
                    },
                },
                {
                    className: 'align-middle', targets: 'Ho2Hora', title: '',
                    "render": function (data, type, row, meta) {
                        let datacol = '<span title="Código de Horario">' + row['Ho2Hora'] + '</span>'
                        return datacol;
                    },
                },
                {
                    className: 'align-middle w-100', targets: 'HorDesc', title: 'Horario',
                    "render": function (data, type, row, meta) {
                        let datacol = '<span title="Horario">' + row['HorDesc'] + '</span>'
                        return datacol;
                    },
                },
                {
                    className: 'align-middle', targets: '', title: '',
                    "render": function (data, type, row, meta) {
                        let datos = JSON.stringify({ 'horario': row[`CodHor`], 'legajo': row[`Legajo`], 'fecha': row[`FechaStr`], 'tabla': 'Desde' });
                        // console.log(row);
                        let datacol = `
                        <div class="btn-group dropleft float-right">
                            <button type="button" class="btn btn-sm fontq text-secondary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu p-0 border-0" style="width:60px !important">
                                <button data="`+ datos + `" title="Editar asignación" class="actModal2 float-right btn btn-outline-custom border mx-1 btn-sm fontq"><i class="bi bi-pencil"></i></button>
                                <button  data="`+ datos + `" title="Eliminar asignación" class="horale2Delete float-right btn btn-outline-danger border btn-sm fontq"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                        `
                        return datacol;
                    },
                },

            ],
            deferRender: true,
            bProcessing: false,
            serverSide: false,
            paging: false,
            searching: false,
            info: false,
            ordering: false,
            responsive: false,
            language: {
                "url": "/" + _homehost + "/js/DataTableSpanishShort2.json"
            }
        });
    }
    function getCitacion(datos, selector) {
        $(selector).dataTable({
            initComplete: function (settings) {

            },
            drawCallback: function (settings) {
                $(selector + " thead").remove()
                let titletabla = '<div>Citaciones: <span class="ls1">(' + (settings.aiDisplay.length) + ')</span></div>';
                let btnAdd = `<button title="Nueva citación" class="btn btn-sm btn-light px-2 pointer border c_citacion"><i class="bi bi-plus fonth"></i></button>`
                if (settings.aiDisplay.length == 0) {
                    let titletabla = '<div class="fw4">Sin Citaciones</div>';
                    $('#titleCitaciones').removeClass('btn-custom')
                    $('#titleCitaciones').addClass('text-dark shadow-sm')
                    $('#titleCitaciones').html(titletabla + btnAdd)
                    $('.c_citacion').removeClass('btn-custom')
                    $('.c_citacion').addClass('btn-custom')
                    $(selector).hide()
                } else {
                    $(selector).show()
                    $('#titleCitaciones').removeClass('text-dark shadow-sm')
                    $('#titleCitaciones').addClass('btn-custom')
                    $('#titleCitaciones').html(titletabla + btnAdd)
                    $('.c_citacion').removeClass('btn-custom')
                    $('.c_citacion').addClass('btn-light')
                }

                $(".c_citacion").click(function () {
                    CheckSesion()
                    $('#actModalCit .modal-title').html('Nueva Citación')
                    getHTML('bodyHorale1.html', '#actModalCitbody')
                    setTimeout(() => {
                        $('#actModalCitbody #divtableHorarios').remove()
                        $('#actModalCitbody #H1Horario label').remove()
                        $('#actModalCitbody #inputH1Codhor').remove()
                        $('#actModalCitbody #inputH1horario').remove()
                        $('#actModalCitbody #inputH1Fecha').remove()
                        // $('#H1Legajo').remove()
                        $('#actModalCitbody #H1Fecha').remove()
                        $('#actModalCitbody #H1Horario').prepend(`
                        <label class="fontq">Fecha:</label>
                        <div class="d-inline-flex align-items-center w-100 animate__animated animate__fadeIn mb-2">
                            <input type="text" class="form-control text-center h40 w150" name="Fecha" id="Fecha">
                            <input type="hidden" class="form-control text-center h40 w150" name="alta_Citación" id="alta_Citación" value="true">
                        </div>
                        <label class="fontq mt-2">Entra / Sale / Descanso</label>
                        <div class="d-inline-flex align-items-center w-100 animate__animated animate__fadeIn">
                            <input type="tel" class="form-control text-center h40 w100 HoraMask" name="CitEntra" id="CitEntra" placeholder="00:00" autocomplete=off >
                            <input type="tel" class="mx-1 form-control text-center h40 w100 HoraMask" name="CitSale" id="CitSale" placeholder="00:00" autocomplete=off >
                            <input type="tel" class="form-control text-center h40 w100 HoraMask" name="CitDesc" id="CitDesc" value="00:00" autocomplete=off >
                        </div>
                        <input type="hidden" class="" name="datos_Citacion" id="datos_Citacion">
                        `)
                        singleDatePicker('#Fecha', 'right', 'down')
                        $('#actModalCitbody #H1Legajo').html('<label class="w60 fontq">Legajo:</label><span class="fw5">(' + $('.divTablas .NumLega').text() + ') ' + $('.divTablas .ApNo').text() + `</span>`)
                        $('#actModalCitbody #inputH1Legajo').val($('.divTablas .NumLega').text())
                        $('#actModalCitbody #inputTipo').val('c_citacion');
                        $('#actModalCitbody #datos_Citacion').val($('.divTablas .NumLega').text() + '-' + $("#Fecha").val())
                        $("#actModalCitbody #Fecha").change(function () {
                            $('#actModalCitbody #datos_Citacion').val($('.divTablas .NumLega').text() + '-' + $("#Fecha").val())
                        });
                        $('#actModalCitbody .HoraMask').mask(maskBehavior, spOptions);
                        $('#actModalCit').modal('show')
                    }, 200);
                    submitFormCit()
                });
                $(".actModalCit").click(function () {
                    CheckSesion()
                    let data = $(selector).DataTable().row($(this).parents('tr')).data();
                    $('#actModalCit .modal-title').html('Editar Citación')

                    getHTML('bodyHorale1.html', '#actModalCitbody')
                    setTimeout(() => {
                        $('#actModalCitbody #divtableHorarios').remove()
                        $('#actModalCitbody #H1Horario label').remove()
                        $('#actModalCitbody #inputH1Codhor').remove()
                        $('#actModalCitbody #inputH1horario').remove()
                        $('#actModalCitbody #inputH1Fecha').remove()
                        // $('#H1Legajo').remove()
                        $('#actModalCitbody #H1Fecha').remove()
                        $('#actModalCitbody #H1Horario').prepend(`
                                <label class="fontq">Fecha:</label>
                                <div class="d-inline-flex align-items-center w-100 animate__animated animate__fadeIn mb-2">
                                    <input type="text" class="form-control text-center h40 w150" name="Fecha" id="Fecha">
                                    <input type="hidden" class="form-control text-center h40 w150" name="alta_Citación" id="alta_Citación" value="true">
                                </div>
                                <label class="fontq mt-2">Entra / Sale / Descanso</label>
                                <div class="d-inline-flex align-items-center w-100 animate__animated animate__fadeIn">
                                    <input type="tel" class="form-control text-center h40 w100 HoraMask" name="CitEntra" id="CitEntra" placeholder="00:00" autocomplete=off>
                                    <input type="tel" class="mx-1 form-control text-center h40 w100 HoraMask" name="CitSale" id="CitSale" placeholder="00:00" autocomplete=off>
                                    <input type="tel" class="form-control text-center h40 w100 HoraMask" name="CitDesc" id="CitDesc" value="00:00" autocomplete=off>
                                </div>
                                <input type="hidden" class="" name="datos_Citacion" id="datos_Citacion">
                                `)
                        singleDatePicker('#Fecha', 'right', 'down')
                        // $('#actModalCitbody #Fecha').data('daterangepicker').setStartDate(data.CitFech);
                        $('#Fecha').data('daterangepicker').setStartDate(data.CitFech);
                        $('#Fecha').data('daterangepicker').setEndDate(data.CitFech);

                        $('#actModalCitbody #CitEntra').val(data.CitEntra)
                        $('#actModalCitbody #CitSale').val(data.CitSale)
                        $('#actModalCitbody #CitDesc').val(data.CitDesc)
                        $('#actModalCitbody #H1Legajo').html('<label class="w60 fontq">Legajo:</label><span class="fw5">(' + $('.divTablas .NumLega').text() + ') ' + $('.divTablas .ApNo').text() + `</span>`)
                        $('#actModalCitbody #inputH1Legajo').val($('.divTablas .NumLega').text())
                        $('#actModalCitbody #inputTipo').val('c_citacion');
                        $('#actModalCitbody #datos_Citacion').val($('.divTablas .NumLega').text() + '-' + $("#Fecha").val())
                        $("#actModalCitbody #Fecha").change(function () {
                            $('#actModalCitbody #datos_Citacion').val($('.divTablas .NumLega').text() + '-' + $("#Fecha").val())
                        });
                        $('#actModalCitbody .HoraMask').mask(maskBehavior, spOptions);
                        $('#actModalCit').modal('show')
                    }, 200);
                    submitFormCit()
                });
                $(".CitDelete").click(function () {
                    let data = $(selector).DataTable().row($(this).parents('tr')).data();
                    // console.table(data);
                    bootbox.confirm({
                        message: `<span class="fonth fw5">¿Eliminar citación?</span><br>
                        <div class="fontq mt-3">
                            <p class="p-0 m-0"><label class="w60 fontq">Legajo:</label><span class="fw5">(` + data.CitLega + `) ` + data.ApNo + `</span></p>
                            <p class="p-0 m-0"><label class="w60 fontq">Fecha:</label><span class="fw5">` + data.CitFech + `</span></p>
                            <p class="p-0 m-0"><label class="w60 fontq">Citación: </label><span class="fw5">` + data.CitEntra + ` a ` + data.CitSale + `</span></p>
                        </div>
                        `,
                        // message: '',
                        buttons: {
                            confirm: {
                                label: 'Aceptar',
                                className: 'btn-custom text-white btn-sm fontq submit'
                            },
                            cancel: {
                                label: 'Cancelar',
                                className: 'btn-light btn-sm fontq text-secondary'
                            }
                        },
                        callback: function (result) {
                            if (result) {
                                let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
                                $.ajax({
                                    type: "POST",
                                    url: '../../general/insert.php',
                                    'data': {
                                        NumLega: data.CitLega,
                                        Fecha: data.CitFech,
                                        tipo: 'd_citacion',
                                        Datos: data.CitLega + '-' + data.CitFech,
                                        baja_Cit: 'true'
                                    },
                                    beforeSend: function (data) {
                                        $.notifyClose();
                                        ActiveBTN(true, '.submit', 'Aguarde..', 'Aceptar')
                                        notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                                    },
                                    success: function (data) {
                                        if (data.status == "ok") {
                                            ActiveBTN(false, '.submit', 'Aguarde..', 'Aceptar')
                                            $.notifyClose();
                                            notify(data.Mensaje, 'success', 5000, 'right')
                                            $("#Citacion").DataTable().ajax.reload(null, false)
                                            $('#actModalCit').modal('hide')
                                        } else {
                                            ActiveBTN(false, '.submit', 'Aguarde..', 'Aceptar')
                                            $.notifyClose();
                                            notify(data.Mensaje, 'danger', 5000, 'right')
                                        }
                                    }
                                });
                            }
                        }
                    });
                });

            },
            createdRow: function (row, data, dataIndex) {
                $(row).addClass('animate__animated animate__fadeIn');
            },
            "ajax": {
                url: "getHorale.php",
                type: "POST",
                dataType: "json",
                "data": function (data) {
                    data.datos = datos;
                },
                error: function () {
                    $(selector).css("display", "none");
                }
            },

            columns: [
                {
                    className: 'align-middle', targets: 'CitFech', title: 'Fecha',
                    "render": function (data, type, row, meta) {
                        let datacol = '<span title="Fecha Citación">' + row['CitFech'] + '</span>'
                        return datacol;
                    },
                },
                {
                    className: 'align-middle text-nowrap', targets: 'CitEntra', title: 'Citación',
                    "render": function (data, type, row, meta) {
                        let datacol = '<span title="Horario de Citación">' + row['CitEntra'] + ' a ' + row['CitSale'] + '</span>'
                        return datacol;
                    },
                },
                {
                    className: 'align-middle w-100', targets: 'CitDesc', title: 'Descanso',
                    "render": function (data, type, row, meta) {
                        let datacol = '<span title="Descanso de Citación">' + row['CitDesc'] + '</span>'
                        return datacol;
                    },
                },
                {
                    className: 'align-middle', targets: '', title: '',
                    "render": function (data, type, row, meta) {
                        let datos = JSON.stringify({ 'horario': row[`CodHor`], 'legajo': row[`Legajo`], 'fecha': row[`FechaStr`], 'tabla': 'Desde' });
                        // console.log(row);
                        let datacol = `
                        <div class="btn-group dropleft float-right">
                            <button type="button" class="btn btn-sm fontq text-secondary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu p-0 border-0" style="width:60px !important">
                                <button data="`+ datos + `" title="Editar citación" class="actModalCit float-right btn btn-outline-custom border mx-1 btn-sm fontq"><i class="bi bi-pencil"></i></button>
                                <button  data="`+ datos + `" title="Eliminar citación" class="CitDelete float-right btn btn-outline-danger border btn-sm fontq"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                        `
                        return datacol;
                    },
                },

            ],
            deferRender: true,
            bProcessing: false,
            serverSide: false,
            paging: false,
            searching: false,
            info: false,
            ordering: false,
            responsive: false,
            language: {
                "url": "/" + _homehost + "/js/DataTableSpanishShort2.json"
            }
        });
    }
    function getRotaDeta(RoLRota, fechar, dia, RotDesc) {
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "getHorale.php",
            'data': {
                datos: RoLRota
            },
            beforeSend: function (data) {

            },
            success: function (data) {
                let rotacion = RotDesc
                let comenzando = dia
                let fecha = fechar

                $('.RotaDeta').html(`
                <div class="toast-header d-flex justify-content-between animate__animated animate__fadeIn py-2 text-dark">
                    <div class'w-100 fw5'>
                        <div class="mr-auto fw5 fonth animate__animated animate__fadeIn">`+ rotacion + `</div>
                        <div class="fontq text-dark">Desde el día ` + fecha + `</div>
                        <div class="fontq text-dark">Comenzando el día ` + comenzando + ` de la rotación</div>
                    </div>
                    <div>
                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                            <span aria-hidden="true"> <i class="bi bi-x"></i> </span>
                        </button>
                    </div>
                </div>
                <div class="toast-body animate__animated animate__fadeIn"></div>
                `)
                $('.RotaDeta').toast('show')
                $.each(data, function (key, value) {
                    let index = key + 1
                    $('.RotaDeta .toast-body').append(`
                        <div class="fontq mb-1">
                            <span class="fw5">`+ index + `. Horario. (` + value.RotHora + `) ` + value.HorDesc + `</span><br>
                            <span class="">Durante `+ value.RotDias + ` días.</span><br>
                        </div>
                    `)
                });
            }
        })
    }
    function getHorario(datos) {
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "getHorale.php",
            'data': {
                datos: datos
            },
            beforeSend: function (data) {

            },
            success: function (data) {

                // console.log(HoraMin(data.HorLuDe));
                function percentHoraUno(uno) {
                    let TotDia = 1440
                    let parteUno = HoraMin(uno)
                    let porcentajeUno = ((parteUno / TotDia) * 100)
                    return (porcentajeUno).toFixed(1)
                }
                function percentHoraDos(Uno, Dos) {
                    let TotDia = 1440
                    let parteUno = HoraMin(Uno)
                    let parteDos = (HoraMin(Dos) - parteUno)
                    let porcentajeDos = ((parteDos / TotDia) * 100)
                    return (porcentajeDos).toFixed(1)
                }
                function percentHoraTres(Dos) {
                    let TotDia = 1440
                    let parteTres = (TotDia - HoraMin(Dos))
                    let porcentajeTres = ((parteTres / TotDia) * 100)
                    return (porcentajeTres).toFixed(1)
                }


                console.log(percentHoraUno(data.HorLuDe));
                console.log(percentHoraDos(data.HorLuDe, data.HorLuHa));
                console.log(percentHoraTres(data.HorLuHa));

                getHTML('grillaHorario.html', '#divGrillaHorario');

                function progressBar(uno, dos, tres) {
                    let unos = percentHoraUno(uno)
                    let doss = percentHoraDos(uno, dos)
                    let tress = percentHoraTres(tres)
                    return `
                    <div class="progress fontq shadow-sm" style="height: 25px;">
                        <div class="progress-bar bg-light" role="progressbar" style="width: `+ unos + `%" aria-valuenow="` + unos + `" aria-valuemin="0" aria-valuemax="100"></div>
                        <div class="progress-bar btn-custom opa8" role="progressbar" style="width: `+ doss + `%" aria-valuenow="` + doss + `" aria-valuemin="` + doss + `" aria-valuemax="100">` + uno + ` a ` + dos + `</div>
                        <div class="progress-bar bg-light" role="progressbar" style="width: `+ tress + `%" aria-valuenow="` + tress + `" aria-valuemin="` + tress + `" aria-valuemax="100">
                        </div>
                    </div>
                    `
                }

                setTimeout(() => {
                    if (data.HorLune) {
                        $('#checkLunes').prop('checked', true)
                        $('#ProgressLunes').html(progressBar(data.HorLuDe, data.HorLuHa, data.HorLuHa))
                    }
                    $('#HorLuHa').html(data.HorLuHa)
                    $('#HorLuDe').html(data.HorLuDe)
                    $('#HorLuHs').html(data.HorLuHs)

                    $('#HorMaHa').html(data.HorMaHa)
                    $('#HorMaDe').html(data.HorMaDe)
                    $('#HorMaHs').html(data.HorMaHs)
                    $('#ProgressMartes').html(progressBar(data.HorMaDe, data.HorMaHa, data.HorMaHa))
                    $('#HorMiHa').html(data.HorMiHa)
                    $('#HorMiDe').html(data.HorMiDe)
                    $('#HorMiHs').html(data.HorMiHs)
                    $('#ProgressMiercoles').html(progressBar(data.HorMiDe, data.HorMiHa, data.HorMiHa))
                    $('#HorJuHa').html(data.HorJuHa)
                    $('#HorJuDe').html(data.HorJuDe)
                    $('#HorJuHs').html(data.HorJuHs)
                    $('#ProgressJueves').html(progressBar(data.HorJuDe, data.HorJuHa, data.HorJuHa))
                    $('#HorViHa').html(data.HorViHa)
                    $('#HorViDe').html(data.HorViDe)
                    $('#HorViHs').html(data.HorViHs)
                    $('#ProgressViernes').html(progressBar(data.HorViDe, data.HorViHa, data.HorViHa))
                }, 500);



                // $('.RotaDeta').html(`
                // <div class="toast-header d-flex justify-content-between animate__animated animate__fadeIn py-2 text-dark">
                //     <div class'w-100 fw5'>
                //         <div class="mr-auto fw5 fonth animate__animated animate__fadeIn">`+ rotacion + `</div>
                //         <div class="fontq text-dark">Desde el día ` + fecha + `</div>
                //         <div class="fontq text-dark">Comenzando el día ` + comenzando + ` de la rotación</div>
                //     </div>
                //     <div>
                //         <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                //             <span aria-hidden="true"> <i class="bi bi-x"></i> </span>
                //         </button>
                //     </div>
                // </div>
                // <div class="toast-body animate__animated animate__fadeIn"></div>
                // `)
                // $('.RotaDeta').toast('show')
                // $.each(data, function (key, value) {
                //     let index = key+1
                //     $('.RotaDeta .toast-body').append(`
                //         <div class="fontq mb-1">
                //             <span class="fw5">`+index+`. Horario. (` + value.RotHora + `) ` + value.HorDesc + `</span><br>
                //             <span class="">Durante `+ value.RotDias + ` días.</span><br>
                //         </div>
                //     `)
                // });
            }
        })
    }
    function getRotacion(datos, selector) {
        $(selector).dataTable({
            initComplete: function (settings) {
                $(selector + " thead").remove()
            },
            drawCallback: function (settings) {
                $(selector + " thead").remove()
                let titletabla = '<div>Rotaciones: <span class="ls1">(' + (settings.aiDisplay.length) + ')</span></div>';
                let btnAdd = `<button title="Nueva asignación" class="btn btn-sm btn-light px-2 pointer border c_rotacion"><i class="bi bi-plus fonth"></i></button>`
                if (settings.aiDisplay.length == 0) {
                    let titletabla = '<div class="fw4">Sin Rotación asignada</div>';
                    $('#titleRotaciones').removeClass('btn-custom')
                    $('#titleRotaciones').addClass('text-dark shadow-sm')
                    $('#titleRotaciones').html(titletabla + btnAdd)
                    $('.c_rotacion').removeClass('btn-custom')
                    $('.c_rotacion').addClass('btn-custom')
                    $(selector).hide()
                } else {
                    $(selector).show()
                    $('#titleRotaciones').removeClass('text-dark shadow-sm')
                    $('#titleRotaciones').addClass('btn-custom')
                    $('#titleRotaciones').html(titletabla + btnAdd)
                    $('.c_rotacion').removeClass('btn-custom')
                    $('.c_rotacion').addClass('btn-light')
                }
                $(".viewRot").click(function () {
                    let data = $(selector).DataTable().row($(this).parents('tr')).data();
                    // console.log(data);
                    let RoLRota = JSON.stringify({ 'RoLRota': data.RoLRota, 'nombre': '', 'legajo': '', 'tabla': 'RotaDeta' });
                    getRotaDeta(RoLRota, data.RoLFech, data.RoLDias, data.RotDesc)
                });
                $(".c_rotacion").click(function () {
                    $('.RotaDeta').toast('hide')
                    CheckSesion()
                    // let data = settings.json.data[0];
                    $('#actModal').modal('show')

                    $('#actModal .modal-title').html('Nueva Asignación de Rotación')
                    getHTML('bodyHorale1.html', '#actModalbody')
                    setTimeout(() => {
                        $('#H1Horario label').html('Rotación')
                        $('#H1Horario').prepend(`
                        <div class="d-inline-flex align-items-center w-100 animate__animated animate__fadeInDown mb-2">
                        <label class="fontq w80">Fecha:</label>
                            <input type="text" class="form-control text-center h40 w120 ml-2" name="RotFecha" id="inputRotFecha">
                        </div>
                        <div class="d-inline-flex align-items-center w-100 animate__animated animate__fadeInDown mb-2">
                        <label class="fontq w80">Día de Comienzo:</label>
                            <input type="tel" class="form-control text-center h40 w120 ml-2" name="RotDia" id="inputRotDia" value='1'>
                        </div>
                        `)
                        singleDatePicker('#inputRotFecha', 'right', 'down')
                        $('#H1Legajo').html('<label class="w60 fontq">Legajo:</label><span class="fw5">(' + $('.divTablas .NumLega').text() + ') ' + $('.divTablas .ApNo').text() + `</span>`)
                        $('#inputH1Legajo').val($('.divTablas .NumLega').text())
                        $('#inputH1Codhor').mask('0000');
                        $('#inputRotDia').mask('000');
                        $('#inputTipo').val('c_rotacion');
                        $('#divtableHorarios').html('')
                        $('#divtableHorarios').html(`
                                <table id="tableHorarios" class="table text-nowrap mt-2 w-100 border border-top-0 table-hover"></table>
                            `)
                        let datos = JSON.stringify({ 'nombre': '', 'legajo': '', 'tabla': 'ListRotaciones' });
                        setTimeout(() => {
                            getListRotaciones(datos, '#tableHorarios')
                            $("#tableHorarios tbody").on('click', '.select', function (e) {
                                e.preventDefault();
                                let data = $("#tableHorarios").DataTable().row($(this).parents('tr')).data();
                                $('#inputH1Codhor').val(data.RotCodi)
                                $('#inputH1horario').val(data.RotDesc)
                                classEfect('#inputH1Codhor', 'fw5 border-info')
                                classEfect('#inputH1horario', 'fw5 border-info')
                                e.stopImmediatePropagation();
                            });
                        }, 300);

                    }, 300);
                });
                $(".actModalRot").click(function () {
                    CheckSesion()
                    $('.RotaDeta').toast('hide')
                    let data = $(selector).DataTable().row($(this).parents('tr')).data();
                    // console.log(data);
                    $('#actModal').modal('show')

                    $('#actModal .modal-title').html('Editar Asignación de Rotación')
                    getHTML('bodyHorale1.html', '#actModalbody')
                    setTimeout(() => {
                        $('#H1Horario label').html('Rotación')
                        $('#H1Horario').prepend(`
                        <input type="hidden" readonly class="form-control text-center h40 w120 ml-2" name="RotFecha" id="inputRotFecha">
                        <div class="d-inline-flex align-items-center w-100 animate__animated animate__fadeInDown mb-2">
                        <label class="fontq w80">Día de Comienzo:</label>
                            <input type="tel" class="form-control text-center h40 w120 ml-2" name="RotDia" id="inputRotDia" value='1'>
                        </div>
                        `)
                        $('#inputRotFecha').val(data.RoLFech);
                        $('#H1Legajo').html('<label class="w60 fontq">Legajo:</label><span class="fw5">(' + data.RoLLega + ') ' + data.ApNo + `</span>`)
                        $('#H1Fecha').html(`<label class="w60 fontq">Desde:</label><span class="fw5 ls1">` + data.RoLFech + `</span>`)
                        $('#inputH1Codhor').val(data.RoLRota)
                        $('#inputH1Codhor2').val(data.RoLRota)
                        $('#inputH1Legajo').val(data.RoLLega)
                        $('#inputRotDia').val(data.RoLDias)
                        $('#inputTipo').val('u_rotacion');
                        $('#inputH1Codhor').mask('0000');
                        $('#inputH1horario').val(data.RotDesc)

                        $('#divtableHorarios').html('')
                        $('#divtableHorarios').html(`
                                <table id="tableHorarios" class="table text-nowrap mt-2 w-100 border border-top-0 table-hover"></table>
                            `)
                        let datos = JSON.stringify({ 'nombre': '', 'legajo': '', 'tabla': 'ListRotaciones' });
                        setTimeout(() => {
                            getListRotaciones(datos, '#tableHorarios')
                            $("#tableHorarios tbody").on('click', '.select', function (e) {
                                e.preventDefault();
                                let data = $("#tableHorarios").DataTable().row($(this).parents('tr')).data();
                                $('#inputH1Codhor').val(data.RotCodi)
                                $('#inputH1horario').val(data.RotDesc)
                                classEfect('#inputH1Codhor', 'fw5 border-info')
                                classEfect('#inputH1horario', 'fw5 border-info')
                                e.stopImmediatePropagation();
                            });
                        }, 300);
                    }, 300);
                });

                $(".RotDelete").click(function () {
                    $('.RotaDeta').toast('hide')
                    let data = $(selector).DataTable().row($(this).parents('tr')).data();
                    // console.table(data);
                    bootbox.confirm({
                        message: `<span class="fonth fw5">¿Eliminar asignación de Rotación?</span><br>
                        <div class="fontq mt-3">
                            <p class="p-0 m-0"><label class="w60 fontq">Legajo:</label><span class="fw5">(` + data.RoLLega + `) ` + data.ApNo + `</span></p>
                            <p class="p-0 m-0"><label class="w60 fontq">Fecha:</label><span class="fw5">` + data.RoLFech + `</span></p>
                            <p class="p-0 m-0"><label class="w60 fontq">Horario:</label><span class="fw5">(` + data.RoLRota + `) ` + data.RotDesc + `</span></p>
                        </div>
                        `,
                        // message: '',
                        buttons: {
                            confirm: {
                                label: 'Aceptar',
                                className: 'btn-custom text-white btn-sm fontq submit'
                            },
                            cancel: {
                                label: 'Cancelar',
                                className: 'btn-light btn-sm fontq text-secondary'
                            }
                        },
                        callback: function (result) {
                            if (result) {
                                let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
                                $.ajax({
                                    type: "POST",
                                    url: "crud.php",
                                    'data': {
                                        NumLega: data.RoLLega,
                                        Fecha: data.RoLFech,
                                        Codhor: data.RoLRota,
                                        tipo: 'd_rotacion'
                                    },
                                    beforeSend: function (data) {
                                        $.notifyClose();
                                        ActiveBTN(true, '.submit', 'Aguarde..', 'Aceptar')
                                        notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                                    },
                                    success: function (data) {
                                        if (data.status == "ok") {
                                            ActiveBTN(false, '.submit', 'Aguarde..', 'Aceptar')
                                            $.notifyClose();
                                            notify(data.Mensaje, 'success', 5000, 'right')
                                            ActualizaTablas()
                                        } else {
                                            ActiveBTN(false, '.submit', 'Aguarde..', 'Aceptar')
                                            $.notifyClose();
                                            notify(data.Mensaje, 'danger', 5000, 'right')
                                        }
                                    }
                                });
                            }
                        }
                    });
                });
                submitForm('#form', 'crud.php')
            },
            createdRow: function (row, data, dataIndex) {
                $(row).addClass('animate__animated animate__fadeIn');
            },
            "ajax": {
                url: "getHorale.php",
                type: "POST",
                dataType: "json",
                "data": function (data) {
                    data.datos = datos;
                },
                error: function () {
                    $(selector).css("display", "none");
                }
            },

            columns: [
                {
                    className: 'align-middle', targets: 'RoLFech', title: 'Fecha',
                    "render": function (data, type, row, meta) {
                        let datacol = row['RoLFech']
                        return datacol;
                    },
                },
                {
                    className: 'align-middle', targets: 'RoLRota', title: 'Fecha',
                    "render": function (data, type, row, meta) {
                        let datacol = row['RoLRota']
                        return datacol;
                    },
                },
                {
                    className: 'viewRot pointer w-100 align-middle', targets: 'RotDesc', title: '',
                    "render": function (data, type, row, meta) {
                        let datacol = '<span title="Rotación">' + row['RotDesc'] + '</span>'
                        return datacol;
                    },
                },
                {
                    className: 'align-middle', targets: '', title: '',
                    "render": function (data, type, row, meta) {
                        let datos = JSON.stringify({ 'horario': row[`CodHor`], 'legajo': row[`Legajo`], 'fecha': row[`FechaStr`], 'tabla': 'Desde' });
                        // console.log(row);
                        let datacol = `
                        <div class="btn-group dropleft float-right">
                            <button type="button" class="btn btn-sm fontq text-secondary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu p-0 border-0" style="width:60px !important">
                                <button data="`+ datos + `" title="Editar Rotación" class="actModalRot float-right btn btn-outline-custom border mx-1 btn-sm fontq"><i class="bi bi-pencil"></i></button>
                                <button  data="`+ datos + `" title="Eliminar Rotación" class="RotDelete float-right btn btn-outline-danger border btn-sm fontq"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                        `
                        return datacol;
                    },
                },

            ],
            deferRender: true,
            bProcessing: false,
            serverSide: false,
            paging: false,
            searching: false,
            info: false,
            ordering: false,
            responsive: false,
            language: {
                "url": "/" + _homehost + "/js/DataTableSpanishShort2.json"
            }
        });
    }

    $("#tablePersonal tbody").on('click', '.view', function (e) {
        e.preventDefault();
        CheckSesion()
        getHTML('actModal.html', '#divactModal')
        getHTML('actModalCit.html', '#divactModalCit')
        $('tr').removeClass('table-active')
        $(this).parents('tr').addClass('table-active')
        let data = $("#tablePersonal").DataTable().row($(this).parents('tr')).data();
        // console.log(data);
        $("#detalleHorario").hide()
        $("#detalleHorario").html(`
        <div class="p-2 divTablas">
            <div class="shadow-sm">
                <div class="p-2 btn-custom fontq d-inline-flex w-100 justify-content-between align-items-center" id="titleDesde">Horarios Desde</div>
                <div class="overflow-auto w-100 table-responsive mb-2" style="max-height:150px">
                    <table class="table fonth w-100 text-wrap" id="Horale1"></table>
                </div>
            </div>
            <div class="shadow-sm">
                <div class="p-2 border-top-0 btn-custom fontq d-inline-flex w-100 justify-content-between align-items-center" id="titleDesdeHasta">Horarios Desde Hasta</div><div class="overflow-auto w-100 table-responsive mb-2" style="max-height:150px">
                    <table class="table fonth text-wrap w-100" id="Horale2"></table>
                </div>
            </div>
            <div class="shadow-sm">
                <div class="p-2 border-top-0 btn-custom fontq d-inline-flex w-100 justify-content-between align-items-center" id="titleRotaciones">Rotaciones</div><div class="overflow-auto w-100 table-responsive mb-2" style="max-height:150px">
                    <table class="table fonth text-wrap w-100" id="Rotacion"></table>
                </div>
            </div>
            <div class="toast RotaDeta border-0" role="alert" aria-live="polite" data-autohide="false" aria-atomic="true">
                
            </div>
        </div>`)

        $(".divTablas").prepend(`<div class="">
            <p class="fontq m-0 p-0"><label class="w60 fontq mb-1">Legajo: </label><span class="fw5 NumLega">` + data.pers_legajo + `</span></p><p class="fontq m-0 p-0"><label class="w60 fontq mb-0">Nombre: </label><span class="fw5 ApNo">` + data.pers_nombre + `</span><button class="btn btn-custom border btn-sm fontq cita float-right px-4 mb-2">Citaciones</button></p>
        </div>`)
        let Horale1 = JSON.stringify({ 'nombre': data.pers_nombre, 'legajo': data.pers_legajo, 'tabla': 'Desde' });
        let Horale2 = JSON.stringify({ 'nombre': data.pers_nombre, 'legajo': data.pers_legajo, 'tabla': 'DesdeHasta' });
        let Citacion = JSON.stringify({ 'nombre': data.pers_nombre, 'legajo': data.pers_legajo, 'tabla': 'Citacion' });
        let Rotacion = JSON.stringify({ 'nombre': data.pers_nombre, 'legajo': data.pers_legajo, 'tabla': 'Rotacion' });
        getHorale1(Horale1, '#Horale1')
        getHorale2(Horale2, '#Horale2')
        getRotacion(Rotacion, '#Rotacion')
        $(".cita").click(function () {
            CheckSesion()
            $('#divCitaciones').remove()
            $('.divTablas').append(`
            <div class="shadow-sm" id="divCitaciones" style="display:none">
                <div class="p-2 border-top-0 btn-custom fontq d-inline-flex w-100 justify-content-between align-items-center" id="titleCitaciones">Citaciones</div><div class="overflow-auto w-100 table-responsive" style="max-height:150px">
                    <table class="table fonth text-wrap w-100" id="Citacion"></table>
                </div>
            </div>
            `)
            getCitacion(Citacion, '#Citacion')
            setTimeout(() => {

                $('#divCitaciones').show()
            }, 200);
        });

        setTimeout(() => {
            $("#detalleHorario").show()
        }, 200);
    });

    function submitForm(selector, url) {
        CheckSesion()
        var loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
        $(selector).bind("submit", function (e) {
            e.preventDefault();
            $.ajax({
                type: $(this).attr("method"),
                url: url,
                data: $(this).serialize(),
                cache: false,
                beforeSend: function (data) {
                    $.notifyClose();
                    ActiveBTN(true, '#submit', 'Aguarde..', 'Aceptar')
                    notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        ActiveBTN(false, '#submit', 'Aguarde..', 'Aceptar')
                        $.notifyClose();
                        notify(data.Mensaje, 'success', 5000, 'right')
                        ActualizaTablas()
                        $('#actModal').modal('hide')
                    } else {
                        ActiveBTN(false, '#submit', 'Aguarde..', 'Aceptar')
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                    }
                }
            });
            e.stopImmediatePropagation();
        });
    }
    function submitFormCit() {
        $('#formCit').bind("submit", function (e) {
            var loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
            e.preventDefault();
            $.ajax({
                type: $(this).attr("method"),
                url: '../../general/insert.php',
                data: $(this).serialize(),
                cache: false,
                beforeSend: function (data) {
                    CheckSesion()
                    $.notifyClose();
                    ActiveBTN(true, '#submit', 'Aguarde..', 'Aceptar')
                    notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        ActiveBTN(false, '#submit', 'Aguarde..', 'Aceptar')
                        $.notifyClose();
                        notify(data.Mensaje, 'success', 5000, 'right')
                        $("#Citacion").DataTable().ajax.reload(null, false)
                        $('#actModalCit').modal('hide')
                    } else {
                        ActiveBTN(false, '#submit', 'Aguarde..', 'Aceptar')
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                    }
                }
            });
            e.stopImmediatePropagation();
        });
    }
});
