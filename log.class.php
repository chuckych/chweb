<?php
class Log
{
    public function __construct($filename, $path)
    {
        $this->path     = ($path) ? $path : "/";
        $this->filename = ($filename) ? $filename : "log";
        $this->date     = date("Ymd");
        $this->ip       = ($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 0;
        $this->otro=date('d/m/Y H:i:s').' ';
    }
    public function insert($text, $dated, $clear, $backup)
    {
        if ($dated) {
            $date   = "_" . str_replace(" ", "_", $this->date);
            $append = null;
        } else {
            $date   = "";
            $append = ($clear) ? null : FILE_APPEND;
            if ($backup) {
                $result = (copy($this->path . $this->filename . ".log", $this->path . $this->filename . "_" . str_replace(" ", "_", $this->date) . "-backup.log")) ? 1 : 0;
                $append = ($result) ? $result : FILE_APPEND;
            }
        };
        $log    = $this->otro . "" . $text .PHP_EOL;
        $result = (file_put_contents($this->path . $this->filename . $date . ".log", $log, $append)) ? 1 : 0;

        return $result;
    }
}
