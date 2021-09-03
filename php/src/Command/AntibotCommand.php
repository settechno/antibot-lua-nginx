<?php

namespace App\Command;

use App\Antibot;
use Symfony\Component\Console\Command\Command;

abstract class AntibotCommand extends Command {
    /**
     * @var Antibot
     */
    protected $antibot;

    public function __construct(Antibot $antibot, string $name = null) {
        parent::__construct($name);

        $this->antibot = $antibot;
    }
}