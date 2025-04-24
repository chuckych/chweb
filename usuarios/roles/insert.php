<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();

FusNuloPOST('aFic', '0');
FusNuloPOST('mFic', '0');
FusNuloPOST('bFic', '0');
FusNuloPOST('aNov', '0');
FusNuloPOST('mNov', '0');
FusNuloPOST('bNov', '0');
FusNuloPOST('aHor', '0');
FusNuloPOST('mHor', '0');
FusNuloPOST('bHor', '0');
FusNuloPOST('aONov', '0');
FusNuloPOST('mONov', '0');
FusNuloPOST('bONov', '0');
FusNuloPOST('Proc', '0');
FusNuloPOST('aCit', '0');
FusNuloPOST('mCit', '0');
FusNuloPOST('bCit', '0');
FusNuloPOST('aTur', '0');
FusNuloPOST('mTur', '0');
FusNuloPOST('bTur', '0');
FusNuloPOST('act_abm', '');
FusNuloPOST('IdRol', '');

if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['act_abm'] == 'true')) {

    $aFic = (test_input($_POST['aFic']) == 'on') ? '1' : '0';
    $mFic = (test_input($_POST['mFic']) == 'on') ? '1' : '0';
    $bFic = (test_input($_POST['bFic']) == 'on') ? '1' : '0';
    $aNov = (test_input($_POST['aNov']) == 'on') ? '1' : '0';
    $mNov = (test_input($_POST['mNov']) == 'on') ? '1' : '0';
    $bNov = (test_input($_POST['bNov']) == 'on') ? '1' : '0';
    $aHor = (test_input($_POST['aHor']) == 'on') ? '1' : '0';
    $mHor = (test_input($_POST['mHor']) == 'on') ? '1' : '0';
    $bHor = (test_input($_POST['bHor']) == 'on') ? '1' : '0';
    $aONov = (test_input($_POST['aONov']) == 'on') ? '1' : '0';
    $mONov = (test_input($_POST['mONov']) == 'on') ? '1' : '0';
    $bONov = (test_input($_POST['bONov']) == 'on') ? '1' : '0';
    $Proc = (test_input($_POST['Proc']) == 'on') ? '1' : '0';
    $aCit = (test_input($_POST['aCit']) == 'on') ? '1' : '0';
    $mCit = (test_input($_POST['mCit']) == 'on') ? '1' : '0';
    $bCit = (test_input($_POST['bCit']) == 'on') ? '1' : '0';
    $aTur = (test_input($_POST['aTur']) == 'on') ? '1' : '0';
    $mTur = (test_input($_POST['mTur']) == 'on') ? '1' : '0';
    $bTur = (test_input($_POST['bTur']) == 'on') ? '1' : '0';
    $act_abm = (test_input($_POST['act_abm']) == 'on') ? '1' : '0';

    if (valida_campo($_POST['RecidRol'])) {
        PrintRespuestaJson('Error', 'Error.');
        exit;
    }
    ;

    $id_rol = (test_input($_POST['IdRol']));
    $recid_rol = (test_input($_POST['RecidRol']));
    $FechaHora = date('Y-m-d H:i:s');

    if ($id_rol) {
        $audCuenta = simple_pdoQuery("SELECT clientes.id 'id', roles.nombre as 'nombre_rol' FROM roles INNER JOIN clientes ON roles.cliente = clientes.id WHERE roles.id = $id_rol LIMIT 1");
    }
    $audCuenta['nombre_rol'] = $audCuenta['nombre_rol'] ?? '';
    $audCuenta['id'] = $audCuenta['id'] ?? '';

    $ABM = simple_pdoQuery("SELECT recid_rol FROM abm_roles WHERE recid_rol = '$recid_rol' LIMIT 1");
    if ($ABM) {
        $update = pdoQuery("UPDATE `abm_roles` SET `aFic`='$aFic', `mFic`='$mFic', `bFic`='$bFic', `aNov`='$aNov', `mNov`='$mNov', `bNov`='$bNov', `aHor`='$aHor', `mHor`='$mHor', `bHor`='$bHor', `aONov`='$aONov', `mONov`='$mONov', `bONov`='$bONov', `Proc`='$Proc', `aCit`='$aCit', `mCit`='$mCit', `bCit`='$bCit', `aTur`='$aTur', `mTur`='$mTur', `bTur`='$bTur', `FechaHora`='$FechaHora' WHERE recid_rol='$recid_rol'");
        if ($update) {
            PrintRespuestaJson('ok', 'Datos Actualizados.');
            auditoria("ABM Rol ($id_rol) $audCuenta[nombre_rol]", 'M', $audCuenta['id'], '1');
            exit;
        }
    } else {
        $insert = pdoQuery("INSERT INTO abm_roles( `id_rol`, `recid_rol`, `aFic`, `mFic`, `bFic`, `aNov`, `mNov`, `bNov`, `aHor`, `mHor`, `bHor`, `aONov`, `mONov`, `bONov`, `Proc`, `aCit`, `mCit`, `bCit`, `aTur`, `mTur`, `bTur`, `FechaHora` ) VALUES ( '$id_rol', '$recid_rol', '$aFic', '$mFic', '$bFic', '$aNov', '$mNov', '$bNov', '$aHor', '$mHor', '$bHor', '$aONov', '$mONov', '$bONov', '$Proc', '$aCit', '$mCit', '$bCit', '$aTur', '$mTur', '$bTur', '$FechaHora' )");
        if ($insert) {
            PrintRespuestaJson('ok', 'Datos Actualizados.');
            auditoria("ABM Rol ($id_rol) $audCuenta[nombre_rol]", 'A', $audCuenta['id'], '1');
            exit;
        }
    }
} else {
    PrintRespuestaJson('Error', 'Error.');
    exit;
}
