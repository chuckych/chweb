$("#collapse_rol").on("shown.bs.collapse",(function(){$("#nombre").focus()})),$(document).on("click",".editRol",(function(t){t.preventDefault();var a=$(this).attr("datarol"),e=$(this).attr("dataidrol"),s=$(this).attr("datarecid_c");setTimeout(()=>{$(".modal-footer").addClass("bg-light"),$(".modal-header").addClass("border-bottom-0"),$(".bootbox-input-text").addClass("h40"),$(".bootbox-input-text").val(a),$(".bootbox-input-text").select(),$(".bootbox-input-text").attr("placeholder","Nombre Rol")},200),bootbox.addLocale("custom",{OK:"OK",CONFIRM:"Confirmar",CANCEL:"Cancelar"}),bootbox.prompt({size:"small",buttons:{confirm:{label:"Guardar",className:"btn-custom btn-sm fontq btn-mobile"},cancel:{label:"Cancelar",className:"btn-outline-custom border btn-sm fontq btn-mobile"}},title:"<span class='fonth text-secondary'>Editar Rol: "+a+"</span>",locale:"custom",callback:function(t){t&&$.ajax({type:"POST",url:"crudRol.php",data:{submit:"editarol",id:e,nombre:a,recid_c:s,nombre_nuevo:t},beforeSend:function(t){$("#Editar_"+e).prop("disabled",!0)},success:function(t){"ok"==t.status?($("#Editar_"+e).prop("disabled",!1),$("#respuesta").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>'+t.Mensaje+'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'),setTimeout(()=>{location.reload()},500)):"nocambios"==t.status?($("#Editar_"+e).prop("disabled",!1),$("#respuesta").html("")):($("#Editar_"+e).prop("disabled",!1),$("#respuesta").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>'+t.Mensaje+'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'))},error:function(t){$("#Editar_"+e).prop("disabled",!1),$("#respuesta").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>Error</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')}})}})})),$(document).on("click",".addRol",(function(t){t.preventDefault();var a=$("#recid_cRol").val();setTimeout(()=>{$(".modal-footer").addClass("bg-light"),$(".modal-header").addClass("border-bottom-0"),$(".bootbox-input-text").addClass("h40"),$(".bootbox-input-text").select(),$(".bootbox-input-text").attr("placeholder","Nombre del Rol")},200),bootbox.addLocale("custom",{OK:"OK",CONFIRM:"Confirmar",CANCEL:"Cancelar"}),bootbox.prompt({size:"small",buttons:{confirm:{label:"Aceptar",className:"btn-custom btn-sm fontq btn-mobile"},cancel:{label:"Cancelar",className:"btn-outline-custom border btn-sm fontq btn-mobile"}},title:"<span class='fonth text-secondary'>Alta Rol</span>",locale:"custom",callback:function(t){t&&$.ajax({type:"POST",url:"crudRol.php",data:{submit:"addRol",recid_c:a,nombre:t},beforeSend:function(t){$("#AltaRol").prop("disabled",!0)},success:function(t){"ok"==t.status?($("#AltaRol").prop("disabled",!1),$("#respuesta").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>'+t.Mensaje+'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'),setTimeout(()=>{location.reload()},500)):"nocambios"==t.status?($("#AltaRol").prop("disabled",!1),$("#respuesta").html("")):($("#AltaRol").prop("disabled",!1),$("#respuesta").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>'+t.Mensaje+'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'))},error:function(t){$("#AltaRol").prop("disabled",!1),$("#respuesta").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>Error</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')}})}})})),$(document).on("click",".deleteRol",(function(t){t.preventDefault();var a=$(this).attr("datarol"),e=$(this).attr("dataidrol");$(this).attr("datarecid_c");bootbox.confirm({message:'<span class="fonth fw4">¿Confirma eliminar el Rol: <span class="fw5">'+a+"</span>?</span>",buttons:{confirm:{label:"Confirmar",className:"btn-custom btn-sm fontq btn-mobile"},cancel:{label:"Cancelar",className:"btn-outline-custom border btn-sm fontq btn-mobile"}},callback:function(t){$(".deleteRol").unbind("click"),t&&$.ajax({type:"POST",url:"crudRol.php",data:{submit:"deleteRol",id:e,nombre:a},beforeSend:function(t){$("#delete_"+e).prop("disabled",!0)},success:function(t){"ok"==t.status?($("#delete_"+e).prop("disabled",!1),$("#respuesta").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-success alert-dismissible fade show fontq" role="alert"><strong>'+t.Mensaje+'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'),setTimeout(()=>{location.reload()},500)):($("#delete_"+e).prop("disabled",!1),$("#respuesta").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>'+t.Mensaje+'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'))},error:function(t){$("#delete_"+e).prop("disabled",!1),$("#respuesta").html('<div class="mt-2 animate__animated animate__fadeInDown alert alert-danger alert-dismissible fade show fontq" role="alert"><strong>Error</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')}})}}),t.stopImmediatePropagation()}));