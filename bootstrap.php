<?php
require_once __DIR__."/vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(
	[__DIR__."/src/Domain/Persistant/Entities"], 
	$isDevMode,
	null,
	null,
	false
	);

$connectionParams = [
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'dbname' => 'fulll',
    'user' => 'root',
    'password' => 'pass',
];

$entityManager = EntityManager::create($connectionParams, $config);

function getEntityManager() {
    global $entityManager;
    return $entityManager;
}