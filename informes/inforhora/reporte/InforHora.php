    <body class="fontq" backtop="5mm" backbottom="10mm">
    chunk       
    <?php
        require __DIR__ . '../data.php';
        ?>
        <?php
        foreach ($dataAgrup as $key => $valueAgrup) {
        ?>
        <div style="page-break-inside: avoid">
            <hr>
            <!-- Encabezado -->
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
                        echo '<p class="bold">' . $LabelVal . '</p>';
                        ?>
                        <!-- <p class="bold">(<?= $valueAgrup['Legajo'] ?>) <?= $valueAgrup['Nombre'] ?></p> -->
                    </th>
                    <th style="width:50%" class="right">
                    </th>
                </tr>
            </table>
            <!-- FIN Encabezado -->
            <hr>
            <table>
                <tr>
                    <th colspan="4"></th>
                <?php            
                if($_VerHoras=='1'){ /** Mostramos Las Horas */                    
                    require __DIR__ . '../dataThoColu.php';
                    foreach ($dataTHoDesc2 as $dataTHoDesc2) {
                    ?>
                        <th class="px-2 bold" colspan="2"><?php echo $dataTHoDesc2['THoDesc2']; ?></th>
                    <?php
                    }
                    // unset($dataTHoDesc2);
                }/** FIN Mostramos Las Horas */ 
                ?>
                </tr>
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
                    <!-- <th class="px-2 bold h20">Fecha</th>
                    <th class="px-2 bold">Día</th> -->
                    <th class="px-2 bold">Horario</th>
                    <th class="px-2 bold bg-light">Trab.</th>
                    <?php
                        if($_VerFic=='1'){ /** Mostramos Las Fichadas E/S */
                    ?>
                    <th class="px-2 bold">Entra</th>
                    <th class="px-2 bold">Sale</th>
                    <?php
                    } /** FIN Mostramos Las Fichadas E/S */
                    if($_VerNove=='1'){ /** Mostramos Las Novedades */  
                    ?>
                    <th class="px-2 bold">Novedad</th>
                    <th class="px-2 bold center"></th>
                    <?php       
                    } /** FIN Mostramos Las Novedades */       
                    if($_VerHoras=='1'){
                        require __DIR__ . '../dataThoColu.php';               
                        foreach ($dataTHoDesc2 as $dataTHoDesc2) {
                        ?>
                            <th class="px-2 bold">Hechas</th>
                            <th class="px-2 bold bg-light">Pagas</th>
                        <?php
                        }
                        unset($dataTHoDesc2);
                    }
                    ?>
                </tr>
                <?php
                require __DIR__ . '../data2.php';
                foreach ($dataRegistros as $dataRegistros) {
                ?>
                    <tr>
                    <?php
                        if ($_Por == 'Fech') {
                            echo '<th class="px-2 vtop">' . $dataRegistros['Gen_Lega'] . '</th>';
                            echo '<th class="px-2 vtop">' . $dataRegistros['Gen_Nombre'] . '</th>';
                        } else {
                            echo '<th class="px-2 vtop">' . $dataRegistros['Fecha'] . '</th>';
                            echo '<th class="px-2 vtop">' . $dataRegistros['Dia'] . '</th>';
                        }
                        ?>
                        <!-- <td class="px-2 vtop"><?php echo $dataRegistros['Fecha']; ?></td>
                        <td class="px-2 vtop"><?php echo $dataRegistros['Dia']; ?></td> -->
                        <td class="px-2 vtop"><?php echo $dataRegistros['Gen_Horario']; ?></td>
                        <td class="px-2 vtop bg-light ls1"><?php echo $dataRegistros['Trabajadas']; ?></td>
                        <?php
                            if($_VerFic=='1'){ /** Mostramos Las Fichadas E/S */
                        ?>
                        <td class="px-2 vtop"><?php echo $dataRegistros['Primera']; ?></td>
                        <td class="px-2 vtop"><?php echo $dataRegistros['Ultima']; ?></td>
                        <?php
                            } /** FIN Mostramos Las Fichadas E/S */
                            if($_VerNove=='1'){ /** Mostramos Las Novedades */  
                        ?>
                        <td class="px-2 vtop"><?php echo $dataRegistros['Novedades']; ?></td>
                        <td class="px-2 vtop center ls1"><?php echo $dataRegistros['NovHor']; ?></td>
                        <?php 
                            }/** FIN Mostramos Las Horas */
                        if($_VerHoras=='1'){ /** Mostramos Las Horas */
                        echo $dataRegistros['Hechas'];
                        // echo $dataRegistros['HsAuto'];
                        }
                        ?>
                    </tr>
                    
                <?php
                }
                unset($dataRegistros);
                if($_VerHoras=='1'){ /** Mostramos Las Horas */
                    if($_TotHoras=='1'){ /** Mostramos el total de horas por columna */
                ?>
                    <tr>
                        <?php
                        if($_VerNove=='0' && $_VerFic =='0'){
                            echo '<td class="px-2 vtop right" colspan="4"></td>';
                        }else if($_VerNove=='0' && $_VerFic =='1'){
                            echo '<td class="px-2 vtop right" colspan="5"></td>';
                        }else if($_VerNove=='1' && $_VerFic =='0'){
                            echo '<td class="px-2 vtop right" colspan="5"></td>';
                        }else{
                            echo '<td class="px-2 vtop right" colspan="7"></td>';
                        }
                        ?>
                <?php
                    require __DIR__ . '../dataTotHorLeg.php';
                    foreach ($dataTotHorLeg as $dataTotHorLeg) {
                    ?>
                    
                    <td class="px-2 vtop center bold ls1" style="font-size:11px;"><?=ceronull($dataTotHorLeg['HsHechas'])?></td>
                    <td class="px-2 vtop center bold bg-light ls1" style="font-size:11px;"><?=ceronull($dataTotHorLeg['HsAutorizadas'])?></td>
                    <?php
                    }  
                    ?>
                    </tr>
                    <?php  } /** FIN Mostramos el total de horas por columna */ ?>
                <?php  } /** FIN Mostramos Las Horas */  ?>
            </table>
            <!-- <hr class="border-top"> -->
            <?php
            if($_TotNove=='1'){ /** Mostramos el total resumen de Novedades por columna */
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
                    <td class="px-2"><?=$dataTotNovLeg['Cod']?></td>
                    <td class="px-2"><?=$dataTotNovLeg['Novedad']?></td>
                    <td class="px-2"><?=$dataTotNovLeg['Tipo']?></td>
                    <td class="px-2 center"><?=$dataTotNovLeg['Horas']?></td>
                    <td class="px-2 center"><?=$dataTotNovLeg['Dias']?></td>
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
            if($_SaltoPag=='1'){ /** Si se activa el salto de pagina por legajo */
                if ($valueAgrup != end($valueAgrup)) {
                    // Este código se ejecutará para todos menos el último
                    echo '<div style="page-break-before: always; clear:both"></div>';
                  }
            }
            ?>
        <?php
        }
        unset($dataAgrup);
        ?>
    </body>