<?php

namespace Classes;

use Classes\ConnectSqlSrv;
use Classes\Response;
use Classes\InputValidator;
use Classes\ValidationException;
use Classes\Auditor;
use Classes\Log;
use Classes\Tools;
use Flight;
use flight\net\Request;
use function ctype_digit;
use function defined;
use function count;
use function trim;

class Novedad2
{
	private ConnectSqlSrv $conect;
	private Request $request;
	private array $getData;
	private Response $resp;
	private Log $log;
	private Auditor $auditor;
	private float $inicio;
	private string $NameLog;
	private Tools $tools;

	public function __construct()
	{
		$this->conect = new ConnectSqlSrv();
		$this->resp = new Response();
		$this->log = new Log();
		$this->auditor = new Auditor();
		$this->tools = new Tools();
		$this->request = Flight::request();
		$this->getData = $this->request->data->getData();
		$this->NameLog = date('Ymd') . '_novedad2.log';
	}

	public function create(): void
	{
		$this->inicio = microtime(true);
		$idCompany = defined('ID_COMPANY') ? ID_COMPANY : 0;

		$items = $this->normalizeItems($this->getData);

		if ($this->tools->jsonNoValido()) {
			throw new \Exception('JSON no valido', 400);
		}

		if (empty($items)) {
			throw new \Exception('No se recibieron datos', 400);
		}

		$insertados = [];
		$actualizados = [];
		$errores = [];
		$conn = $this->conect->conn();

		try {
			$conn->beginTransaction();

			foreach ($items as $item) {
				$audUser = isset($item['User']) && $item['User'] !== '' ? $item['User'] : '';

				try {
					$item = $this->validateItemBase($item);
					$this->validateNovedadAusenciaReference((int) $item['NovCodi'], $conn);
					$this->validateTipoHoraReference((int) $item['NovHora'], $conn);

					$exists = $this->existeNovedad2((int) $item['NovCodi'], (int) $item['NovHora'], $conn);

					if ($exists) {
						$item = $this->mergeWithExistingNovedad2($item, $conn);
						$item = $this->validateItem($item);
						unset($item['User']);
						$this->updateNovedad2($item, $conn);
						$this->auditor->add([['AudUser' => $audUser, 'AudTipo' => 'M', 'AudDato' => 'Novedad2 ' . $item['NovCodi'] . '/' . $item['NovHora']]]);
						$actualizados[] = [
							'NovCodi' => $item['NovCodi'],
							'NovHora' => $item['NovHora'],
						];
						continue;
					}

					$item = $this->applyDefaults($item);
					$item = $this->validateItem($item);
					unset($item['User']);
					$this->insertNovedad2($item, $conn);
					$this->auditor->add([['AudUser' => $audUser, 'AudTipo' => 'A', 'AudDato' => 'Novedad2 ' . $item['NovCodi'] . '/' . $item['NovHora']]]);
					$insertados[] = [
						'NovCodi' => $item['NovCodi'],
						'NovHora' => $item['NovHora'],
					];
				} catch (ValidationException $e) {
					$errores[] = [
						'NovCodi' => $item['NovCodi'] ?? null,
						'NovHora' => $item['NovHora'] ?? null,
						'error' => $e->getMessage(),
					];
				}
			}

			if (empty($insertados) && empty($actualizados)) {
				$conn->rollBack();
				$this->resp->respuesta(
					['errores' => $errores],
					0,
					'No se proceso ningun registro',
					400,
					$this->inicio,
					count($items),
					$idCompany
				);
				return;
			}

			$conn->commit();
			$this->resp->respuesta(
				['insertados' => $insertados, 'actualizados' => $actualizados, 'errores' => $errores],
				count($insertados) + count($actualizados),
				'OK',
				200,
				$this->inicio,
				count($items),
				$idCompany
			);
		} catch (\PDOException $e) {
			if ($conn->inTransaction()) {
				$conn->rollBack();
			}
			$this->log->trace('Novedad2::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw new \Exception('Error al crear novedad2', 400);
		} catch (\Exception $e) {
			if ($conn->inTransaction()) {
				$conn->rollBack();
			}
			$this->log->trace('Novedad2::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw $e;
		}
	}

	public function delete(string $novCodi, string $novHora): void
	{
		$this->inicio = microtime(true);
		$idCompany = defined('ID_COMPANY') ? ID_COMPANY : 0;
		$audUser = isset($this->getData['User']) && $this->getData['User'] !== '' ? (string) $this->getData['User'] : '';

		$novCodiRaw = trim($novCodi);
		$novHoraRaw = trim($novHora);

		if ($novCodiRaw === '' || !ctype_digit($novCodiRaw) || $novHoraRaw === '' || !ctype_digit($novHoraRaw)) {
			throw new ValidationException('NovCodi y NovHora deben ser numericos');
		}

		$novCodiInt = (int) $novCodiRaw;
		$novHoraInt = (int) $novHoraRaw;

		if ($novCodiInt <= 0 || $novHoraInt <= 0 || $novCodiInt > 32767 || $novHoraInt > 32767) {
			throw new ValidationException('NovCodi y NovHora deben estar en rango smallint y ser mayores a 0');
		}

		$conn = $this->conect->conn();

		try {
			$conn->beginTransaction();

			$this->validateNovedadAusenciaReference($novCodiInt, $conn);
			$this->validateTipoHoraReference($novHoraInt, $conn);
			$deleted = $this->deleteNovedad2ByKey($novCodiInt, $novHoraInt, $conn);

			if ($deleted === 0) {
				throw new ValidationException('No existe el registro indicado');
			}

			$this->auditor->add([['AudUser' => $audUser, 'AudTipo' => 'B', 'AudDato' => 'Novedad2 ' . $novCodiInt . '/' . $novHoraInt]]);
			$conn->commit();

			$this->resp->respuesta(
				['NovCodi' => $novCodiInt, 'NovHora' => $novHoraInt],
				1,
				'OK',
				200,
				$this->inicio,
				1,
				$idCompany
			);
		} catch (ValidationException $e) {
			if ($conn->inTransaction()) {
				$conn->rollBack();
			}
			throw $e;
		} catch (\PDOException $e) {
			if ($conn->inTransaction()) {
				$conn->rollBack();
			}
			$this->log->trace('Novedad2::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw new \Exception('Error al eliminar novedad2', 400);
		} catch (\Exception $e) {
			if ($conn->inTransaction()) {
				$conn->rollBack();
			}
			$this->log->trace('Novedad2::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw $e;
		}
	}

	private function normalizeItems(array $payload): array
	{
		if (isset($payload[0]) && is_array($payload[0])) {
			return $payload;
		}

		return empty($payload) ? [] : [$payload];
	}

	private function applyDefaults(array $item): array
	{
		$defaults = [
			'NovFact' => '1',
			'User' => 'API',
		];

		foreach ($defaults as $key => $default) {
			if (!isset($item[$key]) || $item[$key] === '') {
				$item[$key] = $default;
			}
		}

		return $item;
	}

	private function validateItemBase(array $item): array
	{
		$rules = [
			'NovCodi' => ['required', 'smallint'],
			'NovHora' => ['required', 'smallint'],
			'User' => ['varchar100'],
		];

		(new InputValidator($item, $rules))->validate();
		return $item;
	}

	private function validateItem(array $item): array
	{
		$rules = [
			'NovCodi' => ['required', 'smallint'],
			'NovHora' => ['required', 'smallint'],
			'User' => ['varchar100'],
		];

		(new InputValidator($item, $rules))->validate();
		$this->validateNovFact($item['NovFact'] ?? null);
		return $item;
	}

	private function validateNovFact($novFact): void
	{
		if ($novFact === null || $novFact === '') {
			throw new ValidationException('NovFact es requerido y debe ser mayor a 0');
		}

		$novFactStr = trim((string) $novFact);
		if (!is_numeric($novFactStr)) {
			throw new ValidationException('NovFact debe ser numerico, mayor a 0 y con hasta 4 decimales');
		}

		$factor = (float) $novFactStr;
		if ($factor <= 0) {
			throw new ValidationException('NovFact debe ser mayor a 0');
		}

		if (!preg_match('/^\d{1,6}(\.\d{1,4})?$/', $novFactStr)) {
			throw new ValidationException('NovFact debe tener hasta 10 digitos totales y 4 decimales');
		}
	}

	private function mergeWithExistingNovedad2(array $item, \PDO $conn): array
	{
		$sql = 'SELECT * FROM NOVEDAD2 WHERE NovCodi = :NovCodi AND NovHora = :NovHora';
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', (int) $item['NovCodi'], \PDO::PARAM_INT);
		$stmt->bindValue(':NovHora', (int) $item['NovHora'], \PDO::PARAM_INT);
		$stmt->execute();

		$existing = $stmt->fetch(\PDO::FETCH_ASSOC);
		if ($existing === false) {
			throw new ValidationException('No existe el registro indicado');
		}

		$merged = array_merge($existing, $item);
		$merged['User'] = $item['User'] ?? '';

		return $merged;
	}

	private function validateNovedadAusenciaReference(int $novCodi, \PDO $conn): void
	{
		$sql = 'SELECT TOP 1 NovCodi FROM NOVEDAD WHERE NovCodi = :NovCodi AND NovTipo >= 3';
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', $novCodi, \PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt->fetchColumn() === false) {
			throw new ValidationException('NovCodi debe existir en NOVEDAD y ser de tipo ausencia (NovTipo >= 3)');
		}
	}

	private function validateTipoHoraReference(int $novHora, \PDO $conn): void
	{
		$sql = 'SELECT TOP 1 THoCodi FROM TIPOHORA WHERE THoCodi = :NovHora AND THoCodi > 0';
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovHora', $novHora, \PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt->fetchColumn() === false) {
			throw new ValidationException('NovHora debe existir en TIPOHORA');
		}
	}

	private function existeNovedad2(int $novCodi, int $novHora, \PDO $conn): bool
	{
		$sql = 'SELECT COUNT(*) FROM NOVEDAD2 WHERE NovCodi = :NovCodi AND NovHora = :NovHora';
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', $novCodi, \PDO::PARAM_INT);
		$stmt->bindValue(':NovHora', $novHora, \PDO::PARAM_INT);
		$stmt->execute();
		return (int) $stmt->fetchColumn() > 0;
	}

	private function insertNovedad2(array $data, \PDO $conn): void
	{
		$sql = 'INSERT INTO NOVEDAD2 (NovCodi, NovHora, NovFact, FechaHora)
			VALUES (:NovCodi, :NovHora, :NovFact, getdate())';
		$stmt = $conn->prepare($sql);
		$this->bindNovedad2Params($stmt, $data);
		$stmt->execute();
	}

	private function updateNovedad2(array $data, \PDO $conn): void
	{
		$sql = 'UPDATE NOVEDAD2 SET
			NovFact = :NovFact,
			FechaHora = getdate()
			WHERE NovCodi = :NovCodi AND NovHora = :NovHora';
		$stmt = $conn->prepare($sql);
		$this->bindNovedad2Params($stmt, $data);
		$stmt->execute();
	}

	private function deleteNovedad2ByKey(int $novCodi, int $novHora, \PDO $conn): int
	{
		$sql = 'DELETE FROM NOVEDAD2 WHERE NovCodi = :NovCodi AND NovHora = :NovHora';
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', $novCodi, \PDO::PARAM_INT);
		$stmt->bindValue(':NovHora', $novHora, \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->rowCount();
	}

	private function bindNovedad2Params(\PDOStatement $stmt, array $data): void
	{
		$stmt->bindValue(':NovCodi', (int) ($data['NovCodi'] ?? 0), \PDO::PARAM_INT);
		$stmt->bindValue(':NovHora', (int) ($data['NovHora'] ?? 0), \PDO::PARAM_INT);
		$stmt->bindValue(':NovFact', (string) ($data['NovFact'] ?? '1'), \PDO::PARAM_STR);
	}

	public function get(string $novCodi): void
	{

		$this->inicio = microtime(true);
		$idCompany = defined('ID_COMPANY') ? ID_COMPANY : 0;

		$novCodiRaw = trim($novCodi);
		if ($novCodiRaw === '' || !ctype_digit($novCodiRaw)) {
			throw new ValidationException('NovCodi debe ser numerico');
		}

		$novCodiInt = (int) $novCodiRaw;
		if ($novCodiInt <= 0) {
			throw new ValidationException('NovCodi debe ser mayor a 0');
		}

		if ($novCodiInt > 32767) {
			throw new ValidationException('NovCodi esta fuera del rango smallint');
		}

		$conn = $this->conect->conn();

		try {

			$this->validateNovCodiReference($novCodiInt, $conn);

			$sql = 'SELECT N2.NovCodi, N2.NovHora, TH.THoDesc AS "NovAdicHoDesc", N2.NovFact, N2.FechaHora
			FROM NOVEDAD2 N2
			INNER JOIN TIPOHORA TH ON N2.NovHora = TH.THoCodi
			WHERE N2.NovCodi = :NovCodi';

			$stmt = $conn->prepare($sql);
			$stmt->bindValue(':NovCodi', $novCodiInt, \PDO::PARAM_INT);
			$stmt->execute();

			$data = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
			$count = count($data);
			
			// Asegurarse de que NovFact tenga un 0 delante si comienza con un punto, por ejemplo ".5000" se convierte en "0.5000"
			foreach ($data as &$row) {
				if (isset($row['NovFact']) && strpos($row['NovFact'], '.') === 0) {
					$row['NovFact'] = '0' . $row['NovFact'];
				}
			}

			$this->resp->respuesta(
				$data,
				$count,
				'OK',
				200,
				$this->inicio,
				$count,
				$idCompany
			);

		} catch (ValidationException $e) {
			throw $e;
		} catch (\PDOException $e) {
			$this->log->trace('Novedad2::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw new \Exception('Error al obtener novedad2', 400);
		} catch (\Exception $e) {
			$this->log->trace('Novedad2::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw $e;
		}
	}

	private function validateNovCodiReference(int $NovCodi, \PDO $conn): void
	{
		$sql = "SELECT TOP 1 NovCodi FROM NOVEDAD WHERE NovCodi = :NovCodi";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', $NovCodi, \PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt->fetchColumn() === false) {
			throw new ValidationException('NovCodi no existe en NOVEDAD');
		}
	}
}
