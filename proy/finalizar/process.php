<?php
require __DIR__ . '/../../config/index.php';
// require __DIR__ . '/../../vendor/autoload.php';
use Carbon\Carbon;

session_start();
header("Content-Type: application/json");
ultimoacc();
E_ALL();
timeZone();
function TareFinTipo($TareFinTipo, $TareFin)
{
	$TareFinTipo = empty($TareFinTipo) ? 'normal' : $TareFinTipo;
	$tft = $TareFinTipo;
	$tft = ($TareFin == '0000-00-00 00:00:00') ? 'manual' : $TareFinTipo;
	if ($TareFin != '0000-00-00 00:00:00') {
		switch ($tft) {
			case 'manual':
				$tft = 'manual';
				break;
			case 'fichada':
				$tft = 'fichada';
				break;
			case 'turno':
				$tft = 'turno';
				break;
			case 'modificada':
				$tft = 'modificada';
				break;
			case 'normal':
				$tft = 'normal';
				break;
			default:
				$tft = 'modificada';
				break;
		}
	}
	return $tft;
}
function redondearTareFin($tareID, $fechahora)
{
	/** redondeamos TareHoraFin */
	$idCtar = simple_pdoQuery("SELECT c.recid FROM clientes c INNER JOIN proy_tareas pt ON c.id = pt.Cliente WHERE pt.TareID = '$tareID' LIMIT 1"); // Obtenemos el recid de la cuenta
	$d = getIniCuenta($idCtar['recid']);
	$params = ["datetime" => $fechahora];
	$urlHost = $d['hostCHWeb']; // Obtener el host de la cuenta
	$url = $urlHost . "/" . HOMEHOST . "/proy/api/?redondear=1";
	$rs = sendRemoteData($url, ($params));
	$rs = json_decode($rs, true); // Lo decodificamos en un array.
	return $rs['resultado'];
	/** Fin redondear */
}
function calculaDescanso($tareID, $fin)
{
	/** calculamos descanso */
	$dataTar = simple_pdoQuery("SELECT c.recid, pt.TareResp, pt.TareIni FROM clientes c INNER JOIN proy_tareas pt ON c.id = pt.Cliente WHERE pt.TareID = '$tareID' LIMIT 1"); // Obtenemos el recid de la cuenta
	$d = getIniCuenta($dataTar['recid']);
	$params = array(
		"start" => $dataTar['TareIni'],
		"end" => $fin,
		"user" => $dataTar['TareResp'],
	);
	$urlHost = $d['hostCHWeb']; // Obtener el host de la cuenta
	$url = $urlHost . "/" . HOMEHOST . "/proy/api/?descanso=1";
	$rs = sendRemoteData($url, ($params));
	$rs = json_decode($rs, true); // Lo decodificamos en un array.
	return $rs;
	/** Fin */
}

$_SESSION['UID'] = $_SESSION['UID'] ?? '';
$_SESSION['ID_CLIENTE'] = $_SESSION['ID_CLIENTE'] ?? '';

$FechaHora = date("Y-m-d H:i");
$Cliente = $_SESSION['ID_CLIENTE'];
$User = $_SESSION['UID'];
$_POST['tarSubmit'] = $_POST['tarSubmit'] ?? '';
$_POST['assignTar'] = $_POST['assignTar'] ?? '';
$_POST['tarComplete'] = $_POST['tarComplete'] ?? '';
$_POST['openTar'] = $_POST['openTar'] ?? '';
$_POST['ediTar'] = $_POST['ediTar'] ?? '';
$_POST['anulaTar'] = $_POST['anulaTar'] ?? '';
$_POST['calCosto'] = $_POST['calCosto'] ?? '';
$_POST['procPendientes'] = $_POST['procPendientes'] ?? '';

function emptyData($data, $err)
{
	return empty($data) ? PrintRespuestaJson('error', $err) . exit : '';
}

(!$_SERVER['REQUEST_METHOD'] == 'POST') ? PrintRespuestaJson('error', 'Invalid Request Method') . exit : '';

