<?php
/**
 * Created by PhpStorm.
 * User: Valentí
 * Date: 05/11/2018
 * Time: 11:54
 */

require_once __DIR__.'/vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use CTIC\App\Account\Domain\Fixture\AccountFixture;
use CTIC\App\Dashboard\Domain\Fixture\DashboardFixture;
use CTIC\App\System\Domain\Fixture\SystemFixture;
use CTIC\App\Company\Domain\Fixture\CompanyFixture;
use CTIC\App\Iva\Domain\Fixture\IvaFixture;
use CTIC\App\Irpf\Domain\Fixture\IrpfFixture;
use CTIC\App\PaymentMethod\Domain\Fixture\PaymentMethodFixture;
use CTIC\App\Rate\Domain\Fixture\RateFixture;
use CTIC\App\Bank\Domain\Fixture\BankFixture;
use CTIC\App\RealizationArea\Domain\Fixture\RealizationAreaFixture;
use CTIC\App\Origin\Domain\Fixture\OriginFixture;
use CTIC\Product\Product\Domain\Fixture\ProductFixture;
use CTIC\Customer\Customer\Domain\Fixture\CustomerObservationCategoryFixture;
use CTIC\Customer\Customer\Domain\Fixture\CustomerContactCategoryFixture;
use CTIC\Customer\Customer\Domain\Fixture\CustomerGroupFixture;
use CTIC\Document\Invoice\Domain\Fixture\InvoiceSeriesSetFixture;
use CTIC\Document\Invoice\Domain\Fixture\InvoiceExpirationFixture;
use CTIC\Warehouse\Warehouse\Domain\Fixture\WarehouseFixture;
use CTIC\Device\Device\Domain\Fixture\DeviceFixture;
use CTIC\Grh\Dashboard\Domain\Fixture\DashboardFixture as DashboardGrhFixture;
use CTIC\Grh\Employee\Domain\Fixture\EmployeeFixture;
use CTIC\Grh\Employee\Domain\Fixture\EmployeeCategoryFixture;
use CTIC\Grh\Employee\Domain\Fixture\EmployeeAreaFixture;
use CTIC\App\Permission\Domain\Fixture\PermissionFixture;
use CTIC\App\Privacy\Domain\Fixture\PrivacyFixture;
use CTIC\App\User\Domain\Fixture\UserFixture;
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

$loader = new Loader();
$loader->addFixture(new AccountFixture());
$loader->addFixture(new PermissionFixture());
$loader->addFixture(new UserFixture());
$loader->addFixture(new CompanyFixture());
$loader->addFixture(new IvaFixture());
$loader->addFixture(new IrpfFixture());
$loader->addFixture(new PaymentMethodFixture());
$loader->addFixture(new RateFixture());
$loader->addFixture(new BankFixture());
$loader->addFixture(new RealizationAreaFixture());
$loader->addFixture(new OriginFixture());
$loader->addFixture(new ProductFixture());
$loader->addFixture(new CustomerObservationCategoryFixture());
$loader->addFixture(new CustomerContactCategoryFixture());
$loader->addFixture(new CustomerGroupFixture());
$loader->addFixture(new InvoiceSeriesSetFixture());
$loader->addFixture(new InvoiceExpirationFixture());
$loader->addFixture(new WarehouseFixture());
$loader->addFixture(new DeviceFixture());
$loader->addFixture(new PrivacyFixture());
$loader->addFixture(new DashboardFixture());
$loader->addFixture(new SystemFixture());
$loader->addFixture(new DashboardGrhFixture());
$loader->addFixture(new EmployeeCategoryFixture());
$loader->addFixture(new EmployeeAreaFixture());

try{
    $purger = new ORMPurger();
    $executor = new ORMExecutor($entityManager, $purger);
    $executor->execute($loader->getFixtures());
}catch (ToolsException $te)
{
    echo $te->getMessage();
}

return ConsoleRunner::createHelperSet($entityManager);