$(function () {
    "use strict";

    // const axios = require('axios').default;
    let proy_page = sessionStorage.getItem(
        location.pathname.substring(1) + "proy_page"
    );
    // sessionStorage.removeItem(location.pathname.substring(1) + "proy_info");

    let proy_info = sessionStorage.getItem(
        location.pathname.substring(1) + "proy_info"
    );
    proy_info = JSON.parse(proy_info);

    if (proy_info) {
        $("#navName").html(proy_info["name"]);
        $("#navLega").html(proy_info["lega"]);
    } else {
        setTimeout(() => {
            fetch("routes.php/?page=log_rfid")
                .then(response => response.text())
                .then(data => {
                    sessionStorage.setItem(
                        location.pathname.substring(1) + "proy_page",
                        ""
                    );
                    $("#mainNav").addClass("invisible");
                    $("#contenedor").html(data);
                });
        }, 100);
    }

    fetch("navbar.html?" + $.now())
        .then(response => response.text())
        .then(data => {
            $("#mainNav").html(data);
            $(".navLogout").click(function () { // Logout
                fetch("routes.php/?page=log_rfid")
                    .then(response => response.text())
                    .then(data => {
                        sessionStorage.setItem(
                            location.pathname.substring(1) + "proy_page",
                            ""
                        );
                        $("#mainNav").addClass("invisible");
                        $("#contenedor").html(data);
                    });
            });
            getPag("#proyHome", "inicio"); // Home
            $("#mainTitleBar").html(capitalize(proy_page)); // Title
            $("#navName").html(proy_info["name"]);
            $("#navLega").html(proy_info["lega"]);
        });

    fetch("sidebar.php?" + $.now()) // Sidebar
        .then(response => response.text())
        .then(data => {
            $("#mainSideBar").html(data);
            getPag(".sidebarEmpresas", "empresas");
            getPag(".sidebarEstados", "estados");
            getPag(".sidebarProcesos", "procesos");
            getPag(".sidebarPlanos", "planos");
            getPag(".sidebarPlantillas", "plantillas");
            getPag(".sidebarMisTareas", "mistareas");
            getPag(".sidebarProyectos", "proyectos");
            getPag(".sidebarTareas", "tareas");
            getPag(".sidebarInicio", "inicio");
            getPag(".sidebarSalir", "salir");
        });

    proy_page
        ? $("#mainNav").removeClass("invisible")
        : $("#mainNav").addClass("invisible");

    proy_page = proy_page ? proy_page : "log_rfid";

    $(document).prop("title", capitalize(proy_page));

    fetch("routes.php/?page=" + proy_page)
        .then(response => response.text())
        .then(data => {
            $("#contenedor").html(data);
            $("input").attr("autocomplete", "off");
        });
});
function getPag(selector, pag) {
    $(document).on('click', selector, function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        axios.get('routes.php', {
            params: {
                'page': pag
            }
        }).then(function (response) {
            sessionStorage.setItem(
                location.pathname.substring(1) + "proy_page",
                pag
            );
            $("#contenedor").html(response.data);
        }).then(() => {
            $("#mainTitleBar").html(capitalize(pag));
            const p = selector;
            // $("#mainTitleBar").addClass(p.replace('.', ''));
            $(document).prop("title", capitalize(pag));
        }).catch(function (error) {
            alert(error);
        })
    });
}
function getPag2(pag, textPag = '') {
    axios.get('routes.php', {
        params: {
            'page': pag
        }
    }).then(function (response) {
        sessionStorage.setItem(
            location.pathname.substring(1) + "proy_page",
            pag
        );
        $("#contenedor").html(response.data);
    }).then(() => {
        if (textPag) {
            $("#mainTitleBar").html((textPag));
            $(document).prop("title", (textPag));
        } else {
            $("#mainTitleBar").html(capitalize(pag));
            $(document).prop("title", capitalize(pag));
        }
    }).catch(function (error) {
        alert(error);
    })
}
function ActiveBTN(bolean, selector, textTrue, textfalse) {
    if (bolean == true) {
        $(selector).prop("disabled", bolean);
        $(selector).html(textTrue);
    } else {
        $(selector).prop("disabled", bolean);
        $(selector).html(textfalse);
    }
}
function capitalize(word) {
    return word[0].toUpperCase() + word.substring(1).toLowerCase();
}
function decodeHtmlCharCodes(str) {
    return str.replace(/(&#(\d+);)/g, function (match, capture, charCode) {
        return String.fromCharCode(charCode);
    })
}
const decodeEntities = (function () {
    // this prevents any overhead from creating the object each time
    let element = document.createElement('div');
    function decodeHTMLEntities(str) {
        if (str && typeof str === 'string') {
            // strip script/html tags
            str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
            str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
            element.innerHTML = str;
            str = element.textContent;
            element.textContent = '';
        }
        return str;
    }
    return decodeHTMLEntities;
})();

