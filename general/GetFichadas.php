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

$Datos = explode('-', $_GET['Datos']);

$Fecha = $Datos[1];
$Legajo = $Datos[0];

$data = array();

/** FICHADAS */
// if ($Fecha < '20210319') {
$query = "SELECT REGISTRO.RegHoRe AS Fic_Hora, REGISTRO.RegFech AS RegFech, REGISTRO.RegTarj AS RegTarj, REGISTRO.RegHora AS RegHora, REGISTRO.RegLega AS RegLega, Fic_Tipo=CASE REGISTRO.RegTipo WHEN 0 THEN 'Capturador' ELSE 'Manual' END, Fic_Estado=CASE REGISTRO.RegFech WHEN REGISTRO.RegFeRe THEN CASE REGISTRO.RegHora WHEN REGISTRO.RegHoRe THEN 'Normal' ELSE 'Modificada' END ELSE 'Modificada' END, REGISTRO.RegTipo AS RegTipo, REGISTRO.RegFeRe as RegFeRe FROM REGISTRO WHERE REGISTRO.RegFeAs='$Fecha' AND REGISTRO.RegLega='$Legajo' ORDER BY REGISTRO.RegFeAs,REGISTRO.RegLega,REGISTRO.RegFeRe,REGISTRO.RegHoRe";
// } else {
//$query = "SELECT REGISTRO.RegHoRe AS Fic_Hora, REGISTRO.RegFech AS RegFech, REGISTRO.RegTarj AS RegTarj, REGISTRO.RegHora AS RegHora, REGISTRO.RegLega AS RegLega, Fic_Tipo=CASE REGISTRO.RegTipo WHEN 0 THEN 'Capturador' ELSE 'Capturador' END, Fic_Estado=CASE REGISTRO.RegFech WHEN REGISTRO.RegFeRe THEN CASE REGISTRO.RegHora WHEN REGISTRO.RegHoRe THEN 'Normal' ELSE 'Modificada' END ELSE 'Modificada' END, REGISTRO.RegTipo AS RegTipo, REGISTRO.RegFeRe as RegFeRe FROM REGISTRO WHERE REGISTRO.RegFeAs='$Fecha' AND REGISTRO.RegLega='$Legajo' ORDER BY REGISTRO.RegFeAs,REGISTRO.RegLega,REGISTRO.RegFeRe,REGISTRO.RegHoRe";
// }

$result = sqlsrv_query($link, $query, $param, $options);
// print_r($query).PHP_EOL; exit;
if (PerCierre($Fecha, $Legajo)) {
    $percierre = true;
    $disabled = 'disabled';
} else {
    $percierre = false;
}
if (sqlsrv_num_rows($result) > 0) {
    while ($row_Fic = sqlsrv_fetch_array($result)):

        $RegFechaDR = $row_Fic['RegFech']->format('d/m/Y');
        $RegFechStr = $row_Fic['RegFech']->format('Ymd');
        $RegFeRe = $row_Fic['RegFeRe']->format('Y-m-d');

        if ($percierre) {
            $editar = '<a data-titler="Editar Fichada: ' . $row_Fic['Fic_Hora'] . '" href="#TopN"
            class="bi bi-pen btn btn-sm btn-link text-decoration-none ' . $disabled . '"> </a>';
            $eliminar = '<a data-titler="Eliminar Fichada: ' . $row_Fic['Fic_Hora'] . '" 
            class="bi bi-trash btn btn-sm btn-link text-decoration-none ' . $disabled . '"></a>';
        } else {
            $disabled = '';
            $editar = '<a data-titler="Editar Fichada: ' . $row_Fic['Fic_Hora'] . '" href="#" class="bi bi-pen btn btn-sm btn-link text-decoration-none mod_Fic" data-fechadr="' . $RegFechaDR . '" data="' . $RegFechStr . '-' . $row_Fic['RegTarj'] . '-' . $row_Fic['RegHora'] . '-' . $row_Fic['RegLega'] . '-' . $row_Fic['RegTipo'] . '" data2="' . $row_Fic['Fic_Hora'] . '" data3="' . $RegFeRe . '"></a>';
            $eliminar = '<a data-titler="Eliminar Fichada: ' . $row_Fic['Fic_Hora'] . '" data2="' . $row_Fic['Fic_Hora'] . '" data="' . $RegFechStr . '-' . $row_Fic['RegTarj'] . '-' . $row_Fic['RegHora'] . '-' . $row_Fic['RegLega'] . '" id="' . $RegFechStr . '-' . $row_Fic['RegTarj'] . '-' . $row_Fic['RegHora'] . '-' . $row_Fic['RegLega'] . '" class="bi bi-trash btn btn-sm btn-link text-decoration-none baja_Fic"></a>';
        }
        $editar = $_SESSION["ABM_ROL"]['mFic'] == '0' ? '' : $editar;
        $eliminar = $_SESSION["ABM_ROL"]['bFic'] == '0' ? '' : $eliminar;

        $data[] = array(
            'Fic' => color_Fichada2($row_Fic['Fic_Tipo'], $row_Fic['Fic_Estado'], $row_Fic['Fic_Hora']),
            'Estado' => $row_Fic['Fic_Estado'],
            'Tipo' => $row_Fic['Fic_Tipo'],
            'Fecha' => $row_Fic['RegFeRe']->format('d/m/Y'),
            'Original' => $row_Fic['RegFech']->format('d/m/Y') . ' ' . $row_Fic['RegHora'],
            'editar' => $editar,
            'eliminar' => $eliminar,
            'null' => ''
        );
    endwhile;
    sqlsrv_free_stmt($result);
}

/** FIN FICHADAS */

echo json_encode(array('Fichadas' => $data));

sqlsrv_close($link);
exit;
