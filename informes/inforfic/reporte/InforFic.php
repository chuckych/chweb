<body>
    chunk
    <?php
    require __DIR__ . '/data.php';
    ?>
    <!-- <hr class="border-top">   -->
    <!-- Encabezado -->
    <?php
    foreach ($dataAgrup as $key => $valueAgrup) { /** Recorremos matriz de legajos o fecha segun fecha Ini y Fin */
        ?>
        <div style="page-break-inside: avoid">
            <hr>
            <table>
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
                        echo '<span class="bold">' . $LabelVal . '</span>';
                        ?>
                        <!-- <span class="bold">(<?= $valueAgrup['Legajo'] ?>) <?= $valueAgrup['Nombre'] ?></span> -->
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
                    <th class="bold px-2 center bg-light">Primera</th>
                    <th class="bold px-2 center bg-light">Ultima</th>
                    <th class="bold px-2 center"></th>
                    <th class="bold px-2 center">Entra</th> <!-- Fic_1 -->
                    <th class="bold px-2 center">Sale</th> <!-- Fic_2 -->
                    <th class="bold px-2 center">Entra</th> <!-- Fic_3 -->
                    <th class="bold px-2 center">Sale</th> <!-- Fic_4 -->
                    <th class="bold px-2 center">Entra</th> <!-- Fic_5 -->
                    <th class="bold px-2 center">Sale</th> <!-- Fic_6 -->
                    <th class="bold px-2 center">Entra</th> <!-- Fic_7 -->
                    <th class="bold px-2 center">Sale</th> <!-- Fic_8 -->
                    <th class="bold px-2 center">Entra</th> <!-- Fic_9 -->
                    <th class="bold px-2 center">Sale</th> <!-- Fic_10 -->
                    <th class="bold px-2 center">Entra</th> <!-- Fic_11 -->
                    <th class="bold px-2 center">Sale</th> <!-- Fic_12 -->
                    <th class="bold px-2 center">Entra</th> <!-- Fic_13 -->
                    <th class="bold px-2 center">Sale</th> <!-- Fic_14 -->
                    <th class="bold px-2 center"></th>
                    <th class="bold px-2 center bg-light">Cant</th>
                </tr>
                <?php
                require __DIR__ . '/data2.php';
                $count = count($dataFichadas);
                $padding = '';
                foreach ($dataFichadas as $key => $ValuedataFichadas) {
                    ?>
                    <tr>
                        <?php
                        if ($_Por == 'Fech') {
                            echo '<th class="px-2">' . $ValuedataFichadas['FicLega'] . '</th>';
                            echo '<th class="px-2">' . $ValuedataFichadas['FicNombre'] . '</th>';
                        } else {
                            echo '<th class="px-2">' . $ValuedataFichadas['FicFechaAs'] . '</th>';
                            echo '<th class="px-2">' . $ValuedataFichadas['FicDia'] . '</th>';
                        }
                        if ($ValuedataFichadas['FicUltima'] == $ValuedataFichadas['FicPrimera']) {
                            $ValuedataFichadas['FicUltima'] = '';
                        }
                        ?>
                        <td class="px-2 center bg-light"><?= $ValuedataFichadas['FicPrimera'] ?></td>
                        <td class="px-2 center bg-light"><?= $ValuedataFichadas['FicUltima'] ?></td>
                        <td></td>
                        <td class="px-2 center"><?= $ValuedataFichadas['Fic_1'] ?></td>
                        <td class="px-2 center"><?= $ValuedataFichadas['Fic_2'] ?></td>
                        <td class="px-2 center"><?= $ValuedataFichadas['Fic_3'] ?></td>
                        <td class="px-2 center"><?= $ValuedataFichadas['Fic_4'] ?></td>
                        <td class="px-2 center"><?= $ValuedataFichadas['Fic_5'] ?></td>
                        <td class="px-2 center"><?= $ValuedataFichadas['Fic_6'] ?></td>
                        <td class="px-2 center"><?= $ValuedataFichadas['Fic_7'] ?></td>
                        <td class="px-2 center"><?= $ValuedataFichadas['Fic_8'] ?></td>
                        <td class="px-2 center"><?= $ValuedataFichadas['Fic_9'] ?></td>
                        <td class="px-2 center"><?= $ValuedataFichadas['Fic_10'] ?></td>
                        <td class="px-2 center"><?= $ValuedataFichadas['Fic_11'] ?></td>
                        <td class="px-2 center"><?= $ValuedataFichadas['Fic_12'] ?></td>
                        <td class="px-2 center"><?= $ValuedataFichadas['Fic_13'] ?></td>
                        <td class="px-2 center"><?= $ValuedataFichadas['Fic_14'] ?></td>
                        <td></td>
                        <td class="px-2 center bg-light"><?= $ValuedataFichadas['Fic_Cant'] ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
        <?php
        $countLega = count($dataAgrup);
        if ($_SaltoPag == '1') { /** Si se activa el salto de pagina por legajo */
            if ($valueAgrup != end($dataAgrup)) {
                // Este código se ejecutará para todos menos el último
                echo '<div class="mt-2" style="background:#333"></div>';
                echo '<div style="page-break-before: always; clear:both"></div>';
            }
        }
        // unset($dataFichadas);
    }/** FIN  matriz de legajos segun fecha Ini y Fin */
    unset($dataAgrup);
    sqlsrv_close($link);
    ?>
</body>