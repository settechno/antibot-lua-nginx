<?php


namespace App;

class Antibot {
    /**
     * @var Redis
     */
    protected $cache;

    /**
     * @var string
     */
    protected $cacheKey = '';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Antibot constructor.
     *
     * @param string $cacheKey
     */
    public function __construct(Redis $redis, string $cacheKey) {
        $this->cache = $redis;
        $this->cacheKey = $cacheKey;
    }

    protected function filter(array $data): array {
        return $data;

        $items = [];
        $time = time();
        foreach ($data as $ip => $item) {
            if ($item['time'] > $time) {
                $items[$ip] = $item;
            }
        }

        return $items;
    }

    /**
     * @return array
     */
    protected function loadFromStorage(): array {
        $data = $this->cache->get($this->cacheKey);
        if ($data === false) {
            $data = [];
        } else {
            $data = $this->filter($this->decode($data));
        }

        return $data;
    }

    /**
     * @return bool
     */
    protected function saveToStorage(): bool {
        $maxExpiredTime = 1;
        foreach ($this->data as $key => $value) {
            if ($value['time'] - time() > $maxExpiredTime) {
                $maxExpiredTime = $value['time'] - time();
            }
        }

        return $this->cache->set($this->cacheKey, $this->encode($this->filter($this->data)), $maxExpiredTime);
    }

    /**
     * @param array $data
     *
     * @return string
     */
    protected function encode(array $data): string {
        return json_encode($data);
    }

    /**
     * @param string $data
     *
     * @return array
     */
    protected function decode(string $data): array {
        return json_decode($data, true) ?? [];
    }

    /**
     * Занесение IP-адреса в бан
     *
     * @param string $ip
     * @param int $expiredTime - время бана в секундах
     * @param string|null $url - URL, на который распространяется бан
     */
    public function add(string $ip, int $expiredTime, ?string $url = null): void {
        $this->getBanList();

        $this->data[$ip] = ['time' => time() + $expiredTime];
        if (null !== $url) {
            $this->data[$ip]['url'] = $url;
        }
    }

    /**
     * Удаление IP-адреса из бана
     *
     * @param string $ip
     */
    public function delete(string $ip) {
        if (array_key_exists($ip, $this->getBanList())) {
            unset($this->data[$ip]);
        }
    }

    /**
     * Очистка бан-листа
     */
    public function clearBanList(): void {
        $this->data = [];
        $this->cache->del($this->cacheKey);
    }

    /**
     * Получение списка забаненных адресов
     *
     * @return array
     */
    public function getBanList(): array {
        if (empty($this->data)) {
            $this->data = $this->loadFromStorage();
        }

        return $this->data;
    }

    /**
     * Сохранение антибота
     *
     * @return bool
     */
    public function save(): bool {
        return $this->saveToStorage();
    }

    protected function refreshNginxStorage() {

    }
}