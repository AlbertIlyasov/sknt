# Комментарии программиста

1. htaccess через mod_rewrite отправляет переменные и их значения в index.php
2. Роутинг реализуется через проверку в index.php ожидаемых переменных и типов их значений (например, ожидается число для переменной `{user_id}`).
3. В случае, если роутинг найден, то управление передаётся соответствующему контроллеру и методу. Иначе отправляется код ошибки 404.

## Проведённые тесты:
1. `GET /users/{user_id}/services/{service_id}/tarifs` - получает тарифы для конкретного сервиса.
    Тарифы могут быть только с тем же самым tarif_group_id, что и у текущего тарифа сервиса.
    Из текста тестового задания я понял это так: `SELECT * FROM tarifs WHERE tarif_group_id = (SELECT tarif_id FROM services WHERE ID = {service_id} and user_id = {user_id})`, к которому дальше добавляется поле new_payday = Y-m-d + (SELECT pay_period FROM tarifs WHERE ID = x) months
```
curl site.ru/users/111/services/11/tarifs
  % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                 Dload  Upload   Total   Spent    Left  Speed
100   558  100   558    0     0  16411      0 --:--:-- --:--:-- --:--:-- 16909{"result":"ok","tarifs":{"title":"\u0417\u0435\u043c\u043b\u044f","link":"http:\/\/www.sknt.ru\/tarifi_internet\/in\/1.htm","speed":50,"tarifs":[{"ID":1,"title":"\u0417\u0435\u043c\u043b\u044f","price":500,"pay_period":"1","new_payday":"1579208400+0300","speed":50},{"ID":2,"title":"\u0417\u0435\u043c\u043b\u044f (3 \u043c\u0435\u0441)","price":1350,"pay_period":"3","new_payday":"1584392400+0300","speed":50},{"ID":3,"title":"\u0417\u0435\u043c\u043b\u044f (6 \u043c\u0435\u0441)","price":4200,"pay_period":"6","new_payday":"1592341200+0300","speed":50}]}}
```
2. `PUT /users/{user_id}/services/{service_id}/tarif`
    Данные: `{"tarif_id": (ID тарифа из запроса на получениe тарифов)}`
    Из текста тестового задания я понял это так: `UPDATE services SET payday = ` php:date('Y-m-d') ` WHERE ID = {service_id} and user_id = {user_id} and tarif_id = (ID тарифа из запроса на получениe тарифов)`
```
DB before:
ID;user_id;tarif_id;payday
11;111;1;2019-11-10

DB after:
ID;user_id;tarif_id;payday
11;111;1;2019-12-17
```
```
curl -X PUT -d '{"tarif_id":1}' site.ru/users/111/services/11/tarif
  % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                 Dload  Upload   Total   Spent    Left  Speed
100    29  100    15  100    14    365    341 --:--:-- --:--:-- --:--:--   725{"result":"ok"}

```

Ниже идёт оригинал тестового задания.

# Тестовое задание

## Дано

1. Структура таблиц mysql

    `services` - сервисы пользователей
    
    `tarifs` - тарифы сервисов

    Тарифы связываются между собой через параметр `tarif_group_id`

2. Файл с конфигом для доступа к БД - db_cfg.php

## Задача: Написать код, реализующий API-запросы

1. `GET /users/{user_id}/services/{service_id}/tarifs` - получает тарифы для конкретного сервиса.
    Тарифы могут быть только с тем же самым tarif_group_id, что и у текущего тарифа сервиса.
2. `PUT /users/{user_id}/services/{service_id}/tarif` 
    Данные: `{"tarif_id": (ID тарифа из запроса на получениe тарифов)}`

### Общие требования к API

1. Ответы на запросы в виде JSON
2. JSON ответа по тарифам должен соответствовать примеру:

    ```
    {
    "result":"ok",
    "tarifs":[
        {
            "title":"Земля",
            "link":"http://www.sknt.ru/tarifi_internet/in/1.htm",
            "speed":50,
            "tarifs":[
                {"ID":2,"title":"Земля (3 месяца)","price":1350,"pay_period":"3","new_payday":"1452891600+0300","speed":50},
                {"ID":1,"title":"Земля","price":480,"pay_period":"1","new_payday":"1450213200+0300","speed":50},
                {"ID":3,"title":"Земля (6 месяцев)","price":2460,"pay_period":"6","new_payday":"1460754000+0300","speed":50},
                {"ID":4,"title":"Земля (12 месяцев)","price":4200,"pay_period":"12","new_payday":"1476565200+0300","speed":50}
            ]
        },
        ...
    ]
    }
    ```
    new_payday - timestamp даты следующего списания и таймзона. Рассчитывается как текущая дата полночь + pay_period

3. Запрос на выставление тарифа должен возвращать `{"result": "ok"}` при успехе или `{"result": "error"}` в случае ошибки
4. Запрос на выставление тарифа проставляет tarif_id и payday
5. user_id и service_id будут задаваться вручную при проверке задания

## Общие требования к результату

1. Код должен работать в любом каталоге web-сервера (не обязательно в корневом)
2. Код не должен использовать специфических для web-сервера функций и настроек
3. Код должен использовать конфигурационный файл db_cfg.php только для настрок БД
4. Прикладывать db_cfg.php к решению не нужно
5. Точка входа в API должна быть в файле index.php
6. Учесть при написании, что управление на index.php будет передаваться через обработку 404й ошибки
