<?php
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");
session_start();
require __DIR__ . '../../../config/index.php';
require __DIR__ . '../../../config/conect_mssql.php';
E_ALL();

$param   = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$Datos = explode(',', $_POST['DRFech']);
$Fecha = '';
$FechaIni  = $Datos[0];
$FechaFin  = $Datos[1];

$Legajo = test_input($_POST['FicLega']);

$data = array();

/** NOVEDADES */
$query="SELECT FICHAS3.FicNove AS 'nov_novedad', FICHAS3.FicFech as 'FicFech', FICHAS3.FicLega as 'FicLega', NOVEDAD.NovDesc AS 'nov_descripcion', NOVEDAD.NovTipo AS 'nov_tipo', FICHAS3.FicHoras AS 'nov_horas', dbo.fn_DiaDeLaSemana(FICHAS3.FicFech) AS 'Dia',
FICHAS3.FicCaus AS 'CodCaus', NOVECAUSA.NovCDesc AS 'DescCausa', FICHAS3.FicObse AS 'Obserb', FICHAS3.FicCate AS 'FicCate', FICHAS3.FicJust AS 'FicJust', FICHAS3.FicEsta AS 'Estado'
FROM FICHAS3,NOVEDAD,NOVECAUSA  
WHERE FICHAS3.FicLega='$Legajo'
 AND FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaFin' 
AND FICHAS3.FicCaus = NOVECAUSA.NovCCodi
AND FICHAS3.FicNove = NOVEDAD.NovCodi 
AND FICHAS3.FicNove = NOVECAUSA.NovCNove
AND FICHAS3.FicNove > 0 
AND FICHAS3.FicNoTi >=0 
AND NOVEDAD.NovTiCo = '1'
ORDER BY FICHAS3.FicCate, FICHAS3.FicNove";
// print_r($query); exit;

$result = sqlsrv_query($link, $query, $param, $options);

if(PerCierre($Fecha, $Legajo)){
    $percierre=true;
    $disabled = 'disabled';
}else{
    $percierre=false;
}

if (sqlsrv_num_rows($result) > 0) {
    
while ($row = sqlsrv_fetch_array($result)) :
    $NovFechStr= $row['FicFech']->format('Ymd');

    if($percierre){
        $editar='<a title="Editar Novedad:'.$row['nov_descripcion'].'" href="#TopN"
        class="icon btn btn-sm btn-link text-decoration-none '.$disabled.'" <span data-icon="&#xe042;" class="align-middle text-gris"></span></a>';
        $eliminar = '<a title="Eliminar Novedad: '.$row['nov_descripcion'].'" 
        class="icon btn btn-sm btn-link text-decoration-none '.$disabled.'"><span data-icon="&#xe03d;" class="align-middle text-gris"></span></a>';
        $eliminar = ($row['Estado']=='0') ? '':$eliminar; 
    }else{
        $editar= '<a title="Editar Novedad: '.$row['nov_descripcion'].'" href="#TopN"
        class="icon btn btn-sm btn-link text-decoration-none mod_Nov text-gris" 
        data="'.$row['nov_novedad'].'-'.$row['nov_tipo'].'-'.$row['FicCate'].'"
        data2="'.$row['nov_novedad'].'"
        data3="'.$row['nov_descripcion'].'"
        data4="'.$row['CodCaus'].'"
        data5="'.$row['DescCausa'].'"
        data6="'.$row['Obserb'].'"
        data7="'.$row['FicJust'].'"
        data8="'.$row['FicCate'].'"
        data9="'.$row['nov_horas'].'"
        data10="'.$row['nov_tipo'].'"
        <span data-icon="&#xe042;" class="align-middle text-gris"></span></a>';
        $eliminar = '<a title="Eliminar Novedad: '.$row['nov_descripcion'].'"
        data="'.$row['nov_novedad'].'-'.$NovFechStr.'-'.$Legajo.'" 
        data2="'.$row['nov_descripcion'].'" 
        class="icon btn btn-sm btn-link text-decoration-none baja_Nov"><span data-icon="&#xe03d;" class="align-middle text-gris"></span></a>';
        $eliminar = ($row['Estado']=='0') ? '':$eliminar; 
    }

        $editar   = $_SESSION["ABM_ROL"]['mNov']=='0' ? '' : $editar;
        $eliminar = $_SESSION["ABM_ROL"]['bNov']=='0' ? '' : $eliminar;

        $data[] = array(
            'Cod'         => $row['nov_novedad'],
            'Fecha'       => $row['FicFech']->format('d/m/Y'),
            'Dia'         => $row['Dia'],
            'Descripcion' => $row['nov_descripcion'],
            'Horas'       => ($row['nov_horas']),
            'Tipo'        => TipoNov($row['nov_tipo']),
            'CodCausa'    => ($row['CodCaus']),
            'Causa'       => ceronull($row['DescCausa']),
            'Obserb'      => ceronull($row['Obserb']),
            'Cate'        => CateNov($row['FicCate']),
            'Just'        => JustNov($row['FicJust']),
            'editar'      => $editar,
            'eliminar'    => $eliminar,
            'null'        => ''
        );
    endwhile;
    sqlsrv_free_stmt($result);
}
/** FIN NOVEDADES */
echo json_encode(array('Novedades'=> $data));
sqlsrv_close($link);
exit;



