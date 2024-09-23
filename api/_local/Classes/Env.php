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
        $this->fileEnv = __DIR__ . '../../../../../../config_chweb/';
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
}
