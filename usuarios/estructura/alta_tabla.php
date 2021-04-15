 <!--  AGREGAR  -->
 <div class="row bg-white pb-3">
     <?php
        $url = host() . "/" . HOMEHOST . "/data/" . $getjson . "?tk=" . token() . "&_c=" . $_GET['_c'] . "&_r=" . $_GET['_r'] . "&e=" . $_GET['e'];
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
         <form name="e1" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?<?= $_SERVER['QUERY_STRING'] ?>" method="POST" class="w-100" onsubmit="ShowLoading()">
             <script>
                 function Sel_todo_e1() {
                     for (i = 0; i < document.e1.elements.length; i++)
                         if (document.e1.elements[i].type == "checkbox")
                             document.e1.elements[i].checked = 1
                 }

                 function deSel_todo_e1() {
                     for (i = 0; i < document.e1.elements.length; i++)
                         if (document.e1.elements[i].type == "checkbox")
                             document.e1.elements[i].checked = 0
                 }
             </script>
             <div class="col-12">
                 <table class="table text-nowrap table-borderless w-auto" id="table-a">
                     <thead class="">
                         <tr>
                             <th colspan="2"></th>
                             <th colspan="3" class="text-center"><span class="ls1">PERSONAL</span></th>
                         </tr>
                         <tr>
                             <th class="">#</th>
                             <th class="">Descripción</th>
                             <th class=" bg-light text-center">Act</th>
                             <th class="text-center">Baja</th>
                             <th class="text-center">Total</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php
                            foreach ($data as $value) :
                                $id                 = $value['cod'];
                                $nombre             = $value['desc'];
                                $legajos_act        = $value['cant_legajos_act'];
                                $legajos_baja       = $value['cant_legajos_baja'];
                                $legajos            = $value['cant_legajos'];
                                $legajos_act_arr[]  = $value['cant_legajos_act'];
                                $legajos_baja_arr[] = $value['cant_legajos_baja'];
                                $legajos_arr[]      = $value['cant_legajos'];
                                // $sectores[] = array('sector' => $nombre, 'idsect' => $id);
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
                                 <td class="bg-light text-center"><span class="ls1"><?= ($legajos_act) ?></span></td>
                                 <td class="text-center"><span class="ls1"><?= $legajos_baja ?></span></td>
                                 <td class="text-center"><span class="ls1"><?= $legajos ?></span></td>
                             </tr>
                         <?php endforeach; ?>
                     </tbody>
                     <tfoot class="font1">
                         <tr>
                             <td class="fw4"></td>
                             <td class="fw4 text-right">Totales:</td>
                             <td class="fw4 bg-light text-center"><?=array_sum($legajos_act_arr)?></td>
                             <td class="fw4 text-center"><?=array_sum($legajos_baja_arr)?></td>
                             <td class="fw4 text-center"><?=array_sum($legajos_arr)?></td>
                         </tr>
                     </tfoot>
                 </table>
             </div>
             <div class="col-12 py-3">
                 <a class="p-0 fontq btn-link text-secondary" href="javascript:Sel_todo_e1()">Marcar</a> |
                 <a class="p-0 fontq btn-link text-secondary" href="javascript:deSel_todo_e1()">Desmarcar</a>
             </div>
             <div class="col-12">
                 <button type="submit" name="submit" id="" class="d-none d-sm-block btn btn-sm btn-success fontp px-3" value="<?= $submit ?>">AGREGAR</button>
                 <button type="submit" name="submit" id="" class="d-block d-sm-none h50 btn-block btn btn-success fontp" value="<?= $submit ?>">AGREGAR</button>
             </div>
         </form>
     <?php } else { ?>
         <div class="col-12 my-2">
             <p class="m-0 fontq alert alert-success"><?= $e_mensaje ?></p>
         </div>
     <?php } ?>
 </div>
 <!-- 
                FIN DE AGREGAR 
            -->