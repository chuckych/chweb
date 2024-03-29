
var IconExcel = '.xls <img src="/'+$('#_homehost').val()+'/img/xls.png" class="w15" alt="Exportar Excel">'
ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)


function GetFicExcel() {
    $.ajax({
        type: 'POST',
        dataType: "json",
        url: '/'+$('#_homehost').val()+'/novedades/NovXLS.php',
        'data': {
            _l         : $("#_l").val(),
            Per        : $("#Per").val(),
            Per2       : $("#Per2").val(),
            Tipo       : $("#Tipo").val(),
            Emp        : $("#Emp").val(),
            Plan       : $("#Plan").val(),
            Sect       : $("#Sect").val(),
            Sec2       : $("#Sec2").val(),
            Grup       : $("#Grup").val(),
            Sucur      : $("#Sucur").val(),
            _dr        : $("#_dr").val(),
            FicNoTi    : $("#FicNoTi").val(),
            FicNove    : $("#FicNove").val(),
            FicCausa    : $("#FicCausa").val(),
            ToInfornov : $("#ToInfornov").val(),
        },
        beforeSend:function(){
            ActiveBTN(true, "#btnExcel", 'Exportando', IconExcel)
        },
        success: function (data) {
            if (data.status == "ok") {
            ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)
            window.location=data.archivo
            }

        },
        error: function () {
            ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)               
        }
    });
}

$(document).on("click", "#btnExcel", function (e) {
    CheckSesion()
    GetFicExcel()
});

