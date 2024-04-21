<?php
header("Content-Type: application/json");
secure_auth_ch_json();
E_ALL();

$query = "SELECT usuarios.legajo AS 'legajo' FROM usuarios LEFT JOIN roles ON usuarios.rol=roles.id INNER JOIN clientes ON usuarios.cliente=clientes.id WHERE usuarios.id>'0' AND clientes.recid='$_GET[_c]' AND usuarios.legajo > 0 ORDER BY usuarios.estado, usuarios.fecha DESC";

$data  = array();
$respuesta  = array();

$arrLega = array_pdoQuery($query);

require __DIR__ . '../../../config/conect_mssql.php';

$queryPersonal  = "SELECT PERSONAL.LegNume AS 'pers_legajo', PERSONAL.LegApNo AS 'pers_nombre', PERSONAL.LegDocu AS 'pers_dni', PERSONAL.LegSect AS 'pers_LegSect', EMPRESAS.EmpRazon AS 'pers_empresa', PLANTAS.PlaDesc AS 'pers_planta', CONVENIO.ConDesc AS 'pers_convenio', SECTORES.SecDesc AS 'pers_sector', GRUPOS.GruDesc AS 'pers_grupo', SUCURSALES.SucDesc AS 'pers_sucur', PERSONAL.LegMail AS 'pers_mail', PERSONAL.LegDomi AS 'pers_domic', PERSONAL.LegDoNu AS 'pers_numero', PERSONAL.LegDoOb AS 'pers_observ', PERSONAL.LegDoPi AS 'pers_piso', PERSONAL.LegDoDP AS 'pers_depto', LOCALIDA.LocDesc AS 'pers_localidad', PERSONAL.LegCOPO AS 'pers_cp', PROVINCI.ProDesc AS 'pers_prov', NACIONES.NacDesc AS 'pers_nacion', (CASE PERSONAL.LegFeEg WHEN '1753-01-01 00:00:00.000' THEN '0' ELSE '1' END) AS 'pers_estado' FROM PERSONAL INNER JOIN PLANTAS ON PERSONAL.LegPlan=PLANTAS.PlaCodi INNER JOIN SECTORES ON PERSONAL.LegSect=SECTORES.SecCodi INNER JOIN SECCION ON PERSONAL.LegSec2=SECCION.Se2Codi AND SECTORES.SecCodi=SECCION.SecCodi INNER JOIN EMPRESAS ON PERSONAL.LegEmpr=EMPRESAS.EmpCodi INNER JOIN CONVENIO ON PERSONAL.LegConv=CONVENIO.ConCodi INNER JOIN GRUPOS ON PERSONAL.LegGrup=GRUPOS.GruCodi INNER JOIN SUCURSALES ON PERSONAL.LegSucu=SUCURSALES.SucCodi INNER JOIN PROVINCI ON PERSONAL.LegProv=PROVINCI.ProCodi INNER JOIN LOCALIDA ON PERSONAL.LegLoca=LOCALIDA.LocCodi INNER JOIN NACIONES ON PERSONAL.LegNaci=NACIONES.NacCodi WHERE PERSONAL.LegNume >'0' AND PERSONAL.LegFeEg='1753-01-01 00:00:00.000' ORDER BY pers_legajo";

function filtrar($array, $key, $valor) // Función para filtrar un objeto
{
    $r = array_filter($array, function ($e) use ($key, $valor) {
        return $e[$key] == $valor;
    });
    foreach ($r as $key => $value) {
        return ($value);
    }
}

