$(document).ready((function(){let e='<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>';function a(){$("#Legajos").is(":checked")?($("#Personal-select-all").prop("checked",!0),$("#Personal-select-all").prop("disabled",!0),$(".check").prop("checked",!0),$(".check").prop("disabled",!0),$("#GetPersonal_filter input").prop("disabled",!0)):($("#Personal-select-all").prop("disabled",!1),$(".check").prop("checked",!0),$(".check").prop("disabled",!1),$("#GetPersonal_filter input").prop("disabled",!1))}ActiveBTN(!1,"#submit",'Procesando <span class = "dotting mr-1"> </span> '+e,"Procesar"),$(".procesando").bind("submit",(function(a){a.preventDefault(),$.ajax({type:$(this).attr("method"),url:$(this).attr("action"),data:$(this).serialize(),beforeSend:function(a){$.notifyClose(),notify('Procesando <span class = "dotting mr-1"> </span> '+e,"info",0,"right"),ActiveBTN(!0,"#submit",'Procesando <span class = "dotting mr-1"> </span> '+e,"Procesar")},success:function(a){"ok"==a.status?($.notifyClose(),notify(a.Mensaje,"success",5e3,"right"),ActiveBTN(!1,"#submit",'Procesando <span class = "dotting mr-1"> </span> '+e,"Procesar")):($.notifyClose(),notify(a.Mensaje,"danger",2e3,"right"),ActiveBTN(!1,"#submit",'Procesando <span class = "dotting mr-1"> </span> '+e,"Procesar"))},error:function(){$.notifyClose(),notify("Error","danger",2e3,"right"),ActiveBTN(!1,"#submit",'Procesando <span class = "dotting mr-1"> </span> '+e,"Procesar")}}),a.stopImmediatePropagation()})),$(window).width()<769&&$('input[name="_dr"]').prop("readonly",!0),$("#Legajos").prop("disabled",!0),table=$("#GetPersonal").DataTable({bProcessing:!0,bServerSide:!0,deferRender:!0,ajax:{url:"getPersonal.php",type:"POST",data:function(e){e.Per=$("#ProcPer").val(),e.Tipo=$("#ProcTipo").val(),e.Emp=$("#ProcEmp").val(),e.Plan=$("#ProcPlan").val(),e.Sect=$("#ProcSect").val(),e.Sec2=$("#ProcSec2").val(),e.Grup=$("#ProcGrup").val(),e.Sucur=$("#ProcSucur").val()},error:function(){$("#GetPersonal_processing").css("display","none")}},columns:[{class:"align-middle animate__animated animate__fadeIn",data:"check"},{class:"align-middle animate__animated animate__fadeIn",data:"pers_legajo2"},{class:"align-middle animate__animated animate__fadeIn",data:"pers_nombre2"}],scrollY:"335px",scrollX:!0,scrollCollapse:!1,paging:!0,responsive:!1,searching:!0,info:!0,ordering:!1,language:{url:"../js/DataTableSpanishShort2.json?"+vjs()}}),table.on("init.dt",(function(){$("div.loader").remove(),$("#Personal-select-all").prop("checked",!0),$("#Personal-select-all").prop("disabled",!0),$(".check").prop("checked",!0),$(".check").prop("disabled",!0),$("#GetPersonal_filter input").attr("placeholder","Buscar Legajos"),$("#GetPersonal_filter input").prop("disabled",!0),$("#Legajos").change((function(){a()})),$("#Legajos").prop("disabled",!1)})),table.on("draw.dt",(function(){a()})),$("#Personal-select-all").on("click",(function(){var e=table.rows({search:"applied"}).nodes();$('input[type="checkbox"]',e).prop("checked",this.checked)})),$("#GetPersonal tbody").on("change",'input[type="checkbox"]',(function(){if(!this.checked){var e=$("#Personal-select-all").get(0);e&&e.checked&&"indeterminate"in e&&(e.indeterminate=!0)}}))}));