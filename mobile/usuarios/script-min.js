function clean(){$("#table-usuarios").DataTable().search("").draw(),$("input[name=_id]").val(""),$("input[name=_name]").val(""),$("input[name=_email]").val(""),$("input[name=_enable]").prop("checked",!1)}$(".select2").select2({minimumResultsForSearch:-1,placeholder:"Seleccionar"}),$("#Refresh").on("click",(function(){CheckSesion(),$("#table-usuarios").DataTable().ajax.reload(),$("tbody").addClass("opa2"),$("#Refresh").prop("disabled",!0),$("#Refresh").html("Actualizando!."),$("#VerUsuarios").removeClass("d-none"),$("#rowNuevoUsuario").addClass("d-none"),$("#btnSubmitUser").prop("disabled",!1),clean()})),$("#table-usuarios").DataTable({initComplete:function(a,e){CheckSesion()},drawCallback:function(a){$("tbody").removeClass("opa2"),$("#Refresh").prop("disabled",!1),$("#Refresh").html("Actualizar Grilla")},orderFixed:[[1,"asc"],[2,"asc"]],rowGroup:{dataSrc:["enable2","trained"]},iDisplayLength:-1,bProcessing:!0,ajax:{url:"array_usuarios.php",type:"POST",dataSrc:"usuarios",data:function(a){}},createdRow:function(a,e,t){$(a).addClass("animate__animated animate__fadeIn align-middle")},columns:[{class:"fw4",data:"usuario"},{class:"d-none",data:"enable2"},{class:"d-none",data:"trained"},{class:"",data:"estado"},{class:"",data:"date"},{class:"",data:"entrenar"},{class:"",data:"mod"},{class:"",data:"del"},{class:"",data:"null"}],deferRender:!0,paging:!1,searching:!0,scrollY:"50vh",scrollX:!0,scrollCollapse:1,info:!0,language:{url:"../../js/DataTableSpanishShort2.json"}}),$(document).on("click",".EliminaUsuario",(function(a){CheckSesion();var e=$(this).attr("data2"),t=$(this).attr("data1"),s=$(this).attr("data");$("input[name=_id]").attr("disabled",!1),$("input[name=_id]").attr("readonly",!1),$("#_nombreUsuario").html(t),$("#d_tk").val(e),$("#d_nombre").val(t),$("#d_id").val(s)})),$(document).on("click",".EntrenarUsuario",(function(a){CheckSesion();var e=parseFloat($(this).attr("data")),t=($(this).attr("data2"),$(this).attr("data1"));$("#divEntrenar").removeClass("d-none"),$("#VerUsuarios").addClass("d-none"),$("#Encabezado").html("Enrolamiento Facial"),$("#divEntrenar").html('<div class="col-12 py-2"><p class="m-0 float-left fw4">'+t+'</p><button type="button" class="float-right btn btn-custom border px-4 fontq" id="btnBack">Volver</button></div><div class="embed-responsive embed-responsive-21by9" style="height:70vh;"><iframe scrolling="yes" frameborder="1" width="100%" height="1100px;" name="contentFrame" class="embed-responsive-item" src="entrenar.php?u_id='+e+'" allowfullscreen"></iframe></div>')})),$(document).on("click","#btnBack",(function(a){CheckSesion(),$("#table-usuarios").DataTable().search("").draw(),$("#table-usuarios").DataTable().ajax.reload(),$("#divEntrenar").addClass("d-none"),$("#VerUsuarios").removeClass("d-none"),$("#divEntrenar").html(""),$("#Encabezado").html("Usuarios Mobile")})),$(document).on("click","#NuevoUsuario",(function(a){CheckSesion(),$("#Titulo").html("Nuevo"),$("#btnSubmitUser").html("Crear"),$("input[name=_id]").attr("readonly",!1),$("input[name=alta]").val("true"),$("#VerUsuarios").addClass("d-none"),$("#rowNuevoUsuario").removeClass("d-none"),fadeInOnly("#rowNuevoUsuario"),clean()})),$(document).on("click","#cancelUsuario",(function(a){$("#VerUsuarios").removeClass("d-none"),$("#rowNuevoUsuario").addClass("d-none"),fadeInOnly("#VerUsuarios"),clean(),$("#Encabezado").html("Usuarios Mobile")})),$(document).on("click",".ModificarUsuario",(function(a){CheckSesion(),$("#Titulo").html("Editar"),$("#btnSubmitUser").html("Guardar"),$("input[name=alta]").val("update"),$("#VerUsuarios").addClass("d-none"),$("#rowNuevoUsuario").removeClass("d-none"),$("input[name=_id]").attr("readonly",!1);var e=$(this).attr("data"),t=$(this).attr("data1");$("#Encabezado").html("Editar usuario: "+t),$.ajax({type:"POST",dataType:"json",url:"array_user.php",data:{id:e},beforeSend:function(){$("#rowNuevoUsuario").addClass("bg-light"),$("input[name=_id]").attr("disabled",!0),$("input[name=_name]").attr("disabled",!0),$("input[name=_email]").attr("disabled",!0),$("input[name=_enable]").attr("disabled",!0),$("#btnSubmitUser").prop("disabled",!0)},success:function(a){$("#btnSubmitUser").prop("disabled",!1),$("#rowNuevoUsuario").removeClass("bg-light"),$("input[name=_id]").attr("disabled",!1),$("input[name=_id]").attr("readonly",!0),$("input[name=_name]").attr("disabled",!1),$("input[name=_email]").attr("disabled",!1),$("input[name=_enable]").attr("disabled",!1),$("input[name=_id]").val(a.id),$("input[name=_name]").val(a.name),$("input[name=_email]").val(a.email),$("input[name=_enable]").prop("checked",!1),1==a.enable?$("input[name=_enable]").prop("checked",!0):$("input[name=_enable]").prop("checked",!1)},error:function(){$("input[name=_id]").val(""),$("input[name=_name]").val(""),$("input[name=_email]").val(""),$("input[name=_enable]").prop("checked",!1),$("input[name=_enable]").attr("disabled",!1)}})})),$("#DUser").bind("submit",(function(a){a.preventDefault(),$.ajax({type:$(this).attr("method"),url:$(this).attr("action"),data:$(this).serialize(),beforeSend:function(a){$("#btnsi").prop("disabled",!0),$("#btnsi").html("Eliminando.!"),$("#Refresh").prop("disabled",!0),$("#Refresh").html("Actualizando!.")},success:function(a){"ok"==a.status?(clean(),$("#table-usuarios").DataTable().ajax.reload(),$("tbody").addClass("opa2"),$("#btnsi").prop("disabled",!1),$("#btnsi").html("S&iacute;"),$("#Refresh").prop("disabled",!0),$("#Refresh").html("Actualizando!."),$("#EliminaUsuario").modal("hide")):($("#table-usuarios").DataTable().ajax.reload(),$("tbody").addClass("opa2"),$("#btnsi").prop("disabled",!1),$("#btnsi").html("S&iacute;"),$("#Refresh").prop("disabled",!1),$("#Refresh").html("Actualizar Grilla"))},error:function(){$("#table-usuarios").DataTable().ajax.reload(),$("tbody").addClass("opa2"),$("#btnsi").prop("disabled",!1),$("#btnsi").html("S&iacute;"),$("#Refresh").prop("disabled",!1),$("#Refresh").html("Actualizar Grilla")}})})),$("#CrearUsuario").bind("submit",(function(a){a.preventDefault(),$.ajax({type:$(this).attr("method"),url:$(this).attr("action"),data:$(this).serialize(),beforeSend:function(a){$("#btnSubmitUser").prop("disabled",!0),$("#btnSubmitUser").html("Procesando.!")},success:function(a){"ok"==a.status?($("#table-usuarios").DataTable().search(a._name).draw(),$("#table-usuarios").DataTable().ajax.reload(),$("#Refresh").prop("disabled",!0),$("#Refresh").html("Actualizando!"),$("#btnSubmitUser").prop("disabled",!1),$("#btnSubmitUser").html("Crear"),$("#divRespuesta").removeClass("d-none"),$("#divRespuesta").html('<div class="alert alert-success fontq" role="alert"><b>'+a.MESSAGE+"</b></div>"),setTimeout((function(){$("#divRespuesta").addClass("d-none"),$("#divRespuesta").html(""),$("#VerUsuarios").removeClass("d-none"),fadeInOnly("#VerUsuarios"),$("#rowNuevoUsuario").addClass("d-none")}),1e3)):($("#Refresh").prop("disabled",!0),$("#Refresh").html("Actualizando!"),$("#btnSubmitUser").prop("disabled",!1),$("#btnSubmitUser").html("Crear"),$("#divRespuesta").removeClass("d-none"),$("#divRespuesta").html('<div class="alert alert-danger" role="alert">'+a.MESSAGE+"</div>"),setTimeout((function(){$("#divRespuesta").addClass("d-none"),$("#divRespuesta").html("")}),6e3))},error:function(){$("#Refresh").prop("disabled",!0),$("#Refresh").html("Actualizando!"),$("#btnSubmitUser").prop("disabled",!1),$("#btnSubmitUser").html("Crear"),$("#divRespuesta").removeClass("d-none"),$("#divRespuesta").html('<div class="alert alert-danger" role="alert">'+data.MESSAGE+"</div>"),setTimeout((function(){$("#divRespuesta").addClass("d-none"),$("#divRespuesta").html("")}),6e3)}})})),$(".selectjs_cuentaToken").select2({multiple:!1,language:"es",placeholder:"Cambiar de Cuenta",minimumResultsForSearch:-1,selectOnClose:!1,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(a){var e="Máximo 10 caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(e+="es"),e},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar 0 o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"../GetTokenCuenta.php",dataType:"json",type:"POST",data:function(a){return{}},processResults:function(a){return{results:a}}}}),$(".selectjs_cuentaToken").on("select2:select",(function(a){CheckSesion(),$("#RefreshToken").submit()})),$("#RefreshToken").bind("submit",(function(a){a.preventDefault(),$.ajax({type:$(this).attr("method"),url:$(this).attr("action"),data:$(this).serialize(),beforeSend:function(a){},success:function(a){"ok"==a.status&&($("#table-usuarios").DataTable().ajax.reload(),$("tbody").addClass("opa2"),clean())},error:function(){}})}));