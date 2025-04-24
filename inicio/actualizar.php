<?php
require __DIR__ . '/../config/session_start.php';
require __DIR__ . '/../config/index.php';
secure_auth_ch();

if (modulo_cuentas()) {

	//InsertRegistroMySql("ALTER TABLE `modulos` CHANGE COLUMN `id` `id` INT(11) NOT NULL FIRST;");
	//InsertRegistroMySql("ALTER TABLE `modulos` CHANGE COLUMN `nombre` `nombre` VARCHAR(20) NOT NULL DEFAULT '0' COLLATE 'latin1_swedish_ci' AFTER `recid`");
	//InsertRegistroMySql("ALTER TABLE `clientes` CHANGE COLUMN `host` `host` VARCHAR(100) NOT NULL COLLATE 'latin1_swedish_ci' AFTER `nombre`");

	InsertRegistroMySql(
		"REPLACE INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES
	(1, 'EJCrN002', 'Cuentas', 80, '0', 5),
	(2, 'Q4vOuQNR', 'Novedades', 33, '0', 1),
	(3, '5illJcuG', 'Fichadas', 29, '0', 1),
	(4, '7Fgv2-PB', 'General', 10, '0', 1),
	(5, '5i0PeyE8', 'Mobile', 10, '0', 4),
	(6, 'v-uFxTXV', 'Mis Horas', 50, '0', 1),
	(7, 'uzBRa1se', 'Mi Cuenta', 70, '0', 5),
	(8, '8TLv9HuZ', 'Dashboard', 60, '1', 1),
	(9, 'dy46jsIy', 'Cta Cte', 35, '0', 1),
	(10, 'Se45yR4l', 'Personal', 67, '0', 1),
	(11, 'trbd6al2', 'Fichar', 30, '0', 1),
	(12, '63hjsmn2', 'Procesar', 66, '0', 1),
	(13, '46drY75s', 'Cta Cte Horas', 36, '0', 1),
	(14, '5y6R43O9', 'Cierres', 81, '0', 1),
	(15, '9uy6RdX1', 'Liquidar', 82, '0', 1),
	(16, 'H0ra57ra', 'Horas', 31, '0', 1),
	(17, 'o7ra5N0v', 'Otras Novedades', 33, '0', 1),
	(18, '4ud170r1', 'Auditoría', 90, '0', 5),
	(19, 'H0ra551n', 'Horarios Asignados', 10, '1', 2),
	(20, 'P14n1ii4', 'Planilla Horaria', 20, '1', 2),
	(21, 'P4r73d1a', 'Parte Diario', 9, '0', 2),
	(22, '1nf0uQNR', 'Informe de Novedades', 10, '0', 2),
	(23, '1nf0uf1c', 'Informe de Fichadas', 11, '0', 2),
	(24, '1nf0H0r4', 'Informe de Horas', 12, '0', 2),
	(25, 'z0n45m0b', 'Zonas Mobile', 20, '0', 4),
	(26, 'u5u4r10s', 'Usuarios Mobile', 20, '0', 4),
	(27, 'm3n5s4j3', 'Mensajes Mobile', 30, '1', 4),
	(28, 'H0raC057', 'Horas Costeadas', 32,'0', 1)
	"
	);
	InsertRegistroMySql(
		"CREATE TABLE IF NOT EXISTS `abm_roles` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`id_rol` int(11) NOT NULL,
	`recid_rol` char(8) NOT NULL,
	`aFic` enum('0','1') NOT NULL DEFAULT '0',
	`mFic` enum('0','1') NOT NULL DEFAULT '0',
	`bFic` enum('0','1') NOT NULL DEFAULT '0',
	`aNov` enum('0','1') NOT NULL DEFAULT '0',
	`mNov` enum('0','1') NOT NULL DEFAULT '0',
	`bNov` enum('0','1') NOT NULL DEFAULT '0',
	`aHor` enum('0','1') NOT NULL DEFAULT '0',
	`mHor` enum('0','1') NOT NULL DEFAULT '0',
	`bHor` enum('0','1') NOT NULL DEFAULT '0',
	`aONov` enum('0','1') NOT NULL DEFAULT '0',
	`mONov` enum('0','1') NOT NULL DEFAULT '0',
	`bONov` enum('0','1') NOT NULL DEFAULT '0',
	`Proc` enum('0','1') NOT NULL DEFAULT '0',
	`aCit` enum('0','1') NOT NULL DEFAULT '0',
	`mCit` enum('0','1') NOT NULL DEFAULT '0',
	`bCit` enum('0','1') NOT NULL DEFAULT '0',
	`FechaHora` datetime NOT NULL,
	PRIMARY KEY (`id`),
	KEY `id_rol` (`id_rol`),
	KEY `recid_rol` (`recid_rol`)
  ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4"
	);
}