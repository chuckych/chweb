<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");

require __DIR__ . '../../../config/conect_mssql.php';

$data = array();
$countSet = '';
$dataLista = array();
$set = 0;
$_GET['id_rol']    = $_GET['id_rol'] ?? '';
$_GET['recid_rol'] = $_GET['recid_rol'] ?? '';
$_GET['_c']        = $_GET['_c'] ?? '';

$id_rol        = test_input($_GET['id_rol']);
$lista         = test_input($_GET['lista']);
$recid_cliente = test_input($_GET['_c']);

if (!$id_rol) {
    $data = array();
    exit;
}
if (!$lista) {
    $data = array();
    exit;
}

$dataLista = dataLista($lista, $id_rol);
$dataLista = explode(',', $dataLista[0]);
$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

switch ($lista) {
    case 1:
        $query = "SELECT NovCodi, NovDesc, NovID, NovTipo FROM NOVEDAD WHERE NovCodi > 0";
        $rs = sqlsrv_query($link, $query, $params, $options);
        if (sqlsrv_num_rows($rs) > 0) {
            $countSet = count($dataLista);
            while ($r = sqlsrv_fetch_array($rs)) :
                foreach ($dataLista as $key => $value) {
                    $set = ($value == $r['NovCodi']) ? 1 : 0;
                    if ($set === 1) {
                        break;
                    }
                }
                $data[] = array(
                    'codigo'      => $r['NovCodi'],
                    'descripcion' => $r['NovDesc'],
                    'id'          => $r['NovID'],
                    'idtipo'      => ($r['NovTipo']),
                    'tipo'        => TipoNov($r['NovTipo']),
                    'set'         => ($set),
                );
            endwhile;
        }
        sqlsrv_free_stmt($rs);
        sqlsrv_close($link);
        break;
    case 2:
        $query = "SELECT ONovCodi, ONovDesc, ONovTipo FROM OTRASNOV WHERE ONovCodi > 0";
        $rs = sqlsrv_query($link, $query, $params, $options);
        if (sqlsrv_num_rows($rs) > 0) {
            $countSet = count($dataLista);
            while ($r = sqlsrv_fetch_array($rs)) :
                foreach ($dataLista as $key => $value) {
                    $set = ($value == $r['ONovCodi']) ? 1 : 0;
                    if ($set === 1) {
                        break;
                    }
                }
                $data[] = array(
                    'codigo'      => $r['ONovCodi'],
                    'descripcion' => $r['ONovDesc'],
                    'idtipo'      => $r['ONovTipo'],
                    'set'         => ($set),
                    'tipo'        => ($r['ONovTipo'] == 0) ? 'En Valor' : 'En Horas',
                );
            endwhile;
        }
        sqlsrv_free_stmt($rs);
        sqlsrv_close($link);
        break;
    case 3:
        $query = "SELECT HorCodi, HorDesc, HorID FROM HORARIOS";
        $rs = sqlsrv_query($link, $query, $params, $options);
        if (sqlsrv_num_rows($rs) > 0) {
            while ($r = sqlsrv_fetch_array($rs)) :
                foreach ($dataLista as $key => $value) {
                    if ($value != '-') {
                        $value = ($value == '32768') ? 0 : $value;
                        $set = (intval($value) === intval($r['HorCodi'])) ? 1 : 0;
                        if ($set === 1) {
                            break;
                        }
                    }
                }
                $data[] = array(
                    'codigo'      => $r['HorCodi'],
                    'descripcion' => $r['HorDesc'],
                    'id'          => $r['HorID'],
                    'set'         => ($set),
                );
            endwhile;
        }
        sqlsrv_free_stmt($rs);
        sqlsrv_close($link);
        break;
    case 4:
        $query = "SELECT RotCodi, RotDesc FROM ROTACION";
        $rs = sqlsrv_query($link, $query, $params, $options);
        if (sqlsrv_num_rows($rs) > 0) {
            $countSet = count($dataLista);
            while ($r = sqlsrv_fetch_array($rs)) :
                foreach ($dataLista as $key => $value) {
                    $set = ($value == $r['RotCodi']) ? 1 : 0;
                    if ($set === 1) {
                        break;
                    }
                }
                $data[] = array(
                    'codigo'      => $r['RotCodi'],
                    'descripcion' => $r['RotDesc'],
                    'set'         => ($set),
                );
            endwhile;
        }
        sqlsrv_free_stmt($rs);
        sqlsrv_close($link);
        break;
    case 5:
        $query = "SELECT THoCodi, THoDesc, THoID FROM TIPOHORA WHERE THoCodi > 0";
        $rs = sqlsrv_query($link, $query, $params, $options);
        if (sqlsrv_num_rows($rs) > 0) {
            $countSet = count($dataLista);
            while ($r = sqlsrv_fetch_array($rs)) :
                foreach ($dataLista as $key => $value) {
                    $set = ($value == $r['THoCodi']) ? 1 : 0;
                    if ($set === 1) {
                        break;
                    }
                }
                $data[] = array(
                    'codigo'      => $r['THoCodi'],
                    'descripcion' => $r['THoDesc'],
                    'id'          => $r['THoID'],
                    'set'         => ($set),
                );
            endwhile;
        }
        sqlsrv_free_stmt($rs);
        sqlsrv_close($link);
        break;
    case 10:
        require __DIR__ . '../../../config/conect_mysql.php';
        $stmt = mysqli_query($link, "SELECT r.id, r.nombre FROM roles r INNER JOIN clientes c ON r.cliente = c.id WHERE c.recid = '$recid_cliente' AND r.id != '$id_rol' AND r.id != 1");
        // print_r($query); exit;
        if (($stmt)) {
            if (mysqli_num_rows($stmt) > 0) {
                while ($row = mysqli_fetch_assoc($stmt)) {
                    $data[] = array(
                        'codigo'      => $row['id'],
                        'descripcion' => $row['nombre'],
                        'set'         => 0,
                    );
                }
            }
            mysqli_free_result($stmt);
            mysqli_close($link);
        } else {
            statusData('error', mysqli_error($link));
            mysqli_close($link);
            exit;
        }
        break;
}
$countSet = array_count_values(array_column($data, 'set'))[1] ?? 0;

$json_data = array(
    "countSet" => $countSet,
    "data" => $data
);

echo json_encode($json_data);
exit;
