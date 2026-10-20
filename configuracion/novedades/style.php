:root {
    --primary-purple: #4B0082;
    /* --bg-light: #f8f9fa; */
    --border-color: #cecece;
}

.jscolor-btn-close {
    border: 1px solid var(--border-color);
    background-color: transparent !important;
}

th {
    vertical-align: middle !important;
    font-size: 1.1em;
    color: #000000;
}

#tblNovedades_paginate {
    margin-bottom: 0px !important;
    margin-top: 0px !important;
}

.col-form-label {
    font-size: .8rem;
}

.nav-link {
    font-size: .9rem;
    color: #333;
}

.table-sm td,
.table-sm th {
    border-top: 1px solid #dee2e6;
}

.form-control,
.form-control-sm,
input[type=search] {
    height: 40px !important;
    color: #000000;
}

.custom-control-label {
    padding-top: 0.2rem;
}

/* ============================================================
   MODAL NOVEDAD - FULLSCREEN CUSTOM
   ============================================================ */

/* Modal que ocupa toda la altura disponible */
#modal-novedad .modal-dialog.modal-fullscreen-custom {
    /* width: calc(100% - 20px); */
    /* max-width: none; */
    height: calc(100vh - 20px);
    margin: 10px auto;
}

/* El contenido del modal ocupa toda la altura del diálogo */
#modal-novedad .modal-content {
    height: 100%;
    display: flex;
    flex-direction: column;
}

/* El encabezado y el pie mantienen su tamaño natural */
#modal-novedad .modal-header,
#modal-novedad .modal-footer {
    flex-shrink: 0;
}

/* El body ocupa todo el espacio restante */
#modal-novedad .modal-body {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

/* El contenido de las pestañas puede crecer */
#modal-novedad .tab-content {
    min-height: 0;
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
}

/* Cada tab-pane se estira para ocupar el alto del tab-content */
#modal-novedad .tab-content>.tab-pane {
    flex: 1 1 auto;
    min-height: 0;
}

/* ============================================================
   TAB CONTROLES - Alto máximo entre header y footer
   ============================================================ */

/* El contenedor interno de Controles ocupa todo el alto del tab-pane */
#modal-novedad #tabControles .overflow-auto {
    height: 100%;
    overflow-y: auto;
}

dialog {
    border: 1px solid var(--border-color);
    border-radius: 5px;
    padding: 10px;
}

#dialogNovedad1 {
    overflow: visible;
}
#dialogHoras {
    overflow: visible;
}
#dialogNuevaNovedad {
    overflow: visible;
}

.dialog-header {
    display: flex;
    -ms-flex-align: start;
    align-items: flex-start;
    -ms-flex-pack: justify;
    justify-content: space-between;
    padding: 1rem 1rem;
    border-bottom: 1px solid #dee2e6;
    border-top-left-radius: calc(.3rem - 1px);
    border-top-right-radius: calc(.3rem - 1px);
}

.dialog-header-title {
    font-size: 1rem;
    font-weight: bold;
    margin: 0;
}

.dialog-body {
    position: relative;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    padding: 1rem;
    overflow: visible;
}

.dialog-footer {
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    -ms-flex-align: center;
    align-items: center;
    -ms-flex-pack: end;
    justify-content: flex-end;
    padding: .75rem;
    gap: .5rem;
    border-top: 1px solid #dee2e6;
    border-bottom-right-radius: calc(.3rem - 1px);
    border-bottom-left-radius: calc(.3rem - 1px);
}

/* Evita que el dropdown de Select2 quede oculto detrás del modal */
.modal-open .select2-container--open {
    z-index: 2000;
}

#dialogNovedad1 .select2-container--open {
    z-index: 2100;
}
#dialogHoras .select2-container--open {
    z-index: 2100;
}
#dialogNuevaNovedad .select2-container--open {
    z-index: 2100;
}