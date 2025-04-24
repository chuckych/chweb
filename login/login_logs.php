<?php
ExisteModRol('1');
secure_auth_ch();
?>
<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "/../llamadas.php"; ?>
    <title>Login Logs</title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2" style="animation-fill-mode: unset">
        <?php require __DIR__ . '/../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod($bgcolor, 'white', 'login.png', 'Login Logs ', '') ?>
        <!-- Fin Encabezado -->
        <div class="row bg-white mt-2 py-3 radius">
            <div class="col-12 table-responsive">
                <table id="login_logs" class="table table-hover text-nowrap" cellspacing="0">
                    <thead class="text-uppercase">
                        <tr>
                            <th>Usuario</th>
                            <th>Nombre</th>
                            <th>Fecha Hora</th>
                            <th>Estado</th>
                            <th>Cuenta</th>
                            <th>Rol</th>
                            <th>IP</th>
                            <th>Agent</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "/../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLE */
    require __DIR__ . "/../js/DataTable.php";
    ?>
    <script>
        $('#login_logs').dataTable({
            bProcessing: true,
            serverSide: true,
            deferRender: true,
            "ajax": {
                url: "?p=data.php",
                type: "POST",
                error: function () {
                    $("#login_logs_processing").css("display", "none");
                }
            },
            columns: [
                {
                    "class": "",
                    "data": "usuario"
                },
                {
                    "class": "",
                    "data": "nombre"
                },
                {
                    "class": "ls1",
                    "data": "fechahora"
                },
                {
                    "class": "fw4",
                    "data": "estado"
                },
                {
                    "class": "",
                    "data": "cliente"
                },
                {
                    "class": "",
                    "data": "rol"
                },
                {
                    "class": "",
                    "data": "ip"
                },
                {
                    "class": "",
                    "data": "agent"
                }
            ],
            scrollY: '50vh',
            scrollX: true,
            paging: 1,
            searching: 1,
            scrollCollapse: true,
            info: 1,
            ordering: 0,
            responsive: 0,
            language: {
                "url": "/<?= HOMEHOST ?>/js/DataTableSpanish.json"
            }
        });
    </script>
</body>

</html>