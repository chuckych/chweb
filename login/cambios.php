<?php
$pathLog = __DIR__ . '../../logs/info/' . date('Ymd') . '_cambios_db.log';
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
            insert_pdoQuery("INSERT INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES ('31', '357ruc7a', 'Estructura', 11, '0', 3)", $link);
            fileLog("Se inserto el modulo: \"Estructura\"", $pathLog); // escribir en el log
        } else { // Si existe el registro
            fileLog("El modulo: \"Estructura\" ya existe", $pathLog); // escribir en el log
        }
        if (!count_pdoQuery("SELECT 1 FROM modulos where id = 32 LIMIT 1")) { // Si no existe el registro
            insert_pdoQuery("INSERT INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES ('32', 'm0b1l3Hr', 'Mobile HRP', 30, '0', 4)", $link);
            fileLog("Se inserto el modulo: \"Mobile HRP\"", $pathLog); // escribir en el log
        } else { // Si existe el registro
            fileLog("El modulo: \"Mobile HRP\" ya existe", $pathLog); // escribir en el log
        }
        if (!count_pdoQuery("SELECT 1 FROM modulos where id = 33 LIMIT 1")) { // Si no existe el registro
            insert_pdoQuery("INSERT INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES ('33', 'H0r4r10s', 'Horarios', 30, '0', 1)", $link);
            fileLog("Se inserto el modulo: \"Horarios\"", $pathLog); // escribir en el log
        } else { // Si existe el registro
            fileLog("El modulo: \"Horarios\" ya existe", $pathLog); // escribir en el log
        }
        if (!count_pdoQuery("SELECT 1 FROM modulos where id = 34 LIMIT 1")) { // Si no existe el registro
            insert_pdoQuery("INSERT INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES ('34', '1nf0rf4r', 'Informe FAR', 14, '1', 2)", $link);
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
        pdoQuery("DELETE FROM abm_roles WHERE abm_roles.id_rol NOT IN (SELECT roles.id FROM roles)", $link);
        fileLog("Se verificaron valores inconsistentes de la tabla \"abm_roles\"", $pathLog); // escribir en el log
    } else {
        fileLog("No existe tabla: \"abm_roles\"", $pathLog); // escribir en el log
    }

    if (checkTable('params')) {
        $selDataPresentes  = count_pdoQuery("SELECT 1 FROM params WHERE modulo = 29 and descripcion = 'presentes' and cliente = $row[id_cliente] LIMIT 1");
        $selDataAusentes   = count_pdoQuery("SELECT 1 FROM params WHERE modulo = 29 and descripcion = 'ausentes' and cliente = $row[id_cliente] LIMIT 1");
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
    // $verDB  = verDBLocal(); // nueva version de la DB
    // simpleQuery("UPDATE params set valores = $verDB WHERE modulo = 0", $link); // seteo la fecha de actualización de la version de DB
    // fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
    $a = simpleQueryData("SELECT valores FROM params WHERE modulo = 0 and cliente = 0 LIMIT 1", $link); // Traigo el valor de la version de la DB mysql
    $verDB = intval($a['valores']); // valor de la version de la DB mysql
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
    $verDB  = verDBLocal(); // nueva version de la DB // 20211006
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
    $verDB  = verDBLocal(); // nueva version de la DB // 20211006
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

    $verDB  = verDBLocal(); // nueva version de la DB // 20211006
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

    $verDB  = verDBLocal(); // nueva version de la DB // 20211006
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20220304) {
    pdoQuery("RENAME TABLE `reg_phone_` TO `reg_device_`");
    fileLog("Se renombro tabla \"reg_phone_\" por \"reg_device_\"", $pathLog); // escribir en el log

    $verDB  = verDBLocal(); // nueva version de la DB // 20211006
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

    $verDB  = verDBLocal(); // nueva version de la DB // 20211006
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

    $verDB  = verDBLocal(); // nueva version de la DB // 20211006
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

    $verDB  = verDBLocal(); // nueva version de la DB // 20211006
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}
if ($verDB < 20220314) {
    pdoQuery("ALTER TABLE `reg_` ADD COLUMN `distance` DECIMAL(10,7) NOT NULL DEFAULT 0 AFTER `idZone`");
    fileLog("ALTER TABLE `reg_` ADD COLUMN `distance`", $pathLog); // escribir en el log
    
    $verDB  = verDBLocal(); // nueva version de la DB // 20211006
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
        
    $verDB  = verDBLocal(); // nueva version de la DB // 20211006
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
        
    $verDB  = verDBLocal(); // nueva version de la DB // 20220318
    pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
    fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
}


