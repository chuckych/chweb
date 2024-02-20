<?php
$pathLog = __DIR__ . '../../logs/info/' . date('Ymd') . '_cambios_db.log';

function createTable($tableName, $fields, $engine, $pathLog)
{
    if (!checkTable($tableName)) {
        $sql = "CREATE TABLE `$tableName` (";
        $sql .= $fields;
        $sql .= ") $engine";
        if (pdoQuery($sql)) {
            fileLog("Se creo la tabla \"$tableName\"", $pathLog); // escribir en el log
        } else {
            fileLog("no se creo tabla: \"$tableName\"", $pathLog); // escribir en el log
        }
    }
}

if ($verDB < 20210102) {
    if (checkTable('modulos')) { // Si la tabla existe
        if (!count_pdoQuery("SELECT 1 FROM modulos where id = 29 LIMIT 1")) { // Si no existe el registro
            insert_pdoQuery("INSERT INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES ('29', 'FFeVjsix', 'Informe Presentismo', 13, '0', 2)");
            fileLog("Se inserto el modulo: \"Informe Presentismo\"", $pathLog); // escribir en el log
        } else { // Si existe el registro
            fileLog("El modulo: \"Informe Presentismo\" ya existe", $pathLog); // escribir en el log
        }
        if (!count_pdoQuery("SELECT 1 FROM modulos where id = 30 LIMIT 1")) { // Si no existe el registro
            insert_pdoQuery("INSERT INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES ('30', 'geD-wzy1', 'Datos', 10, '0', 3)");
            fileLog("Se inserto el modulo: \"Datos\"", $pathLog); // escribir en el log
        } else { // Si existe el registro
            fileLog("El modulo: \"Datos\" ya existe", $pathLog); // escribir en el log
        }
        if (!count_pdoQuery("SELECT 1 FROM modulos where id = 31 LIMIT 1")) { // Si no existe el registro
            insert_pdoQuery("INSERT INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES ('31', '357ruc7a', 'Estructura', 11, '0', 3)");
            fileLog("Se inserto el modulo: \"Estructura\"", $pathLog); // escribir en el log
        } else { // Si existe el registro
            fileLog("El modulo: \"Estructura\" ya existe", $pathLog); // escribir en el log
        }
        if (!count_pdoQuery("SELECT 1 FROM modulos where id = 32 LIMIT 1")) { // Si no existe el registro
            insert_pdoQuery("INSERT INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES ('32', 'm0b1l3Hr', 'Mobile HRP', 30, '0', 4)");
            fileLog("Se inserto el modulo: \"Mobile HRP\"", $pathLog); // escribir en el log
        } else { // Si existe el registro
            fileLog("El modulo: \"Mobile HRP\" ya existe", $pathLog); // escribir en el log
        }
        if (!count_pdoQuery("SELECT 1 FROM modulos where id = 33 LIMIT 1")) { // Si no existe el registro
            insert_pdoQuery("INSERT INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES ('33', 'H0r4r10s', 'Horarios', 30, '0', 1)");
            fileLog("Se inserto el modulo: \"Horarios\"", $pathLog); // escribir en el log
        } else { // Si existe el registro
            fileLog("El modulo: \"Horarios\" ya existe", $pathLog); // escribir en el log
        }
        if (!count_pdoQuery("SELECT 1 FROM modulos where id = 34 LIMIT 1")) { // Si no existe el registro
            insert_pdoQuery("INSERT INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES ('34', '1nf0rf4r', 'Informe FAR', 14, '1', 2)");
            fileLog("Se inserto el modulo: \"Informe FAR\"", $pathLog); // escribir en el log
        } else { // Si existe el registro
            fileLog("El modulo: \"Informe FAR\" ya existe", $pathLog); // escribir en el log
        }
    } else { // Si la tabla no existe
        fileLog("No existe tabla: \"modulos\"", $pathLog); // escribir en el log
    }
    if (checkTable('mod_roles')) {
        pdoQuery("UPDATE mod_roles SET mod_roles.id_rol = (SELECT roles.id FROM roles WHERE roles.recid = mod_roles.recid_rol) WHERE mod_roles.id_rol = 0");
        fileLog("Se actualizaron valores de la columna \"id_rol\" en la tabla \"mod_roles\"", $pathLog); // escribir en el log
        pdoQuery("DELETE FROM mod_roles WHERE mod_roles.id_rol = 0");
        fileLog("Se eliminaron valores inconsistentes de la tabla \"mod_roles\"", $pathLog); // escribir en el log
    } else {
        fileLog("No existe tabla: \"mod_roles\"", $pathLog); // escribir en el log
    }
    if (checkTable('abm_roles')) {
        pdoQuery("UPDATE abm_roles SET abm_roles.id_rol = (SELECT roles.id FROM roles WHERE roles.recid = abm_roles.recid_rol) WHERE abm_roles.id_rol = 0");
        fileLog("Se actualizaron valores de la columna \"id_rol\" en la tabla \"abm_roles\"", $pathLog); // escribir en el log
        pdoQuery("DELETE FROM abm_roles WHERE abm_roles.id_rol NOT IN (SELECT roles.id FROM roles)");
        fileLog("Se verificaron valores inconsistentes de la tabla \"abm_roles\"", $pathLog); // escribir en el log
    } else {
        fileLog("No existe tabla: \"abm_roles\"", $pathLog); // escribir en el log
    }

    if (checkTable('params')) {
        $selDataPresentes = count_pdoQuery("SELECT 1 FROM params WHERE modulo = 29 and descripcion = 'presentes' and cliente = $row[id_cliente] LIMIT 1");
        $selDataAusentes = count_pdoQuery("SELECT 1 FROM params WHERE modulo = 29 and descripcion = 'ausentes' and cliente = $row[id_cliente] LIMIT 1");
        (!$selDataPresentes) ? pdoQuery("INSERT INTO params (modulo, descripcion, valores, cliente) VALUES ('29', 'presentes', '', $row[id_cliente])") : '';
        fileLog("Se insertaron valores \"presentes\" en tabla \"params\"", $pathLog); // escribir en el log
        (!$selDataAusentes) ? pdoQuery("INSERT INTO params (modulo, descripcion, valores, cliente) VALUES ('29', 'ausentes', '', $row[id_cliente])") : '';
        fileLog("Se insertaron valores \"ausentes\" en tabla \"params\"", $pathLog); // escribir en el log
    } else {
        fileLog("No existe tabla: \"params\"", $pathLog); // escribir en el log
    }

    if (!checkTable('lista_roles')) {
        pdoQuery("CREATE TABLE IF NOT EXISTS `lista_roles` ( `id_rol` TINYINT(4) NOT NULL, `lista` ENUM('0','1','2','3','4','5') NOT NULL DEFAULT '0' COLLATE 'utf8_general_ci', `datos` TEXT NOT NULL COLLATE 'utf8mb4_bin', `fecha` DATETIME NOT NULL, PRIMARY KEY (`id_rol`, `lista`) USING BTREE, CONSTRAINT `FK_lista_roles_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION ) COLLATE='utf8_general_ci' ENGINE=InnoDB");
        if (checkTable('lista_roles')) {
            fileLog("Se creo la tabla \"lista_roles\"", $pathLog); // escribir en el log
        } else {
            fileLog("No se creo tabla: \"lista_roles\"", $pathLog); // escribir en el log
        }
    } else {
        fileLog("Ya existe la tabla: \"lista_roles\"", $pathLog); // escribir en el log
    }

    if (!checkTable('lista_estruct')) {
        pdoQuery("CREATE TABLE IF NOT EXISTS `lista_estruct` (`uid` INT(11) NOT NULL, `lista` ENUM('1','2','3','4','5','6','7','8') NOT NULL COLLATE 'utf8_general_ci', `datos` TEXT NOT NULL COLLATE 'utf8mb4_bin', `fecha` DATETIME NOT NULL, PRIMARY KEY (`uid`, `lista`) USING BTREE, CONSTRAINT `FK_lista_estruct_usuarios` FOREIGN KEY (`uid`) REFERENCES `usuarios` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION) COLLATE='utf8_general_ci' ENGINE=InnoDB");
        if (checkTable('lista_roles')) {
            fileLog("Se creo la tabla \"lista_estruct\"", $pathLog); // escribir en el log
        } else {
            fileLog("No se creo tabla: \"lista_estruct\"", $pathLog); // escribir en el log
        }
    } else {
        fileLog("Ya existe la tabla: \"lista_estruct\"", $pathLog); // escribir en el log
    }

    if (checkColumn('usuarios', 'recid')) { // verificar si existe la columna
        pdoQuery("ALTER TABLE `usuarios` CHANGE COLUMN `recid` `recid` CHAR(8) NOT NULL COLLATE 'latin1_swedish_ci' AFTER `usuario`");
        fileLog("Se modifico el tipo de dato de la columna \"recid\" CHAR(8) de la tabla \"usuarios\"", $pathLog); // escribir en el log
    } else {
        fileLog("No existe columna: \"recid\" en la tabla \"usuarios", $pathLog); // escribir en el log
    }
    if (!checkColumn('abm_roles', 'aTur')) { // si no existe la columna
        pdoQuery("ALTER TABLE `abm_roles` ADD COLUMN `aTur` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `bCit`, ADD COLUMN `mTur` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `aTur`, ADD COLUMN `bTur` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `mTur`");
        fileLog("Se agregaron los campos a la tabla \"abm_roles\"", $pathLog); // escribir en el log
    } else { // si existe la columna
        fileLog("Ya existe columna: \"aTur\" en la tabla \"abm_roles\"", $pathLog); // escribir en el log
    }
    if (checkColumn('abm_roles', 'id_rol')) { // verificar si existe la columna
        pdoQuery("ALTER TABLE `abm_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
        fileLog("Se modifico el tipo de dato de la columna \"id_rol\" TINYINT(4) NOT NULL de la tabla \"abm_roles\"", $pathLog); // escribir en el log
    } else { // si no existe la columna
        fileLog("No existe columna: \"id_rol\" en la tabla \"abm_roles", $pathLog); // escribir en el log
    }
    if (checkColumn('mod_roles', 'id_rol')) { // verificar si existe la columna
        pdoQuery("ALTER TABLE `mod_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
        fileLog("Se modifico el tipo de dato de la columna \"id_rol\" TINYINT(4) NOT NULL de la tabla \"mod_roles\"", $pathLog); // escribir en el log
    } else { // si no existe la columna
        fileLog("No existe columna: \"id_rol\" en la tabla \"mod_roles", $pathLog); // escribir en el log
    }
    if (checkColumn('conv_roles', 'id_rol')) { // verificar si existe la columna
        pdoQuery("ALTER TABLE `conv_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
        fileLog("Se modifico el tipo de dato de la columna \"id_rol\" TINYINT(4) NOT NULL de la tabla \"conv_roles\"", $pathLog); // escribir en el log
    } else { // si no existe la columna
        fileLog("No existe columna: \"id_rol\" en la tabla \"conv_roles", $pathLog); // escribir en el log
    }
    if (checkColumn('emp_roles', 'id_rol')) { // verificar si existe la columna
        pdoQuery("ALTER TABLE `emp_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
        fileLog("Se modifico el tipo de dato de la columna \"id_rol\" TINYINT(4) NOT NULL de la tabla \"emp_roles\"", $pathLog); // escribir en el log
    } else { // si no existe la columna
        fileLog("No existe columna: \"id_rol\" en la tabla \"emp_roles", $pathLog); // escribir en el log
    }
    if (checkColumn('grup_roles', 'id_rol')) { // verificar si existe la columna
        pdoQuery("ALTER TABLE `grup_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
        fileLog("Se modifico el tipo de dato de la columna \"id_rol\" TINYINT(4) NOT NULL de la tabla \"grup_roles\"", $pathLog); // escribir en el log
    } else { // si no existe la columna
        fileLog("No existe columna: \"id_rol\" en la tabla \"grup_roles", $pathLog); // escribir en el log
    }
    if (checkColumn('plan_roles', 'id_rol')) { // verificar si existe la columna
        pdoQuery("ALTER TABLE `plan_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
        fileLog("Se modifico el tipo de dato de la columna \"id_rol\" TINYINT(4) NOT NULL de la tabla \"plan_roles\"", $pathLog); // escribir en el log
    } else { // si no existe la columna
        fileLog("No existe columna: \"id_rol\" en la tabla \"plan_roles", $pathLog); // escribir en el log
    }
    if (checkColumn('secc_roles', 'id_rol')) { // verificar si existe la columna
        pdoQuery("ALTER TABLE `secc_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
        fileLog("Se modifico el tipo de dato de la columna \"id_rol\" TINYINT(4) NOT NULL de la tabla \"secc_roles\"", $pathLog); // escribir en el log
    } else { // si no existe la columna
        fileLog("No existe columna: \"id_rol\" en la tabla \"secc_roles", $pathLog); // escribir en el log
    }
    if (checkColumn('sect_roles', 'id_rol')) { // verificar si existe la columna
        pdoQuery("ALTER TABLE `sect_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
        fileLog("Se modifico el tipo de dato de la columna \"id_rol\" TINYINT(4) NOT NULL de la tabla \"sect_roles\"", $pathLog); // escribir en el log
    } else { // si no existe la columna
        fileLog("No existe columna: \"id_rol\" en la tabla \"sect_roles", $pathLog); // escribir en el log
    }
    if (checkColumn('suc_roles', 'id_rol')) { // verificar si existe la columna
        pdoQuery("ALTER TABLE `suc_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
        fileLog("Se modifico el tipo de dato de la columna \"id_rol\" TINYINT(4) NOT NULL de la tabla \"suc_roles\"", $pathLog); // escribir en el log
    } else { // si no existe la columna
        fileLog("No existe columna: \"id_rol\" en la tabla \"suc_roles", $pathLog); // escribir en el log
    }

    if (!checkKey('FK_abm_roles_roles', 'abm_roles')) { // verificar si no existe la clave foranea
        pdoQuery("ALTER TABLE `abm_roles` ADD CONSTRAINT `FK_abm_roles_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
        fileLog("Se agrego las clave foranea a la tabla \"abm_roles\"", $pathLog); // escribir en el log
    } else { // si existe la clave foranea
        fileLog("Ya existe la clave foranea \"FK_abm_roles_roles\" de la tabla \"abm_roles\"", $pathLog); // escribir en el log
    }
    if (!checkKey('FK_lista_roles_roles', 'lista_roles')) { // verificar si no existe la clave foranea
        pdoQuery("ALTER TABLE `lista_roles` ADD CONSTRAINT `FK_lista_roles_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
        fileLog("Se agrego las clave foranea a la tabla \"lista_roles\"", $pathLog); // escribir en el log
    } else { // si existe la clave foranea
        fileLog("Ya existe la clave foranea \"FK_lista_roles_roles\" de la tabla \"lista_roles\"", $pathLog); // escribir en el log
    }
    if (!checkKey('FK_mod_roles_roles', 'mod_roles')) { // verificar si no existe la clave foranea
        pdoQuery("ALTER TABLE `mod_roles` ADD CONSTRAINT `FK_mod_roles_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
        fileLog("Se agrego las clave foranea a la tabla \"mod_roles\"", $pathLog); // escribir en el log
    } else { // si existe la clave foranea
        fileLog("Ya existe la clave foranea \"FK_mod_roles_roles\" de la tabla \"mod_roles\"", $pathLog); // escribir en el log
    }
    if (!checkKey('FK_conv_roles_roles', 'conv_roles')) { // verificar si no existe la clave foranea
        pdoQuery("ALTER TABLE `conv_roles` ADD CONSTRAINT `FK_conv_roles_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
        fileLog("Se agrego las clave foranea a la tabla \"conv_roles\"", $pathLog); // escribir en el log
    } else { // si existe la clave foranea
        fileLog("Ya existe la clave foranea \"FK_conv_roles_roles\" de la tabla \"conv_roles\"", $pathLog); // escribir en el log
    }
    if (!checkKey('FK_emp_roles_roles', 'emp_roles')) { // verificar si no existe la clave foranea
        pdoQuery("ALTER TABLE `emp_roles` ADD CONSTRAINT `FK_emp_roles_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
        fileLog("Se agrego las clave foranea a la tabla \"emp_roles\"", $pathLog); // escribir en el log
    } else { // si existe la clave foranea
        fileLog("Ya existe la clave foranea \"FK_emp_roles_roles\" de la tabla \"emp_roles\"", $pathLog); // escribir en el log
    }
    if (!checkKey('FK_grup_roles_roles', 'grup_roles')) { // verificar si no existe la clave foranea
        pdoQuery("ALTER TABLE `grup_roles` ADD CONSTRAINT `FK_grup_roles_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
        fileLog("Se agrego las clave foranea a la tabla \"grup_roles\"", $pathLog); // escribir en el log
    } else { // si existe la clave foranea
        fileLog("Ya existe la clave foranea \"FK_grup_roles_roles\" de la tabla \"grup_roles\"", $pathLog); // escribir en el log
    }
    if (!checkKey('FK_plan_roles_roles', 'plan_roles')) { // verificar si no existe la clave foranea
        pdoQuery("ALTER TABLE `plan_roles` ADD CONSTRAINT `FK_plan_roles_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
        fileLog("Se agrego las clave foranea a la tabla \"plan_roles\"", $pathLog); // escribir en el log
    } else { // si existe la clave foranea
        fileLog("Ya existe la clave foranea \"FK_plan_roles_roles\" de la tabla \"plan_roles\"", $pathLog); // escribir en el log
    }
    if (!checkKey('FK_secc_roles_roles', 'secc_roles')) { // verificar si no existe la clave foranea
        pdoQuery("ALTER TABLE `secc_roles` ADD CONSTRAINT `FK_secc_roles_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
        fileLog("Se agrego las clave foranea a la tabla \"secc_roles\"", $pathLog); // escribir en el log
    } else { // si existe la clave foranea
        fileLog("Ya existe la clave foranea \"FK_secc_roles_roles\" de la tabla \"secc_roles\"", $pathLog); // escribir en el log
    }
    if (!checkKey('FK_sect_roles_roles', 'sect_roles')) { // verificar si no existe la clave foranea
        pdoQuery("ALTER TABLE `sect_roles` ADD CONSTRAINT `FK_sect_roles_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
        fileLog("Se agrego las clave foranea a la tabla \"sect_roles\"", $pathLog); // escribir en el log
    } else { // si existe la clave foranea
        fileLog("Ya existe la clave foranea \"FK_sect_roles_roles\" de la tabla \"sect_roles\"", $pathLog); // escribir en el log
    }
    if (!checkKey('FK_suc_roles_roles', 'suc_roles')) { // verificar si no existe la clave foranea
        pdoQuery("ALTER TABLE `suc_roles` ADD CONSTRAINT `FK_suc_roles_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
        fileLog("Se agrego las clave foranea a la tabla \"suc_roles\"", $pathLog); // escribir en el log
    } else { // si existe la clave foranea
        fileLog("Ya existe la clave foranea \"FK_suc_roles_roles\" de la tabla \"suc_roles\"", $pathLog); // escribir en el log
    }
    if (!checkKey('FK_usuario_roles', 'usuarios')) { // verificar si no existe la clave foranea
        pdoQuery("ALTER TABLE `usuarios` ADD CONSTRAINT `FK_usuario_roles` FOREIGN KEY (`rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION"); // 29/09/2021
        fileLog("Se agrego las clave foranea a la tabla \"usuarios\"", $pathLog); // escribir en el log
    } else { // si existe la clave foranea
        fileLog("Ya existe la clave foranea \"FK_usuario_roles\" de la tabla \"usuarios\"", $pathLog); // escribir en el log
    }
    $verDB = 20210102; // nueva version de la DB
    // simpleQuery("UPDATE params set valores = $verDB WHERE modulo = 0", $link); // seteo la fecha de actualización de la version de DB
    // fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20211006) {
    if (!checkKey('FK_lista_estruct_usuarios', 'lista_estruct')) {
        pdoQuery("ALTER TABLE `lista_estruct` ADD CONSTRAINT `FK_lista_estruct_usuarios` FOREIGN KEY (`uid`) REFERENCES `usuarios` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE");
        fileLog("Se agrego clave foranea \"FK_lista_estruct_usuarios\" de la tabla \"lista_estruct\"", $pathLog); // escribir en el log  
    } else {
        pdoQuery("ALTER TABLE `lista_estruct` DROP FOREIGN KEY `FK_lista_estruct_usuarios`");
        fileLog("Se elimino clave foranea \"FK_lista_estruct_usuarios\" de la tabla \"lista_estruct\"", $pathLog); // escribir en el log     
        pdoQuery("ALTER TABLE `lista_estruct` ADD CONSTRAINT `FK_lista_estruct_usuarios` FOREIGN KEY (`uid`) REFERENCES `usuarios` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE");
        fileLog("Se agrego clave foranea \"FK_lista_estruct_usuarios\" de la tabla \"lista_estruct\"", $pathLog); // escribir en el log 
    }
    $verDB = 20211006;
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20211024) {
    pdoQuery("ALTER TABLE `clientes` CHANGE COLUMN `nombre` `nombre` VARCHAR(50) NOT NULL COLLATE 'utf8_general_ci' AFTER `id`");
    fileLog("Se actualizo tabla \"clientes\"", $pathLog); // escribir en el log
    pdoQuery("ALTER TABLE `modulos`	CHANGE COLUMN `nombre` `nombre` VARCHAR(20) NOT NULL DEFAULT '0' COLLATE 'utf8_general_ci' AFTER `id`");
    fileLog("Se actualizo tabla \"modulos\"", $pathLog); // escribir en el log
    if (!checkTable('auditoria')) {
        $table_auditoria = "CREATE TABLE IF NOT EXISTS `auditoria` ( `id` INT(11) NOT NULL AUTO_INCREMENT, `id_sesion` INT(11) NOT NULL, `usuario` VARCHAR(50) NOT NULL COLLATE 'utf8_general_ci', `nombre` VARCHAR(50) NOT NULL COLLATE 'utf8_general_ci', `cuenta` INT(11) NOT NULL, `audcuenta` INT(11) NOT NULL, `fecha` DATE NOT NULL, `hora` TIME NOT NULL, `tipo` ENUM('A','B','M','P') NULL DEFAULT NULL COLLATE 'utf8_general_ci', `dato` VARCHAR(200) NOT NULL COLLATE 'utf8_general_ci', `modulo` INT(11) NOT NULL, `fechahora` DATETIME NOT NULL DEFAULT current_timestamp(), PRIMARY KEY (`id`) USING BTREE, INDEX `FK_auditoria_login_logs` (`id_sesion`) USING BTREE, INDEX `FK_auditoria_clientes` (`cuenta`) USING BTREE, INDEX `FK_auditoria_clientes_2` (`audcuenta`) USING BTREE, CONSTRAINT `FK_auditoria_clientes` FOREIGN KEY (`cuenta`) REFERENCES `clientes` (`id`) ON UPDATE NO ACTION ON DELETE RESTRICT, CONSTRAINT `FK_auditoria_clientes_2` FOREIGN KEY (`audcuenta`) REFERENCES `clientes` (`id`) ON UPDATE NO ACTION ON DELETE RESTRICT, CONSTRAINT `FK_auditoria_login_logs` FOREIGN KEY (`id_sesion`) REFERENCES `login_logs` (`id`) ON UPDATE NO ACTION ON DELETE RESTRICT ) COLLATE='utf8_general_ci' ENGINE=InnoDB AUTO_INCREMENT=0";
        pdoQuery($table_auditoria);
        fileLog("Se creo la tabla \"auditoria\"", $pathLog); // escribir en el log
    } else {
        fileLog("No se creo tabla: \"auditoria\". ya existe", $pathLog); // escribir en el log
    }
    $verDB = 20211024;
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20220301) {
    pdoQuery("ALTER TABLE `reg_` CHANGE COLUMN `attphoto` `attphoto` ENUM('0','1') NOT NULL DEFAULT '0' COLLATE 'utf8_general_ci' AFTER `appVersion`");
    fileLog("Se actualizo tabla \"reg_\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_` CHANGE COLUMN `operation` `operation` VARCHAR(50) NOT NULL DEFAULT '' AFTER `operationType`");
    fileLog("Se actualizo tabla \"reg_\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `clientes` ADD COLUMN `ApiMobileHRP` VARCHAR(30) NOT NULL AFTER `WebService`");
    fileLog("Se creo tabla \"ApiMobileHRP\"", $pathLog); // escribir en el log

    pdoQuery("UPDATE reg_ SET reg_.attphoto = '0' WHERE reg_.eventType = 2");
    fileLog("Se actualizo tabla \"reg_\" columna \"attphoto\"", $pathLog); // escribir en el log

    pdoQuery("UPDATE reg_ SET reg_.attphoto = '1' WHERE reg_.operationType = 3");
    fileLog("Se actualizo tabla \"reg_\" columna \"attphoto\"", $pathLog); // escribir en el log

    pdoQuery("UPDATE reg_ SET reg_.attphoto = '1' WHERE reg_.operationType = 1");
    fileLog("Se actualizo tabla \"reg_\" columna \"attphoto\"", $pathLog); // escribir en el log

    $verDB = 20220301; // nueva version de la DB // 20211006
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20220303) {

    pdoQuery("ALTER TABLE `reg_` CHANGE COLUMN `id_company` `id_company` SMALLINT NOT NULL DEFAULT 0 AFTER `id_user`");
    fileLog("Se actualizo tabla \"reg_\" columna \"id_company\"", $pathLog); // escribir en el log

    if (!checkTable('reg_phone_')) {
        $table_reg_phone_ = "CREATE TABLE IF NOT EXISTS `reg_phone_` ( `id` INT NOT NULL AUTO_INCREMENT, `phoneid` VARCHAR(20) NOT NULL, `nombre` VARCHAR(50) NOT NULL, `evento` SMALLINT NOT NULL, `fechahora` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE currENT_TIMESTAMP(), PRIMARY KEY (`id`) ) COLLATE='utf8_general_ci'";
        pdoQuery($table_reg_phone_);
        fileLog("Se creo la tabla \"reg_phone_\"", $pathLog); // escribir en el log
    } else {
        fileLog("No se creo tabla: \"reg_phone_\". ya existe", $pathLog); // escribir en el log
    }

    pdoQuery("ALTER TABLE `reg_phone_` ADD COLUMN `id_company` SMALLINT NOT NULL DEFAULT 0 AFTER `phoneid`;");
    fileLog("Se creo columna \"id_company\" en tabla \"reg_phone_\"", $pathLog); // escribir en el log

    $verDB = 20220303; // nueva version de la DB // 20211006
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20220304) {
    pdoQuery("RENAME TABLE `reg_phone_` TO `reg_device_`");
    fileLog("Se renombro tabla \"reg_phone_\" por \"reg_device_\"", $pathLog); // escribir en el log

    $verDB = 20220304; // nueva version de la DB // 20211006
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20220310) {
    pdoQuery("ALTER IGNORE TABLE `reg_` ADD UNIQUE INDEX(id_user, phoneid, fechaHora)");
    fileLog("Se crea indice \"id_user\" Unique de columnas id_user, phoneid, fechaHora. Se eliminaron registros duplicados en tabla \"reg_\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_` DROP INDEX `id_user`, ADD UNIQUE INDEX `unique-reg` (`id_user`, `phoneid`, `fechaHora`) USING BTREE");
    fileLog("Se renombra indice \"id_user\" a \"`unique-reg\" en tabla \"reg_\"", $pathLog); // escribir en el log

    pdoQuery("SET @num := 0; UPDATE reg_ SET rid = @num := (@num+1); ALTER TABLE reg_ AUTO_INCREMENT = 1");
    fileLog("se reestablece el autoincrement de tabla \"reg_\"", $pathLog); // escribir en el log

    $verDB = 20220310; // nueva version de la DB // 20211006
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20220311) {
    pdoQuery("ALTER TABLE `reg_user_` CHANGE COLUMN `id_user` `id_user` BIGINT(11) NOT NULL DEFAULT 0 AFTER `nombre`");
    fileLog("ALTER TABLE \"reg_user_\" CHANGE COLUMN \"id_user\"  BIGINT(11)", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_user_` CHANGE COLUMN `id_company` `id_company` SMALLINT NOT NULL DEFAULT 0 AFTER `id_user`");
    fileLog("ALTER TABLE \"reg_user_\" CHANGE COLUMN \"id_company\" \"id_company\" SMALLINT", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_` CHANGE COLUMN `id_user` `id_user` BIGINT NOT NULL DEFAULT 0 AFTER `rid`");
    fileLog("ALTER TABLE \"reg_\" CHANGE COLUMN \"id_user\" BIGINT", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_` ENGINE=MyISAM");
    fileLog("ALTER TABLE \"reg_\" ENGINE=MyISAM", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_user_` ENGINE=MyISAM");
    fileLog("ALTER TABLE \"reg_user_\" ENGINE=MyISAM", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_device_` ENGINE=MyISAM");
    fileLog("ALTER TABLE \"reg_device_\" ENGINE=MyISAM", $pathLog); // escribir en el log

    $verDB = 20220311; // nueva version de la DB // 20211006
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20220313) {

    pdoQuery("CREATE TABLE IF NOT EXISTS `reg_zones` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `id_company` SMALLINT(6) NOT NULL DEFAULT '0',
        `nombre` VARCHAR(50) NOT NULL COLLATE 'utf8_general_ci',
        `lat` DECIMAL(10, 7) NOT NULL,
        `lng` DECIMAL(10, 7) NOT NULL,
        `radio` SMALLINT(6) NOT NULL,
        `fechahora` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`) USING BTREE,
        UNIQUE INDEX `nameZone` (`id_company`, `nombre`),
        UNIQUE INDEX `positionZone` (`id_company`, `lat`, `lng`)
    ) COLLATE 'utf8_general_ci' ENGINE = MyISAM");
    fileLog("CREATE TABLE \"reg_zones\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_` ADD COLUMN `idZone` INT NOT NULL AFTER `lng`");
    fileLog("ALTER TABLE \"reg_\" ADD COLUMN \"idZone\" INT", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_` ADD CONSTRAINT `refZone` FOREIGN KEY (`idZone`) REFERENCES `reg_zones` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION");
    fileLog("ALTER TABLE \"reg_\" ADD CONSTRAINT \"refZone\"", $pathLog); // escribir en el log

    $verDB = 20220313; // nueva version de la DB // 20211006
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20220314) {
    pdoQuery("ALTER TABLE `reg_` ADD COLUMN `distance` DECIMAL(10,7) NOT NULL DEFAULT 0 AFTER `idZone`");
    fileLog("ALTER TABLE `reg_` ADD COLUMN `distance`", $pathLog); // escribir en el log

    $verDB = 20220314; // nueva version de la DB // 20211006
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20220316) {

    pdoQuery("ALTER TABLE `reg_` ADD COLUMN `reg_uid` BINARY(8) NOT NULL AFTER `rid`");
    fileLog("ALTER TABLE `reg_` ADD COLUMN `reg_uid`", $pathLog); // escribir en el log

    pdoQuery("UPDATE `reg_` set `reg_uid` = UUID()"); // seteo el uid de los registros
    fileLog("UPDATE `reg_uid`", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_` ADD UNIQUE INDEX `unique-reguid` (`reg_uid`)"); // agregar un indice unico
    fileLog("ALTER TABLE `reg_` ADD UNIQUE INDEX `unique-reguid`", $pathLog); // escribir en el log

    $verDB = 20220316; // nueva version de la DB // 20211006
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log

}
if ($verDB < 20220318) {
    pdoQuery("CREATE TABLE IF NOT EXIStS `reg_faces` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `createdDate` VARCHAR(20) NOT NULL COLLATE 'utf8_general_ci',
        `id_user` BIGINT(20) NOT NULL,
        `id_company` SMALLINT(6) NOT NULL,
        `photo` BLOB NOT NULL,
        PRIMARY KEY (`id`) USING BTREE
    ) COLLATE='utf8_general_ci' ENGINE=MyISAM AUTO_INCREMENT=0");
    fileLog("CREATE TABLE \"reg_faces\"", $pathLog); // escribir en el log

    $verDB = 20220318; // nueva version de la DB // 20220318
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20220502) {
    pdoQuery("ALTER TABLE `reg_` ADD COLUMN `locked` CHAR(5) NOT NULL DEFAULT '0' AFTER `gpsStatus`");
    fileLog("ADD COLUMN \"locked\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_` ADD COLUMN `error` VARCHAR(200) NOT NULL AFTER `locked`");
    fileLog("ADD COLUMN \"error\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_` ADD COLUMN `confidence` DECIMAL(10,7) NOT NULL DEFAULT '0.0000000' AFTER `error`");
    fileLog("ADD COLUMN \"confidence\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_` ADD COLUMN `id_api` INT(11) NOT NULL AFTER `gpsStatus`");
    fileLog("ADD COLUMN \"id_api\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_user_` ADD COLUMN `expiredStart` DATE NULL DEFAULT NULL AFTER `estado` ");
    fileLog("ADD COLUMN \"expiredStart\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_user_` ADD COLUMN `expiredEnd` DATE NULL DEFAULT NULL AFTER `expiredStart`");
    fileLog("ADD COLUMN \"expiredEnd\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_user_` ADD COLUMN `motivo` VARCHAR(75) NOT NULL AFTER `expiredEnd`");
    fileLog("ADD COLUMN \"motivo\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `clientes` ADD COLUMN `UrlAppMobile` VARCHAR(30) NOT NULL AFTER `ApiMobileHRP`");
    fileLog("ADD COLUMN \"UrlAppMobile\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `clientes` ADD COLUMN `localCH` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `auth`");
    fileLog("ADD COLUMN \"localCH\"", $pathLog); // escribir en el log

    $verDB = 20220502; // nueva version de la DB // 20220318
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log


}
if ($verDB < 20220503) {

    pdoQuery("ALTER TABLE `reg_zones` ADD COLUMN `evento` SMALLINT(6) NOT NULL AFTER `radio`");
    fileLog("ADD COLUMN \"evento\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_` ADD COLUMN `eventZone` SMALLINT(6) NOT NULL AFTER `distance`");
    fileLog("ADD COLUMN \"eventZone\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_` ADD COLUMN `eventDevice` SMALLINT(6) NOT NULL AFTER `eventZone`");
    fileLog("ADD COLUMN \"eventDevice\"", $pathLog); // escribir en el log

    $verDB = 20220503; // nueva version de la DB
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log

}
if ($verDB < 20220517) {

    pdoQuery("ALTER TABLE `clientes`
	CHANGE COLUMN `WebService` `WebService` VARCHAR(100) NOT NULL COLLATE 'latin1_swedish_ci' AFTER `tkmobile`,
	CHANGE COLUMN `ApiMobileHRP` `ApiMobileHRP` VARCHAR(100) NOT NULL COLLATE 'latin1_swedish_ci' AFTER `WebService`,
	CHANGE COLUMN `UrlAppMobile` `UrlAppMobile` VARCHAR(100) NOT NULL COLLATE 'latin1_swedish_ci' AFTER `ApiMobileHRP`");
    fileLog("CHANGE COLUMN \"WebService, ApiMobileHRP, UrlAppMobile\"", $pathLog); // escribir en el log

    $verDB = 20220517; // nueva version de la DB
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20220816) {

    if (checkTable('tipo_modulo')) {
        if (!count_pdoQuery("SELECT 1 FROM tipo_modulo where id = 6 LIMIT 1")) { // Si no existe el registro
            $insert_tipo_modulos = "INSERT INTO `tipo_modulo` (`id`, `descripcion`, `estado`) VALUES (6, 'Proyectos', '0')";
            pdoQuery($insert_tipo_modulos);
            fileLog("Se creo tipo modulo \"Proyectos\" en la tabla \"tipo_modulo\"", $pathLog); // escribir en el log
        } else { // Si existe el registro
            fileLog("Ya existe tipo modulo \"Proyectos\" en la tabla \"tipo_modulo\"", $pathLog); // escribir en el log
        }
    }

    if (checkTable('modulos')) {

        $insert_modulos = "INSERT INTO `modulos` VALUES (35, 'Pr0t3c70', 'Proyectos', 1, '0', 6)";
        pdoQuery($insert_modulos);
        fileLog("Se creo modulo \"Proyectos\" en la tabla \"modulos\"", $pathLog); // escribir en el log

        $insert_modulos = "INSERT INTO `modulos` VALUES (36, 'Mis Tareas', 'm1sT4r3a' , 2, '0', 6)";
        pdoQuery($insert_modulos);
        fileLog("Se creo modulo \"Mis Tareas\" en la tabla \"modulos\"", $pathLog); // escribir en el log

        $insert_modulos = "INSERT INTO `modulos` VALUES (37, 'Tareas', 'T4r3a5' , 3, '0', 6)";
        pdoQuery($insert_modulos);
        fileLog("Se creo modulo \"Tareas\" en la tabla \"modulos\"", $pathLog); // escribir en el log

        $insert_modulos = "INSERT INTO `modulos` VALUES (38, 'Estados', '3s74d0s' , 4, '0', 6)";
        pdoQuery($insert_modulos);
        fileLog("Se creo modulo \"Estados\" en la tabla \"modulos\"", $pathLog); // escribir en el log

        $insert_modulos = "INSERT INTO `modulos` VALUES (39, 'Procesos', 'pr0s3s0s' , 5, '0', 6)";
        pdoQuery($insert_modulos);
        fileLog("Se creo modulo \"Procesos\" en la tabla \"modulos\"", $pathLog); // escribir en el log

        $insert_modulos = "INSERT INTO `modulos` VALUES (40, 'Plantilla Procesos', 'p14npr0s' , 6, '0', 6)";
        pdoQuery($insert_modulos);
        fileLog("Se creo modulo \"Plantilla Procesos\" en la tabla \"modulos\"", $pathLog); // escribir en el log

        $insert_modulos = "INSERT INTO `modulos` VALUES (41, 'Planos', 'p14n0s' , 7, '0', 6)";
        pdoQuery($insert_modulos);
        fileLog("Se creo modulo \"Planos\" en la tabla \"modulos\"", $pathLog); // escribir en el log

        $insert_modulos = "INSERT INTO `modulos` VALUES (42, 'Empresas', '3mpr354s' , 8, '0', 6)";
        pdoQuery($insert_modulos);
        fileLog("Se creo modulo \"Empresas\" en la tabla \"modulos\"", $pathLog); // escribir en el log

        $insert_modulos = "INSERT INTO `modulos` VALUES (43, 'Inicio', '1n1c10' , 0, '0', 6)";
        pdoQuery($insert_modulos);
        fileLog("Se creo modulo \"Inicio\" en la tabla \"modulos\"", $pathLog); // escribir en el log
    }

    $tableName = "uident";
    $fields = "`usuario` INT(11) NOT NULL,
    `ident` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
    `login` ENUM('0','1') NOT NULL DEFAULT '0' COLLATE 'utf8_general_ci',
    `descripcion` VARCHAR(50) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
    `expira` DATE NOT NULL,
    `fechahora` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    UNIQUE INDEX `unique_ident` (`ident`) USING BTREE,
    INDEX `FK__usuarios` (`usuario`) USING BTREE,
    INDEX `indice_fecha` (`fechahora`) USING BTREE,
    INDEX `Índice_ident` (`ident`) USING BTREE,
    CONSTRAINT `FK__usuarios` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE";
    $engine = "COLLATE='utf8_general_ci' ENGINE=InnoDB;";
    createTable($tableName, $fields, $engine, $pathLog);

    $tableName = "proy_empresas";
    $fields = "`EmpID` SMALLINT(6) NOT NULL AUTO_INCREMENT,
	`EmpDesc` VARCHAR(200) NOT NULL COLLATE 'utf8_general_ci',
	`EmpTel` VARCHAR(100) NOT NULL COLLATE 'utf8_general_ci',
	`EmpObs` TEXT NOT NULL COLLATE 'utf8_general_ci',
	`EmpAlta` DATETIME NOT NULL,
	`EmpFeHo` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`Cliente` INT(11) NOT NULL,
	PRIMARY KEY (`EmpID`) USING BTREE,
	INDEX `FK_proy_empresas_clientes` (`Cliente`) USING BTREE,
	CONSTRAINT `FK_proy_empresas_clientes` FOREIGN KEY (`Cliente`) REFERENCES `clientes` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION";
    $engine = "COLLATE='utf8_general_ci' ENGINE=InnoDB AUTO_INCREMENT=0";
    createTable($tableName, $fields, $engine, $pathLog);

    $tableName = "proy_estados";
    $fields = "`EstID` SMALLINT(6) NOT NULL AUTO_INCREMENT,
	`EstDesc` VARCHAR(100) NOT NULL COLLATE 'utf8_general_ci',
	`EstColor` CHAR(8) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
	`EstTipo` ENUM('Abierto','Pausado','Cerrado') NOT NULL DEFAULT 'Abierto' COLLATE 'utf8_general_ci',
	`EstAlta` DATETIME NOT NULL,
	`EstFeHo` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`Cliente` INT(11) NOT NULL,
	PRIMARY KEY (`EstID`) USING BTREE,
	INDEX `FK_proy_estados_clientes` (`Cliente`) USING BTREE,
	CONSTRAINT `FK_proy_estados_clientes` FOREIGN KEY (`Cliente`) REFERENCES `clientes` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION";
    $engine = "COLLATE='utf8_general_ci' ENGINE=InnoDB AUTO_INCREMENT=0";
    createTable($tableName, $fields, $engine, $pathLog);

    $tableName = "proy_planos";
    $fields = "`PlanoID` INT(11) NOT NULL AUTO_INCREMENT,
	`PlanoDesc` VARCHAR(100) NOT NULL COLLATE 'utf8_general_ci',
	`PlanoCod` VARCHAR(20) NOT NULL COLLATE 'utf8_general_ci',
	`PlanoObs` TEXT NOT NULL COLLATE 'utf8_general_ci',
	`PlanoAlta` DATETIME NOT NULL,
	`PlanoFeHo` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`Cliente` INT(11) NOT NULL,
	PRIMARY KEY (`PlanoID`) USING BTREE,
	INDEX `FK_proy_planos_clientes` (`Cliente`) USING BTREE,
	CONSTRAINT `FK_proy_planos_clientes` FOREIGN KEY (`Cliente`) REFERENCES `clientes` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION";
    $engine = "COLLATE='utf8_general_ci'
    ENGINE=InnoDB
    AUTO_INCREMENT=0";
    createTable($tableName, $fields, $engine, $pathLog);

    $tableName = "proy_plantillas";
    $fields = "`PlantID` SMALLINT(6) NOT NULL AUTO_INCREMENT,
	`PlantDesc` VARCHAR(100) NOT NULL COLLATE 'utf8_general_ci',
	`PlantAlta` DATETIME NOT NULL,
	`PlantFeHo` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`Cliente` INT(11) NOT NULL,
	PRIMARY KEY (`PlantID`) USING BTREE,
	INDEX `FK_proy_plantillas_clientes` (`Cliente`) USING BTREE,
	CONSTRAINT `FK_proy_plantillas_clientes` FOREIGN KEY (`Cliente`) REFERENCES `clientes` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION";
    $engine = "COLLATE='utf8_general_ci'
    ENGINE=InnoDB
    AUTO_INCREMENT=0";
    createTable($tableName, $fields, $engine, $pathLog);

    $tableName = "proy_plantilla_proc";
    $fields = "`PlaProPlan` SMALLINT(6) NOT NULL,
	`PlaProcesos` TEXT NOT NULL COLLATE 'utf8_general_ci',
	`PlaProtAlta` DATETIME NOT NULL,
	`PlaProFeHo` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	PRIMARY KEY (`PlaProPlan`) USING BTREE,
	CONSTRAINT `FK_proy_plantilla_proc_proy_plantillas` FOREIGN KEY (`PlaProPlan`) REFERENCES `proy_plantillas` (`PlantID`) ON UPDATE NO ACTION ON DELETE CASCADE";
    $engine = "COLLATE='utf8_general_ci'
    ENGINE=InnoDB";
    createTable($tableName, $fields, $engine, $pathLog);

    $tableName = "proy_proceso";
    $fields = "`ProcID` SMALLINT(6) NOT NULL AUTO_INCREMENT,
	`ProcDesc` VARCHAR(100) NOT NULL COLLATE 'utf8_general_ci',
	`ProcObs` TEXT NOT NULL COLLATE 'utf8_general_ci',
	`ProcCost` DECIMAL(20,2) NOT NULL DEFAULT '0.00',
	`ProcAlta` DATETIME NOT NULL,
	`ProcFeHo` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`Cliente` INT(11) NOT NULL,
	PRIMARY KEY (`ProcID`) USING BTREE,
	INDEX `FK_proy_proceso_clientes` (`Cliente`) USING BTREE,
	CONSTRAINT `FK_proy_proceso_clientes` FOREIGN KEY (`Cliente`) REFERENCES `clientes` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION";
    $engine = "COLLATE='utf8_general_ci'
    ENGINE=InnoDB
    AUTO_INCREMENT=0";
    createTable($tableName, $fields, $engine, $pathLog);

    $tableName = "proy_proyectos";
    $fields = "`ProyID` INT(11) NOT NULL AUTO_INCREMENT,
	`ProyDesc` VARCHAR(200) NOT NULL COLLATE 'utf8_general_ci',
	`ProyNom` VARCHAR(200) NOT NULL COLLATE 'utf8_general_ci',
	`ProyEmpr` SMALLINT(6) NOT NULL,
	`ProyPlant` SMALLINT(6) NOT NULL,
	`ProyResp` INT(11) NOT NULL,
	`ProyEsta` SMALLINT(6) NOT NULL,
	`ProyObs` TEXT NOT NULL COLLATE 'utf8_general_ci',
	`ProyIni` DATE NOT NULL,
	`ProyFin` DATE NOT NULL,
	`ProyAlta` DATETIME NOT NULL,
	`ProyFeHo` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`Cliente` INT(11) NOT NULL,
	PRIMARY KEY (`ProyID`) USING BTREE,
	INDEX `FK_proy_proyectos_proy_empresas` (`ProyEmpr`) USING BTREE,
	INDEX `FK_proy_proyectos_proy_plantillas` (`ProyPlant`) USING BTREE,
	INDEX `FK_proy_proyectos_usuarios` (`ProyResp`) USING BTREE,
	INDEX `FK_proy_proyectos_proy_estados` (`ProyEsta`) USING BTREE,
	INDEX `FK_proy_proyectos_clientes` (`Cliente`) USING BTREE,
	CONSTRAINT `FK_proy_proyectos_clientes` FOREIGN KEY (`Cliente`) REFERENCES `clientes` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `FK_proy_proyectos_proy_empresas` FOREIGN KEY (`ProyEmpr`) REFERENCES `proy_empresas` (`EmpID`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `FK_proy_proyectos_proy_estados` FOREIGN KEY (`ProyEsta`) REFERENCES `proy_estados` (`EstID`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `FK_proy_proyectos_proy_plantillas` FOREIGN KEY (`ProyPlant`) REFERENCES `proy_plantillas` (`PlantID`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `FK_proy_proyectos_usuarios` FOREIGN KEY (`ProyResp`) REFERENCES `usuarios` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION";
    $engine = "COLLATE='utf8_general_ci'
    ENGINE=InnoDB
    AUTO_INCREMENT=0";
    createTable($tableName, $fields, $engine, $pathLog);

    $tableName = "proy_tareas";
    $fields = "`TareID` INT(11) NOT NULL AUTO_INCREMENT,
	`TareEmp` SMALLINT(6) NOT NULL,
	`TareProy` INT(11) NOT NULL DEFAULT '0',
	`TareResp` INT(11) NOT NULL,
	`TareProc` SMALLINT(6) NOT NULL,
	`TarePlano` INT(11) NULL DEFAULT '0',
	`TareCost` DECIMAL(20,2) NOT NULL DEFAULT '0.00',
	`TareIni` DATETIME NOT NULL,
	`TareFin` DATETIME NOT NULL,
	`TareFinTipo` ENUM('normal','fichada','turno','manual','modificada') NOT NULL DEFAULT 'normal' COLLATE 'utf8_general_ci',
	`TareFeHo` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`TareEsta` ENUM('0','1') NOT NULL DEFAULT '0' COLLATE 'utf8_general_ci',
	`Cliente` INT(11) NOT NULL,
	PRIMARY KEY (`TareID`) USING BTREE,
	INDEX `FK_proy_tareas_proy_empresas` (`TareEmp`) USING BTREE,
	INDEX `FK_proy_tareas_proy_proyectos` (`TareProy`) USING BTREE,
	INDEX `FK_proy_tareas_usuarios` (`TareResp`) USING BTREE,
	INDEX `FK_proy_tareas_proy_proceso` (`TareProc`) USING BTREE,
	INDEX `FK_proy_tareas_proy_planos` (`TarePlano`) USING BTREE,
	INDEX `FK_proy_tareas_clientes` (`Cliente`) USING BTREE,
	CONSTRAINT `FK_proy_tareas_clientes` FOREIGN KEY (`Cliente`) REFERENCES `clientes` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `FK_proy_tareas_proy_empresas` FOREIGN KEY (`TareEmp`) REFERENCES `proy_empresas` (`EmpID`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `FK_proy_tareas_proy_planos` FOREIGN KEY (`TarePlano`) REFERENCES `proy_planos` (`PlanoID`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `FK_proy_tareas_proy_proceso` FOREIGN KEY (`TareProc`) REFERENCES `proy_proceso` (`ProcID`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `FK_proy_tareas_proy_proyectos` FOREIGN KEY (`TareProy`) REFERENCES `proy_proyectos` (`ProyID`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `FK_proy_tareas_usuarios` FOREIGN KEY (`TareResp`) REFERENCES `usuarios` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION";
    $engine = "COLLATE='utf8_general_ci'
    ENGINE=InnoDB
    AUTO_INCREMENT=0";
    createTable($tableName, $fields, $engine, $pathLog);

    $tableName = "proy_tare_horas";
    $fields = "`TareHorID` INT(11) NOT NULL,
	`TareHorProy` INT(11) NOT NULL,
	`TareHorCost` DECIMAL(20,2) NOT NULL DEFAULT '0.00',
	`TareHorHoras` TIME NOT NULL,
	`TareHorMin` INT(11) NOT NULL,
	`TareHorFeHo` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	INDEX `FK_proy_tare_horas_proy_tareas` (`TareHorID`) USING BTREE";
    $engine = "COLLATE='utf8_general_ci'
    ENGINE=InnoDB";
    createTable($tableName, $fields, $engine, $pathLog);

    $tableName = "proy_tare_horas";
    $fields = "`TareHorID` INT(11) NOT NULL,
	`TareHorProy` INT(11) NOT NULL,
	`TareHorCost` DECIMAL(20,2) NOT NULL DEFAULT '0.00',
	`TareHorHoras` TIME NOT NULL,
	`TareHorMin` INT(11) NOT NULL,
	`TareHorFeHo` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	INDEX `FK_proy_tare_horas_proy_tareas` (`TareHorID`) USING BTREE";
    $engine = "COLLATE='utf8_general_ci'
    ENGINE=InnoDB";
    createTable($tableName, $fields, $engine, $pathLog);

    $verDB = 20220816; // nueva version de la DB // 20220816
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20220822) {
    pdoQuery("ALTER TABLE `proy_planos`
	ADD COLUMN `PlanoEsta` ENUM('0','1') NOT NULL AFTER `PlanoObs`");
    fileLog("ADD COLUMN \"proy_planos\"", $pathLog); // escribir en el log

    $verDB = 20220822; // nueva version de la DB
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20220824) {
    pdoQuery("ALTER TABLE `proy_plantillas`
	ADD COLUMN `PlantMod` INT(10) NULL AFTER `PlantDesc`,
	ADD CONSTRAINT `FK_proy_plantillas_modulos` FOREIGN KEY (`PlantMod`) REFERENCES `modulos` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION;");
    fileLog("ADD COLUMN \"PlantMod\" en la tabla \"proy_plantillas\"", $pathLog); // escribir en el log

    pdoQuery("UPDATE `proy_plantillas` SET `PlantMod`='40'");
    fileLog("UPDATE \"proy_plantillas PlantMod\"", $pathLog); // escribir en el log

    pdoQuery("INSERT INTO `modulos` (`id`, `nombre`, `recid`, `orden`, `idtipo`) VALUES ('44', 'Plantilla Planos', 'p14np1n0', '6', '6')");
    fileLog("Se creo modulo \"Plantilla Planos\" en la tabla \"modulos\"", $pathLog); // escribir en el log

    pdoQuery("CREATE TABLE IF NOT EXISTS  `proy_plantilla_plano` (
        `PlaPlanoID` SMALLINT(5) NOT NULL,
        `PlaPlanos` TEXT NOT NULL COLLATE 'utf8mb3_general_ci',
        `PlaPlanoAlta` DATETIME NOT NULL,
        `PlaPlanoFeHo` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`PlaPlanoID`) USING BTREE,
        CONSTRAINT `FK_proy_plantilla_plano` FOREIGN KEY (`PlaPlanoID`) REFERENCES `proy_plantillas` (`PlantID`) ON UPDATE NO ACTION ON DELETE CASCADE
    )
    COLLATE='utf8mb3_general_ci'
    ENGINE=InnoDB");
    fileLog("CREATE TABLE \"proy_plantilla_plano\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `proy_proyectos`
	ADD COLUMN `ProyPlantPlano` SMALLINT(5) NULL AFTER `ProyPlant`,
	ADD CONSTRAINT `FK_proy_proyectos_proy_plantillas_2` FOREIGN KEY (`ProyPlantPlano`) REFERENCES `proy_plantillas` (`PlantID`) ON UPDATE NO ACTION ON DELETE NO ACTION");
    fileLog("ADD COLUMN \"ProyPlantPlano\" en la tabla \"proy_proyectos\"", $pathLog); // escribir en el log

    $verDB = 20220824; // nueva version de la DB
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20220901) {
    pdoQuery("ALTER TABLE `reg_faces`
	DROP PRIMARY KEY,
	ADD PRIMARY KEY (`id`, `createdDate`, `id_user`) USING BTREE");
    fileLog("ALTER TABLE \"reg_faces\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_`
	ADD COLUMN `threshold` TINYINT NOT NULL DEFAULT '75' AFTER `confidence`");
    fileLog("ALTER TABLE ADD COLUMN \"threshold\"", $pathLog); // escribir en el log

    pdoQuery("UPDATE reg_ r SET r.confidence = (100-r.confidence) WHERE r.confidence > 0");
    fileLog("UPDATE TABLE reg_ \"confidence\"", $pathLog); // escribir en el log

    $verDB = 20220901; // nueva version de la DB
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20221116) {
    pdoQuery("CREATE TABLE IF NOT EXISTS `reg_enroll` (
        `idPunchEvent` INT(10) NOT NULL,
        `faceIdAws` VARCHAR(50) NOT NULL DEFAULT '' COLLATE 'utf8mb3_general_ci',
        `id_company` SMALLINT(5) NOT NULL,
        `id_user` BIGINT(19) NOT NULL,
        `fechahora` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE INDEX `Índice 1` (`idPunchEvent`, `faceIdAws`, `id_company`, `id_user`) USING BTREE
    )COLLATE='utf8mb3_general_ci' ENGINE=MyISAM;");
    fileLog("CREATE TABLE \"reg_enroll\"", $pathLog); // escribir en el log

    $verDB = 20221116; // nueva version de la DB
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20221213) {
    pdoQuery("ALTER TABLE `proy_proyectos` ADD COLUMN `ProyUsePlant` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `ProyEsta`;");
    fileLog("ALTER TABLE \"proy_proyectos\" ADD COLUMN \"ProyUsePlant\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_device_` ADD COLUMN `regid` VARCHAR(200) NOT NULL DEFAULT '' AFTER `evento`");
    fileLog("ALTER TABLE \"reg_device_\" ADD COLUMN \"regid\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_device_` ADD COLUMN `appVersion` VARCHAR(50) NOT NULL DEFAULT '' AFTER `regid`");
    fileLog("ALTER TABLE \"reg_device_\" ADD COLUMN \"appVersion\"", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `proy_tare_horas`
        ADD COLUMN `TareHorHoras2` TIME NOT NULL AFTER `TareHorHoras`,
        ADD COLUMN `TareHorMin2` INT(10) NOT NULL AFTER `TareHorMin`,
        ADD COLUMN `TareHorCost2` DECIMAL(20,2) NOT NULL DEFAULT '0.00' AFTER `TareHorCost`;");
    fileLog("ALTER TABLE \"proy_tare_horas\" ADD COLUMN \"TareHorHoras2, TareHorMin2, TareHorCost2\"", $pathLog); // escribir en el log

    pdoQuery("CREATE TABLE IF NOT EXISTS `proy_tareas_desc` (
        `TarDesUsr` INT(10) NULL DEFAULT NULL,
        `TarDesIni` TIME NOT NULL,
        `TarDesFin` TIME NOT NULL,
        `TarDesEsta` ENUM('0','1') NOT NULL DEFAULT '0' COLLATE 'utf8mb3_general_ci',
        `TarDesFeHo` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE INDEX `UniqueUser` (`TarDesUsr`) USING BTREE,
        INDEX `ÍndiceFecha` (`TarDesFeHo`) USING BTREE,
        CONSTRAINT `FK_proy_tareas_desc_usuarios` FOREIGN KEY (`TarDesUsr`) REFERENCES `usuarios` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
    )
    COLLATE='utf8mb3_general_ci'
    ENGINE=InnoDB;");
    fileLog("CREATE TABLE \"proy_tareas_desc\"", $pathLog); // escribir en el log

    $verDB = 20221213; // nueva version de la DB
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20221221) {
    pdoQuery("UPDATE `modulos` SET `estado`='0' WHERE `id`=19");
    fileLog("Modulo Horarios asignados habilitado", $pathLog); // escribir en el log;

    write_apiKeysFile();
    $verDB = 20221221; // nueva version de la DB
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20230329) {
    pdoQuery("ALTER TABLE `reg_user_` ADD COLUMN `hasArea` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `estado`");
    fileLog("ALTER TABLE `reg_user_` ADD COLUMN `hasArea`", $pathLog); // escribir en el log
    $verDB = 20230329; // nueva version de la DB
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20230331) {
    pdoQuery("ALTER TABLE `reg_device_` ADD INDEX `phoneidCompany` (`phoneid`, `id_company`)");
    fileLog("ALTER TABLE `reg_device_` ADD INDEX `phoneidCompany`", $pathLog); // escribir en el log
    $verDB = 20230331; // nueva version de la DB
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20230403) {

    pdoQuery("ALTER TABLE `reg_` ADD COLUMN `identified` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `threshold`");
    fileLog("ALTER TABLE `reg_` ADD COLUMN `identified`", $pathLog); // escribir en el log

    pdoQuery("UPDATE reg_ SET reg_.`identified` = '1' WHERE reg_.confidence >= reg_.threshold");
    fileLog("UPDATE reg_ SET reg_.`identified`", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_` ADD COLUMN `deviceID` INT NOT NULL AFTER `phoneid`");
    fileLog("ALTER TABLE `reg_` ADD COLUMN `deviceID`", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_` ADD INDEX `refDevice` (`deviceID`)");
    fileLog("ALTER TABLE `reg_` ADD INDEX `refDevice`", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_` ADD INDEX `refDate` (`fechaHora`)");
    fileLog("ALTER TABLE `reg_` ADD INDEX `refDate`", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE `reg_device_` DROP INDEX `phoneidCompany`, ADD UNIQUE INDEX `phoneidCompany` (`phoneid`, `id_company`) USING BTREE");
    fileLog("ALTER TABLE `reg_` ADD UNIQUE INDEX `phoneidCompany`", $pathLog); // escribir en el log

    pdoQuery("SET @count = 0; UPDATE reg_device_ SET reg_device_.id = @count:= @count + 1");
    fileLog("UPDATE reg_device_", $pathLog); // escribir en el log

    pdoQuery("ALTER TABLE reg_device_  AUTO_INCREMENT = 1");
    fileLog("ALTER TABLE reg_device_  AUTO_INCREMENT", $pathLog); // escribir en el log

    pdoQuery("UPDATE reg_ INNER JOIN reg_device_ ON reg_.phoneid = reg_device_.phoneid SET reg_.deviceID = reg_device_.id WHERE reg_.phoneid = reg_device_.phoneid");
    fileLog("UPDATE reg_.deviceID", $pathLog); // escribir en el log


    $verDB = 20230403; // nueva version de la DB
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20230404) {
    write_apiKeysFile();
    $verDB = 20230404; // nueva version de la DB
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20240219) {
    write_apiKeysFile();
    $verDB = 20240219; // nueva version de la DB
    pdoQuery("INSERT INTO `modulos` (`id`, `nombre`, `recid`, `orden`, `idtipo`) VALUES ('45','Reporte de Totales', 'r3p0rt0t', '12', '2');");
    fileLog("Se creó módulo \"Reporte de Totales\"", $pathLog);
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0");
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
