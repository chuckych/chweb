<div class="row mt-2" id="form-nuevo-campo">
    <div class="col-12">
        <div class="card border radius">
            <div class="card-body border">
                <!-- <h6 class="mb-3">Definir campos</h6> -->

                <div id="campo-feedback" class="alert alert-danger d-none mb-3" role="alert"></div>

                <div class="form-row align-items-end">
                    <div class="col-sm-6 col-md-2 mb-3">
                        <label for="campo-posicion">Posición</label>
                        <input type="number" min="1" step="1" class="form-control h40" id="campo-posicion"
                            placeholder="Ej: 1">
                    </div>

                    <div class="col-sm-6 col-md-3 mb-3">
                        <label for="campo-tipo">Tipo de campo</label>
                        <select id="campo-tipo" class="form-control" style="width:100%;">
                            <option value=""></option>
                            <option value="legajo">Legajo</option>
                            <option value="apno">Apellido y Nombre</option>
                            <option value="dni_legajo">DNI Legajo</option>
                            <option value="cuil_legajo">CUIL Legajo</option>
                            <option value="cod_empresa"><?= $labelEmpr ?></option>
                            <option value="cod_planta"> <?= $labelPlan ?></option>
                            <option value="cod_convenio">Convenio</option>
                            <option value="cod_sector"> <?= $labelSect ?></option>
                            <option value="cod_seccion"> <?= $labelSecc ?></option>
                            <option value="cod_grupo"> <?= $labelGrup ?></option>
                            <option value="cod_sucursal"> <?= $labelSucu ?></option>
                            <option value="fecha">Fecha</option>
                            <option value="novedades">Novedades</option>
                            <option value="horas">Horas</option>
                            <option value="atra">Horas a trabajar</option>
                            <option value="trab">Horas trabajadas</option>
                            <option value="primer_fichada">Primer Fichada</option>
                            <option value="ultima_fichada">Ultima Fichada</option>
                            <option value="todas_fichadas">Todas las fichadas</option>
                            <option value="turstr">Horario</option>
                            <option value="labo">Laboral</option>
                            <option value="feri">Feriado</option>
                        </select>
                    </div>

                    <div class="col-sm-6 col-md-3 mb-3 d-none fadeIn" id="subtipo-wrapper">
                        <label for="campo-subtipo">Valor</label>
                        <select id="campo-subtipo" class="form-control" style="width:100%;"></select>
                        <small id="subtipo-help" class="form-text text-muted d-none">Cargando...</small>
                    </div>

                    <div class="col-sm-6 col-md-2 mb-3">
                        <label for="campo-tamano">Tamaño</label>
                        <input type="number" min="1" step="1" class="form-control h40" id="campo-tamano"
                            placeholder="Ej: 5">
                    </div>

                    <div class="col-sm-6 col-md-2 mb-3">
                        <label for="campo-formato">Formato</label>
                        <select id="campo-formato" class="form-control" style="width:100%;">
                            <option value=""></option>
                            <option value="numero">Numero</option>
                            <option value="decimal">Decimal</option>
                            <option value="horas">Horas (HH:MM)</option>
                            <option value="texto">Texto</option>
                            <option value="YYYY-MM-DD">Fecha YYYY-MM-DD</option>
                            <option value="YYYYMMDD">Fecha YYYYMMDD</option>
                            <option value="YYYY/MM/DD">Fecha YYYY/MM/DD</option>
                            <option value="MM-DD-YYYY">Fecha MM-DD-YYYY</option>
                            <option value="MMDDYYYY">Fecha MMDDYYYY</option>
                            <option value="MM/DD/YYYY">Fecha MM/DD/YYYY</option>
                            <option value="DD-MM-YYYY">Fecha DD-MM-YYYY</option>
                            <option value="DDMMYYYY">Fecha DDMMYYYY</option>
                            <option value="DD/MM/YYYY">Fecha DD/MM/YYYY</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-custom font09" id="btn-agregar-campo">Agregar campo</button>
                </div>
                <div class="row">
                    <div class="col-12 mb-2 d-flex flex-column">
                        <label for="campo-separador " class="fit-content">Separador de campo</label>
                        <div class="d-inline-flex align-items-center" style="gap: 5px">
                            <input type="text" class="form-control h40 w70 text-center" id="campo-separador"
                                placeholder="Ej: ," maxlength="1" value=",">
                            <small class="resultado_separador text-secondary"></small>
                        </div>
                    </div>
                    <div class="col-12">
                        <!-- Nota -->
                        <div class="mt-2">
                            <small>
                                <ul class="m-0 p-4 radius bg-light no-dot border-left">
                                    <li>
                                        <i class="bi bi-chevron-right font06"></i>
                                        Para editar un campo, debe añadir otro en la misma posición o haciendo clic en
                                        <i class="bi bi-pen"></i>
                                    </li>
                                    <li>
                                        <i class="bi bi-chevron-right font06"></i>
                                        El orden de los campos se puede modificar arrastrando el ícono
                                        <i class="bi bi-list"></i>.
                                    </li>
                                    <li>
                                        <i class="bi bi-chevron-right font06"></i>
                                        Puede eliminar un campo haciendo clic en el ícono <i class="bi bi-trash"></i>.
                                    </li>
                                    <li>
                                        <i class="bi bi-chevron-right font06"></i>
                                        Los campos de numéricos y decimal se completan con ceros a la izquierda si el
                                        tamaño es mayor al valor obtenido.
                                    </li>
                                    <li>
                                        <i class="bi bi-chevron-right font06"></i>
                                        Los campos de texto reemplazan caracteres que sean igual al separador por un
                                        espacio.
                                    </li>
                                </ul>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3 mb-3">
    <div class="col-12">
        <div class="table-responsive">
            <div class="border radius p-3">
                <table class="table text-nowrap w-100 fadeIn">
                    <thead class="font08">
                        <tr>
                            <th class="h40">Posición</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th class="text-center">Tamaño</th>
                            <th>Formato</th>
                            <th class="text-right" style="width: 140px;"></th>
                        </tr>
                    </thead>
                    <tbody id="tabla-campos-body">
                        <tr id="tabla-campos-vacio">
                            <td colspan="6" class="text-center text-muted">Todavía no hay campos agregados.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>