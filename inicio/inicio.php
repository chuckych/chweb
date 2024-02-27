<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <title>CH Web</title>
    <style>
        .modal {
            z-index: 1050;
            /* o un número mayor */
        }

        .modal-backdrop {
            z-index: 1040;
            /* debe ser menor que el z-index del modal */
        }
    </style>
</head>

<body class="animate__animated animate__fadeIn">
    <div class="container shadow pb-2">
        <div class="row">
            <div class="col-12">
                <?php require __DIR__ . '../../nav.php'; ?>
            </div>
        </div>
        <?php encabezado_mod($bgcolor, 'white', 'inicio2.png', 'Control Horario Web', ''); ?>
        <div class="row">
            <div class="col-12 p-0">
                <div class="card text-left">
                    <div class="card-body">
                        <h4 class="card-title">Hola
                            <?= $_SESSION["NOMBRE_SESION"] ?>
                        </h4>
                        <p class="text-secondary">
                            <?= $_SESSION["CLIENTE"] ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require __DIR__ . "../../js/jquery.php"; ?>
</body>

</html>