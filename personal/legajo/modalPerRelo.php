<!-- Modal -->
<div class="modal fade" id="altaPerRelo" data-backdrop="static" data-keyboard="true" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdropLabelPerRelo" aria-hidden="true">
    <div class="modal-dialog w350">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h6 class="modal-title" id="staticBackdropLabelPerRelo">Dispositivo</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0">
                <form action="alta_opciones.php" method="post" class="form-PerRelo">
                    <div class="row">
                        <div class="col-12 form-inline">
                            <label for="ReloMarca" class="mr-2 w80">Dispositivo</label>
                            <select class="form-control selectjs_Relojes w200" name="ReloMarca" id="ReloMarca">
                            </select>
                            <input type="hidden" name="PERRELO" value="true">
                            <input type="hidden" name="RelLega" value="<?=$_GET['_leg']?>">
                        </div>
                        <div class="col-12 form-inline mt-2">
                            <label for="" class="mr-2 w80">Desde el: </label>
                            <input class="form-control w200" value="<?=date('Y-m-d')?>" type="date" name="RelFech">
                        </div>
                        <div class="col-12 form-inline mt-2">
                            <label for="" class="mr-2 w80">Vencimiento: </label>
                            <input class="form-control w200" type="date" name="RelFech2">
                        </div>
                        <div class="col-12 my-3">
                            <div class="form-PerRelo d-flex justify-content-end">
                                <button type="submit" class="btn btn-sm text-white fontq <?= $bgcolor ?>"
                                    id="btnPerRelo">Aceptar</button>
                            </div>
                        </div>
                        <div id="alerta_PerRelo"
                            class="radius fontq alert m-0 d-none animate__animated animate__fadeIn w-100" role="alert">
                            <strong class="respuesta_PerRelo fw5"></strong>
                            <span class="mensaje_PerRelo fw4"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>