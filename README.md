# Антибот для nginx на lua с использованием redis

## Принцип работы
Антибот работает на openresty (https://www.nginx.com/resources/wiki/modules/lua/), пропуская запросы к серверу через lua скрипт (antibot.lua). Внутри скрипта стоит опрос внутреннего справочника на наличие забаненных адресов. Если какие-то адреса в бане есть и URL совпадает с указанным в бане (либо IP забанен для всех адресов), то выполнение сценария прерывается и клиенту выдается код 429 (Too many requests).

## Локальный запуск приложения
Для локального запуска приложения необходимо выполнить следующие действия:
* Скачать проект командой:
```bash
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
После этого приложение будет доступно по адресу: http://antibot.lc.
Можно ходить по адресам типа http://antibot.lc/authentication/login для проверки бана конкретных URI. В случае бана сервер отдаст код 429 (Too many requests).

## Включение и конфигурация антибота
Пример nginx конфига
```text
lua_package_path '/etc/nginx/lua/modules/?.lua;;';
lua_shared_dict antibot 14m;

server {

    access_by_lua_file /etc/nginx/lua/antibot-check.lua;

    # start antibot script configure
    set $redis_host ${REDIS_HOST};
    set $redis_port ${REDIS_PORT};
    set $redis_key ${REDIS_KEY};
    #set $redis_auth ${REDIS_AUTH}; # auth if necessary
    set $refresh_key ${REFRESH_KEY}; # if necessary
    # end antibot script configure

    location = /antibot_update {
        default_type 'text/plain';
        content_by_lua_file /etc/nginx/lua/antibot-update.lua;
    }
```
При переходе на адрес http://antibot.lc/antibot_update происходит обновление справочника антибота. Данные берутся из редиса по ключу, указанному в nginx конфигурации. Внутри редиса данные хранятся в JSON формате.

## Консольные команды
* Список доступных команд
```bash
docker compose exec app php console.php list
```

* Добавить IP в бан на 100 секунд для всех адресов сайта
```bash
docker compose exec app php console.php antibot:ban 172.20.0.1 100 "antibot.lc"
```

* Добавить IP в бан на 200 секунд для адреса "antibot.lc/authentication/login" 
```bash
docker compose exec app php console.php antibot:ban 172.20.0.1 200 "antibot.lc/authentication/login"  
```

* Добавить IP в бан на 20 секунд для всех сайтов 
```bash
docker compose exec app php console.php antibot:ban 172.20.0.1 20 
```

* Очистка бан-листа
```bash
docker compose exec app php console.php antibot:clear 
```

* Просмотр бан-листа
```bash
docker compose exec app php console.php antibot:list 
```

* Добавить 60000 произвольных адресов в бан на 500 секунд
```bash
docker compose exec app php console.php antibot:test 60000 500 
```

## HTTP API команды
* Получение JWT-токена
```bash
POST http://antibot.lc/api/login

{
    "username": "admin",
    "password": "admin"
}
```

* Добавить IP в бан на 100 секунд для всех адресов сайта
```bash
Authorization: Bearer token
POST http://antibot.lc/api/antibot/add

{
    "ip": "172.20.0.1",
    "time": 100,
    "url": "antibot.lc"
}
```

* Добавить IP в бан на 200 секунд для адреса "antibot.lc/authentication/login" 
```bash
Authorization: Bearer token
POST http://antibot.lc/api/antibot/add

{
    "ip": "172.20.0.1",
    "time": 200,
    "url": "antibot.lc/authentication/login"
}
```

* Добавить IP в бан на 20 секунд для всех сайтов 
```bash
Authorization: Bearer token
POST http://antibot.lc/api/antibot/add

{
    "ip": "172.20.0.1",
    "time": 20
}
```

* Очистка бан-листа
```bash
Authorization: Bearer token
GET http://antibot.lc/api/antibot/clear
```

* Просмотр бан-листа
```bash
Authorization: Bearer token
GET http://antibot.lc/api/antibot/list
```

* Удалить адрес из бана
```bash
Authorization: Bearer token
POST http://antibot.lc/api/antibot/delete

{
    "ip": "172.20.0.1"
}
```