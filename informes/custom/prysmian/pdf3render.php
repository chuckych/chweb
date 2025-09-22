<?php
// Archivo de renderizado para el PDF de liquidación con datos reales
// Definir las columnas numéricas para los subtotales
$columnasNumericas = [
    "Hs. a Trab.", "Hs. Trab.", "Hs. Fer.", "(90) Nor.", "Normal E", "(23) Hs. Sab.",
    "(80) Noct.", "(20) 50%", "(21) Sab. 50%", "(10) 100%", "Enf.", "Bl.", "Acc.",
    "Art.", "Descomp.", "Hs. Inj.", "Paro", "Susp.", "(22) Ad Tur. 50%",
    "(12) Ad Tur. 100%", "(45) Ad Ext.", "(35) 3 Tur. Sem.", "(15) Dia Tra.", "Vac"
];

// Función para obtener el color según el estado de fichada
function obtenerColorFichada($estado)
{
    switch (trim($estado)) {
        case 'N':
            return '#000000'; // Negro
        case 'M':
            return '#0000FF'; // Azul
        case 'MM':
            return '#FF0000'; // Rojo
        case 'NM':
            return '#FF0000'; // Rojo
        case '':
            return '#808080'; // Gris para valores vacíos
        default:
            return '#000000'; // Negro por defecto
    }
}

// Agrupar datos por legajo
$datosPorLegajo = [];
foreach ($datos as $fila) {
    $legajo = $fila['Legajo'];
    if (!isset($datosPorLegajo[$legajo])) {
        $datosPorLegajo[$legajo] = [];
    }
    $datosPorLegajo[$legajo][] = $fila;
}

// Array para guardar subtotales de cada legajo
$subtotalesGenerales = [];
foreach ($columnasNumericas as $col) {
    $subtotalesGenerales[$col] = 0;
}

?>

