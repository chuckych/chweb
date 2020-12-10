<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '0');

require __DIR__ . '../../config/index.php';
require __DIR__ . '../../config/conect_mssql.php';

$param  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$query = "SELECT 
FICHAS.FicLega AS Lega, 
.PERSONAL.LegApNo AS Nombre, 
.FICHAS.FicFech AS Fecha, 
DATEPART(dw, .FICHAS.FicFech) AS Dia_Sem, 
Horario = CASE FICHAS.FicDiaL WHEN 0 THEN CASE FICHAS.FicDiaF WHEN 1 THEN 'Feriado' ELSE 'Franco' END ELSE (FICHAS.FicHorE + ' a ' + FICHAS.FicHorS) END, 
FICHAS1.FicHora AS Hora, 
(TIPOHORA.THoDesc) AS HoraDesc, (TIPOHORA.THoDesc2) AS HoraDesc2,
FICHAS1.FicHsHe AS HsHechas, 
FICHAS1.FicHsAu AS HsCalculadas,
FICHAS1.FicHsAu2 AS HsAutorizadas,
FICHAS3.FicNove AS FicNove, 
NOVEDAD.NovDesc AS NovDesc, 
NOVEDAD.NovTipo AS NovTipo, 
(LEFT(FICHAS3.FicHoras,2)*60+RIGHT(FICHAS3.FicHoras,2)) AS Horas,
((LEFT(FICHAS3.FicHoras,2)*60+RIGHT(FICHAS3.FicHoras,2))*FICHAS3.FicJust) AS HorasJust,
	(ABS((LEFT(FICHAS3.FicHoras,2)*60+RIGHT(FICHAS3.FicHoras,2))*(FICHAS3.FicJust-1))) AS HorasNoJust,
(FICHAS3.FicNove) AS Dias,
	(FICHAS3.FicJust) AS DiasJust, 
(ABS((FICHAS3.FicJust-1))) AS DiasNoJust, 
TIPOHORA.THoColu AS THoColu, 
REGISTRO.RegFeAs AS 'Fic_Asignada',
REGISTRO.RegHoRe AS Fic_Hora,
CASE REGISTRO.RegTipo WHEN 0 THEN 'Capturador' ELSE 'Manual' END AS Fic_Tipo,
CASE REGISTRO.RegHora WHEN REGISTRO.RegHoRe THEN 'Normal' ELSE 'Modificada' END AS Fic_Estado
FROM FICHAS
JOIN PERSONAL ON FICHAS.FicLega = PERSONAL.LegNume
LEFT JOIN FICHAS1 ON FICHAS.FicLega = FICHAS1.FicLega AND FICHAS.FicFech = FICHAS1.FicFech
LEFT JOIN TIPOHORA ON FICHAS1.FicHora = TIPOHORA.THoCodi
LEFT JOIN FICHAS3 ON FICHAS.FicLega = FICHAS3.FicLega AND FICHAS3.FicFech = FICHAS.FicFech
LEFT JOIN NOVEDAD ON FICHAS3.FicNove = NOVEDAD.NovCodi
LEFT JOIN REGISTRO ON FICHAS.FicLega = REGISTRO.RegLega AND FICHAS.FicFech = REGISTRO.RegFeAs
WHERE PERSONAL.LegFeEg = '17530101' AND FICHAS.FicLega IN (29988600,29408391) AND .FICHAS.FicFech BETWEEN '20200528' AND '20200529'  
ORDER BY .FICHAS.FicFech, FICHAS.FicLega, TIPOHORA.THoColu";
// print_r($query); exit;

$queryRecords = sqlsrv_query($link, $query, $param, $options);

while ($row = sqlsrv_fetch_array($queryRecords)) :
    $Fecha = $row['Fecha']->format('Ymd');
    $row['Dia_Sem'] = nombre_dias($row['Dia_Sem'], false);
    $Fic_Asignada = ($row['Fic_Asignada']) ? $row['Fic_Asignada']->format('Ymd') : null;
    $data[] = array(
        'Lega'          => $row['Lega'],
        'Nombre'        => $row['Nombre'],
        'Fecha'         => $Fecha,
        'Dia_Sem'       => $row['Dia_Sem'],
        'Horario'       => $row['Horario'],
        'Hora'          => $row['Hora'],
        'HoraDesc'      => $row['HoraDesc'],
        'HoraDesc2'     => $row['HoraDesc2'],
        'HsHechas'      => $row['HsHechas'],
        'HsCalculadas'  => $row['HsCalculadas'],
        'HsAutorizadas' => $row['HsAutorizadas'],
        'FicNove'       => $row['FicNove'],
        'NovDesc'       => $row['NovDesc'],
        'NovTipo'       => $row['NovTipo'],
        'Horas'         => $row['Horas'],
        'HorasJust'     => $row['HorasJust'],
        'HorasNoJust'   => $row['HorasNoJust'],
        'Dias'          => $row['Dias'],
        'DiasJust'      => $row['DiasJust'],
        'DiasNoJust'    => $row['DiasNoJust'],
        'THoColu'       => $row['THoColu'],
        'Fic_Asignada'  => $Fic_Asignada,
        'Fic_Hora'      => $row['Fic_Hora'],
        'Fic_Tipo'      => $row['Fic_Tipo'],
        'Fic_Estado'    => $row['Fic_Estado'],
    );
    $dataLega[] = array(
        'Lega'          => $row['Lega'],
        'Nombre'        => $row['Nombre'],
    );
    $dataFecha[] = array(
        'Fecha' => $Fecha,
    );
    $dataFichadas[] = array(
        'Lega'         => $row['Lega'],
        'Nombre'       => $row['Nombre'],
        'Fecha'        => $Fecha,
        'Fic_Asignada' => $Fic_Asignada,
        'Fic_Hora'     => $row['Fic_Hora'],
        'Fic_Tipo'     => $row['Fic_Tipo'],
        'Fic_Estado'   => $row['Fic_Estado'],
    );
endwhile;

$Legajos = array();
$Legajos = (super_unique($dataLega, 'Lega'));





// foreach ($Legajos as $key => $value) {
//     $Lega = $value['Lega'];

//     $r = array_filter($data, function ($e) use ($Lega) {
//         return $e['Lega'] == $Lega;
//     });

//     foreach ($r as $key => $valueData) {
//     $Fecha = (super_unique($data, 'Fecha'));
//     $fech=$Fecha;
//     $r1 = array_filter($Fecha, function ($e1) use ($fech) {
//         return $e1['Fecha'] == $fech;
//     });
//     foreach ($r1 as $key => $valores) {
//         $Fichadas[]=array(
//             'Fic_Asignada' => $valores['Fic_Asignada'],
//             'Fic_Hora'      => $valores['Fic_Hora'],
//             'Fic_Tipo'      => $valores['Fic_Tipo'],
//             'Fic_Estado'    => $valores['Fic_Estado'],
//         );
//     }
//     $data1 = array(
//         'Legajo' => $Lega,
//         'Datos'  => $Fichadas,
//     );



//     }
//     $datos[] = $data1;
// }
// unset($date);

echo json_encode($Legajos);



