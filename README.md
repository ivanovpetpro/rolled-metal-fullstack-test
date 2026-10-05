# Выполненое тестовое задание
Исходник задания: https://docs.google.com/document/d/1vncD-j1tUgYNtWsKvMyQI6nfdunAvYUk/edit?usp=sharing&ouid=116139382824893984782&rtpof=true&sd=true

## Запуск

```bash
docker-compose up --build -d
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate --no-interaction
bin/console doctrine:fixtures:load --no-interaction
```

## Порядок разработки

* Создал сборку Docker (Symfony v7.4.20 + PHP 8.2.34)
