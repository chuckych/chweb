<div class="animate__animated animate__fadeIn p-2 mt-2">
    <form action="crud.php" method="post" class="w-100" id="Formulario">
        <div class="row">
            <div class="col-12">
                <div id="titleForm" class="fw4">
                    <?= $titleForm ?>
                </div>
            </div>
            <div class="col-12 mt-2 overflow-auto" style="max-height: 40vh;">
                <!-- Emp Codigo -->
                <div class="pt-2">
                    <input type="hidden" name="tipo" id="tipo" value="<?= $Tipo ?>">
                    <label class="align-middle fontq mr-2 w100" for="cod" id="labelCod" data-titler="Si se deja en blanco, se asignará automaticamente">C&oacute;digo <i class="bi bi-info-circle"></i></label>
                    <input type="tel" name="cod" id="cod" placeholder="C&oacute;digo" class="form-control h40 w100" value="<?= $Cod ?>" maxlength="5">
                </div>
                <!-- Emp Razón Social -->
                <div class="mt-2">
                    <label class="align-middle fontq mr-2" for="desc">Razón Social </label>
                    <input autofocus="" id="desc" class="form-control h40 w-75" placeholder="Razón Social" type="text" name="desc" maxlength="50" value="<?= $Desc ?>">
                </div>
                <!-- Emp Tipo -->
                <label class="mt-2 align-middle fontq" for="EmpTipo">Tipo / CUIT</label><br>
                <div class="d-inline-flex">
                    <select name="EmpTipo" id="EmpTipo" class="select2Simple form-control h40 w100">
                        <option value="0">Interna</option>
                        <option value="1">Externa</option>
                    </select>
                    <!-- Emp CUIT -->
                    <input id="EmpCUIT" class="form-control h40 ml-2" placeholder="00-00000000-0" type="text" name="EmpCUIT" maxlength="13">
                </div><br>
                <label for="EmpDoNu" class="mt-2">Calle / Número</label><br>
                <div class="d-inline-flex">
                    <!-- Emp Calle -->
                    <input type="text" name="EmpDomi" placeholder="Calle" class="form-control h40" id="EmpDomi" maxlength="50">
                    <!-- Emp Numero -->
                    <input type="number" name="EmpDoNu" class="form-control h40 ml-2" placeholder="Número" id="EmpDoNu">
                </div><br>
                <!-- Emp Direccion  -->
                <label for="EmpPiso" class="mr-2 mt-2">Piso / Depto / CP</label><br>
                <div class="d-inline-flex">
                    <!-- Emp Piso -->
                    <input type="number" name="EmpPiso" class="form-control h40 mr-2 w100" placeholder="Piso" id="EmpPiso">
                    <!-- Emp Depto -->
                    <input type="text" name="EmpDpto" class="form-control h40 mr-2 w100" placeholder="Depto" id="EmpDpto" maxlength="5">
                    <!-- Emp Cp -->
                    <input type="text" name="EmpCoPo" class="form-control h40 w100" placeholder="CP" id="EmpCoPo" maxlength="8">
                </div><br>
                <!-- Emp Provincia -->
                <label for="EmpProv" class="mt-2">Provincia / Localidad</label><br>
                <div class="d-inline-flex">
                    <div>
                        <select class="form-control h40 w200 selectjs_provinciasEmp" name="EmpProv" id="EmpProv">
                        </select>
                    </div>
                    <!-- Emp Localidad -->
                    <div class="ml-2">
                        <select class="form-control h40 selectjs_localidadEmp w200 ml-2" name="EmpLoca" id="EmpLoca">
                        </select>
                    </div>
                </div><br>
                <!-- Fin Domicilio -->
                <!-- Emp Telefono -->
                <label for="EmpTele" class="mt-2">Teléfono / Email</label><br>
                <div class="d-inline-flex">
                    <input type="tel" name="EmpTele" placeholder="Teléfono" class="form-control h40" id="EmpTele" maxlength="15">
                    <!-- Emp Mail -->
                    <input type="email" name="EmpMail" placeholder="Email" class="form-control h40 ml-2" id="EmpMail" maxlength="100">
                </div>
                <!-- Emp Contacto -->
                <div class="mt-2">
                    <label for="EmpCont" class="mr-2 w100">Contacto</label>
                    <input type="text" name="EmpCont" placeholder="Contacto" class="form-control h40 w330" id="EmpCont" maxlength="100">
                </div>
                <!-- Emp Observación -->
                <div class="mt-2">
                    <label for="EmpObse" class="mr-2 w100">Observación</label>
                    <textarea id="EmpObse" placeholder="Observaciones" class="form-control h70 w-100 p-3" name="EmpObse" rows="3" maxlength="240"></textarea>
                </div>
            </div>
        </div>
        <div class="col-12 mt-3">
            <button type="submit" class="btn btn-custom fontq float-right btn-mobile h40 px-3 ml-sm-1 submit">Aceptar</button>
            <button type="button" class="btn btn-outline-custom border fontq float-right btn-mobile mt-2 mt-sm-0 h40 " id="cancelForm">Cancelar</button>
        </div>
    </form>
</div>
<script src="/<?= HOMEHOST ?>/configuracion/estruct/js/form.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/js/select2.min.js"></script>
<script src="/<?= HOMEHOST ?>/vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
<script>
    $(function() {
        $('#EmpCUIT').mask('00-00000000-0');
        $('#cod').mask('0000000000');
        let _c = $('#_c').val()
        let _r = $('#_r').val()
        let urlProv = "/" + _homehost + "/data/getProvincias.php?_c=" + _c + "&_r" + _r;
        let urlLoc = "/" + _homehost + "/data/getLocalidad.php?_c=" + _c + "&_r" + _r;
        select2Ajax(".selectjs_provinciasEmp", "Provincia", true, false, urlProv)
        select2Ajax(".selectjs_localidadEmp", "Localidad", true, false, urlLoc)
        select2Simple('#EmpTipo', 'Tipo', false, false)

        function getEmpresa(cod) {
            $.ajax({
                type: 'post',
                dataType: "json",
                url: "getEmpresa.php?v=" + vjs(),
                data: {
                    cod: cod,
                },
            }).done(function(data) {
                $('#cod').val(data.codigo)
                $('#desc').val((data.descripcion))
                $('#EmpCUIT').val(data.EmpCUIT)
                $('#EmpTipo').val(data.EmpTipo).trigger('change')
                $('#EmpDomi').val(data.EmpDomi)
                $('#EmpDoNu').val(data.EmpDoNu)
                $('#EmpPiso').val(data.EmpPiso)
                $('#EmpDpto').val(data.EmpDpto)
                $('#EmpCoPo').val(data.EmpCoPo)
                $('#EmpTele').val(data.EmpTele)
                $('#EmpMail').val(data.EmpMail)
                $('#EmpCont').val(data.EmpCont)
                $('#EmpObse').val(data.EmpObse)
                Select2Value(data.EmpProv, data.ProDesc, '#EmpProv')
                Select2Value(data.EmpLoca, data.LocDesc, '#EmpLoca')
            });
        }
        if ($('#tipo').val()=='u_empresas') {
            getEmpresa($('#cod').val())
        }
        
    });
</script>