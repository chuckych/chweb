$(document).ready((function(){$("#Tipo").css({width:"200px"}),$(".form-control").css({width:"100%"}),$("#Filtros").on("shown.bs.modal",(function(){var e="0",a=!1,o="10",r="250",t=!0;function n(e){$(e).on("select2:select",(function(e){$("#Per2").val(null),ActualizaTablas2()}))}function l(e){$(e).on("select2:unselecting",(function(e){$("#Per2").val(null),ActualizaTablas2()}))}$(".selectjs_empresa").select2({multiple:!0,allowClear:t,language:"es",dropdownParent:$("#Filtros"),placeholder:"Empresas",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:o,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+o+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},removeAllItems:function(){return"Borrar"},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"},loadingMore:function(){return"Cargando más resultados…"}},ajax:{url:"/"+$("#_homehost").val()+"/general/getSelect/getEmpFichas.php",dataType:"json",type:"POST",delay:r,data:function(e){return{q:e.term,Filtros:_Filtros(),Per:$("#Per").val(),Tipo:$("#Tipo").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicDiaL:$("#FicDiaL").is(":checked")?1:0,FicFalta:$("#datoFicFalta").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val(),Fic3Nov:$("#datoNovedad").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_plantas").select2({multiple:!0,allowClear:t,language:"es",dropdownParent:$("#Filtros"),placeholder:"Plantas",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:o,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+o+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},removeAllItems:function(){return"Borrar"},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"},loadingMore:function(){return"Cargando más resultados…"}},ajax:{url:"/"+$("#_homehost").val()+"/general/getSelect/getPlanFichas.php",dataType:"json",type:"POST",delay:r,data:function(e){return{q:e.term,Filtros:_Filtros(),Per:$("#Per").val(),Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicDiaL:$("#FicDiaL").is(":checked")?1:0,FicFalta:$("#datoFicFalta").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val(),Fic3Nov:$("#datoNovedad").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_sectores").select2({multiple:!0,allowClear:t,language:"es",dropdownParent:$("#Filtros"),placeholder:"Sectores",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:o,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+o+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},removeAllItems:function(){return"Borrar"},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"},loadingMore:function(){return"Cargando más resultados…"}},ajax:{url:"/"+$("#_homehost").val()+"/general/getSelect/getSectFichas.php",dataType:"json",type:"POST",delay:r,data:function(e){return{q:e.term,Filtros:_Filtros(),Per:$("#Per").val(),Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicDiaL:$("#FicDiaL").is(":checked")?1:0,FicFalta:$("#datoFicFalta").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val(),Fic3Nov:$("#datoNovedad").val()}},processResults:function(e){return{results:e}}}}),$(".select_seccion").select2({multiple:!0,allowClear:t,language:"es",dropdownParent:$("#Filtros"),placeholder:"Secciones",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:o,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+o+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},removeAllItems:function(){return"Borrar"},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"},loadingMore:function(){return"Cargando más resultados…"}},ajax:{url:"/"+$("#_homehost").val()+"/general/getSelect/getSec2Fichas.php",dataType:"json",type:"POST",delay:r,data:function(e){return{q:e.term,Filtros:_Filtros(),Per:$("#Per").val(),Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicDiaL:$("#FicDiaL").is(":checked")?1:0,FicFalta:$("#datoFicFalta").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val(),Fic3Nov:$("#datoNovedad").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_grupos").select2({multiple:!0,allowClear:t,language:"es",dropdownParent:$("#Filtros"),placeholder:"Grupos",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:o,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+o+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},removeAllItems:function(){return"Borrar"},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"},loadingMore:function(){return"Cargando más resultados…"}},ajax:{url:"/"+$("#_homehost").val()+"/general/getSelect/getGrupFichas.php",dataType:"json",type:"POST",delay:r,data:function(e){return{q:e.term,Filtros:_Filtros(),Per:$("#Per").val(),Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Sucur:$("#Sucur").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicDiaL:$("#FicDiaL").is(":checked")?1:0,FicFalta:$("#datoFicFalta").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val(),Fic3Nov:$("#datoNovedad").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_sucursal").select2({allowClear:t,multiple:!0,language:"es",dropdownParent:$("#Filtros"),placeholder:"Sucursales",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:o,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+o+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},removeAllItems:function(){return"Borrar"},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"},loadingMore:function(){return"Cargando más resultados…"}},ajax:{url:"/"+$("#_homehost").val()+"/general/getSelect/getSucFichas.php",dataType:"json",type:"POST",delay:r,data:function(e){return{q:e.term,Filtros:_Filtros(),Per:$("#Per").val(),Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicDiaL:$("#FicDiaL").is(":checked")?1:0,FicFalta:$("#datoFicFalta").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val(),Fic3Nov:$("#datoNovedad").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_personal").select2({multiple:!0,allowClear:t,language:"es",dropdownParent:$("#Filtros"),placeholder:"Legajos",minimumInputLength:0,minimumResultsForSearch:5,maximumInputLength:o,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+o+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},removeAllItems:function(){return"Borrar"},inputTooShort:function(){return"Ingresar 2 o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"},loadingMore:function(){return"Cargando más resultados…"}},ajax:{url:"/"+$("#_homehost").val()+"/general/getSelect/getLegajosFichas.php",dataType:"json",type:"POST",delay:r,data:function(e){return{q:e.term,Filtros:_Filtros(),Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicDiaL:$("#FicDiaL").is(":checked")?1:0,FicFalta:$("#datoFicFalta").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val(),Fic3Nov:$("#datoNovedad").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_tipoper").select2({multiple:!1,allowClear:t,language:"es",dropdownParent:$("#Filtros"),placeholder:"Tipo Personal",minimumInputLength:0,minimumResultsForSearch:-1,maximumInputLength:o,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+o+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},removeAllItems:function(){return"Borrar"},inputTooShort:function(){return"Ingresar 2 o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"},loadingMore:function(){return"Cargando más resultados…"}},ajax:{url:"/"+$("#_homehost").val()+"/general/getSelect/getTipoPerFichas.php",dataType:"json",type:"POST",delay:r,data:function(e){return{q:e.term,Filtros:_Filtros(),Per:$("#Per").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicDiaL:$("#FicDiaL").is(":checked")?1:0,FicFalta:$("#datoFicFalta").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val(),Fic3Nov:$("#datoNovedad").val()}},processResults:function(e){return{results:e}}}}),$("#datoNovedad").select2({allowClear:t,multiple:!1,language:"es",dropdownParent:$("#Filtros"),placeholder:"Seleccionar Novedad",minimumInputLength:e,minimumResultsForSearch:2,maximumInputLength:o,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+o+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},removeAllItems:function(){return"Borrar"},inputTooShort:function(){return"Ingresar 2 o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"},loadingMore:function(){return"Cargando más resultados…"}},ajax:{url:"/"+$("#_homehost").val()+"/general/getSelect/getNovNovedades.php",dataType:"json",type:"POST",delay:r,data:function(e){return{q:e.term,Filtros:_Filtros(),Per:$("#Per").val(),Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),_dr:$("#_dr").val(),_l:$("#_l").val(),FicDiaL:$("#FicDiaL").is(":checked")?1:0,FicFalta:$("#datoFicFalta").val(),FicNovT:$("#datoFicNovT").val(),FicNovI:$("#datoFicNovI").val(),FicNovS:$("#datoFicNovS").val(),FicNovA:$("#datoFicNovA").val()}},processResults:function(e){return{results:e}}}}),$("#Per2").on("change",(function(){CheckSesion()})),n(".selectjs_empresa"),n(".selectjs_plantas"),n(".select_seccion"),n(".selectjs_grupos"),n(".selectjs_sucursal"),n(".selectjs_personal"),n(".selectjs_tipoper"),n("#datoNovedad"),l(".selectjs_empresa"),l("#datoNovedad"),l(".selectjs_plantas"),l(".select_seccion"),l(".selectjs_grupos"),l(".selectjs_sucursal"),l(".selectjs_personal"),l(".selectjs_tipoper"),$(".selectjs_sectores").on("select2:select",(function(e){$("#Per2").val(null),$(".select_seccion").prop("disabled",!1),$(".select_seccion").val(null).trigger("change"),ActualizaTablas2();var a=$(".selectjs_sectores :selected").text();$("#DatosFiltro").html("Sector: "+a)})),$(".selectjs_sectores").on("select2:unselecting",(function(e){$("#Per2").val(null),$(".select_seccion").prop("disabled",!0),$(".select_seccion").val(null).trigger("change"),ActualizaTablas2()})),$(".selectjs_personal").on("select2:select",(function(e){$("#Per2").val(null),ActualizaTablas2()}))}))})),$("#Filtros").on("hidden.bs.modal",(function(e){$("#Filtros").modal("dispose"),$(".show-calendar").hide()}));