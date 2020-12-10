<div id="modalGeneral" class="modal animate__animated animate__fadeIn" role="dialog" data-backdrop="static" data-keyboard="true" tabindex="-1" style="padding-right: 0px">
    <div class="modal-dialog modal-xl modal-dialog-top modal-dialog-scrollable" role="document" id="TopN">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 font1 text-secondary bg-light">
                <p class="fw5 font1 nombre"></p>
                <p class="align-middle mt-1 fontq">
                    <span class="dia fw5"></span><br />Horario:<span class="mx-1 horario fw5"></span></p>
            </div>
            <div class="modal-body fw4 pt-0 mt-n3">
                <div class="row bg-white py-2">
                    <div class="col-12">
                        <button class="d-none btn btn-sm btn-link text-decoration-none fontq text-secondary p-0 pb-1 m-0 float-right" id="RefreshModal"> Actualizar</button>
                    </div>
                    <div class="col-12 pb-3">
                        <nav class="fontq">
                            <div class="nav nav-tabs bg-light radius" id="nav-tab" role="tablist">
                                <a class="p-3 nav-item nav-link active text-secondary" id="Fichadas-tab" data-toggle="tab" href="#Fichadas" role="tab" aria-controls="Fichadas" aria-selected="true"><span class="text-tab">Fichadas </span><span class="ml-1 ls1 fw3" id="CantFic"></span></a>
                                <a class="p-3 nav-item nav-link text-secondary" id="Novedades-tab" data-toggle="tab" href="#Novedades" role="tab" aria-controls="Novedades" aria-selected="true"><span class="text-tab">Novedades </span><span class="ml-1 ls1 fw3" id="CantNov"></span></a></span></a>
                                <a class="p-3 nav-item nav-link text-secondary" id="Horas-tab" data-toggle="tab" href="#Horas" role="tab" aria-controls="Horas" aria-selected="true"><span class="text-tab">Horas</span><span class="ml-1 ls1 fw3" id="CantHor"></span></a>
                                <a class="p-3 nav-item nav-link text-secondary d-none" id="OtrasNov-tab" data-toggle="tab" href="#OtrasNov" role="tab" aria-controls="OtrasNov" aria-selected="true"><span class="text-tab">Otras Novedades</span></a>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <!-- Fichadas -->
                            <div class="tab-pane fade show active" id="Fichadas" role="tabpanel" aria-labelledby="Fichadas-tab">
                                <div class="row m-0 border border-top-0 overflow-auto" style="max-height: 250px;min-height: 250px;">
                                    <div class="col-12 py-3">
                                        <table class="font1 fw5 table w-auto text-nowrap table-responsive" id="GetFichadas">
                                            <thead class="border-bottom text-uppercase fontpp">
                                                <th class=" ls1">Hora</th>
                                                <th class="">Estado</th>
                                                <th class="">Tipo</th>
                                                <th class="">Fecha</th>
                                                <th class="">Fichada Original</th>
                                                <th class=" w-100"></th>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Novedades -->
                            <div class="tab-pane fade" id="Novedades" role="tabpanel" aria-labelledby="Novedades-tab">
                                <div class="row m-0 border border-top-0 overflow-auto" style="min-height: 250px;">
                                    <div class="col-12 pt-3">
                                        <table class="font1 fw5 table w-auto text-nowrap table-responsive" id="GetNovedades">
                                            <thead class="border-bottom text-uppercase fontpp">
                                                <th class="">#</th>
                                                <th class="">Novedad</th>
                                                <th class="ls1">Horas</th>
                                                <th class="">Obserbación</th>
                                                <th class="">Causa</th>
                                                <th class="">Just.</th>
                                                <th class="">Tipo</th>
                                                <th class="">Categoría</th>
                                                <th class=""></th>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Horas -->
                            <div class="tab-pane fade" id="Horas" role="tabpanel" aria-labelledby="Horas-tab">
                                <div class="row m-0 border border-top-0 overflow-auto" style="min-height: 250px;">
                                    <div class="col-12 py-3">
                                        <div class="text-dark fontq m-0 d-inline-flex w-100 pb-3 mt-1" id="divHorasTR">
                                            <div class="mr-1" id="TextFicHsAT"></div>
                                            <div class="mr-1 text-nowrap" id="TextFicHsAT_M"></div>
                                            <span id="FicHsAT" class="fw5 ls1"></span>
                                            <div class="mx-1" id="TextFicHsTr"></div>
                                            <div class="mx-1 text-nowrap" id="TextFicHsTr_M"></div>
                                            <span id="FicHsTr" class="fw5 ls1"></span>
                                        </div>
                                        <div id="ProgressHoras">
                                        </div>
                                        <table class="font1 fw5 table w-auto text-nowrap table-responsive" id="GetHoras">
                                            <thead class="border-bottom text-uppercase fontpp">
                                                <th class="px-2">#</th>
                                                <th class="px-2">Descripción</th>
                                                <th class="px-2 text-center ls1">Pagas</th>
                                                <th class="px-2 text-center ls1">Hechas</th>
                                                <th class="px-2 text-center ls1">Calc.</th>
                                                <th class="px-2"></th>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Otras Novedades -->
                            <div class="tab-pane fade d-none" id="OtrasNov" role="tabpanel" aria-labelledby="OtrasNov-tab">
                                <div class="row m-0 border border-top-0 overflow-auto" style="min-height: 250px;">
                                    <div class="col-12 py-3">
                                        <table class="font1 fw5 table w-auto text-nowrap table-responsive" id="GetOtrasNov">
                                            <thead class="border-bottom text-uppercase fontpp">
                                                <th class="px-2">#</th>
                                                <th class="px-2">Descripción</th>
                                                <th class="px-2 text-center ls1">Auto.</th>
                                                <th class="px-2 text-center ls1">Calc.</th>
                                                <th class="px-2 text-center ls1">Hechas</th>
                                                <th class="px-2"></th>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0">
                <button type="button" class="btn opa8 text-white fontq px-3 btn-custom" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>