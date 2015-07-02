<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/Resource"), $isDevMode);

$conn = array(
	'driver' => 'pdo_mysql',
	'user' => 'user',
	'password' => '1234567',
	'dbname' => 'publications',
	'mapping_types' => array(
		'enum' => 'string'
		)
	);
$entityManager = EntityManager::create($conn, $config);