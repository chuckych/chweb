$((function(){"use strict";function t(t){let e="";switch(t){case"A":e="Alta";break;case"B":e="Baja";break;case"M":e="Modificación";break;case"P":e="Proceso";break;default:e=row.tipo;break}return e}function e(t,e,a=""){let o=moment(t,"YYYY"),n=moment(e,"YYYY");a=a||e,$("#_dr").daterangepicker({singleDatePicker:!1,showDropdowns:!0,minYear:parseInt(o),maxYear:parseInt(n),startDate:a,endDate:e,minDate:t,maxDate:e,showWeekNumbers:!1,autoUpdateInput:!0,opens:"center",drops:"down",autoApply:!1,alwaysShowCalendars:!0,linkedCalendars:!1,buttonClasses:"btn btn-sm fontq",applyButtonClasses:"btn-custom  border fw4 px-3 opa8",cancelClass:"btn-link fw4 text-gris",ranges:{Hoy:[moment(),moment()],Ayer:[moment().subtract(1,"days"),moment().subtract(1,"days")],"Ultimos 7 Días":[moment().subtract(6,"days"),moment()],"Ultimos 30 Días":[moment().subtract(29,"days"),moment()],"Este Mes":[moment().startOf("month"),moment().endOf("month")],"Ultimo Mes":[moment().subtract(1,"month").startOf("month"),moment().subtract(1,"month").endOf("month")],"Todo el Periodo":[t,e]},locale:{format:"DD/MM/YYYY",separator:" al ",applyLabel:"Aplicar",cancelLabel:"Cancelar",fromLabel:"Desde",toLabel:"Para",customRangeLabel:"Personalizado",weekLabel:"Sem",daysOfWeek:["Do","Lu","Ma","Mi","Ju","Vi","Sa"],monthNames:["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],firstDay:1,alwaysShowCalendars:!0,applyButtonClasses:"btn-custom fw5 px-3 opa8"}}),$("#_dr").on("apply.daterangepicker",(function(t,e){CheckSesion(),$("#tableAuditoria").DataTable().ajax.reload()}))}let a=$("#tableAuditoria").dataTable({lengthMenu:[[5,10,25,50,100],[5,10,25,50,100]],bProcessing:!1,serverSide:!0,deferRender:!0,responsive:!0,dom:"<'row fila invisible animate__animated animate__fadeInDown'<'col-12 col-sm-6 d-flex justify-content-start'<'filtros'>l><'col-12 col-sm-6 d-flex justify-content-end'<'ml-1 _dr'><'refresh'>>><'row'<'col-12 divFiltros'>><'row fila invisible animate__animated animate__fadeInDown'<'col-12'f>><'row animate__animated animate__fadeIn'<'col-12 table-responsive'tr>><'row animate__animated animate__fadeIn'<'col-12 col-sm-5'i><'col-12 col-sm-7 d-flex justify-content-end'p>>",ajax:{url:"getAuditoria.php?"+$.now(),type:"POST",dataType:"json",data:function(t){t._dr=$("#_dr").val(),t.nombreAud=$("#nombreAud").val(),t.tipoAud=$("#tipoAud").val(),t.userAud=$("#userAud").val(),t.idSesionAud=$("#idSesionAud").val(),t.horaAud=$("#horaAud").val(),t.horaAud2=$("#horaAud2").val(),t.cuentaAud=$("#cuentaAud").val()},error:function(){$("#tablePersonal").css("display","none")}},createdRow:function(t,e,a){$(t).attr({"data-id":e.id,"data-idsesion":e.id_sesion,title:"Ver detalle"})},columns:[{className:"",targets:"",title:"<span data-titler='Nombre / Usuario'>Usuario</span>",render:function(t,e,a,o){return'<div><div class="fw5">'+a.nombre+"</div><div>"+a.usuario+"</div></div>"}},{className:"text-center",targets:"",title:"ID Sesion",render:function(t,e,a,o){return"<div>"+a.id_sesion+"</div>"}},{className:"",targets:"",title:"Cuenta",render:function(t,e,a,o){return"<div>"+a.audcuenta_nombre+"</div>"}},{className:"",targets:"",title:"Fecha Hora",render:function(t,e,a,o){return"<div class='ls1'>"+moment(a.fecha).format("DD/MM/YYYY")+"</div><div class='ls1'>"+a.hora+"</div>"}},{className:"text-center",targets:"",title:"<span data-titlel='Tipo de registro'>Tipo</span>",render:function(e,a,o,n){return'<div data-titlel="'+t(o.tipo)+'">'+o.tipo+"</div>"}},{className:"w-100 text-wrap",targets:"",title:"<span data-titlel='Información de la auditoría'>Dato</span>",render:function(t,e,a,o){return'<div data-titlel="'+a.dato+'">'+a.dato+"</div>"}}],paging:!0,searching:!0,info:!0,ordering:0,responsive:0,language:{url:"../../js/DataTableSpanishShort2.json?"+vjs()}});a.on("init.dt",(function(t,a){let o="#"+t.target.id,n=$(o+"_length select");$(n).addClass("h35");let l=$(o+"_filter input");$(l).attr({placeholder:"Buscar dato..",id:"datosAud",autocomplete:"off"}),fetch("getFechas.php").then((t=>t.json())).then((t=>{$("._dr").html('<label><input readonly title="Filtrar Fecha" type="text" id="_dr" class="form-control h35 text-center ls1 w250 bg-white" autocomplete=off></label>'),$(".refresh").html('<label><button data-titlel="Actualizar Grilla"class="btn ml-1 h35 btn-custom fontq" id="refresh"><i class="bi bi-arrow-repeat"></i></button></label>'),$(".filtros").html('<div class="d-inline-flex align-items-center"><button data-titler="Filtros" class="btn h35 btn-outline-custom border fontq" id="filtros" type="button" data-toggle="collapse" data-target="#collapseFiltros" aria-expanded="false" aria-controls="collapseFiltros"><i class="bi bi-funnel"></i></button><button id="trash_all" data-titler="Limpiar Filtros" class="bi bi-trash fontq text-secondary pointer btn h35 btn-outline-custom border fontq border-0"></button></div>'),fetch("filtros.php").then((t=>t.text())).then((t=>{$(".divFiltros").html(t),$(".fila").removeClass("invisible")})),e(moment(t.start_date).format("DD/MM/YYYY"),moment(t.end_date).format("DD/MM/YYYY"));let a=moment($("#_dr").data("daterangepicker").endDate._d).format("YYYYMMDD");$("#refresh").on("click",(function(){fetch("getFechas.php").then((t=>t.json())).then((t=>{let o=moment($("#_dr").data("daterangepicker").startDate._d).format("DD/MM/YYYY"),n=moment(t.end_date).format("DD/MM/YYYY"),l=moment(t.end_date).format("YYYYMMDD"),r=parseInt(a)<parseInt(l)?l:a;parseInt(a)<parseInt(r)?(e(o,n,o),a=r,$("#tableAuditoria").DataTable().ajax.reload()):$("#tableAuditoria").DataTable().ajax.reload(null,!1)}))}))})),$(o).children("tbody").on("click","tr",(function(){CheckSesion();let t=$(o).DataTable().row($(this)).data();fetch("modal.html?v="+vjs()).then((t=>t.text())).then((e=>{let a="#modalAuditoria",o="#detalleAud";$(a).html(e),$(a+" .modal-title").html("Información de Auditoría"),$(o).modal("show"),$(o+" .l div").addClass("bg-white text-white"),fetch("getDetalle.php",{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:"i="+t.id+"&s="+t.id_sesion}).then((t=>t.json())).then((t=>{1==t.data&&($(a+" #aud_nomb").html(t.aud_nomb),$(a+" #aud_user").html(t.aud_user),$(a+" #aud_nacu").html(t.aud_nacu),$(a+" #aud_fech").html(t.aud_fech),$(a+" #aud_hora").html(t.aud_hora),$(a+" #aud_tipo").html(t.aud_tipn),$(a+" #aud_modu").html(t.aud_modu),$(a+" #aud_dato").html(t.aud_dato),$(a+" #log_fech").html(t.log_fech),$(a+" #log_hora").html(t.log_hora),$(a+" #log_idse").html(t.log_idse),$(a+" #log_nrol").html(t.log_nrol),$(a+" #log_d_ip").html(t.log_d_ip),$(a+" #log_agen").html(t.log_age1+". "+t.log_age2+": "+t.log_age3),$(o).on("hidden.bs.modal",(function(t){$(a).html("")})),$(o+" .l div").removeClass("bg-white text-white"),$(o+" .l div").addClass("animate__animated animate__fadeIn"))}))}))}))})),a.on("page.dt",(function(t,e){let a="#"+t.target.id;CheckSesion(),$(a+" div").addClass("blurtd")})),a.on("draw.dt",(function(t,e){let a="#"+t.target.id;$(a+" div").removeClass("blurtd"),$("#divTableAud").show(),$(a+"_previous").attr("data-titlel","Anterior"),$(a+"_next").attr("data-titlel","Siguiente")}))}));