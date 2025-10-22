<!-- Modal -->
<div class="modal fadeIn" id="altaseccion" data-backdrop="static" data-keyboard="true" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content w350 mx-auto">
            <div class="modal-header border-bottom-0">
                <h6 class="modal-title" id="staticBackdropLabel">Sección</h6>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0">
                <form action="alta_opciones.php" method="post" class="form-seccion">
                    <div class="row">
                        <div id="" class="col-12 mt-2">
                            <div class="form-group">
                                <input autofocus id="Se2Desc" class="form-control w-100 h40" placeholder="" type="text" name="Se2Desc">
                                <input id="SecCodi" type="hidden" name="SecCodi">
                                <input type="hidden" name="dato" value="alta_seccion">
                                <small id="SectorHelpBlock" class="form-text text-muted"></small>
                            </div>
                        </div>
                        <div class="col-12 my-3">
                            <div class="form-group d-flex justify-content-end">
                                <button type="submit" class="btn btn-sm text-white fontq <?= $bgcolor ?>" id="btnSec2">Aceptar</button>
                            </div>
                        </div>
                        <div id="espera"></div>
                        <div id="alerta_seccion" class="radius fontq alert m-0 d-none animate__animated animate__fadeIn w-100" role="alert">
                            <strong class="respuesta_seccion fw5"></strong>
                            <span class="mensaje_seccion fw4"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>