<?php
namespace Classes;

use Classes\Response;
use Classes\Log;
use Classes\ConnectSqlSrv;
use Classes\InputValidator;
use Classes\RRHHWebService;
use Classes\Tools;
use Classes\ParaGene;
use Flight;

class Fichadas
{
    private $resp;
    private $request;
    private $getData;
    private $query;

    private $log;
    private $conect;
    private $webservice;
    private $tools;
    private $paragene;

    function __construct()
    {
        $this->resp = new Response();
        $this->request = Flight::request();
        $this->getData = $this->request->data->getData();
        $this->query = $this->request->query->getData();
        $this->log = new Log();
        $this->conect = new ConnectSqlSrv();
        $this->webservice = new RRHHWebService();
        $this->tools = new Tools();
        $this->paragene = new ParaGene();
    }

    public function create()
    {
        $conn = $this->conect->check_connection();
        $inicio = microtime(true);

        $datos = $this->validateInput();

        $cerrado = $this->check_cierre($datos['Fecha'], $datos['Legajo'], $conn);
        if ($cerrado) {
            throw new \Exception('El periodo esta cerrado', 400);
        }

        $identificador = $this->get_identifica($datos['Legajo'], $conn);

        if (!$identificador) {
            throw new \Exception('No se encontró el identificador para el legajo.', 400);
        }

        $existeFichada = $this->check_fichada($identificador, $datos['Fecha'], $datos['Hora'], $conn);

        $dbData = $this->paragene->dbData(true);
        $systemVer = $dbData['SystemVer'] ?? null;

        $RegFech = date('Ymd', strtotime($datos['Fecha']));
        $RegFeAs = date('Ymd', strtotime($datos['FeAs']));
        $RegHora = $datos['Hora'];
        $RegTipo = '1';
        $RegFeRe = $RegFech;
        $RegHoRe = $datos['HoRe'];
        $RegTran = '1';
        $RegSect = '0';
        $RegRelo = '0';
        $RegLect = '0';
        $RegLega = $datos['Legajo'];

        $params = [
            $identificador,
            $RegLega,
            $RegTipo,
            $RegFech,
            $RegFeAs,
            $RegHora,
            $RegFeRe,
            $RegHoRe,
            $RegTran,
            $RegSect,
            $RegRelo,
            $RegLect
        ];

        if ($existeFichada) {
            $setUpdate = $this->update_fichada($params, $systemVer, $conn);
        } else {
            $setInsert = $this->insert_fichada($params, $systemVer, $conn);
        }

        $this->webservice->procesar_legajos([$RegLega], $datos['FeAs'], $datos['FeAs']);
        sleep(1);
        $this->resp->respuesta(['setUpdate' => $setUpdate ?? false, 'setInsert' => $setInsert ?? false], 0, 'OK', 200, $inicio, 0, 0);
    }
    private function validateInput()
    {
        $datos = $this->getData;

        $rules = [
            'Legajo' => ['required', 'int'],
            'Fecha' => ['required', 'date'],
            'FeAs' => ['date'],
            'Hora' => ['required', 'time'],
            'HoRe' => ['time'],
        ];

        $customValueKey = [
            'Legajo' => '',
            'Fecha' => '',
            'FeAs' => '',
            'Hora' => '',
            'HoRe' => '',
        ];

        if (empty($datos['HoRe'])) {
            $datos['HoRe'] = $datos['Hora'];
        }
        if (empty($datos['FeAs'])) {
            $datos['FeAs'] = $datos['Fecha'];
        }

        if (strtotime($datos['FeAs']) > strtotime($datos['Fecha'])) {
            throw new \Exception('La fecha de asignación no puede ser mayor que la fecha de registro.', 400);
        }
        // FeAs no puede ser menor a fecha -1 día.
        if (strtotime($datos['FeAs']) < strtotime($datos['Fecha']) - 86400) {
            throw new \Exception('La fecha de asignación no puede ser 1 día menor a la fecha de registro.', 400);
        }

        $datos = $this->tools->validar_datos($datos, $rules, $customValueKey, 'validar_input_fichadas');
        return $datos;
    }

