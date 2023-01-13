<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

/** ALTA DE PARAMS AUSENTES, PRESENTES, FRANCOS Y FERIADOS*/
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'params')) {
    // sleep(1);
    $Franco    = test_input($_POST['Franco']);
    $Feriados  = test_input($_POST['Feriados']);
    $presentes = $_POST['presentes'] ?? '';
    $ausentes  = $_POST['ausentes'] ?? '';
    $cliente   = $_SESSION['ID_CLIENTE'];
    if($presentes){
        $presentes = test_input(implode(',', $presentes));
        $presentes .= '@' . $Franco . '@' . $Feriados;
    }else{
        $presentes = '';
    }
    $ausentes  = test_input(implode(',', $ausentes));
    // $ausentes .= '@'.$Franco.','.$Feriados;

    require __DIR__ . '../../../config/conect_mysql.php';

    /* Comprobamos campos vacíos  */
    if ((valida_campo($presentes)) && (valida_campo($ausentes))) {
        PrintRespuestaJson('error', 'Debe marcar al menos una opción');
        exit;
    } else {
        $query_p = "UPDATE params SET valores='$presentes' WHERE modulo=29 and descripcion='presentes' and cliente = $cliente";
        $query_a = "UPDATE params SET valores='$ausentes' WHERE modulo=29 and descripcion='ausentes' and cliente = $cliente";

        $rs_p = mysqli_query($link, $query_p);
        $rs_a = mysqli_query($link, $query_a);

        if ($rs_a && $rs_p) {
            $_SESSION["CONCEPTO_PRESENTES"] = $presentes;
            $_SESSION["CONCEPTO_AUSENTES"]  = $ausentes;
            $_SESSION["DIAS_FRANCO"]        = $Franco;
            $_SESSION["DIAS_FERIADOS"]      = $Feriados;
            PrintRespuestaJson('ok', 'Datos guardados correctamente');
            mysqli_close($link);
            exit;
        } else {
            PrintRespuestaJson('error', mysqli_error($link));
            mysqli_close($link);
            exit;
        }
    }
}
