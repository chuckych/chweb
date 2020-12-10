<!-- Modal -->
<div class="modal" id="altaNuevoLeg" data-backdrop="static" data-keyboard="true" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog w400">
        <div class="modal-content mx-auto">
            <div class="modal-header border-bottom-0">
                <h6 class="modal-title" id="staticBackdropLabel">Alta Legajo</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0">
                <form action="altaLeg.php" method="POST" class="form-NuevoLeg">
                    <div class="row">
                        <div class="col-12 form-inline">
                            <label for="LegNume" class="mr-2 w120">Legajo:</label>
                            <input class="form-control w120" type="number" name="LegNume" id="LegNume" autofocus>
                            <input type="hidden" name="ALTALeg" value="true">
                        </div>
                        <div class="col-12 mt-2 form-inline">
                            <label for="LegApNo" class="mr-2 w120">Nombre y Apellido</label>
                            <input class="form-control w230" type="text" name="LegApNo" id="LegApNo" maxlength="40">
                        </div>
                        <div class="col-12 my-3">
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-sm text-white fontq <?= $bgcolor ?>" id="NuevoLeg">Aceptar</button>
                            </div>
                        </div>
                        <div id="alerta_AltaLega" class="fontq text-right d-none mt-2 col-12 p-2">
                            <span class="p-2 respuesta_AltaLega fw5 align-middle mr-2 mb-4"></span>
                            <br /><span class="mensaje_AltaLega"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>