<?php
session_start(); // Inicia la sesión
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
header("Content-Type: application/json");
E_ALL();

if (($_SERVER["REQUEST_METHOD"] != "POST")) {
    PrintRespuestaJson('ERROR', 'Invalid REQUEST_METHOD');
    exit;
}

function emptyData($data, $err)
{
    return empty($data) ? PrintRespuestaJson('error', $err) . exit : '';
}
// sleep(1);
$_POST['EmpSubmit'] = ($_POST['EmpSubmit']) ?? '';
$_POST['EstSubmit'] = ($_POST['EstSubmit']) ?? '';
$_POST['ProcSubmit'] = ($_POST['ProcSubmit']) ?? '';
$_POST['PlanoSubmit'] = ($_POST['PlanoSubmit']) ?? '';
$_POST['PlantSubmit'] = ($_POST['PlantSubmit']) ?? '';
$_POST['plantillaProc'] = ($_POST['plantillaProc']) ?? '';
$_POST['plantillaPlano'] = ($_POST['plantillaPlano']) ?? '';
$_POST['ProySubmit'] = ($_POST['ProySubmit']) ?? '';
$_POST['conf'] = ($_POST['conf']) ?? '';
$_POST['setDescAll'] = ($_POST['setDescAll']) ?? '';
$_POST['setDesc'] = ($_POST['setDesc']) ?? '';

$FechaHora = FechaHora2();


if ($_POST['conf']) {
    $_POST['getConf'] = $_POST['getConf'] ?? '';
    $_POST['setConf'] = $_POST['setConf'] ?? '';
    $_POST['ProcPendTar'] = $_POST['ProcPendTar'] ?? '0';
    $_POST['ProcRedTar'] = $_POST['ProcRedTar'] ?? '0';
    $_POST['ProcDescTar'] = $_POST['ProcDescTar'] ?? '0';
    $_POST['HoraCierre'] = $_POST['HoraCierre'] ?? '00:00';
    $_POST['LimitTar'] = $_POST['LimitTar'] ?? '00:00';
    $_POST['MinimoDesc'] = $_POST['MinimoDesc'] ?? '';
    $_POST['RecRedTar'] = $_POST['RecRedTar'] ?? '';

    if ($_POST['getConf']) {
        $confTar = (getDataIni('confTarea.php'));
        echo json_encode($confTar);
        exit;
    }
    if ($_POST['setConf']) {
        $assoc = array(
            'confTar' => array(
                'ProcPendTar' => $_POST['ProcPendTar'],
                'ProcDescTar' => $_POST['ProcDescTar'],
                'ProcRedTar' => $_POST['ProcRedTar'],
                'HoraCierre' => $_POST['HoraCierre'],
                'MinimoDesc' => $_POST['MinimoDesc'],
                'LimitTar' => $_POST['LimitTar'],
                'RecRedTar' => $_POST['RecRedTar'],
            ),
        );
        confTar($assoc, 'confTarea.php');
        PrintRespuestaJson('ok', 'Datos Guardados correctamente.');
        exit;
    }
    exit;
}

$_POST['toExcel'] = ($_POST['toExcel']) ?? '';
$_POST['EmpDesc'] = ($_POST['EmpDesc']) ?? '';
$_POST['EmpTel'] = ($_POST['EmpTel']) ?? '';
$_POST['EmpObs'] = ($_POST['EmpObs']) ?? '';
$_POST['EmpID'] = ($_POST['EmpID']) ?? '';

$_POST['ProcDesc'] = ($_POST['ProcDesc']) ?? '';
$_POST['ProcCost'] = ($_POST['ProcCost']) ?? '';
$_POST['ProcObs'] = ($_POST['ProcObs']) ?? '';
$_POST['ProcID'] = ($_POST['ProcID']) ?? '';

$_POST['PlanoDesc'] = ($_POST['PlanoDesc']) ?? '';
$_POST['PlanoCod'] = ($_POST['PlanoCod']) ?? '';
$_POST['PlanoObs'] = ($_POST['PlanoObs']) ?? '';
$_POST['PlanoID'] = ($_POST['PlanoID']) ?? '';

$_POST['PlantID'] = ($_POST['PlantID']) ?? '';
$_POST['PlantDesc'] = ($_POST['PlantDesc']) ?? '';
$_POST['PlantMod'] = ($_POST['PlantMod']) ?? '';

$_POST['PlaPlanoID'] = ($_POST['PlaPlanoID']) ?? '';
$_POST['PlanPlaDesc'] = ($_POST['PlanPlaDesc']) ?? '';
$_POST['checkPlanos'] = ($_POST['checkPlanos']) ?? '';

$_POST['EstDesc'] = ($_POST['EstDesc']) ?? '';
$_POST['EstColor'] = ($_POST['EstColor']) ?? '';
$_POST['EstID'] = ($_POST['EstID']) ?? '';
$_POST['EstTipo'] = ($_POST['EstTipo']) ?? '';

$_POST['checkProc'] = ($_POST['checkProc']) ?? '';
$_POST['PlaProPlan'] = ($_POST['PlaProPlan']) ?? '';
$_POST['PlaProDesc'] = ($_POST['PlaProDesc']) ?? '';

$_POST['EmpDesc'] = test_input($_POST['EmpDesc']) ?? '';
$_POST['EmpTel'] = test_input($_POST['EmpTel']) ?? '';
$_POST['EmpObs'] = test_input($_POST['EmpObs']) ?? '';
$_POST['EmpID'] = test_input($_POST['EmpID']) ?? '';

$_POST['ProcDesc'] = test_input($_POST['ProcDesc']) ?? '';
$_POST['ProcCost'] = test_input($_POST['ProcCost']) ?? '';
$_POST['ProcObs'] = test_input($_POST['ProcObs']) ?? '';
$_POST['ProcID'] = test_input($_POST['ProcID']) ?? '';

