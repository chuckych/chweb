$(function () {
    "use strict";
    getPag("#logoutLogin", "salir");
    $(document).prop('title', 'Ingresar');
    $('#tarjeta').focus();
    $('#tarjeta').mask('AAAAAAAAAAAAAAA', { selectOnFocus: true });

    $('#usuario').focus();
    $('#usuario').on('click', function (e) {
        $('#usuario').select();
    });
    $('#clave').on('click', function (e) {
        $('#clave').select();
    });

    $("#changeLogin").click(function () {
        let val = $("#changeLogin").val();
        let route = "";
        switch (val) {
            case "rfid":
                route = "routes.php/?page=log_rfid";
                break;
            case "user":
                route = "routes.php/?page=log_user";
                break;
        }
        fetch(route).then(response => response.text()).then(data => {
            $("#contenedor").html(data);
            $("input").attr("autocomplete", "off");
        });
    });
    $("#formRFID").bind("submit", function (e) {
        e.preventDefault();
        if ($("#tarjeta").val() == "") {
            $.notifyClose();
            $("#tarjeta").focus();
            $("#tarjeta").addClass("is-invalid");
            let textErr = `<div class="d-inline-flex align-items-center text-danger font-weight-bold"><span class="bi bi-credit-card-2-front me-2 font15"></span>Ingrese una Tarjeta<div>`;
            notify(textErr, 'danger', 0, 'right')
            return;
        }
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                ActiveBTN(true, '#submitLogin', 'Aguarde . . .', 'Ingresar')
                $("#tarjeta").removeClass("is-invalid");
            },
            success: function (data) {
                if (data.status == "ok") {
                    $.notifyClose();
                    $(".form-signin").addClass("animate__animated animate__fadeOutUp"); //animacion de cerrar
                    document.getElementById('mainNav').classList.remove('invisible');
                    document.getElementById('navName').innerHTML = data.Mensaje.name // nombre del usuario
                    document.getElementById('navLega').innerHTML = data.Mensaje.lega // legajo del usuario
                    // try {
                    let infoUser = JSON.stringify({
                        'name': data.Mensaje.name,
                        'lega': data.Mensaje.lega,
                        'last': data.Mensaje.last,
                        'uuid': data.Mensaje.uuid,
                        'urol': data.Mensaje.urol,
                        'lses': data.Mensaje.lses,
                        'addr': data.Mensaje.addr,
                        'uday': data.Mensaje.uday,
                        'pses': data.Mensaje.pses,
                        'reci': data.Mensaje.reci,
                    });
                    // alert(infoUser);

                    sessionStorage.setItem(location.pathname.substring(1) + 'proy_page', 'inicio')
                    sessionStorage.setItem(location.pathname.substring(1) + 'proy_info', infoUser)
                    document.getElementById('mainTitleBar').innerHTML = capitalize('inicio');

                    // const axios = require('axios').default;
                    axios.get('routes.php', {
                        params: {
                            'page': 'inicio'
                        }
                    }).then(function (response) {
                        // document.getElementById('contenedor').innerHTML = response.data;
                        $("#contenedor").html(response.data);
                        document.title = "Inicio";
                    }).then(() => {
                        // $("#inicioNombre").html(`Hola ${data.Mensaje.name}`);

                        fetch("sidebar.php?" + $.now()) // Sidebar
                            .then(response => response.text())
                            .then(data => {
                                $("#mainSideBar").html(data);
                                getPag(".sidebarEmpresas", "empresas");
                                getPag(".sidebarEstados", "estados");
                                getPag(".sidebarProcesos", "procesos");
                                getPag(".sidebarPlanos", "planos");
                                getPag(".sidebarPlantillas", "plantillas");
                                getPag(".sidebarProyectos", "proyectos");
                                getPag(".sidebarTareas", "tareas");
                                getPag(".sidebarInicio", "inicio");
                                getPag(".sidebarSalir", "salir");
                            });

                    }).catch(function (error) {
                        alert(error);
                    })

                } else {
                    ActiveBTN(false, '#submitLogin', 'Aguarde', 'Ingresar')
                    $.notifyClose();
                    notify(data.Mensaje, 'danger', 0, 'right')
                    $("#tarjeta").addClass("is-invalid");
                    $("#tarjeta").focus();
                }
            },
            error: function (data) {
                ActiveBTN(false, '#submitLogin', 'Aguarde', 'Ingresar')
                $.notifyClose();
                notify('Error', 'danger', 5000, 'right')
                $("#tarjeta").addClass("is-invalid");
            }
        });
        // e.stopImmediatePropagation();
    });
});