const maskCosto = (selector) => {
    $(selector).mask("###0,00", { reverse: true, selectOnFocus: true });
};
const checkEmpty = (selector, tipo = 'input') => {
    if (tipo == 'input') {
        if ($(selector).val() == "") {
            $(selector).addClass("border border-danger border-wide");
        } else {
            $(selector).removeClass("border border-danger border-wide");
        }
    } else {
        if ($(selector).val() == null) {
            $(tipo).addClass("border border-danger border-wide");
        } else {
            $(tipo).removeClass("border border-danger border-wide");
        }
    }
};
function checkLengthInput(selector, length) {
    $(selector).keyup(function (e) {
        e.preventDefault();
        if ($(this).val().length == length) {
            $(this).removeClass("border border-danger border-wide");
        } else {
            $(this).addClass("border border-danger border-wide");
        }
    });
}
function select2EmptyRemove(selector) {
    $(selector).on('change', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        if ($(selector).val() != null) {
            let s = selector.split("#");
            s = s[1];
            // notify(s, 'info bg-blue-lt', 1000, 'right')
            $("#select2-" + s + "-container").removeClass("border border-danger border-wide");
        } else {
            let s = selector.split("#");
            s = s[1];
            // notify(s, 'info bg-red-lt', 1000, 'right')
            $("#select2-" + s + "-container").addClass("border border-danger border-wide");
        }
    });
}
const cleanProy_pasos = () => {
    sessionStorage.setItem(location.pathname.substring(1) + 'proy_pasos', '')
};
const get_proy_pasos = () => {
    let get_proy_pasos = sessionStorage.getItem(
        location.pathname.substring(1) + "proy_pasos"
    );
    get_proy_pasos = JSON.parse(get_proy_pasos);
    return get_proy_pasos;
};
function select2Value(id, text, selector) {
    let newOption = new Option(text, id, false, false);
    if (text != '') {
        $(selector).append(newOption).trigger('change');
    }
}
function completeTar(selector, datetime = false) {
    $(document).on('click', selector, function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        let tareID = $(this).attr('data-tareID');
        let datos = new FormData();
        if (datetime) {
            datos.append('datetime', datetime);
        }
        ActiveBTN(true, this, "AGUARDE <span class='animated-dots'></span>", 'COMPLETAR');
        datos.append('tareID', tareID);
        datos.append('tarComplete', 'tarComplete');
        axios({
            method: "post",
            url: 'finalizar/process.php',
            data: datos,
            headers: { "Content-Type": "multipart/form-data" },
        }).then(function (response) {
            $.notifyClose();
            let data = response.data;
            if (data.status == 'ok') {
                notify(data.Mensaje, 'success', 1000, 'right')
                $('#tableTarUser').DataTable().ajax.reload();
                $('#selectProy').DataTable().ajax.reload();
                $('#tableTareas').DataTable().ajax.reload();
            } else {
                notify(data.Mensaje, 'danger', 1000, 'right')
            }
        }).then(() => {
            ActiveBTN(false, this, "AGUARDE <span class='animated-dots'></span>", 'COMPLETAR');
        }).catch(function (error) {
            alert(error);
        })
    });
}
function openTar(selector) {
    $(document).on('click', selector, function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        let tareID = $(this).attr('data-tareID');
        let datos = new FormData();
        datos.append('tareID', tareID);
        datos.append('openTar', 'openTar');
        axios({
            method: "post",
            url: 'finalizar/process.php',
            data: datos,
            headers: { "Content-Type": "multipart/form-data" },
        }).then(function (response) {
            $.notifyClose();
            let data = response.data;
            if (data.status == 'ok') {
                notify(data.Mensaje, 'success', 1000, 'right')
                $('#tableTarUser').DataTable().ajax.reload();
                $('#selectProy').DataTable().ajax.reload();
                $('#tableTareas').DataTable().ajax.reload(null, false);
            } else {
                notify(data.Mensaje, 'danger', 1000, 'right')
            }
        }).then(() => {
        }).catch(function (error) {
            alert(error);
        })
    });
}
function anulaTar(selector) {
    $(document).on('click', selector, function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        let tareID = $(this).attr('data-tareID');
        let datos = new FormData();
        datos.append('tareID', tareID);
        datos.append('anulaTar', 'anulaTar');
        axios({
            method: "post",
            url: 'finalizar/process.php',
            data: datos,
            headers: { "Content-Type": "multipart/form-data" },
        }).then(function (response) {
            $.notifyClose();
            let data = response.data;
            if (data.status == 'ok') {
                notify(data.Mensaje, 'success', 1000, 'right')
                $('#tableTarUser').DataTable().ajax.reload();
                $('#selectProy').DataTable().ajax.reload();
                $('#tableTareas').DataTable().ajax.reload(null, false);
            } else {
                notify(data.Mensaje, 'danger', 1000, 'right')
            }
        }).then(() => {
        }).catch(function (error) {
            alert(error);
        })
    });
}
function select2Val(id, text, selector) {
    var newOption = new Option(text, id, false, false);
    if (text != '') {
        $(selector).append(newOption).trigger('change');
    }
}
var maskBehavior = function (val) {
    val = val.split(":");
    return parseInt(val[0]) > 19 ? "HZ:M0" : "H0:M0";
}
spOptions = {
    onKeyPress: function (val, e, field, options) {
        field.mask(maskBehavior.apply({}, arguments), options);
    },
    translation: {
        'H': { pattern: /[0-2]/, optional: false },
        'Z': { pattern: /[0-3]/, optional: false },
        'M': { pattern: /[0-5]/, optional: false }
    }
};
function classEfect(selector, efect) {
    $(selector).addClass(efect)
    setTimeout(function () {
        $(selector).removeClass(efect)
    }, 500);
}
function classHover(selector, clase) {
    $(selector).hover(
        function () {
            $(this).addClass(clase);
        },
        function () {
            $(this).removeClass(clase);
        }
    );
}
const loadingTable = (selectortable) => {
    $(selectortable + ' td div').removeClass('text-red text-orange text-blue text-green text-dark')
    $(selectortable + ' td div').addClass('bg-light border-0 text-light')
    $(selectortable + ' td button').addClass('invisible')
    $(selectortable + ' td img').addClass('invisible')
    $(selectortable + ' td i').addClass('invisible')
    $(selectortable + ' td span').addClass('invisible')
}
let minmaxDate = (t, f1, f2, fn) => {
    let data = new FormData();
    data.append('t', t);
    data.append('f1', f1);
    data.append('f2', f2);
    axios({
        method: 'post',
        url: 'data/minmaxdate.php',
        data: data,
        headers: { "Content-Type": "multipart/form-data" }
    }).then(function (response) {
        let data = response.data;
        if (data.status == 'ok') {
            let anioMin = data.Mensaje.anio.min;
            let anioMax = data.Mensaje.anio.max;
            let maxFormat = data.Mensaje.fecha.maxFormat;
            let minFormat = data.Mensaje.fecha.minFormat;
            let min = data.Mensaje.fecha.min;
            let max = data.Mensaje.fecha.max;
            let type = ''
            $('#contenedor').prepend(`
            <input type="${type}" class="w100" value="${anioMin}" id="tare_anioMin">
            <input type="${type}" class="w100" value="${anioMax}" id="tare_anioMax">
            <input type="${type}" class="w100" value="${minFormat}" id="tare_minFormat">
            <input type="${type}" class="w100" value="${maxFormat}" id="tare_maxFormat">
            <input type="${type}" class="w100" value="${min}" id="tare_min">
            <input type="${type}" class="w100" value="${max}" id="tare_max">
            `);
        }

    }).then(() => {
        fn
    }).catch(function (error) {
        alert('ERROR minmaxDate\n' + error);
    }).then(function () {

    });
}

const procPend = (reloadTable = false, selectortable) => {
    let proy_info = sessionStorage.getItem(
        location.pathname.substring(1) + "proy_info"
    );
    let p = JSON.parse(proy_info);
    if (p) {
        let data = new FormData();
        data.append('procPendientes', 1);
        data.append('_c', p.reci);
        navigator.sendBeacon('finalizar/process.php', data);
        if (reloadTable) {
            $(selectortable).DataTable().ajax.reload(null, false);
        }
        // alert('Proceso de pendientes finalizado');
    }
}
function goInicio() {
    fetch("routes.php/?page=inicio")
    .then(response => response.text())
    .then(data => {
        sessionStorage.setItem(
            location.pathname.substring(1) + "proy_page",
            "inicio"
        );
        $("#mainTitleBar").html(capitalize('inicio')); // Title
        $("#contenedor").html(data);
    });
}