$_POST['PlantID'] = test_input($_POST['PlantID']) ?? '';
$_POST['PlantDesc'] = test_input($_POST['PlantDesc']) ?? '';

$_POST['PlanoID'] = test_input($_POST['PlanoID']) ?? '';
$_POST['PlanoDesc'] = test_input($_POST['PlanoDesc']) ?? '';
$_POST['PlanoCod'] = test_input($_POST['PlanoCod']) ?? '';
$_POST['PlanoObs'] = test_input($_POST['PlanoObs']) ?? '';
$_POST['PlanoEsta'] = ($_POST['PlanoEsta']) ?? '';
$PlanoEsta = ($_POST['PlanoEsta'] == 'on') ? '0' : '1';

$_POST['EstDesc'] = test_input($_POST['EstDesc']) ?? '';
$_POST['EstColor'] = test_input($_POST['EstColor']) ?? '';
$_POST['EstID'] = test_input($_POST['EstID']) ?? '';
$_POST['EstTipo'] = test_input($_POST['EstTipo']) ?? 'Abierto';

$_POST['checkProc'] = test_input($_POST['checkProc']) ?? '';
$_POST['PlaProPlan'] = test_input($_POST['PlaProPlan']) ?? '';
$_POST['PlaProDesc'] = test_input($_POST['PlaProDesc']) ?? '';

$_POST['ProyIniFin'] = $_POST['ProyIniFin'] ?? '';
$_POST['ProyDesc'] = $_POST['ProyDesc'] ?? '';
$_POST['ProyNom'] = $_POST['ProyNom'] ?? '';
$_POST['ProyEmpr'] = $_POST['ProyEmpr'] ?? '';
$_POST['ProyPlant'] = $_POST['ProyPlant'] ?? '';
$_POST['ProyResp'] = $_POST['ProyResp'] ?? '';
$_POST['ProyEsta'] = $_POST['ProyEsta'] ?? '';
$_POST['ProyObs'] = $_POST['ProyObs'] ?? '';
$_POST['ProyPlantPlanos'] = $_POST['ProyPlantPlanos'] ?? 'NULL';
$_POST['PlantPlano'] = $_POST['PlantPlano'] ?? '';
$_POST['ProyLiPlanos'] = $_POST['ProyLiPlanos'] ?? '';
$_POST['ProyIniFin'] = test_input($_POST['ProyIniFin']);
$_POST['ProyDesc'] = test_input($_POST['ProyDesc']);
$_POST['ProyNom'] = test_input($_POST['ProyNom']);
$_POST['ProyEmpr'] = test_input($_POST['ProyEmpr']);
$_POST['ProyPlant'] = test_input($_POST['ProyPlant']);
$_POST['ProyResp'] = test_input($_POST['ProyResp']);
$_POST['ProyEsta'] = test_input($_POST['ProyEsta']);
$_POST['ProyObs'] = test_input($_POST['ProyObs']);
$_POST['PlantPlano'] = test_input($_POST['PlantPlano']);

$_SESSION['RECID_USER'] = $_SESSION['RECID_USER'] ?? '';
$_SESSION['ID_CLIENTE'] = $_SESSION['ID_CLIENTE'] ?? '';
//print_r("SELECT usuarios.id AS 'id_user', roles.nombre AS 'nombre_rol' FROM usuarios INNER JOIN roles ON usuarios.rol=roles.id WHERE usuarios.recid='$_SESSION[RECID_USER]' ORDER BY usuarios.fecha_alta DESC LIMIT 1").exit;

$dataUser = simple_pdoQuery("SELECT usuarios.id AS 'id_user', usuarios.cliente as 'ID_CLIENTE', roles.nombre AS 'nombre_rol' FROM usuarios INNER JOIN roles ON usuarios.rol=roles.id WHERE usuarios.recid='$_SESSION[RECID_USER]' ORDER BY usuarios.fecha_alta DESC LIMIT 1");

// print_r($dataUser).exit;

