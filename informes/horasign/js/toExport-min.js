const time=document.getElementById("time").value;let IconExcel='<div class="d-inline-flex" data-titler="Exportar datos"><i class="bi bi-file-earmark-arrow-down-fill"></i><span class="ml-1 d-none d-sm-block">Exportar</span></div>';function toExport(){let e='<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>',t="",a="",n="";"1"==$("input[name=exportLegajo]:checked").val()?(a="",n="","1"==$("input[name=exporType]:checked").val()&&(t="toExcel.php"),"2"==$("input[name=exporType]:checked").val()&&(t="reporte/index.php")):"2"==$("input[name=exportLegajo]:checked").val()&&(a="",n="","1"==$("input[name=exporType]:checked").val()&&(a="1",n="",t="getPersonal.php"),"2"==$("input[name=exporType]:checked").val()&&(a="1",n="1",t="getPersonal.php")),ActiveBTN(!0,"#btnExcel","Exportando "+e,IconExcel),$.notifyClose(),notify('Exportando <span class = "dotting mr-1"> </span> '+e,"info",0,"right");let o=(e,t,a)=>{if(!e)return!1;let n="";return $(e).val()?(n=$(e).val().forEach((e=>{a.append(t+"[]",e)})),n):(n="",n)},l=new FormData;l.append("time",$("#time").val()),l.append("_drhorarios",document.getElementById("_drHorarios").value),l.append("Tipo",$("#Tipo").val()),l.append("toExcelAll",a),l.append("toPdfAll",n),o("#Per","Per",l),o("#Emp","Emp",l),o("#Plan","Plan",l),o("#Sect","Sect",l),o("#Sec2","Sec2",l),o("#Grup","Grup",l),o("#Sucur","Sucur",l),o("#Conv","Conv",l),o("#Tare","Tare",l),o("#Regla","Regla",l),axios({method:"POST",url:"/"+getSelectorVal("#_homehost")+"/status_ws.php?status=ws",dataType:"json",url:t,responseType:"stream",data:l}).then((function(e){if(e.data&&"ok"==e.data.status){ActiveBTN(!1,"#btnExcel","Exportando",IconExcel);let t=e.data;$.notifyClose(),t.archivo?notify('<b>Archivo exportado correctamente</b>.<br><div class="shadow-sm w100"><a href="'+t.archivo+'" class="btn btn-custom px-3 btn-sm mt-2 fontq download" target="_blank" download><div class="d-flex align-items-center"><span>Descargar</span><i class="bi bi-file-earmark-arrow-down ml-1 font1"></i></div></a></div>',"warning",0,"right"):notify("No hay datos a exportar","danger",3e3,"right"),$(document).on("click",".download",(function(e){setTimeout((()=>{$.notifyClose()}),3e3)}))}})).catch((function(e){$.notifyClose(),notify(e.message,"warning",0,"right")}))}ActiveBTN(!0,"#btnExcel",IconExcel,IconExcel),$(document).on("click","#btnExcel",(function(e){$.notifyClose(),e.preventDefault(),CheckSesion(),axios.get("js/bodyModal.html?"+time).then((function(e){bootbox.confirm({message:e.data,buttons:{confirm:{label:"Aceptar",className:"btn-custom btn-sm fontq"},cancel:{label:"Cancelar",className:"btn-light btn-sm fontq text-secondary"}},callback:function(e){e&&(CheckSesion(),toExport(),ActiveBTN(!1,"#btnExcel","Exportando",IconExcel))}})}))}));