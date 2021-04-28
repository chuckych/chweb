/** Select */
$(document).ready(function () {
// $('#rowFiltros').on('shown.bs.collapse', function () {
    // var IconExcel = '.xls <img src="../../img/xls.png" class="w15" alt="Exportar Excel">'
    // ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)
    $('#Tipo').css({ "width": "150px" });
    // $('.form-control').css({ "width": "100%" });
    var opt2 = { MinLength: "0", SelClose: false, MaxInpLength: "10", delay: "250", allowClear: true };
    
    $('.select2').select2({
        minimumResultsForSearch: -1,
        placeholder: "Seleccionar"
    });
    SelectSelect2('.select2Resaltar', true, "Resaltar", 0, -1, 10, false)
    $('.select2clear').select2({
        minimumResultsForSearch: -1,
        allowClear: opt2["allowClear"],
        placeholder: "Seleccionar"
    });
    $('#rowFiltros').on('shown.bs.collapse', function () {
        setTimeout(function(){ 
            $('#rowFiltros').removeClass('invisible')
         }, 100); 
    
    $(".selectjs_empresa").select2({
        multiple: true,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Empresas",
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
            url: "/" + $("#_homehost").val() + "/informes/infornov/getSelect/getEmpFichas.php",
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    Per: $("#Per").val(),
                    Tipo: $("#Tipo").val(),
                    // Emp    : $("#Emp").val(),
                    Plan: $("#Plan").val(),
                    Sect: $("#Sect").val(),
                    Sec2: $("#Sec2").val(),
                    Grup: $("#Grup").val(),
                    Sucur: $("#Sucur").val(),
                    _dr: $("#_dr").val(),
                    _l: $("#_l").val(),
                    FicDiaL: $("#datoFicDiaL").val(),
                    FicFalta: $("#datoFicFalta").val(),
                    FicNovT: $("#datoFicNovT").val(),
                    FicNovI: $("#datoFicNovI").val(),
                    FicNovS: $("#datoFicNovS").val(),
                    FicNovA: $("#datoFicNovA").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    });
    $(".selectjs_plantas").select2({
        multiple: true,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Plantas",
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
            url: "/" + $("#_homehost").val() + "/informes/infornov/getSelect/getPlanFichas.php",
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    Per: $("#Per").val(),
                    Tipo: $("#Tipo").val(),
                    Emp: $("#Emp").val(),
                    // Plan   : $("#Plan").val(),
                    Sect: $("#Sect").val(),
                    Sec2: $("#Sec2").val(),
                    Grup: $("#Grup").val(),
                    Sucur: $("#Sucur").val(),
                    _dr: $("#_dr").val(),
                    _l: $("#_l").val(),
                    FicDiaL: $("#datoFicDiaL").val(),
                    FicFalta: $("#datoFicFalta").val(),
                    FicNovT: $("#datoFicNovT").val(),
                    FicNovI: $("#datoFicNovI").val(),
                    FicNovS: $("#datoFicNovS").val(),
                    FicNovA: $("#datoFicNovA").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    });
    $(".selectjs_sectores").select2({
        multiple: true,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Sectores",
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
            url: "/" + $("#_homehost").val() + "/informes/infornov/getSelect/getSectFichas.php",
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    Per: $("#Per").val(),
                    Tipo: $("#Tipo").val(),
                    Emp: $("#Emp").val(),
                    Plan: $("#Plan").val(),
                    // Sect   : $("#Sect").val(),
                    Sec2: $("#Sec2").val(),
                    Grup: $("#Grup").val(),
                    Sucur: $("#Sucur").val(),
                    _dr: $("#_dr").val(),
                    _l: $("#_l").val(),
                    FicDiaL: $("#datoFicDiaL").val(),
                    FicFalta: $("#datoFicFalta").val(),
                    FicNovT: $("#datoFicNovT").val(),
                    FicNovI: $("#datoFicNovI").val(),
                    FicNovS: $("#datoFicNovS").val(),
                    FicNovA: $("#datoFicNovA").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    });
    $(".select_seccion").select2({
        multiple: true,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Secciones",
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
            url: "/" + $("#_homehost").val() + "/informes/infornov/getSelect/getSec2Fichas.php",
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    Per: $("#Per").val(),
                    Tipo: $("#Tipo").val(),
                    Emp: $("#Emp").val(),
                    Plan: $("#Plan").val(),
                    Sect: $("#Sect").val(),
                    // Sec2   : $("#Sec2").val(),
                    Grup: $("#Grup").val(),
                    Sucur: $("#Sucur").val(),
                    _dr: $("#_dr").val(),
                    _l: $("#_l").val(),
                    FicDiaL: $("#datoFicDiaL").val(),
                    FicFalta: $("#datoFicFalta").val(),
                    FicNovT: $("#datoFicNovT").val(),
                    FicNovI: $("#datoFicNovI").val(),
                    FicNovS: $("#datoFicNovS").val(),
                    FicNovA: $("#datoFicNovA").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    });
    $(".selectjs_grupos").select2({
        multiple: true,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Grupos",
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
            url: "/" + $("#_homehost").val() + "/informes/infornov/getSelect/getGrupFichas.php",
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    Per: $("#Per").val(),
                    Tipo: $("#Tipo").val(),
                    Emp: $("#Emp").val(),
                    Plan: $("#Plan").val(),
                    Sect: $("#Sect").val(),
                    Sec2: $("#Sec2").val(),
                    // Grup   : $("#Grup").val(),
                    Sucur: $("#Sucur").val(),
                    _dr: $("#_dr").val(),
                    _l: $("#_l").val(),
                    FicDiaL: $("#datoFicDiaL").val(),
                    FicFalta: $("#datoFicFalta").val(),
                    FicNovT: $("#datoFicNovT").val(),
                    FicNovI: $("#datoFicNovI").val(),
                    FicNovS: $("#datoFicNovS").val(),
                    FicNovA: $("#datoFicNovA").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    });
    $(".selectjs_sucursal").select2({
        allowClear: opt2["allowClear"],
        multiple: true,
        language: "es",
        placeholder: "Sucursales",
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
            url: "/" + $("#_homehost").val() + "/informes/infornov/getSelect/getSucFichas.php",
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    Per: $("#Per").val(),
                    Tipo: $("#Tipo").val(),
                    Emp: $("#Emp").val(),
                    Plan: $("#Plan").val(),
                    Sect: $("#Sect").val(),
                    Sec2: $("#Sec2").val(),
                    Grup: $("#Grup").val(),
                    // Sucur  : $("#Sucur").val(),
                    _dr: $("#_dr").val(),
                    _l: $("#_l").val(),
                    FicDiaL: $("#datoFicDiaL").val(),
                    FicFalta: $("#datoFicFalta").val(),
                    FicNovT: $("#datoFicNovT").val(),
                    FicNovI: $("#datoFicNovI").val(),
                    FicNovS: $("#datoFicNovS").val(),
                    FicNovA: $("#datoFicNovA").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    });
    $(".selectjs_personal").select2({
        multiple: true,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Legajos",
        minimumInputLength: 0,
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
            url: "/" + $("#_homehost").val() + "/informes/infornov/getSelect/getLegajosFichas.php",
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    // Per    : $("#Per").val(),
                    Tipo: $("#Tipo").val(),
                    Emp: $("#Emp").val(),
                    Plan: $("#Plan").val(),
                    Sect: $("#Sect").val(),
                    Sec2: $("#Sec2").val(),
                    Grup: $("#Grup").val(),
                    Sucur: $("#Sucur").val(),
                    _dr: $("#_dr").val(),
                    _l: $("#_l").val(),
                    FicDiaL: $("#datoFicDiaL").val(),
                    FicFalta: $("#datoFicFalta").val(),
                    FicNovT: $("#datoFicNovT").val(),
                    FicNovI: $("#datoFicNovI").val(),
                    FicNovS: $("#datoFicNovS").val(),
                    FicNovA: $("#datoFicNovA").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    });
    $(".selectjs_FicNove").select2({
        multiple: true,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Novedad",
        minimumInputLength: 0,
        minimumResultsForSearch: -1,
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
            url: "/" + $("#_homehost").val() + "/informes/infornov/getSelect/getFicNove.php",
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q       : params.term,
                    Per     : $("#Per").val(),
                    Tipo    : $("#Tipo").val(),
                    Emp     : $("#Emp").val(),
                    Plan    : $("#Plan").val(),
                    Sect    : $("#Sect").val(),
                    Sec2    : $("#Sec2").val(),
                    Grup    : $("#Grup").val(),
                    Sucur   : $("#Sucur").val(),
                    _dr     : $("#_dr").val(),
                    _l      : $("#_l").val(),
                    FicNoTi : $("#FicNoTi").val(),
                    FicNovT : $("#datoFicNovT").val(),
                    FicNovI : $("#datoFicNovI").val(),
                    FicNovS : $("#datoFicNovS").val(),
                    FicNovA : $("#datoFicNovA").val()

                    // FicNove : $("#FicNove").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    });
})
    $(".selectjs_tipoper").select2({
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Tipo de Personal",
        minimumInputLength: 0,
        minimumResultsForSearch: -1,
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
            url: "/" + $("#_homehost").val() + "/informes/infornov/getSelect/getTipoPerFichas.php",
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    Per: $("#Per").val(),
                    // Tipo   : $("#Tipo").val(),
                    Emp: $("#Emp").val(),
                    Plan: $("#Plan").val(),
                    Sect: $("#Sect").val(),
                    Sec2: $("#Sec2").val(),
                    Grup: $("#Grup").val(),
                    Sucur: $("#Sucur").val(),
                    _dr: $("#_dr").val(),
                    _l: $("#_l").val(),
                    FicDiaL: $("#datoFicDiaL").val(),
                    FicFalta: $("#datoFicFalta").val(),
                    FicNovT: $("#datoFicNovT").val(),
                    FicNovI: $("#datoFicNovI").val(),
                    FicNovS: $("#datoFicNovS").val(),
                    FicNovA: $("#datoFicNovA").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    });

    function refreshSelected(slectjs) {
        $(slectjs).on('select2:select', function (e) {
            $('#Per2').val(null)
        });
    }
    function refreshUnselected(slectjs) {
        $(slectjs).on('select2:unselecting', function (e) {
            $('#Per2').val(null)
        });
    }

    refreshSelected('.selectjs_empresa');
    refreshSelected('.selectjs_plantas');
    refreshSelected('.select_seccion');
    refreshSelected('.selectjs_grupos');
    refreshSelected('.selectjs_sucursal');
    refreshSelected('.selectjs_personal');
    refreshSelected('.selectjs_tipoper');

    refreshUnselected('.selectjs_empresa');
    refreshUnselected('.selectjs_plantas');
    refreshUnselected('.select_seccion');
    refreshUnselected('.selectjs_grupos');
    refreshUnselected('.selectjs_sucursal');
    refreshUnselected('.selectjs_personal');
    refreshUnselected('.selectjs_tipoper');

    $('.selectjs_sectores').on('select2:select', function (e) {
        $('#Per2').val(null)
        $(".select_seccion").prop("disabled", false);
        $('.select_seccion').val(null).trigger('change');
        var nombresector = $('.selectjs_sectores :selected').text();
        $("#DatosFiltro").html('Sector: ' + nombresector);
    });
    $('.selectjs_sectores').on('select2:unselecting', function (e) {
        $('#Per2').val(null)
        $(".select_seccion").prop("disabled", true);
        $('.select_seccion').val(null).trigger('change');
    });
    $('.selectjs_personal').on('select2:select', function (e) {
        $('#Per2').val(null)
    });

});

$('#myCollapsible').on('hidden.bs.collapse', function () {
  // do something...
// })
})
