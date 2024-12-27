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
const LS_VALOR_FECHA = 'chweb_valor_fecha'
const FLAG = Date.now();
const DT_GRILLA = 'dt_grilla';
const DT_TIPO_HORA = 'dt_tipo_hora';

ls.set(LS_PARAM_LIQUID, {});
ls.set(LS_VALOR_LIQUID, {});
ls.set(LS_FECHAS, {});
const DIR_APP_DATA = '../../../app-data';

const getLiquid = async () => {
    try {
        const { data } = await axios.get('../../../app-data/parametros/liquid');
        await ls.set(LS_PARAM_LIQUID, data ?? {});
        return data ?? {};
    } catch (error) {
        console.error(error);
        return {};
    }
}
const getFechas = async () => {
    try {
        const { data } = await axios.get('../../../app-data/fichas/dates');
        await ls.set(LS_FECHAS, data ?? {});
        return data ?? {};
    } catch (error) {
        console.error(error);
        return {};
    }
}
const getTipoHora = async () => {
    try {
        const { data } = await axios.get(DIR_APP_DATA + '/horas/tipohora');
        await ls.set(LS_TIPO_HORA, data ?? {});
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
    getTipoHora();
    const years = data.años ?? {};
    if (!Object.keys(years).length) return;

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
    // const anioSeleccionado = yearsKeys[0];
    // currentMonth = new Date().getMonth() + 1;
    // if (anioSeleccionado == currentYear) { // si el año seleccionado es el actual 
    //     delete months[currentMonth]; 
    // }

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
        const value = e.params.data.id;
        const datos = ls.get(LS_TIPO_HORA) ?? [];
        if (value == 2) {
            console.log(datos);
            const columnConfigs = [
                { data: 'THoCodi', title: 'Hora', className: '', render: (data, type, row, meta) => data },
                { data: 'THoDesc2', title: 'Descripción', className: '', render: (data, type, row, meta) => data },
                { data: 'THoID', title: 'ID', className: '', render: (data, type, row, meta) => data },
                { data: '', title: '', className: 'w-100', render: (data, type, row, meta) => '' },
            ];
            dt_grilla(`#${DT_TIPO_HORA}`, datos, columnConfigs, '#div_table_tipo_hora');
        } else {
            const divTableTipoHora = document.querySelector('#div_table_tipo_hora');
            divTableTipoHora.hidden = true;
        }
    });


    setTimeout(() => {
        setPicker(datePickerInstance);
    }, 100);
    const divTable = document.querySelector('#div_table');
    const divTableTipoHora = document.querySelector('#div_table_tipo_hora');
    divTable.innerHTML = `<table id="${DT_GRILLA}" class="table w-100 text-nowrap"></table>`;
    divTableTipoHora.innerHTML = `<table id="${DT_TIPO_HORA}" class="table w-100 text-nowrap"></table>`;

    getData('view');
    getData('xls');

});
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
    const div_fechas = document.querySelector('.div_fechas');
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

    // console.clear();
    // console.log({ month });

    if (!isLeapYear && MensDesde === 29 && monthHastaCalculado === 3) {
        MensDesde = moment(`${monthDesdeCalculado - 1}/${year}`, 'MM/YYYY').daysInMonth();
    }

    if (!isLeapYear && Jor1Desde === 29 && monthHastaCalculado === 3) {
        Jor1Desde = moment(`${monthDesdeCalculado - 1}/${year}`, 'MM/YYYY').daysInMonth();
    }

    // console.log({ isLeapYear, MensDesde, monthHastaCalculado, monthDesdeCalculado });

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
            submitData(action);
            return;
        });
    }
}
const submitData = async (action) => {

    const tipo = action;

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

    axios.post('../../../app-data/prysmian/' + action, {
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

        if (data.data) {
            const datos = data.data;
            const columnConfigs = [
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
            if (tipo == 'view') {
                dt_grilla(`#${DT_GRILLA}`, datos, columnConfigs, '#div_table');
            } else if (tipo == 'xls') {
                $.notifyClose();
                const archivo = datos.archivo ?? '';
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
            }
        }
    }).catch((error) => {
        const errorMessage = error.response?.data?.message ?? error.message
        notify(errorMessage, 'danger', 2000, 'right');
    });
}
const dt_grilla = (idTable, dataSource, columnConfigs, selectorDiv) => {
    // const divTable = document.querySelector('#div_table');
    const divTable = document.querySelector(selectorDiv);
    if ($.fn.DataTable.isDataTable(idTable)) {
        $(idTable).DataTable().destroy();
    }
    $(idTable).DataTable({
        dom: `
        <'row '
            <'col-12 d-inline-flex justify-content-between'lf>
            <'col-12'<'table-responsive border-radius fadeIn w-100't>>
        >
        <'row pt-2'
            <'col-12 d-flex justify-content-between align-items-center'ip>
        >`,
        data: dataSource,
        createdRow: function (row, data, dataIndex) {
            // $(row).find('td').addClass('font08');
        },
        columns: columnConfigs.map(config => ({
            data: config.data,
            title: config.title,
            className: config.className,
            render: config.render
        })),
        initComplete: function (settings, json) {
            const searchInput = document.querySelector(`${idTable}_filter input`);
            searchInput && searchInput.setAttribute('placeholder', 'Buscar..');
            divTable.classList.remove('loader-in');
            divTable.hidden = false;
        },
        lengthMenu: [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
        deferRender: true,
        searchDelay: 200,
        paging: true,
        searching: true,
        info: true,
        ordering: false,
        // stateSave: -1,
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
