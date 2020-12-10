<?php

?>
<script>

    function GetGeneral() {
       $.ajax({
           type: 'POST',
           dataType: "json",
           url: "GetGeneralFecha.php",
           'data': {
                data._f       = $("#_f").val();
                data.Per      = $("#Per").val();
                data.Tipo     = $("#Tipo").val();
                data.Emp      = $("#Emp").val();
                data.Plan     = $("#Plan").val();
                data.Sect     = $("#Sect").val();
                data.Sec2     = $("#Sec2").val();
                data.Grup     = $("#Grup").val();
                data.Sucur    = $("#Sucur").val();
                data._dr      = $("#_dr").val();
                data._l       = $("#_l").val();
                data.FicDiaL  = $("#datoFicDiaL").val();
                data.FicFalta = $("#datoFicFalta").val();
                data.FicNovT  = $("#datoFicNovT").val();
                data.FicNovI  = $("#datoFicNovI").val();
                data.FicNovS  = $("#datoFicNovS").val();
                data.FicNovA  = $("#datoFicNovA").val();
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
        GetGeneral()
    });
   
</script>
