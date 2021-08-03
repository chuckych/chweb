<?php
session_start();
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');

E_ALL();

require __DIR__ . '../../../config/conect_mysql.php';
// sleep(2);
    $query = "SELECT modulos.id AS 'idmodulo', modulos.nombre AS 'modulo', modulos.orden AS 'orden', modulos.estado AS 'estado', modulos.idtipo AS 'idtipo', tipo_modulo.descripcion AS 'tipo' FROM modulos 
    INNER JOIN tipo_modulo ON modulos.idtipo = tipo_modulo.id ORDER BY tipo_modulo.id";
    // h1($query);exit;
    $result = mysqli_query($link, $query);
    $data  = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) :
        $idmodulo   = $row['idmodulo'];
        $modulo     = $row['modulo'];
        $orden      = $row['orden'];
        $estado     = $row['estado'];
        $estadodesc = ($row['estado']=='1') ? 'Inactivo':'Activo';
        $idtipo     = $row['idtipo'];
        $tipo       = $row['tipo'];
        $iconEdit = '<button title="Editar" class="btn btn-sm btn-outline-custom edit border-0" data="'.$idmodulo .'" data1="'.$modulo .'" data2="'.$orden .'" data3="'.$estado .'" data4="'.$idtipo .'" data5="'.$tipo .'">
        <svg class="bi" width="12" height="12" fill="currentColor">
        <use xlink:href="../../img/bootstrap-icons.svg#pen"/>
        </svg></button>';
            $data[] = array(
                'idmodulo'   => $idmodulo,
                'modulo'     => $modulo,
                'orden'      => $orden,
                'estado'     => $estado,
                'estadodesc' => $estadodesc,
                'idtipo'     => $idtipo,
                'tipo'       => $tipo,
                'iconEdit'   => $iconEdit,
            );
        endwhile;
    }
    mysqli_free_result($result);
    mysqli_close($link);

    foreach ($data as $key => $value) {
        if ($value['tipo'] != ($data[$key - 1]['tipo'])) {
            $data2[] = array(
                'id'   => $value['idtipo'],
                'tipo' => $value['tipo']
            );
        } else {
            $data2[] = array();
        }
    }

    $respuesta = array('modulos' => $data, 'UniqueMod' => $data2);
    echo json_encode($respuesta); 
