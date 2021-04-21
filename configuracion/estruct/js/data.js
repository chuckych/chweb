$(function () {
    const _c = $('#_c').val()
    const _r = $('#_r').val()
    var table = 'tablePersonal'
    function ActualizaTablas() {
        $('#tableplantas').DataTable().ajax.reload(null, false);
        $('#tableempresas').DataTable().ajax.reload(null, false);
        $('#tablesucur').DataTable().ajax.reload(null, false);
        $('#tablegrupos').DataTable().ajax.reload(null, false);
        $('#tablesector').DataTable().ajax.reload(null, false);
        $('#tabletareas').DataTable().ajax.reload(null, false);
    };
    function removeButtons() {
        $('.trSelect').removeClass('trSelect')
        $('.trSelect').removeClass('trSelectPer')
        $('.btnAction').remove()
    }
    function eventoOn(x) {
        return (x.matches) ? 'click' : 'mouseenter'
    }
    let mxwidth = window.matchMedia("(max-width: 700px)")
    function ReasignarLegajos(tipo, valuetipo) {
        $("#FormPerson").bind("submit", function (event) {
            event.preventDefault();
            if (!$('.selectEstruc').val()) {
                notify('Debe seleccionar una Empresa', 'danger', 4000, 'center')
            } else {
                $.ajax({
                    type: $(this).attr("method"),
                    url: $(this).attr("action"),
                    data: $(this).serialize() + "&tipo=" + tipo + "&value=" + valuetipo + "&EstructName=" + $(".selectEstruc option:selected").text(),
                    beforeSend: function (data) {
                        ActiveBTN(true, '.submit', 'Aguarde..', 'Aceptar')
                    },
                    success: function (data) {
                        if (data.status == "ok") {
                            notify(data.Mensaje, 'success', 2000, 'center')
                            ActualizaTablas()
                            $('#tablePersonal').DataTable().ajax.reload();
                        } else {
                            ActiveBTN(false, '.submit', 'Aguarde..', 'Aceptar')
                            notify(data.Mensaje, 'danger', 2000, 'center')
                        }
                    },
                    error: function (data) {
                        ActiveBTN(false, '.submit', 'Aguarde..', 'Aceptar')
                        notify('Error', 'danger', 2000, 'center')
                    }
                });
            }
            event.stopImmediatePropagation();
        });
    }
    function actionButtons(data0, data1, nameidbtn1, nameidbtn2, actionForm) {
        if (data0 == '0') {
            let btns = `<div class="d-flex align-items-center justify-content-between">` + data1 + `<div class=""><a href="`+ actionForm + `" data-title="Ver personal afectado" type="button" id="view` + nameidbtn1 + data0 + `n" class="animate__animated animate__fadeIn btnAction mr-1 fontq btn btn-custom px-2 btn-sm view"><i class="bi2 bi-people-fill"></i></a></div></div>`
            return btns;
        } else {
            let btns = `<div class="d-flex align-items-center justify-content-between">` + data1 + `<div class=""><a href="`+ actionForm + `" data-title="Ver personal afectado" type="button" id="view` + nameidbtn1 + data0 + `n" class="animate__animated animate__fadeIn btnAction mr-1 fontq btn btn-custom px-2 btn-sm view"><i class="bi2 bi-people-fill"></i></a><a href="`+ actionForm + `" data-title="Editar ` + data1 + `" type="button" id="` + nameidbtn1 + data0 + `n" class="animate__animated animate__fadeIn btnAction fontq mr-1 btn btn-custom px-2 btn-sm" id="EditEmpresas" value="1"><i class="bi2 bi-pencil"></i></a><a href="`+ actionForm + `" data-title="Eliminar  ` + data1 + `" type="button" id="` + nameidbtn2 + data0 + `n" class="animate__animated animate__fadeIn btnAction fontq btn btn-custom px-2 btn-sm"><i class="bi2 bi-trash"></i></a></div>`
            return btns;
        }
    }
    function personalTable(cod, estruc, selector, url, placeholderselect, urlselect, valuereasign) {
        $(selector).DataTable({
            initComplete: function () {
                $(selector + " thead").remove()
                $(selector + "_filter .form-control").removeClass('form-control-sm')
                $(selector + "_filter .form-control").attr('placeholder', 'Buscar personal')
            },
            drawCallback: function (settings) {
                $(selector + " thead").remove()
                $(selector).show()
                $('.btns').html(`<div class="d-flex justify-content-between"><div class="d-flex align-items-center border"><div class="fontq btn btn-link" id="CheckAll"><span class="d-none d-sm-block">Marcar</span><span class="d-block d-sm-none"><i class="bi bi-check2-all"></i></span></div><div class="fontq btn btn-link" id="UnCheckAll"><span class="d-none d-sm-block">Desmarcar</span><span class="d-block d-sm-none"><i class="bi bi-dash"></i></span></div></div><div class="d-inline-flex"><select class="form-control selectEstruc w200" name="selectEstruc"></select><button type="submit" class="submit btn btn-sm btn-custom fontq ml-1 px-3" id="cambiaEmp">Aceptar</i></button></div></div>`)
                select2Ajax(".selectEstruc", placeholderselect, true, false, urlselect)
                // $('.submit').attr('disabled',true)
                ReasignarLegajos('Reasign', valuereasign)
                CheckUncheck('#CheckAll', '#UnCheckAll', $(".checkReg"))
            },
            dom: "<'row mt-2'<'col-12 col-sm-6 d-flex align-items-start'p><'col-12 col-sm-6 mt-2 mt-sm-0'f>>" +
                "<'row'<'col-12 btns'>>" +
                "<'row'<'col-12'tr>>" +
                "<'row'<'col-sm-12 col-md-6 d-flex align-items-start'i><'col-sm-12 col-md-6 d-flex justify-content-end'l>>",
            lengthMenu: [[5, 10, 25, 100, -1], [5, 10, 25, 50, 100, "Todo"]],
            iDisplayLength: 5,
            bLengthChange: true,
            "ajax": {
                url: url,
                type: "POST",
                data: function (data) {
                    data.estruc = estruc;
                    data.cod = cod;
                },
                error: function () { },
            },
            columnDefs: [
                { className: "align-middle py-4 text-center Check pointer", targets: 0 },
                { className: "align-middle w-100 Check pointer", targets: 1 },
                { className: "align-middle CheckPersonal Check pointer", targets: 2 },
                { className: "align-middle Check pointer", targets: 3 },
            ],
            deferRender: true,
            bProcessing: true,
            bLengthChange: true,
            paging: true,
            searching: true,
            info: true,
            ordering: false,
            autoWidth: true,
            language: {
                "url": "../../js/DataTableSpanishTotal.json?v=" + vjs()
            },
        })
    }
    function printForm(cod, desc, tipo, selectorAction) {
        $.ajax({
            type: 'post',
            dataType: "html",
            url: "formulario.php?v=" + vjs(),
            data: {
                Cod: cod,
                Desc: desc,
                Tipo: tipo,
            },
            beforeSend: function (xhr) {
                $(selectorAction).html('<div class="p-3 text-secondary animate__animated animate__fadeIn">Cargando..</div>')
            }
        }).done(function (data) {
            $(selectorAction).html(data)
            removeButtons()
        });
    }
    function tableEstruct(selectorDT, selectoraddbutton, urlajax, checkEstructClass, className1, className2, className3, actionForm, phfilter, nameidbtn0, nameidbtn1, nameidbtn2, d_,u_, estruct, urlajaxselect, formaction){
        // var table = 'tablePersonal'
        tableDT = $(selectorDT).DataTable({
            initComplete: function () {
                $(selectorDT+' thead').remove()
                // $("#tableempresas_filter").addClass('d-inline-flex ')
                $(selectorDT+'_filter').append(`<a href="`+actionForm+`" data-title="Agregar `+estruct+`" type="button" class="ml-1 btn btn-custom btn-sm" id="`+selectoraddbutton+`"><i class="bi2 bi-plus"></i></a>`)
                $('#'+selectoraddbutton).click(function () {
                    printForm('', '', nameidbtn0, actionForm) /** imprimo html para ingresar datos */
                });
                $(selectorDT+'_filter .form-control').removeClass('form-control-sm')
                $(selectorDT+'_filter .form-control').attr('placeholder', phfilter)
            },
            drawCallback: function (settings) {
                $(selectorDT+' thead').remove()
            },
            dom: "<'row mt-2'<'col-12 col-sm-6 d-flex align-items-start'p><'col-12 col-sm-6 mt-2 mt-sm-0'f>>" +
                "<'row'<'col-12'tr>>" +
                "<'row'<'col-sm-12 col-md-6 d-flex align-items-start'i><'col-sm-12 col-md-6 d-flex justify-content-end'l>>",
            lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "Todo"]],
            stateSave: true,
            stateDuration: -1,
            iDisplayLength: 5,
            bLengthChange: true,
            "ajax": {
                url: urlajax,
                type: "POST",
                "data": function (data) {},
                error: function () { },
            },
            columnDefs: [
                { className: className1, targets: 0 },
                { className: className2, targets: 1 },
                { className: className3, targets: 2 },
            ],
            deferRender: true,
            bProcessing: true,
            bLengthChange: true,
            paging: true,
            searching: true,
            info: true,
            ordering: false,
            autoWidth: true,
            language: {
                "url": "../../js/DataTableSpanishTotal.json?v=" + vjs()
            },
        })
        $(selectorDT+' tbody').on(eventoOn(mxwidth), checkEstructClass, function (e) {
            e.preventDefault();
            removeButtons()
            $(this).parents('tr').addClass('trSelect')
            let data = $(selectorDT).DataTable().row($(this).parents('tr')).data();
            let url = "/" + _homehost + "/data/"+urlajaxselect+"?_c=" + _c + "&_r" + _r;
            $(this).html(actionButtons(data[0], data[1], nameidbtn1, nameidbtn2, actionForm))
            $("#" + nameidbtn1 + data[0] + "n").show()
            $("#" + nameidbtn2 + data[0] + "n").show()
            $("#" + nameidbtn1 + data[0] + "n").click(function (e) {
                e.preventDefault();
                printForm(data[0], data[1], u_, actionForm) /** imprimo html de formulario */
            });
            $("#" + nameidbtn2 + data[0] + "n").click(function (e) {
                e.preventDefault();
                printForm(data[0], data[1], d_, actionForm) /** imprimo html de formulario */
            });
            $("#view" + nameidbtn1 + data[0] + "n").click(function (e) {
                e.preventDefault();
                $(actionForm).html('')
                $('.selectEstruc').focus()
                $(actionForm).html('<form action="'+formaction+'" method="post" id="FormPerson"><div class="animate__animated animate__fadeIn"><p class="mt-3 fontq w-100">'+estruct+': <span class="fw5"><input type="hidden" name="EstructActual" value="' + data[0] + '@' + data[1] + '">' + data[1] + '</span class="fw5"></p><table class="table-responsive table text-nowrap w-100 border table-hover" style="display: none"id="' + table + '"></table></div></form>');
                personalTable(data[0], estruct, '#' + table, 'getPersonalEstruct.php', 'Reasignar '+estruct, url, estruct)
                $('#' + table + ' tbody').on('click', '.Check', function (e) {
                    e.preventDefault();
                    let tablePersonal = $('#' + table).DataTable().row($(this).parents('tr')).data();
                    if ($('#Check' + tablePersonal[0]).is(":checked")) {
                        $('#Check' + tablePersonal[0]).prop('checked', false)
                        $(this).parents('tr').removeClass('table-active')
                    } else {
                        $('#Check' + tablePersonal[0]).prop('checked', true)
                        $(this).parents('tr').addClass('table-active')
                    };
                });
            });
        });
        $(selectorDT).on('mouseleave', checkEstructClass, function () {
            removeButtons()
        });
        $(selectorDT).mouseenter().off("mouseenter")
    }
    /** DATA EMPRESAS */
    tableEstruct("#tableempresas","AddEmpresas","getEmpresas.php",".CheckEmpresas","align-middle val pointer py-4 text-center","align-middle val pointer CheckEmpresas w-100","align-middle pointer","#actionForm_e","Buscar empresa","c_empresas","editEmpresas","deleteEmpresas","d_empresas","u_empresas","Empresa","getEmpresas.php","crud.php");   
    /** DATA PLANTAS */
    tableEstruct("#tableplantas","AddPlantas","getPlantas.php",".CheckPlantas","align-middle val pointer py-4 text-center","align-middle val pointer CheckPlantas w-100","align-middle pointer","#actionForm","Buscar planta","c_plantas","editPlantas","deletePlantas","d_plantas","u_plantas","Planta","getPlantas.php","crud.php");
    /** DATA SUCURSALES */
    tableEstruct("#tablesucur","AddSucur","getSucur.php",".CheckSucur","align-middle val pointer py-4 text-center","align-middle val pointer CheckSucur w-100","align-middle pointer","#actionForm_suc","Buscar sucursal","c_sucur","editSucur","deleteSucur","d_sucur","u_sucur","Sucursal","getSucursales.php","crud.php");
    /** DATA GRUPOS */
    tableEstruct("#tablegrupos","AddGrupos","getGrupos.php",".CheckGrupos","align-middle val pointer py-4 text-center","align-middle val pointer CheckGrupos w-100","align-middle pointer","#actionForm_grupos","Buscar grupo","c_grupos","editGrupos","deleteGrupos","d_grupos","u_grupos","Grupo","getGrupos.php","crud.php");
    /** DATA SECTORES */
    tableEstruct("#tablesector","AddSector","getSector.php",".CheckSector","align-middle val pointer py-4 text-center","align-middle val pointer CheckSector w-100","align-middle pointer","#actionForm_sector","Buscar sector","c_sector","editSector","deleteSector","d_sector","u_sector","Sector","getSectores.php","crud.php");
    /** DATA TAREAS */
    tableEstruct("#tabletareas","AddTarea","getTareas.php",".CheckTarea","align-middle val pointer py-4 text-center","align-middle val pointer CheckTarea w-100","align-middle pointer","#actionForm_tareas","Buscar tarea","c_tareas","editTarea","deleteTarea","d_tareas","u_tareas","Tarea","gettareas.php","crud.php");
    
    $("#Encabezado").addClass('pointer')
    $("#Encabezado").on("click", function () {
        ActualizaTablas()
    });
    function cleanActionsForms() {
        $('#actionForm_e').html('')
        $('#actionForm_suc').html('')
        $('#actionForm_grupos').html('')
        $('#actionForm_tareas').html('')
        $('#actionForm_sector').html('')
        $('#actionForm').html('')
    }
    $('#empresas-tab').on('shown.bs.tab', function (e) {
        cleanActionsForms()
        sessionStorage.setItem('activeTabEstruct', $(e.target).attr('href'));
    })
    $('#plantas-tab').on('shown.bs.tab', function (e) {
        cleanActionsForms()
        sessionStorage.setItem('activeTabEstruct', $(e.target).attr('href'));
    })
    $('#sucur-tab').on('shown.bs.tab', function (e) {
        cleanActionsForms()
        sessionStorage.setItem('activeTabEstruct', $(e.target).attr('href'));
    })
    $('#grupos-tab').on('shown.bs.tab', function (e) {
        cleanActionsForms()
        sessionStorage.setItem('activeTabEstruct', $(e.target).attr('href'));
    })
    $('#sector-tab').on('shown.bs.tab', function (e) {
        cleanActionsForms()
        sessionStorage.setItem('activeTabEstruct', $(e.target).attr('href'));
    })
    $('#tareas-tab').on('shown.bs.tab', function (e) {
        cleanActionsForms()
        sessionStorage.setItem('activeTabEstruct', $(e.target).attr('href'));
    })
    let activeTab = sessionStorage.getItem('activeTabEstruct');
    if (activeTab) {
        $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
    }
});
