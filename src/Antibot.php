<?php


namespace Console;

class Antibot {
    /**
     * @var Redis
     */
    protected $cache;

    /**
     * @var string|null
     */
    protected $cacheKey = '';

    public function __construct() {
        $this->cache = new Redis();
        $this->cacheKey = getenv("REDIS_KEY");
    }

    /**
     * Занесение IP-адреса в бан
     *
     * @param string $ip
     * @param int $expiredTime - время бана в секундах
     * @param string|null $url - URL, на который распространяется бан
     */
    public function banIP(string $ip, int $expiredTime, ?string $url = null): void {
        $maxExpiredTime = $expiredTime;
        if ($this->cache) {
            $ips = $this->cache->get($this->cacheKey);
            if ($ips === false) {
                $ips = [];
            } else {
                $ips = $this->decode($ips);
            }

            $ips[$ip] = ['time' => time() + $expiredTime];
            if (null !== $url) {
                $ips[$ip]['url'] = $url;
            }

            foreach ($ips as $key => $value) {
                if ($value['time'] - time() > $maxExpiredTime) {
                    $maxExpiredTime = $value['time'] - time();
                }
            }

            $this->cache->set($this->cacheKey, $this->encode($ips), $maxExpiredTime);
        }
    }

    public function banIps(array $ipData): void {
        $maxExpiredTime = 0;
        if ($this->cache) {
            $ips = $this->cache->get($this->cacheKey);
            if ($ips === false) {
                $ips = [];
            } else {
                $ips = $this->decode($ips);
            }

            foreach ($ipData as $ipItem) {
                $ip = $ipItem['ip'];
                $expiredTime = $ipItem['expiredTime'];
                $url = $ipItem['url'] ?? null;

                $ips[$ip] = ['time' => time() + $expiredTime];
                if (null !== $url) {
                    $ips[$ip]['url'] = $url;
                }
            }

            foreach ($ips as $key => $value) {
                if ($value['time'] - time() > $maxExpiredTime) {
                    $maxExpiredTime = $value['time'] - time();
                }
            }

            $this->cache->set($this->cacheKey, $this->encode($ips), $maxExpiredTime);
        }
    }
    /**
     * Очистка бан-листа
     */
    public function clearBanList(): void {
        if ($this->cache) {
            $this->cache->del($this->cacheKey);
        }
    }

    public function getBanList(): array {
        $ips = [];
        if ($this->cache) {
            $ips = $this->cache->get($this->cacheKey);
            if ($ips === false) {
                $ips = [];
            } else {
                $ips = $this->decode($ips);
            }
        }

        return $ips;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    protected function encode(array $data):string {
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
}