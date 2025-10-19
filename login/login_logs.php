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
                <table id="login_logs" class="table text-nowrap p-2">
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
            dom: `
                <'row'
                    <'col-12 d-inline-flex justify-content-between align-items-center'lf>
                >
                <'row' <'col-12'<'border table-responsive't>>>
                <'row'
                    <'col-12 d-inline-flex justify-content-between align-items-center mt-2'ip>
                >
            `,
            columns: [
                {
                    data: 'usuario', className: '', targets: '', title: 'USUARIO',
                    "render": function (data, type, row, meta) {
                        if (!type === 'display') return '';
                        const nombre = row.nombre;
                        return `
                            <div>
                                ${data}<br/>
                                <span class="text-muted">${nombre}</span>
                            </div>
                            `
                    }, visible: true
                },
                {
                    data: 'fechahora', className: '', targets: '', title: 'FECHA HORA',
                    "render": function (data, type, row, meta) {
                        if (!type === 'display') return '';
                        const fechaHora = data.split(' ');
                        return `
                            <div>
                                ${fechaHora[0]}<br/>
                                ${fechaHora[1]}
                            </div>
                            `
                    }, visible: true
                },
                {
                    data: 'estado', className: '', targets: '', title: 'ESTADO',
                    "render": function (data, type, row, meta) {
                        if (!type === 'display') return '';
                        // si estado es correcto poner en verde sino en rojo
                        if (data === 'correcto') {
                            data = `<span class="text-success font-weight-bold">${data}</span>`;
                        } else {
                            data = `<span class="text-danger font-weight-bold">${data}</span>`;
                        }
                        return `
                            <div>
                                ${data}
                            </div>
                            `
                    }, visible: true
                },
                {
                    data: 'cliente', className: '', targets: '', title: 'CUENTA',
                    "render": function (data, type, row, meta) {
                        if (!type === 'display') return '';
                        return `
                            <div>
                                ${data}
                            </div>
                            `
                    }, visible: true
                },
                {
                    data: 'rol', className: '', targets: '', title: 'ROL',
                    "render": function (data, type, row, meta) {
                        if (!type === 'display') return '';
                        return `
                            <div>
                                ${data}
                            </div>
                            `
                    }, visible: true
                },
                {
                    data: 'ip', className: '', targets: '', title: 'IP',
                    "render": function (data, type, row, meta) {
                        if (!type === 'display') return '';
                        return `
                            <div>
                                ${data}
                            </div>
                            `
                    }, visible: true
                },
                {
                    data: 'agent', className: 'w-100', targets: '', title: '',
                    "render": function (data, type, row, meta) {
                        if (!type === 'display') return '';
                        return `
                            <div title="${data}" class="text-truncate" style="max-width: 350px;">
                                ${data}
                            </div>
                            `
                    }, visible: true
                },
            ],
            // scrollY: '50vh',
            // scrollX: true,
            paging: 1,
            searching: 1,
            info: 1,
            ordering: 0,
            language: {
                "url": "/<?= HOMEHOST ?>/js/DataTableSpanish.json"
            }
        });
    </script>
</body>

</html>