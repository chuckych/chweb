<?php
if ($verDB < 20210102) {
    $pathLog = __DIR__ . '../../logs/info/' . date('Ymd') . '_cambios_db.log';
    if (!CountRegMayorCeroMySql("SELECT 1 FROM modulos where id = 29 LIMIT 1")) {
        InsertRegistroMySql("INSERT INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES ('29', 'FFeVjsix', 'Informe Presentismo', 13, '0', 2)");
        fileLog("Se inserto el modulo: \"Informe Presentismo\"", $pathLog); // escribir en el log
    }
    if (!CountRegMayorCeroMySql("SELECT 1 FROM modulos where id = 30 LIMIT 1")) {
        InsertRegistroMySql("INSERT INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES ('30', 'geD-wzy1', 'Datos', 10, '0', 3)");
        fileLog("Se inserto el modulo: \"Datos\"", $pathLog); // escribir en el log
    }
    if (!CountRegMayorCeroMySql("SELECT 1 FROM modulos where id = 31 LIMIT 1")) {
        InsertRegistroMySql("INSERT INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES ('31', '357ruc7a', 'Estructura', 11, '0', 3)");
        fileLog("Se inserto el modulo: \"Estructura\"", $pathLog); // escribir en el log
    }
    if (!CountRegMayorCeroMySql("SELECT 1 FROM modulos where id = 32 LIMIT 1")) {
        InsertRegistroMySql("INSERT INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES ('32', 'm0b1l3Hr', 'Mobile HRP', 30, '0', 4)");
        fileLog("Se inserto el modulo: \"Mobile HRP\"", $pathLog); // escribir en el log
    }
    if (!CountRegMayorCeroMySql("SELECT 1 FROM modulos where id = 33 LIMIT 1")) {
        InsertRegistroMySql("INSERT INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES ('33', 'H0r4r10s', 'Horarios', 30, '0', 1)");
        fileLog("Se inserto el modulo: \"Horarios\"", $pathLog); // escribir en el log
    }
    if (!CountRegMayorCeroMySql("SELECT 1 FROM modulos where id = 34 LIMIT 1")) {
        InsertRegistroMySql("INSERT INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES ('34', '1nf0rf4r', 'Informe FAR', 14, '1', 2)");
        fileLog("Se inserto el modulo: \"Informe FAR\"", $pathLog); // escribir en el log
    }

    InsertRegistroMySql("UPDATE mod_roles SET mod_roles.id_rol = (SELECT roles.id FROM roles WHERE roles.recid = mod_roles.recid_rol) WHERE mod_roles.id_rol = 0");
    fileLog("Se actualizaron valores de la columna \"id_rol\" en la tabla \"mod_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("UPDATE abm_roles SET abm_roles.id_rol = (SELECT roles.id FROM roles WHERE roles.recid = abm_roles.recid_rol) WHERE abm_roles.id_rol = 0");
    fileLog("Se actualizaron valores de la columna \"id_rol\" en la tabla \"abm_roles\"", $pathLog); // escribir en el log
    // InsertRegistroMySql("DELETE FROM abm_roles WHERE abm_roles.id_rol = 0");
    InsertRegistroMySql("DELETE FROM abm_roles WHERE abm_roles.id_rol NOT IN (SELECT roles.id FROM roles)");
    fileLog("Se verificaron valores inconsistentes de la tabla \"abm_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("DELETE FROM mod_roles WHERE mod_roles.id_rol = 0");
    fileLog("Se eliminaron valores inconsistentes de la tabla \"mod_roles\"", $pathLog); // escribir en el log

    $createParamsTable = InsertRegistroMySql("CREATE TABLE IF NOT EXISTS params(modulo TINYINT NULL DEFAULT NULL, descripcion VARCHAR(50) NULL DEFAULT NULL, valores TEXT NULL DEFAULT NULL, cliente TINYINT NULL DEFAULT NULL)");
    fileLog("Se creo la tabla \"params\"", $pathLog); // escribir en el log
    if ($createParamsTable) { // si la tabla se ceo correctamente
        $selDataPresentes  = CountRegMayorCeroMySql("SELECT 1 FROM params WHERE modulo = 29 and descripcion = 'presentes' and cliente = $row[id_cliente] LIMIT 1");
        $selDataAusentes   = CountRegMayorCeroMySql("SELECT 1 FROM params WHERE modulo = 29 and descripcion = 'ausentes' and cliente = $row[id_cliente] LIMIT 1");
        (!$selDataPresentes) ? InsertRegistroMySql("INSERT INTO params (modulo, descripcion, valores, cliente) VALUES ('29', 'presentes', '', $row[id_cliente])") : '';
        fileLog("Se insertaron valores \"presentes\" en tabla \"params\"", $pathLog); // escribir en el log
        (!$selDataAusentes) ? InsertRegistroMySql("INSERT INTO params (modulo, descripcion, valores, cliente) VALUES ('29', 'ausentes', '', $row[id_cliente])") : '';
        fileLog("Se insertaron valores \"ausentes\" en tabla \"params\"", $pathLog); // escribir en el log
    }
    InsertRegistroMySql("CREATE TABLE IF NOT EXISTS `lista_roles` ( `id_rol` TINYINT(4) NOT NULL, `lista` ENUM('0','1','2','3','4','5') NOT NULL DEFAULT '0' COLLATE 'utf8_general_ci', `datos` TEXT NOT NULL COLLATE 'utf8mb4_bin', `fecha` DATETIME NOT NULL, PRIMARY KEY (`id_rol`, `lista`) USING BTREE, CONSTRAINT `FK_lista_roles_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION ) COLLATE='utf8_general_ci' ENGINE=InnoDB");
    fileLog("Se creo la tabla \"lista_roles\"", $pathLog); // escribir en el log

    InsertRegistroMySql("CREATE TABLE IF NOT EXISTS `lista_estruct` (`uid` INT(11) NOT NULL, `lista` ENUM('1','2','3','4','5','6','7','8') NOT NULL COLLATE 'utf8_general_ci', `datos` TEXT NOT NULL COLLATE 'utf8mb4_bin', `fecha` DATETIME NOT NULL, PRIMARY KEY (`uid`, `lista`) USING BTREE, CONSTRAINT `FK_lista_estruct_usuarios` FOREIGN KEY (`uid`) REFERENCES `usuarios` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION) COLLATE='utf8_general_ci' ENGINE=InnoDB");
    fileLog("Se creo la tabla \"lista_estruct\"", $pathLog); // escribir en el log

    InsertRegistroMySql("ALTER TABLE `usuarios` CHANGE COLUMN `recid` `recid` CHAR(8) NOT NULL COLLATE 'latin1_swedish_ci' AFTER `usuario`");
    fileLog("Se cambiaron los campos de la tabla \"usuarios\"", $pathLog); // escribir en el log

    $check_schema_abm_roles = "SELECT information_schema.COLUMNS.COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='$db' AND TABLE_NAME='abm_roles' AND COLUMN_NAME='aTur'";

    if (!CountRegMayorCeroMySql($check_schema_abm_roles)) {
        InsertRegistroMySql("ALTER TABLE `abm_roles` ADD COLUMN `aTur` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `bCit`, ADD COLUMN `mTur` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `aTur`, ADD COLUMN `bTur` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `mTur`");
        fileLog("Se agregaron los campos a la tabla \"abm_roles\"", $pathLog); // escribir en el log
    }

    InsertRegistroMySql("ALTER TABLE `abm_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
    fileLog("Se cambiaron los campos de la tabla \"abm_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("ALTER TABLE `mod_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
    fileLog("Se cambiaron los campos de la tabla \"mod_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("ALTER TABLE `conv_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
    fileLog("Se cambiaron los campos de la tabla \"conv_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("ALTER TABLE `emp_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
    fileLog("Se cambiaron los campos de la tabla \"emp_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("ALTER TABLE `grup_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
    fileLog("Se cambiaron los campos de la tabla \"grup_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("ALTER TABLE `plan_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
    fileLog("Se cambiaron los campos de la tabla \"plan_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("ALTER TABLE `secc_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
    fileLog("Se cambiaron los campos de la tabla \"secc_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("ALTER TABLE `sect_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
    fileLog("Se cambiaron los campos de la tabla \"sect_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("ALTER TABLE `suc_roles` CHANGE COLUMN `id_rol` `id_rol` TINYINT(4) NOT NULL AFTER `id`"); // 29/09/2021
    fileLog("Se cambiaron los campos de la tabla \"suc_roles\"", $pathLog); // escribir en el log

    InsertRegistroMySql("ALTER TABLE `abm_roles` ADD CONSTRAINT `FK_abm_roles_roles` FOREIGN KEY  IF NOT EXISTS (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
    fileLog("Se agregaron las claves foraneas a la tabla \"abm_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("ALTER TABLE `mod_roles` ADD CONSTRAINT `FK_mod_roles_roles` FOREIGN KEY  IF NOT EXISTS (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
    fileLog("Se agregaron las claves foraneas a la tabla \"mod_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("ALTER TABLE `conv_roles` ADD CONSTRAINT `FK_conv_roles_roles` FOREIGN KEY  IF NOT EXISTS (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
    fileLog("Se agregaron las claves foraneas a la tabla \"conv_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("ALTER TABLE `emp_roles` ADD CONSTRAINT `FK_emp_roles_roles` FOREIGN KEY  IF NOT EXISTS (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
    fileLog("Se agregaron las claves foraneas a la tabla \"emp_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("ALTER TABLE `grup_roles` ADD CONSTRAINT `FK_grup_roles_roles` FOREIGN KEY  IF NOT EXISTS (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
    fileLog("Se agregaron las claves foraneas a la tabla \"grup_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("ALTER TABLE `plan_roles` ADD CONSTRAINT `FK_plan_roles_roles` FOREIGN KEY  IF NOT EXISTS (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
    fileLog("Se agregaron las claves foraneas a la tabla \"plan_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("ALTER TABLE `secc_roles` ADD CONSTRAINT `FK_secc_roles_roles` FOREIGN KEY  IF NOT EXISTS (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
    fileLog("Se agregaron las claves foraneas a la tabla \"secc_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("ALTER TABLE `sect_roles` ADD CONSTRAINT `FK_sect_roles_roles` FOREIGN KEY  IF NOT EXISTS (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
    fileLog("Se agregaron las claves foraneas a la tabla \"sect_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("ALTER TABLE `suc_roles` ADD CONSTRAINT `FK_suc_roles_roles` FOREIGN KEY  IF NOT EXISTS (`id_rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE"); // 29/09/2021
    fileLog("Se agregaron las claves foraneas a la tabla \"suc_roles\"", $pathLog); // escribir en el log
    InsertRegistroMySql("ALTER TABLE `usuarios` ADD CONSTRAINT `FK_usuario_roles` FOREIGN KEY  IF NOT EXISTS (`rol`) REFERENCES `roles` (`id`) ON UPDATE NO ACTION"); // 29/09/2021
    fileLog("Se agregaron las claves foraneas a la tabla \"usuarios\"", $pathLog); // escribir en el log
    
    $verDB  = verDBLocal(); // nueva version de la DB
	InsertRegistroMySql("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
// if ($verDB < 20211030) {
//     $verDB  = verDBLocal(); // nueva version de la DB
// 	InsertRegistroMySql("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
//     fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
// }

