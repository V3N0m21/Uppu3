<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Types\Type;

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/Resource"), $isDevMode);
if (extension_loaded('apc')) {
	$config->setQueryCacheImpl(new \Doctrine\Common\Cache\ApcCache());
} else {
	$config->setQueryCacheImpl(new \Doctrine\Commmon\Cache\MemcacheCache());
}

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
Type::addType('mediainfotype', 'Uppu3\Resource\MediaInfoType');