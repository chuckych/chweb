<?php
if ($_SERVER["HTTPS"] == "on") {
    session_set_cookie_params([
        'samesite' => 'None',
        'secure' => true, // Asegúrate de usar solo HTTPS para este valor
        'httponly' => true // Esto evita que la cookie sea accesible a través de JavaScript
    ]);
} else {
    session_set_cookie_params([
        'samesite' => 'Lax',
        'httponly' => true // Esto evita que la cookie sea accesible a través de JavaScript
    ]);
}
session_start();