if (($_POST['tarSubmit'])) { // Crear Tarea
	$POSTDATA = $_POST['data'] ?? '';
	emptyData($POSTDATA, 'No se recibieron datos'); // Validar que se recibieron datos
	$data = json_decode($POSTDATA);

	$EmpDesc = $data->EmpDesc ?? '';
	$EmpID = $data->EmpID ?? '';
	$PlanoCod = $data->PlanoCod ?? '';
	$PlanoDesc = $data->PlanoDesc ?? '';
	$PlanoID = $data->PlanoID ?? '';
	$PlantDesc = $data->PlantDesc ?? '';
	$ProcCost = $data->ProcCost ?? '';
	$ProcDesc = $data->ProcDesc ?? '';
	$ProcID = $data->ProcID ?? '';
	$ProyDesc = $data->ProyDesc ?? '';
	$ProyID = $data->ProyID ?? '';
	$ProyNom = $data->ProyNom ?? '';
	$ProyPlant = $data->ProyPlant ?? '';
	$ProyResp = $data->ProyResp ?? '';
	$RespDesc = $data->RespDesc ?? '';

	emptyData($ProyID, 'No se recibieron datos del proyecto'); // Validar que se recibieron datos del proyecto
	emptyData($ProcID, 'No se recibieron datos del proceso'); // Validar que se recibieron datos del proyecto

	$rTar = "SELECT proy_tareas.TareID, proy_empresas.EmpDesc, proy_tareas.TareProy, proy_proyectos.ProyDesc, proy_proyectos.ProyNom, proy_tareas.TareResp, resp.nombre, proy_tareas.TareProc, proy_proceso.ProcDesc, proy_tareas.TarePlano, proy_planos.PlanoDesc, proy_tareas.TareIni, proy_tareas.TareFin, proy_tareas.Cliente FROM proy_tareas INNER JOIN proy_empresas ON proy_tareas.TareEmp=proy_empresas.EmpID INNER JOIN proy_proyectos ON proy_tareas.TareProy=proy_proyectos.ProyID INNER JOIN usuarios resp ON proy_tareas.TareResp=resp.id INNER JOIN proy_proceso ON proy_tareas.TareProc=proy_proceso.ProcID LEFT JOIN proy_tare_horas ON proy_tareas.TareID = proy_tare_horas.TareHorID LEFT JOIN proy_planos ON proy_tareas.TarePlano=proy_planos.PlanoID WHERE `TareResp`='$User' AND proy_tareas.TareEsta = '0' AND `proy_tare_horas`.`TareHorMin` IS NULL LIMIT 1";

	$dataTar = simple_pdoQuery($rTar);

	// $tareDiff = tareDiff($dataTar['TareIni'], $dataTar['TareFin']) ?? [];
	if (isset($dataTar['TareIni'], $dataTar['TareFin'])) {
		$tareDiff = tareDiff($dataTar['TareIni'], $dataTar['TareFin']) ?? [];
	} else {
		$tareDiff = []; // O maneja el caso en que los valores son nulos
	}

	if ($dataTar['TareID'] ?? '') { // Si existe una tarea en curso
		if ($dataTar['TareFin'] = '0000-00-00 00:00:00') { // Si la tarea no ha finalizado
			$TareInicio = FechaFormatH($dataTar['TareIni']); // Fecha de inicio de la tarea
			$dataTar = array(
				"Text" => "Debe completar su tareas pendientes antes de continuar.",
				"Proy" => array(
					"nombre" => $dataTar['ProyNom'],
					"ID" => $dataTar['TareProy']
				),
				"EmpDesc" => $dataTar['EmpDesc'],
				"ProcDesc" => $dataTar['ProcDesc'],
				"PlanoDesc" => $dataTar['PlanoDesc'],
				"TareID" => $dataTar['TareID'],
				"Inicio" => $TareInicio,
				"Duracion" => $tareDiff,
			);
			PrintRespuestaJson('pendTar', $dataTar);
			exit;
		}
	}
	$PlanoID = empty($PlanoID) ? 'NULL' : $PlanoID;

	try {
		$i = "INSERT INTO `proy_tareas` (`TareEmp`, `TareProy`, `TareResp`, `TareProc`, `TarePlano`, `TareCost`, `TareIni`, `TareFin`, `Cliente`) VALUES ( '$EmpID', '$ProyID', '$User', '$ProcID', $PlanoID, '$ProcCost', '$FechaHora', '0000-00-00 00:00:00', '$Cliente')";

		error_log($i); // Log de la consulta SQL

		if (!pdoQuery($i)) {
			throw new Exception("Error al iniciar la tarea");
		}

		$r = "SELECT `TareID` FROM `proy_tareas` WHERE `TareEmp` = '$EmpID' AND `TareProy` = '$ProyID' AND `TareResp` = '$User' AND `TareProc` = '$ProcID' AND `TareCost` = '$ProcCost' AND `TareIni` = '$FechaHora' AND `Cliente` = '$Cliente' ORDER BY `TareID` DESC LIMIT 1";
		$r = simple_pdoQuery($r);

		PrintRespuestaJson('ok', "Tarea (#$r[TareID]) iniciada con correctamente");
		auditoria("Tarea (#$r[TareID]) iniciada correctamente", 'A', '', '37');

		exit;
	} catch (\Throwable $th) {
		PrintRespuestaJson('error', $th->getMessage());
	}
} else if ($_POST['tarComplete']) { // Completar tarea

	$_POST['tareID'] = ($_POST['tareID']) ?? '';
	$_POST['TareFechaFin'] = ($_POST['TareFechaFin']) ?? '';
	$_POST['TareHoraFin'] = ($_POST['TareHoraFin']) ?? '';
	$_POST['fromTareas'] = ($_POST['fromTareas']) ?? '';
	$_POST['finTipo'] = ($_POST['finTipo']) ?? '';
	$_POST['tarCompletePend'] = ($_POST['tarCompletePend']) ?? '';
	$tareID = test_input($_POST['tareID']);

	emptyData($_POST['tareID'], 'No se recibieron datos'); // Validar que se recibieron datos

	if (($_POST['fromTareas'])) { // Si viene de Tareas, validamos datos obligatorios
		emptyData($_POST['TareFechaFin'], 'La Fecha Fin es requerida');
		emptyData($_POST['TareHoraFin'], 'La Hora Fin de es requerida');
		(ValidaFormatoHora($_POST['TareHoraFin'])) ? PrintRespuestaJson('error', 'La Hora Fin debe tener el formato HH:MM') . exit : ''; // Validar formato de la hora
		($_POST['TareHoraFin'] == '00:00') ? PrintRespuestaJson('error', 'Campos Hora no pueden estar en 00:00') . exit : ''; // Validar que la hora no sea 00:00
	}

	$rTar = "SELECT TareID, TareEmp, TareProy, TareResp, TareProc, TarePlano, TareCost, TareIni, TareFin, Cliente, TareFinTipo FROM `proy_tareas` WHERE `TareID` = '$_POST[tareID]' LIMIT 1";
	$dataTar = simple_pdoQuery($rTar);
	$ProyID = $dataTar['TareProy'];

	($dataTar['TareFin'] != '0000-00-00 00:00:00') ? PrintRespuestaJson('ok', "La tarea (#$tareID) ya ha sido completada") . exit : ''; // Validar que la tarea no haya finalizado

	if (($_POST['fromTareas'])) { // Si viene de Tareas, validamos datos obligatorios

		/** redondeamos TareHoraFin */
		$FinTarea = (dr_($_POST['TareFechaFin']) . ' ' . $_POST['TareHoraFin']);
		/** Fin redondear */

		// $fechaFin = ($_POST['fromTareas']) ? dr_($_POST['TareFechaFin']) . ' ' . $_POST['TareHoraFin'] : false;
		$fechaFin = ($_POST['fromTareas']) ? $FinTarea : false;
		$horaIni = intval(str_replace(':', '', HoraFormat($dataTar['TareIni'], false))); // Hora de inicio de la tarea en formato HHMM (24h)
		$horaFin = intval(str_replace(':', '', HoraFormat($fechaFin, false))); // Hora de fin de la tarea en formato HHMM (24h)
		$iniFecha = intval(Fecha_String($dataTar['TareIni'])); // Fecha de inicio de la tarea en formato DD/MM/YYYY
		$finFecha = intval(Fecha_String(dr_($_POST['TareFechaFin']))); // Fecha de fin de la tarea que viene por post en formato DD/MM/YYYY HH:MM:SS
		$err = 'La <span class="font-weight-bold">Fecha Fin</span> no puede ser menor a la de inicio';
		($iniFecha > $finFecha) ? PrintRespuestaJson('error', $err) . exit : '';
		if (($iniFecha == $finFecha)) {
			$err = 'La <span class="font-weight-bold">Hora Fin</span> no puede ser menor o igual a la de inicio';
			($horaIni >= $horaFin) ? PrintRespuestaJson('error', $err) . exit : '';
		}
	}

	// $FechaHora = redondearTareFin($dataTar['TareID'], $FechaHora);

	// $f       = Carbon::parse($dataTar['TareIni']); // Fecha de inicio
	// $f2      = Carbon::parse($FechaHora); // Fecha de finalizacion
	// $minutos = ($f2->diffInMinutes($f)); // Total de minutos
	// $minutos2 = (0); // Total de minutos
	// $total   = MinHora($f2->diffInMinutes($f)); // Total de horas y minutos
	// $total2   = MinHora(0); // Total de horas y minutos

	$calcular = calculaDescanso($dataTar['TareID'], $FechaHora);
	$total = $calcular['totales']['calculadas']['horas'];
	$total2 = $calcular['totales']['reales']['horas'];
	$minutos = $calcular['totales']['calculadas']['min'];
	$minutos2 = $calcular['totales']['reales']['min'];

	if (($_POST['fromTareas'])) {
		// $f      = Carbon::parse($dataTar['TareIni']); // Fecha de inicio
		// $f2     = Carbon::parse($fechaFin); // Fecha de finalizacion
		// $minutos = ($f2->diffInMinutes($f)); // Total de minutos
		// $total = MinHora($f2->diffInMinutes($f)); // Total de horas y minutos
		$calcular = calculaDescanso($dataTar['TareID'], $fechaFin);
		$total = $calcular['totales']['calculadas']['horas'];
		$total2 = $calcular['totales']['reales']['horas'];
		$minutos = $calcular['totales']['calculadas']['min'];
		$minutos2 = $calcular['totales']['reales']['min'];

		// $f       = Carbon::parse($dataTar['TareIni']); // Fecha de inicio
		// $f2      = Carbon::parse($fechaFin); // Fecha de finalizacion
		// $minutos = ($f2->diffInMinutes($f)); // Total de minutos
		// $minutos2 = (0); // Total de minutos
		// $total   = MinHora($f2->diffInMinutes($f)); // Total de horas y minutos
		// $total2   = MinHora(0); // Total de horas y minutos
	}

	$FechaHora = ($_POST['fromTareas']) ? $fechaFin : $FechaHora;
	$calcLimitTar = '';

	# code...
	$costo = floatval($dataTar['TareCost']) ?? '';
	$cost = ($costo / 60) * $minutos; // costo de horas calculadas
	$cost2 = ($costo / 60) * $minutos2; // costo de horas reales

	$r = simple_pdoQuery("SELECT 1 FROM `proy_tare_horas` WHERE `TareHorID` = '$_POST[tareID]' LIMIT 1");
	($r) ? pdoQuery("DELETE FROM `proy_tare_horas` WHERE `TareHorID` = '$_POST[tareID]'") : '';

	$i = "INSERT INTO `proy_tare_horas` (`TareHorID`, `TareHorProy`, `TareHorCost`, `TareHorCost2`,`TareHorHoras`, `TareHorHoras2`, `TareHorMin`, `TareHorMin2`) VALUES ('$_POST[tareID]', '$ProyID', '$cost', '$cost2', '$total', '$total2', '$minutos', '$minutos2')";

	(!pdoQuery($i)) ? PrintRespuestaJson('error', 'Error al finalizar la tarea') . exit : ''; // Sino se pudo insertar la tarea, Salimos del script
	$TareFinTipo = $_POST['finTipo'] ? $_POST['finTipo'] : 'normal';
	if (($_POST['fromTareas'])) {
		if ($_POST['finTipo']) {
			$TareFinTipo = TareFinTipo($_POST['finTipo'], $fechaFin);
		} else {
			$TareFinTipo = TareFinTipo($dataTar['TareFinTipo'], $dataTar['TareFin']);
		}
	}

	$update = "UPDATE `proy_tareas` SET `TareFin` = '$FechaHora', `TareFinTipo` = '$TareFinTipo' WHERE `TareID` = '$_POST[tareID]'"; // Actualizamos la tarea

	if (pdoQuery($update)) {
		$data = array('status' => 'ok', 'Mensaje' => "Tarea (#$tareID) completada correctamente", 'confTar' => ($calcLimitTar));
		echo json_encode($data);
		auditoria("Tarea (#$tareID) completada correctamente", 'M', '', '37');
		exit;
	} else {
		$data = array('status' => 'error', 'Mensaje' => "Error al finalizar la tarea (#$tareID)", 'confTar' => ($calcLimitTar));
		echo json_encode($data);
		exit;
	}
} else if (($_POST['ediTar'])) { // Editar Tarea

	$_POST['TareResp'] = $_POST['TareResp'] ?? '';
	$_POST['TareProy'] = $_POST['TareProy'] ?? '';
	$_POST['TareProc'] = $_POST['TareProc'] ?? '';
	$_POST['TarePlano'] = $_POST['TarePlano'] ?? 'NULL';
	$_POST['TarePlano'] = ($_POST['TarePlano'] == '0') ? 'NULL' : $_POST['TarePlano'];
	$_POST['TareFechaIni'] = $_POST['TareFechaIni'] ?? '';
	$_POST['TareHoraIni'] = $_POST['TareHoraIni'] ?? '';
	$_POST['TareFechaFin'] = $_POST['TareFechaFin'] ?? '';
	$_POST['TareHoraFin'] = $_POST['TareHoraFin'] ?? '';
	$_POST['tareID'] = $_POST['tareID'] ?? '';
	$_POST['fromTareas'] = $_POST['fromTareas'] ?? '';
	$_POST['pendTar'] = $_POST['pendTar'] ?? '';

	$TareFechaFin = test_input($_POST['TareFechaFin']) ?? '';
	$TareHoraFin = test_input($_POST['TareHoraFin']) ?? '';
	$TareFechaIni = test_input($_POST['TareFechaIni']) ?? '';
	$TareHoraIni = test_input($_POST['TareHoraIni']) ?? '';
	$TareProy = test_input($_POST['TareProy']) ?? '';
	$TareProc = test_input($_POST['TareProc']) ?? '';
	$TarePlano = test_input($_POST['TarePlano']) ?? '';
	$TareResp = test_input($_POST['TareResp']) ?? '';
	$tareID = test_input($_POST['tareID']) ?? '';
	$fromTareas = test_input($_POST['fromTareas']) ?? '';
	$pendTar = test_input($_POST['pendTar']) ?? '';

	emptyData($tareID, 'La Tarea es requerida'); // Validamos el ID de la tarea
	emptyData($TareResp, 'El Responsable es requerido'); // Validamos el responsable
	emptyData($TareProy, 'El Proyecto es requerido'); // Validamos el proyecto
	emptyData($TareProc, 'El Proceso es requerido'); // Validamos el proceso
	emptyData($TareFechaIni, 'La Fecha de Inicio es requerida'); // Validamos la fecha de inicio
	emptyData($TareHoraIni, 'La Hora de Inicio es requerida'); // Validamos la hora de inicio
	if (!$pendTar) { // si no esta pendiente la tarea
		emptyData($TareFechaFin, 'La Fecha de Fin es requerida'); // Validamos la fecha de fin
		emptyData($TareHoraFin, 'La Hora de Fin es requerida'); // Validamos la hora de fin
	}

	$FechaHoraIni = dr_($TareFechaIni) . ' ' . $TareHoraIni . ':00'; // Fecha y hora de inicio
	$FechaHoraFin = dr_($TareFechaFin) . ' ' . $TareHoraFin . ':00'; // Fecha y hora de fin

	// $FechaHoraFin = redondearTareFin($tareID, $FechaHoraFin);

	$h1 = intval(str_replace(':', '', $TareHoraIni)); // Hora de inicio en numeros enteros
	$h2 = intval(str_replace(':', '', $TareHoraFin)); // Hora de fin en numeros enteros
	$f1 = intval(Fecha_String(dr_($TareFechaIni))); // Fecha de inicio de la tarea en numeros enteros
	$f2 = intval(Fecha_String(dr_($TareFechaFin))); // Fecha de fin de la tarea en numeros enteros

	$err = 'La <span class="font-weight-bold">Fecha Fin</span> no puede ser menor a la de inicio';
	if (!$pendTar) { // Si la tarea no esta pendiente
		($f1 > $f2) ? PrintRespuestaJson('error', $err) . exit : ''; // Validamos que la fecha de fin no sea menor a la de inicio
		if ($f1 == $f2) { // Si las fechas son del mismo día. 
			$err = 'La <span class="font-weight-bold">Hora Fin</span> no puede ser menor o igual a la de inicio';
			($h1 >= $h2) ? PrintRespuestaJson('error', $err) . exit : ''; // Validamos que la hora de fin no sea menor o igual a la de inicio
		}
	}

	$r = simple_pdoQuery("SELECT * FROM `proy_tareas` WHERE `TareID` = '$tareID' LIMIT 1"); // Obtenemos la tarea
	//$r = count_pdoQuery("SELECT 1 FROM `proy_tareas` WHERE `TareID` = '$tareID' LIMIT 1"); // Contamos si existe la tarea // devuelve true o false
	(!$r) ? PrintRespuestaJson('error', "La Tarea (#$tareID) no existe") . exit : ''; // Si no existe la tarea, salimos del script
	$rCost = simple_pdoQuery("SELECT `ProcCost` FROM `proy_proceso` WHERE `ProcID` = '$TareProc' LIMIT 1"); // Obtenemos el costo de la tarea
	$rCost = $rCost['ProcCost'] ?? '0';

	$FechaHoraFin = ($pendTar) ? '0000-00-00 00:00:00' : $FechaHoraFin; // Si la tarea esta pendiente, la fecha de fin es 0000-00-00 00:00:00

	$TareFinTipo = 'normal';
	$TareFinTipo = TareFinTipo($r['TareFinTipo'], $r['TareFin']);

	$TareFinTipo = ($FechaHoraFin != $r['TareFin']) ? 'modificada' : $TareFinTipo; // Si la fecha de fin es diferente a la de la tarea, la tarea fue modificada

	if ($FechaHoraFin != "0000-00-00 00:00:00") { // Si la fecha de fin no es 0000-00-00 00:00:00 (si la tarea esta pendiente)
		$calcLimitTar = calcLimitTar($FechaHoraIni, $FechaHoraFin); // Calculamos el limite de tiempo de la tarea
		$textErrorCalc = "El límite de tiempo para las tareas es de: $calcLimitTar[limitHor] Hs. Y el tiempo calculado es: $calcLimitTar[diffHor] Hs.";
		if ($calcLimitTar['status']) {
			$data = array('status' => 'error', 'Mensaje' => $textErrorCalc, 'confTar' => ($calcLimitTar));
			echo json_encode($data);
			exit; // Si el limite de tiempo de la tarea es mayor a la diferencia de tiempo de la tarea. Salimos del script
		}
	}


	$u = "UPDATE `proy_tareas` SET `TareProy` = '$TareProy', `TareProc` = '$TareProc', `TarePlano` = $TarePlano, `TareResp` = '$TareResp', `TareCost`= '$rCost'  ,`TareIni` = '$FechaHoraIni', `TareFin` = '$FechaHoraFin', `TareFinTipo` = '$TareFinTipo' WHERE `TareID` = '$tareID'";

	(!pdoQuery($u)) ? PrintRespuestaJson('error', "Error al editar la tarea (#$tareID)") . exit : ''; // Si no se pudo editar la tarea, salimos del script
	//(!pdoQuery($u)) ? PrintRespuestaJson('error', $u) . exit : ''; // Si no se pudo editar la tarea, salimos del script
	(($pendTar)) ? PrintRespuestaJson('ok', "Tarea (#$tareID) editada correctamente.") . auditoria("Tarea (#$r[TareID]) editada correctamente", 'M', '', '37') . exit : ''; // Si la tarea esta pendiente, salimos del script

	// $f       = Carbon::parse($FechaHoraIni); // Fecha de inicio
	// $f2      = Carbon::parse($FechaHoraFin); // Fecha de finalizacion
	// $minutos = ($f2->diffInMinutes($f)); // Total de minutos
	// $minutos2 = (0); // Total de minutos
	// $total   = MinHora($f2->diffInMinutes($f)); // Total de horas y minutos
	// $total2   = MinHora(0); // Total de horas y minutos

	$calcular = calculaDescanso($tareID, $FechaHoraFin);
	$total = $calcular['totales']['calculadas']['horas'];
	$total2 = $calcular['totales']['reales']['horas'];
	$minutos = $calcular['totales']['calculadas']['min'];
	$minutos2 = $calcular['totales']['reales']['min'];


	// $cost = floatval($rCost);
	// $cost = ($cost / 60) * $minutos;

	# code...
	$costo = floatval($rCost) ?? '';
	$cost = ($costo / 60) * $minutos; // costo de horas calculadas
	$cost2 = ($costo / 60) * $minutos2; // costo de horas reales

	$r = simple_pdoQuery("SELECT 1 FROM `proy_tare_horas` WHERE `TareHorID` = '$tareID' LIMIT 1"); // Chequeamos si existe la tarea en proy_tare_horas // devuelve true o false

	($r) ? pdoQuery("DELETE FROM `proy_tare_horas` WHERE `TareHorID` = '$tareID'") : ''; // Si existe la tarea en proy_tare_horas, eliminamos la tarea de la tabla

	// $i = "INSERT INTO `proy_tare_horas` (`TareHorID`, `TareHorProy`, `TareHorCost`, `TareHorHoras`, `TareHorMin`) VALUES ('$tareID', '$TareProy', '$cost', '$total', '$minutos')";

	$i = "INSERT INTO `proy_tare_horas` (`TareHorID`, `TareHorProy`, `TareHorCost`, `TareHorCost2`,`TareHorHoras`, `TareHorHoras2`, `TareHorMin`, `TareHorMin2`) VALUES ('$tareID', '$TareProy', '$cost', '$cost2', '$total', '$total2', '$minutos', '$minutos2')";

	/** procesar todas las tareas */

	// $arr = "SELECT * FROM proy_tareas order by TareID"; // Obtenemos todas las tareas
	// $arr = array_pdoQuery($arr); // Obtenemos todas las tareas
	// $e = 'Comienzo de proceso';
	// foreach ($arr as $key => $value) {
	// 	pdoQuery("DELETE FROM `proy_tare_horas` WHERE `TareHorID` = '$value[TareID]'"); 
	// 	$f       = Carbon::parse($value['TareIni']); // Fecha de inicio
	// 	$f2      = Carbon::parse($value['TareFin']); // Fecha de finalizacion
	// 	$minutos = ($f2->diffInMinutes($f)); // Total de minutos
	// 	$total   = MinHora($f2->diffInMinutes($f)); // Total de horas y minutos

	// 	$cost = floatval($value['TareCost']) ?? '';
	// 	$cost = ($cost / 60) * $minutos;

	// 	$i = "INSERT INTO `proy_tare_horas` (`TareHorID`, `TareHorProy`, `TareHorCost`, `TareHorHoras`, `TareHorMin`) VALUES ('$value[TareID]', '$value[TareProy]', '$cost', '$total', '$minutos')";
	// 	(pdoQuery($i));
	// 	$e .= '<br>Se proceso Tareas - ' . $value['TareID'];

	// }

	// PrintRespuestaJson('ok', $e); // Si se pudo editar la tarea, salimos del script
	// exit;

	/**  */
	(pdoQuery($i)) ? PrintRespuestaJson('ok', "Tarea (#$tareID) editada correctamente.") . auditoria("Tarea (#$tareID) editada correctamente", 'M', '', '37') . exit : PrintRespuestaJson('error', "Error al editar la tarea (#$tareID)") . exit; // Fin del script. Si se inserta la tarea en proy_tare_horas, se termina el script. Si no, se muestra un error.
} else if (($_POST['openTar'])) {

	$_POST['tareID'] = ($_POST['tareID']) ?? '';
	$tareID = test_input($_POST['tareID']);
	emptyData($tareID, 'La Tarea es requerida'); // Validamos la tarea

	$r = count_pdoQuery("SELECT 1 FROM `proy_tareas` WHERE `TareID` = '$tareID' LIMIT 1"); // Contamos si existe la tarea // devuelve true o false
	(!$r) ? PrintRespuestaJson('error', "La Tarea (#$tareID) no existe") . exit : ''; // Si no existe la tarea, salimos del script

	$u = "UPDATE `proy_tareas` SET `TareFin` = '0000-00-00 00:00:00', `TareFinTipo` = 'normal' WHERE `TareID` = '$tareID'";
	(!pdoQuery($u)) ? PrintRespuestaJson('error', "Error al abrir la tarea (#$tareID)") . exit : ''; // Si no se pudo abrir la tarea, salimos del script

	$d = "DELETE FROM `proy_tare_horas` WHERE `TareHorID` = '$tareID'";
	(!pdoQuery($d)) ? PrintRespuestaJson('error', "Error al abrir la tarea (#$tareID)") . exit : PrintRespuestaJson('ok', "Se reabrio la tarea (#$tareID) correctamente") . auditoria("Tarea (#$tareID) reabierta", 'M', '', '37') . exit; // Si no se pudo abrir la tarea, salimos del script

} else if (($_POST['anulaTar'])) {

	$_POST['tareID'] = ($_POST['tareID']) ?? '';
	$tareID = test_input($_POST['tareID']);
	emptyData($tareID, 'La Tarea es requerida'); // Validamos la tarea

	$r = count_pdoQuery("SELECT 1 FROM `proy_tareas` WHERE `TareID` = '$tareID' LIMIT 1"); // Contamos si existe la tarea // devuelve true o false
	(!$r) ? PrintRespuestaJson('error', "La Tarea no existe (#$tareID)") . exit : ''; // Si no existe la tarea, salimos del script

	$u = "UPDATE `proy_tareas` SET `TareEsta` = '1' WHERE `TareID` = '$tareID'";
	(!pdoQuery($u)) ? PrintRespuestaJson('error', "Error al anular la tarea (#$tareID)") . exit : ''; // Si no se pudo abrir la tarea, salimos del script

	$d = "DELETE FROM `proy_tare_horas` WHERE `TareHorID` = '$tareID'";
	(!pdoQuery($d)) ? PrintRespuestaJson('error', 'Error al anular la tarea') . exit : PrintRespuestaJson('ok', "Se anuló la tarea (#$tareID) correctamente") . auditoria("Se anuló la tarea (#$tareID)", 'B', '', '37') . exit; // Si no se pudo abrir la tarea, salimos del script
} else if (($_POST['calCosto'])) {

	$_SESSION['UID'] = '';
	require __DIR__ . '/../data/wcGetTar.php'; //  require where_conditions y variables
	$w_c .= " AND `proy_tare_horas`.`TareHorMin` IS NOT NULL"; // Agregamos la condicion de que la tarea ya este finalizada
	$w_c .= " AND `proy_estados`.`EstTipo` != 'cerrado'"; // Solo de proyectos Abiertos
	$w_c .= " AND `proy_tareas`.`Cliente` = '$_SESSION[ID_CLIENTE]'";

	$qTar = "SELECT `proy_tareas`.`TareID`, `proy_empresas`.`EmpDesc`, `proy_tareas`.`TareEmp`, `proy_tareas`.`TareProy`, `proy_proyectos`.`ProyDesc`, `proy_proyectos`.`ProyNom`, `proy_proyectos`.`ProyPlant`, `proy_tareas`.`TareResp`, `resp`.`nombre`, `proy_tareas`.`TareProc`, `proy_proceso`.`ProcDesc`, `proy_tareas`.`TarePlano`, `proy_planos`.`PlanoDesc`, `proy_tareas`.`TareIni`, `proy_tareas`.`TareFin`, `proy_tareas`.`TareEsta`, `proy_tareas`.`Cliente`, `proy_tare_horas`.`TareHorMin`, `proy_tare_horas`.`TareHorCost`, `proy_tare_horas`.`TareHorHoras`, `proy_proceso`.`ProcCost` 
	FROM `proy_tareas` LEFT JOIN `proy_tare_horas` ON `proy_tareas`.`TareID` = `proy_tare_horas`.`TareHorID` INNER JOIN `proy_empresas` ON `proy_tareas`.`TareEmp`=`proy_empresas`.`EmpID` INNER JOIN `proy_proyectos` ON `proy_tareas`.`TareProy`=`proy_proyectos`.`ProyID` INNER JOIN `usuarios`AS `resp` ON `proy_tareas`.`TareResp`=`resp`.`id` INNER JOIN `proy_proceso` ON `proy_tareas`.`TareProc`=`proy_proceso`.`ProcID` LEFT JOIN `proy_planos` ON `proy_tareas`.`TarePlano`=`proy_planos`.`PlanoID` INNER JOIN `proy_estados` ON `proy_proyectos`.`ProyEsta`=`proy_estados`.`EstID` WHERE `proy_tareas`.`TareID` > 0";

	if ($w_c ?? ''): // if where_conditions existe
		$qTar .= $w_c;
	endif;

	$qTar .= " ORDER BY `proy_tareas`.`TareID` DESC";
	// print_r($qTar);exit;
	$r = array_pdoQuery($qTar);
	$data = $r;
	if ($data) {
		$recordsTotal = count($data);
	} else {
		$recordsTotal = 0;
	}
	$tareID = implode_keys_values('TareID', ',', $data); // Obtenemos los ids de las tareas

	if ($data) {
		$q = "UPDATE proy_tareas tar
		INNER JOIN proy_proceso proc ON tar.TareProc = proc.ProcID SET tar.TareCost = proc.ProcCost
		WHERE tar.TareID IN ($tareID)";
		pdoQuery($q); // Actualizamos el costo de proceso de las tareas
		if (pdoQuery($q)):
			$q = "UPDATE proy_tare_horas tarhor
		INNER JOIN proy_tareas tar ON tarhor.TareHorID = tar.TareID 
		SET tarhor.TareHorCost = (tar.TareCost / 60) * tarhor.TareHorMin
		WHERE tarhor.TareHorID IN ($tareID)";
			pdoQuery($q); // Actualizamos el costo neto de las tareas
		endif;
	}

	$tiempo_fin = microtime(true);
	$tiempo = ($tiempo_fin - $tiempo_inicio);
	$rt = $recordsTotal;
	$msg = '(' . $rt . ') Tareas recalculadas correctamente';
	$msg = ($rt == 1) ? '(' . $rt . ') Tarea recalculada correctamente' : $msg;
	$msg = ($rt == 0) ? 'No hay Tareas para calcular' : $msg;
	$jdata = array(
		"status" => 'ok',
		"recordsTotal" => $rt,
		"Mensaje" => $msg,
		"tareID" => $tareID,
		"tiempo" => round($tiempo, 2)
	);
	// echo json_encode($qTar);
	auditoria("$jdata[Mensaje].", 'M', '', '37');
	echo json_encode($jdata);
	exit;
} else if (($_POST['procPendientes'])) {
	// sleep(1);
	$_POST['ProcPendTar'] = ($_POST['ProcPendTar']) ?? '';
	$_POST['_c'] = ($_POST['_c']) ?? '';

	$_GET['_c'] = $_POST['_c']; // recid de la cuenta para poder conectarnos a la base de datos de SQL Server
	emptyData($_POST['_c'], 'No se recibieron datos de cuenta'); // Validar que se recibieron datos

	$dataCuenta = getIniCuenta($_POST['_c']);
	$urlHost = $dataCuenta['hostCHWeb']; // Obtener el host de la cuenta

	$getConf = getConfTar();

	$getConf = ($getConf['confTar'] ?? []) ? $getConf['confTar'] : PrintRespuestaJson('error', 'No hay datos de configuracion') . exit;
	$getConf['ProcPendTar'] = ($_POST['ProcPendTar']) ? 1 : $getConf['ProcPendTar']; // Si se recibio el parametro de procesar tareas pendientes, lo asignamos a la variable de configuracion sino la dejamos como esta

	if ($getConf['ProcPendTar'] != 1) {
		PrintRespuestaJson('error', 'No se puede actualizar') . exit;
	}

	$r = "SELECT c.id from clientes c WHERE c.recid = '$_POST[_c]' LIMIT 1";
	$dataC = simple_pdoQuery($r); // Obtenemos el id de la cuenta
	$pathLog = "logspend/logs_pendientes_" . $dataC['id'] . "_" . date('Ymd') . ".log"; // Path del log de tareas pendientes
	borrarLogs(__DIR__ . $pathLog, 30, '.log'); // Borramos los logs de tareas pendientes de mas de 30 días

	$dataRequest = $_REQUEST;
	$dataRequest['TareEstado'] = 'pendientes';
	$dataRequest['idCliente'] = $dataC['id'];
	$dataRequest['length'] = 9999;
	$dataRequest['procPendientes'] = 1;
	$dataRequest['HoraLimit'] = $getConf['HoraCierre'];

	$urlTar = $urlHost . "/" . HOMEHOST . "/proy/data/getTareas.php?" . microtime(true); // Url para obtener las tareas pendientes
	$tareasPendientes = sendRemoteData($urlTar, ($dataRequest)); // Obtenemos el array de tareas pendientes
	$tareasPendientes = json_decode($tareasPendientes, true); // Lo decodificamos en un array
	$dataPendientes = ($tareasPendientes['data'] ?? []) ? $tareasPendientes['data'] : PrintRespuestaJson('ok', 'No Hay Tareas para procesar') . exit; // Obtenemos el array de tareas pendientes y validamos que exista algo de lo contrario salimos

	fileLog("Inicio. Tareas Pendientes: $tareasPendientes[recordsTotal]", $pathLog); // Escribimos en el log el inicio del proceso.
	$info = array();
	foreach ($dataPendientes as $v) { // Recorremos el array de tareas pendientes
		$legajo = $v['responsable']['legajo']; // Legajo del responsable de la tarea
		$FechaTar = FechaString($v['fechas']['TareIni']); // Fecha de la tarea
		$q = "SELECT TOP 1 F.FicDiaL, F.FicHorE AS 'FicHorE', F.FicHorS AS 'FicHorS', dbo.fn_STRMinutos((F.FicHorS)) AS 'FicHorSMin', R.RegHoRe AS 'RegHoRe', dbo.fn_STRMinutos((R.RegHoRe)) AS 'RegHoReMin' FROM FICHAS F LEFT JOIN REGISTRO R ON F.FicFech = R.RegFeAs AND F.FicLega = R.RegLega WHERE F.FicFech = '$FechaTar' AND F.FicLega = '$legajo' ORDER BY R.RegHoRe DESC";
		$a = simple_MSQuery($q); // obtenemos datos de la tabla fichas y registro para poder completar la tarea
		$TareIni = (HoraMin($v['fechas']['inicioHora'])); // Hora de inicio de la tarea
		$TareIni2 = FechaFormatVar($v['fechas']['TareIni'], 'YmdHi'); // Fechhora de inicio de la tarea
		$RegHoReMin = ($a['RegHoReMin']); // Hora de ultima fichada del día
		$RegHoRe = ($a['RegHoRe']); // Hora de ultima fichada del día

		if ($a['FicHorS'] < $a['FicHorE']) { // SI ES HORARIOS NOCTURNO PROSESAMOS

			$q = "SELECT TOP 1 F.FicDiaL, F.FicHorE AS 'FicHorE', F.FicHorS AS 'FicHorS', dbo.fn_STRMinutos((F.FicHorS)) AS 'FicHorSMin', R.RegHoRe AS 'RegHoRe', dbo.fn_STRMinutos((R.RegHoRe)) AS 'RegHoReMin', (R.RegFeRe + R.RegHoRe) AS 'FeHora', ((F.FicFech+1) + F.FicHorS) AS 'FeHoraS' FROM FICHAS F LEFT JOIN REGISTRO R ON F.FicFech = R.RegFeAs AND F.FicLega = R.RegLega WHERE F.FicFech = '$FechaTar' AND F.FicLega = '$legajo' ORDER BY R.RegFeRe DESC";

			$a = simple_MSQuery($q); // obtenemos datos de la tabla fichas y registro para poder completar la tarea
			$FeHora = ($a['FeHora'] != null) ? $a['FeHora']->format('YmdHi') : '0';
			$FeHora2 = ($a['FeHora'] != null) ? $a['FeHora']->format('d/m/Y') : '0';
			$FeHoraS = ($a['FeHoraS'] != null) ? $a['FeHoraS']->format('YmdHi') : '0';
			$FeHoraS2 = ($a['FeHoraS'] != null) ? $a['FeHoraS']->format('d/m/Y') : '0';
			$RegHoRe = ($a['RegHoRe']);
			$FicHorS = ($a['FicHorS']);


			$FechaFinFic = new DateTime($a['FeHora']->format('Y-m-d') . ' ' . $RegHoRe);
			$FechaFinFic = redondearTareFin($v['TareID'], $FechaFinFic->format('Y-m-d H:i'));
			$FeHora2 = $FechaFinFic->format('d/m/Y');
			$RegHoRe = $FechaFinFic->format('H:i');

			if ($TareIni2 <= $FeHora) { // Si la hora de inicio de la tarea es menor a la ultima fichada del día
				$data = array(
					'tareID' => $v['TareID'],
					'tarComplete' => 'tarComplete',
					'tarCompletePend' => 1,
					'fromTareas' => 'true',
					'TareFechaFin' => $FeHora2,
					'TareHoraFin' => $RegHoRe,
					'finTipo' => 'fichada',
				);

				$urlSet = $urlHost . "/" . HOMEHOST . "/proy/finalizar/process.php";
				$set = sendRemoteData($urlSet, $data); // Completamos la tarea con la ultima fichada del día

				$set = json_decode($set, true);
				if ($set['status'] == 'error') {
					$textLog = "Tarea (#$data[tareID]) no completada. $set[Mensaje]";
					fileLog($textLog, $pathLog); // Escribimos en el log la respuesta de la tarea
					$info[] = $textLog;
					auditoria("$textLog.", 'P', '', '37');
				} else {
					$textLog = "Tarea (#$data[tareID]) completada. Hora de Fin: $data[TareFechaFin] $data[TareHoraFin]. Tipo: $data[finTipo]";
					fileLog($textLog, $pathLog); // Escribimos en el log la respuesta de la tarea
					$info[] = $textLog;
					auditoria("$textLog.", 'P', '', '37');
				}
				// exit;
			} else {
				$FicHorSMin = ($a['FicHorSMin']); // Hora de fin (Salida) de turno del día
				$FicHorS = ($a['FicHorS']); // Hora de fin (Salida) de turno del día
				if ($a['FicDiaL'] == 1) { // Si el día es laborable
					if ($TareIni2 < $FeHoraS) { // Si la hora de inicio de la tarea es menor a la hora de fin de turno del día
						$data = array(
							'tareID' => $v['TareID'],
							'tarComplete' => 'tarComplete',
							'fromTareas' => 'true',
							'TareFechaFin' => $FeHoraS2,
							'TareHoraFin' => $FicHorS,
							'finTipo' => 'turno',
						);
						$urlSet = $urlHost . "/" . HOMEHOST . "/proy/finalizar/process.php"; // Url para completar la tarea
						$set = sendRemoteData($urlSet, $data); // Completamos la tarea con la ultima fichada del día
						$set = json_decode($set, true);

						if ($set['status'] == 'error') {
							$textLog = "Tarea (#$data[tareID]) no completada. $set[Mensaje]";
							fileLog($textLog, $pathLog); // Escribimos en el log la respuesta de la tarea
							auditoria("$textLog.", 'P', '', '37');
						} else {
							$textLog = "Tarea (#$data[tareID]) completada. Hora de Fin: $data[TareFechaFin] $data[TareHoraFin]. Tipo: $data[finTipo]";
							fileLog($textLog, $pathLog); // Escribimos en el log la respuesta de la tarea
							auditoria("$textLog.", 'P', '', '37');
						}
					} else {
						$textLog = "Tarea (#$v[TareID]) No completada. Fin de turno Menor: $FicHorS. Tipo: turno";
						fileLog($textLog, $pathLog); // Escribimos en el log la respuesta de la tarea
						$info[] = $textLog;
						// auditoria("$textLog.", 'P', '', '37');
					}
				} else {
					$textLog = "Tarea (#$v[TareID]) No completada. Día no Laboral";
					fileLog($textLog, $pathLog); // Escribimos en el log la respuesta de la tarea
					$info[] = $textLog;
					// auditoria("$textLog.", 'P', '', '37');
				}
			}
		} else { // SI NO ES HORARIOS NOCTURNO PROSESAMOS
			if ($TareIni < $RegHoReMin) { // Si la hora de inicio de la tarea es menor a la ultima fichada del día
				$FechaFinFic = new DateTime($RegHoRe);
				$FechaFinFic = redondearTareFin($v['TareID'], $FechaFinFic->format('Y-m-d H:i'));
				// $FeHora2 = $FechaFinFic->format('d/m/Y');
				$RegHoRe = $FechaFinFic;
				// print_r($RegHoRe).exit;
				// print_r(FechaFormatVar($RegHoRe['date'], 'H:i')).exit;
				$data = array(
					'tareID' => $v['TareID'],
					'tarComplete' => 'tarComplete',
					'tarCompletePend' => 1,
					'fromTareas' => 'true',
					'TareFechaFin' => FechaFormatVar($v['fechas']['TareIni'], 'd/m/Y'),
					'TareHoraFin' => FechaFormatVar($RegHoRe['date'], 'H:i'),
					'finTipo' => 'fichada',
				);

				$urlSet = $urlHost . "/" . HOMEHOST . "/proy/finalizar/process.php";
				$set = sendRemoteData($urlSet, $data); // Completamos la tarea con la ultima fichada del día

				$set = json_decode($set, true);

				if ($set['status'] == 'error') {
					$textLog = "Tarea (#$data[tareID]) no completada. $set[Mensaje]";
					fileLog($textLog, $pathLog); // Escribimos en el log la respuesta de la tarea
					$info[] = $textLog;
					auditoria("$textLog.", 'P', '', '37');
				} else {
					$textLog = "Tarea (#$data[tareID]) completada. Hora de Fin: $data[TareFechaFin] $data[TareHoraFin]. Tipo: $data[finTipo]";
					$info[] = $textLog;
					fileLog($textLog, $pathLog); // Escribimos en el log la respuesta de la tarea
					auditoria("$textLog.", 'P', '', '37');
				}
			} else {
				$FicHorSMin = ($a['FicHorSMin']); // Hora de fin (Salida) de turno del día
				$FicHorS = ($a['FicHorS']); // Hora de fin (Salida) de turno del día
				if ($a['FicDiaL'] == 1) { // Si el día es laborable
					if ($TareIni < $FicHorSMin) { // Si la hora de inicio de la tarea es menor a la hora de fin de turno del día
						$data = array(
							'tareID' => $v['TareID'],
							'tarComplete' => 'tarComplete',
							'fromTareas' => 'true',
							'TareFechaFin' => FechaFormatVar($v['fechas']['TareIni'], 'd/m/Y'),
							'TareHoraFin' => $FicHorS,
							'finTipo' => 'turno',
						);
						$urlSet = $urlHost . "/" . HOMEHOST . "/proy/finalizar/process.php"; // Url para completar la tarea
						$set = sendRemoteData($urlSet, $data); // Completamos la tarea con la ultima fichada del día
						$set = json_decode($set, true);

						if ($set['status'] == 'error') {
							$textLog = "Tarea (#$data[tareID]) no completada. $set[Mensaje]";
							$info[] = $textLog;
							fileLog($textLog, $pathLog); // Escribimos en el log la respuesta de la tarea
							auditoria("$textLog.", 'P', '', '37');
						} else {
							$textLog = "Tarea (#$data[tareID]) completada. Hora de Fin: $data[TareFechaFin] $data[TareHoraFin]. Tipo: $data[finTipo]";
							$info[] = $textLog;
							fileLog($textLog, $pathLog); // Escribimos en el log la respuesta de la tarea
							auditoria("$textLog.", 'P', '', '37');
						}
					} else {
						$textLog = "Tarea (#$v[TareID]) No completada. Fin de turno Menor: $FicHorS. Tipo: turno";
						fileLog($textLog, $pathLog); // Escribimos en el log la respuesta de la tarea
						$info[] = $textLog;
						// auditoria("$textLog.", 'P', '', '37');
					}
				} else {
					$textLog = "Tarea (#$v[TareID]) No completada. Día no Laboral";
					fileLog($textLog, $pathLog); // Escribimos en el log la respuesta de la tarea
					$info[] = $textLog;
					// auditoria("$textLog.", 'P', '', '37');
				}
			}
		}
	}
	$data = array('status' => 'ok', 'Mensaje' => 'Tareas procesadas', 'Info' => $info);
	echo json_encode($data);
	fileLog("Fin de proceso de Tareas Pendientes" . PHP_EOL, $pathLog) . exit; // Escribimos en el log el fin del proceso de tareas pendientes
} else if (($_POST['assignTar'])) {

	$TarePlano = $_POST['TarePlano'] ?? '';
	$TareProy = $_POST['TareProy'] ?? '';
	$TareProc = $_POST['TareProc'] ?? '';
	$TareResp = $_POST['TareResp'] ?? '';
	$TareFechaIni = $_POST['TareFechaIni'] ?? '';
	$TareFechaFin = $_POST['TareFechaFin'] ?? '';
	$TareHoraIni = $_POST['TareHoraIni'] ?? '';
	$TareHoraFin = $_POST['TareHoraFin'] ?? '';

	emptyData($TareProy, 'No se recibieron datos del proyecto'); // Validar que se recibieron datos del proyecto
	emptyData($TareProc, 'No se recibieron datos del proceso'); // Validar que se recibieron datos del proyecto
	emptyData($TareFechaIni, 'La fecha de inicio es requerida');
	emptyData($TareFechaFin, 'La fecha de fin es requerida');
	emptyData($TareHoraIni, 'La hora de Inicio es requerida');

	$tareIni = dr_($TareFechaIni) . ' ' . $TareHoraIni . ':00';
	$tareFin = ($TareHoraFin) ? dr_($TareFechaFin) . ' ' . $TareHoraFin . ':00' : '';

	if (intval(FechaString(dr_($TareFechaIni))) < intval(FechaString(dr_($TareFechaFin)))) {
		emptyData($TareHoraFin, 'La hora de fin es requerida');
		PrintRespuestaJson('error', 'La fecha fin debe ser igual a la fecha de inicio');
		exit;
	}
	if (intval(FechaString(dr_($TareFechaIni))) < intval(date('Ymd'))) {
		emptyData($TareFechaFin, 'La fecha de fin es requerida');
		emptyData($TareHoraFin, 'La hora de fin es requerida');
	}

	if ($TareHoraFin) {
		$horaIni = intval(str_replace(':', '', $TareHoraIni));
		$horaFin = intval(str_replace(':', '', $TareHoraFin));
		if (intval(FechaString(dr_($TareFechaFin))) . $horaFin < intval(FechaString(dr_($TareFechaIni))) . $horaIni) {
			PrintRespuestaJson('error', 'La hora de fin debe ser menor a la hora de inicio');
			exit;
		}
	}

	$qProc = "SELECT ProcCost FROM proy_proceso WHERE ProcID = '$TareProc'";
	$qProy = "SELECT ProyEmpr FROM proy_proyectos INNER JOIN proy_empresas ON proy_proyectos.ProyEmpr = proy_empresas.EmpID WHERE ProyID = '$TareProy'";
	$dataProc = simple_pdoQuery($qProc);
	$dataProy = simple_pdoQuery($qProy);
	$TarePlano = empty($TarePlano) ? 'NULL' : $TarePlano;

	$TareFinTipo = ($TareHoraFin) ? 'manual' : 'normal';

	if (($TareHoraFin)) {
		$calcLimitTar = calcLimitTar($tareIni, $tareFin); // Calculamos el limite de tiempo de la tarea
		$textErrorCalc = "El límite de tiempo para las tareas es de: $calcLimitTar[limitHor] Hs. Y el tiempo calculado es: $calcLimitTar[diffHor] Hs.";
		if ($calcLimitTar['status']) {
			$data = array('status' => 'error', 'Mensaje' => $textErrorCalc, 'confTar' => ($calcLimitTar));
			echo json_encode($data);
			exit; // Si el limite de tiempo de la tarea es mayor a la diferencia de tiempo de la tarea. Salimos del script
		}
	}

	// $tareFin = ($TareHoraFin) ? $tareFin : '';
	$tareFin = '';

	$i = "INSERT INTO `proy_tareas` (`TareEmp`, `TareProy`, `TareResp`, `TareProc`, `TarePlano`, `TareCost`, `TareIni`, `TareFin`, `TareFinTipo`, `Cliente`) VALUES ( '$dataProy[ProyEmpr]', '$TareProy', '$TareResp', '$TareProc', $TarePlano, '$dataProc[ProcCost]', '$tareIni', '$tareFin', '$TareFinTipo','$Cliente')";

	if (pdoQuery($i)) {

		$r = "SELECT `TareID`, `TareCost` FROM `proy_tareas` WHERE `TareEmp` = '$dataProy[ProyEmpr]' AND `TareProy` = '$TareProy' AND `TareResp` = '$TareResp' AND `TareProc` = '$TareProc' AND `TareCost` = '$dataProc[ProcCost]' AND `TareIni` = '$tareIni' AND `Cliente` = '$Cliente' ORDER BY `TareID` DESC LIMIT 1";
		$dataTar = simple_pdoQuery($r);

		if ($TareHoraFin) { // Si viene la hora de Fin calculamos las horas y costo.
			// $duration = diffStartEnd($tareIni, $tareFin);

			// $cost = floatval($dataTar['TareCost']) ?? '';
			// $cost = ($cost / 60) * $duration['diffInMinutes'];

			// $r = simple_pdoQuery("SELECT 1 FROM `proy_tare_horas` WHERE `TareHorID` = '$dataTar[TareID]' LIMIT 1");
			// ($r) ? pdoQuery("DELETE FROM `proy_tare_horas` WHERE `TareHorID` = '$dataTar[TareID]'") : '';
			// $i = "INSERT INTO `proy_tare_horas` (`TareHorID`, `TareHorProy`, `TareHorCost`, `TareHorHoras`, `TareHorMin`) VALUES ('$dataTar[TareID]', '$TareProy', '$cost', '$duration[duration]', '$duration[diffInMinutes]')";
			// (!pdoQuery($i)) ? PrintRespuestaJson('error', 'Error al finalizar la tarea') . exit : ''; // Sino se pudo insertar la tarea, Salimos del script

			$getRecid = simple_pdoQuery("SELECT c.recid FROM clientes c INNER JOIN proy_tareas pt ON c.id = pt.Cliente WHERE pt.TareID = '$dataTar[TareID]' LIMIT 1"); // Obtenemos el recid de la cuenta
			$d = getIniCuenta($getRecid['recid']);
			$urlHost = $d['hostCHWeb']; // Obtener el host de la cuenta

			$data = array(
				'tareID' => $dataTar['TareID'],
				'tarComplete' => 'tarComplete',
				'fromTareas' => 'true',
				'TareFechaFin' => $TareFechaFin,
				'TareHoraFin' => $TareHoraFin
			);

			// echo json_encode($data);
			// exit;

			$urlSet = $urlHost . "/" . HOMEHOST . "/proy/finalizar/process.php";
			$set = sendRemoteData($urlSet, $data); // Completamos la tarea con la ultima fichada del día

			$set = json_decode($set, true);


			// if ($set['status'] == 'error') {
			// 	$textLog = "Tarea (#$data[tareID]) no completada. $set[Mensaje]";
			// 	fileLog($textLog, $pathLog); // Escribimos en el log la respuesta de la tarea
			// 	$info[] = $textLog;
			// 	auditoria("$textLog.", 'P', '', '37');
			// } else {
			// 	$textLog = "Tarea (#$data[tareID]) completada. Hora de Fin: $data[TareFechaFin] $data[TareHoraFin]";
			// 	$info[] = $textLog;
			// 	auditoria("$textLog.", 'P', '', '37');
			// }
		}

		$data = array('status' => 'ok', 'Mensaje' => "Tarea (#$dataTar[TareID]) asignada correctamente", 'datatar' => $dataTar);
		echo json_encode($data);
		auditoria("Tarea (#$dataTar[TareID]) asignada correctamente", 'A', '', '37');
		exit;
	} else {
		PrintRespuestaJson('error', 'Error al iniciar la tarea');
	}
	exit;
}
