
document.addEventListener('DOMContentLoaded', function () {
    const homehost = $("#_homehost").val();
    const LS_ID_CLIENTE = `${homehost}_id_cliente`;

    const dt_clientes = async (selectorTable) => {
        $(`${selectorTable}`).addClass('loader-in')

        axios.get('../../app-data/_local/clientes').then(res => {
            const data = res.data;
            if (!data) return;

            if ($.fn.DataTable.isDataTable(selectorTable)) {
                $(selectorTable).DataTable().clear().destroy();
            }

            $(selectorTable).DataTable({
                dom: `
                <'row'
                    <'col-12 d-inline-flex justify-content-between align-items-center'lf>
                >
                <'row' <'col-12'<'border table-responsive't>>>
                <'row'
                    <'col-12 d-inline-flex justify-content-between align-items-center mt-2'ip>
                >
                `,
                "data": data,
                columns: [
                    {
                        data: 'fecha', className: '', targets: '', title: '',
                        "render": function (data, type, row, meta) {
                            return data
                        }, visible: false
                    },
                    {
                        data: 'id', className: 'text-center', targets: '', title: 'ID',
                        "render": function (data, type, row, meta) {
                            return `<div class="text-truncate text-monospace" title="${data}" style="max-width: 30px; min-width: 30px;">${data}</div>`
                        }, orderable: false
                    },
                    {
                        data: 'nombre', className: '', targets: '', title: 'Cuenta',
                        "render": function (data, type, row, meta) {
                            return `<div class="text-truncate" title="${data}" style="max-width: 120px; min-width: 120px;">${data}</div>`
                        }, orderable: false
                    },
                    {
                        data: 'ident', className: '', targets: '', title: 'Ident',
                        "render": function (data, type, row, meta) {
                            return data
                        }, orderable: false
                    },
                    {
                        data: 'db', className: '', targets: '', title: 'DB CH',
                        "render": function (data, type, row, meta) {
                            return `<span class="hint--top" aria-label="Probar Conexión">
                                <div class="pointer text-truncate" title="${data}" style="max-width: 150px; min-width: 150px;" >${data}</div>
                            </span>`

                        }, orderable: false
                    },
                    {
                        data: '', className: 'w-100 text-right', targets: '', title: 'Acciones',
                        "render": function (data, type, row, meta) {
                            if (!type === 'display') return '';
                            // usuarios/?_c=' . $recid . '&alta"
                            const btn = (bi, count, title, classWidth, href) => {
                                return `
                                    <span class="hint--left" aria-label="${title}">
                                        <a ${href ? `href="${href}"` : ''} class="btn border btn-sm btn-outline-custom text-monospace ${classWidth} btn-usuarios">
                                            <i class="bi bi-${bi}"></i>${count}
                                        </a>
                                    </span>`;
                            }
                            const btnUsuarios = btn('people-fill mr-1', row.count_usuarios, 'Ir a Usuarios', 'w80', `../?_c=${row.recid}&alta`);
                            const btnRoles = btn('sliders mr-1', row.count_roles, 'Ir a Roles', 'w80', `../roles?_c=${row.recid}&alta`);
                            const btnEditar = btn('pencil', '', `Editar ${row.nombre}`, 'editCuenta', '');
                            const btnTest = btn('database-check', '', `Probar Conexión`, '', '');
                            return `<div class="d-flex justify-content-end align-items-center" style="gap: 5px;">${btnUsuarios} ${btnRoles} <test>${btnTest}</test><editar>${btnEditar}</editar></div>`
                        }, orderable: false
                    }
                ],
                // ordenar por fecha
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
                    "sInfo": "Pag. _START_ a _END_ de _TOTAL_ Clientes",
                    "sInfoEmpty": "No se encontraron resultados",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ Clientes)",
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

            const dataTablesLength = document?.querySelector(`.dataTables_length select`);
            create_btn_add_cuenta(dataTablesLength); // crear botón de agregar cuenta 

            const inputSearch = document?.querySelector(`${selectorTable}_filter input`);
            inputSearch.attributes.placeholder.value = 'Buscar ... ';
            inputSearch.focus();

            click_row(selectorTable)
            $(`${selectorTable}`).removeClass('loader-in')

            add_cuenta();

        }).catch(err => {
            console.log(err);
        });
    }
    dt_clientes("#tableClientes")

    const click_row = (selectorTable) => {
        const tableBody = document?.querySelector(`${selectorTable} tbody`);
        tableBody?.addEventListener('click', (e) => {
            e.stopImmediatePropagation();
            const target = e.target;

            const row = target.closest('tr');
            if (!row) return; // Salir si no es una fila

            let data = $(selectorTable).DataTable().row(row).data();
            if (!data) return;

            const closestElement = target.closest('EDITAR, TEST');
            switch (closestElement?.tagName) {
                case 'EDITAR':
                    edit_cliente(data);
                    break;
                case 'TEST':
                    test_conect(data);
                    break;
            }
        });
    }
    const edit_cliente = (data) => {
        ls.set(LS_ID_CLIENTE, data.id ?? '');
        if (!data) return;

        const submitAdd = document.getElementById('submitAdd');
        const submitEdit = document.getElementById('submitEdit');
        submitEdit.style.display = 'block';
        submitAdd.style.display = 'none';
        submitEdit.addEventListener('click', (e) => {
            e.preventDefault();
            put_cliente()
            e.stopImmediatePropagation();
        });
        $('#divTokenAPI').show();
        $('#modalFormCuenta').modal('show')
        $('#modalFormCuenta input').attr('autocomplete', 'on')
        $('#submitFormCuenta').val('EditCuenta')

        $('#nombreCuenta').html('Editar Cuenta: ' + data.nombre)
        $('#nombre').val(data.nombre)
        $('#ident').val(data.ident).prop('readonly', true)
        $('#recid').val(data.recid)
        $('#host').val(data.host)
        $('#db').val(data.db)
        $('#user').val(data.user)
        $('#pass').val(data.pass)
        $('#hostCHWeb').val(data.hostLocal)
        $('#AppCode').val(data.recid)
        $('#token_api').text(data.token_api ?? '');
        $('#serverAD').val(data.serverAD ?? '');
        $('#puertoAD').val(data.puertoAD ?? '');
        $('#domainAD').val(data.domainAD ?? '');
        $('#baseDNAD').val(data.baseDNAD ?? '');
        $('#serviceUserAD').val(data.serviceUserAD ?? '');
        $('#servicePassAD').val(data.servicePassAD ?? '');

        // Marcar estado de Active Directory
        const activeAD = data.activeAD ?? '0';
        if (activeAD === '1') {
            $('#activeADSI').prop('checked', true);
            $('#activeADNO').prop('checked', false);
            $('#activeADSI').parent().addClass('active');
            $('#activeADNO').parent().removeClass('active');
        } else {
            $('#activeADSI').prop('checked', false);
            $('#activeADNO').prop('checked', true);
            $('#activeADSI').parent().removeClass('active');
            $('#activeADNO').parent().addClass('active');
        }

        $('#copy_token').off('click').on('click', function () {
            const token = $('#token_api').text();
            if (!token) return;
            navigator.clipboard.writeText(token).then(() => {
                notify('Token copiado al portapapeles', 'success', 5000, 'right');
            }).catch(err => {
                notify('Error al copiar el token', 'danger', 5000, 'right');
            });
        });

        if ((data.auth == '1')) {
            $('#auth').prop('checked', true)
        } else {
            $('#auth').prop('checked', false)
        }
        if (data.localCH == '1') {
            $('#labelInactivo').addClass('active')
            $('#labelActivo').removeClass('active')
            $('#localCHSI').prop('checked', false)
            $('#localCHNO').prop('checked', true)
        } else {
            $('#labelActivo').addClass('active')
            $('#labelInactivo').removeClass('active')
            $('#localCHNO').prop('checked', false)
            $('#localCHSI').prop('checked', true)
        }
        $('#WebService').val(data.WebService);
        $('#ApiMobileHRP').val(data.ApiMobileHRP);
        $('#ApiMobileHRPApp').val(data.UrlAppMobile);
        // on change activeAD trigger change
        $('#activeAD').off('change').on('change', function () {
            $(this).trigger('change');
        });
    }
    const put_cliente = () => {

        const ID = ls.get(LS_ID_CLIENTE); // id del cliente

        $('#submitEdit').prop('disabled', true)
        notifyWait('Aguarde...')

        axios.put('../../app-data/_local/clientes/' + ID, {
            'Nombre': $('#nombre').val(),
            'Ident': $('#ident').val(),
            'Host': $('#hostCHWeb').val(),
            'DBHost': $('#host').val(),
            'DBName': $('#db').val(),
            'DBUser': $('#user').val(),
            'DBPass': $('#pass').val(),
            'DBAuth': $('#auth').is(':checked') ? '1' : '0',
            'WebService': $('#WebService').val(),
            'ApiMobile': $('#ApiMobileHRP').val(),
            'ApiMobileApp': $('#ApiMobileHRPApp').val(),
            'AppCode': $('#AppCode').val(),
            'LocalCH': $('#localCHSI').is(':checked') ? '0' : '1',
            'activeAD': $('input[name="activeAD"]:checked').val() ?? '0',
            'serverAD': $('#serverAD').val(),
            'puertoAD': $('#puertoAD').val(),
            'domainAD': $('#domainAD').val(),
            'baseDNAD': $('#baseDNAD').val(),
            'serviceUserAD': $('#serviceUserAD').val(),
            'servicePassAD': $('#servicePassAD').val(),
        }).then(res => {
            $.notifyClose();
            if ((res.data.MESSAGE ?? '') == 'OK') {
                notify('Cuenta actualizada correctamente', 'success', 5000, 'right')
                dt_clientes("#tableClientes")
                $('#modalFormCuenta').modal('hide')
            } else {
                throw new Error(res.data.MESSAGE)
            }
            $('#submitEdit').prop('disabled', false)
        }).catch(err => {
            $('#submitEdit').prop('disabled', false)
            notify(err.message, 'danger', 5000, 'right')
        }).finally(() => {
        })
    }
    const create_btn_add_cuenta = (selector) => {
        if (!selector) return;
        const btnAdd = document.createElement('button');
        btnAdd.className = 'ml-2 px-3 btn btn-custom addCuenta font08 h35 hint--right hint--no-shadow';
        btnAdd.id = 'addCuenta';
        btnAdd.setAttribute('aria-label', 'Crear Cuenta');
        btnAdd.innerHTML = `<i class="bi bi-plus-lg"></i>`;
        selector?.parentElement.append(btnAdd);
    }
    const post_cliente = () => {

        $('#submitAdd').prop('disabled', true)
        notifyWait('Aguarde...')

        axios.post('../../app-data/_local/clientes/', {
            'Nombre': $('#nombre').val(),
            'Ident': $('#ident').val(),
            'Host': $('#hostCHWeb').val(),
            'DBHost': $('#host').val(),
            'DBName': $('#db').val(),
            'DBUser': $('#user').val(),
            'DBPass': $('#pass').val(),
            'DBAuth': $('#auth').is(':checked') ? '1' : '0',
            'WebService': $('#WebService').val(),
            'ApiMobile': $('#ApiMobileHRP').val(),
            'ApiMobileApp': $('#ApiMobileHRPApp').val(),
            'AppCode': $('#AppCode').val(),
            'LocalCH': $('#localCHSI').is(':checked') ? '0' : '1',
        }).then(res => {
            $.notifyClose();
            if ((res.data.MESSAGE ?? '') == 'OK') {
                notify('Cuenta creada correctamente', 'success', 5000, 'right')
                dt_clientes("#tableClientes")
                $('#modalFormCuenta').modal('hide')
            } else {
                throw new Error(res.data.MESSAGE)
            }
            $('#submitAdd').prop('disabled', false)
        }).catch(err => {
            $('#submitAdd').prop('disabled', false)
            notify(err.message, 'danger', 5000, 'right')
        }).finally(() => {
        })
    }
    const add_cuenta = () => {
        const addCuenta = document?.querySelector(`.addCuenta`);
        addCuenta?.addEventListener('click', (e) => {
            $('#divTokenAPI').hide();
            $('#modalFormCuenta').modal('show');
            $('#modalFormCuenta input').attr('autocomplete', 'on');
            $(`#labelInactivo`).button('toggle')
            const submitEdit = document?.querySelector(`#submitEdit`);
            submitEdit.style.display = 'none';
            const submitAdd = document?.querySelector(`#submitAdd`);
            submitAdd.style.display = 'block';
            submitAdd?.addEventListener('click', (e) => {
                e.stopImmediatePropagation();
                post_cliente()
            });
        });
    }
    $('#modalFormCuenta').on('hidden.bs.modal', function (e) {
        document.getElementById('FormCuenta').reset();
        $('#nombreCuenta').html('Nueva Cuenta')
        $('#submitFormCuenta').val('AltaCuenta')
    })
    const test_ad_connection = (selector, withUser = false) => {
        $(selector).off('click').on('click', function (e) {
            e.preventDefault();
            notifyWait('Probando conexión AD...');

            if (withUser) {
                // validar que los campos serviceUserAD y servicePassAD no estén vacíos
                if (!$('#serviceUserAD').val() || !$('#servicePassAD').val()) {
                    $.notifyClose();
                    notify('Debe ingresar el usuario y la contraseña del servicio AD', 'danger', 5000, 'right');
                    return;
                }
            }

            axios.post('../../app-data/_local/test_ad', {
                'serverAD': $('#serverAD').val(),
                'baseDNAD': $('#baseDNAD').val(),
                'domainAD': $('#domainAD').val(),
                'puertoAD': $('#puertoAD').val(),
                'serviceUserAD': withUser ? $('#serviceUserAD').val() : '',
                'servicePassAD': withUser ? $('#servicePassAD').val() : ''
            }).then(res => {
                $.notifyClose();
                const message = res.data?.MESSAGE ?? 'Error desconocido';
                if ((res.data?.RESPONSE_CODE ?? '') == '200 OK') {
                    if (withUser) {
                        const info = res.data?.DATA?.server_info ?? [];
                        const defaultnamingcontext = info?.defaultnamingcontext ?? null;
                        const dns_hostname = info?.dns_hostname ?? null;
                        const usuarios = info?.usuarios ?? [];

                        let html = `
                            <div class="d-flex flex-column">
                            <div>Conexión exitosa con credenciales</div>
                        `;
                        html += defaultnamingcontext ? `<div class="">Base DN: ${defaultnamingcontext}</div>` : '';
                        html += dns_hostname ? `<div class="">Domain: ${dns_hostname}</div>` : '';
                        html += `</div>`;
                        html += `<div class="mt-2 font08">Usuarios de AD: (${usuarios.length ?? 0})</div>`;
                        if (usuarios.length > 0) {
                            html += `<ul class="my-2" style="max-height: 200px; overflow-y: auto;">`;
                            usuarios.forEach(u => {
                                const email = u['email'] ?? '';
                                html += `<li class="font08">${u['username'] ?? ''} <br> <span class="font07">Nombre: ${u['nombre'] ?? ''} ${email ? ` <br>Email: ${email}` : ''}</span></li>`;
                            });
                            html += `</ul>`;
                        } else {
                            html += `<div>No se encontraron usuarios.</div>`;
                        }
                        notify(html, 'success', 0, 'right')
                        return;
                    }
                    notify(message, 'success', 5000, 'right')
                } else {
                    throw new Error(message)
                }
            }).catch(err => {
                $.notifyClose();
                notify(err.message, 'danger', 5000, 'right')
            });
            e.stopImmediatePropagation();
        });
    }
    test_ad_connection('#testADConnection');
    test_ad_connection('#testUserADConnection', true);
});