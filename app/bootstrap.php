<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

$cache = getCache();
$cachedAnnotationReader = getAnnotationReader($cache);
$evm = doctrineInit($cachedAnnotationReader);
$config = createDoctrineConfig($cache, $cachedAnnotationReader);

$dbconfig = parse_ini_file('config.ini');

$conn = array(
    'driver' => 'pdo_mysql',
    'user' => $dbconfig['user'],
    'password' => $dbconfig['password'],
    'dbname' => $dbconfig['dbname'],
    'mapping_types' => array(
        'enum' => 'string'
    )
);
$entityManager = EntityManager::create($conn, $config, $evm);


function doctrineInit($cachedAnnotationReader)
{

    $evm = new Doctrine\Common\EventManager();
    addTreeExtension($evm, $cachedAnnotationReader);
    Type::addType('mediainfotype', 'Uppu3\Type\MediaInfoType');
    return $evm;
}

function getCache()
{
    if (extension_loaded('apc')) {
        $cache = new \Doctrine\Common\Cache\ApcCache();
    } else {
        $cache = new \Doctrine\Common\Cache\PhpFileCache();
    }
    return $cache;
}

function getAnnotationReader($cache)
{
    $annotationReader = new AnnotationReader;
    $cachedAnnotationReader = new Doctrine\Common\Annotations\CachedReader(
        $annotationReader, // use reader
        $cache // and a cache driver
    );
    return $cachedAnnotationReader;
}

function createDoctrineConfig($cache, $cachedAnnotationReader)
{
    AnnotationRegistry::registerFile(dirname(__DIR__) . "/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php");
//$cache = new Doctrine\Common\Cache\ArrayCache;
//    if (extension_loaded('apc')) {
//        $cache = new \Doctrine\Common\Cache\ApcCache();
//    } else {
//        $cache = new \Doctrine\Common\Cache\PhpFileCache();
//    }
    $isDevMode = true;

//    $annotationReader = new AnnotationReader;
//    $cachedAnnotationReader = new Doctrine\Common\Annotations\CachedReader(
//        $annotationReader, // use reader
//        $cache // and a cache driver
//    );

    $annotationDriver = new Doctrine\ORM\Mapping\Driver\AnnotationDriver(
        $cachedAnnotationReader, // our cached annotation reader
        array(__DIR__ . '/Resource') // paths to look in
    );


    $driverChain = new Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain();
    Gedmo\DoctrineExtensions::registerAbstractMappingIntoDriverChainORM(
        $driverChain, // our metadata driver chain, to hook into
        $cachedAnnotationReader // our cached annotation reader
    );
    $driverChain->addDriver($annotationDriver, 'Uppu3\Entity');

    $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/Resource"), $isDevMode); //!!!!!!
    $config = new Doctrine\ORM\Configuration;
    $config->setProxyDir(sys_get_temp_dir());
    $config->setProxyNamespace('Proxy');
    $config->setAutoGenerateProxyClasses(true); // this can be based on production config.
// register metadata driver
    $config->setMetadataDriverImpl($driverChain);
// use our already initialized cache driver
    $config->setMetadataCacheImpl($cache);
    $config->setQueryCacheImpl($cache);
    $deleted = $cache->deleteAll();
    return $config;
}

function addTreeExtension($evm, $cachedAnnotationReader)
{
    $treeListener = new Gedmo\Tree\TreeListener;
    $treeListener->setAnnotationReader($cachedAnnotationReader);
    $evm->addEventSubscriber($treeListener);
    $evm->addEventSubscriber(new Doctrine\DBAL\Event\Listeners\MysqlSessionInit());
}