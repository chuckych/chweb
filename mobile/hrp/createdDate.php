<?php
// header json
header('Content-Type: application/json');
$createdDate = file_get_contents('createdDate.json', true);
echo (intval($createdDate));
