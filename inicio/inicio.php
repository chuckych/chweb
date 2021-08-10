<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <title>Control Horario Web</title>
</head>

<body class="animate__animated animate__fadeIn">
    <div class="container shadow pb-2">
        <div class="row">
            <div class="col-12">
                <?php require __DIR__ . '../../nav.php'; ?>
            </div>
        </div>
        <?php 
        $vjs =  str_replace('v','', (vjs()));
		$vjs =  str_replace('.','', ($vjs));
		echo intval($vjs);
        
        ?>
        <?= encabezado_mod($bgcolor, 'white', 'inicio2.png', 'Control Horario Web', ''); ?>
            <div class="row">
                <div class="col-12 p-0">
                    <div class="card text-left">
                        <div class="card-body">
                            <h4 class="card-title">Hola <?= $_SESSION["NOMBRE_SESION"] ?></h4>
                            <p class="text-secondary"><?= $_SESSION["CLIENTE"] ?></p>
                            <!-- <a name="" id="" class="btn btn-primary" href="exportar.php" role="button">Exportar</a> -->
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <?php require __DIR__ . "../../js/jquery.php"; ?>
</body>

</html>