 <!--  AGREGAR  -->
 <div class="row bg-white pb-3 mb-2">
     <?php
        $url = host() . "/" . HOMEHOST . "/data/" . $getjson . "?tk=" . token() . "&_c=" . $_GET['_c'] . "&_r=" . $_GET['_r'] . "&act" . "&e=" . $_GET['e'];
        // echo $url;
        // $json  = file_get_contents($url);
        // $array = json_decode($json, TRUE);
        $array = json_decode(getRemoteFile($url), true);
        if (is_array($array)) :
            if (!$array[0]['error']) {
                $rowcount = (count($array[0][$arrayjson]));
            }
        endif;
        $data = $array[0][$arrayjson];
        if (is_array($data)) { ?>
         <form name="e2" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?<?= $_SERVER['QUERY_STRING'] ?>" method="POST" class="w-100" onsubmit="ShowLoading()">
             <script>
                 function Sel_todoe2() {
                     for (i = 0; i < document.e2.elements.length; i++)
                         if (document.e2.elements[i].type == "checkbox")
                             document.e2.elements[i].checked = 1
                 }

                 function deSel_todoe2() {
                     for (i = 0; i < document.e2.elements.length; i++)
                         if (document.e2.elements[i].type == "checkbox")
                             document.e2.elements[i].checked = 0
                 }
             </script>
             <div class="col-12">
                 <table class="table text-nowrap table-borderless w-auto" id="table-b">
                     <thead class="">
                         <tr>
                             <th colspan="2"></th>
                             <th colspan="3" class="text-center"><span class="">PERSONAL</span></th>
                         </tr>
                         <tr>
                             <th class="">#</th>
                             <th class="">Descripción</th>
                             <th class="text-center bg-light">Act</th>
                             <th class="text-center">Baja</th>
                             <th class="text-center">Total</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php
                            foreach ($data as $value) :
                                $id           = $value['cod'];
                                $nombre       = $value['desc'];
                                $legajos_act  = $value['cant_legajos_act'];
                                $legajos_baja = $value['cant_legajos_baja'];
                                $legajos      = $value['cant_legajos'];
                                $sectores[] = array('sector' => $nombre, 'idsect' => $id);
                                $legajos_act_arr_b[]  = $value['cant_legajos_act'];
                                $legajos_baja_arr_b[] = $value['cant_legajos_baja'];
                                $legajos_arr_b[]      = $value['cant_legajos'];
                            ?>
                             <tr>
                                 <td><span class="ls1"><?= $id ?></span></td>
                                 <td>
                                     <div class="custom-control custom-switch">
                                         <input type="checkbox" name="est[]" class="custom-control-input" id="<?= $id ?>" value="<?= $id ?>">
                                         <label class="custom-control-label fw4" for="<?= $id ?>">
                                             <p class="mb-0 text-secondary fontq" style="margin-top: 3px"><?= $nombre ?></p>
                                         </label>
                                     </div>
                                 </td>
                                 <td class="text-center bg-light"><span class="ls1"><?= $legajos_act ?></span></td>
                                 <td class="text-center"><span class="ls1"><?= $legajos_baja ?></span></td>
                                 <td class="text-center"><span class="ls1"><?= $legajos ?></span></td>
                             </tr>
                         <?php endforeach;
                            unset($data) ?>
                     </tbody>
                     <tfoot class="font1">
                         <tr>
                             <td class="fw4"></td>
                             <td class="fw4 text-right">Totales:</td>
                             <td class="fw4 text-center bg-light"><?= array_sum($legajos_act_arr_b) ?></td>
                             <td class="fw4 text-center"><?= array_sum($legajos_baja_arr_b) ?></td>
                             <td class="fw4 text-center"><?= array_sum($legajos_arr_b) ?></td>
                         </tr>
                     </tfoot>
                 </table>
             </div>
             <div class="col-12 py-3">
                 <a class="p-0 fontq btn-link text-secondary" href="javascript:Sel_todoe2()">Marcar</a> |
                 <a class="p-0 fontq btn-link text-secondary" href="javascript:deSel_todoe2()">Desmarcar</a>
             </div>
             <div class="col-12">
                 <!-- <button type="submit" name="submit" id="" class="btn btn-sm btn-danger fontp px-3" value="<?= $submitb ?>">ELIMINAR</button> -->

                 <button type="submit" name="submit" id="" class="d-none d-sm-block btn btn-sm btn-danger fontp px-3" value="<?= $submitb ?>">ELIMINAR</button>
                 <button type="submit" name="submit" id="" class="d-block d-sm-none h50 btn-block btn btn-danger fontp" value="<?= $submitb ?>">ELIMINAR</button>
             </div>
         </form>
     <?php } else { ?>
         <div class="col-12 my-2">
             <p class="m-0 fontq alert alert-success"><?= $e_mensaje2 ?></p>
         </div>
     <?php } ?>
 </div>
 <!-- 
                FIN DE AGREGAR 
            -->
 <!--  AGREGAR  SECCIONES -->
 <?php 
        /** */
    if ($_GET['e'] == 'sectores') {
        /** si el get "e" es igual a sectores mostramos secciones */

    ?>
     <div class="row bg-white">
         <div class="col-12 p-3 bg-light mb-2 w-100">Secciones</div>
     </div>
     <div class="row pt-3 bg-white">
         <?php
            $r = array_filter($sectores, function ($e) {
                return $e['idsect'] != '0';
            });
            foreach ($r as $value) : ?>
             <div class="col-12 pb-0">
                 <p class="mb-0 w-100 fontq fw4 alert alert-secondary"><?= $value['idsect'] ?> - <?= $value['sector'] ?></p>
             </div>
             <div class="col-12 mt-3">
                 <ul class="nav nav-pills" id="pills-tab" role="tablist">
                     <li class="nav-item">
                         <a class="active border fontq btn btn-sm w120 btn-outline-secondary" id="pills-addseccciones_<?= $value['idsect'] ?>-tab" data-toggle="pill" href="#pills-addseccciones_<?= $value['idsect'] ?>" role="tab" aria-controls="pills-addseccciones_<?= $value['idsect'] ?>" aria-selected="true"><small></i>DISPONIBLES</small></a>
                     </li>
                     <?php //if(estructura_rol($GetRol, $_GET['_r'], 'secciones', 'seccion' )) { 
                          $url   = host() . "/" . HOMEHOST . "/data/GetEstructRol.php?tk=" . token() . "&_r=" .  $_GET['_r']. "&e=secciones&sector=".$value['idsect'];
                        //   echo $url; br();
                        //   $json  = file_get_contents($url);
                        //   $array = json_decode($json, TRUE);
                          $array = json_decode(getRemoteFile($url), true);
                          $val_roles = (!$array[0]['error']) ? implode(",", $array[0]['seccion']) : '';
                          $count_roles = (!$array[0]['error']) ? count($array[0]['seccion']) : '';
                          $rol = (!$array[0]['error']) ? "$val_roles" : "";
                          $count_roles = (!$array[0]['error']) ? "$count_roles" : "";
                          if($rol){
                         ?>
                     <li class="nav-item ml-1">
                         <a class="border fontq btn btn-sm w120 btn-outline-success" id="pills-deletesecciones_<?= $value['idsect'] ?>-tab" data-toggle="pill" href="#pills-deletesecciones_<?= $value['idsect'] ?>" role="tab" aria-controls="pills-deletesecciones_<?= $value['idsect'] ?>" aria-selected="false"><small></i>ACTIVOS <span class="ls1 fw5">(<?=$count_roles?>)</span></small></a>
                     </li>
                     <?php } ?>
                 </ul>
             </div>
             <div class="col-12 mb-3">
                 <div class="tab-content" id="pills-tabContent">
                     <div class="tab-pane fade show active" id="pills-addseccciones_<?= $value['idsect'] ?>" role="tabpanel" aria-labelledby="pills-addseccciones_<?= $value['idsect'] ?>-tab">
                         <?php require __DIR__ . '/alta_seccion.php'; ?>
                     </div>
                     <?php if(estructura_rol($GetRol, $_GET['_r'], 'secciones', 'seccion' )) { ?>
                     <div class="tab-pane fade" id="pills-deletesecciones_<?= $value['idsect'] ?>" role="tabpanel" aria-labelledby="pills-deletesecciones_<?= $value['idsect'] ?>-tab">
                         <?php require __DIR__ . '/baja_seccion.php'; ?>
                     </div>
                     <?php } ?>
                 </div>
             </div>

         <?php endforeach;
            ?>
     </div>
     <!--  FIN DE AGREGAR 
            -->
 <?php }
    /** Fin si el get "e" es igual a sectores */ ?>