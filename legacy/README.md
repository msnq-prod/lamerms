# Каталог legacy-модулей

Этот каталог содержит функциональность, удалённую из актуальной MVP-сборки AdamRMS.
Архивированные файлы сохраняют структуру оригинального `src/`, чтобы упростить
возврат к полной версии. Для картирования активных и перемещённых модулей
ориентируйтесь на [`docs/mvp-module-manifest.md`](../docs/mvp-module-manifest.md).

## Перемещённые модули и зависимости

| Раздел | Исходные файлы | Основные зависимости | Назначение |
| --- | --- | --- | --- |
| Администрирование бизнеса | `src/instances/`, `src/api/instances/*` | Таблицы `instances`, `instancePositions`, `projectsStatuses`; API-хуки в [`legacy/api/instances/billing/webhooks.php`](api/instances/billing/webhooks.php) | Управление тарифами, статусами и позициями команды инстанса |
| CMS и публичные страницы | `src/cms/`, `src/public/` | Таблицы `cmsPages`, `cmsPagesDrafts`, `cmsPagesViews`; рассылки через [`legacy/common/libs/Email/*`](common/libs/Email) | Контент-менеджмент и внешние лендинги |
| Обучение и сертификации | `src/training/` | Таблицы `modules`, `modulesSteps`, `userModules`, `userModulesCertifications` | Курсы, чек-листы и отслеживание обучения |
| Локации и CRM | `src/location/`, `src/clients.php`, `src/clients.twig` | Таблицы `locations`, `locationsBarcodes`, `clients`; функции авторизации из [`legacy/auth_permissions.php`](auth_permissions.php) | Управление площадками, клиентами и баркодами помещений |
| Финансы и производители | `src/ledger.php`, `src/manufacturers.php`, связанные Twig-файлы | Таблицы `payments`, `projectsFinanceCache`, `manufacturers` | Финансовые журналы, биллинг и справочники производителей |
| Расширенные функции проектов | `src/project/crew/`, экспорты в `src/project/twigIncludes/` | Таблицы `projectsVacantRoles`, `projectsVacantRolesApplications`, `crewAssignments` | Управление наймом и сменами |
| Серверное администрирование | `src/server/`, `src/api/server/` | Архивные конфиги `config` и S3-таблицы из миграций [`legacy/db/migrations`](db/migrations) | Управление инстансом, интеграциями и файлами |

> **Примечание.** Базовые таблицы (например, `clients`, `locations`, `modules`) всё ещё
создаются в актуальном миграционном скрипте [`db/migrations/20240101000000_core_schema.php`](../db/migrations/20240101000000_core_schema.php),
чтобы сохранить целостность ссылок в активных модулях. Расширения и дополнительные
колонки для legacy-функциональности находятся в архивных миграциях.

## Архивы и связанные данные

- **Миграции:** полный набор до сокращения хранится в [`legacy/db/migrations/`](db/migrations).
  Файл [`legacy/db/README.md`](db/README.md) описывает, как они соотносятся с новой
  схемой.
- **Конфигурация и интеграции:** устаревшие настройки (например, таблица `config`,
  S3-хранилища, OAuth) изменялись миграциями
  [`20240121120000_config_table.php`](db/migrations/20240121120000_config_table.php) и
  [`20240407110000_drop_s3files_config.php`](db/migrations/20240407110000_drop_s3files_config.php).
- **E-mail провайдеры:** обёртки Sendgrid/Mailgun/Postmark сохранены в
  [`legacy/common/libs/Email/`](common/libs/Email) для старых шаблонов уведомлений.
- **Демоданные:** отдельные сиды для legacy-функций не хранятся; актуальные сидеры
  находятся в каталоге [`db/seeds/`](../db/seeds), и они покрывают только MVP.

## Шаги возврата к полной версии

1. **Верните файлы.** Переместите нужные каталоги обратно в `src/` (или подключите
   их через автозагрузку), сверяясь с таблицей выше.
2. **Восстановите миграции.** Выполните архивные скрипты из
   [`legacy/db/migrations/`](db/migrations) в нужном порядке. Для отката после
   MVP-миграций используйте `phinx migrate -e production -t <timestamp>`.
3. **Обновите зависимости.** Пересоберите контейнер (`docker compose build app`) и
   выполните `composer dump-autoload`, чтобы учесть возвращённые пространства имён.
4. **Проверьте конфигурацию.** Убедитесь, что `.env` содержит ключи для интеграций
   (S3, рассылки, OAuth), если ими пользуются восстановленные модули.
5. **Перезапустите сиды при необходимости.** Если требуются дополнительные данные,
   создайте кастомные сиды или импортируйте бэкапы перед запуском приложения.

Следуя этим шагам, вы можете безопасно вернуться к полной функциональности без
расхождений с текущей Docker-инфраструктурой.
