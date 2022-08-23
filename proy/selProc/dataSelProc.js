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

    $("#mainTitleBar").html(('Seleccionar Proceso'));
    $(document).prop("title", ('Seleccionar Proceso'));
    let tableProc = $("#selectProc").dataTable({ //inicializar datatable
        lengthMenu: [[6, 10, 25, 50, 100], [6, 10, 25, 50, 100]],
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        responsive: true,
        dom:
        "<'row '<'col-12 py-2' <'pagProc'p>>>" +
        "<'row '<'col-12 sticky-top d-inline-flex justify-content-between title'f>>" +
        "<'row '<'col-12 table-responsive mt-2 d-none tableProy't>>",
        ajax: {
            url: `data/getProcesos.php?${Date.now()}`,
            type: "POST",
            dataType: "json",
            data: function (data) {
                data.Plant = proy_pasos.ProyPlant;     
            },
            error: function () {
                $("#selectProc").css("display", "none");
            }
        },
        createdRow: function (row, data, dataIndex) {
            $(row).addClass("");
            $('#listSelProcRow').append(`
                <div class="col-12 col-sm-6">
                    <div 
                        data-ProcID="${data.ProcID}" 
                        data-ProcDesc="${data.ProcDesc}" 
                        data-ProcCost="${data.ProcCost}"
                        data-ProcObs="${data.ProcObs}"
                        class="card p-3 mt-2 mt-sm-3 animate__animated animate__fadeIn pointer checkProc">
                        <div class="form-check pointer">
                            <input class="form-check-input" type="checkbox" value="" id="proc_${data.ProcID}">
                            <label class="form-check-label h3" for="proc_${data.ProcID}">
                            <span class="ls1 text-secondary">(#${data.ProcID})</span>  ${data.ProcDesc}
                            </label>
                        </div>
                        <div class="font08 text-secondary">${data.ProcObs}</div>
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
                    let datacol =
                        `
                        <div class="card p-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="${row.ProcID}">
                                <label class="form-check-label font09 font-weight-bold" for="${row.ProcID}">
                                <label class="ls1 text-secondary">(#${row.ProcID})</label>  ${row.ProcDesc}
                                </label>
                            </div>
                            <div class="font08 text-secondary">${row.ProcObs}</div>
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
    tableProc.on('init.dt', function () {
        $("#selectProc thead").remove();
        $(".dataTables_scrollHead").remove();
        $('#selectProc_filter input').attr('placeholder', 'Buscar proceso').addClass('p-3 w300');
        // setTimeout(() => {
        //     $('#selectProc_filter input').focus();
        // }, 1500);
        $('.title').prepend(`
        <div class="w-100 d-none d-sm-block">
            <h3 class="display-6 text-tabler">Seleccionar Proceso</h3>
        </div>
    `)
    $('#listSelProc').fadeIn();
    });
    tableProc.on('draw.dt', function (e, settings) {
        e.preventDefault();
        $("#selectProc thead").remove();
        $(".dataTables_scrollHead").remove();
        if (settings._iRecordsTotal > 6) {
            $(".pagProc").show();
        } else {
            $(".pagProc").hide();
        }
    });
    tableProc.on('search.dt', function (e) {
        $('#listSelProcRow').html('');
    });
    tableProc.on('page.dt', function (e) {
        $('#listSelProcRow').html('');
    });

    $(document).on("click", ".checkProc", function (e) {
    // $(document).on("click", "#selectProc tbody tr", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        $('#selectProc tbody tr').find("input[type='checkbox']").prop('checked', false);
        let checkbox = $(this).find("input[type='checkbox']");
        checkbox.prop("checked", !checkbox.prop("checked"));

        // let data = $("#selectProc").DataTable().row($(this)).data(); //obtener datos de la fila seleccionada

        let dataProcID = $(this).attr('data-ProcID');
        let dataProcDesc = $(this).attr('data-ProcDesc');
        let dataProcCost = $(this).attr('data-ProcCost');
        let dataProcObs = $(this).attr('data-ProcObs');

        let titlePag = 'Vincular Plano';
        let p = get_proy_pasos();

        let proy_pasos = JSON.stringify({
            'EmpID'     : p.EmpID,
            'EmpDesc'   : p.EmpDesc,
            'ProyID'    : p.ProyID,
            'ProyNom'   : p.ProyNom,
            'ProyDesc'  : p.ProyDesc,
            'ProyPlant' : p.ProyPlant,
            'PlantDesc' : p.PlantDesc,
            'ProyResp'  : p.ProyResp,
            'RespDesc'  : p.RespDesc,
            'ProcID'    : dataProcID,
            'ProcDesc'  : dataProcDesc,
            'ProcCost'  : dataProcCost,
            'ProcObs'   : dataProcObs
        });

        sessionStorage.setItem(location.pathname.substring(1) + 'proy_pasos', proy_pasos)

        axios.get('routes.php', {
            params: {
                'page': 'selPlano'
            }
        }).then(function (response) {
            sessionStorage.setItem(
                location.pathname.substring(1) + "proy_page",
                'selPlano'
            );
            $("#contenedor").html(response.data);
        }).then(() => {
            $("#mainTitleBar").html(capitalize(titlePag));
            $(document).prop("title", capitalize(titlePag));
        }).catch(function (error) {
            console.log(error);
        })

    });
});