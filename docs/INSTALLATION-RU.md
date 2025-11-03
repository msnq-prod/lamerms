# Полное руководство по установке и запуску (с нуля)

Это пошаговая инструкция для совсем начинающих: от «пустого» ПК до работающего сервиса локально на http://localhost:8080.

Сервис разворачивается в Docker и включает:
- веб‑приложение (PHP 8 + Apache),
- базу данных MariaDB,
- опционально тестовый почтовый сервер (Mailpit) для отправки писем.

Если что-то пойдёт не так — см. раздел «Частые проблемы» в конце.

## 1) Подготовка компьютера

Поддерживаемые ОС: Windows 10/11, macOS (Intel/Apple Silicon), Linux (Ubuntu 22.04+).

1. Установите Docker:
   - Windows/macOS: Docker Desktop — https://www.docker.com/products/docker-desktop/
   - Linux (Ubuntu):
     ```bash
     # Обновление пакетов
     sudo apt update
     # Установка Docker Engine и compose-плагина
     sudo apt install -y docker.io docker-compose-plugin
     # (необязательно) разрешить запуск без sudo
     sudo usermod -aG docker $USER
     newgrp docker
     ```
2. (Рекомендуется) Установите Git, чтобы скачать проект:
   - Windows: https://git-scm.com/download/win (ставит Git Bash)
   - macOS: `xcode-select --install` или https://git-scm.com/download/mac
   - Ubuntu: `sudo apt install -y git`

## 2) Получение кода проекта

Выберите любой из вариантов:
- Склонировать репозиторий (рекомендуется):
  ```bash
  git clone <URL_ВАШЕГО_РЕПОЗИТОРИЯ>
  cd <папка_проекта>
  ```
- Или скачать ZIP архива из вашего хостинга репозитория и распаковать. Затем перейдите в корневую папку проекта.

Во всех командах ниже предполагается, что вы находитесь в корне проекта (там, где лежат файлы `docker-compose.yml`, `Dockerfile`, `.env.example`).

## 3) Настройка переменных окружения

1. Создайте файл `.env` из примера:
   ```bash
   cp .env.example .env
   ```
2. Откройте `.env` в редакторе и проверьте ключевые значения:
   - `CONFIG_ROOTURL=http://localhost:8080` — базовый адрес приложения (без завершающего `/`).
   - `CONFIG_AUTH_JWTKey=` — секрет на 64 символа (только заглавные латинские буквы и цифры). Обязательно заполните!

   Как сгенерировать `CONFIG_AUTH_JWTKey`:
   - macOS / Linux (в Терминале):
     ```bash
     openssl rand -hex 32 | tr '[:lower:]' '[:upper:]'
     ```
   - Windows (PowerShell):
     ```powershell
     $bytes = New-Object 'System.Byte[]' 32; (New-Object System.Security.Cryptography.RNGCryptoServiceProvider).GetBytes($bytes); ($bytes | ForEach-Object { $_.ToString('X2') }) -join ''
     ```
   Скопируйте полученную строку в `CONFIG_AUTH_JWTKey` в файле `.env`.

3. (Необязательно) Включить тестовую почту. Если хотите видеть отправляемые письма в браузере:
   - В `.env` раскомментируйте SMTP-блок и укажите:
     ```
     CONFIG_EMAILS_ENABLED=Enabled
     CONFIG_EMAILS_PROVIDER=SMTP
     CONFIG_EMAILS_FROM_EMAIL=warehouse@example.com
     CONFIG_EMAILS_SMTP_SERVER=mail
     CONFIG_EMAILS_SMTP_PORT=1025
     ```
   - Запускать будем с профилем `notifications` (см. следующий шаг).

## 4) Запуск сервисов в Docker

Вариант A — минимальный (без почты):
```bash
docker compose up -d
```

Вариант B — с тестовой почтой (Mailpit):
```bash
docker compose --profile notifications up -d
```

Что происходит при старте:
- контейнер `db` поднимает MariaDB и создаёт пустую БД;
- контейнер `app` ждёт готовность БД, применяет миграции и загружает стартовые данные (сиды), затем запускает веб‑сервер на порту 8080;
- при профиле `notifications` контейнер `mail` поднимает Mailpit: SMTP на `1025`, веб‑интерфейс на `8025`.

Проверка статуса:
```bash
docker compose ps
docker compose logs app | tail
```

Если видите сообщения вида «Missing required environment variable» или «CONFIG_AUTH_JWTKey must be exactly 64 characters» — вернитесь к шагу 3 и исправьте `.env`.

## 5) Открыть приложение и войти

Откройте браузер: http://localhost:8080

При первом запуске создаётся тестовый супер‑пользователь. Данные для входа:
- Имя пользователя: `username`
- Пароль: `password!`

Сразу после входа откройте профиль пользователя и смените пароль:
- аватар/имя в правом верхнем углу → профиль;
- вкладка «Password & Sign‑in» → измените пароль.

## 6) Полезные сервисы и порты

- Приложение: http://localhost:8080
- База данных (для админ‑клиентов): `localhost:3307`, пользователь `adam_rms`, пароль `secret` (по умолчанию из `.env`)
- Mailpit (если включали профиль `notifications`):
  - Веб‑интерфейс: http://localhost:8025
  - SMTP: `mail:1025` (используется приложением через `.env`)

## 7) Остановка, перезапуск и сброс данных

- Остановить сервисы (с сохранением БД):
  ```bash
  docker compose down
  ```
- Полностью остановить и удалить БД (чистый старт):
  ```bash
  docker compose down -v
  ```
- Перезапустить после изменения кода/зависимостей:
  ```bash
  docker compose build --no-cache app
  docker compose up -d
  ```

## 8) Частые проблемы и их решение

- Ошибка при старте: `Missing required environment variable: CONFIG_AUTH_JWTKey` или длина не 64 символа.
  - Проверьте `.env`, сгенерируйте ключ заново (раздел 3).

- Ошибка: `CONFIG_ROOTURL must not end with a trailing slash`.
  - В `.env` строка `CONFIG_ROOTURL` должна быть без завершающего `/`, например `http://localhost:8080`.

- Порт уже занят (`bind: address already in use`) на 8080/3307/8025/1025.
  - Закройте приложение, которое использует этот порт, или поменяйте порты в `docker-compose.yml` и в `.env` (для SMTP).

- Не открывается http://localhost:8080 на Windows.
  - Убедитесь, что Docker Desktop запущен и включена интеграция с WSL 2. Перезапустите Docker Desktop.

- Хочу «зайти» в базу данных.
  - Используйте любой MySQL‑клиент, подключение: хост `localhost`, порт `3307`, логин `adam_rms`, пароль `secret`.

- Нужно посмотреть логи приложения.
  - `docker compose logs -f app`

## 9) Что дальше делать в приложении

- Создайте или импортируйте позиции/производителей/категории склада (часть стартовых справочников приходит из сидов).
- Добавляйте оборудование, формируйте заказы, используйте статусы ремонта.
- При необходимости включите отправку писем через Mailpit (профиль `notifications`) или подключите свой SMTP‑сервер через `.env`.
- Подробная инструкция «как внести весь склад и сгенерировать QR‑коды»: см. `docs/USER-GUIDE-INVENTORY-QR-RU.md`.

Готово! Если нужна помощь с конкретным сценарием — откройте `Readme.md` в корне или задайте вопрос сопровождающему проект.
