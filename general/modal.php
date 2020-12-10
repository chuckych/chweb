<button type="button" class="btn text-white btn-sm ' . $bgcolor . ' opa9" data-toggle="modal" data-target="#m' . $Gen_Lega . $Gen_Fecha2 . 'm">+</button>
<div class="modal" id="m' . $Gen_Lega . $Gen_Fecha2 . 'm" tabindex="-1" role="dialog" aria-labelledby="m' . $Gen_Lega . 'mTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <p class="fw5 font1">' . $Gen_Nombre . '</p>
                <p class="ls1 fw4 d-none d-sm-block align-middle mt-1">' . nombre_dia($Gen_Dia_Semana) . ' ' . $Gen_Fecha . ', <span class="mr-1">Horario:</span>' . $Gen_Horario . '</p>
            </div>
            <div class="modal-body fw4 pt-0">
                <div class="row bg-white py-2">
                    <div class="col-12 p-3">
                        <nav class="fontq">
                            <div class="nav nav-tabs bg-light" id="nav-tab" role="tablist"><a class="p-3 nav-item nav-link active text-secondary" id="f' . $Gen_Lega . $Gen_Fecha2 . 'f-tab" data-toggle="tab" href="#f' . $Gen_Lega . $Gen_Fecha2 . 'f" role="tab" aria-controls="f' . $Gen_Lega . $Gen_Fecha2 . 'f" aria-selected="true"><span class="text-tab">Fichadas</span></a><a class="p-3 nav-item nav-link text-secondary" id="n' . $Gen_Lega . $Gen_Fecha2 . 'n-tab" data-toggle="tab" href="#n' . $Gen_Lega . $Gen_Fecha2 . 'n" role="tab" aria-controls="n' . $Gen_Lega . $Gen_Fecha2 . 'n" aria-selected="true"><span class="text-tab">Novedades</span></a><a class="p-3 nav-item nav-link text-secondary" id="h' . $Gen_Lega . $Gen_Fecha2 . 'h-tab" data-toggle="tab" href="#h' . $Gen_Lega . $Gen_Fecha2 . 'h" role="tab" aria-controls="h' . $Gen_Lega . $Gen_Fecha2 . 'h" aria-selected="true"><span class="text-tab">Horas</span></a></div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="f' . $Gen_Lega . $Gen_Fecha2 . 'f" role="tabpanel" aria-labelledby="f' . $Gen_Lega . $Gen_Fecha2 . 'f-tab">
                                <div class="row m-0 border border-top-0 overflow-auto">
                                    <div class="col-12 py-3">
                                        <div class="form-inline mb-2">
                                            <input type="hidden" name="RegTarj" id="RegTarj" class="RegTarj">
                                            <input type="hidden" name="RegFech" id="RegFech" class="form-control RegFech" disabled>
                                            <input type="text" name="RegHora" id="RegHora" class="form-control RegHora w80 h40">
                                            <button class="ml-2 h40 btn btn-sm btn-light text-secondary fontq">Agregar</button>
                                        </div>
                                        <table class="font1 fw5 table table-borderless w-100 text-nowrap table-responsive">
                                            <thead class="border-bottom text-uppercase fontpp">
                                                <th class="px-2 ls1">Hora</th>
                                                <th class="px-2">Estado</th>
                                                <th class="px-2">Tipo</th>
                                                <th class="px-2"></th>
                                            </thead>
                                            <tbody>
                                                <tr>' . $Fichadas . '</tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="n' . $Gen_Lega . $Gen_Fecha2 . 'n" role="tabpanel" aria-labelledby="n' . $Gen_Lega . $Gen_Fecha2 . 'n-tab">
                                <div class="row m-0 border border-top-0 overflow-auto">
                                    <div class="col-12 py-3">
                                        <table class="font1 fw5 table table-borderless w-100 text-nowrap table-responsive">
                                            <thead class="border-bottom text-uppercase fontpp">
                                                <th class="px-2">#</th>
                                                <th class="px-2">Novedad</th>
                                                <th class="px-2 ls1">Horas</th>
                                                <th class="px-2">Tipo</th>
                                                <th class="px-2"></th>
                                            </thead>
                                            <tbody>
                                                <tr>' . $Novedades . '</tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="h' . $Gen_Lega . $Gen_Fecha2 . 'h" role="tabpanel" aria-labelledby="h' . $Gen_Lega . $Gen_Fecha2 . 'h-tab">
                                <div class="row m-0 border border-top-0 overflow-auto">
                                    <div class="col-12 py-3">
                                        <table class="font1 fw5 table table-borderless w-100 text-nowrap table-responsive">
                                            <thead class="border-bottom text-uppercase fontpp">
                                                <th class="px-2">#</th>
                                                <th class="px-2">Descripción</th>
                                                <th class="px-2 text-center ls1">Auto.</th>
                                                <th class="px-2 text-center ls1">Calc.</th>
                                                <th class="px-2 text-center ls1">Hechas</th>
                                                <th class="px-2"></th>
                                            </thead>
                                            <tbody>
                                                <tr>' . $horas . '</tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0"><button type="button" class="btn-sm btn btn-secondary fontp border opa8 float-right ' . $bgcolor . '" data-dismiss="modal">Cerrar</button></div>
        </div>
    </div>
</div>