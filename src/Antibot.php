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
                $ips = json_decode($ips, true);
            }

            $ips[$ip] = ['time' => time() + $expiredTime];
            if (null !== $url) {
                $ips[$ip]['url'] = $this->normalizeUrl($url);
            }

            foreach ($ips as $key => $value) {
                if ($value['time'] - time() > $maxExpiredTime) {
                    $maxExpiredTime = $value['time'] - time();
                }
            }

            $this->cache->set($this->cacheKey, json_encode($ips), $maxExpiredTime);
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
                $ips = json_decode($ips, true);
            }
        }

        return $ips;
    }

    /**
     * Нормализация URL, убирается всё лишнее
     * н.р. /index.php/default/authentication/login -> /authentication/login
     *
     * @param string $url
     *
     * @return string
     */
    protected function normalizeUrl(string $url): string {
        if ($url[0] !== '/') {
            $url = '/' . $url;
        }
        if (strpos($url, '/index.php') === 0) {
            $url = substr($url, 10);
        }
        if (strpos($url, '/default') === 0) {
            $url = substr($url, 8);
        }

        return $url;
    }
}