    <body>
    chunk
        <?php
        require __DIR__ . '../data.php';
        ?>

        <div style="page-break-inside: avoid">
            <!-- <hr class="border-top">   -->
            <hr>  
            <!-- Encabezado -->
            <?php
            // $count = count($dataParte);
            $colorRes='style="background:#ffff8d"';
            ?>
            <table>
                <tr>
                    <th class="bold px-2">Legajo</th>
                    <th class="bold px-2">Nombre</th>
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
                        $_res_tipo='0';
                        break;
                    case 'r_inc':
                        $_res_tipo='1';
                        break;
                    case 'r_sal':
                        $_res_tipo='2';
                        break;
                    default:
                        $_res_tipo='99';
                        break;
                    
                    }
                    $padding='';
                foreach ($dataParte as $key => $ValueDataParte) {
                    if ($ValueDataParte['Legajo'] === ($dataParte[$key-1]['Legajo'])) {
                        $ValueDataParte['Nombre']='-';
                        $ValueDataParte['Legajo']='-';
                        $ValueDataParte['Horario']='-';
                        $padding='style="padding-bottom:7px;"'; 
                    }else{
                        $padding=''; 
                    }
                    // $style=($ValueDataParte['TipoN']==$_resaltar)? $colorRes:'';
                    if($_resaltar=='r_aus'){
                        switch ($ValueDataParte['TipoN']){
                            case '3':
                            case '4':
                            case '5':
                            case '6':
                            case '7':
                            case '8':
                                $style=$colorRes;
                                break;                            
                            default:
                                $style='';
                                break;
                        }
                    }else{
                         $style=($ValueDataParte['TipoN']== "$_res_tipo" ) ? $colorRes:'';                         
                    }
                    if ($ValueDataParte['Novedad']) {
                ?>
                    <tr <?=$style?> >
                        <td class="px-2"><?= $ValueDataParte['Legajo'] ?></td>
                        <td class="px-2"><?= $ValueDataParte['Nombre'] ?></td>
                        <td class="px-2"><?= $ValueDataParte['Horario'] ?></td>
                        <td class="px-2"><?= $ValueDataParte['Codigo'] ?></td>
                        <td class="px-2" <?=$padding?>><?= $ValueDataParte['Novedad'] ?></td>
                        <!-- <td class="px-2"><?= $ValueDataParte['Tipo'] ?></td> -->
                        <td class="px-2"><?= $ValueDataParte['Horas'] ?></td>
                        <td class="px-2"><?= $ValueDataParte['Causa'] ?></td>
                        <td class="px-2"><?= $ValueDataParte['Observacion'] ?></td>
                    </tr>
                <?php
                    }
                }
                // unset($dataParte);
                if ($_PerSN) {
                ?>
                <tr><td colspan="8" class="bold px-2 py-1">Personal sin novedades:</td></tr>
                 <?php 
                foreach ($dataParte as $key => $ValueDataParte) {
                if (!$ValueDataParte['Novedad']) {
                ?>
                <tr>
                    <td class="px-2"><?= $ValueDataParte['Legajo'] ?></td>
                    <td class="px-2"><?= $ValueDataParte['Nombre'] ?></td>
                    <td class="px-2"><?= $ValueDataParte['Horario'] ?></td>
                    <td class="px-2"><?= $ValueDataParte['Codigo'] ?></td>
                    <td class="px-2" <?=$padding?>><?= $ValueDataParte['Novedad'] ?></td>
                    <!-- <td class="px-2"><?= $ValueDataParte['Tipo'] ?></td> -->
                    <td class="px-2"><?= $ValueDataParte['Horas'] ?></td>
                    <td class="px-2"><?= $ValueDataParte['Causa'] ?></td>
                    <td class="px-2"><?= $ValueDataParte['Observacion'] ?></td>
                </tr>
                <?php
                        }
                    }
                }
                unset($dataParte);
                ?>
                                
            </table>
            <!-- FIN Encabezado -->
            <hr>
                <table>
                    <tr>
                        <th class="bold px-2 py-1"><b>Resumen:</b></th>
                        <th class="bold px-2 center">Horas</th>
                        <th class="bold px-2 center">Cant.</th>
                    </tr>
                    <?php
                    if($tc['Llegada tarde']){
                        $style=($_resaltar== 'r_tar' ) ? $colorRes:'';
                    ?>
                    <tr <?=$style?>>
                        <td class="px-2">Llegadas Tarde</td>
                        <td class="px-2 center"><?= MinHora(array_sum($t)) ?></td>
                        <td class="px-2 center"><?= $tc['Llegada tarde'] ?></td>
                    </tr>
                    <?php
                    unset($t);
                    }
                    if($sc['Salida anticipada']){
                        $style=($_resaltar== 'r_sal' ) ? $colorRes:'';
                    ?>
                    <tr <?=$style?>>
                        <td class="px-2">Salidas Anticipadas</td>
                        <td class="px-2 center"><?= MinHora(array_sum($s)) ?></td>
                        <td class="px-2 center"><?= $sc['Salida anticipada'] ?></td>
                    </tr>
                    <?php
                    unset($s);
                    }
                    if($ic['Incumplimiento']){
                        $style=($_resaltar== 'r_inc' ) ? $colorRes:'';
                    ?>
                    <tr <?=$style?>>
                        <td class="px-2">Incumplimientos</td>
                        <td class="px-2 center"><?= MinHora(array_sum($i)) ?></td>
                        <td class="px-2 center"><?= $ic['Incumplimiento'] ?></td>
                    </tr>
                    <?php
                    unset($i);
                    }
                    if($ac['Ausencia']){
                        $style=($_resaltar== 'r_aus' ) ? $colorRes:'';
                    ?>
                    <tr <?=$style?>>
                        <td class="px-2">Ausencias</td>
                        <td class="px-2 center"><?= MinHora(array_sum($a)) ?></td>
                        <td class="px-2 center"><?= $ac['Ausencia'] ?></td>
                    </tr>
                    <?php
                    unset($a);
                    }
                    if($lc['Licencia']){
                        $style=($_resaltar== 'r_aus' ) ? $colorRes:'';
                    ?>
                    <tr <?=$style?>>
                        <td class="px-2">Licencias</td>
                        <td class="px-2 center"><?= MinHora(array_sum($l)) ?></td>
                        <td class="px-2 center"><?= $lc['Licencia'] ?></td>
                    </tr>
                    <?php
                    unset($l);
                    }
                    if($acc['Accidente']){
                        $style=($_resaltar== 'r_aus' ) ? $colorRes:'';
                    ?>
                    <tr <?=$style?>>
                        <td class="px-2">Accidentes</td>
                        <td class="px-2 center"><?= MinHora(array_sum($ac1)) ?></td>
                        <td class="px-2 center"><?= $acc['Accidente'] ?></td>
                    </tr>
                    <?php
                    unset($ac1);
                    }
                    if($vc['Vacaciones']){
                        $style=($_resaltar== 'r_aus' ) ? $colorRes:'';
                    ?>
                    <tr <?=$style?>>
                        <td class="px-2">Vacaciones</td>
                        <td class="px-2 center"><?= MinHora(array_sum($v)) ?></td>
                        <td class="px-2 center"><?= $vc['Vacaciones'] ?></td>
                    </tr>
                    <?php
                    unset($v);
                    }
                    if($suc['Suspensión']){
                        $style=($_resaltar== 'r_aus' ) ? $colorRes:'';
                    ?>
                    <tr <?=$style?>>
                        <td class="px-2">Suspensiones</td>
                        <td class="px-2 center"><?= MinHora(array_sum($sus)) ?></td>
                        <td class="px-2 center"><?= $suc['Suspensión'] ?></td>
                    </tr>
                    <?php
                    unset($sus);
                    }
                    if($arc['ART']){
                        $style=($_resaltar== 'r_aus' ) ? $colorRes:'';
                    ?>
                    <tr <?=$style?>>
                        <td class="px-2">ART</td>
                        <td class="px-2 center"><?= MinHora(array_sum($art)) ?></td>
                        <td class="px-2 center"><?= $arc['ART'] ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <td class="px-2 pt-1">Total Novedades</td>
                        <td class="px-2 center"></td>
                        <td class="px-2 center"><?= $CountNov ?></td>
                    </tr>
                    <?php
                    if ($_PerSN) {
                    ?>
                    <tr>
                        <td class="px-2">Personal s/novedades</td>
                        <td class="px-2 center"></td>
                        <td class="px-2 center"><?= $CountSNov ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </table>
                <?php
                    unset($dataParte);
                ?>
            </div>
        

    </body>