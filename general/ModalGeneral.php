<div id="modalGeneral" class="modal fadeIn" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1" style="padding-right:0px">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document" id="TopN">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 d-flex justify-content-between" style="color: #333333;">
                <section>
                    <div class="font1 nombre fw5"></div>
                    <div class="fontq legajo"></div>
                </section>
                <section class="fontq d-none d-sm-block">
                    <div class="d-flex justify-content-end"><span class="mx-1 dia fw5"></span><span class="fontq d-flex justify-content-end" id="FechCierre"></span></div>
                    <div class="d-inline-flex align-items-center">
                        <div class="d-flex justify-content-end w500 mt-1" id="FicHorario"></div>
                        <?php if ($_SESSION['ABM_ROL']['aCit'] == 1) { ?>
                            <button data-titlel="Citar Horario" class="bi bi-pen btn btn-link fontq Citacion pointer pr-0" id="Citacion" style="display: none;">
                            </button>
                        <?php } ?>
                        <?php if ($_SESSION['ABM_ROL']['bCit'] == 1) { ?>
                            <button data-titlel="Eliminar Citación" class="bi bi-trash btn btn-link fontq pl-2 pr-0 pointer" id="bCit" style="display: none;">
                            </button>
                        <?php } ?>

                    </div>
                </section>
            </div>
            <input type="hidden" name="" id="data">
            <div class="modal-body pt-0 mt-n3">
                <input type="hidden" hidden id="Mxs">
                <?php if ($_SESSION['ABM_ROL']['aCit'] == 1) { ?>
                    <form action="insert.php" method="POST" class="Form_Citacion">
                        <div class="row d-none mt-2" id="rowCitacion">
                            <!-- <div class="col-lg-6 col-12"></div> -->
                            <div class="col-12 col-md-4 col-lg-7">
                            </div>
                            <div class="col-12 col-md-8 col-lg-5 d-flex justify-content-end">
                                <div class="d-inline-flex align-items-center">
                                    <p class="m-0 text-secondary fontq fw5 mr-2">Citación: </p>
                                    <span for="CitEntra" class="mx-2 d-none d-sm-block"><span class="text-secondary fontq">Ent.</span></span>
                                    <input placeholder="00:00" name="CitEntra" id="CitEntra" class="form-control w80 HoraMask" type="tel" autocomplete="off">
                                    <span for="CitSale" class="mx-2 d-none d-sm-block"><span class="text-secondary fontq">Sal.</span></span>
                                    <input placeholder="00:00" name="CitSale" id="CitSale" class="form-control w80 HoraMask ml-1" type="tel" autocomplete="off">
                                    <span for="CitDesc" class="mx-2 d-none d-sm-block"><span class="text-secondary fontq">Desc.</span></span>
                                    <input placeholder="00:00" name="CitDesc" id="CitDesc" class="form-control w80 HoraMask ml-1" type="tel" value="00:00" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-inline d-flex justify-content-end mt-2">
                                    <input type="hidden" name="alta_Citación" id="alta_Citación">
                                    <input type="hidden" name="datos_Citacion" class="datos_Citacion">
                                    <button type="submit" class="h35 mr-sm-2 btn btn-sm btn-custom fontq submit_btn_Citación btn-mobile mt-2">Confirmar</button>
                                    <button type="button" class="h35 btn btn-sm btn-light border fontq btn-mobile mt-2" id="cancelar_btn_Citación">Cancelar</button>
                                </div>
                                <div class="respuesta_Citacion fontq text-secondary mx-2 d-flex float-right"></div>
                            </div>
                        </div>
                    </form>
                <?php } ?>
                <div class="row bg-white py-2" id="Navs">
                    <div class="col-12 d-none d-sm-block">
                        <button class="btn btn-sm btn-link text-decoration-none fontq text-secondary p-0 pb-1 m-0 float-right" id="RefreshModal" data-titlel="Actualizar Grilla">Actualizar</button>
                        <?php if ($_SESSION['ABM_ROL']['Proc'] == 1) { ?>
                            <button class="mr-2 btn btn-sm btn-link text-decoration-none fontq text-secondary p-0 pb-1 m-0 float-right" id="ProcesarLegajo" data-titlel="Procesar Legajo y Fecha">Procesar</button>
                        <?php } ?>
                    </div>
                    <div class="col-12 pb-3">
                        <nav class="fontq">
                            <div class="nav nav-tabs bg-light radius" id="nav-tab" role="tablist">
                                <a class="p-3 nav-item nav-link active text-dark" id="Fichadas-tab" data-toggle="tab" href="#Fichadas" role="tab" aria-controls="Fichadas" aria-selected="true">
                                    <span class="text-tab d-inline-flex align-items-center">
                                        <span class="d-none d-lg-block">Fichadas</span>
                                        <span class="d-block d-lg-none">Fich</span>
                                        <span class="ml-1 ls1 fw3" id="CantFic"></span>
                                        <?php if ($_SESSION['ABM_ROL']['aFic'] == 1) { ?>
                                            <button class="btn btn-light btn-sm p-0 px-2 ml-2 m-0 border border" type="button" id="AddFic" data-titler="Alta Fichada">+</button>
                                            <!-- <button class="btn btn-light btn-sm p-0 px-2 ml-2 m-0 border" type="button" id="AddHora" data-titler="Alta Hora">+</button> -->
                                        <?php } ?>
                                    </span></a>
                                <a class="p-3 nav-item nav-link text-dark" id="Novedades-tab" data-toggle="tab" href="#Novedades" role="tab" aria-controls="Novedades" aria-selected="true">
                                    <span class="text-tab d-inline-flex">
                                        <span class="d-none d-lg-block">Novedades</span>
                                        <span class="d-block d-lg-none">Nov</span>
                                        <span class="ml-1 ls1 fw3" id="CantNov"></span>
                                        <?php if ($_SESSION['ABM_ROL']['aNov'] == 1) { ?>
                                            <button class="btn btn-light btn-sm p-0 px-2 ml-2 m-0 border" type="button" id="AddNov" data-titler="Alta Novedad">+</button>
                                        <?php } ?>
                                    </span></a>
                                <a class="p-3 nav-item nav-link text-dark" id="Horas-tab" data-toggle="tab" href="#Horas" role="tab" aria-controls="Horas" aria-selected="true">
                                    <span class="text-tab d-inline-flex">
                                        <span class="d-none d-lg-block">Horas</span>
                                        <span class="d-block d-lg-none">Hs.</span>
                                        <span class="ml-1 ls1 fw3" id="CantHor"></span>
                                        <?php if ($_SESSION['ABM_ROL']['aHor'] == 1) { ?>
                                            <button class="btn btn-light btn-sm p-0 px-2 ml-2 m-0 border" type="button" id="AddHora" data-titler="Alta Hora">+</button>
                                        <?php } ?>
                                    </span></a>
                                <a class="p-3 nav-item nav-link text-dark" id="OtrasNov-tab" data-toggle="tab" href="#OtrasNov" role="tab" aria-controls="OtrasNov" aria-selected="true">
                                    <span class="text-tab d-inline-flex">
                                        <span class="d-none d-lg-block">Otras Novedades</span>
                                        <span class="d-block d-lg-none">O Nov.</span>
                                        <span class="ml-1 ls1 fw3" id="CantONov"></span>
                                        <?php if ($_SESSION['ABM_ROL']['aONov'] == 1) { ?>
                                            <button class="btn btn-light btn-sm p-0 px-2 ml-2 m-0 border" type="button" id="AddONov" data-titler="Alta Otra Novedad">+</button>
                                        <?php } ?>
                                    </span></a>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active border border-top-0" id="Fichadas" role="tabpanel" aria-labelledby="Fichadas-tab">
                                <div class="row m-0 overflow-auto" style="min-height:250px;">
                                    <div class="col-12 py-2 py-sm-3">
                                        <span class="d-block d-lg-none fontq mb-1" id="xsTFic">Fichadas</span>
                                        <form action="insert.php" method="POST" class="Form_Fichadas d-none">
                                            <div class="form-inline mb-2"><label for="RegFech_Fichada" class="mr-2 w80">Fecha:</label>
                                                <input type="text" readonly name="RegFech" class="h40 form-control RegFech w120 ls1" id="RegFech_Fichada">
                                            </div>
                                            <div class="form-inline mb-2"><input type="hidden" name="alta_fichada" class="" value="true"><input type="hidden" name="datos_fichada" class="datos_fichada" value=""><label for="RegHora" class="mr-2 w80">Hora:</label>
                                                <input type="tel" placeholder="00:00" name="RegHora" id="RegHora" class="HoraMask h40 form-control RegHora w120 ls1">
                                                <button type="submit" class="ml-sm-2 btn btn-sm btn-custom fontq submit_btn btn-mobile mt-2 mt-sm-0">Agregar</button><button type="button" class="float-right ml-sm-2 btn btn-sm btn-link border-0 text-secondary fontq cancelar_btn_fic btn-mobile mt-2 mt-sm-0">Cancelar</button>
                                                <div class="respuesta_fichada fontq text-secondary mx-2"></div>
                                            </div>
                                        </form>
                                        <form action="insert.php" method="POST" class="Form_Fichadas_Mod d-none">
                                            <div class="form-inline mb-2"><label for="RegFech_mod" class="mr-2 w80">Fecha:</label><input type="text" name="RegFech_mod" readonly class="h40 form-control RegFech w120 ls1" id="RegFech_mod">
                                            </div>
                                            <div class="form-inline mb-2">
                                                <input type="hidden" name="mod_fichada" class="" value="true">
                                                <input type="hidden" name="datos_fichada_mod" class="datos_fichada_mod" id="datos_fichada_mod" value="">
                                                <label for="RegHora_mod" class="mr-2 w80">Hora:</label>
                                                <input type="tel" placeholder="00:00" name="RegHora_mod" class="HoraMask form-control w120 h40 ls1" id="RegHora_mod">
                                                <button type="submit" class="ml-sm-2 btn btn-sm btn-custom fontq submit_btn btn-mobile mt-2 mt-sm-0">Modificar</button><button type="button" class="float-right ml-sm-2 btn btn-sm btn-link border-0 text-secondary fontq cancelar_btn_fic  btn-mobile mt-2 mt-sm-0">Cancelar</button>
                                                <div class="respuesta_fichada_mod fontq text-secondary mx-2"></div>
                                            </div>
                                        </form>
                                        <table class="font1 fw4 table table-sm w-auto text-nowrap table-responsive" id="GetFichadas">
                                            <thead class="border-bottom text-uppercase fontpp">
                                                <th class=" ls1">Hora</th>
                                                <th class=""></th>
                                                <th class=""></th>
                                                <th class="">Estado</th>
                                                <th class="">Tipo</th>
                                                <th class="">Fecha</th>
                                                <th class="">Fichada Original</th>
                                                <th class=" w-100"></th>
                                            </thead>
                                        </table>
                                        <div class="respuesta_baja_fichada"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade border border-top-0" id="Novedades" role="tabpanel" aria-labelledby="Novedades-tab">
                                <div class="row m-0 overflow-auto" style="min-height:250px;">
                                    <div class="col-12 py-2 py-sm-3">
                                        <span class="d-block d-lg-none fontq mb-1" id="xsTNov">Novedades</span>
                                        <form action="insert.php" method="POST" class="Form_Novedad d-none">
                                            <input type="hidden" name="_nt" id="novTipo">
                                            <input type="hidden" name="_nc" id="novCate">
                                            <div class="mb-2 d-flex align-items-center">
                                                <input type="hidden" name="alta_novedad" id="alta_novedad">
                                                <input type="hidden" name="datos_novedad" class="datos_novedad" id="datos_novedad" value="">
                                                <input type="hidden" name="CNove" class="CNove" value="" id="CNove">
                                                <label for="FicNove" class="w80 mr-2">Novedad:</label>
                                                <select class="selectjs_Novedades w250 FicNove" name="FicNove" id="FicNove"></select>
                                                <span class="mx-2"></span>
                                                <select class="selectjs_NoveCausa FicCaus w250" name="FicCaus" id="FicCaus"></select>
                                            </div>
                                            <div class="mb-2 d-flex align-items-center">
                                                <label for="FicHoras" class="w80 mr-2">Horas:</label>
                                                <input placeholder="00:00" type="tel" name="FicHoras" id="FicHoras" class="HoraMask ls1 form-control FicHoras w100 h40">
                                                <span class="mx-2"></span>
                                                <input type="text" class="form-control FicObse h40 w400" name="FicObse" id="FicObse" maxlength="40" placeholder="Observaciones">
                                            </div>
                                            <div class="mb-2">
                                                <div class="custom-control custom-switch pt-1 custom-control-inline d-none">
                                                    <input type="checkbox" class="custom-control-input" name="FicJust" id="FicJust">
                                                    <label class="custom-control-label" for="FicJust" style="padding-top:3px;">Justificada</label>
                                                </div>
                                                <div class="custom-control custom-switch pt-1 custom-control-inline">
                                                    <input type="checkbox" class="custom-control-input" name="FicCate" id="FicCate">
                                                    <label class="labelFicCate custom-control-label mr-2" for="FicCate" style="padding-top:3px;">Novedad Forzada</label>
                                                    <button type="submit" class="ml-sm-2 btn btn-sm btn-custom fontq submit_btn_mod btn-mobile mt-2 mt-sm-0">Agregar</button>
                                                    <button type="button" class="ml-sm-2 btn btn-sm btn-link border-0 text-secondary fontq cancelar_btn_nov btn-mobile mt-2 mt-sm-0">Cancelar</button>
                                                    <div class="respuesta_novedad fontq text-secondary mx-2"></div>
                                                </div>
                                            </div>
                                        </form>
                                        <table class="font1 table table-sm w-auto text-nowrap table-responsive" id="GetNovedades">
                                            <thead class="border-bottom text-uppercase fontpp">
                                                <th title="Código de la novedad">#</th>
                                                <th title="Descripción de la novedad">Novedad</th>
                                                <th class="ls1" title="Horas de la novedad">Horas</th>
                                                <th class="" title=""></th>
                                                <th class="" title=""></th>
                                                <th title="Observaciones">Obserb.</th>
                                                <th class="" title="Causa de la novedad">Causa</th>
                                                <th class="" title="Justificada">Just.</th>
                                                <th class="">Tipo</th>
                                                <th title="CATEGORÍA">Categ.</th>
                                                <th class=""></th>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade border border-top-0" id="Horas" role="tabpanel" aria-labelledby="Horas-tab">
                                <div class="row m-0 overflow-auto" style="min-height:250px;">
                                    <div class="col-12 py-2 py-sm-3">
                                        <span class="d-block d-lg-none fontq mb-1" id="xsTHor">Horas</span>
                                        <form action="insert.php" method="POST" class="Form_Horas d-none">
                                            <div class="d-flex align-items-center mb-2">
                                                <label for="Fic1Hora" class="mr-2 w80 d-none d-sm-block">Tipo
                                                    Hora:</label>
                                                <label for="Fic1Hora" class="mr-2 w80 d-block d-sm-none">Hora:</label>
                                                <select class="selectjs_TipoHora w300" name="Fic1Hora" id="Fic1Hora"></select>
                                                <input type="hidden" name="modHora" id="modHora">
                                                <input type="hidden" name="NombreLega" id="NombreLega">
                                                <input type="hidden" name="FicHsAu" id="FicHsAu">
                                                <span class="mx-1"></span>
                                                <label for="Fic1HsAu2" class="mx-2 d-none d-sm-block">Autorizadas:</label>
                                                <label for="Fic1HsAu2" class="mr-1 d-block d-sm-none">Autor</label>
                                                <input placeholder="00:00" type="tel" name="Fic1HsAu2" id="Fic1HsAu2" class="HoraMask form-control w70 h40 ls1" value="00:00">
                                            </div>
                                            <div class="form-inline mb-2">
                                                <label for="Fic1Caus" class="mr-2 w80">Motivo:</label>
                                                <select class="h40 selectjs_MotivoHora w300" name="Fic1Caus" id="Fic1Caus"></select>
                                            </div>
                                            <div class="form-inline mb-2"><label for="Fic1Observ" class="mr-2 w80">Observación:</label>
                                                <input type="text" class="form-control w300 h40" name="Fic1Observ" id="Fic1Observ" maxlength="40">
                                                <input type="hidden" name="alta_horas" id="alta_horas">
                                                <input type="hidden" name="datos_hora" class="datos_hora" value="">
                                                <button type="submit" class="float-right ml-sm-2 btn btn-sm btn-custom fontq submit_btn_HorMod btn-mobile mt-2 mt-sm-0"></button>
                                                <button type="button" class="float-right ml-sm-2 btn btn-sm btn-link border-0 text-secondary fontq btn-mobile mt-2 mt-sm-0" id="cancelar_btn_hor">Cancelar</button>
                                                <div class="respuesta_Horas fontq text-secondary mx-2"></div>
                                            </div>
                                        </form>
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
                                        <table class="font1 table table-sm w-auto text-nowrap table-responsive" id="GetHoras">
                                            <thead class="border-bottom text-uppercase fontpp">
                                                <th class="px-2">#</th>
                                                <th class="px-2">Descripción</th>
                                                <th class="px-2 text-center ">Auto.</th>
                                                <th class="px-2 text-center ">Hechas</th>
                                                <th class="px-2"></th>
                                                <th class="px-2"></th>
                                                <th class="px-2">Motivo</th>
                                                <th class="px-2">Observaciones</th>
                                                <th class="px-2"></th>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade border border-top-0" id="OtrasNov" role="tabpanel" aria-labelledby="OtrasNov-tab">
                                <div class="row m-0 overflow-auto" style="min-height:250px;">
                                    <div class="col-12 py-2 py-sm-3">
                                        <span class="d-block d-lg-none fontq mb-1" id="xsTOnov">Otras Novedades</span>
                                        <form action="insert.php" method="POST" class="Form_OtraNovedad d-none">
                                            <div class="d-flex align-items-center mb-2">
                                                <!-- <label for="FicONov" class="mr-2 w80">Novedad:</label> -->
                                                <label for="FicONov" class="w80 mr-2 d-none d-sm-block">Novedad:</label>
                                                <select class="selectjs_OtrasNovedades w250" name="FicONov" id="FicONov"></select>
                                                <span class="mx-1"></span>
                                                <label for="FicValor" class="mr-1 d-none d-sm-block">Valor:</label>
                                                <input type="tel" name="FicValor" id="FicValor" class="form-control h40 w80" placeholder="0.00">
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <label for="FicONovFechas" class="w80 mr-2 d-none d-sm-block">Fecha:</label>
                                                <input type="text" name="FicONovFechas" id="FicONovFechas" class="form-control text-center h40 w250">
                                            </div>

                                            <div class="form-inline mb-2">
                                                <label for="FicObsN" class="mr-2 w80">Observación:</label>
                                                <input type="text" class="form-control w300 h40" name="FicObsN" id="FicObsN" maxlength="40">
                                                <input type="hidden" name="alta_OtrasNov" id="alta_OtrasNov">
                                                <input type="hidden" name="datos_OtrasNov" class="datos_OtrasNov" value="">
                                                <button type="submit" class="float-right ml-sm-2 btn btn-sm btn-custom fontq submit_btn_OtrasNov btn-mobile mt-2 mt-sm-0"></button>
                                                <button type="button" class="float-right ml-sm-2 btn btn-sm btn-link border-0 text-secondary fontq btn-mobile mt-2 mt-sm-0" id="cancelar_btn_OtrasNov">Cancelar</button>
                                                <div class="respuesta_OtrasNov fontq text-secondary mx-2"></div>
                                            </div>
                                        </form>
                                        <table class="font1 table w-auto text-nowrap table-sm table-responsive" id="GetOtrasNov">
                                            <thead class="border-bottom text-uppercase fontpp">
                                                <th class="px-2 text-center">#</th>
                                                <th class="px-2">Novedad</th>
                                                <th class="px-2">Valor</th>
                                                <th class="px-2">Observación</th>
                                                <th class="px-2"></th>
                                                <th class="px-2"></th>
                                                <th class="px-2">Informar</th>
                                                <th class="px-2 w-100"></th>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0"><button type="button" class="btn btn-custom fontq border float-right" data-dismiss="modal" id="CierraModalGeneral">Cerrar</button></div>
        </div>
    </div>
</div>