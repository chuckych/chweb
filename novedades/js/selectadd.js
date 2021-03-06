/** Select */
// $(document).ready(function () {

HoraMask('.HoraMask')

$(document).on('click', '#addNov', function (e) {
    var AnioMin = parseFloat($('#AnioMin').val());
    var AnioMax = parseFloat($('#AnioMax').val());
    $('#_draddNov').daterangepicker({
        singleDatePicker: false,
        showDropdowns: false,
        minYear: AnioMin,
        maxYear: AnioMax,
        showWeekNumbers: false,
        autoUpdateInput: true,
        opens: "center",
        drops: "down",
        autoApply: false,
        alwaysShowCalendars: true,
        linkedCalendars: false,
        buttonClasses: "btn btn-sm fontq",
        applyButtonClasses: "btn-custom fw4 px-3 opa8",
        cancelClass: "btn-link fw4 text-gris",
        ranges: {
            'Hoy': [moment(), moment()],
            // 'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Esta semana': [moment().day(1), moment().day(7)],
            'Ultima Semana': [moment().subtract(1, 'week').day(1), moment().subtract(1, 'week').day(7)],
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
            monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            firstDay: 1,
            alwaysShowCalendars: true,
            applyButtonClasses: "btn-custom fw5 px-3 opa8",
        },
    });
    $('#_draddNov').on('apply.daterangepicker', function (ev, picker) {
        $("#range").submit();
    });
    $('#Encabezado').html('Ingresar Novedades')

    $('#divTablas').addClass('d-none');
    $('#divaddNov').removeClass('d-none');
    fadeInOnly('#divaddNov');

    var opt2 = { MinLength: "0", SelClose: false, MaxInpLength: "10", delay: "250", allowClear: true };

    SelectSelect2('.select2Tipo', true, "Tipo de Personal", 0, -1, 10, false)

    $(".sel_empresa").select2({
        multiple: false,
        language: "es",
        allowClear: true,
        placeholder: 'Empresa',
        minimumInputLength: '0',
        minimumResultsForSearch: '5',
        maximumInputLength: '10',
        selectOnClose: true,
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
        ajax: {
            url: "/" + $("#_homehost").val() + "/data/getPerEmpresas.php",
            dataType: "json",
            type: 'GET',
            delay: '250',
            data: function (params) {
                return {
                    q: params.term,
                    Tipo: $("#aTipo").val(),
                    Plan: $("#aPlan").val(),
                    Sect: $("#aSect").val(),
                    Sec2: $("#aSec2").val(),
                    Grup: $("#aGrup").val(),
                    Sucur: $("#aSucur").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    })

    $(".sel_plantas").select2({
        multiple: false,
        language: "es",
        allowClear: true,
        placeholder: 'Planta',
        minimumInputLength: '0',
        minimumResultsForSearch: '5',
        maximumInputLength: '10',
        selectOnClose: true,
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
        ajax: {
            url: "/" + $("#_homehost").val() + "/data/getPerPlantas.php",
            dataType: "json",
            type: 'GET',
            delay: '250',
            data: function (params) {
                return {
                    q: params.term,
                    Tipo: $("#aTipo").val(),
                    Emp: $("#aEmp").val(),
                    Sect: $("#aSect").val(),
                    Sec2: $("#aSec2").val(),
                    Grup: $("#aGrup").val(),
                    Sucur: $("#aSucur").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    })

    $(".sel_sectores").select2({
        multiple: false,
        language: "es",
        allowClear: true,
        placeholder: 'Sector',
        minimumInputLength: '0',
        minimumResultsForSearch: '5',
        maximumInputLength: '10',
        selectOnClose: true,
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
        ajax: {
            url: "/" + $("#_homehost").val() + "/data/getPerSectores.php",
            dataType: "json",
            type: 'GET',
            delay: '250',
            data: function (params) {
                return {
                    q: params.term,
                    Tipo: $("#aTipo").val(),
                    Emp: $("#aEmp").val(),
                    Plan: $("#aPlan").val(),
                    Sec2: $("#aSec2").val(),
                    Grup: $("#aGrup").val(),
                    Sucur: $("#aSucur").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    })

    $(".sel_seccion").select2({
        multiple: false,
        language: "es",
        allowClear: true,
        placeholder: 'Sección',
        minimumInputLength: '0',
        minimumResultsForSearch: '5',
        maximumInputLength: '10',
        selectOnClose: true,
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
        ajax: {
            url: "/" + $("#_homehost").val() + "/data/getPerSecciones.php",
            dataType: "json",
            type: 'GET',
            delay: '250',
            data: function (params) {
                return {
                    q: params.term,
                    Sect: $("#aSect").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    })

    $(".sel_grupos").select2({
        multiple: false,
        language: "es",
        allowClear: true,
        placeholder: 'Grupo',
        minimumInputLength: '0',
        minimumResultsForSearch: '5',
        maximumInputLength: '10',
        selectOnClose: true,
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
        ajax: {
            url: "/" + $("#_homehost").val() + "/data/getPerGrupos.php",
            dataType: "json",
            type: 'GET',
            delay: '250',
            data: function (params) {
                return {
                    q: params.term,
                    Tipo: $("#aTipo").val(),
                    Emp: $("#aEmp").val(),
                    Plan: $("#aPlan").val(),
                    Sect: $("#aSect").val(),
                    Sec2: $("#aSec2").val(),
                    Sucur: $("#aSucur").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    })

    $(".sel_sucursal").select2({
        multiple: false,
        language: "es",
        allowClear: true,
        placeholder: 'Sucursal',
        minimumInputLength: '0',
        minimumResultsForSearch: '5',
        maximumInputLength: '10',
        selectOnClose: true,
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
        ajax: {
            url: "/" + $("#_homehost").val() + "/data/getPerSucursales.php",
            dataType: "json",
            type: 'GET',
            delay: '250',
            data: function (params) {
                return {
                    q: params.term,
                    Tipo: $("#aTipo").val(),
                    Emp: $("#aEmp").val(),
                    Plan: $("#aPlan").val(),
                    Sect: $("#aSect").val(),
                    Sec2: $("#aSec2").val(),
                    Grup: $("#aGrup").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    })

    $(".sel_causa").select2({
        multiple: false,
        language: "es",
        allowClear: true,
        placeholder: 'Causa',
        minimumInputLength: '0',
        minimumResultsForSearch: '5',
        maximumInputLength: '10',
        selectOnClose: true,
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
        ajax: {
            url: "/" + $("#_homehost").val() + "/data/getListNoveCausa.php",
            dataType: "json",
            type: 'POST',
            delay: '250',
            data: function (params) {
                return {
                    q: params.term,
                    NovCNove: $("#aFicNove").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    })

    SelectSelect2Ajax(".sel_novedad", false, true, 'Novedad', 0, 5, 10, true, "data/getListNovedad.php", '250', '', 'GET')

    CloseDropdownOnClearSelect2('.sel_causa')
    CloseDropdownOnClearSelect2('.sel_empresa')
    CloseDropdownOnClearSelect2('.sel_plantas')
    CloseDropdownOnClearSelect2('.sel_sectores')
    CloseDropdownOnClearSelect2('.sel_seccion')
    CloseDropdownOnClearSelect2('.sel_grupos')
    CloseDropdownOnClearSelect2('.sel_sucursal')

    $(".sel_seccion").prop("disabled", true);
    $(".sel_causa").prop("disabled", true);
    $('.sel_sectores').on('select2:select', function (e) {
        $(".sel_seccion").prop("disabled", false);
        $("#select_seccion").removeClass("d-none");
        $('.sel_seccion').val(null).trigger('change');
    });
    $('.sel_sectores').on('select2:unselecting', function (e) {
        $('.sel_seccion').val(null).trigger('change');
        $(".sel_seccion").prop("disabled", true);
        setTimeout(function () {
            $('.check').prop('checked', true)
        }, 1000);
    });
    $('.sel_novedad').on('select2:select', function (e) {
        $(".sel_causa").prop("disabled", false);
        $("#select_causa").removeClass("d-none");
        fadeInOnly("#select_causa")
        $('.sel_causa').val(null).trigger('change');
        $('.FicHoras').focus()
        $('#SelNovedad').val($('.sel_novedad' + ' ' + ':selected').text()).trigger('change');
    });
    $('.sel_novedad').on('select2:unselecting', function (e) {
        $('.sel_causa').val(null).trigger('change');
        $(".sel_causa").prop("disabled", true);
        $("#select_causa").addClass("d-none");
    });
    $('.sel_novedad').on('select2:opening', function (e) {
        setTimeout(() => {
            $(".select2-search__field").focus();
        }, 500);
    });
    $('#Cuenta').val(0)
    function SumaCuenta(selector) {
        var cuenta = parseFloat($(selector).val())
        var cuenta1 = 1
        var SumaCuenta = parseFloat(cuenta + cuenta1)
        $(selector).val(0)
        $(selector).val(SumaCuenta)
    }
    function RestaCuenta(selector) {
        var cuenta = parseFloat($(selector).val())
        var cuenta1 = 1
        var RestaCuenta = parseFloat(cuenta - cuenta1)
        $(selector).val(0)
        $(selector).val(RestaCuenta)
    }
    function textoSelected(slectjs, idselec) {
        $(slectjs).on('select2:select', function (e) {
            var selected = slectjs + ' ' + ':selected';
            var texto = $(selected).text();
            $(idselec).val(texto).trigger('change');
            SumaCuenta('#Cuenta')
            GetPers.ajax.reload();
        });
    }
    function UnSelected(slectjs) {
        $(slectjs).on('select2:unselecting', function (e) {
            RestaCuenta('#Cuenta')
            GetPers.ajax.reload();
        });
    }

    textoSelected('.sel_empresa', '#SelEmpresa');
    textoSelected('.sel_plantas', '#SelPlanta');
    textoSelected('.sel_sectores', '#SelSector');
    textoSelected('.sel_seccion', '#SelSeccion');
    textoSelected('.sel_grupos', '#SelGrupo');
    textoSelected('.sel_sucursal', '#SelSucursal');

    UnSelected('.sel_empresa');
    UnSelected('.sel_plantas');
    UnSelected('.sel_sectores');
    UnSelected('.sel_seccion');
    UnSelected('.sel_grupo');
    UnSelected('.sel_sucursal');

    $("#aTipo").change(function () {
        $('.sel_sucursal').val(null).trigger("change");
        $('#SelSucursal').val(null).trigger("change");
        $('.sel_grupos').val(null).trigger("change");
        $('#SelGrupo').val(null).trigger("change");
        $('.sel_seccion').val(null).trigger("change");
        $('#SelSeccion').val(null).trigger("change");
        $('.sel_sectores').val(null).trigger("change");
        $('.sel_seccion').val(null).trigger("change");
        $('#SelSector').val(null).trigger("change");
        $('#SelSeccion').val(null).trigger("change");
        $('.sel_personal').val(null).trigger("change");
        $('.sel_plantas').val(null).trigger("change");
        $('#SelPlanta').val(null).trigger("change");
        $('.sel_empresa').val(null).trigger("change");
        $('#SelEmpresa').val(null).trigger("change");
        GetPers.ajax.reload();
        setTimeout(function () {
            $('.check').prop('checked', true)
        }, 1000);
        $('#Cuenta').val(0)
    });
});

$('.check').prop('disabled', true)
$('#TipoIngreso').val(1);
$("#TipoIngreso1").change(function () {
    if ($("#TipoIngreso1").is(":checked")) {
        $('#TipoIngreso').val(1)
        $('.check').prop('disabled', true)
    }
});
$("#TipoIngreso2").change(function () {
    if ($("#TipoIngreso2").is(":checked")) {
        $('#TipoIngreso').val(2)
        $('.check').prop('disabled', false)
    }
});
/** Variables para las notificaciones de pantalla */
var NotifDelay = 2000;
var NotifOffset = 0;
var NotifOffsetX = 0;
var NotifOffsetY = 0;
var NotifZindex = 9999;
var NotifMouseOver = 'pause'
var NotifEnter = 'animate__animated animate__fadeInDown';
var NotifExit = 'animate__animated animate__fadeOutUp';
var NotifAlign = 'center';

ActiveBTN(false, "#submit", 'Ingresando', 'Ingresar')

function GetLog(archivo) {
    var LOG_URL = archivo;
    $.ajax({
        dataType: "text",
        url: LOG_URL,
        success: function (data) {
            $("#respuetatext").html(data);
        },
        error: function () {
        }
    });
}
$(".alta_novedad").bind("submit", function (e) {
    e.preventDefault();

    var now = $.now()

    function myTimer() {
        GetLog("../novedades/logs/Ingreso_" + now + ".log");
    }
    var myVar = setInterval(myTimer, 1500);
    function myStopFunction() {
        clearInterval(myVar);
    }
    $.ajax({
        type: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize()
            + "&alta_novedad=" + true
            + "&now=" + now,
        // async : false,
        beforeSend: function (data) {
            // console.log(data);
            ActiveBTN(true, "#submit", 'Ingresando', 'Ingresar')
            $("#respuetatext").html("Inicio de Ingreso Novedades");
            $("#respuesta").addClass("alert-info");
            $("#respuesta").removeClass("d-none");
            fadeInOnly("#respuesta")
            $("#respuesta").removeClass("alert-success");
            $("#respuesta").removeClass("alert-danger");
            setTimeout(() => {
                myTimer()
            }, 2000);
        },
        success: function (data) {
            if (data.status == "ok") {
                myStopFunction()
                cleanAll()
                $("#respuetatext").html("Fin de Ingreso de Novedades");
                ActiveBTN(false, "#submit", 'Ingresando', 'Ingresar')
                $("#respuesta").removeClass("alert-info");
                $("#respuesta").removeClass("alert-danger");
                $("#respuesta").addClass("alert-success");
                fadeInOnly("#respuesta")
                // $('.check').prop('checked', false)
                $.notify(`<span class='fonth fw4'><span class="">${data.dato}</span></span>`, {
                    type: 'success',
                    z_index: NotifZindex,
                    delay: NotifDelay,
                    offset: NotifOffset,
                    mouse_over: NotifMouseOver,
                    placement: {
                        align: NotifAlign
                    },
                    animate: {
                        enter: NotifEnter,
                        exit: NotifExit
                    }
                });
                setTimeout(() => {
                    $("#respuetatext").html("");
                    $("#respuesta").addClass("d-none");
                }, 2000);
                GetPers.ajax.reload();
            } else {
                $("#respuetatext").html("");
                myStopFunction()
                // $("#respuetatext").removeClass("animate__animated animate__fadeIn");
                ActiveBTN(false, "#submit", 'Ingresando', 'Ingresar')
                $("#respuesta").removeClass("alert-success");
                $("#respuesta").removeClass("alert-info");
                $("#respuesta").removeClass("alert-danger");
                $.notify(`<span class='fonth fw4'><span class="">${data.dato}</span></span>`, {
                    type: 'danger',
                    z_index: NotifZindex,
                    delay: NotifDelay,
                    offset: NotifOffset,
                    mouse_over: NotifMouseOver,
                    placement: {
                        align: NotifAlign
                    },
                    animate: {
                        enter: NotifEnter,
                        exit: NotifExit
                    }
                });
                // GetPers.ajax.reload();
            }
        },
        error: function (jqXHR, textStatus) {
            myStopFunction()
            if (jqXHR.status === 0) {
                var error = ('No hay Conexión');
            } else if (jqXHR.status == 404) {
                var error = ('No se encontró la página solicitada [404]');
            } else if (jqXHR.status == 500) {
                var error = ('Error de servidor interno [500].');
            } else if (textStatus === 'parsererror') {
                var error = ('Error de análisis JSON solicitado.');
            } else if (textStatus === 'timeout') {
                var error = ('Error de tiempo de espera.');
            } else if (textStatus === 'abort') {
                var error = ('Solicitud cancelada.');
            } else {
                var error = ('Error no detectado: ' + jqXHR.responseText);
            }
            $("#respuetatext").html("");
            // $("#respuetatext").removeClass("animate__animated animate__fadeIn");
            ActiveBTN(false, "#submit", 'Ingresando', 'Ingresar')
            $("#respuesta").removeClass("alert-success");
            $("#respuesta").removeClass("alert-info");
            $("#respuesta").removeClass("alert-danger");
            $.notify("<span class='fonth fw4'><span class=''>" + error + "</span></span>", {
                type: 'danger',
                z_index: NotifZindex,
                delay: NotifDelay,
                offset: NotifOffset,
                mouse_over: NotifMouseOver,
                placement: {
                    align: NotifAlign
                },
                animate: {
                    enter: NotifEnter,
                    exit: NotifExit
                }
            });
        }
    });
    e.stopImmediatePropagation();
});

$("#trash_allFilter").on("click", function () {
    cleanAll()
    // GetPers.search(this.value).draw();
    GetPers.page.len(10).draw()
});

function cleanAll() {
    $('#Cuenta').val(0)
    $('#SelEmpresa').val(null).trigger("change");
    $('#SelPlanta').val(null).trigger("change");
    $('#SelSector').val(null).trigger("change");
    $('#SelSeccion').val(null).trigger("change");
    $('#SelGrupo').val(null).trigger("change");
    $('#SelSucursal').val(null).trigger("change");
    $('.sel_plantas').val(null).trigger("change");
    $('.sel_empresa').val(null).trigger("change");
    $('.sel_sectores').val(null).trigger("change");
    $('.sel_seccion').val(null).trigger("change");
    $('.sel_grupos').val(null).trigger("change");
    $('.sel_sucursal').val(null).trigger("change");
    $('.sel_personal').val(null).trigger("change");
    $('.sel_novedad').val(null).trigger("change");
    $(".sel_seccion").prop("disabled", true);
    $("#aFicCate").prop("checked", false);
    $("#aLaboral").prop("checked", false);
    $("#aFicJust").prop("checked", false);
    $("#aFicObse").val(null).trigger("change");
    $("#aFicHoras").val(null).trigger("change");
    $("#respuetatext").removeClass("animate__animated animate__fadeIn");
    $("#respuetatext").html("");
    ActiveBTN(false, "#submit", 'Ingresando', 'Ingresar')
    $("#respuesta").removeClass("alert-success");
    $("#respuesta").removeClass("alert-danger");
    $("#respuesta").removeClass("alert-info");
    $('.sel_causa').val(null).trigger('change');
    $(".sel_causa").prop("disabled", true);
    $("#select_causa").addClass("d-none");
}
$('#Personal-select-all').addClass('check')
var GetPers = $('#GetPers').DataTable({
    "initComplete": function (settings, json) {
        $('div.loader').remove();
    },
    "drawCallback": function (settings) {
        var pagIni = (settings._iDisplayStart);
        var pagFin = (settings._iDisplayLength);
        $('#pagIni').val(pagIni)
        $('#pagFin').val(pagFin)
        setTimeout(() => {
            $('.check').prop('checked', true)
            $('#divTablePers').removeClass('d-none')
            if ($('#TipoIngreso').val() == 1) {
                $('.check').prop('disabled', true)
            }
        }, 500);
    },
    // lengthMenu: [[10, 50, 100, 200, 300, 400], [10, 50, 100, 200, 300, 400]],
    bProcessing: true,
    // "lengthChange": false,
    serverSide: true,
    deferRender: true,
    searchDelay: 1500,
    ajax: {
        url: "../personal/?p=array_personal.php",
        type: "GET",
        "data": function (data) {
            data.Tipo = $("#aTipo").val();
            data.Emp = $("#aEmp").val();
            data.Plan = $("#aPlan").val();
            data.Sect = $("#aSect").val();
            data.Sec2 = $("#aSec2").val();
            data.Grup = $("#aGrup").val();
            data.Sucur = $("#aSucur").val();
            data._c = $("#_c").val();
            data._r = $("#_r").val();
            data.Modulo = "Cierres",
                data.NoPag = true
        },
        error: function () {
            $("#GetPers_processing").css("display", "none");
        },
    },
    columns: [
        {
            "class": "align-middle animate__animated animate__fadeIn w10",
            "data": 'check'
        },
        {
            "class": "align-middle animate__animated animate__fadeIn w80",
            "data": 'pers_legajo2'
        },
        {
            "class": "align-middle animate__animated animate__fadeIn w300",
            "data": 'pers_nombre2'
        },

    ],
    scrollY: '335px',
    scrollX: true,
    // scrollCollapse: false,
    paging: false,
    responsive: false,
    searching: true,
    info: true,
    ordering: false,
    language: {
        "url": "../js/DataTableSpanishShort.json"
    },
});
GetPers.on('page.dt', function () {
    setTimeout(function () {
        $('.check').prop('checked', true)
        $("#EliminaCierre").prop('checked', false)
    }, 1000);
});
GetPers.on('page', function () {
    $('.check').prop('checked', false);
});
// Handle click on "Select all" control
$('#Personal-select-all').on('click', function () {
    // Check/uncheck all checkboxes in the GetPers
    var rows = GetPers.rows({ 'search': 'applied' }).nodes();
    $('.check', rows).prop('checked', this.checked);
});
// Handle click on checkbox to set state of "Select all" control
$('#GetPers tbody').on('change', '.check', function () {
    // If checkbox is not checked
    if (!this.checked) {
        var el = $('#Personal-select-all').get(0);
        // If "Select all" control is checked and has 'indeterminate' property
        if (el && el.checked && ('indeterminate' in el)) {
            // Set visual state of "Select all" control 
            // as 'indeterminate'
            el.indeterminate = true;
        }
    }
});

$("#Cuenta").change(function () {
    // if ($('#Cuenta').val() > 0) {
    GetPers()
    // }
});
$(document).on('click', '#CloseaddNov', function (e) {
    // $('#CloseaddNov').click(function (e) {
    $('#SelEmpresa').val(null).trigger("change");
    $('#SelPlanta').val(null).trigger("change");
    $('#SelSector').val(null).trigger("change");
    $('#SelSeccion').val(null).trigger("change");
    $('#SelGrupo').val(null).trigger("change");
    $('#SelSucursal').val(null).trigger("change");
    $('.sel_plantas').val(null).trigger("change");
    $('.sel_empresa').val(null).trigger("change");
    $('.sel_sectores').val(null).trigger("change");
    $('.sel_seccion').val(null).trigger("change");
    $('.sel_grupos').val(null).trigger("change");
    $('.sel_sucursal').val(null).trigger("change");
    $('.sel_personal').val(null).trigger("change");
    $('.sel_novedad').val(null).trigger("change");
    $(".sel_seccion").prop("disabled", true);
    $("#FicCate").prop("checked", false);
    $("#Laboral").prop("checked", false);
    $("#FicJust").prop("checked", false);
    $("#FicObse").val(null).trigger("change");
    $("#FicHoras").val(null).trigger("change");
    $("#respuetatext").removeClass("animate__animated animate__fadeIn");
    $("#respuetatext").html("");
    ActiveBTN(false, "#submit", 'Ingresando', 'Ingresar')
    $("#respuesta").removeClass("alert-success");
    $("#respuesta").removeClass("alert-danger");
    $("#respuesta").removeClass("alert-info");
    $('.sel_causa').val(null).trigger('change');
    $(".sel_causa").prop("disabled", true);
    $("#select_causa").addClass("d-none");
    GetPers.search(this.value).draw();
    GetPers.page.len(10).draw();
    $('#divTablePers').addClass('d-none')
    $('#divTablas').removeClass('d-none');
    $('#divaddNov').addClass('d-none');
    fadeInOnly('#divTablas');
    $('#Encabezado').html('Novedades')
});


// });

