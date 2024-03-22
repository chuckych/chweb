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
                            <label class="mr-2">Legajo:
                                <input class="form-control h40 mt-2" type="tel" name="LegNume" id="LegNume" autofocus
                                    placeholder="Número de legajo"></label>
                            <input type="hidden" name="ALTALeg" value="true">
                        </div>
                        <div class="col-12 mt-2">
                            <label class="mr-2 w-100">Apellido y Nombres
                                <input class="form-control h40 mt-2 w-100" type="text" name="LegApNo" id="LegApNo"
                                    maxlength="40" placeholder="Apellido y Nombres"></label>
                        </div>
                        <div class="col-12 mt-2">
                            <!-- Empresa -->
                            <label class="mr-2 w-100">
                                <div class="mb-2">Empresa</div>
                                <select class="form-control h40 mt-2" id="LegEmpr" name="LegEmpr">
                                </select>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn text-white fontq <?= $bgcolor ?>" id="NuevoLeg">Aceptar</button>
                </div>

            </form>
        </div>
    </div>
</div>