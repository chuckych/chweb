class datePicker {
    constructor(selector) {
        this.selector = selector;
        this.init();
        this.countDays = document.querySelector('.count_days') ?? null;
    }
    init() {
        $(this.selector).daterangepicker({
            singleDatePicker: false,
            showDropdowns: false,
            showWeekNumbers: false,
            autoUpdateInput: true,
            opens: "left",
            autoApply: true,
            linkedCalendars: false,
            locale: {
                format: "DD/MM/YYYY",
                separator: " al ",
                applyLabel: "Aplicar",
                cancelLabel: "Cancelar",
                fromLabel: "Desde",
                toLabel: "Para",
                customRangeLabel: "Personalizado",
                weekLabel: "Sem",
                firstDay: 1,
                alwaysShowCalendars: true,
                applyButtonClasses: "text-white bg-custom",
            },
        });
        // on apply date
        $(this.selector).on('apply.daterangepicker', (ev, picker) => {
            const start = picker.startDate.format('DD/MM/YYYY');
            const end = picker.endDate.format('DD/MM/YYYY');
            this.countDays.textContent = `${countDiffDays(start, end)} Días`;
        });
    }
    set_date(start, end) {
        $(this.selector).data('daterangepicker').setStartDate(start);
        $(this.selector).data('daterangepicker').setEndDate(end);
        this.countDays.textContent = `${countDiffDays(start, end)} Días`;
    }
}
const LS_PARAM_LIQUID = 'chweb_param_liquid'
const LS_VALOR_LIQUID = 'chweb_valor_liquid'
const LS_FECHAS = 'chweb_fechas'
const LS_VALORES = 'chweb_valores'
const LS_TIPO_HORA = 'chweb_tipo_hora'
const LS_NOVEDADES = 'chweb_novedades'
const LS_VALOR_FECHA = 'chweb_valor_fecha'
const FLAG = Date.now();
const DT_GRILLA = 'dt_grilla';
const DT_TIPO_HORA = 'dt_tipo_hora';
const DT_NOVEDADES = 'dt_novedades';
const COLUMNAS_MARCADAS = 'columnas_marcadas';
const SORTED_COLUMNAS_MARCADAS = 'sorted_columnas';
ls.set(COLUMNAS_MARCADAS, []);

ls.set(LS_PARAM_LIQUID, {});
ls.set(LS_VALOR_LIQUID, {});
ls.set(LS_FECHAS, {});
const DIR_APP_DATA = '../../../app-data';

