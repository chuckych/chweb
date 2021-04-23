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

    $TipoIngreso  = FusNuloPOST('TipoIngreso', '');
    if ((valida_campo(test_input($TipoIngreso)))) {
        $data = array('status' => 'error', 'dato' => 'Error TipoIngreso!');
        echo json_encode($data);
        exit;
    }
    if ($TipoIngreso == '1') {
        if ($_POST['Cuenta'] == '0') {
            $data = array('status' => 'error', 'dato' => 'Al menos un Filtro es requerido!');
            echo json_encode($data);
            exit;
        }
    }

    if ((valida_campo($_POST['_draddNov']))) {
        $data = array('status' => 'error', 'dato' => 'Campo Fecha requerido!');
        echo json_encode($data);
        exit;
    }

    $DateRange = explode(' al ', $_POST['_draddNov']);
    $FechaIni  = test_input(dr_fecha($DateRange[0]));
    $FechaFin  = test_input(dr_fecha($DateRange[1]));

    if ((($FechaIni) > ($FechaFin))) {
        $data = array('status' => 'error', 'dato' => 'Rango de Fecha Incorrecto.');
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

    $aTipo  = !empty($_POST['aTipo']) ? $_POST['aTipo'] : '0';
    $aEmp   = !empty($_POST['aEmp']) ? $_POST['aEmp'] : '0';
    $aPlan  = !empty($_POST['aPlan']) ? $_POST['aPlan'] : '0';
    $aSect  = !empty($_POST['aSect']) ? $_POST['aSect'] : '0';
    $aSec2  = !empty($_POST['aSec2']) ? $_POST['aSec2'] : '0';
    $aGrup  = !empty($_POST['aGrup']) ? $_POST['aGrup'] : '0';
    $aSucur = !empty($_POST['aSucur']) ? $_POST['aSucur'] : '0';

    $DataFiltros = $aEmp>0 ? 'Empresa: '.$aEmp.'. ' :'';
    $DataFiltros .= $aPlan>0 ? 'Planta: '.$aPlan.'. ' :'';
    $DataFiltros .= $aGrup>0 ? 'Grupo: '.$aGrup.'. ' :'';
    $DataFiltros .= $aSucur>0 ? 'Sucursal: '.$aSucur.'. ' :'';
    $DataFiltros .= $aSect>0 ? 'Sector: '.$aSect.'. ' :'';
    $DataFiltros .= $aSec2>0 ? 'Sección: '.$aSec2.'. ' :'';

    $aFicNove  = FusNuloPOST('aFicNove', '');
    if ((valida_campo(test_input($aFicNove)))) {
        $data = array('status' => 'error', 'dato' => 'Campo Novedad requerido!');
        echo json_encode($data);
        exit;
    }
    $microtime  = FusNuloPOST('now', '');

    if ((valida_campo(($microtime)))) {
        $data = array('status' => 'error', 'dato' => 'Error!');
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
            $data = array('status' => 'error', 'dato' => 'Campo Legajo requerido!');
            echo json_encode($data);
            exit;
        }
        // require __DIR__ . '../../filtros/filtros.php';
        // require_once __DIR__ . '../../config/conect_mssql.php';

        // $qTipo  = ($aTipo == '1') ? "AND PERSONAL.LegTipo = '$aTipo'" : '';
        // $qTipo2  = ($aTipo == '2') ? "AND PERSONAL.LegTipo = '0'" : '';
        // $qEmp   = ($aEmp > '0') ? "AND PERSONAL.LegEmpr = '$aEmp'" : '';
        // $qPlan  = ($aPlan > '0') ? "AND PERSONAL.LegPlan = '$aPlan'" : '';
        // $qSucur = ($aSucur > '0') ? "AND PERSONAL.LegSucu = '$aSucur'" : '';
        // $qGrup  = ($aGrup > '0') ? "AND PERSONAL.LegGrup = '$aGrup'" : '';
        // $qSect  = ($aSect > '0') ? "AND PERSONAL.LegSect = '$aSect'" : '';
        // $qSec2  = ($aSec2 > '0') ? "AND PERSONAL.LegSec2 = '$aSec2'" : '';

        // $Filter = $qTipo;
        // $Filter .= $qTipo2;
        // $Filter .= $qEmp;
        // $Filter .= $qPlan;
        // $Filter .= $qSucur;
        // $Filter .= $qGrup;
        // $Filter .= $qSect;
        // $Filter .= $qSec2;

        $primerLegajo = current($_POST['legajo']);
        $ultimoLegajo = end($_POST['legajo']);

        // $query = "SELECT PERSONAL.LegNume FROM PERSONAL WHERE PERSONAL.LegNume >0 AND PERSONAL.LegFeEg='17530101' $Filter AND PERSONAL.LegNume BETWEEN $primerLegajo AND $ultimoLegajo ORDER BY PERSONAL.LegFeEg, PERSONAL.LegNume";

        // $param = array();
        // $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        // $result = sqlsrv_query($link, $query, $param, $options);

        // while ($row = sqlsrv_fetch_array($result)) {
        //     $arraydb[] = ($row['LegNume']);
        // }

        // sqlsrv_free_stmt($result);
        // sqlsrv_close($link);

        // array_push($arraydb, $ultimoLegajo + 1);
        // $arrayForm[] = ($_POST['legajo']);

        // $resultado = array_diff($arraydb, $arrayForm[0]);

        // $numero = $primerLegajo - 1;

        // if (count($_POST['legajo']) > 1) {
        //     foreach ($arrayForm[0] as $key => $valor) {
        //         foreach ($resultado as $key => $value) {
        //             if ($valor < $value) {
        //                 if (($numero + 1) < ($value - 1)) {
        //                     $arrayLega[] =  (($numero + 1) . ',' . ($value - 1));
        //                 }
        //             }
        //             $numero = $value;
        //         }
        //         break;
        //     }
        // } else {
        //     $arrayLega[] = ($primerLegajo . ',' . $primerLegajo);
        // }
 
        foreach ($_POST['legajo'] as $key => $value) {
            $legajo = $value;
            $procesando = IngresarNovedad($aTipo, $legajo, $legajo, $FechaIni, $FechaFin, $aEmp, $aPlan, $aSucur, $aGrup, $aSect, $aSec2, $aLaboral, $aFicNove, $aFicJust, $aFicObse, $aFicHoras, $aCaus, $aFicCate);
            /** Envio a webservice */
            if (($procesando) == 'Terminado') {
                $mensaje = '(' . $count++ . ') Fin de Ingreso Novedad ' . $DescNovedad . '. <br>Legajo: ' . $value . ' desde ' . Fech_Format_Var($FechaIni, 'd/m/Y') . ' a ' . Fech_Format_Var($FechaFin, 'd/m/Y');
                EscribirArchivo("Ingreso_" . $microtime, "../novedades/logs/", $mensaje, false, true, false);
                audito_ch2("A", 'Alta Novedad Legajo '.$legajo.'. Desde: ' . FechaFormatVar($FechaIni, ('d/m/Y')).' a '.FechaFormatVar($FechaFin, ('d/m/Y')));
            } else {
                $mensaje = 'Error No Enviado';
                EscribirArchivo("Ingreso_" . $microtime, "../novedades/logs/", $mensaje, false, false, false);
                $data = array('status' => 'error', 'dato' => $mensaje);
                echo json_encode($data);
                exit;
            };
        }

        // foreach ($arrayLega as $key => $value) {

        //     $LegaDH = explode(",", $value);
        //     $LegaIni = $LegaDH[0];
        //     $LegaFin = $LegaDH[1];

        //     // $ExisteDH = ($LegaIni < $LegaFin) ? true: false;

        //     $ExisteDH = CountRegistrosMayorCero("SELECT PERSONAL.LegNume FROM PERSONAL WHERE PERSONAL.LegNume >= $LegaIni AND PERSONAL.LegNume <= $LegaFin AND PERSONAL.LegFeEg='17530101' $Filter ORDER BY PERSONAL.LegNume ASC");

        //     if ($ExisteDH) {
        //         // echo $LegaIni . ' a ' . $LegaFin . PHP_EOL;
        //         /** Recorremos el bucle de Legajos y enviamos peticiones al webservice */
        //         // $legajo = $value;
        //         $procesando = IngresarNovedad($aTipo, $LegaIni, $LegaFin, $FechaIni, $FechaFin, $aEmp, $aPlan, $aSucur, $aGrup, $aSect, $aSec2, $aLaboral, $aFicNove, $aFicJust, $aFicObse, $aFicHoras, $aCaus, $aFicCate);
        //         /** Envio a webservice */
        //         // $count=$count++; 
        //         if (($procesando) == 'Terminado') {
        //             $mensaje = '(' . $count++ . ') Fin de Ingreso Novedad ' . $DescNovedad . '. <br>Legajo: ' . $value . ' desde ' . Fech_Format_Var($FechaIni, 'd/m/Y') . ' a ' . Fech_Format_Var($FechaIni, 'd/m/Y');
        //             EscribirArchivo("Ingreso_" . $microtime, "../novedades/logs/", $mensaje, false, true, false);
        //         } else {
        //             $mensaje = 'Error No Enviado';
        //             EscribirArchivo("Ingreso_" . $microtime, "../novedades/logs/", $mensaje, false, false, false);
        //             $data = array('status' => 'error', 'dato' => $mensaje);
        //             echo json_encode($data);
        //             exit;
        //         };
        //     }
        // }
    } else {
        /** Si el tipo de ingreso es 1 (por Filtros) */
        $procesando = IngresarNovedad($aTipo, '1', '99999999', $FechaIni, $FechaFin, $aEmp, $aPlan, $aSucur, $aGrup, $aSect, $aSec2, $aLaboral, $aFicNove, $aFicJust, $aFicObse, $aFicHoras, $aCaus, $aFicCate);
        // $count=$count++; 
        if (($procesando) == 'Terminado') {
            $mensaje = '(' . $count++ . ') Fin de Ingreso NOVEDAD ' . $DescNovedad . '. <br>Desde ' . Fech_Format_Var($FechaIni, 'd/m/Y') . ' a ' . Fech_Format_Var($FechaFin, 'd/m/Y');
            EscribirArchivo("Ingreso_" . $microtime, "../novedades/logs/", $mensaje, false, true, false);
            audito_ch2("A", 'Alta Novedad Legajos. '.$DataFiltros.'Desde: ' . FechaFormatVar($FechaIni, ('d/m/Y')).' a '.FechaFormatVar($FechaFin, ('d/m/Y')));
        } else {
            $mensaje = 'Error No Enviado';
            EscribirArchivo("Ingreso_" . $microtime, "../novedades/logs/", $mensaje, false, false, false);
            $data = array('status' => 'error', 'dato' => $mensaje);
            echo json_encode($data);
            exit;
        };
    }

    $mensaje = 'Fin de Ingreso de Novedades';
    EscribirArchivo("Ingreso_" . $microtime, "../novedades/logs/", $mensaje, false, false, false);
    BorrarArchivosPDF('../novedades/logs/*.log');
    /** Borra los archivos log */
    $data = array('status' => 'ok', 'dato' => 'Fin de Ingreso de Novedades', 'log' => 'Ingreso_' . $microtime . '.log');
    echo json_encode($data);
    exit;
} else {
    $data = array('status' => 'error', 'dato' => 'ErrorPOST');
    echo json_encode($data);
    exit;
}
