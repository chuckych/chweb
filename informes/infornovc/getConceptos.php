<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");

error_reporting(E_ALL);
ini_set('display_errors', '0');

require __DIR__ . '../../../config/conect_mssql.php';

$data = array();
$data2 = array();

$param     = array();
$options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$presentes = ($_SESSION['CONCEPTO_PRESENTES']);
$presentes = explode(',', $presentes);
$ausentes  = ($_SESSION['CONCEPTO_AUSENTES']);
$ausentes  = explode(',', $ausentes);

$query = "SELECT NOVEDAD.NovCodi, NOVEDAD.NovDesc, NOVEDAD.NovID, NOVEDAD.NovTipo FROM NOVEDAD WHERE NOVEDAD.NovCodi > 0";

// print_r($query).PHP_EOL; exit;

$rs = sqlsrv_query($link, $query, $param, $options);

if (sqlsrv_num_rows($rs) > 0) {
    while ($r = sqlsrv_fetch_array($rs)) :
        $NovCodi  = $r['NovCodi'];
        $NovDesc  = $r['NovDesc'];
        $NovID    = $r['NovID'];
        $NovTipo  = $r['NovTipo'];
        $checked = '';
        $checked2 = '';
        $ClassTNov = substr(TipoNov($NovTipo), 0, 3);
        $n = '0';
        foreach ($presentes as $value) {
            if ($NovCodi == $value) {
                $checked = 'checked';
                $n = '1';
                break;
            }
        }
        foreach ($ausentes as $value2) {
            if ($NovCodi == $value2) {
                $checked2 = 'checked';
                $n = '0';
                break;
            }
        }

        // $checked2 = ($n=='0') ? 'checked':'';

        $Presentes = '<div class="custom-control custom-checkbox"><input name="presentes[]" ' . $checked . ' type="checkbox" class="custom-control-input '.$ClassTNov.'_Pre" id="' . $NovCodi . '_Pres" value="' . $NovCodi . '"><label class="custom-control-label" for="' . $NovCodi . '_Pres"></label></div>';

        $Ausentes = '<div class="custom-control custom-checkbox"><input name="ausentes[]" ' . $checked2 . ' type="checkbox" class="custom-control-input '.$ClassTNov.'_Aus" id="' . $NovCodi . '_Aus" value="' . $NovCodi . '"><label class="custom-control-label" for="' . $NovCodi . '_Aus"></label></div>';

        $data[] = array(
            'NovCodi'   => $NovCodi,
            'NovDesc'   => $NovDesc,
            'NovID'     => $NovID,
            'NovTipo'   => '<span class="pointer '.$ClassTNov.'">'.TipoNov($NovTipo).'</span>',
            'Presentes' => $Presentes,
            'Ausentes'  => $Ausentes,
            'n'         => $n
        );
    endwhile;
}
// exit;
sqlsrv_free_stmt($rs);

sqlsrv_close($link);
$json_data = array(
    "presentes" => $presentes,
    "ausentes"  => $ausentes,
    "data"      => $data
);

echo json_encode($json_data);
exit;
