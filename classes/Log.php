<?php

class Log
{
    private $path;
    private $date;
    function __construct()
    {
        $this->path = __DIR__ . '/../logs/';
        $this->date = $this->dateTimeNow();
    }
    function write($text, $nameFile, $type = false)
    {
        $path = $this->path;
        $date = $this->date;
        $text = ($type == 'export') ? $text . "\n" : $date . ' ' . $text . "\n";
        file_put_contents($path . $nameFile, $text, FILE_APPEND | LOCK_EX);
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
