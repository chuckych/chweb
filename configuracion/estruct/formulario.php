<?php
session_start();
header('Content-type: text/html; charset=utf-8');
header('Content-Type: text/html; charset=UTF-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch2();
// sleep(1);
$_POST['Cod']  = $_POST['Cod'] ?? '';
$_POST['Desc'] = $_POST['Desc'] ?? '';
$_POST['Tipo'] = $_POST['Tipo'] ?? '';
$Cod  = test_input($_POST['Cod']);
$Desc = test_input(($_POST['Desc']));
$Tipo = test_input($_POST['Tipo']);

switch ($Tipo) {
    case 'u_empresas':
        $titleForm = 'Editar: <span class="fw5">' . $Desc . '</span>';
        break;
    case 'd_empresas':
        $titleForm = '¿Eliminar Empresa <span class="fw5">' . $Desc . '?</span>';
        break;
    case 'c_empresas':
        $titleForm = 'Nueva Empresa';
        break;
    case 'u_plantas':
        $titleForm = 'Editar: <span class="fw5">' . $Desc . '</span>';
        break;
    case 'd_plantas':
        $titleForm = '¿Eliminar Planta <span class="fw5">' . $Desc . '?</span>';
        break;
    case 'c_plantas':
        $titleForm = 'Nueva Planta';
        break;
    case 'u_sucur':
        $titleForm = 'Editar: <span class="fw5">' . $Desc . '</span>';
        break;
    case 'd_sucur':
        $titleForm = '¿Eliminar Sucursal <span class="fw5">' . $Desc . '?</span>';
        break;
    case 'c_sucur':
        $titleForm = 'Nueva Sucursal';
        break;
    case 'u_grupos':
        $titleForm = 'Editar: <span class="fw5">' . $Desc . '</span>';
        break;
    case 'd_grupos':
        $titleForm = '¿Eliminar Grupo <span class="fw5">' . $Desc . '?</span>';
        break;
    case 'c_grupos':
        $titleForm = 'Nuevo Grupo';
        break;
    case 'u_sector':
        $titleForm = 'Editar: <span class="fw5">' . $Desc . '</span>';
        break;
    case 'd_sector':
        $titleForm = '¿Eliminar Sector <span class="fw5">' . $Desc . '?</span>';
        break;
    case 'c_sector':
        $titleForm = 'Nuevo Sector';
        break;
    case 'u_tareas':
        $titleForm = 'Editar: <span class="fw5">' . $Desc . '</span>';
        break;
    case 'd_tareas':
        $titleForm = '¿Eliminar Tarea <span class="fw5">' . $Desc . '?</span>';
        break;
    case 'c_tareas':
        $titleForm = 'Nuevo Tarea';
        break;
    default:
        $titleForm = '';
        break;
}
switch ($Tipo) {
    case 'c_empresas':
    case 'u_empresas':
        require 'formulario_empresa.php';
        exit;
    break;
}
?>
<div class="animate__animated animate__fadeIn p-2 mt-2">
    <form action="crud.php" method="post" class="w-100" id="Formulario">
        <div id="titleForm" class="fw4"><?= $titleForm ?></div>
        <label class="mt-2 fw4" for="cod" id="labelCod" data-titlel="Si se deja en blanco, se asignará automaticamente">C&oacute;digo <i class="bi bi-info-circle"></i></label>
        <input type="hidden" name="tipo" id="tipo" value="<?= $Tipo ?>">
        <input type="tel" placeholder="C&oacute;digo" name="cod" id="cod" class="form-control w100 h40" value="<?= $Cod ?>" maxlength="8">
        <label class="mt-2 fw4" for="desc">Descripci&oacute;n <span class="requerido"></span></label>
        <input type="text" name="desc" id="desc" class="form-control w350 h40" maxlength="30" value="<?= $Desc ?>">
        <br />
        <button type="submit" class="btn btn-custom fontq float-right btn-mobile h40 px-3 ml-sm-1 submit">Aceptar</button>
        <button type="button" class="btn btn-outline-custom border fontq float-right btn-mobile mt-2 mt-sm-0 h40 " id="cancelForm">Cancelar</button>
    </form>
</div>
<script src="/<?= HOMEHOST ?>/configuracion/estruct/js/form.js?v=<?= vjs() ?>"></script>
<!-- <script src="/<?= HOMEHOST ?>/js/select2.min.js"></script> -->
<script src="/<?= HOMEHOST ?>/vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
<script>
    $(function() {
        $('#cod').mask('0000000000');
    });
</script>