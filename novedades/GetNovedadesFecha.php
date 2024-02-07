<?php
require __DIR__ . '../../config/session_start.php';
require __DIR__ . '../../config/index.php';
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");
secure_auth_ch_json();

require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';
E_ALL();

$data = array();
$params = $_REQUEST;
if (isset($_POST['_f']) && !empty($_POST['_f'])) {
    $Fecha = test_input(FusNuloPOST('_f', 'vacio'));
} else {
    $json_data = array(
        "draw" => intval($params['draw']),
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => $data
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

$sql_query = "SELECT DISTINCT FICHAS.FicLega AS 'nov_LegNume', PERSONAL.LegApNo AS 'nov_leg_nombre', FICHAS.FicFech AS 'nov_Fecha', dbo.fn_DiaDeLaSemana(FICHAS.FicFech) AS 'nov_dia_semana', dbo.fn_HorarioAsignado(FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicDiaL, FICHAS.FicDiaF) AS 'nov_horario' FROM FICHAS INNER JOIN FICHAS3 ON FICHAS.FicLega=FICHAS3.FicLega AND FICHAS.FicFech=FICHAS3.FicFech INNER JOIN NOVEDAD ON FICHAS3.FicNove=NOVEDAD.NovCodi LEFT JOIN NOVECAUSA ON FICHAS3.FicNove=NOVECAUSA.NovCNove AND FICHAS3.FicCaus=NOVECAUSA.NovCCodi INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume WHERE FICHAS.FicFech='$Fecha' AND FICHAS3.FicNove >0 AND FICHAS3.FicNoTi >=0 $FilterEstruct $FiltrosFichas";

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (!empty($params['search']['value'])) {
    $where_condition .= " AND ";
    $where_condition .= " (CONCAT(PERSONAL.LegNume,PERSONAL.LegApNo) LIKE '%" . $params['search']['value'] . "%' ";
    $where_condition .= " OR NOVEDAD.NovDesc LIKE '%" . $params['search']['value'] . "%')";
}

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}

$sqlRec .= " ORDER BY FICHAS.FicLega,FICHAS.FICFech, PERSONAL.LegApNo OFFSET " . $params['start'] . " ROWS FETCH NEXT " . $params['length'] . " ROWS ONLY";
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);
$queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);

// print_r(($sqlRec)); exit;