<div style="padding: 5px;">
    <?php foreach ($datosPorLegajo as $legajo => $registrosLegajo): ?>
        <?php if ($legajo !== array_key_first($datosPorLegajo)): ?>
            <pagebreak />
        <?php endif; ?>

        <!-- Información del empleado -->
        <div style="margin-bottom: 15px;">
            <h2
                style="font-size: 10pt; margin-bottom: 5px; border-top: 0pt solid #333; border-bottom: 0pt solid #333; color:#333">
                Legajo: <?php echo htmlspecialchars($legajo); ?>
            </h2>
            <p style="font-size: 10pt; margin-bottom: 5px;">
                <strong><?php echo htmlspecialchars($registrosLegajo[0]['Apellido y Nombre']); ?></strong>
            </p>
            <p style="font-size: 9pt; margin-bottom: 5px;">
                Sector: <?php echo htmlspecialchars($registrosLegajo[0]['Sector'] ?? 'N/A'); ?>
            </p>
        </div>

        <!-- Tabla de datos -->
        <table style="width: 100%; border-collapse: collapse; font-size: 7pt;" border=0>
            <thead>
                <tr style="background-color: #f0f0f0;">
                    <?php $style = "border: 0.1pt solid #333; padding: 3px; font-weight: bold;"; ?>
                    <?php $styleCenter = "border: 0.1pt solid #333; padding: 3px; text-align: center;"; ?>
                    <th style="<?= $style ?>">Fecha</th>
                    <th style="<?= $style ?>">Horario</th>
                    <!-- <th style="<?= $style ?>">Laboral</th> -->
                    <!-- <th style="<?= $style ?>">Feriado</th> -->
                    <th style="<?= $style ?>">Hs. a Trab.</th>
                    <th style="<?= $style ?>">Ingreso</th>
                    <th style="<?= $style ?>">Egreso</th>
                    <th style="<?= $style ?>">Hs. Trab.</th>
                    <th style="<?= $style ?>">Hs. Fer.</th>
                    <th style="<?= $style ?>">(90) Nor.</th>
                    <th style="<?= $style ?>">Normal E</th>
                    <th style="<?= $style ?>">(23) Hs. Sab.</th>
                    <th style="<?= $style ?>">(80) Noct.</th>
                    <th style="<?= $style ?>">(20) 50%</th>
                    <th style="<?= $style ?>">(21) Sab. 50%</th>
                    <th style="<?= $style ?>">(10) 100%</th>
                    <th style="<?= $style ?>">Enf.</th>
                    <th style="<?= $style ?>">Bl.</th>
                    <th style="<?= $style ?>">Acc.</th>
                    <th style="<?= $style ?>">Art.</th>
                    <th style="<?= $style ?>">Descomp.</th>
                    <th style="<?= $style ?>">Hs. Inj.</th>
                    <th style="<?= $style ?>">Paro</th>
                    <th style="<?= $style ?>">Susp.</th>
                    <th style="<?= $style ?>">(22) Ad Tur. 50%</th>
                    <th style="<?= $style ?>">(12) Ad Tur. 100%</th>
                    <th style="<?= $style ?>">(45) Ad Ext.</th>
                    <th style="<?= $style ?>">(35) 3 Tur. Sem.</th>
                    <th style="<?= $style ?>">(15) Dia Tra.</th>
                    <th style="<?= $style ?>">Vac</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Inicializar subtotales para este legajo
                $subtotalesLegajo = [];
                foreach ($columnasNumericas as $col) {
                    $subtotalesLegajo[$col] = 0;
                }

                // Mostrar cada registro del legajo
                foreach ($registrosLegajo as $fila):
                    // Acumular subtotales
                    foreach ($columnasNumericas as $col) {
                        $valor = floatval($fila[$col] ?? 0);
                        $subtotalesLegajo[$col] += $valor;
                        $subtotalesGenerales[$col] += $valor;
                    }
                    ?>
                    <tr>
                        <td style="border: 0.5px solid #333; padding:6px; text-align: left;">
                            <?php echo date('d/m/Y', strtotime($fila['Fecha'])); ?><br />
                            <span style="font-size: 6pt;"><?php echo htmlspecialchars($fila['Día']); ?></span>
                        </td>
                        <td style="border: 0.5px solid #333; padding:6px; text-align: center; white-space: nowrap;">
                            <?php echo htmlspecialchars($fila['Entrada']) . ' a ' . htmlspecialchars($fila['Salida']); ?>
                        </td>
                        <!-- <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php // echo htmlspecialchars($fila['Laboral']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php // echo htmlspecialchars($fila['Feriado']); ?>
                        </td> -->
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['Hs. a Trab.']); ?>
                        </td>
                        <td
                            style="border: 0.5px solid #333; padding: 2px; text-align: center; color: <?php echo obtenerColorFichada($fila['EstFic']); ?>;">
                            <?php echo htmlspecialchars($fila['Ingreso']); ?>
                        </td>
                        <td
                            style="border: 0.5px solid #333; padding: 2px; text-align: center; color: <?php echo obtenerColorFichada($fila['EstUltFic']); ?>;">
                            <?php echo htmlspecialchars($fila['Egreso']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['Hs. Trab.']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['Hs. Fer.']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['(90) Nor.']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['Normal E']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['(23) Hs. Sab.']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['(80) Noct.']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['(20) 50%']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['(21) Sab. 50%']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['(10) 100%']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['Enf.']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['Bl.']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['Acc.']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['Art.']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['Descomp.']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['Hs. Inj.']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['Paro']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['Susp.']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['(22) Ad Tur. 50%']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['(12) Ad Tur. 100%']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['(45) Ad Ext.']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['(35) 3 Tur. Sem.']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['(15) Dia Tra.']); ?>
                        </td>
                        <td style="border: 0.5px solid #333; padding: 2px; text-align: center;">
                            <?php echo htmlspecialchars($fila['Vac']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <!-- Fila de subtotal para este legajo -->
                <tr style="background-color: #e0e0e0; font-weight: bold;">
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 6px; text-align: right;" colspan="2">
                        SUBTOTALES
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['Hs. a Trab.'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;" colspan="2">
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['Hs. Trab.'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['Hs. Fer.'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['(90) Nor.'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['Normal E'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['(23) Hs. Sab.'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['(80) Noct.'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['(20) 50%'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['(21) Sab. 50%'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['(10) 100%'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['Enf.'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['Bl.'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['Acc.'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['Art.'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['Descomp.'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['Hs. Inj.'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['Paro'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['Susp.'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['(22) Ad Tur. 50%'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['(12) Ad Tur. 100%'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['(45) Ad Ext.'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['(35) 3 Tur. Sem.'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['(15) Dia Tra.'], 2); ?>
                    </td>
                    <td style="font-weight: bold; border: 0.5px solid #333; padding: 2px; text-align: center;">
                        <?php echo number_format($subtotalesLegajo['Vac'], 2); ?>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php endforeach; ?>

    <!-- Página final con totales generales -->
    <pagebreak />
    <div style="padding: 10px;">
        <h2
            style="font-size: 10pt; margin-bottom: 10px; border-top: 0.5pt solid #333; border-bottom: 0.5pt solid #333; color:#333; text-align: center; padding-top: 5px; padding-bottom: 5px;">
            RESUMEN GENERAL - TOTALES
        </h2>

        <!-- Layout con tabla y observaciones lado a lado -->
        <table style="width: 100%; border: 0;">
            <tr>
                <td style="width: 30%; vertical-align: top; padding-right: 10px;">
                    <!-- Tabla de totales -->
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f0f0f0;">
                                <th style="border: 0.5px solid #333; padding: 5px; font-weight: bold;">Concepto</th>
                                <th
                                    style="border: 0.5px solid #333; padding: 5px; font-weight: bold; text-align: right;">
                                    Total Horas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($columnasNumericas as $columna): ?>
                                <tr>
                                    <td style="border: 0.5px solid #333; padding: 4px;">
                                        <?php echo htmlspecialchars($columna); ?>
                                    </td>
                                    <td
                                        style="border: 0.5px solid #333; padding: 4px; text-align: right; font-weight: bold;">
                                        <?php echo number_format($subtotalesGenerales[$columna], 2); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </td>
                <td style="width: 70%; vertical-align: top; padding-left: 10px;">
                    <!-- Observaciones -->
                    <div>
                        <p style="font-size: 12pt; margin-bottom: 15px;">Observaciones:</p>
                        <br />
                        <p style="font-size: 10pt; font-weight: bold; margin-bottom: 8px;">Color de Fichadas
                            (Ingreso / Egreso):
                        </p>
                        <hr>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            <span style="color: #000000; font-weight: bold;">Negro</span>: Normal
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            <span style="color: #0000FF; font-weight: bold;">Azul</span>: Manual
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            <span style="color: #FF0000; font-weight: bold;">Rojo</span>: Modificada
                        </li>
                        <br />
                        <p style="font-size: 10pt; font-weight: bold; margin-bottom: 8px;">
                            Conceptos:
                        </p>
                        <hr>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            Hs a Trab: Cantidad de Horas a Trabajar.
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            Hs Trab: Cantidad de Horas Trabajadas.
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            Hs Fer: Cantidad de Horas Trabajadas en Feriado.
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            90 (Nor): Horas Normales. Código 90.
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            Normal E: Suma de Novedades 4, 6, 204, 206, 410.
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            (23) Hs Sab: Horas Trabajadas en Sábado. Código 23.
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            (80) Noct.: Horas Nocturnas. Código 80.
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            (20) 50%.: Horas 50%. Código 20.
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            (21) Sab. 50%: Horas 50% Sábado. Código 21.
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            (10) 100%: Horas 100%. Código 10.
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            Enf: (Enfermedad) Suma de Novedades 3, 104, 203, 401.
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            Bl: (Beneficio Legal) Suma de Novedades 7, 107, 207, 417
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            Acc: (Accidente Empresa) Suma de Novedades 210, 501
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            Art: (Art) Novedad 801
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            Descomp: (Descanso Compensatorio) Novedad 309
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            Hs. Inj: (Horas Injustificadas) Suma de Novedades 1, 2, 8, 101, 103, 201, 202, 208,301,
                            302, 305, 308, 310
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            Paro: Novedad 303
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            Susp: (Suspensión) Suma de Novedades 701, 702
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            (22) Ad Tur. 50%: Horas Adicionales Nocturno 50%. Código 22.
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            (12) Ad Tur. 100%: Horas Adicionales Nocturno 100%. Código 12.
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            (45) Ad Ext.: Horas Adicionales Extras. Código 45.
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            (35) 3 Tur. Sem.: Dias 3er Turno Semanal. Código 35.
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            (15) Dia Tra.: Días Trabajados. Código 15.
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            Vac: (Vacaciones) Novedad 601
                        </li>
                        <br />
                        <div style="padding:5px; font-size: 10pt; font-weight: bold; margin-bottom: 8px;">
                            Información del Reporte:
                        </div>
                        <hr>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            Período: <?php echo $fechaInicio . ' al ' . $fechaFin; ?>
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            Total Legajos: <?php echo count($datosPorLegajo); ?>
                        </li>
                        <li style="font-size: 9pt; margin-bottom: 5px;">
                            Total registros: <?php echo count($datos); ?>
                        </li>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>