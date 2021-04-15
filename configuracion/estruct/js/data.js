$(function () {
    function _rutauser() {
        var _rutauser = $('#_homehost').val() + '_' + $('#_lega').val()
        return _rutauser
    }
    var _rutauser = _rutauser()
    function ActualizaTablas() {
        tableNacion.ajax.reload(null, false);
        tableProvincias.ajax.reload(null, false);
        tableLocalidad.ajax.reload(null, false);
    };
    function removeButtons() {
        $('.trSelect').removeClass('trSelect')
        $('.btnAction').remove()
    }
    function imprimirForm(cod, desc, tipo) {
        $.ajax({
            type: 'post',
            dataType: "html",
            url: "formulario.php?v=" + vjs(),
            data: {
                Cod: cod,
                Desc: desc,
                Tipo: tipo,
            },
            beforeSend: function (xhr) {
                $('#actionForm').html('<div class="p-3 text-secondary animate__animated animate__fadeIn">Cargando..</div>')
            }
        }).done(function (data) {
            $('#actionForm').html(data)
            removeButtons()
        });
    }

    function eventoOn(x) {
        return (x.matches) ? 'click' : 'mouseenter'
    }
    let mxwidth = window.matchMedia("(max-width: 700px)")
    function actionButtons(data0, data1, nameidbtn1, nameidbtn2) {
        let btns = `<div class="d-flex align-items-center justify-content-between">` + data1 + `
        <div class="">
            <a href="#actionForm" title="Editar" type="button" id="`+ nameidbtn1 + data0 + `n" class="animate__animated animate__fadeIn btnAction fontq mr-1 btn btn-custom px-2 btn-sm" id="EditNacion" value="1"><i class="bi2 bi-pencil"></i></a>
            <a href="#actionForm" title="Eliminar" type="button" id="`+ nameidbtn2 + data0 + `n" class="animate__animated animate__fadeIn btnAction fontq btn btn-custom px-2 btn-sm"><i class="bi2 bi-trash"></i></a>
        </div>
        </div>`
        return btns;
    }

    tableNacion = $('#tableNacion').DataTable({
        initComplete: function () {
            $('#tableNacion thead').remove()
            // $("#tableNacion_filter").addClass('d-inline-flex ')
            $("#tableNacion_filter").append(`
            <a href="#actionForm" title="Agregar Nuevo" type="button" class="btn btn-custom btn-sm" id="AddNacion"><i class="bi2 bi-plus"></i></a>
            `)
            $("#AddNacion").click(function () {
                imprimirForm('', '', 'c_nacion') /** imprimo html para ingresar datos */
            });
            $("#tableNacion_filter .form-control").removeClass('form-control-sm')
            $("#tableNacion_filter .form-control").attr('placeholder', 'Buscar nacionalidad')
        },
        drawCallback: function (settings) {
            $('#tableNacion thead').remove()
            // $("#tableNacion_filter .form-control").focus()
        },
        dom: "<'row mt-2'<'col-12 col-sm-6 d-flex align-items-start'p><'col-12 col-sm-6 mt-2 mt-sm-0'f>>" +
            "<'row'<'col-12'tr>>" +
            "<'row'<'col-sm-12 col-md-6 d-flex align-items-start'i><'col-sm-12 col-md-6 d-flex justify-content-end'l>>",
        lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "Todo"]],
        stateSave: true,
        stateDuration: -1,
        iDisplayLength: 5,
        bLengthChange: true,
        "ajax": {
            url: 'getNacionalidad.php',
            type: "POST",
            "data": function (data) {

            },
            error: function () { },
        },
        columnDefs: [
            { className: "align-middle pointer py-4 text-center", targets: 0 },
            { className: "align-middle pointer CheckNacion w-100", targets: 1 },
        ],
        deferRender: true,
        bLengthChange: true,
        paging: true,
        searching: true,
        info: true,
        ordering: false,
        autoWidth: true,
        language: {
            "url": "../../js/DataTableSpanishTotal.json?v=" + vjs()
        },
    })

    $('#tableNacion tbody').on(eventoOn(mxwidth), '.CheckNacion', function (e) {
        e.preventDefault();
        removeButtons()
        $(this).parents('tr').addClass('trSelect')
        let data = tableNacion.row($(this).parents('tr')).data();
        $(this).html(actionButtons(data[0], data[1], 'editNacion', 'deleteNacion'))
        $("#editNacion" + data[0] + "n").show()
        $("#deleteNacion" + data[0] + "n").show()
        $("#editNacion" + data[0] + "n").click(function (e) {
            e.preventDefault();
            imprimirForm(data[0], data[1], 'u_nacion') /** imprimo html de formulario */
        });
        $("#deleteNacion" + data[0] + "n").click(function (e) {
            e.preventDefault();
            imprimirForm(data[0], data[1], 'd_nacion') /** imprimo html de formulario */
        });
    });
    $('#tableNacion').on('mouseleave', '.CheckNacion', function () {
        removeButtons()
    });
    $('#tableNacion').mouseenter().off("mouseenter")

    function actionButtons_p(data0, data1, nameidbtn1, nameidbtn2) {
        let btns = `<div class="d-flex align-items-center justify-content-between">` + data1 + `
        <div class="">
            <a href="#actionForm_p" title="Editar" type="button" id="`+ nameidbtn1 + data0 + `n" class="animate__animated animate__fadeIn btnAction fontq mr-1 btn btn-custom px-2 btn-sm" id="EditNacion" value="1"><i class="bi2 bi-pencil"></i></a>
            <a href="#actionForm_p" title="Eliminar" type="button" id="`+ nameidbtn2 + data0 + `n" class="animate__animated animate__fadeIn btnAction fontq btn btn-custom px-2 btn-sm"><i class="bi2 bi-trash"></i></a>
        </div>
        </div>`
        return btns;
    }
    function imprimirForm_p(cod, desc, tipo) {
        $.ajax({
            type: 'post',
            dataType: "html",
            url: "formulario.php?v=" + vjs(),
            data: {
                Cod: cod,
                Desc: desc,
                Tipo: tipo,
            },
            beforeSend: function (xhr) {
                $('#actionForm_p').html('<div class="p-3 text-secondary animate__animated animate__fadeIn">Cargando..</div>')
            }
        }).done(function (data) {
            $('#actionForm_p').html(data)
            removeButtons()
        });
    }
    tableProvincias = $('#tableProvincias').DataTable({
        initComplete: function () {
            $('#tableProvincias thead').remove()
            // $("#tableProvincias_filter").addClass('d-inline-flex ')
            $("#tableProvincias_filter").append(`
            <a href="#actionForm_p" title="Agregar Nuevo" type="button" class="btn btn-custom btn-sm" id="AddProvincia"><i class="bi2 bi-plus"></i></a>
            `)
            $("#AddProvincia").click(function () {
                imprimirForm_p('', '', 'c_provincia') /** imprimo html para ingresar datos */
            });
            $("#tableProvincias_filter .form-control").removeClass('form-control-sm')
            $("#tableProvincias_filter .form-control").attr('placeholder', 'Buscar provincia')
        },
        drawCallback: function (settings) {
            $('#tableProvincias thead').remove()
            // $("#tableNacion_filter .form-control").focus()
        },
        dom: "<'row mt-2'<'col-12 col-sm-6 d-flex align-items-start'p><'col-12 col-sm-6 mt-2 mt-sm-0'f>>" +
            "<'row'<'col-12'tr>>" +
            "<'row'<'col-sm-12 col-md-6 d-flex align-items-start'i><'col-sm-12 col-md-6 d-flex justify-content-end'l>>",
        lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "Todo"]],
        stateSave: true,
        stateDuration: -1,
        iDisplayLength: 5,
        bLengthChange: true,
        "ajax": {
            url: 'getProvincias.php',
            type: "POST",
            "data": function (data) {

            },
            error: function () { },
        },
        columnDefs: [
            { className: "align-middle pointer py-4 text-center", targets: 0 },
            { className: "align-middle pointer CheckProvincia w-100", targets: 1 },
        ],
        deferRender: true,
        bLengthChange: true,
        paging: true,
        searching: true,
        info: true,
        ordering: false,
        autoWidth: true,
        language: {
            "url": "../../js/DataTableSpanishTotal.json?v=" + vjs()
        },
    })

    $('#tableProvincias tbody').on(eventoOn(mxwidth), '.CheckProvincia', function (e) {
        e.preventDefault();
        removeButtons()
        $(this).parents('tr').addClass('trSelect')
        let data = tableProvincias.row($(this).parents('tr')).data();
        $(this).html(actionButtons_p(data[0], data[1], 'editProvincia', 'deleteProvincia'))
        $("#editProvincia" + data[0] + "n").show()
        $("#deleteProvincia" + data[0] + "n").show()
        $("#editProvincia" + data[0] + "n").click(function (e) {
            e.preventDefault();
            imprimirForm_p(data[0], data[1], 'u_provincia') /** imprimo html de formulario */
        });
        $("#deleteProvincia" + data[0] + "n").click(function (e) {
            e.preventDefault();
            imprimirForm_p(data[0], data[1], 'd_provincia') /** imprimo html de formulario */
        });
    });
    $('#tableProvincias').on('mouseleave', '.CheckProvincia', function () {
        removeButtons()
    });
    $('#tableProvincias').mouseenter().off("mouseenter")

    function actionButtons_l(data0, data1, nameidbtn1, nameidbtn2) {
        let btns = `<div class="d-flex align-items-center justify-content-between">` + data1 + `
        <div class="">
            <a href="#actionForm_l" title="Editar" type="button" id="`+ nameidbtn1 + data0 + `n" class="animate__animated animate__fadeIn btnAction fontq mr-1 btn btn-custom px-2 btn-sm" id="EditNacion" value="1"><i class="bi2 bi-pencil"></i></a>
            <a href="#actionForm_l" title="Eliminar" type="button" id="`+ nameidbtn2 + data0 + `n" class="animate__animated animate__fadeIn btnAction fontq btn btn-custom px-2 btn-sm"><i class="bi2 bi-trash"></i></a>
        </div>
        </div>`
        return btns;
    }
    function imprimirForm_l(cod, desc, tipo) {
        $.ajax({
            type: 'post',
            dataType: "html",
            url: "formulario.php?v=" + vjs(),
            data: {
                Cod: cod,
                Desc: desc,
                Tipo: tipo,
            },
            beforeSend: function (xhr) {
                $('#actionForm_l').html('<div class="p-3 text-secondary animate__animated animate__fadeIn">Cargando..</div>')
            }
        }).done(function (data) {
            $('#actionForm_l').html(data)
            removeButtons()
        });
    }
    tableLocalidad = $('#tableLocalidad').DataTable({
        initComplete: function () {
            $('#tableLocalidad thead').remove()
            // $("#tableLocalidad_filter").addClass('d-inline-flex ')
            $("#tableLocalidad_filter").append(`
            <a href="#actionForm_l" title="Agregar Nuevo" type="button" class="btn btn-custom btn-sm" id="AddLocalidad"><i class="bi2 bi-plus"></i></a>
            `)
            $("#AddLocalidad").click(function () {
                imprimirForm_l('', '', 'c_localidad') /** imprimo html para ingresar datos */
            });
            $("#tableLocalidad_filter .form-control").removeClass('form-control-sm')
            $("#tableLocalidad_filter .form-control").attr('placeholder', 'Buscar localidad')
        },
        drawCallback: function (settings) {
            $('#tableLocalidad thead').remove()
            // $("#tableNacion_filter .form-control").focus()
        },
        dom: "<'row mt-2'<'col-12 col-sm-6 d-flex align-items-start'p><'col-12 col-sm-6 mt-2 mt-sm-0'f>>" +
            "<'row'<'col-12'tr>>" +
            "<'row'<'col-sm-12 col-md-6 d-flex align-items-start'i><'col-sm-12 col-md-6 d-flex justify-content-end'l>>",
        lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "Todo"]],
        stateSave: true,
        stateDuration: -1,
        iDisplayLength: 5,
        bLengthChange: true,
        "ajax": {
            url: 'getLocalidad.php',
            type: "POST",
            "data": function (data) {

            },
            error: function () { },
        },
        columnDefs: [
            { className: "align-middle pointer py-4 text-center", targets: 0 },
            { className: "align-middle pointer CheckLocalidad w-100", targets: 1 },
        ],
        deferRender: true,
        bLengthChange: true,
        paging: true,
        searching: true,
        info: true,
        ordering: false,
        autoWidth: true,
        language: {
            "url": "../../js/DataTableSpanishTotal.json?v=" + vjs()
        },
    })

    $('#tableLocalidad tbody').on(eventoOn(mxwidth), '.CheckLocalidad', function (e) {
        e.preventDefault();
        removeButtons()
        $(this).parents('tr').addClass('trSelect')
        let data = tableLocalidad.row($(this).parents('tr')).data();
        $(this).html(actionButtons_l(data[0], data[1], 'editLocalidad', 'deleteLocalidad'))
        $("#editLocalidad" + data[0] + "n").show()
        $("#deleteLocalidad" + data[0] + "n").show()
        $("#editLocalidad" + data[0] + "n").click(function (e) {
            e.preventDefault();
            imprimirForm_l(data[0], data[1], 'u_localidad') /** imprimo html de formulario */
        });
        $("#deleteLocalidad" + data[0] + "n").click(function (e) {
            e.preventDefault();
            imprimirForm_l(data[0], data[1], 'd_localidad') /** imprimo html de formulario */
        });
    });
    $('#tableLocalidad').on('mouseleave', '.CheckLocalidad', function () {
        removeButtons()
    });
    $('#tableLocalidad').mouseenter().off("mouseenter")


    $("#Encabezado").addClass('pointer')
    $("#Encabezado").on("click", function () {
        ActualizaTablas()
    });
    function cleanActionsForms() {
        $('#actionForm_p').html('')
        $('#actionForm_l').html('')
        $('#actionForm').html('')
    }
    $('#nacion-tab').on('shown.bs.tab', function (e) {
        cleanActionsForms()
        sessionStorage.setItem('activeTabDir', $(e.target).attr('href'));
    })
    $('#provincia-tab').on('shown.bs.tab', function (e) {
        cleanActionsForms()
        sessionStorage.setItem('activeTabDir', $(e.target).attr('href'));
    })
    $('#localidad-tab').on('shown.bs.tab', function (e) {
        cleanActionsForms()
        sessionStorage.setItem('activeTabDir', $(e.target).attr('href'));
    })
    let activeTab = sessionStorage.getItem('activeTabDir');
    if (activeTab) {
        $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
    }
    // $('#actionForm').on('click', '#cancelForm', function () {
});
