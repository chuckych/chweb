$(function () {
    'use strict'
    function nombreTipoProc(tipo) {
        let a = ''
        switch (tipo) {
            case "A":
                a = 'Alta';
                break;
            case "B":
                a = 'Baja';
                break;
            case "M":
                a = 'Modificación';
                break;
            case "P":
                a = 'Proceso';
                break;
            default:
                a = row['tipo'];
                break;
        }
        return a
    }
    function vjs() {
        return $('#_vjs').val()
    }
    let tableAuditoria = $('#tableAuditoria').dataTable({
        lengthMenu: [[3, 5, 10, 25, 50, 100], [3, 5, 10, 25, 50, 100]],
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        stateSave: true,
        stateDuration: -1,
        dom:"<'row'<'col-12 col-sm-6'l><'col-12 col-sm-6 d-flex justify-content-end'f>>" +
        "<'row'<'col-12'tr>>" +
        "<'row'<'col-12 col-sm-5'i><'col-12 col-sm-7 d-flex justify-content-end'p>>",
        "ajax": {
            url: "getAuditoria.php?"+$.now(),
            type: "POST",
            dataType: "json",
            "data": function (data) {
                // data._eg = $("input[name=_eg]:checked").val();
            },
            error: function () {
                $("#tablePersonal").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass('animate__animated animate__fadeIn');
        },
        columns: [
            {
                className: '', targets: '', title: 'Usuario',
                "render": function (data, type, row, meta) {
                    let datacol = `<div data-titler="Usuario: `+row['nombre']+`"><span class="fw5">`+row['nombre']+`</span><br><span class="">`+row['usuario']+`</span></div>`
                    return datacol;
                },
            },
            {
                className: 'text-center', targets: '', title: 'ID Sesion',
                "render": function (data, type, row, meta) {
                    let datacol = '<div>'+row['id_sesion']+'</div>'
                    return datacol;
                },
            },
            {
                className: '', targets: '', title: 'Cuenta',
                "render": function (data, type, row, meta) {
                    let datacol = '<div>'+row['audcuenta_nombre']+'</div>'
                    return datacol;
                },
            },
            {
                className: 'ls1', targets: '', title: 'Fecha',
                "render": function (data, type, row, meta) {
                    let datacol = '<div>'+moment(row['fecha']).format('DD/MM/YYYY')+'</div>'
                    return datacol;
                },
            },
            {
                className: 'ls1', targets: '', title: 'Hora',
                "render": function (data, type, row, meta) {
                    let datacol = '<div>'+row['hora']+'</div>'
                    return datacol;
                },
            },
            {
                className: 'text-center', targets: '', title: 'Tipo',
                "render": function (data, type, row, meta) {
                    let datacol = `<div data-titlel="`+nombreTipoProc(row['tipo'])+`">`+(row['tipo'])+`</div>`
                    return datacol;
                },
            },
            {
                className: 'w-100 text-wrap', targets: '', title: 'Dato',
                "render": function (data, type, row, meta) {
                    let datacol = `<div data-titlel="`+row['dato']+`">`+row['dato']+`</div>`
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
            "url": "../../js/DataTableSpanishShort2.json" + "?" + $.now(),
        }
    });
    // On Init
    tableAuditoria.on('init.dt', function () {
        $('#tableAuditoria_filter input').attr('placeholder', 'Buscar');
    });
    tableAuditoria.on('page.dt', function () {
        $('#tableAuditoria div').addClass('bg-light text-light');
    });
    tableAuditoria.on('draw.dt', function () {
        $('#tableAuditoria div').removeClass('bg-light text-light');
        $('#divTableAud').show()
    });

})