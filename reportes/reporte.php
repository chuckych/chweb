<style type="text/css">
    table {
        border: 0px solid #cecece;
        border-collapse: collapse;
        width: 190mm;
        margin: 5mm;
        color: #333333;
        /* 210mm - 5mm x 2 (margen A4) - 5mm x 2 (margen tabla) */
        /* margin: 5mm; */
    }

    td {
        border: 1px solid #e2e2e2;
        padding: 5px;
        /* max-width: 25%; */
        word-wrap: break-word;
        font-size: 11px;
    }

    .divcont {
        /* margin-right: 10mm; */
        padding-top: 5px;
        text-align: justify;
        font-family: Arial, Helvetica, sans-serif;
        font-weight: 200;
        line-height: 1.5;
        /* font-size: small; */
    }

    .label {
        line-height: 1.6;
        /* padding: 7px; */
        vertical-align: top;
        /* padding-left: 0px; */
        /* font-size: small; */
        font-family: Arial, Helvetica, sans-serif;
    }

    .text-primary {
        color: blue;
    }

    .text-dark {
        color: #333333;
    }

    .text-danger {
        color: crimson;
    }

    .titulo {
        font-size: 8pt;
        margin: 5mm;
        /* margin-bottom: px; */
    }

    .dato {
        font-weight: bold;
    }

    .contenedor {
        font-family: Arial, Helvetica, sans-serif;
        font-weight: 200;
        line-height: 1.5;
        font-size: 8pt;
        margin: 5mm;
    }

    .border {
        border: solid 1px #333;
    }

    .border-bottom {
        border-bottom: solid 0.5pt #333;
    }

    .border-top {
        border-top: solid 0.5pt #333;
    }

    .border-y {
        border-bottom: solid 0.5pt #333;
        border-top: solid 0.5pt #333;
    }

    .pad5 {
        padding: 5px;
    }

    .pady-5 {
        padding-top: 5px;
        padding-bottom: 5px;
    }

    .text-center {
        text-align: center;
    }

    .m-5 {
        margin: 5mm;
    }

    .mx-5 {
        margin-left: 5mm;
        margin-right: 5mm;
    }

    .pb-2 {
        padding-bottom: 2px;
    }

    .pb-4 {
        padding-bottom: 4px;
    }

    .pb-6 {
        padding-bottom: 6px;
    }

    .page-break {
        page-break-after: always;
    }

    .bg-light {
        background-color: #fafafa;
    }
</style>

<?php
require __DIR__ . '/../config/index.php';

$check_dl = (isset($_GET['_dl'])) ? "AND FICHAS.FicDiaL = '1'" : ''; /** Filtrar Dia Laboral */

$FechaIni = test_input($_GET['FechaIni']);
$FechaFin = test_input($_GET['FechaFin']);

require __DIR__ . '/../filtros/filtros.php';
require __DIR__ . '/../config/conect_mssql.php';

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$FechaIni = '20200401';
$FechaFin = '20200420';

$sql_query = "SELECT DISTINCT 
FICHAS.FicLega AS Gen_Lega, 
PERSONAL.LegApNo AS Gen_Nombre, 
PERSONAL.LegTDoc AS Gen_TDoc,
PERSONAL.LegDocu AS Gen_Docu,
EMPRESAS.EmpRazon AS Gen_empresa,
PLANTAS.PlaDesc AS Gen_planta, 
CONVENIO.ConDesc AS Gen_convenio,
SECTORES.SecDesc AS Gen_sector, 
SECCION.Se2Desc AS Gen_seccion,
GRUPOS.GruDesc AS Gen_grupo, 
SUCURSALES.SucDesc AS Gen_sucur,
.FICHAS.FicFech AS Gen_Fecha, 
DATEPART(dw, .FICHAS.FicFech) AS Gen_Dia_Semana, 
Gen_Horario = CASE FICHAS.FicDiaL WHEN 0 THEN CASE FICHAS.FicDiaF WHEN 1 THEN 'Feriado' ELSE 'Franco' END ELSE (FICHAS.FicHorE + ' a ' + FICHAS.FicHorS) END
FROM .FICHAS
INNER JOIN PERSONAL ON .FICHAS.FicLega = .PERSONAL.LegNume
INNER JOIN PLANTAS ON PERSONAL.LegPlan=PLANTAS.PlaCodi
INNER JOIN SECTORES ON PERSONAL.LegSect=SECTORES.SecCodi
INNER JOIN SECCION ON PERSONAL.LegSec2 = SECCION.Se2Codi AND PERSONAL.LegSect = SECCION.SecCodi
INNER JOIN EMPRESAS ON PERSONAL.LegEmpr=EMPRESAS.EmpCodi
INNER JOIN CONVENIO ON PERSONAL.LegConv = CONVENIO.ConCodi
INNER JOIN GRUPOS ON PERSONAL.LegGrup=GRUPOS.GruCodi
INNER JOIN SUCURSALES ON PERSONAL.LegSucu=SUCURSALES.SucCodi
WHERE PERSONAL.LegFeEg = '17530101' AND FICHAS.FicLega IN (30366320) AND FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $check_dl $filtros ORDER BY .FICHAS.FicFech, FICHAS.FicLega";
// print_r($sql_query); exit;
$queryRecords = sqlsrv_query($link, $sql_query, $param, $options);


