
var IconExcel = '.xls <img src="../../img/xls.png" class="w15" alt="Exportar Excel">'
ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)


function GetFicExcel() {
    $.ajax({
        type: 'POST',
        dataType: "json",
        url: "FicExcel.php",
        'data': {
            fecha   : $("#_drnovc").val(),
            ordenar : $("#ordenar").val(),
            Emp     : $("#Emp").val(),
            Plan    : $("#Plan").val(),
            Sect    : $("#Sect").val(),
            Sec2    : $("#Sec2").val(),
            Grup    : $("#Grup").val(),
            Sucur   : $("#Sucur").val(),
            Tipo    : $("#Tipo").val(),
            Per     : $("#Per").val(),
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
    GetFicExcel()
});

