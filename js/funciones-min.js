function formatDate(e){var t=new Date(e),n=""+(t.getMonth()+1),a=""+t.getDate(),o=t.getFullYear();return n.length<2&&(n="0"+n),a.length<2&&(a="0"+a),[o,n,a].join("-")}function NombreMesJS(e){switch(e){case"01":var t="Enero";break;case"02":t="Febrero";break;case"03":t="Marzo";break;case"04":t="Abril";break;case"05":t="Mayo";break;case"06":t="Junio";break;case"07":t="Julio";break;case"08":t="Agosto";break;case"09":t="Septiembre";break;case"10":t="Octubre";break;case"11":t="Noviembre";break;case"12":t="Diciembre";break}return t}function SumarMes(e,t){return moment(e).add(t,"months").format("YYYY-MM-DD")}function fadeInOnChange(e,t){$(e).change((function(e){$(t).addClass("animate__animated animate__fadeIn"),setTimeout((function(){$(t).removeClass("animate__animated animate__fadeIn")}),100)}))}function fadeInOnClick(e,t){$(e).on("click",(function(e){$(t).addClass("animate__animated animate__fadeIn"),setTimeout((function(){$(t).removeClass("animate__animated animate__fadeIn")}),100)}))}function fadeInOnly(e){$(e).addClass("animate__animated animate__fadeIn"),setTimeout((function(){$(e).removeClass("animate__animated animate__fadeIn")}),1e3)}function classHover(e,t){$(e).hover((function(){$(this).addClass(t)}),(function(){$(this).removeClass(t)}))}function classEfect(e,t){$(e).addClass(t),setTimeout((function(){$(e).removeClass(t)}),1e3)}function switchClass(e,t,n){$(e).addClass(t),$(e).removeClass(n)}function invisibleIO(e){setTimeout((function(){$(e).removeClass("invisible")}),1e3)}function RadioCheckActive(e){$("selector").is(":checked")?$("selector").addClass("opa9"):$("selector").addClass("opa6")}function Modal_XL_LG(e){$(e).removeClass("modal-xl"),$(e).addClass("modal-lg")}function Modal_LG_XL(e){$(e).removeClass("modal-lg"),$(e).addClass("modal-xl")}function pad(e,t,n){return n=n||"0",(e+="").length>=t?e:new Array(t-e.length+1).join(n)+e}function CheckedInput(e){$(e).is(":not(:checked)")&&$(e).prop("checked",!0)}function UnCheckedInput(e){$(e).change((function(){})),$(e).is(":checked")&&$(e).prop("checked",!1)}function CheckedInputVal(e,t,n,a,o){$(e).is(":not(:checked)")?(""!=n&&$(e).val(n),$(e).attr("name",o)):(""!=t&&$(e).val(t),$(e).attr("name",a))}function InputVal(e,t){$(e).val(t)}function CheckedInputValChange(e,t,n,a,o){$(e).change((function(){$(e).is(":not(:checked)")?(""!=n&&$(e).val(n),$(e).attr("name",o)):(""!=t&&$(e).val(t),$(e).attr("name",a))}))}function DisabledInput(e){$(e).is(":not(:disabled)")&&$(e).prop("disabled",!0)}function UnDisabledInput(e){$(e).is(":disabled")&&$(e).prop("disabled",!1)}function ActiveBTN(e,t,n,a){1==e?($(t).prop("disabled",e),$(t).html(n)):($(t).prop("disabled",e),$(t).html(a))}function ShowLoading(e){var t=document.createElement("div"),n=document.createElement("img");return t.innerHTML='<style>.container{opacity:0.5 !important}</style><div id="ShowLoading" class="pl-2 animate__animated animate__fadeIn fixed-top border border-secondary mx-auto d-flex align-items-center text-white font-weight-bold text-center bg-custom" style="top:30%;width:220px;text-align:center;z-index:1050;height:50px;font-size:1.1em;border-radius:4px; opacity:0.5 !important"><small>&nbsp;&nbsp;&nbsp;&nbsp;Aguarde por favor... <div class="spinner-border spinner-border-sm text-white mx-auto" role="status" aria-hidden="true"></div></small></div>',t.style.cssText="",t.appendChild(n),document.body.appendChild(t),!0}function goBack(){window.history.back()}$("li").on("shown.bs.dropdown",(function(){$(this).addClass("bg-light shadow-sm radius"),$(this).children(".dropdown-menu").addClass("animate__animated animate__fadeIn mt-1")})),$("li").on("hidden.bs.dropdown",(function(){$(this).removeClass("bg-light shadow-sm radius"),$(this).children(".dropdown-menu").removeClass("animate__animated animate__fadeIn mt-1")})),$(".trash").attr("data-icon",""),$(".edit").attr("data-icon",""),$('[data-toggle="tooltip"]').tooltip(),$(window).on("load",(function(){$(".loader").fadeOut("slow")}));var _homehost=$("#_homehost").val(),getUrlParameter=function(e){var t,n,a=window.location.search.substring(1).split("&");for(n=0;n<a.length;n++)if((t=a[n].split("="))[0]===e)return void 0===t[1]||decodeURIComponent(t[1])};function TrimEspacios(e){return e.replace(/ /g,"")}function HoraMask(e){var t=function(e){return e=e.split(":"),parseInt(e[0])>19?"HZ:M0":"H0:M0"};return spOptions={onKeyPress:function(e,n,a,o){a.mask(t.apply({},arguments),o)},translation:{H:{pattern:/[0-2]/,optional:!1},Z:{pattern:/[0-3]/,optional:!1},M:{pattern:/[0-5]/,optional:!1}}},$(e).mask(t,spOptions)}function SelectSelect2Ajax(e,t,n,a,o,s,i,r,l,c,u,d){$(e).select2({multiple:t,language:"es",allowClear:n,placeholder:a,minimumInputLength:o,minimumResultsForSearch:s,maximumInputLength:i,selectOnClose:r,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var t="Máximo "+opt2.MaxInpLength+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(t+="es"),t},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar "+opt2.MinLength+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"},removeAllItems:function(){return"Eliminar Selección"}},ajax:{url:"/"+$("#_homehost").val()+"/"+l,dataType:"json",type:d,delay:c,data:function(e){return{q:e.term,data_array:u}},processResults:function(e){return{results:e}}}}).on("select2:unselecting",(function(e){$(this).data("state","unselected")})).on("select2:open",(function(e){if("unselected"===$(this).data("state")){$(this).removeData("state");var t=$(this);setTimeout((function(){t.select2("close")}),1)}}))}function CloseDropdownOnClearSelect2(e){$(e).on("select2:unselecting",(function(e){$(this).data("state","unselected")})).on("select2:open",(function(e){if("unselected"===$(this).data("state")){$(this).removeData("state");var t=$(this);setTimeout((function(){t.select2("close")}),1)}}))}function SelectSelect2(e,t,n,a,o,s,i){$(e).select2({language:"es",allowClear:t,placeholder:n,minimumInputLength:a,minimumResultsForSearch:o,maximumInputLength:s,selectOnClose:i,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(e){var t="Máximo "+opt2.MaxInpLength+" caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(t+="es"),t},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar "+opt2.MinLength+" o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"},removeAllItems:function(){return"Eliminar Selección"}}}).on("select2:unselecting",(function(e){$(this).data("state","unselected")})).on("select2:open",(function(e){if("unselected"===$(this).data("state")){$(this).removeData("state");var t=$(this);setTimeout((function(){t.select2("close")}),1)}}))}function vjs(){return $("#_vjs").val()}function respuesta_form(e,t,n){let a=$(e).html('<div class="mt-3 animate__animated animate__fadeInDown alert alert-'+n+' alert-dismissible fontq p-3 fw5" role="alert">'+t+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');return setTimeout((function(){$(".alert-"+n).removeClass("fadeInDown"),$(".alert-"+n).addClass("fadeOutUp"),setTimeout((function(){$(e).html("")}),3500)}),1e3),a}function notify(e,t,n,a){var o=$(window).width()<769?0:20;$.notify({message:e},{type:t,z_index:9999,delay:n,offset:o,mouse_over:"pause",placement:{align:a},animate:{enter:"animate__animated animate__fadeInDown",exit:"animate__animated animate__fadeOutUp"}})}function focusEndText(e){let t=$(e),n=t.val().length;t.focus(),t[0].setSelectionRange(n,n)}function select2Ajax(e,t,n,a,o){$(e).select2({placeholder:t,allowClear:n,selectOnClose:a,minimumResultsForSearch:10,language:{noResults:function(){return"No hay resultados.."},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."}},ajax:{url:o,dataType:"json",type:"GET",data:function(e){return{q:e.term}},processResults:function(e){return{results:e}}}})}function select2Simple(e,t,n,a){$(e).select2({placeholder:t,minimumResultsForSearch:10,allowClear:n,selectOnClose:a})}function Select2Value(e,t,n){var a=new Option(t,e,!1,!1);""!=t&&$(n).append(a).trigger("change")}function CheckUncheck(e,t,n,a){$(e).click((function(e){$(n).prop("checked",!0),$(n).parents("tr").addClass("table-active")})),$(t).click((function(e){$(n).prop("checked",!1),$(n).parents("tr").removeClass("table-active")}))}function singleDatePicker(e,t,n){$(e).daterangepicker({singleDatePicker:!0,opens:t,drops:n,autoUpdateInput:!0,buttonClasses:"btn btn-sm fontq",applyButtonClasses:"btn-custom fw4 px-3 opa8",cancelClass:"btn-link fw4 text-gris",ranges:{Hoy:[moment(),moment()],Ayer:[moment().subtract(1,"days"),moment().subtract(1,"days")]},locale:{format:"DD/MM/YYYY",separator:" al ",applyLabel:"Aplicar",cancelLabel:"Cancelar",fromLabel:"Desde",toLabel:"Para",customRangeLabel:"Personalizado",weekLabel:"Sem",daysOfWeek:["Do","Lu","Ma","Mi","Ju","Vi","Sa"],monthNames:["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],firstDay:1,alwaysShowCalendars:!0,applyButtonClasses:"text-white bg-custom"}})}function singleDatePickerValue(e,t,n,a){$(e).daterangepicker({singleDatePicker:!0,opens:t,drops:n,startDate:a,endDate:a,autoApply:!1,buttonClasses:"btn btn-sm fontq",applyButtonClasses:"btn-custom fw4 px-3 opa8",ranges:{Hoy:[moment(),moment()],Ayer:[moment().subtract(1,"days"),moment().subtract(1,"days")]},locale:{format:"DD/MM/YYYY",customRangeLabel:"Personalizado",weekLabel:"Sem",daysOfWeek:["Do","Lu","Ma","Mi","Ju","Vi","Sa"],monthNames:["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],firstDay:1}})}function CheckSesion(){$.ajax({dataType:"json",url:"../sesion.php",context:document.body}).done((function(e){"sesion"==e.status?($("#_sesion").val("1"),window.location.href="/"+$("#_homehost").val()+"/login/"):$("#_sesion").val("0")}))}$(".requerido").html("(*)");