<?php

namespace Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AntibotClearCommand extends AntibotCommand {
    protected static $defaultName = 'antibot:clear';


    protected function configure(): void {
        $this
          ->setDescription('Очистка бан-листа')
          ->setHelp('Очистка бан-листа');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->antibot->clearBanList();
        $output->writeln("Banlist was cleared");

        return Command::SUCCESS;
    }
}