$params    = array();
$options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$stmt  = sqlsrv_query($link, $queryPersonal, $params, $options);
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt,  SQLSRV_FETCH_ASSOC)) {
        $filtro = filtrar($arrLega, 'legajo', $row['pers_legajo']) ?? '';

        if (!isset($filtro['legajo'])) {
            $pers_legajo    = $row['pers_legajo'];
            $pers_nombre    = $row['pers_nombre'];
            $pers_dni       = $row['pers_dni'];
            $pers_empresa   = $row['pers_empresa'];
            $pers_planta    = $row['pers_planta'];
            $pers_convenio  = $row['pers_convenio'];
            $pers_sector    = $row['pers_sector'];
            $pers_LegSect   = $row['pers_LegSect'];
            $pers_grupo     = $row['pers_grupo'];
            $pers_sucur     = $row['pers_sucur'];
            $pers_estado    = $row['pers_estado'];
            $pers_estado    = ($pers_estado == '1') ? 'Inactivo' : 'Activo';
            $pers_mail      = $row['pers_mail'];
            $pers_domic     = $row['pers_domic'];
            $pers_numero    = $row['pers_numero'];
            $pers_observ    = $row['pers_observ'];
            $pers_piso      = $row['pers_piso'];
            $pers_depto     = $row['pers_depto'];
            $pers_localidad = $row['pers_localidad'];
            $pers_cp        = $row['pers_cp'];
            $pers_prov      = $row['pers_prov'];
            $pers_nacion    = $row['pers_nacion'];

            $data[] = array(
                'pers_legajo'    => $pers_legajo,
                'pers_nombre'    => $pers_nombre,
                'pers_dni'       => $pers_dni,
                'pers_empresa'   => $pers_empresa,
                'pers_planta'    => $pers_planta,
                'pers_convenio'  => $pers_convenio,
                'pers_sector'    => $pers_sector,
                'pers_grupo'     => $pers_grupo,
                'pers_sucur'     => $pers_sucur,
                'pers_estado'    => $pers_estado,
                'pers_mail'      => $pers_mail,
                'pers_domic'     => $pers_domic,
                'pers_numero'    => $pers_numero,
                'pers_observ'    => $pers_observ,
                'pers_piso'      => $pers_piso,
                'pers_depto'     => $pers_depto,
                'pers_localidad' => $pers_localidad,
                'pers_cp'        => $pers_cp,
                'pers_prov'      => $pers_prov,
                'pers_nacion'    => $pers_nacion
            );
        }
    }
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($link);
} else {
    if (($errors = sqlsrv_errors()) != null) {
        foreach ($errors as $error) {
            $mensaje = explode(']', $error['message']);
        }
    }
    $pathLog = __DIR__ . '../../../logs/error/' . date('Ymd') . '_errorQuery.log';
    fileLog($_SERVER['REQUEST_URI'] . "\n" . $mensaje, $pathLog); // escribir en el log
}
$_c = $_GET['_c'];

foreach ($data as $value) {

    $pers_legajo  = $value['pers_legajo'];
    $pers_nombre  = $value['pers_nombre'];
    $pers_empresa = $value['pers_empresa'];
    $pers_planta  = $value['pers_planta'];
    $pers_sector  = $value['pers_sector'];
    $pers_grupo   = $value['pers_grupo'];
    $pers_sucur   = $value['pers_sucur'];
    $pers_dni     = $value['pers_dni'];
    if (test_input($_GET['LegaPass']) == 'true') {
        if ($pers_dni > '0') {
            $check = "<div style='margin-top:10px;' class='custom-control custom-checkbox custom-control-inline'>
            <input type='checkbox' name='_l[]' class='LegaCheck custom-control-input' id='$pers_legajo' value='$pers_legajo'>
            <label class='custom-control-label ml-2' for='$pers_legajo'>
            <p class='fontq' style='margin-top:3px;'>$pers_legajo</p>
            </label>
            </div>";
        } else {
            $check = "<div style='margin-top:10px;' class='custom-control custom-checkbox custom-control-inline'>
            <input type='text' disabled class='custom-control-input' value='$pers_legajo'>
            <span class='custom-control-label ml-2'>
            <p class='fontq' style='margin-top:3px;'>$pers_legajo</p>
            </span>
            </div>";
        }
    } else {
        $check = "<div style='margin-top:10px;' class='custom-control custom-checkbox custom-control-inline'>
            <input type='checkbox' name='_l[]' class='LegaCheck custom-control-input' id='$pers_legajo' value='$pers_legajo'>
            <label class='custom-control-label ml-2' for='$pers_legajo'>
            <p class='fontq' style='margin-top:3px;'>$pers_legajo</p>
            </label>
            </div>";
    }


    $respuesta[] = array(
        'check'        => '<div>' . $check . '</div>',
        'pers_legajo'  => '<div>' . $pers_legajo . '</div>',
        'pers_dni'     => '<div>' . ceronull($pers_dni) . '</div>',
        'pers_nombre'  => '<div>' . ceronull($pers_nombre) . '</div>',
        'pers_empresa' => '<div>' . ceronull($pers_empresa) . '</div>',
        'pers_planta'  => '<div>' . ceronull($pers_planta) . '</div>',
        'pers_sector'  => '<div>' . ceronull($pers_sector) . '</div>',
        'pers_grupo'   => '<div>' . ceronull($pers_grupo) . '</div>',
        'pers_sucur'   => '<div>' . ceronull($pers_sucur . '</div>')
    );
    // }
}
$respuesta = array('personal' => $respuesta);
echo json_encode($respuesta);
