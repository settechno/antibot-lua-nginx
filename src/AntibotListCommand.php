<?php

namespace Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AntibotListCommand extends AntibotCommand {
    protected static $defaultName = 'antibot:list';

    protected function configure(): void {
        $this
          ->setDescription('Просмотр бан-листа')
          ->setHelp('Просмотр бан-листа');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $ips = $this->antibot->getBanList();
        if (count($ips) === 0) {
            $output->writeln("Banlist is empty");
            return Command::SUCCESS;
        }

        foreach ($ips as $ip => $data) {
            $str = "IP $ip was banned till " . date("d.m.Y H:i:s", $data['time']);
            if ($data['url'] !== null) {
                $str .= "(URI {$data['url']})";
            }

            $output->writeln($str);
        }

        return Command::SUCCESS;
    }
}