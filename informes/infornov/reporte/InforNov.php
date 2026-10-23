<body>
    chunk
    <?php
    require __DIR__ . '/data.php';
    ?>
    <!-- Encabezado -->
    <?php
    $v = [];
    $todo = [];
    $colorRes = 'style="background:#ffff8d"';
    // if ($dataAgrup ?? '') {
    foreach ($dataAgrup as $key => $valueAgrup) {
        /** Recorremos matriz de legajos o fecha segun fecha Ini y Fin */
        ?>
        <div style="page-break-inside: avoid">
            <!-- <div class="mt-1" style="background:#333"></div> -->
            <hr>
            <table class="">
                <tr>
                    <th style="width:5%; font-size: 14px" class="bold px-2">
                        <?php
                        $Label = (($_Por ?? '') == 'Fech') ? 'Fecha:' : 'Legajo';
                        echo $Label;
                        ?>
                    </th>
                    <th style="width:45%; font-size: 14px" class="px-2">
                        <?php
                        $LabelVal = (($_Por ?? '') == 'Fech') ? '(' . $valueAgrup['Dia'] . ') ' . FechaFormatVar($valueAgrup['Fecha'], 'd/m/Y') : '(' . $valueAgrup['Legajo'] . ') ' . $valueAgrup['Nombre'];
                        echo '<p class="bold">' . $LabelVal . '</p>';
                        ?>
                        <!-- <p class="bold">(<?= $valueAgrup['Legajo'] ?>) <?= $valueAgrup['Nombre'] ?></p> -->
                    </th>
                    <th style="width:50%" class="right">
                    </th>
                </tr>
                <tr>
                    <th style="width:5%; font-size: 14px" class="bold px-2">
                        <?php
                        $Label2 = (($_Por ?? '') == 'Fech') ? '' : 'Cuil: ';
                        echo $Label2;
                        ?>
                    </th>
                    <th style="width:45%; font-size: 14px" class="px-2">
                        <?php
                        $Label2Val = (($_Por ?? '') == 'Fech') ? '' : $valueAgrup['Cuil'];
                        echo $Label2Val;
                        ?>
                    </th>
                    <th style="width:50%; font-size: 14px" class="right">
                    </th>
                </tr>
            </table>
            <hr>
            <table>
                <tr>
                    <?php
                    if (($_Por ?? '') == 'Fech') {
                        echo '<th class="bold px-2">Legajo</th>';
                        echo '<th class="bold px-2">Nombre</th>';
                    } else {
                        echo '<th class="bold px-2">Fecha</th>';
                        echo '<th class="bold px-2">D&iacute;a</th>';
                    }
                    ?>
                    <th class="bold px-2">Horario</th>
                    <th class="bold px-2">Cod</th>
                    <th class="bold px-2">Novedad</th>
                    <th class="bold px-2">Horas</th>
                    <th class="bold px-2">Causa</th>
                    <th class="bold px-2">Observaci&oacute;n</th>
                </tr>
                <?php
                $_res_tipo = match (($_resaltar ?? '')) {
                    'r_tar' => '0',
                    'r_inc' => '1',
                    'r_sal' => '2',
                    default => '99',
                };
                require __DIR__ . '/data2.php';


                $count = count($dataNovedades);
                $padding = '';
                foreach ($dataNovedades as $key2 => $ValueDataNovedades) {
                    if (($_Por ?? '') != 'Fech') {
                        if ($ValueDataNovedades['Fecha'] === (($dataNovedades[$key2 - 1]['Fecha']) ?? '')) {
                            $ValueDataNovedades['Fecha'] = '-';
                            $ValueDataNovedades['Horario'] = '-';
                            $ValueDataNovedades['Dia'] = '-';
                            $padding = 'style="padding-bottom:7px;"';
                        } else {
                            $padding = '';
                        }
                    } else {
                        if ($ValueDataNovedades['Legajo'] === (($dataNovedades[$key2 - 1]['Legajo']) ?? '')) {
                            $ValueDataNovedades['Legajo'] = '-';
                            $ValueDataNovedades['Nombre'] = '-';
                            $ValueDataNovedades['Horario'] = '-';
                            $padding = 'style="padding-bottom:7px;"';
                        } else {
                            $padding = '';
                        }
                    }

                    if (($_resaltar ?? '') == 'r_aus') {
                        switch ($ValueDataNovedades['TipoN']) {
                            case '3':
                            case '4':
                            case '5':
                            case '6':
                            case '7':
                            case '8':
                                $style = $colorRes;
                                break;
                            default:
                                $style = '';
                                break;
                        }
                    } else {
                        $style = ($ValueDataNovedades['TipoN'] == (($_res_tipo ?? ''))) ? $colorRes : '';
                    }

                    ?>
                    <tr <?= $style ?>>
                        <?php
                        if (($_Por ?? '') == 'Fech') {
                            echo '<th class="px-2">' . $ValueDataNovedades['Legajo'] . '</th>';
                            echo '<th class="px-2">' . $ValueDataNovedades['Nombre'] . '</th>';
                        } else {
                            echo '<th class="px-2">' . $ValueDataNovedades['Fecha'] . '</th>';
                            echo '<th class="px-2">' . $ValueDataNovedades['Dia'] . '</th>';
                        }
                        ?>
                        <td class="px-2"><?= $ValueDataNovedades['Horario'] ?></td>
                        <td class="px-2"><?= $ValueDataNovedades['Codigo'] ?></td>
                        <td class="px-2" <?= $padding ?>><?= $ValueDataNovedades['Novedad'] ?></td>
                        <td class="px-2"><?= $ValueDataNovedades['Horas'] ?></td>
                        <td class="px-2"><?= $ValueDataNovedades['Causa'] ?></td>
                        <td class="px-2"><?= $ValueDataNovedades['Observacion'] ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <hr>
            <!-- <div class="mt-1"><b>Resumen:</b></div> -->
            <!-- Tabla de Resumen -->
            <table class="mt-1">
                <tr>
                    <th class="bold px-2 py-1">Resumen:</th>
                    <th class="bold px-2">Horas</th>
                    <th class="bold px-2">Cant.</th>
                </tr>
                <?php
                if (!function_exists('safe_array_sum')) {
                    function safe_array_sum($values)
                    {
                        if (!is_array($values)) {
                            $values = [$values];
                        }

                        $sum = 0;
                        foreach ($values as $value) {
                            if ($value === '' || $value === null) {
                                continue;
                            }

                            if (is_numeric($value)) {
                                $sum += $value + 0;
                            }
                        }

                        return $sum;
                    }
                }

                if ($tc['Llegada tarde'] ?? '') {
                    $style = (($_resaltar ?? '') == 'r_tar') ? $colorRes : '';
                    ?>
                    <tr <?= $style ?>>
                        <td class="px-2">Llegadas Tarde</td>
                        <td class="px-2"><?= MinHora(safe_array_sum($t)) ?></td>
                        <td class="px-2"><?= $tc['Llegada tarde'] ?></td>
                    </tr>
                    <?php
                    unset($t);
                }
                if ($sc['Salida anticipada'] ?? '') {
                    $style = (($_resaltar ?? '') == 'r_sal') ? $colorRes : '';
                    ?>
                    <tr <?= $style ?>>
                        <td class="px-2">Salidas Anticipadas</td>
                        <td class="px-2"><?= MinHora(safe_array_sum($s)) ?></td>
                        <td class="px-2"><?= $sc['Salida anticipada'] ?></td>
                    </tr>
                    <?php
                    unset($s);
                }
                if ($ic['Incumplimiento'] ?? '') {
                    $style = (($_resaltar ?? '') == 'r_inc') ? $colorRes : '';
                    ?>
                    <tr <?= $style ?>>
                        <td class="px-2">Incumplimientos</td>
                        <td class="px-2"><?= MinHora(safe_array_sum($i)) ?></td>
                        <td class="px-2"><?= $ic['Incumplimiento'] ?></td>
                    </tr>
                    <?php
                    unset($i);
                }
                if ($ac['Ausencia'] ?? '') {
                    $style = (($_resaltar ?? '') == 'r_aus') ? $colorRes : '';
                    ?>
                    <tr <?= $style ?>>
                        <td class="px-2">Ausencias</td>
                        <td class="px-2"><?= MinHora(safe_array_sum($a)) ?></td>
                        <td class="px-2"><?= $ac['Ausencia'] ?></td>
                    </tr>
                    <?php
                    unset($a);
                }
                if ($lc['Licencia'] ?? '') {
                    $style = (($_resaltar ?? '') == 'r_aus') ? $colorRes : '';
                    ?>
                    <tr <?= $style ?>>
                        <td class="px-2">Licencias</td>
                        <td class="px-2"><?= MinHora(safe_array_sum($l)) ?></td>
                        <td class="px-2"><?= $lc['Licencia'] ?></td>
                    </tr>
                    <?php
                    unset($l);
                }
                if ($acc['Accidente'] ?? '') {
                    $style = (($_resaltar ?? '') == 'r_aus') ? $colorRes : '';
                    ?>
                    <tr <?= $style ?>>
                        <td class="px-2">Accidentes</td>
                        <td class="px-2"><?= MinHora(safe_array_sum($ac1)) ?></td>
                        <td class="px-2"><?= $acc['Accidente'] ?></td>
                    </tr>
                    <?php
                    unset($ac1);
                }
                if ($vc['Vacaciones'] ?? '') {
                    $style = (($_resaltar ?? '') == 'r_aus') ? $colorRes : '';
                    ?>
                    <tr <?= $style ?>>
                        <td class="px-2">Vacaciones</td>
                        <td class="px-2"><?= MinHora(safe_array_sum($v)) ?></td>
                        <td class="px-2"><?= $vc['Vacaciones'] ?></td>
                    </tr>
                    <?php
                    unset($v);
                }
                if ($suc['Suspensión'] ?? '') {
                    $style = (($_resaltar ?? '') == 'r_aus') ? $colorRes : '';
                    ?>
                    <tr <?= $style ?>>
                        <td class="px-2">Suspensiones</td>
                        <td class="px-2"><?= MinHora(safe_array_sum($sus)) ?></td>
                        <td class="px-2"><?= $suc['Suspensión'] ?></td>
                    </tr>
                    <?php
                    unset($sus);
                }
                if ($arc['ART'] ?? '') {
                    $style = (($_resaltar ?? '') == 'r_aus') ? $colorRes : '';
                    ?>
                    <tr <?= $style ?>>
                        <td class="px-2">ART</td>
                        <td class="px-2"><?= MinHora(safe_array_sum($art)) ?></td>
                        <td class="px-2"><?= $arc['ART'] ?></td>
                    </tr>
                    <?php
                    unset($art);
                }
                if ($todoc) {
                    // $style=($_resaltar== 'r_aus' ) ? $colorRes:'';
                    ?>
                    <tr>
                        <td class="px-2"></td>
                        <td class="px-2\"><?= MinHora(safe_array_sum($todo)) ?></td>
                        <td class="px-2\"><?= $count ?></td>
                    </tr>
                    <?php
                    unset($todo);
                }
                ?>
            </table>
            <p class="my-2 px-2"></p>
            <p class="my-2 px-2" style="font-size: 10px;">Total Novedades : <?= $count ?></p>
            <!-- <hr class="border-top"> -->
        </div>
        <?php
        $countLega = count($dataAgrup);
        if (($_SaltoPag ?? '') == '1') {
            /** Si se activa el salto de pagina por legajo */
            if ($valueAgrup != end($dataAgrup)) {
                // Este código se ejecutará para todos menos el último
                echo '<div style="page-break-before: always; clear:both"></div>';
            }
        }

        // unset($dataNovedades);
    }
    unset($dataAgrup);
    // }
    /** FIN  matriz de legajos segun fecha Ini y Fin */
    ?>
</body>