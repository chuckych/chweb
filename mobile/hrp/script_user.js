// $(document).ready(function () {
   
    tableUsuarios = $('#tableUsuarios').DataTable({
        "initComplete": function (settings, json) {

        },
        "drawCallback": function (settings) {
            classEfect("#tableUsuarios tbody", 'animate__animated animate__fadeIn')
            setTimeout(function () {
                loadingTableRemove('#modalUsuarios')
            }, 100);
            $('#tableUsuarios_filter .form-control-sm').attr('placeholder', 'Buscar Usuarios')
        },
        // iDisplayLength: -1,
        dom: "<'row'<'col-12 d-flex align-items-end m-0 justify-content-between'lf>>" +
            "<'row'<'col-12'tr>>" +
            "<'row'<'col-12 d-flex align-items-center justify-content-between'ip>>",
        ajax: {
            url: "getUsuariosMobile.php",
            type: "POST",
            "data": function (data) { },
            error: function () { },
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass('animate__animated animate__fadeIn align-middle');
        },
        columnDefs: [
            { title: 'Phone ID', className: '', targets: 0 },
            { title: 'Nombre', className: 'w-100', targets: 1 },
            { title: 'Fichadas', className: 'text-center', targets: 2 },
        ],
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        paging: true,
        searching: true,
        info: true,
        ordering: false,
        language: {
            "url": "../../js/DataTableSpanishShort2.json"
        },

    });
    tableUsuarios.on('processing.dt', function (e, settings, processing) {
        loadingTable('#modalUsuarios')
    });

// });