<?php
$_SERVER["HTTPS"] ??= "off";
switch ($_SERVER["HTTPS"]) {
    case "on":
        session_set_cookie_params([
            'samesite' => 'None',
            'secure' => true,
            'httponly' => true
        ]);
        break;
    default:
        session_set_cookie_params([
            'samesite' => 'Lax',
            'httponly' => true
        ]);
}
session_start();