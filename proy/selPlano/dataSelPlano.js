$(function () {
    //"use strict"; // Start of use strict

    let proy_info = sessionStorage.getItem(
        location.pathname.substring(1) + "proy_info"
    );
    proy_info = JSON.parse(proy_info);

    let proy_pasos = sessionStorage.getItem(
        location.pathname.substring(1) + "proy_pasos"
    );
    proy_pasos = JSON.parse(proy_pasos);
    // console.log(proy_info);

    if (proy_info) {
        $("#inicioNombre").html(`Hola ${proy_info.name}`);
    }

    $("#mainTitleBar").html(('Seleccionar Plano'));
    $(document).prop("title", ('Seleccionar Plano'));
    let tablePlano = $("#selectPlano").dataTable({ //inicializar datatable
        lengthMenu: [[6, 10, 25, 50, 100], [6, 10, 25, 50, 100]],
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        responsive: true,
        dom:
            "<'row '<'col-12 py-2' <'pagPlano'p>>>" +
            "<'row '<'col-12 sticky-top d-inline-flex justify-content-between title'f>>" +
            "<'row '<'col-12 table-responsive mt-2 d-none tablePlano't>>",
        ajax: {
            url: `data/getPlanos.php?${Date.now()}`,
            type: "POST",
            dataType: "json",
            data: function (data) {
                // data.Plant = proy_pasos.ProyPlant;
                // data.length = 9999;
            },
            error: function () {
                $("#selectPlano").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass("");
            $('#listSelPlanoRow').append(`
                <div class="col-6 col-sm-6">
                    <div 
                        data-PlanoID="${data.PlanoID}" 
                        data-PlanoDesc="${data.PlanoDesc}" 
                        data-PlanoObs="${data.PlanoObs}"
                        data-PlanoCod="${data.PlanoCod}"
                        class="card p-3 mt-3 animate__animated animate__fadeIn pointer checkPlano">
                        <div class="form-check pointer mb-1">
                            <input class="form-check-input" type="checkbox" value="" id="plano_${data.PlanoID}">
                            <label class="form-check-label h3 mb-0" for="plano_${data.PlanoID}">
                            <span class="ls1 text-secondary">(#${data.PlanoID})</span>  ${data.PlanoDesc}
                            </label>
                        </div>
                        <div class="font08 text-secondary overflow-auto" style="max-height:50px">${data.PlanoObs}</div>
                    </div>
                </div>
                `);
        },
        columns: [
            {
                className: "align-middle border-bottom py-1 w-100 text-wrap",
                targets: "",
                title: "",
                render: function (data, type, row, meta) {
                    let PlanoCod = (row.PlanoCod) ? `<span class='text-secondary font08'> - Código: ${row.PlanoCod}</span>` : '';
                    let datacol =
                        `
                        <div class="card p-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="${row.PlanoID}">
                                <label class="form-check-label font09 font-weight-bold" for="${row.PlanoID}">
                                <label class="ls1 text-secondary">(#${row.PlanoID})</label>  ${row.PlanoDesc} ${PlanoCod}
                                </label>
                            </div>
                            <div class="font08 text-secondary">${row.PlanoObs}</div>
                        </div>
                        `;
                    return datacol;
                }
            },
        ],
        info: true,
        ordering: 0,
        responsive: 0,
        language: {
            url: `../js/DataTableSpanishShort2.json?${Date.now()}`
        }

    });
    tablePlano.on('init.dt', function () {
        $("#selectPlano thead").remove();
        $(".dataTables_scrollHead").remove();
        $('#selectPlano_filter input').attr('placeholder', 'Buscar Plano').addClass('p-3 w300');
        setTimeout(() => {
            $('#selectPlano_filter input').focus();
        }, 1500);
        $('.title').prepend(`
        <div class="w-100">
            <h3 class="display-6 text-tabler">Seleccionar Plano</h3>
        </div>
    `)
        $('#listSelPlano').fadeIn();
    });
    tablePlano.on('draw.dt', function (e, settings) {
        e.preventDefault();
        $("#selectPlano thead").remove();
        $(".dataTables_scrollHead").remove();
        if (settings._iRecordsTotal > 6) {
            $(".pagPlano").show();
        } else {
            $(".pagPlano").hide();
        }
    });
    tablePlano.on('search.dt', function (e) {
        $('#listSelPlanoRow').html('');
    });
    tablePlano.on('page.dt', function (e) {
        $('#listSelPlanoRow').html('');
    });
    // $(document).on("click", "#selectPlano tbody tr", function (e) {
    $(document).on("click", ".checkPlano", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        $('#selectPlano tbody tr').find("input[type='checkbox']").prop('checked', false);
        let checkbox = $(this).find("input[type='checkbox']");
        checkbox.prop("checked", !checkbox.prop("checked"));

        // let data = $("#selectPlano").DataTable().row($(this)).data(); //obtener datos de la fila seleccionada

        let dataPlanoObs = $(this).attr('data-PlanoObs');
        let dataPlanoID = $(this).attr('data-PlanoID');
        let dataPlanoDesc = $(this).attr('data-PlanoDesc');
        let dataPlanoCod = $(this).attr('data-PlanoCod');

        let p = get_proy_pasos();

        let proy_pasos = JSON.stringify({
            'EmpID': p.EmpID,
            'EmpDesc': p.EmpDesc,
            'ProyID': p.ProyID,
            'ProyNom': p.ProyNom,
            'ProyDesc': p.ProyDesc,
            'ProyPlant': p.ProyPlant,
            'PlantDesc': p.PlantDesc,
            'ProyResp': p.ProyResp,
            'RespDesc': p.RespDesc,
            'ProcID': p.ProcID,
            'ProcDesc': p.ProcDesc,
            'ProcCost': p.ProcCost,
            'PlanoID': dataPlanoID,
            'PlanoDesc': dataPlanoDesc,
            'PlanoCod': dataPlanoCod,
        });
        sessionStorage.setItem(location.pathname.substring(1) + 'proy_pasos', proy_pasos)
        axios.get('routes.php', {
            params: {
                'page': 'finalizar'
            }
        }).then(function (response) {
            sessionStorage.setItem(
                location.pathname.substring(1) + "proy_page",
                'finalizar'
            );
            $("#contenedor").html(response.data);
        }).then(() => {
        }).catch(function (error) {
            console.log(error);
        })

    });
    $(document).on("click", "#omitePlano", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        let dataPlanoObs = '';
        let dataPlanoID = '';
        let dataPlanoDesc = '';
        let dataPlanoCod = '';

        let p = get_proy_pasos();

        let proy_pasos = JSON.stringify({
            'EmpID': p.EmpID,
            'EmpDesc': p.EmpDesc,
            'ProyID': p.ProyID,
            'ProyNom': p.ProyNom,
            'ProyDesc': p.ProyDesc,
            'ProyPlant': p.ProyPlant,
            'PlantDesc': p.PlantDesc,
            'ProyResp': p.ProyResp,
            'RespDesc': p.RespDesc,
            'ProcID': p.ProcID,
            'ProcDesc': p.ProcDesc,
            'ProcCost': p.ProcCost,
            'PlanoID': dataPlanoID,
            'PlanoDesc': dataPlanoDesc,
            'PlanoCod': dataPlanoCod,
        });
        sessionStorage.setItem(location.pathname.substring(1) + 'proy_pasos', proy_pasos)
        axios.get('routes.php', {
            params: {
                'page': 'finalizar'
            }
        }).then(function (response) {
            sessionStorage.setItem(
                location.pathname.substring(1) + "proy_page",
                'finalizar'
            );
            $("#contenedor").html(response.data);
        }).then(() => {
        }).catch(function (error) {
            console.log(error);
        })

    });

});