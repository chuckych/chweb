<?php
session_start();
require __DIR__ . '../../../config/index.php';
$Modulo='26';
    // print_r($MESSAGE[0]['u_id']);
?>
<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "../../../llamadas.php"; ?>
</head>

<body class="animate__animated animate__fadeIn bg-white">
    <!-- inicio container -->
    <div class="container-fluid">
        <?php

        if (empty($_GET['u_id'])) {
            echo '<div class="row p-3">
            <div class="col-12 p-3 alert alert-info fw4 border-0">No hay Legajo seleccionado.</div>
            </div>';
            exit;
        }
        if (empty($_SESSION["TK_MOBILE"])) {
            echo '<div class="row p-3">
            <div class="col-12 p-3 alert alert-info fw4 border-0">Sesi&oacute;n expirada.</div>
            </div>';
            exit;
        }

        $tkcliente = $_SESSION["TK_MOBILE"];
        $id        = $_GET['u_id'];
        $url = "https://server.xenio.uy/list.php?u_id=" . $id . "&tk=" . $tkcliente . "&TYPE=LIST_TRAIN";
        $json = file_get_contents($url);
        $array = json_decode($json, TRUE);
        $MESSAGE = $array['MESSAGE'];
        
        ?>
        <div class="row">
            <div class="col-12">
                <button data="<?= $MESSAGE[0]['u_id'] ?>" id="reset_face" class="float-left btn btn-outline-custom fontq border">Restablecer</button><p id="reset_respuesta" class="fontq m-0 text-dark fw4 p-2"></p>
            </div>
        </div>
        <!-- Encabezado -->
        <!-- Fin Encabezado -->
        <?php if (token_exist($_SESSION['RECID_CLIENTE'])) {
            /** Check de token */ ?>
            <div class="row" id="rostros">
                <!-- <div class="col-12"><button data="29988600" id="reset_face" class="float-left btn btn-outline-custom fontq border">Restablecer</button></div> -->
                <?php
                foreach ($MESSAGE as $key => $value) {
                    $textBTN     = ($value['status'] == '1') ? 'Enrolar' : 'Listo';
                    $disabledBTN = ($value['status'] == '1') ? '' : 'disabled';
                    $btn_enroll  = ($value['status'] == '1') ? 'btn_enroll' : 'btn_enrolado';
                    $btn_color   = ($value['status'] == '1') ? 'btn-custom' : 'btn-success opa5';
                ?>
                    <div class="col-sm-4 col-6 col-md-3 col-lg-2">
                        <div class="p-3 my-2 shadow-sm">
                            <div class="d-flex justify-content-center">
                                <img loading="lazy" src="<?= $value['face_url'] ?>" class="w120 img-fluid rounded">
                            </div>
                            <div class="d-flex justify-content-center">
                                <button id="<?= $value['_id'] ?>" <?= $disabledBTN ?> data="<?= $value['u_id'] ?>" data1="<?= $value['face_url'] ?>" data2="<?= $value['_id'] ?>" class="<?= $btn_enroll ?> img-fluid btn <?= $btn_color ?> fontp border-0 mt-1 w-100 m-0 w120 fw4"><?= $textBTN ?></button>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        <?php } else {
            echo '<div class="alert alert-light mt-3">La Cuenta no tiene Token Mobile Asociado</div>';
        }
        /** Fin de check de token*/ ?>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "../../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLE */
    // require __DIR__ . "../../../js/DataTable.php";
    ?>
    <script src="enroll.js?v=<?=vjs()?>"></script>
</body>

</html>