<?php
require __DIR__ . '../../../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();
$_POST['i'] = $_POST['i'] ?? '';
$_POST['s'] = $_POST['s'] ?? '';
$id = intval(test_input($_POST['i']));
$ids = intval(test_input($_POST['s']));

if (empty($id) || empty($ids)) {
    $json_data = array(
        "data" => false,
    );
    echo json_encode($json_data);
    exit;
}

require_once __DIR__ . '../../PhpUserAgent/src/UserAgentParser.php';

$query = "SELECT login_logs.id 'log_idse', auditoria.nombre AS 'aud_nomb', auditoria.usuario AS 'aud_user', login_logs.uid AS 'log_usid', login_logs.fechahora AS 'log_feho', login_logs.rol AS 'log_irol', login_logs.ip AS 'log_d_ip', login_logs.agent AS 'log_agen', auditoria.dato AS 'aud_dato', auditoria.tipo AS 'aud_tipo', auditoria.modulo AS 'aud_imod', modulos.nombre AS 'aud_modu', auditoria.fecha AS 'aud_fech', auditoria.hora AS 'aud_hora', auditoria.audcuenta AS 'aud_iacu', clientes.nombre AS 'aud_nacu', roles.nombre AS 'log_nrol' FROM auditoria INNER JOIN login_logs ON auditoria.id_sesion=login_logs.id LEFT JOIN modulos ON auditoria.modulo=modulos.id LEFT JOIN clientes ON auditoria.audcuenta=clientes.id LEFT JOIN roles ON login_logs.rol=roles.id WHERE auditoria.id_sesion=$ids AND auditoria.id=$id LIMIT 1";

$d = simple_pdoQuery($query);

$parsedagent[] = parse_user_agent($d['log_agen']);
foreach ($parsedagent as $key => $value) {
    $platform = $value['platform'];
    $browser = $value['browser'];
    $version = $value['version'];
}

$json_data = array(
    "aud_dato" => $d['aud_dato'],
    "aud_fech" => fechFormat($d['aud_fech']),
    "aud_hora" => $d['aud_hora'],
    "aud_iacu" => $d['aud_iacu'],
    "aud_imod" => $d['aud_imod'],
    "aud_modu" => $d['aud_modu'],
    "aud_nacu" => $d['aud_nacu'],
    "aud_nomb" => $d['aud_nomb'],
    "aud_tipn" => tipoAud($d['aud_tipo']),
    "aud_tipo" => $d['aud_tipo'],
    "aud_user" => $d['aud_user'],
    "log_age1" => $platform,
    "log_age2" => $browser,
    "log_age3" => $version,
    "log_agen" => $d['log_agen'],
    "log_d_ip" => long2ip($d['log_d_ip']),
    "log_feho" => FechaFormatVar($d['log_feho'], 'd/m/Y H:i:s'),
    "log_fech" => FechaFormatVar($d['log_feho'], 'd/m/Y'),
    "log_hora" => FechaFormatVar($d['log_feho'], 'H:i:s'),
    "log_idse" => $d['log_idse'],
    "log_irol" => $d['log_irol'],
    "log_nrol" => $d['log_nrol'],
    "log_usid" => $d['log_usid'],
    "data" => true,
);
echo json_encode($json_data);
