var IconExcel = '.xls <img src="../img/xls.png" class="w15" alt="Exportar Excel">'
ActiveBTN(false, "#btnExcel", 'Exportando', IconExcel)

function GetGeneral() {
$.ajax({
   type: 'POST',
   dataType: "json",
   url: "/" + $("#_homehost").val() + "/general/GetGeneralFecha.php",
   'data': {
        _f      : $("#_f").val(),
        Per     : $("#Per").val(),
        Tipo    : $("#Tipo").val(),
        Emp     : $("#Emp").val(),
        Plan    : $("#Plan").val(),
        Sect    : $("#Sect").val(),
        Sec2    : $("#Sec2").val(),
        Grup    : $("#Grup").val(),
        Sucur   : $("#Sucur").val(),
        _dr     : $("#_dr").val(),
        _l      : $("#_l").val(),
        // FicDiaL : $("#datoFicDiaL").val(),
        FicFalta: $("#datoFicFalta").val(),
        FicNovT : $("#datoFicNovT").val(),
        FicNovI : $("#datoFicNovI").val(),
        FicNovS : $("#datoFicNovS").val(),
        FicNovA : $("#datoFicNovA").val(),
        Excel : true,
   },
   beforeSend:function(){
   },
   success: function (respuesta) {
       if (respuesta.status == "ok") {
           
       }else{
       }
   },
   error: function () {
   }
});
}
$(document).on("click", "#btnExcel", function (e) {
CheckSesion()
GetGeneral()
});