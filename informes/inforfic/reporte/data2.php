<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');

$dataFichadas = array();
$legajo       = $valueAgrup['Legajo'];
$fecha        = $valueAgrup['Fecha'];

$PorLegajo = ($_Por == 'Fech') ? "": "AND REGISTRO.RegLega = '$legajo'"; /** para filtrar por legajo */
$PorFecha = ($_Por == 'Fech') ? "WHERE REGISTRO.RegFeAs = '$fecha'": "WHERE REGISTRO.RegFeAs BETWEEN '$FechaIni' AND '$FechaFin'"; /** Para filtrra por Fecha desde o desde hasta */

    $FicFalta = ($FicFalta && $FicFalta !='null') ? " HAVING (MAX(rn) % 2) = 1" : ""; /** Fichadas Inconsistentes */

    $sql_query = "WITH CTE AS(
        SELECT *,
            ROW_NUMBER() OVER (PARTITION BY RegLega, RegFeAs ORDER BY RegHoRe) AS rn
        FROM REGISTRO 
        $PorFecha $PorLegajo $FilterFicPer
    )
    SELECT CTE.RegLega AS 'FicLega', PERSONAL.LegApNo AS 'FicNombre',
        CTE.RegFeAs AS 'FicFechaAs', dbo.fn_DiaDeLaSemana(CTE.RegFeAs) AS 'FicDia',
        MAX(rn)AS  'Fic_Cant',
        MAX(CASE WHEN rn = 1 THEN RegHoRe END) AS 'FicPrimera',
        MAX(CASE WHEN rn > 0 THEN RegHoRe END) AS 'FicUltima',
        MAX( CASE WHEN rn = 1 THEN RegHoRe END) AS 'Fic_1',
        MAX( CASE WHEN rn = 2 THEN RegHoRe END) AS 'Fic_2',
        MAX( CASE WHEN rn = 3 THEN RegHoRe END) AS 'Fic_3', 
        MAX( CASE WHEN rn = 4 THEN RegHoRe END) AS 'Fic_4', 
        MAX( CASE WHEN rn = 5 THEN RegHoRe END) AS 'Fic_5', 
        MAX( CASE WHEN rn = 6 THEN RegHoRe END) AS 'Fic_6',
        MAX( CASE WHEN rn = 7 THEN RegHoRe END) AS 'Fic_7', 
        MAX( CASE WHEN rn = 8 THEN RegHoRe END) AS 'Fic_8',
        MAX( CASE WHEN rn = 9 THEN RegHoRe END) AS 'Fic_9', 
        MAX( CASE WHEN rn = 10 THEN RegHoRe END) AS 'Fic_10',
        MAX( CASE WHEN rn = 11 THEN RegHoRe END) AS 'Fic_11', 
        MAX( CASE WHEN rn = 12 THEN RegHoRe END) AS 'Fic_12',
        MAX( CASE WHEN rn = 13 THEN RegHoRe END) AS 'Fic_13', 
        MAX( CASE WHEN rn = 14 THEN RegHoRe END) AS 'Fic_14'
    FROM CTE 
    JOIN PERSONAL ON CTE.RegLega = PERSONAL.LegNume
    JOIN FICHAS ON CTE.RegFeAs = FICHAS.FicFech AND CTE.RegLega = FICHAS.FicLega
    WHERE FICHAS.FicLega> 0
    $FilterEstruct $FiltrosFichas
    GROUP BY CTE.RegLega,
             CTE.RegFeAs, 
             PERSONAL.LegApNo        
             $FicFalta
    ORDER BY CTE.RegFeAs, CTE.RegLega";

// h4($sql_query);exit;

// print_r($sql_query);

    $queryRecords = sqlsrv_query($link, $sql_query,$param, $options);
    while ($row = sqlsrv_fetch_array($queryRecords)) {
        $FicLega    = $row['FicLega'];
        $FicNombre  = $row['FicNombre'];
        $FicFechaAs = $row['FicFechaAs']->format('d/m/Y');
        $FicDia     = $row['FicDia'];
        $Fic_Cant   = $row['Fic_Cant'];
        $FicPrimera = $row['FicPrimera'];
        $FicUltima  = $row['FicUltima'];
        $Fic_1      = $row['Fic_1'];
        $Fic_2      = $row['Fic_2'];
        $Fic_3      = $row['Fic_3'];
        $Fic_4      = $row['Fic_4'];
        $Fic_5      = $row['Fic_5'];
        $Fic_6      = $row['Fic_6'];
        $Fic_7      = $row['Fic_7'];
        $Fic_8      = $row['Fic_8'];
        $Fic_9      = $row['Fic_9'];
        $Fic_10     = $row['Fic_10'];
        $Fic_11     = $row['Fic_11'];
        $Fic_12     = $row['Fic_12'];
        $Fic_13     = $row['Fic_13'];
        $Fic_14     = $row['Fic_14'];
        
        $dataFichadas[] = array(
            'FicLega'    => $FicLega,
            'FicNombre'  => $FicNombre,
            'FicFechaAs' => $FicFechaAs,
            'FicDia'     => $FicDia,
            'Fic_Cant'   => $Fic_Cant,
            'FicPrimera' => $FicPrimera,
            'FicUltima'  => $FicUltima,
            'Fic_1'      => $Fic_1,
            'Fic_2'      => $Fic_2,
            'Fic_3'      => $Fic_3,
            'Fic_4'      => $Fic_4,
            'Fic_5'      => $Fic_5,
            'Fic_6'      => $Fic_6,
            'Fic_7'      => $Fic_7,
            'Fic_8'      => $Fic_8,
            'Fic_9'      => $Fic_9,
            'Fic_10'     => $Fic_10,
            'Fic_11'     => $Fic_11,
            'Fic_12'     => $Fic_12,
            'Fic_13'     => $Fic_13,
            'Fic_14'     => $Fic_14
           
        );
    }
sqlsrv_free_stmt($queryRecords);
