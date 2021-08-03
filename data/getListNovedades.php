<?php
require __DIR__ . '../../config/index.php';
header("Content-Type: application/json");
UnsetGet('q');
session_start();
E_ALL();


require_once __DIR__ . '../../config/conect_mssql.php';

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$filtroNov ='';
$ListaNov = $_SESSION['ListaNov'];
if ($ListaNov  != "-") {
    $filtroNov = " AND NOVEDAD.NovCodi IN ($ListaNov)";
}


$_POST['Fic'] = $_POST['Fic'] ?? '';
$Fic = test_input($_POST['Fic']);
FusNuloPOST('_nt', '');
FusNuloPOST('_nc', '');
FusNuloPOST('Datos', '0-17530101');
// $_POST['_nt'] = $_POST['_nt'] ?? '';
$_nt = test_input($_POST['_nt']);
$_ntipo = (!empty($_nt)) ? "AND NOVEDAD.NovTipo = '$_nt'" : '';
// print_r($_nt);exit;

switch ($_nt) {
    case '3':
    case '4':
    case '5':
    case '6':
    case '7':
    case '8':
        $_ntipo = "AND NOVEDAD.NovTipo IN (3,4,5,6,7,8)";
        break;
    case '0':
        $_ntipo = "AND NOVEDAD.NovTipo IN (0)";
        break;
    default:
        $_ntipo = $_ntipo;
        break;
}

$_POST['q'] = $_POST['q'] ?? '';
$q = test_input($_POST['q']);

$Datos = explode('-', $_POST['Datos']);
$RegLega = $Datos[0];
$RegFeAs = $Datos[1];
$FiltrarNovTipo2 = '';
$query = "SELECT TOP 1 REGISTRO.RegLega FROM REGISTRO WHERE REGISTRO.RegFeAs = '$RegFeAs' AND REGISTRO.RegLega = '$RegLega'";
// print_r($query);exit;
$result  = sqlsrv_query($link, $query, $params, $options);
if (sqlsrv_num_rows($result) > 0) {

    $FiltrarNovTipo = "WHERE NOVEDAD.NovTipo <= '2'";
    /** Chequeamos si el registro es esta tarde, si lo esta mostramos las novedades de tarde. sino las ocultamos */
    if ($_POST['_nc'] == '2') {
        $queryTar = "SELECT TOP 1 (dbo.fn_STRMinutos(REGISTRO.RegHoRe) - dbo.fn_STRMinutos(FICHAS.FicHorE)) - PERSONAL.LegToTa AS Tarde
            FROM FICHAS 
            INNER JOIN PERSONAL ON FICHAS.FicLega = PERSONAL.LegNume
            LEFT JOIN REGISTRO ON FICHAS.FicLega = REGISTRO.RegLega
            WHERE FICHAS.FicLega = '$RegLega' AND FICHAS.FicFech = '$RegFeAs' AND REGISTRO.RegFeAs = '$RegFeAs'
            ORDER BY REGISTRO.RegHoRe ASC";
        // print_r($queryTar);exit;

        $resultTar  = sqlsrv_query($link, $queryTar, $params, $options);
        while ($rowTar = sqlsrv_fetch_array($resultTar)) {
            if ($rowTar['Tarde'] <= '0') {
                $FiltrarNovTipo2 = "AND NOVEDAD.NovTipo NOT IN (0)";
            } else {
                $FiltrarNovTipo2 = "AND NOVEDAD.NovTipo <= '2'";
            }
        }
        sqlsrv_free_stmt($resultTar);
    }
} else {
    $FiltrarNovTipo = "WHERE NOVEDAD.NovTipo > '2'";
}
sqlsrv_free_stmt($result);


$query = "SELECT DISTINCT NOVEDAD.NovTipo FROM NOVEDAD $FiltrarNovTipo $_ntipo $FiltrarNovTipo2 $filtroNov";
$result  = sqlsrv_query($link, $query, $params, $options);
// print_r($query);exit;
$data = array();

if (sqlsrv_num_rows($result) > 0) {

    while ($fila = sqlsrv_fetch_array($result)) {

        $NovTipo = $fila['NovTipo'];

        $query = "SELECT NOVEDAD.NovCodi AS Codigo , NOVEDAD.NovDesc AS Descripción FROM NOVEDAD WHERE NOVEDAD.NovTipo='$NovTipo' AND NOVEDAD.NovCodi >0 AND CONCAT(' ', NOVEDAD.NovCodi, NOVEDAD.NovDesc) LIKE '%$q%' AND NOVEDAD.NovCodi NOT IN (SELECT FicNove FROM FICHAS3 WHERE FICHAS3.FicLega='$RegLega' AND FICHAS3.FicFech='$RegFeAs') $filtroNov ORDER BY NOVEDAD.NovCodi";
        // print_r($query);exit;

        $result_Nov  = sqlsrv_query($link, $query, $params, $options);

        $Novedades = array();

        if (sqlsrv_num_rows($result_Nov) > 0) {
            while ($row_Nov = sqlsrv_fetch_array($result_Nov)) :
                // $selected = ($row_Nov['Codigo'] == 2) ? 'selected':'';
                $cod = str_pad($row_Nov['Codigo'], 3, "0", STR_PAD_LEFT);
                $Novedades[] = array(
                    'id'       => $row_Nov['Codigo'],
                    'text'     => $cod . ' - ' . $row_Nov['Descripción'],
                );
            endwhile;
            sqlsrv_free_stmt($result_Nov);
        } else {
            $Novedades = array();
        }


        $tipo = strtoupper(TipoNov($NovTipo));
        $disabled = ($NovTipo > '2') ? 'true' : '';

        $data[] = array(
            'text' => $tipo,
            "children" => $Novedades,
            // "disabled"=> $disabled,
        );
        unset($Novedades);
    }
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode(($data)); 
            // print_r($data);
