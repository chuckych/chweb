    <body class="fontq" backtop="5mm" backbottom="10mm">
        chunk
        <?php
        require __DIR__ . '../data.php';
        ?>
        <?php
        foreach ($dataLegajo as $key => $valueLegajo) {
        ?>
            <div style="page-break-inside: avoid">
                <hr>
                <!-- Encabezado -->
                <table>
                    <tr>
                        <th style="width:5%" class="bold">
                            Legajo:
                        </th>
                        <th style="width:45%">
                            <span class="bold">(<?= $valueLegajo['Legajo'] ?>) <?= $valueLegajo['Nombre'] ?></span>
                        </th>
                        <th style="width:50%" class="right">
                        </th>
                    </tr>
                    <tr>
                        <th style="width:5%" class="bold">
                            Cuil:
                        </th>
                        <th style="width:45%">
                            <?= $valueLegajo['Cuil'] ?>
                        </th>
                        <th style="width:50%" class="right">
                        </th>
                    </tr>
                </table>
                <!-- FIN Encabezado -->
                <hr>
                <table>
                    <tr>
                        <th class="px-2 bold">Fecha</th>
                        <th class="px-2 bold">Día</th>
                        <th class="px-2 bold">Horario</th>
                        <?php
                        if ($_VerFic == '1') {
                            /** Mostramos Las Fichadas E/S */
                        ?>
                            <th class="px-2 bold">Entra</th>
                            <th class="px-2 bold">Sale</th>
                        <?php
                        }
                        /** FIN Mostramos Las Fichadas E/S */
                        if ($_VerNove == '1') {
                            /** Mostramos Las Novedades */
                        ?>
                            <th class="px-2 bold">Novedad</th>
                            <th class="px-2 bold center"></th>
                            <?php
                        }
                        /** FIN Mostramos Las Novedades */
                        if ($_VerHoras == '1') {
                            /** Mostramos Las Horas */
                            require __DIR__ . '../dataThoColu.php';
                            foreach ($dataTHoDesc2 as $dataTHoDesc2) {
                            ?>
                                <th class="px-2 bold"><?php echo $dataTHoDesc2['THoDesc2']; ?></th>
                        <?php
                            }
                        }
                        /** FIN Mostramos Las Horas */
                        ?>
                    </tr>
                    <?php
                    require __DIR__ . '../data2.php';
                    foreach ($dataRegistros as $dataRegistros) {
                    ?>
                        <tr>
                            <td class="px-2 vtop"><?php echo $dataRegistros['Fecha']; ?></td>
                            <td class="px-2 vtop"><?php echo $dataRegistros['Dia']; ?></td>
                            <td class="px-2 vtop"><?php echo $dataRegistros['Gen_Horario']; ?></td>
                            <?php
                            if ($_VerFic == '1') {
                                /** Mostramos Las Fichadas E/S */
                            ?>
                                <td class="px-2 vtop"><?php echo $dataRegistros['Primera']; ?></td>
                                <td class="px-2 vtop"><?php echo $dataRegistros['Ultima']; ?></td>
                            <?php
                            }
                            /** FIN Mostramos Las Fichadas E/S */
                            if ($_VerNove == '1') {
                                /** Mostramos Las Novedades */
                            ?>
                                <td class="px-2 vtop"><?php echo $dataRegistros['Novedades']; ?></td>
                                <td class="px-2 vtop center ls1"><?php echo $dataRegistros['NovHor']; ?></td>
                            <?php
                            }
                            /** FIN Mostramos Las Horas */
                            if ($_VerHoras == '1') {
                                /** Mostramos Las Horas */
                                echo $dataRegistros['HsAuto'];
                            }
                            ?>
                        </tr>

                        <?php
                    }
                    unset($dataRegistros);
                    if ($_VerHoras == '1') {
                        /** Mostramos Las Horas */
                        if ($_TotHoras == '1') {
                            /** Mostramos el total de horas por columna */
                        ?>
                            <tr>
                                <?php
                                if ($_VerNove == '0' && $_VerFic == '0') {
                                    echo '<td class="px-2 vtop right" colspan="3"></td>';
                                } else if ($_VerNove == '0' && $_VerFic == '1') {
                                    echo '<td class="px-2 vtop right" colspan="5"></td>';
                                } else if ($_VerNove == '1' && $_VerFic == '0') {
                                    echo '<td class="px-2 vtop right" colspan="5"></td>';
                                } else {
                                    echo '<td class="px-2 vtop right" colspan="7"></td>';
                                }
                                ?>
                                <?php
                                require __DIR__ . '../dataTotHorLeg.php';
                                foreach ($dataTotHorLeg as $dataTotHorLeg) {
                                ?>
                                    <td class="px-2 vtop center bold" style="font-size:11px;"><?= $dataTotHorLeg['HsAutorizadas'] ?></td>
                                <?php
                                }
                                ?>
                            </tr>
                        <?php  }
                        /** FIN Mostramos el total de horas por columna */ ?>
                    <?php  }
                    /** FIN Mostramos Las Horas */  ?>
                </table>
                <!-- <hr class="border-top"> -->
                <?php
                if ($_TotNove == '1') {
                    /** Mostramos el total resumen de Novedades por columna */
                ?>
                    <div class="py-1"><b>Resumen de Novedades:</b> <span class="">(<?= $valueLegajo['Legajo'] ?>) <?= $valueLegajo['Nombre'] ?></span></div>
                    <table>
                        <!-- <tr>
                    <th class="px-2 bold">Cod.</th>
                    <th class="px-2 bold">Novedad</th>
                    <th class="px-2 bold">Tipo</th>
                    <th class="px-2 bold">Horas</th>
                    <th class="px-2 bold">Dias</th>
                </tr> -->
                        <?php
                        require __DIR__ . '../dataTotNovLeg.php';
                        foreach ($dataTotNovLeg as $dataTotNovLeg) {
                        ?>
                            <tr>
                                <td class="px-2"><?= $dataTotNovLeg['Cod'] ?></td>
                                <td class="px-2"><?= $dataTotNovLeg['Novedad'] ?></td>
                                <td class="px-2"><?= $dataTotNovLeg['Tipo'] ?></td>
                                <td class="px-2 center"><?= $dataTotNovLeg['Horas'] ?></td>
                                <td class="px-2 center"><?= $dataTotNovLeg['Dias'] ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                    <br />
                <?php
                }
                ?>
            </div>
            <?php
            if ($_SaltoPag == '1') {
                /** Si se activa el salto de pagina por legajo */
                if ($valueLegajo != end($dataLegajo)) {
                    // Este código se ejecutará para todos menos el último
                    echo '<div style="page-break-before: always; clear:both"></div>';
                }
            }
            ?>
        <?php
        }
        unset($dataLegajo);
        ?>
    </body>