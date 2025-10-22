<form action="alta_opciones.php" method="post" class="form-empresas">
    <!-- Modal Alta Empresa-->
    <div class="modal fadeIn" id="altaEmpresa" data-backdrop="static" data-keyboard="true" role="dialog" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content mx-auto">
                <div class="modal-header border-bottom-0">
                    <h6 class="modal-title" id="staticBackdropLabel">Empresa</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pb-0">
                    <div class="row">
                        <div id="" class="col-12 p-4">
                            <!-- Emp Razón Social -->
                            <div class="form-inline">
                                <label class="align-middle fontq mr-2 w80" for="EmpRazon">Razón Social</label>
                                <input autofocus id="EmpRazon" class="form-control w330" placeholder="" type="text" name="EmpRazon" maxlength="50">
                                <input type="hidden" name="dato" value="alta_empresa">
                            </div>
                            <!-- Emp Tipo -->
                            <div class="form-inline mt-2">
                                <label class="align-middle fontq mr-2 w80" for="EmpTipo">Tipo</label>
                                <select name="EmpTipo" id="EmpTipo" class="select2 form-control w330">
                                    <?php
                                    foreach (TIPO_EMP as $key => $value) {
                                        echo '<option value="' . $value . '">' . $key . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- Emp CUIT -->
                            <div class="form-inline mt-2">
                                <label class="align-middle fontq mr-2 w80" for="EmpCUIT">CUIT</label>
                                <input id="EmpCUIT" class="form-control w330" placeholder="00-00000000-0" type="text" name="EmpCUIT" maxlength="13">
                            </div>
                            <!-- Emp Calle -->
                            <div class="form-inline mt-2">
                                <label for="EmpDomi" class="mr-2 w80">Calle</label>
                                <input type="text" name="EmpDomi" class="form-control w330" id="EmpDomi" maxlength="50">
                            </div>
                            <!-- Emp Direccion  -->
                            <div class="form-inline mt-2">
                                <!-- Emp Numero -->
                                <label for="EmpDoNu" class="mr-2 w80">Número</label>
                                <input type="number" name="EmpDoNu" class="form-control w70" placeholder="" id="EmpDoNu">
                                <!-- Emp Piso -->
                                <input type="number" name="EmpPiso" class="form-control mx-2" placeholder="Piso" id="EmpPiso" style="width: 76px;">
                                <!-- Emp Depto -->
                                <input type="text" name="EmpDpto" class="form-control w80 mr-2" placeholder="Depto" id="EmpDpto" maxlength="5">
                                <!-- Emp Cp -->
                                <input type="text" name="EmpCoPo" class="form-control w80" placeholder="CP" id="EmpCoPo" maxlength="8">
                            </div>
                            <!-- Emp Provincia -->
                            <div class="form-inline mt-2">
                                <label for="EmpProv" class="mr-2 w80">Provincia</label>
                                <select class="form-control selectjs_provinciasEmp w310" name="EmpProv" id="EmpProv">
                                </select>
                                <div id="trash_provEmp" class="btn btn-sm btn-link opa1 px-1"><?=imgIcon('trash3', 'Limpiar Selección ' ,'w15');?></div>
                            </div>
                            <!-- Emp Localidad -->
                            <div class="form-inline mt-2">
                                <label for="EmpLoca" class="mr-2 w80">Localidad</label>
                                <select class="form-control selectjs_localidadEmp w310" name="EmpLoca" id="EmpLoca">
                                </select>
                                <div id="trash_locaEmp" class="btn btn-sm btn-link opa1 px-1"><?=imgIcon('trash3', 'Limpiar Selección ' ,'w15');?></div>
                            </div>
                            <!-- Fin Domicilio -->
                            <!-- Emp Telefono -->
                            <div class="form-inline mt-2">
                                <label for="EmpTele" class="mr-2 w80">Teléfono</label>
                                <input type="tel" name="EmpTele" class="form-control w330" id="EmpTele" maxlength="15">
                            </div>
                            <!-- Emp Mail -->
                            <div class="form-inline mt-2">
                                <label for="EmpMail" class="mr-2 w80">E-Mail</label>
                                <input type="email" name="EmpMail" class="form-control w330" id="EmpMail" maxlength="100">
                            </div>
                            <!-- Emp Contacto -->
                            <div class="form-inline mt-2">
                                <label for="EmpCont" class="mr-2 w80">Contacto</label>
                                <input type="text" name="EmpCont" class="form-control w330" id="EmpCont" maxlength="100">
                            </div>
                            <!-- Emp Descripción -->
                            <div class="form-inline mt-2">
                                <label for="EmpObse" class="mr-2 w80">Observación</label>
                                <textarea id="EmpObse" class="form-control h70 w330 p-3" name="EmpObse" rows="3" maxlength="240"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-0">
                    <div class="form-group d-flex justify-content-end">
                        <button type="submit" class="mb-3 btn btn-sm text-white fontq <?= $bgcolor ?>" id="btnEmpresa">Aceptar</button>
                    </div>
                    <div id="espera"></div>
                    <div id="alerta_empresa" class="radius fontq alert m-0 d-none animate__animated animate__fadeIn w-100" role="alert">
                        <strong class="respuesta_empresa fw5"></strong>
                        <span class="mensaje_empresa fw4"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>