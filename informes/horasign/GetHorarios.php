<?php

session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
date_default_timezone_set('America/Argentina/Buenos_Aires');
header("Content-Type: application/json");
E_ALL();

    $DateRange = explode(' al ', $_POST['_dr']);
    $FechaIni  = test_input(dr_fecha($DateRange[0]));
    $FechaFin  = test_input(dr_fecha($DateRange[1]));
    $data = array();
        require __DIR__ . '../../../filtros/filtros.php';
        require __DIR__ . '../../../config/conect_mssql.php';

        $legajo = test_input(FusNuloPOST('_l', 'vacio'));

        if($legajo=='vacio'){
            $json_data = array(
            "data"            => $data
        );

        echo json_encode($json_data);
        exit;
        }

        require __DIR__ . '../valores.php';

        $param   = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

        $params = $columns = $totalRecords ='';
        $params = $_REQUEST;
        $where_condition = $sqlTot = $sqlRec = "";
        
        $arrayFechas = ArrayFechas($FechaIni,$FechaFin); /** creamos un array con la fecha de inicio y la fecha de fin */
        foreach ($arrayFechas as $key => $FechaValue) { /** recorremos el array para consultar el horario dia a dia */
            $sql_query = "SELECT 
            PLANIFICA.PlaLega AS 'Legajo',
            PERSONAL.LegApNo AS 'Nombre',
            PLANIFICA.PlaFech AS 'Fecha',
            dbo.fn_DiaDeLaSemana(PLANIFICA.PlaFech) AS 'Dia',
            PLANIFICA.PlaLabo AS 'Laboral',
            PLANIFICA.PlaFeri AS 'Feriado',
            PLANIFICA.PlaHora AS 'CodHorario',
            PLANIFICA.PlaHorEnt AS 'Entrada',
            PLANIFICA.PlaHorSal AS 'Salida',
            PLANIFICA.PlaHorDes AS 'Descanso',
            PLANIFICA.PlaCitEnt AS 'CitEntrada',
            PLANIFICA.PlaCitSal AS 'CitaSalida',
            PLANIFICA.PlaCitDes AS 'CitDescano',
            HORARIOS.HorDesc AS 'Descripcion'
        FROM PLANIFICA
        INNER JOIN PERSONAL ON PLANIFICA.PlaLega = PERSONAL.LegNume
        INNER JOIN HORARIOS ON PLANIFICA.PlaHora = HORARIOS.HorCodi
        WHERE PlaLega = '$legajo'
            AND PlaFech = '$FechaValue'
            AND PlaTurn = 1 ORDER BY PERSONAL.LegNume";

        $queryTot     = sqlsrv_query($link, $sql_query, $param, $options);
        $totalRecords = sqlsrv_num_rows($queryTot);
        $queryRecords = sqlsrv_query($link, $sql_query, $param, $options);

        // print_r($sql_query); exit;

            while ($row = sqlsrv_fetch_array($queryRecords)) :

                $Entra = (MinHora($row['CitEntrada']) != MinHora($row['Entrada'])) ? $row['CitEntrada'] : $row['Entrada'];
                $Sale  = (MinHora($row['CitaSalida']) != MinHora($row['Salida'])) ? $row['CitaSalida'] : $row['Salida'];
                $Horario = $Entra.' a '.$Sale;
                $Horario = $row['Laboral']=='0'? 'Franco': $Horario;
                $Horario = $row['Feriado']=='1'? 'Feriado': $Horario;
                $Descripcion = $Horario == 'Franco' ? '-':$row['Descripcion'];

                $data[] = array(
                    'Legajo'       => $row['Legajo'],
                    'Nombre'       => '<span class="d-inline-block text-truncate" style="max-width:200px">'.$row['Nombre'].'</span>',
                    'Fecha'       => $row['Fecha']->format('d/m/Y'),
                    'Dia'         => $row['Dia'],
                    'CodHorario'  => $row['CodHorario'],
                    'Horario'     => $Horario,
                    'Descripcion' => $Descripcion,
                );
                
            endwhile;            
        }
        sqlsrv_free_stmt($queryRecords);
        sqlsrv_close($link);
        echo json_encode(array('horarios'=>$data));

