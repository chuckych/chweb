/** Select */
// $("#divJornal").hide()
// $(document).ready(function () {
var opt2 = { MinLength: "0", SelClose: false, MaxInpLength: "10", delay: "250" };
$('.select2').select2({
    minimumResultsForSearch: -1
});
$('.select2_quincena').select2({
    minimumResultsForSearch: -1
});
$(".selectjs_anio").select2({
    language: "es",
    placeholder: "Año",
    minimumResultsForSearch: -1,
    language: {
        noResults: function () {
            return 'No hay resultados..'
        },
        searching: function () {
            return ''
        },
        errorLoading: function () {
            return 'Sin datos..'
        },
    },
    ajax: {
        url: "/" + $("#_homehost").val() + "/data/getFichAnio.php",
        dataType: "json",
        type: "POST",
        data: function () {
            return {
                // q: params.term,
            }
        },
        processResults: function (data) {
            return {
                results: data
            }
        },
        cache: true
    }
})
$(".selectjs_mes").select2({
    language: "es",
    placeholder: "Mes",
    minimumResultsForSearch: -1,
    language: {
        noResults: function () {
            return 'No hay resultados..'
        },
        searching: function () {
            return ''
        },
        errorLoading: function () {
            return 'Sin datos..'
        },
    },
    ajax: {
        url: "/" + $("#_homehost").val() + "/data/getFichMes.php",
        dataType: "json",
        type: "POST",
        delay: opt2["delay"],
        data: function () {
            return {
                Anio: $("#Anio").val(),
            }
        },
        processResults: function (data) {
            return {
                results: data
            }
        },
    }
})
$(".selectjs_empresa").select2({
    multiple: false,
    allowClear: true,
    language: "es",
    placeholder: "Empresa",
    minimumInputLength: opt2["MinLength"],
    minimumResultsForSearch: 5,
    maximumInputLength: opt2["MaxInpLength"],
    selectOnClose: opt2["SelClose"],
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
        }
    },
    ajax: {
        // url: "/" + $("#_homehost").val() + "/filtros/array_estruct.php?_c=" + $("#_c").val() + "&_r=" + $("#_r").val() + "&e=empresas&act",
        url: "/" + $("#_homehost").val() + "/data/getPerEmpresas.php",
        dataType: "json",
        type: "GET",
        delay: opt2["delay"],
        data: function (params) {
            return {
                q: params.term,
                Tipo: $("#Tipo").val(),
                // Emp: $("#Emp").val(),
                Plan: $("#Plan").val(),
                Sect: $("#Sect").val(),
                Sec2: $("#Sec2").val(),
                Grup: $("#Grup").val(),
                Sucur: $("#Sucur").val(),
                Per: $("#Per").val(),
            }
        },
        processResults: function (data) {
            return {
                results: data
            }
        },
    }
})
$(".selectjs_plantas").select2({
    multiple: false,
    allowClear: true,
    language: "es",
    placeholder: "Planta",
    minimumInputLength: opt2["MinLength"],
    minimumResultsForSearch: 5,
    maximumInputLength: opt2["MaxInpLength"],
    selectOnClose: opt2["SelClose"],
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
        }
    },
    ajax: {
        // url: "/"+ $("#_homehost").val() +"/filtros/array_estruct.php?_c="+ $("#_c").val() +"&_r="+$("#_r").val()+"&e=plantas&act",
        url: "/" + $("#_homehost").val() + "/data/getPerPlantas.php",
        dataType: "json",
        type: "GET",
        delay: opt2["delay"],
        data: function (params) {
            return {
                q: params.term,
                Tipo: $("#Tipo").val(),
                Emp: $("#Emp").val(),
                // Plan: $("#Plan").val(),
                Sect: $("#Sect").val(),
                Sec2: $("#Sec2").val(),
                Grup: $("#Grup").val(),
                Sucur: $("#Sucur").val(),
                Per: $("#Per").val(),
            }
        },
        processResults: function (data) {
            return {
                results: data
            }
        },
    }
})
$(".selectjs_sectores").select2({
    multiple: false,
    allowClear: true,
    language: "es",
    placeholder: "Sector",
    minimumInputLength: opt2["MinLength"],
    minimumResultsForSearch: 5,
    maximumInputLength: opt2["MaxInpLength"],
    selectOnClose: opt2["SelClose"],
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
        }
    },
    ajax: {
        // url: "/"+ $("#_homehost").val() +"/filtros/array_estruct.php?_c="+ $("#_c").val() +"&_r="+$("#_r").val()+"&e=sectores&act",
        url: "/" + $("#_homehost").val() + "/data/getPerSectores.php",
        dataType: "json",
        type: "GET",
        delay: opt2["delay"],
        data: function (params) {
            return {
                q: params.term,
                Tipo: $("#Tipo").val(),
                Emp: $("#Emp").val(),
                Plan: $("#Plan").val(),
                // Sect: $("#Sect").val(),
                Sec2: $("#Sec2").val(),
                Grup: $("#Grup").val(),
                Sucur: $("#Sucur").val(),
                Per: $("#Per").val(),
            }
        },
        processResults: function (data) {
            return {
                results: data
            }
        },
    }
})
$(".select_seccion").select2({
    multiple: false,
    allowClear: true,
    language: "es",
    placeholder: "Sección",
    minimumInputLength: opt2["MinLength"],
    minimumResultsForSearch: 5,
    maximumInputLength: opt2["MaxInpLength"],
    selectOnClose: opt2["SelClose"],
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
        }
    },
    ajax: {
        // url: "/"+ $("#_homehost").val() +"/data/getSecciones.php?_c="+ $("#_c").val() +"&_r="+$("#_r").val(),
        url: "/" + $("#_homehost").val() + "/data/getPerSecciones.php",
        dataType: "json",
        type: "GET",
        delay: opt2["delay"],
        data: function (params) {
            return {
                q: params.term,
                // sect: $("#Sect").val(),
                Tipo: $("#Tipo").val(),
                Emp: $("#Emp").val(),
                Plan: $("#Plan").val(),
                Sect: $("#Sect").val(),
                // Sec2: $("#Sec2").val(),
                Grup: $("#Grup").val(),
                Sucur: $("#Sucur").val(),
                Per: $("#Per").val(),
            }
        },
        processResults: function (data) {
            return {
                results: data
            }
        },
    }
})
$(".selectjs_grupos").select2({
    multiple: false,
    allowClear: true,
    language: "es",
    placeholder: "Grupo",
    minimumInputLength: opt2["MinLength"],
    minimumResultsForSearch: 5,
    maximumInputLength: opt2["MaxInpLength"],
    selectOnClose: opt2["SelClose"],
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
        }
    },
    ajax: {
        url: "/" + $("#_homehost").val() + "/data/getPerGrupos.php",
        dataType: "json",
        type: "GET",
        delay: opt2["delay"],
        data: function (params) {
            return {
                q: params.term,
                Tipo: $("#Tipo").val(),
                Emp: $("#Emp").val(),
                Plan: $("#Plan").val(),
                Sect: $("#Sect").val(),
                Sec2: $("#Sec2").val(),
                // Grup: $("#Grup").val(),
                Sucur: $("#Sucur").val(),
                Per: $("#Per").val(),
            }
        },
        processResults: function (data) {
            return {
                results: data
            }
        },
    }
})
$(".selectjs_sucursal").select2({
    multiple: false,
    allowClear: true,
    language: "es",
    placeholder: "Sucursal",
    minimumInputLength: opt2["MinLength"],
    minimumResultsForSearch: 5,
    maximumInputLength: opt2["MaxInpLength"],
    selectOnClose: opt2["SelClose"],
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
        }
    },
    ajax: {
        url: "/" + $("#_homehost").val() + "/data/getPerSucursales.php",
        dataType: "json",
        type: "GET",
        delay: opt2["delay"],
        data: function (params) {
            return {
                q: params.term,
                Tipo: $("#Tipo").val(),
                Emp: $("#Emp").val(),
                Plan: $("#Plan").val(),
                Sect: $("#Sect").val(),
                Sec2: $("#Sec2").val(),
                Grup: $("#Grup").val(),
                Per: $("#Per").val(),
                // Sucur: $("#Sucur").val()
            }
        },
        processResults: function (data) {
            return {
                results: data
            }
        },
    }
})
$(".selectjs_personal").select2({
    multiple: true,
    // allowClear :true,
    language: "es",
    placeholder: "",
    minimumInputLength: 2,
    minimumResultsForSearch: 5,
    maximumInputLength: opt2["MaxInpLength"],
    selectOnClose: opt2["SelClose"],
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
            return 'Ingresar ' + "2" + ' o mas caracteres'
        },
        maximumSelected: function () {
            return 'Puede seleccionar solo una opción'
        }
    },
    ajax: {
        url: "/" + $("#_homehost").val() + "/filtros/array_personal.php",
        dataType: "json",
        type: "GET",
        delay: opt2["delay"],
        data: function (params) {
            return {
                q: params.term,
                Tipo: $("#Tipo").val(),
                Emp: $("#Emp").val(),
                Plan: $("#Plan").val(),
                Sect: $("#Sect").val(),
                Sec2: $("#Sec2").val(),
                Grup: $("#Grup").val(),
                Sucur: $("#Sucur").val()
            }
        },
        processResults: function (data) {
            return {
                results: data
            }
        },
    }
})

