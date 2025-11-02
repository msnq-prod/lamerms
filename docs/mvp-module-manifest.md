# MVP Module Manifest

Этот документ фиксирует текущее разделение между активными модулями MVP (склад, заказы, ремонт) и функциональностью, которую предстоит перенести в каталог `/legacy`.

## Остаётся в `src/`

- **Общие точки входа**: `src/index.php`, шаблоны `src/index_*.twig`, `src/dashboard.twig`, `src/dashboard-cmsPage.twig` (до удаления CMS), макет `src/assets/template.twig`.
- **Склад**: `src/assets.php`, `src/assets.twig`, `src/asset.php`, `src/asset.twig`, `src/newAsset.php`, `src/newAsset.twig`, утилиты каталога в `src/assets/widgets/` и `src/common/libs/Search/SearchProjects.php`.
- **Заказы**: контроллеры и шаблоны в `src/project/` (кроме подкаталога `crew/`), экспортные шаблоны `src/project/twigIncludes/`.
- **Ремонт**: весь каталог `src/maintenance/`.
- **Профиль и аутентификация**: `src/login/`, `src/user.php`, `src/user.twig`, вспомогательные `src/common/`.
- **API для поддерживаемых модулей**: `src/api/assets/`, `src/api/projects/`, `src/api/maintenance/`, `src/api/file/`, общие библиотеки в `src/common/libs/`.

## Переносится в `/legacy`

- **Администрирование бизнеса**: весь каталог `src/instances/`, включая настройки, статусы проектов, группы, биллинг и навигацию.
- **CMS и публичные страницы**: каталог `src/cms/`, публичные встраивания `src/public/`.
- **Обучение и знаниевая база**: каталог `src/training/`.
- **Локации и клиенты**: `src/location/`, `src/clients.php`, `src/clients.twig`.
- **Финансы и производители**: `src/ledger.php`, `src/ledger.twig`, `src/manufacturers.php`, `src/manufacturers.twig`.
- **Расширенные функции проектов**: подкаталог `src/project/crew/`, экспорт вакантий и связанные шаблоны.
- **Серверное администрирование**: каталог `src/server/` и связанные API `src/api/server/`.
- **Прочее**: любые вспомогательные скрипты и шаблоны, которые обслуживают перечисленные разделы (например, `src/api/instances/*`, `src/api/clients/*`).

## Следующие шаги

1. Создать каталог `/legacy/` и перенести указанные модули, сохранив исходную структуру.
2. Добавить маршруты или редиректы, если потребуется временно поддерживать старые URL.
3. Убедиться, что в коде не осталось `require` или `include`, указывающих на файлы после перемещения.
4. Обновить документацию (`Readme.md`, `/legacy/README.md`) после фактического переноса.
