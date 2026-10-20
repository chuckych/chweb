<?php
$NovAlertaSeg = [
    "0" => "No cambiar",
    "1" => "Activar Alerta en identificador",
    "2" => "Quitar Alerta en identificador",
];
$mapNovRegl = [
    "0" => "No Calcular",
    "1" => "Calcular segun regla de calculo en dias laborales",
    "2" => "Calcular segun regla de calculo en cualquier dia",
];
$mapNovTiCo = [
    "0" => "Ninguno",
    "1" => "Franco Compensatorio (Resta en Cta. Cte de Horas)",
    "2" => "Jornada Reducida (Resta en Cta. Cte de Horas)",
    "3" => "Cuenta Corriente de Novedades",
    "4" => "Franco Compensatorio (Suma en Cta. Cte de Horas)",
    "5" => "Jornada Reducida (Suma en Cta. Cte de Horas)",
];
?>
<div class="w-100 rounded border p-3 mt-2">
    <div class="row p-2 px-3">
        <div class="col-12 p-0">
            <p class="font09">Control</p>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group row">
                <div class="custom-control custom-switch not-ausencia-permitted">
                    <input type="checkbox" class="custom-control-input" id="NovSusT" data-field="NovSusT">
                    <label class="custom-control-label" for="NovSusT">Suspender Tarjeta</label>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-8">
            <div class="form-group row">
                <div class="custom-control custom-switch not-ausencia-permitted">
                    <input type="checkbox" class="custom-control-input" id="NovVaca" data-field="NovVaca">
                    <label class="custom-control-label" for="NovVaca">
                        Descontar de días trab. para los días de vacaciones (Hasta 6 Meses de antig.)
                    </label>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group row">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="NovInsust" data-field="NovInsust">
                    <label class="custom-control-label" for="NovInsust">Insustituible</label>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-8">
            <div class="form-group row">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="NovInco" data-field="NovInco">
                    <label class="custom-control-label" for="NovInco">En control General reporte Inconsistencias</label>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group row">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="NovFeri" data-field="NovFeri">
                    <label class="custom-control-label" for="NovFeri">
                        Informar Feriados con esta novedad
                    </label>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-8">
            <div class="form-group row">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="NovGeSe" data-field="NovGeSe">
                    <label class="custom-control-label" for="NovGeSe">
                        Generar Novedad secundaria por la diferencia con las horas cargadas
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-4">
            <p class="font09 mb-2">Alerta de Seguridad</p>
            <div class="form-group">
                <?php foreach ($NovAlertaSeg as $key => $value): ?>
                    <div class="custom-control custom-switch">
                        <input type="radio" class="custom-control-input" name="NovAlertaSeg"
                            id="NovAlertaSeg<?php echo $key; ?>" data-field="NovAlertaSeg">
                        <label class="custom-control-label" for="NovAlertaSeg<?php echo $key; ?>">
                            <?php echo $value; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-12 col-md-8 p-0">
            <p class="font09 mb-2">Horas Como Presencia</p>
            <div class="form-group">
                <?php foreach ($mapNovRegl as $key => $value): ?>
                    <div class="custom-control custom-switch">
                        <input type="radio" class="custom-control-input" name="NovRegl" id="NovRegl<?php echo $key; ?>"
                            data-field="NovRegl">
                        <label class="custom-control-label" for="NovRegl<?php echo $key; ?>">
                            <?php echo $value; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="w-100 rounded border p-3 mt-3">
    <div class="row p-2 px-3">
        <div class="col-12 p-0">
            <p class="font09">Cuenta Corriente</p>
        </div>
        <div class="col-12 p-0 col-md-6">
            <div class="form-group mb-3" style="width:450px">
                <?php foreach ($mapNovTiCo as $key => $value): ?>
                    <div class="custom-control custom-switch">
                        <input type="radio" class="custom-control-input" name="NovTiCo" id="NovTiCo<?php echo $key; ?>"
                            data-field="NovTiCo">
                        <label class="custom-control-label" for="NovTiCo<?php echo $key; ?>">
                            <?php echo $value; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-12 p-0 col-md-6">
            <div class="form-group mb-2">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="NovLimCC" data-field="NovLimCC">
                    <label class="custom-control-label" for="NovLimCC">
                        Limitar ingreso de novedad segun Cta Cte
                    </label>
                </div>
            </div>
            <div class="d-flex flex-row gap8">
                <div class="form-group">
                    <label class="col-form-label w100">Cant. anual</label>
                    <div class="d-flex flex-row gap8">
                        <input type="number" maxlength="3" min="1" max="999" class="form-control w80" id="NovCant"
                            data-field="NovCant">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-form-label w200">Periodo de aplicación (Meses)</label>
                    <div class="d-flex flex-row gap8 align-items-center">
                        <input type="number" maxlength="2" class="form-control w80" id="NovCtaD" data-field="NovCtaD"
                            min="1" max="12">
                        <span class="gap5 font08">al</span>
                        <input type="number" maxlength="2" class="form-control w80" id="NovCtaH" data-field="NovCtaH"
                            min="1" max="12">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="w-100 rounded border p-3 mt-3">
    <div class="row p-2 px-3">
        <div class="col-12 p-0">
            <p class="font09">Al cargar la novedad en forma masiva</p>
        </div>
        <div class="col-12 col-md-6 p-0 mb-3">
            <div class="form-group">
                <label class="col-form-label fit-content">Cambiar por</label>
                <div class="d-flex flex-row gap8">
                    <select class="form-control" id="NovCarCodi" data-field="NovCarCodi">
                    </select>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group row gap8 align-items-center">
                <label class="col-form-label w150">Tope de carga</label>
                <div class="d-flex flex-row gap8">
                    <input type="number" maxlength="3" min="1" max="999" class="form-control w100" id="NovCarg"
                        data-field="NovCarg">
                </div>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="NovDispoManual" data-field="NovDispoManual">
                    <label class="custom-control-label" for="NovDispoManual">Disponible para carga manual</label>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group row gap8">
                <label class="col-form-label w150">A partir del día</label>
                <div class="d-flex flex-row gap8">
                    <input type="number" maxlength="2" min="1" max="31" class="form-control w100" id="NovCarDias"
                        data-field="NovCarDias">
                </div>
            </div>
        </div>
    </div>
</div>