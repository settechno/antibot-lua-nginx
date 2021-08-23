<?php

namespace Console;

use Symfony\Component\Console\Command\Command;

class AntibotCommand extends Command {
    /**
     * @var Antibot
     */
    protected $antibot;

    public function __construct(string $name = null) {
        parent::__construct($name);

        $this->antibot = new Antibot();
    }
}