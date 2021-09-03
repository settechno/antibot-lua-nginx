<?php


namespace App;

/**
 * Враппер для класса \Redis
 * Class Redis
 */
class Redis extends \Redis {
    protected const TIMEOUT = 10;

    /**
     * Redis constructor.
     */
    public function __construct(string $host, int $port, ?string $password = null) {
        parent::__construct();

        if (!$this->pconnect($host, $port, self::TIMEOUT)) {
            throw new Exception('Невозможно подключиться к Redis ' . $host . ':' . $port . ' таймаут ' . self::TIMEOUT . ' секунд');
        }

        if ($password) {
            $this->auth($password);
        }
    }
}