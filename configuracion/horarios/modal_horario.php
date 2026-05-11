<?php
$mapDays = [
    'HorLune' => 'Lunes',
    'HorMart' => 'Martes',
    'HorMier' => 'Miércoles',
    'HorJuev' => 'Jueves',
    'HorVier' => 'Viernes',
    'HorSaba' => 'Sábado',
    'HorDomi' => 'Domingo',
    'HorFeri' => 'Feriado',
];

$mapListaLaboralFeriado = [
    0 => 'No Laboral',
    1 => 'Es Laboral',
    2 => 'Según Día',
    3 => 'Según Día con Horario',
];

$daysConfig = [];
foreach ($mapDays as $key => $day) {
    $daysConfig[] = [
        'key' => $key,
        'label' => $day,
        'shortId' => substr($key, 3, 2),
        'prefix' => substr($key, 0, 5),
    ];
}
?>
<div class="modal fadeIn" id="modal-horario" tabindex="-1" role="dialog" aria-labelledby="modal-title"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex flex-column w-100">
                    <div class="d-inline-flex w-100 align-items-center mb-2 justify-content-between">
                        <p class="modal-title">Editar Horario</p>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="bi bi-x-lg"></span>
                        </button>
                    </div>
                    <div class="config-card w-100">
                        <!-- Header Row -->
                        <div class="row g-3 header-inputs align-items-end">
                            <div class="col-md-2">
                                <label class="form-label-sm">Código</label>
                                <input type="number" class="form-control" id="HorCodi" value="21" min="0" max="32767"
                                    step="1" inputmode="numeric" pattern="[0-9]*">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label-sm">Descripción</label>
                                <input type="text" class="form-control" id="HorDesc"
                                    value="22:00 a 07:00 L a V Nocheros">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-sm">ID</label>
                                <input type="text" class="form-control" id="HorID" value="21" maxlength="3"
                                    pattern="[A-Za-z0-9]{0,3}" inputmode="text" autocomplete="off">
                            </div>
                            <div class="col-md-3 text-end">
                                <label class="form-label-sm d-block text-start">Color</label>
                                <input type="text" class="form-control" id="HorColor" value="#000000" data-jscolor="{}"
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <div class="btn-group-toggle mt-n2 p-1 border radius fit-content shadow-sm"
                            data-toggle="buttons">
                            <?php
                            foreach ($daysConfig as $dayCfg) {
                                $shortId = htmlspecialchars($dayCfg['shortId'], ENT_QUOTES, 'UTF-8');
                                ?>
                                <label class="btn btn-sm font08 btn-outline-custom border-0">
                                    <input type="checkbox" id="<?= $shortId ?>"> <?= $shortId ?>
                                </label>
                                <?php
                            } ?>
                        </div>
                    </div>
                    <div class="pb-1">&nbsp;</div>
                </div>
            </div>
            <div class="modal-body mt-n4">
                <?php
                $laboral = '';
                foreach ($daysConfig as $dayCfg) {
                    $laboral = '';
                    $dayCfgID = htmlspecialchars($dayCfg['key'], ENT_QUOTES, 'UTF-8');
                    $dayCfgLabel = htmlspecialchars($dayCfg['label'], ENT_QUOTES, 'UTF-8');
                    $prefix = htmlspecialchars($dayCfg['prefix'], ENT_QUOTES, 'UTF-8');
                    if ($dayCfgID === 'HorFeri') {
                        $laboral .= "<select class='form-control form-control-sm w200' id='{$dayCfgID}'>";
                        foreach ($mapListaLaboralFeriado as $value => $text) {
                            $laboral .= "<option value='{$value}'>{$text}</option>";
                        }
                        $laboral .= "</select>";
                    } else {
                        $laboral .= "<div class=\"custom-control custom-switch custom-control-inline ml-1 d-flex align-items-center\">";
                        $laboral .= "<input type=\"checkbox\" class=\"custom-control-input\" id=\"{$dayCfgID}\">";
                        $laboral .= "<label class=\"custom-control-label\" for=\"{$dayCfgID}\" style=\"padding-top: 3px;\">";
                        $laboral .= "<span>Es laboral</span>";
                        $laboral .= "</label>";
                        $laboral .= "</div>";
                    }
                    ?>
                    <div class="config-card">
                        <div class="row align-items-center mb-3">
                            <div class="col-1">
                                <span class="day-label"><?= $dayCfgLabel ?></span>
                            </div>
                            <div class="col-11">
                                <div class="d-flex justify-content-between">
                                    <?= $laboral ?>
                                    <div class="hint--left" aria-label="Copiar a todos los días">
                                        <i class="bi bi-copy btn btn-sm  btn-outline-custom border-0 btn-copy-day"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label-sm">Desde</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-time" placeholder="10:00"
                                        id="<?= $prefix ?>De">
                                    <!-- <span class="input-group-text"><i class="bi bi-clock"></i></span> -->
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-sm">Hasta</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-time" placeholder="07:00"
                                        id="<?= $prefix ?>Ha">
                                    <!-- <span class="input-group-text"><i class="bi bi-clock"></i></span> -->
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-sm">Descanso</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-time" placeholder="12:00"
                                        id="<?= $prefix ?>Re">
                                    <!-- <span class="input-group-text"><i class="bi bi-clock"></i></span> -->
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-sm">Horas</label>
                                <input type="text" class="form-control" placeholder="09:00" id="<?= $prefix ?>Hs">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-sm">Límite (%):</label>
                                <input type="number" class="form-control" placeholder="80" value="80" min="0" max="99"
                                    step="1" inputmode="numeric" id="<?= $prefix ?>Li">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="timeline-container">
                                    <!-- Hours labels and bars would go here -->
                                    <div class="timeline-bar" style="left: 0%; width: 37.5%;"></div>
                                    <!-- 22:00 to 07:00 representation -->
                                    <div class="timeline-bar" style="right: 0%; width: 8%;"></div>
                                    <!-- Rendering simple representation of the 24h grid -->
                                    <div class="timeline-hour"><span>0</span></div>
                                    <div class="timeline-hour"><span>3</span></div>
                                    <div class="timeline-hour"><span>6</span></div>
                                    <div class="timeline-hour"><span>9</span></div>
                                    <div class="timeline-hour"><span>12</span></div>
                                    <div class="timeline-hour"><span>15</span></div>
                                    <div class="timeline-hour"><span>18</span></div>
                                    <div class="timeline-hour"><span>21</span></div>
                                    <div class="timeline-hour"><span>24</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="font09 pointer float-right btn btn-outline-secondary border"
                    data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-custom font09" id="btn-save">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fadeIn" id="modal-confirm-delete-horario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Confirmar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="bi bi-x-lg"></span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="confirm-delete-horario-text">¿Confirma eliminar este horario?</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="font09 pointer float-right btn btn-outline-secondary border" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger font09" id="btn-confirm-delete-horario">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fadeIn" id="modal-importar-horarios" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Importar horarios</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="bi bi-x-lg"></span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-2 font09">Descargue el archivo de ejemplo para completar los horarios con el formato correcto.</p>
                <div class="form-group mb-2">
                    <label for="import-horarios-file" class="font08 mb-1">Archivo a importar</label>
                    <input type="file" class="form-control-file" id="import-horarios-file" accept=".xls,.xlsx">
                    <small class="form-text text-muted">Solo se permiten archivos .xls o .xlsx válidos.</small>
                </div>
                <div id="import-horarios-alert" class="alert alert-warning d-none mb-0" role="alert">
                    <div class="font-weight-bold mb-1">Advertencias encontradas antes de importar</div>
                    <ul class="mb-0 pl-3 font08" id="import-horarios-alert-list"></ul>
                </div>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-between">
                <button type="button" class="font09 pointer btn btn-outline-secondary border" data-dismiss="modal">Cerrar</button>
                <div class="d-flex align-items-center" style="gap:8px;">
                    <button type="button" class="btn btn-outline-custom font09 border" id="btn-descargar-ejemplo-importar-xls">
                        <i class="bi bi-file-earmark-arrow-down"></i> Descargar ejemplo
                    </button>
                    <button type="button" class="btn btn-custom font09" id="btn-importar-xls-modal">
                        <i class="bi bi-upload"></i> Importar XLS
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>