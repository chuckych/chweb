<?php

session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
date_default_timezone_set('America/Argentina/Buenos_Aires');
header("Content-Type: application/json");
E_ALL();

    UnsetPost('Visualizar', '2');

    $DateRange = explode(' al ', $_POST['_dr']);
    $FechaIni  = test_input(dr_fecha($DateRange[0]));
    $FechaFin  = test_input(dr_fecha($DateRange[1]));

    $FechaPag = (isset($_POST['k'])) ? test_input($_POST['k']):'';
    $FechaIni = ((isset($_POST['k']))) ? $FechaPag : $FechaIni;

    $FechaIni = FechaString($FechaIni);
    $FechaFin = FechaString($FechaFin);
    $Visualizar = test_input($_POST['Visualizar']);

    switch ($_POST['cta']) {
        case '2':
            $cta = "AND Ex.HorasEx-((S.JornadaReducida1 + S.FrancoCompe1)-(S.JornadaReducida2 + S.FrancoCompe2)) > 0";
            break;
        case '1':
            $cta = "AND Ex.HorasEx-((S.JornadaReducida1 + S.FrancoCompe1)-(S.JornadaReducida2 + S.FrancoCompe2)) < 0";
            break;
        default:
            // $cta = "AND Ex.HorasEx-((S.JornadaReducida1 + S.FrancoCompe1)-(S.JornadaReducida2 + S.FrancoCompe2)) <> 0";
            $cta ='';
            break;
    }

    switch ($Visualizar) {
        case '2':
            $FiltroNulo = 'AND (S.FrancoCompe1+S.FrancoCompe2+S.JornadaReducida1+S.JornadaReducida2+Ex.HorasEx) <> 0';
            break;
        default:
            $FiltroNulo = '';
            break;
    }


        require __DIR__ . '../../../filtros/filtros.php';
        require __DIR__ . '../../../config/conect_mssql.php';
        require __DIR__ . '../valores.php';
   

        $param   = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        
        /** Query de primer registro de Fecha */
        $query="SELECT MIN(FICHAS3.FicFech) AS 'min_Fecha', MAX(FICHAS3.FicFech) AS 'max_Fecha'
        FROM FICHAS3,PERSONAL
        WHERE PERSONAL.LegFeEg = '17530101' AND FICHAS3.FicLega = PERSONAL.LegNume";
        $result = sqlsrv_query($link, $query, $param, $options); 
        // print_r($query); exit;
        while ($row = sqlsrv_fetch_array($result)) :
            $firstDate = array(
                'firstDate'=> $row['min_Fecha']->format('Y/m/d'),
                'firstYear' => $row['min_Fecha']->format('Y')
            );
            $maxDate = array(
                'maxDate'=> $row['max_Fecha']->format('Y/m/d'),
                'maxYear' => $row['max_Fecha']->format('Y')
            );
        endwhile;
        sqlsrv_free_stmt($result);

        $params = $columns = $totalRecords = $data = array();
        $params = $_REQUEST;
        $where_condition = $sqlTot = $sqlRec = "";   

        $sql_query="SELECT PERSONAL.LegNume as Legajo, PERSONAL.LegApNo as Nombre, Ex.HorasEx, S.FrancoCompe1, S.FrancoCompe2, S.JornadaReducida1, S.JornadaReducida2, CtaCte=Ex.HorasEx-((S.JornadaReducida1 + S.FrancoCompe1)-(S.JornadaReducida2 + S.FrancoCompe2)) FROM PERSONAL CROSS APPLY (SELECT ISNULL(SUM( CASE WHEN N.NovTiCo=1 THEN LEFT(H3.FicHoras,2)*60+RIGHT(H3.FicHoras,2) ELSE 0 END),0) AS FrancoCompe1, ISNULL(SUM( CASE WHEN N.NovTiCo=4 THEN LEFT(H3.FicHoras,2)*60+RIGHT(H3.FicHoras,2) ELSE 0 END),0) AS FrancoCompe2, ISNULL(SUM( CASE WHEN N.NovTiCo=2 THEN LEFT(H3.FicHoras,2)*60+RIGHT(H3.FicHoras,2) ELSE 0 END),0) AS JornadaReducida1, ISNULL(SUM( CASE WHEN N.NovTiCo=5 THEN LEFT(H3.FicHoras,2)*60+RIGHT(H3.FicHoras,2) ELSE 0 END),0) AS JornadaReducida2 FROM FICHAS3 H3 JOIN NOVEDAD N ON H3.FicNove=N.NovCodi WHERE H3.FicLega=PERSONAL.LegNume AND H3.FicFech >='$FechaIni' AND H3.FicFech <='$FechaFin' AND H3.FicNove >0 ) S CROSS APPLY (SELECT ISNULL(SUM((LEFT(H1.FicHsAu,2)*60+RIGHT(H1.FicHsAu,2)) - (LEFT(H1.FicHsAu2,2)*60+RIGHT(H1.FicHsAu2,2))),0) AS HorasEx FROM FICHAS1 H1 JOIN TIPOHORA TH ON H1.FicHora=TH.THoCodi WHERE H1.FicLega=PERSONAL.LegNume AND H1.FicFech >='$FechaIni' AND H1.FicFech <='$FechaFin' AND TH.THoCtaH=1 AND H1.FicHsAu2 < H1.FicHsAu) Ex WHERE PERSONAL.LegNume >0 AND PERSONAL.LegFeEg='17530101' $cta $FiltroNulo $FilterEstruct $filtros";

        // print_r($sql_query); exit;

        $sqlTot .= $sql_query;
        $sqlRec .= $sql_query;

        if( !empty($params['search']['value']) ) {
        $where_condition .=	" AND ";
        $where_condition .= " (dbo.fn_Concatenar(PERSONAL.LegNume,PERSONAL.LegApNo) LIKE '%".$params['search']['value']."%')";
        // $where_condition .= " (dbo.fn_Concatenar(PERSONAL.LegNume,PERSONAL.LegApNo) LIKE '%".$params['search']['value']."%')";
        }

        if(isset($where_condition) && $where_condition != '') {
        $sqlTot .= $where_condition;
        $sqlRec .= $where_condition;
        }

        $sqlRec .=  "ORDER BY PERSONAL.LegNume OFFSET ".$params['start']." ROWS FETCH NEXT ".$params['length']." ROWS ONLY";
        $queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
        $totalRecords = sqlsrv_num_rows($queryTot);
        $queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);
        // print_r($sqlRec); exit;

            while ($row = sqlsrv_fetch_array($queryRecords)) :
                
                          $Legajo = $row['Legajo'];
                          $Nombre = $row['Nombre'];
                         $HorasEx = FormatHora($row['HorasEx']);
                    $FrancoCompe1 = FormatHora($row['FrancoCompe1']);
                    $FrancoCompe2 = FormatHora($row['FrancoCompe2']);
                $JornadaReducida1 = FormatHora($row['JornadaReducida1']);
                $JornadaReducida2 = FormatHora($row['JornadaReducida2']);

                $ctacte = $row['CtaCte'];
                
                if($ctacte>=0){
                    $ctacte = "<span class='fw5 m-0 ls1' style='color: #00c853' >".FormatHora($ctacte)."</span>"; /** positivo */
                }
                else{
                    $ctacte  = str_replace("-", "", $ctacte);
                    $ctacte = "<span class='fw5 m-0 ls1' style='color: #d32f2f'>-".FormatHora($ctacte)."</span>"; /** negativo */
                }

                $FrancoCompe1     = "<span class='fw4 m-0 ls1' style='color: #d32f2f' >".($FrancoCompe1)."</span>"; /** negativo */
                $JornadaReducida1 = "<span class='fw4 m-0 ls1' style='color: #d32f2f' >".($JornadaReducida1)."</span>"; /** negativo */
                $FrancoCompe2     = "<span class='fw4 m-0 ls1' style='color: #00c853' >".($FrancoCompe2)."</span>"; /** positivo */
                $JornadaReducida2 = "<span class='fw4 m-0 ls1' style='color: #00c853' >".($JornadaReducida2)."</span>"; /** positivo */
                $HorasEx          = "<span class='fw4 m-0 ls1' style='color: #333333' >".($HorasEx)."</span>"; /** positivo */
                
               if ($row['CtaCte']=='0' && $row['HorasEx']=='0' && $row['FrancoCompe1']=='0' && $row['FrancoCompe2']=='0' && $row['JornadaReducida1']=='0' && $row['JornadaReducida2']=='0') {
                    $sumaValores = true;
                }else{
                    $sumaValores = false;
                }

                $modal = ($sumaValores) ? '<button title="No hay datos" type="button" class="btn btn-sm btn-custom opa8" disabled>+</button>'
                 :
                 '<button title="Detalle del registro" type="button" class="btn btn-sm btn-custom Detalle opa8" data-toggle="modal" data="' . $Legajo. '" data1="' .$FechaIni.','.$FechaFin . '" data2="'.$Nombre.'"  data3="'.$Legajo.'" data4="'.Fech_Format_Var($FechaIni, 'd/m/Y').'" data5="'.Fech_Format_Var($FechaFin, 'd/m/Y').'" data6="'.$HorasEx.'" data7="'.$JornadaReducida1.'" data8="'.$ctacte.'" data9="'.$FrancoCompe1.'" data10="'.$JornadaReducida2.'" data11="'.$FrancoCompe2.'">+</button>';  
                           
                $data[] = array(
                    'Legajo'   => $Legajo,
                    'Nombre'   => $Nombre,
                    'HorasEx'  => $HorasEx,
                    'Franco1'  => $FrancoCompe1,
                    'Franco2'  => $FrancoCompe2,
                    'JorRedu1' => $JornadaReducida1,
                    'JorRedu2' => $JornadaReducida2,
                    'ctacte'   => ($ctacte),
                    'modal'    => $modal
                );

                // $data = array_filter($data, function ($e) {
                //     return $e['ctacte2'] <> 0;
                // });
            endwhile;
            sqlsrv_free_stmt($queryRecords);
            sqlsrv_close($link);
            
         

            $json_data = array(
                "draw"            => intval($params['draw']),   
                "recordsTotal"    => intval($totalRecords),  
                "recordsFiltered" => intval($totalRecords),
                "data"            => $data
                );
    
        echo json_encode($json_data);

