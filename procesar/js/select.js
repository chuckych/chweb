/** Select Empresas */
$(document).ready(function () {
    var opt2 = { MinLength: "0", SelClose: false, MaxInpLength: "10", delay: "250" };
    $('.select2').select2({
        minimumResultsForSearch: -1
    });
    $(".selectjs_empresa").select2({
        multiple: false,
        language: "es",
        placeholder: "Empresa",
        minimumInputLength: opt2["MinLength"],
        minimumResultsForSearch: 2,
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
            url: "/" + $("#_homehost").val() + "/filtros/array_estruct.php?_c=" + $("#_c").val() + "&_r=" + $("#_r").val() + "&e=empresas&act",
            dataType: "json",
            type: "GET",
            delay: opt2["delay"],
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
    $(".selectjs_plantas").select2({
        multiple: false,
        language: "es",
        placeholder: "Planta",
        minimumInputLength: opt2["MinLength"],
        minimumResultsForSearch: 2,
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
            url: "/" + $("#_homehost").val() + "/filtros/array_estruct.php?_c=" + $("#_c").val() + "&_r=" + $("#_r").val() + "&e=plantas&act",
            dataType: "json",
            type: "GET",
            delay: opt2["delay"],
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
    $(".selectjs_sectores").select2({
        multiple: false,
        language: "es",
        placeholder: "Sector",
        minimumInputLength: opt2["MinLength"],
        minimumResultsForSearch: 2,
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
            url: "/" + $("#_homehost").val() + "/filtros/array_estruct.php?_c=" + $("#_c").val() + "&_r=" + $("#_r").val() + "&e=sectores&act",
            dataType: "json",
            type: "GET",
            delay: opt2["delay"],
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
    $(".selectjs_grupos").select2({
        multiple: false,
        language: "es",
        placeholder: "Grupo",
        minimumInputLength: opt2["MinLength"],
        minimumResultsForSearch: 2,
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
            url: "/" + $("#_homehost").val() + "/filtros/array_estruct.php?_c=" + $("#_c").val() + "&_r=" + $("#_r").val() + "&e=grupos&act",
            dataType: "json",
            type: "GET",
            delay: opt2["delay"],
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
    $(".selectjs_sucursal").select2({
        multiple: false,
        language: "es",
        placeholder: "Sucursal",
        minimumInputLength: opt2["MinLength"],
        minimumResultsForSearch: 2,
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
            url: "/" + $("#_homehost").val() + "/filtros/array_estruct.php?_c=" + $("#_c").val() + "&_r=" + $("#_r").val() + "&e=sucursales&act",
            dataType: "json",
            type: "GET",
            delay: opt2["delay"],
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
    $(".select_seccion").select2({
        multiple: false,
        language: "es",
        placeholder: "Sección",
        minimumInputLength: opt2["MinLength"],
        minimumResultsForSearch: 2,
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
            url: "/" + $("#_homehost").val() + "/data/getSecciones.php?_c=" + $("#_c").val() + "&_r=" + $("#_r").val(),
            dataType: "json",
            type: "GET",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    sect: $("#ProcSect").val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    })

    $('.selectjs_sectores').on('select2:select', function (e) {
        $("#select_seccion").removeClass("d-none");
        $("#select_seccion").addClass("animate__animated animate__fadeIn");
        $("#trash_sect").removeClass("d-none");
        $('.select_seccion').val(null).trigger('change');
    });
    $('.select_seccion').on('select2:select', function (e) {
        $("#trash_secc").removeClass("d-none");
    });
    function textoSelected(slectjs,idselec) {
        $(slectjs).on('select2:select', function (e) {
            var selected = slectjs + ' '+ ':selected';
            var texto = $(selected).text();
            $(idselec).val(texto).trigger('change');
        });
    }
    textoSelected('.selectjs_empresa','#SelEmpresa');
    textoSelected('.selectjs_plantas','#SelPlanta');
    textoSelected('.selectjs_sectores','#SelSector');
    textoSelected('.select_seccion','#SelSeccion');
    textoSelected('.selectjs_grupos','#SelGrupo');
    textoSelected('.selectjs_sucursal','#SelSucursal');

    $('#procesando').val('true').trigger('change');
    $('#ProcLegaIni').mask('000000000');
    $('#ProcLegaFin').mask('000000000');
});