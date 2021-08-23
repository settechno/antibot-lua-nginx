#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new Console\RedisTestCommand());

// Команды антибота
$application->add(new Console\AntibotBanCommand());
$application->add(new Console\AntibotListCommand());
$application->add(new Console\AntibotClearCommand());

$application->run();