/** GET PARACONT */
function GetParacont() {
    $.ajax({
        type: 'POST',
        dataType: "json",
        url: "GetParacont.php",
        success: function (data) {

            $("#MensDesde").val(data.MensDesde)
            $("#MensHasta").val(data.MensHasta)
            $("#Jor1Desde").val(data.Jor1Desde)
            $("#Jor1Hasta").val(data.Jor1Hasta)
            $("#Jor2Desde").val(data.Jor2Desde)
            $("#Jor2Hasta").val(data.Jor2Hasta)
            $("#ArchDesc").val(data.ArchDesc)
            $("#ArchNomb").val(data.ArchNomb)
            $("#ArchPath").val(data.ArchPath)

            $(".MensDesde").html(data.MensDesde)
            $(".MensHasta").html(data.MensHasta)
            $(".Jor1Desde").html(data.Jor1Desde)
            $(".Jor1Hasta").html(data.Jor1Hasta)
            $(".Jor2Desde").html(data.Jor2Desde)
            $(".Jor2Hasta").html(data.Jor2Hasta)
            $(".ArchDesc").html(data.ArchDesc)
            $(".ArchNomb").html(data.ArchNomb)
            // let tipoPer = ($('#Tipo').val() == '1') ? 'jornales' : 'mensuales'
            // GetArch($(".ArchNomb").text()+ '?v=' + $.now(), data.ArchNomb, tipoPer)
            // GetArch($(".ArchNomb").text() + '?v=' + $.now(), $(".ArchNomb").text(), tipo)
            $(".ArchPath").html("<a class='text-secondary' href=" + data.ArchPath + "/" + data.ArchNomb + ">" + data.ArchPath + "</a>")
            var Mes = moment().format("MM");
            var Anio = moment().format("YYYY");

            if (data.MensHasta == '31') {
                Hasta = parseFloat(data.MensHasta) - 5
                var FechaHasta = moment(Anio + "/" + Mes + "/" + Hasta).endOf('month').format("DD/MM/YYYY");
            } else if (data.MensHasta == '30') {
                Hasta = parseFloat(data.MensHasta) - 5
                var FechaHasta = moment(Anio + "/" + Mes + "/" + Hasta).endOf('month').format("DD/MM/YYYY");
            } else {
                var FechaHasta = moment(Anio + "/" + Mes + "/" + data.MensHasta).add(1, 'months').format("DD/MM/YYYY");
            }
            var FechaDesde = moment(Anio + "/" + Mes + "/" + data.MensDesde).format("DD/MM/YYYY");

            DatePicker()

            $('input[name="_drLiq"]').data('daterangepicker').setStartDate(FechaDesde);
            $('input[name="_drLiq"]').data('daterangepicker').setEndDate(FechaHasta);

            $('.selectjs_mes').val(null).trigger('change');
            var newOption = new Option(NombreMesJS(Mes), Mes, true, true);
            $('.selectjs_mes').append(newOption).trigger('change');

        },
        error: function () {
        }
    });
}
// GetParacont();
function GetArch(arch, nombrearch, tipo) {
    let TXT_URL = arch
    let jqxhr = $.ajax(TXT_URL)
        .done(function () {
            $(".ArchPath").html(`<a class="d-inline-flex btn btn-sm btn-custom fw5 fontq px-4" download=` + moment().format("DDMMYYYYHmmss") + `_` + tipo + `_` + nombrearch + ` href=` + nombrearch + `>` + nombrearch + `<i class='ml-2 bi bi-file-earmark-arrow-down'></i></a>`)
            $('#trDownload').show()
        })
        .fail(function () {
            console.log('Error al descargar el archivo')
        })
        .always(function () { });
    jqxhr.always(function () {
        $(".ArchPath").html(`<a class="d-inline-flex btn btn-sm btn-custom fw5 fontq px-4" download=` + moment().format("DDMMYYYYHmmss") + `_` + tipo + `_` + nombrearch + ` href=` + nombrearch + `>` + nombrearch + `<i class='ml-2 bi bi-file-earmark-arrow-down'></i></a>`)
        $('#trDownload').show()
        classEfect('#tdDescargar', 'animate__animated animate__fadeIn')
        classEfect('.ArchPath', 'animate__animated animate__fadeIn')
    });
}