// if ($verDB < 20211102) {

//     if (checkTable('tipo_modulo')) {
//         if (!count_pdoQuery("SELECT 1 FROM tipo_modulo where id = 6 LIMIT 1")) { // Si no existe el registro
//             $insert_tipo_modulos = "INSERT INTO `tipo_modulo` (`id`, `descripcion`, `estado`) VALUES (6, 'Proyectos', '0')";
//             pdoQuery($insert_tipo_modulos);
//             fileLog("Se creo tipo modulo \"Proyectos\" en la tabla \"tipo_modulo\"", $pathLog); // escribir en el log
//         } else { // Si existe el registro
//             fileLog("Ya existe tipo modulo \"Proyectos\" en la tabla \"tipo_modulo\"", $pathLog); // escribir en el log
//         }
//     }

//     if (checkTable('modulos')) {
//         if (!count_pdoQuery("SELECT 1 FROM modulos where id = 35 LIMIT 1")) { // Si no existe el registro
//             $insert_modulos = "INSERT INTO `modulos` (`id`, `recid`, `nombre`, `orden`, `estado`, `idtipo`) VALUES (35, 'Pr0t3c70', 'Proyectos', 1, '0', 6)";
//             pdoQuery($insert_modulos);
//             fileLog("Se creo modulo \"Proyectos\" en la tabla \"modulos\"", $pathLog); // escribir en el log
//         } else { // Si existe el registro
//             fileLog("Ya existe modulo \"Proyectos\" en la tabla \"modulos\"", $pathLog); // escribir en el log
//         }
//     }
    
//     if (!checkTable('uident')) {
//         $table_ident = "CREATE TABLE IF NOT EXISTS `uident` (
//         `usuario` INT(11) NOT NULL,
//         `ident` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
//         `login` ENUM('0','1') NOT NULL DEFAULT '0' COLLATE 'utf8_general_ci',
//         `descripcion` VARCHAR(50) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
//         `vence` DATE NOT NULL,
//         `fechahora` DATETIME NOT NULL DEFAULT current_timestamp(),
//         UNIQUE INDEX `unique_ident` (`ident`) USING BTREE,
//         INDEX `FK__usuarios` (`usuario`) USING BTREE,
//         INDEX `indice_fecha` (`fechahora`) USING BTREE,
//         CONSTRAINT `FK__usuarios` FOREIGN KEY (`usuario`) REFERENCES `chweb`.`usuarios` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
//     ) COLLATE='utf8_general_ci' ENGINE=InnoDB";

//         pdoQuery($table_ident);
//         if (checkTable('uident')) {
//             fileLog("Se creo la tabla \"uident\"", $pathLog); // escribir en el log
//         } else {
//             fileLog("no se creo tabla: \"uident\"", $pathLog); // escribir en el log
//         }
//     }

//     $verDB  = verDBLocal(); // nueva version de la DB // 20211006
//     pdoQuery("UPDATE params set valores = $verDB WHERE modulo = 0"); // seteo la fecha de actualización de la version de DB
//     fileLog("Se actualizó la fecha de la versión de DB: \"$verDB\"", $pathLog); // escribir en el log
// }
