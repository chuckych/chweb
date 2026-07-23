/** Select */
$(document).ready(function () {

    const mapUrl = {
        'onov-getleg': "/" + _homehost + "/app-data/custom/onov-getleg",
        'onov-getemp': "/" + _homehost + "/app-data/custom/onov-getemp",
        'onov-getnove': "/" + _homehost + "/app-data/custom/onov-getnove",
        'onov-getgru': "/" + _homehost + "/app-data/custom/onov-getgru",
        'onov-getpla': "/" + _homehost + "/app-data/custom/onov-getpla",
        'onov-getsec2': "/" + _homehost + "/app-data/custom/onov-getsec2",
        'onov-getsec': "/" + _homehost + "/app-data/custom/onov-getsec",
        'onov-getsuc': "/" + _homehost + "/app-data/custom/onov-getsuc",
        'onov-gettipoper': "/" + _homehost + "/app-data/custom/onov-gettipoper"
    };

    const LANG_SELECT2 = {
        noResults: function () {
            return 'No hay resultados..'
        },
        inputTooLong: function (args) {
            var message = 'Máximo ' + 10 + ' caracteres. Elimine ' + overChars + ' caracter';
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
            return 'Ingresar ' + 0 + ' o mas caracteres'
        },
        maximumSelected: function () {
            return 'Puede seleccionar solo una opción'
        },
        removeAllItems: function () {
            return "Eliminar Selección"
        }
    }

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
        setTimeout(function () {
            $('#rowFiltros').removeClass('invisible')
        }, 100);

        $(".selectjs_empresa").select2({
            multiple: true,
            allowClear: opt2["allowClear"],
            language: "es",
            placeholder: $(".selectjs_empresa").data('label') || "Empresas",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 5,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: LANG_SELECT2,
            ajax: {
                url: mapUrl['onov-getemp'],
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
            placeholder: $(".selectjs_plantas").data('label') || "Plantas",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 5,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: LANG_SELECT2,
            ajax: {
                url: mapUrl['onov-getpla'],
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
            placeholder: $(".selectjs_sectores").data('label') || "Sectores",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 5,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: LANG_SELECT2,
            ajax: {
                url: mapUrl['onov-getsec'],
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
            placeholder: $(".select_seccion").data('label') || "Secciones",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 5,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: LANG_SELECT2,
            ajax: {
                url: mapUrl['onov-getsec2'],
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
            placeholder: $(".selectjs_grupos").data('label') || "Grupos",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 5,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: LANG_SELECT2,
            ajax: {
                url: mapUrl['onov-getgru'],
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
            placeholder: $(".selectjs_sucursal").data('label') || "Sucursales",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 5,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: LANG_SELECT2,
            ajax: {
                url: mapUrl['onov-getsuc'],
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
            language: LANG_SELECT2,
            ajax: {
                url: mapUrl['onov-getleg'],
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
            language: LANG_SELECT2,
            ajax: {
                url: mapUrl['onov-getnove'],
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
                        Sucur: $("#Sucur").val(),
                        _dr: $("#_dr").val(),
                        _l: $("#_l").val(),
                        FicNoTi: $("#FicNoTi").val(),
                        FicNovT: $("#datoFicNovT").val(),
                        FicNovI: $("#datoFicNovI").val(),
                        FicNovS: $("#datoFicNovS").val(),
                        FicNovA: $("#datoFicNovA").val()
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
        language: LANG_SELECT2,
        width: '200px',
        ajax: {
            url: mapUrl['onov-gettipoper'],
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
