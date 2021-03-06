<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/4/14 0014
 * Time: 12:07
 */
include "./vendor/autoload.php";
\EasySwoole\EasySwoole\Core::getInstance()->initialize()->globalInitialize();

//初始化baseModel和BaseController
$baseController = new \AutomaticGeneration\InitGeneration\BaseController();
$baseController->generate();
$baseModel = new \AutomaticGeneration\InitGeneration\BaseModel();
$baseModel->generate();


go(function (){
    $mysqlConfig = new \EasySwoole\ORM\Db\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));
    $connection = new \EasySwoole\ORM\Db\Connection($mysqlConfig);

    $tableName = 'article_category_list';
    $tableObjectGeneration =  new \EasySwoole\ORM\Utility\TableObjectGeneration($connection, $tableName);
    $schemaInfo = $tableObjectGeneration->generationTable();

    $path = '\\User';
    $modelConfig = new \AutomaticGeneration\Config\ModelConfig();
    $modelConfig->setBaseNamespace("App\\Model" . $path);
    $modelConfig->setTable($schemaInfo);
//    $modelConfig->setBaseDirectory(EASYSWOOLE_ROOT . '/' .\AutomaticGeneration\AppLogic::getAppPath() . 'Model');
    $modelConfig->setTablePre("");
    $modelConfig->setExtendClass(\App\Model\BaseModel::class);
    $modelBuilder = new \AutomaticGeneration\Model($modelConfig);
    $result = $modelBuilder->generate();
    var_dump($result);

//
    $path = '\\Api\\Admin\\User';
    $controllerConfig = new \AutomaticGeneration\Config\ControllerConfig();
    $controllerConfig->setBaseNamespace("App\\HttpController" . $path);
//    $controllerConfig->setBaseDirectory( EASYSWOOLE_ROOT . '/' . $automatic::APP_PATH . '/HttpController/Api/');
    $controllerConfig->setTablePre('');
    $controllerConfig->setTable($schemaInfo);
    $controllerConfig->setExtendClass(\App\HttpController\Base::class);
    $controllerConfig->setModelClass($modelConfig->getBaseNamespace().'\\'.$modelBuilder->getClassName());
    $controllerBuilder = new \AutomaticGeneration\Controller($controllerConfig);
    $result = $controllerBuilder->generate();
    var_dump($result);

    \EasySwoole\Component\Timer::getInstance()->clearAll();


    $path = '\\Admin';
    $unitTestConfig = new \AutomaticGeneration\Config\UnitTestConfig();
    $unitTestConfig->setBaseNamespace("UnitTest". $path);
    $unitTestConfig->setTable($schemaInfo);
    $unitTestConfig->setExtendClass(\UnitTest\BaseTest::class);
    $unitTestConfig->setControllerClass($controllerConfig->getBaseNamespace().'\\'.$controllerBuilder->getClassName());
    $unitTestConfig->setModelClass($modelConfig->getBaseNamespace().'\\'.$modelBuilder->getClassName());
    $unitTestBuilder = new \AutomaticGeneration\UnitTestGeneration\UnitTest($unitTestConfig);

    $result = $unitTestBuilder->generate();
    var_dump($result);
});
