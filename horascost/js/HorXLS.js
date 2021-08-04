
var IconExcel = '.xls <img src="../img/xls.png" class="w15" alt="Exportar Excel">'
ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)


function GetFicExcel() {
    $.ajax({
        type: 'POST',
        dataType: "json",
        url: "HorXLS.php",
        'data': {
            _f       : $("#_f").val(),
            Per      : $("#Per").val(),
            Tipo     : $("#Tipo").val(),
            Emp      : $("#Emp").val(),
            Plan     : $("#Plan").val(),
            Sect     : $("#Sect").val(),
            Sec2     : $("#Sec2").val(),
            Grup     : $("#Grup").val(),
            Sucur    : $("#Sucur").val(),
            _dr      : $("#_dr").val(),
            _l       : $("#_l").val(),
            Thora    : $("#Thora").val(),
            SHoras   : $("#SHoras").val(),
            HoraMin  : $("#HoraMin").val(),
            HoraMax  : $("#HoraMax").val(),
            Calculos : $("#Calculos").val(),
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
    onOpenSelect2()
    GetFicExcel()
});

