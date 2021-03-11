<!-- Modal -->
<input type="hidden" id="tconcepto" value="0">
<form action="crud.php" method="post" id="FormConceptos">
<div class="modal fade" id="ConceptosModal" data-keyboard="false" tabindex="-1" tabindex="-1" aria-labelledby="ConceptosModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title text-secondary" id="ConceptosModal">Conceptos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="bi bi2 bi-x"></i>
                </button>
            </div>
            <div class="modal-body mt-n3">
                <div class="row">
                    <div class="col-12 table-responsive" id="divGetConceptos">
                        <table id="GetConceptos" class="table text-nowrap w-100">
                            <thead>
                                <th>Presente</th>
                                <th>Ausente</th>
                                <th>Cod</th>
                                <th class="">Descripci&oacute;n</th>
                                <th class="">ID</th>
                                <th class=""></th>
                                <th class=""></th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
            <span class="respuesta mr-2 fontq"></span>
                <button type="button" class="btn btn-sm fontq btn-outline-secondary border" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-sm fontq btn-custom" id="btnsubmit">Guardar</button>
            </div>
        </div>
    </div>
</div>
</form>