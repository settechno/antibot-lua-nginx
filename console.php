#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new Console\Command\RedisTestCommand());
$application->add(new Console\Command\AntibotTestCommand());

// Команды антибота
$application->add(new Console\Command\AntibotBanCommand());
$application->add(new Console\Command\AntibotListCommand());
$application->add(new Console\Command\AntibotClearCommand());

$application->run();