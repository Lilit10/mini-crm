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
docker compose exec app php artisan storage:link
docker compose exec app php artisan migrate:fresh --seed
```

3) Открыть приложение:

- приложение: `http://localhost:8080`
- **виджет (форма заявки):** `http://localhost:8080/widget`

## Локальный запуск (без Docker)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate:fresh --seed
php artisan serve
```

Виджет: `http://127.0.0.1:8000/widget`

## Виджет (iframe)

Страница `GET /widget` отдаёт оформленную форму и отправляет данные на **`POST /api/tickets`** через `fetch` (JSON-ответы и ошибки валидации).

Пример вставки на другой сайт:

```html
<iframe
  src="https://your-domain.com/widget"
  title="Feedback"
  width="480"
  height="720"
  style="border:0;width:100%;max-width:480px;min-height:560px;">
</iframe>
```

Замените `https://your-domain.com` на свой `APP_URL`.

## API (кратко)

Все ответы — через API Resources (`Accept: application/json`).

**Создание заявки**

```bash
curl -s -X POST http://127.0.0.1:8000/api/tickets \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","phone_e164":"+15551234567","email":"a@b.com","subject":"Hi","message":"Text"}'
```

**С вложениями** (до 5 файлов, до 10 МБ каждый; типы: jpg, jpeg, png, pdf, doc, docx): `multipart/form-data`, поля те же + `attachments[]`.

**Статистика**

```bash
curl -s http://127.0.0.1:8000/api/tickets/statistics -H "Accept: application/json"
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

## Примечания

- Репозиторий не должен содержать `vendor/`, `node_modules/` и `.env` (см. `.gitignore`).
- Для ссылок на загруженные файлы в API (`attachments[].url`) нужен `php artisan storage:link` и корректный `APP_URL`.