?>
<page backtop="30pt" backbottom="5pt" class="contenedor">
    <?php
    while ($row = sqlsrv_fetch_array($queryRecords)):
        $Gen_Lega = $row['Gen_Lega'];
        $Gen_TDoc = $row['Gen_TDoc'];
        $Gen_Docu = $row['Gen_Docu'];
        $Gen_Nombre = $row['Gen_Nombre'];
        $Gen_empresa = $row['Gen_empresa'];
        $Gen_planta = $row['Gen_planta'];
        $Gen_convenio = $row['Gen_convenio'];
        $Gen_sector = $row['Gen_sector'];
        $Gen_seccion = $row['Gen_seccion'];
        $Gen_grupo = $row['Gen_grupo'];
        $Gen_sucur = $row['Gen_sucur'];
        $Gen_Fecha = $row['Gen_Fecha']->format('d/m/Y');
        $Gen_Fecha2 = $row['Gen_Fecha']->format('Ymd');
        $Gen_Dia_Semana = $row['Gen_Dia_Semana'];
        $Gen_Horario = $row['Gen_Horario'];

        /** FICHADAS */
        $query_Fic = "SELECT
        REGISTRO.RegHoRe AS Fic_Hora, /** HORA */
        Fic_Tipo = CASE REGISTRO.RegTipo WHEN 0 THEN 'Capturador' ELSE 'Manual' END, /** TIPO */
        Fic_Estado = CASE REGISTRO.RegFech WHEN REGISTRO.RegFeRe THEN CASE REGISTRO.RegHora WHEN REGISTRO.RegHoRe THEN 'Normal' ELSE 'Modificada' END ELSE 'Modificada' END /** ESTADO */
        FROM REGISTRO
        WHERE REGISTRO.RegFeAs = '$Gen_Fecha2' AND REGISTRO.RegLega = '$Gen_Lega'
        ORDER BY REGISTRO.RegFeAs,REGISTRO.RegLega,REGISTRO.RegFeRe,REGISTRO.RegHoRe";

        $result_Fic = sqlsrv_query($link, $query_Fic, $param, $options);
        // print_r($query_Fic).PHP_EOL; exit;
        if (sqlsrv_num_rows($result_Fic) > 0) {
            while ($row_Fic = sqlsrv_fetch_array($result_Fic)):
                $Fic_Hora[] = array(
                    'Fic' => $row_Fic['Fic_Hora'],
                    'Estado' => $row_Fic['Fic_Estado'],
                    'Tipo' => $row_Fic['Fic_Tipo']
                );
            endwhile;
            sqlsrv_free_stmt($result_Fic);
            $primero = (array_key_first($Fic_Hora));
            $ultimo = (array_key_last($Fic_Hora));
            $primero = (array_values($Fic_Hora)[$primero]);
            $ultimo = (array_values($Fic_Hora)[$ultimo]);
            $ultimo = ($ultimo == $primero) ? array('Fic' => "", 'Estado' => "", 'Tipo' => "") : $ultimo;
        } else {
            $Fic_Hora[] = array('Fic' => "", 'Estado' => "", 'Tipo' => "");
            $primero = array('Fic' => "", 'Estado' => "", 'Tipo' => "");
            $ultimo = array('Fic' => "", 'Estado' => "", 'Tipo' => "");
        }
        $entrada = color_fichada(array($primero));
        $salida = color_fichada(array($ultimo));
        /** FIN FICHADAS */

        /** NOVEDADES */
        $query_Nov = "SELECT  
        FICHAS3.FicNove AS nov_novedad,
        NOVEDAD.NovDesc AS nov_descripcion,
        NOVEDAD.NovTipo AS nov_tipo,
        FICHAS3.FicHoras AS nov_horas
        FROM FICHAS3,NOVEDAD
        WHERE FICHAS3.FicLega = '$Gen_Lega' 
        AND FICHAS3.FicFech = '$Gen_Fecha2'
        AND FICHAS3.FicNove = NOVEDAD.NovCodi
        AND FICHAS3.FicNove > 0 
        AND FICHAS3.FicNoTi >= 0
        ORDER BY FICHAS3.FICFech";
        $result_Nov = sqlsrv_query($link, $query_Nov, $param, $options);
        // print_r($query_Nov); exit;
    
        if (sqlsrv_num_rows($result_Nov) > 0) {
            while ($row_Nov = sqlsrv_fetch_array($result_Nov)):
                $Novedad[] = array(
                    'Cod' => $row_Nov['nov_novedad'],
                    'Descripcion' => $row_Nov['nov_descripcion'],
                    'Horas' => $row_Nov['nov_horas'],
                    'Tipo' => $row_Nov['nov_tipo']);
            endwhile;
            sqlsrv_free_stmt($result_Nov);
        } else {
            $Novedad[] = array(
                'Cod' => "",
                'Descripcion' => "",
                'Horas' => "",
                'Tipo' => ""
            );
        }
        if (is_array($Novedad)) {
            foreach ($Novedad as $fila) {
                $Cod[] = ($fila["Cod"]);
                $desc2[] = ($fila["Descripcion"]);
                $desc3[] = ($fila["Horas"]);
            }
            $CodNov = implode("<br/>", $Cod);
            $Novedad = implode("<br/>", $desc2);
            $NoveHoras = implode("<br/>", $desc3);
            unset($Cod);
            unset($desc2);
            unset($desc3);
        }
        /** FIN NOVEDADES */

        /** HORAS */
        $query_Horas = "SELECT FICHAS1.FicHora AS Hora, TIPOHORA.THoDesc AS HoraDesc, TIPOHORA.THoDesc2 AS HoraDesc2, FICHAS1.FicHsHe AS HsHechas, FICHAS1.FicHsAu AS HsCalculadas, FICHAS1.FicHsAu2 AS HsAutorizadas
        FROM FICHAS1,TIPOHORA,TIPOHORACAUSA
        WHERE FICHAS1.FicLega = '$Gen_Lega' AND FICHAS1.FicFech = '$Gen_Fecha2' AND FICHAS1.FicHora = TIPOHORA.THoCodi AND FICHAS1.FicHora = TIPOHORACAUSA.THoCHora AND FICHAS1.FicCaus = TIPOHORACAUSA.THoCCodi AND TIPOHORA.THoColu > 0
        ORDER BY TIPOHORA.THoColu, FICHAS1.FicLega,FICHAS1.FicFech,FICHAS1.FicTurn, FICHAS1.FicHora";
        $result_Hor = sqlsrv_query($link, $query_Horas, $param, $options);
        // print_r($query_Horas);
        // exit;
    
        if (sqlsrv_num_rows($result_Hor) > 0) {
            while ($row_Hor = sqlsrv_fetch_array($result_Hor)):
                $Horas[] = array(
                    'Cod' => $row_Hor['Hora'],
                    'Descripcion' => $row_Hor['HoraDesc'],
                    'Descripcion2' => $row_Hor['HoraDesc2'],
                    'HsHechas' => $row_Hor['HsHechas'],
                    'HsCalc' => $row_Hor['HsCalculadas'],
                    'HsAuto' => $row_Hor['HsAutorizadas']
                );
            endwhile;
            sqlsrv_free_stmt($result_Hor);
        } else {
            $Horas[] = array(
                'Cod' => '',
                'Descripcion' => '',
                'Descripcion2' => '',
                'HsHechas' => '',
                'HsCalc' => '',
                'HsAuto' => ''
            );
        }
        if (is_array($Horas)) {
            foreach ($Horas as $fila) {
                $DescHora[] = ($fila["Descripcion"]);
                $DescHora2[] = ($fila["Descripcion2"]);
                $HsHechas[] = ceronull($fila["HsHechas"]);
                $HsCalc[] = ceronull($fila["HsCalc"]);
                $HsAuto[] = ceronull($fila["HsAuto"]);
            }
            $Hora = implode("<br/>", $DescHora); /** Descripcion 1 del tipo de hora */
            $Hora2 = implode("<br/>", $DescHora2); /** Descripcion 2 del tipo de hora */
            $horas3 = implode("<br/>", $HsHechas);
            $horas4 = implode("<br/>", $HsCalc);
            $horas5 = implode("<br/>", $HsAuto);
            unset($DescHora);
            unset($DescHora2);
            unset($HsHechas);
            unset($HsCalc);
            unset($HsAuto);
            // var_export($Novedades); 
        }
        /** Fin HORAS */

        $matriz[] = array(
            'Gen_TDoc' => TipoDoc($Gen_TDoc),
            'Gen_Docu' => $Gen_Docu,
            'Gen_Lega' => $Gen_Lega,
            'Gen_Nombre' => $Gen_Nombre,
            'Gen_empresa' => $Gen_empresa,
            'Gen_planta' => $Gen_planta,
            'Gen_convenio' => $Gen_convenio,
            'Gen_sector' => $Gen_sector,
            'Gen_seccion' => $Gen_seccion,
            'Gen_grupo' => $Gen_grupo,
            'Gen_sucur' => $Gen_sucur,
            'Gen_Fecha' => $Gen_Fecha,
            'Gen_Dia_Semana' => $Gen_Dia_Semana,
            'Gen_Horario' => $Gen_Horario,
            'Gen_Entrada' => $entrada['Fic'],
            'Gen_Salida' => ($salida['Fic']),
            'Cod' => $CodNov,
            'Novedades' => $Novedad,
            'NovHor' => $NoveHoras,
            'DescHoras' => $Hora,
            'HsHechas' => ($horas3),
            'HsCalc' => ($horas4),
            'HsAuto' => ($horas5),
        );
        // $encabezado[] = array(
        //     'Gen_TDoc'     => TipoDoc($Gen_TDoc),
        //     'Gen_Docu'     => $Gen_Docu,
        //     'Gen_Lega'     => $Gen_Lega,
        //     'Gen_Nombre'   => $Gen_Nombre,
        //     'Gen_empresa'  => $Gen_empresa,
        //     'Gen_planta'   => $Gen_planta,
        //     'Gen_convenio' => $Gen_convenio,
        //     'Gen_sector'   => $Gen_sector,
        //     'Gen_seccion'  => $Gen_seccion,
        //     'Gen_grupo'    => $Gen_grupo,
        //     'Gen_sucur'    => $Gen_sucur
        // );
        unset($Fic_Hora);
        unset($Novedad);
        unset($primero);
        unset($ultimo);
        unset($Horas);
    endwhile;
    // echo '<pre>';
