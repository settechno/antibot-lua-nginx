#Антибот для nginx на lua с использованием redis

##Локальный запуск приложения
Для локального запуска приложения необходимо выполнить следующие действия:
* Скачать проект командой:
```bash
$ git clone http://gitlab.roseltorg.local/Tanygin.SA/antibot-lua-nginx --config core.autocrlf=input
$ git clone http://gitlab.roseltorg.local/Tanygin.SA/antibot-lua-nginx --config core.autocrlf=input
```

* Добавить в hosts файл

```text
127.0.0.1    antibot.lc
```

* Запустить проект командой:
```bash
$ make start
```
После этого приложение будет доступно по адресу: http://antibot.lc

##Включение и конфигурация антибота
Пример nginx конфига
```text
lua_shared_dict antibot 1m;

server {

    access_by_lua_file /etc/nginx/lua/antibot.lua;

    # start antibot script configure
    set $redis_host ${REDIS_HOST};
    set $redis_port ${REDIS_PORT};
    set $redis_connection_timeout 5;
    set $redis_key ${REDIS_KEY};
    set $cache_ttl 10;
    # end antibot script configure
```
Здесь cache_ttl - время кеша в секундах, в течении которого антибот не обращается к редису для обновления данных

## Консольные команды
* Список доступных команд
```bash
docker compose exec app php console.php list
```

* Добавить IP в бан на 100 секунд
```bash
docker compose exec app php console.php antibot:ban 172.20.0.1 10 
```

* Добавить IP в бан на 200 секунд для адреса "authentication/login" 
```bash
docker compose exec app php console.php antibot:ban 172.20.0.1 20 "authentication/login"  
```

* Добавить CIDR в бан на 200 
```bash
docker compose exec app php console.php antibot:ban 172.20.0.1/32
```

* Очистка бан-листа
```bash
docker compose exec app php console.php antibot:clear 
```

* Просмотр бан-листа
```bash
docker compose exec app php console.php antibot:list 
```