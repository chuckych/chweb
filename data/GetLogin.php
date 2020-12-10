<?php
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME,"es_ES");

require __DIR__ . '../../funciones.php';
error_reporting(E_ALL);
ini_set('display_errors', '0');
UnsetGet('tk');
UnsetGet('q');
// UnsetGet('k');
$respuesta = '';
$respuesta = '';
$token = token();

    /** VALORES POR DEFECTO DE FECHA */
    
    if(!isset($_GET['Leg_Num']) or (empty($_GET['Leg_Num']))){
        $_GET['Leg_Num'] = '0';
    }

    $FechaIni=test_input($_GET['FechaIni']);
    $FechaFin=test_input($_GET['FechaFin']);

    $FechaPag= (isset($_GET['k'])) ? test_input($_GET['k']) : '';
    
    $FechaIni = ((isset($_GET['k']))) ? $FechaPag : $FechaIni;
        
if ($_GET['tk'] == $token) {
    if (isset($_GET['tk']) && ($_GET['tk'] == $token)) {
        
        require __DIR__ . '../../config/conect_mysql.php';

        
        $query="SELECT DISTINCT DATE_FORMAT(login_logs.fechahora, '%Y-%m-%d') AS Fecha
        FROM login_logs
        WHERE login_logs.id > '0' AND .login_logs.fechahora BETWEEN '$_GET[FechaIni]' AND '$_GET[FechaFin]'
        ORDER BY .login_logs.fechahora";

        $result = mysqli_query($link, $query); 
        // print_r($query);
        // exit;
        while ($row = mysqli_fetch_array($result)) :
            $IndiFecha[] = ($row['Fecha']->format('Y-m-d'));
        endwhile;
            $IndiFecha = (isset($IndiFecha)) ? $IndiFecha : array($_GET['FechaIni']);
        mysqli_free_result($result); /** LIBERAMOS MEMORIA */
        /** Query de primer registro de Fecha */
        $query="SELECT  
        MIN(DATE_FORMAT(login_logs.fechahora, '%Y-%m-%d')) AS 'min_Fecha',
        MAX(DATE_FORMAT(login_logs.fechahora, '%Y-%m-%d')) AS 'max_Fecha'
        FROM login_logs";
        $result = mysqli_query($link, $query); 
        // print_r($query);
        // exit;
        while ($row = mysqli_fetch_array($result)) :
            $firstDate = array(
                'firstDate'=> FechaFormatVar($row['min_Fecha'], "Y-m-d"),
                'firstYear' => FechaFormatVar($row['min_Fecha'], "Y")
            );
            $maxDate = array(
                'maxDate'=> FechaFormatVar($row['max_Fecha'], "Y-m-d"),
                'maxYear' => FechaFormatVar($row['max_Fecha'], "Y")
            );
        endwhile;
        mysqli_free_result($result); /** LIBERAMOS MEMORIA */

        $primero = (array_key_first($IndiFecha));
        $ultimo  = (array_key_last($IndiFecha));
        $primero = (array_values($IndiFecha)[$primero]);
        $ultimo  = (array_values($IndiFecha)[$ultimo]);
        // exit;
        $k        = (isset($_GET['k'])) ? $_GET['k'] :'0';
        $FechaPag = array_values($IndiFecha)[$k];
        $FechaIni = (isset($_GET['k'])) ? FechaString($FechaPag) : FechaString($primero);
        $FechaFin = FechaString($FechaFin);
        $fechahora = ($row['fechahora']);
        $query = "SELECT login_logs.id, login_logs.usuario, login_logs.uid, login_logs.fechahora,login_logs.estado, login_logs.ip, login_logs.agent, clientes.nombre as cliente, roles.nombre as rol
        FROM login_logs
        LEFT JOIN clientes ON login_logs.cliente = clientes.id
        LEFT JOIN roles ON login_logs.rol = roles.id
        WHERE DATE_FORMAT(login_logs.fechahora, '%Y-%m-%d') = '$FechaIni'
        ORDER BY .login_logs.fechahora desc";

        // print_r($query).PHP_EOL;
        // exit;

        $result = mysqli_query($link, $query);
        // $data  = array();
        /** BUSCAMOS DENTRO DE FICHAS EL LEGAJO NOMBRE FECHA DIA HORARIO */
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) :
                       $id        = $row['id'];
                       $usuario   = $row['usuario'];
                       $uid       = $row['uid'];
                       $cliente       = $row['cliente'];
                       $rol       = $row['rol'];
                       $fechahora = ($row['fechahora']);
                       $fecha     = FechaFormatVar($row['fechahora'], "d-m-Y");
                       $hora      = FechaFormatVar($row['fechahora'], "H:i");
                       $estado    = $row['estado'];
                       $ip        = $row['ip'];
                       $agent     = $row['agent'];

                $data[] = array(
                    'id'         => $id,
                    'usuario'    => $usuario,
                    'uid'        => $uid,
                    'cliente'        => $cliente,
                    'rol'        => $rol,
                    'fecha_hora' => $fechahora,
                    'fecha'      => $fecha,
                    'Hora'       => $hora,
                    'estado'     => $estado,
                    'ip'         => $ip,
                    'agent'      => $agent
                );
            endwhile;
            mysqli_free_result($result);
            mysqli_close($link);
            $respuesta = array('success' => 'YES', 'error' => '0', 'firstDate'=>$firstDate, 'maxDate'=>$maxDate, 'rango_fecha' => ($IndiFecha),'login' => ($data));
        } else {
            $respuesta = array('success' => 'NO', 'error' => true, 'firstDate'=>$firstDate, 'maxDate'=>$maxDate, 'rango_fecha' => ($IndiFecha), 'login' => 'No hay Datos');
        }
    } else {
        $respuesta = array('success' => 'NO', 'error' => true, 'login' => 'error');
    }
} else {
    $respuesta = array('success' => 'NO', 'error' => true, 'login' => 'ERROR TOKEN');
}
$datos = array($respuesta);
echo json_encode($datos);
// var_export($datos);
