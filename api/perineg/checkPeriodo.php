<?php
/** Validamos que si la fecha de FeIn sea mayor a otra fecha de ingreso encontrada */
$q = "SELECT * FROM PERINEG WHERE InEgLega = $dp[Lega]";
$s = $dbApiQuery($q);

if ($method == 'PUT') {
    $q = "SELECT * FROM PERINEG WHERE InEgLega = $dp[Lega] AND InEgFeIn = '$dp[FeIn]'";
    $s1 = $dbApiQuery($q);
    if (empty($s1)) {
        http_response_code(200);
        (response("No existe el registro", 0, 'ERROR', 200, $time_start, 0, $idCompany));
        exit;
    }
}

if ($s) {
    foreach ($s as $key => $v) {

        $InEgLega  = intval($v['InEgLega']);
        $InEgFeIn  = fechFormat($v['InEgFeIn'], 'Ymd');
        $InEgFeIn2 = fechFormat($v['InEgFeIn'], 'd/m/Y');
        $InEgFeEg  = fechFormat($v['InEgFeEg'], 'Ymd');
        $InEgFeEg2 = fechFormat($v['InEgFeEg'], 'd/m/Y');
        $FeIn2     = fechFormat($dp['FeIn'], 'd/m/Y');
        $FeEg2     = fechFormat($dp['FeEg'], 'd/m/Y');      

        if ($method == 'POST') {

            if ($InEgLega == intval($dp['Lega']) && $InEgFeIn == $dp['FeIn']) {
                http_response_code(200);
                (response("Ya existe el registro Lega: $dp[Lega] y FeIn : $FeIn2", 0, 'ERROR', 200, $time_start, 0, $idCompany));
                exit;
            }

        }

        if ($method == 'POST') {
            if (
                $InEgLega == intval($dp['Lega']) 
                && $dp['FeIn'] >= $InEgFeIn
                && $dp['FeIn'] < $InEgFeEg  
                ) 
            {
                http_response_code(200);
                (response("Existe un periodo de $InEgFeIn2 a $InEgFeEg2", 0, 'ERROR', 200, $time_start, 0, $idCompany));
                exit;
            }
        }

        if ($method == 'POST') {
            if (
                $InEgLega == intval($dp['Lega']) 
                && $dp['FeIn'] <= $InEgFeIn
                && $dp['FeIn'] <= $InEgFeEg  
                && ($dp['FeEg'] == '17530101')
                ) 
            {
                http_response_code(200);
                (response("Existe un periodo cerrado posterior a $FeIn2. Debe indicar fecha de egreso menor a $InEgFeIn2", 0, 'ERROR', 200, $time_start, 0, $idCompany));
                exit;
            }
        }
        if (
            $InEgLega == intval($dp['Lega']) 
            && ($dp['FeEg'] == '17530101')
            ) 
        {
            http_response_code(200);
            (response("Existen periodos sin si fecha de egreso.", 0, 'ERROR', 200, $time_start, 0, $idCompany));
            exit;
        }
    }
}
