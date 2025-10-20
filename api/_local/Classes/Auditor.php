<?php
namespace Classes;

use Classes\Log;


class Auditor
{
    private $log;

    public function __construct()
    {
        $this->log = new Log();
    }

    public function set($arrayData, $audMod, $session, $conn)
    {
        $this->setTimeZone();

        $values = [];
        $params = [];
        $index = 0;

        try {

            foreach ($arrayData as $value) {
                $values[] = "(:id_sesion{$index} , :usuario{$index} , :nombre{$index} , :cuenta{$index} , :audcuenta{$index} , :fecha{$index} , :hora{$index} , :tipo{$index} , :dato{$index} , :modulo{$index})";

                $params[":id_sesion{$index}"] = $session['id'] ?? '';
                $params[":usuario{$index}"] = $session["usuario"] ?? 'Sin usuario';
                $params[":nombre{$index}"] = $session["usuario_nombre"] ?? 'Sin nombre';
                $params[":cuenta{$index}"] = $session["cliente_id"] ?? '';
                $params[":audcuenta{$index}"] = $session["cliente_id"] ?? '';
                $params[":fecha{$index}"] = date("Y-m-d");
                $params[":hora{$index}"] = date("H:i:s");
                $params[":tipo{$index}"] = $value['AudTipo'] ?? 'Null';
                $params[":dato{$index}"] = trim($value['AudDato']) ?? 'No se especificaron datos';
                $params[":modulo{$index}"] = $audMod ?? '';
                $index++;
            }

            $valuesList = implode(',', $values);

        } catch (\Throwable $th) { // si hay error
            $this->log->write(
                $th->getMessage(),
                '_errorAudito.log'
            );
        }
        
        $conn->beginTransaction();

        try {
            
            $sql = "INSERT INTO auditoria( id_sesion, usuario, nombre, cuenta, audcuenta, fecha, hora, tipo, dato, modulo) VALUES $valuesList";
            $stmt = $conn->prepare($sql); // prepara la consulta
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
            }
            $stmt->execute();
            $conn->commit(); // si todo salio bien, confirma la transacción
            return true;
        } catch (\Throwable $th) { // si hay error
            $conn->rollBack(); // revierte la transacción
            $this->log->write(
                $th->getMessage(),
                '_errorAudito.log'
            );
        }
        return false;
    }
    private function setTimeZone(): bool
    {
        return date_default_timezone_set('America/Argentina/Buenos_Aires');
    }
}