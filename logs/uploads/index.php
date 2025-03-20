<?php
// Página de protección para el directorio de logs
// Redirecciona a cualquier intento de acceso directo

header('HTTP/1.1 403 Forbidden');
header('Location: /');
exit('Acceso denegado');
