$(document).ready((function(){function o(){$("#datoFicNovA").val("0"),$("#datoFicFalta").val("0"),$("#datoFicNovT").val("0"),$("#datoFicNovI").val("0"),$("#datoFicNovS").val("0"),$("#FicNovI").prop("checked",!1),$("#FicFalta").prop("checked",!1),$("#FicNovT").prop("checked",!1),$("#FicNovS").prop("checked",!1),$("#FicNovA").prop("checked",!1),$("#FicNovA").prop("disabled",!1),$("#FicFalta").prop("disabled",!1),$("#FicNovT").prop("disabled",!1),$("#FicNovI").prop("disabled",!1),$("#FicNovS").prop("disabled",!1)}function e(){$("#datoTotNove").val("0"),$("#TotNove").prop("checked",!1),$("#VerNove").prop("checked",!1),$("#datoVerNove").val("0")}function a(){$("#datoTotNove").val("1"),$("#TotNove").prop("checked",!0),$("#VerNove").prop("checked",!0),$("#datoVerNove").val("1")}function t(){$("#datoTotHoras").val("0"),$("#TotHoras").prop("checked",!1),$("#VerHoras").prop("checked",!1),$("#datoVerHoras").val("0")}function c(){$("#datoVerFic").val("0"),$("#VerFic").prop("checked",!1)}$(".select2").select2({minimumResultsForSearch:-1,placeholder:"Seleccionar"}),SelectSelect2(".select2Plantilla",!0,"Plantilla",0,-1,10,!1),$("#RangoDr").html($("#_dr").val()),$("#_dr").change((function(){$("#RangoDr").html($("#_dr").val())})),$(document).on("click","#FiltroReporte",(function(o){CheckSesion(),$(document).off("keydown"),o.preventDefault(),$("#Exportar").modal("hide"),$("#Filtros").modal("show")})),$(document).on("click","#ReporteFiltro",(function(o){$(document).off("keydown"),o.preventDefault(),$("#Filtros").modal("hide"),$("#Exportar").modal("show")})),$("#Exportar").on("shown.bs.modal",(function(){CheckSesion(),$("#_titulo").select(),$(document).off("keydown")})),$("#Exportar").on("hidden.bs.modal",(function(){})),$("#datoSaltoPag").val("0"),$("#SaltoPag").change((function(){$("#SaltoPag").is(":checked")?$("#datoSaltoPag").val("1"):$("#datoSaltoPag").val("0")})),$("#datoTotHoras").val("1"),$("#TotHoras").prop("checked",!0),$("#TotHoras").change((function(){$("#TotHoras").is(":checked")?$("#datoTotHoras").val("1"):$("#datoTotHoras").val("0")})),$("#datoTotNove").val("1"),$("#TotNove").prop("checked",!0),$("#TotNove").change((function(){$("#TotNove").is(":checked")?$("#datoTotNove").val("1"):$("#datoTotNove").val("0")})),$("#datoVerHoras").val("1"),$("#VerHoras").prop("checked",!0),$("#VerHoras").change((function(){$("#VerHoras").is(":checked")?($("#datoTotHoras").val("1"),$("#TotHoras").prop("checked",!0),$("#datoVerHoras").val("1")):($("#datoTotHoras").val("0"),$("#TotHoras").prop("checked",!1),$("#datoVerHoras").val("0"))})),$("#datoVerNove").val("1"),$("#VerNove").prop("checked",!0),$("#VerNove").change((function(){$("#VerNove").is(":checked")?($("#datoTotNove").val("1"),$("#TotNove").prop("checked",!0),$("#datoVerNove").val("1")):($("#datoTotNove").val("0"),$("#TotNove").prop("checked",!1),$("#datoVerNove").val("0"))})),$("#datoVerFic").val("1"),$("#VerFic").prop("checked",!0),$("#VerFic").change((function(){$("#VerFic").is(":checked")?$("#datoVerFic").val("1"):$("#datoVerFic").val("0")})),$("#_plantilla").on("select2:select",(function(r){"p_fic"==$("#_plantilla").val()?($("#_titulo").val("Reporte de Fichadas (Entrada y Salida)"),o(),$("#datoVerFic").val("1"),$("#VerFic").prop("checked",!0),e(),t()):"p_nov"==$("#_plantilla").val()?(o(),$("#_titulo").val("Reporte de Novedades"),a(),c(),t()):"p_hor"==$("#_plantilla").val()?(o(),$("#_titulo").val("Reporte de Horas"),$("#datoTotHoras").val("1"),$("#TotHoras").prop("checked",!0),$("#VerHoras").prop("checked",!0),$("#datoVerHoras").val("1"),c(),e()):"p_tar"==$("#_plantilla").val()?(o(),$("#_titulo").val("Reporte de Tardes"),$("#datoFicNovT").val("1"),$("#FicNovT").prop("checked",!0),$("#FicNovA").prop("disabled",!0),a(),c(),t()):"p_aus"==$("#_plantilla").val()?(o(),$("#_titulo").val("Reporte de Ausencias"),$("#datoFicNovA").val("1"),$("#FicNovA").prop("checked",!0),$("#FicFalta").prop("disabled",!0),$("#FicNovT").prop("disabled",!0),$("#FicNovI").prop("disabled",!0),$("#FicNovS").prop("disabled",!0),a(),c(),t()):"p_sal"==$("#_plantilla").val()?(o(),$("#_titulo").val("Reporte de Salidas Anticipadas"),$("#datoFicNovS").val("1"),$("#FicNovS").prop("checked",!0),$("#FicNovA").prop("disabled",!0),a(),c(),t()):"p_inc"==$("#_plantilla").val()&&(o(),$("#_titulo").val("Reporte de Incumplimientos"),$("#datoFicNovI").val("1"),$("#FicNovI").prop("checked",!0),$("#FicNovA").prop("disabled",!0),a(),c(),t())}));let r="Generar PDF",i=$("#FicDiaL").is(":checked")?1:0;$(document).on("change","#FicDiaL",(function(o){i=$("#FicDiaL").is(":checked")?1:0})),$("#btnExportar").html(r),$("#FormExportar").bind("submit",(function(o){o.preventDefault(),CheckSesion(),$.ajax({type:$(this).attr("method"),url:$(this).attr("action"),data:$(this).serialize()+"&_l= "+$("#_l").val()+"&Filtros= "+_Filtros()+"&Per= "+$("#Per").val()+"&Tipo= "+$("#Tipo").val()+"&Emp= "+$("#Emp").val()+"&Plan= "+$("#Plan").val()+"&Sect= "+$("#Sect").val()+"&Sec2= "+$("#Sec2").val()+"&Grup= "+$("#Grup").val()+"&Sucur= "+$("#Sucur").val()+"&_dr= "+$("#_dr").val()+"&FicDiaL= "+i+"&FicFalta= "+$("#datoFicFalta").val()+"&FicNovT= "+$("#datoFicNovT").val()+"&FicNovI= "+$("#datoFicNovI").val()+"&FicNovS= "+$("#datoFicNovS").val()+"&FicNovA= "+$("#datoFicNovA").val()+"&Fic3Nov= "+$("#datoNovedad").val(),dataType:"json",beforeSend:function(o){ActiveBTN(!0,"#btnExportar","Generando.!",r),$("#IFrame").addClass("d-none"),$("#Permisos").collapse("hide"),$.notifyClose();notify('Generando <span class = "dotting mr-1"> </span> <div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>',"info",0,"right")},success:function(o){if("ok"==o.status)if(ActiveBTN(!1,"#btnExportar","Generando.!",r),"V"==o.destino){var e=$("#_homehost").val(),a=$("#_host").val();window.open(a+"/"+e+"/general/reporte/archivos/"+o.archivo,"_blank"),$.notifyClose(),notify("Reporte Generado","success",2e3,"right"),ActiveBTN(!1,"#btnExportar","Generando.!",r)}else $("#IFrame").removeClass("d-none"),$("#IFrame").html(`<div class="col-12 pt-2"><iframe id="IframeID" src="reporte/archivos/${o.archivo}" width="100%" height="600" style="border:none;"></iframe></div>`),ActiveBTN(!1,"#btnExportar","Generando.!",r),$.notifyClose(),notify("Reporte Generado","success",2e3,"right");else ActiveBTN(!1,"#btnExportar","Generando.!",r),$.notifyClose(),notify(`${o.Mensaje}`,"danger",5e3,"right")},error:function(){$.notifyClose(),notify("Error","danger",5e3,"right"),ActiveBTN(!1,"#btnExportar","Generando.!",r)}})}))}));