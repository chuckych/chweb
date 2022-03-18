<?php
session_start();
require __DIR__ . '../../../../config/index.php';
secure_auth_ch();
header("Location:/".HOMEHOST."/mobile/hrp");