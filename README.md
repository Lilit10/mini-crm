# mini-crm

Мини-CRM на Laravel (PHP 8.4) для сбора и обработки заявок с сайта через встраиваемый виджет.

## Требования

- PHP 8.4
- Docker + Docker Compose (рекомендуется)

## Быстрый старт (Docker)

1) Запуск контейнеров:

```bash
docker compose up -d --build
```

2) Установка зависимостей и подготовка окружения:

```bash
docker compose exec app composer install
docker compose exec app cp .env.example .env
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate:fresh --seed
```

3) Открыть приложение:

- `http://localhost:8080`

## Локальный запуск (без Docker)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

## Полезные команды

- Тестовые данные (после `php artisan migrate:fresh --seed`):
  - менеджер: `manager@example.com`
  - пароль: `password`
  - роль: `manager`
  - создаются 3 клиента и 6 заявок (статус `new`)

- Тесты:

```bash
php artisan test
```

- Форматирование (Pint):

```bash
./vendor/bin/pint
```

## API и виджет

Будут добавлены:

- `POST /api/tickets` — создание заявки
- `GET /api/tickets/statistics` — статистика заявок
- `/widget` — Blade-страница виджета (iframe-ready)

## Примечания

- Репозиторий не должен содержать `vendor/`, `node_modules/` и `.env` (см. `.gitignore`).
