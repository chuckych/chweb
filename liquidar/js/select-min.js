var opt2={MinLength:"0",SelClose:!1,MaxInpLength:"10",delay:"250"};function GetParacont(){$.ajax({type:"POST",dataType:"json",url:"GetParacont.php",success:function(e){$("#MensDesde").val(e.MensDesde),$("#MensHasta").val(e.MensHasta),$("#Jor1Desde").val(e.Jor1Desde),$("#Jor1Hasta").val(e.Jor1Hasta),$("#Jor2Desde").val(e.Jor2Desde),$("#Jor2Hasta").val(e.Jor2Hasta),$("#ArchDesc").val(e.ArchDesc),$("#ArchNomb").val(e.ArchNomb),$("#ArchPath").val(e.ArchPath),$(".MensDesde").html(e.MensDesde),$(".MensHasta").html(e.MensHasta),$(".Jor1Desde").html(e.Jor1Desde),$(".Jor1Hasta").html(e.Jor1Hasta),$(".Jor2Desde").html(e.Jor2Desde),$(".Jor2Hasta").html(e.Jor2Hasta),$(".ArchDesc").html(e.ArchDesc),$(".ArchNomb").html(e.ArchNomb),$(".ArchPath").html("<a class='text-secondary' href="+e.ArchPath+"/"+e.ArchNomb+">"+e.ArchPath+"</a>");var a=moment().format("MM"),t=moment().format("YYYY");if("31"==e.MensHasta){Hasta=parseFloat(e.MensHasta)-5;var n=moment(t+"/"+a+"/"+Hasta).endOf("month").format("DD/MM/YYYY")}else if("30"==e.MensHasta){Hasta=parseFloat(e.MensHasta)-5;n=moment(t+"/"+a+"/"+Hasta).endOf("month").format("DD/MM/YYYY")}else n=moment(t+"/"+a+"/"+e.MensHasta).add(1,"months").format("DD/MM/YYYY");var r=moment(t+"/"+a+"/"+e.MensDesde).format("DD/MM/YYYY");DatePicker(),$('input[name="_drLiq"]').data("daterangepicker").setStartDate(r),$('input[name="_drLiq"]').data("daterangepicker").setEndDate(n),$(".selectjs_mes").val(null).trigger("change");var l=new Option(NombreMesJS(a),a,!0,!0);$(".selectjs_mes").append(l).trigger("change")},error:function(){}})}function GetArch(e,a,t){let n=e;$.ajax(n).done((function(){$(".ArchPath").html('<a class="d-inline-flex btn btn-sm btn-custom fw5 fontq px-4" download='+moment().format("DDMMYYYYHmmss")+"_"+t+"_"+a+" href="+a+">"+a+"<i class='ml-2 bi bi-file-earmark-arrow-down'></i></a>"),$("#trDownload").show()})).fail((function(){})).always((function(){})).always((function(){$(".ArchPath").html('<a class="d-inline-flex btn btn-sm btn-custom fw5 fontq px-4" download='+moment().format("DDMMYYYYHmmss")+"_"+t+"_"+a+" href="+a+">"+a+"<i class='ml-2 bi bi-file-earmark-arrow-down'></i></a>"),$("#trDownload").show(),classEfect("#tdDescargar","animate__animated animate__fadeIn"),classEfect(".ArchPath","animate__animated animate__fadeIn")}))}$(".select2").select2({minimumResultsForSearch:-1}),$(".select2_quincena").select2({minimumResultsForSearch:-1}),$(".selectjs_anio").select2({language:"es",placeholder:"Año",minimumResultsForSearch:-1,language:{noResults:function(){return"No hay resultados.."},searching:function(){return""},errorLoading:function(){return"Sin datos.."}},ajax:{url:"/"+$("#_homehost").val()+"/data/getFichAnio.php",dataType:"json",type:"POST",data:function(){return{}},processResults:function(e){return{results:e}},cache:!0}}),$(".selectjs_mes").select2({language:"es",placeholder:"Mes",minimumResultsForSearch:-1,language:{noResults:function(){return"No hay resultados.."},searching:function(){return""},errorLoading:function(){return"Sin datos.."}},ajax:{url:"/"+$("#_homehost").val()+"/data/getFichMes.php",dataType:"json",type:"POST",delay:opt2.delay,data:function(){return{Anio:$("#Anio").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_empresa").select2({multiple:!1,allowClear:!0,language:"es",placeholder:"Empresa",minimumInputLength:opt2.MinLength,minimumResultsForSearch:5,maximumInputLength:opt2.MaxInpLength,selectOnClose:opt2.SelClose,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+opt2.MaxInpLength+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar "+opt2.MinLength+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"/"+$("#_homehost").val()+"/data/getPerEmpresas.php",dataType:"json",type:"GET",delay:opt2.delay,data:function(e){return{q:e.term,Tipo:$("#Tipo").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),Per:$("#Per").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_plantas").select2({multiple:!1,allowClear:!0,language:"es",placeholder:"Planta",minimumInputLength:opt2.MinLength,minimumResultsForSearch:5,maximumInputLength:opt2.MaxInpLength,selectOnClose:opt2.SelClose,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+opt2.MaxInpLength+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar "+opt2.MinLength+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"/"+$("#_homehost").val()+"/data/getPerPlantas.php",dataType:"json",type:"GET",delay:opt2.delay,data:function(e){return{q:e.term,Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),Per:$("#Per").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_sectores").select2({multiple:!1,allowClear:!0,language:"es",placeholder:"Sector",minimumInputLength:opt2.MinLength,minimumResultsForSearch:5,maximumInputLength:opt2.MaxInpLength,selectOnClose:opt2.SelClose,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+opt2.MaxInpLength+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar "+opt2.MinLength+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"/"+$("#_homehost").val()+"/data/getPerSectores.php",dataType:"json",type:"GET",delay:opt2.delay,data:function(e){return{q:e.term,Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),Per:$("#Per").val()}},processResults:function(e){return{results:e}}}}),$(".select_seccion").select2({multiple:!1,allowClear:!0,language:"es",placeholder:"Sección",minimumInputLength:opt2.MinLength,minimumResultsForSearch:5,maximumInputLength:opt2.MaxInpLength,selectOnClose:opt2.SelClose,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+opt2.MaxInpLength+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar "+opt2.MinLength+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"/"+$("#_homehost").val()+"/data/getPerSecciones.php",dataType:"json",type:"GET",delay:opt2.delay,data:function(e){return{q:e.term,Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val(),Per:$("#Per").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_grupos").select2({multiple:!1,allowClear:!0,language:"es",placeholder:"Grupo",minimumInputLength:opt2.MinLength,minimumResultsForSearch:5,maximumInputLength:opt2.MaxInpLength,selectOnClose:opt2.SelClose,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+opt2.MaxInpLength+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar "+opt2.MinLength+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"/"+$("#_homehost").val()+"/data/getPerGrupos.php",dataType:"json",type:"GET",delay:opt2.delay,data:function(e){return{q:e.term,Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Sucur:$("#Sucur").val(),Per:$("#Per").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_sucursal").select2({multiple:!1,allowClear:!0,language:"es",placeholder:"Sucursal",minimumInputLength:opt2.MinLength,minimumResultsForSearch:5,maximumInputLength:opt2.MaxInpLength,selectOnClose:opt2.SelClose,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+opt2.MaxInpLength+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar "+opt2.MinLength+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"/"+$("#_homehost").val()+"/data/getPerSucursales.php",dataType:"json",type:"GET",delay:opt2.delay,data:function(e){return{q:e.term,Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),Per:$("#Per").val()}},processResults:function(e){return{results:e}}}}),$(".selectjs_personal").select2({multiple:!0,language:"es",placeholder:"",minimumInputLength:2,minimumResultsForSearch:5,maximumInputLength:opt2.MaxInpLength,selectOnClose:opt2.SelClose,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+opt2.MaxInpLength+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar 2 o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"/"+$("#_homehost").val()+"/filtros/array_personal.php",dataType:"json",type:"GET",delay:opt2.delay,data:function(e){return{q:e.term,Tipo:$("#Tipo").val(),Emp:$("#Emp").val(),Plan:$("#Plan").val(),Sect:$("#Sect").val(),Sec2:$("#Sec2").val(),Grup:$("#Grup").val(),Sucur:$("#Sucur").val()}},processResults:function(e){return{results:e}}}}),GetParacont();let TipoPer=$("#Tipo").val();function SumarMes1(e,a){return moment(e).add(a,"months").format("DD/MM/YYYY")}function FechaDeHa(e,a,t,n){var r=null!=a?a:moment().format("MM"),l=null!=e?e:moment().format("YYYY"),o=n,s=moment(l+"/"+r+"/"+t).format("DD/MM/YYYY");if("31"==o){o=parseFloat(o)-5;var c=moment(l+"/"+r+"/"+o).endOf("month").format("DD/MM/YYYY")}else if("30"==o){o=parseFloat(o)-5;c=moment(l+"/"+r+"/"+o).endOf("month").format("DD/MM/YYYY")}else c=moment(l+"/"+r+"/"+o).add(1,"months").format("DD/MM/YYYY");DatePicker(),$('input[name="_drLiq"]').data("daterangepicker").setStartDate(s),$('input[name="_drLiq"]').data("daterangepicker").setEndDate(c)}function FechaDeHaJor(e,a,t,n){var r=null!=a?a:moment().format("MM"),l=null!=e?e:moment().format("YYYY"),o=t,s=n;if("31"==s){s=parseFloat(s)-5;var c=moment(l+"/"+r+"/"+s).endOf("month").format("DD/MM/YYYY")}else if(o>=s)c=moment(l+"/"+r+"/"+s).add(1,"months").format("DD/MM/YYYY");else c=moment(l+"/"+r+"/"+s).format("DD/MM/YYYY");var i=moment(l+"/"+r+"/"+o).format("DD/MM/YYYY");DatePicker(),$('input[name="_drLiq"]').data("daterangepicker").setStartDate(i),$('input[name="_drLiq"]').data("daterangepicker").setEndDate(c)}function FadeInSelec2Select(e,a,t){$(e).on("select2:select",(function(){$(a).addClass("animate__animated animate__fadeIn bg-light"),$(t).addClass("animate__animated animate__fadeIn bg-light"),setTimeout((function(){$(a).removeClass("animate__animated animate__fadeIn bg-light"),$(t).removeClass("animate__animated animate__fadeIn bg-light")}),500)}))}function cambioAnio(){$("#Anio").change((function(e){e.preventDefault(),$(".select2_mes").val(null).trigger("change");var a=$("#Tipo").val();$("#TipoPer").val(a),"1"==(a=$("#TipoPer").val())?($("#divJornal").hide(),FechaDeHa($("#Anio").val(),$("#Mes").val(),$("#MensDesde").val(),$("#MensHasta").val()),$(".selectjs_mes").on("select2:select",(function(e){FechaDeHa($("#Anio").val(),$("#Mes").val(),$("#MensDesde").val(),$("#MensHasta").val())}))):"2"==a&&($("#divJornal").show(),FechaDeHa($("#Anio").val(),$("#Mes").val(),$("#Jor1Desde").val(),$("#Jor1Hasta").val()),$(".selectjs_mes").on("select2:select",(function(e){FechaDeHa($("#Anio").val(),$("#Mes").val(),$("#Jor1Desde").val(),$("#Jor1Hasta").val())})),$(".select2_quincena").on("select2:select",(function(e){var a=$("#Quincena").val();$("#TipoJornal").val(a);a=$("#TipoJornal").val();$(".selectjs_mes").on("select2:select",(function(e){FechaDeHa($("#Anio").val(),$("#Mes").val(),$("#Jor2Desde").val(),$("#Jor2Hasta").val())})),"1"==a?(FechaDeHa($("#Anio").val(),$("#Mes").val(),$("#Jor1Desde").val(),$("#Jor1Hasta").val()),$(".selectjs_mes").on("select2:select",(function(e){FechaDeHa($("#Anio").val(),$("#Mes").val(),$("#Jor1Desde").val(),$("#Jor1Hasta").val())}))):(FechaDeHa($("#Anio").val(),$("#Mes").val(),$("#Jor2Desde").val(),$("#Jor2Hasta").val()),$(".selectjs_mes").on("select2:select",(function(e){FechaDeHa($("#Anio").val(),$("#Mes").val(),$("#Jor2Desde").val(),$("#Jor2Hasta").val())})))})))}))}function textoSelected(e,a){$(e).on("select2:select",(function(t){var n=$(e+" :selected").text();$(a).val(n).trigger("change"),$('input[type="checkbox"]').prop("checked",!1)}))}$("#TipoPer").val(TipoPer),FadeInSelec2Select(".selectjs_mes",'input[name="_drLiq"]','input[name="_drLiq"]'),FadeInSelec2Select(".select2_quincena",'input[name="_drLiq"]','input[name="_drLiq"]'),FadeInSelec2Select(".selectjs_anio",'input[name="_drLiq"]','input[name="_drLiq"]'),FadeInSelec2Select(".select2",'input[name="_drLiq"]','input[name="_drLiq"]'),FechaDeHa($("#Anio").val(),$("#Mes").val(),$("#MensDesde").val(),$("#MensHasta").val()),$(".selectjs_mes").on("select2:select",(function(e){FechaDeHa($("#Anio").val(),$("#Mes").val(),$("#MensDesde").val(),$("#MensHasta").val())})),$("#Tipo").change((function(e){e.preventDefault(),$(".selectjs_sucursal").val(null).trigger("change"),$("#SelSucursal").val(null).trigger("change"),$(".selectjs_grupos").val(null).trigger("change"),$("#SelGrupo").val(null).trigger("change"),$(".select_seccion").val(null).trigger("change"),$("#SelSeccion").val(null).trigger("change"),$(".selectjs_sectores").val(null).trigger("change"),$(".select_seccion").val(null).trigger("change"),$("#SelSector").val(null).trigger("change"),$("#SelSeccion").val(null).trigger("change"),$(".selectjs_personal").val(null).trigger("change"),$(".selectjs_plantas").val(null).trigger("change"),$("#SelPlanta").val(null).trigger("change"),$(".selectjs_empresa").val(null).trigger("change"),$("#SelEmpresa").val(null).trigger("change"),$(".select2_quincena").val("1").trigger("change");var a=$("#Tipo").val();$("#TipoPer").val(a),cambioAnio(),"0"==(a=$("#TipoPer").val())?($("#divJornal").hide(),FechaDeHa($("#Anio").val(),$("#Mes").val(),$("#MensDesde").val(),$("#MensHasta").val()),$(".selectjs_mes").on("select2:select",(function(e){FechaDeHa($("#Anio").val(),$("#Mes").val(),$("#MensDesde").val(),$("#MensHasta").val())}))):"1"==a&&($("#divJornal").show(),FechaDeHaJor($("#Anio").val(),$("#Mes").val(),$("#Jor1Desde").val(),$("#Jor1Hasta").val()),$(".selectjs_mes").on("select2:select",(function(e){FechaDeHaJor($("#Anio").val(),$("#Mes").val(),$("#Jor1Desde").val(),$("#Jor1Hasta").val())})),$(".select2_quincena").on("select2:select",(function(e){var a=$("#Quincena").val();$("#TipoJornal").val(a);a=$("#TipoJornal").val();$(".selectjs_mes").on("select2:select",(function(e){FechaDeHaJor($("#Anio").val(),$("#Mes").val(),$("#Jor2Desde").val(),$("#Jor2Hasta").val())})),"1"==a?(FechaDeHaJor($("#Anio").val(),$("#Mes").val(),$("#Jor1Desde").val(),$("#Jor1Hasta").val()),$(".selectjs_mes").on("select2:select",(function(e){FechaDeHaJor($("#Anio").val(),$("#Mes").val(),$("#Jor1Desde").val(),$("#Jor1Hasta").val())}))):(FechaDeHaJor($("#Anio").val(),$("#Mes").val(),$("#Jor2Desde").val(),$("#Jor2Hasta").val()),$(".selectjs_mes").on("select2:select",(function(e){FechaDeHaJor($("#Anio").val(),$("#Mes").val(),$("#Jor2Desde").val(),$("#Jor2Hasta").val())})))})))})),$(".selectjs_anio").on("select2:select",(function(e){$(".selectjs_mes").prop("disabled",!1)})),$(".selectjs_sectores").on("select2:select",(function(e){$(".select_seccion").prop("disabled",!1),$("#Sec2").addClass("animate__animated animate__fadeIn"),$(".select_seccion").val(null).trigger("change")})),$("#alta_liquidacion").val("true").trigger("change"),$("#LegaIni").mask("000000000"),$("#LegaFin").mask("000000000"),textoSelected(".selectjs_empresa","#SelEmpresa"),textoSelected(".selectjs_plantas","#SelPlanta"),textoSelected(".selectjs_sectores","#SelSector"),textoSelected(".select_seccion","#SelSeccion"),textoSelected(".selectjs_grupos","#SelGrupo"),textoSelected(".selectjs_sucursal","#SelSucursal");