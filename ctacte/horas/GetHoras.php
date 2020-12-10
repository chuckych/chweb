<?php
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");

error_reporting(E_ALL);
ini_set('display_errors', '0');

session_start();

require __DIR__ . '../../../config/index.php';
require __DIR__ . '../../../config/conect_mssql.php';

$param   = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$Datos = explode(',', $_POST['DRFech']);

$FechaIni  = $Datos[0];
$FechaFin  = $Datos[1];

$Legajo = test_input($_POST['FicLega']);

$data = array();

 /** HORAS */
//$query="SELECT FICHAS1.FicHora AS Hora, TIPOHORA.THoDesc AS HoraDesc, TIPOHORA.THoDesc2 AS HoraDesc2, FICHAS1.FicHsHe AS FicHsHe, FICHAS1.FicHsAu AS FicHsAu, FICHAS1.FicHsAu2 AS FicHsAu2, FICHAS1.FicObse AS Observ, TIPOHORACAUSA.THoCCodi AS Motivo, TIPOHORACAUSA.THoCDesc AS DescMotivo, FICHAS1.FicFech AS FicFech, FICHAS1.FicEsta AS Estado FROM FICHAS1, TIPOHORA,TIPOHORACAUSA WHERE FICHAS1.FicLega='$Legajo' AND FICHAS1.FicFech='$Fecha' AND FICHAS1.FicHora=TIPOHORA.THoCodi AND FICHAS1.FicHora=TIPOHORACAUSA.THoCHora AND FICHAS1.FicCaus=TIPOHORACAUSA.THoCCodi AND TIPOHORA.THoColu >0 ORDER BY TIPOHORA.THoColu, FICHAS1.FicLega,FICHAS1.FicFech,FICHAS1.FicTurn, FICHAS1.FicHora";
$query="SELECT FICHAS1.FicFech AS 'Fecha',  FICHAS1.FicHora AS 'Hora',
    dbo.fn_DiaDeLaSemana(FICHAS1.FicFech) AS 'Dia',
    TIPOHORA.THoDesc AS 'HoraDesc',
    TIPOHORA.THoDesc2 AS 'HoraDesc2',
    FICHAS1.FicHsHe AS 'FicHsHe',
    FICHAS1.FicHsAu AS 'FicHsAu',
    FICHAS1.FicHsAu2 AS 'FicHsAu2',
    FICHAS1.FicObse AS 'Observ',
    TIPOHORACAUSA.THoCCodi AS 'Motivo',
    TIPOHORACAUSA.THoCDesc AS 'DescMotivo',
    FICHAS1.FicFech AS 'FicFech',
    FICHAS1.FicEsta AS 'Estado',
    TIPOHORA.THoColu AS 'THoColu'
FROM FICHAS1
    INNER JOIN TIPOHORA ON FICHAS1.FicHora = TIPOHORA.THoCodi
    LEFT JOIN TIPOHORACAUSA ON FICHAS1.FicHora = TIPOHORACAUSA.THoCHora
    AND FICHAS1.FicCaus = TIPOHORACAUSA.THoCCodi
WHERE FICHAS1.FicLega = '$Legajo'
    AND TIPOHORA.THoCtaH = 1
    AND FICHAS1.FicFech BETWEEN '$FechaIni' AND '$FechaFin'
ORDER BY FICHAS1.FicFech, TIPOHORA.THoColu,
    FICHAS1.FicHora";

// print_r($query);exit;

$result = sqlsrv_query($link, $query, $param, $options);

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :
        
        $HoraFechStr= $row['FicFech']->format('Ymd');

        $HorasEx = (HoraMin($row['FicHsAu']) - HoraMin($row['FicHsAu2']));
        // $HorasEx = MinHora($HorasEx);

        if ($HorasEx>0) {
            $data[] = array(
                'Fecha'        => $row['Fecha']->format('d/m/Y'),
                'Dia'          => $row['Dia'],
                'Cod'          => $row['Hora'],
                'Descripcion'  => $row['HoraDesc'],
                'Descripcion2' => $row['HoraDesc2'],
                'HorasEx'      => MinHora($HorasEx),
                'FicHsHe'      => $row['FicHsHe'],
                'FicHsAu'      => $row['FicHsAu'],
                'FicHsAu2'     => $row['FicHsAu2'],
                'Observ'       => ceronull($row['Observ']),
                'Motivo'       => ceronull($row['Motivo']),
                'DescMotivo'   => ceronull($row['DescMotivo']),
                'null'         => ''
            );
        }
        endwhile;
        sqlsrv_free_stmt($result);
}
/** Fin HORAS */
echo json_encode(array('Horas'=> $data));
sqlsrv_close($link);
exit;



