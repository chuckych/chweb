<div class="tab-pane fade" id="empresa" role="tabpanel" aria-labelledby="empresa-tab">
    <div class="row m-0 border border-top-0 overflow-auto" style="max-height: 450px;min-height: 450px;">
        <div class="col-12 py-3">
            <div class="form-inline mt-2">
                <!-- Empresa -->
                <label for="LegEmpr" class="mr-2 w80 ReqLegEmpr">Empresa <span class="requerido ml-1"></span></label>
                <select class="form-control selectjs_empresas w200" id="LegEmpr" name="LegEmpr">
                </select>
                <span data-toggle="modal" data-target="#altaEmpresa" class="pointer">
                    <button type="button" class="ml-1 btn btn-sm btn-light" data-toggle="tooltip" data-placement="right" title="CREAR" style="z-index: 1;">
                        +
                    </button>
                </span>
                <span id="trash_emp" class="trash"></span>
            </div>
            <div class="form-inline mt-2">
                <!-- Planta -->
                <label for="LegPlan" class="mr-2 w80">Planta</label>
                <select class="form-control selectjs_plantas w200" id="LegPlan" name="LegPlan">
                </select>
                <span data-toggle="modal" data-target="#altaPlanta" class="pointer">
                    <button type="button" class="ml-1 btn btn-sm btn-light" data-toggle="tooltip" data-placement="right" title="CREAR">
                        +
                    </button>
                </span>
                <span id="trash_plan" class="trash"></span>
            </div>
            <div class="form-inline mt-2">
                <!-- Convenio -->
                <label for="LegConv" class="mr-2 w80">Convenio</label>
                <select class="form-control selectjs_convenio w200" id="LegConv" name="LegConv">
                </select>
                <span data-toggle="modal" data-target="#altaconvenio" class="pointer">
                    <button type="button" class="ml-1 btn btn-sm btn-light" data-toggle="tooltip" data-placement="right" title="CREAR">
                        +
                    </button>
                </span>
                <span id="trash_conv" class="trash"></span>
            </div>
            <div class="form-inline mt-2">
                <!-- Sector -->
                <label for="LegSect" class="mr-2 w80">Sector</label>
                <select class="form-control selectjs_sectores w200" id="LegSect" name="LegSect">
                </select>
                <!-- Button modal modalSectores-->
                <span data-toggle="modal" data-target="#altaSector" class="pointer">
                    <button type="button" class="ml-1 btn btn-sm btn-light" data-toggle="tooltip" data-placement="right" title="CREAR">
                        +
                    </button>
                </span>
                <span id="trash_sect" class="trash"></span>
            </div>
            <div class="form-inline mt-2 d-none" id="select_seccion">
                <!-- Seccion -->
                <label for="LegSec2" class="mr-2 w80">Sección</label>
                <select class="form-control selectjs_secciones w200" id="LegSec2" name="LegSec2" >
                </select>
                <!-- Button modal altaSeccion-->
                <span data-toggle="modal" data-target="#altaseccion" class="pointer">
                    <button type="button" class="ml-1 btn btn-sm btn-light" data-toggle="tooltip" data-placement="right" title="CREAR">
                        +
                    </button>
                </span>
                <span id="trash_secc" class="trash"></span>
            </div><small id="SectorHelpBlock2" class="form-text text-muted"></small>
            <div class="form-inline mt-2">
                <!-- Grupos -->
                <label for="LegGrup" class="mr-2 w80">Grupos</label>
                <select class="form-control selectjs_grupos w200" id="LegGrup" name="LegGrup">
                </select>
                <span data-toggle="modal" data-target="#altaGrupo" class="pointer">
                    <button type="button" class="ml-1 btn btn-sm btn-light" data-toggle="tooltip" data-placement="right" title="CREAR">
                        +
                    </button>
                </span>
                <span id="trash_grup" class="trash"></span>
            </div>
            <div class="form-inline mt-2">
                <!-- Sucursal -->
                <label for="LegSucu" class="mr-2 w80">Sucursal</label>
                <select class="form-control selectjs_sucursal w200" id="LegSucu" name="LegSucu">
                </select>
                <span data-toggle="modal" data-target="#altasucur" class="pointer">
                    <button type="button" class="ml-1 btn btn-sm btn-light" data-toggle="tooltip" data-placement="right" title="CREAR">
                        +
                    </button>
                </span>
                <span id="trash_sucur" class="trash"></span>
            </div>
            <div class="form-inline mt-2">
                <!-- Tareas de produccion -->
                <label for="LegTareProd" class="mr-2 w80">Tareas</label>
                <select class="form-control selectjs_tarea w200" id="LegTareProd" name="LegTareProd">
                </select>
                <span data-toggle="modal" data-target="#altatarea" class="pointer">
                    <button type="button" class="ml-1 btn btn-sm btn-light" data-toggle="tooltip" data-placement="right" title="CREAR">
                        +
                    </button>
                </span>
                <span id="trash_tar" class="trash"></span>
            </div>
            <div class="form-inline mt-2">
                <!-- Tel interno y Mail -->
                <div class="form-inline">
                <label for="LegTel3" class="mr-2 w80">Teléfono</label>
                <input type="text" class="form-control w200" value="<?=$pers['LegTel3']?>" id="LegTel3" name="LegTel3" maxlength="15">
                <label for="LegMail" class="mx-2">E-Mail</label>
                <input type="mail" class="mx-2 form-control w350" value="<?=$pers['LegMail']?>" id="LegMail" name="LegMail" maxlength="250">
                </div>
            </div>
        </div>
    </div>
</div>