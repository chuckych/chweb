<?php
session_start();
require __DIR__ . '../../config/index.php';
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");
secure_auth_ch_json();

E_ALL();

require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';

FusNuloPOST('Excel', false);
$Excel = $_POST['Excel'];
$params = $_REQUEST;
$data = array();
if (isset($_POST['_l']) && !empty($_POST['_l'])) {
    $legajo = test_input(FusNuloPOST('_l', 'vacio'));
}else{
    $json_data = array(
        "draw"            => intval($params['draw']),
        "recordsTotal"    => 0,
        "recordsFiltered" => 0,
        "data"            => $data
    );
    echo json_encode($json_data);
    exit;
}
$_POST['_f'] = $_POST['_f'] ?? '';

if (empty($_POST['_f'])) {
    $FechaMinMax = (fecha_min_max('FICHAS', 'FICHAS.FicFech'));
    $Fecha = FechaString($FechaMinMax['max']);
}else{
    $Fecha = FechaString(test_input($_POST['_f']));
}
require __DIR__ . '../valores.php';

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$params = $columns = $totalRecords ='';
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

$sql_query = "SELECT FICHAS.FicLega AS 'Gen_Lega', dbo.fn_DiaDeLaSemana(FICHAS.FicFech) AS 'Gen_dia', PERSONAL.LegApNo AS 'Gen_Nombre', FICHAS.FicFech AS 'Gen_Fecha', DATEPART(dw, .FICHAS.FicFech) AS 'Gen_Dia_Semana', dbo.fn_HorarioAsignado(FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicDiaL, FICHAS.FicDiaF) AS 'Gen_Horario' FROM FICHAS INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume WHERE FICHAS.FicFech='$Fecha' $FilterEstruct $FiltrosFichas";
// print_r($sql_query); exit;

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (!empty($params['search']['value'])) {
    $where_condition .=    " AND ";
    $where_condition .= " (CONCAT(PERSONAL.LegNume,PERSONAL.LegApNo) LIKE '%" . $params['search']['value'] . "%') ";
}

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}
if ($Excel) {
    $sqlRec .=  " ORDER BY .FICHAS.FicFech, FICHAS.FicLega";
} else {
    $sqlRec .=  " ORDER BY .FICHAS.FicFech, FICHAS.FicLega OFFSET " . $params['start'] . " ROWS FETCH NEXT " . $params['length'] . " ROWS ONLY";
}
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);
$queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);
// print_r($sqlRec); exit;

