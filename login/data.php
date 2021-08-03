<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME,"es_ES");
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
// require __DIR__ . '../../config/index.php';
require __DIR__ . '../../config/conect_mysql.php';
// require __DIR__ . '../../funciones.php';
E_ALL();

$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
// $columns = array(
//     0 => 'id',
//     1 => 'usuario', 
//     2 => 'nombre', 
//     3 => 'fechahora',
//     4 => 'estado',
//     5 => 'rol',
//     6 => 'cliente',
//     7 => 'ip',
//     8 => 'agent'
// );
$where_condition = $sqlTot = $sqlRec = "";
if( !empty($params['search']['value']) ) {
$where_condition .=	" WHERE ";
$where_condition .= " (usuarios.nombre LIKE '%".$params['search']['value']."%' ";
$where_condition .= " OR login_logs.usuario LIKE '%".$params['search']['value']."%' ";
$where_condition .= " OR INET_NTOA(login_logs.ip) LIKE '%".$params['search']['value']."%' ";
$where_condition .= " OR clientes.nombre LIKE '%".$params['search']['value']."%')";
}
$sql_query = "SELECT login_logs.id as id, 
login_logs.usuario as usuario, 
usuarios.nombre as nombre, 
(login_logs.fechahora) AS fechahora, 
login_logs.estado as estado,
roles.nombre as rol,
clientes.nombre as cliente, 
INET_NTOA(login_logs.ip) as ip, 
login_logs.agent as agent 
FROM login_logs
LEFT JOIN clientes ON login_logs.cliente = clientes.id
LEFT JOIN usuarios ON login_logs.uid = usuarios.id
LEFT JOIN roles ON login_logs.rol = roles.id ";
$sqlTot .= $sql_query;
$sqlRec .= $sql_query;
if(isset($where_condition) && $where_condition != '') {
$sqlTot .= $where_condition;
$sqlRec .= $where_condition;
}
// $sqlRec .=  "ORDER BY login_logs.id desc";
$sqlRec .=  "ORDER BY login_logs.id desc LIMIT ".$params['start']." ,".$params['length']." ";
$queryTot = mysqli_query($link, $sqlTot) or die("Database Error:". mysqli_error($link));
$totalRecords = mysqli_num_rows($queryTot);
$queryRecords = mysqli_query($link, $sqlRec);
// print_r($sqlRec); exit;
while( $row = mysqli_fetch_assoc($queryRecords) ) {

    $id        = $row['id'];
    $usuario   = $row['usuario'];
    $nombre    = $row['nombre'];
    $fechahora = Fech_Format_Var($row['fechahora'], 'd-m-Y H:i:s');
    $estado    = $row['estado'];
    $rol       = $row['rol'];
    $cliente   = $row['cliente'];
    $ip        = $row['ip'];
    $agent     = $row['agent'];
    switch ($estado) {
        case 'correcto':
            $estado='<span class="text-success">'.ucfirst($estado).'</span>';
            break;
        
        default:
            $estado='<span class="text-danger">'.ucfirst($estado).'</span>';
            break;
    }

$data[] = array(
    'id'        => $id,
    'usuario'   => $usuario,
    'nombre'    => $nombre,
    'fechahora' => $fechahora,
    'estado'    => $estado,
    'rol'       => $rol,
    'cliente'   => $cliente,
    'ip'        => $ip,
    'agent'     => $agent
);
}
mysqli_free_result($queryRecords);
$json_data = array(
"draw"            => intval( $params['draw'] ),   
"recordsTotal"    => intval( $totalRecords ),  
"recordsFiltered" => intval($totalRecords),
"data"            => $data
);
echo json_encode($json_data);
