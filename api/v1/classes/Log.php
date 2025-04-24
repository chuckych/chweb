<?php

namespace Classes;

class Log
{
    private $path;
    private $pathCache;
    private $date;
    private $optJson;
    function __construct()
    {
        $this->path = __DIR__ . '/../logs/';
        $this->pathCache = __DIR__ . '/../cache/';
        $this->date = $this->dateTimeNow();
        $this->optJson = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT;
    }
    function write($text, $nameFile, $type = false)
    {
        $path = $this->path;
        $date = $this->date;
        $text = ($type == 'export') ? $text . "\n" : $date . ' ' . $text . "\n";
        file_put_contents($path . $nameFile, $text, FILE_APPEND | LOCK_EX);
    }
    public function cache($text, $nameFile, $ext = '.json')
    {
        $path = $this->pathCache;
        $fullPath = "{$path}" . ID_COMPANY . "_{$nameFile}{$ext}";
        if ($ext == '.json') {
            $text = json_encode($text, $this->optJson);
        }
        file_put_contents($fullPath, $text, LOCK_EX);
    }
    public function get_cache($nameFile, $ext = '.json')
    {
        $path = $this->pathCache;
        $fullPath = "{$path}" . ID_COMPANY . "_{$nameFile}{$ext}";
        if (file_exists($fullPath)) {
            $text = file_get_contents($fullPath);
            if ($ext == '.json') {
                $text = json_decode($text, true);
            }
            return $text;
        }
        return false;
    }
    function delete($ext, $days = 1)
    {
        $path = $this->path;
        $files = glob($path . '*.' . $ext);
        $now = time();
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * 24 * $days) {
                    unlink($file);
                }
            }
        }
    }
    function tz($tz = 'America/Argentina/Buenos_Aires')
    {
        return date_default_timezone_set($tz);
    }
    function tzLang($tzLang = "es_ES")
    {
        return setlocale(LC_TIME, $tzLang);
    }
    function dateTimeNow()
    {
        $this->tz(); // Llama al método tz() usando $this->
        $t = date("Y-m-d H:i:s");
        return $t;
    }
}
