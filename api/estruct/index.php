<?php
require __DIR__ . '/../fn.php';
header("Content-Type: application/json");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
tz();
tzLang();
errorReport();

try {
    $FechaHora = date('Ymd H:i:s');
    $checkMethod('GET');

    $wc = '';
    $dp = $request->query; // dataPayload
    $start = start();
    $length = length();

    $dp['Codi'] = ($dp['Codi']) ?? [];
    $dp['Codi'] = vp($dp['Codi'], 'Codi', 'intArrayM0', 11);

    $dp['Estruct'] = ($dp['Estruct']) ?? [];
    $dp['Estruct'] = vp($dp['Estruct'], 'Estruct', 'str', 11);


    if (empty($dp['Estruct'])) {
        throw new Exception("Parámetro 'Estruct' es requerido", 400);
    }

    $arrDP = [
        'Codi' => $dp['Codi'], // Codigo de tipo de hora {int} {array}
    ];

    $mapEstructValid = [
        'Emp' => [
            'tabla' => 'EMPRESAS',
            'pref' => 'Emp',
        ],
        'Pla' => [
            'tabla' => 'PLANTAS',
            'pref' => 'Pla',
        ],
        'Sec' => [
            'tabla' => 'SECTORES',
            'pref' => 'Sec',
        ],
        'Con' => [
            'tabla' => 'CONVENIO',
            'pref' => 'Con',
        ],
        'Se2' => [
            'tabla' => 'SECCION',
            'pref' => 'Se2',
        ],
        'Gru' => [
            'tabla' => 'GRUPOS',
            'pref' => 'Gru',
        ],
        'Suc' => [
            'tabla' => 'SUCURSALES',
            'pref' => 'Suc',
        ],
        'Nov' => [
            'tabla' => 'NOVEDAD',
            'pref' => 'Nov',
        ],
        'ONov' => [
            'tabla' => 'OTRASNOV',
            'pref' => 'ONov',
        ],
        'THo' => [
            'tabla' => 'TIPOHORA',
            'pref' => 'THo',
        ],
        'THoC' => [
            'tabla' => 'TIPOHORACAUSA',
            'pref' => 'THoC',
        ],
        'NovC' => [
            'tabla' => 'NOVECAUSA',
            'pref' => 'NovC',
        ],
        'RC' => [
            'tabla' => 'REGLASCH',
            'pref' => 'RC',
        ],
        'Hor' => [
            'tabla' => 'HORARIOS',
            'pref' => 'Hor',
        ],
        'Loc' => [
            'tabla' => 'LOCALIDA',
            'pref' => 'Loc',
        ],
        'Nac' => [
            'tabla' => 'NACIONES',
            'pref' => 'Nac',
        ],
        'Pro' => [
            'tabla' => 'PROVINCI',
            'pref' => 'Pro',
        ],
        'Tare' => [
            'tabla' => 'TAREAS',
            'pref' => 'Tare',
        ],
        'Gua' => [
            'tabla' => 'GUARDIAS',
            'pref' => 'Gua',
        ],
        'Lega' => [
            'tabla' => 'PERSONAL',
            'pref' => 'Leg',
        ],
    ];

    if (!array_key_exists($dp['Estruct'], $mapEstructValid)) {
        throw new Exception("Parámetro {$dp['Estruct']} en 'Estruct' es inválido", 400);
    }

    $tabla = $mapEstructValid[$dp['Estruct']]['tabla'];
    $pref = $mapEstructValid[$dp['Estruct']]['pref'];

    function validarCodi(int $valor, string $tabla): void
    {
        if ($tabla === 'PERSONAL' && $valor > 9999999999) {
            throw new Exception("Valor {$valor} en 'Codi' debe ser menor o igual a 9999999999", 400);
        }
        if ($tabla !== 'PERSONAL' && $valor > 32767) {
            throw new Exception("Valor {$valor} en 'Codi' debe ser menor o igual a 32767", 400);
        }
    }

    if (!empty($dp['Codi'])) {
        foreach ($dp['Codi'] as $val) {
            $valor = intval($val);
            validarCodi($valor, $tabla);
        }
    }

    foreach ($arrDP as $key => $p) {
        $e = [];
        $columna = ($tabla === 'PERSONAL') ? 'LegNume' : "{$pref}{$key}";
        if (is_array($p)) {
            $v = '';
            $e = array_filter($p, function ($v) {
                return $v !== false && !is_null($v) && ($v != '' || $v == '0');
            });
            $e = array_unique($e);
            if ($e) {
                if (count($e) > 1) {
                    $e = "'" . join("','", $e) . "'";
                    $wc .= " AND $tabla.$columna IN ($e)";
                } else {
                    foreach ($e as $v) {
                        if ($v !== NULL) {
                            $wc .= " AND $tabla.$columna = '$v'";
                        }
                    }
                }
            }
        } else {
            if ($v) {
                $wc .= " AND $tabla.$pref$key = '$v'";
            }
        }
    }

    $Codi = "{$pref}Codi";
    $Desc = ($dp['Estruct'] == 'Emp') ? "{$pref}Razon" : "{$pref}Desc";
    $Sec = ($dp['Estruct'] == 'Se2') ? true : '';
    $THoC = ($dp['Estruct'] == 'THoC') ? true : '';
    $Lega = ($dp['Estruct'] == 'Lega') ? true : '';
    $NovC = ($dp['Estruct'] == 'NovC') ? true : '';
    $Nov = ($dp['Estruct'] == 'Nov') ? true : '';
    $JoinSe2 = ($dp['Estruct'] == 'Se2') ? "INNER JOIN SECTORES ON SECCION.SecCodi = SECTORES.SecCodi" : '';
    $SecDesc = ($dp['Estruct'] == 'Se2') ? ",SECTORES.SecDesc" : '';

    $JoinTHora = ($dp['Estruct'] == 'THoC') ? "INNER JOIN TIPOHORA ON TIPOHORACAUSA.THoCHora = TIPOHORA.THoCodi" : '';
    // $THoraDesc = ($dp['Estruct'] == 'THoC') ? ",TIPOHORA.THoDesc" : '';
    $JoinNovC = ($dp['Estruct'] == 'NovC') ? "INNER JOIN NOVEDAD ON NOVECAUSA.NovCNove = NOVEDAD.NovCodi" : '';
    $TipoNovedad = ($dp['Estruct'] == 'Nov') ? ", NOVEDAD.NovTipo, dbo.fn_TipoNovedad(NOVEDAD.NovTipo) as 'NovTipoDesc' " : '';

    if ($Lega) {
        $Codi = 'LegNume';
        $Desc = 'LegApNo';
    }

    $wc .= ($dp['Desc']) ? " AND CONCAT('', $Desc, $Codi) LIKE '%$dp[Desc]%'" : '';
    $wc .= ($dp['Estruct'] == 'Lega') ? " AND PERSONAL.LegFeEg = '1753-01-01 00:00:00.000'" : '';

    $colNov = $Nov ? ', ' . implode(', ', ['NOVEDAD.NovCol1', 'NOVEDAD.NovCol2', 'NOVEDAD.NovCol3', 'NOVEDAD.NovCol4']) : '';

    $query = "SELECT * $SecDesc $TipoNovedad $colNov FROM $tabla $JoinSe2 $JoinTHora $JoinNovC WHERE $Codi > 0";
    $queryCount = "SELECT count(1) as 'count' FROM $tabla WHERE $Codi > 0";

    if ($dp['Estruct'] == 'Con') {
        $query = "SELECT * $SecDesc $TipoNovedad FROM $tabla $JoinSe2 $JoinTHora $JoinNovC";
        $queryCount = "SELECT count(1) as 'count' FROM $tabla";
    }

    if ($wc) {
        $query .= $wc;
        $queryCount .= $wc;
    }

    $stmtCount = $dbApiQuery($queryCount)[0]['count'] ?? '';

    $query .= " ORDER BY $Codi";
    $query .= " OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";

    $stmt = $dbApiQuery($query) ?? '';

    foreach ($stmt as $v) {
        $item = [
            "Codi" => $v[$Codi],
            "Desc" => $v[$Desc],
            "FechaHora" => fechFormat($v['FechaHora'], 'Y-m-d H:i:s')
        ];

        switch (true) {
            case $Sec:
                $item["Sector"] = [
                    "Codi" => $v['SecCodi'],
                    "Desc" => $v['SecDesc'],
                ];
                break;
            case $THoC:
                $item["CodiHora"] = $v['THoCodi'];
                $item["DescHora"] = $v['THoDesc'];
                break;
            case $NovC:
                $item["CodiNov"] = $v['NovCodi'];
                $item["DescNov"] = $v['NovDesc'];
                break;
            case $Nov:
                $item["Tipo"] = $v['NovTipo'];
                $item["TipoDesc"] = $v['NovTipoDesc'];
                $item["CodMens1"] = $v['NovCol1'];
                $item["CodMens2"] = $v['NovCol2'];
                $item["CodJor1"] = $v['NovCol3'];
                $item["CodJor2"] = $v['NovCol4'];
                break;
        }

        $data[] = $item;
    }

    if (empty($data)) {
        http_response_code(200);
        (response('', 0, 'OK', 200, $time_start, 0, $idCompany));
        exit;
    }
    $countData = count($data);
    http_response_code(200);
    (response($data, $stmtCount, 'OK', 200, $time_start, $countData, $idCompany));
} catch (\Throwable $th) {
    $code = $th->getCode();

    http_response_code($code);
    if ($code == 0) {
        $code = 400;
    }
    (response($th->getMessage(), 0, 'ERROR', $code, $time_start, 0, $idCompany));
}