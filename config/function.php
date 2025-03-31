<?php
/**
 * Obtiene la ruta de configuración normalizada
 * 
 * @param string $configFile Nombre del archivo de configuración
 * @return string Ruta de configuración normalizada
 */
function getConfigPath(string $configFile = 'path_config.txt'): string
{
    try {
        $DS = DIRECTORY_SEPARATOR;
        $defaultPath = __DIR__ . $DS . '..' . $DS . '..' . $DS . '..' . $DS . 'config_chweb' . $DS;
        // $defaultPath = __DIR__ . DIRECTORY_SEPARATOR . '../../../config_chweb/';
        // Si no existe el archivo de configuración, retorna la ruta por defecto
        $PathConfigFile = __DIR__ . '/' . $configFile;

        if (!file_exists($PathConfigFile)) {
            return $defaultPath;
        }

        // Leer el contenido del archivo
        $path = trim(file_get_contents($PathConfigFile));
        if (empty($path)) {
            return $defaultPath;
        }

        // Normalizar separadores de directorio
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        // Quitar separador final si existe
        if (substr($path, -1) === DIRECTORY_SEPARATOR) {
            $path = substr($path, 0, -1);
        }

        // Verificar si la ruta existe
        if (!is_dir($path)) {
            return $defaultPath;
        }

        return $path . DIRECTORY_SEPARATOR;
    } catch (\Throwable $th) {
        error_log(print_r($th->getMessage(), true)); // Log the DSN for debugging
        return '';
    }
}