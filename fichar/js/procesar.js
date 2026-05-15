$(function () {
    "use strict";

    const FORMAT_BACKEND = 'YYYY-MM-DD';
    const FORMAT_VIEW = 'DD-MM-YYYY';

    const syncDateInputs = function (viewSelector, hiddenSelector) {
        const $view = $(viewSelector);
        const $hidden = $(hiddenSelector);

        if (!$view.length || !$hidden.length || typeof moment === 'undefined' || !$.fn.daterangepicker) {
            return;
        }

        const currentHidden = $hidden.val();
        const initialDate = moment(currentHidden, FORMAT_BACKEND, true).isValid()
            ? moment(currentHidden, FORMAT_BACKEND)
            : moment();

        $hidden.val(initialDate.format(FORMAT_BACKEND));
        $view.val(initialDate.format(FORMAT_VIEW));

        singleDatePicker(viewSelector, 'right', 'down', '', false, true);

        const picker = $view.data('daterangepicker');
        if (picker) {
            picker.locale.format = FORMAT_VIEW;
            picker.setStartDate(initialDate);
            picker.setEndDate(initialDate);
        }

        $view.on('apply.daterangepicker', function (ev, pickerDate) {
            $(this).val(pickerDate.startDate.format(FORMAT_VIEW));
            $hidden.val(pickerDate.startDate.format(FORMAT_BACKEND));
        });

        $view.on('change', function () {
            const typedDate = moment($(this).val(), FORMAT_VIEW, true);
            if (typedDate.isValid()) {
                $hidden.val(typedDate.format(FORMAT_BACKEND));
                $(this).val(typedDate.format(FORMAT_VIEW));
                return;
            }

            const hiddenDate = moment($hidden.val(), FORMAT_BACKEND, true);
            $(this).val((hiddenDate.isValid() ? hiddenDate : moment()).format(FORMAT_VIEW));
        });
    };

    syncDateInputs('#FichFechaIniView', '#FichFechaIni');
    syncDateInputs('#FichFechaFinView', '#FichFechaFin');

    onOpenSelect2()
    ActiveBTN(false, "#submit", '', 'Ingresar Fichadas')
    $(".FicharHorario").bind("submit", function (e) {
        e.preventDefault();
        CheckSesion()
        let loading = `<div class="spinner-border fontppp" role="status" style="width: 15px; height:15px" ></div>`
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: $(this).serialize(),
            beforeSend: function (data) {
                $.notifyClose();
                notify('Ingresando Fichadas', 'info', 0, 'right')
                ActiveBTN(true, "#submit", 'Aguarde <span class = "dotting mr-1"> </span> ' + loading, 'Ingresar Fichadas')
            },
            success: function (data) {
                if (data.status == "ok") {
                    $.notifyClose();
                    notify(data.Mensaje, 'success', 2000, 'right')
                    ActiveBTN(false, "#submit", 'Aguarde <span class = "dotting mr-1"> </span> ' + loading, 'Ingresar Fichadas')
                } else {
                    $.notifyClose();
                    notify(data.Mensaje, 'danger', 2000, 'right')
                    ActiveBTN(false, "#submit", 'Aguarde <span class = "dotting mr-1"> </span> ' + loading, 'Ingresar Fichadas')
                }
            }
        });
    });
});