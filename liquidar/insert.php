<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', '0');

$data      = array();
$FechaHora = date('Ymd H:i:s');

$_POST['alta_liquidacion']  = $_POST['alta_liquidacion'] ?? '';

/** GENERA LIQUIDACION */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta_liquidacion'] == 'true')) {
    
        $SelEmpresa  = test_input(FusNuloPOST('SelEmpresa',''));
        $SelPlanta   = test_input(FusNuloPOST('SelPlanta',''));
        $SelSector   = test_input(FusNuloPOST('SelSector',''));
        $SelSeccion  = test_input(FusNuloPOST('SelSeccion',''));
        $SelGrupo    = test_input(FusNuloPOST('SelGrupo',''));
        $SelSucursal = test_input(FusNuloPOST('SelSucursal',''));
    
        $LegajoDesde    = test_input(FusNuloPOST('LegaIni','1'));
        $LegajoHasta    = test_input(FusNuloPOST('LegaFin','999999999'));
        $TipoDePersonal = test_input(FusNuloPOST('Tipo','1'));
        $FechaDesde     = test_input(FusNuloPOST('FechaIni',date('d/m/Y')));
        $FechaHasta     = test_input(FusNuloPOST('FechaFin',date('d/m/Y')));
        $Empresa        = test_input(FusNuloPOST('Emp','0'));
        $Planta         = test_input(FusNuloPOST('Plan','0'));
        $Sucursal       = test_input(FusNuloPOST('Sect','0'));
        $Grupo          = test_input(FusNuloPOST('Sec2','0'));
        $Sector         = test_input(FusNuloPOST('Grup','0'));
        $Seccion        = test_input(FusNuloPOST('Sucur','0'));

        $FechaHora = date('Ymd H:i:s');

        $FechaIni = test_input(FechaString($_POST['FechaIni']));
        $FechaFin = test_input(FechaString($_POST['FechaFin']));

        if ((valida_campo($_POST['FechaIni'])) || (valida_campo($_POST['FechaFin']))|| (valida_campo($_POST['LegaIni']))|| (valida_campo($_POST['LegaFin']))|| (valida_campo($_POST['Tipo']))) {
            $data = array('status' => 'error', 'dato' => 'Campos requeridos!');
            echo json_encode($data);
            exit;
        };

        if ($FechaIni > $FechaFin) {
            $data = array('status' => 'error', 'dato' => 'Rango de Fecha Incorrecto!');
            echo json_encode($data);
            exit;
        };
        
        if ((($LegajoDesde) > ($LegajoHasta))) {
            $data = array('status' => 'error', 'dato' => 'Rango de Legajos Incorrecto.</span>');
            echo json_encode($data);
            exit;
        };

        if (!ValNumerico($_POST['LegaIni']) || (!ValNumerico($_POST['LegaFin']))) {
            $data = array('status' => 'error', 'dato' => 'Campos de Legajo deben ser Números');
            echo json_encode($data);
            exit;
        };

        $Liquidar=Liquidar($FechaDesde,$FechaHasta, $LegajoDesde, $LegajoHasta, $TipoDePersonal,$Empresa,$Planta,$Sucursal,$Grupo,$Sector,$Seccion);

        $arrEstruct = array(
            'Empresa'  => $SelEmpresa,
            'Planta'   => $SelPlanta,
            'Sector'   => $SelSector,
            'Seccion'  => $SelSeccion,
            'Grupo'    => $SelGrupo,
            'Sucursal' => $SelSucursal,
        );

        foreach ($arrEstruct as $key => $value) {
            $datas[] = ($value) ? '<br />'.$key.': <b>'.$value.'</b>' : '';
        }
        $datas = implode("",$datas);
    
        function dato_proceso($LegaIni,$LegaFin,$FechaIni,$FechaFin){
            $textoLegajo= ($LegaIni == $LegaFin) ? 'Legajo: '.$LegaIni : 'Legajos: '.$LegaIni.' a '.$LegaFin;
            $textoFecha= (FechaString($FechaIni) == FechaString($FechaFin)) ? '. Fecha: '.Fech_Format_Var($FechaIni,'d/m/Y') : '. Desde: '.Fech_Format_Var($FechaIni,'d/m/Y').' hasta '.Fech_Format_Var($FechaFin,'d/m/Y');
            $Dato = 'Liquidación. '. $textoLegajo. $textoFecha;
            return $Dato;
        }
    
        if(($Liquidar)=='Terminado'){
            $data = array('status' => 'ok', 'dato' => dato_proceso($LegajoDesde,$LegajoHasta,$FechaIni,$FechaFin).$datas);
            /** Insertar en tabla Auditor */
            $Dato=dato_proceso($LegajoDesde,$LegajoHasta,$FechaIni,$FechaFin);
            audito_ch('P', $Dato);
            /** */
            echo json_encode($data);
            exit;
        } else if(($Liquidar)=='No Hay Conexión'){

            $data = array('status' => 'error', 'dato' => '<span class="fw4">No Hay Conexión</span>');
            echo json_encode($data);
            exit;

        } else{
            $data = array('status' => 'error', 'dato' => 'Error');
            echo json_encode($data);
            exit;
        };
}
