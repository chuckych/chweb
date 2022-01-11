
$('.select2').select2({
    minimumResultsForSearch: -1,
    placeholder: "Seleccionar"
});

function clean() {
    $('#table-usuarios').DataTable().search('').draw();
    $("input[name=_id]").val('');
    $("input[name=_name]").val('');
    $("input[name=_email]").val('');
    $("input[name=_enable]").prop('checked', false);
}
function enableUser(selector) {
    if ($(selector).is(':checked')) {
        $('.text-estado').html('Usuario Activo');
        $('.text-estado').addClass('text-success');
        $('.text-estado').removeClass('text-danger');
        $('.text-enable').html('Desactivar usuario');
        $('.text-enable').addClass('btn btn-outline-danger');
        $('.text-enable').removeClass('btn btn-outline-success');
    } else {
        $('.text-estado').html('Usuario Inactivo');
        $('.text-estado').addClass('text-danger');
        $('.text-estado').removeClass('text-success');
        $('.text-enable').html('Activar usuario');
        $('.text-enable').addClass('btn btn-outline-success');
        $('.text-enable').removeClass('btn btn-outline-danger');
    }
}

$("#Refresh").on("click", function () {
    CheckSesion()
// $(document).on("click", "#Refresh", function (e) {
    $('#table-usuarios').DataTable().ajax.reload();
    $("tbody").addClass("opa2");
    $("#Refresh").prop("disabled", true);
    $("#Refresh").html("Actualizando!.");
    $('#VerUsuarios').removeClass('d-none')
    $('#rowNuevoUsuario').addClass('d-none')
    $("#btnSubmitUser").prop("disabled", false);
    clean()
});
$('#table-usuarios').DataTable({
    "initComplete": function (settings, json) {
        CheckSesion()
    },
    "drawCallback": function (settings) {
        $("tbody").removeClass("opa2");
        $("#Refresh").prop("disabled", false);
        $("#Refresh").html("Actualizar Grilla");
    },
    orderFixed: [[1, "asc"], [2, "asc"]],
    rowGroup: {
        dataSrc: ['enable2', 'trained']
    },
    iDisplayLength: -1,
    bProcessing: true,
    // search:{ search:("HR")},
    ajax: {
        url: "array_usuarios.php",
        type: "POST",
        dataSrc: "usuarios",
        "data": function (data) { },
    },
    createdRow: function (row, data, dataIndex) {
        $(row).addClass('animate__animated animate__fadeIn align-middle');
    },
    columns: [
        {
            "class": 'fw4',
            "data": "usuario",
        },
        {
            "class": 'd-none',
            "data": "enable2",
        },
        {
            "class": 'd-none',
            "data": "trained",
        },
        {
            "class": '',
            "data": "estado",
        },
        {
            "class": '',
            "data": "date",
        },
        {
            "class": '',
            "data": "entrenar",
        },
        {
            "class": '',
            "data": "mod",
        },
        {
            "class": '',
            "data": "del",
        },
        {
            "class": '',
            "data": "null",
        },

    ],
    deferRender: true,
    paging: false,
    searching: true,
    scrollY: '50vh',
    scrollX: true,
    scrollCollapse: 1,
    info: true,
    language: {
        "url": "../../js/DataTableSpanishShort2.json"
    },

});

$(document).on("click", ".EliminaUsuario", function (e) {
    CheckSesion()
    var _tk     = $(this).attr('data2');
    var _nombre = $(this).attr('data1');
    var _id     = $(this).attr('data');
    $("input[name=_id]").attr('disabled', false);
    $("input[name=_id]").attr('readonly', false);
    $("#_nombreUsuario").html(_nombre)
    $('#d_tk').val(_tk)
    $('#d_nombre').val(_nombre)
    $('#d_id').val(_id)

}); 

$(document).on("click", ".EntrenarUsuario", function (e) {
    CheckSesion()
    var ide = parseFloat($(this).attr('data'));
    var itk = ($(this).attr('data2'));
    var iname = ($(this).attr('data1'));
    $('#divEntrenar').removeClass('d-none')
    $('#VerUsuarios').addClass('d-none')
    $('#Encabezado').html('Enrolamiento Facial')
    // $('#divEntrenar').html('<div class="col-12 py-2" style="background-color: #ececec;"><button type="button" class="float-right btn btn-custom border btn-sm px-4 fontq" id="btnBack">Volver</button></div>')
    $('#divEntrenar').html('<div class="col-12 py-2"><p class="m-0 float-left fw4">'+iname+'</p><button type="button" class="float-right btn btn-custom border px-4 fontq" id="btnBack">Volver</button></div><div class="embed-responsive embed-responsive-21by9" style="height:70vh;"><iframe scrolling="yes" frameborder="1" width="100%" height="1100px;" name="contentFrame" class="embed-responsive-item" src="entrenar.php?u_id='+ide+'" allowfullscreen"></iframe></div>')
}); 

