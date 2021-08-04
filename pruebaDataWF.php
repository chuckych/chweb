<?php
header( 'Content-type: application/json' );
function getRemoteFile($url, $timeout = 10, $username, $password)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    // curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
    $headers = array(
        'Content-Type:application/json',
        'Authorization: Basic ' . base64_encode($username . ":" . $password)
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return ($file_contents) ? $file_contents : false;
    exit;
}
function fechFormat($fecha)
{
    $fecha = date_create($fecha);
    $fecha = date_format($fecha, 'd/m/Y');
    return $fecha;
}
$url   = "52.38.68.90/hrc/api/novedades/?status=P,E";
$array = json_decode(getRemoteFile($url, 0, 'admin', 'admin'));
if (is_array($array)) {
    foreach (($array) as $key => $value) {
        $data[] = array(
            'legajo'      => $value->legajo,
            'novedad'     => $value->novedad,
            'fecha_desde' => fechFormat($value->fecha_desde),
            'fecha_hasta' => fechFormat($value->fecha_hasta),
            'horas'       => $value->horas,
            'status'      => ($value->status == 'P') ? 'Pendiente' : 'Exportado'
        );
    }
}else {
    $data=array();
}
echo json_encode(array('data'=>$data));exit;