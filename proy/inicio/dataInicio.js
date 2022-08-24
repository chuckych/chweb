$(function () {
    //"use strict"; // Start of use strict
    cleanProy_pasos();

    let proy_info = sessionStorage.getItem(
        location.pathname.substring(1) + "proy_info"
    );
    proy_info = JSON.parse(proy_info);
    // console.log(proy_info);
    if (!proy_info) {
        fetch("routes.php/?page=log_rfid")
            .then(response => response.text())
            .then(data => {
                sessionStorage.setItem(
                    location.pathname.substring(1) + "proy_page",
                    ""
                );
                $("#mainNav").addClass("invisible");
                $("#contenedor").html(data);
            });
    }
    sessionStorage.setItem(
        location.pathname.substring(1) + "proy_page",
        'inicio'
    );
    let tableProy = '';

    if (proy_info) {
        $("#inicioNombre").html(`Hola ${proy_info.name}`);
    }
    // $('#selectProy').DataTable().destroy();
    // $("#btnSelProyecto").on("click", function () {
    $(document).on("click", "#btnSelProyecto", function (e) {
        // alert("Selec proyecto");
        $('#btnSelProy').fadeOut();
        setTimeout(() => {
            sessionStorage.setItem(
                location.pathname.substring(1) + "proy_page",
                'inicio'
            );
            $("#mainTitleBar").html(('Seleccionar Proyecto'));
            $(document).prop("title", ('Seleccionar Proyecto'));

            $('#listSelProy').fadeIn();
        }, 500);

    });

    tableProy = $("#selectProy").dataTable({ //inicializar datatable
        lengthMenu: [[6, 10, 25, 50, 100], [6, 10, 25, 50, 100]], //mostrar cantidad de registros
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        responsive: true,
        dom:
            "<'row '<'col-12 py-2' <'pagProy'p>>>" +
            "<'row '<'col-12 sticky-top d-inline-flex justify-content-between title'f>>" +
            "<'row '<'col-12 table-responsive mt-2 d-none tableProy't>>",
        ajax: {
            url: `data/getProyectos.php?${Date.now()}`,
            type: "POST",
            dataType: "json",
            data: function (data) {
                data.FiltroEstTipo = "Abierto";
            },
            error: function () {
                $("#selectProy").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass("");
            let divHoras = ''
            let horasProy = data.ProyCalc.Horas
            let textHS = data.ProyCalc.Minutos < 60 ? 'Min' : 'Hs'
            horasProy = data.ProyCalc.Minutos < 60 ? data.ProyCalc.Minutos : horasProy
            if (data.ProyCalc.Minutos > 0) {
                divHoras = `<span class="font08 px-3 d-none d-sm-block radius-0 badge bg-azure font-weight-normal">${horasProy}<span class="text-capitalize font07">${(textHS)}</span></span>`
            }
            $('#listSelProyRow').append(`
                <div class="col-12 col-sm-6">
                    <div 
                        data-EmpID="${data.ProyEmpr.ID}" 
                        data-EmpDesc="${data.ProyEmpr.Nombre}" 
                        data-ProyID="${data.ProyData.ID}"
                        data-ProyNom="${data.ProyData.Nombre}"
                        data-ProyDesc="${data.ProyData.Desc}"
                        data-ProyPlant="${data.ProyPlant.ID}"
                        data-PlantDesc="${data.ProyPlant.Nombre}"
                        data-ProyResp="${data.ProyResp.ID}"
                        data-RespDesc="${data.ProyResp.Nombre}"
                        data-ProyPlantPlano="${data.ProyPlantPlano.ID}"
                        class="card p-3 mt-2 mt-sm-3 animate__animated animate__fadeIn pointer checkProy">
                        <div class="form-check pointer">
                            <input class="form-check-input" type="checkbox" value="" id="proy_${data.ProyData.ID}">
                            <div class="d-inline-flex w-100">
                                <label class="form-check-label h3 w-100" for="proy_${data.ProyData.ID}">
                                <span class="ls1 text-secondary">(#${data.ProyData.ID})</span>  ${data.ProyData.Nombre}
                                </label>
                                ${divHoras}
                            </div> 
                        </div>
                        <div class="font08 text-secondary">${data.ProyData.Desc}</div>
                    </div>
                </div>
                `);
        },
        columns: [
            {
                className: "align-middle border-bottom py-1 w-100",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                        <div class="card p-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="${row.ProyID}">
                                <label class="form-check-label font09 font-weight-bold" for="${row.ProyID}">
                                <label class="ls1 text-secondary">(#${row.ProyID})</label>  ${row.ProyNom}
                                </label>
                            </div>
                            <div class="font08 text-secondary">${row.ProyDesc}</div>
                        </div>
                        `;
                    return datacol;
                }
            },
            {
                className: "align-middle border-bottom px-3 d-none",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                        <span class="font08 text-secondary"><i class="bi bi-building me-1"></i> Empresa: </span><br><span class="font09 font-weight-bold">${row.EmpDesc}</span>
                        `;
                    return datacol;
                }
            },
        ],
        paging: true,
        searching: true,
        info: true,
        ordering: 0,
        responsive: 0,
        language: {
            url: `../js/DataTableSpanishShort2.json?${Date.now()}`
        }

    });
    // on init datatable
    tableProy.on('init.dt', function () {
        $("#selectProy thead").remove();
        $(".dataTables_scrollHead").remove();
        $('#selectProy_filter input').attr('placeholder', 'Buscar proyecto').addClass('p-3 w300');
        // setTimeout(() => {
            // $('#selectProy_filter input').focus();
        // }, 1500);
        $('.title').prepend(`
            <div class="w-100 d-sm-block d-none">
                <h3 class="display-6 text-tabler">Seleccionar Proyecto</h3>
            </div>
        `)
        procPend(true, '#tableTarUser');
    });
    tableProy.on('draw.dt', function (e, settings) {
        e.preventDefault();
        $("#selectProy thead").remove();
        $(".dataTables_scrollHead").remove();
        if (settings._iRecordsTotal > 6) {
            $(".pagProy").show();
        } else {
            $(".pagProy").hide();
        }
    });
    tableProy.on('search.dt', function (e) {
        $('#listSelProyRow').html('');
    });
    tableProy.on('page.dt', function (e) {
        $('#listSelProyRow').html('');
    });
    $(document).on("click", ".checkProy", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        $('#selectProy tbody tr').find("input[type='checkbox']").prop('checked', false);
        let checkbox = $(this).find("input[type='checkbox']");
        checkbox.prop("checked", !checkbox.prop("checked"));

        let dataEmpID = $(this).attr('data-EmpID');
        let dataEmpDesc = $(this).attr('data-EmpDesc');
        let dataProyID = $(this).attr('data-ProyID');
        let dataProyNom = $(this).attr('data-ProyNom');
        let dataProyDesc = $(this).attr('data-ProyDesc');
        let dataProyPlant = $(this).attr('data-ProyPlant');
        let dataPlantDesc = $(this).attr('data-PlantDesc');
        let dataProyResp = $(this).attr('data-ProyResp');
        let dataRespDesc = $(this).attr('data-RespDesc');
        let ProyPlantPlano = $(this).attr('data-ProyPlantPlano');

        let titlePag = 'Seleccionar Proceso';
        let proy_pasos = JSON.stringify({
            'EmpID': dataEmpID,
            'EmpDesc': dataEmpDesc,
            'ProyID': dataProyID,
            'ProyNom': dataProyNom,
            'ProyDesc': dataProyDesc,
            'ProyPlant': dataProyPlant,
            'PlantDesc': dataPlantDesc,
            'ProyResp': dataProyResp,
            'RespDesc': dataRespDesc,
            'ProyPlantPlano': ProyPlantPlano
        });
        sessionStorage.setItem(location.pathname.substring(1) + 'proy_pasos', proy_pasos)

        axios.get('routes.php', {
            params: {
                'page': 'selProc'
            }
        }).then(function (response) {
            sessionStorage.setItem(
                location.pathname.substring(1) + "proy_page",
                'selProc'
            );
            $("#contenedor").html(response.data);
        }).then(() => {
            $("#mainTitleBar").html(capitalize(titlePag));
            $(document).prop("title", capitalize(titlePag));
        }).catch(function (error) {
            console.log(error);
        })

    });
    tableTarUser = $("#tableTarUser").dataTable({ //inicializar datatable
        lengthMenu: [[3, 10, 25, 50, 100], [3, 10, 25, 50, 100]], //mostrar cantidad de registros
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        responsive: true,
        dom:
            "<'row '<'col-12 table-responsive mt-2't>>",
        ajax: {
            url: `data/getTareas.php?${Date.now()}`,
            type: "POST",
            dataType: "json",
            data: function (data) {
                data.TareResp = proy_info.uuid;
                data.TarePend = true;
                data.length = 9999;
            },
            error: function () {
                $("#selectProy").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass("");
        },
        rowGroup: {
            dataSrc: function (row) {
                return `
                    <div class="d-inline-flex justify-content-between w-100">
                        <div>
                            <div class="${row.fechas.inicioHora} h2">
                                <span class="tracking-wide">(#${row.proyecto.ID})</span> ${row.proyecto.nombre}
                            </div>
                            <div class="text-mutted font08 m-0 p-0">${row.proyecto.descripcion}</div>
                        </div>
                        <div>
                            <button class="btn btn-success px-4 completeTar" data-tareID="${row.TareID}">COMPLETAR</button>
                        </div>
                    </div>

                    `;
            },
            endRender: null,
            startRender: function (rows, group) {
                return `${group}`;
            },
        },
        columns: [
            {
                className: "",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                            <div class="font08 text-secondary">Empresa:</div>
                            <div class="h3">${row.empresa.nombre}</div>
                        `;
                    return datacol;
                }
            },
            {
                className: "",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                            <div class="font08 text-secondary">Proceso:</div>
                            <div class="h3">${row.proceso.nombre}</div>
                        `;
                    return datacol;
                }
            },
            {
                className: "",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                            <div class="font08 text-secondary">Plano:</div>
                            <div class="h3">${row.plano.nombre}</div>
                        `;
                    return datacol;
                }
            },
            {
                className: "",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                            <div class="font08 text-secondary">Fecha:</div>
                            <div class="h3">${row.fechas.inicio}</div>
                        `;
                    return datacol;
                }
            },
            {
                className: "",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                            <div class="font08 text-secondary">Inicio:</div>
                            <div class="h3">${row.fechas.inicioHora}</div>
                        `;
                    return datacol;
                }
            },
            {
                className: "",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let datacol =
                        `
                            <div class="font08 text-secondary">Tiempo</div>
                            <div class="h3">${row.fechas.diffHuman}</div>
                        `;
                    return datacol;
                }
            },
        ],
        paging: true,
        searching: false,
        info: false,
        ordering: 0,
        responsive: 0,
        language: {
            url: `../js/DataTableSpanishShort2.json?${Date.now()}`
        }

    });
    tableTarUser.on('init.dt', function (e) {
        e.preventDefault();
        $("#tableTarUser thead").remove();
        $(".dataTables_scrollHead").remove();
        // $('#tableTarUser_filter input').attr('placeholder', 'Buscar proyecto').addClass('p-3 w300');
        setTimeout(() => {
            $('#tableTarUser_filter input').focus();
        }, 1500);
        setInterval(() => {
            $('#tableTarUser').DataTable().ajax.reload();
        }, 60000);
        $('#divTarPend').show()
    });
    tableTarUser.on('draw.dt', function (e, settings, data) {
        e.preventDefault();
        if (settings._iRecordsTotal <= 0) {
            $('#titleTareas').html('No registra Tareas Pendientes')
            $('#tableTarUser').hide();
            $('#btnSelProyecto').prop('disabled', false);
        } else {
            $('#titleTareas').html('Tareas Pendientes')
            $('#tableTarUser').show();
            $('#btnSelProyecto').prop('disabled', true);
        }
        $("#tableTarUser thead").remove();
        $(".dataTables_scrollHead").remove();
    });
    completeTar('.completeTar')
});