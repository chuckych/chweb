<?php
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");

error_reporting(E_ALL);
ini_set('display_errors', '0');

session_start();

require __DIR__ . '../../config/index.php';

$check_dl = (isset($_POST['_dl'])) ? "AND FICHAS.FicDiaL = '1'" : '';
/** Filtrar Dia Laboral */

$DateRange = explode(' al ', $_POST['_dr']);
$FechaIni  = test_input(dr_fecha($DateRange[0]));
$FechaFin  = test_input(dr_fecha($DateRange[1]));

require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';

$param  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

 $query="SELECT DISTINCT FICHAS.FicFech AS Fecha FROM .FICHAS INNER JOIN .PERSONAL ON .FICHAS.FicLega=.PERSONAL.LegNume WHERE PERSONAL.LegFeEg='17530101' AND .FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $check_dl $filtros ORDER BY .FICHAS.FicFech";

$result = sqlsrv_query($link, $query, $param, $options);
// print_r($query); exit;

while ($row = sqlsrv_fetch_array($result)) :
    $IndiFecha[] = ($row['Fecha']->format('Y-m-d'));
endwhile;
$IndiFecha = (isset($IndiFecha)) ? $IndiFecha : array($FechaIni);
sqlsrv_free_stmt($result);
/** LIBERAMOS MEMORIA */
/** Query de primer registro de Fecha */
 $query="SELECT MIN(REGISTRO.RegFeAs) AS 'min_Fecha', MAX(REGISTRO.RegFeAs) AS 'max_Fecha' FROM REGISTRO,PERSONAL WHERE PERSONAL.LegFeEg='17530101' AND REGISTRO.RegFeAs >'17530101' AND REGISTRO.RegLega=PERSONAL.LegNume";
$result = sqlsrv_query($link, $query, $param, $options);
// print_r($query); exit;
while ($row = sqlsrv_fetch_array($result)) :
    $firstDate = array(
        'firstDate' => $row['min_Fecha']->format('Y/m/d'),
        'firstYear' => $row['min_Fecha']->format('Y')
    );
    $maxDate = array(
        'maxDate' => $row['max_Fecha']->format('Y/m/d'),
        'maxYear' => $row['max_Fecha']->format('Y')
    );
endwhile;
sqlsrv_free_stmt($result);
/** LIBERAMOS MEMORIA */

$primero = (array_key_first($IndiFecha));
$ultimo  = (array_key_last($IndiFecha));
$primero = (array_values($IndiFecha)[$primero]);
$ultimo  = (array_values($IndiFecha)[$ultimo]);
$k        = (isset($_GET['k'])) ? $_GET['k'] : '0';
$FechaPag = array_values($IndiFecha)[$k];
$FechaIni = (isset($_GET['k'])) ? FechaString($FechaPag) : FechaString($primero);
$FechaFin = FechaString($FechaFin);
//$Count_Per = (isset($_GET['_per'])) ? count($_GET['_per']) :'0'; /** Contamos filtros de legajo. */
$_range = (($_POST['_range'] == 'on')) ? true : false;
/** _range Filtramos por todo el rango. */
if ($_range) {
    /** Si la cuenta de filtro de legajo es igual a 1. Filtramos la fecha por el rango. Sino solo por el día. */
    $Filter_fecha = "AND FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin'";
} else {
    $Filter_fecha = "AND FICHAS.FicFech = '$FechaIni'";
}

$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

 $sql_query="SELECT FICHAS.FicLega AS 'Gen_Lega', dbo.fn_DiaDeLaSemana(FICHAS.FicFech) AS 'Gen_dia', .PERSONAL.LegApNo AS 'Gen_Nombre', .FICHAS.FicFech AS 'Gen_Fecha', DATEPART(dw, .FICHAS.FicFech) AS 'Gen_Dia_Semana', 'Gen_Horario'= CASE FICHAS.FicDiaL WHEN 0 THEN CASE FICHAS.FicDiaF WHEN 1 THEN 'Feriado' ELSE 'Franco' END ELSE (FICHAS.FicHorE + ' a ' + FICHAS.FicHorS) END FROM .FICHAS INNER JOIN .PERSONAL ON .FICHAS.FicLega=.PERSONAL.LegNume WHERE PERSONAL.LegFeEg='17530101' $Filter_fecha $check_dl $filtros";

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

$sqlRec .=  "ORDER BY .FICHAS.FicFech DESC OFFSET " . $params['start'] . " ROWS FETCH NEXT " . $params['length'] . " ROWS ONLY";
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);
$queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);
// print_r($sqlRec); exit;

