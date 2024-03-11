$('li').on('shown.bs.dropdown', function () {
    $(this).addClass('bg-light shadow-sm radius')
    $(this).children(".dropdown-menu").addClass("animate__animated animate__fadeIn mt-1");
})
$('li').on('hidden.bs.dropdown', function () {
    $(this).removeClass('bg-light shadow-sm radius')
    $(this).children(".dropdown-menu").removeClass("animate__animated animate__fadeIn mt-1");
})
function formatDate(date) {
    let d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;
    return [year, month, day].join('-');
}
function formatDate2(date) {
    let d = date.split("/")
    return [d[2], d[1], d[0]].join('-');
}
function formatDate3(date) {
    let d = date.split("-")
    return [d[2], d[1], d[0]].join('/');
}
function NombreMesJS(Mes) {
    switch (Mes) {
        case "01":
            var Desc = "Enero"
            break;
        case "02":
            var Desc = "Febrero"
            break;
        case "03":
            var Desc = "Marzo"
            break;
        case "04":
            var Desc = "Abril"
            break;
        case "05":
            var Desc = "Mayo"
            break;
        case "06":
            var Desc = "Junio"
            break;
        case "07":
            var Desc = "Julio"
            break;
        case "08":
            var Desc = "Agosto"
            break;
        case "09":
            var Desc = "Septiembre"
            break;
        case "10":
            var Desc = "Octubre"
            break;
        case "11":
            var Desc = "Noviembre"
            break;
        case "12":
            var Desc = "Diciembre"
            break;
    }
    return Desc;
}
function SumarMes(fecha, cant) {
    var Fecha = moment(fecha).add(cant, 'months').format('YYYY-MM-DD');
    return Fecha
}
function fadeInOnChange(selector, selector2) {
    $(selector).change(function (e) {
        $(selector2).addClass('animate__animated animate__fadeIn')
        setTimeout(function () {
            $(selector2).removeClass('animate__animated animate__fadeIn')
        }, 100);
    });
}
function fadeInOnClick(selector, selector2) {
    $(selector).on("click", function (e) {
        $(selector2).addClass('animate__animated animate__fadeIn')
        setTimeout(function () {
            $(selector2).removeClass('animate__animated animate__fadeIn')
        }, 100);
    });
}
function fadeInOnly(selector2) {
    $(selector2).addClass('animate__animated animate__fadeIn')
    setTimeout(function () {
        $(selector2).removeClass('animate__animated animate__fadeIn')
    }, 1000);
}
function classHover(selector, efect) {
    $(selector).hover(
        function () {
            $(this).addClass(efect);
        },
        function () {
            $(this).removeClass(efect);
        }
    );
}
function classEfect(selector, efect) {
    $(selector).addClass(efect)
    setTimeout(function () {
        $(selector).removeClass(efect)
    }, 1000);
}
function switchClass(selector, add, remove) {
    $(selector).addClass(add)
    $(selector).removeClass(remove)
}

function invisibleIO(selector2) {
    // $(selector2).addClass('invisible')
    setTimeout(function () {
        $(selector2).removeClass('invisible')
    }, 1000);
}
function RadioCheckActive(selector) {
    if ($("selector").is(":checked")) {
        $('selector').addClass('opa9')
    } else {
        $('selector').addClass('opa6')
    };
}
function Modal_XL_LG(selector) {
    $(selector).removeClass('modal-xl')
    $(selector).addClass('modal-lg')
}
function Modal_LG_XL(selector) {
    $(selector).removeClass('modal-lg')
    $(selector).addClass('modal-xl')
}
/**
  * Rellena un número con un carácter específico hasta la longitud deseada.
  *
  * @param {number} num: el número que se va a rellenar.
  * @param {number} largo - La longitud deseada del número rellenado.
  * @param {string} [char='0'] - El carácter utilizado para el relleno. El valor predeterminado es '0'.
  * @returns {string} El número rellenado.
  */
function pad(num, largo, char) {
    char = char || '0';
    num = num + '';
    return num.length >= largo ? num : new Array(largo - num.length + 1).join(char) + num;
}

