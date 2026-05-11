<?php
$Modulo = 43;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuracion</title>
    <?php require __DIR__ . "/../llamadas.php"; ?>
    <style>
        iframe {
            height: 100vh;
        }
    </style>
</head>

<body class="bg-secondary">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '/../nav.php';
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="mb-1 mr-2" viewBox="0 0 16 16">
        <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
        </svg>';
        ?>
        <!-- Encabezado -->
        <?php encabezado_mod_svgIcon('bg-custom', 'white', $svg, '<span>Configuración</span>', ''); ?>
        <!-- Fin Encabezado -->

    </div>
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require_once __DIR__ . "/../js/jquery.php";
    require_once __DIR__ . "/../js/DataTable.php";
    ?>

    <script src="estruct/js/data.js?v=<?= version_file("/configuracion/estruct/js/data.js") ?>"></script>
    <script src="../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="/<?= HOMEHOST ?>/js/select2.min.js"></script>
</body>

</html>