// print_r($matriz); exit;
    ?>
    <page_header>
        <div class="">
            <div class="titulo border-bottom pb-4">FICHADAS NOVEDADES Y HORAS Desde <span
                    class="dato"><?= FechaFormatVar($FechaIni, 'd/m/Y') ?></span> hasta <span
                    class="dato"><?= FechaFormatVar($FechaFin, 'd/m/Y') ?></span></div>
        </div>
    </page_header>
    <page_footer>
        <p style="font-size: small; text-align:right;">
            <span style="text-align:right;">Página [[page_cu]]/[[page_nb]]</span>
        </p>
    </page_footer>
    <table class="encabezado" border="0" style="margin-top:0mm">
        <tr>
            <td class="label">Legajo:</td>
            <td class="label"><span class="dato"><?= $Gen_Lega ?></span> | <?= TipoDoc($Gen_TDoc) ?>: <?= $Gen_Docu ?>
            </td>
            <td class="label">Nombre:</td>
            <td class="label"><span class="dato"><?= $Gen_Nombre ?></span></td>
            <td class="label">Empresa:</td>
            <td class="label"><span class=""><?= $Gen_empresa ?></span></td>
        </tr>
        <tr>
            <td class="label">Planta:</td>
            <td class="label"><span class=""><?= $Gen_planta ?></span></td>
            <td class="label">Convenio:</td>
            <td class="label"><span class=""><?= $Gen_convenio ?></span></td>
            <td class="label">Sector:</td>
            <td class="label"><span class=""><?= $Gen_sector ?></span></td>
        </tr>
        <tr>
            <td class="label">Sección:</td>
            <td class="label"><span class=""><?= $Gen_seccion ?></span></td>
            <td class="label">Grupo:</td>
            <td class="label"><span class=""><?= $Gen_grupo ?></span></td>
            <td class="label">Sucursal:</td>
            <td class="label"><span class=""><?= $Gen_sucur ?></span></td>
        </tr>
    </table>
    <!-- <div class="page-break"></div> -->
    <table border="0" style="margin-top:-3mm">
        <!-- <thead> -->
        <tr>
            <!-- 1 -->
            <td class="label border-y">Fecha</td>
            <!-- 2 -->
            <td class="label border-y">Dia</td>
            <!-- 3 -->
            <td class="label border-y">Horario</td>
            <!-- 4 -->
            <td class="label border-y text-center">Ent</td>
            <!-- 5 -->
            <td class="label border-y text-center">Sal</td>
            <!-- 6 -->
            <td class="label border-y">Cod</td>
            <!-- 7 -->
            <td class="label border-y">Novedades</td>
            <!-- 8 -->
            <td class="label border-y"></td>
            <!-- 9 -->
            <td class="label border-y">Tipo de Hora</td>
            <!-- 10 -->
            <td class="label border-y text-center bg-light">Autor.</td>
            <!-- 11 -->
            <td class="label border-y text-center">Hechas</td>
            <!-- 12 -->
            <td class="label border-y text-center">Calc.</td>
        </tr>
        <?php
        foreach ($matriz as $key => $value) {
            $ColorFranco = ($value['Gen_Horario'] == 'Franco' || $value['Gen_Horario'] == 'Feriado') ? 'bg-light' : '';
            ?>
            <tr class="<?= $ColorFranco ?>">
                <!-- 1 -->
                <td class="label"><?= $value['Gen_Fecha'] ?></td>
                <!-- 2 -->
                <td class="label"><?= nombre_dias($value['Gen_Dia_Semana'], false) ?></td>
                <!-- 3 -->
                <td class="label"><?= $value['Gen_Horario'] ?></td>
                <!-- 4 -->
                <td class="label text-center"><?= ceronull($value['Gen_Entrada']) ?></td>
                <!-- 5 -->
                <td class="label text-center"><?= ceronull($value['Gen_Salida']) ?></td>
                <!-- 6 -->
                <td class="label"><?= ceronull($value['Cod']) ?></td>
                <!-- 7 -->
                <td class="label"><?= ceronull($value['Novedades']) ?></td>
                <!-- 8 -->
                <td class="label"><?= ceronull($value['NovHor']) ?></td>
                <!-- 9 -->
                <td class="label"><?= ceronull($value['DescHoras']) ?></td>
                <!-- 10 -->
                <td class="label text-center bg-light"><?= $value['HsAuto'] ?></td>
                <!-- 11 -->
                <td class="label text-center"><?= $value['HsHechas'] ?></td>
                <!-- 12 -->
                <td class="label text-center"><?= $value['HsCalc'] ?></td>
            </tr>
            <?php
        }
        unset($matriz);
        ?>
        <tr>
            <td colspan="12" class="border-bottom" style="padding:0px"></td>
        </tr>
    </table>

    <!-- <p class="mx-5" style="margin-top:-3mm">Totales Novedades</p>
    <table border="1" style="margin-top:-2mm">
        <tr>
            <td class="bg-light">Descripción</td>
            <td class="bg-light text-center">Cant</td>
            <td class="bg-light text-center">Horas</td>
        </tr>
        <tr>
            <td>Llegada Tarde Injustificada</td>
            <td class="text-center">15</td>
            <td class="text-center">18:00</td>
        </tr>
        <tr>
            <td>Salida Anticipada</td>
            <td class="text-center">15</td>
            <td class="text-center">13:00</td>
        </tr>
    </table>
    <p class="mx-5" style="margin-top:-3mm">Totales Horas</p>
    <table border="1" style="margin-top:-2mm">
        <tr>
            <td class="bg-light">Descripción</td>
            <td class="bg-light text-center">Horas</td>
        </tr>
        <tr>
            <td>Hs Normales</td>
            <td class="text-center">180:00</td>
        </tr>
        <tr>
            <td>Hs Extras</td>
            <td class="text-center">10:00</td>
        </tr>
    </table> -->
    <!-- <div class="page-break"></div> -->

</page>
<?php //}
sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);
?>