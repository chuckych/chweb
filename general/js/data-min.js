function ActualizaTablas(){$(".modal-footer .result").html(""),$("#Visualizar").is(":checked")?($("#GetFechas").DataTable().ajax.reload(null,!1),$("#GetFechas_paginate .page-link").addClass("border border-0")):($("#GetPersonal").DataTable().ajax.reload(null,!1),$("#Per2").addClass("d-none"),$(".pers_legajo").removeClass("d-none"))}function ActualizaTablas2(){$(".modal-footer .result").html(""),$("#Visualizar").is(":checked")?($("#GetFechas").DataTable().ajax.reload(),$("#GetFechas_paginate .page-link").addClass("border border-0")):($("#GetPersonal").DataTable().ajax.reload(),$("#Per2").addClass("d-none"),$(".pers_legajo").removeClass("d-none"))}function atajosTeclado(){let a={17:!1,18:!1,32:!1,16:!1,39:!1,37:!1,13:!1,27:!1};$(document).keydown((function(e){e.keyCode in a&&(a[e.keyCode]=!0,a[32]&&($("#Exportar").hasClass("show")||$("#Filtros").modal("show"))),e.keyCode in a&&(a[e.keyCode]=!0,a[27]&&$("#Filtros").modal("hide")),e.keyCode in a&&(a[e.keyCode]=!0,a[39]&&($("#Visualizar").is(":checked")?$("#GetFechas").DataTable().page("next").draw("page"):$("#GetPersonal").DataTable().page("next").draw("page"))),e.keyCode in a&&(a[e.keyCode]=!0,a[37]&&($("#Visualizar").is(":checked")?$("#GetFechas").DataTable().page("previous").draw("page"):$("#GetPersonal").DataTable().page("previous").draw("page")))})).keyup((function(e){e.keyCode in a&&(a[e.keyCode]=!1)}))}function _Filtros(){let a=parseInt($("#LegDe").val()),e=parseInt($("#LegHa").val());a&&e&&(a>e?$("#LegDe").val(e):$("#LegDe").val(a));let l={LegDe:a,LegHa:e,SoloFic:$("#SoloFic").is(":checked")?1:0};return JSON.stringify(l)}$("#pagFech").hide(),$("#GetGeneralFechaTable").hide(),$("#datoFicFalta").val("0"),$("#FicFalta").change((function(){$("#FicFalta").is(":checked")?($("#datoFicFalta").val("1"),$("#datoFicNovA").val("0"),$("#FicNovA").prop("checked",!1),$("#FicNovA").prop("disabled",!0),ActualizaTablas2()):($("#datoFicFalta").val("0"),$("#FicNovA").prop("disabled",!1),ActualizaTablas2())})),$("#datoFicNovT").val("0"),$("#FicNovT").change((function(){$("#FicNovT").is(":checked")?($("#datoFicNovT").val("1"),$("#datoFicNovA").val("0"),$("#FicNovA").prop("checked",!1),$("#FicNovA").prop("disabled",!0),ActualizaTablas2(),$("#datoNovedad").val(null).trigger("change")):($("#datoNovedad").val(null).trigger("change"),$("#datoFicNovT").val("0"),$("#FicNovA").prop("disabled",!1),ActualizaTablas2())})),$("#datoFicNovI").val("0"),$("#FicNovI").change((function(){$("#FicNovI").is(":checked")?($("#datoNovedad").val(null).trigger("change"),$("#datoFicNovI").val("1"),$("#datoFicNovA").val("0"),$("#FicNovA").prop("checked",!1),$("#FicNovA").prop("disabled",!0),ActualizaTablas2()):($("#datoNovedad").val(null).trigger("change"),$("#datoFicNovI").val("0"),$("#FicNovA").prop("disabled",!1),ActualizaTablas2())})),$("#datoFicNovS").val("0"),$("#FicNovS").change((function(){$("#FicNovS").is(":checked")?($("#datoNovedad").val(null).trigger("change"),$("#datoFicNovS").val("1"),$("#datoFicNovA").val("0"),$("#FicNovA").prop("checked",!1),$("#FicNovA").prop("disabled",!0),ActualizaTablas2()):($("#datoNovedad").val(null).trigger("change"),$("#datoFicNovS").val("0"),$("#FicNovA").prop("disabled",!1),ActualizaTablas2())})),$("#datoFicNovA").val("0"),$("#FicNovA").change((function(){$("#FicNovA").is(":checked")?($("#datoNovedad").val(null).trigger("change"),$("#datoFicNovA").val("1"),$("#datoFicFalta").val("0"),$("#datoFicNovT").val("0"),$("#datoFicNovI").val("0"),$("#datoFicNovS").val("0"),$("#FicNovI").prop("checked",!1),$("#FicFalta").prop("checked",!1),$("#FicNovT").prop("checked",!1),$("#FicNovS").prop("checked",!1),$("#FicFalta").prop("disabled",!0),$("#FicNovT").prop("disabled",!0),$("#FicNovI").prop("disabled",!0),$("#FicNovS").prop("disabled",!0),ActualizaTablas2()):($("#datoNovedad").val(null).trigger("change"),$("#datoFicNovA").val("0"),$("#FicFalta").prop("disabled",!1),$("#FicNovT").prop("disabled",!1),$("#FicNovI").prop("disabled",!1),$("#FicNovS").prop("disabled",!1),ActualizaTablas2())})),_Filtros(),$("._filtroLegDeHa").change((function(a){a.preventDefault(),$(".selectjs_personal").val(null).trigger("change")})),$("._filtro").change((function(a){a.preventDefault(),_Filtros(),ActualizaTablas2()}));let GetPersonal=$("#GetPersonal").DataTable({initComplete:function(a,e){},pagingType:"full",lengthMenu:[[1],[1]],bProcessing:!1,serverSide:!0,deferRender:!0,searchDelay:1500,dom:',<"d-inline-flex d-flex align-items-center"t<"m,l-2"p>><"mt-n3 d-flex justify-content-end"i>',ajax:{url:"/"+$("#_homehost").val()+"/general/GetPersonalFichas.php",type:"POST",data:function(a){a._l=$("#_l").val(),a.Per=$("#Per").val(),a.Per2=$("#Per2").val(),a.Tipo=$("#Tipo").val(),a.Emp=$("#Emp").val(),a.Plan=$("#Plan").val(),a.Sect=$("#Sect").val(),a.Sec2=$("#Sec2").val(),a.Grup=$("#Grup").val(),a.Sucur=$("#Sucur").val(),a._dr=$("#_dr").val(),a.FicDiaL=$("#FicDiaL").is(":checked")?1:0,a.FicFalta=$("#datoFicFalta").val(),a.FicNovT=$("#datoFicNovT").val(),a.FicNovI=$("#datoFicNovI").val(),a.FicNovS=$("#datoFicNovS").val(),a.FicNovA=$("#datoFicNovA").val(),a.Fic3Nov=$("#datoNovedad").val(),a.FechaFin=$("#FechaFin").val(),a.Filtros=_Filtros()},error:function(){$("#GetPersonal_processing").css("display","none")}},columns:[{class:"w80 px-3 border fw4 bg-light radius pers_legajo",data:"pers_legajo"},{class:"w300 px-3 border border-left-0 fw4 bg-light radius",data:"pers_nombre"}],paging:!0,responsive:!1,info:!0,ordering:!1,language:{url:"../js/DataTableSpanishShort2.json"}}),buttonCommon={exportOptions:{format:{body:function(a,e,l,o){return 5===l?a.replace(/[$,]/g,""):a}}}};function textResult(a,e,l){if(a>0){let o=a>1?"s":"",i="Hay "+a+" "+l+o+" con resultado"+o;$(e).html(i)}else $(e).html("No se encontraron resultados.");classEfect(e,"animate__animated animate__fadeIn")}$("#GetPersonal").DataTable().on("draw.dt",(function(a,e){e.json.recordsTotal>0?$("#Visualizar").prop("disabled",!1):$("#Visualizar").prop("disabled",!0),!$("#Visualizar").is(":checked")&&textResult(e.json.recordsTotal,".modal-footer .result","legajo"),$("#GetPersonal thead").remove(),$(".page-link").addClass("border border-0"),$(".dataTables_info").addClass("text-secondary"),$("#pagLega").removeClass("d-none"),1===e.iDraw?(atajosTeclado(),$("#GetGeneral").DataTable({initComplete:function(a,e){$("#Refresh").prop("disabled",!1),$("#trash_all").removeClass("invisible"),fadeInOnly("#GetGeneralTable"),fadeInOnly("#pagLega")},lengthMenu:[[30,60,90,120],[30,60,90,120]],bProcessing:!0,serverSide:!0,deferRender:!0,searchDelay:1500,ajax:{url:"/"+$("#_homehost").val()+"/general/GetGeneral.php",type:"POST",data:function(a){a.Per=$("#Per").val(),a.Tipo=$("#Tipo").val(),a.Emp=$("#Emp").val(),a.Plan=$("#Plan").val(),a.Sect=$("#Sect").val(),a.Sec2=$("#Sec2").val(),a.Grup=$("#Grup").val(),a.Sucur=$("#Sucur").val(),a._dr=$("#_dr").val(),a._l=$("#_l").val(),a.FicDiaL=$("#FicDiaL").is(":checked")?1:0,a.FicFalta=$("#datoFicFalta").val(),a.FicNovT=$("#datoFicNovT").val(),a.FicNovI=$("#datoFicNovI").val(),a.FicNovS=$("#datoFicNovS").val(),a.FicNovA=$("#datoFicNovA").val(),a.Fic3Nov=$("#datoNovedad").val(),a.FechaFin=$("#FechaFin").val(),a.Filtros=_Filtros()},error:function(){$("#GetGeneral").css("display","none")}},columns:[{class:"align-middle",data:"modal"},{class:"align-middle",data:"FechaDia"},{class:"text-nowrap ls1 align-middle",data:"Gen_Horario"},{class:"ls1 text-center fw4 align-middle",data:"Primera"},{class:"align-middle",data:"DescHoras"},{class:"text-center fw4 ls1 align-middle",data:"HsAuto"},{class:"text-center ls1 align-middle",data:"HsCalc"},{class:"align-middle",data:"Novedades"},{class:"text-center fw4 ls1 align-middle",data:"NovHor"}],scrollX:!0,scrollCollapse:!0,scrollY:"50vmax",paging:!0,info:!0,searching:!1,ordering:!1,language:{url:"../js/DataTableSpanishShort2.json"}}),$("#GetGeneral").DataTable().on("draw.dt",(function(){$(".page-link").addClass("border border-0"),$(".dataTables_info").addClass("text-secondary"),$(".custom-select").addClass("text-secondary bg-light"),$("#pagLega").removeClass("invisible"),$("#GetGeneralTable").removeClass("invisible"),setTimeout((function(){$(".Filtros").prop("disabled",!1)}),1e3),fadeInOnly("#GetGeneral")}))):($("#GetGeneral").DataTable().ajax.reload(),fadeInOnly("#GetGeneral"))})),$("#GetFechas").DataTable({initComplete:function(a,e){$("#GetFechas thead").remove()},drawCallback:function(a){$(".page-link").addClass("border border-0"),a.json.recordsTotal},pagingType:"full",lengthMenu:[[1],[1]],bProcessing:!1,serverSide:!0,deferRender:!0,searchDelay:1500,dom:'<"d-inline-flex d-flex align-items-center"t<"ml-2"p>><"mt-n3 d-flex justify-content-end"i>',ajax:{url:"/"+$("#_homehost").val()+"/general/GetFechasFichas.php",type:"POST",data:function(a){a.Per=$("#Per").val(),a.Tipo=$("#Tipo").val(),a.Emp=$("#Emp").val(),a.Plan=$("#Plan").val(),a.Sect=$("#Sect").val(),a.Sec2=$("#Sec2").val(),a.Grup=$("#Grup").val(),a.Sucur=$("#Sucur").val(),a._dr=$("#_dr").val(),a._l=$("#_l").val(),a.FicDiaL=$("#FicDiaL").is(":checked")?1:0,a.FicFalta=$("#datoFicFalta").val(),a.FicNovT=$("#datoFicNovT").val(),a.FicNovI=$("#datoFicNovI").val(),a.FicNovS=$("#datoFicNovS").val(),a.FicNovA=$("#datoFicNovA").val(),a.Fic3Nov=$("#datoNovedad").val(),a.FechaFin=$("#FechaFin").val(),a.Filtros=_Filtros()},error:function(){$("#GetFecha_processing").css("display","none")}},columns:[{class:"w80 px-3 border fw4 bg-light radius ls1",data:"FicFech"},{class:"w300 px-3 border fw4 bg-light radius",data:"Dia"}],paging:!0,responsive:!1,info:!0,ordering:!1,language:{url:"../js/DataTableSpanishShort2.json"}}),$("#GetFechas").DataTable().on("draw.dt",(function(a,e){$("#Visualizar").is(":checked")&&textResult(e.json.recordsTotal,".modal-footer .result","día"),1===e.iDraw?($("#GetGeneralFecha").DataTable({drawCallback:function(a){$(".page-link").addClass("border border-0"),a.json.recordsTotal,setTimeout((function(){$(".Filtros").prop("disabled",!1)}),1e3)},lengthMenu:[[10,25,50,100],[10,25,50,100]],bProcessing:!0,serverSide:!0,deferRender:!0,searchDelay:1500,dom:"lBfrtip",ajax:{url:"/"+$("#_homehost").val()+"/general/GetGeneralFecha.php",type:"POST",data:function(a){a._f=$("#_f").val(),a.Per=$("#Per").val(),a.Tipo=$("#Tipo").val(),a.Emp=$("#Emp").val(),a.Plan=$("#Plan").val(),a.Sect=$("#Sect").val(),a.Sec2=$("#Sec2").val(),a.Grup=$("#Grup").val(),a.Sucur=$("#Sucur").val(),a._dr=$("#_dr").val(),a._l=$("#_l").val(),a.FicDiaL=$("#FicDiaL").is(":checked")?1:0,a.FicFalta=$("#datoFicFalta").val(),a.FicNovT=$("#datoFicNovT").val(),a.FicNovI=$("#datoFicNovI").val(),a.FicNovS=$("#datoFicNovS").val(),a.FicNovA=$("#datoFicNovA").val(),a.Fic3Nov=$("#datoNovedad").val(),a.FechaFin=$("#FechaFin").val(),a.Filtros=_Filtros()},error:function(){$("#GetGeneralFecha_processing").css("display","none")}},columns:[{class:"align-middle",data:"modal"},{class:"align-middle",data:"LegNombre"},{class:"text-nowrap ls1 align-middle",data:"Gen_Horario"},{class:"ls1 text-center fw4 align-middle",data:"Primera"},{class:"align-middle",data:"DescHoras"},{class:"text-center fw4 ls1 align-middle",data:"HsAuto"},{class:"text-center ls1 align-middle",data:"HsCalc"},{class:"align-middle",data:"Novedades"},{class:"text-center fw4 ls1 align-middle",data:"NovHor"}],scrollX:!0,scrollCollapse:!0,scrollY:"50vmax",paging:!0,info:!0,searching:!0,ordering:!1,language:{url:"../js/DataTableSpanishShort2.json"}}),$("#GetGeneralFecha").DataTable().on("draw.dt",(function(a,e){$(".page-link").addClass("border border-0"),$(".dataTables_info").addClass("text-secondary"),$(".custom-select").addClass("text-secondary bg-light"),fadeInOnly("#GetGeneralFecha")}))):($("#GetGeneralFecha").DataTable().ajax.reload(),$("#GetGeneralFecha").DataTable().on("draw.dt",(function(a,e){fadeInOnly("#GetGeneralFecha"),$("#GetGeneralFechaTable").removeClass("invisible")})))})),$("#GetPersonal").on("page.dt",(function(){CheckSesion()})),$("#GetGeneral").on("page.dt",(function(){CheckSesion()})),$("#GetFechas").on("page.dt",(function(){CheckSesion()})),$("#GetGeneralFecha").on("page.dt",(function(){CheckSesion()})),$("#Refresh").on("click",(function(){ActualizaTablas(),CheckSesion()})),$("#_dr").on("apply.daterangepicker",(function(a,e){let l=e.endDate.format("DD/MM/YYYY"),o=e.startDate.format("DD/MM/YYYY");$("#_drFiltro").data("daterangepicker").setStartDate(o),$("#_drFiltro").data("daterangepicker").setEndDate(l),CheckSesion(),ActualizaTablas()})),$("#_drFiltro").on("apply.daterangepicker",(function(a,e){let l=e.endDate.format("DD/MM/YYYY"),o=e.startDate.format("DD/MM/YYYY");$("#_dr").data("daterangepicker").setStartDate(o),$("#_dr").data("daterangepicker").setEndDate(l),CheckSesion(),ActualizaTablas()})),$("#FicDiaLFiltro").change((function(a){a.preventDefault(),$("#FicDiaLFiltro").is(":checked")?$("#FicDiaL").prop("checked",!0):$("#FicDiaL").prop("checked",!1),ActualizaTablas()})),$("#FicDiaL").change((function(a){a.preventDefault(),$("#FicDiaL").is(":checked")?$("#FicDiaLFiltro").prop("checked",!0):$("#FicDiaLFiltro").prop("checked",!1),CheckSesion(),ActualizaTablas()})),$("#VerPor").html('<span class="d-none d-lg-block">Por Fecha</span>'),$("#VerPorM").html('<span class="d-block d-lg-none">Fecha</span>'),$("#Visualizar").change((function(a){a.preventDefault(),CheckSesion(),$("#Per2").addClass("d-none"),$("#Visualizar").is(":checked")?($("#VisualizarFiltro").prop("checked",!0),$("#GetGeneralTable").addClass("d-none"),$("#GetGeneralFechaTable").show(),$("#pagLega").hide(),$("#pagFech").show()):($("#VisualizarFiltro").prop("checked",!1),$("#GetGeneralTable").removeClass("d-none"),$("#GetGeneralFechaTable").hide(),$("#pagFech").hide(),$("#pagLega").show()),ActualizaTablas()})),$("#VisualizarFiltro").change((function(a){a.preventDefault(),CheckSesion(),$("#Per2").addClass("d-none"),$("#VisualizarFiltro").is(":checked")?($("#Visualizar").prop("checked",!0),$("#GetGeneralTable").addClass("d-none"),$("#GetGeneralFechaTable").show(),$("#pagLega").hide(),$("#pagFech").show()):($("#Visualizar").prop("checked",!1),$("#GetGeneralTable").removeClass("d-none"),$("#GetGeneralFechaTable").hide(),$("#pagFech").hide(),$("#pagLega").show()),ActualizaTablas()})),$(".MaskLega").mask("000000000000",{selectOnFocus:!0});