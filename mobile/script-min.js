$(document).ready((function(){function a(){var a=parseFloat($("#latitud").val()),e=parseFloat($("#longitud").val()),t=(t=$("#zona").val())||"Fuera de Zona",n=parseFloat($("#map_size").val());const o={lat:a,lng:e},s=new google.maps.Map(document.getElementById("mapzone"),{zoom:15,center:o,mapTypeId:google.maps.MapTypeId.TERRAIN,zoomControl:!1,mapTypeControl:!1,scaleControl:!1,streetViewControl:!1,rotateControl:!1,fullscreenControl:!0}),l='<div id="content"><span>'+t+"</span></div>",r=new google.maps.InfoWindow({content:l}),i=new google.maps.Marker({position:o,map:s,icon:"../img/marker.png",title:t});i.addListener("click",(()=>{r.open(s,i)}));var c={strokeColor:"#0388D1",strokeOpacity:1,strokeWeight:1,fillColor:"#0388D1",fillOpacity:.25,map:s,center:o,radius:n};cityCircle=new google.maps.Circle(c),cityCircle.bindTo("center",i,"position")}function e(){$("#btnSubmitZone").prop("disabled",!1),$("#btnSubmitZone").html("Aceptar"),$("input[name=nombre]").val(""),$(".select2").val("200").trigger("change"),$("#rowRespuesta").addClass("d-none"),$("#rowCreaZona").addClass("d-none"),$("#map_size").val("5"),$("#btnCrearZona").removeClass("d-none")}$("#Refresh").on("click",(function(){$("#table-mobile").DataTable().ajax.reload(),$(".dataTables_scrollBody").addClass("opa2")})),$("#btnFiltrar").removeClass("d-sm-block"),$("#table-mobile").DataTable({drawCallback:function(a){var e=jQuery.makeArray(a.json);$.each(e,(function(a,e){$(".appcode").html("<b>"+e.AppCode+"</b>"),$(".cuenta").html("<b>"+e.Cuenta+"</b>"),"YES"==e.success?($("#alertmessage").fadeOut("slow"),setTimeout((function(){$("#alertmessage").remove()}),1e3)):$("#RowTableMobile").append('<div class="col-12 mt-2"><div class="alert alert-danger" id="alertmessage">'+e.message+"</div></div>")})),$(".dataTables_scrollBody").removeClass("opa2"),$(".form-control-sm").attr("placeholder","Buscar")},columnDefs:[{visible:!0,targets:0,orderable:!1},{visible:!0,targets:6,orderable:!1},{visible:!0,targets:7,orderable:!1}],iDisplayLength:-1,bProcessing:!0,ajax:{url:"?p=array_mobile.php",type:"POST",dataSrc:"mobile",data:function(a){a._drMob=$("#_drMob").val()}},createdRow:function(a,e,t){$(a).addClass("animate__animated animate__fadeIn align-middle")},columns:[{data:"face_url"},{data:"uid"},{class:"",data:"name"},{class:"",data:"Fecha2"},{class:"",data:"Fecha"},{class:"ls1 fw5",data:"time"},{class:"text-center",data:"mapa"},{class:"text-center",data:"similarity"},{data:"zone"},{data:"IN_OUT"},{data:"t_type"}],deferRender:!0,paging:!1,searching:!0,scrollY:"50vh",scrollX:!0,scrollCollapse:!0,info:!0,ordering:!1,language:{url:"../js/DataTableSpanishShort2.json"}}),$("#_drMob").on("change",(function(){$("#table-mobile").DataTable().ajax.reload(null,!1),$(".dataTables_scrollBody").addClass("opa2")})),$(".select2").select2({minimumResultsForSearch:-1,placeholder:"Seleccionar"}),$(document).on("click",".pic",(function(t){$("#pic").modal("show");var n=$(this).attr("datafoto"),o=$(this).attr("dataname"),s=$(this).attr("datauid"),l=$(this).attr("datacerteza"),r=$(this).attr("datacerteza2"),i=$(this).attr("datainout"),c=$(this).attr("datazone"),d=$(this).attr("datahora"),u=($(this).attr("datagps"),$(this).attr("datatype")),m=$(this).attr("datadia"),p=$(this).attr("datalat"),b=$(this).attr("datalng");$("#latitud").val(p),$("#longitud").val(b),$("input[name=lat]").val(p),$("input[name=lng]").val(b),$("#zona").val(c),n?$(".picFoto").html('<img loading="lazy" src="https://server.xenio.uy/'+n+'" class="w150 img-fluid rounded">'):$(".picFoto").html('<img loading="lazy" src="../img/user.png" class="img-fluid rounded" alt="Sin Foto" title="Sin Foto">'),$(".picName").html(o),$(".picUid").html(s),$(".picHora").html("<b>"+d+"</b>"),$(".picModo").html(i),$(".picTipo").html(u),$(".picDia").html(m);var h=parseFloat(p)+parseFloat(b);if(l>70?$(".picCerteza").html('<img src="../img/check.png" class="w15" alt="'+l+'" title="'+l+'">&nbsp;<span class="fontp fw4 text-success">('+r+")</span>"):$(".picCerteza").html('<img src="../img/uncheck.png" class="w15" alt="'+l+'" title="'+l+'">&nbsp;<span class="fontp fw4 text-danger">('+r+")</span>"),"0"!=h){c?$("#btnCrearZona").addClass("d-none"):$("#btnCrearZona").removeClass("d-none");var g=c?'<span class="text-success">'+c+"</span>":'<span class="text-danger">Fuera de Zona</span>';$(".picZona").html(g)}else $(".picZona").html("Sin ubicaci&oacute;n");"0"!=h?($("#mapzone").removeClass("d-none"),a()):($("#mapzone").addClass("d-none"),$("#btnCrearZona").addClass("d-none")),$(document).on("click","#btnCrearZona",(function(e){fadeInOnly("#rowCreaZona"),$("#rowRespuesta").addClass("d-none"),$("#map_size").val("200"),a(),fadeInOnly("#mapzone"),$(".select2").on("select2:select",(function(e){var t=$(e.currentTarget).val();$("#map_size").val(t),a(),fadeInOnly("#mapzone")})),$("#rowCreaZona").removeClass("d-none"),$("#CrearZona").bind("submit",(function(e){e.preventDefault(),$.ajax({type:$(this).attr("method"),url:$(this).attr("action"),data:$(this).serialize(),beforeSend:function(a){$("#btnSubmitZone").prop("disabled",!0),$("#btnSubmitZone").html("Creando Zona.!")},success:function(e){"ok"==e.status?($("#btnCrearZona").addClass("d-none"),$("#btnSubmitZone").prop("disabled",!1),$("#btnSubmitZone").html("Aceptar"),$("#rowRespuesta").removeClass("d-none"),$("#respuesta").html('<div class="alert alert-success fontq"><b>¡Zona creada correctamente!<br>La misma se ver&aacute; reflejada en futuras marcaciones.</b></div>'),$("#rowCreaZona").addClass("d-none"),setTimeout((function(){$("#rowRespuesta").addClass("d-none")}),4e3),$("#map_size").val(e.radio),a()):($("#btnSubmitZone").prop("disabled",!1),$("#btnSubmitZone").html("Aceptar"))},error:function(){$("#btnSubmitZone").prop("disabled",!1),$("#btnSubmitZone").html("Aceptar")}})}))})),$(document).on("click","#cancelZone",(function(t){e(),a()}))})),$("#pic").on("hidden.bs.modal",(function(a){e()})),$(".selectjs_cuentaToken").select2({multiple:!1,language:"es",placeholder:"Cambiar de Cuenta",minimumInputLength:"0",minimumResultsForSearch:-1,maximumInputLength:"10",selectOnClose:!1,language:{noResults:function(){return"No hay resultados.."},inputTooLong:function(a){var e="Máximo 10 caracteres. Elimine "+overChars+" caracter";return 1!=overChars&&(e+="es"),e},searching:function(){return"Buscando.."},errorLoading:function(){return"Sin datos.."},inputTooShort:function(){return"Ingresar 0 o mas caracteres"},maximumSelected:function(){return"Puede seleccionar solo una opción"}},ajax:{url:"GetTokenCuenta.php",dataType:"json",type:"POST",data:function(a){return{}},processResults:function(a){return{results:a}}}}),$(".selectjs_cuentaToken").on("select2:select",(function(a){$("#RefreshToken").submit()})),$("#RefreshToken").bind("submit",(function(a){a.preventDefault(),$.ajax({type:$(this).attr("method"),url:$(this).attr("action"),data:$(this).serialize(),beforeSend:function(a){},success:function(a){"ok"==a.status&&($("#table-mobile").DataTable().ajax.reload(),$(".dataTables_scrollBody").addClass("opa2"))},error:function(){}})}))}));