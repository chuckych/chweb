<body>
    chunk
    <?php
    require __DIR__ . '/data.php';
    ?>
    <!-- Encabezado -->
    <?php
    $_Por ??= '';
    $v = [];
    $todo = [];
    foreach ($dataAgrup as $key => $valueAgrup) {
        /** Recorremos matriz de legajos o fecha segun fecha Ini y Fin */
        ?>
        <div style="page-break-inside: avoid">
            <hr>
            <table style="font-size: 18pt">
                <tr>
                    <th style="width:5%;">
                        <?php
                        $Label = ($_Por == 'Fech') ? 'Fecha:' : 'Legajo';
                        echo $Label;
                        ?>
                    </th>
                    <th style="width:45%;">
                        <?php
                        $LabelVal = ($_Por == 'Fech') ? '(' . $valueAgrup['Dia'] . ') ' . FechaFormatVar($valueAgrup['Fecha'], 'd/m/Y') : '(' . $valueAgrup['Legajo'] . ') ' . $valueAgrup['Nombre'];
                        echo "<p class=\"bold\">$LabelVal</p>";
                        ?>
                    </th>
                    <th style="width:50%;" class="right">
                    </th>
                </tr>
                <tr>
                    <th style="width:5%;">
                        <?php
                        $Label2 = ($_Por == 'Fech') ? '' : 'Cuil: ';
                        echo $Label2;
                        ?>
                    </th>
                    <th style="width:45%;">
                        <?php
                        $Label2Val = ($_Por == 'Fech') ? '' : $valueAgrup['Cuil'];
                        echo $Label2Val;
                        ?>
                    </th>
                    <th style="width:50%;" class="right">
                    </th>
                </tr>
            </table>
            <hr>
            <table>
                <tr>
                    <?php
                    switch ($_Por) {
                        case 'Fech':
                            echo '<th class="bold px-2">Legajo</th>';
                            echo '<th class="bold px-2">Nombre</th>';
                            break;
                        default:
                            echo '<th class="bold px-2">Fecha</th>';
                            echo '<th class="bold px-2">D&iacute;a</th>';
                            break;
                    }
                    ?>
                    <!-- <th class="bold px-2">Horario</th> -->
                    <th class="bold px-2">Cod</th>
                    <th class="bold px-2">Novedad</th>
                    <th class="bold px-2">Horas</th>
                    <th class="bold px-2">Valor</th>
                    <th class="bold px-2">Observaci&oacute;n</th>
                </tr>
                <?php

                require __DIR__ . '/data2.php';

                $padding = '';
                $totalesPorCodigo = [];
                foreach ($dataNovedades as $key2 => $ValueDataNovedades) {
                    $codigo = $ValueDataNovedades['Codigo'] ?? '';
                    $novedad = $ValueDataNovedades['Novedad'] ?? '';
                    $valorNumerico = is_numeric($ValueDataNovedades['Valor'] ?? null) ? (float) $ValueDataNovedades['Valor'] : 0;

                    if (!isset($totalesPorCodigo[$codigo])) {
                        $totalesPorCodigo[$codigo] = [
                            'Novedad' => $novedad,
                            'Total' => 0,
                        ];
                    }
                    $totalesPorCodigo[$codigo]['Total'] += $valorNumerico;

                    if ($_Por != 'Fech') {
                        if ($ValueDataNovedades['Fecha'] === ($dataNovedades[$key2 - 1]['Fecha'] ?? '')) {
                            $ValueDataNovedades['Fecha'] = '-';
                            $ValueDataNovedades['Horario'] = '-';
                            $ValueDataNovedades['Dia'] = '-';
                            $padding = 'style="padding-bottom:7px;"';
                        } else {
                            $padding = '';
                        }
                    } else {
                        if ($ValueDataNovedades['Legajo'] === ($dataNovedades[$key2 - 1]['Legajo'] ?? '')) {
                            $ValueDataNovedades['Legajo'] = '-';
                            $ValueDataNovedades['Nombre'] = '-';
                            $ValueDataNovedades['Horario'] = '-';
                            $padding = 'style="padding-bottom:7px;"';
                        } else {
                            $padding = '';
                        }
                    }

                    ?>
                    <tr>
                        <?php
                        switch ($_Por) {
                            case 'Fech':
                                echo '<th class="px-2">' . $ValueDataNovedades['Legajo'] . '</th>';
                                echo '<th class="px-2">' . $ValueDataNovedades['Nombre'] . '</th>';
                                break;
                            default:
                                echo '<th class="px-2">' . $ValueDataNovedades['Fecha'] . '</th>';
                                echo '<th class="px-2">' . $ValueDataNovedades['Dia'] . '</th>';
                                break;
                        }
                        $TipoOnovHoras = $ValueDataNovedades['TipoOnov'] === 'Horas' ? $ValueDataNovedades['Valor'] : '-';
                        $TipoOnovValor = $ValueDataNovedades['TipoOnov'] === 'Valor' ? $ValueDataNovedades['Valor'] : '-';
                        ?>
                        <!-- <td class="px-2"><?php // echo $ValueDataNovedades['Horario'] ?></td> -->
                        <td class="px-2"><?= $ValueDataNovedades['Codigo'] ?></td>
                        <td class="px-2" <?= $padding ?>><?= $ValueDataNovedades['Novedad'] ?></td>
                        <td class="px-2"><?= $TipoOnovHoras ?></td>
                        <td class="px-2"><?= $TipoOnovValor ?></td>
                        <td class="px-2"><?= $ValueDataNovedades['Observacion'] ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <hr>
            <table>
                <tr>
                    <th class="bold px-2">Cod</th>
                    <th class="bold px-2">Novedad</th>
                    <th class="bold px-2">Total</th>
                </tr>
                <?php foreach ($totalesPorCodigo as $codigo => $datoTotal): ?>
                    <tr>
                        <td class="px-2"><?= $codigo ?></td>
                        <td class="px-2"><?= $datoTotal['Novedad'] ?></td>
                        <td class="px-2"><?= $datoTotal['Total'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <br>
        </div>
        <?php
        $countLega = count($dataAgrup);
        if (($_SaltoPag ??= '') == '1') {
            /** Si se activa el salto de pagina por legajo */
            if ($valueAgrup != end($dataAgrup)) {
                // Este código se ejecutará para todos menos el último
                echo '<div style="page-break-before: always; clear:both"></div>';
            }
        }

        unset($dataNovedades);
    }
    unset($dataAgrup);
    ?>
</body>