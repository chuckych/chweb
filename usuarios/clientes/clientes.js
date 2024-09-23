
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
        $('#WebService').val(data.WebService)
        $('#ApiMobileHRP').val(data.ApiMobileHRP)
        $('#ApiMobileHRPApp').val(data.UrlAppMobile)
    }
    const test_conect = async (data) => {
        notifyWait('Aguarde...')
        axios.post('../../app-data/test_connect', {
            'DBHost': data.host,
            'DBName': data.db,
            'DBUser': data.user,
            'DBPass': data.pass
        }).then(res => {

            if (res.data.RESPONSE_CODE == '200 OK') {
                $.notifyClose();
                const data = res.data.DATA;
                const html = `
                    <div class="fw5">Conexión exitosa</div>
                    <div class="">${data.VersionStr ?? ''}</div>
                    <div class="py-2"></div>
                    <div class="">Database: ${data.SQLServerName.CurrentDatabase ?? ''}</div>
                    <div class="">Server Name: ${data.SQLServerName.SQLServerName ?? ''}</div>
                    <div class="">Server Version: ${data.SQLServerName.SQLServerVersion ?? ''}</div>
                `
                notify(html, 'success', 5000, 'right')
            } else {
                throw new Error(res.data.MESSAGE)
            }

        }).catch(err => {
            $.notifyClose();
            notify(err.message, 'danger', 5000, 'right')
        })
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
});