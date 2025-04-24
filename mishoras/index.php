<?php
require __DIR__ . '/../config/session_start.php';
require __DIR__ . '/../config/index.php';
secure_auth_ch();
$Modulo = '6';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexión a MSSQL redirigimos al inicio
$getData = 'GetGeneral';
$_datos = 'mishoras';
$_SESSION['FechaMinMax'] = (fecha_min_max('FICHAS', 'FICHAS.FicFech'));
$FirstDate = $_SESSION['FechaMinMax']['min'];
/** FirstDate */
$FirstYear = Fech_Format_Var($_SESSION['FechaMinMax']['min'], 'Y');
/** FirstYear */
$maxDate = $_SESSION['FechaMinMax']['max'];
/** maxDate */
$maxYear = date('Y');
/** maxYear */
$FechaIni = date("Y-m-d", strtotime(hoy() . "- 30 days"));
$FechaFin = $_SESSION['FechaMinMax']['max'];
if (BrowserIE()) {
    $icon_bar_chart_fill = '<svg class="mr-1 bi" width="1.4em" height="1.4em" viewBox="0 0 16 16" class="bi bi-bar-chart-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><rect width="4" height="5" x="1" y="10" rx="1" /><rect width="4" height="9" x="6" y="6" rx="1" /><rect width="4" height="14" x="11" y="1" rx="1" /></svg>';
    $icon_calendar_range = '<svg width="1.2em" height="1.4em" viewBox="0 0 16 16" class="bi bi-calendar-range" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z" /><path d="M9 7a1 1 0 0 1 1-1h5v2h-5a1 1 0 0 1-1-1zM1 9h4a1 1 0 0 1 0 2H1V9z" /></svg>';
    $icon_arrow_repeat = '<svg width="1.4em" height="1.4em" viewBox="0 0 16 16" class="bi bi-arrow-repeat" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/><path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/></svg>';
} else {
    $icon_bar_chart_fill = '<svg class="bi mr-1" width="18" height="18" fill="currentColor"><use xlink:href="../img/bootstrap-icons.svg#bar-chart-fill" /></svg>';
    $icon_calendar_range = '<svg class="bi mr-1" width="18" height="18" fill="currentColor"><use xlink:href="../img/bootstrap-icons.svg#calendar-range" /></svg>';
    $icon_arrow_repeat = '<svg class="bi mr-1" width="18" height="18" fill="currentColor"><use xlink:href="../img/bootstrap-icons.svg#arrow-repeat" /></svg>';
}
require pagina('mishoras.php');
