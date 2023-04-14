<!-- Modal -->
<div class="modal" id="altaNuevoLeg" data-backdrop="static" data-keyboard="true" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog w400">
        <div class="modal-content mx-auto">
            <form action="altaLeg.php" method="POST" class="form-NuevoLeg">
                <div class="modal-header border-bottom-0">
                    <h6 class="modal-title" id="staticBackdropLabel">Alta Legajo</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="LegNume" class="mr-2 w120">Legajo:</label>
                            <input class="form-control h40" type="tel" name="LegNume" id="LegNume" autofocus
                                placeholder="Número de legajo">
                            <input type="hidden" name="ALTALeg" value="true">
                        </div>
                        <div class="col-12 mt-2">
                            <label for="LegApNo" class="mr-2 w120">Nombre y Apellido</label>
                            <input class="form-control h40" type="text" name="LegApNo" id="LegApNo" maxlength="40"
                                placeholder="Nombre y Apellido">
                        </div>
                        <div class="col-12 mt-2">
                            <!-- Empresa -->
                            <label for="LegEmpr" class="mr-2">Empresa</label>
                            <select class="form-control h40" id="LegEmpr" name="LegEmpr">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn text-white fontq <?= $bgcolor ?>"
                        id="NuevoLeg">Aceptar</button>
                </div>

            </form>
        </div>
    </div>
</div>