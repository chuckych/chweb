<?php
require __DIR__ . '/../config/session_start.php';
require __DIR__ . '/../config/index.php';
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");
secure_auth_ch_json();
E_ALL();

require __DIR__ . '/../config/conect_mssql.php';

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$Datos = explode('-', $_GET['Datos']);

$Fecha = $Datos[1];
$Legajo = $Datos[0];

$data = array();

/** FICHADAS */
if ($Fecha < '20210319') {
    $query = "SELECT REGISTRO.RegHoRe AS Fic_Hora, REGISTRO.RegFech AS RegFech, REGISTRO.RegTarj AS RegTarj, REGISTRO.RegHora AS RegHora, REGISTRO.RegLega AS RegLega, Fic_Tipo=CASE REGISTRO.RegTipo WHEN 0 THEN 'Capturador' ELSE 'Manual' END, Fic_Estado=CASE REGISTRO.RegFech WHEN REGISTRO.RegFeRe THEN CASE REGISTRO.RegHora WHEN REGISTRO.RegHoRe THEN 'Normal' ELSE 'Modificada' END ELSE 'Modificada' END, REGISTRO.RegTipo AS RegTipo, REGISTRO.RegFeRe as RegFeRe FROM REGISTRO WHERE REGISTRO.RegFeAs='$Fecha' AND REGISTRO.RegLega='$Legajo' ORDER BY REGISTRO.RegFeAs,REGISTRO.RegLega,REGISTRO.RegFeRe,REGISTRO.RegHoRe";
} else {
    $query = "SELECT REGISTRO.RegHoRe AS Fic_Hora, REGISTRO.RegFech AS RegFech, REGISTRO.RegTarj AS RegTarj, REGISTRO.RegHora AS RegHora, REGISTRO.RegLega AS RegLega, Fic_Tipo=CASE REGISTRO.RegTipo WHEN 0 THEN 'Capturador' ELSE 'Capturador' END, Fic_Estado=CASE REGISTRO.RegFech WHEN REGISTRO.RegFeRe THEN CASE REGISTRO.RegHora WHEN REGISTRO.RegHoRe THEN 'Normal' ELSE 'Modificada' END ELSE 'Modificada' END, REGISTRO.RegTipo AS RegTipo, REGISTRO.RegFeRe as RegFeRe FROM REGISTRO WHERE REGISTRO.RegFeAs='$Fecha' AND REGISTRO.RegLega='$Legajo' ORDER BY REGISTRO.RegFeAs,REGISTRO.RegLega,REGISTRO.RegFeRe,REGISTRO.RegHoRe";
}

$result = sqlsrv_query($link, $query, $param, $options);
// print_r($query).PHP_EOL; exit;
if (sqlsrv_num_rows($result) > 0) {
    while ($row_Fic = sqlsrv_fetch_array($result)):

        $RegFechStr = $row_Fic['RegFech']->format('Ymd');
        $RegFeRe = $row_Fic['RegFeRe']->format('Y-m-d');

        $data[] = array(
            'Fic' => color_Fichada2($row_Fic['Fic_Tipo'], $row_Fic['Fic_Estado'], $row_Fic['Fic_Hora']),
            'Estado' => $row_Fic['Fic_Estado'],
            'Tipo' => $row_Fic['Fic_Tipo'],
            'Fecha' => $row_Fic['RegFeRe']->format('d/m/Y'),
            'Original' => $row_Fic['RegFech']->format('d/m/Y') . ' ' . $row_Fic['RegHora'],
            'editar' => '<a title="Editar Fichada: ' . $row_Fic['Fic_Hora'] . '" href="#" class="icon btn btn-sm btn-link text-decoration-none mod_Fic" data="' . $RegFechStr . '-' . $row_Fic['RegTarj'] . '-' . $row_Fic['RegHora'] . '-' . $row_Fic['RegLega'] . '-' . $row_Fic['RegTipo'] . '" data2="' . $row_Fic['Fic_Hora'] . '" data3="' . $RegFeRe . '"><span data-icon="&#xe042;" class="align-middle text-gris"></span></a>',
            'eliminar' => '<a title="Eliminar Fichada: ' . $row_Fic['Fic_Hora'] . '" data2="' . $row_Fic['Fic_Hora'] . '" data="' . $RegFechStr . '-' . $row_Fic['RegTarj'] . '-' . $row_Fic['RegHora'] . '-' . $row_Fic['RegLega'] . '" class="icon btn btn-sm btn-link text-decoration-none baja_Fic"><span data-icon="&#xe03d;" class="align-middle text-gris"></span></a>',
            'null' => ''
        );
    endwhile;
    sqlsrv_free_stmt($result);
}

/** FIN FICHADAS */

echo json_encode(array('Fichadas' => $data));

sqlsrv_close($link);
exit;
