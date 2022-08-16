
$(function () {

    Array.prototype.unique = function (a) {
        return function () { return this.filter(a) }
    }(function (a, b, c) {
        return c.indexOf(a, b + 1) < 0
    });

    let modulos = $('#modulos').DataTable({
        initComplete: function (settings, json) {
            console.log(json.UniqueMod);
            $.each(json.UniqueMod, function (key, value2) {
                if (value2.id > 0) {
                    $('#TipoMod').prepend('<option value="' + value2.id + '">' + value2.tipo + '</option>');
                }
            });
        },
        drawCallback: function (settings) {
            $.each(settings.json, function (key, value) {
                (value.length > 0) ? $('#divModulos').show() : '';
                $('#aguarde').hide()
            });
            // }
            $('td').removeClass('text-light')
            $('.dataTable').removeClass('opa5')
            $('.form-control-sm').attr('placeholder', 'Buscar')
            $('#NombreMod').attr('placeholder', 'Descripción')
            $('#OrdenMod').attr('placeholder', 'Orden')
        },
        columnDefs: [
            { "visible": false, "targets": 2, "type": "html" }
        ],
        orderFixed: [[2, "desc"], [3, "asc"]],
        rowGroup: {
            dataSrc: ['tipo'],
            endRender: null,
            startRender: function (rows, group) {
                var TextRegistro = (rows.count() == '1') ? ' Módulo' : ' Módulos';
                return group + ' <span class="fontp text-secondary">( ' + rows.count() + TextRegistro + ' )</span>';
            },
        },
        // iDisplayLength: -1,
        bProcessing: true,
        ajax: {
            url: "GetMods.php",
            type: "POST",
            dataSrc: "modulos",
            "data": function (data) {
                // data._dr = $("#_dr").val();
            },
        },

        createdRow: function (row, data, dataIndex) {
            $(row).addClass('animate__animated animate__fadeIn align-middle');
        },
        columns: [
            {
                "class": 'text-center',
                "data": "idmodulo"
            },
            {
                "class": '',
                "data": "modulo"
            },
            {
                "class": '',
                "data": "tipo"
            },
            {
                "class": 'text-center',
                "data": "orden"
            },
            {
                "class": '',
                "data": "estadodesc"
            },
            {
                "class": '',
                "data": "iconEdit"
            },
        ],

        deferRender: false,
        paging: false,
        searching: true,
        scrollY: '50vh',
        scrollX: true,
        scrollCollapse: true,
        info: true,
        // ordering: false,
        language: {
            "url": "../../js/DataTableSpanishShort2.json"
        },

    });

    function Refresh() {
        $('td').addClass('text-light')
        $('.dataTable').addClass('opa5')
        $('#modulos').DataTable().ajax.reload();
    }
    function hideForm() {
        $('#rowTable').show()
        fadeInOnly('#rowTable')
        $('#Refresh').show()
        $('#AddMod').show()
        $('#RowAddMod').hide()
        $('#formAddMod')[0].reset()
        $('#NombreMod').prop('disabled', false)
        $('#respuesta').hide()
        $('#respuesta').removeClass('text-danger text-success')
    }
    function showForm() {
        $('#rowTable').hide()
        $('#Refresh').hide()
        $('#AddMod').hide()
        $('#RowAddMod').show()
        fadeInOnly('#RowAddMod')
    }

    $(document).on("click", "#Refresh", function (e) {
        Refresh()
    });

    $(document).on("click", "#AddMod", function (e) {
        $('#tituloForm').html('Agregar M&oacute;dulo<svg class="bi" width="15" height="15" fill="currentColor"><use xlink:href="../../img/bootstrap-icons.svg#plus" /></svg>')
        $('#TipoMod').val('1')
        showForm()
        $('#accion').val(1)
    });

    $(document).on("click", ".edit", function (e) {

        $('#tituloForm').html('Editar M&oacute;dulo<svg class="bi ml-2" width="10" height="10" fill="currentColor"><use xlink:href="../../img/bootstrap-icons.svg#pen" /></svg>')

        showForm()
        $('#accion').val(2)

        var idmodulo = $(this).attr('data');
        var modulo = $(this).attr('data1');
        var orden = $(this).attr('data2');
        var estado = $(this).attr('data3');
        var idtipo = $(this).attr('data4');
        var tipo = $(this).attr('data5');

        $('#IdMod').val(idmodulo)
        $('#NombreMod').val(modulo)
        $('#NombreMod').prop('disabled', true)
        $('#OrdenMod').val(orden)
        $('#EstadoMod').val(estado)
        $('#TipoMod').val(idtipo)

    });

    $(document).on("click", "#CancelMod", function (e) {
        hideForm()
    });

    ActiveBTN(false, '#Aceptar', 'Guardando', 'Aceptar');

    $('#respuesta').hide()

    $("#formAddMod").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            // contetnType: "application_json; charset=utf-8",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                ActiveBTN(true, '#Aceptar', 'Guardando', 'Aceptar');
                $.notifyClose();
                notify('Aguarde..', 'info', 0, 'right')
            },
            success: function (data) {
                if (data.status == "ok") {
                    $('#respuesta').removeClass('text-danger')
                    $('#respuesta').addClass('text-success')
                    $('#respuesta').show()
                    $('#respuesta').html(data.dato)
                    ActiveBTN(false, '#Aceptar', 'Guardando', 'Aceptar');
                    $.notifyClose();
                    notify(data.Mensaje, 'success', 5000, 'right')
                    setTimeout(() => {
                        Refresh()
                        hideForm()
                    }, 2000);
                } else {
                    $('#respuesta').removeClass('text-success')
                    $('#respuesta').addClass('text-danger')
                    ActiveBTN(false, '#Aceptar', 'Guardando', 'Aceptar');
                    $('#respuesta').show()
                    $('#respuesta').html(data.dato)
                    $.notifyClose();
                    notify(data.Mensaje, 'danger', 5000, 'right')
                }
            }
        });
    });

});