if ($_POST['EmpSubmit'] == 'alta') {
    $checkEmpDesc = count_pdoQuery("SELECT 1 FROM proy_empresas WHERE proy_empresas.EmpDesc = '$_POST[EmpDesc]' AND proy_empresas.Cliente = '$_SESSION[ID_CLIENTE]' LIMIT 1");
    if ($checkEmpDesc) {
        PrintRespuestaJson('ERROR', 'Ya existe una empresa con ese nombre');
        exit;
    }
    $query = "INSERT INTO proy_empresas (EmpDesc, EmpTel, EmpObs, EmpAlta, Cliente) VALUES ('$_POST[EmpDesc]', '$_POST[EmpTel]', '$_POST[EmpObs]', '$FechaHora', '$_SESSION[ID_CLIENTE]')";
    $insertEmpresa = pdoQuery($query);

    if ($insertEmpresa) {
        $dataEmpresa = simple_pdoQuery("SELECT proy_empresas.EmpID AS 'id_empresa', proy_empresas.EmpDesc AS 'nombre_empresa' FROM proy_empresas WHERE proy_empresas.EmpDesc = '$_POST[EmpDesc]' AND proy_empresas.Cliente = '$_SESSION[ID_CLIENTE]' ORDER BY proy_empresas.EmpAlta DESC LIMIT 1");
        auditoria("Proyectos - Empresa: ($dataEmpresa[id_empresa]) $_POST[EmpDesc].", 'A', '', '42');
        PrintRespuestaJson('ok', 'Empresa agregada correctamente.');
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al cargar la empresa');
        exit;
    }
} else if ($_POST['EmpSubmit'] == 'mod') {
    $checkEmpDesc = count_pdoQuery("SELECT 1 FROM proy_empresas WHERE proy_empresas.EmpDesc = '$_POST[EmpDesc]' AND proy_empresas.EmpID != '$_POST[EmpID]' AND proy_empresas.Cliente = '$_SESSION[ID_CLIENTE]' LIMIT 1");
    if ($checkEmpDesc) {
        PrintRespuestaJson('ERROR', 'Ya existe una empresa con ese nombre');
        exit;
    }
    $query = "UPDATE proy_empresas SET EmpDesc = '$_POST[EmpDesc]', EmpTel = '$_POST[EmpTel]', EmpObs = '$_POST[EmpObs]' WHERE EmpID = '$_POST[EmpID]'";
    $updateEmpresa = pdoQuery($query);

    if ($updateEmpresa) {
        auditoria("Proyectos - Empresa: ($_POST[EmpID]) $_POST[EmpDesc].", 'B', '', '42');
        PrintRespuestaJson('ok', 'Empresa modificada correctamente.');
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al cargar la empresa');
        exit;
    }
} else if ($_POST['EmpSubmit'] == 'baja') {
    $query = "DELETE FROM proy_empresas WHERE EmpID = '$_POST[EmpID]'";
    $deleteEmpresa = pdoQuery($query);
    if ($deleteEmpresa) {
        auditoria("Proyectos - Empresa: ($_POST[EmpID]) $_POST[EmpDesc].", 'B', '', '42');
        PrintRespuestaJson('ok', "Empresa eliminada correctamente.<div class='pt-1 font-weight-bold'>$_POST[EmpDesc]<div>");
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al eliminar la empresa');
        exit;
    }
} else if ($_POST['EstSubmit'] == 'alta') {

    // PrintRespuestaJson('error', $_POST['EstTipo']);
    // exit;

    $checkEmpDesc = count_pdoQuery("SELECT 1 FROM proy_estados WHERE proy_estados.EstDesc = '$_POST[EstDesc]' AND proy_estados.Cliente = '$_SESSION[ID_CLIENTE]' LIMIT 1");
    if ($checkEmpDesc) {
        PrintRespuestaJson('ERROR', 'Ya existe un estado con ese nombre');
        exit;
    }
    $query = "INSERT INTO proy_estados (EstDesc, EstColor, EstTipo, EstAlta, Cliente) VALUES ('$_POST[EstDesc]', '$_POST[EstColor]', '$_POST[EstTipo]', '$FechaHora', '$_SESSION[ID_CLIENTE]')";
    $insertEstado = pdoQuery($query);

    if ($insertEstado) {
        $dataEstado = simple_pdoQuery("SELECT proy_estados.EstID AS 'id_estado', proy_estados.EstDesc AS 'nombre_estado' FROM proy_estados WHERE proy_estados.EstDesc = '$_POST[EstDesc]' AND proy_estados.Cliente = '$_SESSION[ID_CLIENTE]' ORDER BY proy_estados.EstAlta DESC LIMIT 1");
        auditoria("Proyectos - Estado: ($dataEstado[id_estado]) $_POST[EstDesc].", 'A', '', '35');
        PrintRespuestaJson('ok', 'Estado creado correctamente.');
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al cargar la empresa');
        exit;
    }
} else if ($_POST['EstSubmit'] == 'mod') {

    $checkEmpDesc = count_pdoQuery("SELECT 1 FROM proy_estados WHERE proy_estados.EstDesc = '$_POST[EstDesc]' AND proy_estados.EstID != '$_POST[EstID]' AND proy_estados.Cliente = '$_SESSION[ID_CLIENTE]' LIMIT 1");
    if ($checkEmpDesc) {
        PrintRespuestaJson('ERROR', 'Ya existe un estado con ese nombre');
        exit;
    }
    $query = "UPDATE proy_estados SET EstDesc = '$_POST[EstDesc]', EstColor = '$_POST[EstColor]', EstTipo = '$_POST[EstTipo]' WHERE EstID = '$_POST[EstID]'";
    $updateEstado = pdoQuery($query);

    if ($updateEstado) {
        auditoria("Proyectos - Estado: ($_POST[EstID]) $_POST[EstDesc].", 'M', '', '35');
        PrintRespuestaJson('ok', 'Estado modificado correctamente.');
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al modificar el estado.');
        exit;
    }
} else if ($_POST['EstSubmit'] == 'baja') {
    $query = "DELETE FROM proy_estados WHERE EstID = '$_POST[EstID]'";
    $deleteEstado = pdoQuery($query);
    if ($deleteEstado) {
        auditoria("Proyectos - Estado: ($_POST[EstID]) $_POST[EstDesc].", 'B', '', '35');
        PrintRespuestaJson('ok', "Estado eliminado correctamente.<div class='pt-1 font-weight-bold'>$_POST[EstDesc]<div>");
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al eliminar el estado');
        exit;
    }
} else if ($_POST['ProcSubmit'] == 'alta') {
    $checkProceDesc = count_pdoQuery("SELECT 1 FROM proy_proceso WHERE proy_proceso.ProcDesc = '$_POST[ProcDesc]' AND proy_proceso.Cliente = '$_SESSION[ID_CLIENTE]' LIMIT 1");
    if ($checkProceDesc) {
        PrintRespuestaJson('ERROR', 'Ya existe un proceso con ese nombre');
        exit;
    }
    $query = "INSERT INTO proy_proceso (ProcDesc, ProcCost, ProcObs, ProcAlta, Cliente) VALUES ('$_POST[ProcDesc]', '$_POST[ProcCost]', '$_POST[ProcObs]', '$FechaHora', '$_SESSION[ID_CLIENTE]')";
    $insertProceso = pdoQuery($query);

    if ($insertProceso) {
        $dataProcesos = simple_pdoQuery("SELECT proy_proceso.ProcID AS 'id_proceso', proy_proceso.ProcDesc AS 'nombre_proceso' FROM proy_proceso WHERE proy_proceso.ProcDesc = '$_POST[ProcDesc]' AND proy_proceso.Cliente = '$_SESSION[ID_CLIENTE]' ORDER BY proy_proceso.ProcAlta DESC LIMIT 1");
        auditoria("Proyectos - Proceso: ($dataProcesos[id_proceso]) $_POST[ProcDesc].", 'A', '', '38');
        PrintRespuestaJson('ok', 'Proceso agregado correctamente.');
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al cargar el proceso.');
        exit;
    }
} else if ($_POST['ProcSubmit'] == 'mod') {

    $checkProcDesc = count_pdoQuery("SELECT 1 FROM proy_proceso WHERE proy_proceso.ProcDesc = '$_POST[ProcDesc]' AND proy_proceso.ProcID != '$_POST[ProcID]' AND proy_proceso.Cliente = '$_SESSION[ID_CLIENTE]' LIMIT 1");
    if ($checkProcDesc) {
        PrintRespuestaJson('ERROR', 'Ya existe un proceso con ese nombre');
        exit;
    }
    $query = "UPDATE proy_proceso SET ProcDesc = '$_POST[ProcDesc]', ProcCost = '$_POST[ProcCost]', ProcObs = '$_POST[ProcObs]' WHERE ProcID = '$_POST[ProcID]'";
    $updateProceso = pdoQuery($query);

    if ($updateProceso) {
        auditoria("Proyectos - Proceso: ($_POST[ProcID]) $_POST[ProcDesc].", 'M', '', '38');
        PrintRespuestaJson('ok', 'Proceso modificado correctamente.');
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al modificar el proceso.');
        exit;
    }
} else if ($_POST['ProcSubmit'] == 'baja') {
    $query = "DELETE FROM proy_proceso WHERE ProcID = '$_POST[ProcID]'";
    $deleteProceso = pdoQuery($query);
    if ($deleteProceso) {
        auditoria("Proyectos - Proceso: ($_POST[ProcID]) $_POST[ProcDesc].", 'B', '', '38');
        PrintRespuestaJson('ok', "Proceso eliminado correctamente.<div class='pt-1 font-weight-bold'>$_POST[ProcDesc]<div>");
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al eliminar el Proceso');
        exit;
    }
} else if ($_POST['PlanoSubmit'] == 'alta') {
    $checkPlanoDesc = count_pdoQuery("SELECT 1 FROM proy_planos WHERE proy_planos.PlanoDesc = '$_POST[PlanoDesc]' AND proy_planos.Cliente = '$_SESSION[ID_CLIENTE]' LIMIT 1");
    if ($checkPlanoDesc) {
        PrintRespuestaJson('ERROR', 'Ya existe un plano con ese nombre');
        exit;
    }
    $query = "INSERT INTO proy_planos (PlanoDesc, PlanoCod, PlanoObs, PlanoEsta, PlanoAlta, Cliente) VALUES ('$_POST[PlanoDesc]', '$_POST[PlanoCod]', '$_POST[PlanoObs]', '$PlanoEsta', '$FechaHora', '$_SESSION[ID_CLIENTE]')";
    $insertPlano = pdoQuery($query);

    if ($insertPlano) {
        $dataPlanos = simple_pdoQuery("SELECT proy_planos.PlanoID AS 'id_plano',  proy_planos.PlanoCod AS 'PlanoCod', proy_planos.PlanoDesc AS 'nombre_plano' FROM proy_planos WHERE proy_planos.PlanoDesc = '$_POST[PlanoDesc]' AND proy_planos.Cliente = '$_SESSION[ID_CLIENTE]' ORDER BY proy_planos.PlanoAlta DESC LIMIT 1");

        auditoria("Proyectos - Planos: ($dataPlanos[id_plano]) $_POST[PlanoDesc].", 'A', '', '41');

        if ($_POST['PlantPlano']) { // Si viene el ID de plantilla Plano
            $readPlantilla = simple_pdoQuery("SELECT PlaPlanos, PlaPlanoID, PlantDesc FROM proy_plantilla_plano INNER JOIN proy_plantillas ON proy_plantilla_plano.PlaPlanoID = proy_plantillas.PlantID WHERE PlaPlanoID='$_POST[PlantPlano]' LIMIT 1");

            if ($readPlantilla['PlaPlanos']) {
                $PlaPlanos = $readPlantilla['PlaPlanos'] . ',' . $dataPlanos['id_plano']; // concateno el id a la plantilla
            } else {
                $PlaPlanos = $dataPlanos['id_plano']; // concateno el id a la plantilla
            }
            pdoQuery("UPDATE proy_plantilla_plano SET PlaPlanos='$PlaPlanos' WHERE PlaPlanoID = '$_POST[PlantPlano]'");
            auditoria("Proyectos - Plantillas Planos: ($readPlantilla[PlaPlanoID]) $readPlantilla[PlantDesc].", 'M', '', '44');
        }

        $data = array('status' => 'ok', 'Mensaje' => 'Plano agregado correctamente.', 'dataPlano' => $dataPlanos);
        echo json_encode($data);
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al cargar el plano.');
        exit;
    }
} else if ($_POST['PlanoSubmit'] == 'mod') {

    $checkPlanoDesc = count_pdoQuery("SELECT 1 FROM proy_planos WHERE proy_planos.PlanoDesc = '$_POST[PlanoDesc]' AND proy_planos.PlanoID != '$_POST[PlanoID]' AND proy_planos.Cliente = '$_SESSION[ID_CLIENTE]' LIMIT 1");
    if ($checkPlanoDesc) {
        PrintRespuestaJson('ERROR', 'Ya existe un plano con ese nombre');
        exit;
    }
    $query = "UPDATE `proy_planos` SET PlanoDesc = '$_POST[PlanoDesc]', PlanoCod = '$_POST[PlanoCod]', PlanoObs = '$_POST[PlanoObs]', PlanoEsta = '$PlanoEsta' WHERE PlanoID = '$_POST[PlanoID]'";
    $updatePlano = pdoQuery($query);

    if ($updatePlano) {
        auditoria("Proyectos - Planos: ($_POST[PlanoID]) $_POST[PlanoDesc].", 'M', '', '41');
        PrintRespuestaJson('ok', 'Plano modificado correctamente.');
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al modificar el plano.');
        exit;
    }
} else if ($_POST['PlanoSubmit'] == 'baja') {
    $query = "DELETE FROM proy_planos WHERE PlanoID = '$_POST[PlanoID]'";
    $deletePlano = pdoQuery($query);
    if ($deletePlano) {
        auditoria("Proyectos - Planos: ($_POST[PlanoID]) $_POST[PlanoDesc].", 'B', '', '41');
        PrintRespuestaJson('ok', "Plano eliminado correctamente.<div class='pt-1 font-weight-bold'>$_POST[PlanoDesc]<div>");
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al eliminar el plano');
        exit;
    }
} else if ($_POST['PlantSubmit'] == 'alta') {

    if (empty($_POST['PlantMod'])) {
        PrintRespuestaJson('error', "Debe seleccionar un módulo");
        exit;
    }

    $checkPlanDesc = count_pdoQuery("SELECT 1 FROM proy_plantillas WHERE proy_plantillas.PlantDesc = '$_POST[PlantDesc]' AND proy_plantillas.PlantMod = '$_POST[PlantMod]' AND proy_plantillas.Cliente = '$dataUser[ID_CLIENTE]' LIMIT 1");

    if ($checkPlanDesc) {
        PrintRespuestaJson('ERROR', 'Ya existe una plantilla con ese nombre');
        exit;
    }

    $query = "INSERT INTO proy_plantillas (PlantDesc, PlantMod, PlantAlta, Cliente) VALUES ('$_POST[PlantDesc]', '$_POST[PlantMod]', '$FechaHora', '$dataUser[ID_CLIENTE]')";

    $insertPlantilla = pdoQuery($query);

    switch ($_POST['PlantMod']) {
        case '44':
            $PlantillaModulo = 'Plantilla Planos';
            break;
        case '40':
            $PlantillaModulo = 'Plantilla Procesos';
            break;
    }

    if ($insertPlantilla) {
        $dataPlantilla = simple_pdoQuery("SELECT proy_plantillas.PlantID AS 'id_plantilla', proy_plantillas.PlantDesc AS 'nombre_plantilla' FROM proy_plantillas WHERE proy_plantillas.PlantDesc = '$_POST[PlantDesc]' AND proy_plantillas.Cliente = '$_SESSION[ID_CLIENTE]' ORDER BY proy_plantillas.PlantAlta DESC LIMIT 1");
        auditoria("Proyectos - $PlantillaModulo: ($dataPlantilla[id_plantilla]) $_POST[PlantDesc].", 'A', '', $_POST['PlantMod']);
        PrintRespuestaJson('ok', "$PlantillaModulo creada correctamente.");
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al cargar la plantilla');
        exit;
    }
} else if ($_POST['PlantSubmit'] == 'mod') {

    $checkPlantDesc = count_pdoQuery("SELECT 1 FROM proy_plantillas WHERE proy_plantillas.PlantDesc = '$_POST[PlantDesc]' AND proy_plantillas.PlantID != '$_POST[PlantID]' AND proy_plantillas.Cliente = '$_SESSION[ID_CLIENTE]' LIMIT 1");
    if ($checkPlantDesc) {
        PrintRespuestaJson('ERROR', 'Ya existe una plantilla con ese nombre');
        exit;
    }
    $query = "UPDATE proy_plantillas SET PlantDesc = '$_POST[PlantDesc]' WHERE PlantID = '$_POST[PlantID]'";
    $updatePlano = pdoQuery($query);

    switch ($_POST['PlantMod']) {
        case '44':
            $PlantillaModulo = 'Plantilla Planos';
            break;
        case '40':
            $PlantillaModulo = 'Plantilla Procesos';
            break;
    }

    if ($updatePlano) {
        auditoria("Proyectos - $PlantillaModulo: ($_POST[PlantID]) $_POST[PlantDesc].", 'M', '', $_POST['PlantMod']);
        PrintRespuestaJson('ok', "Plantillas modificado correctamente.");
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al modificar la plantilla.');
        exit;
    }
} else if ($_POST['PlantSubmit'] == 'baja') {
    $query = "DELETE FROM proy_plantillas WHERE PlantID = '$_POST[PlantID]'";
    $deletePlantilla = pdoQuery($query);
    switch ($_POST['PlantMod']) {
        case '44':
            $PlantillaModulo = 'Plantilla Planos';
            break;
        case '40':
            $PlantillaModulo = 'Plantilla Procesos';
            break;
    }
    if ($deletePlantilla) {
        auditoria("Proyectos - $PlantillaModulo: ($_POST[PlantID]) $_POST[PlantDesc].", 'B', '', $_POST['PlantMod']);
        PrintRespuestaJson('ok', "Plantilla eliminada correctamente.<div class='pt-1 font-weight-bold'>$_POST[PlantDesc]<div>");
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al eliminar la plantilla');
        exit;
    }
} else if ($_POST['plantillaProc'] == true) {

    $_POST['actualizar'] = ($_POST['actualizar']) ?? '';

    if (empty($_POST['PlaProPlan'])) {
        PrintRespuestaJson('error', "Debe Seleccionar un Plantilla");
        exit;
    }

    $checkProc = count_pdoQuery("SELECT 1 FROM proy_plantilla_proc WHERE proy_plantilla_proc.PlaProPlan = '$_POST[PlaProPlan]' LIMIT 1");
    $valores = json_decode($_POST['checkProc']);
    $valores = implode(',', ($valores));

    if ($checkProc) {
        $query = "UPDATE proy_plantilla_proc SET proy_plantilla_proc.PlaProcesos = '$valores' WHERE proy_plantilla_proc.PlaProPlan = '$_POST[PlaProPlan]'";
        $update = pdoQuery($query);
        if ($update) {
            ($_POST['actualizar'] == 'true') ? exit : '';
            auditoria("Proyectos - Procesos Plantilla: ($_POST[PlaProPlan]) $_POST[PlaProDesc]. Se actualizaron valores", 'M', '', '40');
            PrintRespuestaJson('ok', "Procesos asignados correctamente a la plantilla<div class='font-weight-bold lh-lg'>$_POST[PlaProDesc].</div>");
            exit;
        } else {
            PrintRespuestaJson('ERROR', 'Error al modificar la plantilla.');
            exit;
        }
    } else {
        $query = "INSERT INTO proy_plantilla_proc(PlaProPlan, PlaProcesos, PlaProtAlta) VALUES ('$_POST[PlaProPlan]', '$valores', '$FechaHora')";
        $insert = pdoQuery($query);
        if ($insert) {

            ($_POST['actualizar'] == 'true') ? exit : '';

            auditoria("Proyectos - Procesos Plantilla: ($_POST[PlaProPlan]) $_POST[PlaProDesc]. Se agregaron valores", 'A', '', '40');
            PrintRespuestaJson('ok', "Procesos asignados correctamente a la plantilla<div class='font-weight-bold lh-lg'>$_POST[PlaProDesc].</div>");
            exit;
        } else {
            PrintRespuestaJson('ERROR', 'Error al modificar la plantilla.');
            exit;
        }
    }
} else if ($_POST['plantillaPlano'] == true) {

    $_POST['actualizar'] = ($_POST['actualizar']) ?? '';

    if (empty($_POST['PlaPlanoID'])) {
        PrintRespuestaJson('error', "Debe Seleccionar un Plantilla");
        exit;
    }

    $checkPlanos = count_pdoQuery("SELECT 1 FROM proy_plantilla_plano WHERE proy_plantilla_plano.PlaPlanoID = '$_POST[PlaPlanoID]' LIMIT 1");
    $valores = json_decode($_POST['checkPlanos']);
    $valores = implode(',', ($valores));

    if ($checkPlanos) {
        $query = "UPDATE proy_plantilla_plano SET proy_plantilla_plano.PlaPlanos = '$valores' WHERE proy_plantilla_plano.PlaPlanoID = '$_POST[PlaPlanoID]'";
        $update = pdoQuery($query);
        if ($update) {
            ($_POST['actualizar'] == 'true') ? exit : '';
            auditoria("Proyectos - Planos Plantilla: ($_POST[PlaPlanoID]) $_POST[PlanPlaDesc]. Se actualizaron valores", 'M', '', '44');
            PrintRespuestaJson('ok', "Planos asignados correctamente a la plantilla<div class='font-weight-bold lh-lg'>$_POST[PlanPlaDesc].</div>");
            exit;
        } else {
            PrintRespuestaJson('ERROR', 'Error al modificar la plantilla.');
            exit;
        }
    } else {
        $query = "INSERT INTO proy_plantilla_plano(PlaPlanoID, PlaPlanos, PlaPlanoAlta) VALUES ('$_POST[PlaPlanoID]', '$valores', '$FechaHora')";
        $insert = pdoQuery($query);
        if ($insert) {

            ($_POST['actualizar'] == 'true') ? exit : '';

            auditoria("Proyectos - Planos Plantilla: ($_POST[PlaPlanoID]) $_POST[PlanPlaDesc]. Se agregaron valores", 'A', '', '44');
            PrintRespuestaJson('ok', "Planos asignados correctamente a la plantilla<div class='font-weight-bold lh-lg'>$_POST[PlanPlaDesc].</div>");
            exit;
        } else {
            PrintRespuestaJson('ERROR', 'Error al modificar la plantilla.');
            exit;
        }
    }
} else if ($_POST['ProySubmit'] == 'alta') {
    // print_r($dataUser['ID_CLIENTE']).exit;
    (valida_campo($_POST['ProyIniFin'])) ? PrintRespuestaJson('Error', 'Campo Inicio / Fin es requerido') . exit : '';
    (valida_campo($_POST['ProyEsta'])) ? PrintRespuestaJson('Error', 'Campo Estado es requerido') . exit : '';
    (valida_campo($_POST['ProyResp'])) ? PrintRespuestaJson('Error', 'Campo Responsable es requerido') . exit : '';
    (valida_campo($_POST['ProyPlant'])) ? PrintRespuestaJson('Error', 'Campo Planta es requerido') . exit : '';
    (valida_campo($_POST['ProyEmpr'])) ? PrintRespuestaJson('Error', 'Campo Empresa es requerido') . exit : '';
    (valida_campo($_POST['ProyNom'])) ? PrintRespuestaJson('Error', 'Campo Nombre es requerido') . exit : '';
    (valida_campo($_POST['ProyDesc'])) ? PrintRespuestaJson('Error', 'Campo Descripción es requerido') . exit : '';

    $checkProyNom = count_pdoQuery("SELECT 1 FROM proy_proyectos WHERE proy_proyectos.ProyNom = '$_POST[ProyNom]' AND proy_proyectos.Cliente = '$_SESSION[ID_CLIENTE]' LIMIT 1");
    if ($checkProyNom) {
        PrintRespuestaJson('ERROR', 'Ya existe un proyecto con ese nombre');
        exit;
    }
    if (isset($_POST['ProyIniFin']) && !empty($_POST['ProyIniFin'])) {
        $DateRange = explode(' al ', $_POST['ProyIniFin']);
        $ProyIni = test_input(dr_fecha($DateRange[0]));
        $ProyFin = test_input(dr_fecha($DateRange[1]));
    } else {
        $ProyIni = date('Ymd');
        $ProyFin = date('Ymd');
    }

    /** Inicio de proceso de asignacion de planos */
    require 'crud_procesarPlantillaProy.php';
    /** */

    $query = "INSERT INTO proy_proyectos (ProyNom, ProyDesc, ProyEmpr, ProyPlant, ProyPlantPlano, ProyResp, ProyEsta, ProyObs, ProyIni, ProyFin, ProyAlta, Cliente) VALUES ( '$_POST[ProyNom]', '$_POST[ProyDesc]', '$_POST[ProyEmpr]', '$_POST[ProyPlant]', '$_POST[ProyPlantPlanos]', '$_POST[ProyResp]', '$_POST[ProyEsta]', '$_POST[ProyObs]', '$ProyIni', '$ProyFin', '$FechaHora', '$dataUser[ID_CLIENTE]')";
    $insertProy = pdoQuery($query);
    if ($insertProy) {
        $dataProyecto = simple_pdoQuery("SELECT proy_proyectos.ProyID AS 'id', proy_proyectos.ProyNom AS 'nombre' FROM proy_proyectos WHERE proy_proyectos.ProyNom = '$_POST[ProyNom]' AND proy_proyectos.Cliente = '$dataUser[ID_CLIENTE]' ORDER BY proy_proyectos.ProyAlta DESC LIMIT 1");
        auditoria("Proyectos - Proyecto: ($dataProyecto[id]) $dataProyecto[nombre].", 'A', '', '35');
        PrintRespuestaJson('ok', 'Proyecto creado correctamente.');
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al cargar el proyecto');
        exit;
    }
} else if ($_POST['ProySubmit'] == 'mod') {

    $checkProyNom = count_pdoQuery("SELECT 1 FROM proy_proyectos WHERE proy_proyectos.ProyNom = '$_POST[ProyNom]' AND proy_proyectos.Cliente = '$_SESSION[ID_CLIENTE]' AND proy_proyectos.ProyID != '$_POST[ProyID]' LIMIT 1");

    if ($checkProyNom) {
        PrintRespuestaJson('ERROR', 'Ya existe un proyecto con ese nombre');
        exit;
    }
    /** Inicio de proceso de asignacion de planos */
    require 'crud_procesarPlantillaProy.php';
    /** */
    $ProyPlantPlanos = $_POST['ProyPlantPlanos'];

    $DateRange = explode(' al ', $_POST['ProyIniFin']);
    $ProyIni = test_input(dr_fecha($DateRange[0]));
    $ProyFin = test_input(dr_fecha($DateRange[1]));
    $proyObs = test_input($_POST['ProyObs']);
    $query = "UPDATE proy_proyectos SET ProyDesc='$_POST[ProyDesc]', ProyNom='$_POST[ProyNom]', ProyPlant='$_POST[ProyPlant]', ProyPlantPlano=$ProyPlantPlanos, ProyResp='$_POST[ProyResp]', ProyEsta='$_POST[ProyEsta]', ProyObs='$proyObs', ProyIni = '$ProyIni', ProyFin = '$ProyFin' WHERE ProyID='$_POST[ProyID]'";
    $updateProyecto = pdoQuery($query);
    if ($updateProyecto) {
        auditoria("Proyectos - Proyecto: ($_POST[ProcID]) $_POST[ProcDesc].", 'M', '', '35');
        PrintRespuestaJson('ok', 'Proyecto modificado correctamente.');
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al Editar el proyecto');
        exit;
    }
} else if ($_POST['ProySubmit'] == 'baja') {
    $query = "DELETE FROM proy_proyectos WHERE ProyID = '$_POST[ProyID]'";
    $deleteProyecto = pdoQuery($query);
    if ($deleteProyecto) {
        auditoria("Proyectos - Proyectos: ($_POST[ProyID]) $_POST[ProyNom].", 'B', '', '35');
        PrintRespuestaJson('ok', "Proyecto eliminado correctamente.<div class='pt-1 font-weight-bold'>$_POST[ProyNom]<div>");
        exit;
    } else {
        PrintRespuestaJson('ERROR', 'Error al eliminar el proyecto');
        exit;
    }
} else if ($_POST['toExcel'] == true) {

    $_POST['_c'] = ($_POST['_c']) ?? '';
    $_GET['_c'] = $_POST['_c']; // recid de la cuenta para poder conectarnos a la base de datos de SQL Server
    emptyData($_POST['_c'], 'No se recibieron datos de cuenta'); // Validar que se recibieron datos
    $dataCuenta = getIniCuenta($_POST['_c']); // Obtener el host de la cuenta
    // print_r($dataCuenta) . exit;
    $urlHost = $dataCuenta['hostCHWeb']; // Obtener el host de la cuenta
    $idCliente = $dataCuenta['idCompany']; // Obtener el host de la cuenta

    $dataRequest = $_REQUEST;
    $dataRequest['idCliente'] = $idCliente;
    $urlTar = $urlHost . "/" . HOMEHOST . "/proy/data/getTareas.php?" . microtime(true); // Url para obtener las tareas pendientes
    $tareasPendientes = sendRemoteData($urlTar, ($dataRequest)); // Obtenemos el array de tareas pendientes
    $xlsData = json_decode($tareasPendientes, true); // Lo decodificamos en un array
    $xlsData = $xlsData['data']; // Obtenemos el array de tareas pendientes

    require __DIR__ . '/tarToXls.php';

    echo PrintRespuestaJson('ok', $routeFile);
    auditoria("Proyectos - Tareas: Se exporto excel", 'A', '', '37');
    exit;
} else if ($_POST['setDescAll'] == '1') {


    $_POST['ini'] = ($_POST['ini']) ?? '';
    $_POST['fin'] = ($_POST['fin']) ?? '';

    $ini = test_input($_POST['ini']);
    $fin = test_input($_POST['fin']);

    $iniVal = (intval(str_replace(':', '', $ini)));
    $finVal = (intval(str_replace(':', '', $fin)));

    if ($iniVal > $finVal) {
        PrintRespuestaJson('error', 'La hora de inicio debe ser menor a la hora de Fin');
        exit;
    }

    if ($iniVal != '00:00' && $finVal != '00:00') {
        if ($iniVal == $finVal) {
            PrintRespuestaJson('error', 'La hora de Inicio y fin no pueden ser iguales');
            exit;
        }
    }

    if (strlen($ini) < 5) {
        PrintRespuestaJson('error', 'Formato de hora de inicio incorrecto');
        exit;
    }

    if (strlen($fin) < 5) {
        PrintRespuestaJson('error', 'Formato de hora de fin incorrecto');
        exit;
    }

    emptyData($ini, 'No se recibieron datos de cuenta'); // Validar que se recibieron datos
    emptyData($fin, 'No se recibieron datos de cuenta'); // Validar que se recibieron datos

    $d = "TRUNCATE `proy_tareas_desc`";
    if (pdoQuery($d)) {
        $qUser = "SELECT usr.id AS 'idUser' FROM usuarios usr INNER JOIN mod_roles mrol ON usr.rol=mrol.id_rol LEFT JOIN proy_tareas_desc ON usr.id = proy_tareas_desc.TarDesUsr WHERE usr.cliente='$_SESSION[ID_CLIENTE]' AND mrol.modulo=43 AND usr.estado='0' GROUP BY usr.id ORDER BY `usr`.`nombre` ASC";
        $r = array_pdoQuery($qUser);

        $all = ",(NULL, '$ini', '$fin')";
        foreach ($r as $key => $v) {
            $user[] = "($v[idUser], '$ini', '$fin')";
        }

        $datos = implode(",", $user);

        $i = "INSERT INTO `proy_tareas_desc` (`TarDesUsr`, `TarDesIni`, `TarDesFin`) VALUES $datos $all";
        // print_r($i);
        if (pdoQuery($i)) {
            PrintRespuestaJson('ok', 'Datos guardados');
            auditoria("Proyectos - Tareas: Se configuró descanso masivo. De $ini a $fin", 'A', '', '37');
            exit;
        }
    }
} else if ($_POST['setDesc'] == '1') {

    $_POST['TarDesUsr'] = ($_POST['TarDesUsr']) ?? '';
    $_POST['TarDesNom'] = ($_POST['TarDesNom']) ?? '';
    $_POST['TarDesLeg'] = ($_POST['TarDesLeg']) ?? '';
    $_POST['TarDesIni'] = ($_POST['TarDesIni']) ?? '';
    $_POST['TarDesFin'] = ($_POST['TarDesFin']) ?? '';
    $_POST['TarDesEsta'] = ($_POST['TarDesEsta']) ?? '';
    $_POST['usuarioUsr'] = ($_POST['usuarioUsr']) ?? '';

    $ini = test_input($_POST['TarDesIni']); // hora de inicio
    $fin = test_input($_POST['TarDesFin']); // hora de fin
    $user = test_input($_POST['usuarioUsr']); // usuario de login
    $nombre = test_input($_POST['TarDesNom']); // nombre del usuario
    $userID = test_input($_POST['TarDesUsr']); // userid
    $legajo = test_input($_POST['TarDesLeg']); // Legajo
    $estado = test_input($_POST['TarDesEsta']); // Legajo

    $iniVal = (intval(str_replace(':', '', $ini)));
    $finVal = (intval(str_replace(':', '', $fin)));

    if ($iniVal > $finVal) {
        PrintRespuestaJson('error', 'La hora de Inicio debe ser menor a la hora de Fin');
        exit;
    }

    if ($iniVal != '00:00' && $finVal != '00:00') {
        if ($iniVal == $finVal) {
            PrintRespuestaJson('error', 'La hora de Inicio y fin no pueden ser iguales');
            exit;
        }
    }

    if (strlen($ini) < 5) {
        PrintRespuestaJson('error', 'Formato de hora de inicio incorrecto');
        exit;
    }

    if (strlen($fin) < 5) {
        PrintRespuestaJson('error', 'Formato de hora de fin incorrecto');
        exit;
    }
    if (($iniVal) > 2400) {
        PrintRespuestaJson('error', 'Formato de hora de inicio incorrecto');
        exit;
    }
    if (($finVal) > 2400) {
        PrintRespuestaJson('error', 'Formato de hora de fin incorrecto');
        exit;
    }

    emptyData($ini, 'Hora de inicio requerida'); // Validar que se recibieron datos
    emptyData($fin, 'Hora de fin requerida'); // Validar que se recibieron datos
    emptyData($userID, 'Usuario requerido'); // Validar que se recibieron datos

    $d = "DELETE FROM `proy_tareas_desc` WHERE TarDesUsr = $userID";
    if ($userID == 'null') {
        $d = "DELETE FROM `proy_tareas_desc` WHERE TarDesUsr IS NULL";
    }
    if (pdoQuery($d)) {
        $i = "INSERT INTO `proy_tareas_desc` (`TarDesUsr`, `TarDesIni`, `TarDesFin`, `TarDesEsta`) VALUES ($userID, '$ini', '$fin', '$estado')";
        if (pdoQuery($i)) {
            $estaStr = ($estado == '0') ? "activo" : "inactivo";
            PrintRespuestaJson('ok', 'Datos guardados');
            auditoria("Proyectos - Tareas: Se configuró descanso de usuario $nombre. De $ini a $fin. Estado $estaStr", 'A', '', '37');
            exit;
        }
    }
}