/** BUSCAMOS DENTRO DE FICHAS EL LEGAJO NOMBRE FECHA DIA HORARIO */
while ($row = sqlsrv_fetch_array($queryRecords)) :
    $Gen_Lega       = $row['Gen_Lega'];
    $Gen_Nombre     = $row['Gen_Nombre'];
    $Gen_Fecha      = $row['Gen_Fecha']->format('d/m/Y');
    $Gen_Fecha2     = $row['Gen_Fecha']->format('Ymd');
    $Gen_Fecha3     = $row['Gen_Fecha']->format('Y-m-d');
    $Gen_Dia_Semana = $row['Gen_dia'];
    $Gen_Dia_Semana2 = ($row['Gen_dia']);
    $Gen_Horario    = $row['Gen_Horario'];

    /** FICHADAS */
    if ($Gen_Fecha2 < '20210319') {
        $query_Fic = "SELECT REGISTRO.RegHoRe AS Fic_Hora, Fic_Tipo=CASE REGISTRO.RegTipo WHEN 0 THEN 'Capturador' ELSE 'Manual' END, Fic_Estado=CASE REGISTRO.RegFech WHEN REGISTRO.RegFeRe THEN CASE REGISTRO.RegHora WHEN REGISTRO.RegHoRe THEN 'Normal' ELSE 'Modificada' END ELSE 'Modificada' END FROM REGISTRO WHERE REGISTRO.RegFeAs='$Gen_Fecha2' AND REGISTRO.RegLega='$Gen_Lega' ORDER BY REGISTRO.RegFeAs,REGISTRO.RegLega,REGISTRO.RegFeRe,REGISTRO.RegHoRe";
    } else {
        $query_Fic = "SELECT REGISTRO.RegHoRe AS Fic_Hora, Fic_Tipo=CASE REGISTRO.RegTipo WHEN 0 THEN 'Capturador' ELSE 'Capturador' END, Fic_Estado=CASE REGISTRO.RegFech WHEN REGISTRO.RegFeRe THEN CASE REGISTRO.RegHora WHEN REGISTRO.RegHoRe THEN 'Normal' ELSE 'Modificada' END ELSE 'Modificada' END FROM REGISTRO WHERE REGISTRO.RegFeAs='$Gen_Fecha2' AND REGISTRO.RegLega='$Gen_Lega' ORDER BY REGISTRO.RegFeAs,REGISTRO.RegLega,REGISTRO.RegFeRe,REGISTRO.RegHoRe";
    }

    $result_Fic = sqlsrv_query($link, $query_Fic, $param, $options);
    // print_r($query_Fic).PHP_EOL; exit;
    if (sqlsrv_num_rows($result_Fic) > 0) {
        while ($row_Fic = sqlsrv_fetch_array($result_Fic)) :
            $Fic_Hora[] = array(
                'Fic'    => $row_Fic['Fic_Hora'],
                'Estado' => $row_Fic['Fic_Estado'],
                'Tipo'   => $row_Fic['Fic_Tipo']
            );
        endwhile;
        sqlsrv_free_stmt($result_Fic);
        $primero = (array_key_first($Fic_Hora));
        $ultimo  = (array_key_last($Fic_Hora));
        $primero = (array_values($Fic_Hora)[$primero]);
        $ultimo  = (array_values($Fic_Hora)[$ultimo]);
        $ultimo = ($ultimo == $primero) ? array('Fic' => "", 'Estado' => "", 'Tipo' => "") : $ultimo;
    } else {
        $Fic_Hora[] = array('Fic' => "", 'Estado' => "", 'Tipo' => "");
        $primero  = array('Fic' => "", 'Estado' => "", 'Tipo' => "");
        $ultimo   = array('Fic' => "", 'Estado' => "", 'Tipo' => "");
    }
    // if (is_array($Fic_Hora)) {
    //     foreach ($Fic_Hora as $fila) {
    //         // $Fici[] = ("<span class='ls1 mr-1 border-left px-1'>" . ($fila["Fic"]) . "</span>");
    //         $Fici[] = "<tr class='py-2'><td class='px-2'>" . ceronull($fila["Fic"]) . "</td><td class='px-2'>" . ceronull($fila["Estado"]) . "</td><td class='px-2'>" . ceronull($fila["Tipo"]) . "</td><td class='w-100'></td></tr>";
    //     }

    //     $Fichadas = implode("", $Fici);
    //     unset($Fici);
    //     // var_export($Fichadas); 
    // } else {
    //     $Fichadas = "No hay Fichadas";
    // }
    /** FIN FICHADAS */

    /** NOVEDADES */
    $query_Nov = "SELECT FICHAS3.FicNove AS nov_novedad, NOVEDAD.NovDesc AS nov_descripcion, NOVEDAD.NovTipo AS nov_tipo, FICHAS3.FicHoras AS nov_horas FROM FICHAS3,NOVEDAD WHERE FICHAS3.FicLega='$Gen_Lega' AND FICHAS3.FicFech='$Gen_Fecha2' AND FICHAS3.FicNove=NOVEDAD.NovCodi AND FICHAS3.FicNove >0 AND FICHAS3.FicNoTi >=0 ORDER BY FICHAS3.FICFech";
    $result_Nov = sqlsrv_query($link, $query_Nov, $param, $options);

    if (sqlsrv_num_rows($result_Nov) > 0) {
        while ($row_Nov = sqlsrv_fetch_array($result_Nov)) :
            $Novedad[] = array(
                'Cod'         => $row_Nov['nov_novedad'],
                'Descripcion' => $row_Nov['nov_descripcion'],
                'Horas'       => $row_Nov['nov_horas'],
                'Tipo'        => $row_Nov['nov_tipo']
            );
        endwhile;
        sqlsrv_free_stmt($result_Nov);
    } else {
        $Novedad[] = array(
            'Cod'         => "",
            'Descripcion' => "",
            'Horas'       => "",
            'Tipo'        => ""
        );
    }
    if (is_array($Novedad)) {
        foreach ($Novedad as $fila) {
            $desc[] = "<tr class='py-2'><td class='px-2'>" . ceronull($fila["Cod"]) . "</td><td class='px-2'>" . ceronull($fila["Descripcion"]) . "</td><td class='px-2'>" . ceronull($fila["Horas"]) . "</td><td class='px-2'>" . ceronull(TipoNov($fila["Tipo"])) . "</td><td class='w-100'></td></tr>";
            $desc2[] = '<span title="(' . $fila['Cod'] . ') ' . $fila['Descripcion'] . ' ' . $fila["Horas"] . 'hs.">' . ($fila["Descripcion"]) . '</span>';
            $desc3[] = ($fila["Horas"]);
        }

        $Novedades  = implode("", $desc);
        $Novedades2 = implode("<br/>", $desc2);
        $NoveHoras  = implode("<br/>", $desc3);
        unset($desc);
        unset($desc2);
        unset($desc3);
        // var_export($Novedades); 
    }
    /** FIN NOVEDADES */

    /** HORAS */
    $query_Horas="SELECT FICHAS1.FicHora AS Hora, TIPOHORA.THoDesc AS HoraDesc, TIPOHORA.THoDesc2 AS HoraDesc2, FICHAS1.FicHsHe AS HsHechas, FICHAS1.FicHsAu AS HsCalculadas, FICHAS1.FicHsAu2 AS HsAutorizadas FROM FICHAS1 INNER JOIN TIPOHORA ON FICHAS1.FicHora=TIPOHORA.THoCodi LEFT JOIN TIPOHORACAUSA ON FICHAS1.FicHora=TIPOHORACAUSA.THoCHora AND FICHAS1.FicCaus=TIPOHORACAUSA.THoCCodi WHERE FICHAS1.FicLega='$Gen_Lega' AND FICHAS1.FicFech='$Gen_Fecha2' AND TIPOHORA.THoColu >0 ORDER BY TIPOHORA.THoColu, FICHAS1.FicLega,FICHAS1.FicFech,FICHAS1.FicTurn, FICHAS1.FicHora";
    $result_Hor = sqlsrv_query($link, $query_Horas, $param, $options);
    // print_r($query_Horas);
    // exit;

    if (sqlsrv_num_rows($result_Hor) > 0) {
        while ($row_Hor = sqlsrv_fetch_array($result_Hor)) :
            $Horas[] = array(
                'Cod'          => $row_Hor['Hora'],
                'Descripcion'  => $row_Hor['HoraDesc'],
                'Descripcion2' => $row_Hor['HoraDesc2'],
                'HsHechas'     => $row_Hor['HsHechas'],
                'HsCalc'       => $row_Hor['HsCalculadas'],
                'HsAuto'       => $row_Hor['HsAutorizadas']
            );
        endwhile;
        sqlsrv_free_stmt($result_Hor);
    } else {
        $Horas[] = array(
            'Cod'          => '',
            'Descripcion'  => '',
            'Descripcion2' => '',
            'HsHechas'     => '',
            'HsCalc'       => '',
            'HsAuto'       => ''
        );
    }
    if (is_array($Horas)) {
        foreach ($Horas as $fila) {
            $hor[] = "<tr class='py-2'><td class='px-2'>" . ceronull($fila["Cod"]) . "</td><td class='px-2'>" . ceronull($fila["Descripcion"]) . "</td><td class='px-2 text-center bg-light fw4'>" . ceronull($fila["HsAuto"]) . "</td><td class='px-2 text-center'>" . ceronull($fila["HsCalc"]) . "</td><td class='px-2 text-center'>" . ceronull($fila["HsHechas"]) . "</td><td class='w-100'></td></tr>";
            // $hor2[] = $fila["Descripcion"];
            $hor2[] = '<span title="(' . $fila['Cod'] . ') ' . $fila['Descripcion'] . ' ' . ceronull($fila["HsAuto"]) . 'hs.">' . ($fila["Descripcion"]) . '</span>';
            $hor6[] = '<span title="(' . $fila['Cod'] . ') ' . $fila['Descripcion'] . ' ' . ceronull($fila["HsAuto"]) . 'hs.">' . ($fila["Descripcion2"]) . '</span>';
            $HsHechas[] = ceronull($fila["HsHechas"]);
            $HsCalc[]   = ceronull($fila["HsCalc"]);
            $HsAuto[]   = ceronull($fila["HsAuto"]);
        }

        $horas  = implode("", $hor);
        $horas2 = implode("<br/>", $hor2);
        /** Descripcion 1 del tipo de hora */
        $horas6 = implode("<br/>", $hor6);
        /** Descripcion 2 del tipo de hora */
        $horas3 = implode("<br/>", $HsHechas);
        $horas4 = implode("<br/>", $HsCalc);
        $horas5 = implode("<br/>", $HsAuto);
        unset($hor);
        unset($hor2);
        unset($hor6);
        unset($HsHechas);
        unset($HsCalc);
        unset($HsAuto);
        // var_export($Novedades); 
    }
    /** Fin HORAS */

    $entrada = color_fichada(array($primero));
    $salida  = color_fichada(array($ultimo));

    $modal = '<button type="button" class="btn btn-sm open-modal btn-custom opa9" data-toggle="modal" data="' . $Gen_Lega . '-' . $Gen_Fecha2 . '" data2="' . $Gen_Nombre . '" data3="' . $Gen_Fecha . '" data4="' . $Gen_Dia_Semana2 . ' ' . $Gen_Fecha . '" data5="' . $Gen_Horario . '" data6="' . $Gen_Fecha3 . '">+</button>';

    $entrada['Fic'] = '<button title="Ver Fichada" type="button" class="btn btn-link ls1 fontq p-0 mFic open-modal text-secondary" data-toggle="modal" data="' . $Gen_Lega . '-' . $Gen_Fecha2 . '" data2="' . $Gen_Nombre . '" data3="' . $Gen_Fecha . '" data4="' . $Gen_Dia_Semana2 . ' ' . $Gen_Fecha . '" data5="' . $Gen_Horario . '" data6="' . $Gen_Fecha3 . '" data_mFic="1" >' . $entrada['Fic'] . '</button>';

    $horas6 = '<button title="Ver Horas" type="button" class="text-left btn btn-link fontq p-0 mFic open-modal text-secondary" data-toggle="modal" data="' . $Gen_Lega . '-' . $Gen_Fecha2 . '" data2="' . $Gen_Nombre . '" data3="' . $Gen_Fecha . '" data4="' . $Gen_Dia_Semana2 . ' ' . $Gen_Fecha . '" data5="' . $Gen_Horario . '" data6="' . $Gen_Fecha3 . '" data_mHor="1" >' . $horas6 . '</button>';

    $Novedades2 = '<button title="Ver Horas" type="button" class="text-left btn btn-link fontq p-0 mFic open-modal text-secondary" data-toggle="modal" data="' . $Gen_Lega . '-' . $Gen_Fecha2 . '" data2="' . $Gen_Nombre . '" data3="' . $Gen_Fecha . '" data4="' . $Gen_Dia_Semana2 . ' ' . $Gen_Fecha . '" data5="' . $Gen_Horario . '" data6="' . $Gen_Fecha3 . '" data_mNov="1" >' . $Novedades2 . '</button>';

    if ($_SESSION['ABM_ROL']['aCit']) {
        $Gen_Horario = '<button title="Citar Horario" type="button" class="btn btn-link ls1 fontq p-0 Cita open-modal text-secondary" data-toggle="modal" data="' . $Gen_Lega . '-' . $Gen_Fecha2 . '" data2="' . $Gen_Nombre . '" data3="' . $Gen_Fecha . '" data4="' . $Gen_Dia_Semana2 . ' ' . $Gen_Fecha . '" data5="' . $Gen_Horario . '" data6="' . $Gen_Fecha3 . '" data7="Cita">' . $Gen_Horario . '</button>';
    }

    if ($Excel) {
        $data[] = array(
            'Gen_Lega'    => $Gen_Lega,
            'LegNombre'   => $Gen_Nombre,
            'Gen_Nombre'  => $Gen_Nombre,
            'Fecha'       => $Gen_Fecha,
            'FechaDia'    => nombre_dia($Gen_Dia_Semana),
            'Fechastr'    => $Gen_Fecha2,
            'Entra'       => strip_tags($entrada['Fic']),
            'Sale'        => strip_tags($salida['Fic']),
            'Novedades'   => strip_tags($Novedades2),
            'NovHor'      => $NoveHoras,
            'DescHoras'   => strip_tags($horas6),
            'HsHechas'    => ($horas3),
            'HsCalc'      => ($horas4),
            'HsAuto'      => $horas5,
            'num_dia'     => nombre_dia($Gen_Dia_Semana),
        );
    } else {
        $data[] = array(
            'LegNombre'     => '<span 
            data-nombre="'.$Gen_Nombre.'" 
            data-lega="'.$Gen_Lega.'"
            data-fechaini="'.($Gen_Fecha2).'"
            data-fechafin="'.($Gen_Fecha2).'"
            data-procLega="true"
            "title="Procesar registro: ' . $Gen_Nombre . '. '.$Gen_Fecha.'" class="d-inline-block text-truncate pointer procReg" style="max-width: 200px;">' . $Gen_Nombre . '<br>' . $Gen_Lega . '</span>',
            'Gen_Lega'      => $Gen_Lega,
            'Gen_Nombre'    => $Gen_Nombre,
            'Fecha'         => $Gen_Fecha,
            'FechaDia'      => $Gen_Fecha . '<br />' . nombre_dia($Gen_Dia_Semana),
            'Fechastr'      => $Gen_Fecha2,
            'Primera'       => $entrada['Fic'] . '<br />' . $salida['Fic'],
            //    'Novedades'     => $Novedades2,
            'Novedades'     => '<span class="d-inline-block text-truncate" style="max-width: 210px;">' . $Novedades2 . '</span>',
            'NovHor'        => $NoveHoras,
            'DescHoras'     => $horas6,
            'HsHechas'      => ($horas3),
            'HsCalc'        => ($horas4),
            'HsAuto'        => $horas5,
            //    'Primera' => '',
            'num_dia'       => nombre_dia($Gen_Dia_Semana),
            'Gen_Horario'   => $Gen_Horario,
            'modal'         => $modal,
            'null'         => '',
            //    'Fichadas'    => ($Fic_Hora),
            // //    'Novedades'   => ($Novedad),
            //    'FichaFirst'  => ($primero),
            //    'FichaLast'   => ($ultimo),
            //    'Horas'       => $Horas
        );
    }
    unset($Fic_Hora);
    unset($Novedad);
    unset($primero);
    unset($ultimo);
    unset($Horas);
endwhile;
sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);

$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $data
);

if ($Excel) {
    echo json_encode($data);
} else {
    echo json_encode($json_data);
}
