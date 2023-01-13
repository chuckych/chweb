<!-- Modal -->
<div class="modal animate__animated animate__fadeIn" id="Filtros" tabindex="-1" aria-labelledby="FiltrosLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        
                        <label for="Tipo" class="mb-1 fontq">Tipo Personal: </label>
                        <select class="selectjs_tipoper" id="Tipo" name="Tipo">
                        </select>
                        <span id="trash_allIn" title="Limpiar Filtros"
                            class="trash align-middle pb-0 fw5 float-right">Limpiar Filtros</span>
                    </div>
                    <div class="col-12 col-sm-4">
                        <!-- Empresa -->
                        <label for="Emp" class="mb-1 fontq">Empresas</label>
                        <select class="form-control selectjs_empresa" id="Emp" name="Emp">
                        </select>
                    </div>
                    <div class="col-12 col-sm-4">
                        <!-- Planta -->
                        <label for="Plan" class="mb-1 w100 fontq">Plantas </label>
                        <select class="form-control selectjs_plantas" id="Plan" name="Plan">
                        </select>
                    </div>
                    <div class="col-12 col-sm-4">
                        <!-- Sector -->
                        <label for="Sect" class="mb-1 w100 fontq">Sectores</label>
                        <select class="form-control selectjs_sectores" id="Sect" name="Sect">
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-4">
                        <!-- Seccion -->
                        <label for="Sec2" class="mb-1 w100 fontq">Secciónes</label>
                        <select disabled class="form-control select_seccion" id="Sec2" name="Sec2">
                        </select>
                    </div>
                    <div class="col-12 col-sm-4">
                        <!-- Grupos -->
                        <label for="Grup" class="mb-1 w100 fontq">Grupos</label>
                        <select class="form-control selectjs_grupos" id="Grup" name="Grup">
                        </select>
                    </div>
                    <div class="col-12 col-sm-4">
                        <!-- Sucursal -->
                        <label for="Sucur" class="mb-1 w100 fontq">Sucursales</label>
                        <select class="form-control selectjs_sucursal" id="Sucur" name="Sucur">
                        </select>
                    </div>
                </div>
                <div class="row d-flex align-items-center">
                    <div class="col-12 col-sm-4">
                        <!-- Tareas -->
                        <label for="Tare" class="mb-1 w100 fontq">Tareas</label>
                        <select class="form-control selectjs_tareprod" id="Tare" name="Tare">
                        </select>
                    </div>
                    <div class="col-12 col-sm-4">
                        <!-- Tareas -->
                        <label for="Conv" class="mb-1 w100 fontq">Convenios</label>
                        <select class="form-control selectjs_conv" id="Conv" name="Conv">
                        </select>
                    </div>
                    <div class="col-12 col-sm-4">
                        <!-- Regla -->
                        <label for="Regla" class="mb-1 w100 fontq">Regla</label>
                        <select class="form-control selectjs_regla" id="Regla" name="Regla">
                        </select>
                    </div>
                </div>
                <div class="row d-flex align-items-center">
                    <div class="col-12">
                        <!-- Legajo -->
                        <label for="Per" class="mb-1 w100 fontq">Legajos</label>
                        <select class="form-control selectjs_personal" id="Per" name="Per">
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-custom btn-sm fontq" data-dismiss="modal">Aplicar Filtros</button>
            </div>
        </div> 
    </div>
</div>