    private function check_cierre($Fecha, $Legajo, $conn = null)
    {
        $conn = $this->conect->check_connection($conn);

        $query = "SELECT TOP 1 CierreFech FROM PERCIERRE WHERE PERCIERRE.CierreLega = :Legajo";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':Legajo', $Legajo, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC) ?? [];
        $CierreFech = ($result['CierreFech'] ?? null) ? strtotime($result['CierreFech']) : strtotime('17530101');
        $PERCIERRE = strtotime($Fecha) <= $CierreFech; // si retorna false esta ok.
        if ($PERCIERRE) {
            return true;
        }
        $query = "SELECT ParCierr FROM PARACONT WHERE ParCodi = 0 ORDER BY ParCodi";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC) ?? [];
        $ParCierr = ($result['ParCierr'] ?? null) ? strtotime($result['ParCierr']) : strtotime('17530101');
        $PARCIERRE = strtotime($Fecha) <= $ParCierr; // si retorna false esta ok.
        if ($PARCIERRE) {
            return true;
        }
        return false;
    }
    private function get_identifica($Legajo, $conn = null)
    {
        $conn = $this->conect->check_connection($conn);
        $sql = "SELECT TOP 1 IDCodigo FROM IDENTIFICA WHERE IDAsigna = '1' AND IDLegajo = :Legajo AND IDFichada = '1' ORDER BY IDCodigo";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':Legajo', $Legajo, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC) ?? [];
        return $result['IDCodigo'] ?? null;
    }
    private function check_fichada($Identificador, $Fecha, $Hora, $conn = null)
    {
        $sql = "SELECT TOP 1 REGISTRO.RegTarj FROM REGISTRO WHERE RegTarj = :Identificador and RegFech = :Fecha and RegHora = :Hora ORDER BY RegTarj,RegFech,RegHora";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':Identificador', $Identificador, \PDO::PARAM_INT);
        $stmt->bindParam(':Fecha', $Fecha, \PDO::PARAM_STR);
        $stmt->bindParam(':Hora', $Hora, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC) ?? [];
        return !empty($result['RegTarj']);
    }
    private function update_fichada($params, $systemVer, $conn = null)
    {
        [$identificador, $RegLega, $RegTipo, $RegFech, $RegFeAs, $RegHora, $RegFeRe, $RegHoRe, $RegTran, $RegSect, $RegRelo, $RegLect] = $params;

        $sql = "UPDATE REGISTRO SET 
            RegTipo = :RegTipo,
            RegLega = :RegLega,
            RegFeAs = :RegFeAs,
            RegFeRe = :RegFeRe,
            RegHoRe = :RegHoRe,
            RegTran = :RegTran,
            RegSect = :RegSect,
            RegRelo = :RegRelo,
            RegLect = :RegLect,
            FechaHora = getdate()";
        $sql .= $systemVer >= 70 ? ", RegUsua='API'" : "";
        $sql .= " WHERE RegTarj = :RegTarj AND RegFech = :RegFech AND RegHora = :RegHora";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':RegTarj', $identificador, \PDO::PARAM_INT);
        $stmt->bindParam(':RegTipo', $RegTipo, \PDO::PARAM_STR);
        $stmt->bindParam(':RegLega', $RegLega, \PDO::PARAM_INT);
        $stmt->bindParam(':RegFeAs', $RegFeAs, \PDO::PARAM_STR);
        $stmt->bindParam(':RegFeRe', $RegFeRe, \PDO::PARAM_STR);
        $stmt->bindParam(':RegHoRe', $RegHoRe, \PDO::PARAM_STR);
        $stmt->bindParam(':RegTran', $RegTran, \PDO::PARAM_STR);
        $stmt->bindParam(':RegSect', $RegSect, \PDO::PARAM_STR);
        $stmt->bindParam(':RegRelo', $RegRelo, \PDO::PARAM_STR);
        $stmt->bindParam(':RegLect', $RegLect, \PDO::PARAM_STR);
        $stmt->bindParam(':RegFech', $RegFech, \PDO::PARAM_STR);
        $stmt->bindParam(':RegHora', $RegHora, \PDO::PARAM_STR);
        $stmt->execute();
        $affectedRows = $stmt->rowCount();
        if ($affectedRows === 0) {
            throw new \Exception('No se pudo actualizar la fichada.', 400);
        }
        return true;

    }
    private function insert_fichada($params, $systemVer, $conn = null)
    {
        [$identificador, $RegLega, $RegTipo, $RegFech, $RegFeAs, $RegHora, $RegFeRe, $RegHoRe, $RegTran, $RegSect, $RegRelo, $RegLect] = $params;

        $sql = "INSERT INTO REGISTRO (RegTarj, RegFech, RegHora, RegTipo, RegLega, RegFeAs, RegFeRe, RegHoRe, RegTran, RegSect, RegRelo, RegLect, FechaHora";
        if ($systemVer >= 70) {
            $sql .= ", RegUsua";
        }
        $sql .= ") VALUES (:RegTarj, :RegFech, :RegHora, :RegTipo, :RegLega, :RegFeAs, :RegFeRe, :RegHoRe, :RegTran, :RegSect, :RegRelo, :RegLect, getdate()";
        if ($systemVer >= 70) {
            $sql .= ", 'API'";
        }
        $sql .= ")";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':RegTarj', $identificador, \PDO::PARAM_INT);
        $stmt->bindParam(':RegTipo', $RegTipo, \PDO::PARAM_STR);
        $stmt->bindParam(':RegLega', $RegLega, \PDO::PARAM_INT);
        $stmt->bindParam(':RegFeAs', $RegFeAs, \PDO::PARAM_STR);
        $stmt->bindParam(':RegFeRe', $RegFeRe, \PDO::PARAM_STR);
        $stmt->bindParam(':RegHoRe', $RegHoRe, \PDO::PARAM_STR);
        $stmt->bindParam(':RegTran', $RegTran, \PDO::PARAM_STR);
        $stmt->bindParam(':RegSect', $RegSect, \PDO::PARAM_STR);
        $stmt->bindParam(':RegRelo', $RegRelo, \PDO::PARAM_STR);
        $stmt->bindParam(':RegLect', $RegLect, \PDO::PARAM_STR);
        $stmt->bindParam(':RegFech', $RegFech, \PDO::PARAM_STR);
        $stmt->bindParam(':RegHora', $RegHora, \PDO::PARAM_STR);
        $stmt->execute();
        $affectedRows = $stmt->rowCount();
        if ($affectedRows === 0) {
            throw new \Exception('No se pudo insertar la fichada.', 400);
        }
        return true;
    }

}
