<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");

require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';
E_ALL();
$data = array();

$params = $_REQUEST;
if (isset($_POST['_f']) && !empty($_POST['_f'])) {
    $Fecha = test_input(FusNuloPOST('_f', 'vacio'));
} else {
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

$params = $columns = $totalRecords = '';
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

$sql_query = "SELECT DISTINCT FICHAS2.FicLega AS 'nov_LegNume',
    FICHAS2.FicFech AS 'nov_Fecha', PERSONAL.LegApNo AS 'nov_leg_nombre',
    dbo.fn_DiaDeLaSemana(FICHAS2.FicFech) AS 'nov_dia_semana',
    dbo.fn_HorarioAsignado(
        FICHAS.FicHorE,
        FICHAS.FicHorS,
        FICHAS.FicDiaL,
        FICHAS.FicDiaF
    ) AS 'nov_horario'
    FROM FICHAS2
    LEFT JOIN FICHAS ON FICHAS2.FicLega = FICHAS.FicLega AND FICHAS2.FicFech = FICHAS.FicFech AND FICHAS2.FicTurn = FICHAS.FicTurn
    INNER JOIN OTRASNOV ON FICHAS2.FicONov = OTRASNOV.ONovCodi 
    INNER JOIN PERSONAL ON FICHAS2.FicLega = PERSONAL.LegNume
    WHERE FICHAS2.FicFech = '$Fecha' $FilterEstruct $FilterEstruct2 $FiltrosFichas";

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (!empty($params['search']['value'])) {
    $where_condition .=    " AND ";
    $where_condition .= " (CONCAT(PERSONAL.LegNume,PERSONAL.LegApNo) LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR NOVEDAD.NovDesc LIKE '%" . $params['search']['value'] . "%')";
}

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}

$sqlRec .=  " ORDER BY FICHAS2.FicFech, FICHAS2.FicLega OFFSET " . $params['start'] . " ROWS FETCH NEXT " . $params['length'] . " ROWS ONLY";
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);
$queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);

while ($row = sqlsrv_fetch_array($queryRecords)) :

    $nov_leg_nombre = $row['nov_leg_nombre'];
    $nov_LegNume    = $row['nov_LegNume'];
    $nov_Fecha      = ($row['nov_Fecha']->format('d/m/Y'));
    $nov_Fecha2     = ($row['nov_Fecha']->format('Ymd'));
    $nov_dia_semana = $row['nov_dia_semana'];
    $nov_horario    = $row['nov_horario'];

    $query_Nov = "SELECT FICHAS2.FicONov AS 'nov_novedad',
        OTRASNOV.ONovDesc AS 'nov_descripcion',
        OTRASNOV.ONovTipo AS 'nov_tipo',
        FICHAS2.FicValor AS 'nov_horas',
        FICHAS2.FicObsN AS 'nov_observ'
    FROM FICHAS2
    LEFT JOIN FICHAS ON FICHAS2.FicLega = FICHAS.FicLega AND FICHAS2.FicFech = FICHAS.FicFech AND FICHAS2.FicTurn = FICHAS.FicTurn
    INNER JOIN OTRASNOV ON FICHAS2.FicONov = OTRASNOV.ONovCodi
    WHERE FICHAS2.FicFech = '$nov_Fecha2'
        and FICHAS2.FicLega = '$nov_LegNume' $FilterEstruct2";

    // print_r($query_Nov).PHP_EOL; exit;

    $Novedad = array();
    $result_Nov = sqlsrv_query($link, $query_Nov, $param, $options);
    if (sqlsrv_num_rows($result_Nov) > 0) {
        while ($row_Nov = sqlsrv_fetch_array($result_Nov)) :
            $Novedad[] = array(
                'cod'       => $row_Nov['nov_novedad'],
                'desc'      => $row_Nov['nov_descripcion'],
                'tipo'      => $row_Nov['nov_tipo'],
                'horas'     => $row_Nov['nov_horas'],
                'observ'    => $row_Nov['nov_observ']
            );
        endwhile;
        sqlsrv_free_stmt($result_Nov);
    }

    if (is_array($Novedad)) {
        foreach ($Novedad as $fila) {
            $Cod[]    = ($fila["cod"]);
            $desc[]   = '<span title="(' . $fila['cod'] . ') ' . $fila['desc'] . ' ' . $fila["horas"] . 'hs.">' . ($fila["desc"]) . '</span>';
            $horas[]  = ($fila["horas"]);
            $observ[] = ($fila["observ"]);
            $tipo[]   = TipoONov($fila["tipo"]);
        }

        $NoveCod    = implode("<br/>", $Cod);
        $Novedades2 = implode("<br/>", $desc);
        $NoveHoras  = implode("<br/>", $horas);
        $NoveObserv = implode("<br/>", $observ);
        $NoveTipo = implode("<br/>", $tipo);
        unset($Cod);
        unset($desc);
        unset($horas);
        unset($observ);
        unset($tipo);
    }

    $data[] = array(
        'nov_leg_nombre' => $nov_leg_nombre,
        'nov_LegNume'    => $nov_LegNume,
        'Fecha'          => ($nov_Fecha),
        'nov_nom_dia'    => ($nov_dia_semana),
        'nov_horario'    => $nov_horario,
        'NoveCod'        => $NoveCod,
        'Novedades'      => $Novedades2,
        'NovValor'         => $NoveHoras,
        'NoveObserv'     => $NoveObserv,
        'NoveTipo'       => ($NoveTipo),
    );
    unset($Novedad);
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
// print_r($datos);
