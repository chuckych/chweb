
    $(document).ready(function() {
        $('.select2').select2({
            minimumResultsForSearch: -1
        });
    });
    $(document).ready(function() {
        $('#table-clientes_length').select2({
            minimumResultsForSearch: -1
        });
    });
    $(document).ready(function() {
        var opt2 = {MinLength:"0", SelClose:false, MaxInpLength:"10", delay:"250"};

        $(".selectjs_empresa").select2({
            multiple: true,
            language: "es",
            placeholder: "Empresa",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 2,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function() {
                    return 'No hay resultados..'
                },
                inputTooLong: function(args) {
                    var message = 'Máximo '+ opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function() {
                    return 'Buscando..'
                },
                errorLoading: function() {
                    return 'Sin datos..'
                },
                inputTooShort: function() {
                    return 'Ingresar '+opt2["MinLength"]+' o mas caracteres'
                },
                maximumSelected: function() {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "/"+ $("#_homehost").val() +"/filtros/array_estruct.php?_c="+ $("#_c").val() +"&_r="+$("#_r").val()+"&e=empresas&act",
                dataType: "json",
                type: "GET",
                delay: opt2["delay"],
                data: function(params) {
                    return {
                        q: params.term,
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    }
                },
            }
        })

        $(".selectjs_plantas").select2({
            multiple: true,
            language: "es",
            placeholder: "Plantas",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 2,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function() {
                    return 'No hay resultados..'
                },
                inputTooLong: function(args) {
                    var message = 'Máximo '+ opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function() {
                    return 'Buscando..'
                },
                errorLoading: function() {
                    return 'Sin datos..'
                },
                inputTooShort: function() {
                    return 'Ingresar '+opt2["MinLength"]+' o mas caracteres'
                },
                maximumSelected: function() {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "/"+ $("#_homehost").val() +"/filtros/array_estruct.php?_c="+ $("#_c").val() +"&_r="+$("#_r").val()+"&e=plantas&act",
                dataType: "json",
                type: "GET",
                delay: opt2["delay"],
                data: function(params) {
                    return {
                        q: params.term,
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    }
                },
            }
        })

        $(".selectjs_convenios").select2({
            multiple: true,
            language: "es",
            placeholder: "Convenios",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 2,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function() {
                    return 'No hay resultados..'
                },
                inputTooLong: function(args) {
                    var message = 'Máximo '+ opt2["MaxInpLength"] + ' caracteres.. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function() {
                    return 'Buscando..'
                },
                errorLoading: function() {
                    return 'Sin datos..'
                },
                inputTooShort: function() {
                    return 'Ingresar '+opt2["MinLength"]+' o mas caracteres'
                },
                maximumSelected: function() {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "/"+ $("#_homehost").val() +"/filtros/array_estruct.php?_c="+ $("#_c").val() +"&_r="+$("#_r").val()+"&e=convenios&act",
                dataType: "json",
                type: "GET",
                delay: opt2["delay"],
                data: function(params) {
                    return {
                        q: params.term,
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    }
                },
            }
        })

        $(".selectjs_sector").select2({
            multiple: true,
            language: "es",
            placeholder: "Sector",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 2,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function() {
                    return 'No hay resultados..'
                },
                inputTooLong: function(args) {
                    var message = 'Máximo '+ opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function() {
                    return 'Buscando..'
                },
                errorLoading: function() {
                    return 'Sin datos..'
                },
                inputTooShort: function() {
                    return 'Ingresar '+opt2["MinLength"]+' o mas caracteres'
                },
                maximumSelected: function() {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "/"+ $("#_homehost").val() +"/filtros/array_estruct.php?_c="+ $("#_c").val() +"&_r="+$("#_r").val()+"&e=sectores&act",
                dataType: "json",
                type: "GET",
                delay: opt2["delay"],
                data: function(params) {
                    return {
                        q: params.term,
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    }
                },
            }
        })

        $(".selectjs_grupos").select2({
            multiple: true,
            language: "es",
            placeholder: "Grupos",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 2,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function() {
                    return 'No hay resultados..'
                },
                inputTooLong: function(args) {
                    var message = 'Máximo '+ opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function() {
                    return 'Buscando..'
                },
                errorLoading: function() {
                    return 'Sin datos..'
                },
                inputTooShort: function() {
                    return 'Ingresar '+opt2["MinLength"]+' o mas caracteres'
                },
                maximumSelected: function() {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "/"+ $("#_homehost").val() +"/filtros/array_estruct.php?_c="+ $("#_c").val() +"&_r="+$("#_r").val()+"&e=grupos&act",
                dataType: "json",
                type: "GET",
                delay: opt2["delay"],
                data: function(params) {
                    return {
                        q: params.term,
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    }
                },
            }
        })

        $(".selectjs_sucursal").select2({
            multiple: true,
            language: "es",
            placeholder: "Sucursal",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 2,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function() {
                    return 'No hay resultados..'
                },
                inputTooLong: function(args) {
                    var message = 'Máximo '+ opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function() {
                    return 'Buscando..'
                },
                errorLoading: function() {
                    return 'Sin datos..'
                },
                inputTooShort: function() {
                    return 'Ingresar '+opt2["MinLength"]+' o mas caracteres'
                },
                maximumSelected: function() {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "/"+ $("#_homehost").val() +"/filtros/array_estruct.php?_c="+ $("#_c").val() +"&_r="+$("#_r").val()+"&e=sucursales&act",
                dataType: "json",
                type: "GET",
                delay: opt2["delay"],
                data: function(params) {
                    return {
                        q: params.term,
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    }
                },
            }
        })

        $(".selectjs_personal").select2({
            multiple: true,
            language: "es",
            placeholder: "Personal",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 2,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function() {
                    return 'No hay resultados..'
                },
                inputTooLong: function(args) {
                    var message = 'Máximo '+ opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function() {
                    return 'Buscando..'
                },
                errorLoading: function() {
                    return 'Sin datos..'
                },
                inputTooShort: function() {
                    return 'Ingresar '+opt2["MinLength"]+' o mas caracteres'
                },
                maximumSelected: function() {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "/"+ $("#_homehost").val() +"/filtros/array_personal.php?_c="+ $("#_c").val() +"&_r="+$("#_r").val(),
                dataType: "json",
                type: "GET",
                delay: opt2["delay"],
                data: function(params) {
                    return {
                        q: params.term,
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    }
                },
            }
        })

        $(".selectjs_personal2").select2({
            multiple: false,
            language: "es",
            placeholder: "Seleccionar legajo",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 2,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function() {
                    return 'No hay resultados..'
                },
                inputTooLong: function(args) {
                    var message = 'Máximo '+ opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function() {
                    return 'Buscando..'
                },
                errorLoading: function() {
                    return 'Sin datos..'
                },
                inputTooShort: function() {
                    return 'Ingresar '+opt2["MinLength"]+' o mas caracteres'
                },
                maximumSelected: function() {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "/"+ $("#_homehost").val() +"/filtros/array_personal.php?_c="+ $("#_c").val() +"&_r="+$("#_r").val(),
                dataType: "json",
                type: "GET",
                delay: opt2["delay"],
                data: function(params) {
                    return {
                        q: params.term,
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    }
                },
            }
        })
    });