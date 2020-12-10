<!-- Modal -->
<div class="modal fade" id="altaconceptos" data-backdrop="static" data-keyboard="true" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content w300 mx-auto">
            <div class="modal-header border-bottom-0">
                <h6 class="modal-title" id="staticBackdropLabel">Asignar Concepto</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0">
                <form action="alta_opciones.php" method="post" class="form-otrosconleg">
                    <div class="row">
                        <div class="col-12 form-inline">
                            <label for="OTROConCodi" class="mr-2 w60" >Concepto</label>
                            <input type="hidden" name="OTROCONLEG" value="OTROCONLEG">
                            <input type="hidden" name="OTROConLega" value="<?=$_GET['_leg']?>">
                            <select class="form-control selectjs_conceptos w180" name="OTROConCodi" id="OTROConCodi">
                            </select>
                        </div>
                        <div class="col-12 mt-2 form-inline">
                            <label for="OTROConValor" class="mr-2 w60">Valor</label>
                            <input class="form-control w80" type="text" placeholder="0,00" name="OTROConValor" id="OTROConValor">
                        </div>
                        <div class="col-12 my-3">
                            <div class="form-group d-flex justify-content-end">
                                <button type="submit" class="btn btn-sm text-white fontq <?= $bgcolor ?>" id="btnconceptos">Aceptar</button>
                            </div>
                        </div>
                        <div id="alerta_conceptos" class="radius fontq alert m-0 d-none animate__animated animate__fadeIn w-100" role="alert">
                            <strong class="respuesta_conceptos fw5"></strong>
                            <span class="mensaje_conceptos fw4"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>