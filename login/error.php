<?php
require __DIR__ .'../../config/index.php';
$Modulo = '';
require __DIR__ .'../../llamadas.php';
$ErrConBDHTML = "<html><body style='background:#333'><h1 style='text-align:center; padding:20px;color:#fff; font-family: Arial'>No hay conexión.</h1><div class='d-flex justify-content-center'>
<a class='btn btn-light btn-lg mt-2' href='index.php'>Ir al Login</a>
</body></html>";
echo $ErrConBDHTML;