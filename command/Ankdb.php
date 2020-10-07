<?php


namespace command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ank\Utils;
use ank\App;
use ReflectionClass;

class Ankdb extends Command
{
    // the name of the command (the part after "bin/console")
    // protected static $defaultName = 'model';

    protected function configure()
    {
        $this
            ->setName('db')
            // the short description shown while running "php bin/console list"
            ->setDescription('数据库迁移升级或生成迁移脚本')
            ->addArgument('action', InputArgument::REQUIRED, '参数1：migrate [-n] 迁移到最新版本,-n为不需要确认' . PHP_EOL . '参数2：generate 生成一个迁移脚本');
        // 配置一个可选参数
        //            ->addArgument('optional_argument', InputArgument::OPTIONAL, 'this is a optional argument')
        // 配置选项 isHan，缩写为 -x，可选，不需要值
        //            ->addOption('isHan', 'x', InputOption::VALUE_NONE, '是否汉族');
        // the full command description shown when running the command with
        // the "--help" option
        //            ->setHelp('This command allows you to create a user...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $pathinfo = $input->getArgument('pathinfo');
        // $arr      = explode('/', trim($pathinfo, '/'));
        // if (count($arr) < 3) {
        //     throw new \ank\Exception('pathinfo error');
        // }
        // $app = App::getInstance();
        // $app->run(Request::__make($app, $pathinfo))->send();
        global $argv, $argc;
        array_shift($argv);
        // array_shift($argv);
        array_shift($_SERVER['argv']);
        // array_shift($_SERVER['argv']);
        $argc            -= 1;
        $_SERVER['argc'] -= 1;
        require dirname(__DIR__) . '/bin/ank-db.php';
        return 0;
    }
}