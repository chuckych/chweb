<?php

if (!isset($_GET['_dr']) or (empty($_GET['_dr']))) {
    $FechaIni = date("Y-m-d", strtotime(hoy() . "- 0 days"));
    $FechaFin = date("Y-m-d", strtotime(hoy() . "- 0 days"));
} else {
    $DateRange = explode(' al ', $_GET['_dr']);
    $FechaIni  = test_input(dr_fecha($DateRange[0]));
    $FechaFin  = test_input(dr_fecha($DateRange[1]));
}
// or ($_datos == 'novedades') or ($_datos == 'fichadas')
$_range = (($_GET['_range'] ?? '')==='on') ? true : false;
$OcultarCount = ($_range) ? 'd-none': '';
if (($_datos == 'general')) {
    $_range = (($_GET['_range'] ?? '')==='on') ? true : false;
    $col='col-sm-6';
    // $OcultarCount = ($_range) ? 'd-none': 'd-none d-sm-block';
    $OcultarCount = ($_range) ? 'd-none': '';
    $OcultarCount2 = ($_range) ? 'd-none': 'd-block d-sm-none';
?>
<div class="col-12 col-sm-6 mb-2 mb-sm-0">
    <button type="button" class="mt-2 <?=$OcultarCount?> float-left btn text-white btn-sm mr-1 opa9 fontq w100 <?= $bgcolor ?>" data-toggle="modal" data-target="#Total_pdia" id="Total_dia">
        Total Día
    </button>
    <button type="button" class="mt-2 d-none d-sm-block btn text-white btn-sm opa9 fontq w100 <?= $bgcolor ?>" data-toggle="modal" data-target="#Total_General">
        Totales
    </button>
</div>
<?php }else{$col='col-sm-12';} ?>
<div class="col-12 mb-2 mb-sm-0 <?=$col?>">
    <div class="d-flex justify-content-sm-end justify-content-center">
        <input type="text" readonly class="mx-2 form-control text-center w250 mt-2 mt-sm-0 ls2 h40" name="_dr" id="_dr" placeholder="<?= fechformat($FechaIni) . ' - ' . fechformat($FechaFin) ?>">
        <button type="submit" id="btnFiltrar" class="d-none d-sm-block btn h40 btn-info px-4 border-0 fontq float-right mt-2 mt-sm-0 <?= $bgcolor ?>">Filtrar</button>
    </div>
</div>
<?php
if (($_datos != 'cta_horas') && ($_datos != 'mobile')) {
    $url   = host() . "/" . HOMEHOST . "/data/$getData.php?tk=" . token() . "&_c=" . $_SESSION["RECID_CLIENTE"] . "&FechaIni=" . $FechaIni . "&FechaFin=" . $FechaFin . "&" . $_SERVER['QUERY_STRING'] . "&_r=" . $_SESSION["RECID_ROL"];
    // echo "<a href='".$url."' target='_blank'>".$url."</a>"; br();
    $json      = file_get_contents($url);
    $array     = json_decode($json, TRUE);

    $FirstDate = $array[0]['firstDate']['firstDate'];
    /** FirstDate */
    $FirstYear = $array[0]['firstDate']['firstYear'];
    /** firstYear */
    $maxDate   = $array[0]['maxDate']['maxDate'];
    /** maxDate */
    $maxDate   = ($FechaFin < $maxDate) ? $FechaFin : $maxDate;
    /** maxDate */
    $maxYear   = $array[0]['maxDate']['maxYear'];
    /** maxYear */
    $data      = $array[0]['rango_fecha'];
    // var_export($array);
    $class_btn_pag = 'p-2 btn btn-sm btn-light border-0 w50';
    function paginar($v, $l, $p, $dia, $class_btn_pag)
    {
        $disabled = (count($v) - $p) == 1 ? 'disabled' : '';
        $paginas = ceil(count($v) / $l);

        if ($p > 1) {
            echo '<button title="Anterior" type="submit" class="' . $class_btn_pag . '" name="k" value="' . ($p - 1) . '">' . imgIcon('left', 'Anterior', 'w12') . '</button>';
        } else {
            echo '<button disabled title="Anterior" type="submit" class="' . $class_btn_pag . '" name="k" value="' . ($p - 1) . '">' . imgIcon('left', 'Anterior', 'w12') . '</button>';
        }
        echo $dia;
        if ($p < $paginas) {
            echo '<button ' . $disabled . ' title="Siguiente" type="submit" class="' . $class_btn_pag . '" name="k" value="' . ($p + 1) . '">' . imgIcon('right', 'Siguiente', 'w12') . '</button>';
        } else {
            echo '<button disabled title="Siguiente" type="submit" class="' . $class_btn_pag . '" name="k" value="' . ($p + 1) . '">' . imgIcon('right', 'Siguiente', 'w12') . '</button>';
        }
    }
    $p = (isset($_GET['k'])) ? $_GET['k'] : 0;

    if (!$array[0]['error']) {
        $data2     = $array[0][$_datos];
        /** num_dia */
        $primero   = (array_key_first($data));
        $ultimo    = (array_key_last($data));

        foreach ($data2 as $key => $value) {
            $banner = Fech_Format_Var($value['Fecha'], 'd') . ' ' . Nombre_Mes($value['Fecha']) . ' ' . Fech_Format_Var($value['Fecha'], 'Y');
            $dia = '<span class="align-middle">' . nombre_dias($value['num_dia'], true) . '&nbsp;<span class="">' . $banner . '</span></span>';
            $dia = '<small><div class="d-flex align-items-center align-middle text-white radius h40 px-4 border fw4 opa9 text-center ' . $bgcolor . '" value="">' . $dia .= '</div></small>';
        }
    }
}

if (($_datos != 'cta_horas') && ($_datos != 'mobile')) {
    // if (!$array[0]['error']) {
        $si_datos = '1';
        /** para mostrar el botón de totales en modulo general */
?>
        <div class="col-12 mt-n3 mt-sm-0 <?=$OcultarCount?>" id="PagDia">
            <div class="mt-3 d-flex justify-content-sm-end justify-content-center">
                <button title="Primero" type="submit" class="<?= $class_btn_pag ?>" name="k" value="<?= $primero ?>"><?= imgIcon('double-left', 'Primero', 'w12') ?></button>
                <?php
                paginar($data, 1, $p, $dia, $class_btn_pag);
                ?>
                <button title="Ultimo" type="submit" class="<?= $class_btn_pag ?>" name="k" value="<?= $ultimo ?>"><?= imgIcon('double-right', 'Último', 'w12') ?></button>
            </div>
        </div>
        <div class="col-6 pt-2">
            <div class="d-flex justify-content-start text-secondary fontq">
            <div class="custom-control custom-switch" id="divRangoFecha">
                <?php
                $check_range = (($_GET['_range'] ?? '') ==  'on') ? 'checked' : ''; 
                ?>
                    <input <?=$check_range?> type="checkbox" class="custom-control-input" name="_range" id="_range">
                    <label class="custom-control-label fontq" for="_range" style="padding-top: 3px;">Rango Fecha</label>
                </div>
            </div>
        </div>
        <div class="col-6 d-none pt-2">
            <div class="d-flex justify-content-end text-secondary fontq">
                Dias Filtrados: <span class="ml-1 ls1 fw5"><?= count($data) ?></span><br/>
            </div>
        </div>
<?php
}
?>