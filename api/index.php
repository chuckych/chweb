<?php
require __DIR__ . '/fn.php';
http_response_code(404);
(response([], 0, 'Not Found', 404, $time_start, 0, $idCompany));
exit;
