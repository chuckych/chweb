/** Select */
$(function () {
    $('#Tipo').css({"width": "200px"});
    $('.form-control').css({"width": "100%"});

    function Select2Estruct(selector, multiple, placeholder, estruct, url, parent) {
        let opt2 = { MinLength: "0", SelClose: false, MaxInpLength: "10", delay: "250", allowClear: true };
        $(selector).select2({
            multiple: multiple,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: parent,
            placeholder: placeholder,
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
                url: url,
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                cache: false,
                data: function (params) {
                    return {
                        q: params.term,
                        estruct: estruct,
                        Per      : $("#Per").val(),
                        Tipo     : $("#Tipo").val(),
                        Emp      : $("#Emp").val(),
                        Plan     : $("#Plan").val(),
                        Sect     : $("#Sect").val(),
                        Sec2     : $("#Sec2").val(),
                        Grup     : $("#Grup").val(),
                        Sucur    : $("#Sucur").val(),
                        _dr      : $("#_dr").val(),
                        _l       : $("#_l").val(),
                        FicFalta : $("#FicFalta").val(),
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            },
        });
    }
    
    $('#Filtros').on('shown.bs.modal', function () {
       let url = "/" + $("#_homehost").val() + "/fichadas/getSelect/getEstruct.php";
       let Empr =  Select2Estruct(".selectjs_empresa", true, "Empresas", "Empr",url, $('#Filtros'));
       let Plan =  Select2Estruct(".selectjs_plantas", true, "Plantas", "Plan",url, $('#Filtros'));
       let Sect =  Select2Estruct(".selectjs_sectores", true, "Sectores", "Sect",url, $('#Filtros'));
       let Sec2 =  Select2Estruct(".select_seccion", true, "Secciones", "Sec2",url, $('#Filtros'));
       let Grup =  Select2Estruct(".selectjs_grupos", true, "Grupos", "Grup",url, $('#Filtros'));
       let Sucu =  Select2Estruct(".selectjs_sucursal", true, "Sucursales", "Sucu",url, $('#Filtros'));
       let Lega =  Select2Estruct(".selectjs_personal", true, "Legajos", "Lega",url, $('#Filtros'));
       let Tipo =  Select2Estruct(".selectjs_tipoper", false, "Tipo de Personal", "Tipo",url, $('#Filtros'));


        function refreshSelected(slectjs) {
            $(slectjs).on('select2:select', function (e) {
                $('#Per2').val(null)
                // ActualizaTablas()
            });
        }
        function refreshUnselected(slectjs) {
            $(slectjs).on('select2:unselecting', function (e) {
                $('#Per2').val(null)
                // ActualizaTablas()
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
            // ActualizaTablas()
            var nombresector = $('.selectjs_sectores :selected').text();
            $("#DatosFiltro").html('Sector: ' + nombresector);
        });
        $('.selectjs_sectores').on('select2:unselecting', function (e) {
            $('#Per2').val(null)
            $(".select_seccion").prop("disabled", true);
            $('.select_seccion').val(null).trigger('change');
            // ActualizaTablas()
        });
        $('.selectjs_personal').on('select2:select', function (e) {
            $('#Per2').val(null)
            // ActualizaTablas()
        });
    });
});

$('#Filtros').on('hidden.bs.modal', function (e) {
    // $('#Filtros').modal('dispose');
    ActualizaTablas()
  });