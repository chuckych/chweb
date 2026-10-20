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
use function ctype_digit;
use function defined;
use function sprintf;

class Novedad
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
		$this->NameLog = date('Ymd') . '_novedad.log';
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
					$exists = $this->existeNovedad((int) $item['NovCodi'], $conn);

					if ($exists) {
						$novDescProvidedOnUpdate = array_key_exists('NovDesc', $item) && trim((string) $item['NovDesc']) !== '';
						$item = $this->mergeWithExistingNovedad($item, $conn);
						$item = $this->validateItem($item, false);
						if ($novDescProvidedOnUpdate) {
							$this->validateNovDescUnique((string) $item['NovDesc'], (int) $item['NovCodi'], $conn);
						}
						$this->validateNovCarCodiReference($item, $conn);
						$this->validateNovAdicHoReference($item, $conn);
						$this->validateNovSusTReference($item);
						$this->validateNovVacaReference($item);
						unset($item['User']);
						$this->updateNovedad($item, $conn);
						$this->auditor->add([['AudUser' => $audUser, 'AudTipo' => 'M', 'AudDato' => 'Novedad ' . $item['NovCodi']]]);
						$actualizados[] = [
							'NovCodi' => $item['NovCodi'],
							'NovDesc' => $item['NovDesc'],
						];
						continue;
					}

					$item = $this->applyDefaults($item);
					$item = $this->validateItem($item);
					$this->validateNovDescUnique((string) $item['NovDesc'], (int) $item['NovCodi'], $conn);
					$this->validateNovCarCodiReference($item, $conn);
					$this->validateNovAdicHoReference($item, $conn);
					$this->validateNovSusTReference($item);
					$this->validateNovVacaReference($item);
					unset($item['User']);
					$this->insertNovedad($item, $conn);
					$this->auditor->add([['AudUser' => $audUser, 'AudTipo' => 'A', 'AudDato' => 'Novedad ' . $item['NovCodi']]]);
					$insertados[] = [
						'NovCodi' => $item['NovCodi'],
						'NovDesc' => $item['NovDesc'],
					];
				} catch (ValidationException $e) {
					$errores[] = [
						'NovCodi' => $item['NovCodi'] ?? null,
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
			$this->log->trace('Novedad::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw new \Exception('Error al crear novedad', 400);
		} catch (\Exception $e) {
			if ($conn->inTransaction()) {
				$conn->rollBack();
			}
			$this->log->trace('Novedad::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw $e;
		}
	}
	public function delete(string $novCodi): void
	{
		$this->inicio = microtime(true);
		$idCompany = defined('ID_COMPANY') ? ID_COMPANY : 0;
		$audUser = isset($this->getData['User']) && $this->getData['User'] !== '' ? (string) $this->getData['User'] : '';
		$novCodiRaw = trim($novCodi);

		if ($novCodiRaw === '' || !ctype_digit($novCodiRaw)) {
			throw new ValidationException('NovCodi inválido: debe ser numérico');
		}

		$novCodi = (int) $novCodiRaw;
		if ($novCodi > 32767) {
			throw new ValidationException('NovCodi inválido: fuera de rango permitido');
		}

		if ($novCodi <= 0) {
			throw new ValidationException('NovCodi inválido para eliminar');
		}

		$conn = $this->conect->conn();

		try {
			$conn->beginTransaction();

			if ($this->hasFichas3References($novCodi, $conn)) {
				throw new ValidationException('No se puede eliminar la novedad porque tiene referencias');
			}

			$this->deleteNovedad1ByNove($novCodi, $conn);
			$this->deleteNovedad2ByNove($novCodi, $conn);
			$deletedNovedad = $this->deleteNovedadByCodi($novCodi, $conn);
			$this->deleteNoveCausaByNove($novCodi, $conn);

			if ($deletedNovedad === 0) {
				throw new ValidationException('No existe la novedad indicada para eliminar');
			}

			$this->auditor->add([['AudUser' => $audUser, 'AudTipo' => 'B', 'AudDato' => 'Novedad ' . $novCodi]]);
			$conn->commit();

			$this->resp->respuesta(
				['NovCodi' => $novCodi],
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
			$this->log->trace('Novedad::' . __FUNCTION__ . ': ', $this->NameLog, $e);
			throw new \Exception('Error al eliminar novedad', 400);
		} catch (\Exception $e) {
			if ($conn->inTransaction()) {
				$conn->rollBack();
			}
			$this->log->trace('Novedad::' . __FUNCTION__ . ': ', $this->NameLog, $e);
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
			'NovCodi' => ['required', 'smallint'],
			'User' => ['varchar100'],
		];

		(new InputValidator($item, $rules))->validate();
		return $item;
	}
	private function mergeWithExistingNovedad(array $item, \PDO $conn): array
	{
		$sql = "SELECT * FROM NOVEDAD WHERE NovCodi = :NovCodi";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', (int) $item['NovCodi'], \PDO::PARAM_INT);
		$stmt->execute();

		$existing = $stmt->fetch(\PDO::FETCH_ASSOC);
		if ($existing === false) {
			throw new \Exception('Novedad no encontrada para actualizar', 400);
		}

		$this->validateNovTipoOnUpdate($item, $existing);

		if (isset($existing['NovColor'])) {
			$existing['NovColor'] = sprintf('#%06X', ((int) $existing['NovColor']) & 0xFFFFFF);
		}

		if (array_key_exists('NovDesc', $item) && trim((string) $item['NovDesc']) === '') {
			unset($item['NovDesc']);
		}

		$merged = array_merge($existing, $item);
		$merged['User'] = $item['User'] ?? '';

		return $merged;
	}
	private function validateNovTipoOnUpdate(array $payload, array $existing): void
	{
		if (!array_key_exists('NovTipo', $payload)) {
			return;
		}

		if ((int) $payload['NovTipo'] !== (int) ($existing['NovTipo'] ?? -1)) {
			throw new ValidationException('NovTipo no puede modificarse');
		}
	}
	private function applyDefaults(array $item): array
	{
		$customValueKey = [
			'NovID' => '',
			'NovColor' => '#000000',
			'NovCoL1' => '',
			'NovCoL2' => '',
			'NovCoL3' => '',
			'NovCoL4' => '',
			'NovInfM' => '0',
			'NovInfJ' => '0',
			'NovSusT' => '0',
			'NovInsust' => '0',
			'NovInco' => '0',
			'NovFeri' => '1',
			'NovAlertaSeg' => '0',
			'NovGeSe' => '0',
			'NovRegl' => '0',
			'NovCant' => '0',
			'NovTiCo' => '0',
			'NovCtaD' => '1',
			'NovCtaH' => '12',
			'NovLimCC' => '0',
			'NovCarg' => '0',
			'NovCarDias' => '0',
			'NovDispoManual' => '1',
			'NovCarCodi' => '0',
			'NovCoDo' => '0',
			'NovCoLu' => '0',
			'NovCoMa' => '0',
			'NovCoMi' => '0',
			'NovCoJu' => '0',
			'NovCoVi' => '0',
			'NovCoSa' => '0',
			'NovCoFe' => '0',
			'NovFaDo' => '0',
			'NovFaLu' => '0',
			'NovFaMa' => '0',
			'NovFaMi' => '0',
			'NovFaJu' => '0',
			'NovFaVi' => '0',
			'NovFaSa' => '0',
			'NovFaFe' => '0',
			'NovCoF1' => '0',
			'NovCoF2' => '0',
			'NovFaF1' => '0',
			'NovFaF2' => '0',
			'NovVaca' => '0',
			'NovFich' => '0',
			'NovMailLega' => '0',
			'NovMailLista' => '0',
			'NovMailAsunto' => '',
			'NovMailTexto' => '',
			'NovAdicHo' => '0',
			'NovMailEnviarA' => '0',
			'User' => '',
		];

		foreach ($customValueKey as $key => $default) {
			if (!isset($item[$key]) || $item[$key] === '') {
				$item[$key] = $default;
			}
		}

		return $item;
	}
	private function validateItem(array $item, bool $requireNovDesc = true): array
	{
		if (array_key_exists('NovDesc', $item)) {
			$item['NovDesc'] = trim((string) $item['NovDesc']);
		}

		$rules = [
			'NovCodi' => ['required', 'smallint'],
			'NovDesc' => $requireNovDesc ? ['required', 'varchar40'] : ['varchar40'],
			'NovTipo' => ['requiredPermittedCero', 'tiponov'],
			'NovID' => ['varchar3'],
			'NovColor' => ['rgbColor'],
			'NovCoL1' => ['varchar10'],
			'NovCoL2' => ['varchar10'],
			'NovCoL3' => ['varchar10'],
			'NovCoL4' => ['varchar10'],
			'NovInfM' => ['allowed01'],
			'NovInfJ' => ['allowed01'],
			'NovSusT' => ['allowed01'],
			'NovInsust' => ['allowed01'],
			'NovInco' => ['allowed01'],
			'NovFeri' => ['allowed01'],
			'NovAlertaSeg' => ['allowed012'],
			'NovGeSe' => ['allowed01'],
			'NovRegl' => ['allowed012'],
			'NovCant' => ['allowed0a9999'],
			'NovTiCo' => ['allowed0a5'],
			'NovCtaD' => ['allowed1a12'],
			'NovCtaH' => ['allowed1a12'],
			'NovLimCC' => ['allowed01'],
			'NovCarg' => ['allowed0a999'],
			'NovCarDias' => ['allowed0a999'],
			'NovDispoManual' => ['allowed01'],
			'NovCarCodi' => ['smallint'],
			'User' => ['varchar100'],
		];

		(new InputValidator($item, $rules))->validate();
		$item['NovColor'] = (string) $this->rgbHexToDecimal($item['NovColor']);
		return $item;
	}
	private function validateNovDescUnique(string $novDesc, int $currentNovCodi, \PDO $conn): void
	{
		$trimmedNovDesc = trim($novDesc);
		$sql = "SELECT COUNT(*) FROM NOVEDAD WHERE LTRIM(RTRIM(NovDesc)) = :NovDesc AND NovCodi <> :NovCodi";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovDesc', $trimmedNovDesc, \PDO::PARAM_STR);
		$stmt->bindValue(':NovCodi', $currentNovCodi, \PDO::PARAM_INT);
		$stmt->execute();

		if ((int) $stmt->fetchColumn() > 0) {
			throw new ValidationException('NovDesc ya existe para otra novedad');
		}
	}
	private function validateNovCarCodiReference(array $item, \PDO $conn): void
	{
		$novCarCodi = (int) ($item['NovCarCodi'] ?? 0);
		if ($novCarCodi === 0) {
			return;
		}

		$currentNovCodi = (int) ($item['NovCodi'] ?? 0);
		if ($currentNovCodi > 0 && $novCarCodi === $currentNovCodi) {
			throw new ValidationException('NovCarCodi no puede referenciar la misma fila de NOVEDAD');
		}

		$sql = "SELECT TOP 1 NovCodi, NovTipo FROM NOVEDAD WHERE NovCodi = :NovCodi";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', $novCarCodi, \PDO::PARAM_INT);
		$stmt->execute();

		$reference = $stmt->fetch(\PDO::FETCH_ASSOC);
		if ($reference === false) {
			throw new ValidationException('NovCarCodi referencia un NovCodi inexistente');
		}

		$payloadTipo = (int) $item['NovTipo'];
		$referenceTipo = (int) ($reference['NovTipo'] ?? -1);

		if ($payloadTipo >= 3) {
			if ($referenceTipo < 3) {
				throw new ValidationException('NovCarCodi debe referenciar un NovCodi con NovTipo >= 3');
			}
			return;
		}

		if ($referenceTipo !== $payloadTipo) {
			throw new ValidationException('NovCarCodi debe referenciar un NovCodi con el mismo NovTipo');
		}
	}

	private function validateNovAdicHoReference(array $item, \PDO $conn): void
	{
		$novAdicHo = (int) ($item['NovAdicHo'] ?? 0);
		if ($novAdicHo === 0) {
			return;
		}

		$payloadTipo = (int) ($item['NovTipo'] ?? 0);
		if ($payloadTipo < 3) {
			throw new ValidationException('Solo se puede cargar Novedad en NovAdicHo si la novedad es de tipo ausencia (NovTipo >= 3)');
		}

		$novCodi = (int) ($item['NovCodi'] ?? 0);
		$sql = "SELECT TOP 1 NovCodi
			FROM NOVEDAD
			WHERE NovCodi = :NovAdicHo
				AND NovTipo >= 3
				AND NovCodi <> :NovCodi";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovAdicHo', $novAdicHo, \PDO::PARAM_INT);
		$stmt->bindValue(':NovCodi', $novCodi, \PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt->fetchColumn() === false) {
			throw new ValidationException('NovAdicHo debe ser un NovCodi válido de tipo ausencia (NovTipo >= 3) y distinto de la novedad actual');
		}
	}

	private function validateNovSusTReference(array $item): void
	{
		$novSusT = (int) ($item['NovSusT'] ?? 0);
		$novTipo = (int) ($item['NovTipo'] ?? 0);

		if ($novTipo >= 3) {
			return;
		}

		if ($novSusT === 0) {
			return;
		}

		if ($novSusT === 1) {
			throw new ValidationException('Suspender Tarjeta (NovSusT) no permitido con NovTipo <= 3');
		}
	}
	private function validateNovVacaReference(array $item): void
	{
		$NovVaca = (int) ($item['NovVaca'] ?? 0);
		$novTipo = (int) ($item['NovTipo'] ?? 0);

		if ($novTipo >= 3) {
			return;
		}

		if ($NovVaca === 0) {
			return;
		}

		if ($NovVaca === 1) {
			throw new ValidationException('Descontar de días trab. para los días de vacaciones (NovVaca) no permitido con NovTipo <= 3');
		}
	}

	private function existeNovedad(int $novCodi, \PDO $conn): bool
	{
		$sql = "SELECT COUNT(*) FROM NOVEDAD WHERE NovCodi = :NovCodi";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', $novCodi, \PDO::PARAM_INT);
		$stmt->execute();
		return (int) $stmt->fetchColumn() > 0;
	}

	private function hasFichas3References(int $novCodi, \PDO $conn): bool
	{
		$sql = "SELECT COUNT(*) FROM FICHAS3 WHERE FicNove = :NovCodi";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', $novCodi, \PDO::PARAM_INT);
		$stmt->execute();
		return (int) $stmt->fetchColumn() > 0;
	}

	private function deleteNovedadByCodi(int $novCodi, \PDO $conn): int
	{
		$sql = "DELETE FROM NOVEDAD WHERE NovCodi = :NovCodi";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', $novCodi, \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->rowCount();
	}

	private function deleteNoveCausaByNove(int $novCodi, \PDO $conn): void
	{
		$sql = "DELETE FROM NOVECAUSA WHERE NovCNove = :NovCodi";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', $novCodi, \PDO::PARAM_INT);
		$stmt->execute();
	}

	private function deleteNovedad1ByNove(int $novCodi, \PDO $conn): void
	{
		$sql = "DELETE FROM NOVEDAD1 WHERE NovCodi = :NovCodi";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', $novCodi, \PDO::PARAM_INT);
		$stmt->execute();
	}

	private function deleteNovedad2ByNove(int $novCodi, \PDO $conn): void
	{
		$sql = "DELETE FROM NOVEDAD2 WHERE NovCodi = :NovCodi";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCodi', $novCodi, \PDO::PARAM_INT);
		$stmt->execute();
	}
	private function insertNovedad(array $data, \PDO $conn): void
	{
		$localTransaction = false;

		try {
			if (!$conn->inTransaction()) {
				$conn->beginTransaction();
				$localTransaction = true;
			}

			$sql = "INSERT INTO NOVEDAD (
			NovCodi, NovDesc, NovTipo, NovID, NovColor,
			NovCoL1, NovCoL2, NovCoL3, NovCoL4, NovInfM, NovInfJ,
			NovSusT, NovInsust, NovInco, NovFeri, NovAlertaSeg, NovGeSe, NovRegl,
			NovCant, NovTiCo, NovCtaD, NovCtaH, NovLimCC,
			NovCarg, NovCarDias, NovDispoManual, NovCarCodi,
			NovCoDo, NovCoLu, NovCoMa, NovCoMi, NovCoJu, NovCoVi, NovCoSa, NovCoFe,
			NovFaDo, NovFaLu, NovFaMa, NovFaMi, NovFaJu, NovFaVi, NovFaSa, NovFaFe,
			NovCoF1, NovCoF2, NovFaF1, NovFaF2, NovVaca, NovFich, NovMailLega, NovMailLista,
			NovMailAsunto, NovMailTexto, NovAdicHo, NovMailEnviarA, FechaHora
		) VALUES (
			:NovCodi, :NovDesc, :NovTipo, :NovID, :NovColor,
			:NovCoL1, :NovCoL2, :NovCoL3, :NovCoL4, :NovInfM, :NovInfJ,
			:NovSusT, :NovInsust, :NovInco, :NovFeri, :NovAlertaSeg, :NovGeSe, :NovRegl,
			:NovCant, :NovTiCo, :NovCtaD, :NovCtaH, :NovLimCC,
			:NovCarg, :NovCarDias, :NovDispoManual, :NovCarCodi,
			:NovCoDo, :NovCoLu, :NovCoMa, :NovCoMi, :NovCoJu, :NovCoVi, :NovCoSa, :NovCoFe,
			:NovFaDo, :NovFaLu, :NovFaMa, :NovFaMi, :NovFaJu, :NovFaVi, :NovFaSa, :NovFaFe,
			:NovCoF1, :NovCoF2, :NovFaF1, :NovFaF2, :NovVaca, :NovFich, :NovMailLega, :NovMailLista,
			:NovMailAsunto, :NovMailTexto, :NovAdicHo, :NovMailEnviarA, getdate()
		)";


			$stmt = $conn->prepare($sql);
			$this->bindNovedadParams($stmt, $data);
			$stmt->execute();

			$novCodi = (int) $data['NovCodi'];
			if (!$this->existeNovecausaDefault($novCodi, $conn)) {
				$this->insertNovecausaDefault($novCodi, $conn);
			}

			if ($localTransaction) {
				$conn->commit();
			}
		} catch (\Exception $e) {
			if ($localTransaction && $conn->inTransaction()) {
				$conn->rollBack();
			}
			throw $e;
		}
	}

	private function insertNovecausaDefault(int $novCodi, \PDO $conn): void
	{
		$sql = "INSERT INTO NOVECAUSA (NovCNove, NovCCodi, NovCDesc, FechaHora)
			VALUES (:NovCNove, :NovCCodi, :NovCDesc, getdate())";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCNove', $novCodi, \PDO::PARAM_INT);
		$stmt->bindValue(':NovCCodi', 0, \PDO::PARAM_INT);
		$stmt->bindValue(':NovCDesc', '', \PDO::PARAM_STR);
		$stmt->execute();
	}

	private function existeNovecausaDefault(int $novCodi, \PDO $conn): bool
	{
		$sql = "SELECT COUNT(*) FROM NOVECAUSA WHERE NovCNove = :NovCNove AND NovCCodi = 0";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':NovCNove', $novCodi, \PDO::PARAM_INT);
		$stmt->execute();
		return (int) $stmt->fetchColumn() > 0;
	}
	private function updateNovedad(array $data, \PDO $conn): void
	{
		$sql = "UPDATE NOVEDAD SET
			NovDesc = :NovDesc,
			NovTipo = :NovTipo,
			NovID = :NovID,
			NovColor = :NovColor,
			NovCoL1 = :NovCoL1,
			NovCoL2 = :NovCoL2,
			NovCoL3 = :NovCoL3,
			NovCoL4 = :NovCoL4,
			NovInfM = :NovInfM,
			NovInfJ = :NovInfJ,
			NovSusT = :NovSusT,
			NovInsust = :NovInsust,
			NovInco = :NovInco,
			NovFeri = :NovFeri,
			NovAlertaSeg = :NovAlertaSeg,
			NovGeSe = :NovGeSe,
			NovRegl = :NovRegl,
			NovCant = :NovCant,
			NovTiCo = :NovTiCo,
			NovCtaD = :NovCtaD,
			NovCtaH = :NovCtaH,
			NovLimCC = :NovLimCC,
			NovCarg = :NovCarg,
			NovCarDias = :NovCarDias,
			NovDispoManual = :NovDispoManual,
			NovCarCodi = :NovCarCodi,
			NovCoDo = :NovCoDo,
			NovCoLu = :NovCoLu,
			NovCoMa = :NovCoMa,
			NovCoMi = :NovCoMi,
			NovCoJu = :NovCoJu,
			NovCoVi = :NovCoVi,
			NovCoSa = :NovCoSa,
			NovCoFe = :NovCoFe,
			NovFaDo = :NovFaDo,
			NovFaLu = :NovFaLu,
			NovFaMa = :NovFaMa,
			NovFaMi = :NovFaMi,
			NovFaJu = :NovFaJu,
			NovFaVi = :NovFaVi,
			NovFaSa = :NovFaSa,
			NovFaFe = :NovFaFe,
			NovCoF1 = :NovCoF1,
			NovCoF2 = :NovCoF2,
			NovFaF1 = :NovFaF1,
			NovFaF2 = :NovFaF2,
			NovVaca = :NovVaca,
			NovFich = :NovFich,
			NovMailLega = :NovMailLega,
			NovMailLista = :NovMailLista,
			NovMailAsunto = :NovMailAsunto,
			NovMailTexto = :NovMailTexto,
			NovAdicHo = :NovAdicHo,
			NovMailEnviarA = :NovMailEnviarA,
			FechaHora = getdate()
		WHERE NovCodi = :NovCodi";

		$stmt = $conn->prepare($sql);
		$this->bindNovedadParams($stmt, $data);
		$stmt->execute();
	}
	private function bindNovedadParams(\PDOStatement $stmt, array $data): void
	{
		$intFields = [
			'NovCodi', 'NovTipo', 'NovColor', 'NovInfM', 'NovInfJ', 'NovSusT', 'NovInsust', 'NovInco',
			'NovFeri', 'NovAlertaSeg', 'NovGeSe', 'NovRegl', 'NovCant', 'NovTiCo', 'NovCtaD', 'NovCtaH',
			'NovLimCC', 'NovCarg', 'NovCarDias', 'NovDispoManual', 'NovCarCodi', 'NovCoDo', 'NovCoLu',
			'NovCoMa', 'NovCoMi', 'NovCoJu', 'NovCoVi', 'NovCoSa', 'NovCoFe', 'NovFaDo', 'NovFaLu',
			'NovFaMa', 'NovFaMi', 'NovFaJu', 'NovFaVi', 'NovFaSa', 'NovFaFe', 'NovCoF1', 'NovCoF2',
			'NovFaF1', 'NovFaF2', 'NovVaca', 'NovFich', 'NovMailLega', 'NovMailLista', 'NovAdicHo',
			'NovMailEnviarA',
		];

		foreach ($intFields as $field) {
			$stmt->bindValue(':' . $field, (int) ($data[$field] ?? 0), \PDO::PARAM_INT);
		}

		$strFields = [
			'NovDesc', 'NovID', 'NovCoL1', 'NovCoL2', 'NovCoL3', 'NovCoL4', 'NovMailAsunto', 'NovMailTexto',
		];

		foreach ($strFields as $field) {
			$stmt->bindValue(':' . $field, (string) ($data[$field] ?? ''), \PDO::PARAM_STR);
		}
	}
	private function rgbHexToDecimal(string $hex): int
	{
		$hex = ltrim($hex, '#');
		$r = hexdec(substr($hex, 0, 2));
		$g = hexdec(substr($hex, 2, 2));
		$b = hexdec(substr($hex, 4, 2));

		$rgb24 = ($r << 16) | ($g << 8) | $b;
		$unsigned = 0xFF000000 | $rgb24;

		return $unsigned - 4294967296;
	}
}
