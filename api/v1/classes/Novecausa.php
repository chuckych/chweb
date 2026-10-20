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
use function array_key_exists;
use function count;
use function ctype_digit;
use function defined;

class Novecausa
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
		$this->NameLog = date('Ymd') . '_novecausa.log';
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
					$this->validateNovCNoveReference((int) $item['NovCNove'], $conn);
					$exists = $this->existeNovecausa((int) $item['NovCCodi'], (int) $item['NovCNove'], $conn);

					if ($exists) {
						$novCDescProvidedOnUpdate = array_key_exists('NovCDesc', $item) && trim((string) $item['NovCDesc']) !== '';
						$item = $this->mergeWithExistingNovecausa($item, $conn);
						$item = $this->validateItem($item, false);

						if ($novCDescProvidedOnUpdate) {
							$this->validateNovCDescUnique((string) $item['NovCDesc'], (int) $item['NovCNove'], (int) $item['NovCCodi'], $conn);
						}

						unset($item['User']);
						$this->updateNovecausa($item, $conn);
						$this->auditor->add([['AudUser' => $audUser, 'AudTipo' => 'M', 'AudDato' => 'Novecausa ' . $item['NovCNove'] . '/' . $item['NovCCodi']]]);
						$actualizados[] = [
							'NovCNove' => $item['NovCNove'],
							'NovCCodi' => $item['NovCCodi'],
							'NovCDesc' => $item['NovCDesc'],
						];
						continue;
					}

					$item = $this->applyDefaults($item);
					$item = $this->validateItem($item, true);
					$this->validateNoDuplicatePair((int) $item['NovCCodi'], (int) $item['NovCNove'], $conn);
					$this->validateNovCDescUnique((string) $item['NovCDesc'], (int) $item['NovCNove'], (int) $item['NovCCodi'], $conn);

					unset($item['User']);
					$this->insertNovecausa($item, $conn);
					$this->auditor->add([['AudUser' => $audUser, 'AudTipo' => 'A', 'AudDato' => 'Novecausa ' . $item['NovCNove'] . '/' . $item['NovCCodi']]]);
					$insertados[] = [
						'NovCNove' => $item['NovCNove'],
						'NovCCodi' => $item['NovCCodi'],
						'NovCDesc' => $item['NovCDesc'],
					];
				} catch (ValidationException $e) {
					$errores[] = [
						'NovCNove' => $item['NovCNove'] ?? null,
						'NovCCodi' => $item['NovCCodi'] ?? null,
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
			$this->log->trace('Novecausa::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw new \Exception('Error al crear novecausa', 400);
		} catch (\Exception $e) {
			if ($conn->inTransaction()) {
				$conn->rollBack();
			}
			$this->log->trace('Novecausa::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw $e;
		}
	}

	public function delete(string $novCNove, string $novCCodi): void
	{
		$this->inicio = microtime(true);
		$idCompany = defined('ID_COMPANY') ? ID_COMPANY : 0;
		$audUser = isset($this->getData['User']) && $this->getData['User'] !== '' ? (string) $this->getData['User'] : '';

		$novCNoveRaw = trim($novCNove);
		$novCCodiRaw = trim($novCCodi);

		if ($novCNoveRaw === '' || !ctype_digit($novCNoveRaw) || $novCCodiRaw === '' || !ctype_digit($novCCodiRaw)) {
			throw new ValidationException('NovCNove y NovCCodi deben ser numéricos');
		}

		$novCNoveInt = (int) $novCNoveRaw;
		$novCCodiInt = (int) $novCCodiRaw;

		if ($novCNoveInt <= 0 || $novCCodiInt <= 0) {
			throw new ValidationException('NovCNove y NovCCodi deben ser mayores a 0');
		}

		if ($novCNoveInt > 32767 || $novCCodiInt > 32767) {
			throw new ValidationException('NovCNove y NovCCodi están fuera del rango smallint');
		}

		$conn = $this->conect->conn();

		try {
			$conn->beginTransaction();
			$this->validateNovCNoveReference($novCNoveInt, $conn);

			if ($this->hasFichas3References($novCNoveInt, $novCCodiInt, $conn)) {
				throw new ValidationException('No se puede eliminar porque tiene referencias');
			}

			$deleted = $this->deleteNovecausaByPair($novCNoveInt, $novCCodiInt, $conn);
			if ($deleted === 0) {
				throw new ValidationException('No existe la novecausa indicada para eliminar');
			}

			$this->auditor->add([['AudUser' => $audUser, 'AudTipo' => 'B', 'AudDato' => 'Novecausa ' . $novCNoveInt . '/' . $novCCodiInt]]);
			$conn->commit();

			$this->resp->respuesta(
				['NovCNove' => $novCNoveInt, 'NovCCodi' => $novCCodiInt],
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
			$this->log->trace('Novecausa::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw new \Exception('Error al eliminar novecausa', 400);
		} catch (\Exception $e) {
			if ($conn->inTransaction()) {
				$conn->rollBack();
			}
			$this->log->trace('Novecausa::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw $e;
		}
	}

	public function get(string $novCNove): void
	{
		$this->inicio = microtime(true);
		$idCompany = defined('ID_COMPANY') ? ID_COMPANY : 0;

		$novCNoveRaw = trim($novCNove);
		if ($novCNoveRaw === '' || !ctype_digit($novCNoveRaw)) {
			throw new ValidationException('NovCNove debe ser numerico');
		}

		$novCNoveInt = (int) $novCNoveRaw;
		if ($novCNoveInt <= 0) {
			throw new ValidationException('NovCNove debe ser mayor a 0');
		}

		if ($novCNoveInt > 32767) {
			throw new ValidationException('NovCNove esta fuera del rango smallint');
		}

		$conn = $this->conect->conn();

		try {
			$this->validateNovCNoveReference($novCNoveInt, $conn);

			$sql = "SELECT NovCNove, NovCCodi, NovCDesc, FechaHora
				FROM NOVECAUSA
				WHERE NovCNove = :NovCNove
					AND NovCCodi > 0
				ORDER BY NovCCodi";

			$stmt = $conn->prepare($sql);
			$stmt->bindValue(':NovCNove', $novCNoveInt, \PDO::PARAM_INT);
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
			$this->log->trace('Novecausa::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw new \Exception('Error al obtener novecausa', 400);
		} catch (\Exception $e) {
			$this->log->trace('Novecausa::' . __FUNCTION__ . ': ', $this->NameLog, $e);
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

	private function validateItemBase(array $item): array
	{
		$rules = [
			'NovCNove' => ['required', 'smallint'],
			'NovCCodi' => ['required', 'smallint'],
			'User' => ['varchar100'],
		];

		(new InputValidator($item, $rules))->validate();
		return $item;
	}

	private function applyDefaults(array $item): array
	{
		$defaults = [
			'NovCDesc' => '',
			'User' => '',
		];

		foreach ($defaults as $key => $default) {
			if (!isset($item[$key]) || $item[$key] === '') {
				$item[$key] = $default;
			}
		}

		return $item;
	}

	private function validateItem(array $item, bool $requireNovCDesc = true): array
	{
		if (array_key_exists('NovCDesc', $item)) {
			$item['NovCDesc'] = trim((string) $item['NovCDesc']);
		}

		$rules = [
			'NovCNove' => ['required', 'smallint'],
			'NovCCodi' => ['required', 'smallint'],
			'NovCDesc' => $requireNovCDesc ? ['required', 'varchar40'] : ['varchar40'],
			'User' => ['varchar100'],
		];

		(new InputValidator($item, $rules))->validate();
		return $item;
	}

	private function mergeWithExistingNovecausa(array $item, \PDO $conn): array
	{
		$sql = "SELECT * FROM NOVECAUSA WHERE NovCCodi = :NovCCodi AND NovCNove = :NovCNove";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCCodi', (int) $item['NovCCodi'], \PDO::PARAM_INT);
		$stmt->bindValue(':NovCNove', (int) $item['NovCNove'], \PDO::PARAM_INT);
		$stmt->execute();

		$existing = $stmt->fetch(\PDO::FETCH_ASSOC);
		if ($existing === false) {
			throw new \Exception('Novecausa no encontrada para actualizar', 400);
		}

		if (array_key_exists('NovCDesc', $item) && trim((string) $item['NovCDesc']) === '') {
			unset($item['NovCDesc']);
		}

		$merged = array_merge($existing, $item);
		$merged['User'] = $item['User'] ?? '';

		return $merged;
	}

	private function validateNovCNoveReference(int $novCNove, \PDO $conn): void
	{
		$sql = "SELECT TOP 1 NovCodi FROM NOVEDAD WHERE NovCodi = :NovCNove";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCNove', $novCNove, \PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt->fetchColumn() === false) {
			throw new ValidationException('NovCNove no existe en NOVEDAD');
		}
	}

	private function validateNovCDescUnique(string $novCDesc, int $novCNove, int $currentNovCCodi, \PDO $conn): void
	{
		$trimmedDesc = trim($novCDesc);
		$sql = "SELECT COUNT(*)
			FROM NOVECAUSA
			WHERE LTRIM(RTRIM(NovCDesc)) = :NovCDesc
				AND NovCNove = :NovCNove
				AND NovCCodi <> :NovCCodi";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCDesc', $trimmedDesc, \PDO::PARAM_STR);
		$stmt->bindValue(':NovCNove', $novCNove, \PDO::PARAM_INT);
		$stmt->bindValue(':NovCCodi', $currentNovCCodi, \PDO::PARAM_INT);
		$stmt->execute();

		if ((int) $stmt->fetchColumn() > 0) {
			throw new ValidationException('NovCDesc ya existe para NovCNove');
		}
	}

	private function validateNoDuplicatePair(int $novCCodi, int $novCNove, \PDO $conn): void
	{
		if ($this->existeNovecausa($novCCodi, $novCNove, $conn)) {
			throw new ValidationException('Ya existe la novecausa para el par NovCCodi/NovCNove');
		}
	}

	private function existeNovecausa(int $novCCodi, int $novCNove, \PDO $conn): bool
	{
		$sql = "SELECT COUNT(*) FROM NOVECAUSA WHERE NovCCodi = :NovCCodi AND NovCNove = :NovCNove";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCCodi', $novCCodi, \PDO::PARAM_INT);
		$stmt->bindValue(':NovCNove', $novCNove, \PDO::PARAM_INT);
		$stmt->execute();
		return (int) $stmt->fetchColumn() > 0;
	}

	private function hasFichas3References(int $novCNove, int $novCCodi, \PDO $conn): bool
	{
		$sql = "SELECT COUNT(*) FROM FICHAS3 WHERE FicNove = :NovCNove AND FicCaus = :NovCCodi";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCNove', $novCNove, \PDO::PARAM_INT);
		$stmt->bindValue(':NovCCodi', $novCCodi, \PDO::PARAM_INT);
		$stmt->execute();
		return (int) $stmt->fetchColumn() > 0;
	}

	private function insertNovecausa(array $data, \PDO $conn): void
	{
		$sql = "INSERT INTO NOVECAUSA (NovCCodi, NovCNove, NovCDesc, FechaHora)
			VALUES (:NovCCodi, :NovCNove, :NovCDesc, getdate())";
		$stmt = $conn->prepare($sql);
		$this->bindNovecausaParams($stmt, $data);
		$stmt->execute();
	}

	private function updateNovecausa(array $data, \PDO $conn): void
	{
		$sql = "UPDATE NOVECAUSA
			SET NovCDesc = :NovCDesc,
				FechaHora = getdate()
			WHERE NovCCodi = :NovCCodi
				AND NovCNove = :NovCNove";
		$stmt = $conn->prepare($sql);
		$this->bindNovecausaParams($stmt, $data);
		$stmt->execute();
	}

	private function deleteNovecausaByPair(int $novCNove, int $novCCodi, \PDO $conn): int
	{
		$sql = "DELETE FROM NOVECAUSA WHERE NovCNove = :NovCNove AND NovCCodi = :NovCCodi";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCNove', $novCNove, \PDO::PARAM_INT);
		$stmt->bindValue(':NovCCodi', $novCCodi, \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->rowCount();
	}

	private function bindNovecausaParams(\PDOStatement $stmt, array $data): void
	{
		$stmt->bindValue(':NovCCodi', (int) ($data['NovCCodi'] ?? 0), \PDO::PARAM_INT);
		$stmt->bindValue(':NovCNove', (int) ($data['NovCNove'] ?? 0), \PDO::PARAM_INT);
		$stmt->bindValue(':NovCDesc', (string) ($data['NovCDesc'] ?? ''), \PDO::PARAM_STR);
	}
}

