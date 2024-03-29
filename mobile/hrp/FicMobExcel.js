
var IconExcel = '.xls <img src="../img/xls.png" class="w15" alt="Exportar Excel">'
ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)

function GetFicExcel() {
    $.ajax({
        type: 'POST',
        dataType: "json",
        url: "FicMobExcel.php",
        'data': {
            _dr   : $("#_drMob").val(),
        },
        beforeSend:function(){
            ActiveBTN(true, "#btnExcel", 'Exportando', IconExcel)
            $.notifyClose();
            notify('Exportando', 'info', 0, 'right')
        },
        success: function (data) {
            if (data.status == "ok") {
            ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)
            window.location=data.archivo
            $.notifyClose();
            notify(data.Mensaje, 'success', 5000, 'right')
            }
        },
        error: function () {
            ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)     
            $.notifyClose();
            notify('Error', 'danger', 5000, 'right')          
        }
    });
}

$(document).on("click", "#btnExcel", function (e) {
    GetFicExcel()
});