function CheckedInput(selector) {
    if ($(selector).is(':not(:checked)')) {
        $(selector).prop('checked', true)
    }
}
function UnCheckedInput(selector) {
    $(selector).change(function () {
    });

    if ($(selector).is(':checked')) {
        $(selector).prop('checked', false)
    }
}
function CheckedInputVal(selector, valcheck, valuncheck, namecheck, nameuncheck) {
    if ($(selector).is(':not(:checked)')) {
        if (valuncheck != '') {
            $(selector).val(valuncheck)
        }
        $(selector).attr('name', nameuncheck);
    } else {
        if (valcheck != '') {
            $(selector).val(valcheck)
        }
        $(selector).attr('name', namecheck);
    }
}
function InputVal(selector, val) {
    $(selector).val(val)
}
function CheckedInputValChange(selector, valcheck, valuncheck, namecheck, nameuncheck) {
    $(selector).change(function () {
        if ($(selector).is(':not(:checked)')) {
            if (valuncheck != '') {
                $(selector).val(valuncheck)
            }
            $(selector).attr('name', nameuncheck);
        } else {
            if (valcheck != '') {
                $(selector).val(valcheck)
            }
            $(selector).attr('name', namecheck);
        }
    });
}
function DisabledInput(selector) {
    if ($(selector).is(':not(:disabled)')) {
        $(selector).prop('disabled', true)
    }
}
function UnDisabledInput(selector) {
    if ($(selector).is(':disabled')) {
        $(selector).prop('disabled', false)
    }
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
$(".trash").attr('data-icon', '');
$(".edit").attr('data-icon', '');
$('[data-toggle="tooltip"]').tooltip()

function ShowLoading(e) {
    var div = document.createElement('div');
    var img = document.createElement('img');
    div.innerHTML = '<style>.container{opacity:0.5 !important}</style><div id="ShowLoading" class="pl-2 animate__animated animate__fadeIn fixed-top border border-secondary mx-auto d-flex align-items-center text-white font-weight-bold text-center bg-custom" style="top:30%;width:220px;text-align:center;z-index:1050;height:50px;font-size:1.1em;border-radius:4px; opacity:0.5 !important"><small>&nbsp;&nbsp;&nbsp;&nbsp;Aguarde por favor... <div class="spinner-border spinner-border-sm text-white mx-auto" role="status" aria-hidden="true"></div></small></div>';
    div.style.cssText = '';
    div.appendChild(img);
    document.body.
        appendChild(div); return true;
}
function cargando_table(e) {
    let div = document.createElement('div');
    div.setAttribute("class", "_cargando");
    div.innerHTML = `
    <div><style>.table div{opacity:0.4}</style>
    <div class="animate__animated animate__fadeIn fixed-top mx-auto d-flex align-items-center justify-content-center text-white text-center bg-custom" style="top:30%;width:220px;text-align:center;z-index:1050;height:50px">
        <span class="fontq">Aguarde por favor... 
            <div class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></div>
        </span>
    </div></div>`;
    document.body.appendChild(div); return true;
}
function goBack() {
    window.history.back();
}
$(window).on('load', function () {
    $(".loader").fadeOut("slow");
});

let _homehost = $("#_homehost").val();

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};
function TrimEspacios(data) {
    return data.replace(/ /g, "");
}

