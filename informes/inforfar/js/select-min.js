$(document).ready((function(){$("#Tipo").css({width:"150px"});var e="0",a=!1,t="10",o="250",r=!0;function n(e){$(e).on("select2:select",(function(e){$("#Per2").val(null)}))}function l(e){$(e).on("select2:unselecting",(function(e){$("#Per2").val(null)}))}$(".select2").select2({minimumResultsForSearch:-1,placeholder:"Seleccionar"}),$(".select2clear").select2({minimumResultsForSearch:-1,allowClear:r,placeholder:"Seleccionar"}),$(".selectjs_empresa").select2({multiple:!0,allowClear:r,language:"es",placeholder:"Empresas",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:t,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+t+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"/"+$("#_homehost").val()+"/informes/inforfar/getSelect/getEmpFichas.php",dataType:"json",type:"POST",delay:o,data:function(e){return{q:e.term,Per:$("#Per").val(),Tipo:$("#Tipo").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicDiaL:$("#datoFicDiaL").val(),FicFalta:$("#datoFicFalta").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_plantas").select2({multiple:!0,allowClear:r,language:"es",placeholder:"Plantas",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:t,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+t+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"/"+$("#_homehost").val()+"/informes/inforfar/getSelect/getPlanFichas.php",dataType:"json",type:"POST",delay:o,data:function(e){return{q:e.term,Per:$("#Per").val(),Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicDiaL:$("#datoFicDiaL").val(),FicFalta:$("#datoFicFalta").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_sectores").select2({multiple:!0,allowClear:r,language:"es",placeholder:"Sectores",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:t,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+t+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"/"+$("#_homehost").val()+"/informes/inforfar/getSelect/getSectFichas.php",dataType:"json",type:"POST",delay:o,data:function(e){return{q:e.term,Per:$("#Per").val(),Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicDiaL:$("#datoFicDiaL").val(),FicFalta:$("#datoFicFalta").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val()}},processResults:function(e){return{results:e}}}}),$(".select_seccion").select2({multiple:!0,allowClear:r,language:"es",placeholder:"Secciones",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:t,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+t+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"/"+$("#_homehost").val()+"/informes/inforfar/getSelect/getSec2Fichas.php",dataType:"json",type:"POST",delay:o,data:function(e){return{q:e.term,Per:$("#Per").val(),Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicDiaL:$("#datoFicDiaL").val(),FicFalta:$("#datoFicFalta").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_grupos").select2({multiple:!0,allowClear:r,language:"es",placeholder:"Grupos",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:t,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+t+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"/"+$("#_homehost").val()+"/informes/inforfar/getSelect/getGrupFichas.php",dataType:"json",type:"POST",delay:o,data:function(e){return{q:e.term,Per:$("#Per").val(),Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Sucur:$("#Sucur").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicDiaL:$("#datoFicDiaL").val(),FicFalta:$("#datoFicFalta").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_sucursal").select2({allowClear:r,multiple:!0,language:"es",placeholder:"Sucursales",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:t,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+t+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"/"+$("#_homehost").val()+"/informes/inforfar/getSelect/getSucFichas.php",dataType:"json",type:"POST",delay:o,data:function(e){return{q:e.term,Per:$("#Per").val(),Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicDiaL:$("#datoFicDiaL").val(),FicFalta:$("#datoFicFalta").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_personal").select2({multiple:!0,allowClear:r,language:"es",placeholder:"Legajos",minimumInputLength:0,minimumResultsForSearch:5,maximumInputLength:t,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+t+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar 2 o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"/"+$("#_homehost").val()+"/informes/inforfar/getSelect/getLegajosFichas.php",dataType:"json",type:"POST",delay:o,data:function(e){return{q:e.term,Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicDiaL:$("#datoFicDiaL").val(),FicFalta:$("#datoFicFalta").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_FicNove").select2({multiple:!0,allowClear:r,language:"es",placeholder:"Novedad",minimumInputLength:0,minimumResultsForSearch:-1,maximumInputLength:t,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+t+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar 2 o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"/"+$("#_homehost").val()+"/informes/inforfar/getSelect/getFicNove.php",dataType:"json",type:"POST",delay:o,data:function(e){return{q:e.term,Per:$("#Per").val(),Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicNoTi:$("#FicNoTi").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_tipoper").select2({multiple:!1,allowClear:r,language:"es",placeholder:"Tipo de Personal",minimumInputLength:0,minimumResultsForSearch:-1,maximumInputLength:t,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+t+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar 2 o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"/"+$("#_homehost").val()+"/informes/inforfar/getSelect/getTipoPerFichas.php",dataType:"json",type:"POST",delay:o,data:function(e){return{q:e.term,Per:$("#Per").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicDiaL:$("#datoFicDiaL").val(),FicFalta:$("#datoFicFalta").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val()}},processResults:function(e){return{results:e}}}}),n(".selectjs_empresa"),n(".selectjs_plantas"),n(".select_seccion"),n(".selectjs_grupos"),n(".selectjs_sucursal"),n(".selectjs_personal"),n(".selectjs_tipoper"),l(".selectjs_empresa"),l(".selectjs_plantas"),l(".select_seccion"),l(".selectjs_grupos"),l(".selectjs_sucursal"),l(".selectjs_personal"),l(".selectjs_tipoper"),$(".selectjs_sectores").on("select2:select",(function(e){$("#Per2").val(null),$(".select_seccion").prop("disabled",!1),$(".select_seccion").val(null).trigger("change");var a=$(".selectjs_sectores :selected").text();$("#DatosFiltro").html("Sector: "+a)})),$(".selectjs_sectores").on("select2:unselecting",(function(e){$("#Per2").val(null),$(".select_seccion").prop("disabled",!0),$(".select_seccion").val(null).trigger("change")})),$(".selectjs_personal").on("select2:select",(function(e){$("#Per2").val(null)}))}));