let TipoPer = $("#Tipo").val()
$("#TipoPer").val(TipoPer)
function SumarMes1(fecha, cant) {
    var Fecha = moment(fecha).add(cant, 'months').format('DD/MM/YYYY');
    // 'Este mes': [moment().startOf('month'), moment().endOf('month')],
    return Fecha
}
function FechaDeHa(anio, mes, diad, diah) {
    var Mes = (mes != null) ? mes : moment().format("MM");
    var Anio = (anio != null) ? anio : moment().format("YYYY");
    var Desde = diad;
    var Hasta = diah;
    var FechaDesde = moment(Anio + "/" + Mes + "/" + Desde).format("DD/MM/YYYY");
    if (Hasta == '31') {
        Hasta = parseFloat(Hasta) - 5
        var FechaHasta = moment(Anio + "/" + Mes + "/" + Hasta).endOf('month').format("DD/MM/YYYY");
    } else if (Hasta == '30') {
        Hasta = parseFloat(Hasta) - 5
        var FechaHasta = moment(Anio + "/" + Mes + "/" + Hasta).endOf('month').format("DD/MM/YYYY");
    } else {
        var FechaHasta = moment(Anio + "/" + Mes + "/" + Hasta).add(1, 'months').format("DD/MM/YYYY");
    }
    DatePicker()
    $('input[name="_drLiq"]').data('daterangepicker').setStartDate(FechaDesde);
    $('input[name="_drLiq"]').data('daterangepicker').setEndDate(FechaHasta);
}
function FechaDeHaJor(anio, mes, diad, diah) {
    var Mes = (mes != null) ? mes : moment().format("MM");
    var Anio = (anio != null) ? anio : moment().format("YYYY");
    var Desde = diad;
    var Hasta = diah;
    if (Hasta == '31') {
        Hasta = parseFloat(Hasta) - 5
        var FechaHasta = moment(Anio + "/" + Mes + "/" + Hasta).endOf('month').format("DD/MM/YYYY");
    } else if (Desde >= Hasta) {
        var FechaHasta = moment(Anio + "/" + Mes + "/" + Hasta).add(1, 'months').format("DD/MM/YYYY");
    } else {
        var FechaHasta = moment(Anio + "/" + Mes + "/" + Hasta).format("DD/MM/YYYY");
    }
    var FechaDesde = moment(Anio + "/" + Mes + "/" + Desde).format("DD/MM/YYYY");
    DatePicker()
    $('input[name="_drLiq"]').data('daterangepicker').setStartDate(FechaDesde);
    $('input[name="_drLiq"]').data('daterangepicker').setEndDate(FechaHasta);
}
function FadeInSelec2Select(selec, selector2, selector3) {
    $(selec).on('select2:select', function () {
        $(selector2).addClass("animate__animated animate__fadeIn bg-light");
        $(selector3).addClass("animate__animated animate__fadeIn bg-light");
        setTimeout(function () {
            $(selector2).removeClass("animate__animated animate__fadeIn bg-light");
            $(selector3).removeClass("animate__animated animate__fadeIn bg-light");
        }, 500);
    });
}

