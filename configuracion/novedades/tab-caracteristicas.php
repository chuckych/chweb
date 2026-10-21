<?php
$tipos = [
    "0" => "Llegada tarde",
    "1" => "Incumplimiento",
    "2" => "Salida anticipada",
    "3" => "Ausencia",
    "4" => "Licencia",
    "5" => "Accidente",
    "6" => "Vacaciones",
    "7" => "Suspensión",
    "8" => "ART"
];
?>

<div class="w-100 rounded border p-3 mt-3">
    <div class="row">
        <div class="col-12 col-md-3">
            <div class="form-group">
                <label for="NovTipo" class="col-form-label">Tipo</label>
                <select type="text" class="form-control" id="NovTipo" data-field="NovTipo">
                    <?php foreach ($tipos as $key => $value): ?>
                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-12 col-md-2">
            <div class="form-group">
                <label for="NovID" class="col-form-label">ID</label>
                <input type="text" maxlength="3" class="form-control" id="NovID" data-field="NovID">
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="form-group">
                <label for="NovColor" class="col-form-label">Color</label>
                <input type="text" class="form-control" id="NovColor" data-field="Color" value="#333333"
                    data-jscolor="{}" autocomplete="off">
            </div>
        </div>
        <div class="col-12 col-md-6"></div>
    </div>
</div>
<div class="row p-2 px-3 gap-8">
    <div class="w-100 rounded border p-3 mt-3">
        <div class="row p-2 px-3">
            <div class="col-12 p-0">
                <p class="font09">Codigos de Liquidación</p>
            </div>
            <div class="col-12">
                <div class="form-group row">
                    <label class="col-form-label w150">Para Mensuales</label>
                    <div class="d-flex flex-row gap8 align-items-center">
                        <input type="text" maxlength="10" class="form-control w80" id="NovCoL1" data-field="NovCoL1">
                        <input type="text" maxlength="10" class="form-control w80" id="NovCoL2" data-field="NovCoL2">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="NovInfM" data-field="NovInfM">
                            <label class="custom-control-label d-none d-sm-block" for="NovInfM">Informar en Horas</label>
                            <label class="custom-control-label d-block d-sm-none" for="NovInfM">En Horas</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label w150">Para Jornales</label>
                    <div class="d-flex flex-row gap8 align-items-center">
                        <input type="text" maxlength="10" class="form-control w80" id="NovCoL3" data-field="NovCoL3">
                        <input type="text" maxlength="10" class="form-control w80" id="NovCoL4" data-field="NovCoL4">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="NovInfJ" data-field="NovInfJ">
                            <label class="custom-control-label d-none d-sm-block" for="NovInfJ">Informar en Horas</label>
                            <label class="custom-control-label d-block d-sm-none" for="NovInfJ">En Horas</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>