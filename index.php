<?php
require __DIR__ . '/config/session_start.php';
require __DIR__ . '/config/index.php';
secure_auth_ch();
header("Location:/" . HOMEHOST . "/inicio/");