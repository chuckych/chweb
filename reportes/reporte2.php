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
$FechaFin = '20200405';

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
SUCURSALES.SucDesc AS Gen_sucur
FROM .FICHAS
INNER JOIN PERSONAL ON .FICHAS.FicLega = .PERSONAL.LegNume
INNER JOIN PLANTAS ON PERSONAL.LegPlan=PLANTAS.PlaCodi
INNER JOIN SECTORES ON PERSONAL.LegSect=SECTORES.SecCodi
INNER JOIN SECCION ON PERSONAL.LegSec2 = SECCION.Se2Codi AND PERSONAL.LegSect = SECCION.SecCodi
INNER JOIN EMPRESAS ON PERSONAL.LegEmpr=EMPRESAS.EmpCodi
INNER JOIN CONVENIO ON PERSONAL.LegConv = CONVENIO.ConCodi
INNER JOIN GRUPOS ON PERSONAL.LegGrup=GRUPOS.GruCodi
INNER JOIN SUCURSALES ON PERSONAL.LegSucu=SUCURSALES.SucCodi
WHERE PERSONAL.LegFeEg = '17530101' AND FICHAS.FicLega IN (30366320,29988600) AND FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $check_dl $filtros ORDER BY FICHAS.FicLega";
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
        );
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
    <?php
    foreach ($matriz as $key => $value) {
        //$ColorFranco = ($value['Gen_Horario'] == 'Franco' || $value['Gen_Horario'] == 'Feriado') ? 'bg-light':'';
        ?>
        <table class="encabezado" border="0" style="margin-top:0mm">
            <tr>
                <td class="label">Legajo:</td>
                <td class="label"><span class="dato"><?= $value['Gen_Lega'] ?></span> | <?= ($value['Gen_TDoc']) ?>:
                    <?= ($value['Gen_Docu']) ?>
                </td>
                <td class="label">Nombre:</td>
                <td class="label"><span class="dato"><?= ($value['Gen_Nombre']) ?></span></td>
                <td class="label">Empresa:</td>
                <td class="label"><span class=""><?= ($value['Gen_empresa']) ?></span></td>
            </tr>
            <tr>
                <td class="label">Planta:</td>
                <td class="label"><span class=""><?= ($value['Gen_planta']) ?></span></td>
                <td class="label">Convenio:</td>
                <td class="label"><span class=""><?= ($value['Gen_convenio']) ?></span></td>
                <td class="label">Sector:</td>
                <td class="label"><span class=""><?= ($value['Gen_sector']) ?></span></td>
            </tr>
            <tr>
                <td class="label">Sección:</td>
                <td class="label"><span class=""><?= ($value['Gen_seccion']) ?></span></td>
                <td class="label">Grupo:</td>
                <td class="label"><span class=""><?= ($value['Gen_grupo']) ?></span></td>
                <td class="label">Sucursal:</td>
                <td class="label"><span class=""><?= ($value['Gen_sucur']) ?></span></td>
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
                <!-- <td class="label border-y text-center">Ent</td> -->
                <!-- 5 -->
                <!-- <td class="label border-y text-center">Sal</td> -->
                <!-- 6 -->
                <!-- <td class="label border-y">Cod</td> -->
                <!-- 7 -->
                <!-- <td class="label border-y">Novedades</td> -->
                <!-- 8 -->
                <!-- <td class="label border-y"></td> -->
                <!-- 9 -->
                <!-- <td class="label border-y">Tipo de Hora</td> -->
                <!-- 10 -->
                <!-- <td class="label border-y text-center bg-light">Autor.</td> -->
                <!-- 11 -->
                <!-- <td class="label border-y text-center">Hechas</td> -->
                <!-- 12 -->
                <!-- <td class="label border-y text-center">Calc.</td> -->
            </tr>
            <?php
            //foreach ($matriz as $key => $value) {
            //$ColorFranco = ($value['Gen_Horario'] == 'Franco' || $value['Gen_Horario'] == 'Feriado') ? 'bg-light':'';
            ?>
            <tr class="">
                <!-- 1 -->
                <td class="label"></td>
                <!-- 2 -->
                <td class="label"></td>
                <!-- 3 -->
                <td class="label"></td>
                <!-- 4 -->
                <!-- <td class="label text-center"><?= ceronull($value['Gen_Entrada']) ?></td> -->
                <!-- 5 -->
                <!-- <td class="label text-center"><?= ceronull($value['Gen_Salida']) ?></td> -->
                <!-- 6 -->
                <!-- <td class="label"><?= ceronull($value['Cod']) ?></td> -->
                <!-- 7 -->
                <!-- <td class="label"><?= ceronull($value['Novedades']) ?></td> -->
                <!-- 8 -->
                <!-- <td class="label"><?= ceronull($value['NovHor']) ?></td> -->
                <!-- 9 -->
                <!-- <td class="label"><?= ceronull($value['DescHoras']) ?></td> -->
                <!-- 10 -->
                <!-- <td class="label text-center bg-light"><?= $value['HsAuto'] ?></td> -->
                <!-- 11 -->
                <!-- <td class="label text-center"><?= $value['HsHechas'] ?></td> -->
                <!-- 12 -->
                <!-- <td class="label text-center"><?= $value['HsCalc'] ?></td> -->
            </tr>

            <tr>
                <td colspan="12" class="border-bottom" style="padding:0px"></td>
            </tr>
        </table>
        <?php
    }
    //unset($matriz);  
    ?>
    <!-- Totales -->
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
    <!-- Fin Totales -->
</page>
<?php //}
sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);
?>