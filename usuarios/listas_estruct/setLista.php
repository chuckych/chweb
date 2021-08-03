<?php
ini_set('max_execution_time', 900); // 900 segundos 15 minutos
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();

require __DIR__ . '../../../config/conect_mysql.php';

$fechaHora = date("Y-m-d H:i:s");

if (array_key_exists('lista', $_POST)) {
    if (!empty($_POST['lista'])) {

        $_POST['check']   = $_POST['check'] ?? '';
        $_POST['uid']     = $_POST['uid'] ?? '';
        $_POST['_c'] = $_POST['_c'] ?? '';
        $datos            = json_decode(test_input(($_POST['check'])));
        $datos            = implode(',', ($datos));
        $uid              = (test_input($_POST['uid']));
        $cliente          = (test_input($_POST['_c']));
        $lista            = (test_input($_POST['lista']));

        $NombreLista = listaEstruct($lista);

        $validaUser = (ExisteUser($cliente, $uid));

        if (empty($datos)) {
            $delete = "DELETE FROM lista_estruct WHERE uid = '$uid' and lista = '$lista'";
            if (deleteRegistroMySql($delete)) {
                PrintRespuestaJson('ok', 'Valores eliminados en lista de "' . $NombreLista . '."');
                exit;
            }
        }

        if (!$validaUser) {
            PrintRespuestaJson('error', 'Usuario Inválido');
            exit;
        }

        $CheckLista = CountRegMayorCeroMySql("SELECT 1 FROM lista_estruct WHERE uid = '$uid' and lista = '$lista' LIMIT 1");
        // $datos = str_replace(32768, 0, $datos);
        if ($CheckLista) {
            $update = "UPDATE lista_estruct SET datos = '$datos', fecha = '$fechaHora' WHERE uid = '$uid' and lista = '$lista'";
            if (UpdateRegistroMySql($update)) {
                PrintRespuestaJson('ok', 'Valores guardados en lista de "' . $NombreLista . '."');
                exit;
            }
        } else {
            $insert = "INSERT INTO lista_estruct (uid, lista, datos, fecha) VALUES ('$uid', '$lista', '$datos', '$fechaHora')";
            if (InsertRegistroMySql($insert)) {
                PrintRespuestaJson('ok', 'Valores creados en lista de "' . $NombreLista . '."');
                exit;
            }
        }
    }
    exit;
}

if (array_key_exists('lista2', $_POST)) {
    if (!empty($_POST['lista2'])) {

        $_POST['check'] = $_POST['check'] ?? '';
        $_POST['uid']   = $_POST['uid'] ?? '';
        $_POST['_c']    = $_POST['_c'] ?? '';
        $datos            = json_decode(test_input(($_POST['check'])));
        $datos            = implode(',', ($datos));
        $uid              = (test_input($_POST['uid']));
        $cliente          = (test_input($_POST['_c']));
        $lista            = (test_input($_POST['lista2']));

        $NombreLista = listaEstruct($lista);

        $validaUser = (ExisteUser($cliente, $uid));
        if (empty($datos)) {
            $delete = "DELETE FROM lista_estruct WHERE uid = '$uid' and lista = '$lista'";
            if (deleteRegistroMySql($delete)) {
                PrintRespuestaJson('ok', 'Valores eliminados en lista de "' . $NombreLista . '."');
                exit;
            }
        }

        if (!$validaUser) {
            PrintRespuestaJson('error', 'Usuario Inválido');
            exit;
        }

        $CheckLista = CountRegMayorCeroMySql("SELECT 1 FROM lista_estruct WHERE uid = '$uid' and lista = '$lista' LIMIT 1");

        if ($CheckLista) {
            $update = "UPDATE lista_estruct SET datos = '$datos', fecha = '$fechaHora' WHERE uid = '$uid' and lista = '$lista'";
            if (UpdateRegistroMySql($update)) {
                PrintRespuestaJson('ok', 'Valores guardados en lista de "' . $NombreLista . '."');
                exit;
            }
        } else {
            $insert = "INSERT INTO lista_estruct (uid, lista, datos, fecha) VALUES ('$uid', '$lista', '$datos', '$fechaHora')";
            if (InsertRegistroMySql($insert)) {
                PrintRespuestaJson('ok', 'Valores creados en lista de "' . $NombreLista . '."');
                exit;
            }
        }
    }
    exit;
}

