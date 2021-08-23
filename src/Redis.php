<?php


namespace Console;

/**
 * Враппер для класса \Redis
 * Class Redis
 */
class Redis extends \Redis {
    protected const TIMEOUT = 10;

    /**
     * Redis constructor.
     */
    public function __construct() {
        parent::__construct();

        if (!$this->pconnect(getenv('REDIS_HOST'), getenv('REDIS_PORT'), self::TIMEOUT)) {
            $errorMsg  = 'Невозможно подключиться к Redis ' . getenv('REDIS_HOST') . ':' . getenv('REDIS_PORT');
            $errorMsg .= ' таймаут ' . self::TIMEOUT . ' секунд';
            throw new Exception($errorMsg);
        }

        // @TODO if necessary
        if (false) {
            $this->auth($params['password']);
        }

        // @TODO if necessary
        if (true) {
            $this->select(0);
        }

        //$this->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
    }
}