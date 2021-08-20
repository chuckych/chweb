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

require __DIR__ . '../../config/conect_mssql.php';

$param   = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$Datos = explode('-', $_GET['Datos']);

$Fecha  = $Datos[1];
$Legajo = $Datos[0];

$data = array();

/** HORAS */
//$query="SELECT FICHAS1.FicHora AS Hora, TIPOHORA.THoDesc AS HoraDesc, TIPOHORA.THoDesc2 AS HoraDesc2, FICHAS1.FicHsHe AS FicHsHe, FICHAS1.FicHsAu AS FicHsAu, FICHAS1.FicHsAu2 AS FicHsAu2, FICHAS1.FicObse AS Observ, TIPOHORACAUSA.THoCCodi AS Motivo, TIPOHORACAUSA.THoCDesc AS DescMotivo, FICHAS1.FicFech AS FicFech, FICHAS1.FicEsta AS Estado FROM FICHAS1, TIPOHORA,TIPOHORACAUSA WHERE FICHAS1.FicLega='$Legajo' AND FICHAS1.FicFech='$Fecha' AND FICHAS1.FicHora=TIPOHORA.THoCodi AND FICHAS1.FicHora=TIPOHORACAUSA.THoCHora AND FICHAS1.FicCaus=TIPOHORACAUSA.THoCCodi AND TIPOHORA.THoColu >0 ORDER BY TIPOHORA.THoColu, FICHAS1.FicLega,FICHAS1.FicFech,FICHAS1.FicTurn, FICHAS1.FicHora";
//$query="SELECT FICHAS1.FicHora AS Hora, TIPOHORA.THoDesc AS HoraDesc, TIPOHORA.THoDesc2 AS HoraDesc2, FICHAS1.FicHsHe AS FicHsHe, FICHAS1.FicHsAu AS FicHsAu, FICHAS1.FicHsAu2 AS FicHsAu2, FICHAS1.FicObse AS Observ, TIPOHORACAUSA.THoCCodi AS Motivo, TIPOHORACAUSA.THoCDesc AS DescMotivo, FICHAS1.FicFech AS FicFech, FICHAS1.FicEsta AS Estado, TIPOHORA.THoColu FROM FICHAS1 INNER JOIN TIPOHORA ON FICHAS1.FicHora=TIPOHORA.THoCodi LEFT JOIN TIPOHORACAUSA ON FICHAS1.FicHora=TIPOHORACAUSA.THoCHora AND FICHAS1.FicCaus=TIPOHORACAUSA.THoCCodi WHERE FICHAS1.FicLega='$Legajo' AND FICHAS1.FicFech='$Fecha' ORDER BY TIPOHORA.THoColu, FICHAS1.FicHora";
$query = "SELECT FICHAS1.FicHora AS 'Hora', TIPOHORA.THoDesc AS 'HoraDesc', TIPOHORA.THoDesc2 AS 'HoraDesc2', FICHAS1.FicHsHe AS 'FicHsHe', FICHAS1.FicHsAu AS 'FicHsAu', FICHAS1.FicHsAu2 AS 'FicHsAu2', FICHAS1.FicObse AS 'Observ', TIPOHORACAUSA.THoCCodi AS 'Motivo', TIPOHORACAUSA.THoCDesc AS 'DescMotivo', FICHAS1.FicFech AS 'FicFech', FICHAS1.FicEsta AS 'Estado', TIPOHORA.THoColu AS 'THoColu', FICHAS.FicHsTr AS 'FicHsTr', FICHAS.FicHsAT AS 'FicHsAT', FICHAS.FicDiaL AS 'FicDiaL', FICHAS.FicDiaF AS 'FicDiaF', FICHAS.FicHorE AS 'FicHorE', FICHAS.FicHorS AS 'FicHorS', FICHAS.FicLega AS 'FicLega', dbo.fn_HorarioAsignado( FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicDiaL, FICHAS.FicDiaF ) AS 'Horario', dbo.fn_STRMinutos(FicHsTr) AS 'FicHsTrMin', dbo.fn_STRMinutos(FicHsAT) AS 'FicHsATMin' FROM FICHAS LEFT JOIN FICHAS1 ON FICHAS.FicLega=FICHAS1.FicLega AND FICHAS.FicFech=FICHAS1.FicFech LEFT JOIN TIPOHORA ON FICHAS1.FicHora=TIPOHORA.THoCodi LEFT JOIN TIPOHORACAUSA ON FICHAS1.FicHora=TIPOHORACAUSA.THoCHora AND FICHAS1.FicCaus=TIPOHORACAUSA.THoCCodi WHERE FICHAS.FicLega='$Legajo' AND FICHAS.FicFech='$Fecha' ORDER BY TIPOHORA.THoColu, FICHAS1.FicHora";
// print_r($query);exit;
if (PerCierre($Fecha, $Legajo)) {
    $percierre = true;
    $disabled = 'disabled';
} else {
    $percierre = false;
}
$result = sqlsrv_query($link, $query, $param, $options);

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :

        $HorasNeg  = ($row['FicHsATMin'] <= $row['FicHsTrMin']) ? 0 : 1;
        $Exedentes = ($row['FicHsTrMin'] - $row['FicHsATMin']);

        $disabled  = '';
        $editar    = '';
        $eliminar  = '';

        $HoraFechStr = !empty($row['FicFech']) ? $row['FicFech']->format('Ymd') : '';
        if (!empty($row['FicFech'])) {
            # code..
            if ($percierre) {
                $disabled = 'disabled';
                $editar = '<a title="Editar Hora:' . $row['HoraDesc'] . '" href="#TopN"
            class="bi bi-pen btn btn-sm btn-link text-decoration-none ' . $disabled . '"</a>';
                $eliminar = '<a title="Eliminar Hora: ' . $row['HoraDesc'] . '" 
            class="bi bi-trash btn btn-sm btn-link text-decoration-none ' . $disabled . '"></a>';
                $eliminar = ($row['Estado'] == '2') ? $eliminar : '';
            } else {
                $disabled = ($row['Estado'] == '2') ? '' : '';
                $editar = '<a title="Editar Hora: ' . $row['HoraDesc'] . '" href="#TopN"
            class="bi bi-pen btn btn-sm btn-link text-decoration-none mod_hora text-gris ' . $disabled . '" 
            data="' . $row['Hora'] . '"
            data1="' . $row['FicHsAu'] . '"
            data2="' . $row['FicHsAu2'] . '"
            data3="' . $row['HoraDesc'] . '"
            data4="' . $row['Motivo'] . '"
            data5="' . $row['DescMotivo'] . '"
            data6="' . $row['Observ'] . '"</a>';
                $eliminar = '<a title="Eliminar Hora: ' . $row['HoraDesc'] . '"
            data="' . $row['Hora'] . '-' . $HoraFechStr . '-' . $Legajo . '" 
            data2="' . $row['HoraDesc'] . '" 
            class="bi bi-trash btn btn-sm btn-link text-decoration-none baja_Hora ' . $disabled . '"></a>';
                $eliminar = ($row['Estado'] == '2') ? $eliminar : '';
            }
        }
        if (str_replace("-", "", $_SESSION['ListaTipoHora'])) {
            if (in_array(intval($row['Hora']), explode(',', $_SESSION['ListaTipoHora']))) {
                $editar   = $editar;
                $eliminar = $eliminar;
            } else {
                $editar = '';
                $eliminar = '';
            }
        }
        $editar   = $_SESSION["ABM_ROL"]['mHor'] == '0' ? '' : $editar;
        $eliminar = $_SESSION["ABM_ROL"]['bHor'] == '0' ? '' : $eliminar;
        if (!empty($row['FicFech'])) {
            $data[] = array(
                'Cod'          => $row['Hora'],
                'Descripcion'  => $row['HoraDesc'],
                'Descripcion2' => $row['HoraDesc2'],
                'HsCalc'       => $row['FicHsAu'],
                'HsHechas'     => $row['FicHsHe'],
                'HsAuto'       => $row['FicHsAu2'],
                'Observ'       => ceronull($row['Observ']),
                'Motivo'       => ceronull($row['Motivo']),
                'DescMotivo'   => ceronull($row['DescMotivo']),
                'editar'      => $editar,
                'eliminar'    => $eliminar,
                'null'         => ''
            );
        }
        $dataFichas = array(
            'Horario'    => $row['Horario'],
            'FicHsTr'    => $row['FicHsTr'],
            'FicHsAT'    => $row['FicHsAT'],
            'FicDiaL'    => $row['FicDiaL'],
            'FicDiaF'    => $row['FicDiaF'],
            'FicHorE'    => $row['FicHorE'],
            'FicHorS'    => $row['FicHorS'],
            'FicHsTrMin' => $row['FicHsTrMin'],
            'FicHsATMin' => $row['FicHsATMin'],
            'HorasNeg'   => $HorasNeg,
            'DatoFicha'  => $HoraFechStr . $row['FicLega'],
            'Exedentes'  => MinHora($Exedentes),
        );
    endwhile;
    sqlsrv_free_stmt($result);
}
/** Fin HORAS */
echo json_encode(array('Horas' => $data, 'Fichas' => $dataFichas));
sqlsrv_close($link);
exit;
