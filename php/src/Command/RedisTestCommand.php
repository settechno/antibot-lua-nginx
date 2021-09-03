<?php

namespace App\Command;

use App\Redis;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisTestCommand extends Command {
    protected static $defaultName = 'redis:test';

    protected $testKey = 'asd';
    protected $testValue = 'asd';

    protected $redis;

    public function __construct(Redis $redis, string $name = null) {
        parent::__construct($name);
        $this->redis = $redis;
    }

    protected function configure(): void {
        $this
          ->setDescription('Test redis connection')
          ->setHelp('Test redis connection');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->redis->set($this->testKey, $this->testValue);

        if ($this->redis->get($this->testKey) === $this->testValue) {
            $output->writeln('Redis successfully working');
            return Command::SUCCESS;
        } else {
            $output->writeln('Redis test failed');
            return Command::FAILURE;
        }
    }
}