$(document).ready((function(){$("#collapse_rol").on("shown.bs.collapse",(function(){$("#nombre").focus()})),$(document).on("click",".editRol",(function(t){t.preventDefault();var e=$(this).attr("datarol"),a=$(this).attr("dataidrol"),o=$(this).attr("datarecid_c");setTimeout((()=>{$(".modal-footer").addClass("bg-light"),$(".modal-header").addClass("border-bottom-0"),$(".bootbox-input-text").addClass("h40"),$(".bootbox-input-text").val(e),$(".bootbox-input-text").select(),$(".bootbox-input-text").attr("placeholder","Nombre Rol")}),200),bootbox.addLocale("custom",{OK:"OK",CONFIRM:"Confirmar",CANCEL:"Cancelar"}),bootbox.prompt({size:"small",buttons:{confirm:{label:"Guardar",className:"btn-custom btn-sm fontq btn-mobile"},cancel:{label:"Cancelar",className:"btn-outline-custom border btn-sm fontq btn-mobile"}},title:"<span class='fonth text-secondary'>Editar Rol: "+e+"</span>",locale:"custom",callback:function(t){t&&$.ajax({type:"POST",url:"crudRol.php",data:{submit:"editarol",id:a,nombre:e,recid_c:o,nombre_nuevo:t},beforeSend:function(t){$("#Editar_"+a).prop("disabled",!0),$.notifyClose(),notify("Aguarde..","info",0,"right")},success:function(t){"ok"==t.status?($("#Editar_"+a).prop("disabled",!1),$.notifyClose(),notify(t.Mensaje,"success",5e3,"right"),$("#GetRoles").DataTable().ajax.reload()):"nocambios"==t.status?$("#Editar_"+a).prop("disabled",!1):($("#Editar_"+a).prop("disabled",!1),$.notifyClose(),notify(t.Mensaje,"danger",5e3,"right"))},error:function(t){$("#Editar_"+a).prop("disabled",!1),$.notifyClose(),notify("Error","danger",5e3,"right")}})}})})),$(document).on("click",".addRol",(function(t){t.preventDefault();var e=$("#recid_cRol").val();setTimeout((()=>{$(".modal-footer").addClass("bg-light"),$(".modal-header").addClass("border-bottom-0"),$(".bootbox-input-text").addClass("h40"),$(".bootbox-input-text").select(),$(".bootbox-input-text").attr("placeholder","Nombre del Rol")}),200),bootbox.addLocale("custom",{OK:"OK",CONFIRM:"Confirmar",CANCEL:"Cancelar"}),bootbox.prompt({size:"small",buttons:{confirm:{label:"Aceptar",className:"btn-custom btn-sm fontq btn-mobile"},cancel:{label:"Cancelar",className:"btn-outline-custom border btn-sm fontq btn-mobile"}},title:"<span class='fonth text-secondary'>Alta Rol</span>",locale:"custom",callback:function(t){t&&$.ajax({type:"POST",url:"crudRol.php",data:{submit:"addRol",recid_c:e,nombre:t},beforeSend:function(t){$("#AltaRol").prop("disabled",!0)},success:function(t){"ok"==t.status?($("#AltaRol").prop("disabled",!1),$("#GetRoles").DataTable().ajax.reload(),$.notifyClose(),notify(t.Mensaje,"success",5e3,"right")):"nocambios"==t.status?$("#AltaRol").prop("disabled",!1):($("#AltaRol").prop("disabled",!1),$.notifyClose(),notify(t.Mensaje,"danger",5e3,"right"))},error:function(t){$("#AltaRol").prop("disabled",!1),$.notifyClose(),notify("Error","danger",5e3,"right")}})}})})),$(document).on("click",".deleteRol",(function(t){t.preventDefault();var e=$(this).attr("datarol"),a=$(this).attr("dataidrol");$(this).attr("datarecid_c");bootbox.confirm({message:'<span class="fonth fw4">¿Confirma eliminar el Rol: <span class="fw5">'+e+"</span>?</span>",buttons:{confirm:{label:"Confirmar",className:"btn-custom btn-sm fontq btn-mobile"},cancel:{label:"Cancelar",className:"btn-outline-custom border btn-sm fontq btn-mobile"}},callback:function(t){$(".deleteRol").unbind("click"),t&&$.ajax({type:"POST",url:"crudRol.php",data:{submit:"deleteRol",id:a,nombre:e},beforeSend:function(t){$("#delete_"+a).prop("disabled",!0)},success:function(t){"ok"==t.status?($("#delete_"+a).prop("disabled",!1),$.notifyClose(),notify(t.Mensaje,"success",5e3,"right"),$("#GetRoles").DataTable().ajax.reload()):($("#delete_"+a).prop("disabled",!1),$.notifyClose(),notify(t.Mensaje,"danger",5e3,"right"))},error:function(t){$("#delete_"+a).prop("disabled",!1),$.notifyClose(),notify("Error","danger",5e3,"right")}})}}),t.stopImmediatePropagation()})),$("#GetRoles").DataTable({initComplete:function(t,e){$(".form-control-sm").attr("placeholder","Buscar Rol"),$(".LabelSearchDT").html(""),$("#GetRoles_filter").prepend('<button title="Nuevo Rol" class="px-2 btn btn-outline-custom addRol fontq border" id="AltaRol"><span><svg class="" width="20" height="20" fill="currentColor"><use xlink:href="../../img/bootstrap-icons.svg#plus-circle-fill"/></svg></span></button>'),$(".table-responsive").show(),fadeInOnly("#GetRoles"),$(".addRol").hover((function(){$(this).find("span").html('<span class="animate__animated animate__fadeIn"><svg class="mr-2" width="20" height="20" fill="currentColor"><use xlink:href="../../img/bootstrap-icons.svg#plus-circle-fill"/></svg>Nuevo Rol</span>')}),(function(){$(this).find("span").last().html('<span class="animate__animated animate__fadeIn"><svg class="" width="20" height="20" fill="currentColor"><use xlink:href="../../img/bootstrap-icons.svg#plus-circle-fill"/></svg></span>')})),$(".ListaRol").on("click",(function(t){t.preventDefault(),CheckSesion();let e=$(this).attr("data-c"),a="../listas/index.php?_r="+$(this).attr("data-r")+"&id="+$(this).attr("data-id")+"&_c="+e;$.get(a).done((function(t){$("#modalListas .modal-body").html(t),$("#modalListas").modal("show")})),t.stopImmediatePropagation()}))},drawCallback:function(t){$(".contentd").removeClass("text-light bg-light border-0")},lengthMenu:[5,10,25,50,100],columnDefs:[{visible:!1,targets:0},{visible:!1,targets:1},{visible:!1,targets:2},{visible:!1,targets:4},{visible:!1,targets:5},{visible:!1,targets:16},{visible:!1,targets:17}],bProcessing:!0,serverSide:!0,deferRender:!0,searchDelay:1500,iDisplayLenght:5,ajax:{url:"GetRoles.php?_c="+$("#recid_cRol").val(),type:"POST",data:function(t){t.recid_c=$("#recid_cRol").val()},error:function(){$("#GetRoles_processing").hide()}},columns:[{class:"",data:"id"},{class:"",data:"recid"},{class:"",data:"recid_cliente"},{class:"w-100",data:"nombre"},{class:"",data:"id_cliente"},{class:"",data:"cliente"},{class:"text-center",data:"cant_roles"},{class:"text-center",data:"cant_modulos"},{class:"text-center",data:"listas"},{class:"text-center",data:"abm_rol"},{class:"text-center",data:"cant_empresas"},{class:"text-center",data:"cant_plantas"},{class:"text-center",data:"cant_convenios"},{class:"text-center",data:"cant_sectores"},{class:"text-center",data:"cant_grupos"},{class:"text-center",data:"cant_sucur"},{class:"",data:"fecha_alta"},{class:"",data:"fecha_mod"},{class:"",data:"edit_rol"}],paging:!0,responsive:!1,info:!0,searching:!0,ordering:!1,language:{url:"../../js/DataTableSpanishShort.json"}}).on("page.dt",(function(){$(".open-modal").removeClass("btn-outline-custom"),$(".contentd").addClass("text-light bg-light border-0"),$(".botones").hide()})),$("#modalListas").on("hidden.bs.modal",(function(t){$("#modalListas .modal-body").html(""),$.notifyClose()}))}));