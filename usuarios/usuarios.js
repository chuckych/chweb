$(document).ready(function () {

    $('.requerido').addClass('fontp ml-1 ls1');
    $('.requerido').html('(*)');

    const get_cliente = async () => {
        const recid_c = $('#recid_c').val() ?? '';
        if (!recid_c) return;
        await axios.get(`../app-data/_local/clientes/?recid=${recid_c}`).then(res => {
            if (res.data) {
                const cuenta = res.data?.[0] ?? '';
                if (!cuenta) return;

                const activeAD = cuenta?.activeAD ?? 0;
                const divImprAD = document.getElementById('div_import_ad') ?? null;
                if (!divImprAD) return;

                divImprAD.style.display = (activeAD == 1) ? 'block' : 'none';

                const serverAD = cuenta?.serverAD ?? '';
                const puertoAD = cuenta?.puertoAD ?? '';
                const serviceUserAD = cuenta?.serviceUserAD ?? '';
                const servicePassAD = cuenta?.servicePassAD ?? '';
                const baseDNAD = cuenta?.baseDNAD ?? '';
                const domainAD = cuenta?.domainAD ?? '';

                const object = {
                    serverAD,
                    puertoAD,
                    baseDNAD,
                    domainAD,
                    serviceUserAD,
                    servicePassAD
                };

                obtener_usuarios('#btnImportarAD', object);
            }
        });
    }
    const get_usuarios = async () => {
        try {
            const res = await axios.get(`../app-data/_local/usuarios`);
            if (res.data) {
                const data = res.data?.DATA ?? [];
                // Convertir el array en un objeto indexado por username para búsqueda rápida
                const usersByUsername = {};
                data.forEach(user => {
                    usersByUsername[user.usuario] = user;
                });
                return usersByUsername;
            }
            return {};
        } catch (error) {
            console.error('Error al obtener usuarios:', error);
            return {};
        }
    }
    const selectRolAD = (selector) => {
        const opt2 = {
            MinLength: "0",
            SelClose: false,
            MaxInpLength: "10",
            delay: "250",
            allowClear: false
        };
        $(selector).select2({
            multiple: false,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: $('#modalUserAD'),
            placeholder: "Rol",
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
                url: "getRol.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q: params.term,
                        recid_c: $("#recid_c").val(),
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        });
    }
    const alta_usuario_ad = async (object, rowElement) => {
        $.notifyClose();
        notifyWait('Agregando usuario...');

        if (!object.rol) {
            $.notifyClose();
            notify('Debe seleccionar un rol para el usuario', 'danger', 5000, 'right');
            return;
        }
        axios.post('../app-data/_local/usuarios', {
            recid_c: object.recid_c,
            nombre: object.nombre,
            usuario: object.usuario,
            rol: object.rol,
            clave: object.clave,
            legajo: object.legajo,
            user_ad: '1'
        }).then(res => {

            const RESPONSE_CODE = res.data?.RESPONSE_CODE ?? '';
            const MESSAGE = res.data?.MESSAGE ?? '';
            $.notifyClose();

            if (RESPONSE_CODE !== '200 OK') {
                notify(MESSAGE, 'danger', 5000, 'right');
                return;
            }

            notify(MESSAGE, 'success', 2000, 'right');
            $('#GetUsuarios').DataTable().ajax.reload();

            // Actualizar la fila en el DataTable de AD sin recargar toda la tabla
            if (rowElement) {
                const table = $('#tableUserAD').DataTable();
                const row = table.row(rowElement);
                const rowData = row.data();

                // Actualizar el dato user_exist a true
                rowData.user_exist = true;

                // Invalidar y redibujar solo esta fila
                row.data(rowData).draw(false);
            }
        });
    }
    const obtener_usuarios = async (selector, object) => {
        $(selector).off('click').on('click', async function (e) {
            e.preventDefault();
            notifyWait('Probando conexión AD...');

            if (!object.serverAD || !object.puertoAD) {
                $.notifyClose();
                notify('Faltan datos de configuración del servicio AD en el cliente', 'danger', 5000, 'right');
                return;
            }
            if (!object.serviceUserAD || !object.servicePassAD) {
                $.notifyClose();
                notify('Faltan datos de usuario/contraseña del servicio AD en el cliente', 'danger', 5000, 'right');
                return;
            }

            axios.post('../app-data/_local/test_ad', {
                'serverAD': object.serverAD,
                'puertoAD': object.puertoAD,
                'serviceUserAD': object.serviceUserAD,
                'servicePassAD': object.servicePassAD,
                'baseDNAD': object.baseDNAD,
                'domainAD': object.domainAD
            }).then(res => {
                $.notifyClose();
                const message = res.data.MESSAGE ?? '';
                if ((res.data.RESPONSE_CODE ?? '') == '200 OK') {

                    const info = res.data?.DATA?.server_info ?? [];
                    const defaultnamingcontext = info?.defaultnamingcontext ?? null;
                    const dns_hostname = info?.dns_hostname ?? null;
                    const usuarios = info?.usuarios ?? [];

                    if (usuarios.length == 0) {
                        notify('Conexión exitosa al AD (' + dns_hostname + '). No se encontraron usuarios.', 'warning', 7000, 'right');
                        return;
                    }

                    // Obtener usuarios existentes y agregar propiedad user_exist
                    get_usuarios().then((data_users) => {

                        // Agregar propiedad user_exist a cada usuario
                        const usersWithExistFlag = usuarios.map(element => {
                            const userExists = data_users[element.username] ? true : false;
                            return {
                                ...element,
                                user_exist: userExists
                            };
                        });


                        // Crear el modal y DataTable con todos los usuarios
                        $('#modalUserAD .modal-body').html('');

                        const html = `<div class="table-responsive border p-2"><table class="table table-hover text-nowrap w-100 " id="tableUserAD" style="margin-top: 0px !important;"></table></div>`;

                        $('#modalUserAD .modal-body').html(html);

                        $('#tableUserAD').DataTable({
                            dom: `
                                <'row'
                                    <'col-12 d-inline-flex justify-content-between align-items-center'lf>
                                >
                                <'row' <'col-12'<'select-rol py-2'>>>
                                <'row' <'col-12'<'border table-responsive't>>>
                                <'row'
                                    <'col-12 d-inline-flex justify-content-between align-items-center mt-2'ip>
                                >
                                `,
                            "data": usersWithExistFlag,
                            initComplete: function (settings, json) {
                                // remover thead
                                $('#tableUserAD thead').remove();
                                const selectRol = document.querySelector('.select-rol') ?? null;
                                if (!selectRol) return;
                                const div = document.createElement('div');
                                div.className = 'w-100';
                                div.innerHTML = `<label>Seleccionar un Rol</label>
                                    <select class="form-select form-select-sm selected-rol" style="width: 100%;">
                                    </select>
                               `;
                                selectRol.appendChild(div);
                                const select = div.querySelector('select');
                                if (!select) return;

                                selectRolAD(select);

                            },
                            drawCallback: function (settings) {
                            },
                            columns: [
                                {
                                    className: 'w-100 align-middle', targets: '', title: '',
                                    render: function (data, type, row, meta) {
                                        const user_exist = row.user_exist ?? false;
                                        const btnAdd = `<div class="float-right hint hint--left" aria-label="Agregar usuario"><button class="btn btn-sm btn-outline-custom border btn_add_user_ad"><i class="bi bi-plus"></i></button></div>`;
                                        const btnExist = `<div class="float-right hint hint--left hint--success" aria-label="Usuario existente"><i class="bi bi-check-all p-2"></i></div>`;

                                        return `
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>${row.username}</strong>
                                                    <br>${row.nombre}
                                                </div>
                                                ${!user_exist ? btnAdd : btnExist}
                                            </div>
                                            `
                                    }, visible: true, orderable: false
                                },
                            ],
                            order: [[0, 'desc']],
                            lengthMenu: [[5, 10, 25], [5, 10, 25]], //mostrar cantidad de registros
                            deferRender: true,
                            paging: true,
                            searching: true,
                            info: true,
                            ordering: true,
                            language:
                            {
                                "sProcessing": "Actualizando . . .",
                                "sLengthMenu": "_MENU_",
                                "sZeroRecords": "",
                                "sEmptyTable": "",
                                "sInfo": "_START_ a _END_ de _TOTAL_ usuarios",
                                "sInfoEmpty": "No se encontraron resultados",
                                "sInfoFiltered": "(filtrado de un total de _MAX_ usuarios)",
                                "sInfoPostFix": "",
                                "sSearch": "",
                                "sUrl": "",
                                "sInfoThousands": ",",
                                "oPaginate": {
                                    "sFirst": "<i class='bi bi-chevron-left'></i>",
                                    "sLast": "<i class='bi bi-chevron-right'></i>",
                                    "sNext": "<i class='bi bi-chevron-right'></i>",
                                    "sPrevious": "<i class='bi bi-chevron-left'></i>"
                                },
                                "sLoadingRecords": "<div class='spinner-border text-light'></div>",
                            },
                        });

                        $('#modalUserAD').modal('show');

                        const btnAddUserAD = document.querySelector('#modalUserAD .modal-body') ?? null;
                        if (!btnAddUserAD) return;
                        btnAddUserAD.addEventListener('click', function (e) {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            const target = e.target;
                            if (target && target.closest('.btn_add_user_ad')) {
                                const btn = target.closest('.btn_add_user_ad');
                                const tr = btn.closest('tr');
                                const table = $('#tableUserAD').DataTable();
                                const data = table.row(tr).data();
                                if (!data) return;

                                alta_usuario_ad({
                                    recid_c: $('#recid_c').val() ?? '',
                                    nombre: data.nombre,
                                    usuario: data.username,
                                    rol: $('.selected-rol').val() ?? '',
                                    clave: '',
                                    legajo: '0',
                                    user_ad: '1'
                                }, tr); // Pasar el elemento tr para actualizar la fila
                                return;
                            }
                        });
                    }); // Cierre del .then(get_usuarios)
                } // Cierre del if RESPONSE_CODE == 200 OK
            }).catch(err => {
                $.notifyClose();
                notify(err.message, 'danger', 5000, 'right')
            });
            e.stopImmediatePropagation();
        });
    }
    get_cliente();

    function ClassTBody() {
        $('.open-modal').removeClass('btn-outline-custom')
        $('.contentd').addClass('text-light bg-light w30')
        $('.botones').hide()
    }
    // $.fn.DataTable.ext.pager.numbers_length = 5;
    const table = $('#GetUsuarios').DataTable({
        initComplete: function (settings, json) {
        },
        drawCallback: function (settings) {

            // $(".page-link").addClass('border border-0');
            $(".dataTables_info").addClass('text-secondary');
            $(".custom-select").addClass('text-secondary');
            $('.contentd').removeClass('text-light bg-light w30')
            $('.botones').show()
            $('.table-responsive').removeClass('invisible')
            fadeInOnly('.table-responsive')
            $('.dataTables_length').addClass('d-none d-sm-block')
            setTimeout(() => {
                if ($(window).width() < 769) {
                    $('.botones').removeClass('float-right')
                } else {
                    $('.botones').addClass('float-right')
                }

            }, 100);

        },
        lengthMenu: [5, 10, 25, 50, 100],
        columnDefs: [{
            "visible": false,
            "targets": 0
        },
        {
            "visible": false,
            "targets": 1
        },
        {
            "visible": false,
            "targets": 2
        },
        {
            "visible": true,
            "targets": 3
        },
        {
            "visible": false,
            "targets": 6
        },
        {
            "visible": false,
            "targets": 8
        },
        {
            "visible": false,
            "targets": 9
        },
        {
            "visible": true,
            "targets": 10
        },
        {
            "visible": false,
            "targets": 11
        },
        {
            "visible": false,
            "targets": 15
        },
        ],
        rowGroup: {
            dataSrc: ['nombre']
        },
        bProcessing: true,
        serverSide: true,
        deferRender: true,
        iDisplayLenght: 5,
        searchDelay: 1500,
        dom: `<'row'<'col-sm-3 d-none d-sm-block'l><'col-sm-9 col-12 d-inline-flex w-100 justify-content-end'f>>` +
            `<'row'<'col-12 table-responsive invisible'tr>>` +
            `<'row'<'col-sm-5 d-none d-sm-block'i><'col-sm-7 col-12 d-none d-sm-block'p>>` +
            `<'row d-sm-none d-block'<'d-flex justify-content-center fixed-bottom col-12 bg-white'p>>`,
        ajax: {
            url: "GetUsuarios.php",
            type: "POST",
            "data": function (data) {
                // console.log(data);
                data.recid_c = $("#recid_c").val();
            },
            error: function () {
                $("#GetUsuarios_processing").hide();
            },
        },
        columns: [{
            "class": "",
            /** Col 00 */
            "data": "uid"
        },
        {
            "class": "",
            /** Col 01 */
            "data": "recid"
        },
        {
            "class": "",
            /** Col 02 */
            "data": "nombre"
        },
        {
            "class": "border-0 pb-2 text-nowrap",
            /** Col 03 */
            "data": "usuario"
        },
        {
            "class": "border-0",
            /** Col 04 */
            "data": "legajo"
        },
        {
            "class": "border-0",
            /** Col 05 */
            "data": "rol_n"
        },
        {
            "class": "",
            /** Col 06 */
            "data": "estado"
        },
        {
            "class": "border-0",
            /** Col 07 */
            "data": "estado_n"
        },
        {
            "class": "",
            /** Col 08 */
            "data": "id_cliente"
        },
        {
            "class": "",
            /** Col 09 */
            "data": "recid_cliente"
        },
        {
            "class": "border-0",
            /** Col 10 */
            "data": "cliente"
        },
        {
            "class": "",
            /** Col 11 */
            "data": "rol"
        },
        {
            "class": "border-0",
            /** Col 12 */
            "data": "last_access"
        },
        {
            "class": "border-0",
            /** Col 13 */
            "data": "fecha_alta"
        },
        {
            "class": "border-0",
            /** Col 14 */
            "data": "fecha_mod"
        },
        {
            "class": "text-nowrap",
            /** Col 15 */
            "data": "Buttons"
        },
        ],
        paging: true,
        responsive: false,
        info: true,
        searching: true,
        ordering: false,
        language: {
            "url": "../js/DataTableSpanishShort.json"
        },
    });

    // table.page.len('5').draw();
    table.on('page.dt', function () {
        CheckSesion()
        ClassTBody()
        $('#respuestaResetClave').html('')
    });
    table.on('init.dt', function () {
        $('.form-control-sm').attr('placeholder', 'Buscar nombre o rol')
        $('#GetUsuarios_filter').prepend('<button data-titlel="Alta de Usuario" class="px-2 btn btn-outline-custom add fontq border"><svg class="" width="20" height="20" fill="currentColor"><use xlink:href="../img/bootstrap-icons.svg#person-plus-fill"/></svg></button>')
        if ($(window).width() < 769) {
            $('.botones').removeClass('float-right')
        } else {
            $('.botones').addClass('float-right')
        }

    });
    if ($('#_rol').val() != '') {
        $('#GetUsuarios').DataTable().search($('#_rol').val()).draw();
    }
    if ($(window).width() < 769) {
        $('#GetUsuarios').removeClass('text-wrap')
        $('#GetUsuarios').addClass('text-nowrap')
    } else {
        $('#GetUsuarios').removeClass('text-nowrap')
        $('#GetUsuarios').addClass('text-wrap')
    }
    $(document).on("click", ".editar", function (e) {
        CheckSesion()
        $.notifyClose();
        e.preventDefault();
        $('#modalEditUser').modal('show');
        // $('#e_nombre').focus()

        let data_uid = $(this).attr('data_uid');
        let data_tarjeta = $(this).attr('data_tarjeta');
        let data_nombre = $(this).attr('data_nombre');
        let data_usuario = $(this).attr('data_usuario');
        let data_rol_n = $(this).attr('data_rol_n');
        let data_rol = $(this).attr('data_rol');
        let data_legajo = $(this).attr('data_legajo');
        let data_estado_n = $(this).attr('data_estado_n');
        let data_estado = $(this).attr('data_estado');
        let data_fecha_alta = $(this).attr('data_fecha_alta');
        let data_fecha_mod = $(this).attr('data_fecha_mod');
        let data_cliente = $(this).attr('data_cliente');
        let data_user_ad = $(this).attr('data_user_ad');

        $('#data_nombre').html(data_nombre);
        $('#e_nombre').val(data_nombre);
        $('#e_usuario').val(data_usuario);
        $('#e_legajo').val(data_legajo);
        $('#e_uid').val(data_uid);
        $('#e_tarjeta').val(data_tarjeta);

        if (data_user_ad == '1') {
            $('#e_usuario').prop('disabled', true);
        } else {
            $('#e_usuario').prop('disabled', false);
        }

        var opt2 = {
            MinLength: "0",
            SelClose: false,
            MaxInpLength: "10",
            delay: "250",
            allowClear: false
        };

        $(".selectRol").select2({
            multiple: false,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: $('#modalEditUser'),
            placeholder: "Rol",
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
                url: "getRol.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q: params.term,
                        recid_c: $("#recid_c").val(),
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        });

        var newOption = new Option(data_rol_n, data_rol, false, true);
        $('.selectRol').append(newOption).trigger('change');

        ActiveBTN(false, '#submitEdit', 'Guardando', 'Guardar')

        $("#FormEdit").bind("submit", function (e) {
            CheckSesion()
            e.preventDefault();
            let dataForm = $(this).serialize();
            dataForm += '&e_usuario=' + $('#e_usuario').val();
            console.log(dataForm);

            $.ajax({
                type: $(this).attr("method"),
                url: $(this).attr("action"),
                data: dataForm,
                // async : false,
                beforeSend: function (data) {
                    ActiveBTN(true, '#submitEdit', 'Guardando', 'Guardar')
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        ActiveBTN(false, '#submitEdit', 'Guardando', 'Guardar')
                        $.notifyClose();
                        notify(data.Mensaje, 'success', 5000, 'right')
                        $('#modalEditUser').modal('hide')
                        $('#GetUsuarios').DataTable().ajax.reload()
                    } else {
                        ActiveBTN(false, '#submitEdit', 'Guardando', 'Guardar')
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                    }
                }
            });
            e.stopImmediatePropagation();
        });
    });

    $('#modalEditUser').on('hidden.bs.modal', function () {
        ActiveBTN(false, '#submitEdit', 'Guardando', 'Guardar')
        $('#data_nombre').html('');
        $('#e_nombre').val('');
        $('#e_usuario').val('');
        $('#e_legajo').val('');
        $('#e_uid').val('');
        $('#respuestaForm').html('')
    });

    $(document).on("click", ".add", function (e) {
        CheckSesion()
        $.notifyClose();
        e.preventDefault();
        $('#modalAddUser').modal('show');
        $('#a_nombre').focus()
        $('#a_recid').val($("#recid_c").val())

        var opt2 = {
            MinLength: "0",
            SelClose: false,
            MaxInpLength: "10",
            delay: "250",
            allowClear: false
        };

        $(".selectRol").select2({
            multiple: false,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: $('#modalAddUser'),
            placeholder: "Rol",
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
                url: "getRol.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q: params.term,
                        recid_c: $("#recid_c").val(),
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        });

        ActiveBTN(false, '#submitAdd', 'Guardando', 'Agregar')

        $("#FormAdd").bind("submit", function (e) {
            CheckSesion()
            e.preventDefault();
            $.ajax({
                type: $(this).attr("method"),
                url: $(this).attr("action"),
                data: $(this).serialize(),
                // async : false,
                beforeSend: function (data) {
                    ActiveBTN(true, '#submitAdd', 'Guardando', 'Agregar')
                    $.notifyClose();
                    notify('Aguarde..', 'info', 0, 'right')
                },
                success: function (data) {
                    if (data.status == "ok") {
                        ActiveBTN(false, '#submitAdd', 'Guardando', 'Agregar')
                        $('#GetUsuarios').DataTable().ajax.reload()
                        $.notifyClose();
                        notify(data.Mensaje, 'success', 5000, 'right')
                        $('#modalAddUser').modal('hide')
                    } else {
                        ActiveBTN(false, '#submitAdd', 'Guardando', 'Agregar')
                        $.notifyClose();
                        notify(data.Mensaje, 'danger', 5000, 'right')
                    }
                },
                error: function (data) {
                    ActiveBTN(false, '#submitAdd', 'Guardando', 'Agregar')
                    $.notifyClose();
                    notify('Error', 'danger', 5000, 'right')
                }
            });
            e.stopImmediatePropagation();
        });

    });

    $(document).on("click", "#GetUsuarios .ListaUsuario", function (e) {
        CheckSesion()
        e.preventDefault();
        let c = $(this).attr('data-c');
        let nombre = $(this).attr('data_nombre');
        let usuario = $(this).attr('data_usuario');
        let rol_n = $(this).attr('data_rol_n');
        let uid = $(this).attr('data-uid');
        let url = 'listas_estruct/index.php?uid=' + uid + '&_c=' + c + '&nombre=' + nombre + '&usuario=' + usuario + '&rol_n=' + rol_n;
        $.get(url).done(function (data) {
            let urltabs = 'listas_estruct/tabs.php?v=' + vjs();
            $.get(urltabs).done(function (data) {
                $('#nav-tabContent').html(data);
                $('#copyListas .nombreUsuario').html(nombre)
            });
            $('#modalListas .modal-body').html(data);
            $('#modalListas').modal('show');

        });
        e.stopImmediatePropagation();
    });


    $('#modalAddUser').on('hidden.bs.modal', function () {
        ActiveBTN(false, '#submitAdd', 'Guardando', 'Agregar')
        $('#a_nombre').val('')
        $('#a_usuario').val('')
        $('#a_legajo').val('')
        $(".selectRol").val(null).trigger('change')
        $('#respuestaFormAdd').html('')
    });

    $(document).on('click', '.resetKey', function (e) {
        $.notifyClose();
        e.preventDefault();
        var data_uid = $(this).attr('data_uid');
        var data_nombre = $(this).attr('data_nombre');
        var data_usuario = $(this).attr('data_usuario');
        $('.resetKey').unbind('click');

        bootbox.confirm({
            // title: "<span class='fonth'>Eliminar Usuario</span>",
            message: '<span class="fonth fw4">¿Desea restablecer la contraseña de <span class="fw5">' + data_nombre + '</span>?<br />Su nueva contraseña será: ' + data_usuario + '</span>',
            buttons: {
                confirm: {
                    label: 'Confirmar',
                    className: 'btn-custom btn-sm fontq btn-mobile'
                },
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-outline-custom border btn-sm fontq btn-mobile'
                }
            },
            callback: function (result) {
                $('.resetKey').unbind('click');
                if (result) {
                    $.ajax({
                        type: "POST",
                        url: "crud.php",
                        'data': {
                            submit: 'key',
                            uid: data_uid,
                            nombre: data_nombre,
                            usuario: data_usuario
                        },
                        beforeSend: function (data) {
                            $('#reset_' + data_uid).prop('disabled', true)
                            $.notifyClose();
                            notify('Aguarde..', 'info', 0, 'right')
                        },
                        success: function (data) {
                            if (data.status == "ok") {
                                $('#reset_' + data_uid).prop('disabled', false)
                                $.notifyClose();
                                notify(data.Mensaje, 'success', 10000, 'right')
                            } else {
                                $('#reset_' + data_uid).prop('disabled', false)
                                $.notifyClose();
                                notify(data.Mensaje, 'danger', 5000, 'right')
                            }
                        },
                        error: function (data) {
                            $('#reset_' + data_uid).prop('disabled', false)
                            $.notifyClose();
                            notify('Error', 'danger', 5000, 'right')
                        }
                    });

                }
            }
        });
        e.stopImmediatePropagation();
    });

    $(document).on('click', '.estado', function (e) {
        $.notifyClose();
        e.preventDefault();
        var data_uid = $(this).attr('data_uid');
        var data_nombre = $(this).attr('data_nombre');
        var data_estado = $(this).attr('data_estado');
        var data_title = $(this).attr('data_title');
        $('.estado').unbind('click');

        bootbox.confirm({
            // title: "<span class='fonth'>Eliminar Usuario</span>",
            message: '<span class="fonth fw4">¿Confirma dar de ' + data_title + ' a <span class="fw5">' + data_nombre + '</span>?</span>',
            buttons: {
                confirm: {
                    label: 'Confirmar',
                    className: 'btn-custom btn-sm fontq btn-mobile'
                },
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-outline-custom border btn-sm fontq btn-mobile'
                }
            },
            callback: function (result) {
                $('.estado').unbind('click');
                if (result) {
                    $.ajax({
                        type: "POST",
                        url: "crud.php",
                        'data': {
                            submit: 'estado',
                            uid: data_uid,
                            nombre: data_nombre,
                            estado: data_estado
                        },
                        beforeSend: function (data) {
                            $('#estado_' + data_uid).prop('disabled', true)
                            $.notifyClose();
                            notify('Aguarde', 'info', 0, 'right')
                        },
                        success: function (data) {
                            if (data.status == "ok") {
                                $('#estado_' + data_uid).prop('disabled', false)
                                $.notifyClose();
                                notify(data.Mensaje, 'success', 5000, 'right')
                                $('#GetUsuarios').DataTable().ajax.reload()
                            } else {
                                $('#estado_' + data_uid).prop('disabled', false)
                                $.notifyClose();
                                notify(data.Mensaje, 'danger', 5000, 'right')
                            }
                        },
                        error: function (data) {
                            $('#estado_' + data_uid).prop('disabled', false)
                            $.notifyClose();
                            notify('Error', 'success', 5000, 'right')
                        }
                    });
                }
            }
        });

        e.stopImmediatePropagation();
    });

    $(document).on('click', '.delete', function (e) {
        $.notifyClose();
        e.preventDefault();
        var data_uid = $(this).attr('data_uid');
        var data_nombre = $(this).attr('data_nombre');
        $('.delete').unbind('click');

        bootbox.confirm({
            // title: "<span class='fonth'>Eliminar Usuario</span>",
            message: '<span class="fonth fw4">¿Confirma eliminar el usuario/a: <span class="fw5">' + data_nombre + '</span>?</span>',
            buttons: {
                confirm: {
                    label: 'Confirmar',
                    className: 'btn-danger btn-sm fontq btn-mobile'
                },
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-light btn-sm fontq btn-mobile'
                }
            },
            callback: function (result) {
                $('.delete').unbind('click');
                if (result) {
                    $.ajax({
                        type: "POST",
                        url: "crud.php",
                        'data': {
                            submit: 'delete',
                            uid: data_uid,
                            nombre: data_nombre,
                        },
                        beforeSend: function (data) {
                            $('#delete_' + data_uid).prop('disabled', true)
                            $.notifyClose();
                            notify('Aguarde', 'info', 0, 'right')
                        },
                        success: function (data) {
                            if (data.status == "ok") {
                                $('#delete_' + data_uid).prop('disabled', false)
                                $.notifyClose();
                                notify(data.Mensaje, 'success', 5000, 'right')
                                $('#GetUsuarios').DataTable().ajax.reload()
                            } else {
                                $('#delete_' + data_uid).prop('disabled', false)
                                $.notifyClose();
                                notify(data.Mensaje, 'danger', 5000, 'right')
                            }
                        },
                        error: function (data) {
                            $('#delete_' + data_uid).prop('disabled', false)
                            $.notifyClose();
                            notify('Error', 'danger', 5000, 'right')
                        }
                    });
                }
            }
        });
        $('.bootbox-confirm .modal-body').addClass('confirmDelete');
        e.stopImmediatePropagation();
    });
    $('#modalListas').on('hidden.bs.modal', function (e) {
        $('#modalListas .modal-body').html('');
        $.notifyClose();
    })
});