/** BUSCAMOS DENTRO DE FICHAS EL LEGAJO NOMBRE FECHA DIA HORARIO */
while ($row = sqlsrv_fetch_array($queryRecords)) :
    $Gen_Lega        = $row['Gen_Lega'];
    $Gen_Nombre      = $row['Gen_Nombre'];
    $Gen_Fecha       = $row['Gen_Fecha']->format('d/m/Y');
    $Gen_Fecha2      = $row['Gen_Fecha']->format('Ymd');
    $Gen_Fecha3      = $row['Gen_Fecha']->format('Y-m-d');
    $Gen_dia         = $row['Gen_dia'];
    $Gen_Dia_Semana  = $row['Gen_dia'];
    $Gen_Dia_Semana2 = ($row['Gen_dia']);
    $Gen_Horario     = $row['Gen_Horario'];

    /** FICHADAS */
        $query_Fic="SELECT REGISTRO.RegHoRe AS Fic_Hora, Fic_Tipo=CASE REGISTRO.RegTipo WHEN 0 THEN 'Capturador' ELSE 'Manual' END, Fic_Estado=CASE REGISTRO.RegFech WHEN REGISTRO.RegFeRe THEN CASE REGISTRO.RegHora WHEN REGISTRO.RegHoRe THEN 'Normal' ELSE 'Modificada' END ELSE 'Modificada' END FROM REGISTRO WHERE REGISTRO.RegFeAs='$Gen_Fecha2' AND REGISTRO.RegLega='$Gen_Lega' ORDER BY REGISTRO.RegFeAs,REGISTRO.RegLega,REGISTRO.RegFeRe,REGISTRO.RegHoRe";

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
        if (is_array($Fic_Hora)) {
            foreach ($Fic_Hora as $fila) {
                // $Fici[] = ("<span class='ls1 mr-1 border-left px-1'>" . ($fila["Fic"]) . "</span>");
                $Fici[] = "<tr class='py-2'><td class='px-2'>" . ceronull($fila["Fic"]) . "</td><td class='px-2'>" . ceronull($fila["Estado"]) . "</td><td class='px-2'>" . ceronull($fila["Tipo"]) . "</td><td class='w-100'></td></tr>";
            }

            $Fichadas = implode("", $Fici);
            unset($Fici);
            // var_export($Fichadas); 
        } else {
            $Fichadas = "No hay Fichadas";
        }
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
        $query_Horas="SELECT FICHAS1.FicHora AS Hora, TIPOHORA.THoDesc AS HoraDesc, TIPOHORA.THoDesc2 AS HoraDesc2, FICHAS1.FicHsHe AS HsHechas, FICHAS1.FicHsAu AS HsCalculadas, FICHAS1.FicHsAu2 AS HsAutorizadas FROM FICHAS1,TIPOHORA,TIPOHORACAUSA WHERE FICHAS1.FicLega='$Gen_Lega' AND FICHAS1.FicFech='$Gen_Fecha2' AND FICHAS1.FicHora=TIPOHORA.THoCodi AND FICHAS1.FicHora=TIPOHORACAUSA.THoCHora AND FICHAS1.FicCaus=TIPOHORACAUSA.THoCCodi AND TIPOHORA.THoColu >0 ORDER BY TIPOHORA.THoColu, FICHAS1.FicLega,FICHAS1.FicFech,FICHAS1.FicTurn, FICHAS1.FicHora";
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

    if (BrowserIE()) {
        $icono='<svg width="1.2em" height="1.2em" viewBox="0 0 16 16" class="bi bi-clipboard-data" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
        <path fill-rule="evenodd" d="M9.5 1h-3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
        <path d="M4 11a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0v-1zm6-4a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0V7zM7 9a1 1 0 0 1 2 0v3a1 1 0 1 1-2 0V9z"/>
      </svg>';
    } else {
        $icono = '<svg class="bi" width="18" height="18" fill="currentColor">
        <use xlink:href="../img/bootstrap-icons.svg#clipboard-data" />
        </svg>';
    }

    $modal = '<a title="Ver detalle" type="button" class="open-modal btn-outline-custom btn-sm border-0 py-2 btn animate__animated animate__flipInX contentd" data-toggle="modal" data="' . $Gen_Lega . '-' . $Gen_Fecha2 . '" data2="' . $Gen_Nombre . '" data3="' . $Gen_Fecha . '" data4="' . $Gen_dia . ' ' . $Gen_Fecha . '" data5="' . $Gen_Horario . '" data6="' . $Gen_Fecha3 . '">'.$icono.'</a>';
    $data[] = array(
        'LegNombre'     => '<span class="contentd">'.$Gen_Nombre . '<br><span class="fw3">Legajo: </span> ' . $Gen_Lega.'</span>',
        'Gen_Lega'      => '<span class="contentd">'.$Gen_Lega.'</span>',
        'Gen_Nombre'    => '<span class="contentd">'.$Gen_Nombre.'</span>',
        'Fecha'         => '<span class="contentd">'.$Gen_Fecha.'</span>',
        'FechaDia'      => '<span class="contentd">'.$Gen_Fecha . '<br />' . ($Gen_dia).'</span>',
        'Fechastr'      => '<span class="contentd">'.$Gen_Fecha2.'</span>',
        'Primera'       => '<span class="contentd">'.$entrada['Fic'].'</span>',
        'Ultima'        => '<span class="contentd">'.$salida['Fic'].'</span>',
        'Novedades'     => '<span class="contentd">'.$Novedades2.'</span>',
        'NovHor'        => '<span class="contentd">'.$NoveHoras.'</span>',
        'DescHoras'     => '<span class="contentd">'.$horas6.'</span>',
        'HsHechas'      => '<span class="contentd">'.($horas3).'</span>',
        'HsCalc'        => '<span class="contentd">'.($horas4).'</span>',
        'HsAuto'        => '<span class="contentd">'.$horas5.'</span>',
        //    'Primera' => '<span class="contentd">'.''.'</span>',
        'num_dia'       => '<span class="contentd">'.nombre_dia($Gen_Dia_Semana).'</span>',
        'Gen_Horario'   => '<span class="contentd">'.$Gen_Horario.'</span>',
        'modal'         => '<span class="contentd py-2">'.$modal.'</span>',
        //    'Fichadas'    => ($Fic_Hora),
        //    'Novedades'   => ($Novedad),
        //    'FichaFirst'  => ($primero),
        //    'FichaLast'   => ($ultimo),
        //    'Horas'       => $Horas
    );
    unset($Fic_Hora);
    unset($Novedad);
    unset($primero);
    unset($ultimo);
    unset($Horas);

endwhile;
// sleep(2);
sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);
$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $data,
    "nombre"          => $Gen_Nombre
);
echo json_encode($json_data);
