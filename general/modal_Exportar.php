<!-- Modal -->
<div class="modal fade" id="Exportar" tabindex="-1" aria-labelledby="ExportarLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body">
                <form action="reporte/index.php" method="POST" id="FormExportar">
                    <div class="row">
                        <div class="col-12 fontq">
                            <span class="text-dark">Rango de Fecha a Exportar: </span><b><span class="text-dark ls1" id="RangoDr"></span></b><br /><span class="fontp text-secondary">M&aacute;ximo 31 días</span>
                            <span class="float-right text-dark fontq btn btn-outline-custom btn-sm border" id="FiltroReporte">Filtros</span>
                        </div>
                        <div class="col-12 form-inline mt-1 mt-0">
                            <label class="" for="_plantilla"><span class="w80 d-none d-sm-none d-md-none d-lg-block">Plantilla:</span> </label>
                            <span class="w200">
                                <select name="" id="_plantilla" class="select2Plantilla form-control">
                                    <?php
                                    foreach (PLANTILLAS as $key => $value) {
                                        echo '<option value="' . $value . '">' . $key . '</option>';
                                    }
                                    ?>
                                </select></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <a class="btn btn-link text-decoration-none text-dark fontq px-0" data-toggle="collapse" href="#Permisos" role="button" aria-expanded="false" aria-controls="Permisos">
                                <span id="btnPermiso">Opciones del Reporte</span>
                                <span class="ml-1">
                                    <svg class="bi mr-1" width="10" height="10" fill="currentColor">
                                        <use xlink:href="../img/bootstrap-icons.svg#chevron-down" />
                                    </svg>
                                </span>
                            </a>
                        </div>
                        <div class="collapse" id="Permisos">
                            <div class="col-12 form-inline pt-2">
                                <label for="_format"><span class="w80 d-none d-sm-none d-md-none d-lg-block">Hoja: </span></label>
                                <select name="_format" id="_format" class="select2 form-control">
                                    <?php
                                    foreach (TIPO_HOJA as $key => $value) {
                                        echo '<option value="' . $value . '">' . $key . '</option>';
                                    }
                                    ?>
                                </select>
                                <span class="ml-1"></span>
                                <select name="_orientation" id="_orientation" class="select2 form-control w150">
                                    <?php
                                    foreach (ORIENTACION as $key => $value) {
                                        echo '<option value="' . $value . '">' . $key . '</option>';
                                    }
                                    ?>
                                </select>
                                <span class="ml-sm-1 mt-sm-0 mt-1">
                                    <select name="_destino" id="_destino" class="select2 form-control w250">
                                        <?php
                                        foreach (DESTINO as $key => $value) {
                                            echo '<option value="' . $value . '">' . $key . '</option>';
                                        }
                                        ?>
                                    </select>
                                </span>
                            </div>
                            <div class="col-12 form-inline pt-1">
                                <label for="_format"><span class="w80 d-none d-sm-block">Nombre: </span></label>
                                <input class="form-control w250 h40" type="text" name="_nombre" id="_nombre" placeholder="Nombre del archivo. (Opcional)">
                            </div>
                            <div class="col-12 form-inline pt-1">
                                <label for="_format"><span class="w80 d-none d-sm-block">T&iacute;tulo:: </span></label>
                                <input class="form-control w250 h40" type="text" name="_titulo" id="_titulo" placeholder="T&iacute;tulo del Reporte. (Opcional)">
                            </div>
                            <div class="col-12 pt-2 d-none">
                                <span class="fontq">Bloquear:</span>
                                <div class="custom-control custom-switch custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" id="_print" name="_print" value="print">
                                    <label class="custom-control-label" for="_print" style="padding-top: 3px;">
                                        <span id="VerPor" data-toggle="tooltip" data-placement="top" data-html="true" title="" data-original-title="<b>Incluye valores en cero.</b>" aria-describedby="tooltip">Imprimir</span>
                                    </label>
                                </div>
                                <div class="custom-control custom-switch custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" id="_annotforms" name="_annotforms" value="annot-forms">
                                    <label class="custom-control-label" for="_annotforms" style="padding-top: 3px;">Comentarios</label>
                                </div>
                                <div class="custom-control custom-switch custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" id="_copy" name="_copy" value="copy">
                                    <label class="custom-control-label" for="_copy" style="padding-top: 3px;">Copiar</label>
                                </div>
                            </div>
                            <div class="col-12 mt-2 form-inline d-none">
                                <label class="mr-2" for="_password">Contrase&ntilde;a de apertura: </label>
                                <input type="text" class="form-control w200" maxlength="10" name="_password">
                            </div>
                            <div class="col-12 pt-1 form-inline">
                                <label for="_watermark"><span class="mr-2 d-none d-sm-block">Marca de agua: </span></label>
                                <input type="text" class="form-control w200 h40" name="_watermark" placeholder="Marca de agua">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="pt-2 col-12">
                            <div class="custom-control custom-switch custom-control-inline ml-1">
                                <input type="checkbox" class="custom-control-input" id="SaltoPag">
                                <label class="custom-control-label w180" for="SaltoPag" style="padding-top: 3px;"><span class="text-dark">Salto de p&aacute;gina</span></label>
                                <input type="hidden" name="_SaltoPag" id="datoSaltoPag">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="pt-2 col-12">
                            <div class="custom-control custom-switch custom-control-inline ml-1">
                                <input type="checkbox" class="custom-control-input" id="VerHoras">
                                <label class="custom-control-label w180" for="VerHoras" style="padding-top: 3px;"><span class="text-dark">Mostrar Horas</span></label>
                                <input type="hidden" name="_VerHoras" id="datoVerHoras">
                            </div>
                            <div class="custom-control custom-switch custom-control-inline ml-1">
                                <input type="checkbox" class="custom-control-input" id="VerNove">
                                <label class="custom-control-label w180" for="VerNove" style="padding-top: 3px;"><span class="text-dark">Mostrar Novedades</span></label>
                                <input type="hidden" name="_VerNove" id="datoVerNove">
                            </div>
                            <div class="custom-control custom-switch custom-control-inline ml-1">
                                <input type="checkbox" class="custom-control-input" id="VerFic">
                                <label class="custom-control-label w180" for="VerFic" style="padding-top: 3px;" title="Visualiza primer y &uacute;ltima salida"><span class="text-dark">Mostrar Fichadas</span></label>
                                <input type="hidden" name="_VerFic" id="datoVerFic">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="pt-2 col-12">
                            <div class="custom-control custom-switch custom-control-inline ml-1">
                                <input type="checkbox" class="custom-control-input" id="TotHoras">
                                <label class="custom-control-label w180" for="TotHoras" style="padding-top: 3px;"><span class="text-dark">Total Horas por Columna</span></label>
                                <input type="hidden" name="_TotHoras" id="datoTotHoras">
                            </div>
                            <div class="custom-control custom-switch custom-control-inline ml-1">
                                <input type="checkbox" class="custom-control-input" id="TotNove">
                                <label class="custom-control-label w180" for="TotNove" style="padding-top: 3px;"><span class="text-dark">Resumen por Novedad</span></label>
                                <input type="hidden" name="_TotNove" id="datoTotNove">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="pt-2 col-12">
                            <button class="btn btn-custom btn-sm fontq px-3 float-right btn-mobile" type="submit" id="btnExportar">Generar PDF</button>
                        </div>
                    </div>
                </form>
                <div class="row d-none" id="IFrame">

                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-custom btn-sm fontq border" data-dismiss="modal">Cerrar</button>
            </div>
        </div>

    </div>
</div>