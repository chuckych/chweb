 <!--  AGREGAR  -->
 <div class="row">
     <?php
        $url = host() . "/" . HOMEHOST . "/data/GetEstructuraSeccion.php?tk=" . token() . "&_c=" . $_GET['_c'] . "&_r=" . $_GET['_r'] . "&act&e=secciones&sector=" . $value['idsect'];
        // echo $url;
        $json         = file_get_contents($url);
        $array        = json_decode($json, TRUE);
        if (is_array($array)) :
            if (!$array['error']) {
                $rowcount = (count($array['secciones']));
            }
        endif;
        $data = $array['secciones'];
        if (is_array($data)) { ?>
         <div class="col-12 table-responsive">
             <form name="" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?<?= $_SERVER['QUERY_STRING'] ?>" method="POST" class="w-100" onsubmit="ShowLoading()">
                 <input type="hidden" name="seccion" value="1">
                 <input type="hidden" name="sector" value="<?= $value['idsect'] ?>">
                 <table class="table text-nowrap table-borderless w-auto" id="table-secc-<?= $value['idsect'] ?>_b">
                     <thead class="">
                         <tr>
                             <th>#</th>
                             <th>Descripción</th>
                             <th class="bg-light">Act</th>
                             <th>Baja</th>
                             <th>Total</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php
                            foreach ($data as $value_c) :
                                $id                = $value_c['cod'];
                                $nombre            = $value_c['desc'];
                                $cant_legajos_act  = $value_c['cant_legajos_act'];
                                $cant_legajos_baja = $value_c['cant_legajos_baja'];
                                $cant_legajos      = $value_c['cant_legajos'];
                                $cod_sector        = $value_c['cod_sector'];
                                $legajos_act_arr_c[]  = $value_c['cant_legajos_act'];
                                $legajos_baja_arr_c[] = $value_c['cant_legajos_baja'];
                                $legajos_arr_c[]      = $value_c['cant_legajos'];
                            ?>
                             <tr>
                                 <td><span class="ls1"><?= $id ?></span></td>
                                 <td>
                                     <div class="custom-control custom-switch">
                                         <input type="checkbox" name="est[]" class="custom-control-input" id="secc_<?= $id ?>_<?= $cod_sector ?>" value="<?= $id ?>">
                                         <label class="custom-control-label fw4" for="secc_<?= $id ?>_<?= $cod_sector ?>">
                                             <p class="mb-0 text-secondary fontq" style="margin-top: 3px"><?= $nombre ?></p>
                                         </label>
                                     </div>
                                 </td>
                                 <td class="bg-light text-center"><span class="ls1"><?= $cant_legajos_act ?></span></td>
                                 <td class="text-center"><span class="ls1"><?= $cant_legajos_baja ?></span></td>
                                 <td class="text-center"><span class="ls1"><?= $cant_legajos ?></span></td>
                             </tr>
                         <?php endforeach; ?>
                     </tbody>
                     <tr>
                         <td class="fw4"></td>
                         <td class="fw4 text-right">Totales:</td>
                         <td class="fw4 bg-light text-center"><?= array_sum($legajos_act_arr_c) ?></td>
                         <td class="fw4 text-center"><?= array_sum($legajos_baja_arr_c) ?></td>
                         <td class="fw4 text-center"><?= array_sum($legajos_arr_c) ?></td>
                     </tr>
                     <?php
                        unset($legajos_act_arr_c);
                        unset($legajos_baja_arr_c);
                        unset($legajos_arr_c);
                        ?>
                 </table>
                 <div class="my-4">
                     <!-- <button type="submit" name="submit" id="" class="btn btn-sm btn-danger fontp px-3" value="<?= $submitb ?>">ELIMINAR</button> -->
                     
                 <button type="submit" name="submit" id="" class="d-none d-sm-block btn btn-sm btn-danger fontp px-3" value="<?= $submitb ?>">ELIMINAR</button>
                 <button type="submit" name="submit" id="" class="d-block d-sm-none h50 btn-block btn btn-danger fontp" value="<?= $submitb ?>">ELIMINAR</button>
                 </div>
             </form>
         </div>
     <?php } else { ?>
         <div class="col-12 my-2">
             <p class="m-0 fontq alert alert-info">No hay secciones activas</p>
         </div>
     <?php } ?>
 </div>
 <!-- FIN DE AGREGAR 
            -->