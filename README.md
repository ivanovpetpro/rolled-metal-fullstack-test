# Выполненое тестовое задание
Исходник задания: https://docs.google.com/document/d/1vncD-j1tUgYNtWsKvMyQI6nfdunAvYUk/edit?usp=sharing&ouid=116139382824893984782&rtpof=true&sd=true

## Запуск

```bash
docker-compose up --build -d
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate --no-interaction
bin/console doctrine:fixtures:load
```

```bash
docker-compose up --build -d
```

### frontend
SPA страницы каталога доступно по адресу http://localhost:3000

### backend
API доступно по адресу http://localhost:8080. Все эндпоинты описаны в файле postman_collection.json в корне проекта.



## Порядок разработки

### backend

* Создал сборку Docker (Symfony v7.4.20 + PHP 8.2.34)
* Настроил бандлы и конфигурацию Symfony
* Настроил БД, создал основные бизнес-сущности, добавил миграцию, фикстуры
* Создал контроллеры и API эндпоинты
* Добавил Postman коллекцию API

### frontend
* Реализовал SPA каталога, настроил CORS