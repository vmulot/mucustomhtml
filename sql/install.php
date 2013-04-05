<?php

	// Init
	$sql = array();

	// Create Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'mucustomhtml` (
		  `id_mucustomhtml` int(10) NOT NULL AUTO_INCREMENT,
		  `blockname` varchar(255) NOT NULL,
		  `active` TINYINT(1) unsigned DEFAULT 0,
		  `position` INT(10) unsigned DEFAULT 0,
  		PRIMARY KEY (`id_mucustomhtml`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
		
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'mucustomhtml_lang` (
				`id_mucustomhtml` int(10) unsigned NOT NULL,
				`id_lang` int(10) unsigned NOT NULL,
				`htmlcontent` TEXT NOT NULL,
				`cssclass` varchar(255) NOT NULL,
		  PRIMARY KEY (`id_mucustomhtml`, `id_lang`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';		
	