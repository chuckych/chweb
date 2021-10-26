$(function () {
  "use strict";

  let opt2 = {
    MinLength: "0",
    SelClose: false,
    MaxInpLength: "10",
    delay: "250",
    allowClear: true,
  };
  $("#nombreAud").select2({
    language: "es",
    multiple: false,
    allowClear: opt2["allowClear"],
    language: "es",
    placeholder: "Buscar Nombre",
    minimumInputLength: opt2["MinLength"],
    minimumResultsForSearch: 5,
    maximumInputLength: opt2["MaxInpLength"],
    selectOnClose: opt2["SelClose"],
    language: {
      noResults: function () {
        return "No hay resultados..";
      },
      inputTooLong: function (args) {
        var message =
          "Máximo " +
          opt2["MaxInpLength"] +
          " caracteres. Elimine " +
          overChars +
          " caracter";
        if (overChars != 1) {
          message += "es";
        }
        return message;
      },
      searching: function () {
        return "Buscando..";
      },
      errorLoading: function () {
        return "Sin datos..";
      },
      removeAllItems: function () {
        return "Borrar";
      },
      inputTooShort: function () {
        return "Ingresar " + opt2["MinLength"] + " o mas caracteres";
      },
      maximumSelected: function () {
        return "Puede seleccionar solo una opción";
      },
      loadingMore: function () {
        return "Cargando más resultados…";
      },
    },
    ajax: {
      url: "getSelect.php?d=nombre",
      dataType: "json",
      type: "POST",
      delay: opt2["delay"],
      data: function (params) {
        return {
          q: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
    },
  });
  $("#userAud").select2({
    language: "es",
    multiple: false,
    allowClear: opt2["allowClear"],
    language: "es",
    placeholder: "Buscar Usuario",
    minimumInputLength: opt2["MinLength"],
    minimumResultsForSearch: 5,
    maximumInputLength: opt2["MaxInpLength"],
    selectOnClose: opt2["SelClose"],
    language: {
      noResults: function () {
        return "No hay resultados..";
      },
      inputTooLong: function (args) {
        var message =
          "Máximo " +
          opt2["MaxInpLength"] +
          " caracteres. Elimine " +
          overChars +
          " caracter";
        if (overChars != 1) {
          message += "es";
        }
        return message;
      },
      searching: function () {
        return "Buscando..";
      },
      errorLoading: function () {
        return "Sin datos..";
      },
      removeAllItems: function () {
        return "Borrar";
      },
      inputTooShort: function () {
        return "Ingresar " + opt2["MinLength"] + " o mas caracteres";
      },
      maximumSelected: function () {
        return "Puede seleccionar solo una opción";
      },
      loadingMore: function () {
        return "Cargando más resultados…";
      },
    },
    ajax: {
      url: "getSelect.php?d=usuario",
      dataType: "json",
      type: "POST",
      delay: opt2["delay"],
      data: function (params) {
        return {
          q: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
    },
  });
  $("#idSesionAud").select2({
    language: "es",
    multiple: false,
    allowClear: opt2["allowClear"],
    language: "es",
    placeholder: "ID Sesion",
    minimumInputLength: opt2["MinLength"],
    minimumResultsForSearch: 5,
    maximumInputLength: opt2["MaxInpLength"],
    selectOnClose: opt2["SelClose"],
    language: {
      noResults: function () {
        return "No hay resultados..";
      },
      inputTooLong: function (args) {
        var message =
          "Máximo " +
          opt2["MaxInpLength"] +
          " caracteres. Elimine " +
          overChars +
          " caracter";
        if (overChars != 1) {
          message += "es";
        }
        return message;
      },
      searching: function () {
        return "Buscando..";
      },
      errorLoading: function () {
        return "Sin datos..";
      },
      removeAllItems: function () {
        return "Borrar";
      },
      inputTooShort: function () {
        return "Ingresar " + opt2["MinLength"] + " o mas caracteres";
      },
      maximumSelected: function () {
        return "Puede seleccionar solo una opción";
      },
      loadingMore: function () {
        return "Cargando más resultados…";
      },
    },
    ajax: {
      url: "getSelect.php?d=id_sesion",
      dataType: "json",
      type: "POST",
      delay: opt2["delay"],
      data: function (params) {
        return {
          q: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
    },
  });
  $("#tipoAud").select2({
    language: "es",
    multiple: false,
    allowClear: opt2["allowClear"],
    language: "es",
    placeholder: "Tipo",
    minimumInputLength: opt2["MinLength"],
    minimumResultsForSearch: 5,
    maximumInputLength: opt2["MaxInpLength"],
    selectOnClose: opt2["SelClose"],
    language: {
      noResults: function () {
        return "No hay resultados..";
      },
      inputTooLong: function (args) {
        var message =
          "Máximo " +
          opt2["MaxInpLength"] +
          " caracteres. Elimine " +
          overChars +
          " caracter";
        if (overChars != 1) {
          message += "es";
        }
        return message;
      },
      searching: function () {
        return "Buscando..";
      },
      errorLoading: function () {
        return "Sin datos..";
      },
      removeAllItems: function () {
        return "Borrar";
      },
      inputTooShort: function () {
        return "Ingresar " + opt2["MinLength"] + " o mas caracteres";
      },
      maximumSelected: function () {
        return "Puede seleccionar solo una opción";
      },
      loadingMore: function () {
        return "Cargando más resultados…";
      },
    },
    ajax: {
      url: "getSelect.php?d=tipo",
      dataType: "json",
      type: "POST",
      delay: opt2["delay"],
      data: function (params) {
        return {
          q: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
    },
  });
  $("#cuentaAud").select2({
    language: "es",
    multiple: false,
    allowClear: opt2["allowClear"],
    language: "es",
    placeholder: "Cuenta",
    minimumInputLength: opt2["MinLength"],
    minimumResultsForSearch: 5,
    maximumInputLength: opt2["MaxInpLength"],
    selectOnClose: opt2["SelClose"],
    language: {
      noResults: function () {
        return "No hay resultados..";
      },
      inputTooLong: function (args) {
        var message =
          "Máximo " +
          opt2["MaxInpLength"] +
          " caracteres. Elimine " +
          overChars +
          " caracter";
        if (overChars != 1) {
          message += "es";
        }
        return message;
      },
      searching: function () {
        return "Buscando..";
      },
      errorLoading: function () {
        return "Sin datos..";
      },
      removeAllItems: function () {
        return "Borrar";
      },
      inputTooShort: function () {
        return "Ingresar " + opt2["MinLength"] + " o mas caracteres";
      },
      maximumSelected: function () {
        return "Puede seleccionar solo una opción";
      },
      loadingMore: function () {
        return "Cargando más resultados…";
      },
    },
    ajax: {
      url: "getSelect.php?d=audcuenta",
      dataType: "json",
      type: "POST",
      delay: opt2["delay"],
      data: function (params) {
        return {
          q: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
    },
  });
  var maskBehavior = function (val) {
    val = val.split(":");
    return parseInt(val[0]) > 19 ? "HZ:M0:M0" : "H0:M0:M0";
  };
  let spOptions = {
    onKeyPress: function (val, e, field, options) {
      field.mask(maskBehavior.apply({}, arguments), options);
    },
    translation: {
      H: { pattern: /[0-2]/, optional: false },
      Z: { pattern: /[0-3]/, optional: false },
      M: { pattern: /[0-5]/, optional: false },
    },
  };
  $(".HoraMask").mask(maskBehavior, spOptions);
});
