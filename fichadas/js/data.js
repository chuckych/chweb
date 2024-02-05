$(function () {
    // $(".Filtros").prop('disabled', true);
    function ActualizaTablas() {
        loadingTable('#GetFichadas')
        loadingTable('#GetFichadasFecha')
        loadingTable('#GetPersonal')
        loadingTable('#GetFechas')
        if ($("#Visualizar").is(":checked")) {
            $('#GetFechas').DataTable().ajax.reload();
        } else {
            $('#GetPersonal').DataTable().ajax.reload();
            $('#GetFechas').DataTable().ajax.reload();
            $('#GetFichadas').show();
            $('#Per2').addClass('d-none')
            $('.pers_legajo').removeClass('d-none')
        };
    };
    $('#Filtros').on('shown.bs.modal', function () {
        CheckSesion()
    });
    let loadingTable = (selectortable) => {
        $(selectortable + ' td div').addClass('bg-light text-light')
        $(selectortable + ' td img').addClass('invisible')
        $(selectortable + ' td i').addClass('invisible')
        $(selectortable + ' td span').addClass('invisible')
    }
    let colorFic = (value) => {
        textColor = ''
        switch (value['Tipo']) {
            case 'Normal':
                textColor = ''
                break;
            case 'Manual':
                textColor = 'text-primary'
                break;
            default:
                textColor = ''
                break;
        }
        textColor = (value['Esta'] == 'Modificada') ? 'text-danger' : textColor
        return textColor
    }
    let map = { 17: false, 18: false, 32: false, 16: false, 39: false, 37: false, 13: false, 27: false };

    $(document).keydown(function (e) {
        if (e.keyCode in map) {
            map[e.keyCode] = true;
            if (map[32]) { /** Barra espaciadora */
                $('#Filtros').modal('show');
            }
        }
        if (e.keyCode in map) {
            map[e.keyCode] = true;
            if (map[27]) { /** Esc */
                $('#Filtros').modal('hide');
            }
        }

        // if (e.keyCode in map) {
        //     map[e.keyCode] = true;
        //     if (map[13]) { /** Enter */
        //         ActualizaTablas()
        //     }
        // }
        if (e.keyCode in map) {
            map[e.keyCode] = true;
            if (map[39]) { /** Flecha derecha */
                if ($("#Visualizar").is(":checked")) {
                    $('#GetFechas').DataTable().page('next').draw('page');
                } else {
                    $('#GetPersonal').DataTable().page('next').draw('page');
                };
            }
        }
        if (e.keyCode in map) {
            map[e.keyCode] = true;
            if (map[37]) { /** Flecha izquierda */
                if ($("#Visualizar").is(":checked")) {
                    $('#GetFechas').DataTable().page('previous').draw('page');
                } else {
                    $('#GetPersonal').DataTable().page('previous').draw('page');
                };

            }
        }
    }).keyup(function (e) {
        if (e.keyCode in map) {
            map[e.keyCode] = false;
        }
    });

    $("#pagFech").addClass('d-none');
    $("#GetFichadasFechaTable").addClass('d-none');

    $('#datoFicFalta').val('0');

    function CheboxChecked(selector) {
        CheckSesion()
        $(selector).val(0)
        $(selector).change(function () {
            if (($(selector).is(":checked"))) {
                $(selector).val(1)
            } else {
                $(selector).val(0)
            }
        });
    }
    CheboxChecked('#FicFalta');
    $('#Visualizar').prop('disabled', true)

    const GetPersonal = $('#GetPersonal').DataTable({
        pagingType: "full",
        lengthMenu: [[1], [1]],
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        searchDelay: 1500,
        dom: `
    <"row"
        <"col-12 d-flex justify-content-end p-0"
            <"d-flex justify-content-end align-items-end">
            <"d-inline-flex align-items-center"<"mt-2 mt-sm-1"t>
                <"d-none d-sm-block ml-1"p>
            >   
        >
        <"col-12"
            <"d-block d-sm-none mt-n2"p>
            <"d-flex justify-content-end align-items-end mt-n4 p-0"i>
        >
    >
        `,
        ajax: {
            url: "/" + $("#_homehost").val() + "/fichadas/GetPersonalFichas.php",
            type: "POST",
            "data": function (data) {
                data._l = $("#_l").val();
                data.Per = $("#Per").val();
                data.Per2 = $("#Per2").val();
                data.Tipo = $("#Tipo").val();
                data.Emp = $("#Emp").val();
                data.Plan = $("#Plan").val();
                data.Sect = $("#Sect").val();
                data.Sec2 = $("#Sec2").val();
                data.Grup = $("#Grup").val();
                data.Sucur = $("#Sucur").val();
                data._dr = $("#_dr").val();
                data.FicFalta = $('#FicFalta').val();
                data.onlyReg = $("#onlyReg:checked").val() ?? '';
            },

            error: function () {
                $("#GetPersonal_processing").css("display", "none");
            },
        },
        columns: [
            /** Columna Legajo */
            {
                className: 'w80 px-3 border fw4 bg-light radius pers_legajo', targets: 'pers_legajo',
                "render": function (data, type, row, meta) {
                    let datacol = '<input type="text" id="Per2" class="d-none border-0 border bg-white form-control-sm mr-2 w100 text-center" style="height: 20px;"><span class="numlega btn p-0 pointer fontq fw4">' + row.pers_legajo + '</span><input type="hidden" id="_l" value=' + row.pers_legajo + '>'

                    return '<div>' + datacol + '</div>';
                },
            },
            /** Columna Nombre */
            {
                className: 'w300 px-3 border border-left-0 fw4 bg-light radius', targets: 'pers_nombre',
                "render": function (data, type, row, meta) {
                    let datacol = row.pers_nombre
                    return '<div class="d-flex align-items-center justify-content-start" style="margin-top:1px"><span>' + datacol + '</span></div>';
                },
            }
        ],
        paging: true,
        responsive: false,
        info: true,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishPag.json" + "?" + vjs(),
        },
    });

    GetPersonal.on('draw.dt', function (e, settings, json) {
        $(".dataTables_info").addClass('text-secondary');
        $("#GetPersonal thead").remove();
        $("#GetPersonal").removeClass('invisible');
        if (settings.iDraw > 1) {
            $('#GetFichadas').DataTable().ajax.reload();
        }
    });

    GetPersonal.on('init.dt', function (e, settings, json) {
        $('#Per2').mask('000000000');
        if (settings.iDraw == 1) {
            const GetFichadas = $('#GetFichadas').DataTable({
                dom: "<'row'" +
                    "<'col-12 col-sm-6 d-flex align-items-start'l><'col-12 col-sm-6 d-inline-flex align-items-start justify-content-end'f>>" +
                    "<'row '<'col-12 table-responsive't>>" +
                    "<'row d-none d-sm-block'<'col-12 d-flex bg-transparent align-items-center justify-content-between'<i><p>>>" +
                    "<'row d-block d-sm-none'<'col-12 fixed-bottom h70 bg-white d-flex align-items-center justify-content-center'p>>" +
                    "<'row d-block d-sm-none'<'col-12 d-flex align-items-center justify-content-center'i>>",
                lengthMenu: [[5, 10, 30, 60, 90], [5, 10, 30, 60, 90]],
                bProcessing: false,
                serverSide: true,
                deferRender: true,
                fixedHeader: true,
                "bStateSave": true,
                ajax: {
                    url: "/" + $("#_homehost").val() + "/fichadas/GetFichadas.php",
                    type: "POST",
                    "data": function (data) {
                        data.Per = $("#Per").val();
                        data.Tipo = $("#Tipo").val();
                        data.Emp = $("#Emp").val();
                        data.Plan = $("#Plan").val();
                        data.Sect = $("#Sect").val();
                        data.Sec2 = $("#Sec2").val();
                        data.Grup = $("#Grup").val();
                        data.Sucur = $("#Sucur").val();
                        data._dr = $("#_dr").val();
                        data._l = $("#_l").val();
                        data.FicFalta = $('#FicFalta').val();
                        data.onlyReg = $("#onlyReg:checked").val();
                    },
                    error: function (e) {
                        // console.log(e.responseText);
                        $("#GetFichadas").css("display", "none");
                    },
                },
                columns: [
                    /** Columna Legajo */
                    {
                        className: '', targets: 'Fic_Lega', title: 'LEGAJO',
                        "render": function (data, type, row, meta) {
                            let datacol = row.Fic_Lega
                            return '<div>' + datacol + '</div>';
                        },
                    },
                    /** Columna Nombre */
                    {
                        className: '', targets: 'Fic_Nombre', title: 'NOMBRE',
                        "render": function (data, type, row, meta) {
                            let datacol = row.Fic_Nombre
                            return '<div>' + datacol + '</div>';
                        },
                    },
                    /** Columna Primera */
                    {
                        className: 'text-center', targets: 'Primera', title: '<span data-titler="Primer Fichada">PRIMERA</span>',
                        "render": function (data, type, row, meta) {
                            let datacol = '-'
                            let textColor = '';
                            let horaOriginal = ''
                            if (row.Fich) {
                                // console.log(row.Fich);
                                let countOfItems = row.Fich.length
                                if (countOfItems) {
                                    if (row.Fich[0]['Esta'] == 'Modificada') {
                                        horaOriginal = '. Hora original: ' + row.Fich[0]['Hora']
                                    }
                                    textColor = colorFic(row.Fich[0])
                                    datacol = '<span data-titlel="(# 1) ' + row.Fich[0]['HoRe'] + ' ' + row.Fich[0]['Tipo'] + ' ' + row.Fich[0]['Esta'] + horaOriginal + '"><span class="ls1">' + row.Fich[0]['HoRe'] + '</span></span>'
                                }
                                return '<div class="' + textColor + '">' + datacol + '</div>';
                            }
                        },
                    },
                    /** Columna Ultima */
                    {
                        className: 'text-center', targets: 'Ultima', title: '<span data-titler="Última Fichada">ÚLTIMA</span>',
                        "render": function (data, type, row, meta) {
                            let datacol = '-'
                            let textColor = '';
                            let horaOriginal = '';
                            if (row.Fich) {
                                let countOfItems = row.Fich.length
                                if (countOfItems > 1) {
                                    countOfItems = row.Fich.length - 1
                                    item = countOfItems + 1
                                    if (row.Fich[countOfItems]['Esta'] == 'Modificada') {
                                        horaOriginal = '. Hora original: ' + row.Fich[countOfItems]['Hora']
                                    }
                                    textColor = colorFic(row.Fich[countOfItems])
                                    datacol = '<span data-titlel="(# ' + item + ') ' + row.Fich[countOfItems]['HoRe'] + ' ' + row.Fich[countOfItems]['Tipo'] + ' ' + row.Fich[countOfItems]['Esta'] + horaOriginal + '"><span class="ls1">' + row.Fich[countOfItems]['HoRe'] + '</span></span>'
                                }
                            }
                            return '<div class="' + textColor + '">' + datacol + '</div>';
                        },
                    },
                    /** Columna DIA */
                    {
                        className: '', targets: 'Fic_Dia', title: 'DÍA',
                        "render": function (data, type, row, meta) {
                            let datacol = row.Fic_Dia
                            return '<div>' + datacol + '</div>';
                        },
                    },
                    /** Columna Fecha */
                    {
                        className: '', targets: 'Fic_Fecha', title: 'FECHA',
                        "render": function (data, type, row, meta) {
                            let datacol = row.Fic_Fecha
                            return '<div>' + datacol + '</div>';
                        },
                    },
                    /** Columna Horario */
                    {
                        className: '', targets: 'Fic_Horario', title: 'HORARIO',
                        "render": function (data, type, row, meta) {
                            let datacol = row.Fic_Horario
                            return '<div>' + datacol + '</div>';
                        },
                    },
                    /** Columna Fichadas */
                    {
                        className: '', targets: 'Fichadas', title: 'FICHADAS',
                        "render": function (data, type, row, meta) {
                            let datacol = ''
                            let total = ''
                            let horaOriginal = ''
                            let fic = []
                            if (row.Fich.length > 0) {
                                // datacol = row.Fichadas.replace(/,/g, " ")
                                total = '(' + row.Fich.length + ') ';
                                $.each(row.Fich, function (index, value) {
                                    if (value['Esta'] == 'Modificada') {
                                        horaOriginal = '. Hora original: ' + value['Hora']
                                    }
                                    fic.push('<span data-titlel="' + value['HoRe'] + ' ' + value['Tipo'] + ' ' + value['Esta'] + horaOriginal + '" class="' + colorFic(value) + '"> <span class="ls1">' + value['HoRe'] + '</span></span>')
                                });
                                return '<div>' + total + ' ' + fic + '</div>';
                            }
                            return datacol
                        },
                    },
                ],
                searching: false,
                ordering: false,
                language: {
                    "url": "../js/DataTableSpanishShort2.json" + "?" + vjs(),
                },
            });
            GetFichadas.on('init.dt', function (e, settings, json) {
                $("#Refresh").prop('disabled', false);
                $('#trash_all').removeClass('invisible');
                $(".Filtros").prop('disabled', false);
                $("#GetFichadasTable").fadeIn();
                $("#GetFichadasTable").removeClass('invisible');
            });
            GetFichadas.on('page.dt', function () {
                CheckSesion()
                loadingTable('#GetFichadas')
            });
        }
    });

    GetPersonal.on('page.dt', function (e, settings, json) {
        CheckSesion()
        loadingTable('#GetPersonal')
        loadingTable('#GetFichadas')
        if (settings.iDraw) {
            setTimeout(() => {
                $('#GetFichadas').DataTable().ajax.reload();
            }, 50);
        }
    });

    const GetFechas = $('#GetFechas').DataTable({
        pagingType: "full",
        lengthMenu: [[1], [1]],
        bProcessing: false,
        serverSide: true,
        deferRender: true,
        dom: `
        <"row"
            <"col-12 d-flex justify-content-end p-0"
                <"d-flex justify-content-end align-items-end">
                <"d-inline-flex align-items-center"<"mt-2 mt-sm-1"t>
                    <"d-none d-sm-block ml-1"p>
                >   
            >
            <"col-12"
                <"d-block d-sm-none mt-n2"p>
                <"d-flex justify-content-end align-items-end mt-n4 p-0"i>
            >
        >
            `,
        ajax: {
            url: "/" + $("#_homehost").val() + "/fichadas/GetFechasFichas.php",
            type: "POST",
            "data": function (data) {
                data.Per = $("#Per").val();
                data.Tipo = $("#Tipo").val();
                data.Emp = $("#Emp").val();
                data.Plan = $("#Plan").val();
                data.Sect = $("#Sect").val();
                data.Sec2 = $("#Sec2").val();
                data.Grup = $("#Grup").val();
                data.Sucur = $("#Sucur").val();
                data._dr = $("#_dr").val();
                data._l = $("#_l").val();
                data.FicFalta = $('#FicFalta').val();
                data.onlyReg = $("#onlyReg:checked").val();
            },
            error: function () {
                $("#GetFecha_processing").css("display", "none");
            },
        },
        columns: [
            /** Columna Fecha */
            {
                className: 'w80 px-3 border fw4 bg-light radius ls1', targets: 'FechaFormat',
                "render": function (data, type, row, meta) {
                    let datacol = '<span>' + row.FechaFormat + '</span><input type="hidden" class="" id="_f" value=' + row.Fecha + '>'
                    return '<div>' + datacol + '</div>';
                },
            },
            /** Columna Dia */
            {
                className: 'w300 px-3 border fw4 bg-light radius', targets: 'Dia',
                "render": function (data, type, row, meta) {
                    let datacol = row.Dia
                    return '<div class="">' + datacol + '</div>';
                },
            },
        ],
        paging: true,
        responsive: false,
        info: true,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishPagFech.json" + "?" + vjs(),
        },
    });

    GetFechas.on('init.dt', function (e, settings, json) {
        $("#GetFechas").removeClass('invisible');
        $("#GetFechas thead").remove();
        $("#GetFichadasFechaTable").removeClass('invisible');
        $(".dataTables_info").addClass('text-secondary');
        if (settings.iDraw == 1) {
            const GetFichadasFecha = $('#GetFichadasFecha').DataTable({
                dom: "<'row'" +
                    "<'col-12 col-sm-6 d-flex align-items-start'l><'col-12 col-sm-6 d-inline-flex align-items-start justify-content-end'f>>" +
                    "<'row '<'col-12 table-responsive't>>" +
                    "<'row d-none d-sm-block'<'col-12 d-flex bg-transparent align-items-center justify-content-between'<i><p>>>" +
                    "<'row d-block d-sm-none'<'col-12 fixed-bottom h70 bg-white d-flex align-items-center justify-content-center'p>>" +
                    "<'row d-block d-sm-none'<'col-12 d-flex align-items-center justify-content-center'i>>",
                lengthMenu: [[5, 10, 30, 60, 90], [5, 10, 30, 60, 90]],
                bProcessing: false,
                serverSide: true,
                deferRender: true,
                "bStateSave": true,
                ajax: {
                    url: "/" + $("#_homehost").val() + "/fichadas/GetFichadasFecha.php",
                    type: "POST",
                    "data": function (data) {
                        data._f = $("#_f").val();
                        data.Per = $("#Per").val();
                        data.Tipo = $("#Tipo").val();
                        data.Emp = $("#Emp").val();
                        data.Plan = $("#Plan").val();
                        data.Sect = $("#Sect").val();
                        data.Sec2 = $("#Sec2").val();
                        data.Grup = $("#Grup").val();
                        data.Sucur = $("#Sucur").val();
                        data._dr = $("#_dr").val();
                        data._l = $("#_l").val();
                        data.FicFalta = $('#FicFalta').val();
                        data.onlyReg = $("#onlyReg:checked").val();
                    },
                    error: function () {
                        $("#GetFichadasFecha_processing").css("display", "none");
                    },
                },
                columns: [
                    /** Columna Legajo */
                    {
                        className: '', targets: 'Fic_Lega', title: 'LEGAJO',
                        "render": function (data, type, row, meta) {
                            let datacol = row.Fic_Lega
                            return '<div>' + datacol + '</div>';
                        },
                    },
                    /** Columna Nombre */
                    {
                        className: '', targets: 'Fic_Nombre', title: 'NOMBRE',
                        "render": function (data, type, row, meta) {
                            let datacol = row.Fic_Nombre
                            return '<div>' + datacol + '</div>';
                        },
                    },
                    /** Columna Primera */
                    {
                        className: 'text-center', targets: 'Primera', title: '<span data-titler="Primer Fichada">PRIMERA</span>',
                        "render": function (data, type, row, meta) {
                            let datacol = '-'
                            let textColor = '';
                            let horaOriginal = ''
                            if (row.Fich) {
                                // console.log(row.Fich);
                                let countOfItems = row.Fich.length
                                if (countOfItems) {
                                    if (row.Fich[0]['Esta'] == 'Modificada') {
                                        horaOriginal = '. Hora original: ' + row.Fich[0]['Hora']
                                    }
                                    textColor = colorFic(row.Fich[0])
                                    datacol = '<span data-titlel="(# 1) ' + row.Fich[0]['HoRe'] + ' ' + row.Fich[0]['Tipo'] + ' ' + row.Fich[0]['Esta'] + horaOriginal + '"><span class="ls1">' + row.Fich[0]['HoRe'] + '</span></span>'
                                }
                                return '<div class="' + textColor + '">' + datacol + '</div>';
                            }
                        },
                    },
                    /** Columna Ultima */
                    {
                        className: 'text-center', targets: 'Ultima', title: '<span data-titler="Última Fichada">ÚLTIMA</span>',
                        "render": function (data, type, row, meta) {
                            let datacol = '-'
                            let textColor = '';
                            let horaOriginal = '';
                            if (row.Fich) {
                                let countOfItems = row.Fich.length
                                if (countOfItems > 1) {
                                    countOfItems = row.Fich.length - 1
                                    item = countOfItems + 1
                                    if (row.Fich[countOfItems]['Esta'] == 'Modificada') {
                                        horaOriginal = '. Hora original: ' + row.Fich[countOfItems]['Hora']
                                    }
                                    textColor = colorFic(row.Fich[countOfItems])
                                    datacol = '<span data-titlel="(# ' + item + ') ' + row.Fich[countOfItems]['HoRe'] + ' ' + row.Fich[countOfItems]['Tipo'] + ' ' + row.Fich[countOfItems]['Esta'] + horaOriginal + '"><span class="ls1">' + row.Fich[countOfItems]['HoRe'] + '</span></span>'
                                }
                            }
                            return '<div class="' + textColor + '">' + datacol + '</div>';
                        },
                    },
                    /** Columna DIA */
                    {
                        className: '', targets: 'Fic_Dia', title: 'DÍA',
                        "render": function (data, type, row, meta) {
                            let datacol = row.Fic_Dia
                            return '<div>' + datacol + '</div>';
                        },
                    },
                    /** Columna Fecha */
                    {
                        className: '', targets: 'Fic_Fecha', title: 'FECHA',
                        "render": function (data, type, row, meta) {
                            let datacol = row.Fic_Fecha
                            return '<div>' + datacol + '</div>';
                        },
                    },
                    /** Columna Horario */
                    {
                        className: '', targets: 'Fic_Horario', title: 'HORARIO',
                        "render": function (data, type, row, meta) {
                            let datacol = row.Fic_Horario
                            return '<div>' + datacol + '</div>';
                        },
                    },
                    /** Columna Fichadas */
                    {
                        className: '', targets: 'Fichadas', title: 'FICHADAS',
                        "render": function (data, type, row, meta) {
                            let datacol = ''
                            let total = ''
                            let horaOriginal = ''
                            let fic = []
                            if (row.Fich.length > 0) {
                                // datacol = row.Fichadas.replace(/,/g, " ")
                                total = '(' + row.Fich.length + ') ';
                                $.each(row.Fich, function (index, value) {
                                    if (value['Esta'] == 'Modificada') {
                                        horaOriginal = '. Hora original: ' + value['Hora']
                                    }
                                    fic.push('<span data-titlel="' + value['HoRe'] + ' ' + value['Tipo'] + ' ' + value['Esta'] + horaOriginal + '" class="' + colorFic(value) + '"> <span class="ls1">' + value['HoRe'] + '</span></span>')
                                });
                                return '<div>' + total + ' ' + fic + '</div>';
                            }
                            return datacol
                        },
                    },
                ],
                paging: true,
                info: true,
                searching: false,
                ordering: false,
                language: {
                    "url": "../js/DataTableSpanishShort2.json" + "?" + vjs(),
                },
            });
            GetFichadasFecha.on('init.dt', function (e, settings, json) {
                $("#GetFichadasFecha").removeClass('invisible');
                $(".Filtros").prop('disabled', false);
                $('#Visualizar').prop('disabled', false)
            });
            GetFichadasFecha.on('page.dt', function () {
                CheckSesion()
                loadingTable('#GetFichadasFecha')
            });
        }
    });

    GetFechas.on('page.dt', function () {
        CheckSesion()
        loadingTable('#GetFechas')
        loadingTable('#GetFichadasFecha')
        setTimeout(() => {
            $('#GetFichadasFecha').DataTable().ajax.reload();
        }, 50);
    });

    GetFechas.on('draw.dt', function (e, settings, json) {
        if (settings.iDraw > 1) {
            $('#GetFichadasFecha').DataTable().ajax.reload();
        }
    });

    $("#Refresh").on("click", function (e) {
        e.preventDefault();
        CheckSesion()
        ActualizaTablas()
    });

    $("#_dr").change(function (e) {
        e.preventDefault();
        loadingTable('#GetFichadas')
        loadingTable('#GetFichadasFecha')
        // ActualizaTablas()
        CheckSesion()
    });

    $('#_dr').on('apply.daterangepicker', function (ev, picker) {
        ActualizaTablas()
    });

    document.getElementById("VerPor").innerHTML = "Visualizar por Fecha"

    $("#Visualizar").change(function () {
        CheckSesion()
        // $("#loader").addClass('loader');
        if ($("#Visualizar").is(":checked")) {
            $('#GetFechas').DataTable().ajax.reload();
            $("#GetFichadasTable").hide()
            $("#GetFichadasFechaTable").show()
            $("#pagLega").addClass('d-none');
            $("#pagFech").removeClass('d-none')
            $("#GetFichadasFechaTable").removeClass('d-none');
            // $('#VerPor').html('Visualizar por Legajo')
        } else {
            $('#GetPersonal').DataTable().ajax.reload();
            $("#GetFichadasTable").show()
            $("#GetFichadasFechaTable").hide()
            $("#pagLega").removeClass('d-none')
            $("#pagFech").addClass('d-none')
            // $('#VerPor').html('Visualizar por Fecha')
        }
    });
    /** Select */
    // $(function () {
    $('#Tipo').css({ "width": "200px" });
    $('.form-control').css({ "width": "100%" });

    function Select2Estruct(selector, multiple, placeholder, estruct, url, parent) {
        let opt2 = { MinLength: "0", SelClose: false, MaxInpLength: "10", delay: "250", allowClear: true };
        $(selector).select2({
            multiple: multiple,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: parent,
            placeholder: placeholder,
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 5,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function () {
                    return 'No hay resultados..'
                },
                inputTooLong: function (args) {
                    var message = 'Máximo ' + opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
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
                    return 'Ingresar ' + opt2["MinLength"] + ' o mas caracteres'
                },
                maximumSelected: function () {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: url,
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                cache: false,
                data: function (params) {
                    return {
                        q: params.term,
                        estruct: estruct,
                        Per: $("#Per").val(),
                        Tipo: $("#Tipo").val(),
                        Emp: $("#Emp").val(),
                        Plan: $("#Plan").val(),
                        Sect: $("#Sect").val(),
                        Sec2: $("#Sec2").val(),
                        Grup: $("#Grup").val(),
                        Sucur: $("#Sucur").val(),
                        _dr: $("#_dr").val(),
                        _l: $("#_l").val(),
                        FicFalta: $("#FicFalta").val(),
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            },
        });
    }

    $('#Filtros').on('shown.bs.modal', function () {
        let url = "/" + $("#_homehost").val() + "/fichadas/getSelect/getEstruct.php";
        let Empr = Select2Estruct(".selectjs_empresa", true, "Empresas", "Empr", url, $('#Filtros'));
        let Plan = Select2Estruct(".selectjs_plantas", true, "Plantas", "Plan", url, $('#Filtros'));
        let Sect = Select2Estruct(".selectjs_sectores", true, "Sectores", "Sect", url, $('#Filtros'));
        let Sec2 = Select2Estruct(".select_seccion", true, "Secciones", "Sec2", url, $('#Filtros'));
        let Grup = Select2Estruct(".selectjs_grupos", true, "Grupos", "Grup", url, $('#Filtros'));
        let Sucu = Select2Estruct(".selectjs_sucursal", true, "Sucursales", "Sucu", url, $('#Filtros'));
        let Lega = Select2Estruct(".selectjs_personal", true, "Legajos", "Lega", url, $('#Filtros'));
        let Tipo = Select2Estruct(".selectjs_tipoper", false, "Tipo de Personal", "Tipo", url, $('#Filtros'));

        function refreshSelected(slectjs) {
            $(slectjs).on('select2:select', function (e) {
                CheckSesion()
                $('#Per2').val(null)
            });
        }
        function refreshUnselected(slectjs) {
            $(slectjs).on('select2:unselecting', function (e) {
                CheckSesion()
                $('#Per2').val(null)
            });
        }

        // $(".form-control").click(function (e) {
        // $('input').on('focus', function (e) {
        //     e.preventDefault();
        //     CheckSesion()
        //     e.stopPropagation();
        // });

        refreshSelected('.selectjs_empresa');
        refreshSelected('.selectjs_plantas');
        refreshSelected('.select_seccion');
        refreshSelected('.selectjs_grupos');
        refreshSelected('.selectjs_sucursal');
        refreshSelected('.selectjs_personal');
        refreshSelected('.selectjs_tipoper');

        refreshUnselected('.selectjs_empresa');
        refreshUnselected('.selectjs_plantas');
        refreshUnselected('.select_seccion');
        refreshUnselected('.selectjs_grupos');
        refreshUnselected('.selectjs_sucursal');
        refreshUnselected('.selectjs_personal');
        refreshUnselected('.selectjs_tipoper');

        $('.selectjs_sectores').on('select2:select', function (e) {
            CheckSesion()
            $('#Per2').val(null)
            $(".select_seccion").prop("disabled", false);
            $('.select_seccion').val(null).trigger('change');
            // ActualizaTablas()
            var nombresector = $('.selectjs_sectores :selected').text();
            $("#DatosFiltro").html('Sector: ' + nombresector);
        });
        $('.selectjs_sectores').on('select2:unselecting', function (e) {
            CheckSesion()
            $('#Per2').val(null)
            $(".select_seccion").prop("disabled", true);
            $('.select_seccion').val(null).trigger('change');
            // ActualizaTablas()
        });
        $('.selectjs_personal').on('select2:select', function (e) {
            CheckSesion()
            $('#Per2').val(null)
            // ActualizaTablas()
        });
    });
    // });
    onOpenSelect2()
    $('#Filtros').on('hidden.bs.modal', function (e) {
        CheckSesion()
        ActualizaTablas()
    });

    function TrashSelect(slectjs) {
        CheckSesion()
        $(slectjs).val(null).trigger("change");
        ActualizaTablas()
    }

    function LimpiarFiltros() {
        CheckSesion()
        $('.selectjs_plantas').val(null).trigger("change");
        $('.selectjs_empresa').val(null).trigger("change");
        $('.selectjs_sectores').val(null).trigger("change");
        $('.select_seccion').val(null).trigger("change");
        $(".select_seccion").prop("disabled", true);
        $('.selectjs_grupos').val(null).trigger("change");
        $('.selectjs_sucursal').val(null).trigger("change");
        $('.selectjs_personal').val(null).trigger("change");
        $('.selectjs_tipoper').val(null).trigger("change");
        $('#FicFalta').prop('checked', false)
        $('#FicFalta').val(0)
        $('#Per2').val(null)
    }
    function LimpiarFiltros2() {
        CheckSesion()
        $('.selectjs_plantas').val(null).trigger("change");
        $('.selectjs_empresa').val(null).trigger("change");
        $('.selectjs_sectores').val(null).trigger("change");
        $('.select_seccion').val(null).trigger("change");
        $(".select_seccion").prop("disabled", true);
        $('.selectjs_grupos').val(null).trigger("change");
        $('.selectjs_sucursal').val(null).trigger("change");
        $('.selectjs_personal').val(null).trigger("change");
        $('.selectjs_tipoper').val(null).trigger("change");
    }
    $("#trash_all").on("click", function () {
        CheckSesion()
        $('#Filtros').modal('show')
        LimpiarFiltros()
        $('#Filtros').modal('hide')
        ActualizaTablas()
    });

    $("#trash_allIn").on("click", function () {
        CheckSesion()
        LimpiarFiltros()
        // ActualizaTablas()
    });

    $(document).on("click", ".numlega", function (e) {
        CheckSesion()
        $('#Per2').val(null)
        $(this).addClass('d-none')
        $('#Per2').removeClass('d-none')
        $('#Per2').focus();
    });

    function refreshOnChange(selector) {
        $(document).on("change", selector, function (e) {
            $('#Filtros').modal('show')
            LimpiarFiltros2()
            $('#Filtros').modal('hide')
            ActualizaTablas()
            GetPersonal.on('xhr', function () {
                var json = GetPersonal.ajax.json();
                // $("#tableInfo").html(json.recordsTotal)
                if (json.recordsTotal) {
                    $('#GetFichadasTable').removeClass('d-none')
                } else {
                    $('#GetFichadasTable').addClass('d-none')
                }
            });
        });
    };
    refreshOnChange("#Per2");

});