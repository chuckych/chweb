<!-- Modal -->
<div class="modal fade" id="altahistorial" data-backdrop="static" data-keyboard="true" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content w300 mx-auto">
            <div class="modal-header border-bottom-0">
                <h6 class="modal-title" id="staticBackdropLabel">Ingreso y Egreso</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0">
            <form action="alta_opciones.php" method="post" class="form-perineg">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-inline">
                                <label for="InEgFeIn" class="ml-2 w80">Ingreso</label>
                                <input autofocus class="form-control" type="date" name="InEgFeIn" id="InEgFeIn">
                                <input type="hidden" name="dato" value="alta_perineg">
                                <input type="hidden" name="InEgLega" value="<?=$_GET['_leg']?>">
                            </div>
                            <div class="form-inline mt-2">
                                <label for="InEgFeEg" class="ml-2 w80">Egreso</label>
                                <input class="form-control" type="date" name="InEgFeEg" id="InEgFeEg">
                            </div>
                            <div class="form-group mt-2">
                                <label for="InEgCaus" class="ml-2">Causa:</label>
                                <input class="form-control" type="text" name="InEgCaus" id="InEgCaus" maxlength="30">
                            </div>
                        </div>
                        <div class="col-12 my-3">
                            <div class="form-group d-flex justify-content-end">
                                <button type="submit" class="btn btn-sm text-white fontq <?= $bgcolor ?>" id="btnHisto">Aceptar</button>
                            </div>
                        </div>
                        <div id="alerta_historial" class="radius fontq alert m-0 d-none animate__animated animate__fadeIn w-100" role="alert">
                            <strong class="respuesta_historial fw5"></strong>
                            <span class="mensaje_historial fw4"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>