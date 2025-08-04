<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../llamadas.php"; ?>
    <title><?= MODULOS['personal'] ?></title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '/../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod('bg-fich', 'white', 'usuarios3.png', MODULOS['personal'], '') ?>
        <!-- Fin Encabezado -->
        <div class="row bg-white pt-3">
            <div class="col-12">
                <button type="button" data-titlet="Nuevo Legajo" class="border btn btn-sm fontq px-3 btn-outline-custom"
                    data-toggle="modal" data-target="#altaNuevoLeg">
                    <span>Nuevo</span>
                </button>
                <button type="button" class="btn btn-outline-custom border btn-sm fontq Filtros" data-toggle="modal"
                    data-target="#Filtros">
                    Filtros
                </button>
                <span id="trash_all" data-toggle="tooltip" data-placement="top" data-html="true" title=""
                    data-original-title="<b>Limpiar Filtros</b>" class="fontq text-secondary mx-1 pointer"><i
                        class="bi bi-trash"></i></span>
                <button type="button" class="btn btn-light text-success fw5 border btn-sm fontq" id="btnExcel"
                    data-titler="Exportar todos los legajos. Incluyendo De Baja">
                    Excel
                </button>
                <!-- </div> -->
                <div class="custom-control custom-switch float-right pt-1" data-titlet="Filtrar Legajos Inactivos">
                    <input type="checkbox" class="custom-control-input" name="_eg" id="_eg">
                    <label class="custom-control-label" for="_eg" style="padding-top: 3px;">De Baja</label>
                </div>
                <div class="custom-control custom-switch float-right pt-1 mr-2" data-titlet="Ordenar por Nombre">
                    <input type="checkbox" class="custom-control-input" name="_porApNo" id="_porApNo">
                    <label class="custom-control-label" for="_porApNo" style="padding-top: 3px;">Por Nombre</label>
                </div>
            </div>
        </div>
        <div class="row bg-white py-3 radius invisible" id="PersonalTable">
            <div class="col-12 table-responsive">
                <table class="table table-hover text-nowrap w-100 p-2 border" id="table-personal">
                    <thead class="text-uppercase border-top-0">
                        <tr>
                            <th class="text-center p-2"><i class="bi bi-pencil"></i></th>
                            <th><span data-toggle="tooltip" data-placement="top" data-html="true"
                                    data-original-title="<strong>Legajo</strong>">LEG.</span></th>
                            <!-- <th>NOMBRE y APELLIDO</th> -->
                            <th class="text-center"><span data-toggle="tooltip" data-placement="top" data-html="true"
                                    data-original-title="<strong>Tipo de personal</strong>">TIPO</span></th>
                            <th>EMPRESA / PLANTA</th>
                            <th>SECTOR / SECCION</th>
                            <th>GRUPO / SUCURSAL</th>
                            <th>CONVENIO / REGLA</th>
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
    require __DIR__ . '/modal_Filtros.html';
    require __DIR__ . '/modalNuevoLeg.php';
    ?>
    <script src="altaLeg-min.js?v=<?= version_file("/personal/altaLeg-min.js") ?>"></script>
    <script src="script.js?v=<?= version_file("/personal/script.js") ?>"></script>
    <script src="perExcel-min.js?v=<?= version_file("/personal/perExcel-min.js") ?>"></script>
    <script src="../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../js/select2.min.js"></script>
    <script src="getSelect/select.js?v=<?= version_file("/personal/getSelect/select.js") ?>"></script>
    <script src="../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>

    <script>
        //Fetch data using Axios Ajax
        $('#LegNume').mask('000000000');

        axios.get('getEstruct.php?Estruct=Emp&length=1000')
            .then(function (response) {
                //Get the select element
                let LegEmpr = document.getElementById("LegEmpr");

                //Loop through the response data and create a new option element for each item
                response.data.forEach(function (item) {
                    let option = document.createElement("option");
                    option.value = item.id;
                    option.text = item.text;

                    //Add the new option element to the select element
                    LegEmpr.add(option);
                });
            })
            .catch(function (error) {
                console.log(error);
            });
        select2Simple('#LegEmpr', 'Empresa', true, false, '100%')
    </script>
</body>

</html>