<?php
//---------------------------------------------------------------
// Setup Global Error Levels
//
//--------------------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', 1);
global $loader;
$autoloadFiles   = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php',
];
$autoloaderFound = false;

foreach ($autoloadFiles as $autoloadFile) {
    if (!file_exists($autoloadFile)) {
        continue;
    }
    $loader          = require_once $autoloadFile;
    $autoloaderFound = true;
}

$autoloaderFound or die(
    'You must set up the project dependencies, run the following commands:' . PHP_EOL .
    'curl -s http://getcomposer.org/installer | php' . PHP_EOL .
    'php composer.phar install' . PHP_EOL
);

use ank\migration\MigrationFinder;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Tools\Console\Command;
use Doctrine\Migrations\Tools\Console\Helper\ConfigurationHelper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;

$app = \ank\App::getInstance([
    'siteRoot' => realpath(__dir__ . '/../../../../web'),
    'appPath'  => realpath(__dir__ . '/../../../../'),
]);
$app->init();
$config                   = $app->config('db_config');
$migrationsConfig         = $app->config('migrations') ?: [];
$migrations               = [
    'name'       => 'ANK DB Migrations',
    'name_space' => 'db\migrations',
    'table_name' => 'kl_migration',
    // 'paths'       => dirname($app->getRuntimePath()) . '/db/migrations',
    'paths'      => [],
];
$migrations               = array_merge($migrations, $migrationsConfig);
$migrations['table_name'] = $config['prefix'] . $migrations['table_name'];
function getInputChar($paths)
{
    echo 'Input path index' . PHP_EOL;
    foreach ($paths as $key => $value) {
        echo $key, ' :', $value, PHP_EOL;
    }
    while (!feof(STDIN)) {
        $line = fread(STDIN, 1024);

        return $line;
    }
}

if (is_array($migrations['paths'])) {
    $par = $argv[1] ?? '';
    //生成迁移脚本只生成最后一个路径
    if ($par === 'migrations:generate' || $par === 'generate') {
        $index = 0;
        while (true) {
            $index = intval(getInputChar($migrations['paths']));
            if (isset($migrations['paths'][$index])) {
                break;
            }
        }
        $migrations['paths'] = $migrations['paths'][$index];
        if (!is_dir($migrations['paths'])) {
            @mkdir($migrations['paths'], 777, true);
        }
    }
    else {
        $migrations['paths'] = implode(',', $migrations['paths']);
    }
}

$dbParams = [
    'driver'   => 'pdo_mysql',
    'host'     => $config['server'],
    'port'     => $config['port'],
    'user'     => $config['username'],
    'password' => $config['password'],
    'dbname'   => $config['database_name'],
];

$connection = DriverManager::getConnection($dbParams);
// 迁移组件配置
$configuration = new Configuration($connection);
$configuration->setName($migrations['name']);
$configuration->setMigrationsNamespace($migrations['name_space']);
$configuration->setMigrationsTableName($migrations['table_name']);
$configuration->setMigrationsDirectory($migrations['paths']);
$configuration->setMigrationsFinder(new MigrationFinder());

$helperSet = new HelperSet();
$helperSet->set(new QuestionHelper(), 'question');
$helperSet->set(new ConnectionHelper($connection), 'db');
$helperSet->set(new ConfigurationHelper($connection, $configuration));

$cli = new Application('Doctrine Migrations');
$cli->setCatchExceptions(true);
$cli->setHelperSet($helperSet);

$cli->addCommands([
    new Command\DumpSchemaCommand(),
    new Command\ExecuteCommand(),
    new Command\GenerateCommand(),
    new Command\LatestCommand(),
    new Command\MigrateCommand(),
    new Command\RollupCommand(),
    new Command\StatusCommand(),
    new Command\VersionCommand(),
]);

$cli->run();
