<?php
// Iniciar sesión y generar token CSRF
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!doctype html>
<html lang="es">

<head>
    <style>
        .validation-info {
            background-color: #ffffff;
            padding: 20px;
            margin-top: 20px;
        }

        .required-field {
            color: #dc3545;
            font-weight: bold;
        }

        .table th {
            /* border: none; */
            font-weight: normal
        }

        .small-info {
            font-size: 0.85rem;
        }

        .download-example {
            margin-top: 15px;
        }

        .custom-input::-webkit-file-upload-button {
            visibility: hidden;
        }

        .custom-input::before {
            content: 'Selecionar archivo';
            display: inline-block;
            background: linear-gradient(top, #f9f9f9, #e3e3e3);
            border: 1px solid #999;
            padding: 5px 8px;
            outline: none;
            white-space: nowrap;
            -webkit-user-select: none;
            cursor: pointer;
            /* text-shadow: 1px 1px #fff; */
            font-size: 10pt;
        }

        .custom-input:hover::before {
            border-color: black;
            background-color: #4a5461;
            color: white;
            transition: background-color 0.5s ease;
        }

        .custom-input:active::before {
            background: -webkit-linear-gradient(top, #e3e3e3, #f9f9f9);
        }

        .submit-import {
            background-color: #4a5461;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            font-weight: normal;
            margin-top: 20px;
            transition: background-color 0.3s ease;
            width: 250px;
        }

        .submit-import:hover {
            background-color: rgb(116, 129, 147);
        }

        /* Estilo para hacer sticky las filas de la cabecera */
        .sticky-header {
            position: sticky;
            top: 0;
            /* Asegura que el fondo sea visible */
            z-index: 10;
            /* Asegura que la cabecera esté sobre el contenido desplazable */
        }

        /* Estilo para el contenedor de la tabla */
        .table-responsive {
            max-height: 400px;
            /* Altura máxima del contenedor */
            overflow-y: auto;
            /* Permite el desplazamiento vertical */
        }

        .icon-index {
            /* Rotar 45 grados */
            transform: rotate(45deg);
        }
    </style>
</head>

<div class="p-3">

    <!-- Modal para importar usuarios -->
    <div class="modal fadeInDown" id="importUserModal" tabindex="-1" aria-labelledby="importUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content bg-light">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="importUserModalLabel">Importar Usuarios</h5>
                    <span class="bi bi-x-lg pointer" data-dismiss="modal" aria-label="Close"></span>
                </div>
                <div class="modal-body">
                    <form id="importUserForm" enctype="multipart/form-data">
                        <div class="border p-3 shadow-sm d-flex flex-column bg-white" style="gap: 1rem;">
                            <label for="userFile" class="form-label">Seleccione un archivo Excel (.xls o .xlsx)</label>
                            <input type="file" size="32" class="custom-input font09" id="userFile" name="userFile"
                                accept=".xls,.xlsx">
                            <div class="form-text small-info">Tamaño máximo del archivo: 2MB</div>
                            <div id="fileError" class="text-danger mt-2 d-none">Por favor, seleccione un archivo válido
                                (xls o xlsx)</div>
                            <button type="button" class="submit-import" id="submitImport">Procesar archivo</button>
                        </div>
                        <!-- Campo oculto para token CSRF -->
                        <input type="hidden" name="csrf_token" id="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    </form>

                    <!-- Sección de información y ejemplo -->
                    <div class="validation-info border">
                        <div class="d-flex justify-content-between align-items-center" style="gap: 1rem;">
                            <p class="">Información sobre el formato y validaciones</p>
                            <a href="download_example.php" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download me-1"></i> Descargar plantilla
                            </a>
                        </div>

                        <p class="small-info">El archivo Excel debe contener las siguientes columnas en la primera fila
                            como encabezados:</p>

                        <div class="table-responsive">
                            <table class="table table-bordered border bg-white table-sm">
                                <thead class="table-primary" style="height:40px;">
                                    <tr>
                                        <th>ID <span class="required-field">*</span></th>
                                        <th>Nombre y Apellido <span class="required-field">*</span></th>
                                        <th>Estado <span class="required-field">*</span></th>
                                        <th>Visualizar zona <span class="required-field">*</span></th>
                                        <th>Bloqueo Fecha inicio</th>
                                        <th>Bloqueo Fecha Fin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Juan Pérez</td>
                                        <td>activo</td>
                                        <td>activo</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Ana García</td>
                                        <td>bloqueado</td>
                                        <td>inactivo</td>
                                        <td>2023-01-01</td>
                                        <td>2023-12-31</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <p class="small-info"><span class="required-field">*</span> Campos obligatorios</p>

                        <div class="border bg-light small-info p-3">
                            <h6 class="">Validaciones por columna:</h6>
                            <ul class="small-info">
                                <li><strong>ID:</strong> Obligatorio. Debe ser un número entero. No se permiten IDs
                                    duplicados.</li>
                                <li><strong>Nombre y Apellido:</strong> Obligatorio. Longitud máxima de 50 caracteres.
                                </li>
                                <li><strong>Estado:</strong> Obligatorio. Valores permitidos: "activo" o "bloqueado".
                                </li>
                                <li><strong>Visualizar zona:</strong> Obligatorio. Valores permitidos: "activo" o
                                    "inactivo".</li>
                                <li><strong>Bloqueo Fecha inicio:</strong> Opcional. Si se proporciona, debe tener
                                    formato YYYY-MM-DD (ej. 2025-01-01).</li>
                                <li><strong>Bloqueo Fecha Fin:</strong> Opcional. Si se proporciona, debe tener formato
                                    YYYY-MM-DD (ej. 2025-12-31).</li>
                            </ul>

                            <strong>Nota:</strong>
                            <ul class="p-3 m-0">
                                <li>La primera fila del archivo debe contener los encabezados exactamente como se
                                    muestran arriba.</li>
                                <li>Asegúrese de que no existan IDs duplicados en su archivo.</li>
                                <li>Las fechas pueden ingresarse en formato Excel o como texto con formato YYYY-MM-DD.
                                </li>
                                <li>El sistema procesa hasta un máximo de 1000 filas por archivo.</li>
                                <li>Al finalizar la importación, se mostrará un informe con los resultados y posibles
                                    errores.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

</script>

</html>