// $data  = array();
// if (sqlsrv_num_rows($result) > 0) {
while ($row = sqlsrv_fetch_array($queryRecords)):
    $nov_leg_nombre = $row['nov_leg_nombre'];
    $nov_LegNume = $row['nov_LegNume'];
    $nov_Fecha = ($row['nov_Fecha']->format('d/m/Y'));
    $nov_Fecha2 = ($row['nov_Fecha']->format('Ymd'));
    $nov_FechaStr = ($row['nov_Fecha']->format('Y-m-d'));
    $nov_dia_semana = $row['nov_dia_semana'];
    $nov_horario = $row['nov_horario'];

    $query_Nov = "SELECT FICHAS3.FicFech as 'nov_fecha', FICHAS3.FicLega as 'nov_lega',FICHAS3.FicNove AS nov_novedad, NOVEDAD.NovDesc AS nov_descripcion, NOVEDAD.NovTipo AS nov_tipo, FICHAS3.FicHoras AS nov_horas,FICHAS3.FicCate as 'nov_cate', FICHAS3.FicNoTi AS 'nov_tipo_cod', FICHAS3.FicCaus AS nov_cod_causa, NOVECAUSA.NovCDesc AS nov_causa, nov_justif=CASE FICHAS3.FicJust WHEN 1 THEN 'Si' ELSE 'No' END, FICHAS3.FicObse AS nov_observ FROM FICHAS  INNER JOIN PERSONAL ON FICHAS.FicLega = PERSONAL.LegNume INNER JOIN FICHAS3 ON FICHAS.FicLega=FICHAS3.FicLega AND FICHAS.FicFech=FICHAS3.FicFech INNER JOIN NOVEDAD ON FICHAS3.FicNove=NOVEDAD.NovCodi INNER JOIN NOVECAUSA ON FICHAS3.FicNove=NOVECAUSA.NovCNove AND FICHAS3.FicCaus=NOVECAUSA.NovCCodi WHERE FICHAS.FicFech='$nov_Fecha2' AND FICHAS.FicLega=$nov_LegNume $FilterEstruct";
    // print_r($query_Nov).PHP_EOL; exit;

    $result_Nov = sqlsrv_query($link, $query_Nov, $param, $options);
    if (sqlsrv_num_rows($result_Nov) > 0) {
        while ($row_Nov = sqlsrv_fetch_array($result_Nov)):
            $Novedad[] = array(
                'cod' => $row_Nov['nov_novedad'],
                'desc' => $row_Nov['nov_descripcion'],
                'tipo' => $row_Nov['nov_tipo'],
                'tipo_cod' => $row_Nov['nov_tipo_cod'],
                'horas' => $row_Nov['nov_horas'],
                'cod_causa' => $row_Nov['nov_cod_causa'],
                'causa' => $row_Nov['nov_causa'],
                'justif' => $row_Nov['nov_justif'],
                'observ' => $row_Nov['nov_observ'],
                'nov_cate' => $row_Nov['nov_cate'],
                'nov_fecha' => ($row_Nov['nov_fecha']->format('Ymd')),
                'nov_lega' => $row_Nov['nov_lega'],
            );
        endwhile;
        sqlsrv_free_stmt($result_Nov);
    } else {
        $Novedad = array(
            'cod' => '',
            'desc' => '',
            'tipo' => '',
            'horas' => '',
            'cod_causa' => '',
            'causa' => '',
            'justif' => '',
            'observ' => '',
            'nov_cate' => '',
            'nov_fecha' => '',
            'nov_lega' => '',
        );
    }

    if (is_array($Novedad)) {
        foreach ($Novedad as $fila) {
            $Cod[] = ($fila["cod"]);
            $desc[] = '<span title="(' . $fila['cod'] . ') ' . $fila['desc'] . ' ' . $fila["horas"] . 'hs.">' . ($fila["desc"]) . '</span>';
            $horas[] = ($fila["horas"]);
            $causa[] = ($fila["causa"]);
            $observ[] = ($fila["observ"]);
            $justif[] = ($fila["justif"]);
            $tipo[] = TipoNov($fila["tipo"]);
            $tipo_cod[] = ($fila["tipo_cod"]);
        }

        $NoveCod = implode("<br/>", $Cod);
        $Novedades2 = implode("<br/>", $desc);
        $NoveHoras = implode("<br/>", $horas);
        $NoveCausa = implode("<br/>", $causa);
        $NoveObserv = implode("<br/>", $observ);
        $NoveJustif = implode("<br/>", $justif);
        $NoveTipo = implode("<br/>", $tipo);
        $NoveTipoCod = implode("<br/>", $tipo_cod);
        unset($Cod);
        unset($desc);
        unset($horas);
        unset($causa);
        unset($observ);
        unset($justif);
        unset($tipo);
        unset($tipo_cod);
    }

    $data[] = array(
        'nov_LegNume' => $nov_LegNume,
        'nov_leg_nombre' => $nov_leg_nombre,
        'Fecha' => ($nov_Fecha),
        'FechaStr' => ($nov_FechaStr),
        'num_dia' => ($nov_dia_semana),
        'nov_nom_dia' => ($nov_dia_semana),
        'nov_horario' => $nov_horario,
        'NoveCod' => $NoveCod,
        'Novedades' => $Novedades2,
        'NovHor' => $NoveHoras,
        'NoveCausa' => $NoveCausa,
        'NoveObserv' => $NoveObserv,
        'NoveJustif' => $NoveJustif,
        'NoveTipo' => ($NoveTipo),
        'NoveTipoCod' => $NoveTipoCod,
        'arrayNove' => $Novedad ?? '',
        'NoveEdit' => $_SESSION['ABM_ROL']['mNov'],
        'NoveDelete' => $_SESSION['ABM_ROL']['bNov'],
    );
    unset($Novedad);
endwhile;

sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);
$json_data = array(
    "draw" => intval($params['draw']),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $data
);

echo json_encode($json_data);
// print_r($datos);
