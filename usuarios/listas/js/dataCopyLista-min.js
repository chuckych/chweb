$((function(){"use strict";let t=$("#cliente_rol").val(),e=$("#recid_rol").val(),a=$("#id_rol").val(),i=$("#tableCopyListas").dataTable({dom:"<'row'<'col-12 divCopyListaFilter d-flex align-items-center justify-content-between pt-1'f>><'row'<'col-12 divtablecopylista 't>><'row'<'col-12 d-flex align-items-center justify-content-end'><'col-12 d-flex align-items-center justify-content-between divCopyListaInfo pt-2 pl-0 pr-0'i><'col-12 pt-2 pl-0 divRefreshCopyLista'>>",ajax:{url:"../listas/getListas.php",type:"GET",dataType:"json",data:function(e){e._c=t,e.lista=10,e.id_rol=a},error:function(){$("#tableCopyListas").css("display","none")}},createdRow:function(t,e,a){$(t).addClass("animate__animated animate__fadeIn pointer checkCopyLista")},columns:[{className:"align-middle",targets:"codigo",title:"",render:function(t,e,a,i){return a.codigo}},{className:"align-middle w-100 ",targets:"descripcion",title:"",render:function(t,e,a,i){return a.descripcion}},{className:"align-middle",targets:"",title:"",render:function(t,e,a,i){let s=1===a.set?"checked":"";return s&&setTimeout((()=>{$("#CopyLista_"+a.codigo).parents("tr").addClass("table-active")}),200),'\n                    <div class="custom-control custom-checkbox">\n                        <input '+s+' type="checkbox" class="custom-control-input '+("CopyListaClass_"+a.idtipo)+'" id="CopyLista_'+a.codigo+'" value="'+a.codigo+'">\n                        <label class="custom-control-label" for="CopyLista_'+a.codigo+'"></label>\n                    </div>\n                    '}}],bProcessing:!0,serverSide:!1,deferRender:!0,paging:!1,searching:!0,info:!0,ordering:0,responsive:0,language:{sProcessing:"Actualizando . . .",sLengthMenu:"_MENU_",sZeroRecords:"",sEmptyTable:"",sInfo:"_START_ al _END_ de _TOTAL_ Roles",sInfoEmpty:"No se encontraron resultados",sInfoFiltered:"<br>(Filtrado de un total de _MAX_ Roles)",sInfoPostFix:"",sSearch:"",sUrl:"",sInfoThousands:",",sLoadingRecords:"<div class='spinner-border text-light'></div>",oPaginate:{sFirst:"<i class='bi bi-chevron-left'></i>",sLast:"<i class='bi bi-chevron-right'></i>",sNext:"<i class='bi bi-chevron-right'></i>",sPrevious:"<i class='bi bi-chevron-left'></i>"},oAria:{sSortAscending:":Activar para ordenar la columna de manera ascendente",sSortDescending:":Activar para ordenar la columna de manera descendente"}}});i.on("init.dt",(function(){$("#tableCopyListas_info").css("margin-top","0px"),$("#tableCopyListas_info").addClass("p-0"),$("#tableCopyListas tbody").on("click",".checkCopyLista",(function(t){t.preventDefault();let e=$(i).DataTable().row($(this)).data();$("#CopyLista_"+e.codigo).is(":checked")?($("#CopyLista_"+e.codigo).prop("checked",!1),$(this).removeClass("table-active")):($("#CopyLista_"+e.codigo).prop("checked",!0),$(this).addClass("table-active"))})),$("#tableCopyListas_filter .form-control").attr("placeholder","Buscar Rol"),$(this).children("thead").remove(),$(".divtablecopylista").css("max-height","300px"),$(".divtablecopylista").addClass("overflow-auto");$(this).parents().find(".divCopyListaFilter").prepend('\n            <div class="">\n                <button class="btn btn-link btn-sm fontq" id="checkAllCopyLista">Marcar</button>\n                <button class="ml-1 btn btn-link btn-sm fontq" id="nocheckAllCopyLista">Desmarcar</button>\n            </div>\n            '),$(this).parents().find(".divCopyListaInfo").append('\n            <div class="">\n                <button type="button" class="btn btn-custom btn-sm btn-mobile fontq w100" id="aplicarCopyLista">Aplicar</button>\n            </div>\n            '),$(this).parents().find(".divRefreshCopyLista").append('\n            <div class="">\n                <button class="btn btn-link btn-sm fontq p-0 m-0" id="refreshCopyLista">Actualizar Grilla</button>\n            </div>\n            '),$("#refreshCopyLista").on("click",(function(t){t.preventDefault(),$("#tableCopyListas").DataTable().ajax.reload()})),$("#checkAllCopyLista").on("click",(function(){$("#tableCopyListas input:checkbox").prop("checked",!0),$("#tableCopyListas tr").addClass("table-active")})),$("#nocheckAllCopyLista").on("click",(function(){$("#tableCopyListas input:checkbox").prop("checked",!1),$("#tableCopyListas tr").removeClass("table-active")})),$("#copyListas .nombreRol").html($("#modalListasLabel .nombreRol").text()),$("#aplicarCopyLista").on("click",(function(){$("#tableCopyListas").DataTable().search("").draw(),CheckSesion();let t=new Array;$("#tableCopyListas input:checkbox:checked").each((function(){t.push(parseInt($(this).val()))})),$.ajax({url:"../listas/setLista.php",type:"POST",data:{listaRol:10,check:JSON.stringify(t),id_rol:a,recid_rol:e},beforeSend:function(t){$.notifyClose(),notify("Aguarde..","info",0,"right")},success:function(t){"ok"==t.status?($.notifyClose(),notify(t.Mensaje,"success",1e4,"right"),$("#tableCopyListas").DataTable().ajax.reload()):($.notifyClose(),notify(t.Mensaje,"danger",5e3,"right"),$("#tableCopyListas").DataTable().ajax.reload())},error:function(t){$.notifyClose(),notify("Error","danger",5e3,"right")}})})),$("#copyListas").removeClass("invisible")}))}));