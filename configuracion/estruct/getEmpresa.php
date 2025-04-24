<?php
require __DIR__ . '/../../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");

require __DIR__ . '/../../config/conect_mssql.php';

$data = array();

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$_POST['cod'] = $_POST['cod'] ?? '';
$cod = test_input($_POST['cod']);
$query = "SELECT TOP 1 E.EmpRazon AS 'descripcion', E.EmpCodi AS 'codigo', E.EmpTipo AS 'EmpTipo', E.EmpCUIT AS 'EmpCUIT', E.EmpDomi AS 'EmpDomi', E.EmpDoNu AS 'EmpDoNu', E.EmpPiso AS 'EmpPiso', E.EmpDpto AS 'EmpDpto', E.EmpCoPo AS 'EmpCoPo', E.EmpProv AS 'EmpProv', E.EmpLoca AS 'EmpLoca', E.EmpTele AS 'EmpTele', E.EmpMail AS 'EmpMail', E.EmpCont AS 'EmpCont', E.EmpObse AS 'EmpObse', P.ProDesc AS 'ProDesc', L.LocDesc AS 'LocDesc' FROM EMPRESAS E LEFT JOIN PROVINCI P ON E.EmpProv=P.ProCodi LEFT JOIN LOCALIDA L ON E.EmpLoca=L.LocCodi WHERE E.EmpCodi=$cod";
// print_r($query).PHP_EOL; exit;
$rs = sqlsrv_query($link, $query, $params, $options);
if (sqlsrv_num_rows($rs) > 0) {
    while ($r = sqlsrv_fetch_array(($rs))):
        $codigo = $r['codigo'];
        $descripcion = $r['descripcion'];
        $EmpTipo = $r['EmpTipo'];
        $EmpCUIT = $r['EmpCUIT'];
        $EmpDomi = $r['EmpDomi'];
        $EmpDoNu = $r['EmpDoNu'];
        $EmpPiso = $r['EmpPiso'];
        $EmpDpto = $r['EmpDpto'];
        $EmpCoPo = $r['EmpCoPo'];
        $EmpProv = $r['EmpProv'];
        $EmpLoca = $r['EmpLoca'];
        $EmpTele = $r['EmpTele'];
        $EmpMail = $r['EmpMail'];
        $EmpCont = $r['EmpCont'];
        $EmpObse = $r['EmpObse'];
        $ProDesc = $r['ProDesc'];
        $LocDesc = $r['LocDesc'];
        $data = array(
            'codigo' => $codigo,
            'descripcion' => utf8str($descripcion),
            'EmpTipo' => $EmpTipo,
            'EmpCUIT' => $EmpCUIT,
            'EmpDomi' => utf8str($EmpDomi),
            'EmpDoNu' => $EmpDoNu,
            'EmpPiso' => utf8str($EmpPiso),
            'EmpDpto' => utf8str($EmpDpto),
            'EmpCoPo' => utf8str($EmpCoPo),
            'EmpProv' => utf8str($EmpProv),
            'EmpLoca' => utf8str($EmpLoca),
            'EmpTele' => utf8str($EmpTele),
            'EmpMail' => utf8str($EmpMail),
            'EmpCont' => utf8str($EmpCont),
            'EmpObse' => utf8str($EmpObse),
            'ProDesc' => utf8str($ProDesc),
            'LocDesc' => utf8str($LocDesc)
        );
    endwhile;
}
sqlsrv_free_stmt($rs);
sqlsrv_close($link);

echo json_encode(($data));
exit;
