<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\DBAL\Types\Type;

// $isDevMode = true;
// $config = Setup::createConfiguration($isDevMode);
// $paths = array(__DIR__."/Resource");

// $driver = new Doctrine\ORM\Mapping\Driver\AnnotationDriver;
// $config->setMetadataDriverImpl($driver);
// $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver($paths, $useSimpleAnnotationReader));
// // if (extension_loaded('apc')) {
// // 	$config->setQueryCacheImpl(new \Doctrine\Common\Cache\ApcCache());
// // } else {
// // 	$config->setQueryCacheImpl(new \Doctrine\Commmon\Cache\MemcacheCache());
// // }
// $cache = new Doctrine\Common\Cache\ArrayCache;
// $conn = array(
// 	'driver' => 'pdo_mysql',
// 	'user' => 'user',
// 	'password' => '1234567',
// 	'dbname' => 'publications',
// 	'mapping_types' => array(
// 		'enum' => 'string'
// 		)
// 	);

// Doctrine\Common\Annotations\AnnotationRegistry::registerFile(
//     __DIR__.'/../vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php'
// );


// #$conn->getDatabasePlatform()->registerDoctrineTypeMapping('mediainfotype', 'Uppu3\Type\MediaInfoType');
// $annotationReader = new Doctrine\Common\Annotations\AnnotationReader;
// $cachedAnnotationReader = new Doctrine\Common\Annotations\CachedReader(
//     $annotationReader, // use reader
//     $cache
// );
// $evm = new Doctrine\Common\EventManager();
// $treeListener = new Gedmo\Tree\TreeListener;
// $treeListener->setAnnotationReader($cachedAnnotationReader);
// $evm->addEventSubscriber($treeListener);

// $treeListener = new Gedmo\Tree\TreeListener();
// $annotationReader = new Doctrine\Common\Annotations\AnnotationReader();

// $treeListener->setAnnotationReader($annotationReader);
// //var_dump($treeListener);die();
// #$evm->addEventSubscriber($treeListener);

// $evm->addEventSubscriber(new Doctrine\DBAL\Event\Listeners\MysqlSessionInit());
// //var_dump($evm);die();
// $entityManager = EntityManager::create($conn, $config, $evm);

// //var_dump($entityManager);die();
// Type::addType('mediainfotype', 'Uppu3\Type\MediaInfoType');

// globally used cache driver, in production use APC or memcached
$cache = new Doctrine\Common\Cache\ArrayCache;
// standard annotation reader
$annotationReader = new Doctrine\Common\Annotations\AnnotationReader;
$cachedAnnotationReader = new Doctrine\Common\Annotations\CachedReader(
    $annotationReader, // use reader
    $cache // and a cache driver
);
// create a driver chain for metadata reading
$driverChain = new Doctrine\ORM\Mapping\Driver\DriverChain();
// load superclass metadata mapping only, into driver chain
// also registers Gedmo annotations.NOTE: you can personalize it
Gedmo\DoctrineExtensions::registerAbstractMappingIntoDriverChainORM(
    $driverChain, // our metadata driver chain, to hook into
    $cachedAnnotationReader // our cached annotation reader
);

// now we want to register our application entities,
// for that we need another metadata driver used for Entity namespace
$annotationDriver = new Doctrine\ORM\Mapping\Driver\AnnotationDriver(
    $cachedAnnotationReader, // our cached annotation reader
    array(__DIR__."/Resource/") // paths to look in
);

// NOTE: driver for application Entity can be different, Yaml, Xml or whatever
// register annotation driver for our application Entity namespace
$driverChain->addDriver($annotationDriver, 'Entity');

// general ORM configuration
$config = new Doctrine\ORM\Configuration;
$config->setProxyDir(sys_get_temp_dir());
$config->setProxyNamespace('Proxy');
$config->setAutoGenerateProxyClasses(false); // this can be based on production config.
// register metadata driver
$config->setMetadataDriverImpl($driverChain);
// use our already initialized cache driver
$config->setMetadataCacheImpl($cache);
$config->setQueryCacheImpl($cache);

// create event manager and hook preferred extension listeners
$evm = new Doctrine\Common\EventManager();
// gedmo extension listeners, remove which are not used
$treeListener = new Gedmo\Tree\TreeListener;
$treeListener->setAnnotationReader($cachedAnnotationReader);
$evm->addEventSubscriber($treeListener);
// mysql set names UTF-8 if required
$evm->addEventSubscriber(new Doctrine\DBAL\Event\Listeners\MysqlSessionInit());
// DBAL connection
$connection = array(
	'driver' => 'pdo_mysql',
	'user' => 'user',
	'password' => '1234567',
	'dbname' => 'publications',
	'mapping_types' => array(
		'enum' => 'string'
		)
	);
// Finally, create entity manager
$entityManager = Doctrine\ORM\EntityManager::create($connection, $config, $evm);




