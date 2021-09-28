<?php
require __DIR__ . '/config/index.php';
session_start();
header("Content-Type: application/json");
ultimoacc();
if (!secure_auth_ch_json()) {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $_GET['status'] = $_GET['status'] ?? '';
        $status = $_GET['status'];
        ($status == 'ws') ? pingWebService('No hay conexion con WebService CH'):'';
    }
}
E_ALL();
exit;