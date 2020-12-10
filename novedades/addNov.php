<form action="insertNov.php" method="post" class="alta_novedad w-100">
    <div class="row shadow-sm p-2 radius">
        <div class="col-12 col-sm-6">
            <div class="row">
                <div class="col-12">
                    <span class="float-left fontq py-2 fw5">Filtros: <span class="requerido">(*)</span></span>
                    <button type="button" class="float-right fontq btn btn-link text-decoration-none text-secondary" id="trash_allFilter">Limpiar Filtro</button>
                </div>
                <div class="col-12 d-none">
                    <div class="d-inline-flex mt-1 w-100">
                        <!-- Tipo personal -->
                        <div style="width:30%;" class="">
                            <select class="select2Tipo form-control" id="aTipo" name="aTipo">
                                <?php
                                foreach (TIPO_PER as $key => $value) {
                                    echo '<option value="' . $value . '">' . $key . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-inline-flex mt-1 w-100">
                        <!-- Empresa -->
                        <div style="width:50%;">
                            <select class="form-control sel_empresa w200" id="aEmp" name="aEmp">
                            </select>
                        </div>
                        <!-- PLanta -->
                        <div class="ml-1" style="width:50%;">
                            <select class="form-control sel_plantas w200" id="aPlan" name="aPlan">
                            </select>
                        </div>
                    </div>
                    <div class="d-inline-flex mt-1 w-100">
                        <!-- Sector -->
                        <div style="width:50%;">
                            <select class="form-control sel_sectores w200" id="aSect" name="aSect">
                            </select>
                        </div>
                        <!-- Seccion -->
                        <div class="ml-1" id="select_seccion" style="width:50%;">
                            <select class="form-control sel_seccion w200" id="aSec2" name="aSec2">
                            </select>
                        </div>
                    </div>
                    <div class="d-inline-flex mt-1 w-100">
                        <!-- Grupo -->
                        <div style="width:50%;">
                            <select class="form-control sel_grupos w200" id="aGrup" name="aGrup">
                            </select>
                        </div>
                        <!-- Sucursal -->
                        <div class="ml-1" style="width:50%;">
                            <select class="form-control sel_sucursal w200" id="aSucur" name="aSucur">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-2">
                    <!-- <span class="float-left fontq py-2 fw5">Ingresar:</span> -->
                    <hr>
                </div>
                <div class="col-12">
                    <div class="d-inline-flex mt-1 w-100">
                        <!-- Rango de Fecha -->
                        <div style="width:100%;">
                            <input type="text" readonly class="bg-white h40 fw5 form-control text-center ls1" name="_draddNov" id="_draddNov">
                        </div>
                    </div>
                    <div class="d-inline-flex mt-1 w-100">
                        <!-- Novedad -->
                        <div style="width:70%;">
                            <select class="form-control sel_novedad w200" id="aFicNove" name="aFicNove">
                            </select>
                        </div>
                        <!-- Horas -->
                        <div class="ml-1" style="width:30%;">
                            <input type="text" placeholder="00:00" name="aFicHoras" id="aFicHoras" class="HoraMask ls1 form-control text-center FicHoras h40" autocomplete="off">
                        </div>
                    </div>
                    <div class="d-inline-flex mt-1 w-100 d-none" id="select_causa">
                        <!-- Causa -->
                        <div style="width:100%;">
                            <select class="sel_causa form-control" id="aCaus" name="aCaus">
                            </select>
                        </div>
                    </div>
                    <div class="d-inline-flex mt-1 w-100">
                        <!-- Observacion -->
                        <div style="width:100%;">
                            <input type="text" placeholder="Observaci&oacute;n" class="form-control h40" name="aFicObse" id="aFicObse" maxlength="40">
                        </div>
                    </div>
                    <div class="mt-3 d-flex justify-content-between">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="aFicCate" id="aFicCate">
                            <label class="custom-control-label" for="aFicCate" style="padding-top:3px;">Novedad secundaria</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="aLaboral" id="aLaboral">
                            <label class="custom-control-label" for="aLaboral" style="padding-top:3px;">Cargar solo d&iacute;as laborales</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="aFicJust" id="aFicJust">
                            <label class="custom-control-label" for="aFicJust" style="padding-top:3px;">Justificada</label>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-3">
                    <span class="fontq py-2 fw5 mr-2">Ingresar Por:</span>
                    <div class="d-flex align-items-center pt-2">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-sm fontq btn btn-outline-light border w100 text-dark fw4 radius" id="TipoIngresoFiltros" data-toggle="tooltip" data-placement="bottom" data-html="true" title="" data-original-title="<span class='fw5 fontq'>Ingresar por Filtros<br>(Carga R&aacute;pida)</span>">
                                <input checked type="radio" name="TipoIngresos" id="TipoIngreso1"> Filtros
                            </label>
                            <label class="btn btn-sm fontq btn btn-outline-light border w100 text-dark fw4 radius" id="TipoIngresoFiltrosLegajos" data-toggle="tooltip" data-placement="bottom" data-html="true" title="" data-original-title="<span class='fw5 fontq'>Ingresar por Legajo<br>(Carga Lenta)</span>">
                                <input type="radio" name="TipoIngresos" id="TipoIngreso2"> Legajos
                            </label>
                            <input type="hidden" hidden id="TipoIngreso" name="TipoIngreso">
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-3 mt-lg-0 d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-custom btn-sm border fontq" id="CloseaddNov">
                        Cerrar
                    </button>
                    <button type="submit" class="ml-1 btn bg-custom btn-sm text-white fontq w100" id="submit"></button>
                </div>
                <div class="col-12 mt-3 d-flex justify-content-start">
                    <span class="fontq text-dark">Nota: <br>Para la carga por Filtros. al menos un filtro es obligatorio. Para la carga por Legajos, al menos un legajo es obligatorio.</span>
                </div>
                <input type="hidden" name="SelEmpresa" id="SelEmpresa">
                <input type="hidden" name="SelPlanta" id="SelPlanta">
                <input type="hidden" name="SelSector" id="SelSector">
                <input type="hidden" name="SelSeccion" id="SelSeccion">
                <input type="hidden" name="SelGrupo" id="SelGrupo">
                <input type="hidden" name="SelSucursal" id="SelSucursal">
                <input type="hidden" name="SelNovedad" id="SelNovedad">
                <input type="hidden" name="pagIni" id="pagIni">
                <input type="hidden" name="pagFin" id="pagFin">
                <input type="hidden" name="Cuenta" id="Cuenta">
            </div>
        </div>
        <div class="col-12 col-sm-6 table-responsive pt-2 d-none" id="divTablePers">
            <table class="table table-hover text-nowrap w-100 table-sm table-borderless" id="GetPers">
                <thead class="">
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" name="select_all" value="1" id="Personal-select-all" type="checkbox">
                                <label class="custom-control-label" for="Personal-select-all"></label>
                            </div>
                        </th>
                        <th>LEGAJO</th>
                        <th>NOMBRE</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="col-12 mt-4">
            <div id="respuesta" class="alert d-none fonth text-wrap">
                <div id="respuetatext" class="text-wrap fw4"></div>

            </div>
        </div>
    </div>
</form>