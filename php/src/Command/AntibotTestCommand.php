<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AntibotTestCommand extends AntibotCommand {
    protected static $defaultName = 'antibot:test';

    protected function configure(): void {
        $this
          ->setDescription('Тестирование антибота')
          ->setHelp('Тестирование антибота');

        $this
          ->addArgument('count', InputArgument::REQUIRED, 'Количество забаненных адресов')
          ->addArgument('time', InputArgument::OPTIONAL, 'Время бана');
    }

    protected function generateIp(): string {
        return mt_rand(1, 255) . "." . mt_rand(1, 255) . "." . mt_rand(1, 255) . "." . mt_rand(1, 255);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $count = intval($input->getArgument('count'));
        $time = intval($input->getArgument('time') ?? 600);

        while ($count >= 0) {
            $this->antibot->add($this->generateIp(), $time);
            --$count;
        }

        $this->antibot->save();

        $output->writeln("Ban " . $input->getArgument('count') . " successfully");

        return Command::SUCCESS;
    }
}