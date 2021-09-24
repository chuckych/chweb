<?php
ini_set('max_execution_time', 900); // 900 segundos 15 minutos
session_start();
header("Content-Type: application/json");
// header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

$params    = array();
$options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$data      = array();
$FechaHora = date('Ymd H:i:s');
$_POST['alta_novedad']  = $_POST['alta_novedad'] ?? '';
/** ALTA NOVEDAD */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta_novedad'] == true)) {
    pingWebService('Error al ingresar Novedades. Intentelo denuevo mas tarde');
    function checkTipoNov($novCodi)
    {
        $params    = array();
        $options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        require __DIR__ . '../../config/conect_mssql.php';
        $query = "SELECT NOVEDAD.NovCodi, NOVEDAD.NovDesc, NOVEDAD.NovTipo FROM NOVEDAD WHERE NOVEDAD.NovCodi = '$novCodi'";
        $stmt  = sqlsrv_query($link, $query, $params, $options);
        while ($row = sqlsrv_fetch_array($stmt)) {
            $NovTipo = $row['NovTipo'];
        }
        return $NovTipo;
        sqlsrv_free_stmt($stmt);
        sqlsrv_close($link);
    }
    function checkPresentes($query)
    {
        $rows    = array();
        $params    = array();
        $options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        require __DIR__ . '../../config/conect_mssql.php';
        $stmt  = sqlsrv_query($link, $query, $params, $options);
        while ($row = sqlsrv_fetch_array($stmt))
        $rows[] = array(
            'Legajo' => $row['Legajo'],
            'Nombre' => $row['Nombre'],
            'Fecha'  => $row['Fecha']->format('d/m/Y'),
            'Count'  => $row['Fichada'],
        );
        return $rows;
        sqlsrv_free_stmt($stmt);
        sqlsrv_close($link);
    }
    $tipoNov = checkTipoNov(FusNuloPOST('aFicNove', ''));
    $TipoIngreso  = FusNuloPOST('TipoIngreso', '');
    if ((valida_campo(test_input($TipoIngreso)))) {
        $data = array('status' => 'error', 'Mensaje' => 'Error TipoIngreso!');
        echo json_encode($data);
        exit;
    }
    if ($TipoIngreso == '1') {
        if ($_POST['Cuenta'] == '0') {
            $data = array('status' => 'error', 'Mensaje' => 'Al menos un Filtro es requerido!');
            echo json_encode($data);
            exit;
        }
    }
    if ((valida_campo($_POST['_draddNov']))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo Fecha requerido!');
        echo json_encode($data);
        exit;
    }

    $DateRange = explode(' al ', $_POST['_draddNov']);
    $FechaIni  = test_input(dr_fecha($DateRange[0]));
    $FechaFin  = test_input(dr_fecha($DateRange[1]));
    if ((($FechaIni) > ($FechaFin))) {
        $data = array('status' => 'error', 'Mensaje' => 'Rango de Fecha Incorrecto.');
        echo json_encode($data);
        exit;
    };

    FusNuloPOST('legajo', '');
    FusNuloPOST('aTipo', '0');
    FusNuloPOST('aEmp', '0');
    FusNuloPOST('aPlan', '0');
    FusNuloPOST('aSect', '0');
    FusNuloPOST('aSec2', '0');
    FusNuloPOST('aGrup', '0');
    FusNuloPOST('aSucur', '0');
    FusNuloPOST('legajos', '');

    $aTipo  = !empty($_POST['aTipo']) ? $_POST['aTipo'] : '0';
    $aEmp   = !empty($_POST['aEmp']) ? $_POST['aEmp'] : '0';
    $aPlan  = !empty($_POST['aPlan']) ? $_POST['aPlan'] : '0';
    $aSect  = !empty($_POST['aSect']) ? $_POST['aSect'] : '0';
    $aSec2  = !empty($_POST['aSec2']) ? $_POST['aSec2'] : '0';
    $aGrup  = !empty($_POST['aGrup']) ? $_POST['aGrup'] : '0';
    $aSucur = !empty($_POST['aSucur']) ? $_POST['aSucur'] : '0';
    $Legajos = !empty($_POST['legajos']) ? $_POST['legajos'] : '0';

    $queryFiltro = ($aTipo == '0') ? "":" AND P.LegTipo = $aTipo";
    $queryFiltro .= ($aEmp == '0') ? "":" AND P.LegEmpr = $aEmp";
    $queryFiltro .= ($aPlan == '0') ? "":" AND P.LegPlan = $aPlan";
    $queryFiltro .= ($aSect == '0') ? "":" AND P.LegSect = $aSect";
    $queryFiltro .= ($aSec2 == '0') ? "":" AND P.LegSec2 = $aSec2";
    $queryFiltro .= ($aGrup == '0') ? "":" AND P.LegGrup = $aGrup";
    $queryFiltro .= ($aSucur == '0') ? "":" AND P.LegSucu = $aSucur";
    $queryFiltro .= ($TipoIngreso != '1') ? "":" AND R.RegLega BETWEEN '1' AND '99999999'";
    $queryFiltro .= ($TipoIngreso == '1') ? "":" AND R.RegLega IN ($Legajos)";
    $queryFiltro .= " AND R.RegFeAs BETWEEN '$FechaIni' AND '$FechaFin'";

    $queryPresentes = "SELECT COUNT(R.RegHoRe) AS 'Fichada', R.RegFeAs AS 'Fecha', R.RegLega AS 'Legajo', P.LegApNo AS 'Nombre', P.LegTipo AS 'Tipo', P.LegEmpr AS 'Empresa', P.LegPlan AS 'Planta', P.LegSect AS 'Sector', P.LegSec2 AS 'Seccion', P.LegGrup AS 'Grupo', P.LegSucu AS 'Sucursal' 
    FROM REGISTRO R
    INNER JOIN PERSONAL P ON R.RegLega = P.LegNume
    WHERE R.RegLega > 0 $queryFiltro GROUP BY R.RegFeAs, R.RegLega, P.LegApNo, P.LegTipo, P.LegEmpr, P.LegPlan, P.LegSect, P.LegSec2, P.LegGrup, P.LegSucu";

    $DataFiltros = $aEmp > 0 ? 'Empresa: ' . $aEmp . '. ' : '';
    $DataFiltros .= $aPlan > 0 ? 'Planta: ' . $aPlan . '. ' : '';
    $DataFiltros .= $aGrup > 0 ? 'Grupo: ' . $aGrup . '. ' : '';
    $DataFiltros .= $aSucur > 0 ? 'Sucursal: ' . $aSucur . '. ' : '';
    $DataFiltros .= $aSect > 0 ? 'Sector: ' . $aSect . '. ' : '';
    $DataFiltros .= $aSec2 > 0 ? 'Sección: ' . $aSec2 . '. ' : '';

    $aFicNove  = FusNuloPOST('aFicNove', '');
    if ((valida_campo(test_input($aFicNove)))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo Novedad requerido!');
        echo json_encode($data);
        exit;
    }
    $microtime  = FusNuloPOST('now', '');

    if ((valida_campo(($microtime)))) {
        $data = array('status' => 'error', 'Mensaje' => 'Error!');
        echo json_encode($data);
        exit;
    }

    $SelEmpresa  = FusNuloPOST('SelEmpresa', '');
    $SelPlanta   = FusNuloPOST('SelPlanta', '');
    $SelSector   = FusNuloPOST('SelSector', '');
    $SelSeccion  = FusNuloPOST('SelSeccion', '');
    $SelGrupo    = FusNuloPOST('SelGrupo', '');
    $SelSucursal = FusNuloPOST('SelSucursal', '');
    $pagIni      = FusNuloPOST('pagIni', '');
    $pagFin      = FusNuloPOST('pagFin', '');

    $aFicHoras   = FusNuloPOST('aFicHoras', '00:00');
    $aFicObse    = FusNuloPOST('aFicObse', '');
    $aFicCate    = FusNuloPOST('aFicCate', '');
    $aLaboral    = FusNuloPOST('aLaboral', '');
    $aFicJust    = FusNuloPOST('aFicJust', '');
    $DescNovedad = FusNuloPOST('SelNovedad', '');
    $aCaus       = FusNuloPOST('aCaus', 0);
    $FechaHora   = date('Ymd H:i:s');

    $aFicCate = $aFicCate == 'on' ? '2' : '0';
    $aLaboral = $aLaboral == 'on' ? '1' : '0';
    $aFicJust = $aFicJust == 'on' ? '1' : '0';
    $aTipo = empty($aTipo) ? 0 : $aTipo;

    $aFicHoras = empty($aFicHoras) ? '00:00' : $aFicHoras;

    switch ($aTipo) {
        case '2':
            $aTipo = '1';
        case '1':
            $aTipo = '2';
            break;
    }
    $count = 1;
    if ($TipoIngreso == '2') {
        /** Si el tipo de ingreso es 2 (por Legajo) */
        /** Si el tipo de ingreso es 2. Por Legajo. Validamos que vengan legajos. */
        if ((valida_campo(($_POST['legajo'])))) {
            $data = array('status' => 'error', 'Mensaje' => 'Campo Legajo requerido!');
            echo json_encode($data);
            exit;
        }

        // foreach ($_POST['legajo'] as $key => $value) {
            // $legajo = $value;
            $arrLega = str_replace(',',';', $Legajos);
            $procesando = IngresarNovedad($aTipo. ',Legajos=['.$arrLega.']', '1', '99999999', $FechaIni, $FechaFin, $aEmp, $aPlan, $aSucur, $aGrup, $aSect, $aSec2, $aLaboral, $aFicNove, $aFicJust, $aFicObse, $aFicHoras, $aCaus, $aFicCate);
            /** Envio a webservice */
            if (($procesando) == 'Terminado') {
                //$mensaje = '(' . $count++ . ') Fin de Ingreso Novedad ' . $DescNovedad . '. <br>Legajo: ' . $value . ' desde ' . Fech_Format_Var($FechaIni, 'd/m/Y') . ' a ' . Fech_Format_Var($FechaFin, 'd/m/Y');
                //EscribirArchivo("Ingreso_" . $microtime, "../novedades/logs/", $mensaje, false, true, false);
                //audito_ch2("A", 'Alta Novedad Legajos ' . $legajo . '. Desde: ' . FechaFormatVar($FechaIni, ('d/m/Y')) . ' a ' . FechaFormatVar($FechaFin, ('d/m/Y')));
                audito_ch2("A", 'Alta Novedad ('.$aFicNove.') Legajos varios. Desde: ' . FechaFormatVar($FechaIni, ('d/m/Y')) . ' a ' . FechaFormatVar($FechaFin, ('d/m/Y')));
            } else {
                $mensaje = 'Error No Enviado';
                //EscribirArchivo("Ingreso_" . $microtime, "../novedades/logs/", $mensaje, false, false, false);
                $data = array('status' => 'error', 'Mensaje' => $mensaje);
                echo json_encode($data);
                exit;
            };
        // }

    } else {
        
        /** Si el tipo de ingreso es 1 (por Filtros) */
        $procesando = IngresarNovedad($aTipo, '1', '99999999', $FechaIni, $FechaFin, $aEmp, $aPlan, $aSucur, $aGrup, $aSect, $aSec2, $aLaboral, $aFicNove, $aFicJust, $aFicObse, $aFicHoras, $aCaus, $aFicCate);
        // $count=$count++; 
        if (($procesando) == 'Terminado') {
            //$mensaje = '(' . $count++ . ') Fin de Ingreso NOVEDAD ' . $DescNovedad . '. <br>Desde ' . Fech_Format_Var($FechaIni, 'd/m/Y') . ' a ' . Fech_Format_Var($FechaFin, 'd/m/Y');
            //EscribirArchivo("Ingreso_" . $microtime, "../novedades/logs/", $mensaje, false, true, false);
            audito_ch2("A", 'Alta Novedad ('.$aFicNove.') Legajos. ' . $DataFiltros . 'Desde: ' . FechaFormatVar($FechaIni, ('d/m/Y')) . ' a ' . FechaFormatVar($FechaFin, ('d/m/Y')));
        } else {
            $mensaje = 'Error No Enviado';
            //EscribirArchivo("Ingreso_" . $microtime, "../novedades/logs/", $mensaje, false, false, false);
            $data = array('status' => 'error', 'Mensaje' => $mensaje);
            echo json_encode($data);
            exit;
        };
    }

    $mensaje = 'Fin de Ingreso de Novedades';
    EscribirArchivo("Ingreso_" . $microtime, "../novedades/logs/", $mensaje, false, false, false);
    BorrarArchivosPDF('../novedades/logs/*.log');
    /** Borra los archivos log */
    $presentes = array();
    if ($tipoNov>2) {
        // header("Content-Type: application/json");
        $presentes = checkPresentes($queryPresentes);
    }
    header("Content-Type: application/json");
    $data = array('status' => 'ok', 'Mensaje' => 'Fin de Ingreso de Novedades', 'log' => 'Ingreso_' . $microtime . '.log', 'Errores' => ($presentes), 'ErrorTotal' => count($presentes));
    echo json_encode($data);
    exit;

} else {
    $data = array('status' => 'error', 'Mensaje' => 'ErrorPOST');
    echo json_encode($data);
    exit;
}
