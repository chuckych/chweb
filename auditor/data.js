$("#Refresh").on("click", function() {
    $('#table-auditoria').DataTable().ajax.reload();
});
$('#table-auditoria').dataTable({
    bProcessing: true,
    serverSide: true,
    deferRender: true,
    fixedHeader: {
        footer: true
    },
    "ajax": {
        url: "GetAudito.php",
        type: "POST",
        dataType: "json",
        "data": function(data) {
            data._c = $("#_c").val();
            data._r = $("#_r").val();
            // data._eg = $("input[name=_eg]:checked").val();
        },
        error: function() {
            $("#table-auditoria").css("display", "none");
        }
    },
    columns: [{
            "class": "ls1",
            "data": 'AudFech'
        },
        {
            "class": "ls1",
            "data": 'AudHora'
        },
        {
            "class": "",
            "data": 'AudUser'
        },
        {
            "class": "",
            "data": 'AudTerm'
        },
        {
            "class": "",
            "data": 'AudModu'
        },
        {
            "class": "",
            "data": 'AudTipo'
        },
        {
            "class": "",
            "data": 'AudDato'
        },
        // {
        //     "class": "w-100",
        //     "data": 'null'
        // },
    ],
    // scrollY: '50vh',
    scrollX: true,
    paging: true,
    searching: true,
    // scrollCollapse: true,
    info: true,
    ordering: false,
    responsive: false,
    language: {
        "url": "/" + _homehost + "/js/DataTableSpanish.json"
    }
});