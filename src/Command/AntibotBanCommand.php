<?php

namespace Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class AntibotBanCommand extends AntibotCommand {
    protected static $defaultName = 'antibot:ban';

    protected function configure(): void {
        $this
          ->setDescription('Бан IP-адреса')
          ->setHelp('Бан IP-адреса');

        $this
          ->addArgument('ip', InputArgument::REQUIRED, 'строка из IP адресов, разделенных запятой')
          ->addArgument('time', InputArgument::REQUIRED, 'время бана в секундах')
          ->addArgument('url', InputArgument::OPTIONAL, 'адрес страницы, на которую будет запрещен доступ (н.р. /data/auctionlist2.php, /authentication/login). Если не указан адрес - бан на все страницы под защитой антибота');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $ips = explode(',', $input->getArgument('ip'));
        $expiredTime = intval($input->getArgument('time'));
        $url = $input->getArgument('url') ?? null;

        foreach ($ips as $ip) {
            $ip = trim($ip);
            if ($ip === '') {
                continue;
            }

            $this->antibot->banIP($ip, $expiredTime, $url);
            $output->writeln("IP $ip was banned successfull");
        }

        return Command::SUCCESS;
    }
}