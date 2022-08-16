<form action="" method="post" autocomplete="off" id="tarForm">
    <div class="modal modal-blur fade" id="tarModal" tabindex="-1" aria-labelledby="tarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row divEditar">
                        <div class="col-sm-6 col-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">Responsable</label>
                                <select type="text" class="form-control" name="TareResp" id="TareResp"></select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">Proyecto</label>
                                <select type="text" class="form-control" name="TareProy" id="TareProy"></select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">Proceso</label>
                                <select type="text" class="form-control" name="TareProc" id="TareProc"></select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">Plano</label>
                                <select type="text" class="form-control" name="TarePlano" id="TarePlano"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-12 mb-3 divIni">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Fecha Inicio</label>
                                        <input type="text" class="form-control date text-center bg-white" name="TareFechaIni" id="TareFechaIni" readonly>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Hora Inicio</label>
                                        <input type="tel" class="form-control HoraMask text-center" name="TareHoraIni" id="TareHoraIni" placeholder="00:00">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12 mb-3 divFin">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Fecha Fin</label>
                                        <input type="text" class="form-control date text-center bg-white" name="TareFechaFin" id="TareFechaFin" readonly>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Hora Fin</label>
                                        <input type="tel" class="form-control HoraMask text-center" name="TareHoraFin" id="TareHoraFin" placeholder="00:00">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto btn-light h50" data-bs-dismiss="modal">Cerrar</button>
                    <div class="tarSubmit">
                        <button type="submit" class="btn btn-teal h50" id="ediTarSubmit"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="op/js/selectTar.js?<?= vjs() ?>"></script>