FadeInSelec2Select('.selectjs_mes', 'input[name="_drLiq"]', 'input[name="_drLiq"]');
FadeInSelec2Select('.select2_quincena', 'input[name="_drLiq"]', 'input[name="_drLiq"]');
FadeInSelec2Select('.selectjs_anio', 'input[name="_drLiq"]', 'input[name="_drLiq"]');
FadeInSelec2Select('.select2', 'input[name="_drLiq"]', 'input[name="_drLiq"]');

function cambioAnio() {
    $("#Anio").change(function (e) {
        e.preventDefault();
        $(".select2_mes").val(null).trigger('change');

        var TipoPer = $("#Tipo").val()
        $("#TipoPer").val(TipoPer)

        var TipoPer = $("#TipoPer").val()
        if (TipoPer == '1') {
            $("#divJornal").hide()
            FechaDeHa($("#Anio").val(), $("#Mes").val(), $("#MensDesde").val(), $("#MensHasta").val());
            $('.selectjs_mes').on('select2:select', function (e) {
                FechaDeHa($("#Anio").val(), $("#Mes").val(), $("#MensDesde").val(), $("#MensHasta").val());
            });
        } else if (TipoPer == '2') {
            // $('.selectjs_mes').off('select2:select');
            /** valores por defecto */
            $("#divJornal").show();
            FechaDeHa($("#Anio").val(), $("#Mes").val(), $("#Jor1Desde").val(), $("#Jor1Hasta").val());
            /** */
            $('.selectjs_mes').on('select2:select', function (e) {
                FechaDeHa($("#Anio").val(), $("#Mes").val(), $("#Jor1Desde").val(), $("#Jor1Hasta").val());
            });

            $('.select2_quincena').on('select2:select', function (e) {

                // $('.selectjs_mes').off('select2:select');
                var TipoJornal = $("#Quincena").val();
                $("#TipoJornal").val(TipoJornal);
                var TipoJornal = $("#TipoJornal").val();

                $('.selectjs_mes').on('select2:select', function (e) {
                    FechaDeHa($("#Anio").val(), $("#Mes").val(), $("#Jor2Desde").val(), $("#Jor2Hasta").val());
                });

                if (TipoJornal == '1') {
                    // $('.selectjs_mes').off('select2:select');
                    /** valores por defecto */
                    FechaDeHa($("#Anio").val(), $("#Mes").val(), $("#Jor1Desde").val(), $("#Jor1Hasta").val());
                    /** */
                    $('.selectjs_mes').on('select2:select', function (e) {
                        FechaDeHa($("#Anio").val(), $("#Mes").val(), $("#Jor1Desde").val(), $("#Jor1Hasta").val());
                    });
                } else {
                    // $('.selectjs_mes').off('select2:select');
                    /** valores por defecto */
                    FechaDeHa($("#Anio").val(), $("#Mes").val(), $("#Jor2Desde").val(), $("#Jor2Hasta").val());
                    /** */
                    $('.selectjs_mes').on('select2:select', function (e) {
                        FechaDeHa($("#Anio").val(), $("#Mes").val(), $("#Jor2Desde").val(), $("#Jor2Hasta").val());
                    });
                }
            });
        }
    });
}

