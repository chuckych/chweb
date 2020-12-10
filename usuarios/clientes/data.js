$('#GetClientes').DataTable({
    lengthMenu: [[10,15,20], [10,15,20]],
    bProcessing: true,
    deferRender: true,
    searchDelay: 1500,
    ajax: {
        url: "GetClientes.php",
        type: "POST",
        dataType: "json",
        "data": function(data){
           
        },
    },
    columns: [
    {
        "class": "text-center",
        "data": "Editar"
    }, 
    {
        "class": "fw4",
        "data": "nombre"
    }, 
    {
        "class": "",
        "data": "cant_usuarios"
    }, 
    {
        "class": "",
        "data": "cant_roles"
    }, 
    {
        "class": "",
        "data": "ident"
    }, 
    {
        "class": "",
        "data": "host"
    }, 
    // {
    //     "class": "",
    //     "data": "db"
    // }, 
    {
        "class": "",
        "data": "user"
    }, 
    // {
    //     "class": "",
    //     "data": "pass"
    // }, 
    {
        "class": "",
        "data": "auth_windows"
    }, 
    {
        "class": "",
        "data": "tkmobile"
    }, 
    {
        "class": "",
        "data": "WebService"
    }, 
    // {
    //     "class": "",
    //     "data": ""
    // }, 
    // {
    //     "class": "",
    //     "data": "null"
    // }, 
],
    scrollX: true,
    scrollCollapse: true,
    scrollY: '30vmax',
    paging: true,
    info: true,
    searching: true,
    ordering: false,
    language: {
        "url": "../../js/DataTableSpanishShort2.json"
    },
});