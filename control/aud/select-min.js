$((function(){"use strict";onOpenSelect2();let e="0",a=!1,n="10",r="250",u=!0;function t(e){$(e).on("select2:select",(function(e){$("#tableAuditoria").DataTable().ajax.reload()}))}function o(e){$(e).on("select2:unselecting",(function(e){$("#tableAuditoria").DataTable().ajax.reload()}))}$("#nombreAud").select2({language:"es",multiple:!1,allowClear:u,language:"es",placeholder:"Nombre",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:n,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+n+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},removeAllItems:function(){return"Borrar"},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"},loadingMore:function(){return"Cargando más resultados…"}},ajax:{url:"getSelect.php?d=nombre",dataType:"json",type:"POST",delay:r,data:function(e){return{q:e.term,_dr:$("#_dr").val(),nombreAud:"",userAud:$("#userAud").val(),idSesionAud:$("#idSesionAud").val(),tipoAud:$("#tipoAud").val(),cuentaAud:$("#cuentaAud").val(),horaAud:$("#horaAud").val(),datosAud:$("#datosAud").val()}},processResults:function(e){return{results:e}}}}),$("#userAud").select2({language:"es",multiple:!1,allowClear:u,language:"es",placeholder:"Usuario",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:n,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+n+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},removeAllItems:function(){return"Borrar"},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"},loadingMore:function(){return"Cargando más resultados…"}},ajax:{url:"getSelect.php?d=usuario",dataType:"json",type:"POST",delay:r,data:function(e){return{q:e.term,_dr:$("#_dr").val(),nombreAud:$("#nombreAud").val(),userAud:"",idSesionAud:$("#idSesionAud").val(),tipoAud:$("#tipoAud").val(),cuentaAud:$("#cuentaAud").val(),horaAud:$("#horaAud").val(),datosAud:$("#datosAud").val()}},processResults:function(e){return{results:e}}}}),$("#idSesionAud").select2({language:"es",multiple:!1,allowClear:u,language:"es",placeholder:"ID Sesion",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:n,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+n+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},removeAllItems:function(){return"Borrar"},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"},loadingMore:function(){return"Cargando más resultados…"}},ajax:{url:"getSelect.php?d=id_sesion",dataType:"json",type:"POST",delay:r,data:function(e){return{q:e.term,_dr:$("#_dr").val(),nombreAud:$("#nombreAud").val(),userAud:$("#userAud").val(),idSesionAud:"",tipoAud:$("#tipoAud").val(),cuentaAud:$("#cuentaAud").val(),horaAud:$("#horaAud").val(),datosAud:$("#datosAud").val()}},processResults:function(e){return{results:e}}}}),$("#tipoAud").select2({language:"es",multiple:!1,allowClear:u,language:"es",placeholder:"Tipo",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:n,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+n+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},removeAllItems:function(){return"Borrar"},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"},loadingMore:function(){return"Cargando más resultados…"}},ajax:{url:"getSelect.php?d=tipo",dataType:"json",type:"POST",delay:r,data:function(e){return{q:e.term,_dr:$("#_dr").val(),nombreAud:$("#nombreAud").val(),userAud:$("#userAud").val(),idSesionAud:$("#idSesionAud").val(),tipoAud:"",cuentaAud:$("#cuentaAud").val(),horaAud:$("#horaAud").val(),datosAud:$("#datosAud").val()}},processResults:function(e){return{results:e}}}}),$("#cuentaAud").select2({language:"es",multiple:!1,allowClear:u,language:"es",placeholder:"Cuenta",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:n,selectOnClose:a,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var a="Máximo "+n+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(a+="es"),a},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},removeAllItems:function(){return"Borrar"},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"},loadingMore:function(){return"Cargando más resultados…"}},ajax:{url:"getSelect.php?d=audcuenta",dataType:"json",type:"POST",delay:r,data:function(e){return{q:e.term,_dr:$("#_dr").val(),nombreAud:$("#nombreAud").val(),userAud:$("#userAud").val(),idSesionAud:$("#idSesionAud").val(),tipoAud:$("#tipoAud").val(),cuentaAud:"",horaAud:$("#horaAud").val(),datosAud:$("#datosAud").val()}},processResults:function(e){return{results:e}}}}),t("#nombreAud"),t("#idSesionAud"),t("#tipoAud"),t("#cuentaAud"),t("#userAud"),o("#nombreAud"),o("#idSesionAud"),o("#tipoAud"),o("#cuentaAud"),o("#userAud"),$(".filtros").on("click","#trash_all",(function(){CheckSesion(),$("#nombreAud").val(null).trigger("change"),$("#idSesionAud").val(null).trigger("change"),$("#tipoAud").val(null).trigger("change"),$("#cuentaAud").val(null).trigger("change"),$("#userAud").val(null).trigger("change"),$("#horaAud").val(""),$("#horaAud2").val(""),$("#tableAuditoria").DataTable().search("").draw()})),$("#collapseFiltros").on("change","#horaAud",(function(){""==!$("#horaAud").val()&&$("#tableAuditoria").DataTable().ajax.reload()})),$("#collapseFiltros").on("change","#horaAud2",(function(){""==!$("#horaAud2").val()&&$("#tableAuditoria").DataTable().ajax.reload()})),$("#horaAud").text("clear").click((function(){""==!$("#horaAud").val()&&($("#horaAud").val(""),$("#tableAuditoria").DataTable().ajax.reload())})),$("#horaAud2").text("clear").click((function(){""==!$("#horaAud2").val()&&($("#horaAud2").val(""),$("#tableAuditoria").DataTable().ajax.reload())}));var l=function(e){return e=e.split(":"),parseInt(e[0])>19?"HZ:M0:M0":"H0:M0:M0"};let s={onKeyPress:function(e,a,n,r){n.mask(l.apply({},arguments),r)},translation:{H:{pattern:/[0-2]/,optional:!1},Z:{pattern:/[0-3]/,optional:!1},M:{pattern:/[0-5]/,optional:!1}}};$(".HoraMask").mask(l,s)}));