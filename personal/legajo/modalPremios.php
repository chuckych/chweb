<!-- Modal -->
<div class="modal fade" id="altapremios" data-backdrop="static" data-keyboard="true" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content w300 mx-auto">
            <div class="modal-header border-bottom-0">
                <h6 class="modal-title" id="staticBackdropLabel">Premio</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0">
                <form action="alta_opciones.php" method="post" class="form-premios">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" name="PERPREMI" value="PERPREMI">
                            <input type="hidden" name="LPreLega" value="<?=$_GET['_leg']?>">
                            <select class="form-control selectjs_premios w250" name="LPreCodi" id="LPreCodi">
                            </select>
                        </div>
                        <div class="col-12 my-3">
                            <div class="form-group d-flex justify-content-end">
                                <button type="submit" class="btn btn-sm text-white fontq <?= $bgcolor ?>" id="btnPremios">Aceptar</button>
                            </div>
                        </div>
                        <div id="alerta_premios" class="radius fontq alert m-0 d-none animate__animated animate__fadeIn w-100" role="alert">
                            <strong class="respuesta_premios fw5"></strong>
                            <span class="mensaje_premios fw4"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>