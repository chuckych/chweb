<!-- Modal -->
<div class="modal fade" id="altahistorial" data-backdrop="static" data-keyboard="true" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content w300 mx-auto">
            <form action="alta_opciones.php" method="post" class="form-perineg">
                <div class="modal-header border-bottom-0">
                    <h6 class="modal-title" id="staticBackdropLabel">Ingreso y Egreso</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="InEgFeIn" class="">Ingreso</label>
                            <div class="d-inline-flex w-100 align-items-center">
                                <input class="form-control h50 text-center" type="text" name="InEgFeIn" id="InEgFeIn"
                                    placeholder="Fecha de ingreso">
                                <span id="trash_InEgFeIn"
                                    class="btn btn-sm btn-link opa5 h50 d-flex align-items-center"><i
                                        class="bi bi-eraser "></i></span>
                            </div>
                            <input type="hidden" name="dato" value="alta_perineg">
                            <input type="hidden" name="InEgLega" value="<?= $_GET['_leg'] ?>">
                            <label for="InEgFeEg" class="mt-2">Egreso</label>
                            <div class="d-inline-flex w-100 align-items-center">
                                <input class="form-control h50 text-center" type="text" name="InEgFeEg" id="InEgFeEg"
                                    placeholder="Fecha de egreso">
                                <span id="trash_InEgFeEg"
                                    class="btn btn-sm btn-link opa5 h50 d-flex align-items-center"><i
                                        class="bi bi-eraser "></i></span>
                            </div>
                            <div class="form-group mt-2">
                                <label for="InEgCaus" class="">Causa:</label>
                                <input class="form-control h50" type="text" name="InEgCaus" id="InEgCaus"
                                    placeholder="Causa" maxlength="30">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn px-3 text-white fontq <?= $bgcolor ?>"
                        id="btnHisto">Aceptar</button>
                    <button type="submit" style="display: none" class="btn px-3 text-white fontq <?= $bgcolor ?>"
                        id="btnHisto2">Modificar</button>
                </div>
            </form>
        </div>
    </div>
</div>