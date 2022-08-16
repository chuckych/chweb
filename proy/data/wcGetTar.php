<?php
$totalRecords = $data = $count = $qTotales = array();
$params = $_REQUEST;
// sleep(1);
$params['TareNum']        = $params['TareNum'] ?? '';
$params['draw']           = $params['draw'] ?? '';
$params['search']         = $params['search'] ?? '';
$params['tarTotales']     = $params['tarTotales'] ?? '';
$params['idCliente']      = $params['idCliente'] ?? '';
$params['procPendientes'] = $params['procPendientes'] ?? '';
$params['HoraLimit']      = $params['HoraLimit'] ?? '';
$_SESSION['ID_CLIENTE']   = $_SESSION['ID_CLIENTE'] ?? $params['idCliente'];
$params['tarProyEsta']    = $params['tarProyEsta'] ?? 'abierto';
// $params['search']['value'] = $params['search']['value'] ?? $params['TareNum'];
// $params['search']['value'] = test_input($params['search']['value']) ?? '';
$params['start']      = $params['start'] ?? 0;
$params['length']     = $params['length'] ?? 1;

$tiempo_inicio = microtime(true);
$w_c = $sqlTot = $sqlRec = "";
($params['testTable'] ?? '') ? $_SESSION['ID_CLIENTE'] = 1 : '';
$params['TareEstado'] = test_input(($params['TareEstado']) ?? 'todos');

if (($params['FiltroTarFechas'] ?? '')) {
    $DateRange = explode(' al ', $params['FiltroTarFechas']);
    $TareIni  = test_input(dr_($DateRange[0]) . ' 00:00:00');
    $TareFin  = test_input(dr_($DateRange[1]) . ' 23:59:59');
    $w_c .= " AND proy_tareas.TareIni >= '$TareIni'";
    $w_c .= " AND proy_tareas.TareIni <= '$TareFin'";
}
$today = date('Y-m-d');
$w_c .= (!empty($params['search']['value'])) ?  " AND CONCAT_WS(' ', TareID) = '" . $params['search']['value'] . "'" : '';
$w_c .= (!empty($params['TareNum'])) ?  " AND TareID = '" . $params['TareNum'] . "'" : '';
$w_c .= " AND proy_tareas.Cliente = '$_SESSION[ID_CLIENTE]'";
$w_c .= " AND TareEsta = '0'";
$w_c .= (test_input($params['TareResp'] ?? '')) ? " AND proy_tareas.TareResp = '$params[TareResp]'" : ''; // Tarea Responsable
$w_c .= (test_input($params['TareID'] ?? '')) ? " AND proy_tareas.TareID = '$params[TareID]'" : ''; // Tarea ID
$w_c .= (test_input($params['TarePend'] ?? '')) ? " AND proy_tareas.TareFin = '0000-00-00 00:00:00'" : ''; // Tarea Pendiente
$w_c .= (test_input($params['TareEstado'] == 'todos')) ? "" : ""; // Todas las Tareas
$w_c .= (test_input($params['TareEstado'] == 'pendientes')) ? " AND proy_tareas.TareFin = '0000-00-00 00:00:00'" : ""; // Tareas Pendientes
$w_c .= (test_input($params['TareEstado'] == 'completadas')) ? " AND proy_tareas.TareFin != '0000-00-00 00:00:00'" : ""; // Tareas Completadas
$w_c .= (test_input($params['tarProyNomFiltro'] ?? '')) ? " AND TareProy = '$params[tarProyNomFiltro]'" : ''; // Filtrar Proyecto
$w_c .= (test_input($params['tarEmprFiltro'] ?? '')) ? " AND TareEmp = '$params[tarEmprFiltro]'" : ''; // Filtrar Empresa
$w_c .= (test_input($params['tarProcNomFiltro'] ?? '')) ? " AND TareProc = '$params[tarProcNomFiltro]'" : ''; // Filtrar Proceso
$w_c .= (test_input($params['tarPlanoFiltro'] ?? '')) ? " AND TarePlano = '$params[tarPlanoFiltro]'" : ''; // Filtrar Plano
$w_c .= (test_input($params['tarRespFiltro'] ?? '')) ? " AND TareResp = '$params[tarRespFiltro]'" : ''; // Filtrar Responsable
$w_c .= (test_input($params['tarProyEsta'])) ? " AND proy_estados.EstTipo = '$params[tarProyEsta]'" : '';

if ($params['procPendientes']) { // Tareas Pendientes de Procesar
    if ($params['HoraLimit']) { // Tareas Pendientes de Procesar con Hora Limite
        $FechaHoraActual = fechaHora2(); // Fecha y Hora Actual
        $FechaHoraCierre = hoy() . ' ' . $params['HoraLimit'] . ':59'; // Fecha y Hora Limite
        $WCTarIniCierre = " AND proy_tareas.TareIni < '$FechaHoraCierre'"; // Tareas con Inicio menor a la Fecha y Hora Limite
        $WCTarIniActual = " AND proy_tareas.TareIni < '$today'"; // Tareas con Inicio menor a la Fecha Actual
        $w_c .= ($FechaHoraCierre > $FechaHoraActual) ? $WCTarIniActual : $WCTarIniCierre;
    } else {
        $w_c .= " AND proy_tareas.TareIni < '$today'"; // Tareas con Inicio menor a la Fecha Actual
    }
}
