## Parse SQL FunPay
*тестовое задачние на ваканчию https://career.habr.com/vacancies/1000140914*

### Предварительные условия
1. Docker-cli >= 24.0.5
2. Docker compose >= v2.3.3

### Развертывание
1. Переходим в папку docker
2. Выполняем сборку контейнеров - ```docker compose build```
3. В файле app/test.php прописываем настройки подключения к БД (пароль root DB = 'funpay')
4. Запускаем контейнеры - ```docker compose up -d```
5. Подключаемся к БД ```mysql -uroot -p -h 127.0.0.1```
6. Создаем БД которую указывали для подключения к БД в файле app/test.php

### Запуск
1. Переходим в папку docker
2. Запускаем команду ```docker compose run --rm  php-funpay bash -c './test.php'```

### Комментарии
1. В коде оставил комментарии. Цель была пройти тесты.
2. Docker контейнер с php8.2, но под php8.3 так же будет работать, просто контейнеры (которые использую) с php8.3 еще бетта версии.
2. Вероятно можно добиться большей связанности (некоторые участки кода выделить в отдельные методы). 
3. Так же можно подумать над дополнительными тестами (возможно код их не все пройдет т.к. цель была пройти текущие тесты)