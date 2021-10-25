
var IconExcel = '.xls <img src="../img/xls.png" class="w15" alt="Exportar Excel">'
ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)

function GetFicExcel() {
    let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
    $.ajax({
        type: 'POST',
        dataType: "json",
        url: "FicMobExcel.php",
        'data': {
            _dr: $("#_drMob").val(),
        },
        beforeSend: function () {
            ActiveBTN(true, "#btnExcel", 'Exportando', IconExcel)
            $.notifyClose();
            notify('Exportando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
        },
        success: function (data) {
            if (data.status == "ok") {
                ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)
                window.location = data.archivo
                $.notifyClose();
                notify('Archivo exportado correctamente.<br/>Si la descarga no ha iniciado, haga clic en el siguiente enlace. <a href="' + data.archivo + '" class="btn btn-custom btn-sm mt-2 fontq">Descargar</a>', 'success', 10000, 'right')
            } else {
                ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)
                $.notifyClose();
                notify(data.Mensaje, 'warning', 0, 'right')
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

