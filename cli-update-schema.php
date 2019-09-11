<?php
/**
 * Created by PhpStorm.
 * User: Valentí
 * Date: 05/11/2018
 * Time: 11:54
 */

require_once __DIR__.'/config/vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationException;
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parse(file_get_contents(__DIR__.'/config.yml'));

$paths = array();
foreach ($config['modules'] as $module) {
    $paths[] = $module['path'] . "/src";
}
$isDevMode = $config['general']['dev_mode'];

// the connection configuration
$dbParams = array(
    'driver'    => $config['db']['driver'],
    'user'      => $config['db']['user'],
    'password'  => $config['db']['password'],
    'host'      => $config['db']['host'],
    'dbname'    => $config['db']['dbname']
);

$config = Setup::createConfiguration($isDevMode);
$config->setProxyDir('proxy');
try{
    $driver = new AnnotationDriver(new AnnotationReader(), $paths);
}catch (AnnotationException $ae)
{
    echo $ae->getMessage();
}

// registering noop annotation autoloader - allow all annotations by default
AnnotationRegistry::registerLoader('class_exists');
$config->setMetadataDriverImpl($driver);

try{
    $entityManager = EntityManager::create($dbParams, $config);
}catch (ORMException $oe)
{
    echo $oe->getMessage();
}

$tool = new SchemaTool($entityManager);
$classes = array(
    $entityManager->getClassMetadata('CTIC\App\Account\Domain\Account'),
    $entityManager->getClassMetadata('CTIC\App\Privacy\Domain\Privacy'),
    $entityManager->getClassMetadata('CTIC\App\User\Domain\User'),
    $entityManager->getClassMetadata('CTIC\App\Company\Domain\Company'),
    $entityManager->getClassMetadata('CTIC\App\Iva\Domain\Iva'),
    $entityManager->getClassMetadata('CTIC\App\Irpf\Domain\Irpf'),
    $entityManager->getClassMetadata('CTIC\App\PaymentMethod\Domain\PaymentMethod'),
    $entityManager->getClassMetadata('CTIC\App\PaymentMethod\Domain\PaymentMethodExpire'),
    $entityManager->getClassMetadata('CTIC\App\Rate\Domain\Rate'),
    $entityManager->getClassMetadata('CTIC\App\Bank\Domain\Bank'),
    $entityManager->getClassMetadata('CTIC\App\RealizationArea\Domain\RealizationArea'),
    $entityManager->getClassMetadata('CTIC\App\Origin\Domain\Origin'),
    $entityManager->getClassMetadata('CTIC\Customer\Customer\Domain\Customer'),
    $entityManager->getClassMetadata('CTIC\Customer\Customer\Domain\CustomerObservationCategory'),
    $entityManager->getClassMetadata('CTIC\Customer\Customer\Domain\CustomerContactCategory'),
    $entityManager->getClassMetadata('CTIC\Customer\Customer\Domain\CustomerContact'),
    $entityManager->getClassMetadata('CTIC\Customer\Customer\Domain\CustomerObservation'),
    $entityManager->getClassMetadata('CTIC\Customer\Customer\Domain\CustomerAddress'),
    $entityManager->getClassMetadata('CTIC\Customer\Customer\Domain\CustomerGroup'),
    $entityManager->getClassMetadata('CTIC\Document\Invoice\Domain\Invoice'),
    $entityManager->getClassMetadata('CTIC\Document\Invoice\Domain\InvoiceSeries'),
    $entityManager->getClassMetadata('CTIC\Document\Invoice\Domain\InvoiceSeriesSet'),
    $entityManager->getClassMetadata('CTIC\Document\Invoice\Domain\InvoiceExpiration'),
    $entityManager->getClassMetadata('CTIC\Product\Product\Domain\Product'),
    $entityManager->getClassMetadata('CTIC\Product\Product\Domain\ProductDevice'),
    $entityManager->getClassMetadata('CTIC\Product\Product\Domain\ProductObservation'),
    $entityManager->getClassMetadata('CTIC\Product\Product\Domain\ProductVariant'),
    $entityManager->getClassMetadata('CTIC\Product\Product\Domain\ProductVariantComposed'),
    $entityManager->getClassMetadata('CTIC\Product\Product\Domain\ProductVariantComposedComplement'),
    $entityManager->getClassMetadata('CTIC\Product\Product\Domain\ProductVariantRate'),
    $entityManager->getClassMetadata('CTIC\Product\Option\Domain\Option'),
    $entityManager->getClassMetadata('CTIC\Product\Attribute\Domain\Attribute'),
    $entityManager->getClassMetadata('CTIC\Warehouse\Warehouse\Domain\Warehouse'),
    $entityManager->getClassMetadata('CTIC\Device\Device\Domain\Device'),
    $entityManager->getClassMetadata('CTIC\App\Dashboard\Domain\Dashboard'),
    $entityManager->getClassMetadata('CTIC\App\Permission\Domain\Permission'),
    $entityManager->getClassMetadata('CTIC\App\System\Domain\System'),
    $entityManager->getClassMetadata('CTIC\Grh\Dashboard\Domain\Dashboard'),
    $entityManager->getClassMetadata('CTIC\Grh\Event\Domain\Event'),
    $entityManager->getClassMetadata('CTIC\Grh\Event\Domain\EventEmployee'),
    $entityManager->getClassMetadata('CTIC\Grh\Employee\Domain\EmployeeAttached'),
    $entityManager->getClassMetadata('CTIC\Grh\Employee\Domain\Employee'),
    $entityManager->getClassMetadata('CTIC\Grh\Employee\Domain\EmployeeCategory'),
    $entityManager->getClassMetadata('CTIC\Grh\Employee\Domain\EmployeeArea'),
    $entityManager->getClassMetadata('CTIC\Grh\Employee\Domain\EmployeeLow'),
    $entityManager->getClassMetadata('CTIC\Grh\Employee\Domain\EmployeePersonalAffairs'),
    $entityManager->getClassMetadata('CTIC\Grh\Fichar\Domain\Fichar'),
    $entityManager->getClassMetadata('CTIC\Grh\Report\Domain\Report'),
);
try{
    $tool->updateSchema($classes);
}catch (ToolsException $te)
{
    echo $te->getMessage();
}

return ConsoleRunner::createHelperSet($entityManager);