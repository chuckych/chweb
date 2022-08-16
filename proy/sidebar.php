<?php
require __DIR__ . '../../config/index.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST['page'] == 'sidebar')) {
    session_start();
    E_ALL();
    // $_SESSION["RECID_ROL"] = $_SESSION["RECID_ROL"] ?? '';
    // ($_SESSION["RECID_ROL"]) ? '': header('Location: /'.HOMEHOST.'/proy/index.php');
    $arrMod = array_pdoQuery("SELECT 
	`mod_roles`.`modulo` AS `modsrol`, `modulos`.`idtipo` AS `tipo`, `modulos`.`nombre` as `modulo`, `modulos`.`orden` as `orden`
	FROM `mod_roles` 
	INNER JOIN `modulos` ON `mod_roles`.`modulo` = `modulos`.`id`
	WHERE `mod_roles`.`recid_rol` ='$_SESSION[RECID_ROL]' AND  `modulos`.`idtipo` = 6");
    // filtrar $mod_proy por tipo = 6
    // $arrMod = array_filter($data_mod, function ($item) {
    //     return $item['tipo'] == 6;
    // });
    // ordenar por orden 
    usort($arrMod, function ($a, $b) {
        return $a['orden'] > $b['orden'];
    });
    echo json_encode($arrMod);
    exit;
}
?>
<div class="offcanvas offcanvas-start" tabindex="-1" id="mainCanva" aria-labelledby="mainCanvaLabel">
    <div class="offcanvas-header m-0">
        <h2 class="offcanvas-title" id="mainCanvaLabel">Operaciones</h2>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body pt-3">
        <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
            <ul class="navbar-nav" data-bs-dismiss="offcanvas" data-bs-target="#mainCanva" id="ulsidebar">
            </ul>
        </div>
        <div class="mt-3">
            <button class="btn" type="button" data-bs-dismiss="offcanvas">
                Cerrar
            </button>
        </div>
    </div>
</div>
<script src="js/sidebar.js?<?= vjs() ?>"></script>