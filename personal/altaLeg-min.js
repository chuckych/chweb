$(document).on("shown.bs.modal","#altaNuevoLeg",(function(){CheckSesion(),$(this).find("[autofocus]").focus()})),$(document).ready((function(){$(".form-NuevoLeg").bind("submit",(function(e){e.preventDefault(),$.ajax({type:$(this).attr("method"),contetnType:"application_json; charset=utf-8",url:$(this).attr("action"),data:$(this).serialize(),beforeSend:function(e){$("#alerta_AltaLega").addClass("d-none"),$.notifyClose()},success:function(e){"ok"==e.status?($.notifyClose(),notify(e.Mensaje,"success",5e3,"right"),$("#alerta_AltaLega").removeClass("d-none").removeClass("d-none").removeClass("text-danger").addClass("text-success"),$(".respuesta_AltaLega").html("Legajo Creado!"),$(".mensaje_AltaLega").html(""),window.location.href=`legajo/?_leg=${e.Legajo}`):($.notifyClose(),notify(e.Mensaje,"danger",5e3,"right"),$("#alerta_AltaLega").removeClass("d-none").removeClass("text-success").addClass("text-danger"),$(".respuesta_AltaLega").html("¡Error!"),$(".mensaje_AltaLega").html(`${e.Mensaje}`))}})}))}));