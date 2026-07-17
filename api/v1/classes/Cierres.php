<?php

namespace Classes;

use Classes\ConnectSqlSrv;
use Classes\Response;
use Classes\InputValidator;
use Classes\ValidationException;
use Classes\Auditor;
use Classes\Log;
use Flight;
use flight\net\Request;
use function count;

class Cierres
{
	private ConnectSqlSrv $conect;
	private Request $request;
	private array $getData;
	private Response $resp;
	private Log $log;
	private Auditor $auditor;
	private float $inicio;
	private string $NameLog;

	public function __construct()
	{
		$this->conect = new ConnectSqlSrv;
		$this->resp = new Response();
		$this->log = new Log();
		$this->auditor = new Auditor();
		$this->request = Flight::request();
		$this->getData = $this->request->data->getData();
		$this->NameLog = date('Ymd') . '_cierres.log';
	}

	public function generate(): void
	{
		$this->inicio = microtime(true);
		$idCompany = \defined('ID_COMPANY') ? ID_COMPANY : 0;

		$payload = $this->getData;
		$payload['Eliminar'] = isset($payload['Eliminar']) && $payload['Eliminar'] !== '' ? (string) $payload['Eliminar'] : '0';

		$rules = [
			'Legajos' => ['required', 'arrInt'],
			'Fecha' => ['required', 'date'],
			'Eliminar' => ['allowed01'],
			'User' => ['varchar100'],
		];

		try {
			(new InputValidator($payload, $rules))->validate();
		} catch (ValidationException $e) {
			$this->resp->respuesta([], 0, $e->getMessage(), 400, $this->inicio, 0, $idCompany);
			return;
		}

		$conn = $this->conect->conn();
		$fechaHora = (new ConnectSqlSrv())->FechaHora();
		$fechaCierre = $this->toSqlDateStart((string) $payload['Fecha']);
		$humanFechaCierre = date('d/m/Y', strtotime((string) $payload['Fecha']));
		$fechaReset = '1753-01-01T00:00:00.000';
		$humanFechaReset = '01/01/1753';
		$isDelete = $payload['Eliminar'] === '1';
		$audUser = isset($payload['User']) && $payload['User'] !== '' ? $payload['User'] : '';

		$insertados = [];
		$actualizados = [];
		$omitidos = [];
		$errores = [];
		$auditItems = [];

		$legajosValidos = [];
		foreach ($payload['Legajos'] as $legajoRaw) {
			$legajo = (int) $legajoRaw;
			if ($legajo <= 0) {
				$errores[] = [
					'Legajo' => $legajoRaw,
					'error' => 'El legajo debe ser mayor a 0',
				];
				continue;
			}
			$legajosValidos[$legajo] = $legajo;
		}

		$legajosValidos = array_values($legajosValidos);

		if (empty($legajosValidos)) {
			$this->resp->respuesta(
				['insertados' => [], 'actualizados' => [], 'omitidos' => [], 'errores' => $errores],
				0,
				'No se procesó ningún registro',
				400,
				$this->inicio,
				\count($payload['Legajos']),
				$idCompany
			);
			return;
		}

		try {
			$conn->beginTransaction();

			$legajosExistentes = $this->getExistingLegajos($legajosValidos, $conn);
			$existentesMap = array_flip($legajosExistentes);

			$legajosActualizar = $legajosExistentes;
			$legajosInsertar = [];

			if ($isDelete) {
				foreach ($legajosValidos as $legajo) {
					if (!isset($existentesMap[$legajo])) {
						$omitidos[] = [
							'Legajo' => $legajo,
							'motivo' => 'No existe cierre para resetear',
						];
					}
				}

				if (!empty($legajosActualizar)) {
					$this->updateCierresMasivo($legajosActualizar, $fechaReset, $fechaHora, $conn);
					foreach ($legajosActualizar as $legajo) {
						$actualizados[] = [
							'Legajo' => $legajo,
							'Fecha' => $humanFechaReset,
						];
						// $auditItems[] = [
						// 	'AudUser' => $audUser,
						// 	'AudTipo' => 'M',
						// 	'AudDato' => "Cierre reset legajo $legajo",
						// ];
					}
					$countActualizados = count($legajosActualizar);
					$auditItems[] = [
						'AudUser' => $audUser,
						'AudTipo' => 'B',
						'AudDato' => "Cierre de $countActualizados legajos. Fecha $humanFechaReset",
					];
				}
			} else {
				foreach ($legajosValidos as $legajo) {
					if (!isset($existentesMap[$legajo])) {
						$legajosInsertar[] = $legajo;
					}
				}

				if (!empty($legajosActualizar)) {
					$this->updateCierresMasivo($legajosActualizar, $fechaCierre, $fechaHora, $conn);
					foreach ($legajosActualizar as $legajo) {
						$actualizados[] = [
							'Legajo' => $legajo,
							'Fecha' => $humanFechaCierre,
						];
						// $auditItems[] = [
						// 	'AudUser' => $audUser,
						// 	'AudTipo' => 'M',
						// 	'AudDato' => "Cierre legajo $legajo",
						// ];
					}
					$countActualizados = count($legajosActualizar);
					$auditItems[] = [
						'AudUser' => $audUser,
						'AudTipo' => 'M',
						'AudDato' => "Cierre de $countActualizados legajos. Fecha $humanFechaCierre",
					];
				}

				if (!empty($legajosInsertar)) {
					$this->insertCierresMasivo($legajosInsertar, $fechaCierre, $fechaHora, $conn);
					foreach ($legajosInsertar as $legajo) {
						$insertados[] = [
							'Legajo' => $legajo,
							'Fecha' => $humanFechaCierre,
						];
						// $auditItems[] = [
						// 	'AudUser' => $audUser,
						// 	'AudTipo' => 'A',
						// 	'AudDato' => "Cierre legajo $legajo",
						// ];
					}
					$countInsertados = count($legajosInsertar);
					$auditItems[] = [
						'AudUser' => $audUser,
						'AudTipo' => 'A',
						'AudDato' => "Cierre de $countInsertados legajos. Fecha $humanFechaCierre",
					];
				}
			}

			$procesados = count($insertados) + count($actualizados);

			if ($procesados === 0) {
				$conn->rollBack();
				$this->resp->respuesta(
					['insertados' => $insertados, 'actualizados' => $actualizados, 'omitidos' => $omitidos, 'errores' => $errores],
					0,
					'No se procesó ningún registro',
					400,
					$this->inicio,
					\count($payload['Legajos']),
					$idCompany
				);
				return;
			}

			$conn->commit();

			if (!empty($auditItems)) {
				$this->auditor->add($auditItems);
			}

			$this->resp->respuesta(
				['insertados' => $insertados, 'actualizados' => $actualizados, 'omitidos' => $omitidos, 'errores' => $errores],
				$procesados,
				'OK',
				200,
				$this->inicio,
				\count($payload['Legajos']),
				$idCompany
			);
		} catch (\PDOException $e) {
			if ($conn->inTransaction()) {
				$conn->rollBack();
			}
			$this->log->trace('Cierres::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw new \Exception('Error al generar cierres', 400);
		} catch (\Exception $e) {
			if ($conn->inTransaction()) {
				$conn->rollBack();
			}
			$this->log->trace('Cierres::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw $e;
		}
	}

	private function getExistingLegajos(array $legajos, \PDO $conn): array
	{
		$existentes = [];
		foreach (array_chunk($legajos, 500) as $chunk) {
			$params = [];
			$inClause = $this->buildInClause($chunk, 'sel', $params);
			$sql = "SELECT CierreLega FROM PERCIERRE WHERE CierreLega IN ($inClause)";
			$stmt = $conn->prepare($sql);
			foreach ($params as $name => $value) {
				$stmt->bindValue($name, $value, \PDO::PARAM_INT);
			}
			$stmt->execute();
			$rows = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0) ?: [];
			foreach ($rows as $row) {
				$existentes[(int) $row] = (int) $row;
			}
		}

		return array_values($existentes);
	}

	private function updateCierresMasivo(array $legajos, string $fechaCierre, string $fechaHora, \PDO $conn): void
	{
		foreach (array_chunk($legajos, 500) as $chunk) {
			// $inicio = microtime(true);
			$params = [];
			$inClause = $this->buildInClause($chunk, 'upd', $params);
			$sql = "UPDATE PERCIERRE SET CierreFech = :CierreFech, FechaHora = :FechaHora WHERE CierreLega IN ($inClause)";
			$stmt = $conn->prepare($sql);
			$stmt->bindValue(':CierreFech', $fechaCierre, \PDO::PARAM_STR);
			$stmt->bindValue(':FechaHora', $fechaHora, \PDO::PARAM_STR);
			foreach ($params as $name => $value) {
				$stmt->bindValue($name, $value, \PDO::PARAM_INT);
			}
			$stmt->execute();
			// $fin = microtime(true);
			// $tiempo = round($fin - $inicio, 2);
			// error_log("updateCierresMasivo: " . count($chunk) . " registros en $tiempo segundos");
		}
	}

	private function insertCierresMasivo(array $legajos, string $fechaCierre, string $fechaHora, \PDO $conn): void
	{
		foreach (array_chunk($legajos, 500) as $chunk) {
			$valuesSql = [];
			$params = [];

			foreach ($chunk as $idx => $legajo) {
				$legParam = ":leg{$idx}";
				$fechaParam = ":cierre{$idx}";
				$horaParam = ":fh{$idx}";
				$valuesSql[] = "($legParam, $fechaParam, $horaParam)";

				$params[$legParam] = ['value' => (int) $legajo, 'type' => \PDO::PARAM_INT];
				$params[$fechaParam] = ['value' => $fechaCierre, 'type' => \PDO::PARAM_STR];
				$params[$horaParam] = ['value' => $fechaHora, 'type' => \PDO::PARAM_STR];
			}

			$sql = 'INSERT INTO PERCIERRE (CierreLega, CierreFech, FechaHora) VALUES ' . implode(',', $valuesSql);
			$stmt = $conn->prepare($sql);

			foreach ($params as $name => $paramData) {
				$stmt->bindValue($name, $paramData['value'], $paramData['type']);
			}
			$stmt->execute();
		}
	}

	private function buildInClause(array $legajos, string $prefix, array &$params): string
	{
		$placeholders = [];
		foreach ($legajos as $idx => $legajo) {
			$key = ':' . $prefix . $idx;
			$placeholders[] = $key;
			$params[$key] = (int) $legajo;
		}

		return implode(',', $placeholders);
	}

	private function toSqlDateStart(string $fecha): string
	{
		$normalizada = str_replace('/', '-', trim($fecha));

		if (preg_match('/^\d{8}$/', $normalizada)) {
			$normalizada = substr($normalizada, 0, 4) . '-' . substr($normalizada, 4, 2) . '-' . substr($normalizada, 6, 2);
		}
		return "{$normalizada}T00:00:00.000";
	}
}
