$((function(){"use strict";let e=$("#cliente_rol").val(),o=$("#recid_rol").val(),t=$("#id_rol").val(),a=$("#tableHorarios").dataTable({initComplete:function(e,o){0===o.data.length&&($("#tableHorarios").parents(".table-responsive").hide(),$("#horarios").html('<div class="my-3 fontq">No se encontraron resultados</div>'))},dom:"<'row'<'col-12 divHorFilter d-flex align-items-center justify-content-between pt-1'f>><'row'<'col-12 divtablehorario 't>><'row'<'col-12 d-flex align-items-center justify-content-end'><'col-12 d-flex align-items-center justify-content-between divHorarioInfo pt-2 pl-0 pr-0'i><'col-12 pt-2 pl-0 divRefreshHorario'>>",ajax:{url:"../listas/getListas.php",type:"GET",dataType:"json",data:function(o){o._c=e,o.lista=3,o.id_rol=t},error:function(){$("#tableHorarios").css("display","none")}},createdRow:function(e,o,t){$(e).addClass("animate__animated animate__fadeIn pointer checkHorario")},columns:[{className:"align-middle",targets:"codigo",title:"",render:function(e,o,t,a){return t.codigo}},{className:"align-middle w-100 ",targets:"descripcion",title:"",render:function(e,o,t,a){return t.descripcion}},{className:"align-middle",targets:"id",title:"",render:function(e,o,t,a){return t.id}},{className:"align-middle",targets:"",title:"",render:function(e,o,t,a){let i=1===t.set?"checked":"";i&&setTimeout((()=>{$("#Horario_"+t.codigo).parents("tr").addClass("table-active")}),200);let r="Horclass_"+t.idtipo,n=0===t.codigo?32768:t.codigo;return'\n                    <div class="custom-control custom-checkbox">\n                        <input '+i+' type="checkbox" class="custom-control-input '+r+'" id="Horario_'+t.codigo+'" value="'+n+'">\n                        <label class="custom-control-label" for="Horario_'+t.codigo+'"></label>\n                    </div>\n                    '}}],bProcessing:!0,serverSide:!1,deferRender:!0,paging:!1,searching:!0,info:!0,ordering:0,responsive:0,language:{sProcessing:"Actualizando . . .",sLengthMenu:"_MENU_",sZeroRecords:"",sEmptyTable:"",sInfo:"_START_ al _END_ de _TOTAL_ Horarios",sInfoEmpty:"No se encontraron resultados",sInfoFiltered:"<br>(Filtrado de un total de _MAX_ Horarios)",sInfoPostFix:"",sSearch:"",sUrl:"",sInfoThousands:",",sLoadingRecords:"<div class='spinner-border text-light'></div>",oPaginate:{sFirst:"<i class='bi bi-chevron-left'></i>",sLast:"<i class='bi bi-chevron-right'></i>",sNext:"<i class='bi bi-chevron-right'></i>",sPrevious:"<i class='bi bi-chevron-left'></i>"},oAria:{sSortAscending:":Activar para ordenar la columna de manera ascendente",sSortDescending:":Activar para ordenar la columna de manera descendente"}}});a.on("init.dt",(function(){$("#tableHorarios_info").css("margin-top","0px"),$("#tableHorarios_info").addClass("p-0"),$("#tableHorarios tbody").on("click",".checkHorario",(function(e){e.preventDefault();let o=$(a).DataTable().row($(this)).data();$("#Horario_"+o.codigo).is(":checked")?($("#Horario_"+o.codigo).prop("checked",!1),$(this).removeClass("table-active")):($("#Horario_"+o.codigo).prop("checked",!0),$(this).addClass("table-active"))})),$("#tableHorarios_filter .form-control").attr("placeholder","Buscar Horario"),$(this).children("thead").remove(),$(".divtablehorario").css("max-height","300px"),$(".divtablehorario").addClass("overflow-auto");$(this).parents().find(".divHorFilter").prepend('\n            <div class="">\n                <button class="btn btn-link btn-sm fontq" id="checkAllHorario">Marcar</button>\n                <button class="ml-1 btn btn-link btn-sm fontq" id="nocheckAllHorario">Desmarcar</button>\n            </div>\n            '),$(this).parents().find(".divHorarioInfo").append('\n            <div class="">\n                <button type="button" class="btn btn-custom btn-sm btn-mobile fontq w100" id="aplicarHorarios">Aplicar</button>\n            </div>\n            '),$(this).parents().find(".divRefreshHorario").append('\n            <div class="">\n                <button class="btn btn-link btn-sm fontq p-0 m-0" id="refreshHorarioList">Actualizar Grilla</button>\n            </div>\n            '),$("#refreshHorarioList").on("click",(function(e){e.preventDefault(),$("#tableHorarios").DataTable().ajax.reload()})),$("#checkAllHorario").on("click",(function(){$("#tableHorarios input:checkbox").prop("checked",!0),$("#tableHorarios tr").addClass("table-active")})),$("#nocheckAllHorario").on("click",(function(){$("#tableHorarios input:checkbox").prop("checked",!1),$("#tableHorarios tr").removeClass("table-active")})),$("#aplicarHorarios").on("click",(function(){CheckSesion();let e=new Array;$("#tableHorarios input:checkbox:checked").each((function(){e.push(parseInt($(this).val()))})),$.ajax({url:"../listas/setLista.php",type:"POST",data:{lista:3,check:JSON.stringify(e),id_rol:t,recid_rol:o},beforeSend:function(e){$.notifyClose(),notify("Aguarde..","info",0,"right")},success:function(e){"ok"==e.status?($.notifyClose(),notify(e.Mensaje,"success",5e3,"right"),$("#tableHorarios").DataTable().ajax.reload()):($.notifyClose(),notify(e.Mensaje,"danger",5e3,"right"),$("#tableHorarios").DataTable().ajax.reload())},error:function(e){$.notifyClose(),notify("Error","danger",5e3,"right")}})})),$("#horarios").removeClass("invisible")}))}));