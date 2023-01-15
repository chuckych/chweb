const time = document.getElementById('time').value
let IconExcel = '<div class="d-inline-flex" data-titler="Exportar datos"><i class="bi bi-file-earmark-arrow-down-fill"></i><span class="ml-1 d-none d-sm-block">Exportar</span></div>'
ActiveBTN(true, "#btnExcel", IconExcel, IconExcel)

$(document).on('click', '#btnExcel', function (e) {
    $.notifyClose();
    e.preventDefault();
    CheckSesion()
    axios.get('js/bodyModal.html?'+time).then(function (response) {
        bootbox.confirm({
            // title: '',
            message: response.data,
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
    })
});

function toExport() {
    let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
    let url = '';
    let toExcelAll = '';
    let toPdfAll = '';

    if ($("input[name=exportLegajo]:checked").val() == '1') {
        toExcelAll = ''
        toPdfAll = ''
        if ($("input[name=exporType]:checked").val() == '1') {
            url = 'toExcel.php';
        }
        if ($("input[name=exporType]:checked").val() == '2') {
            url = 'reporte/index.php';
        }
    } else if ($("input[name=exportLegajo]:checked").val() == '2') {
        toExcelAll = ''
        toPdfAll = ''
        if ($("input[name=exporType]:checked").val() == '1') {
            toExcelAll = '1'
            toPdfAll = ''
            url = 'getPersonal.php';
        }
        if ($("input[name=exporType]:checked").val() == '2') {
            toExcelAll = '1'
            toPdfAll = '1'
            url = 'getPersonal.php';
        }
    }

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
            // console.log('No existe el selector ' + selector);
            return s
        }
    }

    let data = new FormData()
    data.append('time', $("#time").val())
    data.append('_drhorarios', document.getElementById("_drHorarios").value)
    data.append('Tipo', $("#Tipo").val())
    data.append('toExcelAll', toExcelAll)
    data.append('toPdfAll', toPdfAll)
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
                    // window.location = file.archivo
                    // window.open(file.archivo)
                }
                $(document).on('click', '.download', function (e) {
                    setTimeout(() => {
                        $.notifyClose();
                    }, 3000);
                });
            }
        }
    }).catch(function (error) {
        console.log(error.message);
        $.notifyClose();
        console.log(error.request);
        notify(error.message, 'warning', 0, 'right')
    });;
}

