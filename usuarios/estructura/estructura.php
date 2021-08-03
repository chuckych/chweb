<?php
ExisteRol($_GET['_r']);
UnsetGet('id');
UnsetGet('_r');

// list($id_Rol, $nombreRol, $clienteRol, $UsuariosRol, $recid_clienteRol, $idClienteRol) = Rol_Recid($_GET['_r']);
(list($id_Rol, $nombreRol, $clienteRol, $UsuariosRol, $recid_clienteRol, $idClienteRol) = Rol_Recid($_GET['_r']));
switch ($_GET['e']) {
    case 'sectores':
        $e_titulo   = 'Sectores';
        /** Titulo del encabezado */
        $e_mensaje  = 'No hay sectores disponibles';
        /** Mensaje sin valores para agregar  */
        $e_mensaje2 = 'Todos los sectores han sido quitados';
        /** Mensaje sin valores para quitar  */
        $submit     = 'alta_sector';
        /** Valor del submit para el crud de alta */
        $submitb    = 'baja_sector';
        /** Valor del submit para el crud de baja */
        $submitba   = 'alta_seccion';
        /** Valor del submit para el crud de alta */
        $submitbb   = 'baja_seccion';
        /** Valor del submit para el crud de baja */
        $e_tabla_a  = 'alta_tabla.php';
        /** nombre de archivo con la tabla de alta */
        $e_tabla_b  = 'baja_tabla.php';
        /** nombre de archivo con la tabla de baja */
        $GetRol     = 'GetEstructRol';
        /**  */
        /** para el crud */
        $e_nombretabla = 'sect_roles';
        /** nombre de la tabla de la BD */
        $e_coltabla1   = 'sector';
        /** nombre de la columna de del sector */
        $e_coltabla2   = 'recid_rol';
        /** nombre de la columna del recid */
        $e_coltabla3   = 'id_rol';
        /** nombre de la columna del id del valor */
        $e_coltabla4   = 'cliente';
        /** nombre de la columna del id de cliente */
        /** */
        $getjson       = 'GetEstructura.php';
        $arrayjson     = 'sectores';
        $arrayjson2    = 'sector';
        $arrayjson3    = 'seccion';
        break;
    case 'plantas':
        $e_titulo      = 'Plantas';
        $e_mensaje     = 'No hay plantas disponibles';
        $e_mensaje2    = 'Todas las plantas han sido quitadas';
        $submit        = 'alta_planta';
        $submitb       = 'baja_planta';
        $e_tabla_a     = 'alta_tabla.php';
        $e_tabla_b     = 'baja_tabla.php';
        $GetRol        = 'GetEstructRol';
        /** para el crud */
        $e_nombretabla = 'plan_roles';
        $e_coltabla1   = 'planta';
        $e_coltabla2   = 'recid_rol';
        $e_coltabla3   = 'id_rol';
        $e_coltabla4   = 'cliente';
        /** */
        $getjson       = 'GetEstructura.php';
        $arrayjson     = 'plantas';
        $arrayjson2    = 'planta';
        break;
    case 'grupos':
        $e_titulo      = 'Grupos';
        $e_mensaje     = 'No hay grupos disponibles';
        $e_mensaje2    = 'Todos los grupos han sido agregados';
        $submit        = 'alta_grupo';
        $submitb       = 'baja_grupo';
        $e_tabla_a     = 'alta_tabla.php';
        $e_tabla_b     = 'baja_tabla.php';
        $GetRol        = 'GetEstructRol';
        /** para el crud */
        $e_nombretabla = 'grup_roles';
        $e_coltabla1   = 'grupo';
        $e_coltabla2   = 'recid_rol';
        $e_coltabla3   = 'id_rol';
        $e_coltabla4   = 'cliente';
        /** */
        $getjson       = 'GetEstructura.php';
        $arrayjson     = 'grupos';
        $arrayjson2    = 'grupo';
        break;
    case 'sucursales':
        $e_titulo      = 'Sucursales';
        $e_mensaje     = 'No hay sucursales disponibles';
        $e_mensaje2    = 'Todas las sucursales han sido agregadas';
        $submit        = 'alta_sucursal';
        $submitb       = 'baja_sucursal';
        $e_tabla_a     = 'alta_tabla.php';
        $e_tabla_b     = 'baja_tabla.php';
        $GetRol        = 'GetEstructRol';
        /** para el crud */
        $e_nombretabla = 'suc_roles';
        $e_coltabla1   = 'sucursal';
        $e_coltabla2   = 'recid_rol';
        $e_coltabla3   = 'id_rol';
        $e_coltabla4   = 'cliente';
        /** */
        $getjson       = 'GetEstructura.php';
        $arrayjson     = 'sucursales';
        $arrayjson2    = 'sucursal';
        break;
    case 'empresas':
        $e_titulo      = 'Empresas';
        $e_mensaje     = 'No hay empresas disponibles';
        $e_mensaje2    = 'Todas las empresas han sido agregadas';
        $submit        = 'alta_empresa';
        $submitb       = 'baja_empresa';
        $e_tabla_a     = 'alta_tabla.php';
        $e_tabla_b     = 'baja_tabla.php';
        $GetRol        = 'GetEstructRol';
        /** para el crud */
        $e_nombretabla = 'emp_roles';
        $e_coltabla1   = 'empresa';
        $e_coltabla2   = 'recid_rol';
        $e_coltabla3   = 'id_rol';
        $e_coltabla4   = 'cliente';
        /** */
        $getjson       = 'GetEstructura.php';
        $arrayjson     = 'empresas';
        $arrayjson2    = 'empresa';
        break;
    case 'convenios':
        $e_titulo      = 'Convenios';
        $e_mensaje     = 'No hay convenios disponibles';
        $e_mensaje2    = 'Todos los convenios han sido agregados';
        $submit        = 'alta_convenio';
        $submitb       = 'baja_convenio';
        $e_tabla_a     = 'alta_tabla.php';
        $e_tabla_b     = 'baja_tabla.php';
        $GetRol        = 'GetEstructRol';
        /** para el crud */
        $e_nombretabla = 'conv_roles';
        $e_coltabla1   = 'convenio';
        $e_coltabla2   = 'recid_rol';
        $e_coltabla3   = 'id_rol';
        $e_coltabla4   = 'cliente';
        /** */
        $getjson       = 'GetEstructura.php';
        $arrayjson     = 'convenios';
        $arrayjson2    = 'convenio';
        break;
    case 'secciones':
        $e_titulo      = 'Secciones';
        $e_mensaje     = 'No hay secciones disponibles';
        $e_mensaje2    = 'Todas las secciones han sido agregadas';
        $submit        = 'alta_seccion';
        $submitb       = 'baja_seccion';
        $e_tabla_a     = 'alta_tabla.php';
        $e_tabla_b     = 'baja_tabla.php';
        $GetRol        = 'GetEstructRol';
        /** para el crud */
        $e_nombretabla = 'secc_roles';
        $e_coltabla1   = 'seccion';
        $e_coltabla2   = 'recid_rol';
        $e_coltabla3   = 'id_rol';
        $e_coltabla4   = 'cliente';
        $e_coltabla5   = 'sector';
        /** */
        $getjson       = 'GetEstructura.php';
        $arrayjson     = 'secciones';
        $arrayjson2    = 'seccion';
        break;
    default:
        header('Location:../roles/?_c=' . $_GET['_c']);
        exit;
        break;
}
require __DIR__ . '/crud.php';
?>
<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <title><?= MODULOS['cuentas'] ?> » Rol » Estructura</title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <div class="pb-3">
            <?= encabezado_mod($bgcolor, 'white', 'estructura.png', $e_titulo, '') ?>
            <div class="row bg-white shadow-sm py-3">
                <div class="col-6">
                    <p class="p-0 m-0">Rol: <span class="fw4"><?= $nombreRol ?></span></p>
                </div>
                <div class="col-6">
                    <a href="/<?= HOMEHOST ?>/usuarios/roles/?_c=<?= $recid_clienteRol ?>" class="btn fontq mt-1 float-right m-0 opa7 text-white <?=$bgcolor?>">Volver a Roles</a>
                </div>
                <div class="col-12">
                    <p class="p-0 m-0 fontq">Cliente: <?= $clienteRol ?></p>
                    <p class="p-0 m-0 fontq">Usuarios del Rol: <?= $UsuariosRol ?></p>
                </div>
            </div>
            <?php if(!principal($_GET['_r'])){ /** Check de usuario principal del sistema*/?>
            <div class="row bg-transparent mt-2" >
                <div class="p-3 bg-light m-0 w-100"><?= $e_titulo ?></div>
                <div class="col-12 py-3 bg-white">
                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="active border fontq btn btn-sm w120 btn-outline-secondary" id="pills-addsectores-tab" data-toggle="pill" href="#pills-addsectores" role="tab" aria-controls="pills-addsectores" aria-selected="true"><small></i>DISPONIBLES</small></a>
                        </li>
                        <?php if (estructura_rol($GetRol, $_GET['_r'], $_GET['e'], $arrayjson2)) {
                            $cant_rol = (estructura_rol_count($GetRol, $_GET['_r'], $_GET['e'], $arrayjson2));
                        ?>
                            <li class="nav-item ml-1">
                                <a class="border fontq btn btn-sm w120 btn-outline-success" id="pills-deletesectores-tab" data-toggle="pill" href="#pills-deletesectores" role="tab" aria-controls="pills-deletesectores" aria-selected="false"><small></i>ACTIVOS <span class="ls1 fw5">(<?= $cant_rol ?>)</span></small></a>
                            </li>
                        <?php }; ?>
                    </ul>
                </div>
                <div class="col-12 pb-3 animate__animated animate__fadeIn">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-addsectores" role="tabpanel" aria-labelledby="pills-addsectores-tab">
                            <?php require __DIR__ . "/" . $e_tabla_a ?>
                        </div>
                        <?php if (estructura_rol($GetRol, $_GET['_r'], $_GET['e'], $arrayjson2)) { ?>
                            <div class="tab-pane fade" id="pills-deletesectores" role="tabpanel" aria-labelledby="pills-deletesectores-tab">
                                <?php require __DIR__ . "/" . $e_tabla_b ?>
                            </div>
                        <?php }; ?>
                    </div>
                </div>
            </div>
            <?php }else{ echo '<div class="alert alert-light mt-3">Rol principal del sistema. No se puede modificar.</div>';} /** Fin de check de usuario principal del sistema*/ ?>
        </div>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "../../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "../../../js/DataTable.php";
    ?>
    <script>
        $('a[role="tab"]').on('shown.bs.tab', function(e){ $($.fn.dataTable.tables(true)).DataTable() .columns.adjust();}); $(document).ready(function(){ $('#table-a').DataTable({ scrollY:'40vh', scrollX:true, scrollCollapse:true, paging:false, searching:0, scrollCollapse:true, info:1, ordering:1, language:{ "url":"/<?=HOMEHOST ?>/js/DataTableSpanish.json"}});}); $(document).ready(function(){ $('#table-b').DataTable({ scrollY:'40vh', scrollX:true, scrollCollapse:true, paging:false, searching:0, scrollCollapse:true, info:1, ordering:1, language:{ "url":"/<?=HOMEHOST ?>/js/DataTableSpanish.json"}});}); $(document).ready(function(){ $('#table-a2').DataTable({ scrollY:'40vh', scrollX:true, scrollCollapse:true, paging:false, searching:0, scrollCollapse:true, info:1, ordering:1, language:{ "url":"/<?=HOMEHOST ?>/js/DataTableSpanish.json"}});}); $(document).ready(function(){ $('#table-b2').DataTable({ scrollY:'40vh', scrollX:true, paging:false, searching:0, scrollCollapse:true, info:1, ordering:1, language:{ "url":"/<?=HOMEHOST ?>/js/DataTableSpanish.json"}});});
    </script>
    <script>
        <?php
        if (estructura_rol($GetRol, $_GET['_r'], $_GET['e'], $arrayjson2)) {
    foreach ($sectores as $value) :?>$(document).ready(function(){ $('#table-secc-<?=$value['idsect'] ?>').DataTable({ scrollY:'40vh', scrollX:true, scrollCollapse:true, paging:false, searching:0, scrollCollapse:true, info:1, ordering:0, language:{ "url":"//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"}});}); $(document).ready(function(){ $('#table-secc-<?=$value['idsect'] ?>_b').DataTable({ scrollY:'70vh', scrollX:true, scrollCollapse:0, paging:false, searching:0, scrollCollapse:true, info:1, ordering:0, language:{ "url":"//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"}});});
        <?php endforeach; } unset($sectores); ?>
    </script>


</body>

</html>