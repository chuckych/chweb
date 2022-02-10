<!-- Modal -->
<div class="modal fade" id="altaidentifica" data-backdrop="static" data-keyboard="true" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog w350">
        <div class="modal-content mx-auto">
            <div class="modal-header border-bottom-0">
                <h6 class="modal-title" id="staticBackdropLabel">Identificador</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0">
                <form action="alta_opciones.php" method="post" class="form-Identifica">
                    <div class="row">
                        <div class="col-12 form-inline">
                            <label for="IDCodigo" class="mr-2 w100">Identificador</label>
                            <input autofocus class="form-control w180" type="tel" name="IDCodigo" id="IDCodigo" placeholder="Identificador">
                            <input type="hidden" name="IDENTIFICA" value="IDENTIFICA">
                            <input type="hidden" name="IDLegajo" value="<?= $_GET['_leg'] ?>">
                        </div>
                        <div class="col-12 form-inline mt-2">
                            <label for="IDTarjeta" class="mr-2 w100">Tarjeta</label>
                            <input class="form-control w180" type="tel" name="IDTarjeta" id="IDTarjeta" placeholder="N° de Tarjeta">
                        </div>
                        <div class="col-12 form-inline mt-2">
                            <div class="d-inline-flex">
                                <label for="IDVence" class="mr-2 w100">Vencimiento</label>
                                <input class="form-control w180" type="text" name="IDVence" id="IDVence" placeholder="Fecha vencimiento">
                                <span id="trash_IDVence" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Borrar ', 'w15'); ?></span>
                            </div>
                        </div>
                        <div class="col-12 fontq mt-3 mb-2">
                            <div class="border p-3 bg-light">
                                <label for="" class="fontq">Habilitar en Dispositivos</label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" id="IDCap04" name="IDCap04" class="custom-control-input" checked>
                                    <label class="custom-control-label" style="padding-top:6px;" for="IDCap04">ZKTeco</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" id="IDCap05" name="IDCap05" class="custom-control-input">
                                    <label class="custom-control-label" style="padding-top:6px;" for="IDCap05">Suprema</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" id="IDCap06" name="IDCap06" class="custom-control-input">
                                    <label class="custom-control-label" style="padding-top:6px;" for="IDCap06">HIKVision</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" id="IDCap01" name="IDCap01" class="custom-control-input">
                                    <label class="custom-control-label" style="padding-top:6px;" for="IDCap01">Macronet</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" id="IDCap03" name="IDCap03" class="custom-control-input">
                                    <label class="custom-control-label" style="padding-top:6px;" for="IDCap03">Silycon Bayres</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 my-3">
                            <div class="form-group d-flex justify-content-end">
                                <button type="submit" class="btn btn-sm text-white fontq <?= $bgcolor ?>" id="btnidentifica">Aceptar</button>
                            </div>
                        </div>
                        <div id="alerta_identifica" class="radius fontq alert m-0 d-none animate__animated animate__fadeIn w-100" role="alert">
                            <strong class="respuesta_identifica fw5"></strong>
                            <span class="mensaje_identifica fw4"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>