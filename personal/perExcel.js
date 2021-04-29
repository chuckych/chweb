
var IconExcel = '.xls <img src="../img/xls.png?v='+vjs()+'" class="w15" alt="Exportar Excel">'
ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)


function GetExcel() {
    $.ajax({
        type: 'POST',
        dataType: "json",
        url: "perExcel.php",
        'data': {
            _eg      : $("input[name=_eg]:checked").val(),
            _porApNo : $("input[name=_porApNo]:checked").val(),
            Per      : $("#Per").val(),
            Tipo     : $("#Tipo").val(),
            Emp      : $("#Emp").val(),
            Plan     : $("#Plan").val(),
            Sect     : $("#Sect").val(),
            Sec2     : $("#Sec2").val(),
            Grup     : $("#Grup").val(),
            Sucur    : $("#Sucur").val(),
            Tare     : $("#Tare").val(),
            Conv     : $("#Conv").val(),
            Regla    : $("#Regla").val(),
            toexcel  : true,
        },
        beforeSend: function () {
            ActiveBTN(true, "#btnExcel", 'Exportando', IconExcel)
        },
        success: function (data) {
            if (data.status == "ok") {
                ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)
                window.location = data.archivo
            }

        },
        error: function () {
            ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)
        }
    });
}

$(document).on("click", "#btnExcel", function (e) {
    GetExcel()
});

