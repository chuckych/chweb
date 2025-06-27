<?php
require __DIR__ . '/../../config/session_start.php';
require __DIR__ . '/../../config/index.php';
secure_auth_ch();
$Modulo = '19';
ExisteModRol($Modulo);
// if (($_SESSION['DBDATA'] ?? 0) >= 7120250528) {
//     require pagina('horarios.php');
// } else {
//     require pagina('invalid_db.php');
// }
require pagina('horarios.php');
