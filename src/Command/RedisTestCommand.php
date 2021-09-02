<?php

namespace Console\Command;

use Console\Redis;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisTestCommand extends Command {
    protected static $defaultName = 'redis:test';

    protected $testKey = 'asd';
    protected $testValue = 'asd';

    protected function configure(): void
    {
        $this
          ->setDescription('Test redis connection')
          ->setHelp('Test redis connection');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $redis = new Redis();

        $content = $redis->get('antibot:gos');
        file_put_contents('content.lua', $content);
        $redis->set($this->testKey, $this->testValue);
        if ($redis->get($this->testKey) === $this->testValue) {
            $output->write('Redis successfully working');
            return Command::SUCCESS;
        } else {
            $output->write('Redis test failed');
            return Command::FAILURE;
        }
    }
}