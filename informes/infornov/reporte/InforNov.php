<body>
chunk
    <?php
    require __DIR__ . '../data.php';
    ?>
    <!-- Encabezado -->
    <?php
    $colorRes = 'style="background:#ffff8d"';
    foreach ($dataAgrup as $key => $valueAgrup) {
        /** Recorremos matriz de legajos o fecha segun fecha Ini y Fin */
    ?>
        <div style="page-break-inside: avoid">
            <!-- <div class="mt-1" style="background:#333"></div> -->
           <hr>
            <table class="">
                <tr>
                    <th style="width:5%" class="">
                        <?php
                        $Label = ($_Por == 'Fech') ? 'Fecha:' : 'Legajo';
                        echo $Label;
                        ?>
                    </th>
                    <th style="width:45%">
                        <?php
                        $LabelVal = ($_Por == 'Fech') ? '(' . $valueAgrup['Dia'] . ') ' . FechaFormatVar($valueAgrup['Fecha'], 'd/m/Y') : '(' . $valueAgrup['Legajo'] . ') ' . $valueAgrup['Nombre'];
                        echo '<p class="bold">' . $LabelVal . '</p>';
                        ?>
                        <!-- <p class="bold">(<?= $valueAgrup['Legajo'] ?>) <?= $valueAgrup['Nombre'] ?></p> -->
                    </th>
                    <th style="width:50%" class="right">
                    </th>
                </tr>
                <tr>
                    <th style="width:5%" class="">
                        <?php
                        $Label2 = ($_Por == 'Fech') ? '' : 'Cuil: ';
                        echo $Label2;
                        ?>
                    </th>
                    <th style="width:45%">
                        <?php
                        $Label2Val = ($_Por == 'Fech') ? '' : $valueAgrup['Cuil'];
                        echo $Label2Val;
                        ?>
                    </th>
                    <th style="width:50%" class="right">
                    </th>
                </tr>
            </table>
            <hr>
            <table>
                <tr>
                    <?php
                    if ($_Por == 'Fech') {
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
                switch ($_resaltar) {
                    case 'r_tar':
                        $_res_tipo = '0';
                        break;
                    case 'r_inc':
                        $_res_tipo = '1';
                        break;
                    case 'r_sal':
                        $_res_tipo = '2';
                        break;
                    default:
                        $_res_tipo = '99';
                        break;
                }
                require __DIR__ . '../data2.php';


                $count = count($dataNovedades);
                $padding = '';
                foreach ($dataNovedades as $key => $ValueDataNovedades) {
                    if ($_Por != 'Fech') {
                        if ($ValueDataNovedades['Fecha'] === ($dataNovedades[$key - 1]['Fecha'])) {
                            $ValueDataNovedades['Fecha'] = '-';
                            $ValueDataNovedades['Horario'] = '-';
                            $ValueDataNovedades['Dia'] = '-';
                            $padding = 'style="padding-bottom:7px;"';
                        } else {
                            $padding = '';
                        }
                    } else {
                        if ($ValueDataNovedades['Legajo'] === ($dataNovedades[$key - 1]['Legajo'])) {
                            $ValueDataNovedades['Legajo'] = '-';
                            $ValueDataNovedades['Nombre'] = '-';
                            $ValueDataNovedades['Horario'] = '-';
                            $padding = 'style="padding-bottom:7px;"';
                        } else {
                            $padding = '';
                        }
                    }

                    if ($_resaltar == 'r_aus') {
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
                        $style = ($ValueDataNovedades['TipoN'] == "$_res_tipo") ? $colorRes : '';
                    }

                ?>
                    <tr <?= $style ?>>
                        <?php
                        if ($_Por == 'Fech') {
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
                    <th class="bold px-2 center">Horas</th>
                    <th class="bold px-2 center">Cant.</th>
                </tr>
                <?php
                if ($tc['Llegada tarde']) {
                    $style = ($_resaltar == 'r_tar') ? $colorRes : '';
                ?>
                    <tr <?= $style ?>>
                        <td class="px-2">Llegadas Tarde</td>
                        <td class="px-2 center"><?= MinHora(array_sum($t)) ?></td>
                        <td class="px-2 center"><?= $tc['Llegada tarde'] ?></td>
                    </tr>
                <?php
                    unset($t);
                }
                if ($sc['Salida anticipada']) {
                    $style = ($_resaltar == 'r_sal') ? $colorRes : '';
                ?>
                    <tr <?= $style ?>>
                        <td class="px-2">Salidas Anticipadas</td>
                        <td class="px-2 center"><?= MinHora(array_sum($s)) ?></td>
                        <td class="px-2 center"><?= $sc['Salida anticipada'] ?></td>
                    </tr>
                <?php
                    unset($s);
                }
                if ($ic['Incumplimiento']) {
                    $style = ($_resaltar == 'r_inc') ? $colorRes : '';
                ?>
                    <tr <?= $style ?>>
                        <td class="px-2">Incumplimientos</td>
                        <td class="px-2 center"><?= MinHora(array_sum($i)) ?></td>
                        <td class="px-2 center"><?= $ic['Incumplimiento'] ?></td>
                    </tr>
                <?php
                    unset($i);
                }
                if ($ac['Ausencia']) {
                    $style = ($_resaltar == 'r_aus') ? $colorRes : '';
                ?>
                    <tr <?= $style ?>>
                        <td class="px-2">Ausencias</td>
                        <td class="px-2 center"><?= MinHora(array_sum($a)) ?></td>
                        <td class="px-2 center"><?= $ac['Ausencia'] ?></td>
                    </tr>
                <?php
                    unset($a);
                }
                if ($lc['Licencia']) {
                    $style = ($_resaltar == 'r_aus') ? $colorRes : '';
                ?>
                    <tr <?= $style ?>>
                        <td class="px-2">Licencias</td>
                        <td class="px-2 center"><?= MinHora(array_sum($l)) ?></td>
                        <td class="px-2 center"><?= $lc['Licencia'] ?></td>
                    </tr>
                <?php
                    unset($l);
                }
                if ($acc['Accidente']) {
                    $style = ($_resaltar == 'r_aus') ? $colorRes : '';
                ?>
                    <tr <?= $style ?>>
                        <td class="px-2">Accidentes</td>
                        <td class="px-2 center"><?= MinHora(array_sum($ac1)) ?></td>
                        <td class="px-2 center"><?= $acc['Accidente'] ?></td>
                    </tr>
                <?php
                    unset($ac1);
                }
                if ($vc['Vacaciones']) {
                    $style = ($_resaltar == 'r_aus') ? $colorRes : '';
                ?>
                    <tr <?= $style ?>>
                        <td class="px-2">Vacaciones</td>
                        <td class="px-2 center"><?= MinHora(array_sum($v)) ?></td>
                        <td class="px-2 center"><?= $vc['Vacaciones'] ?></td>
                    </tr>
                <?php
                    unset($v);
                }
                if ($suc['Suspensión']) {
                    $style = ($_resaltar == 'r_aus') ? $colorRes : '';
                ?>
                    <tr <?= $style ?>>
                        <td class="px-2">Suspensiones</td>
                        <td class="px-2 center"><?= MinHora(array_sum($sus)) ?></td>
                        <td class="px-2 center"><?= $suc['Suspensión'] ?></td>
                    </tr>
                <?php
                    unset($sus);
                }
                if ($arc['ART']) {
                    $style = ($_resaltar == 'r_aus') ? $colorRes : '';
                ?>
                    <tr <?= $style ?>>
                        <td class="px-2">ART</td>
                        <td class="px-2 center"><?= MinHora(array_sum($art)) ?></td>
                        <td class="px-2 center"><?= $arc['ART'] ?></td>
                    </tr>
                <?php
                    unset($art);
                }
                if ($todoc) {
                    // $style=($_resaltar== 'r_aus' ) ? $colorRes:'';
                ?>
                    <tr>
                        <td class="px-2"></td>
                        <td class="px-2 center"><?= MinHora(array_sum($todo)) ?></td>
                        <td class="px-2 center"><?= $todoc ?></td>
                    </tr>
                <?php
                    unset($todo);
                }
                ?>
            </table>
            <!-- Fin Tabla de Resumen -->
            <p class="my-1">Total Novedades : <?= $count ?></p>
            <!-- <hr class="border-top"> -->
        </div>
    <?php
        $countLega = count($dataAgrup);
        if ($_SaltoPag == '1') {
            /** Si se activa el salto de pagina por legajo */
            if ($valueAgrup != end($dataAgrup)) {
                // Este código se ejecutará para todos menos el último
                echo '<div style="page-break-before: always; clear:both"></div>';
            }
        }

        // unset($dataNovedades);
    }
    /** FIN  matriz de legajos segun fecha Ini y Fin */
    unset($dataAgrup);
    sqlsrv_close($link);
    ?>
</body>