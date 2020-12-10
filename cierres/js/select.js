/** Select */
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
        language: "es",
        placeholder: "Planta",
        minimumInputLength: opt2["MinLength"],
        minimumResultsForSearch: 5,
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
            url: "/" + $("#_homehost").val() + "/data/getPerPlantas.php",
            dataType: "json",
            type: "GET",
            delay: opt2["delay"],
            data: function(params) {
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
            processResults: function(data) {
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
        minimumResultsForSearch: 5,
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
            url: "/" + $("#_homehost").val() + "/data/getPerSectores.php",
            dataType: "json",
            type: "GET",
            delay: opt2["delay"],
            data: function(params) {
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
            processResults: function(data) {
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
        minimumResultsForSearch: 5,
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
            url: "/" + $("#_homehost").val() + "/data/getPerSecciones.php",
            dataType: "json",
            type: "GET",
            delay: opt2["delay"],
            data: function(params) {
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
            processResults: function(data) {
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
        minimumResultsForSearch: 5,
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
            url: "/" + $("#_homehost").val() + "/data/getPerGrupos.php",
            dataType: "json",
            type: "GET",
            delay: opt2["delay"],
            data: function(params) {
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
            processResults: function(data) {
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
        minimumResultsForSearch: 5,
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
            url: "/" + $("#_homehost").val() + "/data/getPerSucursales.php",
            dataType: "json",
            type: "GET",
            delay: opt2["delay"],
            data: function(params) {
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
        placeholder: "",
        minimumInputLength: 2,
        minimumResultsForSearch: 5,
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
                return 'Ingresar '+"2"+' o mas caracteres'
            },
            maximumSelected: function() {
                return 'Puede seleccionar solo una opción'
            }
        },
        ajax: {
            url: "/"+ $("#_homehost").val() +"/filtros/array_personal.php",
            dataType: "json",
            type: "GET",
            delay: opt2["delay"],
            data: function(params) {
                return {
                    q: params.term,
                    Tipo: $("#Tipo").val(),
                    Emp: $("#Emp").val(),
                    Plan: $("#Plan").val(),
                    Sect: $("#Sect").val(),
                    Sec2: $("#Sec2").val(),
                    Grup: $("#Grup").val(),
                    Sucur: $("#Sucur").val(),
                    _c:$("#_c").val(),
                    _r:$("#_r").val()
                }
            },
            processResults: function(data) {
                return {
                    results: data
                }
            },
        }
    })
    $('.selectjs_sectores').on('select2:select', function(e) {
        $(".select_seccion").prop("disabled", false);
        // $("#select_seccion").addClass("animate__animated animate__fadeIn");
        $('.select_seccion').val(null).trigger('change');
    });
    $('#alta_cierre').val('true').trigger('change');
    $('#LegaIni').mask('000000000');
    $('#LegaFin').mask('000000000');

    function textoSelected(slectjs,idselec) {
        $(slectjs).on('select2:select', function (e) {
            var selected = slectjs + ' '+ ':selected';
            var texto = $(selected).text();
            $(idselec).val(texto).trigger('change');
            $('input[type="checkbox"]').prop('checked', false);
            $('#GetPersonal').DataTable().ajax.reload();
            setTimeout(function(){ 
                $('input[type="checkbox"]').prop('checked', true)
                $("#EliminaCierre").prop('checked', false)
             }, 1000);
        });
    }
    textoSelected('.selectjs_empresa','#SelEmpresa');
    textoSelected('.selectjs_plantas','#SelPlanta');
    textoSelected('.selectjs_sectores','#SelSector');
    textoSelected('.select_seccion','#SelSeccion');
    textoSelected('.selectjs_grupos','#SelGrupo');
    textoSelected('.selectjs_sucursal','#SelSucursal');
    
    $("#Tipo").change(function(){
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
        $('#GetPersonal').DataTable().ajax.reload();
        setTimeout(function(){ 
            $('input[type="checkbox"]').prop('checked', true)
            $("#EliminaCierre").prop('checked', false)
         }, 1000);
      });
     
    $('.selectjs_personal').on('select2: unselecting', function(e) {
        $('#GetPersonal').DataTable().ajax.reload();
        setTimeout(function(){ 
            $('input[type="checkbox"]').prop('checked', true)
            $("#EliminaCierre").prop('checked', false)
         }, 1000);
    });
    $('.selectjs_personal').on('select2:select', function(e) {
        $('#GetPersonal').DataTable().ajax.reload();
        setTimeout(function(){ 
            $('input[type="checkbox"]').prop('checked', true)
            $("#EliminaCierre").prop('checked', false)
         }, 1000);
    });
    $("#EliminaCierre").change(function(){
        if ($("#EliminaCierre").is(":checked")) {
            $("#submit").html("Quitar Cierres");
            $("#cierre").prop('disabled', true);
            $('#switch').tooltip('hide')
            $('input[type="checkbox"]').prop('checked', true)
            
        } else {
            $("#submit").html("Ingresar Cierres");
            $("#cierre").prop('disabled', false);
            $('#switch').tooltip('hide')
        }
    });
});
// fadeInOnChange('#Tipo','#cierre')