function HoraMask(selector) {
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
    return $(selector).mask(maskBehavior, spOptions);
}
let templateData = (data) => {
    if ($(data.html).length === 0) {
        return data.text;
    }
    return $(data.html);
};
function SelectSelect2Ajax(selector, multiple, allowClear, placeholder, minimumInputLength, minimumResultsForSearch, maximumInputLength, selectOnClose, ajax_url, delay, data_array, type) {
    $(selector).select2({
        multiple: multiple,
        language: "es",
        allowClear: allowClear,
        templateResult: templateData,
        placeholder: placeholder,
        minimumInputLength: minimumInputLength,
        minimumResultsForSearch: minimumResultsForSearch,
        maximumInputLength: maximumInputLength,
        selectOnClose: selectOnClose,
        language: {
            noResults: function () {
                return 'No hay resultados..'
            },
            inputTooLong: function (args) {
                var message = 'Máximo ' + maximumInputLength + ' caracteres. Elimine ' + overChars + ' caracter';
                if (overChars != 1) {
                    message += 'es'
                }
                return message
            },
            searching: function () {
                return 'Buscando..'
            },
            errorLoading: function () {
                return 'Sin datos..'
            },
            inputTooShort: function () {
                return 'Ingresar ' + minimumInputLength + ' o mas caracteres'
            },
            maximumSelected: function () {
                return 'Puede seleccionar solo una opción'
            },
            removeAllItems: function () {
                return "Eliminar Selección"
            }
        },
        ajax: {
            url: "/" + $("#_homehost").val() + "/" + ajax_url,
            dataType: "json",
            type: type,
            delay: delay,
            data: function (params) {
                return {
                    q: params.term,
                    data_array
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    }).on("select2:unselecting", function (e) {
        $(this).data('state', 'unselected');
    }).on("select2:open", function (e) {
        if ($(this).data('state') === 'unselected') {
            $(this).removeData('state');
            var self = $(this);
            setTimeout(function () {
                self.select2('close');
            }, 1);
        }
    });
}

function CloseDropdownOnClearSelect2(selector) {
    $(selector).on("select2:unselecting", function (e) {
        $(this).data('state', 'unselected');
    }).on("select2:open", function (e) {
        if ($(this).data('state') === 'unselected') {
            $(this).removeData('state');
            var self = $(this);
            setTimeout(function () {
                self.select2('close');
            }, 1);
        }
    });
}

function SelectSelect2(selector, allowClear, placeholder, minimumInputLength, minimumResultsForSearch, maximumInputLength, selectOnClose) {
    $(selector).select2({
        language: "es",
        allowClear: allowClear,
        placeholder: placeholder,
        minimumInputLength: minimumInputLength,
        minimumResultsForSearch: minimumResultsForSearch,
        maximumInputLength: maximumInputLength,
        selectOnClose: selectOnClose,
        language: {
            noResults: function () {
                return 'No hay resultados..'
            },
            inputTooLong: function (args) {
                var message = 'Máximo ' + opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                if (overChars != 1) {
                    message += 'es'
                }
                return message
            },
            searching: function () {
                return 'Buscando..'
            },
            errorLoading: function () {
                return 'Sin datos..'
            },
            inputTooShort: function () {
                return 'Ingresar ' + opt2["MinLength"] + ' o mas caracteres'
            },
            maximumSelected: function () {
                return 'Puede seleccionar solo una opción'
            },
            removeAllItems: function () {
                return "Eliminar Selección"
            }
        },
    }).on("select2:unselecting", function (e) {
        $(this).data('state', 'unselected');
    }).on("select2:open", function (e) {
        if ($(this).data('state') === 'unselected') {
            $(this).removeData('state');
            var self = $(this);
            setTimeout(function () {
                self.select2('close');
            }, 1);
        }
    });
}
function vjs() {
    return $('#_vjs').val()
}
$('.requerido').html('(*)')

function respuesta_form(selector, Mensaje, alert) {
    let respuesta_form = $(selector).html('<div class="mt-3 animate__animated animate__fadeInDown alert alert-' + alert + ' alert-dismissible fontq p-3 fw5" role="alert">' + Mensaje + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
    setTimeout(function () {
        $('.alert-' + alert).removeClass('fadeInDown')
        $('.alert-' + alert).addClass('fadeOutUp')
        setTimeout(function () {
            $(selector).html('')
        }, 3500);
    }, 1000);
    return respuesta_form
}

function notify(Mensaje, type, delay, NotifAlign, from) {

    var offset = ($(window).width() < 769) ? 0 : 20;

    $.notify({
        // options
        message: Mensaje
    }, {
        // settings
        type: type,  /** success, danger, warning, secondary, light, etc */
        z_index: 9999,
        delay: delay, /** ej 2000 */
        offset: offset,
        mouse_over: 'pause',
        placement: {
            from: from,
            align: NotifAlign /** orientación de la notificación */
        },
        animate: {
            enter: 'animate__animated animate__fadeInDown',
            exit: 'animate__animated animate__fadeOut'
        },
    });
}
function focusEndText(input) {
    let textInput = $(input);
    let strLength = textInput.val().length;
    textInput.focus();
    textInput[0].setSelectionRange(strLength, strLength);
}
function select2Ajax(selector, placeholder, clear, selclose, url) {
    $(selector).select2({
        placeholder: placeholder,
        allowClear: clear,
        selectOnClose: selclose,
        minimumResultsForSearch: 10,
        language: {
            noResults: function () {
                return 'No hay resultados..'
            },
            searching: function () {
                return 'Buscando..'
            },
            errorLoading: function () {
                return 'Sin datos..'
            }
        },
        ajax: {
            url: url,
            dataType: "json",
            type: "GET",
            data: function (params) {
                return {
                    q: params.term,
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    })
}
function select2Simple(selector, placeholder, clear, selclose, width = '') {
    $(selector).select2({
        placeholder: placeholder,
        minimumResultsForSearch: 10,
        allowClear: clear,
        selectOnClose: selclose,
        width: width
    })
}
function Select2Value(id, text, selector) {
    var newOption = new Option(text, id, false, false);
    if (text != '') {
        $(selector).append(newOption).trigger('change');
    }
}
function CheckUncheck(selector_check, selector_uncheck, selectorcheckbox, classactive) {
    $(selector_check).click(function (e) {
        $(selectorcheckbox).prop('checked', true)
        $(selectorcheckbox).parents('tr').addClass('table-active')
    });
    $(selector_uncheck).click(function (e) {
        $(selectorcheckbox).prop('checked', false)
        $(selectorcheckbox).parents('tr').removeClass('table-active')
    });
}
function singleDatePicker(selector, opens, drop, maxDate = '') {
    $(selector).attr('autocomplete', 'off')
    $(selector).daterangepicker({
        singleDatePicker: true,
        opens: opens,
        drops: drop,
        autoUpdateInput: true,
        autoApply: false,
        buttonClasses: "btn btn-sm fontq",
        applyButtonClasses: "btn-custom fw4 px-3 opa8",
        cancelClass: "btn-link fw4 text-gris",
        maxDate: maxDate,
        ranges: {
            'Hoy': [moment(), moment()],
            'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        },
        locale: {
            format: "DD/MM/YYYY",
            separator: " al ",
            applyLabel: "Aplicar",
            cancelLabel: "Cancelar",
            fromLabel: "Desde",
            toLabel: "Para",
            customRangeLabel: "Personalizado",
            weekLabel: "Sem",
            daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
            "monthNames": ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            firstDay: 1,
            alwaysShowCalendars: true,
            applyButtonClasses: "text-white bg-custom",
        },
    });
    // $(selector).on('show.daterangepicker', function (ev, picker) {
    //     $('.drp-buttons').show()
    // });
    // $(selector).on('hide.daterangepicker', function (ev, picker) {
    //     $('.drp-buttons').hide()
    // });
}
function singleDatePickerValue(selector, opens, drop, value) {
    $(selector).daterangepicker({
        singleDatePicker: true,
        opens: opens,
        drops: drop,
        startDate: value,
        endDate: value,
        autoApply: false,
        buttonClasses: "btn btn-sm fontq",
        applyButtonClasses: "btn-custom fw4 px-3 opa8",
        ranges: {
            'Hoy': [moment(), moment()],
            'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        },
        locale: {
            format: "DD/MM/YYYY",
            customRangeLabel: "Personalizado",
            weekLabel: "Sem",
            daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
            monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            firstDay: 1,
        },
    });
}
function dobleDatePicker(selector, opens, drop) {
    $(selector).daterangepicker({
        singleDatePicker: false,
        opens: opens,
        drops: drop,
        autoUpdateInput: true,
        buttonClasses: "btn btn-sm fontq",
        applyButtonClasses: "btn-custom fw4 px-3 opa8",
        cancelClass: "btn-link fw4 text-gris",
        linkedCalendars: false,
        // ranges: {
        //     'Hoy': [moment(), moment()],
        //     'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        // },
        locale: {
            format: "DD/MM/YYYY",
            separator: " al ",
            applyLabel: "Aplicar",
            cancelLabel: "Cancelar",
            fromLabel: "Desde",
            toLabel: "Para",
            customRangeLabel: "Personalizado",
            weekLabel: "Sem",
            daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
            "monthNames": ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            firstDay: 1,
            alwaysShowCalendars: true,
            applyButtonClasses: "text-white bg-custom",
        },

    });
}
function dobleDatePickerAuto(selector, opens, drop) {

    $(selector).daterangepicker({
        singleDatePicker: false,
        opens: opens,
        drops: drop,
        autoUpdateInput: true,
        autoApply: true,
        linkedCalendars: false,
        ranges: {
            'Esta semana': [moment().day(1), moment().day(7)],
            'Próxima Semana': [moment().add(1, 'week').day(1), moment().add(1, 'week').day(7)],
            'Este mes': [moment().startOf('month'), moment().endOf('month')],
            'Próximo Mes': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
        },
        locale: {
            format: "DD/MM/YYYY",
            separator: " al ",
            applyLabel: "Aplicar",
            cancelLabel: "Cancelar",
            fromLabel: "Desde",
            toLabel: "Para",
            customRangeLabel: "Personalizado",
            weekLabel: "Sem",
            daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
            "monthNames": ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            firstDay: 1,
            alwaysShowCalendars: true,
            applyButtonClasses: "text-white bg-custom",
        },
    });
}
function CheckSesion() {

    let _homehost = document.getElementById('_homehost')

    if (_homehost == null) { return false }
    if (_homehost.value == '') { return false }

    let _referer = document.getElementById('_referer')
    let _url = "/" + _homehost.value + "/sesion.php"
    let _sesion = document.getElementById('_sesion')

    axios.get(_url).then(function (response) {
        if (response.status != 200) { return false }

        let status = response.data.status ?? false;
        if (status == '') { return false }

        if (status == 'sesion') {
            _sesion.value = '1'
            if (_referer != null) {
                if (_referer.value != '') {
                    window.location.href = "/" + _homehost.value + "/login/?l=" + _referer.value
                } else {
                    window.location.href = "/" + _homehost.value + "/login/"
                }
            } else {
                window.location.href = "/" + _homehost.value + "/login/"
            }
        } else {
            _sesion.value = '0'
        }
    }).catch(function (error) {
        console.log(error);
    });
}
function Procesar(FechaIni, FechaFin, LegaIni, LegaFin) {
    $.ajax({
        type: 'POST',
        dataType: "json",
        url: "/" + $("#_homehost").val() + "/procesarajax.php",
        'data': {
            FechaIni: FechaIni,
            FechaFin: FechaFin,
            LegaIni: LegaIni,
            LegaFin: LegaFin,
        },
        context: document.body
    }).done(function (data) {
        if (data.status == 'ok') {
            console.log('ok');
        } else {
            console.log('error');
        }
    });
}
function HoraMin(Hora) {
    if (Hora) {
        let hora = Hora;
        let parts = hora.split(':');
        let total = parseInt(parts[0]) * 60 + parseInt(parts[1]);
        return total
    } else {
        return 0
    }

}
function getHTML(url, selector) {
    $.ajax({
        url: url + '?v=' + $('#_vjs').val(),
        type: "get",
        DataType: 'html'
    }).done(function (data) {
        $(selector).html(data);
    })
}
/**
 * Función que se ejecuta al abrir el select2.
 * Verifica la sesión y detiene la propagación del evento.
 */
function onOpenSelect2() {
    $('select').on('select2:opening', function (e) {
        CheckSesion()
        e.stopImmediatePropagation();
    });
}
function clearInput(selector) {
    $(selector).val('');
    $(selector).trigger('change');
}
setInterval(() => {
    if (document.visibilityState === 'visible') {
        CheckSesion();
    };
}, 60000);

/**
 * @param String name
 * @return String
 */
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
function launchFullScreen(element) {
    if (element.requestFullScreen) {
        element.requestFullScreen();
    } else if (element.mozRequestFullScreen) {
        element.mozRequestFullScreen();
    } else if (element.webkitRequestFullScreen) {
        element.webkitRequestFullScreen();
    }
    // example: launchFullScreen(document.documentElement)
}
function cancelFullScreen() {
    if (document.cancelFullScreen) {
        document.cancelFullScreen();
    } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
    } else if (document.webkitCancelFullScreen) {
        document.webkitCancelFullScreen();
    }
}

let formDataVisibilityChange = new FormData();
formDataVisibilityChange.append('state', '1');
navigator.sendBeacon('', formDataVisibilityChange)

document.addEventListener('visibilitychange', function logData() {
    let formData = new FormData();
    if (document.visibilityState === 'visible') {
        formData.append('state', '1');
    } else {
        formData.append('state', '2');
    }
    navigator.sendBeacon('', formData);
});
// console.log($('#_host').val()+'/'+$('#_homehost').val());

const getFicNovHor = (selector, date1, date2, lega) => {
    let = d = new FormData()
    d.append('fechaIni', date1)
    d.append('fechaFin', date2)
    d.append('legajo', lega)
    axios({
        method: 'POST',
        url: $('#_host').val() + '/' + $('#_homehost').val() + '/data/getFicNovHor.php',
        data: d
    }).then(function (response) {
        if (response.data.data) {
            let container = document.getElementById(selector)
            container.innerHTML = `<div class="fontq mb-2 alert alert-info alertDataClean">La siguiente información posterior a la fecha de egreso será eliminada.</div>`

            container.innerHTML += `
            <div class="p-2">
                <table class="table w-100 animate__animated animate__fadeIn">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th class="text-center">Fichadas</th>
                            <th class="text-center">Novedades</th>
                            <th class="text-center">Horas</th>
                            <th class="text-center">Otras Nove</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyData"></tbody>
                </table>
            </div>
        `
            response.data.data.forEach(function callback(value, index) {
                // console.log(`${value.Lega}`);
                let tbodyData = document.getElementById('tbodyData');
                tbodyData.innerHTML += `
            <tr>
                <td>${moment(value.Fech).format("DD/MM/YYYY")}</td>
                <td class="text-center">${(value.Fich.length) ? value.Fich.length : '-'}</td>
                <td class="text-center">${(value.Nove.length) ? value.Nove.length : '-'}</td>
                <td class="text-center">${(value.Horas.length) ? value.Horas.length : '-'}</td>
                <td class="text-center">${(value.ONove.length) ? value.ONove.length : '-'}</td>
            </tr>
            `
            });
        } else {
            $('.alertDataClean').addClass('d-none')
        }

    }).catch(function (error) {
        console.log(error);
    });
}

const getSelectorVal = (selector) => {
    if (!selector) return false
    let s = ''
    if (document.querySelector(selector)) {
        s = document.querySelector(selector).value
        return s
    } else {
        s = ''
        console.log('No existe el selector ' + selector);
        return s
    }
}
const colorFichada = (value) => {
    textColor = ''
    switch (value['Tipo']) {
        case 'Normal':
            textColor = ''
            break;
        case 'Manual':
            textColor = 'text-primary'
            break;
        default:
            textColor = ''
            break;
    }
    textColor = (value['Esta'] == 'Modificada') ? 'text-danger' : textColor
    return textColor
}
const getFichada = (array, fichada) => {
    let datacol = '-';
    let textColor = '';
    let horaOriginal = ''
    if (fichada == 'primera') {
        let countOfItems = array.length
        if (countOfItems) {
            if (array[0]['Esta'] == 'Modificada') {
                horaOriginal = '. Hora original: ' + array[0]['Hora']
            }
            textColor = colorFichada(array[0])
            datacol = '<span data-titlel="(# 1) ' + array[0]['HoRe'] + ' ' + array[0]['Tipo'] + ' ' + array[0]['Esta'] + horaOriginal + '"><span class="ls1">' + array[0]['HoRe'] + '</span></span>'
        }
        return '<div class="' + textColor + '">' + datacol + '</div>';
    }
    if (fichada == 'ultima') {
        let countOfItems = array.length
        if (countOfItems > 1) {
            countOfItems = array.length - 1
            item = countOfItems + 1
            if (array[countOfItems]['Esta'] == 'Modificada') {
                horaOriginal = '. Hora original: ' + array[countOfItems]['Hora']
            }
            textColor = colorFichada(array[countOfItems])
            datacol = '<span data-titlel="(# ' + item + ') ' + array[countOfItems]['HoRe'] + ' ' + array[countOfItems]['Tipo'] + ' ' + array[countOfItems]['Esta'] + horaOriginal + '"><span class="ls1">' + array[countOfItems]['HoRe'] + '</span></span>'
        }
        return '<div class="' + textColor + '">' + datacol + '</div>';
    }
    return '-';
}
const getHorario = (array, Labo, Feri) => {
    if (!array) return false
    let Tur = array;
    let Turno = `${Tur['ent']} a ${Tur['sal']}`;
    Turno = (Labo == '0') ? 'Franco' : Turno;
    Turno = (Feri == '1') ? 'Feriado' : Turno;
    let Descanso = `Descanso: ${Tur['des']}`;
    Descanso = (Labo == '0') ? '' : Descanso;
    Descanso = (Feri == '1') ? '' : Descanso;
    return `<span title="${Descanso}">${Turno}</span>`;
}
function CateNov(value) {
    switch (value) {
        case '0':
            str = 'Primaria';
            break;
        case '1':
            str = 'Adicional';
            break;
        case '2':
            str = 'Forzada';
            break;
        default:
            str = value;
            break;
    }
    return str;
}
const loaderIn = (selector, state) => {
    if (!selector) return;
    if (state) {
        $(selector).addClass('loader-in')
    } else {
        $(selector).removeClass('loader-in')
    }
}