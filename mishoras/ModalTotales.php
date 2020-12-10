<!-- Modal Total General-->
<div class="modal animate__animated animate__fadeIn" id="Total_General" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 font1 text-secondary pb-0 bg-light">
                <p class="fw5 nombre"></p>
                <p class="fw5 fontq text-nowrap d-flex align-items-center p-1">
                    <svg class="bi" width="18" height="18" fill="currentColor">
                        <use xlink:href="../img/bootstrap-icons.svg#calendar-range" />
                    </svg>
                    <span id="" class="ml-2 Fechas"></span>
                </p>
            </div>
            <div class="modal-body pt-0">
                <div class="table-responsive mt-2">
                    <p class="fw4 m-0 fontq fw5 text-secondary p-1">HORAS</p>
                    <table class="table text-nowrap w-100" id="table-Total_General">
                        <thead class="text-uppercase fontpp fw4">
                            <th class="ls1">#</th>
                            <th class="ls1">Descripción</th>
                            <th class="text-center" title="Horas Autorizadas">Pagas</th>
                            <th class="text-center" title="Horas Calculadas">Hechas</th>
                            <th class="text-center" title="Horas Hechas"></th>
                            <th class="text-center"></th>
                        </thead>
                    </table>
                </div>
                <hr>
                <div class="table-responsive">
                    <p class="fw4 m-0 fontq fw5 text-secondary p-1">NOVEDADES</p>
                    <table class="table text-nowrap w-100" id="table-Total_Novedades">
                        <thead class="text-uppercase fontpp fw4">
                            <th class="ls1">#</th>
                            <th class="ls1">Descripción</th>
                            <th class="text-center">Horas</th>
                            <th class="text-center">Cant</th>
                            <th class="ls1">Tipo</th>
                            <th class="text-center"></th>
                        </thead>
                    </table>
                </div>
                <hr>
                <div class="table-responsive">
                    <p class="fw4 m-0 fontq fw5 text-secondary p-1">NOVEDADES POR TIPO</p>
                    <table class="table text-nowrap w-100" id="table-TotalNovTipo">
                        <thead class="text-uppercase fontpp fw4">
                            <!-- <th class="ls1">#</th> -->
                            <th class="ls1">Descripción</th>
                            <th class="text-center">Horas</th>
                            <th class="text-center">Cant</th>
                            <th class="text-center"></th>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top-0 bg-light">
                <button type="button" class="btn opa8 text-white fontq px-3 btn-custom" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>