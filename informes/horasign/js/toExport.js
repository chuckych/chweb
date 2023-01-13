
let IconExcel = '<div class="d-inline-flex" data-titler="Exportar datos"><i class="bi bi-file-earmark-arrow-down-fill"></i><span class="ml-1 d-none d-sm-block">Exportar</span></div>'
ActiveBTN(true, "#btnExcel", IconExcel, IconExcel)

$(document).on('click', '#btnExcel', function (e) {
    $.notifyClose();
    e.preventDefault();
    CheckSesion()
    let body = `
        <span class="fonth">Exportar datos</span>
        <div class="row pt-3">
            <div class="col-12">
                <div class="d-inline-flex">
                    <div class="custom-control custom-switch w130">
                        <input type="radio" class="custom-control-input" checked id="legajoActual" name="exportLegajo" value="1">
                        <label class="custom-control-label" style="padding-top: 3px;" for="legajoActual">Legajo actual</label>
                    </div>
                    <div class="custom-control custom-switch ml-3">
                        <input type="radio" class="custom-control-input" id="legajoTodos" name="exportLegajo" value="2">
                        <label class="custom-control-label" style="padding-top: 3px;" for="legajoTodos">Todos (Según filtro)</label>
                    </div>
                </div>
            </div>
            <div class="col-12 pt-3">
                <div class="d-inline-flex">
                    <div class="custom-control custom-switch w130">
                        <input type="radio" class="custom-control-input" checked id="exportExcel" name="exporType" value="1">
                        <label class="custom-control-label" style="" for="exportExcel"><i class="bi bi-filetype-xls font1"></i> xls</label>
                    </div>
                    <div class="custom-control custom-switch ml-3">
                        <input type="radio" class="custom-control-input" id="exportPDF" name="exporType" value="2">
                        <label class="custom-control-label" style="" for="exportPDF"><i class="bi bi-filetype-pdf font1"></i> pdf</label>
                    </div>
                </div>
            </div>
        </div>
    `;
    bootbox.confirm({
        // title: '',
        message: body,
        // message: '',
        buttons: {
            confirm: {
                label: 'Aceptar',
                className: 'btn-custom btn-sm fontq'
            },
            cancel: {
                label: 'Cancelar',
                className: 'btn-light btn-sm fontq text-secondary'
            }
        },
        callback: function (result) {
            if (result) {
                CheckSesion()
                toExport()
                ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)
            }
        }
    });
    e.stopImmediatePropagation();
});

function toExport() {
    let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
    let url = '';
    let toExcelAll = '';
    let toPdfAll = '';
    // url = ($("input[name=exportLegajo]:checked").val() == '1') ? 'toExcel.php' : 'getPersonal.php'
    // url = ($("input[name=exporType]:checked").val() == '2') ? 'reporte/index.php' : 'getPersonal.php';

    switch ($("input[name=exporType]:checked").val()) {
        case '2':
            url = 'reporte/index.php';
            break;
        case '1':
            url = 'toExcel.php';
            break;
        default:
            url = ''
            break;
    }
    switch ($("input[name=exportLegajo]:checked").val()) {
        case '2':
            toExcelAll = 1
            toPdfAll = 1
            break;
    }
    switch ($("input[name=exportLegajo]:checked").val()) {
        case '2':
            toExcelAll = 1
            break;
    }

    url = (toExcelAll == 1) ? 'getPersonal.php' : url
    url = (toPdfAll == 1) ? 'getPersonal.php' : url

    ActiveBTN(true, "#btnExcel", 'Exportando ' + loading, IconExcel)
    $.notifyClose();
    notify('Exportando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')

    let getObjVal = (selector, key, formdata) => {
        if (!selector) return false
        let s = ''
        if ($(selector).val()) {
            s = $(selector).val().forEach((a => {
                formdata.append(key + "[]", a)
            }));
            return s
        } else {
            s = ''
            console.log('No existe el selector ' + selector);
            return s
        }
    }

    let data = new FormData()
    data.append('time', $("#time").val())
    data.append('_drhorarios', document.getElementById("_drHorarios").value)
    data.append('Tipo', $("#Tipo").val())
    data.append('toExcelAll', toExcelAll)
    // data.append('exportPDF', exportPDF)
    data.append('toPdfAll', toPdfAll)
    // $("#Per").val().forEach((a => { data.append("Per[]", a) }));
    getObjVal("#Per", 'Per', data);
    getObjVal("#Emp", 'Emp', data);
    getObjVal("#Plan", 'Plan', data);
    getObjVal("#Sect", 'Sect', data);
    getObjVal("#Sec2", 'Sec2', data);
    getObjVal("#Grup", 'Grup', data);
    getObjVal("#Sucur", 'Sucur', data);
    getObjVal("#Conv", 'Conv', data);
    getObjVal("#Tare", 'Tare', data);
    getObjVal("#Regla", 'Regla', data);

    axios({
        method: 'POST',
        url: '/' + getSelectorVal('#_homehost') + '/status_ws.php?status=ws',
        dataType: "json",
        url: url,
        responseType: 'stream',
        data: data,
    }).then(function (response) {
        if (response.data) {
            if (response.data.status == "ok") {
                ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)
                let file = response.data
                // window.location = file.data
                $.notifyClose();
                if (!file.archivo) {
                    notify('No hay datos a exportar', 'danger', 3000, 'right');
                } else {
                    notify('<b>Archivo exportado correctamente</b>.<br><div class="shadow-sm w100"><a href="' + file.archivo + '" class="btn btn-custom px-3 btn-sm mt-2 fontq download" target="_blank" download><div class="d-flex align-items-center"><span>Descargar</span><i class="bi bi-file-earmark-arrow-down ml-1 font1"></i></div></a></div>', 'warning', 0, 'right')
                }
                $(document).on('click', '.download', function (e) {
                    setTimeout(() => {
                        $.notifyClose();
                    }, 3000);
                });

                // window.location = data.archivo
            }
        }
    }).catch(function (error) {
        console.log(error.message);
        $.notifyClose();
        console.log(error.request);
        notify(error.message, 'warning', 0, 'right')
    });;
}

