$(document).ready((function(){$(".requerido").addClass("fontp ml-1 ls1"),$(".requerido").html("(*)");var a=$("#GetUsuarios").DataTable({initComplete:function(a,e){$(".form-control-sm").attr("placeholder","Buscar nombre"),$("#GetUsuarios_filter").prepend('<button tittle="Alta de Usuario" class="px-2 btn btn-outline-custom add fontq border"><svg class="" width="20" height="20" fill="currentColor"><use xlink:href="../img/bootstrap-icons.svg#person-plus-fill"/></svg></button>'),$(window).width()<769?$(".botones").removeClass("float-right"):$(".botones").addClass("float-right")},drawCallback:function(a){$(".page-link").addClass("border border-0"),$(".dataTables_info").addClass("text-secondary"),$(".custom-select").addClass("text-secondary"),$(".contentd").removeClass("text-light bg-light w30"),$(".botones").show(),$(".table-responsive").removeClass("invisible"),fadeInOnly(".table-responsive"),$(".dataTables_length").addClass("d-none d-sm-block")},lengthMenu:[5,10,25,50,100],columnDefs:[{visible:!1,targets:0},{visible:!1,targets:1},{visible:!1,targets:2},{visible:!0,targets:3},{visible:!1,targets:6},{visible:!1,targets:8},{visible:!1,targets:9},{visible:!0,targets:10},{visible:!1,targets:11},{visible:!1,targets:14}],rowGroup:{dataSrc:["nombre"]},bProcessing:!0,serverSide:!0,deferRender:!0,searchDelay:1500,ajax:{url:"GetUsuarios.php",type:"POST",data:function(a){a.recid_c=$("#recid_c").val()},error:function(){$("#GetUsuarios_processing").hide()}},columns:[{class:"",data:"uid"},{class:"",data:"recid"},{class:"",data:"nombre"},{class:"border-0 pb-2",data:"usuario"},{class:"border-0",data:"legajo"},{class:"border-0",data:"rol_n"},{class:"",data:"estado"},{class:"border-0",data:"estado_n"},{class:"",data:"id_cliente"},{class:"",data:"recid_cliente"},{class:"border-0",data:"cliente"},{class:"",data:"rol"},{class:"border-0",data:"fecha_alta"},{class:"border-0",data:"fecha_mod"},{class:"text-nowrap",data:"Buttons"}],paging:!0,responsive:!1,info:!0,searching:!0,ordering:!1,language:{url:"../js/DataTableSpanishShort.json"}});a.page.len("5").draw(),a.on("page.dt",(function(){$(".open-modal").removeClass("btn-outline-custom"),$(".contentd").addClass("text-light bg-light w30"),$(".botones").hide()})),$(window).width()<769?($("#GetUsuarios").removeClass("text-wrap"),$("#GetUsuarios").addClass("text-nowrap")):($("#GetUsuarios").removeClass("text-nowrap"),$("#GetUsuarios").addClass("text-wrap")),$(document).on("click",".editar",(function(a){a.preventDefault(),$("#modalEditUser").modal("show");var e=$(this).attr("data_uid"),t=$(this).attr("data_nombre"),s=$(this).attr("data_usuario"),r=$(this).attr("data_rol_n"),o=$(this).attr("data_rol"),n=$(this).attr("data_legajo");$(this).attr("data_estado_n"),$(this).attr("data_estado"),$(this).attr("data_fecha_alta"),$(this).attr("data_fecha_mod"),$(this).attr("data_cliente");$("#data_nombre").html(t),$("#e_nombre").val(t),$("#e_usuario").val(s),$("#e_legajo").val(n),$("#e_uid").val(e);var i="0",d=!1,l="10",u="250",c=!1;$(".selectRol").select2({multiple:!1,allowClear:c,language:"es",dropdownParent:$("#modalEditUser"),placeholder:"Rol",minimumInputLength:i,minimumResultsForSearch:5,maximumInputLength:l,selectOnClose:d,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(a){var e="Máximo "+l+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(e+="es"),e},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar "+i+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"getRol.php",dataType:"json",type:"POST",delay:u,data:function(a){return{q:a.term,recid_c:$("#recid_c").val()}},processResults:function(a){return{results:a}}}});var m=new Option(r,o,!1,!0);$(".selectRol").append(m).trigger("change"),ActiveBTN(!1,"#submitEdit","Guardando","Guardar"),$("#FormEdit").bind("submit",(function(a){a.preventDefault(),$.ajax({type:$(this).attr("method"),url:$(this).attr("action"),data:$(this).serialize(),beforeSend:function(a){ActiveBTN(!0,"#submitEdit","Guardando","Guardar")},success:function(a){"ok"==a.status?(ActiveBTN(!1,"#submitEdit","Guardando","Guardar"),$("#respuestaForm").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>'+a.Mensaje+'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'),$("#GetUsuarios").DataTable().ajax.reload(),setTimeout(()=>{classEfect("#modalEditUser","animate__animated animate__fadeOut"),setTimeout(()=>{$("#modalEditUser").modal("hide")},500)},1500)):(ActiveBTN(!1,"#submitEdit","Guardando","Guardar"),$("#respuestaForm").html('<div class="py-3 fontq text-danger fw5">'+a.Mensaje+"</div>"))}}),a.stopImmediatePropagation()}))})),$("#modalEditUser").on("hidden.bs.modal",(function(){ActiveBTN(!1,"#submitEdit","Guardando","Guardar"),$("#data_nombre").html(""),$("#e_nombre").val(""),$("#e_usuario").val(""),$("#e_legajo").val(""),$("#e_uid").val(""),$("#respuestaForm").html("")})),$(document).on("click",".add",(function(a){a.preventDefault(),$("#modalAddUser").modal("show"),$("#a_nombre").focus(),$("#a_recid").val($("#recid_c").val());var e="0",t=!1,s="10",r="250",o=!1;$(".selectRol").select2({multiple:!1,allowClear:o,language:"es",dropdownParent:$("#modalAddUser"),placeholder:"Rol",minimumInputLength:e,minimumResultsForSearch:5,maximumInputLength:s,selectOnClose:t,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(a){var e="Máximo "+s+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(e+="es"),e},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar "+e+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"getRol.php",dataType:"json",type:"POST",delay:r,data:function(a){return{q:a.term,recid_c:$("#recid_c").val()}},processResults:function(a){return{results:a}}}}),ActiveBTN(!1,"#submitAdd","Guardando","Agregar"),$("#FormAdd").bind("submit",(function(a){a.preventDefault(),$.ajax({type:$(this).attr("method"),url:$(this).attr("action"),data:$(this).serialize(),beforeSend:function(a){ActiveBTN(!0,"#submitAdd","Guardando","Agregar")},success:function(a){"ok"==a.status?(ActiveBTN(!1,"#submitAdd","Guardando","Agregar"),$("#respuestaFormAdd").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>'+a.Mensaje+'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'),$("#GetUsuarios").DataTable().ajax.reload(),setTimeout(()=>{classEfect("#modalAddUser","animate__animated animate__fadeOut"),setTimeout(()=>{$("#modalAddUser").modal("hide")},500)},1500)):(ActiveBTN(!1,"#submitAdd","Guardando","Agregar"),$("#respuestaFormAdd").html('<div class="py-3 fontq text-danger fw5">'+a.Mensaje+"</div>"))},error:function(a){ActiveBTN(!1,"#submitAdd","Guardando","Agregar"),$("#respuestaFormAdd").html('<div class="py-3 fontq text-danger fw5">Error</div>')}}),a.stopImmediatePropagation()}))})),$("#modalAddUser").on("hidden.bs.modal",(function(){ActiveBTN(!1,"#submitAdd","Guardando","Agregar"),$("#a_nombre").val(""),$("#a_usuario").val(""),$("#a_legajo").val(""),$(".selectRol").val(null).trigger("change"),$("#respuestaFormAdd").html("")})),$(document).on("click",".resetKey",(function(a){a.preventDefault();var e=$(this).attr("data_uid"),t=$(this).attr("data_nombre"),s=$(this).attr("data_usuario");$(".resetKey").unbind("click"),$.ajax({type:"POST",url:"crud.php",data:{submit:"key",uid:e,nombre:t,usuario:s},beforeSend:function(a){$("#reset_"+e).prop("disabled",!0)},success:function(a){"ok"==a.status?($("#reset_"+e).prop("disabled",!1),$("#respuestaResetClave").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>'+a.Mensaje+'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')):($("#reset_"+e).prop("disabled",!1),$("#respuestaResetClave").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>'+a.Mensaje+'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'))},error:function(a){$("#reset_"+e).prop("disabled",!1),$("#respuestaResetClave").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>Error</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')}}),a.stopImmediatePropagation()})),$(document).on("click",".estado",(function(a){a.preventDefault();var e=$(this).attr("data_uid"),t=$(this).attr("data_nombre"),s=$(this).attr("data_estado");$(".estado").unbind("click"),$.ajax({type:"POST",url:"crud.php",data:{submit:"estado",uid:e,nombre:t,estado:s},beforeSend:function(a){$("#estado_"+e).prop("disabled",!0)},success:function(a){"ok"==a.status?($("#estado_"+e).prop("disabled",!1),$("#respuestaResetClave").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>'+a.Mensaje+'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'),$("#GetUsuarios").DataTable().ajax.reload()):($("#estado_"+e).prop("disabled",!1),$("#respuestaResetClave").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>'+a.Mensaje+'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'))},error:function(a){$("#estado_"+e).prop("disabled",!1),$("#respuestaResetClave").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>Error</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')}}),a.stopImmediatePropagation()})),$(document).on("click",".delete",(function(a){a.preventDefault();var e=$(this).attr("data_uid"),t=$(this).attr("data_nombre");$(".delete").unbind("click"),$.ajax({type:"POST",url:"crud.php",data:{submit:"delete",uid:e,nombre:t},beforeSend:function(a){$("#delete_"+e).prop("disabled",!0)},success:function(a){"ok"==a.status?($("#delete_"+e).prop("disabled",!1),$("#respuestaResetClave").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>'+a.Mensaje+'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'),$("#GetUsuarios").DataTable().ajax.reload()):($("#delete_"+e).prop("disabled",!1),$("#respuestaResetClave").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>'+a.Mensaje+'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'))},error:function(a){$("#delete_"+e).prop("disabled",!1),$("#respuestaResetClave").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>Error</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')}}),a.stopImmediatePropagation()}))}));