$(document).on("click", "#btnBack", function (e) {
    CheckSesion()
    $('#table-usuarios').DataTable().search('').draw();
    $('#table-usuarios').DataTable().ajax.reload();
    $('#divEntrenar').addClass('d-none')
    $('#VerUsuarios').removeClass('d-none')
    $('#divEntrenar').html('');
    $('#Encabezado').html('Usuarios Mobile')
}); 

$(document).on("click", "#NuevoUsuario", function (e) {
    e.preventDefault();
    CheckSesion()
    $("#Titulo").html('Nuevo')
    $("#btnSubmitUser").html("Crear");
    $("input[name=_id]").attr('readonly', false);
    $("input[name=alta]").val('true');
    $('#VerUsuarios').addClass('d-none')
    $('#rowNuevoUsuario').removeClass('d-none')
    fadeInOnly('#rowNuevoUsuario')
    clean()
    $("#_enable").prop('checked', true);
});

$(document).on("click", "#cancelUsuario", function (e) {
    $('#VerUsuarios').removeClass('d-none')
    $('#rowNuevoUsuario').addClass('d-none')
    fadeInOnly('#VerUsuarios')
    clean()
    $('#Encabezado').html('Usuarios Mobile')
});

$(document).on("click", ".ModificarUsuario", function (e) {
    CheckSesion()
    $("#Titulo").html('Editar')
    $("#btnSubmitUser").html("Guardar");
    $("input[name=alta]").val('update');
    $('#VerUsuarios').addClass('d-none')
    $('#rowNuevoUsuario').removeClass('d-none')
    $("input[name=_id]").attr('readonly', false);

    var id     = $(this).attr('data');
    var nombre = $(this).attr('data1');
    $('#Encabezado').html('Editar usuario: '+nombre)

    function GetUser() {
       
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: "array_user.php",
            'data': {
                id: id
            },
            beforeSend:function(){
                $('#rowNuevoUsuario').addClass('bg-light')
                $("input[name=_id]").attr('disabled', true);
                $("input[name=_name]").attr('disabled', true);
                $("input[name=_email]").attr('disabled', true);
                // $("input[name=_enable]").attr('disabled', true);
                $("#btnSubmitUser").prop("disabled", true);
            },
            success: function (respuesta) {
                $("#btnSubmitUser").prop("disabled", false);
                $('#rowNuevoUsuario').removeClass('bg-light')
                $("input[name=_id]").attr('disabled', false);
                $("input[name=_id]").attr('readonly', true);
                $("input[name=_name]").attr('disabled', false);
                $("input[name=_email]").attr('disabled', false);
                // $("input[name=_enable]").attr('disabled', false);
                $("input[name=_id]").val(respuesta.id);
                $("input[name=_name]").val(respuesta.name);
                $("input[name=_email]").val(respuesta.email);
                $("input[name=_enable]").prop('checked', false);

                if(respuesta.enable == true) {
                    $("#_enable").prop('checked', true)
                }else{
                    $("#_disabled").prop('checked', true);
                }  
            },
            error: function () {
                $("input[name=_id]").val('');
                $("input[name=_name]").val('');
                $("input[name=_email]").val('');
                $("input[name=_enable]").prop('checked', false);       
                $("input[name=_enable]").attr('disabled', false);        
            }
        });
    }
    GetUser()
});


$("#DUser").bind("submit", function (e) {
    e.preventDefault();
    $.ajax({
        type: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        // dataType: "json",
        beforeSend: function (data) {
            $("#btnsi").prop("disabled", true);
            $("#btnsi").html("Eliminando.!");
            $("#Refresh").prop("disabled", true);
            $("#Refresh").html("Actualizando!.");
        },
        success: function (data) {
            if (data.status == "ok") {
                clean()
                $('#table-usuarios').DataTable().ajax.reload();
                $("tbody").addClass("opa2");
                $("#btnsi").prop("disabled", false);
                $("#btnsi").html("S&iacute;");
                $("#Refresh").prop("disabled", true);
                $("#Refresh").html("Actualizando!.");
                $('#EliminaUsuario').modal('hide');

            } else {
                $('#table-usuarios').DataTable().ajax.reload();
                $("tbody").addClass("opa2");
                $("#btnsi").prop("disabled", false);
                $("#btnsi").html("S&iacute;");
                $("#Refresh").prop("disabled", false);
                $("#Refresh").html("Actualizar Grilla");
            }
        },
        error: function () {
            $('#table-usuarios').DataTable().ajax.reload();
            $("tbody").addClass("opa2");
            $("#btnsi").prop("disabled", false);
            $("#btnsi").html("S&iacute;");
            $("#Refresh").prop("disabled", false);
            $("#Refresh").html("Actualizar Grilla");

        }
    });
});

