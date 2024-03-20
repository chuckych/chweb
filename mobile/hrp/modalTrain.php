<!-- Modal -->
<form action="" method="post" class="border px-3 pb-3 shadow-sm mt-1" id="formTrain">
    <div class="modal" id="modalTrain" tabindex="-1" aria-labelledby="modalTrainLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" style="min-height:500px">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <p class="modal-title h6" id="modalTrainLabel"></p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="bi bi-x-lg"></span>
                    </button>
                    <input type="hidden" name="selected" id="selectedPhoto">
                    <input type="hidden" name="userID" id="userPhoto">
                    <input type="hidden" name="type" id="typeEnroll">
                </div>
                <div class="modal-body pt-0">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border btn-sm fontq h40 w100 text-secondary"
                        data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-sm btn-custom fontq h40 w100" id="submitTrain">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</form>