<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
ultimoacc();
secure_auth_ch2();
// sleep(1);
$_POST['Cod'] = $_POST['Cod'] ?? '';
$_POST['Desc'] = $_POST['Desc'] ?? '';
$_POST['Tipo'] = $_POST['Tipo'] ?? '';
$Cod = test_input($_POST['Cod']);
$Desc = test_input($_POST['Desc']);
$Tipo = test_input($_POST['Tipo']);

switch ($Tipo) {
    case 'u_nacion':
        $titleForm = 'Editar: <span class="fw5">' . $Desc . '</span>';
        break;
    case 'd_nacion':
        $titleForm = '¿Eliminar Nacionalidad <span class="fw5">' . $Desc . '?</span>';
        break;
    case 'c_nacion':
        $titleForm = 'Nueva Nacionalidad';
        break;
    case 'u_provincia':
        $titleForm = 'Editar: <span class="fw5">' . $Desc . '</span>';
        break;
    case 'd_provincia':
        $titleForm = '¿Eliminar Provincia <span class="fw5">' . $Desc . '?</span>';
        break;
    case 'c_provincia':
        $titleForm = 'Nueva Provincia';
        break;
    case 'u_localidad':
        $titleForm = 'Editar: <span class="fw5">' . $Desc . '</span>';
        break;
    case 'd_localidad':
        $titleForm = '¿Eliminar Localidad <span class="fw5">' . $Desc . '?</span>';
        break;
    case 'c_localidad':
        $titleForm = 'Nueva Localidad';
        break;

    default:
        $titleForm = '';
        break;
}
?>
<div class="animate__animated animate__fadeIn p-2 mt-2">
    <form action="crud.php" method="post" class="w-100" id="Formulario">
        <div id="titleForm" class="fw4"><?= $titleForm ?></div>
        <label class="mt-2 fw4" for="cod" id="labelCod">C&oacute;digo <span
                title="Si se deja en blanco, se asignará automaticamente" class="opcional"></span></label>
        <input type="hidden" name="tipo" id="tipo" value="<?= $Tipo ?>">
        <input type="number" name="cod" id="cod" class="form-control w100 h40" value="<?= $Cod ?>">
        <label class="mt-2 fw4" for="desc">Descripci&oacute;n <span class="requerido"></span></label>
        <input type="text" name="desc" id="desc" class="form-control w350 h40" maxlength="30" value="<?= $Desc ?>">
        <br />
        <button type="submit"
            class="btn btn-custom fontq float-right btn-mobile h40 px-3 ml-sm-1 submit">Aceptar</button>
        <button type="button" class="btn btn-outline-custom border fontq float-right btn-mobile mt-2 mt-sm-0 h40 "
            id="cancelForm">Cancelar</button>
    </form>
</div>
<script src="js/form.js?v=<?= vjs() ?>"></script>