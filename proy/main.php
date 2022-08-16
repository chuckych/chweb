<?php
$_SESSION["RECID_ROL"] = $_SESSION["RECID_ROL"] ?? '';
($_SESSION["RECID_ROL"]) ? '' : header('Location: ../logout.php');
?>
<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "/head.php"; ?>
    <title>Control de proyectos</title>
</head>

<body>
    <div id="mainNav"></div>
    <section class="container">
        <div class="sticky-top" style="margin-top: 60px;"></div>
        <div id="contenedor"></div>
    </section>
    <div id="mainSideBar"></div>
    <?php require __DIR__ . "/footer.php"; ?>
    <div id="modales"></div>
    <div id="divFiltros"></div>
</body>

</html>