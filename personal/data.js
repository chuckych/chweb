$(function () {
    $("#_eg").click(function() {
        if ($("#_eg").is(":checked")) {
            $("#_eg").val('on').trigger('change')
            $('#table-personal').DataTable().ajax.reload();
        } else {
            $("#_eg").val('off').trigger('change')
            $('#table-personal').DataTable().ajax.reload();
        }
    });
    $('#table-personal').dataTable({
        "initComplete": function(settings) {
            $("#PersonalTable").removeClass('invisible');
        },
        "drawCallback": function(settings) {
            classEfect("#PersonalTable", 'animate__animated animate__fadeIn')
        },
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        "ajax": {
            url: "?p=array_personal.php&<?= $_SERVER['QUERY_STRING'] ?>",
            type: "GET",
            dataType: "json",
            "data": function(data) {
                data._c = $("#_c").val();
                data._r = $("#_r").val();
                data._eg = $("input[name=_eg]:checked").val();
            },
            error: function() {
                $("#table-personal").css("display", "none");
            }
        },
        columns: [{
                "class": "align-middle",
                "data": 'editar'
            },
            {
                "class": "align-middle",
                "data": 'pers_legajo'
            },
            {
                "class": "align-middle",
                "data": 'pers_nombre'
            },
            {
                "class": "align-middle",
                "data": 'pers_dni'
            },
            {
                "class": "align-middle",
                "data": 'pers_estado'
            },
            {
                "class": "align-middle",
                "data": 'pers_empresa'
            },
            {
                "class": "align-middle",
                "data": 'pers_planta'
            },
            {
                "class": "align-middle",
                "data": 'pers_convenio'
            },
            {
                "class": "align-middle",
                "data": 'pers_sector'
            },
            {
                "class": "align-middle",
                "data": 'pers_seccion'
            },
            {
                "class": "align-middle",
                "data": 'pers_grupo'
            },
            {
                "class": "align-middle",
                "data": 'pers_sucur'
            },
        ],
        // scrollY: '50vh',
        scrollX: true,
        paging: true,
        searching: true,
        scrollCollapse: true,
        info: 1,
        ordering: 0,
        responsive: 0,
        language: {
            "url": "/" + _homehost + "/js/DataTableSpanishShort2.json"
        }
    });
});
