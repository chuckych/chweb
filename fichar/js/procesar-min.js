$((function(){"use strict";onOpenSelect2(),ActiveBTN(!1,"#submit","","Ingresar Fichadas"),$(".FicharHorario").bind("submit",(function(s){s.preventDefault(),CheckSesion();let t='<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>';$.ajax({type:$(this).attr("method"),url:$(this).attr("action"),data:$(this).serialize(),beforeSend:function(s){$.notifyClose(),notify("Ingresando Fichadas","info",0,"right"),ActiveBTN(!0,"#submit",'Aguarde <span class = "dotting mr-1"> </span> '+t,"Ingresar Fichadas")},success:function(s){"ok"==s.status?($.notifyClose(),notify(s.Mensaje,"success",2e3,"right"),ActiveBTN(!1,"#submit",'Aguarde <span class = "dotting mr-1"> </span> '+t,"Ingresar Fichadas")):($.notifyClose(),notify(s.Mensaje,"danger",2e3,"right"),ActiveBTN(!1,"#submit",'Aguarde <span class = "dotting mr-1"> </span> '+t,"Ingresar Fichadas"))}})}))}));