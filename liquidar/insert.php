<?php
require __DIR__ . '../../config/index.php';
session_start();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
ultimoacc();
secure_auth_ch_json();
E_ALL();

$data      = array();
$FechaHora = date('Ymd H:i:s');

$_POST['alta_liquidacion']  = $_POST['alta_liquidacion'] ?? '';

/** GENERA LIQUIDACION */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta_liquidacion'] == 'true')) {

    $SelEmpresa  = test_input(FusNuloPOST('SelEmpresa', ''));
    $SelPlanta   = test_input(FusNuloPOST('SelPlanta', ''));
    $SelSector   = test_input(FusNuloPOST('SelSector', ''));
    $SelSeccion  = test_input(FusNuloPOST('SelSeccion', ''));
    $SelGrupo    = test_input(FusNuloPOST('SelGrupo', ''));
    $SelSucursal = test_input(FusNuloPOST('SelSucursal', ''));

    $LegajoDesde    = test_input(FusNuloPOST('LegaIni', '1'));
    $LegajoHasta    = test_input(FusNuloPOST('LegaFin', '999999999'));
    $TipoDePersonal = test_input(FusNuloPOST('Tipo', '1'));
    // $FechaDesde     = test_input(FusNuloPOST('FechaIni', date('d/m/Y')));
    // $FechaHasta     = test_input(FusNuloPOST('FechaFin', date('d/m/Y')));
    $Empresa        = test_input(FusNuloPOST('Emp', '0'));
    $Planta         = test_input(FusNuloPOST('Plan', '0'));
    $Sucursal       = test_input(FusNuloPOST('Sect', '0'));
    $Grupo          = test_input(FusNuloPOST('Sec2', '0'));
    $Sector         = test_input(FusNuloPOST('Grup', '0'));
    $Seccion        = test_input(FusNuloPOST('Sucur', '0'));

    $FechaHora = date('Ymd H:i:s');

    // $FechaIni = test_input(FechaString($_POST['FechaIni']));
    // $FechaFin = test_input(FechaString($_POST['FechaFin']));

   

    if (valida_campo($_POST['_drLiq'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Campos Fechas requerido');
        echo json_encode($data);
        exit;
    };
    
    function dr_fechaformat($ddmmyyyy)
    {
        $fecha = date("d/m/Y", strtotime((str_replace("/", "-", $ddmmyyyy))));
        return $fecha;
    }
    
    if ($_POST['_drLiq']) {
        $DateRange = explode(' al ', $_POST['_drLiq']);
        $FechaDesde  = test_input(dr_fechaformat($DateRange[0]));
        $FechaHasta  = test_input(dr_fechaformat($DateRange[1]));
    }
    if ($_POST['_drLiq']) {
        $DateRange = explode(' al ', $_POST['_drLiq']);
        $FechaIni  = test_input(dr_fecha($DateRange[0]));
        $FechaFin  = test_input(dr_fecha($DateRange[1]));
    }
    

    if ((valida_campo($FechaDesde)) || (valida_campo($FechaHasta)) || (valida_campo($_POST['LegaIni'])) || (valida_campo($_POST['LegaFin'])) || (valida_campo($_POST['Tipo']))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campos requeridos!');
        echo json_encode($data);
        exit;
    };
    

    // if ($FechaDesde > $FechaHasta) {
    //     $data = array('status' => 'error', 'Mensaje' => 'Rango de Fecha Incorrecto!');
    //     echo json_encode($data);
    //     exit;
    // };
    

    if ((($LegajoDesde) > ($LegajoHasta))) {
        $data = array('status' => 'error', 'Mensaje' => 'Rango de Legajos Incorrecto.</span>');
        echo json_encode($data);
        exit;
    };

    if (!ValNumerico($_POST['LegaIni']) || (!ValNumerico($_POST['LegaFin']))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campos de Legajo deben ser Números');
        echo json_encode($data);
        exit;
    };

   

    $Liquidar = Liquidar($FechaDesde, $FechaHasta, $LegajoDesde, $LegajoHasta, $TipoDePersonal, $Empresa, $Planta, $Sucursal, $Grupo, $Sector, $Seccion);

    $arrEstruct = array(
        'Empresa'  => $SelEmpresa,
        'Planta'   => $SelPlanta,
        'Sector'   => $SelSector,
        'Seccion'  => $SelSeccion,
        'Grupo'    => $SelGrupo,
        'Sucursal' => $SelSucursal,
    );

    // $data = array('status' => 'error', 'Mensaje' => $FechaDesde.' '.$FechaHasta);
    // echo json_encode($data);
    // exit;

    foreach ($arrEstruct as $key => $value) {
        $datas[] = ($value) ? '<br />' . $key . ': <b>' . $value . '</b>' : '';
    }
    $datas = implode("", $datas);

    function dato_proceso($LegaIni, $LegaFin, $FechaIni, $FechaFin, $FechaDesde, $FechaHasta)
    {
        $textoLegajo = ($LegaIni == $LegaFin) ? 'Legajo: ' . $LegaIni : 'Legajos: ' . $LegaIni . ' a ' . $LegaFin;
        $textoFecha = ($FechaIni == $FechaFin) ? ' Fecha: ' . $FechaDesde : ' Desde: ' . $FechaDesde . ' hasta ' . $FechaHasta;
        $Dato = 'Liquidación. ' . $textoLegajo . $textoFecha;
        return $Dato;
    }

    if (($Liquidar) == 'Terminado') {
        $data = array('status' => 'ok', 'Mensaje' => dato_proceso($LegajoDesde, $LegajoHasta, $FechaIni, $FechaFin, $FechaDesde, $FechaHasta) . $datas, 'Tipo de Personal' => $TipoDePersonal);
        /** Insertar en tabla Auditor */
        $Dato = dato_proceso($LegajoDesde, $LegajoHasta, $FechaIni, $FechaFin, $FechaDesde, $FechaHasta);
        audito_ch('P', $Dato);
        /** */
        echo json_encode($data);
        exit;
    } else if (($Liquidar) == 'No Hay Conexión') {

        $data = array('status' => 'error', 'Mensaje' => '<span class="fw4">No Hay Conexión</span>');
        echo json_encode($data);
        exit;
    } else {
        $data = array('status' => 'error', 'Mensaje' => 'Error');
        echo json_encode($data);
        exit;
    };
}
