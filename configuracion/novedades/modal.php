<div class="modal fadeIn" id="modal-novedad" tabindex="-1" role="dialog" aria-labelledby="modal-title">
    <div class="modal-dialog modal-xl modal-fullscreen-custom" role="document">
        <div class="modal-content p-2">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex flex-column w-100">
                    <div class="d-inline-flex w-100 align-items-start mb-2 justify-content-between">
                        <div class="d-inline-flex gap8" id="caracteristicas-container">
                            <div class="form-group">
                                <label for="NovCodi">Código</label>
                                <input type="number" disabled class="form-control w80" id="NovCodi" min="1" max="32768"
                                    maxlength="5" data-field="NovCodi">
                            </div>
                            <div class="form-group">
                                <label for="NovDesc">Descripción</label>
                                <input type="text" class="form-control w300" id="NovDesc" maxlength="40"
                                    data-field="NovDesc">
                            </div>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="bi bi-x-lg"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs font09">
                    <li class="nav-item pointer">
                        <a class="nav-link active" data-toggle="tab" data-target="#tabCarac">
                            Características
                        </a>
                    </li>
                    <li class="nav-item pointer">
                        <a class="nav-link" data-toggle="tab" data-target="#tabControles">
                            Controles
                        </a>
                    </li>
                    <li class="nav-item pointer">
                        <a class="nav-link" data-toggle="tab" data-target="#tabCausas">
                            Causas
                            <span id="tab-causas-count" class="p-1 px-2 bg-light ml-1 text-monospace rounded">0</span>
                        </a>
                    </li>
                    <li class="nav-item pointer">
                        <a class="nav-link" data-toggle="tab" data-target="#tabNovedadesAdicionales">
                            Novedades Adicionales
                            <span id="tab-novedad1-count" class="p-1 px-2 bg-light ml-1 text-monospace rounded">0</span>
                        </a>
                    </li>
                    <li class="nav-item pointer">
                        <a class="nav-link" data-toggle="tab" data-target="#tabNovedadesAdicionalesHoras">
                            Novedades Adicionales según horas
                            <span id="tab-horas-count" class="p-1 px-2 bg-light ml-1 text-monospace rounded">0</span>
                        </a>
                    </li>
                </ul>
                <!-- Contenido de las pestañas -->
                <div class="tab-content">
                    <div class="tab-pane fadeIn show active" role="tabpanel" id="tabCarac">
                        <!-- Contenido de la pestaña Características -->
                        <?php require 'tab-caracteristicas.php'; ?>
                    </div>
                    <div class="tab-pane fadeIn pt-2" role="tabpanel" id="tabControles">
                        <div class="overflow-auto w-100 h-100">
                            <!-- Contenido de la pestaña Controles -->
                            <?php require 'tab-controles.php'; ?>
                        </div>
                    </div>
                    <div class="tab-pane fadeIn" role="tabpanel" id="tabCausas">
                        <!-- Contenido de la pestaña Causas -->
                        <?php require 'tab-causas.php'; ?>
                    </div>
                    <div class="tab-pane fadeIn" role="tabpanel" id="tabNovedadesAdicionales">
                        <!-- Contenido de la pestaña Novedades Adicionales -->
                        <?php require 'tab-novedades-adicionales.php'; ?>
                    </div>
                    <div class="tab-pane fadeIn" role="tabpanel" id="tabNovedadesAdicionalesHoras">
                        <!-- Contenido de la pestaña Novedades Adicionales según horas -->
                        <?php require 'tab-novedades-adicionales-horas.php'; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="font09 pointer float-right btn btn-outline-secondary border"
                    data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-custom font09" id="btn-save">Guardar</button>
            </div>
        </div>
    </div>
</div>
<?php require 'dialog-causa.php'; ?>
<?php require 'dialog-novedad1.php'; ?>
<?php require 'dialog-horas.php'; ?>
<?php require 'dialog-nueva-novedad.php'; ?>
<?php require 'dialog-eliminar-novedad.php'; ?>