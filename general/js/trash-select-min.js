function TrashSelect(e){$(e).val(null).trigger("change"),ActualizaTablas()}function LimpiarFiltros(){$(".selectjs_plantas").val(null).trigger("change"),$("#datoNovedad").val(null).trigger("change"),$(".selectjs_empresa").val(null).trigger("change"),$(".selectjs_sectores").val(null).trigger("change"),$(".select_seccion").val(null).trigger("change"),$(".select_seccion").prop("disabled",!0),$(".selectjs_grupos").val(null).trigger("change"),$(".selectjs_sucursal").val(null).trigger("change"),$(".selectjs_personal").val(null).trigger("change"),$(".selectjs_tipoper").val(null).trigger("change"),$("#Per2").val(null),$("#FicDiaL").prop("checked",!1),$("#datoFicFalta").val("0"),$("#FicFalta").prop("checked",!1),$("#datoFicNovT").val("0"),$("#FicNovT").prop("checked",!1),$("#datoFicNovI").val("0"),$("#FicNovI").prop("checked",!1),$("#datoFicNovS").val("0"),$("#FicNovS").prop("checked",!1),$("#datoFicNovA").val("0"),$("#LegDe").val(""),$("#LegHa").val(""),_Filtros(),$("#FicNovA").prop("checked",!1),$("#FicNovA").prop("disabled",!1),$("#FicFalta").prop("disabled",!1),$("#FicNovT").prop("disabled",!1),$("#FicNovI").prop("disabled",!1),$("#FicNovS").prop("disabled",!1)}function LimpiarFiltros2(){$("#datoNovedad").val(null).trigger("change"),$(".selectjs_plantas").val(null).trigger("change"),$(".selectjs_empresa").val(null).trigger("change"),$(".selectjs_sectores").val(null).trigger("change"),$(".select_seccion").val(null).trigger("change"),$(".select_seccion").prop("disabled",!0),$(".selectjs_grupos").val(null).trigger("change"),$(".selectjs_sucursal").val(null).trigger("change"),$(".selectjs_personal").val(null).trigger("change"),$(".selectjs_tipoper").val(null).trigger("change"),$("#FicDiaL").prop("checked",!1),$("#datoFicFalta").val("0"),$("#FicFalta").prop("checked",!1),$("#datoFicNovT").val("0"),$("#FicNovT").prop("checked",!1),$("#datoFicNovI").val("0"),$("#FicNovI").prop("checked",!1),$("#datoFicNovS").val("0"),$("#FicNovS").prop("checked",!1),$("#datoFicNovA").val("0"),$("#LegDe").val(""),$("#LegHa").val(""),_Filtros(),$("#FicNovA").prop("checked",!1),$("#FicNovA").prop("disabled",!1),$("#FicFalta").prop("disabled",!1),$("#FicNovT").prop("disabled",!1),$("#FicNovI").prop("disabled",!1),$("#FicNovS").prop("disabled",!1)}function refreshOnChange(e){$(e).change((function(){$("#Filtros").modal("show"),LimpiarFiltros2(),$("#Filtros").modal("hide"),ActualizaTablas2(),GetPersonal.on("xhr",(function(){GetPersonal.ajax.json().recordsTotal?$("#GetGeneralTable").removeClass("d-none"):$("#GetGeneralTable").addClass("d-none")}))}))}$("#trash_all").on("click",(function(){$("#Filtros").modal("show"),LimpiarFiltros(),$("#Filtros").modal("hide"),ActualizaTablas2()})),$("#trashDeHa").on("click",(function(){$("#LegDe").val(""),$("#LegHa").val(""),$(".selectjs_personal").val(null).trigger("change"),ActualizaTablas2()})),$("#trash_allIn").on("click",(function(){LimpiarFiltros(),ActualizaTablas()})),$(document).on("click",".numlega",(function(e){$("#Per2").val(null),$(".pers_legajo").addClass("d-none"),$("#Per2").removeClass("d-none"),$("#Per2").focus()})),refreshOnChange("#Per2");