/** valores por defecto */
FechaDeHa($("#Anio").val(), $("#Mes").val(), $("#MensDesde").val(), $("#MensHasta").val());
/** */
$('.selectjs_mes').on('select2:select', function (e) {
    FechaDeHa($("#Anio").val(), $("#Mes").val(), $("#MensDesde").val(), $("#MensHasta").val());
});

$("#Tipo").change(function (e) {
    e.preventDefault();
    // $('.selectjs_mes').off('select2:select');
    $('.selectjs_sucursal').val(null).trigger("change");
    $('#SelSucursal').val(null).trigger("change");
    $('.selectjs_grupos').val(null).trigger("change");
    $('#SelGrupo').val(null).trigger("change");
    $('.select_seccion').val(null).trigger("change");
    $('#SelSeccion').val(null).trigger("change");
    $('.selectjs_sectores').val(null).trigger("change");
    $('.select_seccion').val(null).trigger("change");
    $('#SelSector').val(null).trigger("change");
    $('#SelSeccion').val(null).trigger("change");
    $('.selectjs_personal').val(null).trigger("change");
    $('.selectjs_plantas').val(null).trigger("change");
    $('#SelPlanta').val(null).trigger("change");
    $('.selectjs_empresa').val(null).trigger("change");
    $('#SelEmpresa').val(null).trigger("change");
    $(".select2_quincena").val('1').trigger('change');
    var TipoPer = $("#Tipo").val()
    $("#TipoPer").val(TipoPer)
    cambioAnio();
    var TipoPer = $("#TipoPer").val()
    if (TipoPer == '0') {
        $("#divJornal").hide()
        FechaDeHa($("#Anio").val(), $("#Mes").val(), $("#MensDesde").val(), $("#MensHasta").val());
        $('.selectjs_mes').on('select2:select', function (e) {
            FechaDeHa($("#Anio").val(), $("#Mes").val(), $("#MensDesde").val(), $("#MensHasta").val());
        });
    } else if (TipoPer == '1') {

        $("#divJornal").show();
        FechaDeHaJor($("#Anio").val(), $("#Mes").val(), $("#Jor1Desde").val(), $("#Jor1Hasta").val());
        /** */
        $('.selectjs_mes').on('select2:select', function (e) {
            FechaDeHaJor($("#Anio").val(), $("#Mes").val(), $("#Jor1Desde").val(), $("#Jor1Hasta").val());
        });

        $('.select2_quincena').on('select2:select', function (e) {

            // $('.selectjs_mes').off('select2:select');
            var TipoJornal = $("#Quincena").val();
            $("#TipoJornal").val(TipoJornal);
            var TipoJornal = $("#TipoJornal").val();

            $('.selectjs_mes').on('select2:select', function (e) {
                FechaDeHaJor($("#Anio").val(), $("#Mes").val(), $("#Jor2Desde").val(), $("#Jor2Hasta").val());
            });

            if (TipoJornal == '1') {
                // $('.selectjs_mes').off('select2:select');
                /** valores por defecto */
                FechaDeHaJor($("#Anio").val(), $("#Mes").val(), $("#Jor1Desde").val(), $("#Jor1Hasta").val());
                /** */
                $('.selectjs_mes').on('select2:select', function (e) {
                    FechaDeHaJor($("#Anio").val(), $("#Mes").val(), $("#Jor1Desde").val(), $("#Jor1Hasta").val());
                });
            } else {
                // $('.selectjs_mes').off('select2:select');
                /** valores por defecto */
                FechaDeHaJor($("#Anio").val(), $("#Mes").val(), $("#Jor2Desde").val(), $("#Jor2Hasta").val());
                /** */
                $('.selectjs_mes').on('select2:select', function (e) {
                    FechaDeHaJor($("#Anio").val(), $("#Mes").val(), $("#Jor2Desde").val(), $("#Jor2Hasta").val());
                });
            }
        });
    }
});

$('.selectjs_anio').on('select2:select', function (e) {
    $(".selectjs_mes").prop("disabled", false);
});
$('.selectjs_sectores').on('select2:select', function (e) {
    $(".select_seccion").prop("disabled", false);
    $("#Sec2").addClass("animate__animated animate__fadeIn");
    $('.select_seccion').val(null).trigger('change');
});

$('#alta_liquidacion').val('true').trigger('change');
$('#LegaIni').mask('000000000');
$('#LegaFin').mask('000000000');

function textoSelected(slectjs, idselec) {
    $(slectjs).on('select2:select', function (e) {
        var selected = slectjs + ' ' + ':selected';
        var texto = $(selected).text();
        $(idselec).val(texto).trigger('change');
        $('input[type="checkbox"]').prop('checked', false);
    });
}
textoSelected('.selectjs_empresa', '#SelEmpresa');
textoSelected('.selectjs_plantas', '#SelPlanta');
textoSelected('.selectjs_sectores', '#SelSector');
textoSelected('.select_seccion', '#SelSeccion');
textoSelected('.selectjs_grupos', '#SelGrupo');
textoSelected('.selectjs_sucursal', '#SelSucursal');
// });