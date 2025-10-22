<!-- Modal -->
<div class="modal fadeIn" id="altahorarioal" data-backdrop="static" data-keyboard="true" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content mx-auto">
            <div class="modal-header border-bottom-0">
                <h6 class="modal-title" id="staticBackdropLabel">Horario Alternativo</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0">
                <form action="alta_opciones.php" method="post" class="form-PerHoAl">
                    <div class="row">
                        <div class="col-12 form-inline">
                            <label for="LegHoAl" class="mr-2 w60">Horario</label>
                            <select class="form-control selectjs_horarioal w350" name="LegHoAl" id="LegHoAl">
                            </select>
                            <input type="hidden" name="PERHOAL" value="PERHOAL">
                            <input type="hidden" name="LeHALega" value="<?=$_GET['_leg']?>">
                        </div>
                        <div class="col-12 my-3">
                            <div class="form-group d-flex justify-content-end">
                                <button type="submit" class="btn btn-sm text-white fontq <?= $bgcolor ?>" id="btnhorarioal">Aceptar</button>
                            </div>
                        </div>
                        <div id="alerta_horarioal" class="radius fontq alert m-0 d-none animate__animated animate__fadeIn w-100" role="alert">
                            <strong class="respuesta_horarioal fw5"></strong>
                            <span class="mensaje_horarioal fw4"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>