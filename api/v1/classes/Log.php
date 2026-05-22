<?php

namespace Classes;

class Log
{
    private $path;
    private $pathCache;
    private $date;
    private $optJson;
    public function __construct()
    {
        $this->path = __DIR__ . '/../logs/';
        $this->pathCache = __DIR__ . '/../cache/';
        $this->date = $this->dateTimeNow();
        $this->optJson = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT;
    }
    public function write($text, $nameFile, $type = false)
    {
        // obtener la extension de $nameFile
        $textOriginal = $text;
        $ext = pathinfo($nameFile, PATHINFO_EXTENSION);
        $path = $this->path;
        $date = $this->date;
        $text = ($type == 'export') ? $text . "\n" : $date . ' ' . $text . "\n";
        $text = ($ext == 'sql') ? "-- " . $date . "\n" . $textOriginal . ';' . "\n" : $text;
        if (!is_dir($path))
            mkdir($path, 0777, true);
        file_put_contents($path . $nameFile, $text, FILE_APPEND);
    }
    public function cache($text, $nameFile, $ext = '.json')
    {
        $path = $this->pathCache;
        $fullPath = "{$path}" . ID_COMPANY . "_{$nameFile}{$ext}";
        if (!is_dir($path))
            mkdir($path, 0777, true);
        if ($ext == '.json') {
            $text = json_encode($text, $this->optJson);
        }
        file_put_contents($fullPath, $text);
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
    public function delete($ext, $days = 1)
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
    public function tz($tz = 'America/Argentina/Buenos_Aires')
    {
        return date_default_timezone_set($tz);
    }
    public function tzLang($tzLang = "es_ES")
    {
        return setlocale(LC_TIME, $tzLang);
    }
    public function dateTimeNow()
    {
        $this->tz(); // Llama al método tz() usando $this->
        $t = date("Y-m-d H:i:s");
        return $t;
    }
    public function traceError(string $error = '')
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $file = $trace[0]['file'] ?? 'unknown file';
        $line = $trace[0]['line'] ?? 'unknown line';
        $desde = $trace[1]['file'] ?? 'unknown file';
        $desdeLine = $trace[1]['line'] ?? 'unknown line';
        $text = $error ? "Error: {$error} in {$file} on line {$line} (called from {$desde} on line {$desdeLine})" : "Error in {$file} on line {$line} (called from {$desde} on line {$desdeLine})";
        \error_log($text);
    }
    public function trace(string $error = '', $nameFile = '', $exception = null)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $file = $trace[0]['file'] ?? 'unknown file';
        $line = $trace[0]['line'] ?? 'unknown line';
        $desde = $trace[1]['file'] ?? 'unknown file';
        $desdeLine = $trace[1]['line'] ?? 'unknown line';
        $realIp = $this->returnRealIpAddress();
        $fn = $trace[1]['function'] ?? 'unknown function';
        $getMessage = $exception ? $exception->getMessage() : '';
        $strError = $exception ? 'Error: ' : '';
        $text = $error ? "{$strError}{$error} {$getMessage}\nin {$file} on line {$line}\n(called from {$desde} on line {$desdeLine})\nfn -> {$fn}()\nIP -> {$realIp}\n" : "{$strError}\nin {$file} on line {$line}\n(called from {$desde} on line {$desdeLine})\nfn -> {$fn}()\nIP -> {$realIp}\n";
        $this->write($text, $nameFile);
        if ($exception) {
            \error_log($text);
        }
    }
    private function returnRealIpAddress()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }
}
