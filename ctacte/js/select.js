/** Select */
$(document).ready(function () {
    $('#Tipo').css({"width": "200px"});
    $('.form-control').css({"width": "100%"});
    // $('#RegHora').mask('00:00');
    // $('#RegHora_mod').mask('00:00');
    $('#Filtros').on('shown.bs.modal', function () {
        var opt2 = { MinLength: "0", SelClose: false, MaxInpLength: "10", delay: "250", allowClear: true };
        $(".selectjs_empresa").select2({
            multiple: true,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: $('#Filtros'),
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
                url: "/" + $("#_homehost").val() + "/ctacte/getSelect/getEmpFichas.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q      : params.term,
                        Per    : $("#Per").val(),
                        Tipo   : $("#Tipo").val(),
                        // Emp    : $("#Emp").val(),
                        Plan   : $("#Plan").val(),
                        Sect   : $("#Sect").val(),
                        Sec2   : $("#Sec2").val(),
                        Grup   : $("#Grup").val(),
                        Sucur  : $("#Sucur").val(),
                        _dr    : $("#_dr").val(),
                        _l     : $("#_l").val(),
                        FicNoTi : $("#FicNoTi").val(),
                        FicNove : $("#FicNove").val(),
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
            dropdownParent: $('#Filtros'),
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
                url: "/" + $("#_homehost").val() + "/ctacte/getSelect/getPlanFichas.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q      : params.term,
                        Per    : $("#Per").val(),
                        Tipo   : $("#Tipo").val(),
                        Emp    : $("#Emp").val(),
                        // Plan   : $("#Plan").val(),
                        Sect   : $("#Sect").val(),
                        Sec2   : $("#Sec2").val(),
                        Grup   : $("#Grup").val(),
                        Sucur  : $("#Sucur").val(),
                        _dr    : $("#_dr").val(),
                        _l     : $("#_l").val(),
                        FicNoTi : $("#FicNoTi").val(),
                        FicNove : $("#FicNove").val(),
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
            dropdownParent: $('#Filtros'),
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
                url: "/" + $("#_homehost").val() + "/ctacte/getSelect/getSectFichas.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q      : params.term,
                        Per    : $("#Per").val(),
                        Tipo   : $("#Tipo").val(),
                        Emp    : $("#Emp").val(),
                        Plan   : $("#Plan").val(),
                        // Sect   : $("#Sect").val(),
                        Sec2   : $("#Sec2").val(),
                        Grup   : $("#Grup").val(),
                        Sucur  : $("#Sucur").val(),
                        _dr    : $("#_dr").val(),
                        _l     : $("#_l").val(),
                        FicNoTi : $("#FicNoTi").val(),
                        FicNove : $("#FicNove").val(),
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
            dropdownParent: $('#Filtros'),
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
                url: "/" + $("#_homehost").val() + "/ctacte/getSelect/getSec2Fichas.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q      : params.term,
                        Per    : $("#Per").val(),
                        Tipo   : $("#Tipo").val(),
                        Emp    : $("#Emp").val(),
                        Plan   : $("#Plan").val(),
                        Sect   : $("#Sect").val(),
                        // Sec2   : $("#Sec2").val(),
                        Grup   : $("#Grup").val(),
                        Sucur  : $("#Sucur").val(),
                        _dr    : $("#_dr").val(),
                        _l     : $("#_l").val(),
                        FicNoTi : $("#FicNoTi").val(),
                        FicNove : $("#FicNove").val(),
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
            dropdownParent: $('#Filtros'),
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
                url: "/" + $("#_homehost").val() + "/ctacte/getSelect/getGrupFichas.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q      : params.term,
                        Per    : $("#Per").val(),
                        Tipo   : $("#Tipo").val(),
                        Emp    : $("#Emp").val(),
                        Plan   : $("#Plan").val(),
                        Sect   : $("#Sect").val(),
                        Sec2   : $("#Sec2").val(),
                        // Grup   : $("#Grup").val(),
                        Sucur  : $("#Sucur").val(),
                        _dr    : $("#_dr").val(),
                        _l     : $("#_l").val(),
                        FicNoTi : $("#FicNoTi").val(),
                        FicNove : $("#FicNove").val(),
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
            dropdownParent: $('#Filtros'),
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
                url: "/" + $("#_homehost").val() + "/ctacte/getSelect/getSucFichas.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q      : params.term,
                        Per    : $("#Per").val(),
                        Tipo   : $("#Tipo").val(),
                        Emp    : $("#Emp").val(),
                        Plan   : $("#Plan").val(),
                        Sect   : $("#Sect").val(),
                        Sec2   : $("#Sec2").val(),
                        Grup   : $("#Grup").val(),
                        // Sucur  : $("#Sucur").val(),
                        _dr    : $("#_dr").val(),
                        _l     : $("#_l").val(),
                        FicNoTi : $("#FicNoTi").val(),
                        FicNove : $("#FicNove").val(),
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
            dropdownParent: $('#Filtros'),
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
                url: "/" + $("#_homehost").val() + "/ctacte/getSelect/getLegajosFichas.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q      : params.term,
                        // Per    : $("#Per").val(),
                        Tipo   : $("#Tipo").val(),
                        Emp    : $("#Emp").val(),
                        Plan   : $("#Plan").val(),
                        Sect   : $("#Sect").val(),
                        Sec2   : $("#Sec2").val(),
                        Grup   : $("#Grup").val(),
                        Sucur  : $("#Sucur").val(),
                        _dr    : $("#_dr").val(),
                        _l     : $("#_l").val(),
                        FicNoTi : $("#FicNoTi").val(),
                        FicNove : $("#FicNove").val(),
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        });
        $(".selectjs_tipoper").select2({
            multiple: false,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: $('#Filtros'),
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
                url: "/" + $("#_homehost").val() + "/ctacte/getSelect/getTipoPerFichas.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q      : params.term,
                        Per    : $("#Per").val(),
                        // Tipo   : $("#Tipo").val(),
                        Emp    : $("#Emp").val(),
                        Plan   : $("#Plan").val(),
                        Sect   : $("#Sect").val(),
                        Sec2   : $("#Sec2").val(),
                        Grup   : $("#Grup").val(),
                        Sucur  : $("#Sucur").val(),
                        _dr    : $("#_dr").val(),
                        _l     : $("#_l").val(),
                        FicNoTi : $("#FicNoTi").val(),
                        FicNove : $("#FicNove").val(),
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        });
        $(".selectjs_CTA2Peri").select2({
            multiple: false,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: $('#Filtros'),
            placeholder: "Período",
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
                url: "/" + $("#_homehost").val() + "/ctacte/getSelect/getCTA2Peri.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q : params.term,
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        });
        var data = $('#UltPeri').val();
        var newOption = new Option(data, data, false, false);
        $('.selectjs_CTA2Peri').append(newOption).trigger('change');

        function refreshSelected(slectjs) {
            $(slectjs).on('select2:select', function (e) {
                $('#Per2').val(null)
                ActualizaTablas()
            });
        }
        function refreshUnselected(slectjs) {
            $(slectjs).on('select2:unselecting', function (e) {
                $('#Per2').val(null)
                ActualizaTablas()
            });
        }
             
        refreshSelected('.selectjs_empresa');
        refreshSelected('.selectjs_plantas');
        refreshSelected('.select_seccion');
        refreshSelected('.selectjs_grupos');
        refreshSelected('.selectjs_sucursal');
        refreshSelected('.selectjs_personal');
        refreshSelected('.selectjs_tipoper');
        refreshSelected('.selectjs_FicNoTi');
        refreshSelected('.selectjs_FicNove');
        refreshSelected('.selectjs_CTA2Peri');

        refreshUnselected('.selectjs_empresa');
        refreshUnselected('.selectjs_plantas');
        refreshUnselected('.select_seccion');
        refreshUnselected('.selectjs_grupos');
        refreshUnselected('.selectjs_sucursal');
        refreshUnselected('.selectjs_personal');
        refreshUnselected('.selectjs_tipoper');
        refreshUnselected('.selectjs_FicNoTi');
        refreshUnselected('.selectjs_FicNove');
        refreshUnselected('.selectjs_CTA2Peri');

        $('.selectjs_sectores').on('select2:select', function (e) {
            $('#Per2').val(null)
            $(".select_seccion").prop("disabled", false);
            $('.select_seccion').val(null).trigger('change');
            ActualizaTablas()
            var nombresector = $('.selectjs_sectores :selected').text();
            $("#DatosFiltro").html('Sector: ' + nombresector);
        });
        $('.selectjs_sectores').on('select2:unselecting', function (e) {
            $('#Per2').val(null)
            $(".select_seccion").prop("disabled", true);
            $('.select_seccion').val(null).trigger('change');
            ActualizaTablas()
        });
        $('.selectjs_personal').on('select2:select', function (e) {
            $('#Per2').val(null)
            ActualizaTablas()
        });

    });
});

$('#Filtros').on('hidden.bs.modal', function (e) {
    $('#Filtros').modal('dispose');
  });