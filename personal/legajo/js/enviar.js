$(document).ready(function () {

    const btnAltaEmpresa = document.querySelector('[data-target="#altaEmpresa"]');
    btnAltaEmpresa && btnAltaEmpresa.remove();

    ActiveBTN(false, "#btnGuardar", 'Aguarde..', 'Aceptar')
    const formUpdLeg = document.querySelector('.Update_Leg');
    formUpdLeg && formUpdLeg.addEventListener('submit', function (e) {
        e.preventDefault();
        function submitForm() {
            let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
            $.ajax({
                type: $(".Update_Leg").attr("method"),
                url: $(".Update_Leg").attr("action"),
                data: $(".Update_Leg").serialize(),
                beforeSend: function (data) {
                    $.notifyClose();
                    notify('Procesando <span class = "dotting mr-1"> </span> ' + loading, 'info', 0, 'right')
                    $("#alerta_UpdateLega").addClass("d-none")
                    ActiveBTN(true, "#btnGuardar", 'Aguarde..', 'Aceptar')
                },
                success: function (data) {
                    // console.log(data.status);
                    if (data.status == 'ok') {
                        $.notifyClose();
                        ActiveBTN(false, "#btnGuardar", 'Aguarde..', 'Aceptar')
                        var dt = new Date();
                        var Minutos = ("0" + dt.getMinutes()).substr(-2);
                        var Segundos = ("0" + dt.getSeconds()).substr(-2);
                        var Horas = ("0" + dt.getHours()).substr(-2);
                        var HoraActual = Horas + ":" + Minutos + ":" + Segundos + "Hs.";
                        $("#alerta_UpdateLega").removeClass("d-none").removeClass("d-none").removeClass("text-danger").addClass("text-success")
                        // $(".respuesta_UpdateLega").html(`Datos Guardados.! ${HoraActual}`)
                        $(".mensaje_UpdateLega").html('');
                        $("#Encabezado").html(`Legajo: ${data.Lega} &#8250 ${data.Nombre}`);
                        $("#LegDocu").val(`${data.docu}`)

                        notify(`Datos Guardados.<br /><span class="fw5">Legajo: ${data.Lega} <br>Nombre:  ${data.Nombre}</span>`, 'success', 5000, 'right')

                    } else {
                        $.notifyClose();
                        ActiveBTN(false, "#btnGuardar", 'Aguarde..', 'Aceptar')
                        $("#alerta_UpdateLega").removeClass("d-none").removeClass("text-success").addClass("text-danger")
                        $(".respuesta_UpdateLega").html("¡Error!")
                        $(".mensaje_UpdateLega").html(`Mensaje: ${data.dato}`);
                        notify(`Error: ${data.dato}`, 'danger', 3000, 'right')
                    }
                }
            });
        }
        if (($('#LegEmpr').val() == null) || ($('#LegEmpr').val() == '' || $('#LegEmpr').val() == '0')) {
            $.notifyClose();
            notify(`<span class="font-weight-bold">Campo empresa es requerido.</span>`, 'danger', 5000, 'right')
            $(".ReqLegEmpr").addClass('text-danger font-weight-bold')
            $('#empresa-tab').tab('show');
            return false;
        } else {
            $(".ReqLegEmpr").removeClass('text-danger font-weight-bold')
        }
        if ($('#LegFeEg').val()) {
            bootbox.confirm({
                // centerVertical: true,
                title: '<span class="fontq font-weight-bold"><i class="bi bi-exclamation-circle"></i> Se está dando de baja el legajo. Fecha de egreso: <span class="ls1">' + $('#LegFeEg').val() + '</span></span>',
                className: 'animate__animated  animate__fadeIn',
                message: '<div id="dataClean"></div>',
                buttons: {
                    confirm: {
                        label: 'Aceptar',
                        className: 'btn-custom btn-sm fontq'
                    },
                    cancel: {
                        label: 'Cancelar',
                        className: 'btn-light btn-sm fontq text-secondary'
                    }
                },
                callback: function (result) {

                    if (result) {

                        submitForm();
                    }
                }
            });
            $('.modal-dialog').addClass('modal-dialog-scrollable')
            $('.modal-header').addClass('border-0')
            $('.modal-body').addClass('mt-n1 p-0')
            getFicNovHor('dataClean', formatDate2($('#LegFeEg').val()), '2099-01-01', $('#LegNume').val());
            e.stopImmediatePropagation();
        } else {
            submitForm();
        }

    });

    $(document).on('shown.bs.modal', '#altaEmpresa', function () {
        $(this).find('[autofocus]').focus();
    });
    $(document).on('shown.bs.modal', '#altaPlanta', function () {
        $(this).find('[autofocus]').focus();
    });
    $(document).on('shown.bs.modal', '#altaconvenio', function () {
        $(this).find('[autofocus]').focus();
    });
    $(document).on('shown.bs.modal', '#altaSector', function () {
        $(this).find('[autofocus]').focus();
    });
    $(document).on('shown.bs.modal', '#altaseccion', function () {
        $(this).find('[autofocus]').focus();
    });
    $(document).on('shown.bs.modal', '#altaGrupo', function () {
        $(this).find('[autofocus]').focus();
    });
    $(document).on('shown.bs.modal', '#altasucur', function () {
        $(this).find('[autofocus]').focus();
    });
    $(document).on('shown.bs.modal', '#altatarea', function () {
        $(this).find('[autofocus]').focus();
    });
    $(document).on('shown.bs.modal', '#altaProvincia', function () {
        $(this).find('[autofocus]').focus();
    });
    $(document).on('shown.bs.modal', '#altaLocalidad', function () {
        $(this).find('[autofocus]').focus();
    });
    $(document).on('shown.bs.modal', '#altahistorial', function () {
        $(this).find('[autofocus]').focus();
    });
    $(document).on('shown.bs.modal', '#altaidentifica', function () {
        $(this).find('[autofocus]').focus();
    });
    $(document).on('shown.bs.modal', '#altaNacion', function () {
        $(this).find('[autofocus]').focus();
    });
    const LegNume = $('#LegNume').val();
    /** NACIONES */
    const formNac = document.querySelector('.Form_Nacion');
    formNac && formNac.addEventListener('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (respuesta) {
                $("#alerta").addClass("d-none");
                // $(".ocultar").addClass("d-none");
                $(".respuesta").html('');
                $(".mensaje").html("");
                $("#btnNacion").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">
                <span class="sr-only"></span>`);
                $("#btnNacion").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    // var dato = data.dato;
                    $("#alerta").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta").html("¡Bien hecho!");
                    $(".mensaje").html(`<br />Se guardó correctamente.<br/ >Cod: ${data.cod} <br />Desc: ${data.desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $(".Form_Nacion")[0].reset();
                    $('.selectjs_naciones').val(null).trigger('change');
                    var newOption = new Option(data.desc, data.cod, true, true);
                    $('.selectjs_naciones').append(newOption).trigger('change');
                    // $('#altaNacion').modal('hide')
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaNacion').on('hidden.bs.modal', function (e) {
                        $("#alerta").addClass("d-none");
                    })
                    $("#btnNacion").html('Aceptar');
                    $("#btnNacion").prop('disabled', false);

                } else if (data.status == "duplicado") {
                    $("#alerta").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-warning");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta").html("¡Atención!");
                    $(".mensaje").html(`El dato ${data.desc} ya existe. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaNacion').on('hidden.bs.modal', function (e) {
                        $("#alerta").addClass("d-none");
                    })
                    $("#btnNacion").html('Aceptar');
                    $("#btnNacion").prop('disabled', false);

                } else if (data.status == "requerido") {
                    $("#alerta").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-danger");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta").html("¡Error!");
                    $(".mensaje").html(`Campo requerido.`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaNacion').on('hidden.bs.modal', function (e) {
                        $("#alerta").addClass("d-none");
                    })
                    $("#btnNacion").html('Aceptar');
                    $("#btnNacion").prop('disabled', false);

                } else {
                    $("#alerta").removeClass("d-none").removeClass("alert-danger").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta").html("Error!");
                    $(".mensaje").html("Error al enviar..");
                    $("#btnNacion").prop('disabled', false);
                }
            }
        });
    });
    /** PROVINCIAS */
    const formProvincias = document.querySelector('.form-provincias');
    formProvincias && formProvincias.addEventListener('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (respuesta) {
                $("#alerta_prov").addClass("d-none");
                $(".respuesta_prov").html('');
                $(".mensaje_prov").html("");
                $("#btnProv").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">
                <span class="sr-only"></span>`);
                $("#btnProv").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    $("#alerta_prov").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_prov").html("¡Bien hecho!");
                    $(".mensaje_prov").html(`<br />Se guardó correctamente.<br/ >Cod: ${data.cod} <br />Desc: ${data.desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $(".form-provincias")[0].reset();
                    $('.selectjs_provincias').val(null).trigger('change');
                    var newOption = new Option(data.desc, data.cod, true, true);
                    $('.selectjs_provincias').append(newOption).trigger('change');
                    // $('#altaProvincia').modal('hide')
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaProvincia').on('hidden.bs.modal', function (e) {
                        $("#alerta_prov").addClass("d-none");
                        $(".form-provincias")[0].reset();
                    })
                    $("#btnProv").html('Aceptar');
                    $("#btnProv").prop('disabled', false);

                } else if (data.status == "duplicado") {
                    $("#alerta_prov").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-warning");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_prov").html("¡Atención!");
                    $(".mensaje_prov").html(`El dato ${data.desc} ya existe. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaProvincia').on('hidden.bs.modal', function (e) {
                        $("#alerta_prov").addClass("d-none");
                        $(".form-provincias")[0].reset();
                    })
                    $("#btnProv").html('Aceptar');
                    $("#btnProv").prop('disabled', false);

                } else if (data.status == "requerido") {
                    $("#alerta_prov").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-danger");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_prov").html("¡Error!");
                    $(".mensaje_prov").html(`Campo requerido.`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaProvincia').on('hidden.bs.modal', function (e) {
                        $("#alerta_prov").addClass("d-none");
                    })
                    $("#btnProv").html('Aceptar');
                    $("#btnProv").prop('disabled', false);

                } else {
                    $("#alerta_prov").removeClass("d-none").removeClass("alert-danger").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_prov").html("Error!");
                    $(".mensaje_prov").html("Error al enviar..");
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaProvincia').on('hidden.bs.modal', function (e) {
                        $("#alerta_prov").addClass("d-none");
                    })
                    $("#btnProv").html('Aceptar');
                    $("#btnProv").prop('disabled', false);
                }
            }
        });
    });
    /** LOCALIDADES */
    const formLocalidad = document.querySelector('.form-localidad');
    formLocalidad && formLocalidad.addEventListener('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_local").addClass("d-none");
                $(".respuesta_local").html('');
                $(".mensaje_local").html("");
                $("#btnLoca").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">
                <span class="sr-only"></span>`);
                $("#btnLoca").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    var dato = data.dato;
                    $("#alerta_local").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_local").html("¡Bien hecho!");
                    $(".mensaje_local").html(`<br />Se guardó correctamente.<br/ >Cod: ${data.cod} <br />Desc: ${data.desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $(".form-localidad")[0].reset();
                    $('.selectjs_localidad').val(null).trigger('change');
                    var newOption = new Option(data.desc, data.cod, true, true);
                    $('.selectjs_localidad').append(newOption).trigger('change');
                    // $('#altaLocalidad').modal('hide')
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaLocalidad').on('hidden.bs.modal', function (e) {
                        $("#alerta_local").addClass("d-none");
                        $(".form-localidad")[0].reset();
                    })
                    $("#btnLoca").html('Aceptar');
                    $("#btnLoca").prop('disabled', false);

                } else if (data.status == "duplicado") {
                    $("#alerta_local").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-warning");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_local").html("¡Atención!");
                    $(".mensaje_local").html(`El dato ${data.desc} ya existe. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaLocalidad').on('hidden.bs.modal', function (e) {
                        $("#alerta_local").addClass("d-none");
                        $(".form-localidad")[0].reset();
                    })
                    $("#btnLoca").html('Aceptar');
                    $("#btnLoca").prop('disabled', false);

                } else if (data.status == "requerido") {
                    $("#alerta_local").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-danger");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_local").html("¡Error!");
                    $(".mensaje_local").html(`Campo requerido.`);
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaLocalidad').on('hidden.bs.modal', function (e) {
                        $("#alerta_local").addClass("d-none");
                    })
                    $("#btnLoca").html('Aceptar');
                    $("#btnLoca").prop('disabled', false);

                } else {
                    $("#alerta_local").removeClass("d-none").removeClass("alert-danger").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_local").html("Error!");
                    $(".mensaje_local").html("Error al enviar..");
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaLocalidad').on('hidden.bs.modal', function (e) {
                        $("#alerta_local").addClass("d-none");
                    })
                    $("#btnLoca").html('Aceptar');
                    $("#btnLoca").prop('disabled', false);
                }
            }
        });
        // return false;
    });
    /** EMPRESAS */
    const formEmpresa = document.querySelector('.form-empresas');
    formEmpresa && formEmpresa.addEventListener('submit', function (e) {
        e.preventDefault();

        $('#altaEmpresa').on('hidden.bs.modal', function (e) {
            $("#alerta_empresa").addClass("d-none");
            $(".form-empresas")[0].reset();
        })

        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (respuesta) {
                $("#alerta_empresa").addClass("d-none");
                $(".respuesta_empresa").html('');
                $(".mensaje_empresa").html("");
                $("#btnEmpresa").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">
            <span class="sr-only"></span>`);
                $("#btnEmpresa").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    var dato = data.dato;
                    $("#alerta_empresa").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_empresa").html("¡Bien hecho!");
                    $(".mensaje_empresa").html(`<br />Se guardó correctamente.<br/ >Cod: ${data.cod} <br />Desc: ${data.desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $(".form-empresas")[0].reset();
                    $('.selectjs_empresas').val(null).trigger('change');
                    var newOption = new Option(data.desc, data.cod, true, true);
                    $('.selectjs_empresas').append(newOption).trigger('change');
                    $("#btnEmpresa").html(`Enviar`);
                    $("#btnEmpresa").prop('disabled', false);

                } else if (data.status == "duplicado") {
                    $("#alerta_empresa").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-warning");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_empresa").html("¡Atención!");
                    $(".mensaje_empresa").html(`El dato ${data.desc} ya existe. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnEmpresa").html(`Enviar`);
                    $("#btnEmpresa").prop('disabled', false);

                } else if (data.status == "requerido") {
                    $("#alerta_empresa").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").removeClass('alert-warning').addClass("alert-danger");
                    // $(".ocultar").addClass("d-none");
                    $(".respuesta_empresa").html("¡Error!");
                    $(".mensaje_empresa").html(`Campo <strong>Razón Social</strong> requerido.`);
                    $("#btnEmpresa").html(`Enviar`);
                    $("#btnEmpresa").prop('disabled', false);

                } else {
                    $("#alerta_empresa").removeClass("d-none").removeClass("alert-danger").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_empresa").html("Error!");
                    $(".mensaje_empresa").html("Error al enviar..");
                    $("#btnEmpresa").prop('disabled', false);
                    $("#btnEmpresa").html(`Enviar`);
                }
            }
        });
    });
    /** PLANTA */
    const formPlantas = document.querySelector('.form-plantas');
    formPlantas && formPlantas.addEventListener('submit', function (e) {
        e.preventDefault();
        const data = new FormData(formPlantas);
        data.append('Desc', data.get('desc_planta'));
        data.append('Estruct', 'Plan');
        altaEstruct(data, ".form-plantas", '.selectjs_plantas', '#altaPlanta', "#btnPlanta");
    });
    /** SECTORES */
    const formSector = document.querySelector('.form-sector');
    formSector && formSector.addEventListener('submit', function (e) {
        e.preventDefault();
        const data = new FormData(formSector);
        data.append('Desc', data.get('desc_sector'));
        data.append('Estruct', 'Sect');
        altaEstruct(data, ".form-sector", '.selectjs_sectores', '#altaSector', "#btnSect");
    });
    /** SECCIONES */
    const formSe2 = document.querySelector('.form-seccion');
    formSe2 && formSe2.addEventListener('submit', function (e) {
        e.preventDefault();
        const data = new FormData(formSe2);
        data.append('Desc', data.get('Se2Desc'));
        data.append('SecCodi', data.get('SecCodi'));
        data.append('Estruct', 'Sec2');
        altaEstruct(data, ".form-seccion", '.selectjs_secciones', '#altaseccion', "#btnSec2");
    });
    /** GRUPOS */
    const formGrupo = document.querySelector('.form-grupo');
    formGrupo && formGrupo.addEventListener('submit', function (e) {
        e.preventDefault();
        const data = new FormData(formGrupo);
        data.append('Desc', data.get('desc_grupo'));
        data.append('Estruct', 'Grup');
        altaEstruct(data, ".form-grupo", '.selectjs_grupos', '#altaGrupo', "#btnGrup");
    });
    /** SUCURSALES */
    const formSucu = document.querySelector('.form-sucur');
    formSucu && formSucu.addEventListener('submit', function (e) {
        e.preventDefault();
        const data = new FormData(formSucu);
        data.append('Desc', data.get('desc_sucur'));
        data.append('Estruct', 'Sucu');
        altaEstruct(data, ".form-sucur", '.selectjs_sucursal', '#altasucur', "#btnSucur");
    });
    /** TAREAS DE PRODUCCIÓN */
    const formTarea = document.querySelector('.form-tarea');
    formTarea && formTarea.addEventListener('submit', function (e) {
        e.preventDefault();
        const data = new FormData(formTarea);
        data.append('Desc', data.get('desc_tarea'));
        data.append('Estruct', 'Tare');
        altaEstruct(data, ".form-tarea", '.selectjs_tarea', '#altatarea', "#btnTarea");
    });
    /** CONVENIOS */
    $('#altaconvenio').on('hidden.bs.modal', function (e) {
        $("#alerta_convenio").addClass("d-none");
        $(".form-convenio")[0].reset();
        $(".form-diasvac")[0].reset();
        $('input[name = dato_conv]').val("alta_convenio");
        $('input[name = codConv]').val("");
        $('#ConvVaca').DataTable().clear().draw().destroy();
        $('#ConvFeri').DataTable().clear().draw().destroy();
        $("#rowConvVac").addClass("d-none"); /** Agregar la clase d-none */
        $("#ConvVacaTabla").addClass("d-none");
        $("#ConvFeriTabla").addClass("d-none");
    })
    $(".form-convenio").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_convenio").addClass("d-none");
                $(".mensaje_convenio").html("");
                $("#btnConv").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">
                    <span class="sr-only"></span>`);
                $("#btnConv").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {

                    var cod = data.cod;
                    var desc = data.desc;
                    var ConDias = data.ConDias;
                    var ConTDias = data.ConTDias;

                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $("#rowConvVac").removeClass("d-none"); /** remover la clase d-none */
                    $('#altaconvenio').modal('handleUpdate') /** Reajuste manualmente la posición del modal si la altura de un modal cambia mientras está abierto (es decir, en caso de que aparezca una barra de desplazamiento). */

                    $('input[name=codConv]').val(`${cod}`);
                    $('input[name=cod-diasvac]').val(`${cod}`).trigger('change');
                    $('input[name=CFConv]').val(`${cod}`).trigger('change');
                    $('input[name=dato_conv]').val("mod_convenio");


                    $('#ConDias').val(`${ConDias}`);
                    $('#ConTDias').val(`${ConTDias}`);

                    $(".respuesta_convenio").html("¡Bien hecho!");
                    $(".mensaje_convenio").html(`<br />Se creó correctamente.<br/ >Cod: ${cod} <br />Desc: ${desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);

                    $('.selectjs_convenio').val(null).trigger('change');
                    var newOption = new Option(desc, cod, true, true);
                    $('.selectjs_convenio').append(newOption).trigger('change');

                    $('#ConvVaca').DataTable({
                        deferRender: true,
                        "ajax": {
                            url: "../../data/getConvVaca.php",
                            type: "GET",
                            'data': {
                                q: cod,
                            },
                        },

                        columns: [{ "class": "align-middle text-center", "data": "CVAnios" }, { "class": "align-middle text-center", "data": "CVMeses" }, { "class": "align-middle text-center", "data": "CVDias" }, { "class": "align-middle text-center", "data": "eliminar" }, { "class": "w-100 align-middle", "data": "null" }],
                        paging: false,
                        scrollY: '40vh',
                        scrollX: true,
                        scrollCollapse: true,
                        searching: false,
                        info: true,
                        ordering: false,
                        language: {
                            "url": "../../js/DataTableSpanish.json"
                        },
                    });
                    $('#ConvFeri').DataTable({
                        deferRender: true,
                        "ajax": {
                            url: "../../data/getConvFeri.php",
                            type: "GET",
                            'data': {
                                q: cod,
                            },
                        },

                        columns: [
                            { "class": "align-middle ls1", "data": "CFFech" },
                            { "class": "align-middle", "data": "CFDesc" },
                            { "class": "align-middle text-center", "data": "CFInFeTR" },
                            { "class": "align-middle", "data": "CFCodM" },
                            { "class": "align-middle", "data": "CFCodJ" },
                            { "class": "align-middle text-center", "data": "CFInfM" },
                            { "class": "align-middle text-center", "data": "CFInfJ" },
                            { "class": "align-middle text-center", "data": "eliminar" },
                            { "class": "align-middle w-100", "data": "null" }
                        ],
                        paging: false,
                        // scrollY: '40vh',
                        // scrollX: false,
                        // scrollCollapse: true,
                        searching: false,
                        info: true,
                        ordering: false,
                        language: {
                            "url": "../../js/DataTableSpanish.json"
                        },
                    });
                    $("#btnConv").html('Aceptar');
                    $("#btnConv").prop('disabled', false);

                } else if (data.status == "okm") {
                    var cod = data.cod;
                    var desc = data.desc;
                    var ConDias = data.ConDias;
                    var ConTDias = data.ConTDias;
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $("#diasvac").removeClass("d-none");
                    $(".respuesta_convenio").html("¡Bien hecho!");
                    $(".mensaje_convenio").html(`<br />Se modificó correctamente.<br/ >Cod: ${cod} <br />Desc: ${desc} <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnConv").html('Aceptar');
                    $("#btnConv").prop('disabled', false);

                } else if (data.status == "nomod") {
                    $("#alerta_convenio").addClass("d-none");
                    $("#btnConv").html('Aceptar');
                    $("#btnConv").prop('disabled', false);

                } else if (data.status == "duplicado") {
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-warning");
                    $(".respuesta_convenio").html("¡Atención!");
                    $(".mensaje_convenio").html(`El dato ${data.desc} ya existe. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnConv").html('Aceptar');
                    $("#btnConv").prop('disabled', false);

                } else if (data.status == "requerido") {
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_convenio").html("¡Error!");
                    $(".mensaje_convenio").html(`Campo requerido.`);
                    $("#btnConv").html('Aceptar');
                    $("#btnConv").prop('disabled', false);

                } else {
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_convenio").html("Error!");
                    $(".mensaje_convenio").html("Error al enviar..");
                    /** Funcion para ocultar el alert al cerra el modal */
                    $('#altaconvenio').on('hidden.bs.modal', function (e) {
                        $("#alerta_convenio").addClass("d-none");
                    })
                    $("#btnConv").html('Aceptar');
                    $("#btnConv").prop('disabled', false);
                }
            }
        });
        // return false;
    });
    /** ALTA CONVENIO DIAS VACACIONES*/
    $(".form-diasvac").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_convenio").addClass("d-none");
            },

            success: function (data) {
                if (data.status == "ok") {
                    $('#ConvVaca').DataTable().ajax.reload();
                    $("#ConvVacaTabla").removeClass("d-none");
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $(".respuesta_convenio").html("Se agregó correctamente.!");
                    $(".mensaje_convenio").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                } else if (data.status == "existe") {
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-warning");
                    $(".respuesta_convenio").html("¡Ya existe!");
                    $(".mensaje_convenio").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $('#ConvVaca').DataTable().ajax.reload();
                } else {
                    $('#ConvVaca').DataTable().ajax.reload();
                }
            }
        });
    });
    /** BORRAR DIAS VAC CONVENIO*/
    $(document).on('click', '.delete_convVaca', function (e) {
        e.preventDefault();
        // var parent = $(this).parent().parent().attr('id');
        var del_cod = $(this).attr('data');
        var del_anios = $(this).attr('data2');
        var del_meses = $(this).attr('data3');
        var del_dias = $(this).attr('data4');
        var del_ConvVac = $(this).attr('data5');

        $.ajax({
            type: "POST",
            url: "alta_opciones.php",
            'data': {
                del_cod,
                del_anios,
                del_meses,
                del_dias,
                del_ConvVac
            },

            success: function (data) {
                if (data.status == "ok_delete") {
                    $('#ConvVaca').DataTable().ajax.reload();
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-info");
                    $(".respuesta_convenio").html("Se eliminó correctamente.!");
                    $(".mensaje_convenio").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                } else {
                    $('#ConvVaca').DataTable().ajax.reload();
                }
            }
        });
    });
    /** ALTA CONVENIO FERIADOS*/
    $(".form-fericonv").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_convenio").addClass("d-none");
            },
            success: function (data) {
                if (data.status == "ok") {
                    // $(".form-fericonv")[0].reset();
                    $("#CFFech").val('');
                    $("#CFDesc").val('');
                    // $("#CFInFeTR").checked="";
                    // $("#CFInFeTR").checked=false;
                    $("#CFCodM").val('');
                    $("#CFCodM3").val('');
                    $("#CFCodM2").val('');
                    // $("#CFInfM").val('');
                    // $("#CFInfJ").val('');
                    $("#CFCodJ").val('');
                    $("#CFCodJ3").val('');
                    $("#CFCodJ2").val('');

                    $("#ConvFeriTabla").removeClass("d-none");
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $(".respuesta_convenio").html("Se agregó correctamente.!");
                    $(".mensaje_convenio").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);

                    var cod = data.cod;

                    $('#ConvFeri').DataTable().ajax.reload();

                } else if (data.status == "existe") {
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-warning");
                    $(".respuesta_convenio").html("¡Ya existe!");
                    $(".mensaje_convenio").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $('#ConvFeri').DataTable().ajax.reload();
                } else if (data.status == "requeridos") {
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_convenio").html("Campos requeridos!");
                    $(".mensaje_convenio").html(`<br />Fecha - Descripción. <a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $('#ConvFeri').DataTable().ajax.reload();
                } else {
                    $('#ConvFeri').DataTable().ajax.reload();
                }
            }
        });
    });
    /** BORRAR FERIADOS CONVENIO*/
    $(document).on('click', '.delete_convFeri', function (e) {
        e.preventDefault();
        // var parent = $(this).parent().parent().attr('id');
        var CFConv = $(this).attr('data');
        var CFDesc = $(this).attr('data2');
        var CFFech = $(this).attr('data3');
        var del_ConvFeri = $(this).attr('data4');

        $.ajax({
            type: "POST",
            url: "alta_opciones.php",
            'data': {
                CFConv,
                CFDesc,
                CFFech,
                del_ConvFeri,
            },

            success: function (data) {
                if (data.status == "ok_delete") {
                    $('#ConvFeri').DataTable().ajax.reload();
                    $("#alerta_convenio").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-info");
                    $(".respuesta_convenio").html("Se eliminó correctamente.!");
                    $(".mensaje_convenio").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                } else {
                    $('#ConvFeri').DataTable().ajax.reload();
                }
            }
        });
    });
    /** ALTA HISTORIAl INGRESOS LEGAJOS*/
    $('#altahistorial').on('hidden.bs.modal', function (e) {
        $("#alerta_historial").addClass("d-none");
        $(".form-perineg")[0].reset();
        $("#btnHisto").html('Aceptar');
        $("#btnHisto").prop('disabled', false);

        $('#InEgFeIn').val('').prop('disabled', false)
        $('#trash_InEgFeIn').removeClass('d-none')
        $('#InEgFeEg').val('')
        $('#InEgCaus').val('')
        $('#btnHisto').attr('type', 'submit')
        $('#btnHisto').show()
        $('#btnHisto2').hide()
    })
    $(".form-perineg").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_historial").addClass("d-none");
                $("#btnHisto").html(`Aguarde`);
                $("#btnHisto").prop('disabled', true);
            },
            success: function (data) {
                if (data.status == "ok") {
                    notify(data.dato, 'success', 3000, 'right')
                    tablePerInEg();
                    $('#altahistorial').modal('hide');

                } else {
                    notify(data.dato, 'danger', 3000, 'right')
                    $("#btnHisto").prop('disabled', false);
                    $("#btnHisto").html(`Aceptar`);

                }
            }
        });
    });
    /** BORRAR HISTORIAl INGRESOS LEGAJOS*/
    $(document).on('click', '.delete_perineg', function (e) {
        e.preventDefault();
        // var parent = $(this).parent().parent().attr('id');
        var DelInEgFeIn = $(this).attr('data');
        var DelInEgLega = $(this).attr('data2');
        var DelPerineg = $(this).attr('data3');

        $.ajax({
            type: "POST",
            url: "alta_opciones.php",
            'data': {
                DelInEgFeIn,
                DelInEgLega,
                DelPerineg
            },
            success: function (data) {
                if (data.status == "ok_delete") {
                    notify(data.dato, 'success', 3000, 'right')
                    tablePerInEg();
                } else {
                    notify(data.dato, 'danger', 3000, 'right')
                    tablePerInEg();
                }
            }
        });
    });
    /** EDITA HISTORIAl INGRESOS LEGAJOS*/
    $(document).on('click', '.edita_perineg', function (e) {
        e.preventDefault();
        // var parent = $(this).parent().parent().attr('id');
        let InEgFeIn = $(this).attr('data');
        let InEgLega = $(this).attr('data2');
        let Perineg = $(this).attr('data3');
        let InEgFeEg = $(this).attr('data4');
        let InEgCaus = $(this).attr('data5');

        $('#InEgFeIn').val(InEgFeIn).prop('disabled', true)
        $('#trash_InEgFeIn').addClass('d-none')
        $('#InEgFeEg').val(InEgFeEg)
        $('#InEgCaus').val(InEgCaus)
        // $('#btnHisto').attr('type', 'button')
        $('#altahistorial').modal('show')
        $('#btnHisto').hide()
        $('#btnHisto2').show()

        let btnHisto2 = document.getElementById('btnHisto2')

        btnHisto2.addEventListener("click", function (e) {

            e.preventDefault()
            e.stopImmediatePropagation()

            btnHisto2.disabled = true
            btnHisto2.innerHTML = 'Aguarde'

            let formData = new FormData();
            formData.append("dato", 'edita_perineg');
            formData.append("InEgFeIn", $('#InEgFeIn').val());
            formData.append("InEgLega", InEgLega);
            formData.append("InEgFeEg", $('#InEgFeEg').val());
            formData.append("InEgCaus", $('#InEgCaus').val());

            axios.post('alta_opciones.php', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                btnHisto2.disabled = false
                btnHisto2.innerHTML = 'Aceptar'

                if (response.data.status != 'ok') {
                    notify(response.data.dato, 'danger', 3000, 'right')
                    return
                }
                notify(response.data.dato, 'success', 3000, 'right')
                tablePerInEg();
                $('#altahistorial').modal('hide')
            }).catch(error => {
                btnHisto2.disabled = false
                btnHisto2.innerHTML = 'Aceptar'
                console.error('Error:', error);
            });

        }, false)
    });
    /** ALTA PERSONAL PREMIOS */
    $('#altapremios').on('hidden.bs.modal', function (e) {
        $("#alerta_premios").addClass("d-none");
        $("#btnPremios").html('Aceptar');
        $("#btnPremios").prop('disabled', false);
        $('.selectjs_premios').val(null).trigger("change");
    })

    $(".form-premios").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_premios").addClass("d-none");
                $("#btnPremios").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">`);
                $("#btnPremios").prop('disabled', true);
            },

            success: function (data) {
                if (data.status == "ok") {
                    tablePerPremio();
                    $("#alerta_premios").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $(".respuesta_premios").html("Se agregó correctamente.!");
                    $(".mensaje_premios").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);

                    $("#btnPremios").html('Aceptar');
                    $("#btnPremios").prop('disabled', false);
                    $('#altapremios').modal('hide');

                } else if (data.status == "existe") {
                    tablePerPremio();
                    $("#alerta_premios").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-warning");
                    $(".respuesta_premios").html("¡Ya existe!");
                    $(".mensaje_premios").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnPremios").html('Aceptar');
                    $("#btnPremios").prop('disabled', false);
                    // $('#ConvVaca').DataTable().ajax.reload();
                } else {
                    tablePerPremio();
                    $("#alerta_premios").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_premios").html("¡Error!");
                    $(".mensaje_premios").html(`<br />${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnPremios").html('Aceptar');
                    $("#btnPremios").prop('disabled', false);
                    // $('#ConvVaca').DataTable().ajax.reload();
                }
            }
        });
    });
    /** BORRAR PERSONAL PREMIOS*/
    $(document).on('click', '.delete_perpremi', function (e) {
        e.preventDefault();
        // var parent = $(this).parent().parent().attr('id');
        var DelLPreLega = $(this).attr('data');
        var DelLPreCodi = $(this).attr('data2');
        var DelPerPremi = $(this).attr('data3');

        $.ajax({
            type: "POST",
            url: "alta_opciones.php",
            'data': {
                DelLPreLega,
                DelLPreCodi,
                DelPerPremi
            },

            success: function (data) {
                if (data.status == "ok_delete") {
                    tablePerPremio();
                } else {
                    tablePerPremio();
                }
            }
        });
    });
    /** ALTA PERSONAL OTROS CONCEPTOS */
    $('#altaconceptos').on('hidden.bs.modal', function (e) {
        $("#alerta_conceptos").addClass("d-none");
        $("#btnconceptos").html('Aceptar');
        $("#btnconceptos").prop('disabled', false);
        $('.selectjs_conceptos').val(null).trigger("change");
        $('#OTROConValor').val(null);
    })

    $(".form-otrosconleg").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_conceptos").addClass("d-none");
                $("#btnconceptos").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">`);
                $("#btnconceptos").prop('disabled', true);
            },

            success: function (data) {
                if (data.status == "ok") {
                    tableOtrosConLeg();
                    $("#alerta_conceptos").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $(".respuesta_conceptos").html("Se agregó correctamente.!");
                    $(".mensaje_conceptos").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);

                    $("#btnconceptos").html('Aceptar');
                    $("#btnconceptos").prop('disabled', false);
                    $('#altaconceptos').modal('hide');

                } else if (data.status == "existe") {
                    tableOtrosConLeg();
                    $("#alerta_conceptos").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-warning");
                    $(".respuesta_conceptos").html("¡Ya existe!");
                    $(".mensaje_conceptos").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnconceptos").html('Aceptar');
                    $("#btnconceptos").prop('disabled', false);
                    // $('#ConvVaca').DataTable().ajax.reload();
                } else {
                    tableOtrosConLeg();
                    $("#alerta_conceptos").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_conceptos").html("¡Error!");
                    $(".mensaje_conceptos").html(`<br />${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnconceptos").html('Aceptar');
                    $("#btnconceptos").prop('disabled', false);
                }
            }
        });
    });
    /** BORRAR PERSONAL OTROS CONCEPTOS*/
    $(document).on('click', '.delete_otrosconleg', function (e) {
        e.preventDefault();
        // var parent = $(this).parent().parent().attr('id');
        var OTROConLega = $(this).attr('data');
        var OTROConCodi = $(this).attr('data2');
        var DelOtroConLeg = $(this).attr('data3');

        $.ajax({
            type: "POST",
            url: "alta_opciones.php",
            'data': {
                OTROConLega,
                OTROConCodi,
                DelOtroConLeg
            },

            success: function (data) {
                if (data.status == "ok_delete") {
                    tableOtrosConLeg();
                } else {
                    tableOtrosConLeg();
                }
            }
        });
    });
    /** ALTA PERSONAL HORARIO ALTERNATIVO */
    $('#altahorarioal').on('hidden.bs.modal', function (e) {
        $("#alerta_horarioal").addClass("d-none");
        $("#btnhorarioal").html('Aceptar');
        $("#btnhorarioal").prop('disabled', false);
        $('.selectjs_horarioal').val(null).trigger("change");
        $('#OTROConValor').val(null);
    })

    $(".form-PerHoAl").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_horarioal").addClass("d-none");
                $("#btnhorarioal").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">`);
                $("#btnhorarioal").prop('disabled', true);
            },

            success: function (data) {
                if (data.status == "ok") {
                    tablePerHoAlt();
                    $("#alerta_horarioal").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $(".respuesta_horarioal").html("Se agregó correctamente.!");
                    $(".mensaje_horarioal").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);

                    $("#btnhorarioal").html('Aceptar');
                    $("#btnhorarioal").prop('disabled', false);
                    $('#altahorarioal').modal('hide');

                } else if (data.status == "existe") {
                    tablePerHoAlt();
                    $("#alerta_horarioal").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-warning");
                    $(".respuesta_horarioal").html("¡Ya existe!");
                    $(".mensaje_horarioal").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnhorarioal").html('Aceptar');
                    $("#btnhorarioal").prop('disabled', false);
                    // $('#ConvVaca').DataTable().ajax.reload();
                } else {
                    tablePerHoAlt();
                    $("#alerta_horarioal").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_horarioal").html("¡Error!");
                    $(".mensaje_horarioal").html(`<br />${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnhorarioal").html('Aceptar');
                    $("#btnhorarioal").prop('disabled', false);
                }
            }
        });
    });
    /** BORRAR PERSONAL HORARIO ALTERNATIVO*/
    $(document).on('click', '.delete_perhoalt', function (e) {
        e.preventDefault();
        var LeHALega = $(this).attr('data');
        var LeHAHora = $(this).attr('data2');
        var DelPerHoAl = $(this).attr('data3');

        $.ajax({
            type: "POST",
            url: "alta_opciones.php",
            'data': {
                LeHALega,
                LeHAHora,
                DelPerHoAl
            },

            success: function (data) {
                if (data.status == "ok_delete") {
                    tablePerHoAlt();
                } else {
                    tablePerHoAlt();
                }
            }
        });
    });
    /** ALTA IDENTIFICA */
    $('#altaidentifica').on('hidden.bs.modal', function (e) {
        $("#alerta_identifica").addClass("d-none");
        $("#btnidentifica").html('Aceptar');
        $("#btnidentifica").prop('disabled', false);
        $('.form-Identifica')[0].reset();
    })

    $(".form-Identifica").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_identifica").addClass("d-none");
                $("#btnidentifica").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">`);
                $("#btnidentifica").prop('disabled', false);
            },

            success: function (data) {
                $.notifyClose();
                if (data.status == "ok") {
                    ls.remove('#Identifica-table');
                    tableIdentifica('#Identifica-table');
                    $("#alerta_identifica").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $(".respuesta_identifica").html("Se agregó correctamente.!");
                    $(".mensaje_identifica").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);

                    $("#btnidentifica").html('Aceptar');
                    $("#btnidentifica").prop('disabled', false);
                    $('#altaidentifica').modal('hide');
                    notify('Datos guardados correctamente<br>' + data.dato, 'success', 3000, 'right')

                } else if (data.status == "existe") {
                    ls.remove('#Identifica-table');
                    tableIdentifica('#Identifica-table');
                    $("#alerta_identifica").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-warning");
                    $(".respuesta_identifica").html("¡Ya existe!");
                    $(".mensaje_identifica").html(`<br />${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnidentifica").html('Aceptar');
                    $("#btnidentifica").prop('disabled', false);
                    notify(data.dato, 'info', 3000, 'right')
                } else {
                    tableIdentifica('#Identifica-table');
                    $("#alerta_identifica").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_identifica").html("¡Error!");
                    $(".mensaje_identifica").html(`<br />${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnidentifica").html('Aceptar');
                    $("#btnidentifica").prop('disabled', false);
                    notify(data.dato, 'danger', 3000, 'right')
                }
            }
        });
    });
    /** BORRAR IDENTIFICA*/
    $(document).on('click', '.delete_identifica', function (e) {
        e.preventDefault();
        const IDCodigo = $(this).attr('data');
        const IDLegajo = $(this).attr('data2');
        const DelIdentifica = $(this).attr('data3');

        $.ajax({
            type: "POST",
            url: "alta_opciones.php",
            'data': {
                IDCodigo,
                IDLegajo,
                DelIdentifica
            },

            success: function (data) {
                if (data.status == "ok_delete") {
                    ls.remove('#Identifica-table');
                    tableIdentifica('#Identifica-table');
                } else {
                    tableIdentifica('#Identifica-table');
                }
            }
        });
    });
    /** UPDATE GRUPO CAPTURADORES */
    $('.selectjs_grupocapt').on('select2:select', function (e) {
        e.preventDefault();

        const LegajoGrHa = $('#LegajoGrHa').val()
        const GrupoHabi = $('#GrupoHabi').val()
        const LegGrHa2 = $('#LegGrHa2').val()

        $.ajax({
            type: "POST",
            url: "alta_opciones.php",
            'data': {
                LegajoGrHa,
                GrupoHabi,
                LegGrHa2
            },
            beforeSend: function (data) {
                $("#alerta_grupocapt").addClass("d-none");
                $("#btngrupocapt").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">`);
                $("#btngrupocapt").prop('disabled', false);
            },
            success: function (data) {
                if (data.status == "ok") {
                    $('#LegGrHa').prop('value', data.LegGrHa);
                    ls.remove('#GrupoCapt');
                    tableGrupoCapt('#GrupoCapt');
                    $("#btngrupocapt").html('Aceptar');
                    $("#btngrupocapt").prop('disabled', false);

                } else {
                    tableGrupoCapt('#GrupoCapt');
                    $("#btngrupocapt").html('Aceptar');
                    $("#btngrupocapt").prop('disabled', false);
                }
            }
        });
    });
    /** ALTA PERRelo */
    $('#altaPerRelo').on('hidden.bs.modal', function (e) {
        $("#alerta_PerRelo").addClass("d-none");
        $("#btnPerRelo").html('Aceptar');
        $("#btnPerRelo").prop('disabled', false);
        $('.form-PerRelo')[0].reset();
        $('.selectjs_Relojes').val(null).trigger('change');
    })

    $(".form-PerRelo").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $("#alerta_PerRelo").addClass("d-none");
                $("#btnPerRelo").html(`Aceptar <div class="fontq spinner-border spinner-border-sm text-white" role="status">`);
                $("#btnPerRelo").prop('disabled', false);
            },

            success: function (data) {
                if (data.status == "ok") {
                    ls.remove('#TablePerRelo');
                    tablePerRelo('#TablePerRelo');
                    $("#alerta_PerRelo").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-success");
                    $(".respuesta_PerRelo").html("Se agregó correctamente.!");
                    $(".mensaje_PerRelo").html(`<br />Registro: ${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);

                    $("#btnPerRelo").html('Aceptar');
                    $("#btnPerRelo").prop('disabled', false);
                    $('#altaPerRelo').modal('hide');

                } else if (data.status == "existe") {
                    ls.remove('#TablePerRelo');
                    tablePerRelo('#TablePerRelo');
                    $("#alerta_PerRelo").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-warning");
                    $(".respuesta_PerRelo").html("¡Ya existe!");
                    $(".mensaje_PerRelo").html(`<br />${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnPerRelo").html('Aceptar');
                    $("#btnPerRelo").prop('disabled', false);
                    // $('#ConvVaca').DataTable().ajax.reload();
                } else {
                    tablePerRelo('#TablePerRelo');
                    $("#alerta_PerRelo").removeClass("d-none").removeClass("alert-danger").removeClass("alert-info").removeClass("alert-warning").removeClass("alert-success").addClass("alert-danger");
                    $(".respuesta_PerRelo").html("¡Error!");
                    $(".mensaje_PerRelo").html(`<br />${data.dato}<a href='#' data-dismiss='modal' class='float-right alert-link fw5 mt-2'>Cerrar</a>`);
                    $("#btnPerRelo").html('Aceptar');
                    $("#btnPerRelo").prop('disabled', false);
                }
            }
        });
    });
    /** BORRAR PERRelo*/
    $(document).on('click', '.delete_perrelo', function (e) {
        e.preventDefault();
        const RelRelo = $(this).attr('data');
        const RelReMa = $(this).attr('data2');
        const RelLega = $(this).attr('data3');
        const DelPerrelo = $(this).attr('data4');

        $.ajax({
            type: "POST",
            url: "alta_opciones.php",
            'data': {
                RelRelo,
                RelReMa,
                RelLega,
                DelPerrelo
            },

            success: function (data) {
                if (data.status == "ok_delete") {
                    ls.remove('#TablePerRelo');
                    tablePerRelo('#TablePerRelo');
                } else {
                    tablePerRelo('#TablePerRelo');
                }
            }
        });
    });
    const altaEstruct = (FormData, selectorForm, selectorSelect, selectorModal, btn) => {
        const post = axios.post('../../app-data/estructuras/alta/', FormData);
        post.then((rs) => {

            $(btn).prop('disabled', true); // Deshabilitar botón

            if (rs.data.MESSAGE != "OK") { // Si no es OK
                notify(rs.data.MESSAGE, 'danger', 3000, 'right'); // Mostrar mensaje de error
                return;
            }

            notify('Registro creado', 'success', 3000, 'right'); // Mostrar mensaje de exito
            const Cod = rs.data.DATA.Cod ?? '';
            const Desc = rs.data.DATA.Desc ?? '';
            $(selectorForm)[0].reset(); // Limpiar formulario

            if (Cod != '' && Desc != '') {
                $(selectorSelect).val(null).trigger('change'); // Limpiar select
                var newOption = new Option(Desc, Cod, true, true);
                $(selectorSelect).append(newOption).trigger('change');
                if (selectorSelect == '.selectjs_sectores') {
                    $('.selectjs_secciones').val(null).trigger('change');
                }
            }
            $(selectorModal).modal('hide');

        }).catch((error) => {
            notify('Error', 'danger', 3000, 'right'); // Mostrar mensaje de error
            console.log(error);
        }).finally(() => {
            $(btn).prop('disabled', false); // Habilitar botón
        });
    }
});