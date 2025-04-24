<?php
require __DIR__ . '/../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$data = array();
$FechaHora = date('Ymd H:i:s');

$_POST['alta_cierre'] = $_POST['alta_cierre'] ?? '';


/** GENERA CIERRE */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta_cierre'] == 'true')) {

    FusNuloPOST('Quita', '');
    FusNuloPOST('legajo', '');
    FusNuloPOST('Tipo', '0');
    FusNuloPOST('cierre', '');
    FusNuloPOST('Emp', '');
    FusNuloPOST('Plan', '');
    FusNuloPOST('Sect', '');
    FusNuloPOST('Sec2', '');
    FusNuloPOST('Grup', '');
    FusNuloPOST('Sucur', '');
    $FechaHora = date('Ymd H:i:s');
    $t = explode(" ", microtime());
    $FechaHora = date("Ymd H:i:s", $t[1]) . substr((string) $t[0], 1, 4);
    // $CierreFech = test_input(FechaString($_POST['cierre']));
    $CierreFech = test_input(dr_fecha($_POST['cierre']));

    // $legajo = !empty(($_POST['legajo'])) ? implode(',', $_POST['legajo']):''; 

    // $data = array('status' => 'error', 'dato' => $legajo);
    // echo json_encode($data);
    // exit;

    if ($_POST['Quita'] != 'on') {
        if ((valida_campo($_POST['legajo'])) || (valida_campo($_POST['cierre']))) {
            $data = array('status' => 'error', 'dato' => 'Campos requeridos!');
            echo json_encode($data);
            exit;
        }
        $CierreFech2 = '<span class="fw5">' . (FechaFormatVar($CierreFech, ('d/m/Y'))) . '</span>';
    } else {
        if ((valida_campo($_POST['legajo']))) {
            $data = array('status' => 'error', 'dato' => 'Campos requeridos!');
            echo json_encode($data);
            exit;
        }
        $CierreFech = '17530101';
        $CierreFech2 = '<span class="fw5">Eliminada</span>';
    }

    foreach ($_POST['legajo'] as $key => $value) {
        $FechaHora = fechaHora();
        $legajo = $value;
        if (CountRegistrosMayorCero("SELECT CierreLega FROM PERCIERRE WHERE CierreLega = '$legajo' AND CierreFech != '$CierreFech'")) {
            $update = UpdateRegistro("UPDATE PERCIERRE SET CierreFech = '$CierreFech', FechaHora = '$FechaHora' Where CierreLega = '$legajo'");
            audito_ch2("M", 'Cierre Legajo ' . $legajo . '. Fecha: ' . strip_tags($CierreFech2), '14');
        } else {
            if (CountRegistrosMayorCero("SELECT CierreLega FROM PERCIERRE WHERE CierreLega = '$legajo'")) {
                //$update = UpdateRegistro("UPDATE PERCIERRE SET CierreFech = '$CierreFech', FechaHora = '$FechaHora' Where CierreLega = '$legajo'");
                // audito_ch2("M", 'Cierre Legajo '.$legajo.'. Fecha: ' . strip_tags($CierreFech2));
            } else {
                $Insert = InsertRegistro("INSERT INTO PERCIERRE (CierreLega, CierreFech, FechaHora)VALUES('$legajo','$CierreFech', '$FechaHora')");
                audito_ch2("A", 'Cierre Legajo ' . $legajo . '. Fecha: ' . strip_tags($CierreFech2), '14');
            }

        }
    }
    $count = count($_POST['legajo']);
    $legajos = !empty(($_POST['legajo'])) ? implode(', ', $_POST['legajo']) : '';
    $data = array('status' => 'ok', 'dato' => '<span class="fw5">Cierres generados correctamente.</span> <br />Fecha de Cierre: <span class="ls1">' . ($CierreFech2) . '</span>. Total: ' . $count);
    echo json_encode($data);
    // audito_ch("M", 'Cierre Legajos. Fecha: ' . strip_tags($CierreFech2));
}
