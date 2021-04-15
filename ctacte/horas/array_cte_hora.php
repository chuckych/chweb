<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL);
ini_set('display_errors', '0');
$url   = host() . "/" . HOMEHOST . "/data/GetCtaCteHoras.php?tk=" . token() . "&_c=" . $_SESSION["RECID_CLIENTE"] . "&_r=" . $_SESSION["RECID_ROL"] . "&" . $_SERVER['QUERY_STRING'];
// echo ($url);exit;
// $json  = file_get_contents($url);
// $array = json_decode($json, true);
$array = json_decode(getRemoteFile($url), true);
if ($array[0]['cta_horas']) {
    foreach ($array[0]['cta_horas'] as $key => $value) {
        $ctacte = ($value['ctacte']);

        if($ctacte>0){
            $ctacte = "<p class='fw5 m-0' style='color: #00c853' >".FormatHora($ctacte)."</p>"; /** positivo */
        }
        else{
            $ctacte  = str_replace("-", "", $ctacte);
            $ctacte = "<p class='fw5 m-0' style='color: #d32f2f'>-".FormatHora($ctacte)."</p>"; /** negativo */
        }

        $arr[] = array(
            "Legajo"  => $value['Legajo'],
            "Nombre"  => $value['Nombre'],
            "HorasEx" => ($value['HorasEx']),
            "Franco"  => ($value['FrancoCompe1']),
            "JorRedu" => ($value['JornadaReducida1']),
            "ctacte"  => ($ctacte),
            "haber"   => ($haber),
            "null"    => null,
        );

    }
    $arr = (array("cta_horas" => $arr));
    echo json_encode($arr);
} else {
    $arr[] = array(
        "Legajo"  => null,
        "Nombre"  => null,
        "HorasEx" => null,
        "Franco"  => null,
        "JorRedu" => null,
        "ctacte"  => null,
        "haber"   => null,
        "null"    => null
    );
    $arr = (array("cta_horas" => $arr));
    echo json_encode($array);
}
