<?php
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

        $_POST['check']     = $_POST['check'] ?? '';
        $_POST['id_rol']    = $_POST['id_rol'] ?? '';
        $_POST['recid_rol'] = $_POST['recid_rol'] ?? '';
        // $count = count($_POST['check']);
        $datos     = json_decode(test_input(($_POST['check'])));
        $datos     = implode(',', ($datos));
        $recid_rol = (test_input($_POST['recid_rol']));
        $id_rol    = (test_input($_POST['id_rol']));
        $lista     = (test_input($_POST['lista']));

        $NombreLista = listaRol($lista);

        $validaRol = (ExisteRol4($recid_rol, $id_rol));
        if (empty($datos)) {
            $delete = "DELETE FROM lista_roles WHERE id_rol = '$id_rol' and lista = '$lista'";
            if (deleteRegistroMySql($delete)) {
                PrintRespuestaJson('ok', 'Valores eliminados en lista de "' . $NombreLista . '."');
                exit;
            }
        }

        if (!$validaRol) {
            PrintRespuestaJson('error', 'Rol Inválido');
            exit;
        }
        $CheckLista = CountRegMayorCeroMySql("SELECT 1 FROM lista_roles WHERE id_rol = '$id_rol' and lista = '$lista' LIMIT 1");      
        // $datos = str_replace(32768, 0, $datos);
        if ($CheckLista) {
            $update = "UPDATE lista_roles SET datos = '$datos', fecha = '$fechaHora' WHERE id_rol = '$id_rol' and lista = '$lista'";
            if (UpdateRegistroMySql($update)) {
                PrintRespuestaJson('ok', 'Valores guardados en lista de "' . $NombreLista . '."');
                exit;
            }
        } else {
            $insert = "INSERT INTO lista_roles (id_rol, lista, datos, fecha) VALUES ('$id_rol', '$lista', '$datos', '$fechaHora')";
            if (InsertRegistroMySql($insert)) {
                PrintRespuestaJson('ok', 'Valores creados en lista de "' . $NombreLista . '."');
                exit;
            }
        }
    }
    exit;
}
if (array_key_exists('listaRol', $_POST)) {
    if (!empty($_POST['listaRol'])) {

        $_POST['check']     = $_POST['check'] ?? '';
        $_POST['id_rol']    = $_POST['id_rol'] ?? '';
        $_POST['recid_rol'] = $_POST['recid_rol'] ?? '';
        // $count     = count($_POST['check']);
        $arrDatos  = json_decode(test_input(($_POST['check'])));
        $datos     = json_decode(test_input(($_POST['check'])));
        $datos     = implode(',', ($datos));
        $recid_rol = (test_input($_POST['recid_rol']));
        $id_rol    = (test_input($_POST['id_rol']));
        $lista     = (test_input($_POST['listaRol']));

        $NombreLista = listaRol($lista);

        $validaRol = (ExisteRol4($recid_rol, $id_rol));
        if (empty($datos)) {
            PrintRespuestaJson('error', 'Debe seleccionar un rol');
            exit;
        }

        if (!$validaRol) {
            PrintRespuestaJson('error', 'Rol Inválido');
            exit;
        }
        foreach ($arrDatos as $key => $value) {
            /** Recorremos los checks seleccionados y borramos de la tabla lista_roles*/
            $delete = "DELETE FROM lista_roles WHERE id_rol = '$value'";
            deleteRegistroMySql($delete);
        }

        require __DIR__ . '../../../config/conect_mysql.php';
        $stmt = mysqli_query($link, "SELECT id_rol, lista, datos FROM lista_roles WHERE id_rol = '$id_rol'");
        // print_r($query); exit;
        $data=array();
        if (($stmt)) {
            if (mysqli_num_rows($stmt) > 0) {
                while ($row = mysqli_fetch_assoc($stmt)) {
                    $data[] = array(
                        'id_rol' => $row['id_rol'],
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
                    $id_rol = $value['id_rol'];
                    $lista  = $value['lista'];
                    $datos  = $value['datos'];

                    $insert = "INSERT INTO lista_roles (id_rol, lista, datos, fecha) VALUES ('$valueDatos', '$lista', '$datos', '$fechaHora')";
                    InsertRegistroMySql($insert);
                }
            }
            $data = array('status' => 'ok', 'Mensaje' => 'Valores copiados correctamente a los roles:<br><span class="ls1">'.json_encode($arrDatos).'</span>', 'data' => ($arrDatos));
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
