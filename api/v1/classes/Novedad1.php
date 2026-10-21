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
use function in_array;


class Novedad1
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
		$this->NameLog = date('Ymd') . '_novedad1.log';
	}

	public function create(): void
	{
		$this->inicio = microtime(true);
		$idCompany = defined('ID_COMPANY') ? ID_COMPANY : 0;

		$items = $this->normalizeItems($this->getData);

		if ($this->tools->jsonNoValido()) {
			throw new \Exception('JSON no válido', 400);
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
					$this->validateNovedadReference((int) $item['NovCodi'], $conn);

					$exists = $this->existeNovedad1((int) $item['NovCodi'], (int) $item['NovItem'], $conn);

					if ($exists) {
						$item = $this->mergeWithExistingNovedad1($item, $conn);
						$item = $this->applyDefaults($item);
						$item = $this->validateItem($item, false);
						$this->validateNovCoAd((string) $item['NovCoAd'], $conn);
						unset($item['User']);
						$this->updateNovedad1($item, $conn);
						$this->auditor->add([['AudUser' => $audUser, 'AudTipo' => 'M', 'AudDato' => 'Novedad1 ' . $item['NovCodi'] . '/' . $item['NovItem']]]);
						$actualizados[] = [
							'NovCodi' => $item['NovCodi'],
							'NovCoAd' => $item['NovCoAd'],
						];
						continue;
					}

					$item = $this->applyDefaults($item);
					$item = $this->validateItem($item, true);
					$this->validateNovCoAd((string) $item['NovCoAd'], $conn);

					unset($item['User']);
					$this->insertNovedad1($item, $conn);
					$this->auditor->add([['AudUser' => $audUser, 'AudTipo' => 'A', 'AudDato' => 'Novedad1 ' . $item['NovCodi'] . '/' . $item['NovItem']]]);
					$insertados[] = [
						'NovCodi' => $item['NovCodi'],
						'NovCoAd' => $item['NovCoAd'],
					];
				} catch (ValidationException $e) {
					$errores[] = [
						'NovCodi' => $item['NovCodi'] ?? null,
						'NovCoAd' => $item['NovCoAd'] ?? null,
						'error' => $e->getMessage(),
					];
				}
			}

			if (empty($insertados) && empty($actualizados)) {
				$conn->rollBack();
				$this->resp->respuesta(
					['errores' => $errores],
					0,
					'No se procesó ningún registro',
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
			$this->log->trace('Novedad1::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw new \Exception('Error al crear novedad1', 400);
		} catch (\Exception $e) {
			if ($conn->inTransaction()) {
				$conn->rollBack();
			}
			$this->log->trace('Novedad1::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw $e;
		}
	}

	public function delete(string $novCodi, string $novItem): void
	{
		$this->inicio = microtime(true);
		$idCompany = defined('ID_COMPANY') ? ID_COMPANY : 0;
		$audUser = isset($this->getData['User']) && $this->getData['User'] !== '' ? (string) $this->getData['User'] : '';

		$novCodiRaw = trim($novCodi);
		$novItemRaw = trim($novItem);

		if ($novCodiRaw === '' || !ctype_digit($novCodiRaw) || $novItemRaw === '' || !ctype_digit($novItemRaw)) {
			throw new ValidationException('NovCodi y NovItem deben ser numéricos');
		}

		$novCodiInt = (int) $novCodiRaw;
		$novItemInt = (int) $novItemRaw;

		if ($novCodiInt <= 0 || $novItemInt <= 0 || $novCodiInt > 32767 || $novItemInt > 32767) {
			throw new ValidationException('NovCodi y NovItem deben estar en rango smallint y ser mayores a 0');
		}

		$conn = $this->conect->conn();

		try {
			$conn->beginTransaction();

			$this->validateNovedadReference($novCodiInt, $conn);
			$deleted = $this->deleteNovedad1ByKey($novCodiInt, $novItemInt, $conn);

			if ($deleted === 0) {
				throw new ValidationException('No existe el registro indicado');
			}

			$this->auditor->add([['AudUser' => $audUser, 'AudTipo' => 'B', 'AudDato' => 'Novedad1 ' . $novCodiInt . '/' . $novItemInt]]);
			$conn->commit();

			$this->resp->respuesta(
				['NovCodi' => $novCodiInt, 'NovItem' => $novItemInt],
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
			$this->log->trace('Novedad1::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw new \Exception('Error al eliminar novedad1', 400);
		} catch (\Exception $e) {
			if ($conn->inTransaction()) {
				$conn->rollBack();
			}
			$this->log->trace('Novedad1::' . __FUNCTION__ . ': ', $this->NameLog, $e);
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
			'NovDia' => '0',
			'NovMeJo' => '0',
			'NovFact' => '0',
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
			'NovItem' => ['required', 'smallint'],
			'User' => ['varchar100'],
		];

		(new InputValidator($item, $rules))->validate();
		return $item;
	}

	private function validateItem(array $item, bool $requireTimes = true): array
	{
		$rules = [
			'NovCodi' => ['required', 'smallint'],
			'NovItem' => ['required', 'smallint'],
			'NovDia' => ['smallintEmpty'],
			'NovMeJo' => ['smallintEmpty'],
			'NovCoAd' => ['required', 'smallint'],
			'NovFact' => ['decima12.2'],
			'NovDesde' => $requireTimes ? ['required', 'time'] : ['timeEmpty'],
			'NovHasta' => $requireTimes ? ['required', 'time'] : ['timeEmpty'],
			'User' => ['varchar100'],
		];

		if (isset($item['NovDesde']) && $item['NovDesde'] !== '') {
			$item['NovDesde'] = trim((string) $item['NovDesde']);
		}

		if (isset($item['NovHasta']) && $item['NovHasta'] !== '') {
			$item['NovHasta'] = trim((string) $item['NovHasta']);
		}

		(new InputValidator($item, $rules))->validate();
		$this->validateNovDia(isset($item['NovDia']) ? (string) $item['NovDia'] : null);
		$this->validateNovMeJo(isset($item['NovMeJo']) ? (string) $item['NovMeJo'] : null);
		return $item;
	}

	private function mergeWithExistingNovedad1(array $item, \PDO $conn): array
	{
		$sql = 'SELECT * FROM NOVEDAD1 WHERE NovCodi = :NovCodi AND NovItem = :NovItem';
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', (int) $item['NovCodi'], \PDO::PARAM_INT);
		$stmt->bindValue(':NovItem', (int) $item['NovItem'], \PDO::PARAM_INT);
		$stmt->execute();

		$existing = $stmt->fetch(\PDO::FETCH_ASSOC);
		if ($existing === false) {
			throw new ValidationException('No existe el registro indicado');
		}

		$merged = array_merge($existing, $item);
		$merged['User'] = $item['User'] ?? '';

		return $merged;
	}

	private function validateNovDia(?string $value): void
	{
		if ($value === null || $value === '') {
			return;
		}

		$valid = range(0, 9);
		$valueInt = filter_var($value, FILTER_VALIDATE_INT);

		if ($valueInt === false || !in_array($valueInt, $valid, true)) {
			throw new ValidationException('El campo NovDia debe ser un valor permitido: ' . $this->validNovDia());
		}
	}

	private function validateNovMeJo(?string $value): void
	{
		if ($value === null || $value === '') {
			return;
		}

		$valid = range(0, 2);
		$valueInt = filter_var($value, FILTER_VALIDATE_INT);

		if ($valueInt === false || !in_array($valueInt, $valid, true)) {
			throw new ValidationException('El campo NovMeJo debe ser un valor permitido: ' . $this->validNovMeJo());
		}
	}

	private function validNovDia(): string
	{
		return '0 = Domingo, 1 = Lunes, 2 = Martes, 3 = Miércoles, 4 = Jueves, 5 = Viernes, 6 = Sábado, 7 = Feriado nacional, 8 = Feriado optativo, 9 = Feriado empresarial';
	}

	private function validNovMeJo(): string
	{
		return '0 = Todos, 1 = Mensual, 2 = Jornal';
	}

	private function validateNovedadReference(int $novCodi, \PDO $conn): void
	{
		$sql = 'SELECT TOP 1 NovCodi FROM NOVEDAD WHERE NovCodi = :NovCodi';
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', $novCodi, \PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt->fetchColumn() === false) {
			throw new ValidationException('NovCodi no existe en NOVEDAD');
		}
	}

	private function validateNovCoAd(string $novCoAd, \PDO $conn): void
	{
		$novCoAd = trim($novCoAd);
		if ($novCoAd === '') {
			return;
		}

		if (!ctype_digit($novCoAd)) {
			throw new ValidationException('NovCoAd debe ser un NovCodi válido y mayor a 0');
		}

		$novCoAdInt = (int) $novCoAd;
		if ($novCoAdInt <= 0 || $novCoAdInt > 32767) {
			throw new ValidationException('NovCoAd debe ser un NovCodi válido y mayor a 0');
		}

		$sql = 'SELECT TOP 1 NovCodi FROM NOVEDAD WHERE NovCodi = :NovCodi';
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', $novCoAdInt, \PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt->fetchColumn() === false) {
			throw new ValidationException('NovCoAd no existe en NOVEDAD');
		}
	}

	private function existeNovedad1(int $novCodi, int $novItem, \PDO $conn): bool
	{
		$sql = 'SELECT COUNT(*) FROM NOVEDAD1 WHERE NovCodi = :NovCodi AND NovItem = :NovItem';
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', $novCodi, \PDO::PARAM_INT);
		$stmt->bindValue(':NovItem', $novItem, \PDO::PARAM_INT);
		$stmt->execute();
		return (int) $stmt->fetchColumn() > 0;
	}

	private function insertNovedad1(array $data, \PDO $conn): void
	{
		$sql = 'INSERT INTO NOVEDAD1 (NovCodi, NovItem, NovDia, NovMeJo, NovCoAd, NovFact, NovDesde, NovHasta, FechaHora)
			VALUES (:NovCodi, :NovItem, :NovDia, :NovMeJo, :NovCoAd, :NovFact, :NovDesde, :NovHasta, getdate())';
		$stmt = $conn->prepare($sql);
		$this->bindNovedad1Params($stmt, $data);
		$stmt->execute();
	}

	private function updateNovedad1(array $data, \PDO $conn): void
	{
		$sql = 'UPDATE NOVEDAD1 SET
			NovDia = :NovDia,
			NovMeJo = :NovMeJo,
			NovCoAd = :NovCoAd,
			NovFact = :NovFact,
			NovDesde = :NovDesde,
			NovHasta = :NovHasta,
			FechaHora = getdate()
			WHERE NovCodi = :NovCodi AND NovItem = :NovItem';
		$stmt = $conn->prepare($sql);
		$this->bindNovedad1Params($stmt, $data);
		$stmt->execute();
	}

	private function deleteNovedad1ByKey(int $novCodi, int $novItem, \PDO $conn): int
	{
		$sql = 'DELETE FROM NOVEDAD1 WHERE NovCodi = :NovCodi AND NovItem = :NovItem';
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', $novCodi, \PDO::PARAM_INT);
		$stmt->bindValue(':NovItem', $novItem, \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->rowCount();
	}

	private function bindNovedad1Params(\PDOStatement $stmt, array $data): void
	{
		$stmt->bindValue(':NovCodi', (int) ($data['NovCodi'] ?? 0), \PDO::PARAM_INT);
		$stmt->bindValue(':NovItem', (int) ($data['NovItem'] ?? 0), \PDO::PARAM_INT);
		$stmt->bindValue(':NovDia', (int) ($data['NovDia'] ?? 0), \PDO::PARAM_INT);
		$stmt->bindValue(':NovMeJo', (int) ($data['NovMeJo'] ?? 0), \PDO::PARAM_INT);
		$stmt->bindValue(':NovCoAd', (int) ($data['NovCoAd'] ?? 0), \PDO::PARAM_INT);
		$stmt->bindValue(':NovFact', (string) ($data['NovFact'] ?? '0'), \PDO::PARAM_STR);
		$stmt->bindValue(':NovDesde', (string) ($data['NovDesde'] ?? ''), \PDO::PARAM_STR);
		$stmt->bindValue(':NovHasta', (string) ($data['NovHasta'] ?? ''), \PDO::PARAM_STR);
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

			$sql = 'SELECT N1.NovCodi, N1.NovItem, N1.NovDia, N1.NovMeJo, N1.NovCoAd, N1.NovFact, N1.NovDesde, N1.NovHasta, N.NovDesc as "NovCoAdDesc" FROM NOVEDAD1 N1 INNER JOIN NOVEDAD N ON N1.NovCoAd = N.NovCodi WHERE N1.NovCodi = :NovCodi';

			$stmt = $conn->prepare($sql);
			$stmt->bindValue(':NovCodi', $novCodiInt, \PDO::PARAM_INT);
			$stmt->execute();

			$data = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
			$count = count($data);

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
			$this->log->trace('Novedad1::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw new \Exception('Error al obtener novedad1', 400);
		} catch (\Exception $e) {
			$this->log->trace('Novedad1::' . __FUNCTION__ . ': ', $this->NameLog, $e);
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
