<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
// header("Content-Type: application/json");
E_ALL();

require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';
$params = $_REQUEST;
$data = array();
if (isset($_POST['_l']) && !empty($_POST['_l'])) {
    $legajo = test_input(FusNuloPOST('_l', 'vacio'));
}else{
    $json_data = array(
        "draw"            => intval($params['draw']),
        "recordsTotal"    => 0,
        "recordsFiltered" => 0,
        "data"            => $data
    );
    echo json_encode($json_data);
    exit;
}

require __DIR__ . '../valores.php';

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$params = $columns = $totalRecords ='';
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

$sql_query="SELECT DISTINCT FICHAS.FicLega AS 'Fic_Lega', PERSONAL.LegApNo AS 'Fic_Nombre', FICHAS.FicFech AS 'Fic_Asignada', dbo.fn_DiaDeLaSemana(FICHAS.FicFech) AS 'Fic_Dia_Semana', dbo.fn_HorarioAsignado( FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicDiaL, FICHAS.FicDiaF ) AS 'Fic_horario'
FROM FICHAS INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume INNER JOIN REGISTRO ON FICHAS.FicLega=REGISTRO.RegLega AND FICHAS.FicFech=REGISTRO.RegFeAs
WHERE FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND FICHAS.FicLega='$legajo' $FilterEstruct $filtros";

// print_r($sql_query).PHP_EOL; exit;

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (!empty($params['search']['value'])) {
    $where_condition .=    " AND ";
    $where_condition .= " (CONCAT(PERSONAL.LegNume,PERSONAL.LegApNo) LIKE '%" . $params['search']['value'] . "%') ";
}

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}

$sqlRec .=  " ORDER BY FICHAS.FicFech, FICHAS.FicLega OFFSET " . $params['start'] . " ROWS FETCH NEXT " . $params['length'] . " ROWS ONLY";
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);
$queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);

// print_r($sqlRec).PHP_EOL; exit;

while ($row = sqlsrv_fetch_array($queryRecords)) :
    $Fic_Lega       = $row['Fic_Lega'];
    $Fic_Asignada   = $row['Fic_Asignada']->format('d-m-Y');
    $Fic_Asignada2  = $row['Fic_Asignada']->format('Ymd');
    $Fic_Nombre     = $row['Fic_Nombre'];
    $Fic_Dia_Semana = $row['Fic_Dia_Semana'];
    $Fic_horario    = $row['Fic_horario'];
    // if ($Fic_Asignada2 < '20210319') {
        $query_Fic="SELECT REGISTRO.RegHoRe AS 'Fic_Hora', REGISTRO.RegFeRe AS 'Fic_RegFeRe', 'Fic_Tipo'=CASE REGISTRO.RegTipo WHEN 0 THEN 'Capturador' ELSE 'Manual' END, 'Fic_Estado'=CASE REGISTRO.RegFech WHEN REGISTRO.RegFeRe THEN CASE REGISTRO.RegHora WHEN REGISTRO.RegHoRe THEN 'Normal' ELSE 'Modificada' END ELSE 'Modificada' END FROM REGISTRO WHERE REGISTRO.RegFeAs='$Fic_Asignada2' AND REGISTRO.RegLega='$Fic_Lega' ORDER BY REGISTRO.RegFeAs,REGISTRO.RegLega,REGISTRO.RegFeRe,REGISTRO.RegHoRe";
    // } else {
       //$query_Fic="SELECT REGISTRO.RegHoRe AS 'Fic_Hora', REGISTRO.RegFeRe AS 'Fic_RegFeRe', 'Fic_Tipo'=CASE REGISTRO.RegTipo WHEN 0 THEN 'Capturador' ELSE 'Capturador' END, 'Fic_Estado'=CASE REGISTRO.RegFech WHEN REGISTRO.RegFeRe THEN CASE REGISTRO.RegHora WHEN REGISTRO.RegHoRe THEN 'Normal' ELSE 'Modificada' END ELSE 'Modificada' END FROM REGISTRO WHERE REGISTRO.RegFeAs='$Fic_Asignada2' AND REGISTRO.RegLega='$Fic_Lega' ORDER BY REGISTRO.RegFeAs,REGISTRO.RegLega,REGISTRO.RegFeRe,REGISTRO.RegHoRe";
    // }
    // print_r($query_Fic).PHP_EOL; exit;
    $result_Fic = sqlsrv_query($link, $query_Fic, $param, $options);

    if (sqlsrv_num_rows($result_Fic) > 0) {
        while ($row_Fic = sqlsrv_fetch_array($result_Fic)) :
            $Fic_Hora[] = array(
                'Fic'     => $row_Fic['Fic_Hora'],
                'Estado'  => $row_Fic['Fic_Estado'],
                'Tipo'    => $row_Fic['Fic_Tipo'],
                'RegFeRe' => $row_Fic['Fic_RegFeRe']->format('d/m/Y'),
            );
        endwhile;
        sqlsrv_free_stmt($result_Fic);
        $primero = (array_key_first($Fic_Hora));
        $ultimo  = (array_key_last($Fic_Hora));
        $primero = (array_values($Fic_Hora)[$primero]);
        $ultimo  = (array_values($Fic_Hora)[$ultimo]);
        $ultimo = ($ultimo == $primero) ? array('Fic' => "", 'Estado' => "", 'Tipo' => "", 'RegFeRe' => "") : $ultimo;
    } else {
        $Fic_Hora[] = array('Fic' => "", 'Estado' => "", 'Tipo' => "", 'RegFeRe' => "");
        $primero    = array('Fic' => "", 'Estado' => "", 'Tipo' => "", 'RegFeRe' => "");
        $ultimo     = array('Fic' => "", 'Estado' => "", 'Tipo' => "", 'RegFeRe' => "");
    }

    $entrada = color_fichada3(array($primero));
    $salida  = color_fichada3(array($ultimo));

    // print_r($entrada); exit;

    foreach ($Fic_Hora as $fila) {
        $Fici[] = ("<span class='ls1 px-1'>" . ($fila["Fic"]) . "</span>");
    }

    $Fichadas = implode(" ", $Fici);
    unset($Fici);

    $data[] = array(
        'Fic_Lega'    => $Fic_Lega,
        'Fic_Nombre'  => $Fic_Nombre,
        'num_dia'     => ($Fic_Dia_Semana),
        'Fic_horario' => ($Fic_horario),
        'Fecha'       => ($Fic_Asignada),
        'Primera'     => $entrada['Fic'],
        'Ultima'      => $salida['Fic'],
        'Fichadas'    => ($Fichadas),
        'null'        => '',
    );
    $data2[] = array(
        'Fic_Lega'    => $Fic_Lega,
        'Fic_Nombre'  => $Fic_Nombre,
        'num_dia'     => ($Fic_Dia_Semana),
        'Fic_horario' => ($Fic_horario),
        'Fecha'       => ($Fic_Asignada),
        'Primera'     => $entrada['Fic'],
        'Ultima'      => $salida['Fic'],
        'Fichadas'    => ($Fichadas),
        'null'        => '',
    );
    unset($Fic_Hora);

endwhile;


sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);
$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $data
);

echo json_encode($json_data);
