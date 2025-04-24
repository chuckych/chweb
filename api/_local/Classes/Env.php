<?php

namespace Classes;

use Flight;

class Env
{
    private $dotenv;
    private $fileEnv;
    private $response;
    private $inicio;

    function __construct()
    {
        // $this->fileEnv = __DIR__ . '/../../../../../config_chweb/';
        $this->fileEnv = $this->getConfigPath();
        $this->dotenv = \Dotenv\Dotenv::createImmutable($this->fileEnv);
        $this->dotenv->load();
        $this->response = new Response;
        $this->inicio = microtime(true);
    }
    public function get(): array
    {
        $data = $this->dotenv->load() ?? '';

        if (!$data) {
            throw new \Exception('Error', 401);
        }

        foreach ($data as $key => $value) {
            $data[$key] = $value;
        }
        return $data;
    }
    private function ds()
    {
        return DIRECTORY_SEPARATOR;
    }
    private function configFile()
    {
        $DS = $this->ds();
        return __DIR__ . "..{$DS}..{$DS}..{$DS}path_config.txt";
    }
    /**
     * Obtiene la ruta de configuración normalizada
     * 
     * @param string $configFile Nombre del archivo de configuración
     * @return string Ruta de configuración normalizada
     */
    private function getConfigPath(): string
    {
        $DS = $this->ds();
        $configFile = $this->configFile();

        // $defaultPath = __DIR__ . DIRECTORY_SEPARATOR . '../../../../config_chweb/';
        $defaultPath = __DIR__ . "..{$DS}..{$DS}..{$DS}..{$DS}..{$DS}..{$DS}config_chweb{$DS}";

        // Si no existe el archivo de configuración, retorna la ruta por defecto
        if (!file_exists($configFile)) {
            return $defaultPath;
        }

        // Leer el contenido del archivo
        $path = trim(file_get_contents($configFile));
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
    }
}