$("#CrearUsuario").bind("submit", function (e) {
    e.preventDefault();
    $.ajax({
        type: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        // dataType: "json",
        beforeSend: function (data) {
            $("#btnSubmitUser").prop("disabled", true);
            $("#btnSubmitUser").html("Procesando.!");
        },
        success: function (data) {
            if (data.status == "ok") {
                // clean()
                $('#table-usuarios').DataTable().search(data._name).draw();
                $('#table-usuarios').DataTable().ajax.reload();
                $("#Refresh").prop("disabled", true);
                $("#Refresh").html("Actualizando!");
                $("#btnSubmitUser").prop("disabled", false);
                $("#btnSubmitUser").html("Crear");
                
                $('#divRespuesta').removeClass('d-none')
                $('#divRespuesta').html('<div class="alert alert-success fontq" role="alert"><b>' + data.MESSAGE + '</b></div>')

                setTimeout(function () {
                    $('#divRespuesta').addClass('d-none')
                    $('#divRespuesta').html('')
                    $('#VerUsuarios').removeClass('d-none')
                    fadeInOnly('#VerUsuarios')
                    $('#rowNuevoUsuario').addClass('d-none')
                }, 1000);

            } else {
                // $('#table-usuarios').DataTable().ajax.reload();
                $("#Refresh").prop("disabled", true);
                $("#Refresh").html("Actualizando!");
                $("#btnSubmitUser").prop("disabled", false);
                $("#btnSubmitUser").html("Crear");

                $('#divRespuesta').removeClass('d-none')
                $('#divRespuesta').html('<div class="alert alert-danger" role="alert">' + data.MESSAGE + '</div>')
                setTimeout(function () {
                    $('#divRespuesta').addClass('d-none')
                    $('#divRespuesta').html('')
                }, 6000);
            }
        },
        error: function () {
            $("#Refresh").prop("disabled", true);
            $("#Refresh").html("Actualizando!");
            $("#btnSubmitUser").prop("disabled", false);
            $("#btnSubmitUser").html("Crear");

            $('#divRespuesta').removeClass('d-none')
            $('#divRespuesta').html('<div class="alert alert-danger" role="alert">' + data.MESSAGE + '</div>')
            setTimeout(function () {
                $('#divRespuesta').addClass('d-none')
                $('#divRespuesta').html('')
            }, 6000);
        }
    });
});


$(".selectjs_cuentaToken").select2({
    multiple: false,
    language: "es",
    placeholder: "Cambiar de Cuenta",
    // minimumInputLength: '0',
    minimumResultsForSearch: -1,
    // maximumInputLength: '10',
    selectOnClose: false,
    language: {
        noResults: function () {
            return 'No hay resultados..'
        },
        inputTooLong: function (args) {
            var message = 'Máximo ' + '10' + ' caracteres. Elimine ' + overChars + ' caracter';
            if (overChars != 1) {
                message += 'es'
            }
            return message
        },
        searching: function () {
            return 'Buscando..'
        },
        errorLoading: function () {
            return 'Sin datos..'
        },
        inputTooShort: function () {
            return 'Ingresar ' + '0' + ' o mas caracteres'
        },
        maximumSelected: function () {
            return 'Puede seleccionar solo una opción'
        }
    },
    ajax: {
        url: "../GetTokenCuenta.php",
        dataType: "json",
        type: "POST",
        // delay: opt2["delay"],
        data: function (params) {
            return {
            }
        },
        processResults: function (data) {
            return {
                results: data
            }
        },
    }
});
$('.selectjs_cuentaToken').on('select2:select', function (e) {
    CheckSesion()
    $("#RefreshToken").submit();
});
$("#RefreshToken").bind("submit", function (e) {
    e.preventDefault();
    $.ajax({
        type: $(this).attr("method"),
        url: $(this).attr("action"),
        data: $(this).serialize(),
        // dataType: "json",
        beforeSend: function (data) {
        },
        success: function (data) {
            if (data.status == "ok") {
                $('#table-usuarios').DataTable().ajax.reload();
                $("tbody").addClass("opa2");
                clean()
            }
        },
        error: function () {
        }
    });
});
