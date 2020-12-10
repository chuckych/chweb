<?php
echo '<h1>'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'</h1>';
header('Location:/'.HOMEHOST.'/usuarios/clientes/');