if (array_key_exists('listaEstruct', $_POST)) {
    if (!empty($_POST['listaEstruct'])) {

        $_POST['check'] = $_POST['check'] ?? '';
        $_POST['uid']   = $_POST['uid'] ?? '';
        $_POST['_c']    = $_POST['_c'] ?? '';
        $arrDatos       = json_decode(test_input(($_POST['check'])));
        $datos          = json_decode(test_input(($_POST['check'])));
        $datos          = implode(',', ($datos));
        $uid            = (test_input($_POST['uid']));
        $cliente        = (test_input($_POST['_c']));
        $lista          = (test_input($_POST['listaEstruct']));

        $NombreLista = listaEstruct($lista);

        $validaUser = (ExisteUser($cliente, $uid));
        if (empty($datos)) {
            PrintRespuestaJson('error', 'Debe seleccionar un usuario');
            exit;
        }

        if (!$validaUser) {
            PrintRespuestaJson('error', 'Usuario Inválido');
            exit;
        }
        foreach ($arrDatos as $key => $value) {
            /** Recorremos los checks seleccionados y borramos de la tabla lista_estruct*/
            $delete = "DELETE FROM lista_estruct WHERE uid = '$value'";
            deleteRegistroMySql($delete);
        }

        require __DIR__ . '../../../config/conect_mysql.php';
        $stmt = mysqli_query($link, "SELECT uid, lista, datos FROM lista_estruct WHERE uid = '$uid'");
        // print_r($query); exit;
        $data=array();
        if (($stmt)) {
            if (mysqli_num_rows($stmt) > 0) {
                while ($row = mysqli_fetch_assoc($stmt)) {
                    $data[] = array(
                        'uid' => $row['uid'],
                        'lista'  => $row['lista'],
                        'datos'  => $row['datos']
                    );
                }
            }

            mysqli_free_result($stmt);
            mysqli_close($link);

            foreach ($arrDatos as $key => $valueDatos) {
                /** Recorremos los checks seleccionados */
                foreach ($data as $key => $value) {
                    /** Recorremos el resultado del select de arriba para insertar los valores*/
                    $uid = $value['uid'];
                    $lista  = $value['lista'];
                    $datos  = $value['datos'];

                    $insert = "INSERT INTO lista_estruct (uid, lista, datos, fecha) VALUES ('$valueDatos', '$lista', '$datos', '$fechaHora')";
                    InsertRegistroMySql($insert);
                }
            }
            $data = array('status' => 'ok', 'Mensaje' => 'Valores copiados correctamente.', 'data' => ($arrDatos));
            echo json_encode($data);
            exit;
        } else {
            statusData('error', mysqli_error($link));
            mysqli_close($link);
            exit;
        }
    }
    exit;
}
if (array_key_exists('listaInit', $_POST)) {
    if (!empty($_POST['listaInit'])) {

        $_POST['uid']   = $_POST['uid'] ?? '';
        $_POST['_c']    = $_POST['_c'] ?? '';
        $uid            = (test_input($_POST['uid']));
        $cliente        = (test_input($_POST['_c']));

        $validaUser = (ExisteUser($cliente, $uid));

        if (!$validaUser) {
            PrintRespuestaJson('error', 'Usuario Inválido');
            exit;
        }
        $delete = "DELETE FROM lista_estruct WHERE uid = '$uid'";
        if (deleteRegistroMySql($delete)) {
            $data = array('status' => 'ok', 'Mensaje' => 'Estructura inicializada correctamente.');
            echo json_encode($data);
            exit;
        } else {
            statusData('error', mysqli_error($link));
            mysqli_close($link);
            exit;
        }
    }
    exit;
}