const getLiquid = async () => {
    try {
        const { data } = await axios.get(DIR_APP_DATA + '/parametros/liquid');
        await ls.set(LS_PARAM_LIQUID, data ?? {});
        return data ?? {};
    } catch (error) {
        console.error(error);
        return {};
    }
}
const getFechas = async () => {
    try {
        const { data } = await axios.get(DIR_APP_DATA + '/fichas/dates');
        await ls.set(LS_FECHAS, data ?? {});
        return data ?? {};
    } catch (error) {
        console.error(error);
        return {};
    }
}
const noveHorasData = async () => {
    try {
        const { data } = await axios.get(DIR_APP_DATA + '/nove-horas/data');
        await ls.set(LS_NOVEDADES, data.novedades ?? {});
        await ls.set(LS_TIPO_HORA, data.horas ?? {});
        await ls.set(COLUMNAS_MARCADAS, data.columnas ?? []);
        return data ?? {};
    } catch (error) {
        console.error(error);
        return {};
    }
}
getLiquid().then(data => {
    if (!data) return;
    const keys = Object.keys(data);
    keys.forEach(el => {
        const value = data[el] ?? '';
        const element = document.querySelector(`.${el}`);
        const element2 = document.querySelector(`#${el}`);
        if (element) element.textContent = value;
        if (element2) element2.value = value;
    });
});
const mesString = {
    "1": "Enero",
    "2": "Febrero",
    "3": "Marzo",
    "4": "Abril",
    "5": "Mayo",
    "6": "Junio",
    "7": "Julio",
    "8": "Agosto",
    "9": "Septiembre",
    "10": "Octubre",
    "11": "Noviembre",
    "12": "Diciembre"
};
const mesStringShort = {
    "1": "01 Ene",
    "2": "02 Feb",
    "3": "03 Mar",
    "4": "04 Abr",
    "5": "05 May",
    "6": "06 Jun",
    "7": "07 Jul",
    "8": "08 Ago",
    "9": "09 Sep",
    "10": "10 Oct",
    "11": "11 Nov",
    "12": "12 Dic"
};
const JornalString = {
    "1": "Primer",
    "2": "Segundo"
};
const tipoPerString = {
    "0": "Mensual",
    "1": "Jornal"
};
getFechas().then(data => {
    noveHorasData();
    const wrapper = document.querySelector('.wrapper');

    const years = data.años ?? {};
    if (!Object.keys(years).length) {
        notify('No hay fechas disponible.', 'danger', 0, 'right');
        return;
    };

    setTimeout(() => {
        wrapper.hidden = false;
        wrapper.classList.add('fadeIn');
    }, 200);

    const currentYear = new Date().getFullYear(); // año actual
    Object.keys(years).forEach(el => { // recorrer años
        if (parseInt(el) > currentYear) { // si el año es mayor al actual
            delete years[el]; // eliminar el año del objeto
        }
    });

    const selectYear = document.querySelector('.selectjs_year');
    const selectMonth = document.querySelector('.selectjs_month');
    const reporte = document.querySelector('.selectjs_reporte');

    const yearsKeys = Object.keys(years) ?? [];
    yearsKeys.sort((a, b) => b - a);
    let dataYear = [];
    yearsKeys && yearsKeys.forEach(el => {
        dataYear.push({ id: el, text: el });
    });

    const datePickerInstance = new datePicker('.date-picker');

    // const months = years[selectYear.value] ?? [];
    const months = years[yearsKeys[0]] ?? [];

    months.sort((a, b) => b - a);

    renderButton(yearsKeys, 'year', '.div_años', datePickerInstance, 'Año');
    renderButton(months, 'month', '.div_meses', datePickerInstance, 'Mes');
    renderButton([1, 2], 'jornal', '.div_jornales', datePickerInstance, 'Jornal');
    renderButton([0, 1], 'tipo', '.div_tipo', datePickerInstance, 'Tipo');

    let dataMonth = [];
    months && months.forEach(el => {
        // renderOptionSelect(selectMonth, el, mesString[el]);
        dataMonth.push({ id: el, text: mesString[el] });
    });

    applySelect2('.selectjs_year', 'Seleccionar Año', -1, dataYear);
    applySelect2('.selectjs_month', 'Seleccionar Mes', -1, dataMonth);
    applySelect2('.selectjs_jornal', 'Seleccionar Jornal', -1);
    applySelect2('.selectjs_tipo', 'Seleccionar tipo', -1);
    applySelect2('.selectjs_reporte', 'Seleccionar reporte', -1);


    $('.selectjs_tipo').on('change', function (e) { // al seleccionar el tipo
        setPicker(datePickerInstance);
    });

    $('.selectjs_year').on('change', function (e) { // al seleccionar el año
        // const value = e.params.data.id; // valor seleccionado
        const value = e.target.value; // valor seleccionado
        const months = years[value] ?? []; // meses del año seleccionado
        months.sort((a, b) => b - a); // ordenar meses
        selectMonth.innerHTML = ''; // limpiar select de meses
        let dataMonth = [];

        months && months.forEach(el => {
            dataMonth.push({ id: el, text: mesString[el] });
            // renderOptionSelect(selectMonth, el, mesString[el]);
        });

        applySelect2('.selectjs_month', 'Seleccionar Mes', -1, dataMonth);

        renderButton(months, 'month', '.div_meses', datePickerInstance, 'Mes');
        setPicker(datePickerInstance);
    });

    $('.selectjs_month').on('select2:select', function (e) {
        setPicker(datePickerInstance);
    });

    $('.selectjs_jornal').on('select2:select', function (e) {
        setPicker(datePickerInstance);
    });

    $('.selectjs_reporte').on('select2:select', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        const value = e.params.data.id;
        // const dataHoras = ls.get(LS_TIPO_HORA) ?? [];
        const dataNovedades = ls.get(LS_NOVEDADES) ?? [];
        const btnConfigAct = document.querySelector('#config-actividad');
        const div_table_tipo_hora = document.querySelector('#div_table_tipo_hora') ?? null;

        if (value == 2) {

            btnConfigAct.hidden = false;

            btnConfigAct.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopImmediatePropagation();

                btnConfigAct.classList.add('loader-in');
                if (div_table_tipo_hora) div_table_tipo_hora.classList.add('loader-in');

                noveHorasData().then(data => {

                    const dataHoras = data.horas ?? [];

                    btnConfigAct.classList.remove('loader-in');
                    if (div_table_tipo_hora) div_table_tipo_hora.classList.remove('loader-in');

                    const textTHoDesc = (THoDesc, THoDesc2 = '') => `
                        <div class="d-flex flex-column">
                            <div>${THoDesc}</div>
                            <div class="text-secondary font07">${THoDesc2}</div>
                        </div>
                    `;

                    const colCheckBox = (row) => {
                        const id = row.THoCodi !== '' ? row.THoCodi : slugify(row.THoDesc);
                        return `
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" value="${id}" id="THoCodi-${id}">
                        <label class="custom-control-label" for="THoCodi-${id}"></label>
                    </div>
                `;
                    };
                    const colCustom = (row) => {
                        const btn = `<div class="hint--top hint--rounded hint--default hint--no-shadow" aria-label="Configurar Novedad">
                                    <button
                                        class="btn-nov btn btn-sm btn-outline-secondary border" type="button">
                                        <i class="bi bi-list"></i>
                                    </button>
                                </div>`;
                        return btn;
                    };
                    const columnHoras = [
                        // { title: '#', render: (data, type, row, meta) => meta.row + 1 },
                        { data: 'THoCodi', className: 'THoCodi', title: '', render: (data, type, row, meta) => data },
                        { data: 'THoCodi', className: '', title: 'Tipo', render: (data, type, row, meta) => data < 3000 ? 'Hora' : 'Custom' },
                        { data: 'THoDesc', title: 'Descripción', className: 'w-100', render: (data, type, row, meta) => textTHoDesc(data, row.THoDesc2) },
                        { data: 'THoCodi', title: '', render: (data, type, row, meta) => data < 3000 ? '' : colCustom() },
                        { data: 'THoCodi', className: 'w-100', render: (data, type, row, meta) => colCheckBox(row) },
                    ];
                    const titleConceptos = `
                        <div class="mb-2 font-weight-bolder font09 text-secondary">
                            Conceptos
                        </div>
                    `;

                    const opt = {
                        paging: false,
                        search: false,
                        classTable: 'max-h-500 overflow-auto pr-1'
                    }
                    dt_grilla(`${DT_TIPO_HORA}`, dataHoras, columnHoras, '#div_table_tipo_hora', titleConceptos, opt).then(() => {
                        marcarColumnas(`#${DT_TIPO_HORA}`); // marcar las columnas seleccionadas
                        guardarColumna(`#${DT_TIPO_HORA}`); // guardar las columnas seleccionadas
                        configNovedad(`#${DT_TIPO_HORA}`); // configurar las novedades
                        sortedCols(`#${DT_TIPO_HORA}`); // ordenar las columnas seleccionadas
                    });
                });
            });
        } else {
            btnConfigAct.hidden = true;
            const divTableTipoHora = document.querySelector('#div_table_tipo_hora');
            divTableTipoHora.hidden = true;
            const divTableNovedades = document.querySelector('#div_table_novedades');
            divTableNovedades.hidden = true;
        }
    });

    setTimeout(() => {
        setPicker(datePickerInstance);
    }, 100);

    const divTable = document.querySelector('#div_table');
    const divTableTipoHora = document.querySelector('#div_table_tipo_hora');
    const divTableNovedades = document.querySelector('#div_table_novedades');

    // divTable.innerHTML = `<table id="${DT_GRILLA}" class="table w-100 text-nowrap"></table>`;
    divTableTipoHora.innerHTML = `<table id="${DT_TIPO_HORA}" class="table w-100 text-nowrap"></table>`;
    divTableNovedades.innerHTML = `<table id="${DT_NOVEDADES}" class="table w-100 text-nowrap"></table>`;
    divTable.hidden = true;

    getData('view');
    getData('xls');

});
const marcarColumnas = (selectorTable) => {
    const columnasMarcadas = ls.get(COLUMNAS_MARCADAS) ?? [];
    if (columnasMarcadas.length) { // si hay columnas marcadas
        const table = `${selectorTable}`; // obtener la tabla

        if (!$.fn.DataTable.isDataTable(table)) {
            alert('no hay tabla ' + selectorTable);
            return;
        }

        const dt = $(table).DataTable(); // obtener la instancia de la tabla
        const rows = dt.rows().data(); // obtener los datos de la tabla

        rows.each((row, index) => { // recorrer los datos
            const THoCodi = parseInt(row.THoCodi); // obtener el THoCodi
            const checked = columnasMarcadas.includes(THoCodi); // verificar si el THoCodi está en el array
            const checkbox = document.querySelector(`${table} #THoCodi-${THoCodi}`); // obtener el checkbox
            if (checkbox) {
                checkbox.checked = checked; // marcar el checkbox
            }
        });
    }
}
const marcarNovedades = (selectorTable, row) => {
    const tableCustom = document.querySelector(selectorTable);
    if (!tableCustom) return; // si no hay tabla, salir

    const dtCustom = $(selectorTable).DataTable(); // obtener la instancia de la tabla
    if (!dtCustom) return; // si no hay instancia de la tabla, salir

    tableCustom.addEventListener('click', (e) => {
        const targetCustom = e.target;

        if (targetCustom.matches('input[type="checkbox"]')) {
            // obtener el id del checkbox marcado
            const idCheckbox = targetCustom.id;

            // recorrer la tabla y obtener el value de los checkbox marcados
            const rows = dtCustom.rows().data(); // obtener los datos de la tabla
            let values = []; // array para almacenar los valores de los checkbox marcados
            rows.each((row, index) => {
                const id = row.NovCodi;
                const checkbox = tableCustom.querySelector(`#NovCodi-${id}`);
                if (checkbox.checked) {
                    values.push(id);
                }
            });

            axios.post(DIR_APP_DATA + '/params', {
                valores: values.join(','), // valores de los checkbox marcados
                descripcion: row.THoDesc,
                modulo: 46
            }).then((data) => {
                const checkbox = document.querySelector(`#dt_novedades_${row.THoCodi} #${idCheckbox}`);
                const style = document.querySelector('style');
                const label = `#dt_novedades_${row.THoCodi} label[for="${idCheckbox}"]`;
                const opacity = checkbox.checked ? 1 : .7;
                style.innerHTML += ` ${label}::before { opacity: ${opacity}; } `;
            }).catch((error) => {
                alert('Error al guardar las novedades');
            });
            return;
        };
    });
}
const guardarColumna = (selectorTable) => {
    const table = document.querySelector(selectorTable);
    if (!table) return;
    const dt = $(selectorTable).DataTable();
    table.addEventListener('change', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const target = e.target;
        if (target.matches('input[type="checkbox"]')) {
            const value = target.value;
            const row = dt.row(target.closest('tr')).data();
            if (!row) return;
            // almacenar el THoCodi marcado eun un objeto
            row.THoCodi = parseInt(value);
            row.checked = target.checked;
            // dt.row(target.closest('tr')).data(row).draw();
            let columnasMarcadas = ls.get(COLUMNAS_MARCADAS) ?? [];

            if (!Array.isArray(columnasMarcadas)) {
                columnasMarcadas = [];
            }
            // agregar el THoCodi al array si no está ya presente
            if (!columnasMarcadas.includes(row.THoCodi)) {
                columnasMarcadas.push(row.THoCodi);
            }
            // si se desmarca el checkbox, eliminar el THoCodi del array
            if (!row.checked) {
                columnasMarcadas = columnasMarcadas.filter(codigo => codigo !== row.THoCodi);
            }
            // almacenar el objeto en el localStorage
            ls.set(COLUMNAS_MARCADAS, columnasMarcadas);
            const splitColumnasMarcadas = columnasMarcadas.join(',');

            axios.post(DIR_APP_DATA + '/params', {
                valores: splitColumnasMarcadas,
                descripcion: 'columnas',
                modulo: 46
            }).then((data) => {
                saveSorted(selectorTable);
            }).catch((error) => {
            });
        }
    });
}
const saveSorted = (selectorTable) => {

    const tBody = document.querySelector(`${selectorTable} tbody`);
    if (!tBody) return;

    let sortedContent = [];
    const checkedRows = tBody.querySelectorAll(`tr.sortable-row input[type="checkbox"]:checked`);
    if (!checkedRows) return;

    checkedRows.forEach(row => {
        let value = row.value; // obtener el valor del checkbox
        if (row.value) {
            sortedContent.push(value);
        }
    });
    ls.set(SORTED_COLUMNAS_MARCADAS, sortedContent);

    if (!Array.isArray(sortedContent)) {
        sortedContent = [];
    }

    const post = (values) => {
        axios.post(DIR_APP_DATA + '/params', {
            valores: values, descripcion: 'columnasSorted', modulo: 46
        }).then(() => {

        })
    }

    if (sortedContent.length) {
        const splitColumnas = sortedContent.join(',');
        post(splitColumnas);
    } else {
        post('');
    }
    // eliminar la clase table-secondary de las filas
    // tBody.querySelectorAll('tr').forEach(row => row.classList.remove('table-secondary'));
}
const sortedCols = (selectorTable) => {
    if (!selectorTable) return;

    const tBody = document.querySelector(`${selectorTable} tbody`);
    if (!tBody) return;

    new Sortable(tBody, {
        animation: 450,
        multiDrag: false,
        selectedClass: 'table-secondary',
        fallbackTolerance: 3, // distancia de desplazamiento antes de que se active el fallback
        handle: '.sortable-row', // Asegúrate de que solo las filas se puedan arrastrar
        onEnd: function () {
            saveSorted(selectorTable);
        }
    });
}
const configNovedad = (selectorTable) => {
    const table = document.querySelector(selectorTable);
    if (!table) return;
    const dt = $(table).DataTable();

    table.addEventListener('click', (e) => {
        const target = e.target;
        const row = dt.row(target.closest('tr')).data();
        if (!row) return;

        if (target.matches('.btn-nov') || target.closest('.btn-nov')) { // si se hace click en el botón de novedad

            const title = document.querySelector('#configModal .modal-title');
            const body = document.querySelector('#configModal .modal-body');
            const footer = document.querySelector('#configModal .modal-footer');

            $('#configModal').modal('show');

            title.textContent = `Custom: ${row.THoDesc}`;
            $('#configModal .modal-footer').remove();

            const dataNovedades = ls.get(LS_NOVEDADES) ?? [];

            const colCheckBox = (row) => {
                const id = row.NovCodi !== '' ? row.NovCodi : slugify(row.NovDesc);
                return `
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" value="${id}" id="NovCodi-${id}">
                    <label class="custom-control-label" for="NovCodi-${id}"></label>
                </div>
            `;
            };
            const columnNovedades = [
                { data: 'NovCodi', title: 'Cod', className: '', render: (data, type, row, meta) => data },
                {
                    data: 'NovDesc', title: 'Descripción', className: '', render: (data, type, row, meta) =>
                        `
                        <div class="d-flex flex-column">
                            <div class="text-truncate" style="max-width:180px" title="${data}">${data}</div>
                            <div class="font07 text-secondary" title="${row.NovTipoDesc}">${row.NovTipoDesc}</div>
                        </div>
                        `
                },
                { data: 'NovID', title: 'ID', className: 'w-100', render: (data, type, row, meta) => data },
                { data: 'NovCodi', className: '', render: (data, type, row, meta) => colCheckBox(row) },
            ];

            // body.innerHTML = `<table id="${DT_NOVEDADES}_${row.THoCodi}" class="table w-100 text-nowrap"></table>`;
            body.innerHTML = `<div id="div_${DT_NOVEDADES}_${row.THoCodi}" class="loader-in"></div>`;
            const opt = {
                paging: false,
                search: false,
                info: false,
                classTable: 'max-h-400 overflow-auto pr-1'
            }

            dt_grilla(`${DT_NOVEDADES}_${row.THoCodi}`, dataNovedades, columnNovedades, `#div_${DT_NOVEDADES}_${row.THoCodi}`, '', opt).then(() => {
                $(`#${DT_NOVEDADES}_${row.THoCodi} thead`).remove();

                $(`#div_${DT_NOVEDADES}_${row.THoCodi}`).addClass('loader-in');

                const dataColumn = axios.get(DIR_APP_DATA + '/params', {
                    params: {
                        descripcion: row.THoDesc,
                        modulo: 46
                    }
                }).then((data) => {
                    const valores = data.data[0]['valores'] ?? '';
                    let count = 0;
                    if (valores) {
                        const valoresSplit = valores.split(',');
                        count = valoresSplit.length;

                        const tableCustom = document.querySelector(`#${DT_NOVEDADES}_${row.THoCodi}`);
                        const dtCustom = $(tableCustom).DataTable();
                        const rows = dtCustom.rows().data();
                        rows.each((row, index) => {
                            const id = row.NovCodi;
                            const checkbox = tableCustom.querySelector(`#NovCodi-${id}`);
                            if (valoresSplit.includes(id)) {
                                checkbox.checked = true;
                            }
                        });
                    }
                    marcarNovedades(`#${DT_NOVEDADES}_${row.THoCodi}`, row);
                }).then(() => {
                    $(`#div_${DT_NOVEDADES}_${row.THoCodi}`).removeClass('loader-in');
                }).finally(() => {
                });
            });
        }
    });
}
const applySelect2 = (selector, placeholder, search = -1, data = []) => {
    $(selector).select2({
        placeholder: placeholder,
        minimumResultsForSearch: search,
        data: data,
        language: {
            noResults: function () {
                return 'No hay resultados..'
            },
            searching: function () {
                return ''
            },
            errorLoading: function () {
                return 'Sin datos..'
            },
        },
    });
}
const renderOptionSelect = (selector, id, text) => {
    const option = document.createElement('option');
    option.value = id;
    option.textContent = text;
    selector.appendChild(option);
}
const renderButton = (array, tipo, selector, datePickerInstance, label) => {
    if (!array) return;
    // if (tipo == 'month') {
    //     array.sort((a, b) => a - b);
    // }
    const div = document.querySelector(selector);
    div.innerHTML = '';
    let render = '';
    let text = '';
    array.forEach(el => {
        switch (tipo) {
            case 'year':
                text = el;
                break;
            case 'month':
                text = mesStringShort[el];
                break;
            case 'jornal':
                text = JornalString[el];
                break;
            case 'tipo':
                text = tipoPerString[el];
                break;
        }
        render += `<label class="btn btn-outline-dark border-ddd font08"><input type="radio" name="${tipo}" id=${tipo}_${el}" value="${el}"> ${text}</label>`;
    });
    div.innerHTML = `
        <label class="w-100 m-0">${label}</label>
        <div class="btn-group btn-group-toggle overflow-auto" data-toggle="buttons" style="display:flex; gap:5px">
            ${render}
        </div>
    `;
    // onClick para los botones
    const buttons = div.querySelectorAll('.btn-group-toggle label');
    buttons[0].classList.add('active');

    buttons.forEach(el => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const value = el.querySelector('input').value;
            buttons.forEach(el => el.classList.remove('active'));
            el.classList.add('active');
            $(`.selectjs_${tipo}`).val(value).trigger('change');
            setPicker(datePickerInstance);
        });
    });
}
const inputVal = () => {
    const year = document.querySelector('.selectjs_year').value ?? '';
    const month = document.querySelector('.selectjs_month').value ?? '';
    const tipo = document.querySelector('.selectjs_tipo').value ?? '';
    let jornal = document.querySelector('.selectjs_jornal').value ?? '';
    // jornal = tipo == 1 ? jornal : '';
    const obj = {
        year: parseInt(year),
        month: parseInt(month),
        tipo: parseInt(tipo),
        jornal: parseInt(jornal)
    };
    ls.set(LS_VALOR_LIQUID, obj);
    return obj;
}
const setPicker = async (datePickerInstance) => {
    if (!datePickerInstance) return;
    const div_fechas = document.querySelector('.div_fechas');
    if (!div_fechas) return;
    const classes = ['font-weight-bold', 'text-secondary'] ?? [];
    div_fechas.classList.add(...classes);
    let { year, month, tipo, jornal } = inputVal();
    let { MensDesde, MensHasta, Jor1Desde, Jor1Hasta, Jor2Desde, Jor2Hasta } = ls.get(LS_PARAM_LIQUID) ?? {};
    let monthDesdeCalculado = month;
    let monthHastaCalculado = month;
    let yearDesdeCalculado = year;
    let yearHastaCalculado = year;
    let monthJorDesdeCalculado = month;
    let monthJorHastaCalculado = month;
    let yearJorDesdeCalculado = year;
    let yearJorHastaCalculado = year;
    const isLeapYear = moment([year]).isLeapYear();

    const divJornal = document.querySelector('.div_jornal');
    const divJornales = document.querySelector('.div_jornales');

    const lastDayOfMonth = moment(`${month}/${year}`, 'MM/YYYY').daysInMonth();

    if (!isLeapYear && MensDesde === 29 && monthHastaCalculado === 3) {
        MensDesde = moment(`${monthDesdeCalculado - 1}/${year}`, 'MM/YYYY').daysInMonth();
    }

    if (!isLeapYear && Jor1Desde === 29 && monthHastaCalculado === 3) {
        Jor1Desde = moment(`${monthDesdeCalculado - 1}/${year}`, 'MM/YYYY').daysInMonth();
    }

    // Validar MensDesde y MensHasta
    if (MensDesde >= MensHasta) {
        if (month === 1) {
            monthDesdeCalculado = 12;
            yearDesdeCalculado -= 1;
        } else {
            monthDesdeCalculado -= 1;
        }
    }

    // Ajustar MensHasta si es 31
    if (MensHasta === 31) {
        MensHasta = lastDayOfMonth;
    }
    if (!isLeapYear && MensHasta === 29 && monthHastaCalculado === 2) {
        MensHasta = lastDayOfMonth;
    }

    // Validar Jor1Desde y Jor1Hasta
    if (Jor1Desde >= Jor1Hasta) {
        if (month === 1) {
            monthJorDesdeCalculado = 12;
            yearJorDesdeCalculado -= 1;
        } else {
            monthJorDesdeCalculado -= 1;
        }
    }

    // Validar Jor2Desde y Jor2Hasta
    if (Jor2Hasta < Jor2Desde) {
        if (month === 12) {
            monthJorHastaCalculado = 1;
            yearJorHastaCalculado += 1;
        } else {
            monthJorHastaCalculado += 1;
        }
    }

    if (Jor2Hasta === 31) {
        Jor2Hasta = lastDayOfMonth;
    }

    if (!isLeapYear && Jor2Hasta === 29 && monthJorHastaCalculado === 2) {
        Jor2Hasta = lastDayOfMonth;
    }
    if (Jor2Hasta === 30 && monthJorHastaCalculado === 2) {
        Jor2Hasta = lastDayOfMonth;
    }
    // console.log({ Jor1Desde, Jor1Hasta, Jor2Desde, Jor2Hasta });
    // console.log({ jornal });

    let start, end;

    const lastDaysJor1Desde = moment(`${monthJorDesdeCalculado}/${yearJorDesdeCalculado}`, 'MM/YYYY').daysInMonth();
    if (jornal === 1 && Jor1Desde > lastDaysJor1Desde) {
        Jor1Desde = lastDaysJor1Desde;
    }

    if (tipo === 0) { // Mensual

        start = `${MensDesde}/${monthDesdeCalculado}/${yearDesdeCalculado}`;
        end = `${MensHasta}/${monthHastaCalculado}/${yearHastaCalculado}`;

    } else if (tipo === 1) { // Jornal
        start = jornal === 1 ? `${Jor1Desde}/${monthJorDesdeCalculado}/${yearJorDesdeCalculado}` : `${Jor2Desde}/${month}/${yearJorDesdeCalculado}`;
        end = jornal === 1 ? `${Jor1Hasta}/${monthJorHastaCalculado}/${yearJorHastaCalculado}` : `${Jor2Hasta}/${monthJorHastaCalculado}/${yearJorHastaCalculado}`;
    }

    divJornal.classList.toggle('loader-in', tipo != 1);
    divJornales.classList.toggle('loader-in', tipo != 1);
    datePickerInstance.set_date(start, end);
    const startFormat = moment(start, 'DD/MM/YYYY').format('YYYY-MM-DD');
    const endFormat = moment(end, 'DD/MM/YYYY').format('YYYY-MM-DD');
    ls.set(LS_VALOR_FECHA, { startFormat, endFormat });
    setTimeout(() => {
        div_fechas.classList.remove(...classes);
    }, 300);
}
const countDiffDays = (start, end) => {
    const startMoment = moment(start, 'DD/MM/YYYY');
    const endMoment = moment(end, 'DD/MM/YYYY');
    const days = endMoment.diff(startMoment, 'days') + 1;
    return days;
}
const getData = (action) => {
    if (!action) return;

    const accionesDisponibles = ['view', 'xls'];

    if (!accionesDisponibles.includes(action)) return;

    const selectorClick = document.querySelector(`#${action}`);

    if (selectorClick) {
        selectorClick.addEventListener('click', async (e) => {
            $('#div_table').addClass('loader-in py-3');
            submitData(action);
            return;
        });
    }
}
const submitData = async (action) => {

    const tipo = action;
    const selectorClick = document.querySelector(`#${action}`);
    selectorClick.classList.toggle('loader-in', true);

    const divTableTipoHora = document.querySelector('#div_table_tipo_hora');
    divTableTipoHora.hidden = true;

    $.notifyClose();
    notify('Aguarde por favor...', 'dark', 0, 'right');

    const Valores = ls.get(LS_VALOR_LIQUID) ?? {};

    const datePicker = document.querySelector('.date-picker');
    const picker = $(datePicker).data('daterangepicker');
    const start = picker.startDate.format('YYYY/MM/DD');
    const end = picker.endDate.format('YYYY/MM/DD');
    const reporte = document.querySelector('.selectjs_reporte').value ?? 0;

    const FechaIni = start;
    const FechaFin = end;
    const LegTipo = Valores.tipo ?? 0;
    const Params = ls.get(LS_PARAM_LIQUID) ?? {};
    const Values = ls.get(LS_VALOR_LIQUID) ?? {};

    axios.post(DIR_APP_DATA + '/prysmian/' + action, {
        FechIni: FechaIni,
        FechFin: FechaFin,
        getNov: 1,
        NovA: [1],
        LegTipo: [LegTipo],
        start: 0,
        length: 10000,
        flag: FLAG,
        data: { ...Params, ...Values },
        Reporte: reporte
    }).then((data) => {
        $.notifyClose();
        selectorClick.classList.toggle('loader-in', false);
        if (data.data) {
            let columnConfigs = [];
            let columnConfigsHoras = [];
            let titleTabla = '';
            let datos = data.data;
            switch (reporte) {
                case '1':
                    titleTabla = 'Inasistencias';
                    columnConfigs = [
                        { data: 'Action', title: 'Action', className: 'text-center', render: (data, type, row, meta) => data },
                        { data: 'Employee', title: 'Legajo', className: '', render: (data, type, row, meta) => data },
                        { data: 'EmployeeStr', title: 'Nombre', className: '', render: (data, type, row, meta) => data },
                        { data: 'Cod Inasistencia', title: 'Cod', className: 'text-center', render: (data, type, row, meta) => data },
                        {
                            data: 'NovedadStr', title: 'Novedad', className: '', render: (data, type, row, meta) => {
                                const Novedad = row['Novedad'] ?? '';
                                return `(${Novedad}) ${data}`;
                            }
                        },
                        { data: 'Fecha inicio', title: 'Inicio', className: '', render: (data, type, row, meta) => moment(data, 'YYYY-MM-DD').format('DD/MM/YYYY') },
                        { data: 'Fecha fin', title: 'Fin', className: '', render: (data, type, row, meta) => moment(data, 'YYYY-MM-DD').format('DD/MM/YYYY') },
                        { data: '', title: '', className: 'w-100', render: () => '' }
                    ];
                    break;
                case '2':
                    titleTabla = 'Reporte de Actividad';
                    datos = data.data.data;
                    const columnKeys = data.data.columnKeys ?? {};
                    let index = 0;
                    for (const key in columnKeys) {
                        index++;
                        const tipo = columnKeys[key].tipo ?? '';
                        // const esPar = index % 2 === 0;
                        const colum = columnKeys[key].titulo ?? '';
                        const clase = tipo === 'number' ? 'text-center' : '';
                        // const clase2 = (tipo === 'number' && esPar) ? ' bg-light' : '';
                        columnConfigs.push({ data: '', title: colum, className: clase, render: (data, type, row, meta) => row[key] == '00:00' ? '-' : row[key] });
                    }
                    break;
            }

            if (tipo === 'view') {
                dt_grilla(`${DT_GRILLA}_${reporte}`, datos, columnConfigs, '#div_table', '<div class="mb-2 font-weight-bolder font09 text-secondary">' + titleTabla + '</div>');
                return;
            }

            $.notifyClose();
            const archivo = data.data.archivo ?? '';
            if (!archivo) {
                throw new Error('No se pudo generar el archivo.');
            }
            const bannerDownload = `
                    <div class="d-flex flex-column">
                        <div class="font-weight-bold">Reporte generado.</div>
                        <a href="${DIR_APP_DATA}/${archivo}" class="btn btn-custom px-2 btn-sm mt-2 font08 download" target="_blank" download>
                        <div class="d-flex align-items-center w-100 justify-content-center" style="gap:5px">
                            <span>Descargar</span> <i class="bi bi-file-earmark-arrow-down font1"></i>
                        </div>
                        </a>
                    </div>
                `;
            notify(bannerDownload, 'warning', 0, 'right');

            const download = document.querySelector('.download');

            if (download) {
                download.addEventListener('click', (e) => {
                    $.notifyClose();
                });
            }

            $('#div_table').removeClass('loader-in');
        }
    }).catch((error) => {
        const errorMessage = error.response?.data?.message ?? error.message
        notify(errorMessage, 'danger', 2000, 'right');
    });
}
const dt_grilla = async (idTable, dataSource, columnConfigs, selectorDiv, titulo = '', opt = {}) => {
    const divTable = selectorDiv && document.querySelector(selectorDiv);
    console.log({ idTable, dataSource, columnConfigs, selectorDiv, divTable });

    if (!divTable) return;
    // console.log({ idTable, dataSource, columnConfigs, selectorDiv, divTable });
    if ($.fn.DataTable.isDataTable(`#${idTable}`)) {
        $(`#${idTable}`).DataTable().destroy();
    }

    divTable.innerHTML = `<table id="${idTable}" class="table w-100 text-nowrap loader-in"></table>`;

    await new Promise(resolve => setTimeout(resolve, 0));

    const optPaging = opt.paging ?? true;
    const optSearch = opt.search ?? true;
    const optInfo = opt.info ?? true;
    const optClassTable = opt.classTable ?? '';

    $(`#${idTable}`).DataTable({
        dom: `
        <'row '
            <'col-12 titulo'>
        >
        <'row '
            <'col-12 d-inline-flex justify-content-between'lf>
            <'col-12'<'table-responsive border-radius border p-2 fadeIn w-100'<'${optClassTable}'t>>>
        >
        <'row pt-2'
            <'col-12 d-sm-block d-none'<'d-flex justify-content-between align-items-center'ip>>
            <'col-12 d-sm-none d-block'<'d-flex justify-content-center align-items-center'i>>
            <'col-12 d-sm-none d-block'<'d-flex justify-content-center align-items-center pb-3'p>>
        >`,
        data: dataSource,
        createdRow: function (row, data, dataIndex) {
            row.classList.add('sortable-row');
        },
        columns: columnConfigs.map(config => ({
            data: config.data,
            title: config.title,
            className: config.className,
            render: config.render
        })),
        initComplete: function (settings, json) {
            const searchInput = document.querySelector(`#${idTable}_filter input`);
            searchInput && searchInput.setAttribute('placeholder', 'Buscar..');
            const t = document.querySelector(`#${idTable}_wrapper .titulo`);
            t.innerHTML = titulo;
        },
        // ondraw
        drawCallback: function (settings) {
            divTable.classList.remove('loader-in');
            divTable.hidden = false;
            $(`#${idTable}`).removeClass('loader-in');
        },
        lengthMenu: [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
        deferRender: true,
        searchDelay: 200,
        paging: optPaging,
        searching: optSearch,
        info: optInfo,
        ordering: false,
        language: {
            "processing": "Procesando..",
            "loadingRecords": "Actualizando..",
            "sLengthMenu": "_MENU_",
            "sZeroRecords": "",
            "sEmptyTable": "Sin resultados",
            "sInfo": "_START_ / _END_ | _TOTAL_ Registros",
            "sInfoEmpty": "",
            "sInfoFiltered": "",
            "sInfoPostFix": "",
            "sSearch": "",
            "sUrl": "",
            "sInfoThousands": ",",
            "oPaginate": {
                "sFirst": "<i class='bi bi-chevron-double-left'></i>",
                "sLast": "<i class='bi bi-chevron-double-right'></i>",
                "sNext": "<i class='bi bi-chevron-right'></i>",
                "sPrevious": "<i class='bi bi-chevron-left'></i>"
            },
            "oAria": {
                "sSortAscending": ":Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ":Activar para ordenar la columna de manera descendente"
            }
        }
    });
    $.fn.DataTable.ext.pager.numbers_length = 5;
}
