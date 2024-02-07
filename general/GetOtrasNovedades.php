<?php
require __DIR__ . '../../config/session_start.php';
require __DIR__ . '../../config/index.php';
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");
secure_auth_ch_json();

E_ALL();
require __DIR__ . '../../config/conect_mssql.php';

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$Datos = (explode('-', ($_GET['Datos'])));

$FicFech = test_input($Datos[1]);
$FicLega = test_input($Datos[0]);

$data = array();

/** OTRAS NOVEDADES */
$query = "SELECT FICHAS2.FicONov as FicONov, OTRASNOV.ONovDesc as Descrip, OTRASNOV.ONovColo as Color, FICHAS2.FicObsN as Observ, FICHAS2.FicValor as FicValor, OTRASNOV.ONovTipo AS ONovTipo, FICHAS2.FicFech as FicFech From FICHAS2,OTRASNOV Where FICHAS2.FicLega = '$FicLega' and FICHAS2.FicFech = '$FicFech' and FICHAS2.FicTurn = 1 and FICHAS2.FicONov = OTRASNOV.ONovCodi and FICHAS2.FicONov > 0 Order By FICHAS2.FicLega,FICHAS2.FicFech,FICHAS2.FicTurn,FICHAS2.FicONov";
// print_r($query); exit;

$result = sqlsrv_query($link, $query, $param, $options);
if (PerCierre($FicFech, $FicLega)) {
    $percierre = true;
    $disabled = 'disabled';
} else {
    $percierre = false;
}
if (sqlsrv_num_rows($result) > 0) {

    while ($row = sqlsrv_fetch_array($result)):
        $NovFechStr = $row['FicFech']->format('Ymd');

        if ($percierre) {
            $editar = '<a data-titler="Editar Novedad:' . $row['Descrip'] . '" href="#TopN"
        class="bi bi-pen btn btn-sm btn-link text-decoration-none ' . $disabled . '" </a>';
            $eliminar = '<a data-titler="Eliminar Novedad: ' . $row['Descrip'] . '" 
        class="bi bi-trash btn btn-sm btn-link text-decoration-none ' . $disabled . '"></a>';
        } else {
            $disabled = '';
            $editar = '<a data-titler="Editar Novedad: ' . $row['Descrip'] . '" href="#TopN"
        class="bi bi-pen btn btn-sm btn-link text-decoration-none mod_ONov text-gris" 
        data="' . $row['FicONov'] . '-' . $NovFechStr . '-' . $FicLega . '"
        data1="' . $row['Descrip'] . '"
        data2="' . $row['Observ'] . '"
        data3="' . $row['FicValor'] . '"
        data4="' . $row['FicONov'] . '"
        </a>';
            $eliminar = '<a data-titler="Eliminar Novedad: ' . $row['Descrip'] . '"
        data="' . $row['FicONov'] . '-' . $NovFechStr . '-' . $FicLega . '"
        data2="' . $row['Descrip'] . '" 
        class="bi bi-trash btn btn-sm btn-link text-decoration-none baja_ONov"></a>';
        }
        $FicValor = ($row['FicValor'] == '0') ? '-' : $row['FicValor'];

        if (str_replace("-", "", $_SESSION['ListaONov'])) {
            if (in_array(intval($row['FicONov']), explode(',', $_SESSION['ListaONov']))) {
                // $editar   = $editar;
                // $eliminar = $eliminar;
            } else {
                $editar = '';
                $eliminar = '';
            }
        }

        $editar = $_SESSION["ABM_ROL"]['mONov'] == '0' ? '' : $editar;
        $eliminar = $_SESSION["ABM_ROL"]['bONov'] == '0' ? '' : $eliminar;

        $data[] = array(
            'Cod' => $row['FicONov'],
            'Descripcion' => $row['Descrip'],
            'FicValor' => ($FicValor),
            'Tipo' => TipoONov($row['ONovTipo']),
            'Observ' => ceronull($row['Observ']),
            'editar' => $editar,
            'eliminar' => $eliminar,
            'null' => ''
        );
    endwhile;
    sqlsrv_free_stmt($result);
}
/** FIN NOVEDADES */
echo json_encode(array('ONovedades' => $data));
sqlsrv_close($link);
exit;



