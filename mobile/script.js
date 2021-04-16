$(document).ready(function () {

    $("#Refresh").on("click", function () {
        $('#table-mobile').DataTable().ajax.reload();
        $(".dataTables_scrollBody").addClass("opa2");
    });

    $('#btnFiltrar').removeClass('d-sm-block');

    $('#table-mobile').DataTable({
        // "initComplete": function (settings, json) {
         
        // },
        "drawCallback": function (settings) {
            // console.log(jQuery.makeArray(settings.json));
            var arrayJson = jQuery.makeArray(settings.json)
            $.each(arrayJson, function (key, value) {
                // console.log(value['success']);
                $('.appcode').html('<b>'+value['AppCode']+'</b>')
                $('.cuenta').html('<b>'+value['Cuenta']+'</b>')
                if (value['success']=='YES') {
                        $("#alertmessage").fadeOut("slow");
                        setTimeout(function () {
                            $("#alertmessage").remove();
                        }, 1000);
                }else{
                    $('#RowTableMobile').append('<div class="col-12 mt-2"><div class="alert alert-danger" id="alertmessage">' + value['message'] + '</div></div>')
                }
            });

            $(".dataTables_scrollBody").removeClass("opa2");
            $('.form-control-sm').attr('placeholder', 'Buscar')
        },
        // orderFixed: [[0, "desc"]],
        // rowGroup: {
        //     dataSrc: ['Fecha2']
        // },
        // sort: true,
        // order: [[3, "desc"], [5, "desc"]],
        columnDefs: [
            // { "visible": false, "targets": 0 , "type": "html"},
            { "visible": true, "targets": 0, "orderable": false },
            { "visible": true, "targets": 6, "orderable": false },
            { "visible": true, "targets": 7, "orderable": false }
        ],
        iDisplayLength: -1,
        bProcessing: true,
        ajax: {
            url: "?p=array_mobile.php",
            type: "POST",
            dataSrc: "mobile",
            "data": function (data) {
                data._drMob = $("#_drMob").val();
            },
        },

        createdRow: function (row, data, dataIndex) {
            $(row).addClass('animate__animated animate__fadeIn align-middle');
        },
        columns: [
            {
                "data": "face_url"
            },
            {
                "data": "uid"
            },
            {
                "class": '',
                "data": "name"
            },
            {
                "class": '',
                "data": "Fecha2"
            },
            {
                "class": "",
                "data": "Fecha"
            },
            {
                "class": "ls1 fw5",
                "data": "time"
            }, {
                "class": "text-center",
                "data": "mapa"
            }, {
                "class": "text-center",
                "data": "similarity"
            }, {
                "data": "zone"
            }, {
                "data": "IN_OUT"
            }, {
                "data": "t_type"
            }],

        deferRender: true,
        paging: false,
        searching: true,
        scrollY: '50vh',
        scrollX: true,
        scrollCollapse: true,
        info: true,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort2.json"
        },

    });

    $("#_drMob").on("change", function () {
        $('#table-mobile').DataTable().ajax.reload(null,false);
        $(".dataTables_scrollBody").addClass("opa2");
    });

    function initMap() {

        var lati = parseFloat($('#latitud').val())
        var long = parseFloat($('#longitud').val())
        var zona = ($('#zona').val())
        var zona = (zona) ? zona : 'Fuera de Zona';
        var radio = parseFloat($('#map_size').val())

        const myLatLng = {
            lat: lati,
            lng: long
        };

        const map = new google.maps.Map(document.getElementById("mapzone"), {
            zoom: 15,
            center: myLatLng,
            mapTypeId: google.maps.MapTypeId.TERRAIN,
            zoomControl: false,
            mapTypeControl: false,
            scaleControl: false,
            streetViewControl: false,
            rotateControl: false,
            fullscreenControl: true,
            // mapTypeId: "terrain",
            // gestureHandling: "cooperative", /** para anular el zoom del scroll */
        });
        const contentString = '<div id="content"><span>' + zona + "</span></div>";

        const infowindow = new google.maps.InfoWindow({
            content: contentString,
        });

        const image = "../img/marker.png";
        const marker = new google.maps.Marker({
            position: myLatLng,
            map,
            // animation: google.maps.Animation.DROP,
            // draggable: true,
            icon: image,
            title: zona
        })

        marker.addListener("click", () => {
            infowindow.open(map, marker);
        });

        var sunCircle = {
            strokeColor: "#0388D1",
            strokeOpacity: 1,
            strokeWeight: 1,
            fillColor: "#0388D1",
            fillOpacity: 0.25,
            map: map,
            center: myLatLng,
            radius: radio // en metros
        };
        cityCircle = new google.maps.Circle(sunCircle)
        cityCircle.bindTo('center', marker, 'position');
    }
    $('.select2').select2({
        minimumResultsForSearch: -1,
        placeholder: "Seleccionar"
    });

    $(document).on("click", ".pic", function (e) {

        $('#pic').modal('show')

        var picfoto = $(this).attr('datafoto');
        var picnombre = $(this).attr('dataname');
        var picuid = $(this).attr('datauid');
        var piccerteza = $(this).attr('datacerteza');
        var piccerteza2 = $(this).attr('datacerteza2');
        var picinout = $(this).attr('datainout');
        var piczone = $(this).attr('datazone');
        var pichora = $(this).attr('datahora');
        var picgps = $(this).attr('datagps');
        var pictype = $(this).attr('datatype');
        var picdia = $(this).attr('datadia');
        var _lat = $(this).attr('datalat');
        var _lng = $(this).attr('datalng');

        $('#latitud').val(_lat)
        $('#longitud').val(_lng)

        $("input[name=lat]").val(_lat);
        $("input[name=lng]").val(_lng);

        $('#zona').val(piczone)
        if (picfoto) {
            $('.picFoto').html('<img loading="lazy" src="https://server.xenio.uy/' + picfoto + '" class="w150 img-fluid rounded">');
        } else {
            $('.picFoto').html('<img loading="lazy" src="../img/user.png" class="img-fluid rounded" alt="Sin Foto" title="Sin Foto">');
        }

        $('.picName').html(picnombre);
        $('.picUid').html(picuid);
        $('.picHora').html('<b>' + pichora + '</b>');
        $('.picModo').html(picinout);
        $('.picTipo').html(pictype);
        $('.picDia').html(picdia);

        var position = (parseFloat(_lat) + parseFloat(_lng))

        if (piccerteza > 70) {
            $('.picCerteza').html('<img src="../img/check.png" class="w15" alt="' + piccerteza + '" title="' + piccerteza + '">&nbsp;<span class="fontp fw4 text-success">(' + piccerteza2 + ')</span>');
        } else {
            $('.picCerteza').html('<img src="../img/uncheck.png" class="w15" alt="' + piccerteza + '" title="' + piccerteza + '">&nbsp;<span class="fontp fw4 text-danger">(' + piccerteza2 + ')</span>');
        }
        if (position != '0') {
            if (piczone) {
                $('#btnCrearZona').addClass('d-none')
            } else {
                $('#btnCrearZona').removeClass('d-none')
            }
            var zone = (piczone) ? '<span class="text-success">' + piczone + '</span>' : '<span class="text-danger">Fuera de Zona</span>';
            $('.picZona').html(zone);
        } else {
            $('.picZona').html('Sin ubicaci&oacute;n');
        }
        // console.log(position);
        if (position != '0') {
            $('#mapzone').removeClass('d-none');
            initMap()
        } else {
            $('#mapzone').addClass('d-none');
            $('#btnCrearZona').addClass('d-none')
        }

        $(document).on("click", "#btnCrearZona", function (e) {
            fadeInOnly('#rowCreaZona')
            $("#rowRespuesta").addClass("d-none");

            $("#map_size").val('200')
            initMap()
            fadeInOnly('#mapzone')

            $('.select2').on('select2:select', function (e) {
                var select_val = $(e.currentTarget).val();
                $("#map_size").val(select_val)
                initMap()
                fadeInOnly('#mapzone')
            });

            $('#rowCreaZona').removeClass('d-none')

            $("#CrearZona").bind("submit", function (e) {
                e.preventDefault();
                $.ajax({
                    type: $(this).attr("method"),
                    url: $(this).attr("action"),
                    data: $(this).serialize(),
                    // dataType: "json",
                    beforeSend: function (data) {
                        $("#btnSubmitZone").prop("disabled", true);
                        $("#btnSubmitZone").html("Creando Zona.!");
                    },
                    success: function (data) {
                        if (data.status == "ok") {
                            $('#btnCrearZona').addClass('d-none')
                            $("#btnSubmitZone").prop("disabled", false);
                            $("#btnSubmitZone").html("Aceptar");
                            $("#rowRespuesta").removeClass("d-none");
                            $("#respuesta").html('<div class="alert alert-success fontq"><b>¡Zona creada correctamente!<br>La misma se ver&aacute; reflejada en futuras marcaciones.</b></div>')
                            $("#rowCreaZona").addClass("d-none");
                            setTimeout(function () {
                                $('#rowRespuesta').addClass('d-none')
                            }, 4000);
                            $("#map_size").val(data.radio)
                            initMap()
                        } else {
                            $("#btnSubmitZone").prop("disabled", false);
                            $("#btnSubmitZone").html("Aceptar");
                        }
                    },
                    error: function () {
                        $("#btnSubmitZone").prop("disabled", false);
                        $("#btnSubmitZone").html("Aceptar");
                        // $("#rowCreaZona").hide();
                    }
                });
            });

        });
        $(document).on("click", "#cancelZone", function (e) {
            clean()
            initMap()
        });
    });

    $('#pic').on('hidden.bs.modal', function (e) {
        clean()
    })

    function clean() {
        // $('#mapzone').addClass('d-none');
        $("#btnSubmitZone").prop("disabled", false);
        $("#btnSubmitZone").html("Aceptar");
        $("input[name=nombre]").val('');
        $('.select2').val('200').trigger("change");
        $('#rowRespuesta').addClass('d-none')
        $("#rowCreaZona").addClass("d-none");
        $("#map_size").val('5')
        $('#btnCrearZona').removeClass('d-none')
    }

    $(".selectjs_cuentaToken").select2({
        multiple: false,
        language: "es",
        placeholder: "Cambiar de Cuenta",
        minimumInputLength: '0',
        minimumResultsForSearch: -1,
        maximumInputLength: '10',
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
            url: "GetTokenCuenta.php",
            dataType: "json",
            type: "POST",
            // delay: opt2["delay"],
            data: function (params) {
                return {}
            },
            processResults: function (data) {
                return {
                    results: data
                }
            },
        }
    });

    $('.selectjs_cuentaToken').on('select2:select', function (e) {
        $("#RefreshToken").submit();
    });
    $("#RefreshToken").bind("submit", function (e) {
        e.preventDefault();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            // dataType: "json",
            beforeSend: function (data) { },
            success: function (data) {
                if (data.status == "ok") {
                    $('#table-mobile').DataTable().ajax.reload();
                    $(".dataTables_scrollBody").addClass("opa2");
                }
            },
            error: function () { }
        });
    });

});