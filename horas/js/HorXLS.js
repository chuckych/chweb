
var IconExcel = '.xls <img src="../img/xls.png" class="w15" alt="Exportar Excel">'
ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)


function GetFicExcel() {
    $.ajax({
        type: 'POST',
        dataType: "json",
        url: "HorXLS.php",
        'data': {
            _f: $("#_f").val(),
            Per: $("#Per").val(),
            Tipo: $("#Tipo").val(),
            Emp: $("#Emp").val(),
            Plan: $("#Plan").val(),
            Sect: $("#Sect").val(),
            Sec2: $("#Sec2").val(),
            Grup: $("#Grup").val(),
            Sucur: $("#Sucur").val(),
            _dr: $("#_dr").val(),
            _l: $("#_l").val(),
            Thora: $("#Thora").val(),
            SHoras: $("#SHoras").val(),
            HoraMin: $("#HoraMin").val(),
            HoraMax: $("#HoraMax").val(),
            Calculos: $("#Calculos").val(),
        },
        beforeSend: function () {
            ActiveBTN(true, "#btnExcel", 'Exportando', IconExcel)
        },
        success: function (data) {
            if (data.status == "ok") {
                ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)
                // window.location=data.archivo

                $.notifyClose();
                const archivo = data.archivo ?? '';

                if (!archivo) {
                    notify('No se ha podido generar el archivo.', 'danger', 5000, 'right');
                    return;
                }

                const bannerDownload = `
                    <div class="d-flex flex-column">
                        <div class="font-weight-bold">Reporte generado.</div>
                        <a href="${archivo}" class="btn btn-custom px-2 btn-sm mt-2 font08 download" target="_blank" download>
                        <div class="d-flex align-items-center w-100 justify-content-center" style="gap:5px">
                            <span>Descargar</span> <i class="bi bi-file-earmark-arrow-down font1"></i>
                        </div>
                        </a>
                    </div>
                `;

                notify(bannerDownload, 'warning', 0, 'right');

                const download = document.querySelector('.download') ?? null;

                if (download) {
                    download.addEventListener('click', (e) => {
                        $.notifyClose();
                    });
                }
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

