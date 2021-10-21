<?php
require __DIR__ . '../../../config/conect_mssql.php';
// E_ALL();
$data   = array();
$legajo = $valueLegajo['Legajo'];

//require __DIR__ . '../../valores.php';

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$sql_query = "SELECT FICHAS.FicLega AS 'Gen_Lega', dbo.fn_DiaDeLaSemana(FICHAS.FicFech) AS 'Gen_dia', PERSONAL.LegApNo AS 'Gen_Nombre', FICHAS.FicFech AS 'Gen_Fecha', DATEPART(dw,.FICHAS.FicFech) AS 'Gen_Dia_Semana', dbo.fn_HorarioAsignado( FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicDiaL, FICHAS.FicDiaF ) AS 'Gen_Horario' FROM FICHAS $joinFichas3 INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume WHERE FICHAS.FicLega='$legajo' AND FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $FiltrosFichas ORDER BY FICHAS.FicFech, FICHAS.FicLega";
// print_r($sql_query); exit;

$queryRecords = sqlsrv_query($link, $sql_query, $param, $options);
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
    if ($_VerFic == '1') {
        /** Mostramos Las Fichadas E/S */
        $query_Fic = "SELECT REGISTRO.RegHoRe AS Fic_Hora, Fic_Tipo=CASE REGISTRO.RegTipo WHEN 0 THEN 'Capturador' ELSE 'Manual' END, Fic_Estado=CASE REGISTRO.RegFech WHEN REGISTRO.RegFeRe THEN CASE REGISTRO.RegHora WHEN REGISTRO.RegHoRe THEN 'Normal' ELSE 'Modificada' END ELSE 'Modificada' END FROM REGISTRO WHERE REGISTRO.RegFeAs='$Gen_Fecha2' AND REGISTRO.RegLega='$Gen_Lega' ORDER BY REGISTRO.RegFeAs,REGISTRO.RegLega,REGISTRO.RegFeRe,REGISTRO.RegHoRe";
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
    }
    /** FIN Mostramos Las Fichadas E/S */
    /** FIN FICHADAS */

    /** NOVEDADES */
    if ($_VerNove == '1') {
        /** Mostramos Las Novedades */
        $query_Nov = "SELECT FICHAS3.FicNove AS nov_novedad, NOVEDAD.NovDesc AS nov_descripcion, NOVEDAD.NovTipo AS nov_tipo, FICHAS3.FicHoras AS nov_horas FROM FICHAS3,NOVEDAD WHERE FICHAS3.FicLega='$Gen_Lega' AND FICHAS3.FicFech='$Gen_Fecha2' AND FICHAS3.FicNove=NOVEDAD.NovCodi AND FICHAS3.FicNove >0 AND FICHAS3.FicNoTi >=0 $FilterEstruct2 ORDER BY FICHAS3.FICFech";
        $result_Nov = sqlsrv_query($link, $query_Nov, $param, $options);
        // print_r($query_Nov); exit;

        $Novedad = array();
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
        }
        
        $desc = array();
        $desc2 = array();
        $desc3 = array();

        if (is_array($Novedad)) {
            foreach ($Novedad as $fila) {
                $desc[]  = ($fila["Cod"]);
                $desc2[] = ($fila["Descripcion"]);
                $desc3[] = ($fila["Horas"]);
            }
           
            if (is_array($desc)) {
                $Novedades  = implode("", $desc);
            }
            /** Codigo de la novedad */
            // $Novedades2 = implode("<hr>", $desc2);
            if (is_array($desc2)) {
                $Novedades2 = implode("<hr style='height: 1px; color: #fff; background-color: #fff; border: none; margin:0px;margin-top:2px'>", ($desc2));
            }

            /** Descripción de la novedad */
            // $NoveHoras  = implode("<hr>", $desc3);
            if (is_array($desc3)) {
                $NoveHoras  = implode("<hr style='height: 1px; color: #fff; background-color: #fff; border: none; margin:0px;margin-top:2px'>", $desc3);
            }

            /** Horas de la novedad */
            unset($desc);
            unset($desc2);
            unset($desc3);
        }
    }
    /** FIN Mostramos Las Novedades */
    /** FIN NOVEDADES */

    /** HORAS */
    // $query_Horas="SELECT FICHAS1.FicHora AS Hora, TIPOHORA.THoDesc AS HoraDesc, TIPOHORA.THoDesc2 AS HoraDesc2, FICHAS1.FicHsHe AS HsHechas, FICHAS1.FicHsAu AS HsCalculadas, 
    // FICHAS1.FicHsAu2 AS HsAutorizadas
    // FROM FICHAS1
    // INNER JOIN TIPOHORA ON FICHAS1.FicHora = TIPOHORA.THoCodi
    // WHERE FICHAS1.FicLega = '$Gen_Lega' AND FICHAS1.FicFech = '$Gen_Fecha2'
    // AND TIPOHORA.THoColu > 0
    // ORDER BY TIPOHORA.THoColu, FICHAS1.FicLega, FICHAS1.FicFech, FICHAS1.FicTurn, FICHAS1.FicHora";
    $query_Horas="SELECT TIPOHORA.THoCodi AS Hora, TIPOHORA.THoDesc2 AS 'HoraDesc2', TIPOHORA.THoDesc AS 'HoraDesc', (SELECT FICHAS1.FicHsAu2 AS HsAutorizadas FROM FICHAS1 WHERE FICHAS1.FicLega='$Gen_Lega' AND FICHAS1.FicFech='$Gen_Fecha2' AND FICHAS1.FicHora=TIPOHORA.THoCodi) AS 'HsAutorizadas' FROM TIPOHORA WHERE TIPOHORA.THoColu >0 ORDER BY TIPOHORA.THoColu";
    // print_r($query_Horas);exit;
    $Horas = array();
    if ($_VerHoras == '1') {
        /** Mostramos Las Horas */
        $result_Hor = sqlsrv_query($link, $query_Horas, $param, $options);
        // print_r($query_Horas); exit;
        if (sqlsrv_num_rows($result_Hor) > 0) {
            while ($row_Hor = sqlsrv_fetch_array($result_Hor)) :
                $Horas[] = array(
                    'Cod'          => $row_Hor['Hora'],
                    'Descripcion'  => $row_Hor['HoraDesc'],
                    'Descripcion2' => $row_Hor['HoraDesc2'],
                    'HsAuto'       => $row_Hor['HsAutorizadas']
                );
            endwhile;
            sqlsrv_free_stmt($result_Hor);
        }
        if (is_array($Horas)) {
            foreach ($Horas as $fila) {
                $hor[] = ceronull($fila["Cod"]);
                $hor2[] = $fila["Descripcion"];
                $hor6[] = $fila["Descripcion2"];
                // $HsHechas[] = ceronull($fila["HsHechas"]);
                //   $HsCalc[] = ceronull($fila["HsCalc"]);
                $HsAuto[] = '<td class="px-2 vtop center ls1">' . ceronull($fila["HsAuto"]) . '</td>';
            }

            $horas  = implode("", $hor);
            $horas2 = implode("<br/>", $hor2);
            /** Descripcion 1 del tipo de hora */
            $horas6 = implode("<br/>", $hor6);
            /** Descripcion 2 del tipo de hora */
            // $horas3 = implode("<br/>", $HsHechas);
            // $horas4 = implode("<br/>", $HsCalc);
            $horas5 = implode('', $HsAuto);

            unset($hor);
            unset($hor2);
            unset($hor6);
            // unset($HsHechas);
            // unset($HsCalc);
            unset($HsAuto);
            // var_export($Novedades); 
        }
    }
    /** FIN Mostramos Las Horas */
    /** Fin HORAS */

    $entrada = color_fichada(array($primero));
    $salida  = color_fichada(array($ultimo));

    $dataRegistros[] = array(
        'LegNombre'   => $Gen_Nombre . '<br>' . $Gen_Lega,
        'Gen_Lega'    => $Gen_Lega,
        'Gen_Nombre'  => $Gen_Nombre,
        'Fecha'       => $Gen_Fecha,
        'Dia'         => $Gen_Dia_Semana,
        'FechaDia'    => $Gen_Fecha . '<br />' . nombre_dia($Gen_Dia_Semana),
        'Fechastr'    => $Gen_Fecha2,
        'Primera'     => $entrada['Fic'],
        'Ultima'      => $salida['Fic'],
        'Novedades'   => ucwords(strtolower($Novedades2)),
        'NovHor'      => $NoveHoras,
        'DescHoras'   => $horas6,
        'HsAuto'      => ($horas5),
        'num_dia'     => nombre_dias($Gen_Dia_Semana, true),
        'Gen_Horario' => $Gen_Horario,
        // 'modal'       => $modal,
    );
    unset($Fic_Hora);
    unset($Novedad);
    unset($primero);
    unset($ultimo);
    unset($Horas);

endwhile;


sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);
            // print_r($dataRegistros);exit;
